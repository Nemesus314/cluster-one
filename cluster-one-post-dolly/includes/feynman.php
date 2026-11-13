<?php
/**
 * Feynman — die Lehrschicht (v0.1.0, umgeräumt in v0.1.2).
 *
 * Feynman ist eine EIGENE Seite mit eigenem Reiterband, kein Kranz-Reiter.
 * Kranz ist Einstellungen; Feynman ist der Lernpfad — und "Über mich" ist
 * kein Systemteil, sondern der Mensch hinter dem Knoten, gehört also hierher
 * und nicht zu den Einstellungen. Das leere Gerüst 'cluster_one_feynman'
 * unter Kranz ist entfallen: es war eine zweite Seite mit demselben Namen.
 *
 * 03.11.2026 (Neo), Boden 1.22.0 — RUNDE TAU: FEYNMAN IST BODEN. Bis 1.21.1 reiste diese Datei nur in Kleinrock und
 * Bismarck (mit faust.php), und seit die Kranz-Leiste in die Produktleisten aufgeloest war (19.10.) fuehrte kein Reiter
 * mehr hierher. Nemesus am 02.11.: „Das koennte wieder Feynman sein. Aber wenn, gehoert das als Reiter zu jedem PlugIn."
 * Also: ein Reiter „Feynman" in jeder Leiste des Hauses, vor „Knoten" (kern.php, cluster_one_render_tabs); diese Datei
 * und why.txt in allen fuenf Paketen („Redundanz ist der Preis fuer Modularitaet"); oben die Leiste des Hauses, von dem
 * der Leser kam, in der Ueberschrift dessen Name (cluster_one_page_h1); die eigene Reihe darunter ohne „Knoten" — der
 * steht schon oben. Pacioli traegt ausser kasper.php keinen Boden: dort liegt diese Datei bei und deklariert, ihre Seiten
 * meldet aber erst das menue.php eines Boden-Pakets auf demselben Knoten an. Liegt Pacioli allein, liegt die Datei und
 * schweigt, und Pacioli zeichnet keinen Reiter auf eine Seite, die WordPress nicht kennt.
 *
 * Reiter, alle bewusst als GERÜST ausgeliefert:
 *
 *   1. Feynman — das Tutorial. Der Name ist Programm: erklären, bis es
 *      jemand versteht, der es nicht schon weiß. Der Text bleibt leer, bis
 *      der Knoten lange genug gelebt hat, um ehrlich beschrieben zu werden —
 *      ein Tutorial aus der Erinnerung ans eigene Bauen wäre eine Anleitung
 *      für den Erbauer, nicht für den Ankommenden.
 *
 *   2. Über mich — der letzte Reiter. Kein Systemteil, sondern der Mensch
 *      hinter dem Knoten. Steht mit Absicht ganz am Ende: wer bis hierher
 *      gelesen hat, hat die Maschine verstanden und darf erfahren, wer sie
 *      gebaut hat.
 *
 * Beide Seiten sind rein lesend, ohne Formular, ohne Einstellung, ohne
 * Speicher — es gibt nichts zu konfigurieren. Text wird eingefüllt, wenn er
 * reif ist; die Struktur ändert sich dabei nicht mehr.
 */

if (!defined('ABSPATH')) {
    exit;
}

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
/* VERTRAG 27 §4 — DIE MELDUNG STEHT VOR DEM RIEGEL.
   Bis hierher stand cluster_one_hier() INNERHALB des Riegels: eine verdraengte
   Kopie dieser Datei schwieg dann auch ueber ihre eigene Existenz. Die
   Bedingung wird jetzt EINMAL gelesen und traegt beide Folgen — die Meldung
   und den Riegel.
       Ein Melder kennt sich selbst. „Verdraengt" ist eine BEZIEHUNG, und
       Beziehungen gehoeren dem Sammler.            (Maillart, 11.09.)
   Die Fassung bleibt LEER: Kasper liest sie am Wirt (H118), und eine
   geliehene Nummer ist schlimmer als keine. */
$cluster_one_feynman_laeuft = !function_exists('cluster_one_why_text');

cluster_one_hier('feynman', '', $cluster_one_feynman_laeuft);

