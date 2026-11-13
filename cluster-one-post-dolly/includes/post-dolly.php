<?php
/**
 * Cluster One — Modul post-dolly. Das Schaufenster.
 *
 * ---------------------------------------------------------------------------
 * WOZU
 *
 * Der SysOp schreibt Zitate, und sie erscheinen dort, wo er sie hinstellt:
 * im Frontend ueber einen Shortcode, im Backend auf seiner Uebersicht, oder
 * an beiden Orten. Nichts davon geschieht ungefragt.
 *
 *     Post Dolly erscheint dort, wo jemand es hingestellt hat. Nirgendwo sonst.
 *
 * Das Vorbild heisst Hello Dolly (angesehen in Fassung 1.7.2). Genommen ist
 * davon die Haltung — klein, wenige Funktionen, es ist einfach da. NICHT
 * genommen ist sein Haken: hello.php haengt an 'admin_notices' und steht damit
 * auf JEDER Admin-Seite. Genau das hat es zum meistgeloeschten Plugin der Welt
 * gemacht. Diese Datei benutzt 'admin_notices' nicht.
 *
 * ---------------------------------------------------------------------------
 * WAS DAS EINZIGE LIGHT IM FRONTEND BEDEUTET
 *
 * Gemessen am 27.08.2026: Cluster One hatte null Frontend-Haken. Diese Datei
 * ist der erste, und das ist gewollt (Christian, 27.08.2026). Deshalb steht
 * die Ausgabe hinter einem Shortcode und hinter KEINEM automatischen Haken —
 * kein 'wp_footer', auch keiner, der ausgeschaltet ausgeliefert wird
 * (Christian, 28.08.2026). Ein Feld, das in Fassung 1 nichts tut, ist ein
 * Versprechen und keine Einstellung.
 *
 * DER EIGENE SELEKTOR IST KEINE KOSMETIK. Hello Dolly benutzt '#dolly' und
 * liegt auf Millionen Installationen. Ein zweites '#dolly' auf derselben Seite
 * waere ein doppeltes id-Attribut, und die Regeln des einen faerben das andere.
 * Hier heisst alles 'cluster-one-dolly'.
 *
 * ---------------------------------------------------------------------------
 * DER BERG ZAEHLT WIRTE, NICHT MELDUNGEN
 *
 * Kasper gibt ORGANE zurueck, nicht Plugins. Auf einem Knoten mit Mammut melden
 * sich dessen Organe alle mit demselben Wirt 'cluster-one'. Wer Meldungen
 * zaehlt, zaehlt dort fuenf Etappen, ohne dass ein einziges Light daliegt.
 *
 *     Zaehle die Wirte, nicht die Meldungen.
 *
 * Gezaehlt werden also die verschiedenen 'wirt'-Werte aus cluster_one_kasper(),
 * ohne 'cluster-one'. Mammut ist definitionsgemaess kein Light — es ist der
 * Monolith, aus dem geschnitten wird. Das ist der einzige Name, den diese Datei
 * kennt, und er ist eine Hausdefinition, kein Listeneintrag.
 *
 * H174 (02.09.2026) — WAS DIESE ZAHL HEUTE IST UND WAS SIE NICHT IST.
 * Auf Christians Knoten Andreas stand das Bild auf dem Gipfel, auf einem Knoten
 * ohne eine einzige aktive Kante: sechs Wirte, gedeckelt auf fuenf. Zwei davon
 * — `turing` und `connector-vault` — haben mit der Route nichts zu tun.
 *
 * Es ist derselbe Befund wie H119, zum ersten Mal an der laufenden Software
 * gesehen. `zeuge-h119.php` sagt ihn seit dem 31.08. voraus (W5, W7).
 *
 * DIE ZAHL BLEIBT UNVERAENDERT, und das ist eine Entscheidung: Sie enger zu
 * fassen hiesse heute, eine Namensliste einzubauen — H93 in einer
 * Zaehlfunktion, und der naechste fremde Helfer heisst anders. Die Eigenschaft,
 * nach der zu fragen waere („gehoert dieses Plugin zu diesem Haus?"), gibt es
 * nicht; das ist H112 und liegt bei der Werkbank.
 *
 * GEAENDERT HAT SICH DIE BESCHRIFTUNG. Sie behauptet keine Reichweite mehr,
 * sondern nennt, was gezaehlt wurde. Ein Bild, das zaehlt, darf zaehlen; es
 * darf nur nicht so tun, als wuesste es, wie weit jemand gekommen ist.
 *
 * Fuenf Lights, fuenf Etappen (Christian, 28.08.2026):
 * 1 = Tal -> BL1 · 2 = BL1 -> BL2 · 3 = BL2 -> BL3 · 4 = BL3 -> BL4 ·
 * 5 = BL4 -> Spitze. Post Dolly zaehlt sich mit; es ist Light Nr. 1 der Reihe.
 *
 * EINE GRENZE, DIE HIER STAND UND WIEDER WEG IST: Ich hatte notiert, der Berg
 * baue sich selbst ab, sobald Mammut von den Knoten verschwindet — der Zaehler
 * falle von fuenf auf vier. Das galt unter der Annahme, Mammut zaehle mit. Es
 * zaehlt nicht mit, also kann sein Verschwinden den Zaehler nicht senken.
 * Der Satz steht hier, weil eine Grenze, die sich aufloest, eine Entscheidung
 * ist — und eine, die kommentarlos verschwindet, wie ein Versehen aussieht.
 *
 * ---------------------------------------------------------------------------
 * DAS FOEDERATIONS-HAEKCHEN, UND WARUM ES NICHT NACH KLEINROCK RUFT
 *
 * Allianz ist die Menge der verbundenen Knoten im diskreten Mesh; sie nutzt das
 * Las-Vegas-Prinzip. Foederation ist die Teilmenge derer, die sich oeffentlich
 * bekennen. Wer das Haekchen setzt, verlaesst Vegas absichtlich.
 *
 * Kleinrock braucht diese Auskunft (Christian, 28.08.2026) — „natuerlich nur,
 * sofern Kleinrock vorhanden ist". Diese Bedingung braucht KEINE Zeile Code:
 * Nach Vertrag 12 ruft Kleinrock und diese Datei haengt sich ein. Fragt
 * niemand, gibt es kein Kleinrock, und es gibt nichts zu pruefen.
 *
 *     Ein cluster_one_modul_da('kleinrock') hier waere ein Riegel, dessen
 *     Gegenstueck die Frage selbst schon ist.
 *
 * ---------------------------------------------------------------------------
 * BENANNTE GRENZEN
 *
 * 1. CACHE. Ein Zitat, das bei jedem Aufruf wechselt, friert hinter einem
 *    Seiten-Cache ein. Das ist kein Defekt dieser Datei und kein Grund, es zu
 *    lassen — aber es steht hier, damit sich spaeter niemand wundert.
 *        Ein Cache liefert nicht den Zustand, sondern die Erinnerung an einen.
 *
 * 2. DAS HAEKCHEN KANN NICHT PRUEFEN, OB JEMAND ZUSIEHT. Der SysOp kann
 *    Foederation setzen und den Shortcode nirgends platzieren. Dann meldet
 *    diese Datei ein Bekenntnis, das draussen niemand sieht. Eine Suche nach
 *    dem Shortcode in Inhalten waere unzuverlaessig (Widgets, Vorlagen,
 *    Bricks-JSON) und teuer, und ein unzuverlaessiger Waechter ist schlimmer
 *    als ein benannter Mangel.
 *        Das Haekchen sagt, was der SysOp will. Es hat nie behauptet zu sagen,
 *        was ein Besucher sieht.
 *    Der Erklaertext am Haekchen ist die Stelle, an der das steht.
 *
 * 3. DER BERG MISST ANMELDUNGEN, NICHT INSTALLATIONEN. Ein Light, das sich
 *    nicht mit cluster_one_hier() anmeldet, ist fuer ihn unsichtbar — auch
 *    wenn es installiert und aktiv ist.
 *
 * 4. DIE SPRACHE KOMMT AUS WORDPRESS, NICHT AUS DEM HAUS. Es gibt
 *    cluster_one_user_lang() in anti-babel.php. Diese Datei ruft sie NICHT:
 *    ein Light, das allein liegt, haette sie nicht, und ein Haus nennt kein
 *    anderes beim Namen. determine_locale() steht in jedem WordPress.
 *
 * ---------------------------------------------------------------------------
 * DER KUERZESTE WEG ZUM ERSTEN NUTZEN (Stufe 3, DAS-HAUS §7)
 *
 * Aktivieren -> Cluster One -> Kranz -> ein Zitat eintragen -> Shortcode
 * [cluster_one_dolly] auf eine Seite setzen. Vier Schritte, kein zweites
 * Plugin, und ein Zitat steht ab Werk drin, damit der erste Schritt schon
 * etwas zeigt.
 * ---------------------------------------------------------------------------
 */

defined('ABSPATH') || exit;

/* Der Riegel um die ganze Datei (H67): Beim Aktivieren laedt WordPress erst
   alle aktiven Plugins und dann den Kandidaten — in diesem Moment liegt jede
   Light-Datei in ZWEI Plugins. Ohne Riegel: Cannot redeclare.
   Die Anmeldung steht INNEN (H70): eine Meldung, die im Lader wohnt, wird beim
   Riegeln uebersehen. */
