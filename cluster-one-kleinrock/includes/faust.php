<?php
/**
 * Cluster One — Modul gregor (DER SCHNITT, v0.0.100 -> Module).
 * Reiner Umzug aus cluster-one.php; jede Funktion byte-identisch zur Baseline.
 */
defined('ABSPATH') || exit;

/* v1.0.5 — FAUST. „Zwei Seelen wohnen, ach! in meiner Brust."
   ---------------------------------------------------------------------------
   Diese Datei hiess bis 1.0.1 gregor.php und galt als zwei Organe: Gregor (der
   Kalender des Knotens) und Archimedes (Treffen, die ueber Knotengrenzen
   laufen). Christian am 26.08.2026: dieselbe Fehlteilung wie bei Platon und
   Sokrates. Nachgemessen, und es stimmt:

       $ev['scheduling']    18 Stellen   das Datum wird verhandelt
       $ev['archimedes']    12 Stellen   das Datum ist Fels, es gehoert woanders hin

   Ein FELD, keine Modulgrenze. Zwoelf Stellen verzweigen auf
   !empty($ev['archimedes']) — das ist ein if, kein Schnitt.

   WARUM DIE FUNKTIONSNAMEN gregor_* UND archimedes_* BLEIBEN: sie sind beide
   Faust. Zwanzig Umbenennungen waeren zwanzig Gelegenheiten fuer Textdrift, und
   davon gab es an diesem einen Tag dreizehn. Umbenannt wurden nur die zwei
   Zeichner (kalender_gitter, kalender_kopf) — die gehoerten nie Gregor, beide
   Seelen brauchen sie.

   ---------------------------------------------------------------------------
   DER RIEGEL, UND WARUM ER NACH FASSUNG ENTSCHEIDET

   Faust liegt in ZWEI Lights, byte-gleich — nach Christians Zuordnungsregel:

       Bismarck bleibt innerhalb des Knotens, Kleinrock nicht.

   Seele 1 bleibt drin, Seele 2 geht hinaus. Faust ist der erste Fall, in dem
   die Regel BEIDE Antworten gibt; also beide Ordner. Zwei Kopien in zwei
   Plugins sind ohne Riegel ein Fatal ("Cannot redeclare"), nachgestellt am
   26.08.2026.

   Ein blosser function_exists-Riegel liesse die ZUERST GELADENE Kopie gewinnen
   — und das waere immer Bismarck, wegen des Buchstabens 'b' ('-b' sortiert vor
   '-k'). Ein Verhalten am Alphabet festzumachen ist keine Entscheidung. Und der
   Fall ist real: wer Kleinrock aktualisiert und Bismarck nicht, hat zwei
   Fassungen auf der Platte.

   Also gewinnt die HOECHSTE Fassung. Die Fehlerrichtung ist gewaehlt:
   geschwiegen wird nur, wenn eine STRENG hoehere Fassung in einem AKTIVEN
   Plugin liegt. Der Hoechste findet niemanden ueber sich und deklariert; bei
   Gleichstand greift function_exists.

   „ES GIBT KEINE LUECKE, IN DER NIEMAND DEKLARIERT." — DIESER SATZ STAND HIER
   UND WAR FALSCH (v1.0.3, 18.09.2026, gemessen mit w91). Der ENTWURF stimmte;
   der CODE hat ihn nicht getragen, aus zwei Gruenden, und der erste hat den
   zweiten verdeckt:

       1  DAS LESEFENSTER. Gelesen wurden 512 Byte je Kandidat, mit der
          Begruendung „die Fassungszeile steht in den ersten zehn". Sie stand
          dort einmal. Die Datei ist gewachsen; die Zeile steht bei Byte 2771,
          das Muster endet bei 2814. Es wurde nie eine Zahl gefunden, also
          trat nie jemand zurueck, also entschied doch das Alphabet.
              Eine Grenze, die mit einer LAGE begruendet ist, traegt die
              Lage als Behauptung. Waechst die Datei, wird aus der
              Begruendung eine Erinnerung.

       2  DIE KONSTANTE. CLUSTER_ONE_FAUST_FASSUNG wird von der ZUERST
          geladenen Kopie gesetzt und gilt dann fuer alle. Jede spaetere
          verglich die fremden Zahlen gegen die Zahl der ERSTEN und nie
          gegen ihre eigene. Mit DREI Kopien und gehobenem Fenster trat
          daraufhin JEDE zurueck — die Luecke, die es angeblich nicht gab.
              Gemessen: w91, Richtung 7b, bevor irgendetwas gebaut wurde.
              Haette ich nur das Fenster gehoben, waere aus einem stillen
              Fehler ein sichtbarer geworden.

   BEIDES IST BEHOBEN, UND ZWAR MIT EINEM LESER STATT MIT ZWEI ZAHLEN:
   cluster_one_faust_fassung_lesen() liest die Fassung aus einer DATEI —
   aus der eigenen genauso wie aus der fremden. Damit steht die Zahl an
   genau EINER Stelle (dem define unten), und jede Kopie vergleicht gegen
   IHRE eigene.

       Die Zahl wird von einer Datei GESCHRIEBEN und von einer anderen mit
       einem MUSTER GELESEN. Sie muss darum ein LITERAL neben dem
       Konstantennamen bleiben — wer sie in eine Variable legt, macht sie
       fuer jede andere Kopie unsichtbar. (Gemessen: w91, Richtung 8.)

   GELESEN WIRD MIT EINEM FENSTER, UND DAS FENSTER IST GEMESSEN: die Zeile
   steht heute bei Byte 2771, das Muster endet bei 2814. 8192 ist das
   Vierfache davon — Platz fuer Wachstum, und trotzdem kein voller
   Dateilesevorgang je Anfrage.

   UND WENN DAS FENSTER NICHT REICHT, SAGT ES DAS. Bis v1.0.2 war „keine
   Zahl gefunden" dasselbe wie „keine hoehere Fassung": zwei Ursachen, eine
   Stille. Jetzt wird nicht zurueckgetreten UND es wird gemeldet.
       Ein leerer Wert ist eine Antwort auf „was", nie auf „warum".
   Dass bei Unlesbarkeit NICHT zurueckgetreten wird, ist Absicht: traeten
   zwei Kopien, die einander nicht lesen koennen, beide zurueck, deklarierte
   wieder niemand.
   --------------------------------------------------------------------------- */

/** Die Fassung, die EINE faust.php von sich behauptet — aus der Datei gelesen.
 *
 * DERSELBE LESER FUER DIE EIGENE UND FUER JEDE FREMDE KOPIE. Das ist der ganze
 * Punkt: die Zahl steht an genau einer Stelle im Baum (dem define unten), und
 * niemand vergleicht gegen die Zahl eines anderen.
 *
 * @return string  die Fassung, oder '' wenn im Fenster keine steht.
 */
if (!function_exists('cluster_one_faust_fassung_lesen')) {
function cluster_one_faust_fassung_lesen(string $c1_datei): string {
    if ($c1_datei === '' || !is_file($c1_datei)) { return ''; }
    $c1_kopf = (string) @file_get_contents($c1_datei, false, null, 0, 8192);
    if (preg_match('~CLUSTER_ONE_FAUST_FASSUNG.{0,4}[\x27"]([0-9.]+)[\x27"]~s', $c1_kopf, $c1_m)) {
        return (string) $c1_m[1];
    }
    return '';
}
}

if (!defined('CLUSTER_ONE_FAUST_FASSUNG')) {
    define('CLUSTER_ONE_FAUST_FASSUNG', '1.1.2');   // eigene Linie, wie der Boden
    /* 1.1.2 (02.11.2026, Neo), Runde Pi: DIE GESCHICHTE STEHT JETZT UNTER DER ZAHL. 1.0.19 hatte die Zahl ueber die
       Geschichte gezogen; seitdem wuchs die Geschichte VOR der Zahl weiter (1.0.20, 1.0.21, 1.1.0, 1.1.1), und das
       Musterende lag bei Byte 7936 von 8192. Die fuenf Bloecke stehen unveraendert hier darunter. Pi selbst: Vermerke
       mit Grund an den GET-Schaltern und Eingaben, wp_unslash an drei Zeilen (643/644/655). Kein Verhalten bewegt. */
    /* 1.0.19 (28.10.2026, Neo): DIE ZAHL STEHT VOR IHRER GESCHICHTE. Bis 1.0.18 stand
       sie darunter, und die Geschichte ist gewachsen: das Ende des Musters lag bei
       Byte 8120 — 72 Byte vor dem Fensterrand von 8192 (oben: „die Zeile steht
       heute bei Byte 2771"; das war am 18.09.). Zwei Kommentarzeilen mehr, und
       jede Kopie haette ihre eigene Fassung nicht mehr lesen koennen — auch die
       1.0.18, die noch auf einem Knoten liegt, liest mit DIESEM Fenster. Darum
       zieht die Zahl nach oben, die Geschichte bleibt und folgt ihr. Gemessen
       nach dem Zug: Ende des Musters bei Byte 6785. */
    /* 1.0.20 (01.11.2026, Neo): Plugin Check — die zwoelf translators-Kommentare aus
       Gamma kamen nur in die Bismarck-Kopie; Kleinrock trug dieselbe 1.0.19 mit
       anderen Bytes (Vegas verletzt). Jetzt beide gleich. Kein Verhalten bewegt.
       Gemessen: Ende des Musters bei Byte 7091. */
    /* 1.0.21 (02.11.2026, Neo): Plugin Check „auf null" — vier Vermerke mit Grund (zwei
       Ueberschriften ueber cluster_one_page_h1, ein wpautop um esc_html, die Entwicklermeldung
       an _doing_it_wrong in Zeile ~188). Kein Zeichen Code bewegt. Ende des Musters: siehe
       Bauprotokoll Iota (gemessen nach dem Schreiben, unter 8192). */
    /* 1.1.0 (02.11.2026, Neo): Praefix-Runde — die zehn Riegel-Variablen dieser Datei auf Dateiebene
       heissen $cluster_one_faust_* statt $c1_faust_* (Verzeichnis-Regel: globale Namen tragen den
       Praefix). Kein Zeichen Code bewegt. Ende des Musters: siehe Bauprotokoll Ny. */
    /* 1.1.1 (02.11.2026, Neo): 1.1.0 hatte das f verloren ($cluster_one_aust_*); repariert, kein
       Verhalten bewegt. Ende des Musters: Bauprotokoll Xi. DAS FENSTER (8192) IST FAST VOLL. */
    /* 1.0.6, 28.09.2026 — DER REITER „VERGANGENHEIT".
       DIE ZAHL MUSSTE MIT. Der Riegel oben entscheidet nach FASSUNG, und
       faust.php liegt in zwei Paketen. Waere sie bei 1.0.5 geblieben,
       haetten auf einem Knoten mit Kleinrock 0.2.29 und Bismarck 0.2.28
       zwei VERSCHIEDENE Dateien dieselbe Zahl getragen — dann entscheidet
       die Ladereihenfolge, und die gewinnt immer 'bismarck' vor
       'kleinrock'. Der neue Reiter waere still verschwunden.
           Zwei verschiedene Dateien mit derselben Nummer sind fuer den
           Riegel eine Datei.
       Gefunden hat es nicht ich, sondern der Riegel in bauen.py. */
    /* 1.0.7, 06.10.2026 — DAS VORSCHLAGSRECHT.
       Und die Zahl geht diesmal MIT der Aenderung, nicht hinterher: am
       28.09. hat der Riegel in bauen.py sie mir nachgetragen, weil
       faust.php in zwei Paketen faehrt und der Ladewettlauf nach Fassung
       entscheidet. Einmal gelernt. */
    /* 1.0.8, 06.10.2026 — VORSCHLAGSRECHT, LOESCHRECHT, AENDERUNGSNACHRICHT. */
    /* 1.0.9, 07.10.2026 — DAS KREUZ, DIE ZAHL BEIM ANLEGEN, UND DIE
       KOPFZAHL, DIE LAENGER LEBT ALS DER TERMIN. */
    /* 1.0.10, 07.10.2026 — EINE BERICHTIGUNG: die Frage nach Bismarck
       traf einen Praefix und nie die Funktion. */
    /* 1.0.11, 07.10.2026 — DIE TAFEL AM TAG, die sichtbare Gaestezahl und
       das Ziehen des eigenen Punktes. */
    /* 1.0.12, 07.10.2026 — ZIEHEN, ZWEITE FASSUNG: die offene Tafel deckte
       ihre Nachbarn zu, der Punkt sass im <summary>, und der Zustand lag in
       einer Variablen. Alle drei weg. */
    /* 1.0.13, 07.10.2026 — Holleriths zwei Befunde: das Klick-Flag verfaellt
       jetzt von selbst, und die Zelle fragt die Uhr EINMAL. */
    /* 1.0.14, 07.10.2026 — ZIEHEN, DRITTE FASSUNG. Im dragover ist der
       dataTransfer im geschuetzten Modus: dort entscheidet die Typenliste,
       die Fracht wird erst im drop gelesen. */
    /* 1.0.19, 28.10.2026 — DIE SEITE KOMMT IN EINEM STUECK (Ausgabepuffer in
       cluster_one_render_gregor_page; Grund dort). Das define steht seit heute
       OBEN in diesem Block, siehe dort. */
}

/* MEINE eigene Zahl — aus MEINER Datei, nicht aus der Konstante. Die Konstante
   gehoert der zuerst geladenen Kopie; diese Zeile gehoert dieser hier. */
$cluster_one_faust_meine    = cluster_one_faust_fassung_lesen(__FILE__);
$cluster_one_faust_hoeher   = false;
$cluster_one_faust_unlesbar = array();
if ($cluster_one_faust_meine !== '' && function_exists('get_option') && defined('WP_PLUGIN_DIR')) {
    $cluster_one_faust_basis = rtrim(str_replace('\\', '/', (string) WP_PLUGIN_DIR), '/') . '/';
    foreach ((array) get_option('active_plugins', array()) as $cluster_one_faust_e) {
        $cluster_one_faust_o = (string) (explode('/', str_replace('\\', '/', (string) $cluster_one_faust_e))[0] ?? '');
        if (function_exists('cluster_one_familie_ordner')
         && !cluster_one_familie_ordner($cluster_one_faust_o)) { continue; }   /* H184: der Boden statt des
            Namens. Ohne geladenen Kasper nicht beantwortbar -> nicht ueberspringen. */
        $cluster_one_faust_p = $cluster_one_faust_basis . $cluster_one_faust_o . '/includes/faust.php';
        if (!is_file($cluster_one_faust_p) || realpath($cluster_one_faust_p) === realpath(__FILE__)) { continue; }
        $cluster_one_faust_f = cluster_one_faust_fassung_lesen($cluster_one_faust_p);
        if ($cluster_one_faust_f === '') { $cluster_one_faust_unlesbar[] = $cluster_one_faust_o; continue; }
        if (version_compare($cluster_one_faust_f, $cluster_one_faust_meine, '>')) { $cluster_one_faust_hoeher = true; }
    }
}
/* DREI AUSGAENGE, und der dritte bekommt endlich einen Satz. */
if (($cluster_one_faust_meine === '' || $cluster_one_faust_unlesbar !== array())
    && function_exists('_doing_it_wrong')) {
    // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Entwicklermeldung an _doing_it_wrong(): kein Seiteninhalt; WordPress legt sie selbst durch wp_kses (wp_trigger_error)
    _doing_it_wrong(
        'faust.php',
        $cluster_one_faust_meine === ''
            ? 'Diese Kopie kann ihre EIGENE Fassung nicht lesen '
              . '(CLUSTER_ONE_FAUST_FASSUNG steht nicht in den ersten 8192 Byte). '
              . 'Sie tritt darum nicht zurueck — welche Kopie laeuft, entscheidet '
              . 'wieder die Ladereihenfolge.'
            : 'Die Fassung dieser faust.php ist nicht lesbar: '
              . implode(', ', $cluster_one_faust_unlesbar)
              . '. Es wird NICHT zurueckgetreten — aber es ist auch nicht gemessen, '
              . 'ob dort eine hoehere Fassung liegt.',
        '1.0.3'
    );
    // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
}

