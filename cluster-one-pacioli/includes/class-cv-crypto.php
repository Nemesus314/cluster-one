<?php
/**
 * Verschlüsselung der hinterlegten API-Schlüssel.
 *
 * Symmetrisch über sodium_crypto_secretbox. Der Verschlüsselungsschlüssel
 * kommt seit 0.3.0 aus einer von ZWEI Stufen:
 *
 *   STUFE 1 — Konstante CV_SECRET_KEY in der wp-config.php.
 *             Die bessere. Wer die Datenbank liest, kann damit nichts
 *             anfangen.
 *
 *   STUFE 2 — beim Aktivieren erzeugt und in der Datenbank abgelegt.
 *             Ehrlich schwächer: Wer die Datenbank lesen kann, kann die
 *             Schlüssel entschlüsseln. Dafür braucht niemand Dateizugriff.
 *
 * Warum überhaupt zwei Stufen: Das Publikum dieses Plugins ist zu einem
 * guten Teil dadurch definiert, dass es ausschliesslich über den
 * WordPress-Uploader installiert und kein FTP anfasst. Eine Anleitung, die
 * mit „öffne die wp-config.php“ beginnt, ist für diese Menschen kein Modul,
 * sondern eine Hürde. Also: beide Stufen zulassen, und in der Oberfläche
 * sagen, welche gerade gilt.
 *
 * Der Verlust der Stufe 2 wiegt weniger schwer als der der Stufe 1: In
 * `wp_options` steht seit 0.3.0 ohnehin kein Schlüssel mehr, sondern eine
 * Marke. Wer die Datenbank abzieht, findet den verschlüsselten Vault und
 * den Schlüssel dazu — aber ein reiner Options-Dump liefert nichts.
 *
 * Was KEINE der beiden Stufen schützt: den Zugriff des Betreibers. Wer den
 * Server hat, hat alles. Das steht so in der Oberfläche, und die einzig
 * wirksame Antwort darauf ist ein Ausgabelimit beim Anbieter.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 *  DER RIEGEL UM DIE KLASSE — 12.09.2026, Weg C, dritte Schicht
 *
 *  Ohne ihn stirbt ein Knoten mit ZWEI Produktordnern an
 *  `Cannot redeclare class CV_Crypto`, sobald der Lader seine EIGENEN Dateien
 *  laedt (zweite Schicht). Bis heute war dieser Tod VERDECKT: der Lader
 *  kam gar nicht bis hierher.
 *
 *      Ein Fehler, der von einem anderen Fehler verdeckt wird, ist beim
 *      Reparieren des ersten kein Fortschritt, sondern eine Ueberraschung.
 *
 *  Die Einrueckung ist nicht die Form, sondern die Sache: PHP bindet eine
 *  UNBEDINGTE Deklaration beim Uebersetzen, also bevor der Riegel je
 *  gefragt wird. Nur was IM Block steht, wird spaeter gebunden. Gemessen
 *  am 11.09. an den Funktionen, heute an den Klassen.
 * ------------------------------------------------------------------------- */
