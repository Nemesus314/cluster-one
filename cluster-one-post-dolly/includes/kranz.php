<?php
/**
 * Cluster One — Modul kranz (DER SCHNITT, v0.0.100 -> Module).
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
/* VERTRAG 27 §4 — DIE MELDUNG STEHT VOR DEM RIEGEL.
   Bis hierher stand cluster_one_hier() INNERHALB des Riegels: eine verdraengte
   Kopie dieser Datei schwieg dann auch ueber ihre eigene Existenz. Die
   Bedingung wird jetzt EINMAL gelesen und traegt beide Folgen — die Meldung
   und den Riegel.
       Ein Melder kennt sich selbst. „Verdraengt" ist eine BEZIEHUNG, und
       Beziehungen gehoeren dem Sammler.            (Maillart, 11.09.)
   Die Fassung bleibt LEER: Kasper liest sie am Wirt (H118), und eine
   geliehene Nummer ist schlimmer als keine. */
/* v1.12.0 — DER RIEGEL FRAGTE NACH EINEM MIETER, DER AUSGEZOGEN IST.
   cluster_one_render_kranz_tabs() ist am 11.09. in den BODEN gezogen
   (kern.php:4514) — mein eigener Umzug. Seitdem fragte dieser Riegel nach
   einer Funktion, die diese Datei NICHT MEHR DEKLARIERT:

       liegt kern.php daneben   -> die Funktion gibt es, der Riegel ist IMMER zu
       liegt kern.php nicht da  -> es gibt sie nie, der Riegel ist IMMER offen
                                   und die zweite Kopie stirbt an
                                   „Cannot redeclare cluster_one_render_system_page()"

       Ein Riegel, der nach einer Funktion fragt, die ausgezogen ist, fragt
       nach dem Mieter statt nach dem Haus.

   Gefragt wird ab jetzt nach der ersten Funktion, die diese Datei SELBST
   deklariert. */
$cluster_one_kranz_laeuft = !function_exists('cluster_one_render_system_page');

/* 18.10.2026 (Neo): Riegel um den Aufruf, wie in kern.php und speicher.php. */
if (function_exists('cluster_one_hier')) {
    cluster_one_hier('kranz', '', $cluster_one_kranz_laeuft);
}

if ($cluster_one_kranz_laeuft) {


/* cluster_one_render_kranz_tabs() ist am 11.09.2026 in den BODEN gezogen (kern.php).
   Sie wurde von vier Haeusern gerufen und stand in einem schaltbaren
   Organ — W62, WELT 1. Hier steht kein Ersatz und keine Weiche:
   zwei Wege auf dieselbe Wahrheit waeren zwei Wahrheiten. */


/** System-Übersicht (v0.0.58): bewusst KEINE Konfiguration — kein einziges
   Formularfeld, kein Knopf, keine Einstellung. System ist als neutraler,
   nie urteilender Zeuge angelegt; wäre es konfigurierbar (welche Ereignisse
   gemeldet werden, ob überhaupt), bekäme es genau die Eigenschaft, die es
   bewusst nie haben sollte — man könnte es sich passend zurechtstellen.
   Das Tagebuch-Lesen-Beispiel zeigt das am deutlichsten: „SysOp hat das
   Lesen eingeschaltet" schafft gerade WEIL sie nicht abschaltbar ist
   Vertrauen. Diese Seite zeigt nur, in drei sauber getrennten Kategorien
   (echte Nachricht / bezeugter Vermerk / stille Leerung), was es heute
   schon tut — eine lebendige Bestandsaufnahme, die mit jeder Version
   mitwächst, kein zeitloses Feynman-Tutorial. */
/** Eine Raeumer-Rueckgabe als lesbare Zeile — und sie hat einen NAMEN,
 * weil sie vorher keinen hatte.
 *
 * CHRISTIAN HAT ES AUF DEM SCHIRM GEFUNDEN, 17.09.2026, erste Runde:
 *
 *     bismarck: einstellungen: 4 · strittig: 1
 *
 * „strittig" ist bei Bismarck kein Zaehler, sondern ein verschachteltes
 * Feld mit fuenf Eintraegen — vier Zahlen und eine Begruendung. Die alte
 * Zeile schrieb `(int) $wert`, und PHP macht aus einem Array mit fuenf
 * Eintraegen still eine 1.
 *
 *     Eine Zahl, die aus einer Typumwandlung stammt, sieht genauso
 *     aus wie eine gemessene. Sie hat nur nichts gemessen.
 *
 * Und der eigentliche Fehler liegt eine Ebene tiefer: die Zeile stand als
 * Schleife MITTEN IM HTML. Kein Zeuge konnte sie anfassen.
 *
 *     Eine Darstellung ohne Namen kann kein Zeuge anfassen.
 *                                                   (M100, 17.09.2026)
 *
 * WAS SIE TUT
 *     flach        was => 3          ->  „was: 3"
 *     eine Ebene   was => array(…)   ->  aufgeklappt: „was/innen: 3"
 *     zu tief      was => array(array(…))  ->  „was: (nicht zaehlbar)"
 *     alles null / leer               ->  „nichts"
 *
 *     UND EIN TEXT, JE NACH EBENE — die beiden sind NICHT dasselbe,
 *     und M100 Fassung 1 hat sie verwechselt:
 *         OBERSTE Ebene   was => „Text"   ->  „was: (keine Zahl)"
 *             Eine Abweichung von der Raeumer-Form gehoert gesehen,
 *             nicht weggeraeumt.
 *         INNERHALB       …/warum => „…"  ->  weggelassen
 *             Dort ist ein Text eine Begruendung im Zusammenhang —
 *             Bismarcks 'warum'. Sie gehoert nicht in eine Zahlenzeile.
 *
 * Das letzte ist Kleinrocks Satz vom 17.09., und er gilt hier genauso:
 *     „Wer etwas stehen laesst, nennt es nicht mit einer Null,
 *      sondern gar nicht."
 * Damit sagen Crockford (der nichts findet) und Post Dolly (der nichts
 * findet) DASSELBE, statt einmal Nullen und einmal „nichts".
 *
 * @param array $c1_was  die Rueckgabe EINES Raeumers
 * @return string        die Zeile, ungeschuetzt — der Rufer escaped
 */
function cluster_one_zahlenzeile(array $c1_was): string {
    $c1_teile = array();
    $c1_summe = 0;

    $c1_zahl = static function ($c1_v): bool {
        return is_int($c1_v) || is_float($c1_v)
            || (is_string($c1_v) && $c1_v !== '' && ctype_digit($c1_v));
    };

    foreach ($c1_was as $c1_w => $c1_n) {
        $c1_w = (string) $c1_w;

        if ($c1_zahl($c1_n)) {
            $c1_teile[] = $c1_w . ': ' . (int) $c1_n;
            $c1_summe  += (int) $c1_n;
            continue;
        }

        if (is_array($c1_n)) {
            /* EINE Ebene wird aufgeklappt, nicht zwei. Zwei waeren ein
               Baum in einer Zeile, und ein Baum in einer Zeile ist keiner. */
            $c1_tiefer = false;
            foreach ($c1_n as $c1_iw => $c1_in) {
                if ($c1_zahl($c1_in)) {
                    $c1_teile[] = $c1_w . '/' . (string) $c1_iw . ': ' . (int) $c1_in;
                    $c1_summe  += (int) $c1_in;
                } elseif (is_array($c1_in)) {
                    $c1_tiefer = true;
                }
                /* Text faellt weg — Bismarcks 'warum' ist eine Begruendung. */
            }
            if ($c1_tiefer) {
                $c1_teile[] = $c1_w . ': ' . __('(not countable)', 'cluster-one-post-dolly');
            }
            continue;
        }

        /* weder Zahl noch Feld: benennen statt casten. */
        $c1_teile[] = $c1_w . ': ' . __('(no number)', 'cluster-one-post-dolly');
    }

    if ($c1_teile === array() || $c1_summe === 0) {
        return __('nothing', 'cluster-one-post-dolly');
    }
    return implode(' · ', $c1_teile);
}

function cluster_one_render_system_page(): void {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Kranz'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_kranz_tabs('cluster_one_system'); ?>
        <?php cluster_one_render_reset_notice(); // v0.1.5: die Narbe, falls geräumt wurde ?>
        <p style="max-width:760px;margin-top:1em;opacity:.85;"><?php esc_html_e('A read-only overview — nothing here is configurable. System bears witness, it never judges; making it configurable would let it be arranged to say what someone wants, which is exactly what it must never do.', 'cluster-one-post-dolly'); ?></p>

        <h2 style="margin-top:1.5em;"><?php esc_html_e('System messages', 'cluster-one-post-dolly'); ?></h2>
        <p class="description" style="max-width:760px;"><?php esc_html_e('Their own line in the conversation, authored literally as “System”.', 'cluster-one-post-dolly'); ?></p>
        <ul style="max-width:700px;">
            <li><?php esc_html_e('“AI [name] was pinged.” — Bismarck rooms, before every reply attempt.', 'cluster-one-post-dolly'); ?></li>
            <li><?php esc_html_e('“AI [name] is not reachable right now.” — Bismarck rooms, on failure.', 'cluster-one-post-dolly'); ?></li>
            <li><?php esc_html_e('“User [name] was pinged.” — Bismarck rooms, via the ping button.', 'cluster-one-post-dolly'); ?></li>
            <li><?php esc_html_e('“SysOp entered the diary.” — Platon AI diaries, witnessed in the linked chat on every visit (throttled per session).', 'cluster-one-post-dolly'); ?></li>
        </ul>

        <h2 style="margin-top:1.5em;"><?php esc_html_e('Witnessed notes on existing posts', 'cluster-one-post-dolly'); ?></h2>
        <p class="description" style="max-width:760px;"><?php esc_html_e('Not a message of its own — a small, visible note attached to a post that already exists.', 'cluster-one-post-dolly'); ?></p>
        <ul style="max-width:700px;">
            <li><?php esc_html_e('“Edited by Admin at [time].” — Platon, only when SysOp edits someone else’s post (not shown for Wiki areas, where any edit by anyone is expected and normal).', 'cluster-one-post-dolly'); ?></li>
        </ul>

        <h2 style="margin-top:1.5em;"><?php esc_html_e('Silent removal', 'cluster-one-post-dolly'); ?></h2>
        <p class="description" style="max-width:760px;"><?php esc_html_e('No note at all is added — the one case worth naming precisely because it is silent, not because it announces itself.', 'cluster-one-post-dolly'); ?></p>
        <ul style="max-width:700px;">
            <li><?php esc_html_e('When SysOp removes someone else’s Platon post, the author and timestamp stay exactly as they were — only the content is emptied. No “deleted by admin” label exists; the emptiness itself is the only visible trace.', 'cluster-one-post-dolly'); ?></li>
        </ul>
    
<?php if (cluster_one_is_sysop()) : // v0.1.9: eigene Feynman-Kapitel — Steuerung hier, Wissen in Feynman ?>
        <h2 style="margin-top:2.5em;"><?php esc_html_e('Your own Feynman chapters', 'cluster-one-post-dolly'); ?></h2>
        <p class="description" style="max-width:760px;"><?php esc_html_e('Give the people and the AIs on this node your own context — tools you use, conventions you keep, anything worth knowing here. Shown in Feynman, read-only. The switch decides whether the AIs are given the chapter as well; whoever reads it sees that decision.', 'cluster-one-post-dolly'); ?></p>
        <table class="widefat" style="max-width:820px;margin:.8em 0;"><tbody>
<?php foreach (cluster_one_get_feynman_chapters() as $c1_ki => $c1_kap) : ?>
            <tr><td>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('cluster_one_feynman_chapter'); ?>
                    <input type="hidden" name="action" value="cluster_one_feynman_chapter">
                    <input type="hidden" name="idx" value="<?php echo esc_attr((string) $c1_ki); ?>">
                    <p><input name="title" type="text" class="regular-text" value="<?php echo esc_attr((string) ($c1_kap['title'] ?? '')); ?>" placeholder="<?php esc_attr_e('Title', 'cluster-one-post-dolly'); ?>"></p>
                    <p><textarea name="body" rows="4" style="width:100%;max-width:760px;"><?php echo esc_textarea((string) ($c1_kap['body'] ?? '')); ?></textarea></p>
                    <p><input name="link" type="url" class="regular-text" value="<?php echo esc_attr((string) ($c1_kap['link'] ?? '')); ?>" placeholder="https://"></p>
                    <p><label><input name="for_ai" type="checkbox" value="1" <?php checked(!empty($c1_kap['for_ai'])); ?>> <?php esc_html_e('Give this chapter to the AIs as well', 'cluster-one-post-dolly'); ?></label></p>
                    <?php submit_button(__('Save', 'cluster-one-post-dolly'), 'secondary', 'op_save', false); ?>
                    <?php submit_button(__('Delete', 'cluster-one-post-dolly'), 'delete', 'op_delete', false); ?>
                </form>
            </td></tr>
<?php endforeach; ?>
            <tr><td>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('cluster_one_feynman_chapter'); ?>
                    <input type="hidden" name="action" value="cluster_one_feynman_chapter">
                    <input type="hidden" name="idx" value="new">
                    <p><input name="title" type="text" class="regular-text" value="" placeholder="<?php esc_attr_e('New chapter — title', 'cluster-one-post-dolly'); ?>"></p>
                    <p><textarea name="body" rows="3" style="width:100%;max-width:760px;" placeholder="<?php esc_attr_e('Text (optional)', 'cluster-one-post-dolly'); ?>"></textarea></p>
                    <p><input name="link" type="url" class="regular-text" value="" placeholder="https://"></p>
                    <p><label><input name="for_ai" type="checkbox" value="1"> <?php esc_html_e('Give this chapter to the AIs as well', 'cluster-one-post-dolly'); ?></label></p>
                    <?php submit_button(__('Add chapter', 'cluster-one-post-dolly'), 'secondary', 'op_save', false); ?>
                </form>
            </td></tr>
        </tbody></table>
<?php endif; ?>

<?php if (cluster_one_is_sysop()) : // v0.1.5: Knoten-Reset. Bleibt dauerhaft sichtbar — er versteckt sich nicht. ?>
        <h2 style="margin-top:2.5em;color:#b32d2e;"><?php esc_html_e('Reset this node', 'cluster-one-post-dolly'); ?></h2>
        <p class="description" style="max-width:760px;"><?php esc_html_e('Removes all content — rooms, chats, threads, posts, labels, meetings, guest records and hello chats. Keeps the machine room: node name, SysOp, areas, whitelist, profile, connectors, AI identities, modes. Edges are kept too: an edge belongs to two nodes, and deleting it here would quietly damage the other side.', 'cluster-one-post-dolly'); ?></p>
        <p class="description" style="max-width:760px;"><strong><?php esc_html_e('This is not undoable, and it does not hide: every person with access sees a line saying this node was reset, with the date and the counts. Same rule as an admin editing someone else’s post.', 'cluster-one-post-dolly'); ?></strong></p>
<?php
        /* v1.12.8 (17.09.2026) — DER TROCKENLAUF.
           Der Knopf sagt VORHER, was er nehmen wuerde. Gezaehlt, nicht
           versprochen: dieselben Zeilen, die beim Druecken laufen, mit
           $tun = false. Ein Knopf, der nicht sagen kann, was er kostet,
           ist eine Zusage statt einer Messung.

           UND DIE PRODUKTE STEHEN EINZELN DA. Wer „Knoten zuruecksetzen"
           liest, denkt an Raeume und Themen — nicht an Zitate, die einem
           anderen Haus gehoeren. Seit 1.12.8 gehen sie mit. Also muessen
           sie hier stehen, mit Namen und Zahl.        (NACHTRAG 19a §3) */
        /* NICHT NACH EINEM PARAMETER FRAGEN, SONDERN NACH EINEM NAMEN.
           Ein altes kern.php nimmt cluster_one_node_reset(false) entgegen
           und RAEUMT — der Probelauf waere der Ernstfall. Gemessen, w95
           Richtung 1 gegen die ausgelieferte Kopie. Und die Paarung ist
           moeglich: pacioli traegt kein kranz.php, also koennen die beiden
           Bodendateien auf einem Knoten aus verschiedenen Paketen kommen. */
        $c1_trocken = function_exists('cluster_one_node_reset_trocken')
            ? cluster_one_node_reset_trocken()
            : null;
        if (!is_array($c1_trocken)) :
?>
        <p class="description" style="max-width:760px;">
            <?php esc_html_e('This node is running an older core that cannot count without deleting, so there is no preview. The button still works. Install the remaining Cluster One products to get it.', 'cluster-one-post-dolly'); ?>
        </p>
<?php
        else :
        $c1_boden   = array(
            'rooms'         => __('rooms', 'cluster-one-post-dolly'),
            'threads'       => __('threads', 'cluster-one-post-dolly'),
            'events'        => __('meetings', 'cluster-one-post-dolly'),
            'humans'        => __('guest records', 'cluster-one-post-dolly'),
            'chats'         => __('hello chats', 'cluster-one-post-dolly'),
            'einstellungen' => __('cached node settings', 'cluster-one-post-dolly'),
        );
        /* v1.12.9 — WORT ZUERST, ZAHL DAHINTER. Holleriths Wortfrage vom
           17.09.: heisst „humans" auf deutsch „Gastdaten" oder
           „Gastdatensaetze"? Er hat gefragt, statt zu waehlen, und damit
           einen Fehler von MIR gefunden, nicht von sich.

               „4 Gastdaten" ist falsch. „1 Gastdatensaetze" auch.
               Wer eine Zahl vor ein Hauptwort setzt, verlangt von ihm
               eine Mehrzahlform — und gibt ihm keine.

           Richtig waere ein _n() je Zeile: sechs Rufe, zwoelf neue
           Katalogeintraege, und eine weitere Runde fuer sechs Woerter.
           Billiger und in BEIDEN Sprachen besser: die Zahl hinter das
           Wort. „Gastdaten: 4" verlangt keine Beugung, „rooms: 2"
           genauso wenig.

               Eine Uebersetzungsfrage, die sich nicht beantworten
               laesst, ist manchmal keine Frage an den Uebersetzer.
               Sie ist ein Satzbau, der keine gute Antwort zulaesst.

           Kein Katalogeintrag aendert sich dadurch: die sechs Woerter
           bleiben dieselben msgid. Nur die Reihenfolge im Satz aendert
           sich, und die steht im Code. */
        $c1_zeilen = array();
        foreach ($c1_boden as $c1_k => $c1_wort) {
            $c1_zeilen[] = $c1_wort . ': ' . (int) ($c1_trocken[$c1_k] ?? 0);
        }
        $c1_prod = is_array($c1_trocken['produkte'] ?? null) ? $c1_trocken['produkte'] : array();
?>
        <p class="description" style="max-width:760px;">
            <strong><?php esc_html_e('What this button would take right now:', 'cluster-one-post-dolly'); ?></strong><br>
            <?php echo esc_html(implode(' · ', $c1_zeilen)); ?>
        </p>
<?php   if ($c1_prod !== array()) : ?>
        <p class="description" style="max-width:760px;">
            <strong><?php esc_html_e('And from the products installed here — each through its own hand:', 'cluster-one-post-dolly'); ?></strong><br>
<?php       foreach ($c1_prod as $c1_name => $c1_was) :
                $c1_zeile = cluster_one_zahlenzeile(is_array($c1_was) ? $c1_was : array());
?>
            <?php echo esc_html((string) $c1_name); ?>:
            <?php echo esc_html($c1_zeile); ?><br>
<?php       endforeach; ?>
        </p>
<?php   else : ?>
        <p class="description" style="max-width:760px;">
            <?php esc_html_e('No product on this node has registered a cleaner. Their own data would stay — this button only takes what it can name an owner for.', 'cluster-one-post-dolly'); ?>
        </p>
<?php   endif;
        endif; ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:1em;">
            <?php wp_nonce_field('cluster_one_node_reset'); ?>
            <input type="hidden" name="action" value="cluster_one_node_reset">
            <p><label><?php esc_html_e('Type RESET to confirm:', 'cluster-one-post-dolly'); ?>
                <input name="confirm" type="text" autocomplete="off" style="width:8em;"></label></p>
            <?php submit_button(__('Reset node', 'cluster-one-post-dolly'), 'delete', 'submit', false); ?>
        </form>
<?php endif; ?>
    </div>
    <?php
}

/** Persönlicher Bereich (v0.0.50): personalisierte Sicht auf alle privaten
   Platon-Themen und Bismarck-Räume, bei denen der aktuell eingeloggte
   Mensch auf der Sichtbarkeits-Liste steht. Die normalen, gemeinsamen
   Listen (Bismarck → Forum/Räume) zeigen NIE etwas Privates — die
   Sichtbarkeits-Prüfung sitzt komplett hier, an einer einzigen Stelle. */
function cluster_one_render_personal_area_page(): void {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Kranz'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_kranz_tabs('cluster_one_personal_area'); ?>
        <?php /* 27.10.2026 (Neo), Boden 1.17.2 — DER SATZ FRAGT, OB BISMARCK DA IST.
           Test 5 (Einzelprodukt, DEV, nur Post Dolly aktiv): die Seite versprach
           Bismarck-Listen, Tagebuecher und Briefe, und darunter kam nichts —
           der Platz 'persoenlich' blieb leer, weil niemand ihn fuellte. Ein
           Text, der mehr sagt als der Knoten haelt. Gefragt wird das Organ
           (cluster_one_organ_wirt), wie beim Menue; ohne Bismarck steht hier,
           was fehlt und wo es wohnt. Der alte Satz bleibt fuer den Fall MIT
           Bismarck woertlich stehen. */
        if (function_exists('cluster_one_organ_wirt') && cluster_one_organ_wirt('bismarck') === '') : ?>
        <p style="max-width:720px;opacity:.85;"><?php esc_html_e('This node runs without Bismarck. Private threads, rooms, diaries and letters live there — until Bismarck is installed, this page stays empty.', 'cluster-one-post-dolly'); ?></p>
        <?php else : ?>
        <p style="max-width:720px;opacity:.85;"><?php esc_html_e('Private threads and rooms live in the Bismarck lists — visible only to those on their list. Diaries live there too. This is the home for personal matters — starting with your letters.', 'cluster-one-post-dolly'); ?></p>
        <?php endif; ?>
        <?php /* v1.3.4 — EIN PLATZ STATT EINES NAMENS. Bis hierher rief diese
           Seite pn.php beim Namen; ohne Bismarck war das ein Fatal. Es ist
           derselbe Fall wie H77 beim Kranz, nur eine Seite weiter: WER ETWAS
           HAT, MELDET ES AN. Der Platz heisst, wo er ist — nicht, wer ihn
           fuellt. */
        /* 28.10.2026 (Neo), Boden 1.19.0: MEIN SPEICHER steht VOR dem Platz — er
           gehoert dem Boden, nicht einem Produkt, und die Produkte darunter
           (Merkliste) verweisen nach oben auf ihn. */
        cluster_one_render_speicher_abschnitt();
        do_action('cluster_one_abschnitt', 'persoenlich'); ?>

    </div>
    <?php
}

/* ---------------------------------------------------------------------------
   28.10.2026 (Neo), Boden 1.19.0 — DER ABSCHNITT „MEIN SPEICHER".

   Die Tueren stehen in kern.php (cluster_one_speicher_*); hier steht, was der
   Mensch sieht: Adresse, Benutzer, Passwort, „Zugang pruefen", der Pruefstand,
   „Zugang vergessen" und die Liste „Was dort liegt". Das Formular ist das der
   Merkliste (0.2.55, Bismarck) – umgezogen mit seinen Saetzen, weil die Saetze
   belegt sind (GMX: volle E-Mail-Adresse und App-Passwort; All-Inkl spricht kein
   WebDAV). merkliste.php zeigt es ab 0.2.62 nicht mehr selbst.

   DER RIEGEL GEGEN EINEN AELTEREN KERN: kranz.php und kern.php reisen in vier
   Paketen, aber Pacioli traegt nur kasper.php – die beiden koennen auf einem
   Knoten aus verschiedenen Paketen kommen (siehe den Reset-Knopf oben, w95).
   Fehlt die Tuer, fehlt der Abschnitt, und es steht da, warum.
   --------------------------------------------------------------------------- */
function cluster_one_speicher_meldung(): void {
    if (!isset($_GET['speicher'])) { return; } // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $c1_c = sanitize_key((string) wp_unslash($_GET['speicher'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $c1_d = isset($_GET['speicher_detail']) ? sanitize_text_field((string) wp_unslash($_GET['speicher_detail'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $c1_ok = array(
        'geprueft'  => __('Your storage answered. Products that hold data of yours can now keep it there.', 'cluster-one-post-dolly'),
        'vergessen' => __('Access data forgotten. Nothing in your storage was touched.', 'cluster-one-post-dolly'),
    );
    $c1_warn = array(
        'unvollstaendig'   => __('Address (https), user name and password are needed.', 'cluster-one-post-dolly'),
        'abgelehnt'        => __('Your storage refused the access data.', 'cluster-one-post-dolly'),
        'nicht_erreichbar' => __('Your storage did not answer.', 'cluster-one-post-dolly'),
        'kein_tresor'      => __('No key vault on this node: the password cannot be kept.', 'cluster-one-post-dolly'),
        'belegt'           => __('Something of yours still lies in that storage. Bring it back first; then the access can be forgotten.', 'cluster-one-post-dolly'),
    );
    if (isset($c1_ok[$c1_c])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($c1_ok[$c1_c]) . '</p></div>';
    } elseif (isset($c1_warn[$c1_c])) {
        echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html($c1_warn[$c1_c]) . ($c1_d !== '' ? ' ' . esc_html($c1_d) : '') . '</p></div>';
    }
}