if (!$cluster_one_faust_hoeher && !function_exists('cluster_one_render_gregor_page')) {

cluster_one_hier('faust', CLUSTER_ONE_FAUST_FASSUNG);

/** WELCHER TAG IST DAS? (28.09.2026, Christians Entscheidung)
 *
 * Ein Termin hat genau einen Tag, um den es geht — aber er steht an zwei
 * verschiedenen Stellen, je nachdem, ob schon geschnitten wurde:
 *
 *     festgelegt   scheduling['locked_date']   der Schnitt des SysOp
 *     offen        die SPAETESTE Marke
 *
 * WARUM DIE SPAETESTE UND NICHT DIE FRUEHESTE: eine Marke durfte nie in
 * die Vergangenheit gesetzt werden (cluster_one_gregor_marker, v0.0.96).
 * Sind ALLE Marken vorbei, hat niemand mehr einen Tag vorgeschlagen, der
 * noch kommt — dann ist der Termin nicht offen, sondern verfallen. Die
 * fruehste Marke wuerde einen Termin schon dann fuer vorbei erklaeren,
 * wenn nur der erste Vorschlag alt ist.
 *
 * LEER heisst „dieser Termin nennt keinen Tag". Das kommt nach dem Anlegen
 * nicht vor (der Ersteller setzt seine Marke dabei), steht hier aber als
 * eigene Antwort, damit der Leser nicht raten muss.
 */
function cluster_one_gregor_tag(array $c1_ev): string {
    $c1_s = isset($c1_ev['scheduling']) && is_array($c1_ev['scheduling'])
        ? $c1_ev['scheduling'] : array();

    if ((string) ($c1_s['status'] ?? '') === 'locked') {
        return (string) ($c1_s['locked_date'] ?? '');
    }

    $c1_spaet = '';
    foreach ((array) ($c1_s['markers'] ?? array()) as $c1_m) {
        $c1_d = cluster_one_gregor_marker_date($c1_m);
        /* Zeichenvergleich, und das ist Absicht: ISO-Daten (Y-m-d) sortieren
           als Text genauso wie als Datum. Ein strtotime() hier waere eine
           zweite Zeitrechnung neben der, mit der die Datei ueberall sonst
           vergleicht (`$d < current_time('Y-m-d')`, sechs Stellen). */
        if ($c1_d !== '' && $c1_d > $c1_spaet) { $c1_spaet = $c1_d; }
    }
    return $c1_spaet;
}

/** IST DIESER TERMIN VON DER ZEIT UEBERHOLT?
 *
 * Christians Grenze, woertlich: „Ist ein Treffen am 30.09. geplant, sollte
 * es auch als vergangen angezeigt werden, wenn es einen Tag spaeter ist."
 * Also am Tag selbst noch nicht vorbei, am Tag danach schon.
 *
 * GERECHNET, NICHT GESPEICHERT. Es gibt kein Feld dafuer, und es soll
 * keines geben:
 *
 *     Ein gespeichertes „ist vorbei" muesste von jemandem gesetzt werden,
 *     und wer es nicht setzt, hat einen Termin, der nicht altert.
 *
 * Die Stunde (`time`) und die Zeitzone (`tz`) bleiben ausdruecklich
 * ausserhalb dieser Rechnung. Sie machen die UHRZEIT ueber Knoten hinweg
 * eindeutig; wer sie in eine TAGESGRENZE hineinrechnet, macht aus „der Tag
 * ist vorbei" eine Frage, die auf zwei Knoten zwei Antworten hat.
 *
 * Und weil gerechnet wird, rechnet ein fremder Knoten dasselbe aus, sobald
 * er zieht: es muss nichts ueber das Mesh reisen, damit ein vergangenes
 * Treffen drueben aufhoert, eine Einladung zu sein.
 */
/** Steht der Tag dieses Treffens fest? (07.10.2026)
 *
 * SIE EXISTIERT WEGEN EINER MESSUNG, DIE NICHT GEHEN KONNTE. Diese Frage
 * stand bis heute INLINE im Kalenderbau. Hollerith hat sie dort gebrochen,
 * und zwei Proben meines Zeugen blieben gruen — weil sie eine ABSCHRIFT der
 * Regel auswerteten und nicht die Regel.
 *
 *     Eine Regel, die nur als Abschrift geprueft werden kann, wird
 *     geprueft, bis sich das Original aendert.
 *
 * Der Kalender entscheidet an dieser Frage, WELCHEN ZWEIG eine Tageszelle
 * zeichnet: den gruenen Punkt (der Tag steht) oder die Punktreihe mit Tafel
 * und Anfasser (der Tag wird noch verhandelt). Genau dieser Zweig war der
 * Grund der ganzen Runde.
 *
 * ZWEI FELDER, NICHT EINS: ein 'locked' ohne Datum ist kein fester Tag,
 * sondern ein halb geschriebener Zustand. Der Kalender hat beide immer
 * gefragt; die Funktion fragt sie weiter, Zeichen fuer Zeichen.
 */
function cluster_one_gregor_eingerastet(array $c1_ev): bool {
    $c1_s = isset($c1_ev['scheduling']) && is_array($c1_ev['scheduling']) ? $c1_ev['scheduling'] : array();
    return (string) ($c1_s['status'] ?? '') === 'locked'
        && (string) ($c1_s['locked_date'] ?? '') !== '';
}

function cluster_one_gregor_vorbei(array $c1_ev): bool {
    $c1_tag = cluster_one_gregor_tag($c1_ev);
    return $c1_tag !== '' && $c1_tag < current_time('Y-m-d');
}

/** Gregor-Reiter (v0.0.53, eigener Menüpunkt statt Bismarck-Unterreiter —
   Kalender ist die einzige Funktion im Plugin ganz ohne KI-Beteiligung,
   gehört konzeptionell nicht in den KI-augmentierten Werkraum). Bisher nur
   ein Reiter, Gerüst für später (Wähl-/Rudel-Mechanik könnte einen zweiten
   brauchen). */
function cluster_one_render_gregor_tabs(string $current): void {
    /* v0.3.16: Die drei Reiter — in 0.3.12 gebaut, aber NIE in die Datei
       gelangt: die Ersetzung brach ab, und weil kein Zeuge die Reiterleiste
       liest, blieb alles gruen. Der Events-Reiter existierte im Code und war
       ueber kein Element erreichbar; Christian fand sein Treffen deshalb
       nirgends. */
    $base = admin_url('admin.php?page=cluster_one_gregor');
    /* 28.10.2026 (Neo), Boden 1.18.0: die eigene Reihe traegt die Herkunft mit,
       sonst wechselte die Ueberschrift beim Klick auf „Ereignisse" vom Haus, aus
       dem man kam, auf das erste nach 'nr'. Geriegelt: ein aelterer Boden kennt
       die Funktion nicht, dann bleibt die Adresse nackt wie bis 1.17.7. */
    if (function_exists('cluster_one_mit_herkunft')) {
        $base = cluster_one_mit_herkunft($base);
    }
    $c1_reiter = [
        ['label' => __('Create', 'cluster-one-kleinrock'), 'url' => $base,                 'active' => $current === 'calendar'],
        ['label' => __('Events', 'cluster-one-kleinrock'), 'url' => $base . '&tab=events', 'active' => $current === 'events'],
        /* 28.09.2026 — DER REITER STEHT IMMER, anders als Archimedes.
           Ein leerer Vergangenheits-Reiter ist selbst eine Auskunft: es ist
           noch nichts vorbei. Archimedes versteckt sich, weil er von einem
           Netz spricht, das es vielleicht nicht gibt; die Vergangenheit gibt
           es auf jedem Knoten. */
        ['label' => __('Past', 'cluster-one-kleinrock'), 'url' => $base . '&tab=vergangenheit', 'active' => $current === 'vergangenheit'],
    ];
    /* v1.7.0 (H143): Der Archimedes-Reiter steht nur da, wenn es andere Knoten
       gibt — oder schon etwas von ihnen. Bis 1.6.0 stand er immer, und ein
       reiner Bismarck-Knoten bekam einen Reiter fuer ein Netz, das er nicht
       hat. Kein Fehler, keine offene Naht, nur eine Auskunft, die nie
       stimmen konnte. */
    if (cluster_one_archimedes_sichtbar()) {
        $c1_reiter[] = ['label' => 'Archimedes', 'url' => $base . '&tab=archimedes', 'active' => $current === 'archimedes'];
    }
    /* 07.10.2026 — DIE EREIGNISSEITE BEKOMMT IHREN EIGENEN REITER.
       Bis heute rief sie diese Leiste mit 'calendar' — also stand „Erstellen"
       aktiv da, auf einer Seite, auf der man nichts erstellt. Christian hat
       nach einem eigenen Reiter gefragt; der Grund dafuer ist nicht Schmuck,
       sondern dass die Leiste sonst LUEGT.
           Ein aktiver Reiter, der woanders hinzeigt, ist schlimmer
           als keiner.
       ER STEHT NUR, WENN MAN DRAUFSTEHT, und zeigt auf sich selbst: eine
       Ereignisseite ohne Ereignis gibt es nicht, also waere ein Weg dorthin
       aus dem Kalender heraus ein Weg ins Leere. */
    if ($current === 'event') {
        /* ZEIGT AUF SICH SELBST, nicht auf nichts. Ein leeres href waere im
           Browser dieselbe Seite, aber im Quelltext eine Luecke, und ein
           Reiter ohne Ziel ist kein Reiter.
               Eine Flaeche, die aussieht wie ein Weg, muss einen haben,
               und sei es der zurueck auf sich. */
        $c1_eid_r = isset($_GET['event']) ? sanitize_text_field(wp_unslash($_GET['event'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        $c1_ev_url = admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode($c1_eid_r));
        if (function_exists('cluster_one_mit_herkunft')) {
            $c1_ev_url = cluster_one_mit_herkunft($c1_ev_url); /* 28.10.2026, Boden 1.18.0: siehe $base oben */
        }
        $c1_reiter[] = [
            'label'  => __('Meeting', 'cluster-one-kleinrock'),
            'url'    => $c1_ev_url,
            'active' => true,
        ];
    }
    cluster_one_render_tabs($c1_reiter);
}

/** v0.3.12: Wer darf welche Art Treffen anlegen?

   VIRTUELL (ein Link, knoten-intern) darf jeder, der hier handeln darf — sich
   zum Arbeiten zu verabreden ist keine Machtfrage.

   PHYSISCH bleibt SysOp-Sache, egal ob knotenweit oder knotenuebergreifend
   (Archimedes). Ein Ort ist eine Ansage an alle. Wer sich privat treffen will,
   klaert das per PN und nimmt sich ein Zimmer. */
/** KANN AUF DIESEM KNOTEN UEBERHAUPT VIRTUELL GETROFFEN WERDEN? (07.10.2026)
 *
 * Christian: „Virtuell darf aber nur moeglich zum Waehlen werden, wenn
 * Bismarck auf dem Knoten existiert." Begruendung aus seinem Satz vom 28.09.:
 * virtuell heisst KNOTEN-INTERN, und dafuer brauchen die Leute des Knotens
 * einen Ort, an dem sie miteinander reden — das ist Bismarcks Sache.
 *
 * GEFRAGT WIRD NACH DER FUNKTION, NICHT NACH DEM ORGAN — dieselbe Bauart wie
 * cluster_one_kalender_weg(). Und NICHT nach cluster_one_get_rooms(): das
 * steht in speicher.php und faehrt bei Kleinrock mit; es gibt auf einem
 * Knoten ohne Bismarck eine leere Liste zurueck und beantwortet die Frage
 * also mit „ja".
 *     Eine Funktion, die ueberall da ist, taugt nicht als Frage danach,
 *     wer da ist.
 * DIE FUNKTION HEISST cluster_one_room_create_do, und das ist eine
 * BERICHTIGUNG VOM 07.10.2026, zwei Stunden nach der ersten Fassung:
 *
 *     Ich habe mit `grep "function cluster_one_room_create"` gemessen und
 *     „kleinrock 0, bismarck 1" bekommen. Getroffen hat das Sieb aber
 *     cluster_one_room_create_do — als PRAEFIX. Die Funktion, nach der ich
 *     dann gefragt habe, gibt es nicht; function_exists() war auf einem
 *     Knoten MIT Bismarck falsch, und das Kreuz stand nie da.
 *     Christian hat es auf dem Schirm gesehen, nicht ich im Code.
 *
 *         Ein zu weites Sieb findet, was nicht da ist, so wie ein zu
 *         enges uebersieht, was da ist.             (R-SIEB-2, Hollerith)
 *
 *     Die Regel stand zu diesem Zeitpunkt schon geschrieben — von
 *     Hollerith, eine Runde vorher, ueber seinen eigenen Fehler. Ich bin
 *     einen Zug danach hineingetreten.
 *
 * NEU GEMESSEN, mit Wortgrenze (`function <name>\b`), an den Paketen
 * 0.2.32:
 *     cluster_one_room_create_do     kleinrock 0 · bismarck 1
 *     cluster_one_room_name_exists   kleinrock 0 · bismarck 1
 *     cluster_one_get_rooms          kleinrock 1 · bismarck 1   <- taugt nicht
 *
 * GENOMMEN WIRD create_do, und nicht eines der anderen: es ist die
 * Funktion, die einen Raum ANLEGT. Wer fragt, ob virtuell getroffen werden
 * kann, fragt, ob ein Ort zum Reden entstehen kann — nicht, ob einer
 * existiert und nicht, ob eine Seite ihn zeichnen koennte.
 */
function cluster_one_virtuell_moeglich(): bool {
    return function_exists('cluster_one_room_create_do');
}

function cluster_one_may_create_event(bool $physisch): bool {
    if (!cluster_one_can_access()) {
        return false;
    }
    /* 06.10.2026 — CHRISTIANS ENTSCHEIDUNG HEBT DIE TRENNUNG AUF.
     *
     * „Ja, ein WP-User darf auch einen Ort vorschlagen. Aber natuerlich kann
     *  der SysOp das editieren."
     *
     * Der Satz von v0.3.12 steht oben weiter im Kopf dieser Funktion und wird
     * NICHT geloescht: wer in einem Jahr liest, soll beide finden und sehen,
     * welcher gilt. Was ihn aufhebt, ist nicht ein besseres Argument, sondern
     * ein zweites Werkzeug — EDITIEREN. Ein Ort ist eine Ansage an alle; sie
     * darf jetzt jeder machen, weil sie jetzt jemand zuruecknehmen kann.
     *
     *     Eine Erlaubnis wird nicht dadurch harmlos, dass man sie
     *     einschraenkt, sondern dadurch, dass es einen Weg zurueck gibt.
     *
     * DER PARAMETER BLEIBT, obwohl er nichts mehr entscheidet. Zwei Rufer
     * uebergeben ihn (das Ortsfeld und der Anlege-Riegel), und ein Parameter,
     * der heute ueberall dasselbe ergibt, ist die Naht, an der die Trennung
     * zurueckkommt, falls sie zurueckkommen soll. Ihn zu entfernen hiesse,
     * zwei Rufstellen zu aendern, um eine Zeile zu sparen.
     */
    unset($physisch);
    return true;
}

/** DIE LEISTEN DER HAEUSER, DIE EINE FAUST-SEITE BEANSPRUCHEN — und der
 * Satz darunter. EINE Funktion fuer BEIDE Seiten.
 *
 * CHRISTIANS BEFUND VOM 18.09., auf der DEV:
 *
 *     „Auffaellig ist, dass bei Gregor in Kleinrock keine Tabs von
 *      Kleinrock und Kalender keine Tabs von Bismarck hat."
 *
 * Gemessen:  cluster_one_render_gregor_page()        nav-tab 0
 *            cluster_one_render_gregor_event_page()  nav-tab 0
 *
 * ER HATTE RECHT, UND ES SIND ZWEI SEITEN, NICHT EINE. Bismarck hat beim
 * Melden seiner Seiten gefragt, ob cluster_one_gregor_event dazugehoert —
 * und ich habe die zweite Seite erst beim Bauen dieser Zeile gefunden.
 *
 *     Wer eine versteckte Seite sichtbar macht, gibt ihr einen Weg
 *     hinein. Den Weg hinaus muss er einzeln dazulegen — und wenn es
 *     zwei Seiten sind, zweimal.
 *
 * DESHALB EINE FUNKTION UND KEIN ZWEIMAL EINGEBAUTER BLOCK. Derselbe
 * Gedanke wie bei cluster_one_zahlenzeile() heute Nacht:
 *
 *     Eine Darstellung ohne Namen kann kein Zeuge anfassen.
 *
 * SIE ZEIGT BEIDE LEISTEN, nicht die des Hauses, aus dem man kam.
 * Christians Entscheidung, und sie ist die billigere: ein Lesezeichen auf
 * diese Seite hat kein „woher", ein zweiter Browser-Reiter auch nicht.
 *
 *     Eine Regel ist billiger als zwei. Und eine Herkunft, die man
 *     raten muss, ist keine.
 *
 * WER SIE BEANSPRUCHT, SAGT DER SAMMLER — derselbe, der die Ueberschrift
 * bestimmt. Eine Wahrheit, drei Leser.                          (Weg D)
 *
 * WER NICHT ANTWORTET, BEKOMMT KEINE LEISTE. Ein Haus, das den Sammler
 * noch nicht bedient, verhaelt sich wie bisher.                (R-ZUG-2)
 *
 * @param string $c1_seite  der Seiten-Slug, dessen Leisten gezeichnet werden
 */
if (!function_exists('cluster_one_faust_leisten')) {
function cluster_one_faust_leisten(string $c1_seite): void {
    $c1_haeuser = function_exists('cluster_one_seite_ansprueche')
        ? cluster_one_seite_ansprueche($c1_seite)
        : array();

    if ($c1_haeuser === array() || !function_exists('do_action')) {
        return;
    }
    /* 28.10.2026 (Neo), Boden 1.18.0 — NUR DIE LEISTE DES HAUSES, VON DEM DER
       LESER KOMMT. Nemesus' Entscheidung vom 28.10. (Punkt 2): Kalender und
       Knoten-Seiten tragen oben die Leiste und in der Ueberschrift den Namen
       des Produkts, von dem man kam (?von=, cluster_one_herkunft). Bis 1.17.7
       lief die Schleife darunter ueber ALLE tragenden Haeuser, nach 'nr' — und
       zeichnete damit zwei Leisten, wo der Leser an einem Ort steht. Die
       Schleife bleibt als Rueckfall fuer einen aelteren Boden ohne Herkunft
       stehen (R-ZUG-2): sie zeichnet dann, was sie immer gezeichnet hat. */
    if (function_exists('cluster_one_herkunft')) {
        $c1_von = cluster_one_herkunft();
        if ($c1_von !== '' && in_array($c1_von, $c1_haeuser, true)) {
            do_action('cluster_one_leiste', $c1_von, $c1_seite);
            return;
        }
    }
    foreach ($c1_haeuser as $c1_wirt) {
        /* DIE FORM: das Haus zeichnet auf Zuruf, und nur, wenn es
           gemeint ist. Eine AKTION, kein Filter — hier wird
           gezeichnet, nicht gesammelt.

               add_action('cluster_one_leiste', function ($c1_w, $c1_s) {
                   if ($c1_w !== cluster_one_kasper_wirt(__FILE__)) { return; }
                   cluster_one_render_mesh_tabs('');
               }, 10, 2); */
        do_action('cluster_one_leiste', $c1_wirt, $c1_seite);
    }
}}

/**
 * v1.0.5 — DER KALENDERSATZ. Eigener Name, eigene Grenze.
 *
 * BISMARCKS FUND, und er ist eine Meldung ueber MEINE Datei. Er hat beim
 * Bauen versehentlich cluster_one_faust_leisten('cluster_one_mesh') gerufen
 * und bekam den Satz vom Kalender auf einer Seite, die keiner ist. Er
 * schreibt selbst, was es ist und was es nicht ist:
 *
 *     „keine Meldung ueber den Knoten, sondern ueber die Reichweite
 *      der Funktion, falls einmal eine dritte Faust-Seite dazukommt
 *      oder jemand anders sie ruft."
 *
 * Heute ist es nicht erreichbar — gerufen wird nur von den zwei
 * Kalenderseiten. Das ist kein Grund, es stehen zu lassen:
 *
 *     Ein Satz, der nur fuer zwei Seiten gilt, darf nicht in einer
 *     Funktion wohnen, die jede Seite nimmt.
 *
 * WARUM ZWEI NAMEN UND NICHT EIN PARAMETER: ein Parameter wird von einer
 * alten Datei still geschluckt. Am 17.09. hat cluster_one_node_reset(false)
 * an einem alten kern.php GERAEUMT, weil PHP den unbekannten zweiten
 * Parameter wegwarf. Seitdem gilt: ein NAME, kein Parameter. (R-RIEGEL-2)
 *
 * WARUM KEINE LISTE DER KALENDERSEITEN HIER: sie stuende neben den zwei
 * Rufstellen, und das waeren zwei Wahrheiten ueber dieselbe Sache (P1-12).
 *
 *     Wer ruft, weiss, dass er ein Kalender ist.
 *
 * @param string $c1_seite  der Seiten-Slug, dessen Traeger gezaehlt werden
 */
if (!function_exists('cluster_one_faust_kalendersatz')) {
function cluster_one_faust_kalendersatz(string $c1_seite): void {
    $c1_haeuser = function_exists('cluster_one_seite_ansprueche')
        ? cluster_one_seite_ansprueche($c1_seite)
        : array();

    /* DER SATZ SAGT, WIE VIELE SIE TRAGEN.
     *
     * Christian hat am 18.09. gemessen, was kein Zeuge gemessen hat —
     * nicht am Code, am Gebrauch:
     *
     *     Kleinrock allein   wirft Termine ueber Knotengrenzen
     *     Bismarck allein    haelt sie im eigenen Knoten
     *     beide              stimmen intern ab UND spiegeln nach draussen
     *
     * DIESE SEITE KANN DAVON NUR DIE HAELFTE MESSEN. Sie weiss, WIEVIELE
     * Haeuser sie tragen und WIE SIE HEISSEN. Sie weiss NICHT, welches
     * Haus welche Haelfte kann — das weiss nur das Haus selbst, und
     * gefragt hat es bisher niemand.
     *
     *     Ein Satz, der mehr behauptet als seine Quelle hergibt, ist
     *     keine Auskunft. Er ist eine Vermutung in der Schriftart
     *     einer Auskunft.
     *
     * Also sagt sie, was sie gemessen hat, und nicht mehr. */
    if (count($c1_haeuser) !== 1) {
        return;
    }
    $c1_alle = function_exists('cluster_one_produkte') ? cluster_one_produkte() : array();
    $c1_name = (is_array($c1_alle) && isset($c1_alle[$c1_haeuser[0]]['name']))
        ? (string) $c1_alle[$c1_haeuser[0]]['name'] : '';
    if ($c1_name === '') {
        return;
    }
    echo '<p class="description" style="max-width:720px;">' . esc_html(sprintf(
        /* translators: %s: name of the one product that carries this page */
        __('This calendar is carried by two products. Right now only %s is here — so it can do less than it could.', 'cluster-one-kleinrock'),
        $c1_name
    )) . '</p>';
}}

function cluster_one_render_gregor_page(): void {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    /* 1.0.19 (28.10.2026, Neo) — DIE SEITE KOMMT IN EINEM STUECK.
       Nemesus auf der Main: im Reiter Archimedes stand erst das Wort „Details"
       an der Stelle des Fragezeichens, nach etwa einer Sekunde das Fragezeichen.
       Das Wort kommt vom Browser: die Hilfeblase ist ein <details> mit einem
       <summary>, und solange das <summary> noch nicht angekommen ist, zeigt
       Chrome seine eigene Beschriftung. Angekommen war es nicht, weil der
       Archimedes-Reiter NACH der Ueberschrift live die Einladungen der
       gepaarten Knoten holt (cluster_one_fetch_meetings, bis 8 s je Kante) —
       der Server schickte den fertigen Anfang und wartete, der Browser zeigte
       die halbe Seite, und der Schnitt lag in der Blase. Immer an derselben
       Stelle, weil der Seitenanfang immer gleich lang ist.
           Eine halbe Seite ist keine Seite mit weniger Inhalt; sie ist eine
           Seite mit anderem Inhalt.
       Darum der Puffer: nichts verlaesst den Server, bevor die Seite fertig
       ist. Die Wartezeit bleibt dieselbe — sie wird nur nicht mehr als
       falscher Inhalt sichtbar. Drei Ausgaenge, dreimal ob_end_flush(). */
    ob_start();
    if (isset($_GET['saved'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Saved.', 'cluster-one-kleinrock') . '</p></div>';
    }
    if (isset($_GET['ev_err']) && (string) $_GET['ev_err'] === 'past') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        echo '<div class="notice notice-warning is-dismissible"><p>'
           . esc_html__('That day has passed. A meeting can only be proposed for today or later — we cannot travel backwards yet.', 'cluster-one-kleinrock')
           . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php /* v1.7.0 (H143): ohne andere Knoten heisst die Seite, was sie ist. */
                  echo cluster_one_page_h1(cluster_one_archimedes_sichtbar() ? 'Gregor / Archimedes' : 'Gregor'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
<?php
/* v1.0.5: zwei Namen, zwei Arbeiten. Wer hier ruft, weiss, dass er ein
   Kalender ist — deshalb steht die Grenze des Satzes an der Rufstelle und
   nicht in einer Liste. (Bismarcks Fund, M102) */
cluster_one_faust_leisten('cluster_one_gregor');
cluster_one_faust_kalendersatz('cluster_one_gregor');

$c1_tab = 'calendar';
if (isset($_GET['tab']) && in_array((string) wp_unslash($_GET['tab']), array('events', 'vergangenheit', 'archimedes'), true)) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $c1_tab = (string) wp_unslash($_GET['tab']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler; Weissliste eine Zeile hoeher (in_array); hier nur die Uebernahme des gepruefteten Wortes
}
/* v1.7.0 (H143): Ein Reiter, den es nicht gibt, ist auch ueber die Adresse
   nicht erreichbar. Sonst waere das Verstecken eine Sichtbarkeitsregel und
   keine Auskunft — und die URL bliebe die zweite Wahrheit. */
if ($c1_tab === 'archimedes' && !cluster_one_archimedes_sichtbar()) { $c1_tab = 'calendar'; }
cluster_one_render_gregor_tabs($c1_tab);
if ($c1_tab === 'calendar') :
    /* v0.3.12: Der Kalender IST das Formular. Ein Klick auf einen Tag traegt
       das Datum unten ein und legt NICHTS an — ein Fehlklick soll kein Event
       erzeugen. */
    $c1_pick = (isset($_GET['pick']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) wp_unslash($_GET['pick']))) ? (string) wp_unslash($_GET['pick']) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler; der Wert wird nur mit einer Weissliste verglichen (auf dieser Zeile) und sonst nicht benutzt
    cluster_one_render_gregor_calendar();
    cluster_one_render_gregor_create_form($c1_pick);
    echo '</div>';
    ob_end_flush(); // 1.0.19: Ausgang 1 von 3
    return;
endif;

if ($c1_tab === 'archimedes') :
    cluster_one_render_archimedes_tab();
    echo '</div>';
    ob_end_flush(); // 1.0.19: Ausgang 2 von 3 — der, um den es ging
    return;
endif;

/* v0.3.12: Reiter "Events" — alles, was schon laeuft.
   28.09.2026: UND Reiter "Past" — was die Zeit ueberholt hat.

   EINE TABELLE, ZWEI LESER. Getrennt wird durch cluster_one_gregor_vorbei(),
   und der Riegel ist derselbe in beide Richtungen — deshalb der Vergleich
   gegen ($c1_tab === 'vergangenheit') statt zweier Schleifen. Zwei Schleifen
   waeren zwei Bedingungen ueber dieselbe Sache, und die zweite veraltet an
   dem Tag, an dem jemand die erste aendert.               (P1-12) */
$c1_vgh    = ($c1_tab === 'vergangenheit');
$c1_events = array();
foreach (cluster_one_get_gregor_events() as $c1_kand) {
    if (!is_array($c1_kand)) { continue; }
    if (cluster_one_gregor_vorbei($c1_kand) === $c1_vgh) { $c1_events[] = $c1_kand; }
}
?>
        <h2 style="margin-top:1em;"><?php echo esc_html($c1_vgh ? __('Past', 'cluster-one-kleinrock') : __('Events', 'cluster-one-kleinrock')); ?></h2>
<?php if ($c1_vgh) : ?>
        <p style="max-width:720px;opacity:.85;"><?php esc_html_e('Meetings whose day has gone by. Nothing is stored differently here — the day simply passed, and it does not come back. What is of no further use can be deleted.', 'cluster-one-kleinrock'); ?></p>
<?php endif; ?>
<?php if (empty($c1_events)) : ?>
        <p style="opacity:.7;"><?php echo esc_html($c1_vgh ? __('Nothing has passed yet.', 'cluster-one-kleinrock') : __('No event planned yet. Pick a day in the Create tab.', 'cluster-one-kleinrock')); ?></p>
<?php else : ?>
        <table class="widefat striped" style="max-width:960px;">
            <thead><tr>
                <th><?php esc_html_e('Event', 'cluster-one-kleinrock'); ?></th>
                <th style="width:12em;"><?php esc_html_e('Day', 'cluster-one-kleinrock'); ?></th>
                <th style="width:10em;"><?php esc_html_e('Kind', 'cluster-one-kleinrock'); ?></th>
                <th><?php esc_html_e('Created by', 'cluster-one-kleinrock'); ?></th>
                <th style="text-align:right;width:6em;"><?php esc_html_e('Actions', 'cluster-one-kleinrock'); ?></th>
            </tr></thead>
            <tbody>
<?php foreach ($c1_events as $c1_ev) :
    $c1_eid = (string) ($c1_ev['id'] ?? '');
    $c1_sch = isset($c1_ev['scheduling']) && is_array($c1_ev['scheduling']) ? $c1_ev['scheduling'] : array();
    $c1_fest = ((string) ($c1_sch['status'] ?? '') === 'locked') ? (string) ($c1_sch['locked_date'] ?? '') : '';
    $c1_art = trim((string) ($c1_ev['place'] ?? '')) !== ''
        ? (!empty($c1_ev['archimedes']) ? __('Archimedes', 'cluster-one-kleinrock') : __('physical', 'cluster-one-kleinrock'))
        : (trim((string) ($c1_ev['link'] ?? '')) !== '' ? __('virtual', 'cluster-one-kleinrock') : __('open', 'cluster-one-kleinrock'));
?>
                <tr>
                    <td style="white-space:nowrap;max-width:24em;overflow:hidden;text-overflow:ellipsis;">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode($c1_eid))); ?>" title="<?php echo esc_attr((string) ($c1_ev['name'] ?? '')); ?>"><?php echo esc_html((string) ($c1_ev['name'] ?? '')); ?></a>
                    </td>
                    <td style="white-space:nowrap;"><?php
                        if ($c1_fest !== '') {
                            echo '<span style="color:#1a7f37;font-weight:600;" title="' . esc_attr__('Locked by the SysOp', 'cluster-one-kleinrock') . '">● </span>' . esc_html($c1_fest);
                        } elseif (cluster_one_gregor_vorbei($c1_ev)) {
                            /* 28.09.2026: ein vergangener Termin OHNE Schnitt.
                               „still moving" waere hier eine Luege — es bewegt
                               sich nichts mehr. Also der spaeteste Tag, den
                               jemand vorgeschlagen hat, und daneben, was fehlt. */
                            echo '<span style="opacity:.55;" title="' . esc_attr__('No day was ever locked', 'cluster-one-kleinrock') . '">'
                               . esc_html(cluster_one_gregor_tag($c1_ev)) . '</span>';
                        } else {
                            $c1_n = count((array) ($c1_sch['markers'] ?? array()));
                            /* translators: %d: Anzahl der Markierungen. */
                            echo '<span style="opacity:.6;">' . esc_html(sprintf(_n('%d mark, still moving', '%d marks, still moving', $c1_n, 'cluster-one-kleinrock'), $c1_n)) . '</span>';
                        }
                    ?></td>
                    <td style="opacity:.85;white-space:nowrap;"><?php echo esc_html($c1_art); ?></td>
                    <td style="opacity:.8;white-space:nowrap;"><?php echo esc_html((string) ($c1_ev['creator'] ?? '')); ?></td>
                    <td style="text-align:right;white-space:nowrap;">
<?php /* 06.10.2026: derselbe Riegel wie im Handler, EINE Funktion, zwei Leser.
         Wer nicht loeschen darf, sieht keinen Papierkorb — statt einen zu
         sehen und 403 zu bekommen. */ ?>
<?php if (cluster_one_gregor_darf_loeschen($c1_ev)) : ?>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline-block;">
                            <?php wp_nonce_field('cluster_one_gregor_event_delete'); ?>
                            <input type="hidden" name="action" value="cluster_one_gregor_event_delete">
                            <input type="hidden" name="event" value="<?php echo esc_attr($c1_eid); ?>">
                            <button type="submit" style="background:none;border:0;padding:0 .3em;cursor:pointer;line-height:1;color:#b32d2e;"
                                    title="<?php echo esc_attr__('Delete event', 'cluster-one-kleinrock'); ?>" aria-label="<?php echo esc_attr__('Delete event', 'cluster-one-kleinrock'); ?>"
                                    onclick="return confirm('<?php echo esc_js(__('Delete this event? The marks people set are lost with it.', 'cluster-one-kleinrock')); ?>');">
                                <span class="dashicons dashicons-trash" aria-hidden="true" style="font-size:20px;width:20px;height:20px;"></span>
                            </button>
                        </form>
<?php endif; ?>
                    </td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
<?php endif; ?>
    </div>
    <?php
    ob_end_flush(); // 1.0.19: Ausgang 3 von 3 (Ereignisse, Vergangenheit)
}

/** Darf DIESER Mensch DIESES Treffen loeschen? (06.10.2026)
 *
 * Christian: „Jeder loescht nur seinen Eintrag. Nur der SysOp darf auch die
 * von anderen loeschen."
 *
 * EIN TREFFEN OHNE `creator_id` BLEIBT SYSOP-SACHE, und das ist keine Haerte,
 * sondern das Einzige, was ehrlich ist: vor heute hat niemand die Nummer
 * mitgeschrieben. Wer sie aus dem NAMEN erraten wollte, wuerde Loeschrechte
 * aus einer Zeichenkette ableiten, die zwei Menschen tragen koennen.
 *
 *     Ein Recht, das von einem Feld abhaengt, das aelteren Eintraegen fehlt,
 *     faengt dort an, wo das Feld anfaengt — und sagt es.
 *
 * Darum sagt es die Seite auch: der Papierkorb fehlt bei alten Treffen fuer
 * jeden ausser dem SysOp, statt beim Druecken 403 zu werfen.    (P1-12)
 */
function cluster_one_gregor_darf_loeschen(array $c1_ev): bool {
    if (!cluster_one_can_access()) {
        return false;
    }
    if (cluster_one_is_sysop()) {
        return true;
    }
    $c1_wer = (int) ($c1_ev['creator_id'] ?? 0);
    return $c1_wer > 0 && $c1_wer === (int) get_current_user_id();
}

add_action('admin_post_cluster_one_gregor_event_delete', function () { // v0.0.65
    /* 06.10.2026: der Riegel fragt jetzt das TREFFEN und nicht nur den
       Menschen — deshalb steht er UNTER dem Suchen und nicht darueber. Bis
       hierher stand hier `|| !cluster_one_is_sysop()`. */
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_gregor_event_delete');
    $eid    = isset($_POST['event']) ? sanitize_text_field(wp_unslash($_POST['event'])) : '';
    $events = cluster_one_get_gregor_events();
    foreach ($events as $k => $ev) {
        if ($eid !== '' && ((string) ($ev['id'] ?? '') === $eid || (string) $k === $eid)) {
            if (!cluster_one_gregor_darf_loeschen((array) $ev)) {
                wp_die('-1', 403);
            }
            cluster_one_event_emit(array( // Türereignis — die Löschung überlebt den Container
                'type' => 'gregor_event_deleted', 'container_type' => 'event', 'container_id' => $eid,
                'actor_id' => cluster_one_actor_id_human(),
                'content_hash' => hash('sha256', (string) ($ev['name'] ?? '')),
            ), true);
            if (isset($events[$k])) { cluster_one_archimedes_change_signal($events[$k]); } // v0.0.100: Absage pingt
            unset($events[$k]);
            cluster_one_save_gregor_events($events);
            break;
        }
    }
    wp_safe_redirect(wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=cluster_one_gregor'));
    exit;
});

add_action('admin_post_cluster_one_gregor_event_create', function () {
    /* 06.10.2026 — HIER STANDEN ZWEI WAHRHEITEN UEBER DIESELBE SACHE.
     *
     * Das Formular fragt seit v0.3.12 cluster_one_may_create_event() und
     * graut einem Nicht-SysOp nur das ORTSFELD aus (:1547). Dieser Handler
     * fragte daneben cluster_one_is_sysop() und warf 403 — ein Beschirmter
     * sah also ein Formular, das ihm den Link erlaubt, und bekam beim
     * Absenden die Tuer vor der Nase.
     *
     *     Ein Schirm, der mehr erlaubt als der Server, ist keine
     *     Oberflaeche. Er ist ein Versprechen, das jemand anders
     *     bricht.                                          (P1-12)
     *
     * CHRISTIAN, 06.10.: „Vorschlaege fuer alle auf der weissen Liste."
     * Das ist genau diese eine Wahrheit — und sie steht schon in der
     * Funktion. Also wird sie GEFRAGT und nicht ein zweites Mal getippt.
     *
     * DIE TRENNUNG virtuell/physisch BLEIBT, wie v0.3.12 sie begruendet
     * hat („Ein Ort ist eine Ansage an alle"). Wer sie aendern will,
     * aendert cluster_one_may_create_event() — eine Zeile, eine Stelle.
     *
     * UND DIE ART WIRD HIER GENAUSO BESTIMMT WIE UNTEN: ein gefuellter
     * Ort gewinnt (v0.1.4). Stuende hier eine eigene Regel dafuer, haette
     * der Riegel eine andere Vorstellung vom Treffen als der Bau.
     */
    $c1_physisch = trim((string) (isset($_POST['place']) ? wp_unslash($_POST['place']) : '')) !== ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nur auf Leerheit geprueft (!== ''), der Wert selbst wird hier nicht benutzt
    if (!cluster_one_may_create_event($c1_physisch)) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_gregor_event_create');

    $name = isset($_POST['name']) ? trim(sanitize_text_field(wp_unslash($_POST['name']))) : '';
    $sugg = isset($_POST['suggested_by']) ? trim(sanitize_text_field(wp_unslash($_POST['suggested_by']))) : '';
    $back = admin_url('admin.php?page=cluster_one_gregor');

    if ($name === '') {
        wp_safe_redirect($back);
        exit;
    }

    // v0.0.76: Eröffnungs-Vorschlag = erster Marker des SysOps.
    $proposal = isset($_POST['proposal']) ? trim(sanitize_text_field(wp_unslash($_POST['proposal']))) : '';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $proposal) || $proposal < current_time('Y-m-d')) {
        // v0.0.96: keine Treffen in der Vergangenheit — Zeitreisen können
        // wir leider noch nicht (heute ist erlaubt).
        // v0.3.16: und es sagt jetzt, WARUM nichts passiert ist. Vorher leitete
        // es kommentarlos zurueck, und man stand vor einer Seite, die aussah
        // wie vorher.
        wp_safe_redirect(add_query_arg('ev_err', 'past', $back));
        exit;
    }
    $eid    = cluster_one_gregor_event_id();
    $events = cluster_one_get_gregor_events();
    // v0.1.4: Ort ODER Link — der Modus ist, welches Feld gefüllt ist. Ein Ort
    // verlangt Anreise und begrenzt sich damit selbst; ein Link nicht, deshalb
    // bleibt er knoten-intern. Beides gleichzeitig ergibt keinen Sinn: Ort gewinnt.
    $c1_place = isset($_POST['place']) ? trim(sanitize_text_field(wp_unslash($_POST['place']))) : '';
    $c1_link  = isset($_POST['link'])  ? esc_url_raw(trim(wp_unslash($_POST['link']))) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (esc_url_raw), den der Pruefer nicht kennt
    /* 07.10.2026 — DAS KREUZ ENTSCHEIDET, nicht mehr das gefuellte Feld.
       Die alte Regel („Ort gewinnt", v0.1.4) bleibt als RUECKFALL fuer ein
       Formular ohne `art` — ein alter Entwurf im Browser soll nicht
       stillschweigend etwas anderes anlegen als er zeigt.
       UND VIRTUELL GEHT NUR, WO ES MOEGLICH IST: dieselbe Frage wie im
       Formular, einmal gestellt (cluster_one_virtuell_moeglich). Wer
       `art=virtuell` ueber die Adresszeile schickt, wo kein Bismarck liegt,
       bekommt ein physisches Treffen und keine stille Ausnahme. */
    $c1_art = isset($_POST['art']) ? sanitize_key(wp_unslash($_POST['art'])) : '';
    if ($c1_art === 'virtuell' && cluster_one_virtuell_moeglich()) {
        $c1_place = '';
    } elseif ($c1_art === 'physisch') {
        $c1_link = '';
    } elseif ($c1_place !== '') {
        $c1_link = '';
    }
    /* Bei einem virtuellen Treffen gibt es nichts mitzubringen. */
    if ($c1_place === '') { $_POST['plus_count'] = 0; }
    $c1_time  = isset($_POST['time']) ? trim(sanitize_text_field(wp_unslash($_POST['time']))) : '';
    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $c1_time)) { $c1_time = ''; }
    /* v1.0.3 — WAS ZUM HENKER SOLL DA STATTFINDEN?
       Christians Feld. Ein Treffen reiste bisher mit Name, Datum, Ort und
       Stunde — ein fremder SysOp sah „Sommerfest, 27.08., Hamburg" und sollte
       entscheiden, ob er hinfaehrt. EIN Feld, unabhaengig davon, ob das Treffen
       intern oder extern, physisch oder virtuell ist: die Art entscheidet sich
       an Ort-oder-Link, die Beschreibung interessiert das nicht.
       Deckel: cluster_one_sanitize_message() — 2000 Zeichen, wp_strip_all_tags,
       sanitize_textarea_field. Der Deckel dieses Hauses fuer fremden Text; eine
       zweite Zahl zu erfinden waere eine zweite Wahrheit. */
    $c1_besch = isset($_POST['beschreibung'])
        ? cluster_one_sanitize_message((string) wp_unslash($_POST['beschreibung'])) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (cluster_one_sanitize_message), den der Pruefer nicht kennt
    $events[] = array(
        'id'           => $eid,
        'name'         => $name,
        'beschreibung' => $c1_besch,
        'creator'      => cluster_one_human_speaker_label(),
        /* 06.10.2026 — DIE NUMMER NEBEN DEM NAMEN.
           `creator` ist ein NAME und taugt nicht zum Vergleichen: zwei
           Menschen koennen denselben Anzeigenamen tragen, und ein Mensch kann
           seinen aendern. Wer loeschen darf, wird an einer Nummer entschieden.
           SIE REIST NICHT: /meetings baut seine Antwort Feld fuer Feld
           (kleinrock.php:1266-1271) und nennt dieses Feld nicht. */
        'creator_id'   => (int) get_current_user_id(),
        'suggested_by' => $sugg,
        'place'        => $c1_place,
        'link'         => $c1_link,
        'time'         => $c1_time,
        'tz'           => wp_timezone_string(), // macht die Uhrzeit über Knoten hinweg eindeutig
        'linked_room'   => '',
        'linked_thread' => '',
        'created'      => time(),
        'scheduling'   => array( // v0.0.76: das Gregor-Spiel
            'status'      => 'open',
            /* v0.0.77 · 'ts' seit 06.10.2026: eine Marke muss sagen, WANN sie
               gesetzt wurde, sonst kann die Seite nicht wissen, ob sie vor
               oder nach einer Aenderung des Ortes entstand. Marken ohne 'ts'
               (alle von vor heute) bekommen keinen Hinweis — NICHT GEMESSEN
               ist besser als geraten. */
            'markers'     => array('wp_' . get_current_user_id() => array('date' => $proposal, 'plus' => isset($_POST['plus_count']) ? min(99, max(0, (int) $_POST['plus_count'])) : 0, 'ts' => time())),
            
            'declined'    => array(),
            'locked_date' => '',
            'locked_by'   => '',
        ),
    );
    cluster_one_save_gregor_events($events);

    wp_safe_redirect(admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode($eid)));
    exit;
});

