<?php
/**
 * Deinstallation — cluster-one-crockford.
 *
 * DIES IST EIN FLOSS. ERZEUGT, NICHT GESCHRIEBEN.
 * Wer hier etwas aendern will, aendert floss.php und erzeugt neu.
 *
 * =====================================================================
 *  WARUM ES DIESE DATEI GEBEN MUSS
 * =====================================================================
 *
 *  Bis heute gab es sie in den Kranz-Paketen nicht. Das hiess: der
 *  Raeumer, den dieses Produkt im Register anmeldet, wurde beim
 *  Loeschen NIE gerufen. Die Daten blieben liegen, und niemand sagte es.
 *
 *      Ein Raeumer, der sich anmeldet und nie gerufen wird, ist von
 *      „es gibt keinen" nicht zu unterscheiden.
 *
 *  Der Boden ist seit dem 17.09. dafuer gebaut. kern.php nennt den Fall
 *  woertlich in der Beschreibung von cluster_one_raeumen_mein():
 *
 *      Gefahrenzone im Produkt     EINEN Raeumer, den eigenen
 *      Knoten-Reset im Boden       ALLE
 *      uninstall.php eines Lights  EINEN, seinen eigenen
 *
 * =====================================================================
 *  WAS HIER GESTERN STAND, UND WARUM ES FALSCH WAR       (R-PAPIER-1)
 * =====================================================================
 *
 *  Hier stand ein Absatz mit der Ueberschrift „WARUM SIE NUR DREI
 *  DATEIEN LAEDT", und die Regel darin war meine:
 *
 *      „Sie muss sich holen, was sie braucht, und NICHT MEHR:
 *       kasper.php, kern.php und die Datei(en), die den Raeumer
 *       ANMELDEN."
 *
 *  DIE REGEL WAR SPARSAM UND FALSCH. Sie verwechselt zwei Fragen:
 *
 *      WER MELDET DEN RAEUMER AN   —  eine Datei
 *      WAS BRAUCHT DER RAEUMER     —  etwas ganz anderes
 *
 *  M109 hat den Unterschied gemessen, in zwei Paketen, und in beiden
 *  war die Folge eine andere Sorte Schaden:
 *
 *      KLEINROCK   cluster_one_kleinrock_raeumen() ruft
 *                  cluster_one_get_chat() und cluster_one_delete_chat().
 *                  Die wohnen in speicher.php, und speicher.php meldet
 *                  keinen Raeumer an, wurde also nicht geladen.
 *                      -> Call to undefined function, MITTEN IM LOESCHEN.
 *                      Die Narbe bliebe auf „begonnen" stehen.
 *
 *      CROCKFORD   cluster_one_crockford_raeumen() fragt
 *                  cluster_one_organ_wirt('umgebung') — also die
 *                  Kasper-Tabelle. umgebung.php meldet keinen Raeumer
 *                  an, wurde also nicht geladen, und meldet sich also
 *                  bei Kasper nicht.
 *                      -> der Raeumer haelt sich fuer fremd und zaehlt
 *                      NULL. Kein Fehler, keine Meldung, und
 *                      „gefunden: 1" sagt er trotzdem.
 *
 *  Gemessen, nicht geschlossen — M109 stellt zwei Welten aus echtem
 *  Code her und vergleicht die Zahlen:
 *
 *      Welt LAEUFT     alle Organdateien geladen   notizen 1
 *      Welt LOESCHEN   nur die Melder geladen      notizen 0
 *
 *  DIE LEHRE, und sie ist teurer als die drei Zeilen, die sie kostet:
 *
 *      Wer eine Ladeliste aus der Frage „wer meldet sich an" baut,
 *      hat die Frage „was wird gerufen" nicht gestellt.
 *
 * =====================================================================
 *  WAS SIE DESHALB LAEDT: ALLES, UND DANN VERGISST SIE EINMAL
 * =====================================================================
 *
 *  WordPress deaktiviert das Plugin und laedt DANN diese Datei — in
 *  einem Zustand, in dem vom Paket nichts geladen ist. Sie laedt
 *  deshalb DENSELBEN SATZ ORGANE IN DERSELBEN REIHENFOLGE wie der
 *  Lader. Eine zweite, kuerzere Liste waere eine zweite Wahrheit ueber
 *  dieselbe Sache (P1-12) — und genau diese zweite Wahrheit war der
 *  Fehler oben.
 *
 *      kasper.php   damit cluster_one_kasper_wirt() den Wirt nennt
 *      kern.php     das Register und cluster_one_raeumen_mein()
 *      anti-babel.php
 *               kranz.php
 *               crockford.php   (20.10.2026: bis 0.5.16 stand hier landkarte.php;
 *                                der Raeumer wohnt jetzt in crockford.php)
 *               menue.php
 *               speicher.php
 *               umgebung.php
 *               — derselbe Satz, den der Lader laedt,
 *               in derselben Reihenfolge
 *
 *  UND DANN cluster_one_kasper_vergessen(), UND DAS IST KEINE ZUGABE.
 *  Laeuft beim Loeschen noch ein anderes Cluster-One-Produkt, dann ist
 *  der Boden laengst geladen, plugins_loaded ist durch, und Kasper hat
 *  seine Tabelle GEMERKT — gemerkt ohne die Organe dieses Pakets, denn
 *  das war ja schon deaktiviert. Was diese Datei danach nachlaedt,
 *  kommt in dieser Tabelle nicht mehr an.
 *
 *  M109 hat auch das gemessen, mit warmem Merkzettel:
 *
 *      alles geladen, NICHT vergessen    Crockford: notizen 0
 *      alles geladen, vergessen          Crockford: notizen 1
 *
 *      Eine Tabelle, die vor dir gefuellt wurde, antwortet auf deine
 *      Frage mit dem Stand von vorher — und sieht dabei gesund aus.
 *
 *  DIE BENANNTE FOLGE: nach dem Vergessen steht dieses Paket fuer den
 *  Rest dieser Anfrage in der Kasper-Tabelle, obwohl es geloescht wird.
 *  Loescht WordPress mehrere Plugins in einem Zug, sieht der naechste
 *  Raeumer es dort. Es ist die kleinere von zwei Unwahrheiten, und sie
 *  steht hier, damit sie niemand fuer einen Zufall haelt.
 *
 * =====================================================================
 *  WARUM _mein UND NICHT _alle
 * =====================================================================
 *
 *  Geloescht wird EIN Plugin. Die anderen bleiben liegen und behalten
 *  ihre Daten. cluster_one_raeumen_mein() fragt nach dem WIRT — also
 *  nach dem Paket, das geloescht wird — und ruft nur die Raeumer, die
 *  darin liegen.
 *
 *      Ein Praefix, das den Namen EINES Dinges trug, traegt nach einer
 *      Teilung den Namen der FAMILIE.                   (R-PRAEFIX-1)
 *
 *  Deshalb steht in dieser Datei KEIN Optionsname, KEIN LIKE und KEINE
 *  rohe Abfrage. Das Register weiss, WER raeumen kann; es weiss nicht,
 *  WIE die Schluessel heissen, und genau deshalb kann es nicht veralten.
 *
 * =====================================================================
 *  DIE NARBE — H222, uebernommen von Paciolis uninstall.php
 * =====================================================================
 *
 *      Was der SysOp tut, bleibt sichtbar.
 *
 *  Diese Datei schreibt GENAU EINE Option, cluster_one_entfernt, und
 *  sie wird ausdruecklich NICHT mitgeloescht. Wer einen leeren Knoten
 *  vorfindet, muss sehen koennen, dass geraeumt wurde.
 *
 *  SIE WIRD ANGEHAENGT, NICHT ZUGEWIESEN. Vier Pakete koennen
 *  nacheinander geloescht werden; wer zuweist, loescht die Narbe des
 *  Vorgaengers.                                        (R-SAMMLER-2)
 *
 *  UND SIE WIRD ZWEIMAL GESCHRIEBEN, aus Paciolis Grund:
 *      vorher   die ABSICHT — stirbt der Lauf, gibt es trotzdem eine Spur
 *      nachher  die ZAHLEN — was wirklich geraeumt wurde
 */

