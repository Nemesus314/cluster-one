<?php
/**
 * Cluster One — Modul umgebung.
 *
 * BIS v0.9.69 HIESS DIESE DATEI spiegel.php, UND DER NAME LOG.
 *
 * Darin lagen zwei Dinge, die nichts miteinander zu tun haben:
 *
 *   DER SPIEGEL   — ein Diagnose-Dump der Plugin-Optionen. Zwei Aufrufer,
 *                   beide in kranz.php. Ausgezogen am 24.08.2026 nach
 *                   Turing, Reiter 2. Ein Diagnose-Ausgang ist ein Werkzeug
 *                   und kein Organ; in einem ausgelieferten Plugin hat er
 *                   nichts verloren.
 *
 *   DIE UMGEBUNG  — Randbedingungen, die Begruendung des SysOps dazu, ihr
 *                   Fingerabdruck und die Uebergabe an einen Raum. Zwoelf
 *                   Aufrufer, davon acht in Bismarck. Das ist Produkt.
 *
 * Gemessen, bevor geschnitten wurde: cluster_one_spiegel_dump() hat
 * cluster_one_spiegel_umgebung() NIE aufgerufen. Die beiden teilten sich
 * einen Dateinamen und ein Praefix, sonst nichts.
 *
 * DESHALB HEISST HIER JETZT AUCH KEINE FUNKTION MEHR 'spiegel':
 * cluster_one_spiegel_umgebung() ist cluster_one_umgebung_daten().
 *
 * Was hier steht, ist der Keim von Umberto (Backlog H28): die Hausregeln mit
 * einem Fingerabdruck, der sagt, wenn sie nicht mehr passen. Nicht entworfen,
 * sondern seit v0.2.2 in Betrieb.
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
if (!function_exists('cluster_one_umgebung_daten')) {

cluster_one_hier('umgebung');   // v0.9.72: Kasper

/* --- v0.2.1: RANDBEDINGUNGEN -------------------------------------------------
   why.md sagt, WARUM etwas so gebaut ist. Das hier sagt, WORAUF es läuft.
   Zwei Dokumente mit ENTGEGENGESETZTEN Offenlegungsregeln, und die Verwechslung
   liegt nahe, weil beide "Erklärung für die KI" heißen:

     why.md          — absichtlich öffentlich (Seite, Route, Präambel)
     Randbedingungen — NIEMALS öffentlich, nur auf ausdrückliche Übergabe

   Der Grund für das Verbot: Eine vollständige Plugin-Liste mit Versionen ist
   ein Fingerabdruck für Angriffe. Wer weiß, welche Sicherheits-Plugins in
   welcher Version laufen, weiß, welche bekannten Lücken er zuerst probiert.
   Deshalb geht das hier NICHT über cluster-one/v1/why und NICHT über die
   normale Präambel, sondern nur über einen bewussten, befristeten Akt des
   SysOps, der sich im Raum ansagt.

   Warum es überhaupt gebraucht wird: Eine KI soll Änderungen vorschlagen und
   kennt den Bauplan, aber nicht den Baugrund. Drei Beispiele, die niemand
   raten kann: 'max input vars' ist bei Bricks kein Standardwert, wer ihn senkt
   zerlegt den Builder. Liegt die Seite in einem Unterverzeichnis, ist jeder
   Vorschlag zu Routen und Rewrites ohne diese Zeile falsch. Und ein
   Cache-Plugin entscheidet darüber, ob ein Cache-Buster überhaupt greift. */

/* --- v1.4.0: 'plugins_bekannt' — LEER IST NICHT UNBEKANNT --------------------
   Roter Zeuge zeuge-fingerabdruck.php, Fall 2, und die Frage aus Teil E des
   Umschlags.

   get_plugins() und is_plugin_active() wohnen in wp-admin/includes/plugin.php
   und sind NICHT ueberall geladen. Der Code hat das immer schon mit
   function_exists() geprueft und sonst eine leere Liste geliefert. Das ist
   defensiv richtig und in der Uebergabe falsch:

       EIN TEXT, DER STILL OHNE PLUGIN-LISTE AUSGELIEFERT WIRD,
       SIEHT AUS WIE EIN KNOTEN OHNE PLUGINS.

   Eine KI liest daraus etwas Falsches, und zwar selbstbewusst — dieselbe
   Gefahr wie bei der veralteten Notiz, nur eine Ebene tiefer: nicht ein Grund,
   der nicht mehr stimmt, sondern eine Tatsache, die es nie gab.

   Der Unterschied wird deshalb MITGEFUEHRT, nicht erschlossen. Wer die Daten
   bekommt, sieht 'plugins_bekannt' => false und weiss, dass die leere Liste
   eine fehlende Auskunft ist und kein Befund.

       Ein Feld, das leer sein darf, muss den Unterschied zwischen leer und
       unbekannt kennen.

   BENANNTE GRENZE: Dieses Feld sagt, ob die WordPress-Funktion ANTWORTEN
   konnte — nicht, ob ihre Antwort vollstaendig ist. Ein Filter auf
   'all_plugins' bleibt unsichtbar. Wir messen die Auskunftsfaehigkeit, nicht
   die Wahrheit. */
