<?php
/**
 * Cluster One — Modul Anti-Babel (DER SCHNITT, v0.0.100 -> Module).
 * v0.7.3: Datei umbenannt von uebersetzung.php — sie war die einzige, die
 * nach ihrer Funktion hiess statt nach ihrem Organ.
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
if (!function_exists('cluster_one_clean_langs')) {

/* 18.10.2026 (Neo): Riegel um den Aufruf, wie in kern.php und speicher.php —
   wer diese Datei ohne kasper.php laedt, soll keine Leiche sehen. */
if (function_exists('cluster_one_hier')) {
    cluster_one_hier('anti-babel');   // v0.9.72: Kasper
}

/* --- v1.4.0: DER UEBERSETZUNGS-LADER WOHNT JETZT HIER -----------------------
 *
 * Zugezogen aus cluster-one.php:213. Zwei Gruende, beide gemessen:
 *
 * 1. DORT WAR ER NUR FUER MAMMUT DA. Die vier Lights trugen den Katalog
 *    (languages/cluster-one-de_DE.mo, byte-gleich in allen) und luden ihn
 *    nie. Auf einem Knoten ohne Mammut fiel jeder Satz auf die englische
 *    Quelle zurueck — waehrend WordPress' eigener Katalog weiter deutsch
 *    sprach. Der Fingerabdruck stand am 31.08. auf Andreas' Bildschirm:
 *    „Last updated 6 Sekunden ago." (H140)
 *
 * 2. DORT UEBERLEBTE ER DEN NAECHSTEN SCHNITT NICHT. cluster-one.php und
 *    jede Light-Hauptdatei werden von floss.php ERZEUGT, und floss.php
 *    schreibt keinen Lader. Bismarck und Kleinrock hatten einen, solange sie
 *    von Hand geschnitten waren, und verloren ihn beim Neuschnitt — ohne
 *    dass ein Zeuge etwas sagte, denn erzeuger.php misst gegen Fixsterne,
 *    denen er ebenfalls fehlt (H147, Hollerith).
 *
 * WARUM AUSGERECHNET HIER. anti-babel.php ist allen fuenf Produkten zugesagt
 * und liegt byte-gleich in jedem (sha1 b6f5b812…, 31.08. gemessen). Ein
 * Organ, das ueberall mitreist, ist der einzige Ort, an dem ein Lader nicht
 * verlorengehen kann — und es ist das Organ, dem die Sprache ohnehin gehoert.
 *
 * WARUM INNERHALB DES RIEGELS. Liegen mehrere Kopien auf einem Knoten,
 * deklariert genau eine — dieselbe Regel, nach der auch die Funktionen
 * darunter genau einmal entstehen. Der Katalog ist in allen Kopien gleich,
 * also ist es folgenlos, welche gewinnt; sie meldet sich nur einmal an.
 *
 * dirname(dirname(...)) statt dirname(...): diese Datei liegt in includes/,
 * das Verzeichnis languages/ liegt daneben, eine Ebene hoeher.
 *
 * NICHT MITGEZOGEN: die Kopfzeile „Domain Path: /languages". Sie steht im
 * Plugin-Kopf, den floss.php erzeugt, und sie wird zur Laufzeit nicht
 * gebraucht — load_plugin_textdomain() bekommt den Pfad als Argument. Sie
 * ist eine Angabe fuer Uebersetzungswerkzeuge und bleibt Holleriths Sache.
 * Das hier ist also KEINE vollstaendige Reparatur von H147; es ist die
 * Haelfte, die im Quelltext wohnt.
 * ---------------------------------------------------------------------------
 * H195 — DER LADER IST AM 02.09.2026 WIEDER AUSGEZOGEN, UND ZWAR HIERHER NICHT
 * ZURUECK, SONDERN IN DIE HAUPTDATEI JEDES PRODUKTS.
 *
 * Alles oben Gesagte war richtig, SOLANGE ALLE KOPIEN DIESELBE DOMAENE TRUGEN.
 * Weg A (H194) hat aus einer gemeinsamen Zusage fuenf eigene gemacht — und der
 * Riegel stand noch da:
 *
 *     if (!function_exists('cluster_one_clean_langs')) {   <- gewinnt EINE Kopie
 *         add_action('init', fn() => load_plugin_textdomain('<meine Domaene>'…));
 *     }
 *
 * Auf einem Knoten mit mehreren Produkten gewinnt die zuerst geladene Kopie
 * (H43), und nur DEREN Domaene meldete sich an. Gemessen mit Werkzeug 32:
 * allein waren alle fuenf gruen, zusammen ZWEI VON FUENF.
 *
 *     Ein Riegel schuetzt vor Doppelung. Wo aus einer Sache fuenf geworden
 *     sind, verhindert er nicht die Doppelung, sondern vier Anmeldungen.
 *
 * Und es ist H169 in der Sprachschicht: was byte-gleich mitreist, darf nichts
 * enthalten, das je Produkt VERSCHIEDEN sein muss.
 *
 * GRUND 2 VON OBEN GILT WEITER UND IST DESHALB MITREPARIERT: floss.php schrieb
 * keinen Lader, und genau darum verloren Bismarck und Kleinrock ihn beim
 * Neuschnitt. Ab dem 02.09.2026 erzeugt floss.php ihn mit. Ohne diese zweite
 * Haelfte waere die Reparatur beim naechsten Schnitt wieder weg — H147 zum
 * zweiten Mal.
 *
 *     Wer einen Lader zurueck in eine erzeugte Datei legt, muss den Erzeuger
 *     mitnehmen, sonst hat er ihn nur bis zum naechsten Schnitt gelegt.
 *
 * GRUND 1 BLEIBT GEDECKT: jedes Produkt traegt seinen Lader jetzt SELBST, also
 * laedt auch ein Knoten ohne Mammut jeden Katalog.
 * ---------------------------------------------------------------------------
 */

