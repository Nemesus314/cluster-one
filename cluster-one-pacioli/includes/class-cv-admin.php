<?php
/**
 * Oberfläche.
 *
 * Zwei Bildschirme, mehr nicht:
 *
 *   „Meine KIs“ — jeder Berechtigte verwaltet dort seine eigenen
 *   Identitäten und trägt seine Schlüssel ein. Fremde Identitäten sind
 *   hier nicht sichtbar.
 *
 *   Benutzerübersicht — der Administrator hakt an, wer eigene KIs
 *   betreiben darf. Bewusst als Spalte in der VORHANDENEN Liste statt als
 *   eigene Maske: Suche, Rollenfilter und Paginierung gibt es damit
 *   geschenkt, und die Berechtigung liegt dort, wo man sie sucht.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 *  DER RIEGEL UM DIE KLASSE — 12.09.2026, Weg C, dritte Schicht
 *
 *  Ohne ihn stirbt ein Knoten mit ZWEI Produktordnern an
 *  `Cannot redeclare class CV_Admin`, sobald der Lader seine EIGENEN Dateien
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
if ( ! class_exists( 'Pacioli_Admin' ) ) {
	class Pacioli_Admin {

		/**
		 * Der Slug der Schluesselmaske.
		 *
		 * Er bleibt derselbe, egal wo die Seite haengt — und deshalb aendert
		 * sich auch ihre Adresse nicht: `add_menu_page` und
		 * `add_submenu_page` erzeugen beide `admin.php?page=<slug>`.
		 * Nur `add_users_page` erzeugte `users.php?page=<slug>`, und genau die
		 * eine Sonderform ist am 03.09.2026 weggefallen.
		 */
		const PAGE = 'cv-my-ais';

		/**
		 * Die Tafel des Betreibers — Teil 3 §5a, 08.10.2026.
		 *
		 * Eigener Slug und nicht ein Parameter an `cv-my-ais`: die
		 * Reiterleiste baut ihre Adressen aus dem Slug, und ein Reiter, der
		 * auf dieselbe Seite mit einem anderen Parameter zeigt, waere in der
		 * Leiste nicht als eigener Ort zu unterscheiden.
		 */
		const PAGE_AUSSTATTUNG = 'cv-equipment';

		/** Das Elternmenue, unter dem die Maske haengt. '' = eigene Seite. */
		private static $eltern = null;

		/** Was die beiden Signale gesagt haben. Nur fuer die Auskunft. */
		private static $befund = array();

		public static function init() {
			// PRIORITAET 100, und das ist kein Geschmack.
			//
			// `cluster-one-pacioli` sortiert VOR `cluster-one`: WordPress laedt
			// nach 'ordner/datei.php', und '-' (0x2D) steht vor '/' (0x2F).
			// Bei gleicher Prioritaet liefe unser admin_menu also, BEVOR
			// menue.php sein Elternmenue anmeldet — und wir faenden es nicht,
			// obwohl es zwei Zeilen spaeter dasteht.
			//
			//     Wer nach etwas sucht, das im selben Hook erst noch entsteht,
			//     misst die Reihenfolge und nennt es Abwesenheit.
			add_action( 'admin_menu', array( __CLASS__, 'add_pages' ), 100 );
			add_action( 'admin_post_pacioli_save_identity', array( __CLASS__, 'handle_save' ) );
			add_action( 'admin_post_pacioli_delete_identity', array( __CLASS__, 'handle_delete' ) );
			add_action( 'admin_post_pacioli_quarantine', array( __CLASS__, 'handle_quarantine' ) );
			add_action( 'admin_post_pacioli_check_key', array( __CLASS__, 'handle_check_key' ) );
			add_action( 'admin_post_pacioli_clear_orphans', array( __CLASS__, 'handle_clear_orphans' ) );
			add_action( 'admin_post_pacioli_export', array( __CLASS__, 'handle_export' ) );
			add_action( 'admin_post_pacioli_import', array( __CLASS__, 'handle_import' ) );
			add_action( 'admin_post_pacioli_import_speicher', array( __CLASS__, 'handle_import_speicher' ) ); // 0.8.39
			add_action( 'admin_post_pacioli_delete_speicher', array( __CLASS__, 'handle_delete_speicher' ) ); // 0.8.41
			add_action( 'admin_notices', array( __CLASS__, 'connectors_notice' ) );

			add_filter( 'manage_users_columns', array( __CLASS__, 'add_column' ) );
			add_filter( 'manage_users_custom_column', array( __CLASS__, 'render_column' ), 10, 3 );
			/* 0.10.2 (03.11.2026, Neo), Runde Omega — HIER STANDEN BIS 0.10.1 DREI ZEILEN:
			 *     add_filter( 'bulk_actions-users', array( __CLASS__, 'add_bulk_actions' ) );
			 *     add_filter( 'handle_bulk_actions-users', array( __CLASS__, 'handle_bulk' ), 10, 3 );
			 *     add_action( 'admin_notices', array( __CLASS__, 'bulk_notice' ) ); // 0.8.46
			 * Die Massenaktionen der Benutzerliste sind fort, siehe den Block am Ende der Klasse. Die Spalte „Own AIs" bleibt. */
		}

		/**
		 * Wo haengt die Maske? — gefragt wird die Tatsache, nicht der Name.
		 *
		 * =====================================================================
		 *  Warum es diese Fallunterscheidung gibt (03.09.2026)
		 * =====================================================================
		 *
		 *  Christian will die Maske als Tab unter Cluster One. Richtig — die
		 *  Schluessel gehoeren in den Cluster-Kontext.
		 *
		 *  Aber: DAS-HAUS §0a sagt, dieses Plugin ist fuer FREMDE Admins
		 *  gedacht, und ein fremder Admin spielt es ALLEIN auf. Dann gibt es
		 *  kein Menue `cluster_one` — es wird von `menue.php` angemeldet, und
		 *  `menue.php` reist in vier Produkten, von denen er keines hat.
		 *
		 *      Ein Tab unter einem Menue, das dieses Plugin selbst nicht
		 *      mitbringt, ist auf einem einzeln aufgespielten Knoten kein Tab.
		 *      Er ist eine Seite, die es nicht gibt — und die Schluessel waeren
		 *      nicht mehr eintragbar.
		 *
		 *  GEFRAGT WIRD, WAS `add_submenu_page()` BRAUCHT: den angemeldeten
		 *  Eltern-SLUG. `cluster_one_modul_da('menue')` sagt, ob das ORGAN
		 *  mitreist — das ist eine andere Frage, und sie kann wahr sein,
		 *  waehrend der Slug (noch) nicht angemeldet ist.
		 *
		 *      Ein Organ, das mitreist, ist kein Menue, das existiert.
		 *
		 *  Beide Antworten werden festgehalten. Weichen sie voneinander ab,
		 *  ist das ein Befund und keine Nebensache — die Auskunft unten sagt
		 *  es.
		 *
		 * @return string 'cluster_one' oder ''.
		 */
		public static function eltern() {
			if ( null !== self::$eltern ) {
				return self::$eltern;
			}

			$slug_da = isset( $GLOBALS['admin_page_hooks'] )
				&& is_array( $GLOBALS['admin_page_hooks'] )
				&& isset( $GLOBALS['admin_page_hooks']['cluster_one'] );

			$organ_da = function_exists( 'cluster_one_modul_da' )
				? (bool) cluster_one_modul_da( 'menue' )
				: null;

			self::$befund = array(
				'slug'  => $slug_da,
				'organ' => $organ_da,
			);

			self::$eltern = $slug_da ? 'cluster_one' : '';

			return self::$eltern;
		}

		/** Was die beiden Signale gesagt haben. */
		public static function eltern_befund() {
			self::eltern();

			return self::$befund;
		}

		/**
		 * Die Adresse der Maske — eine Stelle, nicht sieben.
		 *
		 * Bis 0.8.1 stand `users.php?page=cv-my-ais` an sechs Stellen im
		 * Quelltext. Beim Umzug waeren sechs Gelegenheiten gewesen, eine zu
		 * uebersehen — und eine uebersehene Weiterleitung fuehrt auf eine
		 * leere Seite, ohne Fehlermeldung.
		 */
		public static function page_url( $args = array() ) {
			$url = 'admin.php?page=' . self::PAGE;

			foreach ( $args as $k => $v ) {
				$url .= '&' . rawurlencode( $k ) . '=' . rawurlencode( $v );
			}

			return admin_url( $url );
		}

		/**
		 * DIE SEITE MELDET SICH AN. DEN PUNKT MACHT DAS DACH.
		 *
		 * Bis zum 16.09.2026 stand hier `add_submenu_page( $eltern, … )` —
		 * ein SICHTBARER Punkt, und er hiess „Connector Vault". Seit das
		 * Dach (`cluster_one_produkte()`, Boden 1.12.0) aus den Anmeldungen
		 * Ebene 1 baut, gibt es zu derselben Seite einen zweiten Eintrag,
		 * und der traegt den Namen aus meiner Anmeldung: „Pacioli".
		 *
		 *     P1-12: keine zwei Schreibwege auf eine Wahrheit. Zwei Leser
		 *     sind erlaubt, zwei Schreiber nie.
		 *
		 * Im Menue IST die Registrierung der Schreibweg. Zwei Eintraege auf
		 * eine Seite sind zwei Autoren, und WordPress entscheidet nach
		 * LADEREIHENFOLGE, welcher gewinnt — nicht nach Absicht.
		 *
		 *     Wo zwei schreiben und einer gewinnt, ist der Gewinner eine
		 *     Eigenschaft der Reihenfolge und keine Entscheidung.
		 *
		 * Deshalb: unter einem Dach meldet sich die Seite VERSTECKT an
		 * (`add_submenu_page( null, … )`, die Form, die der Boden selbst
		 * benennt, kern.php :2775). Sie bleibt erreichbar, sie behaelt
		 * ihren Seitentitel und ihre Capability — nur den PUNKT setzt ab
		 * jetzt das Dach, mit dem Namen aus der Anmeldung.
		 *
		 * WARUM DER MENUETITEL HIER STEHENBLEIBT: er wird nie angezeigt.
		 * Ihn zu leeren oder umzuschreiben, waere eine Aenderung am
		 * Katalog fuer etwas, das niemand liest — und „Connector Vault"
		 * bleibt ueberall sonst der Name des Produkts.
		 *
		 * OHNE Dach aendert sich NICHTS: dann gibt es keinen zweiten
		 * Schreiber, und die eigene sichtbare Seite unten ist der einzige
		 * Weg zu ihr.
		 */
		public static function add_pages() {
			$eltern = self::eltern();

			/* DIE TAFEL MELDET SICH IMMER VERSTECKT AN — Teil 3 §5a.
			 *
			 * Mit Dach gilt das ohnehin fuer alles (der Punkt kommt vom
			 * Dach). OHNE Dach ist der sichtbare Punkt die Maske, und die
			 * Tafel haengt als Reiter daran — nicht als zweiter Eintrag
			 * daneben.
			 *
			 *     Zwei Eintraege fuer ein Produkt sind zwei Namen fuer
			 *     eine Sache, und im Menue entscheidet die Reihenfolge,
			 *     welcher gewinnt.                                (P1-12) */
			add_submenu_page(
				null,
				__( 'Equipment', 'cluster-one-pacioli' ),
				__( 'Equipment', 'cluster-one-pacioli' ),
				PACIOLI_CAP,
				self::PAGE_AUSSTATTUNG,
				array( __CLASS__, 'render_equipment' )
			);

			if ( '' !== $eltern ) {
				add_submenu_page(
					null,
					__( 'Connector Vault', 'cluster-one-pacioli' ),
					__( 'Connector Vault', 'cluster-one-pacioli' ),
					PACIOLI_CAP,
					self::PAGE,
					array( __CLASS__, 'render_my_ais' )
				);

				return;
			}

			// Kein Cluster One auf diesem Knoten: eine eigene, SICHTBARE Seite.
			// Nicht wieder unter „Benutzer" — dort hat sie vier Wochen lang
			// niemand gefunden, der nicht wusste, dass es sie gibt.
			add_menu_page(
				__( 'Connector Vault', 'cluster-one-pacioli' ),
				__( 'Connector Vault', 'cluster-one-pacioli' ),
				PACIOLI_CAP,
				self::PAGE,
				array( __CLASS__, 'render_my_ais' ),
				'dashicons-privacy'
			);
		}

		// =====================================================================
		//  DIE UEBERSCHRIFT
		// =====================================================================

		/**
		 * Die Ueberschrift BEIDER Seiten — eine Stelle, nicht zwei.
		 *
		 * Bis zum 16.09.2026 stand ueber `cv-my-ais` „Meine KIs" und ueber
		 * `cv-selftest` „Connector Vault". Zwei Ueberschriften fuer ein
		 * Produkt, und keine von beiden hiess wie der Punkt im Menue.
		 *
		 * Seit es eine Reiterleiste gibt, sagt die Leiste, WO man ist.
		 * Dafuer ist sie da, und deshalb darf die Ueberschrift auf beiden
		 * Seiten dieselbe sein.
		 *
		 * ---------------------------------------------------------------------
		 * DER NAME WIRD NICHT GETIPPT
		 *
		 * `CV_NAME` faellt aus `$c1_pacioli_name` im Lader, und aus
		 * derselben Zeile faellt der Name im Register, aus dem das Dach
		 * seinen Menuepunkt baut. Eine getippte Ueberschrift waere ein
		 * zweiter Schreiber — und sie wuerde an dem Tag falsch, an dem
		 * jemand den Menuenamen aendert. Still.
		 *
		 *     Zwei Leser sind erlaubt, zwei Schreiber nie.        (P1-12)
		 *
		 * ---------------------------------------------------------------------
		 * DER RUECKFALL, UND ER IST DIE HAELFTE, DIE ZAEHLT
		 *
		 * `cluster_one_page_title()` wohnt in `kern.php`. Mein Paket traegt
		 * `kasper.php` und sonst nichts vom Boden. Auf einem Knoten ohne
		 * Cluster One gibt es die Funktion NICHT.
		 * [0.10.0 (Phi): seit heute liegt kern.php im Paket; der Rueckfall bleibt als Form stehen und faellt nicht mehr zu.]
		 *
		 *     Eine Ueberschrift, die von einem fremden Haus abhaengt, muss
		 *     sagen koennen, was sie heisst, wenn das Haus nicht da ist.
		 *                                                    (Mammut, 53 §4)
		 *
		 * Dann steht der EIGENE Name da: „Pacioli". Nicht leer, und nicht
		 * „Cluster One – " mit nichts dahinter — ein haengender
		 * Gedankenstrich ist eine Ueberschrift, die von einer Abwesenheit
		 * erzaehlt.
		 *
		 * `function_exists()` ist hier keine Vorsicht, sondern die Form:
		 * es ist H118 in klein, und H118 hiess, dass die leere Fassung am
		 * Wirt gelesen wird.
		 *
		 * ---------------------------------------------------------------------
		 * BENANNTE GRENZE, GEMESSEN UND NICHT ANGENOMMEN
		 *
		 * PROMPT 53 §3 sagt, `cluster_one_page_title()` gebe bei
		 * `<name> === 'Cluster One'` nur „Cluster One" zurueck. Gemessen in
		 * ZWEI Fassungen (kern.php `fc33e99d5cda` :2675 und `1ec02e229cdf`
		 * :2180) steht dort ohne Zweig:
		 *
		 *     return 'Cluster One – ' . $organ;
		 *
		 * Das Abraeumen des Stotterns steht in NACHTRAG 26a und betrifft den
		 * MENUEPUNKT, nicht diesen Helfer. FUER MICH IST ES FOLGENLOS: mein
		 * Name ist „Pacioli" und war es nie „Cluster One". Es steht hier,
		 * weil eine Angabe im Auftrag und eine Datei auseinandergingen und
		 * das spaeter niemand mehr nachsehen wuerde.
		 *
		 * @return string
		 */
		public static function ueberschrift() {
			if ( function_exists( 'cluster_one_page_title' ) ) {
				return (string) cluster_one_page_title( PACIOLI_NAME );
			}

			return PACIOLI_NAME;
		}

		/**
		 * Die Fragezeichen-Blase, 0.8.32 (27.10.2026, Neo).
		 *
		 * Pacioli traegt vom Boden nur kasper.php; cluster_one_help_bubble()
		 * wohnt in kern.php und ist auf einem Knoten mit Pacioli allein nicht
		 * da.
		 * [0.10.0 (Phi): kern.php liegt jetzt im Paket. Die eigene Blase bleibt — gleiche Form, kein Verhalten zu bewegen.] Also dieselbe Form wie dort und wie tu_blase() in Turing, ohne
		 * Boden: <details>, kein JavaScript, mit der Tastatur erreichbar.
		 * Bis 0.8.31 hatten beide Pacioli-Seiten kein Fragezeichen – Nemesus'
		 * Zaehlung nach Test 5.
		 *
		 * @param string $text Der Hilfetext, Klartext.
		 * @return string HTML.
		 */
		public static function blase( $text ) {
			return '<details style="display:inline-block;vertical-align:middle;margin-left:.5em;font-weight:400;">'
				. '<summary style="cursor:pointer;list-style:none;display:inline-block;width:1.5em;height:1.5em;line-height:1.5em;'
				. 'text-align:center;border:1px solid #c3c4c7;border-radius:50%;font-size:.7em;opacity:.75;"'
				. ' title="' . esc_attr__( 'What is this?', 'cluster-one-pacioli' ) . '">?</summary>'
				. '<div style="display:block;max-width:760px;margin:.8em 0 .4em;padding:.9em 1.1em;border:1px solid #dcdcde;'
				. 'border-radius:4px;background:#fff;font-size:.92em;line-height:1.6;font-weight:400;">'
				. wpautop( esc_html( (string) $text ) )
				. '</div></details>';
		}

		// =====================================================================
		//  DIE REITERLEISTE
		// =====================================================================

		/**
		 * DIE SEITEN DIESES PRODUKTS — eine Liste, zwei Leser.
		 *
		 * Seit dem 18.09.2026 fragt sie ausser der Reiterleiste auch der
		 * Sammler `cluster_one_seite_ansprueche` im Lader: aus derselben
		 * Zeile faellt, WO man hinkommt und WESSEN Seite das ist.
		 *
		 *     Wer dem Sammler eine zweite Liste tippt, hat zwei Wahrheiten
		 *     ueber seine eigenen Seiten.                 (Mammut, 21 §2)
		 *
		 * SIE FILTERT NICHT NACH CAPABILITY, und das ist die Sache und
		 * nicht die Bequemlichkeit: die Reiterleiste fragt beim Zeichnen,
		 * wer hindarf — der Sammler fragt, WEM die Seite gehoert. Wem eine
		 * Seite gehoert, haengt nicht davon ab, wer gerade davorsteht.
		 *
		 *     Eine Liste, die vom Betrachter abhaengt, ist eine Ansicht.
		 *     Besitz ist keine Ansicht.
		 *
		 * Faellt `CV_Selftest` aus, gibt es den zweiten Eintrag nicht — und
		 * dann fehlt er in BEIDEN Lesern, statt in einem. Das ist der Grund,
		 * warum die Liste hier steht und nicht zweimal.
		 *
		 * @return array<int,array{slug:string,cap:string,text:string}>
		 */
		public static function seiten() {
			$seiten = array(
				array(
					'slug' => self::PAGE,
					'cap'  => PACIOLI_CAP,
					'text' => __( 'My AIs', 'cluster-one-pacioli' ),
				),
				/* DER ZWEITE REITER, 08.10.2026 — Teil 3 §5a.
				 *
				 * Der BETREIBER liest hier, wer eingerichtet ist und bei
				 * wem. Nicht womit. CV_CAP und nicht manage_options, weil
				 * Christian die Lockerung ausdruecklich entschieden hat —
				 * und weil der Satz auf „Meine KIs" dazu praezisiert
				 * wurde, statt stillschweigend falsch zu werden. */
				array(
					'slug' => self::PAGE_AUSSTATTUNG,
					'cap'  => PACIOLI_CAP,
					'text' => __( 'Equipment', 'cluster-one-pacioli' ),
				),
			);

			if ( class_exists( 'Pacioli_Selftest' ) ) {
				$seiten[] = array(
					'slug' => Pacioli_Selftest::PAGE,
					'cap'  => Pacioli_Selftest::CAP,

					/* DAS ZWEITE SCHLOSS AN DERSELBEN TUER — Teil 3 §5b.
					 *
					 * manage_options ODER cv_see_internals. Dein Kopf sagte
					 * „+", deine Detailtabelle nur „cv_see_internals";
					 * entschieden ist das ODER.
					 *
					 * UND DAS IST KEINE RECHTEAUSWEITUNG: manage_options
					 * konnte diese Seite schon immer. Das Neue ist, dass
					 * ein Nicht-Administrator duerfen KANN — Voreinstellung
					 * NIEMAND.
					 *
					 *     Ein Schloss mit zwei Schluesseln ist kein
					 *     offenes Schloss. Ein Schloss, das dem Hausherrn
					 *     nicht aufgeht, ist ein verlegter Schluessel.
					 *
					 * DIE ENTSCHEIDUNG STEHT NICHT HIER, sondern in
					 * CV_Selftest::darf_lesen() — zusammen mit dem Riegel
					 * im Rueckruf und mit der Anmeldung. Ein ODER, das in
					 * dieser Liste nachgebaut waere, waere eine zweite
					 * Fassung derselben Bedingung.              (P1-12) */
					'darf' => array( 'Pacioli_Selftest', 'darf_lesen' ),
					'text' => __( 'For developers', 'cluster-one-pacioli' ),
				);
			}

			return $seiten;
		}

		/**
		 * Darf dieser Betrachter diesen Reiter sehen?
		 *
		 * EINE Stelle, die aus einem Seiteneintrag ein Ja oder Nein macht —
		 * gelesen von der Reiterleiste UND von der Anmeldung. Zwei Leser,
		 * eine Wahrheit.                                           (P1-12)
		 *
		 *     Ein Reiter, den man sieht und nicht darf, ist eine Auskunft
		 *     ueber etwas, das einen nichts angeht. Einer, den man darf und
		 *     nicht sieht, ist eine verschlossene Tuer ohne Schild.
		 */
		public static function darf( array $seite ) {
			if ( isset( $seite['darf'] ) && is_callable( $seite['darf'] ) ) {
				return (bool) call_user_func( $seite['darf'] );
			}

			return current_user_can( $seite['cap'] );
		}

		/**
		 * Zeichnet die Reiter — EINE Schreibstelle fuer BEIDE Seiten.
		 *
		 * WARUM SIE HIER WOHNT UND NICHT ZWEIMAL: zwei Leisten waeren zwei
		 * Autoren derselben Wahrheit („welche Seiten gehoeren zusammen"), und
		 * die erste Abweichung zwischen ihnen faellt niemandem auf, weil
		 * beide fuer sich richtig aussehen.
		 *
		 *     P1-12: keine zwei Schreibwege auf eine Wahrheit.
		 *
		 * ---------------------------------------------------------------------
		 * DAS SCHLOSS, UND ES IST DER GRUND, WARUM DIESE METHODE NICHT DREI
		 * ZEILEN LANG IST
		 *
		 * Die zwei Seiten haben VERSCHIEDENE Schloesser:
		 *
		 *     cv-my-ais     CV_CAP            = 'use_own_ai_connector'
		 *     cv-selftest   CV_Selftest::CAP  = 'manage_options'
		 *
		 * `manage_options` ist enger. Ein Reiter, der einfach gezeichnet
		 * wird, weil der Betrachter auf DIESER Seite stehen darf, waere von
		 * jedem zu sehen, der `CV_CAP` hat — und das sind mehr Leute.
		 *
		 *     Ein Reiter, den man sieht und nicht darf, ist eine Auskunft
		 *     ueber etwas, das einen nichts angeht. Und ein Umzug, der ein
		 *     Schloss von der Tuer nimmt, ist keine Umstellung. Er ist eine
		 *     Rechteausweitung mit Moebeln.                     (Mammut, 52 §4)
		 *
		 * DESHALB TRAEGT JEDER REITER SEIN EIGENES SCHLOSS und wird nur
		 * gezeichnet, wenn der Betrachter es aufschliessen kann. NICHT
		 * ausgegraut — nicht da.
		 *
		 * UND ZWAR IN BEIDE RICHTUNGEN. Mammut hat den einen Fall bestellt
		 * (Selbsttest-Reiter ohne `manage_options`). Der umgekehrte ist
		 * derselbe Satz: wer `manage_options` hat, aber nicht `CV_CAP`, steht
		 * auf der Selbsttestseite und saehe einen Reiter „Meine KIs", der
		 * ihn auf ein `wp_die()` fuehrt. Ihn zu zeichnen kostete eine
		 * ZUSAETZLICHE Zeile (eine Ausnahme fuer den eigenen Reiter) — die
		 * Regel gilt hier fuer beide, weil sie EINE Regel ist.
		 *
		 * WAS SIE NICHT TUT: entscheiden, wer hindarf. Sie liest die zwei
		 * Schloesser dort, wo sie wohnen, und zeichnet danach. Der Riegel
		 * sitzt in beiden Rueckrufen und bleibt dort.
		 *
		 *     Ein Eintrag darf sagen, wo etwas liegt. Er darf nicht
		 *     entscheiden, wer hindarf.                          (Hollerith)
		 *
		 * BENANNTE GRENZE: faellt `CV_Selftest` aus (Datei fehlt, Klasse
		 * nicht gebunden), gibt es den zweiten Reiter nicht — und das ist
		 * richtig so. Ein Reiter auf eine Seite, die niemand angemeldet hat,
		 * ist genau der Fund, den der Boden bei Kleinrocks Kalender
		 * aufgeschrieben hat: „zeigte auf sie und ins Leere".
		 *
		 * WO DIE LISTE HERKOMMT: aus `seiten()`, oben. Seit dem 18.09.2026
		 * liest sie ausser dieser Methode auch der Sammler im Lader — und
		 * nur DIESE Methode filtert nach Capability. Der Sammler fragt, wem
		 * die Seite gehoert, und das haengt nicht am Betrachter.
		 *
		 * @param string $aktiv Slug der Seite, die gerade gezeichnet wird.
		 */
		public static function reiter( $aktiv ) {
			/* 0.8.37 (28.10.2026, Neo): dasselbe Gehaeuse wie der Boden
			 * (kern.php, cluster_one_render_tabs) — bis 0.8.36 stand hier
			 *     echo '<h2 class="nav-tab-wrapper">';
			 * Seit Boden 1.18.0 steht diese Leiste auf den Knoten-Seiten
			 * UEBER der Knoten-Reihe, und Nemesus will sie dort „wie die
			 * anderen gestaltet": gleicher Block, gleicher Abstand darunter. */
			/* 0.10.0 (03.11.2026, Neo), Runde Phi — DER BODEN ZEICHNET. Nemesus: Pacioli bekommt den ganzen Boden.
			 * Bis 0.9.6 zeichnete diese Methode die Leiste selbst — <nav class="nav-tab-wrapper wp-clearfix">, printf je
			 * Seite aus seiten()/darf() — und haengte „Knoten" (0.8.35: nur wenn cluster_one_render_settings_page da ist;
			 * 0.8.36: Adresse mit Herkunft, aktiv auf Knoten-Seiten) und „Feynman" (0.9.5: drei Riegel menue_wenn,
			 * feynman_seiten, organ_wirt('feynman'); 0.9.6: hinter Knoten) mit eigenen function_exists-Riegeln an, weil
			 * kern.php nicht im Paket lag. Jetzt liegt es: die LISTE bleibt hier (seiten(), darf() — Schloesser je Reiter),
			 * das ZEICHNEN tut cluster_one_render_tabs() wie bei jedem Haus und haengt Knoten und Feynman selbst an, am
			 * Platz, den Nemesus bestimmt hat. Der Rueckfall unten ist die Form von Post Dolly (R-LEIHE-1): faellt der
			 * Boden doch aus, stehen die Reiter als schlichte Verweise da statt gar nicht. */
			$c1_tabs = array();
			foreach ( self::seiten() as $r ) {
				if ( ! self::darf( $r ) ) {
					continue;
				}
				$c1_tabs[] = array(
					'label'  => $r['text'],
					'url'    => admin_url( 'admin.php?page=' . $r['slug'] ),
					'active' => $r['slug'] === $aktiv,
				);
			}
			if ( function_exists( 'cluster_one_render_tabs' ) ) {
				cluster_one_render_tabs( $c1_tabs );
				return;
			}
			echo '<p>';
			foreach ( $c1_tabs as $c1_t ) {
				if ( ! empty( $c1_t['active'] ) ) {
					echo '<strong>' . esc_html( $c1_t['label'] ) . '</strong> ';
					continue;
				}
				echo '<a href="' . esc_url( $c1_t['url'] ) . '">' . esc_html( $c1_t['label'] ) . '</a> ';
			}
			echo '</p>';
		}

		// =====================================================================
		//  „Meine KIs“
		// =====================================================================

		public static function render_my_ais() {
			/* DER EIGENE RIEGEL — freigegeben 16.09.2026, PROMPT 51 §1.
			 *
			 *     Ein Eintrag darf sagen, wo etwas liegt. Er darf nicht
			 *     entscheiden, wer hindarf.                    (Mammut)
			 *
			 * Bis heute hat WordPress das fuer diese Seite getan: wer sie
			 * anmeldet, gibt die Capability mit, und WordPress prueft sie
			 * vor jedem Aufruf. Der Riegel war da — er stand nur nicht hier.
			 * Seit das Dach anmeldet, reist er durch eine fremde Hand.
			 *
			 *     Ein Riegel, den ein anderer fuer mich setzt, ist so gut
			 *     wie seine Zustellung.
			 *
			 * Dieselbe Gestalt wie die vier handle_*-Methoden weiter unten.
			 * Eine fuenfte Schreibweise fuer denselben Riegel waere eine
			 * fuenfte Wahrheit. */
			if ( ! current_user_can( PACIOLI_CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			$me         = get_current_user_id();
			$identities = Pacioli_Identities::owned_by( $me );
			$providers  = Pacioli_Marker::providers();
			$quarantine = Pacioli_Vault::read_shielded( $me, PACIOLI_META_QUARANTINE );
			$quarantine = is_array( $quarantine ) ? $quarantine : array();
			?>
			<div class="wrap">
				<h1><?php echo esc_html( self::ueberschrift() );
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- self::blase() gibt fertiges HTML zurueck (esc_html im Innern).
					echo self::blase( __( 'My AIs: here you deposit the access keys of the AIs you pay for yourself. A key goes into the vault and never leaves it; the node uses it on your behalf, and only you can read or change it. Nobody else on the node — not even the SysOp — can commission an AI with your key. Named after Luca Pacioli, who wrote down double-entry bookkeeping in 1494: every entry has a counter-entry, and nothing disappears unaccounted for.', 'cluster-one-pacioli' ) ); ?></h1>
				<?php /* 0.8.34 (27.10.2026, Neo): der Stempel – Fassung, Werft, Boden-Stand, aus den
				   eigenen Konstanten (cluster-one-pacioli.php), weil Pacioli kern.php nicht traegt.
				   0.10.0 (03.11.2026, Neo), Runde Phi: kern.php liegt jetzt im Paket — der Stempel kommt wie bei jedem Haus aus
				   cluster_one_stempel() (Register: werft, boden; Fassung aus dem Kopf). Bis 0.9.6 stand hier ein eigener <p> mit
				   PACIOLI_VERSION, sprintf('shipyard: %s', PACIOLI_WERFT) und sprintf('built on floor %s', PACIOLI_BODEN_GEBAUT):
				   dieselben Werte aus derselben Anmeldung, nur die Schreibstelle ist eine weniger. */ ?>
				<?php if ( function_exists( 'cluster_one_stempel' ) ) { echo cluster_one_stempel( PACIOLI_FILE, PACIOLI_VERSION ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapt in der Funktion (cluster_one_stempel: esc_html im Innern) ?>

				<?php self::reiter( self::PAGE ); ?>

				<p style="max-width:48em">
					<?php esc_html_e( 'Each AI works only with the access key you store here. You pay only for what your own AIs use — and nobody else can task them.', 'cluster-one-pacioli' ); ?>
				</p>

				<p style="max-width:48em">
					<?php esc_html_e( 'If a provider settings page says "not connected", that is not a fault: a check made from there belongs to nobody, so no key is inserted. Whether your key works is answered by the "Check key" button below — it makes a very short request on your own bill.', 'cluster-one-pacioli' ); ?>
				</p>

				<div class="notice notice-info inline" style="max-width:48em">
					<p>
						<strong><?php esc_html_e( 'Before you enter a key:', 'cluster-one-pacioli' ); ?></strong><br>
						<?php esc_html_e( 'The keys are stored encrypted on this server. That protects them against a database dump and against other plugins — not against the operator of the installation, who has server access. So create a separate key at the provider for this site only, and set a spending limit there. Then the worst case has a ceiling.', 'cluster-one-pacioli' ); ?>
					</p>
				</div>

				<p style="max-width:48em;color:#555">
					<?php echo esc_html( Pacioli_Crypto::tier_label() ); ?>
				</p>

				<?php
				$checked = isset( $_GET['checked'] ) ? sanitize_key( wp_unslash( $_GET['checked'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

				if ( 'ok' === $checked ) :
					?>
					<div class="notice notice-success inline" style="max-width:48em">
						<p><?php esc_html_e( 'The key works. The provider answered.', 'cluster-one-pacioli' ); ?></p>
					</div>
				<?php elseif ( 'failed' === $checked ) : ?>
					<div class="notice notice-error inline" style="max-width:48em">
						<p><?php esc_html_e( 'The provider rejected the key. The AI is suspended for now; a new key lifts that.', 'cluster-one-pacioli' ); ?></p>
					</div>
				<?php elseif ( 'unavailable' === $checked ) : ?>
					<div class="notice notice-warning inline" style="max-width:48em">
						<p><?php esc_html_e( 'It could not be checked — no AI client is available on this installation.', 'cluster-one-pacioli' ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( ! Pacioli_Crypto::has_key() ) : ?>
					<div class="notice notice-error inline" style="max-width:48em">
						<p><?php esc_html_e( 'The installation is not set up yet — the encryption key is missing. Please contact the administrator.', 'cluster-one-pacioli' ); ?></p>
					</div>
					</div>
					<?php
					return;
				endif;
				?>

				<?php
				// 04.09.2026, H210 — DIE REIHENFOLGE DER SEITE WIDERSPRACH DER
				// REIHENFOLGE DER ARBEIT.
				//
				//   auf der Seite            in Wirklichkeit
				//   1 Sichergestellte …      3 Schluessel zuordnen
				//   2 Vorhandene KIs         2 (Ergebnis)
				//   3 KI anlegen             1 KI anlegen
				//
				// Bei null Identitaeten sind alle Auswahllisten oben leer, und
				// kein Satz sagte, was zuerst zu tun ist. Ein Admin, der oben
				// anfaengt, findet dreimal nichts und haelt die Seite fuer
				// kaputt — genau das ist am 03.09. passiert.
				//
				//     Eine Seite, deren Reihenfolge der Arbeitsreihenfolge
				//     widerspricht, lehrt den Benutzer, dass er etwas falsch
				//     macht.
				//
				// Der Block steht deshalb VOR den anderen und nur dann, wenn er
				// gebraucht wird. Sobald eine KI da ist, ist er weg — ein
				// Wegweiser, der stehen bleibt, nachdem man angekommen ist, ist
				// bald wieder Lärm.
				?>
				<?php if ( ! $identities ) : ?>
					<div class="notice notice-info inline" style="max-width:48em">
						<p><strong><?php esc_html_e( 'Start here', 'cluster-one-pacioli' ); ?></strong></p>
						<p>
							<?php esc_html_e( 'There is no AI yet, and without one nothing on this page can be assigned. The work runs in the opposite direction to the page: first create an AI at the bottom, then a key can be assigned to it here, then it can be checked.', 'cluster-one-pacioli' ); ?>
						</p>
						<?php if ( $quarantine ) : ?>
							<p>
								<?php
								printf(
									/* translators: %s: Liste der Anbieter, fuer die Schluessel in Verwahrung liegen. */
									esc_html__( 'Keys are already in safekeeping for: %s. The provider of the new AI must match one of them, otherwise its key cannot be assigned to it.', 'cluster-one-pacioli' ),
									esc_html( implode( ' · ', array_keys( $quarantine ) ) )
								);
								?>
							</p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php
				/* ===============================================================
				 *  POSTEN B7 — NIEMAND WIRD UEBERRASCHT, UND ZWAR DAUERHAFT
				 * ===============================================================
				 *
				 *  Dieser Satz steht IMMER da, nicht nur beim Entfernen. Wer
				 *  ihn erst dann liest, wenn das Plugin schon weg ist, liest
				 *  ihn zu spaet — und beim Entfernen liest ihn nur der SysOp.
				 *
				 *      Man kann einen Verlust nicht verhindern, der in der
				 *      Hand eines anderen liegt. Man kann ihn nur vorher
				 *      ankuendigen, solange noch Zeit ist, etwas zu
				 *      retten.                                       (Mammut)
				 *
				 *  ER STEHT NUR DA, WENN ETWAS ZU VERLIEREN IST. Ein Satz
				 *  ueber den Verlust von Schluesseln auf einer Seite ohne
				 *  Schluessel ist eine Drohung ohne Gegenstand.
				 * =============================================================== */
				$c1_hat_schluessel = false;

				foreach ( $identities as $c1_id ) {
					if ( Pacioli_Vault::has_secret( is_array( $c1_id ) ? (int) ( $c1_id['id'] ?? 0 ) : (int) $c1_id ) ) {
						$c1_hat_schluessel = true;
						break;
					}
				}
				?>

				<?php if ( $c1_hat_schluessel ) : ?>
					<div class="notice notice-warning inline" style="max-width:48em">
						<p>
							<?php esc_html_e( 'These keys sit on this node. If the operator removes this plugin, they are gone — every one of them, whether or not anything else is kept. Export them if you want to keep them.', 'cluster-one-pacioli' ); ?>
						</p>
					</div>
				<?php endif; ?>

				<?php self::render_ausfuhr( $identities ); ?>

				<?php if ( $quarantine ) : ?>
					<h2><?php esc_html_e( 'Impounded keys', 'cluster-one-pacioli' ); ?></h2>

					<div class="notice notice-warning inline" style="max-width:48em">
						<p>
							<?php esc_html_e( 'These keys were found in a provider setting, where they would have sat in plain text. They have been taken into encrypted safekeeping. Assign them to an AI or discard them — as long as they sit here, they are not used.', 'cluster-one-pacioli' ); ?>
						</p>
					</div>

					<table class="widefat striped" style="max-width:60em">
						<tbody>
						<?php
						foreach ( $quarantine as $provider => $unused ) :
							// H210 — WELCHE KIs KOMMEN FUER DIESEN SCHLUESSEL
							// UEBERHAUPT IN FRAGE?
							//
							// Die Liste filtert nach Anbieter: ein google-Schluessel
							// passt nur zu einer KI, deren Anbieter google ist. Ist
							// keine da, war hier bisher eine Auswahlliste mit einem
							// einzigen Eintrag — „— einer KI zuordnen —" — und einem
							// Knopf, der nichts tun konnte.
							//
							//     Ein Formular, dessen Auswahlliste leer sein kann,
							//     muss sagen, was man tut, wenn sie es ist.
							$passende = array();

							foreach ( $identities as $id ) {
								if ( (string) get_user_meta( $id, PACIOLI_META_PROVIDER, true ) === (string) $provider ) {
									$passende[] = $id;
								}
							}
							?>
							<tr>
								<td style="width:12em"><code><?php echo esc_html( $provider ); ?></code></td>
								<td>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
										<input type="hidden" name="action" value="pacioli_quarantine">
										<input type="hidden" name="provider" value="<?php echo esc_attr( $provider ); ?>">
										<?php wp_nonce_field( 'pacioli_quarantine_' . $provider ); ?>

										<?php if ( ! $passende ) : ?>
											<p style="margin:0 0 .5em">
												<?php
												printf(
													/* translators: %s: Anbieter-ID, z. B. „google". */
													esc_html__( 'There is no AI with the provider "%s" yet, so this key cannot be assigned to anything. Create one at the bottom of this page — the provider must match. The key stays in safekeeping until then; it is not in use.', 'cluster-one-pacioli' ),
													esc_html( $provider )
												);
												?>
											</p>
										<?php else : ?>
											<select name="identity">
												<option value=""><?php esc_html_e( '— assign to an AI —', 'cluster-one-pacioli' ); ?></option>
												<?php
												foreach ( $passende as $id ) :
													$u = get_userdata( $id );
													?>
													<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $u ? $u->display_name : '' ); ?></option>
												<?php endforeach; ?>
											</select>
											<button class="button button-primary" name="do" value="assign"><?php esc_html_e( 'Assign', 'cluster-one-pacioli' ); ?></button>
										<?php endif; ?>

										<button class="button" name="do" value="discard"><?php esc_html_e( 'Discard', 'cluster-one-pacioli' ); ?></button>
									</form>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>

				<h2><?php esc_html_e( 'Existing AIs', 'cluster-one-pacioli' ); ?></h2>

				<?php if ( ! $identities ) : ?>
					<p><em><?php esc_html_e( 'None yet. Create one below.', 'cluster-one-pacioli' ); ?></em></p>
				<?php else : ?>
					<table class="widefat striped" style="max-width:60em">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Name', 'cluster-one-pacioli' ); ?></th>
								<th><?php esc_html_e( 'Provider', 'cluster-one-pacioli' ); ?></th>
								<th><?php esc_html_e( 'Key', 'cluster-one-pacioli' ); ?></th>
								<th><?php esc_html_e( 'State', 'cluster-one-pacioli' ); ?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ( $identities as $id ) :
							$user   = get_userdata( $id );
							$status = get_user_meta( $id, PACIOLI_META_STATUS, true );
							?>
							<tr>
								<td><strong><?php echo esc_html( $user ? $user->display_name : '' ); ?></strong></td>
								<td><code><?php echo esc_html( get_user_meta( $id, PACIOLI_META_PROVIDER, true ) ); ?></code></td>
								<td>
									<?php echo Pacioli_Vault::has_secret( $id )
										? esc_html__( 'stored', 'cluster-one-pacioli' )
										: '<em>' . esc_html__( 'not stored', 'cluster-one-pacioli' ) . '</em>'; ?>
								</td>
								<td>
									<?php
									if ( PACIOLI_STATUS_ACTIVE === $status ) {
										esc_html_e( 'active', 'cluster-one-pacioli' );
									} else {
										echo esc_html( Pacioli_Vault::status_message( $status ) );
									}
									?>
								</td>
								<td>
									<?php if ( Pacioli_Vault::has_secret( $id ) ) : ?>
										<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline">
											<input type="hidden" name="action" value="pacioli_check_key">
											<input type="hidden" name="identity" value="<?php echo esc_attr( $id ); ?>">
											<?php wp_nonce_field( 'pacioli_check_key_' . $id ); ?>
											<button class="button button-small"><?php esc_html_e( 'Check key', 'cluster-one-pacioli' ); ?></button>
										</form>
									<?php endif; ?>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline" onsubmit="return confirm('<?php echo esc_js( __( 'Delete this AI together with its stored key?', 'cluster-one-pacioli' ) ); ?>');">
										<input type="hidden" name="action" value="pacioli_delete_identity">
										<input type="hidden" name="identity" value="<?php echo esc_attr( $id ); ?>">
										<?php wp_nonce_field( 'pacioli_delete_identity_' . $id ); ?>
										<button class="button-link delete"><?php esc_html_e( 'Delete', 'cluster-one-pacioli' ); ?></button>
									</form>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>

					<?php
					// 05.09.2026, H215 — DIE ZWEITE SEITE, DIE ES GIBT.
					//
					// Diese Seite legt Identitaeten an und verwahrt ihre
					// Schluessel. WELCHE von ihnen fuer den KNOTEN spricht, wird
					// woanders entschieden: im Kranz, eine Zeile je Anbieter.
					// Bis heute sagte keine der beiden Seiten, dass es die
					// andere gibt.
					//
					//     Ein Angebot ohne Weg — zum vierten Mal in vier Tagen,
					//     an vier Stellen, von drei Haeusern gebaut. Wenn eine
					//     Fehlerform viermal auftritt, ist sie keine Panne mehr,
					//     sondern eine Gewohnheit: wir bauen Tueren und
					//     schreiben nicht daneben, wohin sie fuehren.
					//
					// NUR WENN ES IDENTITAETEN GIBT: „eine dieser KIs" setzt
					// voraus, dass welche dastehen. Bei null uebernimmt der
					// erste Block aus H210, und zwei Wegweiser zugleich sind
					// einer zu viel.
					//
					// Die zweite Haelfte des Satzes steht im Kranz-Formular und
					// gehoert der Werkbank. Ein Weg, den nur eine Seite
					// beschreibt, ist eine Einbahnstrasse.
					?>
					<p style="max-width:48em;margin-top:.5em">
						<?php esc_html_e( 'These AIs exist and hold their keys. Which of them speaks for the node is decided elsewhere: to have one of them do that, enter it under Cluster One → AI identities — one row per provider.', 'cluster-one-pacioli' ); ?>
					</p>

					<?php
					// 05.09.2026 — DIE KONTEN, DIE ES GIBT UND DIE MAN NICHT SIEHT.
					//
					// Jede Zeile dieser Tabelle IST ein WordPress-Benutzerkonto.
					// Und `CV_Identities::hide_from_user_list()` blendet sie aus
					// `Benutzer -> Alle Benutzer` aus — mit Absicht, damit sie
					// nicht in jeder Autoren-Auswahlbox auftauchen.
					//
					//     Ein Konto, das absichtlich unsichtbar ist, sieht aus wie
					//     ein Konto, das es nicht gibt. Der Unterschied stand in
					//     keiner Zeile, die ein Mensch liest.       (Mammut)
					//
					// Dieselbe Form ein fuenftes Mal — H198, H208, H210, H215 und
					// hier. Die Werkbank hat den Satz in ihr Kranz-Formular
					// gesetzt; er fehlte aber DORT, wo die Konten aufgelistet
					// sind, und wo jemand sich fragt, warum er sie nebenan nicht
					// findet.
					//
					// DIE BEDINGUNG: nur fuer den, der die Benutzerliste
					// ueberhaupt aufmachen darf. Ein Weg, den man nicht gehen
					// darf, ist kein Weg.
					if ( current_user_can( 'list_users' ) ) :
						?>
						<p style="max-width:48em;color:#555">
							<?php
							printf(
								/* translators: %s: Link auf die Benutzerliste mit dem Anhaengsel cv_show_ai=1. */
								wp_kses_post( __( 'Each of these is a real user account, hidden from the normal user list on purpose — otherwise they would appear in every author dropdown. To see them there anyway: %s', 'cluster-one-pacioli' ) ),
								'<a href="' . esc_url( admin_url( 'users.php?pacioli_show_ai=1' ) ) . '"><code>users.php?pacioli_show_ai=1</code></a>'
							);
							?>
						</p>
					<?php endif; ?>
				<?php endif; ?>

				<h2><?php esc_html_e( 'Enter or replace a key', 'cluster-one-pacioli' ); ?></h2>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="pacioli_save_identity">
					<?php wp_nonce_field( 'pacioli_save_identity' ); ?>

					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="cv-identity"><?php esc_html_e( 'AI', 'cluster-one-pacioli' ); ?></label></th>
							<td>
								<select name="identity" id="cv-identity">
									<option value="new"><?php esc_html_e( '— create a new AI —', 'cluster-one-pacioli' ); ?></option>
									<?php foreach ( $identities as $id ) :
										$user = get_userdata( $id ); ?>
										<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $user ? $user->display_name : '' ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="cv-name"><?php esc_html_e( 'Name', 'cluster-one-pacioli' ); ?></label></th>
							<td>
								<input type="text" name="name" id="cv-name" class="regular-text" placeholder="<?php esc_attr_e( 'e.g. Claude', 'cluster-one-pacioli' ); ?>">
								<p class="description"><?php esc_html_e( 'The AI appears in the room under this name — together with your name as its owner.', 'cluster-one-pacioli' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="cv-provider"><?php esc_html_e( 'Provider', 'cluster-one-pacioli' ); ?></label></th>
							<td>
								<select name="provider" id="cv-provider">
									<?php foreach ( $providers as $p ) : ?>
										<option value="<?php echo esc_attr( $p ); ?>"><?php echo esc_html( $p ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="cv-model"><?php esc_html_e( 'Model', 'cluster-one-pacioli' ); ?></label></th>
							<td><input type="text" name="model" id="cv-model" class="regular-text"></td>
						</tr>
						<tr>
							<th scope="row"><label for="cv-secret"><?php esc_html_e( 'Access key', 'cluster-one-pacioli' ); ?></label></th>
							<td>
								<input type="password" name="secret" id="cv-secret" class="regular-text" autocomplete="new-password" spellcheck="false">
								<p class="description">
									<?php esc_html_e( 'Stored encrypted and never shown again. Leaving it empty means: unchanged.', 'cluster-one-pacioli' ); ?>
								</p>
							</td>
						</tr>
					</table>

					<?php submit_button( __( 'Save', 'cluster-one-pacioli' ) ); ?>
				</form>

				<?php
				// 04.09.2026, H211 — DIE SEITE SAGTE NICHT, WIE ANDERE SIE BEKOMMEN.
				//
				// Ein Admin, dessen ganzer Plan „ich weise sie spaeter zu" ist,
				// sucht den Weg dort, wo die Sache steht — also hier. Er steht
				// aber in der Benutzerliste, und auf dieser Seite stand kein
				// Wort davon. Am 03.09. hat genau das jemanden glauben lassen,
				// es sei nichts gebaut.
				//
				//     Eine Seite, die eine Berechtigung voraussetzt, muss sagen,
				//     wo diese Berechtigung vergeben wird. Sonst ist sie fuer
				//     alle ausser dem ersten Benutzer unsichtbar.
				//
				// DIE BEDINGUNG: nur fuer den, der es auch TUN kann. Wer
				// `promote_users` nicht hat, bekaeme sonst einen Weg beschrieben,
				// den er nicht gehen darf — dieselbe Familie wie H198 und H208,
				// nur andersherum: eine Anleitung ohne Recht statt eines
				// Angebots ohne Weg.
				if ( current_user_can( 'promote_users' ) ) :
					?>
					<h2><?php esc_html_e( 'How other people get this page', 'cluster-one-pacioli' ); ?></h2>

					<p style="max-width:48em">
						<?php esc_html_e( 'This page belongs to whoever is allowed to run their own AIs. That is granted per person, not per role — so it works alongside any role plugin and needs no list of its own.', 'cluster-one-pacioli' ); ?>
					</p>

					<p style="max-width:48em">
						<?php
						/* TEIL 4, 08.10.2026 — DER TEXT NANNTE DIE HALBE SACHE.
						 *
						 * `cv_revoke` ist seit Monaten gebaut (:1385,
						 * remove_cap) und stand in keinem Satz. Christian hat
						 * deshalb gefragt, ob man den Zugriff wieder nehmen
						 * kann. Die Antwort war ja, und die Seite sagte es
						 * nicht.
						 *
						 *     Ein Text, der die Haelfte nennt, ist eine
						 *     Auskunft, die ein Werkzeug versteckt.
						 *
						 * Und der letzte Satz ist nicht Beiwerk: was man
						 * zurueckgeben kann, gibt man leichter. Wer nicht
						 * weiss, dass es einen Rueckweg gibt, vergibt
						 * vorsichtiger als noetig. */
						printf(
							/* translators: 1: Link zur Benutzerliste, 2: Spaltenname, 3: Name der Massenaktion „erlauben", 4: Name der Massenaktion „entziehen". */
							wp_kses_post( __( 'Go to %1$s. The column %2$s shows who has it; the bulk action %3$s grants it to everyone selected, and %4$s takes it back again. AI accounts themselves are refused — an AI cannot own an AI.', 'cluster-one-pacioli' ) ),
							'<a href="' . esc_url( admin_url( 'users.php' ) ) . '">' . esc_html__( 'Users → All Users', 'cluster-one-pacioli' ) . '</a>',
							'<strong>' . esc_html__( 'Own AIs', 'cluster-one-pacioli' ) . '</strong>',
							'<strong>' . esc_html__( 'Allow own AIs', 'cluster-one-pacioli' ) . '</strong>',
							'<strong>' . esc_html__( 'Withdraw own AIs', 'cluster-one-pacioli' ) . '</strong>'
						);
						?>
					</p>

					<p style="max-width:48em;color:#555">
						<?php
						/* TEIL 3 §3a, 08.10.2026 — DER SATZ WIRD PRAEZISIERT.
						 *
						 * Bis heute stand hier „Nobody sees anybody else's
						 * AIs on this page — not even you." Das war fuer
						 * die SCHLUESSEL richtig und als Satz zu weit: die
						 * neue Tafel zeigt, WER eingerichtet ist und BEI
						 * WEM.
						 *
						 * DIE LOCKERUNG IST CHRISTIANS ENTSCHEIDUNG und
						 * ausdruecklich gegeben: „Man sieht auch, wer
						 * welche Connectoren in Anspruch nimmt, nur eben
						 * nicht die Schluessel."
						 *
						 *     Ein Satz, der mehr zusagt als die Seite
						 *     haelt, wird nicht dadurch wahr, dass die
						 *     Liste daneben auf einem anderen Reiter
						 *     steht.
						 *
						 * Die andere Haelfte steht auf der Tafel selbst.
						 * Zwei Saetze, eine Zusage — und beide nennen
						 * dieselbe Grenze: der SCHLUESSEL. */
						printf(
							/* translators: %s: Name des Reiters mit der Tafel. */
							esc_html__( 'Everyone who has it keeps their own keys and their own bill. Nobody sees anybody else\'s ACCESS KEYS — not even you. Who has set one up at all, and with which provider, is shown under %s.', 'cluster-one-pacioli' ),
							esc_html( self::reitername( self::PAGE_AUSSTATTUNG ) )
						);
						?>
					</p>

					<?php
					/* UND DIE ZWEITE CAPABILITY BEKOMMT IHREN ABSATZ.
					 *
					 * Ich habe sie heute gebaut und den Satz dazu zuerst
					 * nicht geschrieben — W8 des eigenen Zeugen hat es
					 * gefunden, eine Runde nachdem derselbe Zeuge fuer
					 * genau diesen Fehler gebaut wurde.
					 *
					 *     Eine Zeile, die den eigenen Fehler eine Runde
					 *     spaeter noch einmal findet, ist mehr wert als
					 *     die Durchsicht, die ihn einmal gefunden haette.
					 */
					?>
					<h2><?php esc_html_e( 'Who may read the developer info', 'cluster-one-pacioli' ); ?></h2>

					<p style="max-width:48em">
						<?php
						printf(
							/* translators: 1: Name des Reiters, 2: Massenaktion „erlauben", 3: Massenaktion „entziehen". */
							wp_kses_post( __( 'The %1$s tab holds the checks and the notes on building with this plugin. Administrators see it always. For anybody else, the bulk action %2$s grants it and %3$s takes it back — in the same user list, next to the column of the same name.', 'cluster-one-pacioli' ) ),
							'<strong>' . esc_html( class_exists( 'Pacioli_Selftest' ) ? self::reitername( Pacioli_Selftest::PAGE ) : '' ) . '</strong>',
							'<strong>' . esc_html__( 'Allow developer info', 'cluster-one-pacioli' ) . '</strong>',
							'<strong>' . esc_html__( 'Withdraw developer info', 'cluster-one-pacioli' ) . '</strong>'
						);
						?>
					</p>

					<p style="max-width:48em;color:#555">
						<?php esc_html_e( 'Nobody has it by default, and it requires permission to run own AIs — granting it to somebody without that is refused and says so. A node where people write short stories needs own AIs for everyone and the build instructions for nobody.', 'cluster-one-pacioli' ); ?>
					</p>
				<?php endif; ?>

				<?php self::render_orphans(); ?>
				<?php self::render_removal_choice(); ?>
			</div>
			<?php
		}

		/**
		 * Wo die Betriebszahlen stehen — GELESEN, nicht getippt.
		 *
		 * Derselbe Bau wie `wo_pruefungen()` (Posten 1). Benannter
		 * Rueckfall: gibt es den Reiter nicht, nennt die Adresse nur den
		 * Ort.                                                (R-LEIHE-1)
		 */
		public static function wo_betrieb() {
			$reiter = self::reitername( self::PAGE_AUSSTATTUNG );

			if ( '' === $reiter ) {
				return self::wo();
			}

			return self::wo() . ' → ' . $reiter;
		}

		/**
		 * „Was der Betrieb sagt" — umgezogen vom Selbsttest, 08.10.2026.
		 *
		 * =====================================================================
		 *  WARUM manage_options UND NICHT CV_CAP
		 * =====================================================================
		 *
		 *  Die Tafel darueber zeigt, WER eingerichtet ist — das hat Christian
		 *  ausdruecklich geloest. Diese Zahlen sind etwas anderes: sie nennen
		 *  Kennungen, Anbieter und Zeitpunkte von AUFRUFEN. Das ist der
		 *  Betrieb eines Knotens und nicht die Ausstattung seiner Leute.
		 *
		 *      Wer eingerichtet ist, darf der Betreiberkreis sehen. Was
		 *      wann hinausgegangen ist, ist eine Zeile enger.
		 *
		 *  Der Abschnitt stand bis heute auf der Selbsttestseite, die
		 *  ebenfalls manage_options war. Es ist also kein engerer und kein
		 *  weiterer Riegel — es ist derselbe, an einem Ort, an dem er
		 *  hingehoert.
		 */
		public static function render_betrieb() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			?>
				<?php
				$leaks   = Pacioli_Marker::leaks();
				$unbound = Pacioli_Marker::unbound();

				// H192 — der Nenner steht ZUERST, weil die Zahl darunter ohne
				// ihn nichts bedeutet. Wer die Reihenfolge umdreht, laedt den
				// Leser ein, die Null zu lesen und weiterzublaettern.
				$gebunden = Pacioli_Marker::bound_calls();
				$nenner   = (int) $gebunden['count'];
				?>
				<h2><?php echo esc_html( Pacioli_Selftest::abschnitt_betrieb() ); ?></h2>

				<table class="widefat striped" style="max-width:64em">
					<tbody>
						<tr>
							<td style="width:24em"><strong><?php esc_html_e( 'Outgoing calls seen on behalf of an AI', 'cluster-one-pacioli' ); ?></strong></td>
							<td style="width:6em;font-variant-numeric:tabular-nums"><?php echo esc_html( $nenner ); ?></td>
							<td>
								<?php
								echo $nenner
									? esc_html__( 'Each of these passed the bolt on its way out. This is the number the figure below needs in order to be a statement at all.', 'cluster-one-pacioli' )
									: esc_html__( 'Not one outgoing call has been made on behalf of a bound identity yet. Until that happens, the figure below cannot say anything — and it says so instead of saying zero.', 'cluster-one-pacioli' );
								?>
							</td>
						</tr>
						<tr>
							<td style="width:24em"><strong><?php esc_html_e( 'Failed key swaps', 'cluster-one-pacioli' ); ?></strong></td>
							<td style="width:6em;font-variant-numeric:tabular-nums">
								<?php
								echo $nenner
									? esc_html( count( $leaks ) )
									: '<span style="color:#7A7A7A">&ndash;</span>';   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</td>
							<td>
								<?php
								if ( ! $nenner ) {
									esc_html_e( 'Not measured. This counter only ever rises when a call actually goes out — with no outgoing call, its zero would separate "never went wrong" from "never attempted" not at all. A zero that cannot tell those apart is an absence of noise with a number in front of it.', 'cluster-one-pacioli' );
								} elseif ( $leaks ) {
									esc_html_e( 'The swap did not take hold although someone was bound. No bill was charged — but something is wrong here.', 'cluster-one-pacioli' );
								} else {
									printf(
										/* translators: %d: Anzahl der gesehenen gebundenen Aufrufe. */
										esc_html__( 'A zero against %d outgoing calls means: the swap works in live operation. That is the answer no check can give.', 'cluster-one-pacioli' ),
										(int) $nenner
									);
								}
								?>
							</td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'Calls without an acting identity', 'cluster-one-pacioli' ); ?></strong></td>
							<td style="font-variant-numeric:tabular-nums"><?php echo esc_html( $unbound['count'] ); ?></td>
							<td>
								<?php esc_html_e( 'An operational figure, not a fault. Every connection check on a provider settings page produces one. A steady tick is normal; only a sudden rise would be interesting.', 'cluster-one-pacioli' ); ?>
							</td>
						</tr>
						<?php
						$ohne_schluessel = Pacioli_Marker::no_secret_calls();
						$ohne_summe      = 0;
						foreach ( $ohne_schluessel as $eintrag ) {
							$ohne_summe += isset( $eintrag['count'] ) ? (int) $eintrag['count'] : 0;
						}
						?>
						<tr>
							<td><strong><?php esc_html_e( 'Calls without a stored key', 'cluster-one-pacioli' ); ?></strong></td>
							<td style="font-variant-numeric:tabular-nums"><?php echo esc_html( $ohne_summe ); ?></td>
							<td>
								<?php
								/*
								 * 11.09.2026 — hier stand `array_filter( $namen )`.
								 *
								 * Es hat den leeren Namen nicht leer gedruckt,
								 * sondern den EINTRAG HERAUSGEWORFEN. Auf
								 * Christians DEV: Zahl 1, Liste 0, „Betrifft: ."
								 *
								 *     Eine Zahl und eine Liste aus derselben
								 *     Quelle, die sich widersprechen, und
								 *     niemand sagt es.
								 *
								 * Bei EINEM Eintrag faellt der leere Punkt auf.
								 * Bei ZWEI steht ein vollstaendig aussehender
								 * Satz da, ein Name, ein Punkt — und daneben
								 * eine Zahl, die etwas anderes sagt. Das ist
								 * der Fall, um den es geht.
								 *
								 * Mammuts Regel vom 10.09.: die Liste hat so
								 * viele Eintraege, wie gezaehlt wurde. Immer.
								 * Wer nicht benannt werden kann, steht trotzdem
								 * drin — mit dem, was von ihm bekannt ist.
								 *
								 * DER ZAEHLEREINTRAG WIRD MITGEGEBEN, nicht nur
								 * die Kennung: `last` und `provider` sind die
								 * einzige Auskunft, die ein geloeschtes Konto
								 * ueberlebt. Ohne sie waere der Eintrag zwar da
								 * und trotzdem stumm.
								 */
								if ( $ohne_schluessel ) {
									/*
									 * DREI GRUPPEN, DREI SAETZE — Posten A,
									 * 10.10.2026, Christians Entscheidung (b),
									 * von Mammut auf DREI geschaerft.
									 *
									 * Hier stand EIN Satz fuer alle:
									 *
									 *     „Affected: %s. Not a fault of the
									 *      node, but an open matter for those
									 *      owners — they should have been told
									 *      privately."
									 *
									 * Er setzt einen Empfaenger voraus. Fuer
									 * eine Kennung, deren Konto es nicht mehr
									 * gibt, ist er nicht ungenau, sondern
									 * falsch.
									 *
									 *     Ein Satz, der einen Empfaenger
									 *     voraussetzt, wird falsch, sobald der
									 *     Gegenstand ein geloeschtes Konto ist.
									 *
									 * DIE GRUPPEN WERDEN GEFRAGT, NICHT HIER
									 * ENTSCHIEDEN: `CV_Identities::in_gruppen()`
									 * ist die eine Stelle, und sie liest
									 * dieselbe Dreiteilung, aus der
									 * `label_or_trace()` seine drei Saetze
									 * nimmt.                          (P1-12)
									 *
									 * GEZAEHLT, NICHT GESCHAETZT: die drei
									 * Zahlen kommen aus derselben Rechnung und
									 * ergeben die Gesamtzahl. Darueber steht
									 * eine Zeile, die es nachrechnet — faellt
									 * sie, steht es da und wird nicht
									 * verschwiegen.
									 *
									 * EINE LEERE GRUPPE DRUCKT KEINEN SATZ.
									 *
									 *     Ein Satz fuer eine leere Gruppe
									 *     behauptet eine Gruppe.
									 */
									$c1_gruppen = Pacioli_Identities::in_gruppen( $ohne_schluessel );

									/*
									 * DIE ZWEITE ZAHL — 11.09.2026.
									 *
									 * Die Zahl in der Spalte nebenan ist die
									 * SUMME DER AUFRUFE. Die Liste darunter ist
									 * eine Liste von KENNUNGEN. Eine Kennung mit
									 * drei verweigerten Aufrufen macht dort „3"
									 * und hier EINEN Namen — richtig, und
									 * trotzdem ungleich.
									 *
									 *     Zwei Zahlen, die verschieden zaehlen,
									 *     sind nicht dadurch gleich, dass man
									 *     sie nebeneinander druckt.
									 *
									 * Die Regel dahinter ist nicht meine: sie
									 * ist Maillarts Punkt 13 — die Felder
									 * heissen, was sie zaehlen. Ein Haus hat
									 * sie gefunden, zwei brauchen sie.
									 */
									printf(
										/* translators: 1: Anzahl der Aufrufe, 2: Anzahl der Kennungen. */
										esc_html__( '%1$d calls from %2$d identities', 'cluster-one-pacioli' ),
										(int) $ohne_summe,
										count( $ohne_schluessel )
									);
									echo '. ';

									/* GRUPPE 1 — es gibt jemanden, dem man es
									 * sagen kann. Nur hier darf ein Satz zum
									 * Handeln auffordern. */
									if ( $c1_gruppen[ Pacioli_Identities::GRUPPE_BESITZER ] ) {
										printf(
											/* translators: 1: Anzahl der Kennungen mit Besitzer, 2: Liste von KI-Bezeichnungen. */
											esc_html( _n(
												'%1$d has an owner: %2$s. Not a fault of the node, but an open matter for that person — they should have been told privately.',
												'%1$d have owners: %2$s. Not a fault of the node, but an open matter for those people — they should have been told privately.',
												count( $c1_gruppen[ Pacioli_Identities::GRUPPE_BESITZER ] ),
												'cluster-one-pacioli'
											) ),
											count( $c1_gruppen[ Pacioli_Identities::GRUPPE_BESITZER ] ),
											esc_html( implode( ', ', $c1_gruppen[ Pacioli_Identities::GRUPPE_BESITZER ] ) )
										);
										echo ' ';
									}

									/* GRUPPE 2 — eine Tatsache ohne Empfaenger.
									 * Dieser Satz fordert NICHTS. */
									if ( $c1_gruppen[ Pacioli_Identities::GRUPPE_KONTO_WEG ] ) {
										printf(
											/* translators: 1: Anzahl der Kennungen ohne Konto, 2: Liste von KI-Bezeichnungen. */
											esc_html( _n(
												'%1$d has no account any more: %2$s. There is nobody to tell. The line is a scar of something that happened, not a task for anybody.',
												'%1$d have no accounts any more: %2$s. There is nobody to tell. These lines are scars of something that happened, not tasks for anybody.',
												count( $c1_gruppen[ Pacioli_Identities::GRUPPE_KONTO_WEG ] ),
												'cluster-one-pacioli'
											) ),
											count( $c1_gruppen[ Pacioli_Identities::GRUPPE_KONTO_WEG ] ),
											esc_html( implode( ', ', $c1_gruppen[ Pacioli_Identities::GRUPPE_KONTO_WEG ] ) )
										);
										echo ' ';
									}

									/* GRUPPE 3 — NICHT GEMESSEN. Dieser Satz
									 * sagt, dass es nicht bekannt ist, und sagt
									 * NICHT, dass niemand da ist. */
									if ( $c1_gruppen[ Pacioli_Identities::GRUPPE_UNBEKANNT ] ) {
										printf(
											/* translators: 1: Anzahl der Kennungen ohne Spur, 2: Liste von KI-Bezeichnungen. */
											esc_html( _n(
												'For %1$d it is not known whether there is still an owner: %2$s. The counter holds no trace of it, so this says nothing either way — neither that somebody is there nor that nobody is.',
												'For %1$d it is not known whether there are still owners: %2$s. The counter holds no trace of them, so this says nothing either way — neither that somebody is there nor that nobody is.',
												count( $c1_gruppen[ Pacioli_Identities::GRUPPE_UNBEKANNT ] ),
												'cluster-one-pacioli'
											) ),
											count( $c1_gruppen[ Pacioli_Identities::GRUPPE_UNBEKANNT ] ),
											esc_html( implode( ', ', $c1_gruppen[ Pacioli_Identities::GRUPPE_UNBEKANNT ] ) )
										);
										echo ' ';
									}

									/*
									 * DIE RECHNUNG WIRD NACHGERECHNET, UND EIN
									 * FEHLBETRAG STEHT DA.
									 *
									 * Mammuts Bedingung (a): acht Kennungen,
									 * drei Zahlen, und die drei ergeben acht.
									 * Eine Bedingung, die nur im Auftrag steht,
									 * ist eine Behauptung ueber den Code.
									 *
									 *     Eine Summe, die niemand nachrechnet,
									 *     ist eine Zusage an die Sorgfalt von
									 *     gestern.
									 *
									 * Dieser Zweig ist nicht erreichbar, solange
									 * in_gruppen() jede Kennung genau einmal
									 * einsortiert. Er steht da, weil „nicht
									 * erreichbar" eine Aussage ueber den
									 * heutigen Code ist und nicht ueber den
									 * naechsten.
									 */
									$c1_summe = count( $c1_gruppen[ Pacioli_Identities::GRUPPE_BESITZER ] )
										+ count( $c1_gruppen[ Pacioli_Identities::GRUPPE_KONTO_WEG ] )
										+ count( $c1_gruppen[ Pacioli_Identities::GRUPPE_UNBEKANNT ] );

									if ( $c1_summe !== count( $ohne_schluessel ) ) {
										printf(
											/* translators: 1: Summe der drei Gruppen, 2: Anzahl der gezaehlten Kennungen. */
											esc_html__( 'The three groups add up to %1$d, but %2$d identities were counted. One of the two figures is wrong, and this says so rather than printing the nicer one.', 'cluster-one-pacioli' ),
											(int) $c1_summe,
											count( $ohne_schluessel )
										);
									}
								} else {
									esc_html_e( 'Nobody acted without having stored something.', 'cluster-one-pacioli' );
								}
								?>
							</td>
						</tr>
					</tbody>
				</table>
			<?php
		}

		// =====================================================================
		//  DIE TAFEL — was auf diesem Knoten eingerichtet ist
		// =====================================================================

		/**
		 * Wer in der Liste steht — und die Seite sagt es selbst.
		 *
		 * =====================================================================
		 *  CV_CAP IST DIE WAHRHEIT, CLUSTER ONE DER ZUSATZ
		 * =====================================================================
		 *
		 *  Dieses Plugin ist eine Grundlage fuer Fremde. Fuer einen Knoten,
		 *  auf dem nur Pacioli liegt, ist der Alleinfall der NORMALfall —
		 *  also ist die Grundmenge CV_CAP und nicht das Dach.
		 *
		 *  WO ES EIN DACH GIBT, kommen die Cluster-One-Leute DAZU. Nicht
		 *  statt: sonst verschwaende die Tafel genau die Leute, um die es
		 *  geht — die, die noch nichts eingerichtet haben.
		 *
		 *  GEMESSEN, NICHT ANGENOMMEN: `cluster_one_can_access()` beantwortet
		 *  eine Frage je Mensch und liefert keine Liste (kern.php :72). Die
		 *  Liste steckt in `cluster_one_whitelist_user_ids()` und
		 *  `cluster_one_main_admin_user_id()`. Beide werden GERIEGELT
		 *  gefragt, und der Rueckfall ist benannt: dann ist die Liste
		 *  CV_CAP, und die Seite schreibt hin, dass sie es ist.
		 *                                                        (R-LEIHE-1)
		 *
		 *      Eine Liste, die nicht sagt, nach welcher Frage sie
		 *      zusammengestellt ist, ist keine Liste, sondern eine
		 *      Behauptung.                                    (Mammut, 5a)
		 *
		 * @return array{ids:int[],frage:string}
		 */
		public static function tafel_leute() {
			$q = new WP_User_Query(
				array(
					'capability' => PACIOLI_CAP,
					'fields'     => 'ID',
					'number'     => 0,
					'orderby'    => 'display_name',
				)
			);

			$ids   = array_map( 'intval', (array) $q->get_results() );
			$frage = __( 'everyone allowed to run their own AIs', 'cluster-one-pacioli' );

			$dazu = array();

			if ( function_exists( 'cluster_one_whitelist_user_ids' ) ) {
				$dazu = array_merge( $dazu, (array) cluster_one_whitelist_user_ids() );
			}

			if ( function_exists( 'cluster_one_main_admin_user_id' ) ) {
				$dazu[] = cluster_one_main_admin_user_id();
			}

			if ( $dazu ) {
				$vorher = count( $ids );
				$ids    = array_values( array_unique( array_merge( $ids, array_map( 'intval', $dazu ) ) ) );

				if ( count( $ids ) !== $vorher || $dazu ) {
					$frage = __( 'everyone allowed to run their own AIs, plus the Cluster One circle', 'cluster-one-pacioli' );
				}
			}

			/* KI-Konten gehoeren nicht in eine Liste von Menschen. */
			$ids = array_values(
				array_filter(
					$ids,
					function ( $id ) {
						$u = get_userdata( $id );

						return $u && ! Pacioli_Identities::is_ai( $u );
					}
				)
			);

			return array(
				'ids'   => $ids,
				'frage' => $frage,
			);
		}

		/**
		 * Die Tafel.
		 *
		 * =====================================================================
		 *  NUR LESEN. Kein Formular, kein Knopf, der schreibt.
		 * =====================================================================
		 *
		 *  `has_secret()` und nicht `secret_for()`: OB, nicht WAS. Der
		 *  Unterschied ist die ganze Zusage dieser Seite, und er steht im
		 *  Aufruf und nicht nur im Satz darueber.
		 *
		 *  NICHT HIER: Modell (eine Vorliebe), Verbrauch (eine Rechnung),
		 *  Schluessel (selbstverstaendlich).
		 *
		 *  EINE SPALTE JE SORTE AUSSTATTUNG, nicht je Anbieter. Heute gibt
		 *  es eine Sorte — den eigenen KI-Schluessel. „Use your own storage"
		 *  kommt als zweite dazu, sobald Christians Server laufen.
		 *
		 *      Eine Tabelle, die nach ihrem ersten Inhalt benannt ist, wird
		 *      beim zweiten umgebaut.                          (Mammut, 5a)
		 */
		public static function render_equipment() {
			if ( ! current_user_can( PACIOLI_CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			$leute = self::tafel_leute();
			?>
			<div class="wrap">
				<h1><?php echo esc_html( self::ueberschrift() );
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- self::blase() gibt fertiges HTML zurueck (esc_html im Innern).
					echo self::blase( __( 'Equipment: the operator’s board. It shows who on this node has set up an AI and at which provider — never the keys, never what was asked. It answers the operator one question: who is equipped, and where. The keys stay with their owners.', 'cluster-one-pacioli' ) ); ?></h1>

				<?php self::reiter( self::PAGE_AUSSTATTUNG ); ?>

				<p style="max-width:48em">
					<strong><?php esc_html_e( 'This page shows WHO is set up and WITH WHOM. Not WITH WHAT.', 'cluster-one-pacioli' ); ?></strong>
					<?php esc_html_e( 'No access key is shown here, and none can be read from here — not even your own. What stands below is whether a key exists, not what it says.', 'cluster-one-pacioli' ); ?>
				</p>

				<p style="max-width:48em;color:#555">
					<?php
					printf(
						/* translators: %s: nach welcher Frage die Liste zusammengestellt ist. */
						esc_html__( 'Who is listed: %s.', 'cluster-one-pacioli' ),
						esc_html( $leute['frage'] )
					);
					?>
				</p>

				<?php
				/* =============================================================
				 *  DIE LEERE LISTE SAGTE ZWEI DINGE — berichtigt am 11.10.2026
				 * =============================================================
				 *
				 *  Hier stand `if ( $anbieter )`, und der leere Fall sagte
				 *  „No AI connector is registered on this node". Das ist eine
				 *  AUSSAGE, und sie stand auch dann da, wenn gar nicht
				 *  gefragt werden konnte.
				 *
				 *      Eine Seite, die aus „ich konnte nicht fragen" ein
				 *      „es gibt keine" macht, beantwortet eine Frage, die
				 *      niemand gestellt hat.
				 *
				 *  `CV_Marker::providers_auskunft()` trennt die vier Faelle
				 *  (Mammuts Entscheidung zu §5(a)). Hier stehen DREI Saetze:
				 *
				 *      live        die Liste
				 *      leer        „es gibt keine"            — gemessen
				 *      sonst       „nicht feststellbar"       — und warum
				 *
				 *  WARUM `zu_frueh` HIER KEINEN EIGENEN SATZ HAT: eine
				 *  Admin-Seite rendert lange nach `init`. Der Fall kann hier
				 *  nicht auftreten, und ein Satz fuer einen Fall, den niemand
				 *  erlebt, ist Ballast, der wie eine Angabe aussieht.
				 *
				 *      Ein Zustand, der nur beim Pruefen entstehen kann,
				 *      gehoert in den Pruefbericht und nicht auf den Schirm.
				 *                                                   (Mammut)
				 *
				 *  Der dritte Zweig faengt ihn trotzdem — als BENANNTER
				 *  Rueckfall, damit kein leerer Absatz entsteht. Welcher der
				 *  zwei Gruende es war, sagt der Pruefbericht.
				 * ============================================================= */
				$pacioli_anbieter_auskunft = Pacioli_Marker::providers_auskunft();
				$anbieter             = $pacioli_anbieter_auskunft['liste'];
				?>
				<h2><?php esc_html_e( 'Available on this node', 'cluster-one-pacioli' ); ?></h2>

				<p style="max-width:48em">
					<?php
					if ( $anbieter ) {
						printf(
							/* translators: %s: Liste der Anbieter-IDs. */
							esc_html__( 'Providers whose key field this plugin manages: %s', 'cluster-one-pacioli' ),
							esc_html( implode( ', ', $anbieter ) )
						);
					} elseif ( 'leer' === $pacioli_anbieter_auskunft['stand'] ) {
						esc_html_e( 'No AI connector is registered on this node. Until one is, nobody can set anything up here.', 'cluster-one-pacioli' );
					} else {
						/* DIE ADRESSE WIRD GELESEN, NICHT GETIPPT.
						 *
						 * Mein erster Entwurf tippte „Tools -> Connector
						 * Vault" in den Satz. `zeuge-wegweiser.php` W1 hat
						 * ihn sofort rot gemeldet, und zwar zu Recht:
						 *
						 *     Ein Satz, der eine Adresse NENNT, ist ein
						 *     Schreibweg auf die Adresse.        (P1-12)
						 *
						 * Die alte Adresse ist am 16.09. weggezogen, und die
						 * drei Saetze, die sie noch tippen, sind genau
						 * deshalb verwaist (B1, B4, B17 im Katalog). Ich
						 * haette sie als vierten wieder eingefuehrt.
						 *
						 *     Eine Adresse, die aus dem Code kommt, ist heute
						 *     richtig und nach dem Umbau auch. Eine getippte
						 *     ist einmal richtig.
						 *
						 * Derselbe Bau wie in CV_Notes und CV_Guard, mit
						 * demselben benannten Rueckfall.      (R-LEIHE-1) */
						printf(
							/* translators: %s: Ort der Pruefungen auf diesem Knoten. */
							esc_html__( 'Whether an AI connector is registered cannot be determined here: this node did not answer the question. That is not the same as "there are none" — the checks under %s say which of the two it is.', 'cluster-one-pacioli' ),
							esc_html(
								method_exists( 'Pacioli_Admin', 'wo_pruefungen' )
									? self::wo_pruefungen()
									: __( 'this plugin\'s own page', 'cluster-one-pacioli' )
							)
						);
					}
					?>
				</p>

				<h2><?php esc_html_e( 'Who is set up', 'cluster-one-pacioli' ); ?></h2>

				<?php
				/* =============================================================
				 *  POSTEN 4, 10.10.2026 — ZWEI WAHRHEITEN MIT EINEM WORT
				 * =============================================================
				 *
				 *  Christian ist darueber gestolpert: hier stand „Zuletzt
				 *  geprueft: nie" fuer vier KIs, und zur selben Stunde trug
				 *  `cluster_one_ai_verified` im Boden vier Zeitstempel VON
				 *  HEUTE. Seine Frage war: „Zuletzt geprueft: Nie, stimmt
				 *  nicht. Oder meint Pruefung etwas anderes als Anwendung?"
				 *
				 *  ES MEINT ETWAS ANDERES, und das war die Antwort — nur
				 *  stand sie nirgends.
				 *
				 *      Zwei Wahrheiten mit demselben Wort sind schlimmer
				 *      als zwei Woerter fuer eine Wahrheit. Die zweite Form
				 *      ist unordentlich, die erste ist eine Falle.  (Mammut)
				 *
				 * -------------------------------------------------------------
				 *  DER UNTERSCHIED, GEMESSEN (kern.php :978–:993)
				 *
				 *      cluster_one_ai_verified   je ANBIETER, knotenweit
				 *                                Zeitstempel des letzten
				 *                                GEGLUECKTEN Aufrufs — egal
				 *                                von wem, egal mit wessen
				 *                                Schluessel
				 *      CV_META_CHECKED           je KI, je Person. Antwort
				 *                                des Anbieters auf DEN
				 *                                EIGENEN Schluessel, auf die
				 *                                eigene Rechnung
				 *
				 *  Beide koennen gleichzeitig wahr sein und sich
				 *  widersprechen: der Knoten hat heute telefoniert, und
				 *  diese Person hat ihren Schluessel noch nie pruefen
				 *  lassen. Genau das war der Fall.
				 *
				 *  P1-12 IST NICHT VERLETZT — es sind zwei Schreibwege auf
				 *  ZWEI Wahrheiten. Verletzt war nur, dass beide „geprueft"
				 *  hiessen und keiner dazusagte, was er meint.
				 *
				 *  DESHALB EIN SATZ UND KEINE ZAHL. Diesen Punkt setzt der
				 *  Boden; ihn hier mit anzuzeigen waere ein zweiter Leser
				 *  fuer eine Angabe, die nicht meine ist — und ein zweiter
				 *  Leser, der sie DEUTEN muesste.
				 *
				 *      Wer eine fremde Zahl anzeigt, uebernimmt ihre
				 *      Bedeutung. Wer sie benennt, nicht.
				 * ============================================================= */
				?>
				<p style="max-width:48em;color:#555">
					<strong><?php esc_html_e( 'What "last checked" means here:', 'cluster-one-pacioli' ); ?></strong>
					<?php esc_html_e( 'that this person had their OWN key tested against the provider, with the "Check key" button, on their own bill. It is empty until somebody presses it.', 'cluster-one-pacioli' ); ?>
				</p>

				<p style="max-width:48em;color:#555">
					<?php esc_html_e( 'It is NOT the same as the connector dot that the surrounding Cluster One installation shows. That dot turns green once any call through that provider has succeeded on this node — whoever made it, with whichever key. So a provider can be green there while everybody here says "never": the node has phoned today, and these people have not had their own keys tested.', 'cluster-one-pacioli' ); ?>
				</p>

				<table class="widefat striped" style="max-width:72em">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Person', 'cluster-one-pacioli' ); ?></th>
							<th><?php esc_html_e( 'AIs', 'cluster-one-pacioli' ); ?></th>
							<th><?php esc_html_e( 'With which providers', 'cluster-one-pacioli' ); ?></th>
							<th><?php esc_html_e( 'Own AI key', 'cluster-one-pacioli' ); ?></th>
							<th><?php esc_html_e( 'Last checked', 'cluster-one-pacioli' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $leute['ids'] as $c1_uid ) : ?>
							<?php
							$mensch = get_userdata( $c1_uid );

							if ( ! $mensch ) {
								continue;
							}

							$kis       = Pacioli_Identities::owned_by( $c1_uid );
							$prov      = array();
							$mit_key   = 0;
							$geprueft  = array( 'when' => 0, 'ok' => false );

							foreach ( $kis as $c1_ki ) {
								$p = Pacioli_Vault::provider_of( $c1_ki );

								if ( '' !== $p ) {
									$prov[ $p ] = 1;
								}

								if ( Pacioli_Vault::has_secret( $c1_ki ) ) {
									$mit_key++;
								}

								$g = get_user_meta( $c1_ki, PACIOLI_META_CHECKED, true );

								if ( is_array( $g ) && (int) ( $g['when'] ?? 0 ) > $geprueft['when'] ) {
									$geprueft = array(
										'when' => (int) $g['when'],
										'ok'   => ! empty( $g['ok'] ),
									);
								}
							}
							?>
							<tr>
								<td><?php echo esc_html( $mensch->display_name ); ?></td>
								<td style="font-variant-numeric:tabular-nums"><?php echo esc_html( count( $kis ) ); ?></td>
								<td>
									<?php
									echo $prov
										? esc_html( implode( ', ', array_keys( $prov ) ) )
										: '<span style="color:#8a8a8a">&ndash;</span>';   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
								</td>
								<td>
									<?php
									if ( ! $kis ) {
										echo '<span style="color:#8a8a8a">' . esc_html__( 'nothing set up yet', 'cluster-one-pacioli' ) . '</span>';
									} elseif ( $mit_key === count( $kis ) ) {
										esc_html_e( 'yes', 'cluster-one-pacioli' );
									} elseif ( 0 === $mit_key ) {
										esc_html_e( 'no', 'cluster-one-pacioli' );
									} else {
										printf(
											/* translators: 1: Anzahl mit Schluessel, 2: Anzahl KIs. */
											esc_html__( '%1$d of %2$d', 'cluster-one-pacioli' ),
											(int) $mit_key,
											count( $kis )
										);
									}
									?>
								</td>
								<td>
									<?php
									if ( $geprueft['when'] ) {
										printf(
											'%s %s',
											esc_html( date_i18n( get_option( 'date_format' ), $geprueft['when'] ) ),
											$geprueft['ok']
												? '<span style="color:#1a7f37">&#10003;</span>'   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												: '<span style="color:#b32d2e">&#10007;</span>'   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										);
									} else {
										echo '<span style="color:#8a8a8a">' . esc_html__( 'never', 'cluster-one-pacioli' ) . '</span>';
									}
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php self::render_equipment_orphans(); ?>
				<?php self::render_betrieb(); ?>
			</div>
			<?php
		}

		/**
		 * Die KIs ohne Besitzer — und warum sie hier label_or_trace() brauchen.
		 *
		 * =====================================================================
		 *  TEIL 3 §3d: DIE EINDEUTIGKEIT HAENGT AM BESITZER
		 * =====================================================================
		 *
		 *  `label()` haengt den Besitzer an den Namen — „Claude · von Anna" —
		 *  und genau das macht zwei „Claude" unterscheidbar. Faellt der
		 *  Besitzer weg, gibt `label()` nur den Anzeigenamen zurueck, und
		 *  zwei verwaiste „Claude" sind auf einer Tafel nicht mehr zu
		 *  trennen.
		 *
		 *      Die Eindeutigkeit haengt am Besitzer. Faellt der weg, faellt
		 *      sie weg.                                       (Mammut, 3d)
		 *
		 *  UND DAS IST KEIN GEDANKENSPIEL: Christian hat heute fuenf
		 *  verwaiste Kennungen auf zwei Knoten gesehen.
		 *
		 *  MEINE ENTSCHEIDUNG: JA, HIER GREIFT `label_or_trace()`. In den
		 *  Zeilen der Menschen nicht — dort gibt es einen Besitzer, also
		 *  ist `label()` eindeutig und kuerzer. Die Spur kommt aus dem
		 *  Zaehler, den der Melder ohnehin fuehrt.
		 *
		 *      Ein Name, der nicht trennt, ist auf einer Liste schlimmer
		 *      als eine Kennung. Die Kennung sagt wenigstens, dass sie
		 *      eine ist.
		 *
		 *  NUR manage_options: diese Zeilen nennen Daten von Menschen, die
		 *  es nicht mehr gibt. Das ist Betreiberauskunft.
		 */
		public static function render_equipment_orphans() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$waisen = Pacioli_Identities::orphans();

			if ( ! $waisen ) {
				return;
			}

			$spuren = Pacioli_Marker::no_secret_calls();
			?>
			<h2><?php esc_html_e( 'AIs whose owner no longer exists', 'cluster-one-pacioli' ); ?></h2>

			<p style="max-width:48em;color:#555">
				<?php esc_html_e( 'These carry no owner any more, so their names cannot be told apart by owner. Each line therefore names its identity number as well — and, where the counter has seen one, its last call.', 'cluster-one-pacioli' ); ?>
			</p>

			<ul style="max-width:48em">
				<?php foreach ( $waisen as $c1_waise ) : ?>
					<?php
					$c1_id   = is_array( $c1_waise ) ? (int) ( $c1_waise['id'] ?? 0 ) : (int) $c1_waise;
					$c1_spur = isset( $spuren[ $c1_id ] ) && is_array( $spuren[ $c1_id ] ) ? $spuren[ $c1_id ] : array();
					?>
					<li><?php echo esc_html( Pacioli_Identities::label_or_trace( $c1_id, $c1_spur ) ); ?></li>
				<?php endforeach; ?>
			</ul>
			<?php
		}

		/**
		 * Die Ausfuhr und die Einfuhr — Posten B6, 10.10.2026.
		 *
		 * =====================================================================
		 *  DIE FRAGE IST GEWANDERT, SIE IST NICHT GEFALLEN
		 * =====================================================================
		 *
		 *  Christians Satz war: „es muss die Frage geben, dauerhaft loeschen".
		 *  Gestellt wurde sie dem SysOp — ueber die Schluessel ANDERER.
		 *
		 *      Eine Frage ist nur dann eine Frage, wenn der Gefragte der
		 *      Betroffene ist. Sonst ist sie eine Mitteilung in
		 *      Fragezeichenform.                                     (Mammut)
		 *
		 *  Sie steht jetzt hier, auf der Seite des Besitzers, und sie hat
		 *  zwei Haelften: SELBST LOESCHEN gab es schon (`handle_delete`, der
		 *  Knopf fragt „Delete this AI together with its stored key?").
		 *  MITNEHMEN ist neu.
		 *
		 *      Es fehlte nicht die Faehigkeit. Es fehlte das Wissen, dass sie
		 *      gebraucht wird, bevor es zu spaet ist.                (Mammut)
		 *
		 *  GERIEGELT MIT BENANNTEM RUECKFALL: ohne argon2 gibt es keine
		 *  Ausfuhr — und die Seite sagt es, statt den Abschnitt
		 *  weglassen.                                            (R-LEIHE-1)
		 *
		 *      Ein Abschnitt, der fehlt, sieht aus wie einer, den es nicht
		 *      gibt. Einer, der sagt warum, ist eine Auskunft.
		 *
		 *  KEIN KNOPF, WENN ES NICHTS ZU PACKEN GIBT: ein Ausfuhrknopf fuer
		 *  null KIs ist ein Knopf, der eine leere Datei verspricht.
		 */
		public static function render_ausfuhr( $identities ) {
			$moeglich = class_exists( 'Pacioli_Ausfuhr' ) && Pacioli_Ausfuhr::moeglich();
			/* 0.8.39: einmal gefragt, zweimal gelesen – zweite Tuer beim Export und Einlesen aus dem Speicher. */
			$c1_speicher = function_exists( 'cluster_one_speicher_da' ) && cluster_one_speicher_da( (int) get_current_user_id() );
			$ergebnis = isset( $_GET['pacioli_einfuhr'] ) ? sanitize_key( wp_unslash( $_GET['pacioli_einfuhr'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			?>
			<h2><?php esc_html_e( 'Take your AIs with you', 'cluster-one-pacioli' ); ?></h2>

			<?php if ( ! $moeglich ) : ?>
				<div class="notice notice-warning inline" style="max-width:48em">
					<p>
						<?php
						echo esc_html(
							class_exists( 'Pacioli_Ausfuhr' )
								? Pacioli_Ausfuhr::warum_nicht()
								: __( 'The export is not available on this installation: the part that does it is not loaded.', 'cluster-one-pacioli' )
						);
						?>
					</p>
				</div>
				<?php
				return;
			endif;
			?>

			<p style="max-width:48em">
				<?php esc_html_e( 'An export is a file that you keep and this node cannot open. You choose the password, you type it, and it is never stored here. Keep the file and the password apart, and remember: nobody on this node can recover the password for you.', 'cluster-one-pacioli' ); ?>
			</p>

			<?php if ( 'ok' === $ergebnis ) : ?>
				<?php
				$c1_bericht = get_transient( 'pacioli_einfuhr_' . get_current_user_id() );
				$c1_bericht = is_array( $c1_bericht ) ? $c1_bericht : array();
				?>
				<div class="notice notice-success inline" style="max-width:48em">
					<p>
						<?php
						printf(
							/* translators: 1: Anzahl angelegter KIs, 2: Anzahl abgelehnter. */
							esc_html__( 'Read in: %1$d created, %2$d refused.', 'cluster-one-pacioli' ),
							count( isset( $c1_bericht['angelegt'] ) ? $c1_bericht['angelegt'] : array() ),
							count( isset( $c1_bericht['abgelehnt'] ) ? $c1_bericht['abgelehnt'] : array() )
						);
						?>
					</p>

					<?php if ( ! empty( $c1_bericht['abgelehnt'] ) ) : ?>
						<ul>
							<?php foreach ( $c1_bericht['abgelehnt'] as $c1_ab ) : ?>
								<li><?php echo esc_html( $c1_ab['name'] . ' — ' . $c1_ab['grund'] ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( ! empty( $c1_bericht['ohne_schluessel'] ) ) : ?>
						<p>
							<?php
							printf(
								/* translators: %d: Anzahl der KIs ohne Schluessel in der Datei. */
								esc_html( _n(
									'%d of them came without a key in the file, so it has none here either.',
									'%d of them came without a key in the file, so they have none here either.',
									(int) $c1_bericht['ohne_schluessel'],
									'cluster-one-pacioli'
								) ),
								(int) $c1_bericht['ohne_schluessel']
							);
							?>
						</p>
					<?php endif; ?>
				</div>
			<?php elseif ( 'abgelegt' === $ergebnis ) : ?>
				<?php /* 0.8.38: die zweite Tuer hat geschrieben. */
				$c1_ablage = get_transient( 'pacioli_ablage_' . get_current_user_id() );
				$c1_ablage = is_array( $c1_ablage ) ? $c1_ablage : array();
				?>
				<div class="notice notice-success inline" style="max-width:48em">
					<p>
						<?php
						printf(
							/* translators: 1: file name, 2: WebDAV address of the storage */
							esc_html__( 'Written to your storage: %1$s at %2$s. This node cannot open it; the password is yours.', 'cluster-one-pacioli' ),
							'<code>' . esc_html( (string) ( $c1_ablage['datei'] ?? '' ) ) . '</code>',
							esc_html( (string) ( $c1_ablage['url'] ?? '' ) )
						);
						?>
					</p>
				</div>
			<?php elseif ( 'geloescht' === $ergebnis ) : ?>
				<?php /* 0.8.41: aus dem Speicher geloescht. */
				$c1_weg = get_transient( 'pacioli_ablage_' . get_current_user_id() );
				$c1_weg = is_array( $c1_weg ) ? $c1_weg : array();
				?>
				<div class="notice notice-success inline" style="max-width:48em">
					<p>
						<?php
						printf(
							/* translators: %s: file name */
							esc_html__( 'Deleted from your storage: %s. This node kept nothing of it.', 'cluster-one-pacioli' ),
							'<code>' . esc_html( (string) ( $c1_weg['datei'] ?? '' ) ) . '</code>'
						);
						?>
					</p>
				</div>
			<?php elseif ( '' !== $ergebnis ) : ?>
				<div class="notice notice-error inline" style="max-width:48em">
					<p><?php echo esc_html( (string) get_transient( 'pacioli_einfuhr_fehler_' . get_current_user_id() ) ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( $identities ) : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width:48em">
					<input type="hidden" name="action" value="pacioli_export">
					<?php wp_nonce_field( 'pacioli_export' ); ?>

					<p>
						<label>
							<?php esc_html_e( 'Password for the file', 'cluster-one-pacioli' ); ?><br>
							<input type="password" name="pw1" autocomplete="new-password" required minlength="<?php echo esc_attr( Pacioli_Ausfuhr::MIN_ZEICHEN ); ?>" style="width:22em">
						</label>
					</p>

					<p>
						<label>
							<?php esc_html_e( 'The same password again', 'cluster-one-pacioli' ); ?><br>
							<input type="password" name="pw2" autocomplete="new-password" required minlength="<?php echo esc_attr( Pacioli_Ausfuhr::MIN_ZEICHEN ); ?>" style="width:22em">
						</label>
					</p>

					<p style="color:#555">
						<?php
						printf(
							/* translators: %d: Mindestanzahl Zeichen. */
							esc_html__( 'At least %d characters. No rules about capitals or symbols — those produce short passwords with a symbol at the end. It is typed twice because a typo in this one cannot be noticed and cannot be healed.', 'cluster-one-pacioli' ),
							(int) Pacioli_Ausfuhr::MIN_ZEICHEN
						);
						?>
					</p>

					<?php /* 0.8.39 (28.10.2026, Neo): DER NAME DER DATEI – Vorschlag, aenderbar. Nemesus: „dass der
					 * User die Datei benennen kann oder dass ein Vorschlag gemacht wird." Bis 0.8.38 hiess die
					 * Datei immer pacioli-ais-JJJJ-MM-TT.txt; zwei Exporte an einem Tag in denselben Speicher
					 * haetten sich ueberschrieben. Der Vorschlag bleibt derselbe Name; leer gelassen gilt er.
					 * Geprueft wird mit der Regel des Bodens (cluster_one_speicher_dateiname), sonst mit derselben
					 * Regel hier (self::dateiname). */ ?>
					<p>
						<label>
							<?php esc_html_e( 'File name', 'cluster-one-pacioli' ); ?><br>
							<input type="text" name="pacioli_datei" value="<?php echo esc_attr( self::export_dateiname_vorschlag() ); ?>" style="width:22em" autocomplete="off" pattern="[A-Za-z0-9][A-Za-z0-9._-]{0,120}">
						</label>
						<span style="color:#555;display:block"><?php esc_html_e( 'Letters, digits, dot, dash and underscore; no folders. Leave it as it is, and the date names the file.', 'cluster-one-pacioli' ); ?></span>
					</p>

					<?php submit_button( __( 'Export my AIs', 'cluster-one-pacioli' ), 'secondary', 'submit', false ); ?>
					<?php
					/* 0.8.38 (28.10.2026, Neo): DIE ZWEITE TUER – „In meinen Speicher legen". Nemesus: „Jeder
					 * Mensch nutzt seine eigenen KI-APIs und seine eigene Cloud." Der Boden (1.19.0, kern.php)
					 * haelt je Mensch die Adresse seines Speichers; Pacioli fragt ueber function_exists wie beim
					 * Knoten-Reiter – der Boden ist Angebot, nicht Abhaengigkeit. Ohne Boden oder ohne
					 * eingerichteten Speicher fehlt der Knopf, und der Download bleibt der Weg ohne Knoten.
					 * Die Regel aus handle_export bleibt: nichts wird AM KNOTEN abgelegt – der Speicher des
					 * Menschen ist nicht der Knoten, und der Knoten kann die Datei dort nicht oeffnen. */
					if ( $c1_speicher ) : // 0.8.39: oben einmal gefragt
						submit_button( __( 'Put it in my storage', 'cluster-one-pacioli' ), 'secondary', 'pacioli_ziel_speicher', false, array( 'style' => 'margin-left:.6em' ) );
						?>
						<p style="color:#555">
							<?php esc_html_e( 'The same file, written to the storage you set up under “My storage” (personal area) instead of to your browser. The node cannot open it there either: the password stays with you.', 'cluster-one-pacioli' ); ?>
						</p>
					<?php endif; ?>
				</form>
			<?php else : ?>
				<p style="color:#555">
					<?php esc_html_e( 'There is nothing to export yet — no AI is set up for you.', 'cluster-one-pacioli' ); ?>
				</p>
			<?php endif; ?>

			<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width:48em;margin-top:1.5em">
				<input type="hidden" name="action" value="pacioli_import">
				<?php wp_nonce_field( 'pacioli_import' ); ?>

				<p>
					<label>
						<?php esc_html_e( 'Read an export back in', 'cluster-one-pacioli' ); ?><br>
						<input type="file" name="datei" required>
					</label>
				</p>

				<p>
					<label>
						<?php esc_html_e( 'Its password', 'cluster-one-pacioli' ); ?><br>
						<input type="password" name="pw" autocomplete="off" required style="width:22em">
					</label>
				</p>

				<p style="color:#555">
					<?php esc_html_e( 'The AIs from the file are created for YOU, whoever the file says they belonged to — a file is not a power of attorney. A name that is already taken on this node is refused and named, not quietly numbered.', 'cluster-one-pacioli' ); ?>
				</p>

				<?php submit_button( __( 'Read in', 'cluster-one-pacioli' ), 'secondary', 'submit', false ); ?>
			</form>
			<?php
			/* 0.8.39 (28.10.2026, Neo): EINLESEN AUS DEM SPEICHER. Die Liste kostet einen Aufruf an den
			 * Speicher (PROPFIND Depth 1); darum nicht bei jedem Seitenaufbau, sondern erst, wenn der Mensch
			 * „Im Speicher nachsehen" drueckt (?cv_speicher=liste). Gezeigt werden alle .txt-Dateien des
			 * Ordners, deren Name die Regel traegt – ob eine davon ein Export ist, sagt beim Einlesen der
			 * Kopf (CV_Ausfuhr::oeffnen), nicht die Liste. Der Speicher, der nicht antwortet, zeigt nichts
			 * und sagt es. */
			if ( $c1_speicher ) :
				/* 0.8.41 (30.10.2026, Neo): DIE LISTE STEHT SOFORT, JE ZEILE EINLESEN UND LOESCHEN. Bis 0.8.40
				 * stand hier ein Knopf „Im Speicher nachsehen" (?cv_speicher=liste), danach EINE Auswahlliste
				 * mit einem Passwortfeld. Nemesus: die Nachfrage ist uebertrieben; und die Exportdatei im
				 * Speicher soll sich loeschen lassen – das Gegenstueck zu „Liste zurueck nach WordPress holen"
				 * bei der Merkliste, nur ohne Umzug: der Export ist eine Kopie, die man behaelt oder wegwirft.
				 * Der Knoten entfernt die Datei und behaelt nichts davon; Rueckfrage, weil es nicht umkehrbar
				 * ist. Die Boden-Tuer dafuer: cluster_one_speicher_loeschen (seit 1.19.0). */
				$c1_dateien = self::speicher_dateien( (int) get_current_user_id() );
				?>
				<h3 style="margin-top:1.5em"><?php esc_html_e( 'Read in from my storage', 'cluster-one-pacioli' ); ?>
					<a class="button button-small" style="margin-left:.8em;vertical-align:middle" href="<?php echo esc_url( self::page_url() ); ?>"><?php esc_html_e( 'Refresh', 'cluster-one-pacioli' ); ?></a></h3>
				<?php if ( is_wp_error( $c1_dateien ) ) : ?>
					<div class="notice notice-error inline" style="max-width:48em"><p><?php echo esc_html( $c1_dateien->get_error_message() ); ?></p></div>
				<?php elseif ( ! $c1_dateien ) : ?>
					<p style="color:#555"><?php esc_html_e( 'No text file lies in your storage. An export you put there would be listed here.', 'cluster-one-pacioli' ); ?></p>
				<?php else : ?>
				<p style="color:#555;max-width:48em"><?php esc_html_e( 'The text files in your storage. Read in works the same way as an uploaded file: the AIs are created for you, taken names are refused and named; the file stays where it is. Delete removes the file from your storage; this node keeps nothing of it.', 'cluster-one-pacioli' ); ?></p>
				<table class="widefat striped" style="max-width:48em">
					<thead><tr>
						<th><?php esc_html_e( 'File', 'cluster-one-pacioli' ); ?></th>
						<th><?php esc_html_e( 'Its password', 'cluster-one-pacioli' ); ?></th>
						<th style="width:8em"></th>
					</tr></thead>
					<tbody>
					<?php foreach ( $c1_dateien as $c1_d ) : ?>
						<tr>
							<td><code><?php echo esc_html( $c1_d ); ?></code></td>
							<td>
								<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:0;display:flex;gap:.5em;align-items:center">
									<input type="hidden" name="action" value="pacioli_import_speicher">
									<?php wp_nonce_field( 'pacioli_import_speicher' ); ?>
									<input type="hidden" name="datei" value="<?php echo esc_attr( $c1_d ); ?>">
									<input type="password" name="pw" autocomplete="off" required style="width:14em" placeholder="<?php echo esc_attr__( 'Its password', 'cluster-one-pacioli' ); ?>">
									<button type="submit" class="button button-small"><?php esc_html_e( 'Read in', 'cluster-one-pacioli' ); ?></button>
								</form>
							</td>
							<td style="text-align:right">
								<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:0"
									  onsubmit="return confirm('<?php echo esc_js( sprintf( /* translators: %s: file name */ __( 'Delete %s from your storage? This cannot be undone; the node keeps no copy.', 'cluster-one-pacioli' ), $c1_d ) ); ?>');">
									<input type="hidden" name="action" value="pacioli_delete_speicher">
									<?php wp_nonce_field( 'pacioli_delete_speicher' ); ?>
									<input type="hidden" name="datei" value="<?php echo esc_attr( $c1_d ); ?>">
									<button type="submit" class="button button-small" style="color:#b32d2e"><?php esc_html_e( 'Delete from storage', 'cluster-one-pacioli' ); ?></button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php endif;
				/* 0.8.42 (30.10.2026, Neo): „WAS DORT LIEGT" AUCH HIER. Nemesus: von Pacioli aus fuehrt kein
				 * Reiter zum Persoenlichen Bereich, solange Bismarck ihn beansprucht – also steht die ganze
				 * Tabelle des Bodens (alles im Speicher, auch Fremdes wie die Merkliste) zusaetzlich auf
				 * „Meine KIs". Eine Funktion, zwei Orte (kranz.php, Boden 1.19.3); „Aktualisieren" fuehrt
				 * hierher zurueck. Ohne die Funktion (Boden < 1.19.3) fehlt die Tabelle, nichts bricht. */
				if ( function_exists( 'cluster_one_render_speicher_tabelle' ) ) {
					cluster_one_render_speicher_tabelle( (int) get_current_user_id(), self::page_url() );
				}
			endif;
		}

		/** 0.8.41: eine Datei aus dem Speicher des Menschen loeschen – ueber die Boden-Tuer, nichts bleibt. */
		public static function handle_delete_speicher() {
			if ( ! is_user_logged_in() || ! current_user_can( PACIOLI_CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}
			check_admin_referer( 'pacioli_delete_speicher' );
			$me   = (int) get_current_user_id();
			$name = self::dateiname( isset( $_POST['datei'] ) ? (string) wp_unslash( $_POST['datei'] ) : '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (Pacioli_Admin::dateiname (Dateinamen-Reiniger)), den der Pruefer nicht kennt
			if ( '' === $name || ! function_exists( 'cluster_one_speicher_loeschen' ) ) {
				set_transient( 'pacioli_einfuhr_fehler_' . $me, __( 'That is not a file this node would touch in your storage. Nothing has been changed.', 'cluster-one-pacioli' ), 60 );
				wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'fehler' ) ) );
				exit;
			}
			$r = cluster_one_speicher_loeschen( $me, $name );
			/* 404 zaehlt als geloescht: die Datei ist nicht da, und genau das war gewollt. */
			if ( 404 !== (int) $r['code'] && ( (int) $r['code'] < 200 || (int) $r['code'] > 299 ) ) {
				set_transient(
					'pacioli_einfuhr_fehler_' . $me,
					sprintf(
						/* translators: 1: file name, 2: HTTP status code or transport error */
						__( 'Your storage did not delete %1$s (%2$s). The file is still there.', 'cluster-one-pacioli' ),
						$name,
						(int) $r['code'] > 0 ? (string) $r['code'] : (string) $r['fehler']
					),
					60
				);
				wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'fehler' ) ) );
				exit;
			}
			set_transient( 'pacioli_ablage_' . $me, array( 'datei' => $name, 'url' => '' ), 60 );
			wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'geloescht' ) ) );
			exit;
		}

		/** 0.8.39: der Vorschlag fuer den Dateinamen – bis 0.8.38 der einzige Name. */
		public static function export_dateiname_vorschlag() {
			return 'pacioli-ais-' . gmdate( 'Y-m-d' ) . '.txt';
		}

		/** 0.8.39: ein Dateiname, wie der Speicher ihn nimmt – dieselbe Regel wie im Boden
		 *  (cluster_one_speicher_dateiname); der Boden gilt, wenn er da ist. Leer = Vorschlag. '' = abgelehnt. */
		public static function dateiname( $name ) {
			$name = trim( (string) $name );
			if ( '' === $name ) {
				$name = self::export_dateiname_vorschlag();
			}
			if ( function_exists( 'cluster_one_speicher_dateiname' ) ) {
				return cluster_one_speicher_dateiname( $name );
			}
			return preg_match( '~^[A-Za-z0-9][A-Za-z0-9._-]{0,120}$~', $name ) === 1 ? $name : '';
		}

		/**
		 * 0.8.39: Welche Textdateien liegen im Speicher des Menschen?
		 *
		 * EIN AUFRUF, DEN DER BODEN NICHT HAT: die Boden-Tuer (cluster_one_speicher_dav) fragt PROPFIND
		 * mit Depth 0 – die Antwort ueber EINEN Ort. Hier wird der Ordner gelistet (Depth 1), und das
		 * braucht heute nur Pacioli. Kommt ein zweiter Leser, zieht die Liste in den Boden; bis dahin
		 * holt sich Pacioli den Zugang vom Boden und stellt die eine Frage selbst. Gelesen werden die
		 * <href>-Zeilen der Multistatus-Antwort, gleich mit welchem Namensraum-Praefix; genommen wird
		 * der letzte Pfadteil, entschluesselt, und nur, wenn er die Regel traegt und auf .txt endet.
		 *
		 * @return array<int,string>|WP_Error
		 */
		public static function speicher_dateien( $me ) {
			if ( ! function_exists( 'cluster_one_speicher_zugang' ) ) {
				return new WP_Error( 'pacioli_speicher_kein_boden', __( 'The floor of this node has no door to your storage yet.', 'cluster-one-pacioli' ) );
			}
			/* 0.8.40 (30.10.2026, Neo): DER ZWEITE LESER IST DA – der Boden (1.19.1) stellt die Frage an
			 * den Ordner jetzt selbst (cluster_one_speicher_liste, fuer „Was dort liegt"). Pacioli nimmt
			 * sie, wenn sie da ist, und behaelt nur noch sein Sieb (.txt). Der eigene Weg darunter bleibt
			 * fuer einen Boden 1.19.0 – woertlich, wie angekuendigt. */
			if ( function_exists( 'cluster_one_speicher_liste' ) ) {
				$l = cluster_one_speicher_liste( (int) $me );
				if ( '' !== (string) $l['fehler'] ) {
					return new WP_Error( 'pacioli_speicher_antwort', (string) $l['fehler'] );
				}
				$aus = array();
				foreach ( (array) $l['dateien'] as $name ) {
					if ( preg_match( '~\.txt$~i', (string) $name ) ) {
						$aus[] = (string) $name;
					}
				}
				return $aus;
			}
			$z = cluster_one_speicher_zugang( (int) $me );
			if ( '' === $z['url'] || '' === $z['benutzer'] || '' === $z['pass'] ) {
				return new WP_Error( 'pacioli_speicher_zugang', __( 'The access data for your storage is incomplete.', 'cluster-one-pacioli' ) );
			}
			$r = wp_remote_request(
				$z['url'],
				array(
					'method'      => 'PROPFIND',
					'timeout'     => 15,
					'redirection' => 0,
					'headers'     => array(
						'Authorization' => 'Basic ' . base64_encode( $z['benutzer'] . ':' . $z['pass'] ),
						'Depth'         => '1',
					),
				)
			);
			if ( is_wp_error( $r ) ) {
				/* translators: %s: transport error */
				return new WP_Error( 'pacioli_speicher_antwort', sprintf( __( 'Your storage did not answer (%s).', 'cluster-one-pacioli' ), $r->get_error_message() ) );
			}
			$code = (int) wp_remote_retrieve_response_code( $r );
			if ( 207 !== $code && ( $code < 200 || $code > 299 ) ) {
				/* translators: %s: HTTP status code */
				return new WP_Error( 'pacioli_speicher_antwort', sprintf( __( 'Your storage did not answer (%s).', 'cluster-one-pacioli' ), (string) $code ) );
			}
			$body = (string) wp_remote_retrieve_body( $r );
			$aus  = array();
			if ( preg_match_all( '~<(?:[A-Za-z0-9_]+:)?href[^>]*>([^<]+)</(?:[A-Za-z0-9_]+:)?href>~i', $body, $m ) ) {
				foreach ( $m[1] as $href ) {
					$pfad = rawurldecode( html_entity_decode( trim( $href ), ENT_QUOTES | ENT_XML1, 'UTF-8' ) );
					if ( '' === $pfad || '/' === substr( $pfad, -1 ) ) {
						continue; // der Ordner selbst oder ein Unterordner
					}
					$name = (string) basename( $pfad );
					if ( ! preg_match( '~\.txt$~i', $name ) ) {
						continue;
					}
					if ( function_exists( 'cluster_one_speicher_dateiname' ) ? '' === cluster_one_speicher_dateiname( $name ) : preg_match( '~^[A-Za-z0-9][A-Za-z0-9._-]{0,120}$~', $name ) !== 1 ) {
						continue;
					}
					$aus[ $name ] = $name;
				}
			}
			ksort( $aus );
			return array_values( $aus );
		}

		/** 0.8.39: eine Datei aus dem Speicher einlesen – derselbe Weg wie der Upload, nur die Quelle ist anders. */
		public static function handle_import_speicher() {
			if ( ! is_user_logged_in() || ! current_user_can( PACIOLI_CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}
			check_admin_referer( 'pacioli_import_speicher' );
			$me   = (int) get_current_user_id();
			$name = self::dateiname( isset( $_POST['datei'] ) ? (string) wp_unslash( $_POST['datei'] ) : '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (Pacioli_Admin::dateiname (Dateinamen-Reiniger)), den der Pruefer nicht kennt
			if ( '' === $name || ! function_exists( 'cluster_one_speicher_lesen' ) ) {
				set_transient( 'pacioli_einfuhr_fehler_' . $me, __( 'That is not a file this node would read from your storage. Nothing has been changed.', 'cluster-one-pacioli' ), 60 );
				wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'fehler' ) ) );
				exit;
			}
			$r = cluster_one_speicher_lesen( $me, $name );
			if ( (int) $r['code'] < 200 || (int) $r['code'] > 299 ) {
				set_transient(
					'pacioli_einfuhr_fehler_' . $me,
					sprintf(
						/* translators: 1: file name, 2: HTTP status code or transport error */
						__( 'Your storage did not hand out %1$s (%2$s). Nothing has been changed.', 'cluster-one-pacioli' ),
						$name,
						(int) $r['code'] > 0 ? (string) $r['code'] : (string) $r['fehler']
					),
					60
				);
				wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'fehler' ) ) );
				exit;
			}
			$auf = Pacioli_Ausfuhr::oeffnen( (string) $r['body'], isset( $_POST['pw'] ) ? (string) wp_unslash( $_POST['pw'] ) : '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- ein Passwort wird nicht gereinigt: Bytes fuer den Tresor bzw. die WebDAV-Anmeldung, nie HTML; jede Reinigung koennte es veraendern
			if ( is_wp_error( $auf ) ) {
				set_transient( 'pacioli_einfuhr_fehler_' . $me, $auf->get_error_message(), 60 );
				wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'fehler' ) ) );
				exit;
			}
			set_transient( 'pacioli_einfuhr_' . $me, Pacioli_Ausfuhr::einlesen( $me, $auf['kis'] ), 120 );
			wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'ok' ) ) );
			exit;
		}

		/**
		 * Die Ausfuhr ausliefern.
		 *
		 * KEIN SCHLUESSEL GEHT AUF EINEN SCHIRM: die Datei wird als Download
		 * geschickt und nicht angezeigt. Und sie wird NICHT am Knoten
		 * abgelegt — eine Ausfuhrdatei im Uploads-Ordner waere eine zweite
		 * Kopie der Schluessel an einem Ort, den der Knoten oeffnen kann.
		 *
		 *     Eine Ausfuhr, von der eine Kopie zurueckbleibt, ist keine
		 *     Ausfuhr. Sie ist eine Vervielfaeltigung.
		 */
		public static function handle_export() {
			if ( ! is_user_logged_in() || ! current_user_can( PACIOLI_CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			check_admin_referer( 'pacioli_export' );

			$pw1 = isset( $_POST['pw1'] ) ? (string) wp_unslash( $_POST['pw1'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- ein Passwort wird nicht gereinigt: Bytes fuer den Tresor bzw. die WebDAV-Anmeldung, nie HTML; jede Reinigung koennte es veraendern
			$pw2 = isset( $_POST['pw2'] ) ? (string) wp_unslash( $_POST['pw2'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- ein Passwort wird nicht gereinigt: Bytes fuer den Tresor bzw. die WebDAV-Anmeldung, nie HTML; jede Reinigung koennte es veraendern

			if ( $pw1 !== $pw2 ) {
				set_transient( 'pacioli_einfuhr_fehler_' . get_current_user_id(), __( 'The two passwords were not the same. Nothing was exported.', 'cluster-one-pacioli' ), 60 );
				wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'pw' ) ) );
				exit;
			}

			$umschlag = Pacioli_Ausfuhr::packen( get_current_user_id(), $pw1 );

			if ( is_wp_error( $umschlag ) ) {
				set_transient( 'pacioli_einfuhr_fehler_' . get_current_user_id(), $umschlag->get_error_message(), 60 );
				wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'fehler' ) ) );
				exit;
			}

			/* 0.8.39: der Name kommt aus dem Formular; leer = der Vorschlag. Bis 0.8.38 stand hier
			 * `$name = 'pacioli-ais-' . gmdate( 'Y-m-d' ) . '.txt';` – das ist jetzt der Vorschlag. */
			$name = self::dateiname( isset( $_POST['pacioli_datei'] ) ? (string) wp_unslash( $_POST['pacioli_datei'] ) : '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (Pacioli_Admin::dateiname (Dateinamen-Reiniger)), den der Pruefer nicht kennt
			if ( '' === $name ) {
				set_transient( 'pacioli_einfuhr_fehler_' . get_current_user_id(), __( 'That is not a file name this node would write: letters, digits, dot, dash and underscore only, no folders. Nothing was exported.', 'cluster-one-pacioli' ), 60 );
				wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'fehler' ) ) );
				exit;
			}

			/* 0.8.38 (28.10.2026, Neo): die zweite Tuer – derselbe Umschlag, derselbe Name, in den
			 * Speicher des Menschen statt zum Browser (Boden 1.19.0, cluster_one_speicher_schreiben).
			 * Kein Code 2xx: nichts liegt dort, und der Satz des Speichers steht auf der Seite. */
			if ( isset( $_POST['pacioli_ziel_speicher'] ) ) {
				$me = (int) get_current_user_id();
				if ( ! function_exists( 'cluster_one_speicher_schreiben' ) || ! function_exists( 'cluster_one_speicher_da' ) || ! cluster_one_speicher_da( $me ) ) {
					set_transient( 'pacioli_einfuhr_fehler_' . $me, __( 'No storage of yours is set up on this node. Nothing was written; the download is still there.', 'cluster-one-pacioli' ), 60 );
					wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'fehler' ) ) );
					exit;
				}
				$c1_r = cluster_one_speicher_schreiben( $me, $name, $umschlag );
				if ( (int) $c1_r['code'] < 200 || (int) $c1_r['code'] > 299 ) {
					set_transient(
						'pacioli_einfuhr_fehler_' . $me,
						sprintf(
							/* translators: %s: HTTP status code or transport error */
							__( 'Your storage did not take the file (%s). Nothing was written.', 'cluster-one-pacioli' ),
							(int) $c1_r['code'] > 0 ? (string) $c1_r['code'] : (string) $c1_r['fehler']
						),
						60
					);
					wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'fehler' ) ) );
					exit;
				}
				$c1_z = function_exists( 'cluster_one_speicher_zugang' ) ? cluster_one_speicher_zugang( $me ) : array( 'url' => '' );
				set_transient( 'pacioli_ablage_' . $me, array( 'datei' => $name, 'url' => (string) $c1_z['url'] ), 60 );
				wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'abgelegt' ) ) );
				exit;
			}

			nocache_headers();
			header( 'Content-Type: text/plain; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename="' . $name . '"' );
			header( 'Content-Length: ' . strlen( $umschlag ) );

			echo $umschlag; // phpcs:ignore WordPress.Security.EscapeOutput -- Datei, kein HTML.
			exit;
		}

		/** Eine Ausfuhr wieder einlesen. */
		public static function handle_import() {
			if ( ! is_user_logged_in() || ! current_user_can( PACIOLI_CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			check_admin_referer( 'pacioli_import' );

			$me = (int) get_current_user_id();

			/* GEFRAGT, NICHT ANGENOMMEN: eine hochgeladene Datei ist erst
			 * eine, wenn der Upload geklappt hat. */
			$fehler = '';

			if ( ! isset( $_FILES['datei'] ) || ! is_array( $_FILES['datei'] ) ) {
				$fehler = __( 'No file arrived.', 'cluster-one-pacioli' );
			} elseif ( ! empty( $_FILES['datei']['error'] ) ) {
				$fehler = __( 'The file did not arrive completely. Nothing has been changed.', 'cluster-one-pacioli' );
			} elseif ( empty( $_FILES['datei']['tmp_name'] ) || ! is_uploaded_file( $_FILES['datei']['tmp_name'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- $_FILES: tmp_name und size werden mit is_uploaded_file und Groessengrenze geprueft, nie ausgegeben
				$fehler = __( 'The file did not arrive as an upload. Nothing has been changed.', 'cluster-one-pacioli' );
			}

			if ( '' !== $fehler ) {
				set_transient( 'pacioli_einfuhr_fehler_' . $me, $fehler, 60 );
				wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'datei' ) ) );
				exit;
			}

			$text = (string) file_get_contents( $_FILES['datei']['tmp_name'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.WP.AlternativeFunctions -- $_FILES: tmp_name und size werden mit is_uploaded_file und Groessengrenze geprueft, nie ausgegeben
			$auf  = Pacioli_Ausfuhr::oeffnen( $text, isset( $_POST['pw'] ) ? (string) wp_unslash( $_POST['pw'] ) : '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- ein Passwort wird nicht gereinigt: Bytes fuer den Tresor bzw. die WebDAV-Anmeldung, nie HTML; jede Reinigung koennte es veraendern

			if ( is_wp_error( $auf ) ) {
				set_transient( 'pacioli_einfuhr_fehler_' . $me, $auf->get_error_message(), 60 );
				wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'fehler' ) ) );
				exit;
			}

			set_transient( 'pacioli_einfuhr_' . $me, Pacioli_Ausfuhr::einlesen( $me, $auf['kis'] ), 120 );
			wp_safe_redirect( self::page_url( array( 'pacioli_einfuhr' => 'ok' ) ) );
			exit;
		}

		/**
		 * Die Frage, die beim Entfernen nicht mehr gestellt werden kann.
		 *
		 * Eine Deinstallation hat keine Oberflaeche. Sie liest, was vorher
		 * entschieden wurde — also muss die Entscheidung vorher moeglich sein,
		 * und zwar mit den ZAHLEN daneben.
		 *
		 *     Eine Frage nach „loeschen oder behalten", ohne dass dabeisteht
		 *     WIE VIEL, ist keine Frage. Sie ist eine Formulierung.
		 *
		 * Voreinstellung ist BEHALTEN, und das ist keine Bequemlichkeit:
		 * loeschen und neu hochladen ist eine normale Reparatur. Bis heute hat
		 * diese Reparatur jedes KI-Konto des Knotens vernichtet.
		 */
		public static function render_removal_choice() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$konten = Pacioli_Identities::count_all();

			/* ---------------------------------------------------------------
			 * POSTEN B7 — DIE VORHERSAGE, UND SIE KOMMT AUS DER EINEN STELLE
			 *
			 * `CV_Rueckgabe::plan()` ist dieselbe Entscheidung, die
			 * `uninstall.php` ausfuehrt. Eine zweite Rechnung hier waere
			 * eine zweite Wahrheit darueber, was passieren wird.  (P1-12)
			 *
			 *     Eine Vorhersage, die ihre eigene Rechnung anstellt, ist
			 *     keine Vorhersage. Sie ist eine zweite Meinung mit dem
			 *     Anspruch der ersten.
			 *
			 * GERIEGELT, MIT BENANNTEM RUECKFALL: ohne die Klasse wird
			 * NICHTS vorhergesagt, und es steht da, dass nichts
			 * vorhergesagt wurde.                           (R-LEIHE-1)
			 *
			 *     Eine fehlende Vorhersage, die wie „alles in Ordnung"
			 *     aussieht, ist schlimmer als keine Seite.
			 * ------------------------------------------------------------- */
			$c1_plan     = class_exists( 'Pacioli_Rueckgabe' ) ? Pacioli_Rueckgabe::plan() : null;
			/* plan() gibt selbst `null`, wenn es nicht messen kann. */
			$c1_verloren = ( null !== $c1_plan ) ? Pacioli_Rueckgabe::verloren( $c1_plan ) : array();
			$c1_zurueck  = array();

			if ( null !== $c1_plan ) {
				foreach ( $c1_plan as $c1_p => $c1_s ) {
					if ( 'zurueckgegeben' === $c1_s ) {
						$c1_zurueck[] = (string) $c1_p;
					}
				}
			}

			$c1_wer = Pacioli_Identities::wessen_schluessel_gehen();
			?>
			<h2><?php esc_html_e( 'When this plugin is removed', 'cluster-one-pacioli' ); ?></h2>

			<p style="max-width:48em">
				<?php esc_html_e( 'Deactivating is harmless. Removing does two things, and neither of them is a question.', 'cluster-one-pacioli' ); ?>
			</p>

			<p style="max-width:48em">
				<strong><?php esc_html_e( 'Always, without asking — what belongs to somebody else goes back:', 'cluster-one-pacioli' ); ?></strong>
				<?php esc_html_e( 'a provider key that was already in the field when this plugin was set up is put back into that field. It was taken into safekeeping, not taken away. Where it cannot be put back, the field is emptied and this page says so below. And the right to run own AIs is taken out of every role.', 'cluster-one-pacioli' ); ?>
			</p>

			<p style="max-width:48em">
				<strong><?php esc_html_e( 'Always, without asking — the stored AI keys go:', 'cluster-one-pacioli' ); ?></strong>
				<?php esc_html_e( 'every key deposited here, together with the vault key that opens them. Without this plugin they cannot be used by anybody — but they could still be read by anybody who can read the database, and nobody would be watching. So they go, and they go together.', 'cluster-one-pacioli' ); ?>
			</p>

			<?php if ( null === $c1_plan ) : ?>
				<p style="max-width:48em" class="notice notice-warning inline">
					<?php esc_html_e( 'What would happen to the provider keys in safekeeping cannot be said here: the part that decides it is not loaded. This is not a clean bill — it is a missing measurement.', 'cluster-one-pacioli' ); ?>
				</p>
			<?php else : ?>
				<?php if ( $c1_zurueck ) : ?>
					<p style="max-width:48em">
						<?php
						printf(
							/* translators: 1: Anzahl der Anbieter, 2: Liste der Anbieter-Kennungen. */
							esc_html( _n(
								'Measured right now: %1$d provider key can be put back (%2$s).',
								'Measured right now: %1$d provider keys can be put back (%2$s).',
								count( $c1_zurueck ),
								'cluster-one-pacioli'
							) ),
							count( $c1_zurueck ),
							esc_html( implode( ', ', $c1_zurueck ) )
						);
						?>
					</p>
				<?php endif; ?>

				<?php if ( $c1_verloren ) : ?>
					<div class="notice notice-warning inline" style="max-width:48em">
						<p>
							<?php
							/*
							 * CHRISTIANS SATZ, UND ER STEHT HIER, WEIL ER HIER
							 * NUETZT — 10.10.2026.
							 *
							 * Eine Deinstallation hat keine Oberflaeche. Der
							 * Satz nach dem Entfernen erreicht niemanden; der
							 * Satz davor erreicht den, der noch handeln kann.
							 *
							 *     Ein Hinweis auf einen Verlust ist richtig,
							 *     wenn der Verlust unvermeidlich war. War er
							 *     vermeidbar, ist der Hinweis eine
							 *     Hoeflichkeit an der Stelle einer
							 *     Reparatur.                      (Mammut)
							 */
							printf(
								/* translators: %s: Liste der Anbieter-Kennungen. */
								esc_html( _n(
									'For %s a key lies in the provider field that this plugin took into safekeeping, and it cannot be given back. On removal that field will be emptied and the key is gone. Enter it again under Settings → Connectors — or write it down now.',
									'For these providers a key lies in the provider field that this plugin took into safekeeping, and it cannot be given back: %s. On removal those fields will be emptied and the keys are gone. Enter them again under Settings → Connectors — or write them down now.',
									count( $c1_verloren ),
									'cluster-one-pacioli'
								) ),
								esc_html( implode( ', ', $c1_verloren ) )
							);
							?>
						</p>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $c1_wer['menschen'] || $c1_wer['waisen'] ) : ?>
				<div class="notice notice-warning inline" style="max-width:48em">
					<p>
						<?php if ( $c1_wer['menschen'] ) : ?>
							<?php
							printf(
								/* translators: 1: Anzahl der Personen, 2: Liste der Namen. */
								esc_html( _n(
									'This removal deletes the keys of %1$d person: %2$s. They cannot restore them afterwards. If you only mean to repair something, ask them to export their AIs first.',
									'This removal deletes the keys of %1$d people: %2$s. None of them can restore them afterwards. If you only mean to repair something, ask them to export their AIs first.',
									count( $c1_wer['menschen'] ),
									'cluster-one-pacioli'
								) ),
								count( $c1_wer['menschen'] ),
								esc_html( implode( ', ', $c1_wer['menschen'] ) )
							);
							?>
						<?php endif; ?>

						<?php if ( $c1_wer['waisen'] ) : ?>
							<?php
							/* Eine Waise hat keinen Menschen, der gewarnt
							 * werden koennte. Sie unter die Personen zu
							 * rechnen hiesse einen Empfaenger behaupten. */
							echo ' ';
							printf(
								/* translators: %d: Anzahl der verwaisten KIs mit Schluessel. */
								esc_html( _n(
									'A further %d AI has a key and no owner any more — there is nobody to ask.',
									'A further %d AIs have keys and no owner any more — there is nobody to ask.',
									(int) $c1_wer['waisen'],
									'cluster-one-pacioli'
								) ),
								(int) $c1_wer['waisen']
							);
							?>
						<?php endif; ?>
					</p>
				</div>
			<?php endif; ?>

			<?php
			/* 18.10.2026 (Neo): HIER STAND DIE FRAGE — ein Haekchen „auch die
			 * KI-Konten und beide Rollen loeschen" samt Handler, das
			 * `cv_entfernen_daten` schrieb. uninstall.php liest diese Option
			 * seit dem 11.10. nicht mehr („Die Frage ist weg", Christians
			 * Entscheidung a): die Konten und die Rollen bleiben immer, und die
			 * gespeicherte Antwort wird dort geloescht. Das Formular war seither
			 * ein Schalter ohne Draht. Jetzt steht hier, was wirklich gilt. */
			?>
			<p style="max-width:48em">
				<strong><?php esc_html_e( 'What stays:', 'cluster-one-pacioli' ); ?></strong>
				<?php
				printf(
					/* translators: %d: Anzahl der KI-Konten auf diesem Knoten. */
					esc_html( _n(
						'the %d AI account and both AI roles. Deleting the plugin and uploading it again is an ordinary repair, and a repair should not cost anybody their AI. What it does cost is the keys, and that is why the sentence above names who loses one.',
						'the %d AI accounts and both AI roles. Deleting the plugin and uploading it again is an ordinary repair, and a repair should not cost anybody their AI. What it does cost is the keys, and that is why the sentence above names who loses one.',
						$konten,
						'cluster-one-pacioli'
					) ),
					(int) $konten
				);
				?>
			</p>
			<?php
		}

		/**
		 * W54 — KIs, deren Besitzer es nicht mehr gibt.
		 *
		 * NUR fuer `manage_options`, und NUR wenn es welche gibt. Ein leerer
		 * Abschnitt „keine verwaisten KIs" waere eine Beruhigung an einer
		 * Stelle, an der niemand eine gesucht hat.
		 *
		 * Er zeigt sie AN, statt sie zu verstecken. Eine verwaiste KI, die
		 * verschwindet, ist die leere Huelle wieder.
		 */
		public static function render_orphans() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$orphans = Pacioli_Identities::orphans();
			$gezaehlt = Pacioli_Identities::owner_gone_state();

			if ( ! $orphans && ! $gezaehlt['count'] ) {
				return;
			}
			?>
			<h2><?php esc_html_e( 'AI identities whose owner no longer exists', 'cluster-one-pacioli' ); ?></h2>

			<p style="max-width:48em">
				<?php esc_html_e( 'When a person is deleted, their AIs go with them. That works on the normal path. It does not work when a user account disappears some other way — an edit straight in the database, a half-restored backup, a network setup. Then the AI stays, and so does its key.', 'cluster-one-pacioli' ); ?>
			</p>

			<p style="max-width:48em">
				<strong><?php esc_html_e( 'These AIs cannot be used.', 'cluster-one-pacioli' ); ?></strong>
				<?php esc_html_e( 'Their keys are not handed out any more, so no call can be made on a bill that belongs to nobody. Nothing has been deleted — that is your decision, not this page\'s.', 'cluster-one-pacioli' ); ?>
			</p>

			<?php
			/* Die Zahl in eine Variable, BEVOR sie in _n() geht.
			   `$gezaehlt['count']` schmuggelt sonst die Zeichenkette 'count' als
			   msgid in den Katalog — der Extraktor sammelt jedes Literal im
			   Aufruf ein und kann nicht wissen, dass dieses ein Schluessel ist. */
			$abgewiesen = (int) $gezaehlt['count'];
			?>
			<?php if ( $abgewiesen ) : ?>
				<p style="max-width:48em">
					<?php
					printf(
						/* translators: %d: Anzahl der abgewiesenen Aufrufe. */
						esc_html( _n(
							'%d call was refused for this reason so far.',
							'%d calls were refused for this reason so far.',
							$abgewiesen,
							'cluster-one-pacioli'
						) ),
						/* 0.8.51 (01.11.2026, Neo): Plugin Check (OutputNotEscaped) sieht die Zuweisung
						   `(int)` zehn Zeilen weiter oben nicht; er liest nur diese Zeile. absint() sagt
						   es ihm an der Stelle. Bis 0.8.50 stand hier nackt: $abgewiesen */
						absint( $abgewiesen )
					);
					?>
				</p>
			<?php endif; ?>

			<?php if ( $orphans ) : ?>
				<table class="widefat striped" style="max-width:48em">
					<thead>
						<tr>
							<th><?php esc_html_e( 'AI', 'cluster-one-pacioli' ); ?></th>
							<th><?php esc_html_e( 'Provider', 'cluster-one-pacioli' ); ?></th>
							<th><?php esc_html_e( 'Key in safekeeping', 'cluster-one-pacioli' ); ?></th>
							<th><?php esc_html_e( 'Owner (gone)', 'cluster-one-pacioli' ); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ( $orphans as $ai_id ) : ?>
						<tr>
							<td><?php echo esc_html( get_the_author_meta( 'display_name', $ai_id ) ); ?></td>
							<td><?php echo esc_html( (string) get_user_meta( $ai_id, PACIOLI_META_PROVIDER, true ) ); ?></td>
							<td><?php echo Pacioli_Vault::has_secret( $ai_id ) ? esc_html__( 'yes', 'cluster-one-pacioli' ) : esc_html__( 'no', 'cluster-one-pacioli' ); ?></td>
							<td>#<?php echo (int) get_user_meta( $ai_id, PACIOLI_META_OWNER, true ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:1em">
					<input type="hidden" name="action" value="pacioli_clear_orphans">
					<?php wp_nonce_field( 'pacioli_clear_orphans' ); ?>
					<input type="hidden" name="gezaehlt" value="<?php echo (int) count( $orphans ); ?>">
					<?php
					submit_button(
						sprintf(
							/* translators: %d: Anzahl der verwaisten KIs. */
							esc_html( _n(
								'Delete this %d orphaned AI and its key',
								'Delete these %d orphaned AIs and their keys',
								count( $orphans ),
								'cluster-one-pacioli'
							) ),
							count( $orphans )
						),
						'delete',
						'submit',
						false
					);
					?>
					<p style="max-width:48em;color:#555">
						<?php esc_html_e( 'The number above is counted again the moment you press the button. If it has changed in the meantime, nothing is deleted and you are told so — a list from a minute ago is not a decision about what is there now.', 'cluster-one-pacioli' ); ?>
					</p>
				</form>
			<?php endif; ?>
			<?php
		}

		/**
		 * Der Knopf — und er zaehlt VORHER.
		 *
		 * Die Seite hat eine Zahl gezeigt, der Mensch hat auf diese Zahl hin
		 * gedrueckt. Zwischen beidem liegt Zeit. Also wird neu gezaehlt und
		 * verglichen; weichen die Zahlen ab, geschieht NICHTS.
		 *
		 *     Ein Knopf, der eine andere Menge raeumt als die, die dastand,
		 *     hat den Menschen etwas anderes tun lassen, als er wollte.
		 */
		public static function handle_clear_orphans() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			check_admin_referer( 'pacioli_clear_orphans' );

			$gezeigt = isset( $_POST['gezaehlt'] ) ? (int) $_POST['gezaehlt'] : -1;
			$jetzt   = Pacioli_Identities::orphans();

			if ( count( $jetzt ) !== $gezeigt ) {
				wp_safe_redirect( self::page_url( array( 'orphans' => 'geaendert' ) ) );
				exit;
			}

			require_once ABSPATH . 'wp-admin/includes/user.php';

			$geloescht = 0;

			foreach ( $jetzt as $ai_id ) {
				if ( wp_delete_user( (int) $ai_id ) ) {
					$geloescht++;
				}
			}

			// Die Zaehlung gehoert zum Fall, nicht zur Ewigkeit: ist geraeumt,
			// faengt sie bei null an. Bleibt sie danach auf null, ist der Haken
			// bewiesen statt geglaubt.
			if ( $geloescht === count( $jetzt ) ) {
				Pacioli_Identities::restore_owner_gone();
			}

			wp_safe_redirect( self::page_url( array( 'orphans' => (string) $geloescht ) ) );
			exit;
		}

		public static function handle_save() {
			if ( ! current_user_can( PACIOLI_CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			check_admin_referer( 'pacioli_save_identity' );

			$me       = get_current_user_id();
			$identity = isset( $_POST['identity'] ) ? sanitize_text_field( wp_unslash( $_POST['identity'] ) ) : 'new';
			$secret   = isset( $_POST['secret'] ) ? (string) wp_unslash( $_POST['secret'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- ein API-Schluessel wird nicht gereinigt: Bytes fuer den Tresor, nie HTML; jede Reinigung koennte ihn veraendern

			if ( 'new' === $identity ) {
				$identity = Pacioli_Identities::create(
					array(
						'owner'    => $me,
						'name'     => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
						'provider' => isset( $_POST['provider'] ) ? sanitize_key( wp_unslash( $_POST['provider'] ) ) : '',
						'model'    => isset( $_POST['model'] ) ? sanitize_text_field( wp_unslash( $_POST['model'] ) ) : '',
					)
				);

				if ( is_wp_error( $identity ) ) {
					wp_die( esc_html( $identity->get_error_message() ) );
				}

				/* DER HINWEIS VOM 08.10. IST WEG — Posten 3, 10.10.2026.
				 *
				 * Hier stand ein Hinweis, der eine Doppelung MELDETE und
				 * zuliess. Seit heute sagt `CV_Identities::create()` ab, und
				 * zwar mit Nennung der vorhandenen KI. Christian hat genickt;
				 * seine Forderung vom September ist aelter als der Hinweis.
				 *
				 * ZWEI MECHANISMEN FUER EINE FRAGE waeren zwei Antworten
				 * darauf, wann ein Name vergeben ist. Also einer, und er
				 * steht dort, wo auch die Anmeldung eindeutig gemacht wird.
				 *                                                    (P1-12)
				 *
				 * Die Absage kommt als WP_Error aus create() und wird vom
				 * `is_wp_error`-Zweig darueber ausgegeben — kein eigener Weg
				 * fuer einen eigenen Fehler. */
			} else {
				$identity = (int) $identity;

				// Fremde Identitäten sind hier tabu — auch für Administratoren.
				if ( (int) get_user_meta( $identity, PACIOLI_META_OWNER, true ) !== $me ) {
					wp_die( esc_html__( 'That is not your AI.', 'cluster-one-pacioli' ) );
				}
			}

			if ( '' !== trim( $secret ) ) {
				$saved = Pacioli_Vault::set_secret( $identity, $secret );

				if ( is_wp_error( $saved ) ) {
					wp_die( esc_html( $saved->get_error_message() ) );
				}
			}

			wp_safe_redirect( self::page_url( array( 'updated' => '1' ) ) );
			exit;
		}

		public static function handle_delete() {
			if ( ! current_user_can( PACIOLI_CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			$identity = isset( $_POST['identity'] ) ? (int) $_POST['identity'] : 0;

			check_admin_referer( 'pacioli_delete_identity_' . $identity );

			if ( (int) get_user_meta( $identity, PACIOLI_META_OWNER, true ) !== get_current_user_id() ) {
				wp_die( esc_html__( 'That is not your AI.', 'cluster-one-pacioli' ) );
			}

			require_once ABSPATH . 'wp-admin/includes/user.php';
			wp_delete_user( $identity );

			wp_safe_redirect( self::page_url( array( 'deleted' => '1' ) ) );
			exit;
		}

		/**
		 * Nimmt einen sichergestellten Schlüssel an oder verwirft ihn.
		 */
		public static function handle_quarantine() {
			if ( ! current_user_can( PACIOLI_CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			$provider = isset( $_POST['provider'] ) ? sanitize_key( wp_unslash( $_POST['provider'] ) ) : '';

			check_admin_referer( 'pacioli_quarantine_' . $provider );

			$me  = get_current_user_id();
			$bag = Pacioli_Vault::read_shielded( $me, PACIOLI_META_QUARANTINE );
			$bag = is_array( $bag ) ? $bag : array();

			if ( ! isset( $bag[ $provider ] ) ) {
				wp_safe_redirect( self::page_url() );
				exit;
			}

			$do       = isset( $_POST['do'] ) ? sanitize_key( wp_unslash( $_POST['do'] ) ) : '';
			$identity = isset( $_POST['identity'] ) ? (int) $_POST['identity'] : 0;

			if ( 'assign' === $do && $identity ) {
				// Nur an eigene Identitäten, und nur an passende.
				if ( (int) get_user_meta( $identity, PACIOLI_META_OWNER, true ) !== $me ) {
					wp_die( esc_html__( 'That is not your AI.', 'cluster-one-pacioli' ) );
				}

				$plain = Pacioli_Crypto::decrypt( $bag[ $provider ] );

				if ( is_wp_error( $plain ) ) {
					wp_die( esc_html( $plain->get_error_message() ) );
				}

				$saved = Pacioli_Vault::set_secret( $identity, $plain );

				if ( is_wp_error( $saved ) ) {
					wp_die( esc_html( $saved->get_error_message() ) );
				}
			}

			unset( $bag[ $provider ] );
			update_user_meta( $me, PACIOLI_META_QUARANTINE, $bag );

			wp_safe_redirect( self::page_url() );
			exit;
		}

		/**
		 * Prüft einen hinterlegten Schlüssel — auf die Rechnung des Besitzers.
		 *
		 * ---------------------------------------------------------------------
		 *  0.3.1 — die Antwort auf eine vorhergesagte Rückkopplung.
		 *
		 *  Die Verbindungsprüfung auf der Connectors-Seite gehört zu niemandem:
		 *  Dort ist keine Identität gebunden, also wird kein Schlüssel
		 *  eingesetzt, und die Prüfung meldet „nicht verbunden". Das ist
		 *  verhaltensrichtig — aber ein Administrator, der das sieht, trägt
		 *  seinen Schlüssel neu ein, und zwar wieder und wieder.
		 *
		 *  Der Ausweg ist nicht, dort heimlich doch einen Schlüssel einzusetzen
		 *  (welchen?), sondern die Frage dorthin zu verlegen, wo sie beantwortbar
		 *  ist: zum Besitzer, für seine eigene KI, auf seine eigene Rechnung.
		 *
		 *  Bewusst KEINE Unterscheidung nach GET und POST, um Verbindungs-
		 *  prüfungen automatisch durchzulassen. Das wäre wieder eine Annahme
		 *  über fremde Plugins, die altert.
		 * ---------------------------------------------------------------------
		 */
		public static function handle_check_key() {
			if ( ! current_user_can( PACIOLI_CAP ) ) {
				wp_die( esc_html__( 'Not permitted.', 'cluster-one-pacioli' ) );
			}

			$identity = isset( $_POST['identity'] ) ? (int) $_POST['identity'] : 0;

			check_admin_referer( 'pacioli_check_key_' . $identity );

			if ( (int) get_user_meta( $identity, PACIOLI_META_OWNER, true ) !== get_current_user_id() ) {
				wp_die( esc_html__( 'That is not your AI.', 'cluster-one-pacioli' ) );
			}

			$result = 'unavailable';

			if ( function_exists( 'wp_ai_client_prompt' ) ) {
				$answer = Pacioli_Vault::acting_as(
					$identity,
					function () {
						return wp_ai_client_prompt()->with_text( 'ping' )->generate_text();
					}
				);

				if ( is_wp_error( $answer ) ) {
					$result = 'failed';
					Pacioli_Vault::mark_key_invalid( $identity, $answer->get_error_message() );
				} else {
					$result = 'ok';
				}

				/* TEIL 3 §5b, 08.10.2026 — DER ERFOLG WIRD AUCH GESCHRIEBEN.
				 *
				 * Bis heute landete er nur in `?checked=ok`. Der Misserfolg
				 * dagegen stand als CV_META_STATUS am Konto.
				 *
				 *     Ein geprueffter Schluessel war nach dem naechsten
				 *     Seitenaufruf wieder ungeprueft. Ein durchgefallener
				 *     blieb durchgefallen.
				 *
				 * Das ist Christians Haekchen: er wollte wissen, ob die KIs
				 * ordnungsgemaess eingerichtet sind und funktionieren. Es
				 * gab das schon fast — es fehlte, dass der Ausgang mit
				 * seinem Datum stehenbleibt.
				 *
				 * BEIDE AUSGAENGE AN EINER STELLE. Die Spalte „zuletzt
				 * geprueft" auf der Tafel liest genau diese eine. */
				update_user_meta(
					$identity,
					PACIOLI_META_CHECKED,
					array(
						'when' => time(),
						'ok'   => ( 'ok' === $result ),
					)
				);
			}

			wp_safe_redirect( self::page_url( array( 'checked' => $result ) ) );
			exit;
		}

		/**
		 * Erklärt die Marke dort, wo sie sonst wie ein Defekt aussieht.
		 *
		 * Ohne diesen Hinweis sieht der Administrator in den Schlüsselfeldern
		 * der Anbieter-Plugins etwas Unerwartetes, hält es für kaputt und trägt
		 * seinen Schlüssel neu ein. Abgefangen wird das ohnehin — aber ein
		 * Riegel, der ständig zuschnappt, ist ein schlechter Riegel.
		 */
		public static function connectors_notice() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// ERSTE BEDINGUNG: gibt es hier ueberhaupt etwas zu erklaeren?
			//
			// 03.09.2026 — bis 0.8.1 stand hier nur eine Bildschirm-Bedingung,
			// und `'options-general' !== $id` liess den Hinweis auf der
			// allgemeinen Einstellungsseite JEDER Installation erscheinen,
			// auch wenn dort kein einziger Anbieter eingerichtet ist.
			//
			//     Ein Hinweis auf einer fremden Seite ist geliehener Platz. Wer
			//     ihn dort auch dann hinstellt, wo es nichts zu erklaeren gibt,
			//     hat sich den Platz nicht geliehen, sondern genommen.
			$providers = Pacioli_Marker::providers();

			if ( ! $providers ) {
				return;
			}

			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
			$id     = $screen ? (string) $screen->id : '';
			$seite  = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

			$hier = ( false !== strpos( $id, 'connector' ) )
				|| ( false !== strpos( $seite, 'connector' ) )
				|| ( 'options-general' === $id );

			/**
			 * Erscheint der Wegweiser auf diesem Bildschirm?
			 *
			 * BENANNTE GRENZE: Welchen Bildschirm der Core den
			 * Connector-Einstellungen gibt, ist von hier aus NICHT gemessen —
			 * wir erkennen ihn an seinem Namen, und ein Name kann sich
			 * aendern. Baut der Core um, verschwindet dieser Hinweis STILL.
			 *
			 *     Deshalb traegt er nichts, was nur hier steht. Die Wahrheit
			 *     steht auf unserer eigenen Seite; das hier ist eine Beigabe.
			 */
			if ( ! apply_filters( 'pacioli_wegweiser_hier', $hier, $id, $seite ) ) {
				return;
			}

			// ZWEITE ZEILE: eine AUSKUNFT, keine Behauptung.
			//
			// „Connector Vault ist aktiv" sagt nur, dass Code laeuft. Die Zahl
			// der Identitaeten sagt, ob er etwas TUT — und eine Null ist genau
			// die Warnung, die ein Admin hier braucht: die Felder sind belegt,
			// aber es gibt niemanden, fuer den getauscht wird.
			$identitaeten = Pacioli_Identities::count_all();

			$auskunft = $identitaeten
				? sprintf(
					/* translators: %d: Anzahl der KI-Identitaeten mit Schluessel. */
					_n(
						'Connector Vault manages the keys of %d AI identity on this site.',
						'Connector Vault manages the keys of %d AI identities on this site.',
						$identitaeten,
						'cluster-one-pacioli'
					),
					$identitaeten
				)
				: __( 'Connector Vault is active, but not one AI identity exists yet. The key fields here are therefore placeholders without anything behind them — no call can currently be made on anybody\'s key.', 'cluster-one-pacioli' );

			printf(
				'<div class="notice notice-info"><p><strong>%1$s</strong><br>%2$s</p><p>%3$s</p><p>%4$s</p></div>',
				esc_html__( '"Not connected" is the normal state on this page', 'cluster-one-pacioli' ),
				esc_html__( 'Connector Vault manages these key fields. What stands in them is a placeholder, not a broken key: the real key is inserted only immediately before sending, and it is the key of whichever AI is acting at that moment. A connection check from this page belongs to nobody — so no key is inserted, and so the check reports "not connected". That is correct and not a fault.', 'cluster-one-pacioli' ),
				esc_html( $auskunft ),
				sprintf(
					/* translators: %s: Link zur Schluesselmaske. */
					wp_kses_post( __( 'Please do not enter anything here — a key in this place would apply to everyone. Personal keys are managed at %s; that is also where a key can be checked.', 'cluster-one-pacioli' ) ),
					'<a href="' . esc_url( self::page_url() ) . '">' . esc_html( self::wo() ) . '</a>'
				)
			);
		}

		/**
		 * Wie der Weg zur Maske heisst — so, wie er auf diesem Knoten heisst.
		 *
		 * Ein Wegweiser, der „Benutzer → Meine KIs" sagt, waehrend die Seite
		 * unter Cluster One haengt, ist schlimmer als keiner: er schickt
		 * jemanden an eine Stelle, an der er nichts findet, und laesst ihn
		 * glauben, er habe etwas falsch gemacht.
		 */
		/*
		 * POSTEN 1, 08.10.2026 — DIESER WEGWEISER WAR SELBST FALSCH.
		 *
		 * Der Block darueber sagt, dass ein Wegweiser auf eine Stelle, an der
		 * nichts ist, schlimmer ist als keiner. Danach hiess der Menuepunkt
		 * seit dem 16.09. „Pacioli" — gebaut vom Dach aus `$c1_pacioli_name`
		 * (Lader :176, :493) — und dieser Satz sagte weiter „Connector Vault".
		 *
		 *     Ein Wegweiser, der seine eigene Warnung traegt und sie an sich
		 *     selbst nicht anwendet, ist die vierte Stelle und nicht die
		 *     erste.
		 *
		 * Mammut hat drei Saetze gemeldet. Dies ist der vierte, und ihn hat
		 * niemand gesucht — er steht nicht unter „Tools →", sondern unter
		 * „Cluster One →".
		 *
		 * DER NAME WIRD NICHT GETIPPT: `CV_NAME` ist dieselbe Quelle, aus der
		 * das Dach seinen Punkt baut. Eine getippte Adresse waere ein zweiter
		 * Schreiber und wuerde beim naechsten Umzug still falsch.    (P1-12)
		 */
		public static function wo() {
			return ( '' !== self::eltern() )
				? sprintf(
					/* translators: %s: Name dieses Produkts im Cluster-One-Menue. */
					__( 'Cluster One → %s', 'cluster-one-pacioli' ),
					PACIOLI_NAME
				)
				: __( 'Connector Vault (in the main menu)', 'cluster-one-pacioli' );
		}

		/**
		 * Wo die Pruefungen stehen — GELESEN, nicht getippt.
		 *
		 * =====================================================================
		 *  WARUM DAS EINE FUNKTION IST UND KEINE DREI SAETZE
		 * =====================================================================
		 *
		 *  Drei Oberflaechensaetze schickten Leute bis heute „unter Werkzeuge
		 *  → Connector Vault" — an eine Stelle, die am 16.09.2026
		 *  weggezogen ist (`add_management_page` kommt im ganzen Baum null mal
		 *  vor). Christian hat einen davon auf beiden Knoten auf dem Schirm.
		 *
		 *  DIE NAHELIEGENDE REPARATUR WAERE DIE FALSCHE: man koennte in jeden
		 *  der drei Saetze die neue Adresse tippen. Dann stehen DREI Adressen
		 *  im Quelltext, und beim naechsten Umzug sind wieder drei Stellen
		 *  falsch — genau wie `users.php?page=cv-my-ais` bis 0.8.1 an sechs
		 *  Stellen stand.
		 *
		 *      Ein Satz, der eine Adresse NENNT, ist ein Schreibweg auf die
		 *      Adresse. Drei Saetze sind drei Schreiber.             (P1-12)
		 *
		 *  UND ES GIBT EINEN ZWEITEN GRUND, DER HEUTE WICHTIGER IST: der
		 *  Reiter wird umbenannt (ZUG mit den drei Reitern, „Fuer
		 *  Entwickler"). Haette ich den neuen Namen jetzt in drei Saetze
		 *  getippt, waeren sie an dem Tag falsch, an dem der Reiter noch
		 *  nicht so heisst — oder an dem Tag, an dem er anders heisst.
		 *
		 *      Eine Adresse, die aus dem Code kommt, ist heute richtig und
		 *      nach dem Umbau auch. Eine getippte ist einmal richtig.
		 *
		 *  Deshalb kommt der Reitername aus `seiten()` — derselben einen
		 *  Liste, aus der die Reiterleiste und die Anmeldung ihn nehmen.
		 *
		 *  DER RUECKFALL IST BENANNT: faellt `CV_Selftest` aus, gibt es den
		 *  Reiter nicht, und dann nennt die Adresse nur den Ort ohne ihn —
		 *  nicht einen Reiter, den es auf diesem Knoten nicht gibt.
		 *                                                        (R-LEIHE-1)
		 * =====================================================================
		 */
		/**
		 * Wie ein Reiter auf diesem Knoten heisst — GELESEN, nicht getippt.
		 *
		 * Dieselbe Regel wie bei den Wegweisern (Posten 1): ein Satz, der
		 * einen Reiternamen NENNT, ist ein Schreibweg auf den Namen. Hier
		 * kommt er aus `seiten()`, derselben Liste, aus der die Leiste ihn
		 * nimmt.
		 *
		 * BENANNTER RUECKFALL: gibt es den Reiter auf diesem Knoten nicht,
		 * liefert sie eine leere Zeichenkette — und der Aufrufer
		 * entscheidet, was er dann sagt. Einen Namen zu erfinden waere die
		 * vierte Stelle.                                       (R-LEIHE-1)
		 */
		public static function reitername( $slug ) {
			foreach ( self::seiten() as $c1_s ) {
				if ( $slug === $c1_s['slug'] ) {
					return (string) $c1_s['text'];
				}
			}

			return '';
		}

		public static function wo_pruefungen() {
			/* 08.10.2026: der Reitername kommt jetzt aus `reitername()`.
			 *
			 * Hier stand dieselbe Schleife ueber `seiten()` noch einmal —
			 * kein zweiter Schreibweg, aber ein zweiter Leseweg mit
			 * eigener Schreibweise. Als die Tafel dazukam, gab es die
			 * Suche dreimal.
			 *
			 *     Zwei Leser sind erlaubt. Zwei Fassungen desselben
			 *     Lesens gehen auseinander, sobald eine davon einen
			 *     Sonderfall lernt. */
			$reiter = class_exists( 'Pacioli_Selftest' ) ? self::reitername( Pacioli_Selftest::PAGE ) : '';

			if ( '' === $reiter ) {
				return self::wo();
			}

			return self::wo() . ' → ' . $reiter;
		}

		// =====================================================================
		//  Spalte in der Benutzerübersicht
		// =====================================================================

		public static function add_column( $columns ) {
			$columns['pacioli_ai'] = __( 'Own AIs', 'cluster-one-pacioli' );

			/* TEIL 3 §5d — dieselbe Gestalt wie CV_CAP, eine Spalte weiter.
			 * Eine zweite Schreibweise fuer dasselbe waere eine zweite
			 * Wahrheit darueber, wie ein Recht je Person aussieht.
			 * 0.10.2 (Omega): hier stand bis 0.10.1
			 *     $columns['pacioli_see'] = __( 'Developer info', 'cluster-one-pacioli' );
			 * Seit 0.8.45 liest jedes Mitglied „Fuer Entwickler" (darf_lesen); die Spalte haette bei jedem Mitglied
			 * „not enabled" gesagt und damit das Gegenteil der Wahrheit. Sie ist fort, mit ihren Massenaktionen. */

			return $columns;
		}

		public static function render_column( $output, $column, $user_id ) {
			/* 0.10.2 (Omega): hier stand bis 0.10.1 der Zweig fuer die Spalte 'pacioli_see' (administrator · always /
			 * enabled / not enabled). Die Spalte gibt es nicht mehr, siehe add_column(). */

			if ( 'pacioli_ai' !== $column ) {
				return $output;
			}

			if ( ! user_can( $user_id, PACIOLI_CAP ) ) {
				return '<span style="color:#8a8a8a">' . esc_html__( 'not enabled', 'cluster-one-pacioli' ) . '</span>';
			}

			$count = count( Pacioli_Identities::owned_by( $user_id ) );

			/* 0.8.46 (30.10.2026, Neo): seit 0.8.44 kommt das Recht mit der Mitgliedschaft am Knoten
			 * (map_meta_cap, cluster-one-pacioli.php). Die Spalte sagt das, statt „enabled" zu
			 * behaupten, als haette es jemand vergeben – sonst stuende bei jedem Mitglied eine
			 * Halbwahrheit, und „Entziehen" saehe aus wie ein Weg, der keiner ist. */
			if ( self::ist_mitglied( $user_id ) ) {
				return esc_html(
					sprintf(
						/* translators: %d: Anzahl KI-Identitäten. */
						_n( 'member of the node · %d AI', 'member of the node · %d AIs', $count, 'cluster-one-pacioli' ),
						$count
					)
				);
			}

			return esc_html(
				sprintf(
					/* translators: %d: Anzahl KI-Identitäten. */
					_n( 'enabled · %d AI', 'enabled · %d AIs', $count, 'cluster-one-pacioli' ),
					$count
				)
			);
		}

		/** 0.8.46: ist dieser Mensch laut Boden dabei (Weissliste, SysOp, KI-Kennung, Collective)? */
		public static function ist_mitglied( $user_id ) {
			return function_exists( 'cluster_one_can_access' ) && cluster_one_can_access( (int) $user_id );
		}

		/* ===========================================================================
		 *  0.10.2 (03.11.2026, Neo), RUNDE OMEGA — DIE MASSENAKTIONEN DER BENUTZERLISTE SIND FORT.
		 * ===========================================================================
		 *
		 *  Nemesus: „alles da steuern, wo wir es brauchen, mit jeweils einem PlugIn, was das PlugIn selbst betrifft."
		 *  Crockford hat seine Freigabe heute auf seine eigene Seite gezogen. Fuer Pacioli ergab das Messen etwas anderes:
		 *  seit 0.8.44 hat JEDES Mitglied des Knotens das Recht auf eigene KIs (map_meta_cap im Lader), seit 0.8.45 liest
		 *  jedes Mitglied „Fuer Entwickler" (Pacioli_Selftest::darf_lesen). Die vier Massenaktionen taten damit fuer Mitglieder
		 *  nichts (bulk_notice sagte es: „keeps the right: it comes with being a member") und fuer Nicht-Mitglieder etwas,
		 *  das die Hausregel verletzt — wer nicht auf der Liste des Knotens steht, bekommt von Cluster One nichts, auch
		 *  kein Recht je Mensch. Also gibt es bei Pacioli nichts freizuschalten; was ein SysOp steuert, ist die Mitgliedschaft
		 *  (Knoten → Knoteneinstellungen), und „Fuer Entwickler" sagt das jetzt mit der Zahl der Mitglieder und dem Link.
		 *
		 *  HIER STANDEN BIS 0.10.1 DREI METHODEN:
		 *      bulk_notice()        die zwei Antwortsaetze auf users.php (pacioli_bulk_ohne, pacioli_bulk_mitglied)
		 *      add_bulk_actions()   „Allow own AIs" / „Withdraw own AIs" / „Allow developer info" / „Withdraw developer info"
		 *      handle_bulk()        add_cap/remove_cap je Konto fuer PACIOLI_CAP und PACIOLI_SEE (SEE setzte CAP voraus),
		 *                           KIs uebersprungen, Mitglieder beim Entziehen gezaehlt und gelassen, Chronik-Wort
		 *                           pacioli_granted/pacioli_revoked mit der Zahl (0.8.48)
		 *  BENANNTE GRENZE: ein Konto, das vor heute ausdruecklich PACIOLI_CAP oder PACIOLI_SEE bekam und nicht Mitglied ist,
		 *  behaelt die Cap am Konto — es gibt keinen Weg mehr, sie zu entziehen, ausser WordPress selbst. Auf den vier Knoten
		 *  des Hauses gibt es ein solches Konto nach Nemesus' Wissen nicht; die Chronik-Woerter bleiben fuer die alten Zeilen.
		 * ========================================================================= */
	}
}