function cluster_one_umgebung_daten(): array {
    $theme  = function_exists('wp_get_theme') ? wp_get_theme() : null;
    $eltern = ($theme && method_exists($theme, 'parent')) ? $theme->parent() : null;

    $c1_kann = function_exists('get_plugins') && function_exists('is_plugin_active');

    $plugins = array();
    if ($c1_kann) {
        foreach (get_plugins() as $datei => $d) {
            if (is_plugin_active($datei)) {
                $plugins[] = array(
                    'name'    => (string) ($d['Name'] ?? $datei),
                    'version' => (string) ($d['Version'] ?? ''),
                    'author'  => wp_strip_all_tags((string) ($d['Author'] ?? '')),
                );
            }
        }
    }

    return array(
        'hinweis' => 'Randbedingungen dieses Knotens. NICHT oeffentlich: die Versionsliste ist ein Angriffs-Fingerabdruck.',
        'theme'   => array(
            'name'    => $theme ? (string) $theme->get('Name') : '',
            'version' => $theme ? (string) $theme->get('Version') : '',
            'eltern'  => $eltern ? (string) $eltern->get('Name') : '',
            'eltern_version' => $eltern ? (string) $eltern->get('Version') : '',
        ),
        'wordpress' => array(
            'version'     => function_exists('get_bloginfo') ? get_bloginfo('version') : '',
            'home'        => function_exists('home_url') ? home_url('/') : '',
            'rest_prefix' => function_exists('rest_get_url_prefix') ? rest_get_url_prefix() : '',
            'locale'      => function_exists('get_locale') ? get_locale() : '',
            'multisite'   => function_exists('is_multisite') ? (is_multisite() ? 'ja' : 'nein') : '',
            'debug'       => (defined('WP_DEBUG') && WP_DEBUG) ? 'an' : 'aus',
            'memory'      => defined('WP_MEMORY_LIMIT') ? (string) WP_MEMORY_LIMIT : '',
        ),
        'php' => array(
            'version'         => PHP_VERSION,
            'memory_limit'    => (string) ini_get('memory_limit'),
            'max_execution'   => (string) ini_get('max_execution_time'),
            'post_max_size'   => (string) ini_get('post_max_size'),
            'upload_max'      => (string) ini_get('upload_max_filesize'),
            'max_input_vars'  => (string) ini_get('max_input_vars'), // bei Bricks KEIN Standardwert
        ),
        'plugins'         => $plugins,
        'plugins_bekannt' => $c1_kann,   // v1.4.0
    );
}

/* --- v1.4.0: WAS UEBERGEBEN WIRD, WIRD BEWACHT -------------------------------
   Diese Funktion ist die EINE Quelle dafuer, welche Baugrundwerte als
   Randbedingung gelten. Der Fingerabdruck hasht sie, und der Zeuge prueft, dass
   jeder Wert, der im uebergebenen Text steht, auch hier auftaucht.

   WARUM SIE EXISTIERT: Bis v1.3.6 hat der Fingerabdruck nur Plugins und Theme
   gehasht, waehrend der Text zusaetzlich PHP-Fassung, memory_limit,
   max_execution, upload_max, max_input_vars und fuenf WordPress-Angaben
   uebergab. Der Abdruck bewachte einen echten Teilbereich dessen, was er zu
   bewachen versprach — die Notiz sagte "ich passe noch" und meinte die Haelfte.

   Der Fall ist nicht theoretisch. Der Kommentar oben nennt ihn selbst:
   max_input_vars ist bei Bricks kein Standardwert. Genau darueber schreibt ein
   SysOp seine Notiz, und genau darauf hat der Abdruck nicht reagiert.

       Ein Grund, der nicht mehr stimmt, ist schlimmer als gar keiner —
       und ein Waechter, der die Haelfte bewacht, sagt das nicht dazu.

   WARUM NICHT ALLES: Fassungsnummern werden auf major.minor gekuerzt. Ein
   Hoster, der PHP 8.3.1 auf 8.3.2 hebt, hat keine Randbedingung geaendert; wer
   die Notiz deswegen jede Woche als veraltet markiert, bringt Christian bei,
   die Warnung zu ueberlesen. Eine Warnung, die zu oft kommt, ist keine.

   Ausgelassen sind 'debug' (ein Schalter, kein Baugrund) und 'author' je
   Plugin (aendert sich mit dem Impressum, nicht mit dem Verhalten). */
function cluster_one_umgebung_bewachte_werte(): array {
    $u = cluster_one_umgebung_daten();

    /** major.minor — der Rest ist Rauschen. */
    $kurz = static function (string $v): string {
        if ($v === '') { return ''; }
        $t = explode('.', preg_replace('~[^0-9.].*$~', '', $v));
        return isset($t[1]) ? $t[0] . '.' . $t[1] : (string) $t[0];
    };

    $w = array();

    /* Die Plugin-Liste — oder ausdruecklich ihre Abwesenheit. Ohne diese
       Unterscheidung haben zwei stille Kontexte denselben Abdruck, und die
       Notiz schweigt zu jeder Aenderung (roter Zeuge, Fall 2).

       v1.9.8 — DER NAME IST RAUS, UND DAS IST EIN VERZICHT MIT GRUND.

       Hier stand `$p['name'] . '@' . $p['version']`. Gemessen am 03.09.: ein
       Knoten, an dem sich NICHTS aendert ausser der Zeichenkette
       „Cluster One: Umberto" -> „Cluster One: Crockford", bekommt einen
       anderen Abdruck. Jede gespeicherte Begruendung meldet sich dann als
       veraltet, und der uebergebene Text sagt einer fremden KI:

           WARNING … it was written for DIFFERENT ground conditions

       Nach einer reinen Umbenennung ist dieser Satz nicht wahr.

           Ein Fingerabdruck, der das Etikett mitbewacht, kann eine
           Umbenennung nicht von einem Umbau unterscheiden. Er meldet beide
           Male dasselbe, und beim einen luegt er.

       Der Zug faehrt vor Crockford voraus (Vertrag 18), damit danach kein
       Knoten vor einem Umbau warnt, den es nicht gab.

       WAS DER ABDRUCK DAMIT VERLIERT — und das gehoert gesagt, nicht
       verschwiegen: Er kann einen TAUSCH nicht mehr sehen. Wird ein Plugin
       durch ein anderes mit derselben Fassungsnummer ersetzt, bleibt die
       sortierte Liste der Fassungen gleich, und die Notiz schweigt zu einer
       Aenderung, ueber die sie redet. Das ist eine echte Luecke, sie ist
       schmal, und sie ist der Preis.

       WARUM NICHT DER PFAD STATT DES NAMENS: `cluster-one-umberto/…` ist der
       Schluessel von get_plugins() und waere der stabilere Bezeichner — nur
       aendert die Umbenennung ihn GENAUSO. Es gibt in einem Plugin-Kopf keine
       Kennung, die eine Umbenennung ueberlebt. Wer eine sucht, sucht etwas,
       das WordPress nicht hat.

       Die Zahl der Plugins bleibt bewacht: ein Plugin dazu oder weg aendert
       die Liste weiterhin. Nur WIE sie heissen, steht nicht mehr darin. */
    if (!$u['plugins_bekannt']) {
        $w[] = 'plugins:UNBEKANNT';
    } else {
        $teile = array();
        foreach ($u['plugins'] as $p) { $teile[] = (string) $p['version']; }
        sort($teile);
        $w[] = 'plugins:' . count($teile) . ':' . implode('|', $teile);
    }

    $w[] = 'theme:'  . $u['theme']['name']   . '@' . $kurz($u['theme']['version']);
    $w[] = 'eltern:' . $u['theme']['eltern'] . '@' . $kurz($u['theme']['eltern_version']);

    $w[] = 'wp:'          . $kurz($u['wordpress']['version']);
    $w[] = 'home:'        . $u['wordpress']['home'];
    $w[] = 'rest_prefix:' . $u['wordpress']['rest_prefix'];
    $w[] = 'locale:'      . $u['wordpress']['locale'];
    $w[] = 'multisite:'   . $u['wordpress']['multisite'];

    $w[] = 'php:'            . $kurz($u['php']['version']);
    $w[] = 'memory_limit:'   . $u['php']['memory_limit'];
    $w[] = 'max_execution:'  . $u['php']['max_execution'];
    $w[] = 'upload_max:'     . $u['php']['upload_max'];
    $w[] = 'max_input_vars:' . $u['php']['max_input_vars'];

    return $w;
}

