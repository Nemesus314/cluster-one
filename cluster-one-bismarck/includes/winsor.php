<?php
/**
 * Cluster One — Modul winsor (DER SCHNITT, v0.0.100 -> Module).
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
/* v1.9.14 (VERTRAG 27 §4) — DIE MELDUNG STEHT VOR DEM RIEGEL.

   Bis hierher stand cluster_one_hier() INNERHALB des Riegels. Damit meldete
   sich diese Datei nur, wenn sie auch LAEUFT. Liegt sie in fuenf Paketen und
   laeuft in einem, schweigen vier Kopien — auch ueber ihre eigene Existenz.

       Solange eine Datei fuenfmal liegt und einmal spricht, ist die
       Tabelle keine Landkarte, sondern eine Anwesenheitsliste der Lauten.

   Der dritte Wert ist eine Aussage ueber DIESE KOPIE, nicht ueber die
   anderen: „ich deklariere" oder „ich deklariere nicht". Wer verdraengt
   hat, ist eine BEZIEHUNG und gehoert dem Sammler (Maillart, 11.09.).

   DIE BEDINGUNG WIRD EINMAL GELESEN und traegt beide Folgen — die Meldung
   und den Riegel. Zweimal dieselbe Frage zu stellen hiesse, zwei Antworten
   zu erlauben, wo es eine gibt.

   KEIN RIEGEL WIRD ENTFERNT. Die Verdraengung bleibt, wie sie ist; nur ihr
   Schweigen faellt.

   OHNE $c1_winsor_hoeher: die Vorlage aus §4 traegt ein
   `!$c1_<organ>_hoeher &&` vor der Frage. Diese Datei hat keinen
   Fassungsvergleich — gemessen: `hoeher` und `version_compare` kommen hier
   kein einziges Mal vor; im ganzen Baum nur in faust.php und anti-babel.php.
   Ein uebernommener Term ohne Variable waere eine Warnung je Aufruf und ein
   `!null`, das immer wahr ist. Weggelassen und hier gesagt, nicht
   stillschweigend.

   DIE LEERE FASSUNG IST EINE ENTSCHEIDUNG, kein Platzhalter: '' heisst,
   Kasper liest die Fassung am Wirt (H118, v1.9.4) — „eine geliehene Nummer
   ist schlimmer als keine". Sie steht nur deshalb da, weil der dritte Wert
   die dritte Stelle braucht.

   Bezeugt von zeuge-melder.php, fuenf Welten. */
$cluster_one_winsor_laeuft = !function_exists('cluster_one_render_winsor_tab');
cluster_one_hier('winsor', '', $cluster_one_winsor_laeuft);