/** Erlaubte Sprach-Codes; säubert + dedupliziert eine rohe Liste. */
function cluster_one_clean_langs(array $raw): array {
    $allowed = ['de', 'en'];
    $out = [];
    foreach ($raw as $l) {
        $l = strtolower(sanitize_text_field((string) $l));
        if (in_array($l, $allowed, true) && !in_array($l, $out, true)) {
            $out[] = $l;
        }
    }
    return $out;
}

/** Willkommene Kontaktsprachen: 1. Konstante, 2. Backend-Liste. */
function cluster_one_self_languages(): array {
    if (defined('CLUSTER_ONE_LANGUAGES') && is_array(constant('CLUSTER_ONE_LANGUAGES'))) {
        return cluster_one_clean_langs(constant('CLUSTER_ONE_LANGUAGES'));
    }
    $opts = get_option('cluster_one_settings', []);
    $raw  = isset($opts['languages']) && is_array($opts['languages']) ? $opts['languages'] : [];
    return cluster_one_clean_langs($raw);
}

/* v0.9.81 — DER KERN-KI-AUFRUF IST AUSGEZOGEN.
   cluster_one_ai_generate_text() steht jetzt in kern.php, im Boden, neben
   cluster_one_get_ai_identities(). Grund: von ihren vier Aufrufern haben
   zwei nie uebersetzt — platon.php und bismarck.php lassen damit eine KI im
   Raum ANTWORTEN. Die Funktion hiess nach dieser Datei und war es nicht.
   Dieselbe Verwachsung wie spiegel.php v0.9.69: zwei fremde Aufgaben, ein
   Dateiname, ein Praefix, sonst nichts.
   Gemessen vor dem Schnitt: der Rumpf rief nur cluster_one_ai_model(),
   cluster_one_count_keyed_ai_connectors() und cluster_one_sanitize_message()
   — alle drei schon unten. Der Umzug hat den Boden nichts gekostet.
   Was hier bleibt, ist Sprache und sonst nichts. */

