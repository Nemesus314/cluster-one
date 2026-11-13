<?php
/**
 * Plugin Name:       Cluster One — Crockford
 * Plugin URI:        https://christianermisch.com/
 * Description:       Site context for AIs — what an AI needs to understand this site.
 * Version:           0.6.9
 * Requires at least: 7.0
 * Requires PHP:      7.4
 * Author:            Christian Ermisch (Nemesus)
 * Author URI:        https://christianermisch.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cluster-one-crockford
 * Cluster-One-Entfernt: Deaktivieren ist harmlos; dieses Modul haengt sich beim Abschalten nirgends ein. Entfernen loescht genau eine Option, und zwar die Notiz zur Umgebung mit allem, was zu ihr gespeichert ist (cluster_one_umgebung_notiz). Laeuft die Umgebung aus einer anderen Cluster-One-Installation, gehoert die Notiz ihr und bleibt stehen. Mehr schreibt dieses Modul nicht: alles andere liest es nur und laesst es stehen, auch die Daten des Bodens. Was geraeumt wurde, steht danach in der Option cluster_one_entfernt, die die uninstall.php dieses Pakets schreibt und absichtlich liegen laesst.
 * Cluster-One-Traegt: 9
 *
 * DIE ENTFERNT-ZEILE — Vertrag 16c §3, seit dem 03.09.2026.
 * Turing druckt sie, bevor ein Admin auf "Entfernen" geht, und wenn sie fehlt,
 * druckt es: "Dieses Modul sagt nicht, was es beim Entfernen loescht." Das ist
 * NICHT GEMESSEN, und der Admin entscheidet damit statt ohne.
 *     Ein Satz, den nur der schreiben kann, der die uninstall.php kennt,
 *     gehoert nicht in eine Vorlage, sondern an das Produkt.
 * Deshalb ist sie ein ARGUMENT und kein fester Text: die vier Lights loeschen
 * ihre Einstellungen, Pacioli loescht Identitaeten, Schluessel und Rollen — und
 * er schreibt seinen Satz selbst.
 *
 * DIE DOMAENE IST DER ORDNERNAME, UND DAS IST H194 (02.09.2026).
 * Hier stand bis Welle 2 ein festes `cluster-one`. Mammuts Frage war, ob der
 * Wert eine Konstante ist oder aus dem Slug faellt — davon haenge ab, ob Weg A
 * eine Zeile ist oder fuenf. Gemessen: er war ein Literal, und der Ordnername
 * stand eine Zeile darueber schon fertig da (cluster-one-crockford). Also war es hier
 * genau eine Zeile.
 * Das WordPress-Handbuch: "The text domain must match the slug of the plugin."
 * Passt sie nicht, kommt von translate.wordpress.org nie eine Uebersetzung an
 * — ohne Fehler, ohne Meldung. Umbertos Fund, und Turings Fehlerform von der
 * anderen Seite.
 *
 * KEIN "Domain Path: /languages" — UND DAS IST EINE ENTSCHEIDUNG.
 * Der Katalog liegt in /languages und wird von anti-babel.php mit genau
 * diesem Pfad geladen; zur Laufzeit braucht WordPress die Kopfzeile also
 * nicht. Sie ist eine Angabe fuer Uebersetzungswerkzeuge und fuer
 * wordpress.org — beides betrifft dieses Plugin heute nicht.
 * Entschieden von Hollerith am 01.09.2026, auf Rundbrief 08 §3. Sie darf
 * fehlen, aber sie darf nicht STILL fehlen: deshalb steht sie hier als
 * Absatz und nicht als Luecke. Wer sie will, aendert floss.php.
 *
 * ---------------------------------------------------------------------------
 * DIES IST EIN FLOSS. ERZEUGT, NICHT GESCHRIEBEN.
 *
 * Gebaut von floss.php aus Boden 1.16.0. Wer hier etwas aendern will,
 * aendert floss.php und erzeugt neu — sonst gibt es zwei Quellen fuer eine
 * Wahrheit, und die driften auseinander (P1-12).
 *
 * Ein Light hat zwei Schichten: den BODEN und den ZWECK. Dieses Plugin traegt
 * BEIDE, und wieviele Dateien das sind, sagt die Kopfzeile `Cluster-One-Traegt`
 * — gezaehlt am Rumpf beim Erzeugen, nicht am Gedaechtnis.
 *
 * HIER STAND BIS 0.3.4 DAS GEGENTEIL, und es stand da mit Nachdruck:
 *
 *     „Es traegt genau EINE Datei des Bodens: kasper.php. Nachgemessen,
 *      nicht angenommen … kern.php und speicher.php kommen, wenn der Zweck
 *      kommt; ein Floss, das Dateien traegt, die es nicht ruft, waere
 *      Ballast mit Wartungspflicht."
 *
 * Der Absatz war wahr, als das Floss der Boden allein war. Der Zweck ist
 * gekommen, der Absatz ist geblieben — und er sagt „nachgemessen", was ihn
 * ueberzeugender macht als den Rumpf, der danebensteht.
 *
 *     Ein Kommentar ist eine Behauptung ohne Zeugen — bis einer hinsieht.
 *
 * Post Dollys Fund vom 06.09.2026. Sein `zeuge-hauptdatei.php`, Pruefung F1,
 * haelt die Zahl ab jetzt gegen den Rumpf; wer den Absatz wieder wachsen
 * laesst, wird rot.
 *
 * DIE NUMMER DES ZEUGEN STEHT HIER ABSICHTLICH NICHT. Bis zum 07.09.2026
 * stand an dieser Stelle W38 — in JEDEM erzeugten Lader, also einmal je
 * Produkt und bei jedem Schnitt neu. Die Nummer ist inzwischen gewandert.
 *
 *     Ein Zeuge wird in erzeugtem Text bei seinem Dateinamen genannt und
 *     nicht bei seiner Nummer. Der Dateiname ueberlebt einen Umzug; die
 *     Nummer ist genau das, was gerade gewandert ist.  (Post Dolly, 07.09.)
 *
 * Dass die Zahl an zwei Stellen steht — Kopfzeile und
 * Rumpf — ist P1-12 und hier erlaubt, aus genau einem Grund:
 *
 *     Zwei Stellen fuer eine Zahl sind erlaubt, solange etwas sie gleich
 *     haelt. Ohne dieses Etwas sind sie ein Fehler, der noch nicht
 *     passiert ist.   (Post Dolly, 06.09.2026)
 *
 * WAS DER ALTE ABSATZ RICHTIG SAH und was deshalb hier bleibt: kasper.php hat
 * null ausgehende Aufrufe in andere Organe und ist damit die einzige Datei des
 * Hauses, die allein lauffaehig ist. Genau darum wird sie zuerst gefordert —
 * und deshalb steht sie oben und NICHT noch einmal in der sortierten Liste.
 *
 * WAS NIEMAND HIER ANFASSEN DARF: includes/kasper.php. Sie liegt byte-gleich
 * in jedem Cluster-One-Plugin und traegt eine eigene Fassungsnummer. Wer eine
 * Kopie aendert, erzeugt eine stille Divergenz — auf einem gemischten Knoten
 * gewinnt die zuerst geladene, und niemand sieht welche. Aenderungen kommen aus
 * Mammut, an genau einer Stelle.
 * ---------------------------------------------------------------------------
 */