if ($cluster_one_winsor_laeuft) {

/** Labels-Reiter (v0.0.78): die Außenprojektionen des Knotens — Entwürfe
   und (später) Veröffentlichtes. Bewusst OHNE Zähl- oder Vergleichs-Spalten
   (Vergleichsverbot als Bauvorschrift: Labels werden nirgends gezählt
   oder aggregiert angezeigt). */
/** v0.0.95: Das Abklingbecken — lesbar, aber erloschen. */
function cluster_one_render_winsor_tab(): void {
    $threads = cluster_one_get_forum_threads();
    $rooms   = cluster_one_get_rooms();
    ?>
<?php /* v0.7.1: Eine Ueberschrift vor dem Fragezeichen — Christians Wunsch.
   Der Reiter heisst "Winsor", der Bereich ist das Abklingbecken; ohne die
   Ueberschrift stand der einleitende Satz ohne Dach. */ ?>
    <h2 style="margin-top:1em;"><?php esc_html_e('Cooling pool', 'cluster-one-bismarck');
        /* v1.9.13 (W52) — zwei Klinken, Form aus kern.php:2216. */
        echo function_exists('cluster_one_help')
            ? cluster_one_help('winsor') // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_help() gibt cluster_one_help_bubble() zurueck (wpautop(esc_html) im Innern) oder ''
            : cluster_one_help_eigen('winsor'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_help_eigen() gibt cluster_one_help_bubble() zurueck (wpautop(esc_html) im Innern) oder '' ?></h2>
    <p style="max-width:720px;opacity:.85;"><?php esc_html_e('The cooling pool: finished and failed projects, expired meetings, retired labels. Everything stays readable for those who could read it before — but the reactor is off: no new words, no AI triggers, no rounds. Only labels still accept a sparse, witnessed note (with a warning first). Lifting things out again is one witnessed act.', 'cluster-one-bismarck'); ?></p>
<?php /* v0.8.2: EINE TABELLE STATT ZWEIER — Christians Befund.

   Hier standen zwei Listen untereinander, "Threads im Becken" und "Chats im
   Becken", obwohl meistens PAARE darin liegen: Ein Thema und sein Chat gehen
   gemeinsam ins Becken, und der Wiederherstellen-Knopf stand ohnehin nur am
   Thema (Paare hebt man am Thread).

   Christian fragte, ob man die Kopplung erzwingen sollte. Meine Antwort war
   nein, und die Gruende sind konkret: FREMDE LABELS sind Themen ohne Chat (bei
   fuenfzig gezogenen entstuenden fuenfzig leere Raeume, und der Gespraechs-
   partner sitzt auf einem anderen Knoten) · WILDE braucht keinen, die
   Diskussion steht seit 0.5.1 UNTER dem Beitrag · GURU MEDITATION ist ein Raum
   ohne Protokoll · TERMINAL sagt woertlich, dass hier nichts hinauswandert ·
   dazu Bruecken- und Hallo-Chats, die nie ein Thema haben.

   Also nicht das Modell aendern, sondern die Anzeige: eine Tabelle, eine Spalte
   ART. Das Paar wird EINE Zeile, die Einzelgaenger stehen trotzdem da. */ ?>
    <table class="widefat striped" style="max-width:900px;">
        <thead><tr>
            <th><?php esc_html_e('Name', 'cluster-one-bismarck'); ?></th>
            <th style="width:12em;"><?php esc_html_e('Kind', 'cluster-one-bismarck'); ?></th>
            <th style="width:12em;"><?php esc_html_e('In the pool since', 'cluster-one-bismarck'); ?></th>
            <th style="width:10em;text-align:right;"><?php esc_html_e('Actions', 'cluster-one-bismarck'); ?></th>
        </tr></thead>
        <tbody>
<?php
        $c1_any = false;
        /* Zuerst die Themen — sie tragen den Wiederherstellen-Knopf auch fuer
           ihr Paar. Danach nur noch die Raeume, die KEIN Thema haben; die
           anderen sind oben schon als Paar aufgefuehrt. */
        foreach ($threads as $c1_wt_id => $c1_wt) :
            if (!cluster_one_container_is_archived($c1_wt)) { continue; }
            if (!cluster_one_group_visibility_can_view($c1_wt) && !cluster_one_thread_is_ai_diary($c1_wt) && !cluster_one_is_sysop()) { continue; }
            $c1_rid  = (string) ($c1_wt['linked_room'] ?? '');
            $c1_paar = $c1_rid !== '' && isset($rooms[$c1_rid]);
            $c1_any  = true; ?>
            <tr>
                <td><a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode((string) $c1_wt_id))); ?>"><?php echo esc_html((string) ($c1_wt['title'] ?? '')); ?></a><?php if (cluster_one_thread_is_label($c1_wt)) { echo ' ' . cluster_one_edge_icon('label', __('Label', 'cluster-one-bismarck')); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_edge_icon() gibt fertiges HTML zurueck (esc_attr im Innern) ?></td>
                <?php /* v0.9.24: DER CHAT HATTE KEINEN WEG. Christians Befund: Die
                   Spalte sagt "Thread + Chat", aber verlinkt war nur der Thread —
                   und aus der normalen Chat-Liste verschwindet ein archivierter
                   Raum ja gerade. Wer den Chat sehen wollte, kam nirgendwo hin.

                   EINE SPALTE, DIE ZWEI DINGE NENNT, MUSS ZU BEIDEN FUEHREN. */ ?>
                <td style="opacity:.8;"><?php
                    if ($c1_paar) {
                        $c1_wr_id = (string) ($c1_wt['linked_room'] ?? '');
                        echo esc_html__('Thread', 'cluster-one-bismarck') . ' + ';
                        echo '<a href="' . esc_url(admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($c1_wr_id))) . '">'
                           . esc_html__('chat', 'cluster-one-bismarck') . '</a>';
                    } else {
                        echo esc_html__('Thread only', 'cluster-one-bismarck');
                    }
                ?></td>
                <td style="opacity:.6;"><?php echo esc_html(date_i18n(get_option('date_format'), (int) ($c1_wt['archived'] ?? 0))); ?></td>
                <td style="text-align:right;">
                    <?php if (cluster_one_is_sysop()) : ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                        <?php wp_nonce_field('cluster_one_winsor_toggle'); ?>
                        <input type="hidden" name="action" value="cluster_one_winsor_toggle">
                        <input type="hidden" name="ctype" value="thread">
                        <input type="hidden" name="cid" value="<?php echo esc_attr((string) $c1_wt_id); ?>">
                        <input type="hidden" name="op" value="restore">
                        <button type="submit" class="button button-small" title="<?php echo esc_attr__('Back where it came from — its area is kept', 'cluster-one-bismarck'); ?>"><span class="dashicons dashicons-undo" aria-hidden="true" style="font-size:16px;width:16px;height:16px;vertical-align:text-bottom;"></span> <?php esc_html_e('Lift out', 'cluster-one-bismarck'); ?></button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