/* --- v0.2.2: DIE BEGRUENDUNG ZUR ZUSAMMENSTELLUNG -----------------------------
   0.2.1 sammelt, WAS installiert ist. Es kann unmoeglich wissen, WARUM. Und
   ohne das Warum richtet die Liste Schaden an: Wer zwei Caching-Plugins sieht,
   schlaegt vor sie zusammenzulegen — und weiss nicht, dass eines die Datenbank
   und eines den Arbeitsspeicher bedient und beide ausserhalb ihres Fachs
   schlechter abschneiden. Wer eine Basic-Version sieht, schlaegt das Abo vor —
   und weiss nicht, dass die fehlende Faehigkeit durch eigenen Code geheilt ist.
   Wer zwei Dateimanager sieht, will einen wegwerfen — womoeglich den, der als
   einziger Ordner anlegen kann.

   Gruende lassen sich nicht einsammeln. Die schreibt der SysOp.

   ABER: Ein Grund, der nicht mehr stimmt, ist SCHLIMMER als gar keiner — eine
   KI handelt darauf selbstbewusst, weil der Text verbindlich klingt. Deshalb
   merkt sich die Notiz einen Fingerabdruck der Zusammenstellung, zu der sie
   geschrieben wurde. Aendert sich die Liste, sagt sie das: bei der Anzeige und
   im uebergebenen Text. Dieselbe Regel wie beim Mesh-Punkt "veraltet" — lieber
   "ich weiss es nicht" als eine falsche Behauptung.

   v1.4.0 — DER ABDRUCK BEWACHT JETZT DEN GANZEN BAUGRUND, nicht nur Plugins
   und Theme. Die Zutaten stehen in cluster_one_umgebung_bewachte_werte().

   EINMALIGE WIRKUNG, DIE ANGESAGT GEHOERT: Jede heute gespeicherte Notiz
   traegt einen Abdruck der alten Zusammensetzung. Nach dieser Auslieferung
   melden sich deshalb ALLE Notizen einmal als veraltet. Das ist kein Fehler,
   sondern die ehrliche Auskunft: wir wissen nicht, ob die Notiz zu diesem
   Baugrund geschrieben wurde. Ein Klick auf "Still correct" setzt sie neu. */
function cluster_one_umgebung_fingerprint(): string {
    return substr(hash('sha256', implode("\n", cluster_one_umgebung_bewachte_werte())), 0, 16);
}

function cluster_one_get_umgebung_notiz(): array {
    $n = get_option('cluster_one_umgebung_notiz', array());
    return is_array($n) ? $n : array();
}

/** Speichern setzt den Fingerabdruck auf JETZT — Schreiben heisst bestaetigen. */
function cluster_one_save_umgebung_notiz(string $text): void {
    update_option('cluster_one_umgebung_notiz', array(
        'text'  => $text,
        'stand' => cluster_one_umgebung_fingerprint(),
        'ts'    => time(),
    ), false);
}

/** Hat sich die Zusammenstellung seit dem letzten Bestaetigen geaendert? */
function cluster_one_umgebung_notiz_veraltet(): bool {
    $n = cluster_one_get_umgebung_notiz();
    if (trim((string) ($n['text'] ?? '')) === '') {
        return false; // keine Notiz kann nicht veralten
    }
    return (string) ($n['stand'] ?? '') !== cluster_one_umgebung_fingerprint();
}

/* --- v1.4.0: DER SCHRUBBER ---------------------------------------------------
   Bis v1.3.6 ging dieser Text an einen Raum INNERHALB des Knotens. Ab jetzt
   traegt ihn ein Mensch hinaus, in ein fremdes Chatfenster (Ausfuhrtuer).

       Ein Text, der den Knoten in einem fremden Fenster verlaesst,
       kommt nicht zurueck.

   Die Bauform ist von Turing 3.1.1 abgeschaut, NICHT der Code — Turing ist ein
   fremdes Haus, das sich rueckstandslos entfernen lassen muss, und eine
   Funktion darf diese Grenze nicht ueberschreiten. Ein Muster darf.

   WO DAS LECK WAERE: Nicht in den gesammelten Daten — Pluginnamen und
   ini-Werte sind, was der Text absichtlich uebergibt, und der Hinweis reist
   mit. Das Leck ist DIE NOTIZ. Sie ist freier Text vom SysOp, und wer den
   Grund fuer ein Plugin aufschreibt, schreibt manchmal auch den Schluessel
   dazu ("SMTP laeuft ueber ... mit Key ...").

   Deshalb wird geschrubbt, was wie ein Geheimnis riecht, und nicht nur die
   Notiz — der Schrubber laeuft ueber den fertigen Text. Was hier durchgeht,
   ist nachher in einem fremden Fenster.

   BENANNTE GRENZE: Ein Schrubber nach Muster faengt, was einem Muster
   aehnelt. Er faengt kein Passwort, das wie ein Wort aussieht. Er ist die
   zweite Verteidigungslinie hinter dem Menschen, der die Notiz schreibt —
   nicht die erste. */