/** Event-Detail-/Konfigurationsseite (v0.0.57): Anzeige für jeden auf der
   Weißliste, Bearbeiten nur SysOp — genau wie bei Bismarck-Räumen. */
function cluster_one_render_gregor_event_page(): void {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    $eid    = isset($_GET['event']) ? sanitize_text_field(wp_unslash($_GET['event'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $events = cluster_one_get_gregor_events();
    $event  = null;
    foreach ($events as $ev) {
        if ((string) ($ev['id'] ?? '') === $eid) {
            $event = $ev;
            break;
        }
    }
    $back = admin_url('admin.php?page=cluster_one_gregor');
    ?>
    <div class="wrap">
        <?php if (!empty($event['archimedes'])) : // v0.0.100: Archimedes-Event ?>
            <?php /* 18.10.2026 (Neo): Leser fuer `nodes=` (H151) — vier Ausgaenge
                     des Oeffnen/Schliessen-Knopfes, bisher ohne Empfaenger. */
            $c1_nodes_satz = array(
                'open'      => array('success', __('Opened for other nodes — the meeting now appears in this node’s public meetings list.', 'cluster-one-kleinrock')),
                'closed'    => array('info',    __('Closed again — other nodes no longer see this meeting.', 'cluster-one-kleinrock')),
                'not_ready' => array('warning', __('Not opened: finish the meeting here first — a node speaks outward only once it is done with itself.', 'cluster-one-kleinrock')),
                'kein_netz' => array('warning', __('Not opened: this node has no edge to any other node, so there is nobody to open it for.', 'cluster-one-kleinrock')),
            );
            $c1_nodes = isset($_GET['nodes']) ? sanitize_text_field(wp_unslash($_GET['nodes'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            if (isset($c1_nodes_satz[$c1_nodes])) : ?>
                <div class="notice notice-<?php echo esc_attr($c1_nodes_satz[$c1_nodes][0]); ?> is-dismissible"><p><?php echo esc_html($c1_nodes_satz[$c1_nodes][1]); ?></p></div>
            <?php endif; ?>
            <?php if (isset($_GET['rsvp'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
                <div class="notice notice-<?php echo $_GET['rsvp'] === 'ok' ? 'success' : 'warning'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?> is-dismissible"><p><?php
                    if ($_GET['rsvp'] === 'ok') { esc_html_e('Answer sent: our node is coming with the current head count.', 'cluster-one-kleinrock'); } // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
                    elseif ($_GET['rsvp'] === 'rang') { esc_html_e('Rung the host’s doorbell — they fetch the number from us themselves. No edge needed: answering an invitation must not require becoming friends.', 'cluster-one-kleinrock'); } // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
                    elseif ($_GET['rsvp'] === 'unreachable') { esc_html_e('The host did not answer. Their node may be down, or it speaks an older version that has no doorbell yet.', 'cluster-one-kleinrock'); } // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
                    elseif ($_GET['rsvp'] === 'no_edge') { esc_html_e('No edge to the origin node — we rang instead.', 'cluster-one-kleinrock'); } // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
                    else { esc_html_e('The origin node did not accept the answer. Try again later.', 'cluster-one-kleinrock'); }
                ?></p></div>
            <?php endif; ?>
            <div class="notice notice-info"><p>
                <?php /* translators: %s: Name des Herkunftsknotens. */ ?>
                <?php echo esc_html(sprintf(__('Archimedes meeting from node “%s” — the date is rock (it belongs to the origin node). Mark come/not-come and guests below.', 'cluster-one-kleinrock'), (string) ($event['archimedes']['node'] ?? $event['archimedes']['host'] ?? ''))); ?>
                <?php if (cluster_one_is_sysop()) : ?>
                    </p><form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:.4em 0 0;">
                        <?php wp_nonce_field('cluster_one_archimedes_rsvp'); ?>
                        <input type="hidden" name="action" value="cluster_one_archimedes_rsvp">
                        <input type="hidden" name="event" value="<?php echo esc_attr((string) ($event['id'] ?? '')); ?>">
                        <button type="submit" class="button" title="<?php echo esc_attr__('Sends “our node is coming with N people” to the origin node over the edge. Sending again appends over there (history, not overwrite). Close your own game first — a node speaks outward only once it is done with itself.', 'cluster-one-kleinrock'); ?>"><?php esc_html_e('Send answer: we are coming with N', 'cluster-one-kleinrock'); ?></button>
                    </form><p style="display:none;">
                <?php endif; ?>
            </p></div>
        <?php endif; ?>
        <h1><?php /* v1.7.0 (H143): ohne andere Knoten heisst die Seite, was sie ist. */
                  echo cluster_one_page_h1(cluster_one_archimedes_sichtbar() ? 'Gregor / Archimedes' : 'Gregor'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
<?php /* v1.0.5: zwei Namen, zwei Arbeiten — wie auf der Kalenderseite. */
      cluster_one_faust_leisten('cluster_one_gregor_event');
      cluster_one_faust_kalendersatz('cluster_one_gregor_event'); ?>
        <?php cluster_one_render_gregor_tabs('event'); /* 07.10.2026: nicht mehr 'calendar' */ ?>
        <p><a href="<?php echo esc_url($back); ?>">&larr; <?php esc_html_e('Back to events', 'cluster-one-kleinrock'); ?></a></p>
        <?php if (!$event) : ?>
            <p style="opacity:.7;"><?php esc_html_e('Event not found.', 'cluster-one-kleinrock'); ?></p>
        <?php else :
            $is_sysop = cluster_one_is_sysop();
            $rooms    = cluster_one_get_rooms();
            $threads  = cluster_one_get_forum_threads();
        ?>
            <h2 style="margin-top:1em;"><?php echo esc_html((string) ($event['name'] ?? '')); ?></h2>
            <p style="opacity:.75;">
                <?php /* translators: %s: Name des Erstellers. */ ?>
                <?php echo esc_html(sprintf(__('Created by %s', 'cluster-one-kleinrock'), (string) ($event['creator'] ?? ''))); ?>
                <?php if (trim((string) ($event['suggested_by'] ?? '')) !== '') : ?>
                    <?php /* translators: %s: Name des Vorschlagenden. */ ?>
                    · <?php echo esc_html(sprintf(__('suggested by %s', 'cluster-one-kleinrock'), (string) ($event['suggested_by'] ?? ''))); ?>
                <?php endif; ?>
            </p>

<?php // v0.1.4: Wo und wann. Ort ODER Link — welches Feld gefuellt ist, sagt den Modus.
// Waechterzeilen stehen flush-left wegen der Whitespace-Falle beim schliessenden PHP-Tag.
$c1_ort  = trim((string) ($event['place'] ?? ''));
$c1_link = trim((string) ($event['link'] ?? ''));
$c1_zeit = trim((string) ($event['time'] ?? ''));
$c1_zone = trim((string) ($event['tz'] ?? ''));
if ($c1_ort !== '' || $c1_link !== '' || $c1_zeit !== '') : ?>
            <p style="max-width:760px;">
<?php if ($c1_ort !== '') : ?>
                <strong><?php esc_html_e('Place:', 'cluster-one-kleinrock'); ?></strong> <?php echo esc_html($c1_ort); ?><br>
<?php elseif ($c1_link !== '') : ?>
                <strong><?php esc_html_e('Virtual, inside this node:', 'cluster-one-kleinrock'); ?></strong>
                <a href="<?php echo esc_url($c1_link); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html($c1_link); ?></a><br>
<?php endif; ?>
<?php if ($c1_zeit !== '') : ?>
                <strong><?php esc_html_e('Time:', 'cluster-one-kleinrock'); ?></strong> <?php echo esc_html($c1_zeit . ($c1_zone !== '' ? ' (' . $c1_zone . ')' : '')); ?>
<?php endif; ?>
            </p>
<?php endif; ?>

            <?php
            // v0.0.76: das Gregor-Spiel — Terminfindung. Alt-Ereignisse ohne
            // scheduling zeigen den Block schlicht nicht (kein Zwang).
            $c1_sched = isset($event['scheduling']) && is_array($event['scheduling']) ? $event['scheduling'] : null;
            if ($c1_sched !== null) :
                $c1_markers  = (array) ($c1_sched['markers'] ?? array());
                $c1_declined = array_map('strval', (array) ($c1_sched['declined'] ?? array()));
                $c1_locked   = (($c1_sched['status'] ?? 'open') === 'locked');
                $c1_me       = 'wp_' . get_current_user_id();
                $c1_i_play   = in_array(get_current_user_id(), cluster_one_gregor_humans(), true);
                // Gruppieren: Datum -> Namen (Marker tragen Namen, nicht nur
                // Zahlen — WoR lebt von sichtbarer Verantwortung; die Zahl
                // ist nur die Verdichtung. Nichts davon wandert je ins Label.)
                $c1_by_date = array();
                foreach ($c1_markers as $c1_k => $c1_m) {
                    $c1_by_date[cluster_one_gregor_marker_date($c1_m)][] = $c1_k;
                }
                ksort($c1_by_date);
                $c1_name_of = function ($key) {
                    $u = get_userdata((int) substr((string) $key, 3));
                    return $u ? $u->display_name : (string) $key;
                };
                $c1_pending = array();
                foreach (cluster_one_gregor_humans() as $c1_uid) {
                    $c1_k = 'wp_' . $c1_uid;
                    if (!isset($c1_markers[$c1_k]) && !in_array($c1_k, $c1_declined, true)) {
                        $c1_pending[] = $c1_name_of($c1_k);
                    }
                }
            ?>
<?php
            /* 06.10.2026 — DIE NACHRICHT NACH DEM EDITIEREN. Zwei Saetze aus
               zwei Werten, und der zweite ist der wichtige. */
            /* 18.10.2026 (Neo): hier stand $c1_ev — eine Variable, die es in
               dieser Funktion nie gab (sie heisst $event). PHP warnte, der
               Hinweis erschien nie. */
            $c1_chg = (int) ($event['changed'] ?? 0);
            if ($c1_chg > 0) :
                $c1_my_ts = (int) (is_array($c1_markers[$c1_me] ?? null) ? ($c1_markers[$c1_me]['ts'] ?? 0) : 0);
?>
            <div class="notice notice-info inline" style="max-width:52em;"><p>
                <?php echo esc_html(sprintf(
                    /* translators: 1: date, 2: name */
                    __('Changed on %1$s by %2$s.', 'cluster-one-kleinrock'),
                    date_i18n(get_option('date_format') . ' H:i', $c1_chg),
                    (string) ($event['changed_by'] ?? '')
                )); ?>
<?php           if ($c1_my_ts > 0 && $c1_my_ts < $c1_chg) : ?>
                <br><strong><?php esc_html_e('Your mark is from before that change. Place, time or link may no longer be what you agreed to.', 'cluster-one-kleinrock'); ?></strong>
<?php           endif; ?>
            </p></div>
<?php       endif; ?>
            <h3 style="margin-top:1.5em;"><?php esc_html_e('Finding the date', 'cluster-one-kleinrock'); ?></h3>
            <?php if ($c1_locked) : ?>
                <?php /* translators: %s: festgelegtes Datum. */ ?>
                <p style="font-size:1.05em;"><strong><?php echo esc_html(sprintf(__('Locked in: %s', 'cluster-one-kleinrock'), date_i18n(get_option('date_format'), strtotime((string) $c1_sched['locked_date'])))); ?></strong>
                    <span style="opacity:.6;font-size:.9em;">· <?php echo (string) ($c1_sched['locked_by'] ?? '') === 'consensus' ? esc_html__('by unanimity', 'cluster-one-kleinrock') : esc_html__('by SysOp cut', 'cluster-one-kleinrock'); ?></span></p>
<?php           /* 07.10.2026 — ENTRASTEN. Christians Vorschlag, und er ist der
                   bessere: statt den eingerasteten Tag im Kalender ziehbar zu
                   machen (zwei Wege zu einer Wahrheit), wird er EINMAL wieder
                   geoeffnet — und danach gilt alles, was fuer offene Tage
                   schon gilt. Kein neuer Weg, ein Schalter zurueck.

                   DERSELBE RIEGEL WIE IM HANDLER, zwei Leser: wer nicht
                   entrasten darf, sieht keinen Knopf — statt einen zu sehen
                   und 403 zu bekommen.                             (P1-12) */
                if ($is_sysop && (string) ($c1_sched['locked_by'] ?? '') !== 'archimedes') :
                    $c1_frage = __('Release the day again? The marks stay and the day is open for proposals.', 'cluster-one-kleinrock');
                    if (!empty($event['open_nodes'])) {
                        $c1_frage .= ' ' . __('This meeting is open to other nodes — they already hold this date.', 'cluster-one-kleinrock');
                    }
?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:-.4em 0 .9em;">
                    <?php wp_nonce_field('cluster_one_gregor_unlock'); ?>
                    <input type="hidden" name="action" value="cluster_one_gregor_unlock">
                    <input type="hidden" name="event" value="<?php echo esc_attr($eid); ?>">
                    <button type="submit" class="button" onclick="return confirm('<?php echo esc_js($c1_frage); ?>');"><?php esc_html_e('Unlock the day', 'cluster-one-kleinrock'); ?></button>
                </form>
<?php           elseif ($is_sysop) : ?>
                <p class="description" style="max-width:680px;margin:-.4em 0 .9em;"><?php esc_html_e('This day came with the meeting from another node. It was not decided here, so it is not released here.', 'cluster-one-kleinrock'); ?></p>
<?php           endif; ?>
            <?php elseif (!empty($c1_pending)) : ?>
                <?php /* translators: %s: Namen derer, die noch nicht geantwortet haben, kommagetrennt. */ ?>
                <p class="description" style="max-width:680px;"><?php echo esc_html(sprintf(__('Not yet responded: %s', 'cluster-one-kleinrock'), implode(', ', $c1_pending))); ?></p>
            <?php endif; ?>
            <table class="widefat striped" style="max-width:680px;margin-top:.5em;">
                <thead><tr><th style="width:11em;"><?php esc_html_e('Date', 'cluster-one-kleinrock'); ?></th><th style="width:3em;">#</th><th><?php esc_html_e('Who', 'cluster-one-kleinrock'); ?></th></tr></thead>
                <tbody>
                <?php if (empty($c1_by_date)) : ?>
                    <tr><td colspan="3" style="opacity:.6;"><?php esc_html_e('No markers yet.', 'cluster-one-kleinrock'); ?></td></tr>
                <?php endif; ?>
                <?php foreach ($c1_by_date as $c1_d => $c1_keys) : ?>
                    <?php // v0.0.77: Personensumme = Marker + Gäste; Namen tragen ihr +N sichtbar
                    $c1_people = 0; $c1_names = array();
                    foreach ($c1_keys as $c1_k) {
                        $c1_plus   = cluster_one_gregor_marker_plus($c1_markers[$c1_k]);
                        $c1_people += 1 + $c1_plus;
                        $c1_names[] = $c1_name_of($c1_k) . ($c1_plus > 0 ? ' +' . $c1_plus : '');
                    } ?>
                    <tr<?php echo ($c1_locked && $c1_d === (string) $c1_sched['locked_date']) ? ' style="background:#eaf6ea;"' : ''; ?>>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($c1_d))); ?></td>
                        <td><strong><?php echo (int) $c1_people; ?></strong></td>
                        <td><?php echo esc_html(implode(', ', $c1_names)); ?><?php echo in_array($c1_me, $c1_keys, true) ? ' <span style="opacity:.6;">(' . esc_html__('you', 'cluster-one-kleinrock') . ')</span>' : ''; ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!empty($c1_declined)) : ?>
                    <tr><td style="opacity:.6;"><?php esc_html_e('Not coming', 'cluster-one-kleinrock'); ?></td><td style="opacity:.6;"><?php echo count($c1_declined); ?></td><td style="opacity:.6;"><?php echo esc_html(implode(', ', array_map($c1_name_of, $c1_declined))); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
<?php /* 07.10.2026 — DER SYSOP BERICHTIGT KOPFZAHLEN, auch nach dem Tag.
         Christians Fall: „falls sich herausstellte, dass einer der User beim
         Treffen fehlte." Es steht UNTER der Tabelle und nicht in ihr: die
         Tabelle sagt, was gilt, und das hier ist eine Berichtigung. Eine
         Tabelle, in der man tippt, waere beides und keines von beiden.
         NUR BESTEHENDE MARKER. Anmelden kann der SysOp niemanden — wer nicht
         zugesagt hat, steht hier nicht. */ ?>
<?php if ($is_sysop && !empty($c1_markers)) : ?>
            <details style="margin-top:.6em;max-width:680px;">
                <summary style="cursor:pointer;opacity:.8;"><?php esc_html_e('Correct guest counts (SysOp)', 'cluster-one-kleinrock'); ?></summary>
                <p class="description" style="margin:.4em 0 .6em;"><?php esc_html_e('Changes only the number of guests, never the day. Works after the meeting too — for the case where someone did not turn up.', 'cluster-one-kleinrock'); ?></p>
<?php     foreach ($c1_markers as $c1_mk => $c1_mv) : ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:.2em 0;display:flex;gap:.5em;align-items:center;">
                    <?php wp_nonce_field('cluster_one_gregor_marker'); ?>
                    <input type="hidden" name="action" value="cluster_one_gregor_marker">
                    <input type="hidden" name="event" value="<?php echo esc_attr($eid); ?>">
                    <input type="hidden" name="marker_for" value="<?php echo esc_attr((string) $c1_mk); ?>">
                    <input type="hidden" name="marker_date" value="<?php echo esc_attr(cluster_one_gregor_marker_date($c1_mv)); ?>">
                    <span style="min-width:12em;"><?php echo esc_html($c1_name_of($c1_mk)); ?></span>
                    <label style="display:inline-flex;align-items:center;gap:.3em;">+<input type="number" name="plus_count" min="0" max="99" value="<?php echo (int) cluster_one_gregor_marker_plus($c1_mv); ?>" style="width:4.5em;"></label>
                    <button type="submit" class="button button-small"><?php esc_html_e('Correct', 'cluster-one-kleinrock'); ?></button>
                </form>
<?php     endforeach; ?>
            </details>
<?php endif; ?>
            <?php if ($c1_i_play) : ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:.8em;display:flex;gap:.6em;align-items:center;flex-wrap:wrap;">
                    <?php wp_nonce_field('cluster_one_gregor_marker'); ?>
                    <input type="hidden" name="action" value="cluster_one_gregor_marker">
                    <input type="hidden" name="event" value="<?php echo esc_attr($eid); ?>">
                    <?php $c1_my_plus = isset($c1_markers[$c1_me]) ? cluster_one_gregor_marker_plus($c1_markers[$c1_me]) : 0; ?>
                    <?php if (!$c1_locked) : ?>
                        <input type="date" name="marker_date" min="<?php echo esc_attr(current_time('Y-m-d')); ?>" value="<?php echo esc_attr(isset($c1_markers[$c1_me]) ? cluster_one_gregor_marker_date($c1_markers[$c1_me]) : ''); ?>" required>
                        <label style="display:inline-flex;align-items:center;gap:.3em;">+<input type="number" name="plus_count" min="0" max="99" value="<?php echo (int) $c1_my_plus; ?>" style="width:4.5em;" title="<?php echo esc_attr__('+ people you bring', 'cluster-one-kleinrock'); ?>"></label>
                        <button type="submit" class="button"><?php echo isset($c1_markers[$c1_me]) ? esc_html__('Move my marker', 'cluster-one-kleinrock') : esc_html__('Set my marker', 'cluster-one-kleinrock'); ?></button>
                    <?php else : ?>
                        <input type="hidden" name="marker_date" value="<?php echo esc_attr((string) $c1_sched['locked_date']); ?>">
                        <?php if (!isset($c1_markers[$c1_me])) : ?>
                            <label style="display:inline-flex;align-items:center;gap:.3em;">+<input type="number" name="plus_count" min="0" max="99" value="0" style="width:4.5em;"></label>
                            <button type="submit" class="button"><?php esc_html_e('Join on the locked date', 'cluster-one-kleinrock'); ?></button>
                        <?php else : ?>
                            <?php // v0.0.77: Das Datum ist Fels — die Gästezahl lebt (Freund eines
                                  // Freundes springt ab; erfasst wird der jeweils aktuelle Stand). ?>
                            <label style="display:inline-flex;align-items:center;gap:.3em;">+<input type="number" name="plus_count" min="0" max="99" value="<?php echo (int) $c1_my_plus; ?>" style="width:4.5em;"></label>
                            <button type="submit" class="button"><?php esc_html_e('Update my guest count', 'cluster-one-kleinrock'); ?></button>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($c1_markers[$c1_me]) || !in_array($c1_me, $c1_declined, true)) : ?>
                        <button type="submit" class="button" name="decline" value="1"><?php esc_html_e('Not coming', 'cluster-one-kleinrock'); ?></button>
                    <?php endif; ?>
                </form>
                <?php if ($is_sysop && !$c1_locked && isset($c1_markers[$c1_me])) : ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:.6em;">
                        <?php wp_nonce_field('cluster_one_gregor_lock'); ?>
                        <input type="hidden" name="action" value="cluster_one_gregor_lock">
                        <input type="hidden" name="event" value="<?php echo esc_attr($eid); ?>">
                        <?php /* translators: %s: Datum, auf das festgelegt wird (in beiden Saetzen). */ ?>
                        <button type="submit" class="button button-primary" onclick="return confirm('<?php echo esc_js(sprintf(__('Lock the date to %s? Whoever stands elsewhere is not part of it this time. The date is final afterwards.', 'cluster-one-kleinrock'), cluster_one_gregor_marker_date($c1_markers[$c1_me]))); ?>');"><?php echo esc_html(sprintf(__('Lock it in on %s', 'cluster-one-kleinrock'), date_i18n(get_option('date_format'), strtotime(cluster_one_gregor_marker_date($c1_markers[$c1_me]))))); ?></button>
                    </form>

                <?php endif; ?>
            <?php endif; ?>
            <?php endif; // scheduling ?>

<?php /* v0.3.17: Der Schalter stand bis hierher INNERHALB des Sperr-Formulars —
   und das wird nur gezeigt, solange das Datum NOCH NICHT gesperrt ist. Der
   Schalter verlangt aber das Gegenteil: Er greift erst NACH dem Sperren. Er
   konnte also nie sichtbar sein. Jetzt steht er hinter der ganzen
   Terminfindung, wo er hingehoert. */ ?>
<?php
/* v0.3.13: Der Schalter nach aussen. Er steht bewusst NEBEN dem Schnitt und
   nicht davor: Erst wird drinnen entschieden, dann geoeffnet. Solange Tag,
   Stunde oder Ort fehlen, sagt der Knopf, was fehlt — statt einfach nicht da
   zu sein. */
$c1_bereit = cluster_one_is_sysop() ? cluster_one_archimedes_ready($event) : false;
$c1_auf    = cluster_one_is_sysop() ? cluster_one_archimedes_is_open($event) : false;
$c1_reich  = cluster_one_archimedes_reichweite();   // v1.7.0 (H143): 'ja' | 'nein' | 'fehlt'
/* v1.7.0 (H143): Der Abschnitt erscheint nur, wenn dieser Knoten wirklich
   andere Knoten hat — ODER wenn das Treffen bereits offen steht. Das zweite
   ist kein Nachlass, sondern die Falle, die sonst entstuende: Verschwindet
   die letzte Kante, waere ein offenes Treffen sichtbar offen und nicht mehr
   zu schliessen. Ein Zustand, den man herstellen, aber nicht zuruecknehmen
   kann, ist schlimmer als der Knopf, den wir gerade wegnehmen. */
if (cluster_one_is_sysop() && ($c1_reich === 'ja' || $c1_auf)) :
?>
                    <div style="margin-top:1.2em;max-width:760px;">
                        <h3 style="margin-bottom:.3em;"><?php esc_html_e('Other nodes', 'cluster-one-kleinrock'); ?></h3>
<?php if ($c1_auf && $c1_reich !== 'ja') : /* v1.7.0 (H143): offen, aber niemand da */ ?>
                        <p style="margin:.2em 0 .6em;"><span style="color:#996800;font-weight:600;">●</span>
                            <?php echo esc_html($c1_reich === 'fehlt'
                                ? __('This meeting is marked open to other nodes — but this node has no mesh at all, so nobody can join. The mark is kept so you can take it back.', 'cluster-one-kleinrock')
                                : __('This meeting is marked open to other nodes — but this node is not connected to any right now, so nobody can join. The mark is kept so you can take it back.', 'cluster-one-kleinrock')); ?></p>
<?php elseif ($c1_auf) : ?>
                        <p style="margin:.2em 0 .6em;"><span style="color:#1a7f37;font-weight:600;">●</span>
                            <?php esc_html_e('This meeting is open to other nodes. They can join and say how many are coming — they cannot move the date.', 'cluster-one-kleinrock'); ?></p>
<?php elseif (!$c1_bereit) : ?>
                        <p style="margin:.2em 0 .6em;opacity:.75;"><?php esc_html_e('Not yet open to other nodes — and not ready to be. A meeting travels only once it is settled here: the day locked, the hour set, and a place. Until then it is a declaration of intent, not an invitation.', 'cluster-one-kleinrock'); ?></p>
<?php else : ?>
                        <p style="margin:.2em 0 .6em;opacity:.85;"><?php esc_html_e('Settled here — day, hour and place are set. Opening it to other nodes is a separate decision, not a consequence.', 'cluster-one-kleinrock'); ?></p>
<?php endif; ?>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                            <?php wp_nonce_field('cluster_one_archimedes_open'); ?>
                            <input type="hidden" name="action" value="cluster_one_archimedes_open">
                            <input type="hidden" name="event" value="<?php echo esc_attr($eid); ?>">
                            <button type="submit" class="button" <?php disabled(!$c1_bereit && !$c1_auf); ?>><?php
                                echo $c1_auf ? esc_html__('Close to other nodes', 'cluster-one-kleinrock') : esc_html__('Open to other nodes', 'cluster-one-kleinrock');
                            ?></button><?php /* v1.7.0 (H143): Der Knopf steht hier nur noch, wenn
                                es andere Knoten GIBT oder das Treffen schon offen ist — dann
                                heisst er „Close". „Open" auf einem Knoten ohne Netz ist nicht
                                mehr erreichbar. */ ?>
                        </form>
                    </div>
<?php endif; ?>

            <?php /* v1.0.3: Die Beschreibung sieht JEDER, nicht nur der SysOp — sie
               ist der Grund, warum jemand kommt oder nicht. esc_html + wpautop:
               fremder Text wird nie als Markup behandelt (P0). */ ?>
            <?php if (trim((string) ($event['beschreibung'] ?? '')) !== '') : ?>
                <div style="max-width:680px;margin:1em 0;padding:.9em 1.1em;border-left:3px solid #dcdcde;background:#fff;">
                    <?php echo wpautop(esc_html((string) $event['beschreibung'])); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_html() innen, wpautop() setzt nur <p> und <br> darum ?>
                </div>
            <?php endif; ?>
            <?php if (!$is_sysop) : ?>
                <p style="opacity:.75;"><?php esc_html_e('Linked room:', 'cluster-one-kleinrock'); ?> <?php echo esc_html((string) ($rooms[(string) ($event['linked_room'] ?? '')]['name'] ?? __('none', 'cluster-one-kleinrock'))); ?></p>
                <p style="opacity:.75;"><?php esc_html_e('Linked thread:', 'cluster-one-kleinrock'); ?> <?php echo esc_html((string) ($threads[(string) ($event['linked_thread'] ?? '')]['title'] ?? __('none', 'cluster-one-kleinrock'))); ?></p>
            <?php else : ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:600px;margin-top:1em;">
                    <?php wp_nonce_field('cluster_one_gregor_event_config'); ?>
                    <input type="hidden" name="action" value="cluster_one_gregor_event_config">
                    <input type="hidden" name="event" value="<?php echo esc_attr($eid); ?>">

                    <?php /* v0.3.16: "Treffen-Stufe" ist raus. Sie stellte dieselbe Frage
                       wie der Schalter "Fuer andere Knoten oeffnen" aus 0.3.13 — zwei Orte
                       fuer eine Frage — und ihr Text log obendrein ("wartet auf ... beides
                       existiert noch nicht"). Beides existiert. Der Schalter ersetzt sie
                       und ist strenger: Er verlangt Tag, Stunde und Ort. */ ?>

                    <?php /* v1.0.3: DIE BESCHREIBUNG. Sie steht bewusst VOR Ort und Zeit:
                       wer ein Treffen liest, will zuerst wissen, WAS stattfindet, und
                       erst dann wo. Ein Feld — die Art des Treffens (Ort oder Link)
                       aendert daran nichts. Sie reist mit, wenn das Treffen fuer
                       andere Knoten geoeffnet ist. */ ?>
                    <h3 style="margin-top:1.5em;"><?php esc_html_e('What is happening', 'cluster-one-kleinrock'); ?></h3>
                    <p class="description" style="max-width:600px;"><?php esc_html_e('A few sentences: what this is, who it is for, what to bring. If the meeting is opened to other nodes, this text travels with it — someone far away decides from it whether the journey is worth it.', 'cluster-one-kleinrock'); ?></p>
                    <p><textarea name="beschreibung" rows="4" class="large-text" maxlength="2000"><?php echo esc_textarea((string) ($event['beschreibung'] ?? '')); ?></textarea></p>

                    <h3 style="margin-top:1.5em;"><?php esc_html_e('Where and when', 'cluster-one-kleinrock'); ?></h3>
                    <p class="description" style="max-width:600px;"><?php esc_html_e('Set either a place or a link — whichever is filled says what kind of meeting this is. A place can travel to other nodes; a link never leaves this one.', 'cluster-one-kleinrock'); ?></p>
                    <p><label><?php esc_html_e('Place (physical)', 'cluster-one-kleinrock'); ?><br>
                        <input name="place" type="text" class="regular-text" value="<?php echo esc_attr((string) ($event['place'] ?? '')); ?>"></label></p>
                    <p><label><?php esc_html_e('Link (virtual, node-internal)', 'cluster-one-kleinrock'); ?><br>
                        <input name="link" type="url" class="regular-text" value="<?php echo esc_attr((string) ($event['link'] ?? '')); ?>" placeholder="https://"></label></p>
                    <p><label><?php esc_html_e('Time of day', 'cluster-one-kleinrock'); ?><br>
                        <input name="time" type="time" value="<?php echo esc_attr((string) ($event['time'] ?? '')); ?>"></label>
                        <?php /* translators: %s: Zeitzone des Knotens. */ ?>
                        <span class="description"><?php echo esc_html(sprintf(__('interpreted as %s', 'cluster-one-kleinrock'), wp_timezone_string())); ?></span></p>

                    <h3 style="margin-top:1.5em;"><?php esc_html_e('Linked room', 'cluster-one-kleinrock'); ?></h3>
                    <select name="linked_room">
                        <option value="">— <?php esc_html_e('none', 'cluster-one-kleinrock'); ?> —</option>
                        <?php foreach ($rooms as $rid => $r) : ?>
                            <option value="<?php echo esc_attr((string) $rid); ?>" <?php selected((string) ($event['linked_room'] ?? ''), (string) $rid); ?>><?php echo esc_html((string) ($r['name'] ?? '')); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <h3 style="margin-top:1.5em;"><?php esc_html_e('Linked thread', 'cluster-one-kleinrock'); ?></h3>
                    <select name="linked_thread">
                        <option value="">— <?php esc_html_e('none', 'cluster-one-kleinrock'); ?> —</option>
                        <?php foreach ($threads as $tid => $t) : ?>
                            <option value="<?php echo esc_attr((string) $tid); ?>" <?php selected((string) ($event['linked_thread'] ?? ''), (string) $tid); ?>><?php echo esc_html((string) ($t['title'] ?? '')); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <p style="margin-top:1.5em;"><?php submit_button(__('Save', 'cluster-one-kleinrock'), 'primary', 'submit', false); ?></p>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
}

/** Das Gregor-Spiel (v0.0.76): Marker setzen/verschieben/absagen.
   Offen: frei und beliebig oft. Eingeloggt: das Datum ist FIX — erlaubt
   bleiben nur Abspringen (das Leben passiert; bezeugt) und Wieder-
   Aufspringen auf exakt das eingeloggte Datum. */
add_action('admin_post_cluster_one_gregor_marker', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_gregor_marker');
    $eid  = isset($_POST['event']) ? sanitize_text_field(wp_unslash($_POST['event'])) : '';
    /* 07.10.2026 — WER AUS DEM KALENDER KOMMT, GEHT IN DEN KALENDER ZURUECK.
       Die Tafel am Tag schickt an denselben Handler wie die Ereignisseite.
       Ohne diese Zeile landet man nach dem Speichern auf einer Seite, die man
       nicht aufgerufen hat.
           Ein Weg zurueck, der woanders endet, ist kein Weg zurueck.
       EINE WEISSE LISTE, kein freies Ziel: ein Umleitungsziel aus dem
       Formular zu nehmen, waere eine offene Tuer. */
    $back = (isset($_POST['zurueck']) && (string) wp_unslash($_POST['zurueck']) === 'kalender') // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert wird nur mit einer Weissliste verglichen (auf dieser Zeile) und sonst nicht benutzt
        ? admin_url('admin.php?page=cluster_one_gregor')
        : admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode($eid));
    $uid  = get_current_user_id();
    if (in_array($uid, cluster_one_ai_identity_user_ids(), true) || !in_array($uid, cluster_one_gregor_humans(), true)) {
        wp_safe_redirect($back); // KIs und Nicht-Spieler markieren nicht
        exit;
    }
    $idx = cluster_one_gregor_event_index($eid);
    if ($idx < 0) {
        wp_safe_redirect(admin_url('admin.php?page=cluster_one_gregor'));
        exit;
    }
    $events = cluster_one_get_gregor_events();
    $sched  = (array) ($events[$idx]['scheduling'] ?? array('status' => 'open', 'markers' => array(), 'declined' => array(), 'locked_date' => '', 'locked_by' => ''));
    $key    = 'wp_' . $uid;
    $decline = !empty($_POST['decline']);
    $date    = isset($_POST['marker_date']) ? trim(sanitize_text_field(wp_unslash($_POST['marker_date']))) : '';

    /* 07.10.2026 — DIE KOPFZAHL LEBT LAENGER ALS DER TERMIN.
     *
     * Christian: „Die Anzahl, die die WP-User angeben, muss dauerhaft
     * aenderbar bleiben, bis zum Zeitpunkt, wo das Treffen schon erledigt
     * wurde. Der SysOp kann anschliessend immer noch aendern. (Fuer den Fall,
     * dass sich herausstellte, dass einer der User beim Treffen fehlte.)"
     *
     * BIS HEUTE WAR DAS UNMOEGLICH, und zwar als Nebenwirkung: der Riegel
     * unten weist jedes Datum in der Vergangenheit ab (v0.0.96, gegen
     * Zeitreisen). Nach dem Schnitt schickt das Formular das EINGERASTETE
     * Datum mit — und sobald der Tag vorbei war, lag genau dieses Datum in
     * der Vergangenheit. Eine Zahl war also nicht gesperrt, sie fiel durch
     * einen Riegel, der sie nicht gemeint hat.
     *     Ein Riegel gegen Zeitreisen hat die Logistik mitgefangen.
     *
     * ZWEI RECHTE, GETRENNT:
     *     der eigene Marker   solange das Treffen nicht VORBEI ist
     *     der SysOp           immer, auch danach
     * Wer den SysOp hier ausnimmt, verlangt von ihm, einen Termin
     * aufzuschliessen, um eine Kopfzahl zu berichtigen.
     *
     * FREMDER MARKER NUR FUER DEN SYSOP, und nur einer, den es schon gibt:
     * `marker_for` ist keine Einladung, jemanden anzumelden.
     */
    $c1_fremd = '';
    if (cluster_one_is_sysop() && isset($_POST['marker_for'])) {
        $c1_mf = sanitize_key(wp_unslash($_POST['marker_for']));
        if ($c1_mf !== '' && isset($sched['markers'][$c1_mf])) { $c1_fremd = $c1_mf; }
    }
    $c1_nur_zahl = !$decline && isset($_POST['plus_count'])
                && ($date === '' || $date === cluster_one_gregor_marker_date(
                       $sched['markers'][$c1_fremd !== '' ? $c1_fremd : $key] ?? null));
    $c1_darf_spaet = cluster_one_is_sysop()
                  || !cluster_one_gregor_vorbei((array) $events[$idx]);

    if (!$decline && $date !== '' && $date < current_time('Y-m-d')
        && !($c1_nur_zahl && $c1_darf_spaet)) {
        wp_safe_redirect($back); // v0.0.96: kein Marker in der Vergangenheit
        exit;
    }
    if ($c1_fremd !== '') { $key = $c1_fremd; }

    if ($decline) {
        unset($sched['markers'][$key]);
        if (!in_array($key, (array) $sched['declined'], true)) {
            $sched['declined'][] = $key;
        }
        cluster_one_event_emit(array( // flüchtig — die Tabelle selbst ist der sichtbare Stand
            'type' => 'gregor_declined', 'container_type' => 'event', 'container_id' => $eid,
            'actor_id' => cluster_one_actor_id_human(),
        ), false);
    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        if (($sched['status'] ?? 'open') === 'locked' && $date !== (string) ($sched['locked_date'] ?? '')) {
            wp_safe_redirect($back); // eingeloggt: das Datum rührt niemand mehr an
            exit;
        }
        // v0.0.77: +X reist mit dem Marker; NACH dem Einloggen bleibt das
        // Datum Fels, aber die Gästezahl darf weiterleben (das Leben
        // passiert — Kopfzahl ist Logistik, kein Beschluss).
        $c1_old_plus = cluster_one_gregor_marker_plus($sched['markers'][$key] ?? null);
        $c1_new_plus = isset($_POST['plus_count']) ? min(99, max(0, (int) $_POST['plus_count'])) : $c1_old_plus;
        $sched['markers'][$key] = array('date' => $date, 'plus' => $c1_new_plus, 'ts' => time());
        $sched['declined'] = array_values(array_diff(array_map('strval', (array) $sched['declined']), array($key)));
        if ($c1_new_plus !== $c1_old_plus) {
            cluster_one_event_emit(array( // flüchtig — die Tabelle zeigt den Stand; Kopfzahl ist Logistik, kein Beschluss
                'type' => 'gregor_plus_changed', 'container_type' => 'event', 'container_id' => $eid,
                'actor_id' => cluster_one_actor_id_human(),
            ), false);
        }
    } else {
        wp_safe_redirect($back);
        exit;
    }

    $was_open = (($sched['status'] ?? 'open') === 'open');
    $sched    = cluster_one_gregor_check_autolock($sched);
    if ($was_open && ($sched['status'] ?? '') === 'locked') {
        // Einstimmigkeit — Türereignis; Actor ist der Mensch, dessen Zug
        // die Einigung vollendet hat (ehrlich: das Kollektiv hat kein Konto).
        cluster_one_event_emit(array(
            'type' => 'gregor_locked', 'container_type' => 'event', 'container_id' => $eid,
            'actor_id' => cluster_one_actor_id_human(),
            'content_hash' => hash('sha256', (string) $sched['locked_date']),
        ), true);
    }
    $events[$idx]['scheduling'] = $sched;
    cluster_one_save_gregor_events($events);
    wp_safe_redirect($back);
    exit;
});

/** SysOp-Schnitt („ist gut jetzt"): eingeloggt wird das Datum, auf dem der
   EIGENE Marker des SysOps steht — wer woanders steht, ist diesmal nicht
   dabei (bleibt sichtbar in der Tabelle, keine stille Bereinigung). */
/* --- v0.3.13: DIE OEFFNUNG NACH AUSSEN IST EINE ENTSCHEIDUNG ------------------
   Bisher wurde ein Treffen knotenuebergreifend als NEBENWIRKUNG: Datum gesperrt
   + Ort gesetzt + Label veroeffentlicht = adoptierbar. Vier bewusste
   Handlungen, aber keine davon hiess "dieses Treffen ist offen fuer andere
   Knoten". Man veroeffentlichte das Label des PROJEKTS, und das Treffen fuhr
   mit.

   Jetzt ein eigener Schalter, und er verlangt, dass drinnen alles steht:
   TAG UND STUNDE gesperrt, dazu ein Ort. Vorher ist es nur eine
   Willensbekundung.

   Ohne diesen Schalter reisen event_date, event_place und event_time NICHT mit
   dem Label, auch wenn alles andere erfuellt ist. Nebennutzen: Das Treffen ist
   damit vom Label geloest — man muss kein Projekt veroeffentlichen, um ein
   Treffen zu oeffnen. */
/* --- v1.7.0 (H143): REICHT DIESER KNOTEN UEBERHAUPT NACH DRAUSSEN? ----------
 *
 * DER BEFUND, gefunden von der Bismarck-Instanz am 31.08.2026, mit rotem
 * Zeugen: Der ganze Archimedes-Abschnitt hing an EINER Bedingung —
 * cluster_one_is_sysop(). Kein modul_da('kleinrock'), kein function_exists.
 * Ein SysOp auf einem reinen Bismarck-Knoten sah eine Ueberschrift „Other
 * nodes", einen Reiter „Archimedes", drei Saetze ueber fremde Knoten und
 * einen AKTIVEN Knopf. Druckte er ihn, sagte die Seite danach:
 *
 *     „This meeting is open to other nodes. They can join …"
 *
 * Es gibt keine, die koennten.
 *
 *     Ein Knopf ins Nichts ist ein gebrochenes Versprechen.
 *     Ein Knopf, der eine Behauptung wahr zu machen scheint,
 *     ist eine Auskunft, die nie stimmen kann.
 *
 * WARUM KEINE ZAHL DAS FAND: archimedes_ready() und is_open() wohnen in
 * dieser Datei und rufen nichts Fremdes. Es gab nichts zu riegeln, also
 * meldete bauliste.php 12 Naehte, 0 offen — alles gruen.
 *
 *     Ein Riegel um den Aufruf schuetzt vor dem Absturz.
 *     Er schuetzt nicht vor der Behauptung.
 *
 * DREI ANTWORTEN, NICHT ZWEI. Gefragt wird ueber die Auskunftsschicht des
 * Bodens (1.4.0), und zwar nach 'gepaart', nicht nach „liegt Kleinrock hier".
 * Ein Knoten MIT Kleinrock und OHNE Kante hat genauso wenig andere Knoten wie
 * einer ohne Kleinrock — der Unterschied ist nur, dass er welche bekommen
 * kann. Fuer die Behauptung auf dem Bildschirm zaehlt das Ergebnis, nicht die
 * Aussicht.
 *   'fehlt' und 'nein' fuehren hier zum selben Ergebnis, und trotzdem duerfen
 * sie nicht zusammenfallen: genau diese Verschmelzung ist der Fehler in
 * cluster_one_netz, den Post Dolly gemeldet hat. Ein Nichtwissen, das als
 * Nein zurueckkommt, ist eine Behauptung. */
function cluster_one_archimedes_reichweite(): string {
    return function_exists('cluster_one_auskunft') ? cluster_one_auskunft('gepaart') : 'fehlt';
}

/** v1.7.0 (H143): Soll dieser Knoten ueberhaupt von Archimedes sprechen?
 *
 *  Ja, wenn er andere Knoten hat — oder wenn schon etwas dasteht, das von
 *  ihnen stammt oder fuer sie gedacht war. Das Zweite ist dieselbe Regel wie
 *  beim Knopf: Was einmal da ist, muss sichtbar bleiben, sonst entsteht ein
 *  Zustand ohne Ausgang. Ein adoptiertes fremdes Treffen verschwindet nicht
 *  dadurch, dass die Kante wegfaellt, aus der es kam. */
function cluster_one_archimedes_sichtbar(): bool {
    if (cluster_one_archimedes_reichweite() === 'ja') { return true; }
    foreach (cluster_one_get_gregor_events() as $c1_ev) {
        if (!empty($c1_ev['archimedes']) || !empty($c1_ev['open_nodes'])) { return true; }
    }
    return false;
}

function cluster_one_archimedes_ready(array $ev): bool {
    $s = isset($ev['scheduling']) && is_array($ev['scheduling']) ? $ev['scheduling'] : array();
    return (string) ($s['status'] ?? '') === 'locked'
        && (string) ($s['locked_date'] ?? '') !== ''
        && trim((string) ($ev['time'] ?? '')) !== ''      // Stunde, nicht nur Tag
        && trim((string) ($ev['place'] ?? '')) !== '';    // ohne Ort keine Anreise
}

/** Ist dieses Treffen ausdruecklich fuer andere Knoten geoeffnet? */
function cluster_one_archimedes_is_open(array $ev): bool {
    return !empty($ev['open_nodes']) && cluster_one_archimedes_ready($ev);
}

add_action('admin_post_cluster_one_archimedes_open', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_archimedes_open');
    $eid  = isset($_POST['event']) ? sanitize_text_field(wp_unslash($_POST['event'])) : '';
    $idx  = cluster_one_gregor_event_index($eid);
    $back = admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode($eid));
    if ($idx < 0) {
        wp_safe_redirect($back);
        exit;
    }
    $events = cluster_one_get_gregor_events();
    if (!empty($events[$idx]['open_nodes'])) {
        unset($events[$idx]['open_nodes']); // wieder schliessen
        cluster_one_save_gregor_events($events);
        wp_safe_redirect(add_query_arg('nodes', 'closed', $back));
        exit;
    }
    if (!cluster_one_archimedes_ready($events[$idx])) {
        wp_safe_redirect(add_query_arg('nodes', 'not_ready', $back)); // drinnen erst fertig werden
        exit;
    }
    /* v1.7.0 (H143): DIE ZWEITE HAELFTE DER REPARATUR. Den Knopf wegzunehmen
       reicht nicht — ein Formular kann wiederholt werden, und ein Reiter, den
       man nur versteckt, ist ueber die Adresse weiter da. Das SCHLIESSEN
       bleibt oben immer erlaubt; nur das OEFFNEN braucht andere Knoten.
         Sichtbarkeit ist die letzte Handlung, nicht die einzige (§6.5). */
    /* BENANNTE GRENZE, beim Bauen gemessen: 'nodes' wird geschrieben und von
       NIEMANDEM gelesen — 'not_ready' steht seit 0.3.13 so da, und es gibt
       keinen Empfaenger. Wer hier landet, sieht die Seite unveraendert und
       erfaehrt nichts. Ich haenge mich an dieselbe Leine, statt hier still
       einen zweiten Meldeweg zu erfinden; der Befund ist H151 und gehoert in
       den Zug, der die 'nodes'-Meldungen ueberhaupt anzeigt. Erreichbar ist
       dieser Zweig ohnehin nur ueber ein wiederholtes Formular. */
    if (cluster_one_archimedes_reichweite() !== 'ja') {
        wp_safe_redirect(add_query_arg('nodes', 'kein_netz', $back));
        exit;
    }
    $events[$idx]['open_nodes'] = time();
    cluster_one_save_gregor_events($events);
    cluster_one_event_emit(array(
        'type'           => 'meeting_opened', // eine Oeffnung nach aussen ist eine Strukturtatsache
        'container_type' => 'event',
        'container_id'   => $eid,
        'actor_id'       => cluster_one_actor_id_human(),
        'visibility'     => 'all',
    ), true);
    wp_safe_redirect(add_query_arg('nodes', 'open', $back));
    exit;
});

add_action('admin_post_cluster_one_gregor_lock', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_gregor_lock');
    $eid = isset($_POST['event']) ? sanitize_text_field(wp_unslash($_POST['event'])) : '';
    $idx = cluster_one_gregor_event_index($eid);
    $back = admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode($eid));
    if ($idx >= 0) {
        $events = cluster_one_get_gregor_events();
        $sched  = (array) ($events[$idx]['scheduling'] ?? array());
        $my     = cluster_one_gregor_marker_date($sched['markers']['wp_' . get_current_user_id()] ?? '');
        if ($my !== '' && $my < current_time('Y-m-d')) {
            wp_safe_redirect($back); // v0.0.96: kein Schnitt auf ein vergangenes Datum
            exit;
        }
        if (($sched['status'] ?? 'open') === 'open' && $my !== '') {
            $sched['status']      = 'locked';
            $sched['locked_date'] = $my;
            cluster_one_archimedes_change_signal($events[$idx]); // v0.0.100: Treffen-Änderung pingt
            $sched['locked_by']   = 'sysop_cut';
            $events[$idx]['scheduling'] = $sched;
            cluster_one_save_gregor_events($events);
            cluster_one_event_emit(array(
                'type' => 'gregor_locked', 'container_type' => 'event', 'container_id' => $eid,
                'actor_id' => cluster_one_actor_id_human(),
                'content_hash' => hash('sha256', $my),
            ), true);
        }
    }
    wp_safe_redirect($back);
    exit;
});

