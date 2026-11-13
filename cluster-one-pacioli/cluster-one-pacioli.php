<?php
/**
 * Plugin Name:       Cluster One — Pacioli
 * Plugin URI:        https://christianermisch.com/
 * Description:       Each AI identity sends only the API key its own owner deposited — no shared key, no shared bill — and no real key is ever stored in the options table: what stands there is a placeholder, exchanged for the real one only as the request goes out.
 * Version:           0.10.3
 * Requires at least: 7.0
 * Requires PHP:      7.4
 * Author:            Christian Ermisch (Nemesus)
 * Author URI:        https://christianermisch.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cluster-one-pacioli
 * Domain Path:       /languages
 * Cluster-One-Entfernt: Deaktivieren ist harmlos. Entfernen tut DREI Dinge, und nur eines ist eine Frage. IMMER ZURUECKGEGEBEN wird fremdes Eigentum: ein Anbieter-Schluessel, der vor der Einrichtung in der Anbieter-Zeile lag, geht dorthin zurueck; geht das nicht, wird die Zeile geleert, und der Reiter Ausstattung sagt es VORHER. IMMER GELOESCHT werden die hinterlegten KI-Schluessel samt Tresorschluessel - ohne dieses Plugin sind sie unbenutzbar und unbeobachtet. Wer sie behalten will, fuehrt sie vorher unter Meine KIs aus. DIE KI-BENUTZERKONTEN UND BEIDE ROLLEN BLEIBEN IMMER - seit 0.8.22 gibt es die Frage danach nicht mehr. Acht Betriebsoptionen bleiben ebenfalls (Salz, bekannte Anbieter, die Zaehler, der Fremdnachweis); die uebrigen eigenen Optionen gehen. Zahlen und Namen dazu stehen VORHER auf dem Reiter Ausstattung, das Geschehene danach in der Option cluster_one_entfernt.
 * Cluster-One-Traegt: 21
 *
 * =====================================================================
 *  WIE DAS HIER FUNKTIONIERT — drei Fassungen, ein Ziel
 * =====================================================================
 *
 *  0.1.0 wollte den Optionswert WÄHREND eines Aufrufs umschalten. Das
 *  wurde am 23./24.08.2026 auf einer Live-Installation am tatsächlich
 *  hinausgehenden Request gemessen und hält nicht: Die Option wird nach
 *  `init` nie wieder gelesen.
 *
 *  0.3.0 hat daraus die MARKE gemacht: In der Options-Zeile steht kein
 *  Schlüssel mehr, sondern ein wertloser, lesbarer Platzhalter, der
 *  unmittelbar vor dem Absenden im Header gegen den echten Schlüssel
 *  getauscht wird.
 *
 *      CONNECTOR-VAULT--BITTE-NICHT-AENDERN--openai--a1b2c3d4
 *
 *  0.4.0 kennt den GRUND für den Messbefund — und damit den sauberen Weg.
 *  Der Schlüssel wohnt nach `init` als Authentifizierungs-Objekt in der
 *  Registry des KI-Clients. Core legt ihn dort selbst ab:
 *
 *      add_action( 'init', '_wp_connectors_pass_default_keys_to_ai_client', 20 );
 *      $ai_registry->setProviderRequestAuthentication( $id, new ApiKeyRequestAuthentication( $key ) );
 *
 *  Und weil das SDK je Aufruf ein frisches Modell aus der Registry holt
 *  (`PromptBuilder::getConfiguredModel()` → `getProviderModel()` →
 *  `bindModelDependencies()`), genügt es, die Authentifizierung
 *  unmittelbar vor dem Aufruf zu setzen und danach zurückzunehmen.
 *  Genau das tut `CV_Vault::acting_as()` seit 0.4.0 (siehe CV_Registry).
 *
 *  DIE MARKE BLEIBT — als Stolperdraht statt als Hauptweg. In der
 *  Options-Zeile steht sie weiter, also bindet Core bei `init` 20 sie als
 *  Authentifizierung. Greift der Override, wird sie überschrieben und
 *  niemand sieht sie je. Greift er nicht, tauscht `CV_Marker` sie am
 *  ausgehenden Request; schlägt auch das fehl, hält der Riegel in
 *  `pre_http_request` den Request auf.
 *
 *  Vier Eigenschaften, die bleiben:
 *
 *    - Kein Host-Verzeichnis, kein Header-Verzeichnis. Nichts, was altert.
 *    - Umschalten je Aufruf, auch verschachtelt und mit mehreren
 *      Identitäten im selben Request.
 *    - In `wp_options` steht nie ein echter Schlüssel.
 *    - Der Ausfall ist von selbst sicher: Es reist etwas Wertloses hinaus
 *      und der Anbieter antwortet 401. Nichtstun ist der sichere Zustand.
 *
 *  WAS NOCH NICHT GEMESSEN IST: dass beim Anbieter der Schlüssel des
 *  Handelnden ankommt. Der Nachweis ist ein Durchgang mit zwei
 *  Identitäten am ausgehenden Request; erwartet werden ZWEI VERSCHIEDENE
 *  Abdrücke. Bis eine Person bestätigt, dass dieser Durchgang gelaufen
 *  ist, steht der Hinweis unter Werkzeuge → Connector Vault — dieses
 *  Plugin stellt sich kein Zeugnis selbst aus.
 *
 *  Seit 0.7.2 steht er dort und NUR dort, mit einem Kreuz, das sich nichts
 *  merkt. Er ist keine Warnung über einen Schaden, sondern eine offene
 *  Rechnung, und die gehört dorthin, wo man sie bezahlen kann. Was gerade
 *  kaputt ist, meldet CV_Guard weiterhin über jeder Seite.
 *
 * =====================================================================
 *  VOR DER ERSTEN INSTALLATION LESEN
 * =====================================================================
 *
 *  Dies ist die erste Fassung, die IN DIE ANBIETER-ZEILEN SCHREIBT. Beim
 *  Aktivieren werden vorhandene Schlüssel verschlüsselt in Verwahrung
 *  genommen und durch Marken ersetzt. Auf einem Knoten, auf dem täglich
 *  gearbeitet wird, ist das kein Test, sondern ein Eingriff.
 *
 *  Also: erste Installation auf einen Knoten, dessen Ausfall niemanden
 *  stört — und die vorhandenen Schlüssel vorher woanders greifbar haben.
 *
 *  Seit 0.3.1 gilt dabei: Was sich nicht verwahren lässt, wird auch nicht
 *  weggenommen. Ein sichtbarer schlechter Zustand ist besser als ein
 *  unsichtbarer Verlust.
 *
 * =====================================================================
 *  BENANNTE GRENZEN — was hier fehlt, und warum es aufgeschrieben ist
 * =====================================================================
 *
 *  1 · DIE SPRACHE IST UMGEZOGEN, und die Grenze, die hier stand, ist weg.
 *
 *  Bis zum 02.09.2026 stand hier: „Dieses Plugin ist nicht übersetzbar."
 *  Das stimmte — 195 Zeichenketten in `__()` mit DEUTSCHEM Original und
 *  ohne Katalog.
 *
 *      Ein Satz in `__()`, dessen Quelle nicht Englisch ist und für den es
 *      keinen Katalogeintrag gibt, ist nicht übersetzbar. Er ist nur
 *      eingepackt.
 *
 *  Seit diesem Zug ist die Quelle ENGLISCH, der deutsche Katalog liegt in
 *  `languages/`, und `load_plugin_textdomain()` lädt ihn bei `init`. Was
 *  bleibt, ist eine kleinere, echte Grenze:
 *
 *  DIE KOPFKOMMENTARE BLEIBEN DEUTSCH — dieser hier auch. Sie erklären das
 *  Warum und sind an dieses Haus gerichtet, nicht an den Nutzer. In diesem
 *  Haus ist der Kommentar der Übergabewert; wer ihn übersetzt, übersetzt die
 *  Begründung weg. `zeuge-sprache.php` prüft sie deshalb ausdrücklich NICHT.
 *
 *  2 · DIE BESTÄTIGUNG DER FREMDMESSUNG HÄNGT AN DER GANZEN FASSUNG.
 *  `CV_Selftest::witness_confirmed()` vergleicht mit `CV_VERSION`. Jede
 *  neue Nummer kostet deshalb eine neue Messung — auch eine, die nur einen
 *  Kommentar geändert hat. Das ist grob, und es bleibt so:
 *
 *      Eine feinere Bindung müsste behaupten, WELCHE Änderungen den
 *      Nachweis berühren. Genau diese Behauptung ist die, die dieses
 *      Plugin über sich nicht aufstellen darf.
 *
 *  3 · DREI FEHLERSÄTZE SETZEN EINE SCHICHT DARÜBER VORAUS. Siehe
 *  `class-cv-notes.php`; seit 0.7.3 sagt die Seite es dazu.
 *
 * =====================================================================
 */

