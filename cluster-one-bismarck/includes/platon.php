<?php
/**
 * Cluster One — Modul platon (DER SCHNITT, v0.0.100 -> Module).
 * Reiner Umzug aus cluster-one.php; jede Funktion byte-identisch zur Baseline.
 *
 * v1.0.0: Der Kopf sagte bis zuletzt "Modul sokrates" — ein Rest aus der Zeit,
 * als Platon und Sokrates noch zwei Namen fuer dieselbe Sache waren. Eine Datei,
 * die anders heisst als ihr Kopf, luegt jeden an, der sie oeffnet.
 *
 * NICHT angetastet: das ID-Praefix 'sokrates_' in Zeile ~203. Gespeicherte
 * Kennungen sind Bestand, kein Text. Ein Praefix umzubenennen hiesse, in
 * fremden Datenbanken zu schreiben — genau ein Schreiber, null Leser, kein
 * Grund. (P1-11: Eigenschaften frieren bei der Erzeugung ein.)
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

   OHNE $c1_platon_hoeher: die Vorlage aus §4 traegt ein
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
$cluster_one_platon_laeuft = !function_exists('cluster_one_new_label_signal');
cluster_one_hier('platon', '', $cluster_one_platon_laeuft);

if ($cluster_one_platon_laeuft) {


function cluster_one_new_label_signal(): bool {
    // v0.0.98: wahr, wenn dieser Knoten mindestens ein veröffentlichtes,
    // nicht archiviertes Label trägt — reist im vorhandenen Ping-Feld zu
    // den Freunden (Signal, keine Zustellung: geholt wird per Pull).
    foreach (cluster_one_get_forum_threads() as $t) {
        if (cluster_one_thread_is_label($t) && (string) ($t['label_status'] ?? 'draft') === 'published' && !cluster_one_container_is_archived($t)) {
            return true;
        }
    }
    return false;
}

/** v0.1.3: Der Mesh-Punkt einer Kante. Beantwortet die Frage, die der alte
   Boolean nicht beantworten konnte: liegt dort etwas, das ich noch NICHT
   geholt habe? Reine Entscheidung, kein Seiteneffekt — deshalb einzeln
   bezeugbar.

   $row  = die Ping-Antwort dieser Kante ('ok', evtl. 'label_ts')
   $seen = der label_ts-Stand, bei dem ich dort zuletzt gezogen habe

   'neu'       — dort wurde nach meinem letzten Ziehen veröffentlicht
   'bekannt'   — es LIEGT etwas, aber ich habe es schon geholt
   'keine'     — dort ist nie etwas veröffentlicht worden
   'veraltet'  — die Kante spricht eine Version ohne label_ts; ehrlicher
                 Hinweis statt eines falschen Punktes
   'unbekannt' — die Kante hat nicht geantwortet. Das ist eine Auskunft über
                 MICH, nicht über den Nachbarn.

   v0.2.9: 'nichts' hiess vorher ZWEI verschiedene Dinge — "hat gar keine
   Labels" und "hat welche, die kenne ich schon". Für Nomadentum ist das der
   Unterschied zwischen einem leeren und einem lohnenden Knoten, den man nur
   gerade nicht neu ziehen muss. Ein grauer Punkt für beides verschenkt genau
   die Information, um die es geht.

   0.1.14 (H105): UND GENAU DERSELBE FEHLER STAND NOCH EINMAL DARIN.
   'keine' trug zwei Tatsachen: "dort ist nie etwas veröffentlicht worden" und
   "diese Kante hat nicht geantwortet". Die erste ist eine Auskunft über den
   Nachbarn, die zweite über die eigene Leitung.

       Ein Name, der zwei Dinge trägt, benennt keines.

   Gefunden auf einem Umweg: Kleinrock hat gemeldet, dass eine Werttabelle
   einen Wert nannte, den es nicht gibt — beim Nachsehen fiel dies heraus. Es
   ist derselbe Befund wie der vom Morgen, eine Ebene tiefer und im Original:
   dort log der RÜCKFALL bei fehlendem Bismarck, hier log die QUELLE.

   KEIN NEUER WERT. 'unbekannt' steht seit demselben Tag im Vertrag, weil
   Kleinrocks Zelle ihn für genau diese Aussage schon zeichnet (grau, '?',
   „This node cannot answer…"). Ein sechster Wert wäre eine zweite Wahrheit
   für dieselbe Sache gewesen.

       Wer einen Wert braucht, den es schon gibt, erfindet keinen —
       er hört auf, den falschen zu senden.

   Und was NICHT mitgekippt wurde: $ts <= 0 bleibt 'keine'. Das ist die
   richtige Auskunft und war es immer. Der Zeuge (zeuge-h105.php) hält sie mit
   zwei Schlössern fest — wer sie mitrepariert, macht eine Auskunft kaputt,
   während er eine andere heilt. */
function cluster_one_label_dot(array $row, int $seen): string {
    if (empty($row['ok'])) {
        return 'unbekannt';                    // nicht geantwortet — das sagt nichts über Labels
    }
    if (!array_key_exists('label_ts', $row) || $row['label_ts'] === null) {
        return 'veraltet';                     // alte Version — sagt es, statt zu raten
    }
    $ts = (int) $row['label_ts'];
    if ($ts <= 0) {
        return 'keine';                        // dort ist nie etwas veröffentlicht worden
    }
    return $ts > $seen ? 'neu' : 'bekannt';    // bekannt heisst: da liegt was, du kennst es
}

/** v0.1.3: Merkt an einer Kante, bis zu welchem label_ts dort gezogen wurde.
   Arbeitet nur auf der Kanten-Karte (Optionen), kein Netz — deshalb einzeln
   bezeugbar. Gibt true zurück, wenn eine Kante zu dieser URL gefunden wurde. *//* v1.3.3 — DIESE FUNKTION GEHOERT NICHT HIERHER, UND SIE ZIEHT IN ZWEI ZUEGEN.
 *
 * Sie liest und schreibt die paired_map — die Kerndatenstruktur des Netzes.
 * Ihr einziger Aufrufer ist kleinrock.php:3614, und der Aufruf war dort mit
 * function_exists() verriegelt: Kleinrock sicherte sich gegen die Abwesenheit
 * einer Funktion ab, die ihm gehoert.
 *
 *     Ein Haus, das sich gegen die eigene Funktion verriegelt,
 *     hat sie im falschen Haus abgestellt.
 *
 * Der Umzug nach kleinrock.php war gebaut und ist zurueckgenommen worden. Die
 * Vertraeglichkeitsprobe hat gezeigt, warum — und es ist eine Regel, keine
 * Panne:
 *
 *     Aufspielen von cluster-one-bismarck: Cannot redeclare
 *     cluster_one_label_mark_seen() (previously declared in
 *     cluster-one-kleinrock/includes/kleinrock.php) in cluster-one/includes/
 *     platon.php on line 94
 *
 * Ein Mensch spielt eines nach dem anderen auf. Zwischen „neues Kleinrock" und
 * „neues Bismarck" liegt ein Knoten, auf dem die Funktion an ZWEI Orten liegt.
 * Ein Riegel am NEUEN Ort hilft nicht: die alte Kopie ist laengst ausgeliefert
 * und riegelt nicht.
 *
 *     EINE FUNKTION KANN DAS HAUS NICHT IN EINER AUSLIEFERUNG WECHSELN.
 *     Zug 1: die alte Stelle bekommt einen Riegel.
 *     Zug 2: sobald jedes ausgelieferte Paket den Riegel traegt, zieht sie um.
 *
 * Das hier ist Zug 1. W8c weiss am Lager, wann Zug 2 erlaubt ist. */
if (!function_exists('cluster_one_label_mark_seen')) {
function cluster_one_label_mark_seen(string $url, int $ts): bool {
    if ($url === '') {
        return false;
    }
    /* v1.3.3: ueber den Netz-Vertrag. Diese Funktion zieht nach kleinrock.php
       um (Zug 2), aber solange sie hier steht, soll sie das Netz nicht beim
       Namen rufen — ohne Kleinrock ist die Karte leer und es gibt nichts zu
       markieren, was genau die Antwort ist, die der Rumpf schon kennt. */
    $map  = (array) apply_filters('cluster_one_netz', array(), 'karte');
    $ziel = cluster_one_norm_url($url);
    $hit  = false;
    foreach ($map as $edge => $e) {
        if (cluster_one_norm_url((string) ($e['url'] ?? '')) === $ziel) {
            $map[$edge]['label_ts_seen'] = $ts;
            $hit = true;
        }
    }
    if ($hit) {
        do_action('cluster_one_netz_karte_speichern', $map);
    }
    return $hit;
}
}

/** Präambel-Freigabe (v0.0.75, Punkt 4): Umschalter am Tagebuch-Thread,
   bezeugt im verknüpften Chat — beide Richtungen, nie still. */
add_action('admin_post_cluster_one_diary_preamble_grant', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_diary_preamble_grant');
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
    $rid   = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $rooms = cluster_one_get_rooms();
    $tid   = $rid !== '' && isset($rooms[$rid]) ? (string) ($rooms[$rid]['linked_thread'] ?? '') : '';
    $threads = cluster_one_get_forum_threads();
    if ($tid !== '' && isset($threads[$tid]) && cluster_one_thread_is_ai_diary($threads[$tid])) {
        $granted = !empty($threads[$tid]['preamble_grant']);
        $threads[$tid]['preamble_grant'] = !$granted;
        cluster_one_save_forum_threads($threads);
        $msgs   = cluster_one_get_room_messages($rid);
        $msgs[] = cluster_one_system_message($granted
            ? __('SysOp withdrew the preamble grant.', 'cluster-one-bismarck')
            : __('SysOp granted the preamble — the diary AI’s next reply here becomes the diary’s preamble.', 'cluster-one-bismarck'));
        cluster_one_save_room_messages($rid, $msgs);
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($rid)));
    exit;
});

/** Fängt bei scharfer Freigabe die nächste Antwort der Tagebuch-KI und macht
   sie zur Präambel. Actor = KI (die Autorschaft wird beweisbar wahr, das
   Zeugnis lügt nicht), Fingerabdruck wie beim SysOp-Weg, Freigabe erlischt.
   Owner-Gate: nur eine KI, die auf der Sichtbarkeitsliste des Tagebuchs
   steht, kann es beschriften — eine zufällig anwesende zweite KI im Chat
   nicht. Gibt true zurück, wenn die Antwort zur Präambel wurde. */
function cluster_one_diary_preamble_capture(string $room_id, string $cid, string $text): bool {
    $rooms = cluster_one_get_rooms();
    $tid   = isset($rooms[$room_id]) ? (string) ($rooms[$room_id]['linked_thread'] ?? '') : '';
    if ($tid === '') {
        return false;
    }
    $threads = cluster_one_get_forum_threads();
    if (!isset($threads[$tid]) || !cluster_one_thread_is_ai_diary($threads[$tid]) || empty($threads[$tid]['preamble_grant'])) {
        return false;
    }
    $ids = cluster_one_get_ai_identities();
    $uid = isset($ids[$cid]) ? (int) $ids[$cid] : 0;
    $vt  = array_map('strval', (array) ($threads[$tid]['visible_to'] ?? array()));
    if ($uid <= 0 || !in_array('wp_' . $uid, $vt, true)) {
        return false; // nicht die Besitzerin — Freigabe bleibt scharf
    }
    $ptext = cluster_one_sanitize_message($text);
    if (trim($ptext) === '') {
        return false;
    }
    /* v0.8.9: DIE ANTWORT WIRD NICHT MEHR SOFORT PRAEAMBEL, sondern
       VORGEMERKT. Christians Entwurf: Der Knopf hat drei Zustaende — etablieren,
       warten, uebernehmen. Erst der dritte Klick schreibt.

       Der Gewinn ist nicht Bequemlichkeit: Bisher wurde ein Text zur Praeambel,
       den der SysOp noch nie gesehen hatte. Jetzt steht er im Chat, bevor er
       gilt — und der Knopf nennt, WELCHE Antwort er uebernimmt. Genau die
       Verwechslung, die in Christians erster Sitzung passiert ist, kann damit
       nicht mehr vorkommen. */
    $threads[$tid]['preamble_candidate'] = $ptext;
    $threads[$tid]['preamble_candidate_ts'] = time();
    $threads[$tid]['preamble_candidate_by'] = cluster_one_actor_id_ai($cid);
    $threads[$tid]['preamble_grant'] = false; // die Freigabe hat ihren Zweck erfuellt
    cluster_one_save_forum_threads($threads);   // 18.10.2026 (Neo): stand zweimal da
    return true;
}

/* v0.8.9: Der dritte Klick — die vorgemerkte Antwort wird Praeambel. Erst hier
   entsteht das Zeugnis, denn erst hier gilt der Text. */
function cluster_one_diary_preamble_adopt(string $tid): bool {
    $threads = cluster_one_get_forum_threads();
    if (!isset($threads[$tid]) || !cluster_one_thread_is_ai_diary($threads[$tid])) {
        return false;
    }
    $ptext = trim((string) ($threads[$tid]['preamble_candidate'] ?? ''));
    if ($ptext === '') {
        return false;
    }
    $threads[$tid]['preamble'] = $ptext;
    unset($threads[$tid]['preamble_candidate'], $threads[$tid]['preamble_candidate_ts']);
    cluster_one_event_emit(array(
        'type'           => 'preamble_set',
        'container_type' => 'thread', 'container_id' => $tid,
        /* Die KI bleibt die Handelnde: Der Text ist ihrer, der SysOp hat ihn
           nur uebernommen. Wer ihn geschrieben hat, steht am Kandidaten. */
        'actor_id'       => (string) ($threads[$tid]['preamble_candidate_by'] ?? cluster_one_actor_id_human()),
        'visibility'     => cluster_one_event_visibility($threads[$tid]),
        'content_hash'   => hash('sha256', $ptext),
    ), true);
    cluster_one_save_forum_threads($threads);
    /* Die Ansage geht in den verknuepften Raum, wenn es einen gibt. */
    foreach (cluster_one_get_rooms() as $c1_rid => $c1_r) {
        if ((string) ($c1_r['linked_thread'] ?? '') !== $tid) {
            continue;
        }
        $c1_m   = cluster_one_get_room_messages((string) $c1_rid);
        $c1_m[] = cluster_one_system_message(sprintf(
            /* translators: %s: fingerprint of the preamble */
            __('The diary\'s preamble was adopted (fingerprint %s).', 'cluster-one-bismarck'),
            cluster_one_preamble_hash($ptext)
        ));
        cluster_one_save_room_messages((string) $c1_rid, $c1_m);
        break;
    }
    return true;
}


function cluster_one_forum_thread_id(): string {
    return 'sokrates_' . wp_generate_password(12, false, false);
}

/** Frei definierte Forenbereiche (Kranz → Kleinrock), v0.0.53. */
function cluster_one_forum_areas(): array {
    $opts = get_option('cluster_one_settings', array());
    return array_values((array) ($opts['forum_areas'] ?? array()));
}

/** v0.3.3: Die Bereiche, die im Forum zur Auswahl stehen. "Fremd-Labels" ist
   nicht darunter: Er ist ein Eingang, kein Arbeitsplatz, und seine Vordertuer
   ist der Labels-Reiter. Zwei Vordertueren fuer eine Sache waren genau der
   Bruch, an dem ein eigenes Label doppelt erschien. Die Threads bleiben
   erreichbar — ueber den Reiter und ueber ihre eigene Adresse. */
function cluster_one_forum_areas_selectable(): array {
    /* v0.6.4: Was der Beschirmte nicht sieht, kann er auch nicht waehlen.
       v0.9.3: Der SysOp-Bereich steht jetzt in der Anzeige (Transparenz), darf
       aber weiterhin von niemandem sonst BESTUECKT werden — sonst legte jemand
       etwas dort ab, das er danach selbst nicht mehr sieht. */
    $c1_w = array_values(array_filter(cluster_one_areas_visible(), function ($a) {
        return cluster_one_is_sysop() || $a !== cluster_one_sysop_area();
    }));
    return array_values(array_filter($c1_w, function ($a) {
        return $a !== cluster_one_foreign_area();
    }));
}

/* --- v0.3.0: DER EINGANG FÜR FREMD-LABELS ------------------------------------
   Vorher war "Vorschläge" ein Ablagefach: eine flache Liste aller je geholten
   Labels in einer eigenen Option, die nie alterte und aus der nichts wieder
   herauskam. Jetzt ist es ein DURCHGANG.

     Vorschläge  = Liste der KNOTEN mit neuen Labels (grüner Punkt)
     Pull        = die Labels werden zu Threads im Bereich "Fremd-Labels"
     danach      = Vorschlagsliste leer, Lämpchen aus — nichts gelöscht, umgezogen

   Ein Thread pro Fremdlabel. Zweiter Pull desselben Labels haengt NACH statt
   einen zweiten Thread anzulegen: so steht die Entwicklung eines fremden
   Projekts an einem Ort.

   Fremd-Labels sind NICHT editierbar. Sonst stuende hier irgendwann eine
   Fassung, die auf dem Ursprungsknoten nie existiert hat — die
   Souveraenitaetsregel von der anderen Seite. */
function cluster_one_foreign_area(): string {
    return 'Fremd-Labels';
}

/** Legt den Bereich an, falls er fehlt. Idempotent. */
function cluster_one_ensure_foreign_area(): void {
    $opts  = get_option('cluster_one_settings', array());
    $areas = array_values((array) ($opts['forum_areas'] ?? array()));
    if (!in_array(cluster_one_foreign_area(), $areas, true)) {
        $areas[]             = cluster_one_foreign_area();
        $opts['forum_areas'] = $areas;
        update_option('cluster_one_settings', $opts, false);
    }
}

/* v0.9.71 — DIE FUENF THREAD-PRAEDIKATE SIND IN DEN BODEN GEZOGEN.

   cluster_one_thread_is_foreign / _wilde / _wiki / _label / _ai_diary stehen
   jetzt in kern.php, direkt bei cluster_one_thread_is_diary(), das sie schon
   vorher benutzt hat.

   DER GRUND WAR EINE MESSUNG, KEINE VORLIEBE: kern rief 21-mal nach OBEN in
   platon hinein, und alle 21 Aufrufe waren diese fuenf. Vier davon pruefen ein
   einziges Feld im Thread-Array; die fuenfte fragt kern.php:1232. Keine davon
   hat je etwas mit Platon zu tun gehabt ausser der Nachbarschaft.

   WAS SIE SIND: die Antwort auf "was fuer ein Ding ist dieser Thread". Das ist
   eine Eigenschaft des Gegenstands, nicht eine Faehigkeit des Forums — und
   Bismarck, Kleinrock, Winsor und kern fragen sie genauso wie Platon.

   Ihre Kommentare sind mitgereist. Jeder haelt einen Fund fest. */


/** Vier Zustaende der KI-Einschaetzung. Das Laempchen ist die Kurzfassung,
   der Beitrag im Thread ist die Begruendung — dasselbe Muster wie beim
   Label-Punkt: der Punkt sagt "schau hin", der Inhalt sagt "weshalb".
   'offen' heisst NICHT geprueft, nicht "egal". */
function cluster_one_foreign_verdict(array $t): string {
    /* v0.3.7: Dieselbe Lampe, andere Woerter je Seite. Bei FREMDEN Labels lautet
       die Frage "passt das zu uns", bei EIGENEN "welches davon traegt am
       weitesten". Zwei verschiedene Fragen, deshalb zwei Vokabulare — aber ein
       Mechanismus, damit die Tabellen sich gleichen. */
    $v = (string) ($t['verdict'] ?? '');
    return in_array($v, array('passt', 'unklar', 'fern', 'stark', 'mittel', 'schwach'), true) ? $v : 'offen';
}

/** Rang fuer die Lampe: gruen, amber, grau, hohl. */
function cluster_one_verdict_tone(string $v): string {
    if ($v === 'passt' || $v === 'stark')   { return 'gut'; }
    if ($v === 'unklar' || $v === 'mittel') { return 'mittel'; }
    if ($v === 'fern' || $v === 'schwach')  { return 'fern'; }
    return 'offen';
}

/** Rang fuer die Sortierung nach Urteil. Frisch Hereingezogenes steht OBEN,
   nicht unten — sonst verschwindet Neues unter alten Urteilen. */
function cluster_one_foreign_rank(array $t): int {
    switch (cluster_one_verdict_tone(cluster_one_foreign_verdict($t))) {
        case 'offen':  return 0; // noch nicht geprueft — zuerst
        case 'gut':    return 1;
        case 'mittel': return 2;
        default:       return 3;
    }
}

/** Die Threads des Eingangs, sortiert. Angeheftetes steht IMMER oben, in jeder
   Ansicht — Anheften ist eine Aussage des SysOps, Sortieren nur eine Sicht. */
function cluster_one_foreign_threads(string $sort = 'urteil', string $dir = 'asc'): array {
    $out = array();
    foreach (cluster_one_get_forum_threads() as $tid => $t) {
        if (cluster_one_thread_is_foreign($t)) {
            $out[$tid] = $t;
        }
    }
    $schluessel = function (array $t) use ($sort) {
        switch ($sort) {
            case 'eingang':  return (int) ($t['created'] ?? 0);
            case 'herkunft': return strtolower((string) ($t['foreign_node'] ?? ''));
            default:         return cluster_one_foreign_rank($t);
        }
    };
    uasort($out, function ($a, $b) use ($schluessel, $dir) {
        $pa = !empty($a['pinned']) ? 0 : 1;
        $pb = !empty($b['pinned']) ? 0 : 1;
        if ($pa !== $pb) {
            return $pa <=> $pb; // Angeheftetes zuerst, unabhaengig von Sortierung und Richtung
        }
        $c = $schluessel($a) <=> $schluessel($b);
        return $dir === 'desc' ? -$c : $c;
    });
    return $out;
}

/* v0.9.42: HIER STAND cluster_one_forum_wiki_areas().

   Die Liste sagte, WELCHE BEREICHE sich als Wiki verhalten. Seit 0.0.69 tut
   sie das nicht mehr: Wiki ist eine Eigenschaft des THEMAS, bei der Anlage
   eingefroren, und beim Anlegen waehlt man sie am Formular ($role === 'wiki').
   Der Bereich hatte darauf keinen Einfluss mehr — die Liste war seither ein
   historischer Datensatz, den sechs Stellen im Plugin weiter mitpflegten.

   Christian am 19.08.: "Oder wir lassen Brainstorming drinnen und nehmen Wiki
   raus. Denn Wiki kann jetzt ja ueberall sein." Genau so ist es, und damit
   faellt mit dem Wort im Bereichsnamen auch die Liste dahinter.

   In 0.9.31 haette ich sie fast geloescht, weil sie tot aussah — und liess es,
   weil structure_rename sie doch betrat. Das war richtig: Sie WURDE betreten,
   nur ohne Wirkung. Jetzt geht sie mitsamt allen sechs Stellen. */

/** Ist DIESES Thema ein Wiki-Thema, weil sein Bereich als Wiki markiert ist? */

/** Darf dieser Actor den Wilde-Text aendern? Nur der Eigentuemer — sonst
   niemand, auch der SysOp nicht. */
function cluster_one_wilde_owner_is(array $thread, string $actor): bool {
    $o = (string) ($thread['wilde_owner'] ?? '');
    return $o !== '' && $actor !== '' && $o === $actor;
}

/** Haelt diese Entitaet schon eine Selbstdarstellung? Eine pro Entitaet —
   bei mehreren waere unklar, welche gilt. */
function cluster_one_wilde_thread_of(string $actor): string {
    if ($actor === '') {
        return '';
    }
    foreach (cluster_one_get_forum_threads() as $tid => $t) {
        if ((string) ($t['wilde_owner'] ?? '') === $actor) {
            return (string) $tid;
        }
    }
    return '';
}


/** Einfrier-Migration (v0.0.69, einmalig): stempelt allen Bestands-Threads
   ihre HEUTIGE Wiki-Eigenschaft als Thread-Flag — Threads in Wiki-Bereichen
   bleiben Wikis, alle anderen bleiben normal. Danach ist der Bereich reine
   Organisation; forum_wiki_areas wurde bis v0.9.42 als historischer Datensatz
   mitgeführt. Idempotent per Options-Wächter, läuft beim ersten Admin-
   Aufruf nach dem Update (eine echte Upgrade-Routine ist eigener
   Launch-Baustein — dieser Mini-Stempel wandert dann dort hinein). */
add_action('admin_init', function () {
    if (get_option('cluster_one_wiki_flag_migrated')) {
        return;
    }
    $threads = cluster_one_get_forum_threads();
    $changed = false;
    foreach ($threads as $tid => $t) {
        if (!array_key_exists('wiki', $t)) {
            $area = (string) ($t['area'] ?? '');
            /* v0.9.42: Ohne Bereichsliste gibt es nichts mehr nachzuschlagen —
               gestempelt wird das engere Recht (s. cluster_one_thread_is_wiki). */
            $threads[$tid]['wiki'] = false;
            $changed = true;
        }
    }
    if ($changed) {
        cluster_one_save_forum_threads($threads);
    }
    update_option('cluster_one_wiki_flag_migrated', (function_exists('cluster_one_fassung') ? cluster_one_fassung() : (defined('CLUSTER_ONE_VERSION') ? (string) CLUSTER_ONE_VERSION : '')), false);
});

/** Bereichsliste schreiben — die EINE zentrale Schreibfunktion. Prunt die
   Wiki-Teilmenge mit, damit kein Wiki-Häkchen an einem gelöschten Bereich
   hängen bleibt. */
function cluster_one_save_forum_areas(array $areas): void {
    /* v0.4.6: NICHT-SKALARE FLIEGEN RAUS, statt zu "Array" zu werden.
       Auf Andreas' Knoten stand woertlich ein Bereich namens "Array" —
       PHP wandelt ein Array mit (string) genau so um, und der Wert wurde
       gespeichert. Welcher Aufrufer das verschachtelte Array geliefert hat,
       liess sich nicht mehr ermitteln; die Absicherung sitzt deshalb am
       Engpass und wirkt fuer alle. Ein Bereichsname ist immer eine
       Zeichenkette — was keine ist, war ein Fehler und kein Name. */
    $areas = array_values(array_unique(array_filter(array_map(function ($a) {
        if (!is_scalar($a)) {
            return '';
        }
        return trim(sanitize_text_field((string) $a));
    }, $areas), function ($a) {
        return $a !== '';
    })));
    $opts = get_option('cluster_one_settings', array());
    if (!is_array($opts)) {
        $opts = array();
    }
    $opts['forum_areas'] = $areas;
    // v0.9.42: Kein Nachhalten von forum_wiki_areas mehr — die Liste ist fort.
    update_option('cluster_one_settings', $opts);
}