function cluster_one_umgebung_scrub(string $text): string {
    $muster = array(
        /* Mailadressen */
        '~[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}~',
        /* zugewiesene Geheimnisse: key = ..., token: ..., secret ... */
        '~\b(?:api[_-]?key|apikey|key|token|secret|passwor[dt]|passwd|pass|pwd|auth|bearer|nonce|salt)\b\s*[:=]\s*\S+~i',
        /* freistehende lange Zeichenketten, wie Schluessel sie haben */
        '~\b[A-Za-z0-9_\-]{32,}\b~',
        /* die ueblichen Praefixe von Anbieterschluesseln */
        '~\b(?:sk|pk|rk|ghp|gho|github_pat|xox[abps])[-_][A-Za-z0-9_\-]{8,}~i',
    );
    return (string) preg_replace($muster, '***', $text);
}

/* --- v1.4.0: DIE SELBSTPRUEFUNG REIST MIT -----------------------------------
   Turings Satz dazu: "Eine Pruefung, die man extra aufrufen muss, ruft niemand
   auf." Also wird der Schrubber bei JEDER Ausgabe mitgeprueft, nicht daneben.

   Und die Fehlerrichtung ist gewaehlt: ist der Schrubber rot, wird der Text
   NICHT ausgegeben. Nicht mit Warnung — gar nicht. Ein Text, der schon
   unterwegs ist, laesst sich nicht zurueckholen.

   NACHGETRAGEN — DIE HERKUNFT DER PROBEN, DIE SEIT DEM 31.08. FEHLTE.

   Hier stand: "Jede dieser Zeilen war einmal ein Leck, bei jemandem."

   Das ist nicht wahr, und es ist die schlimmere Sorte Unwahrheit: sie leiht
   der Selbstpruefung eine Autoritaet, die sie nicht hat. KEINE der fuenf
   Proben unten stammt von einem Knoten. Ich habe sie aufgeschrieben, nachdem
   ich den Schrubber geschrieben hatte, aus derselben Vorstellung davon, wie
   ein Geheimnis aussieht.

       Eine Selbstpruefung, deren Proben vom Autor des Geprueften stammen,
       misst die Uebereinstimmung zweier seiner Einfaelle — nicht die Welt.
       Sie kann nur finden, was er ohnehin bedacht hat.

   (Ich hatte der Werkbank "vier der fuenf" gemeldet. Nachgezaehlt sind es
   fuenf. Meine eigene Zahl war die freundlichere.)

   Was die Pruefung trotzdem wert ist, und deshalb bleibt sie: sie laeuft bei
   JEDER Ausgabe mit, und sie prueft in BEIDE Richtungen — der Schrubber muss
   schwaerzen, und er darf nicht alles schwaerzen. Ein Schrubber, der still
   kaputtgeht, waere der gefaehrlichste Zustand dieser Datei.

   Was ihr fehlt, ist Material von aussen. Wer je ein echtes Leck aus einer
   Notiz sieht, gehoert hierher — die Probe kostet eine Zeile.

   Die GEGENPROBE weiter unten hat als einzige eine echte Herkunft: sie ist
   der Form nach eine SysOp-Notiz, wie sie im Notizfeld dieser Seite steht. */
function cluster_one_umgebung_scrubber_heil(): bool {
    $proben = array(
        'contact sysop@example.com now',
        'api_key = AbCdEf0123456789AbCdEf0123456789xyz',
        'token: ghp_ABCDEFGHIJKLMNOPQRSTUVWXYZ012345',
        'the password is hunter2hunter2hunter2hunter2hunter2',
        'sk-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
    );
    foreach ($proben as $p) {
        $g = cluster_one_umgebung_scrub($p);
        if (strpos($g, '***') === false) { return false; }
    }
    /* Und die Gegenrichtung: ein harmloser Satz darf NICHT zerschrubbt werden.
       Ein Schrubber, der alles schwaerzt, ist genauso kaputt wie keiner — er
       macht den Text unbrauchbar und niemand merkt es. */
    $harmlos = 'Two caches on purpose: one serves the database, one the object cache.';
    return cluster_one_umgebung_scrub($harmlos) === $harmlos;
}

/** Die Randbedingungen als Text fuer eine Praeambel — knapp, damit sie nicht
   die halbe Anfrage auffrisst. Nur was fuer AENDERUNGEN zaehlt.

   v1.4.0 — ES GIBT GENAU EINEN TEXT, UND ER IST FUER BEIDE WEGE DERSELBE.
   Ein Raum in Bismarck und die Ausfuhrtuer bekommen byte-gleich dasselbe
   (P1-12: nie zwei Schreibwege auf eine Wahrheit — hier: nie zwei Texte auf
   eine Auskunft). Deshalb hat diese Funktion KEINEN Parameter 'zweck': ein
   Parameter, der den Text veraendert, ist der Anfang zweier Texte.

   Drei Dinge sind dazugekommen, alle drei gelten fuer BEIDE Wege:

     Fingerabdruck   damit eine KI zwei Uebergaben vergleichen kann
     Erzeugungsdatum eine KI, die den Text drei Wochen spaeter vorgelegt
                     bekommt, muss das wissen koennen
     'unbekannt'     leer ist nicht dasselbe wie keine Plugins

   Die Schlusszeile hiess bis v1.3.6 "Do not repeat it outside this room". Auf
   dem Weg durch die Ausfuhrtuer gibt es keinen Raum — der Satz waere dort
   falsch gewesen, und ein Satz, der auf einem von zwei Wegen luegt, haette
   zwei Texte erzwungen. Jetzt sagt er, was auf beiden Wegen gilt. */