if ($cluster_one_feynman_laeuft) {

/* --- v0.1.8: why.md — EIN Text, drei Fenster --------------------------------
   Die Begründung des Entwurfs liegt als why.md im Plugin. Sie wird an drei
   Stellen ausgeliefert: als Seite hier unter Feynman (für Menschen auf dem
   Knoten), über die Route cluster-one/v1/why (für KIs mit eigener Agentur und
   für fremde Knoten) und als markierter Kurzblock in der Präambel (für die KI
   im Raum, die keine Werkzeuge hat).

   ALLE DREI LESEN DIE DATEI. Keine kopiert sie. Die Lehre steckt in W8: zwei
   Kopien einer Wahrheit driften auseinander, und dann braucht man einen
   Wächter, der sie vergleicht. Besser: es gibt nur eine Stelle.

   Warum das überhaupt öffentlich ist: Eine Hausordnung, die nur eine Partei
   lesen kann, ist keine Hausordnung, sondern eine Anweisung. In why.md stehen
   Sätze ÜBER die Menschen hier — dass sie jederzeit gehen können, dass Feynman
   die Ausfahrt ist. Wüsste die KI das und der Beschirmte nicht, wäre die KI zur
   Verwalterin der Ausfahrt gemacht. */

/** Der volle Text. '' wenn die Datei fehlt (dann sagt die Seite das ehrlich). */
function cluster_one_why_text(): string {
    /* Rho (02.11.2026, Neo): bis Boden 1.21.0 hiess die Datei why.md. Nemesus: „das why in Texte umwandeln" — KIs lesen
       sie so wie bisher (Markierung AI-BRIEFING, Uebersetzer unten), Menschen ohnehin kaum; und der Pruefer des
       Verzeichnisses erwartet keine Markdown-Dateien im Stamm. Inhalt unveraendert, nur der Name. */
    $p = dirname(__DIR__) . '/why.txt';
    if (!is_readable($p)) {
        return '';
    }
    $s = file_get_contents($p);
    return is_string($s) ? $s : '';
}

/** Der maschinell markierte Kurzblock für KIs im Raum — ein AUSSCHNITT der
   einen Datei, keine zweite Fassung. Fehlt die Markierung, gibt es lieber
   nichts als etwas Erfundenes. */
/* v0.6.0: DER MARKER GEHOERT NICHT why.md, SONDERN JEDEM, DER IHN SETZT.

   Bis 0.5.4 las nur why.md das Paar <!-- AI-BRIEFING:START/END -->, und der
   Leser nahm das ERSTE Paar. Zweisprachige Dateien haben aber zwei Bloecke —
   die Framework-Doku traegt die Kurzkarte einmal deutsch, einmal englisch, und
   sie stehen 380 Zeilen auseinander. Also sammelt der Leser jetzt ALLE Paare
   und haengt sie aneinander.

   Damit ist es ein allgemeiner Mechanismus: Jedes Theme, das die Klammer setzt,
   wird lesbar — Nemesus DNA ist nur das erste, das sie traegt. */
function cluster_one_briefing_blocks(string $t): string {
    $auf  = '<!-- AI-BRIEFING:START -->';
    $zu   = '<!-- AI-BRIEFING:END -->';
    $out  = array();
    $pos  = 0;
    while (($a = strpos($t, $auf, $pos)) !== false) {
        $b = strpos($t, $zu, $a);
        if ($b === false) {
            break; // offene Klammer: der Rest zaehlt nicht, statt bis Dateiende zu raten
        }
        $roh = trim(substr($t, $a + strlen($auf), $b - $a - strlen($auf)));
        if ($roh !== '') {
            $out[] = $roh;
        }
        $pos = $b + strlen($zu);
    }
    return implode("\n\n", $out);
}

function cluster_one_why_briefing(): string {
    $t = cluster_one_why_text();
    return cluster_one_briefing_blocks($t);
}

/** Die Kurzkarte der Framework-Doku, oder ''. Gelesen aus dem Child-Theme —
   nie gespiegelt, also immer die Fassung, die auf DIESEM Knoten liegt. Kein
   Framework, kein Marker, kein Text: dann schweigt sie, statt zu erfinden. */
function cluster_one_dna_briefing(): string {
    $t = cluster_one_dna_doc_text();
    return $t === '' ? '' : cluster_one_briefing_blocks($t);
}

/** Sehr kleiner Markdown-Übersetzer — bewusst nur die Teilmenge, die why.md
   tatsächlich benutzt: Überschriften, Absätze, Listen, Tabellen, Zitatblöcke,
   Trennlinien, fett, kursiv, Code. Kein allgemeiner Parser: was die Datei nicht
   enthält, muss er nicht können. Reine Funktion, deshalb einzeln bezeugbar. */
function cluster_one_why_to_html(string $md): string {
    $md    = str_replace(array("\r\n", "\r"), "\n", $md);
    $md    = preg_replace('/<!--.*?-->/s', '', $md); // Markierungen sind nichts für Augen
    $zeile = explode("\n", (string) $md);
    $out   = '';
    $liste = false;
    $tab   = false;
    $inline = function (string $t): string {
        $t = esc_html($t);
        $t = preg_replace('/`([^`]+)`/', '<code>$1</code>', $t);
        $t = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $t);
        $t = preg_replace('/(?<![\*\w])\*([^*\n]+)\*(?!\*)/', '<em>$1</em>', $t);
        return (string) $t;
    };
    $zu = function () use (&$out, &$liste, &$tab) {
        if ($liste) { $out .= "</ul>\n"; $liste = false; }
        if ($tab)   { $out .= "</tbody></table>\n"; $tab = false; }
    };
    foreach ($zeile as $z) {
        $t = rtrim($z);
        if (trim($t) === '') { $zu(); continue; }
        if (preg_match('/^(#{1,4})\s+(.*)$/', $t, $m)) {
            $zu();
            $n = min(4, strlen($m[1]) + 1); // h1 der Datei wird h2 der Seite
            $out .= '<h' . $n . ' style="margin-top:1.4em;">' . $inline($m[2]) . '</h' . $n . ">\n";
            continue;
        }
        if (preg_match('/^(-{3,}|\*{3,})$/', trim($t))) { $zu(); $out .= "<hr>\n"; continue; }
        if (strpos($t, '|') === 0) {
            if (preg_match('/^\|[\s\-:|]+\|$/', trim($t))) { continue; } // Trennzeile
            $zellen = array_map('trim', explode('|', trim($t, "| \t")));
            if (!$tab) { $out .= '<table class="widefat" style="max-width:820px;margin:.6em 0;"><tbody>' . "\n"; $tab = true; }
            $out .= '<tr><td>' . implode('</td><td>', array_map($inline, $zellen)) . "</td></tr>\n";
            continue;
        }
        if ($tab) { $zu(); }
        if (preg_match('/^[-*]\s+(.*)$/', $t, $m)) {
            if (!$liste) { $out .= '<ul style="max-width:820px;list-style:disc;margin-left:1.4em;">' . "\n"; $liste = true; }
            $out .= '<li>' . $inline($m[1]) . "</li>\n";
            continue;
        }
        if ($liste) { $zu(); }
        if (strpos($t, '>') === 0) {
            $out .= '<blockquote style="max-width:820px;border-left:3px solid #ccc;padding-left:1em;margin:.6em 0;opacity:.9;">'
                  . $inline(ltrim($t, '> ')) . "</blockquote>\n";
            continue;
        }
        $out .= '<p style="max-width:820px;">' . $inline($t) . "</p>\n";
    }
    $zu();
    return $out;
}

