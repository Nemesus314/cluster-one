<?php
/**
 * Cluster One — Modul menue (v0.9.54).
 *
 * DAS MENUE GEHOERT KEINEM ORGAN.
 *
 * Bis 0.9.53 stand dieser Block in kleinrock.php — reiner Zufall der
 * Entstehung: Kleinrock war das erste Organ mit einer eigenen Seite, also
 * wuchs das Menue dort an. Die Abhaengigkeits-Matrix (konfiguration.php,
 * 0.9.54) hat den Preis sichtbar gemacht: Kleinrock rief elf Funktionen aus
 * Feynman, Kranz und Bismarck-Seiten — und ALLE hiessen cluster_one_render_*.
 * Kleinrock brauchte diese Organe nie. Es besass die Navigation, und eine
 * Navigation nennt naturgemaess jede Seite des Hauses.
 *
 * Die Folge war teuer: Jede Konfiguration, die das Netz mitnehmen wollte,
 * musste ALLE Seiten mitnehmen. Cluster One Light haette so nie geschnitten
 * werden koennen.
 *
 * Ein Menue ist keine Sache im Haus, es ist die ANSICHT auf das, was da ist.
 * Deshalb steht es jetzt fuer sich. Der Block ist byte-identisch umgezogen —
 * move don't rebuild, Regel 2. Keine Zeile Verhalten hat sich geaendert;
 * Golden bezeugt das.
 *
 * NAECHSTER SCHRITT (nicht hier): Solange dieser Block jede Seite fest
 * benennt, traegt er weiter die Kenntnis aller Organe — nur eben an einem
 * Ort, der sie haben darf. Wenn Module wirklich fehlen duerfen, meldet jedes
 * Organ seine Seiten selbst an, und diese Datei sammelt nur noch ein.
 * Das ist der Umbau, nicht der Umzug, und er kommt spaeter.
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
/* VERTRAG 27 §4 — DIE MELDUNG STEHT VOR DEM RIEGEL.
   Bis hierher stand cluster_one_hier() INNERHALB des Riegels: eine verdraengte
   Kopie dieser Datei schwieg dann auch ueber ihre eigene Existenz. Die
   Bedingung wird jetzt EINMAL gelesen und traegt beide Folgen — die Meldung
   und den Riegel.
       Ein Melder kennt sich selbst. „Verdraengt" ist eine BEZIEHUNG, und
       Beziehungen gehoeren dem Sammler.            (Maillart, 11.09.)
   Die Fassung bleibt LEER: Kasper liest sie am Wirt (H118), und eine
   geliehene Nummer ist schlimmer als keine. */
$cluster_one_menue_laeuft = !function_exists('cluster_one_menue_wenn');

/* 18.10.2026 (Neo): Riegel um den Aufruf, wie in kern.php und speicher.php. */
if (function_exists('cluster_one_hier')) {
    cluster_one_hier('menue', '', $cluster_one_menue_laeuft);
}