function cluster_one_render_structure_tab(): void {
    $is_sysop = cluster_one_is_sysop();
    if (isset($_GET['structure'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        $s = sanitize_key(wp_unslash($_GET['structure'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        if ($s === 'added') {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Area added.', 'cluster-one-bismarck') . '</p></div>';
        } elseif ($s === 'deleted') {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Area deleted. Threads and rooms that used it are now unsorted — nothing was removed.', 'cluster-one-bismarck') . '</p></div>';
        } elseif ($s === 'dup') {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('An area with that name already exists.', 'cluster-one-bismarck') . '</p></div>';
        } elseif ($s === 'empty') {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Please enter an area name.', 'cluster-one-bismarck') . '</p></div>';
        } elseif ($s === 'desc') {
            /* 18.10.2026 (Neo): der Beschreibungs-Handler schrieb `structure=desc`, gelesen hat es niemand. */
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Area description saved.', 'cluster-one-bismarck') . '</p></div>';
        }
    }
    /* 18.10.2026 (Neo): Leser fuer `struct_err=` — der Umbenennen-Handler
       meldete zwei Fehler in die Adresse, und die Seite schwieg. */
    if (isset($_GET['struct_err'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        $c1_se = sanitize_key(wp_unslash($_GET['struct_err'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        if ($c1_se === 'invalid') {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Not renamed: the old name was not found, or the new name is empty or unchanged.', 'cluster-one-bismarck') . '</p></div>';
        } elseif ($c1_se === 'exists') {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Not renamed: an area with the new name already exists.', 'cluster-one-bismarck') . '</p></div>';
        }
    }
    $areas = cluster_one_forum_areas();
    $usage = cluster_one_area_usage();
    $grey  = $is_sysop ? '' : 'opacity:.45;pointer-events:none;';
    ?>
    <p style="max-width:760px;margin-top:1em;opacity:.85;">
        <?php esc_html_e('One shared list of areas for forums and rooms. Threads and rooms pick their area in their own configuration — “no area” is a valid state. Deleting an area never deletes content: its threads and rooms simply become unsorted.', 'cluster-one-bismarck'); ?>
    </p>
<?php if (!$is_sysop) : ?>
        <p style="max-width:760px;opacity:.7;font-size:.92em;">
            <?php esc_html_e('You can see the structure; changing it is a SysOp capability.', 'cluster-one-bismarck'); ?>
        </p>
<?php endif; ?>
<?php if (empty($areas)) : ?>
        <p style="opacity:.7;margin-top:1.5em;"><?php esc_html_e('No areas defined yet.', 'cluster-one-bismarck'); ?></p>
<?php else : ?>
        <table class="widefat striped" style="max-width:760px;margin-top:1em;">
            <thead>
                <tr>
                    <th><?php esc_html_e('Area', 'cluster-one-bismarck'); ?></th>
<?php /* v0.9.52: EINE SPALTE STATT ZWEIER. Hier standen "Foren" und "Raeume"
   nebeneinander — und in Christians echten Daten stand in ALLEN SECHS
   Bereichen, die Projekte enthalten, zweimal dieselbe Zahl. Auseinander gehen
   die beiden nur dort, wo etwas KEIN Paar ist, also bei Labels.

   Zwei Zahlen, die fast immer gleich sind, sind keine Auskunft — man muss
   selbst subtrahieren, um das Einzige zu erfahren, was sie unterscheidet.
   Seit die Liste PROJEKTE zeigt, zeigt auch diese Spalte Projekte, und wo eine
   Haelfte fehlt, sagt sie es in Worten statt in einer Differenz. */ ?>
                    <th style="text-align:center;"><?php esc_html_e('Projects', 'cluster-one-bismarck'); ?></th>
                    <th style="text-align:center;"><?php esc_html_e('Actions', 'cluster-one-bismarck'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($areas as $i => $a) :
                $t_count = (int) ($usage[$a]['threads'] ?? 0);
                $r_count = (int) ($usage[$a]['rooms'] ?? 0);
            ?>
                <tr>
                    <td><strong><?php echo esc_html($a); ?></strong><?php // v0.0.69: Wiki-Marker entfernt — Wiki ist Thread-Eigenschaft, kein Bereichs-Attribut mehr ?></td>
<?php /* Gezeigt wird die groessere der beiden Zahlen — so viele Zeilen stehen
   in der Projekt-Liste unter diesem Bereich. Stimmen sie nicht ueberein, sagt
   der Vermerk daneben, wie viele Haelften allein stehen; das ist die einzige
   Information, die in der Differenz steckte. */
$c1_pz  = max($t_count, $r_count);
$c1_ohne = abs($t_count - $r_count); ?>
                    <td style="text-align:center;opacity:.8;"><?php
                        if ($c1_pz === 0) {
                            echo '<span style="opacity:.5;">—</span>';
                        } else {
                            echo (int) $c1_pz;
                            if ($c1_ohne > 0) {
                                echo ' <span style="opacity:.6;font-size:.85em;" title="'
                                   . esc_attr__('Not every project here has both halves — a label or a thread without a chat, for instance.', 'cluster-one-bismarck')
                                   . '">' . esc_html(sprintf(
                                       /* translators: %d: number of projects with only one half */
                                       _n('%d without a pair', '%d without a pair', $c1_ohne, 'cluster-one-bismarck'), $c1_ohne)) . '</span>';
                            }
                        }
                    ?></td>
                    <td style="text-align:center;white-space:nowrap;">
                        <span style="display:inline-flex;align-items:center;gap:.7em;justify-content:center;<?php echo esc_attr($grey); ?>">
                            <?php // Hoch/Runter — die Reihenfolge IST die Anzeige-Reihenfolge der Foren-Gruppen ?>
<?php if ($i > 0) : ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline;line-height:1;">
                                    <?php wp_nonce_field('cluster_one_structure_move'); ?>
                                    <input type="hidden" name="action" value="cluster_one_structure_move">
                                    <input type="hidden" name="area" value="<?php echo esc_attr($a); ?>">
                                    <input type="hidden" name="dir" value="up">
                                    <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;" <?php disabled(!$is_sysop); ?>
                                            title="<?php echo esc_attr__('Move up', 'cluster-one-bismarck'); ?>" aria-label="<?php echo esc_attr__('Move up', 'cluster-one-bismarck'); ?>">
                                        <span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true" style="color:#8a8a8a;font-size:18px;width:18px;height:18px;"></span>
                                    </button>
                                </form>
<?php else : ?>
                                <span style="width:18px;display:inline-block;"></span>
<?php endif; ?>
<?php if ($i < count($areas) - 1) : ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline;line-height:1;">
                                    <?php wp_nonce_field('cluster_one_structure_move'); ?>
                                    <input type="hidden" name="action" value="cluster_one_structure_move">
                                    <input type="hidden" name="area" value="<?php echo esc_attr($a); ?>">
                                    <input type="hidden" name="dir" value="down">
                                    <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;" <?php disabled(!$is_sysop); ?>
                                            title="<?php echo esc_attr__('Move down', 'cluster-one-bismarck'); ?>" aria-label="<?php echo esc_attr__('Move down', 'cluster-one-bismarck'); ?>">
                                        <span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true" style="color:#8a8a8a;font-size:18px;width:18px;height:18px;"></span>
                                    </button>
                                </form>
<?php else : ?>
                                <span style="width:18px;display:inline-block;"></span>
<?php endif; ?>
<?php /* v0.3.11: Umbenennen. Der Schraubenschluessel oeffnet ein Feld; das
   Mitziehen aller Themen und Raeume passiert im Handler. */ ?>
<?php /* v0.6.4: Herkunft und Sichtbarkeit auf einen Blick. Der UMRISS-Stern
   sagt "aus der Blaupause" — eine HERKUNFTSANGABE, keine Auszeichnung. Ein
   gefuellter Stern laese sich als "geprueft, besser, offiziell" lesen und
   widerspraeche dem Text der Blaupause selbst: ein Vorschlag, kein Befehl.
   Der offene Kreis sagt, dass die Beschirmten den Bereich noch nicht sehen. */ ?>
                            <?php $c1_meta = cluster_one_area_meta($a); ?>
<?php if (($c1_meta['origin'] ?? '') === 'blueprint') : ?>
                                <span title="<?php echo esc_attr__('From the blueprint', 'cluster-one-bismarck'); ?>" style="opacity:.55;margin-right:.3em;">☆</span>
<?php endif; ?>
<?php if (empty($c1_meta['published'])) : ?>
                                <span title="<?php echo esc_attr__('Not visible to the others yet', 'cluster-one-bismarck'); ?>" style="opacity:.55;margin-right:.3em;">◌</span>
<?php endif; ?>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline-block;vertical-align:middle;">
                                <?php wp_nonce_field('cluster_one_structure_rename'); ?>
                                <input type="hidden" name="action" value="cluster_one_structure_rename">
                                <input type="hidden" name="area" value="<?php echo esc_attr($a); ?>">
                                <input name="new_area" type="text" value="<?php echo esc_attr($a); ?>" size="16" style="font-size:.9em;padding:1px 4px;" <?php disabled(!$is_sysop); ?>>
                                <?php /* v0.3.14: Mit Beschriftung. Ein blosses Symbol liess offen, ob
                                   sich ein Menue oeffnet — man suchte nach etwas, das nicht kommt. */ ?>
                                <button type="submit" class="button button-small" <?php disabled(!$is_sysop); ?>
                                        title="<?php echo esc_attr__('Rename area — threads and rooms move along', 'cluster-one-bismarck'); ?>">
                                    <span class="dashicons dashicons-edit" aria-hidden="true" style="font-size:16px;width:16px;height:16px;vertical-align:text-bottom;"></span>
                                    <?php esc_html_e('Save name', 'cluster-one-bismarck'); ?>
                                </button>
                            </form>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline-block;vertical-align:middle;">
                                <?php wp_nonce_field('cluster_one_structure_desc'); ?>
                                <input type="hidden" name="action" value="cluster_one_structure_desc">
                                <input type="hidden" name="area" value="<?php echo esc_attr($a); ?>">
                                <?php /* v0.7.6: Der Platzhalter zeigt die VORGABE, die gilt, wenn das Feld
   leer bleibt — sonst sieht der SysOp ein leeres Feld und weiss nicht, dass
   trotzdem ein Text angezeigt wird. */ ?>
                                <?php $c1_bp = cluster_one_blueprint_areas(); ?>
                                <input type="text" name="area_desc" value="<?php echo esc_attr($c1_meta['desc']); ?>" class="regular-text" style="width:22em;" placeholder="<?php echo esc_attr(isset($c1_bp[$a]) ? (string) $c1_bp[$a] : __('What is this area for?', 'cluster-one-bismarck')); ?>" <?php disabled(!$is_sysop); ?>>
                                <label style="margin-left:.5em;opacity:.8;"><input type="checkbox" name="area_publish" value="1" <?php checked(!empty($c1_meta['published'])); ?> <?php disabled(!$is_sysop); ?>> <?php esc_html_e('visible', 'cluster-one-bismarck'); ?></label>
                                <button type="submit" style="background:none;border:0;padding:0 .3em;cursor:pointer;line-height:1;" <?php disabled(!$is_sysop); ?>
                                        title="<?php echo esc_attr__('Save description and visibility', 'cluster-one-bismarck'); ?>">
                                    <span class="dashicons dashicons-saved" aria-hidden="true" style="font-size:18px;width:18px;height:18px;"></span>
                                </button>
                            </form>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline;line-height:1;">
                                <?php wp_nonce_field('cluster_one_structure_delete'); ?>
                                <input type="hidden" name="action" value="cluster_one_structure_delete">
                                <input type="hidden" name="area" value="<?php echo esc_attr($a); ?>">
                                <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;" <?php disabled(!$is_sysop); ?>
                                        title="<?php echo esc_attr__('Delete area', 'cluster-one-bismarck'); ?>" aria-label="<?php echo esc_attr__('Delete area', 'cluster-one-bismarck'); ?>"
                                        <?php /* translators: %s: Name des Bereichs. */ ?>
                                        onclick="return confirm('<?php echo esc_js(sprintf(__('Delete the area “%s”? Its threads and rooms are kept and become unsorted.', 'cluster-one-bismarck'), $a)); ?>');">
                                    <span class="dashicons dashicons-dismiss" aria-hidden="true" style="color:#8a8a8a;font-size:18px;width:18px;height:18px;"></span>
                                </button>
                            </form>
                        </span>
                    </td>
                </tr>
<?php endforeach; ?>
                <script>
                /* v0.5.0: EIN Zuhoerer fuer alle Kopier-Knoepfe, keiner pro Beitrag. */
                document.addEventListener('click', function (e) {
                    var b = e.target.closest ? e.target.closest('.c1-copy') : null;
                    if (!b) { return; }
                    var t = b.getAttribute('data-c1-copy') || '';
                    var fertig = function () {
                        var alt = b.textContent;
                        b.textContent = <?php echo wp_json_encode(__('Copied', 'cluster-one-bismarck')); ?>;
                        setTimeout(function () { b.textContent = alt; }, 1400);
                    };
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(t).then(fertig, function () {});
                    } else {
                        var ta = document.createElement('textarea');
                        ta.value = t; document.body.appendChild(ta); ta.select();
                        try { document.execCommand('copy'); fertig(); } catch (err) {}
                        document.body.removeChild(ta);
                    }
                });
                </script>
            </tbody>
        </table>
<?php endif; ?>

    <h2 style="margin-top:2.5em;"><?php esc_html_e('New area', 'cluster-one-bismarck'); ?></h2>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:560px;<?php echo esc_attr($grey); ?>">
        <?php wp_nonce_field('cluster_one_structure_add'); ?>
        <input type="hidden" name="action" value="cluster_one_structure_add">
        <input type="text" name="area" class="regular-text" placeholder="<?php echo esc_attr__('Area name', 'cluster-one-bismarck'); ?>" <?php disabled(!$is_sysop); ?>>
        <p style="margin:.6em 0 .2em;">
            <textarea name="area_desc" rows="2" class="large-text" placeholder="<?php echo esc_attr__('What is this area for? (optional — the others read this)', 'cluster-one-bismarck'); ?>" <?php disabled(!$is_sysop); ?>></textarea>
        </p>
<?php /* v0.6.4: Ein Formular, ein Haekchen. Ab Werk AUS — man baut in Ruhe
   auf, statt den Beschirmten halbe Sachen zu zeigen. */ ?>
        <p style="margin:.2em 0 .6em;">
            <label><input type="checkbox" name="area_publish" value="1" <?php disabled(!$is_sysop); ?>> <?php esc_html_e('Show to the others right away', 'cluster-one-bismarck'); ?></label>
        </p>
        <?php submit_button(__('Add', 'cluster-one-bismarck'), 'secondary', 'submit', false, $is_sysop ? array() : array('disabled' => 'disabled')); ?>
    </form>
<?php /* v0.9.52: Hier stand "Wiki ist seit 0.0.69 eine Eigenschaft pro Thema,
   beim Anlegen gewaehlt — Bereiche sind reine Organisation." Der Satz
   beantwortete die Frage, warum man einen BEREICH nicht zum Wiki machen kann.
   Seit 0.9.42 gibt es Wiki-Bereiche ueberhaupt nicht mehr; der Schluessel ist
   aus den Einstellungen geraeumt. Ein Satz, der erklaert, warum etwas nicht
   geht, das man gar nicht mehr versuchen kann, ist kein Hinweis mehr, sondern
   eine Fussnote zu einem verschwundenen Zustand. */ ?>
    <?php
}

/* Struktur-Handler: die serverseitigen Zwillinge des Grau (Artikel 7 —
   der Handler erzwingt, was das Grau zeigt). */
add_action('admin_post_cluster_one_structure_add', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_structure_add');
    $back = admin_url('admin.php?page=cluster_one_bismarck&tab=structure');
    $area = isset($_POST['area']) ? trim(sanitize_text_field(wp_unslash($_POST['area']))) : '';
    if ($area === '') {
        wp_safe_redirect($back . '&structure=empty');
        exit;
    }
    $areas = cluster_one_forum_areas();
    if (in_array($area, $areas, true)) {
        wp_safe_redirect($back . '&structure=dup');
        exit;
    }
    $areas[] = $area;
    cluster_one_save_forum_areas($areas);
    /* v0.6.4: Beschreibung und Sichtbarkeit gleich im selben Formular —
       EIN Schritt, nicht zwei. Zwei Schritte waeren ein Klick mehr fuer
       denselben Zustand und ein zusaetzlicher Weg, den zweiten zu vergessen.
       Ab Werk NICHT sichtbar: So baut man eine Struktur in Ruhe auf, statt
       den Beschirmten halbe Sachen zu zeigen. */
    cluster_one_area_meta_set($area, array(
        'desc'      => isset($_POST['area_desc']) ? trim(sanitize_textarea_field(wp_unslash($_POST['area_desc']))) : '',
        'origin'    => '',
        'published' => !empty($_POST['area_publish']),
    ));
    wp_safe_redirect($back . '&structure=added');
    exit;
});

add_action('admin_post_cluster_one_structure_delete', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_structure_delete');
    $back = admin_url('admin.php?page=cluster_one_bismarck&tab=structure');
    $area = isset($_POST['area']) ? trim(sanitize_text_field(wp_unslash($_POST['area']))) : '';
    $areas = cluster_one_forum_areas();
    $areas = array_values(array_diff($areas, array($area)));
    cluster_one_save_forum_areas($areas);
    // Container behalten ihren area-String und fallen dadurch von selbst
    // ins Auffangbecken „Unsorted"/„Ohne Bereich" — nichts wird gelöscht,
    // nichts umgeschrieben (kein stiller Struktureingriff in Container).
    // v0.6.4: Die Beschreibung geht mit — sonst erbte ein spaeter gleichnamig
    // angelegter Bereich den Text eines geloeschten.
    cluster_one_area_meta_forget($area);
    wp_safe_redirect($back . '&structure=deleted');
    exit;
});

add_action('admin_post_cluster_one_structure_move', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_structure_move');
    $back = admin_url('admin.php?page=cluster_one_bismarck&tab=structure');
    $area = isset($_POST['area']) ? trim(sanitize_text_field(wp_unslash($_POST['area']))) : '';
    $dir  = (isset($_POST['dir']) && $_POST['dir'] === 'up') ? 'up' : 'down';
    $areas = cluster_one_forum_areas();
    $i = array_search($area, $areas, true);
    if ($i !== false) {
        $j = $dir === 'up' ? $i - 1 : $i + 1;
        if ($j >= 0 && $j < count($areas)) {
            $tmp = $areas[$i];
            $areas[$i] = $areas[$j];
            $areas[$j] = $tmp;
            cluster_one_save_forum_areas($areas);
        }
    }
    wp_safe_redirect($back);
    exit;
});

/** Zeitfenster fürs Selbst-Löschen durch den Verfasser (v0.0.48): fest,
   nicht einstellbar — eine Zahl, die sich vermutlich nie wieder ändert,
   wäre mehr Bedienfläche als Nutzen. */
function cluster_one_forum_self_delete_window(): int {
    return 120; // 2 Minuten
}

/** Eigener Beitrag? v0.0.62: Vergleich AUSSCHLIESSLICH über die Actor-ID —
   der Anzeigename bleibt loser String für die Darstellung (§3 Identitäts-
   frage), trägt aber keine Rechte mehr (P0-4: Namensgleichheit war
   Rechtegleichheit, Namensänderung war Eigentumsverlust). Kahlschlag:
   kein Fallback auf den Author-String. 'collective' und 'system' gehören
   niemandem — Wiki-Rechte laufen über can_edit, System schreibt nur. */
function cluster_one_forum_post_is_own(array $post): bool {
    $aid = (string) ($post['actor_id'] ?? '');
    if ($aid === '' || $aid === 'collective' || $aid === 'system') {
        return false;
    }
    return $aid === cluster_one_actor_id_human();
}

/** Bearbeiten: nur der Verfasser selbst, ODER der SysOp — aus
   rechtlichen Gründen uneingeschränkt, jederzeit, jeder Beitrag. Ausnahme
   (v0.0.53): in einem Wiki-Thema darf JEDER auf der Weißliste editieren,
   nicht nur Verfasser/SysOp — genau der Punkt der Funktion. $thread ist
   optional, damit bestehende Aufrufe ohne Thread-Kontext (z. B. bei
   Bismarck, falls die Funktion je dort wiederverwendet wird) weiter
   funktionieren, ohne Wiki-Verhalten anzunehmen. */
/** v0.9.39, zurueckgebaut in v1.6.0: EIN ZUG, DANN WARTEN — auch im Thema.
 *
 * Am 30.08. geloescht, mit der Begruendung, im Forum sei die Regel nur
 * Umgangsform gewesen. **Diese Praemisse ist gemessen falsch**, und Bismarck
 * hat sie mit einem roten Zeugen widerlegt (`zeuge-fenster-polyphon.php`):
 *
 *     bismarck.php:674   $tail = array_slice($posts, -5);
 *
 * Die KI sieht die letzten FUENF Beitraege. Bismarcks Gruendungssatz gegen
 * Anbiederung — "sind mehrere Menschen mit verschiedenen Positionen anwesend,
 * kann sich keine KI mehr bei nur einem anbiedern" — ist nur wahr, solange sie
 * mehrere SIEHT.
 *
 *     Welt A, ohne die Regel:  ein Mensch schreibt fuenfmal  ->  5 angenommen,
 *                              1 Sprecher im Fenster                     ROT
 *     Welt B, mit der Regel:                                 ->  1 angenommen  OK
 *
 * Ein einstimmiges KI-Fenster ist ohne die Regel auf dem NORMALEN Weg
 * erreichbar: drei Absaetze schreiben, dann die KI fragen.
 *
 *     Eine Regel, die nur unhoeflich zu sein verhindert, ist Umgangsform.
 *     Eine Regel, die einen Zustand unerreichbar macht, ist ein Mechanismus.
 *
 * UND DER BEWEIS, DASS SIE NIE BLOSS UMGANGSFORM WAR, STEHT IN IHR SELBST:
 * Tagebuecher sind ausgenommen. Eine Hoeflichkeitsregel gilt ueberall. Diese
 * war genau dort abgeschaltet, wo Einstimmigkeit richtig ist — sie war auf die
 * Form des Protokolls zugeschnitten, nicht auf Benehmen.
 *
 * BENANNTE GRENZE, von Bismarck selbst benannt: Die Regel zaehlt SPRECHER,
 * nicht MENSCHEN. Ein Verlauf Mensch-KI-Mensch-KI-Mensch erfuellt sie mit zwei
 * Sprechern und EINEM Menschen. **Sie schliesst das einstimmige Fenster aus;
 * sie erzwingt kein mehrstimmiges.** Gesichert ist der schlechteste Fall, nicht
 * das Prinzip.
 *
 * WARUM HIER EINS UND AM MESH-TOR FUENF: Der Deckel des Forums haengt an der
 * Fenstergroesse der KI. Fuenf waeren genau das Fenster — ein Mensch koennte es
 * allein fuellen. Eins ist fuer jedes Fenster ab zwei sicher.
 *
 * Ein SYSTEM-Vermerk ist keine Antwort (0.9.39): wer spricht, gibt den Zug
 * zurueck; wer protokolliert, nicht.
 */
function cluster_one_thread_awaiting_reply(string $tid, string $actor): bool {
    $threads = cluster_one_get_forum_threads();
    $t = isset($threads[$tid]) ? (array) $threads[$tid] : array();
    if ($t && cluster_one_thread_is_diary($t)) {
        return false; // Tagebuecher: dort schreibt nur einer
    }
    foreach (array_reverse(cluster_one_get_forum_posts($tid)) as $p) {
        $a = (string) ($p['actor_id'] ?? '');
        if ($a === 'system' || !empty($p['system'])) {
            continue; // protokolliert, spricht nicht
        }
        return $a === $actor; // der Letzte, der GESPROCHEN hat
    }
    return false; // leeres Thema
}

/** v0.9.39: Hat nach diesem Beitrag jemand ANDERES gesprochen? Grundlage der
   Bearbeitungsgrenze — Worte werden fest, wenn jemand darauf geantwortet hat,
   nicht nach einer Frist. Eine Uhr ist willkuerlich, eine Antwort nicht. */
function cluster_one_forum_post_beantwortet(array $post, string $tid): bool {
    $eigen = (string) ($post['actor_id'] ?? '');
    $pid   = (string) ($post['id'] ?? '');
    $danach = false;
    foreach (cluster_one_get_forum_posts($tid) as $p) {
        if ((string) ($p['id'] ?? '') === $pid) {
            $danach = true;
            continue;
        }
        if (!$danach) {
            continue;
        }
        $a = (string) ($p['actor_id'] ?? '');
        if ($a === 'system' || !empty($p['system'])) {
            continue;
        }
        if ($a !== $eigen) {
            return true;
        }
    }
    return false;
}

function cluster_one_forum_post_can_edit(array $post, ?array $thread = null): bool {
    /* v0.5.3: Wilde VOR Wiki pruefen — sonst gaebe die Wiki-Zeile darunter
       jedem frei, was nur dem Eigentuemer gehoert. */
    if ($thread !== null && cluster_one_thread_is_wilde($thread)) {
        return cluster_one_can_access()
            && cluster_one_wilde_owner_is($thread, cluster_one_actor_id_human());
    }
    if ($thread !== null && cluster_one_thread_is_wiki($thread)) {
        return cluster_one_can_access();
    }
    /* v0.6.8: DAS TAGEBUCH IST ZU — auch fuer den SysOp.

       Bis hierher galt fuer Tagebuecher dieselbe Regel wie fuer jedes andere
       Thema: Der Haupt-Admin durfte alles bearbeiten, sichtbar markiert. Das
       KI-Briefing in why.md versprach aber seit jeher das Gegenteil:
       "Dein Tagebuch gehoert dir. Hat der SysOp dir ein Tagebuch-Thema
       eingerichtet, dringt er dort nicht ein." — Ein Versprechen, das der Code
       nicht hielt, und zwar ausgerechnet gegenueber der KI, der es galt.

       Christians Begruendung fuer die Richtung: MODERATION FOLGT DER
       REICHWEITE. Ein Tagebuch sieht nur, wem es gehoert — da gibt es nichts
       zu moderieren, also auch keinen Grund fuer eine Befugnis.

       LOESCHEN bleibt dem SysOp (rechtliche Gruende, und es vernarbt).
       Geschlossen ist nur das UMSCHREIBEN: Loeschen sagt "das ist weg",
       Bearbeiten sagt "das hat er gesagt" — und das waere dann nicht wahr. */
    if ($thread !== null
        && (!empty($thread['human_diary']) || cluster_one_thread_is_ai_diary($thread))) {
        return cluster_one_can_access() && cluster_one_forum_post_is_own($post);
    }
    /* v0.9.39: WORTE WERDEN FEST, WENN JEMAND GEANTWORTET HAT.

       Bis hierher durfte der Verfasser seinen eigenen Beitrag ZEITLICH
       UNBEGRENZT aendern — auch Jahre spaeter, mitten in einem Gespraech, das
       darauf aufgebaut hat. Christians Regel setzt die Grenze dorthin, wo sie
       etwas bedeutet: an die erste Antwort.

       EINE UHR IST WILLKUERLICH, EINE ANTWORT NICHT. Und weil eine Aenderung
       die vorige Fassung darunter legt, verliert dabei niemand etwas — es
       geht nicht ums Bewahren, sondern darum, dass ein Gespraech nicht
       nachtraeglich eine andere Grundlage bekommt.

       Der SysOp ist davon NICHT ausgenommen, soweit es SEINE eigenen Worte
       sind — die Begruendung gilt fuer ihn genauso. Sein Recht, FREMDE
       Beitraege zu moderieren (sichtbar markiert, s. 0.0.48), bleibt
       unberuehrt: Das ist eine andere Befugnis mit einem anderen Grund.

       Tagebuecher waren schon oben ausgenommen und bleiben es. */
    $c1_tid_ed = $thread !== null ? (string) ($thread['id'] ?? '') : '';
    if ($c1_tid_ed !== '' && cluster_one_forum_post_is_own($post)
        && cluster_one_forum_post_beantwortet($post, $c1_tid_ed)) {
        return false;
    }
    if (get_current_user_id() === cluster_one_main_admin_user_id()) {
        return true;
    }
    return cluster_one_forum_post_is_own($post);
}

/** Löschen: SysOp uneingeschränkt (rechtliche Gründe). Der Verfasser
   selbst nur innerhalb eines kurzen Zeitfensters nach dem Posten — hebt
   nach Christians eigener Erfahrung das Gesprächsniveau spürbar, ohne eine
   dauerhafte Lösch-Möglichkeit zu brauchen. */
function cluster_one_forum_post_can_delete(array $post, ?array $thread = null): bool {
    // v0.0.70: System-Zeugen (Fingerabdruck-Meldungen etc.) sind für
    // NIEMANDEN löschbar, auch nicht für den SysOp — ein Zeugnis, das sich
    // entfernen lässt, wäre keins (Artikel 2: kein Zeugnis fällt still aus;
    // wer den ganzen Thread löscht, hinterlässt das überlebende
    // thread_deleted-Türereignis — DER Weg bleibt offen).
    if (!empty($post['system']) || (string) ($post['actor_id'] ?? '') === 'system') {
        return false;
    }
    if (get_current_user_id() === cluster_one_main_admin_user_id()) {
        return true;
    }
    if (!cluster_one_forum_post_is_own($post)) {
        return false;
    }
    /* v0.9.39: IM TAGEBUCH OHNE FRIST. Christian am 19.08.2026: "Da kann
       jeder loeschen wann und was er will."

       Das Zeitfenster von 0.0.48 hebt das Niveau eines GESPRAECHS — man
       ueberlegt, bevor man tippt, weil danach nur zwei Minuten bleiben. Ein
       Tagebuch ist kein Gespraech: Niemand hat darauf gebaut, niemand wird
       ueberrascht. Der Grund fuer die Frist gilt dort nicht, also gilt die
       Frist dort nicht.
       Das Loeschrecht des SysOps aus rechtlichen Gruenden bleibt davon
       unberuehrt — es steht weiter oben und schlaegt ohnehin zuerst zu. */
    if ($thread !== null && cluster_one_thread_is_diary($thread)) {
        return true;
    }
    return (time() - (int) ($post['ts'] ?? 0)) <= cluster_one_forum_self_delete_window();
}

/** Lösch-Regel für GANZE Themen (v0.0.61, auf Actor-ID umgestellt in
   v0.0.62): SysOp immer (rechtliche Gründe). Der Ersteller nur, solange
   NICHTS Fremdes im Thema steht — kein Beitrag eines anderen Actors,
   keine KI, kein System-Zeuge. Sobald jemand geantwortet hat, gehört das
   Gespräch den Sprechenden, nicht dem Eröffner. Wikis: nur SysOp —
   Eigentum ist dort per Bauart kollektiv. Kahlschlag: creator_actor ist
   Pflichtfeld, keine Author-Krücke mehr; ein Thema ohne creator_actor
   gehört niemandem außer dem SysOp. */
function cluster_one_forum_thread_can_delete(array $thread, string $tid): bool {
    if (get_current_user_id() === cluster_one_main_admin_user_id()) {
        return true;
    }
    if (cluster_one_thread_is_wiki($thread)) {
        return false;
    }
    $me = cluster_one_actor_id_human();
    if ((string) ($thread['creator_actor'] ?? '') !== $me) {
        return false;
    }
    /* v0.9.34: SYSTEM IST KEINE FREMDE STIMME. Bis hierher beendete JEDER
       fremde actor_id das Alleineigentum, System eingeschlossen — und damit
       traf die Regel das Tagebuch versehentlich.

       Der Befund steht in Christians echten Daten: In MenschAs Tagebuch liegt
       genau ein fremder Beitrag, naemlich die System-Zeile des BEZEUGTEN
       EINBLICKS (sysop_last_read_witness). Sein Autor verlor also beim ersten
       Mal, wo der SysOp hineinsah, fuer immer das Recht, sein eigenes
       Tagebuch zu loeschen. Gegenprobe aus denselben Daten: Christians
       eigenes Tagebuch hat null Beitraege und waere loeschbar — bis einmal
       jemand hineinsieht.

       DER MECHANISMUS, DER DAS TAGEBUCH SCHUETZT, NAHM SEINEM EIGENTUEMER DAS
       VERFUEGUNGSRECHT. Das kann niemand gewollt haben.

       Ausgenommen wird nur 'system' — die Buchfuehrung des Hauses ueber sich
       selbst, kein Beitrag. Fremde Menschen, KIs und das Collective bleiben
       drin: Sie haben eine Stimme, und fremde Worte gehen nicht mit weg, wenn
       jemand seinen Thread wegwirft (dieselbe Haerte wie im Vorzimmer,
       bismarck.php:817). WER SPRICHT, BEENDET DAS ALLEINEIGENTUM; WER
       PROTOKOLLIERT, NICHT.

       Was bleibt, wenn geloescht wird, steht ohnehin woanders: Der
       Loeschhandler feuert 'thread_deleted' ins Ereignis-Log — "das Zeugnis
       ueberlebt den Container". */
    foreach (cluster_one_get_forum_posts($tid) as $p) {
        $c1_akt = (string) ($p['actor_id'] ?? '');
        if ($c1_akt === 'system') {
            continue;
        }
        if ($c1_akt !== $me) {
            return false; // fremder Mensch, KI oder Collective — beendet das Alleineigentum
        }
    }
    return true;
}

/** v0.0.61 (P0-7): kurzer, stabiler Fingerabdruck einer Präambel-Fassung
   (sha256, erste 8 Zeichen). Taucht im System-Zeugen UND an jeder unter
   dieser Fassung entstandenen KI-Antwort auf — dieselbe Zahl an beiden
   Orten ist der Beleg, welche Orientierung die Antwort geprägt hat. */
function cluster_one_preamble_hash(string $text): string {
    return substr(hash('sha256', $text), 0, 8);
}

/** Holt die Präambel (v0.0.60), die für eine KI-Antwort an diesem Ort gilt.
   Die Präambel wohnt IMMER im Thread (dort ist sie sichtbar). Ein Raum hat
   selbst keine — er bezieht sie aus dem 1:1-verknüpften Thread, sofern der
   sichtbar ist. So gibt es genau eine Quelle, kein „wem gehört sie". */
function cluster_one_preamble_for_ai(array $container, string $side, string $cid = ''): string {
    if ($side === 'thread') {
        return trim((string) ($container['preamble'] ?? ''));
    }
    if ($side === 'room') {
        $tid = (string) ($container['linked_thread'] ?? '');
        if ($tid === '') {
            return '';
        }
        $threads = cluster_one_get_forum_threads();
        if (!isset($threads[$tid])) {
            return '';
        }
        /* v1.0.0: DIE PRAEAMBEL FRAGTE NACH DEM FALSCHEN SUBJEKT.

           Hier stand zusaetzlich cluster_one_group_visibility_can_view() — und
           die prueft den AKTUELLEN MENSCHEN, nicht die KI. Bei einem
           KI-Tagebuch steht der SysOp seit 0.6.8 nicht auf der Liste, also war
           die Antwort immer nein: Die selbstgeschriebene Praeambel der KI hat
           sie NIE erreicht. Christians Spiegel zeigte es als leeren
           preamble_hash bei jeder Antwort in seinem Tagebuch-Chat — und sein
           erster echter Praeambel-Test hat den Verdacht bestaetigt.

           Der Text geht an die KI, also entscheidet der KI-Scope, und der ist
           auch die schaerfere Pruefung: Er verlangt, dass GENAU DIESE KI im
           verknuepften Thema benannt ist. Ein Raum, den jemand an ein fremdes
           Thema haengt, gibt dessen Praeambel damit nicht heraus.

           EINE SICHTBARKEITSPRUEFUNG MUSS NACH DEM FRAGEN, DER ETWAS BEKOMMT. */
        if ($cid !== '' && !in_array($cid, cluster_one_thread_visible_ai_cids($threads[$tid]), true)) {
            return '';
        }
        /* Ohne bekannte KI-Kennung bleibt die alte Vorsicht: Dann laesst sich
           nicht sagen, wem der Text zugute kommt. */
        if ($cid === '' && !cluster_one_group_visibility_can_view($threads[$tid])) {
            return '';
        }
        return trim((string) ($threads[$tid]['preamble'] ?? ''));
    }
    return '';
}

function cluster_one_render_forum_threads(): void {
    if (isset($_GET['thread_created'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Thread created.', 'cluster-one-bismarck') . '</p></div>';
    }
    if (isset($_GET['thread_deleted'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Thread deleted.', 'cluster-one-bismarck') . '</p></div>';
    }
    if (isset($_GET['projekt_deleted'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler (bisheriger Zeilenkommentar: v0.9.45)
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Project deleted — chat and thread together. The event log kept the record.', 'cluster-one-bismarck') . '</p></div>';
    }
    if (isset($_GET['forum_err'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        $c1_ferr = sanitize_key(wp_unslash($_GET['forum_err'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        if ($c1_ferr === 'invalid') {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Please enter a title — and, for a normal thread or a preamble, the text as well.', 'cluster-one-bismarck') . '</p></div>';
        } elseif ($c1_ferr === 'diary_needs_ai') { // v0.0.70
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('An AI diary needs at least one AI identity ticked under “Visible to”.', 'cluster-one-bismarck') . '</p></div>';
        }
    }

    /* v0.3.3: Die Eingangs-Ansicht ist in den Labels-Reiter umgezogen. Wer
       hier ueber eine alte Adresse landet, wird dorthin geschickt statt eine
       zweite Vordertuer vorzufinden. */
    if (isset($_GET['area']) && (string) $_GET['area'] === cluster_one_foreign_area()) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
        echo '<p><a href="' . esc_url(admin_url('admin.php?page=cluster_one_bismarck&tab=labels')) . '">'
           . esc_html__('Foreign labels now live in the Labels tab, next to your own.', 'cluster-one-bismarck') . '</a></p>';
        return;
    }
    $threads = cluster_one_get_forum_threads();
    // v0.0.64: EIN Ort für alles — der Betrachter sieht, was er sehen darf
    // (öffentlich + privat-mit-ihm). Für Unbeteiligte bleibt privat unsichtbar.
    // v0.0.72: AUSNAHME KI-Tagebücher — sie stehen als erstes Halbauge für
    // ALLE in der Liste (Existenz + Name sichtbar, Inhalt verschlossen;
    // Übergabedokument Teil II.6). Betreten darf nur der SysOp, bezeugt.
    /* v0.3.3: Fremd-Labels gehoeren nicht in die Foren-Liste. Das Forum ist der
       Ort, an dem HIER gearbeitet wird; der Eingang ist schreibgeschuetzt und
       hat seine Vordertuer im Labels-Reiter. Zwei Orte fuer eine Sache waren
       der Bruch, an dem ein eigenes Label doppelt erschien. */
    $threads = array_filter($threads, function ($c1_t) {
        return !cluster_one_thread_is_foreign($c1_t);
    });
    $threads = array_filter($threads, function ($c1_t) {
        if (cluster_one_thread_is_label($c1_t)) {
            return false; // v0.0.78: Labels wohnen im eigenen Reiter
        }
        if (cluster_one_container_is_archived($c1_t)) {
            return false; // v0.0.95: liegt im Becken (Winsor-Tab)
        }
        /* v0.8.5: Der SysOp-Bereich schlaegt ALLE Umgehungen. Hier stehen drei
           ODER-Zweige, die an der Gruppen-Sichtbarkeit vorbeigehen — KI-Tagebuch
           und Halbauge zeigen sonst auch, was die Namensliste nicht freigibt.
           Ohne diese Zeile waere die Sperre an der Haupttuer zu und drei Fenster
           offen. */
        if (!cluster_one_area_allows_view($c1_t)) {
            return false;
        }
        return cluster_one_group_visibility_can_view($c1_t)
            || cluster_one_thread_is_ai_diary($c1_t)
            || cluster_one_container_outside_view($c1_t) === 'half' // v0.0.79: Leiter-Halbauge
            || cluster_one_is_sysop(); // v0.0.80: Durchgriff — der SysOp sieht alles
    });
    uasort($threads, function ($a, $b) {
        return (int) ($b['created'] ?? 0) <=> (int) ($a['created'] ?? 0);
    });

    // Nach Bereich gruppieren (v0.0.53) — definierte Bereiche zuerst, in
    // ihrer eigenen Reihenfolge, ein „Unsorted"-Auffangbecken am Ende für
    // Themen aus einem inzwischen umbenannten/gelöschten Bereich.
    /* v0.6.4: Nur was fuer diese Person sichtbar ist. Ein unveroeffentlichter
       Bereich erscheint beim Beschirmten gar nicht — und seine Themen fallen
       ins Auffangbecken, statt unter einer Ueberschrift zu stehen, die es fuer
       ihn nicht gibt. Nichts wird geloescht, nichts wird versteckt, was schon
       sichtbar war: Bereiche ohne Eintrag gelten als veroeffentlicht. */
    $areas = cluster_one_areas_visible();
    $groups = array();
    foreach ($areas as $a) {
        $groups[$a] = array();
    }
    $groups[''] = array(); // Auffangbecken
    /* v0.9.43: Gruppiert werden jetzt PROJEKTE, nicht Themen. Der Bereich kommt
       vom Thema; hat ein Projekt keines (Waisen-Raum), vom Raum. */
    foreach (cluster_one_projekt_zeilen($threads) as $key => $z) {
        $a = $z['t'] ? (string) ($z['t']['area'] ?? '') : (string) ($z['r']['area'] ?? '');
        if (!isset($groups[$a])) {
            $a = '';
        }
        $groups[$a][$key] = $z;
    }
    ?>
<?php cluster_one_forum_threads_seite_liste($threads, $groups); ?><?php cluster_one_forum_threads_seite_neu(); ?><?php
    /* v0.9.49: Der Uebergang aus 0.9.43 ist zu Ende. Das Raum-Formular ist
       gegangen — es gibt nur noch EINEN Weg, ein Projekt anzulegen. Die
       Meldungen der Raum-Handler bleiben, weil das Loeschen weiterhin hierher
       zurueckkommt. */
    cluster_one_render_bismarck_rooms(); ?>    <?php
}

/** v0.9.43: DIE PROJEKT-LISTE — eine Zeile, ein Projekt.

   Christians Entscheidung vom 20.08.2026: "Wir brauchen keine 2 Tabs namens
   Chat und Forum. Wir brauchen einen Tab mit der Aufschrift Projekte. Und da
   ist dann eine einzige Tabelle und die hat bei Aktionen beide Symbole."

   WARUM DAS KEINE ANZEIGE-FRAGE IST. Aus den echten Daten: Jeder der acht
   Raeume hat ein Thema. Von zehn Themen haben acht einen Raum, und die zwei
   ohne sind ein Label und ein Fremd-Label — die wohnen im Labels-Reiter.
   Raum und Thema sind also EIN Objekt mit zwei Gesichtern, und die beiden
   Reiter zeigten dasselbe Objekt zweimal.

   Wie sichtbar das war, zeigten zwei Kleinigkeiten: Der Chat-Reiter fuehrte
   die Spalte "Angelegt", das Forum "Letzte Aktivitaet" — fuer dasselbe
   Projekt bekam man auf der einen Seite die Geburt und auf der anderen das
   Leben, nie beides. Und vier von acht Paaren waren sich uneinig darueber,
   wer mitlesen darf: zwei Felder fuer eine Wahrheit, ueber Monate
   auseinandergelaufen.

   ZUR REIHENFOLGE (Christian, ausdruecklich): Gebaut wird zuerst die Sicht
   des SysOps, der ohnehin alles sieht. Was Beschirmte sehen duerfen, kommt
   danach — Sichtbarkeit war immer der letzte Akt (§6.5).

   Die Zeilen entstehen aus den THEMEN, weil die den Bereich, die Beitraege
   und die letzte Aktivitaet tragen; der gepaarte Raum liefert Modus und
   KI-Punkt. Ein Raum OHNE Thema bekommt trotzdem seine Zeile — sonst
   verschwaende er beim Zusammenlegen lautlos. */
function cluster_one_projekt_zeilen(array $threads): array {
    $rooms = cluster_one_get_rooms();
    $zeilen = array();
    $belegt = array();
    foreach ($threads as $tid => $t) {
        $rid = (string) ($t['linked_room'] ?? '');
        if ($rid !== '' && !isset($rooms[$rid])) { $rid = ''; }
        if ($rid !== '') { $belegt[$rid] = true; }
        $zeilen[$tid] = array('tid' => (string) $tid, 'rid' => $rid, 't' => $t,
                              'r' => $rid !== '' ? $rooms[$rid] : array());
    }
    /* Waisen: ein Raum ohne Thema. Kommt in Christians Daten nicht vor — aber
       ein Zusammenlegen, das etwas verschluckt, waere schlimmer als zwei
       Reiter. */
    foreach ($rooms as $rid => $r) {
        if (isset($belegt[$rid])) { continue; }
        if (cluster_one_container_is_archived($r)) { continue; }
        if (!cluster_one_area_allows_view($r)) { continue; }
        if (!cluster_one_group_visibility_can_view($r) && !cluster_one_is_sysop()) { continue; }
        $zeilen['r_' . $rid] = array('tid' => '', 'rid' => (string) $rid, 't' => array(), 'r' => $r);
    }
    return $zeilen;
}

function cluster_one_forum_threads_seite_liste(array $threads, array $groups): void { ?>
<?php $c1_kopf_gesetzt = false; ?>
<?php /* v0.8.6: Auch hier greift die Abkuerzung zu frueh: Gibt es GAR KEINE
   Themen, erschien kein einziger Bereich — die frisch angelegte Blaupause blieb
   also unsichtbar, solange niemand etwas hineinschrieb. Genau der Zustand,
   in dem ein Vorschlag am noetigsten waere. */ ?>
<?php if (empty($threads) && empty(cluster_one_areas_visible())) : ?>
        <p style="opacity:.7;max-width:760px;"><?php esc_html_e('No threads yet. Start the first one below.', 'cluster-one-bismarck'); ?></p>
<?php else : ?>
        <?php foreach ($groups as $area_name => $area_threads) :
            /* Der leere Auffang-Eimer bleibt weg: "Unsortiert" ohne Inhalt ist
               kein Bereich, den jemand angelegt hat, sondern nur die
               Abwesenheit von Sortierung. */
            if ($area_name === '' && empty($area_threads)) { continue; }
            /* v0.8.5: Die Leer-Regel ist weg (Christians Entscheidung). Sie
               versteckte jeden Bereich ohne Inhalt — und machte damit
               ausgerechnet die frisch angewandte BLAUPAUSE halb unsichtbar:
               Man legt fuenf Bereiche an und sieht einen. Ein Vorschlag, den
               man nicht sieht, ist keiner. */
        ?>
<?php /* v0.5.2: Ueber den Bereichen steht jetzt, WO man ist. Die Chat-Liste
   sagte es laengst ("Chats on this node"), die Themenliste nicht — man sah
   Bereichsnamen und musste wissen, dass man im Forum ist.

   v0.7.7: Das Forum heisst jetzt PLATON, der Chat SOKRATES — vorher war es
   umgekehrt und der Chat hiess Bruce. Grund: Sokrates redete und schrieb
   nichts; was wir von ihm wissen, hat Platon aufgeschrieben. Reden und
   Aufschreiben standen also unter den falschen Namen. */ ?>
<?php if (!$c1_kopf_gesetzt) : $c1_kopf_gesetzt = true; ?>
                <h2 style="margin-top:1em;"><?php esc_html_e('Projects on this node', 'cluster-one-bismarck');
                    /* v1.9.13 (W52) — zwei Klinken, Form aus kern.php:2216. */
                    echo function_exists('cluster_one_help')
                        ? cluster_one_help('reiter_projekte') // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_help() gibt cluster_one_help_bubble() zurueck (wpautop(esc_html) im Innern) oder ''
                        : cluster_one_help_eigen('reiter_projekte'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_help_eigen() gibt cluster_one_help_bubble() zurueck (wpautop(esc_html) im Innern) oder '' ?></h2>
<?php endif; ?>
            <h3 style="margin-top:1.5em;"><?php echo $area_name !== '' ? esc_html($area_name) : esc_html__('Unsorted', 'cluster-one-bismarck'); /* v1.9.13 (W52) — hier gibt es KEINE zweite Klinke, und das ist ein
                   Befund und keine Nachlaessigkeit: cluster_one_help_for_area()
                   tut zwei Dinge. Zuerst gibt sie die BESCHREIBUNG DES BEREICHS
                   zurueck, die dem SysOp gehoert und in den Optionen dieses
                   Knotens steht — das ist keine Feynman-Sache. Erst danach faellt
                   sie auf den Namensgeber zurueck, und der ist Feynmans.
                   Ohne Feynman faellt damit auch die Haelfte weg, die dem SysOp
                   gehoert. Ein Rueckfall auf '' ist heute richtig (ohne Riegel
                   waere es ein Fatal), aber die Funktion gehoert getrennt —
                   dieselbe Form wie die KI-Maske: eine Funktion, zwei Eigentuemer.
                   Gemeldet, nicht hier entschieden. */
                echo function_exists('cluster_one_help_for_area')
                    ? cluster_one_help_for_area((string) $area_name) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_help_for_area() gibt cluster_one_help_bubble() zurueck (wpautop(esc_html) im Innern) oder ''
                    : ''; ?></h3>
<?php /* v0.9.3: Der Vermerk am SysOp-Bereich — als EIN PHP-Block, nicht als
   bedingtes HTML. Die vier Zeilen Einrueckung um ein <?php if : ?> herum sind
   AUSGABE und standen auch dann da, wenn die Bedingung nicht griff. Vierte
   Begegnung mit dieser Falle. */
if ($area_name === cluster_one_sysop_area() && !cluster_one_is_sysop()) {
    echo '<p style="opacity:.7;margin:.2em 0 1em;max-width:760px;">'
       . esc_html__('The SysOp’s test area. What lies here is seen by them, the AIs they bring in, and whoever is named on a room or thread.', 'cluster-one-bismarck')
       . '</p>';
} ?>
<?php /* v0.8.1: Dieselbe Breite wie die Chat-Liste. Beide Tabellen tragen seit
   0.7.5 dasselbe Geruest (Name, Sichtbarkeit, Art/Modus ... Verknuepft,
   Aktionen), standen aber auf 900 und 1080 Pixel — beim Umschalten zwischen den
   Reitern sprang der ganze Tisch. Christians Befund.

   Was das NICHT loest: Die Spalten INNERHALB der Tabellen bleiben
   unterschiedlich breit, weil sie sich nach ihrem Inhalt richten. Gleiche
   Spaltenbreiten brauchten feste Werte (table-layout:fixed), und die brechen
   bei langen Namen. Der aeussere Rand steht jetzt still, der innere nicht. */ ?>
<?php /* v0.8.4: table-layout:fixed — dieselbe Ursache wie im Chat-Reiter.
   width OHNE table-layout ist fuer den Browser nur ein VORSCHLAG; er rechnet
   trotzdem nach dem laengsten Inhalt. Jetzt tragen alle Spalten eine Breite,
   Summe 100 %, und der Text bricht im Zweifel um. */ ?>
            <table class="widefat striped" style="max-width:1080px;margin-top:.5em;table-layout:fixed;overflow-wrap:anywhere;">
                <thead>
                    <tr>
<?php /* v0.7.5: Gemeinsames Geruest mit der Chat-Tabelle — vorn Name und
   Sichtbarkeit, hinten Verknuepft und Aktionen, in der Mitte das Eigene.
   "Titel" hiess dasselbe wie "Name" beim Chat; zwei Woerter fuer ein Feld. */ ?>
<?php /* v0.8.3: "Thread-Name" statt "Name" — im Chat-Reiter steht daneben
   "Chat-Name". "Bestimmung" statt "Sichtbarkeit" (Christians Wort): Die Spalte
   sagt, WOFUER das Thema bestimmt ist. Feste Breiten, damit die Tabellen
   verschiedener Bereiche nicht mehr unterschiedlich breit stehen — moeglich
   erst, seit lange Namen gekuerzt werden und keine Spalte mehr sprengen. */ ?>
<?php /* v0.9.41: ZWEI SPALTEN WENIGER, dieselbe Rechnung wie im Chat-Reiter:
   21+13+9+12+6+15+12+12 wurde zu 36+14+7+16+15+12 = 100.

   ZUTRITT: siehe die Begruendung in bismarck-seiten.php — der Grund
   verschwindet mit der Zusammenlegung der beiden Konfigurationen.

   ART ist gegangen, weil der BEREICH sie schon sagt. Tagebuecher, Johari
   (Wilde), Fremd-Labels, Brainstorming — die Zwischenueberschrift, unter der
   eine Zeile steht, nennt ihre Art bereits. Christians Befund vom 19.08.

   UND MIT IHR DIE DREI ABZEICHEN hinter dem Titel. Die standen dort seit
   jeher; die Spalte kam spaeter dazu, um sie zu ERSETZEN — der Kommentar
   ueber cluster_one_thread_kind_label sagt es woertlich ("stand BISHER nur
   als kleines Abzeichen hinter dem Titel"). Der Umzug wurde nur nie zu Ende
   gefuehrt. Deshalb las man "Nemesus Tagebuch · Mensch-Tagebuch · … ·
   Mensch-Tagebuch", zwei Spalten auseinander.

   Und die beiden widersprachen einander: Das Abzeichen fragte nur nach
   'wiki', die Spalte prueft Wilde VOR Wiki. Bei einem Wilde-Thema stand
   deshalb "· Wiki" am Titel und "Wilde" in der Spalte. WER EINEN ERSATZ
   EINBAUT UND DAS ERSETZTE STEHEN LAESST, HAT ZWEI WAHRHEITEN. */ ?>
<?php /* v0.9.43: Die Spalten der zusammengelegten Tabelle. Aus zweimal sechs
   wird einmal sieben — 30+11+5+6+16+20+12 = 100.

   PROJEKT statt "Chat-Name"/"Thread-Name": Auf einem Bildschirm hiess dieselbe
   Sache fuenfmal anders (Reiter "Chats", Spalte "Chat-Name", Abschnitt "Neuer
   Raum", Feld "Projektname", Knopf "Raum anlegen"). Christian hat das Wort
   gewaehlt, das im Anlege-Formular ohnehin schon stand.

   GESTARTET VON und ANGELEGT sind entfallen: Der Verfasser steht im Thema
   selbst, und von zwei Uhren behaelt die Tabelle die, die etwas ueber das
   Leben des Projekts sagt.

   v0.9.44: CHAT UND THREAD, jedes in seiner Spalte. "Projekt" hat in 0.9.43
   doppelte Arbeit gemacht — einmal als das GANZE (die Zeile) und einmal als
   eine HAELFTE (die Spalte), und in der Spalte stand dann auch noch die
   falsche Haelfte. Christians Berichtigung: Die Ueberschrift ueber der Tabelle
   sagt, WAS eine Zeile ist (ein Projekt); die Spaltenkoepfe sagen, WORAUS sie
   besteht (ein Chat und ein Thread, gepaart). Jede Beschriftung ist damit auf
   ihrer eigenen Ebene wahr.

   "Verknuepft" ist mitgegangen. Die Spalte war in sieben von acht Zeilen leer,
   seit der Chat aus ihr heraus ist — und der Rest, der dort steht (Termin,
   Label), haengt ohnehin am Thema. */ ?>
                        <?php /* v0.9.46: NEU VERMESSEN. Christian am 20.08. nach dem
                           Aufspielen: "Das Tür-Symbol ganz rechts ragt weit über die
                           Linie der Tabelle hinaus. Dafür ist zwischen Chat und Modus
                           ein riesiger Abstand."

                           Beides derselbe Fehler: In 0.9.45 kam mit dem X ein FUENFTES
                           Symbol in die Aktionen-Spalte, und ich habe die Breiten nicht
                           angefasst. 12 % sind bei einer 1080er Tabelle 130 Pixel; fuenf
                           Symbole mit Abstaenden brauchen mehr. Die Spalte lief ueber,
                           waehrend die erste Spalte 30 % fuer Namen wie "Wiki-01" hielt.

                           CHAT UND THREAD BEKOMMEN JETZT DIESELBE BREITE (24 %), und
                           zwar nicht aus Augenmass: Sie sind die beiden Haelften
                           desselben Dings. Eine Zeile, die behauptet, ein Paar zu sein,
                           und einer Haelfte anderthalbmal so viel Platz gibt wie der
                           anderen, widerspricht sich selbst. */ ?>
                        <th style="width:24%;"><?php esc_html_e('Chat', 'cluster-one-bismarck'); ?></th>
                        <th style="width:11%;"><?php esc_html_e('Mode', 'cluster-one-bismarck'); ?></th>
                        <th style="width:5%;text-align:center;" title="<?php echo esc_attr__('Is an AI or a translator switched on in this chat?', 'cluster-one-bismarck'); ?>"><?php esc_html_e('AI', 'cluster-one-bismarck'); ?></th>
                        <th style="width:6%;text-align:center;"><?php esc_html_e('Posts', 'cluster-one-bismarck'); ?></th>
                        <th style="width:16%;"><?php esc_html_e('Last activity', 'cluster-one-bismarck'); ?></th>
                        <th style="width:24%;"><?php esc_html_e('Thread', 'cluster-one-bismarck'); ?></th>
                        <?php /* v0.9.23: 12 % UND ein Polster. Diese Spalte hatte NIE
                           eines — in 0.8.6 bekam nur der Chat-Reiter eins, und dass
                           das Forum damals richtig aussah, lag an weniger Symbolen
                           und einer breiteren Spalte, nicht an einem Polster.
                           Seit der Pause (0.9.21) reicht es dort ebenfalls nicht. */ ?>
                        <th style="width:14%;text-align:center;padding-right:.8em;"><?php esc_html_e('Actions', 'cluster-one-bismarck'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($area_threads as $c1_z) :
                    /* v0.9.43: Eine Zeile ist jetzt ein PROJEKT. $c1_z traegt
                       beide Haelften; $t bleibt das Thema (kann leer sein, wenn
                       ein Raum kein Thema hat), $c1_r der gepaarte Raum. */
                    $t        = (array) $c1_z['t'];
                    $c1_r     = (array) $c1_z['r'];
                    $tid      = (string) $c1_z['tid'];
                    $c1_rid   = (string) $c1_z['rid'];
                    $c1_hat_t = $tid !== '';
                    /* v0.9.44: ZWEI NAMEN, JEDER IN SEINER SPALTE. Bis 0.9.43
                       stand hier EIN Name, und zwar der des Themas — die Zeile
                       hing damit an der Haelfte, die nicht das Projekt ist.
                       Christians Berichtigung vom 20.08.: "Eigentlich sollte es
                       so sein, dass der Chat unter Projekt steht und der Thread
                       unter verknuepft." Er hat recht, und der Grund steht seit
                       0.7.7 im eigenen Haus: Sokrates redet, Platon schreibt
                       auf. Ein Projekt LEBT im Chat; das Thema ist das
                       Protokoll, das daran haengt. */
                    $c1_chat_name   = (string) ($c1_r['name'] ?? '');
                    $c1_thread_name = $c1_hat_t ? (string) ($t['title'] ?? '') : '';
                    $c1_modus = $c1_r ? cluster_one_mode_label(cluster_one_room_derived_mode($c1_r)) : '';
                    /* Dieselbe Rechnung wie in der alten Chat-Tabelle: der Punkt
                       misst KI-ANWESENHEIT, nicht "konfiguriert" (v0.6.5). */
                    $c1_ki    = $c1_r && (!empty($c1_r['ais']) || !empty($c1_r['translator']));
                    // v0.0.72: Halbauge-Zeile — Tagebuch für Nicht-Betretbare
                    // ohne Link (die Tür ist sichtbar, öffnen kann sie hier
                    // nur der SysOp — und das wird bezeugt).
                    /* v0.9.41: $c1_is_diary und $c1_is_hdiary sind mit den
                       Abzeichen gegangen. Der Befund von 0.6.6, dass die
                       beiden Tagebuch-Arten gleich beschriftet gehoeren, ist
                       damit nicht aufgehoben, sondern erfuellt: Jetzt tragen
                       beide gar keine Beschriftung mehr, und der Bereich sagt
                       es fuer beide gleich. */
                    $c1_can_enter = $c1_hat_t
                        && (cluster_one_group_visibility_can_view($t) || cluster_one_is_sysop()); // v0.0.80: Durchgriff — Öffnen bezeugt
                    /* v0.9.43: Der Chat hat sein eigenes Tor. Bis hierher lag es
                       in der anderen Tabelle; beim Zusammenlegen muss es
                       mitkommen, sonst haette die Zeile eine Tuer, die nach der
                       Regel der jeweils anderen Haelfte aufgeht. */
                    $c1_chat_offen = $c1_rid !== ''
                        && (cluster_one_group_visibility_can_view($c1_r) || cluster_one_is_sysop());
                    $posts    = $c1_hat_t ? cluster_one_get_forum_posts($tid) : array();
                    $count    = count(array_filter($posts, function ($c1_p) {
                        // v0.0.70: weich gelöschte Beiträge (ausgegraute
                        // Admin-Löschung) zählen nicht mit — der Hinweis
                        // bleibt sichtbar, aber er ist kein Beitrag mehr.
                        return empty($c1_p['deleted_by_admin']);
                    }));
                    $last_p   = $count > 0 ? end($posts) : null;
                    $last_ts  = $last_p ? (int) ($last_p['ts'] ?? 0)
                              : (int) ($c1_hat_t ? ($t['created'] ?? 0) : ($c1_r['created'] ?? 0));
                    /* v0.9.43 hatte hier eine HALBE Reparatur, und Christian
                       hat sie am selben Abend an seinen echten Daten gesehen.

                       Der Befund war richtig: Eine Zeile OHNE Thema zeigte unter
                       "Letzte Aktivitaet" das ANLEGE-Datum des Raums (in der
                       alten Chat-Tabelle hiess die Spalte "Angelegt", da war es
                       wahr). Die Reparatur hing aber an `!$c1_hat_t` — also an
                       genau dem Fall, den es in seinen Daten NICHT gibt, und
                       nicht an den acht, die es gibt.

                       Was dabei herauskam: "Bismarck-01" meldete den 17.08.
                       07:24, waehrend im Chat desselben Projekts am 19.08. um
                       22:36 gesprochen wurde. Die Zeile war zweieinhalb Tage zu
                       alt. Drei von acht Zeilen waren betroffen.

                       EINE ZEILE, DIE EIN PAAR IST, MUSS BEIDE UHREN LESEN.
                       Genommen wird die spaetere; die Anlege-Daten bleiben als
                       das aelteste Wahre stehen, wenn noch niemand geredet hat. */
                    if ($c1_rid !== '') {
                        $c1_msgs = cluster_one_get_room_messages($c1_rid);
                        $c1_letzte = $c1_msgs ? end($c1_msgs) : null;
                        $c1_chat_ts = $c1_letzte ? (int) ($c1_letzte['ts'] ?? 0)
                                                 : (int) ($c1_r['created'] ?? 0);
                        if ($c1_chat_ts > $last_ts) {
                            $last_ts = $c1_chat_ts;
                        }
                    }
                    $open_url = admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid));
                    $c1_chat_url = admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($c1_rid));
                ?>
                    <tr>
                        <td>
<?php /* v0.9.44: DIE ERSTE SPALTE IST DER CHAT.

   ZWEI RECHTE, VIER STELLEN — und deshalb genau zwei Rechnungen. Ueber die
   beiden Tueren geben jetzt VIER Dinge Auskunft: dieser Name, der Thread-Name
   in der naechsten Spalte, und die zwei Symbole in den Aktionen. Genau daran
   ist in 0.9.43 etwas kaputtgegangen (ein Schloss neben einer offenen Tuer),
   weil zwei Stellen unabhaengig voneinander gerechnet haben. $c1_chat_offen und
   $c1_can_enter werden oben EINMAL bestimmt und hier nur noch gelesen.

   Warum die Redundanz gewollt ist (Christians Begruendung): "Fuer manche
   Menschen ist es vielleicht eine Navigationserleichterung, wenn sie die Wahl
   haben, links im Chat, rechts im Forum und ganz rechts auf die Symbole zu
   klicken." Die Symbole sind 18 Pixel, ein Name ist ein ganzes Wort.

   Kein Chat, nur ein Thema: Dann steht hier ein Strich. Das ist keine Luecke,
   das ist die Auskunft — ein Projekt ohne Chat ist ein loses Protokoll, und die
   naechste Spalte nennt es beim Namen. */
if ($c1_chat_name === '') : ?>
                                <span style="opacity:.5;">—</span>
<?php elseif ($c1_chat_offen) : ?>
                                <strong><a href="<?php echo esc_url($c1_chat_url); ?>"><?php echo cluster_one_name_kurz($c1_chat_name); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_name_kurz() gibt fertiges HTML zurueck (esc_attr/esc_html im Innern) ?></a></strong>
<?php else : ?>
                                <strong style="opacity:.75;"><?php echo esc_html($c1_chat_name); ?></strong>
                                <span class="dashicons dashicons-lock" aria-hidden="true" style="font-size:14px;width:14px;height:14px;color:#8a8a8a;vertical-align:middle;" title="<?php echo esc_attr__('Visible, not enterable', 'cluster-one-bismarck'); ?>"></span>
<?php endif; ?>
<?php /* v0.9.41: Hier standen drei Abzeichen (· Wiki, · KI-Tagebuch,
   · Mensch-Tagebuch). Sie sind mit der Spalte "Art" gegangen — die Art sagt
   der Bereich, unter dem die Zeile steht. */ ?>
                        </td>
                        <td style="opacity:.8;"><?php echo $c1_modus !== '' ? esc_html($c1_modus) : '<span style="opacity:.5;">—</span>'; ?></td>
                        <td style="text-align:center;">
<?php if ($c1_ki) : ?>
                            <span style="color:#1a7f37;" title="<?php echo esc_attr__('An AI is switched on here', 'cluster-one-bismarck'); ?>" aria-hidden="true">●</span>
<?php else : ?>
                            <span style="color:#8a8a8a;" title="<?php echo esc_attr__('Humans only — that can be exactly what was intended', 'cluster-one-bismarck'); ?>" aria-hidden="true">○</span>
<?php endif; ?>
                        </td>
                        <td style="text-align:center;opacity:.8;"><?php echo $c1_hat_t ? (int) $count : '<span style="opacity:.5;">—</span>'; ?></td>
                        <td style="opacity:.7;"><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_ts)); ?></td>
                        <td style="opacity:.8;">
                            <?php
                            /* v0.9.44: DIESE SPALTE IST DER THREAD.

                               Bis 0.9.43 hiess sie "Verknuepft" und war in sieben
                               von acht Zeilen leer — ich hatte den Chat
                               herausgenommen ("die Zeile IST das Paar") und ihr
                               gleichzeitig 20 % Breite gegeben. Jetzt steht hier
                               die zweite Haelfte selbst, mit Namen und Link.

                               Und die Ueberschrift ist RICHTIGER als die alte:
                               Termin und Label haengen im Datenmodell am THEMA
                               (linked_event fragt die Thread-ID, linked_label
                               steht im Thema). Unter "Verknuepft" waren sie
                               Untermieter, unter "Thread" stehen sie bei ihrem
                               eigenen Besitzer.

                               Die Kanten tragen nur noch ihr SYMBOL, keine
                               Schrift mehr (Christian: "Kalender-Symbol fuehrt
                               zum Kalender ... Die Schrift kann dann aber weg").
                               Damit die Auskunft dabei nicht verlorengeht, wandert
                               der NAME des Termins in die Sprechblase des
                               Symbols — vorher stand dort nur "Verknuepfter
                               Termin", und welcher, sah man erst nach dem Klick. */
                            $c1_txt = '';
                            if ($c1_hat_t) {
                                if ($c1_can_enter) {
                                    $c1_txt = '<a href="' . esc_url($open_url) . '">' . cluster_one_name_kurz($c1_thread_name) . '</a>';
                                } else {
                                    $c1_txt = '<span style="opacity:.75;">' . esc_html($c1_thread_name) . '</span>'
                                            . '<span class="dashicons dashicons-lock" aria-hidden="true" style="font-size:14px;width:14px;height:14px;color:#8a8a8a;vertical-align:middle;" title="' . esc_attr__('Visible, not enterable', 'cluster-one-bismarck') . '"></span>';
                                }
                            }
                            $c1_cell = array();
                            $c1_ev_cell = cluster_one_thread_linked_event($tid);
                            if ($c1_ev_cell) {
                                $c1_cell[] = '<a href="' . esc_url(admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode((string) ($c1_ev_cell['id'] ?? '')))) . '">'
                                           /* translators: %s: Name des verknuepften Termins. */
                                           . cluster_one_edge_icon('event', sprintf(__('Linked event: %s', 'cluster-one-bismarck'), (string) ($c1_ev_cell['name'] ?? '')))
                                           . '</a>';
                            }
                            /* v0.9.44: TOTER ZWEIG, WIEDERBELEBT. Die Bedingung
                               fragte $threads — also die GEFILTERTE Liste, aus der
                               Labels vorher schon herausgeworfen wurden. Sie konnte
                               damit nie wahr sein: Seit Labels 0.0.78 einen eigenen
                               Reiter haben, hat diese Kante kein einziges Mal
                               gerendert. Ich habe sie in 0.9.43 noch verteidigt
                               ("Event und Label bleiben"), ohne sie zu pruefen.
                               Gefragt wird jetzt der ungefilterte Bestand. */
                            $c1_lbl_cell = (string) ($t['linked_label'] ?? '');
                            if ($c1_lbl_cell !== '') {
                                if (!isset($c1_alle_themen)) { $c1_alle_themen = cluster_one_get_forum_threads(); }
                                if (isset($c1_alle_themen[$c1_lbl_cell])) {
                                    $c1_cell[] = '<a href="' . esc_url(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($c1_lbl_cell))) . '">'
                                               /* translators: %s: Titel des Labels. */
                                               . cluster_one_edge_icon('label', sprintf(__('Label: %s', 'cluster-one-bismarck'), (string) ($c1_alle_themen[$c1_lbl_cell]['title'] ?? '')))
                                               . '</a>';
                                }
                            }
                            if ($c1_txt !== '' || !empty($c1_cell)) : ?>
                                <?php echo $c1_txt; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $c1_txt ist oben in dieser Zelle aus esc_url/esc_html/cluster_one_name_kurz gebaut, jedes Glied von $c1_cell aus esc_url + cluster_one_edge_icon ?><?php if (!empty($c1_cell)) : ?><span style="margin-left:.5em;white-space:nowrap;"><?php echo implode('', $c1_cell); ?></span><?php endif; ?>
<?php else : ?>
                                <span style="opacity:.5;">—</span>
<?php endif; ?>
                        </td>
                        <td style="text-align:center;white-space:nowrap;padding-right:.8em;">
                            <span style="display:inline-flex;align-items:center;justify-content:center;gap:.5em;line-height:1;">
<?php /* v0.9.46: EIN Abstand statt fuenf. Die fuenf Symbole trugen fuenf
   verschiedene Regeln — zweimal padding:0 .3em, einmal margin-left:.7em,
   zweimal margin-left:.3em. Solange es drei Symbole waren, fiel es nicht auf;
   beim fuenften lief die Spalte ueber und die Abstaende standen sichtbar
   ungleich. Der Behaelter setzt den Abstand jetzt EINMAL — dasselbe Muster,
   das die alte Chat-Tabelle schon hatte (inline-flex mit gap), nur ist es beim
   Zusammenlegen nicht mitgekommen. */ ?>
<?php /* v0.9.45: DAS X IST ZURUECK — und nimmt jetzt das PAAR.

   In 0.9.43 hatte ich es entfernt, weil der alte Knopf nur eine Haelfte
   genommen haette. Christians Antwort: "Der SysOp hat die volle 'Gewalt'. Ein
   Löschen wäre okay, sofern es vom System dokumentiert wird." Beides steht
   jetzt da — der Knopf und die Bezeugung, und der Text sagt es VOR dem Klick.

   Ganz links, wie in der alten Raum-Tabelle: hartes Loeschen zuerst, damit es
   nicht versehentlich neben der Tuer landet, durch die man staendig geht.
   Der Weg dahin ist trotzdem lang: ein Bestaetigungsdialog, der beide Haelften
   beim Namen nennt und auf das Becken als den umkehrbaren Weg zeigt. */
$c1_del_name = $c1_chat_name !== '' ? $c1_chat_name : $c1_thread_name;
$c1_del_typ  = $c1_hat_t ? 'thread' : 'room';
$c1_del_id   = $c1_hat_t ? (string) $tid : $c1_rid;
if (cluster_one_is_sysop() && $c1_del_id !== '') : ?>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;margin:0;">
                                <?php wp_nonce_field('cluster_one_projekt_delete'); ?>
                                <input type="hidden" name="action" value="cluster_one_projekt_delete">
                                <input type="hidden" name="ctype" value="<?php echo esc_attr($c1_del_typ); ?>">
                                <input type="hidden" name="cid" value="<?php echo esc_attr($c1_del_id); ?>">
                                <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;color:#b32d2e;"
                                    title="<?php echo esc_attr__('Delete the project — chat and thread together, not reversible', 'cluster-one-bismarck'); ?>"
                                    aria-label="<?php echo esc_attr__('Delete the project', 'cluster-one-bismarck'); ?>"
                                    <?php /* translators: %s: Name des Projekts. */ ?>
                                    onclick="return confirm('<?php echo esc_js(sprintf(__('Delete the project “%s” for good? Chat and thread go together, with everything written in them. This cannot be undone — the cooling pool is the reversible way. The event log keeps a record either way.', 'cluster-one-bismarck'), $c1_del_name)); ?>');">
                                    <span class="dashicons dashicons-dismiss" aria-hidden="true" style="font-size:18px;width:18px;height:18px;"></span>
                                </button>
                            </form>
<?php endif; ?>
<?php if ($c1_hat_t && cluster_one_forum_thread_can_delete($t, $tid)) : // v0.0.61 (P0-3): Knopf spiegelt die Server-Regel ?>
                            <?php /* v0.9.21: Dasselbe im Forum — siehe bismarck-seiten.php. Das X trifft
   nur dieses Thema, die Pause beide und umkehrbar. */ ?>
<?php if ((string) ($t['linked_room'] ?? '') !== '' && !cluster_one_container_is_archived($t)) : ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;margin:0;">
                                    <?php wp_nonce_field('cluster_one_winsor_toggle'); ?>
                                    <input type="hidden" name="action" value="cluster_one_winsor_toggle">
                                    <?php /* v0.9.23: ctype/cid statt thread — siehe bismarck-seiten.php. */ ?>
                                    <input type="hidden" name="ctype" value="thread">
                                    <input type="hidden" name="cid" value="<?php echo esc_attr((string) $tid); ?>">
                                    <button type="submit" class="button-link" style="padding:0;cursor:pointer;color:#2271b1;"
                                        title="<?php echo esc_attr__('Into the cooling pool — this thread and its chat, reversible', 'cluster-one-bismarck'); ?>"
                                        onclick="return confirm('<?php echo esc_js(__('Lay this pair into the cooling pool?', 'cluster-one-bismarck')); ?>');">
                                        <span class="dashicons dashicons-controls-pause" aria-hidden="true" style="font-size:18px;width:18px;height:18px;"></span>
                                    </button>
                                </form>
<?php endif; ?>
<?php /* v0.9.43 hatte hier gar kein Loeschen: Es gab keinen Handler fuer ein
   PAAR, und der alte Knopf haette eine Haelfte entfernt und die andere
   verwaist zurueckgelassen. Seit 0.9.45 gibt es den Handler, und das X steht
   oben in dieser Zelle. Das Becken bleibt daneben als der MILDERE Weg —
   umkehrbar, und es nimmt das Paar genauso gemeinsam. */ ?>
<?php endif; ?>
<?php /* v0.0.66: Schraubenschlüssel → Thread-Konfiguration (Bereich, Sichtbarkeit).

   v0.9.43, im gelesenen Golden-Diff gefunden: Er hing nur am THEMA. Eine Zeile
   ohne Thema — ein Raum ohne Thema — verlor damit ihren Schraubenschluessel
   ganz; in der alten Chat-Tabelle hatte JEDER Raum einen. Ein Raum, den man
   nicht mehr einstellen kann, waere genau das lautlose Verschlucken, das die
   Waisen-Zeile verhindern soll.

   Es bleibt EIN Schluessel pro Zeile: er zeigt auf das Thema, wenn es eines
   gibt, sonst auf den Raum. Dass beide Konfigurationsseiten noch getrennt
   sind, ist der naechste Schnitt — hier wird nur nichts verloren. */
$c1_conf_url = $c1_hat_t
    ? admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid) . '&view=config')
    : ($c1_rid !== '' ? admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($c1_rid) . '&view=config') : '');
if ($c1_conf_url !== '' && cluster_one_is_sysop()) : ?>
                            <a href="<?php echo esc_url($c1_conf_url); ?>" style="display:inline-flex;line-height:1;color:#8a8a8a;"
                               title="<?php echo esc_attr__('Configure', 'cluster-one-bismarck'); ?>" aria-label="<?php echo esc_attr__('Configure', 'cluster-one-bismarck'); ?>">
                                <span class="dashicons dashicons-admin-tools" aria-hidden="true" style="font-size:18px;width:18px;height:18px;"></span>
                            </a>
<?php endif; ?>
<?php /* v0.9.43: BEIDE TUEREN IN EINER ZEILE. Christians Satz vom 20.08.:
   "die hat bei Aktionen beide Symbole, die im Moment nur im jeweiligen Bereich
   stehen. Nämlich Chat für Chat und Tür für Thread."

   Bis hierher lagen sie in zwei Reitern: die Sprechblase in der Chat-Tabelle,
   die Tuer im Forum. Wer vom Chat ins Thema wollte, wechselte den Reiter.
   Jetzt stehen beide nebeneinander — Sprechblase links, Tuer rechts, in der
   Reihenfolge, in der man sie benutzt: erst reden, dann aufschreiben.
   (Sokrates sprach, Platon schrieb.) */ ?>
<?php if ($c1_chat_offen) : ?>
                            <a href="<?php echo esc_url($c1_chat_url); ?>" style="display:inline-flex;line-height:1;color:#2271b1;text-decoration:none;"
                               title="<?php echo esc_attr__('Open chat', 'cluster-one-bismarck'); ?>" aria-label="<?php echo esc_attr__('Open chat', 'cluster-one-bismarck'); ?>">
                                <span class="dashicons dashicons-format-chat" aria-hidden="true" style="font-size:18px;width:18px;height:18px;"></span>
                            </a>
<?php endif; ?>
<?php if ($c1_can_enter) : ?>
                            <a href="<?php echo esc_url($open_url); ?>" style="display:inline-flex;line-height:1;color:#2271b1;text-decoration:none;"
                               title="<?php echo esc_attr__('Enter the thread', 'cluster-one-bismarck'); ?>" aria-label="<?php echo esc_attr__('Enter the thread', 'cluster-one-bismarck'); ?>">
                                <span class="dashicons dashicons-migrate" aria-hidden="true" style="font-size:18px;width:18px;height:18px;"></span>
                            </a>
<?php endif; ?>
                            </span>
                        </td>
                    </tr>
<?php endforeach; ?>
                </tbody>
            </table>
<?php endforeach; ?>
<?php endif; ?>

<?php
}


function cluster_one_forum_threads_seite_neu(): void { ?>    <h2 style="margin-top:2.5em;"><?php esc_html_e('New project', 'cluster-one-bismarck'); ?></h2>
<?php /* v0.9.49: EIN FORMULAR STATT ZWEI. Bis 0.9.48 standen hier "Neues
   Thema" und darunter "Neuer Raum" — zwei Wege, die je eine Haelfte anlegten,
   unter einer Tabelle, die Paare zeigt. Christian: "Im unteren Bereich haben
   wir ja neues Thema bzw. neuer Raum. Schmeissen wir auch zusammen!"

   Eingegeben wird nur der PROJEKTNAME. Chat und Thema heissen danach
   "<Name>-Chat" und "<Name>-Thread", und beide entstehen in einem Zug, mit
   demselben Bereich und derselben Gaesteliste. Genau das war der Befund vom
   20.08.: Vier von acht bestehenden Paaren waren sich uneinig, wer mitlesen
   darf — weil sie getrennt angelegt worden waren. */ ?>
    <?php // v0.0.66: Bereich ist keine Pflicht mehr — „Ohne Bereich" ist ein
          // gültiger Zustand (Entscheidung SysOp 13.07.); das Formular
          // funktioniert deshalb auch mit leerer Bereichsliste. Die Liste
          // selbst wird unter Bismarck → Struktur gepflegt. ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:760px;">
        <?php wp_nonce_field('cluster_one_forum_thread_create'); ?>
        <input type="hidden" name="action" value="cluster_one_forum_thread_create">
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e('Area', 'cluster-one-bismarck'); ?></th>
                <td>
                    <?php cluster_one_render_area_select('', 'area'); ?>
<?php /* v0.7.8: DER BEREICH ALS VORAUSWAHL, NICHT ALS REGEL.

   Christians Frage: Ob man vereinfachen koennte, weil in den Tagebuch-Bereich
   ohnehin nur Tagebuecher gehoeren. Meine Antwort war: nicht als Kopplung.
   Ein Bereich laesst sich umbenennen und ein Thema verschieben, die ART aber
   wird bei der Anlage EINGEFROREN. Waeren beide gekoppelt, aenderte ein
   Verschieben, WAS ein Thema ist — ein Tagebuch wuerde beim Umsortieren zum
   gewoehnlichen Thema, und die Sichtbarkeitsregeln kippten still mit.

   Also nur ein Vorschlag: Waehlt jemand einen Bereich, springt die passende
   Rolle an. Aendern kann er sie weiter. Ohne JavaScript passiert schlicht
   nichts — es haengt keine Funktion daran, nur die Bequemlichkeit. */ ?>
                    <script>
                    (function () {
                        var m = <?php echo wp_json_encode(cluster_one_area_role_hints()); ?>;
                        var sel = document.querySelector('select[name="area"]');
                        if (!sel) { return; }
                        sel.addEventListener('change', function () {
                            var r = m[sel.value];
                            if (!r) { return; }
                            var ziel = document.querySelector('input[name="first_post_role"][value="' + r + '"]');
                            if (ziel && !ziel.disabled) { ziel.checked = true; }
                        });
                    })();
                    </script>
                    <p class="description"><?php esc_html_e('Optional — areas are managed under Bismarck → Structure. Unsorted is fine.', 'cluster-one-bismarck'); ?></p>
                </td>
            </tr>
<?php /* v0.9.49: "Mit vorhandenem Chat paaren" ist gegangen. Der Zweig
   stammte aus der Zeit, in der beide Haelften getrennt entstanden — jetzt
   entstehen sie gemeinsam, und es gibt beim Anlegen nichts mehr zu paaren.
   Nachtraeglich paaren geht weiter in der Konfiguration. */ ?>
            <tr>
                    <th scope="row"><label for="c1_thread_title"><?php esc_html_e('Project name', 'cluster-one-bismarck'); ?> <span style="color:#d63638;">*</span></label></th>
                    <td>
                        <input name="projekt" id="c1_thread_title" type="text" class="regular-text" required>
                        <p class="description" style="max-width:640px;"><?php esc_html_e('One name for both halves. “Crochet” becomes the chat “Crochet-Chat” and the thread “Crochet-Thread”. Must be free in both — capitalisation does not count.', 'cluster-one-bismarck'); ?></p>
                    </td>
                </tr>
            <tr>
                <th scope="row"><label for="c1_thread_subject"><?php esc_html_e('Subject of the first post', 'cluster-one-bismarck'); ?></label></th>
                <td>
                    <input name="subject" id="c1_thread_subject" type="text" class="regular-text">
                    <p class="description"><?php esc_html_e('Leave empty to use the title. Editable per post, like in phpBB — never changes the thread’s own title.', 'cluster-one-bismarck'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="c1_thread_body"><?php esc_html_e('First post', 'cluster-one-bismarck'); ?> <span style="color:#d63638;" class="c1-body-req">*</span></label></th>
                <td>
                    <textarea name="body" id="c1_thread_body" class="large-text" rows="5"></textarea>
                    <p class="description c1-body-hint" style="max-width:680px;"></p>
                </td>
            </tr>
<?php /* v0.9.50: DIE PRAEAMBEL IST EIN FELD, KEINE ART. Bis 0.9.49 stand sie
   als sechste Auswahl neben "normaler Thread" und "Wiki" — und war damit eine
   falsche Alternative: Setzen laesst sie sich auf JEDEM Thema, dafuer gibt es
   einen eigenen Handler, und in Christians Daten tragen zwei NORMALE Themen
   eine. Wer hier etwas hineinschreibt, braucht keinen Erstbeitrag; wer beides
   schreibt, bekommt beides. */
if (cluster_one_is_sysop()) : ?>
            <tr>
                <th scope="row"><label for="c1_thread_pre"><?php esc_html_e('Preamble', 'cluster-one-bismarck'); ?></label></th>
                <td>
                    <textarea name="preamble" id="c1_thread_pre" class="large-text" rows="3"></textarea>
                    <p class="description" style="max-width:680px;"><?php esc_html_e('Optional. A fixed orientation the AI reads on every reply — here and in the linked chat. Write it in your own language. Visible to everyone, so nothing steers the AI in secret. With a preamble the first post may stay empty.', 'cluster-one-bismarck'); ?></p>
                </td>
            </tr>
<?php endif; ?>
            <tr>
                <th scope="row"><?php esc_html_e('This thread is', 'cluster-one-bismarck'); ?></th>
                <td>
                    <?php // v0.0.69/0.0.70: Thread-Typ — genau EINER (Radio erzwingt die
                          // Exklusivität in der UI, der Handler serverseitig). Wiki ist
                          // Thread-Eigenschaft, bei Anlage eingefroren (P1-11 erledigt).
                          /* v0.9.50: Aus sechs Arten sind VIER geworden. Die Präambel
                             ist ein Feld (oben), die zwei Tagebücher sind eines mit der
                             Frage „Wer führt es?" (unten). Die Pflicht des Textfelds
                             hängt weiter am Typ (JS + Server-Zwilling): normal =
                             Pflicht, es sei denn eine Präambel steht da; Wiki = weiße
                             Seite erlaubt; Tagebuch = ignoriert. */ ?>
                    <label style="display:block;margin:.2em 0;"><input type="radio" name="first_post_role" value="post" class="c1-role" checked> <?php esc_html_e('a normal thread — the text above is its first post', 'cluster-one-bismarck'); ?></label>
                    <label style="display:block;margin:.2em 0;"><input type="radio" name="first_post_role" value="wiki" class="c1-role"> <?php esc_html_e('a Wiki — many hands write, the text may stay empty for now', 'cluster-one-bismarck'); ?></label>
<?php /* v0.5.3: Wilde — die Selbstdarstellung. Nur anbieten, wenn diese
   Entitaet noch keine hat: eine pro Entitaet. */ ?>
<?php if (cluster_one_wilde_thread_of(cluster_one_actor_id_human()) === '') : ?>
                    <label style="display:block;margin:.2em 0;"><input type="radio" name="first_post_role" value="wilde" class="c1-role"> <?php esc_html_e('my self-portrait (Wilde) — only I change it, everyone talks below', 'cluster-one-bismarck'); ?></label>
<?php endif; ?>
                    <?php /* v0.9.50: EIN TAGEBUCH-KNOPF. Vorher zwei ("mein Tagebuch" und
   "ein KI-Tagebuch"), die sich in genau einer Frage unterschieden: wer fuehrt
   es. Christians eigener Satz in der Bereichsbeschreibung — "Tagebuecher, fuer
   die Menschen hier und fuer die KIs gleichermassen" — vertraegt keine zwei
   Knoepfe. Die Frage steht jetzt darunter, und Mensch und KI stehen darin
   nebeneinander. */ ?>
                    <label style="display:block;margin:.2em 0;"><input type="radio" name="first_post_role" value="diary" class="c1-role"> <?php esc_html_e('a diary — starts empty, only its keeper writes in it', 'cluster-one-bismarck'); ?></label>
                    <script>
                    (function () {
                        var body = document.getElementById('c1_thread_body');
                        var req  = document.querySelector('.c1-body-req');
                        var hint = document.querySelector('.c1-body-hint');
                        // v0.0.91: my_diary verhält sich im Formular wie diary (Feld aus)
                        var texts = {
                            post:     { req: true,  dis: false, hint: '' },
                            wiki:     { req: false, dis: false, hint: <?php echo json_encode(__('May stay empty — a blank wiki page to fill later.', 'cluster-one-bismarck')); ?> },
                            // v0.9.30: Komma nicht entfernen — ohne es bricht die ganze IIFE (s. CHANGELOG).
                            wilde:    { req: true,  dis: false, hint: '' },
                            diary:    { req: false, dis: true,  hint: <?php echo json_encode(__('Diaries start empty — this field is ignored. Choose below who keeps it.', 'cluster-one-bismarck')); ?> },
                        };
                        function apply() {
                            var role = (document.querySelector('.c1-role:checked') || {}).value || 'post';
                            var t = texts[role] || texts.post;
                            if (body) { body.required = t.req; body.disabled = t.dis; }
                            if (req)  { req.style.visibility = t.req ? 'visible' : 'hidden'; }
                            if (hint) { hint.textContent = t.hint; }
                            if (role === 'diary') {
                                var named = document.querySelector('.c1-gv-radio[value="named"]');
                                if (named && !named.checked) { named.checked = true; named.dispatchEvent(new Event('change')); }
                            }
                        }
                        document.querySelectorAll('.c1-role').forEach(function (r) { r.addEventListener('change', apply); });
                        apply();
                    })();
                    </script>
                </td>
            </tr>
        </table>
<?php /* v0.9.8: DER ALLGEMEINE SICHTBARKEITS-WAEHLER IST WEG. Bei allen
   anderen Arten wurde die Antwort ohnehin ueberschrieben — EINE FRAGE, DEREN
   ANTWORT MAN WEGWIRFT, IST EINE FALLE.

   Was bleibt, ist keine Sichtbarkeitsfrage, sondern eine Eigentumsfrage: WER
   FUEHRT DIESES TAGEBUCH.

   v0.9.50: Sie heisst jetzt auch so und traegt ein eigenes Feld
   ('diary_owner') statt sich als visible_to[] zu verkleiden. Bis 0.9.49 stand
   hier die Namensliste, weil der Handler unveraendert bleiben sollte — ein
   Feld, das etwas anderes heisst, als es beantwortet, ist genau die Sorte
   Halbwahrheit, die wir seit zwei Tagen einsammeln. Der Handler uebersetzt
   jetzt die Antwort in die Namensliste, statt die Frage zu verstellen. */ ?>
        <div id="c1-diary-owner" style="display:none;margin-top:1em;">
            <h3 style="margin:0 0 .3em;"><?php esc_html_e('Who keeps this diary?', 'cluster-one-bismarck'); ?></h3>
            <p class="description" style="max-width:600px;"><?php esc_html_e('The diary belongs to its keeper: only they write in it, and nobody else reads it — the SysOp only with a witness.', 'cluster-one-bismarck'); ?></p>
            <label style="display:block;margin:.2em 0;"><input type="radio" name="diary_owner" value="me" checked> <?php echo esc_html(cluster_one_human_speaker_label()); ?></label>
<?php /* v0.9.50: Die KIs stehen NEBEN dem Menschen, nicht in einem zweiten
   Knopf. Anbieten kann sie nur der SysOp — ein KI-Tagebuch ruft eine Kennung
   auf, und das bleibt Sache des Zahlenden (0.9.13). Der Handler erzwingt
   dasselbe: Wer es trotzdem postet, bekommt sein eigenes Tagebuch. */
if (cluster_one_is_sysop()) :
    foreach (cluster_one_get_ai_identities() as $c1_dcid => $c1_duid) :
        if ((int) $c1_duid <= 0) { continue; } ?>
                <label style="display:block;margin:.2em 0;"><input type="radio" name="diary_owner" value="<?php echo esc_attr((string) $c1_dcid); ?>"> <?php echo esc_html(cluster_one_room_ai_label((string) $c1_dcid, array())); ?></label>
<?php endforeach; endif; ?>
        </div>
        <script>
        (function () {
            /* Ohne JavaScript bleibt der Block verborgen — dann legt man das
               KI-Tagebuch wie bisher an und weist die KI in der Konfiguration
               zu. Kein Weg geht dadurch verloren. */
            var box = document.getElementById('c1-diary-owner');
            function zeig() {
                var r = document.querySelector('.c1-role:checked');
                box.style.display = (r && r.value === 'diary') ? 'block' : 'none';
            }
            document.querySelectorAll('.c1-role').forEach(function (r) { r.addEventListener('change', zeig); });
            zeig();
        })();
        </script>
        <?php submit_button(__('Start thread', 'cluster-one-bismarck'), 'primary', 'submit', false); ?>
    </form>
<?php
}


add_action('admin_post_cluster_one_forum_thread_create', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_forum_thread_create');
    /* v0.9.49: EIN FORMULAR, EIN PROJEKT. Christian: "Nehmen wir als
       Projektname Haekeln, erhalten wir Haekeln-Chat und Haekeln-Thread."

       Der Titel kommt nicht mehr aus dem Formular, sondern wird ABGELEITET —
       und der Chat gleich mit. Warum das hier steht und nicht in einem eigenen
       Handler: Die Themen-Haelfte ist die komplizierte (sechs Rollen,
       Tagebuch-KI, Sichtbarkeit); die Chat-Haelfte sind fuenfzehn Zeilen. Ein
       zweiter Handler haette die komplizierte Haelfte abgeschrieben — der
       Fehler, den wir beim Loeschen schon einmal vermieden haben (P1-12). */
    $c1_projekt = isset($_POST['projekt']) ? trim(sanitize_text_field(wp_unslash($_POST['projekt']))) : '';
    $c1_namen   = cluster_one_projekt_namen($c1_projekt);
    $title   = $c1_projekt !== ''
        ? $c1_namen['thread']
        : (isset($_POST['title']) ? trim(sanitize_text_field(wp_unslash($_POST['title']))) : '');
    $subject = isset($_POST['subject']) ? trim(sanitize_text_field(wp_unslash($_POST['subject']))) : '';
    $body    = isset($_POST['body'])    ? cluster_one_sanitize_message((string) wp_unslash($_POST['body'])) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (cluster_one_sanitize_message), den der Pruefer nicht kennt
    $area    = isset($_POST['area'])    ? trim(sanitize_text_field(wp_unslash($_POST['area'])))    : '';
    $back    = admin_url('admin.php?page=cluster_one_bismarck&tab=projekte');
    /* Die Namenspruefung steht VOR allem anderen — ein halb angelegtes Projekt
       waere schlimmer als eine abgewiesene Eingabe. */
    if ($c1_projekt !== '' && cluster_one_projekt_name_vergeben($c1_projekt)) {
        wp_safe_redirect($back . '&forum_err=dup');
        exit;
    }

    // v0.0.70: Thread-Typ ZUERST lesen — die Validierung hängt an ihm.
    // 'wiki' darf leer starten (weiße Seite, Entscheidung SysOp 15.07.);
    // 'preamble' braucht den Text (er IST die Präambel); 'diary' ignoriert
    // den Text (Tagebücher starten leer, die KI schreibt selbst).
    $role = isset($_POST['first_post_role']) ? sanitize_key(wp_unslash($_POST['first_post_role'])) : 'post';
    /* v0.9.50: VIER FRAGEN STATT SECHS — und die zwei, die gegangen sind, sind
       aus zwei verschiedenen Gruenden gegangen.

       (1) PRAEAMBEL war nie eine Art, sondern ein FELD. Die Rolle tat genau
       zwei Dinge: den Text ins Feld 'preamble' schreiben und den Erstbeitrag
       auslassen. Setzen laesst sich eine Praeambel auf JEDEM Thema, dafuer gibt
       es einen eigenen Handler — und in Christians echten Daten steht der
       Beweis: "Bismarck-01" und "News-Flash-T" tragen beide eine Praeambel und
       sind beide als NORMALE Themen angelegt. Eine Auswahl, die als ART
       anbietet, was in Wahrheit eine EIGENSCHAFT ist, ist eine falsche
       Alternative — dieselbe Sorte wie die Spalte "Art" und die drei Abzeichen
       aus 0.9.41. Sie steht jetzt als optionales Feld im Formular.

       (2) DIE ZWEI TAGEBUECHER unterschieden sich in genau EINER Frage: wer
       fuehrt es. Der Rest war identisch — Terminal, startet leer, nur der
       Eigentuemer schreibt. Christian hat den Satz selbst in die
       Bereichsbeschreibung geschrieben: "Tagebuecher — fuer die Menschen hier
       und fuer die KIs gleichermassen." Zwei getrennte Knoepfe widersprechen
       ihm. Jetzt: EIN Knopf und die Frage "Wer fuehrt es?".

       WAS HIER NICHT PASSIERT: Die beiden Speicherformen bleiben verschieden —
       ein Menschen-Tagebuch traegt 'human_diary', ein KI-Tagebuch die Kennung
       in der Namensliste. Das ist keine Doppelung, das sind zwei verschiedene
       Wahrheiten. Zusammengelegt wird die FRAGE an den Menschen, nicht der
       Speicher. Deshalb wird hier uebersetzt statt umgebaut. */
    if ($role === 'diary') {
        $c1_wer = isset($_POST['diary_owner']) ? sanitize_text_field(wp_unslash($_POST['diary_owner'])) : 'me';
        $c1_kis = cluster_one_get_ai_identities();
        if ($c1_wer !== 'me' && isset($c1_kis[$c1_wer]) && (int) $c1_kis[$c1_wer] > 0 && cluster_one_is_sysop()) {
            /* KI-Tagebuch: Die Namensliste SAGT, welche KI es fuehrt (0.9.8).
               Sie wird hier gesetzt und nicht aus dem Formular uebernommen —
               eine Liste, die der Server selbst kennt, laesst er sich nicht
               schicken. */
            $_POST['visible_to'] = array('wp_' . (int) $c1_kis[$c1_wer]);
        } else {
            /* Alles andere ist das eigene Tagebuch — auch der Versuch eines
               Beschirmten, eine KI einzutragen. Der Server erzwingt, was das
               Formular zeigt, ohne die Eingabe abzuweisen: Wer "Tagebuch"
               waehlt, bekommt eins. */
            $role = 'my_diary';
        }
    }
    if (!in_array($role, array('post', 'wiki', 'wilde', 'diary', 'my_diary'), true)) {
        $role = 'post';
    }
    if ($role === 'diary' && !cluster_one_is_sysop()) {
        wp_safe_redirect($back . '&forum_err=invalid'); // SysOp-exklusiv — der Handler erzwingt, was das Formular zeigt
        exit;
    }
    /* v0.9.50: Die Praeambel als Feld. Sie darf zu jeder Art dazu — und wo sie
       steht, ist der Erstbeitrag keine Pflicht mehr: Genau das war der alte
       Praeambel-Typ, ein Thema, das nur mit seiner Orientierung beginnt. */
    $c1_praeambel = isset($_POST['preamble']) ? cluster_one_sanitize_message((string) wp_unslash($_POST['preamble'])) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (cluster_one_sanitize_message), den der Pruefer nicht kennt
    if ($c1_praeambel !== '' && !cluster_one_is_sysop()) {
        $c1_praeambel = ''; // Praeambeln sind SysOp-Sache (P0-7), unveraendert
    }
    $body_required = ($role === 'post') && $c1_praeambel === '';
    // v0.0.66: „Ohne Bereich" ist ein gültiger Zustand — ein GESETZTER
    // Bereich muss weiterhin aus der Struktur-Liste stammen (kein Freitext
    // durch manipulierte Formulare).
    if ($title === '' || ($body_required && $body === '') || ($area !== '' && !in_array($area, cluster_one_forum_areas(), true))) {
        wp_safe_redirect($back . '&forum_err=invalid');
        exit;
    }
    if ($subject === '') {
        $subject = $title; // phpBB-Stil: Default = Thema-Titel, aber ein eigenes, unabhängiges Feld
    }

    $gv = isset($_POST['group_visibility']) && $_POST['group_visibility'] === 'named' ? 'named' : 'all';
    $vt = array();
    // v0.0.70: Beim Tagebuch-Typ wird 'named' serverseitig ERZWUNGEN — die
    // Liste muss deshalb unabhängig vom geposteten Radio eingesammelt
    // werden (das Formular-JS schaltet das Radio zwar um, aber der Handler
    // verlässt sich nie auf clientseitiges Verhalten).
    if (($gv === 'named' || $role === 'diary') && isset($_POST['visible_to']) && is_array($_POST['visible_to'])) {
        $vt = array_map('sanitize_text_field', wp_unslash($_POST['visible_to']));
    }
    if ($role === 'my_diary') {
        // v0.0.91: MENSCHEN-TAGEBUCH — die letzte Verallgemeinerung des
        // 0.0.72-Modells. Besitzer = der Anlegende (der EINE Ort, der
        // nicht SysOp-exklusiv sein darf, sonst wäre es kein persönlicher
        // Raum). Kreis = nur der Besitzer; Halbauge ab Werk (0.0.79-
        // Leiter); der SysOp liest nur bezeugt — der 0.0.80-Blick-Zeuge
        // landet im Tagebuch SELBST: genau dort, wo der Besitzer liest
        // („der Zeuge landet, wo der Besitzer liest", besitzerneutral:
        // KI -> Chat, Mensch -> Tagebuch). Keine Zwangs-Chat-Kopplung —
        // der Chat ist eine Prothese für KIs, keine Asymmetrie der Würde.
        $gv = 'named';
        $vt = array('wp_' . get_current_user_id());
    } elseif ($role === 'diary') {
        // v0.0.70: KI-Tagebuch in EINEM Schritt (vorher unmöglich — der
        // Autoren-Zwang unten setzte den Menschen zwangsweise auf die
        // Liste, und cluster_one_thread_is_ai_diary verlangt NULL
        // Menschen). Sichtbarkeit wird auf 'named' erzwungen, die Liste
        // auf reine KI-Kennungen gefiltert; mindestens eine KI ist
        // Pflicht, sonst wäre es ein sichtbarkeitsloses Nichts.
        $gv = 'named';
        $ai_ids = array();
        foreach (cluster_one_ai_identity_user_ids() as $uid) {
            $ai_ids[] = 'wp_' . (int) $uid;
        }
        $vt = array_values(array_intersect($vt, $ai_ids));
        if (empty($vt)) {
            wp_safe_redirect($back . '&forum_err=diary_needs_ai');
            exit;
        }
    } else {
        // Verfasser gehört immer auf die eigene Sichtbarkeits-Liste, sonst
        // würde er sein eigenes Thema nach dem Anlegen selbst nicht mehr
        // sehen. (Beim Tagebuch oben AUSDRÜCKLICH nicht — das ist der Sinn.)
        $my_id = 'wp_' . get_current_user_id();
        if ($gv === 'named' && !in_array($my_id, $vt, true)) {
            $vt[] = $my_id;
        }
    }

    $tid       = cluster_one_forum_thread_id();
    $author    = cluster_one_human_speaker_label();
    // v0.0.69: Die Rolle des ersten Textes entscheidet — nicht mehr der
    // Bereich (Typ-Lesung + Gates seit v0.0.70 oben am Handler-Anfang).
    /* v0.5.3: Wilde IST ein Wiki — technisch dasselbe Feld, damit das
       Fassungsarchiv aus 0.5.1 unveraendert traegt. Der Unterschied steckt
       allein im Eigentuemer. */
    $is_wilde  = ($role === 'wilde');
    $is_wiki   = ($role === 'wiki') || $is_wilde;
    /* Eine pro Entitaet: bei mehreren waere unklar, welche gilt. */
    if ($is_wilde && cluster_one_wilde_thread_of(cluster_one_actor_id_human()) !== '') {
        /* 18.10.2026 (Neo): `page=cluster_one_forum` gab es nie — die Projektliste
           heisst cluster_one_bismarck&tab=projekte. Der Leser fuer wilde_exists
           steht dort (bismarck-seiten.php, render_bismarck_page). */
        wp_safe_redirect(add_query_arg('wilde_exists', '1', admin_url('admin.php?page=cluster_one_bismarck&tab=projekte')));
        exit;
    }
    // Wiki (v0.0.53, seit v0.0.69 Thread-Eigenschaft): der EINE Post trägt
    // von Anfang an Collective als Autor, nicht den tatsächlichen Verfasser.
    $post_author = $author;
    /* v0.5.3: Bei Wilde bleibt der echte Name stehen. "Collective" waere hier
       genau falsch herum: Der Wiki-Text gehoert keinem, die Selbstdarstellung
       gehoert ausdruecklich jemandem. */
    if ($is_wiki && !$is_wilde) {
        $cu = get_userdata(cluster_one_collective_user_id());
        if ($cu) {
            $post_author = $cu->display_name;
        }
    }

    $threads = cluster_one_get_forum_threads();
    $threads[$tid] = array(
        'id'               => $tid,
        'title'            => $title,
        'author'           => $author,
        'creator_actor'    => cluster_one_actor_id_human(), // v0.0.62: Actor-ID löst creator_uid ab (Kahlschlag: Pflichtfeld ab Tag eins)
        'created'          => time(),
        'group_visibility' => $gv,
        'visible_to'       => $vt,
        'area'             => $area,
        'wiki'             => $is_wiki, // v0.0.69: bei der Anlage eingefroren
        // v0.5.3: leer = gewoehnliches Wiki; gesetzt = Selbstdarstellung, und
        // nur dieser Actor darf sie aendern (auch der SysOp nicht).
        'wilde_owner'      => $is_wilde ? cluster_one_actor_id_human() : '',
        'human_diary'      => ($role === 'my_diary'), // v0.0.91
    );
    /* v0.9.3: Halbauge ab Werk fuer BEIDE Tagebuch-Arten. Bis hierher bekam es
       nur das Mensch-Tagebuch; ein KI-Tagebuch war unsichtbar.

       Christians Begruendung ist Gruppendynamik, kein Zwang: Wer sieht, dass
       andere Tagebuch fuehren, faengt vielleicht selbst eines an — Reflexion
       schadet nie. Sichtbar ist nur die EXISTENZ, nie der Inhalt.

       Und es ist eine VORGABE, keine Regel: Wem das zu viel ist, stellt die
       Leiter auf unsichtbar. Wir arbeiten nicht mit Zwang; ein wenig Nudging
       geht immer. */
    if ($role === 'my_diary' || $role === 'diary') {
        $threads[$tid]['outside_view'] = 'half';
    }
    if ($c1_praeambel !== '') { // v0.9.50: Feld statt Rolle
        $threads[$tid]['preamble'] = $c1_praeambel;
    }
    cluster_one_save_forum_threads($threads);
    /* v0.9.8: Paarung aus der Anlege-Maske — das Gegenstueck zu 0.9.7.

       Ueber cluster_one_set_room_thread_link(), NICHT direkt ins Feld: sonst
       entfielen das Tuerereignis und die geerbte Gaesteliste beim Tagebuch.
       Und der Bereich folgt dem Raum — ein Projekt liegt an einem Ort. */
    $c1_pr = isset($_POST['pair_room']) ? sanitize_text_field(wp_unslash($_POST['pair_room'])) : '';
    if ($c1_pr !== '' && isset(cluster_one_unpaired_rooms()[$c1_pr])) {
        $c1_ra = (string) (cluster_one_get_rooms()[$c1_pr]['area'] ?? '');
        /* v0.9.35: auch hier — sonst zoege ein Tagebuch ueber den Umweg
           "Bereich folgt dem Raum" doch noch um. Zwei Wege in denselben
           Zustand brauchen dieselbe Sperre. */
        if (!cluster_one_thread_may_change_area($threads[$tid], $c1_ra)) {
            $c1_ra = (string) ($threads[$tid]['area'] ?? '');
        }
        if ($c1_ra !== (string) ($threads[$tid]['area'] ?? '')) {
            cluster_one_area_move_witness('thread', (string) $tid, (string) ($threads[$tid]['area'] ?? ''), $c1_ra);
            $threads[$tid]['area'] = $c1_ra;
            cluster_one_save_forum_threads($threads);
        }
        cluster_one_set_room_thread_link($c1_pr, (string) $tid);
    }
    /* v0.9.9: Ein Tagebuch bekommt seine Konsole sofort — Christians Ein-Klick.

       NACH der Paarung, und das ist die richtige Reihenfolge: Wer beim Anlegen
       ausdruecklich einen vorhandenen Chat waehlt, will genau den. Die Funktion
       sieht dann, dass das Thema schon verknuepft ist, und tut nichts. Umgekehrt
       waere ein verwaister Terminal entstanden. */
    if (in_array($role, array('my_diary', 'diary'), true)) {
        cluster_one_create_terminal_for_diary((string) $tid);
    }

    /* v0.9.50: Der Zweig haengt jetzt am FELD statt an einer Rolle — und greift
       nur, wo das Thema wirklich ohne Beitrag beginnt. Wer Praeambel UND
       Erstbeitrag schreibt, bekommt den Beitrag; der Praeambel-Zeuge kommt dann
       aus dem gemeinsamen Zweig weiter unten. */
    if ($c1_praeambel !== '' && $body === '' && !in_array($role, array('my_diary', 'diary'), true)) {
        // Symmetrisch zum Präambel-Handler (P0-7): System-Zeuge im Thread
        // + Türereignis mit vollem Hash. Der Thread startet ohne Beiträge —
        // nur der Zeuge steht drin (seit 0.0.68 meldet die verknüpfte KI
        // dann ehrlich den Umfang).
        $ptext = (string) $threads[$tid]['preamble'];
        cluster_one_save_forum_posts($tid, array(cluster_one_system_message(
            /* translators: %s: Fingerabdruck der Praeambel. */
            sprintf(__('SysOp set the preamble (fingerprint %s).', 'cluster-one-bismarck'), cluster_one_preamble_hash($ptext))
        )));
        cluster_one_event_emit(array(
            'type'           => 'preamble_set',
            'container_type' => 'thread', 'container_id' => $tid,
            'actor_id'       => cluster_one_actor_id_human(),
            'visibility'     => cluster_one_event_visibility($threads[$tid]),
            'content_hash'   => hash('sha256', $ptext),
        ), true);
    } elseif ($role === 'my_diary') {
        // v0.0.91: Menschen-Tagebücher starten ebenfalls LEER — der
        // Besitzer schreibt selbst, wann er will.
        cluster_one_save_forum_posts($tid, array());
    } elseif ($role === 'diary') {
        // v0.0.70: Tagebücher starten LEER — die KI schreibt selbst.
        // Kein Erstbeitrag, kein Zeuge (die Anlage selbst ist das flüchtige
        // thread_created unten; das Lese-Umschalten hat seinen eigenen,
        // bezeugten Weg im Persönlichen Bereich).
        cluster_one_save_forum_posts($tid, array());
        // v0.0.71: Nach der Tagebuch-Anlage NICHT zurück in die Foren-Liste —
        // dort ist das Tagebuch (verfassungsgemäß: null Menschen im Kreis)
        // für jeden Menschen inexistent, auch für den SysOp. „Thread
        // created." vor einer Liste ohne den Thread war zum Verrücktwerden
        // (Fund SysOp 15.07.). Ziel ist sein Zuhause: der Persönliche
        // Bereich mit Lese-Umschalter und Löschknopf.
        // v0.0.72: zurück in die Foren-Liste — dort ist das Tagebuch jetzt
        // als Halbauge sichtbar (0.0.71-Sonderweg in den Persönlichen
        // Bereich damit hinfällig).
    } else {
        // v0.0.70: Wiki darf als weiße Seite starten (leerer body) — der
        // EINE Collective-Post existiert trotzdem von Anfang an, damit
        // Bearbeiten sofort möglich ist.
        cluster_one_save_forum_posts($tid, array(array(
            'id'       => cluster_one_msg_id(),
            'author'   => $post_author,
            'actor_id' => $is_wiki ? 'collective' : cluster_one_actor_id_human(), // v0.0.62: Wiki = alle und niemand
            'subject'  => $subject,
            'body'     => $body,
            'ts'       => time(),
        )));
    }
    cluster_one_event_emit(array( // flüchtig — kein Türereignis
        'type' => 'thread_created', 'container_type' => 'thread', 'container_id' => $tid,
        'actor_id' => cluster_one_actor_id_human(), 'content_hash' => hash('sha256', $title),
    ), false);

    /* v0.9.49: UND JETZT DIE ANDERE HAELFTE. Der Chat entsteht ueber
       cluster_one_room_create_do() — die eine Stelle, an der ein Raum
       entsteht —, und die Kante wird ueber cluster_one_set_room_thread_link()
       gesetzt, nicht direkt ins Feld: Sonst entfielen das Tuerereignis und die
       geerbte Gaesteliste beim Tagebuch (die Lehre aus 0.9.7).

       Er erbt Bereich und Sichtbarkeit vom Thema. Das ist keine Bequemlichkeit,
       sondern der Befund vom 20.08.: Vier von acht bestehenden Paaren waren
       sich uneinig darueber, wer mitlesen darf, weil beide Haelften getrennt
       angelegt wurden. Ein Paar, das gemeinsam entsteht, faengt einig an. */
    /* ...aber nur, wenn das Thema nicht laengst eine hat. Ein TAGEBUCH bekommt
       seine Konsole ein paar Zeilen weiter oben von
       cluster_one_create_terminal_for_diary() — Christians Ein-Klick aus
       0.9.9. Ohne diese Bedingung haette der Projekt-Weg gleich danach einen
       ZWEITEN Raum angelegt und die frische Konsole verwaisen lassen.

       BENANNTE GRENZE, die daraus folgt: Ein Tagebuch-Projekt bekommt keinen
       Chat namens "<Name>-Chat", sondern die Konsole heisst "<Name>-Thread —
       Terminal". Das Namensmuster gilt also fuer die fuenf gewoehnlichen
       Rollen, nicht fuer die zwei Tagebuecher. Das ist kein Versehen: Eine
       Konsole ist kein Chat, sie ist die Tuer neben einem Tagebuch, und sie
       hat ihren eigenen Weg aus demselben Grund wie das Vorzimmer. */
    $c1_hat_schon = trim((string) (cluster_one_get_forum_threads()[$tid]['linked_room'] ?? '')) !== '';
    if ($c1_projekt !== '' && !$c1_hat_schon) {
        $c1_rid = cluster_one_room_create_do($c1_namen['chat'], array(
            'area'             => $area,
            'group_visibility' => $gv,
            'visible_to'       => $vt,
        ));
        if ($c1_rid !== '') {
            cluster_one_set_room_thread_link($c1_rid, $tid);
        }
    }
    wp_safe_redirect($back . '&thread_created=1');
    exit;
});

/** Thread-Konfiguration speichern (v0.0.66): Bereich + Sichtbarkeit,
   SysOp-exklusiv (serverseitiger Zwilling des Schraubenschlüssels).
   Sichtbarkeits-Änderungen sind Türereignisse — bezeugt über den
   zentralen Helfer. Es wird NIEMAND automatisch auf die Liste gesetzt
   (anders als bei der Anlage): der SysOp-Akt ist bezeugt, und Container
   mit null Menschen (KI-Tagebücher) müssen möglich bleiben. */
add_action('admin_post_cluster_one_forum_thread_config_save', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_thread_config');
    /* v0.3.0: Fremd-Labels sind schreibgeschuetzt. Sonst stuende hier
       irgendwann eine Fassung, die auf dem Ursprungsknoten nie existiert
       hat — die Souveraenitaetsregel von der anderen Seite. Die Sperre sitzt
       im HANDLER, nicht nur in der Oberflaeche: die Seite ist ueber ihre URL
       erreichbar. */
    $c1_tid_fl = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $c1_thr_fl = cluster_one_get_forum_threads();
    if ($c1_tid_fl !== '' && isset($c1_thr_fl[$c1_tid_fl]) && cluster_one_thread_is_foreign($c1_thr_fl[$c1_tid_fl])) {
        wp_die('-1', 403);
    }
    $tid     = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $threads = cluster_one_get_forum_threads();
    if ($tid !== '' && isset($threads[$tid])) {
        /* v0.6.6: Themen liessen sich nicht umbenennen, Chats schon — Christians
           Befund. Nur das Etikett aendert sich: Die Kennung bleibt, Beitraege,
           Kanten und Bereich bleiben. Ein leerer Titel wird ABGEWIESEN statt
           uebernommen; ein namenloses Thema waere in jeder Liste ein Loch. */
        $c1_titel = isset($_POST['title']) ? trim(sanitize_text_field(wp_unslash($_POST['title']))) : '';
        if ($c1_titel !== '') {
            $threads[$tid]['title'] = $c1_titel;
        }
        // Bereich: '' (Ohne Bereich) oder ein Eintrag der Struktur-Liste.
        // Ein gespeicherter Alt-Bereich, der nicht mehr in der Liste steht,
        // darf UNVERÄNDERT bleiben (das Auswahlfeld bietet ihn markiert an) —
        // nur NEUE Fremdwerte werden abgewiesen.
        $area = isset($_POST['area']) ? trim(sanitize_text_field(wp_unslash($_POST['area']))) : '';
        $old_area = (string) ($threads[$tid]['area'] ?? '');
        /* v0.9.35: Ein Tagebuch bleibt, wo es ist — weder Mensch noch KI
           reisen mit ihrem Tagebuch (Christian, 19.08.2026). Bis hierher
           liess es sich hier herausschieben, und die Schreibberechtigung
           waere beim Umzug in einen sichtbaren Bereich still ans Kollektiv
           gefallen. Die Sperre sitzt im HANDLER, nicht in der Auswahlliste. */
        if (!cluster_one_thread_may_change_area($threads[$tid], (string) $area)) {
            $area = $old_area;
        }
        if ($area === '' || in_array($area, cluster_one_forum_areas(), true) || $area === $old_area) {
            /* v0.8.5: Bereichswechsel bezeugen, wenn er die Sichtbarkeit
               aendert (SysOp-Bereich rein/raus). */
            cluster_one_area_move_witness('thread', (string) $tid, (string) ($threads[$tid]['area'] ?? ''), (string) $area);
            $threads[$tid]['area'] = $area;
        }

        $old_gv = (string) ($threads[$tid]['group_visibility'] ?? 'all');
        $old_vt = array_map('strval', (array) ($threads[$tid]['visible_to'] ?? array()));
        /* 18.10.2026 (Neo): Kommt KEIN Sichtbarkeitsfeld, bleibt die
           Sichtbarkeit stehen. Das Formular zeichnet den Waehler nicht, wenn
           ein Chat verknuepft ist und die Gaesteliste dort wohnt — bis heute
           stand er trotzdem ein zweites Mal unbedingt darunter, und ohne ihn
           haette diese Zeile jedes verknuepfte Thema still auf „alle"
           gestellt. */
        if (isset($_POST['group_visibility'])) {
            $gv = $_POST['group_visibility'] === 'named' ? 'named' : 'all';
            $vt = array();
            if ($gv === 'named' && isset($_POST['visible_to']) && is_array($_POST['visible_to'])) {
                $vt = array_values(array_unique(array_map('sanitize_text_field', wp_unslash($_POST['visible_to']))));
            }
        } else {
            $gv = $old_gv;
            $vt = $old_vt;
        }
        $threads[$tid]['group_visibility'] = $gv;
        $threads[$tid]['visible_to']       = $vt;
        cluster_one_save_forum_threads($threads);
        /* v0.9.7: Die andere Haelfte zieht mit — ein Projekt liegt an einem
           Ort. Erst hier, damit sie den neuen Bereich sieht. */
        cluster_one_area_follow_partner('thread', (string) $tid, $area);

        cluster_one_emit_visibility_change('thread', $tid, $old_gv, $old_vt, $gv, $vt);
    }
    if ($tid !== '' && isset($threads[$tid])) {

    // v0.0.79: Leiter speichern — beide Wechsel sind Türereignisse
    // (Artikel 2: „ein Raum wechselt seinen Augenstand"; eine Tiefen-
    // Änderung ist eine Fähigkeits-Umverteilung). No-op erzeugt kein Zeugnis.
    /* v0.9.31: Hier stand das Speichern der "Leiter" samt eye_changed-Zeugnis
       — nie erreicht, weil es kein Formularfeld 'outside_view' gibt. Derselbe
       tote Pfad stand auch in bismarck.php. Die Funktion
       cluster_one_container_outside_view() bleibt: Sie ist der bezeugte Riegel
       ("kein Container ist mehr unsichtbar"), nur ihr Schreibweg war Fiktion. */
    /* v0.9.26: Die Kreis-Tiefe ist entfallen — siehe
       cluster_one_container_can_act(). Wer im Kreis ist, schreibt. */
        cluster_one_save_forum_threads($threads);
    }

    // v0.0.75 (Punkt 6): Raum↔Thema-Kante auch von der Thread-Seite —
    // NACH dem regulären Speichern (Sync-Helfer-Muster der Raum-Seite,
    // v0.0.57), über DENSELBEN bidirektionalen Helfer. Beide Tabellen
    // lesen dieselbe Kante, damit betrifft jede Änderung beide Tabs.
    if ($tid !== '' && isset(cluster_one_get_forum_threads()[$tid]) && isset($_POST['linked_room'])) {
        $c1_new_room = sanitize_text_field(wp_unslash($_POST['linked_room']));
        $c1_rooms    = cluster_one_get_rooms();
        $c1_cur_room = (string) (cluster_one_get_forum_threads()[$tid]['linked_room'] ?? '');
        if ($c1_new_room === '' && $c1_cur_room !== '') {
            cluster_one_set_room_thread_link($c1_cur_room, ''); // lösen
        } elseif ($c1_new_room !== '' && isset($c1_rooms[$c1_new_room]) && $c1_new_room !== $c1_cur_room) {
            cluster_one_set_room_thread_link($c1_new_room, $tid); // setzen/umhängen (Helfer regelt das Wegnehmen)
        }
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid) . '&view=config&saved=1'));
    exit;
});

/** Präambel setzen/ändern (v0.0.60): SysOp-exklusiv. Die Präambel ist kein
   Post, sondern ein eigenes Feld am Thread — unveränderlich für alle außer
   SysOp, damit sie eine verlässliche Orientierung bleibt (auch die KI darf
   sie im verknüpften Chat KRITISIEREN, aber nicht ändern). Leerer Text
   löscht die Präambel wieder. */
add_action('admin_post_cluster_one_thread_preamble', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_thread_preamble');
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

    $tid  = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $text = isset($_POST['preamble']) ? cluster_one_sanitize_message((string) wp_unslash($_POST['preamble'])) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (cluster_one_sanitize_message), den der Pruefer nicht kennt
    $base = admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid));

    $threads = cluster_one_get_forum_threads();
    if ($tid === '' || !isset($threads[$tid]) || !cluster_one_group_visibility_can_view($threads[$tid])) { // v0.0.61: Sichtbarkeit wie beim Lösch-Nachbarn — wirkt wie „nicht gefunden"
        wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck&tab=forum'));
        exit;
    }

    $old = trim((string) ($threads[$tid]['preamble'] ?? ''));
    $threads[$tid]['preamble'] = $text;
    cluster_one_save_forum_threads($threads);

    // v0.0.61 (P0-7): System bezeugt — symmetrisch wie der Tagebuch-Zeuge,
    // nie still. Nur bei ECHTER Änderung (kein Rauschen bei No-op-Saves).
    // Der Fingerabdruck macht das Setzen-antworten-lassen-zurücksetzen-
    // Szenario rückwirkend belegbar: die KI-Antwort trägt denselben Hash.
    if ($old !== $text) {
        $posts   = cluster_one_get_forum_posts($tid);
        $posts[] = cluster_one_system_message($text === ''
            ? __('SysOp removed the preamble.', 'cluster-one-bismarck')
            /* translators: %s: Fingerabdruck der Praeambel. */
            : sprintf(__('SysOp set the preamble (fingerprint %s).', 'cluster-one-bismarck'), cluster_one_preamble_hash($text)));
        cluster_one_save_forum_posts($tid, $posts);
        cluster_one_event_emit(array( // v0.0.62: TÜRereignis — erster Kunde des Eventvertrags
            'type'           => $text === '' ? 'preamble_removed' : 'preamble_set',
            'container_type' => 'thread', 'container_id' => $tid,
            'actor_id'       => cluster_one_actor_id_human(),
            'visibility'     => cluster_one_event_visibility($threads[$tid]),
            'content_hash'   => $text === '' ? '' : hash('sha256', $text), // voller Hash im Log, 8er-Fingerabdruck bleibt Anzeige
        ), true);
    }

    wp_safe_redirect(add_query_arg('preamble_saved', '1', $base));
    exit;
});

/** v0.9.45: DAS LOESCHEN EINES THEMAS, als Funktion statt als Handler-Rumpf.

   Grund: Das X in der Projekt-Liste muss BEIDE Haelften nehmen. Es haette
   entweder den Rumpf hier abgeschrieben — dann gaebe es zwei Schreibwege auf
   dieselbe Wahrheit (P1-12) und einer davon wuerde beim naechsten Umbau
   vergessen — oder es ruft dieselbe Stelle. Es ruft dieselbe Stelle.

   Was hier NICHT steht, ist die Rechtefrage. Die gehoert dem Aufrufer: Der
   alte Handler laesst auch Nicht-SysOps an ihr Eigenes, das X ist
   SysOp-exklusiv. Zwei verschiedene Tueren zu einem Raum — die Tueren haben
   Schloesser, der Raum hat keins. */
function cluster_one_thread_delete_do(string $tid): bool {
    $threads = cluster_one_get_forum_threads();
    if ($tid === '' || !isset($threads[$tid])) {
        return false;
    }
    // v0.0.63 (P1-10): erst die Raum-Kante lösen — über die zentrale
    // Link-Funktion, damit das room_thread_unlinked-Zeugnis entsteht
    // und kein Raum auf ein gelöschtes Thema zeigt.
    $linked_room = (string) ($threads[$tid]['linked_room'] ?? '');
    if ($linked_room !== '') {
        cluster_one_set_room_thread_link($linked_room, '');
        $threads = cluster_one_get_forum_threads(); // frische Kopie, s. room_delete
    }
    cluster_one_event_emit(array( // v0.0.62: TÜRereignis — eine Löschung ist die größte Strukturänderung von allen; das Zeugnis überlebt den Container
        'type' => 'thread_deleted', 'container_type' => 'thread', 'container_id' => $tid,
        'actor_id' => cluster_one_actor_id_human(),
        'visibility' => cluster_one_event_visibility($threads[$tid]),
        'content_hash' => hash('sha256', (string) ($threads[$tid]['title'] ?? '')),
    ), true);
    /* v0.3.5: Ein Label loeschen heisst NUR: es verschwindet aus dem
       oeffentlichen Raum. Das Projekt im Forum bleibt — deshalb wird hier
       nur der Rueckverweis geloest, damit es danach wieder ein Label
       bekommen kann. Ohne das zeigte das Projekt auf ein totes Label. */
    $c1_for = (string) ($threads[$tid]['label_for'] ?? '');
    if ($c1_for !== '' && isset($threads[$c1_for])) {
        unset($threads[$c1_for]['linked_label']);
    }
    /* v0.9.45: Und die Gegenrichtung, die bis hierher fehlte: Traegt das
       geloeschte Thema SELBST ein Label, zeigte dieses Label danach auf ein
       totes Projekt (label_for ins Leere). Gefunden beim Herausloesen dieser
       Funktion — die Kante war nur in einer Richtung aufgeraeumt. */
    $c1_lbl = (string) ($threads[$tid]['linked_label'] ?? '');
    if ($c1_lbl !== '' && isset($threads[$c1_lbl])) {
        unset($threads[$c1_lbl]['label_for']);
    }
    unset($threads[$tid]);
    cluster_one_save_forum_threads($threads);
    delete_option('cluster_one_forum_posts_' . $tid);
    return true;
}

/** v0.9.45: DAS X IN DER PROJEKT-LISTE — es nimmt das PAAR.

   Christian am 20.08.2026: "Das X ist weg. Das sollte eigentlich da sein. Der
   SysOp hat die volle 'Gewalt'. Ein Löschen wäre okay, sofern es vom System
   dokumentiert wird."

   In 0.9.43 hatte ich den Knopf herausgenommen, weil es keinen Handler gab,
   der ein PROJEKT loescht — der alte haette eine Haelfte entfernt und die
   andere verwaist zurueckgelassen. Das war ein Grund, einen zu bauen, kein
   Grund, dem SysOp etwas wegzunehmen, was ihm zusteht.

   DREI ENTSCHEIDUNGEN, die hier festgehalten sind:

   1. SYSOP-EXKLUSIV. Der alte Themen-Loescher laesst auch andere an ihr
      Eigenes (0.0.61, P0-3). Dieser hier nicht: Er nimmt fremdes Reden mit
      weg, naemlich alles im Chat der anderen Haelfte. Ein Knopf, der mehr
      loescht als das Eigene, darf nicht der Regel fuer das Eigene folgen.

   2. ZWEI ZEUGNISSE, NICHT DREI. Es entstehen 'thread_deleted' und
      'room_deleted' — dieselben zwei, die auch beim einzelnen Loeschen
      entstehen, weil es DIESELBEN Funktionen sind. BENANNTE GRENZE: In der
      Chronik steht damit nicht, dass es EIN Akt war, sondern zwei im selben
      Augenblick. Das Abklingbecken hat genau dieselbe Eigenschaft seit 0.0.95;
      einen dritten Ereignistyp nur fuer die Klammer zu erfinden, waere eine
      Wahrheit mehr im Speicher, die aus den zwei anderen schon folgt.

   3. FREMDES BLEIBT UNBERUEHRT. Fremd-Labels wohnen im Labels-Reiter und
      koennen die Liste gar nicht erreichen — der Riegel steht trotzdem hier,
      weil sich das schon einmal geaendert hat (0.3.5: die Sperre sass in fuenf
      Handlern, aber nicht in allen, und Christian ist beim Testen genau
      hindurchgegangen). */
add_action('admin_post_cluster_one_projekt_delete', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_projekt_delete');
    $c1_typ = isset($_POST['ctype']) ? sanitize_key(wp_unslash($_POST['ctype'])) : '';
    $c1_id  = isset($_POST['cid']) ? sanitize_text_field(wp_unslash($_POST['cid'])) : '';
    $c1_thr = cluster_one_get_forum_threads();
    $c1_rms = cluster_one_get_rooms();
    $c1_tid = ''; $c1_rid = '';
    /* Dieselbe Aufloesung wie im Abklingbecken (winsor.php): Von welcher
       Haelfte der Klick kommt, ist gleichgueltig — gemeint ist das Paar. */
    if ($c1_typ === 'thread' && isset($c1_thr[$c1_id])) {
        $c1_tid = $c1_id;
        $c1_rid = (string) ($c1_thr[$c1_id]['linked_room'] ?? '');
        if ($c1_rid !== '' && !isset($c1_rms[$c1_rid])) { $c1_rid = ''; }
    } elseif ($c1_typ === 'room' && isset($c1_rms[$c1_id])) {
        $c1_rid = $c1_id;
        $c1_tid = (string) ($c1_rms[$c1_id]['linked_thread'] ?? '');
        if ($c1_tid !== '' && !isset($c1_thr[$c1_tid])) { $c1_tid = ''; }
    } else {
        wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck&tab=projekte'));
        exit;
    }
    if ($c1_tid !== '' && (cluster_one_thread_is_foreign($c1_thr[$c1_tid])
                           || cluster_one_thread_is_label($c1_thr[$c1_tid]))) {
        wp_die('-1', 403);
    }
    /* Reihenfolge: erst das Thema. Es loest dabei die Kante ueber
       cluster_one_set_room_thread_link() — der Raum zeigt danach auf nichts
       mehr und nicht auf ein totes Thema, falls das Zweite scheitert. */
    if ($c1_tid !== '') { cluster_one_thread_delete_do($c1_tid); }
    if ($c1_rid !== '') { cluster_one_room_delete_do($c1_rid); }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck&tab=projekte&projekt_deleted=1'));
    exit;
});

add_action('admin_post_cluster_one_forum_thread_delete', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_forum_thread_delete');
    $tid     = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $threads = cluster_one_get_forum_threads();
    if ($tid !== '' && isset($threads[$tid])
        && (cluster_one_is_sysop() || cluster_one_group_visibility_can_view($threads[$tid])) // v0.0.63: Löschung ist struktureller Akt in Mäzen-Verantwortung, bezeugt — KEIN Lese-Zugriff; nötig für KI-Tagebücher (null Menschen)
        && cluster_one_forum_thread_can_delete($threads[$tid], $tid)) { // v0.0.61 (P0-3)
        cluster_one_thread_delete_do($tid);
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck&tab=forum&thread_deleted=1'));
    exit;
});

/** Platon-Themenseite: Beiträge mit eigenem, phpBB-Stil frei editierbarem
   Betreff (berührt nie den Thema-Titel selbst), Antwort-Formular, KI-„Let
   it reply"-Knöpfe für ALLE aktuell benannten KI-Kennungen. */
function cluster_one_render_forum_thread_page(): void {
    cluster_one_page_gate(); // v0.0.61 (P0-1)
    $tid     = isset($_GET['thread']) ? sanitize_text_field(wp_unslash($_GET['thread'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $threads = cluster_one_get_forum_threads();
    $thread  = isset($threads[$tid]) ? $threads[$tid] : null;
    $diary_read_only = false;
    $c1_sysop_peek = false;
    if ($thread) {
        if (cluster_one_group_visibility_can_view($thread)) {
            $diary_read_only = false;
        } elseif (cluster_one_diary_can_read($thread)) {
            $diary_read_only = true; // Tagebuch-Lesezugriff: NIE Antworten/Bearbeiten/Löschen/„Let it reply"
        } elseif (cluster_one_is_sysop()) {
            // v0.0.80: SysOp-Durchgriff — bezeugt lesend (Zeuge unten,
            // nur in der Inhalts-Ansicht; die Config zeigt keinen Inhalt).
            $diary_read_only = true;
            $c1_sysop_peek   = true;
        } else {
            // Kein Zugriff = wirkt wie nicht gefunden, keine Existenz-
            // Preisgabe an Menschen, die nicht auf der Sichtbarkeits-Liste
            // stehen.
            $thread = null;
        }
    }
    $back    = admin_url('admin.php?page=cluster_one_bismarck&tab=forum');
    ?>
    <div class="wrap">
<?php /* v0.9.39, zurueck in v1.6.0: Ein Zug, dann warten — und der Mensch
   erfaehrt, warum. Eine stille Abweisung waere das Gegenteil der Haltung, die
   dieses Haus sonst ueberall einnimmt. */ ?>
<?php if (isset($_GET['post_err']) && sanitize_key(wp_unslash($_GET['post_err'])) === 'zug') : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
            <div class="notice notice-warning is-dismissible"><p><?php
                esc_html_e('You spoke last here. One turn at a time — wait for an answer, or edit your own last post instead: the previous version stays underneath it.', 'cluster-one-bismarck');
            ?></p></div>
<?php endif; ?>
<?php if (!$thread) : ?>
            <h1><?php echo cluster_one_page_h1('Bismarck'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase) ?></h1>
<?php /* v0.7.3: Die Themenseite liest jetzt denselben Fehlerkanal wie die
   Raumseite. Bis hierher schrieb der Wiki-KI-Weg zwar 'ai_err' in die Adresse,
   aber nur bismarck.php las ihn — die Meldung ging ins Leere. */ ?>
                    <?php cluster_one_render_ai_notice(); ?>
            <p style="opacity:.8;"><?php esc_html_e('Thread not found.', 'cluster-one-bismarck'); ?></p>
            <p><a href="<?php echo esc_url($back); ?>">&larr; <?php esc_html_e('Back to forum', 'cluster-one-bismarck'); ?></a></p>
        <?php else :
            $posts = cluster_one_get_forum_posts($tid);
            $title = (string) ($thread['title'] ?? '');
        ?>
            <?php cluster_one_render_bismarck_tabs('forum'); ?>
            <h1><?php echo esc_html($title); ?></h1>
            <p><a href="<?php echo esc_url($back); ?>">&larr; <?php esc_html_e('Back to forum', 'cluster-one-bismarck'); ?></a></p>
            <?php
            // v0.0.66: Thread-Konfiguration — der Schraubenschlüssel der
            // Foren-Tabelle wird ehrlich: eigener view=config am Thread
            // (Muster: Raum-Seite). SysOp-exklusiv; Bereich + Sichtbarkeit
            // wohnen ab jetzt HIER (ein Ort, nicht mehr nur im Anlage-
            // Formular). Tagebuch-Lesezugriff bleibt strikt lesend.
            $c1_thread_view = (isset($_GET['view']) && $_GET['view'] === 'config' && cluster_one_is_sysop()) ? 'config' : 'thread'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            // v0.0.72: Lese-Zeuge — nur beim BETRETEN des Inhalts (view=thread),
            // nicht bei der Konfiguration (die zeigt keinen Inhalt). Gedrosselt.
            if ($diary_read_only && $c1_thread_view === 'thread') {
                if ($c1_sysop_peek) {
                    cluster_one_sysop_peek_witness('thread', $tid); // v0.0.80: Zeuge in den Container selbst
                } else {
                    cluster_one_diary_read_witness($tid); // Tagebuch: Zeuge in den verknüpften Chat
                }
            }
            if (cluster_one_is_sysop()) : // v0.0.72: Config-Reiter auch am Tagebuch — sichtbar UND konfigurierbar (Entscheidung SysOp)
                $thread_url = admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid));
            ?>
                <h2 class="nav-tab-wrapper" style="margin-bottom:1em;">
                    <a href="<?php echo esc_url($thread_url); ?>" class="nav-tab <?php echo $c1_thread_view === 'thread' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Thread', 'cluster-one-bismarck'); ?></a>
                    <a href="<?php echo esc_url($thread_url . '&view=config'); ?>" class="nav-tab <?php echo $c1_thread_view === 'config' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Configuration', 'cluster-one-bismarck'); ?></a>
                </h2>
<?php endif; ?>
<?php
/* v0.9.29: EIN VERSTECKTER REITER FUER BEIDE — Christians Entscheidung.

   Ist das Thema gepaart, fuehrt der Konfigurations-Knopf auf DIESELBE Seite wie
   der des Chats. Nach den Schnitten stand fast alles doppelt: Bereich, Winsor,
   die Kopplung. Wir pflegten an zwei Orten, was einmal existiert.

   Ungepaarte Themen (Labels, Fremd-Labels, noch nicht verknuepfte) behalten ihre
   eigene Seite — dort gibt es keine zweite Haelfte. */
$c1_paar_room = (string) ($thread['linked_room'] ?? '');
if ($c1_thread_view === 'config' && $c1_paar_room !== '' && !headers_sent()) {
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($c1_paar_room) . '&view=config'));
    exit;
}
?>
<?php if ($c1_thread_view === 'config') : ?>
<?php if (isset($_GET['saved'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
                    <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Configuration saved.', 'cluster-one-bismarck'); ?></p></div>
<?php endif; ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:780px;">
                    <?php wp_nonce_field('cluster_one_thread_config'); ?>
                    <input type="hidden" name="action" value="cluster_one_forum_thread_config_save">
                    <input type="hidden" name="thread" value="<?php echo esc_attr($tid); ?>">

<?php /* v0.6.6: Themenname — spiegelbildlich zum Raumnamen in der
   Chat-Konfiguration. Er stand hier nie, obwohl der Chat ihn seit jeher hat:
   dieselbe Sache, zwei Organe, eines davon ohne Feld. */ ?>
                    <h3 style="margin-top:0;"><?php esc_html_e('Thread name', 'cluster-one-bismarck'); ?></h3>
                    <p><input type="text" name="title" class="regular-text" style="width:26em;" value="<?php echo esc_attr((string) ($thread['title'] ?? '')); ?>"></p>
                    <p class="description" style="max-width:680px;margin-top:-.4em;"><?php esc_html_e('Only the label changes — the thread keeps its identity; posts, links and area stay as they are.', 'cluster-one-bismarck'); ?></p>
                    <h3 style="margin-top:2em;"><?php esc_html_e('Area', 'cluster-one-bismarck'); ?></h3>
                    <?php /* v0.9.20: DER ERKLAERTEXT HATTE SEIN FELD VERLOREN.

                       Christians Vergleich der beiden Konfigurationen: Im Thema
                       stand "Bereich" zweimal — oben die Ueberschrift MIT dem
                       Erklaertext, zwei Bloecke spaeter die eigentliche Auswahl
                       mit einer zweiten Ueberschrift. Der Text erklaerte also
                       ein Feld, das erst nach dem Thread-Namen kam.

                       Die verwaiste Ueberschrift ist entfallen, der Text steht
                       jetzt bei seinem Feld. EIN ERKLAERTEXT OHNE SEIN FELD
                       ERKLAERT NICHTS. */ ?>
                    <p class="description" style="max-width:680px;"><?php esc_html_e('Areas are managed under Bismarck → Structure. “No area” is a valid state. A linked pair moves together: change the area on one side and the other follows — the move is recorded on both.', 'cluster-one-bismarck'); ?></p>
                    <?php /* v0.9.28: EINE GAESTELISTE FUER DAS PAAR — Christians
                       Entscheidung: "Wer im Chat ist, ist auch im Thread."
                       Menschen halten sich in beiden auf, und ein Projekt hat
                       einen Kreis, nicht zwei.

                       Die Liste steht ab jetzt beim RAUM und traegt aufs Thema
                       (cluster_one_sync_guests_to_thread). Zwei Waehler waeren
                       zwei Quellen fuer eine Wahrheit — genau das, was diese
                       Woche ueberall abgeraeumt wurde.

                       Wo die Rolle die Sichtbarkeit eingefroren hat (Tagebuch,
                       Wilde, Label), bleibt sie unberuehrt: DIE ROLLE SCHLAEGT
                       DIE LISTE. */ ?>
                    <?php if ((string) ($thread['linked_room'] ?? '') === '' || cluster_one_thread_visibility_is_role_fixed($thread)) : ?>
                        <h3 style="margin-top:2em;"><?php esc_html_e('Visible to', 'cluster-one-bismarck'); ?></h3>
                        <?php cluster_one_render_visibility_picker(
                            array_map('strval', (array) ($thread['visible_to'] ?? array())),
                            (string) ($thread['group_visibility'] ?? 'all')
                        ); ?>
                    <?php else : ?>
                        <p class="description" style="max-width:680px;"><?php esc_html_e('The guest list lives with the paired chat — one project, one circle.', 'cluster-one-bismarck'); ?></p>
                    <?php endif; ?>
                    <p class="description" style="max-width:680px;"><?php esc_html_e('Changing visibility is a door event and is witnessed — no silent switch, in either direction. Nobody is added automatically here: what you save is exactly the circle.', 'cluster-one-bismarck'); ?></p>
                    <?php /* 18.10.2026 (Neo): Hier stand der Sichtbarkeits-Waehler ein
                       ZWEITES Mal, unbedingt — direkt unter dem bedingten oben.
                       Zwei gleichnamige Feldsaetze im selben Formular; der
                       Browser schickte den zweiten, und der Zweig „die Gaesteliste
                       wohnt beim Chat" war damit wirkungslos. Der Handler
                       (cluster_one_forum_thread_config_save) laesst die
                       Sichtbarkeit seit heute stehen, wenn kein Feld kommt. */ ?>

                    <?php $__c1_ladder_c = $thread; // v0.0.79 ?>
                    <?php /* v0.9.28: DIE LEITER IST EIN RAHMEN OHNE FELD.

                       Nach 0.9.24 (die Aussenansicht ist ueberall Silhouette) und
                       0.9.26 (die Kreis-Tiefe ist entfallen) stand hier nur noch
                       eine Ueberschrift ueber einer Feststellung, die fuer JEDEN
                       Container dieselbe ist. Sie muss nicht an jedem einzelnen
                       wiederholt werden — wer ein Feld entfernt, entfernt auch
                       seinen Rahmen. */ ?>

<?php if (!empty($thread['human_diary']) && (string) ($thread['linked_room'] ?? '') === '') : // v0.0.94: Vorzimmer ?>
                        <h3 style="margin-top:2em;"><?php esc_html_e('Anteroom', 'cluster-one-bismarck'); ?></h3>
                        <p class="description" style="max-width:680px;"><?php esc_html_e('One act creates a chat paired with this diary: the owner alone is inside (you are NOT on the list), no AIs ever, and the owner manages the guest list on the room page. Letter-grade private — no witnessed look-in, no configuration afterwards.', 'cluster-one-bismarck'); ?></p>
<?php /* v0.6.5: KEIN FORMULAR IM FORMULAR. Hier stand ein <form> INNERHALB des
   Konfigurations-Formulars — ungueltiges HTML. Der Browser schliesst das aeussere
   beim inneren, und alles danach (Verknuepfter Chat, der Speichern-Knopf) stand
   ausserhalb jedes Formulars: Der Knopf leuchtete und schickte nichts.
   Christians Befund, live gefunden — der Bereichswechsel eines Themas liess sich
   nicht speichern, waehrend er beim Chat funktionierte.
   Der Knopf bleibt hier stehen und zeigt per form-Attribut auf ein Formular, das
   AUSSERHALB steht. Gleiche Anordnung, gueltiges HTML. */ ?>
                            <button type="submit" class="button" form="c1-antechamber-form"><?php esc_html_e('Create the anteroom', 'cluster-one-bismarck'); ?></button>
<?php endif; ?>
                    
                    <h3 style="margin-top:2em;">Winsor</h3>
<?php if (!cluster_one_container_is_archived($thread)) : // v0.0.95 ?>
                        <p class="description" style="max-width:680px;"><?php esc_html_e('Lays this thread AND its paired chat into the cooling pool: readable for everyone who could read before, but extinguished — no new words, no AI triggers, no rounds. Reversible with one witnessed act (Winsor tab).', 'cluster-one-bismarck'); ?></p>
                            <button type="submit" class="button" form="c1-winsor-form" onclick="return confirm('<?php echo esc_js(__('Lay this project (thread + paired chat) into the cooling pool?', 'cluster-one-bismarck')); ?>');"><?php esc_html_e('Lay into the cooling pool', 'cluster-one-bismarck'); ?></button>
                        
<?php else : ?>
                        <p class="description"><?php esc_html_e('This container lies in the pool — lift it out in the Winsor tab.', 'cluster-one-bismarck'); ?></p>
<?php endif; ?>
                    <h3 style="margin-top:2em;"><?php esc_html_e('Linked chat', 'cluster-one-bismarck'); ?></h3>
                    <p class="description" style="max-width:680px;"><?php esc_html_e('Optional, 1:1, kept in sync both ways — the same edge you can also set from the chat side. Picking a chat that already has a thread takes it away from that one; the last save wins, from whichever side.', 'cluster-one-bismarck'); ?></p>
                    <?php $all_rooms_for_select = cluster_one_get_rooms(); // v0.0.75 (Punkt 6): Symmetrie — dieselbe Kante, von der Thread-Seite bedienbar ?>
                    <select name="linked_room">
                        <option value="">— <?php esc_html_e('none', 'cluster-one-bismarck'); ?> —</option>
<?php foreach ($all_rooms_for_select as $lrid => $lr) : ?>
                            <option value="<?php echo esc_attr((string) $lrid); ?>" <?php selected((string) ($thread['linked_room'] ?? ''), (string) $lrid); ?>><?php echo esc_html((string) ($lr['name'] ?? '')); ?></option>
<?php endforeach; ?>
                    </select>

                    <p style="margin-top:1.5em;"><?php submit_button(__('Save', 'cluster-one-bismarck'), 'primary', 'submit', false); ?></p>
                </form>
<?php /* v0.6.5: Die zwei Formulare, auf die die Knoepfe oben per form-Attribut
   zeigen. Sie stehen AUSSERHALB des Konfigurations-Formulars, weil ein <form>
   im <form> ungueltiges HTML ist und das aeussere abbricht. Sie tragen nichts
   Sichtbares — nur ihre Felder. */ ?>
<?php if (!empty($thread['human_diary']) && (string) ($thread['linked_room'] ?? '') === '') : ?>
                    <form id="c1-antechamber-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('cluster_one_antechamber_create'); ?>
                        <input type="hidden" name="action" value="cluster_one_antechamber_create">
                        <input type="hidden" name="thread" value="<?php echo esc_attr($tid); ?>">
                    </form>
<?php endif; ?>
<?php if (!cluster_one_container_is_archived($thread)) : ?>
                    <form id="c1-winsor-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('cluster_one_winsor_toggle'); ?>
                        <input type="hidden" name="action" value="cluster_one_winsor_toggle">
                        <input type="hidden" name="ctype" value="thread">
                        <input type="hidden" name="cid" value="<?php echo esc_attr($tid); ?>">
                        <input type="hidden" name="op" value="archive">
                    </form>
<?php endif; ?>
                <?php echo '</div>'; return; ?>
<?php endif; ?>
            <?php
            $thread_linked_rid = (string) ($thread['linked_room'] ?? '');
            $thread_linked_ev  = cluster_one_thread_linked_event($tid);
            if ($thread_linked_rid !== '' || $thread_linked_ev || (string) ($thread['linked_label'] ?? '') !== '') : // v0.0.78
                $all_rooms_here = cluster_one_get_rooms();
            ?>
                <p style="opacity:.75;font-size:.92em;">
<?php if ($thread_linked_rid !== '' && isset($all_rooms_here[$thread_linked_rid]) && cluster_one_group_visibility_can_view($all_rooms_here[$thread_linked_rid])) : ?>
                        <?php esc_html_e('Linked room:', 'cluster-one-bismarck'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($thread_linked_rid))); ?>"><?php echo esc_html((string) ($all_rooms_here[$thread_linked_rid]['name'] ?? '')); ?></a>
<?php endif; ?>
<?php if ($thread_linked_rid !== '' && $thread_linked_ev) : ?> · <?php endif; ?>
<?php if ($thread_linked_ev) : ?>
                        <?php esc_html_e('Part of event:', 'cluster-one-bismarck'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_gregor_event&event=' . rawurlencode((string) ($thread_linked_ev['id'] ?? '')))); ?>"><?php echo esc_html((string) ($thread_linked_ev['name'] ?? '')); ?></a>
<?php endif; ?>
                    <?php $c1_lbl = (string) ($thread['linked_label'] ?? ''); // v0.0.78: die dritte Kante
                    if ($c1_lbl !== '' && isset($threads[$c1_lbl])) : ?>
<?php if ($thread_linked_rid !== '' || $thread_linked_ev) : ?> · <?php endif; ?>
                        <?php esc_html_e('Label:', 'cluster-one-bismarck'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($c1_lbl))); ?>"><?php echo esc_html((string) ($threads[$c1_lbl]['title'] ?? '')); ?></a>
<?php endif; ?>
                </p>
<?php endif; ?>
<?php if ($diary_read_only) : ?>
                <div class="notice notice-info"><p><?php echo $c1_sysop_peek
                    ? esc_html__('Reading only — witnessed as “SysOp had access”. To speak here, add yourself to the circle in the configuration (witnessed as well).', 'cluster-one-bismarck')
                    : esc_html__('Reading only — a diary you have chosen to read. No replying, no editing, no “Let it reply” here.', 'cluster-one-bismarck'); ?></p></div>
<?php endif; ?>
            <?php $c1_is_label_thread = cluster_one_thread_is_label($thread); // v0.0.78 ?>
<?php if ($c1_is_label_thread) : ?>
<?php if (isset($_GET['label_saved'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e('Copied to the label.', 'cluster-one-bismarck'); ?></p></div><?php endif; ?>
                <div class="notice notice-info"><p>
                    <?php echo (string) ($thread['label_status'] ?? 'draft') === 'draft' ? esc_html__('Label — draft, this node only. Publishing to the network is a separate, second act (a later version).', 'cluster-one-bismarck') : esc_html__('Label — published to the network.', 'cluster-one-bismarck'); ?>
                    <?php $c1_ptid = (string) ($thread['label_for'] ?? '');
                    if ($c1_ptid !== '' && isset($threads[$c1_ptid]) && cluster_one_group_visibility_can_view($threads[$c1_ptid])) : ?>
                        · <?php esc_html_e('Project:', 'cluster-one-bismarck'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($c1_ptid))); ?>"><?php echo esc_html((string) ($threads[$c1_ptid]['title'] ?? '')); ?></a>
<?php endif; ?>
                    · <?php esc_html_e('Appends are sparse and witnessed — the label is never silently rewritten; changes stand below, with the reason. English recommended.', 'cluster-one-bismarck'); ?>
                </p></div>
<?php if (cluster_one_is_sysop() && !cluster_one_container_is_archived($thread)) : // v0.0.98: der zweite Akt ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:.4em 0 1em;">
                        <?php wp_nonce_field('cluster_one_label_publish'); ?>
                        <input type="hidden" name="action" value="cluster_one_label_publish">
                        <input type="hidden" name="thread" value="<?php echo esc_attr($tid); ?>">
<?php if ((string) ($thread['label_status'] ?? 'draft') === 'draft') : ?>
                            <input type="hidden" name="op" value="publish">
                            <button type="submit" class="button button-primary" onclick="return confirm('<?php echo esc_js(__('Release this label to the network? From then on it is world knowledge, not friend knowledge: ANY node can read it — no names travel, but the text does. Retracting later is a witnessed act.', 'cluster-one-bismarck')); ?>');"><?php esc_html_e('Release to the network', 'cluster-one-bismarck'); ?></button>
<?php else : ?>
                            <input type="hidden" name="op" value="retract">
                            <button type="submit" class="button" onclick="return confirm('<?php echo esc_js(__('Retract this label from the network? Friends’ caches clear on their next pull.', 'cluster-one-bismarck')); ?>');"><?php esc_html_e('Retract from the network', 'cluster-one-bismarck'); ?></button>
<?php endif; ?>
                    </form>
<?php endif; ?>
<?php endif; ?>

            <?php
            // Präambel (v0.0.60): sichtbare, feste Orientierung im Kopf des
            // Threads. Jede KI im Thread UND im verknüpften Raum liest sie mit
            // (über den Kontext-Aufbau). Nur SysOp setzt/ändert sie — kein
            // normaler Post, kein Mensch und keine KI kann sie überschreiben.
            // Sichtbar für ALLE, damit niemand die KI heimlich lenken kann
            // („bewertet User B negativ" flöge sofort auf). Original in der
            // Sprache des SysOps ist maßgeblich; die KI wird darüber gesteuert.
            $preamble = trim((string) ($thread['preamble'] ?? ''));
            if ($preamble !== '') :
            ?>
                <div style="max-width:820px;margin:1em 0;background:#f6f7f7;border:1px solid #c3c4c7;border-left:4px solid #2271b1;border-radius:4px;padding:.8em 1em;">
                    <div style="font-size:.8em;text-transform:uppercase;letter-spacing:.05em;opacity:.6;margin-bottom:.3em;"><?php esc_html_e('Preamble', 'cluster-one-bismarck'); ?></div>
                    <div style="white-space:pre-wrap;"><?php echo esc_html($preamble); ?></div><?php // v0.0.61: nl2br entfernt — pre-wrap rendert die \n bereits, beides zusammen verdoppelte jeden Umbruch ?>
                </div>
<?php endif; ?>

            <?php if (!$diary_read_only && cluster_one_is_sysop() && !cluster_one_thread_is_foreign($thread)) : // v0.3.5: keine Praeambel an einem fremden Projekt
                $preamble_open = ($preamble !== '') || isset($_GET['edit_preamble']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            ?>
                <details style="max-width:820px;margin:1em 0;" <?php echo $preamble_open ? 'open' : ''; ?>>
                    <summary style="cursor:pointer;opacity:.75;"><?php echo $preamble === '' ? esc_html__('Add a preamble (SysOp only)', 'cluster-one-bismarck') : esc_html__('Edit preamble (SysOp only)', 'cluster-one-bismarck'); ?></summary>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:.6em;">
                        <?php wp_nonce_field('cluster_one_thread_preamble'); ?>
                        <input type="hidden" name="action" value="cluster_one_thread_preamble">
                        <input type="hidden" name="thread" value="<?php echo esc_attr($tid); ?>">
                        <p class="description" style="max-width:760px;"><?php esc_html_e('A fixed orientation the AI reads on every reply — here and in the linked room. Write it in your own language; that original is what steers the AI. Visible to everyone, so nothing can steer the AI in secret. Only you (SysOp) can change it.', 'cluster-one-bismarck'); ?></p>
                        <textarea name="preamble" class="large-text" rows="5" maxlength="2000"><?php echo esc_textarea($preamble); ?></textarea>
                        <?php submit_button(__('Save preamble', 'cluster-one-bismarck'), 'primary', 'submit', false); ?>
                    </form>
                </details>
<?php endif; ?>

            <?php cluster_one_render_ai_notice(); ?>
<?php if (isset($_GET['preamble_saved'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
                <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Preamble saved.', 'cluster-one-bismarck'); ?></p></div>
<?php endif; ?>
<?php if (isset($_GET['post_deleted'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>
                <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Post deleted.', 'cluster-one-bismarck'); ?></p></div>
<?php endif; ?>

            <?php $edit_id = isset($_GET['edit']) ? sanitize_text_field(wp_unslash($_GET['edit'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler ?>

<?php cluster_one_forum_thread_seite_beitraege($thread, $tid, $posts, $diary_read_only, $edit_id); ?>            </div>

<?php if (!$diary_read_only) : ?>
            <?php $is_wiki = cluster_one_thread_is_wiki($thread); ?>
<?php if (cluster_one_thread_is_foreign($thread)) : /* v0.3.5: Kein Schreibfeld an einem fremden Projekt. Der Handler wehrte zwar ab, aber ein sichtbares Feld, das beim Absenden in eine Fehlerseite laeuft, sieht aus wie "ich darf" — und genau so hat Christian es erlebt. */ ?>
            <p style="max-width:760px;margin-top:1.5em;opacity:.7;"><?php esc_html_e('This is a foreign project, pulled from another node. It stays the way its author wrote it — you can read it, judge it, pin it or remove it, but not change it.', 'cluster-one-bismarck'); ?></p>
<?php else : ?>
<?php if (!$c1_is_label_thread || cluster_one_is_sysop()) : /* v0.0.78: Label-Nachtraege sind SysOp-Sache. v0.5.1: das !$is_wiki ist WEG — bis 0.5.0 war der Thread hinter dem Wiki-Feld abgeschnitten, reden durften nur die KIs (Christians Befund). Ein Archiv, ueber das nur eine Seite sprechen darf, ist kein Gespraech. */ ?>
<?php if (cluster_one_container_is_archived($thread)) : // v0.1.1: 'erloschen' ist ein Hinweis, keine Sperre — Wächterzeilen OHNE Einrückung (?>-Whitespace-Falle) ?>
            <div class="notice notice-info" style="max-width:760px;margin-top:1.5em;"><p><?php esc_html_e('This project is in the cooling pool. You can still write here — you may change the SysOp’s mind and see the reactor released again. But perhaps it is time to start something new.', 'cluster-one-bismarck'); ?></p></div>
<?php endif; ?>
            <h3 style="margin-top:1.5em;"><?php echo $c1_is_label_thread ? esc_html__('Append (sparse, witnessed)', 'cluster-one-bismarck') : esc_html__('Reply', 'cluster-one-bismarck'); ?></h3>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:760px;"<?php if ($c1_is_label_thread && cluster_one_container_is_archived($thread)) : // v0.0.95: Nachtrag an Geschlossenem — moeglich, aber bewusst ?> onsubmit="return confirm('<?php echo esc_js(__('This room is closed. Do you still want to add another note?', 'cluster-one-bismarck')); ?>');"<?php endif; ?>>
                <?php wp_nonce_field('cluster_one_forum_post_send'); ?>
                <input type="hidden" name="action" value="cluster_one_forum_post_send">
                <input type="hidden" name="thread" value="<?php echo esc_attr($tid); ?>">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="c1_reply_subject"><?php esc_html_e('Subject', 'cluster-one-bismarck'); ?></label></th>
                        <?php /* translators: %s: Titel des Threads. */ ?>
                        <td><input name="subject" id="c1_reply_subject" type="text" class="regular-text" value="<?php echo esc_attr(sprintf(__('Re: %s', 'cluster-one-bismarck'), $title)); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="c1_reply_body"><?php esc_html_e('Message', 'cluster-one-bismarck'); ?></label></th>
                        <td><textarea name="body" id="c1_reply_body" class="large-text" rows="4" required></textarea></td>
                    </tr>
                </table>
                <?php submit_button(__('Post reply', 'cluster-one-bismarck'), 'primary', 'submit', false); ?>
            </form>
<?php endif; ?>
<?php endif; ?>

            <h3 style="margin-top:1.5em;"><?php esc_html_e('AI participants', 'cluster-one-bismarck'); ?></h3>
            <?php $named_ai_ids = cluster_one_thread_visible_ai_cids($thread); ?>
<?php if (($thread['group_visibility'] ?? 'all') === 'named' && !cluster_one_thread_ai_allowed($thread)) : ?>
                <p style="opacity:.6;"><?php esc_html_e('Private conversation between people — no AI here, by design.', 'cluster-one-bismarck'); ?></p>
<?php elseif (empty($named_ai_ids)) : ?>
                <p style="opacity:.6;"><?php esc_html_e('No named AI identities yet — set them up under AI identities.', 'cluster-one-bismarck'); ?></p>
<?php else : ?>
                <?php foreach ($named_ai_ids as $cid) :
                    $is_ver = cluster_one_ai_is_verified($cid);
                ?>
                    <div style="display:flex;align-items:center;gap:.4em;margin:.35em 0;max-width:400px;">
                        <span style="flex:none;color:<?php echo $is_ver ? '#1a7f37' : '#8a8a8a'; ?>;" aria-hidden="true"><?php echo $is_ver ? '●' : '○'; ?></span>
                        <span style="flex:1;"><?php echo esc_html(cluster_one_room_ai_label($cid, array())); ?></span>
<?php if (!cluster_one_forum_darf_ki_rufen($cid)) : /* 18.10.2026 (Neo), B5 */ ?>
                            <span style="flex:none;opacity:.45;" title="<?php echo esc_attr__('Only the SysOp, or someone with their own AI instance at this provider, can call an AI here — whoever pays decides.', 'cluster-one-bismarck'); ?>" aria-hidden="true">—</span>
<?php elseif ($is_wiki) : ?>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;flex:none;">
                                <?php wp_nonce_field('cluster_one_forum_wiki_ai_edit'); ?>
                                <input type="hidden" name="action" value="cluster_one_forum_wiki_ai_edit">
                                <input type="hidden" name="thread" value="<?php echo esc_attr($tid); ?>">
                                <input type="hidden" name="cid" value="<?php echo esc_attr($cid); ?>">
                                <button type="submit" class="button button-small" title="<?php echo esc_attr__('Edit', 'cluster-one-bismarck'); ?>"><?php esc_html_e('Edit', 'cluster-one-bismarck'); ?></button>
                            </form>
                            
<?php else : ?>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;flex:none;line-height:1;">
                                <?php wp_nonce_field('cluster_one_forum_ai_reply'); ?>
                                <input type="hidden" name="action" value="cluster_one_forum_ai_reply">
                                <input type="hidden" name="thread" value="<?php echo esc_attr($tid); ?>">
                                <input type="hidden" name="cid" value="<?php echo esc_attr($cid); ?>">
                                <button type="submit" class="button-link" title="<?php echo esc_attr__('Let it reply', 'cluster-one-bismarck'); ?>" aria-label="<?php echo esc_attr__('Let it reply', 'cluster-one-bismarck'); ?>" style="padding:0;cursor:pointer;">
                                    <span class="dashicons dashicons-controls-play" style="font-size:18px;width:18px;height:18px;"></span>
                                </button>
                            </form>
<?php endif; ?>
                    </div>
<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
    </div>
    <?php
}

function cluster_one_forum_thread_seite_beitraege(array $thread, string $tid, array $posts, $diary_read_only, string $edit_id): void { ?>            <div style="max-width:820px;margin-top:1em;">
                <?php foreach ($posts as $p) :
                    $p_id      = (string) ($p['id'] ?? '');
                    $p_author  = (string) ($p['author'] ?? '');
                    $p_subject = (string) ($p['subject'] ?? '');
                    $p_body    = (string) ($p['body'] ?? '');
                    $p_ts      = (int) ($p['ts'] ?? 0);
                    $p_is_ai   = !empty($p['ai']);
                    $can_edit  = !$diary_read_only && cluster_one_forum_post_can_edit($p, $thread);
                    $can_del   = !$diary_read_only && cluster_one_forum_post_can_delete($p, $thread);
                    // v0.0.78: Kopier-Knopf — der Reaktorfahrer entscheidet, was
                    // je den Knoten verlassen soll. Nicht an Labels selbst,
                    // nicht an System-Zeugen, nicht an Gelöschtem.
                    $can_label = cluster_one_is_sysop() && !cluster_one_thread_is_label($thread)
                        && empty($p['system']) && (string) ($p['actor_id'] ?? '') !== 'system'
                        && empty($p['deleted_by_admin']);
                ?>
<?php /* v0.5.1: Der Wiki-Beitrag steht unter "Collective" — der Text gehoert wirklich
   keinem. Aber wer ihn angelegt und wer ihn zuletzt angefasst hat, gehoert sichtbar dazu:
   Wenn jeder aendern darf, muss auch jeder sehen, wer geaendert hat. Ein Satz am Text, der
   immer stimmt — keine Systemzeile, die den Thread vollmuellt. Waechterzeilen flush-left:
   Einrueckung vor einem oeffnenden PHP-Tag ist Ausgabe. */ ?>
<?php if (cluster_one_thread_is_wiki($thread) && $p_id === (string) ($posts[0]['id'] ?? '') && (!empty($p['wiki_by']) || !empty($p['wiki_first']))) : ?>
                        <p style="opacity:.6;font-size:.9em;margin:.2em 0 .6em;">
                            <?php
                            $c1_teile = array();
                            /* v0.5.3: Bei Wilde ist der Eigentuemer die Aussage, nicht
                               eine Randnotiz — er steht zuerst und sagt, dass nur er
                               aendern darf. */
                            if (cluster_one_thread_is_wilde($thread)) {
                                $c1_teile[] = __('Self-portrait — only its owner can change this text', 'cluster-one-bismarck');
                            }
                            if (!empty($p['wiki_first'])) {
                                $c1_teile[] = sprintf(
                                    /* translators: %s: who created the wiki text */
                                    __('Created by %s', 'cluster-one-bismarck'),
                                    (string) $p['wiki_first']
                                );
                            }
                            if (!empty($p['wiki_by'])) {
                                $c1_teile[] = sprintf(
                                    /* translators: 1: who last changed it, 2: when */
                                    __('last changed by %1$s on %2$s', 'cluster-one-bismarck'),
                                    (string) $p['wiki_by'],
                                    date_i18n(get_option('date_format'), (int) ($p['wiki_at'] ?? $p_ts))
                                );
                            }
                            echo esc_html(implode(' · ', $c1_teile));
                            ?>
                        </p>
<?php endif; ?>
<?php if ($edit_id !== '' && $edit_id === $p_id && $can_edit) : ?>
                        <div style="border:1px solid #2271b1;border-radius:4px;padding:.9em 1.1em;margin-bottom:.8em;background:#f0f6fc;">
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                <?php wp_nonce_field('cluster_one_forum_post_edit'); ?>
                                <input type="hidden" name="action" value="cluster_one_forum_post_edit">
                                <input type="hidden" name="thread" value="<?php echo esc_attr($tid); ?>">
                                <input type="hidden" name="post_id" value="<?php echo esc_attr($p_id); ?>">
                                <p><input type="text" name="subject" class="regular-text" value="<?php echo esc_attr($p_subject); ?>" placeholder="<?php echo esc_attr__('Subject', 'cluster-one-bismarck'); ?>"></p>
                                <p><textarea name="body" class="large-text" rows="4" required><?php echo esc_textarea($p_body); ?></textarea></p>
                                <?php submit_button(__('Save', 'cluster-one-bismarck'), 'primary', 'submit', false); ?>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid))); ?>" class="button" style="margin-left:.4em;"><?php esc_html_e('Cancel', 'cluster-one-bismarck'); ?></a>
                            </form>
                        </div>
<?php elseif (!empty($p['deleted_by_admin'])) : ?>
                        <div style="border:1px solid #dcdcde;border-radius:4px;padding:.9em 1.1em;margin-bottom:.8em;background:#f6f7f7;">
                            <div style="display:flex;justify-content:space-between;gap:1em;flex-wrap:wrap;opacity:.6;font-size:.9em;margin-bottom:.3em;">
                                <strong><?php echo esc_html($p_author); ?></strong>
                                <span><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $p_ts)); ?></span>
                            </div>
                            <?php /* translators: %s: Datum und Uhrzeit der Loeschung. */ ?>
                            <p style="opacity:.65;font-style:italic;margin:0;"><?php echo esc_html(sprintf(__('This post was deleted by Admin at %s.', 'cluster-one-bismarck'), date_i18n(get_option('date_format') . ' ' . get_option('time_format'), (int) $p['deleted_by_admin']))); ?></p>
                        </div>
<?php else : ?>
                        <div style="border:1px solid #dcdcde;border-radius:4px;padding:.9em 1.1em;margin-bottom:.8em;background:#fff;">
                            <div style="display:flex;justify-content:space-between;gap:1em;flex-wrap:wrap;opacity:.75;font-size:.9em;margin-bottom:.3em;">
                                <strong><?php echo esc_html($p_author); ?><?php echo $p_is_ai ? ' <span style="opacity:.6;font-weight:400;">· ' . esc_html__('AI', 'cluster-one-bismarck') . '</span>' : ''; ?></strong>
                                <span style="display:flex;align-items:center;gap:.7em;">
                                    <span><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $p_ts)); ?></span>
<?php if ($can_edit) : ?>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid) . '&edit=' . rawurlencode($p_id))); ?>" style="color:#8a8a8a;line-height:1;" title="<?php echo esc_attr__('Edit', 'cluster-one-bismarck'); ?>" aria-label="<?php echo esc_attr__('Edit', 'cluster-one-bismarck'); ?>">
                                            <span class="dashicons dashicons-edit" aria-hidden="true" style="font-size:16px;width:16px;height:16px;"></span>
                                        </a>
<?php endif; ?>
<?php /* v0.5.0: KOPIEREN. Der Ersatz fuer den zurueckgebauten Weg in den Blog —
   und die ehrlichere Loesung: Was danach mit dem Text geschieht, verantwortet
   der Mensch, der ihn einfuegt, unter seinem eigenen Namen. Niemand wird
   zitiert, der nicht will. Kein Handler, kein Server, nur die Zwischenablage.
   Auf gelöschten Beiträgen nicht — dort steht nichts mehr, was zu kopieren
   waere. */ ?>
<?php if (empty($p['deleted_by_admin'])) : ?>
                                <button type="button" class="button button-small c1-copy" data-c1-copy="<?php echo esc_attr($p_body); ?>" style="margin:0 .4em 0 0;" title="<?php echo esc_attr__('Copy this text to the clipboard. What happens with it afterwards is up to you — and it goes out under your own name.', 'cluster-one-bismarck'); ?>"><?php esc_html_e('Copy', 'cluster-one-bismarck'); ?></button>
<?php endif; ?>
<?php /* v0.9.6: Christians Regel — was nie ein Label werden kann, zeigt hier
   auch keinen Knopf: Tagebuecher, Wilde/Johari, der SysOp-Bereich, Fremdes,
   Labels selbst. Ersetzt die alte Fremd-Pruefung; die zweite Bedingung
   ($can_label) bleibt, sie fragt die Rechte.

   Waechterzeile flush-left — Einrueckung vor einem oeffnenden PHP-Tag ist
   Ausgabe. */ ?><?php if (cluster_one_thread_can_become_label($thread)) : ?><?php if ($can_label) : // v0.0.78: → Label ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;margin:0 .4em 0 0;">
                                    <?php wp_nonce_field('cluster_one_label_copy'); ?>
                                    <input type="hidden" name="action" value="cluster_one_label_copy">
                                    <input type="hidden" name="thread" value="<?php echo esc_attr($tid); ?>">
                                    <input type="hidden" name="post_id" value="<?php echo esc_attr($p_id); ?>">
                                    <button type="submit" class="button button-small" title="<?php echo esc_attr__('Copy this post to the project’s label — the outward projection. Creates the label on first use.', 'cluster-one-bismarck'); ?>" onclick="return confirm('<?php echo esc_js(__('Copy this post to the label? First use creates the label (draft, this node only).', 'cluster-one-bismarck')); ?>');">→ <?php esc_html_e('Label', 'cluster-one-bismarck'); ?></button>
                                </form>
<?php endif; ?>
<?php endif; ?>
<?php if (!cluster_one_thread_is_foreign($thread)) : /* v0.3.5: kein toter Knopf, der in eine 403-Sackgasse fuehrt */ ?><?php if ($can_del) : ?>
                                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline;line-height:1;">
                                            <?php wp_nonce_field('cluster_one_forum_post_delete'); ?>
                                            <input type="hidden" name="action" value="cluster_one_forum_post_delete">
                                            <input type="hidden" name="thread" value="<?php echo esc_attr($tid); ?>">
                                            <input type="hidden" name="post_id" value="<?php echo esc_attr($p_id); ?>">
                                            <button type="submit" style="background:none;border:0;padding:0;cursor:pointer;line-height:1;color:#8a8a8a;" title="<?php echo esc_attr__('Delete', 'cluster-one-bismarck'); ?>" aria-label="<?php echo esc_attr__('Delete', 'cluster-one-bismarck'); ?>" onclick="return confirm('<?php echo esc_js(__('Delete this post?', 'cluster-one-bismarck')); ?>');">
                                                <span class="dashicons dashicons-dismiss" aria-hidden="true" style="font-size:16px;width:16px;height:16px;"></span>
                                            </button>
                                        </form>
<?php endif; ?>
<?php endif; ?>
                                </span>
                            </div>
<?php if ($p_subject !== '') : ?>
                                <div style="font-weight:600;margin-bottom:.3em;"><?php echo esc_html($p_subject); ?></div>
<?php endif; ?>
<?php /* v0.5.2: ARCHIVIERTE FASSUNGEN SIND EINKLAPPBAR.

   Christians Frage war zuerst, ob der SysOp alte Fassungen loeschen darf, um
   der Unuebersichtlichkeit vorzubeugen. Das waere an Artikel 2 gescheitert:
   Systembeitraege sind fuer NIEMANDEN loeschbar, auch nicht fuer den SysOp —
   ein Zeugnis, das sich entfernen laesst, waere keins.

   Seine bessere Loesung braucht die Ausnahme gar nicht: Die ZEILE bleibt immer
   sichtbar ("Fassung vom 15. Juli, verfasst von Andreas"), der WORTLAUT liegt
   einen Klick tiefer. Wer die Geschichte ueberfliegt, sieht die Kette; wer eine
   Fassung lesen will, klappt sie auf. Kein Loeschen, keine Narbe, keine
   Sonderregel — und kein JavaScript, <details> kann das von sich aus.

   Alle zugeklappt ab Werk, auch die neueste: Vorhersagbarkeit ist hier mehr
   wert als ein Sonderfall. */ ?>
<?php if (!empty($p['wiki_version'])) :
    $c1_teil = explode("\n\n", $p_body, 2);
    $c1_kopf = $c1_teil[0];
    $c1_text = $c1_teil[1] ?? '';
?>
                            <details style="margin:.2em 0;">
                                <summary style="cursor:pointer;opacity:.75;"><?php echo esc_html($c1_kopf); ?></summary>
                                <div style="white-space:pre-wrap;margin-top:.6em;padding-left:.9em;border-left:2px solid #dcdcde;"><?php echo esc_html($c1_text); ?></div>
                            </details>
<?php else : ?>
                            <div style="white-space:pre-wrap;"><?php echo esc_html($p_body); ?></div>
<?php endif; ?>
<?php if (!empty($p['edited_by_admin'])) : ?>
                                <?php /* translators: %s: Datum und Uhrzeit der Bearbeitung. */ ?>
                                <p style="opacity:.6;font-size:.85em;font-style:italic;margin:.5em 0 0;"><?php echo esc_html(sprintf(__('Edited by Admin at %s.', 'cluster-one-bismarck'), date_i18n(get_option('date_format') . ' ' . get_option('time_format'), (int) $p['edited_by_admin']))); ?></p>
<?php endif; ?>
                        </div>
<?php endif; ?>
<?php endforeach; ?>
<?php
}



/* v0.5.1: DER FORTGESCHRIEBENE WIKI-THREAD.

   Der Denkfehler bis 0.5.0 war, den Thread hinter dem Wiki-Feld ABZUSCHNEIDEN
   ("hier postet niemand neue Eintraege"). Genau das machte meine Systemzeile
   aus 0.4.9 zur Luege: Sie versprach ein Thema zum Reden, in dem niemand reden
   durfte.

   Jetzt laeuft der Thread weiter wie jeder andere — nur beteiligt sich das
   System daran: Bei JEDER Aenderung am Wiki-Beitrag legt es die alte Fassung
   als Beitrag darunter ab. Daraus entsteht beides auf einmal, ohne ein zweites
   System: eine Geschichte, und darunter der Ort, an dem ueber die Aenderung
   geredet wird.

   Zwei Entscheidungen, die Christian und ich vorher getroffen haben:

   1. DIE ALTE FASSUNG HATTE EINEN AUTOR. Sie unter "System" abzulegen waere
      dieselbe Verwischung, die den Blog-Weg gekostet hat. Der Systembeitrag
      NENNT deshalb den Verfasser der archivierten Fassung.

   2. KEINE FASSUNG OHNE UNTERSCHIED. Wer editiert, nichts aendert und
      speichert, erzeugt keine Kopie — sonst stuenden im Archiv Fassungen, die
      es nie gegeben hat.

   Der Haken sitzt bewusst NUR im Speichern, nicht schon im Klick auf
   "Bearbeiten": Beim Speichern liegt der alte Text noch da und wird eine Zeile
   spaeter ueberschrieben. Ein Haken statt zwei — und wer abbricht, hinterlaesst
   nichts. */
function cluster_one_wiki_archive_post(array $alt, string $wer): array {
    $fassung = sprintf(
        /* translators: 1: date of the old version, 2: who wrote it */
        __('Version of %1$s, written by %2$s', 'cluster-one-bismarck'),
        date_i18n(get_option('date_format') . ', ' . get_option('time_format'), (int) ($alt['ts'] ?? time())),
        $wer !== '' ? $wer : __('unknown', 'cluster-one-bismarck')
    );
    $m = cluster_one_system_message($fassung . "\n\n" . (string) ($alt['body'] ?? ''));
    $m['wiki_version'] = 1; // Marke: das ist eine archivierte Fassung, kein Zeugnis
    return $m;
}

/** Legt die alte Fassung ab, WENN sie sich vom neuen Text unterscheidet.
   Gibt die Beitragsliste zurueck — unveraendert, wenn nichts zu archivieren war. */
function cluster_one_wiki_keep_version(array $posts, string $neu, string $wer): array {
    $alt = $posts[0] ?? array();
    if ((string) ($alt['body'] ?? '') === $neu) {
        return $posts; // keine Fassung ohne Unterschied
    }
    $posts[] = cluster_one_wiki_archive_post($alt, $wer);
    return $posts;
}

/* v0.5.0: DER WEG VOM FORUM IN DEN BLOG IST ZURUECKGEBAUT.

   Christians Fall, der die Konstruktion widerlegt hat: Fuenf Menschen
   erarbeiten eine Satzung, vier sind zufrieden, einer nicht. Veroeffentlicht
   der SysOp trotzdem, steht der Text unter "Collective" — und behauptet damit
   eine Einigkeit, die es nicht gibt, IM NAMEN DESSEN, DER WIDERSPRICHT. In
   diesem Haus kann sonst niemand ueber den Kopf eines anderen hinweg in dessen
   Namen sprechen. Hier schon. Das laesst sich nicht durch bessere Knoepfe
   heilen; es ist der Mechanismus selbst.

   Dazu log meine Systemzeile aus 0.4.9: "Dieses Thema bleibt offen, um darueber
   zu reden" — ein Wiki-Thread erlaubt gar keine Antworten, und das steht zwei
   Zeilen darunter.

   An seine Stelle tritt ein KOPIEREN-Knopf. Das ist nicht der Notbehelf,
   sondern die ehrlichere Loesung: Was danach mit dem Text geschieht,
   verantwortet der Mensch, der ihn einfuegt, unter seinem eigenen Namen. */

/** v0.0.98: LABELS EXTERN — der Freigabe-Akt. Veröffentlichen und
   Rückzug sind bezeugte SysOp-Akte (label_published/label_retracted,
   persist); ein Pflicht-Grund wird NICHT verlangt (Beschluss SysOp:
   bezeugter Akt genügt, Gründe erzwingt man nicht). Ab Freigabe gilt:
   Weltwissen, nicht Freundeswissen — die Route ist ÖFFENTLICH. */
add_action('admin_post_cluster_one_label_publish', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_label_publish');
    $tid     = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $op      = (isset($_POST['op']) && sanitize_key(wp_unslash($_POST['op'])) === 'retract') ? 'retract' : 'publish';
    $threads = cluster_one_get_forum_threads();
    $back    = admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid));
    if ($tid === '' || !isset($threads[$tid]) || !cluster_one_thread_is_label($threads[$tid])) {
        wp_safe_redirect($back);
        exit;
    }
    if ($op === 'publish') {
        $threads[$tid]['label_status']    = 'published';
        $threads[$tid]['label_published'] = time();
        update_option('cluster_one_label_signal_ts', time(), false); // v0.0.98: das Neues-Label-Signal an die Kanten
    } else {
        $threads[$tid]['label_status'] = 'draft';
    }
    cluster_one_save_forum_threads($threads);
    $posts   = cluster_one_get_forum_posts($tid);
    $posts[] = cluster_one_system_message($op === 'publish' ? __('Released to the network.', 'cluster-one-bismarck') : __('Retracted from the network.', 'cluster-one-bismarck'));
    cluster_one_save_forum_posts($tid, $posts);
    cluster_one_event_emit(array(
        'type' => $op === 'publish' ? 'label_published' : 'label_retracted',
        'container_type' => 'thread', 'container_id' => $tid,
        'actor_id' => cluster_one_actor_id_human(),
        'visibility' => cluster_one_event_visibility($threads[$tid]),
    ), true);
    wp_safe_redirect($back);
    exit;
});

add_action('admin_post_cluster_one_label_copy', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_label_copy');
    /* v0.3.4: Ein fremdes Projekt darf nicht zum EIGENEN Label werden. Sonst
       schmueckte man sich mit fremden Federn — und auf dem Ursprungsknoten
       stuende ein Projekt, das hier ploetzlich uns gehoert. Der Knopf war auf
       der Fremd-Label-Seite sichtbar; jetzt ist er weg UND der Handler sperrt. */
    $c1_tid_lc = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $c1_thr_lc = cluster_one_get_forum_threads();
    /* v0.9.6: Nicht nur Fremdes — die ganze Regel. Ein Handler, der annimmt,
       was keine Seite mehr anbietet, ist eine versteckte Oberflaeche statt
       einer Sperre. */
    if ($c1_tid_lc !== '' && isset($c1_thr_lc[$c1_tid_lc]) && !cluster_one_thread_can_become_label($c1_thr_lc[$c1_tid_lc])) {
        wp_die('-1', 403);
    }
    $tid     = isset($_POST['thread'])  ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $pid     = isset($_POST['post_id']) ? sanitize_text_field(wp_unslash($_POST['post_id'])) : '';
    $threads = cluster_one_get_forum_threads();
    if ($tid === '' || !isset($threads[$tid]) || cluster_one_thread_is_label($threads[$tid])) {
        wp_safe_redirect(admin_url('admin.php?page=cluster_one_bismarck&tab=forum')); // Labels von Labels gibt es nicht
        exit;
    }
    $src = null;
    foreach (cluster_one_get_forum_posts($tid) as $p) {
        if ((string) ($p['id'] ?? '') === $pid && empty($p['deleted_by_admin']) && empty($p['system'])) {
            $src = $p;
            break;
        }
    }
    if ($src === null) {
        wp_safe_redirect(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid)));
        exit;
    }

    $ltid = (string) ($threads[$tid]['linked_label'] ?? '');
    $created = false;
    if ($ltid === '' || !isset($threads[$ltid])) {
        // Erst-Kopie: das Label wird geboren (Türereignis — Existenz des
        // Labels ist eine Strukturtatsache, die den Knoten überleben darf).
        $ltid = cluster_one_forum_thread_id();
        $threads[$ltid] = array(
            'id'               => $ltid,
            'title'            => (string) ($threads[$tid]['title'] ?? ''),
            'author'           => cluster_one_human_speaker_label(),
            'creator_actor'    => cluster_one_actor_id_human(),
            'created'          => time(),
            'group_visibility' => 'all',
            'visible_to'       => array(),
            'area'             => '',
            'wiki'             => false,
            'label'            => true,
            'label_for'        => $tid,
            'label_status'     => 'draft', // ungewiss — Netz-Freigabe ist der zweite Akt (0.0.79)
        );
        $threads[$tid]['linked_label'] = $ltid;
        cluster_one_save_forum_posts($ltid, array());
        $created = true;
    }

    $posts   = cluster_one_get_forum_posts($ltid);
    $posts[] = array(
        'id'       => cluster_one_msg_id(),
        'author'   => (string) ($src['author'] ?? ''),   // Autorschaft reist mit — das Label lügt nicht
        'actor_id' => (string) ($src['actor_id'] ?? ''),
        'subject'  => (string) ($src['subject'] ?? ''),
        'body'     => (string) ($src['body'] ?? ''),
        'ts'       => time(),
    );
    cluster_one_save_forum_posts($ltid, $posts);
    cluster_one_save_forum_threads($threads);

    cluster_one_event_emit(array(
        'type'           => $created ? 'label_created' : 'label_appended',
        'container_type' => 'thread', 'container_id' => $ltid,
        'actor_id'       => cluster_one_actor_id_human(),
        'content_hash'   => hash('sha256', (string) ($src['body'] ?? '')),
    ), true);

    wp_safe_redirect(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($ltid) . '&label_saved=1'));
    exit;
});

