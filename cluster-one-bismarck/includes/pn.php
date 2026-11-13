<?php
/**
 * Cluster One — Modul pn (DER SCHNITT, v0.0.100 -> Module).
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

   OHNE $c1_pn_hoeher: die Vorlage aus §4 traegt ein
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
$cluster_one_pn_laeuft = !function_exists('cluster_one_render_pn_section');
cluster_one_hier('pn', '', $cluster_one_pn_laeuft);

if ($cluster_one_pn_laeuft) {

/** Gregor-Seite (v0.0.53 Kalender-Gerüst, v0.0.57 Events): eigener
   Menüpunkt. Die Treffen-Stufe war zunächst global fürs ganze Tab gedacht,
   zu grob — mehrere Events können gleichzeitig laufen, mit je eigener
   Stufe. Sie gehört zum EVENT, nicht zum Tab, daher jetzt dort statt hier. */
/** Die PN-Seite (v0.0.87): Briefkasten, Verfassen, Postausgang. */
function cluster_one_render_pn_section(): void { // v0.0.93: Sektion statt Seite
    if (!cluster_one_can_access()) {
        return;
    }
    $me    = get_current_user_id();
    $inbox = array_reverse(cluster_one_pn_get($me, 'inbox'));
    $sent  = array_reverse(cluster_one_pn_get($me, 'sent'));
    $muted = array_map('intval', (array) get_user_meta($me, 'cluster_one_pn_muted', true));
    $name_of = function (int $uid): string {
        $u = get_userdata($uid);
        return $u ? $u->display_name : ('#' . $uid);
    };
    // Betreten = gelesen (der Blick in den Briefkasten).
    $had_unread = false;
    $inbox_store = cluster_one_pn_get($me, 'inbox');
    foreach ($inbox_store as $i => $p) {
        if (empty($p['read'])) {
            $inbox_store[$i]['read'] = true;
            $had_unread = true;
        }
    }
    if ($had_unread) {
        cluster_one_pn_save($me, 'inbox', $inbox_store);
    }
    ?>
    <div style="max-width:860px;">
        <h2 style="margin-top:1.5em;">PN</h2>
        <p style="opacity:.75;max-width:680px;"><?php esc_html_e('Letters between the humans of this node. No witnesses, no AIs, no links — a letter lives in the recipient’s mailbox, not in a room of the node. Honest limit: a WordPress admin can always force access to anything; the system just refuses to make that comfortable.', 'cluster-one-bismarck'); ?></p>
        <?php if (isset($_GET['pn_sent'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
            <div class="notice <?php echo $_GET['pn_sent'] === 'ok' ? 'notice-success' : 'notice-warning'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?> is-dismissible"><p>
                <?php
                /* v1.5.1: Der dritte Ausgang "warten" ist fort — mit der
                   Ein-Zug-Regel, die ihn ausloeste. cluster_one_pn_unread_from()
                   bleibt: der Zuruecknehmen-Knopf weiter unten braucht sie und
                   fragt etwas anderes ("kann ich diesen Brief noch holen?"). */
                if ($_GET['pn_sent'] === 'ok') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
                    esc_html_e('Your letter was delivered.', 'cluster-one-bismarck');
                } elseif ($_GET['pn_sent'] === 'voll') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
                    /* v1.6.0: Der Deckel meldet sich selbst — ehrlich sagen
                       statt lautlos verschlucken, und mit dem Weg heraus. */
                    esc_html_e('Five unread letters from you are already waiting with this person. One more only after they have read them — you can still edit the ones that are unread.', 'cluster-one-bismarck');
                } else {
                    esc_html_e('You have been muted by this person — your letter could not be delivered.', 'cluster-one-bismarck');
                }
                ?>
            </p></div>
        <?php endif; ?>

        <h2 style="margin-top:1.2em;"><?php esc_html_e('Write a letter', 'cluster-one-bismarck'); ?></h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:680px;">
            <?php wp_nonce_field('cluster_one_pn_send'); ?>
            <input type="hidden" name="action" value="cluster_one_pn_send">
            <p><label><?php esc_html_e('To:', 'cluster-one-bismarck'); ?>
                <select name="to" required>
                    <option value=""><?php esc_html_e('— choose —', 'cluster-one-bismarck'); ?></option>
                    <?php foreach (cluster_one_pn_recipients() as $uid) : ?>
                        <option value="<?php echo (int) $uid; ?>"><?php echo esc_html($name_of($uid)); ?></option>
                    <?php endforeach; ?>
                </select></label></p>
            <p><textarea name="body" rows="5" style="width:100%;" required placeholder="<?php echo esc_attr__('Your letter…', 'cluster-one-bismarck'); ?>"></textarea></p>
            <p><button type="submit" class="button button-primary"><?php esc_html_e('Send', 'cluster-one-bismarck'); ?></button></p>
        </form>

        <h2 style="margin-top:1.6em;"><?php esc_html_e('Mailbox', 'cluster-one-bismarck'); ?></h2>
        <?php if (empty($inbox)) : ?>
            <p style="opacity:.6;"><?php esc_html_e('No letters.', 'cluster-one-bismarck'); ?></p>
        <?php endif; ?>
        <?php foreach ($inbox as $p) :
            $from = (int) ($p['from'] ?? 0);
        ?>
            <div style="border:1px solid #e0e0e0;border-radius:6px;padding: .7em .9em;margin:.5em 0;background:#fff;">
                <div style="display:flex;gap:.7em;align-items:baseline;flex-wrap:wrap;">
                    <strong><?php echo esc_html($name_of($from)); ?></strong>
                    <span style="opacity:.55;font-size:.85em;"><?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', (int) ($p['ts'] ?? 0))); ?></span>
                    <?php if (!empty($p['reported_by'])) : ?>
                        <?php /* translators: %1$s: Name des Meldenden, %2$s: Datum des Originals. (0.2.68: nummeriert, Plugin Check) */ ?>
                        <span style="color:#b45309;font-size:.85em;"><?php echo esc_html(sprintf(__('reported by %1$s — original from %2$s', 'cluster-one-bismarck'), $name_of((int) $p['reported_by']), date_i18n(get_option('date_format'), (int) ($p['original_ts'] ?? 0)))); ?></span>
                    <?php endif; ?>
                </div>
                <p style="margin:.5em 0;white-space:pre-wrap;"><?php echo esc_html((string) ($p['body'] ?? '')); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:flex;gap:.5em;">
                    <?php wp_nonce_field('cluster_one_pn_action'); ?>
                    <input type="hidden" name="action" value="cluster_one_pn_action">
                    <input type="hidden" name="pn" value="<?php echo esc_attr((string) ($p['id'] ?? '')); ?>">
                    <input type="hidden" name="uid" value="<?php echo (int) $from; ?>">
                    <button type="submit" name="op" value="mute" class="button button-small"><?php echo in_array($from, $muted, true) ? esc_html__('Unmute sender', 'cluster-one-bismarck') : esc_html__('Mute sender', 'cluster-one-bismarck'); ?></button>
                    <?php if (!cluster_one_is_sysop()) : ?>
                        <button type="submit" name="op" value="report" class="button button-small" onclick="return confirm('<?php echo esc_js(__('Send a full copy of this letter to the SysOp?', 'cluster-one-bismarck')); ?>');"><?php esc_html_e('Report to SysOp', 'cluster-one-bismarck'); ?></button>
                    <?php endif; ?>
                    <button type="submit" name="op" value="delete" class="button button-small"><?php esc_html_e('Delete', 'cluster-one-bismarck'); ?></button>
                </form>
            </div>
        <?php endforeach; ?>

        <h2 style="margin-top:1.6em;"><?php esc_html_e('Sent', 'cluster-one-bismarck'); ?></h2>
        <?php if (empty($sent)) : ?>
            <p style="opacity:.6;"><?php esc_html_e('Nothing sent yet.', 'cluster-one-bismarck'); ?></p>
        <?php endif; ?>
        <table class="widefat striped" style="max-width:860px;">
            <?php foreach ($sent as $p) : ?>
                <tr>
                    <td style="width:11em;"><strong><?php echo esc_html($name_of((int) ($p['to'] ?? 0))); ?></strong></td>
                    <td style="width:11em;opacity:.6;"><?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', (int) ($p['ts'] ?? 0))); ?></td>
                    <td style="white-space:pre-wrap;"><?php echo esc_html((string) ($p['body'] ?? '')); ?></td>
                    <td style="width:9em;"><?php
                        /* v0.9.38: Vierter Zustand — zurueckgenommen. Die Spur
                           bleibt im Postausgang stehen, ausdruecklich NICHT
                           spurlos: Der Absender soll sich erinnern, dass er es
                           geschrieben hat. Ueberschrieben wird hier nichts. */
                        if (!empty($p['withdrawn'])) {
                            echo '<span style="color:#6b7280;">' . esc_html__('withdrawn', 'cluster-one-bismarck') . '</span>';
                        } elseif (!empty($p['delivered'])) {
                            echo '<span style="color:#1a7f37;">' . esc_html__('delivered', 'cluster-one-bismarck') . '</span>';
                        } else {
                            echo '<span style="color:#b45309;">' . esc_html__('not delivered (muted)', 'cluster-one-bismarck') . '</span>';
                        }
                    ?></td>
                    <td style="width:11em;"><?php
                        /* Der Knopf steht nur da, solange er etwas bewirken
                           kann: eigener Brief, zugestellt, noch nicht gelesen,
                           noch nicht zurueckgenommen. Ein Knopf, der nichts
                           tut, ist ein luegendes Etikett (0.9.26). */
                        $c1_zu = (int) ($p['to'] ?? 0);
                        if (empty($p['withdrawn']) && !empty($p['delivered'])
                            && $c1_zu > 0 && cluster_one_pn_unread_from($me, $c1_zu)) :
                            $c1_offen = false;
                            foreach (cluster_one_pn_get($c1_zu, 'inbox') as $c1_i) {
                                if ((string) ($c1_i['id'] ?? '') === (string) ($p['id'] ?? '') && empty($c1_i['read'])) {
                                    $c1_offen = true;
                                    break;
                                }
                            }
                            if ($c1_offen) : ?>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                            <?php wp_nonce_field('cluster_one_pn_action'); ?>
                            <input type="hidden" name="action" value="cluster_one_pn_action">
                            <input type="hidden" name="op" value="withdraw">
                            <input type="hidden" name="pn" value="<?php echo esc_attr((string) ($p['id'] ?? '')); ?>">
                            <input type="hidden" name="uid" value="<?php echo (int) $c1_zu; ?>">
                            <button type="submit" class="button button-small" title="<?php echo esc_attr__('Not read yet — you can take it back. The entry stays here, marked as withdrawn.', 'cluster-one-bismarck'); ?>"><?php esc_html_e('Withdraw', 'cluster-one-bismarck'); ?></button>
                        </form>
                    <?php endif; endif; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php
}

/* --- Nachricht in einen Raum schreiben (Backend-Aktion, Nonce + Capability) --- */
/** PN-Helfer (v0.0.87). Briefkasten und Postausgang als User-Meta. */
function cluster_one_pn_get(int $uid, string $box): array {
    $v = get_user_meta($uid, 'cluster_one_pn_' . $box, true);
    return is_array($v) ? $v : array();
}

function cluster_one_pn_save(int $uid, string $box, array $items): void {
    update_user_meta($uid, 'cluster_one_pn_' . $box, $items);
}

/** v1.6.0: WIE VIELE UNGELESENE BRIEFE LIEGEN BEI IHM VON MIR?
 *
 * Grundlage des Deckels. Die Ein-Zug-Regel (0.9.38) hatte ihn bei EINS; das
 * war zu eng fuer einen Menschen, der zwei Dinge zu sagen hat. Christians Zahl
 * vom 31.08. ist fuenf.
 *
 * WAS DIE REGEL WIRKLICH ABDECKTE, und es stand nirgends: Bismarck hat am
 * 31.08. gemessen, dass der PN-Speicher **keinen Deckel** hat —
 *
 *     Chat        behaelt die letzten 200 Nachrichten
 *     Forum       bewusst KEIN Deckel (ein Thema soll nicht vorne verlieren)
 *     PN          KEINER — ein serialisiertes user_meta waechst unbegrenzt
 *
 * Die Ein-Brief-Regel war also nicht nur Umgangsform, sie war die einzige
 * Bremse vor einem unbegrenzt wachsenden Feld. Und die Stummschaltung ersetzt
 * sie nicht: sie wirkt erst, NACHDEM jemand stummgeschaltet hat, und im Haus
 * ist Stummschalten ein schwererer Akt als Warten ("Las Vegas, kein Pranger").
 *
 *     Wer eine Bremse entfernt, muss wissen, was sie ausser dem Offensichtlichen
 *     noch gehalten hat.
 */
function cluster_one_pn_ungelesen_von(int $von, int $an): int {
    $c1_n = 0;
    foreach (cluster_one_pn_get($an, 'inbox') as $c1_p) {
        if ((int) ($c1_p['from'] ?? 0) === $von && empty($c1_p['read'])) {
            $c1_n++;
        }
    }
    return $c1_n;
}

/** v1.6.0: Der Deckel, an EINER Stelle — wie am Mesh-Tor. */
function cluster_one_pn_deckel(): int {
    return 5;
}

/** v0.9.38: Liegt beim Gegenueber noch ein ungelesener Brief von mir?
   Grundlage der Ein-Brief-Regel und der Ruecknahme. Gelesen ist gelesen —
   danach gehoert der Brief dem Empfaenger. */
function cluster_one_pn_unread_from(int $von, int $an): bool {
    foreach (cluster_one_pn_get($an, 'inbox') as $p) {
        if ((int) ($p['from'] ?? 0) === $von && empty($p['read'])) {
            return true;
        }
    }
    return false;
}

function cluster_one_pn_recipients(): array { // Menschen der Weißliste außer mir
    $out = array();
    foreach (cluster_one_gregor_humans() as $uid) {
        if ($uid !== get_current_user_id()) {
            $out[] = $uid;
        }
    }
    return $out;
}

add_action('admin_post_cluster_one_pn_action', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_pn_action');
    $op   = isset($_POST['op']) ? sanitize_key(wp_unslash($_POST['op'])) : '';
    $pid  = isset($_POST['pn']) ? sanitize_text_field(wp_unslash($_POST['pn'])) : '';
    $uid  = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
    $me   = get_current_user_id();
    $back = admin_url('admin.php?page=cluster_one_personal_area');

    if ($op === 'mute' && $uid > 0 && $uid !== $me) {
        $muted = array_map('intval', (array) get_user_meta($me, 'cluster_one_pn_muted', true));
        if (in_array($uid, $muted, true)) {
            $muted = array_values(array_diff($muted, array($uid)));
        } else {
            $muted[] = $uid;
        }
        update_user_meta($me, 'cluster_one_pn_muted', $muted);
    } elseif ($op === 'withdraw' && $pid !== '' && $uid > 0) {
        /* v0.9.38: EIN UNGESEHENER BRIEF LAESST SICH ZURUECKNEHMEN.
           Christians Regel: "Solange die nicht gesehen wurde, kann die
           beendet werden."

           Drei Bedingungen, und jede hat einen Grund:
             - nur MEIN Brief (from === $me): man nimmt nur zurueck, was man
               selbst geschrieben hat — dieselbe Grenze wie beim Fremd-Label
             - nur UNGELESEN: was gesehen wurde, gehoert dem Empfaenger. Der
               Absender greift nicht in fremde Vergangenheit (die Ulli-Regel)
             - im Postausgang bleibt die Spur, als zurueckgenommen markiert.
               NICHT SPURLOS: der Absender soll sich erinnern, dass er es
               geschrieben hat. Ueberschrieben wird hier nichts.

           Die Ruecknahme ist die Gegenseite der Ein-Brief-Regel: Wer nur
           einmal schreiben darf, muss den einen Zug zurueckholen koennen. */
        $c1_in  = cluster_one_pn_get($uid, 'inbox');
        $c1_neu = array();
        $c1_weg = false;
        foreach ($c1_in as $c1_p) {
            if ((string) ($c1_p['id'] ?? '') === $pid
                && (int) ($c1_p['from'] ?? 0) === $me
                && empty($c1_p['read'])) {
                $c1_weg = true;
                continue;
            }
            $c1_neu[] = $c1_p;
        }
        if ($c1_weg) {
            cluster_one_pn_save($uid, 'inbox', $c1_neu);
            $c1_out = cluster_one_pn_get($me, 'sent');
            foreach ($c1_out as $c1_i => $c1_s) {
                if ((string) ($c1_s['id'] ?? '') === $pid) {
                    $c1_out[$c1_i]['withdrawn'] = time();
                }
            }
            cluster_one_pn_save($me, 'sent', $c1_out);
        }
    } elseif ($op === 'delete' && $pid !== '') {
        $inbox = array_values(array_filter(cluster_one_pn_get($me, 'inbox'), function ($p) use ($pid) {
            return (string) ($p['id'] ?? '') !== $pid;
        }));
        cluster_one_pn_save($me, 'inbox', $inbox);
    } elseif ($op === 'report' && $pid !== '') {
        // Melden = kein Bruch des Briefgeheimnisses, sondern seine
        // Bestätigung: Der Brief gehört dem Empfänger — wem ein Brief
        // gehört, der darf ihn zeigen. Vollständige Kopie an den SysOp.
        foreach (cluster_one_pn_get($me, 'inbox') as $p) {
            if ((string) ($p['id'] ?? '') === $pid) {
                $sys = cluster_one_main_admin_user_id();
                if ($sys > 0 && $sys !== $me) {
                    $inbox   = cluster_one_pn_get($sys, 'inbox');
                    $inbox[] = array(
                        'id'          => cluster_one_msg_id(),
                        'from'        => (int) ($p['from'] ?? 0),
                        'to'          => $sys,
                        'body'        => (string) ($p['body'] ?? ''),
                        'ts'          => time(),
                        'read'        => false,
                        'reported_by' => $me,
                        'original_ts' => (int) ($p['ts'] ?? 0),
                    );
                    cluster_one_pn_save($sys, 'inbox', $inbox);
                }
                break;
            }
        }
    }
    wp_safe_redirect($back);
    exit;
});