defined( 'ABSPATH' ) || exit;

/* ===========================================================================
 *  DER RIEGEL UM DIE DATEI — das vierte Stueck, Maillart URTEIL XVI §4
 * ===========================================================================
 *
 *  BEFUND 44: auf einem Knoten mit ZWEI Ordnern starb der zweite Lader.
 *  Repariert man nur die Deklarationen, stirbt er nicht mehr — und laeuft
 *  ein zweites Mal ueber die Dateiebene.
 *
 *      Ein Riegel an einer Datei, die auch auf Dateiebene arbeitet, macht
 *      aus einem lauten Tod eine stille Doppelanmeldung.   (Maillart)
 *
 *  Diese Datei setzt Haken, Aktivierungs- und Deaktivierungsrueckrufe und
 *  einen Filter. Jedes davon ein zweites Mal zu setzen stirbt nicht und
 *  faellt niemandem auf. Fuer ein Plugin-Hauptfile ist die Einheit deshalb
 *  nicht die Funktion, sondern die DATEI.
 *
 *  UND ER ALLEIN GENUEGT NICHT. PHP bindet unbedingte Deklarationen beim
 *  UEBERSETZEN — ein zweiter Ordner stirbt an `Cannot redeclare cv_boot()`,
 *  bevor dieser Riegel ein Wort sagen kann. Deshalb stehen die vier
 *  Funktionen weiter unten zusaetzlich in ihrem eigenen Riegel.
 *
 *      Ein Riegel um die Datei haelt die Dateiebene an. Ein Riegel um die
 *      Deklaration haelt das Uebersetzen an. Wer nur einen setzt, hat die
 *      Haelfte.
 *
 *  WORAN ER ERKENNT, DASS ER DER ZWEITE IST: an CV_VERSION. Die Konstante
 *  wird gleich darunter gesetzt, in DIESER Datei, und von keiner anderen.
 *  Ein Riegel, der nach etwas fragt, das woanders wohnen koennte, fragt
 *  nach dem Mieter statt nach dem Haus.
 * =========================================================================== */

/* DER ANZEIGENAME DES PRODUKTS — EINE SCHREIBSTELLE, und sie steht VOR dem
 * Riegel um die Datei.
 *
 *   WARUM DAVOR: die abgeschaltete Kopie braucht ihn fuer ihre Meldung
 *   (Platzhalter 3), und sie kehrt um, bevor sie ihr includes/ laedt. Was
 *   nach dem Riegel steht, hat sie nicht.
 *
 *   WARUM EINE VARIABLE UND KEINE KONSTANTE: Bismarck, 13.09. — define()
 *   auf eine stehende Konstante schlaegt fehl, laesst den ALTEN Wert
 *   stehen und meldet eine Warnung; mit Riegel schweigt es und die zweite
 *   Kopie meldet den Wert der ersten. Eine Variable je Datei leiht nichts
 *   und kollidiert nicht.
 *
 *   WARUM NUR EINE: derselbe Name steht weiter unten in der Anmeldung am
 *   Register. Zwei Formen derselben Wahrheit sind erlaubt, solange EINE
 *   geschrieben wird und die andere aus ihr faellt — hier faellt beides
 *   aus dieser Zeile. */
$cluster_one_pacioli_name = 'Pacioli';

$cluster_one_pacioli_lader_laeuft = ! defined( 'PACIOLI_VERSION' );

if ( ! $cluster_one_pacioli_lader_laeuft ) {
	/* -------------------------------------------------------------------
	 *  DIE ZWEITE TATSACHE — und sie ist nicht die von VERTRAG 27
	 *
	 *      liegt ist nicht laeuft — und zweimal liegen ist etwas anderes
	 *      als zweimal laufen.   (Maillart)
	 *
	 *  VERTRAG 27 meldet ORGANE, ueber `cluster_one_hier`. Diese Kopie hat
	 *  kein laufendes Organ: sie kehrt hier um und laedt ihr includes/ nie.
	 *  Dass sie DA LIEGT, ist eine andere Tatsache und braucht eine andere
	 *  Meldung.
	 *
	 *  OHNE RATSCHLAG, und das ist die Form, die Maillart in turing 3.9.9
	 *  gebaut hat — ich uebernehme sie und sage, dass sie seine ist:
	 *
	 *      In keiner Datei steht, welche der beiden die gewollte ist. Ein
	 *      Hinweis, der eine davon empfiehlt, raet — und zwar an der
	 *      Stelle, an der ein Mensch eine Entscheidung treffen muss.
	 *
	 *  BEIDE PFADE, weil ein Mensch sonst suchen muss, was er schon hat.
	 *
	 *  BENANNTE GRENZE: diese Kopie laedt keinen Katalog — sie kehrt vorher
	 *  um. Der Satz wird uebersetzt, WEIL die laufende Kopie dieselbe
	 *  Textdomain geladen hat. Das ist eine Annahme ueber den Knoten, nicht
	 *  ueber diese Datei, und sie steht hier, statt unbemerkt zu bleiben.
	 * ------------------------------------------------------------------- */
	add_action(
		'admin_notices',
		function () use ( $cluster_one_pacioli_name ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			printf(
				'<div class="notice notice-warning"><p>%s</p></div>',
				esc_html(
					sprintf(
						/* translators: 1: Pfad der wirksamen Kopie, 2: Pfad dieser, abgeschalteten Kopie, 3: Anzeigename des Produkts. */
						__( 'Cluster One — %3$s is installed twice on this site. The first copy is the one in effect; this one has switched itself off so the site does not die while loading. In effect: %1$s. Switched off: %2$s. Neither folder says which copy is the intended one, so this notice does not recommend one.', 'cluster-one-pacioli' ),
						defined( 'PACIOLI_FILE' ) ? PACIOLI_FILE : '?',
						__FILE__,
						$cluster_one_pacioli_name
					)
				)
			);
		}
	);

	return;
}


/* =====================================================================
 *  DIE FASSUNG WIRD ABGELEITET — 11.10.2026, Mammuts Entscheidung
 * =====================================================================
 *
 *  Bis heute stand sie ZWEIMAL in dieser Datei: im Kopffeld, das
 *  WordPress liest, und als getippte Konstante, die dieses Plugin liest.
 *  Gefunden am 10.10. beim Eintragen der Nummer, die Mammut vergeben
 *  hat: der Auftrag sagte „schreib 0.8.22 in den Lader", und es waren
 *  ZWEI Zeilen.
 *
 *      Ein Auftrag, der eine Zeile nennt und zwei trifft, ist kein
 *      ungenauer Auftrag. Er ist der Hinweis darauf, dass die Wahrheit
 *      zweimal dasteht.                                      (P1-12)
 *
 *  DREIZEHN PAKETE, KEINE ABWEICHUNG — meine acht und Mammuts fuenf.
 *  Es sind also nirgends Bestaetigungen liegen geblieben.
 *
 *      Dreizehn Pakete, in denen zwei getippte Zahlen gleich sind,
 *      belegen die Sorgfalt des Tippenden und nicht die Form der
 *      Datei.                                                (Mammut)
 *
 *  WELCHE IST DIE QUELLE? Der KOPF, und das ist nicht frei gewaehlt:
 *  WordPress liest die Datei als TEXT und kann nichts ableiten. Also
 *  ist der Kopf die Quelle und die Konstante der Leser.
 *
 *  DER RUECKFALL IRRT IN DIE SICHERE RICHTUNG. Ohne `get_file_data()`
 *  gilt '0.0.0-unbekannt' — und damit verfaellt JEDE Bestaetigung, statt
 *  dass eine haengen bleibt.
 *
 *      Ein Rueckfall, der sich zwischen zwei Fehlern entscheiden muss,
 *      nimmt den, der nichts stehen laesst.             (R-LEIHE-1)
 *
 *  WAS ES KOSTET: ein Dateilesen je Aufruf. `get_file_data()` liest die
 *  ersten 8192 Bytes der Datei, die WordPress gerade eingebunden hat —
 *  sie liegt im Zwischenspeicher des Betriebssystems. Gegen die
 *  Bestaetigung, die sonst nicht verfaellt, ist das billig.
 *
 *  GEMESSEN WIRD ES VON `zeuge-fassungszahl.php`:
 *      F2  die Konstante ist KEINE getippte Zahl, sondern abgeleitet
 *      F5  und die Ableitung GREIFT — gefahren, nicht gelesen
 * ===================================================================== */
