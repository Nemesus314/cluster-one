<?php
/**
 * Cluster One — Modul speicher (v0.9.56).
 *
 * DIE TUER ZU EINEM SPEICHER IST KEINE SACHE IM ZIMMER.
 *
 * Hier stehen die elf Funktionen, die nichts tun als lesen und schreiben —
 * reine Huellen um get_option/update_option, ohne eine Zeile Verhalten. Sie
 * wohnten bis 0.9.55 in den Organen, die sie zuerst brauchten:
 *
 *     cluster_one_rooms · room_chat_*     -> bismarck.php
 *     cluster_one_forum_threads · posts_* -> platon.php
 *     cluster_one_chat_* (Kanten-Chat)    -> kleinrock.php
 *     chat_append (dieselbe Tuer!)        -> bismarck.php
 *
 * Die Rechnung dafuer stand in der Abhaengigkeits-Matrix (konfiguration.php):
 * Der KERN musste durch die Organe hindurch lesen. Wer die Raeume wollte,
 * musste Bismarck mitnehmen; wer die Themen wollte, Platon. Ein Modul, das
 * nur Daten braucht, schleppte das ganze Organ mit.
 *
 * Der aelteste Fall ist chat_append: Es schreibt in den KANTEN-Chat, dessen
 * beide anderen Tueren in Kleinrock standen. Ein Speicher, zwei Dateien —
 * genau die Form, aus der P1-12 entsteht. Seit dem Schnitt v0.0.100 so, und
 * es ist nie aufgefallen, weil beide Dateien immer zusammen geladen wurden.
 *
 * DIE TUER HATTE KEINEN AUSGANG — SEIT 0.9.67 EINEN HALBEN:
 * Bis 0.9.66 bot diese Schicht kein Loeschen an, und deshalb erfand jede
 * Loeschstelle ihr eigenes delete_option. Der Knoten-Reset darf das: Er
 * raeumt den ganzen Speicher und muss die Schluessel kennen. Zwei
 * Einzel-Loeschungen (Raum-Chat in bismarck.php, Forum-Beitraege in
 * platon.php) und eine Migration duerften es eigentlich nicht.
 * Ein Zeuge in verhalten.php nagelt die Zahl bei ZWOELF fest, damit sie nicht
 * heimlich waechst.
 *
 * In 0.9.67 hat er zugeschlagen. Die neue Netz-Loeschung in kleinrock.php
 * haette eine dreizehnte und vierzehnte rohe Stelle gebracht, und der Zeuge
 * meldete es sofort. **Die Antwort war nicht, seine Zahl hochzusetzen** — ein
 * Deckel, den man hebt, sobald er drueckt, ist kein Deckel. Die Antwort war
 * `cluster_one_delete_chat()`, die erste echte Loeschtuer dieser Schicht. Die
 * Zahl steht weiter bei zwoelf.
 *
 * OFFEN BLEIBT DIE ANDERE HAELFTE: Raum-Chats und Forum-Beitraege haben noch
 * keine Tuer, und der Knoten-Reset geht weiter am Schloss vorbei. Das ist ein
 * Umbau, kein Umzug — also weiter nicht in dieser Version.
 *
 * ZWEI ASYMMETRIEN, sichtbar erst, seit die drei Speicher nebeneinander
 * liegen — festgehalten, NICHT geaendert (das waere Verhalten):
 *   · save_room_messages und save_chat kappen bei 200 Nachrichten,
 *     save_forum_posts nicht. Ich hielt das erst fuer eine unbegruendete
 *     Asymmetrie — sie ist begruendet, und die Begruendung ist mit
 *     umgezogen: "Bewusst KEIN Ringpuffer-Abschneiden wie beim Chat — ein
 *     Forum-Thema soll nicht bei 200 Beitraegen stillschweigend vorne
 *     verlieren." Der Umzug hat den Satz sichtbar gemacht, nicht erzeugt.
 *   · save_room_messages und save_chat sind bis auf den Optionsnamen
 *     Zeile fuer Zeile identisch. Das ist echte Verdopplung — aber sie
 *     zusammenzulegen waere Umbau, nicht Umzug.
 *
 * Byte-identisch umgezogen (move don't rebuild, Regel 2); Golden bezeugt es.
 */
defined('ABSPATH') || exit;

/* v1.0.1 — DER RIEGEL, aus demselben Grund wie in kern.php: ein Light traegt
   den ganzen Boden, und zwei ungeriegelte Kopien sind ein Fatal. Waechter ist
   die erste Tuer, cluster_one_get_rooms(). */