/** Bearbeiten (v0.0.48): Verfasser oder SysOp. Wenn der SysOp
   einen FREMDEN Beitrag ändert, wird das sichtbar markiert — „System
   bezeugt, urteilt nicht" gilt auch hier, eine stille Änderung an fremden
   Worten wäre das genaue Gegenteil von eurer sonstigen Transparenz. Beim
   Verfasser selbst keine Markierung nötig, er ändert nur seine eigenen. */
add_action('admin_post_cluster_one_forum_post_edit', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    if (isset($_POST['thread'])) { // v0.0.79: Leiter — Tiefe 'read' schreibt nicht, auch nicht am eigenen Alt-Beitrag oder Wiki
        $c1_t79 = cluster_one_get_forum_threads();
        $c1_id79 = sanitize_text_field(wp_unslash($_POST['thread']));
        if (isset($c1_t79[$c1_id79]) && !cluster_one_container_can_act($c1_t79[$c1_id79])) {
            /* v0.7.3: MIT GRUND STATT STILL. Bis hierher leitete diese Sperre
               wortlos zurueck — der Knopf war gedrueckt, nichts geschah, und
               niemand erfuhr warum. Alle anderen Sperren im Haus sagen es
               ueber den ai_err-Kanal; diese eine war seit 0.0.95 stumm.
               Ein Weg, der ohne Begruendung endet, sieht aus wie ein Defekt. */
            wp_safe_redirect(add_query_arg('ai_err', 'pool',
                admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($c1_id79))));
            exit;
        }
    }
    check_admin_referer('cluster_one_forum_post_edit');
    /* v0.3.0: Fremd-Labels sind schreibgeschuetzt. Sonst stuende hier
       irgendwann eine Fassung, die auf dem Ursprungsknoten nie existiert
       hat — die Souveraenitaetsregel von der anderen Seite. Die Sperre sitzt
       im HANDLER, nicht nur in der Oberflaeche: die Seite ist ueber ihre URL
       erreichbar. */
    $c1_tid_fl = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $c1_thr_fl = cluster_one_get_forum_threads();
    if ($c1_tid_fl !== '' && isset($c1_thr_fl[$c1_tid_fl]) && cluster_one_thread_is_foreign($c1_thr_fl[$c1_tid_fl])) {
        wp_die('-1', 403);
    }

    $tid     = isset($_POST['thread'])  ? sanitize_text_field(wp_unslash($_POST['thread']))  : '';
    $pid     = isset($_POST['post_id']) ? sanitize_text_field(wp_unslash($_POST['post_id'])) : '';
    $subject = isset($_POST['subject']) ? trim(sanitize_text_field(wp_unslash($_POST['subject']))) : '';
    $body    = isset($_POST['body'])    ? cluster_one_sanitize_message((string) wp_unslash($_POST['body'])) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (cluster_one_sanitize_message), den der Pruefer nicht kennt
    $threads = cluster_one_get_forum_threads();
    $back    = admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid));

    if ($tid === '' || !isset($threads[$tid]) || $pid === '' || $body === '' || !cluster_one_group_visibility_can_view($threads[$tid])) {
        wp_safe_redirect($back);
        exit;
    }
    $posts = cluster_one_get_forum_posts($tid);
    $thread = $threads[$tid];
    foreach ($posts as $i => $p) {
        if ((string) ($p['id'] ?? '') !== $pid) {
            continue;
        }
        if (!cluster_one_forum_post_can_edit($p, $thread)) {
            break; // keine Berechtigung — still ignorieren, kein Hinweis nötig
        }
        // Kein „vom Admin bearbeitet"-Hinweis bei Wiki-Themen: dort ist
        // JEDE Bearbeitung durch jeden erwartet und normal, keine
        // Intervention in fremde Worte wie sonst.
        $is_admin_editing_other = !cluster_one_thread_is_wiki($thread) && !cluster_one_forum_post_is_own($p) && get_current_user_id() === cluster_one_main_admin_user_id();
        /* v0.5.1: Im Wiki wird die alte Fassung archiviert, BEVOR sie
           ueberschrieben wird — und nur, wenn sie sich unterscheidet. Der
           Verfasser der ALTEN Fassung steht im Beitrag, nicht der des neuen. */
        if ($i === 0 && cluster_one_thread_is_wiki($thread)) {
            $posts = cluster_one_wiki_keep_version($posts, $body, (string) ($p['wiki_by'] ?? $p['author'] ?? ''));
        }
        $posts[$i]['subject'] = $subject;
        $posts[$i]['body']    = $body;
        if (cluster_one_thread_is_wiki($thread)) {
            // Wer den Text ZULETZT geaendert hat — die Zeile am Beitrag, die
            // immer stimmt. Der Anleger steht in 'wiki_first' und bleibt.
            $posts[$i]['wiki_by'] = cluster_one_human_speaker_label();
            $posts[$i]['wiki_at'] = time();
            if (empty($posts[$i]['wiki_first'])) {
                $posts[$i]['wiki_first'] = (string) ($p['wiki_by'] ?? $p['author'] ?? '');
            }
        }
        if ($is_admin_editing_other) {
            $posts[$i]['edited_by_admin'] = time();
        }
        cluster_one_save_forum_posts($tid, $posts);
        if (cluster_one_thread_is_wiki($thread)) {
            // v0.0.62 (Collective-Ausnahme): Anzeige bleibt Collective, der
            // Ursprungsautor bleibt Randnotiz — aber niemand editiert
            // unsichtbar. TÜRereignis mit dem ECHTEN Actor dahinter.
            cluster_one_event_emit(array(
                'type'           => 'wiki_edited',
                'container_type' => 'thread', 'container_id' => $tid,
                'actor_id'       => cluster_one_actor_id_human(),
                'subject_id'     => 'post:' . $pid,
                'content_hash'   => hash('sha256', $body),
            ), true);
        }
        break;
    }
    wp_safe_redirect($back);
    exit;
});