defined('WP_UNINSTALL_PLUGIN') || exit;

require_once __DIR__ . '/includes/kasper.php';
require_once __DIR__ . '/includes/kern.php';
require_once __DIR__ . '/includes/anti-babel.php';
require_once __DIR__ . '/includes/feynman.php';   // 03.11.2026 (Neo), Runde Tau: Feynman ist Boden (1.22.0), wie in Kleinrock geladen
require_once __DIR__ . '/includes/kranz.php';
/* 20.10.2026 (Neo), 0.5.17: hier stand `require_once __DIR__ . '/includes/landkarte.php';`.
   Der Raeumer und seine Anmeldung sind nach crockford.php gezogen; ohne diese
   Zeile wuerde beim Loeschen kein Raeumer gefunden („kein-raeumer"). */
require_once __DIR__ . '/includes/crockford.php';
require_once __DIR__ . '/includes/menue.php';
require_once __DIR__ . '/includes/speicher.php';
require_once __DIR__ . '/includes/umgebung.php';

/* Der Merkzettel ist auf dem Knoten schon warm. Siehe oben. */
if (function_exists('cluster_one_kasper_vergessen')) {
    cluster_one_kasper_vergessen();
}

/* ---- die Absicht, bevor etwas passiert ------------------------------- */
$cluster_one_wirt = function_exists('cluster_one_kasper_wirt')
    ? cluster_one_kasper_wirt(__FILE__) : '';