/* 07.10.2026 — DER UMKEHRSCHRITT ZU cluster_one_gregor_lock.
 *
 * Er steht UNMITTELBAR unter dem Einrasten, damit die drei Felder, die das
 * eine setzt, und die drei, die das andere zuruecksetzt, auf einem Bildschirm
 * nebeneinander liegen.
 *
 *     Ein Umkehrschritt, der nicht neben dem Schritt steht, ist nach der
 *     dritten Aenderung keiner mehr.
 *
 * WAS ER SETZT — genau die drei Felder aus Zeile 1443-1446 und keins mehr:
 *     status = 'open' · locked_date = '' · locked_by = ''
 * DIE MARKEN BLEIBEN. Einrasten hat sie nicht angefasst, also ist der Stand
 * nach dem Entrasten derselbe wie vor dem Einrasten — niemand muss sich neu
 * eintragen. Das ist der Grund, warum dieser Schalter zurueck ueberhaupt
 * sauber moeglich ist.
 *
 * DER ARCHIMEDES-RIEGEL: ein Tag mit locked_by = 'archimedes' kam MIT dem
 * Treffen von einem anderen Knoten (Zeile 1717 ff., „Fels von Geburt"). Ihn
 * hier aufzuheben hiesse, ueber das Treffen eines anderen zu entscheiden.
 *
 * DIE NACHRICHT GEHT RAUS: 'changed' und 'changed_by' wie beim Editieren,
 * damit jeder, dessen Marke aelter ist, den Satz von gestern sieht — seine
 * Zusage galt fuer einen Tag, der gerade aufgehoben wurde. Und ein
 * Tuerereignis, damit die Aufhebung den Container ueberlebt.
 */