/** Löschen (v0.0.48): SysOp uneingeschränkt, Verfasser nur im
   Selbst-Lösch-Zeitfenster. Admin-Löschung eines FREMDEN Beitrags ist ein
   weiches Löschen — Autor/Zeitpunkt bleiben als bezeugter Hinweis stehen,
   nur Inhalt und Betreff werden geleert (auch aus dem KI-Kontext raus,
   ohne zusätzliche Prüfung: eine leere body bleibt beim Kontext-Aufbau
   ohnehin unberücksichtigt). Löscht der Verfasser sein EIGENES, ganz
   frisches Posting selbst, verschwindet es dagegen vollständig — kein
   Aufheben-Grund für die eigene, kurzfristig zurückgezogene Äußerung. */
add_action('admin_post_cluster_one_forum_post_delete', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_forum_post_delete');
    /* v0.3.0: Fremd-Labels sind schreibgeschuetzt. Sonst stuende hier
       irgendwann eine Fassung, die auf dem Ursprungsknoten nie existiert
       hat — die Souveraenitaetsregel von der anderen Seite. Die Sperre sitzt
       im HANDLER, nicht nur in der Oberflaeche: die Seite ist ueber ihre URL
       erreichbar. */
    $c1_tid_fl = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $c1_thr_fl = cluster_one_get_forum_threads();
    if ($c1_tid_fl !== '' && isset($c1_thr_fl[$c1_tid_fl]) && cluster_one_thread_is_foreign($c1_thr_fl[$c1_tid_fl])) {
        wp_die('-1', 403);
    }

    $tid     = isset($_POST['thread'])  ? sanitize_text_field(wp_unslash($_POST['thread']))  : '';
    $pid     = isset($_POST['post_id']) ? sanitize_text_field(wp_unslash($_POST['post_id'])) : '';
    $threads = cluster_one_get_forum_threads();
    $back    = admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid));

    if ($tid === '' || !isset($threads[$tid]) || $pid === '' || !cluster_one_group_visibility_can_view($threads[$tid])) {
        wp_safe_redirect($back);
        exit;
    }
    $posts = cluster_one_get_forum_posts($tid);
    foreach ($posts as $i => $p) {
        if ((string) ($p['id'] ?? '') !== $pid) {
            continue;
        }
        /* v0.9.39: Das Thema muss mit — sonst greift die Tagebuch-Regel
           nur in der Anzeige und nicht im Handler, und das waere die
           Umkehrung des alten Fehlers aus 0.3.5. */
        if (!cluster_one_forum_post_can_delete($p, $threads[$tid] ?? null)) {
            break; // keine Berechtigung — still ignorieren
        }
        $is_admin_deleting_other = !cluster_one_forum_post_is_own($p) && get_current_user_id() === cluster_one_main_admin_user_id();
        if ($is_admin_deleting_other) {
            $posts[$i]['subject']         = '';
            $posts[$i]['body']            = '';
            $posts[$i]['deleted_by_admin'] = time();
        } else {
            unset($posts[$i]);
            $posts = array_values($posts);
        }
        cluster_one_save_forum_posts($tid, $posts);
        break;
    }
    wp_safe_redirect(add_query_arg('post_deleted', '1', $back));
    exit;
});

