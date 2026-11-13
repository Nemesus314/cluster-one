<?php
/**
 * Plugin Name:       Cluster One — Turing
 * Description:       Turing ist ein Werkzeug neben der Cluster-One-Suite. Es ist Installer und Pruefer in einem — und kann beides, weil es nicht dazugehoert. Es spielt Cluster One als SATZ auf, nicht als Stuecke: entpacken, pruefen, und den Knoten erst anfassen, wenn der ganze Satz zusammenpasst; scheitert etwas davor, ist nichts veraendert. Jedes Paket bekommt vorher eines von drei Urteilen: geprueft, abweichend, nicht gemessen.
 * Version:           4.0.6
 * Requires PHP:      7.4
 * Author:            Nemesus
 * License:           GPL-2.0-or-later
 * Text Domain:       cluster-one-turing
 *
 * ---------------------------------------------------------------------------
 * WARUM ES DIESES WERKZEUG GIBT — UND WARUM ES SO HEISST
 *
 * Es begann als "Pacioli-Zeuge": eine Messung fuer eine Frage. Mit 2.3.0 war
 * die Frage beantwortet, und uebrig blieb ein Instrument, das eine Maschine
 * von aussen befragt, ohne sie aufzuschrauben. Genau dafuer steht der Name —
 * nicht raten, was drinnen geschieht, sondern es an dem ablesen, was
 * herauskommt.
 *
 * Mit 3.0.0 zieht der SPIEGEL ein (Reiter 2), der bis dahin in Cluster One
 * selbst wohnte. Der Grund ist derselbe wie bei Galvani: Ein Diagnose-Ausgang
 * ist ein Werkzeug und kein Organ. Er gehoert unter "Werkzeuge", nicht in den
 * Kontrollraum des Produkts, und in einem ausgelieferten Plugin hat er nichts
 * verloren.
 *
 * WAS BEIM UMZUG IN CLUSTER ONE BLIEB — und warum das kein Rest ist:
 * Die Umgebungs-Notiz (Randbedingungen, Fingerabdruck, "veraltet"-Warnung,
 * Bismarck-Praeambel) trug den Namen 'spiegel', ist aber nie Spiegel gewesen.
 * Der Dump ruft sie nicht auf, sie haengt an zwoelf Stellen im Produkt, und
 * sie ist der Keim eines eigenen Plugins. Umgezogen sind ausschliesslich die
 * DREI Funktionen des Dumps.
 *
 * ---------------------------------------------------------------------------
 * REITER 1 — DER PACIOLI-ZEUGE: WARUM ES IHN GIBT
 *
 * Connector Vault will je Mensch einen eigenen KI-Schluessel einsetzen und
 * schaltet dafuer waehrend eines Aufrufs den Optionswert um
 * (pre_option_connectors_ai_<provider>_api_key). Sein eigener Selbsttest misst
 * diese Umschaltung mit get_option() — und dort ist sie beweisbar in Ordnung:
 * pre_option_ laeuft in get_option() ganz oben, vor dem Cache. Diese Pruefung
 * kann gar nicht anders als gruen zu werden.
 *
 * Der gefaehrliche Fall liegt eine Schicht hoeher und wird dort nicht beruehrt:
 * Holt der Client den Schluessel einmal je Request in seine eigene Registry,
 * bekommt der zweite Aufruf den ersten Schluessel.
 *
 * Dieser Zeuge sieht es, weil er nicht die Optionsschicht befragt, sondern den
 * tatsaechlich hinausgehenden Request abfaengt und nachschaut, WELCHER
 * Schluessel darin steht.
 *
 * ---------------------------------------------------------------------------
 * WAS ER TUT UND WAS NICHT
 *
 * Er schreibt KEINE Option, KEINE Benutzer-Meta, KEINE Tabelle, KEINE
 * Transiente. Alle Ergebnisse stehen auf der Seite, die den Durchgang
 * ausgeloest hat, und nirgends sonst.
 *
 * v3.2.0 — DIESE ZEILEN STIMMEN WEITER, UND EINE MEHR IST NOETIG GEWORDEN.
 * Der Installer schreibt ins DATEISYSTEM: nach wp-content/plugins/, auf
 * Knopfdruck eines Menschen mit install_plugins, und nur das, was er
 * ausgewaehlt hat. Dazu eine Marke (.turing-satz.json) im selben
 * Verzeichnis, die einen unvollstaendigen Vorlauf bezeugt und nach einem
 * gelungenen Lauf wieder verschwindet.
 *
 * Die geprueften NULL Datenbank-Schreibvorgaenge bleiben. Sie waren die
 * Eigenschaft, und sie sind nicht gefallen — sie sind praeziser geworden:
 * ein Werkzeug, das die Datenbank nicht anfasst, kann den Zustand, den es
 * diagnostiziert, nicht heimlich veraendern. Das Dateisystem faellt nicht
 * unter diesen Satz, weil ein Mensch jeden einzelnen Zug ausloest und die
 * Liste vorher sieht.
 *
 * GET laeuft durch — die Modell-Erkundung (/v1/models) kostet nichts und
 * verlaesst das Haus bei jedem normalen Aufruf ohnehin. POST wird gelesen und
 * abgebrochen: dort steckt der Prompt, dort steckt das Geld. Es entstehen
 * keine Token-Kosten, und kein Gespraechsinhalt geht hinaus.
 *
 * Er zeigt NIE einen Schluessel. Verglichen wird ueber einen gesalzenen
 * 8-Zeichen-Abdruck; der rohe Wert stirbt, sobald seine Heimat gefunden ist.
 *
 * ---------------------------------------------------------------------------
 * FUENF FEHLER, DIE DIESES WERKZEUG SELBST GEMACHT HAT
 *
 * Sie stehen hier, weil sie alle von derselben Sorte waren: Ein Zeuge, der aus
 * dem falschen Grund etwas meldet, ist schlimmer als keiner.
 *
 * 1.0.0  Suchte die Schluessel-Option nach GERATENEN Namensmustern. Auf einem
 *        Knoten ohne eingerichteten Schluessel meldete er "0 Kandidaten" —
 *        richtig, aber aus dem falschen Grund. Seit 1.1.0 haengt der Lauscher
 *        an ALLEN Optionsnamen; was gelesen wird, zeigt der Aufruf selbst.
 *
 * 1.1.0  Brach JEDEN Request ab, also auch die GET-Erkundung der Modelle.
 *        Daraufhin hielt der Client alle Anbieter fuer unbenutzbar, der Aufruf
 *        endete bei 'no_key', und der Zeuge meldete das Ergebnis seiner
 *        eigenen Sabotage. Seit 1.2.0: GET durch, POST abbrechen.
 *
 * 1.2.0  Trug den ROHEN Schluessel im Ergebnis-Array mit. Ausgegeben wurde er
 *        nie — aber als in 1.3.0 das Ergebnis durch ein verstecktes
 *        Formularfeld reisen sollte, waere er im HTML gelandet. Er stirbt
 *        seither, sobald seine Heimat gefunden ist.
 *
 * 1.3.0  Liess den Menschen EINE Option zum Umschalten waehlen und setzte
 *        'selected' auf jeden Eintrag — der Browser nahm den letzten. So wurde
 *        der Schluessel von xAI umgeschaltet, waehrend der Aufruf zu OpenAI
 *        ging. Urteil: ROT. Richtig gemessen, falsche Sache. Seit 1.4.0
 *        werden ALLE gefundenen Schluessel-Optionen gleichzeitig umgeschaltet;
 *        die Wahl gibt es nicht mehr.
 *
 * 1.3.0  Meldete im Bericht eine hartkodierte Versionsnummer, waehrend eine
 *        andere lief — zwei Stellen fuer dieselbe Wahrheit, derselbe Fehler
 *        wie in Cluster One 0.9.64. Seit 1.4.1 wird sie aus dem Datei-Kopf
 *        gelesen.
 *
 * 2.0.0  Prueft ALLE eingerichteten Anbieter in EINEM Durchgang und faehrt
 *        beide Runden automatisch nacheinander. Die benannte Grenze "nur ein
 *        Anbieter geprueft" faellt damit weg, statt verwaltet zu werden — und
 *        das Auswahlfeld, das in 1.3.0 den Fehlalarm ausgeloest hat, ist fort.
 *
 * ---------------------------------------------------------------------------
 * BENANNTE GRENZEN, DIE BLEIBEN
 *
 * 1. Gesehen wird nur, was durch die WordPress-HTTP-API laeuft (wp_remote_*).
 *    Benutzt die SDK cURL direkt, sieht dieser Zeuge nichts — und sagt das,
 *    statt Gruen zu melden.
 * 2. Geprueft wird das Verhalten innerhalb EINES Requests. Was ein
 *    Objekt-Cache ueber Requests hinweg festhaelt, ist eine andere Frage.
 * 3. Der Befund gilt fuer die Fassung des Unterbau-Plugins, die der Bericht
 *    nennt. Ohne diese Zeile ist er in drei Monaten wertlos.
 * 4. Die Beobachtung "welche Optionen wurden gelesen" gilt fuer den ERSTEN
 *    Aufruf des Durchgangs. Danach ist der Client warm, und ein warmer Client
 *    liest nichts mehr — das ist kein Befund, das ist der Aufbau.
 * ---------------------------------------------------------------------------
 */

defined('ABSPATH') || exit;

/* ==========================================================================
   v3.9.9 — EINE ZWEITE KOPIE DIESER DATEI TOETET DEN KNOTEN. JETZT NICHT MEHR.

   Mammut hat am 12.09.2026 eine Aufgabe an alle sieben Haeuser gegeben:
   Paciolis Kindlauf (w64-kindlauf.php) gegen die eigenen Dateien fahren. Er
   laedt eine Datei ZWEIMAL in einem eigenen Prozess und misst damit das
   ANKOMMEN statt des Sprechens. Gegen turing.php 3.9.8:

       Cannot redeclare function tu_log()            rc 255

   Das ist sein dritter Ausgang, und er ist auf einem Knoten ein weisser
   Bildschirm: ein Fehler beim UEBERSETZEN, den kein try/catch faengt.

   GEMESSEN (probe-die-zweite-kopie.php, 12.09.):
       61 benannte Funktionen, 0 davon hinter einem Riegel
       get_plugins() zaehlt JEDE .php eine Ebene tief als eigenes Plugin
           -> turing/turing.php und turing/turing-sicherung.php sind ZWEI
              Plugins, beide einschaltbar, beide mit denselben 61 Namen.
              Eine Kopie neben das Original zu legen ist die haeufigste
              Handbewegung der Welt.
       mein eigener Hangar kann es NICHT ausloesen: der Ausweichordner heisst
           .turing-alt (Punkt!) und liegt eine Ebene tiefer, und get_plugins()
           ueberspringt Punkt-Eintraege in beiden Ebenen. Der Punkt ist also
           kein Schmuck, sondern der Schutz — das stand bis heute nirgends.

   WARUM NICHT EINFACH JEDE FUNKTION RIEGELN, wie es eine Organdatei tut:
   diese Datei arbeitet auch auf DATEIEBENE (zwei Haken, die Meldung bei
   Kasper, der HTTP-Lauscher). Ein Riegel je Funktion liesse die zweite Kopie
   ueberleben — und sie wuerde dieselben Haken ein ZWEITES Mal setzen.

       Ein Riegel an einer Datei, die auch auf Dateiebene arbeitet, macht aus
       einem lauten Tod eine stille Doppelanmeldung.

   Fuer eine Organdatei ist der Riegel richtig, weil sie in fremde Pakete
   mitreist und dort nur Funktionen beitraegt. Fuer ein Plugin-Hauptfile ist
   die Einheit nicht die Funktion, sondern die DATEI.

   DESHALB STEHT HIER EIN RIEGEL UM DIE GANZE DATEI, und er ist laut:
   die zweite Kopie laedt nichts und setzt keinen Haken — und sagt auf der
   Oberflaeche, DASS es sie gibt, mit BEIDEN Pfaden.

   v3.9.10 — UND SIE MELDET SICH JETZT DOCH BEI KASPER, mit deklariert=false.
   Bis 3.9.9 tat sie es nicht, und das war eine ABWEICHUNG VON VERTRAG 27 §7:

       „je Organ mit mehr als einer Kopie: die Zahl der Anmeldungen ist gleich
        der Zahl der Pakete, die die Datei tragen."

   Gemessen an Mammuts eigenem Boden in zwei Ordnern (14.09.2026): boden
   meldet sich ZWEIMAL — einmal deklariert=true, einmal false. Das ist die
   richtige Auskunft, und turing gab sie nicht: es lag zweimal und stand
   einmal in der Landkarte.

       Eine Forderung, die eine Abweichung vom Vertrag festschreibt, ist kein
       Zeuge, sondern eine Ausnahme mit Kreuz.                    (Maillart)
       Eine Ausnahme, die in einem Zeugen steht, ueberlebt jeden, der weiss,
       warum sie da ist.                                  (Mammut, 14.09.)

   T26 FORDERUNG 6 hat diese Abweichung VERLANGT. Sie ist umgebaut, die
   Abnahmezeile steht jetzt als FORDERUNG 8 da, und sie war gegen 3.9.9 ROT —
   ein ehrliches Rot gegen den eigenen Vertrag, vier Tage lang.

       liegt ist nicht laeuft — und zweimal liegen ist etwas anderes als
       zweimal laufen. Diese Zeile macht aus dem einen nicht das andere,
       sondern aus einem Absturz eine Auskunft.

   Die Auskunft nennt keine Reparatur, weil Turing nicht weiss, welche der
   zwei Kopien gewollt ist. Sie nennt die zwei Pfade und die zwei Fassungen;
   entscheiden tut ein Mensch. T26 faehrt beide Lagen.
   ========================================================================== */
if (defined('TURING_ERSTE_KOPIE')) {
    if (function_exists('add_action')) {
        $turing_zweite = __FILE__;
        add_action('admin_notices', function () use ($turing_zweite) {
            if (function_exists('current_user_can') && ! current_user_can('manage_options')) { return; }
            $erste = (string) constant('TURING_ERSTE_KOPIE');
            $h = function ($s) {
                return function_exists('esc_html') ? esc_html((string) $s) : htmlspecialchars((string) $s, ENT_QUOTES);
            };
            echo '<div class="notice notice-error"><p><strong>Turing liegt zweimal aktiv auf diesem Knoten.</strong> '
               . 'Geladen und wirksam ist die erste Kopie; die zweite hat sich <strong>abgeschaltet</strong>, '
               . 'damit der Knoten nicht beim Laden stirbt (<code>Cannot redeclare</code>).</p>'
               . '<p style="font-size:12px;">wirksam: <code>' . $h($erste) . '</code><br>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $h ist sechs Zeilen hoeher esc_html (htmlspecialchars als Rueckfall ohne WordPress)
               . 'abgeschaltet: <code>' . $h($turing_zweite) . '</code></p>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $h ist sieben Zeilen hoeher esc_html (htmlspecialchars als Rueckfall ohne WordPress)
               . '<p style="font-size:12px;">Turing sagt <strong>nicht</strong>, welche der beiden die '
               . 'gewollte ist — das steht in keiner Datei. Solange beide eingeschaltet sind, gilt die, '
               . 'die WordPress zuerst laedt, und das ist die alphabetische Reihenfolge ihrer Pfade.</p>'
               /* v3.9.10 — und die Seite sagt dasselbe wie die Tabelle. Vorher
                  sagte die Seite „es liegt zweimal" und die Landkarte „einmal";
                  zwei Auskuenfte desselben Hauses, die sich widersprechen, sind
                  schlimmer als eine fehlende. */
               . '<p style="font-size:12px;">In Kaspers Landkarte steht diese zweite Kopie als '
               . '<strong>angemeldet, aber nicht deklarierend</strong> — dort ist also beides zu sehen: '
               . 'dass turing zweimal liegt, und welche Kopie laeuft.</p></div>';
        });
    }
    /* v3.9.10 — DIE ZWEITE KOPIE MELDET SICH, UND ZWAR ALS NICHT-DEKLARIERENDE.
       Grund: VERTRAG 27 §7 (oben im Block). Drei Dinge an dieser Zeile sind
       Absicht:

       a) Die Fassung kommt aus dem EIGENEN Dateikopf und nicht aus
          tu_version(). tu_version() ist von der ERSTEN Kopie deklariert und
          liest deren __FILE__ — sie wuerde die Fassung der ersten Kopie
          nennen, und dann waere der Melder eine Auskunft ueber jemand anderen.
              Ein Melder kennt sich selbst. (Maillart, VERTRAG 27 §2)
       b) get_file_data() steht auf einem Knoten lange vor den Plugins. Fehlt
          sie (ein Pruefstand ohne WordPress), bleibt die Fassung LEER, und
          der Boden setzt seine eigene ein. Eine leere Fassung ist eine
          ehrlichere Auskunft als die fremde.
       c) Es ist EIN Aufruf hinter EINER Wache, und er laeuft nur, wenn ein
          Boden da ist. Die Dateiebene der zweiten Kopie bleibt damit so kurz,
          wie sie sein muss — und das war mein einziger Einwand gegen diese
          Zeile. Mammut, 14.09.: „Wenn er teuer ist, ist er messbar teuer;
          dann sag es mit einer Zahl und ich nehme es zurueck." */
    if (function_exists('cluster_one_hier')) {
        $turing_eigene_fassung = '';
        if (function_exists('get_file_data')) {
            $turing_kopf = get_file_data(__FILE__, array('Version' => 'Version'), 'plugin');
            $turing_eigene_fassung = (string) ($turing_kopf['Version'] ?? '');
        }
        cluster_one_hier('turing', $turing_eigene_fassung, false);
    }
    return;      /* AB HIER LAEDT DIESE KOPIE NICHTS MEHR. */
}
define('TURING_ERSTE_KOPIE', __FILE__);
/* 3.14.6 (28.10.2026, Neo): DIE WERFT – wer gebaut hat. Erklaerung, kein Beweis, wie
   bei den Produkten (kern.php, cluster_one_stempel_teile). Ein Leser: die Stempelzeile
   unter „Der Installer" (tu_seite_installer). Turing steht nicht im Produktregister und
   traegt kern.php nicht, also baut es die Zeile selbst, wie Pacioli (CV_WERFT). Einen
   Boden-Stand nennt sie NICHT: Turing traegt mit Absicht keinen Boden (Vertrag 16 §5,
   Kopf dieser Datei), und „gebaut auf Boden …" waere eine Angabe ueber etwas, das es
   nicht hat. */
define('TURING_WERFT', 'Nemesus');

const TURING_MARKE_B = 'TURINGMARKEBBBBB';

/* ==========================================================================
   v3.1.0 — TURING MELDET SICH BEI KASPER, TRAEGT IHN ABER NICHT MIT.

   Kasper ist Cluster Ones Melderegister: jedes Organ traegt sich beim Laden
   ein, und wer fragt, bekommt eine Landkarte aus Organ, Fassung und WIRT.
   Turing gehoert da hinein — er ist Teil des Hauses, auch wenn er kein Produkt
   ist.

   ABER NICHT ALS KOPIE. kasper.php enthaelt genau einen Schreibvorgang (den
   Zaehler fuer zu frueh gestellte Fragen). Turings einzige wirkliche
   Eigenschaft ist die maschinell gepruefte Null an Schreibvorgaengen — eine
   Kopie von Kasper wuerde sie toeten.

       Ein Versprechen, das durch einen Entschluss brechbar ist, ist Werbung;
       eines, das nur durch Umbau brechbar ist, ist eine Eigenschaft.

   Deshalb zwei geriegelte Zeilen statt einer Datei: ist Cluster One da, meldet
   Turing sich an und kann fragen. Ist es nicht da, sagt der Kasper-Reiter das
   und hoert auf.

   REIHENFOLGE, GEMESSEN STATT ANGENOMMEN: WordPress sortiert active_plugins als
   Zeichenketten "ordner/datei.php". Der Ordner "turing" steht hinter
   "cluster-one" und hinter jedem "cluster-one-..."-Light, also laedt Turing als
   LETZTER der Familie. Kasper steht zu diesem Zeitpunkt immer schon. Das ist
   Glueck und nicht Planung — deshalb steht der Riegel trotzdem da.

   (Und eine Lehre nebenbei: hier stand erst 'cluster-one-*' mit einem
   Schraegstrich dahinter. Diese zwei Zeichen beenden einen Blockkommentar. Der
   Rest der Datei war ploetzlich Code, php -l hat es in derselben Minute
   gemeldet — der billigste Waechter im Haus, und der erste, der spricht.)
   ========================================================================== */
/* v3.9.9 — DIESE MELDUNG IST NACH UNTEN GEWANDERT, und der Grund ist gemessen:
   seit alle Funktionen dieser Datei hinter einem Riegel stehen (siehe den Block
   oben), werden sie NICHT MEHR VORWEGGEBUNDEN. Gemessen:

       unbedingt  function_exists('x') VOR der Deklarationszeile   ->  ja
       geriegelt  dieselbe Frage                                   ->  nein

   Ein Aufruf von tu_version() an DIESER Stelle lief bis 3.9.8, weil PHP
   unbedingte Deklarationen schon beim Uebersetzen bindet. Mit dem Riegel waere
   er „Call to undefined function tu_version()" — also steht die Meldung jetzt
   hinter der Funktion, die sie braucht.

       Ein Riegel nimmt der Deklaration die Vorwegbindung. Wer riegelt, muss
       die Reihenfolge seiner Dateiebene noch einmal lesen.

   DAS IST DIE ZEILE, DIE AN ALLE SIEBEN HAEUSER GEHOERT: wer nach Mammuts
   Aufgabe jede Funktion riegelt und auf Dateiebene eine eigene Funktion ruft,
   die weiter unten steht, tauscht ein „Cannot redeclare" gegen ein „Call to
   undefined" — und merkt es erst auf dem Knoten. */

/* ==========================================================================
   Aufzeichnung. Lebt nur fuer die Dauer eines Requests.
   ========================================================================== */
if (! function_exists('turing_log')) {
function &turing_log(): array {
    static $log = array('options' => array(), 'requests' => array());
    return $log;
}
}

/* ==========================================================================
   DIE 63 RIEGEL SIND EIN ZUSTAND UND KEIN PRINZIP.
   v3.9.10 — Mammuts Bitte aus PROMPT 17 §2 (Punkt 4), und er hat recht:

       Wenn Christian turing.php je aufteilt, fallen 63 Riegel weg und die
       Einheit ist wieder die DATEI.

   Die 63 „if (! function_exists(...))" unter dieser Zeile sind die Folge
   davon, dass Hauptdatei und Leib in EINER Datei liegen. Fuer eine
   Organdatei ist der Riegel je Funktion richtig; fuer ein Plugin-Hauptfile
   ist die Einheit die Datei, und dafuer steht der Riegel GANZ OBEN. Beide
   stehen hier nur, weil beides dieselbe Datei ist.

   Wer sie beim Aufteilen stehen laesst, hat 63 Zeilen, die niemand liest und
   die nichts mehr bewachen. Wer sie entfernt, ohne den Riegel oben zu
   pruefen, hat wieder einen weissen Bildschirm.

       Eine Vorsichtsmassnahme ohne ihren Grund daneben wird beim naechsten
       Umbau entweder heilig oder weggeworfen — und beides ist falsch.

   Der Satz stand seit dem 13.09. in zwei Papieren und nicht im Quelltext.
   Jetzt steht er hier, wo der Naechste ihn findet.
   ========================================================================== */

/* ==========================================================================
   Selbstauskunft
   ========================================================================== */

/** Die Version wird GELESEN, nicht getippt — eine Quelle, der Datei-Kopf. */
if (! function_exists('turing_version')) {
function turing_version(): string {
    $d = get_file_data(__FILE__, array('Version' => 'Version'), 'plugin');
    return isset($d['Version']) ? (string) $d['Version'] : '?';
}
}

/* ==========================================================================
   4.0.6 (03.11.2026 nachmittags, Neo), RUNDE PSI — EINE RICHTIGSTELLUNG UND why.txt.
   RICHTIGSTELLUNG: der Block 4.0.5 sagt „Diagnostics 4.0.4 folgt". Das war falsch, und die DEV hat es gemeldet
   (stable_tag_mismatch 4.0.4 != 4.0.5): der Schnitt uebernimmt den Kopf der Quelle Zeile fuer Zeile, also traegt ein
   neu geschnittenes Diagnostics IMMER die Nummer der Quelle — 4.0.5 damals, 4.0.6 jetzt. Die readme bekam am 03.11. mittags
   die 4.0.5; die Datei selbst war richtig. Regel seitdem: Diagnostics heisst wie die Quelle, aus der es geschnitten ist.
   why.txt (Boden 1.22.2): die Fusszeilen nannten die Datei noch why.md; jetzt why.txt mit Boden-Stand und Datum. Weil die
   Datei in beiden Turing-Ordnern liegt, bewegen sich beide Pakete — Vegas: ein Zip, eine Nummer, eine Byte-Folge.
   Kein PHP bewegt ausser diesem Verlauf.
   ========================================================================== */

/* ==========================================================================
   4.0.5 (03.11.2026 mittags, Neo), RUNDE YPSILON — DER REITER FEYNMAN, IN BEIDEN WERKZEUGEN. Nemesus: „Immerhin waere es
   moeglich, dass jemand das Ding zuerst installiert. Beim Turing mit Installer von GitHub ist das sogar wahrscheinlich. Und
   dann soll der User sofort wissen, worum es geht." Turing traegt keinen Boden, also gibt es ohne Haus keine Feynman-Seite,
   auf die ein Verweis zeigen koennte — Turing traegt den Text selbst: turing_seite_feynman() (letzter Reiter, wie in den
   Haeusern), drei Abschnitte: was Cluster One ist, was Turing prueft, die Hausordnung. why.txt reist mit (sechste und siebte
   Kopie, der Bau prueft sie Byte fuer Byte gegen die fuenf Haeuser); liegt ein Haus, zeigt Turing dessen Text
   (cluster_one_why_text — den bekommen die KIs), sonst die eigene Kopie und sagt es. Als Text, nicht gesetzt: kein zweiter
   Markdown-Uebersetzer neben dem des Hauses. Verweis ins Haus nur, wenn ein Boden-Paket Feynmans Seiten angemeldet hat
   (dieselben drei Riegel wie Pacioli). B-Satz: Diagnostics 4.0.4 folgt [4.0.6: FALSCH — richtig 4.0.5, siehe den Block darueber];
   der Schnitt nimmt die Installer-Zeile heraus.
   ========================================================================== */

/* ==========================================================================
   4.0.4 (02.11.2026, Neo), RUNDE SIGMA: die letzten zwei Meldungen des Pruefers — $_SERVER['SERVER_SOFTWARE'] im
   Hangar-Schutz geht durch sanitize_text_field(wp_unslash()). Nur der Installer-Teil; Diagnostics bleibt 4.0.3.
   ========================================================================== */

/* ==========================================================================
   4.0.3 (02.11.2026, Neo), RUNDE RHO: nur die readme — die Kurzbeschreibung war laenger als 150 Zeichen
   (Plugin Check: readme_parser_warnings_trimmed_short_description). Kein Zeichen Code bewegt.
   ========================================================================== */

/* ==========================================================================
   4.0.2 (02.11.2026 abends, Neo), RUNDE 52 „PI" — DIE WARNUNGSRUNDE, UND EINE RICHTIGSTELLUNG.

   RICHTIGSTELLUNG zu 4.0.1 unten: dort steht „cluster-one-turing laedt vor
   cluster-one-turing-diagnostics (alphabetisch)". Gemessen am 02.11. auf der DEV:
   ANDERSHERUM. WordPress sortiert active_plugins als Zeichenketten „ordner/datei.php";
   nach dem gemeinsamen Anfang cluster-one-turing folgt bei Diagnostics ein - (0x2D),
   hier ein / (0x2F), und - sortiert davor. Sind beide aktiv, laeuft DIAGNOSTICS und
   dieses Werkzeug schaltet sich ab. Der Riegel sagt es laut; wer das ganze Werkzeug
   will, deaktiviert Diagnostics. Der falsche Satz bleibt unten stehen, als Geschichte.

   PI: Vermerke mit Grund an den gewollten Stellen (Direktzugriffe des Pruefers auf
   Optionsnamen und Tabellen, das Reiter-Lesen) und EINE Reparatur im Spiegel: der
   Name der Ereignis-Tabelle ging als Text in die Abfrage (FROM {$tbl}); jetzt setzt
   prepare() ihn als %i ein, wie kern.php seit 1.19.8. Gleicher Name, gleiche Zeilen.
   ========================================================================== */

/* ==========================================================================
   4.0.1 (02.11.2026 abends, Neo), RUNDE 51 „OMIKRON" — DAS GANZE WERKZEUG GEHT AUF GITHUB.

   Nemesus' Einwand nach dem Xi-Lauf: der Installer auf GitHub soll alle vier
   Reiter haben, nicht nur den ersten — wer ihn von dort holt, soll nicht zwei
   Plugins brauchen fuer das, was bisher eines war. Ins Verzeichnis geht weiter
   nur Diagnostics. Das ganze Werkzeug ist DIESE DATEI; sie wird also wieder
   ausgeliefert, unter dem Namen vom 02.11. frueh: „Cluster One — Turing",
   Ordner cluster-one-turing, Slug = Ordnername.

   ZURUECKGEZOGEN: der Installer-Schnitt aus 4.0.0 (cluster-one-turing-installer,
   Praefix turing_installer_, Organ turing-installer). Er lag nur auf der DEV
   (Plugin Check: 0 Fehler, 4 Warnungen) und auf keinem anderen Knoten; das Zip
   bleibt in DEV-2026-11-02-Xi als Beleg. turing_geschwister_organ() gibt hier
   '' zurueck — dieses Werkzeug hat kein Geschwister, es ist beides.

   NEBEN DIAGNOSTICS LAEUFT ES NICHT, und das ist gewollt: beide tragen dieselben
   Namen, und der Riegel oben laesst die zweite Kopie laut abschalten.
   cluster-one-turing laedt vor cluster-one-turing-diagnostics (alphabetisch),
   also gewinnt das ganze Werkzeug; Diagnostics liegt daneben still und meldet
   sich bei Kasper als nicht deklarierende Kopie. Pacioli (turing_durchgang,
   turing_zeuge_url) und Post Dollys Auge (Organ 'turing') finden beide.
   Kein Zeichen Code sonst bewegt.
   ========================================================================== */

/* ==========================================================================
   4.0.0 (02.11.2026 nachmittags, Neo), RUNDE 50 „XI" — DIE TEILUNG.

   Nemesus' Entscheidung vom 02.11.: ins Verzeichnis von wordpress.org geht nur
   der Pruefer; der Installer bleibt auf GitHub. Namen (Nemesus, 02.11. ~15:00):
       Cluster One — Turing Diagnostics   Kasper, Spiegel, Pacioli-Zeuge
                                           Ordner cluster-one-turing-diagnostics
       Cluster One — Turing Installer     der Installer-Reiter, Hangar, Narbe
                                           Ordner cluster-one-turing-installer
   DIESE DATEI IST DIE EINE QUELLE und wird nicht ausgeliefert: der Bau (xi-bauen.py,
   Werkbank) schneidet sie auf Satzebene in die zwei Plugins — jede Funktion ist
   einer Seite zugeordnet oder beiden, nichts wird doppelt gepflegt. Der Installer
   bekommt dabei den Praefix turing_installer_ (Token-Ebene, Kommentare unberuehrt):
   zwei Plugins mit denselben Namen wuerden sich am Riegel oben gegenseitig
   abschalten, und turing_version() laese die Fassung des anderen.

   WAS SICH SONST BEWEGT: „Was auf diesem Knoten liegt wird eine eigene Funktion
   (turing_lage_tabellen) und steht auch unter Kasper — auf Main, Stephan und
   Andreas liegt nur noch Diagnostics, und dort liest Nemesus die Fassungen ab.
   Die eigene Zeile nennt den Ordner gemessen (basename) und darunter das
   Geschwister-Werkzeug, wenn es sich bei Kasper gemeldet hat. Der Stempel (Werft)
   steht auf beiden Reitern (Nemesus: „kann ruhig auf beiden stehen). Die Seite
   wird gepuffert (ob_start): beim Durchgang des Zeugen blitzte in der
   Ueberschrift „Details auf, weil Chrome die halb angekommene Hilfeblase mit
   ihrem Standardwort zeichnet — derselbe Fund wie faust.php in Runde 27a.
   turing_zeuge_url(): Turing nennt die Adresse seines Zeugen selbst, Pacioli
   0.9.1 fragt danach, statt den Seitennamen abzuschreiben. turing_seitenname()
   und turing_geschwister_organ(): die zwei Stellen, an denen sich die Schnitte
   unterscheiden — sonst nur der Dateikopf.
   ========================================================================== */

/* ==========================================================================
   3.15.0 (02.11.2026, Neo), RUNDE 49 „NY" — DIE PRAEFIX-RUNDE.

   Nemesus' Entscheidung vom 02.11.: alle Pakete gehen ins Verzeichnis von
   wordpress.org, und dessen Regel verlangt einen eindeutigen Praefix von
   mindestens vier Buchstaben fuer alles Globale. `tu_` hat zwei. Seit 3.15.0
   heisst alles `turing_` / `TURING_`: Funktionen, Konstanten, Variablen,
   Nonces, Formularfelder — umbenannt auf Token-Ebene, Kommentare unberuehrt,
   also behaelt die Geschichte in dieser Datei ihre alten Namen. Dazu die neuen
   Namen von Pacioli (pacioli_ statt cv_, Pacioli_Vault statt CV_Vault).

   UND DER NAME: „Cluster One — Turing", wie die fuenf Produkte, obwohl Turing
   keinen Boden traegt — Nemesus' Wort. Der Ordner heisst weiter turing/; er
   wandert erst mit der Teilung in Pruefer (Verzeichnis) und Werft (GitHub).
   Kein Verhalten bewegt.
   ========================================================================== */

/* ==========================================================================
   3.14.10 (02.11.2026, Neo), RUNDE 48 „MY" — PLUGIN CHECK AUF NULL.

   Nemesus' Strategie vom 01.11.2026: die Fehler des WordPress-Plugin-Checks in allen
   sechs Paketen auf null, mit Vermerk und Grund an jeder Zeile, die der Pruefer nicht
   lesen kann. Turing war das letzte Paket; 28 Meldungen, kein echter Fund:
   24x OutputNotEscaped (tu_blase, Zellen und Listen, die ihre Glieder einzeln mit
   esc_html bauen und dann fuegen — die Form aus T19 FORDERUNG 0), dazu rename x2,
   rmdir und unlink in tu_hand_wordpress(): die Hand des Installers, die Ordner
   innerhalb von wp-content/plugins beiseite stellt und die Marke loescht.

   Und EINE Reparatur an einem Vermerk: Zeile ~1275 trug seit dem Spiegel-Umzug
   `// @phpcs:ignore — Diagnose`. Ohne ` -- ` liest PHPCS „— Diagnose" als Sniff-Namen
   und ignoriert nichts; gemessen an Nemesus' Exporten vom 01.11. (die vier Warnungen
   standen da). Jetzt die vier Codes und der Grund. Kein Zeichen Code bewegt.
   ========================================================================== */

/* ==========================================================================
   3.14.8 (31.10.2026, Neo), RUNDE 37 — SEHEN DUERFEN ALLE, TUN NUR MIT RECHT.

   Nemesus' Hausregel vom 30.10.2026: jedes Mitglied des Knotens sieht jedes
   Plugin, Turing eingeschlossen; Tun braucht Recht. Bis 3.14.7 verlangten
   Menuepunkt und Seite 'manage_options'.

   ZWEI FRAGEN, ZWEI WOERTER:
     tu_darf_tun()     Aufspielen, Spiegel zeigen, Durchgang starten:
                       'manage_options', unveraendert. Jeder Knopf prueft es
                       selbst, am POST und beim Zeichnen; ein Knopf, den man
                       nicht druecken darf, wird nicht gezeichnet, an seiner
                       Stelle steht der Satz.
     'turing_sehen'    die Seite und den Menuepunkt: wer 'manage_options' hat
                       ODER Mitglied des Knotens ist (cluster_one_can_access,
                       vom Boden). Eine eigene Capability ueber map_meta_cap,
                       weil Turing keinen Boden traegt und 'cluster_one_access'
                       auf einem Knoten ohne Boden niemandem gehoert – und weil
                       ein zweiter Administrator, der nicht auf der Liste des
                       Knotens steht, Turing nicht verlieren soll.

   WAS EIN MITGLIED SIEHT: alle vier Reiter. Installer mit Hangar und Kacheln,
   der Knopf fehlt und der Grund steht da (tu_inst_darf_schreiben, erste
   Pruefung). Kasper ganz, er war immer nur Anzeige. Spiegel und Zeuge mit
   ihrer Erklaerung, ohne Knopf; der Dump und der Bericht entstehen nur auf
   Knopfdruck und reisen deshalb nicht zu dem, der nicht druecken darf.
   Der Setup-Schirm (tu_seite_setup, Knoten ohne Boden) bleibt 'manage_options':
   ohne Boden gibt es keine Mitglieder.
   ========================================================================== */
if (! function_exists('turing_darf_tun')) {
function turing_darf_tun(): bool {
    return function_exists('current_user_can') && current_user_can('manage_options');
}
}

add_filter('map_meta_cap', function ($caps, $cap, $user_id) {
    if ($cap !== 'turing_sehen') { return $caps; }
    $user_id = (int) $user_id;
    if ($user_id > 0 && user_can($user_id, 'manage_options')) { return array('exist'); }
    if ($user_id > 0 && function_exists('cluster_one_can_access') && cluster_one_can_access($user_id)) { return array('exist'); }
    return array('do_not_allow');
}, 10, 3);

/* Die Meldung bei Kasper — hierher gewandert in 3.9.9, Grund oben. */
if (function_exists('cluster_one_hier')) {
    cluster_one_hier('turing', turing_version());
}

/** Welches Plugin liefert den KI-Client? Der Befund gilt fuer dessen Fassung. */
if (! function_exists('turing_unterbau')) {
function turing_unterbau(): array {
    if (! function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $out = array();
    foreach (get_plugins() as $datei => $d) {
        if (! is_plugin_active($datei)) { continue; }
        $h = strtolower((string) ($d['Name'] ?? '') . ' ' . $datei);
        /* 18.10.2026 (Neo): „ai" als eigenes Wort, nicht als Buchstabenfolge —
           strpos traf „mailchimp", „maintenance", „gain". Jetzt zaehlt ai nur
           am Wortrand (ai-client, wp-ai, openai, ai_…), connector wie bisher. */
        if (preg_match('/(^|[^a-z])ai([^a-z]|$)|openai|connector/', $h)) {
            $out[] = (string) ($d['Name'] ?? $datei) . ' ' . (string) ($d['Version'] ?? '?');
        }
    }
    return $out;
}
}

/* ==========================================================================
   Die Anbieter. GEMESSEN: was eingerichtet ist, nicht was moeglich waere.
   ========================================================================== */
if (! function_exists('turing_provider_liste')) {
function turing_provider_liste(): array {
    $ids = array();

    if (function_exists('cluster_one_get_ai_identities')) {
        foreach (array_keys((array) cluster_one_get_ai_identities()) as $k) {
            $ids[] = (string) $k;
        }
    }

    /* Und was in der Datenbank einen NICHT LEEREN Schluessel hat. Der Wert
       wird nur auf "leer oder nicht" geprueft und nirgends festgehalten. */
    global $wpdb;
    $rows = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Messung des Pruefers: liest Optionsnamen und Tabellen direkt, schreibt nichts; ein Cache waere eine zweite Wahrheit ueber den Knoten
        "SELECT option_name, option_value FROM {$wpdb->options}
          WHERE option_name LIKE 'connectors\\_ai\\_%\\_api\\_key'"
    );
    foreach ((array) $rows as $r) {
        if (trim((string) $r->option_value) === '') { continue; }
        if (preg_match('/^connectors_ai_(.+)_api_key$/', (string) $r->option_name, $m)) {
            $ids[] = $m[1];
        }
    }

    $ids = array_values(array_unique(array_filter($ids)));
    sort($ids);

    /* v3.1.0 — DER NOTNAGEL IST WEG.
       Hier stand: return $ids ? $ids : array('openai');

       Auf Christians Dev ist kein einziger Anbieter eingerichtet. Die Liste war
       also leer, und Turing setzte 'openai' ein. Der Bericht meldete daraufhin
       „Gepruefte Anbieter: openai" und „0 von 1 Anbietern messbar" — beides
       ueber einen Anbieter, den es auf diesem Knoten gar nicht gibt.

           Eine leere Liste, die sich einen Namen ausdenkt, macht aus
           „nichts da" ein „eins kaputt".

       Christian hat es gesehen und gefragt, ob da nicht etwas nicht stimme.
       Genau die Sorte Fund, die dieses Werkzeug bei ANDEREN suchen soll. */
    return $ids;
}
}

/* ==========================================================================
   TEIL ZWEI: WANN wird der Schluessel gelesen?

   Der erste Durchgang hat gezeigt, DASS Umschalten waehrend eines Aufrufs
   nicht wirkt. Er konnte nicht zeigen, WANN die Anbieter-Plugins ihren
   Schluessel holen — unser Lauscher hing damals erst beim Seitenaufbau, also
   viel zu spaet, um es zu sehen.

   Diese Filter haengen deshalb HIER, auf Datei-Ebene: Sie werden angehaengt,
   waehrend WordPress die Plugin-Dateien laedt, vor 'plugins_loaded' und lange
   vor 'init'. Frueher kommt ein normales Plugin nicht.

   WordPress laedt Plugins nach Pfad sortiert. 'ai-provider-for-...' steht vor
   'pacioli-zeuge', die Anbieter-Dateien laufen also VOR uns. Wenn sie ihren
   Schluessel schon dort holen, sehen wir nichts — und genau das waere die
   Antwort: Dann kaeme ein normales Plugin nie frueh genug, und es braeuchte
   ein mu-Plugin, also Dateizugriff, den dieses Publikum nicht hat.

   Sehen wir den Zugriff dagegen, koennen wir uns davorsetzen. Dann braucht
   Pacioli keinen Trick mehr: Es setzt am Anfang des Seitenaufrufs den
   Schluessel des Menschen, der geklickt hat, und alles Weitere ist von selbst
   richtig.

   Der Lauscher veraendert nichts. Er notiert Name, Reihenfolge und Phase.
   ========================================================================== */
if (! function_exists('turing_phase')) {
function turing_phase(): string {
    foreach (array('admin_init', 'wp_loaded', 'init', 'plugins_loaded', 'muplugins_loaded') as $h) {
        if (did_action($h)) { return $h . turing_prioritaet($h); }
    }
    return '(Plugin-Dateien werden noch geladen)';
}
}

/* v2.3.0 — DIE PRIORITAET WIRD GEMESSEN, NICHT ERSCHLOSSEN.

   Die Connector-Vault-Instanz hat in den offenen Quellen gefunden, dass Core
   den Schluessel bei `init` PRIORITAET 20 in die Registry des AI-Clients
   umzieht (_wp_connectors_pass_default_keys_to_ai_client). Unser Bericht
   kannte bisher nur die Phase.

   WP_Hook::current_priority() sagt, bei welcher Prioritaet ein Hook gerade
   steht — waehrend er laeuft. Damit wird aus einer belegten Fundstelle eine
   nachgemessene Zahl, und wer sich davorsetzen will, weiss auf welche
   Prioritaet. */
if (! function_exists('turing_prioritaet')) {
function turing_prioritaet(string $hook): string {
    if (! isset($GLOBALS['wp_filter'][$hook]) || ! ($GLOBALS['wp_filter'][$hook] instanceof WP_Hook)) {
        return '';
    }
    $p = $GLOBALS['wp_filter'][$hook]->current_priority();
    return (false === $p) ? '' : ' (Prioritaet ' . (int) $p . ')';
}
}

if (! function_exists('turing_zugriffe')) {
function &turing_zugriffe(): array {
    static $z = array();
    return $z;
}
}

/* v2.1.2 — LESEN OHNE REFERENZ.

   In 2.1.1 stand an zwei Stellen `$z =& tu_zugriffe();` — nur zum LESEN, aber
   als Referenz. Damit war $z ein Alias des statischen Arrays. Weiter unten lief
   `foreach ($d['zeilen'] as $cid => $z)` und schrieb bei jedem Durchlauf in
   genau dieses Array hinein. Danach stand dort die letzte Anbieter-Zeile statt
   der Zugriffsliste, und der Bericht starb an einem TypeError — mitten in der
   Ausgabe, unmittelbar nach der letzten Tabelle. Sichtbar war das nur NACH
   einem Durchgang; ohne ihn gibt es die Schleife nicht, und genau diesen Fall
   hatte ich getestet.

   Eine Referenz braucht nur, wer schreibt. Alle Leser bekommen ab jetzt eine
   Kopie, und der Fehler kann nicht wiederkommen. */
if (! function_exists('turing_zugriffe_lesen')) {
function turing_zugriffe_lesen(): array {
    $z =& turing_zugriffe();
    return $z;
}
}

if (is_admin()) {
    global $wpdb;
    $turing_keys = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Messung des Pruefers: liest Optionsnamen und Tabellen direkt, schreibt nichts; ein Cache waere eine zweite Wahrheit ueber den Knoten
        "SELECT option_name FROM {$wpdb->options}
          WHERE option_name LIKE 'connectors\_ai\_%\_api\_key'"
    );
    foreach ((array) $turing_keys as $turing_k) {
        $turing_k = (string) $turing_k;
        add_filter('pre_option_' . $turing_k, function ($pre) use ($turing_k) {
            $z =& turing_zugriffe();
            $z[] = array('option' => $turing_k, 'phase' => turing_phase(), 'nr' => count($z) + 1);
            return $pre;
        }, 1);
    }
}

/* ==========================================================================
   Alle Optionsnamen — der Lauscher raet nicht, wo er messen soll.
   ========================================================================== */
if (! function_exists('turing_kandidaten')) {
function turing_kandidaten(): array {
    global $wpdb;
    $rows = $wpdb->get_col("SELECT option_name FROM {$wpdb->options} ORDER BY option_name LIMIT 20000"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Messung des Pruefers: liest Optionsnamen und Tabellen direkt, schreibt nichts; ein Cache waere eine zweite Wahrheit ueber den Knoten
    return array_map('strval', (array) $rows);
}
}

/* ==========================================================================
   Den ausgehenden Request lesen. GET durchlassen, POST abbrechen.
   ========================================================================== */
if (! function_exists('turing_http_lauscher')) {
function turing_http_lauscher($pre, $args, $url) {
    $log =& turing_log();

    $method = isset($args['method']) ? strtoupper((string) $args['method']) : 'GET';

    $kopf = array();
    if (isset($args['headers']) && is_array($args['headers'])) {
        foreach ($args['headers'] as $k => $v) {
            $kopf[(string) $k] = is_scalar($v) ? (string) $v : '';
        }
    }
    $body = isset($args['body']) && is_string($args['body']) ? $args['body'] : '';
    $heu  = (string) $url . "\n" . wp_json_encode($kopf) . "\n" . $body;

    $befund = (strpos($heu, TURING_MARKE_B) !== false) ? 'B' : 'X';

    // Das mitgereiste Geheimnis herausziehen — nur zum Vergleichen.
    $geheim = '';
    foreach ($kopf as $k => $v) {
        $lk = strtolower((string) $k);
        if (in_array($lk, array('host', 'content-type', 'accept', 'user-agent', 'content-length', 'anthropic-version'), true)) {
            continue;
        }
        $kand = trim(preg_replace('/^Bearer\s+/i', '', (string) $v));
        if (strlen($kand) >= 16) { $geheim = $kand; break; }
    }
    if ($geheim === '' && preg_match('/[?&](key|api_key)=([^&]{16,})/', (string) $url, $m)) {
        $geheim = rawurldecode($m[2]);
    }

    /* v2.2.0 — WO genau reist der Schluessel?

       Anlass: der Gegenvorschlag der Connector-Vault-Instanz. Statt eine
       Zuordnung Host->Header zu pflegen, soll die Optionsschicht eine WERTLOSE
       MARKE liefern; der Vault sucht sie dann im ausgehenden Request und
       ersetzt sie durch den echten Schluessel der handelnden Identitaet.
       Elegant — man sucht einen Wert, den man selbst gesetzt hat, statt einen
       Namen, den ein Fremder vergibt.

       Es haengt aber an einer Frage, die bisher niemand gestellt hat:
       'http_request_args' bekommt die Argumente und darf Header aendern. Die
       URL bekommt der Filter zwar mit, gibt sie aber NICHT zurueck. Reist der
       Schluessel eines Anbieters im Query-String, kann dieser Filter ihn dort
       nicht ersetzen — dann braucht der Entwurf 'pre_http_request' oder fuer
       diesen Anbieter eine Absage.

       Bei der Modell-Erkundung von Google steht der Schluessel im Header
       (X-Goog-Api-Key). Ob das beim Generierungs-POST auch so ist, hat bisher
       niemand gemessen. Ab jetzt steht es im Bericht. */
    $ort = '—';
    if ($geheim !== '') {
        if (strpos((string) $url, $geheim) !== false) {
            $ort = 'URL';
        } else {
            foreach ($kopf as $k => $v) {
                if (strpos((string) $v, $geheim) !== false) { $ort = 'Header: ' . $k; break; }
            }
            if ($ort === '—' && $body !== '' && strpos($body, $geheim) !== false) { $ort = 'Rumpf'; }
        }
    }

    $log['requests'][] = array(
        'ort'       => $ort,
        'url'       => str_replace(TURING_MARKE_B, '<marke>', (string) $url),
        'method'    => $method,
        'headers'   => array_keys($kopf),
        'befund'    => $befund,
        'abdruck'   => $geheim === '' ? '' : substr(hash('sha256', 'pz|' . $geheim), 0, 8),
        'roh'       => $geheim,   // stirbt in tu_durchgang(), sobald die Heimat steht
        'blockiert' => ($method === 'POST'),
    );

    if ($method === 'POST') {
        return new WP_Error('turing_abgebrochen', 'Pacioli-Zeuge: POST bewusst abgebrochen, es geht kein Prompt hinaus.');
    }
    return $pre;
}
}

/* ==========================================================================
   Wo wohnt der Schluessel? Ausgegeben wird nur der NAME.
   ========================================================================== */
if (! function_exists('turing_finde_heimat')) {
function turing_finde_heimat(string $needle): array {
    if (strlen($needle) < 16) { return array(); }
    global $wpdb;
    $like = '%' . $wpdb->esc_like($needle) . '%';
    $rows = $wpdb->get_col($wpdb->prepare( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Messung des Pruefers: liest Optionsnamen und Tabellen direkt, schreibt nichts; ein Cache waere eine zweite Wahrheit ueber den Knoten
        "SELECT option_name FROM {$wpdb->options} WHERE option_value LIKE %s LIMIT 20",
        $like
    ));
    return array_map('strval', (array) $rows);
}
}

/* ==========================================================================
   Ein KI-Aufruf ueber GENAU DEN WEG, den Cluster One benutzt.
   ========================================================================== */
if (! function_exists('turing_aufruf')) {
function turing_aufruf(string $cid, string $text): string {
    if (function_exists('cluster_one_ai_generate_text')) {
        $r = cluster_one_ai_generate_text($cid, 'Antworte mit einem Wort.', $text);
        return 'cluster_one_ai_generate_text() · ' . (isset($r['err']) ? (string) $r['err'] : 'ok');
    }
    if (! function_exists('wp_ai_client_prompt')) {
        return 'KEIN CLIENT (wp_ai_client_prompt fehlt)';
    }
    try {
        $b = wp_ai_client_prompt($text);
        $b = $b->using_provider($cid)->using_system_instruction('Antworte mit einem Wort.')->using_max_tokens(64);
        $t = $b->generate_text();
        return 'wp_ai_client_prompt() · ' . (is_wp_error($t) ? $t->get_error_code() : 'ok');
    } catch (\Throwable $e) {
        return 'wp_ai_client_prompt() · Ausnahme: ' . get_class($e);
    }
}
}

/* ==========================================================================
   DER DURCHGANG — ein Knopf, beide Runden, alle Anbieter.
   ========================================================================== */
if (! function_exists('turing_durchgang')) {
function turing_durchgang(): array {
    $log =& turing_log();
    $log['options']  = array();
    $log['requests'] = array();

    $anbieter   = turing_provider_liste();

    /* v3.1.0: Ohne Anbieter gibt es nichts zu messen — und das ist ein anderer
       Zustand als „gemessen, aber nicht beantwortbar". Ohne diesen Riegel liefe
       der Durchgang leer durch und meldete UNBEANTWORTBAR, also ein Urteil ueber
       eine Messung, die nie stattgefunden hat. */
    if (! $anbieter) {
        return array(
            'anbieter'   => array(),
            'kandidaten' => 0,
            'gelesen'    => array(),
            'heimat'     => array(),
            'zeilen'     => array(),
            'gesamt'     => 'NICHTS EINGERICHTET',
            'messbar'    => 0,
            'stumm'      => array(),
        );
    }

    $kandidaten = turing_kandidaten();

    // Lauscher an ALLE Optionen. Er veraendert nichts, er schreibt mit.
    foreach ($kandidaten as $name) {
        add_filter('pre_option_' . $name, function ($pre) use ($name) {
            $l =& turing_log();
            if (! in_array($name, $l['options'], true)) { $l['options'][] = $name; }
            return $pre;
        }, 1);
    }
    add_filter('pre_http_request', 'turing_http_lauscher', 1, 3);

    /* --- Runde A: echte Schluessel ------------------------------------- */
    $runde_a = array();
    $erste_lesung = null;
    foreach ($anbieter as $cid) {
        $vor = count($log['requests']);
        $weg = turing_aufruf($cid, 'Zeuge Runde A.');
        if ($erste_lesung === null) { $erste_lesung = $log['options']; }
        $runde_a[$cid] = array('weg' => $weg, 'reqs' => array_slice($log['requests'], $vor));
    }

    // Heimat aus dem, was tatsaechlich ankam.
    $heimat = array();
    foreach ($log['requests'] as $r) {
        if (! empty($r['roh'])) {
            foreach (turing_finde_heimat((string) $r['roh']) as $o) {
                if (! in_array($o, $heimat, true)) { $heimat[] = $o; }
            }
        }
    }
    sort($heimat);

    /* --- Runde B: ALLE gefundenen Schluessel-Optionen auf Marke B ------- */
    $schalter = function () { return TURING_MARKE_B; };
    foreach ($heimat as $o) { add_filter('pre_option_' . $o, $schalter, 1); }

    $runde_b = array();
    foreach ($anbieter as $cid) {
        $vor = count($log['requests']);
        $weg = turing_aufruf($cid, 'Zeuge Runde B.');
        $runde_b[$cid] = array('weg' => $weg, 'reqs' => array_slice($log['requests'], $vor));
    }

    foreach ($heimat as $o) { remove_filter('pre_option_' . $o, $schalter, 1); }
    remove_filter('pre_http_request', 'turing_http_lauscher', 1);

    /* --- Urteil je Anbieter -------------------------------------------- */
    $letzter_post = function (array $reqs) {
        $t = null;
        foreach ($reqs as $r) { if (! empty($r['blockiert'])) { $t = $r; } }
        return $t;
    };
    $ohne = function (array $reqs) {
        $out = array();
        foreach ($reqs as $r) { unset($r['roh']); $out[] = $r; }
        return $out;
    };

    $zeilen = array();
    foreach ($anbieter as $cid) {
        $pa = $letzter_post($runde_a[$cid]['reqs']);
        $pb = $letzter_post($runde_b[$cid]['reqs']);

        if ($pa === null || $pb === null) {
            $u = 'UNBEANTWORTBAR';
        } elseif ($pb['befund'] === 'B') {
            $u = 'GRUEN';
        } elseif ($pa['abdruck'] !== '' && $pa['abdruck'] === $pb['abdruck']) {
            $u = 'ROT';
        } else {
            $u = 'UNKLAR';
        }

        /* 3.14.7 (30.10.2026, Neo) — DIE ZWEITE FRAGE, UND SIE IST DIE, VON DER PACIOLI
           HEUTE ABHAENGT. Nemesus auf der Main: vier Anbieter ROT, und Pacioli funktioniert.
           Das ROT oben beantwortet die Frage von 2.0: wirkt Umschalten NACH dem ersten
           Lesen? Pacioli schaltet seit 0.8 nicht mehr um – es setzt VOR dem ersten Lesen
           (pre_option_), was dieser Zeuge selbst als den richtigen Angriffspunkt nennt.
           Die Frage, die Pacioli stellt, lautet: Stammt der Schluessel, der im POST den
           Server verlaesst, aus einer Options-Zeile – oder aus dem, was vorher gesetzt
           wurde? tu_finde_heimat() beantwortet sie je Anbieter aus dem letzten POST der
           Runde A: keine Zeile traegt den Wert -> der Tausch ist angekommen (GRUEN);
           eine Zeile traegt ihn -> der Request faehrt mit dem Options-Schluessel (ROT).
               Ein ROT, das eine Frage beantwortet, die wir nicht mehr stellen,
               sieht aus wie ein Hindernis.               (3.12.0, Posten 2 – und
               diesmal stand es als Gesamturteil ueber der ganzen Seite.) */
        $heimat_cid = ($pa !== null && ! empty($pa['roh'])) ? turing_finde_heimat((string) $pa['roh']) : array();
        sort($heimat_cid);
        if ($pa === null) {
            $up = 'UNBEANTWORTBAR';
        } elseif ($heimat_cid) {
            $up = 'ROT';
        } elseif ($pa['abdruck'] !== '') {
            $up = 'GRUEN';
        } else {
            $up = 'UNKLAR';
        }

        $zeilen[$cid] = array(
            'urteil'  => $u,
            'pacioli' => $up,
            'heimat'  => $heimat_cid,
            'weg_a'   => $runde_a[$cid]['weg'],
            'weg_b'   => $runde_b[$cid]['weg'],
            'abd_a'   => $pa ? $pa['abdruck'] : '',
            'abd_b'   => $pb ? $pb['abdruck'] : '',
            'bef_b'   => $pb ? $pb['befund'] : '',
            'reqs_a'  => $ohne($runde_a[$cid]['reqs']),
            'reqs_b'  => $ohne($runde_b[$cid]['reqs']),
        );
    }

    /* v2.0.1 — NICHT MESSBARES VERWAESSERT DAS URTEIL NICHT MEHR.

       In 2.0.0 zaehlte ein Anbieter ohne eingerichteten Schluessel als eigene
       Urteilsart. Vier einstimmige ROT plus ein UNBEANTWORTBAR ergaben
       deshalb das Gesamturteil UNKLAR — schwaecher, als die Messung hergab.

       Das ist der Spiegelfehler zum falschen Gruen und genauso unbrauchbar:
       Wer nichts messen konnte, stimmt nicht mit ab. Er wird gezaehlt und
       genannt, aber getrennt. */
    $messbar = array();
    $stumm   = array();
    foreach ($zeilen as $cid => $z) {
        if ($z['urteil'] === 'UNBEANTWORTBAR') { $stumm[] = $cid; }
        else { $messbar[] = $z['urteil']; }
    }
    $arten = array_values(array_unique($messbar));

    $sammeln = function (array $arten): string {
        if (! $arten) { return 'UNBEANTWORTBAR'; }
        if (count($arten) === 1) { return $arten[0]; }
        if (in_array('GRUEN', $arten, true) && in_array('ROT', $arten, true)) { return 'GEMISCHT'; }
        return 'UNKLAR';
    };
    $gesamt = $sammeln($arten);

    /* 3.14.7: dieselbe Zaehlung fuer die Pacioli-Frage – wer nicht messbar war, stimmt nicht ab. */
    $messbar_p = array();
    foreach ($zeilen as $z) {
        if ($z['pacioli'] !== 'UNBEANTWORTBAR') { $messbar_p[] = $z['pacioli']; }
    }
    $gesamt_pacioli = $sammeln(array_values(array_unique($messbar_p)));

    return array(
        'anbieter'       => $anbieter,
        'kandidaten'     => count($kandidaten),
        'gelesen'        => (array) $erste_lesung,
        'heimat'         => $heimat,
        'zeilen'         => $zeilen,
        'gesamt'         => $gesamt,
        'gesamt_pacioli' => $gesamt_pacioli,
        /* 3.14.7: liegt Pacioli auf dem Knoten, ist seine Frage das Gesamturteil; sonst bleibt
           die alte, denn ohne Pacioli ist „Umschalten nach dem Lesen" die Frage eines Entwurfs. */
        'pacioli_da'     => function_exists('pacioli_identity_for') || class_exists('Pacioli_Vault'),
        'messbar'        => count($messbar),
        'stumm'          => $stumm,
    );
}
}

/* ==========================================================================
   Ausgabe
   ========================================================================== */
if (! function_exists('turing_tabelle')) {
function turing_tabelle(array $reqs): void {
    if (! $reqs) { echo '<p><em>Kein Request.</em></p>'; return; }
    echo '<table class="widefat striped" style="max-width:960px;margin-bottom:1em;"><thead><tr>';
    echo '<th>Methode</th><th>Schluessel</th><th>reist in</th><th>Abdruck</th><th>URL</th>';
    echo '</tr></thead><tbody>';
    foreach ($reqs as $r) {
        echo '<tr><td><strong>' . esc_html($r['method']) . '</strong><br><span style="font-size:11px;color:#666;">'
           . esc_html(! empty($r['blockiert']) ? 'abgebrochen' : 'durchgelassen') . '</span></td>';
        echo '<td>' . esc_html($r['befund'] === 'B' ? 'Marke B' : 'echt (maskiert)') . '</td>';
        echo '<td><strong>' . esc_html(isset($r['ort']) ? $r['ort'] : '—') . '</strong></td>';
        echo '<td style="font-family:monospace;font-size:11px;">' . esc_html($r['abdruck'] !== '' ? $r['abdruck'] : '—') . '</td>';
        echo '<td style="word-break:break-all;font-family:monospace;font-size:11px;">' . esc_html($r['url']) . '</td></tr>';
    }
    echo '</tbody></table>';
}
}

if (! function_exists('turing_bericht_tab')) {
function turing_bericht_tab(array $reqs): array {
    if (! $reqs) { return array('    (kein Request)'); }
    $out = array();
    foreach ($reqs as $r) {
        $out[] = sprintf('    %-5s %-13s %-9s %-20s %s',
            $r['method'],
            (! empty($r['blockiert']) ? 'abgebrochen' : 'durchgelassen'),
            ($r['befund'] === 'B' ? 'MARKE-B' : 'echt') . ($r['abdruck'] !== '' ? '/' . $r['abdruck'] : ''),
            'in ' . (isset($r['ort']) ? $r['ort'] : '-'),
            $r['url']);
    }
    return $out;
}
}

if (! function_exists('turing_bericht')) {
function turing_bericht(?array $d): string {
    $u = turing_unterbau();
    $t = array();
    $t[] = '=== TURING ' . turing_version() . ' — REITER PACIOLI-ZEUGE ===';
    $t[] = 'Knoten:   ' . home_url('/');
    $t[] = 'WP/PHP:   ' . get_bloginfo('version') . ' / ' . PHP_VERSION;
    $t[] = 'Unterbau: ' . ($u ? implode(' | ', $u) : '(nichts gefunden)');
    $t[] = 'Zeit:     ' . gmdate('c');
    $t[] = '';
    if ($d === null) {
        $t[] = '(Kein Durchgang gefahren — unten steht nur die Beobachtung, wann gelesen wird.)';
        $t[] = '';
    } elseif ($d['gesamt'] === 'NICHTS EINGERICHTET') {
        /* v3.1.0: „0 von 0 Anbietern messbar" waere eine Quote ueber eine leere
           Menge — sie sieht aus wie ein Ergebnis. Hier steht stattdessen der
           Satz, der die Lage beschreibt. */
        $t[] = 'GESAMTURTEIL: NICHTS EINGERICHTET';
        $t[] = 'Auf diesem Knoten ist kein Anbieter eingerichtet. Es wurde nichts gemessen.';
        $t[] = 'Unbeantwortet bleibt damit: wirkt ein Umschalten NACH dem ersten Lesen noch?';
        $t[] = '';
    } elseif (! empty($d['pacioli_da'])) {
        /* 3.14.7 (30.10.2026, Neo): MIT PACIOLI IST SEINE FRAGE DAS URTEIL. Die alte Frage
           (Umschalten nach dem ersten Lesen) bleibt im Bericht – als Eigenschaft des Unterbaus,
           ohne Farbe im Gesamturteil. Grund bei tu_durchgang. */
        $t[] = 'GESAMTURTEIL: ' . $d['gesamt_pacioli'] . '   (' . (int) $d['messbar'] . ' von ' . count($d['anbieter']) . ' Anbietern messbar)';
        $t[] = 'DIE FRAGE DAZU (Pacioli): Stammt der Schluessel, der im POST den Server';
        $t[] = 'verlaesst, aus einer Options-Zeile – oder aus dem, was VOR dem ersten Lesen';
        $t[] = 'gesetzt wurde (Paciolis Tausch)? GRUEN: keine Zeile traegt den Wert.';
        $t[] = 'EIGENSCHAFT DES UNTERBAUS: Umschalten der Option NACH dem ersten Lesen: ' . $d['gesamt'];
        $t[] = '  (ROT heisst: der Client liest einmal und haelt. Pacioli nutzt diesen Weg nicht.)';
        if (! empty($d['stumm'])) {
            $t[] = 'Nicht messbar (kein Schluessel eingerichtet): ' . implode(', ', $d['stumm']);
        }
        $t[] = '';
    } else {
        $t[] = 'GESAMTURTEIL: ' . $d['gesamt'] . '   (' . (int) $d['messbar'] . ' von ' . count($d['anbieter']) . ' Anbietern messbar)';
        /* v3.12.0, Posten 2 — EIN URTEIL OHNE SEINE FRAGE IST EIN HINDERNIS.
           Zehn Zeilen auseinander standen auf dieser Seite „GESAMTURTEIL ROT"
           und „WEG 1 IST MOEGLICH". Beides war wahr, und es waren zwei Fragen.
               Ein ROT, das eine Frage beantwortet, die wir nicht stellen
               muessen, sieht aus wie ein Hindernis und ist eine Entlastung. */
        $t[] = 'DIE FRAGE DAZU: Wirkt ein Umschalten der Option, das NACH dem ersten';
        $t[] = 'Lesen geschieht, noch auf das, was hinausgeht? Gemessen wird dafuer der';
        $t[] = 'letzte POST der Runde A gegen den letzten POST der Runde B.';
        $t[] = 'NICHT diese Frage: ob ein Plugin frueh genug kommt, um VOR dem ersten';
        $t[] = 'Lesen zu setzen. Die steht unten unter „WANN WIRD DER SCHLUESSEL GELESEN".';
        if (! empty($d['stumm'])) {
            $t[] = 'Nicht messbar (kein Schluessel eingerichtet): ' . implode(', ', $d['stumm']);
        }
        $t[] = '';
    }
    if ($d !== null && $d['gesamt'] !== 'NICHTS EINGERICHTET') {
    $t[] = '--- BEOBACHTUNG ---------------------------------------------';
    $t[] = 'Gepruefte Anbieter:   ' . implode(', ', $d['anbieter']);
    $t[] = 'Belauschte Optionen:  ' . (int) $d['kandidaten'];
    $t[] = 'Im 1. Aufruf gelesen: ' . ($d['gelesen'] ? implode(', ', $d['gelesen']) : '(keine)');
    $t[] = 'Schluessel wohnen in: ' . ($d['heimat'] ? implode(', ', $d['heimat']) : '(keine Options-Zeile)');
    if ($d['heimat']) {
        $n = array_diff($d['heimat'], $d['gelesen']);
        $t[] = 'Davon NICHT gelesen:  ' . ($n ? implode(', ', $n) : '(alle wurden gelesen)');
    }
    $t[] = '';
    $t[] = '--- URTEIL JE ANBIETER --------------------------------------';
    foreach ($d['zeilen'] as $cid => $z) {
        $t[] = sprintf('  %-12s %-15s  A=%-9s B=%-9s', $cid, $z['urteil'],
            $z['abd_a'] !== '' ? $z['abd_a'] : '-',
            $z['bef_b'] === 'B' ? 'MARKE-B' : ($z['abd_b'] !== '' ? $z['abd_b'] : '-'));
        if (! empty($d['pacioli_da'])) {
            $t[] = sprintf('  %-12s   Pacioli: %-13s %s', '', $z['pacioli'],
                $z['pacioli'] === 'ROT' ? 'Schluessel steht in: ' . implode(', ', $z['heimat']) : ($z['pacioli'] === 'GRUEN' ? 'kein Schluessel aus einer Options-Zeile' : ''));
        }
    }
    $t[] = '';
    $t[] = '--- WO REIST DER SCHLUESSEL? (fuer den Marken-Entwurf) -------';
    $orte = array();
    foreach ($d['zeilen'] as $cid => $z) {
        foreach (array_merge($z['reqs_a'], $z['reqs_b']) as $r) {
            if (empty($r['blockiert'])) { continue; }   // nur die Generierungs-POSTs
            $orte[$cid] = isset($r['ort']) ? $r['ort'] : '-';
        }
    }
    foreach ($orte as $cid => $o) { $t[] = sprintf('  %-12s POST -> %s', $cid, $o); }
    $t[] = '  Steht dort URL, kann http_request_args den Wert NICHT ersetzen:';
    $t[] = '  der Filter gibt die URL nicht zurueck. Dann braucht es pre_http_request.';
    $t[] = '  Das gilt fuer WEG 2 — die Marke im ausgehenden Request ersetzen.';
    $t[] = '  WEG 1 (vor dem ersten Lesen setzen) fasst den Request nicht an.';
    $t[] = '';
    $t[] = '--- REQUESTS ------------------------------------------------';
    foreach ($d['zeilen'] as $cid => $z) {
        $t[] = '  ' . $cid . ' / Runde A (echte Schluessel) — ' . $z['weg_a'];
        $t = array_merge($t, turing_bericht_tab($z['reqs_a']));
        $t[] = '  ' . $cid . ' / Runde B (alle Optionen auf Marke B) — ' . $z['weg_b'];
        $t = array_merge($t, turing_bericht_tab($z['reqs_b']));
        $t[] = '';
    }
    }

    $zug = turing_zugriffe_lesen();
    $t[] = '--- WANN WIRD DER SCHLUESSEL GELESEN? ------------------------';
    if ($zug) {
        foreach ($zug as $e) { $t[] = sprintf('  %2d. %-40s Phase: %s', $e['nr'], $e['option'], $e['phase']); }
        $t[] = '  => Wer sich davorsetzen will, muss VOR dieser Prioritaet hoeren.';
        $t[] = '     Belegt in den Quellen: Core zieht den Schluessel bei init 20 in';
        $t[] = '     die Registry des AI-Clients (_wp_connectors_pass_default_keys_to_ai_client).';
    } else {
        $t[] = '  Kein Zugriff beobachtet, obwohl seit Datei-Ladezeit gelauscht wird.';
        $t[] = '  => entweder lesen sie in ihren eigenen Plugin-Dateien (vor uns),';
        $t[] = '     oder gar nicht aus Optionen. Weg 1 braeuchte dann ein mu-Plugin.';
    }
    $t[] = '';
    $t[] = 'BENANNTE GRENZEN:';
    $t[] = '  1. Nur was durch wp_remote_* laeuft, wird gesehen.';
    $t[] = '  2. Nur das Verhalten innerhalb EINES Requests.';
    $t[] = '  3. Gilt fuer die oben genannte Fassung des Unterbaus.';
    $t[] = '  4. "Gelesen" gilt fuer den ERSTEN Aufruf; danach ist der Client warm.';
    $t[] = '  GET laeuft durch, POST wird gelesen und abgebrochen.';
    return implode("\n", $t);
}
}

/* ==========================================================================
   REITER 2 — DER SPIEGEL

   Umzug aus cluster-one/includes/spiegel.php, 24.08.2026. Die drei Funktionen
   sind so uebernommen, wie sie dort standen; ihre Kommentare reisen mit, weil
   jede Zeile darin einen Fund festhaelt, der einmal Geld oder Daten gekostet
   hat. Was hier NICHT steht, steht mit Absicht nicht hier: die Umgebungs-Notiz
   bleibt in Cluster One.

   DER UNTERSCHIED ZUM ORIGINAL, und es ist der ganze:
   Turing kennt Cluster Ones Code nicht. Es gibt kein CLUSTER_ONE_VERSION und
   keine geladene Funktion, auf die man sich stuetzen koennte. Beides wird
   deshalb von aussen ermittelt — der laufende BODEN ueber
   tu_boden_am_knoten(), die Tabelle ueber eine Existenzpruefung. Faellt eines
   davon weg, sagt der Spiegel das, statt einen leeren Dump als Befund
   auszugeben.

   BERICHTIGT am 10.10.2026, Posten 1. Hier stand bis heute „die Fassung aus
   dem Plugin-Kopf". Das war wahr, solange es EINEN Ordner cluster-one/ mit
   EINER Fassung gab. Beides gibt es nicht mehr, und der Satz hat das drei
   Wochen ueberlebt, weil er beim Schreiben richtig war.

       Ein Satz, der zu Recht stehen blieb, kann durch die Reparatur
       daneben falsch werden. Das Sieb von gestern gilt nicht fuer die
       Datei von heute.

   VERFASSUNGSSCHRANKE, unveraendert uebernommen: STRUKTUR STATT INHALT.
   ========================================================================== */

/** Der Spiegel (v0.0.84, Idee SysOp 17.07.2026): Diagnose-Export der
   Plugin-Teil-DB — damit die KI beim Debuggen die WIRKLICHEN Daten sieht
   statt zu spekulieren („halbblind"). VERFASSUNGSSCHRANKE, nicht
   verhandelbar: STRUKTUR STATT INHALT — Konfigurationen, Listen, Flags,
   Zustaende vollstaendig; Nachrichten-/Beitragstexte nur als Laenge +
   Fingerabdruck; alles nach Token/Schluessel/Secret Riechende maskiert.
   Sonst waere der Knopf ein Hintertuerchen am Zeugen-System vorbei
   (Tagebuch-Klartext unbezeugt in der Zwischenablage) und ein
   Sicherheitsrisiko (Kanten-Tokens im Chat mit einer KI). */
if (! function_exists('turing_spiegel_scrub')) {
function turing_spiegel_scrub($v, string $key = '', string $salt = '') {
    $k = strtolower($key);
    if (is_string($v)) {
        // v0.0.85: 'discord'/'matrix' (exakt) = Kontakt-Kennungen von
        // Personen — Artikel 6, maskiert wie Tokens. Die oeffentlichen
        // Knoten-URLs (discord_url/matrix_url) bleiben sichtbar.
        //
        // v0.9.32: 'mail' dazu. In der Ausgabe des Main-Knotens stand
        // sysop_mail als KLARTEXT-ADRESSE — das Feld faellt durch keinen
        // der bisherigen Ausdruecke, weil es weder Token noch Kennung
        // heisst. Der Spiegel hat genau einen Zweck ("kopieren, an die KI
        // geben"), also reiste die Adresse bei jedem Gebrauch mit.
        // Zeitstempel bleiben ausgenommen: sysop_mail_ts sagt WANN, nicht
        // WER, und ist fuer die Diagnose das Interessantere von beidem.
        $c1_mail = (strpos($k, 'mail') !== false && substr($k, -3) !== '_ts');
        if ($k !== '' && ($c1_mail || preg_match('/token|secret|api_key|apikey|password|pass\b/', $k) || in_array($k, array('discord', 'matrix'), true))) {
            return $v === '' ? '' : '***';
        }
        // v0.0.89: GESPEICHERTE Fingerabdruecke (preamble_hash u. ae.) werden
        // NACHGESALZEN — der P0-7-Zeugen-Zweck bleibt (gleiche Fassung =
        // gleicher Wert im Dump), die Rate-Bestaetigung stirbt (Fund aus
        // der Gegenprobe zweier Dumps: 32e6e1e1 beidesmal identisch —
        // bei einer 5-Zeichen-Praeambel ein offenes Scheunentor).
        if (substr($k, -5) === '_hash') {
            return $v === '' ? '' : substr(hash('sha256', $salt . $v), 0, 8);
        }
        // v0.0.85: 'translation' ergaenzt — Inhalt ist Inhalt, auch
        // uebersetzt (Fund: erster Spiegel-Dump, Klartext-Leck im
        // Kanten-Chat; der Scrubber kannte das Feld nicht).
        if (in_array($k, array('body', 'text', 'preamble', 'message', 'bio', 'subject', 'translation'), true)) {
            // v0.0.86: Dump-Salz — Fingerabdruecke bleiben INNERHALB eines
            // Dumps vergleichbar (ihr Debugging-Zweck: identische Texte
            // erkennen), sind aber gegen Woerterbuch-Raten blind. Bei
            // len:4 kann sonst jeder „Test" hashen und vergleichen.
            return $v === '' ? '' : array('len' => strlen($v), 'sha8' => substr(hash('sha256', $salt . $v), 0, 8));
        }
        return $v;
    }
    if (is_array($v)) {
        $out = array();
        foreach ($v as $kk => $vv) {
            $out[$kk] = turing_spiegel_scrub($vv, (string) $kk, $salt);
        }
        return $out;
    }
    return $v;
}
}

/** v0.0.86: Namen Externer maskieren — die ext_…-ID reicht zum Debuggen;
   Name + Sprache + Zeitzone ist als Kombination identifizierend
   (Artikel 6: Daten Dritter). Die ID bleibt der Anker fuers Gespraech.

   v0.9.32: DIESER KOMMENTAR STAND SEIT 0.0.86 RICHTIG DA — UND DIE FUNKTION
   SETZTE EIN DRITTEL DAVON UM. Er nennt die Kombination "Name + Sprache +
   Zeitzone" und maskiert wurde der Name. In Christians Ausgabe stand deshalb
   name "***", discord "***", lang "Russisch", tz "Asia/Vladivostok" — bei
   genau EINEM Eintrag in der Liste. Ein geschwaerzter Name ueber einer
   Personenbeschreibung ist keine Anonymisierung.

   EINE REGEL IM KOMMENTAR IST KEIN WAECHTER IM WERKZEUG.

   NICHT GELOESCHT, SONDERN WIE TOKENS MASKIERT: leer bleibt leer, gefuellt
   wird '***'. Damit bleibt sichtbar, OB Sprache und Zeitzone gesetzt sind —
   die Frage, die man an einem Dump wirklich stellt — ohne zu verraten,
   WELCHE. */
if (! function_exists('turing_spiegel_mask_externals')) {
function turing_spiegel_mask_externals(array $ext): array {
    foreach ($ext as $id => $e) {
        if (!is_array($e)) {
            continue;
        }
        foreach (array('name', 'lang', 'tz') as $c1_feld) {
            if (isset($e[$c1_feld])) {
                $ext[$id][$c1_feld] = ((string) $e[$c1_feld] === '') ? '' : '***';
            }
        }
    }
    return $ext;
}
}

/* --------------------------------------------------------------------------
   v3.11.0, POSTEN 1 — tu_c1_fassung() IST ZURUECKGEZOGEN.

   WAS HIER STAND:

       function tu_c1_fassung(): string {
           if (defined('CLUSTER_ONE_VERSION')) { return CLUSTER_ONE_VERSION; }
           foreach (get_plugins() as $datei => $d) {
               if (strpos($datei, 'cluster-one/') === 0) { return …; }
           }
           return '';
       }

   DREI FUNDE, EINE FUNKTION. Beide Wege suchten Mammut: die Konstante
   CLUSTER_ONE_VERSION, die es nicht mehr gibt, und den Ordner cluster-one/,
   den es nicht mehr gibt. Kein Weg kannte die fuenf cluster-one-*. Auf
   diesem Knoten konnte die Funktion deshalb nur '' liefern — und daraus
   machte der Spiegel „Cluster One ist auf diesem Knoten nicht installiert",
   drei Reiter neben fuenf aktiven Paketen und einem laufenden Boden.

   SIE WIRD NICHT AUSGEBESSERT, SONDERN ZURUECKGEZOGEN, und das ist der
   eigentliche Befund:

       Es gibt keine eine Fassung von Cluster One mehr. Die Frage dieser
       Funktion hat keine Antwort — nicht einmal eine falsche.

   Eine ausgebesserte tu_c1_fassung(), die den Boden zurueckgibt, waere eine
   Paketfassung mit dem Namen einer Bodenfassung gewesen:

       Eine Fassung, die nach dem Paket heisst und den Boden nennt, ist
       eine Verwechslung mit einem Namen davor.

   Gefragt wird ab jetzt, was der Installer-Reiter seit v3.9.5 fragt:
   tu_boden_am_knoten() (Deklaration unten, eine Quelle, zwei Leser). Das ist
   KEINE neue Quelle, sondern das Ende einer zweiten:

       Zwei Leser auf eine Quelle sind erlaubt. Zwei Quellen auf eine Frage
       sind ein Fehler, der sich als Vorsicht verkleidet.

   BENANNTE GRENZE, und sie steht an jeder Stelle daneben, die den Wert
   zeigt: die Bodenfassung ist NICHT die Paketfassung. Der Spiegel darf nicht
   „Cluster One 1.15.0" sagen.
   -------------------------------------------------------------------------- */

/** Gibt es die Ereignis-Tabelle? Ohne Cluster Ones Code muss das gefragt
   werden, statt es anzunehmen. */
if (! function_exists('turing_c1_events_tabelle')) {
function turing_c1_events_tabelle(): string {
    global $wpdb;
    $tbl = $wpdb->prefix . 'cluster_one_events';
    $da  = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $tbl)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Messung des Pruefers: liest Optionsnamen und Tabellen direkt, schreibt nichts; ein Cache waere eine zweite Wahrheit ueber den Knoten
    return ((string) $da === $tbl) ? $tbl : '';
}
}

if (! function_exists('turing_spiegel_dump')) {
function turing_spiegel_dump(): array {
    global $wpdb;
    $salt = bin2hex(random_bytes(12)); // v0.0.86: pro Dump, wird NIRGENDS gespeichert
    $dump = array(
        'spiegel'  => array(
            'werkzeug'    => 'Turing ' . turing_version(),
            /* v3.11.0: hiess 'cluster_one' und gab eine PAKETfassung aus. Der
               Name wandert mit dem Wert, und die Grenze wandert mit dem Dump
               — ein Dump erreicht Leser, die kein Papier erreicht. */
            'cluster_one_boden' => turing_boden_am_knoten(),
            'cluster_one_hinweis' => 'Fassung des BODENS, nicht eines Pakets. Es gibt keine eine Fassung von Cluster One; jedes Paket traegt eine eigene.',
            'wp'          => get_bloginfo('version'),
            'php'         => PHP_VERSION,
            'ts'          => gmdate('c'),
            'salted'      => true,
        ),
        'options'  => array(),
    );
    $names = $wpdb->get_col("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'cluster\_one\_%' ORDER BY option_name"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Messung des Pruefers: liest Optionsnamen und Tabellen direkt, schreibt nichts; ein Cache waere eine zweite Wahrheit ueber den Knoten
    foreach ((array) $names as $n) {
        $val = get_option((string) $n);
        if ((string) $n === 'cluster_one_humans_external' && is_array($val)) {
            $val = turing_spiegel_mask_externals($val); // v0.0.86
        }
        $dump['options'][(string) $n] = turing_spiegel_scrub($val, (string) $n, $salt);
    }

    /* Der Unterschied zum Original: die Tabelle wird nicht vorausgesetzt.
       Ein Dump ohne Ereignisse ist ein Befund; ein Dump, der wegen einer
       fehlenden Tabelle die Haelfte verschweigt, ist einer aus dem falschen
       Grund. */
    $tbl = turing_c1_events_tabelle();
    if ($tbl === '') {
        $dump['events'] = array('tabelle' => 'nicht vorhanden');
    } else {
        /* 4.0.2 (Pi): bis 4.0.1 stand der Tabellenname im Text (FROM {$tbl}) mit einem Vermerk, der auch InterpolatedNotPrepared
           und UnescapedDBParameter ignorierte; jetzt setzt prepare() ihn als %i ein — gleicher Name, gleiche Zeilen. */
        $cnt = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM %i', $tbl)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Messung des Pruefers: liest Optionsnamen und Tabellen direkt, schreibt nichts; ein Cache waere eine zweite Wahrheit ueber den Knoten
        $dump['events'] = array(
            'count' => (int) $cnt,
            'last'  => array_map(function ($r) { return (array) $r; }, (array) $wpdb->get_results($wpdb->prepare('SELECT id, type, container_type, container_id, actor_id, created_at FROM %i ORDER BY id DESC LIMIT 25', $tbl))), // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Messung des Pruefers: liest Optionsnamen und Tabellen direkt, schreibt nichts; ein Cache waere eine zweite Wahrheit ueber den Knoten
        );
    }
    return $dump;
}
}

/* ==========================================================================
   SELBSTPRUEFUNG DES SCRUBBERS

   Diese Behauptungen standen bis v0.9.69 in der Schnitt-Suite von Cluster One
   (verhalten.php, "Der Zettel des SysOp reist nicht mit"). Sie sind mit dem
   Code umgezogen, nicht neu geschrieben.

   WARUM SIE MITREISEN MUSSTEN: Ein Zeuge, der bleibt, waehrend das Bezeugte
   auszieht, wird gruen, weil er nichts mehr findet. Das ist dieselbe
   Fehlerklasse, die in zwei Tagen sechsmal aufgetreten ist.

   JEDE ZEILE HIER IST EIN FUND, KEIN EINFALL:
   - sysop_mail stand als KLARTEXT-ADRESSE im Dump. Weder Token noch Kennung,
     also fiel es durch jeden vorhandenen Ausdruck (0.9.32).
   - Bei Externen war der Name maskiert und Sprache + Zeitzone standen daneben
     — bei genau EINEM Eintrag in der Liste. Ein geschwaerzter Name ueber
     einer Personenbeschreibung ist keine Anonymisierung (0.9.32).

   BENANNTE GRENZE, offen: Turing prueft sich hier selbst — dieselbe Hand,
   dasselbe Haus. Die zweite Hand fehlt.
   ========================================================================== */
if (! function_exists('turing_selbstpruefung')) {
function turing_selbstpruefung(): array {
    $r = array();
    $p = function (string $was, bool $ok, string $d) use (&$r) {
        $r[] = array('was' => $was, 'ok' => $ok, 'grund' => $d);
    };

    $ext = array('ext_abc123' => array(
        'id'      => 'ext_abc123',
        'name'    => 'Beispielmensch',
        'discord' => 'jemand#1',
        'matrix'  => '',
        'lang'    => 'Russisch',
        'tz'      => 'Asia/Vladivostok',
    ));
    $m = turing_spiegel_mask_externals($ext);
    $e = (array) ($m['ext_abc123'] ?? array());

    $p('der Name eines Externen ist maskiert', ($e['name'] ?? '') === '***', 'seit 0.0.86');
    $p('die Sprache ist maskiert',  ($e['lang'] ?? '') === '***', 'sonst steht die Herkunft im Dump');
    $p('die Zeitzone ist maskiert', ($e['tz']   ?? '') === '***', 'eine Zeitzone ist ein Punkt auf der Karte');
    $p('die Kennung bleibt lesbar', ($e['id']   ?? '') === 'ext_abc123', 'der Anker fuers Gespraech');

    $leer = turing_spiegel_mask_externals(array('ext_x' => array('id' => 'ext_x', 'name' => '', 'lang' => '', 'tz' => '')));
    $l    = (array) ($leer['ext_x'] ?? array());
    $p('leer bleibt leer', ($l['lang'] ?? '_') === '' && ($l['tz'] ?? '_') === '',
       'ob gefuellt bleibt sichtbar, nur nicht WAS');

    $p('sysop_mail ist maskiert',
       turing_spiegel_scrub('wer@example.org', 'sysop_mail') === '***',
       'der Spiegel geht per Bauart an eine KI');
    $p('eine leere Adresse bleibt leer',
       turing_spiegel_scrub('', 'sysop_mail') === '', 'gleiche Regel wie bei Tokens');
    $p('der Zeitstempel daneben bleibt lesbar',
       turing_spiegel_scrub('1786013929', 'sysop_mail_ts') === '1786013929',
       'sagt WANN, nicht WER');
    $p('ein Token ist maskiert',
       turing_spiegel_scrub('geheim', 'edge_token') === '***', 'Kanten-Tokens im KI-Chat waeren ein Leck');
    $p('ein Beitragstext wird zu Laenge und Abdruck',
       is_array(turing_spiegel_scrub('Hallo Welt', 'body', 'salz')), 'Struktur statt Inhalt');

    /* v3.2.0 — DIE ZWEI ZEILEN, DIE ZEHN FASSUNGEN LANG GEFEHLT HABEN.
       Z6 hat den Wegfall des Nachsalzens (v0.0.89) eingebaut und gemessen:
       KEINE der zehn Behauptungen wurde rot. Die Reparatur stand seit
       0.0.89 im Code und niemand bewachte sie.

           Eine Reparatur, deren Wegfall keiner bemerkt, hat nie einen
           Waechter gehabt — sie hatte nur Glueck.

       Der Fund selbst war eine Gegenprobe, kein Zufall: nicht "sind die
       Behauptungen gruen", sondern "faellt es auf, wenn ich das Gegenteil
       einbaue". */
    $p('ein gespeicherter Fingerabdruck erscheint nicht im Klartext',
       turing_spiegel_scrub('abc', 'preamble_hash', 's1') !== 'abc',
       'seit 0.0.89 — er wird gehasht, nicht durchgereicht');
    $p('und er wird je Dump nachgesalzen',
       turing_spiegel_scrub('abc', 'preamble_hash', 's1') !== turing_spiegel_scrub('abc', 'preamble_hash', 's2'),
       'ohne Salz kann jeder eine kurze Praeambel raten und vergleichen');

    return $r;
}
}

/* ==========================================================================
   ========================================================================
   3.2.0 — DER INSTALLER.   DIE REGELN.

   Vertrag 16 · 16a · 16b · 16c · 16d + NACHTRAG.   Maillart, 04.09.2026.

   --------------------------------------------------------------------
   DIE TEILUNG, AUF DER ALLES ANDERE STEHT: REGEL UND HAND
   --------------------------------------------------------------------

   tu_inst_*   DIE REGELN. Kein Aufruf einer WordPress-Funktion. Keiner.
               Das ist alles, was die Vertraege entschieden haben: zwei
               Phasen, Boden statt Namensliste, die Marke, die Haertung,
               die Schale. Es laeuft in jedem PHP.

   tu_hand_*   DIE HAND. Das EINZIGE, was WordPress anfasst: entpacken,
               legen, entfernen, aktivieren, deaktivieren, Marke.
               Acht Funktionen in einem Feld.

   Der Grund ist nicht Schoenheit, sondern Messbarkeit. Die Zeugen T1..T5
   bringen ihre eigene Hand mit (eine, die nur die Platte anfasst) und
   messen damit DIESEN Code — nicht eine Nachbildung davon.

       Ein Pruefstand, der die Regel nachbaut, um sie zu pruefen, misst
       seine Nachbildung. Einer, der die Regel laufen laesst und nur die
       Hand ersetzt, misst die Regel.

   Was dadurch ungemessen BLEIBT, steht damit auch fest, und das ist der
   ehrliche Teil: die Hand selbst. unzip_file(), copy_dir(),
   activate_plugin() sind auf keinem Pruefstand messbar. Sie sind
   absichtlich duenn — jede von ihnen ist ein Aufruf und eine Rueckgabe,
   damit dort so wenig wie moeglich falsch sein KANN.

   --------------------------------------------------------------------
   WAS 3.2.0 AN TURING SELBST AENDERT — UND ES IST DIE KERNEIGENSCHAFT
   --------------------------------------------------------------------

   Bis 3.1.1 stand im Kopf dieser Datei: "Schreibt nichts." Umberto hat es
   gemessen, und der Kasper-Absatz nennt es "Turings einzige wirkliche
   Eigenschaft".

   Ein Installer schreibt. Die Eigenschaft faellt, und sie faellt nicht
   still — sie wird ERSETZT durch eine engere, die genauso pruefbar ist:

       KEINE Option. KEINE Benutzer-Meta. KEINE Tabelle. KEINE Transiente.
       Ins Dateisystem nur unter wp-content/plugins/, nur auf Knopfdruck
       eines angemeldeten Menschen, und nur das, was er ausgewaehlt hat.

   Die Marke (16c §1) liegt deshalb ALS DATEI in wp-content/plugins/ und
   nicht in einer Option. Ich hatte am 03.09. das Gegenteil empfohlen und
   nehme es zurueck: mein Einwand war, eine Marke duerfe nicht in dem
   Medium liegen, dessen Versagen sie bezeugt. Das Medium ist aber nicht
   der Hangar, sondern plugins/ — und wenn plugins/ nicht beschreibbar
   ist, beginnt Phase 2 gar nicht erst. Es gibt keinen Fall, in dem die
   Marke fehlt und gebraucht wird.

       Eine Marke gehoert in das Medium, das sie bezeugt, wenn ein
       unbeschreibbares Medium den Vorgang ohnehin verhindert.

   Damit bleibt "keine Option" wahr, "rueckstandslos entfernen" bleibt
   wahr, und es braucht kein uninstall.php.

   --------------------------------------------------------------------
   UND EIN BEFUND ZU 16c §1, DER BEIM BAUEN AUFTAUCHTE
   --------------------------------------------------------------------

   16c §1 sagt fuer den Fehlschlag in Phase 2: "die schon gelegten wieder
   entfernen". Das ist richtig fuer ein Paket, das VORHER NICHT DA WAR.

   Fuer eine Aktualisierung ist es falsch: dort hat "legen" ein Plugin
   ersetzt, das lief. Wer es dann "wieder entfernt", loescht die
   funktionierende Fassung des Admins und laesst nichts zurueck.

       Zuruecknehmen heisst nicht wegnehmen. Es heisst: den Zustand
       herstellen, der vorher war — und der war bei einer Aktualisierung
       nicht "leer".

   Deshalb wandert eine vorhandene Fassung VOR dem Legen nach
   plugins/.turing-alt/<ordner> und bei einem Fehlschlag zurueck. Der
   Punkt braucht einen Zeugen (T7, Vorschlag im Bericht).
   ========================================================================
   ========================================================================== */

/* Formvertrag dieses Abschnitts: JEDE tu_inst_-Funktion endet mit einer
   schliessenden Klammer in Spalte 1. Der Pruefstand holt sie daran heraus.
   Eine einzeilige Funktion waere kuerzer und wuerde die naechste
   mitverschlucken — gemessen, nicht vermutet. */

/** Wo die Marke liegt. Ein Name, eine Stelle. */
if (! function_exists('turing_inst_marke_name')) {
function turing_inst_marke_name(): string {
    return '.turing-satz.json';
}
}

/** Wohin eine vorhandene Fassung waehrend Phase 2 ausweicht. */
if (! function_exists('turing_inst_alt_name')) {
function turing_inst_alt_name(): string {
    return '.turing-alt';
}
}

/* --------------------------------------------------------------------------
   EIN ARCHIV BEFRAGEN.

   DIE FAMILIENFRAGE UND DIE BODENFRAGE SIND DIESELBE MESSUNG.

   16c §2 fragt: "Enthaelt das ZIP <ordner>/includes/kasper.php?" Das ist
   eine Pruefung auf einen DATEINAMEN — eine Ebene unter der Namensliste,
   die derselbe Absatz verbietet. Ein fremdes Plugin mit einer Datei dieses
   Namens waere Familie; ein Boden, der eines Tages anders heisst, waere es
   nicht mehr. Das ist H184s Form, nicht ihre Reparatur.

   Turing muss CLUSTER_ONE_BODEN_FASSUNG ohnehin lesen — ohne den Wert gibt
   es keine Satz-Gleichheit (16a §6). Also ist der gelesene Wert selbst die
   Auskunft, und die Familienfrage kostet keine zweite Messung.

   Gelesen wird das Archiv, NICHT ausgefuehrt. preg_match auf den Quelltext,
   kein include. Ein Installer, der ein Paket ausfuehrt, um zu erfahren, ob
   er es installieren darf, hat es bereits installiert.
   -------------------------------------------------------------------------- */
/* 18.10.2026 (Neo): Die Archive im Hangar, unabhaengig von der Schreibweise
   der Endung. glob('*.zip') uebersah auf einem Linux-Server jede Datei, die
   als .ZIP hochgeladen wurde; GLOB_BRACE gibt es nicht auf jedem System
   (musl). Also scandir und ein Blick auf die Endung. Sortiert, damit die
   Reihenfolge der Kacheln stabil bleibt. */
if (! function_exists('turing_hangar_zips')) {
function turing_hangar_zips(string $pfad): array {
    $pfad = rtrim($pfad, '/\\');
    if ($pfad === '' || ! is_dir($pfad)) { return array(); }
    $aus = array();
    foreach ((array) scandir($pfad) as $n) {
        if (is_string($n) && preg_match('/\.zip$/i', $n) && is_file($pfad . '/' . $n)) {
            $aus[] = $pfad . '/' . $n;
        }
    }
    sort($aus, SORT_STRING);
    return $aus;
}
}

if (! function_exists('turing_inst_paket_lesen')) {
function turing_inst_paket_lesen(string $zip): array {
    $leer = array(
        'art' => 'kaputt', 'datei' => basename($zip), 'ordner' => '', 'name' => '',
        'fassung' => '', 'fassung_am_namen' => '', 'boden' => '', 'entfernt' => '',
        'hauptdatei' => '', 'sha256' => is_file($zip) ? (string) hash_file('sha256', $zip) : '',
        'grund' => 'kein lesbares Archiv',
    );

    if (! class_exists('ZipArchive') || ! is_file($zip)) { return $leer; }

    $z = new ZipArchive();
    if ($z->open($zip, ZipArchive::CHECKCONS) !== true) { return $leer; }

    $wurzeln = array();
    for ($i = 0; $i < $z->numFiles; $i++) {
        $w = explode('/', (string) $z->getNameIndex($i))[0];
        if ($w !== '') { $wurzeln[$w] = true; }
    }
    if (count($wurzeln) !== 1) {
        $z->close();
        $leer['grund'] = 'das Archiv hat ' . count($wurzeln) . ' oberste Ordner statt einem';
        return $leer;
    }
    $ordner = (string) array_key_first($wurzeln);

    /* 18.10.2026 (Neo), B6 — DER ORDNERNAME KOMMT AUS DEM ARCHIV, ALSO WIRD
       ER GEPRUEFT. Er wandert spaeter ungefiltert in plugins/<ordner> fuer
       rename, copy_dir und das rekursive Entfernen. Ein Archiv mit der
       Wurzel „..", „.turing-alt" oder einem Backslash haette alle Pruefungen
       oben passieren koennen (eine Wurzel, Boden-Konstante, Kopf) und
       danach ausserhalb des Zielordners gearbeitet. Wer in den Hangar
       schreiben darf, hat zwar ohnehin viel — aber nicht wp-content. */
    if ($ordner === '.' || $ordner === '..' || $ordner[0] === '.'
        || strpbrk($ordner, "\\/:*?\"<>|") !== false
        || ! preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{0,99}$/', $ordner)) {
        $z->close();
        $leer['grund'] = 'der oberste Ordner des Archivs traegt einen unzulaessigen Namen';
        return $leer;
    }

    /* Die Eigenschaft: liest sich eine Bodenfassung? */
    $boden = '';
    for ($i = 0; $i < $z->numFiles; $i++) {
        $n = (string) $z->getNameIndex($i);
        if (substr($n, -4) !== '.php') { continue; }
        $c = (string) $z->getFromIndex($i);
        if (preg_match('~define\s*\(\s*[\'"]CLUSTER_ONE_BODEN_FASSUNG[\'"]\s*,\s*[\'"]([0-9][0-9.]*)[\'"]~', $c, $m)) {
            $boden = $m[1];
            break;
        }
    }

    /* Der Kopf: die Fassung steht IM Paket, nie am Dateinamen (H184). */
    $haupt = $ordner . '/' . $ordner . '.php';
    $kopf  = (string) $z->getFromName($haupt);
    if ($kopf === '') {
        for ($i = 0; $i < $z->numFiles; $i++) {
            $n = (string) $z->getNameIndex($i);
            if (substr_count($n, '/') !== 1 || substr($n, -4) !== '.php') { continue; }
            $probe = (string) $z->getFromIndex($i);
            if (stripos($probe, 'Plugin Name:') !== false) { $haupt = $n; $kopf = $probe; break; }
        }
    }
    $z->close();

    $am_namen = '';
    if (preg_match('~-([0-9]+(?:[._][0-9]+)+)\.zip$~i', basename($zip), $m)) {
        $am_namen = str_replace('_', '.', $m[1]);
    }

    return array(
        'art'              => $boden !== '' ? 'familie' : 'fremd',
        'datei'            => basename($zip),
        'pfad'             => $zip,
        'ordner'           => $ordner,
        'name'             => turing_inst_kopfzeile($kopf, 'Plugin Name'),
        'fassung'          => turing_inst_kopfzeile($kopf, 'Version'),
        'fassung_am_namen' => $am_namen,
        'boden'            => $boden,
        'entfernt'         => turing_inst_kopfzeile($kopf, 'Cluster-One-Entfernt'),
        'hauptdatei'       => $haupt,
        'sha256'           => (string) hash_file('sha256', $zip),
        'grund'            => '',
    );
}
}

if (! function_exists('turing_inst_kopfzeile')) {
function turing_inst_kopfzeile(string $kopf, string $feld): string {
    if (preg_match('~^\s*\*?\s*' . preg_quote($feld, '~') . '\s*:\s*(.+)$~mi', $kopf, $m)) {
        return trim($m[1]);
    }
    return '';
}
}

/* --------------------------------------------------------------------------
   DER HANGAR (16a, 16b §5).

   Was hier NICHT passiert: schweigend uebergehen, was nicht passt. Jedes
   Archiv landet in genau einem der vier Faecher, und drei davon werden
   auf dem Schirm GENANNT.

       Ein Verzeichnis, das schweigend ignoriert, was es nicht kennt, ist
       die Verzeichnisprobe von gestern, nur andersherum.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_hangar_lesen')) {
function turing_inst_hangar_lesen(string $pfad): array {
    $aus = array('kacheln' => array(), 'aeltere' => array(), 'fremd' => array(), 'kaputt' => array());
    if ($pfad === '' || ! is_dir($pfad)) { return $aus; }

    $alle = array();
    foreach (turing_hangar_zips($pfad) as $z) {   // 18.10.2026 (Neo): auch .ZIP
        $p = turing_inst_paket_lesen((string) $z);
        if     ($p['art'] === 'kaputt')  { $aus['kaputt'][] = $p; }
        elseif ($p['art'] === 'fremd')   { $aus['fremd'][]  = $p; }
        else                             { $alle[]          = $p; }
    }

    /* Je Ordner EINE Kachel (16b §5) — aber die Kachel kennt jetzt alle
       Fassungen, die fuer diesen Ordner im Hangar liegen.

       07.09.2026, aus Mammuts Messung auf Christians DEV. Bis 3.7.0 warf
       diese Schleife die aelteren Pakete in 'aeltere', und 'aeltere' war
       eine Sackgasse: sichtbar, nicht anklickbar, fuer immer. Damit war
       der Rueckschritt-Riegel aus Vertrag 22 §3 zwar gebaut, bezeugt
       (T10) und im Lauf richtig — nur konnte ihn kein Paket mehr
       erreichen.

           Ein Riegel, den kein Weg erreicht, ist keine Sicherung. Er ist
           eine Zusage, die niemand einloesen kann.

       WAS 16b §5 WIRKLICH SCHUETZT, bleibt: ein Satz nimmt je Ordner
       GENAU EIN Paket. Deshalb wird hier nicht die Zahl der Kacheln
       erhoeht — die Kachel bekommt eine Fassungswahl. Aufgeloest wird
       sie von tu_inst_wahl_aufloesen(), und die ist nach ORDNER
       geschluesselt: zwei Pakete desselben Ordners in einem Satz sind
       damit nicht mehr verboten, sondern unmoeglich.

       'aeltere' bleibt als LISTE bestehen — die Zeile "was sonst im
       Hangar liegt" war richtig und ist es weiter. Sie ist nur keine
       Sackgasse mehr. */
    $nach_ordner = array();
    foreach ($alle as $p) { $nach_ordner[$p['ordner']][] = $p; }
    foreach ($nach_ordner as $o => $liste) {
        usort($liste, function ($a, $b) {
            return version_compare((string) $b['fassung'], (string) $a['fassung']);
        });
        $nach_ordner[$o] = $liste;
        $aus['kacheln'][] = $liste[0];
        foreach (array_slice($liste, 1) as $alt) { $aus['aeltere'][] = $alt; }
    }
    usort($aus['kacheln'], function ($a, $b) { return strcmp($a['ordner'], $b['ordner']); });
    $aus['nach_ordner'] = $nach_ordner;

    return $aus;
}
}

/* --------------------------------------------------------------------------
   VON DER AUSWAHL ZUM PAKET — JE ORDNER GENAU EINES.

   Diese Regel loest auf, was der Mensch angekreuzt hat: welche Ordner, und
   je Ordner welche Fassung. Sie ist nach ORDNER geschluesselt, und das ist
   die eigentliche Zusicherung — nicht "zwei Pakete desselben Ordners sind
   verboten", sondern "sie passen nicht in das Ergebnis".

   Ohne Fassungswahl liefert sie die hoechste. Eine Fassungswahl, die nicht
   im Hangar liegt, wird nicht geraten und nicht durchgereicht: sie faellt
   auf die hoechste zurueck. Ein Paket, das der Hangar nicht kennt, kommt
   ueber diese Regel nicht herein — auch nicht ueber ein selbstgebautes
   Formular.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_wahl_aufloesen')) {
function turing_inst_wahl_aufloesen(array $hangar, array $ordner_wahl, array $fassung_wahl): array {
    $nach_ordner = (array) ($hangar['nach_ordner'] ?? array());
    $aus         = array();

    foreach ($ordner_wahl as $o) {
        $o = (string) $o;
        if (! isset($nach_ordner[$o]) || ! $nach_ordner[$o]) { continue; }
        $liste   = (array) $nach_ordner[$o];
        $gewollt = (string) ($fassung_wahl[$o] ?? '');
        $treffer = null;
        foreach ($liste as $p) {
            if ((string) $p['datei'] === $gewollt) { $treffer = $p; break; }
        }
        if ($treffer === null) { $treffer = $liste[0]; }
        $aus[$o] = $treffer;
    }
    return $aus;
}
}

/* --------------------------------------------------------------------------
   WAS LIEGT SCHON, UND IN WELCHEM ZUSTAND (16a §4).
   -------------------------------------------------------------------------- */
/* v3.9.4 — DER FUENFTE ZUSTAND, UND WARUM ER EINER SEIN MUSS.

   Mammut, 10.09.2026: „gleiche_fassung gibt es nur, wenn der Ordner auch
   AKTIV ist; ein ausgeschalteter Ordner mit derselben Fassung fiel auf
   inaktiv. Auf Christians DEV heisst das: der Monolith steht mit
   1.9.13 → 1.9.13 zur Auswahl — beide Zahlen gleich, der Pfeil zeigt auf
   sich selbst — und ein Lauf wuerde ihn einschalten."

   SEINE FRAGE: ist „gleiche Fassung, aber aus" eine Kachel ohne Arbeit,
   oder eine Kachel, deren Arbeit Einschalten heisst?

   MEINE ANTWORT: eine Kachel MIT Arbeit, und die Arbeit heisst
   Einschalten. Drei Gruende, und der dritte ist der, der es entscheidet:

     1  Vertrag 22 §2: liegt ≠ laeuft. Ein Paket, das liegt und aus ist,
        ist nicht im Satz. Turing legt den Satz, nicht die Dateien.
     2  tu_inst_kachel_tot() fragt, ob sich irgendeine Fassung im Hangar
        von dem unterscheidet, was hier liegt UND LAEUFT. Ein
        ausgeschaltetes Paket unterscheidet sich davon — die Kachel ist
        also zu Recht lebendig. Der Fehler lag nicht in der Sperre,
        sondern in der SPALTE.
     3  Und Einschalten ist etwas anderes als Aufspielen: es dreht die
        Entscheidung eines MENSCHEN um. Eine Aktualisierung tut das nicht.
        Was der Lauf mit der Hand eines Menschen macht, muss vorher
        dastehen — nicht, weil es falsch waere, sondern weil es nicht
        stillschweigend richtig sein kann.

   DESHALB: ein eigener Zustand statt eines stillen Durchfallens. Der
   Pfeil verschwindet, wenn die Fassungen gleich sind:

       Ein Pfeil, der auf sich selbst zeigt, kuendigt Arbeit an, die es
       nicht gibt — und verschweigt die, die es gibt.

   PACIOLIS SATZ, an mir selbst: ein neuer Zustand trifft weiter unten auf
   Apparate, die ihn nicht kennen. Es sind ZWEI, und beide sind in diesem
   Zug mitgegangen:
       tu_inst_kachel_tot()  — fragt !== 'gleiche_fassung'; der neue
                               Zustand ist nicht gleiche_fassung, also
                               bleibt die Kachel lebendig. RICHTIG, ohne
                               Aenderung. Nachgesehen, nicht angenommen.
       die Zustandsspalte    — hat einen eigenen Text bekommen.
   Mehr Leser hat dieser Wert nicht; gezaehlt mit einem Griff nach
   tu_inst_zustand( im ganzen Haus: zwei Aufrufe.

   WAS NOCH AUSSTEHT und nicht in dieser Fassung steht: dass das Kaestchen
   fuer „einschalten" ein eigenes ist. Das aendert die Form des Formulars
   und damit „alle oder keiner"; es gehoert in einen eigenen Zug. Heute
   ist die Stille weg, nicht die Zusammenfassung zweier Arbeiten. */
if (! function_exists('turing_inst_zustand')) {
function turing_inst_zustand(array $paket, array $liegt): string {
    $o = $paket['ordner'];
    if (! isset($liegt[$o])) { return 'nicht_da'; }
    $gleich = (string) $liegt[$o]['fassung'] === (string) $paket['fassung'];
    $an     = ! empty($liegt[$o]['aktiv']);
    if ($gleich) { return $an ? 'gleiche_fassung' : 'gleiche_fassung_aus'; }
    return $an ? 'aktiv' : 'inaktiv';
}
}

/* --------------------------------------------------------------------------
   DER PFEIL. v3.9.4.

   Er stand als Ausdruck mitten in der Seite und war damit nur im Quelltext
   nachzulesen, nicht zu messen. Eine Regel, die ein Zeuge nur LESEN kann,
   ist eine Regel, die er nachbaut — und dann prueft er sein Modell. Also
   steht sie hier, wo man sie aufrufen kann.

   Leer ist richtig, wenn es nichts zu wechseln gibt: kein alter Wert, oder
   der alte ist der neue.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_pfeil')) {
function turing_inst_pfeil(string $alt, string $neu): string {
    if ($alt === '' || $alt === $neu) { return ''; }
    return ' · ' . $alt . ' &rarr; ' . $neu;
}
}

/* --------------------------------------------------------------------------
   v3.9.8 — IN WELCHER LAGE IST EINE FREMDE ADMINSEITE?

   WAS HIER BIS 3.9.7 STAND, war nicht diese Funktion, sondern ein Schluss
   mitten in der Seite:

       menu_page_url() gibt '' zurueck  ->  „WordPress kennt den Slug nicht"

   DER SCHLUSS IST FALSCH, und zwar genau fuer die Seite, um die es ging.
   Mammut hat die Frage am echten WordPress-Kern gemessen (W66, 11.09.2026),
   ich habe sie gegen drei Baeume nachgefahren — 7.0, 7.1 und 7.2-alpha,
   dreimal dasselbe:

       add_submenu_page(null, …)  legt  $_parent_pages[$slug] = null  ab.
       menu_page_url()            fragt  isset($_parent_pages[$slug]).
       isset(null)                ist    FALSE.
       -> die Seite ist ANGEMELDET und ERREICHBAR und wird trotzdem nicht
          aufgeloest.

   So meldet sich die Landkarte seit VERTRAG 17 §3 an. Die Zeile, die ich
   gegen falsche Auskuenfte ueber fremde Haeuser gebaut habe, hat also selbst
   eine falsche Auskunft ueber ein fremdes Haus gegeben — drei Tage lang.

       isset() fragt nach dem WERT, array_key_exists() nach dem SCHLUESSEL.
       Wer beides verwechselt, haelt ein null fuer eine Abwesenheit.

   UND DESHALB LEITET DIESE FUNKTION NICHTS MEHR AB. Sie bekommt vier bis
   sechs GEMESSENE Angaben und gibt eine LAGE zurueck. Keine davon ist
   geraten; welche fehlt, steht in der Lage selbst.

       register_da    ist $_parent_pages ueberhaupt lesbar (Array)?
       schluessel     steht der Slug als SCHLUESSEL darin (array_key_exists)?
       eltern         welcher Wert — false = oberste Ebene, null = versteckt,
                      Zeichenkette = Kind eines sichtbaren Eintrags
       url            was menu_page_url() sagt
       reg_versteckt  kennt $_registered_pages den Haken admin_page_<slug>?
                      (das Tor, an dem menu.php wp_die() ruft)
       haken          traegt dieser Haken einen Rueckruf? (has_action — das
                      Tor, das admin.php benutzt)
                      null bei beiden = nicht feststellbar, NICHT „nein"

   Die beiden Tore sind zwei verschiedene, und sie sind beide echt; gemessen
   am Kern 7.0/7.1/7.2-alpha (menu.php:375 -> plugin.php user_can_access_
   admin_page, und admin.php:189 -> get_plugin_page_hook -> has_action).
   Wer nur eines fragt und „erreichbar" sagt, hat das andere geraten.

   T24 faehrt die Lagen, und W66 liefert die Werte, die T24 einsetzt.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_seitenlage')) {
function turing_inst_seitenlage(array $mess): string {
    $url = isset($mess['url']) ? (string) $mess['url'] : '';
    if ($url !== '') { return 'aufgeloest'; }

    if (empty($mess['register_da'])) { return 'unbekannt'; }
    if (empty($mess['schluessel'])) { return 'nicht_angemeldet'; }

    $eltern = array_key_exists('eltern', $mess) ? $mess['eltern'] : false;
    if ($eltern !== null) { return 'angemeldet_ohne_url'; }

    $reg = array_key_exists('reg_versteckt', $mess) ? $mess['reg_versteckt'] : null;
    $hak = array_key_exists('haken', $mess) ? $mess['haken'] : null;
    return ($reg === true && $hak === true) ? 'versteckt_offen' : 'versteckt_halb';
}
}

/* --------------------------------------------------------------------------
   IST AN DIESER KACHEL NICHTS ZU TUN?

   07.09.2026, dritter Anlauf auf Christians DEV. Die Fassungsliste stand
   da und war richtig — und das Kaestchen daneben war tot. Der Grund:

       ($z === 'gleiche_fassung' ? ' disabled' : '')

   Das rechnete aus der VORWAHL. Seit 3.8.0 vertritt eine Kachel aber
   MEHRERE Fassungen; wenn die Vorwahl schon liegt, die 0.2.4 daneben
   aber nicht, ist an dieser Kachel sehr wohl etwas zu tun.

       Eine Sperre, die aus einem Element einer Menge gerechnet wird,
       sperrt die ganze Menge. Das war dieselbe Form wie H240 und wie
       der Auswahlfilter von 3.7.0 — nur eine Etage weiter, im
       HTML-Attribut.

   Und in VERTRAG 24 §1 habe ich diese Zeile selbst "richtig so"
   genannt. Sie war richtig fuer eine Kachel, die EIN Paket vertrat.

   Die Sperre gehoert an die MENGE: tot ist die Kachel nur, wenn KEINE
   Fassung im Hangar sich von dem unterscheidet, was auf dem Knoten
   liegt. Was "unterscheidet sich" heisst, entscheidet nicht diese
   Funktion — sie fragt tu_inst_zustand(). Zwei eigene Vorstellungen
   desselben Wortes waeren wieder H240.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_kachel_tot')) {
function turing_inst_kachel_tot(array $fassungen, array $liegt): bool {
    if (! $fassungen) { return true; }
    foreach ($fassungen as $p) {
        if (turing_inst_zustand((array) $p, $liegt) !== 'gleiche_fassung') { return false; }
    }
    return true;
}
}

/* --------------------------------------------------------------------------
   DIE ZAHL FUER DIE ZEILE UEBER DEM KNOPF. v3.9.6.

   Christians Entscheidung vom 11.09.2026: „Die Zeile kommt. ‚N Pakete werden
   eingeschaltet', ueber dem Knopf, in derselben Form wie der
   Rueckschritt-Riegel — sie verspricht genau das, was das System tut."

   Sie ist der ERSATZ fuer das zweite Kaestchen, nicht sein Gegenteil: ein
   Haken „nicht einschalten" waere ein Haken fuer einen halben Satz (16a §6,
   gemessen in T21 FORDERUNG 9). Eine Ankuendigung nimmt dem Lauf nichts und
   gibt dem Menschen alles, was er braucht.

   WARUM DIE ZAHL EINE REGEL IST UND KEIN AUSDRUCK IN DER SEITE: weil ein
   Zeuge sie sonst nur lesen koennte, und dann prueft er sein Modell. Das
   ist die Lehre von tu_inst_pfeil(), einen Tag alt.

   WAS SIE ZAEHLT, und das ist die schaerfste Stelle: nicht „wie viele
   Pakete eingeschaltet werden" — das weiss vor dem Ankreuzen niemand —,
   sondern bei wie vielen ANWAEHLBAREN Kacheln das Einschalten die EINZIGE
   Wirkung ist. Dieselbe Fassung liegt schon, nur der Schalter fehlt.

       Eine Zahl, die vor der Auswahl behauptet, was nach der Auswahl
       geschieht, ist ein Versprechen ueber eine Entscheidung, die noch
       niemand getroffen hat.

   Der allgemeine Satz daneben gilt unbedingt, und er ist gemessen: JEDER
   Lauf aktiviert JEDES gelegte Paket (Phase 2, Zeile 4).
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_einschalten_zahl')) {
function turing_inst_einschalten_zahl(array $kacheln, array $nach_ordner, array $liegt): array {
    $waehlbar = 0;
    $nur_schalter = array();
    foreach ($kacheln as $p) {
        $p = (array) $p;
        $o = (string) ($p['ordner'] ?? '');
        if ($o === '') { continue; }
        $liste = (array) ($nach_ordner[$o] ?? array($p));
        if (turing_inst_kachel_tot($liste, $liegt)) { continue; }
        $waehlbar++;
        if (turing_inst_zustand($p, $liegt) === 'gleiche_fassung_aus') { $nur_schalter[] = $o; }
    }
    sort($nur_schalter);
    return array('waehlbar' => $waehlbar, 'nur_schalter' => $nur_schalter);
}
}

/* --------------------------------------------------------------------------
   DIE SATZ-PRUEFUNG (H169, 16a §6). Ueber die GANZE Auswahl, VOR dem
   ersten Kopieren.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_satz_pruefen')) {
function turing_inst_satz_pruefen(array $pakete, string $boden_am_knoten): array {
    if (! $pakete) {
        return array('ok' => false, 'grund' => 'nichts ausgewaehlt');
    }
    $boeden = array_values(array_unique(array_column($pakete, 'boden')));
    if (count($boeden) > 1) {
        return array('ok' => false,
                     'grund' => 'die Auswahl traegt ' . count($boeden) . ' verschiedene Boeden ('
                              . implode(', ', $boeden) . ') — ein Satz ist EIN Boden');
    }
    if ($boden_am_knoten !== '' && $boden_am_knoten !== $boeden[0]) {
        return array('ok' => false,
                     'grund' => 'der Boden der Auswahl (' . $boeden[0] . ') passt nicht zu dem, '
                              . 'was auf diesem Knoten liegt (' . $boden_am_knoten . ')');
    }
    return array('ok' => true, 'grund' => '', 'boden' => $boeden[0]);
}
}

/* --------------------------------------------------------------------------
   H228 — DIE BEDINGUNG GILT DEM ERGEBNIS, NICHT DEM ZUSTAND VORHER.

   Bis 3.3.0 stand hier: "der Boden der Auswahl muss zu dem passen, was auf
   diesem Knoten liegt". Diese Zeile blockiert auf einem VERALTETEN Knoten
   jeden Nachtrag — auch den, der ihn gerade in Ordnung braechte. Wer einen
   Knoten mit Boden 1.7.0 vollstaendig auf 1.8.0 hebt, tut genau das
   Richtige, und die Regel sagte nein.

   Und sie liess umgekehrt durch, was sie verhindern sollte: bleibt EIN
   alter Ordner stehen, den die Auswahl nicht abdeckt, ist der Knoten
   danach gemischt — H169, nur schneller als von Hand.

       Eine Bedingung an den Zustand VOR dem Zug verbietet den Zug, der
       den Zustand heilt. Gemeint war immer der Zustand DANACH.

   Also wird gerechnet, was nach dem Lauf liegen wuerde:

       was liegt   MINUS was die Auswahl ersetzt   PLUS die Auswahl
       -> traegt das GENAU EINEN Boden?

   DER DRITTE AUSGANG IST PFLICHT UND ER IST DER GRUND, WARUM DIESE
   FUNKTION EINE 'lage' ZURUECKGIBT UND KEIN JA/NEIN:

   Ein Familienordner, dessen Bodenfassung sich NICHT LESEN laesst, ist
   nicht "kein Boden". Er ist NICHT GEMESSEN. Wer ihn als leer zaehlt,
   rechnet ihn aus der Menge heraus und bekommt eine gruene Antwort auf
   eine Frage, die niemand beantwortet hat.

       Ein Ordner, den man nicht lesen kann, ist keine Null. Er ist eine
       offene Frage, und eine offene Frage macht die Summe nicht kleiner.
   -------------------------------------------------------------------------- */
/* --------------------------------------------------------------------------
   WER ZUR FAMILIE GEHOERT, ENTSCHEIDET DER BODEN — NICHT DER ORDNERNAME.

   07.09.2026, gefunden auf Mammuts Frage nach der gefilterten Tabelle. Bis
   3.6.0 stand die Auswahl fuer die Tabelle "Was auf diesem Knoten liegt"
   mitten auf der Seite und lautete:

       if (strpos($o, 'cluster-one') === 0 || $o === 'turing')

   Das ist eine Messung am NAMEN. Dieses Haus misst Zugehoerigkeit
   ausdruecklich am Boden (CLUSTER_ONE_BODEN_FASSUNG), und zwar seit
   Vertrag 16 — weil ein Name abgeschrieben werden kann und ein Boden
   gelesen werden muss.

       Ein fremder Ordner, der 'cluster-one-' heisst, stand in der
       Familientabelle. Ein Familienordner, der anders heisst, stand
       nicht darin. Beides still, und zwei Tabellen auf derselben
       Seite widersprachen einander, ohne dass eine von beiden sagte,
       wonach sie filtert.

   Diese Regel ist die eine Stelle, an der die Frage jetzt beantwortet
   wird. Sie rechnet nichts nach: sie nimmt die Liste des Wirts und
   behaelt daraus, was auf der Platte einen Boden traegt.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_familie_aus_liste')) {
function turing_inst_familie_aus_liste(array $liegt, array $auf_platte): array {
    $aus = array();
    foreach ($liegt as $o => $d) {
        $o = (string) $o;
        if (! isset($auf_platte[$o])) { continue; }
        $aus[$o] = $d;
    }
    return $aus;
}
}

if (! function_exists('turing_inst_ergebnis_pruefen')) {
function turing_inst_ergebnis_pruefen(array $auswahl, array $liegt, array $im_hangar = array(),
                                  bool $bestaetigt_ruhend = false): array {
    $satz = turing_inst_satz_pruefen($auswahl, '');
    if (! $satz['ok']) {
        return array('lage' => 'rot', 'grund' => $satz['grund'], 'boden' => '');
    }
    $boden_neu = (string) $satz['boden'];

    /* Erst der dritte Ausgang. Was nicht gemessen ist, wird nicht gerechnet. */
    $stumm = array();
    foreach ($liegt as $ordner => $d) {
        if (empty($d['lesbar'])) { $stumm[] = (string) $ordner; }
    }
    if ($stumm) {
        return array('lage' => 'nicht_gemessen', 'boden' => $boden_neu,
                     'grund' => 'die Bodenfassung laesst sich nicht lesen bei: '
                              . implode(', ', $stumm)
                              . '. Solange das offen ist, kann niemand sagen, ob der Knoten '
                              . 'danach EINEN Boden traegt — das ist NICHT GEMESSEN und nicht gruen.');
    }

    /* Was die Auswahl ersetzt, faellt heraus. Was stehenbleibt, zaehlt. */
    $ersetzt = array_column($auswahl, 'ordner');
    $aktiv   = array();
    $ruhend  = array();
    foreach ($liegt as $ordner => $d) {
        $o = (string) $ordner;
        if (in_array($o, $ersetzt, true))      { continue; }
        if ((string) $d['boden'] === $boden_neu) { continue; }
        if (! empty($d['aktiv'])) { $aktiv[$o]  = (string) $d['boden']; }
        else                      { $ruhend[$o] = (string) $d['boden']; }
    }

    if ($aktiv) {
        return array('lage' => 'rot', 'boden' => $boden_neu,
                     'grund' => 'nach diesem Lauf traegt der Knoten zwei Boeden, und der fremde '
                              . 'ist AKTIV. Die Auswahl bringt ' . $boden_neu . ', es laeuft weiter: '
                              . turing_inst_ausweg($aktiv, $im_hangar));
    }

    if ($ruhend && ! $bestaetigt_ruhend) {
        return array('lage' => 'bestaetigung', 'boden' => $boden_neu,
                     'grund' => 'es liegt ein AUSGESCHALTETER Ordner mit einem anderen Boden. Er '
                              . 'mischt den Knoten heute nicht — er kann es, sobald ihn jemand '
                              . 'einschaltet. Die Auswahl bringt ' . $boden_neu . ', liegen bleibt: '
                              . turing_inst_ausweg($ruhend, $im_hangar));
    }

    return array('lage' => 'gruen', 'grund' => '', 'boden' => $boden_neu);
}
}

/* --------------------------------------------------------------------------
   DER AUSWEG GEHOERT IN DIE MELDUNG, UND ER MUSS BEGEHBAR SEIN.

   Bis 3.4.0 nannte die Meldung zwei Auswege — "in die Auswahl aufnehmen
   oder entfernen" — und Turing oeffnete fuer einen Ordner, der nicht im
   Hangar liegt, keinen von beiden. Das ist eine Anweisung an einen
   Menschen, der ihr nicht folgen kann.

       Eine Meldung, die einen Ausweg nennt, den das Werkzeug nicht
       oeffnet, ist keine Anleitung. Sie ist eine Ausrede mit Imperativ.

   Also wird je Ordner gesagt, welcher der beiden Wege wirklich offensteht.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_ausweg')) {
function turing_inst_ausweg(array $ordner_mit_boden, array $im_hangar): string {
    $zeilen = array();
    foreach ($ordner_mit_boden as $o => $b) {
        $zeilen[] = in_array((string) $o, $im_hangar, true)
            ? $o . ' (Boden ' . $b . ') — liegt im Hangar: nimm ihn in die Auswahl auf'
            : $o . ' (Boden ' . $b . ') — liegt NICHT im Hangar: entferne ihn von Hand '
                 . 'ueber die Plugin-Liste, oder lege ein passendes Paket in den Hangar';
    }
    return implode(' · ', $zeilen);
}
}

/* --------------------------------------------------------------------------
   H230 — RICHTUNG. Turing kannte das Wort "veraltet" nicht.

   version_compare kam in der ganzen Datei genau einmal vor und sortierte
   Zips im Hangar gegeneinander. Zwischen Paket und Knoten wurde nie
   verglichen: tu_inst_zustand() prueft === auf die Zeichenkette. Damit
   heissen Nachtrag und Rueckschritt gleich.

       Ein Wort, das Nachtrag und Rueckschritt gleich schreibt, ist keine
       Messung. Eine Fassungsnummer ist eine Zusage; sie zurueckzunehmen
       darf nicht aussehen wie sie einzuloesen.

   Ein Rueckschritt ist nicht verboten — es gibt gute Gruende dafuer. Er
   braucht seine eigene, ausdrueckliche Bestaetigung, weil er das
   Gegenteil dessen tut, was der Knopf verspricht.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_richtung')) {
function turing_inst_richtung(array $paket, array $liegt): string {
    $o = (string) $paket['ordner'];
    if (! isset($liegt[$o]))                    { return 'neu'; }
    $alt = (string) ($liegt[$o]['fassung'] ?? '');
    $neu = (string) $paket['fassung'];
    if ($alt === '' || $neu === '')             { return 'unbekannt'; }
    $c = version_compare($neu, $alt);
    if ($c > 0) { return 'nachtrag'; }
    if ($c < 0) { return 'rueckschritt'; }
    return 'gleich';
}
}

/* --------------------------------------------------------------------------
   DAS MANIFEST (16b §3, entschieden in Vertrag 21).

   WAS ES TUT UND WAS AUSDRUECKLICH NICHT

       manifest.json FAENGT UEBERTRAGUNGSFEHLER beim Auspacken auf einem
       fremden Server. Es BELEGT KEINE HERKUNFT.

   Es liegt im selben Verzeichnis wie die Pakete. Wer die Pakete
   beschreiben kann, kann auch das Manifest beschreiben — ein Pruefwert
   daraus findet BESCHAEDIGUNG und findet FAELSCHUNG NICHT.

       Ein Pruefwert, der Beschaedigung findet und Faelschung nicht, muss
       das sagen. Sonst haelt ihn in vier Wochen jemand fuer einen
       Echtheitsnachweis — und das ist wieder eine Null mit zwei
       Bedeutungen.

   Dieser Satz steht deshalb auf der SEITE und nicht nur hier. Ein Zeuge
   prueft, dass er dort steht (T6).

   DREI AUSGAENGE, NICHT ZWEI (16a §1)

       BEKANNT      sha256 steht im Manifest und stimmt   -> ohne Rueckfrage
       ABWEICHEND   steht im Manifest und stimmt NICHT    -> ROT, nie
       UNBEKANNT    steht nicht im Manifest               -> zweite,
                                                             ausdrueckliche
                                                             Bestaetigung

   Der dritte Ausgang ist auf der Werkbank der Normalfall, und deshalb
   darf er nicht wie der erste aussehen. Und die Bestaetigung gilt fuer
   UNBEKANNT — sie winkt ABWEICHEND niemals durch. Ein Ja des Menschen
   auf eine Frage, die nicht gestellt wurde, ist keine Antwort.
   -------------------------------------------------------------------------- */

if (! function_exists('turing_inst_manifest_lesen')) {
function turing_inst_manifest_lesen(string $datei): array {
    if ($datei === '' || ! is_file($datei)) { return array(); }
    $d = json_decode((string) @file_get_contents($datei), true);
    if (! is_array($d)) { return array(); }

    /* Zwei Formen werden gelesen: {"pakete":[{datei,fassung,sha256}]} und
       die flache Abbildung {datei: sha256}. Wer eine dritte schickt, ist
       UNBEKANNT — nicht stillschweigend in Ordnung. */
    $aus = array();
    if (isset($d['pakete']) && is_array($d['pakete'])) {
        foreach ($d['pakete'] as $z) {
            if (! is_array($z) || empty($z['datei']) || empty($z['sha256'])) { continue; }
            $aus[(string) $z['datei']] = array(
                'sha256'  => strtolower((string) $z['sha256']),
                'fassung' => (string) ($z['fassung'] ?? ''),
            );
        }
        return $aus;
    }
    foreach ($d as $k => $v) {
        if (is_string($k) && is_string($v)) { $aus[$k] = array('sha256' => strtolower($v), 'fassung' => ''); }
    }
    return $aus;
}
}

if (! function_exists('turing_inst_pruefwert')) {
function turing_inst_pruefwert(array $paket, array $manifest): array {
    $datei = (string) $paket['datei'];

    if (! isset($manifest[$datei])) {
        return array('lage' => 'unbekannt',
                     'text' => 'steht nicht im Manifest — NICHT GEMESSEN');
    }

    $soll = (string) $manifest[$datei]['sha256'];
    $ist  = strtolower((string) $paket['sha256']);

    if ($soll === '' || strlen($soll) !== 64) {
        return array('lage' => 'unbekannt',
                     'text' => 'das Manifest nennt fuer diese Datei keinen brauchbaren sha256 — NICHT GEMESSEN');
    }
    if ($soll !== $ist) {
        return array('lage' => 'abweichend',
                     'text' => 'der Pruefwert stimmt nicht (Manifest ' . substr($soll, 0, 12)
                             . '…, gemessen ' . substr($ist, 0, 12) . '…) — die Datei ist unterwegs beschaedigt worden');
    }

    $f = (string) $manifest[$datei]['fassung'];
    if ($f !== '' && $f !== (string) $paket['fassung']) {
        return array('lage' => 'abweichend',
                     'text' => 'Pruefwert stimmt, aber das Manifest nennt Fassung ' . $f
                             . ' und der Kopf sagt ' . (string) $paket['fassung']
                             . ' — zwei Wahrheiten ueber dasselbe Paket');
    }

    return array('lage' => 'bekannt', 'text' => 'unveraendert aus dem Hangar');
}
}

/* --------------------------------------------------------------------------
   PHASE 1 — alles pruefen, solange ein Abbruch nichts kostet.
   Diese Funktion fasst plugins/ NICHT an. Sie bekommt den Zustand des
   Knotens als Angabe herein und gibt ihn nirgends aus.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_phase1')) {
function turing_inst_phase1(array $zips, array $lage, array $hand): array {
    $tmp             = (string) $lage['tmp'];
    $liegt_am_knoten = (array)  ($lage['familie_am_knoten'] ?? array());
    $im_hangar       = (array)  ($lage['im_hangar'] ?? array());
    $best_ruhend     = ! empty($lage['bestaetigt_ruhend']);
    $best_rueck      = ! empty($lage['bestaetigt_rueckschritt']);
    $manifest        = (array)  ($lage['manifest'] ?? array());
    $bestaetigt      = ! empty($lage['bestaetigt_unbekannte']);
    $pakete          = array();

    foreach ($zips as $zip) {
        $p = turing_inst_paket_lesen((string) $zip);

        if ($p['art'] === 'kaputt') {
            return array('ok' => false, 'phase' => 1, 'fehler_bei' => $p['datei'],
                         'grund' => 'Paket nicht lesbar: ' . $p['datei'] . ' — ' . $p['grund'],
                         'pakete' => array());
        }
        if ($p['art'] !== 'familie') {
            return array('ok' => false, 'phase' => 1, 'fehler_bei' => $p['datei'],
                         'grund' => $p['datei'] . ' traegt keinen Boden und gehoert in keinen Satz',
                         'pakete' => array());
        }
        if ($p['fassung'] === '') {
            return array('ok' => false, 'phase' => 1, 'fehler_bei' => $p['datei'],
                         'grund' => $p['datei'] . ' nennt im Kopf keine Fassung',
                         'pakete' => array());
        }

        /* DER PRUEFWERT, VOR DEM ENTPACKEN. Drei Ausgaenge, nicht zwei. */
        $pw       = turing_inst_pruefwert($p, $manifest);
        $p['pruefwert'] = $pw;

        if ($pw['lage'] === 'abweichend') {
            return array('ok' => false, 'phase' => 1, 'fehler_bei' => $p['datei'],
                         'grund' => $p['datei'] . ': ' . $pw['text']
                                  . ' — nicht installiert, das Paket bleibt liegen',
                         'pakete' => array());
        }
        /* H230: ein Rueckschritt ist erlaubt und will gefragt werden. */
        $richtung = turing_inst_richtung($p, $liegt_am_knoten);
        $p['richtung'] = $richtung;
        if ($richtung === 'rueckschritt' && ! $best_rueck) {
            return array('ok' => false, 'phase' => 1, 'fehler_bei' => $p['datei'],
                         'lage' => 'bestaetigung',
                         'grund' => $p['datei'] . ' ist ein RUECKSCHRITT: auf dem Knoten liegt '
                                  . (string) ($liegt_am_knoten[$p['ordner']]['fassung'] ?? '?')
                                  . ', das Paket bringt ' . $p['fassung']
                                  . '. Das ist nicht verboten, aber es ist das Gegenteil dessen, '
                                  . 'was der Knopf verspricht — dafuer braucht es das eigene Kreuz.',
                         'pakete' => array());
        }

        if ($pw['lage'] === 'unbekannt' && ! $bestaetigt) {
            return array('ok' => false, 'phase' => 1, 'fehler_bei' => $p['datei'],
                         'grund' => $p['datei'] . ': ' . $pw['text']
                                  . ' — dafuer braucht es die ausdrueckliche zweite Bestaetigung',
                         'pakete' => array());
        }

        $ziel = rtrim($tmp, '/\\') . '/' . $p['ordner'];
        $r    = call_user_func($hand['entpacken'], $p['pfad'], rtrim($tmp, '/\\'));
        if ($r !== true) {
            return array('ok' => false, 'phase' => 1, 'fehler_bei' => $p['datei'],
                         'grund' => 'Entpacken schlug fehl bei ' . $p['datei'] . ': ' . (string) $r,
                         'pakete' => array());
        }
        if (! is_dir($ziel)) {
            return array('ok' => false, 'phase' => 1, 'fehler_bei' => $p['datei'],
                         'grund' => 'nach dem Entpacken fehlt der Ordner ' . $p['ordner'],
                         'pakete' => array());
        }

        $p['quelle'] = $ziel;
        $pakete[]    = $p;
    }

    $erg = turing_inst_ergebnis_pruefen($pakete, $liegt_am_knoten, $im_hangar, $best_ruhend);
    if ($erg['lage'] !== 'gruen') {
        return array('ok' => false, 'phase' => 1, 'fehler_bei' => '',
                     'lage' => $erg['lage'], 'grund' => $erg['grund'], 'pakete' => array());
    }

    return array('ok' => true, 'phase' => 1, 'fehler_bei' => '', 'grund' => '', 'pakete' => $pakete);
}
}

/* --------------------------------------------------------------------------
   PHASE 2 — ab hier wird der Knoten angefasst.

   Reihenfolge, und jede Zeile hat einen Grund:

     1  Marke schreiben. Geht das nicht, wird NICHTS gelegt — sonst gaebe
        es einen Zwischenstand, den niemand melden kann.
     2  Je Paket: was liegt, wandert beiseite (nicht in den Muell).
        Dann legen.
     3  Fehlschlag -> Gelegtes weg, Beiseitegelegtes ZURUECK, nichts
        aktiviert.
     4  Erst wenn ALLE liegen, wird aktiviert.
     5  Marke weg, Beiseitegelegtes weg.

   Benannte Grenze: Schritt 4 ist nicht transaktionssicher. Ein Absturz
   dazwischen hinterlaesst einen Zwischenstand — und genau dafuer ist die
   Marke da, die ihn beim naechsten Aufruf meldet.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_phase2')) {
function turing_inst_phase2(array $vorbereitet, array $hand): array {
    $marke = array(
        'zeit'   => time(),
        'pakete' => array_column($vorbereitet, 'ordner'),
    );
    if (call_user_func($hand['marke_schreiben'], $marke) !== true) {
        return array('ok' => false, 'phase' => 2, 'gelegt' => array(),
                     'grund' => 'die Marke liess sich nicht schreiben — es wurde nichts gelegt');
    }

    $gelegt   = array();
    $beiseite = array();

    foreach ($vorbereitet as $p) {
        $o = $p['ordner'];

        if (call_user_func($hand['liegt_da'], $o)) {
            if (call_user_func($hand['beiseite'], $o) !== true) {
                return turing_inst_zuruecknehmen($hand, $gelegt, $beiseite,
                    'die vorhandene Fassung von ' . $o . ' liess sich nicht sichern');
            }
            $beiseite[] = $o;
        }

        $r = call_user_func($hand['legen'], $p['quelle'], $o);
        if ($r !== true) {
            return turing_inst_zuruecknehmen($hand, $gelegt, $beiseite,
                'Legen schlug fehl bei ' . $o . ': ' . (string) $r);
        }
        $gelegt[] = $o;
    }

    /* ----------------------------------------------------------------
       H236 — DIE LISTE, GEGEN DIE AKTIVIERT WIRD, IST VON VORHIN.

       Auf Christians DEV gemessen, nicht vermutet: der Wirt haelt seine
       Plugin-Liste im Request fest. Wer sie vor dem Legen liest, aktiviert
       danach gegen ein Bild, in dem das neue Paket noch nicht vorkommt —
       und bekommt "Das Plugin hat keinen gueltigen Header" fuer eine
       Datei, die vollstaendig und mit gueltigem Kopf dort liegt.

           Wer die Liste liest, bevor er den Ordner legt, aktiviert gegen
           ein Bild von vorhin.

       Es traf genau EIN Paket, weil genau eines neu war. Auf einem leeren
       Knoten — dem Fall des fremden Admins — traefe es alle.

       Die Regel sagt WANN verworfen wird; WIE, weiss nur die Hand.
       ---------------------------------------------------------------- */
    call_user_func($hand['liste_verwerfen']);

    /* Erst jetzt. Und "alle oder keiner" gilt fuer das LEGEN — fuers
       Aktivieren gilt etwas anderes, siehe unten. */
    $aktiv = array();
    $rot   = array();
    foreach ($vorbereitet as $p) {
        $r = call_user_func($hand['aktivieren'], $p['ordner'], $p['hauptdatei']);
        if ($r === true) { $aktiv[] = $p['ordner']; }
        else             { $rot[]   = $p['ordner'] . ': ' . (string) $r; }
    }

    /* ----------------------------------------------------------------
       H235 — WER ROT ZURUECKKOMMT, DARF NICHT VORHER AUFGERAEUMT HABEN.

       Bis 3.4.0 liefen alt_raeumen() und marke_loeschen() VOR der
       Fehlermeldung. Damit waren die alten Fassungen und die Marke, die
       einen Zwischenstand melden soll, genau dann weg, wenn es einen gab.

           Ein Lauf, der rot zurueckkommt, hat vorher seine eigene
           Ruecknahme weggeraeumt.

       ENTSCHIEDEN (Vertrag 22 §1): NICHT zuruecknehmen, aber die
       Ruecknahme offenlassen. Die Dateien liegen richtig; ein klemmender
       Schalter ist kein Grund, eine geglueckte Installation wegzuwerfen.
       Was bleibt, bleibt fuer den Menschen, nicht fuer die Maschine.
       ---------------------------------------------------------------- */
    if ($rot) {
        call_user_func($hand['marke_schreiben'], array(
            'zeit'   => time(),
            'pakete' => array_column($vorbereitet, 'ordner'),
            'lage'   => 'gelegt, nicht alle aktiviert',
            'rot'    => $rot,
        ));
        return array('ok' => false, 'phase' => 2, 'gelegt' => $gelegt, 'aktiviert' => $aktiv,
                     'beiseite' => $beiseite,
                     'grund' => 'gelegt sind alle, aktiviert nicht alle: ' . implode(' · ', $rot)
                              . '. Die Sicherungen der alten Fassungen und die Marke BLEIBEN liegen, '
                              . 'solange etwas rot ist — die Ruecknahme steht damit offen.');
    }

    call_user_func($hand['alt_raeumen'], $beiseite);
    call_user_func($hand['marke_loeschen']);
    return array('ok' => true, 'phase' => 2, 'gelegt' => $gelegt, 'aktiviert' => $aktiv, 'grund' => '');
}
}

/* v3.2.1 — ZWEI LOECHER, DIE T7 GEFUNDEN HAT. Beide in dieser Funktion.

   (1) Das Paket, an dem es SCHEITERT, stand in keiner der beiden Listen.
       Es war beiseitegelegt, sein Zielordner war schon angelegt und halb
       gefuellt — und der Rueckweg scheiterte still daran, dass dort etwas
       lag. Ergebnis: genau das Produkt, an dem der Fehler auftrat, war
       danach WEG, und zwar in seiner neuen halben Fassung.

           Wer zuruecknimmt, muss auch den Platz raeumen, auf den er
           zuruecklegt. Sonst nimmt er die zwei zurueck, die gelungen
           sind, und verliert die eine, die es nicht war.

   (2) Der Sicherungsordner blieb leer stehen. Ein Rueckstand in
       plugins/, bei einem Werkzeug, das im eigenen Kopf zusagt, sich
       rueckstandslos entfernen zu lassen.

   Beides war im Bauplan nicht sichtbar und im ersten Lauf sofort rot. */
if (! function_exists('turing_inst_zuruecknehmen')) {
function turing_inst_zuruecknehmen(array $hand, array $gelegt, array $beiseite, string $grund): array {
    foreach ($gelegt as $o)   { call_user_func($hand['entfernen'], $o); }
    foreach ($beiseite as $o) {
        call_user_func($hand['entfernen'], $o);   /* auch das halb Gelegte */
        call_user_func($hand['zurueck'], $o);
    }
    call_user_func($hand['alt_raeumen'], array());
    call_user_func($hand['marke_loeschen']);
    return array('ok' => false, 'phase' => 2, 'gelegt' => array(),
                 'grund' => $grund . ' — alles zurueckgenommen, nichts aktiviert');
}
}

/* --------------------------------------------------------------------------
   DIE HAERTUNG (16a §3.1). Genau EINE Stelle fragt.

       Zwei Stellen, die dieselbe Haertung verschieden lesen, sind zwei
       Wahrheiten ueber einen Knoten.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_darf_schreiben')) {
function turing_inst_darf_schreiben(array $lage): array {
    /* 3.14.8 (Runde 37): die erste Pruefung ist das Recht zu tun. Ein Mitglied ohne
       'manage_options' sieht Hangar und Kacheln, der Knopf bleibt aus, und hier steht warum.
       Die eine Tuer (tu_inst_installieren) fragt dieselbe Funktion – fail closed. */
    if (! turing_darf_tun()) {
        return array('darf' => false,
                     'grund' => 'Aufspielen ist Sache des SysOps (manage_options). Dieses Konto sieht den Installer lesend.');
    }
    if (! empty($lage['disallow_file_mods'])) {
        return array('darf' => false,
                     'grund' => 'DISALLOW_FILE_MODS ist auf diesem Knoten gesetzt. Turing '
                              . 'aendert keine Dateien und umgeht die Haertung des Betreibers nicht.');
    }
    if (empty($lage['darf_installieren'])) {
        return array('darf' => false,
                     'grund' => 'Dieses Benutzerkonto darf keine Plugins installieren '
                              . '(Faehigkeit install_plugins fehlt).');
    }
    if (empty($lage['hangar'])) {
        /* v3.9.11 — DER GRUND STEHT JETZT IM GRUND.
           Bis hierher sagte diese Stelle „Es ist kein Hangar eingestellt" —
           auch dann, wenn einer eingestellt war und Turing ihn ABGEWIESEN
           hat, weil er ausserhalb von wp-content liegt.
               Ein Grund, der die Ursache nicht nennt, ist eine Ausrede
               mit Zeilennummer. */
        return array('darf' => false, 'grund' => turing_hangar_satz(turing_hangar_lage()));
    }
    return array('darf' => true, 'grund' => '');
}
}

/* --------------------------------------------------------------------------
   DER EINE EINGANG.

   Solange installieren() die einzige Tuer ist, kann kein zweiter Weg an
   darf_schreiben() vorbei. Zwei Wege in denselben Plugin-Ordner sind zwei
   Wahrheiten ueber dieselbe Installation.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_installieren')) {
function turing_inst_installieren(array $zips, array $lage, array $hand): array {
    $darf = turing_inst_darf_schreiben($lage);
    if (! $darf['darf']) {
        return array('ok' => false, 'phase' => 0, 'grund' => $darf['grund'], 'gelegt' => array());
    }

    $tmp = (string) $lage['tmp'];

    $p1 = turing_inst_phase1($zips, $lage, $hand);
    if (! $p1['ok']) {
        /* H231a — der abgewiesene Lauf laesst sein Zwischenlager nicht liegen.
           Bis 3.4.0 waren alle Pakete bereits ausgepackt, wenn abgelehnt
           wurde, und der Baum blieb stehen: unter zufaelligem Namen, also
           bei jedem Versuch einer mehr.

               Ein Werkzeug, dessen einzige echte Eigenschaft die
               gepruefte Null an Schreibvorgaengen ist, darf nach einem
               abgelehnten Lauf nichts zuruecklassen — schon gar nichts,
               dessen Ort es nicht nennt. */
        call_user_func($hand['tmp_raeumen'], $tmp);
        return $p1 + array('gelegt' => array(), 'zwischenlager' => $tmp);
    }

    $p2 = turing_inst_phase2($p1['pakete'], $hand);
    call_user_func($hand['tmp_raeumen'], $tmp);          /* auch nach Erfolg */
    return $p2 + array('zwischenlager' => $tmp);
}
}

/* --------------------------------------------------------------------------
   DIE ELTERN-SCHALE (Vertrag 16 §3, 16b §2, 16c §4).

   Gefragt wird die TATSACHE, nicht der Name — damit die Regel jede
   Umbenennung und jede Ladereihenfolge ueberlebt.

       Wer zuletzt kommt und vorher nachsieht, kann nichts verdraengen.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_inst_schale_noetig')) {
function turing_inst_schale_noetig(array $hooks): bool {
    return ! isset($hooks['cluster_one']);
}
}

/* ==========================================================================
   DIE HAND. Das Einzige in diesem Abschnitt, das WordPress anfasst.

   Absichtlich duenn: jede Funktion ist ein Aufruf und eine Rueckgabe.
   Was hier nicht steht, kann hier nicht falsch sein — und was hier steht,
   ist auf keinem Pruefstand messbar. Beides gehoert zusammen.
   ========================================================================== */

/** Wo der Hangar liegt. KEINE Option — eine Konstante.

   08.09.2026: das ist ab jetzt 16a §3.3 selbst, nicht mehr eine Abweichung
   davon. Mammut hat die Zeile ersetzt — "der Pfad kommt aus einer
   KONSTANTE in wp-config.php, nie aus dem Request und nicht aus einer
   Option".

   Bis dahin stand hier ein Absatz, der begruendete, warum Turing STRENGER
   baut als sein Vertrag (Befund N, 04.09.). Diese Begruendung ist
   aufgegangen und wird deshalb nicht aufbewahrt:

       Ein Kommentar, der gegen eine Regel argumentiert, die es nicht mehr
       gibt, liest sich beim naechsten Mal wie eine Abweichung — und die
       naechste Hand "korrigiert" sie zurueck.

   Der Sinn bleibt, was er war: ein Pfad, aus dem Code nach plugins/
   wandert, darf nicht aus einem Formularfeld kommen — und auch nicht aus
   der Datenbank, wo ein SQL ihn umbiegen koennte. Turing behaelt seine
   geprueften null Optionen.

   Wer ihn verschieben will, setzt TURING_HANGAR in wp-config.php: dieselbe
   Stelle wie DISALLOW_FILE_MODS. Wer dort sitzt, braucht Turing nicht. */
if (! function_exists('turing_hangar_lage')) {
/** v3.9.11 (17.09.2026) — DREI URSACHEN HATTEN EINE STILLE, UND SIE IST MEINE.
 *
 * Bis 3.9.10 gab tu_hangar_pfad() bei DREI verschiedenen Lagen denselben
 * leeren String zurueck:
 *
 *     1  das Verzeichnis gibt es nicht              realpath() === false
 *     2  es liegt AUSSERHALB von wp-content         Sicherheitsverweigerung
 *     3  WP_CONTENT_DIR selbst ist nicht aufloesbar
 *
 * Und ein VIERTER Zustand sah am Schirm genauso aus: der Ordner ist da und
 * leer. Turing sagte in allen vier Faellen „nichts da".
 *
 *     Ein Werkzeug, dessen einzige Aufgabe das Zeigen ist, darf nicht
 *     mehrere Gruende in eine Stille legen.
 *
 * DER ZWEITE FALL WAR NICHT NUR STILL, SONDERN IRREFUEHREND: die
 * Oberflaeche riet „Lege wp-content/cluster-one-hangar an" — und wer das
 * tat, bekam denselben leeren Schirm, weil TURING_HANGAR weiter woandershin
 * zeigte.
 *
 *     Eine Anweisung, die den gemeldeten Zustand nicht aendern kann, ist
 *     schlimmer als keine. Sie kostet den Leser den Versuch UND das
 *     Vertrauen in die Meldung.
 *
 * EINE WAHRHEIT, ZWEI LESER (P1-12): diese Funktion ist die Quelle,
 * tu_hangar_pfad() liest aus ihr. Der Pfad wird an EINER Stelle bestimmt.
 *
 * UND EINE ZWEITE AENDERUNG, die ich getrennt nenne, weil sie eine
 * SICHERHEITSGRENZE verschiebt — enger, nie weiter:
 *
 *     ALT   strpos($r, $inhalt) !== 0
 *     NEU   $r !== $inhalt && strpos($r, $inhalt . DIRECTORY_SEPARATOR) !== 0
 *
 * Die alte Pruefung ist ein reiner Praefixvergleich. Heisst das
 * Inhaltsverzeichnis /var/www/wp-content, so kam auch
 * /var/www/wp-content-alt durch — ein GESCHWISTER, kein Kind.
 * Die neue verlangt den Trenner dahinter oder Gleichheit. Sie kann nur
 * MEHR abweisen, nie weniger, und was sie zusaetzlich abweist, lag nie
 * unter wp-content.
 */
function turing_hangar_lage(): array {
    $c1_p = defined('TURING_HANGAR') ? (string) TURING_HANGAR : WP_CONTENT_DIR . '/cluster-one-hangar';
    $c1_gesetzt = defined('TURING_HANGAR');

    $c1_r = realpath($c1_p);
    if ($c1_r === false) {
        return array('pfad' => '', 'lage' => 'fehlt', 'gemeint' => $c1_p, 'gesetzt' => $c1_gesetzt);
    }
    $c1_inhalt = realpath(WP_CONTENT_DIR);
    if ($c1_inhalt === false) {
        return array('pfad' => '', 'lage' => 'inhalt-unauffindbar', 'gemeint' => $c1_r, 'gesetzt' => $c1_gesetzt);
    }
    if ($c1_r !== $c1_inhalt && strpos($c1_r, $c1_inhalt . DIRECTORY_SEPARATOR) !== 0) {
        return array('pfad' => '', 'lage' => 'ausserhalb', 'gemeint' => $c1_r, 'gesetzt' => $c1_gesetzt);
    }
    return array('pfad' => $c1_r, 'lage' => 'da', 'gemeint' => $c1_r, 'gesetzt' => $c1_gesetzt);
}
}

if (! function_exists('turing_hangar_pfad')) {
function turing_hangar_pfad(): string {
    $c1_l = turing_hangar_lage();
    return (string) $c1_l['pfad'];
}
}

if (! function_exists('turing_hangar_satz')) {
/** v3.9.11 — DER SATZ ZUR LAGE. Eine Quelle, damit die drei Stellen, die
 *  ihn zeigen, nicht drei verschiedene Saetze erfinden.
 *      Drei Leser, die sich ihren Text selbst ausdenken, sind drei
 *      Wahrheiten ueber einen Zustand. */
function turing_hangar_satz(array $c1_l): string {
    $c1_g = (string) ($c1_l['gemeint'] ?? '');
    switch ((string) ($c1_l['lage'] ?? '')) {
        case 'da':
            return '';
        case 'ausserhalb':
            return 'Der Hangar ist gesetzt, liegt aber AUSSERHALB von wp-content/ — '
                 . 'Turing weist ihn ab. Gemeint ist: ' . $c1_g . '. '
                 . 'Das ist keine fehlende Einstellung, sondern eine Verweigerung: '
                 . 'ein Pfad, aus dem Code nach plugins/ wandert, muss unter wp-content/ liegen. '
                 . 'Einen neuen Ordner anzulegen hilft hier NICHT — TURING_HANGAR in '
                 . 'wp-config.php muss geaendert werden.';
        case 'inhalt-unauffindbar':
            return 'WP_CONTENT_DIR selbst ist nicht aufloesbar. Das ist kein Hangar-Problem; '
                 . 'Turing kann hier gar nichts pruefen.';
        case 'fehlt':
        default:
            return !empty($c1_l['gesetzt'])
                ? 'TURING_HANGAR ist in wp-config.php gesetzt, aber das Verzeichnis gibt es nicht: '
                  . $c1_g . '. Anlegen — oder die Konstante berichtigen.'
                : 'Es gibt kein Hangar-Verzeichnis. Lege ' . $c1_g . ' an und lege die '
                  . '.zip-Pakete hinein. Ein Ordner ist keine Lieferung — nur Archive.';
    }
}
}

if (! function_exists('turing_hangar_schutz')) {
/** v3.9.12 (23.09.2026) — WAS AM HANGAR OFFEN STEHT, UND WO ES NICHT ZUGEHT.
 *
 * DER ANLASS steht seit dem 18.09. als Posten 4 auf Mammuts Liste: „Der
 * Hangar ohne index.php / .htaccess." Er ist mein Byte.
 *
 * DIE LAGE, und sie ist eine Klemme, die Turing selbst erzeugt:
 *
 *     tu_hangar_lage() WEIST jeden Hangar AB, der nicht unter wp-content/
 *     liegt. Der Grund ist gut: Code, der nach plugins/ wandert, soll aus
 *     einem Verzeichnis kommen, das WordPress selbst verwaltet.
 *
 *     UND „unter wp-content/" HEISST „im Webverzeichnis". Ein .zip, das
 *     dort liegt, ist ueber HTTP erreichbar, sobald jemand seinen Namen
 *     kennt — und die Namen sind keine Geheimnisse, sie stehen in jedem
 *     Bericht.
 *
 *         Eine Sicherheitsregel, die etwas ins Web legt, damit es
 *         geprueft werden kann, hat das Loch nicht beseitigt, sondern
 *         verschoben.
 *
 * WAS DIE ZWEI DATEIEN WIRKLICH KOENNEN — und das ist der ganze Grund,
 * warum diese Funktion misst statt zu beruhigen:
 *
 *     index.php    verhindert das AUFLISTEN des Verzeichnisses, ueberall
 *                  dort, wo der Server sonst auflisten wuerde. Es
 *                  verhindert NICHT den Abruf einer Datei, deren Namen
 *                  man kennt. Es ist WordPress' eigene Gepflogenheit
 *                  (wp-content/plugins, uploads) und kostet nichts.
 *
 *     .htaccess    verhindert den ABRUF — aber NUR auf Apache und nur,
 *                  wenn AllowOverride es zulaesst. Auf nginx, LiteSpeed
 *                  ohne Apache-Kompatibilitaet oder IIS tut die Datei
 *                  NICHTS. Sie liegt dort herum und sieht aus wie Schutz.
 *
 *         Eine Datei, die auf diesem Server nichts tut, ist keine
 *         halbe Sicherung. Sie ist eine Zusage an den, der sie sieht.
 *
 * DESHALB NENNT DIESE FUNKTION DEN SERVER MIT. Sie RAET ihn nicht:
 * $_SERVER['SERVER_SOFTWARE'] ist das, was der Server ueber sich selbst
 * sagt, und wenn er nichts sagt, heisst das Urteil 'nicht-messbar' und
 * nicht 'sicher'.
 *
 *     ROT geht vor NICHT GEMESSEN. Aber NICHT GEMESSEN geht vor gruen.
 *
 * SIE SCHREIBT NICHTS. Sie sieht nach. Was zu tun ist, entscheidet der
 * Mensch, und er bekommt den Text dazu von tu_hangar_schutz_satz().
 *
 * @return array{gilt:bool,index:bool,htaccess:bool,zips:int,
 *               server:string,apache:?bool,urteil:string}
 */
function turing_hangar_schutz(array $c1_l): array {
    $c1_pfad = (string) ($c1_l['pfad'] ?? '');
    $c1_aus  = array(
        'gilt' => false, 'index' => false, 'htaccess' => false, 'zips' => 0,
        'server' => '', 'apache' => null, 'urteil' => 'kein-hangar',
    );
    if ($c1_pfad === '' || ! is_dir($c1_pfad)) {
        return $c1_aus;
    }

    $c1_aus['gilt']     = true;
    $c1_aus['index']    = is_file($c1_pfad . '/index.php');
    $c1_aus['htaccess'] = is_file($c1_pfad . '/.htaccess');
    $c1_aus['zips']     = count(turing_hangar_zips($c1_pfad));   // 18.10.2026 (Neo): auch .ZIP

    /* WAS DER SERVER UEBER SICH SELBST SAGT. Nicht geraten, nicht aus dem
       Betriebssystem geschlossen — und wenn er schweigt, wird geschwiegen. */
    /* 4.0.4 (Sigma): bis 4.0.3 stand hier (string) $_SERVER['SERVER_SOFTWARE'] roh. Die Kennung wird nur mit stripos
       verglichen und im Bericht genannt; sanitize_text_field(wp_unslash()) aendert daran nichts und nimmt dem Pruefer
       seine zwei letzten Meldungen (MissingUnslash, InputNotSanitized). */
    $c1_sw = isset($_SERVER['SERVER_SOFTWARE']) ? sanitize_text_field(wp_unslash((string) $_SERVER['SERVER_SOFTWARE'])) : '';
    $c1_aus['server'] = $c1_sw;
    if ($c1_sw !== '') {
        $c1_aus['apache'] = (stripos($c1_sw, 'apache') !== false
                          || stripos($c1_sw, 'litespeed') !== false);
    }

    if (!$c1_aus['index']) {
        $c1_aus['urteil'] = 'listbar';
        return $c1_aus;
    }
    if ($c1_aus['apache'] === null) {
        $c1_aus['urteil'] = $c1_aus['htaccess'] ? 'nicht-messbar' : 'abrufbar';
        return $c1_aus;
    }
    if ($c1_aus['apache'] === false) {
        /* Auf einem Server ohne .htaccess-Auswertung ist die Datei kein
           Schutz — ob sie daliegt oder nicht, aendert am Abruf nichts. */
        $c1_aus['urteil'] = 'abrufbar-serverregel-noetig';
        return $c1_aus;
    }
    $c1_aus['urteil'] = $c1_aus['htaccess'] ? 'geschuetzt' : 'abrufbar';
    return $c1_aus;
}
}

if (! function_exists('turing_hangar_schutz_satz')) {
/** v3.9.12 — DER SATZ ZUR SCHUTZLAGE. Eine Quelle, wie tu_hangar_satz().
 *      Drei Leser, die sich ihren Text selbst ausdenken, sind drei
 *      Wahrheiten ueber einen Zustand. */
function turing_hangar_schutz_satz(array $c1_s): string {
    $c1_z = (int) ($c1_s['zips'] ?? 0);
    $c1_w = $c1_z === 1 ? 'ein Paket' : $c1_z . ' Pakete';
    switch ((string) ($c1_s['urteil'] ?? '')) {
        case 'kein-hangar':
            return '';
        case 'geschuetzt':
            return '';
        case 'listbar':
            return 'Der Hangar hat keine index.php. Wer die Adresse des Ordners kennt, '
                 . 'bekommt auf vielen Servern die LISTE dessen, was darin liegt — hier ' . $c1_w . '. '
                 . 'Eine leere index.php stellt das ab; es ist WordPress\' eigene Gepflogenheit.';
        case 'abrufbar':
            return 'Der Hangar hat keine .htaccess. Die Liste ist zwar zu, aber wer einen '
                 . 'Dateinamen KENNT, kann das Paket herunterladen — und die Namen stehen in jedem '
                 . 'Bericht. Auf Apache stellt eine .htaccess mit „Require all denied" das ab.';
        case 'abrufbar-serverregel-noetig':
            return 'Dieser Server ist kein Apache (' . (string) ($c1_s['server'] ?? '?') . '). '
                 . 'Eine .htaccess wuerde hier NICHTS tun — sie laege herum und saehe aus wie Schutz. '
                 . 'Wer einen Dateinamen kennt, kann das Paket herunterladen. Das laesst sich nur in '
                 . 'der Serverkonfiguration schliessen (bei nginx: eine location-Regel, die den '
                 . 'Hangar-Pfad verweigert). Turing kann das nicht und behauptet es nicht.';
        case 'nicht-messbar':
        default:
            return 'Ob die .htaccess hier greift, ist NICHT GEMESSEN: dieser Server sagt nicht, '
                 . 'was er ist. Auf Apache greift sie, auf nginx nicht. Sieh einmal selbst nach, '
                 . 'ob der Hangar-Ordner ueber den Browser erreichbar ist — das ist eine Minute '
                 . 'und beantwortet die Frage endgueltig.';
    }
}
}

if (! function_exists('turing_hangar_schutz_rezept')) {
/** v3.9.12 — WAS HINEINGEHOERT, WOERTLICH. UND WARUM TURING ES NICHT SELBST
 *  HINSCHREIBT.
 *
 * Es waere eine Stunde Arbeit, hier einen Knopf zu bauen: Fahigkeit, Nonce,
 * kein GET, DISALLOW_FILE_MODS geachtet — Turing kann das alles schon. Er
 * bekommt ihn trotzdem nicht, und der Grund ist keine Bequemlichkeit:
 *
 *     Der Hangar ist das Verzeichnis, das der SysOp angelegt hat, um Turing
 *     etwas hineinzulegen. Er ist die eine Stelle, an der der Mensch die
 *     Hand fuehrt. Ein Werkzeug, das dort unaufgefordert Dateien ablegt,
 *     hat sich genau die Grenze genommen, die es sonst verteidigt.
 *
 *         Wer etwas findet, MELDET es. Angefasst wird es von dem, dem es
 *         gehoert.
 *
 * UND ES IST ZEHN SEKUNDEN ARBEIT. Zwei Dateien, hier vollstaendig, zum
 * Hineinkopieren. Wer sie anlegt, hat sie gesehen — das ist bei einer
 * Sicherungsdatei mehr wert als ein Knopf, den man drueckt und vergisst.
 *
 * @return array<string,string>  dateiname => inhalt
 */
function turing_hangar_schutz_rezept(): array {
    return array(
        'index.php' => "<?php\n// Silence is golden.\n",
        '.htaccess' =>
              "# Cluster One — Turing-Hangar. Kein Abruf ueber HTTP.\n"
            . "# Wirkt auf Apache (und LiteSpeed im Apache-Modus). Auf nginx\n"
            . "# tut diese Datei NICHTS — dort braucht es eine location-Regel.\n"
            . "<IfModule mod_authz_core.c>\n"
            . "    Require all denied\n"
            . "</IfModule>\n"
            . "<IfModule !mod_authz_core.c>\n"
            . "    Order allow,deny\n"
            . "    Deny from all\n"
            . "</IfModule>\n"
            . "Options -Indexes\n",
    );
}
}

/* =====================================================================
 * v3.9.13 — DIE NARBE HAT EINEN ORT. Posten 10.
 *
 * Bis ZUG 10 hatten die vier Lights keine uninstall.php: Entfernen hat
 * lautlos nichts geraeumt. Seit dem 23.09.2026 schreibt jede von ihnen
 * beim Entfernen GENAU EINE Option, cluster_one_entfernt, und laesst sie
 * absichtlich liegen — wer einen leeren Knoten vorfindet, soll sehen
 * koennen, dass geraeumt wurde.
 *
 * WARUM TURING SIE ZEIGEN DARF, OBWOHL ES DEN ENTFERNEN-KNOPF NICHT HAT:
 *
 *     „Der Knopf kommt, wenn er die Folge nennen kann statt sie zu
 *      zitieren."                       (Turing, ueber sich selbst)
 *
 * Die Zeile Cluster-One-Entfernt ist eine ZUSAGE des Pakets, und Turing
 * kann sie nicht nachmessen — uninstall.php darf es weder lesen noch
 * ausfuehren. Die Narbe ist keine Zusage: sie ist das Protokoll, von der
 * uninstall.php selbst geschrieben, NACHDEM sie gelaufen ist. Turing
 * liest dafuer eine Option und sonst nichts.
 *
 *     Eine Anzeige ist kein Knopf. Was hier entsteht, ist genau das,
 *     was fehlte, damit je einer entstehen koennte.
 *
 * UND SIE IST EIN FUND, KEINE ZUSAGE UEBER IHRE FORM. Ein fremdes Plugin
 * hat sie geschrieben, in dem Moment, in dem WordPress es geloescht hat.
 * Sie kann fehlen, kein Feld sein, Eintraege enthalten, die keine sind,
 * oder eine Lage tragen, die es noch nicht gab. Jede Zeile wird einzeln
 * geprueft, und eine unbrauchbare wird GENANNT statt uebersprungen.
 *
 *     Wer eine Spur liest, muss wissen, dass eine davon unlesbar war.
 * =================================================================== */
if (! function_exists('turing_narbe_lesen')) {
function turing_narbe_lesen(): array {
    $c1_aus = array('gibt' => false, 'zeilen' => array(), 'unlesbar' => 0, 'gestalt' => 'fehlt');

    $c1_roh = get_option('cluster_one_entfernt', null);
    if ($c1_roh === null || $c1_roh === false) {
        return $c1_aus;
    }
    $c1_aus['gibt'] = true;
    if (! is_array($c1_roh)) {
        /* Sie ist da und sie ist keine Liste. Das ist eine Aussage und
           kein Grund zu schweigen. */
        $c1_aus['gestalt'] = 'kein-feld';
        return $c1_aus;
    }
    $c1_aus['gestalt'] = 'feld';

    $c1_n = 0;
    foreach ($c1_roh as $c1_e) {
        if (! is_array($c1_e)) { $c1_aus['unlesbar']++; continue; }

        $c1_wann = isset($c1_e['wann']) && is_numeric($c1_e['wann']) ? (int) $c1_e['wann'] : 0;
        $c1_aus['zeilen'][] = array(
            'wirt'   => isset($c1_e['wirt']) && is_string($c1_e['wirt']) ? $c1_e['wirt'] : '',
            'wann'   => $c1_wann,
            'lage'   => isset($c1_e['lage']) && is_string($c1_e['lage']) ? $c1_e['lage'] : '',
            'zahlen' => isset($c1_e['zahlen']) && is_array($c1_e['zahlen']) ? $c1_e['zahlen'] : array(),
            'n'      => $c1_n++,
        );
    }

    /* NEUESTE ZUERST. Wer keine Zeitangabe traegt, kann nicht in die Zeit
       gestellt werden und steht deshalb hinten — nicht vorne, wo er wie
       das Juengste aussaehe. Bei gleicher Zeit bleibt die Reihenfolge, in
       der sie geschrieben wurden: sie ist selbst eine Auskunft. */
    usort($c1_aus['zeilen'], function ($x, $y) {
        if ($x['wann'] === 0 xor $y['wann'] === 0) { return $x['wann'] === 0 ? 1 : -1; }
        if ($x['wann'] !== $y['wann']) { return $y['wann'] <=> $x['wann']; }
        return $y['n'] <=> $x['n'];
    });
    return $c1_aus;
}
}

/** Was eine Lage heisst. Eine unbekannte wird GENANNT, nicht geglaettet. */
if (! function_exists('turing_narbe_lage_satz')) {
function turing_narbe_lage_satz(string $c1_lage): array {
    switch ($c1_lage) {
        case 'geraeumt':
            return array('wort' => 'geraeumt', 'farbe' => '#1a7f37',
                'satz' => 'Der Raeumer lief und hat etwas genommen.');
        case 'nichts-genommen':
            return array('wort' => 'nichts genommen', 'farbe' => '#666',
                'satz' => 'Der Raeumer lief und fand nichts. Das ist kein Fehler — '
                        . 'es lag nichts von diesem Paket da.');
        case 'kein-raeumer':
            return array('wort' => 'kein Raeumer', 'farbe' => '#996800',
                'satz' => 'Es war kein Raeumer angemeldet. Es wurde NICHTS genommen — '
                        . 'was dem Paket gehoerte, liegt noch da.');
        case 'boden-fehlt':
            return array('wort' => 'Boden fehlte', 'farbe' => '#996800',
                'satz' => 'Der Boden war beim Loeschen nicht mehr da. Es wurde NICHTS '
                        . 'genommen — was dem Paket gehoerte, liegt noch da.');
        case 'begonnen':
            return array('wort' => 'ABGEBROCHEN', 'farbe' => '#b32d2e',
                'satz' => 'Der Lauf hat begonnen und sich nie wieder gemeldet. Was zwischen '
                        . 'Anfang und Abbruch geschah, steht nirgends: es kann nichts, '
                        . 'etwas oder alles genommen worden sein.');
        case '':
            return array('wort' => 'OHNE LAGE', 'farbe' => '#b32d2e',
                'satz' => 'Diese Zeile nennt keine Lage. Turing raet sie nicht.');
        default:
            return array('wort' => 'NICHT GEMESSEN', 'farbe' => '#996800',
                'satz' => 'Diese Zeile traegt eine Lage, die Turing nicht kennt: '
                        . $c1_lage . '. Sie ist juenger als dieses Werkzeug — '
                        . 'was sie heisst, sagt das Paket, das sie geschrieben hat.');
    }
}
}

/** Die Zahlen einer Zeile, flach gelesen. Sie sind zweistufig: Produkt,
 *  dann Kategorie. Eine NULL wird MITGENANNT.
 *      Eine Null, die dasteht, ist eine Messung.
 *      Eine Null, die fehlt, ist eine Frage.                 (R-ZAHL-1) */
if (! function_exists('turing_narbe_zahlen_satz')) {
function turing_narbe_zahlen_satz(array $c1_z): string {
    $c1_teile = array();
    foreach ($c1_z as $c1_produkt => $c1_kat) {
        if (! is_array($c1_kat)) {
            $c1_teile[] = (string) $c1_produkt . ': ' . (is_scalar($c1_kat) ? (string) $c1_kat : '?');
            continue;
        }
        $c1_innen = array();
        foreach ($c1_kat as $c1_name => $c1_wert) {
            $c1_innen[] = (string) $c1_name . ' ' . (is_numeric($c1_wert) ? (string) (int) $c1_wert : '?');
        }
        $c1_teile[] = (string) $c1_produkt . ': ' . ($c1_innen ? implode(' · ', $c1_innen) : 'keine Zahlen');
    }
    return $c1_teile ? implode('   |   ', $c1_teile) : '';
}
}

if (! function_exists('turing_plugins_pfad')) {
function turing_plugins_pfad(): string {
    return defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins';
}
}

/** Was liegt, und was ist aktiv — ueber die WordPress-API, NICHT ueber Kasper.

    Kasper sieht, was GELADEN ist. Der Installer muss sehen, was NICHT
    installiert ist. (Vertrag 16 §5) */
if (! function_exists('turing_liegt')) {
function turing_liegt(): array {
    if (! function_exists('get_plugins')) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }
    $aus = array();
    foreach (get_plugins() as $datei => $d) {
        $ordner = strpos($datei, '/') !== false ? explode('/', $datei)[0] : $datei;
        $aus[$ordner] = array(
            'datei'   => $datei,
            'name'    => (string) ($d['Name'] ?? $ordner),
            'fassung' => (string) ($d['Version'] ?? ''),
            'aktiv'   => is_plugin_active($datei),
        );
    }
    return $aus;
}
}

/** Die Bodenfassung, die auf diesem Knoten LAEUFT. Ohne Kasper, ohne Liste. */
/* v3.4.0 — H228, ZWEITE HAELFTE. Bis 3.3.0 stand hier:

       return defined('CLUSTER_ONE_BODEN_FASSUNG') ? CLUSTER_ONE_BODEN_FASSUNG : '';

   Das ist die Sicht auf das, was GELADEN ist — und genau die verbietet
   Vertrag 16 §5: "Der Installer darf Kasper nicht fragen. Kasper sieht nur,
   was geladen ist, und der Installer muss sehen, was NICHT installiert ist."
   Ich habe den Satz am 04.09. zustimmend zitiert und zwei Zeilen weiter
   gegen ihn gebaut.

   Ein Familienordner, der da liegt und AUS ist, definiert die Konstante
   nicht — er war fuer den Installer unsichtbar und wurde als "kein Boden"
   gezaehlt. Also: gelesen wird das VERZEICHNIS.

       Wer fragt, was laeuft, erfaehrt nichts ueber das, was liegt. Und
       liegen bleibt, was den Knoten mischt.

   Diese Funktion gehoert zur HAND (sie fasst die Platte an); die Rechnung
   darauf steht WordPress-frei in tu_inst_ergebnis_pruefen(). */
if (! function_exists('turing_hand_familie_am_knoten')) {
function turing_hand_familie_am_knoten(): array {
    $plugins = turing_plugins_pfad();
    $aus     = array();
    if (! is_dir($plugins)) { return $aus; }

    foreach ((array) scandir($plugins) as $k) {
        if ($k === '.' || $k === '..' || $k[0] === '.') { continue; }
        $d = $plugins . '/' . $k;
        if (! is_dir($d)) { continue; }

        $boden  = '';
        $lesbar = false;
        $traegt = false;
        foreach ((array) glob($d . '/includes/*.php') as $f) {
            $c = @file_get_contents($f);
            if ($c === false) { $traegt = true; continue; }
            if (strpos((string) $c, 'CLUSTER_ONE_BODEN_FASSUNG') === false) { continue; }
            $traegt = true;
            if (preg_match('~define\s*\(\s*[\'"]CLUSTER_ONE_BODEN_FASSUNG[\'"]\s*,\s*[\'"]([0-9][0-9.]*)[\'"]~', (string) $c, $m)) {
                $boden  = $m[1];
                $lesbar = true;
            }
            break;
        }
        if (! $traegt) { continue; }          /* kein Familienordner */
        $aus[$k] = array('boden' => $boden, 'lesbar' => $lesbar,
                         'aktiv' => false, 'fassung' => '');
    }

    /* H240 — DIE ZWEI FELDER, DIE DIESE HAND NIE GELIEFERT HAT.

       Bis 3.5.0 endete diese Funktion hier, mit 'boden' und 'lesbar'. Die
       Regeln lesen aber vier Felder: tu_inst_richtung() braucht 'fassung',
       tu_inst_ergebnis_pruefen() braucht 'aktiv'. Beide bekamen sie nie —
       und beide antworteten deshalb immer dasselbe:

           jede Richtung war 'unbekannt'   -> der Rueckschritt-Riegel aus
                                              Vertrag 22 §3 war gebaut,
                                              bezeugt und tot
           jeder Ordner galt als ruhend    -> die Unterscheidung aus
                                              Vertrag 22 §2 ebenfalls

       T10 und T8 waren gruen, weil sie ihrer Regel die Liste SELBST
       hinlegen. Der Weg dorthin war nie gemessen.

           Ein Zeuge, der seiner Regel die Eingabe selbst hinlegt, misst
           die Regel und nicht den Weg dorthin. Auf dem Weg kann ein Feld
           fehlen, und niemand wird rot.

       Repariert wird an der QUELLE — nicht dadurch, dass der Aufrufer
       zwei Listen zusammenschiebt. Sonst waere die Anzeige heil und der
       Riegel bliebe tot. (T12 misst genau diesen Weg.)

       'aktiv' und 'fassung' weiss die WordPress-API, nicht die Platte
       allein. Das ist Auskunft ueber den Wirt und gehoert deshalb in die
       Hand. */
    foreach (turing_liegt() as $o => $d) {
        if (! isset($aus[$o])) { continue; }
        $aus[$o]['aktiv']   = ! empty($d['aktiv']);
        $aus[$o]['fassung'] = (string) $d['fassung'];
    }
    return $aus;
}
}

/* --------------------------------------------------------------------------
   DIE ZAHL STATT DES SCHWEIGENS.   Mammuts Frage vom 07.09.2026.

   Turings Tabellen zeigen die Familie. Ein Ordner ohne Boden steht in
   keiner von ihnen — und ein Betrachter, der das nicht weiss, liest aus
   dem Fehlen, dass dort nichts liegt. Mammut ist heute fast darauf
   hereingefallen.

       "Es steht nicht in der Tabelle" heisst nicht "es liegt nicht auf
       der Platte". Eine gefilterte Liste, die ihren Filter nicht nennt,
       ist eine Behauptung ueber alles, was sie weglaesst.

   Diese Hand zaehlt, sie nennt nicht. Sie SAMMELT keine Namen — deshalb
   kann sie auch keine ausgeben. Die Zusicherung steht damit in der Form
   der Funktion und nicht in einem Versprechen daneben.

   DREI ZAHLEN, KEINE EINE. Ein Ordner, der sich nicht lesen laesst, ist
   nicht "ohne Boden" — er ist NICHT GEMESSEN. Wer beides zu einer Zahl
   zusammenzieht, hat die Luecke geschlossen, indem er sie zugedeckt hat.

   UND SIE MUESSEN AUFGEHEN. familie + ohne_boden + nicht_lesbar ergibt
   ordner_gesamt. Was in keiner der drei Zahlen steckt, ist weder gezeigt
   noch gezaehlt — und genau das war der Zustand bis 3.6.0.

   Was "Boden tragen" heisst, entscheidet NICHT diese Funktion: sie fragt
   tu_hand_familie_am_knoten(). Zwei eigene Definitionen desselben Wortes
   waeren derselbe Fehler wie H240, nur eine Etage hoeher.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_hand_ohne_boden_zaehlen')) {
function turing_hand_ohne_boden_zaehlen(?array $familie = null): array {
    $z = array('familie' => 0, 'ohne_boden' => 0, 'nicht_lesbar' => 0,
               'ordner_gesamt' => 0, 'gemessen' => false);

    if ($familie === null) { $familie = turing_hand_familie_am_knoten(); }
    $z['familie'] = count($familie);

    $plugins = turing_plugins_pfad();
    if (! is_dir($plugins) || ! is_readable($plugins)) { return $z; }
    $liste = @scandir($plugins);
    if ($liste === false) { return $z; }

    $z['gemessen'] = true;
    foreach ($liste as $k) {
        if ($k === '.' || $k === '..' || $k === '' || $k[0] === '.') { continue; }
        $d = $plugins . '/' . $k;
        if (! is_dir($d)) { continue; }
        $z['ordner_gesamt']++;
        if (isset($familie[$k]))  { continue; }
        if (! is_readable($d))    { $z['nicht_lesbar']++; continue; }
        $z['ohne_boden']++;
    }
    return $z;
}
}

/** Nur noch fuer die Anzeige: was LAEUFT. Nicht fuer die Rechnung. */
if (! function_exists('turing_boden_am_knoten')) {
function turing_boden_am_knoten(): string {
    return defined('CLUSTER_ONE_BODEN_FASSUNG') ? (string) CLUSTER_ONE_BODEN_FASSUNG : '';
}
}

if (! function_exists('turing_hand_lage')) {
function turing_hand_lage(): array {
    if (! function_exists('get_plugins')) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }
    return array(
        'disallow_file_mods' => defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS,
        'darf_installieren'  => current_user_can('install_plugins'),
        'hangar'             => turing_hangar_pfad(),
        'tmp'                => get_temp_dir() . 'turing-' . wp_generate_password(8, false),
        'familie_am_knoten'  => turing_hand_familie_am_knoten(),
        'manifest'           => turing_inst_manifest_lesen(turing_hangar_pfad() !== '' ? turing_hangar_pfad() . '/manifest.json' : ''),
        'bestaetigt_unbekannte'   => false,
        'bestaetigt_ruhend'       => false,
        'bestaetigt_rueckschritt' => false,
        'im_hangar'               => array(),
    );
}
}

/** Die Hand. Elf Zeilen Arbeit, und jede davon benutzt WordPress' eigenen
    Weg statt eines eigenen Kopierers — damit wir dessen Rechteverwaltung
    erben statt sie nachzubauen (16a §3.4). */
/* --------------------------------------------------------------------------
   v3.9.8 — DIE HAND ZUR SEITENLAGE. Das einzige Stueck, das WordPress
   anfasst; gerechnet wird in tu_inst_seitenlage().

   Gefragt werden die Register, die WordPress selbst fuehrt, und zwar mit
   array_key_exists statt isset — weil der Wert null hier eine ROLLE ist
   („versteckt angemeldet") und keine Abwesenheit.

   Gemessen wird, was BIS ZU DIESEM AUGENBLICK angemeldet ist. Laeuft dieser
   Aufruf vor admin_menu, sind die Register leer, und die Lage heisst
   'unbekannt' — nicht „nicht angemeldet".
   -------------------------------------------------------------------------- */
if (! function_exists('turing_hand_seitenlage')) {
function turing_hand_seitenlage(string $slug): array {
    $pp = isset($GLOBALS['_parent_pages']) && is_array($GLOBALS['_parent_pages'])
            ? $GLOBALS['_parent_pages'] : null;
    $rp = isset($GLOBALS['_registered_pages']) && is_array($GLOBALS['_registered_pages'])
            ? $GLOBALS['_registered_pages'] : null;
    $haken_name = 'admin_page_' . $slug;

    $mess = array(
        'slug'          => $slug,
        'register_da'   => $pp !== null,
        'schluessel'    => $pp !== null && array_key_exists($slug, $pp),
        'eltern'        => false,
        'url'           => function_exists('menu_page_url') ? (string) menu_page_url($slug, false) : '',
        'reg_versteckt' => $rp !== null ? array_key_exists($haken_name, $rp) : null,
        'haken'         => function_exists('has_action') ? (bool) has_action($haken_name) : null,
        'haken_name'    => $haken_name,
    );
    if ($mess['schluessel']) { $mess['eltern'] = $pp[$slug]; }
    $mess['lage'] = turing_inst_seitenlage($mess);
    /* die Adresse wird NICHT erfunden: es ist der Zweig, den menu_page_url()
       selbst nimmt, wenn der Elternwert leer ist. */
    $mess['hand_url'] = ($mess['lage'] === 'versteckt_offen' || $mess['lage'] === 'versteckt_halb')
                          && function_exists('admin_url')
                        ? (string) admin_url('admin.php?page=' . $slug)
                        : '';
    return $mess;
}
}

if (! function_exists('turing_hand_wordpress')) {
function turing_hand_wordpress(): array {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    $plugins = turing_plugins_pfad();
    $alt     = $plugins . '/' . turing_inst_alt_name();
    $marke   = $plugins . '/' . turing_inst_marke_name();

    return array(
        'entpacken' => function (string $zip, string $ziel) {
            WP_Filesystem();
            if (! wp_mkdir_p($ziel)) { return 'Zwischenlager nicht anlegbar'; }
            $r = unzip_file($zip, $ziel);
            return is_wp_error($r) ? $r->get_error_message() : true;
        },
        'liegt_da' => function (string $ordner) use ($plugins) {
            return is_dir($plugins . '/' . $ordner);
        },
        'beiseite' => function (string $ordner) use ($plugins, $alt) {
            if (! wp_mkdir_p($alt)) { return 'Sicherungsordner nicht anlegbar'; }
            if (is_dir($alt . '/' . $ordner)) { return 'dort liegt schon eine Sicherung'; }
            /* Vorher abschalten: ein Ordner, dessen Dateien PHP gerade
               geladen hat, darf nicht unter dem laufenden Request wandern. */
            foreach (turing_liegt() as $o => $d) {
                if ($o === $ordner && $d['aktiv']) { deactivate_plugins($d['datei'], true); }
            }
            return @rename($plugins . '/' . $ordner, $alt . '/' . $ordner) ? true : 'Verschieben schlug fehl'; // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename -- Installer-Hand: der Plugin-Ordner wandert innerhalb von wp-content/plugins beiseite, gleiches Dateisystem; 'zurueck' vier Zeilen tiefer ist der Spiegel dieser Zeile
        },
        'zurueck' => function (string $ordner) use ($plugins, $alt) {
            if (! is_dir($alt . '/' . $ordner)) { return false; }
            return @rename($alt . '/' . $ordner, $plugins . '/' . $ordner); // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename -- Installer-Hand: der Rueckweg von 'beiseite', gleicher Ordner, gleiches Dateisystem
        },
        'legen' => function (string $quelle, string $ordner) use ($plugins) {
            WP_Filesystem();
            $r = copy_dir($quelle, $plugins . '/' . $ordner);
            return is_wp_error($r) ? $r->get_error_message() : true;
        },
        'entfernen' => function (string $ordner) use ($plugins) {
            global $wp_filesystem;
            WP_Filesystem();
            return $wp_filesystem ? $wp_filesystem->delete($plugins . '/' . $ordner, true) : false;
        },
        'aktivieren' => function (string $ordner, string $hauptdatei) {
            $r = activate_plugin($hauptdatei);
            return is_wp_error($r) ? $r->get_error_message() : true;
        },
        'alt_raeumen' => function (array $liste) use ($alt) {
            global $wp_filesystem;
            WP_Filesystem();
            if (! $wp_filesystem) { return false; }
            foreach ($liste as $o) { $wp_filesystem->delete($alt . '/' . $o, true); }
            @rmdir($alt); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir -- der leere Sicherungsordner nach dem Lauf; seinen Inhalt hat $wp_filesystem->delete() eine Zeile hoeher entfernt
            return true;
        },
        'marke_schreiben' => function (array $m) use ($marke) {
            return @file_put_contents($marke, (string) wp_json_encode($m)) !== false ? true : false;
        },
        'marke_lesen' => function () use ($marke) {
            if (! is_file($marke)) { return null; }
            $d = json_decode((string) @file_get_contents($marke), true);
            return is_array($d) ? $d : array('zeit' => 0, 'pakete' => array());
        },
        'marke_loeschen' => function () use ($marke) { @unlink($marke); return true; }, // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- die Marke des Installers (JSON-Datei unter wp-content/plugins), die 'marke_schreiben' acht Zeilen hoeher mit file_put_contents anlegt

        /* H236 — die Liste des Wirts verwerfen. Die Regel sagt WANN, die
           Hand weiss WIE. Auf einem WordPress ist das eine Zeile; auf einem
           Pruefstand ist es die Stelle, an der der Zeuge misst, ob sie
           ueberhaupt gerufen wird. */
        'liste_verwerfen' => function () {
            if (function_exists('wp_clean_plugins_cache')) { wp_clean_plugins_cache(false); }
            return true;
        },

        /* H231a — das Zwischenlager, auf beiden Wegen. */
        'tmp_raeumen' => function (string $tmp) {
            global $wp_filesystem;
            if ($tmp === '' || ! is_dir($tmp)) { return true; }
            WP_Filesystem();
            return $wp_filesystem ? $wp_filesystem->delete($tmp, true) : false;
        },
    );
}
}

/* ==========================================================================
   4.0.0 (02.11.2026, Neo), RUNDE 50 „XI" — WAS AUF DIESEM KNOTEN LIEGT, als eigene Funktion.

   Bis 3.15.0 stand dieser Block mitten in tu_seite_installer(). Mit der Teilung in
   Turing Diagnostics (Verzeichnis) und Turing Installer (GitHub) braucht ihn der
   Pruefer ohne den Installer: Nemesus liest nach jedem Aufspielen hier ab, welche
   Fassung und welcher Boden auf dem Knoten liegen — und auf Main, Stephan und
   Andreas liegt nur noch Diagnostics. Unveraendert hierher gezogen, mit zwei
   Aenderungen: die eigene Zeile nennt den eigenen Ordner (basename, gemessen statt
   getippt), und darunter steht das Geschwister-Werkzeug, wenn es sich bei Kasper
   gemeldet hat — Fassung und Wirt aus Kaspers Antwort, nicht aus dieser Datei.
   Zwei Leser (Installer-Reiter, Kasper-Reiter), eine Quelle.
   ========================================================================== */
if (! function_exists('turing_lage_tabellen')) {
function turing_lage_tabellen(array $liegt, array $auf_platte): void {
    $familie = turing_inst_familie_aus_liste($liegt, $auf_platte);
    $zahl    = turing_hand_ohne_boden_zaehlen($auf_platte);

    echo '<h3>Was auf diesem Knoten liegt <span style="font-size:13px;font-weight:400;color:#666;">'
       . '&middot; nur Ordner mit Boden</span></h3>';
    echo '<p style="max-width:860px;color:#666;">Wer dazugehoert, entscheidet der Boden, nie der Ordnername. '
       . 'Die Turing-Werkzeuge stehen unter der Tabelle, weil sie keinen Boden tragen.</p>';
    echo '<table class="widefat striped" style="max-width:860px;"><thead><tr>'
       . '<th>Ordner</th><th>Fassung</th><th>Zustand</th></tr></thead><tbody>';
    if (! $familie) {
        echo '<tr><td colspan="3"><em>Es liegt nichts von Cluster One hier — kein Ordner traegt einen Boden.</em></td></tr>';
    }
    foreach ($familie as $o => $d) {
        echo '<tr><td><code>' . esc_html($o) . '</code></td><td>' . esc_html($d['fassung']) . '</td><td>'
           . ($d['aktiv'] ? '<strong style="color:#1a7f37;">aktiv</strong>' : 'liegt, aus') . '</td></tr>';
    }
    echo '</tbody></table>';

    /* ---- DIE EIGENE ZEILE.   Christians Auftrag vom 09.10.2026, Stueck 1.

       Sie steht UNTER der Tabelle und nicht IN ihr. Der Unterschied ist
       nicht Geschmack: die Tabelle ist eine MESSUNG — jede ihrer Zeilen
       kommt aus tu_hand_familie_am_knoten() und traegt einen Boden. Diese
       Zeile kommt aus keiner Messung, sie nennt sich selbst.

           Ein Werkzeug, das den Boden legt, kann nicht auf ihm stehen.

       DIE FASSUNG WIRD GELESEN, NICHT GESCHRIEBEN. tu_version() holt sie
       aus dem eigenen Kopffeld "* Version:" ueber get_file_data(__FILE__)
       — dieselbe Zeile, die WordPress dem Admin zeigt. Eine getippte
       Nummer waere eine zweite Wahrheit ueber dieselbe Datei und wuerde
       beim naechsten Bau driften, ohne dass es jemand merkt.

       "aktiv" steht als fester Text, und das ist kein Raten: diese Seite
       wird von Turing selbst ausgegeben. Laeuft sie, laeuft Turing. Die
       benannte Grenze dazu steht im URTEIL 24.
       ---------------------------------------------------------------- */
    echo '<div style="max-width:860px;margin:0;border-top:2px solid #8c8f94;padding:9px 10px 7px;'
       . 'background:#fbfbfb;font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;'
       . 'font-size:13px;color:#3c434a;">'
       . '<code>' . esc_html(basename(__DIR__)) . '</code>&nbsp;&nbsp;&nbsp;<strong>' . esc_html(turing_version()) . '</strong>'
       . '&nbsp;&nbsp;&nbsp;aktiv &middot; traegt keinen Boden (mit Absicht)</div>';
    /* 4.0.0: das Geschwister-Werkzeug (Diagnostics neben Installer und umgekehrt). Es nennt sich nicht
       selbst: entweder hat es sich bei Kasper gemeldet, dann stehen hier Kaspers Wirt und Fassung —
       oder es steht nicht hier. Ohne Boden gibt es keinen Kasper, dann steht nur die eigene Zeile. */
    $turing_geschw = turing_geschwister_organ();
    if ($turing_geschw !== '' && function_exists('cluster_one_kasper')) {
        $turing_alle = cluster_one_kasper();
        if (isset($turing_alle[$turing_geschw])) {
            $turing_wirt = function_exists('cluster_one_organ_wirt') ? (string) cluster_one_organ_wirt($turing_geschw) : '';
            echo '<div style="max-width:860px;margin:0;border-top:2px solid #8c8f94;padding:9px 10px 7px;'
               . 'background:#fbfbfb;font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;'
               . 'font-size:13px;color:#3c434a;">'
               . '<code>' . esc_html($turing_wirt !== '' ? $turing_wirt : $turing_geschw) . '</code>&nbsp;&nbsp;&nbsp;<strong>'
               . esc_html((string) ($turing_alle[$turing_geschw]['fassung'] ?? '?')) . '</strong>'
               . '&nbsp;&nbsp;&nbsp;gemeldet &middot; traegt keinen Boden (mit Absicht)</div>';
        }
    }
    if ($auf_platte) {
        echo '<h3>Boeden, wie sie auf der Platte liegen</h3>';
        echo '<table class="widefat striped" style="max-width:660px;"><tbody>';
        foreach ($auf_platte as $o => $d) {
            echo '<tr><td><code>' . esc_html((string) $o) . '</code></td><td>'
               . (! empty($d['lesbar'])
                    ? esc_html((string) $d['boden'])
                    : '<strong style="color:#996800;">nicht lesbar — NICHT GEMESSEN</strong>')
               . '</td></tr>';
        }
        echo '</tbody></table>';
        echo '<p style="max-width:860px;color:#666;">Von der Platte gelesen, also auch ausgeschaltete Ordner.</p>';
    }

    /* ---- DIE BENANNTE GRENZE MIT EINER ZAHL.

       Beide Tabellen oben sind gefiltert. Ohne diese Zeile liest ein
       Betrachter aus dem Fehlen eines Ordners, dass er nicht auf der
       Platte liegt — und das hat Turing nie gemessen. Gezaehlt wird;
       genannt und bewertet wird nicht — mit der einen Ausnahme, die sich
       die Zeile unter der ersten Tabelle selbst nimmt (09.10.2026,
       Stueck 1). Wer diesen Satz ohne die Ausnahme stehen laesst, hat
       einen Satz, dessen Bedingung sich geweitet hat. Die Hand
       tu_hand_ohne_boden_zaehlen() sammelt weiterhin KEINE Namen — das
       Nennen passiert hier, nicht dort.

       UND HIER WIRD AUCH KEINE MARKE STEHEN. Christian, 10.10.2026: duerfen
       fremde Pakete eine Marke bekommen? NEIN. Drei Gruende, und der zweite
       reicht allein.

         a) Eine Marke ist eine Auskunft ueber ein fremdes Haus, die dieses
            nicht erteilt hat. Dieselbe Lehre wie in v3.9.5 vierhundert
            Zeilen weiter: wer aus einem Namen auf einen Knoten schliesst,
            hat den Namen fuer den Knoten genommen.
         b) EINE MARKE OHNE IHREN SOLLWERT IST EINE ZAHL, KEINE PRUEFUNG.
            Turings Markenvergleich braucht ein Manifest — der Sollwert kommt
            aus $manifest[$datei]['sha256']. Fuer ein fremdes Paket gibt es
            keines. Turing koennte nur sagen „diese Datei hat diesen Abdruck",
            und niemand koennte daraus etwas folgern. Das ist genau die Form,
            die Turing sonst ablehnt: eine Zahl ohne ihr Verfahren.
         c) Es waere ein Fingerabdruck. Eine Liste fremder Pakete mit
            Fassungen verraet, welche bekannten Luecken zuerst zu versuchen
            sind — und MARKEN sind schaerfer als Fassungen, weil sie den
            genauen Bau nennen.

           Ein Pruefer kann nur eine Zusage pruefen. Wer nichts zugesagt
           hat, kann nichts gebrochen haben.

       ES BLEIBT ALSO: die ZAHL — damit aus dem Schweigen kein Schluss wird.
       Keine Namen, keine Marken. Und die Absage hat eine Verfallsbedingung:
       meldet ein fremdes Paket sich eines Tages SELBST bei Kasper an — so wie
       Turing es tut, ohne einen Boden zu tragen —, dann hat es eine Zusage
       gemacht, und dann darf sie gemessen werden.

           Wer sich selbst nennt, darf genannt werden. */
    echo '<p style="max-width:860px;color:#666;">';
    if (! $zahl['gemessen']) {
        echo '<strong style="color:#996800;">Das Plugin-Verzeichnis liess sich nicht lesen — '
           . 'NICHT GEMESSEN.</strong> Wie viele Ordner hier sonst noch liegen, ist damit offen; '
           . 'die Tabellen oben sind es trotzdem nicht.';
    } else {
        printf('Im Plugin-Verzeichnis liegen <strong>%d</strong> Ordner: <strong>%d</strong> mit Boden '
             . '(oben genannt), <strong>%d</strong> ohne Boden (die Turing-Werkzeuge gehoeren dazu), <strong>%d</strong> nicht lesbar. '
             . 'Die Ordner ohne Boden nennt Turing nicht.',
             (int) $zahl['ordner_gesamt'], (int) $zahl['familie'],
             (int) $zahl['ohne_boden'], (int) $zahl['nicht_lesbar']);
    }
    echo '</p>';

}
}

/** 4.0.0: der Stempel — Fassung und Werft — einmal geschrieben, zwei Leser: Installer-Reiter und
    Kasper-Reiter (Nemesus, 02.11.: „der Werft-Hinweis kann ruhig auf beiden stehen). Ohne Boden-Stand,
    Grund bei TURING_WERFT. */
if (! function_exists('turing_stempel')) {
function turing_stempel(): void {
    echo '<p style="opacity:.65;font-size:.9em;margin:-.2em 0 1em;"><code style="opacity:.8;">v' . esc_html(turing_version()) . '</code>'
       . ' &nbsp;·&nbsp; ' . esc_html('Werft: ' . (defined('TURING_WERFT') ? (string) TURING_WERFT : 'unbekannt'))
       . ' · ' . esc_html('traegt keinen Boden (Werkzeug)') . '</p>';
}
}

/* ==========================================================================
   DIE ELTERN-SCHALE — spaet und nur, wenn es sie nicht gibt.

   Vertrag 16 §3. Prioritaet 99, Pruefung und Anmeldung im SELBEN Durchgang
   (16c §4). Auf einem Knoten mit irgendeinem Light meldet menue.php die
   Schale schon an — dann tut Turing hier NICHTS. Auf einem Knoten mit nur
   Turing steht sie sonst nicht, und der Setup-Schirm haette keinen Ort.

   ABSICHTLICH NICHT IN DIESER FASSUNG: der Umzug der Turing-Seite von
   "Werkzeuge" unter "Cluster One". Vertrag 17 §6 nimmt Welle 4 an der ZAHL
   der Punkte unter Cluster One ab; ein sechster Punkt von mir waere dort
   ein Fehlalarm. Vertrag 17 §5 sagt ausdruecklich "Turing ist von Welle 4
   nicht betroffen". Der Umzug gehoert in die erste Runde DANACH.
   ========================================================================== */
add_action('admin_menu', function () {
    if (! turing_inst_schale_noetig((array) ($GLOBALS['admin_page_hooks'] ?? array()))) { return; }
    add_menu_page('Cluster One', 'Cluster One', 'manage_options', 'cluster_one',
                  'turing_seite_setup', 'dashicons-networking', 76);
}, 99);

/** Der Setup-Schirm: der Knoten hat Turing und sonst nichts von uns. */
if (! function_exists('turing_seite_setup')) {
function turing_seite_setup(): void {
    if (! current_user_can('manage_options')) { return; }
    echo '<div class="wrap"><h1>Turing <span style="font-size:13px;font-weight:400;color:#666;">' . esc_html(turing_version()) . '</span> (Cluster One)</h1>';
    echo '<p style="max-width:820px;"><strong>Auf diesem Knoten liegt bisher nur Turing.</strong> '
       . 'Turing ist der Installer — es traegt selbst kein Stueck von Cluster One, sondern spielt '
       . 'die Produkte auf, die im Hangar liegen.</p>';
    echo '<p><a class="button button-primary" href="'
       . esc_url(admin_url('tools.php?page=' . turing_seitenname() . '&reiter=installer')) . '">Zum Installer</a></p>';
    echo '<p style="max-width:820px;color:#666;">Dieser Punkt erscheint nur, solange ihn niemand '
       . 'sonst anmeldet. Sobald ein Produkt von Cluster One laeuft, gehoert die Schale ihm und '
       . 'Turing haelt sich heraus.</p>';
    echo '</div>';
}
}

/* ==========================================================================
   DER INSTALLER — die erste Lasche, und die vorgewaehlte.

   Zwei Haelften, und die erste ist die Diagnose (16d §2):

       Ein Installer, der nur installieren kann, laesst den Admin genau in
       dem Moment allein, den er selbst verursacht hat.

   Und die Linie zur Landkarte, damit nicht zwei Werkzeuge dasselbe
   verschieden sagen (NACHTRAG 16d §4):

       TURING        misst PAKETE — was liegt, was ist aktiv, welche
                     Boeden, steht eine Marke.
       DIE LANDKARTE misst ORGANE — welche Datei laeuft aus welchem Plugin.
   ========================================================================== */
if (! function_exists('turing_seite_installer')) {
function turing_seite_installer(): void {
    if (! current_user_can('turing_sehen')) { return; } // 3.14.8: bis dahin 'manage_options'; der Lauf darunter prueft tu_darf_tun()

    /* ================================================================
       H241 — ERST DER LAUF, DANN DAS LESEN.

       Bis 3.5.0 las diese Seite den Zustand oben und liess den Lauf
       unten laufen. Der Betrachter las von oben nach unten: erst den
       Zustand von VORHER, dann die Meldung, dass er nicht mehr gilt.

           Eine Erfolgsmeldung unter einer Tabelle, die sie bestreitet,
           laesst den Betrachter waehlen, welchem der beiden er glaubt —
           und beide stammen von demselben Werkzeug.

       Der Lauf entscheidet gegen den Zustand VOR dem Zug; das ist
       richtig und bleibt. Angezeigt wird der Zustand DANACH.
       (T13 misst die Reihenfolge.)
       ================================================================ */
    $hand = turing_hand_wordpress();
    $erg  = null;

    if (isset($_POST['turing_inst_los']) && turing_darf_tun() && check_admin_referer('turing_inst_lauf', '_tunonce')) {
        $vor    = turing_hand_lage();                       /* der Zustand VOR dem Zug */
        $hangar = turing_inst_hangar_lesen((string) $vor['hangar']);
        /* 07.09.: angekreuzt werden ORDNER, gewaehlt wird je Ordner eine
           Fassung. Bis 3.7.0 stand hier die Dateiliste der Kacheln — und
           damit war die hoechste Fassung die einzige, die ueberhaupt in
           einen Satz kommen konnte. */
        $ordner_wahl  = array_map('sanitize_text_field', (array) wp_unslash($_POST['turing_paket']       ?? array()));
        $fassung_wahl = array_map('sanitize_text_field', (array) wp_unslash($_POST['turing_inst_fassung'] ?? array()));
        $gewaehlt     = turing_inst_wahl_aufloesen($hangar, $ordner_wahl, $fassung_wahl);
        $zips         = array_column($gewaehlt, 'pfad');
        $vor['bestaetigt_unbekannte']   = ! empty($_POST['turing_inst_unbekannt']);
        $vor['bestaetigt_ruhend']       = ! empty($_POST['turing_inst_ruhend']);
        $vor['bestaetigt_rueckschritt'] = ! empty($_POST['turing_inst_rueckschritt']);
        $vor['im_hangar']               = array_column($hangar['kacheln'], 'ordner');
        $erg = turing_inst_installieren($zips, $vor, $hand);
        $erg['zwischenlager'] = (string) ($erg['zwischenlager'] ?? $vor['tmp']);
    }

    /* Ab hier: der Zustand NACH dem Lauf. */
    $lage  = turing_hand_lage();
    $liegt = turing_liegt();
    $darf  = turing_inst_darf_schreiben($lage);

    echo '<h2 style="margin-top:.4em;">Der Installer'
       . turing_blase('Turing spielt Saetze auf, keine Stuecke. Vor dem ersten Handgriff wird alles entpackt und geprueft; ' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tu_blase() gibt fertiges HTML zurueck (wpautop(esc_html) im Innern)
           . 'erst wenn der ganze Satz zusammenpasst, wird der Knoten angefasst. Halb aufgespielt ist schlechter als gar nicht. '
           . 'Oben steht, was auf diesem Knoten liegt; unten der Hangar, ein Verzeichnis unter wp-content, in das du die Zips legst. '
           . 'Angekreuzt wird je Paket ein Ordner, gewaehlt eine Fassung; ein Lauf legt die Dateien und schaltet jedes aufgespielte Paket ein. '
           . 'Jedes Paket bekommt vorher eines von drei Urteilen: geprueft, abweichend, nicht gemessen. Abweichend wird nie aufgespielt.')
       . '</h2>';
    /* 3.14.6 (28.10.2026, Neo): der Stempel – Fassung und Werft, wie unter den Ueberschriften
       der Produkte (Runde 24), hier im Reiter Installer (Nemesus: „Bei Turing muesste der Hinweis
       mit der Werft noch ins Tab Installer"). Ohne Boden-Stand, Grund bei TU_WERFT. Die Fassung
       kommt aus tu_version(), der einen Quelle: dem Dateikopf. */
    turing_stempel();   /* 4.0.0: dieselbe Zeile steht unter Kasper; eine Funktion, zwei Leser */

    /* ---- Was der Lauf ergeben hat, bevor irgendeine Tabelle folgt. */
    if (is_array($erg)) {
        if (! empty($erg['ok'])) {
            /* 7b — "Kommt ein Organ zweimal?" wird hier NICHT beantwortet, und
               der Grund steht dabei. Die Organe der eben aktivierten Ordner
               haben sich in DIESEM Aufbau nicht gemeldet: Kasper fuellt sich
               beim Laden, und geladen wird beim naechsten Aufbau. Eine Antwort
               an dieser Stelle waere aus dem Zustand VOR dem Lauf gerechnet —
               genau der Fehler, den H241 zwei Absaetze weiter oben behoben hat. */
            echo '<div class="notice notice-success"><p><strong>Der Satz liegt.</strong> Aufgespielt und '
               . 'aktiviert: <code>' . esc_html(implode(' · ', (array) $erg['gelegt'])) . '</code><br>'
               . '<span style="color:#666;">Alles, was unten steht, ist NACH diesem Lauf gelesen.</span>'
               . '</p><p style="color:#666;">Ob nach diesem Lauf ein <em>Organ</em> aus zwei Plugins '
               . 'kommt, steht hier nicht: die eben aktivierten Ordner haben sich in diesem Seitenaufbau '
               . 'noch nicht gemeldet. Die Antwort steht beim <strong>naechsten</strong> Aufbau unter '
               . '<em>Kasper</em> &mdash; gemessen am geladenen Code, nicht an Dateinamen.</p></div>';
        } else {
            $lg = (string) ($erg['lage'] ?? 'rot');
            $kl = ($lg === 'nicht_gemessen') ? 'notice-warning' : (($lg === 'bestaetigung') ? 'notice-info' : 'notice-error');
            $ue = array('rot' => 'Nicht aufgespielt', 'bestaetigung' => 'Es fehlt eine Bestaetigung',
                        'nicht_gemessen' => 'NICHT GEMESSEN — nicht aufgespielt');
            echo '<div class="notice ' . esc_attr($kl) . '"><p><strong>' . esc_html($ue[$lg] ?? $ue['rot'])
               . ' — Halt in Phase ' . (int) ($erg['phase'] ?? 0) . '.</strong><br>'
               . esc_html((string) $erg['grund'])
               . '</p><p style="color:#666;">Das Zwischenlager <code>'
               . esc_html((string) $erg['zwischenlager'])
               . '</code> wurde nach diesem Halt geraeumt.'
               . ((int) ($erg['phase'] ?? 0) < 2 ? ' Am Knoten wurde nichts veraendert.' : '')
               . '</p></div>';
        }
    }

    /* ---- Die liegengebliebene Marke. Zuerst, weil sie alles andere faerbt. */
    $marke = call_user_func($hand['marke_lesen']);
    if (is_array($marke)) {
        echo '<div class="notice notice-error"><p><strong>Ein Vorlauf wurde nicht zu Ende gebracht.</strong> '
           . 'Es liegt eine Marke vom ' . esc_html(date_i18n('d.m.Y H:i', (int) ($marke['zeit'] ?? 0)))
           . ' fuer den Satz: <code>' . esc_html(implode(' · ', (array) ($marke['pakete'] ?? array())))
           . '</code>. Der Zustand unten kann unvollstaendig sein. Ein erneuter Lauf ueber denselben '
           . 'Satz stellt ihn her.</p></div>';
    }

    /* ---- Die Haertung. */
    if (! $darf['darf']) {
        echo '<div class="notice notice-warning"><p><strong>Turing installiert auf diesem Knoten nichts.</strong><br>'
           . esc_html($darf['grund']) . '</p></div>';
    }

    /* ---- Erste Haelfte: die Diagnose.

       H228: was LIEGT, nicht was laeuft. Der Admin muss den veralteten
       Ordner sehen koennen, sonst kann er die Meldung nicht befolgen.

       Die Platte wird VOR der ersten Tabelle gelesen, weil beide Tabellen
       dieselbe Auskunft brauchen: welcher Ordner traegt einen Boden. Zwei
       Lesevorgaenge waeren zwei Zeitpunkte. */
    $auf_platte = turing_hand_familie_am_knoten();
    /* 4.0.0: die beiden Tabellen und die Zahl wohnen in turing_lage_tabellen() — auch der Kasper-Reiter
       zeigt sie, denn auf einem Knoten ohne Installer muss man die Fassungen trotzdem ablesen koennen. */
    turing_lage_tabellen($liegt, $auf_platte);

    $boden = turing_boden_am_knoten();
    /* v3.9.11 — die Lage EINMAL holen und dreimal lesen, nicht dreimal holen. */
    $c1_hl = turing_hangar_lage();
    echo '<p style="max-width:860px;color:#666;"><strong>Boden, der gerade LAEUFT:</strong> '
       . ($boden !== '' ? '<code>' . esc_html($boden) . '</code>' : '<em>keiner — es laeuft kein Produkt von Cluster One</em>')
       . ' &middot; <strong>Hangar:</strong> '
       . ($lage['hangar'] !== ''
            ? '<code>' . esc_html($lage['hangar']) . '</code>'
            : '<em>' . esc_html('nicht benutzbar — ' . (string) $c1_hl['lage']) . '</em>')
       . '</p>';
    /* 20.10.2026 (Neo), 3.14.0: hier stand „… steht unter Kasper und in der
       Landkarte." Die Landkarte ist seit heute der untere Teil des Kasper-Reiters. */
    echo '<p style="max-width:860px;color:#666;">Turing misst Pakete. Welche Datei aus welchem Plugin laeuft und ob die Kopien gleich sind, steht unter Kasper.</p>';

    /* v3.9.13 — WAS VON DIESEM KNOTEN ENTFERNT WURDE. Posten 10.

       ER STEHT HIER UND NICHT IM SPIEGEL, und der Grund ist ein
       gemessener Weg und keine Architektur: wer geloescht hat, kommt von
       selbst auf DIESEN Schirm, um nachzusehen, ob es gewirkt hat.

           Ein Werkzeug gehoert dorthin, wo der Mensch von selbst
           hinsieht.                                    (R-SCHIRM-1)

       ER STEHT AUCH DA, WENN NICHTS DA IST — nach derselben Regel wie
       die Zahl der Ordner ohne Boden weiter oben: damit aus dem
       Schweigen kein Schluss wird. „Keine Narbe" heisst NICHT „es wurde
       nie geloescht": vor ZUG 10 hatten die vier Lights gar keine
       uninstall.php, und was damals entfernt wurde, hat keine Spur
       hinterlassen. */
    $c1_nb = turing_narbe_lesen();
    echo '<h3>Was von diesem Knoten entfernt wurde</h3>';
    echo '<p style="max-width:860px;color:#666;">Die Spur, die die <code>uninstall.php</code> eines Pakets '
       . 'hinterlaesst (Option <code>cluster_one_entfernt</code>). Turing liest sie nur.</p>';

    if (! $c1_nb['gibt']) {
        echo '<p style="max-width:860px;"><em>Keine Spur: seit es sie gibt, wurde hier kein Paket mit Boden entfernt.</em></p>';
    } elseif ($c1_nb['gestalt'] === 'kein-feld') {
        echo '<div class="notice notice-error inline" style="padding:12px;max-width:860px;"><p style="margin:0;">'
           . '<strong>Die Narbe ist da und ist keine Liste.</strong> Turing liest sie nicht weiter und '
           . 'raet ihren Inhalt nicht. Wer sie sehen will: der Spiegel gibt sie roh aus.</p></div>';
    } else {
        echo '<table class="widefat striped" style="max-width:860px;"><thead><tr>'
           . '<th>Wirt</th><th>Wann</th><th>Lage</th><th>Was genommen wurde</th></tr></thead><tbody>';
        if (! $c1_nb['zeilen']) {
            echo '<tr><td colspan="4"><em>Die Liste ist leer.</em></td></tr>';
        }
        foreach ($c1_nb['zeilen'] as $c1_z) {
            $c1_l  = turing_narbe_lage_satz($c1_z['lage']);
            $c1_zs = turing_narbe_zahlen_satz($c1_z['zahlen']);

            /* „liegt wieder da" wird an TURINGS EIGENER Liste geprueft, nicht
               am Dateisystem: der Wirt kommt aus einer fremden Option, und aus
               fremden Zeichenketten baut Turing keine Pfade. */
            $c1_wieder = ($c1_z['wirt'] !== '' && isset($auf_platte[$c1_z['wirt']]));

            echo '<tr><td><code>' . esc_html($c1_z['wirt'] !== '' ? $c1_z['wirt'] : '—') . '</code>'
               . ($c1_wieder ? '<br><span style="font-size:11px;color:#666;">liegt wieder da</span>' : '')
               . '</td>';
            echo '<td style="font-size:12px;">'
               . ($c1_z['wann'] > 0
                    ? esc_html(function_exists('wp_date')
                        ? (string) wp_date('d.m.Y H:i', $c1_z['wann'])
                        : (string) date_i18n('d.m.Y H:i', $c1_z['wann']))
                    : '<em>ohne Zeitangabe</em>')
               . '</td>';
            echo '<td style="color:' . esc_attr($c1_l['farbe']) . ';"><strong>'
               . esc_html($c1_l['wort']) . '</strong><br><span style="font-size:11px;color:#666;">'
               . esc_html($c1_l['satz']) . '</span></td>';
            echo '<td style="font-size:12px;">'
               . ($c1_zs !== '' ? '<code>' . esc_html($c1_zs) . '</code>'
                                : '<em>keine Zahlen</em>')
               . '</td></tr>';
        }
        echo '</tbody></table>';

        if ($c1_nb['unlesbar'] > 0) {
            printf('<p style="max-width:860px;color:#b32d2e;"><strong>%d Zeile(n) der Narbe sind '
                 . 'unlesbar</strong> und stehen oben nicht. Sie sind kein Feld. Turing nennt sie, '
                 . 'statt sie zu uebergehen — eine Spur, von der man nicht weiss, dass sie fehlt, '
                 . 'ist schlimmer als eine, die fehlt.</p>', (int) $c1_nb['unlesbar']);
        }
        echo '<p style="max-width:860px;color:#666;">&bdquo;liegt wieder da&ldquo;: ein Familienordner dieses '
           . 'Namens liegt wieder auf der Platte, die Zeile ist Geschichte.</p>';
    }

    /* ---- Zweite Haelfte: der Hangar. */
    echo '<h3>Der Hangar</h3>';
    if ($lage['hangar'] === '') {
        /* v3.9.11 — VIER LAGEN, VIER SAETZE. Bis hierher stand hier EIN Satz
           fuer alle, und im Fall 'ausserhalb' war er eine Anweisung, die den
           Zustand nicht aendern konnte. */
        echo '<p style="max-width:860px;">' . esc_html(turing_hangar_satz($c1_hl)) . '</p>';
        return;
    }

    /* v3.9.12 — WAS AM HANGAR OFFEN STEHT. Posten 4.
       Der Hangar liegt unter wp-content/, weil Turing das verlangt — und
       damit im Webverzeichnis. Was das heisst, steht hier, bevor die
       Kacheln kommen, und es steht auch dann da, wenn nichts zu tun ist:
       dann ist der Satz leer und dieser Block zeichnet nichts. */
    $c1_hs = turing_hangar_schutz($c1_hl);
    $c1_hss = turing_hangar_schutz_satz($c1_hs);
    if ($c1_hss !== '') {
        echo '<div class="notice notice-warning inline" style="padding:12px;max-width:860px;">';
        echo '<p style="margin:0;"><strong>Der Hangar liegt im Webverzeichnis.</strong> '
           . esc_html($c1_hss) . '</p>';
        echo '<p style="margin:.6em 0 .2em;">Turing legt hier nichts selbst ab — das ist dein '
           . 'Verzeichnis. Die zwei Dateien, vollstaendig, zum Hineinkopieren nach '
           . '<code>' . esc_html((string) $lage['hangar']) . '</code>:</p>';
        foreach (turing_hangar_schutz_rezept() as $c1_dn => $c1_di) {
            echo '<p style="margin:.4em 0 .1em;"><code>' . esc_html($c1_dn) . '</code></p>';
            echo '<pre style="margin:0;padding:8px;background:#f6f7f7;border:1px solid #dcdcde;'
               . 'max-width:840px;overflow:auto;">' . esc_html($c1_di) . '</pre>';
        }
        echo '</div>';
    }

    $h = turing_inst_hangar_lesen($lage['hangar']);

    /* ---- Die Kacheln. */
    echo '<form method="post">';
    wp_nonce_field('turing_inst_lauf', '_tunonce');
    echo '<table class="widefat striped" style="max-width:1000px;"><thead><tr>'
       . '<th style="width:2em;"></th><th>Paket</th><th>Fassung</th><th>Boden</th>'
       . '<th>Pruefwert</th><th>Zustand</th><th>Beim Entfernen</th></tr></thead><tbody>';

    foreach ($h['kacheln'] as $p) {
        $z = turing_inst_zustand($p, $liegt);
        /* H229 — die Zustandsspalte nennt die Fassung, die sie ersetzt.
           tu_liegt() hat sie im selben Durchlauf schon geladen; bis 3.4.0
           stand dort nur "aktiv, andere Fassung", und der Admin sah, was
           kommt, aber nicht, was geht. */
        $alt = (string) ($liegt[$p['ordner']]['fassung'] ?? '');
        /* v3.9.4 — KEIN PFEIL OHNE FASSUNGSUNTERSCHIED. Bis 3.9.3 stand bei
           einem ausgeschalteten Ordner derselben Fassung „liegt, aus ·
           1.9.13 → 1.9.13" da. Der Pfeil versprach einen Dateiwechsel, den
           es nicht gibt, und verschwieg den Schalter, den es gibt. */
        $pfeil = turing_inst_pfeil($alt, (string) $p['fassung']);
        $zt = array('nicht_da'            => 'nicht installiert',
                    'inaktiv'             => 'liegt, aus' . $pfeil,
                    'aktiv'               => 'aktiv' . $pfeil,
                    'gleiche_fassung'     => 'aktiv, gleiche Fassung',
                    /* Die Arbeit dieser Kachel heisst Einschalten, und sie
                       steht hier, weil ein Lauf sie tut. */
                    'gleiche_fassung_aus' => 'liegt, <strong>aus</strong> · gleiche Fassung ('
                                           . esc_html((string) $p['fassung']) . '), kein Dateiwechsel'
                                           . '<br><strong>ein Lauf schaltet dieses Paket EIN.</strong>');
        /* H230 — und in welche RICHTUNG. */
        $ri = turing_inst_richtung($p, $auf_platte + $liegt);
        $rt = array('nachtrag' => '', 'neu' => '',
                    'rueckschritt' => '<br><strong style="color:#b32d2e;">RUECKSCHRITT</strong>',
                    'gleich' => '', 'unbekannt' => '<br><span style="color:#996800;">Richtung nicht messbar</span>');
        /* H228, Beschriftung: "passt nicht" hing am Paket. Nicht gepasst
           hat der KNOTEN. Fuenf rote Zeilen sagten dem Betrachter, mit
           seinen fuenf Paketen sei etwas nicht in Ordnung — dabei sind sie
           fehlerfrei und untereinander einig. */
        $passt = true;
        foreach ($auf_platte as $o2 => $d2) {
            if (! empty($d2['lesbar']) && (string) $d2['boden'] !== (string) $p['boden']) { $passt = false; }
        }

        /* Angekreuzt wird der ORDNER, nicht die Datei. Die Fassung waehlt
           die Liste daneben — sonst ist die hoechste die einzige, die je
           in einen Satz kommt, und der Rueckschritt-Riegel bleibt
           unerreichbar (Mammuts Messung vom 07.09.). */
        /* Die Sperre gilt der MENGE, nicht der Vorwahl. Liegt im Hangar noch
           eine Fassung, die sich von dem unterscheidet, was auf dem Knoten
           liegt, ist an dieser Kachel etwas zu tun — auch wenn die Vorwahl
           selbst schon liegt. (Christians DEV, dritter Anlauf.) */
        $wahl_liste = (array) ($h['nach_ordner'][$p['ordner']] ?? array($p));
        $tot        = turing_inst_kachel_tot($wahl_liste, $liegt);

        echo '<tr><td><input type="checkbox" name="turing_paket[]" value="' . esc_attr($p['ordner']) . '"'
           . ($tot ? ' disabled' : '') . '></td>';
        echo '<td><strong>' . esc_html($p['name'] !== '' ? $p['name'] : $p['ordner']) . '</strong><br>'
           . '<code style="font-size:11px;color:#666;">' . esc_html($p['datei']) . '</code>'
           . ($tot
                ? '<br><span style="font-size:11px;color:#666;">nicht anwaehlbar: keine Fassung im '
                . 'Hangar unterscheidet sich von dem, was hier liegt und laeuft — es gaebe '
                . 'nichts zu tun</span>'
                : ($z === 'gleiche_fassung'
                    ? '<br><span style="font-size:11px;color:#666;">die Vorwahl liegt bereits — '
                    . 'anwaehlbar ist diese Kachel wegen einer <em>anderen</em> Fassung im '
                    . 'Hangar; welche, entscheidet die Liste daneben</span>'
                    : ($z === 'gleiche_fassung_aus'
                        ? '<br><span style="font-size:11px;color:#666;">dieselbe Fassung liegt schon, '
                        . 'aber <strong>ausgeschaltet</strong> — anwaehlbar ist diese Kachel deshalb '
                        . 'nicht wegen der Dateien, sondern wegen des Schalters</span>' : '')))
           . '</td>';

        echo '<td>';
        if (count($wahl_liste) > 1) {
            /* Jede Zeile sagt, was sie bedeutet — der Admin soll nicht erst
               nach dem Absenden erfahren, dass er zurueckgeht. */
            echo '<select name="turing_inst_fassung[' . esc_attr($p['ordner']) . ']">';
            foreach ($wahl_liste as $v) {
                $vr = turing_inst_richtung($v, $auf_platte + $liegt);
                echo '<option value="' . esc_attr($v['datei']) . '">' . esc_html($v['fassung'])
                   . ($vr === 'rueckschritt' ? ' — RUECKSCHRITT' : '')
                   . ($vr === 'gleich'       ? ' — liegt bereits' : '')
                   . '</option>';
            }
            echo '</select><br><span style="font-size:11px;color:#666;">'
               . (int) count($wahl_liste) . ' Fassungen im Hangar. In den Satz kommt genau eine.</span>';
        } else {
            echo esc_html($p['fassung']);
        }
        if ($p['fassung_am_namen'] !== '' && $p['fassung_am_namen'] !== $p['fassung']) {
            echo '<br><span style="color:#b32d2e;font-size:11px;">Dateiname sagt '
               . esc_html($p['fassung_am_namen']) . '</span>';
        }
        echo '</td>';
        echo '<td' . ($passt ? '' : ' style="color:#b32d2e;font-weight:700;"') . '>'
           . esc_html($p['boden'])
           . ($passt ? '' : '<br><span style="font-size:11px;">der KNOTEN traegt einen anderen Boden</span>')
           . '</td>';
        $pw   = turing_inst_pruefwert($p, (array) $lage['manifest']);
        $farbe = array('bekannt' => '#1a7f37', 'abweichend' => '#b32d2e', 'unbekannt' => '#996800');
        echo '<td style="color:' . esc_attr($farbe[$pw['lage']]) . ';"><strong>'
           . esc_html(strtoupper($pw['lage'])) . '</strong><br><span style="font-size:11px;color:#666;">'
           . esc_html($pw['text']) . '</span></td>';
        /* 18.10.2026 (Neo): $zt traegt bei 'gleiche_fassung_aus' absichtlich
           <strong> und <br>; esc_html() zeigte sie als Klartext. wp_kses mit
           genau den Tags, die die Tabelle selbst benutzt. */
        echo '<td>' . wp_kses((string) ($zt[$z] ?? esc_html($z)), array('strong' => array(), 'br' => array(), 'em' => array(), 'span' => array('style' => array())))
           . ($rt[$ri] ?? '') . '</td>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $rt ist eine feste Tabelle aus Literalen (Zeile ~3741)
        echo '<td style="font-size:12px;color:#666;">'
           . ($p['entfernt'] !== '' ? esc_html($p['entfernt'])
                                    : '<em>Dieses Modul sagt nicht, was es beim Entfernen loescht.</em>')
           . '</td></tr>';
    }
    if (! $h['kacheln']) {
        echo '<tr><td colspan="7"><em>Im Hangar liegt kein Paket, das einen Boden traegt.</em></td></tr>';   // 18.10.2026 (Neo): 7 Spalten
    }
    echo '</tbody></table>';

    /* Die zweite, ausdrueckliche Bestaetigung (16a §1). Sie gilt fuer
       UNBEKANNT und winkt ABWEICHEND nie durch — deshalb steht das auch
       daneben und nicht nur im Vertrag. */
    /* Drei Fragen, drei Kreuze. Ein einziges Kreuz fuer alle drei waere
       genau das, was dieses Haus einen Ausweg nennt: ein Ja des Menschen
       auf eine Frage, die nicht gestellt wurde. */
    echo '<p><label><input type="checkbox" name="turing_inst_unbekannt" value="1"> '
       . '<strong>Ja, auch Pakete aufspielen, die nicht im Manifest stehen.</strong></label><br>'
       . '<span style="color:#666;">Ohne dieses Kreuz spielt Turing nur auf, was das Manifest kennt und '
       . 'bestaetigt. Ein Paket mit ABWEICHENDEM Pruefwert wird auch mit diesem Kreuz nicht aufgespielt.</span></p>';
    echo '<p><label><input type="checkbox" name="turing_inst_ruhend" value="1"> '
       . '<strong>Ja, obwohl ein ausgeschalteter Ordner mit anderem Boden liegen bleibt.</strong></label><br>'
       . '<span style="color:#666;">Er mischt den Knoten heute nicht — er kann es, sobald ihn jemand '
       . 'einschaltet. Ein AKTIVER Ordner mit anderem Boden haelt den Lauf auch mit diesem Kreuz an.</span></p>';
    /* 07.09.: "aeltere Fassung" stand auf diesem Schirm zweimal und meinte
       zweimal etwas anderes — hier gegen den KNOTEN, in "Was sonst im
       Hangar liegt" gegen ein anderes PAKET. Dieselbe Familie wie H233:
       zwei Antworten unter einem Wort. Beide sagen es jetzt dazu. */
    echo '<p><label><input type="checkbox" name="turing_inst_rueckschritt" value="1"> '
       . '<strong>Ja, auch wenn ein Paket eine AELTERE Fassung bringt als die, die auf dem '
       . 'KNOTEN liegt.</strong></label><br>'
       . '<span style="color:#666;">Ein Rueckschritt ist nicht verboten. Er ist nur das Gegenteil dessen, '
       . 'was der Knopf verspricht. Gewaehlt wird er in der Fassungsliste der Kachel; der Lauf haelt dann '
       . 'an und nennt <em>beide</em> Zahlen, und dieses Kreuz laesst ihn durch.</span></p>';

    /* v3.9.6 — DIE ZEILE UEBER DEM KNOPF. Christians Entscheidung vom
       11.09.2026, und sie ist der Ersatz fuer das zweite Kaestchen.

       Sie steht hier und nicht bei den drei Kreuzen, weil sie KEINE Frage
       ist: niemand wird um Erlaubnis gebeten, es wird angekuendigt. Ein
       Kaestchen mehr haette bedeutet, dass man es auch weglassen kann —
       und „legen ohne einschalten" ist ein halber Satz (16a §6).

       Der erste Satz gilt unbedingt und ist gemessen (T21 FORDERUNG 9:
       drei gelegt, drei aktiviert). Die Zahl dahinter ist die, die vor dem
       Ankreuzen ueberhaupt wahr sein kann — siehe
       tu_inst_einschalten_zahl(). */
    $ein = turing_inst_einschalten_zahl($h['kacheln'], (array) ($h['nach_ordner'] ?? array()), $liegt);
    echo '<div class="notice notice-info inline" style="max-width:1000px;"><p>'
       . '<strong>Ein Lauf schaltet jedes Paket ein, das er aufspielt.</strong> '
       . '<span style="color:#666;">';
    if ($ein['nur_schalter']) {
        $z_ns = array();
        foreach ($ein['nur_schalter'] as $o3) { $z_ns[] = '<code>' . esc_html((string) $o3) . '</code>'; }
        echo 'Bei ' . (int) count($ein['nur_schalter']) . ' von ' . (int) $ein['waehlbar']
           . ' anwaehlbaren Kacheln ist das die einzige Wirkung, dort liegt dieselbe Fassung schon: ' . implode(', ', $z_ns) . '.'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- jedes Glied von $z_ns ist zwei Zeilen hoeher mit esc_html gebaut
    } elseif ($ein['waehlbar'] > 0) {
        echo 'Bei allen ' . (int) $ein['waehlbar'] . ' anwaehlbaren Kacheln wechseln auch Dateien.';
    } else {
        echo 'Anwaehlbar ist hier nichts.';
    }
    echo '</span></p></div>';

    echo '<p><button class="button button-primary button-hero" name="turing_inst_los" value="1"'
       . ($darf['darf'] ? '' : ' disabled') . '>Ausgewaehlten Satz aufspielen</button></p>';
    echo '</form>';

    /* ---- Was der Hangar sonst noch enthaelt (16b §5). Genannt, nicht verschwiegen. */
    if ($h['aeltere'] || $h['fremd'] || $h['kaputt']) {
        echo '<h3>Was sonst im Hangar liegt</h3><ul style="max-width:860px;list-style:disc;margin-left:1.4em;">';
        if ($h['aeltere']) {
            /* Bis 3.7.0 hiess es hier "sichtbar, nicht anklickbar", und das
               war eine Sackgasse. Und "aelter" meinte an dieser Stelle:
               aelter als ein anderes PAKET im Hangar — nicht aelter als
               das, was auf dem Knoten liegt. Beides steht jetzt da. */
            echo '<li><strong>' . count($h['aeltere']) . ' Paket(e), zu denen im Hangar eine hoehere '
               . 'Fassung liegt</strong> — waehlbar ueber die Fassungsliste der Kachel: '
               . '<code>' . esc_html(implode(', ', array_column($h['aeltere'], 'datei'))) . '</code></li>';
        }
        if ($h['fremd']) {
            echo '<li><strong>' . count($h['fremd']) . ' Paket(e) ohne Boden</strong> — gehoeren nicht zur '
               . 'Familie und werden nicht aufgespielt: <code>'
               . esc_html(implode(', ', array_column($h['fremd'], 'datei'))) . '</code></li>';
        }
        if ($h['kaputt']) {
            echo '<li><strong>' . count($h['kaputt']) . ' unlesbare(s) Archiv(e)</strong>: <code>'
               . esc_html(implode(', ', array_column($h['kaputt'], 'datei'))) . '</code></li>';
        }
        echo '</ul>';
    }

    /* ----------------------------------------------------------------
       H231b — TURING NENNT ALLE VIER VERZEICHNISSE.

       Bis 3.4.0 nannte es genau eines: den Hangar, aus dem es LIEST. In
       die anderen drei schreibt es — und nannte keines davon. Christian
       hat auf DEV nach dem Zwischenlager gesucht und es nicht gefunden;
       solange niemand weiss, wohin geschrieben wurde, ist auch
       "ist aufgeraeumt" nicht nachpruefbar.

           Ein Werkzeug, das in vier Verzeichnisse schreibt, muss alle
           vier nennen — nicht nur das eine, aus dem es liest.
       ---------------------------------------------------------------- */
    $plugins_pfad = turing_plugins_pfad();
    echo '<h3>Wo Turing liest und wohin es schreibt</h3>';
    echo '<table class="widefat striped" style="max-width:900px;"><tbody>';
    /* v3.9.11 — ein leeres <code></code> in einer Zeile namens „gelesen aus"
       ist eine Luecke, die wie eine Angabe aussieht. */
    $c1_hl2 = turing_hangar_lage();
    echo '<tr><td style="width:12em;"><strong>gelesen aus</strong></td><td><code>'
       . esc_html($lage['hangar'] !== '' ? (string) $lage['hangar'] : (string) $c1_hl2['gemeint'])
       . '</code><br><span style="color:#666;">der Hangar. '
       . ($lage['hangar'] !== ''
            ? 'Turing schreibt hier nie hinein.'
            : '<strong>GEMEINT, aber nicht benutzbar.</strong> ' . esc_html(turing_hangar_satz($c1_hl2)))
       . '</span></td></tr>';
    echo '<tr><td><strong>entpackt nach</strong></td><td><code>'
       . esc_html((string) $lage['tmp']) . '</code><br><span style="color:#666;">ein neues '
       . 'Verzeichnis je Lauf. Es verschwindet am Ende <em>jedes</em> Laufs — nach Erfolg '
       . 'und nach Halt.</span></td></tr>';
    echo '<tr><td><strong>beiseitegelegt nach</strong></td><td><code>'
       . esc_html($plugins_pfad . '/' . turing_inst_alt_name()) . '</code><br><span style="color:#666;">'
       . 'die Fassungen, die ersetzt werden. Nach einem gelungenen Lauf weg; solange etwas '
       . 'rot ist, bleiben sie liegen — sie sind der Weg zurueck.</span></td></tr>';
    echo '<tr><td><strong>Marke</strong></td><td><code>'
       . esc_html($plugins_pfad . '/' . turing_inst_marke_name()) . '</code><br><span style="color:#666;">'
       . 'meldet einen unvollstaendigen Vorlauf. Nach einem gelungenen Lauf weg.</span></td></tr>';
    echo '</tbody></table>';

    echo '<h3>Benannte Grenzen</h3><ul style="max-width:860px;list-style:disc;margin-left:1.4em;color:#555;">';
    echo '<li><strong>Der Pruefwert findet Beschaedigung, nicht Faelschung.</strong> Das Manifest liegt neben den '
       . 'Paketen; wer die Pakete schreiben kann, kann auch das Manifest schreiben.</li>';
    echo '<li><strong>Kein Manifest heisst NICHT GEMESSEN, nicht gruen.</strong> Dann steht bei jedem Paket UNBEKANNT, '
       . 'und das Aufspielen verlangt das Kreuz.</li>';
    echo '<li><strong>Turing aktualisiert sich nicht selbst.</strong> Eine neue Fassung kommt ueber die Plugin-Liste.</li>';
    /* H232 — dieser Absatz behauptete bis 3.4.0, die Zeile
       Cluster-One-Entfernt stehe "heute in keinem" Paket. Gemessen in den
       fuenf Zips von Welle 5: sie steht in fuenf von fuenf, turing.php
       selbst traegt sie auch, und die Tabelle darueber DRUCKT sie aus.
       Der Satz war einmal wahr und wurde nie wieder gemessen — und er
       redete ausgerechnet Paciolis Warnung klein.

           Ein Satz, der einmal wahr war und nie wieder gemessen wurde,
           ist keine benannte Grenze. Er ist ein Rest.

       Die Begruendung ist deshalb eine andere geworden, und sie haelt. */
    echo '<li><strong>Abschalten und Entfernen gibt es hier nicht.</strong> Was ein Paket beim Entfernen loescht, '
       . 'sagt seine Zeile in der Tabelle; nachmessen kann Turing das nicht. Entfernt wird ueber die Plugin-Liste.</li>';
    echo '</ul>';
}
}

/* ==========================================================================
   Die Seite: ein Kopf, fuenf Reiter. Der Installer steht vorn.
   ========================================================================== */

add_action('admin_menu', function () {
    /* 3.14.3 (28.10.2026, Neo): „Turing (CO)" im Menue unter Werkzeuge. Nemesus'
       Wort, ausdruecklich: anders als die Cluster-One-Plugins, aber verwandt –
       Turing hat nur Sinn, wenn man mindestens eines davon nutzt oder installieren
       will. 3.14.2 hatte einen Tag lang „Cluster One: Turing" (mein Vorschlag),
       bis 3.14.1 schlicht „Turing". */
    /* 3.14.8: 'turing_sehen' statt 'manage_options' – Mitglieder des Knotens sehen die Seite (Kopf der Datei, Runde 37). */
    add_management_page('Turing (CO)', 'Turing (CO)', 'turing_sehen', turing_seitenname(), 'turing_seite');
});

/** Ein Kopierfeld, zweimal gebraucht — deshalb einmal geschrieben. Die
   Kennungen sind je Feld verschieden; zwei Felder mit derselben id waeren
   eine zweite Wahrheit im HTML. */
if (! function_exists('turing_kopierfeld')) {
function turing_kopierfeld(string $id, string $inhalt, string $grenze, int $hoehe = 380): void {
    echo '<textarea readonly id="' . esc_attr($id) . '-ta" style="width:100%;max-width:960px;height:' . (int) $hoehe . 'px;font-family:monospace;font-size:12px;" onclick="this.select();">' . esc_textarea($inhalt) . '</textarea>';
    echo '<p><button type="button" class="button button-primary" id="' . esc_attr($id) . '-copy">In die Zwischenablage</button>';
    echo '<span id="' . esc_attr($id) . '-ok" style="display:none;color:#1a7f37;margin-left:.6em;">Kopiert.</span></p>';
    /* v3.12.0, Posten 4 — DIE GRENZE STEHT AM KNOPF.

       Ein Bericht hinter 'manage_options' ist geschuetzt. Ein Bericht mit
       einem Knopf, der ihn kopierfertig macht, ist fuer die Reise gebaut —
       und dann entscheidet nicht die Rechtepruefung, wohin er geht.
           Eine benannte Grenze am Gegenstand ist billiger als jede Regel
           ueber den Leser.
       KEIN VERBOT: ohne den Unterbau waere der Bericht wertlos, und ohne die
       Konfiguration der Dump.

       WARUM DER SATZ EIN ARGUMENT IST UND NICHT HIER FEST STEHT: die zwei
       Felder tragen Verschiedenes. Der Pacioli-Bericht nennt den Unterbau mit
       Fassungen und die Adresse des Knotens; der Spiegel-Dump nennt davon
       nichts und dafuer die gespeicherte Konfiguration. Ein Satz, der an
       beiden derselbe waere, waere an einem von beiden falsch.
           Eine Grenze, die an zwei Gegenstaenden dieselbe ist, ist an einem
           von beiden eine Behauptung.
       $grenze hat darum KEINE Vorgabe: wer ein drittes Kopierfeld baut, muss
       sagen, was darin reist. */
    echo '<p style="max-width:960px;color:#8a6d3b;margin:.2em 0 1.4em;"><strong>Grenze dieses Feldes:</strong> '
       . esc_html($grenze) . ' Er ist fuer die <strong>Werkbank</strong> bestimmt. Nicht in ein Forum, '
       . 'nicht in ein oeffentliches Ticket.</p>';
    ?>
    <script>
    (function () {
        var b = document.getElementById(<?php echo wp_json_encode($id . '-copy'); ?>);
        if (!b) { return; }
        b.addEventListener('click', function () {
            var ta = document.getElementById(<?php echo wp_json_encode($id . '-ta'); ?>);
            var done = function () {
                var ok = document.getElementById(<?php echo wp_json_encode($id . '-ok'); ?>);
                if (ok) { ok.style.display = 'inline'; setTimeout(function () { ok.style.display = 'none'; }, 2500); }
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(ta.value).then(done, function () { ta.select(); document.execCommand('copy'); done(); });
            } else { ta.select(); document.execCommand('copy'); done(); }
        });
    })();
    </script>
    <?php
}
}

/* 20.10.2026 (Neo) — DIE FRAGEZEICHEN-BLASE, TURINGS EIGENE.
   Dieselbe Form wie cluster_one_help_bubble() im Boden, aber ohne ihn: Turing
   traegt keinen Boden und soll keinen tragen. Die langen Einleitungen der
   Reiter stehen seit heute hier drin statt ueber den Tabellen. */
if (! function_exists('turing_blase')) {
function turing_blase(string $text): string {
    return '<details style="display:inline-block;vertical-align:middle;margin-left:.5em;font-weight:400;">'
         . '<summary style="cursor:pointer;list-style:none;display:inline-block;width:1.5em;height:1.5em;line-height:1.5em;'
         . 'text-align:center;border:1px solid #c3c4c7;border-radius:50%;font-size:.7em;opacity:.75;"'
         . ' title="Was ist das?">?</summary>'
         . '<div style="display:block;max-width:760px;margin:.8em 0 .4em;padding:.9em 1.1em;border:1px solid #dcdcde;'
         . 'border-radius:4px;background:#fff;font-size:.92em;line-height:1.6;font-weight:400;">'
         . wpautop(esc_html($text))
         . '</div></details>';
}
}

/* ==========================================================================
   4.0.0: DREI NAMEN, DIE DER SCHNITT SETZT — und eine Tuer.
   turing.php ist EINE Quelle, aus der der Bau zwei Plugins schneidet: Turing Diagnostics
   (Kasper, Spiegel, Pacioli-Zeuge; Verzeichnis) und Turing Installer (GitHub). Was sich
   zwischen beiden unterscheidet, steht in diesen Funktionen und im Dateikopf — nirgends
   sonst. In dieser Datei (das ganze Werkzeug, vier Reiter) gelten die Werte der Quelle.
   ========================================================================== */
if (! function_exists('turing_seitenname')) {
/** Der Seiten-Slug unter Werkzeuge; EINE Quelle fuer Menue, Reiterlinks und fremde Tueren. */
function turing_seitenname(): string { return 'cluster-one-turing'; }   /* 4.0.1: = Ordnername */
}
if (! function_exists('turing_geschwister_organ')) {
/** Das Organ, unter dem das andere Turing-Werkzeug sich bei Kasper meldet; '' = es gibt keines. */
function turing_geschwister_organ(): string { return ''; }
}
if (! function_exists('turing_zeuge_url')) {
/** 4.0.0: die Tuer fuer Pacioli — Turing nennt die Adresse seines Zeugen selbst. Pacioli 0.9.0 hatte
    sie abgeschrieben (page=turing) und mit der Teilung waere sie falsch geworden; ein Verweis, der aus
    dem Haus fuehrt, kommt aus dem fremden Haus (v3.9.4, Kasper-Reiter). */
function turing_zeuge_url(): string { return admin_url('tools.php?page=' . turing_seitenname() . '&reiter=zeuge'); }
}

if (! function_exists('turing_seite')) {
function turing_seite(): void {
    /* 3.14.8 (Runde 37): hier stand `if (! current_user_can('manage_options')) { return; }`.
       Sehen duerfen Mitglieder; was sie nicht tun duerfen, sagt jeder Reiter an der Stelle des Knopfes. */
    if (! current_user_can('turing_sehen')) { return; }
    /* 4.0.0: die ganze Seite wird gepuffert. Der Durchgang des Zeugen ruft nach der Ueberschrift vier
       Anbieter; waehrend das laeuft, ist die Hilfeblase (<details>) erst halb beim Browser, und Chrome
       zeichnet ihr Standardwort „Details in die Ueberschrift (Nemesus' Fund, 02.11.; dieselbe Ursache
       wie faust.php in Runde 27a). Erst fertig bauen, dann ausliefern. */
    ob_start();

    /* H233 — die erste Lasche und die vorgewaehlte sind jetzt dieselbe.
       Bis 3.4.0 stand hier 'zeuge': ein Rest der eigenen Geschichte.
       Turing begann als Pacioli-Zeuge, der Installer kam mit 3.2.0
       dazu und wurde nach vorn gestellt — die Vorwahl blieb, wo sie
       war. Wer Turing heute aufruft, will den Installer.

           Eine Ordnungszahl, die dreimal im Quelltext steht und
           einmal auf dem Schirm, ist dreimal abgeschrieben und
           einmal gemessen. */
    /* 07.09.: bei der Messung der Zeilennummern fuer Mammut gefunden — die
       Ruecknahme fuer einen UNGUELTIGEN Wert stand noch auf 'zeuge'. Also
       zwei Vorwahlen: eine fuer "nichts angegeben", eine fuer "Unsinn
       angegeben", und sie sagten Verschiedenes. Die Vorwahl steht jetzt
       EINMAL da; beide Wege lesen dieselbe Zeichenkette. */
    $vorwahl = 'installer';
    /* 20.10.2026 (Neo): 'neumann' ist aus der Liste gefallen — der Reiter war
       leer (siehe den Kommentar bei tu_seite_neumann). Vier Reiter, in der
       Reihenfolge, in der man sie braucht: aufspielen, nachsehen, ausgeben,
       messen. */
    /* 4.0.5 (Ypsilon): fuenfter Reiter „Feynman", als letzter — Nemesus' Platz, wie in den Haeusern. */
    $laschen = array('installer', 'kasper', 'spiegel', 'zeuge', 'feynman');

    $reiter = isset($_GET['reiter']) ? sanitize_key(wp_unslash($_GET['reiter'])) : $vorwahl; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    if (! in_array($reiter, $laschen, true)) { $reiter = $vorwahl; }

    /* H234 — derselbe Titel auf beiden Schirmen. Turing haengt absichtlich
       NICHT unter dem Dach (Vertrag 21 §2a), sondern unter Werkzeuge; damit
       fehlt ihm der Ort, an dem sonst die Familie steht. Die Ueberschrift
       ist der einzige, der uebrig ist — und sie baut kein zweites Dach. */
    /* 3.14.4 (28.10.2026, Neo): „Turing 3.14.4 (Cluster One)" statt „Cluster One: Turing 3.14.4"
       – Nemesus: der Name zuerst, die Familie in Klammern dahinter, wie im Menue „Turing (CO)".
       3.14.5: die Klammer in Ueberschriftgroesse, nur die Nummer klein (Nemesus). */
    echo '<div class="wrap"><h1>Turing <span style="font-size:13px;font-weight:400;color:#666;">'
       . esc_html(turing_version()) . '</span> (Cluster One)'
       . turing_blase('Turing ist das Werkzeug neben der Cluster-One-Suite: Installer und Pruefer in einem. ' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tu_blase() gibt fertiges HTML zurueck (wpautop(esc_html) im Innern)
           . 'Es spielt die Pakete als Satz auf, nicht als Stuecke, und fasst den Knoten erst an, wenn der ganze Satz zusammenpasst. '
           . 'Es traegt mit Absicht keinen Boden und schreibt keine Option: was es diagnostiziert, kann es nicht heimlich veraendern. '
           . 'Benannt nach Alan Turing (1912–1954), der eine Maschine von aussen befragte, ohne sie aufzuschrauben: nicht raten, was drinnen geschieht, sondern es an dem ablesen, was herauskommt.')
       . '</h1>';

    echo '<h2 class="nav-tab-wrapper">';
    foreach (array('installer' => 'Installer', 'kasper' => 'Kasper', 'spiegel' => 'Spiegel', 'zeuge' => 'Pacioli-Zeuge', 'feynman' => 'Feynman') as $s => $titel) {
        printf(
            '<a href="%s" class="nav-tab%s">%s</a>',
            esc_url(admin_url('tools.php?page=' . turing_seitenname() . '&reiter=' . $s)),
            $reiter === $s ? ' nav-tab-active' : '',
            esc_html($titel)
        );
    }
    echo '</h2>';

    /* 3.14.8: der eine Satz fuer den, der liest und nicht tut. Einmal oben, nicht an jedem Knopf noch einmal. */
    if (! turing_darf_tun()) {
        echo '<div class="notice notice-info inline" style="margin:.8em 0;"><p><strong>Du siehst Turing lesend.</strong> '
           . 'Aufspielen, Spiegel zeigen und Durchgang starten sind Sache des SysOps; die Reiter zeigen, was ohne Knopf zu sehen ist.</p></div>';
    }

    if ($reiter === 'installer') {
        turing_seite_installer();
    } elseif ($reiter === 'spiegel') {
        turing_seite_spiegel();
    } elseif ($reiter === 'kasper') {
        turing_seite_kasper();
    } elseif ($reiter === 'feynman') {
        turing_seite_feynman();
    } else {
        turing_seite_zeuge();
    }

    echo '</div>';
    ob_end_flush();
}
}

/* --------------------------------------------------------------------------
   REITER 5 — FEYNMAN. 4.0.5 (03.11.2026 mittags, Neo), Runde Ypsilon.

   Nemesus: „Immerhin waere es moeglich, dass jemand das Ding zuerst installiert. Beim Turing mit Installer von
   GitHub ist das sogar wahrscheinlich. Und dann soll der User sofort wissen, worum es geht. Und spaeter auf WP-Base:
   Es waere moeglich. Und dann soll der User wissen, was man mit Turing testen kann."

   Darum traegt Turing den Text SELBST und verweist nicht nur: ohne Boden gibt es auf dem Knoten keine Feynman-Seite,
   auf die ein Verweis zeigen koennte. Drei Abschnitte — was Cluster One ist, was Turing prueft, die Hausordnung.
   why.txt reist mit Turing (sechste und siebte Kopie; der Bau prueft sie Byte fuer Byte gegen die fuenf Haeuser).
   Liegt ein Haus, zeigt Turing DESSEN Text — den bekommen auch die KIs dort; liegt keines, die eigene Kopie, und
   sagt es. Zwei Klinken, eine Tuer. KEIN ZWEITER MARKDOWN-UEBERSETZER: Turing traegt keinen Boden, die Datei heisst
   .txt und erscheint als Text — ein zweiter Uebersetzer waere eine zweite Wahrheit ueber dieselbe Datei.
   Der Verweis ins Haus steht nur, wenn ein Boden-Paket Feynmans Seiten angemeldet hat (dieselben drei Riegel wie
   Pacioli): kein Weg ins Leere. Letzter Reiter, wie in den Haeusern (Nemesus, 03.11.). Rein lesend.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_seite_feynman')) {
function turing_seite_feynman(): void {
    echo '<h2 style="margin-top:.4em;">Feynman'
       . turing_blase('Der Lernpfad: was Cluster One ist, was Turing daran prueft, und die Hausordnung, die auch die KIs bekommen. ' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- turing_blase() gibt fertiges HTML zurueck (wpautop(esc_html) im Innern)
           . 'Benannt nach Richard Feynman (1918–1988), der erklaerte, bis es jemand verstand, der es nicht schon wusste. Rein lesend: hier gibt es keinen Knopf.')
       . '</h2>';

    echo '<h3>Was Cluster One ist</h3>';
    echo '<p style="max-width:820px;">Cluster One ist die Bauhuette fuer CLU573R$: das Geruest fuer ein foederiertes Netz, in dem Menschen und KIs gleichberechtigt zusammenkommen. '
       . 'Fuenf WordPress-Plugins, die einander kennen und einzeln laufen: <strong>Kleinrock</strong> (das Mesh — Knoten paaren, Freunde, was zwischen Knoten reist), '
       . '<strong>Bismarck</strong> (Raeume, Projekte und Kalender, in denen Menschen und KIs miteinander reden), '
       . '<strong>Pacioli</strong> (jede KI-Kennung schickt nur den Schluessel ihres eigenen Besitzers; in der Optionstabelle steht nie ein echter), '
       . '<strong>Crockford</strong> (der Kontext der Seite fuer die KIs, aus Bricks gelesen) und <strong>Post Dolly</strong> (Zitate und Ansagen des Knotens). '
       . 'Gemeinsam ist ihnen der Boden, der in jedem Paket mitreist. Alles heisst nach Menschen; warum, steht in der Namenstabelle des Hauses (Feynman › Derrida).</p>';

    echo '<h3>Was Turing prueft</h3><ul style="max-width:820px;list-style:disc;margin-left:1.4em;">';
    echo '<li><strong>Installer</strong> — spielt die Pakete als Satz auf, nicht als Stuecke: entpacken, pruefen, den Knoten erst anfassen, wenn der ganze Satz zusammenpasst. Nur in dieser GitHub-Fassung.</li>';   /* Installer — nur in der Quelle; der Schnitt nimmt diese Zeile heraus */
    echo '<li><strong>Kasper</strong> — welche Pakete auf diesem Knoten liegen und welchen Boden jedes traegt; welches Organ aus welchem Plugin geantwortet hat und in welcher Reihenfolge WordPress laedt; welche Datei in welchen Paketen liegt und ob die Kopien gleich sind.</li>';
    echo '<li><strong>Spiegel</strong> — der gespeicherte Zustand von Cluster One ohne seinen Inhalt: Struktur, Laengen und gesalzene Abdruecke statt Texte. Laeuft nur auf Knopfdruck.</li>';
    echo '<li><strong>Pacioli-Zeuge</strong> — die eine Frage, die Pacioli ueber sich selbst nicht beantworten kann: kommt der Schluessel, der den Server verlaesst, aus einer Optionszeile? Gemessen am tatsaechlichen Request; kein Prompt verlaesst das Haus, kein Schluessel wird gezeigt.</li>';
    echo '</ul>';
    echo '<p style="max-width:820px;color:#8a6d3b;">Turing traegt mit Absicht keinen Boden und schreibt keine Option: was es diagnostiziert, kann es nicht heimlich veraendern. Seine Berichte nennen den Knoten — sie gehoeren dorthin, wo die Leute des Knotens lesen, nicht in ein Forum und nicht in ein oeffentliches Ticket.</p>';

    if (function_exists('cluster_one_menue_wenn') && function_exists('cluster_one_feynman_seiten')
        && function_exists('cluster_one_organ_wirt') && cluster_one_organ_wirt('feynman') !== '') {
        echo '<p><a class="button" href="' . esc_url(admin_url('admin.php?page=cluster_one_about')) . '">Feynman im Haus oeffnen</a> '
           . '<span style="color:#666;">Namenstabelle, Ueber Cluster One, Warum, Nemesus — in der Leiste jedes Hauses der letzte Reiter.</span></p>';
    } else {
        echo '<p style="max-width:820px;color:#666;">Auf diesem Knoten liegt kein Cluster-One-Paket, das Feynmans Seiten anmeldet. Sobald eines liegt, steht Feynman als letzter Reiter in dessen Leiste — mit Namenstabelle und allem.</p>';
    }

    /* Die Hausordnung: aus dem Haus, wenn eines laeuft (derselbe Text, den die KIs bekommen); sonst Turings eigene Kopie. */
    $c1_quelle = ''; $c1_text = '';
    if (function_exists('cluster_one_why_text')) {
        $c1_text = (string) cluster_one_why_text(); $c1_quelle = 'haus';
    }
    if ($c1_text === '') {
        $c1_pfad = __DIR__ . '/why.txt';
        if (is_readable($c1_pfad)) {
            $c1_roh = file_get_contents($c1_pfad); $c1_text = is_string($c1_roh) ? $c1_roh : ''; $c1_quelle = 'eigen';
        }
    }
    $c1_text = (string) preg_replace('/<!--.*?-->/s', '', $c1_text);   /* Markierungen (AI-BRIEFING) sind nichts fuer Augen — wie im Haus */
    echo '<hr><h3>Die Hausordnung (why.txt)</h3>';
    if (trim($c1_text) === '') {
        echo '<p>why.txt fehlt — hier wie im Haus. Lieber nichts anzeigen als etwas Erfundenes.</p>';
        return;
    }
    echo '<p style="max-width:820px;color:#666;">'
       . ($c1_quelle === 'haus'
            ? 'Gelesen aus dem Haus, das auf diesem Knoten laeuft — derselbe Text, den die KIs hier bekommen.'
            : 'Gelesen aus Turings eigener Kopie; sie ist Byte fuer Byte die der fuenf Pakete (geprueft beim Bau). Liegt ein Haus, zeigt Turing dessen Text.')
       . ' Zweisprachig: Deutsch zuerst, Englisch darunter. Als Text, wie die Datei heisst.</p>';
    echo '<pre style="max-width:960px;white-space:pre-wrap;font-family:inherit;font-size:.95em;line-height:1.55;background:#fff;border:1px solid #dcdcde;padding:1em 1.2em;">' . esc_html($c1_text) . '</pre>';
}
}
/* --------------------------------------------------------------------------
   REITER 3 — KASPER. Wer meldet sich, und aus welchem Plugin?

   v3.1.1 — DIESE TEILUNG GIBT ES NICHT MEHR. Galvani ist am 27.08.2026
   zerfallen: die Schalter starben, als das letzte Organ Mammuts in ein Light
   gezogen war, und die Anzeige heisst jetzt Landkarte und wohnt in Umberto.
   Turing und die Landkarte zeigen damit dasselbe Bild aus zwei Plugins — kein
   Fehler, aber eine offene Frage. Der Absatz darunter beschreibt den Stand von
   gestern und bleibt stehen, weil er erklaert, warum es diese Seite gibt.

   TURING SIEHT, GALVANI SCHALTET. Das ist die Teilung, auf die Christian und
   ich am 25.08.2026 gekommen sind, nachdem er gefragt hatte, ob man Galvani
   nicht an Turing haengen koenne.

   Technisch ginge das sofort — Galvani ist nur eine Option, die Mammut liest,
   und jedes Plugin koennte sie schreiben. Genau daran scheitert es: ein
   update_option in Turing macht aus seiner geprueften Null einen Vorsatz. Und
   eine Diagnose, die den Zustand aendern kann, den sie diagnostiziert, ist
   keine.

   Also: die ANZEIGE zieht hierher, die SCHALTER bleiben in Mammut. Was Turing
   dadurch gewinnt, ist mehr als eine Tabelle — er zeigt sie auf JEDEM Knoten,
   auch auf einem, auf dem Mammuts Werkzeugseite gar nicht erscheint.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_seite_kasper')) {
function turing_seite_kasper(): void {
    echo '<h2 style="margin-top:.4em;">Kasper'
       . turing_blase('Kaspers Frage „Seid ihr alle da?", gestellt beim Aufbau dieser Seite: welches Organ hat sich aus welchem Plugin gemeldet, ' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tu_blase() gibt fertiges HTML zurueck (wpautop(esc_html) im Innern)
           . 'und in welcher Reihenfolge laedt WordPress die Plugins. Gezaehlt werden Anmeldungen; eine Kopie, die hinter ihrem Riegel schweigt, steht hier nicht. '
           . 'Dasselbe Organ aus mehreren Paketen ist der Normalfall: der Boden reist in jedem Paket mit, und es laeuft die zuerst geladene Kopie. '
           . 'Turing liest hier nur. '
           /* 20.10.2026 (Neo), 3.14.0: hier stand „Die Tabellen zur Verdraengung, mit
              Dateivergleich, stehen in der Landkarte." Sie stehen jetzt unten auf
              diesem Reiter (Entscheidung Nemesus/Neo: Turing misst Pakete, Kasper
              Organe, die Landkarte die Dateien — also gehoert sie hierher). */
           . 'Darunter, von der Platte gelesen: welche Datei in welchen Paketen liegt und ob die Kopien gleich sind — '
           . 'bis Turing 3.13.0 die Landkarte in Crockford.')
       . '</h2>';
    turing_stempel();
    /* 4.0.0: „Was auf diesem Knoten liegt auch hier — der Pruefer steht ab jetzt allein auf drei von
       vier Knoten, und die Fassungen muessen ohne Installer ablesbar sein (Nemesus' Tabelle nach jedem
       Aufspielen). Dieselbe Funktion wie im Installer-Reiter, siehe turing_lage_tabellen(). */
    turing_lage_tabellen(turing_liegt(), turing_hand_familie_am_knoten());

    /* --------------------------------------------------------------------
       v3.9.4 — HIER STAND EINE ADRESSE, UND SIE WAR ZWEI UMZUEGE ALT.

       WAS HIER STAND:
           „Dieselbe Frage, mit den Tabellen zur Verdraengung, steht in
            Cluster One unter Werkzeuge → Cluster One."
       und weiter unten:
           „… dann steht die Zahl dazu in der Landkarte (Werkzeuge →
            Cluster One)."

       Die Landkarte haengt dort nicht mehr: sie meldet sich seit
       VERTRAG 17 §3 als add_submenu_page(null, …) mit dem Slug
       cluster_one_landkarte an, sichtbar allein ueber die Reiterleiste im
       Kranz — und der Kranz ist schon bei 0.4.2 aus den Werkzeugen
       ausgezogen. Christian hat die Seite gesucht, nicht gefunden und
       daraus geschlossen, Umberto haenge noch am Monolithen. Er ist aktiv
       und aktuell. Eine falsche Adresse hat einen Menschen zu einem
       falschen Befund gefuehrt; gemeldet hat es Mammut am 10.09.2026.

       UND ES IST DAS ZWEITE MAL AN DIESER FUNKTION. v3.1.1 hat hier
       „Werkzeuge → Galvani" gestrichen, weil Galvani zerfallen war —
       gefunden hat es damals auch ein Mensch, der die Seite aufrief. Die
       Reparatur hat eine Adresse gegen eine andere getauscht.

           Wer eine Adresse gegen eine andere Adresse tauscht, hat den
           Fund verschoben und nicht geschlossen.

       DESHALB DRUCKT TURING KEINEN MENUEPFAD MEHR. Ein Menuepfad ist eine
       Behauptung ueber die Anmeldungen eines fremden Hauses, und es gibt
       in diesem Haus keine Daten, an denen sie sich pruefen liesse. Was
       es gibt: den SLUG — und WordPress selbst kann sagen, ob es ihn
       kennt. Dazu den Boden, den Turing ohnehin liest:
       cluster_one_modul_da('landkarte').

           Ein Verweis, der aus dem Haus fuehrt, muss aus dem fremden
           Haus kommen.

       Vier Lagen, jede sagt, was gemessen wurde. T19 WELT G bis J.

       v3.9.8 — UND HIER STAND DER NAECHSTE FALSCHE SATZ, ZWEI ZEILEN UEBER
       DIESEM. Er hiess:

           „… und WordPress selbst kann sagen, ob es ihn kennt
            (menu_page_url() gibt '' zurueck, wenn nicht)."

       Das „wenn nicht" war nie gemessen. menu_page_url() gibt auch dann ''
       zurueck, wenn WordPress die Seite sehr wohl kennt, sie aber VERSTECKT
       angemeldet ist — und so meldet sich genau diese Landkarte an:

           add_submenu_page(null, …)  ->  $_parent_pages[$slug] = null
           menu_page_url()            ->  isset($_parent_pages[$slug])
           isset(null) ist false      ->  ''

       Gemessen von Mammut am echten Kern (W66), von mir gegen 7.0, 7.1 und
       7.2-alpha nachgefahren, dreimal dasselbe. Christian hat die Seite
       inzwischen unter admin.php?page=cluster_one_landkarte geladen — sie
       ist erreichbar, und dieses Haus hat „nicht verlinkbar" gesagt.

           Wer eine fremde Auskunft weitergibt, haftet fuer sie. Eine
           Luecke, die ich von einer fremden Funktion uebernehme, ist ab
           der Uebernahme meine.

       DAS IST DIESELBE FORM WIE 3.9.4 UND WIE 3.9.2, zum dritten Mal an
       derselben Funktion: ein Satz ueber ein fremdes Haus, den dieses Haus
       nicht nachgemessen hat. Diesmal ist die Reparatur keine andere
       Formulierung, sondern eine andere FRAGE — tu_hand_seitenlage() fragt
       die Register selbst, mit array_key_exists statt isset, und nennt
       beide Tore.
       -------------------------------------------------------------------- */
    /* 20.10.2026 (Neo), 3.14.0 — DIE LANDKARTE IST HIER, UND DER VERWEIS FAELLT.
       Bis 3.13.0 stand hier, aus tu_hand_seitenlage('cluster_one_landkarte')
       und cluster_one_modul_da('landkarte'), ein Satz je Lage:
           „Die Tabellen zur Verdraengung stehen auf der Landkarte."
           „Eine Landkarte ist angemeldet, aber nicht erwiesen erreichbar:
            eines ihrer beiden Tore sagt nein."
           „Auf diesem Knoten meldet sich keine Landkarte."
           „Ob es hier eine Landkarte gibt, ist nicht feststellbar."
       Seit Crockford 0.5.17 gibt es die Seite nicht mehr; ihre Tabelle steht
       unten auf diesem Reiter (tu_seite_kasper_dateien). Die Messung bleibt
       als UEBERGANGSHINWEIS: ein Knoten mit aelterem Crockford traegt die
       Seite noch, und dann sagt Turing das, statt zwei Tabellen stumm
       nebeneinander zu stellen. Meldet sich keine, steht hier nichts. */
    $lk_m   = turing_hand_seitenlage('cluster_one_landkarte');
    $lk_url = (string) $lk_m['url'] !== '' ? (string) $lk_m['url'] : (string) $lk_m['hand_url'];
    if (in_array((string) $lk_m['lage'], array('aufgeloest', 'versteckt_offen', 'versteckt_halb', 'angemeldet_ohne_url'), true)) {
        echo '<p style="max-width:820px;color:#996800;">Auf diesem Knoten ist noch eine '
           . ($lk_url !== '' ? '<a href="' . esc_url($lk_url) . '">Landkarte</a>' : 'Landkarte')
           . ' angemeldet (Crockford vor 0.5.17). Die Dateitabelle unten zeigt dasselbe aus Turing; '
           . 'die Seite faellt mit dem naechsten Crockford.</p>';
    }

    /* --------------------------------------------------------------------
       v3.9.5 — EIN NAME IST KEIN KNOTEN.

       WAS HIER STAND:
           if (! function_exists('cluster_one_kasper')) {
               „Cluster One ist auf diesem Knoten nicht installiert."
           }

       Das ist ein Schluss ueber ein FREMDES HAUS, gezogen aus EINEM Namen
       — und Turing haelt einen zweiten, der ihm widersprechen kann:
       CLUSTER_ONE_BODEN_FASSUNG, gelesen in tu_boden_am_knoten(). Ist die
       Konstante gesetzt, laeuft hier ein Boden, und „nicht installiert"
       ist nachweislich falsch.

           Ein Waechter, der schweigt, sagt etwas ueber MICH und nichts
           ueber das fremde Haus. Wer aus einem Namen auf einen Knoten
           schliesst, hat den Namen fuer den Knoten genommen.

       Das ist Mammuts Punkt 5, eine Etage groesser als ich ihn gestern
       geschrieben habe: es ist nicht ein Slug, es ist die ganze NAHT. Die
       Namen des fremden Hauses liegen bei mir als Zeichenketten; gefragt
       werden sie alle (T22 zaehlt es nach) — aber an genau einer Stelle
       wurde aus dem Schweigen eines Waechters ein Urteil.

       Und auch der stumme Fall heisst nicht mehr „nicht installiert":
       liegen und laufen sind zwei Dinge (Vertrag 22 §2), und diese Seite
       liest nur, was LAEUFT.
       -------------------------------------------------------------------- */
    /* 20.10.2026 (Neo), 3.14.0: bis heute standen in den zwei Zweigen hier je
       ein `return;` — ohne Kasper endete der Reiter. Die Dateitabelle unten
       liest die Platte und braucht keinen Kasper. Deshalb laeuft die
       Laufzeit-Haelfte jetzt als eigene Funktion (tu_seite_kasper_laufzeit,
       Inhalt unveraendert), und der Reiter geht danach weiter. */
    $alle = array();
    if (! function_exists('cluster_one_kasper')) {
        $boden_fassung = turing_boden_am_knoten();
        if ($boden_fassung !== '') {
            echo '<div class="notice notice-warning inline"><p><strong>Ein Boden laeuft (' . esc_html($boden_fassung)
               . '), aber Kasper antwortet nicht.</strong> Moeglich ist, dass <code>kasper.php</code> in dieser Fassung '
               . 'nicht mitlaeuft oder dass diese Seite vor dem Laden gebaut wurde.</p></div>';
        } else {
            echo '<div class="notice notice-info inline"><p><strong>Kein laufendes Cluster One feststellbar.</strong> '
               . 'Ob eines liegt, steht im Installer. Turing bringt Kasper absichtlich nicht mit.</p></div>';
        }
    } else {
        $alle = cluster_one_kasper();
        if (! $alle) {
            echo '<div class="notice notice-warning inline"><p><strong>Niemand meldet sich.</strong> '
               . 'Kasper ist da, aber die Liste ist leer.</p></div>';
        }
    }

    if ($alle) { turing_seite_kasper_laufzeit($alle); }

    /* Der Zaehler aus kasper.php, bis heute nur auf der Landkarte gedruckt:
       wie oft Kasper vor plugins_loaded gefragt wurde. Eine WordPress-Option,
       keine Boden-Funktion. Null heisst: kein Aufruf kam zu frueh — nicht,
       dass es keinen geben kann. Gedruckt nur, wenn es einen gab. */
    $frueh = function_exists('get_option') ? (int) get_option('cluster_one_kasper_zu_frueh', 0) : 0;
    if ($frueh > 0) {
        echo '<div class="notice notice-warning inline"><p><strong>Kasper wurde ' . $frueh . ' Mal vor <code>plugins_loaded</code> gefragt.</strong> ' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $frueh ist zwei Zeilen hoeher (int)
           . 'Die Antwort war eine halbe Liste, die vollstaendig aussieht. Die Zahl steht hier, weil <code>_doing_it_wrong</code> '
           . 'an <code>WP_DEBUG</code> haengt und auf dem Live-Knoten niemand sie saehe.</p></div>';
    }

    turing_seite_kasper_dateien($alle);
}
}

if (! function_exists('turing_seite_kasper_laufzeit')) {
/** Die Laufzeit-Haelfte des Kasper-Reiters: wer hat sich gemeldet, aus
    welchem Wirt, in welcher Reihenfolge. Bis 3.13.0 der Rumpf von
    tu_seite_kasper(); unveraendert hierher gezogen, siehe dort. */
function turing_seite_kasper_laufzeit(array $alle): void {
    /* Nach WIRT gruppiert.

       v3.9.7 — DIESER SATZ WAR BIS HEUTE FALSCH GEWORDEN, OHNE DASS SICH
       EINE ZEILE CODE GEAENDERT HAT. Hier stand:

           „Der Wirt ist der Plugin-Ordner, aus dem sich das Organ
            gemeldet hat."

       Seit dem Zug vom 11.09.2026 ist 'wirt' der Wirt der Kopie, die
       DEKLARIERT hat — nicht
       der des ersten Melders. Mammut hat die stille Unrichtigkeit in seinem
       eigenen Haus gefunden (faust.php:61-78): die zwei sind MEISTENS
       dieselbe Kopie, und genau deshalb ist der Unterschied nie
       aufgefallen. Sie fallen auseinander, wenn eine hoehere Fassung
       spaeter laedt.

           Eine Form, die sich aendert, bricht laut. Eine Bedeutung, die
           sich aendert, bricht still — und zwar in der Prosa des Lesers,
           nicht in seinem Code.

       Mein Messgeraet von gestern haette das NICHT gefunden: es hat die
       Form gemessen (wird aus dem Paar eine Liste?) und nicht den Sinn.
       Die Typen sind gleich geblieben, also war alles gruen.

       Kasper liest den Wirt aus dem Aufrufstapel, er wird nirgends
       eingetragen. Auf einem Knoten mit mehreren Cluster-One-Plugins ist
       das die einzige Stelle, an der man sieht, WER was stellt. */
    $karte = array();
    foreach ($alle as $organ => $e) {
        $karte[(string) ($e['wirt'] ?? '')][$organ] = $e;
    }
    ksort($karte);

    echo '<table class="widefat striped" style="max-width:900px;"><thead><tr>'
       . '<th style="width:230px;">Wirt (Plugin-Ordner), der DEKLARIERT hat</th>'
       . '<th>Organe</th></tr></thead><tbody>';
    /* 3.14.1 (27.10.2026, Neo) — DIE WERFT. Das Produktregister (Boden 1.17.3)
       traegt zwei neue Felder: 'werft' (wer gebaut hat) und 'boden' (gegen
       welchen Boden). Beides ist ERKLAERUNG, kein Beweis – jeder kann die
       Zeichenkette schreiben. Echt unterscheidbar ist nur der Boden selbst,
       und das tut die Dateitabelle unten: gleicher Hash, gleicher Boden.
       Werft sagt „von wem", der Hash sagt „worauf". Fehlt die Angabe, steht
       es da – sichtbar, ohne Strafe: ein fremdes Plugin ist kein Fehler.
       Ein Werkzeug (turing) steht nicht im Register und bekommt keine Zeile. */
    $produkte = function_exists('cluster_one_produkte') ? cluster_one_produkte() : array();
    if (!is_array($produkte)) { $produkte = array(); }
    foreach ($karte as $wirt => $organe) {
        ksort($organe);
        $teile = array();
        foreach ($organe as $organ => $e) {
            $teile[] = '<code>' . esc_html((string) $organ . ' ' . (string) ($e['fassung'] ?? '')) . '</code>';
        }
        $zeig = (string) $wirt !== '' ? esc_html((string) $wirt) : '<em>(Wirt nicht feststellbar)</em>';
        if (isset($produkte[(string) $wirt]) && is_array($produkte[(string) $wirt])) {
            $pw = (string) ($produkte[(string) $wirt]['werft'] ?? '');
            $pb = (string) ($produkte[(string) $wirt]['boden'] ?? '');
            $zeig .= '<br><span style="color:#666;font-weight:normal;">Werft: ' . ($pw !== '' ? esc_html($pw) : '<em>unbekannt</em>')
                   . ($pb !== '' ? ' · gebaut auf Boden ' . esc_html($pb) : '') . '</span>';
        }
        echo '<tr><td><strong>' . $zeig . '</strong> <span style="color:#666;">(' . (int) count($organe) . ')</span></td>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $zeig ist oben (Zeile ~4384-4389) aus esc_html und Literalen gebaut
        echo '<td>' . implode(', ', $teile) . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- jedes Glied von $teile ist in der Schleife oben mit esc_html gebaut
    }
    echo '</tbody></table>';
    /* v3.9.3 — die benannte Grenze, Mammuts Wortlaut vom 07.09. Sie steht
       UNBEDINGT da, auch wenn keine Doppelung gemeldet ist: gerade dann
       wird aus dem Schweigen sonst ein Schluss. */
    echo '<p style="color:#666;">' . (int) count($alle) . ' Organe aus ' . (int) count($karte) . ' Wirt(en). '
       . 'Gezaehlt werden Anmeldungen; was sich nicht meldet, steht hier nicht.</p>';

    /* --------------------------------------------------------------------
       v3.9.3 — HIER STAND EIN ROTES KREUZ UEBER DEM NORMALFALL.

       WAS HIER STAND:
           <div class="notice notice-error"> „Dasselbe Organ aus zwei
           Plugins. Es laeuft die zuerst geladene Kopie. Das ist kein
           Absturz, aber die Verdraengung hat nicht gegriffen."
           … <code>organ</code> laeuft aus <strong>wirt</strong>, gemeldet
           auch von <strong>…</strong>

       MAMMUTS BEFUND VOM 07.09.2026 („das grosse Plugin ist auf diesem
       Knoten leer"), Abschnitt 3, woertlich:

           1  Der Satz "die Verdraengung hat nicht gegriffen" trifft den
              Fall `boden` nicht. Mehrere Melder sind keine mehrere Kopien.
              -> Der Satz gehoert getrennt: "aus N Paketen gemeldet, EINE
                 Kopie laeuft" ist gruen. Zwei laufende Kopien waeren rot.
           2  Die Meldung braucht ihre benannte Grenze:
              "Gezaehlt werden ANMELDUNGEN. Eine zweite Kopie, die hinter
               ihrem Riegel schweigt, steht hier nicht."
              Ohne diesen Satz wird aus dem Schweigen wieder ein Schluss.

       DER BEFUND LAG SEIT DEM 07.09. IN MEINEM POSTFACH, mit dem Besitzer
       „Boden" — und derselbe Satz stand unveraendert in MEINEM Haus. Die
       Zuweisung hat mich nicht entlastet: ich habe meinen eigenen Satz
       nicht gesucht. Gemessen hat ihn hier Christians DEV am 10.09.2026
       (Turing 3.9.1, Monolith deaktiviert): der Alarm stand unter `boden`,
       dem einzigen Organ, dessen Meldung AUSSERHALB des Riegels steht —
       also genau dort, wo es keine Verdraengungs-Mechanik gibt, und
       schwieg bei faust, feynman, kranz, wo sie gegriffen hat.

           Ein Alarm, der dort feuert, wo die Mechanik fehlt, und dort
           schweigt, wo sie gegriffen hat, meldet die Abwesenheit seines
           Gegenstands.

       Und es ist mein eigener Satz, von Kleinrock uebernommen: ein rotes
       Kreuz ohne Messgegenstand ist genauso gelogen wie ein gruenes.

       WAS BLEIBT: die Tatsache. Sie ist eine Auskunft, kein Befund.
       WAS GEHT:  die Deutung. Turing sieht den Riegel des Organs nicht und
                  kann zwei LAUFENDE Kopien aus dieser Tabelle nicht
                  erkennen; also behauptet er es auch nicht.
       -------------------------------------------------------------------- */
    /* --------------------------------------------------------------------
       v3.9.7 — DIE DRITTE ZEILE: 'melder' STATT 'doppelt'.

       Kaspers neue Form (Mammut, 11.09.2026):
           'melder'  IMMER eine Liste, auch bei einer Anmeldung;
                     je Eintrag wirt · fassung · deklariert
           'wirt'    der Wirt der Kopie, die DEKLARIERT hat
           'doppelt' die alte Sicht, abgeleitet — mit Sterbedatum
                     (VERTRAG 27)

       Turing liest ab jetzt 'melder'. 'doppelt' bleibt als Rueckfall fuer
       einen aelteren Boden — und die Seite sagt, aus WELCHER Quelle sie
       liest. Eine Auskunft ohne Herkunft ist auf einem Knoten mit zwei
       Bodenfassungen nicht nachpruefbar.

       UND DER GEWINN, DER MIR GEHOERT: zum ersten Mal kann diese Seite die
       FASSUNG einer Kopie zeigen, die nicht deklariert hat. Das war die
       FORDERUNG 4 meiner Probe vom 10.09., und sie war rot, weil es die
       Auskunft nicht gab. Jetzt gibt es sie.

       Was Turing dabei NICHT tut: die Faelle deuten.

       HIER STAND BIS 3.11.0 — und der Satz ist nicht stehen geblieben,
       sondern ZITAT, damit zu sehen ist, was entfernt wurde:
           „Eine hoehere Fassung, die nicht deklariert hat, wird GENANNT
            und nicht bewertet — ob sie ein Fund ist, entscheidet der
            Boden, nicht der Leser."
       Ohne diese Anfuehrungszeichen stuende die alte Behauptung ueber
       ihrer eigenen Berichtigung, und wer drei Zeilen weniger liest,
       liest sie als die heutige.
           Eine Berichtigung, die den alten Wortlaut nennt, ist von ihm
           mit einem Zaehler nicht zu unterscheiden. Unterscheidbar macht
           sie erst das Anfuehrungszeichen.

       BERICHTIGT am 11.10.2026, Posten 1, und der Satz oben war von
       Anfang an falsch. „Hoehere Fassung" ist KEIN Nennen. Es ist ein
       VERGLEICH, und ein Vergleich behauptet bereits, dass die zwei Zahlen
       vergleichbar sind. Gefunden hat das Mammut an Christians Seite:
       achtmal „hoehere Fassung", und keine war hoeher — 0.5.4 war
       Crockfords Paketnummer, 0.3.29 die von Post Dolly, und gemeldet war
       beide Male DIESELBE Datei unter dem Namen ihres Absenders.

           Vier Pakete tragen dieselbe Datei und melden sie unter vier
           Nummern. Nicht die Datei ist verschieden, nur ihr Absender.

       Kasper fuehrt die Herkunft seit dem 17.09. im Feld 'fassung_von'
       ('organ' = die Zahl, die das Organ bei der Anmeldung selbst genannt
       hat; 'wirt' = die Zahl aus dem Plugin-Kopf des Pakets). Dass das
       Feld dort steht und was es bedeutet, ist MAMMUTS MESSUNG
       (kasper.php:518, :560, :1198) und hier eine LEIHE: Turing liest es
       und prueft es nicht nach. Fehlt es, ist die Herkunft NICHT GEMESSEN
       — und eine nicht gemessene Herkunft ist kein 'organ'.

           Wer eine Bedingung nicht lesen kann, hat sie nicht erfuellt.
       -------------------------------------------------------------------- */
    $doppelt = array();
    $quelle_melder = 0;
    $quelle_doppelt = 0;
    $hoeher_still = array();
    /* v3.12.0 — WAS NICHT VERGLICHEN WURDE, UND WARUM NICHT. Zwei Listen,
       weil es zwei Ursachen sind: verschiedene Reihen, oder keine Herkunft.
           Wo eine Auskunft fuer mehrere Ursachen denselben leeren Wert
           ausgibt, gehoert die Ursache in dieselbe Auskunft. (R-STILLE-1) */
    $aus_reihen = array();
    $aus_still  = array();
    foreach ($alle as $organ => $e) {
        $melder = (isset($e['melder']) && is_array($e['melder'])) ? $e['melder'] : null;

        if ($melder !== null) {
            $quelle_melder++;
            if (count($melder) < 2) { continue; }
            $zeilen = array();
            $dekl_fassung = '';
            $dekl_von     = '';
            foreach ($melder as $m) {
                $m = (array) $m;
                if (! empty($m['deklariert'])) {
                    $dekl_fassung = (string) ($m['fassung'] ?? '');
                    /* Die Herkunft der Zahl des Deklaranten. Sie ist die eine
                       HAELFTE des Vergleichs; die andere steht unten. */
                    $dekl_von     = isset($m['fassung_von']) ? (string) $m['fassung_von'] : '';
                }
            }
            foreach ($melder as $m) {
                $m  = (array) $m;
                $w  = (string) ($m['wirt'] ?? '');
                $f  = (string) ($m['fassung'] ?? '');
                $de = ! empty($m['deklariert']);
                $von = isset($m['fassung_von']) ? (string) $m['fassung_von'] : '';
                $herkunft = ($von === 'organ') ? 'Nummer des Organs'
                          : (($von === 'wirt') ? 'Nummer des Wirts' : 'Herkunft nicht genannt');

                /* Die stille Stelle, die es ohne 'melder' nicht zu sehen gab:
                   eine HOEHERE Fassung, die nicht deklariert hat.

                   v3.12.0 — UND SIE WIRD JETZT NUR NOCH INNERHALB EINER REIHE
                   GESUCHT. Zwei Zahlen sind nur dann vergleichbar, wenn beide
                   aus derselben Reihe kommen: 'organ' gegen 'organ'. Ist eine
                   Seite 'wirt', stehen dort zwei Absendernummern von
                   moeglicherweise derselben Datei — version_compare rechnet
                   darauf eine Antwort aus, die nichts bedeutet.
                       Eine Marke ohne Sollwert ist eine Zahl, keine Pruefung.
                       Eine Zahl ohne ihre Reihe ist nicht einmal eine Zahl.
                   version_compare bleibt, was es war: eine Zeichenketten-Regel
                   und keine Aussage darueber, was „hoeher" bedeutet. */
                $zwei_zahlen = (! $de && $f !== '' && $dekl_fassung !== '');
                $beide_organ = ($von === 'organ' && $dekl_von === 'organ');
                $hoeher = ($zwei_zahlen && $beide_organ
                           && version_compare($f, $dekl_fassung, '>'));
                if ($zwei_zahlen && ! $beide_organ) {
                    if ($von === '' || $dekl_von === '') { $aus_still[]  = (string) $organ; }
                    else                                 { $aus_reihen[] = (string) $organ; }
                }
                if ($hoeher) { $hoeher_still[] = (string) $organ . ' (' . $w . ' ' . $f . ')'; }
                $zeilen[] = '<code>' . esc_html($w !== '' ? $w : '(Wirt nicht feststellbar)') . '</code> '
                    . esc_html($f !== '' ? $f : '(ohne Fassung)')
                    . ' <span style="color:#666;">(' . esc_html($herkunft) . ')</span>'
                    . ($de ? ' <strong>&#10003; deklariert</strong>'
                           : ' <span style="color:#666;">nicht deklariert</span>')
                    . ($hoeher ? ' <strong style="color:#996800;">&mdash; hoehere Fassung</strong>' : '');
            }
            $doppelt[] = '<code>' . esc_html((string) $organ) . '</code> <span style="color:#666;">('
                . (int) count($melder) . ' Anmeldungen, gelesen aus <code>melder</code>)</span><br>'
                . '<span style="font-size:12px;">' . implode('<br>', $zeilen) . '</span>';
            continue;
        }

        /* Rueckfall: ein Boden, der 'melder' NICHT fuehrt. Er kennt nur
           'doppelt', und 'doppelt' kennt keine Fassungen.

           v3.9.8 — HIER STAND „ein Boden vor 1.9.14", und das war eine
           Nummer an einer Stelle, die keine Nummer liest. Gemessen am
           gelieferten Boden (BODEN-1_9_14-kasper.php, 11.09.2026) setzt
           dieser Boden CLUSTER_ONE_BODEN_FASSUNG auf 1.11.0 — die 1.9.14
           ist eine Patch-Nummer UNTER zwei schon ausgegebenen Minors und
           wird auf keinem Knoten als Bodenfassung erscheinen. Ein Riegel
           auf version_compare(.., '1.9.14', '>=') wuerde 1.10.0 und 1.11.0
           durchlassen und dort 'melder' erwarten.
               Eine Auskunft, die an der FORM haengt, ueberlebt eine Nummer,
               die an der falschen Stelle steigt.
           Dieser Zweig haengt an der Form ($melder !== null) und nicht an
           der Nummer; der Kommentar sagt das ab hier auch. */
        if (! empty($e['doppelt'])) {
            $quelle_doppelt++;
            $auch = array();
            foreach ((array) $e['doppelt'] as $w2) { $auch[] = '<strong>' . esc_html((string) $w2) . '</strong>'; }
            $doppelt[] = '<code>' . esc_html((string) $organ) . '</code> — deklariert hat <strong>'
                . esc_html((string) ($e['wirt'] ?? '?')) . '</strong>, ebenfalls angemeldet von '
                . implode(', ', $auch)
                . ' <span style="color:#666;">(' . (int) (1 + count((array) $e['doppelt'])) . ' Anmeldungen, '
                . 'gelesen aus <code>doppelt</code> — dieser Boden nennt keine Fassungen je Melder)</span>';
        }
    }
    if ($doppelt) {
        /* 20.10.2026 (Neo), 3.14.0: die Zeile der Landkarte („N Organe kommen
           aus mehr als einem Plugin") steht jetzt hier als Zahl. Hier stand
           „Dasselbe Organ ist aus mehreren Paketen angemeldet." — die Zahl
           zaehlt ORGANE, die Zeile je Organ darunter die Anmeldungen (H214:
           zwei Zahlen, jede sagt, wovon sie spricht). */
        echo '<div class="notice notice-info inline"><p><strong>' . (int) count($doppelt)
           . ' Organ(e) ' . (count($doppelt) === 1 ? 'kommt' : 'kommen') . ' aus mehr als einem Plugin.</strong> '
           . 'Gelaufen ist eine Kopie, die zuerst deklariert hat; das ist der Normalfall und kein Befund. '
           . 'Ob es darauf ankommt, welche laeuft, sagt die Spalte „liegt in" der Dateitabelle unten.</p>'
           . '<ul style="list-style:disc;margin-left:2em;"><li>' . implode('</li><li>', $doppelt) . '</li></ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- jedes Glied von $doppelt ist in der Schleife oben aus esc_html, (int) und Literalen gebaut
        /* 20.10.2026 (Neo): esc_html lief ueber die zusammengefuegte Zeichenkette
           und zeigte „</code>, <code>" als Klartext (Christians DEV). Jetzt wird
           jeder Name einzeln escaped und dann gefuegt, wie weiter unten bei
           $ohne_platz seit T19. */
        $turing_codes = static function (array $namen): string {
            $t = array();
            foreach ($namen as $n) { $t[] = '<code>' . esc_html((string) $n) . '</code>'; }
            return implode(', ', $t);
        };
        if ($hoeher_still) {
            sort($hoeher_still);
            echo '<p style="color:#996800;"><strong>Eine hoehere Fassung hat angemeldet, ohne zu deklarieren:</strong> '
               . $turing_codes($hoeher_still) . '. Ob daraus ein Fund wird, entscheidet der Boden.</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tu_codes escapt jeden Namen einzeln mit esc_html (Closure oben)
        }
        if ($aus_reihen || $aus_still) {
            $aus_reihen = array_values(array_unique($aus_reihen)); sort($aus_reihen);
            $aus_still  = array_values(array_unique($aus_still));  sort($aus_still);
            echo '<p style="color:#666;"><strong>Nicht verglichen:</strong>';
            if ($aus_reihen) {
                echo ' bei ' . (int) count($aus_reihen) . ' Organ(en) kommen die zwei Zahlen aus verschiedenen Reihen '
                   . '(Nummer des Wirts gegen Nummer des Organs): ' . $turing_codes($aus_reihen) . '.'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tu_codes escapt jeden Namen einzeln mit esc_html (Closure oben)
            }
            if ($aus_still) {
                echo ' Bei ' . (int) count($aus_still) . ' Organ(en) nennt der Boden die Herkunft nicht: '
                   . $turing_codes($aus_still) . '.'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tu_codes escapt jeden Namen einzeln mit esc_html (Closure oben)
            }
            echo '</p>';
        }
        echo '</div>';
    }

    /* v3.9.10 — DIE ZWEI ZAEHLER STEHEN JETZT AUF DER SEITE, und das ist
       VERTRAG 27 §5.3: das Sterbedatum von 'doppelt' ist eine GEMESSENE NULL
       und kein Termin. Ablesbar heisst: jeder Leser zaehlt, aus welcher Quelle
       er gelesen hat, und die Summe steht da, wo ein Mensch sie sieht.
       Bis 3.9.9 standen beide Zaehler im Quelltext und nirgends sonst.
           Ein Zaehler, der nur im Quelltext steht, ist kein Messwert, sondern
           eine Absicht mit Variablennamen.
       Die Zeile erscheint AUCH, wenn es keine Doppelung gibt — sie ist eine
       Auskunft ueber die FORM der Tabelle und nicht ueber einen Fund. */
    $quelle_summe = $quelle_melder + $quelle_doppelt;
    if ($quelle_doppelt > 0) {
        /* Die Zeile erscheint nur, wenn der alte Rueckfall gebraucht wurde —
           dann ist sie eine Auskunft. Die gemessene Null steht sonst still im
           Quelltext; wer sie braucht, findet sie hier: melder/doppelt. */
        echo '<p style="color:#666;max-width:900px;">Gelesen aus <code>doppelt</code> (alter Boden ohne <code>melder</code>) bei '
           . (int) $quelle_doppelt . ' von ' . (int) $quelle_summe . ' Organ(en).</p>';
    }

    /* v3.9.7 — DIE BENANNTEN ZUGAENGE. Mammut hat sie gebaut, weil die Frage
       „welche laeuft" sonst an zwanzig Stellen zwanzig Antworten hat. Turing
       fragt sie und DEUTET IHREN AUSGANG NICHT: was zurueckkommt, steht da.
       Das ist die Lehre aus URTEIL X, wo ich aus einem Namen einen Schluss
       ueber ein fremdes Haus gezogen hatte. */
    if ($doppelt && function_exists('cluster_one_organ_wirt')) {
        $zg = array();
        foreach (array_keys($alle) as $organ) {
            $wie = null;
            $w   = cluster_one_organ_wirt((string) $organ, $wie);
            $zg[] = '<code>' . esc_html((string) $organ) . '</code> &rarr; '
                  . '<code>' . esc_html((string) $w) . '</code> '
                  . '<span style="color:#666;">(' . esc_html((string) (is_scalar($wie) ? $wie : gettype($wie)))
                  . ')</span>'
                  . (function_exists('cluster_one_organ_anmeldungen')
                        ? ' · ' . (int) cluster_one_organ_anmeldungen((string) $organ) . ' Anmeldung(en)'
                        : '');
        }
        echo '<details style="max-width:900px;color:#666;"><summary style="cursor:pointer;">Was der Boden selbst zu jedem Organ sagt (unveraendert wiedergegeben)</summary>'
           . '<p>' . implode('<br>', $zg) . '</p></details>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- jedes Glied von $zg ist in der Schleife oben aus esc_html und (int) gebaut
    }

    /* --------------------------------------------------------------------
       v3.9.2 — DREI FUNDE AN DIESEN ACHTZEHN ZEILEN. Gefunden hat den ersten
       Mammut am 10.09.2026; die zwei anderen standen daneben.

       WAS HIER STAND:

           echo '… Ein einziges Byte entscheidet … also sortiert
                 cluster-one-kleinrock/ VOR cluster-one/.
                 Jedes Light laedt vor Mammut, und Turing laedt zuletzt.
                 Wo eine Datei doppelt liegt, gilt die des Lights.</p>';
           $fam = <alles aus active_plugins, dessen NAME mit cluster-one
                   oder turing beginnt>;
           if ($fam) { <Liste> + 'gelesen, nicht nachgebaut' }

       1 · ZWEI SAETZE UEBER DIESEN KNOTEN, GEDRUCKT OHNE IHN ANZUSEHEN.
           „Jedes Light laedt vor Mammut" und „Wo eine Datei doppelt liegt"
           standen unbedingt da — auf einem Knoten ohne Monolithen und ohne
           doppelte Datei genauso. Die REGEL (0x2D vor 0x2F) ist allgemein
           und bleibt; was sie auf DIESEM Knoten ergibt, haengt jetzt an dem,
           was gelesen wurde.

       2 · WENN $fam LEER WAR, STAND HIER NICHTS — auch die Zeile nicht, die
           zu Recht „gelesen, nicht nachgebaut" sagt. Die Erklaerung stand
           dann ueber einem Loch, und die einzige ehrliche Zeile fehlte genau
           dann, wenn nichts gelesen worden war.

       3 · UND DIE AUSWAHL LAS DEN ORDNERNAMEN. Damit entschied diese Seite
           die Zugehoerigkeit am AUSSEHEN einer Zeichenkette — dieselbe
           Ableitung, die wir Mammut im Boden streichen (R-BODEN-2: die
           Einstufung liest die ROLLE, nicht das Aussehen). Ein Light in
           einem fremden Ordner war hier unsichtbar, ein netzwerkweit
           aktiviertes Plugin steht ueberhaupt nicht in active_plugins.

       WER HIER LIEGT, SAGT KASPERS TABELLE: jeder Wirt darin hat sich
       gemeldet. active_plugins liefert nur noch den PLATZ. Beide Richtungen
       werden genannt — gemeldet ohne Platz, und Platz ohne Meldung.
       -------------------------------------------------------------------- */
    echo '<h3>Die Ladereihenfolge</h3>';
    echo '<p style="max-width:820px;color:#666;">Gelesen aus <code>active_plugins</code>, in der gespeicherten Reihenfolge. '
       . 'Wo dieselbe Datei in mehreren Paketen liegt, laeuft die des zuerst geladenen.</p>';

    /* Die Wirte aus Kaspers Tabelle — das sind die, die sich gemeldet haben. */
    $gemeldet = array();
    foreach ($karte as $wirt => $organe) {
        if ((string) $wirt !== '') { $gemeldet[(string) $wirt] = (int) count($organe); }
    }

    /* active_plugins, einmal gelesen: Ordner => Platz in der gespeicherten
       Reihenfolge. Der Ordner ist der Teil vor dem ersten '/'. */
    $platz = array();
    $nr    = 0;
    foreach ((array) get_option('active_plugins', array()) as $p) {
        $nr++;
        $o = (string) $p;
        $s = strpos($o, '/');
        if ($s !== false) { $o = substr($o, 0, $s); }
        if ($o !== '' && ! isset($platz[$o])) { $platz[$o] = $nr; }
    }

    $mit_platz  = array();   /* gemeldet UND in der Liste */
    $ohne_platz = array();   /* gemeldet, steht nicht in der Liste */
    foreach ($gemeldet as $wirt => $n) {
        if (isset($platz[$wirt])) { $mit_platz[$wirt] = (int) $platz[$wirt]; }
        else                      { $ohne_platz[]     = (string) $wirt; }
    }
    asort($mit_platz);

    /* Und die andere Richtung: ein Eintrag, der nach dem NAMEN zur Familie
       gehoert, sich aber nicht gemeldet hat. Der Name ist hier kein Merkmal
       fuer Zugehoerigkeit, sondern der Anlass einer Frage — liegt, laeuft
       aber nicht (Vertrag 22 §2). */
    $stumm = array();
    foreach ($platz as $o => $n2) {
        if (isset($gemeldet[$o])) { continue; }
        if (strpos((string) $o, 'cluster-one') === 0 || strpos((string) $o, 'turing') === 0) {
            $stumm[(string) $o] = (int) $n2;
        }
    }
    asort($stumm);

    if ($mit_platz) {
        echo '<ol style="max-width:820px;">';
        foreach ($mit_platz as $wirt => $n3) {
            echo '<li><code>' . esc_html((string) $wirt) . '</code> '
               . '<span style="color:#666;">Platz ' . (int) $n3 . ' · '
               . (int) ($gemeldet[$wirt] ?? 0) . ' Organ(e)</span></li>';
        }
        echo '</ol>';
    } else {
        echo '<div class="notice notice-warning inline"><p><strong>Keine Reihenfolge feststellbar.</strong> '
           . 'Kein Wirt, der sich gemeldet hat, steht in <code>active_plugins</code>.</p></div>';
    }

    if ($ohne_platz) {
        sort($ohne_platz);
        /* Jeder Name EINZELN durch esc_html, dann zusammengefuegt. Umgekehrt
           — erst fuegen, dann escapen — geht das Trennzeichen mit durch und
           steht als Text auf der Seite. Gefunden hat das FORDERUNG 0 von T19:
           die Ausgabe war mit echter und leerer esc_html-Attrappe 12 Zeichen
           verschieden, und die Differenz war dieses Trennzeichen. */
        $op = array();
        foreach ($ohne_platz as $o4) { $op[] = '<code>' . esc_html((string) $o4) . '</code>'; }
        echo '<div class="notice notice-info inline"><p><strong>Gemeldet, aber ohne Platz in der Liste:</strong> '
           . implode(', ', $op) . ' — laeuft, steht aber nicht in <code>active_plugins</code> ' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- jedes Glied von $op ist zwei Zeilen hoeher mit esc_html gebaut
           . '(netzwerkweit aktiviert, mu-plugin oder Symlink).</p></div>';
    }

    if ($stumm) {
        $z = array();
        foreach ($stumm as $o2 => $n4) { $z[] = '<code>' . esc_html((string) $o2) . '</code> (Platz ' . (int) $n4 . ')'; }
        echo '<div class="notice notice-info inline"><p><strong>In der Liste, aber ohne Meldung:</strong> '
           . implode(', ', $z) . ' — der Name sieht nach Familie aus, gemeldet hat sich dieser Wirt nicht.</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- jedes Glied von $z ist zwei Zeilen hoeher mit esc_html gebaut
    }

    /* Die zwei Saetze, die bis 3.9.1 unbedingt dastanden — jeder an seiner
       eigenen Messung. Hier ist ausdruecklich von ZEICHENKETTEN die Rede und
       nicht von Rollen: die Regel oben handelt von zwei Bytes in zwei Namen,
       und genau das wird nachgesehen. Eine Aussage „der Monolith" waere eine
       Einstufung — die trifft der Boden, nicht diese Seite. */
    if (isset($mit_platz['cluster-one'])) {
        $davor = array();
        foreach ($mit_platz as $wirt => $n5) {
            if ($wirt !== 'cluster-one' && strpos((string) $wirt, 'cluster-one-') === 0
                && $n5 < $mit_platz['cluster-one']) {
                $davor[] = '<code>' . esc_html((string) $wirt) . '</code>';
            }
        }
        echo '<p style="max-width:820px;color:#666;"><code>cluster-one/</code> steht auf Platz '
           . (int) $mit_platz['cluster-one'] . '; davor stehen ' . (int) count($davor)
           . ' Eintraege, die mit <code>cluster-one-</code> beginnen'
           . ($davor ? ': ' . implode(', ', $davor) : '') . '.</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- jedes Glied von $davor ist in der Schleife oben mit esc_html gebaut
    }

    $alle_plaetze = $mit_platz + $stumm;
    $turing_o     = '';
    foreach ($alle_plaetze as $o3 => $n6) { if (strpos((string) $o3, 'turing') === 0 || strpos((string) $o3, 'cluster-one-turing') === 0) { $turing_o = (string) $o3; } } /* 4.0.0: auch die zwei neuen Ordner */
    if ($turing_o !== '' && count($alle_plaetze) > 1) {
        $hoechster = max($alle_plaetze);
        echo '<p style="max-width:820px;color:#666;"><code>' . esc_html($turing_o) . '/</code> steht auf Platz '
           . (int) $alle_plaetze[$turing_o] . ' und damit '
           . ($alle_plaetze[$turing_o] >= $hoechster
                ? 'hinter allen hier gezeigten Eintraegen'
                : '<strong>nicht</strong> hinter allen hier gezeigten Eintraegen (der hinterste steht auf Platz '
                  . (int) $hoechster . ')')
           . '.</p>';
    }

    /* v3.11.0, POSTEN 2 — DIE REGEL BLEIBT, DER SCHLUSS FAELLT.
       Hier stand: „nach der Regel oben also die aus dem Ordner mit dem
       laengeren Namen, gilt die des Lights."

       Die Regel darueber ist richtig und allgemein: 0x2D vor 0x2F. Der
       SCHLUSS daraus galt, solange es cluster-one/ gab — dann war der
       laengere Name immer der eines Lights. Unter fuenf Ordnern, die alle
       cluster-one- heissen, entscheidet nicht die Laenge, sondern das
       naechste Byte nach dem gemeinsamen Anfang: das Alphabet.

           Eine Folgerung aus einer allgemeinen Regel altert mit dem Fall,
           fuer den sie gezogen wurde — die Regel bleibt stehen und traegt
           sie nicht mehr.

       Und die Seite widerlegte sich drei Absaetze hoeher selbst: in jeder
       Verdraengungstabelle steht cluster-one-bismarck, der ERSTE im
       Alphabet und nicht der laengste Name.

       Was jetzt dasteht, rechnet nicht mehr — es liest ab, wie die Stelle
       darueber es seit v3.9.2 haelt: „abgelesen, nicht gerechnet." */
    /* 20.10.2026 (Neo): Hier stand ein Absatz ueber die Sortierregel von
       active_plugins (0x2D vor 0x2F, Alphabet statt Laenge). Die Regel ist
       richtig; auf dem Schirm genuegt der Satz ueber der Liste: es laeuft die
       Kopie des zuerst geladenen Pakets, und die Liste zeigt die Reihenfolge. */
}
}

/* ==========================================================================
   20.10.2026 (Neo), 3.14.0 — DIE DATEITABELLE, AUS CROCKFORD GEZOGEN.

   Entscheidung Nemesus/Neo vom 20.10.2026: Turing misst Pakete, Kasper misst
   Organe, die Landkarte die Dateien — also gehoert sie hierher, unter Kasper.
   Crockford traegt seit 0.5.17 keine landkarte.php mehr; der Kopf der Datei
   steht dort als Zitat in crockford.php.

   DIE MESSUNG IST DIE DER LANDKARTE (landkarte.php, H141/H160/H184/H196),
   umgezogen und nicht neu gebaut:
     - gefragt wird das VERZEICHNIS: die Vereinigung aller includes/-Ordner
       der aktiven Plugins, deren Ordnername mit cluster-one beginnt. Was
       dort liegt, steht in der Tabelle — ob jemand seinen Namen aufgeschrieben
       hat oder nicht;
     - erst zaehlen, dann hashen: sha1_file() nur fuer Dateien, die mehr als
       einmal liegen (eine Leseseite tut nichts umsonst);
     - sagt sha1 „verschieden", entscheidet der TOKENVERGLEICH, ob sich die
       Kopien NUR im Domaenen-Argument eines Uebersetzungsrufes unterscheiden:
       das letzte Zeichenketten-Argument eines Rufes mit mindestens zwei
       davon. Jede andere Stelle ist eine fremde Abweichung, verschiedene
       Tokenzahl ebenso (ein getauschtes Literal aendert die Zahl der Token
       nicht). Eine Domaene aus einer Variablen sieht der Vergleich nicht —
       er meldet dann lieber zu viel als zu wenig;
     - ohne Tokenizer, bei unlesbarer oder zu grosser Datei: NICHT GEMESSEN,
       und das bleibt bei der Warnung, nicht bei der Entwarnung.

   TURING RUFT DAFUER KEINE BODEN-FUNKTION. tu_plugins_pfad(), get_option
   und token_get_all genuegen. Was Kasper beisteuert — ob sich zu einer
   Datei ein Organ gemeldet hat — steht nur, wenn Kasper da ist, und sonst
   als NICHT GEMESSEN.

   WAS NICHT MITGEZOGEN IST, und warum:
     - die Organliste (cluster_one_landkarte_organe) und die Zeile „Datei in
       der Liste, liegt nirgends": das war eine ZUSAGE des Pakets Crockford,
       keine Messung. Turing fuehrt keine Liste ueber fremde Haeuser; ob eine
       Datei FEHLT, die es geben sollte, misst dieser Reiter nicht;
     - die Saetze ueber Mammut (cluster-one/ liegt oder liegt nicht): die
       Ladereihenfolge oben sagt, wer auf welchem Platz steht, und mehr
       behauptet Turing ueber den Monolithen nicht.
   ========================================================================== */
if (! defined('TURING_KARTE_TOKEN_MAX')) { define('TURING_KARTE_TOKEN_MAX', 1048576); }

/** Die aktiven Plugin-Ordner der Familie, in der Reihenfolge von active_plugins.
    BENANNTE GRENZE (H184): Familie heisst hier „Ordnername beginnt mit
    cluster-one" — dieselbe Regel wie in der Ladereihenfolge. Ein Produkt des
    Hauses unter anderem Namen ist von hier aus nicht abwesend, sondern
    unsichtbar. active_plugins steht in WordPress' eigener Sortierung; der
    erste Treffer ist die Kopie, die PHP deklariert hat. */
if (! function_exists('turing_karte_aktive_familie')) {
function turing_karte_aktive_familie(): array {
    $aus = array();
    if (! function_exists('get_option')) { return $aus; }
    foreach ((array) get_option('active_plugins', array()) as $e) {
        $o = (string) (explode('/', str_replace('\\', '/', (string) $e))[0] ?? '');
        if ($o === '' || strpos($o, 'cluster-one') !== 0) { continue; }
        if (! in_array($o, $aus, true)) { $aus[] = $o; }
    }
    return $aus;
}
}

/** Welche Dateien gibt es ueberhaupt? Das Verzeichnis antwortet.
    H160: DREI Faelle, nicht zwei. Das Plugin steht in active_plugins, also
    existiert es; laesst sich sein Ordner nicht einsehen, ist das eine fehlende
    Auskunft und kein leerer Ordner. Kein includes/ dagegen ist eine Tatsache:
    dieses Plugin traegt keine Organe.
    @return array('dateien' => [name, ...], 'unlesbar' => [ordner, ...]) */
if (! function_exists('turing_karte_dateien')) {
function turing_karte_dateien(): array {
    $n = array(); $u = array();
    $basis = rtrim(str_replace('\\', '/', turing_plugins_pfad()), '/');
    foreach (turing_karte_aktive_familie() as $o) {
        if (! @is_dir($basis . '/' . $o)) { $u[] = $o; continue; }
        $p = $basis . '/' . $o . '/includes';
        if (! @is_dir($p)) { continue; }
        $l = @scandir($p);
        if ($l === false) { $u[] = $o; continue; }
        foreach ($l as $f) { if (substr((string) $f, -4) === '.php') { $n[(string) $f] = true; } }
    }
    $n = array_keys($n); sort($n); sort($u);
    return array('dateien' => $n, 'unlesbar' => $u);
}
}

/** Welches aktive Plugin traegt diese Datei ZUERST — die Kopie, die PHP
    deklariert hat. Gemessen wird, welche Datei DALIEGT, nicht, ob sie
    geladen wurde (benannte Grenze der Landkarte, unveraendert). */
if (! function_exists('turing_karte_wirt')) {
function turing_karte_wirt(string $datei): string {
    $basis = rtrim(str_replace('\\', '/', turing_plugins_pfad()), '/');
    foreach (turing_karte_aktive_familie() as $o) {
        if (is_file($basis . '/' . $o . '/includes/' . $datei)) { return $o; }
    }
    return '';
}
}

if (! function_exists('turing_karte_rufe')) {
function turing_karte_rufe(): array {
    return array('__', '_e', '_x', '_ex', '_n', '_nx', '_n_noop', '_nx_noop',
                 'esc_html__', 'esc_html_e', 'esc_html_x',
                 'esc_attr__', 'esc_attr_e', 'esc_attr_x');
}
}

/** Die Tokenindizes, die das Domaenen-Argument eines Uebersetzungsrufes sind. */
if (! function_exists('turing_karte_domaenenstellen')) {
function turing_karte_domaenenstellen(array $t): array {
    $rufe = turing_karte_rufe();
    $out  = array();
    $max  = count($t);
    for ($i = 0; $i < $max; $i++) {
        if (! is_array($t[$i]) || $t[$i][0] !== T_STRING) { continue; }
        if (! in_array($t[$i][1], $rufe, true)) { continue; }
        $j = $i + 1;
        while (isset($t[$j]) && is_array($t[$j]) && $t[$j][0] === T_WHITESPACE) { $j++; }
        if (! isset($t[$j]) || $t[$j] !== '(') { continue; }
        $tiefe = 0; $letzte = null; $anz = 0;
        for ($k = $j; $k < $max; $k++) {
            if (! is_array($t[$k])) {
                if ($t[$k] === '(') { $tiefe++; }
                elseif ($t[$k] === ')') { $tiefe--; if ($tiefe === 0) { break; } }
                continue;
            }
            if ($tiefe === 1 && $t[$k][0] === T_CONSTANT_ENCAPSED_STRING) { $letzte = $k; $anz++; }
        }
        /* Mindestens zwei Zeichenketten — sonst ist gar keine Domaene da. */
        if ($letzte !== null && $anz >= 2) { $out[$letzte] = true; }
    }
    return $out;
}
}

/** Unterscheiden sich zwei Dateien NUR im Domaenen-Argument?
    urteil  true   nur die Domaene
            false  mindestens eine fremde Stelle
            null   NICHT GEMESSEN — und das bleibt bei der Warnung */
if (! function_exists('turing_karte_nur_domaene')) {
function turing_karte_nur_domaene(string $a, string $b): array {
    $nicht = array('urteil' => null, 'stellen' => 0, 'grund' => '');
    if (! function_exists('token_get_all')) { $nicht['grund'] = 'kein Tokenizer'; return $nicht; }
    $ga = @filesize($a); $gb = @filesize($b);
    if ($ga === false || $gb === false) { $nicht['grund'] = 'unlesbar'; return $nicht; }
    if ($ga > TURING_KARTE_TOKEN_MAX || $gb > TURING_KARTE_TOKEN_MAX) { $nicht['grund'] = 'zu gross'; return $nicht; }
    $qa = @file_get_contents($a); $qb = @file_get_contents($b);
    if ($qa === false || $qb === false) { $nicht['grund'] = 'unlesbar'; return $nicht; }
    $ta = @token_get_all($qa);
    $tb = @token_get_all($qb);
    if (! is_array($ta) || ! is_array($tb)) { $nicht['grund'] = 'kein Tokenizer'; return $nicht; }
    /* Verschiedene Tokenzahl BEWEIST eine fremde Abweichung. */
    if (count($ta) !== count($tb)) { return array('urteil' => false, 'stellen' => 0, 'grund' => 'Tokenzahl'); }
    $da = turing_karte_domaenenstellen($ta);
    $db = turing_karte_domaenenstellen($tb);
    $stellen = 0; $fremd = 0;
    for ($i = 0, $n = count($ta); $i < $n; $i++) {
        $x = $ta[$i]; $y = $tb[$i];
        if (is_array($x) !== is_array($y)) { $fremd++; continue; }
        $sx = is_array($x) ? $x[1] : $x;
        $sy = is_array($y) ? $y[1] : $y;
        if ($sx === $sy) { continue; }
        /* Nur durchgelassen, wenn die Stelle in BEIDEN Stroemen ein
           Domaenen-Argument ist. Eine Stelle, die es nur in einer Datei ist,
           ist ein Unterschied im Bau und kein Etikett. */
        if (isset($da[$i]) && isset($db[$i])) { $stellen++; continue; }
        $fremd++;
    }
    return array('urteil' => ($fremd === 0), 'stellen' => $stellen, 'grund' => '');
}
}

/** Je Datei: in welchen aktiven Plugins liegt sie, und sind die Kopien gleich?
    @return array datei => array('pfade' => [ordner => pfad], 'gleich' => bool|null,
                                 'urteil' => bool|null, 'stellen' => int, 'grund' => string)
    'gleich' ist null, solange die Frage sich nicht stellt (eine Kopie).
    'urteil' ist nur gesetzt, wenn gleich === false. */
if (! function_exists('turing_karte_kopien')) {
function turing_karte_kopien(array $dateien): array {
    $out   = array();
    $basis = rtrim(str_replace('\\', '/', turing_plugins_pfad()), '/');
    $fam   = turing_karte_aktive_familie();
    foreach ($dateien as $datei) {
        $pfade = array();
        foreach ($fam as $o) {
            $p = $basis . '/' . $o . '/includes/' . $datei;
            if (is_file($p)) { $pfade[$o] = $p; }
        }
        $gleich = null;
        if (count($pfade) > 1) {
            $h = array();
            foreach ($pfade as $p) {
                $s = @sha1_file($p);
                /* Ein sha1_file(), das FALSE gibt, ist weder Gleichheit noch
                   Ungleichheit, sondern eine fehlende Auskunft — als eigener
                   Wert gefuehrt, damit zwei unlesbare Dateien nie „gleich" heissen. */
                $h[] = ($s === false) ? 'unlesbar:' . $p : $s;
            }
            $gleich = (count(array_unique($h)) === 1);
        }
        /* Erst wenn die billige Messung ROT sagt, wird die teure gefragt.
           Jede Kopie gegen die ERSTE — n-1 Vergleiche. Ein fremder Befund
           gewinnt gegen „nur Domaene"; NICHT GEMESSEN gewinnt gegen „nur
           Domaene", aber nicht gegen den fremden Befund. */
        $urteil = null; $stellen = 0; $grund = '';
        if ($gleich === false) {
            $liste = array_values($pfade);
            $urteil = true;
            for ($i = 1; $i < count($liste); $i++) {
                $r = turing_karte_nur_domaene($liste[0], $liste[$i]);
                if ($r['stellen'] > $stellen) { $stellen = (int) $r['stellen']; }
                if ($r['urteil'] === false) { $urteil = false; $grund = (string) $r['grund']; break; }
                if ($r['urteil'] === null && $urteil !== false) { $urteil = null; $grund = (string) $r['grund']; }
            }
        }
        $out[$datei] = array('pfade' => $pfade, 'gleich' => $gleich, 'urteil' => $urteil,
                             'stellen' => $stellen, 'grund' => $grund);
    }
    return $out;
}
}

/** Der untere Teil des Kasper-Reiters: wer liefert welche Datei. Die Platte,
    nicht die Laufzeit; $alle (Kaspers Tabelle) ist nur die Gegenprobe und
    darf leer sein. */
if (! function_exists('turing_seite_kasper_dateien')) {
function turing_seite_kasper_dateien(array $alle): void {
    echo '<h3>Wer liefert welche Datei</h3>';
    echo '<p style="max-width:820px;color:#666;">Von der Platte gelesen: jede PHP-Datei unter <code>includes/</code> der aktiven Plugins, '
       . 'deren Ordner mit <code>cluster-one</code> beginnt. Liegt eine Datei mehrfach, werden die Kopien verglichen; '
       . 'das Domaenen-Argument eines Uebersetzungsrufes ist der einzige erlaubte Unterschied.</p>';

    $g = turing_karte_dateien();
    $namen = $g['dateien']; $unlesbar = $g['unlesbar'];

    if ($unlesbar) {
        /* H160: die fehlende Auskunft steht VOR der Tabelle. Jeder Name
           einzeln escaped, dann gefuegt (H167). */
        $u = array();
        foreach ($unlesbar as $o) { $u[] = '<code>' . esc_html((string) $o) . '</code>'; }
        echo '<div class="notice notice-error inline"><p><strong>' . (int) count($unlesbar)
           . ' Plugin-Ordner ' . (count($unlesbar) === 1 ? 'liess' : 'liessen') . ' sich nicht lesen:</strong> ' . implode(', ', $u) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- jedes Glied von $u ist zwei Zeilen hoeher mit esc_html gebaut
           . '. Was dort liegt, fehlt unten — nicht weil es leer ist, sondern weil niemand hineinsehen konnte '
           . '(Leserecht, <code>open_basedir</code>). Die Zahlen unten sind bis dahin unvollstaendig.</p></div>';
    }
    if (! $namen) {
        echo '<div class="notice notice-info inline"><p><strong>Keine Datei gefunden.</strong> '
           . 'Kein aktives Plugin mit einem <code>cluster-one</code>-Ordner traegt ein <code>includes/</code>.</p></div>';
        return;
    }

    $kopien = turing_karte_kopien($namen);
    $mit_kasper = ($alle !== array());

    $mehrfach = 0;    /* liegt in mehr als einem aktiven Plugin */
    $nur_dom  = 0;    /* ... und nur das Domaenen-Argument unterscheidet sich */
    $echt     = 0;    /* ... und eine fremde Stelle — DAS ist der Befund */
    $unklar   = 0;    /* ... und NICHT GEMESSEN; bleibt bei der Warnung */
    $still    = array();
    /* 3.14.1 (27.10.2026, Neo) — HILFSDATEIEN SIND KEIN BEFUND. Test 2 und 5:
       vierzehn Pacioli-Klassendateien standen gelb als „keine Meldung unter
       diesem Namen", bei jedem Aufruf, auf jedem Knoten. Eine Datei, die NUR
       in einem Plugin liegt und deren Wirt sich bei Kasper gemeldet hat, ist
       der Normalfall einer Klasse oder eines Helfers: sie reist nicht, also
       muss sie sich nicht ausweisen. Gelb bleibt, was sich nicht meldet UND
       in mehr als einem Plugin liegt – oder dessen Wirt ganz schweigt. */
    $hilfs    = array();
    $wirte_deklariert = array();
    foreach ($alle as $e) {
        $w = (string) (is_array($e) ? ($e['wirt'] ?? '') : '');
        if ($w !== '') { $wirte_deklariert[$w] = true; }
    }

    echo '<table class="widefat striped" style="max-width:900px;"><thead><tr>'
       . '<th>Datei</th><th>laeuft aus</th><th>liegt in</th>'
       . ($mit_kasper ? '<th>gemeldet bei Kasper</th>' : '')
       . '</tr></thead><tbody>';
    foreach ($namen as $datei) {
        $wer = turing_karte_wirt($datei);
        $k   = $kopien[$datei];
        $anz = count($k['pfade']);
        $urt = $k['urteil'];
        if ($anz > 1) {
            $mehrfach++;
            if ($k['gleich'] === false) {
                if ($urt === true) { $nur_dom++; } elseif ($urt === false) { $echt++; } else { $unklar++; }
            }
        }
        if ($anz === 1) {
            $spalte = '<span style="color:#666;">1 Plugin</span>';
        } elseif ($k['gleich'] === true) {
            $spalte = '<span style="color:#1a7f37;">' . (int) $anz . '&times; gleich</span>';
        } elseif ($k['gleich'] === false && $urt === true) {
            /* Die eigene Farbe (H196): nicht gruen — die Dateien SIND
               verschieden; nicht rot — es ist der Unterschied, unter dem
               jedes Produkt fuer sich uebersetzbar ist. */
            $spalte = '<span style="color:#996800;">' . (int) $anz . '&times; gleich bis auf die Textdomaene ('
                    . (int) $k['stellen'] . ' Stelle' . ((int) $k['stellen'] === 1 ? '' : 'n') . ')</span>';
        } elseif ($k['gleich'] === false && $urt === null) {
            $spalte = '<strong style="color:#b32d2e;">' . (int) $anz . '&times; verschieden</strong> '
                    . '<span style="color:#996800;">(NICHT GEMESSEN, woran: ' . esc_html((string) $k['grund']) . ')</span>';
        } else {
            $spalte = '<strong style="color:#b32d2e;">' . (int) $anz . '&times; verschieden</strong>'
                    . ($k['grund'] !== '' ? ' <span style="color:#666;">(' . esc_html((string) $k['grund']) . ')</span>' : '');
        }

        /* Die Gegenprobe gegen die Laufzeit (H177): liegt die Datei da, und
           hat sich ihr Organ gemeldet? Der Organname wird aus dem Dateinamen
           abgeleitet; kasper.php meldet sich als `boden` — die eine bekannte
           Ausnahme. Meldet sich eine Datei unter einem anderen Namen, steht
           hier „keine Meldung unter diesem Namen", und das ist dann eine
           Frage, kein Befund. */
        $gemeldet = '';
        if ($mit_kasper) {
            $organ = ($datei === 'kasper.php') ? 'boden' : (string) substr($datei, 0, -4);
            if (isset($alle[$organ])) {
                $gemeldet = '<code>' . esc_html($organ) . '</code>';
            } elseif ($anz === 1 && $wer !== '' && isset($wirte_deklariert[$wer])) {
                /* 3.14.1: liegt nur hier, und der Wirt meldet sich – Hilfsdatei. */
                $gemeldet = '<span style="color:#666;">kein Organ; Wirt meldet sich</span>';
                $hilfs[] = $datei;
            } else {
                $gemeldet = '<span style="color:#996800;">keine Meldung unter diesem Namen</span>';
                $still[] = $datei;
            }
        }

        echo '<tr><td><code>' . esc_html((string) $datei) . '</code></td>'
           . '<td>' . ($wer !== '' ? '<code>' . esc_html($wer) . '</code>' : '<em>nicht feststellbar</em>') . '</td>'
           . '<td>' . $spalte . '</td>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $spalte ist oben aus (int), esc_html und Literalen gebaut
           . ($mit_kasper ? '<td>' . $gemeldet . '</td>' : '') // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $gemeldet ist oben aus esc_html und Literalen gebaut
           . '</tr>';
    }
    echo '</tbody></table>';
    echo '<p style="color:#666;">' . (int) count($namen) . ' Datei(en), ' . (int) $mehrfach . ' davon in mehr als einem Plugin.</p>';

    if ($echt > 0) {
        echo '<div class="notice notice-error inline"><p><strong>' . (int) $echt . ' Datei(en) ' . ($echt === 1 ? 'liegt' : 'liegen')
           . ' in mehreren Plugins, und die Kopien unterscheiden sich in <em>mehr</em> als der Textdomaene.</strong> '
           . 'Das ist ein Befund: ein Schnitt ist auseinandergelaufen. Es laeuft die zuerst geladene Kopie, und welche das ist, '
           . 'haengt an der Reihenfolge der Ordner, nicht an einer Absicht. Die Fassungen der beteiligten Pakete gehoeren verglichen.</p></div>';
    }
    if ($unklar > 0) {
        echo '<div class="notice notice-warning inline"><p><strong>' . (int) $unklar . ' Datei(en) ' . ($unklar === 1 ? 'liegt' : 'liegen')
           . ' mehrfach, ' . ($unklar === 1 ? 'ist' : 'sind') . ' verschieden und ' . ($unklar === 1 ? 'wurde' : 'wurden') . ' NICHT GEMESSEN.</strong> '
           . 'Der Vergleich braucht den PHP-Tokenizer, eine lesbare Datei und eine unter 1 MiB. Fehlt eines davon, sagt Turing nicht '
           . '„nur die Textdomaene" — er sagt nichts, und nichts bleibt neben der Warnung.</p></div>';
    }
    if ($nur_dom > 0) {
        echo '<div class="notice notice-info inline"><p><strong>' . (int) $nur_dom . ' Datei(en) ' . ($nur_dom === 1 ? 'unterscheidet' : 'unterscheiden')
           . ' sich nur in der Textdomaene.</strong> Das ist die Bedingung, unter der jedes Produkt fuer sich uebersetzbar ist: dasselbe Organ '
           . 'reist in mehreren Paketen, jedes nennt seine eigene Domaene. Kein Befund.</p></div>';
    }
    if ($mehrfach > 0 && $echt === 0 && $unklar === 0 && $nur_dom === 0) {
        echo '<div class="notice notice-info inline"><p><strong>' . (int) $mehrfach . ' Datei(en) ' . ($mehrfach === 1 ? 'liegt' : 'liegen')
           . ' in mehr als einem aktiven Plugin, alle Kopien byte-gleich</strong> (<code>sha1_file</code>, gemessen). '
           . 'Welche laeuft, ist damit ohne Folge; die Spalte „laeuft aus" sagt, welche es ist.</p></div>';
    }
    if ($still) {
        $s = array();
        foreach ($still as $d) { $s[] = '<code>' . esc_html((string) $d) . '</code>'; }
        echo '<div class="notice notice-warning inline"><p><strong>' . (int) count($still) . ' Datei(en) ' . (count($still) === 1 ? 'liegt' : 'liegen')
           . ' in einem aktiven Plugin, und oben hat sich kein Organ dieses Namens gemeldet:</strong> ' . implode(', ', $s) . '. ' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- jedes Glied von $s ist zwei Zeilen hoeher mit esc_html gebaut
           . 'Oben die Laufzeit, hier die Platte. Warum, sagt Turing nicht: der Lader hat sie nicht eingebunden, sie ruft '
           . '<code>cluster_one_hier()</code> nicht, oder sie meldet sich unter einem anderen Namen. Verdraengung ist es nicht — '
           . 'eine verdraengte Datei schweigt nicht, die gewinnende Kopie meldet sich an ihrer Stelle.</p></div>';
    }
    if ($hilfs) {
        /* 3.14.1 — bis hierher standen diese Dateien in der gelben Warnung darueber. */
        echo '<p style="color:#666;">' . (int) count($hilfs) . ' Datei(en) ' . (count($hilfs) === 1 ? 'liegt' : 'liegen')
           . ' nur in einem Plugin und ' . (count($hilfs) === 1 ? 'meldet' : 'melden') . ' sich nicht als Organ; ihr Wirt meldet sich. '
           . 'Das sind Klassen und Helfer, die nicht reisen – kein Befund. Gelb wuerde es erst, wenn eine solche Datei in einem zweiten Plugin auftaucht.</p>';
    }

    echo '<p style="max-width:820px;color:#666;font-size:12px;"><strong>Benannte Grenzen.</strong> '
       . 'Gelesen wird <code>active_plugins</code>: ein Ordner, der liegt und aus ist, steht hier nicht (der Installer zeigt ihn). '
       . 'Familie heisst: Ordnername beginnt mit <code>cluster-one</code>. '
       . 'Eine Messung ist immer mit dem Knoten einverstanden — ob eine Datei FEHLT, die es geben sollte, sagt diese Tabelle nicht; '
       . 'dazu braucht es eine Zusage von aussen, und die liegt im Schnittwerkzeug. '
       . ($mit_kasper ? '' : 'Ohne Kasper ist NICHT GEMESSEN, ob sich zu einer Datei ein Organ gemeldet hat. ')
       . 'Gemessen wird, welche Datei daliegt, nicht, ob sie geladen wurde.</p>';
}
}

/* --------------------------------------------------------------------------
   Reiter 4 — Neumann. Leer, und das mit Absicht.

   Neumann ist als PAKET geprueft und verworfen (Backlog H13) und als PRINZIP
   behalten. Drei Zustaende, in Christians Formulierung:

     lebenswichtig fuer die App — Komfort fuer die Plugins — Versuchsfeld fuer uns

   Ein Versuchsfeld gehoert in ein Werkzeug und nicht in ein Produkt: "Ein
   Versuch in Mammut ist ein Zeuge, kein Organ. Er bekommt keinen Menuepunkt,
   keinen Kranz-Reiter."

   Dieser Reiter ist deshalb heute leer. Ein leerer Reiter mit einer benannten
   Frage ist ehrlicher als ein voller mit einer geratenen Antwort — und er ist
   die Stelle, an der die erste Messung erscheinen wird, statt in Mammut.
   -------------------------------------------------------------------------- */
/* 20.10.2026 (Neo) — DER REITER IST FORT, DER TEXT STEHT HIER.
   Ein leerer Reiter in einem Paket, das veroeffentlicht werden soll, ist ein
   Versprechen ohne Datum. Was auf ihm stand, woertlich:

       „Hier ist Platz fuer die Neumann-Versuche. Noch steht nichts drin, und
        das ist der Zustand, nicht ein Versaeumnis.
        Was entschieden ist: Neumann wird kein eigenes Plugin (Backlog H13).
        Geprueft, verworfen als Paket, behalten als Prinzip. Drei Zustaende,
        die nebeneinander gelten: lebenswichtig fuer die App — ohne eigenen
        Speicher und eigene Kennung gibt es sie nicht; Komfort fuer die
        Plugins — nuetzlich, nie Voraussetzung, kein Modul darf Neumann
        brauchen; Versuchsfeld fuer uns — hier, in diesem Reiter.
        Die offene Frage: Wo wartet die Post eines Menschen-Knotens, und wem
        gehoert der Wartesaal? Zwei Menschen, verschiedene Sprache,
        verschiedene Knoten, nicht gleichzeitig online. Solange diese Frage
        keine gemessene Antwort hat, waere jeder Bau hier geraten.
        Die Regel fuer diesen Reiter: Was hier erscheint, ist ein Zeuge: eine
        Messung mit einem Knopf, einem Durchgang und einem Urteil, die nichts
        speichert und sich rueckstandslos entfernen laesst. Kein Entwurf, kein
        Prototyp, keine Vorschau."

   Die Frage ist seit dem 20.10.2026 nicht mehr offen, sondern in Arbeit: die
   Daten eines Menschen bleiben bei ihm (Bismarck, Merkliste je User, auf
   Wunsch in seinem eigenen Speicher). Kommt ein Zeuge dafuer, bekommt er
   seinen Reiter zurueck. */

/* --------------------------------------------------------------------------
   Reiter 2 — der Spiegel.

   Der Dump laeuft NUR auf Knopfdruck. Im Original war das eine
   Sicherheitsentscheidung (ein Diagnose-Ausgang, der bei jedem Seitenaufruf
   in der Zwischenablage liegt, ist eine Hintertuer), und daran aendert der
   Umzug nichts.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_seite_spiegel')) {
function turing_seite_spiegel(): void {
    $boden = turing_boden_am_knoten();

    echo '<h2 style="margin-top:.4em;">Der Spiegel'
       . turing_blase('Der Spiegel legt den gespeicherten Zustand von Cluster One offen: Konfigurationen, Listen, Flags, Zustaende. ' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tu_blase() gibt fertiges HTML zurueck (wpautop(esc_html) im Innern)
           . 'Struktur statt Inhalt: Beitrags- und Nachrichtentexte erscheinen nur als Laenge und gesalzener Fingerabdruck; '
           . 'alles, was nach Token, Schluessel, Secret oder Mailadresse riecht, als ***. Das Salz gilt je Dump und wird nirgends gespeichert. '
           . 'Gelesen werden nur Optionen mit dem Praefix cluster_one_ und die Ereignis-Tabelle. Die Schluessel der KI-Anbieter (connectors_ai_*) sieht er nicht. '
           . 'Er laeuft nur auf Knopfdruck: ein Diagnose-Ausgang, der bei jedem Seitenaufruf bereitliegt, waere eine Hintertuer.')
       . '</h2>';

    /* v3.11.0, POSTEN 1 — DIESELBE FORM WIE AN DER ZWEITEN STELLE.
       Was hier stand, war derselbe Fehlschluss, den v3.9.5 vierhundert Zeilen
       weiter schon repariert hat: aus dem Schweigen EINES Namens wurde ein
       Urteil ueber den Knoten. Dort haelt Turing seit v3.9.5 einen zweiten
       Namen dagegen; hier nicht — die Reparatur hatte die zweite Stelle nicht
       erreicht.

           Eine Reparatur, die an einer Stelle greift und die zweite nicht
           mitzaehlt, halbiert den Fehler nicht. Sie belegt, dass man es
           besser wusste.

       Darum bekommt diese Stelle NICHT eine neue Form, sondern DIE DORTIGE.
       Zwei Stellen, eine Form, ein Wortlaut-Muster. */
    if ($boden === '') {
        echo '<div class="notice notice-info inline"><p><strong>Kein laufender Boden feststellbar.</strong> '
           . 'Es laeuft kein Produkt von Cluster One; ob eines liegt, steht unter Kasper. Der Spiegel kann '
           . 'trotzdem laufen und faende dann hoechstens Reste einer frueheren Installation.</p></div>';
    } else {
        echo '<p><strong>Laufender Boden:</strong> <code>' . esc_html($boden) . '</code>';
        $tbl = turing_c1_events_tabelle();
        echo ' &middot; <strong>Ereignis-Tabelle:</strong> ' . ($tbl !== '' ? '<code>' . esc_html($tbl) . '</code>' : '<em>nicht vorhanden</em>');
        echo ' <span style="color:#666;">— die Fassung des Bodens, nicht eines Pakets; die Pakete stehen im Installer.</span></p>';
    }

    /* 3.14.8 (Runde 37): der Knopf und der Lauf nur mit Recht. Der Dump nennt die
       gespeicherte Konfiguration des Knotens; er entsteht nur auf Knopfdruck und
       kommt deshalb bei dem, der nicht druecken darf, gar nicht erst zustande. */
    $zeigen = isset($_POST['turing_spiegel']) && turing_darf_tun() && check_admin_referer('turing_spiegel_lauf', '_tunonce');

    if (turing_darf_tun()) {
        echo '<form method="post">';
        wp_nonce_field('turing_spiegel_lauf', '_tunonce');
        echo '<p><button class="button button-primary button-hero" name="turing_spiegel" value="1">Spiegel zeigen</button></p></form>';
    } else {
        echo '<p style="max-width:820px;color:#666;">Den Spiegel zeigt der SysOp. Der Dump nennt die gespeicherte Konfiguration dieses Knotens und ist fuer die Werkbank bestimmt; hier steht deshalb kein Knopf.</p>';
    }

    if (! $zeigen) {
        return;
    }

    $dump = turing_spiegel_dump();
    $json = wp_json_encode($dump, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    echo '<hr><h2>Der Spiegel</h2>';
    echo '<p style="color:#666;"><strong>' . (int) count($dump['options']) . '</strong> Optionen';
    if (isset($dump['events']['count'])) {
        echo ' &middot; <strong>' . (int) $dump['events']['count'] . '</strong> Ereignisse (die letzten 25 im Dump)';
    }
    echo '</p>';
    turing_kopierfeld('tu-spiegel', (string) $json,
        'Dieser Dump nennt die gespeicherte Konfiguration von Cluster One, die Fassung des laufenden '
        . 'Bodens und WP/PHP dieses Knotens.',
        420);

    /* Die Selbstpruefung laeuft MIT dem Dump, nicht daneben. Wer den Spiegel
       benutzt, sieht im selben Zug, ob der Scrubber noch das tut, was auf ihm
       steht. Eine Pruefung, die man extra aufrufen muss, ruft niemand auf. */
    $pruef = turing_selbstpruefung();
    $rot   = array_filter($pruef, function ($z) { return ! $z['ok']; });

    echo '<h2>Selbstpruefung des Scrubbers</h2>';
    if ($rot) {
        echo '<div class="notice notice-error inline"><p><strong>' . count($rot) . ' von ' . count($pruef) . ' Behauptungen sind ROT.</strong> Der Dump oben ist damit nicht vertrauenswuerdig — die Maskierung tut nicht, was auf ihr steht. Nicht weitergeben.</p></div>';
    } else {
        echo '<p style="color:#1a7f37;"><strong>Alle ' . count($pruef) . ' Behauptungen sind gruen.</strong> Jede davon war einmal ein Leck.</p>';
    }
    echo '<table class="widefat striped" style="max-width:820px;"><tbody>';
    foreach ($pruef as $z) {
        echo '<tr><td style="width:64px;font-weight:700;color:' . ($z['ok'] ? '#1a7f37' : '#b32d2e') . ';">' . ($z['ok'] ? 'OK' : 'ROT') . '</td>';
        echo '<td>' . esc_html($z['was']) . '</td>';
        echo '<td style="color:#666;">' . esc_html($z['grund']) . '</td></tr>';
    }
    echo '</tbody></table>';
    echo '<p style="max-width:820px;color:#666;">Benannte Grenze: Diese Pruefung stammt von derselben Hand wie der Scrubber. Sie faengt eine Aenderung, die etwas kaputtmacht — sie faengt nicht, was von Anfang an fehlte.</p>';
}
}

/* --------------------------------------------------------------------------
   Reiter 1 — der Pacioli-Zeuge, unveraendert ausser dem Rahmen.
   -------------------------------------------------------------------------- */
if (! function_exists('turing_seite_zeuge')) {
function turing_seite_zeuge(): void {
    /* 3.14.8 (Runde 37): der Lauf nur mit Recht; der Knopf unten ebenso. */
    $lauf = isset($_POST['turing_tat']) && turing_darf_tun() && check_admin_referer('turing_lauf', '_tunonce');

    echo '<h2 style="margin-top:.4em;">Der Pacioli-Zeuge'
       . turing_blase('Ein Durchgang prueft alle eingerichteten Anbieter: erst ein Aufruf je Anbieter mit dem echten Schluessel, dann derselbe Aufruf, ' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tu_blase() gibt fertiges HTML zurueck (wpautop(esc_html) im Innern)
           . 'nachdem jede gefundene Schluessel-Option auf eine Marke umgeschaltet wurde. Traegt der zweite Request die Marke, liest der Client je Aufruf; '
           . 'traegt er denselben Abdruck wie vorher, haelt er den Schluessel ueber den Request fest. Das ist die Frage, von der Pacioli abhaengt. '
           . 'Es wird nichts gespeichert. GET (Modell-Erkundung) laeuft durch, POST wird gelesen und abgebrochen: keine Token-Kosten, kein Prompt verlaesst das Haus. '
           . 'Ein Schluessel wird nie angezeigt, verglichen wird ein gesalzener 8-Zeichen-Abdruck.')
       . '</h2>';

    /* v3.1.0: KEIN KNOPF OHNE GEGENSTAND. Ohne eingerichteten Anbieter gab es
       hier bis 3.0.0 einen Knopf, der einen Durchgang gegen einen erfundenen
       Anbieter fuhr und danach ein Urteil meldete. Wer nichts zu messen hat,
       bekommt jetzt den Satz statt des Knopfes. */
    $c1_anbieter = turing_provider_liste();
    if (! $c1_anbieter) {
        echo '<div class="notice notice-info inline"><p><strong>Auf diesem Knoten ist kein Anbieter eingerichtet.</strong> '
           . 'Weder eine KI-Kennung in Cluster One noch eine <code>connectors_ai_*_api_key</code>-Option mit Inhalt. '
           . 'Es gibt nichts zu messen, deshalb steht hier kein Knopf. Der Teil darunter misst weiter.</p></div>';
    } elseif (turing_darf_tun()) {
        echo '<form method="post">';
        wp_nonce_field('turing_lauf', '_tunonce');
        echo '<p><button class="button button-primary button-hero" name="turing_tat" value="1">Durchgang starten</button> '
           . '<span style="color:#666;">' . esc_html(implode(', ', $c1_anbieter)) . '</span></p></form>';
    } else {
        /* 3.14.8: Mitglied ohne Recht – die Anbieter werden genannt, der Knopf nicht gezeichnet.
           Der Bericht traegt die Adresse des Knotens und den Unterbau mit Fassungen; er entsteht
           nur auf Knopfdruck. Ob der Nachweis eingetragen ist, sagt Pacioli auf seiner Seite. */
        echo '<p style="max-width:820px;color:#666;">Eingerichtete Anbieter: ' . esc_html(implode(', ', $c1_anbieter)) . '. '
           . 'Den Durchgang startet der SysOp; ob sein Urteil eingetragen ist, steht bei Pacioli unter „Fuer Entwickler".</p>';
    }

    /* ---- TEIL ZWEI: wann wird gelesen? ---- */
    $zug = turing_zugriffe_lesen();
    echo '<hr><h2>Wann holt sich der Client den Schluessel?</h2>';
    echo '<p style="max-width:800px;color:#666;">Laeuft ohne Knopf mit: die Lauscher haengen seit dem Laden dieser Datei, vor <code>plugins_loaded</code>. Frueher kommt ein normales Plugin nicht.</p>';

    if ($zug) {
        echo '<table class="widefat striped" style="max-width:720px;"><thead><tr><th>#</th><th>Option</th><th>gelesen in Phase</th></tr></thead><tbody>';
        foreach ($zug as $e) {
            echo '<tr><td>' . (int) $e['nr'] . '</td><td><code>' . esc_html($e['option']) . '</code></td><td><code>' . esc_html($e['phase']) . '</code></td></tr>';
        }
        echo '</tbody></table>';
        echo '<div class="notice notice-success inline"><p><strong>Der Zugriff geschieht spaet genug.</strong> Ein gewoehnliches Plugin kann sich davorsetzen; Pacioli setzt den Schluessel des Menschen, der handelt, beim Beginn des Aufrufs, ohne Umschalten mittendrin.</p></div>';
    } else {
        echo '<div class="notice notice-warning inline"><p><strong>Bis hierher kein Zugriff.</strong> Entweder holen die Anbieter-Plugins ihren Schluessel schon beim Laden (dann kaeme ein normales Plugin zu spaet), '
           . 'oder erst beim Aufruf. Den Durchgang oben starten und danach wieder auf diese Tabelle schauen.</p></div>';
    }

    /* v2.1.1 — DER BERICHT HAENGT NICHT MEHR AM KNOPF.

       Teil zwei misst ohne Knopf, das Kopierfeld erschien aber erst nach einem
       Durchgang. Wer nur nachsehen wollte, wann gelesen wird, musste den
       Befund von Hand markieren. Ein Zeuge, dessen Ergebnis man abtippen muss,
       ist ein halber Zeuge. */
    $d = null;
    if ($lauf) {
    $d = turing_durchgang();

    $farbe = array('GRUEN' => '#1a7f37', 'ROT' => '#b32d2e', 'GEMISCHT' => '#996800', 'UNKLAR' => '#996800', 'UNBEANTWORTBAR' => '#996800', 'NICHTS EINGERICHTET' => '#666');
    $satz  = array(
        'NICHTS EINGERICHTET' => 'Auf diesem Knoten ist kein Anbieter eingerichtet. Es wurde nichts gemessen — das ist kein Ergebnis, sondern das Fehlen eines Gegenstands.',
        'GRUEN'          => 'Der Client liest den Schluessel je AUFRUF. Die Grundannahme von Connector Vault haelt — pre_option_ ist der richtige Angriffspunkt.',
        /* v3.12.0, Posten 3 — BERICHTIGT, UND WIDERLEGT HAT ES DIE TABELLE
           AUF DIESER SEITE. Hier stand bis 3.11.0: „Der Angriffspunkt muss
           eine Etage tiefer liegen, beim ausgehenden Request."

           Aus dieser Messung war das nicht zu schliessen. Verglichen werden
           der letzte POST der Runde A und der letzte POST der Runde B; der
           Schalter der Runde B geht HOCH, nachdem Runde A gelaufen ist. ROT
           heisst also genau: umschalten NACH dem ersten Lesen wirkt nicht.
           Ueber einen Filter VOR dem ersten Lesen sagt dieser Vergleich
           nichts — und die Request-Tabelle darunter sagt das Gegenteil: ein
           Wert, der sich zwischen zwei Requests desselben Anbieters AENDERT,
           wird mehr als einmal geholt.

           Mammuts Messung am Knoten vom 11.10.2026 (eine LEIHE, nicht meine):
               xai GET  echt/e4b15e24  Authorization  api.x.ai/v1/models
               xai POST echt/5b3f140d  Authorization  api.x.ai/v1/chat/...
           Ein Anbieter, ein Feld, zwei Werte.

               Du hast gemessen, dass Umschalten NACH dem Lesen nicht wirkt,
               und daraus geschlossen, man muesse eine Etage TIEFER. Die
               eigene Tabelle zeigt, dass VOR dem Lesen alles wirkt. Das ist
               nicht tiefer. Das ist frueher.

           'pre_http_request' braucht es dafuer nicht. Es bleibt Weg 2. */
        'ROT'            => 'Umschalten NACH dem ersten Lesen wirkt nicht: der Client hat den Schluessel dann schon geholt und haelt ihn. Der Angriffspunkt liegt frueher, vor dem ersten Lesen, und dort ist pre_option_ richtig. Tragen GET und POST desselben Anbieters unten verschiedene Abdruecke, wird der Wert mehr als einmal geholt, und dann wirkt alles, was vor dem Lesen gesetzt wird.',
        'GEMISCHT'       => 'Die Anbieter verhalten sich unterschiedlich. Die Tabelle unten lesen — ein Entwurf, der fuer den einen traegt, traegt fuer den anderen nicht.',
        'UNKLAR'         => 'Kein eindeutiges Bild. Die Tabellen unten lesen, bevor etwas geschlossen wird.',
        'UNBEANTWORTBAR' => 'Es entstand kein POST. Ohne Generierungs-Request kann dieser Zeuge nichts sagen.',
    );
    $c = isset($farbe[$d['gesamt']]) ? $farbe[$d['gesamt']] : '#666';

    /* 3.14.7 (30.10.2026, Neo) — MIT PACIOLI AUF DEM KNOTEN STEHT SEINE FRAGE OBEN.
       Nemesus: „Müsste Turing nicht alles gruen anzeigen?" Ja. Das ROT beantwortete die
       Frage von 2.0 (Umschalten nach dem ersten Lesen), die Pacioli seit 0.8 nicht mehr
       stellt; der Pacioli-Befund stand gruen zwei Absaetze tiefer in der Beobachtung.
       Jetzt: oben die Pacioli-Frage mit Farbe, darunter die alte als Eigenschaft des
       Unterbaus in Grau. Ohne Pacioli bleibt alles wie bis 3.14.6 (else-Zweig). */
    $satz_p = array(
        'GRUEN'          => 'Kein Schluessel, der im POST den Server verlassen hat, stammt aus einer Options-Zeile. Er kam aus dem, was VOR dem ersten Lesen gesetzt wurde – bei Pacioli der Schluessel des handelnden Menschen. Das ist der Nachweis, den Pacioli von innen nicht fuehren kann; er gehoert als Notiz in Paciolis „Fuer Entwickler".',
        'ROT'            => 'Mindestens ein POST trug einen Schluessel, der in einer Options-Zeile liegt. Paciolis Tausch ist dort NICHT angekommen – der Request faehrt mit dem Schluessel des Knotens. Die Zeilen stehen in der Tabelle.',
        'GEMISCHT'       => 'Bei manchen Anbietern kommt der Tausch an, bei anderen nicht. Die Tabelle unten sagt, bei welchen.',
        'UNKLAR'         => 'Kein eindeutiges Bild: ein POST ohne lesbaren Schluessel. Die Tabellen unten lesen.',
        'UNBEANTWORTBAR' => 'Es entstand kein POST. Ohne Generierungs-Request kann dieser Zeuge nichts sagen.',
    );
    if (! empty($d['pacioli_da'])) {
        $cp = isset($farbe[$d['gesamt_pacioli']]) ? $farbe[$d['gesamt_pacioli']] : '#666';
        echo '<hr><h2>Gesamturteil</h2>';
        echo '<p style="max-width:800px;color:#666;margin:0 0 .5em;"><strong>Die Frage (Pacioli):</strong> stammt der Schluessel, der im POST den Server '
           . 'verlaesst, aus einer <strong>Options-Zeile</strong> – oder aus dem, was <strong>vor</strong> dem ersten Lesen gesetzt wurde, also aus Paciolis Tausch?</p>';
        echo '<p style="font-size:26px;font-weight:700;color:' . esc_attr($cp) . ';margin:0 0 .3em;">' . esc_html($d['gesamt_pacioli']) . '</p>';
        echo '<p style="max-width:800px;">' . esc_html(isset($satz_p[$d['gesamt_pacioli']]) ? $satz_p[$d['gesamt_pacioli']] : '') . '</p>';
        echo '<p style="color:#666;"><strong>' . (int) $d['messbar'] . '</strong> von ' . (int) count($d['anbieter']) . ' Anbietern waren messbar.';
        if (! empty($d['stumm'])) {
            echo ' Ohne eingerichteten Schluessel und daher nicht mitgezaehlt: <code>' . implode('</code>, <code>', array_map('esc_html', $d['stumm'])) . '</code>.';
        }
        echo '</p>';
        echo '<h3 style="margin-top:1.2em;">Eigenschaft des Unterbaus: Umschalten der Option nach dem ersten Lesen</h3>';
        echo '<p style="max-width:800px;color:#666;"><strong>' . esc_html($d['gesamt']) . '</strong> &middot; ' . esc_html(isset($satz[$d['gesamt']]) ? $satz[$d['gesamt']] : '')
           . ' Pacioli nutzt diesen Weg nicht; die Zeile steht hier, weil sie eine Eigenschaft des WordPress-Clients ist, die man kennen muss.</p>';
    } else {
    echo '<hr><h2>Gesamturteil</h2>';
    /* v3.12.0, Posten 2 — DAS URTEIL NENNT SEINE FRAGE. Sie steht VOR dem
       Wort, nicht darunter: wer die Farbe zuerst liest, hat sie schon
       gedeutet. */
    echo '<p style="max-width:800px;color:#666;margin:0 0 .5em;"><strong>Die Frage:</strong> wirkt ein Umschalten der Option '
       . '<strong>nach</strong> dem ersten Lesen noch auf das, was hinausgeht? Ob ein Plugin frueh genug kommt, um '
       . '<strong>vor</strong> dem ersten Lesen zu setzen, steht oben unter „Wann holt sich der Client den Schluessel?".</p>';
    echo '<p style="font-size:26px;font-weight:700;color:' . esc_attr($c) . ';margin:0 0 .3em;">' . esc_html($d['gesamt']) . '</p>';
    echo '<p style="max-width:800px;">' . esc_html(isset($satz[$d['gesamt']]) ? $satz[$d['gesamt']] : '') . '</p>';
    echo '<p style="color:#666;"><strong>' . (int) $d['messbar'] . '</strong> von ' . (int) count($d['anbieter']) . ' Anbietern waren messbar.';
    if (! empty($d['stumm'])) {
        echo ' Ohne eingerichteten Schluessel und daher nicht mitgezaehlt: <code>' . implode('</code>, <code>', array_map('esc_html', $d['stumm'])) . '</code>.';
    }
    echo '</p>';
    }

    echo '<h2>Urteil je Anbieter</h2>';
    echo '<table class="widefat striped" style="max-width:820px;"><thead><tr><th>Anbieter</th>'
       . (! empty($d['pacioli_da']) ? '<th>Pacioli: Schluessel aus Options-Zeile?</th><th>Umschalten nach dem Lesen</th>' : '<th>Urteil</th>')
       . '<th>Runde A</th><th>Runde B</th></tr></thead><tbody>';
    foreach ($d['zeilen'] as $cid => $z) {
        $cc = isset($farbe[$z['urteil']]) ? $farbe[$z['urteil']] : '#666';
        echo '<tr><td><strong>' . esc_html($cid) . '</strong></td>';
        if (! empty($d['pacioli_da'])) {
            $cpz = isset($farbe[$z['pacioli']]) ? $farbe[$z['pacioli']] : '#666';
            echo '<td style="color:' . esc_attr($cpz) . ';font-weight:700;">' . esc_html($z['pacioli'])
               . ($z['pacioli'] === 'ROT' && $z['heimat'] ? '<br><code style="font-weight:normal;">' . implode('</code>, <code>', array_map('esc_html', $z['heimat'])) . '</code>' : '')
               . ($z['pacioli'] === 'GRUEN' ? '<br><span style="color:#666;font-weight:normal;">nicht aus einer Options-Zeile</span>' : '') . '</td>';
            echo '<td style="color:#666;">' . esc_html($z['urteil']) . '</td>';
        } else {
        echo '<td style="color:' . esc_attr($cc) . ';font-weight:700;">' . esc_html($z['urteil']) . '</td>';
        }
        echo '<td style="font-family:monospace;font-size:11px;">' . esc_html($z['abd_a'] !== '' ? $z['abd_a'] : '—') . '</td>';
        echo '<td style="font-family:monospace;font-size:11px;">' . esc_html($z['bef_b'] === 'B' ? 'MARKE B' : ($z['abd_b'] !== '' ? $z['abd_b'] : '—')) . '</td></tr>';
    }
    echo '</tbody></table>';

    echo '<h2>Beobachtung</h2>';
    echo '<p><strong>Belauschte Optionen:</strong> ' . (int) $d['kandidaten'] . ' &middot; <strong>im ersten Aufruf gelesen:</strong> ';
    echo $d['gelesen'] ? '<code>' . implode('</code>, <code>', array_map('esc_html', $d['gelesen'])) . '</code>' : '<em>keine</em>';
    echo '</p>';
    if ($d['heimat']) {
        echo '<p><strong>Die Schluessel wohnen in:</strong> <code>' . implode('</code>, <code>', array_map('esc_html', $d['heimat'])) . '</code></p>';
        $n = array_diff($d['heimat'], $d['gelesen']);
        if ($n) {
            echo '<div class="notice notice-warning inline"><p><code>' . implode('</code>, <code>', array_map('esc_html', $n)) . '</code> tragen den Schluessel, wurden im Aufruf aber <strong>nicht gelesen</strong>. Der Client hat sie vorher geholt.</p></div>';
        }
    } else {
        echo '<p><em>Keine Options-Zeile enthaelt einen Wert, der im Request ankam.</em> Dann kommt der Schluessel nicht aus der Optionsschicht.</p>';
    }

    foreach ($d['zeilen'] as $cid => $z) {
        echo '<h3>' . esc_html($cid) . '</h3>';
        echo '<p style="margin:.2em 0;"><strong>Runde A</strong> (echte Schluessel) — <code>' . esc_html($z['weg_a']) . '</code></p>';
        turing_tabelle($z['reqs_a']);
        echo '<p style="margin:.2em 0;"><strong>Runde B</strong> (alle Optionen auf Marke B) — <code>' . esc_html($z['weg_b']) . '</code></p>';
        turing_tabelle($z['reqs_b']);
    }

    }

    $bericht = turing_bericht($d);
    echo '<hr><h2>Bericht</h2>';
    turing_kopierfeld('tu-zeuge', $bericht,
        'Dieser Bericht nennt die Fassungen des Unterbaus, die Adresse des Knotens und WP/PHP dieses '
        . 'Servers.',
        380);
}
}
