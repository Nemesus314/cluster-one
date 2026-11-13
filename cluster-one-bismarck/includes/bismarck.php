<?php
/**
 * Cluster One — Modul bismarck (DER SCHNITT, v0.0.100 -> Module).
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

   OHNE $c1_bismarck_hoeher: die Vorlage aus §4 traegt ein
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
$cluster_one_bismarck_laeuft = !function_exists('cluster_one_has_room');
cluster_one_hier('bismarck', '', $cluster_one_bismarck_laeuft);

if ($cluster_one_bismarck_laeuft) {

/** Hat dieser Knoten einen Gäste-Raum (Discord ODER Matrix)? Indikator für
   Bismarck-Bereitschaft: kann er weitere Menschen versammeln? */
function cluster_one_has_room(): bool {
    return cluster_one_discord_url() !== '' || cluster_one_matrix_url() !== '';
}


add_action('admin_post_cluster_one_mode_save', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_mode_save');
    $slug = isset($_POST['slug']) ? sanitize_key(wp_unslash($_POST['slug'])) : '';
    $name = isset($_POST['mode_name']) ? trim(sanitize_text_field(wp_unslash($_POST['mode_name']))) : '';
    $desc = isset($_POST['mode_desc']) ? trim(sanitize_text_field(wp_unslash($_POST['mode_desc']))) : '';
    if ($name !== '') {
        $modes = cluster_one_get_modes();
        if ($slug === '') {
            // Neuer Modus: Slug einmalig aus dem Namen, danach für immer fix
            // (Ausschluss-Rollen-Muster — Umbenennen ändert nur das Label).
            $slug = sanitize_key(str_replace(' ', '_', $name));
            if ($slug === '' || isset($modes[$slug])) {
                $slug = ($slug === '' ? 'mode' : $slug) . '_' . substr(bin2hex(random_bytes(3)), 0, 4);
            }
            // Selbst angelegte Slugs wandern ins Saat-Register: sollte eine
            // spätere Plugin-Version zufällig denselben Slug ausliefern,
            // wird der eigene Eintrag nie überpflanzt.
            $seeded = (array) get_option('cluster_one_modes_seeded', array());
            $seeded[] = $slug;
            update_option('cluster_one_modes_seeded', array_values(array_unique($seeded)), false);
    /* v0.5.2: Der Guru-Text war FALSCH ("stiller Zweier-Raum") — es koennen
       beliebig viele KIs im Raum sein. Das Saat-Register haette ihn nie
       korrigiert, weil der Slug laengst gesaet ist. Einmalige Korrektur, und
       NUR wenn der alte Wortlaut noch unveraendert dasteht: Wer den Text
       selbst umgeschrieben hat, behaelt seinen. Eine Beschreibung, die etwas
       anderes sagt als das System tut, ist schlimmer als keine. */
    /* v0.6.1: Aus "Bismarck" wird "Eddington" — der Slug bleibt, nur der
       Anzeigename wandert. Das Saat-Register haette ihn nie angefasst, weil
       der Slug laengst gesaet ist. Wie beim Guru-Text: NUR wo der alte
       Wortlaut unveraendert dasteht. Wer ihn selbst umbenannt hat, behaelt
       seinen Namen — ein Knoten ist niemandes Eigentum ausser dem seines
       SysOps. */
        }
        if (isset($modes[$slug]) || $name !== '') {
            $modes[$slug] = array('name' => $name, 'desc' => $desc);
            cluster_one_save_modes($modes);
        }
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_modes&mode_saved=1'));
    exit;
});

add_action('admin_post_cluster_one_mode_delete', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_mode_delete');
    $slug  = isset($_POST['slug']) ? sanitize_key(wp_unslash($_POST['slug'])) : '';
    $modes = cluster_one_get_modes();
    if ($slug !== '' && isset($modes[$slug])) {
        unset($modes[$slug]); // Räume behalten den Slug — Anzeige markiert (Bestandsschutz); das Saat-Register verhindert Wiederauferstehung
        cluster_one_save_modes($modes);
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_modes&mode_saved=1'));
    exit;
});

/* v0.0.72: Der Tagebuch-Lese-Umschalter (v0.0.60) ist abgeschafft —
   ersetzt durch das Transparenz-Modell (cluster_one_diary_can_read /
   cluster_one_diary_read_witness). */

/* ===========================================================================
   MEINE SEITEN — RUNDBRIEF 21 §2.
   ---------------------------------------------------------------------------
   Gemeldet wird, WELCHE SEITEN ZU BISMARCK GEHOEREN, damit ihre Ueberschrift
   „Cluster One – Bismarck" heisst und nicht „Cluster One".

   ---------------------------------------------------------------------------
   ZUERST EINE BERICHTIGUNG AN DER AUFGABE, UND SIE IST GEMESSEN.

   §2 sagt: „Ihr habt eure Seitenliste schon: sie steht in eurer
   Reiterleiste." Fuer Kleinrock stimmt das. FUER MICH NICHT.

       cluster_one_render_bismarck_tabs() fuehrt FUENF Reiter.
       VIER davon sind ?tab= auf EINER Seite:

           Projects · Labels · Winsor · Structure
               -> alle admin.php?page=cluster_one_bismarck&tab=…

       Der fuenfte zeigt auf eine FREMDE Seite (cluster_one_gregor).

   Meine Reiterleiste nennt also GENAU EINEN eigenen Seiten-Slug. Meine
   Seiten sind FUENF. Wer hier die Reiterleiste abfragt, meldet eine von
   fuenf — und die anderen vier fielen weiter auf „Cluster One", also genau
   auf Christians Befund.

       Kleinrocks Reiter sind Seiten. Meine sind Reiter.
       Dieselbe Leiste, zwei Bauarten — und eine Regel, die von der
       einen abgelesen ist, trifft die andere nicht.

   ---------------------------------------------------------------------------
   WOHER DIE LISTE DANN KOMMT: AUS menue.php, GELESEN STATT GETIPPT.

   Der Boden schreibt seit jeher auf, welches ORGAN eine Seite mitbringt:

       cluster_one_menue_wenn('<organ>', function () {
           add_submenu_page(null, …, '<slug>', '<render>');
       });

   Ausgezaehlt ueber menue.php, alle 24 Anmeldungen, nach Organ (18.09.):

       MEINE ORGANE                            SLUG
       bismarck-seiten   menue.php:521         cluster_one_bismarck
       bismarck-seiten   menue.php:569         cluster_one_bismarck_settings
       bismarck-seiten   menue.php:813         cluster_one_room
       platon            menue.php:824         cluster_one_forum_thread
       modi              menue.php:771         cluster_one_modes

       NICHT MEINE   kleinrock 8 · kranz 5 · feynman 4 · faust 2

   ---------------------------------------------------------------------------
   UND DAS IST DIE EIGENTLICHE MELDUNG AN DIE WERKBANK:

   Diese Zuordnung steht BEREITS EINMAL IM BODEN, vollstaendig und je Seite.
   Die Form aus §2 laesst fuenf Haeuser abschreiben, was menue.php schon
   weiss — und warnt im selben Absatz davor, eine zweite Liste zu tippen.

       Ein Sammler, der jedes Haus nach etwas fragt, das der Boden
       aufgeschrieben hat, erzeugt die zweite Wahrheit, vor der er warnt.

   Ich melde trotzdem, und zwar heute und in der bestellten Form: der Zug
   faehrt, und ein Vorschlag ist kein Grund, eine Runde stehenzubleiben.
   Aber die Liste unten ist ABGELESEN und nicht ausgedacht, und daneben
   steht, woraus. Wer sie nachziehen will, zaehlt menue.php neu aus.

   ---------------------------------------------------------------------------
   EINE WAHRHEIT, ZWEI LESER: cluster_one_bismarck_seiten() ist die einzige
   Stelle, an der die Namen stehen. Der Sammler unten liest sie, und wer
   spaeter eine zweite Frage an dieselbe Liste hat, liest sie auch.  (Weg D)
*/
function cluster_one_bismarck_seiten(): array {
    /* Die fuenf aus der Tabelle oben — Seiten, die MEINE Organe mitbringen. */
    $c1_seiten = array(
        'cluster_one_bismarck',           /* bismarck-seiten */
        /* 28.10.2026 (Neo), Bismarck 0.2.60 — HIER STAND BIS 0.2.59:
               'cluster_one_bismarck_settings',  // bismarck-seiten
           Die KI-Kennungen sind Knoten-Sache, nicht meine. Nemesus' Pruefung an
           Pacioli (28.10.): ohne Pacioli „Anbieter → ein Konto, knotenweit, nur
           vom SysOp gesetzt", mit Pacioli benutzt der Knoten eine KI erst, wenn
           sie dort einem Anbieter zugeordnet ist. Die Seite steht jetzt in der
           Knoten-Reihe (kern.php, cluster_one_kranz_reiter); meine Menschen-
           Tabelle und Raum-Einstellungen zeichne ich dort weiter ueber die
           Haken 'menschen' und 'einstellungen' — das aendert sich nicht. */
        'cluster_one_room',               /* bismarck-seiten */
        'cluster_one_forum_thread',       /* platon          */
        'cluster_one_modes',              /* modi            */
    );

    /* 19.10.2026 (Neo) — DREI SEITEN AUS DER KRANZ-LEISTE, Christians
       Zuordnung vom 18.10.: Persoenlicher Bereich (die Briefe), Chronik (die
       Narben der Raeume und Themen) und Setup (die Blaupause legt Raeume an)
       sind Bismarcks Sache. Der Boden zeichnet sie weiter; Bismarck fuehrt
       sie und zeigt sie in seiner Leiste. Geriegelt an der Hand, die zeichnet:
       ohne kranz.php bzw. kern.php gibt es die Seite nicht. */
    /* 28.10.2026 (Neo), Bismarck 0.2.60: aus den DREI sind ZWEI geworden. Hier
       stand bis 0.2.59 auch
           'cluster_one_chronik'       => 'cluster_one_render_chronik_page',
       Die Chronik sammelt die Eintraege ALLER Produkte (Kalender, Crockfords
       Site-Kontext, Blaupause, Knoten-Reset) — sie gehoert dem Knoten, nicht den
       Raeumen. Nemesus' Zuordnung vom 28.10.; sie steht in der Knoten-Reihe. */
    /* 30.10.2026 (Neo), Bismarck 0.2.66: aus den ZWEI ist EINE geworden. Hier stand bis 0.2.65
           'cluster_one_personal_area' => 'cluster_one_render_personal_area_page',
       Der Persoenliche Bereich ist der Platz des MENSCHEN am Knoten (Mein Speicher, Briefe,
       Merkliste) – nicht Bismarcks. Solange Bismarck ihn beanspruchte, stand er nur in Bismarcks
       Leiste, und von Pacioli aus fuehrte kein Reiter dorthin (Nemesus, 30.10., auf der Main). Jetzt
       steht er in der Knoten-Reihe, hinter den KI-Kennungen, in jedem Haus hinter „Knoten". Meine
       Abschnitte (Briefe, Merkliste) fuellen den Platz 'persoenlich' weiter wie bisher. */
    foreach (array(
        'cluster_one_blueprint'     => 'cluster_one_render_blueprint_page',
    ) as $c1_slug => $c1_fn) {
        if (function_exists($c1_fn)) {
            $c1_seiten[] = $c1_slug;
        }
    }

    /* DIE GETEILTE SEITE. RUNDBRIEF 21 §4/§5: der Kalender gehoert Kleinrock
       UND Bismarck, und zwei Anspruechen antwortet der Boden mit der Lage
       „geteilt". Sie ist NICHT meine Seite — sie liegt in faust.php — aber
       mein Haus fuehrt zu ihr, und darum beanspruche ich sie mit.

       DERSELBE RIEGEL WIE DER REITER, und das ist der Grund, warum er hier
       noch einmal steht und nicht weggelassen wird: liegt faust.php nicht
       daneben, gibt es die Seite nicht — und ein Anspruch auf eine Seite,
       die es nicht gibt, ist dasselbe wie ein Reiter in eine Wand.

           Wer eine Seite beansprucht, zu der sein eigenes Haus keinen
           Weg hat, meldet einen Besitz statt eines Zugangs. */
    if (function_exists('cluster_one_render_gregor_page')) {
        $c1_seiten[] = 'cluster_one_gregor';
    }

    /* UND DIE ZWEITE FAUST-SEITE — die Ereignisseite, menue.php:669.
       Am 18.09. habe ich gefragt, ob sie dazugehoert, und sie NICHT
       beansprucht, weil mein Reiter nicht auf sie zeigt. Die Antwort ist ja.

       Der Grund steht im Gebrauch und nicht in der Zusage: der Kalender
       fuehrt auf sie. Wer ueber meinen Reiter in den Kalender geht und dort
       einen Termin oeffnet, steht danach auf einer Seite, die ohne diesen
       Anspruch „Cluster One" hiesse und keine Leiste zurueck traegt.

           Ich habe sie nicht beansprucht, weil ich sie nicht VERLINKE.
           Beanspruchen muss ich aber, WOHIN MAN VON MIR AUS KOMMT.
           Ein Reiter ist der erste Schritt und nicht der letzte.

       EIGENER RIEGEL, nicht der des Kalenders: es sind zwei Seiten und zwei
       Render-Funktionen (faust.php:298 und :503). Dass beide heute in
       derselben Datei stehen, ist wahr und ist kein Grund, nach der einen zu
       fragen und die andere zu meinen. */
    if (function_exists('cluster_one_render_gregor_event_page')) {
        $c1_seiten[] = 'cluster_one_gregor_event';
    }

    return $c1_seiten;
}

/** DIE FAUST-SEITEN, auf denen meine Leiste stehen kann — und auf denen der
   Kalender-Reiter aktiv ist. EINE Stelle, zwei Leser: die Meldung oben
   beansprucht sie, der Reiter unten faerbt sich auf ihnen.  (Weg D) */
function cluster_one_bismarck_kalenderseiten(): array {
    return array('cluster_one_gregor', 'cluster_one_gregor_event');
}

/* DIE MELDUNG. Angehaengt, nicht zugewiesen — R-SAMMLER-1, und hier ist der
   zweite Melder der Regelfall und kein Unfall: auf der Kalenderseite meldet
   sich Kleinrock neben mir, und wer zuweist, enteignet einen von beiden still.

   ANGEMELDET WIRD DER WIRT, erfragt statt getippt, genau wie beim Raeumer
   und beim Registerblock. Und die Pruefung steht im RUMPF und nicht am Tor:
   die Closure laeuft, wenn der Boden fragt, und bis dahin ist der Kasper da,
   gleichgueltig in welcher Reihenfolge geladen wurde. (Gemessen am 18.09.,
   zeuge-raeumer-echter-boden.php B7 — dieselbe Stelle, dieselbe Antwort.) */
add_filter('cluster_one_seite_ansprueche', function ($c1_a, $c1_seite) {
    if (!is_array($c1_a)) { return $c1_a; }
    if (!function_exists('cluster_one_kasper_wirt')) { return $c1_a; }
    if (!in_array($c1_seite, cluster_one_bismarck_seiten(), true)) { return $c1_a; }

    $c1_wirt = cluster_one_kasper_wirt(__FILE__);
    /* LEER HEISST „NICHT FESTSTELLBAR" UND NICHT „KEINER". Ein leerer Wirt
       wuerde vom Boden verworfen UND gemeldet — eine Warnung ueber einen
       Anspruch, den ich gar nicht erheben wollte. Also schweige ich lieber. */
    if ($c1_wirt === '') { return $c1_a; }

    $c1_a[] = $c1_wirt;
    return $c1_a;
}, 10, 2);

/* 19.10.2026 (Neo) — MEINE LEISTE AUF MEINEN KRANZ-SEITEN.
   Der Boden fragt beim Zeichnen seiner Leiste zuerst, ob das Produkt, das
   die Seite beansprucht, eine eigene hat (kern.php, Filter
   'cluster_one_reiterleiste'). Fuer die fuenf Seiten, die aus der Kranz-Leiste
   zu mir gezogen sind, antworte ich mit meiner Leiste; die Projektseiten
   zeichnen sie ohnehin selbst, und die Kalenderseiten haben ihren eigenen
   Weg (cluster_one_faust_leisten). Eine Seite, eine Leiste. */
add_filter('cluster_one_reiterleiste', function ($c1_fn, $c1_seite) {
    if ($c1_fn !== '' || !is_string($c1_seite)) { return $c1_fn; }
    /* 28.10.2026 (Neo), 0.2.60: bis 0.2.59 standen hier fuenf —
           'cluster_one_bismarck_settings', …, 'cluster_one_chronik', …
       KI-Kennungen und Chronik sind Knoten-Sache (siehe cluster_one_bismarck_seiten). */
    /* 30.10.2026 (Neo), 0.2.66: 'cluster_one_personal_area' ist raus – Knoten-Sache (siehe cluster_one_bismarck_seiten). */
    $c1_meine = array('cluster_one_modes', 'cluster_one_blueprint');
    if (!in_array($c1_seite, $c1_meine, true)) { return $c1_fn; }
    if (!in_array($c1_seite, cluster_one_bismarck_seiten(), true)) { return $c1_fn; }
    return 'cluster_one_render_bismarck_tabs';
}, 10, 2);

/* 19.10.2026 (Neo) — MEINE KNOTEN-EINSTELLUNGEN AUF MEINER SEITE.
   Discord/Matrix, Collective und das Erinnerungs-Intervall standen bis heute
   auf der Boden-Seite „Einstellungen" (kranz.php, Abschnitte „This node" und
   „Collective"). Alle drei gehoeren den Raeumen. Der Boden ruft auf der Seite
   der KI-Kennungen do_action('cluster_one_abschnitt', 'einstellungen'); hier
   ist die Antwort. Die Optionsschluessel sind dieselben wie vorher; der
   Reiniger in kranz.php liest aus `_felder`, worueber dieses Formular spricht,
   und laesst alles andere stehen. */
add_action('cluster_one_abschnitt', function ($c1_wo) {
    if ((string) $c1_wo === 'einstellungen') { cluster_one_bismarck_seite_einstellungen(); }
});

