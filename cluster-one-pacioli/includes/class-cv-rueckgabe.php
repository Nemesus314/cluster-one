<?php
/**
 * Die Rückgabe fremden Eigentums — eine Entscheidung, zwei Leser.
 *
 * ===========================================================================
 *  WARUM ES DIESE DATEI GIBT — Posten B1/B2, 10.10.2026
 * ===========================================================================
 *
 *  Der verwahrte Schlüssel ist NICHT der Schlüssel dieses Plugins. Es ist
 *  der des SysOps, der vor Pacioli in der Connector-Zeile lag. Pacioli hat
 *  ihn dorthin genommen, um ihn zu schützen — der eigene Kommentar an
 *  `class-cv-marker.php:210` sagt es: *„Da liegt ein echter Schlüssel.
 *  Sicherstellen statt überschreiben — es könnte der einzige Ort sein, an
 *  dem er existiert."*
 *
 *      Wer etwas wegnimmt, um es zu schützen, schuldet es beim Gehen
 *      zurück — nicht einen Hinweis darauf, dass es fehlt.      (Mammut)
 *
 *  ZWEI STELLEN FRAGEN DASSELBE, UND DESHALB STEHT ES HIER
 *
 *      `uninstall.php`                      tut es
 *      `CV_Admin::render_removal_choice()`  sagt VORHER, was passieren wird
 *
 *  Beide brauchen die Antwort auf „kann dieser Schlüssel zurück?". Zwei
 *  Stellen, die diese Frage selbst entscheiden, wären zwei Wahrheiten
 *  darüber — und die Vorhersage würde irgendwann etwas anderes sagen als
 *  der Lauf.                                                     (P1-12)
 *
 *      Eine Vorhersage, die ihre eigene Rechnung anstellt, ist keine
 *      Vorhersage. Sie ist eine zweite Meinung mit dem Anspruch der
 *      ersten.
 *
 *  Also: EINE Entscheidung (`pruefen()`), zwei Leser — `plan()` sagt sie,
 *  `vollziehen()` führt sie aus. Zwei Leser sind erlaubt.
 *
 *  SECHS AUSGÄNGE, JEDER MIT NAMEN
 *
 *      zurueckgegeben          der echte Schlüssel steht wieder in seiner
 *                              Anbieter-Zeile
 *      kein_verwahrter         für diesen Anbieter liegt nichts in
 *                              Verwahrung — die Zeile wird nur geleert
 *      mehrere_verwahrer       ZWEI Menschen haben für denselben Anbieter
 *                              einen Schlüssel in Verwahrung
 *      kein_entschlueseler     CV_Crypto ist nicht da, oder sodium fehlt
 *      kein_tresorschluessel   es gibt keinen Schlüssel zum Entschlüsseln
 *      nicht_lesbar            das Entschlüsseln schlug fehl
 *      keine_zeile             ein verwahrter Schlüssel ohne Anbieter-Zeile
 *
 *  `mehrere_verwahrer` STEHT IN MAMMUTS AUFTRAG NICHT. Er nennt drei
 *  Gründe; dieser ist der vierte, und er ist erreichbar:
 *  `CV_Marker::quarantine_owner()` nimmt den handelnden Menschen, sonst den
 *  ersten Administrator. Wird später wieder ein echter Schlüssel in die
 *  Anbieter-Zeile getippt und läuft `ensure_markers()` dann unter einem
 *  ANDEREN Administrator, liegt derselbe Anbieter zweimal in Verwahrung.
 *
 *      Ein Schlüssel, der in die falsche Zeile zurückgegeben wird, ist
 *      schlimmer als einer, der fehlt: der fehlende wird nachgetragen,
 *      der falsche wird bezahlt.
 *
 *  WAS NICHT ANGEFASST WIRD
 *
 *      Eine Anbieter-Zeile OHNE Marke dieses Knotens kommt in der Abfrage
 *      nicht vor und wird nicht beschrieben. Wer nach dem Einrichten wieder
 *      einen echten Schlüssel dort eingetragen hat, behält ihn.
 *
 *      Für einen verwahrten Schlüssel ohne Anbieter-Zeile wird KEINE Zeile
 *      angelegt. Eine neue Option wäre kein Zurückstellen, sondern ein
 *      Möbelstück mehr.
 *
 *  KEIN KLARTEXT VERLÄSST DIESE KLASSE. `plan()` gibt Namen und Ausgänge
 *  zurück, nie einen Schlüssel. `vollziehen()` schreibt ihn in seine Zeile
 *  und gibt ihn nicht heraus.
 *
 *      Eine Vorhersage, die den Schlüssel zeigt, um zu beweisen, dass sie
 *      ihn lesen kann, hat ihn gezeigt.
 */