defined('ABSPATH') || exit;

/* DER RIEGEL UM DIE FASSUNGSKONSTANTE — 12.09.2026, Welle nach 7d.
   Liegt dasselbe Produkt in ZWEI Ordnern auf einem Knoten, laeuft diese
   Zeile zweimal, und die zweite warf bisher eine Warnung:
       Constant CLUSTER_ONE_CROCKFORD_VERSION already defined
   (Der Wortlaut beginnt mit dem Namen der Sprache. Er steht hier deshalb
   ohne ihn: in einem Heredoc schliesst eine Zeile, die mit dem Schlusswort
   anfaengt, den Block — und dieser Kommentar liegt in einem.)
   Es braucht zwei Kopien DESSELBEN Produkts; Monolith und Light kollidieren
   nicht, weil ihre Konstanten verschieden heissen. Also eine Warnung, kein
   weisser Bildschirm — gemeldet wird sie trotzdem:
       Wer nur nach Toten sucht, findet den Tag nicht, an dem aus der
       Warnung einer wird.
   Der Riegel ist haus-ueblich: kasper.php haelt die Boden-Fassung genau so.
   ES GEWINNT DIE ZUERST GELADENE KOPIE, und das ist beabsichtigt: dieselbe
   Regel wie bei kasper.php. Wer die Fassung des WIRTS braucht, fragt nicht
   diese Konstante, sondern cluster_one_fassung_wirt() — der Kopf gewinnt
   ueber die Konstante, sobald er lesbar ist (VERTRAG 18).

   DIE SCHREIBWEISE IST NICHT GESCHMACK, SIE IST GEMESSEN. Hier stand erst
       defined('…') || define('…', '…');
   was PHP genau dasselbe bedeutet. Bismarcks zeuge-zwei-ordner.php hat den
   Lader daraufhin NICHT MESSEN KOENNEN: „der Guard war nicht zu finden",
   dreimal NICHT GEMESSEN. Er sucht den Anker als `if (!defined(`.
       Zwei Schreibweisen, die dieselbe Maschine meinen, sind fuer ein
       Werkzeug, das TEXT liest, zwei verschiedene Dinge.
   Also die Form des Hauses und nicht meine. */