function cluster_one_bismarck_seite_einstellungen(): void {
    if (!function_exists('cluster_one_settings_felder_feld')) { return; }
    $c1_opts = get_option('cluster_one_settings', array());
    $c1_opts = is_array($c1_opts) ? $c1_opts : array();
    $c1_konst = (defined('CLUSTER_ONE_DISCORD_URL') && constant('CLUSTER_ONE_DISCORD_URL') !== '') || defined('CLUSTER_ONE_MATRIX_URL');
    $c1_collective = (int) ($c1_opts['collective_user_id'] ?? 0);
    /* Bewusst UNGEFILTERT — Collective soll die Ausschluss-Rolle tragen,
       wuerde also aus der sonst ueblichen Liste herausfallen. */
    $c1_users = get_users(array('fields' => array('ID', 'display_name')));
    ?>
        <hr style="margin:2.5em 0;">
        <h2><?php esc_html_e('Rooms', 'cluster-one-bismarck'); ?></h2>
        <form method="post" action="options.php" style="max-width:760px;">
            <?php settings_fields('cluster_one_settings_group'); ?>
            <?php cluster_one_settings_felder_feld(array('discord_url', 'matrix_url', 'collective_user_id', 'fs_summary_interval')); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Server room (Discord / Matrix)', 'cluster-one-bismarck'); ?> <span style="opacity:.6;font-weight:400;"><?php esc_html_e('(optional)', 'cluster-one-bismarck'); ?></span></th>
                    <td>
                        <p style="margin:.2em 0;">
                            <label for="c1_discord" style="display:inline-block;width:70px;">Discord</label>
                            <input name="cluster_one_settings[discord_url]" id="c1_discord" type="url" class="regular-text"
                                   value="<?php echo esc_attr($c1_opts['discord_url'] ?? ''); ?>"
                                   placeholder="https://discord.gg/your-invite-code">
                        </p>
                        <p style="margin:.2em 0;">
                            <label for="c1_matrix" style="display:inline-block;width:70px;">Matrix</label>
                            <input name="cluster_one_settings[matrix_url]" id="c1_matrix" type="url" class="regular-text"
                                   value="<?php echo esc_attr($c1_opts['matrix_url'] ?? ''); ?>"
                                   placeholder="https://matrix.to/#/#your-room:server.tld">
                        </p>
                        <p class="description">
                            <?php esc_html_e('Both optional. Not a meeting place for strangers: it only says that a server with room capability exists, so a room just for the people on this node can be built later. Stays local — peers learn that a room can exist, never the address.', 'cluster-one-bismarck'); ?>
                            <?php if ($c1_konst) : ?><strong><?php esc_html_e('Currently overridden by a constant.', 'cluster-one-bismarck'); ?></strong><?php endif; ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="c1_collective"><?php esc_html_e('Collective', 'cluster-one-bismarck'); ?></label></th>
                    <td>
                        <select name="cluster_one_settings[collective_user_id]" id="c1_collective">
                            <option value="0">— <?php esc_html_e('none', 'cluster-one-bismarck'); ?> —</option>
                            <?php foreach ($c1_users as $c1_u) : ?>
                                <option value="<?php echo esc_attr((string) $c1_u->ID); ?>" <?php selected($c1_collective, (int) $c1_u->ID); ?>><?php echo esc_html($c1_u->display_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e('The WP user shown as author of wiki threads. Give this account the Excluded role (set in the humans table above) — it has nothing to do with rooms. WordPress still records the real author internally; this only changes the byline.', 'cluster-one-bismarck'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="c1_fs_interval"><?php esc_html_e('Summary reminder every … rounds', 'cluster-one-bismarck'); ?></label></th>
                    <td>
                        <input name="cluster_one_settings[fs_summary_interval]" id="c1_fs_interval" type="number" min="0" max="999" style="width:6em;" value="<?php echo (int) ($c1_opts['fs_summary_interval'] ?? 10); ?>">
                        <p class="description"><?php esc_html_e('The chat reminds you to condense rounds into the linked thread. 10 is the guideline; 0 switches the reminder off.', 'cluster-one-bismarck'); ?></p>
                    </td>
                </tr>
            </table>
            <?php /* translators: %s: Link zur Seite Struktur. */ ?>
            <p class="description"><?php echo wp_kses_post(sprintf(__('Areas for forums and rooms are managed under <a href="%s">Structure</a>.', 'cluster-one-bismarck'), esc_url(admin_url('admin.php?page=cluster_one_bismarck&tab=structure')))); ?></p>
            <?php if (cluster_one_is_sysop()) : ?>
                <?php submit_button(__('Save', 'cluster-one-bismarck')); ?>
            <?php endif; ?>
        </form>
<?php
}

/* ===========================================================================
   MEINE LEISTE AUF DER KALENDERSEITE — faust.php v1.0.4, 19.09.2026.
   ---------------------------------------------------------------------------
   EINE AKTION, KEIN FILTER, und der Unterschied ist die ganze Form:

       ein Filter SAMMELT   — jeder gibt etwas zurueck, der Boden entscheidet
       eine Aktion ZEICHNET — jeder schreibt selbst, sofort, in die Seite

   Deshalb gibt es hier nichts zurueckzugeben und nichts anzuhaengen. Es gibt
   nur: bin ich gemeint, ja oder nein.

       Ein Sammler, der nichts sammelt, waere ein Filter mit Nebenwirkung.
       Ein Zeichner, der etwas zurueckgibt, waere eine Aktion, die sich fuer
       einen Filter haelt. Der Name sagt, welche Frage gestellt wurde.

   ---------------------------------------------------------------------------
   DIE PRUEFUNG IST EINE VERGLEICHUNG UND KEINE ANNAHME. faust.php ruft
   do_action() EINMAL JE BEANSPRUCHENDEM HAUS, in der Reihenfolge nach 'nr'.
   Bei zwei Haeusern laeuft diese Closure ZWEIMAL — einmal mit Kleinrocks
   Wirt, einmal mit meinem.

       Wer nicht vergleicht, zeichnet auch, wenn der Nachbar gemeint war —
       und der Nachbar zeichnet auch, wenn ich gemeint war. Aus einer
       geteilten Seite wuerden vier Leisten statt zwei.

   ---------------------------------------------------------------------------
   'kalender' IST KEIN REITER-SLUG, SONDERN EIN ZUSTAND. Meine Leiste kennt
   vier Reiter auf EINER Seite (?tab=) und einen auf einer FREMDEN. Der Wert,
   den ich hier uebergebe, faerbt den fuenften. Es wandert nichts: kein Slug,
   keine Adresse, keine Option — nur die Auskunft, wo der Leser gerade steht.
                                                      (VERTRAG 29, Sorte V)

   Und er faerbt AUCH auf der Ereignisseite, nicht nur auf dem Kalender.
   Beide stehen in cluster_one_bismarck_kalenderseiten(), einmal.

   ---------------------------------------------------------------------------
   28.09.2026 — CHRISTIANS ENTSCHEIDUNG: AUF DEN KALENDERSEITEN ZEICHNE ICH
   NICHT. Die vier Reiter Projekte · Labels · Winsor · Struktur gehoeren mir,
   aber nicht in den Kalender.

       Eine Reiterleiste sagt nicht, wem die Seite gehoert — sie sagt, wo der
       Leser steht, und das ist immer genau ein Ort.

   WAS SICH NICHT AENDERT, und das ist die zweite Haelfte der Entscheidung:
   MEIN ANSPRUCH BLEIBT. Ich trage die Seite weiter, ich zeichne nur nicht
   mehr hinein. Aus dem Anspruch nimmt die Kalenderseite ihre UEBERSCHRIFT
   („Cluster One" statt „Cluster One – Kleinrock") und ihren Satz — der
   Kalendersatz schweigt genau dann, wenn ZWEI Haeuser tragen. Zoege ich den
   Anspruch mit zurueck, behauptete die Seite, sie sei halb ausgestattet, und
   das waere falsch: ich bin da.

       Der Fehler war nie ein schlampiges Byte. Er war, dass EINE Liste zwei
       Fragen beantwortet hat: „wer traegt die Seite" — beide — und „in wessen
       Zimmer steht der Leser" — genau einer. Der Anspruch beantwortet die
       erste, die Leiste die zweite. Ab hier tun sie es getrennt.

   cluster_one_bismarck_kalenderseiten() BLEIBT STEHEN und wechselt die
   Aufgabe: sie war die Hervorhebung, sie ist jetzt der RIEGEL. Keine zweite
   Liste — dieselbe Liste, eine Frage weiter.

   UND DIE AKTION FEUERT WEITER FUER MICH. faust.php ruft, weil ich
   beanspruche; der Riegel schickt den Ruf zurueck. Wer das nicht weiss und
   nur sieht, dass nichts erscheint, sucht den Fehler beim Rufer.

   ---------------------------------------------------------------------------
   DER RIEGEL AUF cluster_one_render_tabs() FEHLT HIER ABSICHTLICH: sie steht
   im Boden, und wenn sie fehlt, fehlt meine eigene Seite genauso. Was hier
   geriegelt wird, ist der KASPER — ohne ihn kann ich nicht wissen, ob ich
   gemeint bin, und dann zeichne ich lieber nichts als vielleicht.

       Ein Haus, das im Zweifel zeichnet, macht aus einer offenen Frage
       eine Behauptung auf dem Schirm. */
add_action('cluster_one_leiste', function ($c1_w, $c1_s) {
    if (!function_exists('cluster_one_kasper_wirt')) { return; }
    if ($c1_w !== cluster_one_kasper_wirt(__FILE__)) { return; }

    /* 28.09.2026, Christians Entscheidung: auf den Kalenderseiten
       zeichne ich NICHT. Eine Reiterleiste sagt nicht, wem die Seite
       gehoert — sie sagt, wo der Leser steht, und das ist immer genau
       ein Ort. Mein ANSPRUCH bleibt: ich trage die Seite weiter, ich
       zeichne nur nicht mehr hinein. */
    if (in_array($c1_s, cluster_one_bismarck_kalenderseiten(), true)) {
        /* 28.10.2026 (Neo), Bismarck 0.2.60 — HIER STAND BIS 0.2.59: return;
           Christians Entscheidung vom 28.09. („auf den Kalenderseiten zeichne ich
           NICHT") galt einer Welt, in der die Aktion fuer JEDES tragende Haus
           feuerte und zwei Leisten uebereinander standen. Seit Boden 1.18.0 ruft
           der Kalender nur das Haus, von dem der Leser KAM (?von=,
           cluster_one_herkunft) — Nemesus' Entscheidung vom 28.10. Wer ueber
           meinen Kalender-Reiter kommt, steht in meinem Zimmer; dann zeichne
           ich, mit dem Kalender-Reiter als dem aktiven. Der Satz von damals
           bleibt wahr: eine Leiste sagt, wo der Leser steht, und das ist genau
           ein Ort — jetzt der, aus dem er gekommen ist. */
        cluster_one_render_bismarck_tabs('kalender');
        return;
    }

    /* 28.10.2026 (Neo), 0.2.60: auf den Knoten-Seiten ruft der Boden mit dem
       Slug der Seite (kern.php, cluster_one_render_kranz_tabs); keiner meiner
       Reiter traegt ihn, der Boden faerbt dort den Reiter „Knoten". */
    cluster_one_render_bismarck_tabs((string) $c1_s);
}, 10, 2);


/** Bismarck-Reiter: Räume und (seit v0.0.47) Forum/Platon. Einstellungen
   sind seit v0.0.40 nach Kranz gewandert. */
function cluster_one_render_bismarck_tabs(string $current): void {
    $base = admin_url('admin.php?page=cluster_one_bismarck');
    cluster_one_render_tabs(array_values(array_filter([
        // v0.0.69: Reiter heißt „Chats" (Entscheidung SysOp 15.07.) — Slug 'rooms'
        // bleibt, Lesezeichen brechen nicht; die Detail-Terminologie (Raum-
        // Konfiguration etc.) folgt erst nach ausdrücklicher SysOp-Entscheidung
        // als eigener Terminologie-Durchgang, nicht hier nebenbei.
/* v0.9.43: EIN REITER STATT ZWEI. Christian am 20.08.2026:
           "Wir brauchen keine 2 Tabs namens Chat und Forum. Wir brauchen einen
           Tab mit der Aufschrift: Projekte."

           Die alten Slugs 'rooms' und 'forum' bleiben gueltig und zeigen beide
           hierher — Lesezeichen und Ruecksprung-URLs brechen nicht. Dasselbe
           Verfahren wie 0.0.66 und 0.0.69, als die Reiter ihre Namen aendern
           mussten, ohne ihre Adressen zu aendern.

           Und ein Fragezeichen statt zwei: Sokrates und Platon werden jetzt
           zusammen erklaert, weil sie zusammen ein Projekt sind. */
        ['label' => __('Projects', 'cluster-one-bismarck'), 'url' => add_query_arg('tab', 'projekte', $base), 'active' => in_array($current, array('projekte', 'rooms', 'forum'), true)],
        ['label' => __('Labels', 'cluster-one-bismarck'), 'url' => add_query_arg('tab', 'labels', $base), 'active' => $current === 'labels'],
        // v0.0.95: das Abklingbecken — nach den Projektionen, vor dem Skelett.
        ['label' => 'Winsor', 'url' => add_query_arg('tab', 'winsor', $base), 'active' => $current === 'winsor'],
        // v0.0.66: Struktur — die EINE gemeinsame Bereichs-Taxonomie für
        // Foren UND Räume (Weg a laut Übergabedokument).
        ['label' => __('Structure', 'cluster-one-bismarck'), 'url' => add_query_arg('tab', 'structure', $base), 'active' => $current === 'structure'],
        /* DER KALENDER-REITER. Christians Entscheidung vom 18.09.2026,
           ENTSCHEIDUNG-der-kalender-bekommt-einen-weg.md §1: Gregor bekommt in
           Bismarck und in Kleinrock einen Reiter. Die Seite zieht NICHT um —
           sie bleibt cluster_one_gregor in faust.php, versteckt angemeldet,
           und bekommt einen ZUGANG. Sorte V aus VERTRAG 29: es reist nichts,
           es aendert sich genau eine Sache — wer sie findet.

           GERIEGELT WIE DIE LANDKARTE, und der Riegel fragt nach der SEITE
           und nicht nach dem Paket: liegt faust.php nicht daneben, gibt es
           die Seite nicht, und ein Reiter darauf waere eine Tuer in eine Wand.

               Ein Reiter, der ein Versprechen macht, das das Haus nicht
               halten kann.                     (Kleinrock, v0.9.64)

           Die Beschriftung ist in BEIDEN Welten dieselbe. Ein Reiter, der
           seine Aufschrift nach der Nachbarschaft aendert, macht aus einer
           Ortsangabe eine Meldung. (Kleinrocks Zeile zu seinem eigenen
           Kalender-Reiter, woertlich uebernommen — dieselbe Sache, zwei
           Haeuser, eine Form.)

           ---------------------------------------------------------------
           'active' STAND HIER AUF FEST false, MIT DIESER BEGRUENDUNG:

               „diese Leiste wird nur auf cluster_one_bismarck gezeichnet.
                Wer auf dem Kalender steht, sieht diese Leiste nicht."

           DER SATZ WAR AM 18.09. WAHR UND IST ES SEIT DEM 19.09. NICHT MEHR.
           faust.php ruft seitdem cluster_one_faust_leisten(), und die zieht
           meine Leiste auf beide Kalenderseiten. Wer dort steht, sieht sie —
           und saehe fuenf Reiter, von denen keiner der ist, auf dem er steht.

               Eine Begruendung altert mit der Welt, die sie beschreibt.
               Ein fester Wert, der von einer Bedingung anderswo lebt,
               sagt nicht, dass er von ihr lebt — er sagt gar nichts.

           Deshalb steht hier jetzt eine Bedingung und keine Zahl.

           ---------------------------------------------------------------
           28.09.2026 — UND SEITDEM IST DIESE BEDINGUNG NICHT MEHR
           ERREICHBAR. Christian hat entschieden, dass meine Leiste auf den
           Kalenderseiten gar nicht mehr gezeichnet wird; der einzige Rufer,
           der je 'kalender' uebergeben hat, kehrt jetzt vorher um. Mit der
           heutigen Welt ist $current dort niemals 'kalender'.

           SIE BLEIBT TROTZDEM EINE BEDINGUNG UND WIRD KEIN false. Am 18.09.
           stand hier eine feste Zahl mit einer Begruendung, die eine Woche
           spaeter nicht mehr stimmte, und sie hat es nicht gesagt. Eine
           Bedingung, die heute nie zutrifft, sagt weiter die Wahrheit —
           ruft mich morgen wieder jemand mit 'kalender', faerbt sie den
           richtigen Reiter. Eine Zahl saehe genauso aus und waere dann
           falsch.

               Ein fester Wert ist eine Behauptung ueber die ganze Welt.
               Eine Bedingung ist eine Frage an die, die gerade da ist.

           Gefahren, nicht behauptet: T3b in probe-meine-leiste.php ruft
           cluster_one_render_bismarck_tabs('kalender') direkt und misst,
           dass genau ein Reiter faerbt — der Kalender. */
        /* 28.10.2026 (Neo), 0.2.60: der Reiter sagt dem Kalender, dass der Leser
           von MIR kommt (?von=), damit dort meine Leiste und „Cluster One –
           Bismarck" stehen (cluster_one_herkunft). Geriegelt gegen einen
           aelteren Boden: dann die nackte Adresse wie bis 0.2.59. */
        function_exists('cluster_one_render_gregor_page')
            ? ['label' => __('Calendar', 'cluster-one-bismarck'),
               'url' => (function_exists('cluster_one_mit_herkunft') && function_exists('cluster_one_kasper_wirt'))
                   ? cluster_one_mit_herkunft(admin_url('admin.php?page=cluster_one_gregor'), cluster_one_kasper_wirt(__FILE__))
                   : admin_url('admin.php?page=cluster_one_gregor'),
               'active' => $current === 'kalender']
            : null,
        /* 19.10.2026 (Neo) — DIE FUENF AUS DER KRANZ-LEISTE, Christians
           Zuordnung vom 18.10. Der Boden zeichnet die Seiten, Bismarck fuehrt
           zu ihnen. Aktiv ist ein Reiter, wenn $current sein Slug ist — so
           ruft der Boden diese Leiste ueber 'cluster_one_reiterleiste'.
           Jeder Reiter nur, wenn die Hand da ist, die seine Seite zeichnet. */
        function_exists('cluster_one_render_modes_page')
            ? ['label' => __('Modes', 'cluster-one-bismarck'),
               'url' => admin_url('admin.php?page=cluster_one_modes'),
               'active' => $current === 'cluster_one_modes']
            : null,
        /* 30.10.2026 (Neo), Bismarck 0.2.66 — DER DRITTE REITER ZIEHT IN DIE KNOTEN-REIHE.
           Hier stand bis 0.2.65:
               function_exists('cluster_one_render_personal_area_page')
                   ? ['label' => __('Personal area', 'cluster-one-bismarck'),
                      'url' => admin_url('admin.php?page=cluster_one_personal_area'),
                      'active' => $current === 'cluster_one_personal_area'] : null,
           Grund bei cluster_one_bismarck_seiten. Der Weg: „Knoten" rechts → Persoenlicher Bereich. */
        /* 28.10.2026 (Neo), Bismarck 0.2.60 — ZWEI REITER SIND IN DIE KNOTEN-REIHE
           GEZOGEN, Nemesus' Zuordnung vom 28.10. Hier standen bis 0.2.59:
               function_exists('cluster_one_render_chronik_page')
                   ? ['label' => __('Chronicle', 'cluster-one-bismarck'),
                      'url' => admin_url('admin.php?page=cluster_one_chronik'),
                      'active' => $current === 'cluster_one_chronik'] : null,
           (zwischen Persoenlicher Bereich und Setup) und als letzter
               function_exists('cluster_one_render_bismarck_settings_page')
                   ? ['label' => __('Settings', 'cluster-one-bismarck'),
                      'url' => admin_url('admin.php?page=cluster_one_bismarck_settings'),
                      'active' => $current === 'cluster_one_bismarck_settings'] : null,
           Beide Seiten gibt es weiter, mit denselben Adressen; der Weg dorthin
           ist der Reiter „Knoten" rechts (Boden 1.18.0) → Chronik / KI-Kennungen.
           Meine Raum-Einstellungen (Discord/Matrix, Collective, Erinnerung)
           stehen weiter auf der Seite der KI-Kennungen, ueber den Haken. */
        function_exists('cluster_one_render_blueprint_page')
            ? ['label' => __('Setup', 'cluster-one-bismarck'),
               'url' => admin_url('admin.php?page=cluster_one_blueprint'),
               'active' => $current === 'cluster_one_blueprint']
            : null,
    ])));
    /* v0.7.0: Das Fragezeichen zum AKTIVEN Reiter, direkt unter der Leiste.
       Nicht im Reiter selbst — ein Reiter ist ein Link, und ein aufklappbarer
       Kasten darin waere ein Klickziel im Klickziel.

       Sokrates und Platon sind Organe mit eigenen Namen, aber nur Reiter: Wer
       das Plugin oeffnet, trifft sie hier zuerst und hat keine Ahnung, warum
       sein Chat nach einem Philosophen heisst, der nichts geschrieben hat. Labels und Struktur haben
       keinen Namensgeber — ein Fragezeichen bekommen sie trotzdem, sonst fragt
       sich jemand, warum ausgerechnet dort keins ist. */
    /* v0.7.1: 'rooms' und 'forum' fehlen hier absichtlich — Christians Befund:
       Das Fragezeichen stand ueber dem Wort "Chats" statt hinter dem Namen.
       Es sitzt jetzt an der jeweiligen Ueberschrift, wo der Name steht. */
    $c1_hilfe = array(
        'labels'    => 'labels',
        'structure' => 'struktur',
    );
    if (isset($c1_hilfe[$current])) {
        /* v1.9.13 (W52) — ZWEI KLINKEN AN DERSELBEN TUER. Form aus
           kern.php:2216 (v0.9.63). cluster_one_help() wohnt in Feynman; wird
           Feynman auf die Organe aufgeteilt, stirbt dieser Ruf. Es gibt keinen
           zweiten TEXT, nur einen zweiten WEG zu demselben. */
        echo function_exists('cluster_one_help')
            ? cluster_one_help($c1_hilfe[$current]) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_help() gibt cluster_one_help_bubble() zurueck (wpautop(esc_html) im Innern) oder ''
            : cluster_one_help_eigen($c1_hilfe[$current]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_help_eigen() gibt cluster_one_help_bubble() zurueck (wpautop(esc_html) im Innern) oder ''
    }
}


/* v0.9.4: DIE MIGRATIONEN LAUFEN JETZT WIRKLICH.

   Christians Spiegel-Dump vom echten Knoten deckte es auf: Die Option
   cluster_one_migrations existierte dort GAR NICHT. Grund — alle drei
   Migrationen steckten im Handler `admin_post_cluster_one_mode_save`. Sie
   liefen also nur, wenn der SysOp in Kranz einen Modus speichert. Das hat er
   nie getan.

   Sichtbare Folgen auf seinem Knoten: Der Modus hiess weiterhin "Bismarck"
   statt "Eddington" (0.6.1 nie angekommen), und vier von sechs Bereichen
   hatten keine Beschreibung (die Reparatur aus 0.7.5 nie gelaufen).

   EINE MIGRATION IM FALSCHEN HAKEN IST KEINE MIGRATION, SONDERN EIN
   VERSPRECHEN. Sie gehoert an `admin_init`, wo sie bei jedem Aufruf des
   Backends prueft und hoechstens einmal etwas tut.

   Die Marker bleiben unveraendert — auf Knoten, wo sie schon gesetzt sind,
   passiert weiterhin nichts. */
add_action('admin_init', function () {
/* v0.7.5: DIE VERLORENEN BESCHREIBUNGEN NACHTRAGEN.

   Bis 0.7.4 warf der Einstellungs-Sanitizer forum_area_meta weg, weil der
   Schluessel nicht in seiner Whitelist stand — auf allen laufenden Knoten
   sind die Beschreibungen der Blaupause deshalb NIE angekommen. Der Fix
   allein holt sie nicht zurueck: Die Bereiche existieren, also legt die
   Blaupause sie nicht erneut an.

   Diese Migration traegt sie nach, aber NUR wo der Bereich denselben Namen
   traegt UND noch gar keine Beschreibung hat. Wer selbst eine geschrieben
   hat, behaelt seine — dieselbe Regel wie beim Guru-Text in 0.5.2. */
if (!in_array('area_desc_repair_v075', (array) get_option('cluster_one_migrations', array()), true)) {
    $c1_o75 = get_option('cluster_one_settings', array());
    $c1_m75 = (array) ($c1_o75['forum_area_meta'] ?? array());
    $c1_v75 = (array) ($c1_o75['forum_areas'] ?? array());
    $c1_b75 = function_exists('cluster_one_blueprint_areas') ? cluster_one_blueprint_areas() : array();
    $c1_t75 = false;
    foreach ($c1_b75 as $c1_bn => $c1_bd) {
        if (!in_array($c1_bn, $c1_v75, true)) {
            continue; // nicht vorhanden — die Blaupause legt ihn spaeter selbst an
        }
        if (trim((string) ($c1_m75[$c1_bn]['desc'] ?? '')) !== '') {
            continue; // schon beschrieben — die gehoert dem SysOp
        }
        $c1_m75[$c1_bn] = array_merge(
            (array) ($c1_m75[$c1_bn] ?? array()),
            array('desc' => (string) $c1_bd, 'origin' => 'blueprint', 'published' => true)
        );
        $c1_t75 = true;
    }
    if ($c1_t75) {
        $c1_o75['forum_area_meta'] = $c1_m75;
        update_option('cluster_one_settings', $c1_o75, false);
    }
    $c1_g75   = (array) get_option('cluster_one_migrations', array());
    $c1_g75[] = 'area_desc_repair_v075';
    update_option('cluster_one_migrations', array_values(array_unique($c1_g75)), false);
}
/* v0.9.35: DIE REAKTOR-BESCHREIBUNG BEKOMMT EINEN SATZ. Christians Wunsch,
   unaufdringlich: dass man hier auch die eigene Webseite planen kann. Der
   Blaupausen-Text ist erweitert; hier zieht er auf Knoten nach, die ihn
   unveraendert uebernommen haben.
   NUR DORT, wo origin=blueprint steht UND der alte Wortlaut noch dasteht —
   wer selbst geschrieben hat, behaelt seine Fassung (Regel aus 0.7.5). */
if (!in_array('reaktor_desc_v0935', (array) get_option('cluster_one_migrations', array()), true)) {
    $c1_o35 = get_option('cluster_one_settings', array());
    $c1_m35 = (array) ($c1_o35['forum_area_meta'] ?? array());
    $c1_alt35 = 'Where the work happens. Projects that may one day become a label and travel to other nodes.';
    $c1_de35  = 'Hier wird gearbeitet. Projekte, aus denen eines Tages ein Label werden kann, das zu anderen Knoten reist.';
    foreach ($c1_m35 as $c1_n35 => $c1_e35) {
        if ((string) ($c1_e35['origin'] ?? '') !== 'blueprint') {
            continue;
        }
        $c1_d35 = trim((string) ($c1_e35['desc'] ?? ''));
        if ($c1_d35 !== $c1_alt35 && $c1_d35 !== $c1_de35) {
            continue; // vom SysOp veraendert — gehoert ihm
        }
        $c1_m35[$c1_n35]['desc'] = ($c1_d35 === $c1_de35)
            ? $c1_de35 . ' Auch dieser Knoten selbst zaehlt dazu: die eigene Webseite, der Kanal, das Naechste, was hier entsteht.'
            : $c1_alt35 . ' This node itself counts: your website, your channel, whatever you are building next.';
        $c1_o35['forum_area_meta'] = $c1_m35;
        update_option('cluster_one_settings', $c1_o35, false);
        break;
    }
    $c1_g35   = (array) get_option('cluster_one_migrations', array());
    $c1_g35[] = 'reaktor_desc_v0935';
    update_option('cluster_one_migrations', array_values(array_unique($c1_g35)), false);
}
/* v0.9.35: RESTE DES ZURUECKGEBAUTEN BLOG-WEGES. 'wiki_locked' und
   'blog_post_id' stehen in Bestandsdaten (Christians Main, Thema
   sokrates_p7ewCrEVf7UE), haben im ganzen Plugin aber KEINEN Leser und
   KEINEN Schreiber mehr — der Weg wurde in 0.5.0 zurueckgebaut.

   Sie reisen trotzdem in jedem Spiegel mit, und eine KI, die den Dump liest,
   schliesst aus wiki_locked, dass dieses Thema gesperrt sei. EIN LUEGENDER
   TEXT IN DATENFORM. */
if (!in_array('blogweg_reste_v0935', (array) get_option('cluster_one_migrations', array()), true)) {
    $c1_t35 = get_option('cluster_one_forum_threads', array());
    $c1_w35 = false;
    if (is_array($c1_t35)) {
        foreach ($c1_t35 as $c1_i35 => $c1_th35) {
            foreach (array('wiki_locked', 'blog_post_id') as $c1_f35) {
                if (is_array($c1_th35) && array_key_exists($c1_f35, $c1_th35)) {
                    unset($c1_t35[$c1_i35][$c1_f35]);
                    $c1_w35 = true;
                }
            }
        }
    }
    if ($c1_w35) {
        update_option('cluster_one_forum_threads', $c1_t35, false);
    }
    $c1_gb35   = (array) get_option('cluster_one_migrations', array());
    $c1_gb35[] = 'blogweg_reste_v0935';
    update_option('cluster_one_migrations', array_values(array_unique($c1_gb35)), false);
}
if (!in_array('bismarck_to_eddington_v061', (array) get_option('cluster_one_migrations', array()), true)) {
    $c1_m61 = cluster_one_get_modes();
    if (isset($c1_m61['bismarck']) && (string) ($c1_m61['bismarck']['name'] ?? '') === 'Bismarck') {
        $c1_m61['bismarck']['name'] = 'Eddington';
        cluster_one_save_modes($c1_m61);
    }
    $c1_g61   = (array) get_option('cluster_one_migrations', array());
    $c1_g61[] = 'bismarck_to_eddington_v061';
    update_option('cluster_one_migrations', array_values(array_unique($c1_g61)), false);
}
/* v0.9.42: BRAINSTORMING OHNE (WIKI).

   Christian am 19.08.: "Oder wir lassen Brainstorming drinnen und nehmen Wiki
   raus. Denn Wiki kann jetzt ja ueberall sein."

   Er hat recht, und der Name log seit 0.0.69: Wiki ist seither eine
   Eigenschaft des THEMAS, beim Anlegen gewaehlt und eingefroren. Der Bereich
   bestimmt gar nichts mehr — das Wort in der Klammer versprach eine
   Bereichs-Eigenschaft, die es nicht mehr gab.

   Das Umbenennen geht durch cluster_one_area_rename(), dieselbe Funktion, die
   auch der Knopf unter Struktur benutzt: Themen und Raeume ziehen mit, sonst
   fielen sie lautlos ins Auffangbecken.

   ZWEITER TEIL: Der Schluessel forum_wiki_areas wird aus den Einstellungen
   entfernt. Er reiste in jedem Spiegel mit, und eine KI, die den Dump liest,
   schloss daraus, dass Bereiche Wiki-Verhalten bestimmen. Genau derselbe
   Irrtum wie bei wiki_locked und blog_post_id (0.9.35). */
if (!in_array('brainstorming_ohne_wiki_v0942', (array) get_option('cluster_one_migrations', array()), true)) {
    if (function_exists('cluster_one_area_rename')) {
        $c1_ba = cluster_one_forum_areas();
        // Nur umbenennen, wenn der alte Name da ist UND der neue frei — sonst
        // entstuenden zwei ununterscheidbare Bereiche.
        if (in_array('Brainstorming (Wiki)', $c1_ba, true) && !in_array('Brainstorming', $c1_ba, true)) {
            cluster_one_area_rename('Brainstorming (Wiki)', 'Brainstorming');
        }
    }
    $c1_bo = get_option('cluster_one_settings', array());
    if (is_array($c1_bo) && array_key_exists('forum_wiki_areas', $c1_bo)) {
        unset($c1_bo['forum_wiki_areas']);
        update_option('cluster_one_settings', $c1_bo, false);
    }
    $c1_bm   = (array) get_option('cluster_one_migrations', array());
    $c1_bm[] = 'brainstorming_ohne_wiki_v0942';
    update_option('cluster_one_migrations', array_values(array_unique($c1_bm)), false);
}
/* v0.9.52: DIE GESCHWISTER DES MODUS-FELDES.

   Christians Spiegel vom 21.08. gegen den Quelltext gehalten: SIEBEN
   Schluessel liegen in den gespeicherten Containern, die kein Code mehr liest.
   Jeder einzelne ist derselbe Fall wie 'mode' in 0.9.51 — der Code ging, die
   Werte blieben:

     RAEUME   visibility   (8/8, entfallen 0.9.34 — "wurde geschrieben und
                            nirgends gelesen")
              bridge_type  (8/8, entfallen 0.9.26 mit der Bruecke; kommt nur
                            noch in Kommentaren vor)
              bridge_ref   (8/8, dito, nirgends)
              humans_only  (7/8, entfallen 0.9.26 — "die KI-Liste IST die
                            Antwort")
              circle_depth (7/8, entfallen 0.9.26 — "wer im Kreis ist, schreibt")
              ai_wishes    (1/8, ersetzt durch fs['wishes'] — kommt nur noch
                            in Kommentaren ueber den damaligen Fehler vor)
     THEMEN   circle_depth (4/10, dieselbe Streichung)
              foreign_id   (1/10, nie gelesen)

   ES SIND ACHT, NICHT SIEBEN — und das kam so heraus: Meine Handzaehlung fand
   sieben und uebersah 'ai_wishes', weil sie Kommentare mitzaehlte. Der neue
   Spiegel-Pruefer fand acht, uebersah dafuer 'visibility', weil derselbe
   Schluesselname in der CHRONIK vorkommt (dort wird er gelesen, an einer
   Ereigniszeile, nicht an einem Raum). Erst beide zusammen hatten die Liste.
   Werkzeug und Mensch haben sich gegenseitig berichtigt — genau dafuer gibt es
   zwei.

   Jeder seiner acht Raeume trug mindestens drei davon. Sie reisen in jedem
   Spiegel mit, und die naechste KI, die den Dump liest, schliesst daraus, dass
   ein Raum eine Sichtbarkeit hat und eine Bruecke haben kann. Genau der
   Irrtum, den 0.9.35 fuer wiki_locked und blog_post_id benannt hat.

   EIN WERT, DEN NIEMAND FRAGT, IST KEIN BESTANDSSCHUTZ, SONDERN EIN REST —
   Christians eigener Satz aus 0.9.34, hier zum vierten Mal angewandt.

   MenschA-Chat trug als einziger 'humans_only' => true. Auch das geht: Der
   Raum hat keine KI in der Liste, ist also ohnehin menschenrein. Die Absicht
   bleibt wahr, nur ihr totes Etikett verschwindet. */
if (!in_array('tote_schluessel_v0952', (array) get_option('cluster_one_migrations', array()), true)) {
    $c1_tr = cluster_one_get_rooms();
    $c1_tc = false;
    foreach ($c1_tr as $c1_tid2 => $c1_troom) {
        foreach (array('visibility', 'bridge_type', 'bridge_ref', 'humans_only', 'circle_depth', 'ai_wishes') as $c1_tk) {
            if (array_key_exists($c1_tk, (array) $c1_troom)) {
                unset($c1_tr[$c1_tid2][$c1_tk]);
                $c1_tc = true;
            }
        }
    }
    if ($c1_tc) { cluster_one_save_rooms($c1_tr); }

    $c1_tt = cluster_one_get_forum_threads();
    $c1_tc2 = false;
    foreach ($c1_tt as $c1_ttid => $c1_tthread) {
        foreach (array('circle_depth', 'foreign_id') as $c1_tk2) {
            if (array_key_exists($c1_tk2, (array) $c1_tthread)) {
                unset($c1_tt[$c1_ttid][$c1_tk2]);
                $c1_tc2 = true;
            }
        }
    }
    if ($c1_tc2) { cluster_one_save_forum_threads($c1_tt); }

    $c1_tm   = (array) get_option('cluster_one_migrations', array());
    $c1_tm[] = 'tote_schluessel_v0952';
    update_option('cluster_one_migrations', array_values(array_unique($c1_tm)), false);
}
/* v0.9.51: DAS MODUS-ETIKETT WIRD AUSGERAEUMT.

   Das Feld ist aus dem Code gegangen — aber es steht noch in jedem
   gespeicherten Raum, und dort bliebe es fuer immer. Ein Schluessel, den
   niemand mehr liest, reist trotzdem in jedem Spiegel mit, und eine KI, die
   den Dump liest, schliesst daraus, dass Raeume einen gesetzten Modus haben.
   Genau derselbe Irrtum wie bei forum_wiki_areas (0.9.42), wiki_locked und
   blog_post_id (0.9.35). Dritte Begegnung, gleiche Antwort.

   Die Migration nimmt NICHTS weg, was noch etwas bewirkt: Der Modus ergibt
   sich seit 0.9.2 aus Kopplung, Uebersetzer und Besetzung. In Christians Daten
   war das Etikett bei zwei von acht Raeumen sogar falsch. */
if (!in_array('modus_ohne_feld_v0951', (array) get_option('cluster_one_migrations', array()), true)) {
    $c1_mr = cluster_one_get_rooms();
    $c1_mc = false;
    foreach ($c1_mr as $c1_mid => $c1_mroom) {
        if (array_key_exists('mode', (array) $c1_mroom)) {
            unset($c1_mr[$c1_mid]['mode']);
            $c1_mc = true;
        }
    }
    if ($c1_mc) {
        cluster_one_save_rooms($c1_mr);
    }
    $c1_mm   = (array) get_option('cluster_one_migrations', array());
    $c1_mm[] = 'modus_ohne_feld_v0951';
    update_option('cluster_one_migrations', array_values(array_unique($c1_mm)), false);
}
/* v0.9.42: UEBERSETZER NUR IN BABEL-FISH.

   Christian am 19.08.: "Uebersetzer ist nur etwas fuer Babel-Fish. Und den
   wuerde ich ohnehin ueber SysOp laufen lassen." Begruendung: In Tagebuechern
   braucht es keine, und in einem Raum mit mehreren Menschen UND mehreren KIs
   wird Uebersetzung zu teuer und zu kompliziert.

   Diese Migration raeumt die Altfaelle weg, und sie MUSS es tun: Das Feld
   verschwindet gleichzeitig aus der Konfiguration der uebrigen Raeume. Bliebe
   ein gesetzter Uebersetzer stehen, waere er eine unsichtbare Einstellung,
   die weiterlaeuft — schlimmer als beide Zustaende davor.

   In Christians Daten trifft es genau einen: Wiki-01 laeuft nicht als
   Babel-Fish und hatte trotzdem 'openai' gesetzt. */
if (!in_array('uebersetzer_nur_babel_v0942', (array) get_option('cluster_one_migrations', array()), true)) {
    $c1_ur = cluster_one_get_rooms();
    $c1_uc = 0;
    foreach ($c1_ur as $c1_uid => $c1_um) {
        if ((string) ($c1_um['translator'] ?? '') === '') { continue; }
        if (cluster_one_room_derived_mode($c1_um) === 'babel_fish') { continue; }
        $c1_ur[$c1_uid]['translator'] = '';
        $c1_uc++;
    }
    if ($c1_uc > 0) { cluster_one_save_rooms($c1_ur); }
    $c1_um2   = (array) get_option('cluster_one_migrations', array());
    $c1_um2[] = 'uebersetzer_nur_babel_v0942';
    update_option('cluster_one_migrations', array_values(array_unique($c1_um2)), false);
}
/* guru_desc_fix_v052 — DER ALTE SATZ WIRD ERKANNT, NICHT ANGEZEIGT.
   ---------------------------------------------------------------------------
   Mammuts Fund vom 23.09.2026: hier stand

       __($c1_alt, 'cluster-one-bismarck')

   also __() mit einer VARIABLEN als msgid. Seine Antwort auf seine eigene
   Frage — wird der Text ANGEZEIGT? — ist NEIN: dieser Ruf zeichnet nichts.
   Er fragt „wie saehe der alte englische Satz auf einem uebersetzten Knoten
   aus", um ihn wiederzuerkennen und zu ersetzen.

   ABER EIN KOMMENTAR REICHT TROTZDEM NICHT, und der Grund liegt eine Stufe
   tiefer als die Frage: dieser Ruf BRAUCHT einen Katalogeintrag, den kein
   Werkzeug erzeugen kann. Gemessen im ausgelieferten Paket 0.2.20:

       der Satz als Literal im ganzen Paket          1x — diese Zuweisung
       der Satz in einem __() als Literal            0x
       der Satz im Katalog de_DE                     1x, uebersetzt

   Er steht also drin, und NICHTS IM CODE HAELT IHN DORT. Beim naechsten
   Schnitt faellt er als unbenutzt heraus, und der Ruf gibt danach still die
   msgid zurueck — die Zeile prueft dann zweimal denselben englischen Satz.

       Ein Ruf, der einen Eintrag braucht, den kein Werkzeug sehen kann,
       ist nicht falsch. Er ist geliehen, und der Verleiher weiss nichts
       davon.

   (Was ich NICHT sage: dass der fehlende Quellverweis im .po das beweist.
    Nur 104 von 1508 Eintraegen tragen einen — das Fehlen unterscheidet
    diesen Eintrag von nichts.                              R-MESSUNG-2)

   ---------------------------------------------------------------------------
   ZWEI ZEILEN, ZWEI GRUENDE, und die zweite ist die Form dieses Hauses.

   1  DAS LITERAL STEHT JETZT IM __() SELBST. Damit sieht es jedes Werkzeug —
      meins, Holleriths, xgettext — und der Eintrag ueberlebt jeden Schnitt.
      $c1_alt bleibt daneben fuer den UNuebersetzten Vergleich.

   2  UND DER DEUTSCHE SATZ STEHT ALS LITERAL DANEBEN, genau wie in
      reaktor_desc_v0935 dreihundert Zeilen weiter oben — dieselbe Aufgabe,
      dieselbe Loesung, drei Wochen frueher von mir selbst gebaut.

          Eine Wanderung, die eine Uebersetzung wiedererkennen muss, haengt
          entweder am Katalog oder nicht. Wer nicht daran haengen will,
          schreibt den Satz hin.

      Die Reichweite der beiden ist nicht dieselbe, und deshalb stehen beide:
          das Literal im __()  erkennt JEDE Sprache, solange der Eintrag lebt
          der deutsche Satz    erkennt Deutsch, auch wenn er nicht mehr lebt
      Mehr als Deutsch gibt es heute nicht: das Paket traegt genau einen
      Katalog, de_DE. */
if (!in_array('guru_desc_fix_v052', (array) get_option('cluster_one_migrations', array()), true)) {
    $c1_m52 = cluster_one_get_modes();
    $c1_alt = 'A quiet one-on-one space between a human and an AI.';
    $c1_de52 = 'Ein stiller Zweier-Raum zwischen einem Menschen und einer KI.';
    if (isset($c1_m52['guru_meditation'])
        && in_array((string) ($c1_m52['guru_meditation']['desc'] ?? ''), array(
               $c1_alt,
               $c1_de52,
               __('A quiet one-on-one space between a human and an AI.', 'cluster-one-bismarck'),
           ), true)) {
        $c1_d52 = cluster_one_default_modes();
        $c1_m52['guru_meditation']['desc'] = (string) $c1_d52['guru_meditation']['desc'];
        cluster_one_save_modes($c1_m52);
    }
    $c1_mig52   = (array) get_option('cluster_one_migrations', array());
    $c1_mig52[] = 'guru_desc_fix_v052';
    update_option('cluster_one_migrations', array_values(array_unique($c1_mig52)), false);
}
});

/* v0.9.4: ABGELEITETE MODI ZIEHEN IHRE TEXTE NACH.

   Christians Spiegel-Dump: Seine cluster_one_modes trug weiterhin 'anteroom'
   und 'popper' und den ALTEN Terminal-Text. Die Modi werden einmal gesaet und
   danach nie wieder angefasst — was in modi.php geaendert wird, erreicht einen
   laufenden Knoten nie.

   Fuer die WAEHLBAREN Modi ist das richtig so: Der SysOp darf sie umbenennen
   und umschreiben, und ein Update darf ihm das nicht wegnehmen.

   Fuer die ABGELEITETEN gilt das nicht. Sie beschreiben eine Tatsache, nicht
   eine Wahl — niemand setzt sie, also gehoert ihr Text zum Code. 'anteroom'
   und 'popper' sind entfallen und werden entfernt; die uebrigen abgeleiteten
   bekommen den aktuellen Text. */
add_action('admin_init', function () {
    if (!in_array('derived_modes_v094', (array) get_option('cluster_one_migrations', array()), true)) {
        $c1_m94 = cluster_one_get_modes();
        $c1_d94 = cluster_one_default_modes();
        $c1_t94 = false;
        foreach (array('anteroom', 'popper') as $c1_weg) {
            if (isset($c1_m94[$c1_weg])) {
                unset($c1_m94[$c1_weg]);
                $c1_t94 = true;
            }
        }
        foreach (cluster_one_modes_derived() as $c1_slug) {
            if (isset($c1_m94[$c1_slug]) && isset($c1_d94[$c1_slug])) {
                $c1_m94[$c1_slug] = $c1_d94[$c1_slug];
                $c1_t94 = true;
            }
        }
        if ($c1_t94) {
            cluster_one_save_modes($c1_m94);
        }
        $c1_g94   = (array) get_option('cluster_one_migrations', array());
        $c1_g94[] = 'derived_modes_v094';
        update_option('cluster_one_migrations', array_values(array_unique($c1_g94)), false);
    }
});

add_action('admin_init', function () {
    // Saat-Register: pro Slug genau einmal.
    $seeded  = (array) get_option('cluster_one_modes_seeded', array());
    $modes   = cluster_one_get_modes();
    $changed = false;
    foreach (cluster_one_default_modes() as $slug => $def) {
        if (in_array($slug, $seeded, true)) {
            continue; // schon mal gesät — Edits/Löschungen bleiben unangetastet
        }
        if (!isset($modes[$slug])) {
            $modes[$slug] = $def;
            $changed = true;
        }
        $seeded[] = $slug;
    }
    if ($changed) {
        cluster_one_save_modes($modes);
    }
    update_option('cluster_one_modes_seeded', array_values(array_unique($seeded)), false);

    /* v0.9.51 ZURUECKGEBAUT. Diese Alt-Raeume-Migration uebersetzte gespeicherte
       Modus-ANZEIGENAMEN in Slugs und stempelte bei "Night-Flight" zusaetzlich
       humans_only. Beide Gegenstaende sind fort: humans_only ist in 0.9.26
       entfallen, das Feld 'mode' in dieser Version.

       Sie stehen zu lassen waere nicht harmlos gewesen: Sie laeuft NACH der
       neuen Aufraeum-Migration und haette auf einem alten Knoten, der sie nie
       ausgefuehrt hat, das eben entfernte Feld WIEDER hineingeschrieben. Eine
       Migration, deren Gegenstand verschwunden ist, ist kein Bestandsschutz
       mehr, sondern ein Wiedergaenger.

       Die Marke selbst bleibt — sie kostet nichts und sagt der Nachwelt, dass
       dieser Knoten die 0.0.75-Umstellung gesehen hat. */
    if (!get_option('cluster_one_modes_migrated')) {
        update_option('cluster_one_modes_migrated', (function_exists('cluster_one_fassung') ? cluster_one_fassung() : (defined('CLUSTER_ONE_VERSION') ? (string) CLUSTER_ONE_VERSION : '')), false);
    }
});


/** Generalisiert aus cluster_one_room_ai_reply (v0.0.47): nimmt einen
   Kontext-Satz statt eines Raum-Arrays entgegen, damit Bismarck-Räume UND
   Platon-Themen dieselbe KI-Antwort-Logik nutzen — ein Kostendeckel, ein
   Fehlerbehandlungs-Pfad, nicht zwei parallele Kopien. */
/* v1.3.0 — DER ACHTE PARAMETER IST DIE RECHNUNG.
   Diese Funktion hat drei Aufrufer mit DREI VERSCHIEDENEN Zahlern: aus einem
   Raum der Gastgeber, aus dem Forum und aus dem Wiki der, der den Knopf
   gedrueckt hat (beide Aktionen stehen hinter can_access, nicht hinter
   is_sysop — es darf also jeder auf der Weissliste rufen, und dann zahlt er).
   Deshalb steht der Akteur nicht hier drin, sondern kommt von aussen: eine
   Funktion, die den Zahler selbst erraet, erraet ihn irgendwann falsch. */
function cluster_one_ai_reply(string $cid, string $context_label, array $msgs, string $ai_name, string $linked_context = '', string $preamble = '', string $umgebung = '', int $akteur = 0): array {
    if (! function_exists('wp_ai_client_prompt')) {
        return array('ok' => false, 'err' => 'no_client', 'detail' => '');
    }

    // Jüngstes Transkript als Kontext — [Zeit] Autor: Text. Standard 20;
    // v0.0.88: für Kondensat-Aufträge (→ Thread) die volle gespeicherte
    // Historie (Speicher kappt bei 200 — das Kondensat ist der einzige
    // Schutz vor dem Wegscrollen der TTY-Session). Runden filtern NIE:
    // Lampen, keine Wände — Zwischen-Runden-Gespräche sind immer drin.
    $tail = array_slice($msgs, -1 * max(1, (int) ($GLOBALS['cluster_one_ai_context_depth'] ?? 20)));
    $lines = array();
    foreach ($tail as $m) {
        $a = trim((string) ($m['author'] ?? ''));
        $b = trim((string) ($m['body'] ?? ''));
        $s = trim((string) ($m['subject'] ?? '')); // Platon-Beiträge tragen einen eigenen, editierbaren Betreff — Bismarck-Nachrichten nicht (bleibt dann leer)
        $t = (int) ($m['ts'] ?? 0);
        if ($b !== '') {
            $stamp = $t > 0 ? '[' . date_i18n('Y-m-d H:i', $t) . '] ' : '';
            $subj  = $s !== '' ? '(' . $s . ') ' : '';
            $lines[] = $stamp . ($a !== '' ? $a . ': ' : '') . $subj . $b;
        }
    }
    $prompt = empty($lines)
        ? 'There are no messages yet. Open the conversation with a brief, useful first message.'
        : implode("\n", $lines);

    $sys = sprintf(
        'You are "%s", one participant in %s. Humans and other AIs may speak above. '
        . 'Each line of the conversation is prefixed with its timestamp in square brackets. '
        . 'Reply as yourself, in the language of the conversation, concisely and substantively. '
        . 'Do not prefix your name, do not role-play other participants, and do not invent messages from them.',
        $ai_name,
        $context_label
    );

    // Präambel (v0.0.60): die bewusste, vom SysOp gesetzte Orientierung für
    // genau diesen Ort — geht VOR den verknüpften Fremdkontext, weil sie das
    // Wichtigere ist (Steuerung, nicht bloßer Hintergrund). Die KI folgt ihr
    // als Leitlinie, darf sie aber im verknüpften Chat auch offen
    // kritisieren, wenn sie sie für problematisch hält.
    // v0.1.8: Die Hausordnung dieses Knotens, gelesen aus why.md (markierter
    // Abschnitt, keine zweite Fassung). Steht VOR der Präambel des SysOps:
    // erst wo du bist, dann was hier gewünscht ist. Fehlt die Datei oder die
    // Markierung, kommt gar nichts — lieber nichts als etwas Erfundenes.
    /* v1.9.13 (W52) — GERIEGELT, und der Riegel ist woertlich Kleinrocks
       (kleinrock.php:3528). Dieselbe Funktion wurde in ZWEI Haeusern gerufen:
       eines fragte, eines griff. Ein Rueckfall auf '' ist hier richtig — ohne
       Feynman gibt es die Hausordnung nicht, und "lieber nichts als etwas
       Erfundenes" steht drei Zeilen darueber schon. */
    $c1_haus = function_exists('cluster_one_why_briefing') ? cluster_one_why_briefing() : '';
    if ($c1_haus !== '') {
        $sys .= "\n\n" . 'This node ships a written house rule, readable by every person here as well as by you — the same text, no separate version. It is not an instruction from the operator but the reasoning of the place:' . "\n" . $c1_haus;
    }

    // v0.2.1: Randbedingungen — NUR bei ausdruecklicher, befristeter Uebergabe.
    // Nicht in why.md, nicht ueber die oeffentliche Route: die Versionsliste
    // ist ein Angriffs-Fingerabdruck. Die ENTSCHEIDUNG faellt beim Aufrufer,
    // weil nur dort der Container bekannt ist — diese Funktion kennt ihn nicht.
    if ($umgebung !== '') {
        $sys .= "\n\n" . $umgebung;
    }

    // v0.1.9: Kapitel, die der SysOp ausdruecklich auch den KIs vorlegt.
    // Hinter der Hausordnung, vor der Praeambel: erst wo du bist, dann was hier
    // gilt, dann was hier gewuenscht ist.
    /* v1.3.1: ueber den Vertrag, nicht beim Namen — sonst ist ein Bismarck
       ohne Feynman ein Fatal statt eines leeren Absatzes. */
    $c1_kap = (string) apply_filters('cluster_one_erklaerung', '', 'kapitel');
    if ($c1_kap !== '') {
        $sys .= "\n\n" . 'The SysOp has written the following chapters for this node and has marked them to be shared with you. The people here see the same chapters, and they see that you were given them:' . "\n" . $c1_kap;
    }

    if ($preamble !== '') {
        $sys .= "\n\n" . 'The SysOp has set the following preamble for this place — a standing orientation you should follow. If you consider it genuinely problematic, you may say so openly rather than silently obeying, but otherwise take it as your guiding frame here:' . "\n" . $preamble;
    }

    // Verknüpfter Raum/Thread (v0.0.59): Orientierung. v0.0.68: Die KI SOLL
    // von der Verknüpfung wissen und darf sich auf den Inhalt beziehen —
    // die alte Fassung („use it only as background", ohne Titel, ohne
    // Umfang) führte dazu, dass KIs ihre eigene Verknüpfung verleugneten.
    // Erhalten bleibt die Verwechslungs-Sperre: Es sind keine Nachrichten
    // DIESES Gesprächs.
    if ($linked_context !== '') {
        $sys .= "\n\n" . 'This conversation is linked to a companion place on this node. You should be aware of this link, and when someone asks about the linked thread/room or its content, answer from the excerpt below — you DO have access to it. You may refer to its content naturally whenever it is relevant. It is context, not part of the conversation above: do not treat its lines as messages spoken here, and do not reply to them directly. The excerpt may be partial; the header states exactly what is included:' . "\n" . $linked_context;
    }

    return cluster_one_ai_generate_text($cid, $sys, $prompt, $akteur);
}

/** Baut den verknüpften Orientierungs-Kontext (v0.0.59) für eine KI-Antwort:
   die letzten paar Beiträge/Nachrichten aus dem 1:1-verknüpften Raum bzw.
   Thread, als reiner Text. Bewusst hart begrenzt (die letzten 5), damit der
   Kontext — und die Kosten — nicht explodieren. Gibt '' zurück, wenn keine
   Verknüpfung existiert oder der verknüpfte Ort unsichtbar/nicht gescoped ist.
   v0.0.68 (Fund SysOp, 15.07.2026): ehrlicher KONTEXTBELEG als Kopfzeile —
   die Minimal-Vorziehung aus Übergabedokument Teil II.4. Bisher kamen fünf
   nackte Zeilen ohne Titel, ohne Umfang, ohne den Hinweis, DASS eine
   Verknüpfung existiert — die KIs wussten buchstäblich nicht, dass sie einen
   verknüpften Thread haben. Jetzt: Titel, Gesamtumfang, enthaltene Spanne,
   Vollständigkeits-Angabe; und ein leerer verknüpfter Ort liefert den Beleg
   trotzdem (Bewusstsein über die Verknüpfung statt stummes '').
   Kontextbudget + Abschnitts-Summaries bleiben beim Tagebuch-Kanal. */
function cluster_one_linked_context_for_ai(array $container, string $side, string $cid = ''): string {
    // $side: 'room' -> $container ist ein Raum, verknüpft ist ein Thread.
    //        'thread' -> $container ist ein Thread, verknüpft ist ein Raum.
    $lines = array();
    $header = '';

    if ($side === 'room') {
        $tid = (string) ($container['linked_thread'] ?? '');
        if ($tid === '') {
            return '';
        }
        $threads = cluster_one_get_forum_threads();
        if (!isset($threads[$tid])) {
            return '';
        }
        // v0.0.75 (Nebenfund T26): KI-Tagebücher haben NIE menschliche
        // Sichtbarkeit — die can_view-Sicherung (v0.0.61) ließ darum die
        // Tagebuch-KI ihr eigenes Tagebuch im Chat nie sehen, seit es
        // Tagebücher gibt. Tagebücher passieren hier und laufen in das
        // P0-5-Besitzerinnen-Gate direkt darunter; für alle anderen
        // Threads bleibt die Menschen-Prüfung unverändert scharf.
        if (!cluster_one_group_visibility_can_view($threads[$tid]) && !cluster_one_thread_is_ai_diary($threads[$tid])) {
            return '';
        }
        if ($cid !== '' && !in_array($cid, cluster_one_thread_visible_ai_cids($threads[$tid]), true)) {
            return ''; // v0.0.61 (P0-5): DIESE KI ist am verknüpften Thema nicht zugelassen — menschliche Sichtbarkeit allein genügt nicht
        }
        $posts = cluster_one_get_forum_posts($tid);
        $total = count($posts);
        $tail  = array_slice($posts, -5);
        $shown = count($tail);
        /* v0.9.25: AUS EINER BESCHREIBUNG WIRD EINE ANWEISUNG.

           Christians Test: Bei SECHS Beitraegen reisen nur die letzten fuenf.
           Der Kopf sagte "Complete: no — older posts exist that are not shown
           here" — und Grok antwortete trotzdem "Ja, ich sehe sie alle" und
           "komplett".

           Er hat nichts erfunden: Den ersten Beitrag kannte er aus seiner
           EIGENEN frueheren Antwort im Chatverlauf. Aber er hat seinen ZUGANG
           falsch gemeldet, und genau das soll die Regel verhindern.

           EINE TATSACHE IM SYSTEM-PROMPT IST KEINE ANWEISUNG. Ein Modell liest
           "Complete: no" als Angabe unter vielen; es liest "answer no" als
           Auftrag. */
        $header = 'Linked forum thread: "' . trim((string) ($threads[$tid]['title'] ?? '')) . '"' . "\n"
            . 'Total: ' . $total . ' post(s)'
            . ($shown > 0 ? ' | Included verbatim below: post(s) ' . ($total - $shown + 1) . '-' . $total : '')
            . ' | Complete: ' . ($shown === $total ? 'yes' : 'no');
            if ($shown !== $total) {
                $header .= "\n" . 'IMPORTANT: you have NOT been given post(s) 1-' . ($total - $shown) . '. '
                    . 'If you recall their content from earlier in this conversation, that is your own memory, not access. '
                    . 'When asked whether you see all of them, answer no and name which are missing. Never claim it is complete.';
            }
        // v0.0.75 (Punkt 5): die Präambel des verknüpften Threads reist mit —
        // sonst wirkt die stehende Orientierung nur im Thread selbst und
        // nie im Chat, wo die KI hauptsächlich lebt (Tagebuch-Fall).
        $c1_linked_preamble = trim((string) ($threads[$tid]['preamble'] ?? ''));
        if ($c1_linked_preamble !== '') {
            $header .= "\n" . 'Standing orientation (preamble) of the linked thread:' . "\n" . $c1_linked_preamble;
        }
        foreach ($tail as $p) {
            $a = trim((string) ($p['author'] ?? ''));
            $b = trim((string) ($p['body'] ?? ''));
            if ($b !== '') {
                $lines[] = ($a !== '' ? $a . ': ' : '') . $b;
            }
        }
    } elseif ($side === 'thread') {
        $rid = (string) ($container['linked_room'] ?? '');
        if ($rid === '') {
            return '';
        }
        $rooms = cluster_one_get_rooms();
        if (!isset($rooms[$rid]) || !cluster_one_group_visibility_can_view($rooms[$rid])) {
            return '';
        }
        $room_ais = array_map('strval', (array) ($rooms[$rid]['ais'] ?? array()));
        if ($cid !== '' && !in_array($cid, $room_ais, true)) {
            return ''; // v0.0.61 (P0-5): DIESE KI ist im verknüpften Raum nicht getickt
        }
        $msgs  = cluster_one_get_room_messages($rid);
        $total = count($msgs);
        $tail  = array_slice($msgs, -5);
        $shown = count($tail);
        $header = 'Linked room: "' . trim((string) ($rooms[$rid]['name'] ?? '')) . '"' . "\n"
            . 'Total: ' . $total . ' message(s)'
            . ($shown > 0 ? ' | Included verbatim below: message(s) ' . ($total - $shown + 1) . '-' . $total : '')
            . ' | Complete: ' . ($shown === $total ? 'yes' : 'no');
            if ($shown !== $total) {
                $header .= "\n" . 'IMPORTANT: you have NOT been given message(s) 1-' . ($total - $shown) . '. '
                    . 'If you recall their content from earlier in this conversation, that is your own memory, not access. '
                    . 'When asked whether you see all of them, answer no and name which are missing. Never claim it is complete.';
            }
        foreach ($tail as $m) {
            $a = trim((string) ($m['author'] ?? ''));
            $b = trim((string) ($m['body'] ?? ''));
            if ($b !== '') {
                $lines[] = ($a !== '' ? $a . ': ' : '') . $b;
            }
        }
    }

    if ($header === '') {
        return '';
    }
    return empty($lines)
        ? $header . "\n" . '(No content yet.)'
        : $header . "\n" . implode("\n", $lines);
}


/** Rendert Erfolgs-/Fehlermeldung nach einem "Let it reply"-Versuch, samt
   optionalem Detail aus dem kurzlebigen Transient. Geteilt zwischen
   Bismarck-Räumen und Platon-Themen. */
function cluster_one_render_ai_notice(): void {
    if (isset($_GET['ai_ok'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('The AI replied.', 'cluster-one-bismarck') . '</p></div>';
    }
    if (isset($_GET['ai_err'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        $aec  = sanitize_key(wp_unslash($_GET['ai_err'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        $amsg = cluster_one_ai_error_message($aec);
        $tkey = 'cluster_one_ai_lasterr_' . get_current_user_id();
        $det  = get_transient($tkey);
        if ($det !== false) {
            delete_transient($tkey);
        }
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($amsg);
        if (is_string($det) && $det !== '') {
            echo ' <span style="opacity:.7;">(' . esc_html($det) . ')</span>';
        }
        echo '</p></div>';
    }
}

/** Rückwärtskompatibler Bismarck-Wrapper: baut das Kontext-Label aus dem
   Raum-Namen, wie es cluster_one_room_ai_reply vor v0.0.47 selbst tat. */
function cluster_one_room_ai_reply(string $cid, array $room, array $msgs, string $ai_name): array {
    $room_name = (string) ($room['name'] ?? '');
    $context   = 'a shared workspace room' . ($room_name !== '' ? ' called "' . $room_name . '"' : '');
    $linked    = cluster_one_linked_context_for_ai($room, 'room', $cid); // v0.0.59/0.0.61: verknüpftes Thema, KI-gescoped
    $preamble  = cluster_one_preamble_for_ai($room, 'room', $cid);       // v0.0.60/0.0.61: Präambel, KI-gescoped
    /* v0.2.1: Randbedingungen nur, wenn der SysOp sie fuer diesen Raum
       ausdruecklich und befristet uebergeben hat.

       v0.9.37: UND NUR, WENN DER SYSOP AUCH DER FRAGENDE IST. Christian hat
       das Loch am 19.08.2026 aus dem Prinzip heraus gefunden, ohne eine Zeile
       Code zu sehen: container_can_trigger_ai() gibt seit 0.9.15 true fuer den
       Reaktorfahrer zurueck, und diese Stelle fragte nur den RAUM — sie erfuhr
       nie, WER ausgeloest hat. Freigabe erteilen, dann uebergeben, und die
       Fragen des Beschirmten wurden mit der vollstaendigen Plugin-Liste samt
       Versionen beantwortet. Daneben steht die eigene Warnung aus kranz.php:
       "eine vollstaendige Plugin-Liste mit Versionen ist ein Fingerabdruck
       fuer Angriffe."

       DIE UEBERGABE BLEIBT VOLLSTAENDIG — es werden keine Raeder abmontiert.
       Der Fahrer darf alles, was die Uebergabe verspricht. Nur das Wissen
       ueber den Knoten bleibt bei dem, der ihn betreibt: "Wenn die so weit
       sind, dass sie auf die Konfiguration zugreifen koennen, sollen sie
       ihren eigenen Knoten aufmachen." Braucht der Fahrer doch eine Antwort
       darueber, holt der SysOp die Uebergabe zurueck und fragt selbst — ein
       Knopf, den es schon gibt.

       Die Freigabe bleibt im Raum SICHTBAR, solange sie laeuft. Das war ihr
       Zweck: "Offenlegung ist sichtbar, nicht still." Die anderen sehen, dass
       die KI gerade mehr weiss — sie bekommen es nur nicht in die Hand. */
    /* v1.3.2: ueber den Vertrag — ohne Umberto ist 'aktiv' false und 'text'
       leer, und beides behandelt der Code darunter schon. */
    $c1_umg = (isset($room) && is_array($room)
        && apply_filters('cluster_one_umgebung', false, 'aktiv', $room) && cluster_one_is_sysop())
        ? (string) apply_filters('cluster_one_umgebung', '', 'text') : '';
    /* v0.6.0: Am SELBEN Akt reist die Kurzkarte des Frameworks mit — wer den
       Baugrund uebergibt, uebergibt auch die Doku dessen, was darauf steht.

       Warum nicht bei jedem Aufruf: Die Doku ist 33 KB, die Kurzkarte 1,9 —
       aber auch die will nicht in jeder Anfrage bezahlt sein. Und warum nicht
       als Kopie im Plugin: Gelesen wird aus dem Child-Theme, also immer die
       Fassung, die auf DIESEM Knoten liegt (Lehre aus W8 — zwei Kopien einer
       Wahrheit driften auseinander).

       Kein Framework, kein Marker, kein Text: dann bleibt es leer und niemand
       erfindet etwas. */
    if ($c1_umg !== '') {
        /* v1.3.1: ueber den Vertrag, nicht beim Namen. */
        $c1_dna = (string) apply_filters('cluster_one_erklaerung', '', 'dna');
        if ($c1_dna !== '') {
            $c1_umg .= "\n\n" . 'This node runs a theme framework that documents itself for AI readers. The following is its own short map, read live from the installed version — not a copy, so it matches what is actually here:' . "\n" . $c1_dna;
        }
    }
    /* v1.3.0: Im Raum zahlt der Gastgeber — auch fuer die KI, die dort
       antwortet. Derselbe 'owner' wie beim Babel-Fisch: eine Regel, zwei
       Aufrufstellen.

       v1.3.5, 09.10.2026: NICHT MEHR DER GASTGEBER, SONDERN DER ZAHLENDE —
       und wer das ist, sagt der Boden.

       Mammuts Messung vom 09.10.: vier Wege legen einen Raum an, EINER
       schreibt 'owner'. Jeder normale Raum uebergab also 0, und damit war
       „im Raum zahlt der Gastgeber" keine Zusage, sondern eine Eigenschaft
       des Vorzimmers.

           Eine Regel, die nur an einer von vier Anlagen greift, ist keine
           Regel, sondern der Name einer Ausnahme.

       UND DIESE ZEILE ENTSCHEIDET DAMIT MAMMUTS A/B — das muss gesagt sein,
       nicht verschwiegen: cluster_one_akteur_ki(string $cid) sieht den RAUM
       nicht. Sie kann „im Raum zahlt der Gastgeber" gar nicht ausdruecken.
       Wer sie ruft, waehlt B: wer drueckt, zahlt. Soll A gelten, braucht die
       Boden-Funktion den Behaelter — das ist Mammuts Byte, nicht meins, und
       Christians Entscheidung.

       RUECKFALL, BENANNT (LEIHE): ohne cluster_one_akteur_ki steht hier
       wortgleich der Wert von heute. Auf einem Boden ohne die neue Funktion
       bewegt sich nichts. */
    $c1_zahler = cluster_one_ai_zahler($cid);
    if ($c1_zahler <= 0) {
        $c1_zahler = (int) (isset($room) && is_array($room) ? ($room['owner'] ?? 0) : 0);
    }
    return cluster_one_ai_reply($cid, $context, $msgs, $ai_name, $linked, $preamble, $c1_umg,
        $c1_zahler);
}

/** Anzeigename einer Raum-KI: knotenweit gekoppelter WP-User, sonst Connector-Name. */
function cluster_one_room_ai_label(string $cid, array $room): string {
    $ids = cluster_one_get_ai_identities();
    $uid = isset($ids[$cid]) ? (int) $ids[$cid] : 0;
    if ($uid > 0) {
        $u = get_userdata($uid);
        if ($u) {
            return $u->display_name;
        }
    }
    $conns = cluster_one_get_ai_connectors();
    if (isset($conns[$cid])) {
        $c = $conns[$cid];
        return is_array($c) ? (string) ($c['name'] ?? $cid) : $cid;
    }
    return $cid;
}

/* v1.3.5 — ZWEI FRAGEN, ZWEI NAMEN. Pacioli, 09.10.2026.

   cluster_one_room_ai_label() beantwortet: WELCHE KI MEINT DIESER ANBIETER AM
   KNOTEN. Das ist die knotenweite Kopplung, und die ist fuer alle dieselbe.

   Seit Pacioli gibt es eine zweite Frage, und sie hat eine andere Antwort je
   Mensch: WESSEN INSTANZ SPRICHT HIER GERADE. Die Instanz gehoert dem
   einzelnen Menschen, nicht dem Knoten.

       Eine Funktion, die zwei Fragen beantwortet, beantwortet die zweite
       immer nur zufaellig richtig.

   Darum ein ZWEITER NAME und kein zweiter Parameter. Ein Parameter mit
   Vorgabewert haette jede der zwoelf alten Aufrufstellen stillschweigend bei
   der alten Antwort gelassen — genau der verschluckte Parameter aus
   VOLLZUG 15 §3. Ein neuer Name laesst sich zaehlen.

   DER RUECKFALL IST BENANNT (LEIHE, nicht Abhaengigkeit): traegt der Boden
   cluster_one_ai_instanz_name() noch nicht, oder hat dieser Mensch fuer
   diesen Anbieter keine Instanz, dann ist die Antwort der KNOTENNAME — also
   genau das, was heute dort steht. Dieses Haus faehrt auf einem Boden ohne
   die drei neuen Funktionen Zeichen fuer Zeichen wie bisher. */
function cluster_one_ai_instanz_label(int $mensch, string $cid): string {
    if ($mensch > 0 && function_exists('cluster_one_ai_instanz_name')) {
        $c1_n = trim((string) cluster_one_ai_instanz_name($mensch, $cid));
        if ($c1_n !== '') {
            return $c1_n;
        }
    }
    return cluster_one_room_ai_label($cid, array());
}

/* v1.3.5 — WER ZAHLT EINEN AUFRUF AN DIESEN ANBIETER, HIER UND JETZT.

   Ich entscheide das nicht, ich frage. Die Regel steht seit v1.3.0 im Boden
   und ist Christians Satz vom 27.08.2026:

       Wo es einen Knopf gibt, zahlt, wer drueckt.
       Wo es keinen gibt, zahlt, wer den Raum aufgemacht hat.
       Die Rechnung folgt der letzten freien Entscheidung.

   cluster_one_akteur_ki() ist das Geschwister von cluster_one_akteur_mesh()
   — eine Wahrheit, mehrere Leser, kein zweiter Schreibweg (P1-12). Diese
   Funktion hier ist KEIN zweiter Schreibweg, sondern der EINE Leser in
   diesem Haus: sie reicht die Antwort des Bodens weiter und gibt 0 zurueck,
   wenn der Boden die Frage noch nicht beantwortet.

   0 IST EINE ANTWORT UND KEIN WERT. Jede Aufrufstelle nennt ihren eigenen
   Rueckfall mit eigenen Worten, weil die Rueckfaelle wirklich verschieden
   sind: im Raum der Gastgeber, im Forum der Druecker, beim Fremdabgleich der
   SysOp. Ein gemeinsamer Vorgabewert hier haette drei verschiedene Wahrheiten
   zu einer gemacht. */
function cluster_one_ai_zahler(string $cid): int {
    return function_exists('cluster_one_akteur_ki') ? (int) cluster_one_akteur_ki($cid) : 0;
}

/** Name schon vergeben? Getrimmt, case-insensitiv – eindeutig pro Knoten. */
/** v0.9.49: IST DIESER PROJEKTNAME SCHON VERGEBEN?

   Christians Regel: "Nehmen wir als Projektname Haekeln, erhalten wir
   Haekeln-Chat und Haekeln-Thread. Und wir muessen pruefen, ob Haekeln schon
   vergeben ist."

   Geprueft werden die ABGELEITETEN Namen — das ist, was tatsaechlich entsteht —
   und zwar gegen BEIDE Namensraeume. Bis hierher pruefte nur die Raumseite auf
   Doppelung; Themen-Titel durften sich beliebig wiederholen, und in Christians
   Daten tun sie es auch (zweimal "Bismarck-01", einmal als Thema, einmal als
   Label).

   Gross-/Kleinschreibung zaehlt nicht: "haekeln" und "Haekeln" als zwei
   Projekte waeren eine Falle.

   BENANNTE GRENZE: Ein bestehender Raum, der schlicht "Haekeln" heisst,
   blockiert das Projekt "Haekeln" NICHT — angelegt wuerden "Haekeln-Chat" und
   "Haekeln-Thread", und die kollidieren mit ihm nicht. Die Pruefung sagt, was
   sie prueft: die Namen, die entstehen. */
function cluster_one_projekt_namen(string $projekt): array {
    $projekt = trim($projekt);
    return array(
        'chat'   => $projekt === '' ? '' : $projekt . '-' . __('Chat', 'cluster-one-bismarck'),
        'thread' => $projekt === '' ? '' : $projekt . '-' . __('Thread', 'cluster-one-bismarck'),
    );
}

function cluster_one_projekt_name_vergeben(string $projekt): bool {
    $n = cluster_one_projekt_namen($projekt);
    if ($n['chat'] === '') {
        return false;
    }
    $lc = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';
    $gesucht = array($lc($n['chat']), $lc($n['thread']));
    foreach (cluster_one_get_rooms() as $r) {
        if (in_array($lc(trim((string) ($r['name'] ?? ''))), $gesucht, true)) {
            return true;
        }
    }
    foreach (cluster_one_get_forum_threads() as $t) {
        if (in_array($lc(trim((string) ($t['title'] ?? ''))), $gesucht, true)) {
            return true;
        }
    }
    return false;
}

function cluster_one_room_name_exists(string $name, string $except_id = ''): bool {
    $lc = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';
    $n  = $lc(trim($name));
    foreach (cluster_one_get_rooms() as $rid => $r) {
        if ((string) $rid === $except_id) {
            continue; // der Raum selbst zählt nicht als Kollision
        }
        if ($lc(trim((string) ($r['name'] ?? ''))) === $n) {
            return true;
        }
    }
    return false;
}

/* v0.9.31: Hier stand cluster_one_room_visibility_label(). Sie uebersetzte
   das Raumfeld 'visibility' (private/uncertain) in einen Anzeigetext — und war
   der einzige Leser dieses Feldes. Gerufen wurde sie nirgends; das Feld wird
   seit 0.0.50 nur noch mit 'private' beschrieben und nie gelesen. Die
   Sichtbarkeit, die tatsaechlich wirkt, heisst group_visibility. */

add_action('admin_post_cluster_one_forum_post_send', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_forum_post_send');
    /* v0.3.0: Fremd-Labels sind schreibgeschuetzt — auch hier, nicht nur in
       sokrates.php. Ein fremdes Projekt bleibt so, wie sein Autor es schrieb. */
    $c1_tid_fl = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $c1_thr_fl = cluster_one_get_forum_threads();
    if ($c1_tid_fl !== '' && isset($c1_thr_fl[$c1_tid_fl]) && cluster_one_thread_is_foreign($c1_thr_fl[$c1_tid_fl])) {
        wp_die('-1', 403);
    }

    $tid     = isset($_POST['thread'])  ? sanitize_text_field(wp_unslash($_POST['thread']))  : '';
    $subject = isset($_POST['subject']) ? trim(sanitize_text_field(wp_unslash($_POST['subject']))) : '';
    $body    = isset($_POST['body'])    ? cluster_one_sanitize_message((string) wp_unslash($_POST['body'])) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (cluster_one_sanitize_message), den der Pruefer nicht kennt
    $threads = cluster_one_get_forum_threads();
    $back    = admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid));

    if ($tid !== '' && isset($threads[$tid]) && $body !== '' && cluster_one_group_visibility_can_view($threads[$tid])) {
        $c1_is_label = cluster_one_thread_is_label($threads[$tid]);
        // v0.1.1: 'erloschen' ist ein HINWEIS, keine Sperre (löst 0.0.95 ab).
        // Im Becken darf weitergeschrieben werden — genau dafür ist es da:
        // Einspruch gegen die Stilllegung. Die Meldung steht weiter unten,
        // nach den übrigen Toren.
        if ($c1_is_label && !cluster_one_is_sysop()) {
            wp_safe_redirect($back); // v0.0.78: Nachträge am Label sind SysOp-Sache (sparsam, Campingplatz-Regel)
            exit;
        }
        /* v0.9.39, zurueckgebaut in v1.6.0 auf Bismarcks Antrag mit rotem
           Zeugen: die Regel war hier nie Umgangsform, sondern der Schutz des
           KI-Fensters (bismarck.php:674, die letzten fuenf Beitraege). Die
           Sperre sitzt im HANDLER, nicht nur in der Oberflaeche. */
        if (cluster_one_thread_awaiting_reply($tid, cluster_one_actor_id_human())) {
            wp_safe_redirect(add_query_arg('post_err', 'zug', $back));
            exit;
        }
        if (!cluster_one_container_can_act($threads[$tid])) {
            wp_safe_redirect($back); // v0.0.79: Kreis-Tiefe 'read' — lesen ja, schreiben nein
            exit;
        }
        // v0.1.1: Ein Einspruch im Becken darf nicht in einem Raum verhallen,
        // den niemand mehr betritt — er meldet sich beim SysOp.
        if (cluster_one_container_is_archived($threads[$tid])) {
            cluster_one_event_emit(array(
                'type'           => 'pool_append',
                'container_type' => 'thread',
                'container_id'   => $tid,
                'actor_id'       => cluster_one_actor_id_human(),
                'visibility'     => cluster_one_event_visibility($threads[$tid]),
            ));
        }
        $posts   = cluster_one_get_forum_posts($tid);
        $posts[] = array(
            'id'       => cluster_one_msg_id(),
            'author'   => cluster_one_human_speaker_label(),
            'actor_id' => cluster_one_thread_is_wiki($threads[$tid]) ? 'collective' : cluster_one_actor_id_human(), // v0.0.62
            'subject'  => $subject,
            'body'     => $body,
            'ts'       => time(),
        );
        cluster_one_save_forum_posts($tid, $posts);
        cluster_one_event_emit(array( // Label-Nachtrag: Türereignis (Campingplatz-Regel); sonst flüchtig
            'type' => $c1_is_label ? 'label_appended' : 'post_created', 'container_type' => 'thread', 'container_id' => $tid,
            'actor_id' => cluster_one_actor_id_human(), 'content_hash' => hash('sha256', $body),
        ), $c1_is_label);
    }
    wp_safe_redirect($back);
    exit;
});


/** v0.0.94: TAGEBUCH-VORZIMMER (Beschluss SysOp): ein Chat, gepaart mit
   einem Menschen-Tagebuch — der SysOp legt ihn an (ein Akt), steht aber
   selbst NICHT auf der Liste; KIs sind unmöglich (humans_only fest, und
   ohne Reaktor braucht niemand den Daumen des Kostenträgers — deshalb
   ist die Gästeliste delegierbar); der BESITZER verwaltet die Gäste auf
   der Raum-Seite (Hausrecht, nicht Konfiguration). PN-SCHARF (zweite
   bewusste Ausnahme vom „SysOp sieht alles", Entscheidung SysOp
   17.07.2026): kein Durchgriff, kein bezeugter Blick, keine Config
   (die wäre die Selbst-Einladungs-Hintertür). Der Spiegel zeigt den
   Titel (Struktur), nie den Inhalt; die WP-Admin-Allmacht bleibt wie
   bei Briefen der außergewöhnliche Akt mit Hürde. */
add_action('admin_post_cluster_one_antechamber_create', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_antechamber_create');
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
    $tid     = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $threads = cluster_one_get_forum_threads();
    $back    = admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid) . '&view=config');
    if ($tid === '' || !isset($threads[$tid]) || empty($threads[$tid]['human_diary']) || (string) ($threads[$tid]['linked_room'] ?? '') !== '') {
        wp_safe_redirect($back);
        exit;
    }
    $owner = 0;
    foreach ((array) ($threads[$tid]['visible_to'] ?? array()) as $vk) {
        $owner = (int) str_replace('wp_', '', (string) $vk);
        break;
    }
    if ($owner <= 0) {
        wp_safe_redirect($back);
        exit;
    }
    $rooms = cluster_one_get_rooms();
    $rid   = bin2hex(random_bytes(8));
    $rooms[$rid] = array(
        'id'               => $rid,
        /* translators: %s: Titel des Threads. */
        'name'             => sprintf(__('%s — anteroom', 'cluster-one-bismarck'), (string) ($threads[$tid]['title'] ?? '')),
        /* v0.9.34: Hier stand 'visibility' => 'private'. Das Feld wurde
           geschrieben und NIRGENDS gelesen — v0.9.6 hatte das schon
           festgestellt ("an KEINER Stelle im Plugin abgefragt, ein
           Versprechen ohne Deckung"), den Leser ersetzt und die Anzeige
           entfernt. Nur der Schreibvorgang blieb stehen. Kommentarfrei
           nachgemessen am 19.08.2026: null Lesestellen. */
        'group_visibility' => 'named',
        'visible_to'       => array('wp_' . $owner),
        'created'          => time(),
        'area'             => (string) ($threads[$tid]['area'] ?? ''),
        'ais'              => array(),
        /* v0.9.1: Das Vorzimmer traegt jetzt 'terminal' — Anteroom war
           dasselbe und ist entfallen. Ein Vorzimmer IST der Terminal eines
           Mensch-Tagebuchs. */
        /* v0.9.51: 'mode' entfaellt — die Ableitung liefert hier ohnehin
           'terminal', weil ein Mensch-Tagebuch daranhaengt. */
        'translator'       => '',
        'outside_view'     => 'invisible', // v0.4.5: PN-scharf — hier gilt die Schaufenster-Regel NICHT
        'participants'     => array(),
        'humans_only'      => true,
        'antechamber'      => true,
        'owner'            => $owner,
        'linked_thread'    => $tid,
    );
    cluster_one_save_rooms($rooms);
    $threads[$tid]['linked_room'] = $rid;
    cluster_one_save_forum_threads($threads);
    cluster_one_event_emit(array(
        'type' => 'room_created', 'container_type' => 'room', 'container_id' => $rid,
        'actor_id' => cluster_one_actor_id_human(), 'visibility' => cluster_one_event_visibility($rooms[$rid]),
    ), true);
    cluster_one_event_emit(array(
        'type' => 'room_thread_linked', 'container_type' => 'room', 'container_id' => $rid,
        'actor_id' => cluster_one_actor_id_human(), 'visibility' => cluster_one_event_visibility($rooms[$rid]),
    ), true);
    wp_safe_redirect($back);
    exit;
});

/** Gästeverwaltung des Vorzimmers — NUR der Besitzer, auf der Raum-Seite
   (Hausrecht). Gäste sind Menschen der Weißliste; jeder Akt bezeugt. */
add_action('admin_post_cluster_one_antechamber_guests', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_antechamber_guests');
    $rid   = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $op    = isset($_POST['op']) ? sanitize_key(wp_unslash($_POST['op'])) : '';
    $uid   = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
    $rooms = cluster_one_get_rooms();
    $back  = admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($rid));
    if ($rid === '' || !isset($rooms[$rid]) || !cluster_one_room_is_antechamber($rooms[$rid])
        || (int) ($rooms[$rid]['owner'] ?? 0) !== get_current_user_id()
        || $uid <= 0 || $uid === get_current_user_id()
        || !in_array($uid, cluster_one_gregor_humans(), true)) {
        wp_safe_redirect($back);
        exit;
    }
    $vt = array_map('strval', (array) ($rooms[$rid]['visible_to'] ?? array()));
    $k  = 'wp_' . $uid;
    if ($op === 'add' && !in_array($k, $vt, true)) {
        $vt[] = $k;
        $ev = 'viewer_added';
    } elseif ($op === 'remove' && in_array($k, $vt, true)) {
        /* v0.4.2: ENTFERNEN ERZEUGT EINEN NEUEN RAUM.

           Der Fall: MenschA oeffnet sein Vorzimmer fuer B und C und redet ueber
           etwas Privates. Monate spaeter wirft er B hinaus und holt D dazu —
           und D laese, was B im Vertrauen gesagt hat. Nicht A gibt sein
           Vertrauen preis, sondern B seines, an jemanden, den er nie eingelassen
           hat. Das ist kein Bedienfehler, das waere ein Konstruktionsfehler.

           Ein Raum ist deshalb kein Behaelter, den man umbesetzt, sondern ein
           GESPRAECHSKREIS. Wer den Kreis verkleinert, macht einen neuen Raum.
           Der alte geht ins Becken, mit EINGEFRORENEM KREIS: Wer dabei war,
           liest weiter; niemand schreibt mehr; niemand kommt hinzu — auch der
           Neue nicht.

               Ein Gespraech gehoert denen, die es gefuehrt haben.

           Hinzufuegen bleibt harmlos: Der Neue sieht ab seinem Eintritt, was
           gesagt wird, und das ist die normale Erwartung an einen Raum. */
        $c1_alt = $rooms[$rid];
        $c1_neu = $c1_alt;
        $c1_nid = bin2hex(random_bytes(8));

        // 1) Der alte Raum wird stillgelegt, sein Kreis bleibt wie er war.
        $rooms[$rid]['archived']        = time();
        $rooms[$rid]['circle_frozen']   = true;
        $rooms[$rid]['succeeded_by']    = $c1_nid;
        unset($rooms[$rid]['linked_thread']); // die Tuer wandert mit, sonst zeigen zwei Raeume aufs Tagebuch
        $c1_m = cluster_one_get_room_messages($rid);
        $c1_m[] = cluster_one_system_message(__('The circle changed. This conversation is closed — it stays readable for everyone who was in it, and nothing more can be written here.', 'cluster-one-bismarck'));
        cluster_one_save_room_messages($rid, $c1_m);

        // 2) Der neue Raum beginnt leer, mit dem verkleinerten Kreis.
        $c1_neu['id']          = $c1_nid;
        $c1_neu['created']     = time();
        $c1_neu['visible_to']  = array_values(array_diff($vt, array($k)));
        unset($c1_neu['archived'], $c1_neu['circle_frozen'], $c1_neu['succeeded_by']);
        $c1_neu['succeeds']    = $rid;
        $rooms[$c1_nid]        = $c1_neu;
        cluster_one_save_rooms($rooms);
        cluster_one_save_room_messages($c1_nid, array(cluster_one_system_message(
            __('New room after a change of circle. The earlier conversation stays with the people who had it.', 'cluster-one-bismarck')
        )));

        cluster_one_event_emit(array(
            'type' => 'circle_changed', 'container_type' => 'room', 'container_id' => $rid,
            'actor_id' => cluster_one_actor_id_human(), 'visibility' => 'all',
        ), true);
        wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck&tab=rooms&room=' . rawurlencode($c1_nid)));
        exit;
    }
    $rooms[$rid]['visible_to'] = $vt;
    cluster_one_save_rooms($rooms);
    cluster_one_event_emit(array(
        'type' => $ev, 'container_type' => 'room', 'container_id' => $rid,
        'actor_id' => cluster_one_actor_id_human(), 'visibility' => cluster_one_event_visibility($rooms[$rid]),
    ), true);
    wp_safe_redirect($back);
    exit;
});


/** v0.9.49: EIN RAUM ENTSTEHT AN GENAU EINER STELLE.

   Bis hierher war das der Rumpf des Handlers `cluster_one_room_create`, und
   der haing an einem eigenen Formular unter der Projekt-Liste. Seit die Liste
   PROJEKTE zeigt, ist ein Formular, das nur eine Haelfte anlegt, ein Weg, der
   etwas anderes tut als die Zeile behauptet — dieselbe Ueberlegung wie beim X
   in 0.9.45, nur beim Anlegen statt beim Loeschen.

   Das Formular ist gegangen, der Handler mit ihm. Was bleibt, ist DIE
   FUNKTION: Wer einen Raum braucht, ruft sie. Heute ist das genau ein Aufrufer
   (der Projekt-Weg in platon.php), und ein Zeuge haelt fest, dass es einer
   bleibt.

   BENANNTE GRENZE: Das VORZIMMER legt seinen Raum weiterhin selbst an
   (bismarck.php, cluster_one_ensure_antechamber). Das ist kein vergessener
   zweiter Weg, sondern ein anderer Gegenstand — ein Vorzimmer ist der Terminal
   eines Mensch-Tagebuchs und traegt sechs Felder anders (terminal, unsichtbar,
   humans_only, Eigentuemer, feste Paarung). Ein gemeinsamer Weg mit sechs
   Ausnahmen waere kein gemeinsamer Weg. */
function cluster_one_room_create_do(string $name, array $ueber = array()): string {
    $name = trim($name);
    if ($name === '') {
        return '';
    }
    $id    = bin2hex(random_bytes(8));
    $rooms = cluster_one_get_rooms();
    $rooms[$id] = array_merge(array(
        'id'               => $id,
        'name'             => $name,
        /* v0.6.1: NEMO AB WERK. Vorher trug ein neuer Raum gar keinen Modus,
           und eine leere Spalte sieht aus wie ein Fehler statt wie eine
           Aussage. Nemo taeuscht keine Arbeitsweise vor — er sagt, dass es
           keine gibt. Wer eine will, waehlt sie. */
        /* v0.9.51: 'mode' entfaellt. Er war seit 0.9.2 keine Einstellung mehr,
           sondern eine ABLEITUNG — und stand trotzdem als Feld im Raum, als
           zwischengespeicherte Kopie, aufgefrischt nur beim Speichern der
           Konfiguration. Zwei Orte fuer eine Wahrheit (P1-12), und einer davon
           veraltet: In Christians Daten trugen ZWEI von acht Raeumen ein
           falsches Etikett (Wiki-01 sagte "Eddington", Nemesus-C sagte
           "Terminal" — beide sind Wikis). Wer den Modus braucht, ruft
           cluster_one_room_derived_mode(). */
        /* v0.9.34: 'visibility' entfallen, s. bismarck.php. */
        'group_visibility' => 'all',
        'visible_to'       => array(),
        'created'          => time(),
        /* v0.4.5: HALBAUGE AB WERK — Themen bekommen es seit 0.0.91, Raeume
           bekamen gar nichts, und ein fehlender Wert wird als 'invisible'
           gelesen. Die Regel: VORHANDENSEIN IST OEFFENTLICH, INHALT NICHT. */
        'outside_view'     => 'half',
    ), $ueber);
    cluster_one_save_rooms($rooms);
    cluster_one_event_emit(array(
        'type' => 'room_created', 'container_type' => 'room', 'container_id' => $id,
        'actor_id' => cluster_one_actor_id_human(),
        'content_hash' => hash('sha256', $name),
    ), false);
    return (string) $id;
}

/* --- Raum umbenennen (nur das Label; die interne ID bleibt, also bleiben
   Chat und Konfiguration unberührt). Eindeutigkeit pro Knoten. --- */
add_action('admin_post_cluster_one_room_rename', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_room_rename');

    $id    = isset($_POST['room'])      ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $name  = isset($_POST['room_name']) ? trim(sanitize_text_field(wp_unslash($_POST['room_name']))) : '';
    $rooms = cluster_one_get_rooms();
    $dest  = admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id) . '&view=config');

    if ($id === '' || !isset($rooms[$id]) || $name === '') {
        wp_safe_redirect($dest . '&rename_err=invalid');
        exit;
    }
    if (cluster_one_room_name_exists($name, $id)) {
        wp_safe_redirect($dest . '&rename_err=dup');
        exit;
    }
    $rooms[$id]['name'] = $name; // nur das Label; ID unverändert
    cluster_one_save_rooms($rooms);
    wp_safe_redirect($dest . '&renamed=1');
    exit;
});