defined( 'ABSPATH' ) || exit;

/* Der Riegel um die Klasse — Weg C, dritte Schicht. Ohne ihn stirbt ein
 * Knoten mit ZWEI Produktordnern an `Cannot redeclare class`. Und
 * `uninstall.php` holt diese Datei ausdrücklich, kann also auf einen
 * Zustand treffen, in dem der Lader sie schon geladen hat. */
if ( ! class_exists( 'Pacioli_Rueckgabe' ) ) {
	class Pacioli_Rueckgabe {

		const META      = '_pacioli_quarantine';
		const PRAEFIX   = 'connectors_ai_';
		const SUFFIX    = '_api_key';
		const MARKE     = 'CONNECTOR-VAULT--';

		/**
		 * Was liegt in Verwahrung, und bei wem?
		 *
		 * Gelesen am Schutzschild vorbei und ohne den Vault: diese Klasse
		 * läuft auch dann, wenn beim Entfernen nichts geladen ist.
		 *
		 * @return array array( provider => array( array( 'user' => int, 'cipher' => string ) ) )
		 */
		private static function verwahrt() {
			global $wpdb;

			$aus = array();

			$zeilen = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Suche in der Optionstabelle mit LIKE, die get_option nicht bietet; einmal je Durchgang, kein Cache
				$wpdb->prepare(
					"SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s",
					self::META
				)
			);

			foreach ( (array) $zeilen as $zeile ) {
				$beutel = maybe_unserialize( $zeile->meta_value );

				if ( ! is_array( $beutel ) ) {
					continue;
				}

				foreach ( $beutel as $provider => $cipher ) {
					if ( ! is_string( $cipher ) || '' === $cipher ) {
						continue;
					}

					$aus[ (string) $provider ][] = array(
						'user'   => (int) $zeile->user_id,
						'cipher' => $cipher,
					);
				}
			}

			return $aus;
		}

		/**
		 * Welche Anbieter-Zeilen verwaltet dieser Knoten?
		 *
		 * 18.10.2026 (Neo): Bis 0.8.24 hiess die Frage „welche Zeilen tragen
		 * eine Marke" und die Abfrage suchte `LIKE 'CONNECTOR-VAULT--%'`. Seit
		 * dem 11.10. ist der Ruhezustand einer verwalteten Zeile aber LEER
		 * (class-cv-marker.php: zeilen_raeumen schreibt '', intercept_write
		 * gibt '' zurueck). Die Abfrage fand nach dem ersten Sweep nichts
		 * mehr: jeder verwahrte Schluessel endete als `keine_zeile`, die
		 * Rueckgabe beim Entfernen lief nie, und die Vorhersage auf dem
		 * Entfernen-Schirm sagte dasselbe Falsche.
		 *
		 *     Eine Abfrage, die den alten Ruhezustand sucht, findet nach dem
		 *     Umbau genau die Zeilen, die noch nicht umgebaut sind.
		 *
		 * Jetzt zaehlen BEIDE Formen: die leere Zeile (der heutige
		 * Ruhezustand) und die Marke (Rest einer aelteren Fassung). Eine
		 * Zeile mit einem echten Schluessel darin kommt weiterhin nicht vor
		 * und wird nicht angefasst — wer nach dem Einrichten wieder einen
		 * echten Schluessel dort eingetragen hat, behaelt ihn.
		 *
		 * @return array array( provider => option_name )
		 */
		private static function markenzeilen() {
			global $wpdb;

			/* 0.8.49 (01.11.2026, Neo): Plugin Check sah die Marke roh im LIKE. Sie ist eine
			 * feste Konstante, kein Loch — aber prepare() mit esc_like() sagt dasselbe und
			 * laesst den Pruefer schweigen. Bis 0.8.48 stand hier:
			 *   "... OR option_value LIKE '" . self::MARKE . "%' )"
			 * Das %-Zeichen des ersten LIKE ist in prepare() als %% zu schreiben. */
			/* 0.8.50 (01.11.2026, Neo): Plugin Check wollte auch das erste Muster als Parameter
			 * sehen (LikeWildcardsInQuery). Bis 0.8.49 stand es im Text:
			 *   WHERE option_name LIKE 'connectors\\_ai\\_%%\\_api\\_key'
			 * Jetzt bauen esc_like() und ein freies % dasselbe Muster, prepare() setzt es ein.
			 * Gleiche Zeilen, gleiche Treffer. */
			$muster_name  = $wpdb->esc_like( 'connectors_ai_' ) . '%' . $wpdb->esc_like( '_api_key' );
			$muster_marke = $wpdb->esc_like( self::MARKE ) . '%';
			$namen = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Suche in der Optionstabelle mit LIKE, die get_option nicht bietet; einmal je Durchgang, kein Cache
				$wpdb->prepare(
					"SELECT option_name FROM {$wpdb->options}
					 WHERE option_name LIKE %s
					   AND ( option_value = '' OR option_value LIKE %s )",
					$muster_name,
					$muster_marke
				)
			);

			$aus = array();

			foreach ( (array) $namen as $name ) {
				/* Der Anbieter steht im Namen der Zeile — dieselbe Form, die
				 * `CV_Marker::option_name()` baut. Aufgelöst, nicht geraten. */
				$provider = (string) $name;

				if ( 0 === strpos( $provider, self::PRAEFIX ) ) {
					$provider = substr( $provider, strlen( self::PRAEFIX ) );
				}

				if ( self::SUFFIX === substr( $provider, -strlen( self::SUFFIX ) ) ) {
					$provider = substr( $provider, 0, -strlen( self::SUFFIX ) );
				}

				$aus[ $provider ] = (string) $name;
			}

			return $aus;
		}

		/**
		 * Ist der Entschlüsseler da — gefragt, nicht angenommen.
		 *
		 * `uninstall.php` wird per `include` in einen Zustand hineingezogen,
		 * den sie nicht hergestellt hat. Sie holt `class-cv-crypto.php`
		 * ausdrücklich; hier wird nachgefragt, ob es geklappt hat.
		 *
		 * @return array array( 'klasse' => bool, 'tresor' => bool )
		 */
		private static function entschlueseler() {
			$klasse = class_exists( 'Pacioli_Crypto' )
				&& method_exists( 'Pacioli_Crypto', 'decrypt' )
				&& method_exists( 'Pacioli_Crypto', 'is_available' )
				&& Pacioli_Crypto::is_available();

			return array(
				'klasse' => $klasse,
				'tresor' => $klasse && method_exists( 'Pacioli_Crypto', 'has_key' ) && Pacioli_Crypto::has_key(),
			);
		}

		/**
		 * DIE ENTSCHEIDUNG. Eine Stelle, zwei Leser.
		 *
		 * @return array array( provider => array( 'stand' => string, 'zeile' => string|'', 'klar' => string|null ) )
		 */
		private static function pruefen() {
			/* ---------------------------------------------------------
			 * OHNE $wpdb WIRD NICHTS BEHAUPTET — gefragt, nicht
			 * angenommen.
			 *
			 * In WordPress ist `$wpdb` immer da. Diese Klasse laeuft
			 * aber auch in `uninstall.php`, die per `include` in einen
			 * Zustand hineingezogen wird, den sie nicht hergestellt hat
			 * — und in den Welten der Zeugen.
			 *
			 *     Eine leere Antwort und ein „nicht messbar" duerfen
			 *     nicht dasselbe zurueckgeben, wenn eines davon auf dem
			 *     Schirm wie „alles in Ordnung" aussieht.
			 *
			 * `null` heisst NICHT GEMESSEN; `array()` heisst „nichts zu
			 * tun". Der Schirm hat fuer beides einen eigenen Satz.
			 * ------------------------------------------------------- */
			global $wpdb;

			if ( ! is_object( $wpdb )
				|| ! method_exists( $wpdb, 'get_results' )
				|| ! method_exists( $wpdb, 'get_col' ) ) {
				return null;
			}

			$verwahrt = self::verwahrt();
			$zeilen   = self::markenzeilen();
			$crypto   = self::entschlueseler();

			$aus = array();

			foreach ( $zeilen as $provider => $option_name ) {
				$treffer = isset( $verwahrt[ $provider ] ) ? $verwahrt[ $provider ] : array();

				if ( ! $treffer ) {
					$aus[ $provider ] = array( 'stand' => 'kein_verwahrter', 'zeile' => $option_name, 'klar' => null );
					continue;
				}

				if ( count( $treffer ) > 1 ) {
					$aus[ $provider ] = array( 'stand' => 'mehrere_verwahrer', 'zeile' => $option_name, 'klar' => null );
					continue;
				}

				if ( ! $crypto['klasse'] ) {
					$aus[ $provider ] = array( 'stand' => 'kein_entschlueseler', 'zeile' => $option_name, 'klar' => null );
					continue;
				}

				if ( ! $crypto['tresor'] ) {
					$aus[ $provider ] = array( 'stand' => 'kein_tresorschluessel', 'zeile' => $option_name, 'klar' => null );
					continue;
				}

				$klar = Pacioli_Crypto::decrypt( $treffer[0]['cipher'] );

				/* GEFRAGT, NICHT ANGENOMMEN: `decrypt()` gibt einen WP_Error
				 * zurück, und ein WP_Error in einer Anbieter-Zeile wäre ein
				 * Objekt an der Stelle eines Schlüssels. */
				if ( ! is_string( $klar ) || '' === $klar ) {
					$aus[ $provider ] = array( 'stand' => 'nicht_lesbar', 'zeile' => $option_name, 'klar' => null );
					continue;
				}

				$aus[ $provider ] = array( 'stand' => 'zurueckgegeben', 'zeile' => $option_name, 'klar' => $klar );
			}

			/* Verwahrt, aber keine Zeile dafür. Gemeldet, nicht angelegt. */
			foreach ( $verwahrt as $provider => $unused ) {
				if ( ! isset( $aus[ $provider ] ) ) {
					$aus[ $provider ] = array( 'stand' => 'keine_zeile', 'zeile' => '', 'klar' => null );
				}
			}

			return $aus;
		}

		/**
		 * DIE VORHERSAGE. Schreibt nichts und gibt keinen Schlüssel heraus.
		 *
		 * @return array array( provider => stand )
		 */
		public static function plan() {
			$satzliste = self::pruefen();

			if ( null === $satzliste ) {
				return null;   /* NICHT GEMESSEN, nicht „nichts" */
			}

			$aus = array();

			foreach ( $satzliste as $provider => $satz ) {
				$aus[ $provider ] = $satz['stand'];
			}

			return $aus;
		}

		/**
		 * DER VOLLZUG. Dieselbe Entscheidung, nur mit der Hand daran.
		 *
		 * @return array array( provider => stand )
		 */
		public static function vollziehen() {
			$satzliste = self::pruefen();

			if ( null === $satzliste ) {
				/* Keine Zeile wird angefasst. Eine Marke, die stehen
				 * bleibt, taeuscht einen Schluessel vor — eine Zeile,
				 * die geleert wird, waehrend der echte Schluessel noch
				 * lesbar daneben liegt, vernichtet einen. */
				return array( '*' => 'kein_zugriff' );
			}

			$aus = array();

			foreach ( $satzliste as $provider => $satz ) {
				$aus[ $provider ] = $satz['stand'];

				if ( '' === $satz['zeile'] ) {
					continue;
				}

				if ( 'zurueckgegeben' === $satz['stand'] && is_string( $satz['klar'] ) ) {
					update_option( $satz['zeile'], $satz['klar'] );
					continue;
				}

				/*
				 * „Es soll dort nichts zurückbleiben, was einen Schlüssel
				 * vortäuscht." — der eigene Satz von 0.3.0, und er gilt
				 * weiter für jede Zeile, in die nichts zurückgeht.
				 */
				update_option( $satz['zeile'], '' );
			}

			return $aus;
		}

		/**
		 * Für wen ist nach einem gescheiterten Versuch etwas zu tun?
		 *
		 * Nur die Ausgänge, bei denen ein Mensch handeln MUSS, weil ein
		 * Schlüssel verloren ist. `kein_verwahrter` gehört NICHT dazu: dort
		 * lag nie einer, also fehlt auch keiner.
		 *
		 *     Ein Hinweis auf einen Verlust ist richtig, wenn der Verlust
		 *     unvermeidlich war. War er vermeidbar, ist der Hinweis eine
		 *     Höflichkeit an der Stelle einer Reparatur.          (Mammut)
		 *
		 * @param array $stand array( provider => stand )
		 * @return array die Anbieter, für die ein Schlüssel verloren geht
		 */
		public static function verloren( array $stand ) {
			$aus = array();

			foreach ( $stand as $provider => $s ) {
				if ( in_array( $s, array( 'mehrere_verwahrer', 'kein_entschlueseler', 'kein_tresorschluessel', 'nicht_lesbar' ), true ) ) {
					$aus[] = (string) $provider;
				}
			}

			return $aus;
		}
	}
}