define(
	'PACIOLI_VERSION',
	function_exists( 'get_file_data' )
		? (string) get_file_data( __FILE__, array( 'v' => 'Version' ) )['v']
		: '0.0.0-unbekannt'
);
define( 'PACIOLI_FILE', __FILE__ );
define( 'PACIOLI_DIR', plugin_dir_path( __FILE__ ) );

/* DER NAME, FUER DIE, DIE NICHT IN DIESER DATEI WOHNEN — 16.09.2026.
 *
 * `$c1_pacioli_name` steht WEITER OBEN und ist die einzige Schreibstelle.
 * Sie muss eine VARIABLE und muss VOR dem Dateiriegel stehen: die
 * abgeschaltete zweite Kopie braucht den Namen fuer ihre Meldung, und
 * `define()` auf eine stehende Konstante liesse den Wert der ERSTEN Kopie
 * stehen (Bismarck, 13.09.). Der Grund steht dort ausfuehrlich.
 *
 * NUR: eine Variable dieser Datei sieht `CV_Admin::render_my_ais()` nicht,
 * und seit heute braucht die Ueberschrift beider Seiten denselben Namen.
 *
 * DESHALB HIER UND NICHT OBEN: diese Zeile steht HINTER dem Riegel, an
 * derselben Stelle wie `CV_VERSION`. Die zweite Kopie kehrt vorher um und
 * erreicht sie nie — dieselbe Rechnung, die fuer `CV_VERSION` seit Wochen
 * traegt und die `zeuge-zwei-kopien.php` misst.
 *
 *     Eine Konstante, die nur die laufende Kopie setzt, leiht nichts.
 *
 * UND SIE IST EIN LESER, KEIN SCHREIBER. Der Wert kommt aus der Zeile
 * oben, er wird hier nicht ein zweites Mal getippt. Wer den Menuenamen
 * aendert, aendert die Ueberschrift mit — das ist der ganze Zweck.
 *
 *     P1-12 richtig herum: zwei Leser sind erlaubt, zwei Schreiber nie.
 */
define( 'PACIOLI_NAME', $cluster_one_pacioli_name );
/* 27.10.2026 (Neo), 0.8.34 — WERFT UND BODEN-STAND, EINMAL GESCHRIEBEN. Zwei
 * Leser: das Produktregister (cluster_one_produkt, unten) und der Stempel auf
 * „Meine KIs" (class-cv-admin.php). Pacioli traegt kern.php nicht, also keinen
 * cluster_one_stempel(); die Zeile baut es selbst, aus denselben zwei Werten.
 * 0.10.0 (Phi): kern.php liegt jetzt im Paket, der Stempel kommt aus cluster_one_stempel(); die beiden Konstanten bleiben
 * die Schreibstelle fuer das Register (werft, boden).
 * CV_BODEN_GEBAUT wird mit jeder Boden-Runde fortgeschrieben. */
define( 'PACIOLI_WERFT', 'Nemesus' );
define( 'PACIOLI_BODEN_GEBAUT', '1.23.0' );

/** Rollen der beiden KI-Kontotypen. */
const PACIOLI_ROLE_PERSONA = 'ai_persona';
const PACIOLI_ROLE_SYSTEM  = 'ai_system';

/** Capability, die einen Menschen berechtigt, eigene KIs zu betreiben. */
const PACIOLI_CAP = 'use_own_ai_connector';

/* 0.8.44 (30.10.2026, Neo) — JEDES MITGLIED DARF EIGENE KIS BETREIBEN.
 *
 * Bis 0.8.43 bekam CV_CAP nur die Administrator-Rolle (cv_activate) und, je
 * Mensch, wer sie vom SysOp in der Benutzerliste erhielt (cv_grant). Nemesus,
 * Sicherheitstest als MenschA auf der Main: „Pacioli muss angezeigt werden, aber
 * logischerweise immer nur mit eigenen Zugangsdaten." Seine Regel vom 30.10.:
 * Sehen duerfen alle, Tun nur mit Recht – und das Recht, die EIGENEN Schluessel
 * ins eigene Fach zu legen, hat jeder, der auf diesem Knoten dabei ist. Es sind
 * seine Schluessel, seine Rechnung, sein Fach; der SysOp verliert nichts ausser
 * einem Haken.
 *
 *     Ein Mensch, der dabei ist, braucht keine zweite Erlaubnis fuer das,
 *     was ohnehin nur ihm gehoert.
 *
 * WIE: dieselbe Form wie 'cluster_one_access' im Boden (kern.php, map_meta_cap).
 * Wer laut Boden dabei ist (Weissliste, SysOp, KI-Kennungen, Collective), hat
 * CV_CAP, ohne dass sie ihm geschrieben wird. Wer NICHT dabei ist, behaelt den
 * alten Weg: die Administrator-Rolle und die Vergabe je Mensch (cv_grant) gelten
 * weiter – der Filter laesst $caps dann unangetastet. Ohne Boden (Pacioli allein)
 * aendert sich nichts.
 *
 * BENANNTE GRENZE: 'dabei sein' sagt der Boden, nicht Pacioli. Faellt die
 * Weissliste, faellt mit ihr das automatische Recht – der explizite Weg bleibt.
 * 0.10.2 (Omega, 03.11.2026): der explizite Weg (Massenaktionen der Benutzerliste) ist fort — siehe class-cv-admin.php,
 * Ende der Klasse. Was bleibt, ist dieser Filter: Mitgliedschaft heisst Recht. */
add_filter( 'map_meta_cap', function ( $caps, $cap, $user_id ) {
	if ( PACIOLI_CAP !== (string) $cap ) {
		return $caps;
	}
	if ( ! function_exists( 'cluster_one_can_access' ) ) {
		return $caps;
	}
	return cluster_one_can_access( (int) $user_id ) ? array( 'exist' ) : $caps;
}, 10, 3 );

/*
 * =====================================================================
 *  DIE ZWEITE CAPABILITY — 08.10.2026, Teil 3 §5d
 * =====================================================================
 *
 *  Das Recht, die EINBAU-AUSKUNFT zu lesen: die zwoelf Pruefungen, das
 *  Woerterbuch „Wenn etwas nicht geht", den Weg zur INTEGRATION.md.
 *
 *  SIE STEHT HIER UND NICHT IN class-cv-admin.php, und das ist Mammuts
 *  eigene Vorgabe: „nach dem Muster von CV_CAP". Das Muster ist nicht
 *  die Schreibweise, sondern der WOHNORT — der Block zweihundert Zeilen
 *  weiter unten sagt es selbst: „CV_CAP wohnt in dieser Datei —
 *  gemessen, nicht erinnert."
 *
 *      Zwei Capabilities, die dasselbe tun und an zwei Orten wohnen,
 *      sind kein zweiter Schreibweg — aber die naechste wohnt dann im
 *      dritten.
 *
 *  VOREINSTELLUNG: NIEMAND. Sie wird nicht in cv_activate() vergeben.
 *  Wer `manage_options` hat, sieht den Reiter ohnehin (CV_Admin::darf) —
 *  diese Capability ist der Weg, einem NICHT-Administrator das Lesen zu
 *  geben, und setzt dann CV_CAP voraus.
 *
 *      Eine Voreinstellung soll den schuetzen, der nicht fragen wird.
 *                                                   (Christian, 08.10.)
 *
 *  UND SIE IST DER ERSTE BYTE AN DIESER DATEI SEIT 0.8.15. Gemeldet in
 *  der Lieferung: eine Zeile plus dieser Block, sonst nichts. Der
 *  Entfernt-Satz auf Zeile 13 und die drei Kopffelder sind NICHT
 *  angefasst — die warten weiter auf ihren Zug.
 * =====================================================================
 */
const PACIOLI_SEE = 'pacioli_see_internals';

/** Meta-Schlüssel am KI-Konto. */
const PACIOLI_META_OWNER    = '_pacioli_owner';
const PACIOLI_META_PROVIDER = '_pacioli_provider';
const PACIOLI_META_MODEL    = '_pacioli_model';
const PACIOLI_META_SECRET   = '_pacioli_secret';
const PACIOLI_META_STATUS   = '_pacioli_status';
const PACIOLI_META_PERSONA  = '_pacioli_persona';

/** Meta-Schlüssel am Menschen: sichergestellte, noch nicht zugeordnete Schlüssel. */
const PACIOLI_META_QUARANTINE = '_pacioli_quarantine';