/** Secure-Anti-Babel (v0.0.55): übersetzt EINEN Text, ohne dass die KI je
   erfährt, wer ihn geschrieben hat — bewusst kein Autor, kein Zeitstempel,
   kein Gesprächskontext im Prompt, nur der reine Satz. Nutzt den
   Kern-KI-Aufruf aus kern.php (v0.9.81: dorthin ausgezogen), also denselben
   Kostendeckel, dieselbe Anbieter-Pin-Logik, dieselbe Fehlerbehandlung wie
   eine KI-Antwort im Raum — eine Quelle, nicht zwei Kopien. */
function cluster_one_translate_text(string $cid, string $target_lang, string $text, int $akteur = 0): array {
    $sys = sprintf(
        'You are a precise translator, nothing else. Translate the following text into %s. '
        . 'Output ONLY the translation — no notes, no repetition of the original, no explanation, '
        . 'no name or signature. You are not told and must not guess who wrote this text.',
        $target_lang
    );
    return cluster_one_ai_generate_text($cid, $sys, $text, $akteur);
}

/** v0.0.97: ANTI-BABEL LIGHT — der Babel Fisch für Zweier-Chats mit dem
   SysOp. Sprache eines Menschen: eigenes Profil-Feld, Saat = Mutter-
   sprache des Knotens (die WP-Locale wäre Oberflächen-, nicht Herzens-
   sprache). Wachbedingung STRUKTURELL: genau zwei Menschen im benannten
   Kreis, einer davon der SysOp (Kostenträger-Regel), Übersetzer gesetzt,
   Sprachen verschieden. Übersetzt wird beim SENDEN, einmal, gespeichert
   am Nachrichtenobjekt (geerbte Kanten-Chat-Mechanik samt Spiegel-
   Maskierung); der Betrachter sieht SEINE Sprache, die fremde ist immer
   eingeklappt. Gemeinsame Drittsprache (Raum-Feld common_lang) wird nie
   übersetzt — der Fisch erkennt sie selbst (=SKIP=). Störung: der Fisch
   darf husten, aber nie das Gespräch verschlucken — Zustellung immer,
   ehrlich markiert. */
function cluster_one_user_lang(int $uid): string {
    $v = trim((string) get_user_meta($uid, 'cluster_one_lang', true));
    if ($v !== '') {
        return $v;
    }
    $opts = (array) get_option('cluster_one_settings', array());
    return trim((string) ($opts['mother_tongue'] ?? ''));
}

/** Der eigentliche Fisch-Biss: übersetzt in die Sprache des Empfängers,
   lässt die gemeinsame Drittsprache unangetastet (=SKIP=). */
function cluster_one_babel_translate(array $room, string $text, int $to_uid): array {
    $to     = cluster_one_user_lang($to_uid);
    $common = trim((string) ($room['common_lang'] ?? ''));
    $sys    = 'You are a precise translator, nothing else. Translate the following text into ' . $to . '.'
        . ($common !== '' ? ' If the text is already written in ' . $common . ' or in ' . $to . ', reply with exactly =SKIP= and nothing else.' : ' If the text is already written in ' . $to . ', reply with exactly =SKIP= and nothing else.')
        . ' Otherwise output ONLY the translation — no notes, no repetition of the original, no explanation, no name or signature. You are not told and must not guess who wrote this text.';
    /* v1.3.0 — DER GASTGEBER ZAHLT. Christian am 27.08.2026: der Fisch hat
       keinen Knopf, er uebersetzt beim SENDEN. Es gibt also niemanden, der
       drueckt — es gibt den, der den Raum aufgemacht hat. 'owner' steht seit
       jeher im Raum (bismarck.php), es braucht kein Feld und keine Migration.
       Und alte Raeume loesen sich von selbst: dort ist der Gastgeber der SysOp
       und der Uebersetzer seiner, also passt der Anschluss zum Akteur. */
    $res = cluster_one_ai_generate_text((string) ($room['translator'] ?? ''), $sys, $text,
        (int) ($room['owner'] ?? 0));
    if (!empty($res['ok']) && trim((string) $res['text']) === '=SKIP=') {
        return array('ok' => true, 'text' => '');
    }
    return $res;
}

}