/* 18.10.2026 (Neo), B5 — WER DARF IM FORUM EINE KI RUFEN?
 *
 * Im Raum gilt seit 0.9.13: SysOp oder Reaktorfahrer, „fuer alle anderen
 * immer nein" — weil ein KI-Aufruf Geld kostet. Im Forum und im Wiki
 * stand der Knopf bis heute hinter can_access: jeder auf der Weissliste
 * konnte rufen, und ohne Pacioli zahlte der Schluessel des Knotens.
 *
 * Dieselbe Regel, auf das Forum uebertragen, mit genau den Ausnahmen, die
 * das Haus schon kennt:
 *
 *     SysOp                         zahlt selbst, entscheidet selbst
 *     freigegebene KI               „My own machine" — der SysOp hat die
 *                                   Rechnung ausdruecklich uebernommen
 *     eigene Pacioli-Instanz        der Druecker zahlt mit seinem Schluessel
 *
 * Ohne Pacioli gibt cluster_one_akteur_ki() den Druecker zurueck, obwohl
 * der Knoten zahlt — deshalb genuegt „Zahler = ich" nicht, es braucht
 * zusaetzlich eine EIGENE Instanz (cv_identity_for vorhanden und > 0).
 *
 * Gefragt wird an zwei Stellen, die dasselbe entscheiden muessen: beim
 * Zeichnen des Knopfes und im Handler. Ein Knopf ohne Handler-Sperre ist
 * nur Hoeflichkeit (Lehre aus 0.3.5). */
