<?php
/**
 * Cluster One — Modul bismarck-seiten (Züge 11–13: Feinschnitt aus bismarck).
 * Reiner Umzug; jede Funktion byte-identisch zur Baseline v0.0.100.
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

   OHNE $c1_bismarck_seiten_hoeher: die Vorlage aus §4 traegt ein
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
$cluster_one_bismarck_seiten_laeuft = !function_exists('cluster_one_render_bismarck_page');
cluster_one_hier('bismarck-seiten', '', $cluster_one_bismarck_seiten_laeuft);

if ($cluster_one_bismarck_seiten_laeuft) {

function cluster_one_render_bismarck_page(): void {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    // Einstellungen sind seit v0.0.40 nach Kranz gewandert — alte Lesezeichen
    // auf ?tab=einstellungen sanft dorthin weiterleiten statt ins Leere laufen
    // zu lassen.
    $tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'rooms'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    if ($tab === 'einstellungen') {
        wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck_settings'));
        exit;
    }
    /* v0.9.43: 'projekte' ist der neue Name; 'rooms' und 'forum' bleiben als
       Adressen gueltig und landen beide dort. Alte Lesezeichen und die
       Ruecksprung-URLs aus den Handlern brechen damit nicht. */
    if (in_array($tab, array('rooms', 'forum'), true)) {
        $tab = 'projekte';
    }
    if (!in_array($tab, array('projekte', 'structure', 'labels', 'winsor'), true)) { // v0.0.83: labels fehlte — der Reiter fiel auf Chats zurück
        $tab = 'projekte';
    }
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Bismarck'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php
        /* 0.2.58 (27.10.2026, Neo): der Stempel – Fassung, Werft, Boden-Stand (Boden 1.17.6). */
        if (function_exists('cluster_one_stempel')) { echo cluster_one_stempel(__FILE__, defined('CLUSTER_ONE_BISMARCK_VERSION') ? (string) CLUSTER_ONE_BISMARCK_VERSION : ''); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapt in der Funktion
        cluster_one_render_bismarck_tabs($tab);
        /* 18.10.2026 (Neo): Leser fuer `wilde_exists=1` (platon.php, Projekt
           anlegen) — der Handler leitete bisher auf eine Seite, die es nicht
           gab, und der Grund kam nie an. */
        if (isset($_GET['wilde_exists'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('Not created: you already have a Wilde thread. One per person — edit the existing one instead.', 'cluster-one-bismarck') . '</p></div>';
        }
        if ($tab === 'projekte') {
            cluster_one_render_forum_threads();
        } elseif ($tab === 'labels') {
            cluster_one_render_bismarck_labels(); // v0.0.78
        } elseif ($tab === 'winsor') {
            cluster_one_render_winsor_tab(); // v0.0.95
        } elseif ($tab === 'structure') {
            cluster_one_render_structure_tab(); // v0.0.66
        }
        ?>
    </div>
    <?php
}

function cluster_one_render_bismarck_labels(): void {
    $threads = cluster_one_get_forum_threads();
    $labels  = array_filter($threads, function ($c1_l95) {
        return cluster_one_thread_is_label($c1_l95) && !cluster_one_container_is_archived($c1_l95); // v0.0.95: archivierte Labels wohnen in Winsor
    });
    uasort($labels, function ($a, $b) {
        return (int) ($b['created'] ?? 0) <=> (int) ($a['created'] ?? 0);
    });
    ?>
    <h2 style="margin-top:1em;"><?php esc_html_e('Own labels', 'cluster-one-bismarck'); ?></h2>
    <?php /* v0.3.6: Ueberschriften wie in der Foren- und Chat-Ansicht — es sind
       zwei Bereiche, und man soll auf einen Blick sehen, welcher gerade da steht. */ ?>
    <p style="max-width:760px;opacity:.85;"><?php esc_html_e('A label is a project’s outward projection — born by copying a post from its project thread (SysOp only, the “→ Label” button there). Drafts stay on this node; publishing to the network is a separate, second act (a later version). Appends are sparse and witnessed: a label is never silently rewritten — changes are listed below it, with the reason. English recommended, as the network’s lingua franca.', 'cluster-one-bismarck'); ?></p>
<?php
/* v0.8.1: DIE KI-PRUEFUNG AUF DEN EIGENEN LABELS IST WEG — Christians Befund.

   Sie fragte "wie weit traegt unser eigenes Label nach draussen" und legte die
   Antwort als BEITRAG IN JEDES LABEL. Zwei Gruende, warum das nicht hierher
   gehoert:

   1. FALSCHER ZEITPUNKT. Diese Frage gehoert in den Chat oder den Thread,
      BEVOR der SysOp auf "→ Label" drueckt. Ist das Label da, ist die
      Aussenprojektion schon gemacht.

   2. FALSCHER ORT — und das wiegt schwerer, als es beim Lesen aussieht: Die
      Route /labels gibt die letzten 50 BEITRAEGE eines Labels heraus, nicht
      nur den ersten. Die Einschaetzung der eigenen KI waere also mitgereist,
      und ein fremder Knoten haette beim Ziehen mitgelesen, wie das eigene Haus
      ueber sein eigenes Projekt urteilt.

   Bei den FREMD-Labels bleibt die Pruefung: Dort ist sie eine Arbeitsnotiz im
   eigenen Haus und reist nirgendwohin. */
?>
<?php if (empty($labels)) : ?>
        <p style="opacity:.6;"><?php esc_html_e('No labels yet. Grow a project past its inverse event horizon first.', 'cluster-one-bismarck'); ?></p>
<?php else : ?>
        <table class="widefat striped" style="max-width:960px;margin-top:.5em;">
            <thead><tr>
                <?php /* v0.3.7: Dieselbe Lampe wie bei den Fremd-Labels — nur ist die
                   Frage eine andere: nicht "passt das zu uns", sondern "wie weit
                   traegt das nach draussen". */ ?>
                <th style="width:2em;"></th>
                <th><?php esc_html_e('Label', 'cluster-one-bismarck'); ?></th>
                <th style="width:12em;"><?php esc_html_e('Status', 'cluster-one-bismarck'); ?></th>
                <th><?php esc_html_e('Project', 'cluster-one-bismarck'); ?></th>
                <?php /* v0.3.6: "Herkunft" war ein Wort fuer zwei Angaben. Wer ein
                   Label anschaut, will beides wissen: welcher KNOTEN es zeigt und
                   welcher MENSCH dahintersteht. */ ?>
                <?php /* v0.3.8: Die URL statt des Namens — zwei Knoten koennen
                   denselben Namen waehlen, die Adresse ist eindeutig. */ ?>
                <th style="width:14em;"><?php esc_html_e('Node (URL)', 'cluster-one-bismarck'); ?></th>
                <th style="width:10em;"><?php esc_html_e('SysOp', 'cluster-one-bismarck'); ?></th>
                <th style="width:11em;"><?php esc_html_e('Created', 'cluster-one-bismarck'); ?></th>
                <th style="text-align:right;"><?php esc_html_e('Actions', 'cluster-one-bismarck'); ?></th>
            </tr></thead>
            <tbody>
            <?php foreach ($labels as $ltid => $lt) :
                $ptid = (string) ($lt['label_for'] ?? '');
            ?>
                <?php
                $c1_lv = cluster_one_foreign_verdict($lt);
                $c1_lf = array('gut' => '#1a7f37', 'mittel' => '#b26b00', 'fern' => '#8a8a8a', 'offen' => '#c3c4c7');
                $c1_lw = array(
                    'stark'  => __('The AI thinks this label carries — a stranger would understand it', 'cluster-one-bismarck'),
                    'mittel' => __('The AI thinks this label carries only partway', 'cluster-one-bismarck'),
                    'schwach'=> __('The AI thinks this says too little to a stranger', 'cluster-one-bismarck'),
                    'offen'  => __('Not checked yet', 'cluster-one-bismarck'),
                );
                ?>
                <tr>
                    <td style="text-align:center;"><span style="color:<?php echo esc_attr($c1_lf[cluster_one_verdict_tone($c1_lv)]); ?>;font-weight:600;" title="<?php echo esc_attr($c1_lw[$c1_lv] ?? ''); ?>"><?php echo $c1_lv === 'offen' ? '○' : '●'; ?></span></td>
                    <td style="white-space:nowrap;max-width:22em;overflow:hidden;text-overflow:ellipsis;"><?php if (!empty($lt['pinned'])) : ?><span title="<?php echo esc_attr__('The SysOp considers this important', 'cluster-one-bismarck'); ?>">📌</span> <?php endif; ?><strong><a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode((string) $ltid))); ?>" title="<?php echo esc_attr((string) ($lt['title'] ?? '')); ?>"><?php echo esc_html((string) ($lt['title'] ?? '')); ?></a></strong></td>
                    <td><?php echo (string) ($lt['label_status'] ?? 'draft') === 'draft' ? esc_html__('Draft (this node only)', 'cluster-one-bismarck') : esc_html__('Published', 'cluster-one-bismarck'); ?></td>
                    <td style="white-space:nowrap;max-width:16em;overflow:hidden;text-overflow:ellipsis;"><?php if ($ptid !== '' && isset($threads[$ptid]) && cluster_one_group_visibility_can_view($threads[$ptid])) : ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($ptid))); ?>"><?php echo esc_html((string) ($threads[$ptid]['title'] ?? '')); ?></a>
                    <?php else : ?><span style="opacity:.5;">—</span><?php endif; ?></td>
                    <td style="white-space:nowrap;opacity:.85;max-width:14em;overflow:hidden;text-overflow:ellipsis;"><?php $c1_hu = function_exists('home_url') ? (string) home_url('/') : ''; ?><span title="<?php echo esc_attr($c1_hu); ?>"><?php echo esc_html($c1_hu); ?></span></td>
                    <td style="white-space:nowrap;opacity:.85;"><?php echo esc_html(cluster_one_sysop_label()); ?></td>
                    <td><?php echo esc_html(date_i18n(get_option('date_format'), (int) ($lt['created'] ?? 0))); ?></td>
                    <td style="text-align:right;white-space:nowrap;">
<?php if (cluster_one_is_sysop()) : // v0.3.7: Anheften gilt fuer beide Seiten ?>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline;">
                            <?php wp_nonce_field('cluster_one_foreign_pin'); ?>
                            <input type="hidden" name="action" value="cluster_one_foreign_pin">
                            <input type="hidden" name="thread" value="<?php echo esc_attr((string) $ltid); ?>">
                            <button type="submit" style="background:none;border:0;padding:0 .3em;cursor:pointer;line-height:1;<?php echo empty($lt['pinned']) ? 'opacity:.45;' : 'color:#b26b00;'; ?>"
                                    title="<?php echo esc_attr(empty($lt['pinned']) ? __('Pin — stays on top in every view', 'cluster-one-bismarck') : __('Unpin', 'cluster-one-bismarck')); ?>"
                                    aria-label="<?php echo esc_attr(empty($lt['pinned']) ? __('Pin', 'cluster-one-bismarck') : __('Unpin', 'cluster-one-bismarck')); ?>">
                                <span class="dashicons dashicons-sticky" aria-hidden="true" style="font-size:18px;width:18px;height:18px;"></span>
                            </button>
                        </form>
<?php endif; ?>
<?php /* v0.3.6: Die Kopfzelle stand seit 0.3.5 da, die Datenzelle fehlte —
   derselbe Spaltenversatz wie damals im Mesh. Ein Label loeschen nimmt es aus
   dem oeffentlichen Raum; das Projekt im Forum bleibt. */
if (cluster_one_forum_thread_can_delete($lt, (string) $ltid)) : ?>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline;">
                            <?php wp_nonce_field('cluster_one_forum_thread_delete'); ?>
                            <input type="hidden" name="action" value="cluster_one_forum_thread_delete">
                            <input type="hidden" name="thread" value="<?php echo esc_attr((string) $ltid); ?>">
                            <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;color:#b32d2e;"
                                    title="<?php echo esc_attr__('Delete label — it leaves the public space, the project stays in the forum', 'cluster-one-bismarck'); ?>"
                                    aria-label="<?php echo esc_attr__('Delete label', 'cluster-one-bismarck'); ?>"
                                    onclick="return confirm('<?php echo esc_js(__('Delete this label? It disappears from the public space and from other nodes’ view. The project itself stays in the forum, untouched — you can give it a new label any time.', 'cluster-one-bismarck')); ?>');">
                                <span class="dashicons dashicons-trash" aria-hidden="true" style="font-size:18px;width:18px;height:18px;"></span>
                            </button>
                        </form>
<?php endif; ?>
                    </td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