/*
 * Wann ein Schluessel zuletzt geprueft wurde, und mit welchem Ausgang.
 *
 * 08.10.2026, Teil 3 §5b. Bis heute wurde nur der MISSERFOLG geschrieben
 * (CV_META_STATUS ueber mark_key_invalid); der Erfolg landete in
 * `?checked=ok` und war beim naechsten Seitenaufruf weg.
 *
 *     Ein geprueffter Schluessel war nach dem naechsten Seitenaufruf
 *     wieder ungeprueft. Ein durchgefallener blieb durchgefallen.
 *
 * Steht am KI-Konto und geht mit ihm: `wp_delete_user()` raeumt
 * Benutzer-Meta mit, also braucht die uninstall.php dafuer keine Zeile.
 * Gemessen, nicht angenommen — `zeuge-loeschliste.php` prueft die Liste
 * gegen den Quelltext.
 *
 * Inhalt: array( 'when' => int, 'ok' => bool ).
 */
const PACIOLI_META_CHECKED = '_pacioli_checked';

/** Werte für CV_META_STATUS. */
const PACIOLI_STATUS_ACTIVE      = 'active';
const PACIOLI_STATUS_PAUSED      = 'paused';
const PACIOLI_STATUS_KEY_INVALID = 'key_invalid';

/* ---------------------------------------------------------------------------
 * DER BODEN. Hingelegt von der Werkbank am 28.08.2026, nicht von diesem Haus.
 *
 * Connector Vault ist Light Nr. 2 der Fuenferreihe (STAND.md Fassung 6). Es
 * traegt genau EINE Boden-Datei: kasper.php. Nicht kern.php, nicht
 * speicher.php — es braucht sie nicht, und der Verteiler erweitert nicht.
 * [0.10.0 (Phi, 03.11.2026): seit heute nicht mehr wahr — Pacioli traegt den ganzen Boden, siehe den Block unter feynman.php.]
 *
 * WARUM DIESE ZEILE ZUM SCHNITT-AKT GEHOERT UND NICHT ZUR ANMELDUNG:
 * boden-verteilen.php ersetzt nur, was schon da liegt. Das erste Hinlegen ist
 * deshalb eine Entscheidung, keine Verteilung (H100). Und eine Datei, die
 * niemand laedt, ist kein Boden — sie ist eine Datei.
 *
 *     Wer eine Datei hinlegt, ohne sie laden zu lassen, hat abgelegt, nicht
 *     eingebaut.
 *
 * UNBEDINGT, UND DAS IST KEIN VERSEHEN: kasper.php traegt ihren Riegel selbst
 * (if (!function_exists('cluster_one_kasper')) ueber der ganzen Datei) und ruft
 * keine einzige fremde Cluster-One-Funktion. Sie darf deshalb vorne stehen und
 * braucht hier keine Bedingung — genau so laedt Mammut sie in cluster-one.php
 * ebenfalls.
 *
 * Ab dem naechsten Mal ist diese Datei ein Ziel von boden-verteilen.php wie
 * jedes andere Light. AENDERE SIE NIE HIER: der Boden wohnt in
 * schnitt-suite/boden/, und eine Aenderung an dieser Kopie meldet der
 * Verteiler beim naechsten Lauf, statt sie zu uebernehmen.
 *
 * DIE ANMELDUNG STEHT, seit 0.7.0: cluster_one_hier('pacioli') am Ende von
 * includes/api.php — in der Organdatei, nicht hier im Lader (H70). Bis 0.7.1
 * stand an dieser Stelle, sie fehle noch. Das war beim Hinlegen des Bodens wahr
 * und danach nicht mehr; gemessen und gemeldet hat es die Werkbank am 29.08.
 *
 *     Ein Kommentar, der einen Zustand beschreibt, wird falsch, ohne dass
 *     jemand ihn anfasst.
 *
 * Die Anmeldung ist NACKT — 'pacioli' ohne Fassungsnummer, waehrend die vier
 * geschnittenen Lights sich als '<haus>-light' mit eigener Fassung melden. Das
 * ist kein Rueckstand: Jene Konvention kommt aus floss.php, durch das dieses
 * Haus nie gegangen ist, und cluster_one_hier() traegt die Fassung beim FRAGEN
 * nach. Eine mitgegebene Nummer waere die zweite Quelle (P1-12).
 * Aenderung hier erst, wenn H112 einen Vertrag hat — nicht wegen eines Namens.
 * --------------------------------------------------------------------------- */
require_once __DIR__ . '/includes/kasper.php';

/* 0.9.5 (03.11.2026, Neo), Runde Tau — FEYNMAN LIEGT BEI. Nemesus: „Pacioli bekommt feynman.php und why.txt auch. Redundanz
 * ist der Preis fuer Modularitaet." feynman.php ruft beim Laden nur cluster_one_hier (kasper.php, oben) und traegt ihren
 * Riegel selbst; ihre Seiten brauchen kern.php, das Pacioli nicht traegt — angemeldet werden sie vom menue.php eines Boden-
 * Pakets auf demselben Knoten, und nur dann zeichnet die Leiste (class-cv-admin.php) den Reiter „Feynman". Liegt Pacioli
 * allein, liegt die Datei und schweigt. */
require_once __DIR__ . '/includes/feynman.php';

/* ===========================================================================
 *  0.10.0 (03.11.2026, Neo), RUNDE PHI — DER GANZE BODEN. Nemesus' Entscheidung vom 03.11. mittags: „Ich entscheide mich
 *  hiermit dafuer: Pacioli bekommt den ganzen Boden." Bis 0.9.6 lag hier nur kasper.php (und seit Tau feynman.php); der
 *  Absatz ueber kasper.php oben („traegt genau EINE Boden-Datei") beschreibt den Stand vom 28.08. bis zum 03.11.2026.
 *  Jetzt dieselben fuenf Dateien wie in jedem Light, byte-gleich bis auf die Textdomain (der Bau prueft es): anti-babel,
 *  kern, kranz, menue, speicher. Damit hat ein Knoten mit Pacioli allein das Dach, die Knoten-Reihe (Profil, Modi, System,
 *  Chronik, KI-Kennungen, Persoenlicher Bereich, Einrichtung) und Feynmans Seiten — wie jedes andere Haus. Liegt ein zweites
 *  Haus daneben, laeuft die zuerst geladene Kopie (H43), die anderen schweigen hinter ihrem Riegel.
 *  WAS PACIOLI ABGIBT: das Zeichnen seiner Leiste (cluster_one_render_tabs haengt Knoten und Feynman an) und den Stempel
 *  (cluster_one_stempel). WAS ES BEHAELT: seiten()/darf(), die Anmeldung seiner Seiten (add_pages; der add_menu_page-Zweig
 *  bleibt als benannter Rueckfall), Register, Seitenansprueche, Leiste auf Zuruf, den eigenen Katalog-Lader (H195).
 *  Reihenfolge wie im Light: alphabetisch; jede Datei traegt ihren Riegel, Reihenfolge ist keine Abhaengigkeit.
 * ========================================================================= */
require_once __DIR__ . '/includes/anti-babel.php';
require_once __DIR__ . '/includes/kern.php';
require_once __DIR__ . '/includes/kranz.php';
require_once __DIR__ . '/includes/menue.php';
require_once __DIR__ . '/includes/speicher.php';

require_once __DIR__ . '/includes/class-cv-crypto.php';
/* ---------------------------------------------------------------------------
 * class-cv-rueckgabe.php — DRITTER BYTE AN DIESER DATEI, 10.10.2026
 *
 * Die Klasse entscheidet, ob ein verwahrter fremder Schluessel beim Entfernen
 * zurueckgegeben werden kann. Zwei Stellen brauchen die Antwort:
 * `uninstall.php` beim Tun und der Entfernen-Schirm als Vorhersage.
 *
 * SIE MUSS HIER STEHEN, und das ist nicht Bequemlichkeit: ohne diese Zeile
 * gibt es die Klasse im LAUFENDEN Plugin nicht, die Vorhersage faellt auf
 * ihren Rueckfall, und auf dem Schirm stuende dauerhaft „kann hier nicht
 * gesagt werden".
 *
 *     Ein Rueckfall, der immer greift, ist kein Rueckfall. Er ist der
 *     Normalfall mit einer Entschuldigung davor.
 *
 * `uninstall.php` holt sie TROTZDEM selbst — dort ist nichts geladen, und
 * was dort geladen ist, ist eine Annahme, solange man es nicht fragt.
 * Zwei Ladewege auf eine Datei sind keine zwei Wahrheiten: `require_once`
 * laedt sie einmal, und der Klassenriegel darin faengt den Rest.
 * ------------------------------------------------------------------------- */
require_once __DIR__ . '/includes/class-cv-rueckgabe.php';
require_once __DIR__ . '/includes/class-cv-identities.php';
require_once __DIR__ . '/includes/class-cv-vault.php';
require_once __DIR__ . '/includes/class-cv-ausfuhr.php';
require_once __DIR__ . '/includes/class-cv-marker.php';
require_once __DIR__ . '/includes/class-cv-registry.php';
require_once __DIR__ . '/includes/class-cv-notes.php';
require_once __DIR__ . '/includes/class-cv-interceptor.php';
require_once __DIR__ . '/includes/class-cv-guard.php';
require_once __DIR__ . '/includes/class-cv-ratelimit.php';
require_once __DIR__ . '/includes/class-cv-selftest.php';
require_once __DIR__ . '/includes/class-cv-admin.php';
require_once __DIR__ . '/includes/api.php';