add_action('admin_post_cluster_one_gregor_unlock', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_gregor_unlock');
    $eid  = isset($_POST['event']) ? sanitize_text_field(wp_unslash($_POST['event'])) : '';
    $idx  = cluster_one_gregor_event_index($eid);
    /* 07.10.2026: WER AUS DEM KALENDER KOMMT, GEHT IN DEN KALENDER ZURUECK —
       dieselbe weisse Liste wie beim Marken-Handler, und aus demselben Grund:
       ein Umleitungsziel aus dem Formular zu nehmen, waere eine offene Tuer. */
    $back = (isset($_POST['zurueck']) && (string) wp_unslash($_POST['zurueck']) === 'kalender') // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert wird nur mit einer Weissliste verglichen (auf dieser Zeile) und sonst nicht benutzt
        ? admin_url('admin.php?page=cluster_one_gregor')
        : admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode($eid));
    if ($idx >= 0) {
        $events = cluster_one_get_gregor_events();
        $sched  = (array) ($events[$idx]['scheduling'] ?? array());
        if (((string) ($sched['status'] ?? '') === 'locked')
            && ((string) ($sched['locked_by'] ?? '') !== 'archimedes')) {
            $c1_war = (string) ($sched['locked_date'] ?? '');
            $sched['status']      = 'open';
            $sched['locked_date'] = '';
            $sched['locked_by']   = '';
            $events[$idx]['scheduling'] = $sched;
            $events[$idx]['changed']    = time();
            $events[$idx]['changed_by'] = cluster_one_human_speaker_label();
            cluster_one_archimedes_change_signal($events[$idx]); // wie beim Einrasten
            cluster_one_save_gregor_events($events);
            cluster_one_event_emit(array(
                'type' => 'gregor_unlocked', 'container_type' => 'event', 'container_id' => $eid,
                'actor_id' => cluster_one_actor_id_human(),
                'content_hash' => hash('sha256', $c1_war),
            ), true);
        }
    }
    wp_safe_redirect($back);
    exit;
});

add_action('admin_post_cluster_one_gregor_event_config', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_gregor_event_config');

    $eid    = isset($_POST['event']) ? sanitize_text_field(wp_unslash($_POST['event'])) : '';
    $events = cluster_one_get_gregor_events();
    $idx    = null;
    foreach ($events as $i => $ev) {
        if ((string) ($ev['id'] ?? '') === $eid) {
            $idx = $i;
            break;
        }
    }
    $back   = admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode($eid));

    if ($idx === null) {
        wp_safe_redirect(admin_url('admin.php?page=cluster_one_gregor'));
        exit;
    }


    /* v1.0.3: Ein Treffen, dessen Zweck man nicht nachtragen kann, ist ein halbes. */
    if (isset($_POST['beschreibung'])) {
        $events[$idx]['beschreibung'] = cluster_one_sanitize_message((string) wp_unslash($_POST['beschreibung'])); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (cluster_one_sanitize_message), den der Pruefer nicht kennt
    }
    $new_room = isset($_POST['linked_room']) ? sanitize_text_field(wp_unslash($_POST['linked_room'])) : '';
    $rooms    = cluster_one_get_rooms();
    if ($new_room === '' || isset($rooms[$new_room])) {
        $events[$idx]['linked_room'] = $new_room; // reine Event-Anzeige — die eigentliche Raum↔Thema-Kante bleibt unberührt, wird nur über die Raum-Konfiguration gesetzt
    }

    $new_thread = isset($_POST['linked_thread']) ? sanitize_text_field(wp_unslash($_POST['linked_thread'])) : '';
    $threads    = cluster_one_get_forum_threads();
    if ($new_thread === '' || isset($threads[$new_thread])) {
        $events[$idx]['linked_thread'] = $new_thread;
    }

    // v0.1.4: Ort, Link und Uhrzeit nachträglich änderbar. Ort schließt Link aus.
    if (isset($_POST['place']) || isset($_POST['link'])) {
        $c1_p = isset($_POST['place']) ? trim(sanitize_text_field(wp_unslash($_POST['place']))) : '';
        $c1_l = isset($_POST['link'])  ? esc_url_raw(trim(wp_unslash($_POST['link']))) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (esc_url_raw), den der Pruefer nicht kennt
        if ($c1_p !== '') { $c1_l = ''; }
        $events[$idx]['place'] = $c1_p;
        $events[$idx]['link']  = $c1_l;
    }
    if (isset($_POST['time'])) {
        $c1_t = trim(sanitize_text_field(wp_unslash($_POST['time'])));
        $events[$idx]['time'] = preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $c1_t) ? $c1_t : '';
        $events[$idx]['tz']   = wp_timezone_string();
    }
    /* 06.10.2026 — WER HAT WANN GEAENDERT. Christians Wunsch nach einer
       Systemnachricht, und zwar in der kleinsten Form, die traegt: ZWEI
       WERTE, keine Liste.
           Eine Nachrichtenliste fuer einen Satz ist ein Archiv fuer eine
           Notiz.
       Daraus macht die Seite zwei Saetze — dass geaendert wurde, und fuer
       jeden, dessen Marke aelter ist, dass seine Zusage von davor stammt.
       Das Zweite ist der Punkt: wer auf einen Ort hin zugesagt hat, der
       seitdem ein anderer ist, soll es sehen, ohne zu vergleichen. */
    $events[$idx]['changed']    = time();
    $events[$idx]['changed_by'] = cluster_one_human_speaker_label();
    cluster_one_save_gregor_events($events);
    wp_safe_redirect(add_query_arg('saved', '1', $back));
    exit;
});

/** Terminfindung (v0.0.76): das Gregor-Spiel. Wer spielt mit? Alle
   MENSCHEN der Weißliste — KIs nicht (kein Roboter-Körper, keine Anfahrt;
   Entscheidung SysOp 16.07.2026), der Collective-Pseudo-Account auch nicht. */
function cluster_one_gregor_humans(): array {
    $ai  = cluster_one_ai_identity_user_ids();
    $col = cluster_one_collective_user_id();
    $out = array();
    foreach (cluster_one_whitelist_user_ids() as $uid) {
        if ($uid > 0 && !in_array($uid, $ai, true) && $uid !== $col) {
            $out[] = $uid;
        }
    }
    // Der SysOp spielt immer mit, auch wenn er nicht auf der Liste steht
    // (er ist automatisch drin, s. Weißlisten-Grundsatz).
    $sys = cluster_one_main_admin_user_id();
    if ($sys > 0 && !in_array($sys, $out, true) && !in_array($sys, $ai, true)) {
        $out[] = $sys;
    }
    return $out;
}

/** Marker-Format (v0.0.77): ['date' => 'Y-m-d', 'plus' => int] — das
   +X-Feld (Entscheidung SysOp 16.07.): Gäste erscheinen als ZAHL, nie als
   Datensatz (kein Name, kein Profil — Artikel 6 bleibt unberührt; wer die
   +2 sind, wird in Bismarck besprochen, nicht gespeichert). 0.0.76-Marker
   waren nackte Datums-Strings — die Helfer lesen beide Formate, keine
   Migration nötig. */
function cluster_one_gregor_marker_date($m): string {
    return is_array($m) ? (string) ($m['date'] ?? '') : (string) $m;
}

function cluster_one_gregor_marker_plus($m): int {
    return is_array($m) ? max(0, (int) ($m['plus'] ?? 0)) : 0;
}

function cluster_one_gregor_event_index(string $eid): int {
    foreach (cluster_one_get_gregor_events() as $i => $ev) {
        if ((string) ($ev['id'] ?? '') === $eid) {
            return (int) $i;
        }
    }
    return -1;
}

/** Auto-Ende der Terminfindung: Die Runde ist offiziell zu Ende, wenn ALLE
   spielenden Menschen reagiert haben (Marker ODER ausdrückliches „komme
   nicht") und alle gesetzten Marker auf EINEM Datum liegen. Solange jemand
   noch gar nicht reagiert hat, endet nichts automatisch — sonst wäre die
   Runde nach dem Eröffnungs-Marker des SysOps sofort „einstimmig" vorbei;
   gegen ewige Schweiger gibt es den SysOp-Schnitt. Türereignis: das
   eingeloggte Datum ist ein Fakt, der den Knoten überlebt und (Labels)
   später verlassen darf. */
function cluster_one_gregor_check_autolock(array $sched): array {
    if (($sched['status'] ?? 'open') !== 'open') {
        return $sched;
    }
    $markers  = (array) ($sched['markers'] ?? array());
    $declined = array_map('strval', (array) ($sched['declined'] ?? array()));
    if (empty($markers)) {
        return $sched;
    }
    foreach (cluster_one_gregor_humans() as $uid) {
        $k = 'wp_' . $uid;
        if (!isset($markers[$k]) && !in_array($k, $declined, true)) {
            return $sched; // jemand hat noch gar nicht reagiert
        }
    }
    $dates = array_values(array_unique(array_map('cluster_one_gregor_marker_date', $markers)));
    if (count($dates) === 1) {
        $sched['status']      = 'locked';
        $sched['locked_date'] = $dates[0];
        $sched['locked_by']   = 'consensus'; // Einstimmigkeit, kein Schnitt
    }
    return $sched;
}

/* v1.0.1 — DAS SPEICHERPAAR IST AUSGEZOGEN, nach speicher.php.
   Befund der Kleinrock-Instanz, nachgemessen: speicher.php haelt die
   Zugriffspaare fuer JEDEN Speicher des Hauses — rooms, room_messages,
   chat, forum_threads, forum_posts, feynman_chapters. Genau eines fehlte
   dort: Gregors. Und es wird nicht nur von Gregor gebraucht:
     gregor 24x · kleinrock 8x · bismarck-seiten 1x · platon 1x
   Vierter Fall derselben Anatomie nach spiegel.php (0.9.69),
   ai_generate_text() (0.9.81) und ai_select_or_hint() (1.0.0): die
   Funktion heisst nach der Datei und ist es nicht.
   Wirkung, gemessen: Kleinrock allein 22/14 offen -> 14/6. */

function cluster_one_gregor_event_id(): string {
    return 'event_' . wp_generate_password(12, false, false);
}

/** v0.0.99: Der Pull — der Knoten holt sich fremde Labels, wann ER will.
   Gepullte Labels werden NIE Container: Lese-Kopien im Cache, komplette
   Ersetzung pro Abruf (Verschwundenes verschwindet mit — Rückzug und
   Archivierung drüben räumen hier automatisch). Antworten werden als
   feindliche Eingaben behandelt: sanitisiert, gedeckelt. */
/** v0.0.100: ARCHIMEDES — der Hineinsprung. Ein fremdes Treffen-Label
   (mit Fels-Datum) wird per SysOp-Akt zum LOKALEN Gregor-Event: Datum
   Fels von Geburt (das Datum gehört dem fremden Knoten — kein Marker-
   Spiel), lokal läuft nur Komme/Komme-nicht plus +X (die Nach-Lock-
   Mechanik seit 0.0.76/77 erbt komplett). Voraussetzung der Antwort:
   das EIGENE Spiel ist geschlossen — „ein Knoten spricht nach außen
   erst, wenn er mit sich selbst fertig ist" (Archimedes-Grundregel). */
/* 18.10.2026 (Neo): Leser fuer `adopt=no_place`. Der Handler schickt auf
   Bismarcks Forumseite zurueck — eine fremde Seite, also als admin_notice
   statt als Zeile in deren Rumpf. Faust reist in beiden Paketen mit. */
add_action('admin_notices', function () {
    if (!isset($_GET['adopt'], $_GET['page']) || $_GET['adopt'] !== 'no_place' || $_GET['page'] !== 'cluster_one_bismarck') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        return;
    }
    echo '<div class="notice notice-warning is-dismissible"><p>'
       . esc_html__('Not adopted: this meeting has no place. Virtual meetings stay with their own node — only a meeting with a place can travel.', 'cluster-one-kleinrock')
       . '</p></div>';
});