/** Die Warum-Seite. Liest die Datei bei jedem Aufruf — nichts wird gespiegelt. */
function cluster_one_render_why_page(): void {
    cluster_one_page_gate();
    $md = cluster_one_why_text();
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Feynman'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_feynman_tabs('why'); ?>
<?php if ($md === '') : ?>
        <div class="notice notice-warning"><p><?php esc_html_e('why.txt is missing from the plugin directory. Nothing is shown rather than something invented.', 'cluster-one-post-dolly'); ?></p></div>
<?php else : ?>
        <p class="description" style="max-width:820px;margin-top:1em;"><?php esc_html_e('The reasoning behind this plugin — the same text the AIs here are given, and the same text that ships in the plugin directory as why.txt. Bilingual: German first, English below.', 'cluster-one-post-dolly'); ?></p>
        <div style="max-width:860px;"><?php echo cluster_one_why_to_html($md); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- eigene Ausgabe, im Uebersetzer escaped: cluster_one_why_to_html() fuehrt jedes Textstueck durch esc_html() ?></div>
<?php endif; ?>
    </div>
    <?php
}

/* --- v0.1.9: DNA — Feynman als Leser, nicht als Lager -----------------------
   Trägt dieser Knoten Christians Framework (Nemesus DNA), soll seine Doku hier
   lesbar sein — für Menschen wie für KIs, als Verlängerung der Transparenz aus
   0.1.8. AUSDRÜCKLICH KEINE KOPIE ins Plugin: Die Wahrheit bleibt im
   Child-Theme und ist damit automatisch versionsgleich mit dem installierten
   Framework. Null Drift, eine Quelle — dieselbe Regel wie bei why.md.

   Trägt der Knoten das Framework nicht, existiert der Reiter nicht. Lautlos,
   ohne Fehlermeldung, ohne leere Seite.

   ACHTUNG bei der Dateisuche: Die Framework-Doku heißt laut ihrem eigenen
   Strukturbaum readme.md — die Überschrift "LIESMICH / README" ist kein
   Dateiname. Ein Wächter, der auf den falschen Namen hört, feuert nie und
   beschwert sich nie. Deshalb werden beide Namen geprüft. */
function cluster_one_dna_doc_path(): string {
    if (!function_exists('get_stylesheet_directory')) {
        return '';
    }
    $dir = rtrim((string) get_stylesheet_directory(), '/');
    if ($dir === '') {
        return '';
    }
    foreach (array('readme.md', 'LIESMICH.md', 'README.md') as $name) {
        $p = $dir . '/' . $name;
        if (is_readable($p)) {
            return $p;
        }
    }
    return '';
}

/** Der Text der Framework-Doku, oder ''. Gelesen, nie gespiegelt. */
function cluster_one_dna_doc_text(): string {
    $p = cluster_one_dna_doc_path();
    if ($p === '') {
        return '';
    }
    $s = file_get_contents($p);
    return is_string($s) ? $s : '';
}