<?php   endforeach;
        foreach ($rooms as $c1_wr_id => $c1_wr) :
            if (!cluster_one_container_is_archived($c1_wr)) { continue; }
            if (!cluster_one_group_visibility_can_view($c1_wr) && !cluster_one_is_sysop()) { continue; }
            if ((string) ($c1_wr['linked_thread'] ?? '') !== '') { continue; } // steht oben als Paar
            $c1_any = true; ?>
            <tr>
                <td><a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode((string) $c1_wr_id))); ?>"><?php echo esc_html((string) ($c1_wr['name'] ?? '')); ?></a></td>
                <td style="opacity:.8;"><?php esc_html_e('Chat only', 'cluster-one-bismarck'); ?></td>
                <td style="opacity:.6;"><?php echo esc_html(date_i18n(get_option('date_format'), (int) ($c1_wr['archived'] ?? 0))); ?></td>
                <td style="text-align:right;">
                    <?php if (cluster_one_is_sysop()) : ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                        <?php wp_nonce_field('cluster_one_winsor_toggle'); ?>
                        <input type="hidden" name="action" value="cluster_one_winsor_toggle">
                        <input type="hidden" name="ctype" value="room">
                        <input type="hidden" name="cid" value="<?php echo esc_attr((string) $c1_wr_id); ?>">
                        <input type="hidden" name="op" value="restore">
                        <button type="submit" class="button button-small" title="<?php echo esc_attr__('Back where it came from — its area is kept', 'cluster-one-bismarck'); ?>"><span class="dashicons dashicons-undo" aria-hidden="true" style="font-size:16px;width:16px;height:16px;vertical-align:text-bottom;"></span> <?php esc_html_e('Lift out', 'cluster-one-bismarck'); ?></button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
<?php   endforeach;
        if (!$c1_any) : ?>
            <tr><td colspan="4" style="opacity:.5;"><?php esc_html_e('The pool is empty.', 'cluster-one-bismarck'); ?></td></tr>
<?php   endif; ?>
        </tbody>
    </table>
    <?php
}

