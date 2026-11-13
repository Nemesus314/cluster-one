<?php
/**
 * Cluster One — Modul farbspiel (Züge 11–13: Feinschnitt aus bismarck).
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

   OHNE $c1_farbspiel_hoeher: die Vorlage aus §4 traegt ein
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
$cluster_one_farbspiel_laeuft = !function_exists('cluster_one_fs_state');
cluster_one_hier('farbspiel', '', $cluster_one_farbspiel_laeuft);

if ($cluster_one_farbspiel_laeuft) {

/** Das Farbspiel-Cockpit (v0.0.81→0.0.82, Design SysOp 16./17.07.2026):
   Runden im Chat, Ampel pro Teilnehmer — das Cockpit des Reaktorfahrers.
   GELB = Runde offen, noch nicht zu Wort gekommen. GRÜN = hat gesprochen
   oder gepasst (Punkt-Gesetz: grün erst nach Vollzug). ROT = die Runde
   wurde geschlossen, BEVOR die Person sprach — rot dokumentiert den
   SCHNITT des SysOps, nicht das Versäumnis der Person (kein Pranger; die
   Statusmeldung sagt „Runde geschlossen; noch offen: …"), und bleibt, bis
   die Person wieder spricht. BLAU = Wunsch, eine bestimmte KI zu befragen
   — die Reaktorfahrer-Freigabe als Nebenprodukt: Zulassen = normaler
   Trigger (KI antwortet, grün), Überspringen = liegenlassen, Reset mit
   der nächsten Runde. PASSEN zählt wie zu Wort kommen (löst das Loch der
   Schweiger — die Ein-Personen-Hälfte des Anti-Babel-Dual-Consent). */
function cluster_one_fs_state(array $room): array {
    $fs = (isset($room['fs']) && is_array($room['fs'])) ? $room['fs'] : array();
    return array(
        'active' => !empty($fs['active']),
        'n'      => max(0, (int) ($fs['n'] ?? 0)),
        'spoken' => array_map('strval', (array) ($fs['spoken'] ?? array())),
        'passed' => array_map('strval', (array) ($fs['passed'] ?? array())),
        'red'    => array_map('strval', (array) ($fs['red'] ?? array())),
        'wishes' => is_array($fs['wishes'] ?? null) ? $fs['wishes'] : array(),
    );
}

/** Wer spielt am Cockpit mit? Genau die Sidebar-Besetzung: SysOp + die
   angehakten WP-Menschen + die KIs des Raums (Externe können sich nicht
   einloggen — keine Attrappen-Lampen). */
function cluster_one_fs_participants(array $room): array {
    $keys = array('wp_' . cluster_one_main_admin_user_id());
    foreach ((array) ($room['participants'] ?? array()) as $pid) {
        if (strpos((string) $pid, 'wp_') === 0 && !in_array((string) $pid, $keys, true)) {
            $keys[] = (string) $pid;
        }
    }
    foreach ((array) ($room['ais'] ?? array()) as $cid) {
        $keys[] = (string) $cid;
    }
    return $keys;
}

function cluster_one_fs_light(array $fs, string $key): string {
    // v0.0.84 (Beschluss SysOp): In der Runde zeigt die Lampe, wer noch
    // spricht — zwischen den Runden, wer noch schuldet. ROT = offene
    // Schuld ans Gespräch (Schnitt ODER gescheiterter KI-Aufruf), erlischt
    // beim nächsten gelungenen Wort oder per Amnestie. BLAU = „dein Wort
    // ist gewünscht" — KI (Wunsch ans Cockpit) und Mensch (Ping) sprechen
    // dieselbe Lampensprache. Zwischen den Runden ist GRÜN der Ruhewert:
    // keine offene Schuld (das Schlussbild friert ein statt zu erlöschen).
    if (in_array($key, $fs['red'], true)) {
        return 'red';
    }
    if (!empty($fs['wishes'][$key])) {
        return 'blue';
    }
    if ($fs['active']) {
        return (in_array($key, $fs['spoken'], true) || in_array($key, $fs['passed'], true)) ? 'green' : 'yellow';
    }
    return $fs['n'] > 0 ? 'green' : 'off';
}

/** Gescheiterter KI-Aufruf: die KI schuldet dem Gespräch ihr Wort — rot,
   bis ein Aufruf gelingt (mark_spoken löscht rot). Die Systemmeldung im
   Raum existiert seit jeher; jetzt zeigt auch das Cockpit die Störung. */
function cluster_one_fs_mark_failed(string $room_id, string $key): void {
    $rooms = cluster_one_get_rooms();
    if (!isset($rooms[$room_id])) {
        return;
    }
    $fs = cluster_one_fs_state($rooms[$room_id]);
    if (!in_array($key, $fs['red'], true)) {
        $fs['red'][] = $key;
    }
    $rooms[$room_id]['fs'] = $fs;
    cluster_one_save_rooms($rooms);
}

function cluster_one_fs_dot(string $light): string {
    $map = array(
        'green'  => array('#1a7f37', __('has had the floor this round', 'cluster-one-bismarck')),
        'yellow' => array('#d4a017', __('not yet heard this round', 'cluster-one-bismarck')),
        'red'    => array('#c0392b', __('round was closed before they spoke', 'cluster-one-bismarck')),
        'blue'   => array('#2271b1', __('someone wishes to ask this AI', 'cluster-one-bismarck')),
    );
    if (!isset($map[$light])) {
        return '';
    }
    return '<span aria-hidden="true" title="' . esc_attr($map[$light][1]) . '" style="display:inline-block;width:9px;height:9px;border-radius:50%;background:' . $map[$light][0] . ';flex:none;"></span> ';
}