/* VERTRAG 27 §4 — DIE MELDUNG STEHT VOR DEM RIEGEL.
   Bis hierher stand cluster_one_hier() INNERHALB des Riegels: eine verdraengte
   Kopie dieser Datei schwieg dann auch ueber ihre eigene Existenz. Die
   Bedingung wird jetzt EINMAL gelesen und traegt beide Folgen — die Meldung
   und den Riegel.
       Ein Melder kennt sich selbst. „Verdraengt" ist eine BEZIEHUNG, und
       Beziehungen gehoeren dem Sammler.            (Maillart, 11.09.)
   Die Fassung bleibt LEER: Kasper liest sie am Wirt (H118), und eine
   geliehene Nummer ist schlimmer als keine. */
$cluster_one_speicher_laeuft = !function_exists('cluster_one_get_rooms');

if (function_exists('cluster_one_hier')) {
    cluster_one_hier('speicher', '', $cluster_one_speicher_laeuft);
}

if ($cluster_one_speicher_laeuft) {

/* v0.9.72: siehe kern.php — die Meldung an Kasper setzt cluster-one.php ab. */

/* --- RAEUME und ihre Nachrichten (Umzug aus bismarck.php) ---
   Dazu chat_append, das gar nicht hierher gehoerte: Es schreibt in den
   KANTEN-Chat und stand doch bei den Raeumen. --- */

function cluster_one_get_rooms(): array {
    $a = get_option('cluster_one_rooms', []);
    return is_array($a) ? $a : [];
}

function cluster_one_save_rooms(array $a): void {
    update_option('cluster_one_rooms', $a, false);
}

/** Nachrichtenspeicher eines Raums (Ringpuffer, autoload=no). Mehr-Sprecher. */
function cluster_one_get_room_messages(string $room_id): array {
    $m = get_option('cluster_one_room_chat_' . $room_id, []);
    return is_array($m) ? $m : [];
}

function cluster_one_save_room_messages(string $room_id, array $msgs): void {
    if (count($msgs) > 200) {
        $msgs = array_slice($msgs, -200);
    }
    update_option('cluster_one_room_chat_' . $room_id, array_values($msgs), false);
}

/** Hängt eine Nachricht an (Dedup über die ID). Gibt true bei Neuanlage. */
function cluster_one_chat_append(string $edge_id, array $msg): bool {
    $msgs = cluster_one_get_chat($edge_id);
    foreach ($msgs as $m) {
        if (($m['id'] ?? '') === ($msg['id'] ?? '_none_')) {
            return false; // schon vorhanden -> nichts tun (at-least-once safe)
        }
    }
    $msgs[] = $msg;
    cluster_one_save_chat($edge_id, $msgs);
    return true;
}

/* --- THEMEN und ihre Beitraege (Umzug aus platon.php) --- */

/* --- Platon (Forum), v0.0.47 – Schicht 1: Themen + Beiträge, kein Wiki,
   keine PMs, kein Label. Datenspeicher bewusst analog zu Bismarck-Räumen
   (eigene Option pro Thema für die Beiträge). Bewusst KEIN Ringpuffer-
   Abschneiden wie beim Chat — ein Forum-Thema soll nicht bei 200 Beiträgen
   stillschweigend vorne verlieren. --- */
function cluster_one_get_forum_threads(): array {
    $a = get_option('cluster_one_forum_threads', array());
    return is_array($a) ? $a : array();
}

function cluster_one_save_forum_threads(array $a): void {
    update_option('cluster_one_forum_threads', $a, false);
}

/** Beiträge eines Themas. Jeder Beitrag: id, author, subject (phpBB-Stil,
   frei editierbar, berührt nie den Thema-Titel), body, ts, optional 'ai'. */
function cluster_one_get_forum_posts(string $thread_id): array {
    $p = get_option('cluster_one_forum_posts_' . $thread_id, array());
    return is_array($p) ? $p : array();
}

function cluster_one_save_forum_posts(string $thread_id, array $posts): void {
    update_option('cluster_one_forum_posts_' . $thread_id, array_values($posts), false);
}

/* --- KANTEN-CHAT (Umzug aus kleinrock.php) — jetzt bei chat_append,
   mit dem er seit dem Schnitt v0.0.100 haette zusammenwohnen sollen. --- */

/** Liest den Nachrichtenspeicher einer Kante (Ringpuffer, autoload=no). */
function cluster_one_get_chat(string $edge_id): array {
    $m = get_option('cluster_one_chat_' . $edge_id, []);
    return is_array($m) ? $m : [];
}

/** Speichert den Nachrichtenspeicher (gekappt, NICHT autoloaded). */
function cluster_one_save_chat(string $edge_id, array $msgs): void {
    if (count($msgs) > 200) {
        $msgs = array_slice($msgs, -200);
    }
    update_option('cluster_one_chat_' . $edge_id, array_values($msgs), false);
}

/** Loescht den Nachrichtenspeicher einer Kante. Sagt, ob dort etwas lag.
 *
 *  DIE ERSTE LOESCHTUER DIESER SCHICHT (v0.9.67).
 *
 *  Im Kopf dieser Datei stand seit 0.9.56 als benannte Stoerung: "Die Tuer hat
 *  keinen Ausgang — Loeschen wird nicht angeboten." Solange nur der
 *  Knoten-Reset loeschte, war das ertraeglich; er ist EINE Stelle. Mit
 *  cluster_one_kleinrock_knoten_loeschen() waere eine zweite dazugekommen, und
 *  ein Zeuge in verhalten.php hat das sofort gemeldet: "genau 12 Stellen
 *  fassen den Speicher roh an — mehr darf es nicht werden."
 *
 *  DIE ANTWORT DARAUF IST NICHT, SEINE ZAHL HOCHZUSETZEN. Ein Deckel, den man
 *  hebt, sobald er drueckt, ist kein Deckel. Die Antwort ist die Tuer, die
 *  hier immer gefehlt hat.
 *
 *  Der Rueckgabewert ist kein Luxus: Wer loescht, will zaehlen koennen, was er
 *  genommen hat, ohne den Schluessel selbst in die Hand zu nehmen. Ohne ihn
 *  muesste jeder Aufrufer wieder roh nachsehen — und die Tuer waere umsonst.
 */
function cluster_one_delete_chat(string $edge_id): bool {
    $da = get_option('cluster_one_chat_' . $edge_id, null) !== null;
    delete_option('cluster_one_chat_' . $edge_id);
    return $da;
}

/* --- FEYNMANS EIGENE KAPITEL (Umzug aus feynman.php, v0.9.58) ---
   Der Text, den ein SysOp seinen Leuten und seinen KIs mitgibt. Die letzte
   Tuer, die noch in einem Organ stand — mit ihr wird Feynman reiner Text
   und schreibt nichts mehr. --- */

/* --- v0.1.9: EIGENE KAPITEL DES SYSOPS ---------------------------------------
   Jeder SysOp soll seinen Leuten UND seinen KIs eigenes Werkzeug- und
   Kontextwissen mitgeben koennen — generisch, statt die Privatwerkzeuge eines
   Einzelnen in den Kern zu kodieren. (Eine Checkbox "YT-Script-Infos
   einblenden" wurde erwogen und verworfen: persoenlicher Inhalt im generischen
   Plugin, plus ein manueller Wahrheitsschalter.)

   Schnitt wie ueberall: STEUERUNG IN KRANZ, WISSEN IN FEYNMAN.

   Und ein Schalter pro Kapitel: "auch fuer KIs". Bleibt er aus, lesen nur
   Menschen — bleibt er an, wandert das Kapitel in die Praeambel, gleich hinter
   die Hausordnung aus why.md. Der Schalter existiert, damit die Entscheidung
   BEWUSST faellt und fuer beide Seiten sichtbar ist: Wer das Kapitel liest,
   sieht, ob die KIs es auch bekommen. Ohne ihn haetten wir die Asymmetrie
   wieder eingebaut, die 0.1.8 gerade abgeraeumt hat. */
function cluster_one_get_feynman_chapters(): array {
    $a = get_option('cluster_one_feynman_chapters', array());
    return is_array($a) ? array_values($a) : array();
}

function cluster_one_save_feynman_chapters(array $a): void {
    update_option('cluster_one_feynman_chapters', array_values($a), false);
}

/* v1.0.1 — HEREINGEZOGEN AUS gregor.php (heute faust.php), byte-identisch (Regel 2).
   SHA8 bce62318 vorher wie nachher. Ein Speicherzugriff auf eine Option,
   die vier Organe teilen, ist Boden — nicht Gregor. Gefunden von der
   Kleinrock-Instanz am 26.08.2026, hier nachgemessen. */
function cluster_one_get_gregor_events(): array {
    $e = get_option('cluster_one_gregor_events', array());
    return is_array($e) ? $e : array();
}

function cluster_one_save_gregor_events(array $events): void {
    update_option('cluster_one_gregor_events', array_values($events), false);
}


    /* v1.0.6 — dieselbe Bewegung wie in kern.php: die Meldung steht IM Riegel,
       nicht im Lader. Wer meldet, ist sonst nicht, wer laeuft. */
    /* Geriegelt aus demselben Grund wie in kern.php: wer speicher.php allein
       laedt, soll keine Leiche sehen. Die Reihenfolge im Lader misst
       meldung.php. */
    /* v1.12.0 — DIE MELDUNG IST NACH OBEN GEWANDERT, vor den Riegel
       (VERTRAG 27 §4). */
}