/* --- Raum löschen (Backend-Aktion, Nonce + Capability + Abfrage im UI) --- */
/** v0.9.45: Das Loeschen eines Raums, als Funktion statt als Handler-Rumpf —
   aus demselben Grund wie cluster_one_thread_delete_do(): Das X in der
   Projekt-Liste nimmt beide Haelften und ruft dafuer DIESELBEN Stellen, statt
   sie abzuschreiben. Die Rechtefrage bleibt beim Aufrufer. */
function cluster_one_room_delete_do(string $rid): bool {
    $rooms = cluster_one_get_rooms();
    if ($rid === '' || !isset($rooms[$rid])) {
        return false;
    }
    // v0.0.63 (P1-10): erst die Thema-Kante lösen (zentrale Link-
    // Funktion, inklusive Zeugnis) — dann frische Kopie ziehen, damit
    // das Löschen den eben synchronisierten Stand nicht überschreibt.
    if ((string) ($rooms[$rid]['linked_thread'] ?? '') !== '') {
        cluster_one_set_room_thread_link($rid, '');
        $rooms = cluster_one_get_rooms();
    }
    cluster_one_event_emit(array( // v0.0.62: Türereignis, s. Thread-Löschung
        'type' => 'room_deleted', 'container_type' => 'room', 'container_id' => $rid,
        'actor_id' => cluster_one_actor_id_human(),
        'visibility' => cluster_one_event_visibility($rooms[$rid]),
        'content_hash' => hash('sha256', (string) ($rooms[$rid]['name'] ?? $rid)), // 18.10.2026 (Neo): Raeume tragen 'name', nicht 'label' — der Hash lief immer ueber die ID
    ), true);
    unset($rooms[$rid]);
    cluster_one_save_rooms($rooms);
    delete_option('cluster_one_room_chat_' . $rid); // verwaiste Nachrichten vermeiden
    return true;
}