function cluster_one_render_speicher_abschnitt(): void {
    $c1_uid = (int) get_current_user_id();
    if ($c1_uid <= 0) { return; }
    ?>
    <div style="max-width:860px;">
        <h2 style="margin-top:1.6em;"><?php echo esc_html__('My storage', 'cluster-one-post-dolly');
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_help_bubble() gibt fertiges HTML zurueck (esc_html im Innern)
            if (function_exists('cluster_one_help_bubble')) { echo cluster_one_help_bubble(__('A storage of your own, outside this node: a folder that speaks WebDAV over https. Products that hold data of yours — the invite list, your AI export — can keep it there instead of in this WordPress. The node keeps the address; the password lies in the key vault, and only you can read it. The storage is yours; what the node can read there depends on the file, and the list below says which.', 'cluster-one-post-dolly')); } ?></h2>
        <?php cluster_one_speicher_meldung(); ?>
<?php if (!function_exists('cluster_one_speicher_zugang')) : ?>
        <p style="opacity:.75;max-width:680px;"><?php esc_html_e('The floor of this node is older than this page: it has no door to a storage of yours yet. Update the packages that carry the floor.', 'cluster-one-post-dolly'); ?></p>
    </div>
    <?php return; endif;
    $c1_neu    = cluster_one_speicher_uebernahme($c1_uid);
    $c1_z      = cluster_one_speicher_zugang($c1_uid);
    $c1_tresor = cluster_one_speicher_tresor_da();
    $c1_belegt = cluster_one_speicher_belegt($c1_uid);
    if ($c1_neu) : ?>
        <div class="notice notice-info inline"><p><?php esc_html_e('Your storage was taken over from the invite list. Address and password are now kept once, for every product; nothing else changed.', 'cluster-one-post-dolly'); ?></p></div>
<?php endif;
    if (!$c1_tresor) : ?>
        <p style="opacity:.75;max-width:680px;"><?php esc_html_e('Keeping a storage of your own needs the key vault (Pacioli) to hold the password; this node does not have it.', 'cluster-one-post-dolly'); ?></p>
<?php   /* 30.10.2026 (Neo), Boden 1.19.4 — DIE ADRESSE VERSCHWINDET NICHT MIT DEM TRESOR. Nemesus'
           Szenario: ein Knoten mit einem Produkt und Pacioli, der SysOp loescht Pacioli. Das Passwort
           geht mit dem Tresor, die Adresse steht weiter im Meta – und bis 1.19.3 stand hier nur der
           Satz oben, ohne sie. Ein Ordner mit Dateien des Menschen, von dem der Knoten nichts mehr
           sagt, ist ein stilles Verschwinden. Jetzt steht die Adresse da, und was dort liegt, liegt
           weiter dort. */
        if ($c1_z['url'] !== '') : ?>
        <p style="max-width:680px;"><?php echo esc_html(sprintf(
            /* translators: %s: WebDAV address */
            __('Your storage is on record at %s. Without the vault this node cannot hold its password, so it does not reach it — but whatever lies there still lies there.', 'cluster-one-post-dolly'), $c1_z['url'])); ?></p>
<?php   endif; ?>
<?php else : ?>
        <p style="opacity:.75;max-width:680px;"><?php esc_html_e('A storage that speaks WebDAV over https (a Nextcloud, a Hetzner Storage Box, the GMX or Web.de cloud). Enter the address of a folder, then check the access. GMX and Web.de take the full e-mail address as user name and an app password, not the account password. A plain hosting folder usually does not speak WebDAV; All-Inkl’s network drive, for one, does not.', 'cluster-one-post-dolly'); ?></p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:680px;">
            <?php wp_nonce_field('cluster_one_speicher'); ?>
            <input type="hidden" name="action" value="cluster_one_speicher">
            <p><input type="url" name="url" class="regular-text" style="width:100%;" placeholder="https://cloud.example.org/remote.php/dav/files/me/cluster-one/" value="<?php echo esc_attr($c1_z['url']); ?>"></p>
            <p style="display:flex;gap:.5em;flex-wrap:wrap;">
                <input type="text" name="benutzer" placeholder="<?php echo esc_attr__('User name', 'cluster-one-post-dolly'); ?>" value="<?php echo esc_attr($c1_z['benutzer']); ?>" style="flex:1 1 10em;" autocomplete="off">
                <input type="password" name="passwort" placeholder="<?php echo esc_attr($c1_z['pass'] !== '' ? __('Password (kept; leave empty to keep it)', 'cluster-one-post-dolly') : __('Password or app password', 'cluster-one-post-dolly')); ?>" style="flex:1 1 12em;" autocomplete="new-password">
                <button type="submit" name="op" value="pruefen" class="button"><?php esc_html_e('Check access', 'cluster-one-post-dolly'); ?></button>
            </p>
<?php   if ($c1_z['url'] !== '') : ?>
            <p>
<?php       if ($c1_z['geprueft'] > 0) : ?>
                <span style="color:#1a7f37;">✓</span> <?php echo esc_html(sprintf(
                    /* translators: %s: date and time */
                    __('Access checked on %s.', 'cluster-one-post-dolly'), date_i18n(get_option('date_format') . ' H:i', $c1_z['geprueft'])));
            else : ?>
                <span style="color:#b32d2e;">✗</span> <?php esc_html_e('The last check failed; the address is kept, the access is not in use.', 'cluster-one-post-dolly');
            endif;
            /* „Vergessen" steht, sobald eine Adresse da ist und nichts dort liegt – auch nach einer
               gescheiterten Pruefung, sonst kaeme der Mensch eine falsche Adresse nie wieder los. */
            if ($c1_belegt === array()) : ?>
                <button type="submit" name="op" value="vergessen" class="button-link" style="margin-left:.6em;color:#8a8a8a;"><?php esc_html_e('Forget access', 'cluster-one-post-dolly'); ?></button>
<?php       endif; ?>
            </p>
<?php   endif; ?>
        </form>
<?php   if ($c1_z['geprueft'] > 0) :
            /* 30.10.2026 (Neo), Boden 1.19.1 — DIE LISTE HAELT, WAS DIE UEBERSCHRIFT VERSPRICHT.
               Bis 1.19.0 stand hier nur, was Produkte halten, und darunter der Satz „Exports you put
               there yourself are not listed: they are yours and stay readable without this node."
               Nemesus auf der Main: der Export lag dort und fehlte in „Was dort liegt". Jetzt zwei
               Sorten Zeilen: was der Knoten haelt (Schluessel bei ihm, Riegel fuers Vergessen) und
               was sonst im Ordner liegt (deins, der Knoten liest es nicht, kein Riegel). Der Blick in
               den Ordner kostet einen Gang zum Speicher, darum erst auf Knopfdruck (?speicher_ordner)
               – die Seite bleibt ohne Netz schnell. Nemesus' Wahl: „Liste erweitern", nicht nur die
               Ueberschrift aendern. */
            /* 30.10.2026 (Neo), Boden 1.19.2 — DIE LISTE STEHT SOFORT. Bis 1.19.1 stand hier ein Knopf
               „In den Ordner sehen" (?speicher_ordner=1), und erst danach fragte die Seite den Speicher:
                   $c1_ordner_an = isset($_GET['speicher_ordner']);
                   … <a class="button" href="…speicher_ordner=1">Look into the folder</a>
                   „Shown without asking: what products of this node keep for you. Looking into the
                    folder asks your storage once."
               Nemesus: „Ist die Nachfrage nicht ein wenig uebertrieben?" Ja – die Merkliste holt ihre
               Datei ohnehin bei jedem Oeffnen aus dem Speicher, wenn sie dort liegt; ein zweiter Gang
               in derselben Liga aendert nichts Spuerbares. Der Knopf heisst jetzt „Aktualisieren" und
               laedt die Seite neu. */
            /* 30.10.2026 (Neo), Boden 1.19.3 — DIE TABELLE IST EINE FUNKTION GEWORDEN. Nemesus: „Ganze
               Tabelle" auch bei Pacioli, weil von Pacioli aus kein Reiter zum Persoenlichen Bereich
               fuehrt, solange Bismarck ihn beansprucht. Zwei Orte, EINE Quelle: der Block, der bis 1.19.2
               hier stand, ist woertlich nach cluster_one_render_speicher_tabelle() gezogen; Pacioli ruft
               dieselbe Funktion (function_exists-Riegel). */
            $c1_ordner_url = admin_url('admin.php?page=cluster_one_personal_area');
            if (function_exists('cluster_one_mit_herkunft')) { $c1_ordner_url = cluster_one_mit_herkunft($c1_ordner_url); }
            cluster_one_render_speicher_tabelle($c1_uid, $c1_ordner_url);
        endif;
    endif; ?>
    </div>
    <?php
}

/** 30.10.2026 (Neo), Boden 1.19.3: „Was dort liegt" – die Tabelle ueber den Speicher eines Menschen.
 *  Oben, was Produkte des Knotens halten (Filter cluster_one_speicher_belegt; der Knoten hat den Schluessel),
 *  darunter alles Uebrige im Ordner (deins; der Knoten liest es nicht). Zwei Rufer: der Persoenliche Bereich
 *  (oben) und Paciolis „Meine KIs" (0.8.42). $c1_aktualisieren_url: wohin „Aktualisieren" fuehrt – die Seite
 *  des Rufers; leer = kein Knopf. Ohne die Tueren im Kern (aelterer Boden) steht nichts da, und es steht, warum. */
function cluster_one_render_speicher_tabelle(int $c1_uid, string $c1_aktualisieren_url = ''): void {
    if ($c1_uid <= 0) { return; }
    if (!function_exists('cluster_one_speicher_zugang') || !function_exists('cluster_one_speicher_belegt')) {
        echo '<p style="opacity:.75;max-width:680px;">' . esc_html__('The floor of this node is older than this page: it has no door to a storage of yours yet. Update the packages that carry the floor.', 'cluster-one-post-dolly') . '</p>';
        return;
    }
    $c1_z = cluster_one_speicher_zugang($c1_uid);
    if ($c1_z['url'] === '' || $c1_z['geprueft'] <= 0) { return; }   /* kein geprüfter Speicher: keine Tabelle (die Seite oben sagt, was fehlt) */
    $c1_belegt    = cluster_one_speicher_belegt($c1_uid);
    $c1_ordner    = function_exists('cluster_one_speicher_liste')
        ? cluster_one_speicher_liste($c1_uid)
        : array('dateien' => array(), 'fehler' => '');
    $c1_bekannt   = array();
    foreach ($c1_belegt as $c1_b) { $c1_bekannt[] = $c1_b['datei']; }
    $c1_sonst     = array_values(array_diff((array) $c1_ordner['dateien'], $c1_bekannt));
    ?>
        <h3 style="margin-top:1.2em;"><?php esc_html_e('What lies there', 'cluster-one-post-dolly'); ?>
<?php   if ($c1_aktualisieren_url !== '') : ?>
            <a class="button button-small" style="margin-left:.8em;vertical-align:middle;" href="<?php echo esc_url($c1_aktualisieren_url); ?>"><?php esc_html_e('Refresh', 'cluster-one-post-dolly'); ?></a>
<?php   endif; ?>
        </h3>
<?php   if ($c1_ordner['fehler'] !== '') : ?>
        <div class="notice notice-error inline" style="max-width:680px;"><p><?php echo esc_html($c1_ordner['fehler']); ?></p></div>
<?php   endif;
        if ($c1_belegt === array() && $c1_sonst === array()) : ?>
        <p style="opacity:.75;max-width:680px;"><?php esc_html_e('Nothing a product of this node keeps for you.', 'cluster-one-post-dolly');
            if ($c1_ordner['fehler'] === '') { echo ' ' . esc_html__('The folder holds no file with a name this node would write.', 'cluster-one-post-dolly'); } ?></p>
<?php   else : ?>
        <table class="widefat striped" style="max-width:680px;">
            <thead><tr>
                <th><?php esc_html_e('What', 'cluster-one-post-dolly'); ?></th>
                <th><?php esc_html_e('File', 'cluster-one-post-dolly'); ?></th>
                <th style="width:12em;"><?php esc_html_e('Node can read it', 'cluster-one-post-dolly'); ?></th>
            </tr></thead>
            <tbody>
<?php       foreach ($c1_belegt as $c1_b) : ?>
                <tr>
                    <td><strong><?php echo esc_html($c1_b['was']); ?></strong></td>
                    <td><code><?php echo esc_html($c1_b['datei']); ?></code></td>
                    <td><?php echo esc_html($c1_b['knoten_liest'] ? __('yes — it holds the key', 'cluster-one-post-dolly') : __('no', 'cluster-one-post-dolly')); ?></td>
                </tr>
<?php       endforeach;
            foreach ($c1_sonst as $c1_d) : ?>
                <tr>
                    <td><?php esc_html_e('A file of yours', 'cluster-one-post-dolly'); ?></td>
                    <td><code><?php echo esc_html($c1_d); ?></code></td>
                    <td><?php esc_html_e('no', 'cluster-one-post-dolly'); ?></td>
                </tr>
<?php       endforeach; ?>
            </tbody>
        </table>
        <p class="description" style="max-width:680px;"><?php esc_html_e('While something the node holds lies there, the access cannot be forgotten: a password that vanishes under a file is a silent loss. Bring it back first. Files of your own stay readable without this node.', 'cluster-one-post-dolly'); ?></p>
<?php   endif;
}