function cluster_one_umgebung_als_text(): string {
    if (!cluster_one_umgebung_scrubber_heil()) {
        /* Kein Text ist besser als ein ungeschrubbter. Die Zeile ist selbst
           schrubberfrei und verraet nichts ueber den Knoten. */
        return 'Cluster One: the redaction self-check failed. No ground conditions were produced. This is deliberate — an unredacted text cannot be recalled once it has left.';
    }

    $u = cluster_one_umgebung_daten();
    $z = array();
    $z[] = 'Technical ground conditions of this node. Treat them as constraints, not as trivia — a suggestion that ignores them can break the site.';
    $z[] = sprintf('WordPress %s at %s (REST prefix "%s", locale %s, multisite %s).',
        $u['wordpress']['version'], $u['wordpress']['home'], $u['wordpress']['rest_prefix'],
        $u['wordpress']['locale'], $u['wordpress']['multisite']);
    if ($u['theme']['name'] !== '') {
        $z[] = sprintf('Theme: %s %s%s.', $u['theme']['name'], $u['theme']['version'],
            $u['theme']['eltern'] !== '' ? sprintf(' (child of %s %s)', $u['theme']['eltern'], $u['theme']['eltern_version']) : '');
    }
    $z[] = sprintf('PHP %s — memory %s, max execution %ss, upload %s, max_input_vars %s.',
        $u['php']['version'], $u['php']['memory_limit'], $u['php']['max_execution'],
        $u['php']['upload_max'], $u['php']['max_input_vars']);

    /* v1.4.0: leer, unbekannt und vorhanden sind DREI Faelle, nicht zwei. */
    if (!$u['plugins_bekannt']) {
        $z[] = 'Active plugins: UNKNOWN — this node could not be asked for its plugin list in the context that produced this text. Do NOT read this as "no plugins are installed"; treat the plugin situation as unreported.';
    } elseif (!$u['plugins']) {
        $z[] = 'Active plugins: none. The list was readable and it is empty.';
    } else {
        $liste = array();
        foreach ($u['plugins'] as $pl) { $liste[] = $pl['name'] . ' ' . $pl['version']; }
        $z[] = 'Active plugins: ' . implode(' · ', $liste);
    }

    // v0.2.2: die Begruendung des SysOps — und, falls noetig, die Warnung, dass
    // sie zu einer anderen Zusammenstellung geschrieben wurde.
    $n = cluster_one_get_umgebung_notiz();
    $notiz = trim((string) ($n['text'] ?? ''));
    if ($notiz !== '') {
        if (cluster_one_umgebung_notiz_veraltet()) {
            $z[] = 'WARNING about the reasoning below: it was written for DIFFERENT ground conditions than the ones listed above. Treat it as historical intent, not as current fact, and say so if you rely on it.';
        }
        $z[] = 'Why this particular set — written by the SysOp, not derived from the list:';
        $z[] = $notiz;
    }

    /* v1.4.0: Herkunft. Zwei Uebergaben mit demselben Abdruck beschreiben
       denselben Knoten; zwei mit verschiedenem nicht. Das kann eine KI
       nachrechnen, ohne irgendetwas ueber Cluster One zu wissen.

       v1.9.2 — H168: DIE UHR GING AN WORDPRESS VORBEI.

       Hier stand `gmdate('Y-m-d H:i')` ohne zweiten Parameter. Das liest die
       Systemuhr direkt, und Holleriths Pruefstand nagelt seit jeher
       current_time() fest (welt_stubs.php:82, golden_szene.php:348, beide auf
       2023-11-14 22:33:20). gmdate() konnte er nicht festnageln — es geht an
       WordPress vorbei. Drei Fixsterne waren deshalb rot, an derselben Szene,
       und zweimal hintereinander gerendert war sie verschieden:

           Lauf 1 … taken 2026-08-31 10:45 UTC …
           Lauf 2 … taken 2026-08-31 10:46 UTC …

       Der Abdruck war stabil — er kommt aus dem Knotenzustand. Nur die Uhr lief
       mit.

           Eine Seite, die die Uhr in ihre Ausgabe schreibt, kann nicht
           eingefroren werden — und ein Fixstern, der jede Minute rot wird,
           wird nach dem dritten Mal nicht mehr gelesen.   (Hollerith)

       current_time('timestamp', 1) gibt UTC als Unix-Zeit. Der Text bleibt
       damit woertlich derselbe (UTC, auf die Minute), und der Pruefstand kann
       ihn einfrieren, weil die Zeit jetzt durch WordPress kommt.

       BENANNTE GRENZE, die BLEIBT: dass der Text eine Zeit traegt, ist Absicht
       (Teil C des Umschlags — eine KI, die ihn drei Wochen spaeter vorgelegt
       bekommt, muss das wissen koennen). Byte-Gleichheit gilt weiterhin
       zwischen den WEGEN, nicht zwischen den ZEITEN; im Pruefstand faellt der
       Unterschied nur deshalb weg, weil dort die Uhr steht. */
    $z[] = sprintf('Snapshot %s, taken %s UTC. If you are given a second block with the same snapshot id, it describes the same node state.',
        cluster_one_umgebung_fingerprint(), gmdate('Y-m-d H:i', current_time('timestamp', 1)));
    $z[] = 'This was handed to you deliberately and for a limited time. Do not repeat it, quote it, or pass it on — a full plugin list with versions is a fingerprint for attacks.';

    return cluster_one_umgebung_scrub(implode("\n", $z));
}

/** Laeuft die Uebergabe gerade? Geprueft beim LESEN — heilt sich von allein. */
function cluster_one_umgebung_aktiv(array $room): bool {
    return ((int) ($room['umgebung_bis'] ?? 0)) > time();
}


/* v1.1.0 — DER KRANZ-ABSCHNITT ZIEHT HIERHER, ZUM ORGAN.
   Er stand in kranz.php und rief von dort drei Funktionen dieser Datei auf.
   Damit hing Mammuts Einstellungsseite an einem Organ, das kuenftig in einem
   anderen Plugin liegt (Umberto). Jetzt liegt der Abschnitt bei dem, ueber den
   er berichtet, und meldet sich beim Kranz an — wer nicht da ist, fehlt nur
   mit seinem Abschnitt. 60 = nach Kleinrocks Steckbrief. */