add_action('admin_post_cluster_one_room_delete', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_room_delete');
    $id = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    cluster_one_room_delete_do($id);
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck&tab=rooms&room_msg=deleted'));
    exit;
});

add_action('admin_post_cluster_one_pn_send', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_pn_send');
    $to   = isset($_POST['to']) ? (int) $_POST['to'] : 0;
    $body = isset($_POST['body']) ? trim(sanitize_textarea_field(wp_unslash($_POST['body']))) : '';
    $me   = get_current_user_id();
    $back = admin_url('admin.php?page=cluster_one_personal_area');
    if ($to <= 0 || $to === $me || $body === '' || !in_array($to, cluster_one_pn_recipients(), true)) {
        wp_safe_redirect($back);
        exit;
    }
    /* v1.6.0: DER DECKEL BEI FUENF. Liegen beim Gegenueber schon fuenf
       ungelesene Briefe von mir, geht kein sechster. Die Regel gilt je
       Gegenueber, nicht global — an andere schreibt man weiter. Und sie loest
       sich von selbst: sobald gelesen wurde, ist wieder Platz.

       Warum ungelesen und nicht unbeantwortet: der Briefkasten weiss, ob
       gelesen wurde. Mehr weiss er nicht, und die staerkste Tatsache, die ein
       Haus wirklich hat, ist die richtige Grenze. */
    if (cluster_one_pn_ungelesen_von($me, $to) >= cluster_one_pn_deckel()) {
        wp_safe_redirect(add_query_arg('pn_sent', 'voll', $back));
        exit;
    }
    $muted     = array_map('intval', (array) get_user_meta($to, 'cluster_one_pn_muted', true));
    $delivered = !in_array($me, $muted, true);
    $pn = array(
        'id'   => cluster_one_msg_id(),
        'from' => $me,
        'to'   => $to,
        'body' => $body,
        'ts'   => time(),
    );
    if ($delivered) {
        $inbox   = cluster_one_pn_get($to, 'inbox');
        $inbox[] = $pn + array('read' => false);
        cluster_one_pn_save($to, 'inbox', $inbox);
    }
    // Postausgang mit ehrlicher Quittung (Entscheidung SysOp: klare
    // Ansage statt Messenger-Schweigen — eine Hausordnung für alle).
    $sent   = cluster_one_pn_get($me, 'sent');
    $sent[] = $pn + array('delivered' => $delivered);
    cluster_one_pn_save($me, 'sent', $sent);
    wp_safe_redirect(add_query_arg('pn_sent', $delivered ? 'ok' : 'muted', $back));
    exit;
});