$cluster_one_narbe = get_option('cluster_one_entfernt', array());
if (!is_array($cluster_one_narbe)) { $cluster_one_narbe = array(); }
$cluster_one_narbe[] = array(
    'wirt'  => $cluster_one_wirt,
    'wann'  => time(),
    'lage'  => 'begonnen',
    'zahlen' => array(),
);
$cluster_one_platz = array_key_last($cluster_one_narbe);
update_option('cluster_one_entfernt', $cluster_one_narbe, false);

/* ---- raeumen ---------------------------------------------------------- */
$cluster_one_erg = array('wirt' => $cluster_one_wirt, 'gefunden' => 0, 'zahlen' => array());
if (function_exists('cluster_one_raeumen_mein')) {
    $cluster_one_erg = cluster_one_raeumen_mein(__FILE__, true);
}

/* ---- und was wirklich geschah ----------------------------------------
 *
 * VIER LAGEN, NICHT DREI.                            (Crockford, 23.09.2026)
 *
 * Bis heute abend stand hier: gefunden > 0 -> „geraeumt". Crockford hat
 * nachgesehen, ohne dass ihn jemand darum gebeten hatte:
 *
 *     „'gefunden' ist 1, weil es den Raeumer GIBT. 'zahlen' ist
 *      {einstellungen: 0, notizen: 0}, weil er nichts genommen hat. Die
 *      Narbe sagt dann 'geraeumt' und daneben zwei Nullen."
 *
 *     Ein Raeumer, der nichts findet, und ein Raeumer, der nichts nehmen
 *     darf, sind zwei Lagen. Die Narbe hat fuer beide nur ein Wort.
 *
 * ER HAT RECHT, und die Reparatur ist nur die halbe Antwort — das gehoert
 * dazu gesagt:
 *
 *     'geraeumt'          es ist wirklich etwas weggegangen
 *     'nichts-genommen'   es gibt einen Raeumer, er lief, und er hat nichts
 *                         genommen. WARUM, sagt diese Datei NICHT: „es lag
 *                         nichts da" und „ich durfte nicht" sehen von hier
 *                         aus gleich aus.
 *     'kein-raeumer'      dieses Paket meldet keinen an
 *     'boden-fehlt'       der Boden liess sich nicht laden
 *
 *         Wer den Unterschied zwischen zwei Nullen kennt, ist der Raeumer.
 *         Er sagt ihn in SEINEN Zahlen — so wie Kleinrock einen Schluessel
 *         ganz weglaesst, wenn er nicht nachsehen konnte.
 *
 * DIE NARBE RAET NICHT. Sie sagt, was sie sehen kann, und laesst die
 * Zahlen daneben stehen.
 */
$cluster_one_narbe = get_option('cluster_one_entfernt', array());
if (!is_array($cluster_one_narbe)) { $cluster_one_narbe = array(); }
if (isset($cluster_one_narbe[$cluster_one_platz]) && is_array($cluster_one_narbe[$cluster_one_platz])) {
    if (!function_exists('cluster_one_raeumen_mein')) {
        $cluster_one_lage = 'boden-fehlt';
    } elseif ((int) $cluster_one_erg['gefunden'] <= 0) {
        $cluster_one_lage = 'kein-raeumer';
    } else {
        /* Ist ueberhaupt eine Zahl groesser als null? Geschachtelt, weil ein
           Raeumer seine Zahlen gruppieren darf (Bismarck tut es). */
        $cluster_one_etwas = false;
        $cluster_one_stapel = array((array) $cluster_one_erg['zahlen']);
        while ($cluster_one_stapel !== array()) {
            $cluster_one_feld = array_pop($cluster_one_stapel);
            foreach ($cluster_one_feld as $cluster_one_wert) {
                if (is_array($cluster_one_wert)) { $cluster_one_stapel[] = $cluster_one_wert; continue; }
                if ((is_int($cluster_one_wert) || is_float($cluster_one_wert)) && $cluster_one_wert > 0) { $cluster_one_etwas = true; break 2; }
            }
        }
        $cluster_one_lage = $cluster_one_etwas ? 'geraeumt' : 'nichts-genommen';
    }
    $cluster_one_narbe[$cluster_one_platz]['lage']   = $cluster_one_lage;
    $cluster_one_narbe[$cluster_one_platz]['zahlen'] = (array) $cluster_one_erg['zahlen'];
    update_option('cluster_one_entfernt', $cluster_one_narbe, false);
}