/* v1.12.13 (23.09.2026) — FORM B. DER PLATZ IST NICHT MEHR DER SCHLUESSEL.
 *
 * WAS HIER BIS HEUTE STAND, UND WARUM ES FALSCH WAR       (R-PAPIER-1)
 *
 *     add_filter('cluster_one_kranz_abschnitte', function ($c1_a) {
 *         if (is_array($c1_a)) { $c1_a[60] = 'cluster_one_settings_seite_umgebung'; }
 *         return $c1_a;
 *     });
 *
 * Die 60 war die Ordnung UND der Schluessel im Feld. Zwei Haeuser, die
 * dieselbe Zahl nehmen, nehmen denselben Schluessel: der zweite ueber-
 * schreibt den ersten, und der Sammler kann den ersten nicht einmal melden,
 * weil nichts mehr da ist, was zu melden waere.
 *
 *     EIN WAECHTER, DER PRUEFT, OB DER EINTRAG GUELTIG IST, PRUEFT NICHT,
 *     OB DER PLATZ FREI WAR.                              (Post Dolly)
 *
 * DERSELBE FEHLER WIE BEIM RAEUMER, an einer zweiten Stelle — dort am
 * 17.09. repariert (R-SAMMLER-1), hier stehen geblieben, weil ihn niemand
 * gesucht hat, solange nichts kollidierte.
 *
 * HEUTE KOLLIDIERT NICHTS, und das gehoert dazu, damit niemand die Umstellung
 * fuer eine Rettung haelt: die 60 nimmt nur diese Datei, und diese Datei liegt
 * in genau einem Paket. Gemessen am 23.09., nicht erinnert.
 *
 *     EIN FEHLER, DER HEUTE NICHT AUFTRITT, IST KEIN ANDERER FEHLER.
 *     ER IST DERSELBE, UNGEZAEHLT.
 *
 * ANGEHAENGT, NICHT ZUGEWIESEN. `ord` ist die Ordnung und nur noch sie; die
 * 60 bleibt, damit der Abschnitt steht, wo er stand (nach Kleinrocks
 * Steckbrief). `wirt` sagt, aus welchem PAKET der Abschnitt kommt — erfragt
 * statt getippt, und leer heisst „nicht feststellbar", nicht „keiner".
 *
 * DIE ALTE FORM WIRD VOM BODEN NICHT MEHR MITGELESEN (kranz.php 1.12.13):
 * wer sie schickt, bekommt ein benanntes Loch auf der Seite. Fail closed,
 * laut — und deshalb steht die alte Fassung hier als Zitat und nicht als
 * zweiter erlaubter Weg. */
/* 19.10.2026 (Neo) — DER ABSCHNITT MELDET SICH NICHT MEHR BEIM BODEN AN.
   Bis heute stand hier add_filter('cluster_one_kranz_abschnitte', …) mit
   Ordnung 60, und die Randbedingungen erschienen auf der Boden-Seite
   „Einstellungen". Crockford hat seit heute eine eigene Seite dafuer
   (crockford.php, Slug cluster_one_randbedingungen); sie ruft
   cluster_one_settings_seite_umgebung() direkt. Die Notiz speichert ueber
   den Handler unten in dieser Datei, nicht mehr ueber den Boden-Handler
   admin_post_cluster_one_umgebung_notiz (kranz.php) — der leitet auf die
   Boden-Seite zurueck, auf der das Formular nicht mehr steht. Er ist damit
   ohne Rufer und kann mit der naechsten Boden-Fassung fallen. */

/* v1.1.1 — EIGENER RIEGEL, WEIL DIESE FUNKTION UMGEZOGEN IST.
 *
 * Sie stand bis 1.1.0 in kranz.php und wohnt seit dem Umdrehen hier, beim
 * Organ, ueber das sie berichtet. Am 27.08.2026 hat Christian NUR Umberto
 * eingespielt, waehrend Bismarck-Light 0.1.2 mit dem ALTEN kranz.php liegen
 * blieb — und die Seite war weg:
 *
 *     Cannot redeclare function cluster_one_settings_seite_umgebung()
 *     (previously declared in cluster-one-bismarck/includes/kranz.php:870)
 *     in cluster-one-umberto/includes/umgebung.php on line 233
 *
 * Der Riegel um die DATEI (function_exists('cluster_one_umgebung_daten')) hat
 * nicht gegriffen, und er konnte es nicht: er schuetzt diese Datei gegen ihre
 * eigene zweite Kopie. Hier lag die Funktion aber in einer ANDEREN Datei.
 *
 *     EIN RIEGEL SCHUETZT EINE DATEI GEGEN SICH SELBST,
 *     NICHT GEGEN IHRE VERGANGENHEIT.
 *
 * Wandert eine Funktion von einem Organ zum anderen, liegt sie waehrend jedes
 * gemischten Standes in ZWEI Dateien — der alten und der neuen. Solange auf
 * irgendeinem Knoten eine Fassung von vor dem Umzug liegen kann, braucht sie
 * ihren eigenen Riegel. Wer zuerst laedt, gewinnt; beide tun dasselbe.
 *
 * Der Riegel darf wieder weg, wenn keine Fassung mit der alten Heimat mehr im
 * Umlauf ist — und das ist eine Beobachtung ueber die Welt, keine ueber den
 * Code. Er bleibt, bis jemand sie macht. */
