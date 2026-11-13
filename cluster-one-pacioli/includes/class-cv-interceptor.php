<?php
/**
 * Der Türsteher.
 *
 * Seit 0.3.0 ist von dieser Klasse wenig übrig, und das ist der Fortschritt:
 * Der Eingriff in die Schlüsselauflösung ist fort. Er hat nicht funktioniert
 * (gemessen am 24.08.2026 — die Anbieter-Plugins lesen ihren Schlüssel
 * einmal bei `init` und benutzen danach ihre Kopie), und er wird nicht mehr
 * gebraucht: In der Options-Zeile steht ohnehin nur eine Marke, die CV_Marker
 * unmittelbar vor dem Absenden gegen den echten Schlüssel tauscht.
 *
 * Was bleibt, ist die Absage VOR dem Aufruf. Ohne sie liefe eine Anfrage
 * ohne Schlüssel bis zum Anbieter und käme als kryptischer Fehler zurück.
 * Mit ihr bekommt der Besitzer einen Satz, mit dem er etwas anfangen kann.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 *  DER RIEGEL UM DIE KLASSE — 12.09.2026, Weg C, dritte Schicht
 *
 *  Ohne ihn stirbt ein Knoten mit ZWEI Produktordnern an
 *  `Cannot redeclare class CV_Interceptor`, sobald der Lader seine EIGENEN Dateien
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
if ( ! class_exists( 'Pacioli_Interceptor' ) ) {
	class Pacioli_Interceptor {

		public static function init() {
			add_filter( 'wp_ai_client_prevent_prompt', array( __CLASS__, 'prevent_prompt' ), 10, 2 );
		}

		/**
		 * Blockiert Anfragen, die nicht sauber zurechenbar sind.
		 *
		 * @param bool|WP_Error $prevent
		 * @param mixed         $prompt
		 * @return bool|WP_Error
		 */
		public static function prevent_prompt( $prevent, $prompt = null ) {
			if ( $prevent ) {
				return $prevent;
			}

			// Ein gemeinsamer Schlüssel aus Environment oder Konstante schlägt
			// die Datenbank und ist von keinem Filter erreichbar.
			//
			// Der Grund für diese Sperre ist seit 0.4.1 schärfer als „wir
			// können es nicht sicherstellen". In den Quellen steht, dass Core
			// die Authentifizierung in diesem Fall GAR NICHT setzt:
			//
			//     // Skip if the key is already provided via env var or constant.
			//     if ( 'env' === $key_source || 'constant' === $key_source ) { continue; }
			//
			// Core tritt also ausdrücklich zur Seite — und wer den Schlüssel
			// dann tatsächlich liefert, wissen wir nicht. Möglicherweise liest
			// das SDK ihn selbst aus der Umgebung, an jeder Registry vorbei.
			// Eine Stelle, die jemandem gehört, den wir nicht kennen, ist keine
			// Grundlage für eine Zusicherung. Also sagt der Vault ab, statt sie
			// zu brechen: Grundregel R1 dort, wo sie sich durchsetzen lässt.
			$leaks = Pacioli_Guard::leaking_providers();

			if ( $leaks ) {
				return new WP_Error(
					'pacioli_shared_key_present',
					sprintf(
						/* translators: %s: Liste von Anbieter-IDs. */
						__( 'Connector Vault refused the call: a shared key is set for %s via an environment variable or a constant. As long as that exists, everyone works on the same bill.', 'cluster-one-pacioli' ),
						implode( ', ', array_keys( $leaks ) )
					)
				);
			}

			$identity = Pacioli_Vault::acting_identity();

			if ( ! $identity ) {
				return new WP_Error(
					'pacioli_no_identity',
					__( 'No AI identity is set for this request. Calls must go through pacioli_acting_as().', 'cluster-one-pacioli' )
				);
			}

			if ( ! Pacioli_Vault::has_secret( $identity ) ) {
				$owner = (int) get_user_meta( $identity, PACIOLI_META_OWNER, true );

				return new WP_Error(
					'pacioli_no_secret',
					( get_current_user_id() === $owner )
						? __( 'No access key is stored for this AI yet. It can be entered under "My AIs".', 'cluster-one-pacioli' )
						: __( 'This AI is currently unavailable.', 'cluster-one-pacioli' )
				);
			}

			return false;
		}

		/**
		 * Alle Anbieter, deren Zeile der Vault verwaltet.
		 *
		 * @deprecated 0.3.0 Nur noch Weiterleitung auf CV_Marker::providers().
		 * @return string[]
		 */
		public static function providers() {
			return Pacioli_Marker::providers();
		}

		/**
		 * @deprecated 0.3.0 Nur noch Weiterleitung auf CV_Marker::option_name().
		 */
		public static function option_name( $provider ) {
			return Pacioli_Marker::option_name( $provider );
		}
	}
}