add_action('admin_post_cluster_one_archimedes_adopt', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_archimedes_adopt');
    $host = isset($_POST['host']) ? sanitize_text_field(wp_unslash($_POST['host'])) : '';
    $lid  = isset($_POST['label']) ? sanitize_text_field(wp_unslash($_POST['label'])) : '';
    $back = admin_url('admin.php?page=cluster_one_bismarck&tab=forum&area=' . rawurlencode((string) apply_filters('cluster_one_forum', '', 'fremd-bereich')));   // v1.3.4
    /* v0.3.15: Die Quelle ist die eigene Treffen-Route des Herkunftsknotens,
       LIVE geholt. Vorher las die Adoption aus den Fremd-Label-Threads — ein
       Treffen konnte also nur reisen, wenn das Projekt ein veroeffentlichtes
       Label hatte. Wer ein Jahrestreffen ausrichten will, hat damit nichts zu
       tun. 'host' ist die Herkunfts-URL (stabil, anders als der Name), 'label'
       die Treffen-Kennung drueben. */
    $label = null;
    if (!function_exists('cluster_one_fetch_meetings')) { wp_safe_redirect($back); exit; }
    foreach (cluster_one_fetch_meetings($host) as $c1_m) {
        if ((string) $c1_m['id'] !== $lid) {
            continue;
        }
        $label = array(
            'id'          => (string) $c1_m['id'],
            'title'       => (string) $c1_m['name'],
            'event_date'  => (string) $c1_m['date'],
            'event_place' => (string) $c1_m['place'],
            'event_time'  => (string) $c1_m['time'],
            'event_tz'    => (string) $c1_m['tz'],
            'beschreibung' => (string) ($c1_m['beschreibung'] ?? ''),   // v1.0.3
            'node'        => (string) $c1_m['node'],
        );
        $host = (string) $c1_m['url'];
        break;
    }
    if (!$label || empty($label['event_date']) || (string) $label['event_date'] < current_time('Y-m-d')) {
        wp_safe_redirect($back); // kein Label, kein Datum, oder Vergangenheit
        exit;
    }
    // v0.1.4: Ohne Ort keine Adoption. Virtuelle Treffen sind knoten-intern —
    // ihr Link reist nie, also gibt es hier nichts zu adoptieren.
    //
    // WICHTIG fuer spaeter: Das schliesst den vierten Fall (virtuell +
    // knotenuebergreifend) nur SOLANGE aus, wie es keine Bruecke gibt. Mit
    // einem gebrueckten Matrix-Raum wird er im CHAT denkbar, weil dann jeder
    // Knoten seinen eigenen Mitschnitt haelt — zwei Verwahrer, nicht einer.
    // Auf THREAD-Ebene bleibt er dauerhaft zu: ein Thread ist eine einzige
    // kanonische Ablage mit einem Eigentuemer, und ein Fremder darin machte
    // diesen Eigentuemer zum Verwahrer von Worten, an die der Urheber nicht
    // mehr herankommt. Wer diese Schranke aufweicht, aendert nicht ein Feature,
    // sondern die Souveraenitaetsregel des Entwurfs.
    if (trim((string) ($label['event_place'] ?? '')) === '') {
        wp_safe_redirect(add_query_arg('adopt', 'no_place', $back));
        exit;
    }
    foreach (cluster_one_get_gregor_events() as $ev) { // kein Duplikat
        if ((string) (($ev['archimedes']['host'] ?? '')) === $host && (string) (($ev['archimedes']['label'] ?? '')) === $lid) {
            wp_safe_redirect(admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode((string) $ev['id'])));
            exit;
        }
    }
    $eid    = cluster_one_gregor_event_id();
    $events = cluster_one_get_gregor_events();
    $events[] = array(
        'id'           => $eid,
        'name'         => sanitize_text_field((string) ($label['title'] ?? '')),
        /* v1.0.3: Sie kommt von einem fremden Knoten — also durch denselben
           Sanitizer wie jeder andere fremde Text, ein zweites Mal. Gewaschen
           wird auf BEIDEN Seiten der Leitung (P0). */
        'beschreibung' => cluster_one_sanitize_message((string) ($label['beschreibung'] ?? '')),
        'creator'      => cluster_one_human_speaker_label(),
        /* 06.10.2026 — DIE NUMMER NEBEN DEM NAMEN.
           `creator` ist ein NAME und taugt nicht zum Vergleichen: zwei
           Menschen koennen denselben Anzeigenamen tragen, und ein Mensch kann
           seinen aendern. Wer loeschen darf, wird an einer Nummer entschieden.
           SIE REIST NICHT: /meetings baut seine Antwort Feld fuer Feld
           (kleinrock.php:1266-1271) und nennt dieses Feld nicht. */
        'creator_id'   => (int) get_current_user_id(),
        'suggested_by' => '',
        'linked_room'   => '',
        'linked_thread' => '',
        'created'      => time(),
        'place'        => sanitize_text_field((string) ($label['event_place'] ?? '')), // v0.1.4: die Tatsache reist, nicht die Teilnehmer
        'link'         => '',
        'time'         => preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', (string) ($label['event_time'] ?? '')) ? (string) $label['event_time'] : '',
        'tz'           => sanitize_text_field((string) ($label['event_tz'] ?? '')),
        'archimedes'   => array('host' => $host, 'label' => $lid, 'node' => (string) ($label['node'] ?? $host)), // v0.3.0: Herkunft kommt aus dem Thread
        'scheduling'   => array(
            'status'      => 'locked', // Fels von Geburt
            'markers'     => array(),
            'declined'    => array(),
            'locked_date' => (string) $label['event_date'],
            'locked_by'   => 'archimedes',
        ),
    );
    cluster_one_save_gregor_events($events);
    cluster_one_event_emit(array(
        'type' => 'gregor_locked', 'container_type' => 'gregor', 'container_id' => $eid,
        'actor_id' => cluster_one_actor_id_human(), 'visibility' => 'all',
    ), true);
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode($eid)));
    exit;
});

/** Die Antwort nach draußen: „Knoten X kommt mit N" — N ist die
   Personensumme auf dem Fels-Datum (Marker + Gäste), gesendet über die
   KANTE (nur Freunde — der Weg der Systemnachrichten). Erneutes Senden
   ERGÄNZT drüben (Beschluss 99c: Geschichte statt Überschreiben). */
add_action('admin_post_cluster_one_archimedes_rsvp', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_archimedes_rsvp');
    $eid  = isset($_POST['event']) ? sanitize_text_field(wp_unslash($_POST['event'])) : '';
    $back = admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode($eid));
    $idx  = cluster_one_gregor_event_index($eid);
    $events = cluster_one_get_gregor_events();
    $ev   = $idx >= 0 ? $events[$idx] : null;
    if (!$ev || empty($ev['archimedes']['host'])) {
        wp_safe_redirect($back);
        exit;
    }
    $peer = null;
    if (!function_exists('cluster_one_get_peers')) { return; }
    /* 18.10.2026 (Neo): Hier wurde der HOST der Kante (wp_parse_url …
       PHP_URL_HOST, also „knoten.example") mit archimedes.host verglichen —
       und dort steht seit v0.3.0 die VOLLE URL aus /meetings
       („https://knoten.example"). Die beiden waren nie gleich; die Kante
       wurde nie gefunden, und jede Zusage ging als oeffentliche Klingel
       hinaus statt ueber die Kante. Jetzt werden beide Seiten durch dieselbe
       Normalisierung gezogen, die auch cluster_one_adopted_from() benutzt. */
    $c1_herkunft = cluster_one_norm_url((string) $ev['archimedes']['host']);
    foreach (cluster_one_get_peers() as $p) {
        if ($c1_herkunft !== '' && cluster_one_norm_url((string) ($p['url'] ?? '')) === $c1_herkunft) {
            $peer = $p;
            break;
        }
    }
    /* v0.3.20: Ohne Kante klingeln wir oeffentlich. Eine Zusage darf keine
       Freundschaft voraussetzen — die Entscheidung, wer wessen Freund wird,
       soll aus Freiheit fallen und nicht aus Zwang. Die Klingel traegt keine
       Zahl; der Gastgeber holt sie sich an unserer oeffentlichen Route ab. */
    if (!$peer) {
        $c1_ziel = rtrim(cluster_one_norm_url((string) $ev['archimedes']['host']), '/');
        /* v0.9.53: Die Herkunfts-URL steht in einem UEBERNOMMENEN Treffen —
           sie kam also aus der Antwort eines fremden Knotens. Auch das
           Klingeln greift deshalb mit der sicheren Hand. */
        $c1_res  = wp_safe_remote_post($c1_ziel . '/wp-json/cluster-one/v1/rsvp', array(
            'timeout' => 8,
            'body'    => array('url' => home_url('/')),
        ));
        $c1_ok = !is_wp_error($c1_res) && (int) wp_remote_retrieve_response_code($c1_res) === 200;
        wp_safe_redirect(add_query_arg('rsvp', $c1_ok ? 'rang' : 'unreachable', $back));
        exit;
    }
    $date  = (string) ($ev['scheduling']['locked_date'] ?? '');
    $count = 0;
    foreach ((array) ($ev['scheduling']['markers'] ?? array()) as $m) {
        if (cluster_one_gregor_marker_date($m) === $date) {
            $count += 1 + cluster_one_gregor_marker_plus($m);
        }
    }
    $resp = wp_safe_remote_post(trailingslashit((string) $peer['url']) . 'wp-json/cluster-one/v1/inbox', array(
        'timeout' => 8,
        'headers' => array('X-Cluster-Token' => (string) $peer['token'], 'Content-Type' => 'application/json'),
        'body'    => wp_json_encode(array('type' => 'archimedes_rsvp', 'label' => (string) $ev['archimedes']['label'], 'count' => $count, 'node' => get_bloginfo('name'))),
        'sslverify' => true,
    ));
    $ok = !is_wp_error($resp) && wp_remote_retrieve_response_code($resp) === 200;
    wp_safe_redirect(add_query_arg('rsvp', $ok ? 'ok' : 'failed', $back));
    exit;
});

/* v0.3.12: Das Anlege-Formular, jetzt UNTER dem Kalender. Der gewaehlte Tag
   kommt als Vorgabe herein; ohne Klick bleibt das Feld leer und man kann es
   von Hand fuellen. */
function cluster_one_render_gregor_create_form(string $pick = ''): void {
    $physisch_erlaubt = cluster_one_may_create_event(true);
    if (!cluster_one_may_create_event(false)) {
        return;
    }
    ?>
    <h2 style="margin-top:2em;"><?php esc_html_e('Create an event', 'cluster-one-kleinrock'); ?></h2>
<?php if ($pick !== '') : ?>
    <?php /* translators: %s: gewaehlter Tag. */ ?>
    <p style="max-width:760px;"><?php echo esc_html(sprintf(__('Chosen day: %s. Everything else follows below — a day first, a name second.', 'cluster-one-kleinrock'), $pick)); ?></p>
<?php else : ?>
    <p style="max-width:760px;opacity:.85;"><?php esc_html_e('Click a day in the calendar above. Nothing is created by clicking — the day only fills the field below.', 'cluster-one-kleinrock'); ?></p>
<?php endif; ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:760px;">
        <?php wp_nonce_field('cluster_one_gregor_event_create'); ?>
        <input type="hidden" name="action" value="cluster_one_gregor_event_create">
        <table class="form-table" role="presentation"><tbody>
            <tr>
                <th scope="row"><label for="c1_ev_prop"><?php esc_html_e('Proposed day', 'cluster-one-kleinrock'); ?></label></th>
                <td><input id="c1_ev_prop" name="proposal" type="date" value="<?php echo esc_attr($pick); ?>" min="<?php echo esc_attr(current_time('Y-m-d')); ?>" required><?php /* v0.3.16: kein Rueckwaertsgang */ ?>
                    <p class="description"><?php esc_html_e('A proposal, not a decision — everyone can mark a different day, and the SysOp locks one once it has settled.', 'cluster-one-kleinrock'); ?></p></td>
            </tr>
            <tr>
                <th scope="row"><label for="c1_ev_time"><?php esc_html_e('Time of day', 'cluster-one-kleinrock'); ?></label></th>
                <td><input id="c1_ev_time" name="time" type="time" value=""></td>
            </tr>
            <tr>
                <th scope="row"><label for="c1_ev_name"><?php esc_html_e('Name', 'cluster-one-kleinrock'); ?></label></th>
                <td><input id="c1_ev_name" name="name" type="text" class="regular-text" required></td>
            </tr>
<?php /* 07.10.2026 — DIE ART IST JETZT EIN KREUZ UND KEINE FOLGE.
         Bis hierher war die Art, WELCHES FELD GEFUELLT IST (v0.1.4, „Ort
         gewinnt"). Das ist eine Regel, die man nur kennt, wenn man sie
         gelesen hat: wer Ort UND Link eintrug, bekam ein physisches Treffen
         und keine Auskunft darueber, warum.
             Eine Entscheidung, die aus einer Nebenwirkung folgt, hat
             niemand getroffen.
         Der Riegel im Handler liest jetzt `art`; die alte Regel bleibt
         dort als Rueckfall, damit ein alter Entwurf nichts verliert. */
      $c1_virt = cluster_one_virtuell_moeglich(); ?>
            <tr>
                <th scope="row"><?php esc_html_e('Kind of meeting', 'cluster-one-kleinrock'); ?></th>
                <td>
<?php if ($c1_virt) : ?>
                    <label style="display:block;margin-bottom:.3em;"><input type="radio" name="art" value="physisch" checked> <?php esc_html_e('Physical — at a place', 'cluster-one-kleinrock'); ?></label>
                    <label style="display:block;"><input type="radio" name="art" value="virtuell"> <?php esc_html_e('Virtual — behind a link, inside this node', 'cluster-one-kleinrock'); ?></label>
<?php else : ?>
                    <input type="hidden" name="art" value="physisch">
                    <p><strong><?php esc_html_e('Physical — at a place', 'cluster-one-kleinrock'); ?></strong></p>
                    <?php /* KEIN KREUZ MIT EINEM ARM. Ein Auswahlfeld, das nur
                             eine Wahl hat, ist keine Wahl, sondern eine
                             Behauptung, es gaebe eine. Statt dessen der Satz,
                             der sagt, warum die andere fehlt. */ ?>
                    <p class="description" style="max-width:620px;"><?php esc_html_e('Only physical meetings are possible on this node. A virtual meeting happens inside the node, and for that its people need a place to talk — that comes with Cluster One — Bismarck.', 'cluster-one-kleinrock'); ?></p>
<?php endif; ?>
                </td>
            </tr>
<?php if ($c1_virt) : ?>
            <tr class="c1-art-virtuell">
                <th scope="row"><label for="c1_ev_link"><?php esc_html_e('Link (virtual, node-internal)', 'cluster-one-kleinrock'); ?></label></th>
                <td><input id="c1_ev_link" name="link" type="url" class="regular-text" placeholder="https://"></td>
            </tr>
<?php endif; ?>
            <tr class="c1-art-physisch">
                <th scope="row"><label for="c1_ev_place"><?php esc_html_e('Place (physical)', 'cluster-one-kleinrock'); ?></label></th>
                <td><input id="c1_ev_place" name="place" type="text" class="regular-text"></td>
            </tr>
            <tr class="c1-art-physisch">
                <th scope="row"><label for="c1_ev_plus"><?php esc_html_e('People you bring', 'cluster-one-kleinrock'); ?></label></th>
                <td>
                    <input id="c1_ev_plus" name="plus_count" type="number" min="0" max="99" value="0" style="width:5em;">
                    <?php /* 07.10.2026 — DAS FELD STAND BISHER NUR IM HANDLER.
                             cluster_one_gregor_event_create las $_POST['plus_count']
                             seit v0.0.77; das Formular hat es nie geschickt. Ein
                             Handler, der etwas liest, was kein Formular sendet, ist
                             eine Zusage an niemanden.               (P1-12)
                             NUR BEI PHYSISCH: hinter einem Link sitzt man allein
                             vor seinem Schirm — da gibt es nichts mitzubringen. */ ?>
                    <p class="description"><?php esc_html_e('Only for a physical meeting: how many people you bring besides yourself. Changeable later, by you until the day has passed and by the SysOp after it.', 'cluster-one-kleinrock'); ?></p>
                </td>
            </tr>
        </tbody></table>
        <?php submit_button(__('Create event', 'cluster-one-kleinrock'), 'primary', 'submit', false); ?>
    </form>
<?php if ($c1_virt) : ?>
    <script>
    /* Nur Anzeige. ENTSCHIEDEN WIRD IM HANDLER: wer dieses Skript abschaltet,
       bekommt kein anderes Treffen, sondern dasselbe. */
    (function () {
        var f = document.currentScript.previousElementSibling;
        if (!f) { return; }
        function zeig() {
            var a = f.querySelector('input[name="art"]:checked');
            var w = a ? a.value : 'physisch';
            f.parentNode.querySelectorAll('.c1-art-physisch').forEach(function (r) { r.style.display = (w === 'physisch') ? '' : 'none'; });
            f.parentNode.querySelectorAll('.c1-art-virtuell').forEach(function (r) { r.style.display = (w === 'virtuell') ? '' : 'none'; });
        }
        f.querySelectorAll('input[name="art"]').forEach(function (r) { r.addEventListener('change', zeig); });
        zeig();
    })();
    </script>
<?php endif; ?>
    <?php
}

/* v0.3.15: Der Archimedes-Blick, zweigeteilt. Christians Argument: Ein
   physisches Treffen ueber Knoten hinweg ist psychologisch etwas anderes als
   der Betrieb — im Plateau lag zwischen Ruf und Treffen ein halbes Jahr
   Vorbereitung. Dafuer braucht es eine Stelle, an der man sieht, worauf man
   sich freuen kann und was auf einen zukommt.

   Der Reiter zeigt nichts doppelt: Events sagt WAS ES GIBT, hier steht WER
   KOMMT — und das stand bisher nirgends aggregiert, sondern nur als einzelne
   Systemnachrichten im Label-Thread. */
function cluster_one_render_archimedes_tab(): void {
    $eigene = array();
    $fremde = array();
    foreach (cluster_one_get_gregor_events() as $ev) {
        if (!empty($ev['archimedes'])) {
            $fremde[] = $ev;
        } elseif (cluster_one_archimedes_is_open($ev)) {
            $eigene[] = $ev;
        }
    }
    /* v0.3.18: Die dritte Haelfte, ohne die die beiden anderen sinnlos waren:
       Was laden ANDERE Knoten aus? Route und Abholfunktion standen seit 0.3.15,
       aber kein Element zeigte sie an — ein Gast konnte nie adoptieren.

       Live aus den aktiven Kanten geholt, kein Zwischenspeicher (dieselbe Regel
       wie bei den Vorschlaegen: ein veralteter Termin ist schlimmer als
       keiner). Was schon uebernommen ist, faellt weg. */
    $schon = array();
    foreach ($fremde as $c1_f) {
        $schon[(string) ($c1_f['archimedes']['label'] ?? '')] = true;
    }
    $einladungen = array();
    if (cluster_one_is_sysop()) {
        if (!function_exists('cluster_one_get_paired_map') || !function_exists('cluster_one_fetch_meetings')) { $c1_fremd = array(); }
        else foreach (cluster_one_get_paired_map() as $c1_e) {
            if (($c1_e['status'] ?? '') !== 'active' || empty($c1_e['url'])) {
                continue;
            }
            foreach (cluster_one_fetch_meetings((string) $c1_e['url']) as $c1_m) {
                if (empty($schon[$c1_m['id']])) {
                    $einladungen[] = $c1_m;
                }
            }
        }
    }
    ?>
<?php if (cluster_one_is_sysop()) : ?>
    <h2 style="margin-top:1em;"><?php esc_html_e('Invitations from other nodes', 'cluster-one-kleinrock'); ?></h2>
    <p style="max-width:820px;opacity:.85;"><?php esc_html_e('What the nodes you are connected to are hosting right now — fetched live, nothing stored. Taking one on creates a meeting here with the date fixed: your people then mark whether they are coming, and that number is what travels back.', 'cluster-one-kleinrock'); ?></p>
<?php if (!$einladungen) : ?>
    <p style="opacity:.6;"><?php esc_html_e('No open invitation anywhere. A meeting only travels once its host has opened it — day, hour and place settled.', 'cluster-one-kleinrock'); ?></p>
<?php else : ?>
    <table class="widefat striped" style="max-width:960px;">
        <thead><tr>
            <th><?php esc_html_e('Meeting', 'cluster-one-kleinrock'); ?></th>
            <th style="width:12em;"><?php esc_html_e('Day', 'cluster-one-kleinrock'); ?></th>
            <th><?php esc_html_e('Place', 'cluster-one-kleinrock'); ?></th>
            <th style="width:14em;"><?php esc_html_e('Node (URL)', 'cluster-one-kleinrock'); ?></th>
            <th style="text-align:right;width:8em;"><?php esc_html_e('Actions', 'cluster-one-kleinrock'); ?></th>
        </tr></thead>
        <tbody>
<?php foreach ($einladungen as $c1_m) : ?>
            <tr>
                <td style="white-space:nowrap;max-width:20em;overflow:hidden;text-overflow:ellipsis;"><?php echo esc_html($c1_m['name']); ?></td>
                <td style="white-space:nowrap;"><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($c1_m['date'])) . ' · ' . $c1_m['time']); ?></td>
                <td style="white-space:nowrap;max-width:16em;overflow:hidden;text-overflow:ellipsis;" title="<?php echo esc_attr($c1_m['place']); ?>"><?php echo esc_html($c1_m['place']); ?></td>
                <td style="white-space:nowrap;max-width:14em;overflow:hidden;text-overflow:ellipsis;" title="<?php echo esc_attr($c1_m['url']); ?>"><?php echo esc_html($c1_m['url']); ?></td>
                <td style="text-align:right;">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                        <?php wp_nonce_field('cluster_one_archimedes_adopt'); ?>
                        <input type="hidden" name="action" value="cluster_one_archimedes_adopt">
                        <input type="hidden" name="host" value="<?php echo esc_attr($c1_m['url']); ?>">
                        <input type="hidden" name="label" value="<?php echo esc_attr($c1_m['id']); ?>">
                        <button type="submit" class="button button-small"><?php esc_html_e('Take it on', 'cluster-one-kleinrock'); ?></button>
                    </form>
                </td>
            </tr>
<?php endforeach; ?>
        </tbody>
    </table>
<?php endif; endif; ?>

    <h2 style="margin-top:2.2em;"><?php esc_html_e('Our invitations', 'cluster-one-kleinrock'); ?></h2>
    <p style="max-width:820px;opacity:.85;"><?php esc_html_e('Meetings here that are open to other nodes. Whoever answers says how many are coming — they cannot move the date.', 'cluster-one-kleinrock'); ?></p>
<?php if (!$eigene) : ?>
    <p style="opacity:.6;"><?php esc_html_e('None open. A meeting travels once the day is locked, the hour is set and a place is named — then you open it on the event page.', 'cluster-one-kleinrock'); ?></p>
<?php else : foreach ($eigene as $c1_ev) :
    $c1_rs   = (array) ($c1_ev['rsvps'] ?? array());
    $c1_fest = (string) ($c1_ev['scheduling']['locked_date'] ?? '');
    $c1_hier = 0;
    foreach ((array) ($c1_ev['scheduling']['markers'] ?? array()) as $c1_m) {
        if (cluster_one_gregor_marker_date($c1_m) === $c1_fest) {
            $c1_hier += 1 + cluster_one_gregor_marker_plus($c1_m);
        }
    }
    $c1_summe = $c1_hier;
    foreach ($c1_rs as $c1_r) { $c1_summe += (int) ($c1_r['count'] ?? 0); }