/** Sprechen vollzogen: grün, rot erlischt, erfüllte Wünsche erlöschen. */
function cluster_one_fs_mark_spoken(string $room_id, string $key): void {
    $rooms = cluster_one_get_rooms();
    if (!isset($rooms[$room_id])) {
        return;
    }
    $fs = cluster_one_fs_state($rooms[$room_id]);
    $fs['red'] = array_values(array_diff($fs['red'], array($key)));
    if ($fs['active'] && !in_array($key, $fs['spoken'], true)) {
        $fs['spoken'][] = $key;
    }
    unset($fs['wishes'][$key]);
    $rooms[$room_id]['fs'] = $fs;
    cluster_one_save_rooms($rooms);
}

/* --- v0.1.6: REAKTORFAHRER AUF ZEIT -----------------------------------------
   Der Schnitt gehört dem Reaktorfahrer, und das war bisher immer der SysOp.
   Jetzt kann er ihn auf Zeit abgeben — der erste Hebel in diesem Plugin, den
   jemand anderes anfassen darf, während der SysOp zusieht.

   Die Übergabe LÄUFT AB, und das ist der Kern: Ein vergessenes Zurücknehmen
   kann keinen Dauerzustand erzeugen. Geprüft wird beim Lesen, nicht von einem
   Aufräum-Auftrag — deshalb heilt sich der Zustand von allein, auch wenn
   monatelang niemand die Seite öffnet.

   Ein Feld am Raum: fs_pilot = ['actor' => 'wp:N', 'until' => Zeitstempel].
   Fehlt es oder ist die Zeit abgelaufen, fährt der SysOp. */

/** Der aktuelle Reaktorfahrer, oder '' wenn keiner (bzw. abgelaufen). */
function cluster_one_fs_pilot(array $room): string {
    $p = isset($room['fs_pilot']) && is_array($room['fs_pilot']) ? $room['fs_pilot'] : array();
    $actor = (string) ($p['actor'] ?? '');
    $until = (int) ($p['until'] ?? 0);
    if ($actor === '' || $until <= 0 || $until <= time()) {
        return ''; // nicht vergeben, oder die Zeit ist um
    }
    return $actor;
}

/** Restzeit der Übergabe in Sekunden, 0 wenn keine läuft. */
function cluster_one_fs_pilot_left(array $room): int {
    if (cluster_one_fs_pilot($room) === '') {
        return 0;
    }
    return max(0, (int) $room['fs_pilot']['until'] - time());
}

/** Darf dieser Akteur den Schnitt setzen? Der SysOp immer — sonst nur, wer
   gerade fährt. Ersetzt die alte reine is_sysop()-Prüfung an fs_round. */
function cluster_one_fs_may_cut(array $room, string $actor): bool {
    if (cluster_one_is_sysop()) {
        return true; // volle Gewalt bleibt volle Gewalt
    }
    $pilot = cluster_one_fs_pilot($room);
    return $pilot !== '' && $pilot === $actor;
}

function cluster_one_fs_system_msg(string $room_id, string $text): void {
    $msgs = cluster_one_get_room_messages($room_id);
    /* v0.9.15: EIN DOPPELKLICK IST KEINE ZWEITE TATSACHE.

       Christians Befund: Die Randbedingungen-Uebergabe stand zweimal im Chat,
       wortgleich, zur selben Minute. Der Handler schreibt sie einmal — aber er
       ist NICHT IDEMPOTENT: Ein zweiter Klick (oder ein erneut gesendetes
       Formular nach dem Zurueck-Knopf) erzeugt eine zweite Nachricht.

       Das betrifft nicht nur diesen einen Knopf, sondern jede Systemzeile im
       Farbspiel. Deshalb hier, an der einen Stelle, durch die sie alle laufen:
       Ist die letzte Nachricht wortgleich und keine zehn Sekunden alt, war es
       ein Doppelklick. Zwei echte gleiche Ereignisse innerhalb von zehn
       Sekunden gibt es nicht — eine Runde wird nicht zweimal in derselben
       Sekunde eroeffnet. */
    $letzte = end($msgs);
    if (is_array($letzte)
        && !empty($letzte['system'])
        && (string) ($letzte['body'] ?? '') === $text
        && (time() - (int) ($letzte['ts'] ?? 0)) < 10) {
        return;
    }
    $msgs[] = cluster_one_system_message($text);
    cluster_one_save_room_messages($room_id, $msgs);
}

function cluster_one_fs_display_name(string $key): string {
    if (strpos($key, 'wp_') === 0) {
        $u = get_userdata((int) substr($key, 3));
        return $u ? $u->display_name : $key;
    }
    $ids = cluster_one_get_ai_identities();
    if (isset($ids[$key]) && (int) $ids[$key] > 0) {
        $u = get_userdata((int) $ids[$key]);
        if ($u) {
            return $u->display_name;
        }
    }
    return cluster_one_room_ai_label($key, array());
}


/* v1.3.4 — DIE SYSTEMNACHRICHT MELDET SICH AN. Der Bauplan legt einen Raum an
   und will hineinschreiben, dass er neu ist; ohne Farbspiel gibt es niemanden,
   der schreibt, und der Raum ist trotzdem da. */
add_action('cluster_one_raum_systemnachricht', function ($c1_raum, $c1_text) {
    cluster_one_fs_system_msg((string) $c1_raum, (string) $c1_text);
}, 10, 2);

}
