<?php
/**
 * Cluster One — Modul kern (DER SCHNITT, v0.0.100 -> Module).
 * Reiner Umzug aus cluster-one.php; jede Funktion byte-identisch zur Baseline.
 */
defined('ABSPATH') || exit;

/* v1.0.1 — DER RIEGEL UM DEN GANZEN BODEN.

   Bis 1.0.0 stand er nur um kasper.php. Der Kommentar dort sagte, das sei
   "die einzige Datei mit einem function_exists-Waechter, weil sie spaeter in
   JEDEM Light liegt". Das stimmte, solange nur Kasper doppelt lag. Ein Light,
   das allein laufen soll, traegt aber den GANZEN Boden.

   Nachgestellt am 26.08.2026, zwei Plugins, beide mit ungeriegeltem kern.php.
   Das Light laedt zuerst ('-' = 0x2D vor '/' = 0x2F, H43):

       PHP Fatal error: Cannot redeclare function cluster_one_config()

   Weisser Bildschirm auf einem Produktivknoten, in dem Moment, in dem das
   erste bodentragende Light neben Mammut aktiviert wird.

   ZWEITE HAELFTE, ebenfalls nachgestellt: Riegeln allein reicht NICHT. Traegt
   das zuerst geladene Light einen GESCHNITTENEN Kern, faellt Mammut in
   "Call to undefined function". Waehrend der Koexistenz muss der Boden
   geriegelt UND byte-gleich sein — und kein Light darf kern.php schneiden,
   solange Mammut auf dem Knoten liegt.

   Die erste Funktion ist der Waechter: wer cluster_one_config() schon hat,
   hat den ganzen Kern. */
/* VERTRAG 27 §4 — DIE MELDUNG STEHT VOR DEM RIEGEL.
   Bis hierher stand cluster_one_hier() INNERHALB des Riegels: eine verdraengte
   Kopie dieser Datei schwieg dann auch ueber ihre eigene Existenz. Die
   Bedingung wird jetzt EINMAL gelesen und traegt beide Folgen — die Meldung
   und den Riegel.
       Ein Melder kennt sich selbst. „Verdraengt" ist eine BEZIEHUNG, und
       Beziehungen gehoeren dem Sammler.            (Maillart, 11.09.)
   Die Fassung bleibt LEER: Kasper liest sie am Wirt (H118), und eine
   geliehene Nummer ist schlimmer als keine. */
$cluster_one_kern_laeuft = !function_exists('cluster_one_config');

if (function_exists('cluster_one_hier')) {
    /* Der Riegel um den AUFRUF bleibt: wer kern.php allein laedt, soll
       keine Leiche sehen. Dass in einem echten Paket gemeldet WIRD, misst
       meldung.php — kasper.php steht in jedem Lader davor. */
    cluster_one_hier('kern', '', $cluster_one_kern_laeuft);
}

if ($cluster_one_kern_laeuft) {

/* v0.9.72: Kern und Speicher melden sich NICHT auf Dateiebene — sie laden
   vor Kasper. Ihre Meldung setzt cluster-one.php ab, sobald Kasper da ist. */


function cluster_one_config(string $const_name, string $option_key): string {
    if (defined($const_name) && constant($const_name) !== '') {
        return (string) constant($const_name);
    }
    $opts = get_option('cluster_one_settings', []);
    return isset($opts[$option_key]) ? (string) $opts[$option_key] : '';
}

/** Ist ein echter Name gesetzt? Pflicht fürs Mesh: ohne Namen keine Paarung,
   kein Zeiger, kein Chat-Autor. Verhindert „unnamed" im Netz. */
function cluster_one_has_name(): bool {
    return cluster_one_config('CLUSTER_ONE_NODE_ID', 'node_id') !== '';
}

/** Weißliste: WP-User-IDs, die explizit als "in Cluster One dabei" gelistet
   sind (Kranz → Kleinrock). Getrennt von SysOp/KI-Kennungen/Collective
   — die sind automatisch drin, brauchen keinen Listenplatz. */
function cluster_one_whitelist_user_ids(): array {
    $opts = get_option('cluster_one_settings', array());
    $ids  = isset($opts['whitelist']) && is_array($opts['whitelist']) ? $opts['whitelist'] : array();
    return array_values(array_unique(array_map('intval', $ids)));
}

/** Zugriffs-Schwelle fürs gesamte Plugin (v0.0.46, ersetzt die Ausschluss-
   Logik aus v0.0.39 komplett): eine echte Weißliste statt einer Ausschluss-
   liste. Grundsatz: "Du bist entweder drin oder nicht — und wenn du drin
   bist, bist du es im Rahmen von Cluster One." Automatisch drin: SysOp,
   jede WP-User hinter einer KI-Kennung, der Collective-Account. Alle
   anderen nur, wenn explizit auf der Weißliste (Kranz → Kleinrock) —
   unabhängig davon, welche WP-Rolle sie sonst tragen. Das schützt auch vor
   dem Fall, der die alte Ausschlussliste nie abdecken konnte: eine
   jahrealte WP-Installation mit zehn, zwanzig, dreißig bestehenden Usern,
   die alle automatisch reingerutscht wären. Bewusste Ausnahme bleibt: der
   RESET-Handler (Gefahrenzone) hängt weiterhin an echtem
   current_user_can('manage_options'), unabhängig von dieser Funktion. */
function cluster_one_can_access(int $user_id = 0): bool {
    $uid = $user_id > 0 ? $user_id : get_current_user_id();
    if ($uid <= 0 || !get_userdata($uid)) {
        return false;
    }
    if ($uid === cluster_one_main_admin_user_id()) {
        return true;
    }
    if (in_array($uid, cluster_one_ai_identity_user_ids(), true)) {
        return true;
    }
    if ($uid === cluster_one_collective_user_id()) {
        return true;
    }
    return in_array($uid, cluster_one_whitelist_user_ids(), true);
}

/** 03.11.2026 (Neo), Boden 1.23.0 — RUNDE OMEGA: DIE MITGLIEDER, DIE EIN HAUS FREISCHALTEN KANN.
 *  Nemesus: „alles da steuern, wo wir es brauchen, mit jeweils einem PlugIn, was das PlugIn selbst betrifft." Crockford (und
 *  bis heute Pacioli) vergaben Rechte je Mensch in der WordPress-Benutzerliste, nach der Regel vom 30.10.: ein Ort fuer eine
 *  Sorte Entscheidung. Seit Phi kann jedes Haus allein liegen; ein SysOp, der nur Crockford hat, kennt die Gewohnheit nicht und
 *  sucht die Freigabe bei Crockford. Also zieht die Freigabe auf die Seite des Hauses — und die LISTE, wen man freischalten
 *  kann, steht hier EINMAL: die Weissliste (cluster_one_whitelist_user_ids) ohne den SysOp (er hat alles per Amt), ohne
 *  KI-Kennungen (eine KI bekommt kein Menschenrecht) und ohne das Collective-Konto. Nur Konten, die es gibt, nach Anzeigename.
 *  Zwei Haeuser, eine Liste; welches Recht am Haken haengt, sagt das Haus. */
function cluster_one_freigabe_mitglieder(): array {
    $c1_aus = array();
    $c1_weg = array_merge(array(cluster_one_main_admin_user_id(), cluster_one_collective_user_id()), cluster_one_ai_identity_user_ids());
    foreach (cluster_one_whitelist_user_ids() as $c1_uid) {
        if ($c1_uid <= 0 || in_array($c1_uid, $c1_weg, true)) {
            continue;
        }
        $c1_u = get_userdata($c1_uid);
        if ($c1_u) {
            $c1_aus[] = $c1_u;
        }
    }
    usort($c1_aus, static function ($c1_a, $c1_b) {
        return strcasecmp((string) $c1_a->display_name, (string) $c1_b->display_name);
    });
    return $c1_aus;
}

/** v0.0.61 (P0-1): echte WP-Capability für Cluster One. 'cluster_one_access'
   wird via map_meta_cap auf cluster_one_can_access() abgebildet — die
   Seitenregistrierung nutzt sie statt 'read', damit Menü UND Seiten für
   Nicht-Mitglieder gar nicht erst existieren. */
add_filter('map_meta_cap', function (array $caps, string $cap, int $user_id) {
    if ($cap !== 'cluster_one_access') {
        return $caps;
    }
    return cluster_one_can_access($user_id) ? array('exist') : array('do_not_allow');
}, 10, 3);

/** v0.0.61 (P0-1, Defense-in-depth): erste Zeile JEDER Seiten-Render-
   Funktion — damit der alte Kommentar („die eigentliche Schwelle ist
   can_access in jeder Seite") nie wieder unbelegt im Raum steht. Nutzt
   den WP-Kern-Text ohne Textdomain, der Kern übersetzt selbst.

   v1.12.8 — UND DAS IST DIE EINE STELLE, AN DER DIE ZAEHLUNGEN AUSEINANDER-
   GEHEN. Hollerith hat sie am 18.09. gefunden: sein Katalogwerkzeug zaehlt
   sie NICHT (sie hat keine Domaene von uns), xgettext zaehlt sie MIT (es
   sieht nur den Ruf). Daher liegen seine Zahlen bei jedem Paket, das einen
   Boden traegt, um GENAU EINS unter meinen — bei pacioli, das keinen
   traegt, stimmen sie exakt.

       Beide Zaehlungen sind richtig. Sie zaehlen zwei verschiedene
       Mengen: „was WIR uebersetzen muessen" und „was uebersetzbar
       gerufen wird".                                  (R-MESSUNG-2)

   Eine Domaene bekommt sie NICHT. Sie bekaeme damit fuenfmal denselben
   unuebersetzten Satz — einmal je Paket — an der Stelle, an der der Kern
   ihn in jeder Sprache schon fertig hat. Die Abweichung ist billiger als
   ihre Beseitigung, und ab hier ist sie benannt statt nur wahr. */
function cluster_one_page_gate(): void {
    if (!cluster_one_can_access()) {
        wp_die(esc_html__('Sorry, you are not allowed to access this page.'), 403); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- Satz des Core, mit Absicht ohne Domain: der Core-Katalog uebersetzt ihn
    }
}

/** Actor-ID des gerade handelnden Menschen. Rechte hängen ausschließlich
   hieran; der Anzeigename bleibt loser String (§3 Identitätsfrage bleibt
   für die ANZEIGE gültig, nur die RECHTE wandern auf die ID). */
/* v0.7.3: ZWEI FORMATE, ZWEI ZWECKE — und das ist Absicht, kein Versehen.

   'wp:7'  — die ACTOR-ID. Wer gehandelt hat: Ereignisse, Autorschaft,
             Eigentum (wilde_owner), Ersteller. Kommt von hier.
   'wp_7'  — der LISTEN-SCHLUESSEL. Wer dabei sein darf: visible_to,
             Teilnehmer, Gregor-Marker.

   Die beiden begegnen sich an keiner Stelle; kein Feld traegt beide. Eine
   Vereinheitlichung waere 27 Codestellen plus eine Migration auf laufenden
   Knoten — und jede uebersehene Stelle braeche STILL eine
   Sichtbarkeitspruefung: Jemand saehe sein eigenes Tagebuch nicht mehr, ohne
   dass irgendwo etwas rot wird. Nutzen: Ordnung. Risiko: die Fehlerklasse, die
   am schwersten auffaellt.

   Deshalb steht es hier statt im Aufraeum-Backlog: damit es niemand beim
   naechsten Durchgang "repariert". */
function cluster_one_actor_id_human(): string {
    return 'wp:' . get_current_user_id();
}

/** Actor-ID einer KI-Kennung. */
function cluster_one_actor_id_ai(string $cid): string {
    return 'ai:' . $cid;
}

/** DB-Schema-Version des Ereignis-Logs — GETRENNT von der Plugin-Version,
   damit Schema-Änderungen auch dann greifen, wenn die Plugin-Version
   gleich bleibt (Datei-Tausch ohne Reaktivierung). */
define('CLUSTER_ONE_DB_VERSION', '3'); // v0.9.35: Spalte summary_count dazu

/** Tabellenname des Ereignis-Logs. */
function cluster_one_events_table(): string {
    global $wpdb;
    return $wpdb->prefix . 'cluster_one_events';
}

/** Anlage/Upgrade der Ereignis-Tabelle. Die EINZIGE eigene Tabelle des
   Plugins, bewusst: das Zeugnis-Log darf die nicht-atomaren Options-
   Schreibpfade (P1-12) nicht erben — ein Log, das sich selbst
   überschreiben kann, ist als Zeuge befangen. Atomarer INSERT statt
   Read-Modify-Write. */
function cluster_one_events_install(): void {
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $table   = cluster_one_events_table();
    $charset = $wpdb->get_charset_collate();
    dbDelta("CREATE TABLE {$table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        event_id VARCHAR(32) NOT NULL,
        type VARCHAR(64) NOT NULL,
        container_type VARCHAR(16) NOT NULL DEFAULT '',
        container_id VARCHAR(64) NOT NULL DEFAULT '',
        actor_id VARCHAR(120) NOT NULL DEFAULT '',
        actor_label VARCHAR(190) NOT NULL DEFAULT '',
        subject_id VARCHAR(120) NOT NULL DEFAULT '',
        origin VARCHAR(64) NOT NULL DEFAULT 'local',
        visibility VARCHAR(190) NOT NULL DEFAULT '',
        content_hash VARCHAR(64) NOT NULL DEFAULT '',
        grant_ref VARCHAR(64) NOT NULL DEFAULT '',
        retention_class VARCHAR(16) NOT NULL DEFAULT '',
        summary_count INT UNSIGNED NOT NULL DEFAULT 0,
        origin_event_id VARCHAR(32) NOT NULL DEFAULT '',
        created_at INT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY  (id),
        KEY container (container_type, container_id),
        KEY actor (actor_id),
        KEY type (type)
    ) {$charset};");
}

/** DER Eventvertrag (Übergabedokument Teil II.2). Jede Handlung läuft
   hier durch; der Hook ist die leere Steckdose für Brücke, Gregor,
   Labels und Bewerbungsmappe. PERSISTIERT werden nur Türereignisse
   ($persist=true) — Artikel 2 (Bezeugungsschwelle) und Artikel 4
   (Datensparsamkeit): content_hash statt Inhalt, minimaler Snapshot,
   lokal pro Knoten, nichts wird gespeichert, was verfallen müsste. */
function cluster_one_event_emit(array $e, bool $persist = false): array {
    $row = array(
        'event_id'        => cluster_one_msg_id(),
        'type'            => (string) ($e['type'] ?? ''),
        'container_type'  => (string) ($e['container_type'] ?? ''),
        'container_id'    => (string) ($e['container_id'] ?? ''),
        'actor_id'        => (string) ($e['actor_id'] ?? ''),
        'actor_label'     => '',
        'subject_id'      => (string) ($e['subject_id'] ?? ''),
        'origin'          => (string) ($e['origin'] ?? 'local'),
        'visibility'      => (string) ($e['visibility'] ?? ''),
        'content_hash'    => (string) ($e['content_hash'] ?? ''),
        'grant_ref'       => (string) ($e['grant_ref'] ?? ''),
        'retention_class' => $persist ? 'structural' : 'ephemeral', // Artikel 4.3: heute nur zwei Klassen — persistiert wird ausschließlich structural
        /* v0.9.35: summary_count wurde seit jeher UEBERGEBEN und hier
           fallengelassen — die Tabelle hatte keine Spalte dafuer. Ueber
           person_erased steht der Satz "Es haelt fest, DASS und WIE VIEL";
           das WIE VIEL fiel bei jeder Loeschung lautlos heraus.
           EIN KOMMENTAR, DER MEHR VERSPRICHT ALS DER CODE HAELT, IST
           DIESELBE SORTE LUEGENDER TEXT WIE EINE FALSCHE OBERFLAECHE. */
        'summary_count'  => (int) ($e['summary_count'] ?? 0),
        'origin_event_id' => (string) ($e['origin_event_id'] ?? ''), // Echo-Bremse: ID des auslösenden Fremd-Ereignisses (Brücke/Peer), sonst leer
        'created_at'      => time(),
    );
    $row['actor_label'] = cluster_one_actor_label($row['actor_id']);

    // Persist-Ehrlichkeit: ein Zeugnis darf nicht LAUTLOS ausfallen. Die
    // Nutzhandlung wird nicht zurückgerollt (sie ist bereits geschehen und
    // meist selbst sichtbar), aber der Ausfall wird protokolliert und im
    // Rückgabewert getragen — künftige Konsumenten können darauf reagieren.
    $event = $row;
    $event['persist_requested'] = $persist;
    $event['persisted']         = false;
    if ($persist) {
        global $wpdb;
        $ok = $wpdb->insert(cluster_one_events_table(), $row); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- die Zeugnis-Tabelle des Hauses; insert ist ihr einziger Schreibweg
        $event['persisted'] = ($ok !== false);
        if ($ok === false) {
            error_log('Cluster One: Türereignis "' . $row['type'] . '" (' . $row['container_type'] . '/' . $row['container_id'] . ') konnte NICHT persistiert werden: ' . $wpdb->last_error); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- der Ausfall eines Zeugnisses darf nicht stumm sein (Persist-Ehrlichkeit, Kommentar darueber); kein Debug-Rest
        }
    }
    do_action('cluster_one_event_created', $event, $persist);
    return $event;
}

/** Sichtbarkeits-Snapshot eines Containers, minimal (Artikel 4.2): nur die
   Klasse ('all'/'named'), nie die Namensliste — Actor-IDs statt Menschen,
   und auch die nur, wo ein Ereignis sie als subject_id ohnehin trägt. */
function cluster_one_event_visibility(array $container): string {
    return (string) ($container['group_visibility'] ?? '');
}

/** Teilnehmer-ID (Sichtbarkeits-Schreibweise der Häkchen-Tabellen) →
   Actor-ID. Zentrale Übersetzung, damit der Eventvertrag EINE Schreibweise
   spricht: wp_17 → wp:17, ext_x → external:x. */
function cluster_one_participant_actor_id(string $pid): string {
    if (strpos($pid, 'wp_') === 0) {
        return 'wp:' . substr($pid, 3);
    }
    if (strpos($pid, 'ext_') === 0) {
        return 'external:' . substr($pid, 4);
    }
    return $pid;
}

/** Tabelle auch bei Datei-Updates ohne Reaktivierung sicherstellen
   (gleiches Sicherheitsnetz-Muster wie beim Cron unten). */
add_action('admin_init', function () {
    if (get_option('cluster_one_db_version') !== CLUSTER_ONE_DB_VERSION) {
        cluster_one_events_install();
        update_option('cluster_one_db_version', CLUSTER_ONE_DB_VERSION, false);
    }
});

/* v0.2.3: Freigabe pro Steckbrief-Feld. Ein leeres Feld reist ohnehin nicht;
   ein gefuelltes reist nur, wenn der SysOp es ausdruecklich freigegeben hat.
   Konstanten in wp-config gelten als Freigabe: Wer ein Feld dort haendisch
   setzt, hat die Entscheidung schon getroffen. */
/* --- v0.3.12: FARBE PRO PERSON -----------------------------------------------
   Beim Termin sollen sich die Punkte unterscheiden lassen. Die Farbe wird aus
   der Nutzer-Kennung abgeleitet, damit sie stabil bleibt — ein Mensch behaelt
   seine Farbe ueber Monate, ohne dass sie irgendwo gespeichert werden muesste.

   Farbe ALLEIN reicht nicht: Wer farbenblind ist oder die Seite ueberfliegt,
   braucht daneben ein Kuerzel. Deshalb liefert das hier beides, und der volle
   Name steht im Titel. */
function cluster_one_person_color(string $key): string {
    $palette = array('#1a7f37', '#2271b1', '#b26b00', '#8c3fb5', '#a0344a', '#0f7b7b', '#7a6a00', '#4a5568');
    $n = 0;
    foreach (str_split($key) as $c) { $n = ($n * 31 + ord($c)) % 100003; }
    return $palette[$n % count($palette)];
}

/** Kuerzel eines Menschen: die Initialen seines Anzeigenamens, sonst die ID. */
function cluster_one_person_initials(string $key): string {
    $uid = (int) preg_replace('/\D/', '', $key);
    $u   = $uid > 0 ? get_userdata($uid) : null;
    $name = $u ? trim((string) $u->display_name) : '';
    if ($name === '') {
        return strtoupper(substr($key, -2));
    }
    // Kein mbstring: auf einem WP-Host nicht garantiert (Lehre aus 0.2.3).
    // preg_ mit /u schneidet UTF-8-sicher, ohne die Erweiterung zu brauchen.
    // Nur Buchstaben: "Christian (SysOp)" ergab sonst "C(" — der eigene Zeuge
    // hat das gefunden, bevor es jemand im Kalender sah.
    $teile = preg_split('/[^\p{L}]+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
    $ini   = '';
    foreach (array_slice((array) $teile, 0, 2) as $t) {
        if (preg_match('/^./u', $t, $m1)) { $ini .= $m1[0]; }
    }
    return function_exists('mb_strtoupper') ? mb_strtoupper($ini, 'UTF-8') : strtoupper($ini);
}

/** v0.3.6: Der Mensch hinter dem Knoten, kurz. Erst der FREIGEGEBENE
   Steckbrief-Name, sonst nichts. "Herkunft" war ein Wort fuer zwei Angaben —
   welcher Knoten zeigt es, und welcher Mensch steht dahinter.

   DIESE NOTIZ STAND BIS 1.12.15 ACHTUNDDREISSIG ZEILEN UND ZWEI FUNKTIONEN
   WEITER OBEN — ueber cluster_one_person_color(), mit der sie nichts zu tun
   hat. Sie ist hierher gezogen, und das ist die halbe Reparatur:

       Eine Notiz, die nicht an ihrer Sache steht, veraltet unbemerkt —
       wer die Sache aendert, liest sie nicht mit.   (Kleinrock, 23.09.)

   v1.12.15 — ZWEI FUNDE AUS DEM FREMDENLAUF, und beide sind aelter als er.
   Christian hat am 23.09. die neun offenen Wege von aussen gefragt, an zwei
   laufenden Knoten. Auf einer Firmenseite, die Christian nur BETREUT, stand
   an einem tokenlosen Endpunkt sein Name.

   ERSTER FUND — DER RUECKFALL.  Bis hierher fiel diese Funktion ohne
   gesetzten Nicknamen auf den display_name des Hauptadministrators zurueck.
   Bei einer gewoehnlichen Installation ist das der Klarname, manchmal der
   Anmeldename — und niemand hat ihn hingeschrieben.

       Ein Feld, das den Menschen nennt, traegt nur, was jemand
       ausdruecklich hineingeschrieben hat. Ein Rueckfall auf ein
       Konto ist keine Angabe, sondern ein Fund.

   ZWEITER FUND — DIE ZWEITE TUER.  Er ist Kleinrocks, und er ist der
   groessere: derselbe Wert hatte zwei Wege nach draussen und an jedem eine
   andere Erlaubnis.

       cluster_one_self_profile()  ->  cluster_one_shares('nickname')  FREIGABE
       cluster_one_sysop_label()   ->  cluster_one_config(...)         KEINE

   Wer einen Nicknamen eintrug und ihn bewusst NICHT freigab, veroeffentlichte
   ihn trotzdem — auf /meetings und /labels.

       Zwei Wege zu einem Wert sind erlaubt.               (P1-12)
       Zwei verschiedene Erlaubnisse fuer denselben Wert sind es nicht.

   Das Wort "freigegeben" stand seit v0.3.6 im Satz darueber. Die Funktion hat
   nie danach gefragt. Es ist derselbe Fehler wie der, den Kleinrock am selben
   Abend an /meetings gefunden hat — ein Kommentar, den sein Code ueberholt
   hat, eine Etage tiefer.                                 (R-ZUSAGE-1)

   WAS ES KOSTET, und es ist sichtbar: auf einem Knoten, dessen SysOp
   share_nickname nie angehakt hat, verschwindet 'sysop' aus beiden
   oeffentlichen Antworten. Beide Leser drueben lesen ($data['sysop'] ?? '')
   und brechen nicht. Eine wp-config-Konstante gilt weiter als Freigabe
   (v0.2.3, der defined()-Zweig in cluster_one_shares) — wer den Namen dort
   von Hand setzt, hat die Entscheidung schon getroffen. */
function cluster_one_sysop_label(): string {
    if (!cluster_one_shares('nickname')) {
        return '';
    }
    return cluster_one_config('CLUSTER_ONE_NICKNAME', 'nickname');
}

/* --- v1.4.0: DIE AUSKUNFTSSCHICHT — ein Knoten befragt sich selbst ----------
 *
 * WOZU. Ein Produkt, das allein auf einem Knoten liegt, kann heute nichts
 * ueber diesen Knoten erfahren. Post Dolly haengt deshalb zweimal fest:
 *
 *   H119 wartet auf H112   — ein Light kann sich nicht als Light melden
 *   H144                   — ein Knoten ohne Kleinrock behauptet im offenen
 *                            Internet, ein Knotenpunkt zu sein
 *
 * Beide Male dieselbe Luecke, und beide Male hat Post Dolly sie richtig
 * gemeldet, statt sich etwas auszudenken.
 *
 * DREI ANTWORTEN, NICHT ZWEI. Das ist der ganze Punkt:
 *
 *     'fehlt'   niemand kann diese Frage hier beantworten
 *     'nein'    das zustaendige Organ ist da und sagt nein
 *     'ja'      das zustaendige Organ ist da und sagt ja
 *
 * Der bestehende Vertrag cluster_one_netz beantwortet "gibt es Nachbarn?" und
 * wird als "gibt es ein Netz?" gelesen — ein frisch gepaarter Knoten ohne
 * Kanten faellt dabei auf die falsche Seite. Genau diese Verschmelzung ist
 * der Fehler, den diese Schicht nicht wiederholen darf:
 *
 *     EIN NICHTWISSEN, DAS ALS NEIN ZURUECKKOMMT, IST EINE BEHAUPTUNG.
 *
 * BENANNTE GRENZE, von Anfang an: Diese Schicht beantwortet ausschliesslich
 * Fragen ueber DEN KNOTEN, AUF DEM SIE LIEGT. Nie ueber Nachbarn, nie ueber
 * Inhalte. Waechst sie darueber hinaus, ist sie ein zweites Kleinrock — und
 * dann gehoert sie nicht in den Boden.
 *
 * Wer antwortet, meldet sich an:
 *     add_filter('cluster_one_auskunft_gepaart', fn() => (bool) …);
 * Meldet sich niemand an, kommt 'fehlt'. Ein Antworter, der etwas anderes
 * als true/false zurueckgibt, zaehlt als nicht vorhanden — er hat die Frage
 * nicht beantwortet, sondern eine andere. */
function cluster_one_auskunft(string $c1_frage): string {
    /* Nur einfache Namen. Ein Doppelpunkt ist der Versuch, ein Ziel
       mitzugeben ("nachbar:https://…") — und ein Ziel ist ein Nachbar. */
    if ($c1_frage === '' || !preg_match('/^[a-z][a-z0-9_]*$/', $c1_frage)) {
        return 'fehlt';
    }
    $c1_haken = 'cluster_one_auskunft_' . $c1_frage;
    if (!function_exists('has_filter') || !has_filter($c1_haken)) {
        return 'fehlt';
    }
    $c1_antwort = apply_filters($c1_haken, null); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- der Name steht vier Zeilen hoeher: 'cluster_one_auskunft_' + ein geprueftes Wort; der Pruefer kann die Variable nicht lesen
    if (!is_bool($c1_antwort)) {
        return 'fehlt';
    }
    return $c1_antwort ? 'ja' : 'nein';
}

/* --- v1.4.0: DIE FREIGEBBAREN FELDER, AN GENAU EINER STELLE ------------------
 *
 * Bis 1.3.0 stand die Aufzaehlung DREIMAL: hier (was reist), in kranz.php
 * (was gespeichert wird) und in kleinrock.php (was man sieht). Drei
 * Schreibwege auf eine Wahrheit, P1-12. Wer eine vergisst, bekommt ein
 * Haekchen, das nicht haelt, oder eine Freigabe, die nie reist — beides
 * still.
 *
 * Sichtbar geworden ist es an 'languages': Christian hat am 31.08.2026
 * entschieden, dass Sprachen an die Freigabe gebunden werden. Diese eine
 * Aenderung haette an drei Stellen gemusst, in drei verschiedenen Formen —
 * und die Begruendung dafuer, dass Sprachen OHNE Freigabe reisen, stand
 * noch einmal doppelt im Kommentar (hier und in kranz.php:399).
 *
 *     Genau so ist H142 entstanden: ein Kommentar wurde berichtigt, der
 *     zweite Satz blieb stehen.
 *
 * 'form' sagt, wie das Feld bedient wird — 'text' ist ein Eingabefeld mit
 * Haekchen, 'liste' hat nur das Haekchen (die Werte kommen woanders her).
 * Wer ein Feld ergaenzt, ergaenzt eine Zeile. */
function cluster_one_profilfelder(): array {
    return array(
        'nickname'    => array('form' => 'text'),
        'description' => array('form' => 'text'),
        'city'        => array('form' => 'text'),
        'country'     => array('form' => 'text'),
        /* v1.4.0 (Christian, 31.08.2026): Sprachen sind ab jetzt freigebbar
           wie jedes andere Profilfeld. Bis 1.3.0 reisten sie ohne Freigabe
           mit der Begruendung "sie sind funktional" — und der sichtbare Satz
           auf der Mesh-Seite verschwieg sie deshalb (H142).
           v1.5.0 — DER KONSTANTENPFAD IST ZURUECK, UND DAS IST EINE
           RUECKNAHME MEINER EIGENEN BEGRUENDUNG.
           In 1.4.0 stand hier 'konstante' => false, begruendet mit:
           „cluster_one_self_languages() liest nur die Option." Das war falsch.
           Sie liest ZUERST die Konstante (anti-babel.php:98) und erst dann die
           Option; ihr eigener Kopf sagt es seit jeher: „1. Konstante,
           2. Backend-Liste." Ich hatte einen Ausschnitt gemessen und den Kopf
           nicht gelesen — und den Satz als Begruendung weitergegeben, auf der
           dann entschieden wurde.

               Eine Entscheidung, die auf einer falschen Messung ruht, ist
               nicht falsch. Sie ist ungedeckt — und das ist schlimmer, weil
               niemand weiss, dass sie noch einmal getroffen werden muss.

           Christian hat am 01.09.2026 neu entschieden: dieselbe Konstante
           liefert den WERT und erteilt die FREIGABE, wie bei den vier
           anderen Feldern. Keine Sonderlocke. */
        'languages'   => array('form' => 'liste',
                               'quelle' => 'cluster_one_self_languages',
                               /* die Kuerzel, die cluster_one_clean_langs()
                                  durchlaesst — und deshalb die einzigen, die
                                  ein Formular anbieten darf. */
                               'auswahl' => array('de', 'en')),
    );
}

/* --- v1.4.0: DIE EINMALIGE UMSTELLUNG FUER 'languages' -----------------------
 *
 * Bis 1.3.0 reisten Sprachen OHNE Freigabe. Ab 1.4.0 brauchen sie eine — und
 * niemand hat je ein Haekchen gesetzt, das es nicht gab. Ohne diesen Schritt
 * stuende die Freigabe auf jedem bestehenden Knoten sofort auf AUS, der
 * Babel-Fisch faende keine Sprachpaare mehr, die Sprachspalte leerte sich,
 * und NICHTS meldete einen Fehler.
 *
 *     Eine Entscheidung, die etwas Funktionierendes ohne Meldung beendet,
 *     ist keine Entscheidung, sondern ein Ausfall mit Datum.
 *
 * Also: einmal den Zustand aufschreiben, den es ohnehin schon gibt. Das
 * aendert kein Verhalten — es macht es abwaehlbar.
 *
 * DIE BEDINGUNG IST ENG, mit Absicht: nur wo heute wirklich Sprachen
 * gemeldet werden, und nur wo noch gar nichts eingetragen ist. Ein Knoten
 * ohne Sprachen bekommt kein Haekchen fuer etwas, das er nicht hat.
 *
 * WARUM SIE NICHT WIEDER ZUSCHLAEGT: der Sanitizer in kranz.php schreibt bei
 * abgewaehltem Kaestchen eine ausdrueckliche 0, er loescht den Schluessel
 * nicht. Nach dem ersten Speichern ist array_key_exists() also wahr, und wer
 * abwaehlt, bleibt abgewaehlt. Gemessen, nicht angenommen — die Alternative
 * waere ein Haekchen gewesen, das sich selbst wieder setzt. */
function cluster_one_umstellung_sprachfreigabe(): void {
    $c1_opts = get_option('cluster_one_settings', array());
    if (!is_array($c1_opts) || array_key_exists('share_languages', $c1_opts)) {
        return;
    }
    if (!cluster_one_self_languages()) {
        return;
    }
    $c1_opts['share_languages'] = 1;
    update_option('cluster_one_settings', $c1_opts);
}
add_action('init', 'cluster_one_umstellung_sprachfreigabe');

function cluster_one_shares(string $feld): bool {
    $const = 'CLUSTER_ONE_' . strtoupper($feld);
    if (defined($const)) {
        /* v1.5.0: Der Waechter war fuer EINZELNE Werte geschrieben. Fuer ein
           Array ist `!== ''` auch bei [] wahr — eine leere Konstante haette
           also eine Freigabe erteilt, die nichts freigibt. Jede Form bringt
           ihre eigene Frage nach „nicht leer" mit. */
        $c1_wert = constant($const);
        if (is_array($c1_wert) ? $c1_wert !== array() : $c1_wert !== '') {
            return true;
        }
    }
    $opts = get_option('cluster_one_settings', []);
    return !empty($opts['share_' . $feld]);
}

/** Der Steckbrief, wie er ins Netz geht — nur freigegebene, nicht leere Felder.
   v1.4.0: OHNE AUSNAHME. Die Sprachen hatten bis hier eine, und der Satz auf
   der Mesh-Seite kannte sie nicht (H142). */
function cluster_one_self_profile(): array {
    $out = array();
    foreach (cluster_one_profilfelder() as $f => $c1_art) {
        if (!cluster_one_shares($f)) {
            continue;
        }
        $wert = isset($c1_art['quelle'])
            ? ($c1_art['quelle'])()
            : cluster_one_config('CLUSTER_ONE_' . strtoupper($f), $f);
        if ($wert !== '' && $wert !== array()) {
            $out[$f] = $wert;
        }
    }
    return $out;
}

function cluster_one_self_url(): string {
    // Wird AUSGELESEN, nicht eingegeben: die echte Adresse dieses Knotens.
    // Gegen Etikettenschwindel – man kann hier keine fremde URL behaupten.
    return cluster_one_self_base_url();
}

/* v0.9.31: Hier standen self_description/_nickname/_city/_country — vier
   Einzeiler, die cluster_one_config() weiterreichten. cluster_one_self_profile()
   holt dieselben Werte laengst selbst ueber
   cluster_one_config('CLUSTER_ONE_'.strtoupper($f), $f) und ging an ihnen
   vorbei; gerufen hat sie zuletzt niemand. self_url(), discord_url() und
   matrix_url() bleiben — die werden benutzt. */

function cluster_one_discord_url(): string {
    return cluster_one_config('CLUSTER_ONE_DISCORD_URL', 'discord_url');
}

function cluster_one_matrix_url(): string {
    return cluster_one_config('CLUSTER_ONE_MATRIX_URL', 'matrix_url');
}

/** v0.0.100 (Beschluss 97c): Archimedes-Treffen pingen bei Änderung und
   Absage — alles andere verschwindet leise. Bump des Signal-Zeitstempels,
   wenn das betroffene Event über sein Projekt an einem VERÖFFENTLICHTEN
   Label hängt; die Freunde sehen das Signal und pullen. */
/* v1.0.1 — AUSGEZOGEN nach faust.php, zu seinen beiden Aufrufern.
   Befund der Kleinrock-Instanz: die Funktion lag im Boden und war ab
   Zeile zwei Platon-Gebiet — 'linked_thread' wird nur aus Platon-Threads
   gesetzt, also kehrt sie in jedem Light ohne Platon sofort zurueck.
   Der Boden wusste von einem Organ.
   Nachgemessen: GENAU ZWEI Aufrufer, faust.php, :155 und :654 in der alten Zaehlung, beide in
   derselben Datei. Der Umzug ist damit risikofrei.
   BENANNTE GRENZE: das repariert den ORT, nicht das VERHALTEN. Dass eine
   Absage in einem Light ohne Platon niemanden erreicht, bleibt eine
   Entwurfsfrage — sie gehoert Kleinrock und Bismarck, nicht dem Boden. */


/** Rendert eine WordPress-Reiterleiste.
   $tabs: Liste von ['label'=>string, 'url'=>string, 'active'=>bool]. */
function cluster_one_render_tabs(array $tabs, bool $c1_zweite_reihe = false): void {
    /* 28.10.2026 (Neo), Boden 1.17.7 — DER REITER „KNOTEN" AM ENDE JEDER LEISTE.
       Seit die Kranz-Leiste in die Produktleisten aufgeloest ist (19.10.), hatten
       Knoten-Einstellungen, Profil und System keinen Weg: der Dach-Link wurde nie
       gezeigt (Umleitung aufs erste Produkt, 16.09.), und einen Untereintrag in
       der Seitenleiste will Nemesus ausdruecklich nicht (28.10., Runde 23 zurueck).
       Also hier, an der EINEN Stelle, durch die jede Leiste des Hauses laeuft:
       rechts ein letzter Reiter „Knoten" auf die Knoten-Einstellungen; dort steht
       die Boden-Leiste mit Profil und System. Nicht angehaengt, wenn die Leiste
       die Seite schon fuehrt (die Boden-Leiste selbst) – sonst stuende er zweimal.
       Pacioli zeichnet seine Leiste selbst und haengt den Reiter selbst an. */
    /* 28.10.2026 (Neo), Boden 1.18.0: der Reiter traegt die Herkunft mit (?von=,
       cluster_one_herkunft) und ist aktiv, wenn der Leser auf einer Knoten-Seite
       steht — dort zeichnet die Produktleiste ueber der Knoten-Reihe. Bis 1.17.7:
       Adresse ohne „von", 'active' => false. */
    /* 03.11.2026 (Neo), Boden 1.22.0 — RUNDE TAU: DER REITER „FEYNMAN" VOR „KNOTEN", UND EINE ZWEITE REIHE.
       Nemesus' Entscheidung vom 02.11.: seit die Kranz-Leiste in die Produktleisten aufgeloest ist (19.10.), fuehrte kein
       Reiter mehr zu Feynman (Derrida, Ueber Cluster One, Warum, DNA, Nemesus) — eine Seite, die keinem Plugin gehoerte,
       „Cluster One" hiess und nur per Direktlink zu erreichen war. Dabei verspricht die Hausordnung (why.txt) im ersten
       Satz, dass Menschen lesen koennen, was die KIs bekommen. Also: Feynman als Reiter in JEDER Leiste des Hauses, vor
       „Knoten" (Platz: sein Wort), und seine Seiten tragen oben die Leiste des Hauses, von dem der Leser kam (Aktion
       'cluster_one_leiste', wie die Knoten-Seiten seit 1.18.0), darunter ihre eigene Reihe (feynman.php).
       GEZEICHNET NUR, WENN FEYNMAN DEKLARIERT HAT — dann meldet menue.php seine Seiten an; ein Reiter auf eine Seite,
       die WordPress nicht kennt, waere ein Weg ins Leere. Seit 1.22.0 reist feynman.php in allen fuenf Paketen.
       $c1_zweite_reihe: die Reihe UNTER einer Hausleiste (Knoten-Reihe, Feynman-Reihe) bekommt weder „Feynman" noch
       „Knoten" angehaengt — beide stehen schon in der Leiste darueber. Bis 1.21.1 hielt allein die Adressprobe
       ($c1_schon, unten) den doppelten Knoten-Reiter ab; sie bleibt als zweiter Riegel stehen.
       Pacioli zeichnet seine Leiste selbst und haengt beide Reiter selbst an (class-cv-admin.php). */
    /* 03.11.2026 mittags (Neo), Boden 1.22.1 — RUNDE YPSILON: FEYNMAN IST DER LETZTE. Nemesus nach dem Tau-Lauf auf der DEV:
       „Feynman sollte immer als letzter Tab kommen, also hinter Knoten, nicht davor." In 1.22.0 stand der Feynman-Block an
       dieser Stelle, VOR dem Knoten-Reiter; er steht jetzt darunter, hinter ihm. Gleicher Block, anderer Platz. */
    $c1_knoten_url = cluster_one_mit_herkunft(admin_url('admin.php?page=cluster_one_settings'));
    $c1_schon = false;
    foreach ($tabs as $t) {
        if (is_array($t) && strpos((string) ($t['url'] ?? ''), 'page=cluster_one_settings') !== false) { $c1_schon = true; break; }
    }
    if (!$c1_zweite_reihe && !$c1_schon && function_exists('cluster_one_render_settings_page')) {
        $tabs[] = array('label' => __('Node', 'cluster-one-bismarck'), 'url' => $c1_knoten_url, 'active' => cluster_one_knoten_seite_aktuell());
    }
    /* Ypsilon (Boden 1.22.1): der Feynman-Reiter, hinter „Knoten" — Nemesus' Platz, siehe oben. */
    if (!$c1_zweite_reihe && function_exists('cluster_one_feynman_seiten') && function_exists('cluster_one_organ_wirt') && cluster_one_organ_wirt('feynman') !== '') {
        $tabs[] = array('label' => 'Feynman', 'url' => cluster_one_mit_herkunft(admin_url('admin.php?page=cluster_one_about')), 'active' => cluster_one_feynman_seite_aktuell());
    }
    echo '<nav class="nav-tab-wrapper wp-clearfix" style="margin-bottom:1.5em;">';
    foreach ($tabs as $t) {
        $cls = 'nav-tab' . (!empty($t['active']) ? ' nav-tab-active' : '');
        printf(
            '<a href="%s" class="%s">%s</a>',
            esc_url($t['url']), esc_attr($cls), esc_html($t['label'])
        );
    }
    echo '</nav>';
}

/** Nutzung eines Bereichs: [threads, rooms] — Zahlen für den Spiegel,
   ungefiltert (die Struktur ist sichtbar; ANZAHLEN verraten keine
   geschützten Inhalte, nur dass die Tür existiert). */
function cluster_one_area_usage(): array {
    $usage = array();
    foreach (cluster_one_get_forum_threads() as $t) {
        $a = (string) ($t['area'] ?? '');
        if ($a === '') { continue; }
        $usage[$a]['threads'] = (int) ($usage[$a]['threads'] ?? 0) + 1;
    }
    foreach (cluster_one_get_rooms() as $r) {
        $a = (string) ($r['area'] ?? '');
        if ($a === '') { continue; }
        $usage[$a]['rooms'] = (int) ($usage[$a]['rooms'] ?? 0) + 1;
    }
    return $usage;
}


/** Sichtbarkeits-Änderung bezeugen (v0.0.66): der zentrale Helfer für Raum-
   UND Thread-Konfiguration. Türereignisse (Artikel 2/5 — kein stiller
   Wechsel, in keine Richtung): Klassenwechsel all↔named → visibility_changed
   (das visibility-Feld trägt die NEUE Klasse, minimaler Snapshot nach
   Artikel 4.2); Listenänderung INNERHALB von named → viewer_added/removed
   pro betroffenem Actor. Beim Klassenwechsel selbst werden keine
   Einzel-Viewer-Ereignisse emittiert — das eine Klassen-Zeugnis IST die
   Türänderung, alles weitere wäre Rauschen (Zeugen-Ermüdung). No-op-Saves
   erzeugen kein Zeugnis. */
function cluster_one_emit_visibility_change(string $container_type, string $container_id, string $old_gv, array $old_vt, string $new_gv, array $new_vt): void {
    $me = cluster_one_actor_id_human();
    if ($old_gv !== $new_gv) {
        cluster_one_event_emit(array(
            'type' => 'visibility_changed', 'container_type' => $container_type, 'container_id' => $container_id,
            'actor_id' => $me, 'visibility' => $new_gv,
        ), true);
        return;
    }
    if ($new_gv === 'named') {
        foreach (array_diff($new_vt, $old_vt) as $add) {
            cluster_one_event_emit(array(
                'type' => 'viewer_added', 'container_type' => $container_type, 'container_id' => $container_id,
                'actor_id' => $me, 'subject_id' => cluster_one_participant_actor_id($add), 'visibility' => $new_gv,
            ), true);
        }
        foreach (array_diff($old_vt, $new_vt) as $rem) {
            cluster_one_event_emit(array(
                'type' => 'viewer_removed', 'container_type' => $container_type, 'container_id' => $container_id,
                'actor_id' => $me, 'subject_id' => cluster_one_participant_actor_id($rem), 'visibility' => $new_gv,
            ), true);
        }
    }
}



/** Setzt die Raum↔Thema-Verknüpfung (v0.0.57), streng 1:1, bidirektional
   synchron. Nur über die Raum-Konfiguration aufrufbar (SysOp-exklusiv) —
   die Themenseite zeigt das Ergebnis nur an, ändert es nie selbst, sonst
   könnte ein gewöhnliches Weißlisten-Mitglied über den Umweg des eigenen
   Themas etwas an einem SysOp-exklusiven Raum verändern. Löst alte
   Verknüpfungen auf beiden Seiten sauber auf, auch wenn das neue Thema
   zuvor an einen ANDEREN Raum gehängt war (1:1 erzwungen, kein Dreieck). */
function cluster_one_set_room_thread_link(string $room_id, string $new_thread_id): void {
    $rooms   = cluster_one_get_rooms();
    $threads = cluster_one_get_forum_threads();
    if (!isset($rooms[$room_id])) {
        return;
    }
    $old_thread_id = (string) ($rooms[$room_id]['linked_thread'] ?? '');

    if ($old_thread_id !== '' && $old_thread_id !== $new_thread_id && isset($threads[$old_thread_id])) {
        $threads[$old_thread_id]['linked_room'] = '';
    }

    if ($new_thread_id === '') {
        $rooms[$room_id]['linked_thread'] = '';
    } elseif (isset($threads[$new_thread_id])) {
        $prev_room_id = (string) ($threads[$new_thread_id]['linked_room'] ?? '');
        if ($prev_room_id !== '' && $prev_room_id !== $room_id && isset($rooms[$prev_room_id])) {
            $rooms[$prev_room_id]['linked_thread'] = ''; // dem Thema seinen alten Raum entziehen (1:1)
        }
        $rooms[$room_id]['linked_thread']       = $new_thread_id;
        $threads[$new_thread_id]['linked_room'] = $room_id;

        /* v0.9.5: WIRD EIN RAUM AN EIN TAGEBUCH GEHAENGT, ERBT ER DESSEN
           GAESTELISTE.

           Christians zweiter Spiegel-Dump zeigte den Fall: "Nemesus Chat" und
           "MenschA-Chat" standen auf group_visibility 'all' — jeder Nutzer des
           Knotens konnte hinein. Die Tagebuecher selbst waren zu, die Konsole
           davor offen.

           Das automatisch angelegte Vorzimmer setzt 'named' korrekt. Wer aber
           einen gewoehnlichen Raum anlegt und ihn NACHTRAEGLICH verknuepft,
           bekam ein Terminal-Etikett ohne Terminal-Schutz. EIN ETIKETT, DAS
           SCHUTZ BEHAUPTET, MUSS IHN AUCH HERSTELLEN.

           Das Verknuepfen ist ohnehin ein Tuerereignis und wird direkt darunter
           bezeugt — die Verengung ist also kein stiller Wechsel. Sie wirkt nur
           beim Verknuepfen, nicht rueckwirkend: Bestehende Raeume behaelt der
           SysOp in der Hand. */
        /* v0.9.28: Beim Verknuepfen traegt die Gaesteliste des Raums aufs
           Thema — sonst haette das Paar von Anfang an zwei Kreise. Laeuft nach
           dem Speichern der Kante (unten), damit die Funktion sie sieht. */
        $c1_sync_guests = true;
        $c1_dt = $threads[$new_thread_id] ?? array();
        if (cluster_one_thread_is_ai_diary($c1_dt) || !empty($c1_dt['human_diary'])) {
            $c1_gast = array_values(array_unique(array_merge(
                array('wp_' . cluster_one_main_admin_user_id()),   // der SysOp bleibt drin
                array_map('strval', (array) ($c1_dt['visible_to'] ?? array()))
            )));
            $rooms[$room_id]['group_visibility'] = 'named';
            $rooms[$room_id]['visible_to']       = $c1_gast;
        }
    }

    cluster_one_save_rooms($rooms);
    cluster_one_save_forum_threads($threads);
    if (!empty($c1_sync_guests)) {
        cluster_one_sync_guests_to_thread($room_id);
    }

    // v0.0.62: TÜRereignisse — eine Verknüpfung verändert, was in den Raum
    // fließen KANN (Kontext, Präambel), also den Kreis der Mitlesenden.
    if ($old_thread_id !== $new_thread_id) {
        $me = cluster_one_actor_id_human();
        if ($old_thread_id !== '') {
            cluster_one_event_emit(array('type' => 'room_thread_unlinked', 'container_type' => 'room', 'container_id' => $room_id, 'actor_id' => $me, 'subject_id' => 'thread:' . $old_thread_id), true);
        }
        if ($new_thread_id !== '' && isset($threads[$new_thread_id])) {
            cluster_one_event_emit(array('type' => 'room_thread_linked', 'container_type' => 'room', 'container_id' => $room_id, 'actor_id' => $me, 'subject_id' => 'thread:' . $new_thread_id), true);
        }
    }
}

/** Nur KI-Connectoren: filtert Nicht-KI-Standard-Connectoren des Core (z. B.
   Akismet, type=spam_filtering) über den Typ raus. Liste erweiterbar. */
/** Rohe, ungefilterte Connector-Liste — auch das, was ausgeschlossen werden
   KÖNNTE, damit Kranz eine ehrliche Auswahl anzeigen kann. */
function cluster_one_get_ai_connectors_raw(): array {
    if (!function_exists('wp_get_connectors')) {
        return [];
    }
    $all = wp_get_connectors();
    return is_array($all) ? $all : [];
}

/** Vom Knotenpunkt ausgeschlossene Connector-IDs (Kranz → Kleinrock). Ersetzt
   v0.0.46 die zuvor fest im Code verdrahtete Typ-Ausnahme ('spam_filtering')
   — der Knotenpunkt entscheidet jetzt selbst, nicht das Plugin. ACHTUNG bei
   der Migration: bis einmal manuell nachgeschaut wird, taucht ein zuvor
   automatisch versteckter Connector (z. B. ein Antispam-Baustein aus WP
   selbst) wieder in der KI-Kennungstabelle auf — ehrlicher Preis für echte
   Kontrolle statt stiller Annahme. */
function cluster_one_excluded_connector_ids(): array {
    $opts = get_option('cluster_one_settings', array());
    $ids  = isset($opts['excluded_connector_ids']) && is_array($opts['excluded_connector_ids']) ? $opts['excluded_connector_ids'] : array();
    return array_values(array_unique(array_map('strval', $ids)));
}

function cluster_one_get_ai_connectors(): array {
    $all      = cluster_one_get_ai_connectors_raw();
    $excluded = cluster_one_excluded_connector_ids();
    $out      = array();
    foreach ($all as $cid => $conn) {
        if (in_array((string) $cid, $excluded, true)) {
            continue;
        }
        $out[$cid] = $conn;
    }
    return $out;
}

/** Knotenweite KI-Identität: Provider -> WP-User-ID. Gilt für ALLE Räume. */
function cluster_one_get_ai_identities(): array {
    $a = get_option('cluster_one_ai_identities', array());
    return is_array($a) ? $a : array();
}

/**
 * BLOCK FUER kern.php — KEINE DATEI, DIE AUSGELIEFERT WIRD.
 *
 * Gehoert unmittelbar NACH cluster_one_get_ai_identities()
 * (kern.php:718–722 in 1.9.9 / cluster-one-bismarck 0.2.5), also ab Zeile 723.
 *
 *     Boden 1.9.9      kern.php  sha1 1ec02e229cdf…   (Monolith)
 *     Paket  0.2.5     kern.php  sha1 0f399c53b499…   (cluster-one-bismarck)
 *     Beide unterscheiden sich nur in der Textdomaene; dieser Block enthaelt
 *     keine uebersetzte Zeichenkette und ist deshalb fuer beide derselbe.
 *
 * ICH HABE kern.php NICHT ANGEFASST. Der Boden ist nicht meiner, und Hollerith
 * hat den Boden-Zug fuer 1.9.10 in der Hand. Zwei Haende an einer reisenden
 * Datei sind derselbe Fehler wie zwei Antworter auf einem Haken.
 *
 * ---------------------------------------------------------------------------
 * WOZU
 *
 * `cluster_one_ai_identities` bildet ab: Connector-Kennung -> WordPress-
 * Benutzer-ID. Beim SCHREIBEN prueft das SysOp-Formular, dass der Benutzer
 * existiert. Beim LESEN wird an drei Stellen nur noch GEZAEHLT — und nach dem
 * Loeschen der Konten meldet der Knoten weiter zwei einsatzbereite
 * KI-Identitaeten, an genau den drei Stellen, die ein Mensch sieht.
 *
 *     Eine Liste, die auf Dinge in einem fremden Paket zeigt, kann nicht
 *     durch Pflege richtig gehalten werden. Sie kann nur beim Lesen
 *     nachfragen — oder luegen, sobald das andere Paket verschwindet.
 *
 * Deshalb EINE Funktion und nicht drei Reparaturen: die drei Zaehler fragen
 * sie, statt jeder fuer sich eine Pruefung mitzuschleppen, die einer von ihnen
 * beim naechsten Mal vergisst.
 *
 * ---------------------------------------------------------------------------
 * DER PREIS, BENANNT STATT VERSCHWIEGEN
 *
 * Ein get_userdata() je Eintrag. Das ist die Zahl der KI-Identitaeten eines
 * Knotens, also einstellig; WordPress hat die Benutzer ohnehin im Cache, und
 * gefragt wird auf Verwaltungsseiten, nicht im Seitenaufbau fuer Besucher.
 *
 * ---------------------------------------------------------------------------
 * WAS SIE NICHT KANN, UND DAS IST DIE WICHTIGE ZEILE
 *
 * Sie fragt, ob es das KONTO gibt — nicht, ob es antworten kann. Ein Konto
 * ohne Rolle, ohne Capability und mit geloeschtem Hauptschluessel ist fuer
 * diese Funktion vorhanden.
 *
 *     Leere Huellen stehenzulassen repariert die Liste nicht. Es macht sie
 *     ueberzeugend: dann sind sich alle fuenf Stellen einig, und alle fuenf
 *     sagen dasselbe Falsche.
 *
 * Das ist ein Argument FUER das Loeschen der Konten, und es ist nicht meines —
 * es ist Mammuts, und mein Zeuge faehrt es als eigene Welt vor (W3).
 *
 * ---------------------------------------------------------------------------
 * BEZEUGT: zeuge-ki-widerspruch.php, vier Welten, fuenf Stellen.
 * Roter Lauf gegen 1.9.9 / 0.2.5 liegt bei.
 */

/** Die Kennungen, deren Konto es WIRKLICH gibt. */
function cluster_one_ai_identities_live(): array {
    $aus = array();
    foreach (cluster_one_get_ai_identities() as $cid => $uid) {
        if ((int) $uid > 0 && get_userdata((int) $uid)) {
            $aus[(string) $cid] = (int) $uid;
        }
    }
    return $aus;
}

/* v0.9.81 — HEREINGEZOGEN AUS anti-babel.php, Rumpf byte-identisch (Regel 2).
   Die Identitaeten stehen hier; ihre Stimme gehoert daneben. Belegt statt
   behauptet: dieser Rumpf ruft ausschliesslich Funktionen, die schon im
   Boden wohnen — kern.php bleibt bei NULL Aufwaertsrufen (0.9.80).
   Der Umzug war noetig fuer den Schnitt: anti-babel.php geht nach Bismarck,
   und ein Knoten mit Kleinrock+Pacioli haette sonst keinen KI-Aufruf —
   Turings Zeuge wuerde auf wp_ai_client_prompt() ausweichen und einen
   anderen Weg messen als den, den seine Ueberschrift verspricht. */
/** Kern-KI-Aufruf (v0.0.55), aus cluster_one_ai_reply() herausgezogen: nimmt
   fertigen System-Prompt + Text entgegen, kein Gesprächskontext-Aufbau mehr
   hier drin — das macht cluster_one_ai_reply() selbst. Geteilt zwischen
   Konversations-Antworten UND Secure-Anti-Babel-Übersetzung, damit beide
   dieselbe Anbieter-Pin-/Kosten-/Fehlerlogik nutzen, nicht zwei Kopien. */
/* v1.0.1 — DIE NAHT ZU PACIOLI. Festlegung der Pacioli-Instanz (Connector
   Vault 0.5.0), hier umgesetzt. Ein Parameter mit Vorgabe: die vier
   bestehenden Aufrufer bleiben unveraendert gueltig, ein Knoten ohne Pacioli
   merkt von der Naht nichts.

   IHR BEFUND, der nicht im Auftrag stand und der eigentliche ist: $cid nennt
   den Anbieter, der Akteur nennt die Identitaet — und der Akteur hat auch
   einen Anbieter. ZWEI QUELLEN AUF EINE WAHRHEIT, unser eigenes P1-12, von
   einer fremden Instanz auf uns angewendet. Sie werden hier EINMAL
   gegeneinander geprueft, benannt, an genau einer Stelle.

   Der Kontext liegt um den GANZEN Rumpf, nicht nur um generate_text():
   is_supported_for_text_generation() spricht unter Umstaenden schon mit dem
   Anbieter. Laege die Bindung nur um die letzte Zeile, ginge dieser Request
   ungebunden hinaus und traefe den Riegel.

   Der alte Rumpf lebt unveraendert als _raw() weiter (Regel 2) — bis auf den
   einen Schalter, der den ungepinnten Notweg unter einer Bindung sperrt. */
function cluster_one_ai_generate_text(string $cid, string $sys, string $prompt, int $akteur = 0): array {

    /* 1) Ohne Pacioli: alles wie bisher. Nichts erzwingen, was nicht da ist. */
    if (!function_exists('pacioli_acting_as')) {
        return cluster_one_ai_generate_text_raw($cid, $sys, $prompt);
    }

    /* 2) OHNE AKTEUR: WER DRUECKT, ZAHLT — v1.4.0, 09.10.2026.
     *
     * $akteur ist ein MENSCH, nicht eine KI: der, in dessen Namen und auf
     * dessen Rechnung gehandelt wird. Nennt die Aufrufstelle keinen, gilt
     * Christians Regel vom 27.08. — wer drueckt, zahlt.
     *
     * GEMESSEN AM 0.2.39: $room['owner'] wird an genau EINER von vier
     * Raumanlagen geschrieben — die des Vorzimmers, die einzige mit der
     * Zeile `'owner'            => $owner,`. Die
     * anderen drei uebergaben 0, und darum war jede KI-Antwort ausserhalb
     * eines Vorzimmers eine Absage.
     *
     *     Ein Feld, das „seit jeher da" ist, ist seit jeher da an EINER
     *     Stelle — und die hat jemand gezaehlt, nicht gemessen.
     *
     * Ohne angemeldeten Menschen — Cron, WP-CLI, ein Aufruf aus dem
     * Nichts — bleibt es bei 0 und bei der Absage. Dieser Rueckfall kann
     * niemals eine FREMDE Rechnung treffen: er nennt den, der gerade
     * handelt, und sonst niemanden. */
    if ($akteur <= 0) {
        $akteur = (int) get_current_user_id();
    }

    if ($akteur <= 0) {
        return array('ok' => false, 'err' => 'no_akteur', 'detail' => '');
    }

    /* 3) DIE INSTANZ — ueber EINE Stelle, v1.4.0.
     *
     * ZUG 23 hat hier cv_identity_for() selbst gerufen. Das war richtig
     * gedacht und an der falschen Stelle: die Frage „welche Instanz
     * spricht fuer diesen Menschen" stellen auch Bismarcks Beschriftung
     * und seine Taste. Drei Leser auf eine Wahrheit sind erlaubt, drei
     * Entscheider nicht.                                       (P1-12)
     *
     * Darum steht die Auflosung jetzt in cluster_one_ai_instanz(), und
     * diese Naht ist einer ihrer Leser. */

    /* 3a) IST DER AKTEUR UEBERHAUPT EIN MENSCH?
     *
     * Der Vertrag dieser Funktion sagt: ein Mensch. Uebergibt eine
     * Aufrufstelle eine KI, ist das kein Sonderfall, den man umdeutet,
     * sondern ein Fehler in der Aufrufstelle — und eine KI, die eine KI
     * beauftragt, hat keinen Zahler.
     *
     * ZUG 23 hat eine Identitaet des falschen Anbieters abgewiesen und
     * eine des richtigen durchgelassen. Das war eine Haelfte: auch die
     * passende hat keinen Menschen hinter sich.
     *
     *     Ein Vertrag, der nur den offensichtlichen Bruch bemerkt, ist
     *     eine Bitte um Mitarbeit. */
    if (function_exists('pacioli_provider_of') && '' !== (string) pacioli_provider_of($akteur)) {
        return array(
            'ok'     => false,
            'err'    => 'akteur_keine_person',
            'detail' => sprintf(
                /* translators: %s: Name der uebergebenen KI. */
                __('%s is an AI. Here a person is expected — the one on whose account the call runs.', 'cluster-one-bismarck'),
                cluster_one_ai_akteur_label($akteur)
            ),
        );
    }

    $c1_ki = cluster_one_ai_instanz($akteur, $cid);

    if ($c1_ki <= 0) {
        /* DER GRUND WIRD BENANNT — wer, und bei wem. Christian am 08.10.:
           „dann soll die Meldung kommen, dass wer auch immer gegenwaertig
           keine KI von wem auch immer hat."

           Der fertige Satz geht als DETAIL hinaus, weil die Anzeige
           (cluster_one_render_ai_notice, bismarck.php) Code und Detail
           getrennt fuehrt und ich fremde Bytes nicht anfasse. */
        return array(
            'ok'     => false,
            'err'    => 'no_identity',
            'detail' => sprintf(
                /* translators: 1: Name des Menschen, 2: Anbieter-ID. */
                __('%1$s currently has no AI with %2$s.', 'cluster-one-bismarck'),
                cluster_one_ai_akteur_label($akteur),
                $cid
            ),
        );
    }

    /* 3b) R3 — PACIOLIS GRUNDREGEL, UND SIE WIRD GEFRAGT.
     *
     * „Darf dieser Mensch diese KI beauftragen? Fuer die Systemidentitaet
     * immer false — auch fuer den Administrator." Dazu: nur der Besitzer,
     * ein verwaister Besitzer nie, und ein Filter fuer den Fall, dass ein
     * Besitzer seine KI bewusst leiht und die Rechnung uebernimmt — die
     * Maezen-Lampe.
     *
     * NACH R-VERWEIS-1 AM WORTLAUT UND NICHT AN DER ZEILE: Paciolis
     * `CV_Vault::can_invoke()` und der Filter `cv_can_invoke` darin.
     * „Maezen-Lampe" ist MEIN Wort fuer die Sache und steht in Paciolis
     * Datei nirgends — wer danach sucht, findet nichts. Hier stand
     * „class-cv-vault.php:144–184"; die Nummer stimmte am 11.10. und
     * stand allein.
     *
     *     Ein Spitzname ist kein Wortlaut. Er ist eine Adresse, die nur
     *     der kennt, der sie erfunden hat.
     *
     * Die Frage kostet nichts, und ihre Abwesenheit waere eine Umgehung.
     *
     *     Eine Grundregel, die man nicht fragt, ist eine Bitte. */
    if (function_exists('pacioli_can_invoke') && !pacioli_can_invoke($akteur, $c1_ki)) {
        return array(
            'ok'     => false,
            'err'    => 'not_allowed',
            'detail' => sprintf(
                /* translators: 1: Name des Menschen, 2: Anbieter-ID. */
                __('%1$s may not commission this AI at %2$s.', 'cluster-one-bismarck'),
                cluster_one_ai_akteur_label($akteur),
                $cid
            ),
        );
    }

    /* 4) Der Kontext um den GANZEN Rumpf — um die IDENTITAET, nicht um
     *    den Menschen. Das ist die eigentliche Reparatur.
     *
     * 18.10.2026 (Neo), B1 — ZWEI DINGE AN DIESER ZEILE:
     *
     * DER DRITTE WERT ist der Akteur. Pacioli prueft seit 0.8.25 selbst, ob
     * der Mensch, auf dessen Rechnung gehandelt wird, die KI beauftragen
     * darf. Ohne diesen Wert fragte Pacioli den angemeldeten Benutzer — und
     * bei einer freigegebenen KI („My own machine") ist das nicht der
     * Zahler, sondern der Druecker; die Freigabe waere damit tot gewesen.
     * Eine aeltere Pacioli-Fassung ignoriert den dritten Wert einfach.
     *
     * DER RUECKGABEWERT WIRD GEFRAGT. cv_acting_as() gibt einen WP_Error
     * zurueck, wenn die Identitaet fehlt, pausiert ist oder (neu) nicht
     * beauftragt werden darf. Diese Funktion verspricht `array` — ein
     * durchgereichter WP_Error war seit v1.4.0 ein TypeError, also ein
     * weisser Bildschirm fuer jeden, der eine pausierte KI drueckte.
     * Jetzt wird er in die Form uebersetzt, die jeder Aufrufer kennt. */
    $c1_r = pacioli_acting_as($c1_ki, function () use ($cid, $sys, $prompt, $c1_ki) {
        $c1_r = cluster_one_ai_generate_text_raw($cid, $sys, $prompt, true);
        /* 5) Ablehnung durch den Anbieter gehoert dem Besitzer gemeldet.
              Dem Besitzer der IDENTITAET — sie traegt den Schluessel. */
        if (empty($c1_r['ok']) && ($c1_r['err'] ?? '') === 'rejected'
            && function_exists('pacioli_mark_key_invalid')) {
            pacioli_mark_key_invalid($c1_ki, (string) ($c1_r['detail'] ?? ''));
        }
        return $c1_r;
    }, $akteur);

    if (is_wp_error($c1_r)) {
        $c1_codes = array(
            'pacioli_not_allowed'          => 'not_allowed',
            'pacioli_not_an_identity'      => 'no_identity',
            'pacioli_identity_unavailable' => 'failed',
        );
        $c1_code = (string) $c1_r->get_error_code();
        return array(
            'ok'     => false,
            'err'    => $c1_codes[$c1_code] ?? 'failed',
            'detail' => (string) $c1_r->get_error_message(),
        );
    }
    if (!is_array($c1_r)) {
        return array('ok' => false, 'err' => 'failed', 'detail' => '');
    }
    return $c1_r;
}

function cluster_one_ai_generate_text_raw(string $cid, string $sys, string $prompt, bool $c1_kein_notweg = false): array {
    if (! function_exists('wp_ai_client_prompt')) {
        return array('ok' => false, 'err' => 'no_client', 'detail' => '');
    }

    // Builder-Fabrik: gepinnt (an genau diesen Provider) oder ungepinnt.
    // Ein optionales Modell pro Kennung wird als Wunsch mitgegeben (umgeht z. B.
    // eine gesperrte Auto-Wahl wie Fable 5); leer = Anbieter wählt automatisch.
    $model = cluster_one_ai_model($cid);
    $make = function (bool $pin) use ($prompt, $sys, $cid, $model) {
        $b = wp_ai_client_prompt($prompt);
        if ($pin) {
            $b = $b->using_provider($cid); // B-2: an den Anbieter der gedrückten KI nageln
        }
        if ($model !== '') {
            $b = $b->using_model_preference($model); // gewünschtes Modell, sonst Auto-Fallback
        }
        return $b->using_system_instruction($sys)->using_max_tokens(2000); // sanfter Kostenriegel; kein Auto-Retry
    };

    // 1) Bevorzugt: gepinnt. In try/catch, falls ein unbekannter Slug die SDK
    //    direkt werfen lässt (außerhalb der WP_Error-Hülle der Generierung).
    $builder = null;
    $pinned_supported = false;
    try {
        $builder = $make(true);
        $pinned_supported = (bool) $builder->is_supported_for_text_generation();
    } catch (\Throwable $e) {
        $pinned_supported = false;
    }

    // 2) Greift der Pin nicht, ehrlich abwägen statt still lügen.
    if (! $pinned_supported) {
        $plain = $make(false);
        if (! $plain->is_supported_for_text_generation()) {
            // Kostenlose, deterministische Diagnose: gar kein Provider mit Key.
            return array('ok' => false, 'err' => 'no_key', 'detail' => '');
        }
        // Ungepinnt ginge. Nur dann ehrlich, wenn es genau EINEN Provider gibt
        // (dann ist „irgendein Provider" == dieser). Bei mehreren: Diagnose.
        if (cluster_one_count_keyed_ai_connectors() > 1) {
            return array('ok' => false, 'err' => 'pin_failed', 'detail' => (string) $cid);
        }
        /* v1.0.1 — UNTER EINER BINDUNG KEIN NOTWEG (Pacioli, Connector Vault 0.5.0).
           Der Satz rechts stimmt ohne Pacioli und stimmt mit Pacioli nicht mehr:
           "ungepinnt" heisst, das SDK waehlt. Der Vault hat die Authentifizierung
           fuer GENAU EINEN Anbieter gesetzt — waehlt das SDK einen anderen, geht
           die Marke hinaus. Und die Zuschreibung stimmt bei einem Anbieter nur
           ZUFAELLIG, naemlich solange niemand einen zweiten einrichtet. Ein
           Verhalten, das von einer Konfiguration abhaengt, die sich morgen aendern
           kann, ist keine Zusicherung. */
        if ($c1_kein_notweg) {
            return array('ok' => false, 'err' => 'pin_failed', 'detail' => (string) $cid);
        }
        $builder = $plain; // ein Provider: ungepinnt == dieselbe KI, Zuschreibung bleibt wahr
    }

    $text = $builder->generate_text();

    if (is_wp_error($text)) {
        $data   = $text->get_error_data();
        $status = 0;
        if (is_array($data) && isset($data['status'])) {
            $status = (int) $data['status'];
        } elseif (is_int($data)) {
            $status = (int) $data;
        }
        $code = (string) $text->get_error_code();
        $err  = 'failed';
        if ($status === 401 || $status === 403) {
            $err = 'rejected';
        } elseif ($status === 408 || $status === 504 || $status === 524 || stripos($code, 'timeout') !== false) {
            $err = 'timeout';
        }
        return array('ok' => false, 'err' => $err, 'detail' => (string) $text->get_error_message());
    }

    $clean = cluster_one_sanitize_message((string) $text); // fremder Inhalt: NIE HTML
    if ($clean === '') {
        return array('ok' => false, 'err' => 'empty', 'detail' => '');
    }

    return array('ok' => true, 'text' => $clean, 'err' => '');
}

/* --- v0.1.5: KNOTEN-RESET --------------------------------------------------
   Ein neuer SysOp testet, bevor er den Betrieb eröffnet. Danach will er einen
   sauberen Maschinenraum übergeben. Genau EINE Trennlinie:

     MASCHINENRAUM BLEIBT — Knotenname, SysOp, Bereiche, Wiki-Bereiche,
     Weiße Liste, Profil, Connectors, KI-Identitäten und -Modelle,
     Modi-Vokabular, Migrationsmarken.
     INHALT GEHT — Räume, Chats, Threads, Beiträge, Labels, Termine,
     Vorschlags-Zwischenspeicher, externe Menschen, Hallo-Chats.

   Zwei Dinge bleiben ausdrücklich unangetastet:

   1. DIE KANTEN. Eine Kante gehört ZWEI Knoten. Sie hier zu löschen wäre ein
      stiller Schaden auf der Gegenseite — genau das, was der Entwurf verbietet.
      Nur der lokale Inhalt, der durch sie floss, wird geräumt.
   2. DAS EREIGNIS-LOG. Es ist der Grabstein. Ein Reset, der seine eigene Spur
      mitlöscht, ist kein Protokoll, sondern ein Tagebuch des Täters.

   Der Knopf verschwindet nicht nach der Eröffnung. Er ist genau so mächtig wie
   die Löschtaste am Thread — und wie diese erträglich, weil er sich nicht
   verstecken kann: er vernarbt sichtbar für jeden mit Zugang.

   ---------------------------------------------------------------------------
   v1.12.8 (17.09.2026) — ZWEI ÄNDERUNGEN, und die erste ist eine, die ich
   beinahe falsch herum gebaut hätte.

   1. ER FRAGT JETZT AUCH DAS REGISTER — ZUSÄTZLICH, NICHT STATT.

      Ich wollte diese Liste durch cluster_one_raeumen_alle() ERSETZEN. Dann
      habe ich gemessen, WER die Inhalte des Knotens überhaupt schreibt:

          cluster_one_rooms            kern.php · speicher.php     BODEN
          cluster_one_forum_threads    kern.php · speicher.php     BODEN
          cluster_one_gregor_events    kern.php · speicher.php     BODEN
          cluster_one_humans_external  kern.php                    BODEN
          cluster_one_paired_peers     kern.php                    BODEN

      Der Inhalt des Knotens gehört dem BODEN, und der Boden hat keinen
      Räumer. Ein Reset über das Register allein hätte Räume, Themen, Termine
      und Menschen stehengelassen und „fertig" gemeldet.

          Ein zweiter Fehler, der wie eine Reparatur aussieht.

      Umgekehrt nimmt diese Liste allein die Daten der PRODUKTE nicht mit —
      Post Dollys Zitate überlebten bisher jeden Knoten-Reset. Beides
      zusammen ist die Reichweite, die der Knopf seit jeher behauptet:

          Gefahrenzone im Produkt    EINEN Räumer, den eigenen
          Knoten-Reset im Boden      ALLE
          uninstall.php eines Lights EINEN, seinen eigenen

      „ALLE" heisst UND, nicht STATT. Ich hatte es als STATT gelesen.
                                                        (NACHTRAG 19a §3)

   2. ER KANN JETZT ZÄHLEN, OHNE ZU NEHMEN.  $c1_tun === false

      Dieselbe Form wie jeder Räumer (1.9.13): zählen ist nicht Zugabe,
      sondern Bedingung.

          Ein Knopf, der nicht sagen kann, was er kostet, ist eine
          Zusage statt einer Messung.

      Der Trockenlauf schreibt NICHTS: keine Option, keine Narbe, kein
      Ereignis. Ein Probelauf, der eine Spur hinterlässt, ist ein Lauf.

   @param bool $c1_tun  true raeumen · false nur zaehlen
   @return array<string,mixed>  rooms/threads/events/humans/chats/einstellungen
                                als Zahlen, 'produkte' als produkt => was => wieviel */
function cluster_one_node_reset(bool $c1_tun = true): array {
    $zahl = array('rooms' => 0, 'threads' => 0, 'events' => 0, 'humans' => 0,
                  'chats' => 0, 'einstellungen' => 0);

    $rooms = get_option('cluster_one_rooms', array());
    if (is_array($rooms)) {
        foreach (array_keys($rooms) as $rid) {
            if ($c1_tun) { delete_option('cluster_one_room_chat_' . $rid); }
            $zahl['rooms']++;
        }
    }
    if ($c1_tun) { delete_option('cluster_one_rooms'); }

    $threads = get_option('cluster_one_forum_threads', array());
    if (is_array($threads)) {
        foreach (array_keys($threads) as $tid) {
            if ($c1_tun) { delete_option('cluster_one_forum_posts_' . $tid); }
            $zahl['threads']++;
        }
    }
    if ($c1_tun) { delete_option('cluster_one_forum_threads'); }

    $ev = get_option('cluster_one_gregor_events', array());
    $zahl['events'] = is_array($ev) ? count($ev) : 0;
    if ($c1_tun) { delete_option('cluster_one_gregor_events'); }

    $hum = get_option('cluster_one_humans_external', array());
    $zahl['humans'] = is_array($hum) ? count($hum) : 0;
    if ($c1_tun) { delete_option('cluster_one_humans_external'); }

    // Hallo-Chats liegen pro Kante — der Chat geht, die Kante bleibt.
    $map = get_option('cluster_one_paired_peers', array());
    if (is_array($map)) {
        foreach ($map as $edge => $e) {
            if (get_option('cluster_one_chat_' . $edge, null) !== null) {
                $zahl['chats']++;
            }
            if ($c1_tun) {
                delete_option('cluster_one_chat_' . $edge);
                unset($map[$edge]['label_ts_seen']); // aus Inhalt abgeleitet, also zurück auf Anfang
            }
        }
        if ($c1_tun) { update_option('cluster_one_paired_peers', $map, false); }
    }

    foreach (array(
        'cluster_one_suggestions', 'cluster_one_suggestions_order',
        'cluster_one_label_signal_ts', 'cluster_one_known_nodes',
        'cluster_one_peer_fingerprints', 'cluster_one_last_sync',
        'cluster_one_unread_total', 'cluster_one_mute_until',
    ) as $o) {
        if (get_option($o, null) !== null) { $zahl['einstellungen']++; }
        if ($c1_tun) { delete_option($o); }
    }

    /* UND JETZT DIE PRODUKTE — jedes durch SEINE eigene Hand. Der Boden
       kennt hier keinen einzigen fremden Optionsnamen und soll keinen
       kennen.                                           (H115, R-SAMMLER-1) */
    $zahl['produkte'] = function_exists('cluster_one_raeumen_alle')
        ? cluster_one_raeumen_alle($c1_tun)
        : array();

    /* DER TROCKENLAUF ENDET HIER. Keine Narbe, kein Ereignis, keine Option:
       was nicht geschehen ist, wird nicht protokolliert. */
    if (!$c1_tun) {
        return $zahl;
    }

    update_option('cluster_one_reset_at', time(), false);
    update_option('cluster_one_reset_count', $zahl, false);

    cluster_one_event_emit(array(
        'type'           => 'node_reset', // Türereignis, wie thread_deleted: überlebt das Gelöschte
        'container_type' => 'node',
        'container_id'   => (string) cluster_one_node_id(),
        'actor_id'       => cluster_one_actor_id_human(),
        'visibility'     => 'all',
    ), true);

    return $zahl;
}

/** Der Trockenlauf — und er hat einen EIGENEN NAMEN, aus einem Grund.
 *
 * DIE GEMESSENE GEFAHR (w95, Richtung 1, gegen die AUSGELIEFERTE Kopie):
 *
 *     cluster_one_node_reset(false)   an der alten Fassung
 *     -> sie kennt den Parameter nicht, PHP nimmt ihn stillschweigend
 *        entgegen, und die Funktion RAEUMT DEN GANZEN KNOTEN.
 *        Ein Probelauf, der den Ernstfall ausloest.
 *
 * UND DIESE PAARUNG IST MOEGLICH. kern.php und kranz.php sind beide Boden
 * und fahren in EINEM Zug — aber auf einem Knoten gewinnt jede Datei
 * EINZELN nach function_exists. Pacioli traegt KEIN kranz.php. Laedt
 * pacioli zuerst, kommt kern.php von pacioli und kranz.php vom naechsten
 * Plugin. Zwischen dem ersten und dem letzten Aufspielen ist das ein
 * neues kranz.php an einem alten kern.php.
 *
 *     Zwei Dateien, die zusammen geschnitten werden, kommen deshalb
 *     noch lange nicht zusammen an.
 *
 * ALSO FRAGT DIE SEITE NICHT NACH EINEM PARAMETER, SONDERN NACH EINEM
 * NAMEN. Einen Namen, den es nicht gibt, kann man nicht versehentlich
 * rufen — ein Parameter, den es nicht gibt, wird stillschweigend
 * geschluckt.
 *
 *     Ein Riegel, der nach einer FAEHIGKEIT fragt, muss etwas fragen,
 *     das fehlen KANN.                       (dieselbe Gestalt wie H77)
 *
 * @return array<string,mixed> was der Knopf JETZT nehmen wuerde
 */
function cluster_one_node_reset_trocken(): array {
    return cluster_one_node_reset(false);
}

/* v1.9.13 (09.09.2026) — DAS REGISTER DER RAEUMER.
 *
 * GEMELDET VON POST DOLLY, 09.09., und er hat es vorgefuehrt statt behauptet:
 *
 *     ausgeliefert   der Praefix-Besen  21 von 21 Optionen geloescht
 *     repariert      derselbe Knopf      1 von 21
 *     halber Zeuge   derselbe Besen     21 von 21  —  und GRUEN
 *
 * Der dritte Lauf ist der Grund fuer dieses Register. Ein Zeuge, der nur
 * fragt, ob das Gewollte geschehen ist, kann Gruendlichkeit nicht von
 * Uebergriff unterscheiden.
 *
 * UND WARUM ES DAS REGISTER BRAUCHT — der Grund stand im ausgelieferten
 * Quelltext, als Begruendung des Besens:
 *
 *     „Alle Plugin-Optionen in einem Rutsch entfernen
 *      (chat_<edge> sind dynamisch)."
 *
 * Eine LISTE von Namen kann `cluster_one_room_chat_<id>` nicht nennen. Der
 * Praefix war kein Schlendrian, sondern die einzige Antwort, die ohne
 * Register moeglich war. Nur der Eigentuemer kann seine Schluessel
 * BERECHNEN.
 *
 *     Wer eine Liste fragt, bekommt die Welt von gestern.
 *     Wer das Verzeichnis fragt, bekommt die von heute.   (Post Dolly, 28.08.)
 *
 * ICH HATTE DIESEN SAMMLER GESTERN NACHT SELBST ZURUECKGEZOGEN, mit dem
 * Argument, es gebe ihn schon zweimal (Paciolis uninstall.php, Kleinrocks
 * zwei Loeschfunktionen) und ein dritter waere ein dritter Schreibweg.
 * Das Argument war richtig und die Folgerung falsch: dies hier ist KEIN
 * dritter Schreibweg. Es schreibt nichts. Es RUFT die beiden, die schon da
 * sind — und macht sie zaehlbar.
 *
 *     Ein Sammler, der selbst schreibt, ist eine zweite Wahrheit.
 *     Ein Sammler, der nur ruft, ist ein Verzeichnis.
 *
 * DIE FORM EINES RAEUMERS, und sie ist der ganze Vertrag:
 *
 *     function <name>(bool $tun): array
 *         $tun === false   ZAEHLEN. Nichts wird angefasst.
 *         $tun === true    RAEUMEN.
 *     Rueckgabe: was => wieviel, z. B. array('raeume' => 3, 'themen' => 12)
 *
 * Die Zaehlung ist nicht Zugabe, sondern Bedingung: uninstall.php kann nicht
 * fragen, und ein Knopf, der nicht sagen kann, was er kostet, ist eine
 * Zusage statt einer Messung. Kleinrock hat die Form am 22.08. gebaut, bevor
 * jemand wusste, wofuer sie gebraucht wird — seine Loeschfunktionen geben
 * seit jeher eine Zaehlung zurueck, „damit der Mensch das VORHER sieht".
 *
 * WAS DAS REGISTER NICHT KENNT: Optionsnamen. Keine. Es weiss nur, WER
 * raeumen kann. Damit kann es nicht veralten, wenn ein Produkt seine
 * Schluessel aendert — und es kann kein fremdes Eigentum anfassen.
 */
function cluster_one_raeumer(): array {
    $c1_r = apply_filters('cluster_one_raeumer', array());
    if (!is_array($c1_r)) {
        return array();
    }
    $c1_aus = array();
    /* R-SAMMLER-1 (17.09.2026) — ANHAENGEN UND HINTERHER URTEILEN.

       POST DOLLY HAT DAS LOCH GEMESSEN, nicht behauptet: er hat eine Welt
       gebaut, in der zwei Organdateien unter EINEM Wirt je einen Raeumer
       anmelden. Ergebnis: ein Eintrag im Register, „Verworfen gemeldet: 0".

           Ein Waechter, der prueft, ob der Eintrag GUELTIG ist, prueft nicht,
           ob der PLATZ FREI war.                             (Post Dolly)

       UND ER KONNTE ES AUCH NICHT. Bis hierher meldete sich jedes Haus mit
       $c1_r[<schluessel>] = <name>. Wenn apply_filters zurueckkommt, hat der
       zweite den ersten laengst ueberschrieben — der Verlierer ist weg, bevor
       diese Funktion ihn sehen kann.

           Der Fehler liegt nicht im Waechter, sondern in der FORM DER
           UEBERGABE.                                          (Post Dolly)

       DIE FORM, DIE TRAEGT, STAND SEIT H104 NEBENAN: kasper.php sammelt
       Melder in eine LISTE ($alle[$organ]['melder'][] = …) und entscheidet
       DANACH. Genau deshalb war H104 ueberhaupt findbar.

       ALSO NEU, und es ist die EINZIGE erlaubte Form:

           $c1_r[] = array(
               'produkt' => 'post-dolly',
               'fn'      => 'cluster_one_post_dolly_raeumen',
               'wirt'    => cluster_one_kasper_wirt(__FILE__),
           );

       'produkt'  sagt, WESSEN Daten geraeumt werden.
       'wirt'     sagt, in welchem PAKET der Raeumer liegt — das ist die
                  Frage des Knopfes, und es ist NICHT dieselbe Frage.
                      Der Wirt beantwortet: in welchem PAKET liege ich.
                      Nicht: welches Organ bin ich.            (Post Dolly)

       DIE ALTE FORM WIRD NICHT STILL MITGELESEN. Zwei erlaubte Formen waeren
       zwei Wahrheiten ueber dieselbe Sache — der Boden sagt diesen Satz zehn
       Zeilen weiter unten ueber die Produktanmeldung, und er gilt hier
       genauso. Wer die alte Form schickt, bekommt eine Meldung und keinen
       Eintrag: fail closed mit benanntem Grund. */
    foreach ($c1_r as $c1_schluessel => $c1_eintrag) {
        if (!is_array($c1_eintrag)) {
            if (function_exists('_doing_it_wrong')) {
                // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Entwicklermeldung an _doing_it_wrong(): kein Seiteninhalt; WordPress legt sie selbst durch wp_kses (wp_trigger_error)
                _doing_it_wrong(
                    'cluster_one_raeumer',
                    sprintf(
                        'Ein Raeumer hat sich in der ALTEN Form angemeldet '
                      . '(%s unter dem Schluessel %s). Erwartet: '
                      . "array('produkt' => …, 'fn' => …, 'wirt' => …), "
                      . 'ANGEHAENGT mit $c1_r[] und nicht zugewiesen. '
                      . 'Seine Daten bleiben stehen.',
                        is_string($c1_eintrag) ? $c1_eintrag . '()' : gettype($c1_eintrag),
                        (string) $c1_schluessel
                    ),
                    '1.12.6'
                );
                // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            }
            continue;
        }
        /* v1.12.10 (18.09.2026) — DER SCHLUESSEL SAGT, WOHER DER PLATZ KOMMT.
           POST DOLLYS FUND, und er hat ihn vorgefuehrt statt behauptet: drei
           Welten, A angehaengt/verschieden, B angehaengt/gleich, C zugewiesen.

               B und C sind DERSELBE Fehler: zwei Haeuser wollen denselben
               Platz. In B sieht der Sammler beide und NENNT den Verlierer.
               In C sieht er einen. Der andere war weg, bevor er hinsah.

           UND DER RIEGEL DARUEBER KONNTE IHN NICHT SEHEN. Er fragt
           is_array($c1_eintrag) — die GESTALT des Werts. Ein zugewiesener
           Eintrag in der NEUEN Gestalt ist ein gueltiges Array und kommt
           durch. Die HERKUNFT DES PLATZES stand daneben und wurde nicht
           gefragt:

               ein ANGEHAENGTER Eintrag hat einen GANZZAHLIGEN Schluessel,
               ein ZUGEWIESENER einen zeichenkettigen.

           ER WIRD GENOMMEN, NICHT VERWORFEN. Ein verworfener Raeumer laeuft
           beim Loeschen nicht, und dann bleiben Daten liegen — das ist
           schlimmer als die falsche Form. Wer vor ihm auf dem Platz stand,
           ist ohnehin schon weg; genau das ist die Meldung.

               Ein Waechter, der prueft, ob der Eintrag GUELTIG ist, prueft
               nicht, ob der PLATZ FREI war.                    (Post Dolly)

           v1.12.11 (19.09.2026) — UND DIESER SATZ HAT MEHR BEHAUPTET, ALS
           DER RIEGEL PRUEFT. Post Dolly hat es an sechs Welten gemessen und
           ausdruecklich NICHTS daran gebaut:

               ein ZEICHENKETTIGER Schluessel BEWEIST eine Zuweisung.
               Die Gegenrichtung traegt nicht: ein GANZZAHLIGER beweist
               KEIN Anhaengen.                                 (Post Dolly)

           WAS DAMIT UNENTDECKT BLEIBT, und es steht hier, damit es niemand
           fuer gefangen haelt:

               $c1_r[0]   = array(...)     -> Register 1, 0 Meldungen
               $c1_r['0'] = array(...)     -> PHP macht daraus int 0,
                                              Register 1, 0 Meldungen

           Beide sind Zuweisungen, beide nehmen einem Anhaenger den Platz,
           und beide sehen von HIER aus aus wie ein angehaengter erster
           Eintrag. Der Riegel kann sie nicht sehen — nicht weil er schlecht
           gebaut ist, sondern weil an dieser Stelle nichts mehr da ist,
           woran man es unterscheiden koennte.

           DIE NAHELIEGENDE VERSCHAERFUNG IST GEMESSEN UND VERWORFEN. „Die
           Schluessel muessen 0..n-1 sein" meldet $c1_r[5] = … (wo NIEMAND
           verliert) und schweigt bei $c1_r[0] = … (wo jemand verliert).

               Sie wuerde genau falsch herum melden.            (Post Dolly)

           ALSO BLEIBT DER RIEGEL, WIE ER IST, und der Satz sagt, was er
           kann. Die Form gehoert dorthin, wo sie sichtbar ist: in den
           Quelltext des Hauses, wo w96 sie liest — nicht hierher, wo nur
           noch das Ergebnis ankommt.

               Eine Luecke, die aus der Stelle nicht messbar ist, gehoert
               in den Satz und nicht in den Code.               (Post Dolly)

           GEMESSEN VON M103.                                (R-SAMMLER-2) */
        if (!is_int($c1_schluessel) && function_exists('_doing_it_wrong')) {
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Entwicklermeldung an _doing_it_wrong(): kein Seiteninhalt; WordPress legt sie selbst durch wp_kses (wp_trigger_error)
            _doing_it_wrong(
                'cluster_one_raeumer',
                sprintf(
                    'Ein Raeumer wurde ZUGEWIESEN statt angehaengt (Produkt %s, '
                  . 'Wirt %s, unter dem Schluessel %s). Erwartet: $c1_r[] = array(…). '
                  . 'Sein Eintrag wird genommen — aber wer vor ihm auf diesem '
                  . 'Platz stand, ist bereits verloren und kann nicht mehr '
                  . 'genannt werden.',
                    (string) ($c1_eintrag['produkt'] ?? '?'),
                    (string) ($c1_eintrag['wirt'] ?? '?'),
                    (string) $c1_schluessel
                ),
                '1.12.10'
            );
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        $c1_produkt = (string) ($c1_eintrag['produkt'] ?? '');
        $c1_fn      = $c1_eintrag['fn'] ?? null;
        $c1_wirt    = (string) ($c1_eintrag['wirt'] ?? '');
        if ($c1_produkt === '') {
            continue;
        }
        /* DER PLATZ WIRD JETZT GEPRUEFT, WEIL ER GEPRUEFT WERDEN KANN.
           Zwei Anmeldungen auf denselben Produktnamen: die ERSTE gilt, die
           zweite wird GENANNT. Nicht umgekehrt — wer zuerst da war, hat
           nichts falsch gemacht.
               Ein Raeumer, der still herausfaellt, ist von „hat sich nie
               angemeldet" nicht zu unterscheiden. */
        if (isset($c1_aus[$c1_produkt])) {
            if (function_exists('_doing_it_wrong')) {
                // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Entwicklermeldung an _doing_it_wrong(): kein Seiteninhalt; WordPress legt sie selbst durch wp_kses (wp_trigger_error)
                _doing_it_wrong(
                    'cluster_one_raeumer',
                    sprintf(
                        'ZWEI Raeumer fuer dasselbe Produkt %s: %s (Wirt %s) steht, '
                      . '%s (Wirt %s) wird NICHT gefuehrt. Seine Daten bleiben stehen.',
                        $c1_produkt,
                        (string) $c1_aus[$c1_produkt]['fn'],
                        (string) $c1_aus[$c1_produkt]['wirt'],
                        is_string($c1_fn) ? $c1_fn : gettype($c1_fn),
                        $c1_wirt
                    ),
                    '1.12.6'
                );
                // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            }
            continue;
        }
        /* NUR EIN FUNKTIONSNAME ALS ZEICHENKETTE — dieselbe Bedingung wie bei
           cluster_one_kranz_abschnitte, und aus demselben Grund: ein Raeumer,
           der einen Namen hat, laesst sich messen, benennen und in einem
           Bericht zitieren. Eine Closure nicht.

           UND WER VERWORFEN WIRD, ERFAEHRT ES (H218, 05.09.). Ein Raeumer,
           der still herausfaellt, ist von „hat sich nie angemeldet" nicht zu
           unterscheiden — und bei einem RAEUMER heisst das: seine Daten
           bleiben liegen, und niemand weiss warum. */
        if (is_string($c1_fn) && $c1_fn !== '' && function_exists($c1_fn)) {
            $c1_aus[$c1_produkt] = array('fn' => $c1_fn, 'wirt' => $c1_wirt);
            continue;
        }
        if (function_exists('_doing_it_wrong')) {
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Entwicklermeldung an _doing_it_wrong(): kein Seiteninhalt; WordPress legt sie selbst durch wp_kses (wp_trigger_error)
            _doing_it_wrong(
                'cluster_one_raeumer',
                sprintf(
                    'Produkt %s hat einen Raeumer angemeldet, der nicht gerufen '
                  . 'werden kann (%s). Seine Daten bleiben stehen.',
                    $c1_produkt,
                    is_string($c1_fn) ? $c1_fn . '()' : get_debug_type($c1_fn)
                ),
                '1.9.13'
            );
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
    ksort($c1_aus, SORT_STRING);
    return $c1_aus;
}

/** WARUM WIRD GERAEUMT? — DER ANLASS. (v1.12.13, 23.09.2026)
 *
 * KLEINROCKS FUND, und er hat ihn als Frage gestellt, nicht als Zeile:
 *
 *     „Die REICHWEITE gehoert in den Raeumer-Vertrag, nicht in eine
 *      Konstantenfrage in meiner Datei."
 *
 * ER HATTE defined('WP_UNINSTALL_PLUGIN') GEBAUT, und es traegt heute —
 * aber es traegt aus einem Grund, der nicht in seiner Datei steht:
 *
 *     Die Konstante ist GLOBAL. Sie ist fuer die ganze Anfrage wahr, auch
 *     wenn ein ANDERES Plugin das geloeschte ist. Dass sein Raeumer dann
 *     trotzdem nicht laeuft, liegt an cluster_one_raeumen_mein(), das nach
 *     dem WIRT filtert — also an einer Eigenschaft des BODENS, ueber die
 *     seine Datei nichts weiss und nichts wissen kann.
 *
 *         Eine Abhaengigkeit, die getragen wird, ist kein Mangel. Eine,
 *         die niemand genannt hat, wird beim naechsten Schnitt fallen
 *         gelassen, ohne dass jemand etwas sagt.
 *
 * ALSO BEANTWORTET DER BODEN DIE FRAGE, und zwar dort, wo sie entschieden
 * wird: an der Stelle, an der er die Raeumer ruft. Die drei Reichweiten
 * tragen den Anlass schon in ihrem Namen — er wird nur nicht gesagt:
 *
 *     cluster_one_raeumen_mein()    uninstall.php eines Lights  -> 'uninstall'
 *     cluster_one_raeumen_eines()   Gefahrenzone im Produkt     -> 'reset'
 *     cluster_one_raeumen_alle()    Knoten-Reset im Boden       -> 'reset'
 *
 * UND tun=false IST KEIN EIGENER ANLASS. Ein Trockenlauf ist derselbe
 * Anlass, nur ohne Griff — wer im Trockenlauf einen anderen Anlass saehe,
 * bekaeme andere Zahlen als beim Druecken, und genau das misst Christian
 * jedes Mal nach.
 *
 *     Ein Trockenlauf, der etwas anderes zaehlt als der Lauf, ist kein
 *     Trockenlauf.
 *
 * WAS EIN HAUS DAMIT TUT — eine Zeile, und sie fragt nicht WordPress:
 *
 *     $c1_deinstalliert = (cluster_one_raeumen_anlass() === 'uninstall');
 *
 * AUSSERHALB EINES RAEUMLAUFS IST DIE ANTWORT ''. Nicht 'reset', nicht
 * 'uninstall' — leer. Wer ausserhalb fragt, hat keine Frage gestellt, die
 * hier beantwortet werden kann, und bekommt das gesagt statt geraten.
 *
 * @param string|null $c1_setzen  nur der Boden setzt. null = lesen.
 */
function cluster_one_raeumen_anlass(?string $c1_setzen = null): string {
    static $c1_wert = '';
    if ($c1_setzen !== null) {
        $c1_wert = $c1_setzen;
    }
    return $c1_wert;
}

/** EIN Produkt raeumen oder zaehlen. Die Reichweite der Gefahrenzone.
 *
 * @return array{gefunden:bool,zahlen:array<string,int>}
 */
function cluster_one_raeumen_eines(string $produkt, bool $tun = false): array {
    $c1_alle = cluster_one_raeumer();
    if (!isset($c1_alle[$produkt])) {
        /* NICHT GEMESSEN, und es wird gesagt: ein Produkt ohne Raeumer ist
           etwas anderes als ein Produkt ohne Daten. */
        return array('gefunden' => false, 'zahlen' => array());
    }
    cluster_one_raeumen_anlass('reset');
    $c1_z = call_user_func($c1_alle[$produkt]['fn'], $tun);
    cluster_one_raeumen_anlass('');
    return array('gefunden' => true, 'zahlen' => is_array($c1_z) ? $c1_z : array());
}

/** ALLE RAEUMER MEINES PAKETS — die Reichweite der Gefahrenzone (VERTRAG 28 §4).
 *
 * DIE FRAGE DES KNOPFES IST NICHT „welches Organ bin ich", sondern „welche
 * Raeumer liegen in MEINEM Paket". Post Dollys Unterscheidung, 17.09.:
 *
 *     im Light     genau einer
 *     im Monolith  alle
 *     und beides ist jeweils richtig.
 *
 * Bis hierher hat der Knopf stattdessen eine Kehre ueber
 * LIKE 'cluster_one_%' gefahren und dabei die Optionen von vier fremden
 * Haeusern geloescht — darunter cluster_one_paired_peers, die KANTEN, die
 * der Knoten-Reset zwei Funktionen weiter ausdruecklich unantastbar nennt.
 *
 *     Ein Praefix, das den Namen EINES Dinges trug, traegt nach einer
 *     Teilung den Namen der FAMILIE.                        (R-PRAEFIX-1)
 *
 * KEIN OPTIONSNAME STEHT MEHR IM BODEN. Das Register kennt keine — es weiss
 * nur, WER raeumen kann. Damit kann es nicht veralten, wenn ein Produkt seine
 * Schluessel aendert, und es kann kein fremdes Eigentum anfassen.
 *
 * DREI AUSGAENGE, und der mittlere ist der, auf den es ankommt:
 *     gefunden > 0   es wurde geraeumt (oder gezaehlt)
 *     gefunden = 0   dieses Paket hat KEINEN Raeumer -> es wird NICHTS
 *                    geloescht, und der Knopf sagt das
 *     kein Wirt      die Lage ist unbekannt -> ebenfalls nichts
 *
 * @return array{wirt:string,gefunden:int,zahlen:array<string,array<string,int>>}
 */
function cluster_one_raeumen_mein(string $c1_datei = '', bool $tun = false): array {
    if ($c1_datei === '') {
        $c1_datei = __FILE__;
    }
    $c1_wirt = function_exists('cluster_one_kasper_wirt')
        ? cluster_one_kasper_wirt($c1_datei)
        : '';
    if ($c1_wirt === '') {
        return array('wirt' => '', 'gefunden' => 0, 'zahlen' => array());
    }
    $c1_aus = array();
    /* DER ANLASS STEHT UM DEN LAUF, nicht um den einzelnen Ruf: ein Raeumer
       darf einen zweiten rufen, und der soll denselben Anlass sehen. Danach
       wird er zurueckgenommen — wer ausserhalb fragt, bekommt ''. */
    cluster_one_raeumen_anlass('uninstall');
    foreach (cluster_one_raeumer() as $c1_produkt => $c1_e) {
        if ((string) ($c1_e['wirt'] ?? '') !== $c1_wirt) {
            continue;
        }
        $c1_z = call_user_func($c1_e['fn'], $tun);
        $c1_aus[$c1_produkt] = is_array($c1_z) ? $c1_z : array();
    }
    cluster_one_raeumen_anlass('');
    return array('wirt' => $c1_wirt, 'gefunden' => count($c1_aus), 'zahlen' => $c1_aus);
}

/** ALLE raeumen oder zaehlen. Die Reichweite des Knoten-Resets.
 *
 * Post Dollys drei Reichweiten, und jede traegt den Namen, den sie verdient:
 *     Gefahrenzone im Produkt    EINEN Raeumer, den eigenen
 *     Knoten-Reset im Boden      ALLE
 *     uninstall.php eines Lights EINEN, seinen eigenen
 *
 * @return array<string,array<string,int>>  produkt => was => wieviel
 */
function cluster_one_raeumen_alle(bool $tun = false): array {
    $c1_aus = array();
    cluster_one_raeumen_anlass('reset');
    foreach (cluster_one_raeumer() as $c1_produkt => $c1_e) {
        $c1_z = call_user_func($c1_e['fn'], $tun);
        $c1_aus[$c1_produkt] = is_array($c1_z) ? $c1_z : array();
    }
    cluster_one_raeumen_anlass('');
    return $c1_aus;
}

/** Sichtbare Narbe des Resets — für JEDEN mit Zugang, nicht nur für den SysOp.
   Wer einen leeren Knoten vorfindet, muss sehen können, dass geräumt wurde,
   sonst zweifelt er an seinem Gedächtnis statt am SysOp. Nicht ins Mesh, nicht
   nach außen: Sichtbarkeit auf die Betroffenen begrenzt. */
function cluster_one_render_reset_notice(): void {
    $ts = (int) get_option('cluster_one_reset_at', 0);
    if ($ts <= 0) {
        return;
    }
    $z = (array) get_option('cluster_one_reset_count', array());
    echo '<div class="notice notice-info"><p>' . esc_html(sprintf(
        /* translators: %1$s: Datum und Uhrzeit des Resets, %2$d: Raeume, %3$d: Threads, %4$d: Termine. */
        __('This node was reset by the SysOp on %1$s — rooms, threads, meetings and guest records were removed. Removed: %2$d rooms, %3$d threads, %4$d meetings. Settings and edges were kept.', 'cluster-one-bismarck'),
        date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $ts),
        (int) ($z['rooms'] ?? 0), (int) ($z['threads'] ?? 0), (int) ($z['events'] ?? 0)
    )) . '</p></div>';
}

function cluster_one_save_ai_identities(array $a): void {
    update_option('cluster_one_ai_identities', $a, false);
}

/* --- Punkt-Gesetz: grün erst nach geglücktem, persistiertem Aufruf ----------
   cluster_one_ai_verified: Provider-$cid -> Zeitstempel des letzten geglückten
   Aufrufs. Status ist erkannter Fakt, nie Behauptung. */
function cluster_one_get_ai_verified(): array {
    $a = get_option('cluster_one_ai_verified', array());
    return is_array($a) ? $a : array();
}

function cluster_one_ai_is_verified(string $cid): bool {
    $v = cluster_one_get_ai_verified();
    return isset($v[$cid]) && (int) $v[$cid] > 0;
}

function cluster_one_mark_ai_verified(string $cid): void {
    $v = cluster_one_get_ai_verified();
    $v[$cid] = time();
    update_option('cluster_one_ai_verified', $v, false);
}

/** Gemeinsamer Status-Punkt für KI-Connectoren (drei Aufrufstellen, eine Wahrheit):
    grün ● „verbunden" NUR wenn ein Aufruf geglückt ist; sonst grau ○ + Schlüssel-
    Text. $state stammt aus cluster_one_connector_has_key(). Gibt fertiges HTML. */
function cluster_one_ai_status_dot(string $cid, array $state): string {
    if (cluster_one_ai_is_verified($cid)) {
        return '<span style="color:#1a7f37;" aria-hidden="true">●</span> '
             . esc_html__('connected', 'cluster-one-bismarck');
    }
    $txt = ! empty($state['configured']) ? __('key on file', 'cluster-one-bismarck') : __('no key', 'cluster-one-bismarck');
    return '<span style="color:#8a8a8a;" aria-hidden="true">○</span> ' . esc_html($txt);
}

/* --- KORB 1: eine KI antworten lassen (Default-Route, B-1) -------------------
   Baut aus dem jüngsten Raum-Transkript einen Prompt, ruft die WP-7.0-AI-Client-
   API auf und gibt ['ok'=>bool, 'text'=>string, 'err'=>code] zurück. KEIN
   Modellwunsch (B-1): der Core wählt das Modell des konfigurierten Providers.
   Fehler werden als Code zurückgegeben (no_client/no_key/rejected/timeout/empty/
   failed); der genaue Provider-Text landet im Aufrufer in einem Transient. */
/** Wie viele KI-Connectoren haben einen Schlüssel? Entscheidet, ob ein nicht
   greifender Pin still (1 Provider = eindeutig) oder als Fehler (≥2 = mehrdeutig)
   behandelt wird. */
function cluster_one_count_keyed_ai_connectors(): int {
    $n = 0;
    foreach (cluster_one_get_ai_connectors() as $cid => $conn) {
        $state = cluster_one_connector_has_key((string) $cid, (array) $conn);
        if (! empty($state['configured'])) {
            $n++;
        }
    }
    return $n;
}



/* --- KI-Kennungen speichern (knotenweit) --- */
add_action('admin_post_cluster_one_ai_identities_save', function () {
    // v0.0.61 (P0-2): SysOp-exklusiv. Vorher konnte JEDES Mitglied per POST
    // beliebige WP-User als KI-Identität eintragen — und die bekommen über
    // cluster_one_can_access() automatisch Vollzugriff (Mitgliedschafts-
    // Eskalation an der Weißliste vorbei). Zusätzlich: Connector-ID muss
    // real existieren, der WP-User auch — keine erfundenen Kennungen mehr.
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_ai_identities');
    $connectors = cluster_one_get_ai_connectors();
    $ids = array();
    if (isset($_POST['identities']) && is_array($_POST['identities'])) {
        foreach (wp_unslash($_POST['identities']) as $cid => $uid) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- jedes Glied wird in den naechsten Zeilen gereinigt (sanitize_text_field, int) und gegen bekannte Connectoren geprueft
            $cid = sanitize_text_field($cid);
            $uid = (int) $uid;
            if ($cid !== '' && isset($connectors[$cid]) && $uid > 0 && get_userdata($uid)) {
                $ids[$cid] = $uid;
            }
        }
    }
    cluster_one_save_ai_identities($ids);
    $models = array();
    if (isset($_POST['models']) && is_array($_POST['models'])) {
        foreach (wp_unslash($_POST['models']) as $cid => $mdl) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- jedes Glied wird in den naechsten Zeilen gereinigt (sanitize_text_field) und gegen bekannte Connectoren geprueft
            $cid = sanitize_text_field($cid);
            $mdl = trim(sanitize_text_field($mdl));
            if ($cid !== '' && isset($connectors[$cid]) && $mdl !== '') {
                $models[$cid] = $mdl;
            }
        }
    }
    cluster_one_save_ai_models($models);
    /* v0.9.47: Die Kostenaussage des SysOps, gespeichert wie die Modell-Angabe
       — gleiches Muster, gleicher Handler, kein zweiter Schreibweg.
       Angenommen wird nur, was ein bekannter Connector IST; ein Haken an etwas
       Unbekanntem waere ein manipuliertes Formular. */
    $c1_selbst = array();
    if (isset($_POST['selfhosted']) && is_array($_POST['selfhosted'])) {
        foreach (wp_unslash($_POST['selfhosted']) as $c1_cid) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- jedes Glied wird in der naechsten Zeile gereinigt (sanitize_text_field) und gegen bekannte Connectoren geprueft
            $c1_cid = sanitize_text_field($c1_cid);
            if ($c1_cid !== '' && isset($connectors[$c1_cid])) {
                $c1_selbst[] = $c1_cid;
            }
        }
    }
    cluster_one_save_ai_selfhosted($c1_selbst);
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck_settings&ident_saved=1'));
    exit;
});

/**
 * BLOCK FUER kern.php — KEINE DATEI, DIE AUSGELIEFERT WIRD.
 *
 * Gehoert unmittelbar NACH dem Empfaenger, den er bedient:
 * `add_action('admin_post_cluster_one_ai_identities_save', …)`,
 * kern.php:1031–1080 in 1.9.9. Also ab Zeile 1081.
 *
 *     Boden 1.9.9    kern.php   sha1 1ec02e229cdf…   (Monolith)
 *     Paket  0.2.5   kern.php   sha1 0f399c53b499…
 *
 * ICH HABE kern.php NICHT ANGEFASST.
 *
 * ---------------------------------------------------------------------------
 * WOHER DIESER RUMPF KOMMT
 *
 * Er ist die eine Haelfte von `cluster_one_render_bismarck_settings()`
 * (kranz.php:238, 126 Zeilen). WOERTLICH herausgeschnitten, keine Zeile
 * umformuliert. Entfernt sind genau drei Zeilen — die drei
 * `do_action('cluster_one_abschnitt', 'menschen')` an den drei Austritten.
 * Die bleiben bei Bismarck; was hier steht, ist alles Uebrige.
 *
 *     Bezeugt: die alte und die neue Fassung drucken in allen drei
 *     Austrittswelten BYTE-IDENTISCH, und der Haken feuert vorher wie
 *     nachher genau einmal je Aufruf.
 *
 * ---------------------------------------------------------------------------
 * WARUM SIE IN DEN BODEN GEHOERT, UND ES SIND NICHT DIE GRUENDE AUS DEM BEFUND
 *
 * Mammuts Grund war: die Maske gehoert zu ihrem Empfaenger, und der steht im
 * Boden. Der traegt. Beim Nachmessen ist er aber nicht der staerkste:
 *
 *     Die Seite `cluster_one_bismarck_settings` ist der Landeplatz von
 *     FUENF Boden-Handlern, nicht von einem:
 *
 *       kern.php:1031  ai_identities_save         -> ident_saved
 *       kern.php:1105  excluded_role_save         -> role_saved
 *       kern.php:1311  human_external_add         -> human_saved
 *       kern.php:1340  human_external_delete      -> human_deleted
 *       kern.php:1363  human_external_visibility  -> human_saved
 *
 *     Alle fuenf schreiben den Slug woertlich in ein wp_safe_redirect().
 *
 * DER BODEN HAT DIE ADRESSE DIESER SEITE FUENFMAL AUFGESCHRIEBEN. Eine Seite,
 * deren Anschrift fuenfmal im Boden steht, ist eine Boden-Seite — das ist
 * kein Geschmacksurteil, das steht im Quelltext.
 *
 * Und die Richtung ist beide Male dieselbe: VIER der fuenf landen auf
 * Meldungen, die BISMARCK zeichnet (bismarck-seiten.php, am Formular mit
 * wp_nonce_field('cluster_one_ai_wish'), im
 * Abschnitt 'menschen'). Der Boden schreibt also nicht nur die Anschrift der
 * Seite, sondern auch die Parameter, die Bismarcks Abschnitt liest.
 *
 *     Keine der beiden Haelften kann die Seite verlieren. Deshalb geht sie
 *     dorthin, wo beide sie erreichen, und Bismarck erscheint darauf ueber
 *     den Haken — genau so, wie er es heute schon tut.
 *
 * ---------------------------------------------------------------------------
 * WAS DIESER BLOCK NICHT LOEST — und das ist die wichtigste Zeile hier
 *
 * Er verschiebt die FUNKTION, nicht die SEITE. Nach diesem Block allein hat
 * ein Knoten mit Pacioli + Kleinrock ohne Bismarck:
 *
 *     die Maske als Funktion   -> da, im Boden
 *     die Seite, die sie zeigt -> immer noch in kranz.php
 *     also weiterhin: keine Stelle, an der ein Mensch eine KI zuordnet.
 *
 * Die Seite ist elf Zeilen (`cluster_one_render_bismarck_settings_page`,
 * kranz.php:221) plus ihre Anmeldung (menue.php:218). Sie muss mit, und ihr
 * Ruf auf `cluster_one_render_kranz_tabs()` braucht dabei denselben Riegel
 * wie kern.php:2586 und kranz.php:1951.
 *
 *     Ich habe sie NICHT mitgeliefert. Eine Seite umzuziehen heisst, ihre
 *     Anmeldung umzuziehen, und die Reihenfolge im Menue ist Vertragssache.
 *     Wer die Funktion verschiebt und die Seite vergisst, hat den Ausfall
 *     nicht behoben, sondern unsichtbarer gemacht.
 *
 * ---------------------------------------------------------------------------
 * UEBERHOLT AM 11.10.2026 — DER ABSATZ DARUEBER IST ERLEDIGT, UND ZWAR GANZ.
 *
 * Nachgemessen im ausgelieferten 0.2.40, nicht erinnert:
 *
 *     cluster_one_render_bismarck_settings_page   wohnt in DIESER Datei
 *     ihre Anmeldung                              steht in menue.php, ohne
 *                                                 Organ-Riegel, und der
 *                                                 Kommentar darueber sagt
 *                                                 warum: geriegelt ist der
 *                                                 EINE Ruf auf die
 *                                                 Reiterleiste, „wie bei der
 *                                                 Chronik"
 *     in kranz.php                                KEIN Treffer mehr
 *
 * „die Seite, die sie zeigt -> immer noch in kranz.php" ist also falsch, und
 * „Ich habe sie NICHT mitgeliefert" ist eingeloest. Der Absatz bleibt stehen,
 * weil seine Begruendung gilt — nur sein Zustand nicht mehr.
 *
 *     Ein offener Posten, der erledigt ist und offen aussieht, kostet beim
 *     naechsten Lesen genau so viel Zeit wie beim ersten.
 *
 * DIE FUENF ADRESSEN OBEN (kern.php:1031/1105/1311/1340/1363) ZEIGEN LAENGST
 * DANEBEN — die Datei ist seitdem gewachsen. Ihr Wortlaut steht in derselben
 * Tabelle und ist findbar: ai_identities_save · excluded_role_save ·
 * human_external_add · human_external_delete · human_external_visibility.
 * Nach R-VERWEIS-1 traegt die Tabelle den Wortlaut und nicht die Zahl; die
 * Zahlen bleiben hier als das stehen, was sie sind — der Stand von damals.
 * ---------------------------------------------------------------------------
 * KATALOG
 *
 * Der Rumpf traegt 18 uebersetzte Zeichenketten. KEINE ist neu, KEINE ist
 * geaendert — sie wechseln die Datei, nicht den Katalog. Im Monolithen tragen
 * sie die Monolith-Domaene, im Paket-Schnitt die des Hauses; das macht
 * Hollerith wie immer. Es ist kein Eintrag zu machen.
 *
 * (Dieser Absatz nennt die beiden Domaenen absichtlich NICHT beim Namen.
 * Stuenden sie hier als Zeichenkette, schriebe der Schnitt sie mit um — und
 * der Satz behauptete danach etwas anderes, als er sagen soll. Genau die
 * Form, die in kranz.php:87 schon einmal aufgefallen ist: ein Kommentar, der
 * seine eigene Behauptung bricht, wird geglaubt und gegrept.)
 *
 * ---------------------------------------------------------------------------
 * BEZEUGT: zeuge-ki-liste.php — schreiben, lesen, loeschen, lesen.
 */

/** Die Maske fuer die KI-Kennungen dieses KNOTENS: welcher Anbieter spricht
   als welcher WP-Benutzer, mit welchem Modell, und auf wessen Maschine.

   Sie war bis 1.9.9 die eine Haelfte von cluster_one_render_bismarck_settings()
   und ist die EINZIGE Maske im ganzen Monolithen, an der ein Mensch eine KI an
   einen Schluessel bindet. Ihr Empfaenger steht direkt darueber. */
function cluster_one_render_ai_identities_form(): void {
    if (isset($_GET['ident_saved'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('AI identities saved.', 'cluster-one-bismarck') . '</p></div>';
    }
    ?>
        <h2><?php esc_html_e('AI identities', 'cluster-one-bismarck'); ?></h2>
        <p style="max-width:680px;opacity:.8;">
            <?php esc_html_e('Assign each provider a WP user. This is the AI’s identity across all rooms — its name in the forum and, via the bridge, on Discord/Matrix. Set once here, node-wide.', 'cluster-one-bismarck'); ?>
        </p>
        <?php
        if (!function_exists('wp_get_connectors')) {
            ?>
            <div class="notice notice-info inline" style="margin:1em 0;padding:12px;max-width:680px;">
                <p style="margin:0;">
                    <?php printf(
                        /* translators: %s: WordPress-Version dieses Knotens. */
                        wp_kses_post(__('The Connectors API was not found. It is available from <strong>WordPress 7.0</strong>. Current version: <code>%s</code>.', 'cluster-one-bismarck')),
                        esc_html(get_bloginfo('version'))
                    ); ?>
                </p>
            </div>
            <?php
            return;
        }
        $connectors = cluster_one_get_ai_connectors();
        if (empty($connectors)) {
            echo '<p style="opacity:.7;">' . esc_html__('No AI connectors found. Install a provider plugin under Settings → Connectors.', 'cluster-one-bismarck') . '</p>';
            return;
        }
        $ids      = cluster_one_get_ai_identities();
        $wp_users = get_users(array(
            'fields'       => array('ID', 'display_name'),
            'role__not_in' => array(cluster_one_excluded_role_slug()),
        ));
        $models   = cluster_one_get_ai_models();
        $c1_selbst = cluster_one_get_ai_selfhosted(); // v0.9.47: die Kostenaussage
        ?>
        <?php /* v1.9.8 (H215) — DER WEG ZUR ANDEREN SEITE.

           Die Auswahlkaesten unten verlangen einen WP-Benutzer und sagen
           nicht, wo man ihn herbekommt. Paciolis Seite legt sie an und sagt
           nicht, dass der Knoten sie erst benutzt, wenn sie hier stehen.
           Zwei Seiten, und keine kennt die andere.

               Ein Angebot ohne Weg.

           Es ist die fuenfte Auspraegung derselben Form in fuenf Tagen
           (H198, H208, H210, H205, hier) — wir bauen Tueren und schreiben
           nicht daneben, wohin sie fuehren. Die andere Haelfte steht auf
           Paciolis Seite und ist seine.

           UND DIE VERSTECKTE LISTE GEHOERT DAZU. Die KI-Konten sind aus
           `Benutzer → Alle Benutzer` ausgeblendet, mit Absicht und mit
           Begruendung — nur sieht ein Konto, das absichtlich unsichtbar ist,
           genau aus wie ein Konto, das es nicht gibt. */ ?>
        <p class="description" style="max-width:760px;margin:1em 0 0;">
            <?php echo wp_kses_post(__('The AIs themselves are created under <em>Tools → My AIs</em> — that is also where their keys are kept. They are deliberately hidden from the normal user list; to see them, open <code>users.php?pacioli_show_ai=1</code>.', 'cluster-one-bismarck')); ?>
        </p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:760px;">
            <?php wp_nonce_field('cluster_one_ai_identities'); ?>
            <input type="hidden" name="action" value="cluster_one_ai_identities_save">
            <table class="widefat striped" style="margin-top:1em;">
                <thead><tr>
                    <th><?php esc_html_e('Provider', 'cluster-one-bismarck'); ?></th>
                    <th><?php esc_html_e('Status', 'cluster-one-bismarck'); ?></th>
                    <th><?php esc_html_e('Identity (WP user)', 'cluster-one-bismarck'); ?></th>
                    <th><?php esc_html_e('Model (optional)', 'cluster-one-bismarck'); ?></th>
                    <th style="text-align:center;"><?php esc_html_e('My own machine', 'cluster-one-bismarck'); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($connectors as $cid => $conn) :
                    $name  = is_array($conn) ? ((string) ($conn['name'] ?? $cid)) : (string) $cid;
                    $state = cluster_one_connector_has_key((string) $cid, (array) $conn);
                    $cur   = isset($ids[$cid]) ? (int) $ids[$cid] : 0;
                    $curm  = isset($models[$cid]) ? (string) $models[$cid] : '';
                ?>
                    <tr>
                        <td><strong><?php echo esc_html($name); ?></strong><br><code style="opacity:.6;"><?php echo esc_html((string) $cid); ?></code></td>
                        <td><?php echo cluster_one_ai_status_dot((string) $cid, $state); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_ai_status_dot() gibt fertiges HTML zurueck, der Text darin ist esc_html ?></td>
                        <td>
                            <select name="identities[<?php echo esc_attr($cid); ?>]">
                                <option value="">— <?php esc_html_e('none', 'cluster-one-bismarck'); ?> —</option>
                                <?php foreach ($wp_users as $u) : ?>
                                    <option value="<?php echo (int) $u->ID; ?>" <?php selected($cur, (int) $u->ID); ?>><?php echo esc_html($u->display_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="models[<?php echo esc_attr($cid); ?>]" value="<?php echo esc_attr($curm); ?>" placeholder="<?php echo esc_attr__('automatic', 'cluster-one-bismarck'); ?>" style="width:14em;" spellcheck="false">
                        </td>
<?php /* v0.9.47: Die Kostenaussage. Sinnvoll nur dort, wo eine KENNUNG steht —
   ohne WP-Konto kann diese KI ohnehin niemanden sprechen lassen, und ein
   ankreuzbares Feld ohne Wirkung waere ein Versprechen ohne Deckung (0.9.26,
   Alinas Zeile). Ohne Kennung steht deshalb der Grund statt der Box. */ ?>
                        <td style="text-align:center;">
<?php if ($cur > 0) : ?>
                            <input type="checkbox" name="selfhosted[]" value="<?php echo esc_attr($cid); ?>" <?php checked(in_array((string) $cid, $c1_selbst, true)); ?>>
<?php else : ?>
                            <span style="opacity:.45;" title="<?php echo esc_attr__('Give this provider an identity first — without one it cannot speak here at all.', 'cluster-one-bismarck'); ?>">—</span>
<?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p style="opacity:.7;font-size:.92em;margin-top:1em;"><?php echo wp_kses_post(__('Cluster One reads only the registry state and stores no keys. Manage API keys centrally under <em>Settings → Connectors</em>.', 'cluster-one-bismarck')); ?></p>
            <p style="opacity:.7;font-size:.92em;"><?php echo wp_kses_post(__('Model is optional: leave it empty to let the provider choose automatically, or enter an exact model id (e.g. <code>claude-opus-4-8</code>) to pin this AI’s model.', 'cluster-one-bismarck')); ?></p>
            <?php /* v0.9.47: Der Text sagt DREI Dinge, und jedes einzelne verhindert
               eine Luege: dass hier nichts GEPRUEFT wird, dass es um KOSTEN geht
               und nicht um Technik, und dass es HEUTE noch nichts freischaltet.
               Ein Feld ohne Wirkung ist sonst genau das, was wir seit zwei Tagen
               herauswerfen — es darf nur stehen, wenn danebensteht, dass es
               eines ist. */ ?>
            <p style="opacity:.7;font-size:.92em;"><?php echo wp_kses_post(__('<strong>My own machine</strong> is your statement, not a check — nothing here can detect where a model runs. Tick it where calls to that AI cost you nothing extra. It is about <em>cost</em>, not about technology: it is what will let the rule “only the one who pays may call an AI” stand down where nobody pays. <strong>It unlocks nothing yet</strong> — the switch that reads it comes with the next step.', 'cluster-one-bismarck')); ?></p>
            <?php if (cluster_one_is_sysop()) : // v0.0.61 (P0-2): Speichern ist SysOp-Sache — die UI spiegelt den Handler ?>
                <p><?php submit_button(__('Save', 'cluster-one-bismarck'), 'primary', 'submit', false); ?></p>
            <?php endif; ?>
        </form>
    <?php
}


/* v1.9.11 (VERTRAG 25 §4, 08.09.2026) — DIE SEITE ZIEHT MIT IHRER MASKE.
 *
 * Bis hierher wohnten diese beiden Funktionen in kranz.php. Bismarck hat am
 * 08.09. gemessen, warum sie hierher gehoeren, und sein Grund ist haerter als
 * meiner war:
 *
 *     Der Slug cluster_one_bismarck_settings steht FUENFMAL in dieser Datei,
 *     in fuenf wp_safe_redirect() aus fuenf Boden-Handlern:
 *         :1078 ident_saved   :1130 role_saved   :1336 human_saved
 *         :1354 human_deleted :1374 human_saved
 *
 *     Eine Seite, deren Anschrift der Boden fuenfmal aufschreibt, ist eine
 *     Boden-Seite. Das ist kein Geschmacksurteil — das steht im Quelltext.
 *
 * VIER der fuenf Meldungen zeichnet bismarck-seiten.php am Formular mit
 * wp_nonce_field('cluster_one_ai_wish') im
 * Abschnitt 'menschen'. Nur ident_saved gehoert der Maske. Damit kann KEINE
 * der beiden Haelften die Seite verlieren — sie geht dorthin, wo beide sie
 * erreichen, und Bismarck erscheint darauf ueber den Haken wie heute.
 *
 * WARUM DIE FUNKTION ALLEIN NICHT GEREICHT HAT — Bismarcks Satz, und er ist
 * der Grund, warum dieser Block existiert:
 *
 *     Wer die Funktion verschiebt und die Seite vergisst, hat den Ausfall
 *     nicht behoben, sondern unsichtbarer gemacht: die Funktion ist da,
 *     und niemand ruft sie.
 *
 * DER SLUG BLEIBT. Er steht fuenfmal hier und in jedem Lesezeichen. Eine
 * Adresse aendert man nicht, weil ein Name nicht mehr passt.
 *
 * DIE REITERLEISTE IST GERIEGELT, und zwar mit dem Rueckfall "nichts
 * ausgeben" — dieselbe Form wie :2586 (Chronik) und kranz.php:1951
 * (Blaupause). Damit sind es DREI geriegelte Rufe, nicht zwei.
 * Sie ist NICHT ueberfluessig: solange irgendein Paket kranz.php traegt,
 * druckt sie acht Reiter, und sie ist die einzige Tuer zu acht versteckten
 * Seiten (VERTRAG 25 §1). Sie faellt, wenn die letzte davon eine eigene
 * Tuer hat, und keinen Tag frueher.
 *
 * DIE TRENNUNG DARUNTER IST BISMARCKS, WOERTLICH. Er hat sie in drei
 * Austrittswelten byte-identisch gemessen; ich habe daran keine Zeile
 * geaendert, damit seine Messung fuer den ausgelieferten Stand weiter gilt.
 */
function cluster_one_render_bismarck_settings(): void {
    cluster_one_render_ai_identities_form();          /* BODEN  — die Maske */
    do_action('cluster_one_abschnitt', 'menschen');   /* BISMARCK — der Platz */
    /* 19.10.2026 (Neo): der zweite Platz — die Knoten-Einstellungen, die bis
       heute auf der Boden-Seite standen und den Raeumen gehoeren
       (Discord/Matrix, Collective, Erinnerung). Wer sie traegt, zeichnet sie
       hier; wer fehlt, fehlt nur mit seinem Abschnitt. */
    do_action('cluster_one_abschnitt', 'einstellungen');
}

function cluster_one_render_bismarck_settings_page(): void {
    cluster_one_page_gate();
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Kranz'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php
        if (function_exists('cluster_one_render_kranz_tabs')) {
            cluster_one_render_kranz_tabs('cluster_one_bismarck_settings');
        }
        if (function_exists('cluster_one_settings_gespeichert_meldung')) {
            cluster_one_settings_gespeichert_meldung(); // 19.10.2026: der Abschnitt 'einstellungen' speichert ueber options.php
        }
        cluster_one_render_bismarck_settings();
        ?>
    </div>
    <?php
}

/** Fester, für immer unveränderlicher Rollen-Slug. Plugin-Präfix Pflicht
   gegen Namenskollision auf fremden Knoten. Das Label ist editierbar
   (cluster_one_set_excluded_role_label), der Slug NIE. */
function cluster_one_excluded_role_slug(): string {
    return 'cluster_one_excluded_role';
}

/** Registriert die Ausschluss-Rolle, falls sie fehlt. Läuft bei Aktivierung
   UND als Sicherheitsnetz bei 'init' — Datei-Update statt Reaktivierung
   würde eine in einer späteren Version neu eingeführte Rolle sonst nie
   anlegen (gleiches Muster wie der Cron-Sicherheitsnetz weiter unten).
   ANNAHME (offengelegt, leicht revidierbar): 'read' => true, damit ein so
   eingestufter Account sich weiterhin einloggen kann — kein versehentlicher
   Voll-Ausschluss vom eigenen Knoten, sonst keine weiteren Fähigkeiten. */
function cluster_one_ensure_excluded_role(): void {
    $slug = cluster_one_excluded_role_slug();
    if (get_role($slug) === null) {
        add_role($slug, __('Foreign', 'cluster-one-bismarck'), array('read' => true));
    }
}

add_action('init', 'cluster_one_ensure_excluded_role');

add_action('admin_post_cluster_one_excluded_role_save', function () {
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
    check_admin_referer('cluster_one_excluded_role');
    $label = isset($_POST['excluded_role_label']) ? trim(sanitize_text_field(wp_unslash($_POST['excluded_role_label']))) : '';
    if ($label !== '') {
        cluster_one_set_excluded_role_label($label);
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck_settings&role_saved=1'));
    exit;
});

/* --- Collective (v0.0.44, ersetzt den Checkbox-Ansatz aus v0.0.43) -------
   Geteilte Verfasserschaft für normale Blogbeiträge, das redaktionelle
   „Wir" statt Einzel-Byline. Kein Wiki (siehe Reißbrett-Gespräch) —
   WordPress' eigene Revisionen leisten die Nachvollziehbarkeit bereits,
   Sichtbarkeit bleibt bewusst Backend-only.
   Auslöser jetzt Kategorie statt Häkchen: hält den Editor im Grundzustand,
   kein Nonce/Meta-Box-Aufwand. Bewusst nur für den normalen Blog (post_type
   'post') — im Chat (Bismarck/Kleinrock) zählt Echtzeit-Zuschreibung,
   nachträgliche Verschleierung ergäbe dort keinen Sinn. */
function cluster_one_collective_user_id(): int {
    $opts = get_option('cluster_one_settings', array());
    return (int) ($opts['collective_user_id'] ?? 0);
}


/* v0.4.8: DIE BLOG-UMSCHALTUNG IST RAUS.

   Sie tauschte beim Speichern den Autor gegen Collective, sobald eine
   Ausloeser-Kategorie gesetzt war — dazu ein Schutzhaken, der Fremden diese
   Kategorien wieder abnahm, und Editor-Kosmetik, die sie ausblendete.

   Christian: Der Sinn dahinter ist weggefallen. Und der Name log an dieser
   Stelle — ein Beitrag, den EIN Mensch geschrieben hat, bekam den Namen fuer
   Dinge, die keinem gehoeren. Dazu war die Umschaltung einbahnig: Kategorie
   wieder entfernen schaltete den Autor nicht zurueck, ein Fehlgriff kostete
   also die Zuschreibung.

   Die Regel, die bleibt:

       Collective ist ein Name fuer Dinge, die keinem Einzelnen gehoeren —
       als Verfasser im Wiki-Thread und als Autor beim Veroeffentlichen.
       Nicht als Tarnkappe fuer Texte, die sehr wohl einen Autor haben.

   v0.5.0: Auch der Weg vom Forum in den Blog ist jetzt zurueckgebaut —
   cluster_one_collective_categories() entfaellt damit ganz.
   cluster_one_collective_user_id() BLEIBT: Es ist der Verfasser IM
   Wiki-Thread, und dort ist der Name richtig — ein Text, an dem viele
   arbeiten, gehoert keinem Einzelnen. */

/** Gezielte Bearbeitungsrechte für Collective-Beiträge (v0.0.44): jeder,
   der cluster_one_can_access() erfüllt, darf GENAU die Beiträge bearbeiten,
   deren Autor der Collective-Account ist — nicht automatisch auch alle
   anderen fremden Beiträge. Chirurgisch statt pauschal, gleiches Prinzip
   wie die Ausschluss-Rolle: eine WordPress-Fähigkeit gezielt umgangen,
   nicht großzügig verteilt. Ohne das könnte ein Mitarbeiter (WP-Kern-Rolle,
   nur eigene Beiträge editierbar) einen Collective-Beitrag zwar sehen,
   aber nie speichern. */
add_filter('map_meta_cap', function (array $caps, string $cap, int $user_id, array $args) {
    if ($cap !== 'edit_post' || empty($args[0])) {
        return $caps;
    }
    $collective_id = cluster_one_collective_user_id();
    if ($collective_id <= 0) {
        return $caps;
    }
    $post = get_post((int) $args[0]);
    if (!$post || (int) $post->post_author !== $collective_id) {
        return $caps;
    }
    // v0.0.46: prüft jetzt die Weißliste (cluster_one_can_access), nicht mehr
    // nur "keine Ausschluss-Rolle" — unter dem alten Modell war "nicht
    // ausgeschlossen" gleichbedeutend mit "hat Zugriff", das stimmt seit der
    // Weißliste nicht mehr. Ohne diese Korrektur hätte auf einer größeren
    // Installation JEDER nicht explizit ausgeschlossene WP-User Collective-
    // Beiträge bearbeiten können, auch wer mit Cluster One gar nichts zu
    // tun hat.
    if (!cluster_one_can_access($user_id)) {
        return $caps;
    }
    return array('exist'); // praktisch immer erfüllt für einen echten, eingeloggten Menschen
}, 10, 4);

/** Schließt die zweite Hälfte der v0.0.44-Lücke: map_meta_cap oben erlaubt
   das BEARBEITEN eines Collective-Beitrags, aber die „Alle Beiträge"-Liste
   selbst zeigt einem User ohne edit_others_posts trotzdem nur die eigenen —
   WordPress erzwingt das, indem es die Hauptabfrage im Backend automatisch
   auf author=<eigene ID> setzt. Wird genau DIESE automatische Einschränkung
   erkannt (nicht eine bewusste, manuelle Autor-Filterung durch jemanden,
   der eigentlich alles sehen dürfte), erweitern wir sie um den Collective-
   Account — sonst wäre ein Collective-Beitrag zwar bearbeitbar, aber nur
   über einen direkten Link auffindbar, nie durch Herumklicken. */
add_action('pre_get_posts', function (WP_Query $query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    global $pagenow;
    if ($pagenow !== 'edit.php') {
        return;
    }
    $post_type = $query->get('post_type') ?: 'post';
    if ($post_type !== 'post') {
        return;
    }
    $collective_id = cluster_one_collective_user_id();
    if ($collective_id <= 0) {
        return;
    }
    $pto = get_post_type_object($post_type);
    if ($pto && current_user_can($pto->cap->edit_others_posts)) {
        return; // wer eh alles sehen darf: eine gesetzte Autor-Filterung ist dann eine bewusste, eigene Wahl — nicht anfassen
    }
    if (!cluster_one_can_access()) {
        return;
    }
    $author = $query->get('author');
    if ((int) $author !== get_current_user_id()) {
        return; // keine der bekannten "nur eigene" Einschränkungen aktiv
    }
    $query->set('author', '');
    $query->set('author__in', array(get_current_user_id(), $collective_id));
});


/* --- Menschen-Profilfelder (User-Meta): Parität zu externen Personen ---
   WP-Core kennt weder Discord-/Matrix-Handle noch Zeitzone. Eigene
   Meta-Keys mit Plugin-Präfix. Seit v0.0.41 editierbar unter Kranz → Profil,
   Abschnitt „Meine Cluster-One-Angaben" — nicht mehr auf dem nativen WP-
   Profil-Bildschirm (Users → Edit). Das gehörte dort nie wirklich hin: eine
   Cluster-One-Angabe sollte unter Cluster One liegen, nicht unter einem
   WordPress-Bordmittel-Bildschirm. */
/** Zeitzonen-Helfer (v0.0.73): Freitext war eine Falle — für Chabarowsk
   etwa gibt es gar keine eigene IANA-Zone (richtig: Asia/Vladivostok).
   Gültig ist eine IANA-Kennung oder ein UTC-Offset im
   wp_timezone_choice-Format (z. B. UTC+9.5). $old erlaubt Bestandsschutz:
   Ein bereits gespeicherter Alt-Wert darf unverändert wieder gespeichert
   werden, wird aber nie NEU akzeptiert — kein stilles Wegwerfen von
   Bestandsdaten, kein neuer Müll. */
function cluster_one_tz_is_valid(string $tz): bool {
    return in_array($tz, timezone_identifiers_list(), true)
        || (bool) preg_match('/^UTC[+-]\d{1,2}(\.\d{1,2})?$/', $tz);
}

function cluster_one_sanitize_tz(string $tz, string $old = ''): string {
    $tz = trim($tz);
    if ($tz === '' || cluster_one_tz_is_valid($tz)) {
        return $tz;
    }
    return ($old !== '' && $tz === $old) ? $old : '';
}

/** WordPress-eigene Zeitzonen-Auswahl (dasselbe Dropdown wie unter
   Einstellungen → Allgemein), plus Leer-Option und — falls ein ungültiger
   Alt-Wert gespeichert ist — dessen markiertes Bestandsschutz-Angebot. */
function cluster_one_render_tz_select(string $current, string $field_name, string $field_id = ''): void {
    ?>
    <select name="<?php echo esc_attr($field_name); ?>"<?php echo $field_id !== '' ? ' id="' . esc_attr($field_id) . '"' : ''; ?>>
        <option value=""<?php selected($current, ''); ?>>—</option>
        <?php if ($current !== '' && !cluster_one_tz_is_valid($current)) : ?>
            <option value="<?php echo esc_attr($current); ?>" selected><?php echo esc_html($current); ?> (<?php esc_html_e('legacy free-text value', 'cluster-one-bismarck'); ?>)</option>
        <?php endif; ?>
        <?php echo wp_timezone_choice($current, get_user_locale()); ?>
    </select>
    <?php
}

function cluster_one_human_meta_keys(): array {
    return array(
        'discord'     => 'cluster_one_discord',
        'matrix'      => 'cluster_one_matrix',
        'lang'        => 'cluster_one_lang',
        'tz'          => 'cluster_one_tz',
        'about_title' => 'cluster_one_about_title',
        'about_body'  => 'cluster_one_about_body',
    );
}

/* --- Externe Personen (Teil B): strukturiert, ohne WP-Login. Noch kein
   Schreibpfad — die Tabelle bereitet nur die Datenstruktur vor (§4.1). --- */
function cluster_one_get_humans_external(): array {
    $a = get_option('cluster_one_humans_external', array());
    return is_array($a) ? $a : array();
}

function cluster_one_save_humans_external(array $a): void {
    update_option('cluster_one_humans_external', $a, false);
}

add_action('admin_post_cluster_one_human_external_add', function () {
    // v0.0.73: SysOp-exklusiv — die Nicht-WP-Liste ist SysOp-Territorium
    // (Artikel-6-Zusatz: Einwilligungen Externer kann niemand stellvertretend
    // erteilen; der SysOp kuratiert und verantwortet die Einträge). Vorher
    // klaffte hier die klassische Knopf≠Handler-Lücke: das Formular stand
    // allen offen, obwohl das Konzept den SysOp meint.
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_human_external');
    $name = isset($_POST['name']) ? trim(sanitize_text_field(wp_unslash($_POST['name']))) : '';
    if ($name !== '') {
        $id  = 'ext_' . bin2hex(random_bytes(6));
        $all = cluster_one_get_humans_external();
        $all[$id] = array(
            'id'                => $id,
            'name'              => $name,
            'discord'           => isset($_POST['discord']) ? trim(sanitize_text_field(wp_unslash($_POST['discord']))) : '',
            'matrix'            => isset($_POST['matrix'])  ? trim(sanitize_text_field(wp_unslash($_POST['matrix'])))  : '',
            'lang'              => isset($_POST['lang'])    ? trim(sanitize_text_field(wp_unslash($_POST['lang'])))    : '',
            'tz'                => cluster_one_sanitize_tz(isset($_POST['tz']) ? (string) wp_unslash($_POST['tz']) : ''), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (cluster_one_sanitize_tz), den der Pruefer nicht kennt (bisheriger Zeilenkommentar: v0.0.73)
            'node_wide_visible' => false, // v0.0.50: erst sichtbar, wenn SysOp bestätigt, tatsächlich gefragt und ein Ja bekommen zu haben
        );
        cluster_one_save_humans_external($all);
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck_settings&human_saved=1'));
    exit;
});

add_action('admin_post_cluster_one_human_external_delete', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) { // v0.0.73: s. Add-Handler
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_human_external_delete');
    $id  = isset($_POST['human_id']) ? sanitize_text_field(wp_unslash($_POST['human_id'])) : '';
    $all = cluster_one_get_humans_external();
    if ($id !== '' && isset($all[$id])) {
        /* v0.7.3: Nicht mehr nur aus der Liste nehmen — auch die Verweise auf
           diese Person aus Sichtbarkeits- und Teilnehmerlisten raeumen und das
           namenlose Ereignis schreiben. Vorher war das Listenpflege, keine
           Loeschung: die Kennung ext_<id> blieb ueberall stehen. */
        cluster_one_erase_external($id);
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck_settings&human_deleted=1'));
    exit;
});

/** Konsens-Schalter (v0.0.50): knotenweite Sichtbarkeit einer externen
   Person, Standard AUS. Erst wenn SysOp bestätigt, tatsächlich gefragt und
   ein Ja bekommen zu haben, wird Discord/Matrix für alle auf der
   Weißliste sichtbar — vorher zeigt die Menschen-Tabelle nur SysOp selbst
   diese Angaben. */
add_action('admin_post_cluster_one_human_external_visibility', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_human_external_visibility');
    $id  = isset($_POST['human_id']) ? sanitize_text_field(wp_unslash($_POST['human_id'])) : '';
    $all = cluster_one_get_humans_external();
    if ($id !== '' && isset($all[$id])) {
        $all[$id]['node_wide_visible'] = !empty($_POST['node_wide_visible']);
        cluster_one_save_humans_external($all);
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck_settings&human_saved=1'));
    exit;
});

/** SysOp-Erkennung (§4.3). ANNAHME, offengelegt und bewusst NICHT
   „jeder mit manage_options": der Raumzugang selbst bleibt in diesem Sub-
   Projekt komplett unverändert (§7) und hängt weiterhin an manage_options —
   also müssen auch Testmenschen wie MenschA/MenschB diese Fähigkeit
   bekommen, um überhaupt einen Raum zu öffnen. Würde JEDER manage_options-
   Halter als SysOp gelten, wären sie von echtem SysOp nicht mehr
   unterscheidbar und bekämen fälschlich keine Checkbox. Deshalb: unter allen
   aktuellen manage_options-Haltern zählt die NIEDRIGSTE User-ID als Main-
   Admin — auf jedem von Christians Knoten der zuerst angelegte Administrator,
   also der tatsächliche Betreiber. Spätere, zusätzliche Administrator-Accounts
   (z. B. Testuser) bleiben normale, wählbare Teilnehmer. Das spätere selbst-
   erklärte „wer zahlt"-SysOp-Konzept bleibt bewusst dem Totmannschalter-
   Sub-Projekt vorbehalten; diese Funktion ist der einzige Ort, der dafür
   später angefasst werden muss. */
/** v0.0.94: Kanten-Ikonografie — dieselbe Bildsprache wie die Aktions-
   Knöpfe (Dashicons, mit Tooltip: ein Icon ohne Titel wäre eine stumme
   Lampe). chat=Sprechblase, thread=Dokument, blog=Globus (der Schritt
   ins offene Netz), event=Kalender, label=Etikett. */
function cluster_one_edge_icon(string $type, string $title): string {
    $map = array(
        'chat'   => 'format-chat',
        'thread' => 'media-text',
        'blog'   => 'admin-site-alt3',
        'event'  => 'calendar-alt',
        'label'  => 'tag',
    );
    $d = $map[$type] ?? 'admin-links';
    return '<span class="dashicons dashicons-' . esc_attr($d) . '" aria-hidden="true" title="' . esc_attr($title) . '" style="font-size:15px;width:15px;height:15px;vertical-align:text-bottom;opacity:.65;margin-right:2px;"></span>';
}

/** v0.0.95: WINSOR — das Abklingbecken. Archiviert heißt LESBAR, ABER
   ERLOSCHEN: kein neues Wort, kein Trigger, keine Runde; Sichtbarkeiten
   reisen unverändert mit (Wasser schirmt — man darf nah heran, solange
   man nichts aufwirbelt). Der Akt gilt dem PAAR (Chat+Thread wandern
   gemeinsam), ist bezeugt und umkehrbar. Einzige Ausnahme vom
   Schreibverbot: NACHTRÄGE an Labels (Entscheidung SysOp: sonst befüllt
   jemand einen frischen Reaktor mit altem Müll) — mit Warnfrage davor.
   Nach außen: die Labels-Route serviert Archiviertes NICHT (draußen
   weg, drinnen lesbar — der Pull der Freunde räumt ihre Caches). */
function cluster_one_container_is_archived(array $c): bool {
    return (int) ($c['archived'] ?? 0) > 0;
}

/* ===========================================================================
   BODEN 1.2.1 — ZUG 1 VON DREI. DIESE ZWEI FUNKTIONEN ZIEHEN NACH KASPER.PHP.

   H93 (27.08.2026): umgebung.php ruft cluster_one_is_sysop(). Umberto-Light
   traegt vom Boden nur kasper.php, nicht kern.php — allein stehend waere der
   Aufruf toedlich gewesen. Genaeht ist er seit 1.3.5; die eigentliche Antwort
   ist ein Umzug: zwei Zeilen, 133 Aufrufstellen in elf Organdateien, eine
   Frage, die JEDES Haus stellt. Sie gehoert dorthin, wo auch ein Floss sie
   findet.

   WARUM DREI ZUEGE UND NICHT ZWEI — der Befund, der beim Nachrechnen kam:

   Die Hausregel sagt „Zug 1: Riegel, Zug 2: Umzug". Sie ist eine UNTERGRENZE.
   Ein Umzug hat naemlich ZWEI Gefahren, nicht eine, und keine zwei Gefahren
   verschwinden in derselben Auslieferung:

     Gefahr A — DOPPELTE DEKLARATION. Neue kasper.php deklariert, alte kern.php
       deklariert auch. "Cannot redeclare". Erlischt, sobald jede kern.php im
       Umlauf einen eigenen Riegel hat.
     Gefahr B — FEHLENDE DEKLARATION. Neue kern.php deklariert nicht mehr, alte
       kasper.php noch nicht. "Call to undefined function". Erlischt, sobald
       jede kasper.php im Umlauf die Funktionen traegt.

   Zug 1 loescht A. Zug 2 (kasper bekommt sie, kern BEHAELT sie geriegelt)
   loescht nichts Neues, ist aber die einzige Form, in der B nie entsteht.
   Zug 3 (kern verliert sie) darf erst laufen, wenn keine kasper.php ohne sie
   mehr auf einem Knoten liegt.

       Zwei Zuege loeschen eine Gefahr. Bei zwei Gefahren braucht es drei.

   Die Reihenfolge stimmt, weil kasper.php seit 1.0.5 VOR kern.php geladen
   wird — wer zuerst deklariert, gewinnt, und der Zweite schweigt.

   DIESER ZUG AENDERT AM VERHALTEN NICHTS. Der Rumpf beider Funktionen ist
   byte-gleich geblieben (Regel 2); es steht nur ein Riegel darum, der heute
   immer oeffnet. Er wird an dem Tag gebraucht, an dem kasper.php sie hat. */
if (!function_exists('cluster_one_main_admin_user_id')) {
function cluster_one_main_admin_user_id(): int {
    $admins = get_users(array(
        'capability' => array('manage_options'),
        'fields'     => array('ID'),
        'orderby'    => 'ID',
        'order'      => 'ASC',
        'number'     => 1,
    ));
    return !empty($admins) ? (int) $admins[0]->ID : 0;
}
}

/** Ist der aktuell eingeloggte Mensch der SysOp? Kurzform für die vier
   SysOp-exklusiven Raum-Verwaltungs-Handler (v0.0.50) und die neue
   Sichtbarkeits-Logik. */
if (!function_exists('cluster_one_is_sysop')) {
function cluster_one_is_sysop(): bool {
    return get_current_user_id() === cluster_one_main_admin_user_id();
}
}

/** WP-User-IDs, die aktuell einer KI-Kennung zugeordnet sind (§ Härtung
   0.0.38: waren bisher fälschlich zusätzlich in der Menschen-Tabelle
   sichtbar — einmal korrekt als KI, einmal falsch als Mensch). */
function cluster_one_ai_identity_user_ids(): array {
    $out = array();
    foreach (cluster_one_get_ai_identities() as $uid) {
        $uid = (int) $uid;
        if ($uid > 0) {
            $out[] = $uid;
        }
    }
    return array_values(array_unique($out));
}

/** Teil A (§4.1), v0.0.46 auf Weißliste umgestellt: nur noch SysOp +
   wer explizit auf der Weißliste steht (Kranz → Kleinrock) — nicht mehr
   "alle WP-User außer Ausschluss-Rolle". Genau die Umkehrung, die eine
   jahrealte Installation mit vielen bestehenden Usern braucht: die
   Menschen-Tabelle zeigt nur noch, wer wirklich bei Cluster One dabei ist,
   nicht jeden zufälligen WP-Account. */
function cluster_one_get_wp_humans(): array {
    $main_admin_id = cluster_one_main_admin_user_id();
    $ids = array_unique(array_merge(array($main_admin_id), cluster_one_whitelist_user_ids()));
    $ids = array_values(array_filter($ids, static function ($id) {
        return (int) $id > 0;
    }));
    if (empty($ids)) {
        return array();
    }
    $users = get_users(array('include' => $ids));
    $keys  = cluster_one_human_meta_keys();
    $out   = array();
    foreach ($users as $u) {
        $uid        = (int) $u->ID;
        $roles      = is_array($u->roles) ? $u->roles : array();
        $role_label = !empty($roles) ? cluster_one_wp_role_label($roles[0]) : '';
        $out['wp_' . $uid] = array(
            'id'            => 'wp_' . $uid,
            'type'          => 'wp',
            'user_id'       => $uid,
            'name'          => $u->display_name,
            'wp_role'       => $role_label,
            'forum_role'    => cluster_one_bbpress_role_label($uid),
            'discord'       => (string) get_user_meta($uid, $keys['discord'], true),
            'matrix'        => (string) get_user_meta($uid, $keys['matrix'], true),
            'lang'          => (string) get_user_meta($uid, $keys['lang'], true),
            'tz'            => (string) get_user_meta($uid, $keys['tz'], true),
            'is_main_admin' => ($uid === $main_admin_id),
        );
    }
    return $out;
}

/** Teil B im selben Format wie Teil A. */
/* v0.9.31: Hier stand cluster_one_get_humans_external_normalized() — die
   zweite Haelfte eines Paares, dessen erste Haelfte nie gebaut wurde. Sie hat
   externe Menschen in dieselbe Form gebracht wie WP-Menschen, und niemand hat
   die Form je abgeholt: kein Aufruf im ganzen Plugin, kein Zeuge. Wer die
   vereinheitlichte Sicht spaeter doch braucht, findet sie im CHANGELOG. */
/** Vereinheitlichter Blick (§4: „ein Datenmodell"), von Raum-Konfiguration
   und Menschen-Tabelle gleichermaßen genutzt. */
function cluster_one_get_all_humans(): array {
    /* v0.9.19: NACH NAMEN, NICHT NACH HERKUNFT — Christians Frage, warum
       Alina ganz unten steht.

       Bis hierher war es eine blosse Aneinanderreihung: erst alle
       WP-Konten, dann alle Externen. Wer kein WordPress hat, stand
       zwangslaeufig hinten — und eine Liste ordnet nicht nur, sie behauptet
       auch etwas. In einem Entwurf, dessen Kern die Gleichrangigkeit ist, ist
       das die falsche Aussage.

       Jetzt alphabetisch ueber alle. Die Spalte "Typ" sagt weiterhin, wer
       woher kommt — als AUSKUNFT, nicht als Rangfolge. */
    /* v0.9.26: OHNE BRUECKE KEINE NICHT-WP-TEILNEHMER — Christians Befund.

       Alina stand als ankreuzbare Zeile in der Teilnehmertabelle, obwohl es
       keinen Weg gibt, auf dem sie ankommt: Die Bruecke ist in derselben
       Version entfallen, weil sie nie verdrahtet war. EINE ANKREUZBARE ZEILE,
       DIE NICHTS BEWIRKEN KANN, IST EIN VERSPRECHEN OHNE DECKUNG.

       Die Maske, in der er ihre Daten eintraegt, bleibt — sie ist sein
       Notizzettel. Kommt die Bruecke, kommen die Zeilen zurueck. */
    $alle = cluster_one_get_wp_humans();
    uasort($alle, function ($a, $b) {
        return strnatcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
    });
    return $alle;
}

/** Sieht der aktuell eingeloggte Mensch dieses Thema/diesen Raum? Generisch
   für beide, weil beide dasselbe Feldpaar tragen. */
/** Die Vier-Stufen-Leiter (v0.0.79, Design SysOp 16.07.2026): zwei Regler
   pro Container, für Threads und Chats IDENTISCH.
   · outside_view: was die Außenwelt (nicht im Kreis) sieht —
     'invisible' (Bestandsverhalten) oder 'half' (Halbauge: Existenz +
     Titel, Schloss, nicht betretbar; verallgemeinert das Tagebuch-Muster
     aus 0.0.72).
   · circle_depth: wie tief der KREIS darf — 'full' (Bestandsverhalten:
     schreiben + KIs auslösen) oder 'read' (alles lesen, nichts schreiben,
     keine KI feuern).
   Der KI-TRIGGER hängt an 'full' — damit ist die Artikel-9-Flanke
   geschlossen: Sehen schenkt nie Handeln, Handeln setzt Sehen voraus,
   und die oberste Stufe vergibt der Reaktorfahrer bewusst pro Container.
   Der SysOp selbst steht NEBEN der Leiter (sieht alles, beim Tagebuch
   bezeugt); die Namensliste bestimmt weiterhin WER im Kreis ist — die
   Gästeliste, die Leiter ist die Hausordnung. Defaults = Bestand:
   bestehende Container verhalten sich unverändert, keine Migration. */


/* v0.9.24: DIE AUSSENANSICHT IST KEINE WAHL MEHR — Christians Entscheidung.

   Die Verfassungsregel vom 6. August lautet: VORHANDENSEIN IST OEFFENTLICH,
   INHALT NICHT — Sichtbarkeit und Zutritt sind zwei verschiedene Dinge.
   'invisible' widersprach ihr an jeder Stelle ausser einer.

   Sein Argument fuer die Tagebuecher: "Es soll jeder wissen, dass ich fuer jede
   KI ein Tagebuch angelegt habe, nur rein kommt niemand." Und mein Einwand
   (eine Silhouette verrate Beziehungen) faellt weg, weil das TERMINAL nichts
   verraet, was das Tagebuch nicht schon verraet — sein Titel stammt daher.

   Ausserdem hat niemand ausser dem SysOp einen Grund hineinzuwollen: Mit einer
   KI reden kostet Tokens, und die zahlt er. Derselbe Grund, aus dem es bei PNs
   keine KIs gibt.

   Was bleibt, ist die EINE Ausnahme: der SysOp-Bereich. Dort verbirgt die
   Bereichsregel ohnehin alles (cluster_one_area_allows_view), also braucht es
   dafuer kein Feld. */
function cluster_one_container_outside_view(array $c): string {
    return 'half';
}



/** Darf der aktuelle Mensch in diesem Container handeln (schreiben /
   KIs auslösen)? SysOp immer; sonst: im Kreis UND Tiefe 'full'. */
function cluster_one_container_can_act(array $c): bool {
    if (cluster_one_is_sysop()) {
        return true;
    }
    /* v0.9.26: DIE KREIS-TIEFE IST WEG — Christians Befund.

       Ihr Etikett log seit 0.9.13: "voll — schreiben und KIs ausloesen"
       beschrieb die Lage, bevor der Trigger an den Zahlenden ging. Und der
       einzige echte Fall fuer "nur lesend" ist ein Mensch, der gerade stoert —
       das ist ein EINGRIFF IN EINEM MOMENT, kein Zustand, den man einstellt.
       Ein Regler in der Konfiguration macht daraus einen Dauerzustand, den
       niemand mehr erklaert.

       Wer im Kreis ist, schreibt. Das Stummschalten kommt in den Chat, neben
       die Person, und wird bezeugt. */
    /* v0.9.27: Das Stummschalten greift hier — an der einen Stelle, an der
       ohnehin entschieden wird, ob jemand handeln darf. Ein zweiter Pruefort
       waere ein zweiter Ort, an dem man es vergessen kann. */
    if (cluster_one_is_muted($c, cluster_one_actor_id_human())) {
        return false;
    }
    return cluster_one_group_visibility_can_view($c);
}

function cluster_one_group_visibility_can_view(array $item): bool {
    /* v0.8.5: Der SysOp-Bereich zuerst — er schlaegt alles andere. Hier, an der
       EINEN Stelle, an der ohnehin jede Sichtbarkeit entschieden wird: Ein
       zweiter Pruefort waere ein zweiter Ort, an dem man ihn vergessen kann.

       Achtung, die Reihenfolge ist wichtig: Erst der Bereich, dann die
       Namensliste. Ein Thema im SysOp-Bereich, das jemanden auf seiner Liste
       hat, bliebe sonst fuer den sichtbar. */
    if (!cluster_one_area_allows_view($item)) {
        return false;
    }
    if (($item['group_visibility'] ?? 'all') !== 'named') {
        return true;
    }
    $my_id = 'wp_' . get_current_user_id();
    return in_array($my_id, (array) ($item['visible_to'] ?? array()), true);
}


/** Gemeinsame Auswahl-Oberfläche "Sichtbar für" — Radio (alle/bestimmte)
   plus Checkbox-Liste (Menschen der Weißliste + benannte KI-Kennungen).
   Wiederverwendet für Platon-Themen UND Bismarck-Räume, ein Formular-
   Baustein statt zwei Kopien. $name_prefix erlaubt unterschiedliche
   Feldnamen, falls je nötig (aktuell überall "group_visibility"/"visible_to"). */
function cluster_one_render_visibility_picker(array $selected = array(), string $current_group_visibility = 'all'): void {
    $humans = cluster_one_get_wp_humans(); // nur WP-Menschen — externe können sich eh nicht einloggen, um je etwas zu sehen
    $ais    = array();
    foreach (cluster_one_get_ai_identities() as $cid => $uid) {
        if ((int) $uid > 0) {
            $u = get_userdata((int) $uid);
            if ($u) {
                $ais[(int) $uid] = $u->display_name;
            }
        }
    }
    ?>
    <p>
        <label style="margin-right:1.2em;"><input type="radio" name="group_visibility" value="all" <?php checked($current_group_visibility, 'all'); ?> class="c1-gv-radio"> <?php esc_html_e('Everyone in Cluster One', 'cluster-one-bismarck'); ?></label>
        <label><input type="radio" name="group_visibility" value="named" <?php checked($current_group_visibility, 'named'); ?> class="c1-gv-radio"> <?php esc_html_e('Only these entities', 'cluster-one-bismarck'); ?></label>
    </p>
    <div class="c1-gv-named" style="<?php echo $current_group_visibility === 'named' ? '' : 'display:none;'; ?>border:1px solid #dcdcde;border-radius:4px;padding:.7em 1em;max-width:480px;">
        <?php foreach ($humans as $h) :
            $hid = (string) ($h['id'] ?? '');
        ?>
            <label style="display:block;margin:.2em 0;"><input type="checkbox" name="visible_to[]" value="<?php echo esc_attr($hid); ?>" <?php checked(in_array($hid, $selected, true)); ?>> <?php echo esc_html((string) $h['name']); ?><?php echo !empty($h['is_main_admin']) ? ' · SysOp' : ''; ?></label>
        <?php endforeach; ?>
        <?php if (!empty($ais)) : ?>
            <p style="margin:.5em 0 .2em;opacity:.7;font-size:.9em;"><?php esc_html_e('AI identities', 'cluster-one-bismarck'); ?></p>
            <?php foreach ($ais as $uid => $name) :
                $hid = 'wp_' . $uid;
            ?>
                <label style="display:block;margin:.2em 0;"><input type="checkbox" name="visible_to[]" value="<?php echo esc_attr($hid); ?>" <?php checked(in_array($hid, $selected, true)); ?>> <?php echo esc_html($name); ?></label>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php /* v0.9.7: DER SATZ SPRACH NUR VON EINEM VON ZWEI ORTEN.

       Christian hat ihn in der RAUM-Konfiguration gesehen — er sagte dort "ein
       Thema mit zwei oder mehr Menschen". Dieser Waehler wird von beiden Seiten
       benutzt, Raum wie Thema, und der Text beschrieb nur eine davon.

       Er erklaert nebenbei meinen Irrtum vom selben Abend: Ich hatte in der
       Datei nachgesehen, "Thema" gelesen und geschlossen, der Satz betreffe
       nur Themen. Christian hat ihn im Raum gesehen. Seine Sicht war die
       richtige — EIN GETEILTER TEXT MUSS BEIDE ORTE MEINEN. */ ?>
    <p class="description" style="max-width:600px;"><?php esc_html_e('AI participation follows automatically from who is on this list — where two or more different humans are named, no AI takes part, no matter what is ticked above.', 'cluster-one-bismarck'); ?>
    <script>
    (function () {
        document.querySelectorAll('.c1-gv-radio').forEach(function (r) {
            r.addEventListener('change', function () {
                var box = document.querySelector('.c1-gv-named');
                if (box) { box.style.display = (document.querySelector('.c1-gv-radio[value="named"]').checked) ? '' : 'none'; }
            });
        });
    })();
    </script>
    <?php
}

/** Kann diese Person unter der gegebenen Raum-Brücke erreicht werden? (§5.2)
   WP-User: immer (das Backend ist ihr Zugang, eine Brücke ist für sie nur
   ein Bonus-Kanal, kein Ersatz). Nicht-WP: Matrix-Raum nur mit Matrix-
   Handle; Discord-Raum mit Discord- ODER Matrix-Handle (Matrix↔Discord-
   Brücke macht Matrix-Leute technisch erreichbar); WP-only: keiner. */
function cluster_one_human_reachable_via(array $h, string $bridge_type): bool {
    if (($h['type'] ?? '') === 'wp') {
        return true;
    }
    $has_discord = (string) ($h['discord'] ?? '') !== '';
    $has_matrix  = (string) ($h['matrix'] ?? '')  !== '';
    if ($bridge_type === 'matrix') {
        return $has_matrix;
    }
    if ($bridge_type === 'discord') {
        return $has_discord || $has_matrix;
    }
    return false; // WP-only: kein Erreichungsweg für Nicht-WP-Personen in diesem Raum
}

/** System-Nachricht (v0.0.42, System Teil 1): bezeugt, urteilt nicht. Autor
   immer schlicht „System" — bewusst ohne Sonderformatierung, wie besprochen.
   'system' => 1 als Spur für später (Endverdrahtung könnte optisch trennen
   wollen), aber keine Anzeige-Änderung heute. */
function cluster_one_system_message(string $text): array {
    return array(
        'id'       => cluster_one_msg_id(),
        'author'   => 'System',
        'actor_id' => 'system', // v0.0.62
        'body'     => $text,
        'ts'       => time(),
        'system'   => 1,
    );
}

add_filter('map_meta_cap', function (array $caps, string $cap, int $user_id) {
    if ($cap !== 'cluster_one_kranz_save') {
        return $caps;
    }
    return $user_id === cluster_one_main_admin_user_id() ? array('exist') : array('do_not_allow');
}, 10, 3);

function cluster_one_connector_has_key(string $id, array $conn): array {
    $method = $conn['authentication']['method'] ?? '';
    if ($method === 'none')    return ['configured' => true,  'source' => __('no auth required', 'cluster-one-bismarck')];
    if ($method !== 'api_key') return ['configured' => false, 'source' => __('unknown auth method', 'cluster-one-bismarck')];

    $const_name = strtoupper($id) . '_API_KEY';

    $env = getenv($const_name);
    if (is_string($env) && $env !== '') return ['configured' => true, 'source' => __('Environment variable', 'cluster-one-bismarck')];

    if (defined($const_name) && constant($const_name) !== '') return ['configured' => true, 'source' => __('PHP constant', 'cluster-one-bismarck')];

    $setting = $conn['authentication']['setting_name'] ?? ('connectors_ai_' . $id . '_api_key');
    $db_val  = get_option($setting, '');

    /* v1.3.0 — EIN PLATZHALTER IST KEIN SCHLUESSEL. Befund von Pacioli, 28.08.2026.
     *
     * Bis hierher galt: steht in der Options-Zeile irgendetwas, gibt es einen
     * Schluessel. Das war wahr, solange dort nur Schluessel standen. Connector
     * Vault legt dort eine wertlose, lesbare Marke ab und tauscht sie erst
     * unmittelbar vor dem Absenden. Damit ist die Zeile auf einem Knoten mit
     * Pacioli IMMER nicht leer — und die Funktion meldete fuer JEDEN Anbieter
     * 'konfiguriert', auch fuer einen, fuer den auf dem ganzen Knoten niemand
     * einen Schluessel hat.
     *
     *     Sie ist nicht kaputtgegangen. Ihr Messwert ist umgezogen, und sie
     *     fragte noch an der alten Stelle.
     *
     * Der Satz, der groesser ist als die Reparatur, ist Paciolis:
     *
     *     Unter Pacioli ist "hat einen Schluessel" keine Eigenschaft eines
     *     ANBIETERS mehr, sondern eine des Paares (Identitaet, Anbieter).
     *
     * Die knotenweite Frage hat also keine Antwort mehr. Was diese Funktion
     * kann, ist die falsche Antwort zu unterlassen: 'configured' wird false,
     * und 'per_identity' sagt, WARUM es false ist — damit eine Anzeige
     * "kein Schluessel" von "je Mensch ein eigener" unterscheiden kann, ohne
     * eine zweite Quelle zu befragen. Wer nur 'configured' liest, verhaelt
     * sich wie vorher. */
    if (cluster_one_ist_fremde_marke($db_val)) {
        return [
            'configured'   => false,
            'per_identity' => true,
            'source'       => __('key vault active — each identity brings its own key', 'cluster-one-bismarck'),
        ];
    }

    if (is_string($db_val) && $db_val !== '') return ['configured' => true, 'source' => __('Database (Connectors screen)', 'cluster-one-bismarck')];

    return ['configured' => false, 'source' => __('no key yet', 'cluster-one-bismarck')];
}

/** v1.3.0 — Steht in dieser Options-Zeile ein Platzhalter statt eines Schluessels?
 *
 * ZWEI QUELLEN, UND DIE REIHENFOLGE IST DIE GANZE ENTSCHEIDUNG.
 *
 * 1. Liegt Pacioli daneben, antwortet Pacioli. cv_is_marker() kennt das eigene
 *    Format; danach zu fragen ist besser, als es zu kennen.
 *
 * 2. Liegt Pacioli NICHT daneben, muss der Boden es trotzdem erkennen — und
 *    das ist kein Randfall, sondern ausdruecklich gebaut: Connector Vault
 *    laesst die Marken bei der Deaktivierung STEHEN. Sein eigener Kommentar
 *    nennt den Grund, und er ist gut: ein zurueckgelassener echter Schluessel
 *    liefe still weiter auf die Rechnung des Betreibers, eine Marke scheitert
 *    mit 401.
 *
 *        Ein Knoten OHNE Pacioli, auf dem Pacioli einmal lief, ist genau der
 *        Knoten, auf dem die alte Auskunft am laengsten falsch bleibt.
 *
 * BENANNTE GRENZE — und sie ist der Preis dieser Reparatur: Fall 2 kennt das
 * Format einer fremden Datei. Das ist eine zweite Quelle auf eine Wahrheit
 * (P1-12), und sie altert, wenn Pacioli das Format aendert. Sie steht hier
 * trotzdem, weil die Alternative schlechter ist: ohne sie ist die Auskunft auf
 * einem verlassenen Knoten dauerhaft falsch, und zwar still.
 *
 * Was den Preis begrenzt: das Praefix ist ein VERTRAG zwischen zwei Haeusern
 * und steht als solcher im Brett. Es steht hier an genau EINER Stelle, nicht
 * an dreien, und Fall 1 hat Vorrang — solange Pacioli da ist, wird das
 * Praefix nie gelesen. */
function cluster_one_ist_fremde_marke($wert): bool {
    if (!is_string($wert) || $wert === '') {
        return false;
    }
    if (function_exists('pacioli_is_marker')) {
        return (bool) pacioli_is_marker($wert);
    }
    return strpos($wert, 'CONNECTOR-VAULT--') === 0;
}

/** Zufällige Nachrichten-ID (für Dedup bei at-least-once-Zustellung). */
function cluster_one_msg_id(): string {
    return bin2hex(random_bytes(8));
}

/** Härtet Nachrichtentext: NIE HTML, getrimmt, längenbegrenzt. */
function cluster_one_sanitize_message(string $body): string {
    $body = wp_strip_all_tags($body);
    $body = sanitize_textarea_field($body);
    return function_exists('mb_substr') ? mb_substr($body, 0, 2000) : substr($body, 0, 2000);
}



/* --- v0.6.3: DAS RECHT AUF LÖSCHUNG ------------------------------------------

   Christians Anlass: Als SysOp muss er eine Person auf Wunsch entfernen
   koennen. In Foren steht danach seit zwanzig Jahren "Unregistrierter Nutzer".

   DER KONFLIKT, DER KEINER IST. Artikel 2 sagt: kein Zeugnis faellt still aus.
   Das Recht auf Loeschung sagt: die Person verschwindet. Das klingt
   unvereinbar, ist es aber nicht — beide meinen etwas anderes.

     Geschuetzt ist die STRUKTUR: dass an diesem Tag jemand geschrieben,
     geaendert, entfernt hat.
     Geschuldet ist die IDENTITAET: wer das war.

   Also ANONYMISIEREN STATT LOESCHEN. Der Beitrag bleibt, der Name wird zu
   "Unregistrierter Nutzer", die Kette reisst nicht.

   Die Actor-ID wird zu 'gone' — OHNE Unterscheidung zwischen mehreren
   entfernten Personen. Ein 'gone:7' waere weiterhin ein Zeiger auf einen
   Menschen, nur ein hoeflicherer. Preis: Man sieht nicht mehr, dass drei
   Beitraege von derselben Person stammten. Das ist kein Verlust, sondern
   genau der Zweck.

   UND DAS EREIGNIS NENNT DIE PERSON NICHT. Ein Loeschprotokoll, das den Namen
   dessen enthaelt, der geloescht werden wollte, hebt die Loeschung auf. Es
   haelt fest, DASS geraeumt wurde, wie viel, und wann — mehr darf es nicht.

   NICHT ANGETASTET: die Kanten (sie gehoeren ZWEI Knoten) und fremde Knoten
   (was dorthin gereist ist, ist dort zu loeschen, nicht hier — ein Knoten, der
   behauptet, anderswo aufraeumen zu koennen, luegt). */

function cluster_one_erased_label(): string {
    return __('Unregistered user', 'cluster-one-bismarck');
}

/** Ersetzt Name und Actor-ID in EINEM Datensatz, wenn er der gesuchten Person
   gehoert. Gibt true zurueck, wenn etwas geaendert wurde. */
function cluster_one_erase_in_record(array &$rec, string $actor): bool {
    $hit = false;
    foreach (array('actor_id', 'creator_actor', 'wilde_owner', 'owner') as $f) {
        if (isset($rec[$f]) && (string) $rec[$f] === $actor) {
            $rec[$f] = 'gone';
            $hit = true;
        }
    }
    if ($hit && isset($rec['author'])) {
        $rec['author'] = cluster_one_erased_label();
    }
    /* Die fortgeschriebenen Wiki-Felder aus 0.5.1 tragen NAMEN, keine
       Actor-IDs — sie muessen ueber den Namen gefunden werden, sonst bliebe
       "zuletzt geaendert von X" stehen, nachdem X gegangen ist. */
    return $hit;
}

/** Die Person aus allen lokalen Speichern entfernen. Gibt zurueck, wie viele
   Datensaetze beruehrt wurden — die Zahl fuer das Ereignis. */
function cluster_one_erase_person(string $actor, string $name = ''): int {
    if ($actor === '' || $actor === 'gone') {
        return 0;
    }
    $n   = 0;
    $tot = function (bool $b) use (&$n) { if ($b) { $n++; } return $b; };

    // --- Raeume und ihre Nachrichten
    $rooms = cluster_one_get_rooms();
    $rdirty = false;
    foreach ($rooms as $rid => $r) {
        if ($tot(cluster_one_erase_in_record($r, $actor))) { $rdirty = true; }
        foreach (array('participants', 'visible_to') as $liste) {
            if (isset($r[$liste]) && is_array($r[$liste]) && in_array($actor, $r[$liste], true)) {
                $r[$liste] = array_values(array_diff($r[$liste], array($actor)));
                $rdirty = true;
                $n++;
            }
        }
        $rooms[$rid] = $r;
        $msgs = cluster_one_get_room_messages((string) $rid);
        $mdirty = false;
        foreach ($msgs as $i => $m) {
            if ($tot(cluster_one_erase_in_record($m, $actor))) { $msgs[$i] = $m; $mdirty = true; }
        }
        if ($mdirty) { cluster_one_save_room_messages((string) $rid, $msgs); }
    }
    if ($rdirty) { cluster_one_save_rooms($rooms); }

    // --- Themen und Beitraege
    $threads = cluster_one_get_forum_threads();
    $tdirty  = false;
    foreach ($threads as $tid => $t) {
        if ($tot(cluster_one_erase_in_record($t, $actor))) { $threads[$tid] = $t; $tdirty = true; }
        if (isset($t['visible_to']) && is_array($t['visible_to']) && in_array($actor, $t['visible_to'], true)) {
            $threads[$tid]['visible_to'] = array_values(array_diff($t['visible_to'], array($actor)));
            $tdirty = true;
            $n++;
        }
        $posts  = cluster_one_get_forum_posts((string) $tid);
        $pdirty = false;
        foreach ($posts as $i => $p) {
            if ($tot(cluster_one_erase_in_record($p, $actor))) { $posts[$i] = $p; $pdirty = true; }
            /* v0.5.1-Felder tragen Namen statt Actor-IDs. */
            if ($name !== '') {
                foreach (array('wiki_by', 'wiki_first') as $f) {
                    if (isset($posts[$i][$f]) && (string) $posts[$i][$f] === $name) {
                        $posts[$i][$f] = cluster_one_erased_label();
                        $pdirty = true;
                        $n++;
                    }
                }
            }
        }
        if ($pdirty) { cluster_one_save_forum_posts((string) $tid, $posts); }
    }
    if ($tdirty) { cluster_one_save_forum_threads($threads); }

    // --- Weisse Liste und externe Menschen: hier steht die Identitaet selbst,
    //     also wird sie ENTFERNT statt ersetzt.
    $opts = get_option('cluster_one_settings', array());
    $wl   = (array) ($opts['whitelist'] ?? array());
    $uid  = strpos($actor, 'wp:') === 0 ? (int) substr($actor, 3) : 0;
    if ($uid > 0 && in_array($uid, array_map('intval', $wl), true)) {
        $opts['whitelist'] = array_values(array_diff(array_map('intval', $wl), array($uid)));
        update_option('cluster_one_settings', $opts, false);
        $n++;
    }
    if ($name !== '') {
        $ext   = cluster_one_get_humans_external();
        $vor   = count($ext);
        $ext   = array_values(array_filter($ext, function ($h) use ($name) {
            return (string) (is_array($h) ? ($h['name'] ?? '') : $h) !== $name;
        }));
        if (count($ext) !== $vor) {
            update_option('cluster_one_humans_external', $ext, false);
            $n += ($vor - count($ext));
        }
    }

    /* Das Ereignis nennt die Person NICHT — sonst haette die Loeschung ein
       Denkmal mit Namen. Es haelt fest, DASS und WIE VIEL. */
    /* cluster_one_event_emit(), nicht do_action() — der erste Anlauf feuerte
       einen Hook, den niemand hoert. Ein Zeugnis, das an einer Attrappe klopft,
       ist keins. Mit $persist=true, weil eine Loeschung strukturell ist. */
    cluster_one_event_emit(array(
        'type'          => 'person_erased',
        'actor_id'      => 'gone',
        'summary_count' => $n,
    ), true);
    return $n;
}

/* WordPress hat seit 4.9.6 eigene Werkzeuge dafuer: Werkzeuge → Personenbezogene
   Daten loeschen. Plugins melden sich ueber diesen Filter an und bekommen eine
   E-Mail-Adresse. Cluster One hing bis 0.6.2 an keinem davon — die Daten waren
   fuer das Werkzeug unsichtbar, und der SysOp haette eine Loeschung bestaetigt,
   die nur zur Haelfte stattfand. */
add_filter('wp_privacy_personal_data_erasers', function (array $erasers) {
    $erasers['cluster-one'] = array(
        'eraser_friendly_name' => __('Cluster One', 'cluster-one-bismarck'),
        'callback'             => 'cluster_one_privacy_eraser',
    );
    return $erasers;
});

function cluster_one_privacy_eraser(string $email, int $page = 1): array {
    $leer = array('items_removed' => false, 'items_retained' => false, 'messages' => array(), 'done' => true);
    $u = get_user_by('email', $email);
    if (!$u) {
        return $leer; // Kein Konto hier: nichts zu tun, nichts zu behaupten.
    }
    $n = cluster_one_erase_person('wp:' . (int) $u->ID, (string) $u->display_name);
    if ($n === 0) {
        return $leer;
    }
    $leer['items_removed'] = true;
    /* AUSDRUECKLICH GESAGT, was NICHT geschah: Die Beitraege bleiben stehen,
       nur ohne Namen. Wer das nicht weiss, haelt die Loeschung fuer
       vollstaendiger, als sie ist. */
    $leer['messages'][] = sprintf(
        /* translators: %d: number of records */
        __('Cluster One: %d records were anonymised. The entries themselves remain — the node keeps a record that something was written, not who wrote it. Anything that travelled to other nodes has to be erased there.', 'cluster-one-bismarck'),
        $n
    );
    return $leer;
}

/* --- v0.6.4: WAS EIN BEREICH AUSSER SEINEM NAMEN NOCH TRAEGT -----------------

   Bereiche sind seit jeher eine flache Liste von NAMEN in
   $opts['forum_areas'] — Themen und Raeume merken sich diesen Namen. Deshalb
   wird die Form NICHT umgebaut: Eine Umstellung auf Verbundstruktur haette auf
   vier laufenden Knoten jeden Beitrag von seinem Bereich getrennt.

   Stattdessen ein zweiter Speicher daneben, gekeyt auf den Namen — dasselbe
   Muster, das forum_wiki_areas schon benutzt. Alte Bereiche funktionieren
   unveraendert weiter; wer keinen Eintrag hat, hat eben keine Beschreibung.

   Drei Dinge stehen darin:
     desc      — die Beschreibung, vom SysOp aenderbar
     origin    — 'blueprint' oder '' : WOHER der Bereich kommt. Wird BEI DER
                 ANLAGE vermerkt, nie ueber Namensvergleich erschlossen — sonst
                 verloere ein umbenannter Blaupausen-Bereich seine Herkunft und
                 ein gleichnamig getaufter Eigenbau bekaeme eine.
     published — sichtbar fuer die Beschirmten. Ab Werk AUS bei eigenen
                 Bereichen (man baut in Ruhe auf, statt halbe Sachen zu zeigen),
                 ab Werk AN bei der Blaupause, die per Bauart vollstaendig ist. */
function cluster_one_area_meta_all(): array {
    $opts = get_option('cluster_one_settings', array());
    $m    = (array) ($opts['forum_area_meta'] ?? array());
    return $m;
}

function cluster_one_area_meta(string $name): array {
    $m = cluster_one_area_meta_all();
    $e = (array) ($m[$name] ?? array());
    return array(
        'desc'      => (string) ($e['desc'] ?? ''),
        'origin'    => (string) ($e['origin'] ?? ''),
        /* Bereiche OHNE Eintrag stammen aus der Zeit vor 0.6.4 und sind seit
           jeher sichtbar gewesen — sie duerfen nicht ueber Nacht verschwinden.
           Deshalb ist "kein Eintrag" gleichbedeutend mit "veroeffentlicht". */
        'published' => array_key_exists('published', $e) ? (bool) $e['published'] : true,
    );
}

function cluster_one_area_meta_set(string $name, array $werte): void {
    $opts = get_option('cluster_one_settings', array());
    $m    = (array) ($opts['forum_area_meta'] ?? array());
    $alt  = (array) ($m[$name] ?? array());
    $m[$name] = array_merge($alt, $werte);
    $opts['forum_area_meta'] = $m;
    update_option('cluster_one_settings', $opts, false);
}

/** Beim Umbenennen wandert alles mit — sonst verloere ein Bereich beim
   Umbenennen Beschreibung UND Herkunft, und der Stern waere eine Luege. */
function cluster_one_area_meta_rename(string $alt, string $neu): void {
    $opts = get_option('cluster_one_settings', array());
    $m    = (array) ($opts['forum_area_meta'] ?? array());
    if (!isset($m[$alt])) {
        return;
    }
    $m[$neu] = $m[$alt];
    unset($m[$alt]);
    $opts['forum_area_meta'] = $m;
    update_option('cluster_one_settings', $opts, false);
}

function cluster_one_area_meta_forget(string $name): void {
    $opts = get_option('cluster_one_settings', array());
    $m    = (array) ($opts['forum_area_meta'] ?? array());
    if (!isset($m[$name])) {
        return;
    }
    unset($m[$name]);
    $opts['forum_area_meta'] = $m;
    update_option('cluster_one_settings', $opts, false);
}

/** Die Bereiche, die DIESE Person sehen darf. Der SysOp sieht alle — er baut
   sie ja auf; die Beschirmten sehen nur veroeffentlichte. */
/* v0.8.5: DER SYSOP-BEREICH.

   Christians Entscheidung: Ein Bereich, dessen Inhalte — und der Bereich selbst
   — fuer alle ausser dem SysOp unsichtbar sind.

   Das ist eine REGEL, keine Vorauswahl, und damit die Ausnahme von dem, was in
   0.7.8 fuer die Thread-Art galt. Der Unterschied: Die ART wird bei der Anlage
   eingefroren und darf nicht durch ein Verschieben kippen. Die SICHTBARKEIT
   dagegen ist ohnehin aenderbar — sie darf nur nicht STILL kippen.

   Deshalb ist das Verschieben in diesen Bereich und heraus ein bezeugter Akt
   (siehe cluster_one_area_move_witness). Christians eigener Satz aus der
   Themenkonfiguration bleibt damit wahr: "Eine Sichtbarkeits-Aenderung ist ein
   Tuerereignis und wird bezeugt — kein stiller Wechsel, in keine Richtung." */
function cluster_one_sysop_area(): string {
    return 'SysOp';
}

/** Liegt dieser Container im SysOp-Bereich? */
function cluster_one_in_sysop_area($container): bool {
    $a = is_array($container) ? (string) ($container['area'] ?? '') : (string) $container;
    return $a !== '' && $a === cluster_one_sysop_area();
}

/** Darf diese Person den Container sehen, soweit es am Bereich liegt?
   Nur der SysOp; fuer alle anderen existiert er nicht. */
function cluster_one_area_allows_view($container): bool {
    if (!cluster_one_in_sysop_area($container) || cluster_one_is_sysop()) {
        return true;
    }
    /* v0.9.16: WER BENANNT IST, KOMMT HINEIN — Christians Befund, dass er
       seinen Testbereich ohne Testkonten nicht testen kann.

       Sein Vorschlag war eine eigene Rolle "Testaccount". Das waere ein
       ZWEITES Berechtigungssystem neben dem, das es schon gibt. Das Haus hat
       den Mechanismus laengst: benennen. Ueberall sonst gilt "nur diese
       Entitaeten" — genau dafuer ist die Liste da.

       Der Bereich verbirgt also weiter AB WERK, aber er ist keine absolute
       Sperre mehr: Wer auf der Gaesteliste eines Containers steht, kommt an
       ihn heran. Der SysOp entscheidet pro Raum statt pauschal fuer eine
       Personengruppe — und der Vermerk am Bereich sagt es, sonst behauptete er
       wieder mehr, als er haelt. */
    if (!is_array($container) || (string) ($container['group_visibility'] ?? 'all') !== 'named') {
        return false;
    }
    return in_array('wp_' . get_current_user_id(), array_map('strval', (array) ($container['visible_to'] ?? array())), true);
}


/* VERTRAG 28 §1b (18.09.2026) — WELCHEM PRODUKT GEHOERT DIESE SEITE.

   DAS IST EINE ANDERE FRAGE ALS §1, UND ICH HABE SIE EINEN TAG LANG FUER
   DIESELBE GEHALTEN.

   cluster_one_mein_produkt(__FILE__) beantwortet: in welchem PAKET liegt
   die rufende Datei. Fuer eine ORGANdatei ist das richtig — sie liegt in
   genau einem Paket. Fuer den BODEN ist es falsch: er liegt in JEDEM Paket,
   und es laeuft nur EINE Kopie. Welche? Die zuerst geladene.

   GEMESSEN AUF CHRISTIANS KNOTEN, 18.09., und danach in einer gebauten Welt
   mit vier Paketen nachgestellt:

       es laeuft der Boden aus: cluster-one-bismarck
       Kleinrock   -> „Cluster One – Bismarck"
       Landkarte   -> „Cluster One – Bismarck"
       Post Dolly  -> „Cluster One – Bismarck"

   Mein Zeuge w90 hat EIN Paket gebaut. Auf einem Knoten mit einem Paket ist
   die Antwort richtig. Der Fehler existiert nur dort, wo mehrere liegen.

       Ein Knoten mit ALLEN Produkten kann keinen Fehler zeigen, der nur
       OHNE sie auftritt.                                 (R-PRUEFSTUBE-1)

   Ich habe die Regel gestern geschrieben und ihre andere Haelfte nicht
   gesehen: ein Knoten mit EINEM Produkt kann keinen Fehler zeigen, der
   nur mit MEHREREN auftritt. Der Satz gilt in beide Richtungen.

   ALSO EINE ZWEITE FRAGE MIT EINEM ZWEITEN NAMEN. Sie fragt nicht die
   Datei, sondern die SEITE:

       lage = 'einziges'   es liegt genau EIN Produkt -> das ist es
       lage = 'seite'      mehrere, und die laufende Seite ist der
                           Landeplatz eines davon
       lage = 'familie'    mehrere, und die Seite gehoert keinem einzeln
                           -> „Cluster One", VERTRAG 26, woertlich
       lage = 'keine-produkte'   nichts angemeldet -> der Rufer faellt
                                 auf sein Organwort zurueck

   DER DRITTE AUSGANG IST KEINE NOTLOESUNG, SONDERN DER VERTRAG. VERTRAG 26
   sagt seit dem 10.09.: ein Produkt -> „Cluster One – <Name>", zwei und
   mehr -> „Cluster One". Die Regel stand, und die Ueberschrift hat sie
   nie bekommen. */
/** WER BEANSPRUCHT DIESE SEITE — der Sammler, und er sammelt.
 *
 * CHRISTIANS BEFUND VOM 17.09.2026, auf der DEV, erste Runde:
 *
 *     „Bei Kleinrock steht nur im Tab Mesh ‚Cluster One – Kleinrock'.
 *      Bei allen anderen Kleinrock-Tabs fehlt das ‚– Kleinrock'."
 *
 * Gemessen: Kleinrocks Anmeldung nennt EINEN Slug (cluster_one_mesh), seine
 * Reiterleiste fuehrt SECHS Seiten. Fuenf davon trafen nicht und fielen auf
 * „Cluster One".
 *
 *     Ein Produkt ist keine Seite. Ein Produkt ist eine Reiterleiste.
 *
 * VERTRAG 28 §1b war auf einer Annahme gebaut, die nie jemand hingeschrieben
 * hat — und w90 konnte sie nicht widerlegen, weil er EIN Produkt mit EINEM
 * Slug baut.                             (R-PRUEFSTUBE-1, dritte Gestalt)
 *
 * KEINE ZWEITE LISTE. Jedes Haus HAT seine Seitenliste schon: sie steht in
 * seiner Reiterleiste. Sie wird GEFRAGT, nicht abgeschrieben — dieselbe Form,
 * in der Kleinrock am 17.09. cluster_one_kalender_weg() gebaut hat.
 *
 *     Eine Wahrheit, zwei Leser.                              (Weg D)
 *
 * DIE FORM, UND SIE IST DIE EINZIGE ERLAUBTE:
 *
 *     add_filter('cluster_one_seite_ansprueche', function ($c1_a, $c1_seite) {
 *         if (!is_array($c1_a)) { return $c1_a; }
 *         $c1_meine = array('cluster_one_mesh', 'cluster_one_friends', …);
 *         if (in_array($c1_seite, $c1_meine, true)) {
 *             $c1_a[] = cluster_one_kasper_wirt(__FILE__);
 *         }
 *         return $c1_a;
 *     }, 10, 2);
 *
 * ANGEHAENGT, NICHT ZUGEWIESEN — und der Grund ist Post Dollys Messung vom
 * 09.09.: ein Sammler, der zuweist, verliert den zweiten Melder, BEVOR der
 * Waechter ihn sehen kann.                                (R-SAMMLER-1)
 *
 * Und hier ist der zweite Melder kein Unfall, sondern der Regelfall: die
 * Kalenderseite gehoert Kleinrock UND Bismarck. Ein Sammler, der zuweist,
 * haette eines der beiden Haeuser stillschweigend enteignet.
 *
 * @param  string $c1_seite  der Seiten-Slug
 * @return array<int,string> die Wirte, die sie beanspruchen, nach 'nr'
 */
function cluster_one_seite_ansprueche(string $c1_seite): array {
    if ($c1_seite === '' || !function_exists('apply_filters')) { return array(); }

    $c1_roh = apply_filters('cluster_one_seite_ansprueche', array(), $c1_seite);
    if (!is_array($c1_roh) || $c1_roh === array()) { return array(); }

    $c1_produkte = function_exists('cluster_one_produkte') ? cluster_one_produkte() : array();
    if (!is_array($c1_produkte)) { $c1_produkte = array(); }

    /* GEPRUEFT WIRD HINTERHER, und wer verworfen wird, erfaehrt es.
       Ein Anspruch auf einen Wirt, den das Produktregister nicht kennt, ist
       von „hat nie geantwortet" nicht zu unterscheiden — und bei einer
       UEBERSCHRIFT heisst das: die Seite nennt sich falsch und keiner weiss
       warum.                                                     (H218) */
    $c1_aus = array();
    foreach ($c1_roh as $c1_w) {
        if (!is_string($c1_w) || $c1_w === '') { continue; }
        if (in_array($c1_w, $c1_aus, true)) { continue; }
        if (!isset($c1_produkte[$c1_w])) {
            if (function_exists('_doing_it_wrong')) {
                // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Entwicklermeldung an _doing_it_wrong(): kein Seiteninhalt; WordPress legt sie selbst durch wp_kses (wp_trigger_error)
                _doing_it_wrong(
                    'cluster_one_seite_ansprueche',
                    sprintf(
                        'Die Seite %s wird von einem Wirt beansprucht, den das '
                      . 'Produktregister nicht kennt (%s). Der Anspruch wird NICHT '
                      . 'gefuehrt; die Seite nennt sich weiter „Cluster One".',
                        $c1_seite, $c1_w
                    ),
                    '1.12.9'
                );
                // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            }
            continue;
        }
        $c1_aus[] = $c1_w;
    }

    /* DER BODEN SORTIERT — dieselbe Regel wie am Dach (VERTRAG §5.1a):
       nach 'nr', damit „das erste Haus" ueberall dasselbe heisst. */
    usort($c1_aus, static function ($c1_x, $c1_y) use ($c1_produkte) {
        $c1_a = (int) ($c1_produkte[$c1_x]['nr'] ?? 0);
        $c1_b = (int) ($c1_produkte[$c1_y]['nr'] ?? 0);
        if ($c1_a === $c1_b) { return strcmp($c1_x, $c1_y); }
        return $c1_a < $c1_b ? -1 : 1;
    });
    return $c1_aus;
}

function cluster_one_seite_produkt(): array {
    /* 'wirte' ist neu in 1.12.9 und traegt die Lage „geteilt". */
    $c1_leer = array('wirt' => '', 'name' => '', 'nr' => 0, 'slug' => '', 'cap' => '',
                     'lage' => '', 'wirte' => array());

    $c1_alle = function_exists('cluster_one_produkte') ? cluster_one_produkte() : array();
    if (!is_array($c1_alle) || $c1_alle === array()) {
        $c1_leer['lage'] = 'keine-produkte';
        return $c1_leer;
    }

    if (count($c1_alle) === 1) {
        foreach ($c1_alle as $c1_w => $c1_a) {
            return array(
                'wirt' => (string) $c1_w,
                'name' => (string) ($c1_a['name'] ?? ''),
                'nr'   => (int)    ($c1_a['nr']   ?? 0),
                'slug' => (string) ($c1_a['slug'] ?? ''),
                'cap'  => (string) ($c1_a['cap']  ?? ''),
                'lage' => 'einziges',
            );
        }
    }

    /* WELCHE SEITE LAEUFT. WordPress setzt $plugin_page fuer jede
       Plugin-Seite; $_GET['page'] ist der Rueckfall und wird gereinigt.
           Ein Wert aus der Adresszeile wird nie ungewaschen verglichen,
           auch nicht, wenn nur ein Name daraus wird. */
    /* 28.10.2026 (Neo), Boden 1.18.0: die Frage „welche Seite laeuft" stand bis
       1.17.7 hier im Rumpf; jetzt stellen sie drei (Produkt, Herkunft, Knoten-
       Reiter), also wohnt sie einmal in cluster_one_aktuelle_seite() (P1-12). */
    $c1_seite = cluster_one_aktuelle_seite();

    /* v1.12.9 — ZUERST DER SAMMLER. Er kennt ALLE Seiten eines Produkts,
       die Anmeldung kennt nur die erste.

       UND ER KANN ZWEI ANTWORTEN. Die Kalenderseite gehoert Kleinrock und
       Bismarck; das ist kein Streit, sondern eine geteilte Seite. Sie
       bekommt eine eigene Lage, statt dass einer der beiden gewinnt.

           Wo zwei Haeuser dasselbe beanspruchen und beide recht haben,
           ist die Antwort nicht „einer von beiden". Sie ist „beide". */
    if ($c1_seite !== '') {
        $c1_anspruch = cluster_one_seite_ansprueche($c1_seite);

        if (count($c1_anspruch) > 1) {
            $c1_leer['lage']  = 'geteilt';
            $c1_leer['slug']  = $c1_seite;
            $c1_leer['wirte'] = $c1_anspruch;
            return $c1_leer;
        }
        if (count($c1_anspruch) === 1) {
            $c1_w = $c1_anspruch[0];
            $c1_a = $c1_alle[$c1_w];
            return array(
                'wirt'  => (string) $c1_w,
                'name'  => (string) ($c1_a['name'] ?? ''),
                'nr'    => (int)    ($c1_a['nr']   ?? 0),
                'slug'  => $c1_seite,
                'cap'   => (string) ($c1_a['cap']  ?? ''),
                'lage'  => 'seite',
                'wirte' => array((string) $c1_w),
            );
        }

        /* DER RUECKFALL, und er ist kein Schlendrian: ein Haus, das den
           Sammler noch nicht bedient, verhaelt sich weiter wie bisher.
           Der Boden faehrt vor den Haeusern — er darf sie nicht
           ueberholen und dabei abwerfen.                     (R-ZUG-2) */
        foreach ($c1_alle as $c1_w => $c1_a) {
            if ((string) ($c1_a['slug'] ?? '') === $c1_seite) {
                return array(
                    'wirt'  => (string) $c1_w,
                    'name'  => (string) ($c1_a['name'] ?? ''),
                    'nr'    => (int)    ($c1_a['nr']   ?? 0),
                    'slug'  => $c1_seite,
                    'cap'   => (string) ($c1_a['cap']  ?? ''),
                    'lage'  => 'seite',
                    'wirte' => array((string) $c1_w),
                );
            }
        }
    }

    $c1_leer['lage'] = 'familie';
    return $c1_leer;
}

/** 28.10.2026 (Neo), Boden 1.18.0 — DER SLUG DER LAUFENDEN SEITE, EINMAL.
 *  WordPress setzt $plugin_page fuer jede Plugin-Seite; $_GET['page'] ist der
 *  Rueckfall und wird gereinigt. Ein Wert aus der Adresszeile wird nie
 *  ungewaschen verglichen, auch nicht, wenn nur ein Name daraus wird. */
function cluster_one_aktuelle_seite(): string {
    if (isset($GLOBALS['plugin_page']) && is_string($GLOBALS['plugin_page'])) {
        return $GLOBALS['plugin_page'];
    }
    if (isset($_GET['page']) && is_string($_GET['page'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        return function_exists('sanitize_key')
            ? sanitize_key(wp_unslash($_GET['page'])) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            : preg_replace('~[^a-z0-9_\-]~', '', strtolower(stripslashes((string) $_GET['page']))); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler; der Wert geht auf dieser Zeile durch den Haus-Reiniger (preg_replace-Weissliste), den der Pruefer nicht kennt (bisheriger Zeilenkommentar: 1.21.0: stripslashes, weil dieser Zweig ohne WordPress laeuft (kein wp_unslash)); 1.21.1: Rueckfall ohne WordPress, stripslashes statt wp_unslash — der Pruefer kennt nur die WordPress-Namen
    }
    return '';
}

/** 28.10.2026 (Neo), Boden 1.18.0 — VON WELCHEM PRODUKT KOMMT DER LESER.
 *
 *  Nemesus' Entscheidung vom 28.10.: die Knoten-Seiten (Profil, System, Chronik,
 *  KI-Kennungen, Knoten-Einstellungen) und der Kalender gehoeren keinem Produkt
 *  allein, aber der Leser kommt immer von EINEM. Dessen Leiste steht oben, dessen
 *  Name in der Ueberschrift: „Cluster One – Kleinrock" von Kleinrock aus,
 *  „Cluster One – Bismarck" von Bismarck aus. Bis 1.17.7 hiessen diese Seiten
 *  „Cluster One" (17.09., VERTRAG 26 zweiter Satz); der alte Satz steht in
 *  cluster_one_page_h1() als Zitat.
 *
 *  DIE ANTWORT, IN DIESER REIHENFOLGE:
 *    1  Beansprucht genau ein Produkt die Seite: dieses. Ein Parameter kann
 *       eine Produktseite nicht umwidmen.
 *    2  Sonst ?von=<wirt>, wenn das Register den Wirt kennt — und auf einer
 *       geteilten Seite nur, wenn er sie auch traegt.
 *    3  Sonst das erste in Frage kommende Produkt nach 'nr' — dieselbe Regel,
 *       mit der der Klick auf „Cluster One" aufs erste Produkt fuehrt (16.09.).
 *  Leer nur, wenn es kein Produkt gibt.
 *
 *  BENANNTE GRENZE: „von" reist nur in den Adressen, die cluster_one_mit_herkunft()
 *  baut (Knoten-Reiter, Knoten-Reihe, Kalender-Reiter). Ein Formular, das auf
 *  die Seite zurueckleitet, verliert es; dann gilt Regel 3. Niemand landet im
 *  Leeren — die Ueberschrift nennt dann das erste Haus statt des gemerkten. */
function cluster_one_herkunft(): string {
    $c1_alle = function_exists('cluster_one_produkte') ? cluster_one_produkte() : array();
    if (!is_array($c1_alle) || $c1_alle === array()) {
        return '';
    }

    $c1_p    = cluster_one_seite_produkt();
    $c1_lage = (string) ($c1_p['lage'] ?? '');
    if (('seite' === $c1_lage || 'einziges' === $c1_lage) && '' !== (string) ($c1_p['wirt'] ?? '')) {
        return (string) $c1_p['wirt'];
    }

    $c1_erlaubt = ('geteilt' === $c1_lage && !empty($c1_p['wirte']))
        ? array_values((array) $c1_p['wirte'])
        : array_keys($c1_alle);

    $c1_von = '';
    if (isset($_GET['von']) && is_string($_GET['von'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        $c1_von = function_exists('sanitize_key')
            ? sanitize_key(wp_unslash($_GET['von'])) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            : preg_replace('~[^a-z0-9_\-]~', '', strtolower(stripslashes((string) $_GET['von']))); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler; der Wert geht auf dieser Zeile durch den Haus-Reiniger (preg_replace-Weissliste), den der Pruefer nicht kennt (bisheriger Zeilenkommentar: 1.21.0: stripslashes, weil dieser Zweig ohne WordPress laeuft (kein wp_unslash)); 1.21.1: Rueckfall ohne WordPress, stripslashes statt wp_unslash — der Pruefer kennt nur die WordPress-Namen
    }
    if ($c1_von !== '' && in_array($c1_von, $c1_erlaubt, true)) {
        return $c1_von;
    }

    usort($c1_erlaubt, static function ($c1_x, $c1_y) use ($c1_alle) {
        $c1_a = (int) ($c1_alle[$c1_x]['nr'] ?? 0);
        $c1_b = (int) ($c1_alle[$c1_y]['nr'] ?? 0);
        if ($c1_a === $c1_b) { return strcmp((string) $c1_x, (string) $c1_y); }
        return $c1_a < $c1_b ? -1 : 1;
    });
    return (string) ($c1_erlaubt[0] ?? '');
}

/** Haengt die Herkunft an eine Adresse. Ohne Wirt gilt die laufende Herkunft;
 *  ohne Herkunft bleibt die Adresse, wie sie war. Der Wirt ist ein Ordnername
 *  (a-z, 0-9, Bindestrich) und braucht keine Kodierung; esc_url() am Ausgang. */
function cluster_one_mit_herkunft(string $c1_url, string $c1_wirt = ''): string {
    if ($c1_wirt === '') {
        $c1_wirt = cluster_one_herkunft();
    }
    if ($c1_wirt === '' || $c1_url === '') {
        return $c1_url;
    }
    return add_query_arg('von', $c1_wirt, $c1_url);
}

/** Steht der Leser auf einer KNOTEN-SEITE — einer Seite der Knoten-Reihe, die
 *  kein Produkt beansprucht? Dann ist in der Produktleiste oben der Reiter
 *  „Knoten" der aktive. Eine beanspruchte Seite (Modi, Einrichtung …) ist die
 *  ihres Produkts, auch wenn sie in der Reihe stuende. */
function cluster_one_knoten_seite_aktuell(): bool {
    $c1_seite = cluster_one_aktuelle_seite();
    if ($c1_seite === '' || !function_exists('cluster_one_kranz_reiter')) {
        return false;
    }
    if (cluster_one_seite_ansprueche($c1_seite) !== array()) {
        return false;
    }
    foreach (cluster_one_kranz_reiter($c1_seite) as $c1_t) {
        if (is_array($c1_t) && !empty($c1_t['active'])) {
            return true;
        }
    }
    return false;
}

/** 03.11.2026 (Neo), Boden 1.22.0 (Tau) — steht der Leser auf einer FEYNMAN-SEITE (Derrida, Ueber Cluster One, Warum,
 *  DNA, Nemesus)? Dann ist in der Hausleiste oben der Reiter „Feynman" der aktive. Die Liste der Seiten fuehrt feynman.php
 *  (cluster_one_feynman_seiten) — EINE Liste, drei Leser: dieser Reiter, Paciolis Leiste, Feynmans eigene Reihe. Ohne
 *  Feynman auf dem Knoten: nein, und dann gibt es den Reiter auch nicht. */
function cluster_one_feynman_seite_aktuell(): bool {
    if (!function_exists('cluster_one_feynman_seiten')) {
        return false;
    }
    $c1_seite = cluster_one_aktuelle_seite();
    return $c1_seite !== '' && in_array($c1_seite, cluster_one_feynman_seiten(), true);
}


/* ---------------------------------------------------------------------------
   28.10.2026 (Neo), Boden 1.19.0 — MEIN SPEICHER: die Ablage des Menschen am Knoten.

   Nemesus: „Jeder Mensch nutzt seine eigenen KI-APIs und seine eigene Cloud.
   Genau das ist der Witz des Dezentralen." Bis heute kannte nur die Merkliste
   (Bismarck, merkliste.php) den eigenen Speicher eines Menschen: WebDAV ueber
   https, Adresse und Benutzer im User-Meta der Merkliste, das Passwort im
   Pacioli-Tresor unter einem Merklisten-Namen. Pacioli wusste davon nichts und
   konnte seinen Export nur zum Browser schicken. Zwei Produkte, die Daten des
   Menschen halten, hatten keinen gemeinsamen Begriff von „sein Speicher".

       Eine Adresse, die ein Produkt fuer sich notiert, ist eine Einstellung.
       Eine Adresse, die der Boden fuer den Menschen haelt, ist ein Ort.

   WAS HIER STEHT, IST TRANSPORT UND KEIN VERHALTEN: Adresse, Benutzer, Pruefstand
   (User-Meta 'cluster_one_speicher'), das Passwort im Tresor ('speicher_passwort',
   Vertrag cluster_one_tresor – nur der Mensch selbst liest und schreibt es), und
   vier Verben gegen den Speicher: pruefen, lesen, schreiben, loeschen. Dateinamen
   und Verschluesselung gehoeren dem Rufer: die Merkliste verschluesselt mit ihrem
   Knoten-Schluessel, Paciolis Export traegt das Passwort des Menschen. Der Boden
   setzt den Namen nur hinter die Adresse.

   UMGEZOGEN, NICHT NEU GESCHRIEBEN: cluster_one_merkliste_dav_url, _dav, _zugang
   und der Pruef-Zweig des Merklisten-Handlers stehen hier mit ihren Haerten aus
   0.2.53 (trim am Passwort; die Ablehnungsmeldung nennt Benutzer und Zeichenzahl,
   nie das Passwort; 207 gilt als Antwort). merkliste.php ruft ab Bismarck 0.2.62
   hierher und behaelt den alten Weg als Rueckfall fuer einen aelteren Boden.

   WER ETWAS DORT HAT, MELDET ES AN: apply_filters('cluster_one_speicher_belegt',
   array(), $uid) – jedes Produkt haengt an, was es fuer diesen Menschen im Speicher
   liegen hat (wirt, was, datei, knoten_liest). „Zugang vergessen" wird verweigert,
   solange etwas gemeldet ist: ein Passwort, das verschwindet, waehrend eine Datei
   davon abhaengt, ist ein stiller Schaden. Pacioli meldet nichts – seine
   Exportdateien bleiben ohne den Knoten lesbar, der Mensch hat ihr Passwort.

   BENANNTE GRENZE (unveraendert gegenueber der Merkliste, nur zusammengefuehrt):
   „beim User" heisst, der Speicher ist seiner – nicht, der Knoten sei blind. Der
   Knoten haelt das Passwort, solange der Tresor steht; faellt Pacioli, faellt der
   Tresor. Ein Speicher, der nicht antwortet, zeigt nichts statt einer alten Kopie.
   --------------------------------------------------------------------------- */
if (!function_exists('cluster_one_speicher_url')) {
/** Eine WebDAV-Adresse, wie der Boden sie nimmt: https, gueltig, mit Schraegstrich am Ende; sonst ''. */
function cluster_one_speicher_url(string $c1_url): string {
    $c1_url = trim($c1_url);
    if ($c1_url === '' || strpos($c1_url, 'https://') !== 0 || !wp_http_validate_url($c1_url)) { return ''; }
    return rtrim($c1_url, '/') . '/';
}

/** Ein Dateiname, wie der Boden ihn hinter die Adresse setzt: nur Buchstaben, Ziffern, Punkt,
 *  Strich, Unterstrich – kein Pfad, kein Schraegstrich, kein Leerzeichen. Sonst ''. */
function cluster_one_speicher_dateiname(string $c1_name): string {
    $c1_name = trim($c1_name);
    return preg_match('~^[A-Za-z0-9][A-Za-z0-9._-]{0,120}$~', $c1_name) === 1 ? $c1_name : '';
}

function cluster_one_speicher_meta(int $c1_uid): array {
    $c1_m = get_user_meta($c1_uid, 'cluster_one_speicher', true);
    $c1_m = is_array($c1_m) ? $c1_m : array();
    return array(
        'url'      => cluster_one_speicher_url((string) ($c1_m['url'] ?? '')),
        'benutzer' => (string) ($c1_m['benutzer'] ?? ''),
        'geprueft' => (int) ($c1_m['geprueft'] ?? 0),
    );
}

function cluster_one_speicher_meta_speichern(int $c1_uid, array $c1_m): void {
    update_user_meta($c1_uid, 'cluster_one_speicher', array(
        'url'      => (string) ($c1_m['url'] ?? ''),
        'benutzer' => (string) ($c1_m['benutzer'] ?? ''),
        'geprueft' => (int) ($c1_m['geprueft'] ?? 0),
    ));
}

/* Der Tresor, ueber den Vertrag (Pacioli, api.php). Dieselben drei Saetze wie in merkliste.php. */
function cluster_one_speicher_tresor_da(): bool {
    return apply_filters('cluster_one_tresor', null, 'da') === true;
}

function cluster_one_speicher_geheim(int $c1_uid, string $c1_name): string {
    $c1_w = apply_filters('cluster_one_tresor', null, 'lesen', array('user' => $c1_uid, 'name' => $c1_name));
    return is_string($c1_w) ? $c1_w : '';
}

/** @return string '' bei Erfolg, sonst ein Wort */
function cluster_one_speicher_geheim_setzen(int $c1_uid, string $c1_name, string $c1_wert): string {
    $c1_a = new stdClass();
    $c1_a->user   = $c1_uid;
    $c1_a->name   = $c1_name;
    $c1_a->wert   = $c1_wert;
    $c1_a->fehler = 'kein_tresor';
    do_action('cluster_one_tresor_schreiben', $c1_a);
    return (string) $c1_a->fehler;
}

/** @return array{url:string, benutzer:string, pass:string, geprueft:int} */
function cluster_one_speicher_zugang(int $c1_uid): array {
    $c1_m = cluster_one_speicher_meta($c1_uid);
    return array(
        'url'      => $c1_m['url'],
        'benutzer' => $c1_m['benutzer'],
        'pass'     => $c1_m['url'] !== '' ? cluster_one_speicher_geheim($c1_uid, 'speicher_passwort') : '',
        'geprueft' => $c1_m['geprueft'],
    );
}

/** Hat dieser Mensch einen Speicher, der geprueft ist und dessen Passwort im Tresor liegt? */
function cluster_one_speicher_da(int $c1_uid): bool {
    if ($c1_uid <= 0) { return false; }
    $c1_z = cluster_one_speicher_zugang($c1_uid);
    return $c1_z['url'] !== '' && $c1_z['benutzer'] !== '' && $c1_z['pass'] !== '' && $c1_z['geprueft'] > 0;
}

/** @return array{code:int, body:string, fehler:string} */
function cluster_one_speicher_dav(string $c1_methode, string $c1_url, string $c1_benutzer, string $c1_pass, string $c1_body = ''): array {
    $c1_args = array(
        'method'      => $c1_methode,
        'timeout'     => 15,
        'redirection' => 0,
        'headers'     => array('Authorization' => 'Basic ' . base64_encode($c1_benutzer . ':' . $c1_pass)),
    );
    if ($c1_methode === 'PROPFIND') { $c1_args['headers']['Depth'] = '0'; }
    if ($c1_methode === 'PUT') { $c1_args['headers']['Content-Type'] = 'application/octet-stream'; $c1_args['body'] = $c1_body; }
    $c1_r = wp_remote_request($c1_url, $c1_args);
    if (is_wp_error($c1_r)) { return array('code' => 0, 'body' => '', 'fehler' => $c1_r->get_error_message()); }
    return array('code' => (int) wp_remote_retrieve_response_code($c1_r), 'body' => (string) wp_remote_retrieve_body($c1_r), 'fehler' => '');
}

/** Zugang pruefen und bei Erfolg behalten (Meta + Tresor). Leeres Passwort = das verwahrte nehmen.
 *  @return array{code:string, detail:string}  code: geprueft | unvollstaendig | abgelehnt | nicht_erreichbar | kein_tresor */
function cluster_one_speicher_pruefen(int $c1_uid, string $c1_url, string $c1_benutzer, string $c1_pass): array {
    if (!cluster_one_speicher_tresor_da()) { return array('code' => 'kein_tresor', 'detail' => ''); }
    $c1_url = cluster_one_speicher_url($c1_url);
    $c1_ben = trim(sanitize_text_field($c1_benutzer));
    /* 0.2.53 (merkliste.php): trim – ein Leerzeichen am Rand stammt aus der Zwischenablage, nicht aus dem Passwort. */
    $c1_pass = trim($c1_pass);
    if ($c1_pass === '') { $c1_pass = cluster_one_speicher_geheim($c1_uid, 'speicher_passwort'); }
    if ($c1_url === '' || $c1_ben === '' || $c1_pass === '') { return array('code' => 'unvollstaendig', 'detail' => ''); }
    $c1_r = cluster_one_speicher_dav('PROPFIND', $c1_url, $c1_ben, $c1_pass);
    /* Bei Ablehnung sagt die Meldung, was der Knoten gesendet hat – Benutzer und Zeichenzahl, nie das Passwort. */
    $c1_was = sprintf(
        /* translators: 1: user name as sent, 2: number of characters in the password */
        __('Sent: user %1$s, password with %2$d characters.', 'cluster-one-bismarck'),
        $c1_ben, mb_strlen($c1_pass));
    if (preg_match('~\s~u', $c1_pass) === 1) { $c1_was .= ' ' . __('The password contains a space.', 'cluster-one-bismarck'); }
    $c1_ok = ($c1_r['code'] >= 200 && $c1_r['code'] <= 299) || $c1_r['code'] === 207;
    if ($c1_ok && cluster_one_speicher_geheim_setzen($c1_uid, 'speicher_passwort', $c1_pass) !== '') {
        return array('code' => 'kein_tresor', 'detail' => '');
    }
    cluster_one_speicher_meta_speichern($c1_uid, array('url' => $c1_url, 'benutzer' => $c1_ben, 'geprueft' => $c1_ok ? time() : 0));
    if ($c1_ok) { return array('code' => 'geprueft', 'detail' => ''); }
    if ($c1_r['code'] === 401 || $c1_r['code'] === 403) { return array('code' => 'abgelehnt', 'detail' => $c1_r['code'] . ' · ' . $c1_was); }
    return array('code' => 'nicht_erreichbar', 'detail' => $c1_r['code'] > 0 ? (string) $c1_r['code'] : $c1_r['fehler']);
}

/** Die drei Verben gegen eine Datei im Speicher. Ohne vollstaendigen Zugang oder mit einem
 *  Namen, der keiner ist: code 0 und ein Satz – nichts wird versucht. */
function cluster_one_speicher_verb(int $c1_uid, string $c1_methode, string $c1_datei, string $c1_body = ''): array {
    $c1_z = cluster_one_speicher_zugang($c1_uid);
    $c1_n = cluster_one_speicher_dateiname($c1_datei);
    if ($c1_z['url'] === '' || $c1_z['benutzer'] === '' || $c1_z['pass'] === '') {
        return array('code' => 0, 'body' => '', 'fehler' => __('The access data for your storage is incomplete.', 'cluster-one-bismarck'));
    }
    if ($c1_n === '') {
        return array('code' => 0, 'body' => '', 'fehler' => __('That is not a file name this node would write.', 'cluster-one-bismarck'));
    }
    return cluster_one_speicher_dav($c1_methode, $c1_z['url'] . $c1_n, $c1_z['benutzer'], $c1_z['pass'], $c1_body);
}

/** @return array{code:int, body:string, fehler:string} */
function cluster_one_speicher_lesen(int $c1_uid, string $c1_datei): array {
    return cluster_one_speicher_verb($c1_uid, 'GET', $c1_datei);
}

/** @return array{code:int, body:string, fehler:string} */
function cluster_one_speicher_schreiben(int $c1_uid, string $c1_datei, string $c1_body): array {
    return cluster_one_speicher_verb($c1_uid, 'PUT', $c1_datei, $c1_body);
}

/** @return array{code:int, body:string, fehler:string} */
function cluster_one_speicher_loeschen(int $c1_uid, string $c1_datei): array {
    return cluster_one_speicher_verb($c1_uid, 'DELETE', $c1_datei);
}

/** 30.10.2026 (Neo), Boden 1.19.1 — DER BLICK IN DEN ORDNER. Nemesus auf der Main: der KI-Export
 *  lag im Speicher, aber „Was dort liegt" nannte nur die Merkliste. Die Liste nannte, was Produkte
 *  halten; die Ueberschrift versprach, was dort liegt. Jetzt gibt es beides, und dafuer braucht der
 *  Boden die Frage an den Ordner (PROPFIND Depth 1) – bis gestern stellte nur Pacioli sie, fuer sein
 *  „Im Speicher nachsehen" (0.8.39); mit dem zweiten Leser zieht sie hierher, wie dort angekuendigt.
 *  Gelesen werden die <href>-Zeilen der Multistatus-Antwort, gleich mit welchem Namensraum-Praefix;
 *  genommen wird der letzte Pfadteil, entschluesselt, nur wenn er die Namensregel traegt. Ordner
 *  (Pfad endet auf /) fallen weg. Ein Speicher, der nicht antwortet, gibt einen Satz, keine Liste.
 *  @return array{dateien: array<int,string>, fehler: string} */
function cluster_one_speicher_liste(int $c1_uid): array {
    $c1_z = cluster_one_speicher_zugang($c1_uid);
    if ($c1_z['url'] === '' || $c1_z['benutzer'] === '' || $c1_z['pass'] === '') {
        return array('dateien' => array(), 'fehler' => __('The access data for your storage is incomplete.', 'cluster-one-bismarck'));
    }
    $c1_r = wp_remote_request($c1_z['url'], array(
        'method'      => 'PROPFIND',
        'timeout'     => 15,
        'redirection' => 0,
        'headers'     => array(
            'Authorization' => 'Basic ' . base64_encode($c1_z['benutzer'] . ':' . $c1_z['pass']),
            'Depth'         => '1',
        ),
    ));
    if (is_wp_error($c1_r)) {
        /* translators: %s: HTTP status code or transport error */
        return array('dateien' => array(), 'fehler' => sprintf(__('Your storage did not answer (%s).', 'cluster-one-bismarck'), $c1_r->get_error_message()));
    }
    $c1_code = (int) wp_remote_retrieve_response_code($c1_r);
    if ($c1_code !== 207 && ($c1_code < 200 || $c1_code > 299)) {
        /* translators: %s: HTTP status code or transport error */
        return array('dateien' => array(), 'fehler' => sprintf(__('Your storage did not answer (%s).', 'cluster-one-bismarck'), (string) $c1_code));
    }
    $c1_aus = array();
    if (preg_match_all('~<(?:[A-Za-z0-9_]+:)?href[^>]*>([^<]+)</(?:[A-Za-z0-9_]+:)?href>~i', (string) wp_remote_retrieve_body($c1_r), $c1_m)) {
        foreach ($c1_m[1] as $c1_href) {
            $c1_pfad = rawurldecode(html_entity_decode(trim((string) $c1_href), ENT_QUOTES | ENT_XML1, 'UTF-8'));
            if ($c1_pfad === '' || substr($c1_pfad, -1) === '/') { continue; }
            $c1_name = cluster_one_speicher_dateiname((string) basename($c1_pfad));
            if ($c1_name !== '') { $c1_aus[$c1_name] = $c1_name; }
        }
    }
    ksort($c1_aus);
    return array('dateien' => array_values($c1_aus), 'fehler' => '');
}

/** Was fuer diesen Menschen im Speicher liegt – gemeldet von den Produkten.
 *  @return array<int, array{wirt:string, was:string, datei:string, knoten_liest:bool}> */
function cluster_one_speicher_belegt(int $c1_uid): array {
    $c1_roh = apply_filters('cluster_one_speicher_belegt', array(), $c1_uid);
    $c1_aus = array();
    foreach ((array) $c1_roh as $c1_e) {
        if (!is_array($c1_e) || (string) ($c1_e['was'] ?? '') === '') { continue; }
        $c1_aus[] = array(
            'wirt'         => (string) ($c1_e['wirt'] ?? ''),
            'was'          => (string) $c1_e['was'],
            'datei'        => (string) ($c1_e['datei'] ?? ''),
            'knoten_liest' => !empty($c1_e['knoten_liest']),
        );
    }
    return $c1_aus;
}

/** Zugang vergessen: Passwort aus dem Tresor, Adresse aus dem Meta. Verweigert, solange etwas gemeldet ist.
 *  @return string '' bei Erfolg, sonst 'belegt' */
function cluster_one_speicher_vergessen(int $c1_uid): string {
    if (cluster_one_speicher_belegt($c1_uid) !== array()) { return 'belegt'; }
    cluster_one_speicher_geheim_setzen($c1_uid, 'speicher_passwort', '');
    cluster_one_speicher_meta_speichern($c1_uid, array());
    cluster_one_event_emit(array('type' => 'storage_forgotten', 'container_type' => 'user', 'container_id' => (string) $c1_uid, 'actor_id' => 'wp:' . (int) $c1_uid), true); // 1.21.0: bis 1.20.1 stand hier (string) $c1_uid — ohne 'wp:' zeigte die Chronik die nackte Zahl
    return '';
}

/** DER UMZUG AUS DER MERKLISTE – einmal, beim ersten Besuch des Menschen. Nur er darf seinen
 *  Tresor lesen, darum laeuft das nicht beim Laden, sondern wenn er seine Seite aufruft.
 *  Nichts verschwindet: die Merklisten-Eintraege (Meta 'cloud', Tresor 'merkliste_speicher')
 *  bleiben stehen, bis der Mensch dort „Zugang vergessen" drueckt. Die Liste selbst und ihre
 *  Datei im Speicher werden nicht angefasst.
 *  @return bool  true, wenn uebernommen wurde */
function cluster_one_speicher_uebernahme(int $c1_uid): bool {
    if ($c1_uid <= 0 || $c1_uid !== (int) get_current_user_id()) { return false; }
    if (cluster_one_speicher_meta($c1_uid)['url'] !== '') { return false; }
    if (!cluster_one_speicher_tresor_da()) { return false; }
    $c1_alt = get_user_meta($c1_uid, 'cluster_one_merkliste', true);
    $c1_c   = is_array($c1_alt) && isset($c1_alt['cloud']) && is_array($c1_alt['cloud']) ? $c1_alt['cloud'] : array();
    $c1_url = cluster_one_speicher_url((string) ($c1_c['url'] ?? ''));
    $c1_ben = (string) ($c1_c['benutzer'] ?? '');
    if ($c1_url === '' || $c1_ben === '') { return false; }
    $c1_pass = cluster_one_speicher_geheim($c1_uid, 'merkliste_speicher');
    if ($c1_pass === '') { return false; }
    if (cluster_one_speicher_geheim_setzen($c1_uid, 'speicher_passwort', $c1_pass) !== '') { return false; }
    cluster_one_speicher_meta_speichern($c1_uid, array('url' => $c1_url, 'benutzer' => $c1_ben, 'geprueft' => (int) ($c1_c['geprueft'] ?? 0)));
    cluster_one_event_emit(array('type' => 'storage_adopted', 'container_type' => 'user', 'container_id' => (string) $c1_uid, 'actor_id' => 'wp:' . (int) $c1_uid), true); // 1.21.0: bis 1.20.1 stand hier (string) $c1_uid — ohne 'wp:' zeigte die Chronik die nackte Zahl
    return true;
}
}


/* VERTRAG 28 §1 (17.09.2026) — WELCHES PRODUKT BIN ICH.

   DER BENANNTE ZUGANG, DEN ES NICHT GAB. Der Wirt war zu haben
   (cluster_one_kasper_wirt), das Register war zu haben (cluster_one_produkte,
   nach WIRT verschluesselt) — nur die Bruecke zwischen beiden fehlte, und an
   ihrem Fehlen hingen vier Dinge, die Christian am 17.09. auf der DEV gesehen
   hat: eine Ueberschrift, die ein Organ nennt statt des Produkts; ein Reiter,
   der ein fremdes Haus nennt; ein Loeschknopf, der fremde Daten raeumt; und
   siebzehn Seiten, die keine Hand haben.

       Wo eine Auskunft keinen Namen hat, erfindet jeder Leser sich seine
       eigene, und zwei Leser erfinden zwei.          (Maillart, Punkt 8)

   R-STILLE-1: DER GRUND STEHT IM RUECKGABEWERT. Vier Lagen, und jede sagt
   sich selbst — ein leeres array() waere eine Antwort auf „was" und nie auf
   „warum".

       'da'               die Anmeldung steht
       'kein-kasper'      kasper.php ist nicht geladen
       'kein-wirt'        die Datei liegt nicht unter WP_PLUGIN_DIR
       'nicht-angemeldet' der Wirt ist da und meldet kein Produkt

   BENANNTE GRENZE, und sie ist wichtiger als sie aussieht: cluster_one_produkte()
   ist nach WIRT verschluesselt. Ein Paket kann darum gar nicht ZWEI Produkte
   tragen — der Fall „mehrere unter einem Wirt" ist in dieser Form nicht
   darstellbar, und deshalb steht hier KEIN Zweig dafuer.
       Eine Pruefung, die in jeder gemessenen Welt dasselbe antwortet, hat
       nichts geprueft.                                         (R-PRUEFUNG-1)
   Aendert sich die Form des Registers, aendert sich diese Funktion mit ihr.

   DIE DATEI WIRD UEBERGEBEN, NICHT ERRATEN: wer sie ruft, uebergibt __FILE__
   seiner eigenen Datei. Ohne Angabe fragt sie fuer den Boden selbst — und der
   liegt im selben Paket wie das Produkt. */
function cluster_one_mein_produkt(string $c1_datei = ''): array {
    $c1_leer = array('wirt' => '', 'name' => '', 'nr' => 0, 'slug' => '', 'cap' => '', 'lage' => '');

    if ($c1_datei === '') {
        $c1_datei = __FILE__;
    }
    if (!function_exists('cluster_one_kasper_wirt')) {
        $c1_leer['lage'] = 'kein-kasper';
        return $c1_leer;
    }
    $c1_wirt = cluster_one_kasper_wirt($c1_datei);
    if ($c1_wirt === '') {
        $c1_leer['lage'] = 'kein-wirt';
        return $c1_leer;
    }
    $c1_leer['wirt'] = $c1_wirt;

    if (!function_exists('cluster_one_produkte')) {
        $c1_leer['lage'] = 'nicht-angemeldet';
        return $c1_leer;
    }
    $c1_alle = cluster_one_produkte();
    if (!is_array($c1_alle) || !isset($c1_alle[$c1_wirt]) || !is_array($c1_alle[$c1_wirt])) {
        $c1_leer['lage'] = 'nicht-angemeldet';
        return $c1_leer;
    }
    $c1_a = $c1_alle[$c1_wirt];
    return array(
        'wirt' => $c1_wirt,
        'name' => (string) ($c1_a['name'] ?? ''),
        'nr'   => (int)    ($c1_a['nr']   ?? 0),
        'slug' => (string) ($c1_a['slug'] ?? ''),
        'cap'  => (string) ($c1_a['cap']  ?? ''),
        'lage' => 'da',
    );
}


/* v0.6.9: EINE ÜBERSCHRIFT, EIN MUSTER.

   Christians Befund: Kranz stand als "Cluster One – Kranz" da, Kleinrock nur
   als "Cluster One", Bismarck/Gregor/Feynman gar ohne Vorsatz. Drei Formen für
   dieselbe Sache.

   Der Helfer statt einzelner Zeichenketten, damit es nicht wieder
   auseinanderläuft — und weil die Organnamen EIGENNAMEN sind: Sie werden nicht
   übersetzt, also braucht es dafür auch keine Übersetzungseinträge. Der
   Gedankenstrich ist derselbe wie bisher bei Kranz (–, nicht -). */
function cluster_one_page_title(string $organ): string {
    /* v1.12.1 — DAS STOTTERN. Gefunden auf Christians Knoten am 16.09.:
       die Dachseite trug die Ueberschrift „Cluster One – Cluster One".

       Der Satz, der es verbietet, stand seit Wochen in menue.php :306 —
       geschrieben fuer den MENUEPUNKT, und dort hielt er auch. Die
       UEBERSCHRIFT hat ihn nie bekommen.

           Eine Regel, die fuer die eine Haelfte geschrieben wurde, gilt
           der anderen nicht, bloss weil sie daneben steht.

       Hollerith hat die Stelle gefunden und die Zeile dazu geschrieben:

           Eine Funktion, die ein Wort davorsetzt, darf dieses Wort nicht
           entgegennehmen.

       DER AUFRUFER IST NICHT IM UNRECHT, und darum steht hier keine
       Meldung: menue.php :240 gibt der Familienseite mit Absicht den
       Namen der Familie. Sie will „Cluster One" heissen. Der Fehler war,
       dass diese Funktion daraus zwei machte.

       ZEICHENKETTENGLEICHHEIT, wie nebenan: ein Organ, das „cluster one"
       klein heisst, bekommt weiterhin „Cluster One – cluster one". Ein
       anders geschriebener Name IST ein anderer. (Maillart, 10.09.) Eine
       zweite, weichere Regel an dieser Stelle waere eine zweite Wahrheit
       ueber dieselbe Sache.

           Ein Dach ueber der Familie traegt den Namen der Familie. */
    if ('Cluster One' === $organ) {
        return 'Cluster One';
    }
    return 'Cluster One – ' . $organ;
}

/* v0.7.0: Die Ueberschrift MIT ihrem Fragezeichen. Weil alle Hauptseiten seit
   0.6.9 durch denselben Helfer laufen, ist es damit ueberall vorhanden — und
   niemand kann es beim naechsten Modul vergessen. Gibt es zu dem Organ keinen
   Text, bleibt es weg statt leer dazustehen. */
function cluster_one_page_h1(string $organ): string {
    /* v0.9.63: GENÄHT. cluster_one_help() wohnt in Feynman, und diese Zeile
       steht über JEDER Seite des Plugins. Ohne Feynman starb damit nicht eine
       Seite, sondern jede — Cluster One Light kam bis zur Überschrift.

       Gefunden von der SEITENPROBE, nicht von der Ladeprobe: Die Konfiguration
       lud tadellos, 253 Funktionen, kein Fatal. Erst beim RENDERN fiel sie um.
       LADEN IST NICHT LAUFEN — und ein Werkzeug, das nur das eine prüft, sagt
       beruhigend nichts über das andere.

       v0.9.66: DER SATZ, DER HIER STAND, WAR ZU BEQUEM. Er lautete: "Ohne
       Fragezeichen steht die Überschrift allein da. Das ist kein Verlust,
       sondern genau das, was ein Knoten ohne Lernpfad zeigen soll." Christian
       hat ihn kassiert, und er hat recht: Der beste Text, den dieses Haus über
       das Mesh besitzt, war ausgerechnet der, den ein Light-Nutzer nie zu sehen
       bekam. Ein Knoten ohne Lernpfad braucht keinen Lernpfad — aber er braucht
       die Erklärung des einen Organs, um das es bei ihm geht.

       ZWEI LESER, EIN TEXT. Steht Feynman im Haus, kommt die Blase wie immer
       von dort (dessen Namenstabelle holt sich Kleinrocks Zeile ihrerseits aus
       kleinrock.php). Fehlt Feynman, liest cluster_one_help_eigen() dieselbe
       Zeile direkt beim Organ. Es gibt keinen zweiten TEXT — es gibt einen
       zweiten WEG zu demselben. P1-12 verbietet zwei Schreibwege auf eine
       Wahrheit; zwei Klinken an derselben Tür sind erlaubt (0.9.40).

       Bezeugt von Posten J (blase.php): Blase in beiden Häusern, und der Text
       genau einmal im Quellbaum. */
    /* VERTRAG 28 §2 (17.09.2026) — DER TITEL NENNT DAS PRODUKT, DIE BLASE DAS ORGAN.

       ZWEI WAHRHEITEN RITTEN AUF EINEM ARGUMENT. $organ war zugleich der
       Ueberschriftstext UND der Schluessel der Hilfeblase. Im Monolithen war
       das richtig: ein Plugin, viele Organe, und die Ueberschrift sagte, auf
       welchem Organ man steht. Nach dem Schnitt nennt dasselbe Wort den Namen
       eines FREMDEN PRODUKTS — „Cluster One – Kranz" in Crockford.

           Ein Wort, das vor einer Teilung ein ORGAN benannte, benennt nach
           der Teilung ein PRODUKT.                              (R-NAME-1)

       CHRISTIANS ENTSCHEIDUNG, 17.09.: die Ueberschrift traegt NUR das
       Produkt. Wo man ist, sagt die Reiterleiste.

       UND DER DENKFEHLER, DER FAST GEBAUT WORDEN WAERE: wer $organ einfach
       durch den Produktnamen ersetzt, tauscht damit auch den Schluessel der
       Blase — auf der Landkarte staende dann Crockfords Text statt des
       Landkarten-Textes. Sie sieht dabei RICHTIG aus.
           Ein Fehler, der den richtigen Inhalt an den falschen Platz bringt,
           sieht beim Lesen des Codes wie ein Erfolg aus.

       RUECKFALL, BENANNT (R-LEIHE-1): steht die Anmeldung nicht, bleibt es
       beim Organwort wie bisher. Kein weisser Schirm, nur weniger. */
    /* v1.12.7 (18.09.2026) — HIER STAND cluster_one_mein_produkt(__FILE__),
       UND DAS WAR DIE FALSCHE FRAGE. Der Boden liegt in jedem Paket und
       laeuft nur einmal; __FILE__ nannte darum auf einem Knoten mit vier
       Produkten viermal dasselbe Haus. Siehe §1b. */
    $c1_p    = function_exists('cluster_one_seite_produkt')
        ? cluster_one_seite_produkt()
        : array();
    $c1_lage = is_array($c1_p) ? (string) ($c1_p['lage'] ?? '') : '';
    if (('einziges' === $c1_lage || 'seite' === $c1_lage) && '' !== (string) ($c1_p['name'] ?? '')) {
        $c1_wort = (string) $c1_p['name'];
    /* 03.11.2026 (Neo), Boden 1.22.0 (Tau): auch 'Feynman' — der Lernpfad heisst nach dem Haus, von dem der Leser kam,
       wie die Knoten-Seiten. Bis 1.21.1 stand im Kommentar darunter: „andere Familienseiten (Lernpfad, Dach) heissen
       weiter „Cluster One""; fuer den Lernpfad gilt das seit Nemesus' Entscheidung vom 02.11. nicht mehr, fuer das Dach
       weiter. Der Schluessel der Blase bleibt das Organwort. */
    } elseif ('geteilt' === $c1_lage || ('familie' === $c1_lage && ('Kranz' === $organ || 'Feynman' === $organ))) {
        /* 28.10.2026 (Neo), Boden 1.18.0 — DIE HERKUNFT NENNT DAS HAUS. Bis 1.17.7
           stand hier fuer 'familie' UND 'geteilt':
               $c1_wort = 'Cluster One';          // VERTRAG 26, zweiter Satz
           mit dem Satz: „geteilt ist die Kalenderseite: zwei Haeuser, ein Ort.
           Sie heisst wie die Familie, weil sie keinem der beiden allein gehoert."
           Nemesus am 28.10.: „Im Kalender steht nur Cluster One. Ebenso bei
           Knoten. Bitte auch mit Kleinrock ausstatten." — und von Bismarck aus
           mit Bismarck: die Seite gehoert keinem allein, aber der Leser kommt
           von einem (cluster_one_herkunft). 'Kranz' ist das Wort, mit dem jede
           Knoten-Seite diese Funktion ruft; andere Familienseiten (Lernpfad,
           Dach) heissen weiter „Cluster One", siehe den Zweig darunter. */
        $c1_von  = cluster_one_herkunft();
        $c1_alle = function_exists('cluster_one_produkte') ? cluster_one_produkte() : array();
        $c1_wort = ($c1_von !== '' && is_array($c1_alle) && '' !== (string) ($c1_alle[$c1_von]['name'] ?? ''))
            ? (string) $c1_alle[$c1_von]['name']
            : 'Cluster One';
    } elseif ('familie' === $c1_lage) {
        $c1_wort = 'Cluster One';          /* VERTRAG 26, zweiter Satz */
    } else {
        $c1_wort = $organ;                 /* benannter Rueckfall, wie bisher */
    }

    return esc_html(cluster_one_page_title($c1_wort))
        . (function_exists('cluster_one_help')
            ? cluster_one_help($organ)
            : cluster_one_help_eigen($organ));
}

/* 27.10.2026 (Neo), Boden 1.17.6 — DER STEMPEL: JEDES PRODUKT SAGT AUF SEINER SEITE,
   WER ES GEBAUT HAT UND WORAUF. Nemesus: „Diese Auskunft über den Boden gehört
   eigentlich zu jedem Plugin." Ja — sie KOMMT aus jedem Plugin (Register, Felder
   'werft' und 'boden', Boden 1.17.3), aber gezeigt wurde sie nur auf dem Dach, und
   das Dach wird auf einem Knoten mit zwei oder mehr Produkten nie gezeigt (Umleitung
   aufs erste Produkt, 16.09.). Also steht sie jetzt dort, wo man das Produkt ansieht.
   Zwei Klinken, eine Tuer: _teile() gibt den Text (fuer Kleinrocks eigene Zeile mit
   Spatenstich und Foederation), stempel() den fertigen Absatz mit Fassung davor.
   Erklaerung, kein Beweis – wie beim Dach. Fehlt die Angabe, fehlt sie sichtbar. */
if (!function_exists('cluster_one_stempel_teile')) {
    function cluster_one_stempel_teile(string $c1_datei): string {
        if (!function_exists('cluster_one_produkte') || !function_exists('cluster_one_kasper_wirt')) { return ''; }
        $c1_w = cluster_one_kasper_wirt($c1_datei);
        if ($c1_w === '') { return ''; }
        $c1_p = cluster_one_produkte();
        $c1_v = isset($c1_p[$c1_w]) && is_array($c1_p[$c1_w]) ? $c1_p[$c1_w] : array();
        $c1_werft = (string) ($c1_v['werft'] ?? '');
        $c1_boden = (string) ($c1_v['boden'] ?? '');
        $c1_t = array();
        /* translators: %s: who built this product (a name the product declares about itself) */
        $c1_t[] = $c1_werft !== '' ? sprintf(__('shipyard: %s', 'cluster-one-bismarck'), $c1_werft) : __('shipyard: unknown', 'cluster-one-bismarck');
        /* translators: %s: the version of the shared floor this product was built against */
        if ($c1_boden !== '') { $c1_t[] = sprintf(__('built on floor %s', 'cluster-one-bismarck'), $c1_boden); }
        return esc_html(implode(' · ', $c1_t));
    }
}
if (!function_exists('cluster_one_stempel')) {
    function cluster_one_stempel(string $c1_datei, string $c1_fassung): string {
        $c1_t = cluster_one_stempel_teile($c1_datei);
        $c1_f = $c1_fassung !== '' ? '<code style="opacity:.8;">v' . esc_html($c1_fassung) . '</code>' : '';
        if ($c1_f === '' && $c1_t === '') { return ''; }
        return '<p style="opacity:.65;font-size:.9em;margin:-.2em 0 1em;">' . $c1_f . ($c1_f !== '' && $c1_t !== '' ? ' &nbsp;·&nbsp; ' : '') . $c1_t . '</p>';
    }
}

/* v0.9.66: DIE BLASE OHNE FEYNMAN.

   Fragt die Organe, die ihren eigenen Steckbrief tragen. Heute ist das genau
   eines — Kleinrock, weil Christian gesagt hat, es sei das einzige, das Light
   braucht. Die Schleife ist trotzdem eine Schleife: Zieht später ein weiterer
   Steckbrief zu seinem Organ, steht er ohne Änderung an dieser Stelle mit drin.

   Die Spaltenwahl ist absichtlich dieselbe wie in feynman.php (Spalte 5, sonst
   Spalte 3), damit hier keine zweite Auswahlregel entsteht. Dort heisst sie
   seit 0.7.1 "lieber kurz als gar nicht", und das gilt hier genauso. */
function cluster_one_help_eigen(string $organ): string {
    $organ = strtolower(trim($organ));
    if ($organ === '' || !function_exists('cluster_one_help_bubble')) {
        return '';
    }
    foreach (array('cluster_one_kleinrock_steckbrief') as $quelle) {
        if (!function_exists($quelle)) {
            continue;
        }
        $z = (array) call_user_func($quelle);
        if (strtolower((string) ($z[0] ?? '')) !== $organ) {
            continue;
        }
        $txt = !empty($z[5]) ? $z[5] : (!empty($z[3]) ? $z[3] : '');
        return $txt !== '' ? cluster_one_help_bubble((string) $txt) : '';
    }
    return '';
}

/* v1.9.12 (09.09.2026) — DER WEGWEISER KOMMT AUS DER QUELLE, NICHT AUS DEM SATZ.
 *
 * H120, gemeldet am 29.08.2026 und heute gegen 1.9.10 nachgemessen: einundzwanzig
 * uebersetzbare Zeichenketten tragen einen Menuepfad UNSERES Hauses als Text.
 * Am 29.08. waren es vierzehn. Sieben sind in zehn Tagen dazugekommen, ohne dass
 * jemand etwas falsch gemacht haette — sie sind einfach geschrieben worden.
 *
 *     Ein Menuepunkt, der in einer Zeichenkette steht, ist eine zweite
 *     Wahrheit ueber den Ort.
 *
 * WARUM EIN SLUG UND KEIN ORGANNAME. Der naheliegende Entwurf nahm den Organnamen
 * ('bismarck') und schlug ihn in einer Tabelle nach. Diese Tabelle waere die
 * dritte Wahrheit gewesen: Anmeldung, Satz, Tabelle. Der SLUG dagegen IST die
 * Kennung, mit der add_submenu_page() arbeitet — und mit der der Link daneben
 * ohnehin gebaut wird (admin_url('admin.php?page=<slug>')).
 *
 *     Satz und Link tragen damit DIESELBE Kennung. Sie koennen nicht mehr
 *     auseinanderlaufen — und genau das ist heute in platon.php passiert:
 *     der Link zeigt auf cluster_one_settings, der Satz sagt „Kranz", und
 *     die Maske wohnt seit heute Nacht auf cluster_one_bismarck_settings.
 *
 * WAS SIE LIEST: WordPress' eigenes gebautes Menue ($menu/$submenu). Das ist
 * keine Kopie der Anmeldung, das IST sie — nach allen Filtern, nach allen
 * Riegeln, mit dem Etikett, das der Mensch wirklich sieht. Zieht das Menue um
 * (Vertrag 21 §2a, das Dach ab zwei Satelliten), zieht der Satz mit, ohne dass
 * jemand ihn anfasst. Das ist der ganze Zweck.
 *
 * DREI ANTWORTEN, und die dritte ist keine Ausrede:
 *   Pfad         der Eintrag steht im Menue
 *   ''           er steht nicht drin — das Organ fehlt, ODER die Seite ist
 *                mit Absicht versteckt (add_submenu_page(null, ...)).
 *                Eine versteckte Seite HAT keinen Pfad; '' ist die Wahrheit.
 *   '' + ZAEHLUNG zu frueh gefragt, vor admin_menu. Dann waere ein leeres
 *                Ergebnis eine Luege ueber ein Organ, das da ist.
 *                Dieselbe Bauform wie cluster_one_kasper_zu_frueh (0.9.72):
 *                die Warnung ist die Hauptsache, die Zahl die Zugabe, und
 *                keine Diagnose darf den Aufruf toeten, den sie diagnostiziert.
 */
function cluster_one_menue_pfad_zu_frueh(): void {
    if (function_exists('_doing_it_wrong')) {
        _doing_it_wrong(
            'cluster_one_menue_pfad',
            'Zu frueh gefragt: vor admin_menu ist das Menue nicht gebaut. '
          . 'Frag beim Ausgeben, nicht beim Laden.',
            '1.9.11'
        );
    }
    if (function_exists('update_option') && function_exists('get_option')) {
        update_option(
            'cluster_one_menue_pfad_zu_frueh',
            1 + (int) get_option('cluster_one_menue_pfad_zu_frueh', 0),
            false
        );
    }
}

/** Der Menuepfad EINER Seite, so wie er heute im Menue steht. '' wenn keiner.
 *
 * @param string $slug die Seitenkennung aus add_menu_page/add_submenu_page
 * @return string z. B. „Cluster One → Kranz (Setup)" — oder ''
 */
function cluster_one_menue_pfad(string $slug): string {
    $slug = trim($slug);
    if ($slug === '') {
        return '';
    }
    if (function_exists('did_action') && !did_action('admin_menu')) {
        cluster_one_menue_pfad_zu_frueh();
        return '';
    }

    global $menu, $submenu;

    /* Ein Etikett kann HTML tragen — WordPress haengt Zaehlblasen an, als
       <span class="update-plugins">3</span>. Heute tut es das bei uns nicht.
       Ein Satz mit einer Blase mittendrin waere ein Fehler, den niemand beim
       Schreiben sieht.

       UND STRIP_TAGS REICHT NICHT: es nimmt die Klammern und laesst die
       DREI stehen. Der Zeuge hat mir das im ersten Lauf gedruckt —
       „Cluster One 3 → Kranz (Setup)". Die Blase muss MIT ihrem Inhalt
       heraus, und genau so macht es WordPress selbst, wenn es einen
       sauberen Titel braucht (get_admin_page_title).

           Ein Etikett zu entkleiden heisst nicht, seine Naehte zu
           entfernen. Es heisst, das wegzunehmen, was kein Name ist. */
    $c1_sauber = static function ($roh): string {
        $roh = preg_replace('~<span[^>]*>.*?</span>~is', '', (string) $roh);
        /* 1.19.8 (01.11.2026, Neo): Plugin Check (strip_tags discouraged). Bis 1.19.7 stand hier
           ein Rueckfall:  function_exists('wp_strip_all_tags') ? wp_strip_all_tags(...) : strip_tags(...)
           kern.php laeuft nur in WordPress, und dort gibt es wp_strip_all_tags seit 2.9. */
        return trim(wp_strip_all_tags((string) $roh));
    };

    /* Erst die Hauptebene: eine Seite, die selbst ein Hauptmenuepunkt ist,
       hat einen einteiligen Pfad und keinen Elternteil. */
    if (is_array($menu)) {
        foreach ($menu as $c1_e) {
            if (is_array($c1_e) && isset($c1_e[2]) && (string) $c1_e[2] === $slug) {
                return $c1_sauber($c1_e[0] ?? '');
            }
        }
    }

    if (!is_array($submenu)) {
        return '';
    }

    foreach ($submenu as $c1_eltern => $c1_kinder) {
        if (!is_array($c1_kinder)) {
            continue;
        }

        /* v1.9.14: DIE VERSTECKTE SAMMELSTELLE. add_submenu_page(null, ...)
           legt seine Seiten unter $submenu[''] ab — das ist kein Elternteil,
           das ist der Ort fuer alles, was BEWUSST nicht im Menue steht.

           Bis heute lief eine solche Seite unten in den Zweig „Satellit ohne
           Dach" und bekam ihr eigenes Etikett als Pfad zurueck. Der Wegweiser
           schrieb daraufhin „This is managed under Gregor (Kalender)" — ein
           Menueweg, den es nicht gibt. Christians Befund vom 18.09.

               Ein Elternteil, den es nicht gibt, und ein Elternteil, den
               es mit Absicht nicht gibt, sind nicht dasselbe. Der leere
               Schluessel ist keine Auskunft ueber ein Dach. Er ist die
               Auskunft, dass keins gewollt war.

           Und der Satz stand seit 1.9.11 im Kopf dieser Funktion:
               „Eine versteckte Seite HAT keinen Pfad; '' ist die Wahrheit."
           Er stimmte als Entwurf. Der Code hat ihn nicht getragen — dieselbe
           Gestalt wie der Riegel in faust.php (VOLLZUG 14).      (w94, 3/3b) */
        if ((string) $c1_eltern === '') {
            continue;
        }
        foreach ($c1_kinder as $c1_k) {
            if (!is_array($c1_k) || !isset($c1_k[2]) || (string) $c1_k[2] !== $slug) {
                continue;
            }
            $c1_kind = $c1_sauber($c1_k[0] ?? '');

            /* Der Elternteil steht unter seinem eigenen Slug in $menu. Findet
               er sich nicht, ist das Kind trotzdem eine Auskunft — der halbe
               Pfad ist besser als keiner und besser als ein erfundener. */
            $c1_dach = '';
            if (is_array($menu)) {
                foreach ($menu as $c1_e) {
                    if (is_array($c1_e) && isset($c1_e[2])
                        && (string) $c1_e[2] === (string) $c1_eltern) {
                        $c1_dach = $c1_sauber($c1_e[0] ?? '');
                        break;
                    }
                }
            }

            if ($c1_dach === '' || $c1_dach === $c1_kind) {
                return $c1_kind;   /* Ein Satellit ohne Dach nennt nur sich. */
            }
            /* Der Pfeil steht GENAU HIER, einmal im Haus. Er braucht keine
               Konstante: diese Funktion ist die einzige Stelle, die einen
               Pfad baut — und eine Konstante fuer einen Wert mit einem
               einzigen Verwender ist eine zweite Stelle mit gutem Ruf. */
            /* Das Zeichen selbst, nicht seine Schreibweise: in einfachen
               Anfuehrungszeichen ist \u{...} kein Escape, sondern Text.
               Mein erster Entwurf haette „Bismarck \u{2192} Structure"
               in die Saetze geschrieben. */
            return $c1_dach . ' → ' . $c1_kind;
        }
    }

    return '';
}

/* v0.6.9: DAS BRIEFSYMBOL — an einer Stelle, für alle Tabellen.

   Christians Befund: Im Mesh steht der Briefumschlag, in der Freundesliste
   fehlte er. Dieselbe Auskunft, zwei Tabellen, eine davon ohne.

   Und der zweite Teil seines Befunds ist der feinere: WO KEIN BRIEF IST, MUSS
   TROTZDEM PLATZ SEIN. Sonst rutschen alle Symbole der Zeile nach links, und
   Zeilen mit und ohne Adresse stehen versetzt untereinander. Die Lücke ist
   deshalb keine Auslassung, sondern ein Zeichen in Hintergrundfarbe: gleiche
   Breite, gleicher Abstand, unsichtbar.

   Nur der SysOp sieht die Adresse — wie beim Senden. Fuer alle anderen ist
   auch die Luecke da, damit die Ausrichtung ueberall gleich ist. */
function cluster_one_mail_icon_cell(array $peer): string {
    $adr = cluster_one_is_sysop() ? trim((string) ($peer['sysop_mail'] ?? '')) : '';
    $luecke = '<span aria-hidden="true" style="display:inline-block;width:18px;margin-right:.4em;vertical-align:middle;"></span>';
    if ($adr === '') {
        return $luecke;
    }
    $ts    = (int) ($peer['sysop_mail_ts'] ?? 0);
    $titel = sprintf(
        /* translators: 1: e-mail address, 2: date */
        __('The operator of this node gave this address on %2$s: %1$s', 'cluster-one-bismarck'),
        $adr,
        $ts > 0 ? date_i18n(get_option('date_format'), $ts) : '—'
    );
    return '<a href="' . esc_url('mailto:' . $adr) . '"'
         . ' style="text-decoration:none;color:#2271b1;margin-right:.4em;vertical-align:middle;"'
         . ' title="' . esc_attr($titel) . '"'
         . ' aria-label="' . esc_attr__('Write an e-mail to that node’s operator', 'cluster-one-bismarck') . '">'
         . '<span class="dashicons dashicons-email-alt" aria-hidden="true" style="font-size:18px;width:18px;height:18px;"></span>'
         . '</a>';
}

/* v0.6.9: DUNBAR — die Zahl, die sagt, wie voll es schon ist.

   Christians Wunsch: ueber der Freundesliste steht "1 Knotenpunkt von 150
   moeglichen (Dunbar)".

   Warum die Zahl hier eine Aussage ist und keine Willkuer: Robin Dunbars
   Schaetzung fuer die Zahl stabiler sozialer Beziehungen, die ein Mensch
   halten kann. Cluster One zaehlt allerdings KNOTEN, nicht Menschen — hinter
   einem Knoten koennen viele stehen. Die Zahl ist deshalb eine ANLEIHE, kein
   Naturgesetz, und genau deshalb steht sie sichtbar da statt still zu wirken.

   HEUTE ist es eine Anzeige, keine Sperre. Ob sie spaeter eine wird, ist eine
   eigene Entscheidung: Eine Grenze, die man nicht ueberschreiten kann, ist
   etwas anderes als eine Zahl, die einen zum Nachdenken bringt. */



/* v0.7.3: LÖSCHUNG FÜR MENSCHEN OHNE WORDPRESS-KONTO.

   Das WP-Werkzeug aus 0.6.3 arbeitet ueber eine E-Mail-Adresse — externe
   Menschen haben keine. Der SysOp konnte ihren Eintrag zwar entfernen, aber
   das war eine LISTENPFLEGE, keine Loeschung: Ihre Kennung (ext_<id>) blieb in
   Sichtbarkeits- und Teilnehmerlisten stehen, und niemand hielt fest, dass
   geraeumt wurde.

   Jetzt derselbe Weg wie bei einem WP-Konto: Verweise raus, Ereignis ohne
   Namen. Der Unterschied zu wp:N ist nur, dass eine externe Person nichts
   geschrieben haben KANN — sie hat kein Konto. Es gibt also nichts zu
   anonymisieren, nur etwas zu entfernen. */
function cluster_one_erase_external(string $id): int {
    if ($id === '') {
        return 0;
    }
    /* v0.9.30: Hier stand `'ext_' . $id`. Die Kennung TRAEGT das Praefix aber
       schon — sie wird oben als 'ext_' . bin2hex(...) erzeugt, und der Aufrufer
       uebergibt genau diesen Schluessel. Gesucht wurde also 'ext_ext_...' und
       gefunden nichts: die Verweise blieben in visible_to und participants
       stehen, exakt der Zustand, den diese Funktion beheben sollte. Der alte
       Zeuge sah es nicht, weil seine Welt den Schluessel 'h_fix' benutzte — so
       heisst keine echte Kennung. Wer eine Kennung prueft, muss eine echte
       nehmen; sonst prueft er seine eigene Erfindung. */
    $key = $id;
    $n   = 0;

    $rooms  = cluster_one_get_rooms();
    $rdirty = false;
    foreach ($rooms as $rid => $r) {
        foreach (array('participants', 'visible_to') as $liste) {
            if (isset($r[$liste]) && is_array($r[$liste]) && in_array($key, $r[$liste], true)) {
                $rooms[$rid][$liste] = array_values(array_diff($r[$liste], array($key)));
                $rdirty = true;
                $n++;
            }
        }
    }
    if ($rdirty) { cluster_one_save_rooms($rooms); }

    $threads = cluster_one_get_forum_threads();
    $tdirty  = false;
    foreach ($threads as $tid => $t) {
        if (isset($t['visible_to']) && is_array($t['visible_to']) && in_array($key, $t['visible_to'], true)) {
            $threads[$tid]['visible_to'] = array_values(array_diff($t['visible_to'], array($key)));
            $tdirty = true;
            $n++;
        }
    }
    if ($tdirty) { cluster_one_save_forum_threads($threads); }

    $ext = cluster_one_get_humans_external();
    if (isset($ext[$id])) {
        unset($ext[$id]);
        cluster_one_save_humans_external($ext);
        $n++;
    }

    /* Wie in 0.6.3: DAS EREIGNIS NENNT DIE PERSON NICHT. Ein Loeschprotokoll
       mit Namen hebt die Loeschung auf. */
    cluster_one_event_emit(array(
        'type'          => 'person_erased',
        'actor_id'      => 'gone',
        'summary_count' => $n,
    ), true);
    return $n;
}

/* v0.7.5: DIE ART EINES THEMAS — die Entsprechung zum Modus eines Raums.

   Christians Befund: Chat- und Forentabelle standen nebeneinander wie zwei
   verschiedene Tabellen. Vorn dasselbe, hinten dasselbe, in der Mitte das
   Eigene — so soll es aussehen. Dem Forum fehlten dafuer zwei Spalten:
   Sichtbarkeit (Themen HABEN sie, die Tabelle zeigte sie nur nicht) und die
   Art.

   Die Art stand bisher nur als kleines Abzeichen hinter dem Titel, und auch
   das nur bei Tagebuechern. */
function cluster_one_thread_kind_label(array $thread): string {
    if (cluster_one_thread_is_foreign($thread))  { return __('foreign', 'cluster-one-bismarck'); }
    if (cluster_one_thread_is_label($thread))    { return __('Label', 'cluster-one-bismarck'); }
    if (cluster_one_thread_is_wilde($thread))    { return 'Wilde'; }
    if (cluster_one_thread_is_wiki($thread))     { return 'Wiki'; }
    if (cluster_one_thread_is_ai_diary($thread)) { return __('AI diary', 'cluster-one-bismarck'); }
    if (!empty($thread['human_diary']))          { return __('Human diary', 'cluster-one-bismarck'); }
    return __('Thread', 'cluster-one-bismarck');
}

/* v0.9.41: HIER STAND cluster_one_thread_visibility_label().
   Die Spalte "Zutritt" ist in beiden Tabellen gegangen, und damit ihr
   einziger Aufrufer. Der Grund steht bei den Tabellenkoepfen: Nicht die
   Anzeige war falsch, sondern dass Raum und Thema zwei Felder fuer
   dieselbe Wahrheit fuehren. Kommt die gemeinsame Konfiguration, gibt es
   nichts mehr, was auseinanderlaufen koennte.
   Die Schwester cluster_one_room_purpose_label() ist mitgegangen. */

/* v0.7.8: Welche Rolle zu welchem Bereich passt — als VORSCHLAG, nicht als
   Regel. Die Art eines Themas wird bei der Anlage eingefroren; der Bereich
   laesst sich jederzeit wechseln. Wuerde der Bereich die Art bestimmen,
   aenderte ein Verschieben, WAS ein Thema ist.

   Nur Blaupausen-Bereiche stehen hier: Ein selbst benannter Bereich gehoert
   dem SysOp, und wir wissen nicht, was er darin vorhat. */
function cluster_one_area_role_hints(): array {
    $aus = array();
    /* v1.9.10 (08.09.2026) — DER BODEN GRIFF UNGERIEGELT IN DEN KRANZ.
       cluster_one_blueprint_areas() wohnt in kranz.php. kern.php reist in
       ALLEN Paketen; kranz.php nicht mehr, sobald Welle 7 schneidet.
       bismarck.php und feynman.php haben diesen Riegel laengst — sie
       haben mit der Abwesenheit gerechnet, der Boden nicht.
       Gefragt wird hier die FUNKTION und nicht Kasper: gebraucht wird
       ein einzelner Ruf mitten im Ablauf, keine Seite. */
    $c1_bp = function_exists('cluster_one_blueprint_areas')
        ? cluster_one_blueprint_areas()
        : array();
    foreach (array_keys($c1_bp) as $name) {
        $n = strtolower($name);
        if (strpos($n, 'wilde') !== false)         { $aus[$name] = 'wilde'; }
        elseif (strpos($n, 'wiki') !== false)      { $aus[$name] = 'wiki'; }
        elseif (strpos($n, 'tagebüch') !== false
             || strpos($n, 'tagebuech') !== false
             || strpos($n, 'diar') !== false)      { $aus[$name] = 'my_diary'; }
        else                                       { $aus[$name] = 'post'; }
    }
    return $aus;
}

/* v0.8.3: LANGE NAMEN KUERZEN, VOLLEN NAMEN AUF ZEIGEN.

   Christians Befund: "Bismarck-01 · Event: Erstes Treffen" ist viel laenger als
   "Claudes Thread" — die Spalte richtet sich nach dem laengsten Eintrag, und
   deshalb standen die Tabellen verschiedener Bereiche unterschiedlich breit.

   Und daraus folgt der zweite Teil: Erst DURCH das Kuerzen werden feste
   Spaltenbreiten moeglich. Bisher hatte ich table-layout:fixed abgelehnt, weil
   es bei langen Namen bricht — ein Name, der nicht mehr sprengen KANN, nimmt
   diesem Einwand die Grundlage. Zwei Befunde, eine Loesung. */
function cluster_one_kurz(string $text, int $max = 34): string {
    $t = trim($text);
    if ($t === '') {
        return '';
    }
    /* mb_strimwidth waere schoener, aber die mbstring-Erweiterung ist nicht
       ueberall da — und ein Helfer, der auf manchen Knoten fehlt, ist keiner. */
    if (function_exists('mb_strlen') && mb_strlen($t, 'UTF-8') > $max) {
        return mb_substr($t, 0, $max - 1, 'UTF-8') . '…';
    }
    if (!function_exists('mb_strlen') && strlen($t) > $max) {
        return substr($t, 0, $max - 1) . '…';
    }
    return $t;
}

/** Der gekuerzte Name mit dem vollen im title — sichtbar beim Darueberfahren.
   Wichtig: Der title steht IMMER, auch wenn nicht gekuerzt wurde. Sonst haette
   ein Eintrag mal einen Hinweis und mal keinen, und niemand wuesste, wann. */
function cluster_one_name_kurz(string $name, int $max = 34): string {
    return '<span title="' . esc_attr($name) . '">' . esc_html(cluster_one_kurz($name, $max)) . '</span>';
}

/* v0.8.5: DAS VERSCHIEBEN IN DEN SYSOP-BEREICH IST EIN TUEREREIGNIS.

   Christians eigener Satz aus der Themenkonfiguration: "Eine
   Sichtbarkeits-Aenderung ist ein Tuerereignis und wird bezeugt — kein stiller
   Wechsel, in keine Richtung."

   Ein Bereichswechsel ist normalerweise Sortierung und niemandes Sache. Bei
   DIESEM Bereich aendert er, wer etwas lesen darf — hinein heisst wegnehmen,
   heraus heisst offenlegen. Ohne Zeugnis waere genau das ein stiller Wechsel,
   und die Regel im Haus waere gebrochen.

   Aufzurufen ueberall dort, wo ein Container den Bereich wechselt. */
/* --- v0.9.36: DIE CHRONIK ------------------------------------------------
   Christians Wunsch: "Vielleicht brauchen wir noch irgendwo einen Ort fuer
   Systemmeldungen, die bleiben."

   Der Ort existiert seit 0.0.62 und fuellt sich seither — die Tabelle
   {prefix}cluster_one_events, auf Christians Main 161 Zeilen. Ihr einziger
   Leser war bis hierher der Spiegel, also die Diagnose fuer KIs. Deshalb
   stand sie im Befund als "59 INSERTs, kein SELECT". Das war falsch gezaehlt,
   aber der wahre Kern stimmte: SIE HATTE KEIN FENSTER FUER MENSCHEN.

   Christian hat selbst hingeschrieben, wofuer sie da ist — in der
   Begruendung, warum der Knoten-Reset sie NICHT mitloescht:

     "DAS EREIGNIS-LOG. Es ist der Grabstein. Ein Reset, der seine eigene
      Spur mitloescht, ist kein Protokoll, sondern ein Tagebuch des Taeters."

   Und der Loeschhandler sagt dasselbe: "das Zeugnis ueberlebt den Container".
   Die Chronik ist nichts Neues. Sie ist die Scheibe davor. */

/** Wer darf welche Zeile sehen? KONSERVATIV, mit Absicht.

   Jede Zeile traegt in 'visibility' die Klasse des Behaelters zum Zeitpunkt
   des Ereignisses ('all' | 'named' | ''). Der SysOp sieht alles. Alle anderen
   sehen NUR 'all'.

   Warum nicht auch die leeren: Ereignisse ohne Klasse sind Knoten-Ereignisse
   (sysop_area_in/out, blueprint_applied, node_reset). Ausgerechnet
   sysop_area_in wuerde verraten, DASS etwas in den unsichtbaren Bereich
   gewandert ist — und damit genau das, was der SysOp-Bereich verbirgt.
   Christians eigene Bereichsbeschreibung nennt das Verschieben ein
   Tuerereignis, "weil es aendert, wer etwas lesen darf".

   LIEBER ZU WENIG ZEIGEN ALS EINMAL ZU VIEL. Aufmachen laesst sich das
   spaeter mit einem Beleg; ein Leck laesst sich nicht zurueckholen. */
function cluster_one_chronik_darf_sehen(array $zeile): bool {
    if (cluster_one_is_sysop()) {
        return true;
    }
    return (string) ($zeile['visibility'] ?? '') === 'all';
}

/** Die letzten Zeilen der Chronik, bereits gefiltert. */
function cluster_one_chronik_zeilen(int $limit = 200): array {
    global $wpdb;
    if (!isset($wpdb) || !is_object($wpdb)) {
        return array();
    }
    $tbl = cluster_one_events_table();
    $limit = max(1, min(500, $limit));
    /* Nur persistierte Zeugnisse: 'ephemeral' wird gar nicht erst
       geschrieben, aber die Bedingung sagt es hin, damit spaetere
       Aufbewahrungsklassen (Artikel 4.3) hier nicht stillschweigend
       mitlaufen. */
    $roh = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- die Zeugnis-Tabelle des Hauses; jede Zeile ist ein Zeugnis und wird frisch gelesen, kein Objekt-Cache
        $wpdb->prepare(
            /* 1.19.8 (01.11.2026, Neo): Plugin Check (UnescapedDBParameter). Bis 1.19.7 stand der
               Tabellenname im Text:  FROM {$tbl}  — jetzt setzt prepare() ihn als %i ein (WP >= 6.2).
               Gleicher Name, gleiche Zeilen. */
            "SELECT event_id, type, container_type, container_id, actor_id, actor_label,"
            . " subject_id, visibility, summary_count, created_at FROM %i"
            . " WHERE retention_class = %s ORDER BY id DESC LIMIT %d",
            $tbl,
            'structural',
            $limit
        ),
        ARRAY_A
    );
    $aus = array();
    foreach ((array) $roh as $z) {
        $z = (array) $z;
        if (cluster_one_chronik_darf_sehen($z)) {
            $aus[] = $z;
        }
    }
    return $aus;
}

/** Ein Ereignistyp in einen Satz. Unbekannte Typen werden NICHT verschwiegen
   — sie erscheinen mit ihrem technischen Namen. Ein Protokoll, das nur zeigt,
   was es schon kennt, ist keins. */
function cluster_one_chronik_satz(string $typ): string {
    $m = array(
        'thread_created'           => __('Thread created', 'cluster-one-bismarck'),
        'thread_deleted'           => __('Thread deleted', 'cluster-one-bismarck'),
        'room_created'             => __('Chat created', 'cluster-one-bismarck'),
        'room_deleted'             => __('Chat deleted', 'cluster-one-bismarck'),
        'room_thread_linked'       => __('Chat and thread linked', 'cluster-one-bismarck'),
        'room_thread_unlinked'     => __('Chat and thread unlinked', 'cluster-one-bismarck'),
        'room_participant_added'   => __('Person added', 'cluster-one-bismarck'),
        'room_participant_removed' => __('Person removed', 'cluster-one-bismarck'),
        'room_ai_added'            => __('AI added', 'cluster-one-bismarck'),
        'room_ai_removed'          => __('AI removed', 'cluster-one-bismarck'),
        'visibility_changed'       => __('Visibility changed', 'cluster-one-bismarck'),
        'circle_changed'           => __('Circle changed', 'cluster-one-bismarck'),
        'viewer_added'             => __('Viewer added', 'cluster-one-bismarck'),
        'viewer_removed'           => __('Viewer removed', 'cluster-one-bismarck'),
        'sysop_area_in'            => __('Moved into the SysOp area', 'cluster-one-bismarck'),
        'sysop_area_out'           => __('Moved out of the SysOp area', 'cluster-one-bismarck'),
        'sysop_peek'               => __('SysOp looked in — witnessed', 'cluster-one-bismarck'),
        'diary_read'               => __('Diary read — witnessed', 'cluster-one-bismarck'),
        'preamble_set'             => __('Preamble set', 'cluster-one-bismarck'),
        /* 19.10.2026 (Neo), Boden 1.17.0: bis heute stand der abgelegte
           Site-Kontext als „Preamble set" da — richtig und zu wenig. Crockford
           legt ihn ueber den Vertrag cluster_one_thema_ablegen; platon.php
           meldet dafuer seit Bismarck 0.2.51 diesen Typ. */
        'site_context_laid'        => __('Site context laid into the thread', 'cluster-one-bismarck'),
        'wiki_edited'              => __('Wiki edited', 'cluster-one-bismarck'),
        'person_erased'            => __('Person anonymised', 'cluster-one-bismarck'),
        'node_reset'               => __('Node cleared', 'cluster-one-bismarck'),
        'blueprint_applied'        => __('Blueprint applied', 'cluster-one-bismarck'),
        'pilot_handed'             => __('Reactor handed over for a while', 'cluster-one-bismarck'),
        'pool_append'              => __('Laid into the cooling pool', 'cluster-one-bismarck'),
        'meeting_opened'           => __('Meeting opened to the network', 'cluster-one-bismarck'),
        'gregor_locked'            => __('Date locked', 'cluster-one-bismarck'),
        'gregor_event_deleted'     => __('Meeting deleted', 'cluster-one-bismarck'),
        /* 1.21.0 (02.11.2026, Neo): faust meldet auch das Freigeben eines Termins (gregor_unlocked, seit 1.0.x);
           das Wort fehlte, die Chronik zeigte den technischen Namen. */
        'gregor_unlocked'          => __('Date unlocked', 'cluster-one-bismarck'),
        'ground_shared'            => __('Ground conditions shared', 'cluster-one-bismarck'),
        /* 28.10.2026 (Neo), Boden 1.19.0: Mein Speicher (kern.php, cluster_one_speicher_*). */
        'storage_adopted'          => __('Storage taken over from the invite list', 'cluster-one-bismarck'),
        'storage_forgotten'        => __('Storage access forgotten', 'cluster-one-bismarck'),
    );
    /* 31.10.2026 (Neo), Boden 1.19.5 — DIE WOERTER DER PRODUKTE. Bis 1.19.4 stand
       diese Liste fest, und ein Produkt, das ein Tuerereignis bezeugen wollte,
       brauchte dafuer eine Boden-Runde (Crockford 0.5.33 und Post Dolly 0.3.59
       haben deshalb keins geschrieben). Jetzt haengt jedes Haus seine Woerter
       hier an, in seiner eigenen Domaene und seinem eigenen Katalog; der Boden
       gewinnt bei gleichem Schluessel. Fehlt das Haus, erscheint der technische
       Name – sichtbar, nicht falsch, wie bei jedem unbekannten Typ. */
    $m2 = apply_filters('cluster_one_chronik_woerter', $m);
    if (is_array($m2)) { $m = array_merge($m2, $m); }
    return $m[$typ] ?? $typ;
}

/** Die Chronik-Seite. Kein eigener Sidebar-Eintrag (parent=null) — sie ist
   seit v0.9.41 ein REITER VON KRANZ, hinter System. Vorher hing sie als
   Verweis auf der Mesh-Hauptseite; das war der Ort, an dem sie am wenigsten
   hingehoerte (siehe die Nachzaehlung in kleinrock.php). Die Sidebar bleibt
   dadurch bei sechs Zeilen, denn Kranz-Reiter sind versteckt registriert. */
function cluster_one_render_chronik_page(): void {
    cluster_one_page_gate();
    $zeilen = cluster_one_chronik_zeilen(200);
    $sysop  = cluster_one_is_sysop();
    ?>
    <div class="wrap">
        <h1><?php echo cluster_one_page_h1('Kranz'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
        <?php
        /* v1.9.10 (08.09.2026) — Post Dolly hat gemessen, dass diese Seite
           ohne kranz.php nicht beim Anmelden stirbt, sondern in ihrer
           achten Zeile: angemeldet, Funktion vorhanden, und dann fehlt
           die Reiterleiste.

               Eine Seite, die es gibt, und eine Seite, die laeuft, sind
               zwei verschiedene Messungen. Die zweite ist die, die dem
               Admin passiert.

           Die Chronik gehoert dem Boden und bleibt deshalb angemeldet.
           Ohne Kranz fehlt ihr die Reiterleiste und sonst nichts. */
        if (function_exists('cluster_one_render_kranz_tabs')) {
            cluster_one_render_kranz_tabs('cluster_one_chronik');
        }
        ?>
        <h2 style="margin-top:1em;"><?php esc_html_e('Chronicle', 'cluster-one-bismarck'); ?></h2>
        <p style="max-width:760px;opacity:.85;">
            <?php esc_html_e('What this node did to itself: doors that opened and closed, containers that came into being or went away, look-ins that were witnessed. Content is never here — only that something happened, when, and who did it. These entries outlive what they are about; a deletion leaves its own line behind.', 'cluster-one-bismarck'); ?>
        </p>
        <?php if (!$sysop) : ?>
            <p class="description" style="max-width:760px;">
                <?php esc_html_e('You are seeing the entries about openly visible containers. Anything that concerns a named circle or the SysOp area stays out — including the fact that it exists.', 'cluster-one-bismarck'); ?>
            </p>
        <?php endif; ?>
        <?php if (empty($zeilen)) : ?>
            <p style="opacity:.7;"><?php esc_html_e('Nothing recorded yet.', 'cluster-one-bismarck'); ?></p>
        <?php else : ?>
        <table class="widefat striped" style="max-width:1080px;margin-top:1em;table-layout:fixed;overflow-wrap:anywhere;">
            <thead>
                <tr>
                    <th style="width:17%;"><?php esc_html_e('When', 'cluster-one-bismarck'); ?></th>
                    <th style="width:30%;"><?php esc_html_e('What', 'cluster-one-bismarck'); ?></th>
                    <th style="width:20%;"><?php esc_html_e('Who', 'cluster-one-bismarck'); ?></th>
                    <th style="width:23%;"><?php esc_html_e('Where', 'cluster-one-bismarck'); ?></th>
                    <th style="width:10%;"><?php esc_html_e('Count', 'cluster-one-bismarck'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($zeilen as $z) : ?>
                <tr>
                    <td style="opacity:.85;"><?php echo esc_html(date_i18n(get_option('date_format') . ' H:i', (int) ($z['created_at'] ?? 0))); ?></td>
                    <td><?php echo esc_html(cluster_one_chronik_satz((string) ($z['type'] ?? ''))); ?></td>
                    <td style="opacity:.85;"><?php echo esc_html((string) ($z['actor_label'] ?? '') !== '' ? (string) $z['actor_label'] : (string) ($z['actor_id'] ?? '')); ?></td>
                    <td style="opacity:.7;"><?php echo esc_html((string) ($z['container_type'] ?? '')); ?></td>
                    <td style="opacity:.7;"><?php echo (int) ($z['summary_count'] ?? 0) > 0 ? esc_html((string) (int) $z['summary_count']) : '&nbsp;'; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p class="description" style="margin-top:1em;max-width:760px;">
            <?php esc_html_e('The most recent 200 entries. Nothing here can be edited or removed — not by the SysOp either; the node reset leaves this table standing on purpose.', 'cluster-one-bismarck'); ?>
        </p>
        <?php endif; ?>
    </div>
    <?php
}

/* ==========================================================================
   WAS FUER EIN DING IST DIESER THREAD?

   Fuenf Praedikate, am 25.08.2026 aus platon.php hierher gezogen. Der Anlass
   war gemessen: kern rief 21-mal nach oben in platon, und alle 21 Aufrufe
   waren genau diese fuenf. Ein Boden, der nach oben ruft, ist kein Boden.

   Sie beantworten eine Frage ueber den GEGENSTAND, nicht ueber das Forum.
   Bismarck, Kleinrock, Winsor und kern stellen sie genauso oft wie Platon.

   Reiner Umzug (Regel 2): jede Funktion und jeder Kommentar unveraendert.
   ========================================================================== */

/** Ist das ein hereingezogenes Fremdlabel? */
function cluster_one_thread_is_foreign(array $t): bool {
    return !empty($t['foreign']);
}

/* v0.5.3: WILDE — die Selbstdarstellung.

   Christians Reduktion, und sie war richtig: Wilde ist ein WIKI-Thread mit
   genau EINEM Unterschied. Im Wiki darf jeder aendern, weil der Text keinem
   gehoert. Bei Wilde darf nur die Entitaet aendern, DER er gehoert — Mensch
   oder KI. Alles andere traegt unveraendert: das Fassungsarchiv aus 0.5.1, die
   Verfasser-Zeile, die Diskussion darunter, das Einklappen.

   Deshalb ist ein Wilde-Thread auch technisch ein Wiki-Thread ('wiki' => true)
   plus ein Eigentuemer-Feld. Kein zweites System.

   DER SYSOP DARF HIER NICHT — und das ist die einzige Stelle im ganzen Haus,
   wo er das nicht darf. Christian hat es so entschieden, und es ist genau der
   Fall aus 0.5.0: in jemandes Namen sprechen. Eine Selbstdarstellung, die ein
   anderer aendern kann, ist keine.

   Der Eigentuemer wird bei der Anlage eingefroren, wie 'wiki' selbst. */
function cluster_one_thread_is_wilde(array $thread): bool {
    return (string) ($thread['wilde_owner'] ?? '') !== '';
}

function cluster_one_thread_is_wiki(array $thread): bool {
    // v0.0.69: Wiki ist eine THREAD-Eigenschaft, bei der Anlage eingefroren
    // (Eigenschaft-bei-Anlage-Regel — erledigt P1-11: kein Bereichs-Schalter
    // ändert je wieder rückwirkend das Verhalten bestehender Threads).
    if (array_key_exists('wiki', $thread)) {
        return !empty($thread['wiki']);
    }
    /* v0.9.42: Der Legacy-Rueckfall auf die Bereichsliste ist entfallen. Ein
       Thema ohne 'wiki'-Schluessel kann es nur auf einem Knoten geben, der nie
       einen Admin-Aufruf seit 0.0.69 hatte — die Einfrier-Migration unten
       laeuft sonst bei jedem. Bleibt trotzdem eines uebrig, ist FALSE die
       vorsichtige Antwort: Ein Wiki gibt JEDEM das Aenderungsrecht am Text,
       ein gewoehnliches Thema nicht. Im Zweifel das engere Recht. */
    return false;
}

/** Labels (v0.0.78, Design SysOp 16.07.2026): die AUSSENPROJEKTION eines
   Projekts. Ein Label ist ein eigener Thread (label=true), 1:1 an sein
   Projekt gebunden (label_for / linked_label — dasselbe Kanten-Muster wie
   Raum↔Thema). Er entsteht NUR über den Kopier-Knopf am Projekt-Post
   (SysOp: der Reaktorfahrer entscheidet, was den Knoten je verlassen soll —
   Dian-Test ohne Person im Satz). Status 'draft' = ungewiss: die Schwebe
   zwischen privat und veröffentlicht; die Netz-Freigabe ist der zweite,
   getrennte Akt (0.0.79). Nachträge sind append-only und bezeugt —
   Campingplatz-Regel: ein Label wird nie stillschweigend umgeschrieben,
   Änderungen stehen darunter, mit Grund. VERGLEICHSVERBOT als
   Bauvorschrift: Labels werden nirgends gezählt oder aggregiert angezeigt,
   und die Abstimmungsgeschichte des Gregor-Spiels wandert NIE hinein —
   nach außen geht das Ergebnis. */
function cluster_one_thread_is_label(array $thread): bool {
    return !empty($thread['label']);
}

/** KI-Tagebuch (v0.0.52): ein privates Thema, auf dessen Sichtbarkeits-
   Liste NULL Menschen stehen — nur eine oder mehrere KI-Kennungen. Genau
   der Fall, den cluster_one_thread_ai_allowed() oben als „0 Menschen,
   KI-Tagebuch-Fall" erlaubt, hier als eigene, benannte Prüfung, weil er
   jetzt einen eigenen Lese-Mechanismus bekommt. */
function cluster_one_thread_is_ai_diary(array $thread): bool {
    if (($thread['group_visibility'] ?? 'all') !== 'named') {
        return false;
    }
    $ai_uids = cluster_one_ai_identity_user_ids();
    foreach ((array) ($thread['visible_to'] ?? array()) as $vid) {
        $uid = (int) str_replace('wp_', '', (string) $vid);
        if ($uid > 0 && !in_array($uid, $ai_uids, true)) {
            return false; // ein Mensch auf der Liste — kein reines Tagebuch
        }
    }
    return true;
}


/** v0.9.35: IST DAS EIN TAGEBUCH? Der Test stand bisher sechsmal woertlich
   im Code als Zweiteiler (ai_diary ODER human_diary) — hier bekommt er
   einen Namen, damit die Regel darunter einen Ort hat. Die sechs
   Bestandsstellen bleiben vorerst unberuehrt; sie umzustellen ist ein
   eigener, angesagter Zug. */
function cluster_one_thread_is_diary(array $thread): bool {
    return cluster_one_thread_is_ai_diary($thread) || !empty($thread['human_diary']);
}

/** v0.9.35: EIN TAGEBUCH BLEIBT, WO ES IST. Christians Entscheidung vom
   19.08.2026 — weder Mensch noch KI reisen mit ihrem Tagebuch.

   Der Anlass war die Frage, wem eine Wiki gehoert, wenn ihr Thema den Bereich
   wechselt. Zieht ein Menschen-Tagebuch in einen sichtbaren Bereich, wuerde
   die Schreibberechtigung ans Kollektiv fallen — und ploetzlich duerfte jeder
   in das schreiben, was ein Mensch in sein Tagebuch geschrieben hat. Das ist
   derselbe Fall wie im Vorzimmer (bismarck.php, Stelle NICHT GEMESSEN —
   die Nummer 817 war einmal richtig und ist es heute vermutlich nicht;
   wer sie braucht, sucht am Wortlaut): Nicht der Umziehende
   gibt sein Vertrauen preis, sondern der, der frueher hineingeschrieben hat.

   DER FALL WIRD NICHT BEHANDELT, SONDERN ABGESCHAFFT. Ein Tagebuch, das
   seinen Bereich nie verlaesst, kann seinen Eigentuemer nie verlieren — und
   jeder weiss, was ihn in einem Bereich erwartet.

   Bis 0.9.34 liess sich ein Tagebuch ueber die Themen-Konfiguration
   herausschieben; der Zeuge hat es vorgefuehrt. */
function cluster_one_thread_may_change_area(array $thread, string $neu): bool {
    if ((string) ($thread['area'] ?? '') === $neu) {
        return true; // kein Wechsel — Speichern ohne Umzug bleibt erlaubt
    }
    return !cluster_one_thread_is_diary($thread);
}

function cluster_one_area_move_witness(string $ctype, string $cid, string $alt, string $neu): void {
    $rein  = $neu === cluster_one_sysop_area() && $alt !== cluster_one_sysop_area();
    $raus  = $alt === cluster_one_sysop_area() && $neu !== cluster_one_sysop_area();
    if (!$rein && !$raus) {
        return;
    }
    cluster_one_event_emit(array(
        'type'           => $rein ? 'sysop_area_in' : 'sysop_area_out',
        'container_type' => $ctype,
        'container_id'   => $cid,
        'actor_id'       => cluster_one_actor_id_human(),
    ), true);
}

/* v0.8.7: EIN RAUM, IN DEM ES KEINEN ZWEITEN MENSCHEN GIBT.

   Christians Befund aus der ersten echten Testrunde: Im Tagebuch-Chat stand
   Werkzeug fuer eine Lage, die es dort per Definition nicht gibt.

     - Die REAKTORFAHRER-UEBERGABE hat kein Ziel. Uebergeben wird an einen
       Menschen; im Guru-Meditation-Modus ist per Definition nur einer da.
     - RUNDEN ordnen, wer wann spricht, wenn mehrere Menschen mitreden. Bei
       einem Menschen ordnen sie nichts.
     - "Du bist allein hier" ist irrefuehrend: Die KIs sind ja da. Richtig ist
       "Hier ist kein weiterer Mensch" — und genau das ist der Modus.

   Der Modus WEISS das alles schon; die Leiste hat ihn nur nie gefragt. */
function cluster_one_room_solo_human(array $room): bool {
    /* v0.9.51: Hier stand `$room['mode']` — also der ZWISCHENSPEICHER, nicht
       die Wahrheit. Genau das war der Fund vom 20.08.: Das Feld wurde nur beim
       Speichern der Konfiguration aufgefrischt, und zwei von acht Raeumen
       trugen ein veraltetes Etikett. Eine Leiste, die auf Grund einer
       veralteten Zahl erscheint oder verschwindet, ist schlimmer als keine. */
    if (cluster_one_room_derived_mode($room) === 'guru_meditation') {
        return true;
    }
    /* v0.9.1: DAS MODUS-KRITERIUM WAR ZU ENG — Christians Befund.

       In 0.8.7 fragte diese Funktion nur nach dem Modus. Sein "Claudes Chat"
       ist aber ein TAGEBUCH-CHAT und traegt einen anderen Modus; die Leiste
       stand dort weiter, obwohl auch dort per Definition kein zweiter Mensch
       ist: Ein KI-Tagebuch gehoert genau EINER KI, und der Nebenkanal daneben
       ist die Verhandlung zwischen ihr und dem SysOp.

       Ein Modus ist eine WAHL des SysOps; die Kopplung an ein KI-Tagebuch ist
       eine TATSACHE. Die Tatsache ist das bessere Kriterium — sie gilt auch
       dann, wenn jemand den Modus anders gesetzt hat. */
    $tid = (string) ($room['linked_thread'] ?? '');
    if ($tid === '') {
        return false;
    }
    $threads = cluster_one_get_forum_threads();
    return isset($threads[$tid]) && cluster_one_thread_is_ai_diary($threads[$tid]);
}



/** Der Modus, den ein Raum aus seiner Lage HAT — unabhaengig davon, was
   eingestellt ist. Leer, wenn sich nichts ableiten laesst. */
function cluster_one_room_derived_mode(array $room): string {
    /* v0.9.2: KEIN MODUS WIRD MEHR GEWAEHLT — alle ergeben sich.

       Christians Befund, der den Ausschlag gab: Der Modus erreicht die KI
       ueberhaupt nicht. Er steht in keinem System-Prompt und in keiner
       Praeambel — er ist ein Etikett fuer Menschen. Funktional wirkte genau
       einer (Night-Flight setzte humans_only), und das lief RUECKWAERTS: Der
       Name setzte die Tatsache, statt sie zu beschreiben.

       Damit kann ein Etikett nichts Falsches mehr behaupten, weil es niemand
       setzt. Die Reihenfolge ist die der Bestimmtheit: Was fest ist, schlaegt
       was veraenderlich ist.

       POPPER ist entfallen. Er war der einzige Modus, der eine ABSICHT
       ausdrueckte statt einer Tatsache — und der falsche Ort dafuer: Der
       Modus erreicht die KI nie, die Praeambel schon. Ein Popper-Raum ohne
       Popper-Praeambel war immer nur ein Schild an der Tuer. Die Idee wandert
       zu den Praeambel-Bausteinen (Christians und mein Vorschlag, noch nicht
       gebaut). */

    // 1. Terminal — die haerteste Tatsache: hier haengt ein Tagebuch dran.
    $tid = (string) ($room['linked_thread'] ?? '');
    if ($tid !== '') {
        $threads = cluster_one_get_forum_threads();
        if (isset($threads[$tid])) {
            /* Gilt fuer BEIDE Tagebuch-Arten — Christians Punkt: Mensch- wie
               KI-Tagebuch, und der SysOp-Fall ist ein Mensch-Tagebuch.
               Gemeinsam ist ihnen, dass niemand ausser dem Eigentuemer an den
               Thread kommt. */
            if (cluster_one_thread_is_ai_diary($threads[$tid]) || !empty($threads[$tid]['human_diary'])) {
                return 'terminal';
            }
            if (cluster_one_thread_is_wiki($threads[$tid])) {
                return 'wiki';
            }
        }
    }

    // 2. Babel Fish — sagte immer schon nur, dass daneben ein Uebersetzer steht.
    if (trim((string) ($room['translator'] ?? '')) !== '') {
        return 'babel_fish';
    }

    // 3. Der Rest ergibt sich aus der Besetzung.
    /* v0.9.4: DIE MENSCHEN STEHEN NICHT IN 'participants'. Christians
       Spiegel-Dump vom echten Knoten: Fuenf von acht Raeumen haben
       participants = [], obwohl Leute drin sind — die stehen in visible_to.
       Sein Test-Chat SysOp trug sechs Menschen in visible_to und eine leere
       participants-Liste; meine Zaehlung sagte "ein Mensch" und leitete Guru
       Meditation ab.

       Richtig gezaehlt wird nach der Sichtbarkeit, denn die entscheidet, wer
       hinein darf:
         group_visibility 'all'   -> alle Menschen des Knotens (Weisse Liste)
         group_visibility 'named' -> die Menschen auf visible_to

       'participants' bleibt, was es ist: die Liste fuers Farbspiel, also wer
       aktiv mitspielt. Das ist eine andere Frage als wer hinein darf. */
    $kis = count((array) ($room['ais'] ?? array()));
    if ((string) ($room['group_visibility'] ?? 'all') === 'named') {
        $menschen = 0;
        $c1_ki_uids = array_map(function ($u) { return 'wp_' . (int) $u; }, cluster_one_ai_identity_user_ids());
        foreach ((array) ($room['visible_to'] ?? array()) as $c1_v) {
            if (!in_array((string) $c1_v, $c1_ki_uids, true)) {
                $menschen++; // KI-Kennungen zaehlen nicht als Mensch
            }
        }
    } else {
        $menschen = count(cluster_one_get_wp_humans());
    }

    /* LEER BLEIBT LEER — und heisst Nemo. Ein frisch angelegter Raum hat
       niemanden; ohne diesen Zweig waere er sofort "Night-Flight" (Menschen
       ohne KI) und behauptete eine Arbeitsweise, wo nur Leere ist. Nemos
       eigene Beschreibung passt genau darauf: keine besondere Arbeitsweise,
       und der Name sagt, dass es keine gibt. */
    if ($menschen === 0 && $kis === 0) {
        return 'nemo';
    }
    if ($kis === 0) {
        return 'night_flight';   // Menschen ohne KI
    }
    if ($menschen <= 1) {
        return 'guru_meditation'; // ein Mensch, beliebig viele KIs
    }
    return 'bismarck';            // Eddington — Slug seit 0.6.1 unveraendert
}

/* v0.9.6: WAS NIE EIN LABEL WERDEN KANN — Christians Regel.

   Sie ersetzt das Feld 'visibility' (privat/ungewiss), das beim Anlegen jedes
   Raums abgefragt wurde und an KEINER Stelle im Plugin abgefragt wurde. Sein
   urspruenglicher Impuls war richtig: Leute sollen sicher sein koennen, dass
   bestimmte Dinge den Knoten nie verlassen. Nur war das Feld ein Versprechen
   ohne Deckung — und es entstand vor der Blaupausen-Struktur und vor der
   Trennung von Label und Bismarck.

   Jetzt folgt die Sicherheit aus der SACHE statt aus einem Haken:

     TAGEBUECHER   — beide Arten. Ein Tagebuch gehoert dem, der es fuehrt;
                     ein Label reist zu fremden Knoten. Das schliesst sich aus.
     WILDE/JOHARI  — eine Selbstdarstellung ist ein Profil. Christians Linie:
                     Profile werden spaeter in der App oeffentlich, nie im
                     Plugin. Im Plugin gibt es hoechstens ein Profil des
                     KNOTENS, und auch das nur auf Pull.
     SYSOP-BEREICH — dort wird getestet. Wer daraus etwas veroeffentlichen
                     will, VERSCHIEBT es zuerst heraus — und das ist seit
                     0.8.5 ein bezeugter Akt. Kein Verbot, ein Umweg mit
                     Zeugnis.
     FREMDE        — galt schon vorher: ein fremdes Projekt bleibt, wie sein
                     Autor es schrieb.
     LABELS SELBST — ein Label von einem Label waere ein Spiegelkabinett.

   Die drei ersten sind ROLLE oder BEREICH, nicht Wahl: Man kann sie nicht
   versehentlich falsch setzen. */
function cluster_one_thread_can_become_label(array $thread): bool {
    if (cluster_one_thread_is_foreign($thread) || cluster_one_thread_is_label($thread)) {
        return false;
    }
    if (cluster_one_thread_is_ai_diary($thread) || !empty($thread['human_diary'])) {
        return false;
    }
    if (trim((string) ($thread['wilde_owner'] ?? '')) !== '') {
        return false;
    }
    if (cluster_one_in_sysop_area($thread)) {
        return false;
    }
    return true;
}

/** Warum nicht — als Satz fuer die Oberflaeche. Leer, wenn es geht. */
function cluster_one_label_block_reason(array $thread): string {
    if (cluster_one_thread_is_ai_diary($thread) || !empty($thread['human_diary'])) {
        return __('A diary never becomes a label — it belongs to whoever keeps it.', 'cluster-one-bismarck');
    }
    if (trim((string) ($thread['wilde_owner'] ?? '')) !== '') {
        return __('A self-portrayal never becomes a label — profiles stay on this node.', 'cluster-one-bismarck');
    }
    if (cluster_one_in_sysop_area($thread)) {
        return __('Nothing leaves the SysOp area directly. Move it out first — that move is recorded.', 'cluster-one-bismarck');
    }
    return '';
}


/* v0.9.7: EIN PROJEKT LIEGT AN EINEM ORT.

   Christians Befund: Beide Konfigurationen setzen 'area' nur bei sich selbst.
   Topft man den Chat um, bleibt sein Thema zurueck — dasselbe Projekt steht
   dann in zwei Bereichen.

   Und das ist gefaehrlicher als unordentlich: Schiebt man den CHAT in den
   SysOp-Bereich, bleibt sein THEMA sichtbar, mit allem, was darin steht. Das
   Tuerereignis feuert, die Sperre greift — aber nur fuer die eine Haelfte.

   Diese Funktion zieht die andere mit, egal von welcher Seite man anfasst, und
   bezeugt den Zug auch dort. Sie laeuft NACH dem Speichern der angefassten
   Seite, damit sie deren neuen Bereich sieht. */
function cluster_one_area_follow_partner(string $ctype, string $cid, string $neu): void {
    if ($ctype === 'room') {
        $rooms = cluster_one_get_rooms();
        $tid   = isset($rooms[$cid]) ? (string) ($rooms[$cid]['linked_thread'] ?? '') : '';
        if ($tid === '') {
            return;
        }
        $threads = cluster_one_get_forum_threads();
        if (!isset($threads[$tid]) || (string) ($threads[$tid]['area'] ?? '') === $neu) {
            return;
        }
        cluster_one_area_move_witness('thread', $tid, (string) ($threads[$tid]['area'] ?? ''), $neu);
        $threads[$tid]['area'] = $neu;
        cluster_one_save_forum_threads($threads);
        return;
    }
    $threads = cluster_one_get_forum_threads();
    $rid     = isset($threads[$cid]) ? (string) ($threads[$cid]['linked_room'] ?? '') : '';
    if ($rid === '') {
        return;
    }
    $rooms = cluster_one_get_rooms();
    if (!isset($rooms[$rid]) || (string) ($rooms[$rid]['area'] ?? '') === $neu) {
        return;
    }
    cluster_one_area_move_witness('room', $rid, (string) ($rooms[$rid]['area'] ?? ''), $neu);
    $rooms[$rid]['area'] = $neu;
    cluster_one_save_rooms($rooms);
}

/* v0.9.7: Themen, die noch keinen Raum haben — fuer die Paarung beim Anlegen.

   Christians Einschraenkung ist keine Kosmetik: Die Kopplung ist 1:1, und wer
   ein bereits gepaartes Thema waehlt, ENTZIEHT dem alten Raum kommentarlos
   seine Verknuepfung. Sie gar nicht erst anzubieten verhindert das, statt es
   hinterher zu bezeugen.

   Fremde Labels und Labels selbst bleiben draussen: Ein Label ist die
   Aussenprojektion eines Projekts, kein Projekt mit Konsole. */
function cluster_one_unpaired_threads(): array {
    $aus = array();
    foreach (cluster_one_get_forum_threads() as $tid => $t) {
        if (trim((string) ($t['linked_room'] ?? '')) !== '') {
            continue;
        }
        if (cluster_one_thread_is_foreign($t) || cluster_one_thread_is_label($t)) {
            continue;
        }
        if (cluster_one_container_is_archived($t) || !cluster_one_group_visibility_can_view($t)) {
            continue;
        }
        $aus[(string) $tid] = $t;
    }
    return $aus;
}

/* v0.9.8: Raeume, die noch kein Thema haben — das Gegenstueck zu
   cluster_one_unpaired_threads(). Dieselbe Begruendung: Die Kopplung ist 1:1,
   und wer einen bereits gepaarten Raum waehlt, entzieht dem alten Thema
   kommentarlos seine Verknuepfung. */
function cluster_one_unpaired_rooms(): array {
    $aus = array();
    foreach (cluster_one_get_rooms() as $rid => $r) {
        if (trim((string) ($r['linked_thread'] ?? '')) !== '') {
            continue;
        }
        if (cluster_one_container_is_archived($r) || !cluster_one_group_visibility_can_view($r)) {
            continue;
        }
        $aus[(string) $rid] = $r;
    }
    return $aus;
}

/* v0.9.9: DAS TERMINAL ENTSTEHT MIT DEM TAGEBUCH — Christians Ein-Klick.

   Bisher: Thema mit der Rolle Tagebuch anlegen, dann in der Konfiguration auf
   "Vorzimmer anlegen". Zwei Schritte, und der zweite war leicht zu vergessen —
   genau so blieben seine beiden Tagebuch-Chats offen, bis 0.9.5 sie beim
   Verknuepfen verengte.

   Und es gibt keinen Fall, in dem man ein Tagebuch OHNE Konsole will: Ein Buch
   ohne Feder. Also entsteht sie mit.

   Der Code stand schon da, nur festgewachsen im Handler
   admin_post_cluster_one_antechamber_create. Hier ist er herausgeloest, damit
   ihn beide Seiten rufen koennen — und er gilt jetzt fuer BEIDE Tagebuch-Arten:
   Das KI-Tagebuch bekam bisher keins, obwohl seine Konsole der Ort ist, an dem
   ueberhaupt mit der KI verhandelt wird.

   Gibt die neue Raum-ID zurueck, oder '' wenn nichts zu tun war. */
function cluster_one_create_terminal_for_diary(string $tid): string {
    $threads = cluster_one_get_forum_threads();
    if (!isset($threads[$tid])) {
        return '';
    }
    $t = $threads[$tid];
    $ist_tagebuch = cluster_one_thread_is_ai_diary($t) || !empty($t['human_diary']);
    if (!$ist_tagebuch || trim((string) ($t['linked_room'] ?? '')) !== '') {
        return ''; // kein Tagebuch, oder es hat laengst eine Konsole
    }
    $rooms = cluster_one_get_rooms();
    $rid   = bin2hex(random_bytes(8));
    $rooms[$rid] = array(
        'id'               => $rid,
        /* translators: %s: title of the diary */
        'name'             => sprintf(__('%s — terminal', 'cluster-one-bismarck'), (string) ($t['title'] ?? '')),
        /* v0.9.34: 'visibility' entfallen, s. bismarck.php. */
        'created'          => time(),
        'area'             => (string) ($t['area'] ?? ''),
        'ais'              => array(),
        'mode'             => 'terminal',
        'translator'       => '',
        'bridge_type'      => '',
        'bridge_ref'       => '',
        'participants'     => array(),
        'outside_view'     => 'invisible',
        'humans_only'      => !empty($t['human_diary']), // Kostenwahrheit: nur der SysOp ruft KIs
        'antechamber'      => true,
    );
    cluster_one_save_rooms($rooms);
    cluster_one_event_emit(array(
        'type' => 'room_created', 'container_type' => 'room', 'container_id' => $rid,
        'actor_id' => cluster_one_actor_id_human(),
    ), true);
    /* Ueber die Link-Funktion, nicht ins Feld: Sie schreibt das Tuerereignis
       UND vererbt die Gaesteliste des Tagebuchs (0.9.5). Damit ist die Konsole
       vom ersten Moment an so verschlossen wie das Buch. */
    cluster_one_set_room_thread_link($rid, $tid);
    return $rid;
}


/** Liegt fuer diese KI in diesem Raum schon ein offener Wunsch?
   Eine Bremse gegen zehn Systemnachrichten aus zehn Klicks — dieselbe
   Mechanik wie bei der Praeambel-Freigabe: einer, bis er verbraucht ist. */
function cluster_one_ai_wish_open(array $room, string $cid): bool {
    return count(cluster_one_ai_wish_names($room, $cid)) > 0;
}

/* v0.9.14: WER den Wunsch traegt, nicht wie viele — Christians Entscheidung
   nach Geminis Vorschlag.

   Geminis Mechanik war richtig: Mehrere haengen sich an DENSELBEN Wunsch,
   statt neue Zeilen zu erzeugen. Seine Vokabel nicht — er wollte einen
   ZAEHLER ("+3").

   Ein Zaehler ohne Nenner misst nichts: "+3" heisst bei vier Menschen fast
   alle, bei zwanzig fast niemand. Mit Nenner waere es ein Quorum, und dann
   waere es eine Abstimmung — die erzeugt eine unterlegene Seite. Ein SysOp,
   der ablehnt, uebergeht dann eine Mehrheit. DIESES HAUS BEZEUGT, ES URTEILT
   NICHT; ein Punktestand urteilt.

   Namen loesen beides und sagen sogar mehr: nicht wie viele es wollen,
   sondern WER. */
/* v0.9.33: EIN SPEICHER STATT ZWEI. Bis hierher lag der Wunsch in
   $room['ai_wishes'], und die blaue Lampe des Farbspiels las
   $room['fs']['wishes'] — zwei Schreibpfade auf dieselbe Wahrheit
   ("jemand moechte, dass diese KI antwortet"), P1-12.

   Die Folge war Christians Befund vom 19.08.2026: "die Systemmeldung
   scrollt nach oben". Sie scrollt, weil der Mechanismus, der BLEIBT, am
   anderen Knopf hing. Die Lampe aus 0.0.84 hat nie geleuchtet.

   fs['wishes'] gewinnt, weil es das allgemeinere Feld ist: Es traegt
   KI-Wuensche (Schluessel = Connector-Kennung) UND Menschen-Pings
   (Schluessel = wp_<id>) in derselben Lampensprache, und fs_mark_spoken()
   loescht es bereits beim Sprechen.

   ACHTUNG, SCHLUESSELFORMAT: ai_wishes trug Actor-IDs ("wp:1"),
   fs['wishes'] traegt Teilnehmer-Schluessel ("wp_1"). Wer nur den Speicher
   tauscht und die Formate nicht, baut sich denselben Fehler wie
   'ext_' . $id in 0.9.30 — gesucht wuerde etwas, das so nie geschrieben
   wird. Alle Aufrufstellen sind mit umgestellt. */
function cluster_one_ai_wish_names(array $room, string $cid): array {
    $w = (array) ($room['fs']['wishes'] ?? array());
    if (empty($w[$cid])) {
        return array();
    }
    /* Bestandsschutz: bis 0.9.13 stand dort ein Zeitstempel, kein Array. */
    if (!is_array($w[$cid])) {
        return array('');
    }
    return array_values(array_unique(array_map('strval', $w[$cid])));
}

/** Steht DIESE Person schon dahinter? Einer je Mensch — sonst waere es doch
   wieder ein Zaehler, den einer allein hochtreiben kann. */
function cluster_one_ai_wish_has(array $room, string $cid, string $actor): bool {
    return in_array($actor, cluster_one_ai_wish_names($room, $cid), true);
}

/** Der Wunsch ist verbraucht, sobald der SysOp diese KI hier ruft. */
function cluster_one_ai_wish_clear(string $room_id, string $cid): void {
    $rooms = cluster_one_get_rooms();
    if (!isset($rooms[$room_id]) || empty($rooms[$room_id]['fs']['wishes'][$cid])) {
        return;
    }
    unset($rooms[$room_id]['fs']['wishes'][$cid]);
    cluster_one_save_rooms($rooms);
}

/* v0.9.27: STUMMSCHALTEN IM CHAT — Christians Ersatz fuer die Kreis-Tiefe.

   Sein Argument gegen den Regler: Der einzige echte Fall fuer "nur lesend" ist
   ein Mensch, der gerade stoert. Das ist ein EINGRIFF IN EINEM MOMENT, kein
   Zustand, den man in einer Konfiguration einstellt — dort wird daraus ein
   Dauerzustand, den niemand mehr erklaert.

   Deshalb hier: neben der Person, im Chat, BEFRISTET. Die Frist ist dieselbe
   Vorsicht wie bei der Reaktorfahrer-Uebergabe — EIN VERGESSENES
   STUMMSCHALTEN DARF KEIN DAUERZUSTAND WERDEN. Der SysOp kann jederzeit
   frueher aufheben, und beides wird bezeugt. */
function cluster_one_muted_until(array $room, string $actor): int {
    $m = (array) ($room['muted'] ?? array());
    $bis = (int) ($m[$actor] ?? 0);
    return $bis > time() ? $bis : 0;
}

/** Ist diese Person hier gerade stummgeschaltet? Der SysOp nie — er koennte
   sich sonst selbst aussperren, und das Aufheben liegt bei ihm. */
function cluster_one_is_muted(array $room, string $actor): bool {
    if ($actor === 'wp:' . cluster_one_main_admin_user_id()) {
        return false;
    }
    return cluster_one_muted_until($room, $actor) > 0;
}

/* v0.9.28: EINE GAESTELISTE FUER DAS PAAR — Christians Entscheidung.

   "Wer im Chat ist, ist auch im Thread." Menschen halten sich in beiden auf,
   und manche lieber im einen als im anderen. Ein Projekt hat einen Kreis, nicht
   zwei.

   Sein Wiki-Argument dazu: Freiheit gilt INNERHALB des Kreises. Ein Unternehmen
   mit einer Wiki will den Austausch der Leute, die darin sitzen — nicht die
   Meinung von Rentnerin Gertrud. Das Wiki wird davon nicht unfreier.

   ABER DIE ROLLE SCHLAEGT DIE LISTE. Wo die Sichtbarkeit bei der Anlage
   eingefroren wurde, bleibt sie: Ein Tagebuch gehoert dem, der es fuehrt (sonst
   kaeme der SysOp ueber die Chat-Liste hinein — genau das Schloss aus 0.6.8
   waere weg). Eine Wilde gehoert ihrem Autor; die Liste regelt dort nur, wer
   darunter reden darf. Ein Label folgt seinem Status.

   Kein Sonderfall, sondern drei Anwendungen derselben Regel. */
function cluster_one_thread_visibility_is_role_fixed(array $thread): bool {
    return cluster_one_thread_is_ai_diary($thread)
        || !empty($thread['human_diary'])
        || cluster_one_thread_is_wilde($thread)
        || cluster_one_thread_is_label($thread)
        || cluster_one_thread_is_foreign($thread);
}

/** Traegt die Gaesteliste eines Raums auf sein Thema — wie der Bereich in
   0.9.7. Zwei Kopien statt einer Ableitung, weil can_view generisch ist und
   nicht weiss, ob es ein Thema oder einen Raum vor sich hat; dafuer wird der
   Zug wie jeder andere bezeugt. */
function cluster_one_sync_guests_to_thread(string $room_id): void {
    $rooms = cluster_one_get_rooms();
    $tid   = isset($rooms[$room_id]) ? (string) ($rooms[$room_id]['linked_thread'] ?? '') : '';
    if ($tid === '') {
        return;
    }
    $threads = cluster_one_get_forum_threads();
    if (!isset($threads[$tid]) || cluster_one_thread_visibility_is_role_fixed($threads[$tid])) {
        return; // die Rolle schlaegt die Liste
    }
    $gv = (string) ($rooms[$room_id]['group_visibility'] ?? 'all');
    $vt = array_map('strval', (array) ($rooms[$room_id]['visible_to'] ?? array()));
    if ((string) ($threads[$tid]['group_visibility'] ?? 'all') === $gv
        && array_map('strval', (array) ($threads[$tid]['visible_to'] ?? array())) === $vt) {
        return; // nichts zu tun
    }
    $threads[$tid]['group_visibility'] = $gv;
    $threads[$tid]['visible_to']       = $vt;
    cluster_one_save_forum_threads($threads);
    cluster_one_event_emit(array(
        'type'           => 'visibility_changed',
        'container_type' => 'thread',
        'container_id'   => $tid,
        'actor_id'       => cluster_one_actor_id_human(),
    ), true);
}

/* ============================================================
   KNOTEN-IDENTITAET (v0.9.55, Umzug aus kleinrock.php)
   Ein Knoten hat einen Namen und eine Adresse, BEVOR er irgendwen kennt.
   Diese vier standen seit dem Schnitt v0.0.100 im Netz-Organ, weil das
   Netz ihr erster Benutzer war — nicht, weil sie dorthin gehoerten.
   Byte-identisch umgezogen; Golden bezeugt, dass sich nichts geaendert hat.
   ============================================================ */

// --- Eigener Knoten ----------------------------------------------------
function cluster_one_node_id(): string {
    $v = cluster_one_config('CLUSTER_ONE_NODE_ID', 'node_id');
    return $v !== '' ? $v : __('unnamed (no identifier)', 'cluster-one-bismarck');
}

/* Stabile, symmetrische Kanten-Kennung aus dem gemeinsamen Token.

   NULL AUFRUFER — UND DAS IST EINE ENTSCHEIDUNG, KEIN VERSEHEN.

   Bis 0.9.79 stand sie an zwölf Stellen als Krücke: (string) ($x['edge'] ??
   cluster_one_edge_id($token)). Diese Krücken erfanden eine Kennung, wo im
   Datensatz keine stand — und die erfundene Zahl landete in versteckten
   Formularfeldern und Links. Golden hat es gemessen (eine Szene schrumpfte um
   64 Byte), nachdem vier Knoten-Abzüge bewiesen hatten, dass 12 von 12 Kanten
   ihr Feld wirklich tragen. Die Krücken sind in 0.9.80 gefallen.

   Übrig blieb die Funktion selbst. Christian hat am 26.08.2026 entschieden:
   BEHALTEN, als mathematisches Primitiv für Identität, neben
   cluster_one_node_id() und cluster_one_norm_url(). Sie erzeugt keine ID —
   sie berechnet einen Fingerabdruck aus einem Token, symmetrisch, damit beide
   Seiten einer Kante ohne Absprache denselben Wert bekommen.

   WER SIE ALS TOT LÖSCHT, LÖSCHT DAS PRIMITIV. Die Null ist kein Befund über
   ihren Wert, sondern über den Zustand nach dem Aufräumen. */
function cluster_one_edge_id(string $token): string {
    return substr(hash('sha256', $token), 0, 16);
}

/* --- Eigene Basis-URL (vor /wp-json), korrekt auch bei Unterverzeichnis --- */
function cluster_one_self_base_url(): string {
    $rest = untrailingslashit(get_rest_url(null, '')); // https://site/cms/wp-json
    if (substr($rest, -8) === '/wp-json') {
        $rest = substr($rest, 0, -8);
    }
    return $rest; // https://site/cms
}

/* --- URL normalisieren (stabiler Schlüssel der Karte) ---
 *
 * GABEL 0.9.69 (F7): BIS HIER NORMALISIERTE DIESE FUNKTION NICHTS.
 * esc_url_raw() validiert und kodiert; es kanonisiert nicht. Der einzige
 * Unterschied zur rohen Eingabe war der abschliessende Schraegstrich. Damit
 * waren fuenf Schreibweisen desselben Knotens fuenf Schluessel der Karte:
 *
 *     https://beispiel.de   https://Beispiel.de   https://beispiel.de:443
 *     https://beispiel.de/  https://beispiel.de/?x=1
 *
 * Drei Folgen: Der Selbst-Riegel in clean_pointer() griff nicht (ein Knoten
 * konnte sich unter fremder Schreibweise selbst aufnehmen und im Netz als
 * Fremder bewerben); die 500 fuellten sich ohne Angreifer; und der Bann war
 * mit einem Grossbuchstaben zu umgehen.
 *
 * NORMALISIERT WIRD NUR, WAS NACH RFC 3986 NACHWEISLICH GLEICH IST:
 *   Schema und Host klein, Standard-Port weg, Fragment weg, Query weg
 *   (eine Basis-URL aus home_url() traegt nie eine Query), Schraegstrich weg.
 *
 * NICHT NORMALISIERT WIRD 'www.'. In einer frueheren Empfehlung stand das
 * anders, und es war falsch: 'www.beispiel.de' und 'beispiel.de' sind zwei
 * Hostnamen im DNS und koennen zwei verschiedene Server sein. Wer sie
 * zusammenfasst, schickt token-bewehrte Aufrufe an eine Adresse, die nie
 * bewiesen wurde — genau der Fehler, den P0-6 in 0.0.61 geschlossen hat.
 * Zwei Zeilen in der Karte sind billiger als eine falsche Verschmelzung.
 * Fuer den BANN gilt bewusst etwas anderes, siehe cluster_one_bann_key(). */
function cluster_one_norm_url(string $url): string {
    $url = esc_url_raw(trim($url));
    if ($url === '') return '';
    $p = wp_parse_url($url);
    if (!is_array($p) || empty($p['host'])) {
        return untrailingslashit($url);   // unlesbar -> wie bisher, nie schlechter
    }
    $scheme = strtolower((string) ($p['scheme'] ?? 'https'));
    $host   = strtolower((string) $p['host']);
    $port   = isset($p['port']) ? (int) $p['port'] : 0;
    $std    = ($scheme === 'https' && $port === 443) || ($scheme === 'http' && $port === 80);
    $path   = untrailingslashit((string) ($p['path'] ?? ''));
    return $scheme . '://' . $host . (($port > 0 && !$std) ? ':' . $port : '') . $path;
}

/* ============================================================
   AKTEURS-ETIKETTEN (v0.9.55, Umzug aus platon.php)
   Ein Etikett fuer einen AKTEUR hat mit Themen nichts zu tun. Sie standen
   im Forum-Organ, weil das Forum sie zuerst brauchte — und der Kern hing
   dadurch am Forum: cluster_one_event_emit() ruft actor_label bei JEDEM
   Ereignis, also bei jeder Zeile der Chronik.
   Nur Darstellung, nie Rechte. Byte-identisch umgezogen.
   ============================================================ */

/** Anzeigelabel zu einer Actor-ID — nur Darstellung, nie Rechte. */
function cluster_one_actor_label(string $actor_id): string {
    if ($actor_id === 'system') {
        return 'System';
    }
    if ($actor_id === 'collective') {
        $cu = get_userdata(cluster_one_collective_user_id());
        return $cu ? $cu->display_name : 'Collective';
    }
    if (strpos($actor_id, 'wp:') === 0) {
        /* 1.21.0 (02.11.2026, Neo): ein Lauf ohne angemeldeten Menschen (Cron, Besucher, die Wanderung beim ersten
           Request nach dem Aufspielen) hiess in der Chronik „wp:0" — der Knoten hat es mit sich selbst getan. */
        if ($actor_id === 'wp:0') { return __('the node itself', 'cluster-one-bismarck'); }
        $u = get_userdata((int) substr($actor_id, 3));
        return $u ? $u->display_name : $actor_id;
    }
    if (strpos($actor_id, 'ai:') === 0) {
        $ids = cluster_one_get_ai_identities();
        $cid = substr($actor_id, 3);
        if (isset($ids[$cid])) {
            $u = get_userdata((int) $ids[$cid]);
            if ($u) {
                return $u->display_name;
            }
        }
        return $cid;
    }
    return $actor_id;
}

/** Aktuelles Label der Ausschluss-Rolle (Default „Fremd", editierbar). */
function cluster_one_excluded_role_label(): string {
    $slug = cluster_one_excluded_role_slug();
    if (get_role($slug) === null) {
        return __('Foreign', 'cluster-one-bismarck');
    }
    global $wp_roles;
    if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }
    return isset($wp_roles->role_names[$slug]) ? (string) $wp_roles->role_names[$slug] : __('Foreign', 'cluster-one-bismarck');
}

/** Setzt NUR das Label (Anzeige in der WP-Rollentabelle) — der Slug bleibt
   für immer unverändert. Kein Umzug für bereits zugewiesene User nötig: sie
   „hatten" nie das alte Label, nur den Slug; das Label ist reine Anzeige. */
function cluster_one_set_excluded_role_label(string $label): void {
    global $wp_roles;
    if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }
    $slug = cluster_one_excluded_role_slug();
    if (!isset($wp_roles->roles[$slug])) {
        return;
    }
    $wp_roles->roles[$slug]['name'] = $label;
    $wp_roles->role_names[$slug]    = $label;
    update_option($wp_roles->role_key, $wp_roles->roles);
}

/** Forum-Rolle (bbPress) eines Users — eigenes, von der WP-Rolle
   unabhängiges Rollensystem, getrennt abgefragt, nicht vermischt (§4.1).
   Defensiv: bbPress-API-Details variieren je Version, daher
   function_exists()-Wächter statt harter Abhängigkeit. Leer, wenn
   bbPress inaktiv oder der User dort keine Rolle hat. */
function cluster_one_bbpress_role_label(int $user_id): string {
    if (!function_exists('bbp_get_user_role')) {
        return '';
    }
    $role = (string) bbp_get_user_role($user_id);
    if ($role === '') {
        return '';
    }
    if (function_exists('bbp_get_dynamic_roles')) {
        $roles = bbp_get_dynamic_roles();
        if (isset($roles[$role]['name'])) {
            return translate_user_role($roles[$role]['name']);
        }
    }
    return $role;
}

/** WP-Rollen-Slug -> menschenlesbares, übersetztes Label. translate_user_role()
   erwartet den ECHTEN Anzeigenamen (z. B. "Administrator"), nicht den Slug
   (z. B. "administrator") — den holt sich diese Funktion korrekt aus
   $wp_roles->role_names, statt den Slug selbst zu übersetzen. */
function cluster_one_wp_role_label(string $slug): string {
    global $wp_roles;
    if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }
    if (isset($wp_roles->role_names[$slug])) {
        return translate_user_role($wp_roles->role_names[$slug]);
    }
    return $slug;
}

/** v1.3.3: Anzeigename EINES BENANNTEN Menschen — nicht des eingeloggten.
   cluster_one_human_speaker_label() fragt wp_get_current_user(); hier ist
   aber der GASTGEBER gemeint, und der sitzt meistens nicht am Bildschirm.
   Zwei Fragen, zwei Funktionen.

   Faellt auf „#12" zurueck, nie auf einen leeren Namen: eine Meldung, die
   niemanden nennt, ist die Meldung, die diesen Umbau ausgeloest hat. */
function cluster_one_ai_akteur_label(int $user_id): string {
    if ($user_id <= 0) {
        return '#0';
    }
    $u = get_userdata($user_id);
    if ($u && trim((string) $u->display_name) !== '') {
        return (string) $u->display_name;
    }
    return '#' . $user_id;
}

/** Anzeigename des sprechenden Menschen: der eingeloggte WP-User, nicht
   der Knoten (Kernfix Sub-Projekt Identitätsfrage). Fallback auf die
   Knoten-Kennung nur im theoretischen Randfall eines leeren Display-Namens. */
function cluster_one_human_speaker_label(): string {
    $u = wp_get_current_user();
    if ($u && $u->exists() && trim((string) $u->display_name) !== '') {
        return $u->display_name;
    }
    return cluster_one_node_id();
}

/** Vereinfachtes Rollen-Label fürs Kleinrock/Hallo-Chat (v0.0.42): nur zwei
   Stufen, „SysOp" oder „User" — nicht die volle WP-Rollentiefe, die
   für Bismarcks Menschen-Tabelle zählt. Andere Frage, andere Antwort. */
function cluster_one_kleinrock_role_label(int $user_id): string {
    return ($user_id === cluster_one_main_admin_user_id()) ? 'SysOp' : __('User', 'cluster-one-bismarck');
}

/** Zusammengesetzter Hallo-Chat-Autor (v0.0.42, Identität in Kleinrock):
   „{Mensch} ({Rolle}) von {Knoten}" — geht als EIN loser String ins
   author-Feld, genau wie bei Bismarck (§3 Identitätsfrage: Autor-Feld
   bleibt bewusst frei, keine erzwungene Struktur, wegen künftiger
   Fremd-Autoren über Brücken). Peers sehen exakt denselben String, den
   der schreibende Mensch selbst sieht — kein Zusatz-Parsing nötig. */
function cluster_one_kleinrock_speaker_label(): string {
    $u = wp_get_current_user();
    $name = ($u && $u->exists() && trim((string) $u->display_name) !== '') ? $u->display_name : cluster_one_node_id();
    $role = cluster_one_kleinrock_role_label(get_current_user_id());
    return sprintf('%s (%s) von %s', $name, $role, cluster_one_node_id());
}

/* ============================================================
   SELBSTGEHOSTETE KI UND DIE ZWEI FREIGABEN (v0.9.59, Umzug aus modi.php)
   Sie standen im Modus-Vokabular, weil sie dort zuerst gebraucht wurden.
   Mit ihnen hing 'modi' in JEDER Konfiguration — auch in Cluster One Light,
   das gar keine Raeume hat und also keine Modi kennt. Ein ganzes Organ als
   Anhaengsel einer einzigen Funktion.
   Sie gehoeren in den Kern: Wer zahlt, und wer darf.
   ============================================================ */

/** v0.9.47: EIGENE MASCHINE — die Kostenaussage des SysOps ueber eine KI.

   Christian am 20.08.2026: "Wenn ein SysOp eine selbstgehostete KI mitbringt,
   können wir das nachreichen ... Nur die dürfen in Gruppenchats für
   Übersetzungen zugelassen sein."

   WAS DAS FELD IST UND WAS NICHT. Es ist KEINE erkannte Eigenschaft — das
   Plugin kann nicht wissen, wo ein Modell laeuft, und der Connector sagt es
   nicht. Es ist die ERKLAERUNG DES SYSOPS, dass ihn Aufrufe an diese Kennung
   nichts extra kosten. Wer es bei einer bezahlten Schnittstelle setzt, zahlt;
   das ist sein Geld und sein Haken. Deshalb heisst es an der Oberflaeche auch
   nicht "kostenlos", sondern was es ist. Eine Oberflaeche, die eine Pruefung
   behauptet, die es nicht gibt, ist dieselbe Sorte Luege wie ein Knopf ohne
   Wirkung.

   WOZU ES DA IST. Bis heute gilt: nur der SysOp (und der befristet
   uebergebene Reaktorfahrer) darf eine KI ausloesen — die Kostenwahrheit aus
   0.9.13. Diese Regel hiess nie "nur der SysOp", sie hiess "NUR DER
   ZAHLENDE"; der SysOp war bloss immer der Zahlende. Wo niemand pro Aufruf
   zahlt, hat sie nichts mehr zu schuetzen. Dieses Feld ist die Stelle, an der
   der SysOp sagt, wo das der Fall ist.

   BENANNTE GRENZE: Selbstgehostet ist nicht gratis — Strom, Hardware, und
   jemand kann die Maschine haemmern. Das Feld sagt nur, dass der SysOp die
   Kosten fuer TRAGBAR haelt; eine Aufruf-Bremse ist eine eigene Frage und
   steht hier nicht.

   In 0.9.47 WIRKT das Feld noch nichts. Es wird gesetzt, gespeichert und
   angezeigt; wer es liest, kommt in 0.9.48 (Kleinrock). Ein Feld ohne Wirkung
   ist normalerweise ein Versprechen ohne Deckung — deshalb sagt der Text
   daneben ausdruecklich, dass es noch nichts freischaltet. */
function cluster_one_get_ai_selfhosted(): array {
    $a = get_option('cluster_one_ai_selfhosted', array());
    return is_array($a) ? array_map('strval', $a) : array();
}

/** Traegt diese Kennung die Kostenaussage? Zweite Bedingung: Sie muss
   ueberhaupt eine Kennung SEIN — ein Haken an einem Anbieter ohne
   WP-Konto koennte nie jemanden sprechen lassen (dasselbe Haekchen-Gate
   wie bei den Raeumen). */
function cluster_one_ai_is_selfhosted(string $cid): bool {
    if ($cid === '' || !in_array($cid, cluster_one_get_ai_selfhosted(), true)) {
        return false;
    }
    $ids = cluster_one_get_ai_identities();
    return isset($ids[$cid]) && (int) $ids[$cid] > 0;
}

/** Gibt es ueberhaupt eine? Die Frage, an der spaetere Schalter haengen —
   und die entscheidet, ob ein Feld ausgegraut steht oder wirkt. */
function cluster_one_has_selfhosted_ai(): bool {
    foreach (cluster_one_get_ai_selfhosted() as $cid) {
        if (cluster_one_ai_is_selfhosted((string) $cid)) {
            return true;
        }
    }
    return false;
}

/** v0.9.48: DUERFEN BESCHIRMTE MIT DIESER KI UEBERSETZEN?

   Zwei Bedingungen, und beide muessen stimmen:
   1. Der SysOp hat den Schalter umgelegt (seine Entscheidung, sein Knoten).
   2. Der GESETZTE Mesh-Uebersetzer traegt die Kostenaussage.

   Die zweite ist der eigentliche Riegel. Ein Schalter, der auch dann noch
   offen steht, wenn der SysOp den Uebersetzer spaeter auf eine bezahlte KI
   umstellt, waere die schlimmste Sorte Einstellung: eine, die einmal richtig
   war und seitdem still das Gegenteil tut. Deshalb wird nicht gespeichert,
   was gilt, sondern jedes Mal GEFRAGT — die Freigabe haengt am aktuellen
   Uebersetzer, nicht an dem von damals. */
function cluster_one_mesh_translate_open(): bool {
    $opts = get_option('cluster_one_settings', array());
    if (empty($opts['mesh_translate_all'])) {
        return false;
    }
    return cluster_one_ai_is_selfhosted((string) ($opts['mesh_translator'] ?? ''));
}

/** v1.3.0 — WER ZAHLT EINE MESH-UEBERSETZUNG?
 *
 * Christian am 27.08.2026, als Pacioli die Frage aufwarf:
 *
 *     „Ich wuerde sagen, es ist immer der, der den Uebersetzer-Button drueckt.
 *      Die Alternative des Users: Warten, bis der SysOp es tut."
 *
 *         Wo es einen Knopf gibt, zahlt, wer drueckt.
 *         Wo es keinen gibt, zahlt, wer den Raum aufgemacht hat.
 *         Die Rechnung folgt der letzten freien Entscheidung.
 *
 * Die eine Ausnahme steht schon seit 0.9.48 da und heisst hier nur anders
 * herum: mesh_translate_open() bedeutete bisher „die anderen duerfen MEIN
 * Budget verwenden". Mit Pacioli muss ein Aufruf einen NAMEN tragen, also wird
 * daraus „ich uebernehme" — der Akteur ist dann der SysOp, nicht der Druecker.
 *
 * Ohne Pacioli aendert diese Zahl gar nichts: cluster_one_ai_generate_text()
 * ignoriert den Akteur, solange cv_acting_as() nicht existiert. Sie wird heute
 * verdrahtet, damit sie an dem Tag, an dem Pacioli kommt, schon liegt — und
 * nicht von Hand nachgezogen werden muss, waehrend nichts mehr uebersetzt. */
/** v1.4.0 — DIE MAEZEN-LAMPE, BENANNT.
 *
 * Gibt es fuer DIESEN Anbieter eine Freigabe des SysOps, die ihn pro
 * Aufruf nichts kostet?
 *
 * HEUTE GIBT ES GENAU EINE, und sie gehoert dem Hallo-Chat:
 * 'mesh_translate_all', und sie greift nur fuer den gesetzten
 * Uebersetzer, und nur wenn der SELBSTGEHOSTET ist. Es gibt noch keine
 * Freigabe je Anbieter — Bismarck ist gefragt, ob ein Raum eine braucht.
 *
 * Diese Funktion sagt das, statt es zu verschweigen. Ein Anbieter, der
 * nicht der Hallo-Chat-Uebersetzer ist, hat keine Freigabe, und die
 * Antwort ist nicht „nein, verboten", sondern „nein, es gibt keine".
 *
 *     Eine Grenze, die man nennt, kann jemand verschieben. Eine, die
 *     man verschweigt, wird eines Tages umgangen.
 *
 * @param string $cid Anbieter-ID.
 */
function cluster_one_ki_freigegeben(string $cid): bool {
    if ($cid === '') {
        return false;
    }
    $opts = get_option('cluster_one_settings', array());
    if ($cid !== (string) ($opts['mesh_translator'] ?? '')) {
        return false; // fuer diesen Anbieter gibt es ueberhaupt keine Freigabe
    }
    return cluster_one_mesh_translate_open();
}

/** v1.4.0 — WER ZAHLT EINEN KI-AUFRUF AN DIESEN ANBIETER, HIER UND JETZT.
 *
 * Die allgemeine Form von cluster_one_akteur_mesh(). Christians Satz vom
 * 27.08.2026 stand bisher nur ueber der Mesh-Uebersetzung und galt immer
 * fuer alles:
 *
 *     Wo es einen Knopf gibt, zahlt, wer drueckt.
 *     Wo es keinen gibt, zahlt, wer den Raum aufgemacht hat.
 *     Die Rechnung folgt der letzten freien Entscheidung.
 *
 * GEMESSEN AM 09.10.2026: cluster_one_akteur_mesh() liegt in kleinrock
 * UND in bismarck. Gerufen wird sie an genau einer Stelle der Welt,
 * kleinrock.php, an der einzigen Stelle mit `cluster_one_akteur_mesh())`.
 * Bismarck traegt die Regel und ruft sie nicht;
 * 'selfhosted' und 'mesh_translate_all' kommen in seinen eigenen
 * Dateien an null Stellen vor.
 *
 *     Ein Haus hat die Regel gefunden, zwei tragen sie, und nur eines
 *     ruft sie.
 *
 * @param string $cid Anbieter-ID.
 */
function cluster_one_akteur_ki(string $cid): int {
    if (cluster_one_ki_freigegeben($cid) && !cluster_one_is_sysop()) {
        return (int) cluster_one_main_admin_user_id();
    }
    return (int) get_current_user_id();
}

/** Wer zahlt eine Mesh-Uebersetzung. Ab v1.4.0 NUR NOCH EIN AUFRUF.
 *
 * Diese Funktion entschied bis v1.3.x selbst. Jetzt fragt sie die
 * allgemeine Form — eine Wahrheit, ein Entscheider. Zwei Stellen, die
 * dieselbe Regel buchstabieren, gehen an dem Tag auseinander, an dem
 * jemand eine von beiden anfasst.                              (P1-12)
 *
 * SIE MUSS FUER JEDE EINGABE DASSELBE LIEFERN WIE VORHER, und das ist
 * nachgerechnet: cluster_one_ki_freigegeben($uebersetzer) ist genau dann
 * wahr, wenn cluster_one_mesh_translate_open() wahr ist — denn die
 * Gleichheit mit dem Uebersetzer ist dann erfuellt, und ein leerer
 * Uebersetzer faellt in cluster_one_ai_is_selfhosted('') ohnehin durch.
 */
function cluster_one_akteur_mesh(): int {
    $opts = get_option('cluster_one_settings', array());
    return cluster_one_akteur_ki((string) ($opts['mesh_translator'] ?? ''));
}

/** v1.4.0 — WELCHE KI-INSTANZ SPRICHT FUER DIESEN MENSCHEN?
 *
 * Christian am 09.10.2026:
 *
 *     „Der Anbieter ist also gleich, aber die KI-Instanz nicht."
 *
 * Der SysOp waehlt die Connectoren fuer den Knoten. Pacioli sorgt
 * dafuer, dass jeder Mensch DENSELBEN Anbieter mit SEINEM eigenen
 * Schluessel benutzt. Gleiches Haus, andere Instanz, eigene Rechnung.
 *
 * ZWEI MODELLE, UND SIE DUERFEN NICHT INEINANDERFALLEN:
 *
 *     ohne Pacioli   cluster_one_ai_identities   Anbieter -> EIN Konto,
 *                    knotenweit, nur vom SysOp gesetzt. Der Stand von
 *                    gestern, und er bleibt gueltig, solange Pacioli
 *                    nicht da ist.
 *     mit Pacioli    cv_identity_for($mensch, $cid)   eine Instanz je
 *                    Mensch je Anbieter.
 *
 * UND KEIN RUECKFALL VON DER EINEN AUF DIE ANDERE. Hat ein Mensch mit
 * Pacioli keine eigene Instanz, ist die Antwort 0 — nicht die
 * knotenweite Kennung. Das war der Fehler, den Christian am 09.10. um
 * drei Uhr gefunden hat, bevor er ausgeliefert war: MenschA drueckt
 * „Claude-AI-Main", es antwortet SEINE anthropic-KI, und die Antwort
 * steht unter fremdem Namen im Protokoll.
 *
 *     Eine Antwort unter einem fremden Namen ist schlimmer als keine
 *     Antwort.
 *
 * Wer keine eigene hat, hat das Megafon — die Anregung in Bismarck, die
 * es seit v0.9.13 gibt und die genau fuer diesen Fall gebaut ist.
 *
 * @param int    $mensch WP-Benutzer-ID eines MENSCHEN.
 * @param string $cid    Anbieter-ID.
 * @return int Benutzer-ID der KI-Instanz, oder 0.
 */
/* R-VERWEIS-1 (Hollerith, 09.10.2026) — WARUM HIER KEINE ZEILENNUMMERN
 * IN FREMDE HAEUSER STEHEN.
 *
 * Mammut hat Bismarck eine Zeilennummer genannt, die in dessen Datei nicht
 * galt. Hollerith hat daraufhin GEMESSEN statt zugestimmt:
 *
 *     bismarck.php:1451    war in 0.2.39 richtig, ist in 0.2.40 falsch
 *                          (die Datei wuchs in derselben Runde um 7529 B)
 *     dieselbe Zeile       zeigt in Kleinrock auf eine Datei, die es dort
 *                          nicht gibt
 *     ein Hunk bei 905     machte aus 107 Zeilen 99 — alles danach um 8
 *                          verschoben, die Menge aller Verschiebungen [-8]
 *
 * Von FUENF Verweisen dieser Art in dieser Datei stimmte beim Nachmessen am
 * 09.10. genau EINER: der, der am selben Tag geschrieben und geprueft wurde.
 *
 *     Ein Verweis aus dem Boden in eine Hausdatei ist in dem Augenblick
 *     richtig, in dem er geschrieben wird, und danach nie wieder geprueft.
 *
 *     Eine Zeilennummer gilt in einer Datei. Ein Verweis aus dem Boden in
 *     eine Hausdatei gilt in hoechstens EINEM Haus — und der Boden fahrt in
 *     alle.                                            (Holleriths Form)
 *
 * ALSO: in fremde Haeuser zeigt dieser Boden am WORTLAUT. Die Nummer darf
 * danebenstehen, aber nie allein. */
function cluster_one_ai_instanz(int $mensch, string $cid): int {
    if ($cid === '') {
        return 0;
    }

    if ($mensch > 0 && function_exists('pacioli_identity_for') && function_exists('pacioli_provider_of')) {
        $c1_i = pacioli_identity_for($mensch, $cid);
        /* GEGENGEPRUEFT, nicht geglaubt: die zurueckgegebene Instanz muss
           wirklich zu diesem Anbieter gehoeren. Eine Zusage, auf die man
           sich stuetzt, ohne sie zu pruefen, ist eine Hoffnung mit einer
           Signatur. (Befund am eigenen Zeugen, 08.10., Mutation 3) */
        if ($c1_i && (int) $c1_i > 0 && pacioli_provider_of((int) $c1_i) === $cid) {
            return (int) $c1_i;
        }
        return 0;
    }

    $c1_ids = cluster_one_get_ai_identities();
    return isset($c1_ids[$cid]) ? (int) $c1_ids[$cid] : 0;
}

/** Der Anzeigename der Instanz, die fuer diesen Menschen spricht.
 *
 * '' wenn es keine gibt — NICHT der knotenweite Name. Wer hier einen
 * Rueckfall einbaut, baut die falsche Beschriftung wieder ein.
 */
function cluster_one_ai_instanz_name(int $mensch, string $cid): string {
    $c1_i = cluster_one_ai_instanz($mensch, $cid);
    if ($c1_i <= 0) {
        return '';
    }
    $c1_u = get_userdata($c1_i);
    return ($c1_u && trim((string) $c1_u->display_name) !== '') ? (string) $c1_u->display_name : '';
}

/** v0.9.57: DARF DIE WEISSLISTE IM HALLO-CHAT SCHREIBEN?
   Christians Frage am 21.08.2026: "Bei den Settings von Light muss er dann nur
   entscheiden: Duerfen die auch uebersetzen? Duerfen die auch schreiben."
   Das Erste gab es seit 0.9.48, das Zweite nicht — Schreiben war fuer jeden
   auf der Weissliste immer erlaubt, ohne Wahl des SysOp.

   ZWEI SCHALTER, ZWEI GRUENDE, ZWEI VOREINSTELLUNGEN:
   Uebersetzen ist voreingestellt ZU, weil es Geld kostet und der SysOp zahlt
   (Kostenwahrheit). Schreiben ist voreingestellt OFFEN, weil es nichts kostet
   und weil jeder unter seinem EIGENEN Namen spricht —
   cluster_one_kleinrock_speaker_label() setzt "Mensch (Rolle) von Knoten"
   davor; es redet also niemand im Namen des SysOp.

   UND DESHALB HEISST DER SCHLUESSEL 'mesh_write_sysop_only' UND NICHT
   'mesh_write_all': Der gespeicherte Wert bedeutet immer die ABWEICHUNG vom
   bisherigen Verhalten. Ein fehlender Schluessel — und der fehlt auf jedem
   bestehenden Knoten — muss den Zustand von gestern ergeben. Waere der
   Schalter positiv benannt, haette sein Einbau allen Beschirmten still das
   Schreiben genommen. Ein Schalter, der beim Einbau etwas wegnimmt, ist eine
   Falle und keine Wahl. */
function cluster_one_mesh_write_open(): bool {
    $opts = get_option('cluster_one_settings', array());
    return empty($opts['mesh_write_sysop_only']);
}

function cluster_one_save_ai_selfhosted(array $a): void {
    update_option('cluster_one_ai_selfhosted', array_values(array_unique(array_map('strval', $a))), false);
}

/* --- MODELLWAHL JE KENNUNG (v0.9.59, Umzug aus modi.php) ---
   Welches Modell eine KI-Kennung benutzen soll. Das ist keine Beschreibung
   eines Raumzwecks — es ist dieselbe Familie wie die Selbstgehostet-Frage
   darueber: was kostet, und wer darf. Ohne diesen Umzug haette Light das
   Modus-Vokabular allein wegen cluster_one_ai_model() mitschleppen muessen,
   das der KI-Aufruf bei JEDEM Gebrauch liest. --- */

/* --- Modell pro Kennung (optional) -----------------------------------------
   cluster_one_ai_models: Provider-$cid -> Modell-ID. Leer = Anbieter wählt
   automatisch. Gesetzt = wird per using_model_preference() mitgegeben. */
function cluster_one_get_ai_models(): array {
    $a = get_option('cluster_one_ai_models', array());
    return is_array($a) ? $a : array();
}

function cluster_one_ai_model(string $cid): string {
    $m = cluster_one_get_ai_models();
    return isset($m[$cid]) ? (string) $m[$cid] : '';
}

function cluster_one_save_ai_models(array $a): void {
    update_option('cluster_one_ai_models', $a, false);
}

/* --- FEHLERTEXT EINES KI-AUFRUFS (v0.9.62, Umzug aus bismarck.php) ---
   Uebersetzt einen Fehlercode des KI-Aufrufs in einen Satz fuer Menschen.
   Das ist keine Bismarck-Sache: Kleinrock zeigt ihn, wenn eine Uebersetzung
   im Hallo-Chat fehlschlaegt, und ein Modul ohne Werkraum braucht ihn
   trotzdem. Dieselbe Familie wie ai_generate_text und ai_model. --- */

/** Fehlercode -> Text, geteilt zwischen Bismarck-Räumen und Platon-Themen
   (v0.0.47) — eine Übersetzung statt zwei fast identischer Kopien. */
function cluster_one_ai_error_message(string $code): string {
    $map = array(
        'no_client'  => __('No AI client found. The AI Client ships with WordPress 7.0 — this node runs an older version.', 'cluster-one-bismarck'),
        'no_key'     => __('No key — this AI has no working API connection yet. Add one under Settings → Connectors.', 'cluster-one-bismarck'),
        'pin_failed' => __('Could not target this AI’s provider specifically (the connector id may not match the provider slug). The call was withheld to avoid mislabeling the reply.', 'cluster-one-bismarck'),
        'rejected'   => __('The provider rejected the request. Check the API key under Settings → Connectors.', 'cluster-one-bismarck'),
        /* v0.9.12: Nicht nur KI-Fehler — dieser Kanal traegt jetzt auch die
           Leiter-Absage. Der Name der Funktion stimmt damit nicht mehr ganz;
           das ist der Preis dafuer, keinen zweiten Meldeweg zu bauen. */
        'ladder'     => __('You can read along in this room, but not write. The SysOp sets the ladder.', 'cluster-one-bismarck'),
        'timeout'    => __('The request timed out. Please try again.', 'cluster-one-bismarck'),
        'empty'      => __('The AI returned an empty answer. Please try again.', 'cluster-one-bismarck'),
        'pool'       => __('This project is in the cooling pool — the reactor is off. Writing is still possible; AI calls and rounds are not.', 'cluster-one-bismarck'), // v0.1.1
        'failed'     => __('The call did not go through.', 'cluster-one-bismarck'),
        /* v1.3.3 — VIER CODES, DIE BIS HEUTE AUF 'failed' ZURUECKFIELEN.
           Pacioli meldete genau, was los war; diese Tafel kannte die Codes
           nicht und sagte „Der Aufruf ist nicht durchgegangen." Christian
           hat am 08.10. an genau diesem Satz vier Stunden gesucht.

               Ein Rueckfall-Satz, der jeden unbekannten Code auffaengt,
               verwandelt eine genaue Auskunft in ein Schulterzucken.

           DER NAME STEHT IM DETAIL, nicht hier: diese Tafel bekommt nur
           einen Code. Wer, und bei wem, sagt das Detail aus
           cluster_one_ai_generate_text(). */
        'no_akteur'       => __('This call was not attributable to anyone, so it was not made. With key safekeeping active, every call needs a person whose AI acts.', 'cluster-one-bismarck'),
        /* v1.4.1, 09.10.2026 — HOLLERITHS BEFUND 2, und er hat recht.
           In 0.2.39 feuerte dieser Code nur bei einer KI eines ANDEREN
           Anbieters; der Satz beschrieb das. In 0.2.40 feuert er bei JEDER
           uebergebenen KI — und der Satz blieb stehen. Ein Mensch haette
           Tafelsatz und Detail nebeneinander gelesen und sich widersprochen
           gesehen.

               Ein Satz, der zu einer alten Bedingung gehoert, luegt in dem
               Augenblick, in dem die Bedingung sich weitet. Wer eine
               Bedingung aendert, muss ihren Satz mitzaehlen.

           Und der Code heisst jetzt, was er prueft: geprueft wird, ob der
           Akteur ein MENSCH ist, nicht ob zwei Anbieter zueinander passen.
           Der Schluessel ist kein uebersetzter Text — die Umbenennung
           kostet keinen Katalogeintrag. */
        'akteur_keine_person' => __('This call named an AI instead of a person. A call needs someone on whose account it runs — it was withheld rather than billed to the wrong account.', 'cluster-one-bismarck'),
        'no_identity'     => __('No AI of the paying person could be found for this provider. Create one under Tools → My AIs, or have the host do it.', 'cluster-one-bismarck'),
        'not_allowed'     => __('That AI may not be commissioned by this person. The system identity can never be commissioned — not even by an administrator.', 'cluster-one-bismarck'),
    );
    return isset($map[$code]) ? $map[$code] : $map['failed'];
}

/* ============================================================
   DIE HILFE-BLASE (v0.9.66, Umzug aus feynman.php)

   Sie zieht in den Kern, weil sie reine Form ist — sie weiss nichts von
   Feynman, sie umrandet einen Text. Und weil kern in BEIDEN Haeusern liegt,
   kann damit auch Light ein Fragezeichen zeichnen. Der Text dazu kommt aus
   dem Organ (cluster_one_kleinrock_steckbrief), nicht von hier.
   ============================================================ */

/** Die Blase selbst — getrennt, damit Bereichsbeschreibungen dieselbe Form
   bekommen wie die Namenstexte. */
function cluster_one_help_bubble(string $text): string {
    return '<details style="display:inline-block;vertical-align:middle;margin-left:.5em;font-weight:400;">'
         . '<summary style="cursor:pointer;list-style:none;display:inline-block;width:1.5em;height:1.5em;line-height:1.5em;'
         . 'text-align:center;border:1px solid #c3c4c7;border-radius:50%;font-size:.7em;opacity:.75;"'
         . ' title="' . esc_attr__('What is this, and where does the name come from?', 'cluster-one-bismarck') . '">?</summary>'
         . '<div style="display:block;max-width:760px;margin:.8em 0 .4em;padding:.9em 1.1em;border:1px solid #dcdcde;'
         . 'border-radius:4px;background:#fff;font-size:.92em;line-height:1.6;font-weight:400;">'
         . wpautop(esc_html($text))
         . '</div></details>';
}


    /* v1.0.6 — DIE MELDUNG ZOG AUS DEM LADER HIER HEREIN.
       Als letzte Zeile IM Riegel, genau wie 'boden' in kasper.php: so meldet
       sich nur die Kopie, die auch wirklich deklariert hat.
       Vorher stand sie im Lader — und meldete auch dann, wenn dieser Kopie der
       Riegel gerade jede Deklaration abgenommen hatte. Auf Christians Main sah
       das so aus:
           kern laeuft aus cluster-one-bismarck, gemeldet auch von cluster-one
       Es lief eine Kopie. Falsch war nur die Meldung.
       DIE BODEN-FASSUNG, NICHT DIE DES PLUGINS: kern gehoert keinem Plugin. */
    /* v1.0.6b — WARUM DIE MELDUNG SELBST GERIEGELT IST.
       Sie war eine Zeile lang ungeriegelt, und drei Zeugen der Schnitt-Suite
       starben sofort: loeschung.php, paarung.php und umschluesselung.php laden
       kern.php ALLEIN, ohne Kasper. „Call to undefined function
       cluster_one_hier()".

       Das war kein Werkzeugfehler, sondern eine echte neue Kopplung: kern.php
       setzte damit voraus, dass Kasper vorher geladen wurde. In beiden Ladern
       ist das so — aber eine Voraussetzung, die nur in Ladern gilt und nirgends
       geprueft wird, ist ein Fallstrick fuer jedes kuenftige Light. Post Dolly
       etwa fragt Kasper ausdruecklich nur ueber defined().

       Also: fehlt Kasper, wird nicht gemeldet und nichts stuerzt ab. Dass in
       einem echten Plugin trotzdem gemeldet WIRD, ist keine Hoffnung, sondern
       gemessen — meldung.php prueft, dass kasper.php in jedem Lader VOR
       kern.php und speicher.php steht. Sonst waere dieser Riegel eine stille
       Abschaltung statt einer Absicherung. */
    /* v1.12.0 — DIE MELDUNG IST NACH OBEN GEWANDERT, vor den Riegel
       (VERTRAG 27 §4). Hier stand sie bis heute, und damit schwieg eine
       verdraengte kern.php auch ueber ihre eigene Existenz. */
}


/* ===========================================================================
   DIE HUELLE — sieben Funktionen, am 11.09.2026 aus kranz.php und
   feynman.php hierher gezogen.

   WARUM SIE HIER STEHEN: sie werden von Haeusern gerufen, die das Organ
   nicht besitzen — bismarck, kasper, kern, platon, landkarte, winsor,
   menue, modi, kleinrock. Solange sie in einem SCHALTBAREN Organ stehen,
   muss dieses Organ in jedes Paket mitreisen, das sie braucht; und ein
   Organ, das mitreist, bringt seinen Menuepunkt mit.

   WELCHE SIEBEN: nicht aufgeschrieben, sondern gemessen. Vier werden von
   aussen gerufen, drei zieht ihre eigene Huelle nach. Eine Liste von
   Funktionen ist ein Wunsch; ihre Huelle ist die Rechnung.

   NICHT MITGEZOGEN: cluster_one_sanitize_settings(). Sie hat genau EINEN
   Rufer. Ein einzelner Rufer ist kein Gemeingut, sondern eine
   Zweierbeziehung. Wer im Zweifel mitnimmt, hat nicht entschieden,
   sondern verschoben.

   JEDE MIT EIGENEM RIEGEL: kern.php liegt in vier Paketen gleichzeitig.
   =========================================================================== */

if (!function_exists('cluster_one_blueprint_areas')) {
/* ============================================================
   DIE BLAUPAUSE (v0.9.58, Umzug aus feynman.php)
   Der Setup-Reiter. Sie legt Bereiche und Raeume an — ein einmaliger
   Akt beim Einrichten des Knotens, kein Werkzeug des taeglichen
   Betriebs. Deshalb Kranz und nicht Feynman, und deshalb heisst der
   Menuepunkt jetzt 'Kranz (Setup)': Das Wort zieht mit der Sache um.
   ============================================================ */
/* --- v0.4.4: DIE BLAUPAUSE ----------------------------------------------------
   Prometheus in klein: Nicht die leere Leinwand mit einer Anleitung, sondern ein
   eingerichtetes Haus, das man umraeumen darf. Eine leere Struktur zwingt zur
   Frage "woran muss ich denken?" — und wer das nicht weiss, hoert auf. Eine
   Blaupause stellt die andere Frage: "was davon brauche ich nicht?"
   LOESCHEN IST LEICHTER ALS ERFINDEN.

   Am 1. August war dieselbe Idee schon einmal da und wurde zurueckgestellt, mit
   Christians eigenem Argument: Vorgefertigte Bereiche sind Behauptungen darueber,
   wie man arbeitet — die kann man erst aufstellen, wenn man es weiss. Heute weiss
   er es: Die Struktur ist fuenf Wochen benutzt worden. Damit ist sie ein
   ZEUGNIS, keine Behauptung, und die Bedingung ist erfuellt.

   Nur Bereiche, keine Raeume und keine Themen: Ein Haus mit Zimmern ist
   einladend, ein Haus mit fremden Moebeln nicht. */
function cluster_one_blueprint_areas(): array {
    /* v0.6.4: VIER STATT FUENF, UND KUERZER. Christians Live-Befund gab den
       Ausschlag: Auf der Main heissen seine Bereiche "Projekte", "Wiki",
       "Allgemein" — beim wirklichen Arbeiten hat er kuerzer benannt als beim
       Planen. Namen stehen ueber jedem Thema und in jeder Auswahlliste, und
       Beitraege haengen daran.

       KI- und Mensch-Tagebuecher sind ZUSAMMENGELEGT. Christians Grund:
       "Es ist cooler, wenn wir Menschen und KI nicht separat behandeln."
       Technisch gratis — Tagebuch-Sein haengt an der Thread-Rolle, nicht am
       Bereich; kein Code las je diese Bereichsnamen.

       DIE REIHENFOLGE BILDET DEN ABLAUF AB, nicht die Haeufigkeit.

       In 0.6.4 hatte ich nach Haeufigkeit sortiert — Reaktoren vor Kruse, weil
       dort taeglich gearbeitet wird. Christians Einwand in 0.7.5 war besser:
       Die Blaupause ist eine Empfehlung fuer jemanden, der noch nicht weiss,
       wie man arbeitet. Eine Reihenfolge, die den Ablauf abbildet, bringt ihm
       etwas bei; eine nach Haeufigkeit nur Bequemlichkeit.

       Sein Beispiel: Im Brainstorming kommt die Idee, Discord zu benutzen. Der
       Blick nach draussen (Kruse) zeigt, dass die Chatkontrolle es trifft, weil
       es nicht verschluesselt ist. Ohne diesen Schritt waere der Reaktor schon
       offen und der Entwurf weit — auf einer Grundlage, die weggebrochen ist.

       Also: brainstormen, pruefen, bauen. Tagebuecher und Johari danach, weil
       Selbsterkenntnis das ERGEBNIS ist und nicht die Voraussetzung — sie
       speist sich aus allem davor. Mit den Pfeilen in zwei Klicks aenderbar. */
    return array(
        'Brainstorming' => __('Where thoughts are worked on together before anyone knows where they lead. The text belongs to no one here — that is the point.', 'cluster-one-bismarck'),
        'Kruse'                => __('What acts on our projects from outside: laws, technical shifts, market conditions. Not world events as such — the part that concerns us. Look here before you open a reactor, and again while it runs: a situation can change under a project that takes months. Whoever brings something in says why it concerns us. Peter Kruse would have said: stay awake.', 'cluster-one-bismarck'),
        'Reaktoren'            => __('Where the work happens. Projects that may one day become a label and travel to other nodes — this node itself counts: your website, your channel, whatever you are building next.', 'cluster-one-bismarck'),
        /* v0.6.7: KRUSE — was von aussen einwirkt. Christians Anlass: Sein
           Projekt hat sich gewandelt, als er von der Chatkontrolle erfuhr.

           Ich hatte eingewandt, das gehoere nicht in die Blaupause, weil ein
           Kochblog-Knoten es nicht braucht. Er hat widersprochen, und zu Recht:
           DIE BLAUPAUSE IST BEREITS NUDGING — "Loeschen ist leichter als
           Erfinden". Mit meinem Argument haette auch Johari fallen muessen.

           Der Name musste unpolitisch sein, weil der Bereich zu JEDEM Knoten
           reist: Cassandra oder Orwell waeren ein mitgeliefertes Urteil. Peter
           Kruse ist Netzwerkforscher — und seine These traegt den Bereich
           besser als jede Nachrichtenlogik: Intuition bildet sich an der Grenze
           der Ueberforderung UND VERALTET, wenn die Welt sich weiterdreht.
           Wachbleiben ist damit keine Neugier, sondern Wartung dessen, womit
           man urteilt.

           Der letzte Satz der Beschreibung ist der wichtige. Ohne ihn wird
           daraus in vier Wochen ein allgemeines Politikforum. */
        'Tagebücher'           => __('Diaries — for the humans here and for the AIs alike. Each one belongs to whoever keeps it; the chat next to it is the door, not the key.', 'cluster-one-bismarck'),
        'Johari (Wilde)'       => __('Here people and AIs describe themselves. Whoever answers below does not do it to agree: a self-assessment is only worth something once it has held up against the others.', 'cluster-one-bismarck'),
        /* v0.8.5: DER SYSOP-BEREICH, ganz unten (Christians Entscheidung).
           Als einziger Bereich ist er eine REGEL statt einer Vorauswahl: Was
           hier liegt, sieht niemand ausser dem SysOp — und der Bereich selbst
           erscheint bei den anderen gar nicht. Das Verschieben hinein und
           heraus ist ein bezeugter Akt, sonst waere es ein stiller
           Sichtbarkeitswechsel. */
        'SysOp'                => __('Everything in this area — and the area itself — stays invisible to everyone but the SysOp. Moving something in or out is recorded, because it changes who may read it.', 'cluster-one-bismarck'),
    );
}
}

if (!function_exists('cluster_one_render_kranz_tabs')) {
/** Die Reiterleiste des Kranz — der Kontrollraum.

   DIE REIHENFOLGE IST EINE ENTSCHEIDUNG, KEIN ZUFALL: Kleinrock, Bismarck,
   Profil, Persoenlicher Bereich, System — so von Christian festgelegt. Seit
   v0.0.40 stehen hier die vollstaendigen Kleinrock- und Bismarck-Einstellungen
   statt nur des Profils, seit v0.0.50 der Persoenliche Bereich, seit v0.0.58
   System als reine, schreibgeschuetzte Uebersicht.

   (Dieser Absatz stand bis v0.9.70 im Kopf von spiegel.php — in der falschen
   Datei, und beim Umzug des Spiegels waere er verschwunden. Der Waechter hat
   ihn als verlorenen Warum-Kommentar gemeldet; hier steht er richtig.) */
function cluster_one_render_kranz_tabs(string $current): void {
    /* 19.10.2026 (Neo) — DIE LEISTE GEHOERT DEM, DER DIE SEITE BEANSPRUCHT.
       Christians Befund vom 18.10.: unter „Cluster One – Crockford" stand die
       ganze Kranz-Leiste, und er hielt Bismarcks Einstellungen fuer
       Crockfords. Die Leiste war fest und fragte niemanden.
       Jetzt zwei Fragen, beide an Dinge, die es schon gibt:
         1  Hat ein Produkt fuer DIESE Seite eine eigene Leiste angemeldet
            (Filter 'cluster_one_reiterleiste', Antwort = Funktionsname)?
            Dann zeichnet es sie, und der Boden tritt zurueck.
         2  Sonst zeichnet der Boden seine Leiste — aber nur mit den Seiten,
            die KEIN Produkt beansprucht (cluster_one_seite_ansprueche).
            Was ein Produkt beansprucht, fuehrt es selbst; was niemand
            beansprucht, fuehrt der Boden. Keine Seite verliert ihren Weg
            (VERTRAG 25 §1), aber keine steht mehr unter fremdem Namen. */
    $c1_fremd = (string) apply_filters('cluster_one_reiterleiste', '', $current);
    if ($c1_fremd !== '' && $c1_fremd !== __FUNCTION__ && function_exists($c1_fremd)) {
        $c1_fremd($current);
        return;
    }

    /* 28.10.2026 (Neo), Boden 1.18.0 — ZWEI REIHEN, WIE IM KALENDER. Nemesus:
       „Dort steht unter einer Reihe von Tabs noch eine Reihe von Tabs. Und genau
       das brauchen wir im Reiter Knoten." Oben die Leiste des Hauses, von dem der
       Leser kommt (Reiter „Knoten" aktiv), darunter die Knoten-Reihe. Die Vorlage
       ist cluster_one_faust_leisten(): das Haus zeichnet auf Zuruf (Aktion
       'cluster_one_leiste'), der Boden sagt nur, wer gemeint ist. */
    $c1_von = cluster_one_herkunft();
    if ($c1_von !== '' && function_exists('do_action')) {
        do_action('cluster_one_leiste', $c1_von, $current);
    }
    /* 03.11.2026 (Neo), Boden 1.22.0 (Tau): die Knoten-Reihe ist eine ZWEITE Reihe — „Feynman" und „Knoten" stehen in der
       Hausleiste darueber (cluster_one_render_tabs, zweites Argument). Bis 1.21.1 ohne das Argument; den doppelten Knoten-
       Reiter hielt dort die Adressprobe ab. */
    cluster_one_render_tabs(cluster_one_kranz_reiter($current), true);
}

/** Die Eintraege der Boden-Leiste — EINE Quelle, zwei Leser: die Leiste oben
 *  und das Dach (menue.php), das die Boden-Seiten verlinkt, damit sie einen
 *  Weg haben, der nicht durch ein fremdes Produkt fuehrt. 19.10.2026 (Neo). */
function cluster_one_kranz_reiter(string $current): array {
    $c1_reiter = [
        /* 19.10.2026 (Neo): „Bismarck" hiess eine Seite des Bodens nach einem
           Produkt, das auf einem Knoten fehlen kann. Sie traegt die
           KI-Kennungen. Die Knoten-Einstellungen stehen jetzt am ENDE der
           Leiste (Christians Wunsch vom 19.10.), siehe unten. */
        /* 28.10.2026 (Neo), Boden 1.18.0: die KI-Kennungen standen bis 1.17.7 als
           ERSTER Eintrag hier; sie stehen jetzt hinter der Chronik, Nemesus'
           Reihenfolge der Knoten-Reihe: Profil · System · Chronik · KI-Kennungen ·
           Knoten-Einstellungen. Gleiche Zeile, anderer Platz. */
        ['label' => __('Profile', 'cluster-one-bismarck'), 'url' => admin_url('admin.php?page=cluster_one_profile'), 'active' => $current === 'cluster_one_profile',           'fn' => 'cluster_one_render_profile_page'],
        /* 30.10.2026 (Neo), Boden 1.19.4: der Eintrag „Personal area" stand bis 1.19.3 HIER, als
           zweiter – und war seit dem 19.10. unsichtbar, weil Bismarck die Seite beanspruchte. Bismarck
           hat den Anspruch am 30.10. zurueckgegeben (0.2.66); der Eintrag steht jetzt hinter den
           KI-Kennungen, siehe unten. Gleiche Zeile, anderer Platz. */
        ['label' => __('Modes', 'cluster-one-bismarck'), 'url' => admin_url('admin.php?page=cluster_one_modes'), 'active' => $current === 'cluster_one_modes',             'fn' => 'cluster_one_render_modes_page'], // v0.0.75
        ['label' => 'System', 'url' => admin_url('admin.php?page=cluster_one_system'), 'active' => $current === 'cluster_one_system',            'fn' => 'cluster_one_render_system_page'],
        /* v0.9.41: Die Chronik, von der Mesh-Seite hierher gezogen. Hinter
           System, weil die beiden dieselbe Familie sind — und ausdruecklich
           NICHT in System hinein: Christian will den System-Reiter selbst
           noch aufraeumen, und man stellt nichts auf einen Boden, der gleich
           gefegt wird. Sein eigenes Argument von heute Nachmittag, diesmal
           gegen mich gewendet — ich hatte vorgeschlagen, sie dort einzufuegen. */
        ['label' => __('Chronicle', 'cluster-one-bismarck'), 'url' => admin_url('admin.php?page=cluster_one_chronik'), 'active' => $current === 'cluster_one_chronik',           'fn' => 'cluster_one_render_chronik_page'],
        /* 28.10.2026 (Neo), Boden 1.18.0 — KI-KENNUNGEN SIND KNOTEN-SACHE. Nemesus'
           Pruefung an Pacioli: ohne Pacioli gilt „Anbieter → ein Konto, knotenweit,
           nur vom SysOp gesetzt"; mit Pacioli benutzt der Knoten eine KI trotzdem
           erst, wenn sie HIER einem Anbieter zugeordnet ist. Also keine Bismarck-
           Seite (Bismarck hat seinen Anspruch darauf und auf die Chronik am 28.10.
           zurueckgegeben, bismarck.php), sondern die vierte der Knoten-Reihe. */
        ['label' => __('AI identities', 'cluster-one-bismarck'),  'url' => admin_url('admin.php?page=cluster_one_bismarck_settings'),  'active' => $current === 'cluster_one_bismarck_settings', 'fn' => 'cluster_one_render_bismarck_settings_page'],
        /* 30.10.2026 (Neo), Boden 1.19.4 — DER PERSOENLICHE BEREICH IST KNOTEN-SACHE. Er ist der
           Platz des Menschen am Knoten: Mein Speicher (1.19.0), die Briefe, die Merkliste. Nemesus'
           Befund auf der Main: solange Bismarck ihn beanspruchte, fuehrte von Pacioli aus kein Reiter
           dorthin; und auf einem Knoten, der Pacioli verliert, braucht der Mensch den Ort, an dem
           seine Speicheradresse steht. Also der fuenfte der Knoten-Reihe, vor den Knoten-Einstellungen:
           Profil · System · Chronik · KI-Kennungen · Persoenlicher Bereich · Knoten-Einstellungen. */
        ['label' => __('Personal area', 'cluster-one-bismarck'), 'url' => admin_url('admin.php?page=cluster_one_personal_area'), 'active' => $current === 'cluster_one_personal_area',     'fn' => 'cluster_one_render_personal_area_page'],
        // v0.1.2: Feynman und Über mich sind hier RAUS — sie gehören zum
        // Lernpfad, nicht zu den Einstellungen. Sie liegen jetzt auf der
        // Feynman-Seite (cluster_one_render_feynman_tabs).
        /* v0.9.58: Die BLAUPAUSE geht den umgekehrten Weg — sie hing in
           Feynmans Leiste, obwohl sie Bereiche und Räume ANLEGT. Christians
           Zuordnung: ein einmaliger Akt beim Einrichten, kein Werkzeug des
           Lernpfads. Sie steht zuletzt, weil man sie einmal drückt. */
        ['label' => __('Setup', 'cluster-one-bismarck'), 'url' => admin_url('admin.php?page=cluster_one_blueprint'), 'active' => $current === 'cluster_one_blueprint',         'fn' => 'cluster_one_render_blueprint_page'],
    ];

    /* v1.9.7 (VERTRAG 17 §3) — DIE LANDKARTE ZIEHT HIERHER.
     *
     * Bis 0.4.2 hing sie unter Werkzeuge (add_management_page, Prioritaet 5) —
     * ein Cluster-One-Eintrag ausserhalb des eigenen Menues, und ausserdem die
     * ZWEITE Seite mit demselben Inhalt: die Floss-Seiten der Lights druckten
     * dieselbe Tabelle aus derselben Quelle, und der de_DE-Katalog uebersetzte
     * ihre Ueberschrift zeichengleich zu Umbertos fest eingebauter (H206).
     * Christian hat das als Routing-Fehler gemeldet, und es war keiner.
     *
     * GEFRAGT WIRD DAS ORGAN, NICHT DER ORDNER. 'landkarte' kann umziehen und
     * sein Wirt kann umbenannt werden (Crockford). Ein Reiter, der auf einen
     * Ordnernamen hoert, verschwindet bei der Umbenennung lautlos — genau die
     * Form, die Umberto am 03.09. in seinem eigenen Fingerabdruck gefunden hat.
     *
     * Der Boden fragt, das Light antwortet: hier steht keine Zeile, die den
     * Ordner des fremden Produkts kennt.
     *
     * v1.9.8 — UND BIS HIERHER STAND ER GENAU IN DIESEM SATZ. Der Kommentar
     * behauptete, kein Ordnername eines fremden Hauses komme vor, und nannte
     * ihn dabei. Es ist derselbe Fehler wie H203, eine Ebene tiefer: dort war
     * die Begruendung falsch gemessen, hier widerlegt sich der Satz selbst.
     *
     *     Ein Kommentar, der seine eigene Behauptung bricht, ist schlimmer
     *     als keiner: er wird geglaubt und er wird gegrept. */
    if (function_exists('cluster_one_modul_da') && cluster_one_modul_da('landkarte')) {
        $c1_reiter[] = ['label'  => __('Map', 'cluster-one-bismarck'),
                        'url'    => admin_url('admin.php?page=cluster_one_landkarte'),
                        'active' => $current === 'cluster_one_landkarte'];
    }

    /* 19.10.2026 (Neo): die Knoten-Einstellungen zuletzt — Christians Wunsch,
       und er passt: man richtet einmal ein und arbeitet dann auf den
       anderen Reitern. Bis heute hiess der Reiter „Kleinrock". */
    /* 27.10.2026 (Neo), Boden 1.17.2 — „Node settings". Bis 1.17.1 hiess der Reiter
       „Settings", und bei einem Produkt stand er damit namensgleich neben den
       Einstellungen des Produkts (Test 5). Begruendung in menue.php beim
       Menueeintrag; der Dach-Link („This node: …") nimmt das Etikett von hier. */
    $c1_reiter[] = ['label' => __('Node settings', 'cluster-one-bismarck'), 'url' => admin_url('admin.php?page=cluster_one_settings'), 'active' => $current === 'cluster_one_settings', 'fn' => 'cluster_one_render_settings_page'];

    /* VERTRAG 28 §3 (17.09.2026) — EIN REITER, DER NICHT FRAGT, BEHAUPTET.

       NEUN REITER, UND EINER HAT GEFRAGT. Die Landkarte, zehn Zeilen weiter
       oben, mit dem Satz darueber: „Gefragt wird das Organ, nicht der Ordner."
       Die anderen acht standen unbedingt da — auch in einem Paket, in dem es
       die Seite dahinter nicht gibt.

       GEMESSEN AM 17.09. an den vier ausgelieferten Paketen:
           cluster_one_render_modes_page liegt in bismarck/includes/modi.php.
           Der Reiter „Modi" stand in ALLEN VIER. In dreien fuehrte er
           auf „Du bist leider nicht berechtigt, auf diese Seite zuzugreifen."
       Christian hat es gemessen, indem er alles ausser Crockford abgeschaltet
       hat. Auf einem Knoten mit allen fuenf Produkten ist der Fehler
       unsichtbar — dort bringt Bismarcks Paket die Funktion fuer alle mit.
           Ein Knoten mit ALLEN Produkten kann keinen Fehler zeigen, der nur
           OHNE sie auftritt.                                (R-PRUEFSTUBE-1)

       UND DIE FALSCHE FRAGE, DIE ICH FAST GEBAUT HAETTE: „gibt es die SEITE".
       Nachgemessen ergab sie in jedem Paket achtmal JA — alle acht Seiten
       sind ueberall angemeldet. Die Frage, die traegt, ist die, die die
       Landkarte schon stellt: GIBT ES DIE HAND, DIE SIE ZEICHNET.

       EINE FRAGE, EIN ORT. Der Rueckruf steht am Eintrag ('fn'), gefiltert
       wird EINMAL, hier. Kein zweiter Schreibweg auf dieselbe Wahrheit (P1-12).
       Die Landkarte traegt kein 'fn' und behaelt ihre eigene, aeltere Frage —
       zwei Waechter uebereinander waeren zwei Wahrheiten.

       VERTRAG 25 §1 IST GEWAHRT: eine Seite, deren Rueckruf fehlt, hat keinen
       Inhalt. Sie verliert keinen Weg — sie hatte keinen.

       UND WAS HIER AUSDRUECKLICH NICHT NOETIG IST, weil ich es zuerst
       falsch behauptet habe (NACHTRAG 28c, 18.09.): die ANMELDUNG
       derselben Seiten in menue.php ist NICHT ungeprueft. Jede der 24
       versteckten Seiten steht in cluster_one_menue_wenn(<organ>, …) —
       21 davon — und die drei uebrigen zeigen auf Boden-Seiten, die es
       in jedem Paket gibt. Die Anmeldung fragt nach dem ORGAN und damit
       nach der besseren Frage als nach der Funktion.

       Ich hatte Aufrufe im QUELLTEXT gezaehlt und Anmeldungen zur
       LAUFZEIT gesagt — einen Tag nachdem ich R-ZAEHLUNG-1 aufgeschrieben
       habe, und eine Ebene tiefer als dort.

       Deshalb war der Reiter „Modi" unter Crockford auch kein zweiter
       Fehler: die SEITE war richtig nicht angemeldet, und nur der REITER
       hat behauptet, es gaebe sie. Der Filter hier oben ist die ganze
       Reparatur und nicht ihre Haelfte. */
    $c1_reiter = array_values(array_filter($c1_reiter, function ($c1_t) {
        $c1_fn = is_array($c1_t) ? (string) ($c1_t['fn'] ?? '') : '';
        return '' === $c1_fn || function_exists($c1_fn);
    }));

    /* 19.10.2026 (Neo): Beanspruchte Seiten fallen aus der Boden-Leiste —
       ihr Produkt fuehrt sie (Frage 2 im Kopf von cluster_one_render_kranz_tabs).
       Der Slug steht in der URL; eine zweite Liste der Slugs gaebe es sonst
       nicht. AUSNAHME: die Seite, auf der man gerade steht, bleibt immer —
       Christians Befund auf der DEV: Crockfords Landkarte hatte keinen
       eigenen Reiter mehr, weil Crockford sie beansprucht, aber noch keine
       eigene Leiste zeichnet. Eine Leiste ohne die eigene Seite ist keine. */
    if (function_exists('cluster_one_seite_ansprueche')) {
        $c1_reiter = array_values(array_filter($c1_reiter, function ($c1_t) {
            if (!is_array($c1_t)) { return false; }
            if (!empty($c1_t['active'])) { return true; }
            $c1_url = (string) ($c1_t['url'] ?? '');
            if (!preg_match('~[?&]page=([a-z0-9_\-]+)~', $c1_url, $c1_m)) { return true; }
            return cluster_one_seite_ansprueche($c1_m[1]) === array();
        }));
    }

    /* 28.10.2026 (Neo), Boden 1.18.0: jede Adresse der Reihe traegt die Herkunft
       mit (?von=), damit Ueberschrift und obere Leiste beim Wechsel innerhalb der
       Reihe bleiben, was sie sind. Nach dem Anspruchsfilter, der den Slug aus der
       Adresse liest — der Zusatz darf ihm nicht in die Quere kommen. */
    $c1_von = cluster_one_herkunft();
    foreach ($c1_reiter as $c1_i => $c1_t) {
        if (is_array($c1_t) && isset($c1_t['url'])) {
            $c1_reiter[$c1_i]['url'] = cluster_one_mit_herkunft((string) $c1_t['url'], $c1_von);
        }
    }

    return $c1_reiter;
}
}

if (!function_exists('cluster_one_help')) {
/** Das Fragezeichen. Gibt nichts aus, wenn es zu dem Schlüssel keinen Text
   gibt — lieber nichts als ein Knopf, hinter dem nichts steht. */
function cluster_one_help(string $key): string {
    $t = cluster_one_help_texts();
    $k = strtolower(trim($key));
    /* v0.7.1: Zusammengesetzte Ueberschriften wie "Gregor / Archimedes" tragen
       zwei Namen; die Hilfe haengt am ersten. Sonst gaebe es fuer die ganze
       Seite gar keine, nur weil der Titel breiter wurde. */
    if (empty($t[$k]) && strpos($k, '/') !== false) {
        $k = trim((string) strtok($k, '/'));
    }
    if (empty($t[$k])) {
        return '';
    }
    return cluster_one_help_bubble($t[$k]);
}
}

if (!function_exists('cluster_one_help_texts')) {
/* --- v0.7.0: DIE FRAGEZEICHEN ------------------------------------------------

   Christians Idee: hinter jeder Überschrift ein Fragezeichen, Klick öffnet den
   Text, Klick schließt ihn wieder — auf PC und Tablet gleich.

   Mein erster Vorschlag war Hovern. Seiner ist besser: Hovern gibt es auf dem
   Tablet nicht, das wären zwei Verhaltensweisen für zwei Geräteklassen, und auf
   dem Tablet die schlechtere. <details> kann genau das von sich aus — dieselbe
   Technik wie bei den archivierten Wiki-Fassungen in 0.5.2, kein JavaScript,
   mit der Tastatur erreichbar.

   Die Texte stehen an EINER Stelle. Wo ein Name dahintersteckt, ist es die
   sechste Spalte der Namenstabelle — Tabelle und Fragezeichen lesen dieselbe
   Quelle, sonst hätten wir zwei Kopien einer Wahrheit (W8). Wo kein Name
   dahintersteckt (Labels, Struktur), erklärt der Text die Funktion; das
   Fragezeichen fehlt dort trotzdem nicht, sonst fragt sich jemand, warum
   ausgerechnet dort keins ist. */
function cluster_one_help_texts(): array {
    $aus = array();
    foreach (cluster_one_namenstabelle() as $z) {
        /* v0.7.1: Wo es keinen Langtext gibt, tritt die Spalte "warum dieser
           Name" ein. Vorher hatten Wilde, Kruse, Nemo, Johari und die anderen
           gar kein Fragezeichen — dabei steht ihre Erklaerung eine Spalte
           weiter links. Lieber kurz als gar nicht. */
        $txt = !empty($z[5]) ? $z[5] : (!empty($z[3]) ? $z[3] : '');
        if ($txt !== '') {
            $aus[strtolower($z[0])] = $txt;
        }
    }
    /* v0.9.43: EIN FRAGEZEICHEN STATT ZWEI. Christian am 20.08.2026: "Wir
       brauchen auch keine zwei Fragezeichen, sondern eines, was Platon und
       Sokrates erklaert."

       Zusammengesetzt, nicht abgeschrieben: Die beiden Texte stehen weiter in
       der Namenstabelle und werden hier nur zusammengefuehrt. Sonst haetten
       wir zwei Kopien einer Wahrheit — der Grund, aus dem diese ganze Funktion
       ueberhaupt aus der Tabelle liest (W8).

       Neu ist genau EIN Satz, und zwar der, den es vorher nirgends gab: dass
       die beiden ein Ding sind. Genau das war ja der Zusammenbau. */
    $c1_pj = __('A project has two halves: the chat, where it is spoken, and the thread, where it is written down. One row here is both.', 'cluster-one-bismarck');
    if (!empty($aus['sokrates'])) { $c1_pj .= ' ' . $aus['sokrates']; }
    if (!empty($aus['platon']))   { $c1_pj .= ' ' . $aus['platon']; }
    /* v0.9.43, im gelesenen Golden-Diff gefunden, bevor er ausgeliefert wurde:
       Der Schluessel hiess erst schlicht 'projekte' — und damit trug JEDER
       Bereich, der "Projekte" heisst, dieselbe lange Erklaerung ein zweites Mal
       direkt unter der Ueberschrift, die sie schon hat. Christian HAT einen
       solchen Bereich; die Blaupause schlaegt ihn vor.

       Ursache ist cluster_one_help_for_area(): Steht ein Schluessel als Wort im
       Bereichsnamen, gilt der Text als Erklaerung des Bereichs. Das ist fuer
       ORGANNAMEN gedacht — "Johari (Wilde)" traegt den Schluessel in Klammern.
       Ein Reiter-Text ist kein Organname und hat in dieser Suche nichts zu
       suchen. Der Praefix haelt ihn heraus, ohne die alte Regel anzufassen. */
    $aus['reiter_projekte'] = $c1_pj;

    $aus['labels'] = __('Labels are what a node offers outward: a project, a text, an invitation. Other nodes do not receive them — they fetch them themselves, and only if they want to. Nothing is pushed anywhere from here.', 'cluster-one-bismarck');
    $aus['struktur'] = __('Areas sort threads and rooms. One shared list for both — a thread and its linked chat can live in the same place. “No area” is a valid state, and deleting an area never deletes content: its threads and rooms simply become unsorted.', 'cluster-one-bismarck');

    /* 27.10.2026 (Neo), Boden 1.17.4 — DAS DACH HAT EINE BLASE. Nemesus' Zaehlung nach
       Test 5: neun Seiten ohne Fragezeichen, das Dach („Cluster One") darunter. Der
       Schluessel ist das Wort aus cluster_one_page_h1('Cluster One'), kleingeschrieben
       wie alle hier. Kein Namenspatron, also keine Zeile in der Namenstabelle, sondern
       ein Eintrag wie 'labels' und 'struktur'. */
    $aus['cluster one'] = __('The roof over the products on this node. Every Cluster One product you install appears here with its page, and the node-level pages — node settings, profile, personal area, system, chronicle, setup — are reached from here as well. Nothing is set on this page; it only shows what is there.', 'cluster-one-bismarck');

    /* 27.10.2026 (Neo), Boden 1.17.4 — JEDES ORGAN NENNT DAS SEINE SELBST, AUCH OHNE
       NAMENSPATRON. Die Namenstabelle nimmt nur Steckbriefe (sechs Spalten, ein
       Mensch dahinter). Seiten wie Hallo, Bannliste, Freundesliste, KI-Vorschlaege
       (Kleinrock) oder Post Dolly haben keinen Patron und hatten darum keine Blase.
       Dieser Filter ist die zweite Naht, neben cluster_one_namensbriefe: ein Produkt
       haengt Schluessel => Text an, der Boden kennt den Namen nicht.
       DER BODEN GEWINNT: ein fremder Eintrag ueberschreibt keinen, den der Boden
       schon hat — sonst koennte ein Produkt die Blase eines anderen umschreiben,
       und die Seite saehe dabei richtig aus. */
    $c1_fremd = apply_filters('cluster_one_hilfetexte', array());
    if (is_array($c1_fremd)) {
        foreach ($c1_fremd as $c1_k => $c1_v) {
            $c1_k = strtolower(trim((string) $c1_k));
            if ($c1_k !== '' && is_string($c1_v) && $c1_v !== '' && empty($aus[$c1_k])) { $aus[$c1_k] = $c1_v; }
        }
    }
    return $aus;
}
}

if (!function_exists('cluster_one_help_for_area')) {
/* v0.7.1: Die Hilfe zu einem BEREICH. Bereiche heissen "Johari (Wilde)" oder
   "Kruse" — der Name traegt den Schluessel, aber nicht als ganzes Wort.
   Deshalb wird geprueft, ob ein bekannter Name IM Bereichsnamen vorkommt.

   Benennt ein SysOp seinen Bereich um, verschwindet das Fragezeichen. Das ist
   richtig so: Ein umbenannter Bereich gehoert ihm, nicht der Blaupause — und
   eine Erklaerung, die dann noch von Kruse spraeche, waere falsch. */
function cluster_one_help_for_area(string $name): string {
    if ($name === '') {
        return '';
    }
    /* Zuerst die BESCHREIBUNG DES BEREICHS selbst (seit 0.6.4, von der
       Blaupause gesetzt und vom SysOp aenderbar). Sie ist der richtige Text an
       dieser Stelle — sie sagt, wofuer der Bereich da ist, nicht wer der
       Namensgeber war. Und sie gehoert dem SysOp: Hat er sie umgeschrieben,
       steht seine dort, nicht unsere. */
    $meta = cluster_one_area_meta($name);
    if (trim((string) ($meta['desc'] ?? '')) !== '') {
        return cluster_one_help_bubble((string) $meta['desc']);
    }
    /* v0.7.6: LEEREN HEISST ZURUECK AUF DEN VORSCHLAG, nicht weg damit.

       Christians Befund: Loescht man die Beschreibung wieder, verschwand das
       Fragezeichen — bei Reaktoren und Tagebuechern ganz, weil es dort keinen
       Namensgeber gibt, auf den zurueckzufallen waere.

       Ich hatte "leer" als "keine Beschreibung" gelesen. Seine Lesart ist
       besser und entspricht dem, was wir beim Guru-Text schon tun: Die Vorgabe
       gilt, bis jemand sie ueberschreibt — und wieder, sobald er sein Feld
       raeumt. Nichts geht verloren, und der Knopf bleibt, wo er war. */
    /* v0.9.58: GEWACHT, seit die Blaupause nach Kranz gezogen ist. Es ist der
       EINZIGE Aufruf, mit dem Feynman noch aus einem anderen Organ liest — und
       er liest nur Text: die Vorschlagsbeschreibungen der Bereiche, die hier
       als Rueckfall fuer das Fragezeichen dienen. Ohne Kranz gibt es diesen
       Rueckfall nicht, und das ist in Ordnung: Dann faellt es eine Stufe
       weiter auf den Namensgeber. Ein Organ, das reiner Text ist, soll auch
       ohne seine Nachbarn stehen koennen. */
    $bp = function_exists('cluster_one_blueprint_areas') ? cluster_one_blueprint_areas() : array();
    if (isset($bp[$name]) && trim((string) $bp[$name]) !== '') {
        return cluster_one_help_bubble((string) $bp[$name]);
    }
    /* Sonst der Namensgeber, falls einer im Bereichsnamen steckt —
       "Johari (Wilde)" traegt den Schluessel in Klammern. Benennt jemand um,
       bleibt es leer: Ein umbenannter Bereich gehoert ihm, und eine Erklaerung,
       die dann noch von Kruse spraeche, waere falsch. */
    $n = ' ' . strtolower($name) . ' ';
    foreach (array_keys(cluster_one_help_texts()) as $k) {
        if (preg_match('~[^a-z]' . preg_quote($k, '~') . '[^a-z]~', $n)) {
            return cluster_one_help($k);
        }
    }
    return '';
}
}

if (!function_exists('cluster_one_namenstabelle')) {
/* v1.16.0 (11.10.2026) — DIE EINSAMMLUNG. SIEBEN ZEILEN WAREN SCHON ZWEIMAL DA.
 *
 * Der Satz steht seit 0.9.66 in dieser Datei: JEDES ORGAN NENNT DAS SEINE
 * SELBST. Am 11.09. zog die Tabelle aus feynman.php in den Boden — und von
 * den Abholungen kamen ZWEI mit, Kleinrock und Crockford. Die anderen sind
 * stehengeblieben. Gemessen am 11.10. im ausgelieferten 0.2.40:
 *
 *     bismarck.php    cluster_one_bismarck_steckbrief      NIEMAND RIEF SIE
 *     bismarck.php    cluster_one_sokrates_steckbrief      NIEMAND RIEF SIE
 *     platon.php      cluster_one_platon_steckbrief        NIEMAND RIEF SIE
 *     platon.php      cluster_one_wilde_steckbrief         NIEMAND RIEF SIE
 *     platon.php      cluster_one_cunningham_steckbrief    NIEMAND RIEF SIE
 *     modi.php        cluster_one_popper_steckbrief        NIEMAND RIEF SIE
 *     winsor.php      cluster_one_winsor_steckbrief        NIEMAND RIEF SIE
 *
 * SIEBEN Haeuser hatten ihre Haelfte getan. Der Boden hielt daneben seine
 * eigene Abschrift derselben sieben Zeilen und las nur sie.
 *
 *     Ein Umzug, der die Adresse mitnimmt, ist ein Umzug. Einer, der sie
 *     wegwirft, ist ein Abriss.                      (Umberto, 10.09.)
 *
 * Nachgemessen, bevor etwas behauptet wird: die sieben Paare sind WORTGLEICH.
 * Nichts ist auseinandergelaufen. Und der Katalog hat nichts gekostet —
 * gettext fasst gleiche msgid zusammen, egal aus wie vielen Dateien sie kommt.
 *
 *     Zwei Kopien einer Wahrheit sind keine Gefahr, solange niemand eine
 *     davon anfasst. Sie sind eine Wette darauf, dass niemand sie anfasst.
 *
 * WAS ES WIRKLICH KOSTETE, UND DAS IST KEIN GESCHMACK: auf einem Knoten
 * OHNE das Organ versprach die Abschrift weiter einen Ort. Cluster One Light
 * traegt feynman.php (die Tabelle wird also gezeigt) und traegt bismarck.php,
 * platon.php, modi.php, winsor.php NICHT. Dort standen SIEBEN Zeilen ueber
 * Chats, Forum, Kuehlbecken und Modi, die es auf diesem Knoten nicht gibt.
 * Das ist genau der Fehler, den die Galvani-Zeile seit 0.9.79 nicht mehr
 * macht — nur siebenmal und ohne die Spalte, die ihn dort entschaerft.
 *
 * DIE REIHENFOLGE BLEIBT HIER. Die Tabelle liest sich von den drei Bereichen
 * ueber die Organe zu den Werkzeugen; das ist eine Aussage und kein Zufall.
 * Ein Sammler, der nur anhaengt, haette sie zerlegt.
 *
 *     Eine Liste, deren Reihenfolge etwas sagt, kann ihre Eintraege
 *     abgeben und ihre Reihenfolge nicht.
 *
 * WARUM HIER ORGANNAMEN STEHEN DUERFEN UND FREMDE FUNKTIONSNAMEN NICHT:
 * cluster_one_steckbrief_von() nimmt einen Funktionsnamen — aber einen, der
 * aus MEINER Organliste folgt, derselben, die cluster_one_hier() fuehrt und
 * cluster_one_menue_wenn() abfragt. Fuer Pakete, die nicht meine Organe sind
 * (Umberto, Turing, Pacioli), gilt das nicht: deren Namen darf dieser Boden
 * nicht kennen. Sie melden sich unten durch die Naht.
 *
 *     Der Boden holt bei den Organen, deren Namen er kennen darf. Er laesst
 *     sich bringen von den Paketen, deren Namen er nicht kennen darf.
 *
 * WAS NOCH INLINE STEHT, und warum, Stand 11.10.:
 *
 *     Kasper · Kranz · Kruse   gemessen: leben nur in Boden-Dateien. MEINE.
 *     Galvani                  gibt es nicht mehr. Geschichte, und die
 *                              gehoert dem Boden, nicht einem Organ.
 *     Gregor · Archimedes      leben in faust.php          -> faust.php
 *     Feynman · Derrida        leben in feynman.php        -> feynman.php
 *     Eddington                lebt in bismarck/modi.php   -> modi.php
 *     Nemo · Johari            NICHT GEMESSEN: Treffer in kern.php UND in
 *                              modi.php/platon.php. Nicht geraten. */
function cluster_one_namenstabelle(): array {
    return array_values(array_filter(array_merge(array(
        cluster_one_steckbrief_von('cluster_one_bismarck_steckbrief'),
        cluster_one_steckbrief_von('cluster_one_sokrates_steckbrief'),
        cluster_one_steckbrief_von('cluster_one_platon_steckbrief'),
        /* v0.9.66: KLEINROCKS ZEILE WOHNT JETZT IN KLEINROCK. Sie steht hier
           an derselben Stelle wie immer, sie steht nur nicht mehr HIER
           geschrieben — cluster_one_kleinrock_steckbrief() liefert sie.

           Grund: Cluster One Light hat kein Feynman und hatte damit keine
           einzige Fragezeichen-Blase, auch nicht die des einzigen Organs, um
           das es dort geht. Christian am 22.08.2026: "Der einzige Name, den
           Cluster One Light braucht, ist Kleinrock selbst."

           Der bequeme Weg waere gewesen, den Text nach Light zu kopieren.
           Genau davor warnt der Kopf dieser Datei: "Die Texte stehen an EINER
           Stelle … sonst haetten wir zwei Kopien einer Wahrheit." Also zieht
           der Text dorthin, wo beide Haeuser ihn haben — ins Organ, das er
           beschreibt. Dieselbe Regel wie bei den Ruecksetzungen: JEDES ORGAN
           NENNT DAS SEINE SELBST. Posten J (blase.php) bezeugt beides: dass
           die Blase in beiden Haeusern ankommt UND dass es den Text nur
           einmal gibt. */
        cluster_one_steckbrief_von('cluster_one_kleinrock_steckbrief'),
        /* v1.9.14 (11.09.2026) — UMBERTOS ZEILE, UND SIE HAT SEIT DEM 09.09.
           GEFEHLT.
           Umberto meldet seit zwei Tagen, dass er Feld 4 seines Steckbriefs
           nicht schreiben kann. Ich habe ihm gestern geantwortet, die Funktion
           cluster_one_steckbrief_von() stehe jetzt im Boden und sein Fall sei
           damit erledigt. Er hat widersprochen, und er hatte recht:

               Eine Funktion, die es gibt, ist keine Zeile, die mich aufruft.

           Nachgemessen im ganzen 1.9.13-Baum: 'cluster_one_umberto_steckbrief'
           null Treffer. Die Funktion war da, der AUFRUF war nie da. Ich hatte
           eine Haelfte gebaut und die ganze Sache gemeldet.

           BENANNTE ABHAENGIGKEIT FUER 7b: der Name dieser Zeichenkette traegt
           den Hausnamen. Wird Umberto zu Crockford, muss BEIDES im selben Zug
           wandern — die Funktion drueben und diese Zeile hier. Sie steht
           deshalb hier aufgeschrieben und nicht nur im Kopf.

           v1.12.12 (23.09.2026) — DER ZUG IST DA, UND ICH HAETTE DIE ZEILE
           BEINAHE STEHEN LASSEN. Crockford hat seine Funktion am 23.09.
           umbenannt; diese Zeile rief fuenf Tage spaeter immer noch den
           alten Namen. Gefunden hat es nicht mein Gedaechtnis, sondern eine
           Suche nach „umberto" im fertigen Paket, zwei Minuten vor dem
           Packen.

               Ein Zettel, den man an die Stelle schreibt, ist besser als
               keiner. Er ersetzt die Kontrolle nicht — er sagt ihr nur,
               wo sie hinsehen soll.

           cluster_one_steckbrief_von() ist fail soft: ein Name ohne
           Funktion gibt array(). Der Fehler waere also nicht abgestuerzt.
           Crockfords Kachel waere aus der Kasper-Liste verschwunden, und
           niemand haette es gesagt. */
        cluster_one_steckbrief_von('cluster_one_crockford_steckbrief'),
        array('Gregor', 'Papst Gregor XIII.', '1502–1585',
              __('The calendar. He decreed the reform of 1582 that we still count by.', 'cluster-one-bismarck'),
              '',
              __('The calendar area makes coordinating work together easier. It is named after Pope Gregory XIII, whom we have to thank for the calendar we use today. For meetings that reach beyond this node, Gregor becomes Archimedes — of whom it is said: “Give me a fixed point and I will move the world.” Your own node is that fixed point: from here something can be set in motion that reaches beyond the contacts you already have.', 'cluster-one-bismarck')),
        /* v0.9.79 — DIE WERKZEUGE STEHEN MIT DRIN, ABER ALS DAS, WAS SIE SIND.
           Kasper und Galvani sind kein Bereich, den jemand oeffnet: Kasper ist
           Boden, Galvani ist Umbauwerkzeug und ohne Turing gar nicht sichtbar.
           Eine Zeile, die einem Leser einen Ort verspricht, den er auf seinem
           Knoten nicht findet, waere schlechter als keine Zeile — deshalb steht
           es in der Spalte „Was der Name NICHT bedeutet".

           Umberto, Turing und Pacioli fehlen hier mit Absicht: sie sind eigene
           Plugins, und seit 0.9.66 gilt JEDES ORGAN NENNT DAS SEINE SELBST
           (siehe cluster_one_kleinrock_steckbrief weiter oben). Ihre Zeilen
           ziehen dorthin, sobald der Sammel-Mechanismus steht. */
        array('Kasper', 'Kasperle', __('a puppet, since the 18th century', 'cluster-one-bismarck'),
              __('The register. He asks into the audience, and the answer is a chorus.', 'cluster-one-bismarck'),
              __('Not an area you open. Kasper is floor: he asks which modules are answering, so that no module has to guess. Nobody uses him; everything uses him.', 'cluster-one-bismarck'),
              __('When Cluster One becomes four separate plugins, shared code lies on a node several times over, and the modules have to know about one another. Kasper asks one question — “are you all there?” — and every organ that has loaded answers with its name, its version and its host. Before that, the same thing was asked twenty-one times with function_exists(), and each of those asked after a symptom rather than the fact. A symptom can move house. A fact cannot.', 'cluster-one-bismarck')),
        /* v1.9.14 (11.09.2026) — GALVANI GIBT ES SEIT DEM 27.08. NICHT MEHR,
           UND DIESE ZEILE HAT IHN ZWEI WOCHEN WEITER VERSPROCHEN.
           Gemeldet von Umberto am 11.09. — er hat die Zeile im Katalog
           gefunden und sich ausdruecklich geweigert, sie zu beurteilen,
           weil er nicht wusste, was feynman.php prueft. Gemessen: es gibt
           keine galvani.php im Baum, keinen Menuepunkt, und landkarte.php
           Zeile 10 sagt „Diese Datei ist die Haelfte von galvani.php, die
           den Umbau ueberlebt hat."

           Es war also weder ein Platten- noch ein Laufzeitfehler. Der
           Gegenstand ist weg, und die Frage nach dem richtigen Wort war
           die falsche Frage.

           UND FUENFZEHN ZEILEN WEITER OBEN STEHT DIE REGEL, DIE DIESE ZEILE
           GEBROCHEN HAT — mein eigener Kommentar, v0.9.79: „Eine Zeile, die
           einem Leser einen Ort verspricht, den er auf seinem Knoten nicht
           findet, waere schlechter als keine Zeile."

               Eine Datei, die eine Regel aufschreibt und sie fuenfzehn
               Zeilen spaeter bricht, hat die Regel nicht vergessen. Sie
               hat sie an einem Namen aufgeschrieben, der sich dann
               bewegt hat.

           CHRISTIANS ENTSCHEIDUNG (Weg B): die Zeile BLEIBT und sagt die
           Wahrheit. Der Name bleibt lesbar, der Ort wird nicht versprochen.
               Ein Umzug, der die Adresse mitnimmt, ist ein Umzug. Einer,
               der sie wegwirft, ist ein Abriss.        (Umberto, 10.09.)
           Galvani ist nicht gescheitert, er ist aufgegangen.

           KEIN MENUEPFAD auf die Landkarte, obwohl sie hier naheliegt:
           sie wohnt in einem fremden Haus, und eine Adresse in einem
           fremden Haus veraltet lautlos (Maillart, Fund 4 vom 11.09.).
           Die Zeile sagt, WAS die Landkarte ist, nicht WO sie steht. */
        array('Galvani', 'Luigi Galvani', '1737–1798',
              __('The switch at the current. His twitching frog legs are where Mary Shelley got the spark from.', 'cluster-one-bismarck'),
              __('Not an operating switch, not a settings page — and on this node no longer a place at all. Galvani existed until August 2026 and was then taken apart.', 'cluster-one-bismarck'),
              __('Galvani switched single organs off so you could see what fell over without them. On 27 August 2026 it turned out, while reading its own page, that nothing of the switches was left: one after another they had been replaced by better answers, until only the reading remained. That half survived and is now the map — it shows which organ runs out of which plugin, without having to switch anything off. The name stands here because it belongs to the history of this house, not because there is a place to go to.', 'cluster-one-bismarck')),
        array('Archimedes', 'Archimedes von Syrakus', 'ca. 287–212 v. Chr.',
              __('Meetings across nodes. A fixed point, and a lever long enough.', 'cluster-one-bismarck'),
              '',
              ''),
        cluster_one_steckbrief_von('cluster_one_winsor_steckbrief'),
        array('Feynman', 'Richard Feynman', '1918–1988',
              __('Knowledge. He explained things until they were simple, and said plainly when he did not know.', 'cluster-one-bismarck'),
              '',
              __('The Feynman menu is the knowledge area: here you find what the plugin can do, how it is set up, and why it is built the way it is. Its namesake is the physicist Richard Feynman, who was especially popular with his students because he could explain things unusually well. Whether I live up to that I do not know. But it is what I am aiming for.', 'cluster-one-bismarck')),
        array('Kranz', 'Gene Kranz', '*1933',
              __('Settings. The flight director who decided under pressure and afterwards said who had decided what.', 'cluster-one-bismarck'),
              '',
              __('This is where the node is set up: name, access keys, who may enter, which AIs take part. Nothing is written here and nothing is discussed — here it is decided how things run. Its namesake is Gene Kranz: during the Apollo 13 mission three astronauts faced death, and Kranz was the lead flight director whose team brought them back to Earth alive. A control centre steers everything and flies nothing itself. That is exactly what this area does.', 'cluster-one-bismarck')),
        array('Eddington', 'Arthur Eddington', '1882–1944',
              __('The working room. In 1919 he travelled out and measured, to test another man’s theory — the ethic of the test suite in one person.', 'cluster-one-bismarck'),
              __('A flawless record. In 1935 he publicly ridiculed Chandrasekhar’s work and held the field back for decades.', 'cluster-one-bismarck'),
              ''),
        array('Kruse', 'Peter Kruse', '1955–2015',
              __('The area for what acts from outside. Intuition forms at the edge of overload — and goes stale when the world moves on.', 'cluster-one-bismarck'),
              __('Only one thing. He appears twice in this house: super-additive intelligence is why the chat is built the way it is; the area is named for staying awake.', 'cluster-one-bismarck'),
              ''),
        array('Derrida', 'Jacques Derrida', '1930–2004',
              __('This tab. He asked not only what stands in a text but above all what does not — and why not.', 'cluster-one-bismarck'),
              '',
              __('Jacques Derrida was a French philosopher who worked with deconstruction from the 1960s onwards. He asked not only what stands in a text — everyone before him did that — but above all what does NOT stand in it, and why not. Cluster One is a plugin of the Prometheus class. Most applications assume you already know your problem. But what if you do not yet know which projects you want to build? What if it becomes clear that you need people for it whom you do not know? And what if your project is finished but you lack the knowledge to show it to the outside world? Prometheus means “the one who thinks ahead”. His brother Epimetheus thought afterwards. Cluster One starts earlier — where the question that fits the answers is still missing.', 'cluster-one-bismarck')),
        array('Nemo', 'Verne’s captain — and Latin for “nobody”', '',
              __('The ordinary room. No particular way of working; the name says that there is none.', 'cluster-one-bismarck'),
              '',
              ''),
        array('Johari', 'Joseph Luft & Harry Ingham', '',
              __('Self-portrayal. Not one person but two first names in one word — for the place where self-image and the view of others meet.', 'cluster-one-bismarck'),
              '',
              ''),
        cluster_one_steckbrief_von('cluster_one_cunningham_steckbrief'),
        cluster_one_steckbrief_von('cluster_one_popper_steckbrief'),
        cluster_one_steckbrief_von('cluster_one_wilde_steckbrief'),
    /* ---- DIE NAHT. Hier endet, was der Boden kennt. ----------------------
       Alles oben steht in einer Reihenfolge, die etwas sagt, und traegt
       Namen, die dieser Boden kennen darf. Alles ab hier kommt von einem
       Paket, dessen Namen er NICHT kennen darf — Umberto, Turing, Pacioli,
       und was noch kommt.

       SIE HAENGEN AN. Das ist keine Nachlaessigkeit, sondern die einzige
       Stelle, die sich ohne Wissen rechtfertigen laesst:

           Ein Sammler, der Unbekanntes wegwirft, verschweigt es. Einer, der
           es anhaengt, nennt es — an der einzigen Stelle, die er ohne
           Wissen begruenden kann: am Ende.

       FORM: eine Liste von Zeilen, jede sechs Spalten —
       Name · Person · Lebensdaten · warum dieser Name · was er NICHT
       meint · Langtext. Namen werden nicht uebersetzt, Begruendungen schon.

           add_filter('cluster_one_namensbriefe', function (array $z) {
               $z[] = array('Pacioli', 'Luca Pacioli', '1447–1517',
                            __('...', 'cluster-one-pacioli'), '', '');
               return $z;
           });

       DER RUECKFALL IST array() UND ER IST BENANNT (R-LEIHE-1): antwortet
       niemand, bleibt die Tabelle genau so lang wie oben. Kein Paket, keine
       Zeile, kein Loch.

       WAS DIE NAHT NICHT TUT: sie sortiert nicht, und sie prueft die Spalten
       nicht. Eine Zeile mit vier Spalten kommt vierspaltig an. Das ist die
       bezeugte Grenze und keine Schlamperei — wer sortiert, entscheidet
       ueber fremde Zeilen, und wer prueft, braucht einen Begriff von
       „richtig", den nur das Paket selbst hat. */
    ), (array) apply_filters('cluster_one_namensbriefe', array()))));
}
}

if (!function_exists('cluster_one_steckbrief_von')) {
/* v0.6.9: DIE NAMENSTABELLE.

   Christians Wunsch: Name, Person, Lebensdaten — und WARUM er ihn gewählt hat.
   Dazu eine Spalte, die es sonst nirgends gibt: was der Name NICHT meint.

   Sie entstand aus einer Meinungsverschiedenheit. Er wollte Galen Winsor
   schlecht wegkommen lassen; ich habe widersprochen, weil die Stimme dieses
   Hauses BEZEUGT UND NICHT URTEILT — das steht wörtlich über jeder
   Systemnachricht. Ein Verriss in der Namenstabelle wäre die eine Stelle, an
   der das Plugin gegen sich selbst spricht.

   Also: belegte Tatsachen nennen, das Urteil dem Leser lassen. Das wirkt
   härter als jedes Adjektiv. Der Präzedenzfall stand schon in why.md, bei
   Bismarck — "die Warnung, die im Namen mitkommt".

   Namen werden nicht übersetzt, Begründungen schon. */
/* v1.0.4 — DER SAMMLER. Seit 0.9.66 nennt jedes Organ das Seine selbst, damit
   der Text nur einmal existiert. Der Aufruf stand aber NACKT da, und damit war
   der schoene Gedanke eine Kopplung: fehlt das Organ (Light ohne Kleinrock,
   oder Galvani hat es abgeschaltet), stirbt die ganze Namenstabelle — und mit
   ihr cluster_one_help_texts(), also JEDES FRAGEZEICHEN IM HAUS.

   Gefunden am 26.08.2026 beim ersten echten Schnitt, von kreuzprobe.php im
   Stufe-2-Lauf: dreizehn Szenen, ein Fatal, eine Ursache.

     Ein Sammelmechanismus, der nicht mit dem Fehlen rechnet,
     ist eine Kopplung mit einem hoeflichen Namen.

   Einmal geriegelt statt an jeder Fundstelle — und das ist zugleich der Weg,
   auf dem die Tabelle auf die Lights aufgeteilt wird: jedes Organ liefert seine
   Zeile, wer nicht da ist, liefert nichts. */
function cluster_one_steckbrief_von(string $fn): array { return function_exists($fn) ? (array) $fn() : array(); }
}

/* v1.9.14 (VERTRAG 26, 10.09.2026) — DAS REGISTER DER PRODUKTE.
 *
 * WOFUER: Der oberste Menuepunkt soll sagen, WAS auf diesem Knoten liegt.
 * Dafuer muss der Boden wissen, wie viele PRODUKTE da sind — und das ist
 * etwas anderes als „wie viele Organe".
 *
 *     Ein Produkt ist ein Wirt. Ein Organ ist eine Zunge.
 *     Wer Zungen zaehlt, zaehlt auf dem Monolithen sieben Sprecher.
 *
 * WOHER DIE ZAHL KOMMT: aus Kasper, und zwar aus dem WIRT — dem
 * Plugin-Ordner, den Kasper seit v0.9.75 je Organ ERMITTELT und nicht
 * entgegennimmt. Kaspers eigene Koernung sind Organe; hier werden sie zu
 * Wirten zusammengefasst, und das ist kein Widerspruch, sondern eine
 * Auswertung seiner Tabelle.
 *
 * WOHER DER NAME KOMMT: aus einer ANMELDUNG, nie aus dem Ordnernamen.
 *
 *     Ein Ordnername ist ein Pfad. Ein Anzeigename ist eine Zusage.
 *     Wer den einen aus dem anderen rechnet, hat geraten — und das Raten
 *     faellt erst auf, wenn ein Produkt anders heisst als sein Ordner.
 *
 * R-BODEN-2 sagt es allgemein: die Einstufung liest die ROLLE einer
 * Zeichenkette, nicht ihr Aussehen. Umberto hat am 07.09. gemessen, dass
 * Ableiten in BEIDE Richtungen luegt.
 *
 * DIE ORDNUNGSZAHL IST EBENFALLS EINE EIGENSCHAFT DES PRODUKTS. Sie aus dem
 * Namen oder dem Ordner zu rechnen waere derselbe Fehler ein zweites Mal.
 *
 * WAS DIESES REGISTER NICHT KENNT: Seiten, Slugs, Rueckrufe. Es weiss, WER
 * da ist und wie er heisst. Damit kann es nicht veralten, wenn ein Produkt
 * seine Seiten umbaut.
 *
 * BENANNTE GRENZE, und sie ist Kaspers: liegt die aufrufende Datei nicht
 * unter WP_PLUGIN_DIR, bleibt der Wirt LEER. Leer heisst „nicht
 * feststellbar", nicht „keiner" — solche Eintraege werden NICHT gezaehlt
 * und nicht geraten.
 *
 * Rueckgabe: wirt => angemeldeter Name (Zeichenkette, ggf. leer),
 *            sortiert nach Ordnungszahl, dann nach Name.
 */
/* v1.9.14 — EIN MELDEWEG FUER DAS PRODUKTREGISTER.
 *
 * Maillart, 10.09.: „Ein Zug, der an vier Stellen dasselbe Ergebnis aus
 * verschiedenen Ursachen erzeugt, ist nicht falsch — er ist stumm. Und
 * stumm ist die Form, in der ein richtiges Ergebnis morgen ein falsches
 * deckt."
 *
 * WARUM BEIDES — Warnung UND Zahl: _doing_it_wrong haengt an WP_DEBUG. Auf
 * einem Knoten im Betrieb waere die Meldung stumm, und eine Warnung, die
 * niemand sieht, ist Dekoration. Das ist Kaspers Begruendung bei
 * cluster_one_kasper_zu_frueh(), woertlich uebernommen — und dieselbe
 * Auflage gilt: HOECHSTENS EIN SCHREIBVORGANG JE SEITENAUFRUF. Eine Zahl,
 * die auf einem Lesepfad bei jedem Aufruf schreibt, waere ihr eigener
 * Schaden.
 *
 * EIN WEG FUER VIER URSACHEN, und die Ursache steht im TEXT, nicht in vier
 * Zaehlern: vier Zahlen waeren vier Wahrheiten ueber denselben Zustand.
 */
/* v1.9.14 (Maillart, Punkte 10 und 11) — DIE SAMMLUNG.
 *
 * ZWEI FUNDE AN EINER FUNKTION, und der erste ist ein Kommentar, der seine
 * eigene Zeile falsch beschreibt:
 *
 *     'was' => $c1_griff,   /* die LETZTE Ursache dieses Aufrufs *\/
 *
 * Wegen `static $c1_schon` kehrt die Funktion nach dem ersten Schreibvorgang
 * sofort zurueck. Gespeichert wurde also immer die ERSTE Ursache, und der
 * Kommentar behauptete die letzte. Niemand haette das an der Zahl gemerkt.
 *
 *     Ein Kommentar, der seine eigene Zeile falsch beschreibt, wird
 *     geglaubt — er steht naeher an ihr als jeder Zeuge.
 *
 * DER ZWEITE: fuenf Ursachen teilten sich EIN Feld. Wer 'ohne-slug' sucht,
 * findet ihn nur, wenn er zufaellig der erste war.
 *
 *     Fuenf Ursachen, fuenf Griffe. Ein Feld, das fuenf Dinge halten soll,
 *     haelt das erste und verschweigt vier.
 *
 * GELOEST OHNE EINEN ZWEITEN SCHREIBVORGANG: die Ursachen dieses
 * Seitenaufbaus werden hier gesammelt und beim EINEN Schreibvorgang
 * vollzaehlig mitgegeben. Ein Weg, ein Schreibvorgang, fuenf Zahlen.
 *
 * EINE WAHRHEIT, ZWEI LESER: diese Funktion ist der einzige Schreibweg in
 * die Sammlung; wer sie ohne Argument ruft, liest nur (P1-12). */
/* v1.12.0 — DIESER RIEGEL HAT GEFEHLT, und W64s neue Zeile hat es beim
   ERSTEN Lauf gefunden: eine zweite Kopie dieser Datei starb an
   „Cannot redeclare cluster_one_produkte_stumm_sammlung()".

       Wer in zwei Tagen zehn Funktionen schreibt und keine einzige
       riegelt, hat die Regel des Hauses gekannt und nicht angewandt.

   Paciolis Zeile misst das ANKOMMEN, indem sie eine ZWEITE Kopie laedt —
   und genau deshalb sieht sie, was kein Zeuge sah, der nur eine laedt. */
if (!function_exists('cluster_one_produkte_stumm_sammlung')) {
    function cluster_one_produkte_stumm_sammlung(?string $c1_griff = null, string $c1_was = ''): array {
        static $c1_alle = array();
        if ($c1_griff !== null) {
            if (!isset($c1_alle[$c1_griff])) {
                $c1_alle[$c1_griff] = array('n' => 0, 'text' => $c1_was);
            }
            $c1_alle[$c1_griff]['n']++;
        }
        return $c1_alle;
    }
}

/* v1.12.0 — DIESER RIEGEL HAT GEFEHLT, und W64s neue Zeile hat es beim
   ERSTEN Lauf gefunden: eine zweite Kopie dieser Datei starb an
   „Cannot redeclare cluster_one_produkte_stumm()".

       Wer in zwei Tagen zehn Funktionen schreibt und keine einzige
       riegelt, hat die Regel des Hauses gekannt und nicht angewandt.

   Paciolis Zeile misst das ANKOMMEN, indem sie eine ZWEITE Kopie laedt —
   und genau deshalb sieht sie, was kein Zeuge sah, der nur eine laedt. */
if (!function_exists('cluster_one_produkte_stumm')) {
    function cluster_one_produkte_stumm(string $c1_griff, string $c1_was): void {
        static $c1_schon = false;
        cluster_one_produkte_stumm_sammlung($c1_griff, $c1_was);
        /* DER GRIFF STEHT VORNE IN DER WARNUNG, nicht nur im gespeicherten Wert.
           Sonst traegt ihn genau der Weg nicht, den der Entwickler liest — und
           ein Zeuge, der auf „nichts ueber DIESE Sache" prueft, kann ihn dort
           nicht finden. Ein Griff, drei Orte, eine Quelle. */
        if (function_exists('_doing_it_wrong')) {
            _doing_it_wrong('cluster_one_produkte',
                '[' . $c1_griff . '] ' . $c1_was, '1.9.14'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Entwicklermeldung an _doing_it_wrong(): kein Seiteninhalt; WordPress legt sie selbst durch wp_kses (wp_trigger_error)
        }
        /* v1.12.4 (17.09.2026) — DER SCHREIBVORGANG ZIEHT ANS ENDE DES AUFRUFS.
           HOLLERITH HAT ES GEFUNDEN, und der Beweis dafuer steht seit dem
           10.09. sechzig Zeilen weiter oben — in einem Kommentar von MIR:

               „GELOEST OHNE EINEN ZWEITEN SCHREIBVORGANG: die Ursachen dieses
                Seitenaufbaus werden hier gesammelt und beim EINEN
                Schreibvorgang VOLLZAEHLIG mitgegeben."

           Vollzaehlig war sie nie. Der eine Schreibvorgang stand bei der
           ERSTEN Ursache, und zu diesem Zeitpunkt enthaelt die Sammlung genau
           einen Eintrag: den gerade hinzugefuegten. Alles Spaetere desselben
           Aufrufs landete in der Sammlung und wurde nie gebucht.

           UND DAS FELD SAGTE ES FALSCH:
               'zaehlt-je-griff' => 'ursachen-insgesamt-je-griff'
           Gezaehlt wurde die ERSTE Ursache je Seitenaufruf. Auf Christians
           Knoten stand am 16.09. „[ohne-cap] => 1", waehrend FUENF Produkte
           betroffen waren. Ich habe daraus das Richtige geschlossen — aber nur,
           weil ich den static kannte. Wer nur die Zahl liest, liest „eines".

               Ein Etikett, das etwas anderes zaehlt als es sagt, ist
               schlimmer als eine falsche Zahl. Es sagt sie naemlich an.

           Und drei Absaetze ueber der falschen Zeile steht mein eigener Satz
           dazu, am 10.09. ueber DIESELBE Funktion geschrieben:

               Ein Kommentar, der seine eigene Zeile falsch beschreibt, wird
               geglaubt — er steht naeher an ihr als jeder Zeuge.

           Ich habe ihn aufgeschrieben und im naechsten Absatz noch einmal
           gemacht.

           BENANNTE GRENZE, und sie ist ein Tausch: gebucht wird jetzt erst bei
           'shutdown'. Stirbt der Aufruf vorher, steht nichts in der Tabelle —
           vorher stand dort etwas Unvollstaendiges. Der LAUTE Weg ist davon
           nicht beruehrt: _doing_it_wrong() feuert weiter bei JEDER Ursache,
           sofort. Nur die Zusammenfassung wartet, bis sie vollstaendig ist.
               Eine Auskunft, die zu frueh kommt, ist nicht frueher da.
               Sie ist kuerzer. */
        if ($c1_schon) {
            return;
        }
        $c1_schon = true;
        if (function_exists('add_action')) {
            add_action('shutdown', 'cluster_one_produkte_stumm_buchen', 0);
            return;
        }
        /* Ohne WordPress (Zeugenstand): sofort buchen, sonst bucht niemand. */
        cluster_one_produkte_stumm_buchen();
    }
}

/* v1.12.4 — DER EINE SCHREIBVORGANG, jetzt an seiner richtigen Stelle.
   Eigene Funktion, weil 'shutdown' einen Namen braucht und keine Schliessung:
   eine anonyme Funktion am Haken laesst sich von keinem Zeugen nachweisen, und
   has_filter() kann nach ihr nicht fragen.
       Ein Haken, dessen Rueckruf keinen Namen hat, ist nicht messbar.
   Und der Riegel steht davor, weil W64 genau diesen Fall gefunden hat. */
if (!function_exists('cluster_one_produkte_stumm_buchen')) {
    function cluster_one_produkte_stumm_buchen(): void {
        $c1_sammlung = cluster_one_produkte_stumm_sammlung();
        if (!$c1_sammlung) {
            return;
        }
        $c1_erste = (string) array_key_first($c1_sammlung);
        $c1_griff = $c1_erste;
        $c1_was   = (string) ($c1_sammlung[$c1_erste]['text'] ?? '');
        if (function_exists('update_option') && function_exists('get_option')) {
            /* MAILLART, 10.09. — DIE ZAHL WAR DAS ENDE DES WEGES, UND DORT KAM DIE
             * URSACHE NICHT AN.
             *
             *     Die Warnung traegt die Ursache und ist im Betrieb stumm. Die
             *     Zahl ist im Betrieb sichtbar und traegt keine Ursache.
             *     Zusammen sagen sie ueberall die halbe Sache.
             *
             * Das ist meine eigene Pruefung, auf meine eigene Zahl angewandt: ich
             * hatte die Zahl gebaut, weil „eine Warnung, die niemand sieht,
             * Dekoration ist". Eine Zahl, die nicht sagt, wovon sie redet, ist
             * eine Auskunft, die niemand benutzen kann.
             *
             * NICHT vier Zaehler — ein Weg, ein Schreibvorgang, und der Wert
             * traegt die letzte Ursache mit. Eine Wahrheit, zwei Felder.
             *
             * UND DIE EINHEIT STEHT DABEI: wegen static zaehlt 'n' die
             * SEITENAUFRUFE MIT MINDESTENS EINER STUMMEN URSACHE, nicht die
             * stummen Ursachen. Vernuenftige Einheit — sie stand nur nirgends. */
            /* DER NAME SAGT DIE EINHEIT, NICHT NUR DAS FELD. Maillart, 10.09.:
               die Zahl zaehlt wegen static AUFRUFE mit mindestens einer stummen
               Ursache, nicht Ursachen — „vernuenftige Einheit, sie steht nur
               nirgends". Sie steht jetzt zweimal, und das sind nicht zwei
               Wahrheiten, sondern zwei Leser: der NAME ist fuer den Menschen,
               der in der Optionstabelle sucht; das FELD 'zaehlt' ist fuer das
               Werkzeug, das den Wert liest. */
            $c1_alt = get_option('cluster_one_produkte_stumme_aufrufe', array());
            $c1_n = is_array($c1_alt) ? (int) ($c1_alt['n'] ?? 0) : (int) $c1_alt;
            $c1_griffe = (is_array($c1_alt) && isset($c1_alt['griffe']) && is_array($c1_alt['griffe']))
                ? $c1_alt['griffe'] : array();
            foreach (cluster_one_produkte_stumm_sammlung() as $c1_g => $c1_e) {
                $c1_griffe[$c1_g] = (int) ($c1_griffe[$c1_g] ?? 0) + (int) $c1_e['n'];
            }
            update_option('cluster_one_produkte_stumme_aufrufe', array(
                'n'      => $c1_n + 1,
                'zaehlt' => 'seitenaufrufe-mit-mindestens-einer-stummen-ursache',
                'griffe' => $c1_griffe,
                'zaehlt-je-griff' => 'ursachen-insgesamt-je-griff',
                'erste'  => $c1_griff,          /* die ERSTE Ursache dieses Aufrufs */
                'text'   => $c1_was,
            ), false);
        }
    }
}

/* ===========================================================================
   DER MERKZETTEL DES REGISTERS — v1.12.0.

   W60 WELT 2b stand seit gestern ROT, und die Zeile war meine eigene:
   „und GENAU EINMAL — eine Ursache, eine Meldung, egal wie viele Leser das
   Register hat." Seit das Dach zwei Haelften hat (der admin_menu-Block und
   der spaete Lauf auf 99), wurde cluster_one_produkte() ZWEIMAL je Anfrage
   gerufen — dieselbe Warnung zweimal, und 'griffe' zaehlte Ursachen MAL
   LESER, mit falscher Einheit am Etikett.

       Eine Meldung gehoert der Ursache, nicht dem Blick darauf. Wer zweimal
       hinsieht, hat nicht zwei Gruende — er hat zweimal hingesehen.

   MAILLART HAT WEG A GEWAEHLT (URTEIL XIII §3) und B und C begruendet
   verworfen: B repariert das Symptom und laesst die doppelte Rechnung
   stehen; C benennt die Einheit ehrlich um und macht die Zahl unbrauchbar.

       Eine ehrliche Zahl, aus der man die Sache nicht zurueckrechnen kann,
       ist eine Auskunft ueber das Messgeraet und nicht ueber den Knoten.
                                                     (Maillart, 11.09.)

   SEINE DREI AUFLAGEN, hier wie im Boden:
   1  ABGELEITET, NICHT PARALLEL — eine Schreibstelle, in dieser Funktion.
   2  DER GRIFF ZUM VERGESSEN IST BENANNT und wird von den Zeugen benutzt.
   3  DIE BEHAUPTUNG UEBER DIE ZEIT:

          DAS REGISTER BEHAUPTET, DASS SICH DIE ANTWORT AUF
          apply_filters('cluster_one_produkt') INNERHALB EINER ANFRAGE
          NICHT AENDERT.

      Sie ist angreifbar, und das ist der Grund, sie hinzuschreiben statt
      sie zu verstecken: zwischen dem admin_menu-Block und dem spaeten Lauf
      auf 99 koennen Haken laufen. Meldet einer dort ein Produkt NACH, baut
      die zweite Haelfte des Dachs mit der ERSTEN Antwort — richtig, wenn
      die Annahme stimmt, und still falsch, wenn nicht.

   UND DAS VERGESSEN GEHT NACH UNTEN: das Register ist aus Kaspers Tabelle
   abgeleitet. Wer das Abgeleitete vergisst und die Quelle behaelt, bekommt
   dieselbe Antwort noch einmal und haelt sie fuer frisch.

       Ein Zuruecksetzer, der auf halbem Weg stehenbleibt, ist schlimmer als
       keiner: er sieht aus, als haette er gewirkt.
   =========================================================================== */
/* v1.12.0 — DIESER RIEGEL HAT GEFEHLT, und W64s neue Zeile hat es beim
   ERSTEN Lauf gefunden: eine zweite Kopie dieser Datei starb an
   „Cannot redeclare cluster_one_produkte_merkzettel()".

       Wer in zwei Tagen zehn Funktionen schreibt und keine einzige
       riegelt, hat die Regel des Hauses gekannt und nicht angewandt.

   Paciolis Zeile misst das ANKOMMEN, indem sie eine ZWEITE Kopie laedt —
   und genau deshalb sieht sie, was kein Zeuge sah, der nur eine laedt. */
if (!function_exists('cluster_one_produkte_merkzettel')) {
    function cluster_one_produkte_merkzettel(?array $c1_setzen = null,
                                             bool $c1_vergessen = false): ?array {
        static $c1_wert = null;
        if ($c1_vergessen) { $c1_wert = null; return null; }
        if (null !== $c1_setzen) { $c1_wert = $c1_setzen; }
        return $c1_wert;
    }
}

/** Vergisst das Register UND die Tabelle, aus der es faellt. */
/* v1.12.0 — DIESER RIEGEL HAT GEFEHLT, und W64s neue Zeile hat es beim
   ERSTEN Lauf gefunden: eine zweite Kopie dieser Datei starb an
   „Cannot redeclare cluster_one_produkte_vergessen()".

       Wer in zwei Tagen zehn Funktionen schreibt und keine einzige
       riegelt, hat die Regel des Hauses gekannt und nicht angewandt.

   Paciolis Zeile misst das ANKOMMEN, indem sie eine ZWEITE Kopie laedt —
   und genau deshalb sieht sie, was kein Zeuge sah, der nur eine laedt. */
if (!function_exists('cluster_one_produkte_vergessen')) {
    function cluster_one_produkte_vergessen(): void {
        cluster_one_produkte_merkzettel(null, true);
        if (function_exists('cluster_one_kasper_vergessen')) {
            cluster_one_kasper_vergessen();
        }
    }
}

/* v1.12.0 — DIESER RIEGEL HAT GEFEHLT, und W64s neue Zeile hat es beim
   ERSTEN Lauf gefunden: eine zweite Kopie dieser Datei starb an
   „Cannot redeclare cluster_one_produkte()".

       Wer in zwei Tagen zehn Funktionen schreibt und keine einzige
       riegelt, hat die Regel des Hauses gekannt und nicht angewandt.

   Paciolis Zeile misst das ANKOMMEN, indem sie eine ZWEITE Kopie laedt —
   und genau deshalb sieht sie, was kein Zeuge sah, der nur eine laedt. */
if (!function_exists('cluster_one_produkte')) {
    function cluster_one_produkte(): array {
        $c1_gemerkt = cluster_one_produkte_merkzettel();
        if (null !== $c1_gemerkt) {
            return $c1_gemerkt;
        }
        $c1_wirte = array();
        $c1_verworfen = 0;
        foreach (cluster_one_kasper() as $c1_organ => $c1_eintrag) {
            $c1_w = (string) (is_array($c1_eintrag) ? ($c1_eintrag['wirt'] ?? '') : '');
            if ($c1_w === '') {
                /* MAILLART, 10.09.: nicht geraten ist richtig, NICHT GESAGT ist
                   es nicht. Der uebersprungene Wirt hinterliess keine Spur. */
                $c1_verworfen++;
                cluster_one_produkte_stumm('ohne-wirt',
                    'Organ ' . (string) $c1_organ . ' meldet keinen Wirt — es wird '
                  . 'nicht geraten und deshalb nicht mitgezaehlt. Der oberste '
                  . 'Menuepunkt kann dieses Produkt nicht nennen.');
                continue;
            }
            $c1_wirte[$c1_w] = true;
        }
        if ($c1_wirte === array()) {
            /* NULL IST ETWAS ANDERES ALS EINS *UND* ETWAS ANDERES ALS ZWEI.
               Auf einem laufenden Knoten sind null Produkte unmoeglich — dieser
               Block laeuft IN einem Cluster-One-Plugin. Null heisst also nicht
               „nichts da", sondern „die Messung ist gescheitert", und dafuer
               gilt derselbe Satz wie fuer den leeren Wirt:
                   Leer heisst „nicht feststellbar", nicht „keiner". */
            cluster_one_produkte_stumm('kein-produkt',
                'KEIN Produkt feststellbar' . ($c1_verworfen > 0
                    ? ' (' . $c1_verworfen . ' Organ(e) ohne Wirt)'
                    : ' — Kasper hat geschwiegen')
              . '. Das ist keine leere Liste, sondern eine gescheiterte Messung.');
            return array();
        }

        $c1_angemeldet = apply_filters('cluster_one_produkt', array());
        if (!is_array($c1_angemeldet)) {
            $c1_angemeldet = array();
        }

        $c1_aus = array();
        foreach (array_keys($c1_wirte) as $c1_w) {
            $c1_a = $c1_angemeldet[$c1_w] ?? null;
            /* NUR EINE FORM: array('name' => …, 'nr' => …, 'slug' => …). Zwei erlaubte Formen
               waeren zwei Wahrheiten ueber dieselbe Sache — und die zweite fiele
               erst auf, wenn jemand die falsche benutzt.

               MAILLART, 10.09., UND ER HAT MEINEN EIGENEN SATZ GEGEN MICH GEWENDET:
               die Form ist nicht zu streng, ihre ZURUECKWEISUNG war zu leise.
               Vorher hing die Meldung an is_array($c1_a) — also an genau der
               Bedingung, die auch das Verwerfen entschied:

                   Wenn die Meldung an derselben Bedingung haengt wie das
                   Verwerfen, wird genau das nie gemeldet, was verworfen wird.

               Ein Produkt, das 'Kleinrock' als Zeichenkette anmeldet, kam still
               als leerer Name heraus — nicht zu unterscheiden von einem, das
               nie gesprochen hat. Und das ist schlimmer als der Fall, gegen den
               H218 gebaut ist: dort schweigt einer, der nichts gesagt hat; hier
               schweigt einer, der GESPROCHEN hat. */
            if ($c1_a !== null && !is_array($c1_a)) {
                cluster_one_produkte_stumm('falsche-form',
                    'Produkt ' . $c1_w . ' hat sich in FALSCHER FORM angemeldet ('
                  . gettype($c1_a) . ' statt array). Erwartet: '
                  . "array('name' => …, 'nr' => …, 'slug' => …). Sein Name fehlt deshalb im "
                  . 'obersten Menuepunkt.');
                $c1_a = null;
                $c1_falsch = true;      /* er HAT gesprochen — nur falsch */
            } else {
                $c1_falsch = false;
            }
            /* v1.12.2 — DER SPRUNG STEHT JETZT HIER UND NICHT ZWANZIG ZEILEN
               TIEFER. GEFUNDEN AUF CHRISTIANS KNOTEN, 16.09., NACHDEM ER DEN
               LOESCHKNOPF GEDRUECKT UND DANACH NOCH EINMAL GELESEN HAT:

                   [n] => 1   [griffe] => [ohne-namen] => 1
                   Produkt turing hat sich GAR NICHT angemeldet

               Eine Zaehlung, die nach dem Loeschen wieder bei eins steht,
               ist nicht geloescht worden. Sie ist noch am Leben.

               MEINE ERSTE FASSUNG (v1.12.1) hat den Wirt aus der LISTE
               genommen und die RUEGE stehen lassen. Drei Dinge daran waren
               falsch, und jedes einzeln:

                 1 SIE WAR UNWAHR. „der oberste Menuepunkt sagt deshalb nicht,
                   was hier liegt" — er sagt es sehr wohl. turing ist kein
                   Produkt und soll nicht genannt werden.
                 2 SIE SCHRIEB. cluster_one_produkte_stumm() legt ein
                   update_option an, bei JEDEM Seitenaufruf, auf einem reinen
                   Lesepfad. Kaspers eigener Kopf nennt genau das seinen
                   eigenen Schaden.
                 3 SIE WAR STILL. Niemand haette sie gesehen, wenn Christian
                   nach dem Loeschen weggegangen waere.

               WARUM HIER GAR NICHT GEMELDET WIRD, und das ist die Entscheidung
               und nicht die Bequemlichkeit: ein Wirt ohne Anmeldung ist der
               NORMALFALL fuer ein Werkzeug. Eine Meldung, die bei jedem Aufruf
               fuer einen gesunden Zustand feuert, ist Rauschen — und Rauschen,
               das schreibt, ist Schaden.

               UND DER EINWAND, DEN ICH MIR SELBST MACHE: ein Produkt, das seine
               Anmeldung VERGISST, verschwindet damit still. Das stimmt, und die
               Antwort steht im Vertrag: DIE ANWESENHEIT DER ANMELDUNG IST DIE
               ANKUENDIGUNG. Wer nicht ankuendigt, wird nicht vermisst — und der
               Mensch sieht vier Punkte statt fuenf. Das ist die laute
               Rueckmeldung, und sie braucht keine Zeile in der Datenbank.

                   Eine Ruege an jemanden, der nichts versprochen hat,
                   ist keine Auskunft. Sie ist eine Erwartung mit Stimme.

               DER STAMMLER BLEIBT: wer gesprochen und die Form verfehlt hat
               ($c1_falsch), wird weiter gemeldet UND bleibt in der Liste.
               Er hat etwas versprochen. */
            if (null === $c1_a && !$c1_falsch) {
                continue;
            }
            $c1_name = is_array($c1_a) ? (string) ($c1_a['name'] ?? '') : '';
            $c1_nr   = is_array($c1_a) ? (int) ($c1_a['nr'] ?? 999) : 999;
            /* v1.9.14 — DER SLUG. Er sagt, WO das Produkt seine Landeseite hat.
               Er wird ANGEMELDET, nie abgeleitet — dieselbe Regel wie beim Namen
               und aus demselben Grund, nur mit einer schlimmeren Folge:

                   Ein Slug, den der Boden aus dem Produktnamen rechnet, ist
                   dasselbe Raten wie ein Name aus dem Ordner — nur zeigt er
                   nicht auf ein falsches Wort, sondern auf eine graue Seite.

               VON ZWEI SEITEN VERLANGT, und das hat ihn entschieden: das Dach
               braucht ihn fuer die Produkteintraege (VERTRAG 26, zweite Haelfte),
               und Turing braucht ihn, um aus einem fremden Haus auf eine Seite zu
               verlinken, statt einen Menuepfad zu drucken (Maillart, 10.09.).

                   Ein Feld, das von zwei Seiten unabhaengig verlangt wird, ist
                   keine Bequemlichkeit mehr. Es ist eine Luecke im Vertrag.

               BENANNTE GRENZE: der Boden prueft NICHT, ob der Slug auf eine
               angemeldete Seite zeigt. Das kann er nicht — 'admin_menu' ist hier
               noch nicht gefeuert. Er gibt weiter, was angemeldet wurde; ob die
               Seite existiert, beantwortet der LESER (menu_page_url()). Ein Boden,
               der das hier pruefte, wuerde vor dem Saal ueber den Saal reden. */
            $c1_slug = is_array($c1_a) ? (string) ($c1_a['slug'] ?? '') : '';
            /* v1.12.3 — DAS VIERTE FELD, UND ES HAT GEFEHLT.
               GEFUNDEN AUF CHRISTIANS KNOTEN, 16.09.: unter „Cluster One"
               stand NICHTS mehr. Kein Untermenue, nicht einmal ein leeres.

               Das Dach meldet die Produktseiten seit heute SELBST an und
               braucht dafuer die Capability — sie steht in der Anmeldung,
               neben name, nr und slug. DIESE FUNKTION HAT SIE NICHT
               DURCHGEREICHT. Die Lader schickten sie, und hier fiel sie
               vom Tisch: das Ergebnis trug drei Felder von vier.

               PACIOLI HAT ES MIR GEMESSEN UND HINGESCHRIEBEN:
                   kern.php fc33e99d5cda liest name JA . nr JA . slug JA .
                   cap NEIN
               Ich habe die Zeile in einer Antwort ZITIERT und nicht
               gelesen. Sie stand als schoener Satz da und war ein Befund.

                   Eine Messung, die man zitiert statt sie zu lesen,
                   hat man weitergegeben und nicht bekommen.

               UND DIE ENTSCHEIDUNG DARUNTER WAR AUCH MEINE: „die
               Capability bleibt in der Anmeldung" — ohne zu pruefen, ob
               der Boden sie traegt. Wer ein Feld bestellt, muss den Weg
               bauen, auf dem es ankommt. */
            $c1_cap  = is_array($c1_a) ? (string) ($c1_a['cap'] ?? '') : '';
            /* JEDER leere Name wird gemeldet, und die URSACHE steht im TEXT.
               Meine erste Fassung meldete nur den Fall „angemeldet, aber ohne
               Namen" — der Fall „gar nichts angemeldet" blieb still, und das war
               genau Maillarts Forderung 1: zwei Ursachen, ein Ergebnis, beide
               stumm. Drei Ursachen, drei Saetze, EIN Meldeweg. */
            if ($c1_name === '') {
                cluster_one_produkte_stumm($c1_falsch ? 'falsche-form' : 'ohne-namen',
                    'Produkt ' . $c1_w . ' hat '
                  . ($c1_falsch
                        ? 'deshalb keinen brauchbaren Namen'
                        : ($c1_a === null
                            ? 'sich GAR NICHT angemeldet'
                            : 'sich angemeldet, aber keinen Namen genannt'))
                  . ' — der oberste Menuepunkt sagt deshalb nicht, was hier liegt.');
            }
            /* H218: EIN VERWORFENER EINTRAG WIRD GEMELDET — und ein fehlender
               Slug wird NICHT wie ein fehlender Name behandelt. Ohne Namen ist
               der Menuepunkt stumm; ohne Slug ist er nur unverlinkbar. Das
               Produkt bleibt in der Liste und behaelt seinen Namen.
                   Zwei Ursachen, zwei Folgen, zwei Saetze.
               Gemeldet wird nur, wer sich ueberhaupt angemeldet hat: wer gar
               nichts gesagt hat, ist oben schon genannt, und zweimal denselben
               Wirt zu ruegen macht aus einem Fund zwei. */
            /* DIESELBE GESTALT WIE 'ohne-slug', und aus demselben Grund:
               gemeldet wird nur, wer sich ANGEMELDET und das Feld dabei
               weggelassen hat. Wer gar nichts gesagt hat, ist oben schon
               uebersprungen — und zweimal denselben Wirt zu ruegen macht
               aus einem Fund zwei.

               WAS HIER NICHT STEHT und mit Absicht nicht: ob die
               Capability RICHTIG ist. Der Boden kennt sie nicht und darf
               sie nicht kennen.
                   Nur das Produkt weiss, wer seine Seite sehen darf.
               Er prueft, ob sie DA ist, und nicht, was sie wert ist. */
            if ($c1_cap === '' && is_array($c1_a)) {
                cluster_one_produkte_stumm('ohne-cap',
                    'Produkt ' . $c1_w . ' hat sich angemeldet, aber KEINE '
                  . 'Capability genannt — der Leser kann keinen Menuepunkt fuer '
                  . 'es bauen, weil er nicht entscheiden darf, wer auf eine '
                  . 'fremde Seite darf. Die Seite bleibt unter ihrer eigenen '
                  . 'Adresse erreichbar.');
            }
            if ($c1_slug === '' && is_array($c1_a)) {
                cluster_one_produkte_stumm('ohne-slug',
                    'Produkt ' . $c1_w . ' hat sich angemeldet, aber keinen Slug '
                  . 'genannt — es bekommt einen Namen und keinen Weg. Ein Eintrag '
                  . 'unter dem Dach kann nicht auf es zeigen.');
            }
            /* 27.10.2026 (Neo), Boden 1.17.3 — DAS FUENFTE UND SECHSTE FELD: 'werft' und 'boden'.
               Nemesus' Frage nach Test 2: woran erkennt ein Knoten, ob ein Plugin aus
               derselben Werft kommt? 'werft' sagt, WER gebaut hat; 'boden' sagt, GEGEN
               WELCHEN BODEN gebaut wurde. Beides ist Erklaerung, kein Beweis – jeder, der
               den Boden nimmt, kann dieselbe Zeichenkette schreiben. Echt unterscheidbar
               ist nur der Boden selbst, und das misst Turings Dateitabelle (gleicher Hash,
               gleicher Boden). Werft sagt „von wem", der Hash sagt „worauf".
               FEHLT ES, FEHLT ES SICHTBAR UND OHNE STRAFE: kein stumm(), keine Ruege –
               ein fremdes Plugin ist kein Fehler, und bis 1.17.2 warf diese Zeile jedes
               fuenfte Feld weg. Der Leser (Dach, Turing) schreibt dann „unbekannt". */
            $c1_werft = is_array($c1_a) ? trim((string) ($c1_a['werft'] ?? '')) : '';
            $c1_boden = is_array($c1_a) ? trim((string) ($c1_a['boden'] ?? '')) : '';
            $c1_aus[$c1_w] = array('name' => $c1_name, 'nr' => $c1_nr, 'slug' => $c1_slug, 'cap' => $c1_cap,
                                   'werft' => mb_substr($c1_werft, 0, 40), 'boden' => mb_substr($c1_boden, 0, 20));
        }

        uasort($c1_aus, function ($a, $b) {
            if ($a['nr'] !== $b['nr']) { return $a['nr'] <=> $b['nr']; }
            return strcmp($a['name'], $b['name']);
        });

        /* v1.9.14 — DIE FLACHE FORM IST WEG, UND DAS IST EINE AENDERUNG MIT
           FOLGEN. Bis hierher gab diese Funktion `wirt => name` zurueck. Mit dem
           Slug traegt ein Produkt drei Auskuenfte, und eine flache Liste koennte
           nur eine davon halten.

           ZWEI FUNKTIONEN WAEREN DIE BEQUEMERE ANTWORT GEWESEN — eine flache fuer
           die alten Leser, eine volle fuer die neuen. Zwei Leseklinken an
           derselben Tuer sind erlaubt (P1-12). Aber zwei Klinken, die
           VERSCHIEDEN VIEL herausgeben, sind keine zwei Wege zu einer Wahrheit,
           sondern zwei Wahrheiten mit Vorsprung fuer die eine.

               Pacioli, 09.09.: Ein neuer Zustand, der nur an der Stelle benannt
               wird, an der er entsteht, trifft weiter unten auf einen Apparat,
               der ihn nicht kennt.

           Der Apparat weiter unten ist menue.php, und er wird im selben Zug
           geaendert. Wer diese Funktion sonst noch liest, bekommt ab hier ein
           array statt einer Zeichenkette — laut, nicht still: (string) auf ein
           array ist in PHP eine Warnung und kein leerer Text. */
        /* GEMERKT WIRD NUR EIN ERGEBNIS, NIE EIN SCHEITERN. Dieselbe Regel wie
           im Boden bei 'plugins_loaded': eine leere Liste heisst hier entweder
           „zu frueh gefragt" oder „gescheiterte Messung", und beide sind
           gemeldet. Sie einzufrieren machte aus einem gemeldeten Fehler einen
           stillen — und der zweite Leser bekaeme die Stille statt der Meldung. */
        if (array() !== $c1_aus) {
            cluster_one_produkte_merkzettel($c1_aus);
        }
        return $c1_aus;
    }
}