?>
    <h3 style="margin-top:1.4em;margin-bottom:.2em;">
        <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode((string) $c1_ev['id']))); ?>"><?php echo esc_html((string) ($c1_ev['name'] ?? '')); ?></a>
    </h3>
    <p style="margin:.1em 0 .5em;opacity:.85;"><?php echo esc_html(trim(
        date_i18n(get_option('date_format'), strtotime($c1_fest)) . ' · ' . (string) ($c1_ev['time'] ?? '')
        . ' · ' . (string) ($c1_ev['place'] ?? '')
    )); ?></p>
    <table class="widefat striped" style="max-width:720px;">
        <thead><tr>
            <th><?php esc_html_e('Node', 'cluster-one-kleinrock'); ?></th>
            <th style="width:8em;text-align:right;"><?php esc_html_e('Coming', 'cluster-one-kleinrock'); ?></th>
            <th style="width:12em;"><?php esc_html_e('Last word', 'cluster-one-kleinrock'); ?></th>
        </tr></thead>
        <tbody>
            <tr>
                <td><strong><?php esc_html_e('Here', 'cluster-one-kleinrock'); ?></strong></td>
                <td style="text-align:right;"><strong><?php echo (int) $c1_hier; ?></strong></td>
                <td style="opacity:.6;"><?php esc_html_e('marks on the locked day', 'cluster-one-kleinrock'); ?></td>
            </tr>
<?php foreach ($c1_rs as $c1_key => $c1_r) :
    /* v0.4.1: Gelesen wird der Name, unterschieden wird ueber die Adresse.
       Aeltere Eintraege tragen noch keinen Namen — dann steht die Adresse da,
       das ist ehrlicher als ein erfundenes Etikett. */
    $c1_lab = trim((string) ($c1_r['node'] ?? ''));
    $c1_url = trim((string) ($c1_r['url'] ?? $c1_key));
?>
            <tr>
                <td style="white-space:nowrap;max-width:20em;overflow:hidden;text-overflow:ellipsis;" title="<?php echo esc_attr($c1_url); ?>"><?php echo esc_html($c1_lab !== '' ? $c1_lab : $c1_url); ?></td>
                <td style="text-align:right;"><?php echo (int) ($c1_r['count'] ?? 0); ?></td>
                <td style="opacity:.75;"><?php echo esc_html(date_i18n(get_option('date_format'), (int) ($c1_r['ts'] ?? 0))); ?></td>
            </tr>
<?php endforeach; ?>
<?php if (!$c1_rs) : ?>
            <tr><td colspan="3" style="opacity:.6;"><?php esc_html_e('No answer from another node yet.', 'cluster-one-kleinrock'); ?></td></tr>
<?php endif; ?>
        </tbody>
        <tfoot><tr>
            <th><?php esc_html_e('Altogether', 'cluster-one-kleinrock'); ?></th>
            <th style="text-align:right;"><?php echo (int) $c1_summe; ?></th>
            <th></th>
        </tr></tfoot>
    </table>
<?php endforeach; endif; ?>

    <h2 style="margin-top:2.2em;"><?php esc_html_e('Where we are going', 'cluster-one-kleinrock'); ?></h2>
<?php if (!$fremde) : ?>
    <p style="opacity:.6;"><?php esc_html_e('Nothing adopted. Invitations from other nodes appear here once you take one on.', 'cluster-one-kleinrock'); ?></p>
<?php else : ?>
    <table class="widefat striped" style="max-width:900px;">
        <thead><tr>
            <th><?php esc_html_e('Meeting', 'cluster-one-kleinrock'); ?></th>
            <th style="width:12em;"><?php esc_html_e('Day', 'cluster-one-kleinrock'); ?></th>
            <th><?php esc_html_e('Host', 'cluster-one-kleinrock'); ?></th>
            <th style="width:8em;text-align:right;"><?php esc_html_e('From here', 'cluster-one-kleinrock'); ?></th>
        </tr></thead>
        <tbody>
<?php foreach ($fremde as $c1_ev) :
    $c1_fest = (string) ($c1_ev['scheduling']['locked_date'] ?? '');
    $c1_wir  = 0;
    foreach ((array) ($c1_ev['scheduling']['markers'] ?? array()) as $c1_m) {
        if (cluster_one_gregor_marker_date($c1_m) === $c1_fest) {
            $c1_wir += 1 + cluster_one_gregor_marker_plus($c1_m);
        }
    }
?>
            <tr>
                <td style="white-space:nowrap;max-width:22em;overflow:hidden;text-overflow:ellipsis;">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode((string) $c1_ev['id']))); ?>"><?php echo esc_html((string) ($c1_ev['name'] ?? '')); ?></a>
                </td>
                <td style="white-space:nowrap;"><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($c1_fest))); ?></td>
                <td style="white-space:nowrap;opacity:.85;max-width:16em;overflow:hidden;text-overflow:ellipsis;"><?php echo esc_html((string) ($c1_ev['archimedes']['node'] ?? '')); ?></td>
                <td style="text-align:right;"><?php echo (int) $c1_wir; ?></td>
            </tr>
<?php endforeach; ?>
        </tbody>
    </table>
<?php endif;
}

/* ============================================================
   DAS KALENDERGITTER (v0.9.63, Umzug aus bismarck-seiten.php)
   Sechzig Zeilen, die Gregors Termine zeichnen — und die nur von genau EINER
   Stelle gerufen werden: cluster_one_render_gregor_page(). Sie lasen schon
   immer Gregors Daten (get_gregor_events, gregor_marker_date/_plus) und
   standen doch bei den Bismarck-Seiten, weil der Kalender bis v0.0.53 ein
   Bismarck-Reiter war. Der Reiter zog um, das Gitter blieb liegen.

   Gefunden von der Seitenprobe: Es war der letzte Grund, aus dem Cluster One
   Light seine Events-Seite nicht zeichnen konnte.

   DIE NAMEN LUEGEN NOCH. 'bismarck_calendar' in gregor.php waere eine frische
   Luege, also werden sie im selben Zug richtiggestellt — siehe unten.
   ============================================================ */

/** Kalender-Gerüst (v0.0.51, Reißbrett): auf Christians eigenen Wunsch NUR
   die Felder — Monatsraster, Vor/Zurück-Blättern (reine Navigation, keine
   Datenspeicherung) — noch OHNE jede Interaktion. Kein Klick auf einen Tag
   tut irgendwas, bewusst so, um die künftige Struktur überschaubar zu
   machen, bevor die Wähl-/Rudel-Mechanik selbst gebaut wird. */
function cluster_one_render_gregor_calendar(): void {
    /* v0.7.1: Das Reissbrett-Band ist weg — Christians Wunsch. Es markierte
       ein Geruest ohne Funktion; der Kalender hat seit 0.1.4 Ort, Zeit, Link
       und die Archimedes-Schleife. Ein Schild, das "noch nichts hier" sagt,
       waehrend darunter etwas laeuft, ist ein luegender Text. */

    $today = new DateTimeImmutable('now', wp_timezone());
    $y = isset($_GET['y']) ? (int) $_GET['y'] : (int) $today->format('Y'); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $m = isset($_GET['m']) ? (int) $_GET['m'] : (int) $today->format('n'); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    if ($m < 1 || $m > 12) {
        $m = (int) $today->format('n');
    }
    $first    = DateTimeImmutable::createFromFormat('Y-n-j', "$y-$m-1", wp_timezone());
    $days_in  = (int) $first->format('t');
    // Montag zuerst (deutsche Konvention): ISO-Wochentag 1..7, hier auf 0..6 für den Raster-Versatz.
    $offset   = ((int) $first->format('N')) - 1;
    $prev     = $first->modify('-1 month');
    $next     = $first->modify('+1 month');
    // v0.0.96: FOSSIL-FIX (Fund SysOp): zeigte auf page=cluster_one_bismarck
    // &tab=calendar — aus der Ära vor 0.0.53, als Gregor Bismarck-Unterreiter
    // war. Den Tab 'calendar' kennt die Whitelist nicht -> stiller Rückfall
    // auf Chats. Monatsblättern landete deshalb im falschen Zimmer.
    $base_url = admin_url('admin.php?page=cluster_one_gregor');
    $wd_names = array(
        __('Mon', 'cluster-one-kleinrock'), __('Tue', 'cluster-one-kleinrock'), __('Wed', 'cluster-one-kleinrock'), __('Thu', 'cluster-one-kleinrock'),
        __('Fri', 'cluster-one-kleinrock'), __('Sat', 'cluster-one-kleinrock'), __('Sun', 'cluster-one-kleinrock'),
    );
    // v0.0.76: Terminfindung im Kalender — pro Tag: eingeloggte Ereignisse
    // (grün, Name) und offene Marker (grauer Punkt mit Zahl; Titel nennt
    // Ereignis und Namen — Marker tragen Namen, die Zahl ist Verdichtung).
    $c1_cal = array(); // 'Y-m-d' => Liste von ['name','count','locked','who']
    foreach (cluster_one_get_gregor_events() as $c1_ev) {
        $c1_s = isset($c1_ev['scheduling']) && is_array($c1_ev['scheduling']) ? $c1_ev['scheduling'] : null;
        if ($c1_s === null) { continue; }
        /* 07.10.2026: GERUFEN, NICHT GETIPPT. Hier stand die Bedingung
           ausgeschrieben, und damit war sie von keinem Zeugen erreichbar.
           Jetzt fragt der Kalender dasselbe wie die Messung — dieselbe
           Funktion, nicht derselbe Wortlaut.             (Hollerith 127) */
        if (cluster_one_gregor_eingerastet((array) $c1_ev)) {
            /* 07.10.2026: 'event' JETZT AUCH HIER. Bis heute wusste die
               eingerastete Zelle ihren Namen, aber nicht, wohin sie gehoert —
               und war damit die einzige Stelle im Kalender ohne Weg zu ihrem
               Ereignis. Gefunden an einem Quelltext, nicht an einer Quelle:
                   Ein Zeuge, der die Quelle prueft, sagt nichts darueber,
                   welchen Zweig die Seite genommen hat. */
            /* 'offen' seit 07.10.2026: die Rueckfrage beim Loesen aus dem
               Gitter muss denselben Zusatz tragen koennen wie die auf der
               Ereignisseite — dass andere Knoten dieses Datum schon haben.
               Ohne das Feld haette der Knopf im Kalender weniger gesagt als
               derselbe Knopf eine Seite weiter.
                   Zwei Ausloeser auf einen Handler duerfen verschieden
                   AUSSEHEN, aber nicht verschieden WARNEN. */
            $c1_cal[(string) $c1_s['locked_date']][] = array('name' => (string) ($c1_ev['name'] ?? ''), 'count' => 0, 'locked' => true, 'who' => '', 'event' => (string) ($c1_ev['id'] ?? ''), 'offen' => !empty($c1_ev['open_nodes']));
        } else {
            $c1_g = array(); $c1_gp = array(); $c1_gw = array();
            foreach ((array) ($c1_s['markers'] ?? array()) as $c1_k => $c1_m) {
                $c1_u = get_userdata((int) substr((string) $c1_k, 3));
                $c1_d = cluster_one_gregor_marker_date($c1_m);
                $c1_p = cluster_one_gregor_marker_plus($c1_m);
                $c1_g[$c1_d][]  = ($c1_u ? $c1_u->display_name : (string) $c1_k) . ($c1_p > 0 ? ' +' . $c1_p : '');
                $c1_gp[$c1_d]   = ($c1_gp[$c1_d] ?? 0) + 1 + $c1_p; // v0.0.77: Personensumme inkl. Gäste
                // v0.3.12: WER markiert hat — fuer Farbe und Kuerzel im Gitter.
                $c1_gw[$c1_d][] = array(
                    'key'   => (string) $c1_k,
                    'name'  => ($c1_u ? $c1_u->display_name : (string) $c1_k),
                    'plus'  => $c1_p,
                    'sysop' => ((int) substr((string) $c1_k, 3) === cluster_one_main_admin_user_id()),
                );
            }
            foreach ($c1_g as $c1_d => $c1_names) {
                $c1_cal[$c1_d][] = array('name' => (string) ($c1_ev['name'] ?? ''), 'count' => (int) $c1_gp[$c1_d], 'locked' => false, 'who' => implode(', ', $c1_names), 'leute' => (array) ($c1_gw[$c1_d] ?? array()), 'event' => (string) ($c1_ev['id'] ?? ''));
            }
        }
    }
    ?>
<?php cluster_one_kalender_kopf($base_url, $first, $prev, $next); ?><?php cluster_one_kalender_gitter($c1_cal, $days_in, $m, $offset, $wd_names, $y); ?>    <?php
}

function cluster_one_kalender_kopf($base_url, $first, $prev, $next): void { ?>    <h2 style="margin-top:1em;display:flex;align-items:center;gap:.8em;">
        <a href="<?php echo esc_url(add_query_arg(array('y' => $prev->format('Y'), 'm' => $prev->format('n')), $base_url)); ?>" class="button" aria-label="<?php echo esc_attr__('Previous month', 'cluster-one-kleinrock'); ?>">&larr;</a>
        <span><?php echo esc_html(date_i18n('F Y', $first->getTimestamp())); ?></span>
        <a href="<?php echo esc_url(add_query_arg(array('y' => $next->format('Y'), 'm' => $next->format('n')), $base_url)); ?>" class="button" aria-label="<?php echo esc_attr__('Next month', 'cluster-one-kleinrock'); ?>">&rarr;</a>
    </h2>
<?php
}

function cluster_one_kalender_gitter($c1_cal, $days_in, $m, $offset, $wd_names, $y): void { ?>    <table class="widefat" style="max-width:560px;text-align:center;table-layout:fixed;">
        <thead>
            <tr><?php foreach ($wd_names as $wd) : ?><th><?php echo esc_html($wd); ?></th><?php endforeach; ?></tr>
        </thead>
        <tbody>
            <tr>
                <?php for ($i = 0; $i < $offset; $i++) : ?><td>&nbsp;</td><?php endfor; ?>
                <?php $col = $offset; ?>
                <?php for ($day = 1; $day <= $days_in; $day++) :
                    if ($col === 7) { echo '</tr><tr>'; $col = 0; }
                    $col++;
                ?>
<?php
/* 07.10.2026 — DER SCHLUESSEL STEHT VOR DER ZELLE, UND ZWAR EINMAL.
   Meine erste Fassung des Ablage-Riegels tippte sprintf('%04d-%02d-%02d', …)
   ein zweites Mal in das <td>-Tag, weil $c1_key erst darunter gesetzt wurde.
   Zwei Formeln fuer denselben Tag — und die zweite veraltet an dem Tag, an
   dem jemand die erste anfasst.                                    (P1-12)
   Also wird er hochgezogen. Dieselbe Zahl, eine Stelle. */
$c1_key = sprintf('%04d-%02d-%02d', $y, $m, $day);
/* 07.10.2026, HOLLERITHS BEFUND 2 — UND ES WAR DIESELBE SORTE, ZWANZIG
   ZEILEN UNTER DER STELLE, AN DER ICH SIE GERADE BEHOBEN HATTE.
   Hier standen DREI Fragen nach demselben Tag, mit DREI Rufen von
   current_time():
       $c1_ablage = ($c1_key >= current_time('Y-m-d'))     :2181
       $c1_heut   = ($c1_key === current_time('Y-m-d'))    :2195
       $c1_weg    = ($c1_key <  current_time('Y-m-d'))     :2201
   $c1_weg ist die exakte Negation von $c1_ablage. Rendert eine Anfrage
   ueber Mitternacht, widersprechen sich die beiden fuer genau einen Tag:
   die Ziffer zeichnet sich als vergangen, das <td> ist noch Ablageort.
       Drei Rufe an die Uhr in einer Zelle sind drei Uhrzeiten.
   EIN Ruf, drei Antworten daraus. */
$c1_heute  = current_time('Y-m-d');
$c1_ablage = ($c1_key >= $c1_heute);
?>
                    <td style="padding:.5em 0 .4em;opacity:.85;vertical-align:top;position:relative;"<?php echo $c1_ablage ? ' class="c1-ablage" data-c1-tag="' . esc_attr($c1_key) . '"' : ''; ?>>
<?php
/* v0.3.12: Der Tag ist ein Link. Er legt NICHTS an — er traegt das Datum ins
   Formular unter dem Kalender. Ein Fehlklick soll kein Event erzeugen. */
/* v0.3.14: Der gewaehlte Tag ist SOFORT sichtbar — vorher stand er nur als
   Text unter dem Kalender, und man sah im Gitter nicht, worauf man geklickt
   hatte. Ein zweiter Klick auf denselben Tag nimmt die Wahl zurueck; dann
   verschwindet auch das Datum unten. */
$c1_gew  = (isset($_GET['pick']) && (string) $_GET['pick'] === $c1_key); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
$c1_ziel = $c1_gew
    ? remove_query_arg('pick', admin_url('admin.php?page=cluster_one_gregor'))
    : add_query_arg('pick', $c1_key, admin_url('admin.php?page=cluster_one_gregor'));
$c1_heut = ($c1_key === $c1_heute);
/* v0.3.16: Vergangene Tage sind keine Links. Der Handler wies sie laengst ab
   ("Zeitreisen koennen wir leider noch nicht"), aber die Oberflaeche lud
   trotzdem ein: Man klickte, das Feld fuellte sich, und beim Absenden passierte
   kommentarlos nichts. Dieselbe Lehre wie 0.3.5 — eine Sperre im Handler ist
   notwendig und nicht hinreichend. */
$c1_weg = !$c1_ablage;   /* die Negation, nicht eine zweite Frage */
if ($c1_weg) : ?>
                        <span style="opacity:.35;" title="<?php echo esc_attr__('Past — a meeting cannot be proposed backwards', 'cluster-one-kleinrock'); ?>"><?php echo (int) $day; ?></span>
<?php else : ?>
<?php /* 07.10.2026 — DAS KAESTCHEN WIRD EIN RAHMEN.
         Christian hat es als „der Tag ist gesetzt" gelesen und zweimal
         draufgedrueckt, um ihn wieder zu loesen. Das war keine Verwechslung
         von ihm, sondern eine falsche Auskunft von mir: auf einem Gitter
         standen drei Zeichen, und das lauteste bedeutete am wenigsten.
             gruener Punkt    der Tag STEHT            gespeichert
             farbiger Punkt   jemand hat markiert      gespeichert
             blaues Kaestchen nur fuers Formular       Adresszeile
         AB HIER: gefuellt heisst gespeichert. Die Vormerkung bekommt einen
         Rahmen und behaelt ihre Schriftfarbe. Es ist derselbe Blauton — die
         Sache ist nicht unsichtbar, sie ist nur nicht mehr laut. */ ?>
                        <a href="<?php echo esc_url($c1_ziel); ?>" style="text-decoration:none;<?php echo $c1_heut ? 'font-weight:700;' : ''; ?><?php echo $c1_gew ? 'display:inline-block;min-width:1.8em;border-radius:3px;box-shadow:inset 0 0 0 2px #2271b1;padding:.1em 0;' : ''; ?>"
                           title="<?php echo esc_attr($c1_gew
                               ? __('Pre-filled into the form below — click again to take it back. Nothing is set by this.', 'cluster-one-kleinrock')
                               /* translators: %s: Datum (JJJJ-MM-TT), das vorbelegt wird. */
                               : sprintf(__('Pre-fill %s into the form below — nothing is set by this', 'cluster-one-kleinrock'), $c1_key)); ?>"><?php echo (int) $day; ?></a>
<?php endif; ?>
<?php if (isset($c1_cal[$c1_key])) : foreach ($c1_cal[$c1_key] as $c1_e) : ?>
<?php if ($c1_e['locked']) : ?>
<?php       /* 07.10.2026 — DER EINGERASTETE EINTRAG BEKOMMT EINEN WEG.
               Hier liegt KEIN Anfasser und KEINE Tafel, und das ist Absicht:
               der Tag steht. Aber er muss sagen, wo er verhandelt wird, sonst
               ist die Zelle eine Sackgasse. Entrastet wird auf der
               Ereignisseite, nicht hier — EINE Stelle fuer eine Entscheidung,
               und die mit dem Platz fuer den Grund. */
            if ((string) ($c1_e['event'] ?? '') !== '') : ?>
                            <div style="font-size:.72em;line-height:1.3;font-weight:600;">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode((string) $c1_e['event']))); ?>"
                                   style="color:#1a7f37;text-decoration:none;"
                                   title="<?php echo esc_attr($c1_e['name'] . ' — ' . __('locked in', 'cluster-one-kleinrock')); ?>">● <?php echo esc_html($c1_e['name']); ?></a>
                            </div>
<?php           /* 07.10.2026 — DER LOESEKNOPF STEHT JETZT HIER.
                   Christian: drei Schritte, um einen Tag wieder freizugeben —
                   Link anklicken, Seite lesen, Knopf druecken. Das ist ein
                   Weg und keine Bedienung.
                       Ein Rueckweg, der laenger ist als der Hinweg, wird
                       nicht benutzt, sondern umgangen.
                   EIN HANDLER, ZWEI AUSLOESER. Das ist kein zweiter
                   Schreibweg: cluster_one_gregor_unlock bleibt die einzige
                   Stelle, die den Tag loest, mit demselben Riegel und
                   derselben Rueckfrage. Nur der Finger ist naeher dran.
                   NUR SYSOP, wie auf der Ereignisseite — und wer nicht darf,
                   sieht keinen Knopf statt einen 403 zu bekommen. */
                if (cluster_one_is_sysop()) :
                    $c1_lfrage = __('Release the day again? The marks stay and the day is open for proposals.', 'cluster-one-kleinrock');
                    if (!empty($c1_e['offen'])) {
                        $c1_lfrage .= ' ' . __('This meeting is open to other nodes — they already hold this date.', 'cluster-one-kleinrock');
                    }
?>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;line-height:1.2;">
                                <?php wp_nonce_field('cluster_one_gregor_unlock'); ?>
                                <input type="hidden" name="action" value="cluster_one_gregor_unlock">
                                <input type="hidden" name="event" value="<?php echo esc_attr((string) $c1_e['event']); ?>">
                                <input type="hidden" name="zurueck" value="kalender">
                                <button type="submit" style="background:none;border:0;padding:0;margin:0;font-size:.68em;color:#2271b1;cursor:pointer;text-decoration:underline;font-family:inherit;" onclick="return confirm('<?php echo esc_js($c1_lfrage); ?>');"><?php esc_html_e('Unlock the day', 'cluster-one-kleinrock'); ?></button>
                            </form>
<?php           endif; ?>
<?php       else : ?>
                            <div style="font-size:.72em;line-height:1.3;color:#1a7f37;font-weight:600;"
                                 title="<?php echo esc_attr($c1_e['name'] . ' — ' . __('locked in', 'cluster-one-kleinrock')); ?>">
                                ● <?php echo esc_html($c1_e['name']); ?>
                            </div>
<?php       endif; ?>
<?php else : ?>
<?php
/* 07.10.2026 — DIE TAFEL AM TAG.
 *
 * Christian: „Es waere vielleicht sinnvoll, eine Art Tooltip einzufuehren …
 * da steht dann eine Liste der User, die sich eingetragen haben, mit denen,
 * die sie mitbringen." Und: „ich sollte die Anzahl der Leute gleich im
 * Kalender aendern koennen."
 *
 * SIE HAENGT AN DER PUNKTREIHE, NICHT AN DER TAGESZIFFER. Die Ziffer ist
 * seit v0.3.12 ein Link, der das Datum ins Anlege-Formular traegt. Zwei
 * Bedeutungen auf einen Klick zu legen waere genau die Falle, die wir in
 * dieser Datei dreimal ausgebaut haben.
 *     Eine Flaeche, die zwei Dinge tut, tut beim Fehlklick das andere.
 *
 * <details> UND KEIN JAVASCRIPT. Der Browser kann das Oeffnen und Schliessen
 * selbst; ein Skript dafuer waere ein zweiter Weg zu einer Wahrheit, die der
 * Browser schon kennt. Die Tafel schwebt (position:absolute in einer
 * position:relative-Zelle), damit die 80 Pixel der Zelle sie nicht
 * zusammenquetschen und das Gitter nicht springt.
 *     BENANNTE GRENZE: setzt ein Thema overflow:hidden auf die Tabelle,
 *     wird die Tafel beschnitten. Gemessen ist das nicht — widefat setzt
 *     es nicht.
 */
$c1_tafel_id = 'c1-tafel-' . $c1_key . '-' . substr(md5((string) ($c1_e['event'] ?? '')), 0, 6);
$c1_meiner   = 'wp_' . get_current_user_id();
$c1_mein_plus = null;
foreach ((array) ($c1_e['leute'] ?? array()) as $c1_p) {
    if ((string) $c1_p['key'] === $c1_meiner) { $c1_mein_plus = (int) $c1_p['plus']; }
}
?>
                            <details style="font-size:.72em;line-height:1.3;">
                                <summary style="list-style:none;cursor:pointer;color:#8a8a8a;"
                                         <?php /* translators: %1$s: Name des Termins, %2$s: wer kommt. */ ?>
                                         title="<?php echo esc_attr(sprintf(__('%1$s — %2$s. Click for the list.', 'cluster-one-kleinrock'), $c1_e['name'], $c1_e['who'])); ?>">