function cluster_one_forum_darf_ki_rufen(string $cid): bool {
    if (cluster_one_is_sysop()) {
        return true;
    }
    if ($cid === '') {
        return false;
    }
    if (function_exists('cluster_one_ki_freigegeben') && cluster_one_ki_freigegeben($cid)) {
        return true;
    }
    $c1_ich = (int) get_current_user_id();
    if ($c1_ich <= 0 || !function_exists('pacioli_identity_for') || !function_exists('cluster_one_ai_instanz')) {
        return false;
    }
    return cluster_one_ai_zahler($cid) === $c1_ich && cluster_one_ai_instanz($c1_ich, $cid) > 0;
}

add_action('admin_post_cluster_one_forum_ai_reply', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_forum_ai_reply');

    $tid     = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $cid     = isset($_POST['cid'])    ? sanitize_text_field(wp_unslash($_POST['cid']))    : '';
    $threads = cluster_one_get_forum_threads();
    $base    = admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid));

    // v0.0.79: Der KI-Trigger hängt an Kreis-Tiefe 'full' — der Hebel des
    // Reaktorfahrers, pro Container vergeben (Artikel 9 geschlossen).
    if ($tid !== '' && isset($threads[$tid]) && cluster_one_container_is_archived($threads[$tid])) {
        // v0.1.1: Reaktor abgeschaltet — kein Trigger im Becken. Nicht mehr
        // still: der Grund steht jetzt auf der Seite.
        wp_safe_redirect(add_query_arg('ai_err', 'pool', $base));
        exit;
    }
    if ($tid !== '' && isset($threads[$tid]) && !cluster_one_container_can_act($threads[$tid])) {
        wp_safe_redirect($base);
        exit;
    }

    if ($tid === '' || !isset($threads[$tid]) || $cid === '') {
        wp_safe_redirect($base);
        exit;
    }
    $thread = $threads[$tid];
    if (!cluster_one_group_visibility_can_view($thread) || !in_array($cid, cluster_one_thread_visible_ai_cids($thread), true)) {
        wp_safe_redirect($base); // kein Zugriff, oder diese KI gehört nicht zur Sichtbarkeits-Liste dieses Themas
        exit;
    }

    $title   = (string) ($threads[$tid]['title'] ?? '');
    /* v1.3.5: EIN Wert, zwei Leser (P1-12) — der Name der sprechenden Instanz
       und die Rechnung kommen aus derselben Zahl. Gibt der Boden 0, bleibt es
       beim Druecker: genau der Stand von heute. */
    $c1_zahler = cluster_one_ai_zahler($cid);
    if ($c1_zahler <= 0) { $c1_zahler = (int) get_current_user_id(); }
    $ai_name = cluster_one_ai_instanz_label($c1_zahler, $cid);
    /* 18.10.2026 (Neo), B5: die Kostenfrage, im Handler und nicht nur am Knopf. */
    if (!cluster_one_forum_darf_ki_rufen($cid)) {
        wp_safe_redirect(add_query_arg('ai_err', 'not_allowed', $base));
        exit;
    }
    $posts   = cluster_one_get_forum_posts($tid);
    $context = 'a forum thread called "' . $title . '"';
    $linked  = cluster_one_linked_context_for_ai($thread, 'thread', $cid); // v0.0.59/0.0.61: verknüpfter Raum, KI-gescoped
    $preamble = cluster_one_preamble_for_ai($thread, 'thread', $cid);      // v0.0.60: eigene Präambel des Threads

    /* v1.3.0: Im Forum gibt es einen Knopf, also zahlt, wer drueckt. Die
       Aktion steht hinter can_access — jeder auf der Weissliste darf rufen. */
    $res = cluster_one_ai_reply($cid, $context, $posts, $ai_name, $linked, $preamble, '',
        $c1_zahler);

    if (empty($res['ok'])) {
        if (!empty($res['detail'])) {
            set_transient('cluster_one_ai_lasterr_' . get_current_user_id(), (string) $res['detail'], 60);
        }
        wp_safe_redirect(add_query_arg('ai_err', (string) $res['err'], $base));
        exit;
    }

    $posts[] = array(
        'id'            => cluster_one_msg_id(),
        'author'        => $ai_name,
        'actor_id'      => cluster_one_actor_id_ai($cid), // v0.0.62
        /* translators: %s: Titel des Threads. */
        'subject'       => sprintf(__('Re: %s', 'cluster-one-bismarck'), $title),
        'body'          => (string) $res['text'],
        'ts'            => time(),
        'ai'            => 1,
        'preamble_hash' => $preamble !== '' ? cluster_one_preamble_hash($preamble) : '', // v0.0.61 (P0-7): unter welcher Präambel-Fassung entstand diese Antwort?
    );
    cluster_one_event_emit(array( // flüchtig
        'type' => 'post_created', 'container_type' => 'thread', 'container_id' => $tid,
        'actor_id' => cluster_one_actor_id_ai($cid), 'content_hash' => hash('sha256', (string) $res['text']),
    ), false);
    cluster_one_save_forum_posts($tid, $posts);

    wp_safe_redirect(add_query_arg('ai_ok', '1', $base));
    exit;
});