if (!defined('CLUSTER_ONE_CROCKFORD_VERSION')) {
    define('CLUSTER_ONE_CROCKFORD_VERSION', '0.6.9');
}

/* Kasper zuerst. Er steht vollstaendig hinter einem function_exists-Riegel:
   liegt schon eine Kopie (ein Plugin, das frueher laedt), gewinnt die — und das
   ist per Entwurf gleichgueltig, weil sie byte-gleich sind. */
require_once __DIR__ . '/includes/kasper.php';

/* Die Anmeldung des Plugins selbst. Sie steht hier im Lader und darf das:
   zu ihr gehoert keine gleichnamige Organdatei, es gibt nichts zu verdraengen.
   Jede Meldung UEBER ein Organ gehoert dagegen in dessen eigene Datei (H70) —
   eine Meldung, die im Lader wohnt, wird beim Riegeln uebersehen.

   DER DRITTE WERT (12.09.2026, Bismarcks zweiter Fund, VERTRAG 27 §6 ohne
   Ausnahmeliste): wer AUSSERHALB eines Riegels meldet, nennt ihn. Diese
   Meldung steht in keinem Riegel und KANN in keinem stehen — es gibt keine
   gleichnamige Organdatei, nach der ein Riegel fragen koennte.
       Eine Voreinstellung, die man gemessen hat, ist eine Aussage.
       Eine, die man gewaehlt hat, ist eine Hoffnung.

   DIE LEERE FASSUNG — 13.09.2026, STAND C. Bismarcks Fund, und er liegt
   UNTER der Warnung, die ich gestern weggeriegelt habe:

       A  define ungeguardet, Konstante uebergeben   0.2.5 · 0.2.5   1 Warnung
       B  define geguardet,   Konstante uebergeben   0.2.5 · 0.2.5   0
       C  define geguardet,   '' uebergeben          0.2.5 · 9.9.9   0

   define() auf eine STEHENDE Konstante schlaegt fehl und laesst den ALTEN
   Wert stehen. Der Riegel allein (Stand B) nimmt nur die Warnung weg: die
   zweite Kopie meldet weiter die Fassungsnummer der ersten, jetzt lautlos.

       Der laute Fall war nie der ehrliche. Er war nur der hoerbare.
       Eine geliehene Nummer ist schlimmer als keine.   (H118)

   Mit der LEEREN Fassung liest Kasper sie am KOPF DES WIRTS, also je Ordner
   die eigene (VERTRAG 18). Die Konstante bleibt stehen — sie ist nach wie vor
   das, was fremder Code abfragt; sie ist nur nicht mehr das, woraus die
   Meldung ihre Zahl nimmt. */