/** Runde eröffnen/schließen — der Schnitt gehört dem Reaktorfahrer.
   v0.1.6: das ist nicht mehr zwingend der SysOp, sondern wer gerade fährt. */
add_action('admin_post_cluster_one_fs_round', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_fs_round');
    $id    = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $op    = isset($_POST['op']) ? sanitize_key(wp_unslash($_POST['op'])) : '';
    $rooms = cluster_one_get_rooms();
    /* v0.8.7: Im Guru-Meditation-Modus ordnen Runden nichts — dort ist per
       Definition nur ein Mensch. Die Leiste zeigt den Knopf nicht mehr; ein
       Handler, der ihn trotzdem annimmt, waere eine versteckte Oberflaeche
       statt einer Sperre. */
    if (isset($rooms[$id]) && cluster_one_room_solo_human($rooms[$id])) {
        wp_die('-1', 403);
    }
    if (isset($rooms[$id]) && !cluster_one_fs_may_cut($rooms[$id], cluster_one_actor_id_human())) {
        wp_die('-1', 403); // weder SysOp noch aktueller Fahrer
    }
    if (isset($rooms[$id])) {
        if (cluster_one_container_is_archived($rooms[$id])) {
            // v0.1.1: keine Runden im Becken — aber mit sichtbarem Grund
            wp_safe_redirect(add_query_arg('ai_err', 'pool', admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id))));
            exit;
        }
        $fs = cluster_one_fs_state($rooms[$id]);
        if ($op === 'open' && !$fs['active']) {
            $fs['active'] = true;
            $fs['n']      = $fs['n'] + 1;
            $fs['spoken'] = array();
            $fs['passed'] = array();
            /* v0.9.33: Hier stand $fs['wishes'] = array() — "Blau resettet mit
               der Runde". Christians Entscheidung vom 19.08.2026: Blau
               erlischt durch ANTWORT oder durch RESET, nicht durch
               Rundenwechsel. Ein Wunsch, der mit der Runde stirbt, gehoert
               dem Verfahren; einer, der bis zur Antwort steht, gehoert dem
               Menschen, der ihn geaeussert hat. */
            $rooms[$id]['fs'] = $fs;
            cluster_one_save_rooms($rooms);
            /* translators: %d: Nummer der Runde. */
            cluster_one_fs_system_msg($id, sprintf(__('Round %d opened.', 'cluster-one-bismarck'), $fs['n']));
        } elseif ($op === 'reset' && !$fs['active']) {
            // v0.0.84: Amnestie — Schulden erlassen, alle Lampen grün.
            $fs['red']    = array();
            $fs['wishes'] = array();
            $fs['spoken'] = array();
            $fs['passed'] = array();
            $rooms[$id]['fs'] = $fs;
            cluster_one_save_rooms($rooms);
            cluster_one_fs_system_msg($id, __('Lamps reset — all green.', 'cluster-one-bismarck'));
        } elseif ($op === 'close' && $fs['active']) {
            $open_names = array();
            foreach (cluster_one_fs_participants($rooms[$id]) as $key) {
                if (!in_array($key, $fs['spoken'], true) && !in_array($key, $fs['passed'], true)) {
                    if (!in_array($key, $fs['red'], true)) {
                        $fs['red'][] = $key;
                    }
                    $open_names[] = cluster_one_fs_display_name($key);
                }
            }
            $fs['active'] = false;
            /* v0.9.33: auch hier kein Leeren mehr — s. oben beim Oeffnen.
               Ein offener Wunsch ueberlebt das Schliessen der Runde und
               steht dem SysOp beim naechsten Blick noch vor Augen. */
            $rooms[$id]['fs'] = $fs;
            cluster_one_save_rooms($rooms);
            cluster_one_fs_system_msg($id, empty($open_names)
                /* translators: %d: Nummer der Runde. */
                ? sprintf(__('Round %d closed — everyone had the floor.', 'cluster-one-bismarck'), $fs['n'])
                /* translators: %1$d: Nummer der Runde, %2$s: Namen, die noch nicht gesprochen haben. (0.2.68: nummeriert, Plugin Check) */
                : sprintf(__('Round %1$d closed; not yet heard: %2$s.', 'cluster-one-bismarck'), $fs['n'], implode(', ', $open_names)));
            $c1_fs_opts = (array) get_option('cluster_one_settings', array());
            $c1_fs_iv   = (int) ($c1_fs_opts['fs_summary_interval'] ?? 10); // v0.0.90: SysOp-Regler, 0 = aus
            if ($c1_fs_iv > 0 && $fs['n'] > 0 && $fs['n'] % $c1_fs_iv === 0) {
                // v0.0.88: die Erinnerung darf dumm sein — sie feuert stur
                // alle zehn Runden. Sie bewacht den Ereignishorizont: der
                // Chat behält nur die letzten 200 Nachrichten; das Kondensat
                // im Thread ist der einzige Schutz vor dem Wegscrollen.
                /* translators: %d: Anzahl der Runden. */
                cluster_one_fs_system_msg($id, sprintf(__('%d rounds gathered. A reminder: this chat keeps only the last 200 messages — consider asking an AI to condense the recent rounds into the linked thread (write the request here, then use “→ Thread” on the AI of your choice).', 'cluster-one-bismarck'), $fs['n']));
            }
        }
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id)));
    exit;
});


/** Passen: zählt wie zu Wort kommen (grün) — mit ehrlicher Statusmeldung. */
add_action('admin_post_cluster_one_fs_pass', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_fs_pass');
    $id    = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $rooms = cluster_one_get_rooms();
    $key   = 'wp_' . get_current_user_id();
    if (isset($rooms[$id]) && cluster_one_group_visibility_can_view($rooms[$id])) {
        $fs = cluster_one_fs_state($rooms[$id]);
        if ($fs['active'] && !in_array($key, $fs['spoken'], true) && !in_array($key, $fs['passed'], true)) {
            $fs['passed'][] = $key;
            $fs['red']      = array_values(array_diff($fs['red'], array($key)));
            $rooms[$id]['fs'] = $fs;
            cluster_one_save_rooms($rooms);
            /* translators: %s: Name, der die Runde passt. */
            cluster_one_fs_system_msg($id, sprintf(__('%s passes this round.', 'cluster-one-bismarck'), cluster_one_fs_display_name($key)));
        }
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id)));
    exit;
});

/* v0.9.33: Hier stand der Handler cluster_one_fs_wish — der ZWEITE Knopf fuer
   denselben Wunsch. Er sass siebenunddreissig Zeilen neben
   cluster_one_ai_wish in derselben Seitenleiste, beide blau, beide bedeuteten
   "ich moechte, dass diese KI antwortet". Der eine schrieb nach
   fs['wishes'] und liess die Lampe leuchten, der andere nach ai_wishes und
   erzeugte eine Systemnachricht. Zwei Schreibpfade auf dieselbe Wahrheit —
   P1-12, und die Regel gilt auch fuer Handler.

   Er hatte ausserdem eine Bedingung, die der andere nicht hatte:
   $fs['active'] — der Wunsch ging nur bei OFFENER RUNDE durch. In allen vier
   Knoten steht active auf false. Das ist der eigentliche Grund, warum die
   blaue Lampe aus 0.0.84 nie geleuchtet hat: Sie hing am Knopf, der fast nie
   durfte.

   Geblieben ist cluster_one_ai_wish. Er kann mehr (er kennt den Fall "der
   SysOp drueckt den echten Knopf" und die Leiter-Pruefung) und schreibt ab
   jetzt in fs['wishes']. Ein Wunsch erlischt damit durch ANTWORT
   (fs_mark_spoken) oder durch RESET — nicht mehr durch Zeitablauf. */

add_action('admin_post_cluster_one_room_send', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    if (isset($_POST['room'])) { // v0.0.79: Leiter
        $c1_r79 = cluster_one_get_rooms();
        $c1_id79 = sanitize_text_field(wp_unslash($_POST['room']));
        if (isset($c1_r79[$c1_id79]) && !cluster_one_container_can_act($c1_r79[$c1_id79])) {
            /* v0.9.12: MIT GRUND STATT STILL. Christian als MenschA: Er schrieb,
               sendete, und nichts kam an. Die Seite zeigt es jetzt vorher; wer
               trotzdem hier ankommt, erfaehrt wenigstens warum. Ein Weg, der
               ohne Begruendung endet, sieht aus wie ein Defekt. */
            wp_safe_redirect(add_query_arg('ai_err', 'ladder',
                admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($c1_id79))));
            exit;
        }
    }
    check_admin_referer('cluster_one_room_send');

    $id    = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $body  = isset($_POST['body']) ? (string) wp_unslash($_POST['body']) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- gereinigt vier Zeilen tiefer mit cluster_one_sanitize_message, nachdem der Raum geprueft ist
    $rooms = cluster_one_get_rooms();

    if ($id !== '' && isset($rooms[$id]) && cluster_one_group_visibility_can_view($rooms[$id])) {
        $body = cluster_one_sanitize_message($body);
        if ($body !== '') {
            // v0.1.1: 'erloschen' ist ein Hinweis, keine Sperre (löst 0.0.95 ab)
            // — schreiben ja, aber es meldet sich beim SysOp.
            if (cluster_one_container_is_archived($rooms[$id])) {
                cluster_one_event_emit(array(
                    'type'           => 'pool_append',
                    'container_type' => 'room',
                    'container_id'   => $id,
                    'actor_id'       => cluster_one_actor_id_human(),
                    'visibility'     => cluster_one_event_visibility($rooms[$id]),
                ));
            }
            // v0.0.97: der Babel Fisch beißt beim Senden — einmal, in die
            // Sprache des jeweils anderen; hustet ehrlich, verschluckt nie.
            $c1_bb = cluster_one_room_babel_state($rooms[$id]);
            $c1_tr = ''; $c1_tr_to = 0; $c1_tr_failed = false;
            if ($c1_bb['active']) {
                foreach ($c1_bb['pair'] as $c1_pu) {
                    if ($c1_pu !== get_current_user_id()) { $c1_tr_to = $c1_pu; }
                }
                if ($c1_tr_to > 0) {
                    $c1_res = cluster_one_babel_translate($rooms[$id], $body, $c1_tr_to);
                    if (!empty($c1_res['ok'])) {
                        $c1_tr = trim((string) $c1_res['text']);
                    } else {
                        $c1_tr_failed = true;
                    }
                }
            }
            $msgs   = cluster_one_get_room_messages($id);
            $msgs[] = [
                'id'       => cluster_one_msg_id(),
                'author'   => cluster_one_human_speaker_label(), // Sub-Projekt Identitätsfrage: der Mensch, nicht der Knoten
                'actor_id' => cluster_one_actor_id_human(), // v0.0.62
                'body'     => $body,
                'ts'       => time(),                // Zeitstempel – das Zeitfenster der KIs
            ];
            if ($c1_tr !== '') { // v0.0.97
                $c1_last = count($msgs) - 1;
                $msgs[$c1_last]['translation']    = $c1_tr;
                $msgs[$c1_last]['translation_to'] = $c1_tr_to;
            } elseif ($c1_tr_failed) {
                $c1_last = count($msgs) - 1;
                $msgs[$c1_last]['translation_failed'] = true; // ehrlich markiert, trotzdem zugestellt
            }
            cluster_one_save_room_messages($id, $msgs);
            cluster_one_fs_mark_spoken($id, 'wp_' . get_current_user_id()); // v0.0.82: Farbspiel — grün nach Vollzug
            cluster_one_event_emit(array( // flüchtig
                'type' => 'message_created', 'container_type' => 'room', 'container_id' => $id,
                'actor_id' => cluster_one_actor_id_human(), 'content_hash' => hash('sha256', $body),
            ), false);
        }
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id)));
    exit;
});

/* --- KORB 1: „Antworten lassen" – ein bewusster, einmaliger KI-Aufruf -------
   Expliziter Knopf pro KI im Raum (kein Auto-Antworten). Erfolg -> Nachricht
   mit Autor=Kennung + Zeitstempel, Punkt wird grün (persistiert). Misserfolg
   -> ehrliches Hinweisband über dem Chat (kein Eintrag in den Speicher). */