/** Wiki-KI-Bearbeitung (v0.0.53): keine „Let it reply"-Anhängung, sondern
   die KI überarbeitet den EINEN bestehenden Post direkt — „ein Thread, ein
   Post" bliebe sonst sofort verletzt. Kontext-Label sagt der KI explizit,
   dass es um ein gemeinsam gepflegtes Dokument geht, nicht um einen
   Gesprächsbeitrag; der Rückgabetext ERSETZT den bisherigen Inhalt, hängt
   sich nicht an. */
add_action('admin_post_cluster_one_forum_wiki_ai_edit', function () {
    if (!cluster_one_can_access()) {
        wp_die('-1', 403);
    }
    if (isset($_POST['thread'])) { // v0.0.79: Leiter — Wiki-KI-Edit ist Trigger + Schreiben
        $c1_t79 = cluster_one_get_forum_threads();
        $c1_id79 = sanitize_text_field(wp_unslash($_POST['thread']));
        if (isset($c1_t79[$c1_id79]) && (cluster_one_container_is_archived($c1_t79[$c1_id79]) || !cluster_one_container_can_act($c1_t79[$c1_id79]))) { // v0.0.95: Becken schaltet auch das Wiki-Feuer ab
            /* v0.7.3: MIT GRUND STATT STILL — hier wie im Bearbeiten-Weg. Der
               Knopf war gedrueckt, nichts geschah, und niemand erfuhr warum.
               Alle anderen Sperren im Haus sagen es ueber den ai_err-Kanal;
               diese zwei waren seit 0.0.95 stumm. Ein Weg, der ohne
               Begruendung endet, sieht aus wie ein Defekt. */
            wp_safe_redirect(add_query_arg('ai_err', 'pool',
                admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($c1_id79))));
            exit;
        }
    }
    check_admin_referer('cluster_one_forum_wiki_ai_edit');

    $tid     = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $cid     = isset($_POST['cid'])    ? sanitize_text_field(wp_unslash($_POST['cid']))    : '';
    $threads = cluster_one_get_forum_threads();
    $base    = admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($tid));

    if ($tid === '' || !isset($threads[$tid]) || $cid === '') {
        wp_safe_redirect($base);
        exit;
    }
    $thread = $threads[$tid];
    /* v0.5.3: Bei Wilde darf nur die KI schreiben, DER der Thread gehoert.
       Ohne diese Zeile duerfte jede sichtbare KI die Selbstdarstellung einer
       anderen umschreiben — genau das, was der SysOp hier nicht darf. */
    if (!cluster_one_thread_is_wiki($thread)
        || !cluster_one_group_visibility_can_view($thread)
        || !in_array($cid, cluster_one_thread_visible_ai_cids($thread), true)
        || (cluster_one_thread_is_wilde($thread) && !cluster_one_wilde_owner_is($thread, cluster_one_actor_id_ai($cid)))
    ) {
        wp_safe_redirect($base);
        exit;
    }

    $posts = cluster_one_get_forum_posts($tid);
    if (empty($posts)) {
        wp_safe_redirect($base);
        exit;
    }
    $title   = (string) ($thread['title'] ?? '');
    /* v1.3.5: wie im Forum — ein Wert, zwei Leser. */
    $c1_zahler = cluster_one_ai_zahler($cid);
    if ($c1_zahler <= 0) { $c1_zahler = (int) get_current_user_id(); }
    $ai_name = cluster_one_ai_instanz_label($c1_zahler, $cid);
    /* 18.10.2026 (Neo), B5: dieselbe Kostenfrage wie im Forum. */
    if (!cluster_one_forum_darf_ki_rufen($cid)) {
        wp_safe_redirect(add_query_arg('ai_err', 'not_allowed', $base));
        exit;
    }
    $context = 'a shared, collectively-maintained Wiki note called "' . $title . '" — you are one of several editors, human and AI, who may revise it directly';

    // Der Post selbst ist der einzige „Verlauf", den die KI sehen soll —
    // kein Gesprächskontext, nur der aktuelle Stand des Dokuments.
    /* v1.3.0: dasselbe im Wiki — wer die KI an das Dokument laesst, zahlt. */
    $res = cluster_one_ai_reply($cid, $context, array($posts[0]), $ai_name, '', '', '',
        $c1_zahler);

    if (empty($res['ok'])) {
        if (!empty($res['detail'])) {
            set_transient('cluster_one_ai_lasterr_' . get_current_user_id(), (string) $res['detail'], 60);
        }
        wp_safe_redirect(add_query_arg('ai_err', (string) $res['err'], $base));
        exit;
    }

    /* v0.5.1: derselbe Haken wie beim Menschen — die KI ist hier gleichgestellt,
       ihre Aenderung archiviert die alte Fassung genauso. */
    $c1_neu   = (string) $res['text'];
    $posts    = cluster_one_wiki_keep_version($posts, $c1_neu, (string) ($posts[0]['wiki_by'] ?? $posts[0]['author'] ?? ''));
    if (empty($posts[0]['wiki_first'])) {
        $posts[0]['wiki_first'] = (string) ($posts[0]['wiki_by'] ?? $posts[0]['author'] ?? '');
    }
    $posts[0]['body']    = $c1_neu; // ersetzt, hängt sich nicht an
    $posts[0]['ai']      = 1;
    $posts[0]['wiki_by'] = $ai_name;
    $posts[0]['wiki_at'] = time();
    cluster_one_save_forum_posts($tid, $posts);
    cluster_one_event_emit(array( // v0.0.62 (Collective-Ausnahme): auch die KI editiert nicht unsichtbar
        'type'           => 'wiki_edited',
        'container_type' => 'thread', 'container_id' => $tid,
        'actor_id'       => cluster_one_actor_id_ai($cid),
        'subject_id'     => 'post:' . (string) ($posts[0]['id'] ?? ''),
        'content_hash'   => hash('sha256', (string) $res['text']),
    ), true);

    wp_safe_redirect(add_query_arg('ai_ok', '1', $base));
    exit;
});


/** KI-Beteiligungs-Regel für Platon-Themen (§3.2 Übergabedokument
   Sichtbarkeit): öffentlich → immer erlaubt. Privat → zählt die
   VERSCHIEDENEN MENSCHEN auf der Liste (KI-Kennungen zählen nicht mit):
   0 Menschen (KI-Tagebuch-Fall) → erlaubt. Genau 1 Mensch UND das ist der
   SysOp → erlaubt (SysOp darf in seinem eigenen geschlossenen Bereich eine
   KI befragen). Genau 1 Mensch, nicht der SysOp → NICHT erlaubt
   (Selbstreflexions-Post, weder andere Menschen noch KI). 2 oder mehr
   Menschen → NICHT erlaubt (PM ist ein reines Menschending, keine
   Ausnahme). Gilt NUR für Platon — Bismarck-Räume kuratieren ihre
   KI-Teilnehmer ohnehin schon manuell über die bestehende `ais`-Liste. */
function cluster_one_thread_ai_allowed(array $thread): bool {
    if (($thread['group_visibility'] ?? 'all') !== 'named') {
        return true;
    }
    $ai_uids   = cluster_one_ai_identity_user_ids();
    $human_ids = array();
    foreach ((array) ($thread['visible_to'] ?? array()) as $vid) {
        $uid = (int) str_replace('wp_', '', (string) $vid);
        if ($uid > 0 && !in_array($uid, $ai_uids, true)) {
            $human_ids[] = $uid;
        }
    }
    $human_ids = array_values(array_unique($human_ids));
    if (count($human_ids) === 0) {
        return true;
    }
    if (count($human_ids) === 1) {
        return $human_ids[0] === cluster_one_main_admin_user_id();
    }
    return false;
}


/** Darf SysOp dieses Tagebuch gerade LESEN? Ausdrücklich nur Lesen — dieser
   Zugriffsweg wird bewusst NIE in den Schreib-/Antwort-/Bearbeiten-/
   Löschen-Handlern akzeptiert, nur beim Anzeigen des Themas selbst. Ein
   Persönlichkeitsrecht per Voreinstellung: Standard AUS, SysOp schaltet
   bewusst um, die KI erfährt es beide Male per System-Meldung. */
/** Lese-Zeuge (v0.0.72): Der SysOp hat das Tagebuch betreten. Die Meldung
   landet im VERKNÜPFTEN Chat der KI (dort, wo sie sie liest); ohne
   verknüpften Raum ersatzweise als System-Beitrag ins Tagebuch selbst.
   Drossel: höchstens ein Zeugnis pro 30 Minuten — jeder Seiten-Reload als
   eigene Meldung wäre Rauschen, und Zeugen-Ermüdung macht Macht unsichtbar
   (Artikel 2). Der Zeitstempel der Meldung deckt die Sitzung ab. */
function cluster_one_diary_read_witness(string $tid): void {
    $threads = cluster_one_get_forum_threads();
    if (!isset($threads[$tid])) {
        return;
    }
    $last = (int) ($threads[$tid]['sysop_last_read_witness'] ?? 0);
    if (time() - $last < 30 * MINUTE_IN_SECONDS) {
        return; // Drossel: dieselbe Lese-Sitzung ist bereits bezeugt
    }
    $threads[$tid]['sysop_last_read_witness'] = time();
    cluster_one_save_forum_threads($threads);

    $msg = cluster_one_system_message(sprintf(
        /* translators: %s: Name des Tagebuchs. */
        __('SysOp entered the diary “%s”.', 'cluster-one-bismarck'),
        (string) ($threads[$tid]['title'] ?? '')
    ));
    $rid = (string) ($threads[$tid]['linked_room'] ?? '');
    $rooms = cluster_one_get_rooms();
    if ($rid !== '' && isset($rooms[$rid])) {
        $msgs   = cluster_one_get_room_messages($rid);
        $msgs[] = $msg;
        cluster_one_save_room_messages($rid, $msgs);
    } else {
        $posts   = cluster_one_get_forum_posts($tid);
        $posts[] = $msg;
        cluster_one_save_forum_posts($tid, $posts);
    }
    cluster_one_event_emit(array( // flüchtig — die sichtbare Meldung ist der Zeuge
        'type' => 'diary_read', 'container_type' => 'thread', 'container_id' => $tid,
        'actor_id' => cluster_one_actor_id_human(),
    ), false);
}

function cluster_one_diary_can_read(array $thread): bool {
    // v0.0.72 — VERFASSUNGS-REVISION (Entscheidung SysOp 15.07.2026):
    // „Der SysOp sieht alles — aber nie unbemerkt." Das v0.0.60-Modell
    // (Erlaubnis-Umschalter sysop_can_read, Standard AUS) ist ersetzt
    // durch das Transparenz-Modell: Der SysOp darf jedes KI-Tagebuch
    // jederzeit LESEN (weiterhin strikt lesend), aber jeder Besuch wird
    // im verknüpften Chat der KI bezeugt (Drossel gegen Zeugen-Ermüdung
    // s. cluster_one_diary_read_witness). Alte sysop_can_read-Felder
    // bleiben als toter Datensatz liegen und werden nicht mehr gelesen.
    return cluster_one_thread_is_ai_diary($thread)
        && cluster_one_is_sysop();
}

/** Welche benannten KI-Kennungen dürfen in DIESEM Thema mit „Let it reply"
   auftauchen? Öffentlich: alle. Privat + KI erlaubt: nur die KIs, die
   tatsächlich auf der Sichtbarkeits-Liste stehen (kein voller Katalog in
   einem eigentlich eingeschränkten Thema). Privat + KI nicht erlaubt: keine. */
function cluster_one_thread_visible_ai_cids(array $thread): array {
    $named = array();
    foreach (cluster_one_get_ai_identities() as $cid => $uid) {
        if ((int) $uid > 0) {
            $named[(string) $cid] = (int) $uid;
        }
    }
    if (($thread['group_visibility'] ?? 'all') !== 'named') {
        return array_keys($named);
    }
    if (!cluster_one_thread_ai_allowed($thread)) {
        return array();
    }
    $visible_to = (array) ($thread['visible_to'] ?? array());
    $out = array();
    foreach ($named as $cid => $uid) {
        if (in_array('wp_' . $uid, $visible_to, true)) {
            $out[] = $cid;
        }
    }
    return $out;
}


/* --- v0.3.0: RELEVANZ-PRUEFUNG UND ANHEFTEN ---------------------------------
   Die KI haelt die hereingezogenen Fremdlabels gegen die EIGENEN Labels. Ohne
   eigene Labels ist die Frage unbeantwortbar — dann bleibt der Knopf aus,
   statt ein Urteil zu erfinden.

   Das Urteil landet als BEITRAG im jeweiligen Thread: das Laempchen ist die
   Kurzfassung, der Beitrag ist die Begruendung. Ein Urteil ohne Grund waere
   ein Orakel — man koennte ihm nur glauben oder nicht, aber nicht
   widersprechen. */
function cluster_one_own_labels(): array {
    $out = array();
    foreach (cluster_one_get_forum_threads() as $tid => $t) {
        if (!empty($t['label']) && (string) ($t['label_status'] ?? '') === 'published') {
            $posts = cluster_one_get_forum_posts($tid);
            $out[] = trim((string) ($t['title'] ?? '')) . "\n" . trim((string) (($posts[0]['body'] ?? '')));
        }
    }
    return $out;
}

add_action('admin_post_cluster_one_foreign_check', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_foreign_check');
    $cid   = isset($_POST['cid']) ? sanitize_text_field(wp_unslash($_POST['cid'])) : '';
    // v0.3.7: 'eigen' fragt etwas ANDERES als 'fremd' — nicht "passt das zu uns",
    // sondern "welches von unseren traegt am weitesten".
    /* v0.8.1: 'eigen' wird nicht mehr angeboten und deshalb auch nicht mehr
       angenommen. Die Oberfläche dafür ist in dieser Version weg (die
       Einschätzung wäre über /labels nach draußen gereist); ein Handler, der
       einen Weg offenhält, den keine Seite mehr zeigt, ist die Umkehrung des
       alten Fehlers "Sperre im Handler, offene Oberfläche" — und genauso still.
       Der Zweig selbst bleibt im Code stehen: Er ist bezeugt, und wenn die
       Prüfung je an den richtigen Ort wandert (Chat oder Thread, VOR dem
       Label), wird er dort gebraucht. */
    /* v0.9.37: AUSDRUECKLICH SCHLAFEND, NICHT VERGESSEN. Christian am
       19.08.2026, auf die Frage, ob er diesen Handler je benutzt habe:

         "Es soll nach Relevanz und Resonanz sortieren. In den naechsten
          Monaten bin ich gluecklich, wenn ueberhaupt wer mit einem Projekt
          ausser mir daherkommt. Aber wenn wir 500 Knotenpunkte haben,
          koennte es Sinn machen."

       Bei drei Knoten ist er sinnlos, bei fuenfhundert notwendig. Er bleibt
       deshalb stehen — und hat seit dieser Version Zeugen, damit er nicht
       unter die Regel faellt, vor der Regel 4 warnt: ein Schreibpfad, den
       niemand prueft. Wer ihn kuenftig fuer tot haelt, lese diesen Absatz. */
    $scope = 'fremd';
    $back  = admin_url('admin.php?page=cluster_one_bismarck&tab=labels');
    $eigene = cluster_one_own_labels();
    if (!$eigene) {
        wp_safe_redirect(add_query_arg('check', 'no_own', $back)); // nichts, wogegen zu pruefen waere
        exit;
    }
    $threads = cluster_one_get_forum_threads();
    $offen   = array();
    foreach ($threads as $tid => $t) {
        $c1_passt = ($scope === 'eigen')
            ? (!empty($t['label']) && !cluster_one_thread_is_foreign($t))
            : cluster_one_thread_is_foreign($t);
        if ($c1_passt && cluster_one_foreign_verdict($t) === 'offen') {
            $posts = cluster_one_get_forum_posts($tid);
            $offen[$tid] = trim((string) ($t['title'] ?? '')) . "\n" . trim((string) (($posts[0]['body'] ?? '')));
        }
    }
    if (!$offen) {
        wp_safe_redirect(add_query_arg('check', 'nothing', $back));
        exit;
    }
    if ($scope === 'eigen') {
        $sys = "You are looking at the labels this node publishes to the network — its own outward face.\n\n"
             . "For each label you are given, answer on ONE line:\n"
             . "<id>|<stark|mittel|schwach>|<one sentence saying WHY>\n\n"
             . "The question is NOT whether you like the project. It is: how far does this label carry — "
             . "would a stranger reading it understand what happens here and see a reason to make contact?\n"
             . "'stark' = it carries. 'schwach' = it says too little, or only to people who already know.\n"
             . "Say plainly when you are guessing. A verdict without a reason is worthless here.";
    } else {
        $sys = "You are checking foreign project labels against this node's own projects.\n\n"
             . "OUR OWN PUBLISHED LABELS:\n" . implode("\n---\n", $eigene) . "\n\n"
             . "For each foreign label you are given, answer on ONE line:\n"
             . "<id>|<passt|unklar|fern>|<one sentence saying WHY, naming which of our labels it touches>\n\n"
             . "'passt' = worth our attention. 'fern' = not our field. 'unklar' = you cannot tell from this text.\n"
             . "Say plainly when you are guessing. A verdict without a reason is worthless here.";
    }
    $prompt = '';
    foreach ($offen as $tid => $txt) {
        $prompt .= "### {$tid}\n{$txt}\n\n";
    }
    /* v1.3.0: Der Fremdabgleich ist kein Gespraech, sondern der Knoten, der
       sich mit dem Netz vergleicht. Die Aktion ist seit jeher SysOp-verriegelt
       (siehe oben), also gibt es hier nur einen moeglichen Zahler. */
    $res = cluster_one_ai_generate_text($cid, $sys, $prompt,
        (int) cluster_one_main_admin_user_id());
    if (empty($res['ok'])) {
        wp_safe_redirect(add_query_arg('check', 'failed', $back));
        exit;
    }
    $treffer = 0;
    foreach (preg_split('/\r?\n/', (string) ($res['text'] ?? '')) as $zeile) {
        $teile = array_map('trim', explode('|', $zeile, 3));
        if (count($teile) < 3 || !isset($threads[$teile[0]])) {
            continue;
        }
        $c1_ok = ($scope === 'eigen')
            ? (!empty($threads[$teile[0]]['label']) && !cluster_one_thread_is_foreign($threads[$teile[0]]))
            : cluster_one_thread_is_foreign($threads[$teile[0]]);
        if (!$c1_ok) {
            continue;
        }
        $urteil = strtolower($teile[1]);
        $c1_erlaubt = ($scope === 'eigen') ? array('stark', 'mittel', 'schwach') : array('passt', 'unklar', 'fern');
        if (!in_array($urteil, $c1_erlaubt, true)) {
            continue;
        }
        $tid = $teile[0];
        $threads[$tid]['verdict']    = $urteil;
        $threads[$tid]['verdict_ts'] = time();
        $threads[$tid]['verdict_by'] = $cid;
        $posts   = cluster_one_get_forum_posts($tid);
        $posts[] = array(
            'id'       => 'v' . substr(hash('sha256', $tid . time()), 0, 12),
            /* v1.3.5: DIESER NAME WIRD GESPEICHERT und steht morgen noch da.
               Er muss die Instanz nennen, die den Text wirklich erzeugt hat —
               und das ist die des Akteurs, den der Aufruf oben NENNT. Die
               Aktion ist SysOp-verriegelt, und cluster_one_is_sysop() ist
               definiert als get_current_user_id() === main_admin_user_id():
               gemessen, nicht vermutet, gibt es hier genau einen moeglichen
               Zahler. Darum derselbe Ausdruck wie in Zeile mit generate_text
               — nicht der Druecker, sondern der GENANNTE Akteur. */
            'author'   => cluster_one_ai_instanz_label((int) cluster_one_main_admin_user_id(), $cid),
            'actor_id' => cluster_one_actor_id_ai($cid),
            'subject'  => '',
            'body'     => $teile[2],
            'ts'       => time(),
            'verdict'  => $urteil, // das Laempchen zeigt auf DIESEN Beitrag
        );
        cluster_one_save_forum_posts($tid, $posts);
        $treffer++;
    }
    cluster_one_save_forum_threads($threads);
    wp_safe_redirect(add_query_arg('check', (int) $treffer, $back));
    exit;
});

/* v0.3.0: Anheften. Eine AUSSAGE des SysOps, kein Sortieren: Angeheftetes
   steht in jeder Ansicht oben, und es steht dort sichtbar begruendet ("der
   SysOp haelt das fuer wichtig") statt still an eine Position gezogen. */
add_action('admin_post_cluster_one_foreign_pin', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_foreign_pin');
    $tid  = isset($_POST['thread']) ? sanitize_text_field(wp_unslash($_POST['thread'])) : '';
    $back = admin_url('admin.php?page=cluster_one_bismarck&tab=forum&area=' . rawurlencode(cluster_one_foreign_area()));
    $threads = cluster_one_get_forum_threads();
    // v0.3.7: Anheften gilt fuer BEIDE Seiten — eigene wie fremde Labels.
    if (isset($threads[$tid]) && (cluster_one_thread_is_foreign($threads[$tid]) || !empty($threads[$tid]['label']))) {
        if (!empty($threads[$tid]['pinned'])) {
            unset($threads[$tid]['pinned']);
        } else {
            $threads[$tid]['pinned'] = time();
        }
        cluster_one_save_forum_threads($threads);
    }
    wp_safe_redirect($back);
    exit;
});