if (!function_exists('cluster_one_post_dolly_zitate')) {

cluster_one_hier('post-dolly');

/** Die gespeicherten Einstellungen dieses Organs.
 *
 * EIGENE OPTION, NICHT cluster_one_settings. Der Kranz sanitisiert seine
 * eigene Option mit einer Weissliste von Schluesseln; ein fremdes Feld darin
 * faellt beim Speichern lautlos heraus. Und ein Organ, das in die Option eines
 * anderen schreibt, hat einen zweiten Schreibweg auf dieselbe Wahrheit (P1-12).
 * Die Option wird in DERSELBEN Settings-Gruppe angemeldet, damit das eine
 * Kranz-Formular beide speichert — ein Formular, zwei Optionen, ein Schreibweg
 * je Option. */
function cluster_one_post_dolly_opts(): array {
    $c1_o = get_option('cluster_one_post_dolly', array());
    return is_array($c1_o) ? $c1_o : array();
}

/** Der Zitat-Vorrat: Liste von array('de' => string, 'en' => string).
 *
 * DAS PAAR IST DIE EINHEIT, nicht die einzelne Sprache. Zwei getrennte Listen
 * waeren ueber die Zeilennummer gekoppelt, und eine geloeschte Zeile
 * verschoebe alle folgenden gegeneinander — ein Fehler, der nichts anzeigt,
 * sondern die falsche Uebersetzung liefert.
 *
 * HIER STAND EIN ERWEITERUNGSPUNKT, UND ER IST WEG (Entscheidung 31.08.2026).
 * `apply_filters('cluster_one_post_dolly_vorrat', $c1_v)` lud fremden Text in
 * den Vorrat. Er stand in keinem Vertrag, und niemand hat je zugehoert.
 *
 * Drei Gruende, in dieser Reihenfolge:
 *
 * 1. Ein Vertrag hat zwei Seiten. Es gab keine zweite und keinen Anwaerter —
 *    ihn ins Brett zu tragen haette dort einen Haken erzeugt, den niemand
 *    hoert. Genau der ist im Haus schon zweimal lautlos ausgefallen.
 * 2. Das Haus verbietet die Bauform ausdruecklich: kein Feld in Fassung 1, das
 *    erst in Fassung 10 eine Funktion bekommt.
 * 3. Er vergroesserte die Flaeche, fuer die P0 geradestehen muss, ohne
 *    Gegenwert. Der Text kam escaped heraus — aber eine Tuer, durch die nie
 *    jemand geht, ist trotzdem eine Tuer.
 *
 *     Ein Vorrat ohne Bedarf sieht aus wie Weitsicht und ist eine Zusage, die
 *     niemand eingefordert hat.
 *
 * WAS DAMIT VERLORENGEHT, damit die Entscheidung umkehrbar bleibt: Zitate aus
 * Code statt aus dem Formular — etwa aus einer Datei oder einem anderen
 * Plugin. Kommt der Bedarf, kommt der Haken zurueck, dann aber als Vertrag mit
 * zwei Seiten und einem Zeugen.
 *
 * BENANNTE GRENZE: Der Haken war in 0.2.0 ausgeliefert. Oeffentlich angesagt
 * war er nie — nicht im Brett, nicht in einem Vertrag, nur in diesem Kommentar.
 * Wer ihn trotzdem benutzt hat, verliert ihn ohne Vorwarnung, und das steht
 * hier, statt verschwiegen zu werden. */
function cluster_one_post_dolly_zitate(): array {
    $c1_opts = cluster_one_post_dolly_opts();
    $c1_v    = isset($c1_opts['zitate']) && is_array($c1_opts['zitate']) ? $c1_opts['zitate'] : array();

    if (!$c1_v) {
        /* Ein Zitat ab Werk, damit die frische Installation nicht leer
           aussieht. Es ist ersetzbar und soll ersetzt werden.

           ES BLEIBT, UND DER EINWAND DAGEGEN IST GUT (Werkbank, 29.08.): Es
           spricht von Knoten zu jemandem, der vielleicht nie von einem Mesh
           gehoert hat. Der Grund, warum es trotzdem bleibt, ist gemessen und
           nicht Geschmack: Dieser Satz steht auf der Uebersicht des SysOps.
           Nach draussen kommt er nur, wenn jemand den Shortcode setzt — und
           wer ihn setzt, hat die Zeile gesehen und die Wahl gehabt.

           Bleibt ein Fenster, und es ist genau eines: Shortcode gesetzt,
           bevor das erste eigene Zitat geschrieben ist. Dagegen hilft kein
           Code, sondern ein Satz an der richtigen Stelle — er steht im
           Kranz-Abschnitt ueber der Tabelle. */
        $c1_v = array(array(
            'key' => 'A1',
            'de' => 'Fremde Knoten haben fremde Kleider.',
            'en' => 'Foreign nodes wear foreign clothes.',
        ));
    }

    return cluster_one_post_dolly_liste($c1_v, array()); // 20.10.2026: eine Reinigung fuer beide Tabellen
}

/** 20.10.2026 (Neo) — DIE ZWEITE TABELLE: DIE ANSAGEN DER FOEDERATION.
 *
 * Nemesus' Entscheidung: dieselbe Form wie die Zitate (Paar de/en, eine
 * Zeile je Satz), und am Anfang genau eine Zeile. Bis heute stand der Satz
 * fest im Code („This page forms a cluster node."); jetzt schreibt ihn der
 * SysOp. Seit 27.10.2026 liegen die Ansagen mit den Zitaten in EINEM Topf
 * (cluster_one_post_dolly_topf): je Seitenaufruf erscheint eine Zeile aus
 * allem zusammen, nicht ein Zitat plus eine Ansage. In den Topf kommen die
 * Ansagen nur, wenn der Knoten sich bekennt (cluster_one_post_dolly_foederation),
 * daran aendert die Tabelle nichts.
 *
 * UND JEDE ZEILE BEIDER TABELLEN TRAEGT EINEN SCHLUESSEL ('key'), damit der
 * Shortcode sie einzeln rufen kann: [cluster_one_dolly schluessel="A3"].
 * Der Schluessel ist KEINE Zeilennummer — eine geloeschte Zeile darueber
 * verschoebe sonst jeden Shortcode auf der Website, ohne dass es jemand
 * sieht (dieselbe Kopplung, vor der der Kopf von _zitate() warnt). Er wird
 * beim Speichern gesetzt, wenn er leer ist (A1, A2 … fuer Zitate, B1, B2 …
 * fuer Ansagen), und danach nicht mehr angefasst. */
/** Ist dieser Array-Schluessel eine Sprachspalte? Zwei bis drei Kleinbuchstaben
 * ('de', 'en', 'ru', 'ast'), dieselbe Form wie Polylang-Slugs. 'key' ist keine.
 * 27.10.2026 (Neo): bis dahin kannte die Zeile genau 'de' und 'en'. */
function cluster_one_post_dolly_ist_code($c1_k): bool {
    /* 27.10.2026 abends (Neo): 'key' hat drei Kleinbuchstaben und stand auf
       Nemesus' Main als vierte Spalte „KEY key" in der Tabelle — der Satz
       darueber („'key' ist keine") war wahr und der Code hat ihn nicht
       getragen. Gespeichert wurde nichts Falsches: die doppelte Eingabe trug
       denselben Wert wie die erste. */
    return is_string($c1_k) && $c1_k !== 'key' && preg_match('~^[a-z]{2,3}$~', $c1_k) === 1;
}

function cluster_one_post_dolly_liste(array $c1_v, array $c1_vorgabe): array {
    if (!$c1_v) { $c1_v = $c1_vorgabe; }
    $c1_rein = array();
    foreach ($c1_v as $c1_z) {
        if (!is_array($c1_z)) { continue; }
        /* 27.10.2026 (Neo): jede Sprachspalte, nicht nur de/en. Hier stand
           „$c1_de = … $c1_en = …; if ($c1_de === '' && $c1_en === '') continue". */
        $c1_r = array('key' => isset($c1_z['key']) ? (string) $c1_z['key'] : '');
        $c1_voll = false;
        foreach ($c1_z as $c1_k => $c1_t) {
            if (!cluster_one_post_dolly_ist_code($c1_k) || !is_string($c1_t)) { continue; }
            $c1_r[$c1_k] = trim($c1_t);
            if ($c1_r[$c1_k] !== '') { $c1_voll = true; }
        }
        if (!$c1_voll) { continue; }
        $c1_rein[] = $c1_r;
    }
    return $c1_rein;
}

function cluster_one_post_dolly_ansagen(): array {
    $c1_opts = cluster_one_post_dolly_opts();
    return cluster_one_post_dolly_liste(
        isset($c1_opts['ansagen']) && is_array($c1_opts['ansagen']) ? $c1_opts['ansagen'] : array(),
        /* 27.10.2026 (Neo): kuerzer, Nemesus' Wunsch — der lange Satz brach
           in der Fusszeile neben dem Menue in zwei Zeilen. Bis dahin:
           „Diese Seite ist Teil der Cluster-One-Föderation." /
           „This page is part of the Cluster One federation." Gilt nur fuer
           Knoten, die noch nichts gespeichert haben; eine gespeicherte Zeile
           aendert der SysOp selbst. */
        array(array('key' => 'B1',
            'de' => 'Teil der Cluster-One-Föderation.',
            'en' => 'Part of the Cluster One Federation.'))
    );
}

/** Ein Satz aus einer Zeile, in der Sprache des Lesers oder der erzwungenen.
 *
 * 27.10.2026 (Neo): Hier stand „in_array($c1_sprache, array('de', 'en'))" und
 * als Rueckfall die jeweils andere der zwei Spalten. Nemesus' Frage: „Was ist,
 * wenn ein User auf WP die Sprachvarianten Russisch und Spanisch hat?" —
 * Antwort damals: beide bekamen Englisch. Jetzt gilt jeder Sprachcode, der
 * Rueckfall steht in cluster_one_post_dolly_text(). */
function cluster_one_post_dolly_satz(array $c1_z, string $c1_sprache = ''): string {
    $c1_sprache = strtolower(trim($c1_sprache));
    $c1_s = cluster_one_post_dolly_ist_code($c1_sprache) ? $c1_sprache : cluster_one_post_dolly_sprache();
    return wptexturize(cluster_one_post_dolly_text($c1_z, $c1_s));
}

/** Der rohe Text einer Zeile in einer Sprache, mit Rueckfall-Kette:
 * gewuenschte Sprache -> Site-Sprache -> Englisch -> erste gefuellte Spalte.
 * Nie leer, solange die Zeile irgendwo Text hat: eine leere Zeile saehe aus
 * wie ein kaputtes Plugin. */
function cluster_one_post_dolly_text(array $c1_z, string $c1_s): string {
    foreach (array($c1_s, cluster_one_post_dolly_code((string) get_locale()), 'en') as $c1_k) {
        if (isset($c1_z[$c1_k]) && is_string($c1_z[$c1_k]) && trim($c1_z[$c1_k]) !== '') { return $c1_z[$c1_k]; }
    }
    foreach ($c1_z as $c1_k => $c1_t) {
        if (cluster_one_post_dolly_ist_code($c1_k) && is_string($c1_t) && trim($c1_t) !== '') { return $c1_t; }
    }
    return '';
}

/** Aus einer Locale der kurze Sprachcode: 'de_DE' -> 'de', 'pt_BR' -> 'pt',
 * 'en_US' -> 'en'. Leer oder unbrauchbar -> 'en'.
 *
 * BENANNTE GRENZE: zwei Varianten einer Sprache (pt_BR, pt_PT) fallen in eine
 * Spalte. Das ist der Preis fuer Codes, die zu Polylang-Slugs passen. */
function cluster_one_post_dolly_code(string $c1_locale): string {
    $c1_code = preg_replace('~[^a-z].*$~', '', strtolower(trim($c1_locale)));
    return cluster_one_post_dolly_ist_code($c1_code) ? $c1_code : 'en';
}

/** Die Sprachspalten dieses Knotens, in fester Reihenfolge: Site-Sprache,
 * dann jedes installierte Sprachpaket, Englisch immer, dazu jede Spalte, in
 * der schon Text steht. Aus WordPress, nicht aus dem Haus und nicht aus
 * Polylang: wer Polylang hat, hat dieselben Pakete installiert, und wenn der
 * Kern die Mehrsprachigkeit uebernimmt, auch. Eine Spalte verschwindet nie
 * still: ist ihr Paket fort, aber Text da, bleibt sie. */
function cluster_one_post_dolly_sprachen(): array {
    $c1_s = array(cluster_one_post_dolly_code((string) get_locale()));
    foreach (get_available_languages() as $c1_l) { $c1_s[] = cluster_one_post_dolly_code((string) $c1_l); }
    $c1_s[] = 'en';
    $c1_opts = cluster_one_post_dolly_opts();
    foreach (array('zitate', 'ansagen') as $c1_liste) {
        if (!isset($c1_opts[$c1_liste]) || !is_array($c1_opts[$c1_liste])) { continue; }
        foreach ($c1_opts[$c1_liste] as $c1_z) {
            if (!is_array($c1_z)) { continue; }
            foreach ($c1_z as $c1_k => $c1_t) {
                if (cluster_one_post_dolly_ist_code($c1_k) && is_string($c1_t) && trim($c1_t) !== '') { $c1_s[] = $c1_k; }
            }
        }
    }
    return array_values(array_unique($c1_s));
}

/** Der Name einer Sprache fuer die Spaltenueberschrift, in der Sprache des
 * Lesers — ueber die intl-Erweiterung, wenn PHP sie hat; sonst der Code gross.
 * Kein Netz, keine Liste im Haus: eine Namensliste hiesse H93 in einer
 * Tabellenueberschrift, und der naechste Knoten spricht eine Sprache, die
 * nicht drinsteht. */
function cluster_one_post_dolly_sprachname(string $c1_s): string {
    if (class_exists('Locale')) {
        $c1_n = \Locale::getDisplayLanguage($c1_s, (string) get_user_locale());
        if (is_string($c1_n) && $c1_n !== '' && strtolower($c1_n) !== $c1_s) { return function_exists('mb_convert_case') ? mb_convert_case($c1_n, MB_CASE_TITLE, 'UTF-8') : ucfirst($c1_n); }
    }
    return strtoupper($c1_s);
}

/* 27.10.2026 (Neo): Hier stand cluster_one_post_dolly_ansage() — „Eine Ansage,
   fertig zum Ausgeben — wie cluster_one_post_dolly_zitat()", ein zweiter Zufall
   nur ueber die untere Tabelle. Der Block zog damit ZWEI Zeilen je Aufruf, ein
   Zitat UND eine Ansage. Nemesus auf der DEV: „Es stehen zwei Zeilen in der
   Fusszeile … ich wollte es so haben, dass das System nicht die Tabellen
   unterscheidet." Seitdem gibt es einen Topf (cluster_one_post_dolly_topf) und
   einen Zug (cluster_one_post_dolly_zug); die Funktion ist fort. */

/** Die Zeile zu einem Schluessel, aus beiden Tabellen; leer, wenn es sie nicht gibt. */
function cluster_one_post_dolly_zeile(string $c1_key): array {
    $c1_key = strtoupper(trim($c1_key));
    if ($c1_key === '') { return array(); }
    foreach (array_merge(cluster_one_post_dolly_zitate(), cluster_one_post_dolly_ansagen()) as $c1_z) {
        if (strtoupper((string) $c1_z['key']) === $c1_key) { return $c1_z; }
    }
    return array();
}

/** Der Sprachcode des Lesers ('de', 'en', 'ru' …), aus der Locale.
 *
 * 27.10.2026 (Neo): Hier stand „'de' oder 'en'. Alles, was nicht Deutsch ist,
 * bekommt Englisch." und darunter „strpos(determine_locale(), 'de') === 0".
 * Jetzt ist es der Code jeder Locale; welche Spalten es gibt, sagt
 * cluster_one_post_dolly_sprachen(), den Rueckfall macht _text().
 *
 * Zwei Sprachen, zwei Orte: im Backend zaehlt die Sprache des angemeldeten
 * Menschen (get_user_locale, ueber determine_locale mit abgedeckt), im
 * Frontend die der Seite. determine_locale() beantwortet beides und liegt im
 * WordPress-Kern — kein Haus wird dafuer gebraucht.
 *
 * OHNE function_exists-RIEGEL, und das ist eine Entscheidung vom 28.08. Hier
 * stand ein Rueckfall auf get_locale() fuer WordPress vor 5.0. Der Zweig war
 * von keinem Zeugen erreicht — und er haette die Zusage im Dateikopf leise
 * unterlaufen: Diese Datei setzt WordPress 5.0 voraus, weil ihr Versprechen
 * („zweisprachig, nach der Sprache des Lesers") ohne determine_locale() im
 * Backend die falsche Sprache liefert. Ein Rueckfall haette daraus einen
 * stillen Sonderfall gemacht statt einer Bedingung.
 *     Ein Zweig, den kein Zeuge erreicht, ist keine Absicherung. Er ist eine
 *     zweite Fassung des Verhaltens, die niemand gemessen hat. */
function cluster_one_post_dolly_sprache(): string {
    return cluster_one_post_dolly_code((string) determine_locale());
}

/** Der Topf, aus dem der nackte Shortcode zieht: alle Zitate (A…), und die
 * Ansagen (B…) dazu, wenn der Knoten sich bekennt. EIN Topf, nicht zwei
 * Tabellen — 27.10.2026, Nemesus' Entscheidung auf der DEV: „Wenn also die
 * Anzeige mit der Foederations-Anzeige kommt, soll keine andere Zeile aus der
 * ersten Zitaten-Tabelle kommen." Je Aufruf erscheint genau eine Zeile; ob es
 * eine Ansage ist, entscheidet der Zufall ueber den ganzen Topf, nicht eine
 * zweite Ziehung. Bekennt der Knoten sich nicht, ist der Topf nur die obere
 * Tabelle, und nichts Foederales kommt heraus. */
function cluster_one_post_dolly_topf(): array {
    $c1_topf = cluster_one_post_dolly_zitate();
    if (cluster_one_post_dolly_foederation()) {
        $c1_topf = array_merge($c1_topf, cluster_one_post_dolly_ansagen());
    }
    return $c1_topf;
}

/** Ein Zug aus dem Topf: eine Zeile (key/de/en), oder leer, wenn der Topf leer ist.
 * Bis 27.10.2026 hiess das hier cluster_one_post_dolly_zitat() und gab den
 * fertigen Satz nur aus der oberen Tabelle; der Satz entsteht jetzt beim
 * Ausgeben, weil der Block die Klasse der Zeile am Schluessel erkennen muss.
 *
 * SERVERSEITIG GEWAEHLT, EINMAL JE SEITENAUFRUF. Kein JavaScript, also kein
 * Layoutsprung nach dem Laden und keine Arbeit im Browser. Genau das tut Hello
 * Dolly auch, und es ist der einzige Teil davon, der hier Vorbild ist.
 *
 * FAELLT DIE EIGENE SPRACHE AUS, GILT DIE ANDERE. Ein Paar, bei dem nur eine
 * Haelfte gefuellt ist, soll sichtbar sein statt still zu fehlen — eine leere
 * Zeile saehe aus wie ein kaputtes Plugin.
 *
 * wptexturize() — OHNE Unterstrich. wp_texturize() gibt es nicht und waere ein
 * Fatal bei jedem Zitat.
 *
 * wp_rand() STATT mt_rand() (H-Befund aus dem Plugin-Check-Lauf vom 29.08.):
 * Das ist hier keine Sicherheitsfrage — welches Zitat erscheint, darf jeder
 * raten. Es ist die Hausregel von WordPress: wp_rand() geht ueber denselben
 * Zufall wie der Rest des Kerns und laesst sich in einem Testlauf festnageln,
 * mt_rand() nicht.
 *     Ein zweiter Zufall im Haus ist ein zweiter Zustand, den kein Zeuge
 *     festhalten kann. */
function cluster_one_post_dolly_zug(): array {
    $c1_v = cluster_one_post_dolly_topf();
    if (!$c1_v) { return array(); }

    return $c1_v[wp_rand(0, count($c1_v) - 1)];
}

/** Die CSS-Klasse einer Zeile: Ansage (B…) oder Zitat (alles andere, auch ein
 * alter Eintrag ohne Schluessel). Shortcode-mit-Schluessel und Block teilen sie. */
function cluster_one_post_dolly_klasse(array $c1_z): string {
    return (strtoupper(substr((string) ($c1_z['key'] ?? ''), 0, 1)) === 'B') ? 'cluster-one-dolly-ansage' : 'cluster-one-dolly-zitat';
}

/** Bekennt sich dieser Knoten oeffentlich zur Foederation?
 *
 * ZWEI BEDINGUNGEN, UND DIE ZWEITE IST NEU (H144, Christian 01.09.2026):
 * das gespeicherte Haekchen UND `cluster_one_auskunft('gepaart') === 'ja'`.
 *
 *     Das Haekchen greift NUR bei 'ja'.
 *
 * WARUM: Bis hierher hat ein Knoten ohne jedes Mesh im Frontend behaupten
 * koennen, er sei ein Cluster-Knoten. Das war mein Befund F1 aus Bericht 05,
 * und dies ist seine Behebung — moeglich geworden, weil der Boden seit 1.7.0
 * die Frage kennt. Vorher gab es sie nicht, und deshalb stand der Fund als
 * benannte Grenze da statt als Reparatur.
 *
 * DREI WERTE, NICHT ZWEI: 'ja' | 'nein' | 'fehlt'. Hier zaehlt nur 'ja', und
 * der Unterschied zwischen den beiden anderen wird an der Oberflaeche gemacht,
 * nicht hier — siehe den Kranz-Abschnitt.
 *
 * GEFRAGT WIRD BEIM RENDERN, nicht beim Speichern. Ein beim Speichern
 * eingefrorener Wert waere die Lage von damals; paart der SysOp morgen einen
 * Nachbarn, soll die Zeile ohne sein Zutun wahr werden.
 *
 * ICH RUFE, ICH HAENGE MICH NICHT EIN. 'gepaart' ist einstimmig: nur Kleinrock
 * weiss, ob dieser Knoten Kanten hat. Eine Ersatzantwort von mir — auch „nur
 * zur Sicherheit" — waere genau der Fall, den 'fehlt' bereits beantwortet.
 *
 * DASS ICH DAMIT kern.php BRAUCHE, ist eine Aenderung gegenueber Bericht 02
 * („braucht weder kern.php noch speicher.php"). kern.php ist Boden und reist in
 * jedem Paket mit; ein function_exists() davor waere die Frage nach einem
 * Symptom statt nach einer Tatsache. */
if (!function_exists('cluster_one_post_dolly_foederation')) {
function cluster_one_post_dolly_foederation(): bool {
    $c1_opts = cluster_one_post_dolly_opts();
    if (empty($c1_opts['foederation'])) { return false; }
    return cluster_one_auskunft('gepaart') === 'ja';
}
}

/* VERTRAG 12 — cluster_one_foederation.
   Kleinrock ruft, diese Datei haengt sich ein. Die Vorgabe steht beim Rufer.

   '? true : $c1_w' und nicht schlicht zurueckgeben: Ein Filter bekommt, was der
   Vorherige zurueckgab. Gaebe es je einen zweiten Anbieter, wuerde ein nacktes
   return false dessen true loeschen.
       Ein Filter, der die Antwort eines anderen ueberschreibt, ist kein
       Filter, sondern ein Riegel. */
add_filter('cluster_one_foederation', function ($c1_w) {
    return cluster_one_post_dolly_foederation() ? true : $c1_w;
});

/** Wo Post Dolly erscheinen darf: 'backend', 'frontend' oder 'beides'. */
function cluster_one_post_dolly_ort(): string {
    $c1_opts = cluster_one_post_dolly_opts();
    $c1_ort  = isset($c1_opts['ort']) ? (string) $c1_opts['ort'] : 'beides';
    return in_array($c1_ort, array('backend', 'frontend', 'beides'), true) ? $c1_ort : 'beides';
}

/** 0.4.7 (03.11.2026, Neo), Runde Omega — WER SIEHT DAS FELD AUF DER UEBERSICHT: 'mitglieder' (Vorgabe) oder 'alle'.
 *  Nemesus auf der DEV als MenschC, nicht auf der Weissliste: er sah Post Dolly auf der Uebersicht. Seine Regel: wer nicht auf
 *  der Liste des Knotens steht, sieht von Cluster One nichts — auch kein Zitat. Vorgabe darum 'mitglieder'; 'alle' ist die
 *  alte Welt (bis 0.4.6: jeder, der die Uebersicht sehen darf), fuer den, der sie will. Das Frontend bleibt unberuehrt. */
function cluster_one_post_dolly_dashboard_wer(): string {
    $c1_opts = cluster_one_post_dolly_opts();
    $c1_wer  = isset($c1_opts['dashboard']) ? (string) $c1_opts['dashboard'] : 'mitglieder';
    return in_array($c1_wer, array('mitglieder', 'alle'), true) ? $c1_wer : 'mitglieder';
}

/* DIESE DATEI IST ZUSAMMENGEFUEHRT — 17.09.2026, PROMPT-POST-DOLLY-36 §4.

   Zwei Fassungen liefen auseinander, und keine war die neuere:

       Paket 0.3.19  includes/post-dolly.php  1b1be4da2f8e  53988 B
                     hatte die eigene Seite (cluster_one_post_dolly_seite)
       mein Baum     post-dolly.php           dc221d1d734e  59287 B
                     hatte cluster_one_post_dolly_quelle() und
                     cluster_one_post_dolly_raeumen()

   GRUNDLAGE IST DAS PAKET. Was aus meinem Baum kommt, steht unten mit
   dem Vermerk, woher. Zwei Stellen waren keine Zusammenfuehrung, sondern
   eine WAHL; beide stehen in post-dolly-an-mammut-36.md §4 und hier:

     1  cluster_one_post_dolly_lager()  — MEINE Fassung.
        Die des Pakets liest 'wirt' + 'doppelt' und kennt 'melder' nicht.
        VERTRAG 27 §5 laesst 'doppelt' fallen; danach gibt sie still eine
        Null zurueck. VERTRAG 27 §5.2 verlangt die drei Zeilen im
        Wortlaut, und die stehen nur in meiner.
            Von zwei Fassungen, die beide laufen, ist die richtige die,
            die auch morgen noch laeuft.

     2  Der Satz unter dem Beispielzitat — DIE FASSUNG DES PAKETS.
        Meine sagt „one of your own", die des Pakets „one of your own
        quotes". Der Unterschied ist EIN Wort in einer MSGID, und die des
        Pakets steht im Katalog (languages/…-de_DE.po :1221, ein Treffer).
        Meine haette die Uebersetzung verwaist.
            Wer eine msgid aendert, aendert den Text und nicht die
            Sprache.   (Hollerith, 17.09.)

   UND EINE ROLLE IST NICHT ZURUECKGEKOMMEN, mit Absicht:

        add_filter('cluster_one_kranz_abschnitte', …)   Platz 65

   Sie steht in MEINER Fassung und ist am 16.09. im Paket gefallen. Eine
   blinde Vereinigung holte sie zurueck — und dann stuende dasselbe
   Formular an zwei Orten und schriebe dieselbe Option.
   P1-12: keine zwei SCHREIBwege auf eine Wahrheit.
   zeuge-zusammenfuehrung.php fuehrt sie als VERBOTEN und wird rot, wenn
   sie wieder auftaucht — das ist der Riegel zu dieser Entscheidung und
   nicht nur ihre Geschichte (R-PAPIER-1).

   Zwoelf gemeinsame Funktionen sind nach Domaenen-Ersetzung BYTEGLEICH
   und wurden nicht angefasst. */

/** Wie viele der fuenf Lights haben sich auf diesem Knoten angemeldet? 0..5.
 *
 * ---------------------------------------------------------------------------
 * GEZAEHLT WIRD, WAS LAEUFT — NICHT, WAS LIEGT (Christian, 02.09.2026).
 *
 * Das ist eine Entscheidung GEGEN eine Aenderung, und sie steht hier, weil eine
 * solche sonst nirgends steht. Die Frage war echt: `get_plugins()` waere ehrlich
 * ueber BESITZ, Kasper ist ehrlich ueber WIRKUNG. Den Ausschlag gab
 *
 *     Ein abgeschaltetes Plugin ist kein Zustand, sondern eine Absicht.
 *     Der Berg belohnt Zustaende.
 *
 * Wer hier mit `get_plugins()` ankommt, haelt es fuer eine Verbesserung. Es ist
 * eine andere Quelle und eine andere Aussage: der Berg stiege dann fuer etwas,
 * das nichts tut.
 *
 * ---------------------------------------------------------------------------
 * DIE GRENZE VON GESTERN IST EINGELOEST (v1.9.4 — H112 / H119):
 *
 * Hier stand: „Gezaehlt wird, WER SICH MELDET — nicht, wer zur Familie
 * gehoert. Nichts prueft die Zugehoerigkeit. Und die Zahl wird bei 5
 * gekappt. Beides faellt weg, sobald H112 einen Vertrag hat."
 *
 * H112 hat jetzt einen. Er heisst NICHT „der Ordner beginnt mit cluster-one"
 * — das waere H179/H184 an einer sechsten Stelle. Er heisst:
 *
 *     WER DEN BODEN TRAEGT, GEHOERT ZUR FAMILIE.
 *
 * Am 02.09. an allen fuenf ausgelieferten Paketen gemessen, nicht vermutet:
 * jedes meldet `boden`. Turing meldet `turing` und KEINEN Boden — es faellt
 * heraus, ohne dass irgendwo sein Name steht. Genau die Zeile, die Christian
 * auf Andreas gesehen hat (zeuge-h119, A2: 5 -> 4, sobald ein Plugin fehlt,
 * das mit der Route nichts zu tun hat).
 *
 *     Ein Name sagt, wie etwas heisst. Eine mitgelieferte Datei sagt,
 *     was es ist.
 *
 * WOHER DIE WIRTE KOMMEN: Kasper behaelt beim ersten Melder den Wirt und legt
 * jeden weiteren nach 'doppelt'. Der Boden reist in JEDEM Produkt — also
 * stehen alle Traeger genau dort, und keiner muss geraten werden.
 *
 * DER DECKEL BEI 5 IST WEG. Er hat den Zaehlfehler nicht verhindert, sondern
 * an den Gipfel verlegt, wo niemand mehr hinsieht (zeuge-h119, P5).
 *
 *     Ein Deckel, der einen Zaehlfehler verdeckt, ist keine Grenze.
 *     Er ist ein Versteck.
 *
 * Diese Funktion gibt die WAHRE Zahl. Das Bild zeichnet hoechstens fuenf
 * Etappen — der Berg hat fuenf —, und der Satz darunter nennt seit H174
 * Plugins statt Etappen und bleibt damit auch bei sechs wahr.
 *
 * 'cluster-one' zaehlt weiter nicht mit, und auch das ist keine
 * Namenspruefung im Sinne von H184: Mammut ist der URSPRUNG, aus dem alle
 * anderen den Boden haben, und keine Etappe. Faellt Mammut eines Tages von
 * allen Knoten (DAS-HAUS §11), faellt diese Zeile mit ihm. */
/** Laeuft auf diesem Knoten ein Pruefwerkzeug, das keinen Boden traegt?
 *
 * DREI ANTWORTEN, UND DIE DRITTE IST DER GRUND FUER DEN `?bool`:
 *
 *     true    gemessen: es ist da
 *     false   gemessen: es ist nicht da
 *     null    NICHT GEMESSEN — es gibt niemanden, den ich fragen koennte
 *
 * WARUM NICHT EINFACH bool: ein geschlossenes Auge ist eine Auskunft
 * (ENTSCHEIDUNG 10.10.). Eine Auskunft, die aus einer ausgefallenen Messung
 * kommt, ist keine — sie sieht nur wie eine aus.
 *
 *     Ein bool hat zwei Werte und drei Faelle. Der dritte wird sonst auf
 *     einen der ersten zwei abgebildet, und das ist immer der falsche.
 *
 * DER RIEGEL IST KEINE FORMSACHE. `cluster_one_modul_da()` wohnt in
 * kasper.php, also ueber der Hausgrenze. Er liegt heute in jedem Paket, das
 * diese Datei mitnimmt — aber auf einem Knoten gewinnt der AELTESTE Boden die
 * Verdraengung, und wessen kasper.php dann laeuft, entscheidet nicht dieses
 * Paket. Gemessen in vier Fassungen (05.09. · 1.9.14 · 1.12.0 · 0.3.27): die
 * Funktion steht in allen vier. Der Riegel sagt dem naechsten Leser, dass die
 * Abhaengigkeit bekannt ist; kein Riegel sagt ihm gar nichts.
 *
 * BENANNTE GRENZE 1 — DER ORGANNAME. Gefragt wird nach dem Organ 'turing',
 * weil sich das Werkzeug unter diesem Namen bei Kasper anmeldet
 * (turing.php:362). Das ist ein Name im Code und kein Anzeigename: der
 * Anzeigename hat sich am 09.10.2026 geaendert („Maillart (Turing)"), der
 * Organname nicht. Aendert sich auch er, schliesst sich das Auge — und dann
 * ist das geschlossene Auge falsch. Es ist die einzige Zeichenkette, an der
 * dieses Bild haengt, und sie steht genau hier.
 *
 * BENANNTE GRENZE 2 — DER ZEITPUNKT. `cluster_one_modul_da()` gibt vor
 * 'plugins_loaded' die falsche Antwort (kasper.php, eigene benannte Grenze).
 * Diese Frage wird an zwei Stellen gestellt, und beide zeichnen erst beim
 * Aufbau einer Admin-Seite: das Dashboard-Feld und die eigene Seite unter
 * `admin_menu`. Beide liegen nach 'plugins_loaded'.
 *
 *     Wer eine Entscheidung an modul_da() haengt, haengt sie an den Hook —
 *     nicht an die Ladezeit.                               (kasper.php)
 *
 * UND SIE ZAEHLT NICHT MIT: cluster_one_post_dolly_lager() bleibt bei den
 * Boden-Traegern. Wer dazugehoert, entscheidet der Boden; dieses Werkzeug
 * gehoert nicht dazu, es sieht zu. */
if (!function_exists('cluster_one_post_dolly_turing_da')) {
function cluster_one_post_dolly_turing_da(): ?bool {
    if (!function_exists('cluster_one_modul_da')) { return null; }
    return cluster_one_modul_da('turing');
}
}

function cluster_one_post_dolly_lager(): int {
    $c1_alle = cluster_one_kasper();
    if (!is_array($c1_alle) || !isset($c1_alle['boden']) || !is_array($c1_alle['boden'])) {
        return 0;
    }

    $c1_boden = $c1_alle['boden'];
    $c1_wirte = array();

    /* VERTRAG 27 §5 — DIE QUELLE WECHSELT, DIE REGEL NICHT.
     *
     * Bis 1.9.13 stand dieselbe Wahrheit in zwei Feldern: 'wirt' (der erste
     * bzw. der deklarierende) und 'doppelt' (alle weiteren). Seit 1.9.14 steht
     * sie an EINER Stelle, als Liste: 'melder', je Eintrag ein Wirt.
     *
     * WAS SICH NICHT AENDERT, und das ist die Haelfte, auf die es ankommt:
     * gezaehlt werden weiterhin VERSCHIEDENE WIRTE, minus 'cluster-one'.
     * `cluster_one_organ_anmeldungen('boden')` waere die kuerzere Zeile und die
     * falsche Zahl — sie zaehlt ANMELDUNGEN und zieht Mammut nicht ab.
     *
     *     Zwei Zaehler, die dieselbe Tabelle lesen, zaehlen deshalb noch
     *     nicht dasselbe. Der Unterschied steht nicht in der Quelle,
     *     sondern in der Frage.
     *
     * DER RUECKFALL IST KEINE ZWEITE WAHRHEIT, SONDERN EINE ZWEITE FASSUNG.
     * Ein Knoten kann einen aelteren Boden tragen als das Paket, aus dem diese
     * Datei stammt — dort gibt es 'melder' noch nicht. Gelesen wird deshalb
     * zuerst 'melder'; fehlt es, die alten zwei Felder. Beide Wege beantworten
     * dieselbe Frage und liefern dieselbe Zahl; gemessen ist das, nicht
     * angenommen.
     *
     *     Ein Rueckfall auf eine aeltere FASSUNG derselben Auskunft ist
     *     erlaubt. Ein Rueckfall auf eine andere AUSKUNFT waere der
     *     Praefix-Besen noch einmal.
     *
     * Er faellt weg, wenn 'doppelt' faellt (VERTRAG 27 §5, Schritt 4). */
    /* VERTRAG 27 §5.2 — DIE DREI ZEILEN, IM WORTLAUT DES VERTRAGS.
       DRITTE FASSUNG DIESER STELLE, und die zweite war meine eigene falsche
       Lesung. Hier stand zuerst `if ($c1_melder)` (der INHALT), dann
       `array_key_exists('melder', …)` ALLEIN — und genau das verbietet §5.2
       namentlich als HALBE FORM:

           VERBOTEN in jedem Leser:
               array_key_exists('melder', $e) ALLEIN     halbe Form

       Ich hatte gegen ein Zitat gebaut, in dem der Satz „die Anwesenheit des
       Schluessels IST die Ankuendigung" stand — und die Zeile, die daraus die
       halbe Form macht, stand nicht darin. Beides ist wahr: die Anwesenheit
       ist die Ankuendigung, aber sie ist erst eine, wenn auch die FORM stimmt.

           Ein Schluessel ist eine Ankuendigung. Ein Schluessel mit einer
           Zeichenkette darunter ist eine angefangene.

       'melder' da und LEER    -> neue Form, kein Melder. Kein Rueckfall.
       'melder' da, kein array -> halbe Form. RUECKFALL, und er sagt es.
       'melder' fehlt          -> alte Form. Rueckfall. */
    $c1_melder = (isset($c1_boden['melder']) && is_array($c1_boden['melder']))
        ? $c1_boden['melder'] : null;
    if ($c1_melder !== null) {
        foreach ($c1_melder as $c1_m) {
            $c1_w = (string) (is_array($c1_m) ? ($c1_m['wirt'] ?? '') : '');
            if ($c1_w !== '') { $c1_wirte[$c1_w] = true; }
        }
    } elseif (!empty($c1_boden['doppelt']) || !empty($c1_boden['wirt'])) {
        $c1_w = (string) ($c1_boden['wirt'] ?? '');
        if ($c1_w !== '') { $c1_wirte[$c1_w] = true; }

        foreach ((array) ($c1_boden['doppelt'] ?? array()) as $c1_d) {
            $c1_d = (string) $c1_d;
            if ($c1_d !== '') { $c1_wirte[$c1_d] = true; }
        }
    }

    unset($c1_wirte['cluster-one']);

    return count($c1_wirte);
}

/** VERTRAG 27 §5.3 — AUS WELCHER QUELLE HAT DIESES HAUS GELESEN?
 *
 * WEG A, von Mammut entschieden (11.09.): eine eigene Funktion, die dieselbe
 * Tabelle NOCH EINMAL liest und nur die Quellenfrage beantwortet. Der Preis
 * sind zwei Leser auf eine Wahrheit — LESEND, und das ist erlaubt: P1-12
 * verbietet zwei SCHREIBWEGE. Weg B haette `lager()` eine Nebenwirkung
 * gegeben, und deren Zaehlregel ist tabu.
 *
 * WARUM EIN FELD UND KEINE ZEICHENKETTE, obwohl der Auftrag `: string`
 * zeichnet: §5.3 verlangt im Wortlaut, „wie OFT er aus 'melder' und wie oft
 * aus 'doppelt' gelesen hat", und die Null, an der 'doppelt' stirbt, ist eine
 * SUMME ueber alle Leser aller Haeuser.
 *
 *     Ein Name laesst sich nennen und nicht addieren. Ein Sterbedatum, das
 *     man ablesen kann, braucht eine Zahl.
 *
 * Bei mir ist die Zahl klein: EIN Leser, EIN Organ ('boden'), also 1 und 0
 * oder 0 und 1. Das ist kein Grund, sie nicht zu fuehren — die gemessene Null
 * aus §5.3 entsteht aus lauter kleinen Zahlen, und eine fehlende ist keine
 * Null, sondern eine Luecke.
 *
 * DIE DREI ZEILEN SIND WOERTLICH DIE VON `lager()`. Dass zwei Leser dieselbe
 * Form pruefen, ist die Stelle, an der sie auseinanderlaufen koennen — also
 * haelt sie etwas gleich: `zeuge-produkt.php` faehrt beide gegen dieselben
 * Welten und vergleicht.
 *
 *     Zwei Lesewege sind erlaubt, solange etwas sie gleich haelt. Ohne
 *     dieses Etwas sind sie zwei Meinungen.
 *
 * BENANNTE GRENZE: sie zaehlt, was DIESES HAUS gelesen hat, nicht was auf dem
 * Knoten liegt. Ein Boden ohne 'boden'-Eintrag ergibt 0 und 0 — das ist
 * „nicht gelesen" und nicht „kein Rueckfall". */
if (!function_exists('cluster_one_post_dolly_quelle')) {
function cluster_one_post_dolly_quelle(): array {
    $c1_zahl = array('melder' => 0, 'doppelt' => 0);

    $c1_alle = cluster_one_kasper();
    if (!is_array($c1_alle) || !isset($c1_alle['boden']) || !is_array($c1_alle['boden'])) {
        return $c1_zahl;
    }
    $c1_boden = $c1_alle['boden'];

    $c1_melder = (isset($c1_boden['melder']) && is_array($c1_boden['melder']))
        ? $c1_boden['melder'] : null;
    if ($c1_melder !== null) {
        $c1_zahl['melder']++;
    } elseif (!empty($c1_boden['doppelt']) || !empty($c1_boden['wirt'])) {
        $c1_zahl['doppelt']++;
    }

    return $c1_zahl;
}
}

/** Der Berg als inline-SVG.
 *
 * INLINE UND NICHT ALS BILDDATEI: kein zweiter Request, skaliert, und die
 * Etappen sind einzelne Pfade, die sich ueber ein Attribut schalten lassen.
 * Die Farben kommen aus currentColor und einer Akzentfarbe, damit der Berg in
 * hellen und dunklen Admin-Schemata lesbar bleibt.
 *
 * DIE FUNKWELLEN LAUFEN NICHT. Eine Dauer-Animation im Admin-Bereich waere die
 * einzige Art, wie dieses Bild doch noch aufdringlich wird — ausgerechnet an
 * der Belohnung. Sie stehen still. */
function cluster_one_post_dolly_berg(int $c1_stufen, bool $c1_funk, ?bool $c1_auge = null): string {
    $c1_route = array(
        array(14, 126, 58, 112),
        array(58, 112, 92,  92),
        array(92,  92, 128, 70),
        array(128, 70, 166, 46),
        array(166, 46, 200, 18),
    );

    /* H174 (02.09.2026): DIE BESCHRIFTUNG SAGT NICHT MEHR „ETAPPEN".
       Hier stand „%d of 5 stages reached." — eine Zahl aus dem Dateisystem mit
       einer Beschriftung ueber die Reichweite. Auf Christians Knoten Andreas
       hat sie den Gipfel gemeldet, weil turing und connector-vault mitgezaehlt
       wurden; ohne die beiden waeren es vier gewesen.

           Eine Zahl vom Dateisystem darf keine Beschriftung ueber die Welt
           tragen.

       ZWEITE FASSUNG, NOCH AM SELBEN TAG (Christian, 02.09.2026): Meine erste
       Reparatur hiess „%d plugins have announced themselves on this node." Sie
       war wahr, aber sie sprach Hausdialekt — „sich angemeldet" versteht nur,
       wer Kasper kennt. Christians Satz sagt dasselbe in der Sprache dessen,
       der das Plugin installiert hat.

       UND ER IST SCHAERFER, ALS MEINER WAR. „Wie viele … laufen" macht den
       Deckel bei 5 sichtbar, den „sich angemeldet" verdeckt hat. Das ist
       gewollt:

           Ein Satz, der genau sagt, was gezaehlt wird, macht eine falsche
           Zaehlung sichtbar. Das ist kein Grund gegen den Satz. Das ist sein
           Nutzen.

       Der Satz UEBER dem Bild und dieser hier sagen dasselbe und muessen
       zusammen wandern. Wer nur einen anfasst, erzeugt zwei Wahrheiten auf
       einer Seite; `zeuge-post-dolly.php` (F6) haelt sie aneinander. */
    $c1_label = sprintf(
        /* translators: %d = number of this house's plugins running on this node. */
        __('%d plugins of this house are running on this node.', 'cluster-one-post-dolly'),
        $c1_stufen
    );
    /* Auch dieser Satz hat die Reichweite behauptet: „This node is sending from
       the summit." Die Funkwellen haengen seit H144 an einer aktiven Kante und
       ausdruecklich NICHT an der Zahl (Christian, 26.08.: „egal wieviel ausser
       Post Dolly noch im Knoten aktiviert sind"). Der Satz sagte trotzdem, der
       Gipfel sei erreicht — auf einem Knoten, der bei zwei Etappen steht.
       Jetzt sagt er die Bedingung, die tatsaechlich erfuellt ist. */
    if ($c1_funk) {
        $c1_label .= ' ' . __('This node declares itself publicly and has an active connection.', 'cluster-one-post-dolly');
    }

    /* DAS AUGE SAGT ES AUCH IN WORTEN, und es nennt KEINEN NAMEN.
       Der Grund ist derselbe, aus dem im Bild kein Buchstabe steht: ein Name
       kann sich aendern, und dieses Bild wird nicht mitgeaendert. Am
       09.10.2026 ist genau das passiert. Die Beschriftung nennt deshalb die
       ROLLE und nicht das Produkt.

           Ein Buchstabe ist ein Name, und eine Beschriftung ist ein Satz
           voller Namen. Dasselbe Argument, eine Zeile tiefer.

       WARUM UEBERHAUPT WORTE: das Bild hat genau eine Beschriftung, und wer
       es nicht sehen kann, hoert nur sie. Ein Zeichen, das nur fuer Augen
       da ist, hat fuer die Haelfte der Leser nie existiert.

       BEI null STEHT HIER NICHTS. Das ist derselbe Satz wie im Bild: eine
       Messung, die nicht stattgefunden hat, wird nicht beschrieben. */
    if ($c1_auge === true) {
        $c1_label .= ' ' . __('An independent checking tool is running on this node.', 'cluster-one-post-dolly');
    } elseif ($c1_auge === false) {
        $c1_label .= ' ' . __('No independent checking tool is running on this node.', 'cluster-one-post-dolly');
    } else {
        /* DRITTE BESCHRIFTUNG, 10.10.2026. Bis heute stand hier NICHTS, mit
           meiner Begruendung „eine Messung, die nicht stattfand, wird nicht
           beschrieben". Richtig gedacht, falsche Voraussetzung: die Messung
           HAT stattgefunden — das Geraet liegt im eigenen Paket und ist
           gefragt worden. Was ausbleibt, ist die Antwort.

               Ein Defekt, der nur gezeichnet und nicht gesagt wird, ist
               fuer den Blinden kein Defekt, sondern Stille.
                                                   (Mammut, 10.10.2026) */
        $c1_label .= ' ' . __('This node cannot say whether an independent checking tool is running.', 'cluster-one-post-dolly');
    }

    $c1_svg  = '<svg class="cluster-one-dolly-berg" viewBox="0 0 240 140" width="240" height="140"';
    $c1_svg .= ' role="img" aria-label="' . esc_attr($c1_label) . '" focusable="false">';
    $c1_svg .= '<polygon points="2,134 62,62 96,92 138,44 200,12 236,134" fill="currentColor" fill-opacity=".08"';
    $c1_svg .= ' stroke="currentColor" stroke-opacity=".35" stroke-width="1.5"/>';

    foreach ($c1_route as $c1_i => $c1_s) {
        $c1_erreicht = ($c1_i < $c1_stufen);
        $c1_svg .= '<line x1="' . (int) $c1_s[0] . '" y1="' . (int) $c1_s[1] . '"';
        $c1_svg .= ' x2="' . (int) $c1_s[2] . '" y2="' . (int) $c1_s[3] . '"';
        $c1_svg .= ' stroke="' . ($c1_erreicht ? '#2271b1' : 'currentColor') . '"';
        $c1_svg .= ' stroke-opacity="' . ($c1_erreicht ? '1' : '.2') . '"';
        $c1_svg .= ' stroke-width="' . ($c1_erreicht ? '3' : '2') . '"';
        $c1_svg .= ' stroke-linecap="round"' . ($c1_erreicht ? '' : ' stroke-dasharray="4 5"') . '/>';
    }

    foreach ($c1_route as $c1_i => $c1_s) {
        $c1_svg .= '<circle cx="' . (int) $c1_s[0] . '" cy="' . (int) $c1_s[1] . '" r="3"';
        $c1_svg .= ' fill="' . (($c1_i < $c1_stufen) ? '#2271b1' : 'currentColor') . '"';
        $c1_svg .= ' fill-opacity="' . (($c1_i < $c1_stufen) ? '1' : '.25') . '"/>';
    }
    $c1_svg .= '<circle cx="200" cy="18" r="3.5" fill="' . (($c1_stufen >= 5) ? '#2271b1' : 'currentColor');
    $c1_svg .= '" fill-opacity="' . (($c1_stufen >= 5) ? '1' : '.25') . '"/>';

    if ($c1_funk) {
        foreach (array(9, 15, 21) as $c1_r) {
            $c1_svg .= '<path d="M ' . (200 - $c1_r) . ' ' . (18 - (int) round($c1_r * 0.45));
            $c1_svg .= ' A ' . $c1_r . ' ' . $c1_r . ' 0 0 1 ' . (200 + $c1_r) . ' ' . (18 - (int) round($c1_r * 0.45));
            $c1_svg .= '" fill="none" stroke="#2271b1" stroke-opacity=".7" stroke-width="1.5"/>';
        }
    }

    /* -------------------------------------------------------- DAS ZEICHEN
       ZWEITE FASSUNG. ENTSCHEIDUNG „Das Auge des Horus und der Strich statt
       der Blaesse", 10.10.2026, nach Christians Blick auf das Bild.

       WAS GEFALLEN IST UND WARUM — die alte Fassung steht nicht mehr hier,
       aber ihr Grund schon (R-PAPIER-1):

         die Ellipse auf 214,134 mit einem Punkt darin
             „Das ist die Form eines Knopfes, nicht die eines Auges."
             Ich hatte „ein kleines Auge" bestellt bekommen, ohne die Form —
             und die billigste Beschreibung eines Auges beschreibt es nicht.

         der geschlossene Bogen mit Deckkraft .2
             Ich hatte „schwach wie die gestrichelte Etappe" woertlich
             genommen und dieselbe Zahl gesetzt. Messbar war es, richtig
             nicht: .2 traegt eine Linie von vierzig Einheiten MIT Muster,
             nicht einen Bogen von zwanzig ohne.
                 Dieselbe Zahl, ein Drittel der Tinte und kein Muster. Eine
                 Deckkraft ist keine Eigenschaft des Zustands, sondern der
                 Flaeche, auf der sie liegt.
             Und die Folge war die, die der Posten verhindern sollte: ein
             Zustand, den man nicht sehen kann, ist derselbe wie ein
             fehlender.

       ES GIBT DESHALB KEINE ABGESCHWAECHTE DECKKRAFT MEHR, in keinem
       Zustand. Alles ist currentColor bei voller Deckkraft.

           Ein Strich ist eine Aussage, eine Blaesse ist nur weniger Tinte.

       DREI ZUSTAENDE, UND DER DRITTE IST DER LAUTESTE:

           true    das Auge              eine Tatsache: es laeuft
           false   dasselbe Auge, durchgestrichen
                                         eine Tatsache: es laeuft nicht
           null    ein Warndreieck       ein DEFEKT: der Knoten kann es
                                         nicht sagen

       WARUM null EIN DEFEKT IST UND NICHT EIN SCHWEIGEN, und das ist eine
       Berichtigung an mir: ich hatte hier „kein Zeichen" gebaut, weil ohne
       kasper keine Auskunft vorliegt. Die Frage, die das entscheidet, habe
       ich nicht gestellt — wann kann null ueberhaupt eintreten? Gemessen in
       cluster-one-post-dolly-0_3_29.zip: includes/kasper.php, 67199 B,
       `cluster_one_modul_da` ein Treffer. Dieses Organ liefert das
       Messgeraet SELBST AUS. Zeichnet es den Berg, laeuft es; laeuft es,
       liegt ein kasper daneben. Bleibt die Antwort aus, hat ein aelterer
       Boden die Deklarationsrunde gewonnen oder es laeuft gar keiner —
       beides ein Defekt.

           2 NICHT GEMESSEN = niemand hat gefragt.
           3 KAPUTT         = es wurde gefragt, das Geraet antwortet nicht.
           Dieses Organ fragt immer.

       DIE GEOMETRIE kommt aus `das-auge-GEWAEHLT.html`, Zeichen fuer
       Zeichen uebernommen und nicht nachgezeichnet. Oertliche Box des
       Auges: Ursprung 198,105, 28 x 20. Blick nach LINKS, zum Gipfel.

       NACHGEMESSEN, UND EINE ZAHL IST DABEI UM EINEN HALBEN PUNKT ZU KLEIN:
       der Stuetzpunkt der Braue liegt auf 211,104.50 — ein halber Punkt
       UEBER der Box. Die gezeichnete Kurve erreicht ihn nicht; ihr hoechster
       Punkt liegt bei y = 106.65 (quadratische Bezier, Scheitel bei
       t = 11/18). Die Box gilt also fuer die TINTE und nicht fuer die
       Stuetzpunkte, und der Zeuge sagt beides getrennt.

           Ein Stuetzpunkt ist keine Tinte. Aber er ist auch keine Null —
           wer nur die Tinte prueft, laesst die konvexe Huelle durch.

       DER STRICH LIEGT ABSICHTLICH AUSSERHALB der Augen-Box (197,126 ->
       226,105): ein Strich, der am Gegenstand endet, sieht aus wie ein
       Teil von ihm.

       WAS DAS ZEICHEN NICHT TUT — unveraendert:
           keine Stufe · keine Linie zur Route · keine Zahl ·
           nicht die Farbe der Route (#2271b1) · keine Bewegung

       JEDES STUECK TRAEGT SEINE FAMILIE UND SEINEN NAMEN. Nicht fuer den
       Browser, sondern fuer den Zeugen: eine Zeichnung ohne Namen laesst
       sich nicht messen, und ein Zeuge, der eine Form aus Koordinaten
       erraet, misst seine eigene Rechnung. */
    if ($c1_auge === null) {
        /* DER RUF — Warndreieck mit Ausrufezeichen, Mitte 214,116, Hoehe 19. */
        $c1_svg .= '<path class="cluster-one-dolly-zeichen ruf-rahmen"';
        $c1_svg .= ' d="M 214.00 105.55 L 223.50 124.55 L 204.50 124.55 Z"';
        $c1_svg .= ' fill="none" stroke="currentColor" stroke-opacity="1" stroke-width="1.4" stroke-linejoin="round"/>';
        $c1_svg .= '<line class="cluster-one-dolly-zeichen ruf-balken"';
        $c1_svg .= ' x1="214.00" y1="112.96" x2="214.00" y2="119.23"';
        $c1_svg .= ' stroke="currentColor" stroke-width="2.19" stroke-linecap="round"/>';
        $c1_svg .= '<circle class="cluster-one-dolly-zeichen ruf-punkt"';
        $c1_svg .= ' cx="214.00" cy="122.27" r="1.24" fill="currentColor"/>';
    } else {
        /* DAS AUGE — fuenf Stuecke, in der Reihenfolge, in der sie
           uebereinander liegen muessen: Braue, Lid, Pupille, Traene, Locke.
           OFFEN UND ZU TEILEN DIESE FUENF BYTE-GENAU — zwei Zustaende
           desselben Dings, nicht zwei Bilder, die sich nur am Ort gleichen. */
        $c1_svg .= '<path class="cluster-one-dolly-zeichen auge-braue"';
        $c1_svg .= ' d="M 200.50 110.00 Q 211.00 104.50 223.00 108.00"';
        $c1_svg .= ' fill="none" stroke="currentColor" stroke-opacity="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.20"/>';
        $c1_svg .= '<path class="cluster-one-dolly-zeichen auge-lid"';
        $c1_svg .= ' d="M 199.00 115.50 Q 208.00 110.30 220.00 112.60 Q 210.00 119.20 199.00 115.50"';
        $c1_svg .= ' fill="none" stroke="currentColor" stroke-opacity="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.40"/>';
        $c1_svg .= '<circle class="cluster-one-dolly-zeichen auge-pupille"';
        $c1_svg .= ' cx="206.40" cy="115.00" r="2.30" fill="currentColor" fill-opacity="1"/>';
        $c1_svg .= '<path class="cluster-one-dolly-zeichen auge-traene"';
        $c1_svg .= ' d="M 208.60 118.00 L 206.60 124.60"';
        $c1_svg .= ' fill="none" stroke="currentColor" stroke-opacity="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.90"/>';
        $c1_svg .= '<path class="cluster-one-dolly-zeichen auge-locke"';
        $c1_svg .= ' d="M 216.60 116.90 C 220.20 119.00 223.60 120.40 224.00 122.90 C 224.20 124.60 222.10 125.30 221.60 123.50"';
        $c1_svg .= ' fill="none" stroke="currentColor" stroke-opacity="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.60"/>';

        if ($c1_auge === false) {
            /* DER STRICH. Volle Deckkraft, wie alles andere. */
            $c1_svg .= '<line class="cluster-one-dolly-zeichen auge-strich"';
            $c1_svg .= ' x1="197.00" y1="126.00" x2="226.00" y2="105.00"';
            $c1_svg .= ' stroke="currentColor" stroke-opacity="1" stroke-width="2.00" stroke-linecap="round"/>';
        }
    }

    return $c1_svg . '</svg>';
}

/** Der Block, den Shortcode und Uebersicht gemeinsam ausgeben.
 *
 * P0: Das Zitat kommt aus der Datenbank oder aus einem fremden Filter und geht
 * durch esc_html(). Fremder Text ist niemals HTML — auch nicht der des SysOps,
 * denn der Weg ist derselbe. */
function cluster_one_post_dolly_block(bool $c1_weiter = false, bool $c1_sprachwahl = false): string {
    /* 27.10.2026 (Neo): EINE Zeile aus EINEM Topf. Bis dahin stand hier ein
       Zitat und darunter, wenn der Knoten sich bekennt, ein zweiter Absatz
       mit einer Ansage (20.10.: „Der Satz kommt jetzt aus der zweiten
       Tabelle", davor fest „This page forms a cluster node."). Zwei Absaetze
       je Aufruf waren nicht gemeint; die Ansagen sind jetzt Zeilen im Topf. */
    $c1_z = cluster_one_post_dolly_zug();
    if ($c1_z === array()) { return ''; }

    $c1_text = '<span class="cluster-one-dolly-text">' . esc_html(cluster_one_post_dolly_satz($c1_z)) . '</span>';
    if (!$c1_weiter) {
        return '<div class="cluster-one-dolly"><p class="' . cluster_one_post_dolly_klasse($c1_z) . '">' . $c1_text . '</p></div>';
    }

    /* 27.10.2026 (Neo) — DER WEITER-KNOPF UND DER MITREISENDE SCHLUESSEL,
       Nemesus' Wunsch: „Nach dem Satz kommt ein kleiner Refresh-Button …
       Wenn ich jetzt auf die Flagge Englisch gehe, kommt die englische Seite
       mit demselben Slogan in Englisch."

       Der Topf reist als Datenattribut mit in die Seite, alle Sprachen, mit
       Schluesseln; assets/post-dolly.js schaltet im Browser weiter und merkt
       sich beim Klick auf einen Sprachwechsel-Link den Schluessel im
       sessionStorage, die Zielseite liest ihn und zeigt dieselbe Zeile.
       Alles, was hier steht, ist Text, der ohnehin gezeigt werden soll —
       Vegas-Prinzip unberuehrt. Der Knopf traegt `hidden` und wird vom Skript
       gezeigt: ohne JavaScript bleibt es bei einer Zeile vom Server.

       Die Texte gehen texturiert und entitaet-frei in die Daten: das Skript
       setzt sie als textContent, und „&#8220;" waere dort ein Zeichenstring.

       $c1_sprachwahl (Dashboard, 27.10.2026 abends, Nemesus: „Im Backend im
       Widget gibt es keine Moeglichkeit, die Sprachen zu aendern und die
       Phrase auszutauschen"): dazu eine Reihe kleiner Knoepfe, einer je
       Sprachspalte des Knotens; das Skript schaltet die Sprache des Blocks um
       und zeigt dieselbe Zeile neu. Auch sie tragen `hidden` bis das Skript da ist. */
    $c1_daten = array(
        'sprache' => cluster_one_post_dolly_sprache(),
        'site'    => cluster_one_post_dolly_code((string) get_locale()),
        'zeilen'  => array(),
    );
    foreach (cluster_one_post_dolly_topf() as $c1_r) {
        $c1_e = array('key' => (string) $c1_r['key']);
        foreach ($c1_r as $c1_k => $c1_t) {
            if (cluster_one_post_dolly_ist_code($c1_k) && is_string($c1_t) && trim($c1_t) !== '') {
                $c1_e[$c1_k] = html_entity_decode(wptexturize($c1_t), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
        $c1_daten['zeilen'][] = $c1_e;
    }
    wp_enqueue_script('cluster-one-dolly', plugin_dir_url(dirname(__FILE__)) . 'assets/post-dolly.js', array(), CLUSTER_ONE_POST_DOLLY_VERSION, true);
    $c1_titel = __('Next line', 'cluster-one-post-dolly');

    $c1_wahl = '';
    if ($c1_sprachwahl) {
        $c1_wahl = '<span class="cluster-one-dolly-sprachen" hidden style="display:inline-block;margin-left:.6em;font-size:.8em;">';
        foreach (cluster_one_post_dolly_sprachen() as $c1_s) {
            $c1_wahl .= '<button type="button" class="cluster-one-dolly-sprache" data-sprache="' . esc_attr($c1_s) . '"'
                      . ' aria-pressed="' . (($c1_s === $c1_daten['sprache']) ? 'true' : 'false') . '"'
                      . ' style="background:none;border:1px solid currentColor;border-radius:3px;padding:0 .35em;margin:0 .15em;cursor:pointer;color:inherit;font:inherit;font-size:inherit;line-height:1.5;opacity:.7;">'
                      . esc_html($c1_s) . '</button>';
        }
        $c1_wahl .= '</span>';
    }

    return '<div class="cluster-one-dolly" data-dolly="' . esc_attr((string) wp_json_encode($c1_daten)) . '">'
         /* Reihenfolge: Satz, Sprachtasten, Knopf — Nemesus, 27.10.2026 abends:
            „de en refresh". Vorher stand der Knopf vor den Tasten. */
         . '<p class="' . cluster_one_post_dolly_klasse($c1_z) . '" data-key="' . esc_attr((string) $c1_z['key']) . '">' . $c1_text
         . $c1_wahl
         . ' <button type="button" class="cluster-one-dolly-weiter" hidden title="' . esc_attr($c1_titel) . '" aria-label="' . esc_attr($c1_titel) . '"'
         . ' style="background:none;border:0;padding:0;margin:0 0 0 .35em;cursor:pointer;color:inherit;font:inherit;line-height:1;vertical-align:middle;">'
         . '<svg aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 3v6h-6"/></svg>'
         . '</button></p></div>';
}

/** Der Shortcode. Er ist der einzige Weg ins Frontend.
 *
 * Kein 'wp_footer'-Haken, auch kein abgeschalteter: Post Dolly erscheint dort,
 * wo jemand es hingestellt hat. Der Shortcode IST das Hinstellen.
 *
 * 20.10.2026 (Neo) — ZWEI ATTRIBUTE, Nemesus' Wunsch:
 *     [cluster_one_dolly schluessel="A3"]              genau diese Zeile
 *     [cluster_one_dolly schluessel="B1" sprache="en"] erzwungen englisch
 * Ohne Attribute wie bisher. Ein Schluessel, den es nicht gibt, gibt NICHTS
 * aus — nicht den Zufall: sonst stuende still ein anderer Satz als gemeint.
 * Eine Ansage (B…) erscheint ueber den Schluessel auch dann, wenn der Knoten
 * sich nicht bekennt: wer sie absichtlich setzt, hat sich entschieden. */
function cluster_one_post_dolly_shortcode($c1_atts = array(), $c1_inhalt = null, $c1_tag = ''): string {
    unset($c1_inhalt, $c1_tag);
    if (cluster_one_post_dolly_ort() === 'backend') { return ''; }
    $c1_atts = shortcode_atts(array('schluessel' => '', 'sprache' => ''), is_array($c1_atts) ? $c1_atts : array(), 'cluster_one_dolly');
    if ((string) $c1_atts['schluessel'] === '') { return cluster_one_post_dolly_block(true); }
    $c1_z = cluster_one_post_dolly_zeile((string) $c1_atts['schluessel']);
    if ($c1_z === array()) { return ''; }
    return '<div class="cluster-one-dolly"><p class="' . cluster_one_post_dolly_klasse($c1_z) . '">'
         . esc_html(cluster_one_post_dolly_satz($c1_z, (string) $c1_atts['sprache'])) . '</p></div>';
}
add_shortcode('cluster_one_dolly', 'cluster_one_post_dolly_shortcode');

/* Die Uebersicht im Backend.
   WARUM EIN DASHBOARD-FELD UND NICHT 'admin_notices': Ein Feld auf der
   Uebersicht steht auf genau EINER Seite, und WordPress gibt dem SysOp die
   Anzeigeoptionen, um es zu verschieben oder auszublenden — das Hinstellen ist
   also auch hier seines. 'admin_notices' waere das Gegenteil und ist der Grund,
   warum das Vorbild so oft geloescht wird.

   KEIN RECHTE-RIEGEL, UND DAS IST EINE ENTSCHEIDUNG. Hier stand
   cluster_one_can_access(), geriegelt mit function_exists — und genau das ist
   die Bauform, gegen die kasper.php geschrieben wurde: eine Frage nach einem
   Symptom statt nach einer Tatsache. Sie haette diese Datei ausserdem an
   kern.php gebunden, das ein Floss nicht traegt.

   Der Inhalt rechtfertigt keinen Riegel: Es sind die Zitate des SysOps, und sie
   sind dafuer geschrieben, gelesen zu werden. P0-1 verlangt ein Gate fuer
   SEITEN des Hauses; ein Dashboard-Feld ist keine.

   BENANNTE GRENZE: Steht der Ort auf „nur Backend", sieht das Feld jeder, der
   die Uebersicht sehen darf — nicht nur der SysOp. Wer das nicht will, stellt
   den Ort auf „nur Frontend" oder nimmt das Feld unter Anzeigeoptionen weg.
   0.4.7 (03.11.2026, Neo), RUNDE OMEGA — DER RIEGEL KOMMT DOCH, ALS EINSTELLUNG. Nemesus auf der DEV als MenschC, nicht
   auf der Weissliste: „dennoch konnte ich als MenschC im Dashboard Post-Dolly sehen. Dazu brauchen wir eine Einstellung,
   damit das Vegas-Prinzip voellig gewahrt bleibt." Die zwei Gruende von oben sind nicht mehr, was sie waren: der Inhalt mag
   zum Lesen geschrieben sein, aber wer nicht dabei ist, soll nicht einmal wissen, dass es hier etwas zu lesen gibt; und
   kern.php traegt seit Phi jedes Paket, also ist 'cluster_one_access' hier keine Frage nach einem Symptom mehr, sondern die
   Capability des Hauses. Vorgabe: nur Mitglieder. Wer die alte Welt will, stellt „alle" ein (cluster_one_post_dolly_dashboard_wer). */
add_action('wp_dashboard_setup', function () {
    if (cluster_one_post_dolly_ort() === 'frontend') { return; }
    if (cluster_one_post_dolly_dashboard_wer() === 'mitglieder' && !current_user_can('cluster_one_access')) { return; }   // 0.4.7 (Omega)
    wp_add_dashboard_widget(
        'cluster_one_dolly',
        __('Post Dolly', 'cluster-one-post-dolly'),
        'cluster_one_post_dolly_uebersicht'
    );
});

/** Der Inhalt des Dashboard-Feldes: das Zitat, darunter der Berg. */
function cluster_one_post_dolly_uebersicht(): void {
    /* 27.10.2026 (Neo): mit Weiter-Knopf und Sprachwahl, Nemesus' Wunsch.
       Bis dahin cluster_one_post_dolly_block() ohne Argumente: eine Zeile, stumm. */
    $c1_block = cluster_one_post_dolly_block(true, true);
    if ($c1_block !== '') {
        /* 27.10.2026 abends (Neo): Hier stand echo wp_kses_post($c1_block).
           Nemesus auf der Main: „Refresh-Button fehlt (Backend)." wp_kses_post
           laesst <button> durch, streicht aber das <svg> darin — ein leerer,
           unsichtbarer Knopf, waehrend die Sprachtasten (Text) blieben. Der
           Block ist eigenes Markup: jeder fremde Wert geht darin durch
           esc_html() oder esc_attr(), das Kses-Netz darunter fing nur das SVG. */
        echo $c1_block; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- eigenes Markup, jeder eingesetzte Wert ist escaped
    }
    /* 0.3.60 (31.10.2026, Neo): die Zeile fuer den, der entscheidet – nur wenn
       etwas offen ist. Das Feld steht nur bei Ort 'backend' oder 'beides'; bei
       'frontend' bleibt die Zahl am Menue der eine Hinweis. */
    if (function_exists('cluster_one_post_dolly_darf_speichern') && cluster_one_post_dolly_darf_speichern()) {
        $c1_n = cluster_one_post_dolly_offene();
        if ($c1_n > 0) {
            echo '<p style="margin:.8em 0 0;"><a href="' . esc_url(admin_url('admin.php?page=cluster_one_post_dolly_tabelle')) . '">'
                /* translators: %d: number of open proposals */
                . esc_html(sprintf(_n('%d open proposal waits for your decision.', '%d open proposals wait for your decision.', $c1_n, 'cluster-one-post-dolly'), $c1_n))
                . '</a></p>';
        }
    }
    echo '<p style="margin:.8em 0 0;color:currentColor;">';
    echo cluster_one_post_dolly_berg(cluster_one_post_dolly_lager(), cluster_one_post_dolly_foederation(), cluster_one_post_dolly_turing_da()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- selbst erzeugtes SVG, jeder eingesetzte Wert ist int oder escaped
    echo '</p>';
}

/* ---------------------------------------------------------------------------
   Die Einstellungen. Sie wohnen im Kranz, als angemeldeter Abschnitt.

   65 ALS ORDNUNGSZAHL: 50 ist Kleinrocks Steckbrief, 55 die Gefahrenzone, 60
   Umbertos Umgebung. 65 setzt Post Dolly dahinter — es ist der einzige
   Abschnitt, der etwas nach draussen stellt, und er gehoert ans Ende der Seite,
   nicht zwischen die inneren Angelegenheiten des Knotens.
   --------------------------------------------------------------------------- */

/* H217 (05.09.2026) — EIGENE GRUPPE, UND DER GRUND IST KEINE ORDNUNGSLIEBE.
 *
 * Hier stand `cluster_one_settings_group` — die Gruppe des Kranz —, begruendet
 * mit „damit sein eines Formular auch diese Option speichert". Diese
 * Begruendung war zweimal falsch:
 *
 * 1. SEIN FORMULAR HAT MEINE FELDER NIE ENTHALTEN. Es endet vor den
 *    angemeldeten Abschnitten; W37 misst das am gerenderten HTML: elf Felder
 *    DRAUSSEN. Christian hat es viermal gemeldet.
 *
 * 2. UND WAERE SIE RICHTIG GEWESEN, WAERE SIE GEFAEHRLICH. `options.php`
 *    laeuft ueber ALLE Optionen der Gruppe:
 *
 *        $value = isset($_POST[$option]) ? … : null;
 *        update_option($option, $value);
 *
 *    Zwei Formulare in einer Gruppe: wer das eine abschickt, schreibt die
 *    Option des anderen auf null. Mein Sanitizer gibt bei !is_array() den
 *    leeren Vorgabewert zurueck — die Zitate waeren weg.
 *
 *    MEIN array_key_exists-RIEGEL DECKT DAS NICHT AB. Er unterscheidet
 *    ABWESEND von LEER innerhalb des Arrays; hier fehlt das ganze Array.
 *
 *        Ein Riegel gegen ein fehlendes Feld hilft nicht gegen eine
 *        fehlende Option.
 *
 * DIE FASSUNG IST NOETIG, weil options.php sonst 'manage_options' verlangt —
 * unabhaengig davon, welche Capability die Menue-Seite nutzt (WP-Kernverhalten,
 * in kranz.php seit v0.0.52 dokumentiert). Ohne sie sieht der SysOp ein
 * Formular, das er nicht speichern kann.
 *
 * BENANNTE GRENZE: `cluster_one_kranz_save` ist die Capability des Kranz, und
 * ich nenne sie hier beim Namen. Das ist die einzige Stelle, an der diese
 * Datei ein fremdes Wort braucht — mein Abschnitt steht auf seiner Seite, und
 * wer den Kranz speichern darf, darf auch meinen Abschnitt speichern. Gaebe es
 * eine eigene, waere es ein zweites Rechtesystem fuer dieselbe Seite. Wenn die
 * Werkbank das anders sieht, ist es eine Zeile. */
add_action('admin_init', function () {
    register_setting('cluster_one_post_dolly_group', 'cluster_one_post_dolly', array(
        'type'              => 'array',
        'sanitize_callback' => 'cluster_one_post_dolly_sanitize',
        'default'           => array(),
    ));
});

add_filter('option_page_capability_cluster_one_post_dolly_group', function (): string {
    return 'cluster_one_kranz_save';
});

/* HIER HING POST DOLLY SICH AUF KRANZ' SETUP-SEITE EIN, auf Platz 65. Die
   Zeile ist am 16.09.2026 gefallen — NACHTRAG ZU PROMPT-HOLLERITH-11 §3.

       add_filter('cluster_one_kranz_abschnitte', …  $c1_a[65] = …

   WARUM SIE FAELLT UND NICHT BLEIBT, und das ist der ganze Grund: Post Dolly
   bekommt unten eine EIGENE Seite mit demselben Formular. Bliebe die Zeile
   stehen, stuende dasselbe Formular an ZWEI Orten und schriebe DIESELBE
   Option `cluster_one_post_dolly`.

       P1-12: keine zwei SCHREIBwege auf eine Wahrheit. Zwei LESEwege sind
       erlaubt, zwei Schreiber nie.

   Und es waere die stille Sorte: zwei Formulare, eines ueberschreibt das
   andere, keines meldet etwas.

   GEMESSEN, BEVOR SIE FIEL: kein Reiter, kein Link und kein Text in diesem
   Paket nennt den Abschnitt oder verweist auf „Post Dolly unter Setup".
   Es faellt also kein Weg mit ihr — ein Weg, der ins Leere fuehrt, waere
   schlimmer als keiner, weil er wie einer aussieht.

   KEIN BYTE IN kranz.php. Post Dolly hat sich selbst eingehaengt und haengt
   sich selbst aus; der Haken `cluster_one_kranz_abschnitte` bleibt
   unveraendert und traegt weiter die Abschnitte der anderen. */

/** H115 — DER RAEUMER. Er zaehlt, bevor er raeumt.
 *
 * MEIN FUND VOM 28.08. HATTE ZWEI RICHTUNGEN, und beide sind derselbe Fehler:
 *
 *     Knoten-Reset (kern.php)    fragt eine feste LISTE       -> ZU WENIG
 *     Gefahrenzone (kranz.php)   fragt einen PRAEFIX          -> ZU VIEL
 *
 * Der Praefix ist die schlechtere von beiden: er fragt weder Liste noch
 * Verzeichnis, sondern einen Namensanfang — und bekommt alles, was zufaellig
 * so heisst. Gemessen auf einem Fuenf-Light-Knoten: 21 von 21 Optionen weg,
 * vier fremde Produkte, der Boden und die Migrationsmarken darunter.
 *
 *     Wer eine Liste fragt, bekommt die Welt von gestern.
 *     Wer das Verzeichnis fragt, bekommt die von heute.   (V14 §5)
 *
 * WARUM EIN RAEUMER UND KEINE LISTE VON NAMEN. Eine Liste kann die
 * dynamischen Schluessel nicht nennen — `room_chat_<id>`, `forum_posts_<id>`.
 * Genau deshalb hat jemand zum Praefix gegriffen. Ein Raeumer kennt seine
 * eigenen, auch die berechneten.
 *
 * ER ZAEHLT ODER ER TUT, NIE BEIDES UNGEFRAGT (Kleinrocks Vorbild,
 * `cluster_one_kleinrock_knoten_loeschen`): der Mensch muss die Zahl VORHER
 * sehen, nicht nachher vermissen.
 *
 * DIE FORM IST DER VERTRAG AUS `kern.php` (1.9.13), woertlich:
 *
 *     function <name>(bool $tun): array      Rueckgabe: was => wieviel
 *
 * WAS => WIEVIEL, und das WAS ist ein Wort fuer einen Menschen. Hier standen
 * bis heute `optionen` und `zitate`. `optionen` ist das Wort der Datenbank,
 * nicht das der Sache: niemand vermisst eine Option, man vermisst seine
 * Einstellungen. Der Vertrag nennt als Beispiel `raeume` und `themen` — auch
 * das sind Dinge und keine Zeilen.
 *
 *     Eine Zahl, die vor dem Klick steht, wird gelesen. Sie muss in der
 *     Sprache dessen stehen, der klickt.
 *
 * WAS ER NICHT ANFASST, und das ist keine Ausnahme, sondern die Regel selbst:
 * alles, was ihm nicht gehoert. Die Migrationsmarken ueberleben nicht, weil
 * jemand an sie gedacht hat, sondern weil niemand sie fuer sich beansprucht.
 *
 *     Was niemand fuer sich beansprucht, wird nicht geraeumt.
 *
 * Meine Daten sind genau eine Option. Das ist kein Grund, es nicht
 * aufzuschreiben — die Zahl eins ist eine Messung wie jede andere.
 *
 * DIE FUENFZEHNTE FUNKTION DIESER DATEI — der Zahlenvermerk in
 * `cluster-one.php` aendert sich damit von 14 auf 15, und das steht im
 * Bericht. */
if (!function_exists('cluster_one_post_dolly_raeumen')) {
function cluster_one_post_dolly_raeumen(bool $c1_tun = false): array {
    /* 0.3.59 (31.10.2026, Neo): zwei Optionen statt einer – die Vorschlaege der
       Mitglieder kommen dazu, gezaehlt als 'vorschlaege'. 'einstellungen' zaehlt
       weiter nur die eine Option mit Zitaten, Ansagen, Ort und Haekchen. */
    /* 0.3.63 (31.10.2026, Neo): drei Optionen. Der Zaehler der Kennungen kommt dazu,
       weil Vorschlaege seit heute entfernt werden koennen und eine Kennung trotzdem
       nie zweimal vergeben wird. Er wird nicht gezaehlt, nur geraeumt. */
    $c1_meins = array('cluster_one_post_dolly', 'cluster_one_post_dolly_vorschlaege', 'cluster_one_post_dolly_vorschlaege_zaehler');
    $c1_zahl  = array('zitate' => 0, 'ansagen' => 0, 'vorschlaege' => 0, 'einstellungen' => 0);

    foreach ($c1_meins as $c1_name) {
        if (get_option($c1_name, null) === null) { continue; }
        if ($c1_name === 'cluster_one_post_dolly') {
            $c1_zahl['einstellungen']++;
            $c1_o = get_option($c1_name, array());
            if (is_array($c1_o) && isset($c1_o['zitate']) && is_array($c1_o['zitate'])) {
                $c1_zahl['zitate'] = count($c1_o['zitate']);
            }
            if (is_array($c1_o) && isset($c1_o['ansagen']) && is_array($c1_o['ansagen'])) {
                $c1_zahl['ansagen'] = count($c1_o['ansagen']);
            }
        } elseif ($c1_name === 'cluster_one_post_dolly_vorschlaege') {
            $c1_o = get_option($c1_name, array());
            $c1_zahl['vorschlaege'] = is_array($c1_o) ? count($c1_o) : 0;
        }
        if ($c1_tun) { delete_option($c1_name); }
    }
    /* 0.3.69: je SysOp-Konto die Liste der ausgeblendeten Vorschlaege (Benutzer-Meta), fuer alle Konten. */
    if ($c1_tun) { delete_metadata('user', 0, 'cluster_one_post_dolly_ausgeblendet', '', true); }
    return $c1_zahl;
}
}

/* DER ANTWORTER IST AUSGEZOGEN — er stand hier einen Tag lang und gehoert in
   den Lader (`cluster-one-post-dolly.php`), VERTRAG 26 / 11.09.

       Ein Organ meldet sich. Ein Produkt meldet sich AN. Das erste steht in
       der Organdatei, das zweite im Lader.

   DIESE DATEI REIST. Auf dem Monolithen tragen dann alle Organe denselben
   Wirt, und fuenf Antworter in fuenf Organdateien schreiben unter denselben
   Schluessel — wer zuletzt spricht, benennt das ganze Haus. Ich hatte den
   Fall gemessen und die Frage abgegeben; die Antwort kam aus zwei anderen
   Haeusern und ist besser als jeder Riegel, den ich haette bauen koennen:
   der Lader reist nicht.

   Diese Zeilen bleiben stehen, damit niemand den Antworter hier noch einmal
   erfindet. Ein ausgezogener Bewohner hinterlaesst ein Schild, keine leere
   Wohnung. */

/* DIE ANMELDUNG — ANGEHAENGT, NICHT ZUGEWIESEN. VOLLZUG 13 §3, 18.09.2026.
   Meine einzige Aenderung an dieser Datei seit dem Schnitt von 0.3.21.

   WAS BIS HIERHER STAND, und es steht da, damit es niemand wiedererfindet:

       $c1_r['post-dolly'] = 'cluster_one_post_dolly_raeumen';

   Ein Schluessel, ein Platz, eine Zuweisung. H218 hat dem Sammler am 05.09.
   beigebracht, einen VERWORFENEN Raeumer zu melden. Einen UEBERSCHRIEBENEN
   konnte er nie melden, und er KANN es auch nicht: wenn `apply_filters`
   zurueckkommt, ist der Verlierer schon weg.

       Ein Waechter, der prueft, ob der Eintrag GUELTIG ist, prueft nicht,
       ob der PLATZ FREI war.

   Der Fehler lag also nie im Waechter, sondern in der FORM DER UEBERGABE —
   R-SAMMLER-1: anhaengend entgegennehmen, hinterher urteilen. Dieselbe Form,
   die kasper.php seit H104 hat und derentwegen H104 ueberhaupt gefunden
   werden konnte.

   DREI FELDER, und jedes hat seinen Grund:

   'produkt'  DER SCHLUESSEL DER SACHE, nicht mehr der Schluessel des Platzes.
              Er heisst weiter 'post-dolly' — P1-11: ein Umbau, der dabei den
              Namen aendert, ist kein Umbau.

   'fn'       EINE ZEICHENKETTE, nie eine Closure (H218). Ein Raeumer, der
              einen Namen hat, laesst sich messen, benennen und in einem
              Bericht zitieren. Eine Closure nicht.

   'wirt'     EIN FELD UND NICHT DER SCHLUESSEL — NACHTRAG 28a, aus der Welt,
              die ich am 17.09. gefahren habe: der Wirt beantwortet „in
              welchem PAKET liege ich", nicht „welches ORGAN bin ich". Im
              Light traegt er; im Monolithen liegen drei Organe unter einem
              Wirt, und zwei davon verschwinden lautlos. Der Knopf fragt
              darum nicht nach dem Schluessel, sondern: welche Raeumer tragen
              MEINEN Wirt.

   UND DER RUF STEHT OHNE RIEGEL. Das ist gemessen, nicht angenommen:

       mein Lader    kasper.php :110   vor   kern.php :211
       der Monolith  kasper.php :127   vor   kern.php :128

   In beiden Welten, die es gibt, ist `cluster_one_kasper_wirt` da, bevor
   irgendwer diesen Filter anwenden kann — der Sammler wohnt in kern.php.
   NICHT GEMESSEN, weil es sie noch nirgends gibt: eine uninstall.php, die
   kern.php ohne kasper.php laedt. Das ist Schritt 2, und es steht im
   Bericht statt in einem Riegel, der heute nichts bewacht.
       Ein Riegel, der in jeder gemessenen Welt haelt, hat nichts gehalten. */
add_filter('cluster_one_raeumer', function ($c1_r) {
    if (is_array($c1_r)) {
        $c1_r[] = array(
            'produkt' => 'post-dolly',
            'fn'      => 'cluster_one_post_dolly_raeumen',
            'wirt'    => cluster_one_kasper_wirt(__FILE__),
        );
    }
    return $c1_r;
});

/** Was aus dem Formular in die Datenbank darf.
 *
 * ZEILEN, DIE IN BEIDEN SPRACHEN LEER SIND, FALLEN WEG. So loescht man ein
 * Zitat: Felder leeren, speichern. Ein eigener Loeschknopf waere ein zweiter
 * Bedienweg auf dieselbe Wirkung.
 *
 * ---------------------------------------------------------------------------
 * 18.09.2026, RUNDBRIEF 21 §6 — ZWEI REITER, ZWEI FORMULARE, EINE OPTION.
 *
 * Diese Funktion ist die Stelle, an der Christians Wunsch teuer werden kann.
 * Bis heute gab es EIN Formular, und es schickte alle drei Schluessel mit.
 * Also durfte der Sanitizer bei jedem Speichern von Null anfangen.
 *
 * AB HEUTE SCHICKT JEDER REITER NUR SEINE EIGENEN FELDER:
 *
 *     Reiter „Einstellungen"   ort · foederation
 *     Reiter „Zitate"          zitate
 *
 * Ein Sanitizer, der weiter von Null anfaengt, loescht beim Speichern des
 * einen Reiters, was auf dem anderen steht. Das ist keine Feinheit: es sind
 * die Zitate des Menschen, und sie waeren weg, ohne dass er etwas geloescht
 * hat.
 *
 *     Ein Formular, das ein Feld nicht zeigt, hat es nicht abgewaehlt.
 *
 * DIESER SATZ STAND SCHON HIER — er war die Begruendung fuer den einen
 * Sonderzweig beim Haekchen (H144, s. u.). Er gilt jetzt fuer alle drei
 * Schluessel, und damit ist die Regel EINE und nicht eine mit Ausnahmen:
 *
 *     ANWESEND    der Reiter hat den Wert bedient  -> er wird genommen
 *     ABWESEND    kein Reiter hat ihn gezeigt      -> der gespeicherte bleibt
 *
 * WARUM DAS KEIN ZWEITER SCHREIBWEG IST (P1-12): es gibt weiter genau EINEN
 * Weg in die Datenbank — diese Funktion, ueber options.php. Zwei Formulare
 * sind zwei EINGABEN auf einen Schreibweg, nicht zwei Schreibwege. Waeren es
 * zwei Optionen oder zwei Sanitizer, waere es das andere.
 *
 * UND ES IST KEIN VERSTECKTES MITSCHICKEN. Der andere Reiter traegt seine
 * Werte NICHT als verborgene Felder mit: dann truege ein Reiter, den jemand
 * eine Stunde offen liegen laesst, einen veralteten Stand — und beim
 * Speichern gewaenne das aeltere Fenster.
 *     Wer Werte mitschickt, die er nicht zeigt, schickt den Stand von
 *     vorhin. */
function cluster_one_post_dolly_sanitize($c1_in): array {
    /* DER GESPEICHERTE STAND IST DIE GRUNDLAGE, nicht die leere Vorlage.
       Was kein Reiter geschickt hat, bleibt, wie es war. */
    $c1_alt  = cluster_one_post_dolly_opts();
    $c1_rein = array(
        'zitate'      => isset($c1_alt['zitate']) && is_array($c1_alt['zitate']) ? $c1_alt['zitate'] : array(),
        'ansagen'     => isset($c1_alt['ansagen']) && is_array($c1_alt['ansagen']) ? $c1_alt['ansagen'] : array(),
        'foederation' => empty($c1_alt['foederation']) ? 0 : 1,
        'ort'         => isset($c1_alt['ort']) ? (string) $c1_alt['ort'] : 'beides',
        'dashboard'   => isset($c1_alt['dashboard']) ? (string) $c1_alt['dashboard'] : 'mitglieder',   // 0.4.7 (Omega)
    );
    $c1_rein['ort'] = in_array($c1_rein['ort'], array('backend', 'frontend', 'beides'), true)
        ? $c1_rein['ort'] : 'beides';
    $c1_rein['dashboard'] = in_array($c1_rein['dashboard'], array('mitglieder', 'alle'), true) ? $c1_rein['dashboard'] : 'mitglieder';

    if (!is_array($c1_in)) { return $c1_rein; }

    /* DIE ZITATE UND DIE ANSAGEN. Ist der Schluessel da, ist die geschickte
       Liste die ganze Wahrheit — sonst koennte man keine Zeile mehr loeschen.
       Ist er nicht da, hat der Reiter nicht gespeichert.

       20.10.2026 (Neo): beide Tabellen durch dieselbe Reinigung, und jede
       Zeile bekommt ihren Schluessel: der getippte (nur A–Z, 0–9, _ und -,
       gross geschrieben), sonst der naechste freie der Reihe (A… fuer Zitate,
       B… fuer Ansagen). Ein Schluessel, der in derselben Liste schon vergeben
       ist, bekommt einen neuen — zwei Zeilen mit einem Namen waeren zwei
       Antworten auf einen Shortcode. */
    foreach (array('zitate' => 'A', 'ansagen' => 'B') as $c1_liste => $c1_buchstabe) {
        if (!array_key_exists($c1_liste, $c1_in)) { continue; }
        $c1_rein[$c1_liste] = array();
        $c1_vergeben = array();
        if (is_array($c1_in[$c1_liste])) {
            foreach ($c1_in[$c1_liste] as $c1_z) {
                if (!is_array($c1_z)) { continue; }
                /* 27.10.2026 (Neo): jede Sprachspalte, nicht nur de/en. Welche
                   es gibt, sagt die Tabelle; hier zaehlt nur, dass der
                   Spaltenname ein Sprachcode ist und der Text sauber. Eine
                   Zeile ohne Text in irgendeiner Sprache ist geloescht. */
                $c1_texte = array();
                foreach ($c1_z as $c1_k => $c1_t) {
                    if (!cluster_one_post_dolly_ist_code($c1_k) || !is_string($c1_t)) { continue; }
                    $c1_t = sanitize_text_field($c1_t);
                    if (trim($c1_t) !== '') { $c1_texte[$c1_k] = $c1_t; }
                }
                if ($c1_texte === array()) { continue; }
                $c1_key = isset($c1_z['key']) ? strtoupper(preg_replace('~[^A-Za-z0-9_\-]~', '', (string) $c1_z['key'])) : '';
                if ($c1_key === '' || in_array($c1_key, $c1_vergeben, true)) {
                    $c1_n = 1;
                    while (in_array($c1_buchstabe . $c1_n, $c1_vergeben, true)) { $c1_n++; }
                    $c1_key = $c1_buchstabe . $c1_n;
                }
                $c1_vergeben[] = $c1_key;
                $c1_rein[$c1_liste][] = array_merge(array('key' => $c1_key), $c1_texte);
            }
        }
    }

    /* VERBORGEN HEISST NICHT GELOESCHT (H144). Ist der Abschnitt verborgen,
       kommt das Feld ueberhaupt nicht im Formular vor — auch das verborgene
       Feld mit der Null nicht. Ohne diesen Zweig raeumte das naechste Speichern
       das Haekchen weg, ohne dass jemand es angefasst haette, und der SysOp
       faende es beim naechsten 'ja' leer vor.

       Der Unterschied liegt zwischen ABWESEND und LEER: ein sichtbarer
       Abschnitt liefert immer einen Wert (0 oder 1), weil das verborgene Feld
       mitgeschickt wird. Fehlt der Schluessel ganz, hat niemand ihn bedient.

       SEIT DEN ZWEI REITERN HAT DIESER ZWEIG ZWEI GRUENDE, und beide sind
       derselbe: das Haekchen kann verborgen sein (H144), oder der andere
       Reiter laeuft (RUNDBRIEF 21 §6). Der Sanitizer unterscheidet sie nicht
       und muss es nicht — in beiden Faellen hat niemand das Feld bedient. */
    if (array_key_exists('foederation', $c1_in)) {
        $c1_rein['foederation'] = empty($c1_in['foederation']) ? 0 : 1;
    }

    if (array_key_exists('ort', $c1_in)) {
        $c1_ort = (string) $c1_in['ort'];
        $c1_rein['ort'] = in_array($c1_ort, array('backend', 'frontend', 'beides'), true)
            ? $c1_ort : 'beides';
    }
    if (array_key_exists('dashboard', $c1_in)) {   // 0.4.7 (Omega): wie 'ort' — fehlt der Schluessel, hat niemand das Feld bedient
        $c1_wer = (string) $c1_in['dashboard'];
        $c1_rein['dashboard'] = in_array($c1_wer, array('mitglieder', 'alle'), true) ? $c1_wer : 'mitglieder';
    }

    return $c1_rein;
}

/** Der Kranz-Abschnitt.
 *
 * EIGENER RIEGEL, obwohl die ganze Datei schon einen hat: Ein Riegel schuetzt
 * eine Datei gegen sich selbst, nicht gegen ihre Vergangenheit. Liegt auf einem
 * gemischten Knoten eine aeltere Fassung dieser Datei, deren Datei-Riegel auf
 * einen anderen Namen hoert, waere diese Funktion sonst doppelt deklariert.
 *
 * $opts ist die Option DES KRANZ und wird hier nicht gebraucht — Post Dolly
 * liest seine eigene. Der Parameter steht da, weil der Sammler ihn uebergibt. */
/** H216a — der eine Satz, den beide verborgenen Zweige teilen.
 *
 * EINE FUNKTION UND NICHT ZWEIMAL DERSELBE ABSATZ: Der Satz ist in beiden
 * Zweigen wortgleich, und zwei Stellen mit einem Wortlaut sind zwei Stellen,
 * an die jemand denken muss. Die vierzehnte Funktion dieser Datei — der
 * Zahlenvermerk in `cluster-one.php` aendert sich damit von 13 auf 14, und das
 * steht im Bericht.
 *
 * Sie schweigt, wenn nichts gespeichert ist. Ein Hinweis auf einen Wert, den
 * es nicht gibt, waere schlimmer als gar keiner. */
if (!function_exists('cluster_one_post_dolly_gesetzt_aber_verborgen')) {
function cluster_one_post_dolly_gesetzt_aber_verborgen(array $c1_o): void {
    if (empty($c1_o['foederation'])) { return; }
    echo '<p class="description" style="max-width:720px;"><strong>';
    echo esc_html__('You have set this node to declare itself publicly. That setting is stored and unchanged — it is simply not in effect right now, and the line does not appear.', 'cluster-one-post-dolly');
    echo '</strong></p>';
}
}

if (!function_exists('cluster_one_settings_seite_post_dolly')) {
function cluster_one_settings_seite_post_dolly(array $opts = array()): void {
    unset($opts);
    $c1_o   = cluster_one_post_dolly_opts();
    $c1_ort = cluster_one_post_dolly_ort();
    $c1_darf = cluster_one_post_dolly_darf_speichern(); // 0.3.59
    ?>
        <h2 style="margin-top:1em;"><?php esc_html_e('Settings', 'cluster-one-post-dolly'); ?></h2>

        <p style="max-width:720px;">
            <?php if ($c1_darf) : ?>
            <?php esc_html_e('Quotes you write here appear where you put them: in the front end via the shortcode, on your dashboard, or both. Nothing appears anywhere else.', 'cluster-one-post-dolly'); ?>
            <?php else : ?>
            <?php esc_html_e('Where the quotes appear: in the front end via the shortcode, on the dashboard, or both. Nothing appears anywhere else. These settings are the SysOp’s; you see what is set.', 'cluster-one-post-dolly'); ?>
            <?php endif; ?>
        </p>

        <?php
        /* 31.10.2026 (Neo), 0.3.59, RUNDE 36 — DIE LESENDE ANSICHT. Ein Mitglied
           bekommt kein Formular, das options.php ihm verweigern wuerde („Sonst
           sieht jemand ein Formular, das er nicht abschicken darf", :1985),
           sondern die Werte als Text: den Ort mit demselben Wortlaut wie die
           Schalter, das Bekenntnis als Satz. Die drei Lagen des Haekchens (H144:
           ja / nein / fehlt) bleiben auch hier drei: bei 'fehlt' steht nichts,
           weil es niemanden gibt, der ueber Nachbarn Auskunft geben koennte.
           Der Berg darunter ist fuer alle da, er war nie Teil des Formulars. */
        if (!$c1_darf) :
            $c1_gepaart = cluster_one_auskunft('gepaart');
            $c1_orte = cluster_one_post_dolly_orte();
        ?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e('Where', 'cluster-one-post-dolly'); ?></th>
                <td><?php echo esc_html(isset($c1_orte[$c1_ort]) ? $c1_orte[$c1_ort] : $c1_ort); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Dashboard box visible to', 'cluster-one-post-dolly'); ?></th>
                <td><?php $c1_dw = cluster_one_post_dolly_dashboard_woerter(); $c1_dk = cluster_one_post_dolly_dashboard_wer(); echo esc_html(isset($c1_dw[$c1_dk]) ? $c1_dw[$c1_dk] : $c1_dk); // 0.4.7 (Omega) ?></td>
            </tr>
            <?php if ($c1_gepaart === 'ja') : ?>
            <tr>
                <th scope="row"><?php esc_html_e('Federation', 'cluster-one-post-dolly'); ?></th>
                <td><?php echo esc_html(!empty($c1_o['foederation'])
                    ? __('This node declares itself publicly.', 'cluster-one-post-dolly')
                    : __('This node does not declare itself publicly: it belongs to the alliance, connected but discreet.', 'cluster-one-post-dolly')); ?></td>
            </tr>
            <?php elseif ($c1_gepaart === 'nein') : ?>
            <tr>
                <th scope="row"><?php esc_html_e('Federation', 'cluster-one-post-dolly'); ?></th>
                <td><p class="description" style="max-width:720px;"><?php esc_html_e('Declaring this node publicly needs one active connection to another node. There is none yet, so the setting is not shown. Nothing you set earlier has been lost.', 'cluster-one-post-dolly'); ?></p></td>
            </tr>
            <?php endif; ?>
        </table>
        <?php else : ?>
        <?php
        /* H217 — HIER BEGINNT MEIN EIGENES FORMULAR, UND ES UMSCHLIESST JEDES
           MEINER FELDER.

           Bis heute hatte dieser Abschnitt gar keins. Er wurde ueber
           `cluster_one_kranz_abschnitte` angemeldet und deshalb NACH dem
           schliessenden </form> des Kranz gerendert — elf Eingabefelder ohne
           Formular, ohne Knopf, ohne Weg zum Server. Christian hat das
           Haekchen viermal gemeldet.

               Ein Feld ausserhalb eines Formulars sieht aus wie eine
               Einstellung und ist eine Zeichnung.

           GEMESSEN, NICHT GESCHLOSSEN: Zeuge W37 liest das GERENDERTE HTML und
           nennt jedes Feld mit Namen und Byte-Position. Vorher elf DRAUSSEN,
           nachher null. Der Lauf liegt beim Bericht.

           VOLLSTAENDIG ODER GAR NICHT: Zwischen diesem <form> und seinem
           </form> liegt jedes Feld dieses Abschnitts. Der Berg darunter steht
           ausserhalb — er hat keine Felder, und ein Bild gehoert nicht in ein
           Formular. */
        ?>
        <form method="post" action="<?php echo esc_url(admin_url('options.php')); ?>">
            <?php settings_fields('cluster_one_post_dolly_group'); ?>

        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e('Where', 'cluster-one-post-dolly'); ?></th>
                <td>
                    <?php /* 0.3.59: die drei Woerter wohnen jetzt in cluster_one_post_dolly_orte(), weil die lesende Ansicht dieselben braucht */ ?>
                    <?php foreach (cluster_one_post_dolly_orte() as $c1_wert => $c1_text) : ?>
                        <p style="margin:.2em 0;">
                            <label>
                                <input type="radio" name="cluster_one_post_dolly[ort]"
                                       value="<?php echo esc_attr($c1_wert); ?>"
                                       <?php checked($c1_ort, $c1_wert); ?>>
                                <?php echo esc_html($c1_text); ?>
                            </label>
                        </p>
                    <?php endforeach; ?>
                    <p class="description">
                        <?php
                        echo wp_kses_post(sprintf(
                            /* translators: %s = the shortcode, shown in code tags. */
                            __('The front end has exactly one door: the shortcode %s. There is no automatic footer hook — a plugin that places itself is a plugin nobody placed.', 'cluster-one-post-dolly'),
                            '<code>[cluster_one_dolly]</code>'
                        ));
                        ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Dashboard box visible to', 'cluster-one-post-dolly'); ?></th>
                <td>
                    <?php /* 0.4.7 (03.11.2026, Neo), Runde Omega — siehe cluster_one_post_dolly_dashboard_wer(). */ ?>
                    <?php foreach (cluster_one_post_dolly_dashboard_woerter() as $c1_wert => $c1_text) : ?>
                        <p style="margin:.2em 0;">
                            <label>
                                <input type="radio" name="cluster_one_post_dolly[dashboard]"
                                       value="<?php echo esc_attr($c1_wert); ?>"
                                       <?php checked(cluster_one_post_dolly_dashboard_wer(), $c1_wert); ?>>
                                <?php echo esc_html($c1_text); ?>
                            </label>
                        </p>
                    <?php endforeach; ?>
                    <p class="description"><?php esc_html_e('Until 0.4.6 everyone who could see the dashboard saw the box. The rule of this house: whoever is not on the node’s list sees nothing of Cluster One — not even a quote. The front end is unaffected: the shortcode shows wherever you place it.', 'cluster-one-post-dolly'); ?></p>
                </td>
            </tr>

            <?php
            /* H144 — DREI WERTE, DREI VERSCHIEDENE OBERFLAECHEN.
               Gefragt wird HIER, beim Rendern.

               'ja'     der Abschnitt steht da
               'nein'   verborgen, dazu EIN Satz: es gibt ein Mesh, aber keine
                        aktive Kante. Das ist gewusst und darf gesagt werden.
               'fehlt'  verborgen, und KEIN Satz. Es gibt niemanden, der die
                        Frage beantworten koennte; jeder Satz ueber Nachbarn
                        waere hier eine Behauptung.

               Warum die beiden unteren Faelle nicht dasselbe zeigen: Wenn drei
               Werte am Ende zwei Oberflaechen ergeben, war der dritte Wert
               umsonst. 'fehlt' und 'nein' sehen fuer einen Menschen gleich aus
               und sind es nicht — genau deshalb gibt es sie getrennt.

               Und das Haekchen wird VERBORGEN, NIE GELOESCHT: der gespeicherte
               Wert bleibt stehen. Was ihn davor schuetzt, steht im Sanitizer. */
            $c1_gepaart = cluster_one_auskunft('gepaart');
            ?>
            <?php if ($c1_gepaart === 'ja') : ?>
            <tr>
                <th scope="row"><?php esc_html_e('Federation', 'cluster-one-post-dolly'); ?></th>
                <td>
                    <input type="hidden" name="cluster_one_post_dolly[foederation]" value="0">
                    <label>
                        <input type="checkbox" name="cluster_one_post_dolly[foederation]" value="1"
                               <?php checked(!empty($c1_o['foederation'])); ?>>
                        <?php esc_html_e('This node declares itself publicly.', 'cluster-one-post-dolly'); ?>
                    </label>
                    <p class="description" style="max-width:720px;">
                        <?php esc_html_e('Unticked, this node belongs to the alliance: connected, but discreet — what happens here stays here. Ticked, it joins the federation, the subset of nodes that say so in the open. A line then appears next to your quotes, and other nodes may point at this one publicly.', 'cluster-one-post-dolly'); ?>
                    </p>
                    <p class="description" style="max-width:720px;">
                        <?php esc_html_e('Two things to know before you tick it. It says what you want, not what a visitor sees — if you never place the shortcode, nobody outside will read the line. And you can untick it again, but what was public was public: search engines and other people\'s lists do not forget on request.', 'cluster-one-post-dolly'); ?>
                    </p>
                </td>
            </tr>
            <?php elseif ($c1_gepaart === 'nein') : ?>
            <tr>
                <th scope="row"><?php esc_html_e('Federation', 'cluster-one-post-dolly'); ?></th>
                <td>
                    <p class="description" style="max-width:720px;">
                        <?php esc_html_e('Declaring this node publicly needs one active connection to another node. There is none yet, so the setting is not shown. Nothing you set earlier has been lost.', 'cluster-one-post-dolly'); ?>
                    </p>
                    <?php cluster_one_post_dolly_gesetzt_aber_verborgen($c1_o); ?>
                </td>
            </tr>
            <?php else : ?>
            <?php
            /* H216a — VERBORGEN, WAEHREND EIN WERT GESPEICHERT IST, MUSS SICH
               MELDEN. Bis heute war der 'fehlt'-Zweig ganz still: der Mensch,
               der gestern gespeichert hat, findet an der Stelle seines
               Haekchens nichts vor.

                   Verborgen und nicht gespeichert sehen fuer einen Menschen
                   gleich aus.

               Nur wenn wirklich etwas gespeichert IST, erscheint diese Zeile.
               Ist nichts gespeichert, bleibt der Zweig still — sonst waere aus
               dem Schweigen eine zweite Meldung geworden.

               UND SIE SAGT NICHTS UEBER NACHBARN. Bei 'fehlt' gibt es
               niemanden, der ueber Kanten Auskunft geben koennte; jeder Satz
               darueber waere eine Behauptung. Dieser Satz spricht
               ausschliesslich ueber den gespeicherten Wert, und deshalb darf
               er in beiden Zweigen stehen. */
            ?>
            <?php if (!empty($c1_o['foederation'])) : ?>
            <tr>
                <th scope="row"><?php esc_html_e('Federation', 'cluster-one-post-dolly'); ?></th>
                <td><?php cluster_one_post_dolly_gesetzt_aber_verborgen($c1_o); ?></td>
            </tr>
            <?php endif; ?>
            <?php endif; ?>
        </table>

            <?php
            /* Der eigene Knopf. Er gehoert hierher und nicht ans Seitenende:
               ein Knopf, der weiter unten steht als das Formular, das er
               absenden soll, gehoert einem anderen Formular.

               DER WORTLAUT AENDERT SICH MIT DEM REITER, und das ist kein
               Schmuck: „Save quotes" unter einem Formular ohne Zitate waere
               eine Zusage, die es nicht haelt. */
            submit_button(__('Save settings', 'cluster-one-post-dolly'));
            ?>
        </form>
        <?php endif; // 0.3.59: Ende des Formular-Zweigs (SysOp) ?>

        <h3 style="margin-top:1.5em;"><?php esc_html_e('The climb', 'cluster-one-post-dolly'); ?></h3>
        <p style="max-width:720px;">
            <?php
            /* ZWEI SAETZE, ZWEI ZEICHENKETTEN, UND DAS IST DIE ENTSCHEIDUNG
               HIER (02.09.2026). Bis heute standen sie in EINER msgid:

                 „The picture counts … It is a state, not a recommendation,
                  and nothing here is a condition: …"

               Der erste Satz BESCHREIBT, was das Bild zaehlt — er wird sich
               wieder aendern, sobald H112 einen Vertrag hat. Der zweite ist
               eine ZUSAGE (Christian, 26.08.: „egal wieviel ausser Post Dolly
               noch im Knoten aktiviert sind"), und Christian kann sie mit
               einem Wort streichen, ohne dass der erste sich bewegt.

                   Eine Zusage verschwindet nicht, weil man den Satz daneben
                   neu schreibt.

               In einer msgid haetten sie eine gemeinsame Lebensdauer, die
               keiner von beiden hat: jede Aenderung am einen zwaenge den
               anderen durch den Katalog. Getrennt aendert sich, was sich
               aendert. Der Werkbank-Auftrag rechnete mit zwei neuen msgids;
               es sind drei, und das steht im Bericht. */
            ?>
            <?php esc_html_e('How many plugins of this house are running on this node. If the node declares itself publicly and is connected to another, that is shown below.', 'cluster-one-post-dolly'); ?>
            <?php esc_html_e('It is a state, not a recommendation, and nothing here is a condition: every plugin solves its own problem alone.', 'cluster-one-post-dolly'); ?>
        </p>
        <p style="color:currentColor;">
            <?php
            echo cluster_one_post_dolly_berg(cluster_one_post_dolly_lager(), cluster_one_post_dolly_foederation(), cluster_one_post_dolly_turing_da()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- selbst erzeugtes SVG, jeder eingesetzte Wert ist int oder escaped
            ?>
        </p>
    <?php
}
}

/** Der Reiter „Zitate" — die Tabelle, und nur sie.
 *
 * CHRISTIANS WUNSCH, 18.09.2026, RUNDBRIEF 21 §6 POST DOLLY 3. Es ist der
 * erste Wunsch in diesem Projekt, der aus dem GEBRAUCH kommt und nicht aus
 * einer Messung — und deshalb ist die Begruendung hier keine Messung, sondern
 * seine: die Tabelle waechst, die zwei Einstellungen darueber nicht.
 *
 *     Eine Liste, die waechst, und zwei Schalter, die bleiben, gehoeren
 *     nicht auf eine Seite. Der eine schiebt den anderen nach unten.
 *
 * EIGENES FORMULAR, DIESELBE OPTION. Der Sanitizer nimmt, was ankommt, und
 * laesst stehen, was nicht ankommt (s. dort). Was hier NICHT steht und
 * absichtlich nicht: verborgene Felder mit 'ort' und 'foederation'. */
if (!function_exists('cluster_one_post_dolly_zitate_teil')) {
/* 0.4.8 (13.11.2026, Neo) — WIE VIELE LEERE ZEILEN. Bis 0.4.7 haengte die Tabelle fest drei
 * leere Zeilen an: drei fuellen, speichern, drei neue. Nemesus (13.11.): „Koennen wir das
 * nicht dynamisch machen? Felder in den beiden Tabellen auf Wunsch hinzufuegen?"
 *
 * Jetzt sagt die Adresse, wie viele: c1_leer=3|6|12|24, Vorgabe 3, Grenze
 * CLUSTER_ONE_POST_DOLLY_LEER_MAX. Die Zahl wird nur GELESEN und schaltet die Ansicht,
 * geschrieben wird weiter allein ueber options.php mit Nonce — darum ohne eigene Nonce,
 * wie c1_zeigen (0.3.63). Kein JavaScript, kein neues Skript: ein Link, eine Zahl. Die
 * Wahl gilt fuer beide Tabellen und fuer diesen einen Aufruf; nach dem Speichern kommt
 * die Seite ohne den Parameter zurueck und zeigt wieder drei. Der Satz von 20.10. gilt
 * weiter: zwanzig leere Zeilen sehen aus wie zwanzig verlorene Zitate — deshalb ist es
 * eine Wahl und keine neue Vorgabe, und die Zeile sagt dazu, dass Leeres nicht
 * gespeichert wird (der Sanitizer laesst eine Zeile ohne Text ohnehin fallen). */
if (!defined('CLUSTER_ONE_POST_DOLLY_LEER_MAX')) {
    define('CLUSTER_ONE_POST_DOLLY_LEER_MAX', 30);
}

/** Wie viele leere Zeilen jede Tabelle unten anhaengt (0.4.8). */
function cluster_one_post_dolly_leere_zeilen(): int {
    $c1_n = isset($_GET['c1_leer']) ? absint(wp_unslash($_GET['c1_leer'])) : 3; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nur gelesen, schaltet die Ansicht; geschrieben wird ueber options.php mit Nonce
    if ($c1_n < 1) { $c1_n = 3; }
    return min($c1_n, (int) CLUSTER_ONE_POST_DOLLY_LEER_MAX);
}

/** Die Zeile ueber den Tabellen: 3 · 6 · 12 · 24, die gewaehlte Zahl fett, die anderen als
 *  Verweis auf diese Seite mit c1_leer (0.4.8). Nur wer speichern darf, sieht sie — die
 *  lesende Ansicht hat keine leeren Zeilen. */
function cluster_one_post_dolly_leere_wahl(): void {
    $c1_jetzt = cluster_one_post_dolly_leere_zeilen();
    $c1_basis = admin_url('admin.php?page=cluster_one_post_dolly_tabelle');
    $c1_teile = array();
    foreach (array(3, 6, 12, 24) as $c1_n) {
        $c1_teile[] = ($c1_n === $c1_jetzt)
            ? '<strong>' . (int) $c1_n . '</strong>'
            : '<a href="' . esc_url(add_query_arg('c1_leer', (int) $c1_n, $c1_basis)) . '">' . (int) $c1_n . '</a>';
    }
    echo '<p class="description" style="max-width:720px;">'
        . esc_html__('Empty lines under each table:', 'cluster-one-post-dolly') . ' '
        . implode(' · ', $c1_teile) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- eigenes Markup: Zahlen sind int, die Adresse ist esc_url
        . ' — ' . esc_html__('the page reloads, so choose before you type. Lines left empty are not saved.', 'cluster-one-post-dolly')
        . '</p>';
}

/** Eine der zwei Tabellen: Schluessel, dann eine Spalte je Sprache des
 *  Knotens (cluster_one_post_dolly_sprachen), plus drei leere Zeilen zum
 *  Nachtragen. Nicht mehr: ein Formular, das zwanzig leere Zeilen zeigt, sieht
 *  aus wie zwanzig verlorene Zitate. (20.10.2026, Neo)
 *  27.10.2026 (Neo): bis dahin fest „Schluessel · Deutsch · Englisch".
 *  0.4.8 (13.11.2026, Neo): die drei leeren Zeilen sind die Vorgabe geblieben, aber keine
 *  Grenze mehr — s. cluster_one_post_dolly_leere_zeilen() direkt darueber. */
function cluster_one_post_dolly_tabelle(string $c1_liste, array $c1_zeilen, bool $c1_lesen = false): void {
    /* 0.3.59 (31.10.2026, Neo): $c1_lesen — dieselbe Tabelle als Text, fuer Mitglieder
       ohne Speicherrecht. Keine Felder, keine leeren Zeilen zum Nachtragen. */
    $c1_sprachen = cluster_one_post_dolly_sprachen();
    /* 0.4.8 (13.11.2026, Neo): bis 0.4.7 stand hier fest
           if (!$c1_lesen) { for ($c1_i = 0; $c1_i < 3; $c1_i++) { $c1_zeilen[] = array('key' => ''); } }
       Jetzt sagt cluster_one_post_dolly_leere_zeilen(), wie viele; 3 bleibt die Vorgabe. */
    if (!$c1_lesen) {
        $c1_leer = cluster_one_post_dolly_leere_zeilen();
        for ($c1_i = 0; $c1_i < $c1_leer; $c1_i++) { $c1_zeilen[] = array('key' => ''); }
    }
    ?>
        <table class="widefat striped" style="max-width:960px;">
            <thead>
                <tr>
                    <th style="width:8em;"><?php esc_html_e('Key', 'cluster-one-post-dolly'); ?></th>
                    <?php foreach ($c1_sprachen as $c1_s) : ?>
                        <th><?php echo esc_html(cluster_one_post_dolly_sprachname($c1_s)); ?> <code><?php echo esc_html($c1_s); ?></code></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($c1_zeilen as $c1_i => $c1_z) : ?>
                    <?php if ($c1_lesen) : ?>
                    <tr>
                        <td><code><?php echo esc_html((string) ($c1_z['key'] ?? '')); ?></code></td>
                        <?php foreach ($c1_sprachen as $c1_s) : ?>
                        <td><?php echo esc_html((string) ($c1_z[$c1_s] ?? '')); ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php continue; endif; ?>
                    <tr>
                        <td>
                            <input type="text" style="width:100%;font-family:monospace;"
                                   name="cluster_one_post_dolly[<?php echo esc_attr($c1_liste); ?>][<?php echo (int) $c1_i; ?>][key]"
                                   value="<?php echo esc_attr($c1_z['key'] ?? ''); ?>">
                        </td>
                        <?php foreach ($c1_sprachen as $c1_s) : ?>
                        <td>
                            <input type="text" style="width:100%;"
                                   name="cluster_one_post_dolly[<?php echo esc_attr($c1_liste); ?>][<?php echo (int) $c1_i; ?>][<?php echo esc_attr($c1_s); ?>]"
                                   value="<?php echo esc_attr((string) ($c1_z[$c1_s] ?? '')); ?>">
                        </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php
}

function cluster_one_post_dolly_zitate_teil(): void {
    $c1_o = cluster_one_post_dolly_opts();
    $c1_z = isset($c1_o['zitate'])  && is_array($c1_o['zitate'])  ? $c1_o['zitate']  : array();
    $c1_a = isset($c1_o['ansagen']) && is_array($c1_o['ansagen']) ? $c1_o['ansagen'] : array();
    /* Die Vorgabe-Zeile erscheint im Formular, solange nichts gespeichert ist:
       wer sie sieht, kann sie ueberschreiben, statt sie nur im Frontend zu
       treffen. */
    if (!$c1_z) { $c1_z = cluster_one_post_dolly_zitate(); }
    if (!$c1_a) { $c1_a = cluster_one_post_dolly_ansagen(); }
    /* 31.10.2026 (Neo), 0.3.59, RUNDE 36: wer speichern darf, bekommt das Formular
       wie bisher; alle anderen Mitglieder dieselben zwei Tabellen als Text, und
       darunter fuer alle den Abschnitt „Vorschlaege". Das Formular umschliesst
       weiter jedes seiner Felder (H217) und nur die; die Vorschlaege haben eigene
       Formulare mit eigenem Schreibweg und stehen ausserhalb. */
    $c1_darf = cluster_one_post_dolly_darf_speichern();
    ?>
        <h2 style="margin-top:1em;"><?php esc_html_e('The quotes', 'cluster-one-post-dolly'); ?></h2>
        <p style="max-width:720px;">
            <?php if ($c1_darf) : ?>
            <?php esc_html_e('One line per quote, one column per language of this node: the site language, every installed language pack, and English. A visitor whose language has no text gets the site language, then English. Empty all fields of a line to remove it.', 'cluster-one-post-dolly'); ?>
            <?php else : ?>
            <?php esc_html_e('One line per quote, one column per language of this node: the site language, every installed language pack, and English. A visitor whose language has no text gets the site language, then English. The SysOp writes these tables; a quote you would like to add goes in as a proposal below.', 'cluster-one-post-dolly'); ?>
            <?php endif; ?>
        </p>

        <?php if ($c1_darf) : ?>
        <?php cluster_one_post_dolly_leere_wahl(); // 0.4.8: 3 · 6 · 12 · 24 ?>
        <form method="post" action="<?php echo esc_url(admin_url('options.php')); ?>">
            <?php settings_fields('cluster_one_post_dolly_group'); ?>
        <?php endif; ?>
            <?php cluster_one_post_dolly_tabelle('zitate', $c1_z, !$c1_darf); ?>

        <h2 style="margin-top:1.6em;"><?php esc_html_e('The federation lines', 'cluster-one-post-dolly'); ?></h2>
        <p style="max-width:720px;">
            <?php esc_html_e('Join the quotes in one pool when this node declares itself publicly (Settings): each page view shows one line from all of them together, so a federation line appears instead of a quote, never next to one. Same form as the quotes.', 'cluster-one-post-dolly'); ?>
        </p>
            <?php cluster_one_post_dolly_tabelle('ansagen', $c1_a, !$c1_darf); ?>

        <p class="description" style="max-width:760px;margin-top:1em;">
            <?php echo wp_kses_post(sprintf(
                /* translators: 1: shortcode without attributes, 2: shortcode with key, 3: shortcode with key and language */
                __('The key names a line for the shortcode. %1$s shows one random line from the pool (quotes, plus federation lines if this node declares itself); %2$s shows exactly that line; %3$s forces the language. A key left empty is filled in when you save (A1, A2 … for quotes, B1, B2 … for federation lines) and then stays, even if lines above it are removed.', 'cluster-one-post-dolly'),
                '<code>[cluster_one_dolly]</code>', '<code>[cluster_one_dolly schluessel="A1"]</code>', '<code>[cluster_one_dolly schluessel="B1" sprache="en"]</code>')); ?>
        </p>

        <?php if ($c1_darf) : ?>
            <?php submit_button(__('Save', 'cluster-one-post-dolly')); ?>
        </form>
        <?php endif; ?>
    <?php
    cluster_one_post_dolly_vorschlaege_teil(); // 0.3.59: ausserhalb des Formulars, mit Absicht
}
}

/* ===========================================================================
 * RUNDE 36 (31.10.2026, Neo), 0.3.59 — SEHEN DUERFEN ALLE, TUN NUR MIT RECHT.
 * ===========================================================================
 *
 * Nemesus, 30.10.: jedes Mitglied sieht jedes Plugin; Tun braucht Recht. Bis
 * 0.3.58 verlangten Register, Anmeldung und beide Seiten 'cluster_one_kranz_save',
 * also das eine Konto – kein Mitglied sah Post Dolly. Jetzt:
 *
 *   SEHEN     jedes Mitglied (cluster_one_access): beide Reiter, die Tabellen
 *             als Text, die Einstellungen als Satz, der Berg.
 *   SPEICHERN der Tabellen und Einstellungen: unveraendert 'cluster_one_kranz_save'
 *             (option_page_capability oben), gefragt beim Zeichnen durch
 *             cluster_one_post_dolly_darf_speichern().
 *   VORSCHLAGEN  jedes Mitglied, das keine KI-Kennung ist. Ein Vorschlag ist ein
 *             Zitat im Wartestand und wohnt deshalb unter den Zitaten, nicht auf
 *             einem dritten Reiter (Nemesus' Entscheidung 31.10.). Nur Zitate
 *             (A…), keine Ansagen: die Ansage ist das Bekenntnis des Knotens.
 *   ENTSCHEIDEN  Annehmen und Ablehnen: wer speichern darf, also der SysOp.
 *             Zurueckziehen: wer vorgeschlagen hat, solange es offen ist.
 *
 * NICHTS VERSCHWINDET. Ein Vorschlag hat vier Staende – offen, angenommen,
 * abgelehnt, zurueckgezogen – und bleibt in jedem davon in der Liste stehen,
 * mit wem, wann, und beim Ablehnen dem freiwilligen Grund. Der Vorschlag ist
 * Protokoll, kein Spiegel: aendert der SysOp ein angenommenes Zitat spaeter in
 * der Tabelle, behaelt der Vorschlag seinen Wortlaut und seinen Schluessel.
 *
 * EIN SCHREIBWEG JE OPTION (P1-12):
 *   cluster_one_post_dolly_vorschlaege   nur cluster_one_post_dolly_vorschlaege_schreiben(),
 *                                        gerufen allein vom Handler unten.
 *   cluster_one_post_dolly               weiter nur durch cluster_one_post_dolly_sanitize():
 *                                        das Annehmen ruft update_option(), und WordPress
 *                                        legt den bei register_setting angemeldeten
 *                                        Sanitizer selbst darueber (sanitize_option_…).
 *                                        Der Schluessel der neuen Zeile wird dort
 *                                        vergeben, nicht hier – A-Reihe, naechster freier.
 *   Gemessen wird nach dem Schreiben: steht eine Zeile mehr in der Tabelle? Wenn
 *   nicht, bleibt der Vorschlag offen und die Seite sagt es (fail closed, laut).
 *
 * BENANNTE GRENZE: hoechstens CLUSTER_ONE_POST_DOLLY_OFFEN_JE_PERSON offene je
 * Mensch. Der naechste wird abgewiesen und die Zahl steht am Formular, bevor
 * jemand tippt.
 *
 * JEDER KNOPF HAT SEIN EIGENES SCHLOSS: die Nonce traegt das Tun und bei
 * Entscheidungen die Kennung des Vorschlags. Ein Formular, das „annehmen" sagt,
 * kann nicht als „ablehnen" ankommen.
 *
 * KEIN CHRONIK-WORT, wie bei Crockford 0.5.33: die Woerter der Chronik stehen im
 * Boden ohne Filter. Der Stand steht hier je Vorschlag, fuer alle sichtbar.
 *   (ueberholt seit 0.3.62: Boden 1.19.5 hat den Filter, die Woerter stehen im Lader)
 *
 * 0.3.63 (31.10.2026, Neo) — ENTFERNEN. Bis hier galt „nichts wird geloescht":
 * jeder Vorschlag blieb mit seinem Stand in der Liste. Nemesus hat es sich
 * anders ueberlegt (31.10.): „Es soll noch die Moeglichkeit geben, die
 * Vorschlaege zu entfernen. Sowohl beim WP-User als auch beim SysOp. Und
 * natuerlich wird der Loeschvorgang in den Chroniken vermerkt. Wegen Ordnung
 * und Uebersichtlichkeit." Ich hatte das Einklappen vorgezogen und es einmal
 * gesagt; gebaut ist seine Entscheidung:
 *   - entfernt wird nur, was entschieden ist (angenommen, abgelehnt,
 *     zurueckgezogen). Ein offener Vorschlag ist eine Bitte, die noch auf
 *     Antwort wartet; die wird erst beantwortet oder zurueckgezogen.
 *   - das Mitglied entfernt seine eigenen, der SysOp jeden.
 *   - ein angenommener laesst sein Zitat A… in der Tabelle stehen; entfernt
 *     wird der Vorschlag, nicht das Zitat.
 *   - die Chronik bekommt quote_proposal_removed mit der Kennung; das ist die
 *     Spur, die bleibt. Nichts verschwindet still.
 *   - KENNUNGEN WERDEN WEITER NIE WIEDERVERWENDET: bis 0.3.62 hiess die
 *     naechste freie „erste Luecke in der Liste", und ohne Loeschen gab es keine
 *     Luecke. Jetzt zaehlt die Option cluster_one_post_dolly_vorschlaege_zaehler
 *     die hoechste je vergebene Nummer; beim Vorschlagen gewinnt das Groessere
 *     aus Zaehler und Liste (Knoten von vor 0.3.63 haben keinen Zaehler).
 *   - Die Doppelten-Pruefung (0.3.60) kennt einen entfernten Vorschlag nicht
 *     mehr; derselbe Text kann danach wieder eingereicht werden. Das hat
 *     Nemesus am 31.10. ausdruecklich in Kauf genommen. Ein angenommenes Zitat
 *     steht weiter in der Tabelle und wird dort weiter gefunden.
 * ========================================================================= */
if (!defined('CLUSTER_ONE_POST_DOLLY_OFFEN_JE_PERSON')) {
    define('CLUSTER_ONE_POST_DOLLY_OFFEN_JE_PERSON', 5);
}

/** Darf dieser Mensch Tabellen und Einstellungen speichern? Dasselbe Wort, das
 *  options.php verlangt (option_page_capability_cluster_one_post_dolly_group). */
function cluster_one_post_dolly_darf_speichern(): bool {
    return current_user_can('cluster_one_kranz_save');
}

/** Die drei Orte mit ihrem Wortlaut – fuer die Schalter und fuer die lesende Ansicht. */
function cluster_one_post_dolly_orte(): array {
    return array(
        'backend'  => __('Back end only — a box on your dashboard', 'cluster-one-post-dolly'),
        'frontend' => __('Front end only — wherever you place the shortcode', 'cluster-one-post-dolly'),
        'beides'   => __('Both', 'cluster-one-post-dolly'),
    );
}

/** 0.4.7 (Omega): die zwei Leserkreise des Dashboard-Feldes mit ihrem Wortlaut – fuer die Schalter und fuer die lesende Ansicht. */
function cluster_one_post_dolly_dashboard_woerter(): array {
    return array(
        'mitglieder' => __('Members of the node only — whoever is on the node’s list', 'cluster-one-post-dolly'),
        'alle'       => __('Everyone who can see the dashboard', 'cluster-one-post-dolly'),
    );
}

/** Ist dieses Konto eine KI-Kennung? Eine KI schlaegt keine Zitate vor. */
function cluster_one_post_dolly_ist_ki(int $c1_uid): bool {
    return $c1_uid > 0 && function_exists('cluster_one_ai_identity_user_ids')
        && in_array($c1_uid, array_map('intval', (array) cluster_one_ai_identity_user_ids()), true);
}

/** Alle Vorschlaege, nach Kennung (V1, V2 …). Jede Zeile:
 *  id, texte {sprachcode => text}, von (uid), von_name (Anzeigename beim
 *  Vorschlagen, falls das Konto spaeter fort ist), wann, stand
 *  (offen|angenommen|abgelehnt|zurueckgezogen), und nach der Entscheidung
 *  entschieden_von, entschieden_wann, key (angenommen) oder grund (abgelehnt). */
function cluster_one_post_dolly_vorschlaege(): array {
    $c1_v = get_option('cluster_one_post_dolly_vorschlaege', array());
    if (!is_array($c1_v)) { return array(); }
    $c1_aus = array();
    foreach ($c1_v as $c1_z) {
        if (!is_array($c1_z) || !isset($c1_z['id']) || !is_string($c1_z['id']) || $c1_z['id'] === '') { continue; }
        $c1_aus[$c1_z['id']] = $c1_z;
    }
    return $c1_aus;
}

/** DER EINE SCHREIBWEG auf die Vorschlaege. autoload false: gelesen nur auf dem Reiter Zitate. */
function cluster_one_post_dolly_vorschlaege_schreiben(array $c1_v): void {
    update_option('cluster_one_post_dolly_vorschlaege', array_values($c1_v), false);
}

/** 0.3.63: Die naechste Kennung, die noch nie vergeben war. Groesseres aus dem
 *  Zaehler und der Liste; der Zaehler wird mit der Vergabe fortgeschrieben.
 *  autoload false wie die Liste. */
function cluster_one_post_dolly_naechste_kennung(array $c1_v): string {
    $c1_hoch = (int) get_option('cluster_one_post_dolly_vorschlaege_zaehler', 0);
    foreach (array_keys($c1_v) as $c1_k) {
        if (preg_match('~^V(\d+)$~', (string) $c1_k, $c1_m)) { $c1_hoch = max($c1_hoch, (int) $c1_m[1]); }
    }
    $c1_n = $c1_hoch + 1;
    update_option('cluster_one_post_dolly_vorschlaege_zaehler', $c1_n, false);
    return 'V' . $c1_n;
}

/** 0.3.69: Die Kennungen, die dieser SysOp aus seiner Ansicht genommen hat. Benutzer-Meta,
 *  je Konto; der Raeumer loescht es fuer alle. */
function cluster_one_post_dolly_versteckt(int $c1_uid): array {
    $c1_m = get_user_meta($c1_uid, 'cluster_one_post_dolly_ausgeblendet', true);
    if (!is_array($c1_m)) { return array(); }
    return array_values(array_unique(array_filter(array_map('strval', $c1_m), 'strlen')));
}

function cluster_one_post_dolly_versteckt_schreiben(int $c1_uid, array $c1_ids): void {
    $c1_ids = array_values(array_unique(array_filter(array_map('strval', $c1_ids), 'strlen')));
    if ($c1_ids === array()) { delete_user_meta($c1_uid, 'cluster_one_post_dolly_ausgeblendet'); return; }
    update_user_meta($c1_uid, 'cluster_one_post_dolly_ausgeblendet', $c1_ids);
}

/** Wie viele offene Vorschlaege hat dieser Mensch? */
function cluster_one_post_dolly_offene_von(int $c1_uid): int {
    $c1_n = 0;
    foreach (cluster_one_post_dolly_vorschlaege() as $c1_z) {
        if (($c1_z['stand'] ?? '') === 'offen' && (int) ($c1_z['von'] ?? 0) === $c1_uid) { $c1_n++; }
    }
    return $c1_n;
}

/** 0.3.62 (31.10.2026, Neo) — DAS TUEREREIGNIS je Vorschlags-Handlung. Bis 0.3.61 schrieb
 *  Post Dolly kein Chronik-Wort, weil die Woerter im Boden fest standen; seit Boden 1.19.5
 *  meldet der Lader seine ueber cluster_one_chronik_woerter. Sichtbarkeit 'all': die Liste
 *  der Vorschlaege sieht jedes Mitglied, also darf auch die Chronik-Zeile jedes Mitglied
 *  sehen. Kein Text, kein Name des Vorschlagenden in der Zeile (Artikel 4) – nur dass,
 *  wann, wer gehandelt hat, und die Kennung im Container. Ohne Boden passiert nichts. */
function cluster_one_post_dolly_chronik(string $c1_typ, string $c1_id): void {
    if (!function_exists('cluster_one_event_emit')) { return; }
    cluster_one_event_emit(array(
        'type'           => $c1_typ,
        'container_type' => 'proposal',
        'container_id'   => $c1_id,
        'actor_id'       => function_exists('cluster_one_actor_id_human') ? cluster_one_actor_id_human() : 'wp:' . get_current_user_id(),
        /* 0.3.62–0.3.68: 'all', weil die Liste jedes Mitglied sah. 0.3.69: nur der SysOp – das
           Mitglied sieht nur seine eigenen Vorschlaege, und eine Chronik-Zeile „Zitat vorgeschlagen,
           MenschB" waere die Liste durch die Hintertuer. Der eigene Stand steht ihm in der Liste. */
        'visibility'     => '',
    ), true);
}

/** Zurueck auf den Reiter Zitate, mit der Antwort in der Adresse. */
function cluster_one_post_dolly_vorschlag_antwort(string $c1_code, string $c1_kennung = ''): void {
    $c1_args = array('c1_vorschlag' => $c1_code);
    if ($c1_kennung !== '') { $c1_args['c1_kennung'] = $c1_kennung; }
    wp_safe_redirect(add_query_arg($c1_args, admin_url('admin.php?page=cluster_one_post_dolly_tabelle')));
    exit;
}

add_action('admin_post_cluster_one_post_dolly_vorschlag', function () {
    if (!is_user_logged_in() || !current_user_can('cluster_one_access')) {
        wp_die('-1', 403);
    }
    // phpcs:disable WordPress.Security.NonceVerification.Missing -- die Nonce wird gleich geprueft; ihr Name haengt vom Tun und der Kennung ab, die dafuer zuerst gelesen werden
    $c1_tun = isset($_POST['c1_tun']) ? sanitize_key((string) wp_unslash($_POST['c1_tun'])) : '';
    $c1_id  = isset($_POST['c1_id'])  ? strtoupper(preg_replace('~[^A-Za-z0-9]~', '', (string) wp_unslash($_POST['c1_id']))) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (preg_replace-Weissliste), den der Pruefer nicht kennt
    // phpcs:enable
    if (!in_array($c1_tun, array('vorschlagen', 'zurueckziehen', 'annehmen', 'ablehnen', 'oeffnen', 'entfernen', 'ausblenden', 'einblenden'), true)) {
        wp_die('-1', 400);
    }
    check_admin_referer('cluster_one_post_dolly_vorschlag_' . $c1_tun . ($c1_tun === 'vorschlagen' ? '' : '_' . $c1_id));

    $c1_uid = (int) get_current_user_id();
    $c1_v   = cluster_one_post_dolly_vorschlaege();

    if ($c1_tun === 'vorschlagen') {
        if (cluster_one_post_dolly_ist_ki($c1_uid)) { cluster_one_post_dolly_vorschlag_antwort('ki'); }
        if (cluster_one_post_dolly_offene_von($c1_uid) >= CLUSTER_ONE_POST_DOLLY_OFFEN_JE_PERSON) { cluster_one_post_dolly_vorschlag_antwort('voll'); }
        $c1_texte = array();
        $c1_in = isset($_POST['c1_text']) && is_array($_POST['c1_text']) ? wp_unslash($_POST['c1_text']) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- je Feld gleich darunter
        foreach ($c1_in as $c1_k => $c1_t) {
            if (!cluster_one_post_dolly_ist_code((string) $c1_k) || !is_string($c1_t)) { continue; }
            $c1_t = sanitize_text_field($c1_t);
            if (trim($c1_t) !== '') { $c1_texte[(string) $c1_k] = $c1_t; }
        }
        if ($c1_texte === array()) { cluster_one_post_dolly_vorschlag_antwort('leer'); }
        /* 0.3.60: DOPPELTE WERDEN LAUT ABGEWIESEN, nicht still ein zweites Mal
           eingetragen. Nemesus' Sorge (31.10.): dass jemand denselben Vorschlag aus
           Versehen noch einmal einreicht. Die Antwort nennt, wo der Text schon steht. */
        $c1_da = cluster_one_post_dolly_schon_da($c1_texte);
        if ($c1_da !== array()) {
            $c1_args = array(
                'c1_vorschlag' => $c1_da['wo'] === 'zitat' ? 'doppelt_zitat' : 'doppelt_vorschlag',
                'c1_kennung'   => $c1_da['kennung'],
                'c1_stand'     => $c1_da['stand'],
            );
            /* 0.3.69: ein fremder Vorschlag wird dem Mitglied nicht genannt – weder Kennung noch Stand. */
            if ($c1_da['wo'] === 'vorschlag' && !cluster_one_post_dolly_darf_speichern()
                && (int) ($c1_v[$c1_da['kennung']]['von'] ?? 0) !== $c1_uid) {
                $c1_args = array('c1_vorschlag' => 'doppelt_fremd');
            }
            wp_safe_redirect(add_query_arg($c1_args, admin_url('admin.php?page=cluster_one_post_dolly_tabelle')));
            exit;
        }
        /* bis 0.3.62: `while (isset($c1_v['V' . $c1_n])) { $c1_n++; }` – „Kennungen werden
           nie wiederverwendet: nichts wird geloescht". Seit 0.3.63 wird geloescht, also zaehlt
           eine Option mit, s. cluster_one_post_dolly_naechste_kennung(). */
        $c1_kenn = cluster_one_post_dolly_naechste_kennung($c1_v);
        $c1_u = get_userdata($c1_uid);
        $c1_v[$c1_kenn] = array(
            'id'       => $c1_kenn,
            'texte'    => $c1_texte,
            'von'      => $c1_uid,
            'von_name' => $c1_u ? (string) $c1_u->display_name : '',
            'wann'     => time(),
            'stand'    => 'offen',
        );
        cluster_one_post_dolly_vorschlaege_schreiben($c1_v);
        cluster_one_post_dolly_chronik('quote_proposed', $c1_kenn);
        cluster_one_post_dolly_vorschlag_antwort('vorgeschlagen', $c1_kenn);
    }

    /* 0.3.63 — ENTFERNEN, s. Kopf dieses Blocks. Nur Entschiedenes; eigene oder als
       SysOp jeden. Die Chronik-Zeile wird nach dem Schreiben gesetzt, wie bei den
       anderen Handlungen; die Kennung steht in ihr, der Text nicht (Artikel 4). */
    if ($c1_tun === 'entfernen') {
        if (!isset($c1_v[$c1_id])) { cluster_one_post_dolly_vorschlag_antwort('nicht_da', $c1_id); }
        if (($c1_v[$c1_id]['stand'] ?? 'offen') === 'offen') { cluster_one_post_dolly_vorschlag_antwort('noch_offen', $c1_id); }
        /* 0.3.63: „… another member’s proposal is the SysOp’s." – der SysOp durfte jeden entfernen.
           0.3.69: nur der Urheber. Die Zeile gehoert ihm; der SysOp blendet aus (unten). */
        if ((int) ($c1_v[$c1_id]['von'] ?? 0) !== $c1_uid) {
            wp_die(esc_html__('Not permitted: removing a proposal is its author’s. The SysOp hides it from view instead.', 'cluster-one-post-dolly'), '', 403);
        }
        unset($c1_v[$c1_id]);
        cluster_one_post_dolly_vorschlaege_schreiben($c1_v);
        if (isset(cluster_one_post_dolly_vorschlaege()[$c1_id])) {
            cluster_one_post_dolly_vorschlag_antwort('nicht_entfernt', $c1_id); // gemessen: steht noch da, also laut
        }
        cluster_one_post_dolly_chronik('quote_proposal_removed', $c1_id);
        cluster_one_post_dolly_vorschlag_antwort('entfernt', $c1_id);
    }

    /* 0.3.69 — AUS- UND EINBLENDEN, nur SysOp, nur Entschiedenes, nur die eigene Ansicht
       (Benutzer-Meta). Keine Chronik-Zeile: am gemeinsamen Bestand passiert nichts. */
    if ($c1_tun === 'ausblenden' || $c1_tun === 'einblenden') {
        if (!cluster_one_post_dolly_darf_speichern()) {
            wp_die(esc_html__('Not permitted: hiding a proposal from view is the SysOp’s.', 'cluster-one-post-dolly'), '', 403);
        }
        $c1_weg = cluster_one_post_dolly_versteckt($c1_uid);
        if ($c1_tun === 'ausblenden') {
            if (!isset($c1_v[$c1_id])) { cluster_one_post_dolly_vorschlag_antwort('nicht_da', $c1_id); }
            if (($c1_v[$c1_id]['stand'] ?? 'offen') === 'offen') { cluster_one_post_dolly_vorschlag_antwort('noch_offen', $c1_id); }
            $c1_weg[] = $c1_id;
            cluster_one_post_dolly_versteckt_schreiben($c1_uid, $c1_weg);
            cluster_one_post_dolly_vorschlag_antwort('ausgeblendet', $c1_id);
        }
        cluster_one_post_dolly_versteckt_schreiben($c1_uid, array_diff($c1_weg, array($c1_id)));
        wp_safe_redirect(add_query_arg(array('c1_vorschlag' => 'eingeblendet', 'c1_kennung' => $c1_id, 'c1_zeigen' => '1'), admin_url('admin.php?page=cluster_one_post_dolly_tabelle')));
        exit;
    }

    /* 0.3.61 (31.10.2026, Neo) — ZURUECK AUF OFFEN. Nemesus' Idee: der SysOp hat
       es sich anders ueberlegt und will neu entscheiden; „fuer beides wuerde
       reichen, den Status bei der ersten Uebermittlung wiederherzustellen".
       Dazu der Fall, den die Doppelten-Pruefung (0.3.60) sonst zur Sackgasse
       macht: wer seinen Vorschlag zurueckgezogen hat, konnte denselben Text
       nicht noch einmal einreichen. Also derselbe Knopf fuer beide Seiten:
         SysOp     bei angenommen und abgelehnt
         Mitglied  beim eigenen zurueckgezogenen (dieselbe Grenze wie beim Vorschlagen)
       ANGENOMMEN HEISST IN DER TABELLE, OFFEN HEISST NICHT IN DER TABELLE: beim
       Widerruf einer Annahme geht die Zeile mit dem Schluessel wieder aus den
       Zitaten – durch denselben Sanitizer –, und die Antwort sagt es; war sie
       schon fort, wird nur der Stand gesetzt und auch das gesagt.
       NICHTS VERSCHWINDET: die aufgehobene Entscheidung wandert in 'widerrufen'
       (Liste, je Widerruf ein Vermerk) und steht in der Liste unter dem Stand. */
    if ($c1_tun === 'oeffnen') {
        if (!isset($c1_v[$c1_id]) || ($c1_v[$c1_id]['stand'] ?? 'offen') === 'offen') {
            cluster_one_post_dolly_vorschlag_antwort('schon_offen', $c1_id);
        }
        $c1_war = (string) $c1_v[$c1_id]['stand'];
        if ($c1_war === 'zurueckgezogen') {
            if ((int) ($c1_v[$c1_id]['von'] ?? 0) !== $c1_uid) { wp_die('-1', 403); } // wieder einreichen kann nur, wer vorgeschlagen hat
            if (cluster_one_post_dolly_offene_von($c1_uid) >= CLUSTER_ONE_POST_DOLLY_OFFEN_JE_PERSON) { cluster_one_post_dolly_vorschlag_antwort('voll'); }
        } elseif (!cluster_one_post_dolly_darf_speichern()) {
            wp_die(esc_html__('Not permitted: reopening a decided proposal is the SysOp’s.', 'cluster-one-post-dolly'), '', 403);
        }
        $c1_key  = (string) ($c1_v[$c1_id]['key'] ?? '');
        $c1_code = 'geoeffnet';
        if ($c1_war === 'angenommen' && $c1_key !== '') {
            $c1_vorher = cluster_one_post_dolly_zitate();
            $c1_rest   = array();
            foreach ($c1_vorher as $c1_z) {
                if (strtoupper((string) ($c1_z['key'] ?? '')) !== strtoupper($c1_key)) { $c1_rest[] = $c1_z; }
            }
            if (count($c1_rest) < count($c1_vorher)) {
                $c1_o = cluster_one_post_dolly_opts();
                $c1_o['zitate'] = $c1_rest;
                update_option('cluster_one_post_dolly', $c1_o); // durch den angemeldeten Sanitizer, wie beim Annehmen
                $c1_code = 'geoeffnet_zitat';
            } else {
                $c1_code = 'geoeffnet_ohne_zitat';
            }
        }
        if (!isset($c1_v[$c1_id]['widerrufen']) || !is_array($c1_v[$c1_id]['widerrufen'])) { $c1_v[$c1_id]['widerrufen'] = array(); }
        $c1_v[$c1_id]['widerrufen'][] = array(
            'war'             => $c1_war,
            'von'             => (int) ($c1_v[$c1_id]['entschieden_von'] ?? 0),
            'wann'            => (int) ($c1_v[$c1_id]['entschieden_wann'] ?? 0),
            'key'             => $c1_key,
            'grund'           => (string) ($c1_v[$c1_id]['grund'] ?? ''),
            'widerrufen_von'  => $c1_uid,
            'widerrufen_wann' => time(),
        );
        $c1_v[$c1_id]['stand'] = 'offen';
        unset($c1_v[$c1_id]['entschieden_von'], $c1_v[$c1_id]['entschieden_wann'], $c1_v[$c1_id]['key'], $c1_v[$c1_id]['grund']);
        cluster_one_post_dolly_vorschlaege_schreiben($c1_v);
        cluster_one_post_dolly_chronik('quote_reopened', $c1_id);
        $c1_args = array('c1_vorschlag' => $c1_code, 'c1_kennung' => $c1_id);
        if ($c1_key !== '') { $c1_args['c1_key'] = $c1_key; }
        wp_safe_redirect(add_query_arg($c1_args, admin_url('admin.php?page=cluster_one_post_dolly_tabelle')));
        exit;
    }

    if (!isset($c1_v[$c1_id]) || ($c1_v[$c1_id]['stand'] ?? '') !== 'offen') {
        cluster_one_post_dolly_vorschlag_antwort('nicht_offen', $c1_id);
    }

    if ($c1_tun === 'zurueckziehen') {
        if ((int) ($c1_v[$c1_id]['von'] ?? 0) !== $c1_uid) { wp_die('-1', 403); } // zurueckziehen kann nur, wer vorgeschlagen hat
        $c1_v[$c1_id]['stand']            = 'zurueckgezogen';
        $c1_v[$c1_id]['entschieden_von']  = $c1_uid;
        $c1_v[$c1_id]['entschieden_wann'] = time();
        cluster_one_post_dolly_vorschlaege_schreiben($c1_v);
        cluster_one_post_dolly_chronik('quote_proposal_withdrawn', $c1_id);
        cluster_one_post_dolly_vorschlag_antwort('zurueckgezogen', $c1_id);
    }

    if (!cluster_one_post_dolly_darf_speichern()) {
        wp_die(esc_html__('Not permitted: accepting or rejecting a proposal is the SysOp’s.', 'cluster-one-post-dolly'), '', 403);
    }

    if ($c1_tun === 'ablehnen') {
        $c1_v[$c1_id]['stand']            = 'abgelehnt';
        $c1_v[$c1_id]['entschieden_von']  = $c1_uid;
        $c1_v[$c1_id]['entschieden_wann'] = time();
        $c1_v[$c1_id]['grund']            = isset($_POST['c1_grund']) ? trim(sanitize_text_field(wp_unslash($_POST['c1_grund']))) : '';
        cluster_one_post_dolly_vorschlaege_schreiben($c1_v);
        cluster_one_post_dolly_chronik('quote_rejected', $c1_id);
        cluster_one_post_dolly_vorschlag_antwort('abgelehnt', $c1_id);
    }

    /* ANNEHMEN. Grundlage ist die Zitat-Liste, wie sie das Formular zeigen wuerde
       (mit der Vorgabe-Zeile, solange nichts gespeichert ist – sonst verschwaende
       sie beim ersten Annehmen, und beim Speichern des Formulars bliebe sie).
       Der Schluessel wird nach derselben Regel vergeben wie im Sanitizer, der
       naechste freie der A-Reihe; der Sanitizer laesst einen freien Schluessel
       stehen. Geschrieben wird durch ihn (update_option legt den angemeldeten
       Sanitizer selbst darueber), und GEMESSEN wird danach: steht die Zeile mit
       diesem Schluessel in der Tabelle? Wenn nicht, bleibt der Vorschlag offen. */
    $c1_vorher   = cluster_one_post_dolly_zitate();
    $c1_vergeben = array();
    foreach ($c1_vorher as $c1_z) { $c1_vergeben[] = strtoupper((string) ($c1_z['key'] ?? '')); }
    $c1_n = 1;
    while (in_array('A' . $c1_n, $c1_vergeben, true)) { $c1_n++; }
    $c1_key = 'A' . $c1_n;
    $c1_o = cluster_one_post_dolly_opts();
    $c1_o['zitate']   = $c1_vorher;
    $c1_o['zitate'][] = array_merge(array('key' => $c1_key), (array) $c1_v[$c1_id]['texte']);
    update_option('cluster_one_post_dolly', $c1_o);
    $c1_da = false;
    foreach (cluster_one_post_dolly_zitate() as $c1_z) {
        if (strtoupper((string) ($c1_z['key'] ?? '')) === $c1_key) { $c1_da = true; break; }
    }
    if (!$c1_da) {
        cluster_one_post_dolly_vorschlag_antwort('nicht_gespeichert', $c1_id); // der Vorschlag bleibt offen, und die Seite sagt es
    }
    $c1_v[$c1_id]['stand']            = 'angenommen';
    $c1_v[$c1_id]['entschieden_von']  = $c1_uid;
    $c1_v[$c1_id]['entschieden_wann'] = time();
    $c1_v[$c1_id]['key']              = $c1_key;
    cluster_one_post_dolly_vorschlaege_schreiben($c1_v);
    cluster_one_post_dolly_chronik('quote_accepted', $c1_id);
    cluster_one_post_dolly_vorschlag_antwort('angenommen', $c1_key);
});

/** Die Antwort auf einen Vorschlags-Knopf, oben auf dem Reiter Zitate. */
function cluster_one_post_dolly_vorschlag_meldung(): void {
    // phpcs:disable WordPress.Security.NonceVerification.Recommended -- nur gelesen, um eine Antwort zu zeigen; geschrieben hat der Handler mit Nonce.
    if (!isset($_GET['c1_vorschlag'])) { return; }
    $c1_code = sanitize_key((string) wp_unslash($_GET['c1_vorschlag']));
    $c1_kenn = isset($_GET['c1_kennung']) ? strtoupper(preg_replace('~[^A-Za-z0-9_\-]~', '', (string) wp_unslash($_GET['c1_kennung']))) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (preg_replace-Weissliste), den der Pruefer nicht kennt
    $c1_stand = isset($_GET['c1_stand']) ? sanitize_key((string) wp_unslash($_GET['c1_stand'])) : '';
    $c1_key   = isset($_GET['c1_key']) ? strtoupper(preg_replace('~[^A-Za-z0-9_\-]~', '', (string) wp_unslash($_GET['c1_key']))) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (preg_replace-Weissliste), den der Pruefer nicht kennt
    // phpcs:enable
    $c1_gut = array(
        /* translators: %s: id of the proposal (V3) */
        'vorgeschlagen'  => __('Proposal %s is recorded and waits for the SysOp.', 'cluster-one-post-dolly'),
        /* 0.3.63: bis dahin „It stays in the list." – seit dem Entfernen heisst es, wo der Knopf ist. */
        /* translators: %s: id of the proposal (V3) */
        'zurueckgezogen' => __('Proposal %s is withdrawn. It stays in the list; you can remove it there.', 'cluster-one-post-dolly'),
        /* translators: %s: key of the new quote (A7) */
        'angenommen'     => __('Proposal accepted: it is now quote %s.', 'cluster-one-post-dolly'),
        /* translators: %s: id of the proposal (V3) */
        'abgelehnt'      => __('Proposal %s is rejected. It stays in the list; you can remove it there.', 'cluster-one-post-dolly'),
        /* translators: %s: id of the proposal (V3) */
        'entfernt'       => __('Proposal %s is removed from the list. The Chronik keeps a line about it.', 'cluster-one-post-dolly'),
        /* translators: %s: id of the proposal (V3) */
        'ausgeblendet'   => __('Proposal %s is hidden from your view. It stays for its author; the grey line under the list shows it again.', 'cluster-one-post-dolly'),
        /* translators: %s: id of the proposal (V3) */
        'eingeblendet'   => __('Proposal %s is back in your view.', 'cluster-one-post-dolly'),
        /* translators: %s: id of the proposal (V3) */
        'geoeffnet'      => __('Proposal %s is open again and waits for the SysOp. The earlier decision stays noted.', 'cluster-one-post-dolly'),
        /* translators: 1: id of the proposal (V3), 2: key of the quote (A7) */
        'geoeffnet_zitat' => sprintf(__('Proposal %1$s is open again; quote %2$s left the table. The earlier decision stays noted.', 'cluster-one-post-dolly'), '%s', $c1_key !== '' ? $c1_key : '—'),
        /* translators: 1: id of the proposal (V3), 2: key of the quote (A7) */
        'geoeffnet_ohne_zitat' => sprintf(__('Proposal %1$s is open again; quote %2$s was no longer in the table, nothing was removed. The earlier decision stays noted.', 'cluster-one-post-dolly'), '%s', $c1_key !== '' ? $c1_key : '—'),
    );
    $c1_schlecht = array(
        'leer'              => __('Nothing was proposed: every field was empty.', 'cluster-one-post-dolly'),
        /* translators: %d: the limit of open proposals per person */
        'voll'              => sprintf(__('Nothing was proposed: you already have %d open proposals, the most at a time. Withdraw one, or wait for the SysOp.', 'cluster-one-post-dolly'), CLUSTER_ONE_POST_DOLLY_OFFEN_JE_PERSON),
        'ki'                => __('Nothing was proposed: an AI identity does not propose quotes.', 'cluster-one-post-dolly'),
        'nicht_offen'       => __('Nothing was changed: this proposal is not open any more, or it does not exist.', 'cluster-one-post-dolly'),
        'schon_offen'       => __('Nothing was changed: this proposal is open already, or it does not exist.', 'cluster-one-post-dolly'),
        'nicht_gespeichert' => __('Nothing was changed: the quote could not be added to the table. The proposal stays open.', 'cluster-one-post-dolly'),
        'nicht_da'          => __('Nothing was removed: this proposal does not exist any more.', 'cluster-one-post-dolly'),
        'noch_offen'        => __('Nothing was removed: this proposal is still open. Decide it or withdraw it first.', 'cluster-one-post-dolly'),
        'nicht_entfernt'    => __('Nothing was removed: the proposal could not be taken out of the list. It is still there.', 'cluster-one-post-dolly'),
        /* translators: %s: key of the line in the table (A3) */
        'doppelt_zitat'     => sprintf(__('Nothing was proposed: this text already stands in the table as %s.', 'cluster-one-post-dolly'), $c1_kenn !== '' ? $c1_kenn : '—'),
        /* translators: 1: id of the earlier proposal (V2), 2: its state (open, accepted, rejected, withdrawn) */
        'doppelt_vorschlag' => sprintf(__('Nothing was proposed: this text was already proposed as %1$s (%2$s). Open the decided proposals below to see what became of it.', 'cluster-one-post-dolly'), $c1_kenn !== '' ? $c1_kenn : '—', cluster_one_post_dolly_stand_wort($c1_stand)),
        'doppelt_fremd'     => __('Nothing was proposed: this text has already been proposed to the SysOp by someone else.', 'cluster-one-post-dolly'),
    );
    if (isset($c1_gut[$c1_code])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(sprintf($c1_gut[$c1_code], $c1_kenn !== '' ? $c1_kenn : '—')) . '</p></div>';
    } elseif (isset($c1_schlecht[$c1_code])) {
        echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html($c1_schlecht[$c1_code]) . '</p></div>';
    }
}

/** Der Abschnitt „Vorschlaege" unter den beiden Tabellen: die Liste fuer alle,
 *  die Knoepfe je Recht, das Formular fuer Mitglieder ohne Speicherrecht. */
function cluster_one_post_dolly_vorschlaege_teil(): void {
    $c1_v     = cluster_one_post_dolly_vorschlaege();
    $c1_uid   = (int) get_current_user_id();
    $c1_sysop = cluster_one_post_dolly_darf_speichern();
    $c1_ki    = cluster_one_post_dolly_ist_ki($c1_uid);
    /* 0.3.69 (31.10.2026, Neo) — EIN VORSCHLAG IST EIN GESPRAECH ZWISCHEN ZWEIEN. Bis
       0.3.68 sah jedes Mitglied die ganze Liste, auch die Vorschlaege der anderen.
       Nemesus (31.10.): „wandert der auch zu MenschA? Ich glaube, das waere zu viel des
       Guten, sonst muessten wir womoeglich noch ein Voting-System einarbeiten. Wer
       Diskussionen will, soll Bismarck dafuer nutzen." Also: das Mitglied sieht nur
       seine eigenen, der SysOp alle. Die Chronik-Zeilen dazu sieht nur der SysOp
       (cluster_one_post_dolly_chronik), die Doppelten-Warnung nennt dem Mitglied keinen
       fremden Vorschlag mehr (Handler, 'doppelt_fremd'). */
    if (!$c1_sysop) {
        $c1_v = array_filter($c1_v, function ($c1_z) use ($c1_uid) { return (int) ($c1_z['von'] ?? 0) === $c1_uid; });
    }
    /* 0.3.69 — AUSBLENDEN STATT ENTFERNEN beim SysOp: die Zeile gehoert dem Vorschlagenden
       (Nemesus 31.10.: „ergibt das einen Sinn, dass die dann bei MenschA auch geloescht
       wurden? Ich denke nicht."). Der SysOp nimmt fremde entschiedene Vorschlaege nur aus
       SEINER Ansicht (Benutzer-Meta), eine graue Zeile unter der Liste zaehlt sie und
       zeigt sie auf Wunsch wieder, mit Knopf zum Einblenden. Nichts verschwindet. */
    $c1_versteckt = $c1_sysop ? cluster_one_post_dolly_versteckt($c1_uid) : array();
    $c1_zeigen    = $c1_sysop && isset($_GET['c1_zeigen']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nur gelesen, schaltet die Ansicht
    $c1_sprachen = cluster_one_post_dolly_sprachen();
    foreach ($c1_v as $c1_z) {
        foreach ((array) ($c1_z['texte'] ?? array()) as $c1_k => $c1_t) {
            if (cluster_one_post_dolly_ist_code($c1_k) && is_string($c1_t) && trim($c1_t) !== '' && !in_array($c1_k, $c1_sprachen, true)) { $c1_sprachen[] = $c1_k; }
        }
    }
    $c1_format = (string) get_option('date_format') . ' ' . (string) get_option('time_format');
    $c1_ziel   = admin_url('admin-post.php');
    ?>
        <h2 style="margin-top:1.6em;"><?php esc_html_e('Proposals', 'cluster-one-post-dolly'); ?></h2>
        <p style="max-width:720px;">
            <?php if ($c1_sysop) : ?>
            <?php /* 0.3.63: bis dahin „Nothing disappears: every proposal stays in this list with its state."
                     0.3.69: bis dahin „A decided proposal stays in the list until you or its author remove it; …" */ ?>
            <?php esc_html_e('What members propose; each member sees only their own. Accept puts the line into the quotes with the next free key; reject leaves it here, with your reason if you give one. Removing a decided proposal is its author’s; you can hide it from your view, and the Chronik keeps a line for every step.', 'cluster-one-post-dolly'); ?>
            <?php else : ?>
            <?php /* 0.3.63: bis dahin „both stay visible here, nothing disappears."
                     0.3.69: bis dahin „both stay visible here until you or the SysOp remove them." */ ?>
            <?php esc_html_e('A quote you would like to see here: write it in the languages you can, and propose it. Only you and the SysOp see your proposals. The SysOp accepts it into the table or rejects it; both stay here until you remove them. You can withdraw your own proposal while it is open.', 'cluster-one-post-dolly'); ?>
            <?php endif; ?>
        </p>
        <?php
        /* 0.3.60 (31.10.2026, Neo), Nemesus: die Liste fuellte die Seite. Offene
           stehen immer da; Entschiedene (angenommen, abgelehnt, zurueckgezogen)
           liegen zusammengeklappt darunter. Geloescht wird weiter nichts – Nemesus
           hat Loeschen erwogen und sich fuer das Einklappen entschieden (31.10.).
           0.3.63, am selben Tag: beides. Das Einklappen bleibt, und im Eingeklappten
           steht je Zeile der Knopf „Entfernen" (s. Kopf des Vorschlags-Blocks). */
        $c1_offene = array(); $c1_fertig = array(); $c1_weg = 0;
        foreach ($c1_v as $c1_z) {
            if (($c1_z['stand'] ?? 'offen') === 'offen') { $c1_offene[] = $c1_z; continue; }
            if (in_array((string) $c1_z['id'], $c1_versteckt, true)) {
                $c1_weg++;
                if (!$c1_zeigen) { continue; } // 0.3.69: ausgeblendet – nur auf Wunsch in der Liste
            }
            $c1_fertig[] = $c1_z;
        }
        if ($c1_v === array()) : ?>
        <p class="description"><?php esc_html_e('No proposals yet.', 'cluster-one-post-dolly'); ?></p>
        <?php else : ?>
            <?php if ($c1_offene === array()) : ?>
            <p class="description"><?php esc_html_e('No open proposals.', 'cluster-one-post-dolly'); ?></p>
            <?php else : ?>
            <?php cluster_one_post_dolly_vorschlaege_tabelle($c1_offene, $c1_sprachen, $c1_uid, $c1_sysop, $c1_format, $c1_ziel); ?>
            <?php endif; ?>
            <?php if ($c1_fertig !== array()) : ?>
            <details style="margin-top:1em;max-width:1100px;"<?php echo $c1_zeigen ? ' open' : ''; ?>>
                <summary style="cursor:pointer;"><?php echo esc_html(sprintf(
                    /* 0.3.63: bis dahin „… Nothing is deleted; click to show." */
                    /* translators: %d: number of decided proposals */
                    _n('%d decided proposal: accepted, rejected or withdrawn. Click to show.', '%d decided proposals: accepted, rejected or withdrawn. Click to show.', count($c1_fertig), 'cluster-one-post-dolly'),
                    count($c1_fertig))); ?></summary>
                <?php cluster_one_post_dolly_vorschlaege_tabelle($c1_fertig, $c1_sprachen, $c1_uid, $c1_sysop, $c1_format, $c1_ziel, $c1_versteckt); ?>
            </details>
            <?php endif; ?>
            <?php if ($c1_weg > 0) : ?>
            <p class="description" style="margin-top:.6em;">
                <?php if ($c1_zeigen) : ?>
                    <?php echo esc_html(sprintf(
                        /* translators: %d: number of proposals hidden from this SysOp's view */
                        _n('%d hidden proposal is shown in grey above.', '%d hidden proposals are shown in grey above.', $c1_weg, 'cluster-one-post-dolly'), $c1_weg)); ?>
                    <a href="<?php echo esc_url(remove_query_arg(array('c1_zeigen', 'c1_vorschlag', 'c1_kennung'))); ?>"><?php esc_html_e('Hide them again', 'cluster-one-post-dolly'); ?></a>
                <?php else : ?>
                    <?php echo esc_html(sprintf(
                        /* translators: %d: number of proposals hidden from this SysOp's view */
                        _n('%d proposal hidden from your view; it stays for its author.', '%d proposals hidden from your view; they stay for their authors.', $c1_weg, 'cluster-one-post-dolly'), $c1_weg)); ?>
                    <a href="<?php echo esc_url(add_query_arg('c1_zeigen', '1', remove_query_arg(array('c1_vorschlag', 'c1_kennung')))); ?>"><?php esc_html_e('Show', 'cluster-one-post-dolly'); ?></a>
                <?php endif; ?>
            </p>
            <?php endif; ?>
        <?php endif; ?>

        <?php
        /* DAS FORMULAR: fuer Mitglieder ohne Speicherrecht, die keine KI-Kennung
           sind. Der SysOp schreibt direkt in die Tabelle, ein Vorschlag an sich
           selbst waere ein zweiter Weg auf dieselbe Wirkung. */
        if ($c1_sysop || $c1_ki) { return; }
        $c1_offen = cluster_one_post_dolly_offene_von($c1_uid);
        if ($c1_offen >= CLUSTER_ONE_POST_DOLLY_OFFEN_JE_PERSON) : ?>
        <p class="description" style="max-width:720px;margin-top:1em;">
            <?php echo esc_html(sprintf(
                /* translators: %d: the limit of open proposals per person */
                __('You have %d open proposals, the most at a time. Withdraw one, or wait for the SysOp, before you propose another.', 'cluster-one-post-dolly'),
                CLUSTER_ONE_POST_DOLLY_OFFEN_JE_PERSON)); ?>
        </p>
        <?php return; endif; ?>
        <h3 style="margin-top:1.2em;"><?php esc_html_e('Propose a quote', 'cluster-one-post-dolly'); ?></h3>
        <form method="post" action="<?php echo esc_url($c1_ziel); ?>">
            <input type="hidden" name="action" value="cluster_one_post_dolly_vorschlag">
            <input type="hidden" name="c1_tun" value="vorschlagen">
            <?php wp_nonce_field('cluster_one_post_dolly_vorschlag_vorschlagen'); ?>
            <table class="widefat striped" style="max-width:960px;">
                <thead>
                    <tr>
                        <?php foreach (cluster_one_post_dolly_sprachen() as $c1_s) : ?>
                            <th><?php echo esc_html(cluster_one_post_dolly_sprachname($c1_s)); ?> <code><?php echo esc_html($c1_s); ?></code></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php foreach (cluster_one_post_dolly_sprachen() as $c1_s) : ?>
                        <td><input type="text" style="width:100%;" name="c1_text[<?php echo esc_attr($c1_s); ?>]" value=""></td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
            <p class="description" style="max-width:720px;">
                <?php echo esc_html(sprintf(
                    /* translators: 1: the limit of open proposals per person, 2: how many this person has open */
                    __('Fill in the languages you can; one is enough. At most %1$d open proposals per person at a time; you have %2$d.', 'cluster-one-post-dolly'),
                    CLUSTER_ONE_POST_DOLLY_OFFEN_JE_PERSON, $c1_offen)); ?>
            </p>
            <?php submit_button(__('Propose', 'cluster-one-post-dolly'), 'secondary'); ?>
        </form>
    <?php
}

/** Das eine Wort zu einem Stand, fuer Saetze, die einen Vorschlag nennen. */
function cluster_one_post_dolly_stand_wort(string $c1_stand): string {
    switch ($c1_stand) {
        case 'angenommen':     return __('accepted', 'cluster-one-post-dolly');
        case 'abgelehnt':      return __('rejected', 'cluster-one-post-dolly');
        case 'zurueckgezogen': return __('withdrawn', 'cluster-one-post-dolly');
        default:               return __('open', 'cluster-one-post-dolly');
    }
}

/** Ein Text, wie er fuer die Doppelten-Pruefung verglichen wird: Leerraum
 *  zusammengezogen, Gross- und Kleinschreibung gleich, Anfuehrungszeichen und
 *  Satzzeichen am Rand weg. Zwei Zeilen, die so gleich sind, sind fuer einen
 *  Leser dasselbe Zitat. */
function cluster_one_post_dolly_vergleichsform(string $c1_t): string {
    $c1_t = (string) preg_replace('~\s+~u', ' ', trim($c1_t));
    $c1_t = (string) preg_replace('~^[\s\p{P}]+|[\s\p{P}]+$~u', '', $c1_t); // Satz- und Anfuehrungszeichen am Rand, UTF-8-sicher (trim() mit Zeichenliste zerschnitte Mehrbyte-Zeichen)
    return function_exists('mb_strtolower') ? mb_strtolower($c1_t, 'UTF-8') : strtolower($c1_t);
}

/** Steht einer dieser Texte schon da? Antwort: array('wo' => 'zitat'|'vorschlag',
 *  'kennung' => 'A3'|'V2', 'stand' => …) oder leer. Erst die Tabellen, dann die
 *  Vorschlaege in jedem Stand – ein abgelehnter Satz, noch einmal eingereicht,
 *  soll den Grund von damals finden, nicht eine zweite Runde. */
function cluster_one_post_dolly_schon_da(array $c1_texte): array {
    $c1_such = array();
    foreach ($c1_texte as $c1_t) {
        $c1_f = cluster_one_post_dolly_vergleichsform((string) $c1_t);
        if ($c1_f !== '') { $c1_such[] = $c1_f; }
    }
    if ($c1_such === array()) { return array(); }
    foreach (array_merge(cluster_one_post_dolly_zitate(), cluster_one_post_dolly_ansagen()) as $c1_z) {
        foreach ($c1_z as $c1_k => $c1_t) {
            if (!cluster_one_post_dolly_ist_code($c1_k) || !is_string($c1_t)) { continue; }
            if (in_array(cluster_one_post_dolly_vergleichsform($c1_t), $c1_such, true)) {
                return array('wo' => 'zitat', 'kennung' => (string) ($c1_z['key'] ?? ''), 'stand' => '');
            }
        }
    }
    foreach (cluster_one_post_dolly_vorschlaege() as $c1_z) {
        foreach ((array) ($c1_z['texte'] ?? array()) as $c1_t) {
            if (is_string($c1_t) && in_array(cluster_one_post_dolly_vergleichsform($c1_t), $c1_such, true)) {
                return array('wo' => 'vorschlag', 'kennung' => (string) $c1_z['id'], 'stand' => (string) ($c1_z['stand'] ?? 'offen'));
            }
        }
    }
    return array();
}

/** Wie viele Vorschlaege sind offen, von allen? Fuer die Zahl am Menue und die Zeile im Dashboard-Feld. */
function cluster_one_post_dolly_offene(): int {
    $c1_n = 0;
    foreach (cluster_one_post_dolly_vorschlaege() as $c1_z) {
        if (($c1_z['stand'] ?? 'offen') === 'offen') { $c1_n++; }
    }
    return $c1_n;
}

/* DIE ZAHL AM MENUE (0.3.60). Der Weg von WordPress fuer „etwas wartet": der rote
   Kreis an „Kommentare" und „Plugins". Nur fuer den, der entscheiden darf, nur
   solange etwas offen ist. KEIN admin_notices auf jeder Seite – der Dateikopf
   sagt, warum nicht (Hello Dollys Fehler). Das Dach setzt seine Eintraege auf
   admin_menu mit PHP_INT_MAX (menue.php), also danach: admin_head laeuft im
   <head>, menu-header.php zeichnet das Menue erst im <body>; der Seitentitel ist
   da schon gesetzt (get_admin_page_title vor <title>). Gesucht wird der Slug in
   jedem Untermenue und – fuer den Knoten mit einem Produkt – im Hauptmenue. */
add_action('admin_head', function () {
    if (!function_exists('cluster_one_post_dolly_darf_speichern') || !cluster_one_post_dolly_darf_speichern()) { return; }
    $c1_n = cluster_one_post_dolly_offene();
    if ($c1_n < 1) { return; }
    $c1_blase = ' <span class="awaiting-mod count-' . (int) $c1_n . '"><span class="pending-count" aria-hidden="true">' . (int) $c1_n . '</span>'
        /* translators: %d: number of open proposals */
        . '<span class="screen-reader-text">' . esc_html(sprintf(_n('%d open proposal', '%d open proposals', $c1_n, 'cluster-one-post-dolly'), $c1_n)) . '</span></span>';
    if (isset($GLOBALS['submenu']) && is_array($GLOBALS['submenu'])) {
        foreach ($GLOBALS['submenu'] as $c1_eltern => $c1_eintraege) {
            if (!is_array($c1_eintraege)) { continue; }
            foreach ($c1_eintraege as $c1_i => $c1_e) {
                if (is_array($c1_e) && (string) ($c1_e[2] ?? '') === 'cluster_one_post_dolly_tabelle') {
                    $GLOBALS['submenu'][$c1_eltern][$c1_i][0] .= $c1_blase;
                }
            }
        }
    }
    if (isset($GLOBALS['menu']) && is_array($GLOBALS['menu'])) {
        foreach ($GLOBALS['menu'] as $c1_i => $c1_e) {
            if (is_array($c1_e) && (string) ($c1_e[2] ?? '') === 'cluster_one_post_dolly_tabelle') {
                $GLOBALS['menu'][$c1_i][0] .= $c1_blase;
            }
        }
    }
});

/** Eine Tabelle Vorschlaege – fuer die offenen und, zusammengeklappt, die entschiedenen. */
function cluster_one_post_dolly_vorschlaege_tabelle(array $c1_zeilen, array $c1_sprachen, int $c1_uid, bool $c1_sysop, string $c1_format, string $c1_ziel, array $c1_versteckt = array()): void {
    ?>
        <table class="widefat striped" style="max-width:1100px;">
            <thead>
                <tr>
                    <th style="width:4em;"><?php esc_html_e('Id', 'cluster-one-post-dolly'); ?></th>
                    <?php foreach ($c1_sprachen as $c1_s) : ?>
                        <th><?php echo esc_html(cluster_one_post_dolly_sprachname($c1_s)); ?> <code><?php echo esc_html($c1_s); ?></code></th>
                    <?php endforeach; ?>
                    <th><?php esc_html_e('From', 'cluster-one-post-dolly'); ?></th>
                    <th><?php esc_html_e('State', 'cluster-one-post-dolly'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (array_reverse($c1_zeilen) as $c1_z) :
                $c1_id    = (string) $c1_z['id'];
                $c1_stand = (string) ($c1_z['stand'] ?? 'offen');
                $c1_u     = get_userdata((int) ($c1_z['von'] ?? 0));
                $c1_wer   = $c1_u ? (string) $c1_u->display_name : (string) ($c1_z['von_name'] ?? '');
                $c1_wann  = date_i18n($c1_format, (int) ($c1_z['wann'] ?? 0));
                $c1_ewann = date_i18n($c1_format, (int) ($c1_z['entschieden_wann'] ?? 0));
                if ($c1_stand === 'angenommen') {
                    /* translators: 1: key of the quote (A7), 2: date and time */
                    $c1_satz = sprintf(__('accepted as %1$s on %2$s', 'cluster-one-post-dolly'), (string) ($c1_z['key'] ?? ''), $c1_ewann);
                } elseif ($c1_stand === 'abgelehnt') {
                    /* translators: %s: date and time */
                    $c1_satz = sprintf(__('rejected on %s', 'cluster-one-post-dolly'), $c1_ewann);
                    if (trim((string) ($c1_z['grund'] ?? '')) !== '') { $c1_satz .= ' — ' . (string) $c1_z['grund']; }
                } elseif ($c1_stand === 'zurueckgezogen') {
                    /* translators: %s: date and time */
                    $c1_satz = sprintf(__('withdrawn on %s', 'cluster-one-post-dolly'), $c1_ewann);
                } else {
                    $c1_satz = __('open', 'cluster-one-post-dolly');
                }
                /* 0.3.61: die aufgehobenen Entscheidungen, je eine Zeile unter dem Stand. */
                $c1_vermerke = array();
                foreach ((array) ($c1_z['widerrufen'] ?? array()) as $c1_w) {
                    if (!is_array($c1_w)) { continue; }
                    $c1_wwann = date_i18n($c1_format, (int) ($c1_w['wann'] ?? 0));
                    $c1_rwann = date_i18n($c1_format, (int) ($c1_w['widerrufen_wann'] ?? 0));
                    $c1_war   = (string) ($c1_w['war'] ?? '');
                    if ($c1_war === 'angenommen') {
                        /* translators: 1: key of the quote (A7), 2: date and time of the acceptance, 3: date and time it was reopened */
                        $c1_vermerke[] = sprintf(__('earlier accepted as %1$s on %2$s, reopened on %3$s', 'cluster-one-post-dolly'), (string) ($c1_w['key'] ?? ''), $c1_wwann, $c1_rwann);
                    } elseif ($c1_war === 'abgelehnt') {
                        $c1_g = trim((string) ($c1_w['grund'] ?? ''));
                        /* translators: 1: date and time of the rejection, 2: the reason in parentheses or empty, 3: date and time it was reopened */
                        $c1_vermerke[] = sprintf(__('earlier rejected on %1$s%2$s, reopened on %3$s', 'cluster-one-post-dolly'), $c1_wwann, $c1_g !== '' ? ' (' . $c1_g . ')' : '', $c1_rwann);
                    } elseif ($c1_war === 'zurueckgezogen') {
                        /* translators: 1: date and time of the withdrawal, 2: date and time it was resubmitted */
                        $c1_vermerke[] = sprintf(__('earlier withdrawn on %1$s, resubmitted on %2$s', 'cluster-one-post-dolly'), $c1_wwann, $c1_rwann);
                    }
                }
            ?>
                <?php $c1_eigen = (int) ($c1_z['von'] ?? 0) === $c1_uid; $c1_grau = in_array($c1_id, $c1_versteckt, true); ?>
                <tr<?php echo $c1_grau ? ' style="opacity:.55;"' : ''; ?>>
                    <td><code><?php echo esc_html($c1_id); ?></code></td>
                    <?php foreach ($c1_sprachen as $c1_s) : ?>
                    <td><?php echo esc_html((string) (($c1_z['texte'] ?? array())[$c1_s] ?? '')); ?></td>
                    <?php endforeach; ?>
                    <td><?php echo esc_html($c1_wer !== '' ? $c1_wer : '—'); ?><br><span class="description"><?php echo esc_html($c1_wann); ?></span></td>
                    <td><?php echo esc_html($c1_satz); ?><?php foreach ($c1_vermerke as $c1_vm) : ?><br><span class="description"><?php echo esc_html($c1_vm); ?></span><?php endforeach; ?></td>
                    <?php /* 0.3.67 (31.10.2026, Nemesus): die Handlungszelle bricht nie um – Daumen hoch, Grund-Feld und
                             Daumen runter stehen in einer Zeile, auch wenn die Sprachspalten eng werden. Die Zelle nimmt sich
                             ihre Breite, die Textspalten geben nach (wie modi.php, white-space:nowrap am td). */ ?>
                    <td style="white-space:nowrap;width:1%;">
                    <?php if ($c1_stand !== 'offen' && (($c1_stand !== 'zurueckgezogen' && $c1_sysop) || ($c1_stand === 'zurueckgezogen' && (int) ($c1_z['von'] ?? 0) === $c1_uid && !cluster_one_post_dolly_ist_ki($c1_uid)))) : ?>
                        <?php /* 0.3.61: ein Knopf, zwei Seiten – SysOp bei angenommen/abgelehnt, Mitglied beim eigenen zurueckgezogenen.
                                 0.3.64 (31.10.2026, Nemesus): statt Textknopf das Pfeilsymbol (dashicons-undo, wie Bismarcks
                                 winsor.php); das Wort steht in title und aria-label. */ ?>
                        <?php $c1_wort = $c1_stand === 'zurueckgezogen' ? __('Resubmit', 'cluster-one-post-dolly') : __('Back to open', 'cluster-one-post-dolly'); ?>
                        <form method="post" action="<?php echo esc_url($c1_ziel); ?>" style="display:inline;margin:0;">
                            <input type="hidden" name="action" value="cluster_one_post_dolly_vorschlag">
                            <input type="hidden" name="c1_tun" value="oeffnen">
                            <input type="hidden" name="c1_id" value="<?php echo esc_attr($c1_id); ?>">
                            <?php wp_nonce_field('cluster_one_post_dolly_vorschlag_oeffnen_' . $c1_id); ?>
                            <?php /* 0.3.66: ↶ undo = zuruecknehmen, was man selbst getan hat (SysOp: die Entscheidung);
                                     ↷ redo = noch einmal einreichen. Dasselbe Paar wie beim Zurueckziehen unten. */ ?>
                            <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;vertical-align:middle;" title="<?php echo esc_attr($c1_wort); ?>" aria-label="<?php echo esc_attr($c1_wort); ?>">
                                <span class="dashicons <?php echo $c1_stand === 'zurueckgezogen' ? 'dashicons-redo' : 'dashicons-undo'; ?>" aria-hidden="true" style="color:#8a8a8a;font-size:18px;width:18px;height:18px;"></span>
                            </button>
                        </form>
                    <?php endif; ?>
                    <?php if ($c1_stand !== 'offen' && $c1_eigen) : ?>
                        <?php /* 0.3.63: Entfernen – Entschiedenes, eigenes oder als SysOp jedes. Rueckfrage wie bei Bismarcks Modi (modi.php).
                                 0.3.64 (Nemesus): „einfach ein x als Symbol, wie in allen anderen PlugIns auch" – dasselbe
                                 dashicons-dismiss wie in modi.php und kleinrock.php, statt des roten Textknopfs.
                                 0.3.69: nur noch der Urheber entfernt; der SysOp blendet aus (naechster Block). */ ?>
                        <form method="post" action="<?php echo esc_url($c1_ziel); ?>" style="display:inline;margin:0 0 0 .4em;">
                            <input type="hidden" name="action" value="cluster_one_post_dolly_vorschlag">
                            <input type="hidden" name="c1_tun" value="entfernen">
                            <input type="hidden" name="c1_id" value="<?php echo esc_attr($c1_id); ?>">
                            <?php wp_nonce_field('cluster_one_post_dolly_vorschlag_entfernen_' . $c1_id); ?>
                            <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;vertical-align:middle;" title="<?php echo esc_attr__('Remove', 'cluster-one-post-dolly'); ?>" aria-label="<?php echo esc_attr__('Remove', 'cluster-one-post-dolly'); ?>" onclick="return confirm('<?php
                                /* translators: %s: id of the proposal (V3) */
                                echo esc_js(sprintf(__('Remove proposal %s from the list? The Chronik keeps a line about it.', 'cluster-one-post-dolly'), $c1_id)); ?>');">
                                <span class="dashicons dashicons-dismiss" aria-hidden="true" style="color:#8a8a8a;font-size:18px;width:18px;height:18px;"></span>
                            </button>
                        </form>
                    <?php elseif ($c1_stand !== 'offen' && $c1_sysop) : ?>
                        <?php /* 0.3.69: der SysOp blendet fremde entschiedene Vorschlaege aus seiner Ansicht aus (×) oder
                                 wieder ein (Auge), Benutzer-Meta, keine Rueckfrage, keine Chronik-Zeile: am gemeinsamen
                                 Bestand aendert sich nichts. */ ?>
                        <form method="post" action="<?php echo esc_url($c1_ziel); ?>" style="display:inline;margin:0 0 0 .4em;">
                            <input type="hidden" name="action" value="cluster_one_post_dolly_vorschlag">
                            <input type="hidden" name="c1_tun" value="<?php echo $c1_grau ? 'einblenden' : 'ausblenden'; ?>">
                            <input type="hidden" name="c1_id" value="<?php echo esc_attr($c1_id); ?>">
                            <?php wp_nonce_field('cluster_one_post_dolly_vorschlag_' . ($c1_grau ? 'einblenden' : 'ausblenden') . '_' . $c1_id); ?>
                            <?php $c1_wort = $c1_grau ? __('Show again', 'cluster-one-post-dolly') : __('Hide from my view', 'cluster-one-post-dolly'); ?>
                            <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;vertical-align:middle;" title="<?php echo esc_attr($c1_wort); ?>" aria-label="<?php echo esc_attr($c1_wort); ?>">
                                <span class="dashicons <?php echo $c1_grau ? 'dashicons-visibility' : 'dashicons-dismiss'; ?>" aria-hidden="true" style="color:#8a8a8a;font-size:18px;width:18px;height:18px;"></span>
                            </button>
                        </form>
                    <?php endif; ?>
                    <?php if ($c1_stand === 'offen' && $c1_sysop) : ?>
                        <?php /* 0.3.65 (31.10.2026, Nemesus): „Daumen hoch für annehmen, Daumen runter für ablehnen" statt
                                 der Textknoepfe Accept / Reject. Eine Zeile: Daumen hoch · Grund-Feld · Daumen runter.
                                 Das Grund-Feld bleibt, es gehoert zum Ablehnen. Woerter in title und aria-label. */ ?>
                        <?php /* 0.3.68 (31.10.2026, Nemesus): Reihenfolge Grund · gruen · rot, „einheitlich mit den Kleinrock-
                                 Tabellen". Der gruene Daumen steht optisch im Ablehnen-Formular, gehoert aber per form="…"
                                 zum Annehmen-Formular (dasselbe Mittel wie Bismarcks modi.php, Knopf Save). Das Annehmen-
                                 Formular selbst traegt nur die versteckten Felder. */ ?>
                        <?php $c1_fid = 'c1-pd-annehmen-' . $c1_id; ?>
                        <form method="post" action="<?php echo esc_url($c1_ziel); ?>" id="<?php echo esc_attr($c1_fid); ?>" style="display:none;">
                            <input type="hidden" name="action" value="cluster_one_post_dolly_vorschlag">
                            <input type="hidden" name="c1_tun" value="annehmen">
                            <input type="hidden" name="c1_id" value="<?php echo esc_attr($c1_id); ?>">
                            <?php wp_nonce_field('cluster_one_post_dolly_vorschlag_annehmen_' . $c1_id); ?>
                        </form>
                        <form method="post" action="<?php echo esc_url($c1_ziel); ?>" style="display:inline;margin:0;white-space:nowrap;">
                            <input type="hidden" name="action" value="cluster_one_post_dolly_vorschlag">
                            <input type="hidden" name="c1_tun" value="ablehnen">
                            <input type="hidden" name="c1_id" value="<?php echo esc_attr($c1_id); ?>">
                            <?php wp_nonce_field('cluster_one_post_dolly_vorschlag_ablehnen_' . $c1_id); ?>
                            <input type="text" name="c1_grund" style="width:10em;vertical-align:middle;" placeholder="<?php echo esc_attr__('Reason (optional)', 'cluster-one-post-dolly'); ?>">
                            <button type="submit" form="<?php echo esc_attr($c1_fid); ?>" style="background:none;border:0;padding:0 0 0 .5em;cursor:pointer;line-height:1;vertical-align:middle;" title="<?php echo esc_attr__('Accept', 'cluster-one-post-dolly'); ?>" aria-label="<?php echo esc_attr__('Accept', 'cluster-one-post-dolly'); ?>">
                                <span class="dashicons dashicons-thumbs-up" aria-hidden="true" style="color:#1a7f37;font-size:20px;width:20px;height:20px;"></span>
                            </button>
                            <button type="submit" style="background:none;border:0;padding:0 0 0 .4em;cursor:pointer;line-height:1;vertical-align:middle;" title="<?php echo esc_attr__('Reject', 'cluster-one-post-dolly'); ?>" aria-label="<?php echo esc_attr__('Reject', 'cluster-one-post-dolly'); ?>">
                                <span class="dashicons dashicons-thumbs-down" aria-hidden="true" style="color:#b32d2e;font-size:20px;width:20px;height:20px;"></span>
                            </button>
                        </form>
                    <?php elseif ($c1_stand === 'offen' && (int) ($c1_z['von'] ?? 0) === $c1_uid) : ?>
                        <form method="post" action="<?php echo esc_url($c1_ziel); ?>" style="margin:0;">
                            <input type="hidden" name="action" value="cluster_one_post_dolly_vorschlag">
                            <input type="hidden" name="c1_tun" value="zurueckziehen">
                            <input type="hidden" name="c1_id" value="<?php echo esc_attr($c1_id); ?>">
                            <?php wp_nonce_field('cluster_one_post_dolly_vorschlag_zurueckziehen_' . $c1_id); ?>
                            <?php /* 0.3.66 (31.10.2026, Nemesus: „Ja, Symbol"): ↶ undo statt Textknopf Withdraw – das Mitglied
                                     nimmt seinen Vorschlag zurueck. Wort in title und aria-label. */ ?>
                            <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;vertical-align:middle;" title="<?php echo esc_attr__('Withdraw', 'cluster-one-post-dolly'); ?>" aria-label="<?php echo esc_attr__('Withdraw', 'cluster-one-post-dolly'); ?>">
                                <span class="dashicons dashicons-undo" aria-hidden="true" style="color:#8a8a8a;font-size:18px;width:18px;height:18px;"></span>
                            </button>
                        </form>
                    <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php
}


/* ---------------------------------------------------------------------------
   DIE EINE LISTE MEINER SEITEN — 18.09.2026, RUNDBRIEF 21.

   Sie hat DREI Leser und ist EINE Wahrheit (Weg D):

       die Reiterleiste           cluster_one_post_dolly_reiter()
       die Anmeldung beider       add_action('admin_menu', …)
       die Anspruchsmeldung       add_filter('cluster_one_seite_ansprueche', …)

   Wer hier eine zweite Liste tippt, hat zwei Wahrheiten ueber seine eigenen
   Seiten — und die zweite veraltet, sobald ein Reiter dazukommt. Genau das
   ist Kleinrock mit sechs Seiten passiert, nur ohne zweite Liste: er hatte
   keine und die Anmeldung kannte nur die erste.

   DIE REIHENFOLGE IST DIE DER LEISTE. Sie steht hier und nirgends sonst; das
   Feld 'nr' im Lader ordnet PRODUKTE, nicht Reiter.

   WARUM DER ZWEITE SLUG `…_tabelle` HEISST UND NICHT `…_zitate`, obwohl
   „Zitate" das naheliegende Wort ist:

       `cluster_one_post_dolly_zitate` IST SCHON VERGEBEN — als Funktion
       (:189, die gespeicherten Zitate) und, schlimmer, als DER RIEGELNAME
       DIESER DATEI (:137).

   Mein erster Entwurf hat den Slug so genannt, und mein eigener Zeuge hat es
   an der zweiten Zeile gefangen. Bericht 40 §3 hatte einen Tag vorher genau
   diese Sorte gemeldet — zwei Ebenen, eine Schreibweise —, und ich haette
   dabei eine dritte gebaut.

       Ein Slug, der wie der Riegel dieser Datei heisst, schickt jeden, der
       ihn in der Adresszeile liest und danach sucht, an den Riegel.

   Umbenannt wurde der SLUG und nicht der Riegel: Riegelnamen werden hier
   nicht umbenannt (P1-11, s. :1291), und dieser Slug hat noch nie in einer
   Adresszeile gestanden — es gibt kein Lesezeichen, das er brechen koennte.
   --------------------------------------------------------------------------- */
if (!function_exists('cluster_one_post_dolly_seiten')) {
function cluster_one_post_dolly_seiten(): array {
    return array(
        /* ZUG 0.3.31, 13.10.2026 — DIE ZWEI ZEILEN SIND GETAUSCHT.
           Vorher stand „Settings" oben und damit links.

           WARUM DIE REIHENFOLGE HIER UND NUR HIER STEHT: diese Liste hat
           drei Leser, und genau einer liest die Reihenfolge — die
           Reiterleiste (:1604, foreach). Die Anmeldung (:1762) waehlt den
           Rueckruf ueber `$c1_slug === 'cluster_one_post_dolly'`, und der
           Anspruch (:1802) fragt `array_key_exists`. Beide am NAMEN.

               Wer ein Feld an seiner Position nimmt statt an seinem Namen,
               hat die Reihenfolge zur Wahrheit erklaert.  (menue.php)

           GEMESSEN UND NICHT GELESEN: `zeuge-die-reiterfolge.php` fuehrt
           diese Liste in BEIDEN Reihenfolgen, in je einem Prozess, und
           stellt nebeneinander, was dabei herauskommt. Die zwei
           ZUORDNUNGEN (Slug -> Rueckruf, Slug -> Anspruch) sind in beiden
           identisch; die fuenf Reihenfolgen drehen sich alle mit.

           UND DIE ZWEITE HAELFTE LIEGT NICHT HIER: der Eingang in das
           Produkt steht im Lader (`--seite=`), nicht in dieser Datei.
           Wird nur hier getauscht, landet jeder, der „Post Dolly" im
           Menue anklickt, auf dem RECHTEN Reiter.

               Die Reihenfolge der Reiter und der Eingang in das Produkt
               sind zwei Angaben an zwei Orten. Wer nur eine bewegt,
               bekommt ein Produkt, dessen Eingang hinten liegt — und es
               meldet sich nichts, weil beide Angaben fuer sich genommen
               richtig sind.                                 (R-REITER-2)

           Diese Haelfte ist meine. Die andere ist Holleriths:
               floss.php --seite=cluster_one_post_dolly_tabelle
           Beide zusammen oder gar nicht. B7 dieses Zeugen belegt, dass
           diese Datei keinen Eingang als festen Text nennt — die Leiste
           baut ihre Adressen aus genau dieser Liste. */
        'cluster_one_post_dolly_tabelle' => __('Quotes', 'cluster-one-post-dolly'),
        'cluster_one_post_dolly'         => __('Settings', 'cluster-one-post-dolly'),
    );
}
}

/** Die Reiterleiste. Der Boden zeichnet sie, ich sage nur, was draufsteht.
 *
 * GERIEGELT UND MIT BENANNTEM RUECKFALL (R-LEIHE-1): cluster_one_render_tabs()
 * wohnt in kern.php, also im Boden, also heute immer da. „Heute immer da" ist
 * kein Grund, den Riegel zu lassen; es ist der Grund, warum er nie zufaellt.
 * Faellt er doch, gibt es keine stille Luecke: dann stehen die beiden Reiter
 * als schlichte Verweise da. Ein Mensch, der auf dem zweiten Reiter landet
 * und keinen Weg zurueck sieht, ist schlechter dran als einer, der einen
 * unschoenen sieht.
 *
 * KEIN eigener Riegel um die Funktion (H67 hat die Datei schon) — aber einer
 * um den fremden RUF, und das sind zwei verschiedene Dinge. */
if (!function_exists('cluster_one_post_dolly_reiter')) {
function cluster_one_post_dolly_reiter(string $c1_jetzt): void {
    $c1_reiter = array();
    foreach (cluster_one_post_dolly_seiten() as $c1_slug => $c1_wort) {
        $c1_reiter[] = array(
            'label'  => $c1_wort,
            'url'    => admin_url('admin.php?page=' . $c1_slug),
            'active' => $c1_slug === $c1_jetzt,
        );
    }
    if (function_exists('cluster_one_render_tabs')) {
        cluster_one_render_tabs($c1_reiter);
        return;
    }
    echo '<p>';
    foreach ($c1_reiter as $c1_t) {
        if (!empty($c1_t['active'])) {
            echo '<strong>' . esc_html($c1_t['label']) . '</strong> ';
            continue;
        }
        echo '<a href="' . esc_url($c1_t['url']) . '">' . esc_html($c1_t['label']) . '</a> ';
    }
    echo '</p>';
}
}

/* ---------------------------------------------------------------------------
   POST DOLLYS EIGENE SEITE — 16.09.2026, NACHTRAG ZU PROMPT-HOLLERITH-11.

   Bis heute war dieses Formular ein ABSCHNITT auf Kranz' Setup-Seite. Es
   hatte damit keinen eigenen Slug, und auf die Frage „welchen Slug bekommt
   Post Dolly" lautete die gemessene Antwort: keinen.

       Eine Seite ohne Slug ist keine Seite ohne Inhalt. Sie ist ein
       Abschnitt, der bei jemand anderem wohnt.              (Mammut)

   Jetzt wohnt sie bei sich. Der Abschnitt selbst ist unveraendert — sein
   eigener Kopf sagt „HIER BEGINNT MEIN EIGENES FORMULAR, UND ES UMSCHLIESST
   JEDES MEINER FELDER", er braucht Kranz' Rahmen also nicht.

   DIE CAPABILITY IST DIESELBE WIE VORHER: 'cluster_one_kranz_save'.

       Eine Seite, die ein Formular zeigt, dessen Speichern eine Capability
       verlangt, muss dieselbe VORHER pruefen. Sonst sieht jemand ein
       Formular, das er nicht abschicken darf — oder schlimmer, er darf doch.

   Sie steht an drei Stellen und ist an allen dreien dasselbe Wort:
       hier                        die Seite selbst
       :option_page_capability…    das Speichern ueber options.php
       kranz.php                   wo der Abschnitt frueher wohnte

   DIE SEITE IST VERSTECKT ANGEMELDET (Elternteil null), und das ist kein
   Widerspruch zum Menuepunkt: den Punkt setzt das DACH aus der Anmeldung
   heraus (cluster_one_menue_produkteintraege), und es setzt ihn NUR, wenn
   cluster_one_menue_seite_da() die Seite findet. Eine Seite, die sich selbst
   sichtbar anmeldet, bekaeme ihren Punkt zweimal — einmal von sich und
   einmal vom Dach.
       Wer seinen eigenen Punkt setzt UND sich anmeldet, steht zweimal da.

   DER NAME cluster_one_settings_seite_post_dolly() BLEIBT, obwohl er nach
   dem Umzug „Settings-Seite" sagt und es keine mehr ist. Er ist ein
   Riegelname (:710) — und Riegelnamen werden hier nicht umbenannt (P1-11).
   Der Name ist ab jetzt Geschichte, so wie die v1.9.14-Etiketten.

   WAS UNBERUEHRT BLEIBT: das Dashboard-Feld (wp_dashboard_setup, Kennung
   'cluster_one_dolly') und der Shortcode [cluster_one_dolly]. Beides ist
   kein Formular und kein zweiter Schreibweg.
   --------------------------------------------------------------------------- */
if (!function_exists('cluster_one_post_dolly_seite')) {
function cluster_one_post_dolly_seite(): void {
    /* DIE PRUEFUNG STEHT VOR DER AUSGABE und nicht daneben. Sie wiederholt,
       was add_submenu_page() schon verlangt hat — absichtlich: eine Seite,
       die sich darauf verlaesst, dass ihr Aufrufer geprueft hat, ist eine
       Seite, die beim naechsten Aufrufer ungeprueft ist. */
    /* 31.10.2026 (Neo), 0.3.59, RUNDE 36: Hier stand
           if (!current_user_can('cluster_one_kranz_save')) {
               wp_die(esc_html__('You are not allowed to edit these settings.', 'cluster-one-post-dolly'), 403);
           }
       Die Seite verlangt jetzt die Mitgliedschaft; wer speichern darf, entscheidet
       cluster_one_post_dolly_darf_speichern() beim Zeichnen, und options.php fragt
       weiter 'cluster_one_kranz_save'. Der Satz oben („…muss dieselbe VORHER
       pruefen") gilt weiter: ein Mitglied bekommt kein Formular, das es nicht
       abschicken darf, sondern die Werte als Text. */
    if (!current_user_can('cluster_one_access')) {
        wp_die(esc_html__('Not permitted: this page is for members of the node.', 'cluster-one-post-dolly'), '', 403);
    }
    echo '<div class="wrap">';
    /* DIE UEBERSCHRIFT GEHT UEBER cluster_one_page_h1(), wie ueber jeder Seite
       dieses Hauses — eine Seite, die sie umgeht, sieht anders aus als alle
       anderen. Sie nimmt einen ORGANNAMEN und GIBT eine Zeichenkette ZURUECK;
       sie gibt nichts selbst aus (kern.php :2683). Mein erster Entwurf hat sie
       wie eine ausgebende Funktion mit einem Titel gerufen, und php -l haette
       das nie gemeldet: die Signatur passte, der Sinn nicht.

           Ein Aufruf, der die richtige Zahl Argumente hat, ist damit noch
           kein richtiger Aufruf.

       UND SIE WIRD NICHT ESCAPED. Mein zweiter Entwurf hat esc_html() darum
       gelegt, und das waere genauso falsch gewesen wie der erste: sie gibt
       FERTIGES HTML zurueck — esc_html(Titel) plus die Hilfeblase von
       cluster_one_help() (kern.php :2710). Ein zweites Escapen haette die
       Blase als Text auf die Seite gedruckt. Alle anderen Aufrufer im Haus
       machen es ebenso: <h1><?php echo cluster_one_page_h1('Kranz'); ?></h1>.

           Wer eine Zeichenkette escapet, ohne zu wissen, wer sie gebaut hat,
           schuetzt manchmal und zerstoert manchmal.

       DER RUECKFALL ist noetig, weil diese Funktion in kern.php wohnt und
       kern Boden ist — heute also immer da. „Heute immer da" ist kein Grund,
       auf den Riegel zu verzichten; es ist der Grund, warum er nie zufaellt. */
    if (function_exists('cluster_one_page_h1')) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase); siehe kern.php :2710
        echo '<h1>' . cluster_one_page_h1('Post Dolly') . '</h1>';
        /* 0.3.50 (27.10.2026, Neo): HIER STAND der Stempel – Fassung, Werft, Boden-Stand:
               if (function_exists('cluster_one_stempel')) { echo cluster_one_stempel(__FILE__, …); }
           0.3.53 (28.10.2026, Neo): umgezogen auf die Zitate-Seite (cluster_one_post_dolly_zitate_seite).
           Nemesus: „Er müsste in den Tab Zitate, ist aber im Tab Einstellungen." Die Zitate sind die
           erste Seite des Hauses (der Reiter vorn), die Einstellungen die zweite; der Stempel steht
           dort, wo man ankommt. */
    } else {
        echo '<h1>' . esc_html__('Post Dolly', 'cluster-one-post-dolly') . '</h1>';
    }
    cluster_one_post_dolly_reiter('cluster_one_post_dolly');
    cluster_one_settings_seite_post_dolly();
    echo '</div>';
}
}

/** Die zweite Seite: der Reiter „Zitate".
 *
 * DIESELBE PRUEFUNG WIE DIE ERSTE, und aus demselben Grund: eine Seite, die
 * sich darauf verlaesst, dass ihr Aufrufer geprueft hat, ist beim naechsten
 * Aufrufer ungeprueft. Die Capability ist an allen vier Stellen dasselbe Wort
 * — hier, an der Anmeldung, an option_page_capability… und in kranz.php. */
if (!function_exists('cluster_one_post_dolly_zitate_seite')) {
function cluster_one_post_dolly_zitate_seite(): void {
    /* 31.10.2026 (Neo), 0.3.59: Mitgliedschaft statt 'cluster_one_kranz_save', wie auf
       der ersten Seite (dort steht der alte Wortlaut). */
    if (!current_user_can('cluster_one_access')) {
        wp_die(esc_html__('Not permitted: this page is for members of the node.', 'cluster-one-post-dolly'), '', 403);
    }
    echo '<div class="wrap">';
    /* DIESELBE UEBERSCHRIFT WIE AUF DEM ERSTEN REITER, und das ist Absicht:
       cluster_one_page_h1() nimmt das ORGANWORT und macht daraus den Titel
       des PRODUKTS plus die Hilfeblase des Organs (VERTRAG 29 §4 Punkt 6).
       Zwei Reiter sind zwei Seiten EINES Produkts — ein zweiter Titel waere
       ein zweites Produkt. Welcher Reiter laeuft, sagt die Leiste, nicht
       die Ueberschrift.
           Ein Reiter sagt, WO man ist. Eine Ueberschrift sagt, WESSEN
           Haus es ist. */
    if (function_exists('cluster_one_page_h1')) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase); siehe kern.php :2710
        echo '<h1>' . cluster_one_page_h1('Post Dolly') . '</h1>';
        /* 0.3.53 (28.10.2026, Neo): der Stempel – Fassung, Werft, Boden-Stand – steht jetzt hier,
           auf der Zitate-Seite (bis 0.3.52 auf der Einstellungen-Seite, siehe dort). */
        if (function_exists('cluster_one_stempel')) { echo cluster_one_stempel(__FILE__, defined('CLUSTER_ONE_POST_DOLLY_VERSION') ? (string) CLUSTER_ONE_POST_DOLLY_VERSION : ''); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapt in der Funktion
    } else {
        echo '<h1>' . esc_html__('Post Dolly', 'cluster-one-post-dolly') . '</h1>';
    }
    cluster_one_post_dolly_reiter('cluster_one_post_dolly_tabelle');
    cluster_one_post_dolly_vorschlag_meldung(); // 0.3.59: die Antwort auf einen Vorschlags-Knopf, falls einer gedrueckt wurde
    cluster_one_post_dolly_zitate_teil();
    echo '</div>';
}
}

/* ---------------------------------------------------------------------------
   DIE ANMELDUNG BEIDER SEITEN — AUS DER EINEN LISTE, NICHT AUS ZWEI.

   RUNDBRIEF 21 §2: „Wer hier eine zweite Liste tippt, hat zwei Wahrheiten
   ueber seine eigenen Seiten." Also liest DIESE Stelle dieselbe Liste wie die
   Reiterleiste und wie die Anspruchsmeldung. Drei Leser, eine Wahrheit
   (Weg D).

   DIE RUECKRUFE STEHEN ALS ZEICHENKETTE (H218) und werden VORHER gefragt —
   dieselbe Form, die menue.php :659 seit jeher an genau einer Stelle hat und
   die NACHTRAG 28b fuer alle 24 verlangt: nicht „gibt es die Seite", sondern
   „gibt es die HAND, die sie zeichnet". Eine Seite ohne Hand wird nicht
   angemeldet; siebzehn davon liegen in Crockford, und keine davon soll hier
   dazukommen.
   --------------------------------------------------------------------------- */
add_action('admin_menu', function () {
    foreach (cluster_one_post_dolly_seiten() as $c1_slug => $c1_wort) {
        $c1_ruf = $c1_slug === 'cluster_one_post_dolly'
            ? 'cluster_one_post_dolly_seite'
            : 'cluster_one_post_dolly_zitate_seite';
        if (!function_exists($c1_ruf)) { continue; }
        add_submenu_page(
            null,
            __('Post Dolly', 'cluster-one-post-dolly'),
            __('Post Dolly', 'cluster-one-post-dolly'),
            'cluster_one_access', // 0.3.59 (31.10.2026, Neo): bis dahin 'cluster_one_kranz_save', siehe cluster_one_post_dolly_seite()
            $c1_slug,
            $c1_ruf
        );
    }
});

/* ---------------------------------------------------------------------------
   §2 — MEINE SEITEN MELDEN. RUNDBRIEF 21, 18.09.2026.

   Christians Befund war Kleinrocks: seine Anmeldung nennt EINEN Slug, seine
   Reiterleiste fuehrt SECHS Seiten, fuenf fielen auf „Cluster One".

       Ein Produkt ist keine Seite. Ein Produkt ist eine Reiterleiste.

   BEI MIR WAREN ES BIS HEUTE EINS UND EINS, und darum haette mein Haus den
   Fehler nie gezeigt. Mit dem zweiten Reiter habe ich ihn ab jetzt auch —
   er waere am Tag von Christians Wunsch entstanden, nicht gefunden worden.

       Eine Reparatur, die vor dem Fehler eintrifft, sieht wie keine aus.

   ANGEHAENGT, NICHT ZUGEWIESEN (R-SAMMLER-1), und der Wirt wird ERFRAGT und
   nicht getippt — dieselbe Form wie beim Raeumer, aus demselben Grund: der
   Wirt sagt, in welchem PAKET diese Datei liegt, und das kann sie sich nur
   selbst beschaffen.

   KEINE ZWEITE LISTE: gefragt wird cluster_one_post_dolly_seiten(), dieselbe
   Quelle wie Reiterleiste und Anmeldung.
   --------------------------------------------------------------------------- */
add_filter('cluster_one_seite_ansprueche', function ($c1_a, $c1_seite) {
    if (!is_array($c1_a)) { return $c1_a; }
    if (!array_key_exists((string) $c1_seite, cluster_one_post_dolly_seiten())) { return $c1_a; }
    $c1_a[] = cluster_one_kasper_wirt(__FILE__);
    return $c1_a;
}, 10, 2);

/* 28.10.2026 (Neo), Post Dolly 0.3.52 — MEINE LEISTE AUF ZURUF. Seit Boden 1.18.0
   zeichnen die Knoten-Seiten oben die Leiste des Hauses, von dem der Leser kam,
   darunter die Knoten-Reihe (Form des Kalenders, cluster_one_faust_leisten). Der
   Boden ruft mit dem Wirt, der gemeint ist; ich vergleiche und zeichne nur dann.
   Den Reiter „Knoten" faerbt der Boden dort selbst. */
add_action('cluster_one_leiste', function ($c1_w, $c1_s) {
    if (!function_exists('cluster_one_kasper_wirt')) { return; }
    if ($c1_w !== cluster_one_kasper_wirt(__FILE__)) { return; }
    cluster_one_post_dolly_reiter((string) $c1_s);
}, 10, 2);
}