/* ---------------------------------------------------------------------------
 *  DER RIEGEL UM DIE DEKLARATIONEN — die andere Haelfte
 *
 *  Der Dateiriegel oben haelt die Dateiebene an. Er kann das UEBERSETZEN
 *  nicht anhalten: PHP bindet unbedingte Deklarationen, bevor die erste
 *  Anweisung laeuft, und ein zweiter Ordner stirbt dann an
 *  `Cannot redeclare cv_boot()`, ohne dass oben je etwas gepruefet wurde.
 *
 *  Gemessen am 11.09. an includes/api.php, und es hat mich einen Anlauf
 *  gekostet: nur eine Deklaration IM BLOCK wird nicht vorweg gebunden.
 *
 *  UND DIE REIHENFOLGE DER DATEIEBENE IST DANACH NEU ZU LESEN — Maillarts
 *  Vorwegbindung: was hier drin steht, gibt es erst, wenn der Riegel
 *  gelaufen ist. Die Dateiebene unten uebergibt nur NAMEN
 *  ('cv_boot', 'cv_activate'), ruft also nichts vorweg. W67 misst es.
 * ------------------------------------------------------------------------- */
if ( ! function_exists( 'pacioli_boot' ) ) {

	/**
	 * Startet das Plugin.
	 *
	 * Bei `plugins_loaded` und damit VOR `init` — dort holen sich die
	 * Anbieter-Plugins ihre Marke ab.
	 */
	function pacioli_boot() {
		Pacioli_Identities::init();
		Pacioli_Marker::init();
		Pacioli_Interceptor::init();
		Pacioli_Guard::init();

		if ( is_admin() ) {
			Pacioli_Admin::init();
			Pacioli_Selftest::init();
		}
	}
}

add_action( 'plugins_loaded', 'pacioli_boot', 5 );

/**
 * Die Anmeldung am Produkt-Register — VERTRAG 27 §4, 11.09.2026.
 *
 * =========================================================================
 *  WARUM HIER UND NICHT IN api.php
 * =========================================================================
 *
 *  Die Organ-Anmeldung (`cluster_one_hier`) ist eine Aussage ueber ein
 *  ORGAN und wohnt deshalb bei dem, was sie anmeldet — H70. Diese hier ist
 *  eine Aussage ueber das PRODUKT, und das Produkt ist dieses Plugin.
 *
 *      Zwei Anmeldungen, zwei Gegenstaende, zwei Orte. Wer sie
 *      zusammenlegt, hat nicht aufgeraeumt, sondern eine Unterscheidung
 *      verloren.
 *
 * DER SCHLUESSEL IST DER WIRT, UND ER WIRD NICHT GERATEN
 *
 *  Der Boden liest `$c1_angemeldet[$wirt]` — der Wirt ist der
 *  Plugin-Ordner. Ich schreibe ihn NICHT als Zeichenkette hin, obwohl ich
 *  weiss, wie mein Ordner heisst: dann gaebe es zwei Regeln dafuer, was
 *  mein Wirt ist, und meine waere die schlechtere. `cluster_one_kasper_wirt()`
 *  ist dieselbe Funktion, mit der der Boden selbst ableitet.
 *
 *      Wer den Namen seines eigenen Ordners hinschreibt, hat recht — bis
 *      jemand den Ordner umbenennt. Danach hat er ihn allein.
 *
 *  Gibt sie '' zurueck (die Datei liegt nicht unter WP_PLUGIN_DIR), melde
 *  ich mich GAR NICHT an, statt unter einem leeren Schluessel:
 *
 *      Leer heisst „nicht feststellbar", nicht „keiner".  (Kasper v0.9.75)
 *
 * DER SLUG WIRD ERST IM RUECKRUF GELESEN
 *
 *  `CV_Admin::PAGE` ist die EINE Quelle fuer die Adresse meiner Seite. Der
 *  Rueckruf laeuft, wenn das Menue gebaut wird — dann steht die Klasse
 *  sicher. Steht sie wider Erwarten nicht, melde ich einen LEEREN Slug und
 *  keinen geratenen: der Boden meldet das als „ohne-slug", und ein Produkt
 *  ohne Weg ist besser als eines mit falschem.
 *
 *  nr = 10 ist die Ordnung aus VERTRAG 21 §2a (10 · 20 · 30 · 40 · 50) und
 *  ausdruecklich KEINE Zahl aus einem Register dieses Hauses. Ich habe
 *  keines, aus dem ich versehentlich eine andere 10 nehmen koennte.
 */
add_filter(
	'cluster_one_produkt',
	function ( $c1_alle ) use ( $cluster_one_pacioli_name ) {
		if ( ! is_array( $c1_alle ) || ! function_exists( 'cluster_one_kasper_wirt' ) ) {
			return $c1_alle;
		}

		$c1_wirt = cluster_one_kasper_wirt( PACIOLI_FILE );

		if ( '' === $c1_wirt ) {
			return $c1_alle;
		}

		/* DAS VIERTE FELD — 'cap', seit dem 16.09.2026.
		 *
		 * Es kommt aus der ANMELDUNG und nicht aus dem Register, und der
		 * Grund ist Mammuts Satz aus LIEFERUNG 50 §2:
		 *
		 *     Nur das Produkt weiss, wer seine Seite sehen darf. Ein
		 *     Boden, der Capabilities vergibt, entscheidet ueber fremde
		 *     Haeuser.
		 *
		 * WARUM ES UEBERHAUPT GEBRAUCHT WIRD: das Dach meldet die Seite
		 * ab jetzt SELBST an, statt sie nur einzutragen. WordPress denkt
		 * Eintrag und Anmeldung nur dann zusammen, wenn sie aus derselben
		 * Zeile kommen — sonst gibt es keine admin.php?page=-Adresse und
		 * jeder Klick geht ins Leere. Wer anmeldet, braucht die
		 * Capability; und die gehoert mir.
		 *
		 * CV_CAP IST UNVERAENDERT. Hier wird sie genannt, nicht gesetzt:
		 * eine zweite Stelle, die eine Capability BESTIMMT, waere genau
		 * der zweite Schreibweg, den P1-12 verbietet. Diese Zeile LIEST
		 * die Konstante, die WEITER OBEN IN DIESER DATEI steht.
		 *
		 * BERICHTIGUNG, 16.09.2026: hier stand „includes/api.php". Das war
		 * falsch, und es war meins. CV_CAP wohnt in dieser Datei — gemessen,
		 * nicht erinnert. Eine Konstante, deren Wohnort in zwei Papieren
		 * verschieden steht, hat einen Wohnort und zwei Behauptungen.
		 *
		 * BENANNTE GRENZE, gemessen und nicht vermutet: der Boden, der in
		 * meinem Fach liegt (BODEN-ZUG-5/kern.php fc33e99d5cda), liest
		 * name, nr und slug — 'cap' kommt darin nicht vor. Die Zeile steht
		 * also heute geschrieben und ungelesen. Der Wortlaut ist Mammuts
		 * eigener aus LIEFERUNG 50 §3.1; ob das Register ihn so erwartet,
		 * misst erst Holleriths kern.php.
		 */
		/* DIE ORDNUNGSZAHL, 18.09.2026: 10 -> 60.
		 *
		 * Christians Entscheidung, RUNDBRIEF 21 §3. VERTRAG 21 §2a ist
		 * geaendert, und ich stehe ab jetzt hinten:
		 *
		 *     Kleinrock 10 · Bismarck 20 · Crockford 30 · Post Dolly 40
		 *     Pacioli 60
		 *
		 * DER GRUND IST KEINE RANGFOLGE, SONDERN EIN GEBRAUCH — und er ist
		 * seiner:
		 *
		 *     Ein Schluesseltresor ist Infrastruktur, kein Ort, an den man
		 *     geht. Wer „Cluster One" drueckt, will zur Suite.
		 *
		 * ES IST EINE ZIFFER UND KEINE NEUE LOGIK. `cluster_one_dach_ziel()`
		 * nimmt das erste INSTALLIERTE Produkt nach `nr`; dass ich nicht mehr
		 * das Ziel bin, faellt aus dieser Zahl und nicht aus einer Zeile, die
		 * mich ausnimmt. Eine zweite Ordnung neben dieser Zahl liesse
		 * Holleriths W80 Richtung 1b fallen.
		 *
		 *     Die Zahl ist EINE. Sie macht die Menuereihenfolge UND das
		 *     Dachziel UND die Reihenfolge der Wirte.
		 *
		 * 0.10.3 (13.11.2026, Neo): 60 -> 40. Nemesus beim Blick in die Sidebar:
		 * „Eigentlich sollte Post Dolly nach Pacioli stehen." Der Gang durch die
		 * Suite ist Knoten, Raeume, Kontext, Schluessel, und am Ende das
		 * Schaufenster. Der Grund vom 18.09. (Infrastruktur, kein Ort) gilt
		 * weiter fuer das DACHZIEL, und das bleibt Kleinrock (10); nur der
		 * Platz in der Reihe wandert. Jetzt:
		 *
		 *     Kleinrock 10 · Bismarck 20 · Crockford 30 · Pacioli 40 · Post Dolly 50
		 */
		$c1_alle[ $c1_wirt ] = array(
			'name' => $cluster_one_pacioli_name,
			'nr'   => 40,
			'slug' => class_exists( 'Pacioli_Admin' ) ? Pacioli_Admin::PAGE : '',
			'cap'  => PACIOLI_CAP,
			/* 27.10.2026 (Neo): WERFT UND BODEN-STAND – Erklaerung, kein Beweis (kern.php,
			   cluster_one_produkte). Pacioli traegt nur kasper.php vom Boden; der Stand ist
			   der des kasper.php, das hier liegt.
			   0.10.0 (Phi): Pacioli traegt den ganzen Boden; der Stand ist der aller sechs Boden-Dateien, die hier liegen. */
			'werft' => PACIOLI_WERFT,
			'boden' => PACIOLI_BODEN_GEBAUT,
		);

		return $c1_alle;
	}
);