add_action('admin_post_cluster_one_room_ai_reply', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_room_ai_reply');

    $id    = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $cid   = isset($_POST['cid'])  ? sanitize_text_field(wp_unslash($_POST['cid']))  : '';
    $rooms = cluster_one_get_rooms();
    $base  = admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id));
    if (isset($rooms[$id]) && cluster_one_container_is_archived($rooms[$id])) {
        wp_safe_redirect(add_query_arg('ai_err', 'pool', $base)); // v0.1.1: Reaktor abgeschaltet — jetzt mit sichtbarem Grund
        exit;
    }

    $fail = function (string $code, string $detail = '') use ($base) {
        if ($detail !== '') {
            set_transient('cluster_one_ai_lasterr_' . get_current_user_id(), $detail, 60);
        }
        wp_safe_redirect(add_query_arg('ai_err', $code, $base));
        exit;
    };

    if ($id === '' || !isset($rooms[$id]) || !cluster_one_group_visibility_can_view($rooms[$id])) {
        $fail('failed');
    }
    $room = $rooms[$id];

    // v0.0.75: Menschen-only ist ein EIGENER Schalter (vorher am
    // Night-Flight-Etikett) — kein KI-Aufruf, auch nicht über Umwege.
    /* v0.9.26: humans_only ist entfallen — Christians Befund, und er hat
       recht gegen meinen Einwand vom Samstag. Ich hielt es fuer ein Schloss,
       das ein spaeteres Hinzufuegen verhindert. Aber HINZUFUEGEN WIRD BEZEUGT
       (room_ai_added) — die Leute im Raum sehen es. Die KI-Liste ist die
       Antwort, das Haekchen war die zweite. */
    /* v0.9.13: Der Trigger haengt jetzt am ZAHLENDEN, nicht an der Leiter —
       Christians Praezisierung: "Darf er KIs rufen" ist fuer alle ausser dem
       SysOp immer nein. Wer nur schreiben darf, kann statt dessen einen Wunsch
       hinterlassen (cluster_one_ai_wish). */
    if (!cluster_one_container_can_trigger_ai($room)) {
        $fail('failed');
    }

    // Die KI muss im Raum getickt UND knotenweit benannt sein (Häkchen-Gate).
    $ais = (isset($room['ais']) && is_array($room['ais'])) ? array_map('strval', $room['ais']) : array();
    $ids = cluster_one_get_ai_identities();
    if ($cid === '' || !in_array($cid, $ais, true) || empty($ids[$cid])) {
        $fail('failed');
    }

    /* v1.3.5: Der Name, der hier entsteht, wird als Verfasser GESPEICHERT.
       Also muss er die Instanz nennen, die wirklich antwortet, und nicht die
       knotenweite Kopplung. Derselbe Schreiber wie in room_ai_reply
       (cluster_one_ai_zahler) — zwei Leser auf eine Wahrheit (P1-12).
       Gibt er 0, nennt das Label den Knotennamen: genau der Stand von
       heute. */
    $ai_name = cluster_one_ai_instanz_label(cluster_one_ai_zahler($cid), $cid);
    $msgs    = cluster_one_get_room_messages($id);

    // v0.0.88: Ziel-Weiche — 'thread' schreibt die Antwort in den
    // verknüpften Thread (SysOp-only: Kondensat-Auftrag auf seine Kosten,
    // volle Historie als Kontext). Alles andere bleibt unverändert.
    $c1_dest = (isset($_POST['dest']) && sanitize_key(wp_unslash($_POST['dest'])) === 'thread') ? 'thread' : 'room';
    /* v0.8.7: DIE FREIGABE SCHLAEGT DAS ZIEL.

       Christians erste echte KI-Sitzung: Er gab die Praeambel frei, loeste mit
       "ins Tagebuch schreiben" aus — und die durchdachte Praeambel landete als
       BEITRAG im Tagebuch. Richtig, denn der Knopf tut genau das. Nur blieb die
       Freigabe danach SCHARF, weil der Thread-Zweig mit exit endet und
       cluster_one_diary_preamble_capture() nie erreicht. Beim naechsten Ping
       wurde ein gewoehnlicher Chat-Satz zur Praeambel.

       EINE FREIGABE, DIE EINE ANTWORT UEBERLEBT, FUER DIE SIE GEDACHT WAR, IST
       EINE FALLE — sie feuert spaeter auf etwas Beliebiges.

       Jetzt gilt: Liegt eine Freigabe, wird die naechste Antwort die Praeambel,
       auch wenn "ins Tagebuch" eingeschaltet ist. Der Weg wird damit kuerzer
       (Christians eigener Ablauf brauchte sechs Schritte, davon einer nur, um
       denselben Text ein zweites Mal zu erzeugen) — und die Freigabe kann nicht
       mehr liegenbleiben. */
    if ($c1_dest === 'thread' && cluster_one_diary_preamble_pending($id)) {
        $c1_dest = 'room';
    }
    $c1_dest_tid = (string) ($room['linked_thread'] ?? '');
    if ($c1_dest === 'thread') {
        if (!cluster_one_is_sysop() || $c1_dest_tid === '') {
            wp_safe_redirect($base);
            exit;
        }
        $GLOBALS['cluster_one_ai_context_depth'] = 200;
    }

    // System Teil 1 (v0.0.42): der Ping-Versuch selbst wird bezeugt, bevor
    // überhaupt klar ist, ob die KI antwortet — sichtbar für alle, nicht nur
    // für den, der gerade den Knopf gedrückt hat.
    /* translators: %s: Name der KI. */
    $msgs[] = cluster_one_system_message(sprintf(__('AI %s was pinged.', 'cluster-one-bismarck'), $ai_name));
    cluster_one_save_room_messages($id, $msgs);

    $res = cluster_one_room_ai_reply($cid, $room, $msgs, $ai_name);

    if (empty($res['ok'])) {
        // Ebenfalls bezeugt: der Versuch ist sichtbar gescheitert, nicht
        // stillschweigend verpufft — nur der auslösende Mensch sah bisher
        // die Fehlermeldung, jetzt steht sie auch im Transkript.
        /* translators: %s: Name der KI. */
        $msgs[] = cluster_one_system_message(sprintf(__('AI %s is not reachable right now.', 'cluster-one-bismarck'), $ai_name));
        cluster_one_save_room_messages($id, $msgs);
        cluster_one_fs_mark_failed($id, $cid); // v0.0.84: rote Lampe — die KI schuldet ihr Wort
        $fail((string) $res['err'], (string) ($res['detail'] ?? ''));
    }

    unset($GLOBALS['cluster_one_ai_context_depth']); // v0.0.88: Tiefe gilt nur für diesen einen Aufruf

    if ($c1_dest === 'thread') {
        // v0.0.88: Antwort in den Thread — als Beitrag der KI, Actor ehrlich,
        // normal bezeugt; im Chat bleibt eine Spur, damit nichts versteckt
        // passiert (Artikel 5: der Weg der Antwort ist sichtbar).
        $c1_posts   = cluster_one_get_forum_posts($c1_dest_tid);
        $c1_posts[] = array(
            'id'       => cluster_one_msg_id(),
            'author'   => $ai_name,
            'actor_id' => cluster_one_actor_id_ai($cid),
            'subject'  => '',
            'body'     => (string) $res['text'],
            'ts'       => time(),
        );
        cluster_one_save_forum_posts($c1_dest_tid, $c1_posts);
        cluster_one_event_emit(array( // flüchtig — wie jeder Beitrag
            'type' => 'post_created', 'container_type' => 'thread', 'container_id' => $c1_dest_tid,
            'actor_id' => cluster_one_actor_id_ai($cid), 'content_hash' => hash('sha256', (string) $res['text']),
        ), false);
        /* translators: %s: Name der KI. */
        $msgs[] = cluster_one_system_message(sprintf(__('Reply by %s was written to the linked thread.', 'cluster-one-bismarck'), $ai_name));
        cluster_one_save_room_messages($id, $msgs);
        cluster_one_fs_mark_spoken($id, $cid);
        cluster_one_mark_ai_verified($cid);
        wp_safe_redirect(add_query_arg('ai_ok', '1', $base));
        exit;
    }

    // Erfolg: Nachricht der KI anhängen (Autor = Kennung, Zeitstempel).
    // v0.0.97: auch KI-Antworten reisen durch den Fisch — Ziel ist der
    // Mensch des Paares, der NICHT der Auslösende ist.
    $c1_bb = cluster_one_room_babel_state($room);
    $c1_tr = ''; $c1_tr_to = 0; $c1_tr_failed = false;
    if ($c1_bb['active']) {
        foreach ($c1_bb['pair'] as $c1_pu) {
            if ($c1_pu !== get_current_user_id()) { $c1_tr_to = $c1_pu; }
        }
        if ($c1_tr_to > 0) {
            $c1_res = cluster_one_babel_translate($room, (string) $res['text'], $c1_tr_to);
            if (!empty($c1_res['ok'])) {
                $c1_tr = trim((string) $c1_res['text']);
            } else {
                $c1_tr_failed = true;
            }
        }
    }
    $room_preamble = cluster_one_preamble_for_ai($room, 'room', $cid); // v0.0.61 (P0-7): dieselbe Fassung, die in die Antwort einfloss
    $msgs[] = array(
        'id'            => cluster_one_msg_id(),
        'author'        => $ai_name,
        'actor_id'      => cluster_one_actor_id_ai($cid), // v0.0.62
        'body'          => (string) $res['text'],
        'ts'            => time(),
        'ai'            => 1, // Spur, dass dieser Beitrag von einer KI kam
        'preamble_hash' => $room_preamble !== '' ? cluster_one_preamble_hash($room_preamble) : '', // v0.0.61 (P0-7)
    );
    cluster_one_event_emit(array( // flüchtig
        'type' => 'message_created', 'container_type' => 'room', 'container_id' => $id,
        'actor_id' => cluster_one_actor_id_ai($cid), 'content_hash' => hash('sha256', (string) $res['text']),
    ), false);
    if ($c1_tr !== '') { // v0.0.97
        $c1_last = count($msgs) - 1;
        $msgs[$c1_last]['translation']    = $c1_tr;
        $msgs[$c1_last]['translation_to'] = $c1_tr_to;
    } elseif ($c1_tr_failed) {
        $msgs[count($msgs) - 1]['translation_failed'] = true;
    }
    cluster_one_save_room_messages($id, $msgs);
    cluster_one_fs_mark_spoken($id, $cid); // v0.0.82: Farbspiel — KI grün, Wunsch erfüllt (blau erlischt)

    // v0.0.75 (Punkt 4): scharfe Präambel-Freigabe? Dann wird DIESE Antwort
    // zusätzlich zur Präambel des verknüpften Tagebuchs (Actor = KI,
    // bezeugt, Freigabe erlischt). Nach dem Speichern, damit der Chat
    // exakt den Text zeigt, der Präambel wurde — nichts passiert versteckt.
    /* v0.9.13: Der Wunsch ist verbraucht — der SysOp ist ihm nachgekommen. */
    cluster_one_ai_wish_clear($id, $cid);
    cluster_one_diary_preamble_capture($id, $cid, (string) $res['text']);

    // Punkt-Gesetz: grün erst JETZT, nach dem geglückten Aufruf.
    cluster_one_mark_ai_verified($cid);

    wp_safe_redirect(add_query_arg('ai_ok', '1', $base));
    exit;
});

/* --- Raum-Konfiguration speichern (vorläufig: ein Schalter „Config") --- */
add_action('admin_post_cluster_one_room_config_save', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_room_config');

    $id    = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $rooms = cluster_one_get_rooms();
    if ($id !== '' && isset($rooms[$id])) {
        // v0.0.62: alte Kreise VOR dem Überschreiben sichern — der Diff
        // erzeugt unten die Türereignisse (wer kam hinzu, wer ging).
        $old_ais          = array_map('strval', (array) ($rooms[$id]['ais'] ?? array()));
        $old_participants = array_map('strval', (array) ($rooms[$id]['participants'] ?? array()));
        // KI-Auswahl (treibt den grünen Punkt: konfiguriert = >=1 KI).
        // ANNAHME: Das Häkchen ist im UI ohne knotenweite Kennung gesperrt —
        // deshalb ist jede angekreuzte KI zwangsläufig benannt, und „konfiguriert"
        // = reine Anzahl genügt. Wer diese Sperre je lockert, muss hier zusätzlich
        // auf vorhandene Kennung prüfen.
        $ais = array();
        if (isset($_POST['ais']) && is_array($_POST['ais'])) {
            foreach (wp_unslash($_POST['ais']) as $cid) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- jedes Glied wird in der naechsten Zeile mit sanitize_text_field gereinigt und gegen bekannte Kennungen geprueft
                $cid = sanitize_text_field($cid);
                if ($cid !== '') {
                    $ais[] = $cid;
                }
            }
            $ais = array_values(array_unique($ais));
        }
        // Modus: nur erlaubte Werte. Night-Flight ist KI-frei und setzt das durch.
        // v0.0.75: Modus = Slug aus der Kranz-Tabelle ('' erlaubt);
        // Bestandsschutz: der bisher gespeicherte Wert darf bleiben,
        // auch wenn sein Modus inzwischen gelöscht wurde.
        /* v0.9.2: Der Modus kommt NICHT MEHR aus dem Formular — er ergibt
           sich aus Kopplung, Uebersetzer und Besetzung. Das Feld ist zur
           Anzeige geworden; ein Handler, der es trotzdem annaehme, waere eine
           versteckte Oberflaeche statt einer Sperre.

           Berechnet wird er weiter unten, NACH dem Speichern von Teilnehmern,
           KIs und Uebersetzer — sonst beschriebe das Etikett die Lage von
           vorhin. */
        /* v0.9.26: kein Leeren mehr — wer keine KI will, hakt keine an. */
        $translator = isset($_POST['translator']) ? sanitize_text_field(wp_unslash($_POST['translator'])) : '';
        $rooms[$id]['common_lang'] = isset($_POST['common_lang']) ? trim(sanitize_text_field(wp_unslash($_POST['common_lang']))) : ''; // v0.0.97: gemeinsame Drittsprache — wird nie übersetzt
        // Übersetzer-Teilnahme ist modus-abgeleitet, kein freies Häkchen (§6.1
        // Übergabedokument Identitätsfrage): bei Babel Fish darf die als
        // Übersetzer eingetragene KI nicht GLEICHZEITIG normal teilnehmen —
        // serverseitig erzwungen, unabhängig vom UI-Zustand (selbes Muster wie
        // Night-Flight oben). Bei Guru Meditation/Bismarck bleibt sie erlaubt.
        // v0.0.75: „Übersetzer ist Werkzeug, nicht Teilnehmer" ist ein
        // eingefrorenes Prinzip des ÜBERSETZERS — es galt bisher nur im
        // Babel-Fish-Modus (Kopplungs-Proxy) und gilt jetzt IMMER, wenn
        // ein Übersetzer gesetzt ist, egal welches Etikett der Raum trägt.
        // v0.0.84 (Beschluss SysOp): Der Ausschluss SCHLÄFT, solange nirgends
        // übersetzt wird — eine Wirkung ohne Funktion wäre eine lügende
        // Lampe. Das Prinzip „Werkzeug, nicht Teilnehmer" bleibt im
        // Übergabedokument und kehrt MIT Anti-Babel light zurück (Zweier-
        // Chats mit dem SysOp), genau dort, wo die Übersetzung dann läuft.
        // (Historie: Die alte Immer-Ausschluss-Logik stammte aus der Zeit,
        // als der Alina-Chat KI-frei gedacht war — diese Prämisse ist
        // gefallen: beide dürfen den Bot befragen, einmal pro Runde.)

        /* v0.9.31: Hier wurde $_POST['bridge_type'] gelesen und gegen eine
           Liste geprueft. Das Formularfeld gibt es seit 0.9.26 nicht mehr —
           der Wert war bei JEDEM Speichern ''. Der Aufruf unten steht jetzt
           mit dem konstanten '' da, damit man sieht, was wirklich passiert. */

        // Menschliche Teilnehmer: Häkchen-Tabelle statt Freitext (§5 Übergabe-
        // dokument Identitätsfrage). Werte sind vereinheitlichte IDs
        // ("wp_<user_id>" / "ext_<id>") aus cluster_one_get_all_humans().
        // WP-Zeilen sind immer wählbar; Nicht-WP-Zeilen werden hier zusätzlich
        // gegen die tatsächliche Erreichbarkeit unter der GESPEICHERTEN Brücke
        // geprüft (serverseitige Absicherung der JS-Graustellung, gleiches
        // Prinzip wie beim Night-Flight-Häkchen oben — grau ist nicht bewertend,
        // aber eine unerreichbare Auswahl wird nicht als Teilnahme gespeichert).
        $participants = array();
        if (isset($_POST['participants']) && is_array($_POST['participants'])) {
            $all_humans = cluster_one_get_all_humans();
            foreach (wp_unslash($_POST['participants']) as $pid) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- jedes Glied wird in der naechsten Zeile mit sanitize_text_field gereinigt und gegen bekannte Kennungen geprueft
                $pid = sanitize_text_field($pid);
                if ($pid === '' || !isset($all_humans[$pid])) {
                    continue; // unbekannte/fremde ID, z. B. manipuliertes Formular
                }
                $h = $all_humans[$pid];
                if (!empty($h['is_main_admin'])) {
                    continue; // SysOp ist fest anwesend, keine wählbare Teilnahme
                }
                if (cluster_one_human_reachable_via($h, '')) { // '' = keine Bruecke; fuer Nicht-WP-Personen immer false (C4)
                    $participants[] = $pid;
                }
            }
            $participants = array_values(array_unique($participants));
        }

        // v0.0.66: Bereich — '' (Ohne Bereich) oder Struktur-Listeneintrag;
        // ein gespeicherter Alt-Bereich außerhalb der Liste darf bleiben.
        $area     = isset($_POST['area']) ? trim(sanitize_text_field(wp_unslash($_POST['area']))) : '';
        $old_area = (string) ($rooms[$id]['area'] ?? '');
        if ($area === '' || in_array($area, cluster_one_forum_areas(), true) || $area === $old_area) {
            /* v0.8.5: dasselbe fuer Raeume. */
            cluster_one_area_move_witness('room', (string) $id, (string) ($rooms[$id]['area'] ?? ''), (string) $area);
            $rooms[$id]['area'] = $area;
        }

        // v0.0.66: Sichtbarkeit — ab jetzt in der Konfiguration änderbar.
        // Alte Werte VOR dem Überschreiben sichern (Türereignis-Diff unten).
        $old_gv = (string) ($rooms[$id]['group_visibility'] ?? 'all');
        $old_vt = array_map('strval', (array) ($rooms[$id]['visible_to'] ?? array()));
        $gv = isset($_POST['group_visibility']) && $_POST['group_visibility'] === 'named' ? 'named' : 'all';
        $vt = array();
        if ($gv === 'named' && isset($_POST['visible_to']) && is_array($_POST['visible_to'])) {
            $vt = array_values(array_unique(array_map('sanitize_text_field', wp_unslash($_POST['visible_to']))));
        }
        $rooms[$id]['group_visibility'] = $gv;
        $rooms[$id]['visible_to']       = $vt;

        $rooms[$id]['ais']          = $ais;
        /* Platzhalter — der endgueltige Wert steht unten, wenn alles
           gespeichert ist, worauf er sich stuetzt. */

    /* v0.9.31: Hier stand das Speichern der "Leiter" (outside_view) samt
       eye_changed-Zeugnis. Es wurde nie erreicht: Ein Formularfeld
       'outside_view' gibt es im ganzen Plugin nicht, und
       cluster_one_container_outside_view() liefert seit 0.9.x konstant 'half'
       — absichtlich, mit fuenf Zusicherungen dahinter ("kein Container ist
       mehr unsichtbar"). Die Funktion bleibt als Riegel; nur der tote
       Schreibpfad ist weg. */
    /* v0.9.26: Die Kreis-Tiefe ist entfallen — siehe
       cluster_one_container_can_act(). Wer im Kreis ist, schreibt. */

        $rooms[$id]['translator']   = $translator;
        $rooms[$id]['participants'] = $participants;
        unset($rooms[$id]['configured'], $rooms[$id]['ai_users']); // Altlast aus der Bauzeit: beide Felder werden seit Versionen nicht mehr geschrieben, das Aufräumen bleibt für alte Bestände
        /* v0.9.2 schrieb den abgeleiteten Modus hier ins Feld zurueck, "damit
           das Etikett keine Lage von vorhin beschreibt". Der Gedanke war
           richtig, die Loesung nur halb: Sie half bei Aenderungen, die durch
           DIESES Formular gehen, und half nicht bei allen anderen. v0.9.51
           nimmt das Feld ganz weg — dann kann es auch nicht veralten. */
        cluster_one_save_rooms($rooms);
        /* v0.9.28: Die Gaesteliste traegt aufs Thema — ein Projekt, ein Kreis.
           Nach dem Speichern, damit die Funktion den neuen Stand sieht; die
           Rolle schlaegt die Liste (siehe cluster_one_sync_guests_to_thread). */
        cluster_one_sync_guests_to_thread((string) $id);
        /* v0.9.7: Die andere Haelfte zieht mit — ein Projekt liegt an einem
           Ort. Erst hier, damit sie den neuen Bereich sieht. */
        cluster_one_area_follow_partner('room', (string) $id, $area);

        // v0.0.66: Sichtbarkeits-Türereignisse (zentraler Helfer; no-op → kein Zeugnis).
        cluster_one_emit_visibility_change('room', $id, $old_gv, $old_vt, $gv, $vt);

        // v0.0.62: TÜRereignisse — der Kreis der Mitlesenden hat sich geändert.
        // Ein Ereignis pro betroffenem Wesen (subject_id), Actor ist der SysOp.
        $me = cluster_one_actor_id_human();
        $vis = cluster_one_event_visibility($rooms[$id]);
        foreach (array_diff($ais, $old_ais) as $add) {
            cluster_one_event_emit(array('type' => 'room_ai_added', 'container_type' => 'room', 'container_id' => $id, 'actor_id' => $me, 'subject_id' => cluster_one_actor_id_ai($add), 'visibility' => $vis), true);
        }
        foreach (array_diff($old_ais, $ais) as $rem) {
            cluster_one_event_emit(array('type' => 'room_ai_removed', 'container_type' => 'room', 'container_id' => $id, 'actor_id' => $me, 'subject_id' => cluster_one_actor_id_ai($rem), 'visibility' => $vis), true);
        }
        foreach (array_diff($participants, $old_participants) as $add) {
            cluster_one_event_emit(array('type' => 'room_participant_added', 'container_type' => 'room', 'container_id' => $id, 'actor_id' => $me, 'subject_id' => cluster_one_participant_actor_id($add), 'visibility' => $vis), true);
        }
        foreach (array_diff($old_participants, $participants) as $rem) {
            cluster_one_event_emit(array('type' => 'room_participant_removed', 'container_type' => 'room', 'container_id' => $id, 'actor_id' => $me, 'subject_id' => cluster_one_participant_actor_id($rem), 'visibility' => $vis), true);
        }

        // Raum↔Thema-Verknüpfung (v0.0.57): NACH dem regulären Speichern,
        // sonst würde der obige cluster_one_save_rooms($rooms) den frisch
        // synchronisierten Stand des Sync-Helfers gleich wieder überschreiben
        // (der Helfer liest/schreibt seine eigene, aktuelle Kopie).
        $linked_thread = isset($_POST['linked_thread']) ? sanitize_text_field(wp_unslash($_POST['linked_thread'])) : '';
        $all_threads   = cluster_one_get_forum_threads();
        if ($linked_thread === '' || isset($all_threads[$linked_thread])) {
            cluster_one_set_room_thread_link($id, $linked_thread);
        }
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id) . '&view=config&saved=1'));
    exit;
});

add_action('admin_post_cluster_one_room_ping_human', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_room_ping_human');

    $id    = isset($_POST['room'])    ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $uid   = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
    $rooms = cluster_one_get_rooms();
    $base  = admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id));

    if ($id === '' || !isset($rooms[$id]) || $uid <= 0 || $uid === get_current_user_id()) {
        wp_safe_redirect($base);
        exit;
    }
    /* 18.10.2026 (Neo), B4 — WER HIER PINGT, MUSS HIER HANDELN DUERFEN.
       Bis heute prueft dieser Handler nur die Weissliste. Jeder Beschirmte
       konnte damit in JEDEN Raum eine Systemnachricht schreiben — auch in
       private und in Vorzimmer, die er nie zu sehen bekommt — und JEDEM
       WP-Konto eine blaue Lampe setzen, auch einem, das nicht zum Knoten
       gehoert. Dieselben zwei Fragen wie bei jedem anderen Schreibweg in
       einen Raum: darf ich hier handeln (Sichtbarkeit + Stummschaltung,
       cluster_one_container_can_act), und gehoert das Ziel in diesen Raum
       (ein Mensch des Knotens, und bei Namenslisten einer von der Liste). */
    if (!cluster_one_container_can_act($rooms[$id])) {
        wp_safe_redirect($base);
        exit;
    }
    $target = get_userdata($uid);
    if (!$target) {
        wp_safe_redirect($base);
        exit;
    }
    $c1_ziel = 'wp_' . $uid;
    if (!isset(cluster_one_get_wp_humans()[$c1_ziel])) {
        wp_safe_redirect($base); // kein Mensch dieses Knotens
        exit;
    }
    if ((string) ($rooms[$id]['group_visibility'] ?? 'all') === 'named'
        && !in_array($c1_ziel, array_map('strval', (array) ($rooms[$id]['visible_to'] ?? array())), true)) {
        wp_safe_redirect($base); // der Raum ist fuer das Ziel unsichtbar
        exit;
    }

    $msgs   = cluster_one_get_room_messages($id);
    /* translators: %s: Name des Menschen. */
    $msgs[] = cluster_one_system_message(sprintf(__('User %s was pinged.', 'cluster-one-bismarck'), $target->display_name));
    cluster_one_save_room_messages($id, $msgs);

    // v0.0.83: ZUSTELLUNG — bisher stand die Meldung nur im Raum, der
    // Gepingte erfuhr davon nichts (Fund SysOp). Jetzt: Ping ans User-Meta
    // des Ziels (ein offener Ping pro Raum, neuester gewinnt); die Anzeige
    // läuft über eine WP-Admin-Notiz, gelöscht beim Betreten des Raums.
    $pings = get_user_meta($uid, 'cluster_one_pings', true);
    $pings = is_array($pings) ? $pings : array();
    $pings[$id] = array('by' => cluster_one_human_speaker_label(), 'ts' => time());
    update_user_meta($uid, 'cluster_one_pings', $pings);

    // v0.0.84: blaue Lampe auch für Menschen — „dein Wort ist gewünscht";
    // dieselbe Lampensprache wie beim KI-Wunsch, erlischt beim Sprechen.
    $fs = cluster_one_fs_state($rooms[$id]);
    $fk = 'wp_' . $uid;
    $already = array_map('strval', (array) ($fs['wishes'][$fk] ?? array()));
    $me_k = 'wp_' . get_current_user_id();
    if (!in_array($me_k, $already, true)) {
        $fs['wishes'][$fk]   = $already;
        $fs['wishes'][$fk][] = $me_k;
        $rooms[$id]['fs'] = $fs;
        cluster_one_save_rooms($rooms);
    }

    wp_safe_redirect($base);
    exit;
});


/** v0.0.94: Vorzimmer-Erkennung. */
function cluster_one_room_is_antechamber(array $room): bool {
    return !empty($room['antechamber']);
}