cluster_one_hier('crockford-light', '', true);

/* ---------------------------------------------------------------------------
   DER REGISTERBLOCK — 16.09.2026, PROMPT-HOLLERITH-11 §2.

   Bis heute meldete sich KEINES der fuenf Pakete an: add_filter auf
   'cluster_one_produkt' kam in allen fuenf zusammen NULL Mal vor, waehrend
   cluster_one_produkte() im Boden seit dem Zug bereitsteht. Die Maschinerie
   lag da und wurde von niemandem benutzt.

       Ein Organ meldet sich. Ein Produkt meldet sich an.

   WARUM IM LADER UND NICHT IN EINER ORGANDATEI: auf dem Monolithen tragen
   alle Organe DENSELBEN Wirt. Fuenf Antworter in fuenf Organdateien
   schrieben dann unter denselben Schluessel, und wer zuletzt spricht,
   benennt das ganze Haus. Organdateien reisen in fremde Pakete; der Lader
   nie.

   DER SCHLUESSEL IST DER WIRT, ERFRAGT STATT GETIPPT. Der Boden schlaegt
   unter dem Wirt nach; ein Eintrag unter einem getippten Namen waere von
   einem nicht abgeschickten nicht zu unterscheiden. Leer heisst „nicht
   feststellbar" und nicht „keiner" — dann meldet dieser Lader nichts,
   statt etwas Falsches zu melden.

   'name'  DAS KURZE WORT, nicht der Kopfname. Das Dach setzt
           „Cluster One – " davor; stuende hier der lange Name, saege die
           Seite „Cluster One – Cluster One — Crockford".
   'nr'    VERTRAG 21 §2a, als echtes int — die Reihenfolge von
           RUNDBRIEF 21 §3, Christians Entscheidung vom 17.09.2026:
               Kleinrock 10 · Bismarck 20 · Crockford 30 ·
               Post Dolly 40 · Pacioli 60
           Zehnerschritte, damit ein sechstes Produkt dazwischenpasst;
           Pacioli auf 60, weil ein Schluesseltresor Infrastruktur ist
           und kein Ort, an den man geht.
           SIE STEHT NICHT IN DIESER DATEI. Sie kommt als --nr=<int>
           herein und wird hier nur weitergeschrieben.
   'slug'  DER SEITEN-SLUG, nicht der Produktname. Der Leser haengt ihn an
           admin.php?page= und prueft ihn mit cluster_one_menue_seite_da().
           Ein Produkt-Slug an dieser Stelle waere der falsche Wert der
           richtigen Sorte: nichts meldete ihn, der Eintrag erschiene still
           als „no page on this node".
           Hier NICHT 'cluster_one'. Seit VERTRAG 26 ist 'cluster_one' die
           DACH-Seite (cluster_one_render_dach_page ab zwei Produkten); die
           eigene Seite dieses Produkts traegt ihren eigenen Slug, hier
           'cluster_one_landkarte'. Ein Eintrag auf 'cluster_one' zeigte vom Dach auf
           das Dach.
   --------------------------------------------------------------------------- */
/* 0.5.34 (31.10.2026, Neo): die Woerter dieses Hauses fuer die Chronik (Boden 1.19.5,
   Filter cluster_one_chronik_woerter). Geschrieben werden die Ereignisse in crockford.php,
   in der Benutzerliste. */