/** Die DNA-Seite. Rein lesend, kein Formular, keine Interaktion. */
function cluster_one_render_dna_page(): void {
    cluster_one_page_gate();
    $md = cluster_one_dna_doc_text();
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Feynman'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_feynman_tabs('dna'); ?>
<?php if ($md === '') : ?>
        <div class="notice notice-info"><p><?php esc_html_e('No framework documentation found in the active theme. Nothing is shown rather than something invented.', 'cluster-one-post-dolly'); ?></p></div>
<?php else : ?>
        <p class="description" style="max-width:820px;margin-top:1em;"><?php echo esc_html(sprintf(
            /* translators: %s: Adresse, von der die Dokumentation gelesen wird. */
            __('The documentation of the framework this node runs on, read live from %s. It is not copied into the plugin — that way it is always the version that is actually installed.', 'cluster-one-post-dolly'),
            basename(cluster_one_dna_doc_path())
        )); ?></p>
        <div style="max-width:860px;"><?php echo cluster_one_why_to_html($md); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- eigene Ausgabe, im Uebersetzer escaped: cluster_one_why_to_html() fuehrt jedes Textstueck durch esc_html() ?></div>
<?php endif; ?>
    </div>
    <?php
}


/** Die Kapitel, die auch den KIs vorgelegt werden — Text fuer die Praeambel. */
function cluster_one_feynman_chapters_for_ai(): string {
    $out = array();
    foreach (cluster_one_get_feynman_chapters() as $k) {
        if (empty($k['for_ai'])) {
            continue;
        }
        $z = array();
        if (trim((string) ($k['title'] ?? '')) !== '') { $z[] = trim((string) $k['title']); }
        if (trim((string) ($k['body'] ?? ''))  !== '') { $z[] = trim((string) $k['body']); }
        if (trim((string) ($k['link'] ?? ''))  !== '') { $z[] = trim((string) $k['link']); }
        if ($z) { $out[] = implode("\n", $z); }
    }
    return implode("\n\n", $out);
}

/** Die Kapitel des SysOps, rein lesend. Zeigt bei jedem Kapitel an, ob die KIs
   es ebenfalls bekommen — eine Entscheidung, die sichtbar sein muss. */