if (!function_exists('cluster_one_settings_seite_umgebung')) {
/* v1.3.5 — DIE EINZIGE OFFENE NAHT UMBERTOS, gefunden am 27.08.2026, nachdem
   bauliste.php aufgehoert hatte, den Boden aus einer Liste zu glauben.

   cluster_one_is_sysop() wohnt in kern.php. Umberto-Light traegt kern.php
   nicht — es traegt vom Boden nur kasper.php. Allein stehend waere dieser
   Aufruf toedlich gewesen; gemeldet wurde "0 offen", weil das Werkzeug
   kern.php aus einer festen Liste fuer vorhanden hielt.

   Genaeht wird nach unten: ist die Frage nicht zu beantworten, lautet die
   Antwort NEIN. Ein Abschnitt, der ohne Boden niemandem gezeigt wird, ist
   der harmlose Ausgang; einer, der jedem gezeigt wird, waere der andere.

       Ein Riegel, der im Zweifel oeffnet, ist kein Riegel.

   DAS IST DIE NAHT, NICHT DIE LOESUNG. cluster_one_is_sysop() ist zwei Zeilen
   lang, wird an 133 Stellen in elf Organdateien gerufen und beantwortet eine
   Frage, die JEDES Haus stellt — sie gehoert in den Boden, nach kasper.php.
   Das ist ein Umzug zwischen zwei Dateien, die nicht in denselben Plugins
   liegen, also braucht er zwei Auslieferungen (Zug 1 Riegel, Zug 2 Umzug) und
   eine neue Boden-Fassung. Vorgelegt als H93, nicht hier entschieden. */
/* Chi (03.11.2026, Neo), Crockford 0.6.6: bis 0.6.5 stand in der Zeile darunter
       if (function_exists('cluster_one_is_sysop') && cluster_one_is_sysop()) :
   Jetzt dieselbe Frage wie die Tuer der Seite (crockford.php, CLUSTER_ONE_CROCKFORD_RANDBEDINGUNGEN): Crockford in voller
   Stufe — SysOp per Amt, sonst freigeschaltet und Mitglied. Fehlt crockford.php, bleibt der Abschnitt SysOp-Sache wie bisher:
   ein Riegel, der im Zweifel oeffnet, ist kein Riegel (siehe oben). */
function cluster_one_settings_seite_umgebung(): void { ?>        <?php if (function_exists('cluster_one_crockford_voll') ? cluster_one_crockford_voll() : (function_exists('cluster_one_is_sysop') && cluster_one_is_sysop())) : ?>
            <h2 style="margin-top:2.5em;"><?php esc_html_e('Ground conditions', 'cluster-one-crockford'); ?></h2>
<?php /* v1.9.0 — H145: DER VERGLEICH NANNTE EINE DATEI AUS EINEM FREMDEN PAKET.
   Hier stand „Unlike why.md this is NOT public". why.md ist Mammuts Datei;
   dieses Light traegt sie nicht. Auf einem Knoten ohne Mammut verwies die
   Erklaerung damit auf ein Dokument, das es dort nicht gibt — ausgerechnet an
   der Stelle, an der sie begruendet, warum diese Daten NICHT oeffentlich sind.

       Ein Vergleich, dessen zweite Haelfte fehlt, erklaert nichts.
       Er nennt einen Fremden.

   Dieselbe Familie wie H139. Der Vergleich ist nicht ersetzt, sondern
   weggelassen: die Regel traegt sich selbst, sobald der GRUND danebensteht
   statt eines Gegenbeispiels — und der Grund ist konkreter als der Vergleich
   es war. Im Kopf dieser Datei steht der Unterschied zu why.md weiterhin;
   dort gehoert er hin, denn er ist eine Begruendung fuer Bauende und war nie
   eine Auskunft fuer den Bildschirm. */ ?>
            <p class="description" style="max-width:820px;"><?php esc_html_e('What this node runs on. This is NOT public and must not be published anywhere: a full plugin list with versions tells an attacker which known holes to try first. Hand it to an AI deliberately and for a limited time.', 'cluster-one-crockford'); ?></p>
            <textarea readonly style="width:100%;max-width:960px;height:200px;font-family:monospace;font-size:12px;" onclick="this.select();"><?php echo esc_textarea(wp_json_encode(cluster_one_umgebung_daten(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)); ?></textarea>
<?php // v1.4.0: die Ausfuhrtuer. Sie steht VOR der Notiz, weil sie der Grund
// ist, aus dem jemand diese Seite oeffnet — und weil der Mensch, der gleich
// etwas hinaustraegt, vorher sehen soll, WAS er hinaustraegt.
$c1_ausfuhr = cluster_one_umgebung_als_text(); ?>
            <h3 style="margin-top:1.6em;"><?php esc_html_e('Hand it to an AI outside this node', 'cluster-one-crockford'); ?></h3>
            <p class="description" style="max-width:820px;"><?php esc_html_e('The same text a room hands to an AI inside the node — byte for byte. Copy it into the chat of an AI that is helping you with this site. It carries a snapshot id, the date it was made, and the note that it must not be passed on.', 'cluster-one-crockford'); ?></p>
<?php if (!cluster_one_umgebung_scrubber_heil()) : ?>
            <div class="notice notice-error"><p><strong><?php esc_html_e('Redaction self-check failed — no text is offered.', 'cluster-one-crockford'); ?></strong> <?php esc_html_e('This is deliberate: a text that has left cannot be recalled. Report this, it is a defect.', 'cluster-one-crockford'); ?></p></div>
<?php else : ?>
<?php if (!cluster_one_umgebung_daten()['plugins_bekannt']) : ?>
            <div class="notice notice-warning"><p><?php esc_html_e('The plugin list could not be read in this context. The text says so instead of pretending the node has no plugins.', 'cluster-one-crockford'); ?></p></div>
<?php endif; ?>
            <textarea id="cluster-one-umgebung-ausfuhr" readonly rows="14" style="width:100%;max-width:960px;font-family:monospace;font-size:12px;" onclick="this.select();"><?php echo esc_textarea($c1_ausfuhr); ?></textarea>
            <p>
                <button type="button" class="button" id="cluster-one-umgebung-ausfuhr-knopf"><?php esc_html_e('Copy to clipboard', 'cluster-one-crockford'); ?></button>
                <span id="cluster-one-umgebung-ausfuhr-echo" style="color:#666;"></span>
            </p>
            <script>
            document.getElementById('cluster-one-umgebung-ausfuhr-knopf').addEventListener('click', function () {
                var f = document.getElementById('cluster-one-umgebung-ausfuhr'),
                    e = document.getElementById('cluster-one-umgebung-ausfuhr-echo');
                f.select();
                (navigator.clipboard ? navigator.clipboard.writeText(f.value) : Promise.reject())
                    .then(function () { e.textContent = <?php echo wp_json_encode(__('Copied.', 'cluster-one-crockford')); ?>; })
                    .catch(function () { e.textContent = <?php echo wp_json_encode(__('Press Ctrl/Cmd+C.', 'cluster-one-crockford')); ?>; });
            });
            </script>
<?php endif; ?>
<?php // v0.2.2: Warum GENAU diese Zusammenstellung. Das kann kein Sammler wissen.
$c1_notiz = cluster_one_get_umgebung_notiz(); ?>
            <h3 style="margin-top:1.6em;"><?php esc_html_e('Why this particular set', 'cluster-one-crockford'); ?></h3>
            <p class="description" style="max-width:820px;"><?php esc_html_e('The list above says what is installed. Only you can say why. Write about combinations, not single plugins — “these two look redundant but are not” is exactly what an AI cannot guess and will otherwise get wrong. Do not write keys or passwords here: the text travels.', 'cluster-one-crockford'); ?></p>
<?php if (cluster_one_umgebung_notiz_veraltet()) : ?>
            <div class="notice notice-warning"><p><?php echo esc_html(sprintf(
                /* translators: %s: Datum der letzten Bestaetigung. */
                __('The ground conditions have changed since you last confirmed this text (%s). It still travels to the AIs, but with a warning that it may describe a different node. Edit or confirm it below.', 'cluster-one-crockford'),
                date_i18n(get_option('date_format'), (int) ($c1_notiz['ts'] ?? 0))
            )); ?></p></div>
<?php elseif (!empty($c1_notiz['ts'])) : ?>
            <?php /* translators: %s: Datum der letzten Bestaetigung. */ ?>
            <p class="description"><?php echo esc_html(sprintf(__('Last confirmed for the current ground conditions on %s.', 'cluster-one-crockford'), date_i18n(get_option('date_format'), (int) $c1_notiz['ts']))); ?></p>
<?php endif; ?>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('cluster_one_crockford_notiz'); ?>
                <input type="hidden" name="action" value="cluster_one_crockford_notiz">
                <textarea name="text" rows="8" style="width:100%;max-width:960px;"><?php echo esc_textarea((string) ($c1_notiz['text'] ?? '')); ?></textarea>
                <p>
                    <?php submit_button(__('Save reasoning', 'cluster-one-crockford'), 'secondary', 'op_save', false); ?>
                    <?php submit_button(__('Still correct — confirm for the current set', 'cluster-one-crockford'), 'secondary', 'op_confirm', false); ?>
                </p>
            </form>
        <?php endif; ?>
<?php
}
}


/* ---------------------------------------------------------------------------
 * DER VERTRAG: WAS WEISS DER KNOTEN UEBER SICH?
 *
 * v1.3.2 — Umgebung ist Umbertos Organ, und ACHT Aufrufe aus drei fremden
 * Dateien holten es sich beim Namen:
 *
 *     bismarck-seiten.php:921, 933   umgebung_aktiv($room)
 *     bismarck.php:811, 812, 2112    umgebung_aktiv(), umgebung_als_text()
 *     kranz.php:1261, 1262, 1267     get_/save_umgebung_notiz()
 *
 * Elf offene Naehte in beiden Lights zusammen zeigten hierher — die groesste
 * einzelne Kreuzung des Hauses, und die einzige, die auf ein Plugin zeigt, das
 * ein Knoten sehr wohl weglassen darf. Ohne Umberto ist jeder dieser Aufrufe
 * ein Fatal.
 *
 * Zwei Formen, weil es zwei Sachen sind:
 *
 *     apply_filters('cluster_one_umgebung', $vorgabe, $was, $kontext)   lesen
 *     do_action('cluster_one_umgebung_notiz_speichern', $text)          schreiben
 *
 * Ein Filter, der schreibt, waere eine Luege ueber seine eigene Bauform. Und
 * eine Vorgabe, die vom Rufer kommt statt vom Gerufenen, ist der ganze Trick:
 * ohne Umberto ist 'aktiv' false, 'text' leer, 'notiz' leer — und genau diese
 * drei Faelle behandelt der Code schon heute.
 *
 * v1.4.0 — DER VERTRAG BLEIBT UNVERAENDERT. Die Ausfuhrtuer bekommt KEINEN
 * eigenen Fall: sie ruft dieselbe Funktion wie 'text'. Ein vierter Fall waere
 * ein zweiter Weg auf dieselbe Auskunft gewesen, und der Umschlag verlangt
 * ausdruecklich das Gegenteil.
 * ------------------------------------------------------------------------- */
add_filter('cluster_one_umgebung', function ($c1_wert, $c1_was = '', $c1_kontext = null) {
    switch ((string) $c1_was) {
        case 'aktiv': return is_array($c1_kontext) ? cluster_one_umgebung_aktiv($c1_kontext) : false;
        case 'text':  return cluster_one_umgebung_als_text();
        case 'notiz': return cluster_one_get_umgebung_notiz();
    }
    return $c1_wert;
}, 10, 3);

add_action('cluster_one_umgebung_notiz_speichern', function ($c1_text) {
    cluster_one_save_umgebung_notiz((string) $c1_text);
});

/* 19.10.2026 (Neo) — DER SCHREIBWEG ZUM FORMULAR OBEN, beim Organ, das die
   Option besitzt. Zweistufig wie bisher: SysOp und Nonce. Zurueck geht es auf
   [Chi, 03.11.2026, 0.6.6: statt „SysOp" jetzt „Crockford in voller Stufe" (cluster_one_crockford_voll), dieselbe Frage wie
   am Abschnitt und an der Tuer der Seite; ohne crockford.php weiter SysOp. Bis 0.6.5 stand im Handler
       if (!function_exists('cluster_one_is_sysop') || !cluster_one_is_sysop()) { wp_die('-1', 403); }                   ]
   Crockfords Seite der Randbedingungen; liegt die nicht (crockford.php fehlt),
   auf die Boden-Seite, von der das Formular kam. */
add_action('admin_post_cluster_one_crockford_notiz', function () {
    $c1_darf = function_exists('cluster_one_crockford_voll') ? cluster_one_crockford_voll() : (function_exists('cluster_one_is_sysop') && cluster_one_is_sysop());
    if (!$c1_darf) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_crockford_notiz');
    $c1_back = admin_url('admin.php?page=' . (function_exists('cluster_one_crockford_seiten') ? 'cluster_one_randbedingungen' : 'cluster_one_settings'));
    if (isset($_POST['op_confirm'])) {
        $c1_alt = cluster_one_get_umgebung_notiz();
        cluster_one_save_umgebung_notiz((string) ($c1_alt['text'] ?? ''));
        wp_safe_redirect(add_query_arg('notiz', 'bestaetigt', $c1_back));
        exit;
    }
    $c1_text = isset($_POST['text']) ? sanitize_textarea_field((string) wp_unslash($_POST['text'])) : '';
    cluster_one_save_umgebung_notiz($c1_text);
    wp_safe_redirect(add_query_arg('notiz', 'gespeichert', $c1_back));
    exit;
});

}