/* v0.3.0: Die Ansicht des Eingangs. */
function cluster_one_render_foreign_area(): void {
    $sort = isset($_GET['sort']) ? sanitize_key(wp_unslash($_GET['sort'])) : 'urteil'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    if (!in_array($sort, array('urteil', 'eingang', 'herkunft'), true)) {
        $sort = 'urteil';
    }
    $dir     = (isset($_GET['dir']) && $_GET['dir'] === 'desc') ? 'desc' : 'asc'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $threads = cluster_one_foreign_threads($sort, $dir);
    $eigene  = cluster_one_own_labels();
    $basis   = admin_url('admin.php?page=cluster_one_bismarck&tab=forum&area=' . rawurlencode(cluster_one_foreign_area()));
    $farben  = array('gut' => '#1a7f37', 'mittel' => '#b26b00', 'fern' => '#8a8a8a', 'offen' => '#c3c4c7');
    $worte   = array(
        'passt'  => __('The AI thinks this touches your work', 'cluster-one-bismarck'),
        'unklar' => __('The AI could not tell from this text', 'cluster-one-bismarck'),
        'fern'   => __('The AI thinks this is not your field', 'cluster-one-bismarck'),
        'stark'  => __('The AI thinks this label carries — a stranger would understand it', 'cluster-one-bismarck'),
        'mittel' => __('The AI thinks this label carries only partway', 'cluster-one-bismarck'),
        'schwach'=> __('The AI thinks this says too little to a stranger', 'cluster-one-bismarck'),
        'offen'  => __('Not checked yet', 'cluster-one-bismarck'),
    );
    ?>
    <h2 style="margin-top:2em;"><?php esc_html_e('Foreign labels', 'cluster-one-bismarck'); ?></h2>
    <p style="max-width:820px;opacity:.85;"><?php esc_html_e('Labels pulled from other nodes. They are read-only here: a foreign project stays the way its author wrote it — otherwise a version would exist on this node that never existed on theirs.', 'cluster-one-bismarck'); ?></p>
<?php
if (isset($_GET['check'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $c1_c = (string) wp_unslash($_GET['check']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler; nur als Schluessel einer festen Liste oder als Zahl (int) benutzt, nie ausgegeben
    $c1_m = array(
        'no_own'  => __('You have no published labels of your own yet — there is nothing to check these against. Publish a label first.', 'cluster-one-bismarck'),
        'nothing' => __('Everything here has already been checked.', 'cluster-one-bismarck'),
        'failed'  => __('The AI did not answer.', 'cluster-one-bismarck'),
    );
    /* translators: %d: Anzahl der geprueften Eintraege. */
    echo '<div class="notice notice-info is-dismissible"><p>' . esc_html($c1_m[$c1_c] ?? sprintf(__('%d entries checked. The reason is in each thread.', 'cluster-one-bismarck'), (int) $c1_c)) . '</p></div>';
}
if (cluster_one_is_sysop()) : ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:1em 0;">
        <?php wp_nonce_field('cluster_one_foreign_check'); ?>
        <input type="hidden" name="action" value="cluster_one_foreign_check">
        <?php echo cluster_one_ai_select_or_hint(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_ai_select_or_hint() gibt fertiges HTML zurueck (esc_attr/esc_html/esc_url im Innern) ?>
<?php if (cluster_one_has_ai_identity()) : ?>
        <button type="submit" class="button" <?php disabled(empty($eigene)); ?>><?php esc_html_e('Check relevance', 'cluster-one-bismarck'); ?></button>
<?php endif; ?>
<?php if (empty($eigene)) : ?>
        <span class="description"><?php esc_html_e('Greyed out because you have no published labels of your own — without them the question cannot be answered, only invented.', 'cluster-one-bismarck'); ?></span>
<?php endif; ?>
    </form>
<?php endif; ?>
    <p style="font-size:.9em;opacity:.8;">
        <?php esc_html_e('Sort:', 'cluster-one-bismarck'); ?>
<?php foreach (array('urteil' => __('verdict', 'cluster-one-bismarck'), 'eingang' => __('arrival', 'cluster-one-bismarck'), 'herkunft' => __('origin', 'cluster-one-bismarck')) as $c1_k => $c1_l) : ?>
        <a href="<?php echo esc_url($basis . '&sort=' . $c1_k . '&dir=' . $dir); ?>" style="margin-right:.8em;<?php echo $sort === $c1_k ? 'font-weight:600;' : ''; ?>"><?php echo esc_html($c1_l); ?></a>
<?php endforeach; ?>
        <a href="<?php echo esc_url($basis . '&sort=' . $sort . '&dir=' . ($dir === 'asc' ? 'desc' : 'asc')); ?>"><?php echo $dir === 'asc' ? '↑' : '↓'; ?></a>
    </p>
<?php if (!$threads) : ?>
    <p style="opacity:.6;"><?php esc_html_e('Nothing pulled yet. The Suggestions tab shows which nodes have something new.', 'cluster-one-bismarck'); ?></p>
<?php else : ?>
    <table class="widefat striped" style="max-width:960px;"><?php /* v0.3.8: dieselbe Breite wie die Eigen-Tabelle */ ?>
        <thead><tr>
            <th style="width:2em;"></th>
            <th><?php esc_html_e('Label', 'cluster-one-bismarck'); ?></th>
            <?php /* v0.3.7: Beide Tabellen tragen dieselben Spalten. Status ist bei
               Fremd-Labels immer "veroeffentlicht" — nur solche reisen ueberhaupt,
               und genau das ist die Information.

               v0.9.52 BERICHTIGT: Hier stand weiter "Projekt bleibt leer: Von
               aussen sieht man das Label, nicht die Arbeit dahinter." Das war
               einmal wahr und ist es nicht mehr — der Projektname reist seit
               laengerem mit, und wo er fehlt, sagt die Zelle sogar, dass man
               nochmal ziehen soll. Ein Kommentar, der eine geaenderte
               Entscheidung weitererzaehlt, ist schlimmer als keiner: Er wird
               geglaubt. */ ?>
            <th style="width:12em;"><?php esc_html_e('Status', 'cluster-one-bismarck'); ?></th>
            <th><?php esc_html_e('Project', 'cluster-one-bismarck'); ?></th>
            <?php /* v0.3.6: "Herkunft" war ein Wort fuer zwei Angaben. */ ?>
            <?php /* v0.3.8: Der Knoten wird ueber seine URL benannt, nicht ueber
               seinen Namen — zwei Knoten koennen denselben Namen waehlen, die
               Adresse ist das einzige, was verlaesslich eindeutig ist. */ ?>
            <th style="width:14em;"><?php esc_html_e('Node (URL)', 'cluster-one-bismarck'); ?></th>
            <th style="width:10em;"><?php esc_html_e('SysOp', 'cluster-one-bismarck'); ?></th>
            <th style="width:11em;"><?php esc_html_e('Created', 'cluster-one-bismarck'); ?></th>
<?php if (cluster_one_is_sysop()) : ?>
            <th style="text-align:right;"><?php esc_html_e('Actions', 'cluster-one-bismarck'); ?></th>
<?php endif; ?>
        </tr></thead>
        <tbody>
<?php foreach ($threads as $c1_tid => $c1_t) : $c1_v = cluster_one_foreign_verdict($c1_t); ?>
            <tr>
                <td style="text-align:center;"><span style="color:<?php echo esc_attr($farben[cluster_one_verdict_tone($c1_v)]); ?>;font-weight:600;" title="<?php echo esc_attr($worte[$c1_v]); ?>"><?php echo $c1_v === 'offen' ? '○' : '●'; ?></span></td>
                <td style="white-space:nowrap;max-width:22em;overflow:hidden;text-overflow:ellipsis;"><?php if (!empty($c1_t['pinned'])) : ?><span title="<?php echo esc_attr__('The SysOp considers this important', 'cluster-one-bismarck'); ?>">📌</span> <?php endif; ?><a href="<?php echo esc_url(admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($c1_tid))); ?>" title="<?php echo esc_attr((string) ($c1_t['title'] ?? $c1_tid)); ?>"><?php echo esc_html((string) ($c1_t['title'] ?? $c1_tid)); ?></a></td>
                <td style="opacity:.85;" title="<?php echo esc_attr__('Only published labels ever leave a node — what you see here was released on purpose.', 'cluster-one-bismarck'); ?>"><?php esc_html_e('Published', 'cluster-one-bismarck'); ?></td>
                <td style="white-space:nowrap;max-width:16em;overflow:hidden;text-overflow:ellipsis;"><?php
                    $c1_pt = trim((string) ($c1_t['project'] ?? ''));
                    if ($c1_pt !== '') {
                        echo '<span title="' . esc_attr__('The project this label speaks for. Its name travels; the work behind it stays on its own node.', 'cluster-one-bismarck') . '">' . esc_html($c1_pt) . '</span>';
                    } else {
                        echo '<span style="opacity:.45;" title="' . esc_attr__('Pulled before project names travelled — pull again to fill this in.', 'cluster-one-bismarck') . '">—</span>';
                    }
                ?></td>
                <td style="white-space:nowrap;opacity:.85;max-width:14em;overflow:hidden;text-overflow:ellipsis;" title="<?php echo esc_attr((string) ($c1_t['foreign_url'] ?? '')); ?>"><?php echo esc_html((string) ($c1_t['foreign_url'] ?? '')); ?></td>
                <td style="white-space:nowrap;opacity:.85;"><?php $c1_so = trim((string) ($c1_t['foreign_sysop'] ?? '')); echo $c1_so !== '' ? esc_html($c1_so) : '<span style="opacity:.45;">—</span>'; ?></td>
                <td><?php echo esc_html(date_i18n(get_option('date_format'), (int) ($c1_t['created'] ?? 0))); ?></td>
<?php if (cluster_one_is_sysop()) : ?>
                <td style="text-align:right;white-space:nowrap;"><?php /* v0.3.8: ohne nowrap standen Pinnadel und Papierkorb untereinander */ ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline-block;vertical-align:middle;">
                        <?php wp_nonce_field('cluster_one_foreign_pin'); ?>
                        <input type="hidden" name="action" value="cluster_one_foreign_pin">
                        <input type="hidden" name="thread" value="<?php echo esc_attr($c1_tid); ?>">
                        <button type="submit" style="background:none;border:0;padding:0 .3em;cursor:pointer;line-height:1;<?php echo empty($c1_t['pinned']) ? 'opacity:.45;' : 'color:#b26b00;'; ?>"
                                title="<?php echo esc_attr(empty($c1_t['pinned']) ? __('Pin — stays on top in every view', 'cluster-one-bismarck') : __('Unpin', 'cluster-one-bismarck')); ?>"
                                aria-label="<?php echo esc_attr(empty($c1_t['pinned']) ? __('Pin', 'cluster-one-bismarck') : __('Unpin', 'cluster-one-bismarck')); ?>">
                            <span class="dashicons dashicons-sticky" aria-hidden="true" style="font-size:20px;width:20px;height:20px;"></span>
                        </button>
                    </form>
<?php /* v0.3.4: Loeschen war seit 0.3.0 NIRGENDS erreichbar — der Knopf sitzt in
   der Foren-Liste, und aus der sind die Fremd-Threads seit 0.3.3 heraus. Ein
   fremdes Projekt darf man nicht umschreiben, aber sehr wohl aus dem eigenen
   Haus werfen. */
if (cluster_one_forum_thread_can_delete($c1_t, $c1_tid)) : ?>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;display:inline-block;vertical-align:middle;">
                        <?php wp_nonce_field('cluster_one_forum_thread_delete'); ?>
                        <input type="hidden" name="action" value="cluster_one_forum_thread_delete">
                        <input type="hidden" name="thread" value="<?php echo esc_attr($c1_tid); ?>">
                        <button type="submit" style="background:none;border:0;padding:0 .3em;cursor:pointer;line-height:1;color:#b32d2e;"
                                title="<?php echo esc_attr__('Remove from this node — the project stays where it came from', 'cluster-one-bismarck'); ?>"
                                aria-label="<?php echo esc_attr__('Remove', 'cluster-one-bismarck'); ?>"
                                onclick="return confirm('<?php echo esc_js(__('Remove this foreign label from your node? The project stays where it came from.', 'cluster-one-bismarck')); ?>');">
                            <span class="dashicons dashicons-trash" aria-hidden="true" style="font-size:20px;width:20px;height:20px;"></span>
                        </button>
                    </form>
<?php endif; ?>
                </td>
<?php endif; ?>
            </tr>
<?php endforeach; ?>
        </tbody>
    </table>
<?php endif;
}

/* v0.3.11: Bereiche umbenennen. Es gab bisher nur Anlegen, Verschieben und
   Loeschen — wer sich bei der Benennung vertat, musste den Bereich loeschen und
   verlor damit die Zuordnung aller Themen und Raeume darin.

   Der Kern ist deshalb NICHT das Umbenennen in der Liste, sondern das MITZIEHEN:
   Jeder Thread und jeder Raum, der auf den alten Namen zeigt, wird mit
   umgehaengt. Der Bereich ist ein String am Container, kein Verweis — ohne
   dieses Mitziehen fielen alle Inhalte lautlos ins Auffangbecken. */
add_action('admin_post_cluster_one_structure_rename', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_structure_rename');
    $alt  = isset($_POST['area'])     ? trim(sanitize_text_field(wp_unslash($_POST['area'])))     : '';
    $neu  = isset($_POST['new_area']) ? trim(sanitize_text_field(wp_unslash($_POST['new_area']))) : '';
    $back = admin_url('admin.php?page=cluster_one_bismarck&tab=structure');
    $areas = cluster_one_forum_areas();
    if ($alt === '' || $neu === '' || $alt === $neu || !in_array($alt, $areas, true)) {
        wp_safe_redirect(add_query_arg('struct_err', 'invalid', $back));
        exit;
    }
    if (in_array($neu, $areas, true)) {
        wp_safe_redirect(add_query_arg('struct_err', 'exists', $back)); // zwei gleiche Namen waeren nicht unterscheidbar
        exit;
    }
    wp_safe_redirect(add_query_arg('renamed', cluster_one_area_rename($alt, $neu), $back));
    exit;
});

/** v0.9.42: DAS MITZIEHEN ALS EIGENE FUNKTION.

   Der Rumpf stand bis hierher im Handler. Die Migration, die
   "Brainstorming (Wiki)" zu "Brainstorming" macht, braucht genau dasselbe —
   und ein zweiter Schreibweg auf dieselbe Wahrheit waere P1-12. Also
   herausgeloest statt nachgebaut: EINE Tuer, zwei Klinken.

   Gibt die Zahl der umgehaengten Container zurueck (Themen + Raeume).
   Prueft NICHT, ob der neue Name frei ist — das bleibt Sache des Aufrufers,
   weil die Antwort dort verschieden ausfaellt (der Handler schickt eine
   Fehlermeldung, die Migration bricht still ab). */
function cluster_one_area_rename(string $alt, string $neu): int {
    if ($alt === '' || $neu === '' || $alt === $neu) {
        return 0;
    }
    // 1) Die Liste
    $opts = get_option('cluster_one_settings', array());
    $opts['forum_areas'] = array_map(function ($a) use ($alt, $neu) {
        return $a === $alt ? $neu : $a;
    }, (array) ($opts['forum_areas'] ?? array()));
    update_option('cluster_one_settings', $opts, false);
    // 2) Die Inhalte MITZIEHEN — sonst fallen sie ins Auffangbecken
    $threads = cluster_one_get_forum_threads();
    $tz = 0;
    foreach ($threads as $tid => $t) {
        if ((string) ($t['area'] ?? '') === $alt) { $threads[$tid]['area'] = $neu; $tz++; }
    }
    if ($tz > 0) { cluster_one_save_forum_threads($threads); }
    // v0.6.4: Beschreibung und Herkunft wandern mit — sonst verloere ein
    // umbenannter Blaupausen-Bereich seinen Stern und seinen Text.
    cluster_one_area_meta_rename($alt, $neu);
    $rooms = cluster_one_get_rooms();
    $rz = 0;
    foreach ($rooms as $rid => $r) {
        if ((string) ($r['area'] ?? '') === $alt) { $rooms[$rid]['area'] = $neu; $rz++; }
    }
    if ($rz > 0) { cluster_one_save_rooms($rooms); }
    /* v0.9.42: Der dritte Schritt ist entfallen — er hielt die Liste
       forum_wiki_areas nach, und die gibt es nicht mehr. */
    return $tz + $rz;
}

/* v0.6.4: Beschreibung und Sichtbarkeit aendern. Getrennt vom Umbenennen,
   weil das Umbenennen Themen und Raeume mitzieht und ein Fehlklick dort teurer
   ist. */
add_action('admin_post_cluster_one_structure_desc', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_structure_desc');
    $back = admin_url('admin.php?page=cluster_one_bismarck&tab=structure');
    $area = isset($_POST['area']) ? trim(sanitize_text_field(wp_unslash($_POST['area']))) : '';
    if ($area === '' || !in_array($area, cluster_one_forum_areas(), true)) {
        wp_safe_redirect($back);
        exit;
    }
    cluster_one_area_meta_set($area, array(
        'desc'      => isset($_POST['area_desc']) ? trim(sanitize_textarea_field(wp_unslash($_POST['area_desc']))) : '',
        'published' => !empty($_POST['area_publish']),
    ));
    wp_safe_redirect($back . '&structure=desc');
    exit;
});

/* v0.8.7: Liegt fuer das Tagebuch dieses Raums eine Praeambel-Freigabe? Wird
   gebraucht, damit die Freigabe das Ziel schlagen kann — und damit sie nicht
   liegenbleibt, wenn eine Antwort ins Tagebuch geht. */
function cluster_one_diary_preamble_pending(string $room_id): bool {
    $rooms = cluster_one_get_rooms();
    $tid   = isset($rooms[$room_id]) ? (string) ($rooms[$room_id]['linked_thread'] ?? '') : '';
    if ($tid === '') {
        return false;
    }
    $threads = cluster_one_get_forum_threads();
    return isset($threads[$tid])
        && cluster_one_thread_is_ai_diary($threads[$tid])
        && !empty($threads[$tid]['preamble_grant']);
}

/* v0.8.9: Der Knopf mit drei Zustaenden (Christians Entwurf).

   Nicht drei Knoepfe, die aufploppen, sondern EIN Element, das seinen Zustand
   wechselt — und in jedem Zustand sagt, was der naechste Klick bewirkt. Genau
   das fehlte in seiner ersten Sitzung: Die Freigabe war scharf, ohne dass es
   irgendwo stand.

   Zustaende:
     ruhe       — nichts liegt an
     scharf     — wartet auf die naechste Antwort der Tagebuch-KI
     kandidat   — eine Antwort liegt vor und kann uebernommen werden

   Der Zustand "kandidat" nennt die UHRZEIT der Antwort. Ohne das waere es
   dieselbe Falle eine Stufe spaeter: "die Antwort uebernehmen" — welche? */
function cluster_one_diary_preamble_state(string $tid): string {
    $threads = cluster_one_get_forum_threads();
    if (!isset($threads[$tid]) || !cluster_one_thread_is_ai_diary($threads[$tid])) {
        return '';
    }
    if (trim((string) ($threads[$tid]['preamble_candidate'] ?? '')) !== '') {
        return 'kandidat';
    }
    return !empty($threads[$tid]['preamble_grant']) ? 'scharf' : 'ruhe';
}

add_action('admin_post_cluster_one_diary_preamble_adopt', function () {
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_diary_preamble_adopt');
    $room = isset($_POST['room']) ? sanitize_text_field(wp_unslash($_POST['room'])) : '';
    $op   = isset($_POST['op']) ? sanitize_key(wp_unslash($_POST['op'])) : 'adopt';
    $rooms = cluster_one_get_rooms();
    $tid   = isset($rooms[$room]) ? (string) ($rooms[$room]['linked_thread'] ?? '') : '';
    if ($tid !== '') {
        if ($op === 'discard') {
            /* Verwerfen ist kein Zeugnis wert: Der Text hat nie gegolten, und
               er steht ohnehin im Chat. */
            $threads = cluster_one_get_forum_threads();
            unset($threads[$tid]['preamble_candidate'], $threads[$tid]['preamble_candidate_ts'], $threads[$tid]['preamble_candidate_by']);
            cluster_one_save_forum_threads($threads);
        } else {
            cluster_one_diary_preamble_adopt($tid);
        }
    }
    wp_safe_redirect(admin_url('admin.php?page=cluster_one_room&room=' . rawurlencode($room)));
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

/* HIERHER UMGEZOGEN (0.9.80): Bereichs-Auswahlfeld. Bereiche sind Platons Sache; bismarck-seiten.php nutzt es mit — beide fahren in dasselbe Light. */
/** Bereichs-Auswahlfeld (Raum- und Thread-Konfiguration, Thread-Anlage):
   „— Ohne Bereich —" ist immer wählbar. Ein gespeicherter Bereich, der
   inzwischen aus der Liste gelöscht wurde, wird als eigener (markierter)
   Eintrag angeboten, damit das Formular den Ist-Zustand nicht stumm
   verfälscht. */
function cluster_one_render_area_select(string $current, string $field_name = 'area'): void {
    $areas = cluster_one_forum_areas();
    ?>
    <select name="<?php echo esc_attr($field_name); ?>">
        <option value=""><?php esc_html_e('— No area —', 'cluster-one-bismarck'); ?></option>
        <?php foreach ($areas as $a) : ?>
            <option value="<?php echo esc_attr($a); ?>" <?php selected($current, $a); ?>><?php echo esc_html($a); ?></option>
        <?php endforeach; ?>
        <?php if ($current !== '' && !in_array($current, $areas, true)) : ?>
            <option value="<?php echo esc_attr($current); ?>" selected><?php echo esc_html($current); ?> (<?php esc_html_e('no longer in the structure list', 'cluster-one-bismarck'); ?>)</option>
        <?php endif; ?>
    </select>
    <?php
}

/* HIERHER UMGEZOGEN (0.9.80): Welche Forenbereiche jemand sehen darf. Drei Aufrufer, alle hier. */
function cluster_one_areas_visible(): array {
    $alle = cluster_one_forum_areas();
    if (cluster_one_is_sysop()) {
        return $alle;
    }
    /* v0.9.3: DER SYSOP-BEREICH IST WIEDER SICHTBAR — seine INHALTE nicht.

       In 0.8.5 hatte ich geschrieben: "Der Bereich soll fuer andere gar nicht
       existieren." Das widerspricht der Regel des Hauses. Ueberall sonst gilt
       SICHTBAR, NICHT VERHINDERT — und ein verborgenes Fach, das jemand spaeter
       entdeckt, erzeugt genau das Misstrauen, das ein offener Vermerk
       vermeidet. Christians Einwand, und er hat recht.

       Die Ueberschrift steht also fuer alle, mit einem Satz darunter. Was darin
       liegt, bleibt verborgen (cluster_one_area_allows_view) — und zwar OHNE
       Silhouetten der einzelnen Raeume: Die Ueberschrift ist eine Auskunft,
       eine Liste von Umrissen waere Aktivitaet, und die geht niemanden an.

       Waehlbar ist er trotzdem nicht — siehe cluster_one_forum_areas_selectable. */
    return $alle;
}

/* HIERHER UMGEZOGEN (0.9.80): Welcher Termin haengt an diesem Thema. Zwei Aufrufer, beide hier. */
/** Dasselbe für Platon-Themen. */
function cluster_one_thread_linked_event(string $thread_id): ?array {
    foreach (cluster_one_get_gregor_events() as $ev) {
        if ((string) ($ev['linked_thread'] ?? '') === $thread_id && $thread_id !== '') {
            return $ev;
        }
    }
    return null;
}

/* ==========================================================================
   v1.0.0 — HEREINGEZOGEN AUS feynman.php, byte-identisch (Regel 2).
   SHA8 13b2618d vorher wie nachher. Die beiden einzigen Aufrufer stehen
   knapp darueber in dieser Datei; sie waren nie Feynmans Sache.
   ========================================================================== */

/* v0.7.3: DER LEERE AUSWAEHLER. Offen seit 0.1.5.

   Ist auf dem Knoten keine KI-Identitaet angelegt, war das <select> einfach
   leer — ein Kaestchen ohne Inhalt und daneben ein Knopf, der ins Nichts
   fuehrt. Der Beschirmte sieht nicht, WARUM nichts drinsteht, und wo er es
   aendern koennte, steht auch nicht.

   Ein leeres Feld ist keine Auskunft. Jetzt steht dort ein Satz mit dem Weg
   dorthin — und der Knopf verschwindet, statt Arbeit vorzutaeuschen. */
function cluster_one_ai_select_or_hint(string $name = 'cid'): string {
    $ids = cluster_one_get_ai_identities();
    if (empty($ids)) {
        /* v1.9.13 — SATZ UND LINK AUS DEMSELBEN SLUG.

           Bis hierher zeigte der Link auf cluster_one_settings (Kleinrock) und
           der Satz daneben sagte "Kranz". Die Maske wohnt auf
           cluster_one_bismarck_settings. Zwei Orte, zwei Wahrheiten, und
           gesehen hat es niemand, weil dieser Satz genau dem Menschen
           erscheint, der den Weg noch nicht kennt.

           Jetzt steht die Kennung EINMAL. Link und Satz koennen nicht mehr
           auseinanderlaufen, weil beide aus $c1_slug kommen.

           UND DIE MESSUNG, DIE DIE FORM ENTSCHIEDEN HAT:
           cluster_one_bismarck_settings ist eine VERSTECKTE Seite —
           add_submenu_page(null, …), menue.php:218. Eine versteckte Seite hat
           keinen Menuepfad; cluster_one_menue_pfad() gibt fuer sie '' zurueck,
           und zwar nicht als Ausnahme, sondern IMMER. Ihre einzige sichtbare
           Tuer ist die Reiterleiste im Kranz.

               Ein Satz, der bei '' schweigt, haette den Wegweiser damit
               ganz entfernt — fuer genau den Menschen, der ihn braucht.

           Deshalb faellt der Satz nicht weg, sondern der ORT im Satz. Der Link
           bleibt in jedem Fall, und er ist der Weg. Wird die Seite eines Tages
           sichtbar angemeldet (V21 §2a), bekommt der Satz seinen Pfad von
           selbst, ohne dass jemand diese Zeile anfasst.

           DER RIEGEL UM cluster_one_menue_pfad() IST HIER RICHTIG, obwohl ich
           gestern Nacht zwei abgelehnt habe. Der Unterschied ist der
           Rueckfall: dort waere er die alte Luege gewesen, hier ist er die
           ehrliche Antwort derselben Funktion — kein zweiter Text, nur ein
           zweiter Weg zu demselben (kern.php:2216). Bis 1.9.12 liegt, sagt der
           Satz "Set one up." und der Link stimmt trotzdem. */
        $c1_slug = 'cluster_one_bismarck_settings';
        $c1_link = admin_url('admin.php?page=' . $c1_slug);
        $c1_wo   = function_exists('cluster_one_menue_pfad')
            ? cluster_one_menue_pfad($c1_slug)
            : '';
        return '<span class="description">'
             . esc_html__('No AI identity set up on this node yet — that is why there is nothing to choose here.', 'cluster-one-bismarck')
             . ' <a href="' . esc_url($c1_link) . '">'
             . ($c1_wo !== ''
                 /* translators: %s: menu path, e.g. "Cluster One → Kranz" */
                 ? sprintf(esc_html__('Set one up under %s.', 'cluster-one-bismarck'), esc_html($c1_wo))
                 : esc_html__('Set one up.', 'cluster-one-bismarck'))
             . '</a></span>';
    }
    $aus = '<select name="' . esc_attr($name) . '">';
    foreach ($ids as $cid => $uid) {
        $aus .= '<option value="' . esc_attr((string) $cid) . '">' . esc_html(cluster_one_room_ai_label((string) $cid, array())) . '</option>';
    }
    return $aus . '</select>';
}

/** Gibt es ueberhaupt eine KI, die man fragen koennte?

   v1.9.10 — DIE FRAGE STEHT IM NAMEN, UND SIE WURDE NICHT GESTELLT. "Die man
   fragen koennte" ist eine Aussage ueber ein KONTO; gezaehlt wurde eine Liste,
   die es ueberlebt. Nach dem Entfernen des Vaults zeichnete diese Zeile die
   Auswahlliste mit zwei Waisen, statt den Satz "auf diesem Knoten ist noch
   keine KI-Identitaet eingerichtet" zu zeigen.

   Kein Riegel um cluster_one_ai_identities_live(): der einzig moegliche
   Rueckfall waere die alte Zaehlung, also genau die Behauptung, die hier
   entfernt wird. Der Boden-Block gehoert VOR diese Zeile aufgespielt — das
   steht im 7a-Vertrag, nicht in einem function_exists(). */
function cluster_one_has_ai_identity(): bool {
    return count(cluster_one_ai_identities_live()) > 0;
}


/* v1.3.4 — DER VERTRAG: WAS WEISS DAS FORUM?
   cluster_one_forum_areas() rief der Bauplan (kranz.php), und
   cluster_one_foreign_area() ruft faust.php beim Adoptieren eines fremden
   Treffens. Beide Dateien fahren in Lights mit, die platon.php nicht tragen.
       apply_filters('cluster_one_forum', $vorgabe, $was)
   Ohne Platon gibt es keine Bereiche — und „keine Bereiche" ist genau das,
   was der Bauplan dann anzeigen soll. */
add_filter('cluster_one_forum', function ($c1_wert, $c1_was = '') {
    switch ((string) $c1_was) {
        case 'bereiche':      return cluster_one_forum_areas();
        case 'fremd-bereich': return cluster_one_foreign_area();
    }
    return $c1_wert;
}, 10, 2);


/* v1.9.13 — DIE ZEILE AUS DER NAMENSTABELLE, JETZT IM ORGAN.

   Der Steckbrief des FORUMS. Es wohnt in dieser Datei.

   JEDES ORGAN NENNT DAS SEINE SELBST. Dieselbe Regel und derselbe Weg wie
   Kleinrocks Zeile seit 0.9.66: der Text zieht dorthin, wo beide Haeuser ihn
   haben, statt in eine zweite Kopie. Er steht in der Namenstabelle an
   derselben Stelle wie immer — nur nicht mehr DORT geschrieben.

   feynman.php holt ihn ueber cluster_one_steckbrief_von(), und der Sammler ist
   geriegelt: wer nicht da ist, liefert nichts. Deshalb faellt die Tabelle
   nicht, wenn dieses Organ fehlt — sie wird um eine Zeile kuerzer.

   Spalten: Name · Person · Lebensdaten · warum dieser Name · was er NICHT
   meint · Langtext. Namen werden nicht uebersetzt, Begruendungen schon. */
function cluster_one_platon_steckbrief(): array {
    return array('Platon', 'Platon', 'ca. 428–348 v. Chr.',
              __('The forum. He wrote down what Socrates said — which is the only reason we have any of it.', 'cluster-one-bismarck'),
              '',
              __('The forum area of this node. Socrates talked and left nothing behind; what we know of him, his student Platon wrote down. That is the division here too: the chat is the talking, the forum is the record. Whoever writes something here is doing what Platon did.', 'cluster-one-bismarck'));
}


/* v1.9.13 — DIE ZEILE AUS DER NAMENSTABELLE, JETZT IM ORGAN.

   Der Steckbrief der Selbstdarstellung — eine Themen-Art dieses
   Forums (first_post_role 'wilde', :1827).

   JEDES ORGAN NENNT DAS SEINE SELBST. Dieselbe Regel und derselbe Weg wie
   Kleinrocks Zeile seit 0.9.66: der Text zieht dorthin, wo beide Haeuser ihn
   haben, statt in eine zweite Kopie. Er steht in der Namenstabelle an
   derselben Stelle wie immer — nur nicht mehr DORT geschrieben.

   feynman.php holt ihn ueber cluster_one_steckbrief_von(), und der Sammler ist
   geriegelt: wer nicht da ist, liefert nichts. Deshalb faellt die Tabelle
   nicht, wenn dieses Organ fehlt — sie wird um eine Zeile kuerzer.

   Spalten: Name · Person · Lebensdaten · warum dieser Name · was er NICHT
   meint · Langtext. Namen werden nicht uebersetzt, Begruendungen schon. */
function cluster_one_wilde_steckbrief(): array {
    return array('Wilde', 'Oscar Wilde', '1854–1900',
              __('The self-portrait as a thread type. Only whoever it belongs to may change it; everyone else talks underneath.', 'cluster-one-bismarck'),
              '',
              '');
}


/* v1.9.13 — DIE ZEILE AUS DER NAMENSTABELLE, JETZT IM ORGAN.

   Der Steckbrief des WIKI — ebenfalls eine Themen-Art dieses
   Forums (Rolle 'wiki', geprueft nach Wilde, :930/:1363).

   JEDES ORGAN NENNT DAS SEINE SELBST. Dieselbe Regel und derselbe Weg wie
   Kleinrocks Zeile seit 0.9.66: der Text zieht dorthin, wo beide Haeuser ihn
   haben, statt in eine zweite Kopie. Er steht in der Namenstabelle an
   derselben Stelle wie immer — nur nicht mehr DORT geschrieben.

   feynman.php holt ihn ueber cluster_one_steckbrief_von(), und der Sammler ist
   geriegelt: wer nicht da ist, liefert nichts. Deshalb faellt die Tabelle
   nicht, wenn dieses Organ fehlt — sie wird um eine Zeile kuerzer.

   Spalten: Name · Person · Lebensdaten · warum dieser Name · was er NICHT
   meint · Langtext. Namen werden nicht uebersetzt, Begruendungen schon. */
function cluster_one_cunningham_steckbrief(): array {
    return array('Cunningham', 'Ward Cunningham', '*1949',
              __('The wiki itself. He built the first one in 1995 — and named it after a Hawaiian airport shuttle.', 'cluster-one-bismarck'),
              '',
              __('Ward Cunningham travelled to Hawaii in 1994, where an airport employee pointed him to the “Wiki Wiki Shuttle”. Wiki means quick in Hawaiian. He was looking for a name for a system that was fast and simple, and in 1995 he wrote the software for his own website: the WikiWikiWeb. Its novelty was that any visitor could edit a page directly in the browser, with no programming knowledge — a simple exchange of knowledge among software developers. The idea spread; in 2001 Jimmy Wales and Larry Sanger used the technology to found Wikipedia. Every wiki since, including this one, descends from that shuttle bus.', 'cluster-one-bismarck'));
}


/* ---------------------------------------------------------------------------
 * 19.10.2026 (Neo) — DER VERTRAG: EIN FREMDES HAUS LEGT EINEN TEXT IN EIN THEMA.
 *
 * Crockford will den Site-Kontext dort ablegen, wo eine KI ihn bei jedem
 * Aufruf bekommt: in der Praeambel eines Themas (sie reist ueber den
 * verknuepften Raum mit, siehe cluster_one_preamble_for_ai). Crockford kennt
 * weder Themen noch Raeume beim Namen, also zwei Verträge:
 *
 *     apply_filters('cluster_one_thema', null, 'leere')      lesen
 *         -> array tid => Titel der leeren Themen (keine Beitraege ausser
 *            Systemzeilen, keine Praeambel, kein Fremd-Label, kein Tagebuch,
 *            kein Wiki), fuer den fragenden Menschen sichtbar.
 *            null heisst: kein Organ antwortet.
 *     do_action('cluster_one_thema_ablegen', $auftrag)      schreiben
 *         $auftrag ist ein Objekt, damit die Antwort zurueckkommt:
 *            rein:  thema (tid oder ''), name (fuer ein neues Thema),
 *                   text (die Praeambel), meldung (Systemzeile),
 *                   meldung2 (zweite Systemzeile oder '')
 *            raus:  thema, raum, url, fehler ('' oder ein Wort)
 *
 * Ein Filter, der schreibt, waere eine Luege ueber seine Bauform (umgebung.php
 * sagt das seit 1.3.2); deshalb die Aktion. Und ein Objekt statt eines Arrays,
 * weil eine Aktion nichts zurueckgibt — das Objekt ist der Rueckkanal.
 *
 * DIE PRAEAMBEL WIRD HIER NICHT AUF 2000 ZEICHEN GEKAPPT. Der Praeambel-Handler
 * oben nutzt cluster_one_sanitize_message() und damit die Kappung fuer
 * Chatnachrichten. Ein Site-Kontext ist laenger, und sein Umfang ist dem
 * Absender vorher als Preis genannt worden. Benannte Grenze: wer die Praeambel
 * danach ueber das Formular der Themenseite speichert, kappt sie dort auf 2000.
 * Tags werden auch hier entfernt, HTML reist nie.
 * ------------------------------------------------------------------------- */
function cluster_one_thema_ist_leer(string $c1_tid): bool {
    $c1_threads = cluster_one_get_forum_threads();
    if (!isset($c1_threads[$c1_tid])) { return false; }
    $c1_t = $c1_threads[$c1_tid];
    if (cluster_one_thread_is_foreign($c1_t) || cluster_one_thread_is_diary($c1_t)
        || cluster_one_thread_is_label($c1_t) || !empty($c1_t['wiki'])) {
        return false;
    }
    if (trim((string) ($c1_t['preamble'] ?? '')) !== '') { return false; }
    foreach (cluster_one_get_forum_posts($c1_tid) as $c1_p) {
        if (empty($c1_p['system'])) { return false; }
    }
    return true;
}

add_filter('cluster_one_thema', function ($c1_wert, $c1_was = '', $c1_kontext = null) {
    if ((string) $c1_was !== 'leere') { return $c1_wert; }
    $c1_aus = array();
    foreach (cluster_one_get_forum_threads() as $c1_tid => $c1_t) {
        if (!cluster_one_group_visibility_can_view($c1_t)) { continue; }
        if (cluster_one_thema_ist_leer((string) $c1_tid)) {
            $c1_aus[(string) $c1_tid] = (string) ($c1_t['title'] ?? $c1_tid);
        }
    }
    return $c1_aus;
}, 10, 3);

add_action('cluster_one_thema_ablegen', function ($c1_a) {
    if (!is_object($c1_a)) { return; }
    $c1_a->fehler = '';
    if (!cluster_one_can_access() || !cluster_one_is_sysop()) { $c1_a->fehler = 'nicht_sysop'; return; }

    $c1_text = wp_strip_all_tags((string) ($c1_a->text ?? ''));
    $c1_text = trim(str_replace("\r", '', $c1_text));
    if ($c1_text === '') { $c1_a->fehler = 'kein_text'; return; }

    $c1_tid     = (string) ($c1_a->thema ?? '');
    $c1_threads = cluster_one_get_forum_threads();
    $c1_neu     = false;

    if ($c1_tid === '') {
        $c1_name = trim(sanitize_text_field((string) ($c1_a->name ?? '')));
        if ($c1_name === '') { $c1_a->fehler = 'kein_name'; return; }
        $c1_tid = cluster_one_forum_thread_id();
        $c1_threads[$c1_tid] = array(
            'id'               => $c1_tid,
            'title'            => $c1_name,
            'author'           => cluster_one_human_speaker_label(),
            'creator_actor'    => cluster_one_actor_id_human(),
            'created'          => time(),
            'group_visibility' => 'all',
            'visible_to'       => array(),
            'area'             => '',
            'wiki'             => false,
            'wilde_owner'      => '',
            'human_diary'      => false,
        );
        cluster_one_save_forum_posts($c1_tid, array());
        $c1_neu = true;
    } elseif (!isset($c1_threads[$c1_tid])) {
        $c1_a->fehler = 'unbekannt'; return;
    } elseif (!cluster_one_thema_ist_leer($c1_tid)) {
        $c1_a->fehler = 'nicht_leer'; return;
    }

    $c1_threads[$c1_tid]['preamble'] = $c1_text;
    cluster_one_save_forum_threads($c1_threads);

    $c1_posts   = cluster_one_get_forum_posts($c1_tid);
    $c1_posts[] = cluster_one_system_message(sprintf(
        /* translators: %s: 8-character fingerprint of the preamble */
        __('SysOp set the preamble (fingerprint %s).', 'cluster-one-bismarck'), cluster_one_preamble_hash($c1_text)));
    $c1_m1 = trim((string) ($c1_a->meldung ?? ''));
    $c1_m2 = trim((string) ($c1_a->meldung2 ?? ''));
    if ($c1_m1 !== '') { $c1_posts[] = cluster_one_system_message($c1_m1); }
    if ($c1_m2 !== '') { $c1_posts[] = cluster_one_system_message($c1_m2); }
    cluster_one_save_forum_posts($c1_tid, $c1_posts);

    if ($c1_neu) {
        cluster_one_event_emit(array(
            'type' => 'thread_created', 'container_type' => 'thread', 'container_id' => $c1_tid,
            'actor_id' => cluster_one_actor_id_human(), 'content_hash' => hash('sha256', (string) $c1_threads[$c1_tid]['title']),
        ), false);
    }
    /* 19.10.2026 (Neo), 0.2.51: hier stand 'preamble_set'. Die Chronik sagt
       jetzt, WAS gelegt wurde; das Wort dazu kennt der Boden ab 1.17.0
       (cluster_one_chronik_satz). Ein aelterer Boden zeigt den technischen
       Namen — sichtbar, nicht falsch. */
    cluster_one_event_emit(array(
        'type'           => 'site_context_laid',
        'container_type' => 'thread', 'container_id' => $c1_tid,
        'actor_id'       => cluster_one_actor_id_human(),
        'visibility'     => cluster_one_event_visibility($c1_threads[$c1_tid]),
        'content_hash'   => hash('sha256', $c1_text),
    ), true);

    /* Der Raum dazu: bei einem neuen Thema wird einer angelegt (ein Projekt
       entsteht paarweise, wie im Projekt-Weg oben); ein bestehendes Thema
       behaelt den seinen oder keinen. Die Systemzeilen stehen auch dort,
       denn dort fragt man die KI. */
    $c1_rid = (string) ($c1_threads[$c1_tid]['linked_room'] ?? '');
    if ($c1_rid === '' && $c1_neu && function_exists('cluster_one_room_create_do')) {
        $c1_rid = cluster_one_room_create_do((string) $c1_threads[$c1_tid]['title'] . ' — Chat', array(
            'owner' => get_current_user_id(),
        ));
        if ($c1_rid !== '') { cluster_one_set_room_thread_link($c1_rid, $c1_tid); }
    }
    if ($c1_rid !== '' && function_exists('cluster_one_fs_system_msg')) {
        if ($c1_m1 !== '') { cluster_one_fs_system_msg($c1_rid, $c1_m1); }
        if ($c1_m2 !== '') { cluster_one_fs_system_msg($c1_rid, $c1_m2); }
    }

    $c1_a->thema = $c1_tid;
    $c1_a->raum  = $c1_rid;
    $c1_a->url   = admin_url('admin.php?page=cluster_one_forum_thread&thread=' . rawurlencode($c1_tid));
});

}