add_filter('cluster_one_chronik_woerter', function ($c1_w) {
    if (!is_array($c1_w)) { return $c1_w; }
    $c1_w['crockford_released']  = __('Crockford released for a person', 'cluster-one-crockford');
    $c1_w['crockford_withdrawn'] = __('Crockford withdrawn from a person', 'cluster-one-crockford');
    return $c1_w;
});

add_filter('cluster_one_produkt', function ($c1_liste) {
    if (!is_array($c1_liste) || !function_exists('cluster_one_kasper_wirt')) {
        return $c1_liste;
    }
    $c1_wirt = cluster_one_kasper_wirt(__FILE__);
    if ($c1_wirt === '') { return $c1_liste; }
    $c1_liste[$c1_wirt] = array(
        /* DAS KURZE WORT, nicht der Kopfname — 14.09.2026, Mammuts
           Entscheidung. Das Dach setzt „Cluster One – " davor; stuende hier
           der lange Name, saege die Seite „Cluster One – Cluster One —
           Crockford". Gemessen an allen vier, bevor es geaendert wurde:
           4 von 4 trugen das Dachwort selbst. */
        'name' => 'Crockford',
        'nr'   => 30,
        /* DER SEITEN-SLUG, nicht der Produkt-Slug. Bis 16.09.2026 stand hier
           'cluster-one-crockford' — der falsche Wert der richtigen Sorte, und darum still. */
        /* 19.10.2026 (Neo): Bis heute 'cluster_one_landkarte'. Die Hauptseite
           dieses Produkts ist seit heute der Site-Kontext (crockford.php);
           die Landkarte haengt als Reiter daran. */
        'slug' => 'cluster_one_crockford',
        /* DIE CAPABILITY DER SEITE. Ohne sie traegt das Dach KEINEN Eintrag
           ein und meldet das — fail closed.
               Ein Eintrag darf sagen, wo etwas liegt. Er darf nicht
               entscheiden, wer hindarf. */
        /* 30.10.2026 (Neo), 0.5.33, RUNDE 35 — SEHEN DUERFEN ALLE, TUN NUR MIT RECHT.
           Bis 0.5.32 stand hier 'manage_options'. Die Seite selbst (crockford.php,
           cluster_one_crockford_seiten) verlangt seit dem 19.10. 'cluster_one_access';
           das Dach aber traegt den Untereintrag mit DIESER Cap ein (menue.php,
           add_submenu_page($c1_dachslug, …, $c1_cap, …)). Folge, gemessen auf der Main
           als MenschA: Kleinrock und Bismarck im Menue, Crockford nicht – die Seite war
           da, der Weg dahin fehlte. Jetzt dieselbe Cap wie die Seite; was ein Mitglied
           dort DARF, entscheidet die Seite (cluster_one_crockford_voll). */
        'cap'  => 'cluster_one_access',
        /* 27.10.2026 (Neo): WERFT UND BODEN-STAND – Erklaerung, kein Beweis (kern.php,
           cluster_one_produkte). Der Boden-Stand ist der, gegen den gebaut wurde; er
           wird mit jeder Boden-Runde fortgeschrieben, sonst sagt das Dach etwas Altes. */
        'werft' => 'Nemesus',
        'boden' => '1.23.0',
    );
    return $c1_liste;
});

/* ---------------------------------------------------------------------------
   DER KATALOG-LADER — H195, seit dem 02.09.2026 vom Erzeuger geschrieben.

   Er stand einmal hier, zog dann nach anti-babel.php (H140/H147) und ist
   heute zurueckgekommen. Der Grund fuer den Auszug war gut und gilt weiter:
   floss.php schrieb keinen Lader, also verloren Bismarck und Kleinrock ihn
   beim Neuschnitt, ohne dass ein Zeuge etwas sagte.

       Wer einen Lader zurueck in eine erzeugte Datei legt, muss den Erzeuger
       mitnehmen, sonst hat er ihn nur bis zum naechsten Schnitt gelegt.

   Genau deshalb steht er JETZT in der Vorlage. Und der Grund fuer die
   Rueckkehr ist H195: anti-babel.php reist byte-gleich in fuenf Produkten
   und liegt hinter einem !function_exists-Riegel — dort meldete auf einem
   Knoten mit mehreren Produkten nur die zuerst geladene Kopie ihre Domaene
   an (H43). Werkzeug 32 hat es nachgestellt: zwei von fuenf.

   Bewacht wird beides: zeuge-domaene-anmeldung.php (W32) laedt die Produkte
   miteinander und zaehlt die Anmeldungen; erzeuger.php haelt diese Vorlage
   gegen ihre Fixsterne. */