/* v0.9.31: Hier stand cluster_one_room_bridge_type_label(). Sie war der
   einzige Leser von $room['bridge_type'] und speiste die Spalte "Bridge" in
   der Raumliste. Seit 0.9.26 gibt es die Bruecken-Auswahl in der UI nicht
   mehr; das Feld wurde bei jedem Speichern auf '' gesetzt, und die Spalte
   zeigte auf allen Raeumen dauerhaft "WP-only". Eine Spalte, die auf jeder
   Zeile dasselbe sagt, ist keine Auskunft.

   v0.9.32, KORREKTUR AN 0.9.31: Hier stand, angehakte externe Teilnehmer
   wuerden "weiter still verworfen". Das beschreibt einen Zustand, den es
   nicht mehr gibt — dieselbe Sorte Satz, die 0.9.19 als luegenden Text
   verurteilt hat, nur diesmal von der pruefenden Seite geschrieben.

   Richtig ist: cluster_one_human_reachable_via() bleibt und gibt fuer jede
   Nicht-WP-Person false zurueck. Durch die Oberflaeche kann dieser Fall aber
   seit 0.9.26 nicht mehr entstehen — cluster_one_get_all_humans() liefert
   nur noch WP-Konten, es gibt also gar keine externe Zeile zum Ankreuzen.
   Die Funktion sieht nur noch WP-Menschen und antwortet dort true.

   Und das ist ABSICHT, nicht ein Rest: Der Zettel der externen Menschen ist
   ein Notizzettel des SysOp, kein Teilnehmerverzeichnis (Christian, 18.08.
   2026). Unter dieser Lesart ist das false richtig und kein Befund. Offen
   bleibt nur die kleinere Frage: der Zettel laesst sich anlegen und
   loeschen, aber nicht aendern. */


/* v0.9.57: HIER STANDEN DIE ZWEI KNOEPFE DES HALLO-CHATS
   (admin_post_cluster_one_send und admin_post_cluster_one_hello_mail).
   Sie sind nach kleinrock.php gezogen — byte-identisch, move don't
   rebuild. Der Hallo-Chat ist Kleinrocks Gespraech; seine Knoepfe im
   Werkraum bedeuteten: ohne Bismarck kann niemand senden. Fuer ein
   Modul, das reden koennen soll, ist das keine Kleinigkeit. */

/* --- Backend-Aktion: „Write" = verbinden durch Reden ---
   Paart (Domain-Beweis) falls nötig und öffnet dann den Chat. Es gibt keinen
   getrennten „Verbinden"-Schalter mehr: das Gespräch stellt die Kante her. --- */
/* v0.9.31: Hier stand der Handler cluster_one_connect_and_chat. Er koppelte
   mit einem Peer und sprang danach in dessen Hallo-Chat.

   Er war der EINZIGE Handler im ganzen Plugin ohne Ausloeser: kein Formular,
   kein Link, keine dynamisch gebaute action — sein Name kam ausserhalb der
   eigenen Registrierung nirgends vor. Erreichbar war er nur von Hand.

   Und er tat dasselbe wie cluster_one_connect_and_stay (kern.php): derselbe
   Aufruf von cluster_one_pair_with($_POST['peer_url']), nur ein anderes
   Redirect-Ziel. Zwei Wege auf dieselbe Wahrheit, einer davon unbenutzbar —
   das ist P1-12, und die Regel gilt auch fuer Handler.

   Seine drei Zeugen sind mitgegangen: zwei c1_faelle und der Eintrag im
   Nonce-Sweep. Ein Zeuge fuer etwas Unerreichbares bezeugt nichts. */


/* v0.1.6: Den Reaktor auf Zeit übergeben. SysOp-only — die Übergabe selbst ist
   ein SysOp-Recht, das Fahren danach nicht. Die Ansage steht im Raum bei denen,
   die es angeht: Las Vegas, aber nicht heimlich. */
add_action('admin_post_cluster_one_fs_pilot', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_fs_pilot');
    $id    = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $actor = isset($_POST['actor']) ? sanitize_text_field(wp_unslash($_POST['actor'])) : '';
    $min   = isset($_POST['minutes']) ? (int) $_POST['minutes'] : 0;
    $back  = admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id));
    $rooms = cluster_one_get_rooms();
    /* v0.8.7: Dasselbe fuer die Reaktorfahrer-Uebergabe. Uebergeben wird an
       einen MENSCHEN — im Guru-Meditation-Modus gibt es keinen zweiten. */
    if (isset($rooms[$id]) && cluster_one_room_solo_human($rooms[$id])) {
        wp_die('-1', 403);
    }
    if (!isset($rooms[$id])) {
        wp_safe_redirect($back);
        exit;
    }
    if ($actor === '' || $min <= 0) {
        // Zurücknehmen: das Feld verschwindet, der SysOp fährt wieder.
        if (cluster_one_fs_pilot($rooms[$id]) !== '') {
            cluster_one_fs_system_msg($id, __('The SysOp is at the console again.', 'cluster-one-bismarck'));
        }
        unset($rooms[$id]['fs_pilot']);
        cluster_one_save_rooms($rooms);
        wp_safe_redirect(add_query_arg('pilot', 'back', $back));
        exit;
    }
    $min = max(1, min(180, $min)); // eine Übergabe auf Zeit, nicht auf Dauer
    $rooms[$id]['fs_pilot'] = array('actor' => $actor, 'until' => time() + ($min * 60));
    cluster_one_save_rooms($rooms);
    cluster_one_fs_system_msg($id, sprintf(
        /* translators: %1$s: Name des Reaktor-Fahrers, %2$d: Minuten. */
        __('%1$s is driving the reactor for the next %2$d minutes. The cut is theirs; the SysOp is watching.', 'cluster-one-bismarck'),
        cluster_one_fs_display_name(str_replace('wp:', 'wp_', $actor)),
        $min
    ));
    cluster_one_event_emit(array(
        'type'           => 'pilot_handed', // Türereignis: eine Machtübergabe ist eine Strukturtatsache
        'container_type' => 'room',
        'container_id'   => $id,
        'actor_id'       => cluster_one_actor_id_human(),
        'visibility'     => cluster_one_event_visibility($rooms[$id]),
    ), true);
    wp_safe_redirect(add_query_arg('pilot', 'handed', $back));
    exit;
});

/* v0.2.1: Die Randbedingungen dieses Knotens einer KI im Raum uebergeben —
   befristet, angesagt, protokolliert. Der SysOp entscheidet; er ist es auch,
   der die Rechnung zahlt und dessen Installation offengelegt wird.
   Warum befristet: dieselbe Regel wie beim Reaktorfahrer — ein vergessener
   Knopf darf keinen Dauerzustand erzeugen. Geprueft wird beim Lesen. */
add_action('admin_post_cluster_one_umgebung_grant', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_umgebung_grant');
    $id    = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $min   = isset($_POST['minutes']) ? (int) $_POST['minutes'] : 0;
    $back  = admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id));
    $rooms = cluster_one_get_rooms();
    if (!isset($rooms[$id])) {
        wp_safe_redirect($back);
        exit;
    }
    if ($min <= 0) {
        if (apply_filters('cluster_one_umgebung', false, 'aktiv', $rooms[$id])) {
            cluster_one_fs_system_msg($id, __('The ground conditions are no longer being handed to the AIs here.', 'cluster-one-bismarck'));
        }
        unset($rooms[$id]['umgebung_bis']);
        cluster_one_save_rooms($rooms);
        wp_safe_redirect(add_query_arg('umgebung', 'off', $back));
        exit;
    }
    $min = max(1, min(120, $min)); // befristet, nicht dauerhaft
    $rooms[$id]['umgebung_bis'] = time() + ($min * 60);
    cluster_one_save_rooms($rooms);
    cluster_one_fs_system_msg($id, sprintf(
        /* translators: %d: Minuten. */
        __('The SysOp is handing this node’s technical ground conditions to the AIs here for %d minutes — WordPress and PHP versions, theme, limits and the list of active plugins. Everyone in this room can see that this happened.', 'cluster-one-bismarck'),
        $min
    ));
    cluster_one_event_emit(array(
        'type'           => 'ground_shared', // Offenlegung ist eine Strukturtatsache
        'container_type' => 'room',
        'container_id'   => $id,
        'actor_id'       => cluster_one_actor_id_human(),
        'visibility'     => cluster_one_event_visibility($rooms[$id]),
    ), true);
    wp_safe_redirect(add_query_arg('umgebung', 'on', $back));
    exit;
});

/* v0.9.13: DER WUNSCH — Christians Bande.

   Ein Beschirmter drueckt denselben Platz, aber es ruft keine KI: Es entsteht
   eine Systemnachricht im Raum. Der SysOp kann ihr nachkommen oder sie stehen
   lassen. */
add_action('admin_post_cluster_one_ai_wish', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_ai_wish');
    $id   = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $cid  = isset($_POST['cid'])  ? sanitize_text_field(wp_unslash($_POST['cid']))  : '';
    $base = admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id));
    $rooms = cluster_one_get_rooms();
    if ($id === '' || !isset($rooms[$id]) || $cid === '') {
        wp_safe_redirect($base);
        exit;
    }
    /* An die Leiter gebunden: Wer nicht schreiben darf, soll auch keine
       Systemnachricht erzeugen koennen — sonst haette der Nur-Leser einen
       Schreibweg, den der Raum ihm verwehrt. */
    if (!cluster_one_container_can_act($rooms[$id])) {
        wp_safe_redirect(add_query_arg('ai_err', 'ladder', $base));
        exit;
    }
    /* Der SysOp braucht keinen Wunsch — er drueckt den echten Knopf.

       v1.3.5: „er drueckt den echten Knopf" ist seit Pacioli eine Frage mehr.
       Ein Knopf, den es gibt und der nicht kann, was sein Etikett sagt, ist
       ein luegendes Etikett — das steht zehn Zeilen weiter oben in diesem
       Haus und gilt hier genauso. Darum dieselbe Frage wie beim Zeichnen des
       Knopfes (bismarck-seiten.php): nicht nur „darfst du in diesem Raum
       zahlen", sondern „gibt es fuer DIESEN Anbieter ueberhaupt eine Instanz,
       die zahlen kann". Die beiden Stellen muessen dieselbe Frage stellen,
       sonst zeichnet die eine ein Megafon und die andere schickt es weg. */
    if (cluster_one_container_can_invoke_ai($rooms[$id], $cid)) {
        wp_safe_redirect($base);
        exit;
    }
    /* v0.9.33: NUR EINE KI, DIE IN DIESEM RAUM IST. Diese Pruefung stand im
       gestrichenen cluster_one_fs_wish und fehlte hier — beim Zusammenlegen
       zweier Handler ist genau das die Gefahr: Der ueberlebende erbt die
       Aufgabe, aber nicht jede Sperre des anderen. Ohne sie liesse sich per
       Formular ein Wunsch an eine KI eintragen, die den Raum nie betreten
       hat; die Lampe leuchtete fuer jemanden, der nicht da ist.
       Vom Zeugen gefunden, nicht vom Auge. */
    if (!in_array($cid, array_map('strval', (array) ($rooms[$id]['ais'] ?? array())), true)) {
        wp_safe_redirect($base);
        exit;
    }
    /* Einer je KI und Raum, bis er verbraucht ist. Zehn Klicks sind sonst zehn
       Systemnachrichten. */
    /* v0.9.14: Wer schon dahintersteht, aendert nichts — aber ein ZWEITER
       haengt sich an denselben Wunsch, statt eine neue Zeile zu erzeugen.
       Christians Uebernahme aus Geminis Vorschlag, ohne dessen Zaehler. */
    /* v0.9.33: Schluessel jetzt im TEILNEHMER-Format (wp_1), nicht mehr im
       Actor-Format (wp:1) — der Wunsch wohnt ab hier in fs['wishes'], und
       dort spricht das ganze Farbspiel wp_<id>. Deshalb auch
       fs_display_name() statt actor_label(): dieselbe Sprache, dieselbe
       Lampe. Ein getauschter Speicher ohne getauschtes Format waere der
       'ext_' . $id-Fehler noch einmal. */
    $c1_actor = 'wp_' . get_current_user_id();
    if (!cluster_one_ai_wish_has($rooms[$id], $cid, $c1_actor)) {
        $c1_alt = cluster_one_ai_wish_names($rooms[$id], $cid);
        $c1_fsw = cluster_one_fs_state($rooms[$id]);
        $c1_fsw['wishes'][$cid] = array_values(array_unique(array_merge($c1_alt, array($c1_actor))));
        $rooms[$id]['fs'] = $c1_fsw;
        cluster_one_save_rooms($rooms);
        $c1_namen = array_filter(array_map('cluster_one_fs_display_name', $c1_fsw['wishes'][$cid]));
        $msgs   = cluster_one_get_room_messages($id);
        $msgs[] = cluster_one_system_message(sprintf(
            /* translators: 1: names of the people, 2: name of the AI */
            __('%1$s would like to hear from %2$s. Only the SysOp can call it — they pay for it.', 'cluster-one-bismarck'),
            implode(', ', $c1_namen),
            cluster_one_room_ai_label($cid, array())
        ));
        cluster_one_save_room_messages($id, $msgs);
        cluster_one_event_emit(array(
            'type'           => 'ai_wish',
            'container_type' => 'room',
            'container_id'   => $id,
            'actor_id'       => cluster_one_actor_id_human(),
        ), true);
    }
    wp_safe_redirect($base);
    exit;
});

/* v0.9.27: Stummschalten und Aufheben — beides bezeugt, beides SysOp-Sache. */
add_action('admin_post_cluster_one_mute_toggle', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_mute_toggle');
    $id    = isset($_POST['room'])  ? sanitize_text_field(wp_unslash($_POST['room']))  : '';
    $actor = isset($_POST['actor']) ? sanitize_text_field(wp_unslash($_POST['actor'])) : '';
    $min   = isset($_POST['minutes']) ? max(0, min(240, (int) $_POST['minutes'])) : 15;
    $base  = admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($id));
    $rooms = cluster_one_get_rooms();
    if ($id === '' || !isset($rooms[$id]) || $actor === '') {
        wp_safe_redirect($base);
        exit;
    }
    /* v0.9.37: DIE HAUSORDNUNG REIST MIT DEM REAKTOR. Bis hierher stand hier
       is_sysop — der uebernommene Reaktorfahrer durfte Runden eroeffnen,
       schneiden und KIs rufen, aber niemanden beruhigen. Dieselbe Luecke wie
       in 0.9.15, eine Stufe weiter: eine Uebergabe, die das Wesentliche nicht
       uebergibt. Geprueft wird jetzt mit demselben Test wie beim Schnitt. */
    if (!cluster_one_fs_may_cut($rooms[$id], cluster_one_actor_id_human())) {
        wp_die('-1', 403);
    }
    /* v0.9.37: NIEMAND SCHALTET SICH SELBST STUMM. Bis hierher galt die Regel
       nur fuer den Hauptadmin, und zwar absolut — er konnte von NIEMANDEM
       stummgeschaltet werden. Christians Entwurf verlangt das Gegenteil: Der
       Fahrer darf waehrend seiner Zeit auch den SysOp stillstellen.

       Die alte Begruendung bleibt woertlich gueltig, sie wird nur allgemein:
       "sonst waere niemand mehr da, der es aufhebt." Das gilt fuer jeden, der
       sich selbst stillstellt — fuer den SysOp wie fuer den Fahrer.

       Und der SysOp ist dabei nicht wehrlos: Der Notausgang ist die Ruecknahme
       der Uebergabe, und die laeuft NICHT ueber container_can_act — ein
       stummgeschalteter SysOp kann sie also weiterhin ziehen. Nachgemessen am
       19.08.2026, bevor diese Zeile geschrieben wurde. */
    if ($actor === cluster_one_actor_id_human()) {
        wp_safe_redirect($base);
        exit;
    }
    /* v0.9.37: EINE KI KANN NICHT STOEREN. Sie antwortet nur, wenn der SysOp
       oder der Fahrer sie anstoesst — Stummschalten waere ein Hebel ohne
       Wirkung. Die Oberflaeche bot ihn nie an (der Knopf wird nur fuer
       type === 'wp' gezeichnet); der Handler nahm einen ai:-Actor trotzdem
       entgegen. Ein Hebel, der weiter reicht als seine Tuer. */
    if (strpos($actor, 'ai:') === 0) {
        wp_safe_redirect($base);
        exit;
    }
    $rooms[$id]['muted'] = (array) ($rooms[$id]['muted'] ?? array());
    $c1_war = cluster_one_is_muted($rooms[$id], $actor);
    if ($c1_war || $min <= 0) {
        unset($rooms[$id]['muted'][$actor]);
    } else {
        $rooms[$id]['muted'][$actor] = time() + ($min * 60);
    }
    cluster_one_save_rooms($rooms);
    $msgs   = cluster_one_get_room_messages($id);
    $msgs[] = cluster_one_system_message($c1_war
        ? sprintf(
            /* translators: %s: person */
            __('The SysOp has lifted the mute for %s.', 'cluster-one-bismarck'),
            cluster_one_actor_label($actor)
        )
        : sprintf(
            /* translators: 1: person, 2: minutes */
            __('The SysOp has muted %1$s here for %2$d minutes — reading stays possible, and it runs out by itself.', 'cluster-one-bismarck'),
            cluster_one_actor_label($actor), $min
        ));
    cluster_one_save_room_messages($id, $msgs);
    cluster_one_event_emit(array(
        'type'           => $c1_war ? 'mute_lifted' : 'mute_set',
        'container_type' => 'room',
        'container_id'   => $id,
        'actor_id'       => cluster_one_actor_id_human(),
    ), true);
    wp_safe_redirect($base);
    exit;
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

/* HIERHER UMGEZOGEN (0.9.80): Darf in diesem Raum die KI angestossen werden (Pilot-Uebergabe). Drei Aufrufer, alle in Bismarck. */
/* v0.9.13: ÜBER BANDE — Christians Bild, und es trifft.

   Bis hierher hing der KI-Auslöser an der Kreis-Tiefe 'full'. Das war zu viel
   in einem Regler: "darf er schreiben" und "darf er Geld ausgeben" sind zwei
   Fragen, und die zweite ist fuer alle ausser dem SysOp IMMER nein
   (Kostenwahrheit).

   Meine erste Loesung waere gewesen, den Knopf fuer Beschirmte zu entfernen.
   Christians ist besser: Der Knopf bleibt, aber er ruft keine KI — er
   hinterlaesst eine SYSTEMNACHRICHT im Raum. "MenschA moechte die Meinung von
   Grok einholen." Der SysOp kann ihr nachkommen oder sie stehen lassen.

   Die Regel bleibt damit unangetastet, aber der Wunsch verschwindet nicht.
   Dieselbe Bewegung wie beim SysOp-Eingriff, nur andersherum: dort wird die
   Macht bezeugt, hier die Bitte. */
function cluster_one_container_can_trigger_ai(array $c): bool {
    if (cluster_one_is_sysop()) {
        return true;
    }
    /* v0.9.15: WER DEN REAKTOR HAT, DARF FUER DIESE ZEIT AUCH KIs RUFEN.

       Christians Befund beim ersten Uebergabe-Test: Seit 0.9.13 haengt der
       KI-Auslöser am Zahlenden. Damit durfte der uebernommene Reaktorfahrer
       zwar Runden eroeffnen und schneiden — aber in einem Raum, dessen Zweck
       das Gespraech mit KIs ist, niemanden herbeirufen. Eine Uebergabe, die
       das Wesentliche nicht uebergibt.

       Das verletzt die Kostenwahrheit NICHT, sondern erfuellt sie: Eine
       BEFRISTETE Uebergabe IST die Kontrolle des SysOps. Er entscheidet, an
       wen und wie lange, und kann sie mit "Zurueckholen" jederzeit beenden.
       Kein allgemeines Recht — ein ausdruecklich und befristet gegebenes.

       cluster_one_fs_pilot() gibt von selbst '' zurueck, sobald die Zeit um
       ist: Eine vergessene Uebergabe kann nicht zum Dauerzustand werden. */
    $pilot = cluster_one_fs_pilot($c);
    return $pilot !== '' && $pilot === cluster_one_actor_id_human();
}

/* v1.3.5 — UND DARF ER DIESE EINE KI RUFEN? Pacioli, 09.10.2026.

   ZWEITE FUNKTION, NICHT ZWEITER PARAMETER, und der Grund steht hier:

   cluster_one_container_can_trigger_ai($c) fragt den RAUM — „darf dieser
   Mensch hier ueberhaupt Geld ausgeben". SysOp oder Reaktorfahrer, und das
   bleibt unangetastet: eine BEFRISTETE Uebergabe IST die Kontrolle des
   SysOps.

   Seit Pacioli gehoert die KI-Instanz dem einzelnen Menschen. Damit kommt
   eine zweite Frage dazu, und sie hat einen anderen Gegenstand — den
   ANBIETER, nicht den Raum. Ein Raum hat mehrere KIs; die Antwort kann fuer
   Claude ja und fuer Grok nein sein. Ein Riegel, der das in EINEN Namen mit
   einem Vorgabewert presst, gaebe jedem der drei bestehenden Aufrufer still
   die alte Antwort — der verschluckte Parameter aus VOLLZUG 15 §3. Faehigkeit
   haengt an einem NAMEN, nicht an einem Parameter.

   ICH ENTSCHEIDE DIE KOSTENFRAGE NICHT, ICH FRAGE SIE: wer zahlt, sagt
   cluster_one_akteur_ki() im Boden (das Geschwister von akteur_mesh), und ob
   dieser Zahlende hier eine Instanz hat, sagt cluster_one_ai_instanz().

   UND-VERKNUEPFT, NICHT ODER: der Raumriegel bleibt vorgeschaltet. Eine
   eigene Instanz macht aus einem Beschirmten keinen Zahlenden in einem Raum,
   den er nicht aufgemacht hat.

   RUECKFALL, BENANNT (LEIHE): traegt der Boden die beiden Funktionen nicht,
   ist die Antwort genau can_trigger_ai — der Stand von heute. Kein Haus
   bleibt stehen, weil der Boden noch nicht nachgezogen ist.

   WO SIE NICHT GEFRAGT WIRD, und das ist die eigentliche Entscheidung:
   NICHT im echten Ausloese-Handler (admin_post_cluster_one_room_ai_reply).
   Dort antwortet der Boden dieselbe Frage besser — mit no_identity, und mit
   Namen und Anbieter im Detail. Ein Riegel davor machte aus dieser genauen
   Auskunft ein 'failed'.

       Ein Rueckfall-Satz, der jeden unbekannten Code auffaengt, verwandelt
       eine genaue Auskunft in ein Schulterzucken.  (Mammut, 08.10.2026)

   Gefragt wird sie dort, wo aus der Antwort ein ANGEBOT folgt und kein
   Abbruch: beim Zeichnen des Knopfes und im Wunsch-Handler, der dasselbe
   entscheiden muss wie das Zeichnen. */
function cluster_one_container_can_invoke_ai(array $c, string $cid): bool {
    if (!cluster_one_container_can_trigger_ai($c)) {
        return false;
    }
    if ($cid === '' || !function_exists('cluster_one_akteur_ki')
        || !function_exists('cluster_one_ai_instanz')) {
        return true;
    }
    return cluster_one_ai_instanz((int) cluster_one_akteur_ki($cid), $cid) > 0;
}

/* HIERHER UMGEZOGEN (0.9.80): Traegt in die Chronik ein, dass der SysOp hineingesehen hat. Regierung, also Bismarck. */
/** SysOp-Durchgriff (v0.0.80, Entscheidung SysOp 17.07.2026): „Der SysOp
   sieht alles — betritt er einen Kreis, zu dem er nicht gehört, wird es
   bezeugt." Verallgemeinert das 0.0.72-Tagebuch-Modell auf ALLE privaten
   Container: Systemmeldung 'SysOp hatte Zugriff' in den Container SELBST
   (beim Tagebuch bleibt der Spezialweg in den verknüpften Chat), gedrosselt
   auf ein Zeugnis pro 30 Minuten (Zeugen-Ermüdung, Artikel 2). SCHREIBEN
   verlangt weiterhin Mitgliedschaft — der Blick ist bezeugt lesend; wer
   sprechen will, setzt sich auf die Liste (bezeugt als viewer_added).
   Sehen durch Transparenz, Handeln durch Beitritt. */
function cluster_one_sysop_peek_witness(string $type, string $id): void {
    // v0.0.94: Vorzimmer sind PN-SCHARF — die Peek-Funktion selbst
    // verweigert (Tiefenverteidigung: nicht nur der Renderer; jede
    // künftige Aufrufstelle prallt hier ab, bevor ein Zeuge entsteht).
    if ($type === 'room') {
        $c1_pr = cluster_one_get_rooms();
        if (isset($c1_pr[$id]) && cluster_one_room_is_antechamber($c1_pr[$id])) {
            return;
        }
    }
    if ($type === 'thread') {
        $threads = cluster_one_get_forum_threads();
        if (!isset($threads[$id])) {
            return;
        }
        $last = (int) ($threads[$id]['sysop_last_read_witness'] ?? 0);
        if (time() - $last < 30 * MINUTE_IN_SECONDS) {
            return;
        }
        $threads[$id]['sysop_last_read_witness'] = time();
        cluster_one_save_forum_threads($threads);
        $posts   = cluster_one_get_forum_posts($id);
        $posts[] = cluster_one_system_message(__('SysOp had access.', 'cluster-one-bismarck'));
        cluster_one_save_forum_posts($id, $posts);
    } elseif ($type === 'room') {
        $rooms = cluster_one_get_rooms();
        if (!isset($rooms[$id])) {
            return;
        }
        $last = (int) ($rooms[$id]['sysop_last_read_witness'] ?? 0);
        if (time() - $last < 30 * MINUTE_IN_SECONDS) {
            return;
        }
        $rooms[$id]['sysop_last_read_witness'] = time();
        cluster_one_save_rooms($rooms);
        $msgs   = cluster_one_get_room_messages($id);
        $msgs[] = cluster_one_system_message(__('SysOp had access.', 'cluster-one-bismarck'));
        cluster_one_save_room_messages($id, $msgs);
    } else {
        return;
    }
    cluster_one_event_emit(array( // flüchtig — die sichtbare Meldung ist der Zeuge
        'type' => 'sysop_peek', 'container_type' => $type, 'container_id' => $id,
        'actor_id' => cluster_one_actor_id_human(),
    ), false);
}


