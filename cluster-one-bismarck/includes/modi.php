<?php
/**
 * Cluster One — Modul modi (Züge 11–13: Feinschnitt aus bismarck).
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

   OHNE $c1_modi_hoeher: die Vorlage aus §4 traegt ein
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
$cluster_one_modi_laeuft = !function_exists('cluster_one_render_modes_page');
cluster_one_hier('modi', '', $cluster_one_modi_laeuft);

if ($cluster_one_modi_laeuft) {

/** Modi-Vokabular (v0.0.75): Tabelle Name/Beschreibung, SysOp-editierbar.
   Reine Beschreibungen — die Seite selbst sagt es. Slug bleibt für immer
   (Ausschluss-Rollen-Muster); Auslieferungs-Modi via Saat-Register, s.
   cluster_one_default_modes(). */
function cluster_one_render_modes_page(): void {
    cluster_one_page_gate();
    $modes = cluster_one_get_modes();
    $i_am_sysop = cluster_one_is_sysop();
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Kranz'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php
        /* v0.2.6 (Welle 7) — DER WAISE, GERIEGELT.

           cluster_one_render_kranz_tabs() wohnt in kranz.php. Nach 7a liegt
           kranz.php nicht mehr in diesem Paket, und diese Zeile war der
           einzige NACKTE Griff einer Nicht-Kranz-Seite in den Kranz: ein
           Fatal auf der Modi-Seite am Tag des Schnitts, ohne dass jemand
           kranz.php vermisst. Gefunden von Mammut beim Gegenpruefen einer
           fremden Zahl, nicht von einer Liste.

           MAMMUTS FRAGE WAR: was ist eine Reiterleiste wert, wenn es den
           Kranz nicht mehr gibt — und wenn die Antwort „nichts" ist, gehoert
           die Zeile entfernt statt geriegelt. Der Satz stimmt. Die Frage
           entscheidet den Fall trotzdem nicht, und das ist gemessen:

               ohne kranz.php   Riegel und Entfernen drucken dieselbe Seite.
                                Der Rueckfall IST das Entfernen.
               mit  kranz.php   der Riegel druckt acht Reiter, das Entfernen
                                keinen.

           Riegeln verliert in keiner Welt etwas und gewinnt in einer.
           Entfernen gewinnt in keiner Welt etwas und verliert in einer.
           Waere der Rueckfall irgendetwas anderes als „nichts ausgeben",
           waere die Antwort andersherum.

           Und „mit kranz.php" ist nicht nur heute: nach dem Schnitt traegt
           jedes Paket, das kranz.php mitbringt, die Funktion auf diesen
           Knoten; welche Kopie laedt, entscheidet der Riegel (H67).

           Form nach dem Vorbild kern.php:2216 (v0.9.63) und kranz.php:1951.
           Bezeugt von zeuge-modi-waise.php, sechs Welten. */
        if (function_exists('cluster_one_render_kranz_tabs')) {
            cluster_one_render_kranz_tabs('cluster_one_modes');
        }
        ?>
        <?php if (isset($_GET['mode_saved'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e('Saved.', 'cluster-one-bismarck'); ?></p></div><?php endif; ?>
        <h2 style="margin-top:1em;"><?php esc_html_e('Modes', 'cluster-one-bismarck'); ?></h2>
        <p style="max-width:720px;opacity:.85;"><?php esc_html_e('A shared vocabulary for what a chat is for. Modes are pure descriptions — they never switch any function (technical switches like “humans only” live in the chat configuration). The shipped entries are yours: rename, rewrite or delete them; deleted entries never come back on updates, and chats keep showing a deleted mode’s last known name, marked.', 'cluster-one-bismarck'); ?></p>
        <?php if (empty($modes)) : ?>
            <p style="opacity:.6;"><?php esc_html_e('None yet.', 'cluster-one-bismarck'); ?></p>
        <?php else : ?>
            <table class="widefat striped" style="max-width:960px;">
                <thead><tr>
                    <th style="width:22%;"><?php esc_html_e('Mode name', 'cluster-one-bismarck'); ?></th>
                    <th><?php esc_html_e('Description', 'cluster-one-bismarck'); ?></th>
                    <?php if ($i_am_sysop) : ?><th style="width:9em;"></th><?php endif; ?>
                </tr></thead>
                <tbody>
                <?php foreach ($modes as $slug => $m) : ?>
                    <tr>
                        <?php if ($i_am_sysop) : ?>
                            <td>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="c1-mode-<?php echo esc_attr((string) $slug); ?>" style="margin:0;">
                                    <?php wp_nonce_field('cluster_one_mode_save'); ?>
                                    <input type="hidden" name="action" value="cluster_one_mode_save">
                                    <input type="hidden" name="slug" value="<?php echo esc_attr((string) $slug); ?>">
                                    <input type="text" name="mode_name" value="<?php echo esc_attr((string) ($m['name'] ?? '')); ?>" class="regular-text" style="width:100%;" required>
                                </form>
                                <span style="opacity:.45;font-size:.82em;"><?php echo esc_html((string) $slug); ?></span>
                            </td>
                            <td><textarea name="mode_desc" form="c1-mode-<?php echo esc_attr((string) $slug); ?>" rows="2" class="large-text"><?php echo esc_textarea((string) ($m['desc'] ?? '')); ?></textarea></td>
                            <td style="white-space:nowrap;">
                                <button type="submit" form="c1-mode-<?php echo esc_attr((string) $slug); ?>" class="button button-small"><?php esc_html_e('Save', 'cluster-one-bismarck'); ?></button>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;margin:0;">
                                    <?php wp_nonce_field('cluster_one_mode_delete'); ?>
                                    <input type="hidden" name="action" value="cluster_one_mode_delete">
                                    <input type="hidden" name="slug" value="<?php echo esc_attr((string) $slug); ?>">
                                    <?php /* translators: %s: Name des Modus. */ ?>
                                    <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;vertical-align:middle;" title="<?php echo esc_attr__('Delete', 'cluster-one-bismarck'); ?>" aria-label="<?php echo esc_attr__('Delete', 'cluster-one-bismarck'); ?>" onclick="return confirm('<?php echo esc_js(sprintf(__('Delete the mode “%s”? Chats using it keep its last known name, marked.', 'cluster-one-bismarck'), (string) ($m['name'] ?? $slug))); ?>');">
                                        <span class="dashicons dashicons-dismiss" aria-hidden="true" style="color:#8a8a8a;font-size:18px;width:18px;height:18px;"></span>
                                    </button>
                                </form>
                            </td>
                        <?php else : ?>
                            <td><strong><?php echo esc_html((string) ($m['name'] ?? $slug)); ?></strong></td>
                            <td><?php echo esc_html((string) ($m['desc'] ?? '')); ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <?php if ($i_am_sysop) : ?>
            <h3 style="margin-top:2em;"><?php esc_html_e('New mode', 'cluster-one-bismarck'); ?></h3>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:760px;">
                <?php wp_nonce_field('cluster_one_mode_save'); ?>
                <input type="hidden" name="action" value="cluster_one_mode_save">
                <table class="form-table" role="presentation">
                    <tr><th scope="row"><label for="c1_mode_name"><?php esc_html_e('Mode name', 'cluster-one-bismarck'); ?></label></th>
                        <td><input type="text" name="mode_name" id="c1_mode_name" class="regular-text" required></td></tr>
                    <tr><th scope="row"><label for="c1_mode_desc"><?php esc_html_e('Description', 'cluster-one-bismarck'); ?></label></th>
                        <td><textarea name="mode_desc" id="c1_mode_desc" rows="2" class="large-text"></textarea></td></tr>
                </table>
                <?php submit_button(__('Add mode', 'cluster-one-bismarck'), 'secondary', 'submit', false); ?>
            </form>
        <?php endif; ?>
    </div>
    <?php
}

/* --- Bismarck-Räume: Speicher + Liste + echtes „Neuer Raum"-Formular --- */

/** Modi (v0.0.75): reine BESCHREIBUNGEN, keine Funktionskopplung mehr
   (Entscheidung SysOp 16.07.2026). Slug/Label-Trennung nach dem Vorbild der
   Ausschluss-Rolle: Räume speichern den stabilen Slug, Name + Beschreibung
   gehören dem SysOp. Das SAAT-REGISTER löst die Auslieferungs-Frage: jeder
   mitgelieferte Modus wird pro Slug genau EINMAL eingepflanzt und im
   Register vermerkt — Edits werden nie überschrieben, Löschungen nie
   wiederbelebt, und ein später mitgelieferter NEUER Modus kommt trotzdem an
   (sein Slug steht noch nicht im Register). */
function cluster_one_default_modes(): array {
    return array(
        /* v0.5.2: "Zweier-Raum" beschrieb etwas, das das System gar nicht tut —
           es koennen beliebig viele KIs im Raum sein. Was den Modus ausmacht,
           ist die Abwesenheit eines ZWEITEN MENSCHEN. */
        'guru_meditation' => array('name' => 'Guru Meditation', 'desc' => __('One human, any number of AIs — and no second human. A quiet space for thinking out loud.', 'cluster-one-bismarck')),
        'babel_fish'      => array('name' => 'Babel Fish',      'desc' => __('A room where translation carries the conversation across languages.', 'cluster-one-bismarck')),
        /* v0.6.1: Aus "Bismarck" wird EDDINGTON. Der alte Name war der des
           Gehaeuses, in dem alle Raeume stecken (Bismarck ist der
           Kontrollbereich mit allen Reitern) — derselbe Fehler, den 0.5.4 fuer
           die Chatraeume behoben hat.

           Eddington passt doppelt: Er schlug Fusion als Energiequelle der
           Sterne vor (das Reaktor-Vokabular steht schon — Reaktorfahrer,
           Kuehlbecken), und 1919 fuhr er hin und MASS, um Einsteins Theorie zu
           pruefen. Die Ethik der Schnitt-Suite in einer Person.

           DER SLUG BLEIBT 'bismarck'. Raeume speichern den Slug, nicht den
           Anzeigenamen — ein neuer Slug haette allen bestehenden Raeumen ihren
           Modus genommen. */
        'bismarck'        => array('name' => 'Eddington',       'desc' => __('A structured working room — rules, witnesses, results.', 'cluster-one-bismarck')),
        /* v0.6.1: NEMO — der gewoehnliche Raum. Zuvor trugen solche Raeume gar
           nichts, und eine leere Spalte sieht aus wie ein Fehler.

           Christians Regel dabei: kein Modus, der eine besondere Arbeitsweise
           VORTAEUSCHT. Nemo taeuscht nichts vor — er sagt, dass es keine gibt.
           Latein fuer "niemand", die Uebersetzung von Odysseus' Outis. */
        'nemo'            => array('name' => 'Nemo',            'desc' => __('An ordinary room. No particular way of working — what happens here is up to the people in it.', 'cluster-one-bismarck')),
        'night_flight'    => array('name' => 'Night-Flight',    'desc' => __('Humans only, after hours. (The technical humans-only switch lives in the room configuration — this label only describes.)', 'cluster-one-bismarck')),
        /* v0.5.2: Drei Betriebsarten, die es laengst GAB, aber nicht im
           Vokabular standen. Christians Befund an den echten Knoten: 'Wiki-01'
           trug faelschlich "bismarck", zwei Raeume gar nichts. Ein Raum, der
           seinen Modus nicht kennt, kann ihn auch nicht einhalten — und der
           SysOp konnte ihn nicht einmal auswaehlen. */
        'wiki'            => array('name' => 'Wiki',            'desc' => __('A text many hands write. The topic keeps its history: every change files the previous version below, and the talking happens underneath it.', 'cluster-one-bismarck')),
        /* v0.9.1: TERMINAL IST KEINE WAHL MEHR, SONDERN EINE TATSACHE.

           Christian wollte das Wort retten, und es ist das genaueste im ganzen
           Haus: Ein Terminal ist die Konsole, an der man arbeitet — die Daten
           liegen woanders und gehoeren jemand anderem. Genau so ist es hier:
           Der Chat ist die Konsole, der Thread der Speicher, und der Speicher
           hat einen Eigentuemer, an den niemand sonst herankommt.

           ANTEROOM IST DAMIT ENTFALLEN — es war dasselbe. Die Beschreibung von
           Terminal sagte woertlich "der Vorraum zu einem Tagebuch"; zwei Modi
           fuer eine Sache, und einer davon hiess wie der andere.

           Der Slug bleibt in dieser Liste, damit bestehende Raeume ihr Etikett
           behalten. Neu waehlen kann man ihn nicht mehr (siehe
           cluster_one_modes_selectable) — ein Raum IST Terminal, weil er an
           einem Tagebuch haengt, nicht weil jemand es angekreuzt hat. Waere es
           waehlbar, koennte ein Raum ohne Tagebuch so heissen und ein echter
           Tagebuch-Chat anders. Genau daran ist der Riegel aus 0.8.7
           gescheitert. */
        'terminal'        => array('name' => 'Terminal',        'desc' => __('The console next to a diary: you work here, the diary itself belongs to whoever keeps it. What is written here stays here; whoever wants to share something from it carries it in themselves. A room is a terminal because it is linked to a diary — this is not something you pick.', 'cluster-one-bismarck')),
    );
}

function cluster_one_get_modes(): array {
    $a = get_option('cluster_one_modes', array());
    return is_array($a) ? $a : array();
}

function cluster_one_save_modes(array $a): void {
    update_option('cluster_one_modes', $a, false);
}

function cluster_one_mode_label(string $slug): string {
    if ($slug === '') {
        return '';
    }
    $modes = cluster_one_get_modes();
    if (isset($modes[$slug])) {
        return (string) ($modes[$slug]['name'] ?? $slug);
    }
    /* Bestandsschutz-Anzeige: gelöschter/unbekannter Modus — der Raum
       behält seinen gespeicherten Wert, markiert (tz-Muster). */
    return $slug . ' (' . __('deleted mode', 'cluster-one-bismarck') . ')';
}


function cluster_one_room_babel_state(array $room): array {
    $none = array('active' => false, 'pair' => array());
    if ((string) ($room['translator'] ?? '') === '' || (string) ($room['group_visibility'] ?? '') !== 'named') {
        return $none;
    }
    if (cluster_one_container_is_archived($room)) {
        return $none;
    }
    $ai_uids = cluster_one_ai_identity_user_ids();
    $humans  = array();
    foreach ((array) ($room['visible_to'] ?? array()) as $vk) {
        $u = (int) str_replace('wp_', '', (string) $vk);
        if ($u > 0 && !in_array($u, $ai_uids, true)) {
            $humans[] = $u;
        }
    }
    $sysop = cluster_one_main_admin_user_id();
    if (count($humans) !== 2 || !in_array($sysop, $humans, true)) {
        return $none;
    }
    $l0 = cluster_one_user_lang($humans[0]);
    $l1 = cluster_one_user_lang($humans[1]);
    if ($l0 === '' || $l1 === '' || strcasecmp($l0, $l1) === 0) {
        return $none; // gleiche Sprache: der Fisch schweigt von selbst
    }
    return array('active' => true, 'pair' => $humans);
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

/* HIERHER UMGEZOGEN (0.9.80): Liste aller Modus-Namen. Modi sind Modi. */
/* v0.9.1: WELCHE MODI MAN WAEHLEN KANN — und welche sich ergeben.

   Christians Aufraeum-Runde: Vier der neun Modi sagten etwas, das ein anderes
   Feld schon sagt.

     Terminal  — der Raum haengt an einem Tagebuch. TATSACHE, nicht Wahl.
     Wiki      — steht am THEMA, nicht am Raum.
     Babel Fish— sagt "hier wird uebersetzt"; daneben steht das Feld Uebersetzer.
     (Anteroom — war dasselbe wie Terminal und ist ganz entfallen.)

   Sie bleiben als Etikett bestehen, damit vorhandene Raeume ihren Namen
   behalten und ein Fremder auf einen Blick sieht, wo er ist. Waehlbar sind sie
   nicht mehr: Ein waehlbares Etikett kann falsch gesetzt werden, und dann
   heisst ein Raum Wiki, der keins ist.

   Uebrig zur Wahl bleiben die fuenf, die wirklich eine ARBEITSWEISE
   beschreiben: Guru Meditation, Eddington, Night-Flight, Popper, Nemo. */
function cluster_one_modes_derived(): array {
    /* v0.9.2: ALLE. Der Modus ist kein Feld mehr, sondern eine Anzeige. */
    return array_keys(cluster_one_get_modes());
}


/* v1.9.13 — DIE ZEILE AUS DER NAMENSTABELLE, JETZT IM ORGAN.

   Der Steckbrief eines MODUS. Die Modi wohnen hier —
   cluster_one_default_modes() nennt ihn in :267.

   JEDES ORGAN NENNT DAS SEINE SELBST. Dieselbe Regel und derselbe Weg wie
   Kleinrocks Zeile seit 0.9.66: der Text zieht dorthin, wo beide Haeuser ihn
   haben, statt in eine zweite Kopie. Er steht in der Namenstabelle an
   derselben Stelle wie immer — nur nicht mehr DORT geschrieben.

   feynman.php holt ihn ueber cluster_one_steckbrief_von(), und der Sammler ist
   geriegelt: wer nicht da ist, liefert nichts. Deshalb faellt die Tabelle
   nicht, wenn dieses Organ fehlt — sie wird um eine Zeile kuerzer.

   Spalten: Name · Person · Lebensdaten · warum dieser Name · was er NICHT
   meint · Langtext. Namen werden nicht uebersetzt, Begruendungen schon. */
function cluster_one_popper_steckbrief(): array {
    return array('Popper', 'Karl Popper', '1902–1994',
              __('The room where agreement is not the goal. A thesis is worth something only once someone has seriously tried to knock it down.', 'cluster-one-bismarck'),
              '',
              __('Karl Popper held that a claim earns its standing not by being confirmed but by surviving the attempt to refute it. This room is named for that: bring an idea and let the others try to knock it down. The mode is a label — it does not make an AI sharper. What is wanted here belongs in the preamble as well.', 'cluster-one-bismarck'));
}

}