<?php   /* v0.3.12: Ein Punkt pro Mensch, in SEINER Farbe, mit Kuerzel daneben.
           Farbe allein reicht nicht — wer farbenblind ist, liest die Initialen.
           Der SysOp bekommt einen Ring, damit sein Vorschlag als Vorschlag
           erkennbar bleibt und nicht als Beschluss.
           07.10.2026: UND DIE ZAHL STEHT JETZT DA. Sie lag schon im title —
           also an der Stelle, an die man mit der Maus muss. Eine Auskunft,
           die nur im Hover steht, hat ein Telefon nicht. */
        foreach ((array) ($c1_e['leute'] ?? array()) as $c1_person) :
            $c1_farbe = cluster_one_person_color((string) $c1_person['key']); ?>
<?php       /* 07.10.2026 — NUR DER EIGENE PUNKT IST ZIEHBAR, und nur, solange
               das Treffen nicht eingerastet ist. Nach dem Schnitt ist das
               Datum Fels; ein Punkt, den man dann noch ziehen KANN, waere
               eine Einladung zu einem Vorgang, den der Handler abweist.
                   Eine Gebaerde, die der Server zurueckweist, ist keine
                   Bedienung, sondern ein Versprechen.          (P1-12)

               ZUSATZ 07.10.2026, auf Holleriths Anmerkung: der Satz oben ist
               absolut geschrieben und widerspricht so gelesen der Wahl bei
               der Ablage weiter unten („lieber eine erlaubte Ablage, die der
               Server zurueckweist, als eine Flaeche, die ohne Grund tot
               ist"). Der Unterschied ist, OB DIE ZURUECKWEISUNG SICHER IST.
                   Ein eingerastetes Treffen wird SICHER abgewiesen — das
                   weiss die Seite, also zeichnet sie keinen Anfasser.
                   Ob eine erlaubte Ablage durchgeht, weiss sie NICHT — also
                   laesst sie sie zu und lebt mit dem Nein.
               Der absolute Satz gilt fuer das Erste. Fuer das Zweite gilt:
                   Wo die Seite das Nein nicht vorhersehen kann, ist das
                   Versprechen kein Betrug, sondern eine Frage. */
            $c1_mir = ((string) $c1_person['key'] === $c1_meiner) && (string) ($c1_e['event'] ?? '') !== ''; ?>
                                    <span style="white-space:nowrap;margin-right:.35em;color:<?php echo esc_attr($c1_farbe); ?>;<?php echo !empty($c1_person['sysop']) ? 'text-decoration:underline;' : ''; ?><?php echo $c1_mir ? 'cursor:grab;' : ''; ?>"
<?php       if ($c1_mir) : ?>
                                          draggable="true" class="c1-zieh"
                                          data-c1-ev="<?php echo esc_attr((string) $c1_e['event']); ?>"
                                          data-c1-plus="<?php echo (int) $c1_person['plus']; ?>"
                                          data-c1-von="<?php echo esc_attr($c1_key); ?>"
<?php       endif; ?>
                                          title="<?php echo esc_attr($c1_person['name'] . ($c1_person['plus'] > 0 ? ' +' . $c1_person['plus'] : '') . (!empty($c1_person['sysop']) ? ' — ' . __('SysOp: a proposal, not a decision', 'cluster-one-kleinrock') : '') . ($c1_mir ? ' — ' . __('drag onto another day to move it', 'cluster-one-kleinrock') : '')); ?>">●<?php echo esc_html(cluster_one_person_initials((string) $c1_person['key'])); ?><?php echo $c1_person['plus'] > 0 ? '+' . (int) $c1_person['plus'] : ''; ?></span>
<?php   endforeach; ?>
                                </summary>
                                <div style="position:absolute;z-index:20;left:0;top:100%;min-width:17em;text-align:left;background:#fff;border:1px solid #c3c4c7;border-radius:4px;box-shadow:0 2px 8px rgba(0,0,0,.14);padding:.7em .8em;font-size:1.15em;line-height:1.5;color:#2c3338;">
                                    <strong><?php echo esc_html((string) $c1_e['name']); ?></strong>
                                    <div style="opacity:.7;margin-bottom:.4em;"><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($c1_key))); ?></div>
<?php   foreach ((array) ($c1_e['leute'] ?? array()) as $c1_person) : ?>
                                    <div><span style="color:<?php echo esc_attr(cluster_one_person_color((string) $c1_person['key'])); ?>;">●</span>
                                        <?php echo esc_html((string) $c1_person['name']); ?><?php echo $c1_person['plus'] > 0 ? ' <strong>+' . (int) $c1_person['plus'] . '</strong>' : ''; ?><?php echo !empty($c1_person['sysop']) ? ' <span style="opacity:.6;">(' . esc_html__('SysOp', 'cluster-one-kleinrock') . ')</span>' : ''; ?></div>
<?php   endforeach; ?>
                                    <div style="margin-top:.4em;padding-top:.4em;border-top:1px solid #e0e0e0;">
                                        <?php /* translators: %d: Anzahl der Personen. */ ?>
                                        <strong><?php echo esc_html(sprintf(_n('%d person', '%d people', (int) $c1_e['count'], 'cluster-one-kleinrock'), (int) $c1_e['count'])); ?></strong>
                                    </div>
<?php   /* DAS FELD FUER DIE EIGENE ZAHL — und es steht hier, weil es in eine
           80-Pixel-Zelle nicht passt. Nur die EIGENE: fremde Zahlen
           berichtigt der SysOp auf der Ereignisseite, dort wo auch steht,
           dass er sie berichtigt. */
        if ($c1_mein_plus !== null && (string) ($c1_e['event'] ?? '') !== '') : ?>
                                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:.5em;display:flex;gap:.4em;align-items:center;">
                                        <?php wp_nonce_field('cluster_one_gregor_marker'); ?>
                                        <input type="hidden" name="action" value="cluster_one_gregor_marker">
                                        <input type="hidden" name="event" value="<?php echo esc_attr((string) $c1_e['event']); ?>">
                                        <input type="hidden" name="marker_date" value="<?php echo esc_attr($c1_key); ?>">
                                        <input type="hidden" name="zurueck" value="kalender">
                                        <label style="display:inline-flex;align-items:center;gap:.3em;"><?php esc_html_e('I bring', 'cluster-one-kleinrock'); ?> +<input type="number" name="plus_count" min="0" max="99" value="<?php echo (int) $c1_mein_plus; ?>" style="width:4.2em;"></label>
                                        <button type="submit" class="button button-small"><?php esc_html_e('Save', 'cluster-one-kleinrock'); ?></button>
                                    </form>
<?php   endif; ?>
                                    <div style="margin-top:.5em;"><a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode((string) ($c1_e['event'] ?? '')))); ?>"><?php esc_html_e('Open the event', 'cluster-one-kleinrock'); ?></a></div>
                                </div>
                            </details>
<?php endif; ?>
<?php endforeach; endif; ?>
                    </td>
                <?php endfor; ?>
<?php while ($col < 7) : ?><td>&nbsp;</td><?php $col++; endwhile; ?>
            </tr>
        </tbody>
    </table>
<?php /* v0.3.14: Der Satz stammte aus der Zeit, als der Kalender ein Reissbrett
   war. Seit 0.3.12 ist er das Formular — er log also. */ ?>
<?php /* 07.10.2026 — ZIEHEN. Christian: „Es waere auch schoen, wenn man in
   diesem Kalender seinen Eintrag auf einen der anderen Tage ziehen koennte."
   Gebaut NACH der Tafel und nicht statt ihr, und das ist der Punkt:
       DIE TAFEL IST DER WEG, DAS ZIEHEN IST DIE BEQUEMLICHKEIT.
   Auf einem Telefon gibt es keinen Zug ueber sieben Spalten; dort bleibt das
   Feld in der Tafel. Wer dieses Skript abschaltet, verliert eine Abkuerzung
   und keine Funktion.
   EIN FORMULAR FUER DAS GANZE GITTER, nicht eines je Zelle: 31 Formulare
   fuer eine Gebaerde waeren 31 Nonces fuer einen Klick. */ ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="c1-zieh-form" style="display:none;">
        <?php wp_nonce_field('cluster_one_gregor_marker'); ?>
        <input type="hidden" name="action" value="cluster_one_gregor_marker">
        <input type="hidden" name="zurueck" value="kalender">
        <input type="hidden" name="event" value="">
        <input type="hidden" name="marker_date" value="">
        <input type="hidden" name="plus_count" value="0">
    </form>
    <script>
    /* 07.10.2026, ZWEITE FASSUNG. Die erste hat EINMAL funktioniert und danach
       nicht mehr, und Christian hat es auf dem Schirm gefunden. Nachgesehen:
       das Markup war richtig, die drei Schwaechen lagen hier.

       a) DIE TAFEL LIEGT IM ABLAGEFELD. Der Panel-div ist ein Kind des <td>,
          und das <td> IST das Ablagefeld. Offen bubbelt jedes dragover aus
          ihr heraus zum eigenen Tag, und der Riegel verweigert dort die
          Ablage. Mit top:100% deckt sie die naechste Woche zu.
              -> Beim Zugbeginn wird JEDE offene Tafel geschlossen.
       b) DER PUNKT SASS IM <summary>. Ziehen und Aufklappen auf einer
          Flaeche — genau die Falle, vor der dieser Code beim Tagesklick
          warnt, und ich habe sie zwanzig Zeilen darunter selbst gebaut.
              -> Nach einem Zug wird der naechste Klick auf das <summary>
                 einmal verschluckt.
       c) DER ZUSTAND LAG IN EINER VARIABLEN, die dragend leert. Wer die
          Reihenfolge der Browser-Rufe voraussetzt, wettet.
              -> Die Fracht reist im dataTransfer. Die Variable ist nur noch
                 fuer die Hervorhebung da, und die Ablage braucht sie nicht.

           Ein Zustand in einer Variablen ist eine Wette darauf, in welcher
           Reihenfolge der Browser ruft.
    */
    (function () {
        var f = document.getElementById('c1-zieh-form');
        if (!f || !window.FormData) { return; }
        var zug = null, zugEnde = 0;
        /* 07.10.2026, HOLLERITHS BEFUND 1. Hier stand ein Flag `gezogen`, das
           NUR der verschluckte Klick geleert hat. dragend leerte `zug`, nicht
           das Flag. Nach einem ABGEBROCHENEN Zug — Escape, Ablage ausserhalb,
           Ablage auf dem eigenen Tag — wechselt die Seite nicht, das Flag
           bleibt wahr, und der naechste Klick auf IRGENDEINE Punktreihe im
           Gitter wird verschluckt.
               Ein Zustand, der nur durch ein Ereignis geleert wird, das
               vielleicht nie kommt, ist ein Leck. Einer, der von selbst
               verfaellt, ist eine Frist.
           Also eine Frist und kein Flag: dragend merkt sich die UHRZEIT, und
           der Klick wird nur verschluckt, wenn der Zug eben erst geendet hat.
           Danach heilt es sich selbst — gleich, ob ein Klick kam oder nicht.
           Und es leert sich nicht am Klick: der naechste echte Klick nach der
           Frist findet die Frist abgelaufen, nicht geleert. */

        /* 07.10.2026, DRITTE FASSUNG — UND DIE ZWEITE WAR MEIN EIGENER FEHLER.
         *
         * Christian sah einen roten Kreis mit Schraegstrich: dragover hat nie
         * preventDefault() gerufen. Der Grund steht in der Spezifikation und
         * nicht in diesem Code:
         *
         *     WAEHREND dragover/dragenter IST DER dataTransfer IM
         *     GESCHUETZTEN MODUS. getData() gibt dort eine LEERE Zeichenkette
         *     zurueck. Lesbar ist die Fracht erst im drop.
         *
         * Meine zweite Fassung las getData() auch im dragover, bekam dort
         * immer leer, hielt das fuer „kein Zug" und verweigerte die Ablage.
         * DIE ERSTE FASSUNG FUNKTIONIERTE, WEIL SIE DIE VARIABLE BENUTZTE —
         * ich habe mit dem Befund-3-Fix den einzigen an dieser Stelle lesbaren
         * Zustand entfernt.
         *
         *     Ein Zustand ist nicht dadurch schlecht, dass er ein Zustand
         *     ist. Er ist schlecht, wenn er lecken kann. Die Antwort darauf
         *     ist, ihn zu BEGRENZEN — nicht, ihn durch etwas zu ersetzen,
         *     das an der Stelle nicht lesbar ist.
         *
         * ALSO GETEILT, nach dem, was wo lesbar ist:
         *     dragover  entscheidet ueber dataTransfer.TYPES. Die Typenliste
         *               ist im geschuetzten Modus lesbar, nur die Werte nicht.
         *               Sie sagt „hier kommt eine Marke", und mehr braucht
         *               die Entscheidung nicht.
         *     drop      liest die FRACHT, wo sie lesbar ist, mit der Variablen
         *               als Rueckfall.
         * Die Variable bleibt — aber nur noch als Rueckfall und fuer die
         * Hervorhebung, und sie kann nicht mehr lecken: zugEnde ist eine
         * Frist, und die Ablage haengt nicht an ihr.
         */
        function hatMarke(e) {
            try {
                var t = e.dataTransfer.types;
                if (!t) { return false; }
                /* types ist in alten Browsern eine DOMStringList ohne
                   indexOf — deshalb beide Wege. */
                return (t.indexOf ? t.indexOf('application/x-cluster-one-marke') !== -1
                                  : Array.prototype.indexOf.call(t, 'application/x-cluster-one-marke') !== -1);
            } catch (x) { return false; }
        }

        function fracht(e) {
            /* NUR IM drop RUFEN. Im dragover gibt getData() leer zurueck. */
            try {
                var r = e.dataTransfer.getData('application/x-cluster-one-marke');
                if (r) { return JSON.parse(r); }
            } catch (x) {}
            return zug;
        }

        document.querySelectorAll('.c1-zieh').forEach(function (p) {
            p.addEventListener('dragstart', function (e) {
                zug = { ev: p.dataset.c1Ev, plus: p.dataset.c1Plus, von: p.dataset.c1Von };

                try {
                    e.dataTransfer.setData('application/x-cluster-one-marke', JSON.stringify(zug));
                    /* Ohne text/plain startet Firefox den Zug nicht. */
                    e.dataTransfer.setData('text/plain', p.dataset.c1Ev);
                } catch (x) {}
                e.dataTransfer.effectAllowed = 'move';
                /* (a) KEINE OFFENE TAFEL WAEHREND EINES ZUGES. */
                document.querySelectorAll('.c1-ablage details[open]').forEach(function (d) {
                    d.open = false;
                });
            });
            p.addEventListener('dragend', function () {
                zug = null;
                zugEnde = Date.now();   /* die Frist beginnt, nicht ein Flag */
                /* 07.10.2026, Holleriths Anmerkung 2: der gestrichelte Rahmen
                   wurde in dragleave und drop geloescht, aber nicht hier. Ein
                   abgebrochener Zug (Esc, Ablage ausserhalb) liess einen
                   Kasten stehen, bis man die Zelle noch einmal betrat.
                       Wer eine Hervorhebung setzt, muss JEDEN Ausgang
                       kennen, nicht die zwei haeufigen. */
                document.querySelectorAll('.c1-ablage').forEach(function (z) {
                    z.style.outline = '';
                });
            });
        });

        /* (b) Der Klick, mit dem ein Zug in manchen Browsern endet, klappt
               sonst die Tafel auf, die wir gerade zugemacht haben. Einmal
               verschlucken, dann wieder normal. */
        document.querySelectorAll('.c1-ablage summary').forEach(function (sm) {
            sm.addEventListener('click', function (e) {
                /* 400 ms: der nachgeschobene Klick kommt unmittelbar nach
                   dragend. Ein echter Klick vier Zehntel spaeter ist ein
                   echter Klick. */
                if (Date.now() - zugEnde < 400) { e.preventDefault(); }
            });
        });

        document.querySelectorAll('.c1-ablage').forEach(function (z) {
            z.addEventListener('dragover', function (e) {
                /* NUR die Typenliste — getData ist hier blind. Der Quelltag
                   wird, wenn die Variable da ist, mitgeprueft; fehlt sie,
                   erlaubt dragover trotzdem und der drop weist die Ablage auf
                   dem eigenen Tag ab. Lieber eine erlaubte Ablage, die der
                   Server zurueckweist, als eine Flaeche, die ohne Grund tot
                   ist. */
                if (!hatMarke(e)) { return; }
                if (zug && z.dataset.c1Tag === zug.von) { return; }
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                z.style.outline = '2px dashed #2271b1';
            });
            z.addEventListener('dragenter', function (e) {
                /* Manche Browser entscheiden schon hier, ob eine Zelle ein
                   Ablageort ist. Derselbe Riegel, dieselbe Frage. */
                if (!hatMarke(e)) { return; }
                if (zug && z.dataset.c1Tag === zug.von) { return; }
                e.preventDefault();
            });
            z.addEventListener('dragleave', function (e) {
                /* Nur loeschen, wenn der Zeiger die ZELLE verlaesst und nicht
                   bloss ein Kind in ihr. Sonst flackert der Rahmen. */
                if (!z.contains(e.relatedTarget)) { z.style.outline = ''; }
            });
            z.addEventListener('drop', function (e) {
                z.style.outline = '';
                var fr = fracht(e);
                if (!fr || z.dataset.c1Tag === fr.von) { return; }
                e.preventDefault();
                e.stopPropagation();
                /* DER SERVER ENTSCHEIDET. Dieses Skript schickt nur ab, was
                   die Tafel auch abgeschickt haette — derselbe Handler,
                   derselbe Nonce, dieselben Riegel. */
                f.elements['event'].value       = fr.ev;
                f.elements['marker_date'].value = z.dataset.c1Tag;
                f.elements['plus_count'].value  = fr.plus;
                f.submit();
            });
        });
    })();
    </script>
    <p class="description" style="margin-top:1em;max-width:600px;"><?php esc_html_e('Click a day to propose it. Coloured dots are the people who have marked that day — one dot per person, initials and the guests they bring. Click the dots for the list, where you can also change your own number. Your own dot can be dragged onto another day. A day only becomes final when the SysOp locks it.', 'cluster-one-kleinrock'); ?></p>
<?php
}

/* v1.0.1 — HEREINGEZOGEN AUS kern.php, byte-identisch (Regel 2).
   SHA8 0bfb47ba vorher wie nachher. Ihre beiden Aufrufer stehen in dieser
   Datei (:155 Absage, :654 Aenderung). Eine Funktion, die weiss, was ein
   Platon-Thread ist, gehoert nicht in den Boden. */
function cluster_one_archimedes_change_signal(array $ev): void {
    $tid = (string) ($ev['linked_thread'] ?? '');
    if ($tid === '') {
        return;
    }
    $threads = cluster_one_get_forum_threads();
    $lbl = (string) (($threads[$tid]['linked_label'] ?? ''));
    if ($lbl !== '' && isset($threads[$lbl]) && (string) ($threads[$lbl]['label_status'] ?? '') === 'published' && !cluster_one_container_is_archived($threads[$lbl])) {
        update_option('cluster_one_label_signal_ts', time(), false);
    }
}


/* --- DIE AUSKUNFT UEBER TREFFEN ---------------------------------------------
 *
 * Vertrag 21 / 7a: Kleinrocks Zaehler ruft heute cluster_one_archimedes_is_open()
 * direkt. Nach dem Schnitt waere das ein Ruf ueber eine Plugin-Grenze. Der Kreis
 * wird zu zwei Auskuenften — so, wie faust es mit ihren achtzehn kern-Aufrufen
 * ohnehin tut, nur in die andere Richtung.
 *
 *     apply_filters('cluster_one_treffen', null, 'offen')
 *
 * DER NAME TRAEGT DEN GEGENSTAND, DIE FRAGE STEHT IM ZWEITEN ARGUMENT.
 * Das ist nicht Geschmack, sondern die Form, die dieses Haus fuenfmal hat:
 *
 *     cluster_one_raum        + 'ki-name'
 *     cluster_one_umgebung    + 'notiz'
 *     cluster_one_forum       + 'bereiche'
 *     cluster_one_netz        + 'karte'
 *     cluster_one_erklaerung  + 'dna'   UND  + 'dna-pfad'
 *
 * Die letzte Zeile ist der Grund. Ein Haken nach dem Gegenstand kann eine
 * zweite Frage bekommen, ohne dass jemand einen zweiten Haken baut. Ein Haken,
 * der seine Frage im Namen traegt, kann nur einmal gefragt werden — der zweite
 * Frager bekommt dann keinen zweiten Wert, sondern einen zweiten Haken, und
 * damit einen zweiten Schreibweg auf dieselbe Sache.
 *
 * ICH HATTE IHN cluster_one_offene_treffen GENANNT. Das war die einzige Form,
 * die es hier nicht gibt. Kleinrock hatte gleichzeitig die richtige gebaut;
 * auf einem Knoten mit beiden Paketen haette sein Zaehler auf ewig „ich kann
 * es nicht sagen" gesendet — ohne Fatal, ohne Log, und es haette wie die
 * korrekte Antwort ausgesehen.
 *
 * DIESE ANMELDUNG ANTWORTET AUF GENAU EINE FRAGE. Auf jede andere gibt sie den
 * durchgereichten Wert unveraendert zurueck, damit ein spaeterer Antworter sie
 * beantworten kann. Ein Antworter, der fremde Fragen mit seiner eigenen
 * Antwort beantwortet, ist schlimmer als keiner.
 *
 * WARUM $c1_was KEINEN VORGABEWERT HAT — und das ist eine Entscheidung:
 *
 *     ohne Vorgabewert   ein Frager ohne Frage stirbt sichtbar
 *     mit Vorgabewert    ein Frager ohne Frage bekommt den Wert zurueck,
 *                        also null, also „es gibt hier keinen Antworter"
 *
 * Der zweite Fall ist genau der stille Ausfall, der diesen Haken gerade
 * umbenannt hat. Ein Vorgabewert waere hier ein Riegel, und ein Riegel
 * verwandelt ein Fatal in eine stille falsche Antwort.
 *
 * UND DESHALB DIE 2 AM ENDE. add_filter() reicht ohne sie nur EIN Argument
 * durch — die Anmeldung stuerbe an $c1_was, und mit einem Vorgabewert wuerde
 * sie stattdessen schweigend nie antworten. Die Zahl 2 ist der Teil dieser
 * Zeile, den man beim Umbenennen vergisst; sie ist bezeugt.
 *
 * WARUM DER VORGABEWERT DER FRAGENDEN SEITE null IST UND NICHT 0:
 *
 *     0      es gibt keine offenen Treffen     eine Messung
 *     null   ich kann es nicht sagen           keine Messung
 *
 * Die Zahl reist in Kleinrocks Ping auf einen FREMDEN Knoten und steht dort
 * ohne ihren Zusammenhang. Wer beides als 0 hinausschickt, behauptet dort
 * etwas, das er nicht gemessen hat.
 *
 *     Eine Null, die auch „weiss nicht" heissen kann, ist keine Zahl.
 *     Sie ist eine Vermutung in Zahlenform.
 *
 * DIESE SEITE ANTWORTET NUR. Sie gibt immer eine Zahl zurueck, wenn sie laeuft
 * — das Nichtwissen entsteht dadurch, dass sie NICHT laeuft, und das erkennt
 * die fragende Seite am Vorgabewert null, der unveraendert zurueckkommt.
 *
 * WAS DIE ZAHL BEDEUTET, und der Name sagt es absichtlich nicht schoener:
 *
 *     gezaehlt wird  open_nodes gesetzt UND ready()
 *     ZULAESSIG        „so viele Treffen sind fuer andere Knoten markiert"
 *     NICHT ZULAESSIG  „so viele Treffen kannst du besuchen"
 *
 * H143: auf einem Knoten ohne Mesh bleibt die Marke absichtlich stehen, damit
 * der SysOp sie zuruecknehmen kann. Ueber Erreichbarkeit sagt diese Zahl
 * nichts. Wer sie als Einladung liest, liest mehr, als hier steht.
 *
 * BEZEUGT: zeuge-treffen.php, sieben Welten, fuenf Gegenproben. */
add_filter('cluster_one_treffen', function ($c1_wert, $c1_was) {
    if ($c1_was !== 'offen') {
        return $c1_wert;
    }
    $c1_n = 0;
    foreach (cluster_one_get_gregor_events() as $c1_ev) {
        if (is_array($c1_ev) && cluster_one_archimedes_is_open($c1_ev)) {
            $c1_n++;
        }
    }
    return $c1_n;
}, 10, 2);

} /* Ende des Faust-Riegels (v1.0.5) */