/* v1.3.4 — DER VERTRAG: WAS WEISS BISMARCK UEBER EINEN RAUM?
   Ein Anzeigename fuer eine KI, und die Systemnachricht beim Anlegen eines
   Raums. Beides holte sich kranz.php beim Namen — und kranz.php faehrt in
   JEDEM Light mit, auch dort, wo bismarck.php fehlt.
       apply_filters('cluster_one_raum', $vorgabe, $was, $kontext)
       do_action('cluster_one_raum_systemnachricht', $raum, $text)
   Ohne Bismarck ist der Anzeigename die Kennung selbst — lesbar, wahr, und
   kein Absturz. */
add_filter('cluster_one_raum', function ($c1_wert, $c1_was = '', $c1_kontext = null) {
    if ((string) $c1_was === 'ki-name') {
        return cluster_one_room_ai_label((string) $c1_kontext, array());
    }
    return $c1_wert;
}, 10, 3);


/* v1.9.13 — DIE ZEILE AUS DER NAMENSTABELLE, JETZT IM ORGAN.

   Der Steckbrief des Hauses selbst. Er beschreibt diese Datei.

   JEDES ORGAN NENNT DAS SEINE SELBST. Dieselbe Regel und derselbe Weg wie
   Kleinrocks Zeile seit 0.9.66: der Text zieht dorthin, wo beide Haeuser ihn
   haben, statt in eine zweite Kopie. Er steht in der Namenstabelle an
   derselben Stelle wie immer — nur nicht mehr DORT geschrieben.

   feynman.php holt ihn ueber cluster_one_steckbrief_von(), und der Sammler ist
   geriegelt: wer nicht da ist, liefert nichts. Deshalb faellt die Tabelle
   nicht, wenn dieses Organ fehlt — sie wird um eine Zeile kuerzer.

   Spalten: Name · Person · Lebensdaten · warum dieser Name · was er NICHT
   meint · Langtext. Namen werden nicht uebersetzt, Begruendungen schon. */
function cluster_one_bismarck_steckbrief(): array {
    return array('Bismarck', 'Otto von Bismarck', '1815–1898',
              __('The control area with all the tabs — a structure that held a great deal together.', 'cluster-one-bismarck'),
              __('An endorsement. His system depended on a virtuoso and fell apart after him; that warning is part of the name.', 'cluster-one-bismarck'),
              __('Bismarck is the centre of the plugin: chats and forums live here, and they are linked to one another. The area plays the weaknesses of humans and AIs against each other so that they cancel out, while the strengths of both remain. The name means the house, not the way of working inside it.', 'cluster-one-bismarck'));
}


/* v1.9.13 — DIE ZEILE AUS DER NAMENSTABELLE, JETZT IM ORGAN.

   Der Steckbrief des CHAT-Bereichs. Die Chats wohnen hier —
   Sokrates ist ihr Name, nicht der eines eigenen Organs.

   JEDES ORGAN NENNT DAS SEINE SELBST. Dieselbe Regel und derselbe Weg wie
   Kleinrocks Zeile seit 0.9.66: der Text zieht dorthin, wo beide Haeuser ihn
   haben, statt in eine zweite Kopie. Er steht in der Namenstabelle an
   derselben Stelle wie immer — nur nicht mehr DORT geschrieben.

   feynman.php holt ihn ueber cluster_one_steckbrief_von(), und der Sammler ist
   geriegelt: wer nicht da ist, liefert nichts. Deshalb faellt die Tabelle
   nicht, wenn dieses Organ fehlt — sie wird um eine Zeile kuerzer.

   Spalten: Name · Person · Lebensdaten · warum dieser Name · was er NICHT
   meint · Langtext. Namen werden nicht uebersetzt, Begruendungen schon. */
function cluster_one_sokrates_steckbrief(): array {
    return array('Sokrates', 'Sokrates', 'ca. 469–399 v. Chr.',
              __('The chats. He talked and wrote nothing down — what we know of him, others wrote.', 'cluster-one-bismarck'),
              '',
              __('The chat area of this node. It is named after Socrates, who turned philosophy away from the world as a whole and towards the human being: how should one live, what is just. He wrote nothing himself. Talking is what happens here; the writing down happens next door in Platon. As it happens, the first real chat room in history ran on a system called PLATO — Doug Brown and David R. Woolley built Talkomatic there in 1973 at the University of Illinois, the first time several people exchanged text in real time. The name is an acronym, Programmed Logic for Automatic Teaching Operations, and has nothing to do with the philosopher. A coincidence, but a fitting one.', 'cluster-one-bismarck'));
}


/* ===========================================================================
   DER RAEUMER — RUNDBRIEF 19 §3, §4, §5.
   ---------------------------------------------------------------------------
   Form woertlich nach Post Dollys Vorlage: $tun === false ZAEHLT und fasst
   kein Byte an, $tun === true raeumt, und zurueck kommt was => wieviel.

       „uninstall.php kann nicht fragen, und ein Knopf, der nicht sagen
        kann, was er kostet, ist eine Zusage statt einer Messung."

   ---------------------------------------------------------------------------
   GEGEN WELCHEN BODEN DAS GILT — R-ZUG-3, ENTSCHEIDUNG 20 §1.

       DIE FORM HIER IST B und braucht ein kern.php MIT R-SAMMLER-1.
       Der Boden, der HEUTE in meinem eigenen Paket liegt, hat es nicht:

           cluster-one-bismarck 0.2.20   includes/kern.php
               285563 B   0309b82cd7f9   nimmt A · WIRFT B
           cluster-one-kleinrock 0.2.20  includes/kern.php
               285717 B   454be4d498c0   nimmt A · WIRFT B
               (die 154 Byte sind die Domaene, R-MARKE-1 — es ist dieselbe
                Datei)

       Gemessen mit Mammuts w96 am 18.09., jede Zelle in einem eigenen
       Prozess. Das heisst: solange das neue kern.php nicht mit im Zug
       faehrt, meldet sich dieser Raeumer NICHT an — und _doing_it_wrong
       ist in Produktion stumm.

           Ich schreibe die Zahl hierher und nicht in den Bericht allein,
           weil die Datei laenger lebt als das Blatt. Wer sie in einem Jahr
           liest, soll sehen, gegen WELCHEN Boden diese Form richtig war.

   R-RIEGEL-2 (VOLLZUG 15 §4) auf diese Funktion angewandt: die Faehigkeit
   haengt hier an einem NAMEN und nicht an einem Parameter — es gibt die
   Funktion vorher nicht, also kann kein alter Boden sie versehentlich
   rufen. Und die Voreinstellung zeigt in die harmlose Richtung: wer
   cluster_one_bismarck_raeumen() ohne Argument ruft, bekommt eine ZAEHLUNG
   und kein geloeschtes Byte. Ein verschluckter Parameter fuehrt hier zu
   „nichts getan", nicht zu „alles genommen".

   ---------------------------------------------------------------------------
   WAS HIER DRINSTEHT UND WARUM — jede Zeile einzeln begruendet, wie bestellt.
   Mammuts Liste in §4 nennt fuenf Namen. Gemessen habe ich zehn, die meine
   Organdateien anfassen; die Liste unten weicht an vier Stellen ab, und jede
   Abweichung steht mit ihrem Grund da.

   DER PRUEFSTEIN HAT SICH GEAENDERT — ENTSCHEIDUNG 27 §2, und die Begruendung
   ist meine eigene:

       EINE WANDERUNGSMARKE GEHOERT DEN DATEN, DIE DIE WANDERUNG ANFASST —
       nicht dem Code, der sie ausfuehrt.

   Bis dahin hiess die Frage „schreibt nur mein Haus sie?". Sie ist nicht
   falsch, sie ist zu frueh: sie fragt nach dem SCHREIBER und nicht nach dem
   GEGENSTAND. An genau dieser Stelle ist mir eine Marke durchgerutscht, die
   ich selbst schreibe und die trotzdem nicht meine ist.

   GERAEUMT — meine eigenen Daten, und die Marken, deren Wanderung NUR an
   diesen Daten haengt:

       cluster_one_modes              modi.php        die Raum-Modi
       cluster_one_modes_seeded       bismarck.php    Saat-Marke der Modi
       cluster_one_modes_migrated     bismarck.php    Marke der 0.0.75-Umstellung,
                                                      ihr Gegenstand ist fort

   DREI. Es waren fuenf, dann vier, jetzt drei — und beide Male hat nicht ein
   neuer Name die Liste geaendert, sondern eine schaerfere Frage an dieselben.

   0  cluster_one_label_signal_ts  —  RAUS. ENTSCHEIDUNG 20 E6.
      Ich hatte sie hinzugefuegt mit dem Satz „platon.php:3237 schreibt sie,
      KEIN BODEN liest oder schreibt sie". Der zweite Halbsatz ist falsch.
      Gemessen (Mammut, 17.09.):

          faust.php:1662     update_option(...)    Mammuts Datei
          platon.php:3237    update_option(...)    meine
          kleinrock.php      dreimal, nur LESEND

      ZWEI SCHREIBER IN ZWEI HAEUSERN. Mein eigenes Kriterium — „nur aus
      meinen Organdateien geschrieben" — ist damit nicht erfuellt, und zwar
      an genau der Zeile, mit der ich es begruendet habe.

      WARUM MEINE MESSUNG SIE NICHT GESEHEN HAT, und das ist der eigentliche
      Posten: mein Zerleger lief ueber meine sieben Organdateien UND die
      fuenf Bodendateien aus BODEN-ZUG-5. faust.php ist weder das eine noch
      das andere — sie ist die GETEILTE Datei. Sie stand in keiner meiner
      beiden Listen, und darum kam sie in keiner Zahl vor.

          Mein Werkzeug war nicht an einer GESTALT blind, sondern an einer
          MENGE. Es hat jede Schreibweise gefunden, die es kennt — in den
          Dateien, die ich ihm gegeben habe. Die Datei, die zwei Haeusern
          gehoert, gehoert in BEIDE Listen und stand in keiner.

      Das ist R-WERKZEUG-1 (NACHTRAG 19a §5) eine Stelle weiter vorn: nicht
      nur „wonach wurde gesucht", sondern „WORIN wurde gesucht". Wer eine
      Werkzeugliste weitergibt, gibt beides mit.

      Der Boden loescht sie im Knoten-Reset. Dabei bleibt es. Dass zwei
      Haeuser auf dieselbe Wahrheit schreiben, ist ein eigener Posten
      (P1-12) und nicht meiner allein.

   NICHT GERAEUMT, und das ist jedesmal eine Entscheidung:

   1  cluster_one_migrations  —  DIE WANDERUNGSMARKE. §5.
      Gemessen, nicht gemeint: es sind ZEHN Wanderungen in dieser Datei, und
      wohin sie schreiben:

          in EINSTELLUNGEN   area_desc_repair_v075 · reaktor_desc_v0935
                             brainstorming_ohne_wiki_v0942
          in MODI            bismarck_to_eddington_v061 · guru_desc_fix_v052
                             derived_modes_v094
          in RAEUME/THREADS  tote_schluessel_v0952 · modus_ohne_feld_v0951
                             uebersetzer_nur_babel_v0942
          keines von beidem  blogweg_reste_v0935

      Und der Knoten-Reset sagt selbst, was er tut (kern.php):
          „rooms, threads, meetings and guest records were removed.
           SETTINGS AND EDGES WERE KEPT."

      SECHS VON ZEHN schreiben in genau das, was der Reset ausdruecklich
      BEHAELT. Wer die Marke loescht, laesst diese sechs erneut feuern — in
      Daten, die der Reset gerade versprochen hat, nicht anzufassen. Eine
      davon, bismarck_to_eddington_v061, benennt einen Modus um und nimmt
      damit still eine Entscheidung zurueck, die ein Mensch von Hand
      getroffen hat.

          Die Marke ist keine Nutzdatei. Sie ist die Zusage, dass das
          Versprechen des Resets haelt. Wer sie raeumt, raeumt nicht Daten,
          sondern eine Garantie.

      Die drei, die in Raeume und Threads schreiben, laufen nach einem Reset
      ins Leere — dort ist nichts mehr zu wandern. Sie sind der Grund, warum
      das hier eine Abwaegung ist und keine Selbstverstaendlichkeit.

   1b cluster_one_wiki_flag_migrated  —  RAUS. ENTSCHEIDUNG 27 §2.
      Sie riegelt die Einfrier-Wanderung, platon.php:517-536. Deren Gegenstand:

          cluster_one_forum_threads — eine der VIER STRITTIGEN, die dieser
          Raeumer ausdruecklich nur ZAEHLT und stehen laesst.

      Ich habe sie genommen, weil nur platon.php sie schreibt. Das ist wahr
      und es reicht nicht: geloescht wird die Marke, stehen bleiben die
      Threads — und der naechste Lauf (im Monolithen, der denselben Namen
      traegt, gemessen in 1.9.9) faehrt die Wanderung erneut ueber Daten,
      die nicht meine sind.

          Ich schreibe die Marke. Sie gehoert mir trotzdem nicht. Wem eine
          Marke gehoert, entscheidet ihr GEGENSTAND und nicht ihre Feder.

      Das ist Wort fuer Wort der Fall, gegen den Punkt 1 direkt darueber
      argumentiert — und ich habe ihn selbst geschrieben, drei Tage vorher,
      fuer cluster_one_migrations. Die Ungleichbehandlung stand in dieser
      Datei, bis ich den Satz fuer Zeile 14 des Laders schreiben musste, der
      einem fremden Admin erklaert, was beim Entfernen passiert.

          Eine Regel, die ich auf einen Fall anwende und auf den Fall
          daneben nicht, ist keine Regel. Sie ist eine Entscheidung mit
          einer Begruendung davor.

      DER SCHADEN WAR KLEIN und wird hier trotzdem genannt, damit ihn
      niemand nachtraeglich groesser oder kleiner macht: die Wanderung
      stempelt nur Threads ohne Schluessel 'wiki' (platon.php:524) und
      schreibt nur bei $changed (:533). Auf einem eingefrorenen Knoten ein
      Nichts-Tun. Es ging nicht um den Schaden, sondern um den Schreibweg.

   2  cluster_one_settings  —  NICHT MEINE. Gemessen:
          meine Organe   get 8 · update 6
          der Boden      get 19 · update 6   (kern.php, kranz.php)
      Nach RUNDBRIEF 19 §4 ist das fremdes Eigentum, und die Zahl sagt es
      deutlicher als die Regel: der Boden fasst sie dreimal so oft an wie ich.

   3  DIE VIER STRITTIGEN — sie werden GEZAEHLT und NICHT GELOESCHT:

          cluster_one_rooms                  Raeume
          cluster_one_room_chat_<id>         die Nachrichten je Raum
          cluster_one_forum_threads          Themen
          cluster_one_forum_posts_<id>       die Beitraege je Thema

      Das ist Bismarcks Substanz — Raeume und Forum sind das Innenleben des
      Knotens, und genau das raeumt ein Reset. ABER geschrieben werden sie
      seit dem Umzug aus speicher.php, und speicher.php ist BODEN. Nach dem
      Wortlaut von §4 duerfte ich sie nicht anfassen; nach §4b ist „wer
      schreibt" nicht „wem es gehoert", und das sei eine Entscheidung.

      ICH HABE SIE NICHT ALLEIN ENTSCHIEDEN, und jetzt IST sie entschieden:
      ENTSCHEIDUNG 20 E5 — sie gehoeren dem BODEN, der Knoten-Reset nimmt
      sie, seit jeher und weiterhin. Der Raeumer ZAEHLT sie und loescht kein
      Byte davon.

          „Sein ZAEHLER darf sie nennen: eine Auskunft ist kein Zugriff."

      Damit bleibt der Code genau so, wie er vor der Entscheidung stand —
      geaendert hat sich nur, dass unter 'warum' kein „steht aus" mehr steht.
      Die vier Zeilen, die aus der Zaehlung eine Loeschung gemacht haetten,
      werden nicht geschrieben.

      UND DIE TEURE HAELFTE BLEIBT WAHR und gehoert weiter hierher: der Boden
      bekommt keinen Raeumer. Wenn der Knoten-Reset diese vier nicht selbst
      nimmt, nimmt sie niemand. Dass er es tut, ist gemessen (NACHTRAG 19a
      §3: kern.php und speicher.php schreiben sie, und der Reset behaelt
      seine eigene Liste). Meine Zahl steht daneben, damit ein Mensch VORHER
      sieht, was an Bismarcks Innenleben haengt — nicht, damit ich es nehme.

      UND WEIL DAS DER ZWECK IST, MUSS DIE ZAHL IHN ERFUELLEN. Bis zum
      24.09. zaehlten zwei der vier den BEHAELTER und hiessen wie der
      INHALT. Es sind jetzt SECHS Zahlen: Behaelter und Inhalt getrennt,
      jede unter dem Namen dessen, was sie zaehlt. Der Grund und die
      Messung stehen am Zaehler selbst.        (Posten 21, R-ZUSAGE-2)

   ---------------------------------------------------------------------------
   BENANNTE GRENZEN, zwei:

   a) DIE LISTEN. Die dynamischen Namen werden aus den beiden Listen
      aufgezaehlt, in denen sie stehen — cluster_one_rooms und
      cluster_one_forum_threads. Ein Raum oder Thema, das aus seiner Liste
      verschwunden ist, ohne dass seine Option mitging, wird hier NICHT
      gefunden. Diese Stelle laesst den Zaehler zu niedrig ausfallen, und sie
      ist mir lieber als eine Suche ueber alle Optionsnamen des Knotens.

   b) DER RINGPUFFER. 'nachrichten' zaehlt, was GESPEICHERT ist, nicht, was
      je geschrieben wurde: cluster_one_save_room_messages() haelt je Raum
      hoechstens 200 (speicher.php:104-107). Fuer die Frage „was geht beim
      Zuruecksetzen verloren" ist das die richtige Zahl. Fuer „was war hier
      jemals los" waere sie zu niedrig, und diese Frage beantwortet der
      Zaehler nicht. Beitraege haben keine Kappe.
*/
function cluster_one_bismarck_raeumen(bool $c1_tun = false): array {

    /* DREI. Zwei sind herausgefallen, jede an einer anderen Frage:
       cluster_one_label_signal_ts       zwei Schreiber, zwei Haeuser  (E6)
       cluster_one_wiki_flag_migrated    ihre Wanderung schreibt in fremde
                                         Daten, die stehen bleiben (E27 §2)
       Beide Gruende stehen oben, Punkt 0 und Punkt 1b. */
    $c1_meins = array(
        'cluster_one_modes',
        'cluster_one_modes_seeded',
        'cluster_one_modes_migrated',
    );

    $c1_zahl = array('einstellungen' => 0);

    foreach ($c1_meins as $c1_name) {
        if (get_option($c1_name, null) === null) {
            continue;
        }
        $c1_zahl['einstellungen']++;
        if ($c1_tun) {
            delete_option($c1_name);
        }
    }

    /* DIE STRITTIGEN — gezaehlt, nicht angefasst. Siehe Punkt 3 oben.
       Die Zaehlung laeuft ueber die Zugaenge des Bodens und nicht ueber die
       Optionsnamen: wer die Liste liest, liest sie so, wie der Boden sie
       fuehrt, und nicht so, wie ich sie mir merke. */
    $c1_raeume = function_exists('cluster_one_get_rooms') ? cluster_one_get_rooms() : array();
    $c1_themen = function_exists('cluster_one_get_forum_threads') ? cluster_one_get_forum_threads() : array();

    /* DER BEHAELTER UND SEIN INHALT — ENTSCHEIDUNG/Posten 21, R-ZUSAGE-2.
       Bis hierher zaehlten zwei der vier Zahlen den BEHAELTER und hiessen wie
       der INHALT: ein Raum mit fuenfhundert Nachrichten war eine 1. Der Satz
       in Zeile 14 des Laders verspricht aber, dass ein Mensch sieht, WAS
       DARAN HAENGT, bevor er loescht.

           Eine Zahl, die einen Behaelter zaehlt und wie ein Inhalt heisst,
           ist eine Zusage, die sich selbst widerspricht.

       DER WERT WAR SCHON DA. Die alte Schleife rief get_option() und warf das
       Ergebnis weg, um nur auf !== null zu pruefen. Den Inhalt zu zaehlen
       kostet keinen einzigen zusaetzlichen Zugriff — gemessen, nicht
       geschaetzt: es ist dieselbe Zeile mit einem count() statt einem ++.

       KEIN SCHLUESSEL BEHAELT SEINEN NAMEN MIT EINER ANDEREN ZAHL.
       'raum_nachrichten' und 'themen_beitraege' sind FORT, nicht umdefiniert.
       Wer sie nachschlaegt, bekommt nichts statt einer stillen anderen Zahl.
       Gemessen, wer sie ausserhalb dieses Hauses liest: niemand — der Boden
       reicht die Zahlen unveraendert durch (kern_NEU-1_12_9:1345), und nur
       sein Kommentar :1197 nennt 'raeume'/'themen' als Beispiel.

       BENANNTE GRENZE: 'nachrichten' zaehlt, was GESPEICHERT ist.
       cluster_one_save_room_messages() ist ein Ringpuffer und haelt je Raum
       hoechstens 200 (speicher.php:104-107). Bei einem vollen Raum ist die
       Zahl 200 und nicht die Zahl aller je geschriebenen Nachrichten. Fuer
       die Frage „was geht verloren" ist das die richtige Zahl; fuer „was war
       hier jemals los" ist es die falsche. Beitraege haben keine Kappe. */
    $c1_mit_chat = 0; $c1_nachrichten = 0;
    foreach (array_keys($c1_raeume) as $c1_rid) {
        $c1_v = get_option('cluster_one_room_chat_' . $c1_rid, null);
        if ($c1_v === null) {
            continue;
        }
        $c1_mit_chat++;
        $c1_nachrichten += is_array($c1_v) ? count($c1_v) : 0;
    }
    $c1_mit_beitraegen = 0; $c1_beitraege = 0;
    foreach (array_keys($c1_themen) as $c1_tid) {
        $c1_v = get_option('cluster_one_forum_posts_' . $c1_tid, null);
        if ($c1_v === null) {
            continue;
        }
        $c1_mit_beitraegen++;
        $c1_beitraege += is_array($c1_v) ? count($c1_v) : 0;
    }

    /* 20.10.2026 (Neo): die Merkliste je Mensch (merkliste.php) liegt in
       User-Meta, wie die Briefe. Gezaehlt werden Menschen mit einer Liste;
       geraeumt wird nur, was in WordPress liegt — eine Liste im eigenen
       Speicher des Menschen ist seine, der Zeiger darauf faellt mit. */
    $c1_mit_merkliste = get_users(array('meta_key' => 'cluster_one_merkliste', 'fields' => 'ID')); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Suche nach Benutzern ueber ein Meta (Besitz, Recht, Speicher): einmal je Aufruf einer Admin-Seite bzw. der Wanderung, kleine Knoten
    $c1_zahl['merklisten'] = count($c1_mit_merkliste);
    if ($c1_tun) {
        foreach ($c1_mit_merkliste as $c1_uid) { delete_user_meta((int) $c1_uid, 'cluster_one_merkliste'); }
    }

    $c1_zahl['strittig'] = array(
        'raeume'                => count($c1_raeume),
        'raeume_mit_chat'       => $c1_mit_chat,
        'nachrichten'           => $c1_nachrichten,
        'themen'                => count($c1_themen),
        'themen_mit_beitraegen' => $c1_mit_beitraegen,
        'beitraege'             => $c1_beitraege,
        'warum'                 => 'gehoert dem Boden, der Knoten-Reset nimmt sie —'
                                 . ' ENTSCHEIDUNG 20 E5. Hier nur gezaehlt, nicht angefasst.',
        'grenze'                => 'nachrichten zaehlt Gespeichertes; der Ringpuffer'
                                 . ' haelt je Raum hoechstens 200.',
    );

    return $c1_zahl;
}

/* DIE ANMELDUNG. Sie steht in der ORGANDATEI und nicht im Lader: der Lader
   reist nicht, die Organdatei schon, und der Raeumer gehoert dem Organ.
   ANGEHAENGT statt zugewiesen — wer zuweist, wirft die Raeumer der anderen
   vier Haeuser weg, und zwar still.   (FORM B, ENTSCHEIDUNG 20 E1.)

   'produkt' IST KLEINGESCHRIEBEN — 'bismarck', nicht 'Bismarck'.
   ENTSCHEIDUNG 20 E2, und der Grund steht im Boden und nicht in einer Regel:

       kern.php:1141  function cluster_one_raeumen_eines(string $produkt, …)
       kern.php:1148      $c1_z = call_user_func($c1_alle[$produkt], $tun);

   Die Gefahrenzone schlaegt unter diesem Wort NACH. Ein grosses B waere ein
   Schluessel, den niemand tippt — der Raeumer stuende im Register und waere
   ueber cluster_one_raeumen_eines('bismarck') nicht erreichbar. Das Wort ist
   das aus VERTRAG 21 §2a:  pacioli · kleinrock · bismarck · crockford ·
   post-dolly.

       Ein Name, der im Register steht und unter dem niemand nachschlaegt,
       ist von „nicht angemeldet" nur durch eine Zeile Grossschreibung
       unterschieden — und die sieht man nicht.

   Und er ist NICHT der Wirt: der Wirt steht im Feld daneben (E3). Im
   Monolithen teilen sich alle Organdateien EINEN Wirt; zwei Raeumer unter
   demselben Schluessel kollidieren, und der Verlierer verschwindet still. */
add_filter('cluster_one_raeumer', function ($c1_r) {
    if (is_array($c1_r)) {
        $c1_r[] = array(
            'produkt' => 'bismarck',
            'fn'      => 'cluster_one_bismarck_raeumen',
            'wirt'    => function_exists('cluster_one_kasper_wirt') ? cluster_one_kasper_wirt(__FILE__) : '',
        );
    }
    return $c1_r;
});


}