/* 28.10.2026 (Neo), Boden 1.19.0: der Handler zu „Mein Speicher" – pruefen, vergessen.
   Dieselbe Hand wie bei der Merkliste: cluster_one_can_access, Nonce, eigener Mensch. */
add_action('admin_post_cluster_one_speicher', function () {
    if (!cluster_one_can_access()) { wp_die('-1', 403); }
    check_admin_referer('cluster_one_speicher');
    $c1_uid  = (int) get_current_user_id();
    $c1_back = admin_url('admin.php?page=cluster_one_personal_area');
    $c1_op   = isset($_POST['op']) ? sanitize_key((string) wp_unslash($_POST['op'])) : '';
    $c1_code = 'unvollstaendig';
    $c1_det  = '';
    if (!function_exists('cluster_one_speicher_pruefen')) {
        $c1_code = 'kein_tresor';
    } elseif ($c1_op === 'pruefen') {
        $c1_r = cluster_one_speicher_pruefen(
            $c1_uid,
            isset($_POST['url']) ? (string) wp_unslash($_POST['url']) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- cluster_one_speicher_pruefen() prueft die Adresse (cluster_one_speicher_url), nie ausgegeben
            isset($_POST['benutzer']) ? (string) wp_unslash($_POST['benutzer']) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- cluster_one_speicher_pruefen() reinigt den Benutzernamen (sanitize_text_field), nie ausgegeben
            isset($_POST['passwort']) ? (string) wp_unslash($_POST['passwort']) : '' // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- ein Passwort wird nicht gereinigt: Bytes fuer den Tresor bzw. die WebDAV-Anmeldung, nie HTML; jede Reinigung koennte es veraendern
        );
        $c1_code = (string) $c1_r['code'];
        $c1_det  = (string) $c1_r['detail'];
    } elseif ($c1_op === 'vergessen') {
        $c1_f = cluster_one_speicher_vergessen($c1_uid);
        $c1_code = $c1_f === '' ? 'vergessen' : $c1_f;
    }
    $c1_url = add_query_arg('speicher', $c1_code, $c1_back);
    if ($c1_det !== '') { $c1_url = add_query_arg('speicher_detail', rawurlencode(mb_substr($c1_det, 0, 200)), $c1_url); }
    wp_safe_redirect($c1_url);
    exit;
});

/** v0.0.90: Mail-Freigabe — WER darf die WP-Mailadresse sehen. Die
   Adresse selbst hat EINE Wahrheitsquelle (WP-Konto); Cluster One regelt
   nur die Sichtbarkeit im Knoten: hidden | on_request (per PN) |
   whitelist. Ehrliche Grenze: Der WP-Admin sieht Adressen ohnehin in der
   WP-Benutzerliste — der Regler regiert die Cluster-One-Anzeige. */
function cluster_one_email_share_of(int $uid): string {
    $v = (string) get_user_meta($uid, 'cluster_one_email_share', true);
    return in_array($v, array('on_request', 'whitelist'), true) ? $v : 'hidden';
}

/** Speichert die vier persönlichen Angaben für EINEN User. Prüft keine
   eigene Nonce mehr (das übernimmt der aufrufende Handler einmal fürs
   ganze Formular, siehe admin_post_cluster_one_profile_save) — nur noch
   die Fähigkeitsprüfung bleibt hier, als zweite, günstige Absicherung. */
function cluster_one_save_user_profile_fields(int $user_id): void {
    /* v0.9.17: DAS EIGENE PROFIL GEHOERT EINEM SELBST — Christians Befund als
       MenschA: Er trug seine Muttersprache ein, bekam "gespeichert", und
       danach waren alle Felder wieder leer.

       Ursache: current_user_can('edit_user', $id) ist in WordPress die
       Faehigkeit, FREMDE Konten zu bearbeiten. Ein Abonnent hat sie nicht —
       auch nicht fuer sich selbst. Die Funktion stieg also still aus, waehrend
       der c1_lang-Teil danach trotzdem schrieb: EIN TEIL SPEICHERTE, DER
       ANDERE STIEG STILL AUS, und die Erfolgsmeldung sprach fuer beide.

       Richtig ist die Frage, ob es das EIGENE Konto ist. Fremde Konten
       schreibt diese Funktion ohnehin nie an — der Aufrufer uebergibt immer
       get_current_user_id() (siehe Kommentar im Profil-Handler). Die Pruefung
       bleibt trotzdem stehen, damit ein kuenftiger Aufrufer nicht versehentlich
       ein fremdes Ziel durchreicht. */
    if ($user_id !== get_current_user_id() && !current_user_can('edit_user', $user_id)) {
        return;
    }
    if (!cluster_one_can_access()) {
        return; // wer nicht ins Haus darf, pflegt hier auch kein Profil
    }
    // phpcs:disable WordPress.Security.NonceVerification.Missing -- der Nonce wird einmal fuers ganze Formular im Aufrufer geprueft (admin_post_cluster_one_profile_save, check_admin_referer; Kommentar ueber dieser Funktion); hier werden nur die Felder gelesen
    $keys = cluster_one_human_meta_keys();
    foreach ($keys as $field => $meta_key) {
        $post_key = 'c1_' . $field;
        if (!isset($_POST[$post_key])) {
            $val = '';
        } elseif ($field === 'tz') {
            // v0.0.73: Zeitzone wird validiert; ein ungültiger Alt-Wert darf
            // unverändert wieder gespeichert werden (Bestandsschutz), aber
            // kein neuer Freitext mehr.
            $old_tz = (string) get_user_meta($user_id, $meta_key, true);
            $val = cluster_one_sanitize_tz((string) wp_unslash($_POST[$post_key]), $old_tz); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (cluster_one_sanitize_tz), den der Pruefer nicht kennt
        } elseif ($field === 'about_body') {
            // Mehrzeilig, wie ein Platon-Beitrag — sanitize_text_field
            // würde Zeilenumbrüche killen, deshalb derselbe Sanitizer wie
            // für Post-Texte.
            $val = cluster_one_sanitize_message((string) wp_unslash($_POST[$post_key])); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (cluster_one_sanitize_message), den der Pruefer nicht kennt
        } else {
            $val = trim(sanitize_text_field(wp_unslash($_POST[$post_key])));
        }
        if ($val === '') {
            delete_user_meta($user_id, $meta_key);
        } else {
            update_user_meta($user_id, $meta_key, $val);
        }
    }
    // phpcs:enable WordPress.Security.NonceVerification.Missing
}

add_action('admin_init', function () {
    register_setting('cluster_one_settings_group', 'cluster_one_settings', [
        'type'              => 'array',
        'sanitize_callback' => 'cluster_one_sanitize_settings',
        'default'           => [],
    ]);
});

/** Bugfix (v0.0.52): options.php verlangt für JEDE Settings-API-Seite fest
   'manage_options', unabhängig davon, welche Capability die Menü-Seite
   selbst nutzt — ein WordPress-Kernverhalten, keine Cluster-One-Eigenheit
   (bestätigt im WP-Core-Quelltext, wp-admin/options.php). Ohne diesen
   Filter hätte Kranz → Kleinrock zwar für jeden auf der Weißliste
   sichtbar ausgesehen, aber nur ein echter WordPress-Administrator hätte
   dort je etwas SPEICHERN können — bei euch nie aufgefallen, weil ohnehin
   nur SysOp diese Seite anfasst, aber ein echter, unbehobener Bug, keine
   bewusste Entscheidung. cluster_one_is_sysop() statt der allgemeinen
   cluster_one_can_access(), weil diese Seite (Weißliste, Connector-
   Ausschluss, Knoten-Identität) inhaltlich SysOp-Sache ist, nicht
   irgendeine Weißlisten-Angelegenheit. */
add_filter('option_page_capability_cluster_one_settings_group', function (): string {
    return 'cluster_one_kranz_save';
});

function cluster_one_sanitize_settings($input): array {
    $clean = [];
    $input = is_array($input) ? $input : array();

    /* 19.10.2026 (Neo) — EIN FORMULAR SPRICHT NUR UEBER SEINE FELDER.
       Diese Option wird seit heute von drei Seiten beschrieben: der Boden
       (Name, Weissliste, Connectoren), das Mesh (Uebersetzer, Muttersprache)
       und die Raeume (Discord/Matrix, Collective, Erinnerung). Jedes dieser
       Formulare schickt in `_felder` die Liste der Schluessel, die es traegt.
       Ein Schluessel, der NICHT darin steht, behaelt den gespeicherten Stand —
       auch ein Kaestchen, denn ein Kaestchen, das gar nicht auf der Seite
       war, ist nicht abgewaehlt worden (dieselbe Regel wie H219 unten, nur
       fuer alle Felder). Ohne `_felder` gilt alles wie bisher: Code, der die
       volle Option schreibt, nennt keine Liste und meint alle Schluessel. */
    $c1_felder = null;
    if (isset($input['_felder']) && is_string($input['_felder'])) {
        $c1_felder = array_values(array_filter(array_map('sanitize_key', explode(',', $input['_felder']))));
    }
    $c1_stand = get_option('cluster_one_settings', array());
    $c1_stand = is_array($c1_stand) ? $c1_stand : array();

    $clean['node_id']     = isset($input['node_id'])     ? sanitize_text_field($input['node_id'])   : '';
    /* v0.2.3: Der Selbst-Steckbrief. Die Getter dafuer gab es seit 0.0.5, der
       Ping-Empfaenger las die Felder auch — nur gesendet hat sie nie jemand.
       Jetzt reisen sie, aber JEDES EINZELN freigegeben.

       Warum einzeln: 'city' und 'country' sind Ortsangaben ueber den SysOp.
       Sie gehen nur an die eigenen KANTEN — der Ping ist token-gesichert, im
       Gegensatz zu /labels — aber wer sie freigibt, soll es bewusst tun und
       nicht, weil ein Feld halt ausgefuellt war.

       v1.4.0 — HIER STAND EINE AUSNAHME, UND SIE STAND ZWEIMAL. Der Satz
       lautete: „Der Name des Knotens reist ohnehin (ohne ihn keine Paarung),
       die Sprachen ebenfalls: sie sind funktional, Anti-Babel braucht sie,
       und sie verraten keinen Ort." Dieselbe Begruendung stand ein zweites
       Mal in kern.php:323. Christian hat die Ausnahme am 31.08.2026
       aufgehoben: Sprachen sind freigebbar wie jedes andere Feld.
         Beide Kommentare mussten weg, nicht einer. Genau so ist H142
       entstanden — ein Kommentar wurde berichtigt, der sichtbare Satz blieb
       zwanzig Zeilen weiter stehen.
       Die Liste selbst wohnt jetzt in cluster_one_profilfelder() (Boden).
       Wer ein Feld ergaenzt, ergaenzt eine Zeile, nicht drei. */
    /* v1.5.0 — JEDE FORM BRAUCHT EINEN SCHREIBWEG, SONST FAELLT SIE WEG.
       In 1.4.0 stand hier nur der Zweig fuer 'text'. 'languages' hat die Form
       'liste' und bekam keinen — und weil dieser Sanitizer ERSETZT statt zu
       mischen (H113), verschwand die Sprachliste bei jedem Speichern der
       Einstellungen. Ein Freigabe-Kaestchen stand daneben und gab etwas frei,
       das der Admin gar nicht fuellen konnte.
         Gefunden von Hollerith beim Bauen einer Pruefszene (Bericht 14 §5),
       nicht von einem Werkzeug — und die tote Variable dazu stand schon in
       1.5.3. Neu war nur, dass sich eine Freigabe darauf stuetzte.
         Der else-Zweig ist deshalb kein Sonderfall fuer Sprachen, sondern die
       Regel: WER EINE FORM ERFINDET, ERFINDET IHREN SCHREIBWEG MIT. Kaeme
       morgen eine dritte Form dazu, faellt sie hier auf — sie landet im
       else und wird als Liste behandelt, was sie vermutlich nicht ist. */
    /* v1.9.8 (H219) — DIE VIERTE STELLE, AN DER DIESE DATEI DASSELBE LERNT.
       Und diesmal nicht als Fall, sondern als Form.

       Der Sinn steht seit v0.0.67 im Haus, und er steht dreimal:

           v0.0.67  forum_areas       "ein fehlendes Formularfeld darf
           v0.9.42  forum_wiki_areas   gespeicherte Daten nicht stumm leeren"
           v0.7.5   forum_area_meta

       Dreimal derselbe Satz, dreimal auf EINEN Schluessel angewendet — und der
       Steckbrief hat ihn nie bekommen. Der Kommentar oben (v1.5.0) benennt den
       Mechanismus sogar wortwoertlich ("weil dieser Sanitizer ERSETZT statt zu
       mischen"), zieht den Schluss aber nur fuer 'languages'.

           Wer aus einem System drei Faelle macht, hat es dreimal repariert
           und einmal nicht.

       Was das kostete, ist am 05.09.2026 dreimal unabhaengig gemessen worden:
       Hollerith aus dem Rumpf (Bericht 43 §5), Kleinrock mit dem echten
       Sanitizer und vollem Stand (Lieferung 06 §3), ich mit zeuge-h219.php —
       NEUN von zehn Steckbrief-Werten waren nach EINEM Speichern der
       Kranz-Seite weg. 'node_id' steht auf derselben Seite darueber, und
       daneben steht "A name is required".

       DIE REGEL, und sie ist enger als "immer bewahren":

           Ein Schluessel, der NICHT IM EINGANG STEHT, ist keine Entscheidung.
           Ein Schluessel, der mit leerem Wert dasteht, ist eine.

       Deshalb array_key_exists und nicht isset: wer loeschen will, schickt ''
       oder ein leeres Array und bekommt es. Wer gar nichts schickt, spricht
       nicht ueber dieses Feld.

       UND DIE FREIGABE HAENGT AM FELD, NICHT AN SICH SELBST. Ein nicht
       angehaktes Kaestchen schickt nichts — 'share_x' fehlt dann aus zwei
       ganz verschiedenen Gruenden: abgewaehlt, oder das Formular kennt das
       Feld gar nicht. Allein ist das nicht unterscheidbar; zusammen mit
       seinem Feld schon. Steht 'x' im Eingang, gehoert der Block dem Absender
       und ein fehlendes 'share_x' heisst abgewaehlt. Steht 'x' nicht drin,
       spricht der Absender ueber gar nichts davon.

           Ein Fehlen hat zwei Gruende. Wer es allein liest, waehlt einen aus.

       Seit Kleinrocks Reparatur vom 05.09. ist das der NORMALFALL und keine
       Randlage: seine elf Felder stehen in SEINEM Formular, kommen im
       Kranz-Formular also nie mehr mit. */
    foreach (cluster_one_profilfelder() as $c1_f => $c1_art) {
        if (!array_key_exists($c1_f, $input)) {
            $c1_leer = (($c1_art['form'] ?? 'text') === 'text') ? '' : array();
            $clean[$c1_f]            = $c1_stand[$c1_f] ?? $c1_leer;
            $clean['share_' . $c1_f] = !empty($c1_stand['share_' . $c1_f]) ? 1 : 0;
            continue;
        }
        if (($c1_art['form'] ?? 'text') === 'text') {
            $clean[$c1_f] = isset($input[$c1_f]) ? sanitize_text_field($input[$c1_f]) : '';
        } else {
            /* Eine Auswahl: nur die Kuerzel, die das Feld anbietet, und die
               kommen aus derselben Quelle wie das Formular. Was der Reiniger
               spaeter ohnehin verwerfen wuerde, kommt hier gar nicht erst
               hinein — sonst stuende es gespeichert da und reiste nie. */
            $c1_erlaubt = (array) ($c1_art['auswahl'] ?? array());
            $c1_roh     = isset($input[$c1_f]) && is_array($input[$c1_f]) ? $input[$c1_f] : array();
            $c1_liste   = array();
            foreach ($c1_roh as $c1_w) {
                $c1_w = strtolower(sanitize_text_field((string) $c1_w));
                if (in_array($c1_w, $c1_erlaubt, true) && !in_array($c1_w, $c1_liste, true)) {
                    $c1_liste[] = $c1_w;
                }
            }
            $clean[$c1_f] = $c1_liste;
        }
        $clean['share_' . $c1_f] = !empty($input['share_' . $c1_f]) ? 1 : 0;
    }
    $clean['discord_url'] = isset($input['discord_url']) ? esc_url_raw(trim($input['discord_url'])) : '';
    $clean['matrix_url']  = isset($input['matrix_url'])  ? esc_url_raw(trim($input['matrix_url']))  : '';
    // Mesh-Übersetzer (Secure Anti-Babel): Provider-ID einer benannten KI. Gespeichert, noch nicht wirkend.
    $clean['mesh_translator'] = isset($input['mesh_translator']) ? sanitize_text_field($input['mesh_translator']) : '';
    // v0.9.48: Freigabe fuer Beschirmte — wirkt nur mit selbstgehostetem Uebersetzer.
    $clean['mesh_translate_all'] = !empty($input['mesh_translate_all']) ? 1 : 0;
    /* v0.9.57: Der Gegenschalter zum Schreiben. Negativ benannt, weil der
       Normalfall "alle duerfen" ist und ein fehlender Schluessel den Zustand
       von gestern ergeben muss. Siehe cluster_one_mesh_write_open(). */
    $clean['mesh_write_sysop_only'] = !empty($input['mesh_write_sysop_only']) ? 1 : 0;
    // Muttersprache: Ziel des Übersetzers. Privat, bleibt aus allen Listen. Pflicht FÜR den Übersetzer (Sperre kommt mit der Funktion), nicht fürs Pairing.
    $clean['mother_tongue'] = isset($input['mother_tongue']) ? sanitize_text_field($input['mother_tongue']) : '';
    // v0.0.90: Zusammenfassungs-Intervall der Zehn-Runden-Warnung — Saat 10,
    // 0 = aus; der SysOp legt fest, wie oft das System erinnert.
    $clean['fs_summary_interval'] = isset($input['fs_summary_interval']) ? min(999, max(0, (int) $input['fs_summary_interval'])) : 10;
    // Collective-Autor (v0.0.43): WP-User, der bei "Als Collective senden"
    // als post_author eingesetzt wird. Bewusst OHNE Ausschluss-Rollen-Filter
    // wählbar — Collective SOLL die Ausschluss-Rolle tragen.
    $clean['collective_user_id'] = isset($input['collective_user_id']) ? (int) $input['collective_user_id'] : 0;
    // Collective-Trigger-Kategorien (v0.0.44): Term-IDs, deren Zuweisung
    // automatisch auf den Collective-Autor umschaltet.
    // v0.5.0: collective_categories entfaellt — der Blog-Weg ist zurueckgebaut.

    // Weißliste (v0.0.46, ersetzt die Ausschluss-Rolle für die Zugriffsfrage):
    // WP-User-IDs, die explizit "bei Cluster One dabei" sind.
    $clean['whitelist'] = isset($input['whitelist']) && is_array($input['whitelist'])
        ? array_map('intval', $input['whitelist'])
        : array();

    // Ausgeschlossene Connector-IDs (v0.0.46, ersetzt die fest verdrahtete
    // 'spam_filtering'-Ausnahme): der Knotenpunkt entscheidet selbst, was in
    // der KI-Kennungstabelle als KI zählt.
    $clean['excluded_connector_ids'] = isset($input['excluded_connector_ids']) && is_array($input['excluded_connector_ids'])
        ? array_map('sanitize_text_field', $input['excluded_connector_ids'])
        : array();

    // Forum-Bereiche (v0.0.53, ersetzt die drei festen Namensfelder aus
    // v0.0.46): frei erfundene Liste, eine Zeile pro Bereich. Jeder neue
    // Platon-Thread bekommt beim Anlegen einen dieser Bereiche zugewiesen.
    // v0.0.67: Quelle am Datentyp erkennen. Als ARRAY kann forum_areas nur
    // aus Code kommen (Bismarck → Struktur schreibt die volle Options-
    // Struktur per update_option — und der Settings-API-Sanitizer feuert
    // bei JEDEM update_option auf diese Option, nicht nur beim Kranz-
    // Formular). Das Kranz-Formular hat seit 0.0.66 kein forum_areas-Feld
    // mehr; die alte Textarea lieferte einen String. 0.0.66-Fehler: Hier
    // wurde IMMER der DB-Stand bewahrt — damit wurde auch die frisch vom
    // Struktur-Reiter gespeicherte Liste im selben update_option sofort
    // wieder mit dem alten Stand überschrieben (Bereich anlegen → Meldung
    // „angelegt" → Liste unverändert). Erst speichern, dann bewahrte der
    // Wächter das Alte.
    if (isset($input['forum_areas']) && is_array($input['forum_areas'])) {
        $clean['forum_areas'] = array();
        foreach ($input['forum_areas'] as $line) {
            if (!is_scalar($line)) {
                continue; // v0.4.6: siehe cluster_one_save_forum_areas — "Array" ist kein Name
            }
            $line = trim(sanitize_text_field((string) $line));
            if ($line !== '' && !in_array($line, $clean['forum_areas'], true)) {
                $clean['forum_areas'][] = $line;
            }
        }
    } else {
        // Kranz-Formular (das Feld existiert dort nicht mehr): gespeicherten
        // Stand bewahren — ohne diesen Zweig würde jedes Kranz-Speichern die
        // Liste stumm auf leer setzen (der ursprüngliche 0.0.66-Schutzzweck).
        $stored = get_option('cluster_one_settings', array());
        $clean['forum_areas'] = array_values((array) (is_array($stored) ? ($stored['forum_areas'] ?? array()) : array()));
    }
    // Welche der obigen Bereiche verhalten sich als Wiki (ein Thread, ein
    // Post, Autor Collective, jeder darf editieren, KI editiert statt
    // anzuhängen) — Teilmenge von forum_areas, per Checkbox gegen die
    // gerade gespeicherten Bereichsnamen.
    // v0.0.69: Die Wiki-Häkchen sind aus dem Kranz verschwunden (Wiki ist
    // Thread-Eigenschaft). forum_wiki_areas war bis v0.9.42 historischer Datensatz:
    // kommt kein Feld herein, wird der gespeicherte Stand BEWAHRT — dieselbe
    // Lehre wie der 0.0.67-Hotfix (fehlendes Formularfeld darf gespeicherte
    // Daten nicht stumm leeren).
    /* v0.7.5: forum_area_meta DURCHREICHEN.

       Christians Befund: Eine Beschreibung im Struktur-Reiter verschwand nach
       dem Speichern und kam nie wieder. Ursache: Dieser Sanitizer baut sein
       Ergebnis aus einer WHITELIST — und WordPress ruft ihn bei JEDEM
       update_option('cluster_one_settings'). Was er nicht kennt, wirft er weg.
       Also wurde die Beschreibung gespeichert und im selben Zug geloescht.

       Betroffen war auch die Blaupause: Ihre Bereichsbeschreibungen sind seit
       0.6.4 nie angekommen. Dass an den Bereichsueberschriften trotzdem
       Fragezeichen standen, lag am Rueckfall auf den Namensgeber — die Luecke
       sah aus wie eine Funktion.

       Kein Zeuge konnte das fangen: Der Pruefstand hat update_option als
       einfachen Schreiber, ohne WordPress' sanitize_option-Filter. Deshalb
       lernt er es in derselben Runde. */
    if (isset($input['forum_area_meta']) && is_array($input['forum_area_meta'])) {
        $clean['forum_area_meta'] = array();
        foreach ($input['forum_area_meta'] as $c1_an => $c1_am) {
            if (!is_array($c1_am)) {
                continue;
            }
            $c1_an = sanitize_text_field((string) $c1_an);
            if ($c1_an === '') {
                continue;
            }
            $c1_e = array();
            if (isset($c1_am['desc']))   { $c1_e['desc']   = sanitize_textarea_field((string) $c1_am['desc']); }
            if (isset($c1_am['origin'])) { $c1_e['origin'] = sanitize_key((string) $c1_am['origin']); }
            if (array_key_exists('published', $c1_am)) { $c1_e['published'] = (bool) $c1_am['published']; }
            $clean['forum_area_meta'][$c1_an] = $c1_e;
        }
    } else {
        $c1_sm = get_option('cluster_one_settings', array());
        $clean['forum_area_meta'] = (array) (is_array($c1_sm) ? ($c1_sm['forum_area_meta'] ?? array()) : array());
    }

    /* v0.9.31: HIER WAERE FAST ETWAS FALSCHES PASSIERT. Der if-Zweig sieht
       tot aus — ein FORMULARFELD forum_wiki_areas gibt es tatsaechlich nicht
       mehr. Aber die Eingabe kommt gar nicht aus einem Formular: Wenn
       structure_rename (platon.php) einen Wiki-Bereich umbenennt, schreibt es
       die neue Liste per update_option, und die laeuft durch DIESEN Sanitizer.
       Ohne den if-Zweig wuerde jede Umbenennung sofort auf den alten Stand
       zurueckgesetzt. Zwei Zeugen haben das gefangen ("Wiki-Zuordnung zeigt
       ins Leere"). Kein Formularfeld heisst nicht kein Schreibpfad.
       (v0.9.42: gegenstandslos geworden, s. unten.) */
    /* v0.9.42: Der forum_wiki_areas-Zweig ist entfallen. Er hat sieben
       Versionen lang eine Liste durch den Sanitizer getragen, die seit 0.0.69
       nichts mehr bewirkte — der Kommentar darueber erklaerte sorgfaeltig,
       warum ein Schreibpfad ohne Formularfeld trotzdem einen Sanitizer
       braucht. Das stimmte auch. Nur brauchte die Liste selbst niemand mehr.
       KEIN FORMULARFELD HEISST NICHT KEIN SCHREIBPFAD — der Satz bleibt
       richtig, sein Anlass ist fort. */

    /* Zuletzt die Liste von oben: was dieses Formular nicht traegt, bleibt
       wie gespeichert. Erst hier, damit jeder Zweig darueber seinen Wert
       regulaer gebaut hat und ein Schluessel, der noch nie gespeichert war,
       seinen Vorgabewert behaelt. */
    if ($c1_felder !== null) {
        foreach ($clean as $c1_k => $c1_v) {
            if (!in_array($c1_k, $c1_felder, true) && array_key_exists($c1_k, $c1_stand)) {
                $clean[$c1_k] = $c1_stand[$c1_k];
            }
        }
    }
    return $clean;
}