<?php endif; ?>

        <?php /* v0.3.3: Eigene und fremde Labels stehen jetzt an EINEM Ort. Der
           Reiter beantwortet eine Frage — "was zeige ich, was zeigen die
           anderen" — und zwei Orte fuer eine Frage waren der Bruch, an dem
           Christians eigenes Label doppelt erschien. Die Threads bleiben, wo sie
           sind; hier wird gelistet und verlinkt. */ ?>
        <?php cluster_one_render_foreign_area(); ?>
    <?php
}


/* v0.0.93: Schnellknopf-Handler rückgebaut — tote Handler sind
   dieselbe Reißbrett-Lüge eine Ebene tiefer. */

/** v0.9.43: WAS VON DER CHAT-TABELLE UEBRIG IST.

   Die Liste ist mit dem Zusammenlegen entfallen — sie zeigte dasselbe Objekt
   wie die Forums-Liste, nur mit anderen Spalten und einer anderen Uhr. Was
   hier bleibt, sind die Meldungen der Raum-Handler und das Anlege-Formular.

   HALB FERTIG, UND ICH SAGE ES: Unter dem Reiter "Projekte" stehen deshalb
   vorerst ZWEI Anlege-Abschnitte — "Neues Thema" und "Neuer Raum". Das ist
   ein Uebergangszustand. Sie werden zu einem Formular, in das man einen
   Projektnamen tippt ("Haekeln") und das beide Haelften anlegt
   (Haekeln-Chat, Haekeln-Thread). Erst danach faellt auch der Zweig "Mit
   vorhandenem Thread paaren" weg.

   Warum nicht sofort: Das Anlegen ist ein Schreibweg, und die
   Eindeutigkeitspruefung muss dafuer ueber BEIDE Namensraeume laufen —
   heute werden nur Raumnamen geprueft, Thread-Titel nicht. Einen halben
   Schreibweg in derselben Version wie den groessten Umbau haette ich nicht
   verantwortet. Die Liste ist zusammengelegt und nachpruefbar; das Formular
   folgt als eigener, kleiner Schnitt. */