/* ===========================================================================
   v0.9.80 — UMGEZOGEN AUS kern.php (Bodenarbeit)
   =========================================================================== */

/* HIERHER UMGEZOGEN UND GETEILT (0.9.80).
   In kern.php stand EIN admin_notices-Hook, der ZWEI Organe bediente: die
   ungelesenen Briefe (PN) und die Anstupser in Raeumen (Bismarck). Beide
   fahren zwar in dasselbe Light, aber sie haben nichts miteinander zu tun
   ausser dem Hook — derselbe Zustand wie beim Menue vor 0.9.54.

       Jedes Organ nennt das seine selbst.

   DIES IST DER EINZIGE NICHT BYTE-IDENTISCHE UMZUG dieser Fassung: aus einer
   Closure wurden zwei. Jede Haelfte ist innen unveraendert; nur der Rahmen ist
   neu, und der musste es sein. */
add_action('admin_notices', function () {
    if (!cluster_one_can_access()) {
        return;
    }
    // v0.0.87: ungelesene Briefe
    $c1_unread = 0;
    foreach (cluster_one_pn_get(get_current_user_id(), 'inbox') as $c1_p) {
        if (empty($c1_p['read'])) {
            $c1_unread++;
        }
    }
    if ($c1_unread > 0) {
        echo '<div class="notice notice-info"><p>Cluster One: ';
        /* translators: %d: Anzahl ungelesener Briefe. */
        echo esc_html(sprintf(_n('You have %d unread letter.', 'You have %d unread letters.', $c1_unread, 'cluster-one-bismarck'), $c1_unread));
        echo ' <a href="' . esc_url(admin_url('admin.php?page=cluster_one_personal_area')) . '">' . esc_html__('Open the mailbox', 'cluster-one-bismarck') . '</a></p></div>';
    }
});


/* v1.3.4 — PN MELDET SEINEN ABSCHNITT AN, STATT GERUFEN ZU WERDEN.
   kranz.php rief cluster_one_render_pn_section() beim Namen. Ohne Bismarck war
   das ein Fatal; jetzt bleibt der Platz einfach leer.
       Ein Platz heisst, wo er ist — nicht, wer ihn fuellt. */
add_action('cluster_one_abschnitt', function ($c1_wo) {
    if ((string) $c1_wo === 'persoenlich') { cluster_one_render_pn_section(); }
});

}