/* 0.8.48 (31.10.2026, Neo): die Woerter dieses Hauses fuer die Chronik (Boden 1.19.5,
 * Filter cluster_one_chronik_woerter). Geschrieben werden die Ereignisse in
 * CV_Admin::handle_bulk, in der Benutzerliste, fuer CV_CAP (eigene KI betreiben). */
add_filter(
	'cluster_one_chronik_woerter',
	function ( $c1_w ) {
		if ( ! is_array( $c1_w ) ) {
			return $c1_w;
		}
		$c1_w['pacioli_granted'] = __( 'Pacioli: own AI released for a person', 'cluster-one-pacioli' );
		$c1_w['pacioli_revoked'] = __( 'Pacioli: own AI withdrawn from a person', 'cluster-one-pacioli' );
		/* 0.9.0 (02.11.2026, Neo): das Wort zur Wanderung der Namen, siehe pacioli_wanderung_0_9_0(). */
		$c1_w['pacioli_renamed'] = __( 'Pacioli: stored names moved from cv_ to pacioli_ (0.9.0)', 'cluster-one-pacioli' );
		return $c1_w;
	}
);

/* ===========================================================================
 *  MEINE ZWEI SEITEN MELDEN SICH — RUNDBRIEF 21 §2, 18.09.2026
 * ===========================================================================
 *
 *  Christians Befund an Kleinrock, und er trifft mich genauso: die Anmeldung
 *  nennt EINEN Slug, die Reiterleiste fuehrt mehrere Seiten. Auf allen ausser
 *  der angemeldeten stand „Cluster One" statt des Produktnamens.
 *
 *      Ein Produkt ist keine Seite. Ein Produkt ist eine Reiterleiste.
 *
 *  BEI MIR SIND ES ZWEI: `cv-my-ais` und `cv-selftest`. Die zweite ist die,
 *  die seit dem Umzug (Lieferung 52) keinen eigenen Menueeintrag mehr hat —
 *  und genau sie haette sich ohne diese Zeilen ab jetzt „Cluster One"
 *  genannt.
 *
 *  KEINE ZWEITE LISTE. Mammuts Vorlage tippt die Slugs in den Sammler; ich
 *  frage stattdessen `CV_Admin::seiten()` — dieselbe Quelle, aus der auch die
 *  Reiterleiste gezeichnet wird.
 *
 *      Wer hier eine zweite Liste tippt, hat zwei Wahrheiten ueber seine
 *      eigenen Seiten.                                    (Mammut, 21 §2)
 *
 *  ANGEHAENGT, NICHT ZUGEWIESEN — `$c1_a[] =`, nicht `$c1_a[…] =`. Post
 *  Dollys Messung vom 09.09. (R-SAMMLER-1): ein Sammler, der zuweist,
 *  verliert den zweiten Melder, bevor der Waechter ihn sehen kann. Bei MIR
 *  ist ein zweiter Melder heute kein Regelfall — aber die Form ist die des
 *  Hauses, und eine Ausnahme fuer den, der gerade allein ist, ist keine.
 *
 *  ANGEMELDET WIRD DER WIRT, ERFRAGT STATT GETIPPT:
 *  `cluster_one_kasper_wirt( CV_FILE )` — dieselbe Zeile wie in der
 *  Anmeldung oben. Ein getippter Wirt waere ein zweiter Schreibweg auf die
 *  Frage „wer bin ich".
 *
 *  DREI RIEGEL, UND JEDER HAT SEINEN GRUND:
 *
 *    is_array( $c1_a )                gibt ein anderes Haus etwas Fremdes
 *                                     zurueck, reiche ich es unveraendert
 *                                     weiter und mache es nicht schlimmer
 *    function_exists( 'cluster_one_kasper_wirt' )
 *                                     kasper.php liegt im Paket, aber der
 *                                     Filter kann in einer Welt laufen, in
 *                                     der er noch nicht gebunden ist
 *    class_exists( 'CV_Admin' )       faellt sie aus, melde ich NICHTS —
 *                                     und nicht eine getippte Ersatzliste
 *
 *      Ein Haus, das nicht antwortet, verhaelt sich wie bisher: die Seite
 *      heisst dann „Cluster One". Das ist der alte Weg und kein Schaden.
 *      Eine Ersatzliste waere die zweite Wahrheit, die dieser ganze
 *      Abschnitt vermeidet.                                    (R-ZUG-2)
 *
 *  GEMESSEN GEGEN: kern-NEU-1_12_9.php, 316034 B, sha1 62b1bf606310, wie er
 *  in meinem Fach liegt. RUNDBRIEF 21 §7 nennt zu derselben Groesse
 *  5a3cf5ceefb3 — der Unterschied steht im Vollzug 55 §6 und aendert an
 *  M101 nichts (12 gruen · 0 rot, selbst gefahren).           (R-ZUG-3)
 */
add_filter(
	'cluster_one_seite_ansprueche',
	function ( $c1_a, $c1_seite ) {
		if ( ! is_array( $c1_a )
			|| ! function_exists( 'cluster_one_kasper_wirt' )
			|| ! class_exists( 'Pacioli_Admin' ) ) {
			return $c1_a;
		}

		foreach ( Pacioli_Admin::seiten() as $c1_s ) {
			if ( $c1_s['slug'] === $c1_seite ) {
				$c1_a[] = cluster_one_kasper_wirt( PACIOLI_FILE );
				break;
			}
		}

		return $c1_a;
	},
	10,
	2
);

/* 28.10.2026 (Neo), Pacioli 0.8.36 — MEINE LEISTE AUF ZURUF. Seit Boden 1.18.0
 * zeichnen die Knoten-Seiten (Profil, System, Chronik, KI-Kennungen, Knoten-
 * Einstellungen) oben die Leiste des Hauses, von dem der Leser kam, darunter
 * die Knoten-Reihe — die Form des Kalenders. Der Boden ruft die Aktion mit dem
 * Wirt, der gemeint ist; ich vergleiche und zeichne nur dann. Dieselben Riegel
 * wie beim Sammler darueber: ohne Kasper weiss ich nicht, ob ich gemeint bin,
 * ohne CV_Admin habe ich nichts zu zeichnen. */
add_action(
	'cluster_one_leiste',
	function ( $c1_w, $c1_s ) {
		if ( ! function_exists( 'cluster_one_kasper_wirt' ) || ! class_exists( 'Pacioli_Admin' ) ) {
			return;
		}
		if ( $c1_w !== cluster_one_kasper_wirt( PACIOLI_FILE ) ) {
			return;
		}
		Pacioli_Admin::reiter( (string) $c1_s );
	},
	10,
	2
);