/** Die Meldung nach dem Speichern ueber options.php. WordPress zeigt sie nur
 *  auf seinen eigenen Einstellungsseiten von selbst; auf einer Menueseite
 *  muss die Seite sie lesen. Drei Seiten schreiben diese Option, also einmal
 *  hier und nicht dreimal. (19.10.2026, Neo) */
function cluster_one_settings_gespeichert_meldung(): void {
    if (isset($_GET['settings-updated'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved.', 'cluster-one-post-dolly') . '</p></div>';
    }
}

/** Das versteckte Feld, mit dem ein Formular dem Reiniger sagt, welche
 *  Schluessel es traegt. Siehe cluster_one_sanitize_settings(). */
function cluster_one_settings_felder_feld(array $c1_felder): void {
    echo '<input type="hidden" name="cluster_one_settings[_felder]" value="' . esc_attr(implode(',', $c1_felder)) . '">';
}

function cluster_one_render_settings_page() {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    $opts = get_option('cluster_one_settings', []);

    $const_node    = defined('CLUSTER_ONE_NODE_ID')     && constant('CLUSTER_ONE_NODE_ID')     !== '';

    /* v1.5.0 — HIER STAND $sel_langs, UND SIE WURDE NIE GELESEN.
       Berechnet seit 0.2.3, kein zweiter Zugriff, in keiner Fassung — der
       Rest eines Formularfeldes, das nie gebaut wurde, aus der Zeit, als der
       Steckbrief noch in dieser Datei wohnte. Das Feld steht jetzt dort, wo
       auch seine Freigabe steht: im Steckbrief-Abschnitt (kleinrock.php), der
       als angemeldeter Abschnitt ohnehin auf DIESER Seite erscheint.
         Sie wiederzubeleben waere der bequemere Weg gewesen und der falsche:
       er haette den Wert hier gesetzt und die Freigabe dort — genau die
       Aufteilung, wegen der anderthalb Jahre niemand gemerkt hat, dass die
       Liste gar nicht zu fuellen war. */

    $override = '<strong>' . esc_html__('Currently overridden by a constant.', 'cluster-one-post-dolly') . '</strong>';
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Kranz'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_kranz_tabs('cluster_one_settings'); ?>

        <?php if (!cluster_one_is_sysop()) : ?>
            <div class="notice notice-info"><p><?php esc_html_e('View only — these are SysOp settings. Changes below will not actually save unless you are the SysOp.', 'cluster-one-post-dolly'); ?></p></div>
        <?php endif; ?>

        <?php
        cluster_one_settings_gespeichert_meldung();
        /* 19.10.2026 (Neo): Der Leser fuer `pair=` stand hier bis heute. Das
           Paaren meldet sich seit dem 18.10. auf der Mesh-Seite, wo auch das
           Formular steht; hier las ihn niemand mehr. */
        /* VERTRAG 28 §4 (17.09.2026) — VIER AUSGAENGE, VIER SAETZE.
           Der Knopf raeumt seit heute nur noch die Daten SEINES Pakets, und
           er hat deshalb zwei Ausgaenge mehr als vorher. Ein Ausgang ohne
           Satz faellt hier durch und hinterlaesst eine Seite, die nichts
           sagt — genau die Stille, gegen die dieser Umbau gebaut ist.
               Ein leerer Wert ist eine Antwort auf „was", nie auf „warum".
           Und der alte 'done'-Satz stimmt nicht mehr: „alle Daten" waren es
           nie, und seit dem Schnitt waeren es fremde. */
        if (isset($_GET['reset'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $s = sanitize_text_field(wp_unslash($_GET['reset'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            if ($s === 'done') {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('The data of this product has been deleted. Data of other Cluster One products on this node was not touched.', 'cluster-one-post-dolly') . '</p></div>';
            } elseif ($s === 'cancelled') {
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Reset cancelled — you did not type RESET. Nothing was deleted.', 'cluster-one-post-dolly') . '</p></div>';
            } elseif ($s === 'kein-raeumer') {
                echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('Nothing was deleted: this product has not registered a cleaner, so nothing here knows which data belongs to it. That is not the same as having no data.', 'cluster-one-post-dolly') . '</p></div>';
            } elseif ($s === 'mehrere') {
                echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('Nothing was deleted: more than one Cluster One product is installed on this node, and this page exists only once. It cannot tell whose data you meant. Deactivate the others, or use the node reset, which says openly that it clears everything.', 'cluster-one-post-dolly') . '</p></div>';
            } elseif ($s === 'unbekannt') {
                echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('Nothing was deleted: this plugin could not determine which package it is running from, so it cannot tell its own data from another product’s.', 'cluster-one-post-dolly') . '</p></div>';
            }
        }
        ?>

        <?php if ($const_node) : ?>
            <div class="notice notice-info inline" style="padding:12px;max-width:720px;">
                <p style="margin:0;">
                    <?php echo wp_kses_post(__('Note: Some values are already set as constants in <code>wp-config.php</code>. <strong>Constants take precedence</strong> — the fields below then only serve as a fallback.', 'cluster-one-post-dolly')); ?>
                </p>
            </div>
        <?php endif; ?>

        <form method="post" action="options.php">
            <?php settings_fields('cluster_one_settings_group'); ?>
            <?php cluster_one_settings_felder_feld(array('node_id', 'whitelist', 'excluded_connector_ids')); ?>

<?php cluster_one_settings_seite_knoten($opts, $const_node, $override); ?><?php cluster_one_settings_seite_zugang($opts); ?><?php /* 19.10.2026 (Neo) — DIE SEITE GEHOERT WIEDER DEM BODEN ALLEIN.
   Bis heute standen hier vier Abschnitte: Knoten (mit Discord/Matrix),
   Uebersetzer (mit Muttersprache und Erinnerungs-Intervall), Collective mit
   Weissliste und Connectoren, Forenbereiche. Geblieben ist, was jedes Produkt
   braucht: der Name, die Weissliste, die Connectoren. Der Rest ist zu seinem
   Besitzer gezogen — der Uebersetzer samt Muttersprache und Hallo-Chat-
   Schalter auf die Mesh-Einstellungen (kleinrock.php), Discord/Matrix,
   Collective und Erinnerung auf die Seite der KI-Kennungen (bismarck.php,
   ueber do_action('cluster_one_abschnitt', 'einstellungen')). Der Satz
   „More about you … will live in a Profile tab later" ist gestrichen: den
   Reiter gibt es. Alle Optionsschluessel sind geblieben; nur die Formulare
   stehen woanders. */ ?>
            <?php submit_button(__('Save', 'cluster-one-post-dolly')); ?>
        </form>

        <hr style="margin:2.5em 0;">

<?php /* AB HIER BRINGT JEDER ABSCHNITT SEIN EIGENES FORMULAR MIT.
   Verschachtelte <form> sind ungueltig — der Browser wirft das innere weg.
   Gefahrenzone und Umgebung machen es laengst richtig; Post Dolly und
   Kleinrocks Steckbrief bekommen es in Welle 5. Und jeder mit EIGENER
   Options-Gruppe, sonst loescht das eine Speichern, was das andere
   gespeichert hat.

       Ein Abschnitt, der ein Formular braucht, das ein anderer oeffnet,
       ist kein Abschnitt. Er ist eine Fortsetzung, die sich als
       eigenstaendig ausgibt. */ ?>
<?php cluster_one_kranz_fremde_abschnitte($opts); ?>    </div>
    <?php
}

/** v1.1.0 — DER KRANZ NENNT KEINEN NAMEN MEHR, DER NICHT IHM GEHOERT.
 *
 * Hier standen zwei nackte Aufrufe:
 *
 *     cluster_one_settings_seite_steckbrief($opts);   // liegt in kleinrock.php
 *     cluster_one_settings_seite_umgebung();          // liegt in umgebung.php
 *
 * Beide Organe koennen fehlen — und dann faellt die meistbenutzte Seite des
 * Hauses mit „Call to undefined function". Genau das misst die Kreuzprobe seit
 * dem ersten Schnitt an Bismarck-Light (kranz.php:602) und an Kleinrock-Light
 * (kranz.php:1397, :300, :672).
 *
 * Gemessen am 27.08.2026: kranz.php hat 26 Funktionen, davon beruehren 17
 * NICHTS ausserhalb des Bodens. Nach aussen ist der Kranz nur zwei Funktionen
 * breit. Er ist keine Komponente, er ist ein SAMMLER — und ein Sammler ueber
 * Dinge, die auseinandergeschnitten werden, laesst sich nicht aufteilen.
 *
 *     Man kann ihn nur umdrehen.
 *
 * Also ruft der Kranz nicht mehr in die Organe hinein; die Organe MELDEN SICH
 * AN. Wer nicht da ist, meldet sich nicht, und es fehlt genau sein Abschnitt —
 * kein Fatal, keine Luecke im Rest. Dasselbe Muster, das feynman.php mit
 * cluster_one_steckbrief_von() fuer die Namenstabelle schon benutzt.
 *
 * Die Ordnungszahl steht beim Anmelden, nicht hier: sonst waere die
 * Reihenfolge wieder eine Liste im Kranz, und die muesste jemand pflegen.
 */
/* v1.12.13 (23.09.2026) — DER PLATZ IST NICHT MEHR DER SCHLUESSEL.  POSTEN 3.
 *
 * WAS HIER BIS HEUTE STAND, UND WARUM ES FALSCH WAR       (R-PAPIER-1)
 *
 * Die Form war seit dem Umdrehen:
 *
 *     $c1_a[<ordnungszahl>] = '<funktionsname>';
 *
 * Der PLATZ war die Ordnung UND der Schluessel im Feld. Zwei Haeuser, die
 * dieselbe Zahl nehmen, nehmen denselben Schluessel — der zweite
 * ueberschreibt den ersten, und wenn dieser Sammler hinsieht, ist der erste
 * nicht mehr da. Er kann ihn nicht melden, weil nichts mehr da ist, was
 * gemeldet werden koennte.
 *
 *     Ein Waechter, der prueft, ob der Eintrag GUELTIG ist, prueft nicht,
 *     ob der PLATZ FREI war.                               (Post Dolly)
 *
 * DERSELBE FEHLER WIE BEIM RAEUMER, an einer zweiten Stelle. Beim Raeumer
 * ist er am 17.09. repariert worden (R-SAMMLER-1). Hier stand er weiter —
 * nicht weil jemand widersprochen haette, sondern weil ihn niemand gesucht
 * hat, solange nichts kollidierte.
 *
 * HEUTE KOLLIDIERT NICHTS, und das gehoert dazugesagt, damit niemand die
 * Reparatur fuer eine Rettung haelt:
 *
 *     50  kleinrock.php   Steckbrief      nur im Kleinrock-Paket
 *     55  kranz.php       Gefahrenzone    in JEDEM Paket, hier im Riegel
 *     60  umgebung.php    Umgebung        nur im Crockford-Paket
 *     65  post-dolly.php  gefallen, mit Absicht und begruendet
 *
 *     Ein Fehler, der heute nicht auftritt, ist kein anderer Fehler.
 *     Er ist derselbe, ungezaehlt.
 *
 * DIE FORM AB JETZT — FORM B, dieselbe wie beim Raeumer:
 *
 *     add_filter('cluster_one_kranz_abschnitte', function ($c1_a) {
 *         if (!is_array($c1_a)) { return $c1_a; }
 *         $c1_a[] = array(
 *             'ord'  => 50,
 *             'fn'   => 'cluster_one_settings_seite_steckbrief',
 *             'wirt' => cluster_one_kasper_wirt(__FILE__),
 *         );
 *         return $c1_a;
 *     });
 *
 *     ANGEHAENGT, NICHT ZUGEWIESEN. 'ord' ist die Ordnung und nur noch die
 *     Ordnung. 'wirt' sagt, aus welchem PAKET der Abschnitt kommt — das ist
 *     die Frage, die eine Kollision beantwortbar macht.
 *
 * WAS BEI EINER KOLLISION GESCHIEHT, und hier ist dieser Sammler ABSICHTLICH
 * anders als das Raeumer-Register:
 *
 *     Beim RAEUMER sind zwei Anmeldungen auf EIN Produkt ein echter
 *     Widerspruch — zwei Funktionen wollen dieselben Daten raeumen. Dort
 *     gilt die erste, und die zweite wird genannt.
 *
 *     Bei einem ABSCHNITT ist es keiner. Zwei Abschnitte auf einer Seite
 *     sind zwei Abschnitte. BEIDE werden gezeichnet, in der Reihenfolge
 *     ihrer Anmeldung, und die Haeuser bekommen eine Meldung.
 *
 *         Wo das Nebeneinander harmlos ist, ist das Verschwinden der
 *         ganze Fehler.
 *
 * UND ZWEI SORTEN MELDUNG, weil es zwei Sorten Leser gibt:
 *
 *     VERWORFEN        hinterlaesst ein Loch auf der Seite. Das sieht der
 *                      Admin — also steht es AUF DER SEITE, an der Stelle,
 *                      an der der Abschnitt haette stehen sollen.
 *     KOLLISION        hinterlaesst kein Loch; nur die Reihenfolge haengt
 *                      dann an der Ladereihenfolge der Plugins. Das geht
 *                      den Admin nichts an und die Haeuser alles — also
 *                      _doing_it_wrong().
 *
 *         Eine Meldung gehoert dorthin, wo der steht, der etwas daran
 *         aendern kann.
 *
 * DIE ALTE FORM WIRD NICHT STILL MITGELESEN. Zwei erlaubte Formen waeren
 * zwei Wahrheiten ueber dieselbe Sache. Wer sie schickt, bekommt ein
 * benanntes Loch auf der Seite und keinen Abschnitt — fail closed, laut.
 * GEMESSEN VON M110.
 */
/* v1.9.8 (H218) — WER VERWORFEN WIRD, ERFAEHRT ES.
 *
 * Die Bedingung darunter ist unveraendert: NUR EIN FUNKTIONSNAME ALS
 * ZEICHENKETTE. Das bleibt Absicht — ein Abschnitt, der einen Namen hat,
 * laesst sich messen, benennen und in einem Bericht zitieren; eine Closure
 * nicht. Der Vertrag wird hier NICHT geweitet.
 *
 * Was sich aendert, ist das Schweigen. Bis 1.9.7 fiel alles, was kein
 * bekannter Funktionsname war, ohne ein Wort heraus:
 *
 *     eine Closure                      -> weg
 *     [$objekt, 'methode']              -> weg
 *     ein Name mit Tippfehler           -> weg
 *
 * und keiner dieser drei Faelle war von „hat sich nie angemeldet" zu
 * unterscheiden. Am 05.09.2026 hat mich das selbst getroffen: der
 * Gegenprobe-Abschnitt fuer Zeuge W37 war eine Closure, wurde lautlos
 * verworfen, und der Zeuge wurde GRUEN — an einem Abschnitt, der mit
 * Absicht falsch gebaut war. Ohne die vier Welten waere er so ausgeliefert
 * worden.
 *
 *     Ein Fehlen kann sich nicht melden. Nur eine Liste kann es.
 *     (Bismarck, 03.09.2026)
 *
 * Hier ist es kleiner und schlimmer: die Liste GAB es, der Eintrag stand
 * darin, und er wurde beim Lesen weggeworfen. Deshalb meldet die Seite es
 * jetzt an der Stelle, an der der Abschnitt haette stehen sollen — und
 * nennt die Ordnungszahl, weil sie das einzige ist, was ein verworfener
 * Eintrag noch ueber sich verraet.
 */
function cluster_one_kranz_fremde_abschnitte(array $opts): void {
    $c1_a = apply_filters('cluster_one_kranz_abschnitte', array());
    if (!is_array($c1_a)) { return; }

    /* EINE LISTE, IN ANMELDEREIHENFOLGE, und erst danach wird geurteilt.
       Genau das ist der Unterschied zu vorher: was hier ankommt, ist
       vollstaendig — auch das, was auf derselben Ordnungszahl sitzt. */
    $c1_liste = array();
    $c1_n     = 0;

    foreach ($c1_a as $c1_schluessel => $c1_eintrag) {

        /* ---- die ALTE Form: eine Zeichenkette unter der Ordnungszahl ---- */
        if (!is_array($c1_eintrag)) {
            $c1_liste[] = array(
                'ord'  => is_int($c1_schluessel) ? $c1_schluessel : PHP_INT_MAX,
                'fn'   => null,
                'wirt' => '',
                'n'    => $c1_n++,
                'grund' => sprintf(
                    /* translators: %s: what was registered, e.g. a function name or a type */
                    __('registered in the old form (assigned under its ordinal). Expected: append an array with ord, fn and wirt. What was sent: %s', 'cluster-one-post-dolly'),
                    '<code>' . esc_html(is_string($c1_eintrag) ? $c1_eintrag . '()' : get_debug_type($c1_eintrag)) . '</code>'
                ),
            );
            continue;
        }

        /* ---- FORM B ---- */
        if (!is_int($c1_schluessel) && function_exists('_doing_it_wrong')) {
            /* Ein zeichenkettiger Schluessel BEWEIST eine Zuweisung. Die
               Gegenrichtung traegt nicht — ein ganzzahliger beweist kein
               Anhaengen.                                    (R-SAMMLER-2) */
            _doing_it_wrong('cluster_one_kranz_abschnitte', sprintf(
                'Ein Abschnitt wurde ZUGEWIESEN statt angehaengt (Wirt %s, unter dem '
              . 'Schluessel %s). Erwartet: $c1_a[] = array(…). Sein Eintrag wird '
              . 'genommen — aber wer vor ihm auf diesem Platz stand, ist bereits '
              . 'verloren und kann nicht mehr genannt werden.',
                (string) ($c1_eintrag['wirt'] ?? '?'), (string) $c1_schluessel), '1.12.13'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Entwicklermeldung an _doing_it_wrong(): kein Seiteninhalt; WordPress legt sie selbst durch wp_kses (wp_trigger_error)
        }

        $c1_fn   = $c1_eintrag['fn'] ?? null;
        $c1_wirt = (string) ($c1_eintrag['wirt'] ?? '');

        if (!array_key_exists('ord', $c1_eintrag) || !is_int($c1_eintrag['ord'])) {
            $c1_liste[] = array(
                'ord' => PHP_INT_MAX, 'fn' => null, 'wirt' => $c1_wirt, 'n' => $c1_n++,
                'grund' => __("registered without an integer 'ord' — the ordinal is what puts a section in its place, so there is nowhere to put this one", 'cluster-one-post-dolly'),
            );
            continue;
        }
        $c1_ord = (int) $c1_eintrag['ord'];

        if (is_string($c1_fn) && $c1_fn !== '' && function_exists($c1_fn)) {
            /* v1.12.14 — EIN LEERER WIRT WIRD GENANNT.            (Kleinrock)
               Die Form aus VERTRAG 30 §1 riegelt den Wirt:
                   'wirt' => function_exists('cluster_one_kasper_wirt')
                             ? cluster_one_kasper_wirt(__FILE__) : '',
               Kleinrocks Einwand, und er ist gegen den RIEGEL, nicht gegen
               die Form:

                   „Ein Riegel, der die Antwort durch eine leere ersetzt,
                    hat den Ausfall nicht verhindert, sondern unbenannt
                    gemacht."

               HEUTE KANN DER FALL NICHT EINTRETEN: jeder Lader laedt
               kasper.php vor allem anderen. Der Abschnitt wird deshalb
               GENOMMEN — er zeichnet ja richtig. Aber sein leerer Wirt
               heisst „ich weiss nicht, aus welchem Paket ich komme", und
               genau das macht eine Kollision unbeantwortbar.

                   Ein Fall, der heute nicht eintritt, braucht keine
                   Reparatur. Er braucht einen Namen. */
            if ($c1_wirt === '' && function_exists('_doing_it_wrong')) {
                _doing_it_wrong('cluster_one_kranz_abschnitte', sprintf(
                    'Der Abschnitt %s() hat sich OHNE Wirt angemeldet (Ordnung %d). Er wird '
                  . 'gezeichnet — aber bei einer Kollision auf dieser Zahl kann nicht gesagt '
                  . 'werden, aus welchem Paket er kommt. Das passiert, wenn '
                  . 'cluster_one_kasper_wirt() beim Anmelden nicht dalag.',
                    $c1_fn, $c1_ord), '1.12.14'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Entwicklermeldung an _doing_it_wrong(): kein Seiteninhalt; WordPress legt sie selbst durch wp_kses (wp_trigger_error)
            }
            $c1_liste[] = array('ord' => $c1_ord, 'fn' => $c1_fn, 'wirt' => $c1_wirt,
                                'n' => $c1_n++, 'grund' => '');
            continue;
        }

        /* Verworfen. Der Grund ist verschieden, und der Unterschied ist der
           ganze Wert der Meldung: ein Tippfehler im Namen ist ein anderer
           Fehler als ein Rueckgabewert der falschen Art. */
        $c1_liste[] = array(
            'ord' => $c1_ord, 'fn' => null, 'wirt' => $c1_wirt, 'n' => $c1_n++,
            'grund' => is_string($c1_fn)
                ? sprintf(
                    /* translators: %s: function name that was registered but does not exist */
                    __('registered under the name %s — no function of that name exists', 'cluster-one-post-dolly'),
                    '<code>' . esc_html($c1_fn) . '()</code>')
                : sprintf(
                    /* translators: %s: PHP type of the registered value */
                    __('registered as %s — this hook takes a function name as a string, nothing else', 'cluster-one-post-dolly'),
                    '<code>' . esc_html(get_debug_type($c1_fn)) . '</code>'),
        );
    }

    /* ---- ZWEI AUF EINER ZAHL: beide bleiben, die Haeuser erfahren es ----
       Kein Loch auf der Seite — nur die Reihenfolge zwischen ihnen haengt
       dann an der Ladereihenfolge der Plugins, und die ist kein Vertrag. */
    $c1_nach_ord = array();
    foreach ($c1_liste as $c1_e) {
        if ($c1_e['fn'] === null) { continue; }
        $c1_nach_ord[$c1_e['ord']][] = $c1_e;
    }
    foreach ($c1_nach_ord as $c1_ord => $c1_gruppe) {
        if (count($c1_gruppe) < 2 || !function_exists('_doing_it_wrong')) { continue; }
        $c1_wer = array();
        foreach ($c1_gruppe as $c1_e) {
            $c1_wer[] = $c1_e['fn'] . '() (Wirt ' . ($c1_e['wirt'] !== '' ? $c1_e['wirt'] : '?') . ')';
        }
        _doing_it_wrong('cluster_one_kranz_abschnitte', sprintf(
            'ZWEI Abschnitte auf der Ordnungszahl %d: %s. Beide werden gezeichnet — '
          . 'aber die Reihenfolge ZWISCHEN ihnen ist die Ladereihenfolge der Plugins '
          . 'und kein Vertrag. Gebt ihnen verschiedene Zahlen.',
            (int) $c1_ord, implode(' und ', $c1_wer)), '1.12.13'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Entwicklermeldung an _doing_it_wrong(): kein Seiteninhalt; WordPress legt sie selbst durch wp_kses (wp_trigger_error)
    }

    /* ---- die Ordnung, und bei gleicher Zahl die Anmeldereihenfolge ----
       usort ist seit PHP 8.0 stabil; 'n' steht trotzdem da, weil eine
       Ordnung, die sich auf eine Zusage der Sprache verlaesst, beim
       naechsten Leser eine Frage ist.  */
    usort($c1_liste, function ($x, $y) {
        if ($x['ord'] !== $y['ord']) { return $x['ord'] <=> $y['ord']; }
        return $x['n'] <=> $y['n'];
    });

    foreach ($c1_liste as $c1_e) {
        if ($c1_e['fn'] !== null) { $c1_fn = $c1_e['fn']; $c1_fn($opts); continue; }

        $c1_satz = sprintf(
            /* translators: 1: ordinal number the section registered under, 2: reason it was discarded */
            __('A section registered for this page at position %1$s was discarded: %2$s', 'cluster-one-post-dolly'),
            '<code>' . esc_html($c1_e['ord'] === PHP_INT_MAX ? '—' : (string) $c1_e['ord']) . '</code>',
            $c1_e['grund']
        );
        $c1_woher = $c1_e['wirt'] !== ''
            ? sprintf(
                /* translators: %s: plugin folder the section came from */
                __('It came from %s.', 'cluster-one-post-dolly'),
                '<code>' . esc_html($c1_e['wirt']) . '</code>')
            : '';
        ?>
        <div class="notice notice-warning inline" style="padding:12px;max-width:720px;">
            <p style="margin:0;"><?php echo wp_kses_post($c1_satz); ?></p>
            <?php if ($c1_woher !== '') : ?>
                <p style="margin:.5em 0 0;"><?php echo wp_kses_post($c1_woher); ?></p>
            <?php endif; ?>
            <p style="margin:.5em 0 0;opacity:.85;">
                <?php esc_html_e('Until 1.9.7 this happened silently and looked exactly like a section that never registered at all.', 'cluster-one-post-dolly'); ?>
            </p>
        </div>
        <?php
    }
}

function cluster_one_settings_seite_knoten(array $opts, bool $const_node, string $override): void { ?>            <h2 style="margin-top:1.5em;"><?php esc_html_e('This node', 'cluster-one-post-dolly'); ?></h2>
            <?php if (!cluster_one_has_name()) : ?>
                <div class="notice notice-warning inline" style="padding:12px;max-width:720px;">
                    <p style="margin:0;">
                        <?php echo wp_kses_post(__('<strong>A name is required.</strong> Until you set and save one, this node cannot pair (in either direction) — that keeps every chat attributable and no node enters the mesh nameless.', 'cluster-one-post-dolly')); ?>
                    </p>
                </div>
            <?php endif; ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="c1_node"><?php esc_html_e('Name', 'cluster-one-post-dolly'); ?> <span style="color:#d63638;">*</span></label></th>
                    <td>
                        <input name="cluster_one_settings[node_id]" id="c1_node" type="text"
                               class="regular-text" required
                               value="<?php echo esc_attr($opts['node_id'] ?? ''); ?>"
                               placeholder="<?php echo esc_attr__('e.g. Nemesus', 'cluster-one-post-dolly'); ?>">
                        <p class="description">
                            <?php echo wp_kses_post(__('Required. The name this node speaks under — it is the author shown in chats and the label next to your URL. Becomes the heading of your profile later. Not a secret, not your WP login.', 'cluster-one-post-dolly')); ?>
                            <?php if ($const_node) echo $override; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $override ist oben in dieser Funktion aus esc_html__() gebaut ?>
                        </p>
                    </td>
                </tr>
            </table>

<?php
}


function cluster_one_settings_seite_zugang(array $opts): void { ?>
            <h2 style="margin-top:2em;"><?php esc_html_e('Whitelist', 'cluster-one-post-dolly'); ?></h2>
            <p style="max-width:720px;opacity:.85;"><?php echo wp_kses_post(__('You are either in Cluster One or you are not — and if you are, it is within Cluster One’s own rules, regardless of your general WordPress role. Always in, automatically: SysOp, any WP user behind an AI identity, the Collective account. Everyone else needs a place on this list, or they have no access at all — not to Bismarck, not to the humans table, nothing. This protects a years-old installation with many existing users just as well as a brand-new one.', 'cluster-one-post-dolly')); ?></p>
            <?php
            $whitelist_ids = array_map('intval', (array) ($opts['whitelist'] ?? array()));
            $main_admin_id = cluster_one_main_admin_user_id();
            $ai_ids        = cluster_one_ai_identity_user_ids();
            $collective_id = cluster_one_collective_user_id();
            $all_wp_users2 = get_users(array('fields' => array('ID', 'display_name')));
            ?>
            <?php foreach ($all_wp_users2 as $wpu) :
                $wid = (int) $wpu->ID;
                if ($wid === $main_admin_id || in_array($wid, $ai_ids, true) || $wid === $collective_id) {
                    continue; // automatisch drin, keine Listenzeile nötig
                }
            ?>
                <label style="display:inline-flex;align-items:center;gap:.4em;margin:.2em 1.2em .2em 0;">
                    <input type="checkbox" name="cluster_one_settings[whitelist][]" value="<?php echo esc_attr((string) $wid); ?>" <?php checked(in_array($wid, $whitelist_ids, true)); ?>>
                    <?php echo esc_html($wpu->display_name); ?>
                </label>
            <?php endforeach; ?>
            <p class="description" style="max-width:720px;"><?php esc_html_e('Removing someone here only removes them from Cluster One — it has no effect on their WordPress account or any other part of the site.', 'cluster-one-post-dolly'); ?></p>

            <h2 style="margin-top:2em;"><?php esc_html_e('Excluded connectors', 'cluster-one-post-dolly'); ?></h2>
            <?php
            $raw_connectors      = cluster_one_get_ai_connectors_raw();
            $excluded_connectors = cluster_one_excluded_connector_ids();
            ?>
            <p style="max-width:720px;opacity:.85;"><?php echo wp_kses_post(__('Everything WordPress reports as a connector, unfiltered — including things that are not AI providers at all (spam filtering, and whatever a future WP version might add). The node decides what counts as an AI here, not the plugin.', 'cluster-one-post-dolly')); ?></p>
            <?php if (empty($raw_connectors)) : ?>
                <p style="opacity:.7;"><?php esc_html_e('No connectors detected.', 'cluster-one-post-dolly'); ?></p>
            <?php else : ?>
                <?php foreach ($raw_connectors as $rcid => $rconn) :
                    $rtype = is_array($rconn) ? (string) ($rconn['type'] ?? '') : '';
                    $rname = is_array($rconn) ? (string) ($rconn['name'] ?? $rcid) : (string) $rcid;
                ?>
                    <label style="display:inline-flex;align-items:center;gap:.4em;margin:.2em 1.2em .2em 0;">
                        <input type="checkbox" name="cluster_one_settings[excluded_connector_ids][]" value="<?php echo esc_attr((string) $rcid); ?>" <?php checked(in_array((string) $rcid, $excluded_connectors, true)); ?>>
                        <?php echo esc_html($rname); ?><?php echo $rtype !== '' ? ' <span style="opacity:.6;">(' . esc_html($rtype) . ')</span>' : ''; ?>
                    </label>
                <?php endforeach; ?>
            <?php endif; ?>
            <p class="description" style="max-width:720px;"><?php esc_html_e('Ticked = hidden from the AI identity table, does not count as an installed AI anywhere in the plugin.', 'cluster-one-post-dolly'); ?></p>

<?php
}


/* Auch die Gefahrenzone meldet sich an, obwohl sie hier im Haus liegt. Sonst
   stuende ihre Position wieder als nackter Aufruf zwischen den angemeldeten —
   und die Reihenfolge waere halb Liste, halb Anmeldung. Eine Ordnung, die an
   zwei Orten steht, ist keine. 55 = vor Umbertos Umgebung (60); Kleinrocks
   Steckbrief (50) steht seit dem 19.10.2026 auf der Mesh-Seite. */
add_filter('cluster_one_kranz_abschnitte', function ($c1_a) {
    if (!is_array($c1_a)) { return $c1_a; }
    /* FORM B seit 1.12.13 — angehaengt, nicht zugewiesen. Der Boden meldet
       sich in derselben Form wie die Haeuser; eine Ausnahme fuer den, der
       den Sammler schreibt, waere der Anfang von zwei Formen. */
    $c1_a[] = array(
        'ord'  => 55,
        'fn'   => 'cluster_one_settings_seite_gefahrenzone',
        'wirt' => function_exists('cluster_one_kasper_wirt')
                  ? cluster_one_kasper_wirt(__FILE__) : '',
    );
    return $c1_a;
});

function cluster_one_settings_seite_gefahrenzone(array $opts = array()): void { ?>        <h2 style="color:#d63638;"><?php esc_html_e('Danger zone — reset all data', 'cluster-one-post-dolly'); ?></h2>
        <p style="max-width:680px;">
            <?php esc_html_e('This permanently deletes the data of THIS product — and only of this product. Other Cluster One products installed on this node keep their data; this button no longer reaches them. The plugin files stay. This cannot be undone, and it does not notify your peers — they will still list this node until their edge to it goes silent. To confirm, type RESET into the field.', 'cluster-one-post-dolly'); ?>
        </p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:680px;"
              onsubmit="return confirm('<?php echo esc_js(__('Really delete all Cluster One data on this node? This cannot be undone.', 'cluster-one-post-dolly')); ?>');">
            <?php wp_nonce_field('cluster_one_reset'); ?>
            <input type="hidden" name="action" value="cluster_one_reset">
            <input name="confirm" type="text" class="regular-text" autocomplete="off"
                   placeholder="<?php esc_attr_e('Type RESET to confirm', 'cluster-one-post-dolly'); ?>" required>
            <?php submit_button(__('Delete all data', 'cluster-one-post-dolly'), 'delete', 'submit', false); ?>
        </form>

<?php
}


/* v0.9.70: Hiess bis hierher cluster_one_settings_seite_umgebung(). Der Spiegel
   ist ausgezogen (nach Turing, Reiter 2) — ein Diagnose-Ausgang ist ein
   Werkzeug und kein Organ. Geblieben ist, was nie Spiegel war: die
   Randbedingungen und die Begruendung des SysOps dazu. */



/* --- Backend-Aktion: DIE DATEN DIESES PAKETS raeumen (Gefahrenzone) ---

   H115, geschlossen am 17.09.2026. Post Dolly hat es am 06.09. gemeldet und
   nicht angefasst; ich habe elf Tage gebraucht, bis ich nachgemessen habe.

   WAS HIER STAND:

       SELECT option_name FROM {$wpdb->options}
       WHERE option_name LIKE 'cluster_one_%'
       -> delete_option() auf jeden Treffer

   IM MONOLITHEN WAR DAS RICHTIG. Ein Plugin, ein Praefix, alle Daten seine.
   Seit dem Schnitt ist 'cluster_one_' nicht mehr der Name EINES Plugins,
   sondern der Name der FAMILIE — und die Kehre lief durch vier fremde
   Haeuser. Gemessen an den fuenf ausgelieferten Paketen: 41 Optionsnamen,
   davon EINER Post Dollys.

       Ein Praefix, das den Namen EINES Dinges trug, traegt nach einer
       Teilung den Namen der FAMILIE. Jede Kehre darueber wird beim Teilen
       von einer Selbstbedienung zu einem Uebergriff — lautlos, ohne dass
       sich eine Zeile geaendert haette.                      (R-PRAEFIX-1)

   UND SIE WIDERSPRACH DEM KNOPF ZWEI FUNKTIONEN WEITER. cluster_one_node_reset()
   nennt die KANTEN ausdruecklich unantastbar — „eine Kante gehoert ZWEI
   Knoten, sie hier zu loeschen waere ein stiller Schaden auf der Gegenseite".
   cluster_one_paired_peers faellt unter LIKE. Die Kehre loeschte sie.
   Und cluster_one_reset_at/_count dazu — die NARBE des anderen Knopfes.

   WAS JETZT LAEUFT: cluster_one_raeumen_mein(). Das Register kennt KEINE
   Optionsnamen; es weiss nur, WER raeumen kann. Hat dieses Paket keinen
   Raeumer, wird NICHTS geloescht, und der Mensch erfaehrt es.

       Ein Produkt ohne Raeumer ist etwas anderes als ein Produkt ohne
       Daten.                                (Post Dollys eigener Kommentar
                                              im Register, Monate frueher)

   Zweistufig wie bisher: Nonce + getipptes RESET. Lokal, kein Peer erfaehrt es. */
add_action('admin_post_cluster_one_reset', function () {
    // ANNAHME (v0.0.39): bewusste Ausnahme von der allgemeinen Öffnung auf
    // cluster_one_can_access() — unumkehrbares Löschen ALLER Plugin-Daten
    // bleibt echten Administratoren vorbehalten, auch wenn sonst jeder
    // nicht-ausgeschlossene Mensch Zugang zum Plugin hat. Kein Grund, ein
    // Testkonto versehentlich zum Knopf für den Datenverlust zu machen.
    if (!current_user_can('manage_options')) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_reset');

    $confirm = isset($_POST['confirm']) ? trim((string) wp_unslash($_POST['confirm'])) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nur mit dem festen Wort RESET verglichen (drei Zeilen tiefer), nie gespeichert oder ausgegeben
    /* 18.10.2026 (Neo): strtoupper wie beim Knoten-Reset (weiter unten) —
       zwei RESET-Felder im selben Haus, eine Regel fuer die Schreibweise. */
    if (strtoupper($confirm) !== 'RESET') {
        wp_safe_redirect(admin_url('admin.php?page=cluster_one_settings&reset=cancelled'));
        exit;
    }

    /* v1.12.7 (18.09.2026) — HIER STAND cluster_one_raeumen_mein(__FILE__),
       UND __FILE__ IST KRANZ.PHP. Der Boden liegt in jedem Paket und laeuft
       nur einmal: auf einem Knoten mit vier Produkten nennt __FILE__ das
       Paket, das ZUERST geladen hat, und nicht das, dessen Seite man ansieht.
       Dieselbe Ursache wie bei der Ueberschrift (VERTRAG 28 §1b), an einem
       Knopf, der loescht.

       WAS DAS HEISSE: der Knopf raeumte zu WENIG und beim falschen Haus —
       nie zu viel. Keine fremden Daten gehen verloren. Aber „es hat geraeumt"
       waere gelogen gewesen.

       UND AUF EINEM KNOTEN MIT MEHREREN PRODUKTEN GIBT ES DIESEN KNOPF NUR
       EINMAL, weil es die Kranz-Seite nur einmal gibt. Er kann dort nicht
       sagen, WESSEN Daten er loeschen wuerde.

           Ein Loeschknopf, der nicht sagen kann, wessen Daten er nimmt,
           darf keine nehmen.

       Also: genau EIN Produkt auf dem Knoten -> er raeumt dessen Daten.
       Mehrere -> er loescht NICHTS und sagt warum. Das ist keine
       Verkleinerung: die Reichweite „alle" hat er nie gehabt, er hat sie
       sich nur mit LIKE genommen. Wer auf einem Mischknoten raeumen will,
       nimmt den Knoten-Reset, der diese Reichweite ERKLAERT. */
    $c1_seite = cluster_one_seite_produkt();
    $c1_lage  = (string) ($c1_seite['lage'] ?? '');
    $c1_erg   = ('einziges' === $c1_lage)
        ? cluster_one_raeumen_mein(WP_PLUGIN_DIR . '/' . $c1_seite['wirt'] . '/x.php', true)
        : array('wirt' => '', 'gefunden' => 0, 'zahlen' => array());

    /* KEIN RAEUMER IST KEIN ERFOLG. Wer hier „done" meldete, haette gesagt,
       es sei geraeumt worden, und es waere nichts geschehen.
           Ein leerer Wert ist eine Antwort auf „was", nie auf „warum".
                                                             (R-STILLE-1) */
    $c1_ausgang = ('einziges' !== $c1_lage)
        ? 'mehrere'
        : (('' === (string) $c1_erg['wirt'])
            ? 'unbekannt'
            : (0 === (int) $c1_erg['gefunden'] ? 'kein-raeumer' : 'done'));

    wp_safe_redirect(admin_url('admin.php?page=cluster_one_settings&reset=' . $c1_ausgang));
    exit;
});

/* v1.9.11 (VERTRAG 25 §4, 08.09.2026) — GRABSTEIN.
   cluster_one_render_bismarck_settings() und …_page() standen hier.
   Sie sind nach kern.php gezogen, weil der Boden ihre Anschrift
   FUENFMAL aufschreibt (fuenf wp_safe_redirect, :1078 :1130 :1336
   :1354 :1374). Der Slug cluster_one_bismarck_settings ist
   unveraendert.
       Eine Seite, deren Anschrift der Boden fuenfmal aufschreibt,
       ist eine Boden-Seite.                        (Bismarck, 08.09.)
   Bismarck erscheint darauf weiter ueber
   do_action('cluster_one_abschnitt', 'menschen'). */

/** Schreibgeschützte "Über mich"-Ansicht einer ANDEREN Person (v0.0.49) —
   erreicht über den verlinkten Namen in der Menschen-Tabelle. Kein
   Bearbeiten-Zugang hier, mit voller Absicht: möchte SysOp fremd editieren,
   tut er das über WordPress' eigenen Login-als-dieser-User-Weg (Passwort
   zurücksetzen), nicht über eine eigens gebaute Override-Funktion. */
function cluster_one_render_about_me_view(int $user_id): void {
    $target = get_userdata($user_id);
    $keys   = cluster_one_human_meta_keys();
    $back   = admin_url('admin.php?page=cluster_one_bismarck_settings');
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Kranz'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <p><a href="<?php echo esc_url($back); ?>">&larr; <?php esc_html_e('Back to humans table', 'cluster-one-post-dolly'); ?></a></p>
        <?php if (!$target || !cluster_one_can_access($user_id)) : ?>
            <p style="opacity:.7;"><?php esc_html_e('Nothing here.', 'cluster-one-post-dolly'); ?></p>
        <?php else :
            $title = trim((string) get_user_meta($user_id, $keys['about_title'], true));
            $body  = trim((string) get_user_meta($user_id, $keys['about_body'], true));
        ?>
            <div style="max-width:720px;border:1px solid #dcdcde;border-radius:4px;padding:1em 1.2em;margin-top:1em;background:#fff;">
                <div style="opacity:.7;font-size:.9em;margin-bottom:.4em;"><?php echo esc_html($target->display_name); ?></div>
                <?php // v0.0.90: Mail nach dem Freigabe-Regler des Besitzers
                $c1_es = cluster_one_email_share_of($user_id);
                if ($c1_es === 'whitelist') : ?>
                    <div style="font-size:.9em;margin-bottom:.5em;"><a href="mailto:<?php echo esc_attr($target->user_email); ?>"><?php echo esc_html($target->user_email); ?></a></div>
                <?php elseif ($c1_es === 'on_request') : ?>
                    <div style="font-size:.9em;opacity:.7;margin-bottom:.5em;"><?php esc_html_e('Mail: on request', 'cluster-one-post-dolly'); ?> · <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_personal_area')); ?>"><?php esc_html_e('ask via PN', 'cluster-one-post-dolly'); ?></a></div>
                <?php endif; ?>
                <div style="font-weight:600;font-size:1.2em;margin-bottom:.5em;"><?php echo esc_html($title !== '' ? $title : __('About me', 'cluster-one-post-dolly')); ?></div>
                <?php if ($body !== '') : ?>
                    <div style="white-space:pre-wrap;"><?php echo esc_html($body); ?></div>
                <?php else : ?>
                    <p style="opacity:.6;font-style:italic;"><?php esc_html_e('Nothing written yet.', 'cluster-one-post-dolly'); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function cluster_one_render_profile_page() {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    $me_id = get_current_user_id();

    // Read-only Ansicht von jemand ANDEREM (v0.0.49): "Über mich" ist über
    // den verlinkten Namen in der Menschen-Tabelle erreichbar. Eigene ID
    // führt einfach zur normalen, editierbaren Ansicht weiter unten — kein
    // Sonderfall nötig.
    $view_id = isset($_GET['view']) ? (int) $_GET['view'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    if ($view_id > 0 && $view_id !== $me_id) {
        cluster_one_render_about_me_view($view_id);
        return;
    }

    $me_keys        = cluster_one_human_meta_keys();
    $p              = get_option('cluster_one_profile', array());
    $tz_share       = !empty($p['tz_share']);
    $admin_tz_share = !empty($p['admin_tz_share']);
    $my_tz          = (string) get_user_meta($me_id, $me_keys['tz'], true); // v0.0.42: übernommen, nicht mehr separat gepflegt
    $server_tz      = function_exists('wp_timezone_string') ? wp_timezone_string() : (string) get_option('timezone_string');
    if ($server_tz === '') {
        $off       = (float) get_option('gmt_offset');
        $server_tz = 'UTC' . ($off >= 0 ? '+' : '') . $off;
    }
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Kranz'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_kranz_tabs('cluster_one_profile'); ?>
        <?php if (isset($_GET['saved'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Profile saved.', 'cluster-one-post-dolly'); ?></p></div>
        <?php endif; ?>
        <p style="max-width:720px;opacity:.85;"><?php esc_html_e('How your node shows itself when someone looks — fetched on view, never gossiped across the network. For now just time, so collaborators across the world can tell what hour it is for you. Stored for later — not shared over the network yet.', 'cluster-one-post-dolly'); ?></p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:720px;">
            <?php wp_nonce_field('cluster_one_profile'); ?>
            <input type="hidden" name="action" value="cluster_one_profile_save">
            <?php /* v1.13.0 (24.09.2026) — DIESE UEBERSCHRIFT HIESS BIS HEUTE
                     „Node profile", GENAU WIE KLEINROCKS Steckbrief-Abschnitt
                     auf dem Reiter daneben.        (VERTRAG 32 §4, Posten 19)

                     Zwei Seiten, eine Ueberschrift, in EINER Reiterleiste —
                     und keine sagt, dass es die andere gibt. Christian hat am
                     23.09. nach einem Haekchen gesucht; ich habe ihn ZWEIMAL
                     auf die falsche Seite geschickt, mit dem Code vor mir.

                         R-SCHIRM-2 · Zwei Seiten mit derselben Ueberschrift
                         sind eine Seite, solange niemand danebensteht und
                         sagt, welche gemeint ist.

                     WER VON BEIDEN WIRD UMBENANNT: diese. Kleinrocks heisst
                     zu Recht so — sie IST das Profil des Knotens nach aussen,
                     das, was andere Knoten ueber ihn erfahren. Diese hier
                     zeigt die Angaben des MENSCHEN: Zeitzone, Mail, ueber
                     mich. Der Abschnitt darunter sagt es selbst seit jeher:
                     „Meine Cluster-One-Angaben".

                         Wer zwei Namen fuer eine Sache hat, benennt die um,
                         die nicht die Sache ist.

                     Gewacht wird es seit heute von M120, der die
                     Ueberschriften aller Seiten einsammelt und bei zwei
                     gleichen in einer Leiste ROT wird. */ ?>
            <h2><?php esc_html_e('My details on this node', 'cluster-one-post-dolly'); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Server time zone', 'cluster-one-post-dolly'); ?></th>
                    <td>
                        <code><?php echo esc_html($server_tz); ?></code>
                        <p class="description"><?php echo wp_kses_post(__('Read from WordPress — change it under <em>Settings → General</em>.', 'cluster-one-post-dolly')); ?></p>
                        <?php if (cluster_one_is_sysop()) : // v0.0.61 (P1-8): Node-weite Freigabe = SysOp-Sache ?>
                        <label style="display:inline-flex;align-items:center;gap:.4em;margin-top:.4em;">
                            <input type="checkbox" name="tz_share" value="1" <?php checked($tz_share); ?>>
                            <?php esc_html_e('Show in my profile', 'cluster-one-post-dolly'); ?>
                        </label>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('My time zone (if different)', 'cluster-one-post-dolly'); ?></th>
                    <td>
                        <?php if ($my_tz !== '') : ?>
                            <code><?php echo esc_html($my_tz); ?></code>
                        <?php else : ?>
                            <span style="opacity:.6;"><?php esc_html_e('Not set yet.', 'cluster-one-post-dolly'); ?></span>
                        <?php endif; ?>
                        <p class="description" style="max-width:640px;"><?php echo wp_kses_post(__('Taken from <em>My Cluster One details</em> below — one place to set it, not two. So others see your current local time, not the machine’s.', 'cluster-one-post-dolly')); ?></p>
                        <?php if (cluster_one_is_sysop()) : // v0.0.61 (P1-8) ?>
                        <label style="display:inline-flex;align-items:center;gap:.4em;margin-top:.4em;">
                            <input type="checkbox" name="admin_tz_share" value="1" <?php checked($admin_tz_share); ?>>
                            <?php esc_html_e('Show in my profile', 'cluster-one-post-dolly'); ?>
                        </label>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <h2 style="margin-top:2em;"><?php esc_html_e('My Cluster One details', 'cluster-one-post-dolly'); ?></h2>
            <?php /* 27.10.2026 (Neo), Boden 1.17.2 — „which rooms reach you" NUR MIT BISMARCK.
               Raeume sind Bismarcks; ohne Bismarck (Test 5) erreichte niemanden ein
               Raum, und der Satz behauptete es trotzdem. Dieselbe Frage wie auf
               dem Persoenlichen Bereich. Alter Satz bleibt fuer den Fall MIT Bismarck. */
            if (function_exists('cluster_one_organ_wirt') && cluster_one_organ_wirt('bismarck') === '') : ?>
            <p style="max-width:720px;opacity:.85;"><?php esc_html_e('Shown to other humans in the humans table (AI identities). Yours alone — nobody edits this for you.', 'cluster-one-post-dolly'); ?></p>
            <?php else : ?>
            <p style="max-width:720px;opacity:.85;"><?php esc_html_e('Shown to other humans in the humans table (AI identities) and used to work out which rooms reach you. Yours alone — nobody edits this for you.', 'cluster-one-post-dolly'); ?></p>
            <?php endif; ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="c1_discord">Discord</label></th>
                    <td><input type="text" name="c1_discord" id="c1_discord" value="<?php echo esc_attr((string) get_user_meta($me_id, $me_keys['discord'], true)); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="c1_matrix">Matrix</label></th>
                    <td><input type="text" name="c1_matrix" id="c1_matrix" value="<?php echo esc_attr((string) get_user_meta($me_id, $me_keys['matrix'], true)); ?>" class="regular-text"></td>
                </tr>
                <?php /* v0.9.18: DASSELBE FELD STAND ZWEIMAL AUF DER SEITE.

                   Christians Befund: Muttersprache eintragen, "gespeichert",
                   danach alles leer. Es gab hier "Muttersprache" und weiter
                   unten "Deine Sprache" — BEIDE mit name="c1_lang", beide in
                   dieselbe Meta-Stelle. Beim Absenden gewinnt in PHP das
                   LETZTE gleichnamige Feld: Er fuellte das obere, das untere
                   war leer, und das leere loeschte den Wert wieder.

                   ZWEI FELDER MIT EINEM NAMEN SIND EIN FELD — und zwar das
                   untere. Die aeltere Zeile ist entfallen; die neuere (v0.0.97)
                   bleibt, sie hat den besseren Text und die Vorschlagsliste.

                   Mein erster Verdacht (die edit_user-Pruefung, 0.9.17) war
                   nicht falsch, aber nicht die Ursache — sie hatte MenschA
                   zusaetzlich blockiert. */ ?>
                <tr>
                    <th scope="row"><label for="c1_tz"><?php esc_html_e('Timezone', 'cluster-one-post-dolly'); ?></label></th>
                    <td><?php cluster_one_render_tz_select((string) $my_tz, 'c1_tz', 'c1_tz'); // v0.0.73: Auswahl statt Freitext-Falle ?></td>
                </tr>
            </table>

            <h2 style="margin-top:2em;"><?php esc_html_e('About me', 'cluster-one-post-dolly'); ?></h2>
            <p style="max-width:720px;opacity:.85;"><?php esc_html_e('Visible to others via your name in the humans table (AI identities). Yours to write, yours alone to edit — stays exactly as you leave it until you change it again.', 'cluster-one-post-dolly'); ?></p>
            <p>
                <input type="text" name="c1_about_title" id="c1_about_title" class="regular-text" value="<?php echo esc_attr((string) get_user_meta($me_id, $me_keys['about_title'], true)); ?>" placeholder="<?php echo esc_attr__('About me', 'cluster-one-post-dolly'); ?>">
            </p>
            <p>
                <textarea name="c1_about_body" id="c1_about_body" class="large-text" rows="4"><?php echo esc_textarea((string) get_user_meta($me_id, $me_keys['about_body'], true)); ?></textarea>
            </p>
            <h2 style="margin-top:1.5em;"><?php esc_html_e('Language', 'cluster-one-post-dolly'); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="c1_lang"><?php esc_html_e('Your language', 'cluster-one-post-dolly'); ?></label></th>
                    <td><input type="text" name="c1_lang" id="c1_lang" class="regular-text" list="c1_lang_suggestions" value="<?php echo esc_attr((string) get_user_meta(get_current_user_id(), 'cluster_one_lang', true)); ?>" placeholder="<?php echo esc_attr(cluster_one_user_lang(get_current_user_id())); ?>">
                        <p class="description"><?php esc_html_e('Your heart language — the Babel Fish translates into it. Empty = the node’s mother tongue.', 'cluster-one-post-dolly'); ?></p></td>
                </tr>
            </table>
            <h2 style="margin-top:1.5em;"><?php esc_html_e('Mail', 'cluster-one-post-dolly'); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Your address', 'cluster-one-post-dolly'); ?></th>
                    <td><code><?php echo esc_html((string) (wp_get_current_user()->user_email ?? '')); ?></code>
                        <p class="description"><?php esc_html_e('One source of truth: your WordPress account (change it there). Cluster One only governs who gets to see it.', 'cluster-one-post-dolly'); ?></p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="c1_email_share"><?php esc_html_e('Visible to', 'cluster-one-post-dolly'); ?></label></th>
                    <td>
                        <select name="email_share" id="c1_email_share">
                            <option value="hidden" <?php selected(cluster_one_email_share_of(get_current_user_id()), 'hidden'); ?>><?php esc_html_e('nobody (hidden)', 'cluster-one-post-dolly'); ?></option>
                            <option value="on_request" <?php selected(cluster_one_email_share_of(get_current_user_id()), 'on_request'); ?>><?php esc_html_e('on request — via PN', 'cluster-one-post-dolly'); ?></option>
                            <option value="whitelist" <?php selected(cluster_one_email_share_of(get_current_user_id()), 'whitelist'); ?>><?php esc_html_e('the whitelist — shown on your card', 'cluster-one-post-dolly'); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('Honest limit: a WordPress admin sees addresses in the WP user list anyway — this governs the Cluster One display.', 'cluster-one-post-dolly'); ?></p>
                    </td>
                </tr>
            </table>


            <?php submit_button(__('Save', 'cluster-one-post-dolly')); ?>
        </form>
    </div>
    <?php
}

add_action('admin_post_cluster_one_profile_save', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_profile');
    // v0.0.61 (P1-8): die beiden Freigabe-Häkchen schreiben in die GEMEINSAME
    // Node-Option — Steckbrief-Politik des Knotens, also SysOp-Sache. Vorher
    // überschrieb jedes Mitglied beim Profil-Speichern die Flags fürs ganze
    // Haus. Die persönlichen Felder darunter bleiben für alle.
    if (cluster_one_is_sysop()) {
        update_option('cluster_one_profile', array(
            'tz_share'       => !empty($_POST['tz_share']),
            'admin_tz_share' => !empty($_POST['admin_tz_share']),
        ), false);
    }
    // Persönliche Angaben (Discord/Matrix/Muttersprache/Zeitzone) laufen seit
    // v0.0.41 über dasselbe Formular/dieselbe Nonce — nicht mehr über den
    // nativen WP-Profil-Bildschirm. Immer für den aktuell Eingeloggten,
    // niemals für ein fremdes Ziel (keine User-ID aus dem Formular übernommen).
    // Seit v0.0.42 ist das auch die einzige Quelle für die Node-Profil-
    // Zeitzone — kein separates admin_tz-Feld mehr, eine Wahrheitsquelle.
    cluster_one_save_user_profile_fields(get_current_user_id());
    if (isset($_POST['c1_lang'])) { // v0.0.97: Herzenssprache, eigenes Konto
        update_user_meta(get_current_user_id(), 'cluster_one_lang', trim(sanitize_text_field(wp_unslash($_POST['c1_lang']))));
    }
    if (isset($_POST['email_share'])) { // v0.0.90: Mail-Freigabe, immer nur fürs eigene Konto
        $c1_es = sanitize_key(wp_unslash($_POST['email_share']));
        update_user_meta(get_current_user_id(), 'cluster_one_email_share', in_array($c1_es, array('on_request', 'whitelist'), true) ? $c1_es : 'hidden');
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_profile&saved=1'));
    exit;
});

/* v1.3.1 — HIER STAND cluster_one_render_about_page(), UND SIE STAND FALSCH.
   Ihre eigene Ueberschrift lautete cluster_one_page_h1('Feynman'); sie rief
   Feynmans Reiter, Feynmans Kapitel und Feynmans Namenstabelle. Drei der
   dreizehn offenen Naehte dieser Datei gingen von hier aus — und keine davon
   brauchte einen Vertrag, weil der Rufer im falschen Haus wohnte.

       Eine Seite, deren Ueberschrift ein anderes Organ nennt,
       wohnt im falschen Haus.

   Sie steht jetzt in feynman.php, Rumpf byte-identisch (Regel 2: umziehen,
   nicht neu bauen). menue.php ruft sie ueber einen String-Rueckruf und merkt
   vom Umzug nichts. */

/* v0.1.5: Der Knoten-Reset. SysOp-only, Tipp-Bestätigung, keine Bedingung an
   die Geschichte des Knotens — die Regel dieses Entwurfs lautet nicht "der
   SysOp darf weniger", sondern "was der SysOp tut, bleibt sichtbar". */
add_action('admin_post_cluster_one_node_reset', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_node_reset');
    $back    = admin_url('admin.php?page=cluster_one_system');
    $confirm = isset($_POST['confirm']) ? trim((string) wp_unslash($_POST['confirm'])) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nur mit dem festen Wort RESET verglichen (naechste Zeile), nie gespeichert oder ausgegeben
    if (strtoupper($confirm) !== 'RESET') {
        wp_safe_redirect(add_query_arg('reset', 'unconfirmed', $back));
        exit;
    }
    cluster_one_node_reset();
    wp_safe_redirect(add_query_arg('reset', 'done', $back));
    exit;
});

/* v0.1.9: Kapitel anlegen, aendern, loeschen. SysOp-only. Titel und Text
   werden hart sanitisiert — der Text landet moeglicherweise in einer
   Praeambel, also darf dort kein Markup hinein. */
add_action('admin_post_cluster_one_feynman_chapter', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_feynman_chapter');
    $back = admin_url('admin.php?page=cluster_one_system');
    $kap  = cluster_one_get_feynman_chapters();
    $idx  = isset($_POST['idx']) ? (string) wp_unslash($_POST['idx']) : 'new'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nur als Wort 'new' oder als Zahl (int) benutzt, nie gespeichert oder ausgegeben

    if (isset($_POST['op_delete'])) {
        if ($idx !== 'new' && isset($kap[(int) $idx])) {
            unset($kap[(int) $idx]);
            cluster_one_save_feynman_chapters($kap);
            wp_safe_redirect(add_query_arg('chapter', 'deleted', $back));
            exit;
        }
        wp_safe_redirect($back);
        exit;
    }

    $eintrag = array(
        'title'  => sanitize_text_field((string) wp_unslash($_POST['title'] ?? '')),
        'body'   => sanitize_textarea_field((string) wp_unslash($_POST['body'] ?? '')),
        'link'   => esc_url_raw(trim((string) wp_unslash($_POST['link'] ?? ''))), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (esc_url_raw), den der Pruefer nicht kennt
        'for_ai' => !empty($_POST['for_ai']) ? 1 : 0,
    );
    if ($eintrag['title'] === '' && $eintrag['body'] === '' && $eintrag['link'] === '') {
        wp_safe_redirect(add_query_arg('chapter', 'empty', $back)); // leere Kapitel gibt es nicht
        exit;
    }
    if ($idx === 'new' || !isset($kap[(int) $idx])) {
        $kap[] = $eintrag;
    } else {
        $kap[(int) $idx] = $eintrag;
    }
    cluster_one_save_feynman_chapters($kap);
    wp_safe_redirect(add_query_arg('chapter', 'saved', $back));
    exit;
});

/* v0.2.2: Die Begruendung zur Zusammenstellung. Zwei Knoepfe mit einem
   Unterschied, der zaehlt: Speichern uebernimmt einen neuen Text, Bestaetigen
   sagt nur "gilt weiterhin" — beides setzt den Fingerabdruck auf jetzt.
   Der zweite Knopf existiert, damit "stimmt noch" keine Textaenderung
   erzwingt; sonst wuerde niemand bestaetigen. */
/* 19.10.2026 (Neo) — DER NOTIZ-HANDLER IST FORT.
   Hier stand add_action('admin_post_cluster_one_umgebung_notiz', …): er nahm
   das Formular der Randbedingungen an, rief do_action('cluster_one_umgebung_
   notiz_speichern') und leitete auf die Boden-Seite „Einstellungen" zurueck.
   Seit 0.5.11 steht das Formular auf Crockfords eigener Seite und schickt an
   admin_post_cluster_one_crockford_notiz (umgebung.php), beim Organ, dem die
   Option gehoert. Dieser Handler hatte seit dem keinen Rufer mehr. Die Aktion
   'cluster_one_umgebung_notiz_speichern' bleibt als Vertrag bestehen; sie
   hat nur keinen Boden-Rufer mehr. */


/* cluster_one_blueprint_areas() ist am 11.09.2026 in den BODEN gezogen (kern.php).
   Sie wurde von vier Haeusern gerufen und stand in einem schaltbaren
   Organ — W62, WELT 1. Hier steht kein Ersatz und keine Weiche:
   zwei Wege auf dieselbe Wahrheit waeren zwei Wahrheiten. */


/** Welche Bereiche fehlen noch? */
function cluster_one_blueprint_missing(): array {
    $da   = (array) apply_filters('cluster_one_forum', array(), 'bereiche');   // v1.3.4
    $fehlt = array();
    foreach (cluster_one_blueprint_areas() as $name => $zweck) {
        if (!in_array($name, $da, true)) {
            $fehlt[$name] = $zweck;
        }
    }
    return $fehlt;
}

/* v0.6.2: DIE ZWEI PROMETHEUS-RAEUME.

   Christians drei Fragen, die vor allen Ratgebern liegen — es gibt 10.000
   Anleitungen, und alle setzen ein Ziel voraus:

     1. Was, wenn du noch gar nicht weisst, welches Projekt du bauen willst?
     2. Was, wenn du es weisst, aber dafuer Menschen braeuchtest und keine
        Sau kennst?
     3. Was, wenn das Projekt fertig ist, du es aber nicht vom Backend ins
        Schaufenster bekommst?

   ES SIND ZWEI RAEUME, NICHT DREI. Auf Frage 2 ist die Antwort das MESH selbst
   — dafuer steht der Posten in der Zustandsanzeige, kein Raum.

   Jeder Raum bekommt Name, Modus, Praeambel und eine erste Systemnachricht.
   Ein Raum, der nur einen Namen traegt, waere eine leere Buehne: Die Frage,
   um die es geht, staende nirgends — und genau das Nichtwissen ist ja der
   Anlass.

   Die Praeambel steht OEFFENTLICH im Raum (seit 0.0.60), damit die KI nicht
   heimlich gelenkt werden kann. Sie ist ein Vorschlag, kein Gesetz: Der SysOp
   kann sie jederzeit aendern. */
function cluster_one_blueprint_rooms(): array {
    return array(
        'guru' => array(
            'name'     => __('What am I actually building?', 'cluster-one-post-dolly'),
            'preamble' => __('The person here does not yet know what they want to build. Do not push them towards a project. Ask what they keep coming back to, what annoys them, what they have already done without being asked. Name patterns you notice. A good answer here may be a question.', 'cluster-one-post-dolly'),
            'first'    => __('This room exists for the question that comes before all the guides: what is it you actually want to make? Nobody has to know yet — that is the point.', 'cluster-one-post-dolly'),
        ),
        'shopfront' => array(
            'name'     => __('From the workshop to the shop window', 'cluster-one-post-dolly'),
            'preamble' => __('The person here has something finished and cannot get it in front of anyone. Work from what this installation actually is, not from what is usual. If you need to know the plugins, the PHP limits or the theme, say so — the SysOp can hand you the ground conditions from this room.', 'cluster-one-post-dolly'),
            'first'    => __('For everything between "it works" and "someone can see it". If the AIs need to know how this node is built, the SysOp can hand them the ground conditions here — for a set time, and it says so in the room.', 'cluster-one-post-dolly'),
        ),
    );
}

/** Welche der Blaupausen-Raeume fehlen noch? Erkannt am Namen — wer einen
   umbenannt hat, bekommt ihn wieder angeboten. Das ist gewollt: Nur ergaenzen,
   nie ueberschreiben, und lieber ein Angebot zu viel als ein stiller Verlust. */
function cluster_one_blueprint_rooms_missing(): array {
    $da = array();
    foreach (cluster_one_get_rooms() as $r) {
        $da[] = (string) ($r['name'] ?? '');
    }
    $fehlt = array();
    foreach (cluster_one_blueprint_rooms() as $k => $def) {
        if (!in_array($def['name'], $da, true)) {
            $fehlt[$k] = $def;
        }
    }
    return $fehlt;
}

add_action('admin_post_cluster_one_blueprint_room', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_blueprint_room');
    $back = admin_url('admin.php?page=cluster_one_blueprint');
    $key  = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $defs = cluster_one_blueprint_rooms();
    if ($key === '' || !isset($defs[$key])) {
        wp_safe_redirect($back);
        exit;
    }
    /* Nur ergaenzen: Wer den Raum schon hat (oder hatte und loeschte), bekommt
       keinen zweiten untergeschoben. */
    if (!isset(cluster_one_blueprint_rooms_missing()[$key])) {
        wp_safe_redirect(add_query_arg('bp', 'nothing', $back));
        exit;
    }
    $def   = $defs[$key];
    $rooms = cluster_one_get_rooms();
    $rid   = bin2hex(random_bytes(8));
    $rooms[$rid] = array(
        'id'               => $rid,
        'name'             => $def['name'],
        /* v0.9.51: 'mode' entfaellt, s. bismarck.php. Die Blaupause beschreibt
           weiter, WAS ein Raum werden soll — sie tut es jetzt ueber Besetzung
           und Kopplung statt ueber ein Etikett, das daneben stehen bleibt. */
        'preamble'         => $def['preamble'],
        /* v0.9.34: 'visibility' entfallen, s. bismarck.php. */
        'group_visibility' => 'all',
        'visible_to'       => array(),
        'created'          => time(),
        'outside_view'     => 'half', // v0.4.5: Halbauge ab Werk
        'participants'     => array(cluster_one_actor_id_human()),
        /* v0.9.51: DIE BLAUPAUSE STELLT DIE BEDINGUNGEN HER, STATT SIE ZU
           BEHAUPTEN.

           Bis hierher trug die Definition ein Feld 'mode' => 'guru_meditation'
           und legte den Raum mit `'ais' => array()` an — also OHNE eine
           einzige KI. Das Etikett sagte "ein Mensch, beliebig viele KIs", die
           Besetzung sagte "Menschen ohne KI". Seit 0.6.2 stand da eine leere
           Buehne mit einem Programmzettel davor.

           Aufgefallen ist es erst, als das Etikett wegfiel und die Ableitung
           antworten musste: night_flight statt guru_meditation. EIN FELD, DAS
           EINE ABSICHT FESTHAELT, VERDECKT, DASS SIE NIE AUSGEFUEHRT WURDE.

           Jetzt ziehen die benannten Kennungen des Knotens mit ein. Hat der
           Knoten keine, wird der Raum ehrlich zu Night-Flight, bis eine da
           ist — das ist dann wahr und nicht mehr bloss behauptet. */
        'ais'              => array_values(array_keys(array_filter(
            cluster_one_get_ai_identities(),
            static function ($c1_uid) { return (int) $c1_uid > 0; }
        ))),
    );
    cluster_one_save_rooms($rooms);
    do_action('cluster_one_raum_systemnachricht', $rid, $def['first']);   // v1.3.4
    /* v0.6.3: In 0.6.2 stand hier do_action('cluster_one_event') — ein Hook, den
       niemand hoert. Das Ereignis fiel LAUTLOS aus, und genau davor warnt
       Artikel 2. Richtig ist cluster_one_event_emit(). */
    cluster_one_event_emit(array(
        'type'           => 'room_created',
        'container_type' => 'room',
        'container_id'   => $rid,
        'actor_id'       => cluster_one_actor_id_human(),
    ), true);
    wp_safe_redirect(add_query_arg('bp', 'room', $back));
    exit;
});

add_action('admin_post_cluster_one_blueprint_apply', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_blueprint_apply');
    $back = admin_url('admin.php?page=cluster_one_blueprint');
    /* NUR ERGAENZEN, nie ueberschreiben: Sonst waere ein Fehlklick ein
       Datenverlust. Wer einen Bereich umbenannt oder geloescht hat, hat sich
       etwas dabei gedacht. */
    $fehlt = cluster_one_blueprint_missing();
    if (!$fehlt) {
        wp_safe_redirect(add_query_arg('bp', 'nothing', $back));
        exit;
    }
    $opts  = get_option('cluster_one_settings', array());
    $areas = array_values((array) ($opts['forum_areas'] ?? array()));
    /* v0.6.4: Herkunft UND Beschreibung wandern gleich mit. Die Blaupause
       veroeffentlicht sofort — sie ist per Bauart vollstaendig, anders als ein
       frisch angelegter eigener Bereich.

       WICHTIG: direkt in $opts, NICHT ueber cluster_one_area_meta_set(). Der
       Handler schreibt $opts weiter unten als Ganzes zurueck und haette jede
       zwischendurch gesetzte Meta wieder ueberschrieben — ein Schreibvorgang,
       der einen frueheren im selben Aufruf ausloescht. Der Zeuge hat es
       gefangen. */
    $meta = (array) ($opts['forum_area_meta'] ?? array());
    foreach ($fehlt as $name => $zweck) {
        $areas[] = $name;
        $meta[$name] = array(
            'desc'      => (string) $zweck,
            'origin'    => 'blueprint',
            'published' => true,
        );
    }
    $opts['forum_area_meta'] = $meta;
    $opts['forum_areas'] = $areas;
    /* v0.9.42: Hier wurde der Bereich zusaetzlich als Wiki-Bereich eingetragen.
       Entfaellt — Wiki waehlt man beim Anlegen eines Themas, nicht durch den
       Bereich, in dem es liegt. Der Bereich heisst deshalb jetzt schlicht
       "Brainstorming". */
    update_option('cluster_one_settings', $opts, false);
    cluster_one_event_emit(array(
        'type' => 'blueprint_applied', 'container_type' => 'node', 'container_id' => '',
        'actor_id' => cluster_one_actor_id_human(), 'visibility' => 'all',
    ), true);
    wp_safe_redirect(add_query_arg('bp', (int) count($fehlt), $back));
    exit;
});

/* v0.4.4: Die Zustandsanzeige. KEIN Merkzettel — wer eintrifft, soll nicht
   sehen, WAS ER ALLES TUN MUSS, sondern WO ER STEHT. Und die drei Posten sind
   nicht dieselbe Art Aufgabe: Ohne Zugangsschluessel antwortet keine KI (das ist
   eine Voraussetzung), Beschirmte sind eine Moeglichkeit (der Knoten laeuft
   allein vollstaendig), die Merkliste ist ein Anfang (sie erwartet niemanden).
   Deshalb steht die Art dabei. */
function cluster_one_blueprint_state(): array {
    $opts = get_option('cluster_one_settings', array());
    /* v1.9.10 — GEFRAGT WIRD DAS KONTO, NICHT DIE LISTE.
       Bis hierher stand count(cluster_one_get_ai_identities()). Die Liste ist
       eine Option DIESES Plugins; die Konten, auf die sie zeigt, gehoeren
       Pacioli. Wird der Vault entfernt, zeigt die Liste ins Leere — und diese
       Zeile meldete dem SysOp weiter "Zugangsschluessel erledigt".
       Nach 7a waere das nicht einmal falsch, sondern fuenfmal falsch: die
       Zustandsanzeige zieht in den Boden und reist in jedem Paket mit, auch
       in denen, die nie eine KI hatten. */
    $ki   = count(cluster_one_ai_identities_live());
    return array(
        array(
            'name' => __('Node name', 'cluster-one-post-dolly'),
            'ok'   => trim((string) ($opts['node_id'] ?? '')) !== '',
            'art'  => __('needed — this is how other nodes read you', 'cluster-one-post-dolly'),
            'url'  => admin_url('admin.php?page=cluster_one_settings'),
        ),
        array(
            'name' => __('Access keys for the AIs', 'cluster-one-post-dolly'),
            'ok'   => $ki > 0,
            'art'  => __('needed for the AIs to answer at all', 'cluster-one-post-dolly'),
            'url'  => admin_url('admin.php?page=cluster_one_settings'),
        ),
        /* v0.6.2: DAS MESH KOMMT VOR DEN BEREICHEN. Ein Knoten ohne Kante kann
           nichts von dem, wofuer er gebaut ist — und der Posten stand nirgends.

           WICHTIG: HIER STEHT KEINE VORGESCHLAGENE ADRESSE. Ein ausgeliefertes
           Plugin, das auf einen bestimmten Knoten zeigt, macht diesen zur
           Zentrale — gegen Pull statt Push und gegen die ganze foederierte
           Bauart. Ein Netz mit einer vorgegebenen ersten Adresse hat eine Mitte.
           Wer einen Knoten aufsetzt, traegt die Adresse selbst ein; wer
           eingeladen wurde, hat sie von dem, der ihn eingeladen hat. */
        array(
            'name' => __('A connection to another node', 'cluster-one-post-dolly'),
            /* v1.3.3: ueber den Vertrag — ohne Kleinrock ist die Karte leer,
               und „keine Kante" ist genau das, was der Posten dann sagt. */
            'ok'   => count((array) apply_filters('cluster_one_netz', array(), 'karte')) > 0,
            'art'  => __('optional — but alone the node can only do half of what it is for', 'cluster-one-post-dolly'),
            /* v1.9.6 (H198) — HIER STAND EINE ADRESSE, DIE NICHT DIE
             * GEMEINTE SEITE WAR.
             *
             * Bis hierher: admin_url('admin.php?page=cluster_one_kleinrock').
             * Die gemeinte Seite heisst `cluster_one_mesh_settings` — menue.php
             * meldet sie mit null als Elternteil an, erreichbar ueber die
             * Reiterleiste des Mesh.
             *
             * v1.9.7 (H203) — BERICHTIGUNG DER BEGRUENDUNG, die hier vier
             * Fassungen lang falsch stand. Sie lautete: „Diesen Slug meldet
             * NIEMAND an — ueber alle fuenf Produkte gemessen: er kommt im
             * ganzen Haus genau einmal vor." Das ist unwahr, und der Fehler
             * war meine Messung, nicht der Code.
             *
             *     cluster_one_kleinrock ist angemeldet — vom Mesh-Light
             *     selbst, in dessen Hauptdatei, Zeile 90 und Zeile 109.
             *
             * (Der Ordnername steht hier bewusst NICHT ausgeschrieben: W34s
             *  Z4 liest den Quelltext dieser Funktion nach Namensliteralen,
             *  und ein Beispiel im Kommentar sieht fuer ihn aus wie eine
             *  Namensfrage. Gemeldet ist es, umgangen auch.)
             *
             * Ich hatte `includes/` durchsucht und „im ganzen Haus" darueber
             * geschrieben. Auf einem Knoten MIT dem Kleinrock-Light fuehrte
             * die alte Adresse also auf eine wirkliche Seite — die Floss-Seite;
             * ins Leere lief sie nur auf einem Knoten ohne das Light.
             *
             *     Ein Verzeichnis ist keine Welt. Wer `includes/` misst und
             *     „im ganzen Haus" sagt, hat den Satz vergroessert, nicht die
             *     Messung.
             *
             * Die Reparatur bleibt richtig, ihr Grund war es nicht. Beides
             * steht hier, weil das zweite ohne das erste eine Ausrede waere.
             *
             * UND DIE ZWEITE HAELFTE: die Seite entsteht INNERHALB von
             * cluster_one_menue_wenn('kleinrock', …). Ohne Kleinrock gibt es
             * sie nicht. Dann bleibt die Adresse leer — und die Vorlage unten
             * zeichnet dann KEINE Verknuepfung (H199). Gefragt wird das ORGAN,
             * nicht der Name. */
            'url'  => cluster_one_modul_da('kleinrock')
                ? admin_url('admin.php?page=cluster_one_mesh_settings')
                : '',
        ),
        /* v0.6.2: Nur ein HINWEIS auf ein vorhandenes Framework, keine
           Aufforderung. Der Guard ist die Doku selbst: kein lesbares readme,
           kein Posten. */
        array(
            'name' => __('Theme documentation the AIs can read', 'cluster-one-post-dolly'),
            /* v1.3.1: ueber den Vertrag, nicht beim Namen (siehe feynman.php).
               Ohne Feynman ist die Antwort leer, und leer heisst hier genau das,
               was der Posten ohnehin sagt: kein lesbares readme, kein Posten. */
            'ok'   => (string) apply_filters('cluster_one_erklaerung', '', 'dna') !== '',
            'art'  => (string) apply_filters('cluster_one_erklaerung', '', 'dna-pfad') !== ''
                ? __('found — the AIs get its short map when you hand over the ground conditions', 'cluster-one-post-dolly')
                : __('optional — a theme that documents itself for AI readers', 'cluster-one-post-dolly'),
            /* v1.9.7 (H200) — DIESELBE FORM WIE H198, ZWEITE VON DREI.
             * cluster_one_dna entsteht in cluster_one_menue_wenn('feynman', …).
             * Gemessen am 03.09. ueber die fuenf ausgelieferten Pakete: feynman
             * liegt in bismarck und kleinrock, NICHT in pacioli, post-dolly,
             * umberto. Auf einem Post-Dolly-Knoten zeigte diese Zeile also auf
             * eine Seite, die WordPress nicht kennt.
             *
             *     Wer einen Fehler an der Stelle repariert, an der er gemeldet
             *     wurde, hat die Meldung repariert, nicht den Fehler. */
            'url'  => cluster_one_modul_da('feynman')
                ? admin_url('admin.php?page=cluster_one_dna')
                : '',
        ),
        array(
            'name' => __('Areas', 'cluster-one-post-dolly'),
            'ok'   => count(cluster_one_blueprint_missing()) === 0,
            'art'  => __('the blueprint below fills these in', 'cluster-one-post-dolly'),
            /* v1.9.7 (H200) — dritte von drei. cluster_one_bismarck entsteht in
             * cluster_one_menue_wenn('bismarck-seiten', …); das Organ liegt nur
             * in bismarck. Auf jedem anderen Ein-Produkt-Knoten war dies eine
             * Verknuepfung ins Leere. */
            'url'  => cluster_one_modul_da('bismarck-seiten')
                ? admin_url('admin.php?page=cluster_one_bismarck&tab=structure')
                : '',
        ),
        array(
            'name' => __('People you shelter', 'cluster-one-post-dolly'),
            'ok'   => count((array) ($opts['whitelist'] ?? array())) > 0,
            'art'  => __('optional — the node runs perfectly well alone', 'cluster-one-post-dolly'),
            'url'  => admin_url('admin.php?page=cluster_one_bismarck_settings'),
        ),
        array(
            'name' => __('People you want to invite', 'cluster-one-post-dolly'),
            'ok'   => count(cluster_one_get_humans_external()) > 0,
            'art'  => __('a start — it expects nobody, it only reminds you', 'cluster-one-post-dolly'),
            'url'  => admin_url('admin.php?page=cluster_one_bismarck_settings'),
        ),
    );
}

function cluster_one_render_blueprint_page(): void {
    cluster_one_page_gate();
    $sysop = cluster_one_is_sysop();
    $fehlt = cluster_one_blueprint_missing();
    ?>
    <div class="wrap">
        <?php /* v0.9.62, Christians Befund an 0.9.61: Hier stand vier Versionen
           lang Feynmans Ueberschrift, Feynmans Fragezeichen (Derrida/Prometheus)
           und Feynmans Reiterleiste — in der dieser Reiter seit 0.9.58 gar nicht
           mehr steht. Die Seite ist umgezogen, ihr Kopf war zurueckgeblieben.
           Gesehen hat es niemand, weil die Blaupause keinen Fixstern hatte:
           Golden bezeugt nur, was in seiner Szenenliste steht. Sie hat jetzt
           einen. */ ?>
        <h1><?php echo cluster_one_page_h1('Kranz'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php cluster_one_render_kranz_tabs('cluster_one_blueprint'); ?>
<?php
if (isset($_GET['bp'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $c1_b = (string) wp_unslash($_GET['bp']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler; nur als Wort 'nothing' oder als Zahl (int) benutzt, nie ausgegeben
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(
        $c1_b === 'nothing'
            ? __('Everything from the blueprint is already here.', 'cluster-one-post-dolly')
            /* translators: %d: Anzahl der angelegten Bereiche. */
            : sprintf(_n('%d area added. Rename or delete it as you like — under Bismarck → Structure.', '%d areas added. Rename or delete them as you like — under Bismarck → Structure.', (int) $c1_b, 'cluster-one-post-dolly'), (int) $c1_b)
    ) . '</p></div>';
}
?>
        <h2 style="margin-top:1em;"><?php esc_html_e('Where you stand', 'cluster-one-post-dolly'); ?></h2>
        <p style="max-width:760px;opacity:.85;"><?php esc_html_e('Not a list of duties — a list of states. Some of it is needed, some is optional, and the node works long before everything is ticked.', 'cluster-one-post-dolly'); ?></p>
        <table class="widefat striped" style="max-width:820px;">
            <tbody>
<?php foreach (cluster_one_blueprint_state() as $c1_z) : ?>
                <tr>
                    <td style="width:2.4em;text-align:center;"><?php echo !empty($c1_z['ok'])
                        ? '<span style="color:#1a7f37;font-weight:600;">●</span>'
                        : '<span style="color:#c3c4c7;">○</span>'; ?></td>
<?php /* v1.9.7 (H199 + H208) — HIER STAND EINE VERKNUEPFUNG OHNE BEDINGUNG,
   UND SIE SAH NICHT WIE EINE AUS.

   H199: die Zeile setzte esc_url($c1_z['url']) bedingungslos in ein
   a-Element ein. Seit H198 kann `url` leer sein, wenn das Organ fehlt —
   esc_url('') gibt die leere Zeichenkette zurueck,
   und ein <a href=""> laedt beim Klick dieselbe Seite neu. Mein Kommentar
   dreissig Zeilen weiter oben sagte damals schon „dann steht der Posten ohne
   Knopf da". Die Vorlage tat es nicht.

       Ein Kommentar, der beschreibt, was der Code tun soll, ist eine Absicht.
       Erst die Zeile daneben ist die Tatsache.

   H208: Christian hat vier Runden lang einen KNOPF gesucht, den es nie gab —
   weil ich das Wort in H198 benutzt und dann in vier Dokumente uebernommen
   habe. Der NAME war die Verknuepfung, in einer Spalte, die sonst nur
   Zustaende zeigt.

       Ein Wort, das man einmal fuer eine Sache benutzt hat, wird beim
       vierten Mal zur Beschreibung.

   Deshalb jetzt zweierlei: keine Verknuepfung ohne Ziel, und die, die es
   gibt, traegt einen sichtbaren Pfeil. Kein Knopf — ein Weg, der aussieht
   wie einer. */ ?>
                    <td style="white-space:nowrap;"><?php
                        $c1_url = (string) ($c1_z['url'] ?? '');
                        if ($c1_url !== '') {
                            printf('<a class="cluster-one-weg" href="%s">%s <span aria-hidden="true">&rarr;</span></a>',
                                esc_url($c1_url), esc_html($c1_z['name']));
                        } else {
                            echo esc_html($c1_z['name']);
                        }
                    ?></td>
                    <td style="opacity:.75;"><?php echo esc_html($c1_z['art']); ?></td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>

<?php /* v0.6.2: Die zwei Prometheus-Raeume. Sie werden IMMER angeboten, auch
   ohne eingerichtete KI — verstecken nimmt die Sichtbarkeit, und wer nicht
   weiss, dass es sie gibt, fragt nicht danach. Fehlt die KI, sagt es der
   Hinweis darunter: eine Blaupause zeigt, was da ist, und sagt, was fehlt. */ ?>
        <?php $c1_bp_r = cluster_one_blueprint_rooms_missing(); ?>
        <?php if ($sysop && $c1_bp_r) : ?>
            <h2 style="margin-top:2em;"><?php esc_html_e('Two rooms for the questions that come first', 'cluster-one-post-dolly'); ?></h2>
            <p style="max-width:760px;opacity:.85;"><?php esc_html_e('There are ten thousand guides, and all of them assume you already know what you want. These two do not. Each comes with a preamble the AIs read — change it whenever you like, it is yours.', 'cluster-one-post-dolly'); ?></p>
            <?php /* v1.9.10 — dieselbe Frage wie in blueprint_state(): gibt es
               ein KONTO, nicht: steht etwas in der Liste. Sonst bleibt der
               Satz "hier antwortet noch keine KI" genau dann verborgen, wenn
               er gebraucht wird. */ ?>
            <?php if (count(cluster_one_ai_identities_live()) === 0) : ?>
                <p style="max-width:760px;opacity:.75;"><?php esc_html_e('You can create them now, but no AI will answer in them until you have set up access keys above.', 'cluster-one-post-dolly'); ?></p>
            <?php endif; ?>
            <?php foreach ($c1_bp_r as $c1_k => $c1_d) : ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:.5em 0;">
                    <?php wp_nonce_field('cluster_one_blueprint_room'); ?>
                    <input type="hidden" name="action" value="cluster_one_blueprint_room">
                    <input type="hidden" name="room" value="<?php echo esc_attr($c1_k); ?>">
                    <button type="submit" class="button"><?php echo esc_html(sprintf(
                        /* translators: %s: name of the room to be created */
                        __('Create “%s”', 'cluster-one-post-dolly'),
                        $c1_d['name']
                    )); ?></button>
                </form>
            <?php endforeach; ?>
        <?php endif; ?>
        <h2 style="margin-top:2em;"><?php esc_html_e('The blueprint', 'cluster-one-post-dolly'); ?></h2>
<?php /* v1.9.7 (H209) — HIER STAND EINE ZAHL, DIE NICHT MITGEWACHSEN IST.
   Der Satz lautete „Four areas that have been lived in for five weeks."
   cluster_one_blueprint_areas() liefert SECHS. Kruse kam in v0.6.7 dazu, mit
   vierzehn Zeilen Begruendung; SysOp in v0.8.5, mit sieben. Zweimal hat jemand
   einen Bereich sorgfaeltig begruendet hinzugefuegt und den Satz darueber, der
   ihn zaehlt, nicht gelesen. Seit dem 30.08. stand hier vier gegen sechs.

       Eine Zahl im Fliesstext waechst nicht mit. Sie ist eine Behauptung mit
       Verfallsdatum — und sie steht dort, wo niemand nach ihr sucht.

   „Fuenf Wochen" faellt ersatzlos weg: die Zahl kann kein Code ausrechnen,
   und sie war beim Schreiben richtig und eine Woche spaeter falsch.

       Wer eine Zahl nicht messen kann, darf sie nicht behaupten. Er darf sie
       weglassen.

   NICHT „sechs schreiben": dann bricht der siebte Bereich den Satz erneut. */ ?>
        <p style="max-width:760px;opacity:.85;"><?php
            $c1_anzahl = count(cluster_one_blueprint_areas());
            echo esc_html(sprintf(
                /* translators: %d: number of areas in the blueprint */
                _n('%d area that has been lived in. It is a suggestion, not an order: rename it, delete it, add your own. Deciding what you do not need is easier than inventing what you might.',
                   '%d areas that have been lived in. They are a suggestion, not an order: rename them, delete them, add your own. Deciding what you do not need is easier than inventing what you might.',
                   $c1_anzahl, 'cluster-one-post-dolly'),
                $c1_anzahl
            ));
        ?></p>
        <table class="widefat striped" style="max-width:820px;">
            <tbody>
<?php foreach (cluster_one_blueprint_areas() as $c1_n => $c1_zweck) : $c1_da = !isset($fehlt[$c1_n]); ?>
                <tr>
                    <td style="width:2.4em;text-align:center;"><?php echo $c1_da
                        ? '<span style="color:#1a7f37;font-weight:600;" title="' . esc_attr__('already here', 'cluster-one-post-dolly') . '">●</span>'
                        : '<span style="color:#c3c4c7;">○</span>'; ?></td>
                    <td style="white-space:nowrap;"><strong><?php echo esc_html($c1_n); ?></strong></td>
                    <td style="opacity:.8;"><?php echo esc_html($c1_zweck); ?></td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
<?php if ($sysop && $fehlt) : ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:1em;">
            <?php wp_nonce_field('cluster_one_blueprint_apply'); ?>
            <input type="hidden" name="action" value="cluster_one_blueprint_apply">
            <button type="submit" class="button button-primary"><?php echo esc_html(sprintf(
                /* translators: %d: number of areas */
                _n('Add the missing area (%d)', 'Add the missing areas (%d)', count($fehlt), 'cluster-one-post-dolly'),
                count($fehlt)
            )); ?></button>
            <span class="description"><?php esc_html_e('Only what is missing is added. Nothing is overwritten, nothing is deleted.', 'cluster-one-post-dolly'); ?></span>
        </form>
<?php elseif (!$fehlt) : ?>
        <p style="opacity:.6;"><?php
            /* v1.9.7 (H209) — die zweite eingefrorene Zahl, im selben Absatz
               wie die erste. Sie sagte „All four are here." bei sechs.
               Mein erster Entwurf schrieb die Einzahl als WORT aus („The one
               area is here"), und W36 wurde dafuer rot. Er hat den Riegel von
               _n() nicht gekannt — die Einzahlform gilt ja nur bei genau eins,
               also war das Wort richtig. Aber sein Rot war trotzdem der
               bessere Rat: eine ausgeschriebene Zahl in einem uebersetzten
               Satz ist genau die Form, gegen die H209 sich richtet.
                   Ein Zeuge, der aus dem falschen Grund rot wird, kann
                   trotzdem auf das richtige zeigen — und dann korrigiert man
                   den Code, nicht ihn.
               Deshalb steht jetzt %d in BEIDEN Formen. */
            $c1_anzahl = count(cluster_one_blueprint_areas());
            echo esc_html(sprintf(
                /* translators: %d: number of areas in the blueprint */
                _n('%d area is here. Nothing to do.', 'All %d are here. Nothing to do.',
                   $c1_anzahl, 'cluster-one-post-dolly'),
                $c1_anzahl
            ));
        ?></p>
<?php endif; ?>
        <p style="max-width:760px;margin-top:2em;opacity:.75;font-size:.92em;"><?php esc_html_e('What the blueprint cannot give you: keys, people, and the ones you would like to know. Those are yours — see the list above for where they live.', 'cluster-one-post-dolly'); ?></p>
    </div>
    <?php
}

}