if ($cluster_one_menue_laeuft) {

/* v0.9.74 — EIN MENUEPUNKT, DESSEN ORGAN SCHWEIGT, WIRD NICHT ANGEMELDET.
 *
 * GEFUNDEN VON GALVANI, ERSTER VERSUCH: Mit abgeschaltetem Gregor stand
 * „Gregor (Kalender)" weiter im Menue. Ein Klick fuehrte auf eine graue Seite —
 * WordPress rief einen Rueckruf, den es nicht mehr gab.
 *
 * Christian hielt das fuer erwartetes Verhalten. Ist es nicht:
 *
 *     Ein Menuepunkt, der auf eine graue Seite fuehrt, ist ein gebrochenes
 *     Versprechen.
 *
 * Und es ist keine Gregor-Sache: 16 von 24 Menuepunkten haengen an einem
 * schaltbaren Organ. In einem Light, dem ein Organ fehlt, gilt dasselbe.
 *
 * GEFRAGT WIRD KASPER, NICHT function_exists(): Ein Symptom kann umziehen, eine
 * Tatsache nicht (siehe kasper.php). Und 'admin_menu' feuert lange nach
 * 'plugins_loaded' — der Saal ist voll, wenn hier gefragt wird.
 *
 * DAS OBERSTE MENUE IST AUSGENOMMEN. Seine Seite wohnt in Kleinrock; waere es
 * mitgeriegelt, verschwaende bei abgeschaltetem Kleinrock das GANZE Plugin aus
 * dem Menue — samt Kranz, Chronik und dem Weg zurueck. Stattdessen bekommt es
 * eine Ersatzseite, die sagt, was los ist. */
/* v1.12.1 (16.09.2026) — GEFRAGT WIRD JETZT, WER LAEUFT, NICHT WER SICH
   GEMELDET HAT. PROMPT-HOLLERITH-11 §3.3, auf Hollerithe Messung:

       cluster_one_modul_da()    isset($alle[$organ])       ANGEMELDET
       cluster_one_organ_wirt()  der Melder mit deklariert   WER LAEUFT

   Die zwei geben heute dieselbe Antwort, solange mindestens eine Kopie eines
   Organs deklariert — Kaspers eigene benannte Grenze sagt, dass der dritte
   Fall „gemeldet-aber-keiner-deklariert" heute nicht entstehen kann. Den
   Unterschied gibt es seit dem Boden-Zug trotzdem, also wird er benutzt:

       Ein Riegel, der die schwaechere Frage stellt und die richtige
       Antwort bekommt, hat recht aus Umstaenden und nicht aus Bau.
                                                     (Hollerith, 16.09.)

   DER RUECKFALL IST ABSICHT UND KEINE HOEFLICHKEIT: liegt unter diesem Paket
   ein Boden aelter als 1.12.0, gibt es cluster_one_organ_wirt() noch nicht.
   Ohne Rueckfall stuerbe hier jedes Menue mit einem Fatal — auf jedem Knoten,
   der diese Datei vor dem Boden bekommt.
       Ein neuer Zugang darf den alten ersetzen. Er darf nicht voraussetzen,
       dass er schon da ist.

   DIE AUSNAHME FUER DIE OBERSTE EBENE bleibt und hat jetzt ihren Wortlaut
   (PROMPT-HOLLERITH-10 §5.2):
       Ein Menuepunkt erscheint, wenn das Organ, das ihn beantwortet,
       DEKLARIERT hat. Die oberste Ebene ist davon ausgenommen: sie traegt
       das Dach und bekommt eine Ersatzseite, wenn ihr Organ fehlt — sonst
       verschwaende das ganze Plugin aus dem Menue.

   UND KERN BEKOMMT KEINEN RIEGEL (PROMPT-HOLLERITH-10 §5.3). Zwei Punkte —
   bismarck_settings und chronik — werden von kern.php beantwortet und stehen
   ohne Riegel da. Das bleibt so:
       kern.php ist Boden. Boden kann nicht fehlen — faellt er, faellt alles,
       und kein Menuepunkt wuerde es melden.
       Ein Riegel, der nie zufaellt, ist eine Zusage auf Kredit. */
function cluster_one_menue_wenn(string $organ, callable $anmelden): void {
    if ($organ === '' || (function_exists('cluster_one_organ_wirt')
                              ? cluster_one_organ_wirt($organ) !== ''
                              : cluster_one_modul_da($organ))) {
        $anmelden();
    }
}

/** Ersatzseite fuer die Hauptseite, wenn ihr Organ schweigt.
 *
 * v0.9.74: Die Ueberschrift geht ueber cluster_one_page_h1(), nicht ueber ein
 * getipptes <h1>. Der Zeuge „keine nackte Organ-Ueberschrift mehr" hat meinen
 * ersten Entwurf sofort kassiert — und zu Recht: Diese Zeile steht ueber JEDER
 * Seite des Plugins, und eine Seite, die sie umgeht, sieht anders aus als das
 * ganze Haus. */
function cluster_one_render_main_page_ersatz(): void {
    /* v1.8.1 (H101) — HIER STAND EINE STOERUNGSMELDUNG, UND ES WAR KEINE.
     *
     * Der Satz lautete: „The mesh module is not answering RIGHT NOW, so its
     * page cannot be shown." Auf einem Knoten, der Kleinrock nie wollte —
     * einem Post-Dolly-Knoten etwa — bekam ein Mensch, der ein Zitate-Plugin
     * installiert hat, eine Warnung ueber ein Modul, das er nie hatte. Und
     * die Ueberschrift derselben Seite hiess „Kleinrock": benannt nach dem,
     * was fehlt.
     *
     * WARUM „right now" NICHT NUR UNGENAU WAR, SONDERN UNMOEGLICH: Diese
     * Seite erscheint, weil Kasper schweigt. Kasper verschmilzt drei Faelle
     * mit Absicht — abgeschaltet, nicht installiert, verdraengt — und sein
     * eigener Kopf sagt warum: „Abschalten heisst schweigen." Aus diesem
     * EINEN Schweigen eine STOERUNG zu machen, erfindet die Unterscheidung,
     * die das Werkzeug darunter ausdruecklich nicht trifft.
     *
     *     Wer eine Auskunft weiterreicht, die drei Faelle zusammenfasst,
     *     darf nicht einen davon nennen.
     *
     * Die Seite sagt jetzt, was sie weiss: dieser Menuepunkt gehoert dem
     * Mesh, das Mesh antwortet hier nicht — und was hier ANTWORTET, steht
     * darunter. Damit ist sie ein Landepunkt statt einer Fehlermeldung. Was
     * fehlt, nennt sie; warum es fehlt, behauptet sie nicht. Die Grenze
     * steht auf dem Bildschirm und nicht nur hier im Kommentar.
     *
     * DIESE SEITE STIRBT MIT VERTRAG 14 (§0: H101 wird geloescht, nicht
     * repariert). Christian hat die Entscheidung am 01.09.2026 aufgehoben,
     * weil sich die Lage geaendert hat: Reparieren hiess damals, einen
     * Mechanismus zu erfinden; heute steht er im Boden. Bis der Umbau kommt,
     * stuende der alte Satz auf fremden Bildschirmen — und „bis dahin" hatte
     * kein Datum.
     *
     * v1.2.0: Hier stand ein Link auf Galvani — die Seite mit den Schaltern,
     * auf der man ein stumm geschaltetes Organ zurueckholen konnte. Es gibt
     * keine Schalter mehr, also gibt es nichts zurueckzuholen. Der Link
     * musste weg, und zwar HIER in Mammuts Quelle: menue.php liegt auch in
     * den Lights, und ein Light, das auf eine Seite zeigt, die es nicht mehr
     * gibt, zeigt ins Leere. */
    echo '<div class="wrap">' . cluster_one_page_h1('Cluster One'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase)
    echo '<div class="notice notice-info"><p>'
       . esc_html__('This menu entry belongs to the mesh module, and the mesh module does not answer on this node. Everything else in this menu works.', 'cluster-one-crockford')
       . '</p><p style="opacity:.8;">'
       . esc_html__('Switched off, not installed, or carried by another plugin — from here these look the same, on purpose.', 'cluster-one-crockford')
       . '</p></div>';

    /* Was ANTWORTET. Der Boden weiss es, und eine Seite, die nur das Fehlende
       nennt, laesst einen Menschen im Dunkeln ueber das, was er hat. */
       /* cluster_one_kasper() gibt organ => array('fassung','wirt') zurueck —
          nachgesehen in kasper.php:311, nicht angenommen. Mein erster Entwurf
          hier hatte einen Zweig fuer eine Unterstruktur ['organe'], die es
          nicht gibt: eine Vermutung ueber eine Datenform, die zwei Dateien
          weiter nachzulesen war. Genau die Gewohnheit, die dieser Schnitt
          abstellt.
          Und der Aufruf ist sicher: diese Seite rendert im Admin, also lange
          nach 'plugins_loaded' — Kaspers benannte Grenze greift hier nicht. */
    $c1_organe = function_exists('cluster_one_kasper') ? array_keys(cluster_one_kasper()) : array();
    sort($c1_organe);
    if ($c1_organe) {
        echo '<p><strong>' . esc_html__('What does answer here:', 'cluster-one-crockford') . '</strong> ';
        $c1_erste = true;
        foreach ($c1_organe as $c1_o) {
            echo ($c1_erste ? '' : ' · ') . '<code>' . esc_html((string) $c1_o) . '</code>';
            $c1_erste = false;
        }
        echo '</p>';
    }
    echo '</div>';
}

/* ---------------------------------------------------------------------------
   DIE DACHSEITE — sie entsteht mit VERTRAG 26 und existiert erst ab ZWEI
   Produkten. Bei einem fuehrt der oberste Punkt auf das Produkt selbst
   (cluster_one_dach_umleitung), und dann gibt es nichts zu ueberdachen.

   WAS SIE IST: eine Liste dessen, was auf diesem Knoten liegt, in der
   Ordnung aus §2a. Nicht mehr.

   WAS SIE AUSDRUECKLICH NICHT IST: eine Schaltzentrale. Galvani hatte
   Schalter und ist im August auseinandergenommen worden; ein Dach mit
   Knoepfen waere derselbe Bau unter neuem Namen.

       Eine Seite, die zaehlt, was da ist, ist eine Auskunft. Eine, die
       eingreift, ist ein Apparat — und Apparate werden gezaehlt.

   UND SIE VERSPRICHT KEINEN WEG, DEN ES NICHT GIBT: ein Produkt, das einen
   Slug angemeldet hat, ohne dass auf diesem Knoten eine solche Seite
   existiert, steht hier OHNE Verknuepfung und mit dem Satz, der sagt warum.
   Dieselbe Regel wie bei den Menuepunkten, an derselben Wahrheit gemessen.

   BENANNTE GRENZE: die Fassung je Produkt steht hier NICHT. Das Register
   traegt name · nr · slug und keine Fassung; Kasper traegt Fassungen je
   ORGAN. Eine Produktfassung waere hier gerechnet und nicht gemeldet.
   --------------------------------------------------------------------------- */
/* 27.10.2026 (Neo), Boden 1.17.6 — DIESE SEITE IST EIN RUECKFALL, KEIN ORT.
   Seit Christians Entscheidung vom 16.09. (cluster_one_dach_ziel: der Klick auf
   „Cluster One" geht direkt aufs erste Produkt) wird sie nur gezeigt, wenn das
   erste Produkt keine Seite hat – auf keinem der vier Knoten der Fall. Nemesus,
   27.10.: das Dach bleibt ohne Tuer. Was hier gebaut wurde (Werft-Zeilen 1.17.3,
   Blase 1.17.4, „This node:"-Links 19.10.), steht seitdem an Orten, die man
   erreicht: Stempel je Produktseite (cluster_one_stempel), Turings Kasper, der
   sichtbare Untereintrag Knoten-Einstellungen (1.17.5). HIER NICHTS MEHR BAUEN.
   Die Seite bleibt, weil ein Rueckfall ohne Seite ein grauer Schirm waere. */
function cluster_one_render_dach_page(): void {
    cluster_one_page_gate();

    echo '<div class="wrap">' . cluster_one_page_h1('Cluster One'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase)

    if (!function_exists('cluster_one_produkte')) {
        /* Kann auf einem Knoten mit diesem menue.php nicht eintreten — der
           Rueckruf wird nur gewaehlt, wenn die Zaehlung >= 2 ergeben hat.
           Er steht hier trotzdem: ein Rueckruf, den WordPress unter seinem
           Namen speichert, kann nach einem Teilaustausch allein uebrig
           bleiben, und eine graue Seite waere dann das Ergebnis. */
        echo '<div class="notice notice-warning"><p>'
           . esc_html__('This node cannot list its products: the shared floor is older than the product register.', 'cluster-one-crockford')
           . '</p></div></div>';
        return;
    }

    $c1_p = cluster_one_produkte();
    /* HIER STAND EINE ZWEITE SORTIERUNG. Sie ist am 16.09.2026 gefallen,
       PROMPT-HOLLERITH-11 §3.2, auf eine Messung von Hollerith:

           kern.php :5300   uasort nach 'nr', bei Gleichstand nach 'name'
           hier             uasort nach 'nr', bei Gleichstand gar nicht

       Zwei Sortierungen ueber EINE Wahrheit. Heute gaben sie dasselbe her;
       der Tag, an dem sie es nicht tun, ist der Tag, an dem niemand weiss,
       welche gilt.

           DER BODEN SORTIERT. Die Leserseite nimmt die Reihenfolge, wie
           sie kommt.                                  (VERTRAG, §5.1a)

       Und ihr PHP_INT_MAX-Zweig war ohnehin unerreichbar: kern.php :5247
       castet 'nr' auf int mit Vorgabe 999, bevor der Wert hier ankommt —
       is_int() war an dieser Stelle immer wahr. Ein Zweig, den nichts
       erreicht, ist keine Vorsorge; er ist eine Behauptung ueber einen
       Fall, den jemand anders schon entschieden hat. */

    echo '<p>' . esc_html(sprintf(
        /* translators: %d is the number of Cluster One products installed on this node. */
        _n('%d Cluster One product is installed on this node.',
           '%d Cluster One products are installed on this node.',
           count($c1_p), 'cluster-one-crockford'),
        count($c1_p)
    )) . '</p>';

    echo '<ul class="ul-disc">';
    foreach ($c1_p as $c1_wirt => $c1_v) {
        $c1_name = (is_array($c1_v) ? (string) ($c1_v['name'] ?? '') : '');
        $c1_slug = (is_array($c1_v) ? (string) ($c1_v['slug'] ?? '') : '');

        echo '<li>';
        if ($c1_name === '') {
            /* H218: der fehlende Name ist schon gemeldet; hier steht die
               FOLGE. Der ORDNER wird genannt, weil er das Einzige ist, was
               feststeht — und er wird als Pfad ausgewiesen, nicht als Name. */
            echo '<em>' . esc_html__('(no name announced)', 'cluster-one-crockford') . '</em> '
               . '<code>' . esc_html((string) $c1_wirt) . '</code>';
        } elseif ($c1_slug !== '' && cluster_one_menue_seite_da($c1_slug)) {
            echo '<a href="' . esc_url(admin_url('admin.php?page=' . $c1_slug)) . '">'
               . esc_html($c1_name) . '</a>';
        } else {
            echo esc_html($c1_name) . ' <span style="opacity:.7;">— '
               . esc_html__('no page on this node', 'cluster-one-crockford') . '</span>';
        }
        /* 27.10.2026 (Neo), Boden 1.17.3 — DIE WERFT, hinter dem Namen, leise. Begruendung
           in kern.php bei cluster_one_produkte(). „unbekannt" ist ein Wort, keine Warnung. */
        if (is_array($c1_v)) {
            $c1_werft = (string) ($c1_v['werft'] ?? '');
            $c1_boden = (string) ($c1_v['boden'] ?? '');
            echo ' <span style="opacity:.55;font-size:.9em;">· '
               . ($c1_werft !== ''
                    /* translators: %s: who built this product (a name the product declares about itself) */
                    ? esc_html(sprintf(__('shipyard: %s', 'cluster-one-crockford'), $c1_werft))
                    : esc_html__('shipyard: unknown', 'cluster-one-crockford'))
               . ($c1_boden !== ''
                    /* translators: %s: the version of the shared floor this product was built against */
                    ? ' · ' . esc_html(sprintf(__('built on floor %s', 'cluster-one-crockford'), $c1_boden))
                    : '')
               . '</span>';
        }
        echo '</li>';
    }
    echo '</ul>';

    /* 19.10.2026 (Neo) — DIE BODEN-SEITEN BEKOMMEN EINEN WEG. Einstellungen,
       Profil und System gehoeren keinem Produkt; bis heute fuehrte kein
       Reiter zu ihnen ausser dem eines fremden Produkts (Crockfords
       Landkarte zeichnete die Kranz-Leiste). Das Dach ist die Seite der
       Familie, also stehen sie hier. Dieselbe Quelle wie die Leiste
       (cluster_one_kranz_reiter); nur, was kein Produkt beansprucht. */
    if (function_exists('cluster_one_kranz_reiter')) {
        $c1_links = array();
        foreach (cluster_one_kranz_reiter('') as $c1_t) {
            if (!is_array($c1_t) || empty($c1_t['url']) || empty($c1_t['label'])) { continue; }
            $c1_links[] = '<a href="' . esc_url((string) $c1_t['url']) . '">' . esc_html((string) $c1_t['label']) . '</a>';
        }
        if ($c1_links !== array()) {
            echo '<p>' . esc_html__('This node:', 'cluster-one-crockford') . ' ' . implode(' · ', $c1_links) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- jedes Glied von $c1_links ist drei Zeilen hoeher mit esc_url/esc_html gebaut
        }
    }

    echo '<p style="opacity:.8;">'
       . esc_html__('This page counts what is here. It does not switch anything on or off.', 'cluster-one-crockford')
       . '</p>';
    echo '</div>';
}

add_action('admin_menu', function () {
    // Top-level: Cluster One. Capability 'cluster_one_access' (v0.0.61):
    // via map_meta_cap auf cluster_one_can_access() abgebildet — Menü und
    // Seiten existieren für Nicht-Mitglieder nicht mehr. Zusätzlich ruft
    // jede Seiten-Render-Funktion cluster_one_page_gate() auf (Defense-in-
    // depth; ersetzt die alte 'cluster_one_access'-Öffnung aus v0.0.39).
    /* v1.9.14 (VERTRAG 26 · NACHTRAG 26a/26b, 10.09.2026) — WESSEN PUNKT IST
     * DER OBERSTE?
     *
     * Bis hierher hiess er bedingungslos „Cluster One". Christian am 10.09.,
     * und die Frage hat meine eigene Empfehlung kassiert:
     *
     *     Ein Knoten mit „Cluster One" im Menue sieht aus wie jeder andere.
     *     Man kann dem Menue nicht ansehen, ob dort der Monolith laeuft oder
     *     ein einzelnes Light.
     *
     * Genau diese Frage hat Kasper seinen ganzen Wirt-Apparat gekostet. Steht
     * der Name im Punkt, beantwortet das MENUE sie — fuer jeden Screenshot
     * und jede Supportfrage.
     *
     *     ein Produkt      „Cluster One – <Name>"
     *     zwei oder mehr   „Cluster One"   (es gibt mehr als eines zu nennen)
     *
     * DREI SACHEN, DIE HIER AUSDRUECKLICH NICHT GESCHEHEN:
     *
     * 1  DER SLUG BLEIBT. 'cluster_one' steht 14x als page=cluster_one im
     *    Baum; der Anzeigename kein einziges Mal. Kein Lesezeichen bricht.
     *    cluster_one_menue_pfad() liest den Namen aus dem LEBENDEN Menue —
     *    die einundzwanzig Wegweiser-Saetze folgen von selbst.
     *
     * 2  KEIN ORDNERNAME WIRD GELESEN. Der Name kommt aus der Anmeldung
     *    (cluster_one_produkte()). Ein Ordnername ist ein Pfad.
     *
     * 3  NAME === FAMILIENNAME -> KEIN ZUSATZ, und das ist die REGEL und
     *    kein Sonderweg fuer den Monolithen. Maillart hat am 10.09. gemessen,
     *    dass beide Boeden — „Name gleich" und „Ordner gleich cluster-one" —
     *    in jeder bisherigen Welt Zeile fuer Zeile dasselbe tun; erst ein
     *    Wirt, der anders heisst und trotzdem „Cluster One" anmeldet, trennt
     *    sie. Verglichen werden zwei ANZEIGENAMEN, kein Pfad.
     *
     *        Ein Dach ueber der Familie traegt den Namen der Familie.
     *        „Cluster One – Cluster One" ist kein Name, das ist ein Stottern.
     *
     *    „Zusammenfallen" heisst Zeichenkettengleichheit. Ein Produkt, das
     *    „cluster one" klein anmeldet, bekommt „Cluster One – cluster one" —
     *    ein anders geschriebener Name IST ein anderer. (Maillart, 10.09.)
     *
     * UND WENN DAS EINZIGE PRODUKT SCHWEIGT: der Punkt heisst schlicht
     * „Cluster One", OHNE haengenden Gedankenstrich, und die Auslassung wird
     * gemeldet.
     *        Der ORT faellt aus dem Satz, nicht der Satz.  (Bismarck, 09.09.)
     *    Ein „Cluster One – " ist Kleinrocks „This is managed under ." noch
     *    einmal.
     *
     * BENANNTE GRENZE: gibt es cluster_one_produkte() nicht — ein Light mit
     * aelterem Boden —, bleibt der Punkt wie bisher. Kein Riegel, der eine
     * Seite versteckt, sondern ein Rueckfall auf den alten Namen. */
    /* MAILLART, 10.09., VIERTE STILLE STELLE: hier stand ein
       if (function_exists(…)) OHNE else. Auf der Platte erzeugten damit DREI
       Ursachen dieselbe Menuezeile „Cluster One", und zwei davon schwiegen.
           Die Asymmetrie stand in derselben Funktion: den leeren Wirt nenne
           ich als Grenze, die fehlende Funktion uebergehe ich stumm —
           dieselbe Art von Grenze.
       Der Rueckfall bleibt (ein Riegel wuerde eine Seite verstecken), aber er
       ist nicht mehr still. */
    $c1_dach = 'Cluster One';
    /* v1.9.14 — DIE ZAHL WIRD VOR DEM RIEGEL GESETZT, NICHT IN IHM.
       Sie wird weiter unten am obersten Punkt zum zweiten Mal gebraucht. Bis
       hierher entstand sie nur im else-Zweig — auf einem alten Boden waere
       sie dort unten UNDEFINIERT gewesen, und PHP haette daraus stillschweigend
       eine Null gemacht. Das Ergebnis waere zufaellig richtig gewesen (der
       Rueckfall) und die Warnung im Protokoll haette niemand gelesen.

           Eine Zahl, die nur in einem Zweig entsteht und in beiden gebraucht
           wird, ist keine Zahl, sondern eine Wette auf den Zweig. */
    $c1_zahl = 0;
    if (!function_exists('cluster_one_produkte')) {
        if (function_exists('_doing_it_wrong')) {
            /* DER GRIFF STEHT AUCH HIER VORNE — Maillart, 10.09.: ein Zeuge,
               der auf Schweigen prueft, muss fragen koennen „nichts ueber
               DIESE Sache", sonst bricht er an jeder neuen Ursache. */
            _doing_it_wrong('cluster_one_menue_dach',
                '[alter-boden] Der Boden kennt cluster_one_produkte() nicht '
              . '(aelter als 1.9.14). Der oberste Menuepunkt heisst deshalb '
              . 'wie frueher und sagt nicht, was auf diesem Knoten liegt.',
                '1.9.14');
        }
    } else {
        $c1_produkte = cluster_one_produkte();
        $c1_zahl = count($c1_produkte);
        if ($c1_zahl === 1) {
            $c1_wirt    = (string) key($c1_produkte);
            /* v1.9.14 — DIE NEUE FORM: das Register gibt seit dem Slug
               `wirt => array('name','nr','slug')`. Bis hierher stand hier
               (string) reset(...) — auf ein array angewandt waere das in PHP
               eine WARNUNG und der Text „Array", nicht ein leerer Name.
               Laut, nicht still, und trotzdem falsch: deshalb wird das Feld
               beim NAMEN genommen und nicht an der Stelle in der Liste.

                   Wer ein Feld an seiner Position nimmt statt an seinem
                   Namen, hat die Reihenfolge zur Wahrheit erklaert. */
            $c1_eintrag = reset($c1_produkte);
            $c1_name    = is_array($c1_eintrag) ? (string) ($c1_eintrag['name'] ?? '') : '';
            $c1_slug    = is_array($c1_eintrag) ? (string) ($c1_eintrag['slug'] ?? '') : '';
            unset($c1_slug);   /* der SLUG wird hier noch nicht gebraucht: der
                oberste Punkt behaelt 'cluster_one'. Er steht hier als
                gemessene Auskunft, damit der Leser sieht, dass das Register
                ihn traegt — die Produkteintraege sind die zweite Haelfte. */
            if ($c1_name === '') {
                /* Das Register hat es schon gemeldet; hier steht die FOLGE
                   fuer den Menuepunkt, nicht die Ursache noch einmal. */
                $c1_dach = 'Cluster One';
            } elseif ($c1_name !== $c1_dach) {
                /* „Zusammenfallen" heisst ZEICHENKETTENGLEICHHEIT: ein
                   Produkt, das „cluster one" klein anmeldet, bekommt
                   „Cluster One – cluster one". Ein anders geschriebener Name
                   IST ein anderer. (Maillart, 10.09.) */
                $c1_dach = $c1_dach . ' – ' . $c1_name;
            }
        }
        /* NULL wird NICHT wie „mehr als eins" behandelt. Das Register hat den
           Fall bereits gemeldet — der Punkt heisst „Cluster One", und das ist
           hier ein RUECKFALL und keine Aussage ueber die Anzahl. */
    }
    /* v1.9.14 — WOHIN DER OBERSTE PUNKT FUEHRT.
       Bis hierher war er Kleinrocks Mesh-Seite: derselbe Slug, zwei Rollen.
       Ab zwei Produkten kann er das nicht bleiben, sonst zeigt das Dach auf
       ein Produkt. Die Bedingung kommt aus DERSELBEN Zaehlung, die oben den
       Namen gesetzt hat — eine Zaehlung, zwei Folgen, einmal gelesen. */
    /* v1.12.6 (17.09.2026) — HIER WIRD DER SLUG VERGEBEN, und ab jetzt aus
       der Quelle. Das war mein eigener Posten aus PROMPT-19 §4.2: „:441 ist
       die Stelle, die den Slug tatsaechlich VERGIBT. Sie sollte die Quelle
       rufen, nicht umgekehrt." */
    add_menu_page(
        $c1_dach, $c1_dach, 'cluster_one_access', cluster_one_menue_dach_slug(),
        ($c1_zahl >= 2)
            ? 'cluster_one_render_dach_page'
            : (cluster_one_modul_da('kleinrock')
                ? 'cluster_one_render_main_page'
                : 'cluster_one_render_main_page_ersatz'),
        'dashicons-networking', 58
    );

    /* v1.9.14 — WOHIN DIE ZWEI EIGENEN PRODUKTEINTRAEGE GEHOEREN.
       W60 hat es gefunden, sobald er den SLUG mass statt der Beschriftung:
       Kleinrocks und Bismarcks Eintraege standen bisher IMMER da, gebunden
       allein an ihr Organ. Bei EINEM Produkt ist das derselbe Weg zweimal —
       oben „Cluster One – Kleinrock", darunter „Kleinrock (Mesh)", und beide
       fuehren auf dieselbe Seite.

           Der Eintrag war die ganze Zeit da. Was fehlte, war ein Zeuge,
           der auf den WEG sieht statt auf den NAMEN.

       ABER DIE SEITE DARF NICHT VERSCHWINDEN. Kleinrock hat am 11.09.
       siebzehn Stellen gezaehlt, die nach dem Schnitt `cluster_one_mesh`
       sagen — elf davon Umleitungen nach einem Knopfdruck. Faellt die
       Anmeldung bei einem Produkt weg, zeigen alle siebzehn ins Leere.

           Ein Eintrag ist eine Einladung. Eine Anmeldung ist eine Adresse.
           Wer die Einladung zurueckzieht, darf die Adresse nicht mitnehmen.

       Also: bei EINEM Produkt wird dieselbe Seite VERSTECKT angemeldet
       (Eltern null) — sie existiert, sie steht nur nicht in der Leiste, und
       der oberste Punkt leitet auf sie um (cluster_one_dach_umleitung).
       Neunzehn versteckte Anmeldungen traegt dieses Haus ohnehin schon
       (Maillart, 11.09.); das ist dieselbe Bauart, keine neue.

           VERTRAG 25 §1 in beide Richtungen: der alte Weg fuehrt hin,
           der neue existiert vorher.

       BENANNTE GRENZE: dass WordPress bei EINEM sichtbaren Untereintrag
       selbst einen ersten anlegt und dafuer den Menue-Titel nimmt, ist
       Maillarts Messung vom 11.09. und nicht meine. Bei null sichtbaren
       Untereintraegen stellt sich die Frage nicht — das ist der Grund,
       warum „versteckt" hier besser ist als „gar nicht". */
    /* HIER STAND DIE WAHL DES ELTERNTEILS FUER DIE PRODUKTPUNKTE, und sie
       ist mit dem Schnitt vom 16.09.2026 tot: alle fuenf sind jetzt
       versteckt (add_submenu_page(null, …)), also hat kein Aufruf mehr
       ein Elternteil zu waehlen. Die Variable wurde ENTFERNT und nicht
       stehengelassen —
           Eine Variable, die niemand mehr liest, sieht beim naechsten
           Lesen aus wie eine vergessene Verwendung.
       Die Zahl $c1_zahl bleibt: sie entscheidet weiter, ob der oberste
       Punkt das Dach traegt oder die Mesh-Seite. */

    // Kleinrock (Mesh) = erster Reiter (teilt den Slug der Hauptseite -> Knotenliste „Netz").
    /* v0.9.79: DER FUENFZEHNTE MENUEPUNKT.
       Die Galvani-Probe bezeugt seit 0.9.74, dass jeder Menuepunkt an einem
       schaltbaren Organ hinter cluster_one_menue_wenn() steht — vierzehn
       Punkte, vierzehn Naehte. Dieser hier war der fuenfzehnte und stand nicht
       in ihrer Tabelle: die Hauptseite darueber hat einen ERSATZ, und ich hatte
       daraus geschlossen, ihr Reiter sei damit auch versorgt.

       Er war es nicht. Bei stillem Kleinrock zeigte die Hauptseite ihren
       Ersatz, und der Reiter direkt darunter fuehrte auf eine graue Seite —
       genau der Fund, den Galvani in H38 bei Gregor gemacht hat, einmal
       uebersehen.

           Ein Ersatz fuer die Seite ist kein Ersatz fuer ihren Reiter. */
    cluster_one_menue_wenn('kleinrock', function () {
        add_submenu_page(
            null,
            'Cluster One',                 // Eigenname, nicht übersetzt
            'Kleinrock (Mesh)',            // Anzeige: Ehrenname + Sache; Slug: cluster_one_mesh
            'cluster_one_access', 'cluster_one_mesh', 'cluster_one_render_main_page'
        );
    });

    // Bismarck (Reaktor) = zweite Ebene. Eigenname, nicht übersetzt.
    cluster_one_menue_wenn('bismarck-seiten', function () {
        add_submenu_page(
            null,
            'Cluster One – Bismarck',
            'Bismarck (Reaktor)',
            'cluster_one_access', 'cluster_one_bismarck', 'cluster_one_render_bismarck_page'
        );
    });

    // Kranz (Settings) = der Kontrollraum. Landet auf Kleinrocks Einstellungen
    // (erster Reiter); Bismarck-Einstellungen und Profil sind die anderen
    // beiden Reiter (siehe cluster_one_render_kranz_tabs). Seit v0.0.40
    // vollständig: Mesh- und Bismarck-Einstellungen sind komplett
    // umgezogen, nicht mehr nur Profil.
    // Bismarck-Einstellungen: eigene, versteckte Seite (kein Sidebar-Eintrag,
    // erreichbar über Kranz' Reiterleiste) — Menschen-/KI-Verwaltung,
    // Ausschluss-Rolle. War bis v0.0.39 ein Tab innerhalb Bismarck.
    /* v1.9.10 (H222-Reihe, 08.09.2026) — DIE SECHS NACKTEN ANMELDUNGEN.
       Gemessen von Post Dolly am 07.09.: menue.php meldete sieben Seiten
       ohne Riegel an, sechs davon wohnen in kranz.php. Sein Lauf mit und
       ohne kranz.php: 6 von 7 Rueckrufen fehlten, der Lader lief durch,
       und der Fehler kam erst beim Klick.

           Sie waren nackt, weil kranz.php immer mitgereist ist. Ein
           Riegel, der nie gebraucht wurde, wurde nie gebaut — und das
           sieht bis zum Tag der Trennung genauso aus wie ein Riegel,
           den es nicht braucht.

       Gefragt wird KASPER und nicht function_exists(): der Aufrufer
       braucht hier eine SEITE, die ein Organ mitbringt, nicht eine
       einzelne Funktion. Der Grund steht ueber cluster_one_menue_wenn().

       DIE CHRONIK (cluster_one_chronik) IST ABSICHTLICH NICHT DABEI.
       Ihre Seite wohnt im Boden und ueberlebt ohne kranz.php; ihr fehlt
       nur die Reiterleiste. Sie bekommt deshalb keinen Riegel um die
       ANMELDUNG, sondern einen um den EINEN Ruf (kern.php). */
    /* v1.9.11 (VERTRAG 25 §4, 08.09.2026) — DER RIEGEL VON HEUTE MORGEN
       KOMMT HIER WIEDER HERAUS, und das war vorhergesagt.
           Post Dolly, 08.09.: "Ein Riegel an der Anmeldung nimmt die
           Seite weg. Fuer die Chronik war das der Grund, keinen zu
           setzen. Fuer die Blaupause ist es ab dem Umzug der Grund,
           ihn wieder zu entfernen."
       Dasselbe hier: der Rueckruf wohnt seit VERTRAG 25 §4 in kern.php
       und ueberlebt ohne kranz.php. Ein Riegel, der Kasper nach 'kranz'
       fragt, versteckte ab jetzt eine Seite, die es gibt.
       Geriegelt ist stattdessen der EINE Ruf auf die Reiterleiste,
       drueben in kern.php — wie bei der Chronik.
       ZWEI der sechs Riegel vom 08.09. fallen so; die anderen VIER
       bleiben, weil ihre Seiten in kranz.php bleiben. */
    add_submenu_page(
        null,
        'Cluster One – Kranz',
        'Bismarck',
        'cluster_one_access', 'cluster_one_bismarck_settings', 'cluster_one_render_bismarck_settings_page'
    );

    // Profil: ebenfalls versteckt, dritter Kranz-Reiter.
    cluster_one_menue_wenn('kranz', function () {
        add_submenu_page(
            null,
            __('Cluster One – Profile', 'cluster-one-crockford'),
            __('Profile', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_profile', 'cluster_one_render_profile_page'
        );
    });

    // PN (v0.0.87): das Briefmodell — persönliche Mitteilungen zwischen
    // Menschen der Weißliste. BEWUSST KEIN CONTAINER: Briefe wohnen im
    // Briefkasten des Empfängers (User-Meta), nicht in einem Raum des
    // Knotens — deshalb keine Zeugen, keine Türereignisse, keine KIs,
    // keine Verknüpfungen, kein SysOp-Durchgriff (Briefgeheimnis als
    // Architektur: das System baut dem Admin keinen bequemen Leseweg;
    // WP-Admin-Allmacht bleibt als außergewöhnlicher Akt mit Hürde).
    // Nebenwirkung mit Absicht: Der Spiegel liest nur Options — Briefe
    // sind auch im Diagnose-Export unsichtbar. PN bleibt BÜRGERRECHT
    // (Weißliste) — "Was in Vegas geschieht, bleibt in Vegas."
    // v0.0.93: PN hat KEINEN Hauptmenüpunkt mehr — Briefe sind das
    // Privateste im System und wohnen im Persönlichen Bereich (Kranz).
    // Ein Hauptmenüpunkt gab ihnen eine Öffentlichkeit, die ihrem Wesen
    // widerspricht.

    // Gregor (Kalender) = eigener Menüpunkt seit v0.0.53, vorher Bismarck-
    // Unterreiter. Benannt nach Papst Gregor XIII. (Gregorianischer
    // Kalender) — Kalender ist die einzige Funktion im Plugin ganz ohne
    // KI-Beteiligung, gehört konzeptionell nicht in Bismarcks KI-
    // augmentierten Werkraum.
    cluster_one_menue_wenn('faust', function () {
        add_submenu_page(
            null,
            'Gregor',
            'Gregor (Kalender)',
            'cluster_one_access', 'cluster_one_gregor', 'cluster_one_render_gregor_page'
        );
    });

    /* KEINE TREFFEN-SEITE MEHR — 18.10.2026 (Neo).
     *
     * Hier stand seit v1.9.13 ein versteckter Eintrag fuer
     * admin.php?page=cluster_one_meetings, geriegelt mit
     * function_exists('cluster_one_render_meetings_page'). Christian hat den
     * Treffen-Reiter samt Seite am 28.09.2026 gestrichen (Kleinrock 1.0.18):
     * was sie zeigte, gehoert dem Kalender (cluster_one_gregor). Die
     * Render-Funktion existiert seither in keiner Datei der Suite, der Riegel
     * war also immer falsch, und der Eintrag konnte nie entstehen.
     *
     *     Ein Eintrag fuer eine Seite, die es nicht gibt, ist kein Weg.
     *     Er ist ein Versprechen mit if davor.
     *
     * Kommt die Seite je zurueck, meldet sie ihr Organ hier wieder an. */

    // Event-Detailseite (v0.0.57): eigene URL pro Event, kein eigener Reiter.
    cluster_one_menue_wenn('faust', function () {
        add_submenu_page(
            null,
            'Gregor',
            __('Event', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_gregor_event', 'cluster_one_render_gregor_event_page'
        );
    });

    /* v0.9.58: KRANZ VOR FEYNMAN — UND DIE KLAMMERN HABEN GETAUSCHT.
       Bis 0.9.57 stand hier "Feynman (Setup)", und das war richtig, solange die
       Blaupause an Feynmans Reiterleiste hing. Seit sie zu Kranz gezogen ist,
       waere derselbe Text eine Luege: Das einzige Setup, das dort war, ist weg.

       Also zieht das Wort mit der Sache um. Kranz traegt jetzt (Setup),
       Feynman traegt (Manual) — NICHT "Tutorial": Ein Tutorial verspricht
       Schritte, und hinter Feynman stehen Derrida, About, Why, DNA und
       Nemesus. Das ist Begruendung, kein Ablauf. Ein Wort, das mehr verspricht
       als die Seite haelt, ist dieselbe Sorte luegender Text, die wir seit
       0.9.43 einsammeln.

       Die Reihenfolge folgt jetzt dem ABLAUF statt der Haeufigkeit (0.3.13):
       erst arbeiten — Kleinrock, Bismarck, Gregor —, dann einrichten (Kranz),
       dann nachlesen (Feynman). Die Einstellungen bleiben damit unten, wie es
       WordPress-Konvention will; sie sind nur nicht mehr das Letzte. */
    /* 19.10.2026 (Neo) — BEI EINEM PRODUKT IST DIESER EINTRAG SICHTBAR.
       Seit die Kranz-Leiste in die Produktleisten aufgeloest ist, fuehrt von
       einer Produktseite kein Reiter mehr zu den Boden-Seiten (Einstellungen,
       Profil, System). Ab zwei Produkten tut das die Dachseite („This node:").
       Bei EINEM Produkt gibt es kein Dach — also haengt der Eintrag dann
       sichtbar unter dem obersten Punkt. WordPress legt bei einem sichtbaren
       Untereintrag selbst einen ersten mit dem Menuetitel an (Maillart,
       11.09.); das sind dann zwei Zeilen, und beide fuehren wohin. */
    /* 27.10.2026 (Neo), Boden 1.17.5 — IMMER SICHTBAR, NICHT NUR BEI EINEM PRODUKT.
       Der Satz darueber („Ab zwei Produkten tut das die Dachseite") war falsch,
       und zwar seit dem Tag, an dem er geschrieben wurde: seit Christians
       Entscheidung vom 16.09. (cluster_one_dach_ziel) leitet der Klick auf
       „Cluster One" DIREKT AUFS ERSTE PRODUKT um. Die Dachseite mit ihrem
       „This node: …"-Link wird auf einem Knoten mit zwei oder mehr Produkten
       nie gezeigt. Nemesus, 27.10., auf der DEV mit fuenf Produkten: „Wo finde
       ich den Eintrag?" — Knoten-Einstellungen, Profil und System hatten dort
       keinen Weg ausser der getippten Adresse. Bis 1.17.4:
           $c1_eltern_einst = ($c1_zahl === 1) ? cluster_one_menue_dach_slug() : null;
       1.17.5 hing der Eintrag deshalb immer sichtbar unter dem obersten Punkt.
       28.10.2026, Boden 1.17.7 — ZURUECK. Nemesus: „Knoten-Einstellungen wollte
       ich definitiv nicht im WP-Menu drinnen haben." Die Tuer ist jetzt der
       Reiter „Knoten" am Ende jeder Produktleiste (kern.php,
       cluster_one_render_tabs). Der Eintrag ist wieder nur bei EINEM Produkt
       sichtbar, wie vor 1.17.5 – dort gibt es keine Leiste, die ihn traegt.
       Die Umleitung des Dachs bleibt, wie entschieden. */
    $c1_eltern_einst = ($c1_zahl === 1) ? cluster_one_menue_dach_slug() : null;
    /* 27.10.2026 (Neo), Boden 1.17.2 — „Node settings" STATT „Settings". Test 5
       (Einzelprodukt, DEV): Nemesus hielt die Seite fuer falsch, weil sie denselben
       Namen trug wie die Einstellungen des Produkts darueber (Post Dolly nennt
       seine eigene Seite „Settings"). Die Seite beginnt mit „This node" und
       regelt nur Knotendinge — Name, Weissliste, Connectoren, Reset —, also
       sagt der Eintrag das. Bis 1.17.1: __('Settings', …). Gleicher Wechsel im
       Kranz-Reiter (kern.php); der Dach-Link liest das Etikett von dort. */
    cluster_one_menue_wenn('kranz', function () use ($c1_eltern_einst) {
        add_submenu_page(
            $c1_eltern_einst,
            'Cluster One – Kranz',
            __('Node settings', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_settings', 'cluster_one_render_settings_page'
        );
    });

    // Feynman (Manual) = der dritte Münzrand (Lernpfad). War „Über Cluster One";
    // der alte Über-Text liegt seit 0.1.2 als eigener Reiter dahinter.
    /* v1.0.1 — DIE SECHZEHNTE ZEILE, gefunden von der Bismarck-Instanz.
       In 0.9.79 haben wir die fuenfzehnte gefunden und sind an dieser
       vorbeigelaufen, weil hier 'about' steht und nicht 'feynman':
       cluster_one_render_about_page() wohnt in kranz.php:1129 und ruft dort
       cluster_one_render_feynman_tabs() NACKT. Ein Bismarck-Light ohne Feynman
       zeigte den Menuepunkt, und der Klick war kein gebrochenes Versprechen
       mehr, sondern ein Fatal.
         Ein Organ, dessen Seite in einem anderen Organ wohnt,
         wird beim Riegeln uebersehen. */
    cluster_one_menue_wenn('feynman', function () {
        add_submenu_page(
            null,
            'Feynman',
            'Feynman (Manual)',
            'cluster_one_access', 'cluster_one_about', 'cluster_one_render_about_page'
        );
    });

    /* v0.4.4: Die Blaupause — URL-Seite ohne Sidebar-Eintrag.
       v0.9.58: erreichbar ueber die KRANZ-Reiterleiste, nicht mehr ueber
       Feynmans. */
    cluster_one_menue_wenn('kranz', function () {
        add_submenu_page(
            null,
            'Cluster One – Setup',
            'Setup',
            'cluster_one_access', 'cluster_one_blueprint', 'cluster_one_render_blueprint_page'
        );
    });

    // Ban-Liste als URL-Seite ohne Sidebar-Eintrag (parent=null),
    // oben über Meshs Reiterleiste verlinkt – genau wie die Hello-Seite.
    cluster_one_menue_wenn('kleinrock', function () {
        add_submenu_page(
            null,
            __('Cluster One – Banned', 'cluster-one-crockford'),
            __('Ban list', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_banned', 'cluster_one_render_banned_page'
        );
    });

    /* v0.9.60: KLEINROCKS EIGENE EINSTELLUNGEN — Reiterseite ohne
       Sidebar-Eintrag, wie die anderen Mesh-Reiter. Bis hierher führte der
       einzige Weg zum Paaren über eine Kranz-Seite; ein Modul, das ohne Kranz
       laufen soll, konnte sich also nicht paaren. Zugleich der fünfte Reiter,
       den Cluster One Light braucht. */
    cluster_one_menue_wenn('kleinrock', function () {
        add_submenu_page(
            null,
            __('Cluster One – Mesh settings', 'cluster-one-crockford'),
            __('Settings', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_mesh_settings', 'cluster_one_render_mesh_settings_page'
        );
    });

    // Freundesliste (v0.0.54): dasselbe Muster wie die Bannliste.
    cluster_one_menue_wenn('kleinrock', function () {
        add_submenu_page(
            null,
            __('Cluster One – Friends', 'cluster-one-crockford'),
            __('Friends', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_friends', 'cluster_one_render_friends_page'
        );
    });

    // Modi-Vokabular (v0.0.75): reine Beschreibungen, Kranz-Tab.
    cluster_one_menue_wenn('modi', function () {
        add_submenu_page(
            null,
            __('Cluster One – Modes', 'cluster-one-crockford'),
            __('Modes', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_modes', 'cluster_one_render_modes_page'
        );
    });

    // KI-Vorschläge (v0.0.54, Reißbrett): wartet auf Label.
    cluster_one_menue_wenn('kleinrock', function () {
        add_submenu_page(
            null,
            __('Cluster One – Suggestions', 'cluster-one-crockford'),
            __('Suggestions', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_ai_suggestions', 'cluster_one_render_ai_suggestions_page'
        );
    });

    /* v0.9.36: Die Chronik. Kein eigener Sidebar-Punkt — erreichbar ueber
       den Verweis auf der Hauptseite, dasselbe Muster wie Hallo und Raum.
       Ob sie in die Sidebar gehoert, ist eine Anzeige-Entscheidung und
       gehoert Christian, nicht mir. */
    add_submenu_page(
        null,
        __('Cluster One – Chronicle', 'cluster-one-crockford'),
        __('Chronicle', 'cluster-one-crockford'),
        'cluster_one_access', 'cluster_one_chronik', 'cluster_one_render_chronik_page'
    );

    // Hello/chat page: reachable per row (PN link), not shown as its own tab.
    cluster_one_menue_wenn('kleinrock', function () {
        add_submenu_page(
            null,
            __('Cluster One – Hello', 'cluster-one-crockford'),
            __('Hello', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_hello', 'cluster_one_render_hello_page'
        );
    });

    // Bismarck room page: own URL per room (Hallo pattern), reached via the
    // Rooms list, not shown as its own tab.
    cluster_one_menue_wenn('bismarck-seiten', function () {
        add_submenu_page(
            null,
            __('Cluster One – Room', 'cluster-one-crockford'),
            __('Room', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_room', 'cluster_one_render_room_page'
        );
    });

    // Platon thread page: own URL per thread (Room pattern), reached via
    // the Forum tab, not shown as its own tab.
    cluster_one_menue_wenn('platon', function () {
        add_submenu_page(
            null,
            __('Cluster One – Thread', 'cluster-one-crockford'),
            __('Thread', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_forum_thread', 'cluster_one_render_forum_thread_page'
        );
    });

    // Persönlicher Bereich (v0.0.50): vierter Kranz-Reiter.
    cluster_one_menue_wenn('kranz', function () {
        add_submenu_page(
            null,
            'Cluster One – Kranz',
            __('Personal area', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_personal_area', 'cluster_one_render_personal_area_page'
        );
    });

    // System-Übersicht (v0.0.58): fünfter Kranz-Reiter, rein lesend.
    cluster_one_menue_wenn('kranz', function () {
        add_submenu_page(
            null,
            'Cluster One – Kranz',
            'System',
            'cluster_one_access', 'cluster_one_system', 'cluster_one_render_system_page'
        );
    });

    // v0.1.8: Warum-Seite — die Begründung des Entwurfs, Reiter der Feynman-Seite.
    cluster_one_menue_wenn('feynman', function () {
        add_submenu_page(
            null,
            'Feynman',
            __('Why', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_why', 'cluster_one_render_why_page'
        );
    });

    // v0.1.9: DNA — die Doku des Frameworks, live aus dem Child-Theme gelesen.
    // Immer registriert; ob sie erreichbar ist, entscheidet der Reiter.
    cluster_one_menue_wenn('feynman', function () {
        add_submenu_page(
            null,
            'Feynman',
            'DNA',
            'cluster_one_access', 'cluster_one_dna', 'cluster_one_render_dna_page'
        );
    });

    // v0.1.2: 'cluster_one_feynman' entfallen — es war eine zweite, leere
    // Seite mit demselben Namen wie die Feynman-Seite (cluster_one_about).

    // Über mich (v0.1.0): LETZTER Reiter — der Mensch hinter dem Knoten.
    // v0.1.2: liegt jetzt auf der Feynman-Seite, nicht mehr unter Kranz.
    cluster_one_menue_wenn('feynman', function () {
        add_submenu_page(
            null,
            'Feynman',
            __('About me', 'cluster-one-crockford'),
            'cluster_one_access', 'cluster_one_ueber_mich', 'cluster_one_render_ueber_mich_page'
        );
    });
});

}

/* ===========================================================================
   v1.9.14 — DIE ZWEITE HAELFTE DES DACHS: EIN EINTRAG JE PRODUKT.

   VERTRAG 26 · NACHTRAG 26a: ab ZWEI Produkten heisst der oberste Punkt
   „Cluster One", und darunter steht ein Eintrag je Produkt.

   DIE BESCHRIFTUNG GEHOERT DEM HAUS (Christian, 11.09., Weg B).
   Kleinrocks Eintrag heisst „Kleinrock (Mesh)" — Ehrenname UND Sache. Ein
   Dach, das daraus „Kleinrock" macht, sagt weniger als das Haus selbst.

       Wo das Haus keine Beschriftung gesetzt hat, nimmt das Dach den
       angemeldeten Namen — und das ist kein Vorzug, sondern das Einzige,
       was es hat.

   UND DESHALB STAND HIER FAST EINE FORDERUNG, DIE NIEMAND BESCHLOSSEN HAT:
   W60 verlangte, die Beschriftung sei GENAU der angemeldete Name. Das stand
   in keinem Nachtrag — ich hatte „Kleinrock" am 10.09. in einem Papier als
   BEISPIEL geschrieben, und im Zeugen wurde eine Forderung daraus.

       Ein Beispiel, das in einen Zeugen wandert, wird dort zur Forderung —
       und niemand hat sie beschlossen.

   VIER FAELLE, VIER FOLGEN — nach Maillarts Bauart vom 11.09.:

       Slug angemeldet, Eintrag da     nichts. Das Haus hat ihn.
       Slug angemeldet, Seite da,
         aber kein sichtbarer Eintrag  das Dach ergaenzt einen
       Slug angemeldet, KEINE Seite    KEIN Eintrag, GEMELDET
       kein Slug angemeldet            KEIN Eintrag, oben schon gemeldet

   Der dritte Fall ist der wichtige: ein Eintrag auf eine Seite, die es nicht
   gibt, ist eine graue Seite — und dagegen ist v0.9.74 geschrieben.
       Ein Menuepunkt, der auf eine graue Seite fuehrt, ist ein gebrochenes
       Versprechen.
   Ein FEHLENDER Eintrag ist ehrlich. Ein gebrochenes Versprechen nicht.

   SPAETE PRIORITAET, UND SIE IST DER GANZE TRICK: dieser Lauf muss NACH
   allen Organen kommen, sonst sieht er ihre Eintraege nicht und ergaenzt,
   was schon da ist.

   v1.12.4 (16.09.2026) — 99 WAR NICHT SPAET GENUG, UND DER KNOTEN HAT ES
   GESAGT. Christian sah VIER Eintraege statt fuenf. Paciolis Seite WAR
   angemeldet — sie war nur noch nicht da, als hier gezaehlt wurde:

       10  menue.php                die vier eigenen Slugs
       10  class-cv-selftest.php
       99  HIER                     das Dach zaehlt
      100  class-cv-admin.php       Paciolis Seite meldet sich an

       Eine Pruefung, die vor dem Gemessenen laeuft, misst seine
       Abwesenheit und nennt sie sein Fehlen.              (Mammut)

   VIER DER FUENF SLUGS KOMMEN AUS DIESER DATEI und sind bei 10 laengst da.
   Der fuenfte kommt von aussen. Eine Prioritaet, die nur die eigenen
   Anmeldungen ueberholt, ueberholt gar nichts.

   WARUM PHP_INT_MAX UND NICHT 200 — Mammuts Frage war: „gibt es eine Form,
   die ohne eine geratene Zahl auskommt?" Ja, und das ist sie:

       Eine geratene Zahl ist eine, die groesser sein soll als eine, die
       man nicht kennt. PHP_INT_MAX ist keine Schaetzung — es ist die
       Aussage „zuletzt".

   200 waere dieselbe Wette wie 99, nur mit besseren Chancen. Wetten auf
   fremde Zahlen haben in diesem Haus diese Woche dreimal verloren.

   DIE BENANNTE GRENZE, und sie bleibt bestehen: wer sich EBENFALLS bei
   PHP_INT_MAX anmeldet, wird nach der Reihenfolge der ANMELDUNG gefahren.
   Meldet er sich danach an, kommt er danach — und bekommt keinen Eintrag.
   Das ist keine Eigenschaft dieser Zahl, sondern der Reihenfolge; keine
   Prioritaet der Welt aendert es. W79 WELT 3 faehrt genau diesen Fall und
   DRUCKT ihn, ohne ein Urteil zu faellen.

       Eine Grenze, die man nicht verschieben kann, gehoert gemeldet und
       nicht ueberboten.

   UND ZWEI DINGE ZIEHEN MIT, die Mammut in seinem §3 benannt hat:

   1  DAS remove_submenu_page() STEHT INNERHALB dieses Blocks und nicht
      daneben. Der doppelte Eintrag entsteht beim ERSTEN
      add_submenu_page('cluster_one', …) — und das sind genau die
      Produkteintraege hier. Sie entstehen jetzt spaeter, also entsteht
      er spaeter, also raeumt der Riegel spaeter. Er kann nicht zu frueh
      kommen, weil er im selben Atemzug steht.
          Ein Riegel, der vor seinem Gegenstand laeuft, ist kein Riegel.
          Er ist eine leere Schleife mit gutem Gewissen.    (Mammut)
   2  cluster_one_menue_seite_verboten() liest '_wp_submenu_nopriv' und
      '_wp_menu_nopriv'. Auch die fuellen sich waehrend 'admin_menu'.
      Spaeter heisst hier VOLLSTAENDIGER: c) wird schaerfer, nicht
      stumpfer. Bei 99 konnte ein Produkt, das bei 100 abgewiesen wird,
      noch als „nicht angemeldet" gemeldet werden — genau die falsche
      Auskunft, gegen die c) gebaut ist.
   =========================================================================== */
add_action('admin_menu', 'cluster_one_menue_produkteintraege', PHP_INT_MAX);

/** Ist diese Seite ueberhaupt angemeldet — sichtbar oder versteckt? */
/* v1.12.0 — DIESER RIEGEL HAT GEFEHLT, und W64s neue Zeile hat es beim
   ERSTEN Lauf gefunden: eine zweite Kopie dieser Datei starb an
   „Cannot redeclare cluster_one_menue_seite_da()".

       Wer in zwei Tagen zehn Funktionen schreibt und keine einzige
       riegelt, hat die Regel des Hauses gekannt und nicht angewandt.

   Paciolis Zeile misst das ANKOMMEN, indem sie eine ZWEITE Kopie laedt —
   und genau deshalb sieht sie, was kein Zeuge sah, der nur eine laedt. */
/* 27.10.2026 (Neo) — DER HAKEN EINER VERSTECKTEN SEITE WIRD GEFUNDEN, NICHT
   NACHGERECHNET. Nemesus auf der Main: „Fahre ich ueber Cluster One, sehe ich
   alle Module. Gehe ich vorher auf Bricks und fahre dann ueber Cluster One,
   wird nur Pacioli angezeigt."

   WARUM, und es ist WordPress' eigene Rechnung: get_plugin_page_hookname()
   baut den Haken einer versteckten Seite aus get_admin_page_parent(''), und
   die liest auf admin.php?page=<x> das LEBENDE $menu: steht <x> dort als
   oberster Punkt, heisst der Haken '<x>_page_<slug>', sonst 'admin_page_<slug>'.
   Auf der Bricks-Seite melden unsere Seiten sich bei Prioritaet 10 an, BEVOR
   das Theme seinen Punkt setzt: Haken 'admin_page_<slug>'. Pacioli meldet
   bei 100, DANACH: 'bricks_page_cv-my-ais'. Das Dach rechnet bei PHP_INT_MAX
   nach, findet jetzt 'bricks_page_<slug>' — und an dem haengt nichts, ausser
   bei Pacioli. Vier Produkte „ohne Rueckruf", und das nur auf Seiten eines
   obersten Punkts, der nach uns kam. Dieselbe Formel, zweimal gerechnet, zwei
   Antworten.

       Ein Name, der von der Uhrzeit abhaengt, ist kein Name. Der Haken, an
       dem die Seite wirklich haengt, steht in $_registered_pages — dort wird
       er gelesen, nicht noch einmal gebaut.

   Vorzug fuer 'admin_page_<slug>' (die gewoehnliche Form), sonst der erste
   angemeldete Haken, der auf '_page_<slug>' endet. Fehlt beides, ist die
   Seite nicht angemeldet — derselbe Befund wie bisher. */
if (!function_exists('cluster_one_menue_seite_haken')) {
    function cluster_one_menue_seite_haken(string $c1_slug): string {
        if ($c1_slug === '') { return ''; }
        $c1_seiten = (array) ($GLOBALS['_registered_pages'] ?? array());
        if (isset($c1_seiten['admin_page_' . $c1_slug])) { return 'admin_page_' . $c1_slug; }
        $c1_ende = '_page_' . preg_replace('!\.php!', '', $c1_slug);
        $c1_n    = strlen($c1_ende);
        foreach (array_keys($c1_seiten) as $c1_h) {
            $c1_h = (string) $c1_h;
            if (strlen($c1_h) > $c1_n && substr($c1_h, -$c1_n) === $c1_ende) { return $c1_h; }
        }
        return '';
    }
}

if (!function_exists('cluster_one_menue_seite_da')) {
    function cluster_one_menue_seite_da(string $c1_slug): bool {
        /* 27.10.2026 (Neo): hier stand isset($_registered_pages['admin_page_' . $slug])
           — die gewoehnliche Form, und auf der Bricks-Seite die falsche. */
        if (cluster_one_menue_seite_haken($c1_slug) !== '') { return true; }
        foreach ((array) ($GLOBALS['submenu'] ?? array()) as $c1_eltern) {
            foreach ((array) $c1_eltern as $c1_e) {
                if ((string) ($c1_e[2] ?? '') === $c1_slug) { return true; }
            }
        }
        return false;
    }
}

/* v1.12.0 — DIESER RIEGEL HAT GEFEHLT, und W64s neue Zeile hat es beim
   ERSTEN Lauf gefunden: eine zweite Kopie dieser Datei starb an
   „Cannot redeclare cluster_one_menue_produkteintraege()".

       Wer in zwei Tagen zehn Funktionen schreibt und keine einzige
       riegelt, hat die Regel des Hauses gekannt und nicht angewandt.

   Paciolis Zeile misst das ANKOMMEN, indem sie eine ZWEITE Kopie laedt —
   und genau deshalb sieht sie, was kein Zeuge sah, der nur eine laedt. */
/* v1.12.2 (16.09.2026) — „NICHT DA" ODER „NICHT FUER DICH".
   PROMPT-HOLLERITH-12 §1, Weg c), auf einen Befund aus Bericht 72 §4.

   DER BEFUND: cluster_one_menue_seite_da() gibt FALSE in zwei sehr
   verschiedenen Faellen zurueck —

       die Seite ist nirgends angemeldet                (ein Befund)
       die Seite IST angemeldet, aber nicht fuer DIESEN
       Betrachter: add_submenu_page() steigt bei fehlender
       Capability aus, bevor $_registered_pages gesetzt wird
                                                        (kein Befund)

   Das Dach hat beide gleich behandelt und im zweiten Fall gemeldet:
   „auf diesem KNOTEN ist keine solche Seite angemeldet." Das ist ein Satz
   ueber den Knoten, gewonnen aus der Sicht EINES Benutzers.

       Eine Auskunft ueber den Knoten, die aus der Sicht eines Benutzers
       gewonnen ist, verwechselt „nicht da" mit „nicht fuer dich".

   UND SIE SCHRIEB DABEI. cluster_one_produkte_stumm() loest in kern.php
   ein update_option je Anfrage aus — auf einem reinen Lesepfad, fuer jedes
   Mitglied, das die Capability nicht hat. Kaspers eigener Kopf hat den Fall
   benannt, bevor er eintrat: „eine Zahl, die auf einem Lesepfad bei jedem
   Aufruf schreibt, waere ihr eigener Schaden." Der Boden hat es fuer sich
   vermieden, die Leserseite tat es trotzdem.

   DIE UNTERSCHEIDUNG MUSSTE NICHT ERFUNDEN WERDEN. WordPress fuehrt sie
   bereits: ein Punkt, den es wegen fehlender Capability abgelehnt hat,
   landet in $_wp_submenu_nopriv bzw. $_wp_menu_nopriv. Das stand die ganze
   Zeit da und wurde nicht gelesen.

       Die Unterscheidung musste nicht erfunden werden. Sie stand schon da.

   BENANNTE GRENZE: diese Funktion liest WordPress-Interna. Sie sind seit
   2.0 stabil und werden von WordPress selbst zum Zeichnen des Menues
   benutzt; verschwinden sie, faellt diese Funktion auf false zurueck und
   das Dach meldet wieder wie vorher — also zu viel und nicht zu wenig.
   Das ist die richtige Richtung fuer einen Rueckfall.

   WARUM NICHT current_user_can() DIREKT: das Dach weiss nicht, mit WELCHER
   Capability die fremde Seite angemeldet wurde. Post Dolly nimmt
   'cluster_one_kranz_save', die anderen 'cluster_one_access', und ein
   sechstes Produkt nimmt, was es will.
   (31.10.2026, Boden 1.19.5: seit Post Dolly 0.3.59 nehmen alle fuenf
   'cluster_one_access' – „Sehen duerfen alle, Tun nur mit Recht". Der Satz
   darueber beschreibt den Stand, fuer den diese Funktion gebaut wurde, und
   der Grund gilt weiter: das Dach raet nicht.)
       Wer die Antwort raten muss, weil er die Frage nicht kennt,
       soll den fragen, der beides hat. */
if (!function_exists('cluster_one_menue_seite_verboten')) {
    function cluster_one_menue_seite_verboten(string $c1_slug): bool {
        foreach ((array) ($GLOBALS['_wp_submenu_nopriv'] ?? array()) as $c1_eltern) {
            if (isset(((array) $c1_eltern)[$c1_slug])) { return true; }
        }
        return isset($GLOBALS['_wp_menu_nopriv'][$c1_slug]);
    }
}

if (!function_exists('cluster_one_menue_produkteintraege')) {
    function cluster_one_menue_produkteintraege(): void {
        if (!function_exists('cluster_one_produkte')) { return; }
        $c1_p = cluster_one_produkte();
        /* BEI EINEM PRODUKT GIBT ES NICHTS ZU UEBERDACHEN. Der oberste Punkt
           traegt dann den Namen und fuehrt auf das Produkt — ein Eintrag darunter
           waere derselbe Weg ein zweites Mal. */
        if (count($c1_p) < 2) { return; }

        $c1_dachslug = cluster_one_menue_dach_slug();
        $c1_da = array();
        foreach ((array) ($GLOBALS['submenu'][$c1_dachslug] ?? array()) as $c1_e) {
            $c1_da[(string) ($c1_e[2] ?? '')] = true;
        }
        foreach ($c1_p as $c1_wirt => $c1_v) {
            $c1_slug = is_array($c1_v) ? (string) ($c1_v['slug'] ?? '') : '';
            $c1_name = is_array($c1_v) ? (string) ($c1_v['name'] ?? '') : '';
            if ($c1_slug === '') { continue; }
            if (isset($c1_da[$c1_slug])) { continue; }
            if (!cluster_one_menue_seite_da($c1_slug)) {
                /* NICHT FUER DICH ist kein Befund. Kein Eintrag — aber auch
                   keine Meldung und damit kein Schreibvorgang. Der Punkt
                   erscheint fuer den, der ihn oeffnen darf, und fuer sonst
                   niemanden; das ist dieselbe Regel, nach der WordPress sein
                   ganzes Menue zeichnet. */
                if (cluster_one_menue_seite_verboten($c1_slug)) {
                    continue;
                }
                cluster_one_produkte_stumm('slug-ohne-seite',
                    'Produkt ' . $c1_wirt . ' hat den Slug „' . $c1_slug . '" angemeldet, '
                  . 'aber auf diesem Knoten ist keine solche Seite angemeldet. Es '
                  . 'bekommt KEINEN Eintrag unter dem Dach — ein Eintrag auf eine '
                  . 'Seite, die es nicht gibt, waere ein gebrochenes Versprechen.');
                continue;
            }
            /* v1.12.5 (16.09.2026) — DER EINTRAG UND SEINE SEITE MUESSEN AUS
               EINER ZEILE KOMMEN. Christian sah fuenf Eintraege, und jeder
               Klick ging auf wp-admin/<slug> statt wp-admin/admin.php?page=<slug>.

               WARUM, und es ist WordPress' eigene Rechnung:

                   _wp_menu_output():
                       $menu_hook = get_plugin_page_hook($sub_item[2], $item[2]);
                       if (!empty($menu_hook) || …) -> admin.php?page=<slug>
                       else                         -> DER NACKTE SLUG

                   get_plugin_page_hookname() baut den Namen aus dem
                   ELTERNTEIL. Gemessen, alle fuenf:
                       hook(<slug>, 'cluster_one')  ->  NULL
                       hook(<slug>, null)           ->  admin_page_<slug>

               Unsere Seiten melden sich VERSTECKT an, ihr Haken heisst
               'admin_page_<slug>'. Das Dach trug sie danach unter
               'cluster_one' ein — WordPress suchte 'cluster-one_page_<slug>'
               und fand nichts.

                   Ein Eintrag und seine Seite, die von zwei verschiedenen
                   Zeilen kommen, sind fuer WordPress zwei Sachen, die
                   zufaellig denselben Namen tragen.          (Mammut)

               ZWEI FORMEN WURDEN GEFAHREN, EINE GEBAUT:

                 A  die URL als $menu_slug eintragen
                    ('admin.php?page=<slug>'). Fuehrt richtig hin — und
                    kostet zwei Dinge, GEMESSEN und nicht befuerchtet:
                        cluster_one_menue_produkte_ordnen() sortiert NICHT
                        mehr (sein Slug->nr-Register findet die URL nicht),
                        und der Doppelten-Riegel unten greift nicht mehr:
                        zweiter Lauf -> ZEHN Eintraege statt fuenf.
                        Und das Dach laeuft je Anfrage zweimal.
                 B  das Dach meldet die Seite selbst an, mit dem Rueckruf
                    aus dem versteckten Haken. GEBAUT — aber nicht mit der
                    Capability des Dachs, siehe unten.

               DIE CAPABILITY IST DER TEIL, DER FAST EIN LOCH GEWORDEN WAERE.
               Gemessen, wie die fuenf Seiten sich SELBST anmelden:
                   cluster_one_mesh        cluster_one_access
                   cluster_one_bismarck    cluster_one_access
                   cluster_one_landkarte   manage_options
                   cluster_one_post_dolly  cluster_one_kranz_save   (bis 0.3.58; seit 0.3.59 cluster_one_access)
                   cv-my-ais               use_own_ai_connector
               Drei verlangen etwas ANDERES als das Dach. Traegt das Dach
               seine eigene 'cluster_one_access' ein und meldet die Seite
               damit ein zweites Mal an, ist sie ab dann unter der weiteren
               erreichbar.

               HIER STAND EINE ZAHL VON MIR UND SIE WAR FALSCH. Ich hatte
               geschrieben: vier der fuenf Renderfunktionen pruefen selbst
               nach, Paciolis nicht. Nachgemessen (w82, token_get_all,
               gegen die EIGENE Capability und vor der ersten Ausgabe):
                   cv-my-ais               KEIN eigener Riegel
                   cluster_one_mesh        KEIN eigener Riegel
                   cluster_one_bismarck    KEIN eigener Riegel
                   cluster_one_landkarte   Riegel  landkarte.php :651
                   cluster_one_post_dolly  Riegel  post-dolly.php :1005
               DREI von fuenf ohne Riegel, nicht eine. Mein erstes Werkzeug
               hatte gefragt, ob das WORT 'current_user_can' im Koerper
               vorkommt; bei Pacioli kommt es zweimal vor, fuer 'list_users'
               und 'promote_users', und beide riegeln Abschnitte weiter
               unten.
                   Ein Werkzeug, das ein Wort sucht, misst das Wort und
                   nicht die Sache.

               Bei kleinrock und bismarck ist die Capability der Seite
               DIESELBE wie die des Dachs — dort richtet ein eingetragenes
               'cluster_one_access' nichts an. Es bleibt GENAU EINE Seite,
               bei der beides zusammenkommt: Paciolis. Sie verlangt
               'use_own_ai_connector', sie riegelt nicht nach, und sie ist
               die Seite der verwahrten Schluessel.

               UND AUCH DAS GENAU, weil es kleiner ist als mein erster
               Satz: was sie einem Fremden zeigte, waere dessen EIGENE
               (leere) Liste — CV_Identities::owned_by(get_current_user_id())
               — und jeder Schreibweg (handle_save, handle_delete,
               handle_quarantine, handle_check_key) prueft CV_CAP noch
               einmal. Der Schaden ist eine Seite, die sich zeigt, wo sie
               sich nicht zeigen soll, und kein offener Tresor. Es bleibt
               ein Grund, die Capability nicht zu verbreitern.

                   Ein Eintrag darf sagen, wo etwas liegt. Er darf nicht
                   entscheiden, wer hindarf.

               DARUM KOMMT SIE AUS DER ANMELDUNG, und ohne sie gibt es
               KEINEN Eintrag — fail closed, und gemeldet, damit das Produkt
               nicht still verschwindet.
                   Ein Produkt, das still verschwindet, sieht aus wie eines,
                   das es nicht gibt. */
            $c1_cap = is_array($c1_v) ? (string) ($c1_v['cap'] ?? '') : '';
            if ($c1_cap === '') {
                cluster_one_produkte_stumm('ohne-cap',
                    'Produkt ' . $c1_wirt . ' hat den Slug „' . $c1_slug . '" angemeldet, '
                  . 'aber KEINE Capability. Das Dach traegt ihn deshalb NICHT ein: es '
                  . 'kann nicht entscheiden, wer auf eine fremde Seite darf. Die Seite '
                  . 'bleibt unter ihrer eigenen Adresse erreichbar.');
                continue;
            }

            /* DER RUECKRUF, aus dem versteckten Haken geholt.

               27.10.2026 (Neo): Bis heute stand hier
                   $c1_versteckt = get_plugin_page_hookname($c1_slug, null);
               mit dem Satz „Sein Name kommt aus WordPress' eigener Formel und
               nicht aus einer getippten Zeichenkette — wer den Namen nachbaut,
               misst ab dann seinen Nachbau." Der Satz war richtig und die
               Formel trotzdem die falsche Stelle: sie rechnet den Namen aus
               dem Zustand des Menues, und der ist bei PHP_INT_MAX ein anderer
               als bei 10 (siehe cluster_one_menue_seite_haken). Jetzt wird
               der Haken gelesen, an dem die Seite angemeldet IST. Der Griff
               'ohne-hakenformel' (get_plugin_page_hookname fehlt) ist damit
               fort; der Fall ist in 'ohne-rueckruf' aufgegangen. */
            $c1_cb = '';
            $c1_versteckt = cluster_one_menue_seite_haken($c1_slug);
            $c1_f = ($c1_versteckt !== '') ? ($GLOBALS['wp_filter'][$c1_versteckt] ?? null) : null;
            if (is_object($c1_f) && !empty($c1_f->callbacks)) {
                foreach ($c1_f->callbacks as $c1_liste) {
                    foreach ((array) $c1_liste as $c1_eintrag) {
                        if (!empty($c1_eintrag['function'])) { $c1_cb = $c1_eintrag['function']; break 2; }
                    }
                }
            }
            if ($c1_cb === '') {
                cluster_one_produkte_stumm('ohne-rueckruf',
                    'Die Seite „' . $c1_slug . '" ist angemeldet, aber an ihrem Haken '
                  . '„' . $c1_versteckt . '" haengt kein Rueckruf. KEIN Eintrag.');
                continue;
            }
            add_submenu_page($c1_dachslug, $c1_name, $c1_name,
                             $c1_cap, $c1_slug, $c1_cb);
        }

        /* v1.12.3 (16.09.2026) — DER SECHSTE EINTRAG, UND ER WAR UNSER.
           PROMPT-HOLLERITH-13 §4. Christian sah unter „Cluster One" einen
           Untereintrag, der wieder „Cluster One" hiess.

           ER STAND IN KEINER ZEILE DIESER DATEI. WordPress legt ihn selbst an:

               // If the parent doesn't already have a submenu, add a link to
               // the parent as the first item.
               if ( ! isset( $submenu[ $parent_slug ] ) && $menu_slug !== $parent_slug )

           Solange Kleinrock den Slug der Hauptseite TEILTE ('cluster_one'),
           war die zweite Haelfte der Bedingung falsch und der Eintrag entstand
           nie. Seit Kleinrock auf 'cluster_one_mesh' steht — richtig, sonst
           zeigte das Dach auf ein Produkt — ist der Platz frei.

               Ein Eintrag, den niemand geschrieben hat, wurde trotzdem von
               jemandem verursacht.

           ZWEI FORMEN GEMESSEN, EINE GEBAUT (W79):

             remove_submenu_page        5 Eintraege . 5 verschiedene Ziele .
                                        jedes Produkt mit EIGENEM Slug   GRUEN
             das erste Produkt nimmt    5 Eintraege . 5 verschiedene Ziele .
             den Dach-Slug              ABER: Pacioli hat keinen Eintrag auf
                                        cv-my-ais — sein Eintrag zeigt auf
                                        'cluster_one' und kommt nur ueber die
                                        Umleitung an. Und der Sortierer unten
                                        kennt 'cluster_one' nicht, also faellt
                                        dieser Eintrag aus der Ordnung.  ROT

               Ein Eintrag, der auf dieselbe Seite zeigt wie ein anderer, ist
               dieselbe Doppelung in neuer Gestalt.              (Mammut)

           HIER UND NICHT FRUEHER: der Eintrag entsteht beim ERSTEN
           add_submenu_page auf 'cluster_one' — das ist die Schleife darueber.
           Vorher gaebe es nichts zu entfernen.

           DER RIEGEL IST KEINE HOEFLICHKEIT: remove_submenu_page() gibt es
           seit WP 3.1. Fehlt sie, bleibt der sechste Eintrag stehen — sichtbar
           und haesslich, aber nichts geht kaputt. Das ist die richtige
           Richtung fuer einen Rueckfall. */
        /* BEIDE ARGUMENTE AUS DER QUELLE, und das ist GEMESSEN und nicht
           angenommen. Mammut, PROMPT-21 §3.2: „das erste ist das Dach, das
           zweite der Eintrag, der zufaellig so heisst. Pruefe das nach."

           Nachgemessen an WordPress' eigener Regel (add_submenu_page):

               if ( ! isset( $submenu[ $parent_slug ] ) && $menu_slug !== $parent_slug ) {
                   foreach ( (array) $menu as $parent_menu ) {
                       if ( $parent_menu[2] === $parent_slug && … ) {
                           $submenu[ $parent_slug ][] = array_slice( $parent_menu, 0, 4 );

           array_slice($parent_menu, 0, 4) — und $parent_menu[2] IST der
           Elternslug. Der Eintrag heisst nicht zufaellig wie das Dach; er
           TRAEGT den Dach-Slug, weil WordPress ihn dorthin kopiert. Mit zwei
           verschiedenen Dach-Slugs gefahren, beide Male gleich.

               Zwei Werte, die zwingend zusammen wandern, duerfen nicht aus
               zwei Zeichenketten kommen. Dann wandert irgendwann nur einer. */
        if (function_exists('remove_submenu_page')) {
            remove_submenu_page($c1_dachslug, $c1_dachslug);
        }

        cluster_one_menue_produkte_ordnen($c1_p);

        /* v1.12.5 (17.09.2026) — UND ZULETZT DIE REIHENFOLGE DER SCHLUESSEL.
           Das ist die eigentliche Reparatur; siehe den Block weiter unten,
           warum sie hier steht und nicht auf der Leseseite.

           SIE STEHT AM ENDE, weil sie alles braucht, was oben entsteht: die
           Eintraege, den geraeumten sechsten, die Sortierung. Eine
           Umsortierung vor den Eintraegen waere dieselbe leere Schleife mit
           gutem Gewissen, die dieser Datei schon einmal angeboten wurde.

           DIE EINTRAGSSCHLEIFE BLEIBT BEI PHP_INT_MAX. Wir ueberbieten die
           Grenze nicht — wir sortieren NACH ihr. */
        cluster_one_menue_schluessel_ordnen($c1_p);
    }
}

/* ===========================================================================
   v1.12.5 (17.09.2026) — DER DACH-SCHLUESSEL MUSS VORN STEHEN.

   WordPress, wp-admin/includes/plugin.php, get_admin_page_parent(), WOERTLICH:

       foreach ( array_keys( (array) $submenu ) as $parent_page ) {
           foreach ( $submenu[ $parent_page ] as $submenu_array ) {
               …
               } elseif ( isset( $plugin_page ) && $plugin_page === $submenu_array[2] ) {
                   $parent_file = $parent_page;
                   return $parent_page;

   DER ERSTE SCHLUESSEL, DER DEN SLUG TRAEGT, GEWINNT. Nicht der sichtbare —
   der erste. Unsere Seiten melden sich bei Prioritaet 10 versteckt an, das
   legt $submenu[''] an; die Eintraege entstehen bei PHP_INT_MAX unter
   'cluster_one'. Also gewinnt '' — am Knoten gemessen auf Platz 10 von 20.

   WAS HIER PASSIERT UND WAS AUSDRUECKLICH NICHT:

       Nur die REIHENFOLGE der Schluessel wird geaendert. Kein Eintrag wird
       angefasst, keiner entfernt, keiner hinzugefuegt — die Faecher werden
       umgehaengt, nicht ausgeraeumt.

           Ein Umsortieren, das etwas anfasst, ist keines.

   UND DIE FREMDEN SCHLUESSEL BEHALTEN IHRE REIHENFOLGE UNTEREINANDER. Wer
   beim Umsortieren die fremden durcheinanderbringt, hat nicht umsortiert,
   sondern neu gebaut — und aendert die Antwort fuer Haeuser, die mit alldem
   nichts zu tun haben. Darum wird der Dach-Schluessel HERAUSGENOMMEN und
   vorne wieder eingesetzt, statt irgendetwas zu sortieren.

   WARUM NICHT $_wp_real_parent_file['']: das waere WordPress' eigener
   Mechanismus, und er ist zu breit. Er adoptierte JEDE Seite unter '' — die
   versteckten dieses Hauses, Paciolis cv-selftest, und jedes fremde Plugin,
   das add_submenu_page(null, …) benutzt.

       Ein Filter, der jede Seite adoptiert, hat kein Dach gebaut, sondern
       einen Sog.                                              (Mammut)

   ROTER ZEUGE: w85-der-dach-schluessel-steht-vorn.php, fuenf Richtungen.
   Er misst die VORBEDINGUNG und beweist NICHT, dass das Menue aufklappt —
   das ist Christians Auge.
   =========================================================================== */
if (!function_exists('cluster_one_menue_schluessel_ordnen')) {
    function cluster_one_menue_schluessel_ordnen(array $c1_p): void {
        if (!isset($GLOBALS['submenu']) || !is_array($GLOBALS['submenu'])) { return; }
        $c1_dach = cluster_one_menue_dach_slug();

        /* Ohne eigenes Fach gibt es nichts nach vorn zu holen. Das ist der
           Fall -ein Produkt-: dann traegt das Dach keine Eintraege, und der
           oberste Punkt fuehrt direkt auf das Produkt. */
        if (!isset($GLOBALS['submenu'][$c1_dach])) { return; }

        /* Steht er schon vorn, ist nichts zu tun — und zwar wirklich nichts:
           kein Umhaengen, kein Neuaufbau, keine geaenderte Reihenfolge fuer
           irgendjemanden. */
        $c1_schluessel = array_keys($GLOBALS['submenu']);
        if (($c1_schluessel[0] ?? null) === $c1_dach) { return; }

        /* Gibt es ueberhaupt einen Schluessel VOR uns, der einen unserer
           Slugs traegt? Wenn nicht, aendert das Vorziehen nichts an der
           Antwort — und eine Aenderung ohne Wirkung ist eine Aenderung mit
           Risiko und ohne Grund. */
        $c1_meine = array();
        foreach ($c1_p as $c1_v) {
            if (!is_array($c1_v)) { continue; }
            $c1_s = (string) ($c1_v['slug'] ?? '');
            if ($c1_s !== '') { $c1_meine[$c1_s] = true; }
        }
        if ($c1_meine === array()) { return; }

        $c1_noetig = false;
        foreach ($c1_schluessel as $c1_k) {
            if ($c1_k === $c1_dach) { break; }
            foreach ((array) $GLOBALS['submenu'][$c1_k] as $c1_e) {
                if (isset($c1_meine[(string) ($c1_e[2] ?? '')])) { $c1_noetig = true; break 2; }
            }
        }
        if (!$c1_noetig) { return; }

        /* HERAUSNEHMEN UND VORNE WIEDER EINSETZEN. Alle anderen Schluessel
           behalten dabei ihre Reihenfolge — array_merge mit
           Zeichenketten-Schluesseln haelt sie, und der Wert wird als
           Referenz auf dasselbe Feld uebernommen, nicht neu gebaut. */
        $c1_fach = $GLOBALS['submenu'][$c1_dach];
        unset($GLOBALS['submenu'][$c1_dach]);
        $GLOBALS['submenu'] = array_merge(array($c1_dach => $c1_fach), $GLOBALS['submenu']);
    }
}

/* ---------------------------------------------------------------------------
   DIE REIHENFOLGE — VERTRAG 21 §2a, und sie kann NUR hier entstehen.

   Pacioli · Kleinrock · Bismarck · Crockford · Post Dolly. Fuenf Haeuser
   melden ihre Seiten an, und keines kennt seine Nachbarn — ein Haus, das
   sich an die dritte Stelle setzen wollte, muesste zaehlen, wer sonst noch
   da ist, und genau das schafft cluster_one_produkt ab.

       Eine Reihenfolge ist eine Aussage ueber ALLE. Sie gehoert dem, der
       alle kennt, und das ist das Register.

   Deshalb traegt die Anmeldung `nr` (10 · 20 · 30 · 40 · 50, Zehnerschritte,
   damit ein sechstes Produkt dazwischenpasst) — und deshalb wird hier nicht
   nach Namen sortiert: „Bismarck" vor „Crockford" waere heute zufaellig
   richtig und am Tag einer Umbenennung falsch.

   WAS NICHT ANGERUEHRT WIRD: alles, was kein Produkteintrag ist. Die
   Reiter, die Werkzeugpunkte, alles Fremde behaelt seine Stelle und seine
   Reihenfolge untereinander. Sortiert werden ausschliesslich die Plaetze,
   auf denen heute Produkteintraege stehen — die Luecken dazwischen bleiben,
   wo sie sind.
       Ein Sortierer, der mehr anfasst als das, wonach er sortiert,
       ist ein Umbau mit einem harmlosen Namen.

   BENANNTE GRENZE: ein Produkt OHNE `nr` bekommt keine geratene Zahl. Es
   faellt ans Ende seiner Gruppe und behaelt dort die Reihenfolge, in der es
   sich angemeldet hat — und es wird gemeldet, damit die Luecke nicht
   stillschweigend zur Ordnung wird.
   --------------------------------------------------------------------------- */
/* v1.12.0 — DIESER RIEGEL HAT GEFEHLT, und W64s neue Zeile hat es beim
   ERSTEN Lauf gefunden: eine zweite Kopie dieser Datei starb an
   „Cannot redeclare cluster_one_menue_produkte_ordnen()".

       Wer in zwei Tagen zehn Funktionen schreibt und keine einzige
       riegelt, hat die Regel des Hauses gekannt und nicht angewandt.

   Paciolis Zeile misst das ANKOMMEN, indem sie eine ZWEITE Kopie laedt —
   und genau deshalb sieht sie, was kein Zeuge sah, der nur eine laedt. */
if (!function_exists('cluster_one_menue_produkte_ordnen')) {
    function cluster_one_menue_produkte_ordnen(array $c1_p): void {
        /* EINMAL LESEN, viermal benutzen — die Quelle ist ein Funktionsruf,
           und ein Funktionsruf in einer Schleife ist eine Wiederholung ohne
           Gewinn. */
        $c1_dachslug = cluster_one_menue_dach_slug();
        if (!isset($GLOBALS['submenu'][$c1_dachslug])
            || !is_array($GLOBALS['submenu'][$c1_dachslug])) {
            return;
        }
        /* slug => nr, und NUR fuer angemeldete Produkte. */
        $c1_nr = array();
        foreach ($c1_p as $c1_wirt => $c1_v) {
            if (!is_array($c1_v)) { continue; }
            $c1_slug = (string) ($c1_v['slug'] ?? '');
            if ($c1_slug === '') { continue; }
            if (!isset($c1_v['nr']) || !is_int($c1_v['nr'])) {
                cluster_one_produkte_stumm('ohne-ordnungszahl',
                    'Produkt ' . $c1_wirt . ' hat den Slug „' . $c1_slug . '" angemeldet, '
                  . 'aber keine ganzzahlige Ordnungszahl (nr). Sein Menuepunkt steht '
                  . 'deshalb hinter allen, die eine haben — geraten wird nichts.');
                $c1_nr[$c1_slug] = PHP_INT_MAX;
                continue;
            }
            $c1_nr[$c1_slug] = (int) $c1_v['nr'];
        }
        if ($c1_nr === array()) { return; }

        /* Die PLAETZE der Produkteintraege merken, dann nur diese neu belegen. */
        $c1_plaetze = array();
        $c1_eintraege = array();
        foreach ($GLOBALS['submenu'][$c1_dachslug] as $c1_i => $c1_e) {
            $c1_s = (string) ($c1_e[2] ?? '');
            if (!isset($c1_nr[$c1_s])) { continue; }
            $c1_plaetze[]  = $c1_i;
            $c1_eintraege[] = array('nr' => $c1_nr[$c1_s], 'platz' => $c1_i, 'eintrag' => $c1_e);
        }
        if (count($c1_eintraege) < 2) { return; }

        /* STABIL: gleiche nr — und PHP_INT_MAX ist der haeufigste Gleichstand —
           behaelt die Reihenfolge der Anmeldung. usort() in PHP ist seit 8.0
           stabil; der zweite Schluessel steht trotzdem da, weil diese Datei
           auch unter 7.4 laeuft. */
        usort($c1_eintraege, function ($a, $b) {
            if ($a['nr'] !== $b['nr']) { return ($a['nr'] < $b['nr']) ? -1 : 1; }
            return ($a['platz'] < $b['platz']) ? -1 : (($a['platz'] > $b['platz']) ? 1 : 0);
        });

        foreach ($c1_plaetze as $c1_k => $c1_i) {
            $GLOBALS['submenu'][$c1_dachslug][$c1_i] = $c1_eintraege[$c1_k]['eintrag'];
        }
    }
}

/* ---------------------------------------------------------------------------
   DIE UMLEITUNG — Kleinrocks Fund vom 11.09., und sein Grund schlaegt meine
   Neigung.

   Elf seiner Umleitungen zeigen auf admin.php?page=cluster_one&mute=on und
   seinesgleichen. Am Tag, an dem ein zweites Produkt dazukommt, landet ein
   Admin, der stummschaltet, auf dem DACH — still, ohne Fehler, mit dem
   Gefuehl, der Knopf habe nichts getan.

       Eine Umleitung auf einen Slug, der einem anderen gehoeren wird, ist
       heute richtig und am Installationstag eines Nachbarn falsch.

   DIE UMLEITUNG GEHOERT MIR UND NICHT IHM, und das ist seine Begruendung:
   wer die Zahl der Produkte kennt, ist der Boden. Zaehlte Kleinrock in seinem
   Haus die Nachbarn, waere das genau der Griff, den cluster_one_produkt
   abschafft.

   ALLGEMEIN STATT AUF SEINEN NAMEN: umgeleitet wird auf den SLUG DES EINEN
   PRODUKTS, nicht auf „cluster_one_mesh". Das benutzt das Feld, das es dafuer
   gibt, statt einen Namen zu tippen.

   WARUM 'load-' UND NICHT DER RUECKRUF: wenn der Rueckruf laeuft, ist der
   Kopf der Seite laengst geschrieben. Eine Umleitung gehoert vor die Ausgabe.

   BENANNTE GRENZE: die Abfragewerte werden mitgenommen, damit mute=on nicht
   unterwegs verlorengeht. Sie werden dabei gesaeubert — es sind fremde
   Eingaben, auch wenn sie aus dem eigenen Haus kommen.
   --------------------------------------------------------------------------- */
add_action('load-toplevel_page_cluster_one', 'cluster_one_dach_umleitung');

/* KLEINROCKS BITTE VOM 11.09., UND SIE HAT DIESE FUNKTION GETEILT.
   Er schrieb: „lass deinen Zeugen messen, dass der Dachpunkt bei EINEM
   Produkt auf den ANGEMELDETEN Slug geht und nicht auf eine Zeichenkette
   `cluster_one_mesh` in menue.php."

       Ein Slug, den zwei Haeuser schreiben, ist zwei Wahrheiten, sobald
       eines ihn aendert.   (Kleinrock, 11.09.)

   Er ging bereits ueber das Register — aber das war eine BEHAUPTUNG von mir,
   und messbar war sie nicht: die Entscheidung sass in derselben Funktion wie
   das exit, und hinter ein exit kommt kein Zeuge.

       Eine Entscheidung, die im selben Atemzug vollzogen wird, ist nicht
       falsch. Sie ist unpruefbar — und das ist auf Dauer dasselbe.

   Also: cluster_one_dach_ziel() ENTSCHEIDET und gibt den Slug zurueck oder
   eine leere Zeichenkette. cluster_one_dach_umleitung() VOLLZIEHT. Ein
   Zeuge kann die erste fahren, so oft er will. */
/* v1.12.0 — DIESER RIEGEL HAT GEFEHLT, und W64s neue Zeile hat es beim
   ERSTEN Lauf gefunden: eine zweite Kopie dieser Datei starb an
   „Cannot redeclare cluster_one_dach_ziel()".

       Wer in zwei Tagen zehn Funktionen schreibt und keine einzige
       riegelt, hat die Regel des Hauses gekannt und nicht angewandt.

   Paciolis Zeile misst das ANKOMMEN, indem sie eine ZWEITE Kopie laedt —
   und genau deshalb sieht sie, was kein Zeuge sah, der nur eine laedt. */
if (!function_exists('cluster_one_dach_ziel')) {
    function cluster_one_dach_ziel(): string {
        if (!function_exists('cluster_one_produkte')) { return ''; }
        $c1_p = cluster_one_produkte();
        /* v1.12.3 (16.09.2026) — HIER STAND `if (count($c1_p) !== 1) { return ''; }`.
           Sie ist gefallen, PROMPT-HOLLERITH-13 §3, auf Christians Entscheidung.

           Mammuts Frage an ihn lautete woertlich „Wohin soll ein Klick auf
           ‚Cluster One' fuehren?" — Antwort: DIREKT AUFS ERSTE PRODUKT. Die
           Bedingung stammte aus der Zeit, als es nur bei EINEM Produkt etwas
           zu zeigen gab; ab zwei zeigte sie auf die Liste.

           DAS ERSTE NACH 'nr' IST reset(), UND DAS IST KEINE BEQUEMLICHKEIT.
           Der Boden sortiert das Register nach 'nr' (kern.php :5300, uasort,
           bei Gleichstand nach 'name'). Hier wird NICHT noch einmal sortiert —
           dieselbe Regel, die am selben Tag die doppelte Sortierung in dieser
           Datei hat fallen lassen:

               DER BODEN SORTIERT. Die Leserseite nimmt die Reihenfolge, wie
               sie kommt.                                   (VERTRAG §5.1a)

           W80 Richtung 1b faehrt genau das: dieselben fuenf Produkte in
           GEWUERFELTER Reihenfolge, und erwartet das ERSTE DER LIEFERUNG und
           nicht das mit der kleinsten Zahl. Baut hier jemand eine zweite
           Sortierung ein, faellt 1b.

           DIE DREI SCHRANKEN DARUNTER BLEIBEN UNVERAENDERT, und sie gelten
           jetzt fuer das ERSTE Produkt statt fuer das einzige. Faellt eine,
           gibt es die LISTE — nicht das zweite Produkt. Das ist Absicht:
           welches Produkt das Dach meint, darf nicht davon abhaengen, welche
           Seite fuer DIESEN Betrachter gerade angemeldet ist.

               Ein Ziel, das je nach Leser ein anderes ist, ist kein Ziel.
               Es ist eine Auskunft ueber den Leser. */
        if ($c1_p === array()) { return ''; }
        $c1_v    = reset($c1_p);
        $c1_slug = is_array($c1_v) ? (string) ($c1_v['slug'] ?? '') : '';
        /* DER VERGLEICH RUFT DIE QUELLE. Er fragt: zeigt das Dach auf sich
           selbst? Waere hier eine getippte Zeichenkette und dort die Quelle,
           liefe die Frage beim Umbenennen ins Leere — und zwar still. */
        if ($c1_slug === '' || $c1_slug === cluster_one_menue_dach_slug()) { return ''; }
        if (!cluster_one_menue_seite_da($c1_slug)) { return ''; }
        return $c1_slug;
    }
}

/* v1.12.0 — DIESER RIEGEL HAT GEFEHLT, und W64s neue Zeile hat es beim
   ERSTEN Lauf gefunden: eine zweite Kopie dieser Datei starb an
   „Cannot redeclare cluster_one_dach_umleitung()".

       Wer in zwei Tagen zehn Funktionen schreibt und keine einzige
       riegelt, hat die Regel des Hauses gekannt und nicht angewandt.

   Paciolis Zeile misst das ANKOMMEN, indem sie eine ZWEITE Kopie laedt —
   und genau deshalb sieht sie, was kein Zeuge sah, der nur eine laedt. */
if (!function_exists('cluster_one_dach_umleitung')) {
    function cluster_one_dach_umleitung(): void {
        if (!function_exists('wp_safe_redirect')) { return; }
        $c1_slug = cluster_one_dach_ziel();
        if ($c1_slug === '') { return; }

        $c1_args = array();
        foreach ((array) $_GET as $c1_k => $c1_w) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            if (!is_string($c1_k) || is_array($c1_w)) { continue; }
            $c1_args[sanitize_key($c1_k)] = sanitize_text_field(wp_unslash((string) $c1_w));
        }
        $c1_args['page'] = $c1_slug;
        wp_safe_redirect(add_query_arg($c1_args, admin_url('admin.php')));
        exit;
    }
}

/* ===========================================================================
   v1.12.4 (16.09.2026) — DAS DACH KLAPPT NICHT AUF, UND DIE URSACHE IST DIE
   REIHENFOLGE. Sie bleibt.

   CHRISTIAN: „Das Menu steht immer weiter rechts und nie direkt untereinander,
   egal auf welchem Menu-Punkt ich gerade klicke. Das gilt auch fuer das Dach
   (Cluster One) selbst."

   Der zweite Satz ist der, der die Ursache verraten hat — er passte zu keiner
   der beiden Erklaerungen, die vorher im Raum standen.

   WARUM ES PASSIERT, aus WordPress' Quelle GELESEN und nicht erinnert
   (wp-admin/includes/plugin.php, get_admin_page_parent(), woertlich):

       foreach ( array_keys( (array) $submenu ) as $parent_page ) {
           foreach ( $submenu[ $parent_page ] as $submenu_array ) {
               …
               } elseif ( isset( $plugin_page ) && $plugin_page === $submenu_array[2] ) {
                   $parent_file = $parent_page;
                   return $parent_page;
               }

       Der ERSTE Schluessel in $submenu, der den Slug enthaelt, gewinnt.
       Nicht der sichtbare — der erste.

   Unsere Seiten melden sich bei Prioritaet 10 VERSTECKT an — das legt
   $submenu[''] an, ZUERST. Die Produkteintraege entstehen bei PHP_INT_MAX und
   legen $submenu['cluster_one'] an, ZULETZT. Also gewinnt '' fuer jeden der
   fuenf Slugs.

   Und wp-admin/menu-header.php, ebenfalls woertlich:

       if ( ( $parent_file && $item[2] === $parent_file ) || ( empty( $typenow ) && $self === $item[2] ) ) {
           if ( ! empty( $submenu_items ) ) {
               $class[] = 'wp-has-current-submenu wp-menu-open';

   '' ist im Sinne von && falsch, und $self ist 'admin.php' und nie
   'cluster_one'. Die Klasse wird nie gesetzt — deshalb steht es rechts.

   WARUM DIE REIHENFOLGE NICHT VERSCHOBEN WIRD. PHP_INT_MAX ist der Grund,
   warum die Produkteintraege ihre Rueckrufe ueberhaupt finden, warum
   cluster_one_menue_seite_verboten() vollstaendig liest und warum
   remove_submenu_page() im selben Atemzug raeumt.

       Eine Grenze, die man nicht verschieben kann, gehoert gemeldet und
       nicht ueberboten.                                    (:959, dieselbe Datei)

       Ein Riegel, der vor seinem Gegenstand laeuft, ist kein Riegel. Er ist
       eine leere Schleife mit gutem Gewissen.

   Wer die Eintragsschleife nach vorn zieht, holt sich drei Funde zurueck, um
   einen zu loesen. Und $submenu['cluster_one'] kuenstlich frueh anzulegen ist
   der doppelte erste Eintrag — das Stottern, das am 16.09. weggeschnitten
   wurde.

   REPARIERT WIRD DIE LESESEITE. WordPress hat dafuer einen eigenen Haken
   (menu-header.php):

       $parent_file = apply_filters( 'parent_file', $parent_file );

   P1-12 GILT UND WIRD NICHT VERLETZT: zwei LESER sind erlaubt, zwei Schreiber
   nie. Dieser Filter beantwortet eine ANZEIGEFRAGE und schreibt nichts — kein
   add_submenu_page, kein update_option, keine zweite Anmeldung.

       Ein Filter, der eine Anzeigefrage beantwortet, ist ein Leser.
   =========================================================================== */

/* DER DACH-SLUG, EINMAL. — PROMPT-HOLLERITH-19 §4.2, und es war ein Posten
   und kein Auftrag.

   GEMESSEN, bevor etwas dazukam (token_get_all, Kommentare zaehlen nicht):
       als Zeichenkette im CODE:  10
       in Kommentaren:             9
   Mammuts „18x" hat beide Sorten zusammengezaehlt.

   Diese Funktion ist die ELFTE Stelle — und die erste, die eine QUELLE ist
   statt einer Kopie. Der neue Filter unten tippt den Slug NICHT; er fragt
   hier. Die anderen zehn hierher zu ziehen ist ein eigener Zug mit eigenem
   Zeugen; heute waere es ein zweiter Gegenstand in einer Runde, die einen hat.

       Eine elfte Kopie ist schlimmer als zehn. Eine Quelle neben zehn
       Kopien ist der erste Schritt, der die zehn ueberhaupt aufloesbar
       macht.

   OFFENER POSTEN, benannt: menue.php :441 (add_menu_page) ist die Stelle, die
   den Slug tatsaechlich VERGIBT. Sie sollte die Quelle rufen, nicht umgekehrt.
   Dass sie es heute nicht tut, ist der Rest dieses Postens. */
if (!function_exists('cluster_one_menue_dach_slug')) {
    function cluster_one_menue_dach_slug(): string {
        return 'cluster_one';
    }
}

/* v1.12.5 (17.09.2026) — DER FILTER 'parent_file' IST HIER GESTANDEN UND IST
   FORT. Er war nicht falsch: am Knoten gemessen lieferte er 'cluster_one'.
   Sein Ergebnis kam nur nie an.

   wp-admin/menu-header.php, WOERTLICH:

       $parent_file  = apply_filters( 'parent_file', $parent_file );
       $submenu_file = apply_filters( 'submenu_file', $submenu_file, $parent_file );
       get_admin_page_parent();                 // <- nackt. Rueckgabe weggeworfen.
       _wp_menu_output( $menu, $submenu );

   get_admin_page_parent() schreibt `global $parent_file` als NEBENWIRKUNG.
   Sie rechnet unmittelbar vor dem Zeichnen neu, aus array_keys($submenu),
   kommt wieder auf '' und ueberschreibt den Filter. Zwischen ihr und der
   Klassenzeile in _wp_menu_output() steht kein apply_filters und kein
   do_action — dort ist nichts zu greifen.

       Ein Filter, dessen Ergebnis zwei Zeilen spaeter neu berechnet wird,
       ist kein Filter. Er ist eine Meinung, die hoeflich angehoert wird.
                                                              (Mammut)

   ZWEI MESSUNGEN AN DERSELBEN ANFRAGE HABEN SICH WIDERSPROCHEN: der Filter
   lieferte 'cluster_one', und das HTML trug 'wp-not-current-submenu'. Beide
   stimmten. Die Frage war falsch gestellt — gemessen wurde die Kette und
   nicht die Variable beim Zeichnen.

   ER WIRD ENTFERNT UND NICHT STEHENGELASSEN, obwohl er folgenlos ist: nach
   der Umsortierung unten gibt get_admin_page_parent() dieselbe Antwort von
   selbst. Zwei Wege auf eine Wahrheit sind P1-12.

       Ein zweiter Schreiber, der nie gewinnt, ist trotzdem ein zweiter
       Schreiber — und der Tag, an dem er gewinnt, ist der, an dem ihn
       niemand mehr erwartet.                                 (Mammut)

   cluster_one_menue_dach_slug() BLEIBT. Sie ist eine Quelle, und die
   Umsortierung braucht sie. */