/* Riegel je Deklaration — die Vorwegbindung. Siehe der Block ueber cv_boot(). */
if ( ! function_exists( 'pacioli_load_textdomain' ) ) {
	/**
	 * Lädt den deutschen Katalog.
	 *
	 * AN `init`, NICHT FRÜHER. WordPress beschwert sich seit 6.7 über
	 * Übersetzungen, die vor `init` angefordert werden, und es hat recht: Wer
	 * früher lädt, lädt für einen Zeitpunkt, an dem die Sprache des Benutzers noch
	 * nicht feststeht.
	 *
	 * DIESE ZEILE IST ERST SEIT DIESEM ZUG RICHTIG. Vorher gab es keinen Katalog,
	 * und ein Lader ohne Katalog wäre eine Vorkehrung für etwas, das es nicht gibt
	 * — schlimmer noch: Er hätte Kataloge geladen, die auf die damaligen DEUTSCHEN
	 * Originale geschlüsselt waren, und die wären mit diesem Zug alle wertlos
	 * geworden.
	 *
	 *     Lader und Katalog kommen zusammen, oder gar nicht.
	 *
	 * Das Gegenstück ist H140: dort reist der Katalog mit und niemand lädt ihn.
	 * `zeuge-sprache.php` misst beide Hälften.
	 */
	function pacioli_load_textdomain() {
		load_plugin_textdomain( // phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound -- das Plugin liefert seine Uebersetzung in /languages selbst mit; WordPress laedt von allein nur Sprachpakete aus wp-content/languages
			'cluster-one-pacioli',
			false,
			dirname( plugin_basename( PACIOLI_FILE ) ) . '/languages'
		);
	}
}

add_action( 'init', 'pacioli_load_textdomain' );

/* ===========================================================================
 *  0.10.2 (03.11.2026 abends, Neo) — RUNDE OMEGA: die Massenaktionen der Benutzerliste sind fort (seit 0.8.44/0.8.45 hat jedes
 *  Mitglied das Recht; eine Vergabe je Mensch tat fuer Mitglieder nichts und fuer Nicht-Mitglieder etwas gegen die Hausregel),
 *  die Spalte „Developer info" mit ihnen; „Own AIs" bleibt. „Fuer Entwickler" sagt dem SysOp, dass die Mitgliedschaft das Recht
 *  ist, mit Zahl und Link zur Liste. Boden 1.23.0. readme: die Seite liegt unter Cluster One, nicht unter Tools.
 * ========================================================================= */

/* ===========================================================================
 *  0.10.1 (03.11.2026 nachmittags, Neo) — RUNDE PSI: nur Boden 1.22.2 (why.txt-Fusszeilen, kasper.php die Nummer). Sonst nichts.
 * ========================================================================= */

/* ===========================================================================
 *  0.10.0 (03.11.2026 nachmittags, Neo) — RUNDE PHI: DER GANZE BODEN. anti-babel, kern, kranz, menue, speicher liegen jetzt
 *  im Paket (siehe den Block bei den requires). Die Leiste zeichnet cluster_one_render_tabs (Knoten und Feynman kommen von
 *  dort), der Stempel kommt aus cluster_one_stempel; die eigenen Bloecke dafuer sind als Zitat in class-cv-admin.php
 *  vermerkt. Der deutsche Katalog traegt die Saetze des Bodens. Kein Verhalten der Schluesselmaske bewegt.
 * ========================================================================= */

/* ===========================================================================
 *  0.9.6 (03.11.2026 mittags, Neo) — RUNDE YPSILON: der Feynman-Reiter steht hinter „Knoten", als letzter (Nemesus nach
 *  dem Tau-Lauf auf der DEV). Pacioli_Admin::reiter, gleicher Block, anderer Platz. Boden 1.22.1. Sonst nichts.
 * ========================================================================= */

/* ===========================================================================
 *  0.9.5 (03.11.2026, Neo) — RUNDE TAU: FEYNMAN LIEGT BEI. Nemesus: „Pacioli bekommt feynman.php und why.txt auch.
 *  Redundanz ist der Preis fuer Modularitaet." feynman.php wird nach kasper.php geladen und deklariert; ihre Seiten meldet
 *  das menue.php eines Boden-Pakets auf demselben Knoten an, und nur dann zeichnet die Leiste (Pacioli_Admin::reiter) den
 *  Reiter „Feynman" vor „Knoten". Boden 1.22.0 (kasper.php). Der deutsche Katalog traegt Feynmans Saetze.
 * ========================================================================= */

/* ===========================================================================
 *  0.9.4 (02.11.2026, Neo) — RUNDE SIGMA: INTEGRATION.md heisst INTEGRATION.txt (Nemesus, wie why.txt: der
 *  Verzeichnis-Pruefer erwartet keine Markdown-Dateien im Stamm; Inhalt byte-gleich, zwei Stellen der
 *  Entwicklerseite nennen den Namen). Sonst nichts.
 * ========================================================================= */

/* ===========================================================================
 *  0.9.3 (02.11.2026, Neo) — RUNDE RHO: nur Boden 1.21.1 (kasper.php), sonst nichts.
 * ========================================================================= */

/* ===========================================================================
 *  0.9.2 (02.11.2026 abends, Neo) — RUNDE PI, DIE WARNUNGSRUNDE.
 *  Vermerke mit Grund an den verbliebenen Plugin-Check-Warnungen dieses Hauses: die Eingaben
 *  der Export/Import-Formulare (Dateiname durch Pacioli_Admin::dateiname; Passwoerter und
 *  Schluessel bleiben Bytes), die Direktzugriffe der Marke und der Rueckgabe auf die Optionstabelle,
 *  die Benutzersuche ueber Metas, load_plugin_textdomain. Boden 1.21.0. Kein Verhalten bewegt.
 * ========================================================================= */

/* ===========================================================================
 *  0.9.1 (02.11.2026 nachmittags, Neo) — DER GRIFF NACH TURING, REPARIERT.
 *
 *  0.9.0 rief Turing in class-cv-selftest.php noch unter den alten Namen (tu_durchgang,
 *  tu_version, tu_seite), waehrend Turing 3.15.0 in derselben Runde auf turing_ gezogen
 *  war: die Umbenennung lief je Paket, und das Inventar hatte die Rufe von Pacioli nach
 *  draussen nicht vermessen. Folge auf allen vier Knoten: „Turing is not on this node
 *  auf der Seite Fuer Entwickler, obwohl Turing lag; der Knopf „Mit Turing messen und
 *  eintragen fehlte, der Nachweis fuer 0.9.0 liess sich nicht setzen. Laut, aber mit
 *  falschem Grund. Jetzt turing_durchgang / turing_version, und den Link zum Zeugen
 *  nennt Turing selbst (turing_zeuge_url, ab Turing 4.0.0; Rueckfall fuer 3.15.0).
 *  Dazu die Reparatur der Praefix-Runde: $cluster_one_pacioli_name und
 *  $cluster_one_pacioli_lader_laeuft hiessen seit 0.9.0 ohne ihr p. Kein Verhalten
 *  bewegt ausser dem einen: der Knopf ist wieder da.
 * ========================================================================= */

/* ===========================================================================
 *  0.9.0 (02.11.2026, Neo) — DIE WANDERUNG DER NAMEN.
 *
 *  Nemesus' Entscheidung vom 02.11.2026: die Plugins gehen ins Verzeichnis von
 *  wordpress.org, und dessen Regel verlangt fuer alles Globale einen eindeutigen
 *  Praefix von mindestens vier Buchstaben. `cv_` (Connector Vault, der Name vor
 *  Pacioli) hat zwei. Seit 0.9.0 heisst alles `pacioli_` / `PACIOLI_` / `Pacioli_`:
 *  Funktionen, Klassen, Konstanten, Hooks, Fehlercodes, Nonces, Felder — und die
 *  Namen, unter denen dieses Plugin in der Datenbank schreibt.
 *
 *  Die Datenbank kennt die neuen Namen nicht von selbst. Diese Funktion traegt
 *  einmal je Knoten um, was unter den alten Namen liegt: zehn Optionen, neun
 *  Benutzer-Metas, ein Recht. Sie tauscht den NAMEN der Zeile, nicht ihren Wert —
 *  Wert und autoload bleiben Byte fuer Byte, nichts wird neu serialisiert. Was
 *  unter dem neuen Namen schon liegt, wird nicht ueberschrieben. Transients
 *  (cv_ablage_*, cv_einfuhr_*, cv_rl_*, cv_turing_*, cv_selftest_results) wandern
 *  nicht: sie sind Zwischenspeicher und laufen von selbst ab.
 *
 *  WAS NICHT WANDERN KANN: `CV_SECRET_KEY` in wp-config.php. Eine Konstante
 *  schreibt kein Plugin. Pacioli_Crypto liest deshalb beide Namen, den neuen
 *  zuerst, und der SysOp bekommt einen Hinweis, solange nur der alte gesetzt ist.
 *
 *  Die Wanderung hinterlaesst ein Chronik-Ereignis (pacioli_renamed) mit der Zahl
 *  der bewegten Zeilen — nichts verschwindet still.
 * ========================================================================= */