function cluster_one_render_bismarck_rooms(): void {
    if (isset($_GET['room_err'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        $e = sanitize_key(wp_unslash($_GET['room_err'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        if ($e === 'dup') {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('A room with that name already exists on this node. Names must be unique.', 'cluster-one-bismarck') . '</p></div>';
        } elseif ($e === 'invalid') {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Please enter a name.', 'cluster-one-bismarck') . '</p></div>';
        }
    }
    if (isset($_GET['room_msg']) && sanitize_key(wp_unslash($_GET['room_msg'])) === 'deleted') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Room deleted.', 'cluster-one-bismarck') . '</p></div>';
    }
    /* v0.9.49: cluster_one_bismarck_rooms_seite_neu() ist gegangen. Ein
       Formular, das nur eine Haelfte anlegt, ist unter einer Tabelle, die
       Paare zeigt, ein Weg, der etwas anderes tut als die Zeile behauptet.
       Was bleibt, sind die MELDUNGEN — das Loeschen kommt weiterhin hierher
       zurueck. */
}

/* v0.9.43: cluster_one_bismarck_rooms_seite_liste() ist entfallen — die
   Chat-Tabelle gibt es nicht mehr. Ihre Spalten Modus und KI leben in der
   Projekt-Tabelle weiter, "Angelegt" ist zugunsten von "Letzte Aktivitaet"
   gegangen, und "Zutritt" war schon in 0.9.41 fort. */
function cluster_one_render_room_page(): void {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    $id    = isset($_GET['room']) ? sanitize_text_field(wp_unslash($_GET['room'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $rooms = cluster_one_get_rooms();
    $room  = isset($rooms[$id]) ? $rooms[$id] : null;
    // Kein Zugriff = wirkt wie nicht gefunden, keine Existenz-Preisgabe.
    // v0.0.80: außer für den SysOp — Durchgriff, bezeugt lesend.
    $c1_room_peek = false;
    if ($room && cluster_one_group_visibility_can_view($room)) {
        /* v1.0.4: Ohne Kleinrock gibt es keine Klingel, die man leeren koennte.
           Gefunden von kreuzprobe.php beim ersten echten Schnitt — sechs Szenen,
           ein Fatal. Der Aufruf ist richtig, er stand nur nackt da. */
        if (function_exists('cluster_one_pings_clear')) {
            cluster_one_pings_clear($id); // v0.0.83: Zustellung angekommen
        }
    }
    if ($room && !cluster_one_group_visibility_can_view($room)) {
        if (cluster_one_is_sysop() && !cluster_one_room_is_antechamber($room)) {
            $c1_room_peek = true;
            if (!isset($_GET['view']) || $_GET['view'] !== 'config') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
                cluster_one_sysop_peek_witness('room', $id);
            }
        } else {
            // v0.0.94: Vorzimmer sind PN-SCHARF — auch für den SysOp kein
            // Blick und keine Config (Selbst-Einladungs-Hintertür). Die
            // ehrliche Absage statt „nicht gefunden":
            if ($room && cluster_one_room_is_antechamber($room) && cluster_one_is_sysop()) {
                echo '<div class="wrap"><h1>' . esc_html((string) ($room['name'] ?? '')) . '</h1><p style="max-width:680px;opacity:.85;">' . esc_html__('This is an anteroom — letter-grade private (your own decision): no witnessed look-in, no configuration. The owner manages the guest list. A WordPress admin can always force access; the system refuses to make it comfortable.', 'cluster-one-bismarck') . '</p></div>';
                return;
            }
            $room = null;
        }
    }
    $back  = admin_url('admin.php?page=cluster_one_bismarck&tab=rooms');
    $view  = (isset($_GET['view']) && $_GET['view'] === 'config' && cluster_one_is_sysop()) ? 'config' : 'chat'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    if ($room && cluster_one_room_is_antechamber($room) && $view === 'config') {
        $view = 'chat'; // v0.0.94: Vorzimmer kennen keine Config — Hausrecht statt Schraubenschlüssel
    }
    ?>
    <div class="wrap">
<?php if (!$room) : ?>
            <h1><?php echo cluster_one_page_h1('Bismarck'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
            <p style="opacity:.8;"><?php esc_html_e('Room not found.', 'cluster-one-bismarck'); ?></p>
            <p><a href="<?php echo esc_url($back); ?>">&larr; <?php esc_html_e('Back to rooms', 'cluster-one-bismarck'); ?></a></p>
<?php else : ?>
<?php
            cluster_one_room_seite_gaesteliste($room, $id);
            cluster_one_room_seite_kopf($room, $id, $back, $view);
            if ($view === 'config') {
                cluster_one_room_seite_config($room, $id);
            } else {
                cluster_one_room_seite_chatbereich($room, $id, $c1_room_peek);
            }
            ?>        <?php endif; ?>
    </div>
    <?php
}

/** Vorzimmer-Hausrecht: der Besitzer verwaltet die Gaesteliste selbst (v0.0.94).
 * Stufe B (0.1.0): byte-genau aus cluster_one_render_room_page herausgeloest, Golden-Master-bezeugt. */
function cluster_one_room_seite_gaesteliste(array $room, string $id): void { ?>
            <?php if (cluster_one_room_is_antechamber($room) && (int) ($room['owner'] ?? 0) === get_current_user_id()) :
                // v0.0.94: HAUSRECHT — der Besitzer verwaltet die Gäste seines
                // Vorzimmers selbst, hier auf der Raum-Seite (keine Config).
                $c1_guests = array_map('strval', (array) ($room['visible_to'] ?? array()));
            ?>
                <div style="border:1px solid #e0e0e0;border-radius:6px;padding:.6em .9em;margin:.6em 0;background:#fafafa;max-width:680px;">
                    <strong><?php esc_html_e('Guest list (your house right)', 'cluster-one-bismarck'); ?></strong>
                    <span style="opacity:.65;font-size:.85em;"> — <?php esc_html_e('humans of the whitelist; every change is witnessed', 'cluster-one-bismarck'); ?></span>
                    <div style="display:flex;gap:.5em;flex-wrap:wrap;margin-top:.5em;align-items:center;">
                        <?php foreach ($c1_guests as $c1_gk) :
                            $c1_gu = (int) str_replace('wp_', '', $c1_gk);
                            if ($c1_gu === get_current_user_id()) { continue; }
                            $c1_gud = get_userdata($c1_gu);
                        ?>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                                <?php wp_nonce_field('cluster_one_antechamber_guests'); ?>
                                <input type="hidden" name="action" value="cluster_one_antechamber_guests">
                                <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                                <input type="hidden" name="op" value="remove">
                                <input type="hidden" name="uid" value="<?php echo (int) $c1_gu; ?>">
                                <button type="submit" class="button button-small" title="<?php echo esc_attr__('Remove from the guest list', 'cluster-one-bismarck'); ?>"><?php echo esc_html($c1_gud ? $c1_gud->display_name : ('#' . $c1_gu)); ?> ✕</button>
                            </form>
<?php endforeach; ?>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:flex;gap:.3em;">
                            <?php wp_nonce_field('cluster_one_antechamber_guests'); ?>
                            <input type="hidden" name="action" value="cluster_one_antechamber_guests">
                            <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                            <input type="hidden" name="op" value="add">
                            <select name="uid">
                                <?php foreach (cluster_one_gregor_humans() as $c1_hu) :
                                    if ($c1_hu === get_current_user_id() || in_array('wp_' . $c1_hu, $c1_guests, true)) { continue; }
                                    $c1_hud = get_userdata($c1_hu);
                                ?>
                                    <option value="<?php echo (int) $c1_hu; ?>"><?php echo esc_html($c1_hud ? $c1_hud->display_name : ('#' . $c1_hu)); ?></option>
<?php endforeach; ?>
                            </select>
                            <button type="submit" class="button button-small"><?php esc_html_e('Invite', 'cluster-one-bismarck'); ?></button>
                        </form>
                    </div>
                </div>
<?php endif; ?>
<?php }

/** Raum-Kopf: Tabs, Titel, Meta, Verknuepfungen, Praeambel-Freigabe, Chat/Config-Leiste.
 * Stufe B (0.1.0): byte-genau herausgeloest, Golden-Master-bezeugt. */
function cluster_one_room_seite_kopf(array $room, string $id, string $back, string $view): void { ?>
            <?php cluster_one_render_bismarck_tabs('rooms'); ?>
            <h1><?php echo esc_html((string) ($room['name'] ?? '')); ?></h1>
<?php if (isset($_GET['created'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
                <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Room created.', 'cluster-one-bismarck'); ?></p></div>
<?php endif; ?>
            <p style="margin:.5em 0 1.2em;">
<?php /* v0.9.6: Die Anzeige der folgenlosen 'visibility' ist entfallen —
   siehe cluster_one_thread_can_become_label(). */ ?>
                &nbsp;·&nbsp;
                <span style="opacity:.7;"><?php esc_html_e('Created', 'cluster-one-bismarck'); ?>:</span>
                <?php echo esc_html(date_i18n(get_option('date_format'), (int) ($room['created'] ?? 0))); ?>
            </p>
            <?php
            $linked_tid = (string) ($room['linked_thread'] ?? '');
            $linked_ev  = cluster_one_room_linked_event($id);
            if ($linked_tid !== '' || $linked_ev) :
                $all_threads_here = cluster_one_get_forum_threads();
            ?>
                <p style="opacity:.75;font-size:.92em;">
<?php if ($linked_tid !== '' && isset($all_threads_here[$linked_tid])) : ?>
                        <?php esc_html_e('Linked thread:', 'cluster-one-bismarck'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($linked_tid))); ?>"><?php echo esc_html((string) ($all_threads_here[$linked_tid]['title'] ?? '')); ?></a>
<?php endif; ?>
<?php if ($linked_tid !== '' && $linked_ev) : ?> · <?php endif; ?>
<?php if ($linked_ev) : ?>
                        <?php esc_html_e('Part of event:', 'cluster-one-bismarck'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode((string) ($linked_ev['id'] ?? '')))); ?>"><?php echo esc_html((string) ($linked_ev['name'] ?? '')); ?></a>
<?php endif; ?>
                </p>
<?php endif; ?>

            <?php
            // v0.0.75 (Punkt 4): Präambel-Freigabe — der SysOp-Daumen wohnt im
            // Chat (Entscheidung SysOp 16.07.), nicht im Forum. Bedingung ist
            // die 1:1-Kante: verknüpfter Thread existiert UND ist ein
            // KI-Tagebuch UND der Betrachter ist SysOp. Die Erlaubnis ist ein
            // KLICK, kein gesprochener Satz — Fähigkeiten entstehen durch
            // explizite, bezeugte Akte, nie durch Sprach-Parsing (Häkchen-
            // Gate-Prinzip). Terminal-Bild des SysOps: chmod +w.
            $c1_grant_tid = (string) ($room['linked_thread'] ?? '');
            if ($c1_grant_tid !== '' && cluster_one_is_sysop()) :
                $c1_all_t = cluster_one_get_forum_threads();
                if (isset($c1_all_t[$c1_grant_tid]) && cluster_one_thread_is_ai_diary($c1_all_t[$c1_grant_tid])) :
                    /* v0.8.9: DER PRAEAMBEL-KNOPF MIT DREI ZUSTAENDEN.

                       Christians Entwurf: ein Element, das seinen Zustand
                       wechselt und in jedem sagt, was der naechste Klick tut.
                       Kurze Verben auf dem Etikett, die Erklaerung daneben —
                       ganze Saetze auf einem Knopf brechen jedes Layout.

                       Der dritte Zustand nennt die UHRZEIT der Antwort. Ohne
                       das waere es dieselbe Falle eine Stufe spaeter:
                       "die Antwort uebernehmen" — welche? */
                    $c1_pz  = cluster_one_diary_preamble_state($c1_grant_tid);
                    $c1_pts = (int) ($c1_all_t[$c1_grant_tid]['preamble_candidate_ts'] ?? 0);
            ?>
                <div style="margin:.2em 0 .8em;display:flex;align-items:center;gap:.6em;flex-wrap:wrap;">
<?php if ($c1_pz === 'kandidat') : ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                        <?php wp_nonce_field('cluster_one_diary_preamble_adopt'); ?>
                        <input type="hidden" name="action" value="cluster_one_diary_preamble_adopt">
                        <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                        <input type="hidden" name="op" value="adopt">
                        <button type="submit" class="button button-primary button-small"><?php echo esc_html(sprintf(
                            /* translators: %s: time of the AI's answer */
                            __('Adopt the answer from %s', 'cluster-one-bismarck'),
                            $c1_pts > 0 ? date_i18n(get_option('time_format'), $c1_pts) : '—'
                        )); ?></button>
                    </form>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                        <?php wp_nonce_field('cluster_one_diary_preamble_adopt'); ?>
                        <input type="hidden" name="action" value="cluster_one_diary_preamble_adopt">
                        <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                        <input type="hidden" name="op" value="discard">
                        <button type="submit" class="button-link" style="color:#8a8a8a;"><?php esc_html_e('Discard', 'cluster-one-bismarck'); ?></button>
                    </form>
                    <span style="opacity:.7;font-size:.92em;"><?php esc_html_e('It only becomes the preamble with this click — until then it is just a chat message.', 'cluster-one-bismarck'); ?></span>
<?php else : ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                        <?php wp_nonce_field('cluster_one_diary_preamble_grant'); ?>
                        <input type="hidden" name="action" value="cluster_one_diary_preamble_grant">
                        <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                        <button type="submit" class="button button-small"><?php echo esc_html($c1_pz === 'scharf'
                            ? __('Cancel', 'cluster-one-bismarck')
                            : __('Establish preamble', 'cluster-one-bismarck')); ?></button>
                    </form>
<?php /* v0.9.0: Der Zustand "scharf" sagt jetzt auch, was er VERHINDERT.

   Seit 0.8.8 schlaegt die Freigabe das Ziel — solange eine liegt, landet die
   Antwort im Chat statt im Tagebuch. Das ist gewollt (ein Ding nach dem
   anderen), stand aber nirgends: eine stille Einschraenkung.

   Christians Frage war, ob man die anderen Knoepfe waehrenddessen SPERREN
   sollte. Dagegen sprach zweierlei: Der Ausloese-Knopf ist in diesem Zustand
   das Werkzeug — ohne ihn kommt die Antwort nie. Und eine Sperre haette eine
   Sackgasse geschaffen, falls die KI nicht erreichbar ist.

   Also die Sperre als AUSKUNFT statt als Verriegelung. */ ?>
                    <span style="opacity:.7;font-size:.92em;"><?php echo esc_html($c1_pz === 'scharf'
                        ? __('Waiting for the next answer of the diary AI — it will be offered here, not written yet. While this is armed, the diary button writes nothing into the diary.', 'cluster-one-bismarck')
                        : __('Asks the diary AI for a preamble. You will see it before it counts.', 'cluster-one-bismarck')); ?></span>
<?php endif; ?>
                </div>
<?php endif; endif; ?>

            <p style="margin:.2em 0 1.2em;"><a href="<?php echo esc_url($back); ?>">&larr; <?php esc_html_e('Back to rooms', 'cluster-one-bismarck'); ?></a></p>

            <?php
            $chat_url   = admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id));
            $config_url = admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id) . '&view=config');
            ?>
            <h2 class="nav-tab-wrapper" style="margin-bottom:1.2em;">
                <a href="<?php echo esc_url($chat_url); ?>" class="nav-tab <?php echo $view === 'chat' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Chat', 'cluster-one-bismarck'); ?></a>
                <a href="<?php echo esc_url($config_url); ?>" class="nav-tab <?php echo $view === 'config' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Configuration', 'cluster-one-bismarck'); ?></a>
            </h2>

            <?php }

/** Konfigurations-Ansicht des Raums (KIs, Leiter, Modus, Uebersetzer, Bruecke, Thread,
 * Bereich, Sichtbarkeit, Menschen). Stufe B (0.1.0): byte-genau, Golden-Master-bezeugt. */
function cluster_one_room_seite_config(array $room, string $id): void { ?>

<?php if (isset($_GET['saved'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
                    <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Configuration saved.', 'cluster-one-bismarck'); ?></p></div>
<?php endif; ?>
<?php if (isset($_GET['renamed'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
                    <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Room renamed.', 'cluster-one-bismarck'); ?></p></div>
<?php endif; ?>
                <?php if (isset($_GET['rename_err'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
                    $re = sanitize_text_field(wp_unslash($_GET['rename_err'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
                    <div class="notice notice-error is-dismissible"><p><?php
                        echo esc_html($re === 'dup'
                            ? __('A room with that name already exists on this node. Names must be unique.', 'cluster-one-bismarck')
                            : __('Please enter a name.', 'cluster-one-bismarck')); ?></p></div>
<?php endif; ?>

<?php cluster_one_room_seite_config_umbenennen($room, $id); ?>                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:780px;">
                    <?php wp_nonce_field('cluster_one_room_config'); ?>
                    <input type="hidden" name="action" value="cluster_one_room_config_save">
                    <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">

<?php cluster_one_room_seite_config_kis($room); ?><?php cluster_one_room_seite_config_verbindungen($room); ?><?php cluster_one_room_seite_config_teilnehmer($room); ?>                    <p style="margin-top:1.5em;"><?php submit_button(__('Save', 'cluster-one-bismarck'), 'primary', 'submit', false); ?></p>
                </form>
<?php /* v0.9.29: Die Thema-Haelfte steht NACH dem Raum-Formular — ein
   Formular im Formular bricht das aeussere (die Falle aus 0.6.5). Der
   Waechter hat es beim ersten Anlauf sofort gemeldet, an der gerenderten
   Szene, bevor das ZIP freigegeben war. */ ?>
<?php cluster_one_paar_seite_thema($room, $id); ?>

            <?php }

function cluster_one_room_seite_config_umbenennen($room, $id): void { ?>                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:780px;margin-bottom:1.6em;">
                    <?php wp_nonce_field('cluster_one_room_rename'); ?>
                    <input type="hidden" name="action" value="cluster_one_room_rename">
                    <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                    <h3 style="margin-top:.5em;"><?php esc_html_e('Room name', 'cluster-one-bismarck'); ?></h3>
                    <input type="text" name="room_name" class="regular-text" value="<?php echo esc_attr((string) ($room['name'] ?? '')); ?>">
                    <?php submit_button(__('Rename', 'cluster-one-bismarck'), 'secondary', 'submit', false); ?>
                    <p class="description" style="max-width:620px;"><?php esc_html_e('Only the label changes — the room keeps its identity, so chat and configuration stay intact.', 'cluster-one-bismarck'); ?></p>
                </form>

<?php
}


function cluster_one_room_seite_config_kis($room): void { ?>                    <h3 style="margin-top:.5em;"><?php esc_html_e('AIs in this room', 'cluster-one-bismarck'); ?></h3>
                    <p style="opacity:.8;max-width:680px;"><?php esc_html_e('Select which AIs are in this room. The green dot lights as soon as at least one is in. Only named AIs can be ticked — give a provider its identity under Bismarck → Settings first.', 'cluster-one-bismarck'); ?></p>
                    <?php
                    $ai_conns = cluster_one_get_ai_connectors();
                    $sel      = (isset($room['ais']) && is_array($room['ais'])) ? array_map('strval', $room['ais']) : array();
                    $ids      = cluster_one_get_ai_identities();
                    ?>
<?php if (empty($ai_conns)) : ?>
                        <p style="opacity:.7;"><?php esc_html_e('No AI connectors found. Install a provider plugin under Settings → Connectors.', 'cluster-one-bismarck'); ?></p>
<?php else : ?>
                        <table class="widefat striped" style="max-width:760px;">
                            <thead><tr>
                                <th><?php esc_html_e('AI name', 'cluster-one-bismarck'); ?></th>
                                <th><?php esc_html_e('Provider', 'cluster-one-bismarck'); ?></th>
                                <th><?php esc_html_e('Status', 'cluster-one-bismarck'); ?></th>
                                <th style="width:2.5em;text-align:center;">✓</th>
                            </tr></thead>
                            <tbody>
                            <?php foreach ($ai_conns as $cid => $conn) :
                                $cname   = is_array($conn) ? ((string) ($conn['name'] ?? $cid)) : (string) $cid;
                                $state   = cluster_one_connector_has_key((string) $cid, (array) $conn);
                                $checked = in_array((string) $cid, $sel, true);
                                $named   = isset($ids[$cid]) && (int) $ids[$cid] > 0;
                                $ainame  = $named ? cluster_one_room_ai_label((string) $cid, $room) : '';
                            ?>
                                <tr>
                                    <td><?php if ($named) : ?><strong><?php echo esc_html($ainame); ?></strong><?php else : ?><span style="color:#b32d2e;opacity:.8;"><?php esc_html_e('no identity', 'cluster-one-bismarck'); ?></span><?php endif; ?></td>
                                    <td><code style="opacity:.6;"><?php echo esc_html((string) $cid); ?></code></td>
                                    <td><?php echo cluster_one_ai_status_dot((string) $cid, $state); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_ai_status_dot() gibt fertiges HTML zurueck, der Text darin ist esc_html ?></td>
                                    <td style="text-align:center;">
<?php if ($named) : ?>
                                            <input type="checkbox" name="ais[]" value="<?php echo esc_attr($cid); ?>" <?php checked($checked); ?>>
<?php else : ?>
                                            <input type="checkbox" disabled title="<?php echo esc_attr__('Give this provider an identity under Bismarck → Settings first.', 'cluster-one-bismarck'); ?>">
<?php endif; ?>
                                    </td>
                                </tr>
<?php endforeach; ?>
                            </tbody>
                        </table>
<?php endif; ?>

                    <?php $__c1_ladder_c = $room; // v0.0.79 ?>
                    <h3 style="margin-top:2em;"><?php esc_html_e('Ladder', 'cluster-one-bismarck'); ?></h3>
                    <p class="description" style="max-width:680px;"><?php esc_html_e('The name list is the guest list — the ladder is the house rules. Outside view: what non-members see. Circle depth: how deep members may go; triggering AIs requires “full”. The SysOp stands beside the ladder.', 'cluster-one-bismarck'); ?></p>
                    <p><label><?php esc_html_e('Outside the circle:', 'cluster-one-bismarck'); ?>
                            <?php /* v0.9.24: Die Aussenansicht ist keine Wahl mehr — siehe
                               cluster_one_container_outside_view(). Sie ist ueberall
                               "Silhouette"; verborgen wird nur, was im SysOp-Bereich
                               liegt, und das regelt der Bereich. */ ?>
                            <span style="opacity:.75;"><?php esc_html_e('Silhouette — everyone sees that it exists, nobody gets in uninvited.', 'cluster-one-bismarck'); ?></span></label></p>
                        <?php /* v0.9.26: Die Kreis-Tiefe ist entfallen. Ihr Etikett log
                           seit 0.9.13 ("voll — schreiben und KIs ausloesen"), und der
                           einzige echte Fall fuer "nur lesend" ist ein Mensch, der
                           gerade stoert — ein Eingriff in einem Moment, kein Zustand.
                           Das Stummschalten kommt in den Chat.

                           v0.9.31: Hinter diesem Kommentar stand noch die Zeile
                           "/label></p>" — der Rest des entfernten Feldes, OHNE
                           spitze Klammer, also blanker Text. Sie wurde bei jedem
                           Aufruf woertlich ins Backend geschrieben. Das <label>
                           oben schliesst jetzt an seiner eigenen Zeile. */ ?>

                    <h3 style="margin-top:2em;"><?php esc_html_e('Mode', 'cluster-one-bismarck'); ?></h3>
                    <?php // v0.0.75: Modi sind reine BESCHREIBUNGEN aus der Kranz-Tabelle
                          // (Entscheidung SysOp 16.07.) — keine Funktionskopplung mehr.
                          // Bestandsschutz: ein gespeicherter, inzwischen gelöschter Modus
                          // bleibt markiert wählbar (tz-Muster).
                          /* v0.9.51: $cm ist entfallen — die Zeile las das Feld
                             und benutzte es nie. Angezeigt wurde schon seit
                             0.9.2 die Ableitung. */
                          /* v0.9.2: Der Modus ist kein FELD mehr, sondern eine
                             Anzeige. Er erreicht die KI ohnehin nie (kein
                             System-Prompt, keine Praeambel) und war ein
                             Etikett fuer Menschen — eines, das falsch gesetzt
                             werden konnte. Jetzt ergibt er sich aus Kopplung,
                             Uebersetzer und Besetzung. */
                          $c1_derived = cluster_one_room_derived_mode($room); ?>
                    <p style="margin:.2em 0 .6em;"><strong><?php echo esc_html(cluster_one_mode_label($c1_derived)); ?></strong>
                    <span style="opacity:.75;"><?php esc_html_e('— follows from this room: what it is linked to, whether a translator is on, and who is in it. Not chosen.', 'cluster-one-bismarck'); ?></span></p>
                    <?php /* v0.9.6: Der Satz beschrieb die Lage vor 0.9.2 — damals
                       waehlte man den Modus aus einer Liste. Heute ergibt er sich. */ ?>
                    <p class="description" style="max-width:680px;"><?php esc_html_e('The mode is not chosen: it follows from what this room is linked to, whether a translator is on, and who is in it. The wording of each mode can still be edited under Modes.', 'cluster-one-bismarck'); ?></p>

                    <?php /* v0.9.26: Das Haekchen "Nur Menschen" ist entfallen — die
                       KI-Liste oben IST die Antwort. Hinzufuegen wird bezeugt
                       (room_ai_added), also braucht es kein zweites Schloss.
                       Mein Einwand vom Samstag war falsch. */ ?>

<?php /* v0.9.42: DAS FELD STEHT NUR NOCH DA, WO ES WIRKEN KANN.

   Christians Regel: "Uebersetzer ist nur etwas fuer Babel-Fish." In dieser
   Architektur ist der Satz allerdings ein Kreis — Babel Fish IST der Modus,
   den ein Raum bekommt, sobald ein Uebersetzer gesetzt ist. Die nicht-
   kreisfoermige Fassung derselben Regel steht in der Ableitungs-Reihenfolge
   (kern.php): Terminal und Wiki schlagen Babel Fish. Wo ein Tagebuch oder ein
   Wiki dranhaengt, ist der Raum also schon etwas anderes, und ein dort
   gesetzter Uebersetzer waere eine Einstellung ohne Wirkung.

   GENAU DAS WAR DER FALL. Wiki-01 auf Christians Knoten trug 'openai' und lief
   trotzdem als Wiki. Die Migration in bismarck.php raeumt solche Reste weg —
   sie MUSS es, denn ein Feld, das verschwindet, waehrend sein Wert bleibt,
   waere eine unsichtbare Einstellung. Schlimmer als beide Zustaende davor. */
$c1_tr_modus = cluster_one_room_derived_mode($room);
$c1_tr_zeigt = !in_array($c1_tr_modus, array('terminal', 'wiki'), true);
if (!$c1_tr_zeigt) : ?>
                    <h3 style="margin-top:2em;"><?php esc_html_e('Translator', 'cluster-one-bismarck'); ?></h3>
                    <p class="description" style="max-width:680px;"><?php esc_html_e('Not here: a diary or a wiki is linked to this room, and that outranks translation. Translation lives in Babel Fish — a room without either.', 'cluster-one-bismarck'); ?></p>
<?php else : ?>
                    <h3 style="margin-top:2em;"><?php esc_html_e('Translator', 'cluster-one-bismarck'); ?></h3>
                    <?php $ct = (string) ($room['translator'] ?? ''); ?>
                    <select name="translator" id="c1-translator">
                        <option value="">— <?php esc_html_e('none', 'cluster-one-bismarck'); ?> —</option>
                        <?php foreach ($ai_conns as $cid => $conn) :
                            $cname = is_array($conn) ? ((string) ($conn['name'] ?? $cid)) : (string) $cid; ?>
                            <option value="<?php echo esc_attr($cid); ?>" <?php selected($ct, (string) $cid); ?>><?php echo esc_html($cname); ?></option>
<?php endforeach; ?>
                    </select>
                    <p style="margin-top:.6em;"><label><?php esc_html_e('Common language (never translated):', 'cluster-one-bismarck'); ?>
                        <input type="text" name="common_lang" class="regular-text" list="c1_lang_suggestions" value="<?php echo esc_attr((string) ($room['common_lang'] ?? '')); ?>" placeholder="<?php echo esc_attr__('e.g. English', 'cluster-one-bismarck'); ?>"></label></p>
                    <p class="description" style="max-width:680px;"><?php esc_html_e('Active in two-person chats with the SysOp (named circle, exactly two humans, different profile languages): messages are translated once at send time; each person reads their own language, the other one folds open on click. Messages in the common language stay untouched. In group chats this field has no function.', 'cluster-one-bismarck'); ?></p>
<?php endif; ?>
                    <script>
                    (function () {
                        // v0.0.75: Vorschau hängt an den SCHALTERN, nicht mehr an
                        // Modus-Etiketten (Modi sind reine Beschreibungen).
                            var ho    = false; // v0.9.26: humans_only entfallen
                        var trSel = document.getElementById('c1-translator');
                        function sync() {
                            var ho    = false; // v0.9.26: humans_only entfallen
                            var trVal = trSel ? trSel.value : '';
                            document.querySelectorAll('input[name="ais[]"]').forEach(function (cb) {
                                var lockedByTranslator = false; // v0.0.84: schläft mit dem Server-Ausschluss — kehrt mit Anti-Babel light zurück
                                cb.disabled = ho || lockedByTranslator;
                                if (ho || lockedByTranslator) { cb.checked = false; }
                                var tr = cb.closest('tr');
                                if (tr) { tr.style.opacity = (ho || lockedByTranslator) ? '.45' : ''; }
                            });
                        }
                        if (trSel) { trSel.addEventListener('change', sync); }
                        sync();
                    })();
                    </script>

<?php
}


function cluster_one_room_seite_config_verbindungen($room): void {
    /* v0.9.26: DIE BRUECKE IST ENTFALLEN — Christians Argument, und es ist das
       eines Entwicklers: Man baut kein Feld in Version 1, damit es in Version
       10 eine Funktion bekommt.

       'bridge_type' und 'bridge_ref' setzten eine Bauform voraus, die es nicht
       gibt — die Matrix-Anbindung ist nicht entworfen. Dass die Felder sich
       selbst als "noch nicht verdrahtet" bezeichneten, machte sie ehrlich und
       trotzdem nutzlos. Kommt die Bruecke, wird sie gebaut, mit dem, was sie
       dann wirklich braucht. */
    ?>
<?php if ((string) ($room['linked_thread'] ?? '') !== '') : ?>
                        <h3 style="margin-top:2em;">Winsor</h3>
<?php if (!cluster_one_container_is_archived($room)) : ?>
                            <p class="description" style="max-width:680px;"><?php esc_html_e('Lays this chat AND its paired thread into the cooling pool: extinguished — no AI triggers, no rounds. Everyone who could read before still can, and may still object: an objection there reaches the SysOp. Reversible with a recorded act (Winsor tab).', 'cluster-one-bismarck'); ?></p>
                            <p><button type="submit" class="button" form="c1-winsor-room-form" onclick="return confirm('<?php echo esc_js(__('Lay this pair into the cooling pool?', 'cluster-one-bismarck')); ?>');"><?php esc_html_e('Lay into the cooling pool', 'cluster-one-bismarck'); ?></button></p>
<?php else : ?>
                            <p class="description"><?php esc_html_e('This container lies in the pool — lift it out in the Winsor tab.', 'cluster-one-bismarck'); ?></p>
<?php endif; ?>
<?php endif; ?>
                    <h3 style="margin-top:2em;"><?php esc_html_e('Linked forum thread', 'cluster-one-bismarck'); ?></h3>
                    <p class="description" style="max-width:680px;"><?php esc_html_e('Optional, 1:1, kept in sync both ways — the linked thread will show it belongs to this room, and picking a thread that already belongs to another room takes it away from that one.', 'cluster-one-bismarck'); ?></p>
                    <?php $all_threads_for_select = cluster_one_get_forum_threads(); ?>
                    <select name="linked_thread">
                        <option value="">— <?php esc_html_e('none', 'cluster-one-bismarck'); ?> —</option>
<?php foreach ($all_threads_for_select as $ltid => $lt) : ?>
                            <option value="<?php echo esc_attr((string) $ltid); ?>" <?php selected((string) ($room['linked_thread'] ?? ''), (string) $ltid); ?>><?php echo esc_html((string) ($lt['title'] ?? '')); ?></option>
<?php endforeach; ?>
                    </select>

                    <h3 style="margin-top:2em;"><?php esc_html_e('Area', 'cluster-one-bismarck'); ?></h3>
                    <p class="description" style="max-width:680px;"><?php esc_html_e('Areas are managed under Bismarck → Structure. “No area” is a valid state. A linked pair moves together: change the area on one side and the other follows — the move is recorded on both.', 'cluster-one-bismarck'); ?></p>
                    <?php cluster_one_render_area_select((string) ($room['area'] ?? ''), 'area'); // v0.0.66: Räume nutzen DIESELBE Struktur-Liste wie Foren (Weg a) ?>

                    <h3 style="margin-top:2em;"><?php esc_html_e('Visible to', 'cluster-one-bismarck'); ?></h3>
                    <p class="description" style="max-width:680px;"><?php esc_html_e('Changing visibility is a door event and is witnessed — no silent switch, in either direction. Nobody is added automatically here: what you save is exactly the circle.', 'cluster-one-bismarck'); ?></p>
                    <?php cluster_one_render_visibility_picker(
                        array_map('strval', (array) ($room['visible_to'] ?? array())),
                        (string) ($room['group_visibility'] ?? 'all')
                    ); // v0.0.66: Sichtbarkeit wohnt ab jetzt in der Konfiguration, nicht mehr nur im Anlage-Formular ?>

<?php
}


function cluster_one_room_seite_config_teilnehmer($room): void { ?>                    <h3 style="margin-top:2em;"><?php esc_html_e('Human participants', 'cluster-one-bismarck'); ?></h3>
                    <p style="opacity:.8;max-width:680px;"><?php esc_html_e('Tick who is in this room. The SysOp is always present and is not a selectable participant.', 'cluster-one-bismarck'); ?></p>
                    <?php
                    $all_humans   = cluster_one_get_all_humans();
                    $sel_partic   = (isset($room['participants']) && is_array($room['participants'])) ? array_map('strval', $room['participants']) : array();
                    ?>
<?php if (empty($all_humans)) : ?>
                        <p style="opacity:.7;"><?php esc_html_e('No humans found yet. Add non-WP people or check WP users under Bismarck → Settings.', 'cluster-one-bismarck'); ?></p>
<?php else : ?>
                        <table class="widefat striped" style="max-width:900px;">
                            <thead><tr>
                                <th><?php esc_html_e('Name', 'cluster-one-bismarck'); ?></th>
                                <th><?php esc_html_e('Type', 'cluster-one-bismarck'); ?></th>
                                <th>Discord</th>
                                <th>Matrix</th>
                                <th style="width:2.5em;text-align:center;">✓</th>
                            </tr></thead>
                            <tbody>
                            <?php foreach ($all_humans as $hid => $h) :
                                $hname   = (string) ($h['name'] ?? '');
                                $htype   = ((string) ($h['type'] ?? '')) === 'wp' ? __('WP user', 'cluster-one-bismarck') : __('non-WP', 'cluster-one-bismarck');
                                $hdisc   = (string) ($h['discord'] ?? '');
                                $hmat    = (string) ($h['matrix'] ?? '');
                                $checked = in_array((string) $hid, $sel_partic, true);
                            ?>
<?php if (!empty($h['is_main_admin'])) : ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($hname); ?></strong></td>
                                        <td>SysOp</td>
                                        <td style="opacity:.6;"><?php echo esc_html($hdisc); ?></td>
                                        <td style="opacity:.6;"><?php echo esc_html($hmat); ?></td>
                                        <td style="text-align:center;opacity:.6;font-size:.85em;"><?php esc_html_e('always present', 'cluster-one-bismarck'); ?></td>
                                    </tr>
<?php else : ?>
                                    <tr>
                                        <td><?php echo esc_html($hname); ?></td>
                                        <td style="opacity:.7;"><?php echo esc_html($htype); ?></td>
                                        <td style="opacity:.6;"><?php echo esc_html($hdisc); ?></td>
                                        <td style="opacity:.6;"><?php echo esc_html($hmat); ?></td>
                                        <td style="text-align:center;"><input type="checkbox" name="participants[]" value="<?php echo esc_attr((string) $hid); ?>" <?php checked($checked); ?>></td>
                                    </tr>
<?php endif; ?>
<?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php /* v0.9.31: Hier stand ein Skript, das die Nicht-WP-Zeilen dieser
                           Tabelle ausgegraut hat, je nachdem welche Bruecke oben gewaehlt
                           war. Es hat nie gelaufen: Es sucht #c1-bridge-type, und die
                           Bruecken-Auswahl ist seit 0.9.26 aus der Oberflaeche
                           verschwunden. Der Waechter in Zeile 2 (`if (!bridgeSel) return;`)
                           brach bei jedem Seitenaufruf sofort ab.
                           Die data-c1-*-Attribute an den Zeilen sind mitgegangen — sie
                           hatten keinen anderen Leser. */ ?>
<?php endif; ?>

<?php
}


/** Chat-Ansicht: Nachrichtenliste, Composer, Seitenleiste mit Farbspiel-Cockpit.
 * Stufe B (0.1.0): byte-genau, Golden-Master-bezeugt. */
function cluster_one_room_seite_chatbereich(array $room, string $id, bool $c1_room_peek): void { ?>

                <?php
                $msgs = cluster_one_get_room_messages($id);
                $fmt  = get_option('date_format') . ' ' . get_option('time_format');
                $me   = cluster_one_human_speaker_label(); // Sub-Projekt Identitätsfrage: der Mensch, nicht der Knoten
                /* v0.9.30: $room_ais fehlte hier. Die Variable wurde beim Teilen
                   der alten Funktion (0.9.11) nur in ..._kis() mitgenommen — die
                   Abfrage unten blieb zurueck und fragte nach etwas, das in
                   diesem Sichtbereich nicht existiert. empty() schweigt bei
                   undefinierten Variablen, also war die Bedingung IMMER wahr:
                   "You are alone here." stand in jedem Raum, auch neben fuenf
                   KIs. Keine Szene sah es, weil in allen fuenf Golden-Raeumen
                   'ais' leer ist — die Welt konnte den Fall gar nicht stellen.
                   Peek stellt ihn seit 0.9.30 (dritter Raum, r_kis). */
                $room_ais = (isset($room['ais']) && is_array($room['ais'])) ? $room['ais'] : array();
                ?>
                <?php cluster_one_render_ai_notice(); ?>
                <div style="display:flex;gap:1.5em;align-items:flex-start;max-width:980px;">
                    <div style="flex:1;min-width:0;">
<?php cluster_one_room_seite_chatbereich_nachrichten($room, $msgs, $fmt); ?>
<?php if (empty($room_ais)) : /* 18.10.2026 (Neo): vor den Composer gezogen, damit der Satz in der Chat-Spalte bleibt — der Composer schliesst sie jetzt selbst. */ ?>
                                <p style="opacity:.6;font-size:.9em;margin:.8em 0 0;"><?php echo esc_html(cluster_one_room_solo_human($room)
                                    ? __('No second human here — the AIs are.', 'cluster-one-bismarck')
                                    : __('You are alone here.', 'cluster-one-bismarck')); ?></p>
<?php endif; ?>
<?php cluster_one_room_seite_chatbereich_composer($room, $id, $c1_room_peek, $me); ?>
<?php if ($c1_room_peek) : /* 18.10.2026 (Neo): DIE DIV-BILANZ. Der Composer schliesst
   die Chat-Spalte (flex:1) in beiden Leiter-Zweigen selbst; nur im Durchgriff
   zeichnet er bloss die Notiz und laesst sie offen — also wird sie HIER
   geschlossen. Vorher stand das Gegenteil (!peek) und darunter zwei weitere
   </div>: im Normalfall zwei Schliesser zu viel, die WordPress' eigene
   Wrapper vorzeitig zumachten; im Lese-Zweig rutschten die Seitenleisten in
   die Chat-Spalte. Gezaehlt: 2 geoeffnet (Zeile 821/822), jetzt 2 geschlossen,
   in jedem Zweig. */ ?>
                        </div>
<?php endif; ?>
                </div>

            <?php }

function cluster_one_room_seite_chatbereich_nachrichten($room, $msgs, $fmt): void { ?>                        <div id="c1-chatbox" style="border:1px solid #dcdcde;border-radius:8px;padding:12px;background:#fff;min-height:160px;max-height:52vh;overflow-y:auto;">
<?php if (empty($msgs)) : ?>
                                <p style="opacity:.6;margin:.3em 0;"><?php esc_html_e('No messages yet. Leave some context for the room.', 'cluster-one-bismarck'); ?></p>
<?php else : ?>
                                <?php $c1_bb_view = cluster_one_room_babel_state($room); if ($c1_bb_view['active']) : ?>
                                    <p style="font-size:.8em;opacity:.6;margin:.2em 0 .6em;">🐟 <?php esc_html_e('Babel Fish active — everyone reads their own language; the other folds open on click.', 'cluster-one-bismarck'); ?></p>
<?php endif; ?>
                                <?php foreach ($msgs as $m) :
                                    $author   = esc_html((string) ($m['author'] ?? ''));
                                    $time     = esc_html(wp_date($fmt, (int) ($m['ts'] ?? 0)));
                                    $bodyhtml = nl2br(esc_html((string) ($m['body'] ?? '')));
                                    // v0.0.97: Babel-Anzeige — der Betrachter sieht seine
                                    // Sprache, die fremde ist IMMER eingeklappt (Klick öffnet).
                                    $c1_m_tr   = trim((string) ($m['translation'] ?? ''));
                                    $c1_m_mine = $c1_m_tr !== '' && (int) ($m['translation_to'] ?? 0) === get_current_user_id();
                                ?>
                                    <div style="margin:.55em 0;">
                                        <div style="font-size:.8em;opacity:.6;"><?php echo $author . ' · ' . $time; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $author und $time sind neun Zeilen hoeher in der Schleife mit esc_html() gebaut ?><?php if (!empty($m['translation_failed'])) : ?> <em style="opacity:.8;">· <?php esc_html_e('translation failed — original only', 'cluster-one-bismarck'); ?></em><?php endif; ?></div>
<?php if ($c1_m_tr === '') : ?>
                                            <div><?php echo $bodyhtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $bodyhtml = nl2br(esc_html(...)) oben in der Schleife ?></div>
<?php elseif ($c1_m_mine) : ?>
                                            <div><?php echo nl2br(esc_html($c1_m_tr)); ?></div>
                                            <details style="margin-top:.25em;"><summary style="cursor:pointer;font-size:.8em;opacity:.6;"><?php esc_html_e('Original', 'cluster-one-bismarck'); ?></summary><div style="opacity:.85;"><?php echo $bodyhtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $bodyhtml = nl2br(esc_html(...)) oben in der Schleife ?></div></details>
<?php else : ?>
                                            <div><?php echo $bodyhtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $bodyhtml = nl2br(esc_html(...)) oben in der Schleife ?></div>
                                            <details style="margin-top:.25em;"><summary style="cursor:pointer;font-size:.8em;opacity:.6;"><?php esc_html_e('Translation', 'cluster-one-bismarck'); ?></summary><div style="opacity:.85;"><?php echo nl2br(esc_html($c1_m_tr)); ?></div></details>
<?php endif; ?>
                                    </div>
<?php endforeach; ?>
<?php endif; ?>
                        </div>
<?php
}


function cluster_one_room_seite_chatbereich_composer($room, $id, $c1_room_peek, $me): void { ?>                        <?php if ($c1_room_peek) : // v0.0.80: bezeugt lesend — kein Schreiben, kein Trigger ?>
                            <div class="notice notice-info" style="margin-top:1em;"><p><?php esc_html_e('Reading only — witnessed as “SysOp had access”. To speak here, add yourself to the circle in the configuration (witnessed as well).', 'cluster-one-bismarck'); ?></p></div>
<?php else : ?>
<?php /* v0.9.12: DIE LEITER STAND NUR IM HANDLER — Christians Befund als MenschA.

   Er konnte schreiben und senden, und nichts kam an: Der Handler prueft
   cluster_one_container_can_act() und leitet bei fehlender Stufe STILL um.
   Das Formular fragte nur nach "archiviert".

   Dieselbe Klasse wie zweimal zuvor katalogisiert: SPERRE IM HANDLER OHNE
   SPERRE IN DER OBERFLAECHE. Ein sichtbares Eingabefeld, das in eine stumme
   Umleitung laeuft, sieht aus wie "ich darf". */ ?>
<?php if (!cluster_one_container_can_act($room)) : ?>
                        <p style="opacity:.75;max-width:620px;margin:.6em 0;"><?php esc_html_e('You can read along here, but not write — the ladder for this room is set to reading. The SysOp can change that.', 'cluster-one-bismarck'); ?></p>
<?php else : ?>
<?php if (cluster_one_container_is_archived($room)) : // v0.1.1: 'erloschen' ist ein Hinweis, keine Sperre — Wächterzeilen OHNE Einrückung (?>-Whitespace-Falle) ?>
                            <div class="notice notice-info" style="margin-top:1em;"><p><?php esc_html_e('This project is in the cooling pool. You can still write here — you may change the SysOp’s mind and see the reactor released again. But perhaps it is time to start something new.', 'cluster-one-bismarck'); ?></p></div>
<?php endif; ?>
                        <form id="c1-send" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:1em;">
                            <?php wp_nonce_field('cluster_one_room_send'); ?>
                            <input type="hidden" name="action" value="cluster_one_room_send">
                            <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                            <div style="display:flex;gap:.5em;align-items:flex-end;">
                                <textarea id="c1-msg" name="body" class="large-text" rows="3" placeholder="<?php echo esc_attr__('Write a message…', 'cluster-one-bismarck'); ?>" style="flex:1;min-width:0;"></textarea>
                                <?php submit_button(__('Send', 'cluster-one-bismarck'), 'primary', 'submit', false); ?>
                            </div>
                            <p class="description" style="margin:.4em 0 0;"><?php esc_html_e('Return sends · Shift+Return for a new line', 'cluster-one-bismarck'); ?></p>
                        </form>
<?php if ((string) ($room['linked_thread'] ?? '') !== '' && !cluster_one_container_is_archived($room)) : ?>
                    <form id="c1-winsor-room-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                        <?php wp_nonce_field('cluster_one_winsor_toggle'); ?>
                        <input type="hidden" name="action" value="cluster_one_winsor_toggle">
                        <input type="hidden" name="ctype" value="room">
                        <input type="hidden" name="cid" value="<?php echo esc_attr($id); ?>">
                    </form>
<?php endif; ?>
                        <script>
                        (function () {
                            var box = document.getElementById('c1-chatbox');
                            if (box) { box.scrollTop = box.scrollHeight; }
                            var ta = document.getElementById('c1-msg');
                            if (ta) {
                                ta.addEventListener('keydown', function (e) {
                                    if (e.key === 'Enter' && !e.shiftKey) {
                                        e.preventDefault();
                                        var f = ta.closest('form');
                                        if (f) { (f.requestSubmit ? f.requestSubmit() : f.submit()); }
                                    }
                                });
                            }
                        })();
                        </script>
<?php endif; // v0.9.12: Ende der Leiter-Pruefung — wer nur lesen darf, sieht kein Eingabefeld ?>
                    </div><?php /* 18.10.2026 (Neo): schliesst die Chat-Spalte (flex:1) — jetzt fuer
   BEIDE Leiter-Zweige. Vorher stand dieser Schliesser nur im Schreib-Zweig;
   wer nur lesen durfte, bekam die Seitenleisten IN die Chat-Spalte gezeichnet.
   Die Leiste braucht weiter das Schreibrecht, die KI-Knoepfe stehen immer. */ ?>
<?php /* v0.8.7: Im Guru-Meditation-Modus keine Leiste — Runden und
   Reaktorfahrer ordnen dort nichts (siehe cluster_one_room_solo_human). */ ?>
<?php /* v0.9.11: Zwei Aufrufe statt eines. Die Ordnungs-Werkzeuge (Runden,
   Reaktorfahrer) bleiben abschaltbar, wo sie nichts ordnen — die KI-Knoepfe
   stehen IMMER. Sie standen bis hierher in derselben Funktion und
   verschwanden mit ihr. */ ?>
<?php if (!cluster_one_room_solo_human($room) && cluster_one_container_can_act($room)) { cluster_one_room_seite_chatbereich_leiste($room, $id, $me); } ?>
<?php cluster_one_room_seite_chatbereich_kis($room, $id, $me); ?>                            <?php endif; // v0.0.80: Ende Blick-Modus-Ausblendung ?>
<?php
}


function cluster_one_room_seite_chatbereich_leiste($room, $id, $me): void { ?>                    <div style="width:200px;flex:none;">
                        <div style="border:1px solid #dcdcde;border-radius:8px;padding:12px;background:#fff;">
                            <strong style="display:block;margin-bottom:.6em;"><?php esc_html_e('In the room', 'cluster-one-bismarck'); ?></strong>
                            <?php
                            // v0.0.82: Farbspiel-Cockpit — Rundenstand + Regler
                            $c1_fs   = cluster_one_fs_state($room);
                            $c1_me_k = 'wp_' . get_current_user_id();
                            ?>
                            <div style="margin:-.2em 0 .7em;padding-bottom:.6em;border-bottom:1px solid #eee;">
<?php // v0.1.6: Reaktorfahrer auf Zeit. Waechterzeilen flush-left.
$c1_pilot = cluster_one_fs_pilot($room);
if ($c1_pilot !== '') : ?>
                                <p style="margin:.2em 0 .5em;font-size:.85em;"><span style="color:#1a7f37;font-weight:600;">▲</span>
                                    <?php echo esc_html(sprintf(
                                        /* translators: %1$s: Name des Reaktor-Fahrers, %2$d: verbleibende Minuten. */
                                        __('%1$s is driving — %2$d minutes left. The SysOp is watching.', 'cluster-one-bismarck'),
                                        cluster_one_fs_display_name(str_replace('wp:', 'wp_', $c1_pilot)),
                                        (int) ceil(cluster_one_fs_pilot_left($room) / 60)
                                    )); ?></p>
<?php endif; ?>
<?php if (apply_filters('cluster_one_umgebung', false, 'aktiv', $room)) : // v0.2.1: Offenlegung ist sichtbar, nicht still; v1.3.2: ueber den Vertrag ?>
                                <p style="margin:.2em 0 .5em;font-size:.85em;"><span style="color:#b26b00;font-weight:600;">◆</span>
                                    <?php echo esc_html(sprintf(
                                        /* translators: %d: verbleibende Minuten. */
                                        __('The AIs here are being given this node’s technical ground conditions — %d minutes left.', 'cluster-one-bismarck'),
                                        (int) ceil(((int) $room['umgebung_bis'] - time()) / 60)
                                    )); ?></p>
<?php endif; ?>
<?php if (cluster_one_is_sysop()) : ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:.2em 0 .6em;font-size:.85em;">
                                    <?php wp_nonce_field('cluster_one_umgebung_grant'); ?>
                                    <input type="hidden" name="action" value="cluster_one_umgebung_grant">
                                    <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
<?php if (apply_filters('cluster_one_umgebung', false, 'aktiv', $room)) : ?>
                                    <input type="hidden" name="minutes" value="0">
                                    <button type="submit" class="button button-small"><?php esc_html_e('Stop sharing ground conditions', 'cluster-one-bismarck'); ?></button>
<?php else : ?>
                                    <label><?php esc_html_e('Hand the AIs the ground conditions for', 'cluster-one-bismarck'); ?>
                                        <input name="minutes" type="number" min="1" max="120" value="30" style="width:4.5em;"> <?php esc_html_e('min', 'cluster-one-bismarck'); ?></label>
                                    <button type="submit" class="button button-small"><?php esc_html_e('Share', 'cluster-one-bismarck'); ?></button>
                                    <span class="description"><?php esc_html_e('Versions, limits and the plugin list — never public, never in why.md. Runs out by itself.', 'cluster-one-bismarck'); ?></span>
<?php endif; ?>
                                </form>
<?php endif; ?>
<?php if (cluster_one_is_sysop()) : ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:.2em 0 .6em;font-size:.85em;">
                                    <?php wp_nonce_field('cluster_one_fs_pilot'); ?>
                                    <input type="hidden" name="action" value="cluster_one_fs_pilot">
                                    <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
<?php if ($c1_pilot !== '') : ?>
                                    <input type="hidden" name="actor" value="">
                                    <input type="hidden" name="minutes" value="0">
                                    <button type="submit" class="button button-small"><?php esc_html_e('Take the reactor back', 'cluster-one-bismarck'); ?></button>
<?php else : ?>
                                    <label><?php esc_html_e('Hand the reactor to:', 'cluster-one-bismarck'); ?>
                                        <select name="actor">
<?php /* v0.3.21: Hier stand "as $c1_pk => $c1_pv" — cluster_one_fs_participants()
   liefert aber eine LISTE, keine Abbildung. Die Schluessel waren also 0,1,2,
   und strpos('0','wp_') ist nie 0: Die Schleife uebersprang JEDEN Eintrag,
   immer. Der Auswaehler konnte nie einen Namen enthalten — die Uebergabe des
   Reaktors war seit 0.1.6 unbenutzbar. */ ?>
<?php foreach (cluster_one_fs_participants($room) as $c1_pk) : if (strpos((string) $c1_pk, 'wp_') !== 0 || (string) $c1_pk === $c1_me_k) { continue; } ?>
                                            <option value="<?php echo esc_attr(str_replace('wp_', 'wp:', (string) $c1_pk)); ?>"><?php echo esc_html(cluster_one_fs_display_name((string) $c1_pk)); ?></option>
<?php endforeach; ?>
                                        </select></label>
                                    <label><?php esc_html_e('for', 'cluster-one-bismarck'); ?>
                                        <input name="minutes" type="number" min="1" max="180" value="15" style="width:4.5em;"> <?php esc_html_e('min', 'cluster-one-bismarck'); ?></label>
                                    <button type="submit" class="button button-small"><?php esc_html_e('Hand over', 'cluster-one-bismarck'); ?></button>
                                    <span class="description"><?php esc_html_e('Runs out by itself — a forgotten handover cannot become permanent.', 'cluster-one-bismarck'); ?></span>
<?php endif; ?>
                                </form>
<?php endif; ?>
<?php if ($c1_fs['active']) : ?>
                                    <?php /* translators: %d: Nummer der Runde. */ ?>
                                    <span style="font-size:.85em;opacity:.75;"><?php echo esc_html(sprintf(__('Round %d — open', 'cluster-one-bismarck'), $c1_fs['n'])); ?></span>
<?php elseif ($c1_fs['n'] > 0) : ?>
                                    <?php /* translators: %d: Nummer der Runde. */ ?>
                                    <span style="font-size:.85em;opacity:.55;"><?php echo esc_html(sprintf(__('Round %d — closed', 'cluster-one-bismarck'), $c1_fs['n'])); ?></span>
<?php endif; ?>
<?php if (cluster_one_fs_may_cut($room, cluster_one_actor_id_human())) : // v0.1.6: SysOp ODER wer gerade fährt ?>
                                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:.4em;">
                                        <?php wp_nonce_field('cluster_one_fs_round'); ?>
                                        <input type="hidden" name="action" value="cluster_one_fs_round">
                                        <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                                        <input type="hidden" name="op" value="<?php echo $c1_fs['active'] ? 'close' : 'open'; ?>">
                                        <button type="submit" class="button button-small"><?php echo $c1_fs['active'] ? esc_html__('Close round', 'cluster-one-bismarck') : esc_html__('Open round', 'cluster-one-bismarck'); ?></button>
                                    </form>
<?php if (!$c1_fs['active'] && $c1_fs['n'] > 0) : // v0.0.84: Amnestie ?>
                                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:.3em;">
                                        <?php wp_nonce_field('cluster_one_fs_round'); ?>
                                        <input type="hidden" name="action" value="cluster_one_fs_round">
                                        <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                                        <input type="hidden" name="op" value="reset">
                                        <button type="submit" class="button button-small" title="<?php echo esc_attr__('Forgive open debts — all lamps green. Only between rounds.', 'cluster-one-bismarck'); ?>"><?php esc_html_e('Reset — all lamps green', 'cluster-one-bismarck'); ?></button>
                                    </form>
<?php endif; ?>
<?php elseif ($c1_fs['active'] && cluster_one_fs_light($c1_fs, $c1_me_k) !== 'green') : ?>
                                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:.4em;">
                                        <?php wp_nonce_field('cluster_one_fs_pass'); ?>
                                        <input type="hidden" name="action" value="cluster_one_fs_pass">
                                        <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                                        <button type="submit" class="button button-small" title="<?php echo esc_attr__('Counts as having the floor — with an honest status line.', 'cluster-one-bismarck'); ?>"><?php esc_html_e('Pass this round', 'cluster-one-bismarck'); ?></button>
                                    </form>
<?php endif; ?>
                            </div>
                            <?php
                            // Sub-Projekt Identitätsfrage, Härtung: bisher stand hier NUR "(du)" + KIs,
                            // kein anderer Mensch war je sichtbar — Überbleibsel aus der Zeit vor
                            // mehreren Menschen im Raum. Jetzt: (du) zuerst, dann SysOp (fest,
                            // außer du bist es selbst), dann die übrigen für den Raum angehakten
                            // Menschen. Kein Grün/Grau-Punkt bei Menschen (bewusst zurückgestellt,
                            // siehe Reißbrett-Notiz zu Präsenz — eine Attrappe würde später
                            // verwirren, wenn ein echtes Signal dazukommt).
                            $current_uid   = get_current_user_id();
                            $main_admin_id = cluster_one_main_admin_user_id();
                            $all_humans    = cluster_one_get_all_humans();
                            $room_partic   = (isset($room['participants']) && is_array($room['participants'])) ? array_map('strval', $room['participants']) : array();
                            $shown_wp_ids  = array($current_uid); // gegen doppelte Selbst-Nennung
                            ?>
                            <div style="margin:.3em 0;display:flex;align-items:center;gap:.35em;">
                                <?php echo cluster_one_fs_dot(cluster_one_fs_light($c1_fs, $c1_me_k)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_fs_dot() gibt fertiges HTML zurueck (esc_attr(Titel), Farbe aus fester Tabelle) oder '' ?>
                                <span><?php echo esc_html($me); ?> <span style="opacity:.6;"><?php esc_html_e('(you)', 'cluster-one-bismarck'); ?></span></span>
                            </div>
                            <?php if ($main_admin_id > 0 && $main_admin_id !== $current_uid) :
                                $ma = get_userdata($main_admin_id);
                                if ($ma) :
                                    $shown_wp_ids[] = $main_admin_id;
                            ?>
                                <div style="margin:.3em 0;display:flex;align-items:center;gap:.35em;">
                                    <?php echo cluster_one_fs_dot(cluster_one_fs_light($c1_fs, 'wp_' . $main_admin_id)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_fs_dot() gibt fertiges HTML zurueck (esc_attr(Titel), Farbe aus fester Tabelle) oder '' ?>
                                    <span><?php echo esc_html($ma->display_name); ?> <span style="opacity:.5;font-size:.85em;">· SysOp</span></span>
                                    <?php do_action('cluster_one_netz_ping_knopf', $id, $main_admin_id); // v1.3.3: ueber den Vertrag ?>
                                </div>
<?php endif; endif; ?>
                            <?php foreach ($room_partic as $pid) :
                                if (!isset($all_humans[$pid])) { continue; } // unbekannte/entfernte ID
                                $h = $all_humans[$pid];
                                if ((($h['type'] ?? '') === 'wp') && in_array((int) ($h['user_id'] ?? 0), $shown_wp_ids, true)) {
                                    continue; // schon als (du) oder SysOp gezeigt
                                }
                            ?>
                                <div style="margin:.3em 0;display:flex;align-items:center;gap:.35em;">
<?php if (($h['type'] ?? '') === 'wp') { echo cluster_one_fs_dot(cluster_one_fs_light($c1_fs, (string) $pid)); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_fs_dot() gibt fertiges HTML zurueck (esc_attr(Titel), Farbe aus fester Tabelle) oder '' ?>
                                    <span><?php echo esc_html((string) $h['name']); ?></span>
<?php if (($h['type'] ?? '') === 'wp') : do_action('cluster_one_netz_ping_knopf', $id, (int) ($h['user_id'] ?? 0)); endif; ?>
<?php /* v0.9.27: Das Stummschalten sitzt neben der Person, nicht in der
   Konfiguration — Christians Punkt: Es ist ein Eingriff in einem Moment.
   Befristet, damit ein vergessenes Stummschalten kein Dauerzustand wird, und
   bezeugt wie jeder Eingriff des SysOps. */ ?>
<?php if (cluster_one_is_sysop() && ($h['type'] ?? '') === 'wp' && (int) ($h['user_id'] ?? 0) !== cluster_one_main_admin_user_id()) :
    $c1_ma = 'wp:' . (int) ($h['user_id'] ?? 0);
    $c1_mstumm = cluster_one_is_muted($room, $c1_ma); ?>
                                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;margin:0;">
                                        <?php wp_nonce_field('cluster_one_mute_toggle'); ?>
                                        <input type="hidden" name="action" value="cluster_one_mute_toggle">
                                        <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                                        <input type="hidden" name="actor" value="<?php echo esc_attr($c1_ma); ?>">
                                        <input type="hidden" name="minutes" value="15">
                                        <button type="submit" class="button-link" style="padding:0 .2em;cursor:pointer;color:<?php echo $c1_mstumm ? '#d63638' : '#8a8a8a'; ?>;"
                                            title="<?php echo esc_attr($c1_mstumm
                                                /* translators: %d: verbleibende Minuten der Stummschaltung. */
                                                ? sprintf(__('Muted — %d minutes left. Click to lift.', 'cluster-one-bismarck'), (int) ceil((cluster_one_muted_until($room, $c1_ma) - time()) / 60))
                                                : __('Mute here for 15 minutes — reading stays possible', 'cluster-one-bismarck')); ?>">
                                            <span class="dashicons <?php echo $c1_mstumm ? 'dashicons-controls-volumeoff' : 'dashicons-controls-volumeon'; ?>" aria-hidden="true" style="font-size:16px;width:16px;height:16px;"></span>
                                        </button>
                                    </form>
<?php endif; ?>
                                </div>
<?php endforeach; ?>
                        </div>
                    </div>
<?php }

/* v0.9.11: DIE KI-KNOEPFE STEHEN JETZT FUER SICH — mein Fehler aus 0.8.7.

   Damals habe ich die Leiste fuer Raeume ohne zweiten Menschen abgeschaltet,
   weil Runden und Reaktorfahrer dort nichts ordnen. Die KI-AUSLOESER standen
   aber in derselben Funktion und verschwanden mit — seit 0.9.1 in jedem
   Tagebuch-Chat, also genau dort, wo die KI das Einzige ist, was zaehlt.
   Christian konnte fragen, aber keine Antwort mehr ausloesen.

   WER EINEN BLOCK VERSTECKT, MUSS WISSEN, WAS ALLES DARIN STEHT. Ein
   Funktionsname ("Leiste") sagt nichts darueber, was sie traegt.

   Jetzt zwei Funktionen: Die Ordnungs-Werkzeuge bleiben abschaltbar, die
   KI-Knoepfe stehen immer. */
function cluster_one_room_seite_chatbereich_kis($room, $id, $me): void {
    /* v0.9.11: Was die alte Funktion oben gesetzt hatte und hier gebraucht
       wird. Beim Teilen faellt so etwas auf — der Prueflauf hat es sofort
       gemeldet, weil er die Szene wirklich rendert. */
    $c1_fs = cluster_one_fs_state($room);
    ?>
                    <div style="width:320px;flex:0 0 320px;">
                        <div style="border:1px solid #dcdcde;border-radius:8px;padding:12px;background:#fff;">
                            <?php
                            $room_ais = (isset($room['ais']) && is_array($room['ais'])) ? $room['ais'] : array();
                            $room_ids = cluster_one_get_ai_identities();
                            foreach ($room_ais as $cid) :
                                $cid    = (string) $cid;
                                $named  = isset($room_ids[$cid]) && (int) $room_ids[$cid] > 0;
                                $is_ver = cluster_one_ai_is_verified($cid);
                            ?>
                                <div style="display:flex;align-items:center;gap:.4em;margin:.35em 0;flex-wrap:nowrap;">
                                    <?php echo cluster_one_fs_dot(cluster_one_fs_light($c1_fs, $cid)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_fs_dot() gibt fertiges HTML zurueck (esc_attr(Titel), Farbe aus fester Tabelle) oder '' (bisheriger Zeilenkommentar: v0.0.82: Farbspiel-Ampel) ?>
                                    <span style="flex:none;color:<?php echo $is_ver ? '#1a7f37' : '#8a8a8a'; ?>;" aria-hidden="true"><?php echo $is_ver ? '●' : '○'; ?></span>
                                    <?php /* v1.3.5: Die Zeile im Raum nennt die Instanz, die antworten WUERDE,
       wenn der Leser gleich drueckt — nicht die knotenweite Kopplung. Derselbe
       Schreiber wie der Handler (cluster_one_ai_zahler), damit die Zeile und
       der gespeicherte Beitrag denselben Namen tragen. */ ?>
                                    <span style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo esc_html(cluster_one_ai_instanz_label(cluster_one_ai_zahler((string) $cid), (string) $cid)); ?> <span style="opacity:.5;font-size:.85em;">· <?php esc_html_e('AI', 'cluster-one-bismarck'); ?></span></span>
<?php if ($named) : ?>
                                        <?php /* v0.9.13: DERSELBE PLATZ, ZWEI WIRKUNGEN — aber nie dasselbe Etikett.

   Fuer den SysOp: die KI antworten lassen. Fuer einen Beschirmten: einen
   Wunsch hinterlassen, der als Systemnachricht im Raum steht. Der Knopf muss
   anders heissen, sonst waere es ein luegendes Etikett — er druecke
   "antworten lassen", und es antwortet niemand. */ ?>
<?php /* v1.3.5: ... und seit Pacioli auch: er druecke "antworten lassen",
   und es gibt fuer DIESEN Anbieter keine Instanz, die zahlen koennte. Dann
   antwortet ebenfalls niemand. Dieselbe Frage stellt der Wunsch-Handler
   (bismarck.php) — die beiden muessen sich einig sein, sonst zeichnet die eine
   Stelle ein Megafon und die andere schickt es weg.

   BENANNTE GRENZE, gemeldet und nicht gebaut: Ein SysOp OHNE Instanz fuer
   diesen Anbieter sieht ab hier das Megafon, und der Wunsch-Satz sagt ihm
   "Rufen kann sie nur der SysOp — er zahlt". Das ist ungenau, und es ist
   dieselbe Ungenauigkeit, die der Auftrag schon fuer den zweiten Menschen mit
   eigenem Schluessel nennt. Der Satz ist eine Entscheidung fuer Christian;
   ich melde ihn, ich baue ihn nicht. */ ?>
<?php if (!cluster_one_container_can_invoke_ai($room, (string) $cid)) : ?>
                                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;margin:0;">
                                            <?php wp_nonce_field('cluster_one_ai_wish'); ?>
                                            <input type="hidden" name="action" value="cluster_one_ai_wish">
                                            <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                                            <input type="hidden" name="cid" value="<?php echo esc_attr((string) $cid); ?>">
                                            <button type="submit" class="button-link" style="padding:0;cursor:pointer;color:#2271b1;" <?php disabled(cluster_one_ai_wish_has($room, (string) $cid, 'wp_' . get_current_user_id())); ?>
                                                title="<?php echo esc_attr(cluster_one_ai_wish_has($room, (string) $cid, 'wp_' . get_current_user_id())
                                                ? __('You already suggested this — waiting for the SysOp.', 'cluster-one-bismarck')
                                                : (cluster_one_ai_wish_open($room, (string) $cid)
                                                    ? __('Others have suggested this — you can join them.', 'cluster-one-bismarck')
                                                    : __('Suggest this AI — the SysOp decides.', 'cluster-one-bismarck'))); ?>">
                                                <span class="dashicons dashicons-megaphone" aria-hidden="true" style="font-size:18px;width:18px;height:18px;"></span>
                                            </button>
                                        </form>
<?php else : ?>
                                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;flex:none;line-height:1;">
                                            <?php wp_nonce_field('cluster_one_room_ai_reply'); ?>
                                            <input type="hidden" name="action" value="cluster_one_room_ai_reply">
                                            <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                                            <input type="hidden" name="cid" value="<?php echo esc_attr($cid); ?>">
                                            <button type="submit" class="button-link" title="<?php echo esc_attr__('Let it reply', 'cluster-one-bismarck'); ?>" aria-label="<?php echo esc_attr__('Let it reply', 'cluster-one-bismarck'); ?>" style="padding:0;cursor:pointer;">
                                                <span class="dashicons dashicons-controls-play" style="font-size:18px;width:18px;height:18px;"></span>
                                            </button>
                                        </form>
<?php endif; ?>
<?php endif; ?>
<?php if (cluster_one_is_sysop() && (string) ($room['linked_thread'] ?? '') !== '') : // v0.0.88: Kondensat-Weg ?>
                                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;flex:none;line-height:1;">
                                            <?php wp_nonce_field('cluster_one_room_ai_reply'); ?>
                                            <input type="hidden" name="action" value="cluster_one_room_ai_reply">
                                            <input type="hidden" name="room" value="<?php echo esc_attr($id); ?>">
                                            <input type="hidden" name="cid" value="<?php echo esc_attr($cid); ?>">
                                            <input type="hidden" name="dest" value="thread">
                                            <button type="submit" class="button-link" style="padding:0;cursor:pointer;font-size:.8em;text-decoration:underline;opacity:.7;" title="<?php echo esc_attr__('Let this AI reply into the linked thread — full stored history as context (e.g. “condense the last rounds”). Write your request into the chat first.', 'cluster-one-bismarck'); ?>">→ <?php esc_html_e('Thread', 'cluster-one-bismarck'); ?></button>
                                        </form>
<?php endif; ?>
<?php /* v0.9.33: Hier stand der ZWEITE Wunsch-Knopf (cluster_one_fs_wish),
       siebenunddreissig Zeilen unter dem ersten, ebenfalls blau, mit
       derselben Bedeutung. Er ist gestrichen; der Knopf weiter oben
       (cluster_one_ai_wish) schreibt jetzt in denselben Speicher und
       zuendet damit die blaue Lampe, die seit 0.0.84 gebaut ist und nie
       geleuchtet hat. EIN WUNSCH, EIN KNOPF, EINE LAMPE. */ ?>
                                </div>
<?php endforeach; ?>
<?php
}


/** Bismarck → Einstellungen: Menschen-Tabelle (§4), Gegenstück zur
   KI-Kennungstabelle. Teil A (WP-User, Profilfelder + Ausschluss-Rolle-
   Label), Teil B (Formular für Nicht-WP-Personen). */
function cluster_one_render_bismarck_humans(): void {
    if (isset($_GET['role_saved'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Label saved.', 'cluster-one-bismarck') . '</p></div>';
    }
    if (isset($_GET['human_saved'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Person added.', 'cluster-one-bismarck') . '</p></div>';
    }
    if (isset($_GET['human_deleted'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Person removed.', 'cluster-one-bismarck') . '</p></div>';
    }
    ?>
<?php cluster_one_bismarck_humans_seite_wp(); ?><?php cluster_one_bismarck_humans_seite_extern(); ?><?php cluster_one_bismarck_humans_seite_rolle(); ?>    <?php
}

function cluster_one_bismarck_humans_seite_wp(): void { ?>    <h2 style="margin-top:2.5em;"><?php esc_html_e('Humans', 'cluster-one-bismarck'); ?></h2>
    <p style="max-width:680px;opacity:.8;"><?php esc_html_e('Every human who can speak in a room, on this node — WP users and, structurally prepared, people without a WP account. The SysOp is always listed but never a selectable room participant.', 'cluster-one-bismarck'); ?></p>

    <h3><?php esc_html_e('WP users', 'cluster-one-bismarck'); ?></h3>
    <?php $wp_humans = cluster_one_get_wp_humans(); ?>
<?php if (empty($wp_humans)) : ?>
        <p style="opacity:.7;"><?php esc_html_e('No WP users found.', 'cluster-one-bismarck'); ?></p>
<?php else : ?>
        <table class="widefat striped" style="max-width:900px;">
            <thead><tr>
                <th><?php esc_html_e('Name', 'cluster-one-bismarck'); ?></th>
                <th><?php esc_html_e('WP role', 'cluster-one-bismarck'); ?></th>
                <th><?php esc_html_e('Forum role', 'cluster-one-bismarck'); ?></th>
                <th>Discord</th>
                <th>Matrix</th>
                <th><?php esc_html_e('Native language', 'cluster-one-bismarck'); ?></th>
                <th><?php esc_html_e('Timezone', 'cluster-one-bismarck'); ?></th>
            </tr></thead>
            <tbody>
<?php foreach ($wp_humans as $h) : ?>
                <tr>
                    <td>
                        <strong><a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_profile&view=' . (int) ($h['user_id'] ?? 0))); ?>"><?php echo esc_html((string) $h['name']); ?></a></strong>
<?php if (!empty($h['is_main_admin'])) : ?>
                            <span style="opacity:.6;font-size:.85em;"> · SysOp</span>
<?php endif; ?>
                    </td>
                    <td style="opacity:.8;"><?php echo esc_html((string) $h['wp_role']); ?></td>
                    <td style="opacity:.8;"><?php echo $h['forum_role'] !== '' ? esc_html((string) $h['forum_role']) : '<span style="opacity:.5;">—</span>'; ?></td>
                    <td style="opacity:.7;"><?php echo esc_html((string) $h['discord']); ?></td>
                    <td style="opacity:.7;"><?php echo esc_html((string) $h['matrix']); ?></td>
                    <td style="opacity:.7;"><?php echo esc_html((string) $h['lang']); ?></td>
                    <td style="opacity:.7;"><?php echo esc_html((string) $h['tz']); ?></td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
        <p style="opacity:.7;font-size:.92em;max-width:680px;"><?php echo wp_kses_post(__('Discord/Matrix handle, native language and timezone are set on each user’s own WP profile page (Users → Edit), section “Cluster One”.', 'cluster-one-bismarck')); ?></p>
<?php endif; ?>

<?php
}


function cluster_one_bismarck_humans_seite_extern(): void { ?>    <?php /* v0.3.14: Der Abschnitt hiess "Non-WP people" und beschrieb sich als
   Vorbereitung fuer eine Bruecke, die es nicht gibt — eine Liste, die nur in
   der Zukunft einen Zweck hatte. Er hat aber SCHON JETZT einen: Es ist die
   Merkliste der Menschen, die man einschreiben will. Wer sie so liest, fuellt
   sie heute aus statt spaeter. */ ?>
    <h3 style="margin-top:2em;"><?php esc_html_e('People to invite', 'cluster-one-bismarck'); ?></h3>
    <p style="max-width:680px;opacity:.85;"><?php esc_html_e('Everyone you want on this node but who is not here yet — with the handles you would reach them by. It is a to-do list first: writing someone down is how you remember to ask them.', 'cluster-one-bismarck'); ?></p>
    <p style="max-width:680px;opacity:.75;font-size:.92em;"><?php esc_html_e('The Discord and Matrix handles are not decoration: a Matrix bridge is being worked on, and these records are what it will connect to. Until it exists, nothing is sent from here — you invite people the way you always did. Anyone who knows Matrix and wants to help build the bridge is welcome.', 'cluster-one-bismarck'); ?></p>
    <?php $ext_humans = cluster_one_get_humans_external(); $i_am_sysop = cluster_one_is_sysop(); ?>
<?php if (!empty($ext_humans)) : ?>
        <table class="widefat striped" style="max-width:960px;margin-bottom:1em;">
            <thead><tr>
                <th><?php esc_html_e('Name', 'cluster-one-bismarck'); ?></th>
                <th>Discord</th>
                <th>Matrix</th>
                <th><?php esc_html_e('Native language', 'cluster-one-bismarck'); ?></th>
                <th><?php esc_html_e('Timezone', 'cluster-one-bismarck'); ?></th>
                <th><?php esc_html_e('Visible to everyone', 'cluster-one-bismarck'); ?></th>
                <th style="width:2em;"></th>
            </tr></thead>
            <tbody>
            <?php foreach ($ext_humans as $id => $h) :
                $visible = !empty($h['node_wide_visible']);
                $show    = $visible || $i_am_sysop; // SysOp sieht immer die echten Angaben, sonst nur nach Freigabe
            ?>
                <tr>
                    <td><?php echo esc_html((string) ($h['name'] ?? '')); ?></td>
                    <td style="opacity:.7;"><?php echo $show ? esc_html((string) ($h['discord'] ?? '')) : '<span style="opacity:.5;">' . esc_html__('hidden', 'cluster-one-bismarck') . '</span>'; ?></td>
                    <td style="opacity:.7;"><?php echo $show ? esc_html((string) ($h['matrix'] ?? '')) : '<span style="opacity:.5;">' . esc_html__('hidden', 'cluster-one-bismarck') . '</span>'; ?></td>
                    <td style="opacity:.7;"><?php echo esc_html((string) ($h['lang'] ?? '')); ?></td>
                    <td style="opacity:.7;"><?php echo esc_html((string) ($h['tz'] ?? '')); ?></td>
                    <td>
<?php if ($i_am_sysop) : ?>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                                <?php wp_nonce_field('cluster_one_human_external_visibility'); ?>
                                <input type="hidden" name="action" value="cluster_one_human_external_visibility">
                                <input type="hidden" name="human_id" value="<?php echo esc_attr((string) $id); ?>">
                                <input type="hidden" name="node_wide_visible" value="<?php echo $visible ? '0' : '1'; ?>">
                                <button type="submit" class="button button-small"><?php echo $visible ? esc_html__('Yes — click to hide', 'cluster-one-bismarck') : esc_html__('No — click to confirm consent', 'cluster-one-bismarck'); ?></button>
                            </form>
<?php else : ?>
                            <?php echo $visible ? esc_html__('Yes', 'cluster-one-bismarck') : esc_html__('No', 'cluster-one-bismarck'); ?>
<?php endif; ?>
                    </td>
                    <td>
<?php if ($i_am_sysop) : // v0.0.73: Entfernen ist SysOp-exklusiv ?>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                            <?php wp_nonce_field('cluster_one_human_external_delete'); ?>
                            <input type="hidden" name="action" value="cluster_one_human_external_delete">
                            <input type="hidden" name="human_id" value="<?php echo esc_attr((string) $id); ?>">
                            <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;" title="<?php echo esc_attr__('Remove', 'cluster-one-bismarck'); ?>" aria-label="<?php echo esc_attr__('Remove', 'cluster-one-bismarck'); ?>" onclick="return confirm('<?php echo esc_js(__('Remove this person?', 'cluster-one-bismarck')); ?>');">
                                <span class="dashicons dashicons-dismiss" aria-hidden="true" style="color:#8a8a8a;font-size:18px;width:18px;height:18px;"></span>
                            </button>
                        </form>
<?php endif; // v0.0.73 ?>
                    </td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
<?php endif; ?>
<?php if ($i_am_sysop) : // v0.0.73: Anlegen ist SysOp-exklusiv — Knopf spiegelt den Handler ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:760px;">
        <?php wp_nonce_field('cluster_one_human_external'); ?>
        <input type="hidden" name="action" value="cluster_one_human_external_add">
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="c1_h_name"><?php esc_html_e('Name', 'cluster-one-bismarck'); ?></label></th>
                <td><input type="text" name="name" id="c1_h_name" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="c1_h_discord">Discord</label></th>
                <td><input type="text" name="discord" id="c1_h_discord" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="c1_h_matrix">Matrix</label></th>
                <td><input type="text" name="matrix" id="c1_h_matrix" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="c1_h_lang"><?php esc_html_e('Native language', 'cluster-one-bismarck'); ?></label></th>
                <td><input type="text" name="lang" id="c1_h_lang" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="c1_h_tz"><?php esc_html_e('Timezone', 'cluster-one-bismarck'); ?></label></th>
                <td><?php cluster_one_render_tz_select('', 'tz', 'c1_h_tz'); // v0.0.73: Auswahl statt Freitext-Falle ?></td>
            </tr>
        </table>
        <?php submit_button(__('Add person', 'cluster-one-bismarck'), 'secondary', 'submit', false); ?>
    </form>
<?php endif; // v0.0.73 ?>

<?php
}


function cluster_one_bismarck_humans_seite_rolle(): void { ?>    <h3 style="margin-top:2.5em;"><?php esc_html_e('Excluded role', 'cluster-one-bismarck'); ?></h3>
    <p style="max-width:680px;opacity:.8;"><?php echo wp_kses_post(__('WP users with this role (assign it under Users → Edit → Role) never show up in the humans table above, and therefore never in any room. For accounts on this node that have nothing to do with Bismarck. The internal slug is fixed forever — only the label below is yours to rename.', 'cluster-one-bismarck')); ?></p>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:500px;">
        <?php wp_nonce_field('cluster_one_excluded_role'); ?>
        <input type="hidden" name="action" value="cluster_one_excluded_role_save">
        <input type="text" name="excluded_role_label" class="regular-text" value="<?php echo esc_attr(cluster_one_excluded_role_label()); ?>">
        <?php submit_button(__('Save label', 'cluster-one-bismarck'), 'secondary', 'submit', false); ?>
    </form>
<?php
}


/* v0.9.29: DIE GEMEINSAME KONFIGURATION — Christians Entscheidung.

   "Es ist egal, ob du auf dem Tab Forum oder dem Tab Chats bist. Wenn du auf
   das Icon fuer Konfigurieren klickst, landest du in einem einzigen
   versteckten Tab."

   Grund: Nach den Schnitten stand fast alles doppelt — Bereich (wandert seit
   0.9.7 gemeinsam), Winsor (legt beide ins Becken), die Kopplung (dieselbe
   Kante). WIR PFLEGTEN AN ZWEI ORTEN, WAS EINMAL EXISTIERT.

   Seine psychologische Trennung bleibt unberuehrt: zwei Reiter, weil Reden und
   Aufschreiben zwei Taetigkeiten sind — aber EINE Konfiguration, weil es EIN
   Projekt ist.

   Dieser Block zeigt die Thema-Haelfte; die Raum-Haelfte steht darueber. */
function cluster_one_paar_seite_thema(array $room, string $id): void {
    $tid = (string) ($room['linked_thread'] ?? '');
    if ($tid === '') {
        return;
    }
    $threads = cluster_one_get_forum_threads();
    if (!isset($threads[$tid])) {
        return;
    }
    $t = $threads[$tid];
    ?>
                    <h3 style="margin-top:2em;"><?php esc_html_e('Thread name', 'cluster-one-bismarck'); ?></h3>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('cluster_one_forum_thread_config_save'); ?>
                        <input type="hidden" name="action" value="cluster_one_forum_thread_config_save">
                        <input type="hidden" name="thread" value="<?php echo esc_attr($tid); ?>">
                        <p><input type="text" name="title" class="regular-text" value="<?php echo esc_attr((string) ($t['title'] ?? '')); ?>"></p>
                        <p class="description" style="max-width:680px;"><?php esc_html_e('Only the label changes — the thread keeps its identity; posts, links and area stay.', 'cluster-one-bismarck'); ?></p>
                        <?php submit_button(__('Save', 'cluster-one-bismarck'), 'secondary', 'submit', false); ?>
                    </form>
<?php
    /* Wo die Rolle die Sichtbarkeit eingefroren hat, steht sie hier — sonst
       gilt die Gaesteliste des Raums (0.9.28). */
    if (cluster_one_thread_visibility_is_role_fixed($t)) : ?>
                    <p class="description" style="max-width:680px;margin-top:1em;"><?php
                        echo esc_html(sprintf(
                            /* translators: %s: kind of the thread */
                            __('This thread is a %s — who may read it follows from that, not from the guest list.', 'cluster-one-bismarck'),
                            cluster_one_thread_kind_label($t)
                        )); ?></p>
<?php endif;
}


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

/* HIERHER UMGEZOGEN (0.9.80): Welcher Termin haengt an diesem Raum. Ein Aufrufer, hier. */
/** Findet das Event, das genau DIESEN Raum verlinkt hat (Rückwärts-Anzeige,
   read-only von der Raumseite aus — gesetzt wird das nur über die Event-
   Konfiguration, SysOp-exklusiv). */
function cluster_one_room_linked_event(string $room_id): ?array {
    foreach (cluster_one_get_gregor_events() as $ev) {
        if ((string) ($ev['linked_room'] ?? '') === $room_id && $room_id !== '') {
            return $ev;
        }
    }
    return null;
}

/* HIERHER UMGEZOGEN UND GETEILT (0.9.80): die zweite Haelfte des
   admin_notices-Hooks aus kern.php — die Anstupser in Raeumen. Begruendung
   steht bei der ersten Haelfte in pn.php. */
add_action('admin_notices', function () {
    if (!cluster_one_can_access()) {
        return;
    }
    $pings = get_user_meta(get_current_user_id(), 'cluster_one_pings', true);
    if (!is_array($pings) || empty($pings)) {
        return;
    }
    $rooms = cluster_one_get_rooms();
    foreach ($pings as $rid => $p) {
        if (!isset($rooms[$rid])) {
            continue;
        }
        $url = admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode((string) $rid));
        echo '<div class="notice notice-info"><p>Cluster One: ';
        /* translators: %1$s: Name des Menschen, der angepingt hat, %2$s: Name des Raums. */
        echo esc_html(sprintf(__('%1$s pinged you in “%2$s”.', 'cluster-one-bismarck'), (string) ($p['by'] ?? '?'), (string) ($rooms[$rid]['name'] ?? $rid)));
        echo ' <a href="' . esc_url($url) . '">' . esc_html__('Open the chat', 'cluster-one-bismarck') . '</a></p></div>';
    }
});


/* v1.3.4 — DIE MENSCHEN-TABELLE MELDET SICH AN. Drei Aufrufe in kranz.php
   riefen cluster_one_render_bismarck_humans() beim Namen. */
add_action('cluster_one_abschnitt', function ($c1_wo) {
    if ((string) $c1_wo === 'menschen') { cluster_one_render_bismarck_humans(); }
});

}
