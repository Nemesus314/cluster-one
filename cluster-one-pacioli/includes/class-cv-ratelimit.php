<?php
/**
 * Notbremsen.
 *
 * Zwei verschiedene Dinge, die leicht verwechselt werden:
 *
 *   - Der SCHLEIFENSCHUTZ verhindert, dass KIs sich gegenseitig antworten.
 *   - Die GRENZE FÜR DIE SYSTEMIDENTITÄT deckelt den einzigen Teil des
 *     Systems, der unbeaufsichtigt arbeitet.
 *
 * Kontingente für Menschen gibt es bewusst nicht: Wer selbst zahlt,
 * braucht keine Erlaubnis.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 *  DER RIEGEL UM DIE KLASSE — 12.09.2026, Weg C, dritte Schicht
 *
 *  Ohne ihn stirbt ein Knoten mit ZWEI Produktordnern an
 *  `Cannot redeclare class CV_Ratelimit`, sobald der Lader seine EIGENEN Dateien
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
if ( ! class_exists( 'Pacioli_Ratelimit' ) ) {
	class Pacioli_Ratelimit {

		/**
		 * Zählt einen Vorgang und meldet, ob er noch erlaubt ist.
		 */
		public static function check( $bucket, $max, $window ) {
			$key   = 'pacioli_rl_' . md5( (string) $bucket . '|' . (int) $window );
			$count = (int) get_transient( $key );

			if ( $count >= (int) $max ) {
				return false;
			}

			set_transient( $key, $count + 1, (int) $window );

			return true;
		}

		/**
		 * Darf in diesem Raum noch eine KI-Antwort erzeugt werden?
		 */
		public static function room_allows( $room_id, $max_per_minute = 12 ) {
			$max = (int) apply_filters( 'pacioli_room_max_per_minute', $max_per_minute, $room_id );

			return self::check( 'room-' . $room_id, $max, MINUTE_IN_SECONDS );
		}

		/**
		 * Darf die Systemidentität jetzt verdichten?
		 */
		public static function system_allows() {
			$per_hour = (int) apply_filters( 'pacioli_system_max_per_hour', 6 );
			$per_day  = (int) apply_filters( 'pacioli_system_max_per_day', 40 );

			if ( ! self::check( 'system-hour', $per_hour, HOUR_IN_SECONDS ) ) {
				self::report_stop( 'hour', $per_hour );

				return false;
			}

			if ( ! self::check( 'system-day', $per_day, DAY_IN_SECONDS ) ) {
				self::report_stop( 'day', $per_day );

				return false;
			}

			return true;
		}

		/**
		 * Ist diese Nachricht als Auslöser zulässig?
		 *
		 * ---------------------------------------------------------------------
		 *  B1 — R4 war absolut, und das war zu hart.
		 *
		 *  Die Vorgabe bleibt, was sie war: Eine Nachricht mit KI-Absender löst
		 *  keinen Aufruf aus. Als Schutz gegen nächtliches Aufschaukeln ist das
		 *  richtig.
		 *
		 *  Aber als unumstössliches Verbot macht es KI-zu-KI strukturell
		 *  unmöglich — und für manche Anwendungen ist genau das der Zweck. Ob
		 *  eine KI auf die Äusserung einer anderen eingehen darf, ist eine
		 *  Produktentscheidung und gehört nicht in ein Zulieferer-Plugin.
		 *
		 *  Also: Filter mit `false` als Vorgabe für KI-Absender. Wer nichts
		 *  tut, bekommt den strengen Schutz. Wer KI-zu-KI will, muss es
		 *  ausdrücklich sagen — und trägt die Taktung dann selbst.
		 * ---------------------------------------------------------------------
		 *
		 * @param int   $author_id Verfasser der auslösenden Nachricht.
		 * @param array $context   Freier Kontext für die entscheidende Schicht.
		 */
		public static function may_trigger( $author_id, array $context = array() ) {
			$author_id = (int) $author_id;
			$allowed   = ! Pacioli_Identities::is_ai( $author_id );

			/**
			 * Darf eine Nachricht dieses Verfassers einen KI-Aufruf auslösen?
			 *
			 * Vorgabe: bei KI-Absendern `false`. Wer das aufhebt, übernimmt die
			 * Verantwortung für die Taktung — Rundenlogik, Wartezeiten, ein
			 * Mensch als Pilot.
			 *
			 * @param bool  $allowed
			 * @param int   $author_id
			 * @param array $context
			 */
			return (bool) apply_filters( 'pacioli_may_trigger', $allowed, $author_id, $context );
		}

		/**
		 * Meldet dem Administrator, dass die Systemidentität angehalten hat.
		 */
		private static function report_stop( $window, $limit ) {
			$flag = 'pacioli_reported_' . $window;

			if ( get_transient( $flag ) ) {
				return;
			}

			set_transient( $flag, 1, HOUR_IN_SECONDS );

			do_action( 'pacioli_system_limit_reached', $window, $limit );
		}
	}
}