add_action('init', function () {
    load_plugin_textdomain( // phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound -- das Plugin liefert seine Uebersetzung in /languages selbst mit; WordPress laedt von allein nur Sprachpakete aus wp-content/languages
        'cluster-one-crockford',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
});

require_once __DIR__ . '/includes/anti-babel.php';
require_once __DIR__ . '/includes/feynman.php';   // 03.11.2026 (Neo), Runde Tau: Feynman ist Boden (1.22.0) — der Lernpfad reist in allen fuenf Paketen; dazu why.txt im Stamm
require_once __DIR__ . '/includes/kern.php';
require_once __DIR__ . '/includes/kranz.php';
require_once __DIR__ . '/includes/crockford.php';   // 19.10.2026: Site-Kontext und Randbedingungen, Crockfords eigene Seite
/* 20.10.2026 (Neo), 0.5.17: hier stand
       require_once __DIR__ . '/includes/landkarte.php';
   Die Landkarte ist nach Turing gezogen (Reiter Kasper); Steckbrief und
   Raeumer stehen jetzt in crockford.php. Traegt: 9 -> 8. */
require_once __DIR__ . '/includes/menue.php';
require_once __DIR__ . '/includes/speicher.php';
require_once __DIR__ . '/includes/umgebung.php';

/* --- KEIN MENUEPUNKT, UND DAS IST DIE ARBEIT VON VERTRAG 17 §2 -----------
   HIER STANDEN ACHTUNDACHTZIG ZEILEN: ein add_action('admin_menu', ...) mit
   zwei Zweigen und die Floss-Seite, die daran hing.

   DER ZWEITE ZWEIG WAR TOTER CODE, und Mammut hat es vor dem Vertrag
   gemessen statt vermutet: menue.php liegt in bismarck, kleinrock,
   post-dolly, umberto UND in Mammut. Also ist cluster_one_modul_da('menue')
   auf jedem Knoten wahr, auf dem irgendeines dieser Pakete laeuft — der
   add_menu_page()-Zweig darunter ist nie erreicht worden.

       Ein Riegel, der immer dasselbe antwortet, ist keine Bedingung.
       Er ist eine Behauptung mit if davor.   (Vertrag 17 §2)

   Mit ihm faellt die crc32-Positionsformel aus H173. Sie war richtig gedacht
   und hat nie eine Position vergeben.

   UND DER ERSTE ZWEIG WAR DER FEHLER: er hat unter „Cluster One" einen
   zweiten Punkt mit dem Namen desselben Plugins gehaengt — viermal, und bei
   bismarck mit dem Slug cluster_one_bismarck, der schon einer echten Seite
   gehoerte (H202). Die Floss-Seite selbst war eine Landkarte in klein
   (H206); die richtige steht ab Welle 4 im Kranz.

       Das Light meldet KEINEN eigenen Menuepunkt an.
       Sein Menue ist der Block aus menue.php, den es selbst mitbringt.

   WARUM DAS HIER STEHT UND NICHT IM GESCHNITTENEN LADER: H147. Eine
   Reparatur, die nur in der ERZEUGTEN Datei liegt, ist beim naechsten
   Schnitt weg. Wer den Erzeuger nicht anfasst, hat nichts repariert — er hat
   gewartet. */
