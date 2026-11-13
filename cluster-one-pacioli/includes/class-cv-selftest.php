<?php
/**
 * Was dieses Plugin über sich selbst beweisen kann — und was nicht.
 *
 * =====================================================================
 *  A2 — warum es `has_passed()` nicht mehr gibt
 * =====================================================================
 *
 *  In 0.1.0 mass die entscheidende Prüfung mit `get_option()`. Damit sah
 *  sie die Optionsschicht — und die war nie strittig: `pre_option_` läuft
 *  in `get_option()` ganz oben, vor dem Cache. Diese Prüfung KONNTE nicht
 *  anders als grün zu werden.
 *
 *  Schlimmer als nutzlos war sie, weil sie im Erfolgsfall die Warnung
 *  löschte, die auf die ungeprüfte Annahme hinwies. Der Dateikopf sagte
 *  damals selbst, sie sei „die notwendige, nicht die hinreichende
 *  Bedingung" — und der Code hielt sich nicht an seinen eigenen Kommentar.
 *
 *  Die Annahme ist inzwischen extern gemessen worden. Sie war falsch.
 *
 *  Daraus die Regel, die dieses Plugin jetzt befolgt:
 *
 *      Ein Zeuge, der aus dem falschen Grund grün ist, ist schlimmer als
 *      keiner.
 *
 *  Die Prüfungen hier bezeugen ausschliesslich, was in DIESEM Prozess
 *  entschieden wird — Riegel, Zuordnung, Aufräumen. Ob der getauschte
 *  Schlüssel beim Anbieter ankommt, entscheidet sich ausserhalb, und
 *  deshalb wird es hier auch nicht behauptet: Dafür gibt es die
 *  Fremdmessung, und die bestätigt ein Mensch.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 *  DER RIEGEL UM DIE KLASSE — 12.09.2026, Weg C, dritte Schicht
 *
 *  Ohne ihn stirbt ein Knoten mit ZWEI Produktordnern an
 *  `Cannot redeclare class CV_Selftest`, sobald der Lader seine EIGENEN Dateien
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
if ( ! class_exists( 'Pacioli_Selftest' ) ) {
	class Pacioli_Selftest {

		const OPTION_WITNESS = 'pacioli_witness_confirmation';

		/**
		 * Der Slug dieser Seite.
		 *
		 * Er stand bis 0.7.2 viermal als Zeichenkette da. Seit CV_Guard fragt, ob
		 * der Betrachter gerade HIER steht, wäre es das fünfte Mal gewesen — und
		 * das fünfte Mal ist eines zu viel: Wer den Slug ändert und eine Stelle
		 * übersieht, bekommt keinen Fehler, sondern eine Meldung, die nirgends
		 * mehr erscheint. Der Name wohnt bei dem, der die Seite anmeldet.
		 */
		const PAGE = 'cv-selftest';

		/**
		 * Das Schloss dieser Seite.
		 *
		 * SEIT DEM 16.09.2026 IST ES AN DREI STELLEN GEFRAGT, DIE ZUSAMMEN
		 * EINE TUER BILDEN: bei der Anmeldung, im Riegel des Rueckrufs und
		 * beim Zeichnen des Reiters. Diese drei lesen ab jetzt hier.
		 *
		 * GEZAEHLT UND NICHT GESCHAETZT: in `handle_run()`, `handle_sweep()`
		 * und `handle_witness()` steht die Zeichenkette weiter als Literal.
		 * Die drei sind Schreibwege und keine Tuer; sie stehen nicht in
		 * diesem Auftrag, und ich fasse sie nicht mit an. Sie stehen hier
		 * benannt, damit sie nicht spaeter als Fund auftauchen: wer sie
		 * nachziehen will, bekommt es in drei Zeilen.
		 * Drei Zeichenketten, die uebereinstimmen MUESSEN, sind drei
		 * Gelegenheiten, dass eine nicht mitgeht — und dieselbe Rechnung
		 * steht zehn Zeilen hoeher schon einmal, fuer den Slug.
		 *
		 * Beim Schloss ist die Folge groesser als beim Namen: wer eine Stelle
		 * uebersieht, bekommt keinen Fehler, sondern eine Tuer und einen
		 * Riegel, die verschiedene Fragen stellen.
		 *
		 *     Ein Riegel, der ein anderes Schloss hat als seine Tuer, ist
		 *     kein zweiter Riegel. Er ist eine zweite Tuer.
		 *
		 * DER WERT IST UNVERAENDERT `'manage_options'`. Er zieht hierher um,
		 * er aendert sich nicht.
		 */
		const CAP = 'manage_options';

		public static function init() {
			add_action( 'admin_menu', array( __CLASS__, 'add_page' ) );
			add_action( 'admin_post_pacioli_run_selftest', array( __CLASS__, 'handle_run' ) );
			add_action( 'admin_post_pacioli_confirm_witness', array( __CLASS__, 'handle_witness' ) );
			add_action( 'admin_post_pacioli_witness_turing', array( __CLASS__, 'handle_witness_turing' ) ); // 0.8.46
			add_action( 'admin_post_pacioli_sweep', array( __CLASS__, 'handle_sweep' ) );
		}

		/**
		 * 0.8.46 (30.10.2026, Neo) — DIE EINE SCHREIBSTELLE, JETZT MIT ZWEI RUFERN.
		 *
		 * Bis 0.8.45 stand das update_option in handle_witness(); seit es einen zweiten Weg zum
		 * Eintrag gibt (Turing misst, Pacioli traegt ein), waere ein zweites update_option genau
		 * der zweite Schreiber, den dieses Haus seit Wochen bei anderen misst (P1-12). Die Zeile
		 * ist umgezogen, nicht verdoppelt; beide Handler rufen sie.
		 */
		public static function witness_schreiben( array $eintrag ) {
			update_option( self::OPTION_WITNESS, $eintrag, false );
		}

		/**
		 * 0.8.46 (30.10.2026, Neo) — PACIOLI LAESST TURING MESSEN UND TRAEGT EIN.
		 *
		 * Nemesus: „Der Mensch ist eine Fehlerquelle." Bis 0.8.45 musste der SysOp Turings Urteil
		 * lesen und als Notiz abtippen. Jetzt faehrt Pacioli den Durchgang selbst, ueber Turings
		 * Funktion, wenn Turing auf dem Knoten liegt.
		 *
		 * DIE RICHTUNG IST DIE SACHE: Turing schreibt weiter nichts. Es traegt mit Absicht keinen
		 * Boden und keine Option, damit es nichts veraendern kann, was es diagnostiziert – ein
		 * Knopf in Turing, der nach Pacioli schreibt, waere seine erste schreibende Hand in ein
		 * anderes Haus. Also ruft Pacioli, und Pacioli schreibt – seine eigene Option, wie bisher.
		 *
		 * FAIL CLOSED: eingetragen wird NUR bei GRUEN (gesamt_pacioli, Turing >= 3.14.7). Alles
		 * andere – ROT, GEMISCHT, UNKLAR, UNBEANTWORTBAR, ein aelteres Turing ohne diese Frage –
		 * bleibt gelb und wird auf der Seite genannt, je Anbieter. Die Notiz baut sich aus der
		 * Messung: Turing-Fassung, Datum, Anbieter, Unterbau-Befund – und sagt, dass Pacioli
		 * den Durchgang angestossen hat. Weniger „von aussen" als ein Mensch am Anbieter-
		 * Dashboard; der Eintrag sagt das selbst.
		 */
		public static function handle_witness_turing() {
			if ( ! current_user_can( self::CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}
			check_admin_referer( 'pacioli_witness_turing' );
			$me = (int) get_current_user_id();

			if ( ! function_exists( 'turing_durchgang' ) ) { // 0.9.1: turing_ statt tu_ — 0.9.0 griff hier ins Leere (Turing 3.15.0 hatte umbenannt)
				wp_safe_redirect( self::page_url( array( 'witness' => 'turing_fehlt' ) ) );
				exit;
			}

			$d = turing_durchgang();
			if ( ! is_array( $d ) || ! isset( $d['gesamt_pacioli'] ) ) {
				wp_safe_redirect( self::page_url( array( 'witness' => 'turing_alt' ) ) );
				exit;
			}

			$urteil = (string) $d['gesamt_pacioli'];
			$zeilen = array();
			foreach ( (array) ( $d['zeilen'] ?? array() ) as $cid => $z ) {
				$zeilen[ (string) $cid ] = (string) ( $z['pacioli'] ?? '?' );
			}
			set_transient( 'pacioli_turing_' . $me, array( 'urteil' => $urteil, 'zeilen' => $zeilen, 'unterbau' => (string) ( $d['gesamt'] ?? '' ) ), 300 );

			if ( 'GRUEN' !== $urteil ) {
				wp_safe_redirect( self::page_url( array( 'witness' => 'turing_' . sanitize_key( strtolower( $urteil ) ) ) ) );
				exit;
			}

			$gruen = array();
			foreach ( $zeilen as $cid => $u ) {
				if ( 'GRUEN' === $u ) {
					$gruen[] = $cid;
				}
			}
			$note = sprintf(
				/* translators: 1: Turing version, 2: date, 3: number of providers, 4: provider list, 5: substrate verdict (switching after first read). */
				__( 'Turing %1$s Pacioli witness, %2$s: %3$d providers GREEN (%4$s) — the key in the outgoing POST comes from no option row. Substrate: switching after the first read %5$s (not used by Pacioli). Run started from Pacioli.', 'cluster-one-pacioli' ),
				function_exists( 'turing_version' ) ? turing_version() : '?',
				date_i18n( get_option( 'date_format' ) . ' H:i' ),
				count( $gruen ),
				implode( ', ', $gruen ),
				(string) ( $d['gesamt'] ?? '?' )
			);

			$grund = self::witness_refusal( (int) Pacioli_Marker::bound_calls()['count'], $note );
			if ( '' !== $grund ) {
				wp_safe_redirect( self::page_url( array( 'witness' => $grund ) ) );
				exit;
			}

			self::witness_schreiben( array(
				'confirmed' => true,
				'version'   => PACIOLI_VERSION,
				'by'        => $me,
				'time'      => time(),
				'note'      => $note,
				'turing'    => array( 'fassung' => function_exists( 'turing_version' ) ? turing_version() : '', 'urteil' => $urteil, 'anbieter' => $gruen ),
			) );
			wp_safe_redirect( self::page_url( array( 'witness' => 'recorded' ) ) );
			exit;
		}

		/**
		 * Hat ein Mensch bestätigt, dass die Fremdmessung grün war?
		 *
		 * Bewusst keine Selbstauskunft: Diese Angabe kann das Plugin nicht
		 * erzeugen, nur entgegennehmen.
		 */
		public static function witness_confirmed() {
			$c = get_option( self::OPTION_WITNESS );

			return is_array( $c ) && ! empty( $c['confirmed'] ) && PACIOLI_VERSION === ( $c['version'] ?? '' );
		}

		/**
		 * Woran wurde gemessen? — Fassung UND Abdruck der Dateien, die liefen.
		 *
		 * =====================================================================
		 *  Warum das hier steht, seit dem 02.09.2026
		 * =====================================================================
		 *
		 *  Auf einem echten Knoten standen zwei rote Zeilen. Die Werkbank
		 *  musste zwei Pakete auspacken und vier Dateien vergleichen, um die
		 *  Frage zu beantworten, aus WELCHEM Stand sie stammen — eine Frage,
		 *  die auf dieser Seite hätte stehen können.
		 *
		 *      Eine Prüfung, die ihren Gegenstand nicht benennt, ist ein
		 *      Ergebnis ohne Adresse. Sie sagt, dass etwas rot ist, und nicht,
		 *      was.
		 *
		 *  DIE FASSUNGSNUMMER ALLEIN REICHT NICHT, und das ist Kleinrocks Regel:
		 *
		 *      Eine Fassungsnummer im Kopf sagt, was der Autor glaubte.
		 *      sha1_file() sagt, welche Datei gelaufen ist.
		 *
		 *  GEMESSEN, NICHT AUFGEZÄHLT: Die Liste kommt aus
		 *  `get_included_files()`, gefiltert auf dieses Plugin — also aus dem,
		 *  was der Prozess tatsächlich geladen hat. Eine Datei, die dazukommt,
		 *  steht am nächsten Tag von selbst darin; eine, die nicht geladen
		 *  wurde, fehlt zu Recht.
		 *
		 *  Der `abdruck` ist der sha1 über alle Paare `pfad:sha1`, sortiert.
		 *  Eine Zahl, die man in einen Bericht schreiben kann, ohne fünfzehn
		 *  Zeilen mitzuschleppen — und die sich ändert, sobald irgendeine
		 *  dieser Dateien sich ändert.
		 *
		 * @return array{version:string,abdruck:string,dateien:array<string,string>}
		 */
		public static function subject() {
			$dir     = defined( 'PACIOLI_DIR' ) ? PACIOLI_DIR : '';
			$dateien = array();

			foreach ( get_included_files() as $pfad ) {
				if ( '' === $dir || 0 !== strpos( $pfad, $dir ) ) {
					continue;
				}

				$sha = @sha1_file( $pfad );

				if ( ! $sha ) {
					continue;
				}

				$dateien[ substr( $pfad, strlen( $dir ) ) ] = $sha;
			}

			ksort( $dateien );

			$zeilen = array();
			foreach ( $dateien as $rel => $sha ) {
				$zeilen[] = $rel . ':' . $sha;
			}

			// 02.09.2026, H186 — DIE HAUPTDATEI EINZELN.
			//
			// Der `abdruck` aendert sich, sobald IRGENDEINE Datei sich aendert.
			// Das ist richtig fuer die Frage „ist das derselbe Stand", aber
			// unbrauchbar zum Zitieren: zwei Berichte mit verschiedenen
			// Abdruecken sagen nicht, worin sie sich unterscheiden.
			//
			// Der sha1 der HAUPTDATEI ist die eine Zahl, die man in einen
			// Bericht schreibt. Sie traegt die Fassungsnummer in sich — CV_VERSION
			// steht in derselben Datei — und ist deshalb genau dann verschieden,
			// wenn die Nummer es auch sein sollte.
			//
			//     Eine Fassungsnummer sagt, was der Autor glaubte. Der sha1 der
			//     Datei, in der sie steht, sagt, ob er sie geaendert hat.
			$haupt = defined( 'PACIOLI_FILE' ) ? PACIOLI_FILE : '';

			return array(
				'version'    => defined( 'PACIOLI_VERSION' ) ? PACIOLI_VERSION : '',
				'hauptdatei' => $haupt ? basename( $haupt ) : '',
				'haupt_sha1' => ( $haupt && is_readable( $haupt ) ) ? (string) @sha1_file( $haupt ) : '',
				'abdruck'    => $zeilen ? sha1( implode( "\n", $zeilen ) ) : '',
				'dateien'    => $dateien,
			);
		}

		/**
		 * Misst — und stempelt, WORAN gemessen wurde.
		 *
		 * H186. Steht als eigene Methode da und nicht im Formular-Empfänger,
		 * damit ein Zeuge sie aufrufen kann, ohne einen Klick nachzustellen.
		 *
		 *     Ein Stempel, den nur ein Formular setzt, wird von keinem Zeugen
		 *     geprueft — und faellt genau dann weg, wenn ihn jemand
		 *     wegoptimiert.
		 *
		 * @return array{gegenstand:array,zeit:int,zeilen:array}
		 */
		public static function measure() {
			// Der Gegenstand ZUERST: was danach kommt, hat ihn nicht mehr
			// veraendert, und was vorher war, gehoert nicht dazu.
			$gegenstand = self::subject();

			return array(
				'gegenstand' => $gegenstand,
				'zeit'       => time(),
				'zeilen'     => self::run(),
			);
		}

		public static function witness_record() {
			$c = get_option( self::OPTION_WITNESS );

			return is_array( $c ) ? $c : array();
		}

		/**
		 * =====================================================================
		 *  DER RIEGEL VOR DER BESTAETIGUNG — Posten 3, 08.10.2026
		 * =====================================================================
		 *
		 *  AUF ZWEI KNOTEN STAND EINE BESTAETIGUNG, DIE NIEMAND GEBEN WOLLTE.
		 *  Der SysOp, der diese Suite mitentworfen hat, hat dazu gesagt: „Ich
		 *  habe gar nichts gemacht. Das PlugIn hat es gemacht. Was es da
		 *  macht? Keine Ahnung." Gemessen wurde danach: genau eine
		 *  Schreibstelle, hinter `manage_options` und Nonce. Das Plugin setzt
		 *  sie nicht selbst — der Knopf wurde gedrueckt.
		 *
		 *  DIE LAGE AUF DEM SCHIRM war zwoelf gruene Haken, gerade erzeugt,
		 *  und darunter ein Knopf, in dem das Wort „gruen" stand. Auf dem
		 *  Knoten selbst: 0 KI-Konten, 0 ausgehende Aufrufe, Tausche „nicht
		 *  gemessen".
		 *
		 *      Ein Knopf, dessen Text von dem redet, was auf dem Schirm
		 *      gerade gruen ist, bekommt die Bestaetigung fuer etwas
		 *      anderes.
		 *
		 *  DREI ZEILEN WEITER OBEN STEHT DER EIGENE SATZ: hier sei „keine
		 *  Schaltflaeche ‚Pruefung bestanden', sondern die Bestaetigung eines
		 *  Menschen". Der Satz war richtig und ist in der Oberflaeche nicht
		 *  angekommen. Der Knopf war der Schaltflaeche naeher als der
		 *  Erklaerung.
		 *
		 *  UND DAS IST DERSELBE FEHLER WIE BEIM VORGAENGER, den diese Seite
		 *  selbst anklagt: der hat an der Options-Schicht gemessen, wo alles
		 *  in Ordnung war, ist gruen geworden und hat damit die Warnung
		 *  geloescht, die auf die ungeprueffte Annahme zeigte. Diese
		 *  Bestaetigung loescht genau dieselbe Warnung (CV_Guard :150) — mit
		 *  einem Klick und ohne Messung.
		 *
		 * ---------------------------------------------------------------------
		 *  WAS DER RIEGEL IST UND WAS ER NICHT IST
		 *
		 *  Er weist den UNMOEGLICHEN Fall ab, nicht den falschen: solange
		 *  kein einziger Aufruf im Namen einer gebundenen KI hinausgegangen
		 *  ist, gibt es auf diesem Knoten nichts, was eine Fremdmessung
		 *  gemessen haben koennte. Dass eine Messung WIRKLICH stattgefunden
		 *  hat, kann dieses Plugin nicht pruefen — sonst braeuchte es die
		 *  Bestaetigung nicht.
		 *
		 *      Ein Riegel, der den unmoeglichen Fall abweist, darf nicht als
		 *      Beweis fuer den moeglichen gelesen werden. Sonst ist er die
		 *      naechste Beruhigung.
		 *
		 *  DESHALB ZWEI STUECKE und nicht eines:
		 *
		 *      witness_gate()     fragt nur den Knoten. Zwei Leser: der
		 *                         Handler und das Formular — das Formular
		 *                         soll nicht anbieten, was der Server
		 *                         abweist.
		 *      witness_refusal()  fragt den Knoten UND die Notiz. Einen
		 *                         Leser: den Handler, denn nur dort gibt es
		 *                         eine Notiz.
		 *
		 *  Beide sind rein und nehmen ihre Zahl entgegen, statt sie zu
		 *  holen. Ein Riegel, den man nur lesen und nicht fahren kann, ist
		 *  eine Behauptung ueber Verhalten.
		 * =====================================================================
		 */
		const WITNESS_NO_CALLS = 'keine_ausgehenden_aufrufe';
		const WITNESS_NO_NOTE  = 'keine_notiz';

		/**
		 * Darf dieser KNOTEN ueberhaupt eine Bestaetigung aufnehmen?
		 *
		 * @param int $bound_calls Gesehene Aufrufe im Namen einer gebundenen KI.
		 * @return string '' = ja, sonst der Grund.
		 */
		public static function witness_gate( $bound_calls ) {
			if ( 0 === (int) $bound_calls ) {
				return self::WITNESS_NO_CALLS;
			}

			return '';
		}

		/**
		 * Darf DIESE Bestaetigung aufgenommen werden?
		 *
		 * Die Notiz ist Pflicht, und das ist kein Formalismus: wer nicht
		 * sagen kann, womit gemessen wurde, hat nicht gemessen. Geprueft
		 * wird dabei NICHT der Inhalt — drei Woerter koennen gelogen sein.
		 * Geprueft wird, dass jemand etwas hinschreiben musste, bevor der
		 * Knopf wirkt.
		 *
		 *     Die Pflichtnotiz ist ein Zwang zum Nachdenken und keine
		 *     Pruefung. Wer sie fuer eine Pruefung haelt, hat die naechste
		 *     Beruhigung gebaut.
		 *
		 * `trim()` und nicht `empty()`: drei Leerzeichen sind keine Auskunft.
		 *
		 * @param int    $bound_calls Gesehene gebundene Aufrufe.
		 * @param string $note        Die Notiz, wie sie eingegeben wurde.
		 * @return string '' = ja, sonst der Grund.
		 */
		public static function witness_refusal( $bound_calls, $note ) {
			$grund = self::witness_gate( $bound_calls );

			if ( '' !== $grund ) {
				return $grund;
			}

			if ( '' === trim( (string) $note ) ) {
				return self::WITNESS_NO_NOTE;
			}

			return '';
		}

		/**
		 * DIE SEITE ZIEHT AUS „WERKZEUGE" AUS — UND BEHAELT IHREN SLUG.
		 *
		 * Bis zum 16.09.2026 stand hier `add_management_page( … )`. Der Punkt
		 * unter Werkzeuge hiess „Connector Vault", und das war der Name des
		 * PRODUKTS auf einer Seite, die den SELBSTTEST zeigt. Solange es ein
		 * Produkt gab, war das kein Unterschied. Seit im Menue „Pacioli"
		 * steht, zeigte er auf etwas, das er nicht ist.
		 *
		 *     Ein Name, der auf etwas zeigt, das er nicht ist, wird nicht
		 *     dadurch richtig, dass er frueher gestimmt hat.
		 *
		 * WAS SICH AENDERT: der Eintrag. WAS NICHT: der Slug, das Schloss,
		 * der Rueckruf, die Seite.
		 *
		 *   `null` als Eltern ist die Form, die der Boden selbst benennt
		 *   (kern.php :2775): die Seite ist angemeldet und unter ihrer
		 *   eigenen Adresse erreichbar, sie bekommt nur keinen Punkt.
		 *   VERTRAG 25 §1 verlangt genau das —
		 *
		 *       Keine Seite verliert ihren Weg, bevor sie einen neuen hat.
		 *
		 *   P1-11 verlangt den Rest: der Slug `cv-selftest` wird NICHT
		 *   umbenannt. Er steht in Lesezeichen.
		 *
		 * BENANNTE GRENZE, UND SIE IST EIN BEFUND UND KEINE NEBENSACHE:
		 * die ADRESSE aendert sich trotzdem. Unter Werkzeuge hiess sie
		 * `tools.php?page=cv-selftest`, ab jetzt `admin.php?page=cv-selftest`.
		 * Ein Lesezeichen auf die alte Adresse geht ins Leere, und Christian
		 * hat diese Seite eine Woche lang taeglich benutzt. Der Umzug ist
		 * bestellt, eine Umleitung von der alten Adresse ist es NICHT — sie
		 * steht als Vorschlag mit gefahrener Gegenprobe im Vollzug 52 und
		 * nicht hier. Wer sie bestellt, bekommt sie in acht Zeilen.
		 *
		 * DER MENUETITEL BLEIBT STEHEN, weil er nie angezeigt wird — dieselbe
		 * Rechnung wie in `CV_Admin::add_pages()`. Die Beschriftung, die ein
		 * Mensch sieht, ist der REITER.
		 *
		 * ---------------------------------------------------------------------
		 *  UND DIESER SATZ HAT DREI TAGE GELOGEN — berichtigt am 11.10.2026
		 *
		 *  Hier stand: „… und der heisst „Self-test"." Am 08.10. (Teil 3 §5a)
		 *  bekam die Seite zwei Reiter, und sie heissen „Equipment"
		 *  (class-cv-admin.php:373) und „For developers" (:403). „Self-test"
		 *  steht seitdem nirgends mehr im Code — die msgid ist verwaist und
		 *  steht als B14 im Katalog-Bericht.
		 *
		 *      Ein Name, der nach dem Umzug seines Gegenstands stehen
		 *      bleibt, ist der naechste falsche Wegweiser — und ein
		 *      Kommentar ist der Ort, an dem er am laengsten unbemerkt
		 *      bleibt, weil ihn kein Zeuge liest.
		 *
		 *  Gefunden auf Mammuts Frage nach B14, nicht von einem Zeugen.
		 *  Dass ich dafuer keinen habe, ist der Fund hinter dem Fund: meine
		 *  Zeugen messen Reiterbeschriftungen gegen den Katalog
		 *  (zeuge-ueberschrift, zeuge-reiter, E6), aber keiner misst, ob ein
		 *  KOMMENTAR eine Beschriftung nennt, die es nicht mehr gibt.
		 *  Gemeldet, nicht gebaut: ein Zeuge, der jede in Anfuehrungszeichen
		 *  zitierte Beschriftung in Kommentaren gegen die msgid-Liste
		 *  prueft, waere ein neuer Gegenstand und kein Nachziehen.
		 * ------------------------------------------------------------------- */
		/**
		 * ZWEI SCHLUESSEL, EIN SCHLOSS — Teil 3 §5b, 08.10.2026.
		 *
		 * `add_submenu_page()` nimmt EINE Capability. Das ODER laesst sich
		 * also nicht an die Tuer schreiben, sondern nur die, die der
		 * Betrachter tatsaechlich haelt.
		 *
		 *     Wer eine Tuer mit zwei Schluesseln anmeldet, muss den
		 *     nennen, den der Besucher dabei hat.
		 *
		 * DER WIRKLICHE RIEGEL STEHT IM RUECKRUF, und er kennt beide.
		 * Diese Wahl hier entscheidet nicht, wer hindarf — sie entscheidet,
		 * unter welchem Namen WordPress die Adresse anlegt.
		 *
		 * REIHENFOLGE MIT GRUND: zuerst `self::CAP`. Wer beides hat, soll
		 * unter der engeren Capability angemeldet sein — dann aendert sich
		 * fuer jeden Administrator genau nichts gegenueber 0.8.15.
		 */
		public static function add_page() {
			/* 0.8.45 (30.10.2026, Neo): DREI SCHLUESSEL AN DER TUER. Bis 0.8.44 zwei – manage_options
			 * oder cv_see_internals. Jetzt auch: wer laut Boden dabei ist ('cluster_one_access', die
			 * Weissliste des Knotens). Nemesus' Regel vom 30.10.: Sehen duerfen alle, Tun nur mit Recht.
			 * Die Anmeldung muss denselben Schluessel tragen wie darf_lesen(), sonst steht WordPress vor
			 * der Tuer, die der Riegel dahinter oeffnen wuerde. */
			$cap = self::CAP;
			if ( ! current_user_can( self::CAP ) ) {
				if ( current_user_can( PACIOLI_SEE ) ) {
					$cap = PACIOLI_SEE;
				} elseif ( function_exists( 'cluster_one_can_access' ) && cluster_one_can_access() ) {
					$cap = 'cluster_one_access';
				}
			}

			add_submenu_page(
				null,
				__( 'Connector Vault', 'cluster-one-pacioli' ),
				__( 'Connector Vault', 'cluster-one-pacioli' ),
				$cap,
				self::PAGE,
				array( __CLASS__, 'render' )
			);
		}

		/**
		 * Darf dieser Betrachter die Einbau-Auskunft LESEN?
		 *
		 * EINE Stelle. Gelesen vom Riegel in render(), von add_page() und
		 * von CV_Admin::darf() ueber `cap_oder`.
		 *
		 *     manage_options konnte diese Seite schon immer. Das Neue ist
		 *     nicht, dass der Administrator darf — es ist, dass ein
		 *     Nicht-Administrator duerfen KANN.
		 */
		/**
		 * Wie der Abschnitt mit den Betriebszahlen heisst — EINE Quelle.
		 *
		 * Er wird an DREI Stellen genannt: in der Ueberschrift auf der
		 * Tafel und in zwei Pruefungszeilen, die auf ihn verweisen. Bis
		 * heute stand der Name dreimal als Zeichenkette da — und beim
		 * Umzug waeren zwei davon stehen geblieben.
		 *
		 *     Genau das war Posten 1. Ein Umzug, der die Saetze darueber
		 *     nicht mitnimmt, erzeugt die naechsten Wegweiser ins Leere.
		 */
		public static function abschnitt_betrieb() {
			return __( 'What live operation says', 'cluster-one-pacioli' );
		}

		public static function darf_lesen() {
			/* 0.8.45 (30.10.2026, Neo): … oder dabei (Boden-Weissliste). Nemesus, Sicherheitstest als
			 * MenschA: „Für Entwickler" fehlte – mit Absicht nach der Regel vom 08.10. („Eine
			 * Voreinstellung soll den schuetzen, der nicht fragen wird"). Seine Regel vom 30.10. kehrt sie
			 * um: wer SysOp-Faehigkeiten lernen soll, muss sehen, was ein SysOp sieht. Gelesen wird hier
			 * nichts Geheimes – Pruefergebnisse, Fassung, Betriebszahlen, der Nachweis, das Woerterbuch.
			 * Die drei Handgriffe (handle_run, handle_sweep, handle_witness) bleiben auf self::CAP. */
			return current_user_can( self::CAP )
				|| current_user_can( PACIOLI_SEE )
				|| ( function_exists( 'cluster_one_can_access' ) && cluster_one_can_access() );
		}

		/** 0.8.45: darf dieser Betrachter die drei Handgriffe tun? EIN Name fuer das Schloss der Knoepfe. */
		public static function darf_handeln() {
			return current_user_can( self::CAP );
		}

		/**
		 * Die Adresse dieser Seite — EINE Stelle, nicht vier.
		 *
		 * DAS WAR EIN FUND BEIM UMZUG UND KEINE VORKEHRUNG. Bis zum
		 * 16.09.2026 stand `admin_url( 'tools.php?page=' . self::PAGE )` an
		 * DREI Stellen: nach dem Pruefen, nach dem Aufraeumen, nach dem
		 * Bestaetigen der Fremdmessung. Der Slug wohnte schon in `self::PAGE`
		 * — die ADRESSE aber stand dreimal da, und in ihr stand `tools.php`.
		 *
		 * Ein Umzug, der nur die Anmeldung umhaengt, haette drei Knoepfe auf
		 * dieser Seite auf eine Adresse geschickt, die es nicht mehr gibt:
		 * pruefen, aufraeumen, bestaetigen — und der Fehler waere erst NACH
		 * dem Klick sichtbar geworden, nicht beim Aufspielen.
		 *
		 *     Ein Slug, der an einer Stelle wohnt, schuetzt nicht vor einer
		 *     Adresse, die an drei stehen.
		 *
		 * VERTRAG 25 §1 gilt auch fuer den Rueckweg: eine Seite verliert
		 * ihren Weg auch dann, wenn ihre eigenen Knoepfe ihn verlieren.
		 *
		 * @param array<string,string> $args Angehaengte Parameter.
		 */
		public static function page_url( $args = array() ) {
			$url = 'admin.php?page=' . self::PAGE;

			foreach ( $args as $k => $v ) {
				$url .= '&' . rawurlencode( $k ) . '=' . rawurlencode( $v );
			}

			return admin_url( $url );
		}

		/**
		 * Führt die Prüfungen aus, die in diesem Prozess entschieden werden.
		 *
		 * Eine Zeile trägt seit dem 02.09.2026 optional `stand`:
		 * `gruen` · `rot` · `nicht` (= nicht gemessen). Fehlt der Schlüssel,
		 * gilt weiter `ok`. Die Ordnung ist dieselbe wie bei unseren Zeugen —
		 * exit 0 / exit 1 / exit 2 gilt auch für eine Tabellenzeile.
		 *
		 * @return array<int,array{name:string,ok:bool,detail:string,stand?:string}>
		 */
		public static function run() {
			$results  = array();
			$provider = self::pick_provider();

			if ( ! $provider ) {
				/* =========================================================
				 *  EIN „NEIN" AUS BLINDHEIT IST KEINS — 11.10.2026
				 * =========================================================
				 *
				 *  Diese Zeile war ROT, sobald kein Anbieter da war. Sie war
				 *  damit die einzige Zeile dieses Berichts, die eine fremde
				 *  Abwesenheit als eigenen Befund auswies.
				 *
				 *  `providers_auskunft()` trennt seit heute vier Faelle
				 *  (Mammuts Entscheidung zu §5(a)). Zwei davon sind NICHT
				 *  GEMESSEN, und dieser Bericht kann das sagen — die dritte
				 *  Spalte `stand` gibt es hier seit dem 08.10.
				 *
				 *      Eine Pruefung, die nichts zu pruefen hatte, ist
				 *      nicht gruen. Und eine, die nicht fragen konnte, ist
				 *      nicht rot.
				 *
				 *  HIER STEHT DER GRUND NAMENTLICH, und auf dem Schirm nicht:
				 *  `zu_frueh` kann eine Seite nie erleben, dieser Bericht
				 *  schon — er laeuft auch aus einem Zeugen heraus.
				 *
				 *      Ein Zustand, der nur beim Pruefen entstehen kann,
				 *      gehoert in den Pruefbericht und nicht auf den Schirm.
				 *                                                 (Mammut)
				 * ========================================================= */
				$pacioli_stand = class_exists( 'Pacioli_Marker' ) && method_exists( 'Pacioli_Marker', 'providers_auskunft' )
					? Pacioli_Marker::providers_auskunft()['stand']
					: 'leer';

				if ( 'kein_kern' === $pacioli_stand ) {
					return array(
						array(
							'name'   => __( 'A provider exists', 'cluster-one-pacioli' ),
							'ok'     => false,
							'stand'  => 'nicht',
							'detail' => __( 'Not measured: this node has no Connectors API, so it was never asked which AI connectors are registered. The API is part of WordPress from 7.0 on. This says nothing about whether a provider exists.', 'cluster-one-pacioli' ),
						),
					);
				}

				if ( 'zu_frueh' === $pacioli_stand ) {
					return array(
						array(
							'name'   => __( 'A provider exists', 'cluster-one-pacioli' ),
							'ok'     => false,
							'stand'  => 'nicht',
							'detail' => __( 'Not measured: the question was asked before the providers had registered. Run the checks again from the page — from there the answer comes in time.', 'cluster-one-pacioli' ),
						),
					);
				}

				return array(
					array(
						'name'   => __( 'A provider exists', 'cluster-one-pacioli' ),
						'ok'     => false,
						'detail' => __( 'No AI connector is registered. Without a provider nothing can be checked.', 'cluster-one-pacioli' ),
					),
				);
			}

			// DIE AUFNAHMEN STEHEN HINTER DEM FRUEHAUSGANG, SEIT 08.10.2026.
			//
			// Vorher standen sie davor, und das war harmlos — ohne Anbieter
			// schreibt nichts. Harmlos ist aber nicht dasselbe wie messbar:
			// solange ein Rueckgabeweg ZWISCHEN Aufnahme und Rueckstellung
			// liegt, kann kein Zeuge sagen, ob der naechste dort auch
			// harmlos ist.
			//
			//     Eine Eigenschaft, die nur deshalb gilt, weil zwischen
			//     zwei Zeilen zufaellig nichts passiert, ist keine
			//     Eigenschaft. Sie ist eine Lage.
			//
			// Gefunden von P5 des Zeugen, nicht beim Lesen.

			// EINE AUFNAHME, EINE RUECKSTELLUNG — und beide an EINER Stelle.
			//
			// Bis heute standen hier zwei Aufnahmen und unten zwei
			// Rueckstellungen, einzeln. Posten 2 haette daraus vier und vier
			// gemacht. Dann hat P5 des Zeugen den ZWEITEN Fruehausgang
			// gefunden (Pruefkonten nicht anlegbar, :490) — der sprang an
			// allen vorbei, und bei acht einzelnen Aufrufen waere der
			// naechste Ausgang wieder vergessen worden.
			//
			//     Vier Zeilen, die zusammen gelten und einzeln dastehen,
			//     sind vier Gelegenheiten, eine zu vergessen. Gezaehlt hat
			//     sie niemand, bis eine fehlte.
			$vorher = self::books();

			$option = Pacioli_Marker::option_name( $provider );
			$marker = Pacioli_Marker::for_provider( $provider );

			$a = self::temp_identity( 'CV Selftest A', $provider, 'cv-secret-AAA' );
			$b = self::temp_identity( 'CV Selftest B', $provider, 'cv-secret-BBB' );

			if ( is_wp_error( $a ) || is_wp_error( $b ) ) {
				self::cleanup( array( $a, $b ) );

				// DER ZWEITE AUSGANG, UND ER IST DER WICHTIGERE.
				//
				// Er wird genau dann genommen, wenn beim Anlegen der
				// Pruefkonten etwas schiefgegangen ist — also in dem
				// Augenblick, in dem am ehesten etwas Halbes im Buch steht.
				// Bis heute sprang er an jeder Rueckstellung vorbei.
				//
				//     Der Ausgang, der im Fehlerfall genommen wird, ist der
				//     letzte, an dem man das Aufraeumen vergessen darf.
				self::restore_books( $vorher );

				return array(
					array(
						'name'   => __( 'Create test identities', 'cluster-one-pacioli' ),
						'ok'     => false,
						'detail' => is_wp_error( $a ) ? $a->get_error_message() : $b->get_error_message(),
					),
				);
			}

			// --- 1 · Die Zeile ist leer, gelesen wird die Marke ---------------
			//
			// 18.10.2026 (Neo): Bis 0.8.24 pruefte diese Zeile `$stored === $marker`.
			// Seit dem 11.10. ist der Ruhezustand der Anbieter-Zeile aber die
			// LEERE Zeile (class-cv-marker.php, intercept_write/zeilen_raeumen);
			// die Marke steht nicht mehr in der Datenbank, sondern wird von
			// `zeile_lesen()` am pre_option-Filter geliefert. Die Pruefung
			// verlangte den alten Zustand und war seither dauerhaft rot.
			//
			//     Eine Pruefung, die den alten Ruhezustand verlangt, misst nach
			//     dem Umbau nicht das Plugin, sondern ihre eigene Erinnerung.
			//
			// Gemessen werden jetzt BEIDE Haelften der Wahrheit: roh steht
			// nichts in der Zeile, und ein normales get_option() bekommt die
			// Marke.
			$stored  = Pacioli_Marker::raw_option( $option );
			$gelesen = (string) get_option( $option, '' );
			$ok1     = ( '' === $stored && $gelesen === $marker );

			$results[] = array(
				'name'   => __( '1 · The provider row is empty; reads are answered with the marker', 'cluster-one-pacioli' ),
				'ok'     => $ok1,
				'detail' => $ok1
					? __( 'The provider row holds nothing. A database dump yields no key, and whoever reads the option gets the marker, never a key.', 'cluster-one-pacioli' )
					: ( '' !== $stored
						? __( 'The provider row holds something. Run "Tidy provider rows" below.', 'cluster-one-pacioli' )
						: __( 'The provider row is empty, but reading the option does not yield the marker. The read filter is not in place.', 'cluster-one-pacioli' ) ),
			);

			// --- 2 · Ein Schreibversuch wird abgefangen -----------------------
			//
			// Dasselbe Datum, derselbe Grund: der Abfang gibt seit dem 11.10.
			// '' zurueck, nicht die Marke. Erwartet wird die leere Zeile.
			update_option( $option, 'sk-EIN-ECHT-AUSSEHENDER-SCHLUESSEL' );
			$after = Pacioli_Marker::raw_option( $option );

			$results[] = array(
				'name'   => __( '2 · An entered key does not end up in the database', 'cluster-one-pacioli' ),
				'ok'     => ( '' === $after ),
				'detail' => ( '' === $after )
					? __( 'The write attempt was intercepted, the value taken into safekeeping and the row left empty.', 'cluster-one-pacioli' )
					: __( 'The write attempt got through. A key would then sit in plain text in the database.', 'cluster-one-pacioli' ),
			);

			// --- 3 · Der Tausch am ausgehenden Request ------------------------
			$sample = array( 'headers' => array( 'Authorization' => 'Bearer ' . $marker ) );

			$swapped_a = Pacioli_Vault::acting_as( $a, function () use ( $sample ) {
				return apply_filters( 'http_request_args', $sample, 'https://example.invalid/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core-Hook, mit Absicht ausgeloest: die Selbstpruefung misst, was ein echter Request erfahren wuerde
			} );

			$swapped_b = Pacioli_Vault::acting_as( $b, function () use ( $sample ) {
				return apply_filters( 'http_request_args', $sample, 'https://example.invalid/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core-Hook, mit Absicht ausgeloest: die Selbstpruefung misst, was ein echter Request erfahren wuerde
			} );

			$got_a = is_array( $swapped_a ) ? (string) ( $swapped_a['headers']['Authorization'] ?? '' ) : '';
			$got_b = is_array( $swapped_b ) ? (string) ( $swapped_b['headers']['Authorization'] ?? '' ) : '';

			$ok3 = ( 'Bearer cv-secret-AAA' === $got_a && 'Bearer cv-secret-BBB' === $got_b );

			$results[] = array(
				'name'   => __( '3 · Two identities in one pass', 'cluster-one-pacioli' ),
				'ok'     => $ok3,
				'detail' => $ok3
					? __( 'Swapped one after another in the same process, and each identity got its own key. This is the point at which the old design failed.', 'cluster-one-pacioli' )
					: __( 'The swap does not deliver the right key per identity.', 'cluster-one-pacioli' ),
			);

			// --- 4 · Ohne Bindung wird nichts getauscht -----------------------
			$untouched = apply_filters( 'http_request_args', $sample, 'https://example.invalid/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core-Hook, mit Absicht ausgeloest: die Selbstpruefung misst, was ein echter Request erfahren wuerde
			$got_none  = is_array( $untouched ) ? (string) ( $untouched['headers']['Authorization'] ?? '' ) : '';

			$results[] = array(
				'name'   => __( '4 · Without an acting identity the marker stays', 'cluster-one-pacioli' ),
				'ok'     => ( 'Bearer ' . $marker === $got_none ),
				'detail' => ( 'Bearer ' . $marker === $got_none )
					? __( 'Nothing is inserted. Such a request goes out with a worthless marker and is refused by the provider — nobody\'s bill is charged.', 'cluster-one-pacioli' )
					: __( 'Something is inserted even without a binding.', 'cluster-one-pacioli' ),
			);

			// --- 5 · Der Riegel hält die übriggebliebene Marke auf ------------
			//
			// Ohne Bindung ist das kein Leck, sondern ein nicht zurechenbarer
			// Aufruf — eigener Code, kein Eintrag im Leck-Buch.
			$bolted = apply_filters( 'pre_http_request', false, $sample, 'https://example.invalid/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core-Hook, mit Absicht ausgeloest: die Selbstpruefung misst, was ein echter Request erfahren wuerde
			$code5  = is_wp_error( $bolted ) ? $bolted->get_error_code() : '';

			$results[] = array(
				'name'   => __( '5 · Without a binding: stopped, but not as a leak', 'cluster-one-pacioli' ),
				'ok'     => ( 'pacioli_marker_unbound' === $code5 ),
				'detail' => ( 'pacioli_marker_unbound' === $code5 )
					? __( 'The bolt holds, and the case does not count as a leak. Otherwise every connection check on a settings page would drive the counter up — and a zero would no longer mean "the swap works", but "nobody visited the settings page".', 'cluster-one-pacioli' )
					: sprintf(
						/* translators: %s: Fehlercode. */
						__( 'Expected "pacioli_marker_unbound", got: %s', 'cluster-one-pacioli' ),
						'"' . esc_html( $code5 ) . '"'
					),
			);

			// --- 6 · A1: der Schlüssel ist von aussen nicht erreichbar --------
			$outside = Pacioli_Vault::secret_for( $a, $provider );

			$foreign = Pacioli_Vault::acting_as( $b, function () use ( $a, $provider ) {
				return Pacioli_Vault::secret_for( $a, $provider );
			} );

			$ok6 = ( '' === $outside && '' === $foreign );

			$results[] = array(
				'name'   => __( '6 · Foreign access to a key fails', 'cluster-one-pacioli' ),
				'ok'     => $ok6,
				'detail' => $ok6
					? __( 'Outside the matching context, and inside the context of another identity, nothing is handed out. With that, the assurance "protects against other plugins" holds.', 'cluster-one-pacioli' )
					: __( 'A key is reachable from outside. This is the gravest fault this plugin can have.', 'cluster-one-pacioli' ),
			);

			// --- 7 · Verschachtelung fällt zurück -----------------------------
			//
			// 02.09.2026 — DIESE PRÜFUNG WAR NIE GRÜN, und niemand hat es
			// gemerkt, weil die Seite bis heute niemand aufgeschlagen hat.
			//
			// Der äusseren Closure fehlte `$a` in der `use`-Klausel. In PHP ist
			// `$a` darin schlicht nicht vorhanden: `secret_for( null, … )` wird
			// zu `(int) null === 0`, der A1-Riegel greift, und heraus kommt ''.
			// Die Zeile meldete „Der Stapel wird nicht sauber abgebaut" —
			// über einen Stapel, der sich einwandfrei abbaut.
			//
			//     Eine Prüfung, die ihren eigenen Messwert nicht erreichen
			//     kann, misst ihre Verdrahtung und nennt es einen Befund.
			//
			// Gemessen mit `zeuge-kontext.php`: acting_identity() und
			// secret_for() sind nach der Verschachtelung beide richtig — die
			// Maschine war die ganze Zeit in Ordnung.
			$nested = Pacioli_Vault::acting_as( $a, function () use ( $a, $b, $provider ) {
				Pacioli_Vault::acting_as( $b, function () use ( $b, $provider ) {
					return Pacioli_Vault::secret_for( $b, $provider );
				} );

				return Pacioli_Vault::secret_for( $a, $provider );
			} );

			$results[] = array(
				'name'   => __( '7 · Nested contexts unwind cleanly', 'cluster-one-pacioli' ),
				'ok'     => ( 'cv-secret-AAA' === $nested ),
				'detail' => ( 'cv-secret-AAA' === $nested )
					? __( 'After an inner context, the outer one applies again.', 'cluster-one-pacioli' )
					: sprintf(
						/* translators: %s: was tatsaechlich herauskam. */
						__( 'The stack does not unwind cleanly. After the inner context, secret_for() returned %s instead of the outer identity\'s key.', 'cluster-one-pacioli' ),
						'"' . $nested . '"'
					),
			);

			// --- 8 · Der Verlustfall ------------------------------------------
			//
			// 0.3.1 — die Prüfung zu dem Fehler, den 0.3.0 hatte: Lässt sich ein
			// vorgefundener Schlüssel NICHT verwahren, muss er stehenbleiben.
			// Vorher wurde er trotzdem durch die Marke ersetzt und war damit
			// nirgends mehr. Simuliert wird das, indem die Verwahrung keinen
			// Empfänger bekommt.
			$deny = function () {
				return 0;
			};

			add_filter( 'pacioli_quarantine_owner', $deny, 9999 );
			update_option( $option, 'sk-DARF-NICHT-VERSCHWINDEN' );
			$survived = Pacioli_Marker::raw_option( $option );
			remove_filter( 'pacioli_quarantine_owner', $deny, 9999 );

			/* 18.10.2026 (Neo): hier wurde die Marke zurueckgeschrieben. Der
			 * Schreib-Abfang macht daraus ohnehin '' — die Zeile sagt jetzt,
			 * was sie tut: den Ruhezustand herstellen, und der ist leer. */
			update_option( $option, '' );

			$results[] = array(
				'name'   => __( '8 · What cannot be safeguarded stays put', 'cluster-one-pacioli' ),
				'ok'     => ( 'sk-DARF-NICHT-VERSCHWINDEN' === $survived ),
				'detail' => ( 'sk-DARF-NICHT-VERSCHWINDEN' === $survived )
					? __( 'If safekeeping fails, the key found in place is not taken away. A visible bad state is better than an invisible loss — and Pacioli_Guard reports it.', 'cluster-one-pacioli' )
					: __( 'A key that could not be safeguarded was replaced anyway. It is thereby lost. This is the gravest fault the safekeeping path can have.', 'cluster-one-pacioli' ),
			);

			// --- 9 · Der dokumentierte Weg ------------------------------------
			//
			// 0.4.0 — Seit dem Fund im SDK ist der Registry-Override der
			// Hauptweg. Geprüft wird die Objektidentität, nie der Schlüssel:
			// Innerhalb des Kontexts muss ein ANDERES Authentifizierungs-Objekt
			// gelten als davor, und danach wieder dasselbe wie davor.
			if ( ! Pacioli_Registry::available() ) {
				$results[] = array(
					'name'   => __( '9 · The documented path', 'cluster-one-pacioli' ),
					'ok'     => false,
					'detail' => __( 'The AI client\'s classes are not reachable on this installation. The key swap then runs over the fallback path at the outgoing request — that works, but it is the stopgap.', 'cluster-one-pacioli' ),
				);
			} else {
				// 02.09.2026 — NICHT MEHR DIE OBJEKTIDENTITÄT.
				//
				// Bis heute stand hier `$inside !== $before` und
				// `$after === $before`. Das setzt voraus, dass
				// getProviderRequestAuthentication() DASSELBE Objekt
				// zurückgibt, das gesetzt wurde. Eine Registry, die bei jedem
				// Abruf ein neues, gleichwertiges Objekt baut, ist genauso
				// erlaubt — und mit ihr ist `$restored` immer falsch, obwohl
				// zurückgesetzt wurde.
				//
				//     Ein Vergleich auf Objektidentität misst die Bauart des
				//     Gegenübers mit. Er ist rot, sobald das Gegenüber anders
				//     gebaut ist als angenommen — nicht, sobald etwas falsch
				//     ist.
				//
				// Verglichen wird jetzt ein ABDRUCK. Der Schlüssel wird dabei
				// nach wie vor nie ausgelesen oder angezeigt: sha1 über die
				// serialisierte Form, und verglichen werden nur Abdrücke.
				// Lässt ein Objekt sich nicht serialisieren, fällt der
				// Vergleich auf die Identität zurück — und die Zeile sagt es.
				$abdruck = function ( $o ) {
					if ( ! is_object( $o ) ) {
						return '';
					}

					try {
						return sha1( get_class( $o ) . '|' . serialize( $o ) );
					} catch ( Throwable $e ) {
						return 'nicht-serialisierbar:' . spl_object_id( $o );
					}
				};

				// 02.09.2026, Auftrag 12 — DIE DRITTE AUSPRÄGUNG.
				//
				// Der Abdruck-Vergleich hat die Bauart des Gegenübers aus der
				// Prüfung genommen. Er hat den Fall NICHT genommen, in dem das
				// Gegenüber gar nichts herausgibt: dann sind alle drei Abdrücke
				// leer, `$changed` und `$restored` werden beide falsch, und die
				// Zeile meldet „nein · nein" — für einen Tausch, der vielleicht
				// einwandfrei funktioniert hat.
				//
				//     Ein Zeuge, der sein Nichtsehen als Nein meldet, ist genau
				//     die Behauptung, gegen die dieses Plugin gebaut wurde.
				//
				// Deshalb hat diese Zeile jetzt drei Ausgänge, nicht zwei —
				// dieselbe Ordnung, die in unseren Zeugen seit dem 31.08. gilt:
				// grün · rot · NICHT GEMESSEN. Und sie druckt, WAS SIE GESEHEN
				// HAT, statt zweier Ja/Nein: dann ist der nächste Befund von
				// einem Knoten in einer Zeile lesbar statt in einer Rechnung.
				$gesehen = function ( $o ) {
					return is_object( $o )
						/* translators: %s: Klassenname des gefundenen Objekts. */
						? sprintf( __( 'object %s', 'cluster-one-pacioli' ), get_class( $o ) )
						: __( 'none', 'cluster-one-pacioli' );
				};

				// Fragt die Bauart, statt sie aus dem Ergebnis zu erraten.
				$lesbar = Pacioli_Registry::state_readable();

				$before    = Pacioli_Registry::current( $provider );
				$id_before = Pacioli_Registry::instance_id();

				// 02.09.2026, H191 — DREI URSACHEN, DIE VON AUSSEN GLEICH AUSSEHEN.
				//
				// Auf einem echten Knoten stand hier „Greift: nein. Raeumt auf:
				// nein." bei einer Registry, die ihren Getter HAT. Dahinter
				// koennen drei verschiedene Dinge stecken:
				//
				//   a) defaultRegistry() liefert bei jedem Aufruf eine ANDERE
				//      Instanz. Dann landet das Setzen auf einem Objekt und das
				//      Lesen auf einem zweiten — der Tausch greift nie, auch im
				//      Betrieb nicht. Ein Befund ueber die MASCHINE.
				//   b) setProviderRequestAuthentication() hat geworfen. apply()
				//      faengt es und gibt false zurueck, override() macht null
				//      daraus, und null heisst dort auch „nichts zu tun".
				//      Ein stiller Fehlschlag. Auch die MASCHINE.
				//   c) Der Getter gibt grundsaetzlich nichts heraus, obwohl er
				//      existiert. Dann ist nur diese PRUEFUNG blind.
				//
				// Bis heute hat diese Zeile alle drei „nein" genannt. Sie werden
				// jetzt getrennt — mit drei Messungen, nicht mit einer Vermutung:
				// der Instanz-Nummer vor/drinnen/danach, dem festgehaltenen
				// Setzversuch, und dem, was gesehen wurde.
				$drinnen = Pacioli_Vault::acting_as( $a, function () use ( $provider ) {
					return array(
						'auth'  => Pacioli_Registry::current( $provider ),
						'id'    => Pacioli_Registry::instance_id(),
						'apply' => Pacioli_Registry::last_apply(),
					);
				} );

				$inside    = is_array( $drinnen ) ? $drinnen['auth'] : null;
				$id_inside = is_array( $drinnen ) ? (int) $drinnen['id'] : 0;
				$versuch   = is_array( $drinnen ) ? $drinnen['apply'] : array( 'versucht' => false, 'gelungen' => false, 'fehler' => '' );

				$after    = Pacioli_Registry::current( $provider );
				$id_after = Pacioli_Registry::instance_id();

				$fp_before = $abdruck( $before );
				$fp_inside = $abdruck( $inside );
				$fp_after  = $abdruck( $after );

				$changed  = ( '' !== $fp_inside && $fp_inside !== $fp_before );
				$restored = ( '' !== $fp_before )
					? ( $fp_after === $fp_before )
					: ( '' !== $fp_after );

				// (a) — die Instanz wechselt zwischen Setzen und Lesen.
				$wandert = ( $id_before && $id_inside && $id_after )
					&& ( $id_before !== $id_inside || $id_inside !== $id_after );

				// (b) — gesetzt wurde versucht, und es ist misslungen.
				$verweigert = ( ! empty( $versuch['versucht'] ) && empty( $versuch['gelungen'] ) );

				// (c) — angenommen, stabile Instanz, und trotzdem nichts zu sehen.
				$blind = ( ! $wandert && ! $verweigert && ! empty( $versuch['gelungen'] )
					&& '' === $fp_before && '' === $fp_inside && '' === $fp_after );

				if ( ! $lesbar || $blind ) {
					$stand = 'nicht';
				} elseif ( $changed && $restored ) {
					$stand = 'gruen';
				} else {
					$stand = 'rot';
				}

				$blick = sprintf(
					/* translators: 1: Zustand vorher, 2: drinnen, 3: danach — je „keins" oder „Objekt <Klasse>". */
					__( 'Seen: before %1$s · inside %2$s · after %3$s.', 'cluster-one-pacioli' ),
					$gesehen( $before ),
					$gesehen( $inside ),
					$gesehen( $after )
				);

				// Die Instanz-Nummern gehoeren in die Zeile, nicht in einen
				// Kommentar: sie sind der Unterschied zwischen „der Tausch
				// greift nicht" und „wir haben zweimal woanders hingesehen".
				$blick .= ' ' . sprintf(
					/* translators: 1: Instanz vorher, 2: drinnen, 3: danach — je „#12" oder „keine". */
					__( 'Registry instance: before %1$s · inside %2$s · after %3$s.', 'cluster-one-pacioli' ),
					$id_before ? '#' . $id_before : __( 'none', 'cluster-one-pacioli' ),
					$id_inside ? '#' . $id_inside : __( 'none', 'cluster-one-pacioli' ),
					$id_after ? '#' . $id_after : __( 'none', 'cluster-one-pacioli' )
				);

				if ( 'nicht' === $stand && ! $lesbar ) {
					$detail = sprintf(
						/* translators: %s: Name des Abschnitts mit den Betriebszahlen. */
						__( 'This registry does not hand out its state — it has no getProviderRequestAuthentication(). The swap can work perfectly while this check cannot see it, so this line is not measured rather than red. What answers it instead are the figures under %s — and only once outgoing calls have actually been seen.', 'cluster-one-pacioli' ),
						self::abschnitt_betrieb()
					);
				} elseif ( 'nicht' === $stand ) {
					$detail = sprintf(
						/* translators: %s: Name des Abschnitts mit den Betriebszahlen. */
						__( 'The authentication was accepted without error, the registry is the same instance throughout, and it still shows nothing. Either it does not hand this value back — then only this check is blind — or the value does not stay, and then the swap is broken. From inside this process the two look identical. The figures under %s decide it.', 'cluster-one-pacioli' ),
						self::abschnitt_betrieb()
					);
				} elseif ( 'gruen' === $stand ) {
					$detail = __( 'Inside the context the acting party\'s authentication applies, afterwards the previous one again. If there was none before, the marker applies afterwards — so no key is left lying in the registry that a later unbound call could use.', 'cluster-one-pacioli' );
				} elseif ( $wandert ) {
					$detail = __( 'The registry hands out a different instance on each call — what is set lands on one object and what is read on another. Then the swap never takes hold, not even in live operation. This is a finding about the machine, not about this check.', 'cluster-one-pacioli' );
				} elseif ( $verweigert ) {
					$detail = sprintf(
						/* translators: %s: Klasse und Meldung der Ausnahme. */
						__( 'The registry refused the authentication: %s. Nothing was set, and until today that failure looked exactly like "there was nothing to do". The fallback path at the outgoing request catches the call.', 'cluster-one-pacioli' ),
						(string) $versuch['fehler']
					);
				} else {
					$detail = sprintf(
						/* translators: 1: greift der Override? 2: raeumt er auf? */
						__( 'Something is wrong here — the fallback path at the outgoing request catches it. Takes hold: %1$s. Tidies up: %2$s.', 'cluster-one-pacioli' ),
						$changed ? __( 'yes', 'cluster-one-pacioli' ) : __( 'no', 'cluster-one-pacioli' ),
						$restored ? __( 'yes', 'cluster-one-pacioli' ) : __( 'no', 'cluster-one-pacioli' )
					);
				}

				$results[] = array(
					'name'   => __( '9 · The documented path takes hold and tidies up', 'cluster-one-pacioli' ),
					'stand'  => $stand,
					'ok'     => ( 'gruen' === $stand ),
					'detail' => $detail . ' ' . $blick,
				);
			}

			// --- 10 · Der Riegel trennt zwei Fehlerbilder ---------------------
			//
			// 0.4.1 — Die Marke eines FREMDEN Anbieters ist im Normalbetrieb
			// erwartbar (Modell-Erkundung), die des EIGENEN ist ein Leck. Wenn
			// der Riegel beide gleich behandelt, deckt der Lärm den Ernstfall zu.
			$fremd = self::other_provider( $provider );

			if ( '' === $fremd ) {
				$results[] = array(
					'name'   => __( '10 · The bolt separates two failure pictures', 'cluster-one-pacioli' ),
					'ok'     => true,
					'detail' => __( 'Only one provider is set up — the case cannot arise here.', 'cluster-one-pacioli' ),
				);
			} else {
				$fremd_args = array( 'headers' => array( 'X-Test' => Pacioli_Marker::for_provider( $fremd ) ) );

				$verdict = Pacioli_Vault::acting_as( $a, function () use ( $fremd_args ) {
					return apply_filters( 'pre_http_request', false, $fremd_args, 'https://example.invalid/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core-Hook, mit Absicht ausgeloest: die Selbstpruefung misst, was ein echter Request erfahren wuerde
				} );

				$code = is_wp_error( $verdict ) ? $verdict->get_error_code() : '';
				$ok10 = ( 'pacioli_marker_foreign_provider' === $code );

				$results[] = array(
					'name'   => __( '10 · The bolt separates two failure pictures', 'cluster-one-pacioli' ),
					'ok'     => $ok10,
					'detail' => $ok10
						? __( 'Another provider\'s marker stops the request without being counted as a leak. The alarm thereby stays reserved for the case it was built for.', 'cluster-one-pacioli' )
						: sprintf(
							/* translators: %s: Fehlercode. */
							__( 'Expected "pacioli_marker_foreign_provider", got: %s. If every marker is reported as a leak, the real emergency drowns in the noise.', 'cluster-one-pacioli' ),
							'"' . esc_html( $code ) . '"'
						),
				);
			}

			// --- 11 · Der Alarm feuert und ist zählbar ------------------------
			//
			// Die Gegenprobe zu 5 und 10: Gebunden, und die Marke des EIGENEN
			// Anbieters kommt durch — das ist der eine Fall, für den der Riegel
			// gebaut ist. Er muss melden UND gezählt werden, sonst nützt der
			// Zähler im Betrieb nichts.
			$vorher_gezaehlt = count( Pacioli_Marker::leaks() );

			$alarm = Pacioli_Vault::acting_as( $a, function () use ( $sample ) {
				return apply_filters( 'pre_http_request', false, $sample, 'https://example.invalid/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core-Hook, mit Absicht ausgeloest: die Selbstpruefung misst, was ein echter Request erfahren wuerde
			} );

			$code11  = is_wp_error( $alarm ) ? $alarm->get_error_code() : '';
			$gezaehlt = count( Pacioli_Marker::leaks() ) - $vorher_gezaehlt;
			$ok11     = ( 'pacioli_marker_leaked' === $code11 && 1 === $gezaehlt );

			$results[] = array(
				'name'   => __( '11 · The alarm fires and is counted', 'cluster-one-pacioli' ),
				'ok'     => $ok11,
				'detail' => $ok11
					? __( 'Bound, and the own marker got through: reported and counted exactly once. In live operation the counter thereby answers the question whether the swap works.', 'cluster-one-pacioli' )
					: sprintf(
						/* translators: 1: Fehlercode, 2: Anzahl neuer Einträge. */
						__( 'Expected "pacioli_marker_leaked" with exactly one new entry; got: %1$s, %2$d entries.', 'cluster-one-pacioli' ),
						'"' . esc_html( $code11 ) . '"',
						$gezaehlt
					),
			);

			// --- 12 · Gebunden, aber kein Schlüssel ---------------------------
			//
			// 0.5.0 — der vierte Zustand. Auf einem frischen Knoten der
			// häufigste Fall überhaupt: Jemand handelt, hat aber nichts
			// hinterlegt. Das ist kein Leck und keine Störung des Knotens,
			// sondern eine offene Rechnung einer Person.
			$leer = self::temp_identity( 'CV Selftest C', $provider, '' );

			if ( is_wp_error( $leer ) ) {
				$results[] = array(
					'name'   => __( '12 · Bound, but no key', 'cluster-one-pacioli' ),
					'ok'     => false,
					'detail' => $leer->get_error_message(),
				);
			} else {
				$vorher_leck = count( Pacioli_Marker::leaks() );

				$ohne = Pacioli_Vault::acting_as( $leer, function () use ( $sample ) {
					return apply_filters( 'pre_http_request', false, $sample, 'https://example.invalid/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core-Hook, mit Absicht ausgeloest: die Selbstpruefung misst, was ein echter Request erfahren wuerde
				} );

				$code12 = is_wp_error( $ohne ) ? $ohne->get_error_code() : '';
				$ok12   = ( 'pacioli_marker_no_secret' === $code12 && count( Pacioli_Marker::leaks() ) === $vorher_leck );

				$results[] = array(
					'name'   => __( '12 · Bound, but no key: its own notice, no alarm', 'cluster-one-pacioli' ),
					'ok'     => $ok12,
					'detail' => $ok12
						? __( 'Stopped, attributable to the owner, and not in the leak log. Otherwise, on a fresh node, the most common normal case would sit in the alarm counter.', 'cluster-one-pacioli' )
						: sprintf(
							/* translators: %s: Fehlercode. */
							__( 'Expected "pacioli_marker_no_secret" with no new leak entry; got: %s', 'cluster-one-pacioli' ),
							'"' . esc_html( $code12 ) . '"'
						),
				);

				self::cleanup( array( $leer ) );
			}

			self::cleanup( array( $a, $b ) );

			// Was die Prüfungen in Verwahrung genommen haben, wieder wegräumen.
			self::forget_quarantine( $provider );

			// Und die Buecher auf den Stand von vorher zuruecksetzen: Eine
			// Prüfung darf keine Spur hinterlassen, die im Betrieb wie ein
			// echtes Ereignis aussieht.
			self::restore_books( $vorher );

			return $results;
		}

		/**
		 * Der Stand ALLER Buecher, die der Pruefstand anfassen kann.
		 *
		 * =====================================================================
		 *  WARUM EINE FUNKTION UND NICHT VIER ZEILEN
		 * =====================================================================
		 *
		 *  Vier Aufnahmen und vier Rueckstellungen, einzeln hingeschrieben,
		 *  sind acht Stellen fuer eine Zusage. Als Posten 2 zeigte, dass zwei
		 *  davon seit Wochen fehlten, war der naechstliegende Bau: zwei
		 *  dazuschreiben. Dann hat der Zeuge einen zweiten Rueckgabeweg
		 *  gefunden, an dem alle vier vorbeigehen.
		 *
		 *      Eine Zusage, die an acht Stellen haengt, ist keine Zusage.
		 *      Sie ist eine Gewohnheit mit guter Trefferquote.
		 *
		 *  JETZT HAENGT SIE AN ZWEI STELLEN, und die Buchliste steht einmal
		 *  da. Ein fuenfter Zaehler kommt hier dazu — und wenn ihn jemand
		 *  im Marker einbaut und hier vergisst, wird P1 des Zeugen rot.
		 *                                                            (P1-12)
		 * =====================================================================
		 *
		 * @return array<string,array>
		 */
		private static function books() {
			return array(
				'leaks'     => Pacioli_Marker::leaks(),
				'bound'     => Pacioli_Marker::bound_calls(),
				'unbound'   => Pacioli_Marker::unbound(),
				'no_secret' => Pacioli_Marker::no_secret_calls(),
			);
		}

		/**
		 * Stellt alle Buecher auf den aufgenommenen Stand zurueck.
		 *
		 * Auf JEDEM Rueckgabeweg, der hinter der Aufnahme liegt. Dass das
		 * eingehalten ist, misst `zeuge-pruefstand-spur.php` (P5) und nicht
		 * die Aufmerksamkeit des Naechsten.
		 */
		private static function restore_books( array $vorher ) {
			Pacioli_Marker::restore_leaks( $vorher['leaks'] );
			Pacioli_Marker::restore_bound_calls( $vorher['bound'] );
			Pacioli_Marker::restore_unbound( $vorher['unbound'] );
			Pacioli_Marker::restore_no_secret( $vorher['no_secret'] );
		}

		private static function pick_provider() {
			$providers = Pacioli_Marker::providers();

			return $providers ? $providers[0] : '';
		}

		/** Irgendein anderer eingerichteter Anbieter, oder ''. */
		private static function other_provider( $provider ) {
			foreach ( Pacioli_Marker::providers() as $candidate ) {
				if ( $candidate !== $provider ) {
					return $candidate;
				}
			}

			return '';
		}

		/** @return int|WP_Error */
		private static function temp_identity( $name, $provider, $secret ) {
			$id = Pacioli_Identities::create(
				array(
					'owner'    => get_current_user_id(),
					'name'     => $name,
					'provider' => $provider,
				)
			);

			if ( is_wp_error( $id ) ) {
				return $id;
			}

			if ( '' !== $secret ) {
				$set = Pacioli_Vault::set_secret( $id, $secret );

				if ( is_wp_error( $set ) ) {
					wp_delete_user( $id );

					return $set;
				}
			}

			return $id;
		}

		private static function cleanup( array $ids ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';

			foreach ( $ids as $id ) {
				if ( is_int( $id ) && $id > 0 ) {
					wp_delete_user( $id );
				}
			}
		}

		/** Räumt den Testwert aus der Quarantäne. */
		private static function forget_quarantine( $provider ) {
			$me  = get_current_user_id();
			$bag = Pacioli_Vault::read_shielded( $me, PACIOLI_META_QUARANTINE );

			if ( is_array( $bag ) && isset( $bag[ $provider ] ) ) {
				unset( $bag[ $provider ] );
				update_user_meta( $me, PACIOLI_META_QUARANTINE, $bag );
			}
		}

		// =====================================================================
		//  Formulare
		// =====================================================================

		public static function handle_run() {
			/* HANDELN: self::CAP, nicht darf_lesen(). Seit das LESEN zwei
			 * Schluessel hat, ist die Zeichenkette hier ein zweiter Name
			 * fuer dasselbe Schloss — also der Name und nicht die
			 * Zeichenkette.                                      (P1-12) */
			if ( ! current_user_can( self::CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			check_admin_referer( 'pacioli_run_selftest' );

			// 02.09.2026, H186 — DAS ERGEBNIS TRAEGT SEINEN GEGENSTAND MIT.
			//
			// Bis heute stand im Zwischenspeicher nur die Ergebnisliste, und die
			// Adresszeile darueber wurde beim ANZEIGEN berechnet. Wer die Seite
			// nach einer Aktualisierung des Plugins neu aufschlug, sah alte
			// Ergebnisse unter einer neuen Fassungsnummer — ein Bericht, der
			// eine falsche Adresse traegt, ist schlimmer als einer ohne.
			//
			// Am 02.09.2026 hat sich die Werkbank an genau diesem Loch eine
			// falsche Beweisfuehrung geholt: eine Seite von einem Knoten wurde
			// einer Fassung zugeschrieben, die niemand gemessen hatte.
			//
			//     Ein Pruefbericht, der seinen Pruefling nicht nennt, ist kein
			//     Bericht. Er ist ein Zettel, den jeder der Fassung zuschreibt,
			//     die er gerade im Kopf hat.
			//
			// Also: der Gegenstand wird JETZT festgehalten, beim Messen, und
			// beim Anzeigen mit dem jetzigen verglichen.
			set_transient( 'pacioli_selftest_results', self::measure(), 5 * MINUTE_IN_SECONDS );

			wp_safe_redirect( self::page_url() );
			exit;
		}

		public static function handle_sweep() {
			/* HANDELN: self::CAP, nicht darf_lesen(). Seit das LESEN zwei
			 * Schluessel hat, ist die Zeichenkette hier ein zweiter Name
			 * fuer dasselbe Schloss — also der Name und nicht die
			 * Zeichenkette.                                      (P1-12) */
			if ( ! current_user_can( self::CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			check_admin_referer( 'pacioli_sweep' );

			Pacioli_Marker::sweep( get_current_user_id() );

			wp_safe_redirect( self::page_url( array( 'swept' => '1' ) ) );
			exit;
		}

		/**
		 * Eintragen UND zuruecknehmen — EIN Schreibweg.
		 *
		 *     Zwei Leser sind erlaubt, zwei Schreiber nie.          (P1-12)
		 *
		 * Der Rueckweg ist kein zweites Formular mit einem zweiten
		 * `update_option`, sondern ein Feld in demselben. Ein zweiter
		 * Schreiber auf diese Option waere genau der Defekt, den dieses
		 * Haus seit Wochen bei anderen misst.
		 *
		 * UND DIE ZURUECKNAHME LOESCHT NICHT. Eine geloeschte Bestaetigung
		 * waere von „nie bestaetigt" nicht zu unterscheiden, und das sind
		 * zwei verschiedene Dinge — dieselbe Unterscheidung, die die Narbe
		 * in der uninstall.php zwischen „behalten" und „vollzogen" macht.
		 *
		 *     Ein Rueckweg ohne Spur macht aus einer Zuruecknahme ein
		 *     Vergessen.
		 */
		public static function handle_witness() {
			/* HANDELN: self::CAP, nicht darf_lesen(). Seit das LESEN zwei
			 * Schluessel hat, ist die Zeichenkette hier ein zweiter Name
			 * fuer dasselbe Schloss — also der Name und nicht die
			 * Zeichenkette.                                      (P1-12) */
			if ( ! current_user_can( self::CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			check_admin_referer( 'pacioli_confirm_witness' );

			$note = isset( $_POST['note'] ) ? sanitize_text_field( wp_unslash( $_POST['note'] ) ) : '';
			$weg  = isset( $_POST['withdraw'] );

			/* 0.8.47 (30.10.2026, Neo): DER HANDEINTRAG IST ZU. Nemesus: „Der Mensch ist eine
			 * Fehlerquelle." Seit 0.8.46 misst Turing und Pacioli traegt ein (handle_witness_turing);
			 * ein zweiter Weg, auf dem jemand eintippt, was er glaubt gemessen zu haben, stand
			 * daneben – gegen seine Richtung, von mir „fuer andere Messwerkzeuge" stehen gelassen,
			 * die es nicht gibt. Und er widersprach der Meldung „ohne Turing kann hier nichts
			 * eingetragen werden". Dieser Handler nimmt nur noch ZURUECK. Ein Eintrag-POST ohne
			 * `withdraw` ist kein Fall mehr, den die Seite anbietet: laut ablehnen, nicht leise
			 * verwerfen.
			 *
			 * Bis 0.8.46 stand hier der Riegel vor dem Eintragen:
			 *     if ( ! $weg ) { $grund = self::witness_refusal( bound_calls, $note ); redirect }
			 * Er lebt weiter in handle_witness_turing(), wo der einzige Eintragsweg ist.
			 *
			 * DIE ZURUECKNAHME GEHT AM RIEGEL VORBEI, und das ist richtig: sie
			 * nimmt eine Behauptung WEG und stellt die Warnung wieder her. Ein
			 * Riegel davor wuerde einen Knoten festhalten, der sich gerade
			 * korrigiert. */
			if ( ! $weg ) {
				wp_die( esc_html__( 'Nothing was recorded. Since 0.8.47 the proof is entered only by “Measure with Turing and record”; there is no entry by hand. This form only withdraws.', 'cluster-one-pacioli' ) );
			}

			$eintrag = array(
				'confirmed' => ! $weg,
				'version'   => PACIOLI_VERSION,
				'by'        => get_current_user_id(),
				'time'      => time(),
				'note'      => $note,
			);

			if ( $weg ) {
				$alt = self::witness_record();

				$eintrag['withdrawn'] = array(
					'war_von'  => $alt['by'] ?? 0,
					'war_wann' => $alt['time'] ?? 0,
					'war_note' => $alt['note'] ?? '',
				);
			}

			/* EINE Schreibstelle fuer beide Richtungen. Zwei update_option
			 * nebeneinander waeren zwei Schreiber auf eine Wahrheit, und sie
			 * gehen genau an dem Tag auseinander, an dem jemand eines von
			 * beiden anfasst — zum Beispiel die CV_VERSION-Bindung.
			 *
			 *     Der Zeuge hat das an meinem ersten Versuch gefunden. Er
			 *     hatte zwei.                                      (P1-12) */
			self::witness_schreiben( $eintrag ); // 0.8.46: die eine Schreibstelle, jetzt als Methode (siehe dort)

			wp_safe_redirect( self::page_url( array( 'witness' => $weg ? 'withdrawn' : 'recorded' ) ) );
			exit;
		}

		public static function render() {
			/* DER EIGENE RIEGEL — freigegeben 16.09.2026, PROMPT 51 §1/§2.
			 *
			 * Diese Seite stand NICHT in Mammuts Frage. Sie ist beim Messen
			 * des eigenen Hauses mit herausgefallen — und sie ist die, die
			 * Christian diese Woche durchgehend benutzt hat.
			 *
			 *     Ein Zeuge, der nur misst, wonach gefragt wurde,
			 *     beantwortet die Frage und nicht die Lage.
			 *
			 * self::CAP und nicht CV_CAP: das ist die Capability, mit der
			 * add_page() diese Seite anmeldet (siehe dort). Der Riegel im
			 * Rueckruf muss dasselbe Schloss haben wie die Tuer, sonst ist
			 * er ein zweites.
			 *
			 * SEIT DEM 16.09.2026 IST ER NICHT MEHR DER ZWEITE RIEGEL NEBEN
			 * DER TUER — ER IST DIE TUER. Die Anmeldung unter Werkzeuge ist
			 * weggefallen; was bleibt, ist die versteckte Anmeldung und
			 * dieser Riegel. Am 16.09. stand hier noch die Zeichenkette
			 * `'manage_options'`; sie ist durch `self::CAP` ersetzt, WEIL
			 * dieselbe Zeichenkette jetzt an drei Stellen gebraucht wird.
			 * Der Wert ist unveraendert.
			 *
			 * 08.10.2026, Teil 3 §5b: ab jetzt `darf_lesen()` — manage_options
			 * ODER cv_see_internals. Das LESEN ist weiter geworden, das
			 * HANDELN nicht: handle_run(), handle_sweep() und
			 * handle_witness() riegeln unveraendert auf self::CAP.
			 *
			 *     Der Pruefungs-Knopf legt Testkonten an. Das darf kein
			 *     Leser duerfen.                             (Mammut, 5c) */
			if ( ! self::darf_lesen() ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			$roh     = get_transient( 'pacioli_selftest_results' );
			$witness = self::witness_record();

			// H186 — DREI FAELLE, und der mittlere ist der gefaehrliche.
			//
			//   neu    ein Ergebnis MIT eigenem Gegenstand
			//   alt    ein Ergebnis aus dem Zwischenspeicher einer aelteren
			//          Fassung, das keinen traegt — dann ist NICHT feststellbar,
			//          welchen Code es gemessen hat, und genau das muss dastehen
			//   keins  noch nicht gelaufen
			$results  = null;
			$gemessen = null;

			if ( is_array( $roh ) && isset( $roh['zeilen'] ) && is_array( $roh['zeilen'] ) ) {
				$results       = $roh['zeilen'];
				$gemessen = isset( $roh['gegenstand'] ) && is_array( $roh['gegenstand'] ) ? $roh['gegenstand'] : null;
			} elseif ( is_array( $roh ) ) {
				$results = $roh;   // Zwischenspeicher aus einer Fassung vor H186.
			}

			$jetzt = self::subject();

			/** Eine Adresse in einer Zeile: Fassung · Datei sha1 · Abdruck. */
			/* POSTEN 4, 08.10.2026 — DIE ZWEITE MARKE WAR UNBENANNT.
			 *
			 * Hier standen zwei Zwoelfzeichen-Marken nebeneinander und nur die
			 * erste war erraten: die Hauptdatei. Was die zweite ist, sagte die
			 * Zeile nicht. Mammut hat sie gegen einen sha256 verglichen und
			 * eine Abweichung gemeldet, die es nicht gab.
			 *
			 *     Eine Marke ohne ihr Verfahren ist eine Zahl, die zum
			 *     Vergleich einlaedt, ohne zu sagen, womit.
			 *
			 * Jetzt tragen BEIDE ihr Verfahren — das ist die eigene Regel vom
			 * 18.09., nur bisher auf Berichte angewendet und nicht auf die
			 * Seite: wer eine Marke MELDET, nennt das Verfahren mit. sha1
			 * bekommt sein Praefix, weil es nicht die Voreinstellung ist.
			 *                                                   (R-MARKE-1) */
			$adresse = function ( $g ) {
				return sprintf(
					/* translators: 1: Fassungsnummer, 2: Dateiname, 3: sha1 der Hauptdatei (12), 4: Abdruck ueber alle geladenen Dateien (12). */
					__( '%1$s · %2$s sha1:%3$s · fingerprint sha1:%4$s', 'cluster-one-pacioli' ),
					'' !== $g['version'] ? $g['version'] : '?',
					isset( $g['hauptdatei'] ) && '' !== $g['hauptdatei'] ? $g['hauptdatei'] : '?',
					isset( $g['haupt_sha1'] ) && '' !== $g['haupt_sha1'] ? substr( $g['haupt_sha1'], 0, 12 ) : '?',
					'' !== $g['abdruck'] ? substr( $g['abdruck'], 0, 12 ) : '?'
				);
			};

			$fremd = ( $results && $gemessen && $gemessen['abdruck'] !== $jetzt['abdruck'] );
			?>
			<div class="wrap">
				<?php
				/* DIE UEBERSCHRIFT WOHNT IN CV_Admin::ueberschrift() — eine
				   Stelle fuer beide Seiten. Faellt CV_Admin aus, steht hier
				   `CV_NAME`, und das ist DIESELBE Quelle, aus der jene
				   Methode liest: die eine Zeile im Lader. Es ist ein
				   zweiter WEG, kein zweiter WERT.

				       Eine Ueberschrift, die von einem fremden Haus
				       abhaengt, muss sagen koennen, was sie heisst, wenn
				       das Haus nicht da ist — und das gilt auch, wenn das
				       fremde Haus das eigene Nebenzimmer ist. */
				$c1_h1 = class_exists( 'Pacioli_Admin' ) ? Pacioli_Admin::ueberschrift() : PACIOLI_NAME;
				?>
				<h1><?php echo esc_html( $c1_h1 ); ?></h1>

				<?php
				/* DIE REITERLEISTE WOHNT IN CV_Admin — eine Schreibstelle fuer
				   beide Seiten. `class_exists` und nicht blind gerufen: faellt
				   CV_Admin aus, ist diese Seite ohne Reiter erreichbar statt
				   mit einem toedlichen Aufruf unerreichbar.

				       Ein Reiter ist ein Wegweiser. Ein Wegweiser, der die
				       Strasse sperrt, wenn er fehlt, ist keiner. */
				if ( class_exists( 'Pacioli_Admin' ) ) {
					Pacioli_Admin::reiter( self::PAGE );
				}
				?>

				<h2><?php esc_html_e( 'What this plugin can prove about itself', 'cluster-one-pacioli' ); ?></h2>

				<p style="max-width:48em">
					<?php esc_html_e( 'The following checks concern only what is decided inside this process: bolts, attribution, tidying up. They say nothing about which key arrives at the provider — that is decided outside and is dealt with further down.', 'cluster-one-pacioli' ); ?>
				</p>

				<?php
				/* 0.8.45 (30.10.2026, Neo): DIE KNOEPFE NUR FUER DEN, DER SIE DARF. Bis 0.8.44 wurden die
				 * drei Formulare jedem Leser gezeichnet, und der Handler sagte danach „Nicht erlaubt" – der
				 * Reiter ins Leere, eine Ebene tiefer. Jetzt steht fuer den Leser EIN Satz, der die drei
				 * Handgriffe nennt und wem sie gehoeren. Was jeder der drei ausloest, steht bei den
				 * Handlern: Testkonten (run), Schluessel in die Quarantaene des Klickenden (sweep), die
				 * Zusage des Knotens (witness). Keiner beruehrt nur das eigene Fach – darum SysOp. */
				if ( self::darf_handeln() ) :
				?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;margin-right:1em">
					<input type="hidden" name="action" value="pacioli_run_selftest">
					<?php wp_nonce_field( 'pacioli_run_selftest' ); ?>
					<?php submit_button( __( 'Run checks', 'cluster-one-pacioli' ), 'primary', 'submit', false ); ?>
				</form>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block">
					<input type="hidden" name="action" value="pacioli_sweep">
					<?php wp_nonce_field( 'pacioli_sweep' ); ?>
					<?php submit_button( __( 'Tidy provider rows', 'cluster-one-pacioli' ), 'secondary', 'submit', false ); ?>
				</form>
				<?php else : ?>
				<p style="max-width:48em;color:#555">
					<?php esc_html_e( 'Three actions on this page belong to the SysOp: running the checks (it creates test accounts for the run), tidying the provider rows (it moves keys into the vault of whoever clicks), and recording the outside measurement (it is a promise of the whole node). What you see here is what the node can show about itself.', 'cluster-one-pacioli' ); ?>
				</p>
				<?php endif; ?>

				<?php
				// H186 — DIESE ZEILE STEHT IMMER.
				//
				// Bis heute erschien sie nur zusammen mit einem Ergebnis. Wer die
				// Seite aufschlug, ohne die Pruefungen laufen zu lassen — und das
				// tut jeder, der nachsehen will, was installiert ist —, bekam
				// keine einzige Zahl zu sehen, an der er den Stand haette
				// festmachen koennen.
				?>
				<p style="max-width:64em;margin-top:1.5em;margin-bottom:.4em;font-family:monospace;font-size:.9em">
					<?php
					printf(
						/* translators: 1: Fassung · Hauptdatei sha1 · Abdruck, 2: Anzahl Dateien. */
						esc_html__( 'Loaded right now: %1$s · %2$d files', 'cluster-one-pacioli' ),
						esc_html( $adresse( $jetzt ) ),
						count( $jetzt['dateien'] )
					);
					?>
				</p>

				<?php if ( is_array( $results ) ) : ?>
					<?php $gegenstand = $gemessen ? $gemessen : $jetzt; ?>

					<?php if ( ! $gemessen ) : ?>
						<div class="notice notice-warning inline" style="max-width:64em;margin:0 0 1em">
							<p><?php esc_html_e( 'These results carry no subject of their own — they were left behind by an older version that did not stamp one. Which code they measured cannot be established. Run the checks again before quoting a single line of them.', 'cluster-one-pacioli' ); ?></p>
						</div>
					<?php elseif ( $fremd ) : ?>
						<div class="notice notice-error inline" style="max-width:64em;margin:0 0 1em">
							<p>
								<?php esc_html_e( 'These results do not belong to the code that is loaded now. Something changed between the run and this page — run the checks again before quoting a single line of them.', 'cluster-one-pacioli' ); ?>
							</p>
							<p style="font-family:monospace;font-size:.9em">
								<?php
								printf(
									/* translators: %s: Fassung · Hauptdatei sha1 · Abdruck des gemessenen Standes. */
									esc_html__( 'Measured against: %s', 'cluster-one-pacioli' ),
									esc_html( $adresse( $gemessen ) )
								);
								?>
							</p>
						</div>
					<?php else : ?>
						<p style="max-width:64em;margin-top:0;margin-bottom:1em;font-family:monospace;font-size:.9em;color:#555">
							<?php
							printf(
								/* translators: %s: Fassung · Hauptdatei sha1 · Abdruck des gemessenen Standes. */
								esc_html__( 'Measured against: %s', 'cluster-one-pacioli' ),
								esc_html( $adresse( $gemessen ) )
							);
							?>
						</p>
					<?php endif; ?>

					<details style="max-width:64em;margin-bottom:1em">
						<summary style="cursor:pointer"><?php esc_html_e( 'Which files ran, and their fingerprints', 'cluster-one-pacioli' ); ?></summary>
						<p style="max-width:64em;color:#555">
							<?php esc_html_e( 'A version number in the header says what the author believed. sha1_file() says which file actually ran. This list comes from what the process loaded, not from a list somebody maintains.', 'cluster-one-pacioli' ); ?>
						</p>
						<pre style="font-size:.85em;overflow-x:auto"><?php
						foreach ( $gegenstand['dateien'] as $rel => $sha ) {
							echo esc_html( sprintf( "%-34s %s\n", $rel, $sha ) );
						}
						?></pre>
					</details>

					<table class="widefat striped" style="max-width:64em">
						<tbody>
						<?php
						// 02.09.2026 — DREI STÄNDE, NICHT ZWEI.
						//
						// Eine Zeile, die zwischen „falsch" und „ich konnte es
						// nicht sehen" nicht unterscheidet, gibt beim zweiten
						// Fall eine Auskunft, die sie nicht hat. Ältere
						// Ergebnisse aus dem Zwischenspeicher tragen `stand`
						// nicht — für die gilt weiter das alte Ja/Nein.
						$zeichen = array(
							'gruen' => '<span style="color:#1F5C46">&#10003;</span>',
							'rot'   => '<span style="color:#9E3B16">&#10007;</span>',
							'nicht' => '<span style="color:#7A7A7A">&ndash;</span>',
						);

						$ungemessen = false;
						?>
						<?php foreach ( $results as $r ) : ?>
							<?php
							$stand = isset( $r['stand'] ) ? $r['stand'] : ( $r['ok'] ? 'gruen' : 'rot' );

							if ( 'nicht' === $stand ) {
								$ungemessen = true;
							}
							?>
							<tr>
								<td style="width:2em;font-size:1.2em">
									<?php echo isset( $zeichen[ $stand ] ) ? $zeichen[ $stand ] : $zeichen['rot']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</td>
								<td style="width:24em"><strong><?php echo esc_html( $r['name'] ); ?></strong></td>
								<td><?php echo esc_html( $r['detail'] ); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>

					<?php if ( $ungemessen ) : ?>
						<p style="max-width:64em;margin-top:.5em;color:#555">
							<?php esc_html_e( 'A dash means: not measured. That is not an all-clear — it says this check could not see what it would have needed to see, and a line that reported its own blindness as a "no" would be making a claim it does not have.', 'cluster-one-pacioli' ); ?>
						</p>
					<?php endif; ?>
				<?php else : ?>
					<p style="max-width:64em;margin-bottom:1em;color:#555">
						<?php esc_html_e( 'The checks have not been run, or their result has expired. The line above says what would be measured, not what was.', 'cluster-one-pacioli' ); ?>
					</p>
				<?php endif; ?>

				<?php
				/* DER ABSCHNITT MIT DEN BETRIEBSZAHLEN IST AM 08.10.2026 AUF
				 * DIE TAFEL UMGEZOGEN — Teil 3 §5a.
				 *
				 * Diese Zahlen nennen Kennungen und Daten, also Personen. Das
				 * ist Betreiberauskunft und keine Entwicklerauskunft — und sie
				 * haben Christian vier Befunde geliefert.
				 *
				 * DER VERWEIS NENNT DEN ORT NICHT GETIPPT. Zwei Pruefungszeilen
				 * weiter oben sprechen von diesen Zahlen und fragen denselben
				 * Namen. Ein Umzug, der drei Saetze zuruecklaesst, ist der Fund
				 * von Posten 1 noch einmal.
				 */
				?>
				<p style="max-width:64em;color:#555">
					<?php
					printf(
						/* translators: 1: Name des Abschnitts, 2: Ort des Abschnitts. */
						esc_html__( 'The figures under %1$s now stand under %2$s — they name identities and dates, so they are an operator\'s answer and not a developer\'s.', 'cluster-one-pacioli' ),
						esc_html( self::abschnitt_betrieb() ),
						esc_html( class_exists( 'Pacioli_Admin' ) ? Pacioli_Admin::wo_betrieb() : __( 'this plugin\'s own page', 'cluster-one-pacioli' ) )
					);
					?>
				</p>

				<hr style="margin:2.5em 0">

				<?php Pacioli_Notes::render(); ?>

				<hr style="margin:2.5em 0">

				<?php
				/* TEIL 3 §3c, 08.10.2026 — EIN SATZ MIT DEM WEG, KEIN UMZUG.
				 *
				 * INTEGRATION.md kommt NICHT auf diese Seite. Gemessen:
				 * 10010 B, 234 Zeilen, 59 Absaetze, 267 Bytes ueber 126 —
				 * also deutsch, waehrend die readme.txt englisch ist.
				 *
				 *     Die Tuer nach aussen ist auf Deutsch beschriftet.
				 *     Das Schild davor ist auf Englisch.   (Mammut, 3c)
				 *
				 * Sie uebersetzen heisst rund fuenfzig weitere msgid in
				 * denselben Katalog und denselben Zug. Das ist ein eigener
				 * Posten, vor dem Tor zur Welt — danach ist der Umzug
				 * billig, weil die msgid dann englisch sind wie alle
				 * anderen.
				 *
				 * UND DER SATZ SAGT, DASS SIE DEUTSCH IST. Wer hier klickt
				 * und Deutsch vorfindet, soll es vorher gelesen haben.
				 *
				 *     Ein Verweis, der die Sprache seines Ziels
				 *     verschweigt, schickt jemanden in eine Ueberraschung.
				 *
				 * DER PFAD WIRD NICHT GETIPPT: er faellt aus
				 * plugin_basename(CV_FILE) und bleibt richtig, wenn jemand
				 * den Ordner umbenennt. */
				$c1_ordner = defined( 'PACIOLI_FILE' ) ? dirname( plugin_basename( PACIOLI_FILE ) ) : '';
				?>
				<h2><?php esc_html_e( 'Building on this plugin', 'cluster-one-pacioli' ); ?></h2>

				<p style="max-width:48em">
					<?php
					if ( '' !== $c1_ordner ) {
						printf(
							/* translators: %s: Pfad der Datei im Plugin-Verzeichnis. */
							esc_html__( 'The integration guide ships with this plugin, as %s. It describes the hooks, what this plugin guarantees, and what it does not. It is written in German, unlike this interface.', 'cluster-one-pacioli' ),
							esc_html( $c1_ordner . '/INTEGRATION.txt' ) // 0.9.4: INTEGRATION.md heisst jetzt INTEGRATION.txt (Verzeichnis-Pruefer; Inhalt gleich)
						);
					} else {
						esc_html_e( 'The integration guide ships with this plugin as INTEGRATION.txt, next to the main file. It is written in German, unlike this interface.', 'cluster-one-pacioli' );
					}
					?>
				</p>

				<h2><?php esc_html_e( 'The proof this plugin cannot supply itself', 'cluster-one-pacioli' ); ?></h2>

				<p style="max-width:48em">
					<?php
					/* 0.8.47: „from outside" hiess bis heute irgendwo jenseits der Installation; Nemesus
					 * las es so und fragte, womit er denn noch messen solle. Gemeint ist: ausserhalb
					 * DIESES PLUGINS, an der letzten Station im Haus. Der Satz sagt das jetzt. */
					esc_html_e( 'Whether the swapped key actually arrives at the provider cannot be established from the inside — a plugin that watches itself swapping only bears witness that it swapped. The proof requires a measurement at the outgoing HTTP request, from outside this plugin: at the last stop before a request leaves this installation.', 'cluster-one-pacioli' );
					?>
				</p>

				<p style="max-width:48em">
					<?php
					/* 0.8.47: bis 0.8.46 endete der Absatz mit „…but a person's confirmation that an
					 * outside measurement has been made." Die Person traegt seit 0.8.46 nichts mehr ein;
					 * Turing misst, Pacioli uebernimmt bei GRUEN. */
					esc_html_e( 'The predecessor of this plugin made a mistake at exactly this point: it measured at the options layer, where everything was fine anyway, turned green, and in doing so deleted the warning that pointed at the untested assumption. The assumption was wrong. That is why there is no "check passed" button here. What stands here is Turing’s measurement at the outgoing request, which Pacioli records only when Turing’s verdict is GREEN — and, until Turing has measured, the open warning.', 'cluster-one-pacioli' );
					?>
				</p>

				<?php
				/* WAS HIER EINGETRAGEN WIRD — und es stand bisher nirgends.
				 *
				 * Der SysOp dieses Knotens konnte nicht sagen, was der Knopf
				 * tut. Das ist keine Nachlaessigkeit von ihm, sondern eine
				 * fehlende Zeile von mir: der Knopf sagte, was gruen war, und
				 * nicht, was er eintraegt. */
				?>
				<p style="max-width:48em">
					<strong><?php esc_html_e( 'What gets recorded here:', 'cluster-one-pacioli' ); ?></strong>
					<?php
					/* 0.8.47: bis 0.8.46 stand hier „that YOU have run a measurement at the actual
					 * outgoing HTTP request, from outside this installation, with two identities." Das
					 * war die Vorstellung vor Turing: zwei Menschen, zwei Abdruecke, ein Werkzeug jenseits
					 * des Hauses. Turing misst anders und maschinell; der Satz nennt jetzt Turing und
					 * Turings Grenze. */
					esc_html_e( 'that Turing (CO) has measured at the actual outgoing HTTP request of this installation, from outside this plugin, and found the key in no option row. It does NOT record that the checks above are green. Those checks are this plugin watching itself, and no number of green ticks up there is evidence for the one thing this entry is about. Turing sits in the same installation and sees what runs through wp_remote_*; no measurement from here reaches further.', 'cluster-one-pacioli' );
					?>
				</p>

				<?php
				$gemeldet = isset( $_GET['witness'] ) ? sanitize_key( wp_unslash( $_GET['witness'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler

				if ( self::WITNESS_NO_CALLS === $gemeldet || self::WITNESS_NO_NOTE === $gemeldet ) :
					?>
					<div class="notice notice-error inline" style="max-width:48em">
						<p>
							<?php
							echo self::WITNESS_NO_NOTE === $gemeldet
								? esc_html__( 'Not recorded: the note was empty. Say what you measured with and when — an entry nobody can trace back to a run is not a record of one.', 'cluster-one-pacioli' )
								: esc_html__( 'Not recorded: no outgoing call has been seen on behalf of a bound AI on this installation. There is nothing here an outside measurement could have measured.', 'cluster-one-pacioli' );
							?>
						</p>
					</div>
					<?php
				endif;

				/* 0.8.46: die Antworten des Turing-Weges (handle_witness_turing). */
				if ( 'turing_fehlt' === $gemeldet ) :
					?>
					<div class="notice notice-error inline" style="max-width:48em"><p><?php esc_html_e( 'Not measured: Turing is not on this node. The outside measurement can only be made once Turing (CO) is installed.', 'cluster-one-pacioli' ); ?></p></div>
					<?php
				elseif ( 'turing_alt' === $gemeldet ) :
					?>
					<div class="notice notice-error inline" style="max-width:48em"><p><?php esc_html_e( 'Not recorded: the Turing on this node is older than 3.14.7 and does not answer Pacioli’s question yet. Update Turing, then run again.', 'cluster-one-pacioli' ); ?></p></div>
					<?php
				elseif ( 0 === strpos( $gemeldet, 'turing_' ) ) :
					$c1_t = get_transient( 'pacioli_turing_' . get_current_user_id() );
					$c1_t = is_array( $c1_t ) ? $c1_t : array( 'urteil' => '?', 'zeilen' => array(), 'unterbau' => '' );
					?>
					<div class="notice notice-error inline" style="max-width:48em">
						<p>
							<?php
							printf(
								/* translators: %s: Turing's verdict word. */
								esc_html__( 'Not recorded: Turing’s verdict on Pacioli’s question is %s, not GREEN. Nothing was written; the notice stays. Per provider:', 'cluster-one-pacioli' ),
								'<strong>' . esc_html( (string) $c1_t['urteil'] ) . '</strong>'
							);
							?>
						</p>
						<ul>
							<?php foreach ( (array) $c1_t['zeilen'] as $c1_cid => $c1_u ) : ?>
								<li><code><?php echo esc_html( (string) $c1_cid ); ?></code> — <?php echo esc_html( (string) $c1_u ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php
				endif;

				/* =============================================================
				 *  POSTEN 1, 10.10.2026 — DER RIEGEL STAND AUF EINER
				 *  VARIABLEN, DIE ES NICHT GAB.
				 * =============================================================
				 *
				 *  Hier stand `self::witness_gate( $nenner )`, und `$nenner`
				 *  kam in dieser ganzen Datei GENAU EINMAL vor: in dieser
				 *  Zeile, lesend. Gesetzt wurde er nie.
				 *
				 *  ER WURDE GESETZT — bis zum 08.10., im Abschnitt mit den
				 *  Betriebszahlen, zwanzig Zeilen darueber. Dann ist der
				 *  Abschnitt auf die Tafel umgezogen (Teil 3 §5a) und hat
				 *  seine Variable mitgenommen. Die Zeile, die sie liest,
				 *  blieb stehen.
				 *
				 *      (int) null === 0, also witness_gate( 0 ), also
				 *      WITNESS_NO_CALLS, also war das Eingabefeld fuer die
				 *      Fremdmessung auf JEDER Installation zu. Zwei Tage
				 *      lang, und auf Christians Knoten stehen 45 Aufrufe.
				 *
				 *  UND ES IST SCHLIMMER ALS EIN FALSCHES GRUEN:
				 *
				 *      Ein Vorbehalt, der nicht aufgehoben werden kann, ist
				 *      keine Ehrlichkeit mehr. Er ist eine Zusage, die
				 *      niemand einloesen darf.                    (Mammut)
				 *
				 *  Dieser Reiter ist die eine Stelle, die gebaut wurde,
				 *  damit dieses Haus sich nicht selbst beruhigt — und sie
				 *  war zugeschweisst, und zwar so, dass es wie Demut aussah.
				 *
				 * -------------------------------------------------------------
				 *  WARUM KEIN ZEUGE ES GEFUNDEN HAT, UND DAS IST DER
				 *  EIGENTLICHE BEFUND
				 *
				 *  B7 misst, DASS render() den Riegel fragt. B2–B5 fahren
				 *  den Riegel mit 0 und mit 7. Keine Zeile hat gemessen,
				 *  WOMIT render() ihn fragt — und der Fall, in dem er
				 *  OEFFNET, ist auf dieser Seite nie gefahren worden.
				 *
				 *      Ein Riegel, der nur in der Richtung gefahren wurde,
				 *      in die er haelt, ist nicht geprueft, sondern
				 *      beobachtet.                                 (Mammut)
				 *
				 *  B13 und B14 fahren ihn ab heute an der SEITE, in beide
				 *  Richtungen.
				 *
				 *  DIE ZAHL KOMMT AUS DERSELBEN QUELLE wie auf der Tafel —
				 *  CV_Marker::bound_calls(). Zwei Leser, eine Wahrheit.
				 *                                                   (P1-12)
				 * ============================================================= */
				$nenner = ( class_exists( 'Pacioli_Marker' ) )
					? (int) Pacioli_Marker::bound_calls()['count']
					: 0;

				$aufgenommen = self::witness_confirmed();
				$riegel      = self::witness_gate( $nenner );
				$war_mal     = ! $aufgenommen && isset( $witness['withdrawn'] );
				?>

				<?php if ( $aufgenommen ) : ?>
					<div class="notice notice-success inline" style="max-width:48em">
						<p>
							<?php
							$by = get_userdata( (int) ( $witness['by'] ?? 0 ) );
							printf(
								/* translators: 1: Name, 2: Datum, 3: Notiz. */
								esc_html__( 'Recorded by %1$s on %2$s. Measured with: %3$s', 'cluster-one-pacioli' ),
								esc_html( $by ? $by->display_name : '?' ),
								esc_html( date_i18n( get_option( 'date_format' ), (int) ( $witness['time'] ?? 0 ) ) ),
								esc_html( $witness['note'] ? $witness['note'] : '—' )
							);
							?>
						</p>
					</div>

					<?php
					/* DER RUECKWEG. Eine Bestaetigung, die man nicht
					 * zuruecknehmen kann, ist ein Beschluss. */
					/* 0.8.45: nur fuer den, der handeln darf (Satz oben). */
					if ( self::darf_handeln() ) :
					?>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width:48em">
						<input type="hidden" name="action" value="pacioli_confirm_witness">
						<input type="hidden" name="withdraw" value="1">
						<?php wp_nonce_field( 'pacioli_confirm_witness' ); ?>
						<p>
							<label for="cv-withdraw-note"><?php esc_html_e( 'Withdrawing this entry? Say why — it stays in the record.', 'cluster-one-pacioli' ); ?></label><br>
							<input type="text" name="note" id="cv-withdraw-note" class="regular-text" style="width:100%">
						</p>
						<?php submit_button( __( 'Withdraw this entry', 'cluster-one-pacioli' ), 'delete', 'submit', false ); ?>
					</form>
					<?php endif; ?>

				<?php elseif ( '' !== $riegel ) : ?>
					<?php
					/* DER RIEGEL, UND ER BIETET NICHTS AN, WAS ER ABWEIST.
					 *
					 * Ein Formular, das eine Handlung anbietet, die der Server
					 * danach verweigert, ist kein Riegel, sondern eine
					 * Enttaeuschung mit zwei Klicks. */
					?>
					<div class="notice notice-warning inline" style="max-width:48em">
						<p>
							<strong><?php esc_html_e( 'Nothing can be recorded here yet.', 'cluster-one-pacioli' ); ?></strong>
							<?php
							/* POSTEN 1b — „die Zahl oben" STAND NICHT MEHR OBEN.
							 *
							 * Derselbe Umzug, derselbe Tag: der Satz verwies
							 * auf eine Zahl, die zwei Absaetze weiter oben
							 * schon als umgezogen angekuendigt wird. Der
							 * Verweis war doppelt falsch — der Gegenstand war
							 * weg, und dort, wo er jetzt steht, sagt er auf
							 * Christians Knoten 45.
							 *
							 *     Ein Verweis altert nicht mit, weil niemand
							 *     ihn zaehlt.                       (Mammut)
							 *
							 * Jetzt nennt der Satz die Zahl SELBST — sie
							 * steht ohnehin schon in dieser Zeile zur
							 * Verfuegung, und eine genannte Zahl kann nicht
							 * umziehen. */
							printf(
								/* translators: %d: gesehene ausgehende Aufrufe im Namen einer gebundenen KI. */
								esc_html__( 'Outgoing calls seen on behalf of a bound AI on this installation: %d. An outside measurement needs an outgoing request to measure; where none has gone out, there is nothing to attest to. Once a call has been made on behalf of an AI, this field opens by itself.', 'cluster-one-pacioli' ),
								(int) $nenner
							);
							?>
						</p>
					</div>

					<?php if ( $war_mal ) : ?>
						<p style="max-width:48em;color:#555">
							<?php esc_html_e( 'An earlier entry was withdrawn and is kept in the record.', 'cluster-one-pacioli' ); ?>
						</p>
					<?php endif; ?>

				<?php else : ?>
					<?php if ( $war_mal ) : ?>
						<p style="max-width:48em;color:#555">
							<?php esc_html_e( 'An earlier entry was withdrawn and is kept in the record.', 'cluster-one-pacioli' ); ?>
						</p>
					<?php endif; ?>

					<?php
					/* 0.8.45 (30.10.2026, Neo): DER WEG ZUR MESSUNG STEHT DABEI. Nemesus: „Ich habe keine
					 * Ahnung, was das bringen soll und wie ich die anstellen sollte." Der Knopf sagte „ich
					 * habe von aussen gemessen", aber nicht, womit. Das Werkzeug ist Turing, Reiter
					 * Pacioli-Zeuge: zwei Aufrufe je Anbieter, erst mit dem echten Schluessel, dann mit
					 * der Marke in der Option – traegt der zweite Request die Marke, liest der Client je
					 * Aufruf, und Paciolis Tausch kommt an. Kein Prompt verlaesst das Haus, keine Kosten.
					 * Liegt Turing auf dem Knoten, ist der Satz ein Link; sonst nennt er den Ort.
					 *
					 * 0.8.47: DER SATZ BESCHRIEB DIE ALTE FRAGE. Bis 0.8.46 stand hier: „It calls every
					 * provider twice — once with the real key, once after switching the key option to a
					 * marker. If the second request carries the marker, the client reads the key per
					 * call and Pacioli's swap arrives. … Then come back and record the run here, with
					 * that result as the note." Das ist seit Turing 3.14.7 die graue Unterbau-Zeile
					 * (ROT, weil der Client einmal liest und haelt) und nicht Paciolis Frage; und das
					 * Abtippen gibt es seit 0.8.46 nicht mehr. Jetzt steht die Frage, die Turing fuer
					 * Pacioli beantwortet, und wo Turing dabei sitzt. */
					/* 0.9.1 (02.11.2026, Neo): bis 0.9.0 stand hier `function_exists( 'tu_seite' ) ? admin_url(
					 * 'tools.php?page=turing&reiter=zeuge' ) : ''` — ein alter Funktionsname UND ein abgeschriebener
					 * Seitenname. Jetzt nennt Turing seine Adresse selbst (turing_zeuge_url, Turing 4.0.0: der Zeuge
					 * wohnt in Turing Diagnostics); fuer ein Turing 3.15.0 der Rueckfall auf den alten Seitennamen. */
					$c1_turing = function_exists( 'turing_zeuge_url' ) ? (string) turing_zeuge_url()
						: ( function_exists( 'turing_seite' ) ? admin_url( 'tools.php?page=turing&reiter=zeuge' ) : '' );
					?>
					<p style="max-width:48em;color:#555">
						<?php
						$c1_satz = esc_html__( 'How the outside measurement works: Turing (CO), tab “Pacioli witness”, asks every provider the one question this plugin cannot answer about itself — does the key that leaves the server in the POST come from an option row, or from what was set before the first read, Pacioli’s swap? Turing listens at the outgoing HTTP request, the last stop before a request leaves this installation. No prompt leaves the house, no tokens are spent. The button below runs that witness and records its verdict; nothing is typed by hand.', 'cluster-one-pacioli' );
						if ( '' !== $c1_turing ) {
							echo $c1_satz . ' <a href="' . esc_url( $c1_turing ) . '">' . esc_html__( 'Open Turing’s Pacioli witness', 'cluster-one-pacioli' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $c1_satz ist esc_html__().
						} else {
							echo $c1_satz; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_html__().
						}
						?>
					</p>
					<?php
					/* 0.8.46 (30.10.2026, Neo): DER KNOPF, DER NICHT ABTIPPT. Mit Turing auf dem Knoten
					 * faehrt Pacioli den Durchgang selbst und traegt bei GRUEN ein (handle_witness_turing).
					 * Ohne Turing: die Meldung, die Nemesus verlangt hat – der Test kann erst erfolgen, wenn
					 * Turing installiert ist. Fuer jeden Leser sichtbar, der Knopf nur fuer den SysOp. */
					if ( '' === $c1_turing ) :
						?>
						<div class="notice notice-info inline" style="max-width:48em"><p><strong><?php esc_html_e( 'Turing is not on this node.', 'cluster-one-pacioli' ); ?></strong> <?php esc_html_e( 'The outside measurement can only be made once Turing (CO) is installed. Until then this notice stays, and nothing can be recorded automatically here.', 'cluster-one-pacioli' ); ?></p></div>
						<?php
					elseif ( self::darf_handeln() ) :
						?>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width:48em;margin-bottom:1.2em">
							<input type="hidden" name="action" value="pacioli_witness_turing">
							<?php wp_nonce_field( 'pacioli_witness_turing' ); ?>
							<?php submit_button( __( 'Measure with Turing and record', 'cluster-one-pacioli' ), 'primary', 'submit', false ); ?>
							<span style="color:#555;margin-left:.6em"><?php esc_html_e( 'Pacioli runs Turing’s witness and records the run only if its verdict is GREEN; anything else is shown and nothing is written.', 'cluster-one-pacioli' ); ?></span>
						</form>
						<?php
					endif;
					?>
					<?php
					/* 0.8.47 (30.10.2026, Neo): HIER STAND DAS HANDFORMULAR, bis 0.8.46:
					 *     „What did you measure with, and when? (required)" – Textfeld `note` –
					 *     Knopf „I measured from outside — record this run" (action cv_confirm_witness).
					 * Weg, weil der Mensch die Fehlerquelle ist, die 0.8.46 gerade ausgebaut hat, und
					 * weil es der Meldung „ohne Turing kann hier nichts eingetragen werden" widersprach.
					 * handle_witness() lehnt einen Eintrag-POST seit heute laut ab; die Zuruecknahme
					 * oben (withdraw=1) ist alles, was dieser Weg noch tut. */
					?>
				<?php endif; ?>
				<?php
				/* 0.10.2 (03.11.2026, Neo), Runde Omega — DIE FREIGABE, DIE ES BEI PACIOLI NICHT GIBT, STEHT HIER.
				 * Nemesus: Rechte vergibt das Plugin, das sie betreffen, auf seiner eigenen Seite. Bei Pacioli ist das Recht
				 * seit 0.8.44/0.8.45 die Mitgliedschaft selbst; der Abschnitt sagt es dem SysOp, mit der Zahl und dem Weg
				 * zur Liste. Die Massenaktionen der Benutzerliste sind fort (class-cv-admin.php, Ende der Klasse). */
				if ( self::darf_handeln() ) :
					$c1_mitglieder = function_exists( 'cluster_one_freigabe_mitglieder' ) ? cluster_one_freigabe_mitglieder() : array();
					$c1_anzahl     = count( $c1_mitglieder );
					?>
					<h2 style="margin-top:2.2em;"><?php esc_html_e( 'Release', 'cluster-one-pacioli' ); ?></h2>
					<p class="description" style="max-width:48em;"><?php esc_html_e( 'Nothing to release here, and that is the rule since 0.8.44: every member of the node may run their own AIs and read this page. The right comes with membership and is given to nobody else — whoever is not on the node’s list gets nothing from Cluster One. Membership is kept under Node → Node settings.', 'cluster-one-pacioli' ); ?></p>
					<p><?php echo esc_html( sprintf(
						/* translators: %d: number of members of the node besides the SysOp */
						_n( '%d member of the node besides the SysOp.', '%d members of the node besides the SysOp.', $c1_anzahl, 'cluster-one-pacioli' ),
						$c1_anzahl
					) ); ?> <a href="<?php echo esc_url( admin_url( 'admin.php?page=cluster_one_settings' ) ); ?>"><?php esc_html_e( 'Node settings', 'cluster-one-pacioli' ); ?></a></p>
				<?php endif; ?>
			</div>
			<?php
		}
	}
}