function cluster_one_render_feynman_chapters(): void {
    $kap = cluster_one_get_feynman_chapters();
    if (!$kap) {
        return;
    }
    foreach ($kap as $k) {
        $t = trim((string) ($k['title'] ?? ''));
        $b = trim((string) ($k['body'] ?? ''));
        $l = trim((string) ($k['link'] ?? ''));
        echo '<h2 style="margin-top:1.8em;">' . esc_html($t !== '' ? $t : __('(untitled)', 'cluster-one-post-dolly')) . '</h2>';
        if (!empty($k['for_ai'])) {
            echo '<p style="font-size:.85em;opacity:.75;margin:-.4em 0 .6em;">'
               . esc_html__('The AIs on this node are given this chapter too.', 'cluster-one-post-dolly') . '</p>';
        }
        if ($b !== '') {
            echo wpautop(esc_html($b)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_html() innen, wpautop() setzt nur <p> und <br> darum
        }
        if ($l !== '') {
            echo '<p><a href="' . esc_url($l) . '" rel="noopener noreferrer" target="_blank">' . esc_html($l) . '</a></p>';
        }
    }
}

/** 03.11.2026 (Neo), Boden 1.22.0 (Tau) — DIE SEITEN DES LERNPFADS: EINE Liste, drei Leser — die Reihe unten, der Reiter
 *  „Feynman" im Boden (kern.php, cluster_one_feynman_seite_aktuell) und Paciolis Leiste (class-cv-admin.php). Dieselben
 *  vier Slugs, die menue.php unter cluster_one_menue_wenn('feynman', …) anmeldet. */
function cluster_one_feynman_seiten(): array {
    return array('cluster_one_about', 'cluster_one_why', 'cluster_one_dna', 'cluster_one_ueber_mich');
}

/** Reiterband der Feynman-Seite. v0.1.2: aus render_about_page herausgelöst,
   damit "Über mich" daneben liegen kann statt unter den Einstellungen.
   $view: 'feynman' | 'about' | 'ueber_mich'. */
function cluster_one_render_feynman_tabs(string $view): void {
    /* 03.11.2026 (Neo), Boden 1.22.0 — RUNDE TAU: ZWEI REIHEN, WIE AUF DEN KNOTEN-SEITEN. Oben die Leiste des Hauses, von
       dem der Leser kam (Aktion 'cluster_one_leiste' mit der Herkunft — die Form von cluster_one_render_kranz_tabs); dort
       faerbt der Boden den Reiter „Feynman". Darunter diese Reihe, mit der Herkunft in jeder Adresse, damit sie beim Wechsel
       von Derrida zu Warum nicht verlorengeht, und als ZWEITE Reihe (cluster_one_render_tabs, zweites Argument), also ohne
       angehaengten „Knoten": der steht schon oben. Bis 1.21.1 stand diese Reihe allein, mit „Knoten" am Ende und
       „Cluster One" darueber. BENANNTE GRENZE: ein Haus ohne Leisten-Zeichner laesst die obere Reihe leer; ein aelterer
       Boden ohne zweites Argument haengt „Knoten" wieder an — weniger, kein Fatal. */
    $c1_seite = function_exists('cluster_one_aktuelle_seite') ? cluster_one_aktuelle_seite() : '';
    $c1_von   = function_exists('cluster_one_herkunft') ? cluster_one_herkunft() : '';
    if ($c1_von !== '') {
        do_action('cluster_one_leiste', $c1_von, $c1_seite);
    }
    $c1_mit = static function (string $c1_u): string {
        return function_exists('cluster_one_mit_herkunft') ? cluster_one_mit_herkunft($c1_u) : $c1_u;
    };
    $base   = $c1_mit(admin_url('admin.php?page=cluster_one_about'));
    $reiter = array(
        /* v0.7.0: Der erste Reiter heisst DERRIDA, die Seite weiter Feynman.
           Christians Entscheidung. Der Reiter traegt die Namenstabelle und den
           Prometheus-Absatz — und Derridas Frage ist genau die, die dort
           gestellt wird: nicht was im Text steht, sondern was NICHT darin
           steht. Die meisten Werkzeuge setzen voraus, dass du dein Problem
           schon kennst; das ist die Leerstelle. */
        array('label' => 'Derrida', 'url' => $base, 'active' => $view === 'feynman'),
        array('label' => __('About Cluster One', 'cluster-one-post-dolly'), 'url' => $c1_mit(admin_url('admin.php?page=cluster_one_about&view=about')), 'active' => $view === 'about'),
        // v0.1.8: die Begründung des Entwurfs, für Menschen lesbar
        array('label' => __('Why', 'cluster-one-post-dolly'), 'url' => $c1_mit(admin_url('admin.php?page=cluster_one_why')), 'active' => $view === 'why'),
    );
    // v0.1.9: Der Reiter IST der Guard — trägt der Knoten kein Framework,
    // existiert er nicht. Lautlos, ohne Fehlermeldung, ohne leere Seite.
    if (cluster_one_dna_doc_path() !== '') {
        $reiter[] = array('label' => 'DNA', 'url' => $c1_mit(admin_url('admin.php?page=cluster_one_dna')), 'active' => $view === 'dna');
    }
    /* v0.9.58: HIER STAND DER SETUP-REITER (die Blaupause). Er haengt jetzt in
       der Kranz-Leiste. Der alte Kommentar sagte, sie stehe hier, "weil sie zum
       Einrichten gehoert und nicht zur Person" — richtig beobachtet, falsche
       Folgerung: Was zum Einrichten gehoert, gehoert zu Kranz, nicht zum
       Lernpfad. Damit traegt Feynman keinen Reiter mehr, hinter dem etwas
       geschieht; alle uebrigen sind Text. */
    /* v0.6.1: Aus "Ueber mich" wird "Nemesus" — und aus dem leeren Geruest ein
       Text. "Ueber mich" war ambig, sobald ein Fremder das Plugin installiert:
       wessen mich? Der Name sagt in einem Wort, dass hier der AUTOR spricht und
       nicht der Knoten. Und er reiht sich ein — die Reiter dieses Plugins
       heissen nach Personen. */
    $reiter[] = array('label' => 'Nemesus', 'url' => $c1_mit(admin_url('admin.php?page=cluster_one_ueber_mich')), 'active' => $view === 'ueber_mich');
    cluster_one_render_tabs($reiter, true);   /* Tau: zweite Reihe, siehe oben */
    /* v0.7.0: dasselbe Muster wie bei Bismarck — das Fragezeichen zum aktiven
       Reiter, unter der Leiste statt im Link. */
    $c1_h = array('feynman' => 'derrida', 'about' => 'bismarck');
    if (isset($c1_h[$view])) {
        echo cluster_one_help($c1_h[$view]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_help() gibt fertiges HTML zurueck (cluster_one_help_bubble, esc_html im Innern) oder ''
    }
}

/* v0.6.1: DER LETZTE REITER — und der einzige, der nicht dem Knoten gehoert.

   Er war als "der Mensch hinter dem KNOTEN" angelegt, also pro SysOp. Genau
   das macht Wilde seit 0.5.3 besser: datiert, mit Geschichte, mit Diskussion
   darunter, und fuer jede Entitaet.

   Christians Unterscheidung, die den Reiter gerettet hat: WILDE IST LOKAL und
   reist nie zu anderen Knoten. DIESER TEXT REIST MIT JEDEM ZIP zu jedem, der
   das Plugin installiert. Fuer dieses Mitreisende gab es sonst keinen Ort —
   die freien Feynman-Kapitel sind pro Knoten und erreichen einen fremden SysOp
   nie, und why.md liest niemand, der gerade ein Plugin installiert hat
   (46 KB). Der Reiter ist eine andere Gattung: eine Ansprache, keine
   Begruendung.

   Der Text steht bewusst IM CODE und nicht in einer Option: Er gehoert dem
   Autor, nicht dem SysOp. Ein fremder SysOp soll ihn lesen, nicht ueberschreiben
   — er hat fuer sich selbst die Kapitel und seine Wilde.

   Zweisprachig, weil er mitreist: Ein Knotenpunkt in Bangkok kann vermutlich
   kein Deutsch. */
function cluster_one_render_ueber_mich_page(): void {
    cluster_one_page_gate();
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Feynman'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_feynman_tabs('ueber_mich'); ?>
        <div style="max-width:720px;margin-top:1.2em;line-height:1.65;">
            <p><?php esc_html_e('I am Nemesus, the developer of this plugin. People have epochs the way cultures do. In this one my rule is: no reaching out empty-handed. What I bring, you have just installed.', 'cluster-one-post-dolly'); ?></p>
            <p><?php esc_html_e('It can help you realise your own visions. I built it for mine: an app that brings together people who would otherwise never have met — one that fosters openness to the world and gets around the language barrier.', 'cluster-one-post-dolly'); ?></p>
            <p><?php esc_html_e('The plugin I could manage alone, thanks to AI. The app I will not be able to build alone. If you are a coder, a YouTuber or a lawyer, and you like what you see: write to me.', 'cluster-one-post-dolly'); ?></p>
            <p style="margin-top:1.2em;">
                <a href="https://christianermisch.com/" target="_blank" rel="noopener">christianermisch.com</a>
            </p>
        </div>
    </div>
    <?php
}

/* v0.9.58: HIER STAND DIE BLAUPAUSE (der Setup-Reiter).
   Sie ist nach kranz.php gezogen — byte-identisch, move don't rebuild.
   Grund (Christian, 21.08.2026): Sie ist ein EINMALIGER Akt beim
   Einrichten, kein Werkzeug des Lernpfads. Und sie war das letzte
   Stueck in Feynman, das etwas SCHREIBT — ohne sie ist Feynman
   reiner Text und jederzeit wegwerfbar. Siehe kranz.php. */


/* cluster_one_steckbrief_von() ist am 11.09.2026 in den BODEN gezogen (kern.php).
   Sie wurde von vier Haeusern gerufen und stand in einem schaltbaren
   Organ — W62, WELT 1. Hier steht kein Ersatz und keine Weiche:
   zwei Wege auf dieselbe Wahrheit waeren zwei Wahrheiten. */



/* cluster_one_namenstabelle() ist am 11.09.2026 in den BODEN gezogen (kern.php).
   Sie wurde von vier Haeusern gerufen und stand in einem schaltbaren
   Organ — W62, WELT 1. Hier steht kein Ersatz und keine Weiche:
   zwei Wege auf dieselbe Wahrheit waeren zwei Wahrheiten. */


function cluster_one_render_namenstabelle(): void {
    ?>
    <table class="widefat striped" style="max-width:1100px;margin-top:1.5em;">
        <thead>
            <tr>
                <th><?php esc_html_e('Name', 'cluster-one-post-dolly'); ?></th>
                <th><?php esc_html_e('Person', 'cluster-one-post-dolly'); ?></th>
                <th style="white-space:nowrap;"><?php esc_html_e('Lived', 'cluster-one-post-dolly'); ?></th>
                <th><?php esc_html_e('Why this name', 'cluster-one-post-dolly'); ?></th>
                <th><?php esc_html_e('What the name does not mean', 'cluster-one-post-dolly'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach (cluster_one_namenstabelle() as $z) : ?>
            <tr>
                <td><strong><?php echo esc_html($z[0]); ?></strong></td>
                <td><?php echo esc_html($z[1]); ?></td>
                <td style="white-space:nowrap;"><?php echo esc_html($z[2]); ?></td>
                <td><?php echo esc_html($z[3]); ?></td>
                <td style="opacity:.8;"><?php echo esc_html($z[4]); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}


/* cluster_one_help_texts() ist am 11.09.2026 in den BODEN gezogen (kern.php).
   Sie wurde von vier Haeusern gerufen und stand in einem schaltbaren
   Organ — W62, WELT 1. Hier steht kein Ersatz und keine Weiche:
   zwei Wege auf dieselbe Wahrheit waeren zwei Wahrheiten. */



/* cluster_one_help() ist am 11.09.2026 in den BODEN gezogen (kern.php).
   Sie wurde von vier Haeusern gerufen und stand in einem schaltbaren
   Organ — W62, WELT 1. Hier steht kein Ersatz und keine Weiche:
   zwei Wege auf dieselbe Wahrheit waeren zwei Wahrheiten. */




/* cluster_one_help_for_area() ist am 11.09.2026 in den BODEN gezogen (kern.php).
   Sie wurde von vier Haeusern gerufen und stand in einem schaltbaren
   Organ — W62, WELT 1. Hier steht kein Ersatz und keine Weiche:
   zwei Wege auf dieselbe Wahrheit waeren zwei Wahrheiten. */


/* v1.0.0 — DIE ZWEI KI-HELFER SIND AUSGEZOGEN, nach platon.php.
   Beschlossen am 25.08.2026 (0.9.79), ausgefuehrt im letzten Zug vor dem
   Einfrieren. cluster_one_ai_select_or_hint() und cluster_one_has_ai_identity()
   hatten je genau EINEN Aufrufer, beide in platon.php (3804/3805).
   Feynman ist Text UEBER das Haus — Steckbriefe, Namenstabelle, Hilfe. Ein
   Auswahlfeld ist Bedienung IM Haus und gehoert dorthin, wo es benutzt wird. */


/* v1.3.1 — WAS HIER FOLGT, STEHT INNERHALB DES RIEGELS.
   Der erste Entwurf haengte den Umzug und den Vertrag ans DATEIENDE — also
   hinter die schliessende Klammer von if (!function_exists(...)). Auf einem
   Knoten mit zwei Lights lag cluster_one_render_about_page() damit zweimal
   unverriegelt da, und aktivierung.php meldete in derselben Minute:

       Cannot redeclare function cluster_one_render_about_page()

   Ein Riegel schuetzt, was zwischen seinen Klammern steht — nicht, was ihm
   folgt. Wer ans Ende einer Datei anbaut, baut leicht daneben.

/* v1.3.1 — UMGEZOGEN AUS kranz.php:1174, Rumpf byte-identisch (Regel 2).
   Die Seite hiess dort 'About', trug aber cluster_one_page_h1('Feynman') und
   rief ausschliesslich Funktionen dieser Datei. Der Umzug schliesst drei offene
   Naehte, ohne eine neue zu oeffnen: page_gate() und page_h1() wohnen im Boden,
   cluster_one_fassung() auch. */
function cluster_one_render_about_page() {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    $view = (isset($_GET['view']) && $_GET['view'] === 'about') ? 'about' : 'feynman'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $base = admin_url('admin.php?page=cluster_one_about');
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Feynman'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_feynman_tabs($view); // v0.1.2: gemeinsames Band mit "Über mich" ?>
        <?php if ($view === 'feynman') { cluster_one_render_feynman_chapters(); } // v0.1.9: die Kapitel des SysOps ?>

        <?php if ($view === 'about') : ?>

            <p style="font-size:1.1em;max-width:760px;">
                <?php printf(
                    /* translators: %s: Versionsnummer von Bismarck. */
                    wp_kses_post(__('<strong>Cluster One</strong> is the builders lodge for <strong>CLU573R$</strong> — the scaffold for a federated network where humans and AIs come together as equals. Currently: <code>v%s</code>.', 'cluster-one-post-dolly')),
                    esc_html((function_exists('cluster_one_fassung') ? cluster_one_fassung() : (defined('CLUSTER_ONE_VERSION') ? (string) CLUSTER_ONE_VERSION : '')))
                ); ?>
            </p>

            <h2 style="margin-top:2em;"><?php esc_html_e('What this plugin does (so far)', 'cluster-one-post-dolly'); ?></h2>
            <p style="max-width:760px;">
                <?php esc_html_e('It connects individual WordPress nodes into a network and shows each admin, in the backend, who else is in the network, which version they run, and whether a new label or Discord room is set up. Since v0.0.9 there is also a minimal hello chat between admins.', 'cluster-one-post-dolly'); ?>
            </p>

            <h2 style="margin-top:2em;"><?php esc_html_e('Security DNA', 'cluster-one-post-dolly'); ?></h2>
            <ul style="max-width:760px;list-style:disc;margin-left:1.5em;">
                <li><strong><?php esc_html_e('A deliberately narrow write path.', 'cluster-one-post-dolly'); ?></strong> <?php esc_html_e('Only the Hello chat writes — token-secured, hard-sanitized, and it grants a peer no rights.', 'cluster-one-post-dolly'); ?></li>
                <li><strong><?php esc_html_e('One token per edge.', 'cluster-one-post-dolly'); ?></strong> <?php esc_html_e('Each connection has its own secret; no single key for all.', 'cluster-one-post-dolly'); ?></li>
                <li><strong><?php esc_html_e('Only the whether, never the what.', 'cluster-one-post-dolly'); ?></strong> <?php esc_html_e('The ping carries existence flags and shared self-info — no label content, no Discord address.', 'cluster-one-post-dolly'); ?></li>
                <li><strong><?php esc_html_e('Nothing in the frontend.', 'cluster-one-post-dolly'); ?></strong> <?php esc_html_e('The network view is visible only in the backend, only to logged-in admins.', 'cluster-one-post-dolly'); ?></li>
                <li><strong><?php esc_html_e('No reach into other nodes.', 'cluster-one-post-dolly'); ?></strong> <?php esc_html_e('A peer never gets rights in your node — saying hello is not an invitation.', 'cluster-one-post-dolly'); ?></li>
                <li><strong><?php esc_html_e('Local disconnect, no pillory.', 'cluster-one-post-dolly'); ?></strong> <?php esc_html_e('You can disconnect (and reconnect) any edge for yourself — blocked at the route, honestly stated to the node concerned, never shared across the network. No one is pre-judged for misbehaving elsewhere.', 'cluster-one-post-dolly'); ?></li>
            </ul>

            <h2 style="margin-top:2em;"><?php esc_html_e('Roadmap', 'cluster-one-post-dolly'); ?></h2>
            <ul style="max-width:760px;list-style:disc;margin-left:1.5em;">
                <li><strong><?php esc_html_e('Presence & Stealth.', 'cluster-one-post-dolly'); ?></strong> <?php esc_html_e('The light is on: whether the human behind a node is currently at the console — switchable.', 'cluster-one-post-dolly'); ?></li>
                <li><strong><?php esc_html_e('Mesh.', 'cluster-one-post-dolly'); ?></strong> <?php esc_html_e('Edges grow into a resilient network.', 'cluster-one-post-dolly'); ?></li>
            </ul>

            <p style="opacity:.7;font-style:italic;margin-top:2.5em;max-width:760px;">
                <?php printf(
                    /* translators: %s: der Satz zur Bauhuette (eigene msgid). */
                    esc_html__('Christian Ermisch (Nemesus) · License: GPL v2 or later · %s', 'cluster-one-post-dolly'),
                    esc_html__('The builders lodge for CLU573R$.', 'cluster-one-post-dolly')
                ); ?>
            </p>

        <?php else : ?>

<?php /* v0.6.9: Statt des Lernpfad-Platzhalters die Ansprache und die
   Namensliste. Der Platzhalter versprach etwas, das nicht kam — und dieser
   Reiter ist der erste, den ein fremder SysOp oeffnet.

   Die Spalte "Nicht gemeint" ist Christians Entscheidung in meiner
   Formulierung: Er wollte Winsor schlecht wegkommen lassen; die Stimme dieses
   Hauses lautet aber BEZEUGT, URTEILT NICHT. Belegte Tatsachen nennen und das
   Urteil dem Leser lassen — wie bei Bismarck, wo die Warnung im Namen seit
   jeher mitkommt. */ ?>
            <p style="font-size:1.1em;max-width:760px;">
                <?php esc_html_e('Everything here is named after people. That is not decoration: a name carries what it was chosen for — and sometimes what it was not.', 'cluster-one-post-dolly'); ?>
            </p>
            <p style="max-width:760px;opacity:.85;">
                <?php esc_html_e('Names are never translated. If you rename a part of this node, the name in the code stays as it is — so that what you read here and what a developer reads stay the same thing.', 'cluster-one-post-dolly'); ?>
            </p>
            <?php cluster_one_render_namenstabelle(); ?>

        <?php endif; ?>
    </div>
    <?php
}

/* ---------------------------------------------------------------------------
 * DER VERTRAG: WER ERKLAERT ETWAS?
 *
 * v1.3.1 — Feynman ist der meistgerufene Gerufene des Hauses. Vier Aufrufe aus
 * zwei fremden Dateien holten sich hier Text ab, jeder beim Namen:
 *
 *     bismarck.php:608   cluster_one_feynman_chapters_for_ai()
 *     bismarck.php:823   cluster_one_dna_briefing()
 *     kranz.php:1641     cluster_one_dna_briefing()
 *     kranz.php:1642     cluster_one_dna_doc_path()
 *
 * Liegt Feynman nicht daneben, ist jeder dieser Aufrufe ein Fatal. Das ist die
 * Sorte Naht, die ein Light daran hindert, allein zu stehen — und sie laesst
 * sich nicht durch Umziehen loesen, weil die Rufer zu Recht dort stehen, wo sie
 * stehen: Bismarck baut eine KI-Anfrage, der Bauplan prueft den Knoten.
 *
 *     Das Floss sagt dir, wer da ist.
 *     Es sagt dir nicht, wie du mit ihm redest.
 *
 * Also dieselbe Form wie cluster_one_kranz_abschnitte (H77), die seit dem
 * 27.08.2026 laeuft: WER ETWAS HAT, MELDET ES AN. Wer etwas will, fragt den
 * Raum und bekommt die Vorgabe, wenn niemand antwortet.
 *
 *     $c1_dna = apply_filters('cluster_one_erklaerung', '', 'dna');
 *
 * Kein Name eines fremden Organs steht mehr in einer fremden Datei. Ohne
 * Feynman ist die Antwort leer — und leer ist an jeder dieser vier Stellen
 * schon heute der Fall, den der Code behandelt ("kein Framework, kein Marker,
 * kein Text: dann bleibt es leer und niemand erfindet etwas").
 *
 * BENANNTE GRENZE: Der Vertrag gibt TEXT und PFADE, keine Objekte und keine
 * Rueckrufe. Wer mehr braucht, braucht kein Erklaerstueck, sondern ein Organ —
 * und dann ist die ehrliche Antwort eine Abhaengigkeit, keine Umgehung.
 * ------------------------------------------------------------------------- */
add_filter('cluster_one_erklaerung', function ($c1_wert, $c1_was = '') {
    switch ((string) $c1_was) {
        case 'dna':        return cluster_one_dna_briefing();
        case 'dna-pfad':   return cluster_one_dna_doc_path();
        case 'kapitel':    return cluster_one_feynman_chapters_for_ai();
    }
    return $c1_wert;
}, 10, 2);

}
