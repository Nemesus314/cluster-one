<?php
/**
 * Die Ausfuhr, die der Mensch hält — Posten B6, 10.10.2026.
 *
 * ===========================================================================
 *  WARUM ES SIE GIBT
 * ===========================================================================
 *
 *  Beim Entfernen gehen die hinterlegten Schlüssel, immer. Ohne dieses
 *  Plugin kann sie niemand mehr benutzen — aber jeder lesen, der die
 *  Datenbank lesen kann, solange der Tresorschlüssel daneben liegt.
 *
 *      Ein Schlüssel, den niemand mehr benutzen kann, ist nicht harmlos.
 *      Er ist nur unbeobachtet.                                  (Mammut)
 *
 *  Und die Antwort darauf ist nicht ein späteres Löschen, sondern ein
 *  früheres Wissen:
 *
 *      Wer nach dem Abriss noch etwas holen will, braucht einen Schlüssel
 *      zum Trümmerfeld. Wer vorher packt, braucht keinen.        (Mammut)
 *
 *      „Jeder hat und behält seine Daten" heisst nicht, dass der Knoten
 *      gut aufbewahrt. Es heisst, dass der Mensch eine Kopie hat, die der
 *      Knoten nicht öffnen kann.                                 (Mammut)
 *
 * ===========================================================================
 *  DREI ENTSCHEIDUNGEN, DIE MAMMUT MIR ÜBERLASSEN HAT — MIT GRUND
 * ===========================================================================
 *
 *  1 · DIE FORM: EINE DATEI, UND IHR INHALT IST LESBARER TEXT
 *
 *      Nicht „Datei oder Textblock", sondern eine Datei, deren Inhalt man
 *      ansehen kann. Der Kopf steht im Klartext, der Leib ist base64 in
 *      Zeilen. Wer die Datei öffnet, sieht sofort, ob sie leer ist, von
 *      welchem Knoten sie kommt und wie viele KIs darin stehen — ohne ein
 *      Passwort zu tippen.
 *
 *          Eine Ausfuhr, die man nicht ansehen kann, muss man glauben.
 *
 *      Und sie ist mit der Hand zu retten: wer die Datei verliert, aber
 *      den Text noch im Posteingang hat, kann ihn in eine neue Datei
 *      kopieren. Ein Binärformat kann das nicht.
 *
 *  2 · WAS IM KLARTEXT STEHT, UND DASS ES DORT STEHT
 *
 *          Knoten · Zeitpunkt · Besitzername · Anzahl · die Ableitung
 *
 *      Das ist Absicht, und es ist der Preis für Punkt 1: wer drei
 *      Ausfuhren in einem Ordner hat, muss sie unterscheiden können, ohne
 *      drei Passwörter zu tippen.
 *
 *      Im Leib — und nur dort — stehen die NAMEN der KIs, ihre Anbieter,
 *      ihre Modelle, ihre Persona und ihre SCHLÜSSEL.
 *
 *          Was im Kopf steht, steht dort ungeschützt. Deshalb steht dort
 *          nichts, was man nicht auch auf den Umschlag schreiben würde.
 *
 *      DIE ABLEITUNG STEHT MIT IM KOPF, und das ist kein Detail:
 *
 *          Ein Umschlag, der seine Ableitung nicht nennt, ist nur so
 *          lange lesbar, wie niemand sie ändert.
 *
 *  3 · DIE PASSWORTSTÄRKE: ZWÖLF ZEICHEN, ERZWUNGEN — UND ZWEIMAL GETIPPT
 *
 *      Erzwungen, nicht empfohlen:
 *
 *          Ein Passwort, das den Knoten verlässt, kann der Knoten nicht
 *          mehr nachbessern.
 *
 *      Zwölf Zeichen und KEINE Zusammensetzungsregel. Eine Regel über
 *      Grosschreibung und Sonderzeichen erzeugt „Passwort1!"; eine Regel
 *      über die Länge erzeugt Länge.
 *
 *      ZWEIMAL GETIPPT, und beide müssen gleich sein. Ein Tippfehler in
 *      einem Passwort, das die einzige Kopie schützt, ist nicht zu
 *      bemerken und nicht zu heilen.
 *
 *          Ein Passwort, das nur einmal getippt wird, schützt manchmal
 *          etwas anderes, als der Tippende glaubt.
 *
 *      KEIN HINWEIS WIRD GESPEICHERT. Ein „Passwort-Hinweis" am Knoten
 *      wäre eine zweite Auskunft über ein Geheimnis, das den Knoten
 *      gerade verlassen hat.
 *
 * ===========================================================================
 *  DIE ABLEITUNG, UND WARUM SIE NICHT DIE TEUERSTE IST
 * ===========================================================================
 *
 *      argon2id13 · OPSLIMIT_INTERACTIVE · MEMLIMIT_INTERACTIVE (64 MiB)
 *
 *  MODERATE wäre stärker und braucht 256 MiB. Das Publikum dieses Plugins
 *  ist zu einem guten Teil dadurch definiert, dass es über den
 *  WordPress-Uploader installiert — also auf geteilten Rechnern mit engem
 *  Speicher.
 *
 *      Eine Ableitung, die auf dem Rechner des Menschen nicht läuft,
 *      erzeugt keine starke Ausfuhr, sondern keine.
 *
 *  Der Ausgleich steht oben: zwölf Zeichen, erzwungen. Beide Hälften sind
 *  aufgeschrieben, damit niemand die eine für die ganze Rechnung hält.
 *
 *  GERIEGELT MIT BENANNTEM RÜCKFALL: fehlt `sodium_crypto_pwhash()` —
 *  libsodium ohne argon2 —, gibt es KEINE Ausfuhr, und die Seite sagt
 *  warum. Ein Rückfall auf etwas Schwächeres wäre eine Ausfuhr, die
 *  aussieht wie die andere.                                  (R-LEIHE-1)
 *
 *      Von zwei Ausfuhren, die gleich heissen und verschieden stark sind,
 *      ist die schwächere die gefährlichere: sie wird für die andere
 *      gehalten.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pacioli_Ausfuhr' ) ) {
	class Pacioli_Ausfuhr {

		const KOPF      = 'CLUSTER-ONE-PACIOLI-EXPORT';
		const FASSUNG   = 1;
		const MIN_ZEICHEN = 12;
		const MAX_BYTES = 1048576;

		/**
		 * Kann auf diesem Rechner ausgeführt werden?
		 */
		public static function moeglich() {
			return function_exists( 'sodium_crypto_pwhash' )
				&& function_exists( 'sodium_crypto_secretbox' )
				&& function_exists( 'sodium_crypto_secretbox_open' )
				&& defined( 'SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13' );
		}

		/**
		 * Und wenn nicht, warum nicht — in einem Satz für die Seite.
		 */
		public static function warum_nicht() {
			if ( self::moeglich() ) {
				return '';
			}

			return __( 'This PHP has no argon2 password derivation (sodium_crypto_pwhash). Without it an export could only be protected by something weaker, and a weaker export under the same name would be taken for the stronger one. So there is none here.', 'cluster-one-pacioli' );
		}

		/**
		 * Die Ableitung, als Zeichenkette für den Kopf — und zurück.
		 *
		 * @return array array( 'alg' => int, 'ops' => int, 'mem' => int )
		 */
		private static function ableitung_jetzt() {
			return array(
				'alg' => SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13,
				'ops' => SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
				'mem' => SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
			);
		}

		/**
		 * Was dieser Mensch mitnehmen kann.
		 *
		 * @return array array( array( 'name', 'provider', 'model', 'persona', 'key' ) )
		 */
		private static function sammeln( $owner_id ) {
			$aus = array();

			foreach ( Pacioli_Identities::owned_by( (int) $owner_id ) as $id ) {
				$id = (int) $id;

				$aus[] = array(
					'name'     => (string) get_userdata( $id )->display_name,
					'provider' => (string) get_user_meta( $id, PACIOLI_META_PROVIDER, true ),
					'model'    => (string) get_user_meta( $id, PACIOLI_META_MODEL, true ),
					'persona'  => (string) get_user_meta( $id, PACIOLI_META_PERSONA, true ),
					'key'      => Pacioli_Vault::secret_for_owner( $id ),
				);
			}

			return $aus;
		}

		/**
		 * Packt die KIs dieses Menschen in einen Umschlag.
		 *
		 * @param int    $owner_id
		 * @param string $passwort
		 * @return string|WP_Error
		 */
		public static function packen( $owner_id, $passwort ) {
			if ( ! self::moeglich() ) {
				return new WP_Error( 'pacioli_ausfuhr_unmoeglich', self::warum_nicht() );
			}

			$owner = get_userdata( (int) $owner_id );

			if ( ! $owner ) {
				return new WP_Error( 'pacioli_ausfuhr_kein_mensch', __( 'No valid owner was given.', 'cluster-one-pacioli' ) );
			}

			if ( (int) $owner_id !== (int) get_current_user_id() ) {
				/* Niemand führt für einen anderen aus. Der Riegel in
				 * `secret_for_owner()` würde ohnehin leere Schlüssel
				 * liefern — eine Ausfuhr voller leerer Schlüssel wäre
				 * schlimmer als eine Absage, weil sie wie eine Ausfuhr
				 * aussieht. */
				return new WP_Error( 'pacioli_ausfuhr_fremd', __( 'An export can only be made by the person it belongs to.', 'cluster-one-pacioli' ) );
			}

			$passwort = (string) $passwort;

			if ( strlen( $passwort ) < self::MIN_ZEICHEN ) {
				return new WP_Error(
					'pacioli_ausfuhr_kurz',
					sprintf(
						/* translators: %d: Mindestanzahl Zeichen. */
						__( 'The password needs at least %d characters. This file leaves the node, and the node cannot make it stronger afterwards.', 'cluster-one-pacioli' ),
						self::MIN_ZEICHEN
					)
				);
			}

			$kis = self::sammeln( $owner_id );

			if ( ! $kis ) {
				return new WP_Error( 'pacioli_ausfuhr_leer', __( 'There is nothing to export: no AI is set up for you.', 'cluster-one-pacioli' ) );
			}

			$ab    = self::ableitung_jetzt();
			$salz  = random_bytes( SODIUM_CRYPTO_PWHASH_SALTBYTES );
			$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

			$schluessel = sodium_crypto_pwhash(
				SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
				$passwort,
				$salz,
				$ab['ops'],
				$ab['mem'],
				$ab['alg']
			);

			$leib = wp_json_encode(
				array(
					'fassung' => self::FASSUNG,
					'kis'     => $kis,
				)
			);

			$cipher = sodium_crypto_secretbox( (string) $leib, $nonce, $schluessel );

			sodium_memzero( $schluessel );

			$zeilen = array(
				self::KOPF . ' ' . self::FASSUNG,
				'node: ' . self::knoten(),
				'when: ' . gmdate( 'c' ),
				'owner: ' . self::eine_zeile( $owner->display_name ),
				'ais: ' . count( $kis ),
				'kdf: argon2id13 ' . (int) $ab['ops'] . ' ' . (int) $ab['mem'],
				'--',
			);

			return implode( "\n", $zeilen ) . "\n"
				. chunk_split( base64_encode( $salz . $nonce . $cipher ), 76, "\n" );
		}

		/**
		 * Öffnet einen Umschlag.
		 *
		 * @param string $text
		 * @param string $passwort
		 * @return array|WP_Error array( 'kis' => array, 'kopf' => array )
		 */
		public static function oeffnen( $text, $passwort ) {
			if ( ! self::moeglich() ) {
				return new WP_Error( 'pacioli_ausfuhr_unmoeglich', self::warum_nicht() );
			}

			$text = (string) $text;

			if ( '' === $text || strlen( $text ) > self::MAX_BYTES ) {
				return new WP_Error( 'pacioli_einfuhr_groesse', __( 'This file is empty or too large to be an export of this plugin.', 'cluster-one-pacioli' ) );
			}

			$teile = explode( "\n--\n", str_replace( "\r\n", "\n", $text ), 2 );

			if ( 2 !== count( $teile ) ) {
				return new WP_Error( 'pacioli_einfuhr_form', __( 'This does not look like an export of this plugin: the line with the two dashes is missing.', 'cluster-one-pacioli' ) );
			}

			$kopf = array();

			foreach ( explode( "\n", $teile[0] ) as $i => $zeile ) {
				if ( 0 === $i ) {
					if ( self::KOPF . ' ' . self::FASSUNG !== trim( $zeile ) ) {
						return new WP_Error(
							'pacioli_einfuhr_fassung',
							sprintf(
								/* translators: %s: die erwartete erste Zeile. */
								__( 'This file does not begin with "%s". An export of a later version is not read by an earlier one — it would have to guess what it does not know.', 'cluster-one-pacioli' ),
								self::KOPF . ' ' . self::FASSUNG
							)
						);
					}

					continue;
				}

				$p = strpos( $zeile, ':' );

				if ( false !== $p ) {
					$kopf[ trim( substr( $zeile, 0, $p ) ) ] = trim( substr( $zeile, $p + 1 ) );
				}
			}

			/* DIE ABLEITUNG WIRD GELESEN UND NICHT ANGENOMMEN. Steht sie
			 * nicht da, wird nicht geraten: eine geratene Ableitung liefert
			 * einen falschen Schlüssel und danach „falsches Passwort" —
			 * eine Meldung über den Menschen für einen Fehler der Datei. */
			if ( ! isset( $kopf['kdf'] ) ) {
				return new WP_Error( 'pacioli_einfuhr_kdf', __( 'This file does not say how its password was turned into a key. Without that line it cannot be opened, and guessing would blame you for a fault of the file.', 'cluster-one-pacioli' ) );
			}

			$k = preg_split( '/\s+/', (string) $kopf['kdf'] );

			if ( 3 !== count( $k ) || 'argon2id13' !== $k[0] ) {
				return new WP_Error( 'pacioli_einfuhr_kdf_fremd', __( 'This file names a password derivation this version does not know.', 'cluster-one-pacioli' ) );
			}

			$roh = base64_decode( preg_replace( '/\s+/', '', $teile[1] ), true );

			$mindestens = SODIUM_CRYPTO_PWHASH_SALTBYTES + SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;

			if ( false === $roh || strlen( $roh ) <= $mindestens ) {
				return new WP_Error( 'pacioli_einfuhr_leib', __( 'The body of this file is damaged or empty.', 'cluster-one-pacioli' ) );
			}

			$salz   = substr( $roh, 0, SODIUM_CRYPTO_PWHASH_SALTBYTES );
			$nonce  = substr( $roh, SODIUM_CRYPTO_PWHASH_SALTBYTES, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
			$cipher = substr( $roh, $mindestens );

			$schluessel = sodium_crypto_pwhash(
				SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
				(string) $passwort,
				$salz,
				max( 1, (int) $k[1] ),
				max( 8192, (int) $k[2] ),
				SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
			);

			$klar = sodium_crypto_secretbox_open( $cipher, $nonce, $schluessel );

			sodium_memzero( $schluessel );

			if ( false === $klar ) {
				return new WP_Error( 'pacioli_einfuhr_passwort', __( 'This file could not be opened with that password. Nothing has been changed.', 'cluster-one-pacioli' ) );
			}

			$daten = json_decode( $klar, true );

			if ( ! is_array( $daten ) || ! isset( $daten['kis'] ) || ! is_array( $daten['kis'] ) ) {
				return new WP_Error( 'pacioli_einfuhr_inhalt', __( 'The file opened, but what is inside is not a list of AIs.', 'cluster-one-pacioli' ) );
			}

			return array( 'kis' => $daten['kis'], 'kopf' => $kopf );
		}

		/**
		 * Legt die KIs aus einem geöffneten Umschlag an — für diesen Menschen.
		 *
		 * =============================================================
		 *  DER BESITZER IST IMMER DER EINLESENDE
		 * =============================================================
		 *
		 *  Der Umschlag nennt den Namen des Besitzers, aber er ist keine
		 *  Vollmacht. Würde die Einfuhr den Besitzer aus der Datei
		 *  nehmen, wäre sie ein Weg, einem anderen Menschen Schlüssel auf
		 *  die Rechnung zu legen.
		 *
		 *      Eine Datei, die sagt, wem etwas gehört, sagt nicht, wer
		 *      sie mitgebracht hat.
		 *
		 *  UND KEIN STILLES UMBENENNEN. `create()` sagt seit heute ab,
		 *  wenn der Name schon vergeben ist (Posten 3). Die Einfuhr
		 *  nimmt diese Absage ENTGEGEN und zählt sie — sie umgeht sie
		 *  nicht mit einer „-2".
		 *
		 *      Eine Einfuhr, die Namen anpasst, damit sie durchgeht,
		 *      liefert einen Zustand, den niemand ausgeführt hat.
		 *
		 * @return array array( 'angelegt' => array, 'abgelehnt' => array, 'ohne_schluessel' => int )
		 */
		public static function einlesen( $owner_id, array $kis ) {
			$angelegt  = array();
			$abgelehnt = array();
			$ohne      = 0;

			foreach ( $kis as $ki ) {
				if ( ! is_array( $ki ) || empty( $ki['name'] ) || empty( $ki['provider'] ) ) {
					$abgelehnt[] = array(
						'name'  => isset( $ki['name'] ) ? (string) $ki['name'] : '?',
						'grund' => __( 'incomplete entry in the file', 'cluster-one-pacioli' ),
					);
					continue;
				}

				$neu = Pacioli_Identities::create(
					array(
						'owner'    => (int) $owner_id,
						'name'     => (string) $ki['name'],
						'provider' => (string) $ki['provider'],
						'model'    => isset( $ki['model'] ) ? (string) $ki['model'] : '',
						'persona'  => isset( $ki['persona'] ) ? (string) $ki['persona'] : '',
					)
				);

				if ( is_wp_error( $neu ) ) {
					$abgelehnt[] = array(
						'name'  => (string) $ki['name'],
						'grund' => $neu->get_error_message(),
					);
					continue;
				}

				$angelegt[] = (string) $ki['name'];

				if ( empty( $ki['key'] ) ) {
					/* Eine KI ohne Schlüssel in der Datei ist keine
					 * Ablehnung — sie war beim Packen schon ohne. Gezählt,
					 * damit die Zahl der angelegten nicht mehr verspricht
					 * als sie hält. */
					$ohne++;
					continue;
				}

				$ok = Pacioli_Vault::set_secret( (int) $neu, (string) $ki['key'] );

				if ( is_wp_error( $ok ) ) {
					$abgelehnt[] = array(
						'name'  => (string) $ki['name'],
						'grund' => $ok->get_error_message(),
					);
				}
			}

			return array(
				'angelegt'        => $angelegt,
				'abgelehnt'       => $abgelehnt,
				'ohne_schluessel' => $ohne,
			);
		}

		/** Der Knoten, für den Kopf — gelesen, nicht getippt. */
		private static function knoten() {
			$host = '';

			if ( function_exists( 'home_url' ) ) {
				$host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
			}

			return '' === $host ? 'unknown' : self::eine_zeile( $host );
		}

		/**
		 * Eine Kopfzeile ist EINE Zeile.
		 *
		 * Ein Anzeigename mit einem Zeilenumbruch darin würde den Kopf
		 * spalten und eine erfundene Zeile erzeugen.
		 */
		private static function eine_zeile( $s ) {
			return trim( preg_replace( '/[\r\n]+/', ' ', (string) $s ) );
		}
	}
}