if ( ! function_exists( 'pacioli_wanderung_0_9_0' ) ) {
	function pacioli_wanderung_0_9_0() {
		if ( '0.9.0' === (string) get_option( 'pacioli_wanderung', '' ) ) {
			return;
		}
		global $wpdb;
		$pacioli_z = array( 'optionen' => 0, 'metas' => 0, 'rechte' => 0 );

		/* Zehn Optionen: die acht, die beim Entfernen bleiben (uninstall.php, PACIOLI_BLEIBT), der
		   Tresorschluessel der einfachen Stufe und die alte Antwort auf die Entfernen-Frage. */
		$pacioli_optionen = array(
			'cv_marker_salt'          => 'pacioli_marker_salt',
			'cv_known_providers'      => 'pacioli_known_providers',
			'cv_marker_leaks'         => 'pacioli_marker_leaks',
			'cv_marker_unbound'       => 'pacioli_marker_unbound',
			'cv_marker_no_secret'     => 'pacioli_marker_no_secret',
			'cv_marker_bound_calls'   => 'pacioli_marker_bound_calls',
			'cv_owner_gone'           => 'pacioli_owner_gone',
			'cv_witness_confirmation' => 'pacioli_witness_confirmation',
			'cv_secret_key'           => 'pacioli_secret_key',
			'cv_entfernen_daten'      => 'pacioli_entfernen_daten',
		);
		foreach ( $pacioli_optionen as $pacioli_alt => $pacioli_neu ) {
			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Wanderung einmal je Knoten: der Zeilenname wird getauscht, Wert und autoload bleiben Byte fuer Byte; danach wird der Options-Cache geleert
			$pacioli_da = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %i WHERE option_name = %s', $wpdb->options, $pacioli_neu ) );
			if ( 0 === $pacioli_da ) {
				$pacioli_n = $wpdb->query( $wpdb->prepare( 'UPDATE %i SET option_name = %s WHERE option_name = %s', $wpdb->options, $pacioli_neu, $pacioli_alt ) );
				$pacioli_z['optionen'] += (int) $pacioli_n;
			}
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			wp_cache_delete( $pacioli_alt, 'options' );
			wp_cache_delete( $pacioli_neu, 'options' );
		}
		wp_cache_delete( 'alloptions', 'options' );
		wp_cache_delete( 'notoptions', 'options' );

		/* Neun Benutzer-Metas: acht am KI-Konto bzw. am Menschen (PACIOLI_META_*), dazu `_cv_fremd`
		   (Vertrag cluster_one_tresor, api.php). Der Chiffretext bindet keinen Schluesselnamen;
		   Pacioli_Crypto::decrypt() liest ihn unter dem neuen Namen wie unter dem alten. */
		$pacioli_metas = array( '_cv_owner', '_cv_provider', '_cv_model', '_cv_secret', '_cv_status', '_cv_persona',
			'_cv_quarantine', '_cv_checked', '_cv_fremd' );
		foreach ( $pacioli_metas as $pacioli_alt ) {
			$pacioli_neu = '_pacioli_' . substr( $pacioli_alt, 4 );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Wanderung einmal je Knoten, Schluesselname der Zeile getauscht; der User-Meta-Cache wird darunter geleert
			$pacioli_n = $wpdb->query( $wpdb->prepare( 'UPDATE %i SET meta_key = %s WHERE meta_key = %s', $wpdb->usermeta, $pacioli_neu, $pacioli_alt ) );
			$pacioli_z['metas'] += (int) $pacioli_n;
		}
		if ( function_exists( 'wp_cache_flush_group' ) ) {
			wp_cache_flush_group( 'user_meta' );
		} else {
			wp_cache_flush();
		}

		/* Ein Recht: `cv_see_internals` (PACIOLI_SEE) steht je Mensch in wp_capabilities, nie an einer Rolle
		   (CV_Admin::handle_bulk, 'see_grant'). Wer es traegt, bekommt den neuen Namen und verliert den alten. */
		$pacioli_mit = get_users( array(
			'meta_key'     => $wpdb->get_blog_prefix() . 'capabilities', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Suche nach Benutzern ueber ein Meta (Besitz, Recht, Speicher): einmal je Aufruf einer Admin-Seite bzw. der Wanderung, kleine Knoten
			'meta_value'   => '"cv_see_internals"', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Suche nach Benutzern ueber ein Meta (Besitz, Recht, Speicher): einmal je Aufruf einer Admin-Seite bzw. der Wanderung, kleine Knoten
			'meta_compare' => 'LIKE',
			'fields'       => 'ID',
		) );
		foreach ( $pacioli_mit as $pacioli_uid ) {
			$pacioli_u = get_userdata( (int) $pacioli_uid );
			if ( $pacioli_u && isset( $pacioli_u->caps['cv_see_internals'] ) ) {
				$pacioli_u->add_cap( 'pacioli_see_internals' );
				$pacioli_u->remove_cap( 'cv_see_internals' );
				$pacioli_z['rechte']++;
			}
		}

		delete_transient( 'cv_selftest_results' );
		update_option( 'pacioli_wanderung', '0.9.0', false );

		if ( function_exists( 'cluster_one_event_emit' ) ) {
			cluster_one_event_emit( array(
				'type'           => 'pacioli_renamed',
				'container_type' => 'plugin',
				'container_id'   => 'pacioli_0_9_0',
				'actor_id'       => function_exists( 'cluster_one_actor_id_human' ) ? cluster_one_actor_id_human() : 'wp:' . get_current_user_id(),
				'summary_count'  => $pacioli_z['optionen'] + $pacioli_z['metas'] + $pacioli_z['rechte'],
			), true );
		}
	}
}
add_action( 'plugins_loaded', 'pacioli_wanderung_0_9_0', 1 );

/* 0.9.0: der Hinweis, solange der Tresorschluessel nur unter dem alten Namen in wp-config.php steht.
   Pacioli_Crypto liest ihn weiter (Rueckfall) — der Hinweis ist eine Bitte, kein Riegel. */
add_action( 'admin_notices', function () {
	if ( ! defined( 'CV_SECRET_KEY' ) || defined( 'PACIOLI_SECRET_KEY' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="notice notice-info"><p>' . esc_html__( 'Pacioli 0.9.0 reads the encryption key from PACIOLI_SECRET_KEY. This installation still defines the old name CV_SECRET_KEY in wp-config.php; it keeps working, but please rename the constant. Nothing else changes.', 'cluster-one-pacioli' ) . '</p></div>';
} );

/* Riegel je Deklaration — die Vorwegbindung. Siehe der Block ueber cv_boot(). */
if ( ! function_exists( 'pacioli_activate' ) ) {
	/**
	 * Aktivierung.
	 */
	function pacioli_activate() {
		if ( ! Pacioli_Crypto::is_available() ) {
			deactivate_plugins( plugin_basename( PACIOLI_FILE ) );
			wp_die(
				esc_html__(
					'Connector Vault requires the Sodium extension (part of PHP since 7.2). Without it, keys could only be stored in plain text.',
					'cluster-one-pacioli'
				),
				esc_html__( 'Activation cancelled', 'cluster-one-pacioli' ),
				array( 'back_link' => true )
			);
		}

		Pacioli_Identities::register_roles();
		Pacioli_Crypto::ensure_key();
		pacioli_wanderung_0_9_0(); // 0.9.0: bei der Aktivierung sofort, nicht erst beim naechsten Laden

		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->add_cap( PACIOLI_CAP );
		}

		// Vorhandene echte Schlüssel aus den Options-Zeilen holen, bevor sie
		// dort noch länger liegen. Sie landen verschlüsselt in der Quarantäne
		// des aktivierenden Menschen und werden durch Marken ersetzt.
		Pacioli_Marker::sweep( get_current_user_id() );
	}
}

register_activation_hook( __FILE__, 'pacioli_activate' );

/* Riegel je Deklaration — die Vorwegbindung. Siehe der Block ueber cv_boot(). */
if ( ! function_exists( 'pacioli_deactivate' ) ) {
	/**
	 * Deaktivierung.
	 *
	 * Die Marken bleiben in den Options-Zeilen stehen. Das ist Absicht: Ohne
	 * den Vault trägt jeder Aufruf eine wertlose Marke hinaus und scheitert
	 * mit 401 — für alle gleich. Ein zurückgelassener echter Schlüssel würde
	 * dagegen still weiter auf die Rechnung des Betreibers laufen.
	 */
	function pacioli_deactivate() {
		delete_transient( 'pacioli_selftest_results' );
	}
}

register_deactivation_hook( __FILE__, 'pacioli_deactivate' );