add_action('admin_post_cluster_one_winsor_toggle', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_winsor_toggle');
    /* v0.3.5: Fremd-Labels sind schreibgeschuetzt — auch hier. Beim Zaehlen aller
       Handler, die Threads oder Beitraege aendern, war diese Tuer offen: die
       Sperre sass in fuenf Handlern, aber nicht in allen. Christian ist beim
       Testen genau hindurchgegangen. */
    $c1_tid_fg = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    if ($c1_tid_fg === '' && isset($_POST['room'])) {
        $c1_r_fg  = cluster_one_get_rooms();
        $c1_rid_fg = sanitize_text_field(wp_unslash($_POST['room']));
        $c1_tid_fg = (string) ($c1_r_fg[$c1_rid_fg]['linked_thread'] ?? '');
    }
    $c1_thr_fg = cluster_one_get_forum_threads();
    if ($c1_tid_fg !== '' && isset($c1_thr_fg[$c1_tid_fg]) && cluster_one_thread_is_foreign($c1_thr_fg[$c1_tid_fg])) {
        wp_die('-1', 403);
    }
    $type = isset($_POST['ctype']) ? sanitize_key(wp_unslash($_POST['ctype'])) : '';
    $id   = isset($_POST['cid']) ? sanitize_text_field(wp_unslash($_POST['cid'])) : '';
    $op   = (isset($_POST['op']) && sanitize_key(wp_unslash($_POST['op'])) === 'restore') ? 'restore' : 'archive';
    $threads = cluster_one_get_forum_threads();
    $rooms   = cluster_one_get_rooms();
    $tid = ''; $rid = '';
    if ($type === 'thread' && isset($threads[$id])) {
        $tid = $id;
        $rid = (string) ($threads[$id]['linked_room'] ?? '');
        if ($rid !== '' && !isset($rooms[$rid])) { $rid = ''; }
    } elseif ($type === 'room' && isset($rooms[$id])) {
        $rid = $id;
        $tid = (string) ($rooms[$id]['linked_thread'] ?? '');
        if ($tid !== '' && !isset($threads[$tid])) { $tid = ''; }
    } else {
        wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck&tab=winsor'));
        exit;
    }
    $val = $op === 'archive' ? time() : 0;
    $msg = $op === 'archive' ? __('Laid into the cooling pool.', 'cluster-one-bismarck') : __('Lifted out of the pool.', 'cluster-one-bismarck');
    if ($tid !== '') {
        $threads[$tid]['archived'] = $val;
        cluster_one_save_forum_threads($threads);
        $p = cluster_one_get_forum_posts($tid); $p[] = cluster_one_system_message($msg);
        cluster_one_save_forum_posts($tid, $p);
        cluster_one_event_emit(array('type' => $op === 'archive' ? 'container_archived' : 'container_restored',
            'container_type' => 'thread', 'container_id' => $tid, 'actor_id' => cluster_one_actor_id_human(),
            'visibility' => cluster_one_event_visibility($threads[$tid])), true);
    }
    if ($rid !== '') {
        $rooms[$rid]['archived'] = $val;
        cluster_one_save_rooms($rooms);
        $m = cluster_one_get_room_messages($rid); $m[] = cluster_one_system_message($msg);
        cluster_one_save_room_messages($rid, $m);
        cluster_one_event_emit(array('type' => $op === 'archive' ? 'container_archived' : 'container_restored',
            'container_type' => 'room', 'container_id' => $rid, 'actor_id' => cluster_one_actor_id_human(),
            'visibility' => cluster_one_event_visibility($rooms[$rid])), true);
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck&tab=winsor'));
    exit;
});


/* v1.9.13 — DIE ZEILE AUS DER NAMENSTABELLE, JETZT IM ORGAN.

   Der Steckbrief des Abklingbeckens. Es IST diese Datei.

   JEDES ORGAN NENNT DAS SEINE SELBST. Dieselbe Regel und derselbe Weg wie
   Kleinrocks Zeile seit 0.9.66: der Text zieht dorthin, wo beide Haeuser ihn
   haben, statt in eine zweite Kopie. Er steht in der Namenstabelle an
   derselben Stelle wie immer — nur nicht mehr DORT geschrieben.

   feynman.php holt ihn ueber cluster_one_steckbrief_von(), und der Sammler ist
   geriegelt: wer nicht da ist, liefert nichts. Deshalb faellt die Tabelle
   nicht, wenn dieses Organ fehlt — sie wird um eine Zeile kuerzer.

   Spalten: Name · Person · Lebensdaten · warum dieser Name · was er NICHT
   meint · Langtext. Namen werden nicht uebersetzt, Begruendungen schon. */
function cluster_one_winsor_steckbrief(): array {
    return array('Winsor', 'Galen Winsor', '1926–2008',
              __('The cooling pool: material that is still warm but no longer in the reactor. Readable for whoever could read it before — but extinguished.', 'cluster-one-bismarck'),
              __('His claims about radiation. He denied that the Three Mile Island meltdown happened and spoke of an energy cartel; his safety claims do not hold up. The name is for the pool, not for the thesis.', 'cluster-one-bismarck'),
              __('The Winsor area keeps things tidy: finished, abandoned or failed projects can be moved here to preserve the overview in the productive areas — extinguished, not deleted, and retrievable at any time. Winsor is the node’s virtual cooling pool. Its namesake, Galen Winsor, worked in fuel reprocessing and was convinced that the dangers of radioactivity were overstated — which he “demonstrated” by swimming in actual spent fuel pools. Wrong message, spectacular stunt. And for me the reason to name the cooling pool after him.', 'cluster-one-bismarck'));
}

}