if ( ! class_exists( 'Pacioli_Crypto' ) ) {
	class Pacioli_Crypto {

		const PREFIX     = 'cv1:';
		const OPTION_KEY = 'pacioli_secret_key';

		/** Ist die Kryptographie einsatzbereit? */
		public static function is_available() {
			return function_exists( 'sodium_crypto_secretbox' )
				&& function_exists( 'sodium_crypto_secretbox_open' );
		}

		/** Ist ein Schlüssel vorhanden — auf welcher Stufe auch immer? */
		public static function has_key() {
			return '' !== self::key_material();
		}

		/**
		 * Welche Stufe gilt gerade?
		 *
		 * @return string 'constant' | 'database' | 'none'
		 */
		public static function tier() {
			if ( '' !== self::schluessel_konstante() ) {
				return 'constant';
			}

			return get_option( self::OPTION_KEY ) ? 'database' : 'none';
		}

		/**
		 * Lesbare Beschreibung der aktuellen Stufe.
		 */
		public static function tier_label() {
			switch ( self::tier() ) {
				case 'constant':
					return __( 'Best tier: the encryption key sits in wp-config.php. A database dump alone is of no use to anyone.', 'cluster-one-pacioli' );

				case 'database':
					return __( 'Basic tier: the encryption key sits in the database. Anyone who can read the database can decrypt the stored keys. Anyone with access to wp-config.php can move up to the better tier.', 'cluster-one-pacioli' );

				default:
					return __( 'There is no encryption key. No access keys can be stored.', 'cluster-one-pacioli' );
			}
		}

		/**
		 * Erzeugt bei Bedarf einen Schlüssel der Stufe 2.
		 */
		public static function ensure_key() {
			if ( 'none' !== self::tier() ) {
				return;
			}

			update_option( self::OPTION_KEY, self::generate_key(), false );
		}

		/** Erzeugt einen neuen Schlüssel (base64, 32 Byte). */
		public static function generate_key() {
			return base64_encode( random_bytes( SODIUM_CRYPTO_SECRETBOX_KEYBYTES ) );
		}

		/**
		 * 0.9.0 (02.11.2026, Neo): der Tresorschluessel aus wp-config.php, unter dem neuen Namen
		 * PACIOLI_SECRET_KEY — und als Rueckfall unter dem alten, CV_SECRET_KEY, bis der SysOp die
		 * Zeile angefasst hat (Hinweis im Backend, cluster-one-pacioli.php). Eine Konstante kann
		 * kein Plugin umbenennen; ein Tresor, der nach der Umbenennung nicht mehr aufgeht, waere
		 * der stille Verlust, den dieses Plugin nie zulaesst. Zwei Leser, eine Quelle.
		 *
		 * @return string base64, oder '' wenn keiner gesetzt ist.
		 */
		private static function schluessel_konstante() {
			if ( defined( 'PACIOLI_SECRET_KEY' ) && '' !== (string) PACIOLI_SECRET_KEY ) {
				return (string) PACIOLI_SECRET_KEY;
			}
			if ( defined( 'CV_SECRET_KEY' ) && '' !== (string) CV_SECRET_KEY ) { // alter Name (bis 0.8.53), bleibt lesbar
				return (string) CV_SECRET_KEY;
			}
			return '';
		}

		/**
		 * Das rohe Schlüsselmaterial, base64-kodiert.
		 */
		private static function key_material() {
			if ( '' !== self::schluessel_konstante() ) {
				return self::schluessel_konstante();
			}

			return (string) get_option( self::OPTION_KEY, '' );
		}

		/**
		 * Der Binärschlüssel oder ein Fehler.
		 *
		 * @return string|WP_Error
		 */
		private static function key() {
			$material = self::key_material();

			if ( '' === $material ) {
				return new WP_Error( 'pacioli_no_key', __( 'There is no encryption key.', 'cluster-one-pacioli' ) );
			}

			$raw = base64_decode( $material, true );

			if ( false === $raw || SODIUM_CRYPTO_SECRETBOX_KEYBYTES !== strlen( $raw ) ) {
				return new WP_Error( 'pacioli_bad_key', __( 'The encryption key is not a valid base64-encoded 32-byte sequence.', 'cluster-one-pacioli' ) );
			}

			return $raw;
		}

		/**
		 * @return string|WP_Error
		 */
		public static function encrypt( $plain ) {
			$key = self::key();

			if ( is_wp_error( $key ) ) {
				return $key;
			}

			$nonce  = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
			$cipher = sodium_crypto_secretbox( (string) $plain, $nonce, $key );
			$out    = self::PREFIX . base64_encode( $nonce . $cipher );

			sodium_memzero( $key );

			return $out;
		}

		/**
		 * @return string|WP_Error
		 */
		public static function decrypt( $stored ) {
			$stored = (string) $stored;

			if ( 0 !== strpos( $stored, self::PREFIX ) ) {
				return new WP_Error( 'pacioli_not_encrypted', __( 'The stored value is not encrypted.', 'cluster-one-pacioli' ) );
			}

			$key = self::key();

			if ( is_wp_error( $key ) ) {
				return $key;
			}

			$raw = base64_decode( substr( $stored, strlen( self::PREFIX ) ), true );

			if ( false === $raw || strlen( $raw ) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ) {
				sodium_memzero( $key );

				return new WP_Error( 'pacioli_corrupt', __( 'The stored value is damaged.', 'cluster-one-pacioli' ) );
			}

			$nonce  = substr( $raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
			$cipher = substr( $raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
			$plain  = sodium_crypto_secretbox_open( $cipher, $nonce, $key );

			sodium_memzero( $key );

			if ( false === $plain ) {
				return new WP_Error(
					'pacioli_decrypt_failed',
					__( 'The key could not be decrypted. Was the encryption key changed?', 'cluster-one-pacioli' )
				);
			}

			return $plain;
		}
	}
}
