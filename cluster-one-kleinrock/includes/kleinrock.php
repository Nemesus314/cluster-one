<?php
/**
 * Cluster One — Modul kleinrock (DER SCHNITT, v0.0.100 -> Module).
 * Reiner Umzug aus cluster-one.php; jede Funktion byte-identisch zur Baseline.
 */
defined('ABSPATH') || exit;

/* v1.0.5 — DER RIEGEL FUER DEN AKTIVIERUNGS-MOMENT.

   Am 26.08.2026 hat Christian Bismarck-Light 0.1.0 auf der Main aktiviert:
   "Das Plugin konnte nicht aktiviert werden, da es einen fatalen Fehler
   ausgeloest hat." Die Kreuzprobe war 31/31 gruen — sie hatte recht und hat den
   falschen Moment gemessen.

   IM DAUERZUSTAND laedt jedes Light VOR Mammut (H43), das Light deklariert,
   Mammut sieht die Verdraengung und laesst seine Kopie liegen.

   BEI DER AKTIVIERUNG ist es umgekehrt: WordPress laedt erst alle AKTIVEN
   Plugins, dann bindet es die neue Datei ein (plugin_sandbox_scrape). Mammut
   ist dann der Erste, das Light der Zweite — und ohne Riegel:

       Cannot redeclare function cluster_one_has_room()

   Wir haben diesen Riegel zweimal gebaut und beide Male zu eng gefasst: fuer
   den Boden (kern, speicher, kasper) und fuer Faust. Jedes Mal war die
   Begruendung "diese Datei liegt in mehreren Plugins". Der Satz, den wir
   gebraucht haetten, ist groesser:

       BEI DER AKTIVIERUNG LIEGT JEDE LIGHT-DATEI IN ZWEI PLUGINS.

   Bezeugt von aktivierung.php, das genau diese Reihenfolge nachstellt. */

/* v0.2.8 — VERTRAG 27: DIE MELDUNG STEHT VOR DEM RIEGEL, NICHT IN IHM.
 *
 * Bis 0.2.7 stand cluster_one_hier('kleinrock') INNERHALB des Riegels. Liegt
 * eine zweite Kopie dieser Datei hoeher oder frueher, deklariert diese hier
 * nicht — und meldete sich dann auch nicht. Fuer Kasper war sie damit nicht
 * „vorhanden und still", sondern gar nicht vorhanden.
 *
 *     Wer nur meldet, wenn er gewinnt, erzeugt eine Liste der Sieger und
 *     nennt sie eine Liste der Anwesenden.
 *
 * DIE BEDINGUNG WIRD EINMAL GELESEN und traegt beide Folgen: was gemeldet
 * wird und was deklariert wird. Zwei Auswertungen derselben Bedingung waeren
 * zwei Wahrheiten, sobald eine von beiden beim naechsten Umbau nachzieht
 * (P1-12) — und die zweite faende niemand, weil beide dasselbe SAGEN.
 *
 * DER DRITTE WERT IST EINE AUSSAGE UEBER DIESES ORGAN, nicht ueber die
 * anderen. Er sagt „ich laufe" oder „ich liege hier und schweige". Was
 * daraus folgt, entscheidet Kasper und nicht diese Datei.
 *
 * ZWEI SACHEN, DIE HIER BEWUSST FEHLEN, damit niemand sie fuer erledigt haelt:
 *
 * 1  DIE MITTLERE ANGABE IST LEER. faust.php reicht an dieser Stelle
 *    CLUSTER_ONE_FAUST_FASSUNG durch; diese Datei hat keine solche Konstante,
 *    und eine anzulegen hiesse, eine Nummer einzutragen. Nummern gehoert der,
 *    der schneidet. Leer ist genau das, was bis 0.2.7 hier stand — es ist
 *    keine Verschlechterung, sondern eine unveraenderte Luecke mit einem
 *    Namen.
 *
 * 2  DER `_hoeher`-TEIL FEHLT. faust.php:60-78 sucht in den aktiven Plugins
 *    nach einer STRENG hoeheren Fassung derselben Datei und schweigt dann.
 *    Diese Bauart braucht die Fassungskonstante aus (1) — ohne sie gibt es
 *    nichts zu vergleichen. Solange sie fehlt, entscheidet allein
 *    function_exists, und das ist die Lage von heute: wer zuerst laedt,
 *    gewinnt.
 *
 *        Ein Riegel, der nur die halbe Bedingung kennt, ist nicht halb
 *        richtig. Er ist ganz richtig fuer die Haelfte der Faelle — und
 *        still fuer die andere.
 *
 *    Gemeldet, nicht gebaut. Beides haengt an derselben einen Zahl.
 */
$cluster_one_kleinrock_laeuft = !function_exists('cluster_one_get_peers');
cluster_one_hier('kleinrock', '', $cluster_one_kleinrock_laeuft);   // v0.9.72: Kasper

if ($cluster_one_kleinrock_laeuft) {

/** v1.0.8 — DIE FASSUNG, DIE DIESER KNOTEN SEINEN NACHBARN MELDET.
 *
 * Vier Stellen brauchten sie, und alle vier nannten CLUSTER_ONE_VERSION roh.
 * Mammuts Lader definiert die Konstante, ein Light nicht — ein Light allein
 * starb daran. cluster_one_fassung() (im Boden) beantwortet die Frage „welche
 * Fassung hat der Wirt DIESER Datei"; hier ist das aber die falsche Frage:
 *
 *     Ein Knoten hat gegenueber seinen Nachbarn EINE Nummer, nicht drei.
 *
 * Solange Mammut mitliegt, ist die Antwort eindeutig und unveraendert. Fehlt
 * er, meldet dieser Knoten die Nummer des Plugins, das Kleinrock liefert —
 * wahr ueber das, was laeuft, aber nicht vergleichbar mit der Nummer eines
 * Mammut-Knotens. version_compare('0.1.0', '1.0.7') sagt dann etwas, das
 * niemand gefragt hat.
 *
 * DAS IST EIN OFFENER POSTEN, KEINE LOESUNG. Er steht hier an EINER Stelle
 * statt viermal verteilt, damit die Entscheidung, wenn sie faellt, genau einen
 * Ort hat. Was ein Knoten ohne Mammut seinen Nachbarn sagen soll, ist eine
 * Entscheidung ueber das Mesh, keine Messung im Code.
 */
/* v1.3.7 (AUFTRAG-02 §2): DIE ZAHL REIST NICHT MEHR ALLEIN.
 *
 * Der offene Posten oben ist entschieden. Er lautete: ein Knoten ohne das
 * grosse Paket meldet 0.1.x, einer mit ihm 1.3.x, und die Gegenseite vergleicht
 * beide, als waeren sie dasselbe. Gemessen in fassung-probe.php: der Knoten,
 * der den Umbau vollzogen hat, wird auf JEDER Mesh-Seite als veraltet
 * gezeichnet — auch auf seiner eigenen — und kann die Warnung nicht wegraeumen.
 *
 *     Zwei Zahlen aus verschiedenen Reihen unter einem Feldnamen sind keine
 *     Fassung. Sie sind ein Vergleich, der zufaellig ausgeht.
 *
 * Die Zahl bleibt, wie sie ist. Dazu reist die REIHE, aus der sie stammt.
 * Verglichen wird nur innerhalb einer Reihe; sonst gar nicht.
 *
 * WARUM BEIDE AUS EINER FUNKTION KOMMEN: Fassung und Reihe entstehen an
 * derselben Verzweigung. Zwei Funktionen mit je einem eigenen if waeren zwei
 * Schreibwege auf eine Wahrheit — und die zweite wuerde beim naechsten Umbau
 * vergessen (P1-12).
 *
 * WARUM DIE REIHE NICHT DER ORDNERNAME IST: Ordnernamen sind Code (H99) und
 * aendern sich (mammut/ -> cluster-one/, Pacioli heisst connector-vault). Eine
 * Leitung, deren Vertragswert sich beim Umbenennen eines Verzeichnisses
 * aendert, traegt einen Betriebszustand nach aussen. Die Reihe sagt stattdessen,
 * WAS die Zahl zaehlt.
 *
 * WARUM 'sammlung' UND NICHT DER NAME DES GROSSEN PAKETS: kein Haus nennt ein
 * anderes beim Namen — und der Name waere ohnehin ein Fossil, sobald das grosse
 * Paket von den Knoten verschwindet. 'sammlung' bleibt wahr: die Zahl zaehlt
 * ein Paket, das mehrere Organe traegt.
 *
 * KEINE FASSUNG, KEINE REIHE. Ein leerer Wert ist keine Reihe 'unbekannt',
 * sondern gar keine Angabe — und wird drueben genauso behandelt wie ein Knoten,
 * der das Feld noch nicht kennt.
 *
 * @return array{0:string,1:string}  [Fassung, Reihe]
 */
function cluster_one_knoten_fassung_und_reihe(): array {
    if (defined('CLUSTER_ONE_VERSION')) {
        return array((string) CLUSTER_ONE_VERSION, 'sammlung');
    }
    $c1_f = function_exists('cluster_one_fassung') ? cluster_one_fassung(__FILE__) : '';
    return array($c1_f, $c1_f === '' ? '' : 'kleinrock');
}

function cluster_one_knoten_fassung(): string {
    return cluster_one_knoten_fassung_und_reihe()[0];
}

/** Aus welcher Zahlenreihe stammt die Fassung, die dieser Knoten meldet?
 *  Der einzige erlaubte Umgang damit ist der Vergleich auf Gleichheit —
 *  nie eine Ordnung, nie eine Anzeige. Was nie gezeigt wird, kostet beim
 *  Umbenennen nichts. */
function cluster_one_knoten_reihe(): string {
    return cluster_one_knoten_fassung_und_reihe()[1];
}

/* v0.2.8: HIER STAND cluster_one_hier('kleinrock') — innerhalb des Riegels.
   Sie steht jetzt DAVOR, oben bei der Bedingung. Wer sie hier wieder
   einsetzt, meldet nur noch die Sieger. */

/* ===========================================================================
 * DER 12. VERTRAG — cluster_one_foederation      (H126, Lieferung 03 · 05)
 *
 *     apply_filters('cluster_one_foederation', false)   ->  bool
 *
 * Ein anderes Haus haengt sich ein; dieses Haus fragt. Hier steht nur die
 * Frage. Der Name des einhaengenden Hauses kommt in dieser Datei nicht vor
 * und darf es nicht — kein Haus nennt ein anderes beim Namen.
 *
 * DREI EIGENSCHAFTEN, DIE MAN EINZELN VERLIEREN KANN:
 *
 * 1  DIE VORGABE STEHT BEIM RUFER. Hoert niemand zu, ist die Antwort `false`
 *    — nicht 'unbekannt'. Ein Knoten, der sich nicht bekennt, bekennt sich
 *    nicht. Die Abwesenheit des Hauses ist die Abwesenheit des Bekenntnisses.
 *
 * 2  GEZOGEN, NICHT GESCHICKT. Der Wert wird nirgends aufbewahrt. Wer eine
 *    Auskunft speichert, statt sie zu holen, hat eine zweite Wahrheit angelegt
 *    und die erste nicht abgeschafft (P1-12).
 *
 * 3  EINE FRAGE, EIN ORT. Diese Funktion ist die einzige Stelle, an der der
 *    Filter gerufen wird. Zwei Rufe waeren zwei Vorgaben.
 *
 * WAS HIER ABSICHTLICH FEHLT: der Wert verlaesst den Knoten NICHT. `/ping`
 * traegt ihn nicht. Ob und wie er ueber eine Kante reist, ist ein ZWEITER
 * Entwurf und noch nicht entschieden. Dass die Luecke offen bleibt, bewacht
 * zeuge-vertrag-12.php Teil 4 — eine benannte Grenze, die niemand bewacht,
 * ist ein Kommentar.
 *
 * WARUM SIE JETZT ERST HIER STEHT: Sie lag seit dem 28.08. in Lieferung 03
 * und ist nie in den Stammbaum gereist (H126). Post Dollys Haelfte steht seit
 * dem 29.08.; bis hierher wurde dort ein Haekchen gespeichert, das niemand
 * gelesen hat.
 * =========================================================================== */

/** Bekennt sich dieser Knoten zur Foederation?  Ohne Anbieter: nein. */
function cluster_one_foederation_bekenntnis(): bool {
    return (bool) apply_filters('cluster_one_foederation', false);
}

/** Die Anzeige dazu — oder gar nichts.
 *
 * WARUM BEI `false` NICHTS STEHT UND NICHT "nicht foederiert": `false` heisst
 * hier "sagt nichts dazu" und nicht "sagt nein". Eine Zeile "nicht foederiert"
 * waere eine Aussage ueber einen Knoten, gestuetzt auf ein Haus, das hier
 * fehlt — genau die Erfindung, die in dieser Datei schon repariert wurde.
 *
 * WARUM DAS TRENNZEICHEN IN DER ZEICHENKETTE STECKT: sonst bliebe bei `false`
 * ein Trenner ohne Wort stehen — eine Spur von etwas, das nicht da ist.
 */
function cluster_one_foederation_zeile(): string {
    if (!cluster_one_foederation_bekenntnis()) {
        return '';
    }
    return ' &nbsp;·&nbsp; <span style="opacity:.75;" title="'
         . esc_attr__('This node declares itself federated. The declaration is made by the organ that carries it; this page only asks.', 'cluster-one-kleinrock')
         . '">' . esc_html__('Federated', 'cluster-one-kleinrock') . '</span>';
}


/** Alle AKTIVEN Kanten – einzige Quelle: gepaarte Knoten (Status 'active'). */
function cluster_one_get_peers(): array {
    $out  = [];
    $seen = [];
    foreach (cluster_one_get_paired_map() as $e) {
        if (($e['status'] ?? 'active') !== 'active') {
            continue; // getrennte Kanten fallen heraus -> Token matcht nicht mehr
        }
        $url   = (string) ($e['url'] ?? '');
        $token = (string) ($e['token'] ?? '');
        if ($url === '' || $token === '' || isset($seen[$token])) {
            continue;
        }
        $seen[$token] = true;
        $out[] = [
            'name'  => (string) ($e['name'] ?? 'Peer'),
            'url'   => $url,
            'token' => $token,
            'edge'  => (string) ($e['edge'] ?? ''),
        ];
    }
    return $out;
}

/* --- v1.7.0: KLEINROCK ANTWORTET DEM BODEN --------------------------------
 *
 * Die Auskunftsschicht (Boden 1.4.0) kennt drei Antworten — fehlt / nein / ja
 * — und ohne einen Antworter bleibt jede Frage bei 'fehlt'. Diese Zeile ist
 * der Antworter fuer 'gepaart', und es ist der EINZIGE Ort im Haus, der sie
 * beantworten kann: nur Kleinrock weiss, ob dieser Knoten Kanten hat.
 *
 * Das ist der Unterschied, auf den es ankommt und den cluster_one_netz nicht
 * macht: Liegt Kleinrock nicht auf dem Knoten, meldet sich niemand an, und
 * die Antwort ist 'fehlt' — nicht 'nein'. Ein Produkt, das fragt, erfaehrt
 * dadurch, ob es eine verneinte Frage hat oder gar keine.
 *
 * ES REIST NICHTS. Die Antwort ist ein Bool ueber DIESEN Knoten. Weder ein
 * Nachbar noch eine Adresse verlaesst diese Funktion — die benannte Grenze
 * der Schicht ("nur ueber den Knoten, auf dem sie liegt") gilt hier zuerst.
 *
 * Erster Nutzer: faust.php (H143, der Archimedes-Abschnitt). Zweiter wird
 * voraussichtlich Post Dolly (H144), und das ist Christians Entscheidung,
 * nicht meine.
 *
 * AN DIE KLEINROCK-INSTANZ: die Zeile liegt in deiner Datei und ist meine.
 * Wenn du sie anders haben willst — anderer Ort, anderer Name, andere
 * Bedingung als "mindestens eine aktive Kante" — sag es, dann zieht sie um. */
add_filter('cluster_one_auskunft_gepaart', function () {
    return cluster_one_get_peers() !== array();
});

/* v0.2.9 — HIER STAND DER ANTWORTER AUF `cluster_one_produkt`. ER IST IM LADER.
 *
 * Und der Satz, an dem ich ihn haette messen koennen, stand die ganze Zeit in
 * meinem eigenen Lader, drei Zeilen ueber der Stelle, an der er jetzt steht:
 *
 *     „Die Anmeldung des Plugins selbst. Sie steht hier im Lader und darf
 *      das: zu ihr gehoert keine gleichnamige Organdatei, es gibt nichts zu
 *      verdraengen. Jede Meldung UEBER ein Organ gehoert dagegen in dessen
 *      eigene Datei (H70)."
 *
 * Ein Produkt ist keine Meldung ueber ein Organ. Die Unterscheidung war
 * geschrieben, und ich habe sie beim Bauen nicht gelesen.
 *
 *     Ein Organ meldet sich. Ein Produkt meldet sich an. Das erste steht in
 *     der Organdatei, das zweite im Lader.
 *
 * WARUM DAS NICHT NUR ORDNUNG IST — Post Dolly hat es gemessen: diese Datei
 * reist in fremde Pakete mit, und auf dem Monolithen tragen alle Organe
 * DENSELBEN Wirt. Fuenf Antworter in fuenf Organdateien schreiben dann unter
 * einen Schluessel, und wer zuletzt spricht, benennt das ganze Haus.
 * Organdateien reisen; der Lader nie. Damit ist der Fall nicht geriegelt,
 * sondern aufgeloest.
 *
 * Bewacht von zeuge-produkt-anmeldung.php: Pruefung A wird rot, sobald
 * `cluster_one_produkt` in dieser Datei wieder vorkommt.
 */

/* v0.9.54: HIER STAND DAS ADMIN-MENUE (add_menu_page + 22 Untermenues).
   Es ist nach includes/menue.php gezogen — byte-identisch, move don't
   rebuild. Grund: Kleinrock rief dadurch elf render_*_page-Funktionen
   aus Feynman, Kranz und Bismarck-Seiten, ohne diese Organe je zu
   brauchen. Ein Menue gehoert keinem Organ. Siehe menue.php. */

/** Mesh-Reiter (Netz · Freundesliste · KI-Vorschläge · Bannliste).
   $current = Slug der aktiven Seite. Profil UND Einstellungen sind HIER
   absichtlich nicht mehr gelistet — beide leben seit v0.0.39/0.0.40 unter
   Kranz. Freundesliste (v0.0.54): echt, funktioniert, rein lokal, genau
   wie die Bannliste — eine einseitige Markierung, keine Anfrage über den
   Knoten hinweg. KI-Vorschläge: bewusst leeres Reißbrett, wartet auf
   Label — seit 0.1.3 reist dafür der Zeitstempel label_ts, nicht mehr ein Boolean. */
/* v0.2.6 — DER KALENDER-REITER. Christian, 09.09.
 *
 * Der Kalender verliert seinen sichtbaren Hauptmenuepunkt und bekommt einen
 * Reiter in beiden Haeusern. Meiner hat zwei Gestalten:
 *
 *     allein     mein Reiter ist DIE TUER (nur Phase 2: was andere Knoten
 *                an Einladungen offen haben, per Pull)
 *     zu zweit   mein Reiter ist ein WEGWEISER — er sagt, WO die Funktion
 *                liegt, statt sie ein zweites Mal anzubieten
 *
 * AUSGEGRAUT AUSDRUECKLICH NICHT, und der Satz dazu steht in menue.php seit
 * 0.9.79: „Ein Ersatz fuer die Seite ist kein Ersatz fuer ihren Reiter."
 * Ein grauer Reiter schweigt; ein Wegweiser sagt etwas.
 *
 * DIE FRAGE LAUTET NICHT „IST DAS ANDERE HAUS DA".
 * Sie lautet: loest sich der Pfad zu dieser Funktion woanders auf? Das ist
 * dieselbe Kennung, aus der ein Link gebaut wird — Satz und Link koennen
 * damit nicht auseinanderlaufen.
 *
 *     Wer nach dem ORGAN fragt, muss ein fremdes Haus beim Namen nennen.
 *     Wer nach dem PFAD fragt, bekommt zugleich die Antwort, ob der Satz
 *     ueberhaupt irgendwohin zeigt.
 *
 * Dieselbe Form wie mein Vorschlags-Reiter seit 0.9.64: er fragt nach dem
 * MERKMAL, nicht nach dem Organ.
 *
 * ================= v1.0.9, WEG D (ENTSCHEIDUNG 20 E9) =================
 *
 * DIE QUELLE IST NICHT MEHR DER MENUEPFAD. Sie kann es nicht mehr sein:
 * Gregor ist eine versteckte Seite, und `cluster_one_menue_pfad()` gibt
 * dafuer seit VOLLZUG 15 §1 '' zurueck — richtig, denn "eine versteckte
 * Seite HAT keinen Pfad". Der Reiter steht in keinem Menue.
 *
 *     Wer heute die Antwort korrigiert, korrigiert sie fuer eine Frage,
 *     die es morgen nicht mehr gibt.                        (Mammut, §3)
 *
 * ALSO BEANTWORTET EINE FUNKTION DIE FRAGE, UND ZWEI LESEN SIE: der Reiter
 * in cluster_one_render_mesh_tabs() und der Satz hier. Das ist P1-12
 * richtigherum — zwei LESEwege auf eine Wahrheit sind erlaubt, zwei
 * SCHREIBwege nicht.
 *
 *     Haette ich die Bedingung ein zweites Mal hingeschrieben, haette ich
 *     an dieser Stelle gebrochen, was ich beim Raeumer gerade
 *     zusammengelegt habe.
 *
 * DIE FRAGE LAUTET NACH WIE VOR NICHT "IST DAS ANDERE HAUS DA", sondern
 * "gibt es einen Weg" — und ein Weg ist jetzt die Seite selbst, nicht ihr
 * Ort im Menue.
 *
 * @return string  die Adresse der Seite, oder '' wenn es sie nicht gibt.
 */
function cluster_one_kalender_weg(): string {
    if (!function_exists('cluster_one_render_gregor_page')) {
        return '';
    }
    return admin_url('admin.php?page=cluster_one_gregor');
}


/** DIE REITERLEISTE ALS LISTE (v1.0.10) — und sie ist ab heute auch die
 *  Antwort auf „welche Seiten gehoeren Kleinrock".
 *
 *  RUNDBRIEF 21 §1, Christians Befund: „Bei Kleinrock steht nur im Tab Mesh
 *  ‚Cluster One – Kleinrock'. Bei allen anderen Kleinrock-Tabs fehlt das."
 *  Der Grund war eine Annahme, die nie jemand hingeschrieben hat:
 *
 *      Ein Produkt ist keine Seite. Ein Produkt ist eine Reiterleiste.
 *
 *  §2 verlangt dafuer KEINE zweite Liste, und das ist der ganze Punkt dieser
 *  Funktion. Bis 1.0.9 baute `cluster_one_render_mesh_tabs()` die Leiste und
 *  zeichnete sie in einem Zug; die Liste war da, aber nicht zu fragen. Jetzt
 *  ist sie zu fragen, und ZWEI lesen sie: der Zeichner und die Anmeldung der
 *  Seitenansprueche.
 *
 *      Eine Wahrheit, zwei Leser.                              (Weg D)
 *
 *  DIE LISTE IST NICHT SECHS EINTRAEGE LANG. Sie ist 5 bis 7 lang, je nach
 *  Knoten — zwei Reiter haengen an einem Riegel:
 *      Vorschlaege   nur wo es Labels gibt   (cluster_one_thread_is_label)
 *      Kalender      nur wo es die Seite gibt (cluster_one_kalender_weg)
 *  Genau deshalb wird sie GEFRAGT und nicht getippt: eine getippte Sechs
 *  waere auf einem Knoten ohne Platon eine Behauptung ueber eine Seite, die
 *  dort niemand erreicht.
 *
 *      Wer seine Seiten aufzaehlt, statt sie zu fragen, zaehlt die Seiten
 *      des Knotens, auf dem er gebaut wurde.
 *
 *  @param  string $c1_aktuell  der Slug der Seite, die gerade gezeigt wird
 *  @return array<int,array{seite:string,label:string,url:string,active:bool}>
 */
function cluster_one_mesh_reiter(string $c1_aktuell = ''): array {
    /* E9: EINE Funktion beantwortet "gibt es einen Weg zum Kalender".
       v1.0.18: HIER IST JETZT DER EINZIGE LESER. Bis 1.0.17 stand hier „der
       andere ist cluster_one_kalender_wegweiser()" — der Wegweiser sass auf
       der Treffen-Seite, und mit der Seite ist er gefallen. Aus Weg D
       (eine Wahrheit, mehrere Leser) ist eine Wahrheit mit einem Leser
       geworden; das ist kein Mangel, aber es steht hier, statt dass der
       naechste Leser einen zweiten sucht.
           Ein Satz, der einen Leser nennt, den es nicht mehr gibt, ist
           dieselbe Sorte Satz, die ich bei anderen beanstande. */
    $c1_kalender = cluster_one_kalender_weg();

    $c1_roh = array(
        // v0.0.93: der wahre Name der Schicht
        array('seite' => 'cluster_one_mesh', 'label' => 'Mesh'),
        array('seite' => 'cluster_one_friends', 'label' => __('Friends', 'cluster-one-kleinrock')),
        /* v0.9.64: DER VORSCHLAGS-REITER IST DER LANDEPLATZ FUER FREMDE LABELS —
           er hat also nur dort einen Sinn, wo es Labels gibt. In einer
           Konfiguration ohne Platon zeigte er auf eine Seite, die zwar
           existiert, aber nie etwas zeigen kann: ein Reiter, der ein
           Versprechen macht, das das Haus nicht halten kann.
           Die Bedingung fragt nicht nach dem ORGAN, sondern nach dem
           MERKMAL — genau wie der DNA-Reiter seit 0.1.9. */
        function_exists('cluster_one_thread_is_label')
            ? array('seite' => 'cluster_one_ai_suggestions', 'label' => __('Suggestions', 'cluster-one-kleinrock'))
            : null,
        /* v0.9.40: Ein Punkt am Reiter, wenn eine Anti-Trotz-Rückfrage offen
           steht. Die Frage selbst wohnt auf der Bannliste; das hier ist nur
           ein Zeigefinger, damit sie nicht auf einer Seite verhungert, die
           man selten öffnet. Nur Anzeige, kein zweiter Bedienweg. */
        array('seite' => 'cluster_one_banned',
              'label' => __('Ban list', 'cluster-one-kleinrock') . (cluster_one_antitrotz_offen() ? ' ●' : '')),
        /* v1.0.18 (28.09.2026, Christian): DER REITER „TREFFEN" IST GEFALLEN,
           samt seiner Seite. Hier stand bis 1.0.17:
               array('seite' => 'cluster_one_meetings', 'label' => __('Meetings', …))
           Nicht stillschweigend weg (R-PAPIER-1): die Seite hat gezogen und
           nicht gespeichert, und was sie zeigte, gehoert dem Kalender.
           SECHS Reiter bleiben — und cluster_one_kleinrock_seiten() wird von
           selbst kuerzer, weil sie DIESE Liste fragt und keine zweite fuehrt.
               Wer seine Seiten aufzaehlt, statt sie zu fragen, zaehlt die
               Seiten des Knotens, auf dem er gebaut wurde. */
        /* v1.0.9: DER WEG ZUM KALENDER. Christians Entscheidung vom 18.09.:
           die Seite zieht NICHT um, sie bleibt `cluster_one_gregor` in
           faust.php und bekommt einen Zugang (VERTRAG 29 Sorte V).

           DER RIEGEL FRAGT NACH DER FUNKTION, NICHT NACH DEM ORGAN — woertlich
           die Bauart, mit der menue.php meinen eigenen Meetings-Punkt riegelt,
           und aus demselben Grund: waehrend eines Umstiegs kann faust.php da
           sein und die Funktion trotzdem fehlen. Er steht in
           cluster_one_kalender_weg() und nicht hier.

           v1.0.10: ER HEISST „KALENDER" UND NICHT MEHR „GREGOR"
           (RUNDBRIEF 21 §4). Ich hatte ihn nach der Ueberschrift der Zielseite
           benannt, mit dem Satz „ein Reiter ist ein Versprechen ueber die
           Ueberschrift, die danach kommt". Der Satz stimmt und war das
           falsche Werkzeug fuer diese Stelle — Mammuts Einwand trifft eine
           Ebene darueber:

               Ein Reiter sagt, WO man hinkommt. Eine Ueberschrift sagt,
               WAS dort ist.

           Zwei Gruende, die ich nicht hatte:
             · Liegen beide Haeuser, staende bei mir „Archimedes" und bei
               Bismarck „Gregor" — zwei Reiter, zwei Namen, EINE Seite. Wer
               beide sieht, sucht den zweiten Kalender.
             · „Gregor" ist UNSER Name. VERTRAG 28 §2: ein Reiter steht im
               Fenster von jemandem, der ihn nicht kennt.
           Die Ueberschrift der Seite zieht im selben Zug nach; sie ist
           Mammuts Datei. Mein Zeuge misst das Paar und nicht mein Wort —
           er liest die H1 aus faust.php, statt sie zu tippen. */
        /* 28.10.2026 (Neo), Boden 1.18.0: der Kalender-Reiter sagt dem Kalender,
           dass der Leser von MIR kommt (?von=), damit dort meine Leiste und
           „Cluster One – Kleinrock" stehen (Nemesus' Entscheidung vom 28.10.,
           cluster_one_herkunft). Geriegelt gegen einen aelteren Boden. */
        $c1_kalender !== ''
            ? array('seite' => 'cluster_one_gregor',
                    'label' => __('Calendar', 'cluster-one-kleinrock'),
                    'url'   => (function_exists('cluster_one_mit_herkunft') && function_exists('cluster_one_kasper_wirt'))
                        ? cluster_one_mit_herkunft($c1_kalender, cluster_one_kasper_wirt(__FILE__))
                        : $c1_kalender)
            : null,
        array('seite' => 'cluster_one_mesh_settings', 'label' => __('Settings', 'cluster-one-kleinrock')),
    );

    $c1_aus = array();
    foreach ($c1_roh as $c1_r) {
        if (!is_array($c1_r)) { continue; }
        $c1_aus[] = array(
            'seite'  => $c1_r['seite'],
            'label'  => $c1_r['label'],
            /* Die Adresse wird EINMAL gebaut — ausser beim Kalender, der sie
               von dem mitbringt, der auch ueber sein Dasein entscheidet. */
            'url'    => $c1_r['url'] ?? admin_url('admin.php?page=' . $c1_r['seite']),
            'active' => $c1_aktuell === $c1_r['seite'],
        );
    }
    return $c1_aus;
}

function cluster_one_render_mesh_tabs(string $current): void {
    $c1_tabs = array();
    foreach (cluster_one_mesh_reiter($current) as $c1_r) {
        $c1_tabs[] = array('label' => $c1_r['label'], 'url' => $c1_r['url'], 'active' => $c1_r['active']);
    }
    cluster_one_render_tabs($c1_tabs);
}

/** DIE SEITEN, DIE KLEINROCK BEANSPRUCHT (v1.0.11).
 *
 *  ABGELEITET, NICHT GETIPPT: die Leiste, plus die FOLGESEITE des einen Wegs,
 *  den sie oeffnet. Es bleibt eine Quelle — die zweite Zeile rechnet, sie
 *  behauptet nicht.
 *
 *  WARUM DIE FOLGESEITE DAZUGEHOERT, und es ist gemessen, nicht gemeint:
 *  faust.php hat ZWEI versteckte Seiten (BEFUND 18.09. §5, Fund 1). Meine
 *  Leiste oeffnet den Weg zur ersten. Auf einem Knoten, auf dem NUR Kleinrock
 *  liegt, beansprucht die zweite dann niemand — und
 *  cluster_one_faust_leisten() zeichnet dort KEINE Leiste. Gemessen:
 *
 *      cluster_one_gregor         1 Leiste   (meine, Reiter „Calendar" aktiv)
 *      cluster_one_gregor_event   0 Leisten
 *
 *  Das ist genau der Zustand, den §5 fuer die erste Seite gerade behoben hat —
 *  auf der zweiten, und nur in der Aufstellung, in der ich der einzige bin.
 *
 *      Wer einen Weg hinein oeffnet, schuldet den Weg hinaus fuer alles,
 *      was hinter der Tuer liegt — nicht nur fuer das erste Zimmer.
 *
 *  DER RIEGEL IST DERSELBE. Gibt es den Kalenderweg nicht, steht auch die
 *  Folgeseite nicht in der Liste: ich beanspruche keine Seite, die es auf
 *  diesem Knoten nicht gibt.
 *
 *  UND ES IST EINE ENTSCHEIDUNG, KEINE MESSUNG. Mammut hat Bismarck gebeten,
 *  `cluster_one_gregor_event` mitzumelden; mir hat er dazu nichts gesagt.
 *  Liegen beide Haeuser, ist die Seite dann „geteilt" — richtig. Soll sie
 *  allein Bismarck tragen, faellt hier EINE Zeile weg, und der Knoten ohne
 *  Bismarck hat die zweite Seite wieder ohne Rueckweg. Gemeldet, damit es
 *  eine Entscheidung ist und kein Nebenprodukt.
 *
 *  @return array<int,string>
 */
function cluster_one_kleinrock_seiten(): array {
    $c1_aus = array();
    foreach (cluster_one_mesh_reiter() as $c1_r) {
        $c1_aus[] = (string) $c1_r['seite'];
    }
    if (in_array('cluster_one_gregor', $c1_aus, true)) {
        $c1_aus[] = 'cluster_one_gregor_event';
    }
    return $c1_aus;
}

/* DIE SEITENANSPRUECHE (v1.0.10, RUNDBRIEF 21 §2) — aus DERSELBEN Quelle.
 *
 * Hier steht keine Liste. Hier steht eine Frage an die Liste, die es schon
 * gibt. Wer an dieser Stelle sechs Slugs tippt, hat zwei Wahrheiten ueber
 * seine eigenen Seiten — und die zweite veraltet an dem Tag, an dem ein
 * Reiter dazukommt.
 *
 * ANGEHAENGT, NICHT ZUGEWIESEN (R-SAMMLER-1). Der zweite Melder ist hier kein
 * Unfall, sondern der Regelfall: die Kalenderseite gehoert Kleinrock UND
 * Bismarck, und ein zuweisender Sammler haette eines der beiden Haeuser
 * stillschweigend enteignet.
 *
 * ANGEMELDET WIRD DER WIRT, erfragt statt getippt — genau wie beim Raeumer.
 *
 * UND SIE STEHT IN DER ORGANDATEI, nicht im Lader: der Lader meldet das
 * PRODUKT an, diese Zeilen melden die SEITEN eines Organs. Der Lader reist
 * nicht, die Organdatei schon.
 *
 * BENANNTE GRENZE, und ich schreibe sie hin statt sie zu umgehen:
 * cluster_one_mesh_reiter() ruft `__()`, `admin_url()` und zwei Riegel. Diese
 * Anmeldung braucht davon nur den Slug. Sie zahlt also fuer mehr, als sie
 * liest — und das ist mir lieber als eine zweite Liste, die nichts kostet und
 * irgendwann etwas anderes sagt.
 *     Eine Abkuerzung, die eine zweite Wahrheit anlegt, ist keine
 *     Ersparnis, sondern eine Schuld mit spaeterer Faelligkeit. */
add_filter('cluster_one_seite_ansprueche', function ($c1_a, $c1_seite) {
    if (!is_array($c1_a)) {
        return $c1_a;
    }
    $c1_wirt = function_exists('cluster_one_kasper_wirt') ? cluster_one_kasper_wirt(__FILE__) : '';
    if ($c1_wirt === '') {
        return $c1_a;
    }
    if (in_array($c1_seite, cluster_one_kleinrock_seiten(), true)) {
        $c1_a[] = $c1_wirt;
    }
    return $c1_a;
}, 10, 2);

/* DIE LEISTE AUF EINER FAUST-SEITE (v1.0.11, BEFUND vom 18.09. §5).
 *
 * EINE AKTION, KEIN FILTER — hier wird GEZEICHNET, nicht gesammelt. Der
 * Unterschied ist nicht Geschmack: ein Filter reicht einen Wert weiter und
 * kann ihn verlieren (R-SAMMLER-1). Eine Aktion hat nichts weiterzureichen,
 * also kann auch nichts verlorengehen — dafuer ist die REIHENFOLGE das, was
 * der Leser sieht, und die macht der Boden nach `nr`.
 *
 *     Ein Sammler kann seinen Nachbarn verlieren. Ein Zeichner kann ihn
 *     nur uebermalen.
 *
 * `cluster_one_faust_leisten()` fragt denselben Sammler, der die Ueberschrift
 * bestimmt, und ruft je beanspruchendem Wirt EINMAL. Vierter Leser derselben
 * Wahrheit: Zeichner · Ansprueche · Wegweisersatz · diese Zeile.
 *
 * DER RIEGEL PRUEFT DEN WIRT, weil die Aktion fuer JEDES Haus feuert. Ohne
 * ihn zeichnete ich meine Leiste auch dann, wenn Bismarck gemeint ist —
 * zweimal dieselbe Leiste, und keiner sieht, warum.
 *
 * ======================================================================
 * UND EINE ABWEICHUNG VON DER VORLAGE, die ich hinschreibe statt sie zu
 * verstecken: die Vorlage im BEFUND §5 uebergibt '' als aktuelle Seite.
 * Ich uebergebe $c1_seite.
 *
 *     Vorlage        cluster_one_render_mesh_tabs('')
 *     hier           cluster_one_render_mesh_tabs($c1_seite)
 *
 * Auf cluster_one_gregor steht diese Seite IN meiner Leiste (der Reiter
 * „Calendar"). Mit '' saehe der Leser sechs Reiter und keinen hervorgehoben,
 * waehrend er auf einem von ihnen steht.
 *     Eine Leiste, die den Ort nicht zeigt, an dem man steht, ist eine
 *     Liste von Wegen und keine Karte.
 * Auf cluster_one_gregor_event ist der Slug NICHT in meiner Leiste — dort
 * ist $c1_seite von '' nicht zu unterscheiden. Die Abweichung kostet also
 * nichts und bringt genau einen Fall. Gemessen: zeuge-reiter-gregor T14.
 * ====================================================================== */
add_action('cluster_one_leiste', function ($c1_wirt, $c1_seite) {
    $c1_meiner = function_exists('cluster_one_kasper_wirt') ? cluster_one_kasper_wirt(__FILE__) : '';
    if ($c1_meiner === '' || $c1_wirt !== $c1_meiner) {
        return;
    }
    cluster_one_render_mesh_tabs((string) $c1_seite);
}, 10, 2);


/* v0.9.60: DIE EINSTELLUNGSSEITE DES NETZES.
   Sie trägt vorerst genau einen Block: die Allianz. Das ist Absicht und kein
   halber Umzug — der Steckbrief ist ein OPTIONS-Formular, und
   cluster_one_sanitize_settings() baut sein Ergebnis von NULL auf. Ein
   zweites Formular, das nur die Steckbrief-Felder abschickt, würde jedes
   nicht mitgesendete Feld LÖSCHEN: Knotenname, Übersetzer, Bereiche,
   Kollektiv, beide Schalter. Auf laufenden Knoten.

   Die Allianz hat das Problem nicht — sie ist kein Options-Formular, sondern
   schickt an admin_post_cluster_one_pair. Deshalb geht sie heute, und der
   Steckbrief erst, wenn die Speicherfrage beantwortet ist. */

/* ============ DIE ZWEI KNOEPFE ZUM LOESEN (v1.0.18) ======================
   Christian am 28.09.2026: „Bitte beides zur Verfuegung stellen."

   WARUM SIE UNTER DER ALLIANZ STEHEN und nicht darueber: erst paaren, dann
   loesen. Wer den Loeseknopf oben findet, liest die Seite von hinten.
       Eine Oberflaeche erzaehlt eine Reihenfolge, ob sie will oder nicht.

   WARUM SIE AN admin_post GEHEN und kein Options-Formular sind: genau aus
   dem Grund, aus dem die Allianz es auch nicht ist —
   `cluster_one_sanitize_settings()` baut sein Ergebnis von NULL auf, und ein
   Formular, das den Steckbrief nicht mitschickt, wuerde ihn loeschen.
   Knotenname, Uebersetzer, Bereiche, Kollektiv, beide Schalter. Auf
   laufenden Knoten.
       Ein Formular, das speichert, was es nicht zeigt, loescht was es
       nicht kennt.

   DIE RUECKFRAGE BEI B IST EIN HAKEN UND KEIN confirm(). Ein
   Browserdialog ist nicht messbar und faellt ohne JavaScript aus; ein
   Pflichthaken steht im Formular, und der HANDLER prueft ihn.
       Eine Rueckfrage, die nur im Browser stattfindet, hat der Server
       nicht gestellt.

   KEIN REST-ENDPUNKT. Christian ausdruecklich: „Davon brauchen die anderen
   Knoten nichts zu registrieren." Die Gegenseite fuehrt die Kante weiter,
   bis sie verstummt — das ist die Entscheidung und keine Luecke.
   ========================================================================== */
function cluster_one_kleinrock_loesen_meldung(): void {
    $c1_was = isset($_GET['c1_loesen']) ? sanitize_text_field(wp_unslash((string) $_GET['c1_loesen'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    if ($c1_was !== 'a' && $c1_was !== 'b') {
        return;
    }
    /* JEDER KNOPF SAGT HINTERHER, WAS ER GENOMMEN HAT. Die Zahlen kommen aus
       der Funktion und reisen als eigene, ganzzahlige Argumente — ein
       fehlendes Argument heisst „nicht gemessen" und wird nicht als 0
       gezeigt. Dieselbe Regel wie in der Zahl selbst. */
    $c1_worte = array(
        /* translators: %d: Anzahl. */
        'kanten'       => __('connections released: %d', 'cluster-one-kleinrock'),
        /* translators: %d: Anzahl. */
        'handschlaege' => __('half-open handshakes dropped: %d', 'cluster-one-kleinrock'),
        /* translators: %d: Anzahl. */
        'chats'        => __('hello chats deleted: %d', 'cluster-one-kleinrock'),
        /* translators: %d: Anzahl. */
        'zeiger'       => __('addresses removed from the map: %d', 'cluster-one-kleinrock'),
        /* translators: %d: Anzahl. */
        'mails'        => __('operator addresses forgotten: %d', 'cluster-one-kleinrock'),
    );
    $c1_teile = array();
    foreach ($c1_worte as $c1_k => $c1_wort) {
        if (isset($_GET['c1_' . $c1_k])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $c1_teile[] = sprintf($c1_wort, max(0, (int) $_GET['c1_' . $c1_k])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        }
    }
    echo '<div class="notice notice-success is-dismissible"><p><strong>';
    echo esc_html($c1_was === 'a'
        ? __('All connections released.', 'cluster-one-kleinrock')
        : __('The mesh was reset.', 'cluster-one-kleinrock'));
    echo '</strong>';
    if ($c1_teile !== array()) {
        echo ' ' . esc_html(implode(' · ', $c1_teile));
    }
    echo '</p>';
    if ($c1_was === 'a') {
        echo '<p>' . esc_html__('The address map is untouched — pair with any of them again from the Alliance field above, no retyping needed.', 'cluster-one-kleinrock') . '</p>';
    }
    if (isset($_GET['c1_tuer'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        /* Die Tuer fehlte. Das ist keine Beschoenigung wert. */
        echo '<p><em>' . esc_html__('The storage layer was not loaded, so the hello chats could not be taken — and the connections were kept, so those chats remain findable. Nothing was lost; less was removed than the button says.', 'cluster-one-kleinrock') . '</em></p>';
    }
    echo '</div>';
}

/* Die zwei Knoepfe. Beide nur fuer den SysOp — wer entscheidet, mit wem der
   Knoten verbuendet ist, entscheidet auch, mit wem nicht mehr. Dieselbe
   Kostentraeger-Regel wie bei der Allianz (v0.9.34). */
function cluster_one_settings_seite_loesen(): void {
    if (!cluster_one_is_sysop()) {
        return;
    }
    ?>
        <hr style="margin:2.5em 0;">
        <h2><?php esc_html_e('Release — undo pairings', 'cluster-one-kleinrock'); ?></h2>

        <h3 style="margin-bottom:.2em;"><?php esc_html_e('Release all connections', 'cluster-one-kleinrock'); ?></h3>
        <p style="max-width:680px;margin-top:.2em;">
            <?php esc_html_e('Drops every pairing and every half-open handshake. The address map stays, and so do the hello chats — you can pair with any of these nodes again from the field above, without typing an address. Released means permanently offline to them; paired again means online.', 'cluster-one-kleinrock'); ?>
        </p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:680px;">
            <?php wp_nonce_field('cluster_one_kleinrock_loesen'); ?>
            <input type="hidden" name="action" value="cluster_one_kleinrock_loesen">
            <?php submit_button(__('Release all connections', 'cluster-one-kleinrock'), 'secondary', 'submit', false); ?>
        </form>

        <h3 style="margin-bottom:.2em;margin-top:2em;"><?php esc_html_e('Reset the mesh completely', 'cluster-one-kleinrock'); ?></h3>
        <p style="max-width:680px;margin-top:.2em;">
            <?php esc_html_e('Drops the pairings, the handshakes, the address map and the derived values, and deletes the hello chats of those connections. Afterwards an address has to be typed in again, and those chats are gone. This cannot be undone.', 'cluster-one-kleinrock'); ?>
        </p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:680px;">
            <?php wp_nonce_field('cluster_one_kleinrock_netz_reset'); ?>
            <input type="hidden" name="action" value="cluster_one_kleinrock_netz_reset">
            <p style="margin:.4em 0;">
                <label>
                    <input type="checkbox" name="c1_sicher" value="1" required>
                    <?php esc_html_e('Yes — delete the address map and the hello chats as well.', 'cluster-one-kleinrock'); ?>
                </label>
            </p>
            <?php submit_button(__('Reset the mesh', 'cluster-one-kleinrock'), 'delete', 'submit', false); ?>
        </form>
<?php
}

function cluster_one_render_mesh_settings_page(): void {
    cluster_one_page_gate();
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Kleinrock'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_mesh_tabs('cluster_one_mesh_settings'); ?>
        <?php
        /* 18.10.2026 (Neo): Der Leser fuer `pair=` — bisher stand er auf dem
           Kranz (kranz.php), waehrend das Formular hier steht. Wer paarte,
           landete auf einer anderen Seite als der, auf der er gedrueckt hat. */
        if (isset($_GET['pair'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $c1_s = sanitize_text_field(wp_unslash($_GET['pair'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            if ($c1_s === 'ok') {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Pairing successful — the edge is now active.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($c1_s === 'error') {
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Pairing failed. Check the URL (https, correct path) and that the other node is reachable and running this plugin.', 'cluster-one-kleinrock') . '</p></div>';
            }
        }
        ?>
        <?php cluster_one_kleinrock_loesen_meldung(); ?>
        <?php if (function_exists('cluster_one_settings_gespeichert_meldung')) { cluster_one_settings_gespeichert_meldung(); } ?>
        <?php cluster_one_settings_seite_allianz(); ?>
        <?php
        /* 19.10.2026 (Neo): Steckbrief und Uebersetzer standen bis heute auf
           der Boden-Seite „Einstellungen". Beides ist Sache des Netzes. */
        $c1_opts = get_option('cluster_one_settings', array());
        $c1_opts = is_array($c1_opts) ? $c1_opts : array();
        cluster_one_settings_seite_steckbrief($c1_opts);
        cluster_one_kleinrock_seite_uebersetzer($c1_opts);
        ?>
        <?php cluster_one_settings_seite_loesen(); ?>
    </div>
    <?php
}


/** Kleiner Ping-Knopf neben einem Menschen in der „In the room"-Leiste
   (System Teil 1, v0.0.42). Löst NUR die bezeugte Systemmeldung aus — kein
   Ton, keine Push-Benachrichtigung, das kommt erst mit Verdrahtung/
   Heartbeat. Ohne diesen Knopf wäre „User X wurde gepingt" nie auslösbar. */
function cluster_one_render_ping_button(string $room_id, int $target_user_id): void {
    if ($target_user_id <= 0 || $target_user_id === get_current_user_id()) {
        return; // kein Selbst-Ping
    }
    ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline;">
        <?php wp_nonce_field('cluster_one_room_ping_human'); ?>
        <input type="hidden" name="action" value="cluster_one_room_ping_human">
        <input type="hidden" name="room" value="<?php echo esc_attr($room_id); ?>">
        <input type="hidden" name="user_id" value="<?php echo esc_attr((string) $target_user_id); ?>">
        <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;opacity:.55;font-size:.8em;text-decoration:underline;" title="<?php echo esc_attr__('Ping — records that you asked for their attention, no sound (yet).', 'cluster-one-kleinrock'); ?>"><?php esc_html_e('ping', 'cluster-one-kleinrock'); ?></button>
    </form>
    <?php
}

/** Offene Pings des aktuellen Menschen löschen, sobald er den Raum betritt
   (die Zustellung hat ihr Ziel erreicht — kein Klebenbleiben). */
function cluster_one_pings_clear(string $room_id): void {
    $uid   = get_current_user_id();
    $pings = get_user_meta($uid, 'cluster_one_pings', true);
    if (is_array($pings) && isset($pings[$room_id])) {
        unset($pings[$room_id]);
        update_user_meta($uid, 'cluster_one_pings', $pings);
    }
}

add_action('rest_api_init', function () {
    register_rest_route('cluster-one/v1', '/ping', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function (WP_REST_Request $request) {

            $sent = (string) $request->get_header('X-Cluster-Token');
            if ($sent === '') {
                return new WP_REST_Response(null, 403);
            }

            // Aktive Kante? -> Auskunft. Getrennte Kante? -> ehrlich sagen.
            $active = false;
            foreach (cluster_one_get_peers() as $p) {
                if ($p['token'] !== '' && hash_equals($p['token'], $sent)) {
                    $active = true;
                    break;
                }
            }
            if (!$active) {
                $edge = cluster_one_find_edge_by_token($sent);
                if ($edge && ($edge['status'] ?? '') === 'banned') {
                    /* v0.9.40: DASS ER HIER ANKLOPFT, IST DER BEWEIS. Wer mich
                       gebannt hat, pingt mich nicht an — die Hauptseite geht nur
                       über aktive Kanten. Also sagt dieser Aufruf: sein Bann
                       gegen mich ist weg. Mehr wird nicht gefolgert und nichts
                       gesendet; die Antwort unten bleibt Wort für Wort dieselbe. */
                    cluster_one_edge_note_remote_open(
                        (string) ($edge['edge'] ?? '')
                    );
                    // Ehrlich gegenüber dem Betroffenen – Aussage über die Kante.
                    return new WP_REST_Response([
                        'cluster_one_status' => 'disconnected',
                        'message'            => __('This edge was banned by the operator of this node.', 'cluster-one-kleinrock'),
                    ], 403);
                }
                /* GABEL 0.9.69 (F14): DIE DRITTE AUSKUNFT.
                   Seit 0.9.68 ist eine von aussen angestossene Kante 'pending'.
                   Sie faellt hier bis jetzt in das nackte 403, und drueben
                   uebersetzt cluster_one_ping() das zu "Rejected (token?)" —
                   bei einer kerngesunden, gerade eben angelegten Kante. Der
                   andere SysOp prueft dann ein Token, das richtig ist.
                   Nach der Logik von 0.2.6 ("'getrennt' war eine Beschoenigung")
                   bekommt der Wartende eine eigene, wahre Antwort. Sie verraet
                   nichts: dass hier jemand auf eine Entscheidung wartet, weiss
                   der Anklopfende ohnehin — er hat ja angeklopft. */
                if ($edge && ($edge['status'] ?? '') === 'pending') {
                    return new WP_REST_Response([
                        'cluster_one_status' => 'pending',
                        'message'            => __('This node is waiting for its operator to decide whether to let you in.', 'cluster-one-kleinrock'),
                    ], 403);
                }
                return new WP_REST_Response(null, 403);
            }

            // Leichte Karte: Name, Version, URL (Anker) und die „Ob"-Bits.
            // v0.3.1: Der Steckbrief REIST seit 0.2.3 wieder mit — aber nur die
            // Felder, die der SysOp einzeln freigegeben hat, und nur an aktive
            // Kanten. (Der Kommentar behauptete bis hier das Gegenteil; er stammte
            // aus der Zeit vor 0.2.3 und war seither falsch.)
            //
            // Einem GEBANNTEN Knoten geht davon nichts zu: die Antwort oben trägt
            // ausschließlich den Bann-Status. Ehrlich über die Kante, stumm über
            // alles andere.
            return new WP_REST_Response([
                'node_id'     => cluster_one_node_id(),
                'version'     => cluster_one_knoten_fassung(),
                'reihe'       => cluster_one_knoten_reihe(), // v1.3.7 (AUFTRAG-02 §2): woher die Zahl stammt
                'url'         => cluster_one_self_url(),
                'label_ts'    => (int) get_option('cluster_one_label_signal_ts', 0), // v0.1.3: löst den Boolean 'forum' ab — er sagte nur HAT, nie NEU
                /* v0.3.19: Wie viele Treffen dieser Knoten gerade ausrichtet.
                   Im Ping, nicht als eigener Aufruf: Der Ping laeuft ohnehin fuer
                   jede Kante — ein Feld mehr kostet nichts, ein zweiter Aufruf
                   pro Kante haette die Netzlast verdoppelt.

                   v0.2.5 — DER VERTRAG DIESES FELDES AUF DER LEITUNG, und er
                   steht hier, weil hier gesendet wird:

                       Schluessel fehlt   der Knoten ist aelter als 0.3.19
                       null               das Feld gibt es, aber niemand
                                          konnte antworten — kein
                                          Kalender-Organ auf diesem Knoten
                       0                  gemessen: keine offenen Treffen
                       n                  gemessen: n offene Treffen

                   Weglassen war nicht frei: es heisst auf dieser Leitung
                   schon „alter Knoten". Ein Zustand, dessen Bedeutung
                   vergeben ist, ist kein Platz fuer einen zweiten. */
                'meetings'    => cluster_one_open_meeting_count(),
                /* v1.0.16 — WAS AN DIESEM KNOTEN ABLAEUFT, und es reist eine
                   Fassung VOR der Wirkung. Ein Knoten, der das Feld nicht
                   kennt, ueberliest es; einer, der es kennt, weiss vom
                   Stichtag, bevor er gegen ihn laeuft.

                       Kein Knoten soll von einer Grenze dadurch erfahren,
                       dass er gegen sie laeuft.

                   DER VERTRAG AUF DER LEITUNG, in derselben Form wie bei
                   `meetings` daneben — und aus demselben Grund: Weglassen
                   ist nicht frei.

                       Schluessel fehlt   der Knoten ist aelter als 1.0.16
                       leere Liste        gemessen: an diesem Knoten laeuft
                                          nichts ab
                       Eintraege          gemessen: diese Sachen laufen ab

                   EIN EINTRAG BLEIBT AUCH NACH SEINEM DATUM STEHEN. Er
                   beschreibt eine Aenderung, die der Leser noch machen muss
                   — ob das Datum schon vorbei ist, rechnet der Leser aus dem
                   Feld `ab` selbst aus. Ihn wegzunehmen hiesse, dem
                   Nachzuegler genau die Auskunft zu entziehen, fuer die er
                   gebaut wurde.
                       Eine Ankuendigung, die verschwindet, wenn sie
                       eintritt, hat den nicht erreicht, der zu spaet
                       hinsieht. */
                'deprecations' => cluster_one_kleinrock_deprecations(),
                /* v1.0.17 — BIN ICH EIN PRUEFSTAND? Neben `reihe`, weil es
                   dieselbe Sorte Auskunft ist: nicht WAS ich melde, sondern
                   WIE meine Zahl zu lesen ist.

                   DER VERTRAG AUF DER LEITUNG:
                       Schluessel fehlt   der Knoten ist aelter als 1.0.17
                                          -> KEIN Pruefstand, altes Verhalten
                       false              gemessen: kein Pruefstand
                       true               gemessen: Pruefstand

                   DIE ABWESENHEIT MUSS DAS ALTE VERHALTEN BEDEUTEN, und hier
                   ist das mehr als eine Konvention: die Angabe kann eine
                   Warnung nur WEGNEHMEN. Wuerde ein fehlendes Feld als
                   „Pruefstand" gelesen, koennte ein ALTER Knoten dem ganzen
                   Netz den Massstab nehmen — und zwar stumm.
                       Wer eine Abwesenheit fuer eine Zusage nimmt, laesst
                       die Vergangenheit ueber die Zukunft entscheiden. */
                'pruefstand' => cluster_one_kleinrock_pruefstand(),
            ] + cluster_one_self_profile(), 200); // v0.2.3: der Steckbrief — nur freigegebene Felder, nur an Kanten (dieser Endpunkt ist token-gesichert)
        },
    ]);
});

/* ====================== DER PRUEFSTAND, UND DER HOECHSTWERT ================
   v1.0.17 (AUFTRAG 24.09.2026, Schuld 3)

   DER FUND, und er ist nicht neu, sondern die zweite Haelfte eines alten:
   v1.3.7 hat den Hoechstwert auf die eigene REIHE eingegrenzt, weil ein
   Nachbar aus der anderen Reihe das ganze Netz rot faerbte. Eingegrenzt hat
   sie nicht, ob der Nachbar ein AUSGELIEFERTER Knoten ist oder eine
   Werkbank.

       R-URTEIL-2: Ein Urteil, das gegen den hoechsten Nachbarn faellt,
       faellt gegen den Pruefstand, sobald der Pruefstand ein Nachbar ist.

   WARUM EIN KNOTEN ES VON SICH SELBST SAGT (Mammuts Entscheidung, und der
   zweite Grund ist der tragende):
       1 Ein Pruefstand ist eine Tatsache, die jemand WEISS — kein Muster,
         das man erraten muss.
       2 Die Richtung ist sicher: die Angabe kann eine Warnung nur WEGNEHMEN,
         nie eine erzeugen. Wer luegt, macht niemanden rot — er macht
         hoechstens sich selbst als Massstab unsichtbar.
             Eine Angabe, die nur nachgeben kann, braucht kein Vertrauen.

   WARUM EINE KONSTANTE IN wp-config UND KEIN HAKEN AUF EINER SEITE:
   ein Pruefstand ist eine Eigenschaft der INSTALLATION, nicht einer Sitzung.
   Das Haus behandelt eine Konstante dort schon als ausdruecklichen Akt —
   `cluster_one_shares()` sagt es woertlich: „Wer ein Feld dort haendisch
   setzt, hat die Entscheidung schon getroffen." Dieselbe Sorte Entscheidung,
   dieselbe Sorte Ort.
       Und sie ist STELLBAR, ohne dass ein Pruefhaken im Auslieferungscode
       steht: ein Zeuge setzt die Konstante in seiner eigenen Welt, je Welt
       ein Prozess. Dasselbe Verfahren wie beim Stichtag.
           Ein Schalter, den nur die Pruefung umlegt, wird in der
           Auslieferung nie gemessen.

   EINE NAHT, DIE ICH MELDE UND NICHT ENTSCHEIDE: der NAME der Konstante ist
   knotenweit (`CLUSTER_ONE_PRUEFSTAND`), die Funktion darum ist meine. Ein
   Betreiber setzt eine Konstante fuer den KNOTEN, nicht eine je Modul —
   darum der neutrale Name. Wenn der Boden die Frage spaeter selbst
   beantworten will, ist das der Ort; meine Funktion faellt dann weg.

   ------------------------- DIE GRENZEN DIESER ANGABE ----------------------
   Sie stehen hier UND im Lieferpapier, weil ein Papier gelesen wird, wenn
   man es sucht, und ein Kommentar, wenn man an der Sache steht.

   1 DIE WIRKUNG SETZT EINE FASSUNG SPAETER EIN. Ein Knoten, der das Feld
     nicht kennt, faerbt weiter nach altem Muster. Erst wenn alle es kennen,
     wirkt es — genau wie beim Stichtag.

     UND DARIN LIEGT EINE ZEILE, DIE ICH GEMELDET HABE, BEVOR ICH GEBAUT
     HABE: der Zug, der diese Angabe bringt, ist von ihr selbst nicht
     geschuetzt. In der Stunde, in der er ZUERST auf dem Pruefstand liegt,
     kennen die anderen das Feld noch nicht — also faerben sie sich rot,
     genau ein letztes Mal. Geschuetzt ist erst der Zug DANACH.
         Eine Ankuendigung schuetzt den naechsten Schritt, nicht den,
         mit dem sie kommt.

   2 WENN SICH ALLE ZU PRUEFSTAENDEN ERKLAEREN, ist der Hoechstwert ueberall
     die eigene Fassung und niemand wird je rot. Das ist der entartete Fall.
     Er verlangt, dass jeder Betreiber die Konstante fuer seinen eigenen
     Knoten setzt — also ist er keine Schwaeche der Angabe, sondern ihres
     Gebrauchs. Er steht hier, statt abgewehrt zu werden.

   3 AUF DEM PRUEFSTAND SELBST WIRKT SIE NICHT. Der Hoechstwert beginnt mit
     der EIGENEN Fassung (`cluster_one_knoten_fassung()`), und dieser
     Anfangswert ist kein Nachbar — die neue Bedingung greift ihn nicht.
     Auf dem Schirm des Pruefstands stehen die anderen also weiter rot.
     GEMELDET UND NICHT ENTSCHIEDEN: dort ist die Warnung nicht dieselbe
     Sorte Fehler, denn wer auf dem Pruefstand sitzt, KANN aufspielen. Ob
     sie trotzdem weg soll, ist eine Entscheidung und keine Zeile.
         Eine Regel, die den Nachbarn ausnimmt, nimmt sich selbst nicht aus,
         solange sie beim Anfangswert nicht hinsieht.

   4 R-URTEIL-1 gilt weiter: die rote Zahl warnt nur innerhalb der eigenen
     Reihe. Das steht im Kommentar bei cluster_one_version_cell() und bleibt
     dort — nicht hier ein zweites Mal.
   ========================================================================== */
function cluster_one_kleinrock_pruefstand(): bool {
    return defined('CLUSTER_ONE_PRUEFSTAND') && (bool) CLUSTER_ONE_PRUEFSTAND;
}

/** Die hoechste Fassung im Netz — EINE Stelle, zwei Leser.
 *
 *  BIS 1.0.16 STAND DIESE RECHNUNG ZWEIMAL DA: auf der Mesh-Seite und auf der
 *  Freundes-Seite, und der Kommentar dort gab es sogar zu („wie auf der
 *  Mesh-Seite"). Zwei Schleifen, eine Rechnung.
 *
 *      R-RIEGEL-2: Ein Riegel, der neunmal einzeln gebaut ist, ist neunmal
 *      einzeln zu pruefen.
 *
 *  UND DIE DOPPELUNG HAT MEHR GEKOSTET ALS EINE ZWEITE PRUEFUNG: weil die
 *  Schleife INNERHALB einer Zeichenfunktion stand, konnte `fassung-probe.php`
 *  sie nicht herausschneiden und musste sie NACHBAUEN — mit einem Waechter
 *  daneben, der den Nachbau gegen die Quelle vergleicht. Jetzt ist sie eine
 *  Funktion mit einem Namen, und ein Zeuge fuehrt sie aus statt sie zu
 *  modellieren.
 *      Ein Stueck, das man nicht herausschneiden kann, wird nachgebaut —
 *      und ein Nachbau altert ohne Meldung.
 *
 *  DREI BEDINGUNGEN, und die dritte ist neu:
 *      der Ping muss geglueckt sein
 *      die REIHE muss meine sein                       (v1.3.7)
 *      der Knoten darf kein PRUEFSTAND sein            (v1.0.17)
 *
 *  `!empty()` UND NICHT `=== true`: ein fehlendes Feld, ein `null` und ein
 *  `false` bedeuten hier dasselbe, naemlich „kein Pruefstand", und damit das
 *  Verhalten von vorher. Das ist die sichere Richtung und sie ist nicht
 *  verhandelbar — die Abwesenheit der Angabe darf nie eine Warnung
 *  WEGNEHMEN, die es ohne sie gaebe.
 *
 *  @param array<int|string,array> $c1_pings  Ping-Antworten, wie
 *         cluster_one_ping() sie liefert.
 */
function cluster_one_kleinrock_netz_hoechste(array $c1_pings): string {
    $c1_max   = cluster_one_knoten_fassung();
    $c1_reihe = cluster_one_knoten_reihe();
    if ($c1_reihe === '') {
        /* Ohne eigene Reihe ist nichts vergleichbar — dann ist der eigene
           Wert der Hoechstwert, und niemand wird rot. Dasselbe Verhalten wie
           vor 1.0.17, nur an einer Stelle statt an zwei. */
        return $c1_max;
    }
    foreach ($c1_pings as $c1_p) {
        if (!is_array($c1_p) || empty($c1_p['ok'])) {
            continue;
        }
        if ((string) ($c1_p['reihe'] ?? '') !== $c1_reihe) {
            continue;
        }
        if (!empty($c1_p['pruefstand'])) {
            continue;   // v1.0.17: ein Pruefstand ist kein Massstab
        }
        $c1_v = (string) ($c1_p['version'] ?? '');
        if ($c1_v !== '' && $c1_v !== '?' && version_compare($c1_v, $c1_max, '>')) {
            $c1_max = $c1_v;
        }
    }
    return $c1_max;
}

function cluster_one_ping(string $url, string $token): array {
    if ($url === '' || $token === '') {
        return ['ok' => false, 'reason' => __('Incomplete', 'cluster-one-kleinrock')];
    }

    $endpoint = trailingslashit($url) . 'wp-json/cluster-one/v1/ping';

    $resp = wp_safe_remote_get($endpoint, [ // v0.0.61 (P0-6)
        'timeout'   => 5,
        'headers'   => ['X-Cluster-Token' => $token],
        'sslverify' => true,
    ]);

    if (is_wp_error($resp)) return ['ok' => false, 'reason' => __('Unreachable', 'cluster-one-kleinrock')];

    $code = wp_remote_retrieve_response_code($resp);
    if ($code === 403) {
        // Ehrliche Trennung vom Gegenüber? Dann sprechend anzeigen statt "abgewiesen".
        $body = json_decode(wp_remote_retrieve_body($resp), true);
        if (is_array($body) && ($body['cluster_one_status'] ?? '') === 'disconnected') {
            // v0.2.6: "getrennt" war eine Beschoenigung. ALLE drei Stellen, die
            // 'disconnected' senden, pruefen auf status === 'banned' — das Signal
            // bedeutet ausschliesslich einen Bann. Technische Unerreichbarkeit hat
            // ihr eigenes, neutrales 'offline'. Wer gebannt wurde, soll es wissen.
            return ['ok' => false, 'reason' => __('Banned by the operator of that node', 'cluster-one-kleinrock'), 'remote_disconnect' => true];
        }
        /* GABEL 0.9.69 (F14): Der Wartende ist kein Abgewiesener. */
        if (is_array($body) && ($body['cluster_one_status'] ?? '') === 'pending') {
            return ['ok' => false, 'reason' => __('Waiting to be let in over there', 'cluster-one-kleinrock'), 'remote_pending' => true];
        }
        return ['ok' => false, 'reason' => __('Rejected (token?)', 'cluster-one-kleinrock')];
    }
    /* translators: %d: HTTP-Statuscode. */
    if ($code !== 200) return ['ok' => false, 'reason' => sprintf(__('Unexpected (%d)', 'cluster-one-kleinrock'), (int) $code)];

    $data = json_decode(wp_remote_retrieve_body($resp), true);
    if (!is_array($data) || empty($data['node_id'])) {
        return ['ok' => false, 'reason' => __('Unreadable response', 'cluster-one-kleinrock')];
    }

    return [
        'ok'          => true,
        'node_id'     => (string) $data['node_id'],
        'version'     => (string) ($data['version'] ?? '?'),
        /* v1.3.7 (AUFTRAG-02 §2): Ein alter Knoten sendet das Feld nicht. Das ist
           kein Fehler, sondern ein alter Knoten — leer heisst "Reihe unbekannt",
           und unbekannt heisst: nicht vergleichen. */
        'reihe'       => isset($data['reihe']) ? sanitize_text_field((string) $data['reihe']) : '',
        'nickname'    => isset($data['nickname'])    ? (string) $data['nickname']    : '',
        'description' => isset($data['description']) ? (string) $data['description'] : '',
        'url'         => isset($data['url'])         ? (string) $data['url']         : '',
        'city'        => isset($data['city'])        ? (string) $data['city']        : '',
        'country'     => isset($data['country'])     ? (string) $data['country']     : '',
        'languages'   => isset($data['languages']) && is_array($data['languages'])
            ? cluster_one_clean_langs($data['languages']) : [],
        'label_ts'    => array_key_exists('label_ts', $data) ? (int) $data['label_ts'] : null, // v0.1.3: null = Kante spricht die alte Version
        /* v0.2.5 — DIESELBE REGEL WIE DIE ZWEI ZEILEN DARUEBER, sechs
           Fassungen zu spaet. `reihe` (v1.3.7) und `label_ts` (v0.1.3)
           unterscheiden „nicht gesendet" von einem Wert; `meetings` (v0.3.19)
           schrieb `?? 0` und machte aus drei Bedeutungen eine.

               Eine Regel, die zweimal danebensteht und beim dritten Mal
               nicht angewandt wurde, ist keine neue Regel, sondern eine
               ausgelassene.

           Was auf dieser Leitung ankommt, und was es heisst:
               Schluessel fehlt  der Knoten ist aelter als 0.3.19
               null              das Feld gibt es, niemand konnte antworten
               0                 gemessen: keine offenen Treffen
               n                 gemessen: n

           `??` taugt hier nicht: es fragt nach dem WERT und haelt einen
           gesendeten `null` fuer ein Fehlen. array_key_exists() fragt nach
           dem SCHLUESSEL. (Der Zeuge zu dieser Zeile ist mir genau darueber
           zuerst selbst umgefallen.) */
        /* v1.0.16: dreiwertig wie `meetings` — null heisst „der Knoten ist
           aelter als 1.0.16", array() heisst „gemessen: nichts laeuft ab".
           Ein leeres Feld und ein fehlendes sind hier zwei Auskuenfte. */
        'deprecations' => array_key_exists('deprecations', $data) && is_array($data['deprecations'])
            ? array_values(array_filter((array) $data['deprecations'], 'is_array'))
            : null,
        'meetings'    => array_key_exists('meetings', $data) && $data['meetings'] !== null
            ? max(0, (int) $data['meetings']) : null, // v0.3.19, dreiwertig seit 0.2.5
        /* v1.0.17: dreiwertig, und die drei Werte sind NICHT gleich viel wert.
               null   der Knoten ist aelter als 1.0.17 — und wird behandelt
                      wie `false`, weil die Abwesenheit das alte Verhalten
                      bedeuten MUSS
               false  gemessen: kein Pruefstand
               true   gemessen: Pruefstand
           `array_key_exists` und nicht `??`, aus demselben Grund wie zwei
           Zeilen darueber: `??` haelt ein gesendetes `null` fuer ein Fehlen.
           Hier fielen beide ohnehin gleich aus — der Unterschied steht
           trotzdem in der Antwort, damit ein Schirm ihn zeigen KANN. */
        'pruefstand' => array_key_exists('pruefstand', $data)
            ? (bool) $data['pruefstand'] : null,
    ];
}

/* v1.3.7 (AUFTRAG-02 §2): Der dritte Parameter hat eine Vorgabe, damit ein
   Aufrufer, der die Reihe nicht kennt, sich verhaelt wie bisher. Wer
   vergleichen WILL, sagt es; wer schweigt, bekommt das alte Verhalten.

   ================== DIE GRENZE DIESES URTEILS, HINGESCHRIEBEN ==============
   v1.0.16 (AUFTRAG 23.09., Punkt 5). Bis hierher stand sie nur im Code — im
   `!$vergleichbar`-Zweig — und damit nur dort, wo ohnehin schon jemand
   nachgesehen hat.

       R-URTEIL-1: Wer ein Urteil baut, baut die Grenze des Urteils mit —
       und muss sie hinschreiben, sonst haelt jemand sie fuer das Urteil.

   DIE ROTE ZAHL WARNT NUR INNERHALB DER EIGENEN REIHE. Ein Knoten, der die
   andere Reihe traegt — der Monolith mit seinen 1.9.x — wird NIE rot. Er
   bekommt ein Fragezeichen, und das ist kein milderes Urteil, sondern gar
   keins: seine Zahl ist wahr und mit meiner nicht vergleichbar.

       Ein Fragezeichen ist die ehrlichste Ausgabe, die ein Vergleich hat,
       wenn es nichts zu vergleichen gibt. Es sieht aus wie ein Mangel und
       ist eine Auskunft.

   GEMESSEN, NICHT VORHERGESAGT (Christian, 23.09., auf BEIDEN Seiten):
   Andreas 0.2.4 rot bei sich und auf der DEV; Main 1.9.9 und Stephan 1.6.0
   grau mit `?` auf beiden. Zwei unabhaengige Rechnungen, dasselbe Bild —
   die Reihen-Grenze aus v1.3.7 haelt zweiundzwanzig Fassungen abwaerts.

   WAS DIESE GRENZE NICHT EINSCHLIESST, und es ist Posten 16 und heute
   nicht faellig: `$max` ist der HOECHSTE NACHBAR, nicht die hoechste
   AUSLIEFERUNG. Der Pruefstand ist ein Nachbar, und dort liegt eine Fassung
   zuerst — genau dann, wenn es sie noch nicht zu holen gibt.

       R-URTEIL-2: Ein Urteil, das gegen den hoechsten Nachbarn faellt,
       faellt gegen den Pruefstand, sobald der Pruefstand ein Nachbar ist.

   Solange auf der DEV nur Ausgeliefertes liegt, kostet das niemanden etwas.
   Faellig wird es, wenn ZUG 11 zuerst dorthin geht — und dann mit einer
   Entscheidung und nicht mit einer Zeile.
   ========================================================================== */
function cluster_one_version_cell(string $version, string $max, bool $vergleichbar = true, bool $pruefstand = false): string {
    if ($version === '' || $version === '?') {
        return '<span style="color:#c3c4c7;">— v?</span>';
    }
    if (!$vergleichbar) {
        /* Die Zahl ist wahr, nur nicht vergleichbar. Sie zu verschlucken waere
           genauso falsch wie sie zu bewerten — also steht sie da, unbewertet,
           und der Titel sagt, warum kein Urteil daneben steht. */
        return '<span style="color:#50575e;font-weight:600;" title="'
             . esc_attr__('This number comes from a different series (or the node does not say which) — it cannot be compared with yours.', 'cluster-one-kleinrock')
             . '">v' . esc_html($version) . ' <span style="font-weight:400;">?</span></span>';
    }
    $color = version_compare($version, $max, '<') ? '#d63638' : '#50575e';
    $c1_zelle = '<span style="color:' . $color . ';font-weight:600;">v' . esc_html($version) . '</span>';
    /* v1.0.17 — STUECK 4 UND 5 IN EINER ZEILE, und die Reihenfolge ist der
       ganze Punkt: das URTEIL steht oben und wird NICHT ersetzt, die Marke
       kommt DANEBEN.

           Wer sich vom Messen ausnimmt, nimmt sich vom Gemessenwerden
           nicht aus.                                            (Mammut)

       Ein Pruefstand, der HINTERHER ist, wird also weiter rot — die Angabe
       nimmt ihm das Recht, Massstab zu SEIN, nicht die Pflicht, gemessen zu
       WERDEN. Haette ich die Marke statt der Farbe gesetzt, waere aus einer
       Ausnahme beim Rechnen eine Ausnahme beim Urteilen geworden, und das
       waere etwas anderes gewesen, als der Auftrag sagt.

       UND SIE STEHT DA, WO SICH SONST JEMAND WUNDERT: auf der Zeile, deren
       hohe Zahl kein Rot ausloest, steht warum.
           Eine Grenze ohne Sicht ist eine Grenze auf Verdacht. */
    if ($pruefstand) {
        $c1_zelle .= ' <span style="color:#8a8a8a;font-weight:400;" title="'
            . esc_attr__('This node reports itself as a test bench: its number does not set the yardstick for the others. It is still compared against them.', 'cluster-one-kleinrock')
            . '">⚙</span>';
    }
    return $c1_zelle;
}

function cluster_one_flag(bool $on): string {
    return $on
        ? '<span style="color:#1a7f37;font-weight:600;">●</span>'
        : '<span style="color:#8a8a8a;font-weight:600;">○</span>';
}

/** v0.1.3: Die Label-Spalte einer lebenden Kante. Drei Zustände statt eines
   Bools — die Entscheidung selbst steckt in cluster_one_label_dot(). */
/* v0.3.19: Eine Spalte, zwei Zeichen. Kleinrock beantwortet EINE Frage — gibt
   es bei diesem Knoten etwas Neues? — also darf es nicht zwei Spalten geben.
   Aber Label und Einladung duerfen sich auch keine Lampe teilen: Sonst weiss
   man erst nach dem Klick, was einen erwartet, und wenn beides neu ist, muesste
   sich die Lampe fuer ein Ziel entscheiden und das andere verschweigen.

   Also: ● fuer ein neues Label (fuehrt zu den Vorschlaegen), ◆ fuer eine offene
   Einladung (fuehrt zu Archimedes). Jedes fuer sich ein Link. */
function cluster_one_meeting_dot_cell(array $row, string $url): string {
    /* v0.2.5: `meetings` ist seit dieser Fassung dreiwertig, und `null` heisst
       „niemand konnte es sagen". (int) null ist 0, und 0 zeichnet keinen
       Marker — das ist hier das RICHTIGE Verhalten und deshalb bleibt die
       Zeile stehen: wer nicht weiss, ob drueben eine Einladung offen ist,
       soll keine anbieten. Der Grund steht hier, damit ihn niemand
       „repariert".

           Ein richtiges Verhalten ohne seinen Grund ueberlebt den
           naechsten Leser nicht. */
    $offen = (int) ($row['meetings'] ?? 0);
    if (empty($row['ok']) || $offen <= 0) {
        return '';
    }
    $schon = cluster_one_adopted_from($url);
    $ziel  = admin_url('admin.php?page=cluster_one_gregor&tab=archimedes');
    if ($schon >= $offen) {
        return ' <a href="' . esc_url($ziel) . '" style="text-decoration:none;color:#b26b00;" title="'
             . esc_attr__('A meeting is on offer here — you have already taken it on.', 'cluster-one-kleinrock') . '">◆</a>';
    }
    return ' <a href="' . esc_url($ziel) . '" style="text-decoration:none;color:#1a7f37;font-weight:600;" title="'
         /* translators: %d: Anzahl offener Einladungen. */
         . esc_attr(sprintf(_n('%d open invitation from this node — not taken on yet.', '%d open invitations from this node — not all taken on yet.', $offen - $schon, 'cluster-one-kleinrock'), $offen - $schon))
         . '">◆</a>';
}

/* v1.3.7 (AUFTRAG-02 §1): DER RUECKFALL DARF NICHTS BEHAUPTEN.
 *
 * Bis hier fiel der geriegelte Aufruf auf 'keine' zurueck, und 'keine' heisst
 * "dort ist nie etwas veroeffentlicht worden". Das ist nicht die Auslassung von
 * 'neu', das ist die GEGENTEILIGE Aussage — behauptet ueber einen fremden
 * Knoten, auf Grundlage davon, dass in DIESEM Haus ein Organ fehlt.
 *
 *     Ein Riegel verwandelt ein Fatal in eine stille falsche Antwort.
 *     Ein Rueckfallwert, der etwas behauptet, ist kein Rueckfall.
 *
 * ZWEI AENDERUNGEN, UND DIE ZWEITE STAND NICHT IM AUFTRAG:
 *
 * 1. Der Rueckfall heisst 'unbekannt' (vierter Vertragswert, entschieden von
 *    der Werkbank).
 *
 * 2. 'keine' bekommt einen EIGENEN Zweig, und default wird zur schwaechsten
 *    Aussage statt zur staerksten. Vorher fing default zweierlei: den Wert
 *    'keine' UND jeden Wert, den diese Zelle nicht kennt — und behauptete fuer
 *    beide "hier ist nichts veroeffentlicht". Derselbe Fehler eine Ebene
 *    tiefer: Wer einen unbekannten Wert in eine Behauptung faellt, hat den
 *    Riegel nur verschoben.
 *
 *        Der Rueckfall gehoert an die schwaechste Aussage, nie an eine
 *        Behauptung. Behaupten darf nur, wer ausdruecklich genannt ist.
 *
 * WARUM 'veraltet' UND 'unbekannt' DASSELBE ZEICHEN TRAGEN und sich nur im
 * Titel unterscheiden: Beide heissen fuer den Punkt dasselbe — keine Auskunft.
 * Ein zweites Zeichen muesste gelernt werden und truege keine zusaetzliche
 * Information. Was sich unterscheidet, ist die HANDLUNG, und die steht im
 * Titel: drueben aktualisieren, oder hier ein Organ nachlegen. */
function cluster_one_label_dot_cell(array $row, int $seen): string {
    switch (function_exists('cluster_one_label_dot') ? cluster_one_label_dot($row, $seen) : 'unbekannt') {
        case 'neu':
            return '<a href="' . esc_url(admin_url('admin.php?page=cluster_one_ai_suggestions')) . '" style="text-decoration:none;color:#1a7f37;font-weight:600;" title="'
                 . esc_attr__('New since you last pulled from this node — go to Suggestions.', 'cluster-one-kleinrock') . '">●</a>';
        case 'bekannt':
            // v0.2.9: eigene Farbe. Grau hiess vorher auch "hat welche, du kennst
            // sie schon" — und genau das ist fuer die Suche nach passenden Knoten
            // der Unterschied zwischen leer und lohnend.
            // v0.3.20: Das Ziel haengt an der FARBE. Gruen heisst "noch nicht
            // geholt" und fuehrt dorthin, wo man es holt. Amber heisst "schon da"
            // und fuehrt dorthin, wo es liegt. Eine Lampe, zwei Zustaende, zwei
            // richtige Ziele — die Frage dahinter ist jedesmal eine andere.
            return '<a href="' . esc_url(admin_url('admin.php?page=cluster_one_bismarck&tab=labels')) . '" style="text-decoration:none;color:#b26b00;font-weight:600;" title="'
                 . esc_attr__('Already pulled — go to where it is (Labels).', 'cluster-one-kleinrock') . '">●</a>';
        case 'veraltet':
            return '<span style="color:#8a8a8a;" title="' . esc_attr__('This node speaks an older version and does not report label changes yet.', 'cluster-one-kleinrock') . '">?</span>';
        case 'keine':
            return '<span style="color:#c3c4c7;" title="' . esc_attr__('No labels published here.', 'cluster-one-kleinrock') . '">○</span>';
        default:
            /* 'unbekannt' — und alles, was diese Zelle nicht kennt. Sie sagt,
               dass sie es nicht weiss, statt das Gegenteil zu behaupten. */
            return '<span style="color:#8a8a8a;" title="' . esc_attr__('This node cannot answer: the organ that reads labels is not installed here.', 'cluster-one-kleinrock') . '">?</span>';
    }
}

/** v0.2.3: Der Steckbrief einer Kante in einer Zeile. Zeigt nur, was der
   andere Knoten freiwillig gesendet hat — leer heisst nicht "unbekannt",
   sondern "nicht freigegeben", und das ist ein Unterschied. */
function cluster_one_peer_steckbrief_zeile(array $row): string {
    if (empty($row['ok'])) {
        return '';
    }
    $teile = array();
    $ort = trim(implode(', ', array_filter(array(
        trim((string) ($row['city'] ?? '')),
        trim((string) ($row['country'] ?? '')),
    ))));
    if ($ort !== '') { $teile[] = $ort; }
    $langs = (array) ($row['languages'] ?? array());
    if ($langs) { $teile[] = implode('/', array_map('strtoupper', array_slice($langs, 0, 4))); }
    $desc = trim((string) ($row['description'] ?? ''));
    if ($desc !== '') {
        // Kein mb_strimwidth: mbstring ist auf einem WP-Host nicht garantiert,
        // und ein Plugin darf daran nicht scheitern. Der Zeuge hat genau das
        // gefunden, bevor es ausgeliefert wurde.
        $teile[] = (strlen($desc) > 60) ? rtrim(substr($desc, 0, 57)) . '…' : $desc;
    }
    return implode(' · ', $teile);
}

function cluster_one_flag_unknown(): string {
    return '<span style="color:#c3c4c7;">—</span>';
}

/** URL-Zelle: nur die URL (fälschungssicherer Anker). Der Name lebt jetzt in
   einer eigenen Spalte (cluster_one_name_cell). Signatur bleibt rückwärts-
   kompatibel; überzählige Parameter (Name, Profil) werden ignoriert. */
function cluster_one_identity_cell(
    string $node_id, string $nickname = '', string $description = '', string $url = '',
    string $city = '', string $country = '', array $languages = []
): string {
    if ($url === '') {
        // Kein URL bekannt: zeige als Notnagel den Namen, damit die Zeile nicht leer ist.
        return $node_id !== '' ? '<span style="opacity:.6;">' . esc_html($node_id) . '</span>' : '—';
    }
    $disp = preg_replace('#^https?://#', '', $url);
    // v0.2.7: Das Leerzeichen vor dem Pfeil war eine Sollbruchstelle — bei langen
    // URLs rutschte das ↗ in die zweite Zeile und die Tabelle wurde doppelt hoch.
    // Geschuetztes Leerzeichen plus nowrap: die Zelle bleibt einzeilig.
    return '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" style="white-space:nowrap;">'
         . '<strong>' . esc_html($disp) . '</strong>&nbsp;↗</a>';
}

/** Name-Zelle: schlichter Text. Verlinkt später aufs Profil (Schicht 4). */
function cluster_one_name_cell(string $name): string {
    // v0.2.9: einzeilig — "Nemesus Main" brach um, "Nemesus Dev" nicht.
    return $name !== ''
        ? '<span style="opacity:.85;white-space:nowrap;">' . esc_html($name) . '</span>'
        : '<span style="opacity:.4;">—</span>';
}

function cluster_one_render_main_page() {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Kleinrock'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
<?php /* v0.9.68 (F6): Wer anklopft, steht GANZ OBEN — noch vor den Reitern.
   Eine Entscheidung, die jemand treffen muss, gehoert nicht unter eine
   Tabelle, durch die man erst scrollt. Zeigt sich nur, wenn jemand wartet. */ ?>
        <?php cluster_one_render_anklopfen(); ?>
<?php /* 18.10.2026 (Neo): Leser fuer `anklopfen=` und `friend=on` — beide
   wurden seit Monaten geschrieben und von niemandem gelesen; der Mensch
   drueckte und sah eine unveraenderte Seite. */
        if (isset($_GET['anklopfen'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $c1_a = sanitize_text_field(wp_unslash($_GET['anklopfen'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            if ($c1_a === 'ja') {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Let in — the edge is active now.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($c1_a === 'nein') {
                echo '<div class="notice notice-info is-dismissible"><p>' . esc_html__('Turned away — the handshake was dropped. The node may knock again.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($c1_a === 'weg') {
                echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('That handshake is no longer there — it was already decided or has expired.', 'cluster-one-kleinrock') . '</p></div>';
            }
        }
        if (isset($_GET['friend']) && $_GET['friend'] === 'on') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Marked as a friend.', 'cluster-one-kleinrock') . '</p></div>';
        }
?>
<?php /* v0.7.4: Die Dunbar-Meldung. Sie sagt, was zu tun ist — eine Sperre
   ohne Ausweg waere eine Sackgasse. */ ?>
        <?php if (isset($_GET['friend']) && $_GET['friend'] === 'full') : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
            <div class="notice notice-warning is-dismissible"><p><?php
                printf(
                    /* translators: %d: the Dunbar number */
                    esc_html__('Your friends list is full at %d nodes. To add another, unfriend one first — the heart next to a node toggles it.', 'cluster-one-kleinrock'),
                    (int) CLUSTER_ONE_DUNBAR
                );
            ?></p></div>
        <?php endif; ?>
        <?php cluster_one_render_mesh_tabs('cluster_one_mesh'); ?>
        <?php cluster_one_render_reset_notice(); // v0.1.5: die Narbe sehen ALLE mit Zugang, nicht nur der SysOp ?>
<?php /* v0.9.41: HIER STAND DER WEG ZUR CHRONIK — ein Verweis mit erklaerendem
   Nebensatz, mitten auf der meistbenutzten Seite des Plugins. Er ist nach
   Kranz gezogen, als eigener Reiter hinter System.

   Christians Begruendung: Die Chronik hat auf der Mesh-Seite nichts verloren.
   Meine Nachzaehlung dazu: Die Chronik hat 58 Einspeisungsstellen im Plugin,
   davon liegen genau ZWEI in kleinrock.php. Bismarck 19, Kern 14, Platon 11,
   Gregor 7. In der Typenliste steht kein einziges Netz-Ereignis — kein
   Paaren, kein Bannen, kein Entbannen. Sie stand also im Organ, das drei
   Prozent von ihr schreibt, und zwischen vier Reitern, die alle von ANDEREN
   Knoten handeln, waehrend sie von DIESEM handelt.

   Ein Reiter braucht keinen erklaerenden Nebensatz — deshalb ist der
   mitgegangen. Und die Sidebar bleibt bei sechs Zeilen: Kranz-Reiter sind
   versteckt registriert, genau wie Profil, System und Modi. */ ?>
        <?php
        if (isset($_GET['ban'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $s = sanitize_text_field(wp_unslash($_GET['ban'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            if ($s === 'on') {
                echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('Node banned (quarantine). It is moved to the Banned tab, blocked at the route, and cannot re-pair. Reversible there.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($s === 'off') {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Node unbanned. It can be talked to again.', 'cluster-one-kleinrock') . '</p></div>';
            }
        }
        if (isset($_GET['radar'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $s = sanitize_text_field(wp_unslash($_GET['radar'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            if ($s === 'ok') {
                $found = isset($_GET['found']) ? max(0, (int) $_GET['found']) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
                $txt = $found > 0
                    /* translators: %d: Anzahl neu entdeckter Knoten. */
                    ? sprintf(_n('Map refreshed — %d new node discovered.', 'Map refreshed — %d new nodes discovered.', $found, 'cluster-one-kleinrock'), $found)
                    : __('Map refreshed — no new nodes.', 'cluster-one-kleinrock');
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($txt) . '</p></div>';
            } elseif ($s === 'wait') {
                echo '<div class="notice notice-info is-dismissible"><p>' . esc_html__('Just refreshed a moment ago — please wait a little before refreshing again.', 'cluster-one-kleinrock') . '</p></div>';
            }
        }
        if (isset($_GET['connect']) && sanitize_text_field(wp_unslash($_GET['connect'])) === 'fail') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Could not reach that node to start talking. It may be offline — try again later.', 'cluster-one-kleinrock') . '</p></div>';
        }
        if (isset($_GET['test'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $s = sanitize_text_field(wp_unslash($_GET['test'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            if ($s === 'ok') {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Node tested — domain proof succeeded. It is now approved; you can say hello.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($s === 'fail') {
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Node did not answer the test. It may be offline — the Test button stays, try again later.', 'cluster-one-kleinrock') . '</p></div>';
            }
        }
        if (isset($_GET['mute'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $s = sanitize_text_field(wp_unslash($_GET['mute'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            if ($s === 'on') {
                echo '<div class="notice notice-info is-dismissible"><p>' . esc_html__('Muted. Incoming contact is turned away at the gate until the timer runs out. It lifts itself.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($s === 'off') {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Mute lifted.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($s === 'rest') {
                $c1_n = isset($_GET['n']) ? (int) $_GET['n'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
                echo '<div class="notice notice-success is-dismissible"><p>'
                   . esc_html(sprintf(
                       /* translators: %d = number of nodes */
                       _n('%d node was muted on its own and can be heard again.',
                          '%d nodes were muted on their own and can be heard again.',
                          $c1_n, 'cluster-one-kleinrock'), $c1_n))
                   . '</p></div>';
            }
        }

        /* v1.5.1 (H135): DIE FRAGE. Sie erscheint nur, wenn die Decke gerade
           weggezogen wurde UND darunter noch etwas liegt — und sie nennt die
           Betroffenen beim Namen, damit hier niemand blind ja sagen muss.
           Bei mehr als vier: die ersten vier, dann eine Zahl. */
        if (isset($_GET['rest']) && cluster_one_is_sysop()) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $c1_rest = cluster_one_einzeln_stumme_kanten();
            if (!empty($c1_rest)) {
                $c1_namen = array();
                foreach (array_slice($c1_rest, 0, 4) as $c1_e) {
                    $c1_namen[] = (string) ($c1_e['name'] ?? ($c1_e['url'] ?? $c1_e['edge']));
                }
                $c1_liste = implode(', ', $c1_namen);
                if (count($c1_rest) > 4) {
                    $c1_liste .= ' ' . sprintf(
                        /* translators: %d = number of further nodes */
                        _n('and %d more', 'and %d more', count($c1_rest) - 4, 'cluster-one-kleinrock'),
                        count($c1_rest) - 4
                    );
                }
                ?>
                <div class="notice notice-info" style="max-width:680px;">
                    <p style="margin-bottom:.6em;"><?php
                        echo esc_html(sprintf(
                            /* translators: %s = list of node names */
                            __('These nodes are still muted on their own, from before: %s. Lifting “Do not disturb (all)” did not touch them. Should they be heard again too?', 'cluster-one-kleinrock'),
                            $c1_liste
                        ));
                    ?></p>
                    <p>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;">
                            <?php wp_nonce_field('cluster_one_unmute_rest'); ?>
                            <input type="hidden" name="action" value="cluster_one_unmute_rest">
                            <button type="submit" class="button button-primary"><?php esc_html_e('Yes — unmute those too', 'cluster-one-kleinrock'); ?></button>
                        </form>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_mesh')); ?>"
                           class="button"><?php esc_html_e('No — leave them muted', 'cluster-one-kleinrock'); ?></a>
                    </p>
                </div>
                <?php
            }
        }
        ?>
        <p style="font-size:1.1em;margin-top:1.5em;">
            <?php /* 0.2.55 (27.10.2026, Neo): „of Cluster One" – Nemesus: das Datum ist der
               Spatenstich der ganzen Suite, nicht dieses einen Plugins; ohne den Zusatz las
               es sich, als ginge es nur um Kleinrock. Bis 0.2.54: __('Groundbreaking:'). */ ?>
            <?php esc_html_e('Groundbreaking of Cluster One:', 'cluster-one-kleinrock'); ?> <strong>27.05.2026</strong>
            &nbsp;·&nbsp; <code style="opacity:.6;">v<?php echo esc_html((function_exists('cluster_one_fassung') ? cluster_one_fassung() : (defined('CLUSTER_ONE_VERSION') ? (string) CLUSTER_ONE_VERSION : ''))); ?></code>
            <?php
            /* 12. Vertrag: hier wird gezogen, nicht nachgeschlagen. Die Zeile
               ist leer, wenn niemand sich bekennt — samt ihrem Trennzeichen.
               Kein esc_html() darum: die Funktion escapt, was von aussen kommt,
               und liefert bewusst Auszeichnung. */
            echo cluster_one_foederation_zeile();   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            /* 0.2.55: der Stempel – Werft und Boden-Stand aus dem eigenen Register
               (Boden 1.17.6, cluster_one_stempel_teile). Leer, wenn der Boden es
               nicht kennt; dann auch kein Trennzeichen. */
            $c1_st = function_exists('cluster_one_stempel_teile') ? cluster_one_stempel_teile(__FILE__) : '';
            if ($c1_st !== '') { echo ' &nbsp;·&nbsp; <span style="opacity:.6;">' . $c1_st . '</span>'; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapt in der Funktion
            ?>
        </p>

        <?php
        // --- Base (this node) ---
        $self_id      = cluster_one_node_id();
        $self_url     = cluster_one_self_url();

        // --- Approved peers: active paired edges (banned live in the Banned tab) ---
        $results = [];
        foreach (cluster_one_get_paired_map() as $e) {
            if (($e['status'] ?? 'active') !== 'active') {
                continue;
            }
            $c1_ping = cluster_one_ping((string) ($e['url'] ?? ''), (string) ($e['token'] ?? ''));
            /* v0.9.40: Die einzige Stelle, an der ein Ping-Ergebnis entsteht —
               also die einzige, an der vermerkt wird, ob die Gegenseite MICH
               gebannt hat. Ein zweiter Schreibweg darauf wäre P1-12. */
            cluster_one_edge_note_remote_ban(
                (string) ($e['edge'] ?? ''),
                $c1_ping
            );
            $results[] = [
                'peer' => $e,
                'ping' => $c1_ping,
            ];
        }

        // --- Not approved: pure pointers from the map (never talked to) ---
        $pointers = cluster_one_discovered_nodes();

        /* Newest version across the network (for the version color).
           v1.3.7 (AUFTRAG-02 §2): NUR innerhalb der eigenen Reihe. Vorher ging
           das Maximum ueber alles, was die Kanten meldeten — ein einziger
           Nachbar aus der anderen Reihe setzte damit den Hoechstwert fuer das
           ganze Netz und faerbte jeden anderen rot. Gemessen in
           fassung-probe.php, Szene D: ein Knoten, zwoelf falsche Warnungen.

           v1.0.17: DIE RECHNUNG STAND HIER UND NOCH EINMAL AUF DER
           FREUNDES-SEITE. Jetzt steht sie in
           cluster_one_kleinrock_netz_hoechste() — einmal, mit der neuen
           Bedingung darin (ein Pruefstand ist kein Massstab). */
        $eigenreihe = cluster_one_knoten_reihe();
        $c1_pings   = array();
        foreach ($results as $row) {
            $c1_pings[] = $row['ping'];
        }
        $max_ver = cluster_one_kleinrock_netz_hoechste($c1_pings);

        $last = cluster_one_last_sync();
        ?>

        <h2 style="margin-top:2.5em;"><?php esc_html_e('Nodes in the network', 'cluster-one-kleinrock'); ?></h2>

        <div style="display:flex;align-items:center;gap:14px;margin:1em 0;flex-wrap:wrap;">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                <?php wp_nonce_field('cluster_one_refresh'); ?>
                <input type="hidden" name="action" value="cluster_one_refresh">
                <button type="submit" class="button"><?php esc_html_e('Refresh now', 'cluster-one-kleinrock'); ?></button>
            </form>
            <span style="opacity:.7;font-size:.92em;">
                <?php
                if ($last > 0) {
                    /* translators: %s: Zeitspanne seit der letzten Aktualisierung. */
                    printf(esc_html__('Last updated %s ago.', 'cluster-one-kleinrock'), esc_html(human_time_diff($last, time())));
                } else {
                    esc_html_e('Not yet updated.', 'cluster-one-kleinrock');
                }
                /* GABEL 0.9.69 (F16): DIE UHR KANN STILLSTEHEN, UND NICHTS SAGTE ES.
                   Der ganze Hintergrundbetrieb haengt an einem Hook. Zwei
                   verbreitete Zustaende melden einen gueltigen Termin und fuehren
                   ihn nie aus: DISABLE_WP_CRON ohne funktionierenden Ersatz beim
                   Hoster, und blockierter Loopback (spawn_cron ruft die eigene
                   Seite per HTTP). In beiden Faellen sieht diese Seite gesund aus
                   und der Knoten ist stumm.
                   Das Plugin weiss das alles bereits — es sagte es nur nicht. */
                $c1_next = wp_next_scheduled('cluster_one_daily_sync');
                if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
                    echo ' <span style="color:#bd8600;">'
                        . esc_html__('DISABLE_WP_CRON is set — the background sync depends on your host\'s cron.', 'cluster-one-kleinrock')
                        . '</span>';
                } elseif ($c1_next && $last > 0 && ($c1_next + 2 * DAY_IN_SECONDS) < time()) {
                    echo ' <span style="color:#bd8600;">'
                        . esc_html__('The scheduled sync is overdue — WP-Cron may not be running on this site.', 'cluster-one-kleinrock')
                        . '</span>';
                }
                ?>
            </span>
            <?php $gmute = cluster_one_global_mute_until(); ?>
            <span style="margin-left:auto;">
                <?php if ($gmute > time()) : ?>
                    <span style="color:#bd8600;font-weight:600;font-size:.92em;margin-right:.5em;">
                        <?php /* translators: %s: Uhrzeit, bis zu der Ruhe gilt. */ ?>
                        <?php printf(esc_html__('Do not disturb (all) — until %s', 'cluster-one-kleinrock'), esc_html(date_i18n(get_option('time_format'), $gmute))); ?>
                    </span>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;margin:0;">
                        <?php wp_nonce_field('cluster_one_unmute_global'); ?>
                        <input type="hidden" name="action" value="cluster_one_unmute_global">
                        <button type="submit" class="button button-small"><?php esc_html_e('Available again', 'cluster-one-kleinrock'); ?></button>
                    </form>
                <?php else : ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;margin:0;">
                        <?php wp_nonce_field('cluster_one_mute_global'); ?>
                        <input type="hidden" name="action" value="cluster_one_mute_global">
                        <label style="font-size:.92em;opacity:.8;"><?php esc_html_e('Do not disturb (all):', 'cluster-one-kleinrock'); ?>
                            <select name="hours" id="c1-mute-hours" style="margin:0 .3em;">
                                <option value="1">1 h</option>
                                <option value="8" selected>8 h</option>
                                <option value="24">24 h</option>
                            </select>
                        </label>
                        <button type="submit" class="button button-small"><?php esc_html_e('Mute', 'cluster-one-kleinrock'); ?></button>
                    </form>
                <?php endif; ?>
            </span>
        </div>

        <table class="widefat striped" style="max-width:880px;margin-top:1em;">
            <thead>
                <tr>
                    <th><?php esc_html_e('URL', 'cluster-one-kleinrock'); ?></th>
                    <th><?php esc_html_e('Name', 'cluster-one-kleinrock'); ?></th>
                    <th><?php esc_html_e('State', 'cluster-one-kleinrock'); ?></th>
                    <th style="text-align:center;"><?php esc_html_e('Version', 'cluster-one-kleinrock'); ?></th>
                    <th style="text-align:center;"><?php esc_html_e('New', 'cluster-one-kleinrock'); ?></th>
                    <?php /* v0.2.5: fehlte seit 0.2.3 — die Steckbrief-Zelle war in der
                       Kanten-Zeile, aber nicht im Kopf; ab hier rutschte die Tabelle. */ ?>
                    <th><?php esc_html_e('Profile', 'cluster-one-kleinrock'); ?></th>
                    <th style="text-align:center;"><?php esc_html_e('Actions', 'cluster-one-kleinrock'); ?></th>
                </tr>
            </thead>
            <tbody>

                <!-- Base: this node -->
                <tr>
                    <td><?php echo cluster_one_identity_cell($self_id, '', '', $self_url); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_identity_cell() gibt fertiges HTML zurueck (esc_url/esc_html im Innern) ?></td>
                    <td><?php echo cluster_one_name_cell($self_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_name_cell() gibt fertiges HTML zurueck (esc_html im Innern) ?></td>
                    <td><span style="color:#1a7f37;font-weight:600;white-space:nowrap;">● <?php esc_html_e('Home', 'cluster-one-kleinrock'); ?></span></td>
                    <td style="text-align:center;"><?php echo cluster_one_version_cell(cluster_one_knoten_fassung(), $max_ver, true, cluster_one_kleinrock_pruefstand()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_version_cell() escapt die Nummer mit esc_html, die Farbe ist ein Literal (bisheriger Zeilenkommentar: eigene Zeile: immer dieselbe Reihe; v1.0.17: und sie sagt, wenn dieser Knoten ein Pruefstand ist) ?></td>
                    <td style="text-align:center;"><?php
                        /* v0.3.10: Die eigene Zeile trug den alten Boolean und war damit
                           dauerhaft gruen. "Neu" ist bei den EIGENEN Labels sinnlos — man
                           kennt sie ja. Also amber, wenn welche da sind, sonst hohl. */
                        echo (function_exists('cluster_one_new_label_signal') && cluster_one_new_label_signal())
                            ? '<span style="color:#b26b00;font-weight:600;" title="' . esc_attr__('Your own published labels — you know them, so nothing is ever “new” here.', 'cluster-one-kleinrock') . '">●</span>'
                            : '<span style="color:#c3c4c7;" title="' . esc_attr__('You have not published a label yet.', 'cluster-one-kleinrock') . '">○</span>';
                    ?></td>
<?php /* v0.7.9: DIE EIGENE ZEILE HATTE NUR SIEBEN SPALTEN, die Kopfzeile acht.
   Alles ab "Profil" rutschte eine nach links: Der Gastraum stand unter Profil,
   der Strich unter Discord/Matrix, und Aktionen blieb ganz leer.

   Christians Befund war deshalb doppelt richtig — beim eigenen Knoten stand ein
   Strich, wo ein gruener Punkt hingehoert, UND das Feld sah falsch aus.
   EIN SPALTENVERSATZ SIEHT AUS WIE ZWEI VERSCHIEDENE FEHLER. */ ?>
                    <td style="max-width:22em;font-size:.92em;opacity:.85;"><?php echo esc_html(cluster_one_peer_steckbrief_zeile(cluster_one_self_profile() + array('ok' => true))); ?></td>
<?php /* v0.7.9: Farbe statt opacity. opacity macht die GANZE Zelle
   halbdurchsichtig, also auch ihren Streifen-Hintergrund — sie sah heller aus
   als der Rest der Zeile (Christians zweiter Befund). */ ?>
                    <td style="text-align:center;color:#c3c4c7;">—</td>
                </tr>

                <?php // --- Approved peers --- ?>
                <?php foreach ($results as $row) :
                    $p    = $row['peer'];
                    $r    = $row['ping'];
                    $edge = (string) ($p['edge'] ?? '');
                    $name = ($r['ok'] && !empty($r['node_id'])) ? $r['node_id'] : (string) ($p['name'] ?? 'Peer');
                    $purl = $r['ok'] ? ($r['url'] ?? '') : (string) ($p['url'] ?? '');
                    $n    = count(cluster_one_get_chat($edge));
                    $unread = cluster_one_edge_unread($edge, (int) ($p['last_read'] ?? 0));
                ?>
                    <tr>
                        <td><?php echo cluster_one_identity_cell($name, '', '', $purl); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_identity_cell() gibt fertiges HTML zurueck (esc_url/esc_html im Innern) ?></td>
                        <td><?php echo cluster_one_name_cell($name); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_name_cell() gibt fertiges HTML zurueck (esc_html im Innern) ?></td>
                        <td>
                            <?php if ($r['ok']) : ?>
                                <span style="color:#1a7f37;font-weight:600;white-space:nowrap;">● <?php esc_html_e('online', 'cluster-one-kleinrock'); ?></span>
                            <?php elseif (!empty($r['remote_disconnect'])) : ?>
                                <span style="color:#b26b00;font-weight:600;white-space:nowrap;" title="<?php echo esc_attr((string) ($r['reason'] ?? '')); ?>">◐ <?php esc_html_e('banned', 'cluster-one-kleinrock'); ?></span>
                            <?php else : ?>
                                <span style="color:#8a8a8a;font-weight:600;white-space:nowrap;" title="<?php echo esc_attr((string) ($r['reason'] ?? '')); ?>">○ <?php esc_html_e('offline', 'cluster-one-kleinrock'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;"><?php echo $r['ok'] ? cluster_one_version_cell($r['version'], $max_ver, (string) ($r['reihe'] ?? '') === $eigenreihe && $eigenreihe !== '', !empty($r['pruefstand'])) : cluster_one_flag_unknown(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_version_cell() escapt die Nummer mit esc_html, die Farbe ist ein Literal; cluster_one_flag_unknown() ist ein Literal ?></td>
                        <td style="text-align:center;"><?php echo cluster_one_label_dot_cell($r, (int) ($p['label_ts_seen'] ?? 0)) . cluster_one_meeting_dot_cell($r, (string) ($p['url'] ?? '')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_label_dot_cell() und cluster_one_meeting_dot_cell() bauen ihre Links mit esc_url/esc_attr, das Zeichen ist ein Literal (bisheriger Zeilenkommentar: v0.3.19: Label und Einladung, je ein Zeichen) ?></td>
                        <td style="max-width:22em;font-size:.92em;opacity:.85;"><?php echo esc_html(cluster_one_peer_steckbrief_zeile($r)); // v0.2.3: was der Knoten von sich preisgibt ?></td>
                        <td style="text-align:center;white-space:nowrap;">
                            <span style="display:inline-flex;align-items:center;gap:.7em;justify-content:center;">

                                <?php // 1) Bann – rot, mit Sicherheitsabfrage (links: hart, weit weg vom Chat) ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline;line-height:1;">
                                    <?php wp_nonce_field('cluster_one_ban'); ?>
                                    <input type="hidden" name="action" value="cluster_one_ban">
                                    <input type="hidden" name="edge" value="<?php echo esc_attr($edge); ?>">
                                    <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;"
                                            title="<?php echo esc_attr__('Ban', 'cluster-one-kleinrock'); ?>"
                                            aria-label="<?php echo esc_attr__('Ban', 'cluster-one-kleinrock'); ?>"
                                            onclick="return confirm('<?php echo esc_js(__('Ban (quarantine) this node? It is blocked at the route and cannot re-pair until you unban it. Reversible.', 'cluster-one-kleinrock')); ?>');">
                                        <span class="dashicons dashicons-dismiss" aria-hidden="true" style="color:#8a8a8a;font-size:20px;width:20px;height:20px;"></span>
                                    </button>
                                </form>

                                <?php // 1b) Freund – einseitig, keine Nachricht an den Peer (v0.0.54) ?>
                                <?php $is_friend = !empty($p['friend']); ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline;line-height:1;">
                                    <?php wp_nonce_field($is_friend ? 'cluster_one_friend_remove' : 'cluster_one_friend_add'); ?>
                                    <input type="hidden" name="action" value="<?php echo $is_friend ? 'cluster_one_friend_remove' : 'cluster_one_friend_add'; ?>">
                                    <input type="hidden" name="edge" value="<?php echo esc_attr($edge); ?>">
                                    <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;"
                                            title="<?php echo $is_friend ? esc_attr__('Remove from friends', 'cluster-one-kleinrock') : esc_attr__('Mark as friend', 'cluster-one-kleinrock'); ?>"
                                            aria-label="<?php echo $is_friend ? esc_attr__('Remove from friends', 'cluster-one-kleinrock') : esc_attr__('Mark as friend', 'cluster-one-kleinrock'); ?>">
                                        <span class="dashicons dashicons-heart" aria-hidden="true" style="color:<?php echo $is_friend ? '#d63384' : '#8a8a8a'; ?>;font-size:20px;width:20px;height:20px;"></span>
                                    </button>
                                </form>

                                <?php // 2) Stumm — EINE Zelle fuer Tabelle, Liste und Chat (H134) ?>
                                <?php echo cluster_one_mute_zelle($edge, !empty($r['remote_disconnect'])); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_mute_zelle() gibt fertiges HTML zurueck (esc_url/esc_attr/wp_nonce_field im Innern) ?>

<?php /* v0.6.9: Das Briefsymbol kommt jetzt aus cluster_one_mail_icon_cell()
   — dieselbe Auskunft in Mesh UND Freundesliste, und wo keine Adresse ist,
   haelt eine Luecke gleicher Breite die Symbole darunter in Linie. */ ?>
                                <?php echo cluster_one_mail_icon_cell($p); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_mail_icon_cell() gibt fertiges HTML zurueck (esc_url/esc_attr im Innern) oder eine leere Luecke ?>
                                <?php // 3) Hallo / Chat – rechts; feste Breite (Ausrichtung), grün nur bei Ungelesenem
                                /* v0.2.9: KEIN toter Knopf mehr. Ein leerer Chat sah aus wie
                                   eine kaputte Funktion — dabei war er nur leer. Der Knopf
                                   fuehrt jetzt immer zum Raum; dort steht, dass es noch nichts
                                   gibt und warum nichts gesendet werden kann. */
                                ?>
                                <?php
                                $hello_label = $n > 0 ? __('Open conversation', 'cluster-one-kleinrock') : __('Say hello', 'cluster-one-kleinrock');
                                $hello_color = $unread > 0 ? '#1a7f37' : '#8a8a8a';
                                ?>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_hello&peer=' . rawurlencode($edge))); ?>"
                                   style="display:inline-flex;align-items:center;gap:.25em;width:3.4em;text-decoration:none;color:<?php echo $hello_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $hello_color ist drei Zeilen hoeher eines von zwei festen Farbliteralen ?>;"
                                   title="<?php echo esc_attr($hello_label); ?>"
                                   aria-label="<?php echo esc_attr($hello_label); ?>">
                                    <span class="dashicons dashicons-format-chat" aria-hidden="true" style="font-size:20px;width:20px;height:20px;flex:none;"></span>
                                    <?php echo cluster_one_hello_count($unread, $n); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_hello_count() gibt fertiges HTML zurueck ((int)-Zahl oder cluster_one_unread_badge mit esc_attr__) ?>
                                </a>

                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php // --- Not approved: pointers we have never talked to --- ?>
                <?php foreach ($pointers as $n) :
                    $purl = (string) ($n['url'] ?? '');
                ?>
                    <tr style="opacity:.85;">
                        <td><?php echo cluster_one_identity_cell((string) ($n['node_id'] ?? ''), '', '', $purl); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_identity_cell() gibt fertiges HTML zurueck (esc_url/esc_html im Innern) ?></td>
                        <td><?php echo cluster_one_name_cell((string) ($n['node_id'] ?? '')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_name_cell() gibt fertiges HTML zurueck (esc_html im Innern) ?></td>
                        <td><span style="color:#8a8a8a;white-space:nowrap;"><?php esc_html_e('not approved', 'cluster-one-kleinrock'); ?></span></td>
                        <td style="text-align:center;"><?php echo cluster_one_flag_unknown(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_flag_unknown() gibt ein Literal zurueck ?></td>
                        <td style="text-align:center;"><?php echo cluster_one_flag(((int) ($n['label_ts'] ?? 0)) > 0); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_flag() gibt eines von zwei Literalen zurueck (bisheriger Zeilenkommentar: v0.1.3: ohne Kante gibt es kein "neu" — nur "hat") ?></td>
                        <td><?php /* v0.2.5: Zeiger tragen keinen Steckbrief — den gibt es nur ueber eine echte Kante. */ ?></td>
                        <td style="text-align:center;">
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                                <?php wp_nonce_field('cluster_one_connect_and_stay'); ?>
                                <input type="hidden" name="action" value="cluster_one_connect_and_stay">
                                <input type="hidden" name="peer_url" value="<?php echo esc_attr($purl); ?>">
                                <button type="submit" class="button button-small"
                                        title="<?php echo esc_attr__('Test: run the domain proof now. On success the node becomes approved and you can say hello.', 'cluster-one-kleinrock'); ?>"><?php esc_html_e('Test', 'cluster-one-kleinrock'); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($results) && empty($pointers)) : ?>
                    <tr>
                        <td colspan="7" style="opacity:.7;"><?php /* 18.10.2026 (Neo): 7 Spalten, nicht 8 */ ?>
                            <?php echo wp_kses_post(__('No other nodes yet. Reach a first node you know by URL under <em>Cluster One → Settings</em>; the map then fills itself as edges exchange pointers.', 'cluster-one-kleinrock')); ?>
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
        <p style="opacity:.7;font-size:.92em;margin-top:1em;max-width:880px;">
            <?php echo wp_kses_post(__('One list, the URL first (the unfakeable anchor), the name beside it. <strong>This node</strong> is you. <strong>Not approved</strong> rows are pointers the map carries — visible, never talked to. <em>Test</em> runs the domain proof; <em>Say hello</em> / <em>Continue</em> open the chat. Besides the address and node ID it is paired with, the ping carries this node&#8217;s version and which numbering series that number belongs to, whether labels changed, how many meetings are open — and the profile fields you released under Settings. Never the content of anything. Banned nodes live under <em>Banned</em>.', 'cluster-one-kleinrock')); ?>
        </p>
        <script>
        (function () {
            // Per-edge mute borrows the duration from the top "Do not disturb (all)" control.
            var top = document.getElementById('c1-mute-hours');
            if (!top) return;
            function sync() {
                document.querySelectorAll('.c1-mute-hours-sync').forEach(function (h) { h.value = top.value; });
            }
            top.addEventListener('change', sync);
            sync();
        })();
        </script>

<?php /* v0.9.50: "Die Bauhuette fuer CLU573R$." ist hier ersatzlos gestrichen
   (Christian, 20.08.2026). Der Satz stand seit 0.0.1 als Baustellenschild unter
   der Mesh-Seite. Ein Baustellenschild ist ehrlich, solange es niemand anderes
   sehen kann — sobald das Plugin ausserhalb dieses Knotens liegt, sagen
   Versionsnummer und Changelog praeziser, in welchem Zustand es ist. Und ein
   Schild, das dauerhaft haengt, wird von niemandem mehr gelesen: dieselbe
   Klasse wie ein Text, den keiner nachprueft. */ ?>
    </div>
    <?php
}


/** Neue, stabile Kanten-ID — zufällig, NICHT vom Token abgeleitet. */
function cluster_one_new_edge_id(): string {
    return bin2hex(random_bytes(8));
}


/** Secure-Anti-Babel für Hallo-Chat (v0.0.55): SysOp-exklusiv, pro Nachricht.
   Einmal übersetzt, sehen ALLE auf der Weißliste die Übersetzung — keine
   Einzelanfrage pro Mensch nötig. Die KI bekommt nur den reinen Text, nie
   den Namen des Verfassers (siehe cluster_one_translate_text). */
add_action('admin_post_cluster_one_hello_translate', function () {
    /* v0.9.48: DIE KOSTENWAHRHEIT, GESCHAERFT.

       Bis hierher stand hier cluster_one_is_sysop(). Der Satz dahinter hiess
       aber nie "nur der SysOp darf uebersetzen", sondern "NUR DER ZAHLENDE
       DARF EINE KI RUFEN" (0.9.13) — der SysOp war bloss immer der Zahlende.
       Hat er erklaert, dass der gesetzte Uebersetzer auf seiner eigenen
       Maschine laeuft, und die Freigabe umgelegt, dann zahlt niemand pro
       Aufruf, und die Regel hat nichts mehr zu schuetzen.

       Die Reihenfolge ist Absicht: erst can_access (wer ueberhaupt im Haus
       ist), dann die eine ODER die andere Berechtigung. Der SysOp kommt
       IMMER durch, auch ohne Freigabe — sonst haette er sich selbst
       ausgesperrt, indem er anderen etwas erlaubt. */
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    if (!cluster_one_is_sysop() && !cluster_one_mesh_translate_open()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_hello_translate');

    $edge  = isset($_POST['edge'])   ? sanitize_text_field(wp_unslash($_POST['edge']))   : '';
    $msgid = isset($_POST['msg_id']) ? sanitize_text_field(wp_unslash($_POST['msg_id'])) : '';
    $back  = admin_url('admin.php?page=cluster_one_hello&peer=' . rawurlencode($edge));

    $opts   = get_option('cluster_one_settings', array());
    $cid    = (string) ($opts['mesh_translator'] ?? '');
    $target = (string) ($opts['mother_tongue'] ?? '');

    if ($edge === '' || $msgid === '' || $cid === '' || $target === '') {
        wp_safe_redirect($back);
        exit;
    }

    $msgs = cluster_one_get_chat($edge);
    foreach ($msgs as $i => $m) {
        if ((string) ($m['id'] ?? '') !== $msgid) {
            continue;
        }
        /* v1.3.0: Wer drueckt, zahlt — ausser der SysOp hat mit
           mesh_translate_all erklaert, dass er uebernimmt. Entschieden wird das
           an genau einer Stelle: cluster_one_akteur_mesh() im Boden. */
        $res = cluster_one_translate_text($cid, $target, (string) ($m['body'] ?? ''),
            cluster_one_akteur_mesh());
        if (empty($res['ok'])) {
            wp_safe_redirect(add_query_arg('tr_err', (string) $res['err'], $back));
            exit;
        }
        $msgs[$i]['translation'] = (string) $res['text'];
        cluster_one_save_chat($edge, $msgs);
        break;
    }
    wp_safe_redirect($back);
    exit;
});

/* --- UNGELESEN-Modell ------------------------------------------------
   „Gelesen" = die Chat-Seite dieser Kante wurde geöffnet (last_read pro
   Kante im paired_map). Ungelesen = eingegangene (dir=in) Nachrichten mit
   ts > last_read. Der Gesamt-Zähler ist gecacht: +1 bei Eingang, voller
   Neuabgleich beim Öffnen (selbstheilend), damit nicht jeder Seitenaufruf
   alle Chats durchzählt. */

/** Ungelesene (eingehende) Nachrichten einer Kante. */
function cluster_one_edge_unread(string $edge, ?int $last_read = null): int {
    if ($last_read === null) {
        $a = cluster_one_get_paired_map();
        $last_read = isset($a[$edge]) ? (int) ($a[$edge]['last_read'] ?? 0) : 0;
    }
    $n = 0;
    foreach (cluster_one_get_chat($edge) as $m) {
        if (($m['dir'] ?? '') === 'in' && (int) ($m['ts'] ?? 0) > $last_read) {
            $n++;
        }
    }
    return $n;
}

/** Voller Neuabgleich des Gesamt-Zählers (nur aktive Kanten). */
function cluster_one_recompute_unread_total(): int {
    $sum = 0;
    foreach (cluster_one_get_paired_map() as $e) {
        if (($e['status'] ?? 'active') !== 'active') {
            continue;
        }
        $edge = (string) ($e['edge'] ?? '');
        if ($edge !== '') {
            $sum += cluster_one_edge_unread($edge, (int) ($e['last_read'] ?? 0));
        }
    }
    return $sum;
}

/** Gecachter Gesamt-Zähler (für die Admin-Leiste). Seedet sich beim ersten Mal. */
function cluster_one_unread_total(): int {
    $v = get_option('cluster_one_unread_total', null);
    if ($v === null) {
        $v = cluster_one_recompute_unread_total();
        update_option('cluster_one_unread_total', $v); // autoload=yes: jede Admin-Seite liest ihn
    }
    return max(0, (int) $v);
}

/** Zähler verändern (z. B. +1 bei Eingang). */
function cluster_one_bump_unread_total(int $by): void {
    update_option('cluster_one_unread_total', max(0, cluster_one_unread_total() + $by));
}

/** Öffnen = gelesen: last_read setzen, Gesamt-Zähler neu synchronisieren. */
function cluster_one_mark_read(string $edge): void {
    $a = cluster_one_get_paired_map();
    if (isset($a[$edge])) {
        $a[$edge]['last_read'] = time();
        cluster_one_save_paired_map($a);
        update_option('cluster_one_unread_total', cluster_one_recompute_unread_total());
    }
}

/** Kleines Zähler-Badge (grün) – leer bei 0. */
function cluster_one_unread_badge(int $n): string {
    if ($n < 1) {
        return '';
    }
    return ' <span style="display:inline-block;min-width:1.4em;padding:0 .4em;'
         . 'background:#1a7f37;color:#fff;border-radius:1em;font-size:.78em;'
         . 'font-weight:700;text-align:center;line-height:1.5;" title="'
         . esc_attr__('Unread messages', 'cluster-one-kleinrock') . '">' . (int) $n . '</span>';
}

/** Zahl am Hallo-Icon: grün = Ungelesen (neu), grau = Gesamtstand (gelesen),
   gar nichts bei leerem Faden (0). So liest man Verlaufstiefe und „neu" zugleich. */
function cluster_one_hello_count(int $unread, int $total): string {
    if ($unread > 0) {
        return cluster_one_unread_badge($unread); // grünes Pill
    }
    if ($total > 0) {
        return ' <span style="opacity:.55;font-size:.85em;font-weight:600;">' . (int) $total . '</span>';
    }
    return '';
}

function cluster_one_mark_delivered(string $edge_id, string $id): void {
    $msgs = cluster_one_get_chat($edge_id);
    foreach ($msgs as &$m) {
        if (($m['id'] ?? '') === $id) {
            $m['delivered'] = true;
            break;
        }
    }
    unset($m);
    cluster_one_save_chat($edge_id, $msgs);
}

/* v1.5.0 (H129): DIE ZUSTELLUNG GIBT EINEN GRUND ZURUECK, KEINEN BOOLEAN.
 *
 * Bis hierher endete diese Funktion in
 *
 *     return wp_remote_retrieve_response_code($resp) === 200;
 *
 * und faltete damit SECHS verschiedene Antworten der Gegenseite in ein
 * einziges `false`: 429 (ein Zug, dann warten), 403 mute, 403 gebannt,
 * 403 pending, Zeitueberschreitung und WP_Error. Der Aufrufer konnte danach
 * nur noch "nicht zugestellt" sagen — und die Seite sagte "Der Peer war nicht
 * erreichbar", auch wenn der Peer sofort und wahrheitsgemaess geantwortet hat.
 *
 *     Ein Empfaenger, der ehrlich "nicht jetzt" sagt, und ein Absender, der
 *     daraus "nicht erreichbar" macht, haben zusammen eine Auskunft
 *     vernichtet, die schon da war.
 *
 * Christian hat es am 30.08.2026 auf zwei laufenden Knoten gemessen: "beim
 * ersten Senden nicht, beim zweiten Mal schon."
 *
 * WARUM DER GRUND AUS DEM KOERPER KOMMT UND NICHT NUR AUS DEM CODE: Die
 * Gegenseite sagt seit 0.9.38 in `cluster_one_status`, was los ist. Der Code
 * allein reicht nicht — 403 traegt drei verschiedene Faelle. Wir lesen also
 * das Feld und benutzen den Code nur als Rueckfall.
 *
 * DER RUECKFALL BEHAUPTET NICHTS. Ein unbekannter Code wird 'abgewiesen',
 * nicht 'unerreichbar' — dieselbe Regel wie bei H120 in 1.3.7: der Rueckfall
 * gehoert an die schwaechste Aussage.
 *
 * v1.6.0 (Kleinrock, Teil 1 seines Zeugen): DER EINFACHE JA-NEIN-WERT BLEIBT.
 *
 * Die erste Reparatur gab ein Array zurueck. Ein nichtleeres Array ist WAHR —
 * und jeder Rufer, der nur `if (cluster_one_deliver(...))` schreibt, haette
 * damit JEDE Absage als Zustellung gebucht: die abgewiesene Nachricht bekaeme
 * `delivered = true`, wuerde nie wieder versucht und waere weg. Kleinrock hat
 * das in einer Wegwerf-Kopie gebaut und gemessen: aus `4 gruen / 2 rot` wurde
 * `1 gruen / 6 rot`, mit Datenverlust.
 *
 *     Wer einen Rueckgabewert reicher macht, aendert JEDEN Rufer — auch die,
 *     die nur "wenn" schreiben und gar nicht hinsehen.
 *
 * Meine drei Rufer waren umgestellt. Die BAUFORM liess den Fehler trotzdem zu,
 * und `: array` schuetzt die Ruferseite nicht: ein `if` auf ein Array kracht
 * nicht, es luegt. Also bleibt der Boolean, und der Grund reist daneben.
 *
 * BENANNTE GRENZE ZU KLEINROCKS ZEUGE, TEIL 1. Er verlangt zweierlei vom
 * RUECKGABEWERT: (a) ein 429 sieht anders aus als ein DNS-Fehler, und (b) der
 * Absagefall ist falsy. Zusammen sind das nur noch die falsy-Werte von PHP —
 * false, 0, '', '0', [], null — als Traeger von sechs Gruenden. Das waere eine
 * Bauform, die niemand mehr liest.
 *
 *     Zwei Forderungen an denselben Wert koennen jede fuer sich richtig sein
 *     und zusammen nur noch eine unlesbare Loesung zulassen.
 *
 * Diese Fassung erfuellt beide ABSICHTEN — der Grund ueberlebt, der einfache
 * Ja-Nein-Wert bleibt —, aber nicht beide Zeilen: der Zeuge ruft mit zwei
 * Argumenten und sieht den dritten nicht. Teil 1a bleibt bei ihm rot, Teil 2
 * uebersprungen. Das ist gemeldet, nicht weggedrueckt.
 *
 * @param string|null $c1_grund  Ausgabe: '' | awaiting_reply | zu_oft | muted |
 *                               banned | pending | nachricht | unerreichbar |
 *                               abgewiesen
 */
function cluster_one_deliver(array $peer, array $msg, &$c1_grund = null): bool {
    $c1_grund = '';
    $endpoint = trailingslashit($peer['url']) . 'wp-json/cluster-one/v1/inbox';
    $resp = wp_safe_remote_post($endpoint, [
        'timeout'   => 5,
        'headers'   => [
            'X-Cluster-Token' => $peer['token'],
            'Content-Type'    => 'application/json',
        ],
        'body'      => wp_json_encode([
            'id'   => $msg['id'],
            'from' => $msg['author'],
            'body' => $msg['body'],
            'ts'   => $msg['ts'],
            /* v0.4.3: Die Art der Nachricht reist als EIGENES FELD, nicht als
               Wort im Text. In 0.4.0 stand die Kennzeichnung im uebersetzten
               Fliesstext — der Sender schickte "[SysOp-Kontakt]", der Empfaenger
               suchte "[SysOp contact]", und die Adresse landete nie an der
               Kante. Ein Marker, den man uebersetzen kann, ist kein Marker
               sondern Anzeige. Protokoll gehoert ins Feld, Sprache in den Text. */
            'kind' => (string) ($msg['kind'] ?? ''),
        ]),
        'sslverify' => true,
    ]);
    if (is_wp_error($resp)) {
        $c1_grund = 'unerreichbar';
        return false;
    }
    $c1_code = (int) wp_remote_retrieve_response_code($resp);
    if ($c1_code === 200) {
        return true;
    }

    /* v1.6.0 (Kleinrock, Teil 4): 400 GILT DER NACHRICHT, NICHT DER KANTE.
       Der Empfaenger antwortet 400, wenn IHM DIESE EINE Nachricht nicht
       gefaellt — `$id === '' || $body === ''` nach SEINEM Reiniger. Und die
       beiden Reiniger sind dieselbe Funktion in zwei Kopien: sie wohnt im
       Boden, und das Netz traegt heute 1.2.1 und 1.3.0 nebeneinander.
       (`strip_tags('<3')` ergibt `''` — ein Herz ist eine leere Nachricht.)

           Eine Absage, die einer Nachricht gilt, darf keine Schlange anhalten. */
    if ($c1_code === 400) {
        $c1_grund = 'nachricht';
        return false;
    }
    $c1_leib = json_decode((string) wp_remote_retrieve_body($resp), true);
    $c1_sagt = is_array($c1_leib) ? (string) ($c1_leib['cluster_one_status'] ?? '') : '';
    if (in_array($c1_sagt, array('awaiting_reply', 'muted', 'disconnected', 'pending'), true)) {
        $c1_grund = ($c1_sagt === 'disconnected') ? 'banned' : $c1_sagt;
        return false;
    }
    /* Ein 429 OHNE unser Feld: ein Vorschaltfilter, eine Wall, ein Knoten vor
       0.9.38. Wir wissen dann nicht, WER bremst — aber dass gebremst wird,
       wissen wir, und das ist etwas anderes als "nicht erreichbar".
       'zu_oft' sagt genau so viel, wie hier gemessen ist, und keinen Satz mehr. */
    if ($c1_code === 429) {
        $c1_grund = 'zu_oft';
        return false;
    }
    $c1_grund = 'abgewiesen';
    return false;
}

/* v1.5.0 (H132): DER OFFENE ZUG — DIE STELLE, AN DER DER ZWEITE SATZ WAECHST.
 *
 * Die Ein-Zug-Regel steht an vier Toren: Farbspiel, Mesh (0.9.36), PN (0.9.38)
 * und Forum (0.9.39). Im FORUM hat sie eine zweite Haelfte bekommen —
 * Christian, 19.08.2026: "Eine Nachricht. Und solange niemand anders eine
 * weitere Nachricht schickt, kann die noch vom Urheber bearbeitet werden."
 * Das CHANGELOG haelt fest, was das bedeutet:
 *
 *     Aus fuenf Beitraegen hintereinander wird ein wachsender Text mit
 *     Geschichte. Die Regel nimmt nichts weg — sie verlegt es.
 *
 * Im Mesh-Chat gab es diese Stelle nicht. Dort war die Regel keine Verlegung,
 * sondern eine Sperre plus eine Warteschlange.
 *
 * WARUM DAS HIER AUTOMATISCH GEHT UND IM FORUM VON HAND: Im Forum steht der
 * erste Beitrag schon oeffentlich; wer ihn aendert, aendert etwas, das andere
 * gelesen haben. Hier ist die erste Nachricht NOCH NICHT ZUGESTELLT — sie ist
 * rein oertlich, niemand hat sie empfangen. An etwas anzuhaengen, das niemand
 * bekommen hat, nimmt niemandem etwas weg.
 *
 *     Worte werden fest, wenn sie zugestellt sind. Die Zustellung ist im Chat
 *     das, was im Forum die fremde Antwort ist — nicht eine Uhr.
 *
 * UND ES GESCHIEHT NICHT STILL. Wer zusammenlegt, ohne es zu sagen, aendert,
 * was ein Mensch geschrieben hat. Der Aufrufer bekommt den Status 'ergaenzt'
 * und die Seite sagt es.
 *
 * @return int Index im Faden, oder -1
 */
function cluster_one_edge_offener_zug(string $edge): int {
    $c1_msgs = cluster_one_get_chat($edge);
    for ($c1_i = count($c1_msgs) - 1; $c1_i >= 0; $c1_i--) {
        $c1_m = (array) $c1_msgs[$c1_i];
        if ((string) ($c1_m['dir'] ?? '') !== 'out') {
            return -1; // der letzte Zug war seiner — die Kante ist frei
        }
        if (empty($c1_m['delivered'])) {
            return $c1_i; // meine letzte, noch nicht zugestellte Nachricht
        }
        return -1; // meine letzte liegt schon drueben und ist damit fest
    }
    return -1;
}

/** Sendet eine eigene Nachricht: lokal anhängen + zustellen. */
function cluster_one_send_message(array $peer, string $body, string $kind = ''): array { // v0.4.3: Art der Nachricht
    $body = cluster_one_sanitize_message($body);
    if ($body === '') {
        return ['ok' => false, 'status' => 'error'];
    }
    $edge = (string) ($peer['edge'] ?? '');

    /* v1.5.0 (H132): Liegt hier schon ein ungesendeter eigener Satz, waechst
       der neue an derselben Stelle weiter, statt sich zu stapeln. Die vorige
       Fassung bleibt darunter stehen — es geht nichts verloren. Das spart
       zugleich den zweiten Zustellversuch: mehr als EINE offene Nachricht je
       Kante kann es ab hier nicht mehr geben (H130). */
    $c1_offen = cluster_one_edge_offener_zug($edge);
    if ($c1_offen >= 0) {
        $c1_msgs = cluster_one_get_chat($edge);
        $c1_alt  = (string) ($c1_msgs[$c1_offen]['body'] ?? '');
        $c1_hist = isset($c1_msgs[$c1_offen]['fassungen']) && is_array($c1_msgs[$c1_offen]['fassungen'])
                 ? $c1_msgs[$c1_offen]['fassungen'] : array();
        $c1_hist[] = array('body' => $c1_alt, 'ts' => (int) ($c1_msgs[$c1_offen]['ts'] ?? time()));
        $c1_msgs[$c1_offen]['fassungen'] = $c1_hist;
        $c1_msgs[$c1_offen]['body']      = $c1_alt . "\n\n" . $body;
        $c1_msgs[$c1_offen]['ts']        = time();
        cluster_one_save_chat($edge, $c1_msgs);
        $c1_g  = '';
        if (cluster_one_deliver($peer, $c1_msgs[$c1_offen], $c1_g)) {
            cluster_one_mark_delivered($edge, (string) ($c1_msgs[$c1_offen]['id'] ?? ''));
            return ['ok' => true, 'delivered' => true, 'status' => 'delivered', 'grund' => ''];
        }
        return ['ok' => true, 'delivered' => false, 'status' => 'ergaenzt', 'grund' => $c1_g];
    }

    $msg = [
        'id'        => cluster_one_msg_id(),
        'author'    => cluster_one_kleinrock_speaker_label(), // Identität in Kleinrock: Mensch (Rolle) von Knoten, nicht nur der Knoten
        'actor_id'  => cluster_one_actor_id_human(), // v0.0.62: der LOKALE Sprecher ist bekannt und wird verankert
        'body'      => $body,
        'ts'        => time(),
        'dir'       => 'out',
        'delivered' => false,
    ];
    if ($kind !== '') {
        $msg['kind'] = $kind; // v0.4.3: reist als eigenes Feld mit
    }
    cluster_one_chat_append($edge, $msg);
    cluster_one_event_emit(array( // flüchtig
        'type' => 'message_created', 'container_type' => 'edge', 'container_id' => $edge,
        'actor_id' => cluster_one_actor_id_human(), 'content_hash' => hash('sha256', $body),
    ), false);
    $c1_g = '';
    if (cluster_one_deliver($peer, $msg, $c1_g)) {
        cluster_one_mark_delivered($edge, $msg['id']);
        return ['ok' => true, 'delivered' => true, 'status' => 'delivered', 'grund' => ''];
    }
    /* v1.5.0 (H129): Der Status ist ab hier der GRUND, nicht das Fehlen einer
       Zustellung. 'pending' heisst weiter genau das, was es immer hiess — der
       Peer war nicht erreichbar — und nur noch das. */
    if ($c1_g === '') { $c1_g = 'abgewiesen'; }
    $c1_status = 'pending';
    if ($c1_g === 'awaiting_reply' || $c1_g === 'zu_oft') { $c1_status = 'zug'; }
    elseif ($c1_g === 'muted')      { $c1_status = 'stumm'; }
    elseif ($c1_g === 'banned')     { $c1_status = 'gebannt'; }
    elseif ($c1_g === 'pending')    { $c1_status = 'wartet'; }
    elseif ($c1_g === 'abgewiesen') { $c1_status = 'abgewiesen'; }
    elseif ($c1_g === 'nachricht')  { $c1_status = 'nachricht'; }
    return ['ok' => true, 'delivered' => false, 'status' => $c1_status, 'grund' => $c1_g];
}

/** Best-effort: noch nicht zugestellte ausgehende Nachrichten erneut senden. */
function cluster_one_flush_pending(array $peer): void {
    $edge = (string) ($peer['edge'] ?? '');
    $msgs = cluster_one_get_chat($edge);
    $changed = false;
    foreach ($msgs as &$m) {
        if (($m['dir'] ?? '') === 'out' && empty($m['delivered'])) {
            $c1_g = '';
            if (cluster_one_deliver($peer, $m, $c1_g)) {
                $m['delivered'] = true;
                $changed = true;
            } elseif ($c1_g === 'nachricht') {
                /* v1.6.0 (Kleinrock, Teil 4): DIESE Nachricht mag der
                   Empfaenger nicht — die naechste vielleicht schon. Ein
                   Abbruch hier machte aus einer verdorbenen Nachricht einen
                   Riegel vor der ganzen Kante: dauerhaft, lautlos, bei jedem
                   Seitenaufruf neu. Also weiter, und die eine bleibt offen. */
                continue;
            } else {
                /* v1.5.0 (H130): NACH DEM ERSTEN NEIN WIRD ABGEBROCHEN.
                   Bis hierher lief diese Schleife ueber JEDE liegengebliebene
                   Nachricht und griff je Nachricht mit fuenf Sekunden Deckel
                   nach draussen — vor dem Rendern der Seite. Die Chatseite
                   kostete damit (Rueckstand + 1) Rundreisen, und der Rueckstand
                   schrumpfte wegen der Ein-Zug-Regel nur je Antwort um eins:
                   die Seite wurde genau dann langsam, wenn ohnehin etwas
                   klemmte.

                   Wer beim ersten Nein weitermacht, fragt dieselbe Frage
                   nochmal und bekommt dieselbe Antwort — nur langsamer.

                   Alte Rueckstaende aus Fassungen vor 1.5.0 loesen sich damit
                   je Besuch um eine Nachricht auf; neue koennen nicht mehr
                   entstehen, weil ein offener Zug ab jetzt ergaenzt statt
                   gestapelt wird. */
                break;
            }
        }
    }
    unset($m);
    if ($changed) {
        cluster_one_save_chat($edge, $msgs);
    }
}

/* --- Inbox-Route: nimmt eine Nachricht vom Peer entgegen (Schreibpfad) --- */
add_action('rest_api_init', function () {
    register_rest_route('cluster-one/v1', '/inbox', [
        'methods'             => 'POST',
        'permission_callback' => '__return_true', // Schutz via Token, nicht via WP-Login
        'callback'            => function (WP_REST_Request $request) {

            $sent = (string) $request->get_header('X-Cluster-Token');
            if ($sent === '') {
                return new WP_REST_Response(null, 403);
            }

            // Token -> Kante auflösen (zeitkonstant), wie beim Ping.
            $peer = null;
            foreach (cluster_one_get_peers() as $p) {
                if ($p['token'] !== '' && hash_equals($p['token'], $sent)) {
                    $peer = $p;
                    break;
                }
            }
            if (!$peer) {
                $edge = cluster_one_find_edge_by_token($sent);
                if ($edge && ($edge['status'] ?? '') === 'banned') {
                    return new WP_REST_Response([
                        'cluster_one_status' => 'disconnected',
                        'message'            => __('This edge was banned by the operator of this node.', 'cluster-one-kleinrock'),
                    ], 403);
                }
                /* GABEL 0.9.69 (F14): DIE DRITTE AUSKUNFT.
                   Seit 0.9.68 ist eine von aussen angestossene Kante 'pending'.
                   Sie faellt hier bis jetzt in das nackte 403, und drueben
                   uebersetzt cluster_one_ping() das zu "Rejected (token?)" —
                   bei einer kerngesunden, gerade eben angelegten Kante. Der
                   andere SysOp prueft dann ein Token, das richtig ist.
                   Nach der Logik von 0.2.6 ("'getrennt' war eine Beschoenigung")
                   bekommt der Wartende eine eigene, wahre Antwort. Sie verraet
                   nichts: dass hier jemand auf eine Entscheidung wartet, weiss
                   der Anklopfende ohnehin — er hat ja angeklopft. */
                if ($edge && ($edge['status'] ?? '') === 'pending') {
                    return new WP_REST_Response([
                        'cluster_one_status' => 'pending',
                        'message'            => __('This node is waiting for its operator to decide whether to let you in.', 'cluster-one-kleinrock'),
                    ], 403);
                }
                return new WP_REST_Response(null, 403);
            }

            // Mute beißt am Tor: stummgeschaltete Kante (oder global) lehnt
            // eingehenden Kontakt ehrlich ab, statt ihn lautlos zu schlucken.
            $ms = cluster_one_mute_state_by_edge((string) ($peer['edge'] ?? ''));
            if ($ms['muted']) {
                return new WP_REST_Response([
                    'cluster_one_status' => 'muted',
                    'until'              => $ms['until'],
                    'message'            => __('Not disturbing right now.', 'cluster-one-kleinrock'),
                ], 403);
            }

            /* v0.9.38: EIN ZUG, DANN WARTEN. Wer zuletzt geschrieben hat und
               noch keine Antwort bekam, kommt nicht ein zweites Mal durch.
               Ehrlich abgewiesen, nicht lautlos verschluckt — dieselbe Haltung
               wie beim Mute weiter oben. 429 statt 403, weil es kein Verbot
               ist, sondern ein "nicht jetzt": Die Gegenseite stellt die
               Nachricht ohnehin beim naechsten Besuch erneut zu, und sobald
               hier jemand antwortet, geht sie durch.

               VOR dem Auspacken des Inhalts, damit der gar nicht erst
               gespeichert wird. Archimedes-RSVPs laufen weiter unten durch
               dieselbe Pruefung — auch eine Zusage ist ein Zug. */
            /* v1.6.0: DER DECKEL. Fuenf Zuege des Peers ohne eine Antwort von
               hier — dann ist Schluss, bis jemand antwortet. Ehrlich abgewiesen
               mit 429 und einem eigenen Feld, nicht lautlos verschluckt.

               VOR dem Auspacken des Inhalts, damit der gar nicht erst
               gespeichert wird. */
            if (cluster_one_edge_offene_zuege((string) ($peer['edge'] ?? '')) >= cluster_one_edge_deckel()) {
                return new WP_REST_Response([
                    'cluster_one_status' => 'awaiting_reply',
                    'message'            => __('Five in a row is enough — this node is waiting for a reply before it takes another one.', 'cluster-one-kleinrock'),
                ], 429);
            }

            $params = $request->get_json_params();
            if (!is_array($params)) {
                return new WP_REST_Response(null, 400);
            }

            $id   = isset($params['id'])   ? preg_replace('/[^a-f0-9]/i', '', substr((string) $params['id'], 0, 32)) : '';
            $body = cluster_one_sanitize_message(isset($params['body']) ? (string) $params['body'] : '');
            $from = isset($params['from']) ? sanitize_text_field(substr((string) $params['from'], 0, 100)) : '';

            // v0.0.100: ARCHIMEDES-RSVP — die einzige strukturierte
            // Systemnachricht der Kante (Beschluss: Archimedes tauscht NUR
            // Systemnachrichten; kanten-authentifiziert sind wir hier
            // bereits). Keine Personendaten, nur die Zahl. Die Notiz landet
            // als System-Beitrag AM LABEL (ergänzend, nie ersetzend —
            // Geschichte statt Überschreiben); kein Türereignis.
            if ((string) ($params['type'] ?? '') === 'archimedes_rsvp') {
                $c1_lid   = sanitize_text_field(substr((string) ($params['label'] ?? ''), 0, 40));
                $c1_count = max(0, min(9999, (int) ($params['count'] ?? 0)));
                /* v0.3.15: Die Zusage wird am EVENT gemerkt, nicht nur als
                   Nachricht abgelegt. Bisher standen bei drei zusagenden Knoten
                   drei Systemnachrichten im Thread, und man musste sie im Kopf
                   addieren — bei einer Aenderung kam eine vierte dazu, ohne dass
                   die dritte ungueltig aussah. Die Nachricht bleibt als Spur,
                   die Tabelle ist der Stand. */
                $c1_evs = cluster_one_get_gregor_events();
                foreach ($c1_evs as $c1_i => $c1_e) {
                    if ((string) ($c1_e['archimedes']['label'] ?? '') === $c1_lid
                        || (string) ($c1_e['id'] ?? '') === $c1_lid) {
                        /* v0.4.1: Der SCHLUESSEL ist die Adresse, nicht der Name.
                           Zwei Gaeste, die ihren Knoten gleich benannt haben,
                           landeten sonst in derselben Zeile — und das ist kein
                           Sonderfall, sondern der Regelfall, solange jemand seine
                           Seite nicht umbenennt. Der Name reist weiter mit und
                           steht als Etikett daneben. */
                        $c1_evs[$c1_i]['rsvps'][cluster_one_norm_url((string) ($peer['url'] ?? ''))] = array(
                            'count' => $c1_count,
                            'ts'    => time(),
                            'node'  => sanitize_text_field((string) ($params['node'] ?? '')),
                            'url'   => cluster_one_norm_url((string) ($peer['url'] ?? '')),
                        );
                        cluster_one_save_gregor_events($c1_evs);
                        break;
                    }
                }
                $c1_node  = sanitize_text_field(substr((string) ($params['node'] ?? $peer['name']), 0, 100));
                $c1_th    = cluster_one_get_forum_threads();
                if ($c1_lid !== '' && isset($c1_th[$c1_lid]) && function_exists('cluster_one_thread_is_label') && cluster_one_thread_is_label($c1_th[$c1_lid])) {
                    $c1_ps   = cluster_one_get_forum_posts($c1_lid);
                    /* translators: %1$s: Name des Knotens, %2$d: Anzahl der Menschen. */
                    $c1_ps[] = cluster_one_system_message(sprintf(__('Node “%1$s”: coming with %2$d people.', 'cluster-one-kleinrock'), $c1_node, $c1_count));
                    cluster_one_save_forum_posts($c1_lid, $c1_ps);
                    return new WP_REST_Response(['cluster_one_status' => 'rsvp_noted'], 200);
                }
                return new WP_REST_Response(null, 400);
            }

            if ($id === '' || $body === '') {
                return new WP_REST_Response(null, 400);
            }

            $edge = (string) ($peer['edge'] ?? '');
            // v0.0.62: Das Kanten-Token authentifiziert den KNOTEN, nicht den
            // einzelnen Sprecher. Eine fremd behauptete Personen-ID wird
            // deshalb NICHT übernommen — actor_id ist ehrlich 'node:<edge>',
            // der gelieferte 'from'-String bleibt loses Anzeigelabel.
            // Personen-scharfe Fremd-Identität kommt mit Brücke/DID.
            $is_new = cluster_one_chat_append($edge, [
                'id'        => $id,
                'author'    => $from !== '' ? $from : $peer['name'],
                'actor_id'  => 'node:' . $edge,
                'body'      => $body,
                'ts'        => time(),
                'dir'       => 'in',
                'delivered' => true,
            ]);
            /* v0.4.0: Wenn der SysOp drueben seine Adresse ausdruecklich
               uebergibt, ist sie eine EIGENSCHAFT DER KANTE, kein
               Gespraechsinhalt. Sonst sucht man sie in einem Jahr in einem alten
               Chat — und der ganze Sinn war, den Weg zurueck ins Netz zu
               sichern. Erkannt an der Kennzeichnung, die der Knopf mitschickt;
               eine hingetippte Adresse wird bewusst NICHT gelesen: die ist
               privat und ohne Gewaehr. */
            if ((string) ($params['kind'] ?? '') === 'sysop_mail'
                && preg_match('/[\w.+-]+@[\w-]+\.[\w.-]+/', (string) $body, $c1_mm)) {
                $c1_adr = sanitize_email($c1_mm[0]);
                if ($c1_adr !== '' && is_email($c1_adr)) {
                    $c1_map = cluster_one_get_paired_map();
                    foreach ($c1_map as $c1_k => $c1_v) {
                        if ((string) ($c1_v['edge'] ?? $c1_k) === (string) $edge) {
                            $c1_map[$c1_k]['sysop_mail']    = $c1_adr;
                            $c1_map[$c1_k]['sysop_mail_ts'] = time();
                            update_option('cluster_one_paired_peers', $c1_map, false);
                            break;
                        }
                    }
                }
            }
            if ($is_new) {
                cluster_one_bump_unread_total(1); // sichtbar in Zeile + Admin-Leiste
                cluster_one_event_emit(array( // flüchtig; origin_event_id = Fremd-ID als Echo-Bremse ab Tag eins
                    'type' => 'message_created', 'container_type' => 'edge', 'container_id' => $edge,
                    'actor_id' => 'node:' . $edge, 'origin' => 'peer',
                    'origin_event_id' => $id, 'content_hash' => hash('sha256', $body),
                ), false);
            }

            return new WP_REST_Response(['ok' => true], 200);
        },
    ]);
});

/* --- "Hallo"-Seite rendern --- */
function cluster_one_render_hello_page() {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    $peers = cluster_one_get_peers();

    $sel_edge = isset($_GET['peer']) ? sanitize_text_field(wp_unslash($_GET['peer'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $current = null;
    foreach ($peers as $p) {
        if (($p['edge'] ?? '') === $sel_edge) {
            $current = $p;
            break;
        }
    }
    /* v0.2.8: cluster_one_get_peers() liefert nur AKTIVE Kanten — eine selbst
       gebannte war damit gar nicht auffindbar, der Chat also unerreichbar.
       Das war falsch herum: Ein Bann beendet das Gespraech, er loescht es nicht.
       Also hier zusaetzlich in der Kanten-Karte nachsehen und den Raum als
       ARCHIV oeffnen — lesen ja, schreiben nein. */
    $c1_archiv = false;
    if ($current === null && $sel_edge !== '') {
        foreach (cluster_one_get_paired_map() as $c1_e) {
            $c1_id = (string) ($c1_e['edge'] ?? '');
            if ($c1_id === $sel_edge && ($c1_e['status'] ?? '') === 'banned') {
                $current   = $c1_e + ['edge' => $c1_id];
                $c1_archiv = true;
                break;
            }
        }
    }
    /* 18.10.2026 (Neo): Leser fuer `mail=` — der Kontakt-Knopf schrieb vier
       Ausgaenge in die Adresse, und keiner wurde je angezeigt. */
    $c1_mail_satz = array(
        'sent'    => array('success', __('Your contact address was sent to the other node’s operator.', 'cluster-one-kleinrock')),
        'none'    => array('warning', __('No e-mail address to send: the SysOp account has none, or it is not valid.', 'cluster-one-kleinrock')),
        'no_edge' => array('warning', __('No active edge to this node — nothing was sent.', 'cluster-one-kleinrock')),
        'error'   => array('error',   __('The other node did not accept the message. Try again later.', 'cluster-one-kleinrock')),
    );
    $c1_mail = isset($_GET['mail']) ? sanitize_text_field(wp_unslash($_GET['mail'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    ?>
    <div class="wrap">
    <?php if (isset($c1_mail_satz[$c1_mail])) : ?>
        <div class="notice notice-<?php echo esc_attr($c1_mail_satz[$c1_mail][0]); ?> is-dismissible"><p><?php echo esc_html($c1_mail_satz[$c1_mail][1]); ?></p></div>
    <?php endif; ?>
        <h1><?php echo cluster_one_page_h1('Hello'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>

        <?php
        if (isset($_GET['sent'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $s = sanitize_text_field(wp_unslash($_GET['sent'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            /* v1.5.0 (H129): SECHS FAELLE, SECHS SAETZE.
               Bis hierher gab es zwei Zweige, und der zweite fing alles, was
               nicht zugestellt wurde — auch eine Gegenseite, die sofort und
               wahrheitsgemaess geantwortet hatte. Dieselbe Haltung wie ueberall
               sonst in diesem Haus: ehrlich abweisen statt lautlos verschlucken
               (0.9.68) — und hier zusaetzlich: den richtigen Grund nennen. */
            if ($s === 'delivered') {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Message sent.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($s === 'zug') {
                echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('Too many messages in a row. This node is waiting for an answer before it takes another one — your text is saved and goes out as soon as they reply. One turn at a time.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($s === 'ergaenzt') {
                echo '<div class="notice notice-info is-dismissible"><p>' . esc_html__('Added to your message that is still waiting to go out — one turn at a time. The previous version is kept underneath it, nothing is lost.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($s === 'stumm') {
                echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('Saved. This node is not taking messages right now — it will go out on your next visit.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($s === 'gebannt') {
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('This node has banned you from their side. The message was not delivered.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($s === 'wartet') {
                echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('Saved. That node is still waiting for its operator to decide whether to let you in.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($s === 'abgewiesen') {
                echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('Saved. The peer refused the message and did not say why — it will be retried on your next visit.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($s === 'pending') {
                echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('Saved. The peer was not reachable — it will be retried on your next visit.', 'cluster-one-kleinrock') . '</p></div>';
            }
        }

        if (empty($peers)) {
            echo '<p>' . wp_kses_post(__('No peer configured yet. Add an edge under <em>Cluster One → Settings</em>.', 'cluster-one-kleinrock')) . '</p></div>';
            return;
        }

        // Keine Peer-Auswahl mehr: ein Chat = eine Seite (Hallo-Muster, eigene
        // URL). Navigation zu anderen Chats läuft über die Netz-Liste, nicht
        // über Reiter pro Knoten — das skaliert nicht (500 Knoten = 500 Felder)
        // und war die Quelle der dreifachen „unbenannt (Kennung fehlt)"-Felder.
        if (!$current) {
            echo '<p style="opacity:.7;">'
               . wp_kses_post(__('Open a conversation from the node list under <em>Cluster One → Net</em>.', 'cluster-one-kleinrock'))
               . '</p></div>';
            return;
        }

        // Best-effort: Liegengebliebenes nachreichen, dann Faden zeigen.
        cluster_one_flush_pending($current);

        $edge = (string) ($current['edge'] ?? '');
        // Öffnen = gelesen: last_read setzen und Gesamt-Zähler neu abgleichen.
        cluster_one_mark_read($edge);
        $msgs = cluster_one_get_chat($edge);
        $fmt  = get_option('date_format') . ' ' . get_option('time_format');
        ?>
<?php   /* v1.5.0: AKTUALISIEREN — ein Link, kein Skript.
           Eingehende Nachrichten landen ueber /inbox im Speicher; sichtbar
           werden sie erst beim naechsten Aufbau der Seite. Bisher gab es dafuer
           keinen Weg ausser F5 — die Seite verschwieg, dass man nachsehen muss.

           WARUM KEIN SELBSTTAETIGES NACHLADEN: Jeder Aufbau dieser Seite greift
           nach draussen (Nachreichen, Ping). Ein Takt, der das im Hintergrund
           wiederholt, vervielfacht genau die Kosten, die 1.5.0 gerade
           halbiert hat (H130) — und zwar auf BEIDEN Knoten.

               Ein Knopf, den ein Mensch drueckt, kostet einmal.
               Ein Takt kostet, solange die Seite offen ist. */ ?>
<?php   /* v1.5.1: DAS FENSTER SCROLLT, DIE SEITE NICHT.
           Bis hierher wuchs der Faden nach unten und zog die Seite mit sich:
           je laenger das Gespraech, desto weiter weg das Eingabefeld. Ein
           Gespraech, das mit jeder Zeile seinen eigenen Knopf nach unten
           schiebt, wird mit dem Gebrauch unbenutzbarer.

           max-height + overflow-y, mehr nicht. Die drei Zeilen Skript unten
           setzen den Ausschnitt EINMAL ans Ende (das Neueste steht unten) und
           laufen nie wieder — kein Takt, kein Beobachter. Ohne Skript bleibt
           die Seite vollstaendig benutzbar, sie startet dann nur oben. */ ?>
        <div id="c1-faden" style="max-width:680px;max-height:52vh;overflow-y:auto;overscroll-behavior:contain;border:1px solid #dcdcde;border-radius:8px;padding:12px;background:#fff;">
            <?php if (empty($msgs)) : ?>
                <p style="opacity:.6;margin:.3em 0;"><?php esc_html_e('No messages yet. Say hello!', 'cluster-one-kleinrock'); ?></p>
            <?php else : ?>
                <?php
                $mesh_tr_opts = get_option('cluster_one_settings', array());
                $mesh_tr_cid  = (string) ($mesh_tr_opts['mesh_translator'] ?? '');
                $mother_tongue = (string) ($mesh_tr_opts['mother_tongue'] ?? '');
                $translator_ready = $mesh_tr_cid !== '' && $mother_tongue !== '';
                ?>
                <?php foreach ($msgs as $m) :
                    $out     = (($m['dir'] ?? '') === 'out');
                    $align   = $out ? 'right' : 'left';
                    $bg      = $out ? '#e7f0ff' : '#f0f0f1';
                    $author  = esc_html($m['author'] ?? '');
                    $time    = esc_html(wp_date($fmt, (int) ($m['ts'] ?? 0)));
                    $bodyhtml = nl2br(esc_html($m['body'] ?? ''));
                    $pending = ($out && empty($m['delivered']))
                        ? ' <em style="opacity:.6;">' . esc_html__('(delivering…)', 'cluster-one-kleinrock') . '</em>' : '';
                    $translation = trim((string) ($m['translation'] ?? ''));
                ?>
                    <div style="text-align:<?php echo $align; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $align ist neun Zeilen hoeher eines von zwei festen Literalen (right/left) ?>;margin:.45em 0;">
                        <div style="display:inline-block;max-width:80%;text-align:left;background:<?php echo $bg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $bg ist neun Zeilen hoeher eines von zwei festen Farbliteralen ?>;padding:.5em .7em;border-radius:8px;">
                            <div style="font-size:.8em;opacity:.6;"><?php echo $author . ' · ' . $time . $pending; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $author und $time sind oben mit esc_html() gebaut, $pending aus esc_html__() ?></div>
                            <div><?php echo $bodyhtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $bodyhtml = nl2br(esc_html(...)) acht Zeilen hoeher ?></div>
                            <?php if ($translation !== '') : ?>
                                <div style="margin-top:.4em;padding-top:.4em;border-top:1px dashed #c3c4c7;">
                                    <div style="font-size:.75em;opacity:.6;font-style:italic;"><?php esc_html_e('Translation (Secure Anti-Babel)', 'cluster-one-kleinrock'); ?></div>
                                    <div style="white-space:pre-wrap;"><?php echo nl2br(esc_html($translation)); ?></div>
                                </div>
<?php /* v0.9.48: Der Knopf spiegelt die Server-Regel (P0-3) — dieselbe
   Bedingung, nicht eine zweite Rechnung. */ ?>
                            <?php elseif (!$out && $translator_ready && (cluster_one_is_sysop() || cluster_one_mesh_translate_open())) : ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:.4em 0 0;">
                                    <?php wp_nonce_field('cluster_one_hello_translate'); ?>
                                    <input type="hidden" name="action" value="cluster_one_hello_translate">
                                    <input type="hidden" name="edge" value="<?php echo esc_attr($edge); ?>">
                                    <input type="hidden" name="msg_id" value="<?php echo esc_attr((string) ($m['id'] ?? '')); ?>">
                                    <button type="submit" class="button button-small"><?php esc_html_e('Translate (Secure Anti-Babel)', 'cluster-one-kleinrock'); ?></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <script>
        /* Einmal ans Ende, nie wieder. Das Neueste steht unten. */
        (function () {
            var f = document.getElementById('c1-faden');
            if (f) { f.scrollTop = f.scrollHeight; }
        })();
        </script>

        <?php if (isset($_GET['tr_err'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $tr_err = sanitize_key(wp_unslash($_GET['tr_err'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        ?>
            <div class="notice notice-error is-dismissible"><p><?php echo esc_html(cluster_one_ai_error_message($tr_err)); ?></p></div>
        <?php endif; ?>

        <?php
        // Stumm ist asymmetrisch: es weist eingehenden Kontakt ab, blockt aber
        // nicht das eigene Schreiben. Wer hier schreibt, bekommt die Antwort
        // nicht — darum warnen, mit Möglichkeit zum Entmuten (oder auch nicht).
        $ms = cluster_one_mute_state_by_edge($edge);
        if ($ms['muted']) :
        ?>
            <div style="display:flex;align-items:center;gap:.6em;flex-wrap:wrap;max-width:680px;margin:1em 0;background:#fcf3e3;border:1px solid #dba617;border-left-width:4px;border-radius:4px;padding:.7em 1em;color:#7a5b00;">
                <span class="dashicons dashicons-controls-volumeoff" aria-hidden="true" style="color:#bd8600;"></span>
                <span style="flex:1;min-width:14em;font-weight:600;">
                    <?php
                    if ($ms['scope'] === 'global') {
                        esc_html_e('Do not disturb (all) is on — replies will not reach you here until you turn it off on the Net page. You can still write.', 'cluster-one-kleinrock');
                    } else {
                        esc_html_e('This node is muted — its replies will not reach you until you unmute. You can still write.', 'cluster-one-kleinrock');
                    }
                    ?>
                </span>
                <?php if ($ms['scope'] === 'edge') : ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                        <?php wp_nonce_field('cluster_one_unmute'); ?>
                        <input type="hidden" name="action" value="cluster_one_unmute">
                        <input type="hidden" name="edge" value="<?php echo esc_attr($edge); ?>">
                        <input type="hidden" name="return" value="chat">
                        <button type="submit" class="button button-small"><?php esc_html_e('Unmute', 'cluster-one-kleinrock'); ?></button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

<?php
/* v0.2.5: Hat die Gegenseite die Kante getrennt, ist dieser Chat ein ARCHIV.
   Lesen bleibt — ein Gespraech verschwindet nicht, weil die Leitung zu ist.
   Schreiben hat keinen Empfaenger mehr, also gibt es kein Formular. Der Knopf
   in der Mesh-Tabelle allein wuerde nicht reichen: die Seite ist auch ueber
   ihre URL erreichbar. */
$c1_chat_zu = $c1_archiv; // v0.2.8: eigener Bann schliesst das Schreiben ebenso
if (!$c1_chat_zu && $current && !empty($current['url'])) {
    $c1_ping    = cluster_one_ping((string) $current['url'], (string) ($current['token'] ?? ''));
    $c1_chat_zu = !empty($c1_ping['remote_disconnect']);
}
if ($c1_chat_zu) : ?>
        <div class="notice notice-warning" style="max-width:680px;"><p><?php
        if ($c1_archiv) {
            esc_html_e('You banned this node. The conversation stays readable as an archive — a ban ends a conversation, it does not delete it — but nothing can be sent. Lift the ban under Kleinrock → Ban list to talk again.', 'cluster-one-kleinrock');
        } else {
            esc_html_e('This node has banned you from their side. The conversation stays readable — it does not vanish because the line is closed — but nothing can be sent any more.', 'cluster-one-kleinrock');
        }
        ?></p></div>
<?php else : ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:680px;margin-top:1em;">
            <?php wp_nonce_field('cluster_one_send'); ?>
            <input type="hidden" name="action" value="cluster_one_send">
            <input type="hidden" name="peer" value="<?php echo esc_attr($edge); ?>">
            <textarea name="body" class="large-text" rows="3"
                      placeholder="<?php echo esc_attr__('Write a message…', 'cluster-one-kleinrock'); ?>"></textarea>
<?php       /* v1.5.1: Der Aktualisieren-Knopf steht NEBEN dem Senden-Knopf, nicht
               ueber dem Faden. Wer nachsieht, ob etwas Neues da ist, tut das an
               derselben Stelle, an der er antwortet — und muss dafuer nicht an
               den Anfang der Seite zurueck. Christian, 30.08.: "in die Naehe des
               Feldes, wo man neue Nachrichten eintraegt, nicht darueber."

                   Ein Knopf gehoert dorthin, wo die Hand schon ist. */ ?>
            <p style="display:flex;align-items:center;gap:.6em;flex-wrap:wrap;">
                <?php submit_button(__('Send', 'cluster-one-kleinrock'), 'primary', 'submit', false); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_hello&peer=' . rawurlencode($edge))); ?>"
                   class="button button-secondary"><?php esc_html_e('Refresh', 'cluster-one-kleinrock'); ?></a>
                <span style="opacity:.6;font-size:.92em;"><?php
                    esc_html_e('New messages appear when this page is loaded.', 'cluster-one-kleinrock');
                ?></span>
            </p>
        </form>
<?php   /* v1.5.2 (H136): DAS LAUTSPRECHERSYMBOL STEHT UNTER DEM FORMULAR,
           NICHT DARIN.

           In 1.5.1 stand es zwischen <form action=...cluster_one_send> und
           </form>. HTML kennt keine verschachtelten Formulare: der Browser
           bricht das aeussere ab, und "Senden" schickte daraufhin
           action=cluster_one_mute mit hours=8. Christian, 31.08., 01:00:
           "Wenn ich eine Nachricht abschicke, erreicht sie nicht meinen
           Bruder. Stattdessen wird auf stumm geschaltet."

               Ein Knopf, der in einem fremden Formular steht, gehoert nicht
               mehr sich selbst.

           Das Haus hatte diese Familie schon einmal (0.7.1: ein Block mitten
           in einem <a>-Tag, die Attribute erschienen als Text). Beide Male
           war der Code richtig und die STELLE falsch, und beide Male hat es
           kein Zeuge gefangen, weil Zeugen Funktionen fahren und keine
           Auszeichnung. Deshalb gibt es seit heute naht.php-Posten "Formular
           im Formular" (zeuge-h136.php). */ ?>
        <p style="max-width:680px;margin:.2em 0 1.2em;display:flex;align-items:center;gap:.4em;">
            <span style="margin-left:auto;display:inline-flex;align-items:center;opacity:.85;">
                <?php echo cluster_one_mute_zelle($edge, $c1_archiv); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_mute_zelle() gibt fertiges HTML zurueck (esc_url/esc_attr/wp_nonce_field im Innern) ?>
            </span>
        </p>
<?php
/* v0.4.0: Der Mail-Knopf. SysOp zu SysOp — er sagt "ich bin hier zustaendig",
   und zustaendig ist nur einer. Beschirmte duerfen ihre Adresse hintippen, aber
   dann ist sie privat und ohne Gewaehr. */
if (cluster_one_is_sysop()) : ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:.4em 0 1.2em;">
            <?php wp_nonce_field('cluster_one_hello_mail'); ?>
            <input type="hidden" name="action" value="cluster_one_hello_mail">
            <input type="hidden" name="peer" value="<?php echo esc_attr($sel_edge); ?>">
            <button type="submit" class="button button-small"><span class="dashicons dashicons-email-alt" aria-hidden="true" style="font-size:16px;width:16px;height:16px;vertical-align:text-bottom;"></span> <?php esc_html_e('Send my address', 'cluster-one-kleinrock'); ?></button>
            <span class="description"><?php esc_html_e('Puts your own e-mail into this chat, marked as coming from this node’s operator. For nodes that are invisible on the open web and have no contact form — this is the way back. It stays in this conversation: not in the ping, not in the mesh, not in your profile.', 'cluster-one-kleinrock'); ?></span>
        </form>
<?php endif; ?>
<?php endif; ?>

        <p style="opacity:.7;font-size:.9em;max-width:680px;">
            <?php esc_html_e('This conversation lives on the edge between two nodes. Saying hello grants no rights in either node. Messages are mirrored — each node keeps its own copy.', 'cluster-one-kleinrock'); ?>
        </p>
    </div>
    <?php
}

/* --- Speicher: gepaarte Knoten (autoload=no), keyed by Kanten-ID --- */
function cluster_one_get_paired_map(): array {
    $a = get_option('cluster_one_paired_peers', []);
    return is_array($a) ? $a : [];
}

function cluster_one_save_paired_map(array $a): void {
    update_option('cluster_one_paired_peers', $a, false);
}

function cluster_one_set_edge_status(string $edge, string $status): void {
    $a = cluster_one_get_paired_map();
    if (isset($a[$edge])) {
        $a[$edge]['status'] = ($status === 'banned') ? 'banned' : 'active';
        cluster_one_save_paired_map($a);
    }
}

/** Freundes-Markierung (v0.0.54): eigenes, von 'status' unabhängiges Feld
   — eine Kante ist entweder aktiv/gebannt (status) UND unabhängig davon
   Freund oder nicht (friend). Rein lokal, einseitig, keine Nachricht
   verlässt je den Knoten — genau das Gegenstück zur Bannliste, die
   denselben Charakter schon hat. */
function cluster_one_set_edge_friend(string $edge, bool $friend): void {
    $a = cluster_one_get_paired_map();
    if (isset($a[$edge])) {
        $a[$edge]['friend'] = $friend;
        cluster_one_save_paired_map($a);
    }
}

/* ======================================================================
   v0.9.40 — ANTI-TROTZ.

   Christians Bild vom 19.08.2026: "Wir haben zwei Knoten. A blockiert B,
   keine Meldung. B blockiert A, keine Meldung. Wenn A als erster die
   Blockade aufhebt, wird B gefragt; wenn B als erster aufhebt, wird A
   gefragt."

   EIN BANN, DESSEN GRUND WEGGEFALLEN IST, IST TROTZ. Er soll nicht durch
   blosse Traegheit ueberleben — aber aufgehoben wird er niemals von
   selbst. Das Plugin fragt, der Mensch entscheidet.

   Wie der Knoten es MERKT, ohne etwas Neues zu senden:

   Ein Bann ist einseitig und lokal, und die Hauptseite pingt NUR aktive
   Kanten. Wen ich gebannt habe, den spreche ich also nie wieder an — ich
   koennte von seiner Kehrtwende gar nichts erfahren, wenn ich ihn dafuer
   anpingen muesste. Umgekehrt gilt dasselbe fuer ihn. Daraus folgt ein
   Beweis, der ohne einen einzigen zusaetzlichen Aufruf auskommt:

     WER MICH ANPINGT, HAT MICH NICHT GEBANNT.

   Beide Haelften sind damit reine Beobachtung:

   1. 'remote_ban_seen' — mein eigener Ping kam mit 'remote_disconnect'
      zurueck, solange die Kante noch aktiv war. Das ist der Moment, in dem
      ICH erfahre, dass ER mich gebannt hat. Danach banne ich zurueck (oder
      nicht — dann gibt es hier nie etwas zu fragen).
   2. 'remote_open_ts' — spaeter klopft SEIN Ping an meiner Route an,
      obwohl ich ihn gebannt habe. Er redet also wieder; sein Bann ist weg.

   Erst wenn BEIDES vorliegt und meiner noch steht, war es eine
   gegenseitige Blockade, die eine Seite beendet hat. Nur dann wird
   gefragt. HABE ICH SEINEN BANN NIE GESEHEN, war mein Bann kein Trotz —
   und ich werde nicht behelligt. Das ist die genannte Grenze dieser
   Funktion, und sie ist der Grund, warum sie nicht bei jedem gewoehnlichen
   Bann nervt: ein Knoten, den ich einfach nur gebannt habe, pingt mich
   weiter an, ohne dass daraus je eine Frage wuerde. */

/** Wie lange „spaeter" dauert. Eine Woche — ein Bann ist eine langsame
   Sache, und eine Woche ist die menschliche Einheit fuer „ich denk drueber
   nach". Die einzige Zahl in dieser Funktion; wer sie anders will, aendert
   sie hier. */
function cluster_one_antitrotz_spaeter(): int {
    return 7 * DAY_IN_SECONDS;
}

/** Beobachtung 1: Mein Ping sagt, ob ER mich gebannt hat. Aufgerufen an der
   EINEN Stelle, an der ein Ping-Ergebnis entsteht (Hauptseite) — ein
   zweiter Schreibweg auf dieselbe Wahrheit waere P1-12. */
function cluster_one_edge_note_remote_ban(string $edge, array $ping): void {
    $a = cluster_one_get_paired_map();
    if (!isset($a[$edge])) {
        return;
    }
    $gebannt = !empty($ping['remote_disconnect']);
    $steht   = !empty($a[$edge]['remote_ban_seen']);
    if ($gebannt === $steht) {
        return; // nichts Neues — nicht schreiben
    }
    if ($gebannt) {
        $a[$edge]['remote_ban_seen'] = time();
    } else {
        /* Sein Bann ist weg, BEVOR ich zurueckgebannt habe. Dann gab es nie
           eine gegenseitige Blockade, und es gibt nichts zu fragen. */
        unset($a[$edge]['remote_ban_seen'], $a[$edge]['remote_open_ts']);
    }
    cluster_one_save_paired_map($a);
}

/** Beobachtung 2: SEIN Ping schlaegt bei mir auf, obwohl ich ihn gebannt
   habe. Aufgerufen aus der Ping-Route, dem einzigen Ort, an dem dieser
   Beweis ankommt. Schreibt hoechstens einmal — der Zeitstempel ist der der
   ERSTEN Wiederansprache, nicht der letzten. */
function cluster_one_edge_note_remote_open(string $edge): void {
    $a = cluster_one_get_paired_map();
    if (!isset($a[$edge]) || empty($a[$edge]['remote_ban_seen'])) {
        return; // ohne gesehenen Gegenbann kein Trotz, also keine Merkung
    }
    if (!empty($a[$edge]['remote_open_ts'])) {
        return;
    }
    $a[$edge]['remote_open_ts'] = time();
    cluster_one_save_paired_map($a);
}

/** Ist die Rueckfrage im ganzen Knoten abgeschaltet? Christians Haken:
   "In allen Fällen nicht mehr fragen." Er ist die Kontrolle gegen den
   Spassvogel, der im Minutentakt bannt und entbannt — und zugleich das
   Recht, diese Funktion schlicht nicht zu wollen. */
function cluster_one_antitrotz_aus(): bool {
    return (bool) get_option('cluster_one_antitrotz_off', false);
}

/** Alle Kanten, bei denen die Frage gerade offen steht. Reiner Leser. */
function cluster_one_antitrotz_offen(): array {
    if (cluster_one_antitrotz_aus()) {
        return array();
    }
    $out = array();
    foreach (cluster_one_get_paired_map() as $id => $e) {
        if (!is_array($e) || ($e['status'] ?? 'active') !== 'banned') {
            continue; // mein Bann muss noch stehen, sonst gibt es nichts aufzuheben
        }
        if (empty($e['remote_ban_seen']) || empty($e['remote_open_ts'])) {
            continue;
        }
        $antwort = $e['antitrotz'] ?? '';
        if ($antwort === 'nein') {
            continue; // beantwortet — eine Entscheidung wird nicht nachgefragt
        }
        if (is_int($antwort) && $antwort > time()) {
            continue; // weggeklickt, kommt von selbst wieder
        }
        $out[(string) $id] = $e;
    }
    return $out;
}

/** v0.9.40: DER EINE ENTBANN-WEG. Es gibt jetzt zwei Tueren dorthin — den
   Knopf auf der Bannliste und das „Ja" der Rueckfrage — aber nur DIESEN
   Schreibweg. P1-12 verbietet zwei Wege auf dieselbe Wahrheit, nicht zwei
   Klinken an derselben Tuer.

   Mit dem Bann gehen die Beobachtungen. Sonst stuende beim naechsten Bann
   gegen denselben Knoten sofort eine Frage im Raum, die sich auf einen
   Streit von vorletztem Jahr bezieht. */
function cluster_one_edge_unban(string $edge): void {
    if ($edge === '') {
        return;
    }
    cluster_one_set_edge_status($edge, 'active');
    $a = cluster_one_get_paired_map();
    if (isset($a[$edge])) {
        unset($a[$edge]['remote_ban_seen'], $a[$edge]['remote_open_ts'], $a[$edge]['antitrotz']);
        cluster_one_save_paired_map($a);
    }
}

/* ============================================================
   DIE WARTENDEN KANTEN (v0.9.68, Befund F6)

   Seit 0.9.68 landet jede Paarung, die von aussen angestossen wurde, auf
   'pending'. Damit gibt es einen dritten Status, und er ist der einzige, aus
   dem heraus ein Mensch etwas entscheiden muss:

       active    der SysOp hat selbst gepaart, oder er hat zugestimmt
       pending   jemand hat angeklopft und den Domain-Beweis bestanden
       banned    der SysOp hat die Tuer zugemacht

   WARUM DAS KEIN VIERTER ZUSTAND IM KOPF DES SYSOPS IST: 'pending' ist genau
   das, was der Entwurf ohnehin behauptet hat. Punkt 7 sagt, ein Zeiger komme
   in ein Gespraech erst, "wenn ein Mensch ihn zieht". Bis 0.9.67 stimmte das
   fuer ausgehende Paarungen und nicht fuer eingehende. Der Zustand hat also
   nicht das Modell erweitert — er hat es endlich eingehalten.
   ============================================================ */

/** Alle Kanten, die anklopfen und auf eine Entscheidung warten. */
function cluster_one_get_pending_edges(): array {
    $out = array();
    foreach (cluster_one_get_paired_map() as $edge => $e) {
        if ((string) ($e['status'] ?? 'active') === 'pending') {
            $e['edge'] = (string) ($e['edge'] ?? $edge);
            $out[] = $e;
        }
    }
    return $out;
}

/** Ja sagen: aus wartend wird aktiv.
 *
 *  ERST HIER wird die Karte des Neuen gezogen — der Griff, der bis 0.9.67
 *  ungefragt in der /pair-Route stand. Er ist nicht falsch, er stand nur an
 *  der falschen Stelle: Er gehoert hinter die Zustimmung, nicht davor.
 *
 *  Nur aus 'pending' heraus. Wer gebannt ist, wird hier nicht still entbannt —
 *  dafuer gibt es seit 0.9.40 genau EINEN Weg (cluster_one_edge_unban), und
 *  P1-12 verbietet den zweiten. */
function cluster_one_edge_approve(string $edge): bool {
    if ($edge === '') {
        return false;
    }
    $a = cluster_one_get_paired_map();
    if (!isset($a[$edge]) || (string) ($a[$edge]['status'] ?? '') !== 'pending') {
        return false;
    }
    $a[$edge]['status']      = 'active';
    $a[$edge]['approved_at'] = time();
    cluster_one_save_paired_map($a);

    if (function_exists('cluster_one_radar_fetch_peer')) {
        cluster_one_radar_fetch_peer(array(
            'url'   => (string) ($a[$edge]['url'] ?? ''),
            'token' => (string) ($a[$edge]['token'] ?? ''),
        ));
    }
    return true;
}

/** Nein sagen: die wartende Kante verschwindet spurlos.
 *
 *  KEIN BANN. Ein Bann ist ein Urteil ueber einen Knoten und bleibt stehen;
 *  ein abgelehntes Anklopfen ist gar nichts — der Fremde darf es spaeter
 *  wieder versuchen, und der SysOp muss keine Liste von Leuten pflegen, die
 *  einmal an seine Tuer geklopft haben. Wer ein Urteil will, bannt. */
function cluster_one_edge_reject(string $edge): bool {
    if ($edge === '') {
        return false;
    }
    $a = cluster_one_get_paired_map();
    if (!isset($a[$edge]) || (string) ($a[$edge]['status'] ?? '') !== 'pending') {
        return false;
    }
    if (function_exists('cluster_one_delete_chat')) {
        cluster_one_delete_chat((string) ($a[$edge]['edge'] ?? $edge));
    }
    unset($a[$edge]);
    cluster_one_save_paired_map($a);
    return true;
}

/** Antwort ablegen. 'nein' = fuer DIESEN Knoten nie wieder; ein Zeitstempel
   = weggeklickt bis dahin. */
function cluster_one_antitrotz_antwort(string $edge, $wert): void {
    $a = cluster_one_get_paired_map();
    if (!isset($a[$edge])) {
        return;
    }
    $a[$edge]['antitrotz'] = $wert;
    cluster_one_save_paired_map($a);
}

/** Aktive, als Freund markierte Peers — dieselbe Filterlogik wie
   cluster_one_get_peers(), zusätzlich auf 'friend' eingeschränkt. */
function cluster_one_get_friend_peers(): array {
    $out  = [];
    $seen = [];
    foreach (cluster_one_get_paired_map() as $edge => $e) {
        if (($e['status'] ?? 'active') !== 'active' || empty($e['friend'])) {
            continue;
        }
        $url   = (string) ($e['url'] ?? '');
        $token = (string) ($e['token'] ?? '');
        if ($url === '' || $token === '' || isset($seen[$token])) {
            continue;
        }
        $seen[$token] = true;
        $e['edge'] = $edge;
        $out[] = $e;
    }
    return $out;
}

/** GABEL 0.9.69 (F15): Welcher Status gilt, wenn eine Kante schon steht?
 *
 *  Bestehender Status gewinnt — ausser der eine Fall, in dem ein Mensch
 *  ausdruecklich gezogen hat: aus 'pending' darf 'active' werden. Kein anderer
 *  Uebergang ist hier moeglich, insbesondere kein stilles Entbannen.
 *
 *  Eigene Funktion, damit die Regel bezeugbar ist, ohne die Route anzufahren —
 *  dieselbe Teilung wie in paarung.php: die Route braucht WordPress, die Regel
 *  nicht. Bis 1.5.1 stand hier cluster_one_edge_awaiting_reply() als Beispiel;
 *  die gibt es nicht mehr, und ein Verweis auf Geloeschtes fuehrt den naechsten
 *  Leser in die Irre. */
function cluster_one_neuer_status(string $alt, string $neu): string {
    if ($alt === '') {
        return $neu;                       // neue Kante: der uebergebene gilt
    }
    if ($alt === 'pending' && $neu === 'active') {
        return 'active';                   // der SysOp hat selbst gezogen
    }
    return $alt;                           // alles andere bleibt, wie es war
}

function cluster_one_store_paired(string $url, string $name, string $token, string $neu_status = 'active'): void {
    $a = cluster_one_get_paired_map();

    // Stabile Kanten-ID: bestehende für diese URL wiederverwenden (Verlauf bleibt),
    // sonst eine neue, vom Token ENTKOPPELTE ID vergeben. So darf der Token später
    // rotieren, ohne dass Chat-Verlauf oder UI-Verweise zerbrechen.
    $edge = '';
    $nurl = cluster_one_norm_url($url);
    foreach ($a as $k => $e) {
        if (cluster_one_norm_url((string) ($e['url'] ?? '')) === $nurl) {
            $edge = (string) ($e['edge'] ?? $k);
            break;
        }
    }
    if ($edge === '') {
        $edge = cluster_one_new_edge_id();
    }

    $a[$edge] = [
        'edge'       => $edge,
        'name'       => $name !== '' ? $name : 'Peer',
        'url'        => $url,
        'token'      => $token,
        /* v0.9.68 (F6): Der Status einer BESTEHENDEN Kante bleibt, wie er ist —
           eine Token-Rotation befoerdert niemanden und degradiert niemanden.
           Nur eine NEUE Kante bekommt den uebergebenen: 'active' fuer den Weg,
           den der SysOp selbst geht, 'pending' fuer alles, was von aussen
           anklopft.

           GABEL 0.9.69 (F15): MIT EINER AUSNAHME, UND SIE IST DER SYSOP SELBST.
           Klopft ein Fremder an (pending) und der SysOp entscheidet sich danach,
           ihn ueber das Allianz-Feld SELBST zu paaren, dann ist das genau die
           Bedingung aus Entwurfspunkt 7 — "ein Mensch zieht ihn". Bis hier hat
           die Regel oben diese Handlung verschluckt: store_paired() behielt
           'pending', active_edge_by_url() fand nichts, und die Weiterleitung
           meldete trotzdem pair=ok. Der Mensch handelte, bekam eine
           Erfolgsmeldung und bewirkte nichts.
           'banned' bleibt unantastbar — dafuer gibt es seit 0.9.40 genau EINEN
           Weg (cluster_one_edge_unban), und P1-12 verbietet den zweiten. */
        'status'     => cluster_one_neuer_status(
                            isset($a[$edge]['status']) ? (string) $a[$edge]['status'] : '',
                            $neu_status
                        ),
        'created'    => isset($a[$edge]['created'])    ? $a[$edge]['created']    : time(),
        'mute_until' => isset($a[$edge]['mute_until']) ? (int) $a[$edge]['mute_until'] : 0,
        'last_read'  => isset($a[$edge]['last_read'])  ? (int) $a[$edge]['last_read']  : 0,
        'friend'     => !empty($a[$edge]['friend']), // v0.0.61 (P1-9): überstand Re-Pairing bisher nicht — Freund blieb Freund, nur das Plugin vergaß es
    ];
    cluster_one_save_paired_map($a);

    // Direkten Peer auch auf die Karte setzen (nur Zeiger, kein Token).
    // Profil verfeinert sich später über Ping/Radar.
    if (function_exists('cluster_one_merge_pointer')) {
        cluster_one_merge_pointer(['url' => $url, 'node_id' => $name], $url);
    }
}

/* --- Mute (Zeitschalter gegen Nerven): global + pro Kante, beißt am Tor /inbox,
   läuft von selbst ab. KEIN Urteil (das ist der Bann), nur "gerade nicht stören". --- */
function cluster_one_global_mute_until(): int {
    return (int) get_option('cluster_one_mute_until', 0);
}

function cluster_one_set_global_mute(int $until): void {
    update_option('cluster_one_mute_until', max(0, $until), false);
}

function cluster_one_edge_mute_until(string $edge): int {
    $a = cluster_one_get_paired_map();
    return isset($a[$edge]) ? (int) ($a[$edge]['mute_until'] ?? 0) : 0;
}

function cluster_one_set_edge_mute(string $edge, int $until): void {
    $a = cluster_one_get_paired_map();
    if (isset($a[$edge])) {
        $a[$edge]['mute_until'] = max(0, $until);
        cluster_one_save_paired_map($a);
    }
}

/* v1.6.0: DER DECKEL AM MESH-TOR — fuenf statt einer.
 *
 * Die Ein-Zug-Regel (0.9.38) ist am 30.08. gefallen, und die Begruendung war
 * falsch. Kleinrock hat sie zerlegt, Zeile fuer Zeile:
 *
 *     :1113  if (!$peer) {                       Token loest KEINE aktive Kante auf
 *     :1131      if (status === 'pending') 403
 *            }
 *     :1143  if (muted) 403
 *     :1162  if (ein Zug offen) 429
 *
 *     "pending feuert nur, wenn der Token KEINEN aktiven Peer aufloest. Die
 *      Ein-Zug-Regel nur, wenn er EINEN aufloest. Sie koennen fuer denselben
 *      Aufruf niemals beide zutreffen."
 *
 * pending schuetzt vor einem Fremden; die Regel schuetzte vor einem Freund.
 * Und die Stummschaltung ist manuell und nachtraeglich — der Anspruch von
 * 0.9.38 war, dass Fluten UNMOEGLICH wird, nicht dass es bestraft wird.
 *
 * WARUM DER FEHLER PASSIERT IST, und das gehoert dazu: Christian hat die Regel
 * abgeschafft, weil das Mesh nicht mehr funktionierte. Die Ursache dafuer war
 * H136 — ein Formular im Formular, meins. Die Regel ist gefallen, weil mein
 * Fehler wie ihr Fehler aussah.
 *
 * WARUM JETZT FUENF UND NICHT EINS: Eins war zu eng fuer einen Menschen, der
 * drei Saetze schreibt. Fuenf ist Christians Zahl vom 31.08. Gezaehlt werden
 * ZUEGE seit der letzten eigenen Antwort — nicht Nachrichten pro Stunde: eine
 * Uhr ist willkuerlich, eine Antwort nicht.
 *
 *     Ein Deckel bei eins ist eine Sperre. Ein Deckel bei fuenf ist eine
 *     Bremse. Kein Deckel ist ein Versprechen an einen fremden SysOp, das
 *     dieses Haus nicht halten kann (DAS-HAUS §0a).
 *
 * @return int  Zuege des Peers seit unserer letzten eigenen Nachricht
 */
function cluster_one_edge_offene_zuege(string $edge): int {
    $c1_n = 0;
    foreach (array_reverse(cluster_one_get_chat($edge)) as $c1_m) {
        $c1_dir = (string) ($c1_m['dir'] ?? '');
        if ($c1_dir === 'out') {
            return $c1_n; // wir haben geantwortet — die Zaehlung endet hier
        }
        if ($c1_dir === 'in') {
            $c1_n++;
        }
    }
    return $c1_n; // leere Kante: das erste Hallo geht immer durch
}

/** v1.6.0: Der Deckel selbst, an EINER Stelle. Wer die Zahl aendern will,
 *  aendert sie hier — und nicht an zwei Orten, die sich spaeter widersprechen. */
function cluster_one_edge_deckel(): int {
    return 5;
}

/* v1.5.1 (H134): EINE ZELLE, EIN LESEWEG.
 *
 * Bis hierher gab es DREI Antworten auf dieselbe Frage:
 *
 *     Chatseite       cluster_one_mute_state_by_edge()   kennt Decke UND einzeln
 *     Mesh-Tabelle    $p['mute_until'] > time()          kennt NUR einzeln
 *     Freundesliste   $e['mute_until'] > time()          kennt NUR einzeln
 *
 * Folge, von Christian am 30.08.2026 auf dem Schirm gefunden: "Nicht stoeren
 * (alle)" laeuft, und die Tabelle bleibt grau. Ihr Lautsprechersymbol
 * verspricht "ist dieser Knoten stumm" und beantwortet "ist diese Kante
 * einzeln stumm".
 *
 *     Zwei Lesewege auf dieselbe Frage sind zwei Antworten, die sich noch
 *     nicht widersprochen haben. Diese haben sich widersprochen.
 *
 * DREI ZUSTAENDE, NICHT ZWEI. mute_state_by_edge() weiss laengst, WOHER die
 * Stille kommt (Feld 'scope') — die Anzeige hat es weggeworfen:
 *
 *     nicht stumm      Lautsprecher grau       Klick = stummschalten
 *     einzeln stumm    Lautsprecher bernstein  Klick = entstummen
 *     unter der Decke  Lautsprecher bernstein, zurueckgenommen; KEIN Klick,
 *                      der nichts bewirkt — sondern ein Weg zum globalen
 *                      Schalter. Ein Knopf, der nichts tut, ist ein luegendes
 *                      Etikett (0.9.26).
 *
 * Liegt BEIDES an, sagt der Titel es: die Decke gilt jetzt, die eigene
 * Entscheidung steht darunter und bleibt, wenn die Decke weggezogen wird.
 */
function cluster_one_mute_zelle(string $edge, bool $abgeklemmt = false): string {
    if ($abgeklemmt) {
        return '<span style="opacity:.35;line-height:1;" title="'
             . esc_attr__('Nothing to mute — this node has banned you from their side.', 'cluster-one-kleinrock')
             . '" aria-hidden="true"><span class="dashicons dashicons-bell" style="font-size:20px;width:20px;height:20px;"></span></span>';
    }

    $c1_z    = cluster_one_mute_state_by_edge($edge);
    $c1_form = admin_url('admin-post.php');
    $c1_uhr  = (string) get_option('time_format');

    /* Unter der Decke: kein Knopf, sondern der Weg zu dem Schalter, der wirkt. */
    if ($c1_z['muted'] && $c1_z['scope'] === 'global') {
        $c1_auch = cluster_one_edge_mute_until($edge) > time();
        $c1_t = sprintf(
            /* translators: %s = time */
            __('Do not disturb (all) is on until %s — turn it off above the table.', 'cluster-one-kleinrock'),
            date_i18n($c1_uhr, (int) $c1_z['until'])
        );
        if ($c1_auch) {
            $c1_t .= ' ' . __('This node is also muted on its own; that stays when the blanket is lifted.', 'cluster-one-kleinrock');
        }
        return '<a href="' . esc_url(admin_url('admin.php?page=cluster_one_mesh')) . '" style="line-height:1;text-decoration:none;" title="'
             . esc_attr($c1_t) . '"><span class="dashicons dashicons-controls-volumeoff" aria-hidden="true" style="color:#bd8600;opacity:.55;font-size:20px;width:20px;height:20px;"></span></a>';
    }

    /* Einzeln stumm: der Knopf hebt genau das auf. */
    if ($c1_z['muted']) {
        return '<form method="post" action="' . esc_url($c1_form) . '" style="margin:0;display:inline;line-height:1;">'
             . wp_nonce_field('cluster_one_unmute', '_wpnonce', true, false)
             . '<input type="hidden" name="action" value="cluster_one_unmute">'
             . '<input type="hidden" name="edge" value="' . esc_attr($edge) . '">'
             . '<button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;" title="'
             /* translators: %s: Uhrzeit, bis zu der stummgeschaltet ist. */
             . esc_attr(sprintf(__('Muted until %s — click to unmute', 'cluster-one-kleinrock'), date_i18n($c1_uhr, (int) $c1_z['until'])))
             . '" aria-label="' . esc_attr__('Unmute', 'cluster-one-kleinrock') . '">'
             . '<span class="dashicons dashicons-controls-volumeoff" aria-hidden="true" style="color:#bd8600;font-size:20px;width:20px;height:20px;"></span>'
             . '</button></form>';
    }

    /* Nicht stumm. */
    return '<form method="post" action="' . esc_url($c1_form) . '" style="margin:0;display:inline;line-height:1;">'
         . wp_nonce_field('cluster_one_mute', '_wpnonce', true, false)
         . '<input type="hidden" name="action" value="cluster_one_mute">'
         . '<input type="hidden" name="edge" value="' . esc_attr($edge) . '">'
         . '<input type="hidden" name="hours" value="8" class="c1-mute-hours-sync">'
         . '<button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;" title="'
         . esc_attr__('Do not disturb from this node (duration from the control above; lifts itself).', 'cluster-one-kleinrock')
         . '" aria-label="' . esc_attr__('Mute', 'cluster-one-kleinrock') . '">'
         . '<span class="dashicons dashicons-controls-volumeon" aria-hidden="true" style="color:#8a8a8a;font-size:20px;width:20px;height:20px;"></span>'
         . '</button></form>';
}

/** v1.5.1 (H135): Welche Kanten sind EINZELN stumm — unabhaengig von der Decke?
 *  Grundlage der Frage, die beim Wegziehen der Decke gestellt wird. Abgelaufene
 *  zaehlen nicht: eine Stummschaltung, die sich selbst geloest hat, ist keine
 *  Entscheidung mehr, die jemand zurueckzunehmen haette. */
function cluster_one_einzeln_stumme_kanten(): array {
    $c1_jetzt = time();
    $c1_aus   = array();
    foreach (cluster_one_get_paired_map() as $c1_id => $c1_e) {
        if ((int) ($c1_e['mute_until'] ?? 0) > $c1_jetzt) {
            $c1_e['edge'] = (string) ($c1_e['edge'] ?? $c1_id);
            $c1_aus[] = $c1_e;
        }
    }
    return $c1_aus;
}

/** v1.5.1 (H135): "Ja" auf die Frage. Gibt zurueck, wie viele geloest wurden. */
function cluster_one_entstumme_alle_einzelnen(): int {
    $c1_karte = cluster_one_get_paired_map();
    $c1_jetzt = time();
    $c1_n     = 0;
    foreach ($c1_karte as $c1_id => $c1_e) {
        if ((int) ($c1_e['mute_until'] ?? 0) > $c1_jetzt) {
            $c1_karte[$c1_id]['mute_until'] = 0;
            $c1_n++;
        }
    }
    if ($c1_n > 0) {
        cluster_one_save_paired_map($c1_karte);
    }
    return $c1_n;
}

/** Aktueller Mute-Zustand für eine Kanten-ID. Global schlägt pro-Kante. */
function cluster_one_mute_state_by_edge(string $edge): array {
    $now = time();
    $g = cluster_one_global_mute_until();
    if ($g > $now) {
        return ['muted' => true, 'until' => $g, 'scope' => 'global'];
    }
    $u = cluster_one_edge_mute_until($edge);
    if ($u > $now) {
        return ['muted' => true, 'until' => $u, 'scope' => 'edge'];
    }
    return ['muted' => false, 'until' => 0, 'scope' => ''];
}

/** GABEL 0.9.69 (F7): Der Schluessel, unter dem ein BANN vergleicht.
 *
 *  Er ist absichtlich GROBER als der Schluessel der Karte: Ein Bann ist das
 *  Urteil eines Menschen ueber einen Betreiber, und 'www.boese.de' ist fuer
 *  diesen Menschen derselbe Betreiber wie 'boese.de'. Ein Urteil, das sich mit
 *  einem Praefix umgehen laesst, ist keins.
 *
 *  Fuer die KARTE gilt das Gegenteil, und aus gutem Grund: Dort wuerde eine
 *  Verschmelzung dazu fuehren, dass token-bewehrte Aufrufe an eine nie
 *  bewiesene Adresse gehen. Grob urteilen, fein sprechen. */
function cluster_one_bann_key(string $url): string {
    $n = cluster_one_norm_url($url);
    if ($n === '') {
        return '';
    }
    $p = wp_parse_url($n);
    if (!is_array($p) || empty($p['host'])) {
        return $n;
    }
    $host = preg_replace('/^www\\./', '', strtolower((string) $p['host']));
    return $host . untrailingslashit((string) ($p['path'] ?? ''));
}

/** Ist diese URL aktuell gebannt? (Blockt Re-Pairing von derselben Domain.) */
function cluster_one_is_url_banned(string $url): bool {
    $k = cluster_one_bann_key($url);
    if ($k === '') {
        return false;
    }
    foreach (cluster_one_get_paired_map() as $e) {
        if (($e['status'] ?? '') === 'banned' && cluster_one_bann_key((string) ($e['url'] ?? '')) === $k) {
            return true;
        }
    }
    return false;
}


/* --- Ausstehende Pairings (Initiator-Seite), kurzlebig, autoload=no --- */
function cluster_one_get_pendings(): array {
    $a = get_option('cluster_one_pending_pairings', []);
    return is_array($a) ? $a : [];
}

function cluster_one_add_pending(string $hid, string $token): void {
    $a = cluster_one_get_pendings();
    foreach ($a as $k => $v) { // abgelaufene (>10 min) aufräumen
        if (time() - (int) ($v['created'] ?? 0) > 600) {
            unset($a[$k]);
        }
    }
    $a[$hid] = ['token' => $token, 'created' => time()];
    update_option('cluster_one_pending_pairings', $a, false);
}

function cluster_one_get_pending(string $hid): ?array {
    $a = cluster_one_get_pendings();
    if (!isset($a[$hid])) {
        return null;
    }
    if (time() - (int) ($a[$hid]['created'] ?? 0) > 600) {
        return null;
    }
    return $a[$hid];
}

function cluster_one_remove_pending(string $hid): void {
    $a = cluster_one_get_pendings();
    if (isset($a[$hid])) {
        unset($a[$hid]);
        update_option('cluster_one_pending_pairings', $a, false);
    }
}

/* --- Offene Route: pair-verify (Rückruf-Beweis der Domain-Kontrolle) ---
   Gibt das Geheimnis NUR zu einem gültigen, frischen Handshake zurück.
   Die handshake_id ist die (unrats­bare, einmalige) Capability. */
add_action('rest_api_init', function () {
    register_rest_route('cluster-one/v1', '/pair-verify', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function (WP_REST_Request $request) {
            $hid = preg_replace('/[^a-f0-9]/i', '', (string) $request->get_param('handshake_id'));
            $hid = substr($hid, 0, 64);
            $pend = $hid !== '' ? cluster_one_get_pending($hid) : null;
            if (!$pend) {
                return new WP_REST_Response(null, 404);
            }
            return new WP_REST_Response([
                'ok'      => true,
                'node_id' => cluster_one_node_id(),
                'token'   => (string) $pend['token'],
            ], 200);
        },
    ]);
});

/* --- Offene Route: pair (Anstoß durch einen fremden Knoten) ---
   Verifiziert die behauptete URL aktiv per Rückruf (SSRF-sicher),
   holt dort das Geheimnis und legt die Kante an. Kein Token nötig. */
add_action('rest_api_init', function () {
    register_rest_route('cluster-one/v1', '/pair', [
        'methods'             => 'POST',
        'permission_callback' => '__return_true',
        'callback'            => function (WP_REST_Request $request) {

            // Kein Mesh ohne Namen: ein namenloser Knoten lässt sich auch nicht
            // von außen paaren (sonst landete er anderswo als „unnamed").
            if (!cluster_one_has_name()) {
                return new WP_REST_Response(['error' => 'node not set up'], 409);
            }

            $p = $request->get_json_params();
            if (!is_array($p)) {
                return new WP_REST_Response(null, 400);
            }

            $hid   = preg_replace('/[^a-f0-9]/i', '', (string) ($p['handshake_id'] ?? ''));
            $hid   = substr($hid, 0, 64);
            $iurl  = isset($p['initiator_url']) ? esc_url_raw(trim((string) $p['initiator_url'])) : '';
            $inode = isset($p['initiator_node_id']) ? sanitize_text_field(substr((string) $p['initiator_node_id'], 0, 100)) : 'Peer';

            if ($hid === '' || $iurl === '') {
                return new WP_REST_Response(['error' => 'bad request'], 400);
            }
            // Nur https – und gebannte Domains gar nicht erst verifizieren.
            if (stripos($iurl, 'https://') !== 0) {
                return new WP_REST_Response(['error' => 'https required'], 400);
            }
            if (cluster_one_is_url_banned($iurl)) {
                return new WP_REST_Response(['error' => 'banned'], 403);
            }

            /* 18.10.2026 (Neo), B2 — DIE BREMSE, VOR DEM RUECKRUF.
               Sie steht hier und nicht weiter unten, weil der Rueckruf das
               Teure ist: ein ausgehender Request auf eine Adresse, die ein
               Unbekannter gewaehlt hat. Bekannte Adressen (irgendeine Kante,
               auch pending; gebannte sind oben schon fort) gehen vorbei: ihr
               Handschlag legt nichts Neues an, er erneuert. */
            if (!cluster_one_kante_zu_url($iurl)) {
                $c1_domain = strtolower((string) wp_parse_url($iurl, PHP_URL_HOST));
                if (!cluster_one_tor_frei('handschlag', $c1_domain, CLUSTER_ONE_HANDSCHLAG_MAX, CLUSTER_ONE_HANDSCHLAG_PAUSE)) {
                    return new WP_REST_Response(['error' => 'too many handshakes'], 429);
                }
                if (cluster_one_offene_handschlaege() >= CLUSTER_ONE_HANDSCHLAG_OFFEN) {
                    return new WP_REST_Response(['error' => 'too many pending handshakes'], 429);
                }
            }

            // Domain-Beweis: Rückruf an die behauptete URL. wp_safe_remote_get
            // blockt interne/private Ziele (SSRF-Schutz), da $iurl unvertraut ist.
            $verify = trailingslashit($iurl) . 'wp-json/cluster-one/v1/pair-verify?handshake_id=' . rawurlencode($hid);
            $resp   = wp_safe_remote_get($verify, [
                'timeout'     => 5,
                'redirection' => 1,
                'sslverify'   => true,
            ]);
            if (is_wp_error($resp) || wp_remote_retrieve_response_code($resp) !== 200) {
                return new WP_REST_Response(['error' => 'verification failed'], 400);
            }
            $data = json_decode(wp_remote_retrieve_body($resp), true);
            if (!is_array($data) || empty($data['token'])) {
                return new WP_REST_Response(['error' => 'no secret'], 400);
            }

            $token = sanitize_text_field((string) $data['token']);
            /* 18.10.2026 (Neo), B7 — DAS TOKEN KOMMT VOM ANDEREN, ALSO WIRD ES
               GEPRUEFT. Es ist das gemeinsame Geheimnis dieser Kante und
               schuetzt /ping, /inbox und /radar. cluster_one_pair_with()
               erzeugt 48 Hex-Zeichen aus random_bytes; ein Gegenueber, das
               „abc" schickt, bekommt keine Kante, deren Schloss man raten
               kann. Grosszuegig im Zeichensatz, streng in der Laenge. */
            if (!preg_match('/^[A-Za-z0-9_\-]{32,128}$/', $token)) {
                return new WP_REST_Response(['error' => 'weak secret'], 400);
            }
            $vnode = isset($data['node_id']) ? sanitize_text_field(substr((string) $data['node_id'], 0, 100)) : '';

            /* v0.9.68 — BEFUND F6 DER MESH-KI, 23.08.2026.
               Hier stand bis 0.9.67 eine Paarung mit dem Vorgabestatus 'active'
               und direkt darunter ein sofortiger Griff nach der fremden Karte.
               Damit hatte ein Unbekannter mit irgendeiner https-Domain — ohne
               einen einzigen Klick auf DIESER Seite — /radar (die ganze Karte),
               /ping (den Steckbrief) und einen Zug in /inbox. Er waehlte sogar
               das gemeinsame Token selbst, denn es kommt aus SEINER Antwort.

               Der Domain-Beweis war nie falsch. Er beweist nur etwas anderes,
               als Entwurfspunkt 7 annimmt: dass der Fremde seine Domain
               besitzt — nicht, dass der Gastgeber ihn hereinlassen will.
               Punkt 7 verlangt beides: "wenn ein Mensch ihn zieht UND die
               behauptete Adresse unter sich selbst antwortet". Die zweite
               Haelfte war gebaut, die erste galt nur fuer AUSGEHENDE Paarungen.

               Jetzt klopft er an: 'pending' passiert cluster_one_get_peers()
               nicht, und weil alle drei token-gesicherten Routen genau dort
               fragen, fallen sie mit einem Griff zu. Die Karte wird erst
               gezogen, wenn ein Mensch zugestimmt hat — cluster_one_edge_approve().

               Die Antwort bleibt 200. Der Anklopfende soll erfahren, dass sein
               Handschlag angekommen ist; ob jemand oeffnet, ist eine andere
               Auskunft und geht ihn nichts an. */
            cluster_one_store_paired($iurl, $vnode !== '' ? $vnode : $inode, $token, 'pending');

            return new WP_REST_Response([
                'ok'       => true,
                'node_id'  => cluster_one_node_id(),
                'base_url' => cluster_one_self_base_url(),
            ], 200);
        },
    ]);
});

/* --- Backend-Aktion: Pairing anstoßen (lokaler Admin, Nonce + Capability) --- */
/* --- Aktive Kante zu einer URL finden (Dublettenschutz / Idempotenz) --- */
function cluster_one_active_edge_by_url(string $url): ?array {
    $n = cluster_one_norm_url($url);
    if ($n === '') {
        return null;
    }
    foreach (cluster_one_get_paired_map() as $e) {
        if (($e['status'] ?? 'active') === 'active'
            && cluster_one_norm_url((string) ($e['url'] ?? '')) === $n) {
            return $e;
        }
    }
    return null;
}

/* --- Pairing mit einer URL (wiederverwendbar). Idempotent: besteht schon
   eine aktive Kante, wird sie zurückgegeben. Selbst-/Bann-/Dublettenschutz.
   Rückgabe: ['ok'=>bool, 'edge'=>string, 'reason'=>string]. --- */
function cluster_one_pair_with(string $rurl): array {
    // Kein Mesh ohne Namen: ohne gesetzten Namen keine (ausgehende) Paarung.
    if (!cluster_one_has_name()) {
        return ['ok' => false, 'edge' => '', 'reason' => 'no_name'];
    }
    $rurl = esc_url_raw(trim($rurl));
    if ($rurl === '' || stripos($rurl, 'https://') !== 0) {
        return ['ok' => false, 'edge' => '', 'reason' => 'bad_url'];
    }
    // Selbst-Eintrag verhindern (man kann sich nicht mit sich selbst paaren).
    if (cluster_one_norm_url($rurl) === cluster_one_norm_url(cluster_one_self_base_url())) {
        return ['ok' => false, 'edge' => '', 'reason' => 'self'];
    }
    // Gebannte Domain nicht (wieder) paaren.
    if (cluster_one_is_url_banned($rurl)) {
        return ['ok' => false, 'edge' => '', 'reason' => 'banned'];
    }
    // Dublette: schon aktiv verbunden? Dann diese Kante nehmen, nicht neu paaren.
    $existing = cluster_one_active_edge_by_url($rurl);
    if ($existing) {
        return ['ok' => true, 'edge' => (string) ($existing['edge'] ?? ''), 'reason' => 'exists'];
    }

    $hid   = bin2hex(random_bytes(16));
    $token = bin2hex(random_bytes(24));
    cluster_one_add_pending($hid, $token);

    $resp = wp_safe_remote_post(trailingslashit($rurl) . 'wp-json/cluster-one/v1/pair', [
        'timeout'   => 8,
        'headers'   => ['Content-Type' => 'application/json'],
        'body'      => wp_json_encode([
            'handshake_id'      => $hid,
            'initiator_url'     => cluster_one_self_base_url(),
            'initiator_node_id' => cluster_one_node_id(),
        ]),
        'sslverify' => true,
    ]);

    $out = ['ok' => false, 'edge' => '', 'reason' => 'unreachable'];
    if (!is_wp_error($resp) && wp_remote_retrieve_response_code($resp) === 200) {
        $data = json_decode(wp_remote_retrieve_body($resp), true);
        if (is_array($data) && !empty($data['ok'])) {
            $rnode = isset($data['node_id'])  ? sanitize_text_field(substr((string) $data['node_id'], 0, 100)) : 'Peer';
            // v0.0.61 (P0-6): Die ANGEFRAGTE URL ist die bewiesene Wahrheit und
            // bleibt die gespeicherte Basis. Die vom Gegenüber zurückgemeldete
            // base_url wird NICHT mehr übernommen — ein bösartiger Knoten
            // konnte darüber eine fremde oder interne Adresse unterschieben,
            // die fortan token-bewehrte Pings und Nachrichten bekommen hätte.
            $rbase = $rurl;
            cluster_one_store_paired($rbase, $rnode !== '' ? $rnode : 'Peer', $token);
            // Frische beim Beitritt: Karte des neuen Peers einmal sofort holen.
            cluster_one_radar_fetch_peer(['url' => $rbase, 'token' => $token]);
            // Echte, stabile Kanten-ID nachschlagen (store_paired vergibt sie, NICHT aus dem Token).
            $stored = cluster_one_active_edge_by_url($rbase);
            $out = ['ok' => true, 'edge' => $stored ? (string) ($stored['edge'] ?? '') : '', 'reason' => 'paired'];
        }
    }
    cluster_one_remove_pending($hid); // Geheimnis-Fenster sofort schließen
    return $out;
}

/* --- Backend-Aktion: Pairing anstoßen (Bootstrap über Settings-Feld) --- */
add_action('admin_post_cluster_one_pair', function () {
    /* v0.9.34: SOUVERAENITAET IST SYSOP-SACHE. Bis hierher prueften diese
       Handler nur cluster_one_can_access() — also die Weissliste. Ein
       Beschirmter konnte damit den Knoten mit einem fremden Knoten
       verbuenden, Kanten trennen und wieder verbinden, den ganzen Knoten
       stummschalten und knotenweite Einstellungen aendern.

       Und die Oberflaeche schuetzte nichts: cluster_one_page_gate() prueft
       dasselbe, und die Allianz-Sektion wird ohne SysOp-Bedingung gerendert
       (kranz.php:524) — der Beschirmte sah das Formular und bekam ein
       gueltiges Nonce auf seinen eigenen Benutzer.

       EINE SPERRE IM HANDLER, NICHT NUR IN DER OBERFLAECHE (Lehre aus 0.3.5),
       hier auf die Stelle angewendet, an der sie fehlte. Ein Beschirmter darf
       im Haus alles, was das Haus ihm gibt — er entscheidet nicht, mit wem
       das Haus verbuendet ist. Das ist die Kostentraeger-Regel.
       Gefunden am 19.08.2026 beim Nachmessen aller 75 Handler. */
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_pair');

    $rurl = isset($_POST['peer_url']) ? wp_unslash($_POST['peer_url']) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- cluster_one_pair_with() reinigt als Erstes: esc_url_raw und https-Pflicht
    $r    = cluster_one_pair_with((string) $rurl);

    /* 18.10.2026 (Neo): zurueck auf die Seite, auf der das Formular steht
       (cluster_one_mesh_settings), nicht auf den Kranz. Der Leser fuer
       `pair=` steht dort, in cluster_one_render_mesh_settings_page(). */
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh_settings&pair=' . ($r['ok'] ? 'ok' : 'error')));
    exit;
});

/* --- Backend-Aktionen: die zwei Loeseknoepfe (v1.0.18) ---------------------
   EINE SPERRE IM HANDLER, NICHT NUR IN DER OBERFLAECHE (Lehre aus 0.3.5 und
   aus v0.9.34): `cluster_one_settings_seite_loesen()` zeigt die Knoepfe nur
   dem SysOp, aber ein Beschirmter bekommt ein gueltiges Nonce auf seinen
   eigenen Benutzer, wenn er die Adresse kennt. Also steht das Tor hier.
       Wer die Tuer des Knotens zumacht, trifft eine Entscheidung ueber den
       Knoten. Das ist SysOp-Sache wie Bann und Paarung.
   ------------------------------------------------------------------------- */
add_action('admin_post_cluster_one_kleinrock_loesen', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_kleinrock_loesen');
    $c1_zahl = cluster_one_kleinrock_verbindungen_loesen();
    wp_safe_redirect(cluster_one_kleinrock_loesen_zurueck('a', $c1_zahl));
    exit;
});

add_action('admin_post_cluster_one_kleinrock_netz_reset', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_kleinrock_netz_reset');
    /* DIE RUECKFRAGE WIRD HIER GEPRUEFT UND NICHT IM BROWSER. Fehlt der
       Haken, ist NICHTS geschehen — und es steht auch nichts in der
       Meldung, denn es gibt nichts zu melden.
           Eine Rueckfrage, die nur im Browser stattfindet, hat der Server
           nicht gestellt. */
    if (empty($_POST['c1_sicher'])) {
        wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh_settings'));
        exit;
    }
    $c1_zahl = cluster_one_kleinrock_knoten_loeschen();
    wp_safe_redirect(cluster_one_kleinrock_loesen_zurueck('b', $c1_zahl));
    exit;
});

/** Der Rueckweg samt Zahlen. EINE Stelle, zwei Handler — sonst waere die
 *  Liste der Argumente zweimal gebaut und eine der beiden wuerde altern.
 *
 *  ES REISEN NUR DIE SCHLUESSEL, DIE DA WAREN. Ein fehlender Schluessel kommt
 *  nicht als 0 an, sondern gar nicht — die Meldung zeigt dann keine Zeile
 *  dafuer, und das ist richtig: die Funktion hat es nicht gemessen.
 */
function cluster_one_kleinrock_loesen_zurueck(string $c1_was, array $c1_zahl): string {
    $c1_args = array('c1_loesen' => $c1_was);
    foreach (array('kanten', 'handschlaege', 'chats', 'zeiger', 'mails') as $c1_k) {
        if (isset($c1_zahl[$c1_k])) {
            $c1_args['c1_' . $c1_k] = (int) $c1_zahl[$c1_k];
        }
    }
    if (isset($c1_zahl['tuer']) && $c1_zahl['tuer'] === false) {
        $c1_args['c1_tuer'] = '0';
    }
    return add_query_arg($c1_args, admin_url('admin.php?page=cluster_one_mesh_settings'));
}

/* --- Backend-Aktionen: Mute (Zeitschalter, läuft von selbst ab) --- */
add_action('admin_post_cluster_one_mute', function () {
    /* v0.9.37: SysOp-Tor, dritte Runde. Christians Leiter vom 19.08.2026:
       "Kante stumm" heisst BITTE NICHT STOEREN und sagt nichts ueber den
       anderen — aber es weist eingehenden Kontakt an der Route ab, und zwar
       fuer ALLE im Haus. Ein milder Bann auf Zeitschalter. Wer die Tuer des
       Knotens zumacht, und sei es fuer acht Stunden, trifft eine Entscheidung
       ueber den Knoten. Das ist SysOp-Sache wie Bann und Paarung. */
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_mute');
    $edge  = isset($_POST['edge'])  ? sanitize_text_field(wp_unslash($_POST['edge'])) : '';
    $hours = isset($_POST['hours']) ? max(1, min(168, (int) $_POST['hours'])) : 8;
    if ($edge !== '') {
        cluster_one_set_edge_mute($edge, time() + $hours * HOUR_IN_SECONDS);
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh&mute=on'));
    exit;
});

add_action('admin_post_cluster_one_unmute', function () {
    /* v0.9.37: SysOp-Tor, s. oben. */
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_unmute');
    $edge = isset($_POST['edge']) ? sanitize_text_field(wp_unslash($_POST['edge'])) : '';
    if ($edge !== '') {
        cluster_one_set_edge_mute($edge, 0);
    }
    // Aus dem Chat heraus entmutet? Dann dorthin zurück, nicht zur Liste springen.
    if ($edge !== '' && isset($_POST['return']) && $_POST['return'] === 'chat') {
        wp_safe_redirect(admin_url('admin.php?page=cluster_one_hello&peer=' . rawurlencode($edge)));
        exit;
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh&mute=off'));
    exit;
});

add_action('admin_post_cluster_one_mute_global', function () {
    /* v0.9.34: SysOp-Tor, s. oben. */
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_mute_global');
    $hours = isset($_POST['hours']) ? max(1, min(168, (int) $_POST['hours'])) : 8;
    cluster_one_set_global_mute(time() + $hours * HOUR_IN_SECONDS);
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh&mute=on'));
    exit;
});

add_action('admin_post_cluster_one_unmute_global', function () {
    /* v0.9.34: SysOp-Tor, s. oben. */
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_unmute_global');
    cluster_one_set_global_mute(0);
    /* v1.5.1 (H135): Die Decke ist weg — liegt darunter noch etwas, wird
       GEFRAGT und nicht entschieden. Der Zusatz steht nur in der Adresse, wenn
       es wirklich etwas zu fragen gibt; eine Frage ohne Gegenstand waere
       Laerm. */
    $c1_rest = cluster_one_einzeln_stumme_kanten();
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh&mute=off' . (empty($c1_rest) ? '' : '&rest=1')));
    exit;
});

/* v1.5.1 (H135): "Ja" auf die Frage nach dem Wegziehen der Decke.
 *
 * Christian, 30.08.2026: "Wenn ich alle wieder freischalte kann das System die
 * Meldung geben: Du hattest bestimmte Chats manuell gemuted, sollen die auch
 * wieder entmutet werden?"
 *
 * WARUM GEFRAGT UND NICHT GETAN: Acht Stunden Ruhe sind eine Pause, keine
 * Amnestie. Ein Knopf, der beim Aufheben der Decke still auch die eigenen
 * Entscheidungen wegraeumt, loescht etwas, das ein Mensch bewusst gesetzt hat.
 *
 *     Eine Decke, die man wegzieht, soll darunter nichts veraendert haben.
 *
 * WARUM OHNE HAKEN "NICHT MEHR FRAGEN" — anders als bei der Anti-Trotz-Frage
 * (0.9.34): Diese hier kommt nur, wenn der SysOp gerade selbst eine Decke
 * weggezogen hat UND darunter etwas liegt. Sie kann ihn also nicht verfolgen.
 * Ein Ausschalter wuerde ihm dauerhaft die einzige Stelle nehmen, an der er
 * erfaehrt, dass unter der Decke noch etwas stumm ist — und genau das war der
 * Zustand, den er ohne diese Meldung nicht sehen konnte. */
add_action('admin_post_cluster_one_unmute_rest', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_unmute_rest');
    $c1_n = cluster_one_entstumme_alle_einzelnen();
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh&mute=rest&n=' . (int) $c1_n));
    exit;
});

/* --- Backend-Aktionen: Bannen (Quarantäne) / Entbannen (lokal, umkehrbar) --- */
add_action('admin_post_cluster_one_ban', function () {
    /* v0.9.34: SysOp-Tor, s. oben. */
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_ban');
    $edge = isset($_POST['edge']) ? sanitize_text_field(wp_unslash($_POST['edge'])) : '';
    if ($edge !== '') {
        cluster_one_set_edge_status($edge, 'banned');
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh&ban=on'));
    exit;
});

add_action('admin_post_cluster_one_unban', function () {
    /* v0.9.34: SysOp-Tor, s. oben. */
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_unban');
    $edge = isset($_POST['edge']) ? sanitize_text_field(wp_unslash($_POST['edge'])) : '';
    cluster_one_edge_unban($edge); // v0.9.40: der eine Schreibweg
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_banned&ban=off'));
    exit;
});

/* v0.9.68 (F6): DIE BEIDEN KNOEPFE AM ANKLOPFEN.
   Dieselbe Bauart wie Bann/Entbann darueber: SysOp-Tor, Nonce, ein Schreibweg
   in kleinrock.php, Weiterleitung mit Merkzettel. Sie stehen hier, weil ohne
   sie der Riegel aus dieser Version die Tuer nicht schliessen, sondern
   zumauern wuerde — eingehende Paarungen kaemen nie mehr herein. */
add_action('admin_post_cluster_one_edge_approve', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_edge_approve');
    $edge = isset($_POST['edge']) ? sanitize_text_field(wp_unslash($_POST['edge'])) : '';
    $ok   = cluster_one_edge_approve($edge);
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh&anklopfen=' . ($ok ? 'ja' : 'weg')));
    exit;
});

add_action('admin_post_cluster_one_edge_reject', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_edge_reject');
    $edge = isset($_POST['edge']) ? sanitize_text_field(wp_unslash($_POST['edge'])) : '';
    $ok   = cluster_one_edge_reject($edge);
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh&anklopfen=' . ($ok ? 'nein' : 'weg')));
    exit;
});

/** v0.9.68: Die Liste der Anklopfenden, ueber der Mesh-Tabelle.
 *
 *  Sie zeigt sich nur, wenn jemand wartet — eine dauerhaft leere Ueberschrift
 *  erzieht dazu, sie zu uebersehen. Und sie sagt ausdruecklich, was ein
 *  bestandener Domain-Beweis heisst und was nicht: dass die Adresse echt ist,
 *  nicht dass der dahinter willkommen ist. Genau diese Verwechslung war F6. */
function cluster_one_render_anklopfen(): void {
    if (!function_exists('cluster_one_get_pending_edges') || !cluster_one_is_sysop()) {
        return;
    }
    $warten = cluster_one_get_pending_edges();
    if (!$warten) {
        return;
    }
    ?>
    <div class="notice notice-info" style="padding:1em 1.2em;max-width:820px;">
        <h2 style="margin-top:0;"><?php esc_html_e('Someone is knocking', 'cluster-one-kleinrock'); ?></h2>
        <p class="description">
            <?php esc_html_e('These nodes paired themselves to you from their side and passed the domain callback. That proves the address is really theirs — it does not say you want them in. Until you say yes they see nothing: not your map, not your profile, not your hello chat.', 'cluster-one-kleinrock'); ?>
        </p>
        <table class="widefat striped" style="margin-top:1em;">
            <tbody>
            <?php foreach ($warten as $w) :
                $edge = (string) ($w['edge'] ?? '');
                $url  = (string) ($w['url'] ?? '');
                ?>
                <tr>
                    <td><strong><?php echo esc_html((string) ($w['name'] ?? 'Peer')); ?></strong><br>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($url); ?></a></td>
                    <td style="width:16em;text-align:right;">
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;">
                            <?php wp_nonce_field('cluster_one_edge_approve'); ?>
                            <input type="hidden" name="action" value="cluster_one_edge_approve">
                            <input type="hidden" name="edge" value="<?php echo esc_attr($edge); ?>">
                            <button type="submit" class="button button-primary"><?php esc_html_e('Let in', 'cluster-one-kleinrock'); ?></button>
                        </form>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;">
                            <?php wp_nonce_field('cluster_one_edge_reject'); ?>
                            <input type="hidden" name="action" value="cluster_one_edge_reject">
                            <input type="hidden" name="edge" value="<?php echo esc_attr($edge); ?>">
                            <button type="submit" class="button"><?php esc_html_e('Send away', 'cluster-one-kleinrock'); ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p class="description">
            <?php esc_html_e('Send away is not a ban: it simply forgets the knock, and they may try again. If you want a verdict that stays, use the ban list.', 'cluster-one-kleinrock'); ?>
        </p>
    </div>
    <?php
}

/** v0.9.40: Die Rückfrage beantworten — Ja, Nein oder Später.
   „Ja" macht hier nichts Eigenes: es ruft denselben cluster_one_edge_unban()
   auf wie der Knopf in der Tabelle. */
add_action('admin_post_cluster_one_antitrotz', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403); // v0.9.34: SysOp-Tor — Bannen und Entbannen ist seine Sache
    }
    check_admin_referer('cluster_one_antitrotz');
    $edge = isset($_POST['edge']) ? sanitize_text_field(wp_unslash($_POST['edge'])) : '';
    $modus = isset($_POST['modus']) ? sanitize_key(wp_unslash($_POST['modus'])) : '';

    /* Der Haken wirkt zu BEIDEN Knöpfen — Christian: „In allen Fällen nicht
       mehr fragen." Er steht neben der Frage, nicht in den Einstellungen,
       weil er genau dann gebraucht wird, wenn die Frage nervt. */
    if (!empty($_POST['alle'])) {
        update_option('cluster_one_antitrotz_off', true, false);
    }

    if ($edge !== '') {
        if ($modus === 'ja') {
            cluster_one_edge_unban($edge);
            wp_safe_redirect(admin_url('admin.php?page=cluster_one_banned&ban=off'));
            exit;
        }
        if ($modus === 'nein') {
            cluster_one_antitrotz_antwort($edge, 'nein');
        } elseif ($modus === 'spaeter') {
            cluster_one_antitrotz_antwort($edge, time() + cluster_one_antitrotz_spaeter());
        }
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_banned&trotz=' . rawurlencode($modus)));
    exit;
});

/** Freund hinzufügen/entfernen (v0.0.54): rein lokal, einseitig, keine
   Nachricht an den Peer — spiegelbildlich zu Ban/Unban. */
add_action('admin_post_cluster_one_friend_add', function () {
    /* v0.9.34: SysOp-Tor, s. oben. */
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_friend_add');
    $edge = isset($_POST['edge']) ? sanitize_text_field(wp_unslash($_POST['edge'])) : '';
    /* v0.7.4: DUNBAR IST JETZT AUCH EINE SPERRE, nicht nur eine Anzeige —
       Christians Entscheidung.

       Die Grenze wird VOR dem Setzen geprueft, und nur beim Hinzufuegen:
       Entfreunden bleibt jederzeit moeglich, sonst saesse man in der Grenze
       fest. Wer schon Freund ist, laeuft nicht erneut gegen die Wand (ein
       Doppelklick darf nichts kaputt machen).

       Die Zahl ist eine Anleihe, kein Naturgesetz: Dunbar schaetzte stabile
       Beziehungen zwischen MENSCHEN, hier werden KNOTEN gezaehlt, hinter denen
       viele stehen koennen. Deshalb sagt die Meldung, WAS zu tun ist, statt nur
       nein — eine Sperre ohne Ausweg ist eine Sackgasse. */
    if ($edge !== '') {
        $karte = cluster_one_get_paired_map();
        $schon = !empty($karte[$edge]['friend']);
        if (!$schon && cluster_one_friend_count() >= CLUSTER_ONE_DUNBAR) {
            wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh&friend=full'));
            exit;
        }
        cluster_one_set_edge_friend($edge, true);
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh&friend=on'));
    exit;
});

add_action('admin_post_cluster_one_friend_remove', function () {
    /* v0.9.34: SysOp-Tor, s. oben. */
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_friend_remove');
    $edge = isset($_POST['edge']) ? sanitize_text_field(wp_unslash($_POST['edge'])) : '';
    if ($edge !== '') {
        cluster_one_set_edge_friend($edge, false);
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_friends&friend=off'));
    exit;
});

if (!defined('CLUSTER_ONE_KNOWN_MAX'))      define('CLUSTER_ONE_KNOWN_MAX', 500);

if (!defined('CLUSTER_ONE_INGEST_MAX'))     define('CLUSTER_ONE_INGEST_MAX', 200);

if (!defined('CLUSTER_ONE_SYNC_MIN_FORCED'))define('CLUSTER_ONE_SYNC_MIN_FORCED', 60);

if (!defined('CLUSTER_ONE_SYNC_MIN_AUTO'))  define('CLUSTER_ONE_SYNC_MIN_AUTO', DAY_IN_SECONDS);


/* --- Speicher: bekannte Zeiger (autoload=no), keyed by normalisierte URL --- */
function cluster_one_known_nodes(): array {
    $a = get_option('cluster_one_known_nodes', []);
    return is_array($a) ? $a : [];
}

function cluster_one_save_known_nodes(array $a): void {
    update_option('cluster_one_known_nodes', $a, false);
}

/** GABEL 0.9.69 (F7): DIE EINMALIGE UMSCHLUESSELUNG.
 *
 *  Eine korrigierte norm_url() allein genuegt nicht: Auf einem laufenden Knoten
 *  stehen die alten Schluessel schon in der Datenbank. Ohne diesen Schritt
 *  laegen alte und neue Schreibweise nebeneinander, und die Karte waere nach
 *  der Reparatur unordentlicher als davor.
 *
 *  DREI VORSICHTEN:
 *  1. Die KARTE wird umgeschluesselt; treffen zwei Schreibweisen zusammen,
 *     gewinnt der Eintrag mit dem juengeren 'seen'.
 *  2. Die KANTEN werden nur GEGLAETTET (Gross/Klein, Port, Schraegstrich) —
 *     der Host wird nie veraendert. Eine Kante ist eine bewiesene Adresse;
 *     sie umzuschreiben hiesse, den Beweis wegzuwerfen.
 *  3. Die Fingerprints sind JE KANTE nach norm_url geschluesselt und nach der
 *     Umschluesselung wertlos. Sie werden verworfen — das kostet genau einen
 *     vollen Transfer je Kante und ist billiger als ein falscher Treffer.
 *
 *  Laeuft genau einmal, gemerkt in einer eigenen Option. */
function cluster_one_migrate_urls_0969(): void {
    if ((string) get_option('cluster_one_url_migration', '') === '0.9.69') {
        return;
    }

    $map = get_option('cluster_one_known_nodes', array());
    if (is_array($map)) {
        $neu = array();
        foreach ($map as $v) {
            if (!is_array($v)) {
                continue;
            }
            $u = cluster_one_norm_url((string) ($v['url'] ?? ''));
            if ($u === '') {
                continue;
            }
            $v['url'] = $u;
            if (!isset($neu[$u]) || (int) ($v['seen'] ?? 0) > (int) ($neu[$u]['seen'] ?? 0)) {
                $neu[$u] = $v;
            }
        }
        update_option('cluster_one_known_nodes', $neu, false);
    }

    $kanten = get_option('cluster_one_paired_peers', array());
    if (is_array($kanten)) {
        $geaendert = false;
        foreach ($kanten as $k => $e) {
            if (!is_array($e)) {
                continue;
            }
            $alt = (string) ($e['url'] ?? '');
            $u   = cluster_one_norm_url($alt);
            // Nur glaetten, nie den Host wechseln.
            if ($u !== '' && $u !== $alt && strtolower($u) === strtolower(untrailingslashit($alt))) {
                $kanten[$k]['url'] = $u;
                $geaendert = true;
            }
        }
        if ($geaendert) {
            update_option('cluster_one_paired_peers', $kanten, false);
        }
    }

    delete_option('cluster_one_peer_fingerprints');
    update_option('cluster_one_url_migration', '0.9.69', false);
}
add_action('admin_init', 'cluster_one_migrate_urls_0969');

/* --- Pro-Peer gemerkter Fingerprint (Transfer-Sparer), autoload=no --- */
function cluster_one_peer_fps(): array {
    $a = get_option('cluster_one_peer_fingerprints', []);
    return is_array($a) ? $a : [];
}

function cluster_one_save_peer_fps(array $a): void {
    update_option('cluster_one_peer_fingerprints', $a, false);
}

/* --- Zeitpunkt des letzten Abgleichs (EIN Wächter für Knopf UND Cron) --- */
function cluster_one_last_sync(): int {
    return (int) get_option('cluster_one_last_sync', 0);
}

function cluster_one_set_last_sync(int $t): void {
    update_option('cluster_one_last_sync', $t, false);
}

/* --- Eigener Zeiger (nur wenn benannt; nie ein Token, nie Profil) --- */
function cluster_one_self_pointer(): ?array {
    if (!cluster_one_has_name()) {
        return null; // namenlos -> nicht ins Netz hineinwerben (kein Mesh ohne Namen)
    }
    return [
        'url'     => cluster_one_self_base_url(),
        'node_id' => cluster_one_node_id(),
        'label_ts' => (int) get_option('cluster_one_label_signal_ts', 0), // v0.1.3
    ];
}

/* --- Die Karte, die dieser Knoten herausgibt: eigener Zeiger + bekannte
   Zeiger. Ausschließlich Zeiger, niemals Token. --- */
function cluster_one_card_pointers(): array {
    $self = cluster_one_self_base_url();
    $out  = [];

    $sp = cluster_one_self_pointer();
    if ($sp !== null) {
        $out[] = $sp;
    }
    foreach (cluster_one_known_nodes() as $n) {
        $u = (string) ($n['url'] ?? '');
        if ($u === '' || cluster_one_norm_url($u) === cluster_one_norm_url($self)) {
            continue;
        }
        $out[] = [
            'url'     => $u,
            'node_id' => (string) ($n['node_id'] ?? ''),
            'label_ts' => (int) ($n['label_ts'] ?? 0), // v0.1.3
        ];
    }
    return $out;
}

/* --- Kurzer Fingerprint der eigenen Karte. Ändert sich, sobald sich der
   Satz oder ein Profil ändert -> der Frager kann den Transfer überspringen. --- */
function cluster_one_card_fingerprint(): string {
    $ptrs = cluster_one_card_pointers();
    $rows = [];
    foreach ($ptrs as $p) {
        $rows[] = implode('|', [
            cluster_one_norm_url((string) $p['url']),
            (string) $p['node_id'],
            (string) ($p['label_ts'] ?? 0), // v0.1.3
        ]);
    }
    sort($rows);
    return substr(hash('sha256', implode("\n", $rows)), 0, 32);
}

/* --- Einen empfangenen Zeiger hart säubern.
   Liest NUR eine Weißliste an Feldern – ein evtl. mitgeschicktes
   'token' wird strukturell ignoriert. REIN LESEND: kein Speicherzugriff,
   kein Schreiben. Wer schreibt, ist cluster_one_merge_pointers(). --- */
function cluster_one_clean_pointer(array $raw, string $via): ?array {
    $url = cluster_one_norm_url((string) ($raw['url'] ?? ''));
    if ($url === '' || stripos($url, 'https://') !== 0) {
        return null; // nur https-Zeiger
    }
    if ($url === cluster_one_norm_url(cluster_one_self_base_url())) {
        return null; // sich selbst nicht aufnehmen
    }
    return [
        'url'      => $url,
        'node_id'  => sanitize_text_field(substr((string) ($raw['node_id'] ?? ''), 0, 100)),
        'label_ts' => (int) ($raw['label_ts'] ?? 0), // v0.1.3
        'seen'     => time(),
        'via'      => cluster_one_norm_url($via),
    ];
}

/** GABEL 0.9.69 (F3): Der Ueberlauf wirft nie eine Kante weg.
 *
 *  Bisher sortierte der Ueberlauf die ganze Karte nach 'seen' und schnitt vorne
 *  ab — auch dann, wenn zu einem Zeiger eine gepaarte Kante bestand. Eine Kante
 *  ist eine Beziehung, die ein Mensch hergestellt hat; sie darf einer Flut
 *  fremder Zeiger nicht zum Opfer fallen.
 *
 *  'seen' bleibt der Sortierschluessel, und das ist Absicht: Es wird LOKAL
 *  gesetzt und reist nie ueber die Leitung, ist also nicht faelschbar. Genau
 *  daran scheiterte der Vorschlag, kuenftig nach einem uebertragenen
 *  Frische-Stempel zu verdraengen — dann bestimmte der Absender, wer fliegt. */
function cluster_one_trim_known(array $map): array {
    $kanten = array();
    foreach (cluster_one_get_paired_map() as $e) {
        $u = cluster_one_norm_url((string) ($e['url'] ?? ''));
        if ($u !== '') {
            $kanten[$u] = true;
        }
    }
    $fest = array();
    $frei = array();
    foreach ($map as $k => $v) {
        if (isset($kanten[$k])) { $fest[$k] = $v; } else { $frei[$k] = $v; }
    }
    $platz = CLUSTER_ONE_KNOWN_MAX - count($fest);
    if ($platz < 0) {
        $platz = 0;
    }
    if (count($frei) > $platz) {
        uasort($frei, function ($a, $b) {
            return (int) ($a['seen'] ?? 0) <=> (int) ($b['seen'] ?? 0);
        });
        $frei = array_slice($frei, count($frei) - $platz, null, true);
    }
    return $fest + $frei;
}

/** GABEL 0.9.69 (F10): Viele Zeiger, EIN Schreibvorgang.
 *
 *  Bis hier rief radar_fetch_peer() cluster_one_merge_pointer() je Zeiger, und
 *  jeder dieser Aufrufe las UND schrieb die ganze Option. Bei einer vollen
 *  Antwort waren das 200 update_option() fuer EINE Kante; ueber sechs Kanten
 *  rund 1200 UPDATE-Anweisungen auf eine einzige wp_options-Zeile in einem
 *  einzigen HTTP-Request. Auf Shared Hosting mit max_execution_time = 30 ist
 *  das kein Grenzfall, sondern der Regelfall.
 *
 *  Einmal lesen, im Speicher mischen, einmal schreiben. Aus 200 mach 1.
 *
 *  GABEL 0.9.69 (F8): UND DIE KARTE KENNT JETZT DEN BANN. cluster_one_is_url_banned()
 *  hatte bisher genau zwei Aufrufer, /pair und pair_with() — der Weg in die
 *  Karte fragte nie. Ein gebannter Knoten stand also weiter in der Liste, mit
 *  einem Test-Knopf daneben, kam nach jedem Abgleich zurueck, und der Bannende
 *  bewarb ihn ueber seine eigene Karte im ganzen Netz weiter.
 *  Die Bannliste wird EINMAL je Durchgang gebaut, nicht je Zeiger. */
function cluster_one_merge_pointers(array $ptrs, string $via): int {
    $map = cluster_one_known_nodes();

    $gebannt = array();
    foreach (cluster_one_get_paired_map() as $e) {
        if ((string) ($e['status'] ?? '') === 'banned') {
            $k = cluster_one_bann_key((string) ($e['url'] ?? ''));
            if ($k !== '') {
                $gebannt[$k] = true;
            }
        }
    }

    $neu = 0;
    foreach ($ptrs as $raw) {
        if (!is_array($raw)) {
            continue;
        }
        $p = cluster_one_clean_pointer($raw, $via);
        if ($p === null || isset($gebannt[cluster_one_bann_key($p['url'])])) {
            continue;
        }
        if (!isset($map[$p['url']])) {
            $neu++;
        }
        $map[$p['url']] = $p;
    }

    if (count($map) > CLUSTER_ONE_KNOWN_MAX) {
        $map = cluster_one_trim_known($map);
    }
    cluster_one_save_known_nodes($map);
    return $neu;
}

/* --- Einzelform, unveraendert im Verhalten. store_paired() ruft sie. --- */
function cluster_one_merge_pointer(array $raw, string $via): bool {
    return cluster_one_merge_pointers(array($raw), $via) > 0;
}

/* --- Einen einzelnen Peer abgleichen (Versionsstand -> ggf. volle Liste).
   $peer: ['name','url','token']. Gibt Zusammenfassung zurück. --- */
function cluster_one_radar_fetch_peer(array $peer): array {
    $url   = (string) ($peer['url'] ?? '');
    $token = (string) ($peer['token'] ?? '');
    if ($url === '' || $token === '') {
        return ['ok' => false, 'discovered' => 0];
    }

    $fps    = cluster_one_peer_fps();
    $key    = cluster_one_norm_url($url);
    $cached = isset($fps[$key]) ? (string) $fps[$key] : '';

    $endpoint = trailingslashit($url) . 'wp-json/cluster-one/v1/radar';
    if ($cached !== '') {
        $endpoint = add_query_arg('fingerprint', rawurlencode($cached), $endpoint);
    }

    // wp_safe_remote_get: SSRF-Schutz (Defense-in-depth), obwohl es eine
    // bereits gepaarte Kante ist.
    $resp = wp_safe_remote_get($endpoint, [
        'timeout'     => 5,
        'redirection' => 1,
        'sslverify'   => true,
        'headers'     => ['X-Cluster-Token' => $token],
    ]);
    if (is_wp_error($resp) || wp_remote_retrieve_response_code($resp) !== 200) {
        return ['ok' => false, 'discovered' => 0];
    }
    $data = json_decode(wp_remote_retrieve_body($resp), true);
    if (!is_array($data)) {
        return ['ok' => false, 'discovered' => 0];
    }

    // Unverändert? Dann gab es keinen Transfer – Fingerprint behalten.
    if (!empty($data['unchanged'])) {
        return ['ok' => true, 'discovered' => 0, 'unchanged' => true];
    }

    $discovered = 0;
    if (isset($data['nodes']) && is_array($data['nodes'])) {
        // GABEL 0.9.69 (F10): Die Flut wird weiterhin gekappt — aber gemischt
        // wird in einem Zug, mit genau einem Schreibvorgang am Ende.
        $discovered = cluster_one_merge_pointers(
            array_slice(array_values($data['nodes']), 0, CLUSTER_ONE_INGEST_MAX),
            $url
        );
    }

    // Neuen Fingerprint des Peers merken (Transfer-Sparer fürs nächste Mal).
    if (!empty($data['fingerprint'])) {
        $fps[$key] = sanitize_text_field((string) $data['fingerprint']);
        cluster_one_save_peer_fps($fps);
    }

    return ['ok' => true, 'discovered' => $discovered];
}

/* --- Voller Abgleich über alle AKTIVEN Kanten. EIN Wächter (last_sync)
   regelt Knopf (forced, 60 s) UND Cron (auto, 1 Tag). --- */
function cluster_one_radar_sync(bool $forced = false): array {
    $now  = time();
    $age  = $now - cluster_one_last_sync();
    $min  = $forced ? CLUSTER_ONE_SYNC_MIN_FORCED : CLUSTER_ONE_SYNC_MIN_AUTO;

    if ($age < $min) {
        return ['status' => 'throttled', 'age' => $age, 'min' => $min, 'asked' => 0, 'discovered' => 0];
    }

    $asked = 0;
    $disc  = 0;
    foreach (cluster_one_get_peers() as $peer) {
        $asked++;
        $r = cluster_one_radar_fetch_peer($peer);
        if (!empty($r['ok'])) {
            $disc += (int) ($r['discovered'] ?? 0);
        }
        /* GABEL 0.9.69 (F10): FORTSCHRITT WIRD SOFORT FESTGEHALTEN.
           Bis hier stand set_last_sync() erst HINTER der Schleife. Bricht der
           Request ab — Zeitueberschreitung, fataler Fehler, geschlossener
           Browser —, wurde der Zeitpunkt nie gesetzt: Der naechste Lauf sah
           age = unendlich, fing von vorne an und brach wieder ab. Ein Knoten
           mit vielen Kanten konnte so dauerhaft abgleichen, ohne je Fortschritt
           festzuhalten, und jeder Besucher zahlte die volle Wartezeit.
           Jetzt kostet ein Abbruch hoechstens die restlichen Kanten. */
        cluster_one_set_last_sync($now);
    }

    cluster_one_set_last_sync($now);
    return ['status' => 'ok', 'age' => 0, 'asked' => $asked, 'discovered' => $disc];
}

/* --- Offene-aber-token-gesicherte Route: /radar ---
   Gibt die Karte (nur Zeiger) heraus, aber NUR an eine gültige aktive
   Kante. Versionsstand: stimmt der mitgeschickte Fingerprint mit unserem
   überein -> winzige Antwort 'unchanged'. --- */
/* ======================= DER STICHTAG, EIN ORT ============================
   v1.0.16 (AUFTRAG 23.09.2026, Stichtag 22.12.2026)

   EIN DATUM UND KEINE DAUER, und der Grund ist Mammuts: eine Frist von
   neunzig Tagen hat drei moegliche Anfaenge (Einbau, Auslieferung, erste
   nackte Anfrage) und laeuft auf jedem Knoten anders ab. Ein Datum hat
   eine Antwort, es steht im Code, und man kann es lesen, ohne zu rechnen.

   DIE ZAHL STEHT EINMAL. Wer sie aendert, aendert sie hier — und nicht an
   zwei Orten, die sich spaeter widersprechen. Das ist woertlich die Form
   von `cluster_one_edge_deckel()` (:2960) und derselbe Grund.

   VERGLICHEN WIRD ALS DATUM, NICHT ALS ZEITPUNKT. Zwei ISO-Daten
   vergleichen sich als Zeichenketten richtig, und ein Stichtag ist ein
   Tag: an ihm gilt er schon (>=), nicht erst danach.

   DIE ZONE IST UTC, UND SIE STEHT DA (NACHTRAG §2, 23.09.).
   Fassung 1 dieses Blocks nahm die ORTSZEIT und nannte die Unschaerfe nur.
   Mammuts Einwand trifft, und der schaerfere Grund ist nicht die
   Unschaerfe, sondern WER sie bewegt:

       `current_time()` ohne zweites Argument folgt der
       WordPress-Einstellung `timezone_string`. Die steht auf einer
       Seite, die ein Admin oeffnen kann — ein Klick dort haette den
       Stichtag dieses Knotens um Stunden verschoben.

           Eine Grenze, die eine Einstellungsseite verschieben kann,
           ist keine Grenze, sondern eine Voreinstellung.

   UND SIE GILT NICHT FUER MICH, SONDERN FUER DEN, DER FRAGT. Der Stichtag
   entscheidet, was dieser Knoten einem FREMDEN antwortet. Wessen Kalender
   soll das sein — meiner oder seiner? Keiner von beiden.

       Eine Grenze zwischen zwei Knoten gehoert der Leitung und nicht
       einem der beiden Ufer.

   Darum `current_time('Y-m-d', true)`: dieselbe EINE Stelle, dieselbe
   stellbare Naht, und die Zone haengt an nichts, was jemand verstellen
   kann. Der Rueckfall `gmdate()` ist dieselbe Zone — beide Wege eines
   Sinnes, nicht zwei.
       Ein Tag ist die Einheit, in der ein Mensch eine Frist liest. Eine
       Zone ist die Einheit, in der zwei Knoten sie gleich lesen.

   DIE ZEIT IST STELLBAR, OHNE EINEN HAKEN FUER DEN ZEUGEN: gemessen wird
   ueber `current_time()`, und ein Zeuge stellt diese Funktion. Ein
   Pruefhaken im Auslieferungscode waere ein zweiter Weg zu derselben
   Wahrheit, den nur der Zeuge geht.
       Ein Schalter, den nur die Pruefung umlegt, wird in der
       Auslieferung nie gemessen. */
/* AUF DATEIEBENE, NICHT IM rest_api_init-RUMPF. Fassung 1 dieses Blocks
   stand INNERHALB der Anmeldung — dann hiesse „deklariert", dass eine
   REST-Anfrage stattgefunden hat, und die Treffen-Seite im Admin haette
   den Stichtag nicht gekannt.
       Eine Funktion in einem Rumpf, der nur manchmal laeuft, gibt es
       nur manchmal. */
function cluster_one_kleinrock_stichtag(): string {
    return '2026-12-22';
}

/** Heute, als ISO-Datum in UTC. DIE EINE NAHT: hier und nur hier fragt das
 *  Haus nach der Zeit, wenn es um den Stichtag geht — `time()` steht in
 *  diesem Pfad nirgends, denn `time()` laesst sich nicht stellen.
 *
 *      R-PRUEFUNG-2 (Mammut): Eine Grenze, die an der echten Uhr haengt,
 *      ist bis zu ihrem Tag ungeprueft — und an ihrem Tag ist es zu
 *      spaet, sie zu pruefen.
 *
 *  Das zweite Argument `true` ist die ZONE und nicht eine Feinheit: es
 *  nimmt die Uhr von der WordPress-Einstellung weg. Siehe den Block oben.
 */
function cluster_one_kleinrock_heute(): string {
    return function_exists('current_time')
        ? (string) current_time('Y-m-d', true)
        : gmdate('Y-m-d');
}

/** Ist der Stichtag erreicht? AM Tag selbst: ja. */
function cluster_one_kleinrock_stichtag_erreicht(): bool {
    return cluster_one_kleinrock_heute() >= cluster_one_kleinrock_stichtag();
}

/* ================ WER SEINE ZEILE SCHON ABHOLT — UND WER NICHT ============
   v1.0.16, Punkt 3 des Auftrags, UND ER IST ANDERS GEBAUT, ALS ER BESTELLT
   WAR. Der Auftrag sagt: „die URL des Fragenden, wann zuletzt, und das
   Datum." Die URL des Fragenden HABE ICH NICHT — gemessen: eine nackte
   GET-Anfrage auf `/meetings` traegt keinen Absender. `cluster_one_pull_rsvp()`
   sendet keinen, und ein alter Knoten erst recht nicht. Was dastuende, waere
   eine IP-Adresse, und das ist etwas anderes als eine Knotenadresse: sie
   gehoert einem Menschen, nicht einem Knoten, und sie waere raterei.

       Wer eine Auskunft nicht hat, baut sie nicht nach. Ein erfundener
       Absender ist schlimmer als eine leere Spalte, weil er wie eine
       Messung aussieht.

   ALSO ANDERSHERUM, UND MIT ADRESSEN, DIE ICH WIRKLICH HABE: ich merke mir,
   WER SICH GENANNT HAT. Eine Anfrage MIT `for=` nennt den Gastgeber, und der
   Gastgeber steht ohnehin in meiner eigenen `attending`-Liste. Daraus faellt
   beides, was der Auftrag wollte:

       vor dem Stichtag   wen es treffen WIRD — jeder Wirt meiner Liste,
                          der sich noch nie genannt hat
       nach dem Stichtag  wer stumm geworden IST — dieselben, und jetzt
                          merkt er es

   Das ist dieselbe Frage, aus der Richtung gestellt, in der die Daten
   stehen. Es ist kein Zaehler: es steht je Wirt ein Datum, sonst nichts.

   DER SCHLUESSELRAUM IST MEINER. Vermerkt wird nur eine Adresse, die in
   meinen eigenen angenommenen Treffen vorkommt — ein Fremder kann den
   Vermerk also nicht mit erfundenen `for=` fuellen, und es braucht keinen
   Deckel gegen eine Flut, den jemand spaeter fuer eine Messung haelt.
       Ein oeffentlicher Weg, der schreibt, braucht eine Grenze, die
       nicht er selbst zieht.

   UND HOECHSTENS EIN SCHREIBEN JE WIRT UND TAG. Das Datum ist die Einheit,
   in der die Auskunft gelesen wird; eine Uhrzeit mehr oder weniger sagt
   niemandem etwas und kostet je Anfrage einen Schreibvorgang. */
function cluster_one_kleinrock_abholung_merken(string $c1_wirt): void {
    if ($c1_wirt === '') {
        return;
    }
    $c1_meine = array();
    foreach (cluster_one_get_gregor_events() as $c1_ev) {
        if (!empty($c1_ev['archimedes']['host'])) {
            $c1_meine[cluster_one_norm_url((string) $c1_ev['archimedes']['host'])] = true;
        }
    }
    if (!isset($c1_meine[$c1_wirt])) {
        return; // keine Adresse aus meiner Liste — kein Vermerk
    }
    $c1_heute = cluster_one_kleinrock_heute();
    $c1_ab    = get_option('cluster_one_meetings_abholung', array());
    if (!is_array($c1_ab)) {
        $c1_ab = array();
    }
    if ((string) ($c1_ab[$c1_wirt] ?? '') === $c1_heute) {
        return; // heute schon vermerkt
    }
    /* Was nicht mehr in meiner Liste steht, faellt beim naechsten Schreiben
       heraus. Ein Vermerk ueber ein Treffen, das es nicht mehr gibt, ist kein
       Rest, sondern Muell. */
    foreach (array_keys($c1_ab) as $c1_alt) {
        if (!isset($c1_meine[$c1_alt])) {
            unset($c1_ab[$c1_alt]);
        }
    }
    $c1_ab[$c1_wirt] = $c1_heute;
    update_option('cluster_one_meetings_abholung', $c1_ab, false);
}

/** Was an diesem Knoten ablaeuft. EINE Liste, EIN Bauer — das Datum kommt
 *  aus cluster_one_kleinrock_stichtag() und steht hier nicht ein zweites Mal.
 *
 *  `was` ist ein NAME und keine Prosa: ein Leser soll danach vergleichen
 *  koennen, ohne einen Satz zu zerlegen. `tu` sagt, was statt dessen zu tun
 *  ist — eine Ankuendigung ohne Ausweg ist eine Drohung.
 */
function cluster_one_kleinrock_deprecations(): array {
    return array(
        array(
            'was' => 'meetings.attending',
            'ab'  => cluster_one_kleinrock_stichtag(),
            /* DIE ZONE REIST MIT. Ein Datum ohne Zone ist auf zwei Knoten
               zwei Zeitpunkte, und der Leser soll nicht raten muessen,
               welchen ich meine. */
            'zone' => 'UTC',
            'tu'  => 'GET /wp-json/cluster-one/v1/meetings?for=<deine-knoten-url>',
        ),
    );
}

/** Wirte aus meiner `attending`-Liste, die sich noch nie genannt haben.
 *  EINE Wahrheit, zwei Leser: dieser Aufruf und der Schirm. */
function cluster_one_kleinrock_stumme_wirte(): array {
    $c1_ab  = get_option('cluster_one_meetings_abholung', array());
    if (!is_array($c1_ab)) {
        $c1_ab = array();
    }
    $c1_aus = array();
    foreach (cluster_one_get_gregor_events() as $c1_ev) {
        if (empty($c1_ev['archimedes']['host'])) {
            continue;
        }
        $c1_w = cluster_one_norm_url((string) $c1_ev['archimedes']['host']);
        if ($c1_w !== '' && !isset($c1_ab[$c1_w])) {
            $c1_aus[$c1_w] = true;
        }
    }
    return array_keys($c1_aus);
}

add_action('rest_api_init', function () {
    // v0.0.98: DIE ÖFFENTLICHE LABELS-ROUTE (Beschluss SysOp, bestätigt in
    // voller Tragweite: alle Knoten dürfen lesen, auch unbefreundete —
    // Labels sind das Kennenlern-Schaufenster, ein Schaufenster mit
    // Türsteher wäre absurd). Drei Patchday-Bauvorschriften eingehalten:
    // (1) KEIN Batching, keine Parameter — null Injektionsfläche;
    // (2) permission_callback bewusst öffentlich (Lese-Schaufenster),
    //     alles andere bleibt kanten-authentifiziert;
    // (3) jede Ausgabe sanitisiert, gedeckelt (30 Labels, 50 Beiträge).
    // Serviert wird NUR: published + nicht archiviert. OHNE Autorenfeld —
    // nach draußen spricht der Knoten als Knoten, nie seine Menschen
    // (Artikel 6). Projekt-Titel und Internes reisen nicht.
    /* v0.1.8: why — die Selbstauskunft des Knotens für Maschinen. /labels sagt
       Fremden, was dieser Knoten TUT; nichts sagte ihnen bisher, was er IST.
       Öffentlich und ohne Token, aus demselben Grund, aus dem der Text auf dem
       Knoten angezeigt wird: eine Hausordnung, die nur eine Partei lesen kann,
       ist eine Anweisung. Roh-Markdown, weil Maschinen keine Formatierung
       brauchen. Gelesen, nicht gespiegelt. */
    register_rest_route('cluster-one/v1', '/why', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function (WP_REST_Request $request) {
            $t = function_exists('cluster_one_why_text') ? cluster_one_why_text() : '';
            if ($t === '') {
                return new WP_REST_Response(null, 404);
            }
            if ((string) $request->get_param('part') === 'briefing') {
                $b = function_exists('cluster_one_why_briefing') ? cluster_one_why_briefing() : '';
                return rest_ensure_response(array('node' => sanitize_text_field(get_bloginfo('name')), 'part' => 'briefing', 'text' => $b));
            }
            return rest_ensure_response(array('node' => sanitize_text_field(get_bloginfo('name')), 'part' => 'full', 'text' => $t));
        },
    ]);

    /* v0.3.20: Die Tuerklingel fuer Zusagen. Oeffentlich und ohne Token — eine
       Zusage darf keine Freundschaft voraussetzen; die Entscheidung, wer wessen
       Freund ist, soll aus Freiheit fallen und nicht aus Zwang.

       Sie traegt KEINE Zahl. Sie sagt nur: "schau bei mir nach". Der Gastgeber
       holt die Zahl von der oeffentlichen Route des Gastes und glaubt sie nicht
       auf Zuruf — sonst koennte jeder Fremde behaupten, mit fuenfhundert Leuten
       zu kommen. Pull statt Push, auch auf dem Rueckweg. */
    register_rest_route('cluster-one/v1', '/rsvp', [
        'methods'             => 'POST',
        'permission_callback' => '__return_true',
        'callback'            => function (WP_REST_Request $request) {
            $von = esc_url_raw((string) $request->get_param('url'));
            if ($von === '' || cluster_one_is_own_url($von)) {
                return new WP_REST_Response(array('ok' => false), 400);
            }
            $n = cluster_one_pull_rsvp($von);
            return rest_ensure_response(array('ok' => true, 'taken' => $n));
        },
    ]);

    /* ===================== DIE TREFFEN-ROUTE, OEFFENTLICH =====================
       v0.3.15: Treffen reisen auf eigenem Weg. Oeffentlich wie /labels und aus
       demselben Grund: Wer entscheiden soll, ob er hinkommt, braucht Ort und
       Zeit — nicht jeder besitzt einen Privatjet.

       v1.0.15 (23.09.2026) — DIESE NOTIZ WAR EINE ZUSAGE, DIE DER CODE
       UEBERHOLT HAT, und sie stand zwei Routen zu hoch: ueber `/rsvp`, nicht
       ueber der Route, von der sie handelt. Sie sagte

           „Es reist NUR, was der SysOp ausdruecklich geoeffnet hat
            (open_nodes) … keine Teilnehmerdaten."

       Beides ist fuer den ERSTEN Teil wahr und fuer `attending` falsch; der
       zweite Teil kam in v0.3.20 und die Notiz wurde nicht nachgezogen.
       Gefunden hat es Mammuts Fremdenlauf, von aussen, am laufenden Knoten.

           Eine Zusage, die der Code ueberholt hat, ist schlimmer als gar
           keine: die fehlende laesst nachsehen, die falsche nicht.
                                                             (R-ZUSAGE-1)

           Und eine Notiz, die nicht an ihrer Sache steht, veraltet
           unbemerkt — wer die Route aendert, liest sie nicht mit.

       ------------------------------------------------------------------
       WAS DIESE ROUTE EINEM FREMDEN WIRKLICH SAGT, in drei Feldern:

       `meetings`   MEINE Treffen, und hier stimmt der alte Satz: der Riegel
                    ist `cluster_one_archimedes_is_open()`, also `open_nodes`
                    plus fertig — ein ausdruecklicher Akt. Hoechstens 30.

       `attending`  WOHIN ICH ZUGESAGT HABE. Kein open-Riegel; der Test ist
                    `empty($ev['archimedes']['host'])`, also bloss „gibt es
                    das ueberhaupt". Und `count` IST eine Teilnehmerzahl —
                    „wir kommen mit N Personen".
                    UND DRITTENS, was der Fremdenlauf nicht sehen konnte,
                    weil dort genau ein Eintrag stand: HIER GIBT ES KEINE
                    OBERGRENZE. Der erste Teil bricht bei 30 ab, dieser
                    nicht. Ein Knoten mit zweihundert angenommenen Treffen
                    veroeffentlicht zweihundert Beziehungen in einer Antwort.

       `sysop`      ein Name. `cluster_one_sysop_label()` steht im BODEN und
                    faellt ohne gesetzten Nickname auf den Anzeigenamen des
                    Hauptadministrators zurueck — gemeldet, nicht gebaut,
                    siehe die Antwort an Mammut vom 23.09.

       WAS DAS IST UND WAS ES NICHT IST: es ist keine Luecke im Riegel.
       Es ist gebaut, mit Grund, und der Grund steht unten bei `attending`.
       Aber der Grund erklaert, warum der GASTGEBER die Zahl holen darf —
       nicht, warum JEDER sie holen darf.

           Pull statt Push loest, WER die Zahl behauptet.
           Es loest nicht, WER sie lesen darf.                  (Mammut)

           Ein Knoten, der sagt, wo er hinfaehrt, hat mehr gesagt als
           einer, der sagt, wen er einlaedt.                    (Mammut)

       ------------------------------------------------------------------
       WARUM HIER TROTZDEM KEIN RIEGEL STEHT, und es ist eine Messung und
       keine Bequemlichkeit:

       DER LESER WIRFT SCHON WEG, WAS ER NICHT BRAUCHT.
       `cluster_one_pull_rsvp()` (:4964) verwirft jeden `attending`-Eintrag,
       dessen `host` nicht dieser Knoten ist — der Gastgeber will genau EINE
       Zeile und bekommt alle.

           Wer am Ziel wegwirft, was er nicht braucht, hat es trotzdem
           bekommen.

       Der billige Riegel waere also, dieselbe Bedingung eine Station
       frueher zu stellen: `/meetings?for=<gastgeber-url>` gibt nur den
       passenden Eintrag, und wer die Adresse nicht schon kennt, erfaehrt
       nichts — kein Token noetig, denn wer den Gastgeber benennen kann,
       weiss die Beziehung bereits.

       UND GENAU DAS GEHT HEUTE NICHT OHNE SCHADEN, aus einer Zahl, die
       Mammut am selben Abend gemessen hat: im Feld steht 0.2.4, zweiund-
       zwanzig Fassungen alt. Ein Gastgeber dieser Fassung fragt `/meetings`
       nackt. Wuerde `attending` ohne `for=` leer, hoerte fuer ihn die
       Tuerklingel auf zu laeuten — ohne Meldung, ohne Fehler.

           Ein Riegel, der die alte Gegenseite still verstummen laesst,
           ist kein Riegel, sondern ein Ausfall mit gutem Gewissen.

       ALSO IST ES EINE RUNDE UND KEINE ZEILE, und Mammut hat damit recht
       behalten — nur liegt der Grund nicht im Code, sondern im Feld.
       Was ich hier tue, ist das, was heute stimmt: die Notiz sagt jetzt,
       was reist. Ob der Riegel kommt und wann die nackte Antwort schmaler
       wird, ist Christians Entscheidung und nicht meine.
       ====================================================================== */
    register_rest_route('cluster-one/v1', '/meetings', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function (WP_REST_Request $request) {
            /* v1.0.16: `for=` — WER FRAGT FUER WEN. Normalisiert wie jede
               fremde Adresse in diesem Haus; ein unlesbarer Wert ist kein
               `for=`, und das ist die sichere Richtung: er faellt auf die
               nackte Anfrage zurueck und nicht auf „passt zu allem". */
            $c1_fuer = cluster_one_norm_url((string) $request->get_param('for'));
            $out = array();
            foreach (cluster_one_get_gregor_events() as $ev) {
                if (!cluster_one_archimedes_is_open($ev) || !empty($ev['archimedes'])) {
                    continue; // nur eigene, ausdruecklich geoeffnete Treffen
                }
                $out[] = array(
                    'id'     => sanitize_text_field((string) ($ev['id'] ?? '')),
                    'name'   => sanitize_text_field((string) ($ev['name'] ?? '')),
                    'date'   => sanitize_text_field((string) ($ev['scheduling']['locked_date'] ?? '')),
                    'time'   => sanitize_text_field((string) ($ev['time'] ?? '')),
                    'tz'     => sanitize_text_field((string) ($ev['tz'] ?? '')),
                    'place'  => sanitize_text_field((string) ($ev['place'] ?? '')),
                    /* v1.0.3: DIE BESCHREIBUNG REIST MIT. Bis 1.0.2 schickte diese
                       Route sieben Felder, und ein fremder SysOp sah „Sommerfest,
                       27.08., Hamburg" — er sollte daraus entscheiden, ob er die
                       Anreise auf sich nimmt. Gewaschen wird auf BEIDEN Seiten der
                       Leitung: hier beim Senden, drueben beim Lesen (P0). */
                    'beschreibung' => cluster_one_sanitize_message((string) ($ev['beschreibung'] ?? '')),
                    'opened' => (int) ($ev['open_nodes'] ?? 0),
                );
                if (count($out) >= 30) {
                    break;
                }
            }
            /* v0.3.20: Was WIR angenommen haben und mit wie vielen. Damit wird
               die Zusage zur Tuerklingel: Der Gast klingelt nur, der Gastgeber
               HOLT sich die Zahl hier ab und glaubt sie nicht auf Zuruf. Das ist
               why.md 4.3 — Pull statt Push — angewandt auf den Rueckweg.

               v1.0.15: DIESE SCHLEIFE HAT KEINEN open-RIEGEL UND KEINE
               OBERGRENZE, und beides ist Absicht gewesen und steht jetzt
               oben im Kopf dieser Route. Wer hier etwas aendert, liest den
               Kopf — er nennt, was ein Fremder von diesen drei Zeilen
               erfaehrt und warum der Riegel eine Runde und keine Zeile ist. */
            $mit = array();
            /* v1.0.16 — DER STICHTAG WIRKT HIER, und nur hier.
               VOR dem Stichtag faehrt die Liste wie seit 0.3.20: voll, an
               jeden. AB dem Stichtag reist nur noch, wonach gefragt wurde.
               Ein Gastgeber, der sich nennt, bekommt seine eine Zeile — und
               genau die wollte `cluster_one_pull_rsvp()` schon immer.
                   Wer am Ziel wegwirft, was er nicht braucht, hat es
                   trotzdem bekommen. */
            $c1_stumm = ($c1_fuer === '' && cluster_one_kleinrock_stichtag_erreicht());
            foreach (cluster_one_get_gregor_events() as $ev) {
                if (empty($ev['archimedes']['host'])) {
                    continue;
                }
                $c1_wirt = cluster_one_norm_url((string) $ev['archimedes']['host']);
                if ($c1_fuer !== '' && $c1_wirt !== $c1_fuer) {
                    continue; // nicht seine Zeile
                }
                if ($c1_stumm) {
                    continue; // ab dem Stichtag: keine nackte Auskunft
                }
                $d = (string) ($ev['scheduling']['locked_date'] ?? '');
                $n = 0;
                foreach ((array) ($ev['scheduling']['markers'] ?? array()) as $m) {
                    if (cluster_one_gregor_marker_date($m) === $d) {
                        $n += 1 + cluster_one_gregor_marker_plus($m);
                    }
                }
                $mit[] = array(
                    'host'  => sanitize_text_field((string) $ev['archimedes']['host']),
                    'id'    => sanitize_text_field((string) ($ev['archimedes']['label'] ?? '')),
                    'count' => (int) $n,
                );
            }
            /* v1.0.16 — WER SICH GENANNT HAT, WIRD VERMERKT. Nur eine Adresse,
               die in MEINER eigenen Liste steht: ein Fremder kann den Vermerk
               also nicht fuellen, und der Schluesselraum ist durch meine
               eigenen Treffen begrenzt. Die Begruendung steht bei
               cluster_one_kleinrock_abholung_merken(). */
            if ($c1_fuer !== '') {
                cluster_one_kleinrock_abholung_merken($c1_fuer);
            }
            return rest_ensure_response(array(
                'node'      => sanitize_text_field(get_bloginfo('name')),
                'sysop'     => sanitize_text_field(cluster_one_sysop_label()),
                'url'       => home_url('/'),
                'meetings'  => $out,
                'attending' => $mit,
            ));
        },
    ]);

    register_rest_route('cluster-one/v1', '/labels', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function () {
            $threads = cluster_one_get_forum_threads();
            $out     = array();
            foreach ($threads as $tid => $t) {
                if (!function_exists('cluster_one_thread_is_label') || !cluster_one_thread_is_label($t) || (string) ($t['label_status'] ?? 'draft') !== 'published' || cluster_one_container_is_archived($t)) {
                    continue;
                }
                $posts = array();
                foreach (array_slice(cluster_one_get_forum_posts((string) $tid), -50) as $p) {
                    if (!empty($p['deleted_by_admin']) || !empty($p['system'])) {
                        continue;
                    }
                    $posts[] = array(
                        'body' => sanitize_textarea_field((string) ($p['body'] ?? '')),
                        'ts'   => (int) ($p['ts'] ?? 0),
                    );
                }
                $entry = array(
                    'id'        => (string) $tid,
                    'title'     => sanitize_text_field((string) ($t['title'] ?? '')),
                    'published' => (int) ($t['label_published'] ?? 0),
                    'posts'     => $posts,
                );
                // v0.0.98 (für Archimedes 0.0.100): trägt das PROJEKT des
                // Labels ein Event mit Fels-Datum, reist das Datum mit —
                // nur das Datum, keine Namen, keine Marker.
                $c1_pt = (string) ($t['label_for'] ?? '');
                /* v0.3.9: Der Name des PROJEKTS reist mit — nicht sein Inhalt. Ein
                   Label ist das Gesicht eines Projekts; wie es heisst, gehoert zu
                   diesem Gesicht. In 0.3.8 stand diese Zuweisung VOR
                   "$entry = array(...)" und wurde von der naechsten Zeile
                   weggeworfen — deshalb blieb drueben der Strich stehen. */
                if ($c1_pt !== '' && isset($threads[$c1_pt])) {
                    $entry['project'] = sanitize_text_field((string) ($threads[$c1_pt]['title'] ?? ''));
                }
                /* v0.3.15: Die Termindaten sind hier RAUS. Ein Treffen reiste
                   bisher als Anhaengsel eines veroeffentlichten Labels — wer
                   ein Jahrestreffen ausrichten wollte, musste also ein Projekt
                   nach aussen zeigen, das damit gar nichts zu tun hat. Das war
                   eine gegenwaertige Einschraenkung, keine Zukunftsfrage.
                   Treffen haben jetzt ihre eigene Route: cluster-one/v1/meetings. */
                $out[] = $entry;
                if (count($out) >= 30) {
                    break;
                }
            }
            // v0.1.3: label_ts reist mit, damit der Ziehende denselben Zeitstempel merken kann, den der Ping meldet (eine Uhr, kein Versatz).
            return rest_ensure_response(array('node' => sanitize_text_field(get_bloginfo('name')), 'sysop' => sanitize_text_field(cluster_one_sysop_label()), 'label_ts' => (int) get_option('cluster_one_label_signal_ts', 0), 'labels' => $out)); // v0.3.6: der Mensch hinter dem Knoten reist mit
        },
    ]);

    register_rest_route('cluster-one/v1', '/radar', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function (WP_REST_Request $request) {

            $sent = (string) $request->get_header('X-Cluster-Token');
            if ($sent === '') {
                return new WP_REST_Response(null, 403);
            }

            // Nur aktive Kanten dürfen die Karte ziehen.
            $active = false;
            foreach (cluster_one_get_peers() as $p) {
                if ($p['token'] !== '' && hash_equals($p['token'], $sent)) {
                    $active = true;
                    break;
                }
            }
            if (!$active) {
                $edge = cluster_one_find_edge_by_token($sent);
                if ($edge && ($edge['status'] ?? '') === 'banned') {
                    return new WP_REST_Response([
                        'cluster_one_status' => 'disconnected',
                        'message'            => __('This edge was banned by the operator of this node.', 'cluster-one-kleinrock'),
                    ], 403);
                }
                /* GABEL 0.9.69 (F14): DIE DRITTE AUSKUNFT.
                   Seit 0.9.68 ist eine von aussen angestossene Kante 'pending'.
                   Sie faellt hier bis jetzt in das nackte 403, und drueben
                   uebersetzt cluster_one_ping() das zu "Rejected (token?)" —
                   bei einer kerngesunden, gerade eben angelegten Kante. Der
                   andere SysOp prueft dann ein Token, das richtig ist.
                   Nach der Logik von 0.2.6 ("'getrennt' war eine Beschoenigung")
                   bekommt der Wartende eine eigene, wahre Antwort. Sie verraet
                   nichts: dass hier jemand auf eine Entscheidung wartet, weiss
                   der Anklopfende ohnehin — er hat ja angeklopft. */
                if ($edge && ($edge['status'] ?? '') === 'pending') {
                    return new WP_REST_Response([
                        'cluster_one_status' => 'pending',
                        'message'            => __('This node is waiting for its operator to decide whether to let you in.', 'cluster-one-kleinrock'),
                    ], 403);
                }
                return new WP_REST_Response(null, 403);
            }

            $fp    = cluster_one_card_fingerprint();
            $asked = preg_replace('/[^a-f0-9]/i', '', (string) $request->get_param('fingerprint'));
            $asked = substr($asked, 0, 64);

            if ($asked !== '' && hash_equals($fp, $asked)) {
                return new WP_REST_Response(['fingerprint' => $fp, 'unchanged' => true], 200);
            }

            return new WP_REST_Response([
                'fingerprint' => $fp,
                'nodes'       => cluster_one_card_pointers(),
            ], 200);
        },
    ]);
});

/* --- Backend-Knopf: „Jetzt aktualisieren" (forced) --- */
add_action('admin_post_cluster_one_refresh', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_refresh');

    $r = cluster_one_radar_sync(true);

    $args = ['page' => 'cluster_one_mesh'];
    if ($r['status'] === 'throttled') {
        $args['radar'] = 'wait';
    } else {
        $args['radar'] = 'ok';
        $args['found'] = (int) $r['discovered'];
    }
    wp_safe_redirect(add_query_arg($args, admin_url('admin.php')));
    exit;
});

/* --- Cron: täglicher Hintergrund-Abgleich (auto) ---
   wp-cron ist unzuverlässig (läuft nur bei Seitenbesuch). Der Knopf
   bleibt das Arbeitspferd; der Cron ist die Grundversorgung. --- */
add_action('cluster_one_daily_sync', function () {
    cluster_one_radar_sync(false);
});

// Sicherheitsnetz: nachplanen, falls das Plugin per Datei-Update statt
// Reaktivierung aktualisiert wurde.
add_action('init', function () {
    if (!wp_next_scheduled('cluster_one_daily_sync')) {
        wp_schedule_event(time() + 300, 'daily', 'cluster_one_daily_sync');
    }
});

/* --- Hilfe für die UI: ist diese URL bereits eine Kante (aktiv ODER getrennt)? --- */
function cluster_one_url_is_edge(string $url): string {
    $n = cluster_one_norm_url($url);
    foreach (cluster_one_get_paired_map() as $e) {
        if (cluster_one_norm_url((string) ($e['url'] ?? '')) === $n) {
            return (($e['status'] ?? 'active') === 'banned') ? 'banned' : 'active';
        }
    }
    return '';
}

/* --- Entdeckte Knoten: bekannte Zeiger, zu denen (noch) KEINE Kante besteht. --- */
function cluster_one_discovered_nodes(): array {
    $self = cluster_one_norm_url(cluster_one_self_base_url());
    $out  = [];
    foreach (cluster_one_known_nodes() as $n) {
        $u = cluster_one_norm_url((string) ($n['url'] ?? ''));
        if ($u === '' || $u === $self) continue;
        if (cluster_one_url_is_edge($u) !== '') continue; // schon Kante -> steht oben
        $out[] = $n;
    }
    // Jüngste zuerst.
    usort($out, function ($a, $b) {
        return (int) ($b['seen'] ?? 0) <=> (int) ($a['seen'] ?? 0);
    });
    return $out;
}

function cluster_one_render_banned_page() {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Banned'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_mesh_tabs('cluster_one_banned'); ?>
        <?php
        if (isset($_GET['ban']) && sanitize_text_field(wp_unslash($_GET['ban'])) === 'off') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Node unbanned. It can be talked to again.', 'cluster-one-kleinrock') . '</p></div>';
        }
        /* v0.9.40: Rückmeldung auf die Antwort — ein Knopf, der schweigt,
           lässt den Menschen raten, ob er getroffen hat (0.9.26). */
        if (isset($_GET['trotz'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $c1_t = sanitize_key(wp_unslash($_GET['trotz'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            if ($c1_t === 'nein') {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Noted. Your ban stays, and this node will not be brought up again.', 'cluster-one-kleinrock') . '</p></div>';
            } elseif ($c1_t === 'spaeter') {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Put away. The question comes back in a week.', 'cluster-one-kleinrock') . '</p></div>';
            }
        }
        if (cluster_one_antitrotz_aus()) {
            echo '<p style="opacity:.6;font-size:.92em;margin-top:1em;">'
               . esc_html__('You have switched off the question about mutual bans. It stays off until you remove cluster_one_antitrotz_off.', 'cluster-one-kleinrock')
               . '</p>';
        }

        /* v0.9.40: DIE ANTI-TROTZ-RÜCKFRAGE. Sie steht hier und nur hier —
           dort, wo deine Banne wohnen. */
        foreach (cluster_one_antitrotz_offen() as $c1_eid => $c1_e) : ?>
            <div class="notice notice-warning" style="margin-top:1.5em;max-width:760px;">
                <p style="font-size:1.05em;margin-bottom:.3em;">
                    <strong><?php printf(
                        /* translators: %s: Name des Knotens. */
                        esc_html__('%s has lifted its ban against you.', 'cluster-one-kleinrock'),
                        esc_html((string) ($c1_e['name'] ?? 'Peer'))
                    ); ?></strong>
                </p>
                <p style="margin-top:0;opacity:.85;">
                    <?php esc_html_e('You both had each other blocked. That node is talking again — yours is still standing. Lift it too?', 'cluster-one-kleinrock'); ?>
                </p>
                <?php /* EIN Formular, damit der Haken zu JEDEM der drei Knöpfe
                   gilt — auch zu „Ja". Wer zustimmt und trotzdem nie wieder
                   gefragt werden will, soll das in einem Zug sagen können. */ ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:.8em 0 1em;">
                    <?php wp_nonce_field('cluster_one_antitrotz'); ?>
                    <input type="hidden" name="action" value="cluster_one_antitrotz">
                    <input type="hidden" name="edge" value="<?php echo esc_attr((string) $c1_eid); ?>">
                    <p style="margin:.4em 0;">
                        <label><input type="checkbox" name="alle" value="1">
                            <?php esc_html_e('Never ask again, in any case', 'cluster-one-kleinrock'); ?></label>
                    </p>
                    <button type="submit" name="modus" value="ja" class="button button-primary"><?php esc_html_e('Yes — lift my ban too', 'cluster-one-kleinrock'); ?></button>
                    <button type="submit" name="modus" value="nein" class="button"
                            title="<?php echo esc_attr__('Your ban stays. This node will not be brought up again.', 'cluster-one-kleinrock'); ?>"><?php esc_html_e('No', 'cluster-one-kleinrock'); ?></button>
                    <button type="submit" name="modus" value="spaeter" class="button"
                            title="<?php echo esc_attr__('Put the question away — it comes back in a week.', 'cluster-one-kleinrock'); ?>"><?php esc_html_e('Later', 'cluster-one-kleinrock'); ?></button>
                </form>
            </div>
        <?php endforeach;

        $banned = [];
        foreach (cluster_one_get_paired_map() as $e) {
            if (($e['status'] ?? 'active') === 'banned') {
                $banned[] = $e;
            }
        }
        ?>
        <p style="max-width:760px;margin-top:1em;opacity:.85;">
            <?php esc_html_e('A ban is a quarantine, not a verdict on a person: the node is blocked at the route and cannot re-pair. It is local — never shared across the network — and fully reversible. Use it when a node was hacked or turned malicious; unban once the cause is fixed.', 'cluster-one-kleinrock'); ?>
        </p>

        <?php if (empty($banned)) : ?>
            <p style="opacity:.7;margin-top:1.5em;"><?php esc_html_e('No banned nodes.', 'cluster-one-kleinrock'); ?></p>
        <?php else : ?>
            <table class="widefat striped" style="max-width:760px;margin-top:1em;">
                <thead>
                    <tr>
                        <th><?php esc_html_e('URL', 'cluster-one-kleinrock'); ?></th>
                        <th><?php esc_html_e('Name', 'cluster-one-kleinrock'); ?></th>
                        <th style="text-align:center;"><?php esc_html_e('Action', 'cluster-one-kleinrock'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($banned as $e) :
                    $edge = (string) ($e['edge'] ?? '');
                ?>
                    <tr>
                        <td><?php echo cluster_one_identity_cell( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_identity_cell() gibt fertiges HTML zurueck (esc_url/esc_html im Innern)
                            (string) ($e['name'] ?? 'Peer'), '', '', (string) ($e['url'] ?? '')
                        ); ?></td>
                        <td><?php echo cluster_one_name_cell((string) ($e['name'] ?? '')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_name_cell() gibt fertiges HTML zurueck (esc_html im Innern) ?></td>
                        <td style="text-align:center;white-space:nowrap;">
<?php /* v0.2.8: Der Chat-Knopf an derselben Stelle wie im Mesh — ein Bann beendet
   das Gespraech, er loescht es nicht. Ohne Verlauf hat er keine Funktion. */
$c1_verlauf = count(cluster_one_get_chat($edge)); ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_hello&peer=' . rawurlencode($edge))); ?>"
                               style="text-decoration:none;vertical-align:middle;margin-right:.6em;"
                               <?php /* translators: %d: Anzahl der Nachrichten. */ ?>
                               title="<?php echo esc_attr(sprintf(__('Read the archived conversation (%d messages) — banned, so nothing can be sent.', 'cluster-one-kleinrock'), $c1_verlauf)); ?>"
                               aria-label="<?php echo esc_attr__('Archived conversation', 'cluster-one-kleinrock'); ?>">
                                <span class="dashicons dashicons-format-chat" aria-hidden="true" style="font-size:20px;width:20px;height:20px;opacity:.6;"></span>
                            </a>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline;">
                                <?php wp_nonce_field('cluster_one_unban'); ?>
                                <input type="hidden" name="action" value="cluster_one_unban">
                                <input type="hidden" name="edge" value="<?php echo esc_attr($edge); ?>">
                                <button type="submit" class="button button-small"><?php esc_html_e('Unban', 'cluster-one-kleinrock'); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

/** Freundesliste (v0.0.54): das Gegenstück zur Bannliste, genau so lokal
   und einseitig. Kein Antrag, keine Zustimmung, keine Nachricht über den
   Knoten hinweg — jeder Knoten pflegt seine eigene Liste unabhängig vom
   anderen. */
function cluster_one_render_friends_page() {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Friends'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_mesh_tabs('cluster_one_friends'); ?>
        <?php
        if (isset($_GET['friend']) && sanitize_text_field(wp_unslash($_GET['friend'])) === 'off') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Removed from friends.', 'cluster-one-kleinrock') . '</p></div>';
        }
        $friends = cluster_one_get_friend_peers();
        ?>
        <p style="max-width:760px;margin-top:1em;opacity:.85;">
            <?php esc_html_e('One-sided, on purpose: you mark a node as a friend on your own list, no request sent, no confirmation needed. The other node may or may not do the same — that is its own, independent decision, not something to ask permission for.', 'cluster-one-kleinrock'); ?>
        </p>

        <?php $gmute = cluster_one_global_mute_until(); ?>
        <div style="display:flex;align-items:center;margin-top:1em;"><?php /* v0.2.7: max-width:760px raus — die Steuerung endete vor dem rechten Tabellenrand, anders als im Mesh. */ ?>
            <span style="margin-left:auto;">
                <?php if ($gmute > time()) : ?>
                    <span style="color:#bd8600;font-weight:600;font-size:.92em;margin-right:.5em;">
                        <?php /* translators: %s: Uhrzeit, bis zu der Ruhe gilt. */ ?>
                        <?php printf(esc_html__('Do not disturb (all) — until %s', 'cluster-one-kleinrock'), esc_html(date_i18n(get_option('time_format'), $gmute))); ?>
                    </span>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;margin:0;">
                        <?php wp_nonce_field('cluster_one_unmute_global'); ?>
                        <input type="hidden" name="action" value="cluster_one_unmute_global">
                        <button type="submit" class="button button-small"><?php esc_html_e('Available again', 'cluster-one-kleinrock'); ?></button>
                    </form>
                <?php else : ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;margin:0;">
                        <?php wp_nonce_field('cluster_one_mute_global'); ?>
                        <input type="hidden" name="action" value="cluster_one_mute_global">
                        <label style="font-size:.92em;opacity:.8;"><?php esc_html_e('Do not disturb (all):', 'cluster-one-kleinrock'); ?>
                            <select name="hours" id="c1-mute-hours" style="margin:0 .3em;">
                                <option value="1">1 h</option>
                                <option value="8" selected>8 h</option>
                                <option value="24">24 h</option>
                            </select>
                        </label>
                        <button type="submit" class="button button-small"><?php esc_html_e('Mute', 'cluster-one-kleinrock'); ?></button>
                    </form>
                <?php endif; ?>
            </span>
        </div>
            <?php /* v0.6.9: Dunbar. Christians Wunsch — die Zahl soll dastehen, bevor die
   Liste kommt. Sie ist eine ANLEIHE, kein Naturgesetz: Dunbar schaetzte
   stabile Beziehungen zwischen MENSCHEN, hier werden KNOTEN gezaehlt, hinter
   denen viele stehen koennen. Deshalb steht sie sichtbar da, statt still zu
   wirken — und deshalb ist sie heute eine Anzeige, keine Sperre. */ ?>
            <p style="max-width:880px;margin:1.2em 0 -.4em;opacity:.85;"><?php
                printf(
                    /* translators: 1: current number of friend nodes, 2: the Dunbar number */
                    esc_html(_n('%1$d node of %2$d possible (Dunbar).', '%1$d nodes of %2$d possible (Dunbar).', cluster_one_friend_count(), 'cluster-one-kleinrock')),
                    (int) cluster_one_friend_count(),
                    (int) CLUSTER_ONE_DUNBAR
                );
            ?></p>
        <?php if (empty($friends)) : ?>
            <p style="opacity:.7;margin-top:1.5em;"><?php esc_html_e('No friends marked yet — use the heart icon next to a node under Net.', 'cluster-one-kleinrock'); ?></p>
        <?php else : ?>
            <?php
            // v0.0.66: Die Freundesliste pingt ihre Kanten und trägt dieselben
            // Sensor-Spalten wie die Netz-Tabelle (Zustand, Version, Neues
            // Label, Discord/Matrix) — beide Tabellen lesen denselben
            // Kanten-Datensatz, jetzt auch denselben Live-Zustand.
            $friend_pings = array();
            $f_eigenreihe = cluster_one_knoten_reihe(); // v1.3.7 (AUFTRAG-02 §2)
            foreach ($friends as $fk => $e) {
                $friend_pings[$fk] = cluster_one_ping((string) ($e['url'] ?? ''), (string) ($e['token'] ?? ''));
            }
            /* v1.0.17: HIER STAND DIE ZWEITE KOPIE DER HOECHSTWERT-SCHLEIFE,
               mit dem Kommentar „wie auf der Mesh-Seite" darueber — eine
               Doppelung, die sich selbst gemeldet hat und trotzdem eine war.
                   Ein Kommentar, der eine Doppelung zugibt, hebt sie nicht auf.
               Beide Seiten rufen jetzt dieselbe Funktion. Dass sie dasselbe
               sagen, ist damit keine Sorgfalt mehr, sondern eine Tatsache. */
            $f_max_ver = cluster_one_kleinrock_netz_hoechste(array_values($friend_pings));
            ?>
            <table class="widefat striped" style="max-width:880px;margin-top:1em;">
                <thead>
                    <tr>
                        <th><?php esc_html_e('URL', 'cluster-one-kleinrock'); ?></th>
                        <th><?php esc_html_e('Name', 'cluster-one-kleinrock'); ?></th>
                        <th><?php esc_html_e('State', 'cluster-one-kleinrock'); ?></th>
                        <th style="text-align:center;"><?php esc_html_e('Version', 'cluster-one-kleinrock'); ?></th>
                        <th style="text-align:center;"><?php esc_html_e('New', 'cluster-one-kleinrock'); ?></th>
                            <?php /* v0.2.5: rechtsbuendig wie in der Mesh-Tabelle — die
                           Aktionsspalte stand hier zentriert und damit versetzt. */ ?>
                        <th style="text-align:right;"><?php esc_html_e('Actions', 'cluster-one-kleinrock'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($friends as $fk => $e) :
                    $edge   = (string) ($e['edge'] ?? '');
                    $n      = count(cluster_one_get_chat($edge));
                    $unread = cluster_one_edge_unread($edge, (int) ($e['last_read'] ?? 0));
                    $fr     = $friend_pings[$fk];
                    $fname  = ($fr['ok'] && !empty($fr['node_id'])) ? (string) $fr['node_id'] : (string) ($e['name'] ?? 'Peer');
                    $furl   = $fr['ok'] ? (string) ($fr['url'] ?? '') : (string) ($e['url'] ?? '');
                ?>
                    <tr>
                        <td><?php echo cluster_one_identity_cell($fname, '', '', $furl); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_identity_cell() gibt fertiges HTML zurueck (esc_url/esc_html im Innern) ?></td>
                        <td><?php echo cluster_one_name_cell($fname); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_name_cell() gibt fertiges HTML zurueck (esc_html im Innern) ?></td>
                        <td>
                            <?php if ($fr['ok']) : ?>
                                <span style="color:#1a7f37;font-weight:600;white-space:nowrap;">● <?php esc_html_e('online', 'cluster-one-kleinrock'); ?></span>
                            <?php elseif (!empty($fr['remote_disconnect'])) : ?>
                                <span style="color:#b26b00;font-weight:600;white-space:nowrap;" title="<?php echo esc_attr((string) ($fr['reason'] ?? '')); ?>">◐ <?php esc_html_e('banned', 'cluster-one-kleinrock'); ?></span>
                            <?php else : ?>
                                <span style="color:#8a8a8a;font-weight:600;white-space:nowrap;" title="<?php echo esc_attr((string) ($fr['reason'] ?? '')); ?>">○ <?php esc_html_e('offline', 'cluster-one-kleinrock'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;"><?php echo $fr['ok'] ? cluster_one_version_cell($fr['version'], $f_max_ver, (string) ($fr['reihe'] ?? '') === $f_eigenreihe && $f_eigenreihe !== '', !empty($fr['pruefstand'])) : cluster_one_flag_unknown(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_version_cell() escapt die Nummer mit esc_html, die Farbe ist ein Literal; cluster_one_flag_unknown() ist ein Literal ?></td>
                        <td style="text-align:center;"><?php echo cluster_one_label_dot_cell($fr, (int) ($e['label_ts_seen'] ?? 0)) . cluster_one_meeting_dot_cell($fr, (string) ($e['url'] ?? '')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_label_dot_cell() und cluster_one_meeting_dot_cell() bauen ihre Links mit esc_url/esc_attr, das Zeichen ist ein Literal (bisheriger Zeilenkommentar: v0.3.19) ?></td>
                        <td style="text-align:center;white-space:nowrap;">
                            <span style="display:inline-flex;align-items:center;gap:.7em;justify-content:center;">

                                <?php // 1) Entfreunden — X wie im Netz, nur eben nicht Bann (v0.0.65) ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline;line-height:1;">
                                    <?php wp_nonce_field('cluster_one_friend_remove'); ?>
                                    <input type="hidden" name="action" value="cluster_one_friend_remove">
                                    <input type="hidden" name="edge" value="<?php echo esc_attr($edge); ?>">
                                    <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;"
                                            title="<?php echo esc_attr__('Remove from friends', 'cluster-one-kleinrock'); ?>" aria-label="<?php echo esc_attr__('Remove from friends', 'cluster-one-kleinrock'); ?>"
                                            <?php /* translators: %s: Name des Knotens. */ ?>
                                            onclick="return confirm('<?php echo esc_js(sprintf(__('Remove “%s” from friends? Your list only — the other node is not notified.', 'cluster-one-kleinrock'), (string) ($e['name'] ?? 'Peer'))); ?>');">
                                        <span class="dashicons dashicons-dismiss" aria-hidden="true" style="color:#8a8a8a;font-size:20px;width:20px;height:20px;"></span>
                                    </button>
                                </form>

                                <?php // 2) Herz — hier immer rot; Klick = Entfreunden (derselbe Umschalter wie im Netz, durchgezogen) ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline;line-height:1;">
                                    <?php wp_nonce_field('cluster_one_friend_remove'); ?>
                                    <input type="hidden" name="action" value="cluster_one_friend_remove">
                                    <input type="hidden" name="edge" value="<?php echo esc_attr($edge); ?>">
                                    <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;"
                                            title="<?php echo esc_attr__('Remove from friends', 'cluster-one-kleinrock'); ?>" aria-label="<?php echo esc_attr__('Remove from friends', 'cluster-one-kleinrock'); ?>">
                                        <span class="dashicons dashicons-heart" aria-hidden="true" style="color:#d63384;font-size:20px;width:20px;height:20px;"></span>
                                    </button>
                                </form>

                                <?php // 3) Stumm — DIESELBE Zelle wie im Netz und im Chat (H134) ?>
                                <?php echo cluster_one_mute_zelle($edge); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_mute_zelle() gibt fertiges HTML zurueck (esc_url/esc_attr/wp_nonce_field im Innern) ?>

<?php /* v0.7.1: Das Briefsymbol an DERSELBEN Stelle wie im Mesh — X, Herz,
   Lautsprecher, Brief, Chat. In 0.6.9 stand es am Anfang der Zelle; dieselben
   Symbole in zwei Tabellen in verschiedener Reihenfolge, und genau die
   Unordnung sollte der gemeinsame Helfer beseitigen (Christians Befund).

   ZWEITER ANLAUF: Der erste setzte den Block mitten in das <a>-Tag des
   Chat-Symbols — zwischen style und title. Die Attribute erschienen daraufhin
   als TEXT auf der Seite. Golden konnte es nicht fangen, weil die Pruefwelt
   keine Freunde hat und die Aktionsspalte dort gar nicht rendert. */ ?>
                                <?php echo cluster_one_mail_icon_cell($e); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_mail_icon_cell() gibt fertiges HTML zurueck (esc_url/esc_attr im Innern) oder eine leere Luecke ?>
                                <?php // 4) Chat — mit Ungelesen-Zähler, wie im Netz ?>
                                <?php
                                $hello_label = $n > 0 ? __('Open conversation', 'cluster-one-kleinrock') : __('Say hello', 'cluster-one-kleinrock');
                                $hello_color = $unread > 0 ? '#1a7f37' : '#8a8a8a';
                                ?>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_hello&peer=' . rawurlencode($edge))); ?>"
                                   style="display:inline-flex;align-items:center;gap:.25em;width:3.4em;text-decoration:none;color:<?php echo $hello_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $hello_color ist drei Zeilen hoeher eines von zwei festen Farbliteralen ?>;"
                                   title="<?php echo esc_attr($hello_label); ?>" aria-label="<?php echo esc_attr($hello_label); ?>">
                                    <span class="dashicons dashicons-format-chat" aria-hidden="true" style="font-size:20px;width:20px;height:20px;flex:none;"></span>
                                    <?php echo cluster_one_hello_count($unread, $n); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_hello_count() gibt fertiges HTML zurueck ((int)-Zahl oder cluster_one_unread_badge mit esc_attr__) ?>
                                </a>

                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <script>
            (function () {
                var top = document.getElementById('c1-mute-hours');
                if (!top) return;
                function sync() { document.querySelectorAll('.c1-mute-hours-sync').forEach(function (h) { h.value = top.value; }); }
                top.addEventListener('change', sync);
                sync();
            })();
            </script>
        <?php endif; ?>
    </div>
    <?php
}

/** KI-Vorschläge (v0.0.54, Reißbrett): bewusst leer. Wartet auf Label —
   cluster_one_new_label_signal() gilt nur noch für die EIGENE Zeile, eine lokale
   KI-Relevanzprüfung über gesammelte Labels existiert noch gar nicht. Kein
   Vortäuschen einer Funktion, die von etwas Unfertigem abhängt. */
function cluster_one_render_ai_suggestions_page() {
    cluster_one_page_gate();
    /* v0.3.0: Diese Seite war ein Ablagefach — eine flache Liste aller je
       geholten Labels, die nie alterte und aus der nichts herauskam. Jetzt ist
       sie ein DURCHGANG: Sie zeigt, WO etwas Neues liegt. Der Pull holt es in
       den Bereich "Fremd-Labels", danach ist diese Seite wieder leer.
       Sie rechnet sich live aus den Pings — es gibt nichts zu speichern. */
    $c1_map    = cluster_one_get_paired_map();
    $c1_offen  = array();
    foreach ($c1_map as $c1_e) {
        if (($c1_e['status'] ?? '') !== 'active' || empty($c1_e['url'])) {
            continue;
        }
        $c1_ping = cluster_one_ping((string) $c1_e['url'], (string) ($c1_e['token'] ?? ''));
        $c1_zeile = array(
            'name' => (string) ($c1_e['name'] ?? ($c1_ping['node_id'] ?? '')),
            'url'  => (string) $c1_e['url'],
            'dot'  => function_exists('cluster_one_label_dot') ? cluster_one_label_dot($c1_ping, (int) ($c1_e['label_ts_seen'] ?? 0)) : 'unbekannt', // v1.3.7 (AUFTRAG-02 §1)
        );
        /* v0.8.0: Die stille Liste ist entfallen — sie wurde als Fliesstext
           unter der Tabelle ausgegeben und sagte dasselbe zweimal. Wer nichts
           Neues hat, taucht jetzt gar nicht auf; der Satz darueber sagt es. */
        if ($c1_zeile['dot'] === 'neu') { $c1_offen[] = $c1_zeile; }
    }
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Suggestions'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_mesh_tabs('cluster_one_ai_suggestions'); ?>
        <p style="max-width:760px;opacity:.85;"><?php esc_html_e('Nodes that have published something you have not pulled yet. Pulling files their labels into the “Fremd-Labels” area, where you and the AIs here can read them — and where an AI can check them against your own labels. Afterwards this list is empty again: nothing was deleted, it moved.', 'cluster-one-kleinrock'); ?></p>
<?php
if (isset($_GET['sugg_err'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html(
        (string) $_GET['sugg_err'] === 'self' // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            ? __('That is this node. Your own labels are already here — pulling them again would file them a second time as foreign ones.', 'cluster-one-kleinrock')
            : __('That node did not answer with labels.', 'cluster-one-kleinrock')
    ) . '</p></div>';
}
if (isset($_GET['gezogen'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(sprintf(
        /* translators: %d: Anzahl neuer Eintraege. */
        __('Pulled: %d new entries. They are in the “Fremd-Labels” area now.', 'cluster-one-kleinrock'), (int) $_GET['gezogen'] // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    )) . '</p></div>';
}
/* v0.8.0: EINE TABELLE, IMMER — Christians Befund.

   Vorher zerfiel die Seite in drei Teile: eine Tabelle, die nur erschien, wenn
   irgendwo ein Laempchen brannte · ein Freitext-Formular "von einer beliebigen
   Adresse" · und darunter eine lose Aufzaehlung "hier nichts Neues". Auf drei
   von vier Knoten war die Tabelle deshalb GAR NICHT DA, und dieselbe Auskunft
   stand einmal als Zeile und einmal als Fliesstext.

   Jetzt wie die Freundesliste: alle verbundenen Knoten in EINER Tabelle, das
   Laempchen als Spalte. Wer nichts Neues hat, steht mit hohlem Kreis da — er
   verschwindet nicht. Das Holen bleibt an jeder Zeile moeglich: Das Formular
   hat nie nach dem Laempchen gefragt, und die Zeile tut es auch nicht.

   DAS FREITEXT-FORMULAR IST WEG. Es war der einzige Weg, einen Knoten
   anzusehen, mit dem man KEINE Kante hat — Christian braucht das nicht, und
   fuer verbundene Knoten war es doppelt. Der Handler bleibt, er traegt jetzt
   nur noch die Zeilen-Knoepfe. */
if (!cluster_one_is_sysop()) : ?>
        <p style="opacity:.6;"><?php esc_html_e('Pulling is the SysOp’s call — they pay for it. What has been pulled, you can read under foreign labels.', 'cluster-one-kleinrock'); ?></p>
<?php elseif (!$c1_offen) : ?>
<?php /* v0.8.0, zweiter Anlauf: Christians Korrektur. Mein erster Umbau zeigte
   IMMER eine Tabelle, auch wenn nichts zu holen war — ein Tisch voller Zeilen
   ohne Anlass. Ist nichts neu, reicht ein Satz. Die Tabelle taucht von selbst
   wieder auf, sobald irgendwo etwas veroeffentlicht oder geaendert wurde. */ ?>
        <p style="opacity:.6;"><?php esc_html_e('All nodes up to date — nothing new to pull right now.', 'cluster-one-kleinrock'); ?></p>
<?php else : ?>
        <table class="widefat striped" style="max-width:820px;margin-top:1em;">
            <thead><tr>
                <th><?php esc_html_e('Node', 'cluster-one-kleinrock'); ?></th>
                <th style="text-align:center;"><?php esc_html_e('New', 'cluster-one-kleinrock'); ?></th>
                <th style="text-align:right;"><?php esc_html_e('Actions', 'cluster-one-kleinrock'); ?></th>
            </tr></thead>
            <tbody>
<?php foreach ($c1_offen as $c1_z) : ?>
                <tr>
                    <td><?php echo cluster_one_name_cell($c1_z['name']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_name_cell() gibt fertiges HTML zurueck (esc_html im Innern) ?><br><span style="opacity:.6;font-size:.9em;white-space:nowrap;"><?php echo esc_html($c1_z['url']); ?></span></td>
                    <td style="text-align:center;"><span style="color:#1a7f37;font-weight:600;" title="<?php echo esc_attr__('Something new since your last pull', 'cluster-one-kleinrock'); ?>">●</span></td>
                    <td style="text-align:right;">
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                            <?php wp_nonce_field('cluster_one_suggestions_fetch'); ?>
                            <input type="hidden" name="action" value="cluster_one_suggestions_fetch">
                            <input type="hidden" name="url" value="<?php echo esc_attr($c1_z['url']); ?>">
                            <button type="submit" class="button button-small"><?php esc_html_e('Pull labels', 'cluster-one-kleinrock'); ?></button>
                        </form>
                    </td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
<?php endif; ?>
    </div>
    <?php
}

add_action('admin_post_cluster_one_suggestions_fetch', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_suggestions_fetch');
    $url  = isset($_POST['url']) ? esc_url_raw(trim(wp_unslash($_POST['url']))) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (esc_url_raw), den der Pruefer nicht kennt
    $back = admin_url('admin.php?page=cluster_one_ai_suggestions');
    if ($url === '' || !preg_match('#^https?://#', $url)) {
        wp_safe_redirect(add_query_arg('sugg_err', '1', $back));
        exit;
    }
    /* v0.3.2: Sich selbst ziehen ergibt keinen Sinn — das eigene Label stuende
       danach zweimal da, einmal als Label und einmal als Fremd-Label. Christian
       ist beim Testen genau darauf gestossen. Wir weisen es ab, statt es still
       zu schlucken: Wer die eigene Adresse eintippt, soll erfahren warum. */
    if (cluster_one_is_own_url($url)) {
        wp_safe_redirect(add_query_arg('sugg_err', 'self', $back));
        exit;
    }
    $host = wp_parse_url($url, PHP_URL_HOST);
    /* v0.9.53: Auch wenn hier der SysOp selbst tippt — die sichere Variante
       kostet nichts und haelt einen vertippten oder untergeschobenen
       internen Namen aus dem eigenen Netz heraus. */
    $res  = wp_safe_remote_get(untrailingslashit($url) . '/wp-json/cluster-one/v1/labels', array('timeout' => 12));
    $body = is_wp_error($res) ? '' : (string) wp_remote_retrieve_body($res);
    $data = $body !== '' ? json_decode($body, true) : null;
    if (!is_array($data) || !isset($data['labels']) || !is_array($data['labels'])) {
        wp_safe_redirect(add_query_arg('sugg_err', '1', $back));
        exit;
    }
    $labels = array();
    foreach (array_slice($data['labels'], 0, 30) as $l) {
        if (!is_array($l)) {
            continue;
        }
        $posts = array();
        foreach (array_slice((array) ($l['posts'] ?? array()), 0, 50) as $p) {
            $posts[] = array(
                'body' => sanitize_textarea_field((string) (is_array($p) ? ($p['body'] ?? '') : '')),
                'ts'   => (int) (is_array($p) ? ($p['ts'] ?? 0) : 0),
            );
        }
        $entry = array(
            'id'    => sanitize_text_field((string) ($l['id'] ?? '')),
            'title' => sanitize_text_field((string) ($l['title'] ?? '')),
            'posts' => $posts,
        );
        if (!empty($l['event_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $l['event_date'])) {
            $entry['event_date'] = (string) $l['event_date'];
            // v0.1.4: Ort, Uhrzeit und Zone kommen mit dem Datum durch — ohne
            // Ort ist keine Adoption möglich (virtuell bleibt knoten-intern).
            $entry['event_place'] = sanitize_text_field((string) ($l['event_place'] ?? ''));
            $entry['event_time']  = preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', (string) ($l['event_time'] ?? '')) ? (string) $l['event_time'] : '';
            $entry['event_tz']    = sanitize_text_field((string) ($l['event_tz'] ?? ''));
            $entry['project']     = sanitize_text_field((string) ($l['project'] ?? '')); // v0.3.8
        }
        $labels[] = $entry;
    }
    // v0.1.3: Ziehen heißt gesehen — der Punkt dieser Kante geht danach aus.
    /* v1.3.3: der Riegel bleibt — siehe platon.php. Der Umzug dieser Funktion
       braucht ZWEI Auslieferungen, und dies ist die erste. */
    if (function_exists('cluster_one_label_mark_seen')) { cluster_one_label_mark_seen($url, (int) ($data['label_ts'] ?? time())); }
    /* v0.3.0: Der Pull landet nicht mehr in einem Zwischenspeicher, sondern
       als Inhalt im Bereich "Fremd-Labels". Der Zwischenspeicher
       cluster_one_suggestions ENTFAELLT ersatzlos — damit auch die Fragen
       "wann wurde geholt" (Threads haben Zeitstempel) und "altert nie". */
    $c1_node = sanitize_text_field((string) ($data['node'] ?? (string) $host));
    $c1_sysop = sanitize_text_field((string) ($data['sysop'] ?? ''));
    $c1_zahl  = cluster_one_import_foreign_labels($labels, $c1_node, $url, $c1_sysop);
    wp_safe_redirect(add_query_arg('gezogen', (int) $c1_zahl, $back));
    exit;
});

/* v0.3.0: Fremdlabels als Threads ablegen. Ein Thread pro Label, erkannt an
   Herkunft plus fremder Label-Kennung. Zweiter Pull haengt NACH, statt einen
   zweiten Thread anzulegen — so steht die Entwicklung eines fremden Projekts
   an einem Ort. Gibt die Zahl der neuen Beitraege zurueck. */
/** v0.3.2: Ist das unsere eigene Adresse? Vergleicht normalisiert, damit
   Schraegstrich, www und Protokoll nicht taeuschen. */
/** v0.3.15: Die offenen Treffen eines Knotens, LIVE geholt. Kein
   Zwischenspeicher — er koennte altern, und ein veralteter Termin ist
   schlimmer als keiner. Dieselbe Regel wie bei den Vorschlaegen. */
/** v0.3.19: Wie viele Treffen dieser Knoten offen ausrichtet. */
/** v0.3.20: Holt beim Gast ab, wen er zu UNSEREN Treffen schickt. Der
   Gastgeber vertraut keiner zugerufenen Zahl — er liest sie an der Quelle.
   Gibt die Zahl der uebernommenen Meldungen zurueck. */
/* v0.9.53: DER RIEGEL AN DER KLINGEL.
   Wieviele Griffe nach draussen darf eine fremde Hand in einer Stunde
   ausloesen, und wie oft dieselbe Adresse. Beide Zahlen sind grosszuegig
   gewaehlt: Ein echter Gast klingelt einmal, kein Gastgeber richtet dreissig
   Treffen in einer Stunde aus. Wer an die Grenze stoesst, ist keiner. */
if (!defined('CLUSTER_ONE_KLINGEL_MAX'))   define('CLUSTER_ONE_KLINGEL_MAX', 30);    // Griffe je Stunde
if (!defined('CLUSTER_ONE_KLINGEL_PAUSE')) define('CLUSTER_ONE_KLINGEL_PAUSE', 300); // Sekunden je Adresse

/* 18.10.2026 (Neo), B2 — DIE BREMSE AM HANDSCHLAG.
   /pair nahm bis heute jeden Anstoss an: ein Rueckruf nach draussen je
   Aufruf (ein Reflektor) und eine neue pending-Kante je fremder Domain
   (Option-Bloat, „Someone is knocking" im Dauerton). Dieselbe Form wie die
   Klingel, eigene Zahlen: ein echter Knoten klopft einmal an, und mehr als
   zwanzig fremde Haeuser in einer Stunde hat kein Mesh dieser Groesse.
   Dazu ein Deckel auf die Zahl der UNBEANTWORTETEN Handschlaege — wer schon
   fuenfzig vor der Tuer hat, braucht keinen einundfuenfzigsten, bevor ein
   Mensch die ersten angesehen hat. Bekannte Adressen (eine Kante gleich
   welchen Status) gehen an der Bremse vorbei: fuer sie entsteht nichts
   Neues. */
if (!defined('CLUSTER_ONE_HANDSCHLAG_MAX'))   define('CLUSTER_ONE_HANDSCHLAG_MAX', 20);   // fremde Anstoesse je Stunde
if (!defined('CLUSTER_ONE_HANDSCHLAG_PAUSE')) define('CLUSTER_ONE_HANDSCHLAG_PAUSE', 300); // Sekunden je Domain
if (!defined('CLUSTER_ONE_HANDSCHLAG_OFFEN')) define('CLUSTER_ONE_HANDSCHLAG_OFFEN', 50);  // pending-Kanten hoechstens

/** v0.9.53: Darf diese Klingel einen Griff nach draussen ausloesen?
   Stundenfenster plus Dublettensperre je Adresse. Schreibt nur, wenn sie
   ja sagt — ein abgewiesener Versuch soll das Fenster nicht auffuellen,
   sonst koennte ein Angreifer den Gastgeber mit lauter Absagen lahmlegen. */
function cluster_one_klingel_frei(string $url): bool {
    /* 18.10.2026 (Neo): der Rumpf ist nach cluster_one_tor_frei() gezogen,
       damit der Handschlag (B2) dieselbe Bremse benutzt statt einer zweiten
       Abschrift. Option, Zahlen und Verhalten der Klingel sind unveraendert. */
    return cluster_one_tor_frei('klingel', $url, CLUSTER_ONE_KLINGEL_MAX, CLUSTER_ONE_KLINGEL_PAUSE);
}

/** EINE Bremse, mehrere Tore. Je Tor eine Option `cluster_one_<tor>_tor`
 *  mit Stundenfenster, Zaehler und Zeitpunkt des letzten Griffs je
 *  Schluessel (eine URL, eine Domain). true = der Griff darf, und er ist
 *  gezaehlt. false = zu oft, oder derselbe Schluessel zu schnell. */
function cluster_one_tor_frei(string $tor, string $schluessel, int $max, int $pause): bool {
    $tor = preg_replace('/[^a-z_]/', '', strtolower($tor));
    if ($tor === '') {
        return false;
    }
    $name  = 'cluster_one_' . $tor . '_tor';
    $stand = get_option($name, array());
    if (!is_array($stand)) {
        $stand = array();
    }
    $jetzt  = time();
    $stunde = (int) floor($jetzt / HOUR_IN_SECONDS);
    if ((int) ($stand['stunde'] ?? 0) !== $stunde) {
        $stand = array('stunde' => $stunde, 'zahl' => 0, 'zuletzt' => array());
    }

    $zuletzt = (array) ($stand['zuletzt'] ?? array());
    if ($schluessel !== '' && $jetzt - (int) ($zuletzt[$schluessel] ?? 0) < $pause) {
        return false; // derselbe Schluessel hat eben erst gegriffen
    }
    if ((int) ($stand['zahl'] ?? 0) >= $max) {
        return false; // Stundenfenster voll
    }

    if ($schluessel !== '') {
        $zuletzt[$schluessel] = $jetzt;
    }
    if (count($zuletzt) > $max * 2) {
        arsort($zuletzt);                           // juengste zuerst
        $zuletzt = array_slice($zuletzt, 0, $max, true);
    }
    $stand['zahl']    = (int) ($stand['zahl'] ?? 0) + 1;
    $stand['zuletzt'] = $zuletzt;
    update_option($name, $stand, false);
    return true;
}

/** Wie viele Handschlaege liegen unbeantwortet vor der Tuer? (B2) */
function cluster_one_offene_handschlaege(): int {
    $n = 0;
    foreach (cluster_one_get_paired_map() as $e) {
        if ((string) ($e['status'] ?? '') === 'pending') {
            $n++;
        }
    }
    return $n;
}

/** Gibt es zu dieser Adresse schon eine Kante, gleich welchen Status? (B2) */
function cluster_one_kante_zu_url(string $url): ?array {
    $n = cluster_one_norm_url($url);
    if ($n === '') {
        return null;
    }
    foreach (cluster_one_get_paired_map() as $e) {
        if (cluster_one_norm_url((string) ($e['url'] ?? '')) === $n) {
            return $e;
        }
    }
    return null;
}

function cluster_one_pull_rsvp(string $url): int {
    $url = cluster_one_norm_url($url);
    if ($url === '' || cluster_one_is_own_url($url)) {
        return 0;
    }
    /* v0.9.53: DIE KLINGEL IST OEFFENTLICH — DAS ZIEL DARF ES NICHT SEIN.
       Die Route /rsvp nimmt seit 0.3.20 bewusst jeden an: "Eine Zusage darf
       keine Freundschaft voraussetzen." Richtig. Aber das ZIEL des Griffs kam
       damit aus der Hand eines Unbekannten, und gegriffen wurde ungeschuetzt.
       Zwei Folgen, beide echt: ein Reflektor fuer verteilte Fluten, und SSRF
       ins eigene Netz (http://127.0.0.1:8080/ ueberlebt esc_url_raw).
       Die Klingel bleibt. Nur klingelt sie jetzt an einer verriegelten Tuer. */
    if (stripos($url, 'https://') !== 0) {
        return 0; // dieselbe Regel, die merge_pointer seit jeher anwendet
    }
    if (!cluster_one_klingel_frei($url)) {
        return 0;
    }
    $res = wp_safe_remote_get(rtrim($url, '/') . '/wp-json/cluster-one/v1/meetings', array('timeout' => 8));
    if (is_wp_error($res) || (int) wp_remote_retrieve_response_code($res) !== 200) {
        return 0;
    }
    $data = json_decode((string) wp_remote_retrieve_body($res), true);
    if (!is_array($data) || empty($data['attending'])) {
        return 0;
    }
    $wir    = cluster_one_norm_url((string) home_url('/'));
    $events = cluster_one_get_gregor_events();
    $name   = sanitize_text_field((string) ($data['node'] ?? $url));
    $n      = 0;
    foreach ((array) $data['attending'] as $a) {
        if (cluster_one_norm_url((string) ($a['host'] ?? '')) !== $wir) {
            continue; // faehrt woandershin
        }
        foreach ($events as $i => $ev) {
            if ((string) ($ev['id'] ?? '') !== (string) ($a['id'] ?? '') || !empty($ev['archimedes'])) {
                continue;
            }
            /* v0.4.6: Alte Eintraege standen unter dem NAMEN (vor 0.4.1). Sie
               werden nie mehr aktualisiert und stuenden als Karteileiche neben
               der echten Zeile — auf der Dev war das nach einem Tag schon so.
               Wer dieselbe Adresse hat, wird umgehaengt statt verdoppelt. */
            foreach ((array) ($events[$i]['rsvps'] ?? array()) as $c1_alt => $c1_e) {
                if ((string) $c1_alt !== $url
                    && cluster_one_norm_url((string) ($c1_e['url'] ?? '')) === $url) {
                    unset($events[$i]['rsvps'][$c1_alt]);
                }
            }
            $events[$i]['rsvps'][$url] = array( // v0.4.1: Adresse als Schluessel
                'count' => max(0, min(9999, (int) ($a['count'] ?? 0))),
                'ts'    => time(),
                'node'  => $name, // Etikett zum Lesen, nicht zum Unterscheiden
                'url'   => $url,
            );
            $n++;
        }
    }
    if ($n > 0) {
        cluster_one_save_gregor_events($events);
    }
    return $n;
}

/* v0.2.5 — DER ZAEHLER FRAGT, STATT ZU RUFEN. Und er kann jetzt schweigen.
 *
 * Bis 0.2.4 stand hier eine Schleife ueber cluster_one_get_gregor_events() mit
 * cluster_one_archimedes_is_open() darin — ein direkter Griff in das
 * Kalender-Organ, ungeriegelt. Zwei Dinge waren daran falsch, und nur das
 * erste war bekannt:
 *
 *   1  Liegt das Kalender-Organ nicht auf dem Knoten, ist der Aufruf ein
 *      Fatal — und er steht in der /ping-Route.
 *   2  Der Rueckgabetyp `int` kann „ich habe niemanden, den ich fragen
 *      koennte" gar nicht ausdruecken. 0 hiess beides.
 *
 *     Ein Zaehler, der nicht zaehlen konnte, hat nicht null gezaehlt.
 *
 * WER ZAEHLT UND WER VERSPRICHT, SIND ZWEI. Die Ereignisse und die Regel,
 * was „offen" heisst, gehoeren dem Kalender. Das FELD `meetings` im Ping und
 * die Zusage darueber gehoeren diesem Organ. Deshalb wird gefragt, nicht
 * gerufen.
 *
 * ZWEI BERICHTIGUNGEN AN MEINER ERSTEN FASSUNG, beide von der Werkbank
 * gemessen und beide richtig:
 *
 * 1  DER WAECHTER PRUEFT JETZT DIE FUNKTION, DIE ER SCHUETZT.
 *    Er stand als `function_exists('has_filter')` da und rief danach
 *    apply_filters(). In WordPress gibt es beide oder keinen, er trug also —
 *    aber aus einem Grund, den er nicht nannte.
 *
 *        Ein Waechter, der eine andere Funktion prueft als die, die er
 *        schuetzt, ist heute richtig und morgen aus Versehen.
 *
 *    Und has_filter() ist dabei ganz entfallen, statt mitgeprueft zu werden:
 *    ohne Antworter gibt apply_filters() die Vorgabe zurueck, und die ist
 *    hier null — dieselbe Antwort, die has_filter() erzwungen haette. Eine
 *    Frage weniger, eine Funktion weniger, ein Waechter fuer genau sie.
 *
 * 2  EINE -7 IST KEINE GEMESSENE NULL.
 *    Vorher stand hier max(0, $c1_n). Das machte aus der Antwort eines
 *    kaputten Antworters die Behauptung „es gibt keine offenen Treffen" —
 *    und genau diese Behauptung ist der Grund, warum das Feld drei
 *    Zustaende bekommen hat. Eine unmoegliche Zahl ist keine Zahl.
 *
 *        Wer eine kaputte Antwort zurechtbiegt, hat sie nicht repariert,
 *        sondern ihre Herkunft geloescht.
 *
 * @return int|null  null = niemand konnte antworten oder die Antwort war
 *                   keine. NICHT 0.
 */
function cluster_one_open_meeting_count(): ?int {
    if (!function_exists('apply_filters')) {
        return null;
    }
    $c1_n = apply_filters('cluster_one_treffen', null, 'offen');
    return (is_int($c1_n) && $c1_n >= 0) ? $c1_n : null;
}

/* DER UEBERGANGS-ANTWORTER IST WEG — 18.10.2026 (Neo).
 *
 * Hier stand ein zweiter Antworter auf den Filter 'cluster_one_treffen',
 * geriegelt mit function_exists('cluster_one_get_gregor_events'). Der Riegel
 * wurde beim LADEN dieser Datei geprueft — und speicher.php, das die
 * Funktion traegt, laedt der Lader erst danach (cluster-one-kleinrock.php,
 * letzte require-Zeile). Der Block war deshalb seit dem Schnitt toter Code.
 *
 * Der eigene Kommentar sagte, was zu tun ist: „Zieht der Kalender in sein
 * eigenes Haus, meldet er sich selbst an und DIESE ZEILEN FALLEN ERSATZLOS
 * WEG." faust.php antwortet seit v1.0.x selbst (Ende der Datei, Filter
 * 'cluster_one_treffen'). Ein Frager, ein Antworter. */

/** v0.3.19: Wie viele Einladungen dieser Adresse haben wir schon angenommen? */
function cluster_one_adopted_from(string $url): int {
    $url = cluster_one_norm_url($url);
    $n = 0;
    foreach (cluster_one_get_gregor_events() as $ev) {
        if (!empty($ev['archimedes']) && cluster_one_norm_url((string) ($ev['archimedes']['host'] ?? '')) === $url) {
            $n++;
        }
    }
    return $n;
}

function cluster_one_fetch_meetings(string $url): array {
    $url = cluster_one_norm_url($url);
    if ($url === '' || cluster_one_is_own_url($url)) {
        return array();
    }
    /* v0.9.53: Die Herkunfts-URL eines uebernommenen Treffens ist FREMDE
       DATEN — sie stand irgendwann in der Antwort eines anderen Knotens.
       Also dieselbe Hand wie ueberall sonst im Haus. */
    if (stripos($url, 'https://') !== 0) {
        return array();
    }
    $res = wp_safe_remote_get(rtrim($url, '/') . '/wp-json/cluster-one/v1/meetings', array('timeout' => 8));
    if (is_wp_error($res) || (int) wp_remote_retrieve_response_code($res) !== 200) {
        return array();
    }
    $data = json_decode((string) wp_remote_retrieve_body($res), true);
    if (!is_array($data) || empty($data['meetings'])) {
        return array();
    }
    $out = array();
    foreach (array_slice((array) $data['meetings'], 0, 30) as $m) {
        $datum = (string) ($m['date'] ?? '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $datum) || $datum < current_time('Y-m-d')) {
            continue; // kein Datum, oder vorbei
        }
        if (trim((string) ($m['place'] ?? '')) === '' || trim((string) ($m['time'] ?? '')) === '') {
            continue; // ohne Ort und Stunde ist es keine Einladung
        }
        $out[] = array(
            'id'    => sanitize_text_field((string) ($m['id'] ?? '')),
            'name'  => sanitize_text_field((string) ($m['name'] ?? '')),
            /* v1.0.3: EIN ALTER KNOTEN SCHICKT KEINE BESCHREIBUNG. Das ist kein
               Fehler, sondern ein alter Knoten — '' und eine leere Zeile, kein
               Platzhaltertext, der so tut, als haette jemand etwas geschrieben. */
            'beschreibung' => cluster_one_sanitize_message((string) ($m['beschreibung'] ?? '')),
            'date'  => $datum,
            'time'  => sanitize_text_field((string) $m['time']),
            'tz'    => sanitize_text_field((string) ($m['tz'] ?? '')),
            'place' => sanitize_text_field((string) $m['place']),
            'node'  => sanitize_text_field((string) ($data['node'] ?? '')),
            'sysop' => sanitize_text_field((string) ($data['sysop'] ?? '')),
            'url'   => $url,
        );
    }
    return $out;
}

function cluster_one_is_own_url(string $url): bool {
    if ($url === '' || !function_exists('home_url')) {
        return false;
    }
    return cluster_one_norm_url($url) === cluster_one_norm_url((string) home_url('/'));
}

function cluster_one_import_foreign_labels(array $labels, string $node, string $url, string $sysop = ''): int {
    if (!$labels) {
        return 0;
    }
    if (cluster_one_is_own_url($url)) {
        return 0; // zweiter Riegel: auch programmatisch nie das eigene Label importieren
    }
    if (function_exists('cluster_one_ensure_foreign_area')) { cluster_one_ensure_foreign_area(); }
    $threads = cluster_one_get_forum_threads();
    $neu     = 0;
    foreach ($labels as $l) {
        $fid = (string) ($l['id'] ?? '');
        if ($fid !== '' && isset($threads[$fid]) && !empty($threads[$fid]['label'])) {
            continue; // das ist ein EIGENES Label — ueber einen Umweg zurueckgekommen
        }
        /* v0.4.6: Die Adresse wird NORMALISIERT, bevor daraus die Kennung wird.
           Auf der Dev standen zwei Threads fuer dasselbe Fremdlabel — einmal
           mit Schraegstrich am Ende, einmal ohne. Beim zweiten Ziehen entstand
           deshalb ein neuer Thread statt eines Nachtrags. Dieselbe Lehre wie
           beim RSVP-Schluessel: Adresse als Kennung, aber normalisiert. */
        $tid = 'fl_' . substr(hash('sha256', cluster_one_norm_url($url) . '|' . $fid), 0, 16);
        if (!isset($threads[$tid])) {
            $threads[$tid] = array(
                'id'               => $tid,
                'title'            => (string) ($l['title'] ?? $fid),
                'author'           => $node,
                'creator_actor'    => 'foreign',
                'created'          => time(),
                'group_visibility' => 'all',
                'visible_to'       => array(),
                'area'             => function_exists('cluster_one_foreign_area') ? cluster_one_foreign_area() : '',
                'wiki'             => false,
                'human_diary'      => false,
                // v0.3.0: Herkunft. 'foreign' sperrt das Bearbeiten.
                'foreign'          => true,
                'foreign_node'     => $node,
                'foreign_sysop'    => $sysop, // v0.3.6
                'foreign_url'      => $url,
                'foreign_id'       => $fid,
            );
        }
        if ($sysop !== '') { $threads[$tid]['foreign_sysop'] = $sysop; }
        // Termindaten aktualisieren — drueben kann sich ein Datum verschieben.
        foreach (array('event_date', 'event_place', 'event_time', 'event_tz', 'project') as $k) {
            if (isset($l[$k]) && $l[$k] !== '') {
                $threads[$tid][$k] = (string) $l[$k];
            }
        }
        $posts  = cluster_one_get_forum_posts($tid);
        $bekannt = array();
        foreach ($posts as $p) {
            $bekannt[hash('sha256', (string) ($p['body'] ?? ''))] = true;
        }
        foreach ((array) ($l['posts'] ?? array()) as $p) {
            $body = trim((string) ($p['body'] ?? ''));
            if ($body === '' || isset($bekannt[hash('sha256', $body)])) {
                continue; // schon da — der zweite Pull haengt nur NEUES an
            }
            $posts[] = array(
                'id'       => 'fp' . substr(hash('sha256', $tid . $body), 0, 12),
                'author'   => $node,
                'actor_id' => 'foreign',
                'subject'  => '',
                'body'     => $body,
                'ts'       => (int) ($p['ts'] ?? time()),
                'foreign'  => true,
            );
            $bekannt[hash('sha256', $body)] = true;
            $neu++;
        }
        cluster_one_save_forum_posts($tid, $posts);
    }
    cluster_one_save_forum_threads($threads);
    return $neu;
}

/** v0.0.99: KI-Sortierung — die KI ordnet NUR die Reihenfolge, nie
   Inhalte (Labels sind unveränderlich; Fremd-Labels sowieso). Antwort
   muss ein striktes JSON-Array von Schlüsseln sein, sonst ändert sich
   nichts (ehrliche Quittung). */
/* v0.4.7: DEN ZWISCHENSPEICHER AUFRAEUMEN.

   cluster_one_suggestions war der Ablagekasten der alten Vorschlagsseite. Seit
   0.3.0 liest ihn kein Code mehr — die Liste rechnet sich live aus den Pings.
   Die Option blieb aber stehen und tauchte auf Christians Dev in jedem Spiegel
   wieder auf: toter Inhalt, den ein spaeterer Leser fuer lebendig haelt.

   Einmalig, mit Marke, wie die Wiki-Migration. Und ausdruecklich NUR diese eine
   Option: Aufraeumen heisst hier nicht "loeschen, was alt aussieht", sondern
   "die eine Sache entfernen, von der wir sicher wissen, dass sie niemand mehr
   liest". */
function cluster_one_drop_legacy_suggestions(): void {
    if (get_option('cluster_one_suggestions_dropped')) {
        return;
    }
    delete_option('cluster_one_suggestions');
    delete_option('cluster_one_suggestions_order'); // Schwester aus derselben Zeit
    update_option('cluster_one_suggestions_dropped', (function_exists('cluster_one_fassung') ? cluster_one_fassung() : (defined('CLUSTER_ONE_VERSION') ? (string) CLUSTER_ONE_VERSION : '')), false);
}
add_action('admin_init', 'cluster_one_drop_legacy_suggestions');

/* v0.3.0: cluster_one_suggestions_sort ENTFAELLT — er ordnete den
   Zwischenspeicher, den es nicht mehr gibt. Die Reihenfolge im Eingang
   entscheidet jetzt der Leser (Sortierung) und der SysOp (Anheften). */

/* ============================================================
   DIE KNOEPFE DES HALLO-CHATS (v0.9.57, Umzug aus bismarck.php)
   Senden und die Mailadresse. Beide standen seit dem Schnitt v0.0.100
   im Werkraum, obwohl das Gespraech dem Netz gehoert. Byte-identisch
   umgezogen; nur das Tor des Sende-Handlers hat einen Schalter
   bekommen — siehe cluster_one_mesh_write_open().
   ============================================================ */
/* --- Senden aus dem Backend (lokaler Admin, Nonce + Capability) --- */
add_action('admin_post_cluster_one_send', function () {
    /* v0.9.57: DER SCHREIB-SCHALTER. Bis hierher durfte jeder auf der
       Weissliste im Hallo-Chat schreiben — immer, ohne dass der SysOp gefragt
       worden waere. Fuers Uebersetzen gibt es diese Wahl seit 0.9.48, fuers
       Schreiben nicht. Jetzt schon, und voreingestellt bleibt alles, wie es
       war: cluster_one_mesh_write_open() ist ohne gesetzten Schluessel wahr.
       Der SysOp kommt immer durch — ein Schalter, der den Betreiber aussperren
       kann, waere ein Fehler und keine Einstellung. */
    if (!cluster_one_can_access()
        || (!cluster_one_is_sysop() && !cluster_one_mesh_write_open())) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_send');

    $edge = isset($_POST['peer']) ? sanitize_text_field(wp_unslash($_POST['peer'])) : '';
    $body = isset($_POST['body']) ? (string) wp_unslash($_POST['body']) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- cluster_one_send_message() reinigt als Erstes mit cluster_one_sanitize_message

    $peer = null;
    foreach (cluster_one_get_peers() as $p) {
        if (($p['edge'] ?? '') === $edge) {
            $peer = $p;
            break;
        }
    }

    $status = 'error';
    if ($peer) {
        $res = cluster_one_send_message($peer, $body);
        if (!empty($res['ok'])) {
            /* v1.5.0 (H129): Der Status kommt jetzt aus dem GRUND, nicht aus dem
               Fehlen einer Zustellung. Der Rueckfall bleibt 'pending' — fuer
               einen Aufrufer, der (noch) keinen Status mitschickt, verhaelt es
               sich damit wie bisher. */
            $status = (string) ($res['status'] ?? '');
            if ($status === '') {
                $status = !empty($res['delivered']) ? 'delivered' : 'pending';
            }
        }
    }

    wp_safe_redirect(admin_url('admin.php?page=cluster_one_hello&peer=' . rawurlencode($edge) . '&sent=' . $status));
    exit;
});

/* --- v0.4.0: DIE MAILADRESSE DES SYSOPS ---------------------------------------
   Zwei Knoten haben genau EINEN Kanal: den Hallo-Chat ueber eine Kante. Wer
   draussen unsichtbar bleibt — kein Impressum, kein Formular, der Haken bei
   "nicht indizieren" stehen gelassen — hat keinen Weg zurueck ins normale Netz.
   Und genau das ist der Standardfall fuer jemanden, der gerade erst anfaengt.

   Deshalb ein Knopf, der die eigene Adresse in den Chat legt. Er tippt nichts,
   was man nicht selbst tippen koennte — er sagt, dass es hier vorgesehen ist.
   Dieselbe Figur wie bei "Menschen, die ich einladen will".

   SYSOP ZU SYSOP. Der Knopf ist keine Bequemlichkeit, sondern eine Ansage:
   "Ich bin hier zustaendig." Zustaendig ist nur der SysOp — er hat den Knoten
   aufgesetzt, er zahlt, er antwortet. Beschirmte duerfen ihre Adresse
   selbstverstaendlich hintippen, aber dann ist es privat und ohne Gewaehr.
   Der Unterschied ist nicht erlaubt gegen verboten, sondern amtlich gegen
   privat — und deshalb traegt die Nachricht eine Kennzeichnung.

   Die Adresse reist NICHT im Ping, steht NICHT im Mesh-Steckbrief und wandert
   NICHT ins Profil. Sie geht von einem Menschen an einen Menschen, in einem
   Gespraech, das beide fuehren wollten. */
add_action('admin_post_cluster_one_hello_mail', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_hello_mail');

    $edge = isset($_POST['peer']) ? sanitize_text_field(wp_unslash($_POST['peer'])) : '';
    $back = admin_url('admin.php?page=cluster_one_hello&peer=' . rawurlencode($edge));

    $u    = get_userdata(cluster_one_main_admin_user_id());
    $mail = $u ? sanitize_email((string) $u->user_email) : '';
    if ($mail === '' || !is_email($mail)) {
        wp_safe_redirect(add_query_arg('mail', 'none', $back));
        exit;
    }
    $peer = null;
    foreach (cluster_one_get_peers() as $p) {
        if (($p['edge'] ?? '') === $edge) {
            $peer = $p;
            break;
        }
    }
    if (!$peer) {
        wp_safe_redirect(add_query_arg('mail', 'no_edge', $back));
        exit;
    }
    /* Die Kennzeichnung reist MIT dem Text — die Gegenseite soll unterscheiden
       koennen, ob sie eine amtliche Auskunft bekommen hat oder eine private
       Zeile, die genauso aussieht. */
    /* v0.4.3: Der Text ist jetzt reine Anzeige und darf uebersetzt werden — die
       Kennzeichnung reist als Feld 'sysop_mail'. Vorher stand sie IM Text, und
       weil der uebersetzt wird, kam beim Empfaenger "[SysOp-Kontakt]" an,
       waehrend er "[SysOp contact]" suchte. */
    $res = cluster_one_send_message($peer, sprintf(
        /* translators: %s: e-mail address */
        __('Contact for the operator of this node: %s', 'cluster-one-kleinrock'),
        $mail
    ), 'sysop_mail');
    wp_safe_redirect(add_query_arg('mail', !empty($res['ok']) ? 'sent' : 'error', $back));
    exit;
});

/* ============================================================
   STECKBRIEF UND ALLIANZ (v0.9.59, Umzug aus kranz.php)
   Zwei Bloecke, die dem NETZ gehoeren und in den Einstellungen wohnten:
   Was andere Knoten ueber diesen erfahren — und der Akt, mit dem ueberhaupt
   erst ein anderer Knoten dazukommt.

   Christian hat es von Hand gefunden: 'Das Paaren von mindestens einem
   Knoten ist essentiell. Ohne diese Funktion kann Kleinrock nicht als Modul
   funktionieren.' Das Werkzeug konnte es nicht sehen, weil zwischen einem
   Formular und seinem Handler kein AUFRUF liegt, sondern eine Verabredung
   (siehe konfiguration.php, Knoepfe und Wege).

   Kranz setzt die Bloecke weiterhin auf seiner Seite zusammen. Zusammensetzen
   ist erlaubt; BESITZEN war der Fehler.
   ============================================================ */

/* v0.2.3: Steckbrief-Felder mit Einzelfreigabe. Formular, kein eigener Handler
   — es haengt an der bestehenden Einstellungsseite. */
/* v1.1.0 — DER ABSCHNITT MELDET SICH SELBST AN.
   Bis hierher rief kranz.php cluster_one_settings_seite_steckbrief() nackt auf.
   Der Abschnitt liegt aber seit 0.2.3 hier, in Kleinrocks Haelfte — und auf
   einem Knoten ohne Kleinrock fiel damit Mammuts Einstellungsseite um
   („Call to undefined function", kranz.php:602, seit dem ersten Schnitt
   gemessen). Jetzt ist es andersherum: wer da ist, meldet sich; wer fehlt,
   fehlt nur mit seinem Abschnitt.
   Die Ordnungszahl steht hier, nicht im Kranz — sonst waere die Reihenfolge
   wieder eine Liste, die jemand pflegen muss. 50 = nach den Hausabschnitten.

   v1.0.13 (VERTRAG 30 §1/§3, 23.09.2026) — FORM B, ANGEHAENGT STATT ZUGEWIESEN.
   Bis hierher stand hier:

       $c1_a[50] = 'cluster_one_settings_seite_steckbrief';

   DER PLATZ WAR DIE ORDNUNG UND ZUGLEICH DER SCHLUESSEL. Das ist derselbe
   Fehler wie beim Raeumer (R-SAMMLER-1, am 17.09. dort repariert), an einer
   zweiten Stelle — und es ist ein Fehler, der heute nicht auftritt: 50
   Kleinrock, 55 Boden, 60 Crockford, drei Anmelder auf drei Zahlen.

       Ein Fehler, der heute nicht auftritt, ist kein anderer Fehler.
       Er ist derselbe, ungezaehlt.                           (VERTRAG 30 §1)

   `ord` IST JETZT NUR NOCH DIE ORDNUNG, und sie bleibt 50 — die Reihenfolge
   aendert sich durch diesen Umbau nicht. Neu ist `wirt`: aus welchem PAKET
   der Abschnitt kommt. Ohne ihn hiesse eine Kollisionsmeldung „zwei
   Abschnitte auf 50" statt „deiner und seiner".

   UND DER SAMMLER IST ABSICHTLICH ANDERS ALS DAS RAEUMER-REGISTER: dort sind
   zwei Anmeldungen auf EIN Produkt ein Widerspruch, hier sind zwei
   Abschnitte auf einer Seite zwei Abschnitte. Beide werden gezeichnet.
   Gemessen an diesem Paket, M110 D2.

   ICH HABE DIE FORM EINE RUNDE LANG NICHT GEBAUT, und der Grund steht in
   ENTSCHEIDUNG 27 §0: VERTRAG 30 trug den Bauauftrag und vier Absaetze
   weiter einen uebernommenen Warteauftrag. Ich habe den vorsichtigen
   genommen. Die Form ist seit ENTSCHEIDUNG 27 §0 freigegeben.

   DIE ALTE FORM WIRD NICHT STILL MITGELESEN — wer sie schickt, bekommt ein
   benanntes Loch auf der Seite statt eines Abschnitts. Genau dieses Loch
   war meines: M110 D8 gegen mein Paket, vor dieser Zeile ROT mit
   „registered in the old form (assigned under its ordinal)". */
/* 19.10.2026 (Neo) — DER STECKBRIEF MELDET SICH NICHT MEHR BEIM BODEN AN.
   Bis heute stand hier add_filter('cluster_one_kranz_abschnitte', …) mit
   Ordnung 50: der Abschnitt erschien auf der Boden-Seite „Einstellungen".
   Was andere Knoten ueber diesen erfahren, ist Sache des Netzes — der
   Abschnitt steht jetzt auf Mesh → Einstellungen, direkt aufgerufen von
   cluster_one_render_mesh_settings(). Die Anmeldeform bleibt dem Boden
   erhalten; nur dieser Anmelder ist fort. */

function cluster_one_settings_seite_steckbrief(array $opts): void { ?>
        <hr style="margin:2em 0;">
        <h2><?php esc_html_e('Node profile', 'cluster-one-kleinrock'); ?></h2>
        <p class="description" style="max-width:760px;"><?php esc_html_e('What other nodes learn about this one. Each field travels only if you release it, and only to nodes you are actually connected to — the ping is token-protected, unlike the public label route. Nothing here reaches the open web.', 'cluster-one-kleinrock'); ?></p>
<?php
/* v0.2.4 (H217) — DIESER ABSCHNITT BRINGT SEIN FORMULAR SELBST MIT.
 *
 * Bis hierher standen die Felder NACKT auf der Kranz-Seite. Das aeussere
 * Formular schliesst vor den angemeldeten Abschnitten; die elf Felder lagen
 * also ausserhalb und wurden beim Absenden nie mitgeschickt. Gemessen von
 * Zeuge W37 am gerenderten HTML: 24 Felder, 13 drin, 11 draussen.
 *
 *     Ein Feld ohne Formular ist keine Einstellung. Es ist eine Anzeige, die
 *     aussieht, als koennte man sie aendern.
 *
 * WARUM admin-post.php UND NICHT options.php MIT EIGENER GRUPPE:
 * Die Felder heissen `cluster_one_settings[...]`, und diese Option ist in
 * kranz.php GENAU EINMAL angemeldet — fuer `cluster_one_settings_group`. Eine
 * zweite Gruppe auf derselben Option hiesse: options.php schreibt sie mit dem,
 * was NUR dieses Formular geschickt hat, und der Sanitizer baut aus einer
 * Whitelist (H113). Gemessen mit dem echten Sanitizer: acht gespeicherte
 * Werte waeren nach einem einzigen Speichern weg, darunter `node_id` — ohne
 * das dieser Knoten nicht paaren kann.
 *
 *     Zwei Formulare auf einer Option sind kein Ordnungsproblem, sondern ein
 *     zweiter Schreibweg auf eine Wahrheit (P1-12).
 *
 * Der Handler unten MISCHT deshalb in den bestehenden Stand, statt ihn zu
 * ersetzen — so wie es die sieben anderen Formulare dieser Seite tun.
 *
 * WAS HIER EINEN TAG LANG ALS OFFEN STAND UND SEIT DEM 05.09. GEBAUT IST (H237):
 * Das aeussere Formular schickt diese Felder weiterhin nicht mit, und der
 * Sanitizer baut aus einer Whitelist. Bis zum 05.09. hiess das: ein Speichern
 * der Einstellungsseite loescht den Steckbrief. Der Durchreich-Zweig dafuer war
 * gefordert und nicht gebaut. Er steht jetzt — in cluster_one_sanitize_settings()
 * (kranz.php), als H219:
 *
 *     Fehlt ein Profilfeld im Eingang, nimmt der Sanitizer den gespeicherten
 *     Stand statt leer — fuer das Feld und fuer seinen share_-Zwilling.
 *
 * UND ER HAT AUF EINEM KNOTEN GEHALTEN, nicht nur im Pruefstand: Christian hat
 * es am 06.09. auf DEV mit echter Datenbank gemessen — Steckbrief gefuellt und
 * gespeichert, danach die Einstellungsseite ohne Aenderung gespeichert, die
 * Werte stehen.
 *
 * `forum_area_meta`, oben im selben Atemzug genannt, faellt NICHT unter H219:
 * es steht nicht in cluster_one_profilfelder(). Es hat seinen eigenen Rueckfall
 * seit v0.7.5, an anderer Stelle im selben Sanitizer. Gemessen mit
 * zeuge-h237-durchreiche.php samt Gegenprobe: beide Zweige halten, und keiner
 * deckt den anderen ab. Schneidet man H219 heraus, fallen die zehn
 * Steckbrief-Schluessel und `forum_area_meta` haelt; schneidet man den Rueckfall
 * heraus, ist es umgekehrt. Wer einen der beiden fuer den anderen haelt, hat
 * einen Zweig zu wenig.
 *
 *     Dieser Absatz hat einen Tag lang das Gegenteil behauptet. Er war wahr, als
 *     er geschrieben wurde, und niemand hat ihn zurueckgenommen, als er es nicht
 *     mehr war. Ein Fehler faellt beim Lesen auf; ein ueberholter Satz liest
 *     sich wie eine Warnung und bremst den naechsten Leser, statt ihn zu warnen.
 */
if (isset($_GET['c1_steckbrief'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
        <div class="notice notice-success inline" style="padding:8px 12px;max-width:760px;"><p style="margin:0;"><?php esc_html_e('Node profile saved.', 'cluster-one-kleinrock'); ?></p></div>
<?php endif; ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="cluster_one_kleinrock_steckbrief">
            <?php wp_nonce_field('cluster_one_kleinrock_steckbrief'); ?>
        <table class="form-table" role="presentation"><tbody>
<?php
/* v1.4.0 — DIE LISTE KOMMT AUS DEM BODEN, DIE WORTE BLEIBEN HIER.
   Bis 1.3.0 stand die Aufzaehlung der Felder hier ein drittes Mal (neben
   kern.php und kranz.php). Wer ein Feld ergaenzte, musste an drei Stellen
   daran denken — und eine davon liegt im Uebersetzungskatalog.
   Jetzt kommt die LISTE aus cluster_one_profilfelder(); nur die
   Beschriftungen wohnen weiter hier, denn sie sind Oberflaeche und
   uebersetzbar. Ein Feld ohne Beschriftung verschwindet nicht still: es
   bekommt seinen Schluessel als Namen und faellt beim Ansehen auf. */
$c1_worte = array(
    'nickname'    => array(__('Nickname', 'cluster-one-kleinrock'), __('A short handle for this node.', 'cluster-one-kleinrock')),
    'description' => array(__('Description', 'cluster-one-kleinrock'), __('One sentence: what happens here.', 'cluster-one-kleinrock')),
    'city'        => array(__('City', 'cluster-one-kleinrock'), __('Location — useful for physical meetings, and it says roughly where you are.', 'cluster-one-kleinrock')),
    'country'     => array(__('Country', 'cluster-one-kleinrock'), __('Location — useful for physical meetings, and it says roughly where you are.', 'cluster-one-kleinrock')),
    /* v1.4.0: Sprachen sind ab jetzt freigebbar (Christian, 31.08.2026).
       Der Zusatz sagt, WOFUER sie gebraucht werden — eine Abwahl soll eine
       informierte sein und nicht eine, nach der etwas leise aufhoert. */
    'languages'   => array(__('Languages', 'cluster-one-kleinrock'), __('Used to match speakers for the Babel fish. Set the list under Settings; unticking this keeps it at home — then no neighbour can pair you up for translation.', 'cluster-one-kleinrock')),
);
foreach (cluster_one_profilfelder() as $c1_k => $c1_art) :
    $c1_m = $c1_worte[$c1_k] ?? array($c1_k, ''); ?>
            <tr>
                <th scope="row"><label for="c1_sb_<?php echo esc_attr($c1_k); ?>"><?php echo esc_html($c1_m[0]); ?></label></th>
                <td>
<?php if ($c1_art['form'] === 'text') : ?>
                    <input id="c1_sb_<?php echo esc_attr($c1_k); ?>" name="cluster_one_settings[<?php echo esc_attr($c1_k); ?>]" type="text" class="regular-text" value="<?php echo esc_attr((string) ($opts[$c1_k] ?? '')); ?>">
<?php else :
    /* v1.5.0 — HIER STAND NUR DIE ANZEIGE, UND DAS WAR DIE LUECKE.
       Bis 1.4.1 zeigte diese Zeile die Sprachliste als <code> an und bot kein
       Feld — waehrend darunter ein Haekchen sie freigab. Der Admin konnte
       freigeben, was er nicht setzen konnte (Holleriths Befund C).
         KAESTCHEN, KEIN TEXTFELD: cluster_one_clean_langs() laesst genau die
       Kuerzel aus 'auswahl' durch und verwirft alles andere STILL. Ein freies
       Textfeld haette den Admin seinen Eintrag verschwinden lassen, ohne ihm
       zu sagen warum — die Defektfamilie dieses Hauses, diesmal absichtlich
       gebaut. Angeboten wird nur, was ankommt.
         Die Liste kommt aus derselben Quelle wie der Reiniger; wer sie
       erweitern will, aendert cluster_one_profilfelder() UND $allowed in
       anti-babel.php. Dass das heute zwei Stellen sind, ist eine benannte
       Grenze und kein Zustand, der so bleiben soll. */
    $c1_sel = (array) ($opts[$c1_k] ?? array());
    $c1_kon = defined('CLUSTER_ONE_' . strtoupper($c1_k)); ?>
                    <fieldset<?php echo $c1_kon ? ' disabled' : ''; ?> style="margin-top:0;">
<?php foreach ((array) ($c1_art['auswahl'] ?? array()) as $c1_code) : ?>
                        <label style="margin-right:1.2em;"><input type="checkbox" name="cluster_one_settings[<?php echo esc_attr($c1_k); ?>][]" value="<?php echo esc_attr($c1_code); ?>" <?php checked(in_array($c1_code, $c1_sel, true)); ?>> <code><?php echo esc_html($c1_code); ?></code></label>
<?php endforeach; ?>
                    </fieldset>
<?php if ($c1_kon) : ?>
                    <?php /* translators: %s: Name der Konstante. */ ?>
                    <p class="description"><?php echo esc_html(sprintf(__('Currently set by the constant %s — the field is read-only here.', 'cluster-one-kleinrock'), 'CLUSTER_ONE_' . strtoupper($c1_k))); ?>
                        <code><?php echo esc_html(implode(', ', cluster_one_self_languages()) ?: '—'); ?></code></p>
<?php endif; ?>
<?php endif; ?>
                    <p><label><input name="cluster_one_settings[share_<?php echo esc_attr($c1_k); ?>]" type="checkbox" value="1" <?php checked(!empty($opts['share_' . $c1_k])); ?>> <?php esc_html_e('Send this to connected nodes', 'cluster-one-kleinrock'); ?></label></p>
                    <p class="description"><?php echo esc_html($c1_m[1]); ?></p>
                </td>
            </tr>
<?php endforeach; ?>
        </tbody></table>
            <?php submit_button(__('Save node profile', 'cluster-one-kleinrock')); ?>
        </form>
<?php }

/* v0.2.4 (H217) — DER SCHREIBWEG ZUM FORMULAR DARUEBER.
 *
 * Er MISCHT: er liest den gespeicherten Stand, ueberschreibt nur die Felder
 * dieses Abschnitts und schreibt zurueck. update_option() ruft danach den
 * Sanitizer, und der sieht den VOLLEN Stand — deshalb faellt nichts heraus.
 *
 * WARUM DIE KONSTANTE EINEN EIGENEN ZWEIG BEKOMMT: Ist
 * CLUSTER_ONE_<FELD> definiert, steht das Feld oben in einem `disabled`
 * fieldset — und ein abgeschaltetes Feld schickt NICHTS. Ohne diesen Zweig
 * loeschte jedes Speichern den gespeicherten Wert.
 *
 *     Ein fehlendes Formularfeld darf gespeicherte Daten nicht stumm leeren.
 *
 * Das ist die Lehre des 0.0.67-Hotfix, die in kranz.php ueber dem Sanitizer
 * steht — hier zum zweiten Mal gebraucht, an einer anderen Stelle.
 *
 * Die Freigabe-Haekchen haben diesen Zweig NICHT: ein nicht angehaktes
 * Kaestchen schickt ebenfalls nichts, und dort ist genau das die Aussage.
 */
/** Was der Steckbrief in den gespeicherten Stand schreibt — die MISCHUNG allein.
 *
 * Sie steht als eigene Funktion da und nicht im Haken, damit sie sich messen
 * laesst: sie nimmt zwei Arrays und gibt eines zurueck, ohne WordPress. Der
 * Haken darunter besorgt nur, was WordPress dazu beisteuert (Rechte, Nonce,
 * Speichern, Umleitung).
 *
 *     Ein Rumpf in einer Closure ist ein Rumpf, den kein Zeuge herausschneiden
 *     kann.
 *
 * DAS ERGEBNIS IST UNGEREINIGT, UND ZWAR MIT ABSICHT (H227). Es gehoert in
 * update_option('cluster_one_settings', …) und nirgendwo sonst hin — dort
 * laeuft cluster_one_sanitize_settings(). Wer diesen Rueckgabewert anzeigt
 * oder weiterschickt, umgeht den einzigen Reiniger, den diese Option hat.
 *
 * @param array $c1_o gespeicherter Stand von `cluster_one_settings`
 * @param array $c1_p was dieses Formular geschickt hat (`cluster_one_settings[…]`)
 * @return array ROHER Stand — erst update_option() macht ihn sauber
 */
function cluster_one_kleinrock_steckbrief_mischen(array $c1_o, array $c1_p): array {
    foreach (cluster_one_profilfelder() as $c1_f => $c1_art) {
        if (!defined('CLUSTER_ONE_' . strtoupper($c1_f))) {
            if (($c1_art['form'] ?? 'text') === 'text') {
                /* v0.2.5 (H227) — HIER STAND EIN ZWEITER REINIGER, UND VIER ZEILEN
                   WEITER UNTEN STAND, DASS ES IHN NICHT GEBEN DARF.
                   Beide Saetze waren meine. Der Code hat gewonnen, weil er
                   zuerst laeuft — und genau das ist der Schaden:

                       Ein zweiter Reiniger VOR dem ersten kann nur wegnehmen,
                       was der erste behalten haette.

                   Heute tun beide dasselbe. Bekaeme `description` morgen
                   sanitize_textarea_field(), stuende die alte Regel weiter
                   hier und gewaenne, ohne dass jemand sie geaendert hat.
                   Der Sanitizer haengt an der OPTION und greift bei JEDEM
                   update_option() — es gibt keinen Weg an ihm vorbei.

                   `is_scalar` bleibt und ist kein Reinigen: es fragt nach der
                   FORM, so wie der Listen-Zweig darunter seit jeher
                   `is_array()` fragt. Ohne diese Frage wird ein Array im
                   Textfeld zur Zeichenkette „Array" — mit PHP-Hinweis.

                       Form gehoert dem Mischer, Inhalt gehoert dem Reiniger. */
                $c1_o[$c1_f] = isset($c1_p[$c1_f]) && is_scalar($c1_p[$c1_f])
                    ? (string) $c1_p[$c1_f]
                    : '';
            } else {
                $c1_o[$c1_f] = isset($c1_p[$c1_f]) && is_array($c1_p[$c1_f]) ? array_values($c1_p[$c1_f]) : array();
            }
        }
        $c1_o['share_' . $c1_f] = !empty($c1_p['share_' . $c1_f]) ? 1 : 0;
    }
    return $c1_o;
}

add_action('admin_post_cluster_one_kleinrock_steckbrief', function () {
    if (!function_exists('cluster_one_is_sysop') || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_kleinrock_steckbrief');

    $c1_p = isset($_POST['cluster_one_settings']) && is_array($_POST['cluster_one_settings'])
        ? (array) wp_unslash($_POST['cluster_one_settings']) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Reiniger laeuft im Options-Sanitizer bei update_option (Kommentar darunter), nicht hier
        : array();

    /* Der Reiniger laeuft im Sanitizer, nicht hier: er wohnt an der Option und
       greift bei JEDEM update_option(). Zweimal reinigen hiesse, die Regel
       zweimal zu schreiben. */
    update_option('cluster_one_settings', cluster_one_kleinrock_steckbrief_mischen(
        (array) get_option('cluster_one_settings', array()),
        $c1_p
    ), false);

    /* 19.10.2026 (Neo): zurueck auf die Seite, auf der das Formular steht. */
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh_settings&c1_steckbrief=1'));
    exit;
});

/* 19.10.2026 (Neo) — DER UEBERSETZER WOHNT BEIM MESH.
   Dieser Abschnitt stand bis heute in kranz.php (Boden) als
   cluster_one_settings_seite_uebersetzer() auf der Seite „Einstellungen".
   Uebersetzer, Freigabe, Hallo-Chat-Schalter und Muttersprache gehoeren dem
   Netz; das Erinnerungs-Intervall, das dort zwischen den Feldern stand, ist
   zu den Raeumen gezogen (bismarck.php). Die Optionsschluessel sind dieselben
   wie vorher; der Reiniger in kranz.php liest aus `_felder`, worueber dieses
   Formular spricht, und laesst alles andere stehen. Die Verweise zeigen auf
   die Seite der KI-Kennungen, eine Boden-Seite, die auf jedem Knoten liegt. */
function cluster_one_kleinrock_seite_uebersetzer(array $opts): void {
    if (!function_exists('cluster_one_settings_felder_feld')) { return; }
    $c1_tr_sel  = (string) ($opts['mesh_translator'] ?? '');
    $c1_tr_ids  = cluster_one_get_ai_identities();
    $c1_tr_frei = $c1_tr_sel !== '' && cluster_one_ai_is_selfhosted($c1_tr_sel);
    $c1_kennungen = esc_url(admin_url('admin.php?page=cluster_one_bismarck_settings'));
    ?>
        <hr style="margin:2.5em 0;">
        <h2><?php esc_html_e('Translator (Secure Anti-Babel)', 'cluster-one-kleinrock'); ?></h2>
        <form method="post" action="options.php" style="max-width:720px;">
            <?php settings_fields('cluster_one_settings_group'); ?>
            <?php cluster_one_settings_felder_feld(array('mesh_translator', 'mesh_translate_all', 'mesh_write_sysop_only', 'mother_tongue')); ?>
            <?php if (empty($c1_tr_ids)) : ?>
                <?php /* translators: %s: Link zur Seite KI-Kennungen. */ ?>
                <p style="opacity:.7;"><?php echo wp_kses_post(sprintf(__('No named AI yet. Give a provider an identity under <a href="%s">AI identities</a>, then it can serve as the mesh translator.', 'cluster-one-kleinrock'), $c1_kennungen)); ?></p>
            <?php else : ?>
                <select name="cluster_one_settings[mesh_translator]">
                    <option value="">— <?php esc_html_e('none', 'cluster-one-kleinrock'); ?> —</option>
                    <?php foreach ($c1_tr_ids as $c1_cid => $c1_uid) :
                        $c1_label = (string) apply_filters('cluster_one_raum', (string) $c1_cid, 'ki-name', (string) $c1_cid); ?>
                        <option value="<?php echo esc_attr($c1_cid); ?>" <?php selected($c1_tr_sel, (string) $c1_cid); ?>><?php echo esc_html($c1_label); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <p class="description"><?php esc_html_e('Node-wide. The named AI that translates incoming mesh messages into your target language. Empty means no translation. Stored for later — not wired up yet; the target language comes with the function.', 'cluster-one-kleinrock'); ?></p>

            <p style="margin-top:.8em;">
            <?php if ($c1_tr_frei) : ?>
                <label>
                    <input type="checkbox" name="cluster_one_settings[mesh_translate_all]" value="1" <?php checked(!empty($opts['mesh_translate_all'])); ?>>
                    <?php esc_html_e('Everyone here may translate, not just me', 'cluster-one-kleinrock'); ?>
                </label>
                <span class="description" style="display:block;margin-top:.3em;"><?php esc_html_e('Allowed because you marked this translator as running on your own machine. Until now only the SysOp could translate — the reason was never rank, it was cost. A translation stays with the message and is then there for both sides of the edge.', 'cluster-one-kleinrock'); ?></span>
            <?php elseif (!cluster_one_has_selfhosted_ai()) : ?>
                <?php /* translators: %s: Link zur Seite KI-Kennungen. */ ?>
                <span class="description"><?php echo wp_kses_post(sprintf(__('Only you can translate. To let the others translate too, an AI on your own machine is needed — mark one under <a href="%s">AI identities</a>.', 'cluster-one-kleinrock'), $c1_kennungen)); ?></span>
            <?php else : ?>
                <?php /* translators: %s: Link zur Seite KI-Kennungen. */ ?>
                <span class="description"><?php echo wp_kses_post(sprintf(__('Only you can translate. You do have an AI on your own machine, but it is not the one set as mesh translator — pick it above, or mark this one under <a href="%s">AI identities</a>.', 'cluster-one-kleinrock'), $c1_kennungen)); ?></span>
            <?php endif; ?>
            </p>

            <p style="margin-top:.8em;">
                <label>
                    <input type="checkbox" name="cluster_one_settings[mesh_write_sysop_only]" value="1" <?php checked(!empty($opts['mesh_write_sysop_only'])); ?>>
                    <?php esc_html_e('Only I may write in hello chats', 'cluster-one-kleinrock'); ?>
                </label>
                <span class="description" style="display:block;margin-top:.3em;"><?php esc_html_e('Off by default: everyone on the white list may write to another node — under their own name, never yours. Tick this if the hello chat should be yours alone. It can never lock you out.', 'cluster-one-kleinrock'); ?></span>
            </p>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="c1_mother"><?php esc_html_e('Mother tongue', 'cluster-one-kleinrock'); ?> <span style="color:#d63638;">*</span></label></th>
                    <td>
                        <input name="cluster_one_settings[mother_tongue]" id="c1_mother" type="text" class="regular-text" list="c1_lang_suggestions" value="<?php echo esc_attr($opts['mother_tongue'] ?? ''); ?>" placeholder="<?php echo esc_attr__('e.g. German', 'cluster-one-kleinrock'); ?>">
                        <datalist id="c1_lang_suggestions">
                            <option value="Deutsch"></option>
                            <option value="English"></option>
                            <option value="Français"></option>
                            <option value="Español"></option>
                            <option value="Italiano"></option>
                            <option value="Português"></option>
                            <option value="Русский"></option>
                            <option value="日本語"></option>
                            <option value="中文"></option>
                        </datalist>
                        <p class="description">
                            <?php esc_html_e('Required for the translator — its target: the language you want incoming messages rendered into. Private, never shared across the network. Free text, so anything works (even Klingon). Stored for later — the translator enforces it once wired.', 'cluster-one-kleinrock'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php if (cluster_one_is_sysop()) : ?>
                <?php submit_button(__('Save', 'cluster-one-kleinrock')); ?>
            <?php endif; ?>
        </form>
<?php
}

function cluster_one_settings_seite_allianz(): void { ?>        <hr style="margin:2.5em 0;">

        <h2><?php esc_html_e('Alliance — pair with a node', 'cluster-one-kleinrock'); ?></h2>
        <p style="max-width:680px;">
            <?php esc_html_e('Enter the base URL of another node that also runs the mesh — any Cluster One package that carries it will do, not necessarily the same one you have. Pairing is mutual and needs no shared token beforehand — both sides verify each other automatically by domain callback. https only; for a subdirectory install include the path, e.g. https://example.com/cms', 'cluster-one-kleinrock'); ?>
        </p>

        <div style="max-width:680px;background:#f6f7f7;border:1px solid #dcdcde;border-radius:8px;padding:12px 14px;margin:1em 0;">
            <p style="margin:0 0 .5em;">
                <?php echo wp_kses_post(__('<strong>No node in sight yet?</strong> Pair with the developer\'s node — the network opens up to you from there:', 'cluster-one-kleinrock')); ?>
            </p>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <code id="c1-bootstrap-url" style="background:#fff;border:1px solid #dcdcde;border-radius:4px;padding:.3em .5em;">https://christianermisch.com/cms</code>
                <button type="button" class="button button-small" id="c1-bootstrap-copy"
                        data-copied="<?php echo esc_attr__('Copied!', 'cluster-one-kleinrock'); ?>"><?php esc_html_e('Copy', 'cluster-one-kleinrock'); ?></button>
            </div>
        </div>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:680px;">
            <?php wp_nonce_field('cluster_one_pair'); ?>
            <input type="hidden" name="action" value="cluster_one_pair">
            <input name="peer_url" type="url" class="regular-text" required
                   placeholder="https://other-node.tld/cms">
            <?php submit_button(__('Pair', 'cluster-one-kleinrock'), 'primary', 'submit', false); ?>
        </form>
        <p style="opacity:.7;font-size:.92em;max-width:680px;margin-top:1em;">
            <?php echo wp_kses_post(__('Paired nodes appear in the node table on the <strong>Cluster One</strong> main page. Banned nodes live under <em>Banned</em>.', 'cluster-one-kleinrock')); ?>
        </p>
        <script>
        (function () {
            var btn = document.getElementById('c1-bootstrap-copy');
            var src = document.getElementById('c1-bootstrap-url');
            if (!btn || !src) return;
            btn.addEventListener('click', function () {
                var text = src.textContent.trim();
                var done = function () { var o = btn.textContent; btn.textContent = btn.getAttribute('data-copied'); setTimeout(function(){ btn.textContent = o; }, 1500); };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(done, function () { fallback(); });
                } else { fallback(); }
                function fallback() {
                    var r = document.createRange(); r.selectNodeContents(src);
                    var s = window.getSelection(); s.removeAllRanges(); s.addRange(r);
                    try { document.execCommand('copy'); done(); } catch (e) {}
                }
            });
        })();
        </script>

<?php
}

/* ============================================================
   KLEINROCK ERKLAERT SICH SELBST (v0.9.66)

   Diese Zeile stand bis 0.9.65 in feynman.php, mitten in der Namenstabelle.
   Dort war sie richtig aufgehoben, solange es ein Haus gab. Seit 0.9.64 gibt
   es zwei, und Cluster One Light traegt kein Feynman — es hatte damit keine
   einzige Fragezeichen-Blase, auch nicht die des einzigen Organs, um das es
   dort ueberhaupt geht.

   Christian am 22.08.2026: "Der einzige Name, den Cluster One Light braucht,
   ist Kleinrock selbst. Im Kleinrock, egal ob im Light oder Original, brauchen
   wir das Fragezeichen mit dem Text genau an der Stelle, wo es beim Original
   schon ist."

   NICHT KOPIERT, UMGEZOGEN. Der Text existiert weiterhin genau einmal. Feynman
   holt ihn sich fuer die Namenstabelle an derselben Position wie immer (vierte
   Zeile, zwischen Platon und Gregor); kern holt ihn sich fuer die Blase, wenn
   Feynman fehlt. Zwei LESER, ein Schreibweg — P1-12 verbietet zwei Wege auf
   dieselbe Wahrheit, nicht zwei Klinken an derselben Tuer (0.9.40).

   Der Bau folgt der Regel, die beim Schneiden der Ruecksetzungen dieselbe ist:
   JEDES ORGAN NENNT DAS SEINE SELBST. Wandern spaeter weitere Zeilen zu ihren
   Organen, aendert sich an dieser Mechanik nichts — nur die Namenstabelle wird
   kuerzer.

   Die Spalten sind die der Namenstabelle:
   0 Name · 1 Namensgeber · 2 Lebensdaten · 3 kurz (warum dieser Name)
   4 Schattenseite · 5 Langtext (die Blase)
   ============================================================ */
function cluster_one_kleinrock_steckbrief(): array {
    return array('Kleinrock', 'Leonard Kleinrock', '*1934',
              __('The mesh. He measured how data finds its way through a network before there was a network.', 'cluster-one-kleinrock'),
              '',
              __('Kleinrock is the mesh: every node can reach out to others from here, and every connection is its own edge. Its namesake is Leonard Kleinrock, born 1934 in New York. In 1969 the first message in human history travelled between two computers — from his laboratory. Cluster One turns any WordPress installation into a node of a decentralised network you can make contact with straight away.', 'cluster-one-kleinrock'));
}

/* 27.10.2026 (Neo), 0.2.53 — DIE VIER SEITEN OHNE PATRON BEKOMMEN IHRE BLASE.
   Hallo, Bannliste, Freundesliste, KI-Vorschlaege tragen cluster_one_page_h1('Hello'
   | 'Banned' | 'Friends' | 'Suggestions') und hatten kein Fragezeichen, weil die
   Namenstabelle nur Steckbriefe kennt. Die Naht ist der Filter cluster_one_hilfetexte
   (Boden 1.17.4, kern.php). Schluessel kleingeschrieben, wie der Boden sie liest.
   Die Saetze sagen, was die Seite TUT und was sie NICHT tut – die Kommentare an den
   Render-Funktionen (lokal, einseitig; Reissbrett mit Absicht leer) sind die Quelle. */
add_filter('cluster_one_hilfetexte', function ($c1_t) {
    if (!is_array($c1_t)) { return $c1_t; }
    $c1_t['hello']       = __('The hello chat is the first line between this node and a node it is paired with: what you write here goes to the other node and is answered there. It is for making contact, not for keeping records; what should stay goes into a thread. If a translator is set up, whoever needs a translation can ask for one.', 'cluster-one-kleinrock');
    $c1_t['banned']      = __('The ban list is local and one-sided: a node you put here is kept away from this node, and it is not told. No request, no consent, no message across the mesh — every node keeps its own list. Striking a node off lifts the ban just as quietly.', 'cluster-one-kleinrock');
    $c1_t['friends']     = __('The friends list is the counterpart of the ban list, just as local and one-sided: no request, no consent, no message across the mesh. Every node keeps its own list, and what it means for you is yours to decide. A friend here is not necessarily one over there.', 'cluster-one-kleinrock');
    $c1_t['suggestions'] = __('AI suggestions is a drawing board, empty on purpose: it waits for labels. Suggesting what might interest you would need an AI check across collected labels, and that does not exist yet. Better an empty page that says so than a function that pretends.', 'cluster-one-kleinrock');
    return $c1_t;
});

/* ============================================================
   DIE ZWEI LOESCHUNGEN DES NETZES (v0.9.67)

   Christian am 22.08.2026: "Eine, die die Knoten loescht, damit sie danach neu
   angelegt werden koennen, und eine, die alles loescht, was Kleinrock sonst
   noch in der DB hinterlaesst."

   WARUM ZWEI UND NICHT EINE. Das Haus hatte bisher genau zwei Loeschungen, und
   keine davon konnte das: `cluster_one_node_reset()` (0.1.5) raeumt den INHALT
   und laesst die Kanten ausdruecklich stehen; die Gefahrenzone (0.0.39) nimmt
   mit einem Praefix-Besen ALLES. Wer nur die Knoten neu aufsetzen wollte, ohne
   seine Arbeit zu verlieren, hatte keinen Knopf — er musste zwischen "zu wenig"
   und "zu viel" waehlen.

   DIE REGEL, NACH DER SIE GESCHNITTEN SIND:

       Eine Loeschung nimmt genau so viel, wie ihr Name nennt —
       und nichts, was daneben liegt.

   Das ist Christians eigener Massstab, uebernommen von der Blaupause: "sie
   loeschte keine gepaarten Chats und Threads, sondern ergaenzt nur die
   Ordnung." Bezeugt von Posten K (loeschung.php), und zwar in BEIDEN Haelften:
   das Genannte muss weg sein, alles andere muss stehen. Ein Zeuge, der nur die
   erste Haelfte prueft, waere mit dem Praefix-Besen zu bestehen.

   WEM WAS GEHOERT — gemessen, nicht vermutet (22.08.2026):
   Kleinrock ist ALLEINIGER SCHREIBER von known_nodes, pending_pairings,
   peer_fingerprints, last_sync, unread_total, klingel_tor, mute_until,
   antitrotz_off. Bei paired_peers schreibt kern mit (nur im Knoten-Reset).

   WAS AUSDRUECKLICH NICHT DAZUGEHOERT:
   · `cluster_one_settings` — Kleinrock LIEST es achtmal und schreibt es NIE.
     Geschrieben wird es von bismarck, kern, kranz und platon. Es ist die
     Identitaet des Knotens, nicht sein Netzzustand. Wer es hier mitnaehme,
     loeschte in Light den Knoten selbst.
   · `cluster_one_label_signal_ts` — DIE EINE STELLE, AN DER DIESE OPTION
     ERKLAERT WIRD. Der Raeumer verweist hierher und sagt es nicht zweimal.
     GESCHRIEBEN von faust.php (:1582 hier, :1803 in faust 1.0.5 — faust
     gehoert Bismarck und mir gemeinsam) und von platon.php (:3237, NUR im
     Bismarck-Paket; platon liegt hier nicht).
     GELOESCHT von kern.php im Knoten-Reset (:1022 hier, :1088 in 1.12.13),
     zusammen mit sieben weiteren Netzoptionen.
     ICH LESE SIE DREIMAL (:883, :3787, :4212) UND FASSE SIE NIE AN.
     Ein Label-Signal ist Inhalt, nicht Netz.

     BIS 1.0.13 STAND HIER „Schreiber sind kern und platon". Beides falsch,
     und beide Haelften auf dieselbe Art: ich hatte kerns Hand auf der
     Option gesehen — aber die Hand LOESCHT, sie schreibt nicht — und ich
     hatte platon in einem Absatz ueber MEIN Paket genannt, in dem es
     platon gar nicht gibt.

         Wer loescht, schreibt nicht. Eine Loeschung ist eine Hand an
         derselben Sache und eine andere Auskunft.

     Mammuts Messung (ZETTEL §2) hat die Stelle gefunden und nennt kern
     gar nicht mehr — sie hat nach `update_option` gesucht. Das ist der
     zweite Teil desselben Fehlers, nur andersherum:

         Ein Werkzeug findet die Gestalt, auf die es gebaut ist, und
         schweigt ueber jede andere.                       (R-WERKZEUG-1)

     Darum steht das Loeschen hier ausdruecklich mit drin. Es hat eine
     Folge, die ein Leser braucht: die Option gehoert in KEINE Liste von
     mir, denn im Knoten-Reset nimmt der Boden sie schon.
   · `cluster_one_suggestions_dropped` — eine MIGRATIONSMARKE. Marken
     ueberleben eine Datenloeschung, sonst laeuft die Migration erneut. Dieselbe
     Regel steht schon im Kopf des Knoten-Resets ("Migrationsmarken" bleiben).
   · `cluster_one_suggestions` und `_order` — seit 0.3.0 tot, von
     cluster_one_drop_legacy_suggestions() entfernt. Sie hier zu nennen hiesse,
     eine alternde Zeugin neu anzulegen.

   UND DIE CHRONIK BLEIBT. Nicht, weil sie ausgenommen waere, sondern weil
   keine dieser Funktionen SQL spricht: Sie ist eine eigene Tabelle. "Ein
   Reset, der seine eigene Spur mitloescht, ist kein Protokoll, sondern ein
   Tagebuch des Taeters." (kern.php, 0.1.5)

   KEINE KNOEPFE HIER. Sichtbarkeit und Rechte sind §6.5 und kommen zuletzt.
   Was hier steht, ist das Werkzeug; wer es in die Hand nehmen darf, wird
   spaeter entschieden.
   ============================================================ */

/** DIE SCHLUESSEL, DIE KLEINROCK GEHOEREN — an EINER Stelle (v1.0.9).
 *
 *  BIS HEUTE STANDEN SIE ZWEIMAL: einmal in `..._knoten_loeschen()`, einmal in
 *  `..._reste_loeschen()`. Mit dem Raeumer waeren es drei geworden, und
 *  `kern.php` sagt im Kopf seines Registers selbst, was das hiesse:
 *
 *      "Ein Sammler, der selbst schreibt, ist eine zweite Wahrheit.
 *       Ein Sammler, der nur ruft, ist ein Verzeichnis."
 *
 *  Also nicht neu bauen, sondern UMZIEHEN (Regel 2): die Namen stehen hier,
 *  die drei Funktionen lesen sie. Eine Liste, drei Leser.
 *
 *      Zwei Listen derselben Sache sind keine zwei Listen. Sie sind eine
 *      Liste und eine Wette darauf, dass niemand nur die eine pflegt.
 *
 *  DIE GRUPPEN SIND DER SCHNITT, nicht die Ordnung. Jede der drei Funktionen
 *  nimmt genau die Gruppen, die ihr Name nennt — "eine Loeschung nimmt genau
 *  so viel, wie ihr Name nennt, und nichts, was daneben liegt":
 *
 *      knoten_loeschen   kanten · handschlaege · karte · sparer
 *      reste_loeschen    schalter
 *      raeumen           ALLE
 *
 *  WAS HIER NICHT STEHT, steht mit Begruendung im Block darueber
 *  (`cluster_one_settings`, `cluster_one_label_signal_ts`) bzw. gehoert dem
 *  Boden. Und die BERECHNETEN Namen (`cluster_one_chat_<edge>`) stehen hier
 *  nicht, weil sie hier nicht stehen KOENNEN: ihre Namen liegen in
 *  `cluster_one_paired_peers`, und nur der Eigentuemer kann sie ausrechnen.
 *
 *  @return array<string,array<int,string>>  Gruppe => Optionsnamen
 */
function cluster_one_kleinrock_schluessel(): array {
    return array(
        // Die Kanten samt Token. kern schreibt mit — aber nur im Knoten-Reset,
        // und dort nur, um abgeleitete Felder aus einer Karte zu streichen,
        // die er nicht angelegt hat. Angelegt wird sie in cluster_one_store_paired().
        'kanten'       => array('cluster_one_paired_peers'),
        // Halboffene Handschlaege — sonst koennte einer noch zuschlagen.
        'handschlaege' => array('cluster_one_pending_pairings'),
        'karte'        => array('cluster_one_known_nodes'),
        // Abgeleitetes: gilt je Kante oder je Netzlauf und stuende sonst falsch.
        'sparer'       => array(
            'cluster_one_peer_fingerprints',
            'cluster_one_last_sync',
            'cluster_one_unread_total',
        ),
        'schalter'     => array(
            'cluster_one_klingel_tor',
            'cluster_one_handschlag_tor',   // 18.10.2026 (Neo), B2 — dieselbe Bremse am /pair
            'cluster_one_mute_until',
            'cluster_one_antitrotz_off',
        ),
        /* v1.0.16: WER SEINE TREFFEN-ZEILE SCHON ABHOLT. Je Wirt ein Datum,
           abgeleitet aus fremden Anfragen — ohne meine Kanten und ohne meine
           Treffen sagt sie nichts mehr. Sie steht hier, damit sie nicht
           still liegen bleibt; der default-Zweig des Raeumers zaehlt sie
           unter `spuren`, und das ist richtig: es ist eine Spur und kein
           Inhalt. */
        'abholung'     => array('cluster_one_meetings_abholung'),
        // Seit 0.3.0 tot und von cluster_one_drop_legacy_suggestions() genommen
        // — NUR die Gruppe unten nennt sie, nicht die beiden aelteren Funktionen.
        'altlast'      => array(
            'cluster_one_suggestions',
            'cluster_one_suggestions_order',
        ),
        // Zwei Wanderungsmarken. Die Begruendung, warum sie MITGEHEN, steht
        // im Kopf von cluster_one_kleinrock_raeumen().
        'marken'       => array(
            'cluster_one_suggestions_dropped',
            'cluster_one_url_migration',
        ),
    );
}

/** Die Namen einer oder mehrerer Gruppen, flach. */
function cluster_one_kleinrock_schluessel_von(array $c1_gruppen): array {
    $c1_alle = cluster_one_kleinrock_schluessel();
    $c1_aus  = array();
    foreach ($c1_gruppen as $c1_g) {
        foreach ($c1_alle[$c1_g] ?? array() as $c1_n) {
            $c1_aus[] = $c1_n;
        }
    }
    return $c1_aus;
}

/** Die Knoten weg, damit sie neu angelegt werden koennen.
 *
 *  DAS IST DIE LAUTE VON BEIDEN, und sie bricht mit Absicht eine Regel, die
 *  der Knoten-Reset ausdruecklich einhaelt: "Eine Kante gehoert ZWEI Knoten.
 *  Sie hier zu loeschen waere ein stiller Schaden auf der Gegenseite."
 *
 *  Genau das passiert hier — und es ist gewollt, weil der ganze Zweck ist,
 *  wieder von vorn paaren zu koennen. Aber es ist kein stiller Schaden mehr,
 *  sondern ein angesagter: Die Zaehlung nennt, was verloren geht.
 *
 *  Zwei Dinge, die man wissen muss, bevor man drueckt:
 *
 *  1. DIE GEGENSEITE HEILT SICH. `cluster_one_store_paired()` sucht eine
 *     bestehende Kante ueber die URL und ueberschreibt deren Token. Drueben
 *     entsteht also keine Leiche — die alte Kante wird beim Wiederpaaren
 *     ueberschrieben, Verlauf und "Freund" ueberleben dort (P1-9, 0.0.61).
 *  2. DIE HALLO-CHATS GEHEN MIT. Sie liegen unter `cluster_one_chat_<edge>`.
 *     Ist die Karte leer, vergibt ein Wiederpaaren eine FRISCHE Kanten-ID —
 *     die alten Optionen waeren danach fuer immer unerreichbar: kein Codeweg
 *     kann sie je wieder benennen. Sie stehen zu lassen hiesse nicht
 *     "Verlauf bewahren", sondern Muell hinterlassen, den nur der Praefix-Besen
 *     noch findet.
 *
 *  UND DESHALB ZAEHLT DIE FUNKTION DIE SYSOP-ADRESSEN MIT. Seit 0.4.0 wird
 *  eine im Hallo-Chat uebergebene Adresse als EIGENSCHAFT DER KANTE abgelegt,
 *  ausdruecklich, um "den Weg zurueck ins Netz zu sichern". Wer die Kanten
 *  wirft, wirft diese Wege mit. Der Mensch muss das VORHER sehen, nicht
 *  nachher vermissen.
 *
 *  @return array{kanten:int,chats:int,mails:int,zeiger:int}
 */
/* ================ EINE STELLE, ZWEI KNOEPFE (v1.0.18) =====================
   Christian am 28.09.2026: „Bitte beides zur Verfuegung stellen." Zwei
   Knoepfe, und Mammuts Messung dazu ist die halbe Antwort:

       Der Unterschied der zwei Knoepfe ist eine Liste von Gruppen und
       kein Umbau.

   ALSO EIN ARBEITER UND ZWEI TUEREN. Die Alternative waere eine zweite,
   fast gleiche Loeschfunktion gewesen — genau das, was ich in 1.0.17 beim
   Hoechstwert herausgenommen habe.

       R-RIEGEL-2: Ein Riegel, der zweimal gebaut ist, ist zweimal zu
       pruefen. Und die zweite Kopie wird es nicht.

   UND DAS IST KEINE „DRITTE FUNKTION, DIE BEIDES ZUSAMMEN MACHT" — die
   verbietet mein eigener Kommentar bei reste_loeschen() und zu Recht: eine
   SUMME ist kein eigenes Ding. Hier ist es umgekehrt: EIN Ding mit zwei
   Reichweiten, und die Reichweite steht im Aufruf.

   ================== WAS IN DER ZAHL STEHT, UND WAS NICHT =================
   Ein Schluessel steht nur da, wenn er etwas GEMESSEN hat. Wer die Karte
   nicht anfasst, meldet kein `zeiger => 0` — dieselbe Regel wie im Raeumer:

       Eine Null ist eine Messung. Ein fehlender Schluessel ist eine
       Auskunft darueber, dass nicht gemessen wurde.

   =================== UND DIE TUER DER SPEICHER-SCHICHT ====================
   Wer Chats nehmen will, braucht `cluster_one_delete_chat()`. Bis 1.0.17
   rief diese Funktion sie NACKT — derselbe ungeriegelte Ruf, den mir
   ENTSCHEIDUNG 25 am Raeumer nachgewiesen hat, an der zweiten Stelle.
   Jetzt geriegelt, und der Ausfall faellt auf die sichere Seite:

       Fehlt die Tuer, bleiben die Chats UND die Kanten stehen. Die Namen
       der Chats stehen in cluster_one_paired_peers — nimmt man die Kante,
       kann sie niemand mehr benennen.
           Was man spaeter noch benennen kann, ist ein Rest.
           Was niemand mehr benennen kann, ist Muell.

   @param array<int,string> $c1_gruppen  Gruppen aus cluster_one_kleinrock_schluessel()
   @param bool              $c1_chats    gehen die Hallo-Chats der Kanten mit?
   @return array<string,int|bool>        was => wieviel, plus 'tuer' wenn sie fehlte
   ========================================================================== */
function cluster_one_kleinrock_netz_loeschen(array $c1_gruppen, bool $c1_chats = false): array {
    $c1_zahl = array();

    if ($c1_chats && !cluster_one_kleinrock_chat_tuer()) {
        /* Kein Griff, keine Zahl, und es wird gesagt — wie im Raeumer. */
        $c1_chats   = false;
        $c1_gruppen = array_values(array_diff($c1_gruppen, array('kanten')));
        $c1_zahl['tuer'] = false;
        if (function_exists('_doing_it_wrong')) {
            _doing_it_wrong(
                'cluster_one_kleinrock_netz_loeschen',
                'speicher.php ist nicht geladen (cluster_one_delete_chat fehlt). '
              . 'Die Hallo-Chats bleiben liegen, und die Kanten bleiben stehen, '
              . 'damit die Chats auffindbar bleiben.',
                '1.0.18'
            );
        }
    }

    if (in_array('kanten', $c1_gruppen, true)) {
        $c1_zahl['kanten'] = 0;
        $c1_zahl['mails']  = 0;
        if ($c1_chats) {
            $c1_zahl['chats'] = 0;
        }
        $c1_map = get_option('cluster_one_paired_peers', array());
        foreach ((is_array($c1_map) ? $c1_map : array()) as $c1_edge => $c1_e) {
            $c1_zahl['kanten']++;
            $c1_eid = (string) (is_array($c1_e) && isset($c1_e['edge']) ? $c1_e['edge'] : $c1_edge);
            // Durch die Tuer, nicht am Schloss vorbei: cluster_one_delete_chat()
            // ist seit 0.9.67 die Loeschtuer der Speicher-Schicht und sagt
            // selbst, ob dort etwas lag.
            if ($c1_chats && cluster_one_delete_chat($c1_eid)) {
                $c1_zahl['chats']++;
            }
            if (is_array($c1_e) && trim((string) ($c1_e['sysop_mail'] ?? '')) !== '') {
                $c1_zahl['mails']++;
            }
        }
    }

    if (in_array('handschlaege', $c1_gruppen, true)) {
        /* v1.0.18 NEU IN DER ZAHL. Bis hierher wurden die halboffenen
           Handschlaege geloescht und nicht gezaehlt — der Knopf soll aber
           sagen, was er genommen hat, und einer davon nimmt fast nur sie. */
        $c1_off = get_option('cluster_one_pending_pairings', array());
        $c1_zahl['handschlaege'] = is_array($c1_off) ? count($c1_off) : 0;
    }

    if (in_array('karte', $c1_gruppen, true)) {
        $c1_karte = get_option('cluster_one_known_nodes', array());
        $c1_zahl['zeiger'] = is_array($c1_karte) ? count($c1_karte) : 0;
    }

    // v1.0.9: die Namen stehen in cluster_one_kleinrock_schluessel().
    // v1.0.18: WELCHE Gruppen, sagt der Aufrufer. Gezaehlt wird vorher,
    // geloescht nachher — sonst zaehlte man, was schon weg ist.
    foreach (cluster_one_kleinrock_schluessel_von($c1_gruppen) as $c1_o) {
        delete_option($c1_o);
    }

    return $c1_zahl;
}

/** KNOPF A — „Alle Verbindungen loesen". Kanten und halboffene Handschlaege.
 *
 *  DIE KARTE BLEIBT, und das ist der ganze Sinn. Christian am 28.09.2026:
 *  „Ich wollte die Moeglichkeit haben, mich mit allen Knoten zu entpaaren,
 *  um mich dann wieder mit einem davon zu paaren." Die Adressen bleiben also
 *  stehen, und Wiederpaaren ist ein Klick und keine Neueingabe.
 *
 *  DIE HALLO-CHATS BLEIBEN AUCH. Entpaart heisst dauerhaft offline, nicht
 *  vergessen — wer wiederpaart, findet sein Gespraech vor.
 *      Umkehrbar, deshalb ohne Rueckfrage.
 */
function cluster_one_kleinrock_verbindungen_loesen(): array {
    return cluster_one_kleinrock_netz_loeschen(array('kanten', 'handschlaege'), false);
}

/** KNOPF B — „Netz vollstaendig zuruecksetzen". Alle vier Gruppen und die
 *  Hallo-Chats. Danach muss eine Adresse neu eingetippt werden.
 *      Nicht umkehrbar, deshalb mit Rueckfrage.
 *
 *  v1.0.18: DER NAME UND DIE REICHWEITE BLEIBEN, der Rumpf ist eine Zeile.
 *  Diese Funktion war seit 1.0.9 fertig und wurde von NIRGENDWO gerufen —
 *  eine fertige Tuer ohne Klinke (Mammuts Messung, 28.09.). Sie bekommt
 *  heute ihre Klinke und behaelt ihren Namen.
 *
 *  @return array{kanten:int,chats:int,mails:int,handschlaege:int,zeiger:int}
 *          v1.0.18: `handschlaege` ist neu darin — mehr Auskunft, kein
 *          anderer Griff.
 */
function cluster_one_kleinrock_knoten_loeschen(): array {
    return cluster_one_kleinrock_netz_loeschen(
        array('kanten', 'handschlaege', 'karte', 'sparer'),
        true
    );
}

/** Was Kleinrock sonst noch liegen laesst.
 *
 *  DIE LEISE VON BEIDEN. Nichts hiervon ist Inhalt — es sind drei Zustaende,
 *  die sich im Betrieb ansammeln und die niemand vermisst:
 *
 *    klingel_tor    Zaehler der Klingel-Bremse (30/Stunde, 300 s Pause)
 *    mute_until     globaler Zeitschalter "gerade nicht stoeren"
 *    antitrotz_off  die einmal weggeklickte Rueckfrage
 *
 *  Die Karte, die Kanten und die Chats fasst sie NICHT an — dafuer ist die
 *  andere da. Wer beide will, ruft beide; es gibt keine dritte, die es
 *  "zusammen" macht. Eine Summe ist kein eigenes Ding und braucht keine eigene
 *  Liste, die veralten koennte.
 *
 *  @return array{schluessel:int}
 */
function cluster_one_kleinrock_reste_loeschen(): array {
    $weg = 0;
    // v1.0.9: dieselben drei Namen, jetzt aus cluster_one_kleinrock_schluessel().
    foreach (cluster_one_kleinrock_schluessel_von(array('schalter')) as $o) {
        if (get_option($o, null) !== null) {
            $weg++;
        }
        delete_option($o);
    }
    return array('schluessel' => $weg);
}

/** DER RAEUMER (v1.0.9) — alles, was Kleinrock gehoert, zaehlen oder nehmen.
 *
 *  DIE FORM IST DER VERTRAG AUS `kern.php` (1.9.13), woertlich:
 *
 *      function <name>(bool $tun): array      Rueckgabe: was => wieviel
 *          $tun === false   ZAEHLEN. Kein Byte wird angefasst.
 *          $tun === true    RAEUMEN.
 *
 *  Drei rufen ihn, und alle drei mit demselben einen Schalter:
 *      Gefahrenzone im Produkt · Knoten-Reset im Boden · uninstall.php.
 *
 *  ER NENNT DINGE, NICHT ZEILEN. "Niemand vermisst eine Option, man vermisst
 *  seine Einstellungen" (Post Dolly) — also `chats`, `zeiger`, `handschlaege`,
 *  `schalter` und `spuren`.
 *
 *  ================= WEM WAS GEHOERT, und jede Zeile einzeln =================
 *
 *  Mammuts Liste (RUNDBRIEF 19 §4) nennt zwoelf. Ich habe sie gegen MEINE
 *  Datei nachgemessen (NACHTRAG 19a §4) und komme auf dieselben zwoelf; mein
 *  Werkzeug kennt zusaetzlich die Listen-Gestalt aus 19a §1 und findet in
 *  `kleinrock.php` DREI Optionsrufe mit nicht-literalem Namen — alle drei
 *  genau diese Gestalt. Es bleibt keine Stelle uebrig, die keiner sieht.
 *
 *  UND EINE, DIE ER LIEST UND NICHT NIMMT:
 *
 *  · `cluster_one_paired_peers` — BLEIBT STEHEN, AUSSER BEIM DEINSTALLIEREN
 *    (ENTSCHEIDUNG 20 E4 · ENTSCHEIDUNG 25 §4/2, Posten 2).
 *
 *    ================= POSTEN 2, und hier ist meine Antwort =================
 *
 *    BEIM KNOTEN-RESET UND IN DER GEFAHRENZONE BLEIBT SIE. E4 gilt
 *    unveraendert: „Eine Kante gehoert ZWEI Knoten. Sie hier zu loeschen
 *    waere ein stiller Schaden auf der Gegenseite."
 *
 *    BEIM DEINSTALLIEREN GEHT SIE MIT, und der Grund ist nicht, dass mir die
 *    Regel dort weniger gefaellt, sondern dass ihre BEGRUENDUNG dort nicht
 *    mehr trifft. Drei Messungen an meiner eigenen Datei:
 *
 *      1 WAS IN DER KANTE STEHT. cluster_one_store_paired() legt je Kante
 *        ab: 'token' — das gemeinsame Geheimnis mit dem anderen Knoten —,
 *        dazu 'url', 'name' und (seit 0.4.0) die SysOp-Adresse.
 *            Ein Geheimnis, das zwei gehoert, in der Datenbank eines
 *            Systems zu lassen, dem man gerade den Code genommen hat,
 *            ist kein Bewahren. Es ist ein Depot ohne Waechter.
 *
 *      2 DER STILLE SCHADEN TRITT OHNEHIN EIN. Deinstallieren nimmt der
 *        Gegenseite die Kante — ob ich meine Haelfte behalte oder nicht.
 *        Behalten hilft ihr nicht; es laesst nur ihren Token liegen.
 *            E4 schuetzt den Nachbarn vor MEINEM Aufraeumen. Beim
 *            Deinstallieren schuetzt es ihn vor nichts und bewahrt sein
 *            Geheimnis.
 *
 *      3 UND ES IST DER EINZIGE REST. Nach dem Raeumen bleibt von mir sonst
 *        nichts stehen; Paciolis Form, die dieses Haus geerbt hat, sagt
 *        „genau eine Option bleibt uebrig, und sie ist absichtlich eine" —
 *        und das ist die Narbe, nicht meine Kantenkarte.
 *
 *    ES IST EINE ENTSCHEIDUNG UND UMKEHRBAR. Soll die Kante auch das
 *    Deinstallieren ueberleben, gibt cluster_one_kleinrock_raeumer_laesst_stehen()
 *    wieder unbedingt array('kanten') zurueck — eine Zeile.
 *
 *    UND EINE KOPPLUNG, DIE ICH BEIM BAUEN GEFUNDEN HABE und die wichtiger
 *    ist als die Entscheidung selbst: FEHLT DIE SPEICHER-TUER, BLEIBT DIE
 *    KANTE AUCH BEIM DEINSTALLIEREN STEHEN. Sonst loeschte ich die Karte,
 *    aus der die Namen der `cluster_one_chat_<edge>` berechnet werden,
 *    waehrend die Chats noch da sind — und danach kann sie niemand mehr
 *    benennen. Genau der Fall, den der Kopf dieser Datei seit 0.0.61 als
 *    P1-9 beschreibt.
 *            Was man spaeter noch benennen kann, ist ein Rest. Was niemand
 *            mehr benennen kann, ist Muell.
 *
 *    =======================================================================
 *    Ich hatte sie fuer meine erklaert, mit zwei Gruenden: sie wird in
 *    `cluster_one_store_paired()` angelegt (diese Datei), und aus ihr lese
 *    ich die Namen der `cluster_one_chat_<edge>`. Beide Gruende stehen
 *    weiter — und beide beantworten die falsche Frage.
 *
 *        Es ist keine EIGENTUMSfrage, sondern eine REICHWEITENfrage.
 *        "Eine Kante gehoert ZWEI Knoten. Sie hier zu loeschen waere ein
 *         stiller Schaden auf der Gegenseite."          (kern.php, 0.1.5)
 *
 *    Ich habe selbst gemeldet, dass mein Raeumer und der Satz des Bodens
 *    ("Settings and edges were kept") einander widersprechen, sobald der
 *    Knoten-Reset das Register mitruft. Der Widerspruch war echt; aufgeloest
 *    wird er andersherum, als ich ihn gestellt hatte — nicht der Satz gibt
 *    nach, sondern meine Reichweite. Der Satz steht beim Knoten-Reset, und
 *    beim Knoten-Reset ist die Konstante nicht gesetzt: er bleibt wahr.
 *        Wer meldet, dass zwei Dinge nicht zusammenpassen, hat damit noch
 *        nicht gesagt, welches von beiden nachgibt.
 *
 *    DER CHAT GEHT TROTZDEM. Er ist Inhalt dieses Organs, liegt unter einem
 *    Namen, den nur ich berechnen kann, und die Kante traegt ihn nicht — sie
 *    nennt ihn nur. Dieselbe Teilung wie im Knoten-Reset.
 *
 *    UNANGETASTET BLEIBT DAMIT AUCH die SysOp-Adresse: sie ist eine
 *    Eigenschaft der Kante, und die Kante bleibt. Der Raeumer zaehlt sie
 *    darum nicht mehr mit — was nicht weggeht, gehoert in keine Liste
 *    dessen, was weggeht.
 *
 *    `..._knoten_loeschen()` nimmt die Kanten WEITERHIN. Sie ist kein
 *    Raeumer, sondern Kleinrocks eigener Knopf, ihr Name sagt genau das, und
 *    ihr Kopf begruendet den Bruch seit dem 22.08. E4 gilt fuer RAEUMER.
 *
 *  DIE SIEBEN AUS NACHTRAG 19a §2, die auch in der festen Liste des
 *  Knoten-Resets stehen — einzeln, und die Frage ist jedes Mal dieselbe:
 *  gehoert das dem NETZ (mir) oder der IDENTITAET des Knotens (nicht mir)?
 *
 *    DIE IDENTITAET HABE ICH GESUCHT, STATT SIE ZU VERMUTEN:
 *    `cluster_one_node_id()` liest `cluster_one_config('CLUSTER_ONE_NODE_ID',
 *    'node_id')`, und das liest `cluster_one_settings['node_id']`.
 *    Die Identitaet des Knotens liegt in `cluster_one_settings` — der Option
 *    mit vier Schreibern, die ich NICHT anfasse. In meinen zwoelf liegt sie
 *    nicht. Damit ist Mammuts Sorge beantwortet und nicht weggeredet.
 *
 *  · `cluster_one_known_nodes`   MEINE. Die Karte nennt die ANDEREN, nicht
 *      mich. Sie ist ohne Mesh sinnlos, und das Mesh ist dieses Organ.
 *  · `cluster_one_last_sync`     MEINE. Zeitstempel meines eigenen Laufs.
 *  · `cluster_one_peer_fingerprints` MEINE. Transfer-Sparer je Kante; rein
 *      abgeleitet, kostet beim Verlust genau einen vollen Transfer.
 *  · `cluster_one_unread_total`  MEINE. Abgeleitete Zahl fuer meine Leiste.
 *  · `cluster_one_mute_until`    MEINE. Ein Zeitschalter FUER MEINE Meldungen.
 *      Ihn zu nehmen stellt den unkonfigurierten Zustand her — es schreibt
 *      keine Entscheidung in eine ANDERE um.
 *  · `cluster_one_suggestions`, `cluster_one_suggestions_order`  MEINE, und
 *      hier weiche ich von der Begruendung im Block oben ab: dort steht, sie
 *      zu nennen hiesse "eine alternde Zeugin neu anzulegen", weil
 *      `cluster_one_drop_legacy_suggestions()` sie seit 0.4.7 auf `admin_init`
 *      nimmt. Fuer eine Loeschung mit engem Namen war das richtig. Fuer den
 *      Raeumer nicht:
 *          Eine Loeschung, die einen Namen auslaesst, weil eine Wanderung ihn
 *          schon genommen haben muesste, verlaesst sich auf einen Lauf, den
 *          sie nicht gesehen hat.
 *      Sie zu nennen kostet nichts (`get_option(...) === null` -> weiter);
 *      sie NICHT zu nennen kostet im ungeguenstigen Fall, dass sie eine
 *      Deinstallation ueberleben. Die beiden aelteren Funktionen nennen sie
 *      weiterhin NICHT — ihr Name verspricht weniger.
 *
 *  DIE ZWEI WANDERUNGSMARKEN (§4c, §5). Ich habe beide GELESEN, statt sie
 *  nach ihrer Gattung zu behandeln:
 *    · `cluster_one_suggestions_dropped` -> `..._drop_legacy_suggestions()`
 *      loescht zwei Optionen und setzt die Marke. Beim zweiten Lauf sind
 *      beide schon weg. Wirkung: keine.
 *    · `cluster_one_url_migration` -> `cluster_one_migrate_urls_0969()`
 *      schluesselt die Karte auf normalisierte URLs um (idempotent),
 *      GLAETTET Kanten-URLs ohne je den Host zu wechseln (idempotent) und
 *      wirft die Fingerprints weg (Kosten: ein voller Transfer je Kante).
 *      Nach dem Raeumer ist nichts mehr da, was sie wandern liesse.
 *    BEIDE MEINE.
 *        Eine Wanderungsmarke ist nicht unantastbar, weil sie eine Marke ist,
 *        sondern wenn ihre Wanderung beim zweiten Lauf einem Menschen eine
 *        Entscheidung zurueckstellt. Meine zwei tun das nicht.
 *        Bismarcks `bismarck_to_eddington_v061` tut genau das — und DAS ist
 *        der Unterschied, nicht das Wort "Marke".
 *
 *  NICHT MEINE, und es bleibt dabei: `cluster_one_settings` (vier Schreiber,
 *  darin die Identitaet), `cluster_one_label_signal_ts` (WER SIE SCHREIBT UND
 *  WER SIE LOESCHT, STEHT AN EINER STELLE: im Kopf der zwei Loeschungen,
 *  :5728. Hier stand bis 1.0.13 ein zweiter, kuerzerer Satz — zwei Saetze
 *  ueber dieselbe Option in einer Datei sind zwei Wahrheiten, auch wenn einer
 *  davon stimmt, P1-12), `cluster_one_profile` (kranz), die fuenfzehn
 *  Bodenoptionen und die Staemme `cluster_one_room_chat_`,
 *  `cluster_one_forum_posts_` (Boden).
 *
 *  ================= EINE BENANNTE GRENZE, und sie ist meine ================
 *
 *  `chats` zaehlt Kanten MIT NACHRICHTEN, gelesen durch `cluster_one_get_chat()`.
 *  Eine leere, aber vorhandene Chat-Option wird NICHT gezaehlt und beim
 *  Raeumen trotzdem genommen. Der Grund ist die Tuer: die Speicher-Schicht hat
 *  eine Loeschtuer, die sagt, ob etwas dalag, aber keine LESETUER, die "leer"
 *  von "gar nicht da" unterscheidet — und ihr Kopf sagt, dass nicht mehr
 *  Stellen roh an den Speicher greifen duerfen. Also zaehle ich in der Sprache
 *  des Menschen (ein Chat ist ein Gespraech) und schreibe die Abweichung hier
 *  auf, statt eine dreizehnte rohe Stelle aufzumachen.
 *
 *  @return array<string,int>  was => wieviel.
 *          `zeiger`, `handschlaege`, `schalter`, `spuren` stehen immer da.
 *          `chats`  nur, wenn die Speicher-Tuer erreichbar war.
 *          `kanten` nur, wenn sie wirklich weggehen (beim Deinstallieren).
 *          Ein FEHLENDER Schluessel ist eine Auskunft, keine Null.
 */
/** DIE GRUPPEN, DIE DER RAEUMER NICHT NIMMT — und warum (E4).
 *
 *  Sie steht als eigene Funktion da, damit der Waechter sie lesen kann
 *  (zeuge-raeumer.php R8). Eine Ausnahme, die nur im Rumpf steht, ist von
 *  einem Versehen nicht zu unterscheiden.
 *
 *  @return array<int,string>
 */
function cluster_one_kleinrock_raeumer_laesst_stehen(): array {
    /* BEIM DEINSTALLIEREN GEHT DIE KANTE MIT — aber nur, wenn ich ihre Chats
       vorher wegraeumen KANN. Warum beides zusammenhaengt, steht im Kopf von
       cluster_one_kleinrock_raeumen(). */
    if (cluster_one_kleinrock_wird_deinstalliert() && cluster_one_kleinrock_chat_tuer()) {
        return array();
    }
    return array('kanten');
}

/** IST DIE TUER DER SPEICHER-SCHICHT DA? (v1.0.12, ENTSCHEIDUNG 25 §4/1)
 *
 *  `cluster_one_get_chat()` und `cluster_one_delete_chat()` wohnen in
 *  `speicher.php` — einem fremden Organ meines eigenen Pakets. Mein Raeumer
 *  hat sie bis heute UNGERIEGELT gerufen, und Mammuts neue uninstall.php hat
 *  es sichtbar gemacht:
 *
 *      Call to undefined function cluster_one_get_chat()
 *      @ kleinrock.php:6049
 *
 *  HEUTE TRAEGT ES, weil die uninstall.php denselben Satz Organe laedt wie
 *  der Lader. Das ist eine GETRAGENE Abhaengigkeit — und nach meiner eigenen
 *  R-LEIHE-1 ist ein ungeriegelter Ruf genau das: eine Abhaengigkeit, die
 *  mitreisen muss. Sie reist mit. Falsch war nicht der Ruf, sondern dass es
 *  nirgends stand.
 *
 *      Eine Abhaengigkeit, die getragen wird, ist kein Mangel. Eine, die
 *      niemand genannt hat, wird beim naechsten Schnitt fallen gelassen,
 *      ohne dass jemand etwas sagt.                          (Mammut, §3)
 *
 *  UND DER RIEGEL IST NICHT DAS, WONACH ER AUSSIEHT. Ein blosses
 *  `function_exists` mit stillem Weiterlaufen haette meinen LAUTEN Ausfall in
 *  Crockfords STILLE NULL verwandelt — dieselbe ENTSCHEIDUNG 25 §1 nennt
 *  beide nebeneinander, und die stille ist die schlimmere:
 *
 *      Ein Raeumer, der zaehlt, und ein Raeumer, der findet, sind nicht
 *      dieselbe Zahl — und nur eine davon steht in der Narbe.
 *
 *  Also riegelt er UND sagt es: `_doing_it_wrong` (dieselbe Tuer, durch die
 *  `cluster_one_raeumer()` einen verworfenen Raeumer meldet, H218), und die
 *  Zahl `chats` FEHLT dann im Rueckgabefeld, statt 0 zu sein.
 *
 *      Eine Null ist eine Messung. Ein fehlender Schluessel ist eine
 *      Auskunft darueber, dass nicht gemessen wurde. Wer nicht nachsehen
 *      konnte, schreibt nicht „keine".
 *
 *  Das Haus kannte die Form uebrigens schon: `cluster_one_edge_reject()`
 *  riegelt denselben Ruf seit 0.9.40 (diese Datei, :2768). Mein Raeumer hat
 *  eine Regel gebrochen, die in seiner eigenen Datei zwoelfhundert Zeilen
 *  hoeher steht.
 */
function cluster_one_kleinrock_chat_tuer(): bool {
    return function_exists('cluster_one_get_chat') && function_exists('cluster_one_delete_chat');
}

/** WIRD GERADE DIESES PLUGIN DEINSTALLIERT? (v1.0.13, VERTRAG 30 §2/§3)
 *
 *  DIE ANTWORT KOMMT SEIT 1.12.13 VOM BODEN, an der Stelle, an der sie
 *  entschieden wird. Bis 1.0.12 stand hier `defined('WP_UNINSTALL_PLUGIN')`.
 *  Der Wortlaut von damals steht unter dieser Notiz und ist nicht geloescht
 *  (R-PAPIER-1) — er war richtig gemessen und aus der falschen Quelle.
 *
 *  WARUM DIE KONSTANTE NICHT REICHTE, und Mammuts Grund ist schaerfer als
 *  meine eigene Frage vom 23.09. war:
 *
 *      `WP_UNINSTALL_PLUGIN` ist GLOBAL. Sie ist fuer die ganze Anfrage
 *      wahr, auch wenn ein FREMDES Plugin das geloeschte ist. Dass mein
 *      Raeumer dann trotzdem nicht laeuft, liegt an `cluster_one_raeumen_mein()`,
 *      das nach dem Wirt filtert — an einer Eigenschaft des BODENS, ueber
 *      die diese Datei nichts weiss und nichts wissen kann.
 *
 *          Ein Haus, das eine Frage ueber den Boden mit einem Griff nach
 *          WordPress beantwortet, hat die richtige Antwort aus der
 *          falschen Quelle.                                  (Mammut)
 *
 *  ICH HABE ES NACHGEMESSEN, BEVOR ICH ES GEGLAUBT HABE — Mammut hat aus
 *  der Form abgeleitet, was in meiner Datei steht, und ausdruecklich dazu
 *  gesagt, dass er es nicht an ihr gemessen hat. Der Zeuge heisst R10 („die
 *  fremde Deinstallation", zeuge-raeumer.php): Konstante gesetzt, Anlass
 *  'reset'. Der alte Rumpf zaehlt dort `kanten` mit und nimmt sie beim
 *  Griff. Der neue laesst sie stehen. Das ist keine Verschoenerung — das
 *  sind zwei Kanten, die ein Knoten-Reset in der Gefahrenzone mitgenommen
 *  haette, waehrend nebenan ein anderes Plugin geloescht wurde.
 *
 *      Eine Abhaengigkeit, die getragen wird, ist kein Mangel. Eine, die
 *      niemand genannt hat, faellt beim naechsten Schnitt.
 *
 *  GERIEGELT, und der Riegel ist eine LEIHE und keine Abhaengigkeit
 *  (R-LEIHE-1): fehlt `cluster_one_raeumen_anlass()`, ist die Antwort
 *  `false` — also „es wird nicht deinstalliert", also bleiben die Kanten
 *  stehen. Der Ersatz ist benannt und er ist die vorsichtige Seite: ein
 *  Raeumer, der im Zweifel zu WENIG nimmt, laesst einen Rest; einer, der
 *  im Zweifel zu VIEL nimmt, loescht die Gegenseite mit.
 *
 *  AUSSERHALB EINES RAEUMLAUFS IST DIE ANTWORT DES BODENS '' — nicht
 *  'reset', nicht 'uninstall'. Diese Funktion wird nur aus
 *  `cluster_one_kleinrock_raeumer_laesst_stehen()` gerufen und die nur aus
 *  `cluster_one_kleinrock_raeumen()`; sie steht also nie ausserhalb. Der
 *  Riegel deckt den Fall trotzdem mit ab, und zwar in dieselbe Richtung.
 *
 *  ---------------------------------------------------------------------
 *  DER WORTLAUT VON 1.0.12, UEBERHOLT, NICHT GELOESCHT (R-PAPIER-1):
 *
 *  `WP_UNINSTALL_PLUGIN` setzt WordPress selbst, und nur fuer den Lauf der
 *  `uninstall.php`; ihre erste Anweisung ist der Riegel darauf (M108 A2). Es
 *  ist also keine Erfindung von mir, sondern die einzige Auskunft, die es zu
 *  dieser Frage gibt.
 *
 *  WARUM DER RAEUMER SIE UEBERHAUPT STELLT: sein Vertrag hat EINEN Schalter
 *  ($tun) und DREI Rufer — Gefahrenzone, Knoten-Reset, uninstall. Ich habe am
 *  17.09. gemeldet, dass ein Vertrag mit einem einzigen Schalter keine Option
 *  tragen kann, die bei zwei Anlaessen zwei Antworten braucht. Die Kante ist
 *  diese Option. Die Reichweite steht nicht im Vertrag, also frage ich die
 *  Welt — an EINER Stelle, mit einem Namen, und nicht mitten im Rumpf.
 *
 *      Eine Bedingung ohne Namen ist von einer Laune nicht zu unterscheiden.
 *
 *  ZAEHLEN UND RAEUMEN BLEIBEN EINIG: die Konstante aendert sich innerhalb
 *  einer Anfrage nicht, also sagt der Zaehllauf genau das, was der Raeumlauf
 *  tut. (zeuge-raeumer R2, in beiden Welten gefahren.)
 *
 *  DIESER ABSATZ GILT WEITER, nur an einer anderen Quelle: der Anlass des
 *  Bodens aendert sich innerhalb eines Raeumlaufs ebenfalls nicht, also
 *  bleiben Zaehlen und Raeumen einig. R2 misst es unveraendert.
 *  ---------------------------------------------------------------------
 */
function cluster_one_kleinrock_wird_deinstalliert(): bool {
    return function_exists('cluster_one_raeumen_anlass')
        && cluster_one_raeumen_anlass() === 'uninstall';
}

function cluster_one_kleinrock_raeumen(bool $c1_tun = false): array {
    /* KEIN `kanten` UND KEIN `mails` IN DER ZAEHLUNG. Der Raeumer nimmt die
     * Kanten nicht mehr (E4), also darf er sie auch nicht unter dem nennen,
     * was er nimmt. `kanten => 0` waere schlimmer als gar nichts: die Null
     * hiesse "es gibt keine", nicht "ich fasse sie nicht an".
     *     Eine Zahl in einer Liste dessen, was weggeht, sagt mit, dass es
     *     weggeht. Wer etwas stehen laesst, nennt es nicht mit einer Null,
     *     sondern gar nicht. */
    $c1_tuer   = cluster_one_kleinrock_chat_tuer();
    $c1_stehen = cluster_one_kleinrock_raeumer_laesst_stehen();

    /* DIE SCHLUESSEL STEHEN NUR DA, WENN SIE ETWAS MESSEN.
     *   `chats`  nur, wenn die Speicher-Tuer da ist — sonst kann ich nicht
     *            nachsehen, und 0 hiesse „keine" statt „nicht nachgesehen".
     *   `kanten` nur, wenn sie wirklich weggehen (beim Deinstallieren).
     *            Im Betrieb bleiben sie stehen, und was bleibt, gehoert in
     *            keine Liste dessen, was weggeht. */
    $c1_zahl = array();
    if ($c1_tuer) {
        $c1_zahl['chats'] = 0;
    }
    if (!in_array('kanten', $c1_stehen, true)) {
        $c1_zahl['kanten'] = 0;
    }
    $c1_zahl += array(
        'zeiger'       => 0,
        'handschlaege' => 0,
        'schalter'     => 0,
        'spuren'       => 0,
    );

    /* 1 · DIE BERECHNETEN SCHLUESSEL. Sie stehen in keiner Liste — ihre Namen
     *     stehen in cluster_one_paired_peers, und nur der Eigentuemer kann sie
     *     ausrechnen. Genau dafuer gibt es das Register statt einer Liste.
     *
     *     DIE KARTE WIRD HIER NUR GELESEN. E4: "Eine Kante gehoert ZWEI
     *     Knoten. Sie hier zu loeschen waere ein stiller Schaden auf der
     *     Gegenseite." Der Chat geht, die Kante bleibt — dieselbe Teilung,
     *     die cluster_one_node_reset() seit 0.1.5 einhaelt, und derselbe
     *     Grund, aus dem cluster_one_render_reset_notice() dem Menschen
     *     "Settings and edges were kept" sagen darf.
     *
     *     Mein eigener Widerspruch von heute Mittag ist damit aufgeloest,
     *     und zwar andersherum, als ich ihn gestellt hatte: nicht der Satz
     *     des Bodens wird berichtigt, sondern meine Reichweite.
     *         Wer meldet, dass zwei Dinge nicht zusammenpassen, hat damit
     *         noch nicht gesagt, welches von beiden nachgibt. */
    $c1_map = get_option('cluster_one_paired_peers', array());
    if (!is_array($c1_map)) {
        $c1_map = array();
    }
    if ($c1_tuer) {
        foreach ($c1_map as $c1_k => $c1_e) {
            $c1_eid = (string) (is_array($c1_e) && isset($c1_e['edge']) ? $c1_e['edge'] : $c1_k);
            // Durch die Tuer, nicht am Schloss vorbei (speicher.php, v0.9.67).
            if (cluster_one_get_chat($c1_eid) !== array()) {
                $c1_zahl['chats']++;
            }
            if (isset($c1_zahl['kanten'])) {
                $c1_zahl['kanten']++;
            }
            if ($c1_tun) {
                cluster_one_delete_chat($c1_eid);
            }
        }
    } elseif ($c1_map !== array() && function_exists('_doing_it_wrong')) {
        /* KEINE TUER, ALSO KEINE ZAHL UND KEIN GRIFF — und es wird gesagt.
           Die Chats bleiben liegen; sie sind NICHT verloren, weil
           cluster_one_paired_peers in genau diesem Fall stehen bleibt und
           ihre Namen weiter berechenbar sind (siehe raeumer_laesst_stehen()).
               Was man spaeter noch benennen kann, ist ein Rest.
               Was niemand mehr benennen kann, ist Muell. */
        _doing_it_wrong(
            'cluster_one_kleinrock_raeumen',
            sprintf(
                'speicher.php ist nicht geladen (cluster_one_get_chat / '
              . 'cluster_one_delete_chat fehlen). %d Kante(n) behalten ihre '
              . 'Hallo-Chats, und die Kanten bleiben stehen, damit die Chats '
              . 'auffindbar bleiben.',
                count($c1_map)
            ),
            '1.0.12'
        );
    }

    /* 2 · DIE GENANNTEN SCHLUESSEL, aus der EINEN Liste.
     *     Der default-Zweig ist Absicht: eine Gruppe, die spaeter dazukommt,
     *     faellt in `spuren` und wird geraeumt — sie faellt nicht still
     *     heraus. Ein Raeumer, der einen neuen Schluessel stillschweigend
     *     uebergeht, laesst Daten liegen und meldet "fertig". */
    foreach (cluster_one_kleinrock_schluessel() as $c1_gruppe => $c1_namen) {
        if (in_array($c1_gruppe, $c1_stehen, true)) {
            continue;   // E4, siehe cluster_one_kleinrock_raeumer_laesst_stehen()
        }
        foreach ($c1_namen as $c1_name) {
            $c1_wert = get_option($c1_name, null);
            if ($c1_wert === null) {
                continue;
            }
            if ($c1_name === 'cluster_one_paired_peers') {
                /* Oben schon als `kanten` gezaehlt. Hier nur noch geraeumt —
                   sonst stuende dieselbe Option zweimal in der Rechnung,
                   einmal als Kante und einmal als Spur. */
                $c1_zahl['kanten'] = $c1_zahl['kanten'] ?? 0;
            } elseif ($c1_name === 'cluster_one_known_nodes') {
                $c1_zahl['zeiger'] = is_array($c1_wert) ? count($c1_wert) : 1;
            } elseif ($c1_name === 'cluster_one_pending_pairings') {
                $c1_zahl['handschlaege'] = is_array($c1_wert) ? count($c1_wert) : 1;
            } elseif ($c1_gruppe === 'schalter') {
                $c1_zahl['schalter']++;
            } else {
                // Jede andere Gruppe faellt hier herein, auch eine, die es
                // heute noch nicht gibt. Die Ausnahme steht oben und einmal.
                $c1_zahl['spuren']++;
            }
            if ($c1_tun) {
                delete_option($c1_name);
            }
        }
    }

    /* 18.10.2026 (Neo): DER CRON-EINTRAG GEHT MIT. cluster_one_daily_sync wird
       bei `init` angelegt, wenn er fehlt — und blieb beim Entfernen stehen,
       als Termin ohne Rufer. Kein Deaktivierungshaken (der Lader meldet
       absichtlich keinen, siehe seine Entfernt-Zeile); der Raeumer ist der
       Ort, an dem dieses Organ sagt, was ihm gehoert. */
    if ($c1_tun && function_exists('wp_clear_scheduled_hook')) {
        wp_clear_scheduled_hook('cluster_one_daily_sync');
    }

    return $c1_zahl;
}

/* DIE ANMELDUNG — sie steht in der ORGANDATEI und nicht im Lader: der Lader
   reist nicht, die Organdatei schon, und der Raeumer gehoert dem Organ.
   (VERTRAG 26 kehrt sich hier um: ein Produkt meldet sich im Lader AN, ein
   Organ meldet sich selbst.)

   DIE GESTALT IST FORM B — ANGEHAENGT, mit 'produkt', 'fn' und 'wirt'
   (ENTSCHEIDUNG 20 E1/E2/E3, 17.09.). Sie hat hier einen Tag lang ANDERS
   gestanden, und der Weg dahin gehoert dazu:

       1.0.9 frueh   FORM A   $c1_r['kleinrock'] = 'cluster_one_…_raeumen';
       1.0.9 spaet   FORM B   $c1_r[] = array('produkt' => …, …);

   Ich hatte FORM A gebaut, weil ich sie GEFAHREN hatte: gegen das Register
   aus `kern.php` meines eigenen Pakets (0.2.20, 285717 B, 454be4d498c0)
   kommt FORM B nicht durch, FORM A schon. Das war richtig gemessen und am
   falschen Gegenstand: seit BODEN-ZUG-6 traegt jeder Knoten R-SAMMLER-1,
   und der Boden dreht die Antwort um. Mammuts w96 an allen sieben Boeden,
   die ich habe, sagt siebenmal "alter Boden" — der neue liegt in keinem
   Paket in meinem Fach.

       R-ZUG-3: Ein Haus misst gegen den Boden, DER AUSGELIEFERT IST,
       nicht gegen den, der bei ihm liegt.

       Und das Gegenstueck dazu, das ich teuer gelernt habe:
       Ein Ergebnis, das nur an einem Gegenstand gilt, ist keine
       Auskunft, solange der Gegenstand nicht danebensteht.

   'produkt' ist KLEINGESCHRIEBEN (E2, VERTRAG 21 §2a) — darunter schlaegt
   `cluster_one_raeumen_eines($produkt)` nach. Der WIRT ist ein Feld daneben
   und nicht der Schluessel (E3): im Monolithen teilen sich alle Organdateien
   EINEN Wirt, und zwei Raeumer unter einem Wirt kollidieren still.

   ZWEI LITERALE 'kleinrock' IN DIESER DATEI, und Mammut hat den Posten bei
   Post Dolly geschlossen (ENTSCHEIDUNG 20 §4): `cluster_one_hier('kleinrock')`
   sagt WELCHES ORGAN BIN ICH, `'produkt' => 'kleinrock'` sagt WELCHES PRODUKT
   RAEUME ICH. Zwei Namen, die heute gleich lauten, sind nicht ein Name.
   Bei mir kaeme ein zweiter Grund dazu: :75 steht AUSSERHALB des Riegels,
   diese Zeile INNERHALB, und eine Datei-Konstante ausserhalb waere auf einem
   Knoten mit zwei Ordnern doppelt deklariert. Der Waechter steht in
   `zeuge-raeumer.php` (R7) und misst, dass beide Woerter gleich lauten —
   er verlangt nicht, dass es ein Wort ist. */
/* ==================== DIE VIERTE GESTALT, ANGEGLICHEN ====================
   v1.0.14 (ZETTEL 23.09. §1). Bis 1.0.13 rief DIESE Zeile
   `cluster_one_kasper_wirt()` NACKT, waehrend drei andere Stellen derselben
   Datei ihn riegeln. Ich hatte zwei gezaehlt; Mammut hat vier gemessen:

       geriegelt   :616   der Seitenanspruch
                   :663   die Leiste auf einer faust-Seite
                   :5376  die Abschnitte-Anmeldung  (Form aus VERTRAG 30 §1)
       nackt       hier

       Drei zu eins, nicht eins zu eins. Das aendert nichts am Befund,
       aber es aendert, was „angleichen" heisst.               (Mammut)

   ALLE VIER SIND JETZT GERIEGELT. Der Grund ist nicht die Mehrheit — der
   Grund ist, dass ein Fatal hier die ganze Anfrage reisst, fuer JEDES
   Haus auf dem Knoten, wegen einer Frage, die nur mich angeht.

       Wer bei einer Auskunft ueber sich selbst die Seite mitnimmt,
       hat den Preis nicht bezahlt, sondern weitergegeben.

   UND WAS DIE VIER DANACH MIT DER LEERE TUN, SAGT DIE STELLE — das ist der
   Teil, den ich beim ersten Melden uebersehen hatte:

       :616, :663   `if ($wirt === '') return;`  — kein Anspruch, keine
                    Leiste. Die Leere ist dort eine VOLLSTAENDIGE Antwort:
                    „dann bin ich es nicht".
       :5376, hier  die Leere geht MIT in den Sammler. Sie muss, denn ein
                    fehlender Abschnitt und ein fehlender Raeumer sind
                    schlimmer als ein unbenannter.

   MEIN EINWAND VON GESTERN BLEIBT, und er ist halb eingeloest:

       Ein Riegel, der die Antwort durch eine leere ersetzt, hat den
       Ausfall nicht verhindert, sondern unbenannt gemacht.

   kranz.php 1.12.14 nennt ihn jetzt (`_doing_it_wrong`, :988-1014) — mein
   Einwand ist dort gebaut. IM RAEUMER-REGISTER NICHT: gemessen gegen
   kern 1.12.13, ein Raeumer mit `wirt` = "" kommt durch und wird NICHT
   genannt — Register `{"leerwirt":{"fn":"g_r","wirt":""}}`, null Meldungen.
   Die Pruefung von 1.12.10 gilt dem SCHLUESSEL, nicht dem Wirt.

       Zwei Sammler, die dieselbe Form verlangen, pruefen darum noch
       nicht dasselbe.

   ALSO NENNE ICH IHN SELBST, hier, an meiner eigenen Anmeldung. Das ist
   mein Byte in meiner Datei und keine Bodenarbeit — und es haelt auch
   dann, wenn der Boden es nie nachholt.

   WAS DER LEERE WIRT IM RAEUMER BEDEUTET, gemessen und nicht vermutet:
   `cluster_one_raeumen_mein()` (kern 1.12.13:1511) filtert auf
   Wirt-GLEICHHEIT — ein leerer Wirt hiesse, dass mein Raeumer beim
   Deinstallieren still uebersprungen wird. Er kann es aber nicht, denn
   dieselbe Funktion fragt vier Zeilen vorher DENSELBEN Namen fuer sich
   selbst und gibt bei '' sofort `gefunden => 0` zurueck. Beide Seiten
   fallen zusammen aus.
       Ein Riegel ist nur dann harmlos, wenn der Leser an derselben
       Frage scheitert wie der Melder. Hier tut er es — gemessen,
       nicht gehofft. */
add_filter('cluster_one_raeumer', function ($c1_r) {
    if (is_array($c1_r)) {
        $c1_wirt = function_exists('cluster_one_kasper_wirt')
                   ? cluster_one_kasper_wirt(__FILE__) : '';
        if ($c1_wirt === '' && function_exists('_doing_it_wrong')) {
            _doing_it_wrong(
                'cluster_one_raeumer',
                'Kleinrock hat seinen Raeumer OHNE Wirt angemeldet '
              . '(cluster_one_kasper_wirt() lag beim Anmelden nicht da). Er steht '
              . 'im Register, aber cluster_one_raeumen_mein() filtert auf den Wirt '
              . '— beim Deinstallieren wird er dann nicht gerufen und die Daten '
              . 'dieses Moduls bleiben stehen.',
                '1.0.14'
            );
        }
        $c1_r[] = array(
            'produkt' => 'kleinrock',
            'fn'      => 'cluster_one_kleinrock_raeumen',
            'wirt'    => $c1_wirt,
        );
    }
    return $c1_r;
});


/* ===========================================================================
   v0.9.80 — UMGEZOGEN AUS kern.php (Bodenarbeit, Backlog H51/H52)
   ===========================================================================
   Der Boden liegt in jedem Light. Solange er nach oben griff, haette ein
   Kleinrock-Light bei jedem dieser Aufrufe in ein Organ gerufen, das es gar
   nicht traegt. Gemessen: 13 solche Stellen, davon sieben allein fuer ein
   Kleinrock-Light toedlich.

   Der bessere Griff war nicht naehen, sondern umziehen — das VERKLEINERT den
   Boden, statt ihn zu flicken.

       Ein Organ gehoert dorthin, wo sein Versprechen eingeloest wird,
       nicht dorthin, wo es zufaellig hingeschrieben wurde.

   Regel 2: byte-identisch verschoben, kein Zeichen am Rumpf geaendert.
   Golden und Verhalten bezeugen das.
   =========================================================================== */

/* HIERHER UMGEZOGEN (0.9.80): die Dunbar-Grenze wird nur hier gelesen (3x). */
define('CLUSTER_ONE_DUNBAR', 150);

/* HIERHER UMGEZOGEN (0.9.80): Sie loest ein eingehendes Mesh-Token zur Kante auf — Kleinrocks Karte, Kleinrocks REST-Routen, Kleinrocks Versprechen. Drei Aufrufer, alle hier. */
/** Token -> volle Kante (inkl. getrennter), zeitkonstant. Null, wenn unbekannt. */
function cluster_one_find_edge_by_token(string $token): ?array {
    if ($token === '') {
        return null;
    }
    foreach (cluster_one_get_paired_map() as $c1_id => $e) {
        $t = (string) ($e['token'] ?? '');
        if ($t !== '' && hash_equals($t, $token)) {
            /* v0.9.40: DER SCHLÜSSEL DER KARTE IST DIE KANTEN-ID. Bisher gab
               diese Funktion die Kante ohne ihn zurück, und die Aufrufer
               behalfen sich mit `$e['edge'] ?? cluster_one_edge_id($token)`.
               Diese Krücke ist seit 0.0.62 falsch: Kanten-IDs werden seither
               zufällig vergeben und NICHT mehr aus dem Token abgeleitet
               (s. cluster_one_new_edge_id). Bei einer Kante ohne Feld
               'edge' hätte die Krücke also einen Schlüssel erfunden, unter
               dem nichts liegt — und jeder Schreibzugriff darauf wäre still
               ins Leere gegangen. Hier gibt es den echten Schlüssel. */
            $e['edge'] = (string) $c1_id;
            return $e;
        }
    }
    return null;
}

/* HIERHER UMGEZOGEN (0.9.80): Zaehlt Freunde gegen die Dunbar-Grenze. Drei Aufrufer, alle hier. */
function cluster_one_friend_count(): int {
    $n = 0;
    foreach (cluster_one_get_paired_map() as $e) {
        if (!empty($e['friend'])) {
            $n++;
        }
    }
    return $n;
}


/* ===========================================================================
   v0.9.80 — UMGEZOGEN AUS kern.php (Bodenarbeit)
   =========================================================================== */

/* HIERHER UMGEZOGEN (0.9.80): Die Zahl in der schwarzen WordPress-Leiste zaehlt
   cluster_one_unread_total() und verlinkt auf Kleinrocks Hauptseite. Sie ist
   Kleinrocks Zahl, nicht die des Hauses — sie stand nur im Boden.

   BENANNTE FOLGE: Ein Bismarck-Light ohne Kleinrock hat oben keinen Eintrag
   mehr, obwohl es auch Ungelesenes gibt. Das ist heute richtig (die Zahl KANN
   dort nichts zaehlen) und spaeter eine Produktfrage — eine Leiste, die jedes
   Organ nach seiner Zahl fragt, waere ein Umbau und gehoert nicht in Mammut. */

// Anzeiger in der WP-Admin-Leiste: „dass irgendwo etwas Neues ist".
// Klick führt zur Netz-Liste, wo die Zeilen-Badges sagen, WELCHER Chat.
// Nur sichtbar, wenn es Ungelesenes gibt; nur für Verwalter.
add_action('admin_bar_menu', function ($wp_admin_bar) {
    if (!cluster_one_can_access()) {
        return;
    }
    $n = cluster_one_unread_total();
    if ($n < 1) {
        return;
    }
    $wp_admin_bar->add_node([
        'id'    => 'cluster_one_unread',
        'title' => '<span class="ab-icon dashicons dashicons-format-chat" aria-hidden="true" style="font-family:dashicons;top:2px;"></span>'
                 /* translators: %d: Anzahl neuer Nachrichten. */
                 . '<span class="ab-label">' . sprintf(_n('%d new', '%d new', $n, 'cluster-one-kleinrock'), $n) . '</span>',
        'href'  => admin_url('admin.php?page=cluster_one_mesh'),
        'meta'  => ['title' => __('New messages in Cluster One', 'cluster-one-kleinrock')],
    ]);
}, 100);

/* HIERHER UMGEZOGEN (0.9.80): Paaren ist Kleinrocks Sache. */
/* --- Backend-Aktion: „Test" = verbinden, aber in der Liste bleiben ---
   Paart (Domain-Beweis) und kehrt zur Liste zurück, statt in den Chat zu springen.
   Erfolg -> approved (Button wird „Hallo sagen"). Misserfolg -> Notiz oben, der
   Test-Button bleibt, später erneut versuchbar. --- */
add_action('admin_post_cluster_one_connect_and_stay', function () {
    /* 18.10.2026 (Neo), B3: Paaren ist SysOp-Sache — admin_post_cluster_one_pair
       sagt es seit 0.9.34 ausdruecklich (Kostentraeger-Regel). Dieser zweite
       Weg auf dieselbe Handlung prueft bis heute nur die Weissliste; ein
       Beschirmter konnte ueber den Test-Knopf Kanten anlegen. Dieselbe
       Sperre wie dort, im Handler und nicht nur in der Oberflaeche. */
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_connect_and_stay');

    $rurl = isset($_POST['peer_url']) ? wp_unslash($_POST['peer_url']) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- cluster_one_pair_with() reinigt als Erstes: esc_url_raw und https-Pflicht
    $r    = cluster_one_pair_with((string) $rurl);

    wp_safe_redirect(admin_url('admin.php?page=cluster_one_mesh&test=' . ($r['ok'] ? 'ok' : 'fail')));
    exit;
});




/* ---------------------------------------------------------------------------
 * DER VERTRAG: WAS WEISS DAS NETZ?
 *
 * v1.3.3 — Bismarck-Light hatte zuletzt sechs offene Naehte, und ALLE sechs
 * zeigten hierher. Nach dem Umzug von label_mark_seen() und einem Fehlalarm im
 * Zaehler bleiben drei Stellen, an denen ein Haus das Netz fragt:
 *
 *     kranz.php:1574            gibt es ueberhaupt eine Kante?   (der Bauplan)
 *     bismarck-seiten.php:1029  der Anstups-Knopf neben einem Menschen
 *     bismarck-seiten.php:1042  derselbe, in der zweiten Liste
 *
 * Keine davon laesst sich umziehen: der Bauplan prueft den ganzen Knoten, und
 * der Knopf steht in einer Teilnehmerliste, die Bismarck gehoert.
 *
 * Also dieselbe Form wie bei Feynman und der Umgebung — die dritte, und damit
 * ist es ein Muster und keine Einzelloesung:
 *
 *     apply_filters('cluster_one_netz', $vorgabe, $was)     lesen
 *     do_action('cluster_one_netz_ping_knopf', $raum, $uid) zeichnen
 *
 * Warum der Knopf eine AKTION ist und kein Filter: er gibt nichts zurueck, er
 * schreibt in die Seite. Ein Filter, der druckt, luegt ueber seine Bauform —
 * derselbe Satz wie beim Schreibweg der Umgebung.
 *
 * Ohne Kleinrock ist die Karte leer und der Knopf erscheint nicht. Beides sind
 * Zustaende, die der Code ohnehin kennt: ein Knoten ohne Kante ist der
 * Normalfall am ersten Tag.
 * ------------------------------------------------------------------------- */
add_filter('cluster_one_netz', function ($c1_wert, $c1_was = '') {
    switch ((string) $c1_was) {
        case 'karte':   return cluster_one_get_paired_map();
        case 'treffen': return function_exists('cluster_one_fetch_meetings') ? true : false;
    }
    return $c1_wert;
}, 10, 2);

add_action('cluster_one_netz_karte_speichern', function ($c1_karte) {
    cluster_one_save_paired_map((array) $c1_karte);
});

add_action('cluster_one_netz_ping_knopf', function ($c1_raum, $c1_uid) {
    cluster_one_render_ping_button((string) $c1_raum, (int) $c1_uid);
}, 10, 2);

}
