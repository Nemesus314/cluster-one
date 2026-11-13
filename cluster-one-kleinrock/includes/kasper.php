<?php
/**
 * Cluster One — KASPER. „Seid ihr alle da?"
 *
 * Entwurf: Christian Ermisch, 25.08.2026. Gemessen vor dem Einbau (Kasper-Zeuge,
 * sechs Durchgaenge). Der Name ist woertlich: Im Kasperletheater fragt Kasper
 * ins Publikum, und die Antwort ist ein Chor.
 *
 * ---------------------------------------------------------------------------
 * WOZU
 *
 * Wenn aus Mammut vier Lights werden, liegt geteilter Code mehrfach auf einem
 * Knoten, und die Module muessen voneinander wissen: Ist Kleinrock da, gilt
 * Kleinrocks Kalender. Ist Bismarck allein, der kleine.
 *
 * Bisher wurde das mit function_exists() gefragt — 21 Stellen in vier Dateien,
 * die nach 17 verschiedenen Funktionen fragen. JEDE FRAGT NACH EINEM SYMPTOM,
 * KEINE NACH DER TATSACHE.
 *
 * DER FUND, DER DAS ENTSCHIEDEN HAT: kleinrock.php fragt zweimal
 * function_exists('cluster_one_thread_is_label'), begruendet mit „ohne Platon
 * faellt Kleinrock sonst um". Am 25.08. ist thread_is_label in den Boden
 * gezogen. Jedes Light traegt den Boden. Also ist die Frage seither IMMER mit
 * Ja beantwortet — auch in einem Kleinrock-Light ohne eine Zeile Platon. Der
 * Riegel prueft nichts mehr, und der Zeuge waere gruen geblieben mit einer
 * Begruendung, die falsch geworden ist.
 *
 *     Ein Symptom kann umziehen. Eine Tatsache nicht.
 *
 * ---------------------------------------------------------------------------
 * DIE KOERNUNG SIND ORGANE, NICHT PLUGINS
 *
 * Ein Modul fehlt aus zwei Gruenden: das Plugin ist nicht installiert, oder
 * Galvani hat es stillgelegt. Fuer den Fragenden ist das dieselbe Auskunft, und
 * das ist richtig so — ihn interessiert, ob das Organ ANTWORTET, nicht warum es
 * schweigt.
 *
 *     Abschalten heisst schweigen.
 *
 * ---------------------------------------------------------------------------
 * DER WIRT (v0.9.75) — AUS EINER LISTE WIRD EINE LANDKARTE
 *
 * Bis 0.9.74 antwortete Kasper mit Organ => Fassung. Darin stand nicht, WER
 * geantwortet hat. Sobald Mammut und ein Light gleichzeitig auf einem Knoten
 * liegen, ist das die entscheidende Angabe: Turings Schaltpult soll sagen
 * koennen „kleinrock — verdraengt, weil Kleinrock-Light aktiv ist", und dafuer
 * muss es Mammuts kleinrock von dem des Lights unterscheiden koennen. Zwei
 * gleiche Fassungsnummern haetten dieselbe Auskunft gegeben.
 *
 * DER WIRT WIRD ERMITTELT, NICHT UEBERGEBEN. Ein Parameter waere ein Feld, das
 * jemand beim achtzehnten Organ vergisst oder falsch abschreibt — und ein
 * falscher Wirt ist schlimmer als keiner, weil er wie eine Auskunft aussieht.
 * Kasper liest stattdessen die aufrufende Datei aus dem Stapel und nimmt den
 * Plugin-Ordner. Das kann niemand vergessen und niemand falsch eintragen.
 *
 * BENANNTE GRENZE: Liegt die aufrufende Datei nicht unter WP_PLUGIN_DIR — ein
 * mu-Plugin, ein Theme, ein Testlauf ohne WordPress —, ist der Wirt LEER. Leer
 * heisst „nicht feststellbar", nicht „keiner". Es wird nichts geraten.
 *
 * ---------------------------------------------------------------------------
 * DOPPELTE MELDUNG: DER ERSTE GEWINNT, DER ZWEITE WIRD GEMELDET
 *
 * Melden sich zwei verschiedene Wirte fuer dasselbe Organ, dann hat sich dieses
 * Organ aus mehr als einem Paket ANGEMELDET. Das ist nicht dasselbe wie „der
 * Code liegt doppelt und die Verdraengung hat nicht gegriffen": ein Organ,
 * dessen Anmeldung im geteilten Riegel steht, meldet sich genau einmal — fuer
 * diese kann doppelt gar nicht feuern. Es feuert fuer die Anmeldungen, die den
 * Riegel ABSICHTLICH verlassen (H195/H197), und dort ist es die erwuenschte
 * Auskunft und kein Fund.
 *
 * H228 (11.09.2026) — UMBERTOS WORTLAUT, WOERTLICH UEBERNOMMEN. Der alte Satz
 * lautete „dann liegt derselbe Code doppelt auf dem Knoten und die Verdraengung
 * hat nicht gegriffen". Umberto hat gemessen, dass er KEINEN Fall hatte, in dem
 * er stimmte: fuer die mitreisenden Organe kann er nie zutreffen, und fuer
 * `boden` trifft er zu und bedeutet das GEGENTEIL — dass sich boden aus fuenf
 * Paketen meldet, IST die Reparatur von H197. Vorher meldete er sich einmal,
 * und DAS war der Fehler.
 *
 *     Ein Satz, der eine Reparatur einen Ausfall nennt, wird geglaubt.
 *
 * Christians DEV hat es sichtbar gemacht: fuenf Anmeldungen fuer `boden`, und
 * die Datei dahinter — diese hier — ist in allen fuenf Kopien byte-gleich. Die
 * eine Datei, die als Ausfall gemeldet wurde, ist die einzige, bei der es
 * nichts zu verdraengen gibt.
 *
 * WER NACH DEM ALTEN SATZ GREIFT, findet ihn oben noch einmal — als Zitat in
 * genau dem Absatz, der ihn ersetzt. Das ist Maillarts Falle vom 10.09., und
 * sie steht hier, damit niemand aus dem Treffer schliesst, der Satz sei noch da.
 *
 * Kasper behaelt den ERSTEN — weil PHP es genauso macht: die zuerst geladene Datei
 * deklariert die Funktionen, jede spaetere laeuft in ihren function_exists-
 * Riegel. Kaspers Landkarte muss dasselbe sagen wie die Wirklichkeit.
 *
 * Der zweite verschwindet aber nicht, er landet unter 'doppelt'. Ein
 * stillschweigend ueberschriebener Eintrag waere genau die Sorte Fund, die
 * dieses Haus jagt.
 *
 * ---------------------------------------------------------------------------
 * DIE FALLE, UND WARUM SIE EIN FEHLER IST UND KEIN WERT
 *
 * Ein Modul kann erst antworten, wenn seine Datei geladen ist. Wer vor
 * 'plugins_loaded' fragt, bekommt die halbe Liste — UND DIE SIEHT GENAUSO
 * VOLLSTAENDIG AUS WIE DIE GANZE. Gemessen im Zeugen, Durchgang A: die
 * Kalender-Entscheidung faellt falsch aus, und es faellt kein Wort.
 *
 * Der erste Entwurf hatte dafuer einen dritten Zustand („weiss ich noch
 * nicht"). Christians Einwand: Das ist selbst eine Fehlerquelle — einen dritten
 * Wert vergisst irgendwann jemand zu behandeln.
 *
 * Also KEIN Wert, sondern ein FEHLER an den, der zu frueh fragt. Zwei Zustaende
 * fuer alle, die richtig fragen; ein lautes Nein fuer den, der falsch fragt.
 *
 * KASPER IST KEIN TUERSTEHER. Er antwortet ab 'plugins_loaded'; eine Funktion
 * wird beim LADEN der Datei deklariert, also davor. Er kann deshalb nicht
 * entscheiden, welche von zwei Kopien laeuft — nur berichten, welche gewonnen
 * hat. Wer verdraengen will, liest get_option('active_plugins') im Loader; die
 * Liste steht in der Datenbank und ist lesbar, bevor irgendetwas laedt.
 *
 * ---------------------------------------------------------------------------
 * DER EINE WAECHTER
 *
 * Diese Datei gehoert in den Boden und liegt damit spaeter in JEDEM Light, also
 * mehrfach auf einem Knoten. Ohne function_exists() stirbt der Knoten an
 * „Cannot redeclare" (Zeuge, Durchgang E). Das ist die einzige Stelle im Haus,
 * an der der Waechter unvermeidlich ist — dafuer fallen die 21 anderen weg.
 *
 * Der Riegel schuetzt vor „Cannot redeclare". Vor AUSEINANDERLAUFEN schuetzt er
 * nicht: liegt Kasper in vier getrennt versionierten Lights, entscheidet die
 * Ladereihenfolge, welche Fassung gilt — und dann sind die Kopien nicht mehr
 * identisch. Deshalb vergleicht die Schnitt-Suite den Hash jeder
 * ausgelieferten Kopie mit dieser hier.
 *
 * ---------------------------------------------------------------------------
 * DIE VERDRAENGUNG (v1.0.0) — WAS KASPER NICHT KANN, UND WER ES STATTDESSEN TUT
 *
 * Sobald Kleinrock als Light fertig ist, liegt kleinrock.php zweimal auf dem
 * Knoten: einmal in Mammut, einmal im Light. Lights laden VOR Mammut (der
 * Ordner "cluster-one-kleinrock" sortiert vor "cluster-one", weil "-" 0x2D ist
 * und "/" 0x2F). Das Light deklariert also zuerst, und Mammuts Datei stirbt an
 * „Cannot redeclare" — sofort, nicht nach dreissig Minuten.
 *
 * Christian hatte dafuer eine Checkbox vorgeschlagen: ein Haken, der Galvanis
 * Selbstheilung aussetzt. Verworfen, weil das ZWEI SCHREIBWEGE AUF EINE
 * WAHRHEIT waeren (P1-12) — die Anwesenheit des Lights und ein Haken, den
 * jemand setzt. Ein Haken kann falsch sein und vergessen werden.
 *
 *     Ein Symptom kann umziehen. Eine Tatsache nicht.
 *
 * Die Tatsache steht in active_plugins und auf der Platte, beide lesbar, BEVOR
 * irgendetwas laedt — also genau dort, wo Kasper nichts sagen kann.
 *
 * KEINE PFLEGELISTE. Naheliegend waere eine Tabelle „Light X bringt die Organe
 * A, B, C". Die muesste jemand nachfuehren. Stattdessen ist der DATEINAME die
 * Auskunft: traegt ein frueher ladendes Plugin eine Datei gleichen Namens,
 * laedt dieses hier seine eigene nicht.
 *
 * NUR FRUEHER LADENDE VERDRAENGEN. Wer nach mir laedt, kann mich nicht
 * verdraengt haben — ich habe dann bereits deklariert. Die Regel bildet damit
 * genau das ab, was PHP ohnehin tut.
 *
 * ---------------------------------------------------------------------------
 * AB HIER EINGEFROREN. Kasper tut eine Sache. Wenn er sich doch aendern muss,
 * aendert er sich ueberall gleichzeitig — wie ein Protokoll, nicht wie Code.
 * Deshalb hat der Boden ab v1.0.0 eine EIGENE Fassungsnummer: er gehoert
 * keinem Plugin mehr, er liegt in allen.
 */
defined('ABSPATH') || exit;

/* v1.9.14 — EINE ZAEHLUNG, ZWEI FOLGEN, EINMAL GELESEN.
 *
 * Bis 1.9.13 stand die Bedingung nur in der Zeile darunter, und die
 * boden-Meldung am Fuss dieser Datei konnte nicht wissen, ob DIESE Kopie die
 * Funktionen deklariert hat. Sie meldete aus jedem Paket dasselbe — fuenf
 * Anmeldungen, eine laufende Kopie, und nichts unterschied sie.
 *
 *     Wer dieselbe Frage zweimal stellt, bekommt zweimal eine Antwort und
 *     hat keine Gewaehr, dass es dieselbe ist.
 *
 * Jetzt wird sie EINMAL gelesen, bekommt einen Namen und traegt beide Folgen:
 * den Riegel und den zweiten Zustand. */
$cluster_one_kasper_deklariert = !function_exists('cluster_one_kasper');

if ($cluster_one_kasper_deklariert) {

    /* Die Fassung des BODENS, nicht die eines Plugins. Sie steht innerhalb des
       Riegels, wird also nur von der Kopie gesetzt, die auch wirklich laeuft —
       und beantwortet damit auf einem laufenden Knoten die Frage, die bis
       v0.9.76 niemand beantworten konnte: WESSEN Boden regiert hier? */
    /* v1.11.0 (09.09.2026): DER BODEN MELDET EINE SEITE AN, DIE ES NICHT GAB.
       ZUG-DREI fasst zwei Bodendateien an, und beide Male tut der Boden
       etwas Neues, statt etwas anzubieten:
           menue.php   meldet cluster_one_meetings an — versteckt, hinter
                       cluster_one_menue_wenn('kleinrock') UND einem
                       function_exists auf cluster_one_render_meetings_page.
                       Gemessen: die Seite war im ganzen 1.9.12-Baum in
                       KEINER Datei angemeldet. Kleinrocks Kalender-Reiter
                       zeigte auf sie und ins Leere.
           kern.php    legt das Register der Raeumer an (cluster_one_raeumer,
                       raeumen_eines, raeumen_alle) — gemessen: 0 Rufer im
                       1.9.12-Baum, 4 Rufstellen INNERHALB des Zuges selbst.

       DIE MINOR-STELLE, und die Trennung von gestern entscheidet sie:
           Ein Boden, dessen eigene Antwort sich aendert, bewegt die Minor.
           Ein Boden, der eine Frage beantworten KANN, die vorher niemand
           stellen konnte, bewegt die Patch-Stelle.
       Das Raeumer-Register allein waere die Patch-Stelle gewesen — ein
       Angebot ohne Rufer, genau wie cluster_one_menue_pfad() in 1.10.1.
       Die Anmeldung ist es nicht: auf einem Knoten mit kleinrock.php und
       der Render-Funktion existiert ab hier eine Seite, die es vorher nicht
       gab. Das ist keine Bereitschaft, das ist eine Handlung. Die groessere
       Stufe gewinnt.

           Ein Angebot wird erst zur Antwort, wenn jemand es annimmt.
           Eine Anmeldung ist die Antwort selbst.

       Die Werkbank hat zu dieser Nummer AUSDRUECKLICH keinen Vorschlag
       gemacht ("ich schlage keine vor und sage ausdruecklich, dass ich keine
       vorschlage", 00-JETZT-DRAN vom 09.09.). Ich setze sie beim Schnitt,
       nach der stehenden Anweisung vom 08.09. Wer 1.10.2 fuer richtiger
       haelt, sagt es, bevor der Satz hinausgeht — ab dem Paket ist sie
       vergeben. */
    /* v1.10.1 (09.09.2026): DER BODEN BIETET EINEN WEGWEISER AN — UND SONST
       AENDERT SICH NICHTS. ZUG-MENUE-PFAD legt cluster_one_menue_pfad() und
       cluster_one_menue_pfad_zu_frueh() in kern.php: 149 Zeilen HINZU, 0
       entfernt, gemessen an der Datei und nicht am Zettel.
         Gemessen, wer den Wegweiser ruft:
             im ganzen Boden       0 Aufrufe ausser der Definition selbst
             im ganzen Baum        0 Aufrufe
         Gemessen, was sich fuer einen fremden Rufer aendert:
             alt  cluster_one_menue_pfad() ist nicht definiert
             neu  sie antwortet (W56: 14 bestanden, 0 fehlgeschlagen)

       DIE PATCH-STELLE UND NICHT DIE MINOR-STELLE, und ich habe beides
       gegeneinander gehalten, weil die Rechnung knapp ist:
         1.9.0 und 1.10.0 stiegen in der Minor, weil eine Stelle im BODEN
       SELBST vorher fiel und danach lief — der Boden rief ins Leere, der
       Boden trug die Seite nicht. Hier faellt nichts und lief nichts anders:
       kein Aufrufer im Baum, keine Zeile veraendert, kein Verhalten
       ersetzt. Was steigt, ist das ANGEBOT.

           Ein Boden, dessen eigene Antwort sich aendert, bewegt die Minor.
           Ein Boden, der eine Frage beantworten KANN, die vorher niemand
           stellen konnte, bewegt die Patch-Stelle.

       Ohne diese Trennung waere jede hinzugefuegte Funktion eine Minor, und
       die Zahl saegte nur noch das Wachstum ab statt das Verhalten.
         Auch diese Nummer hat die Werkbank nicht vorgeschlagen. Ich setze
       sie beim Schnitt, wie am 08.09. angewiesen, und ich sage dazu, dass
       ich sie fuer knapp halte: wer 1.11.0 will, sagt es, bevor der Satz
       hinausgeht. Ab dem Paket ist sie vergeben. */
    /* v1.10.0 (09.09.2026): DER BODEN TRAEGT DIE SEITE JETZT SELBST.
       Die Bismarck-Einstellungsseite und ihre Maske wohnten in kranz.php.
       Auf einem Knoten ohne kranz.php gab es sie nicht — kein Fatal beim
       Laden, sondern eine Anmeldung, die auf einen Rueckruf zeigt, den es
       nicht gibt: der Fehler kam erst beim Klick. Der Zug der Werkbank
       zieht cluster_one_render_bismarck_settings_page(),
       cluster_one_render_bismarck_settings() und
       cluster_one_render_ai_identities_form() nach kern.php und laesst nur
       die Reiterleiste geriegelt im Kranz.
         Gemessen mit dem Zeugen der Werkbank, von mir gefahren
         (Hollerith, 09.09., zeuge-seitenumzug.php):
             alt (1.9.10)   0 bestanden · 2 fehlgeschlagen   exit 1
             neu            9 bestanden · 0 fehlgeschlagen   exit 0
                            W2 ohne kranz.php: 3315 Bytes, Maske gezeichnet
             Gegenprobe     EINEN Riegel heraus -> 4 · 5     exit 1
       DIE MINOR-STELLE UND NICHT DIE PATCH-STELLE: dieselbe Regel wie 1.8.0
       und 1.9.0 und mit demselben Satz — die Zahl steigt, weil sich das
       VERHALTEN des Bodens geaendert hat und nicht nur sein Text. Zwei
       seiner fuenf Dateien sind angefasst (kern.php +350, menue.php +19/-8).
         Die Werkbank hat diesmal KEINE Bodennummer vorgeschlagen. Ich setze
       sie nach der stehenden Anweisung vom 08.09. ("die Bodennummer setzt du
       beim Schnitt, nicht ich beim Bauen") und nach der Regel, die dieses
       Haus dreimal angewandt hat. Wer eine andere will, sagt es, bevor der
       Satz hinausgeht — vergeben ist sie ab dem Paket. */
    /* v1.9.0 (08.09.2026): DER BODEN FASST DEN KRANZ NICHT MEHR UNGERIEGELT AN.
       kern.php rief cluster_one_blueprint_areas() und
       cluster_one_render_kranz_tabs() nackt; menue.php meldete sechs Seiten
       an, deren Rueckrufe in kranz.php wohnen. kern.php und menue.php reisen
       in ALLEN Paketen — kranz.php nicht mehr, sobald Welle 7 schneidet.
         Gemessen, nicht behauptet (Hollerith, 08.09., ohne kranz.php):
             alt  Call to undefined function cluster_one_blueprint_areas()
             neu  laeuft durch, 0 Eintraege
       Die Zahl steht hier nach derselben Regel wie 1.8.0 und mit demselben
       Satz: 1.9.0 ist ausgegeben, weil sich das VERHALTEN des Bodens
       geaendert hat und nicht nur sein Text. Die Werkbank hat sie
       vorgeschlagen und das Setzen dem Schnitt ueberlassen. */
    /* v1.8.0 (05.09.2026): WELT A — der Kopf gewinnt vor der Konstante.
       cluster_one_fassung_wirt() fragte bis hierher erst die Konstante und
       nahm den Kopf als Rueckfall. Widersprachen sie sich, gewann die
       Stelle, die NICHT mitwandert: die Konstante steht im Lader, der Kopf
       im Paket. Ein Lader aus dem vorletzten Schnitt machte damit jede
       Fassungsauskunft still falsch.
         Bismarck hat den Widerspruch als einziger messbar gemacht (Welt A,
       05.09.), und die Entscheidung dazu steht seit dem 03.09.: der Kopf
       gewinnt, sobald er lesbar ist; die Konstante ist der Weg fuer Welten
       ohne Dateisystem; Uneinigkeit ist ein Befund, keine Auswahl.

       DIESE ZAHL IST MEINE UND NICHT SEINE. Bismarck hat sie ausdruecklich
       nicht gesetzt ("Fassung keine") — Haeuser bauen, die Werkbank
       nummeriert. Eine Fassungsnummer ist ein Versprechen und wird einmal
       ausgegeben; 1.8.0 ist hier ausgegeben, weil sich das VERHALTEN des
       Bodens geaendert hat und nicht nur sein Text. */
    /* ==================================================================
       v1.13.0 (24.09.2026) — DIE ZAHL WIRD AUSGEGEBEN, UND DIE LUECKE
       DAVOR WIRD HINGESCHRIEBEN STATT GEFUELLT.       (VERTRAG 32 §2)

       1.12.0 stand hier vom 03.09. bis zum 24.09. Dazwischen ist der Kern
       von 1.12.0 ueber 1.12.13 auf 1.12.15 gegangen, und mindestens zweimal
       hat sich sein VERHALTEN geaendert — zuletzt, als
       cluster_one_sysop_label() aufhoerte, auf den WP-Anzeigenamen des
       Hauptadministrators zurueckzufallen.

       DIE ZAHL IST NICHT MITGEGANGEN. Drei Wochen lang, in fuenf Paketen,
       auf vier Knoten. Und die Regel dafuer steht seit jeher im Kommentar
       DIESER Zeile, zweimal:

           „Der Boden bewegt sich, also bewegt sich seine Nummer (H75)."

           R-ZUSAGE-1 · Eine Zusage, die der Code ueberholt hat, ist
           schlimmer als gar keine.

       GEFUNDEN HAT SIE NICHT EIN ZEUGE, SONDERN EIN SCHIRM: Turings
       Tabelle zeigte am 23.09. fuenfmal 1.12.0 neben fuenf Paketen, die
       gerade einen neuen Kern bekommen hatten.

       WAS ES GEKOSTET HAT, und es ist nicht nichts: Turings Riegel
       „ein AKTIVER Ordner mit anderem Boden haelt den Lauf an" ist gegen
       unsere eigenen fuenf Pakete BLIND, solange alle dieselbe
       eingefrorene Zahl melden. Fuer fremde Boeden traegt er weiter.

       WARUM 1.13.0 UND NICHT 1.12.16:
       Eine Zahl, die drei Wochen stillstand, bekommt keine Nachkommastelle,
       sondern einen Schnitt. Und die Nummern dazwischen werden NICHT
       nachtraeglich verteilt:

           Eine Zahl, die man nachtraeglich verteilt, erfindet eine
           Geschichte, die es nicht gab.

       1.12.0 steht also fuer alles, was zwischen dem 03.09. und dem
       24.09. am Boden passiert ist. Das ist eine Luecke und keine Folge,
       und sie steht hier, damit niemand sie fuer eine haelt.

       DAMIT SIE NICHT WIEDER STEHENBLEIBT, reicht dieser Kommentar nicht.
       Der Bau haelt seit VERTRAG 32 §2 an, wenn kern.php sich gegen die
       Basis bewegt und diese Zahl nicht.

           Was man vergessen kann, ohne dass es auffaellt, wird
           vergessen. Nicht vielleicht — irgendwann.
       ================================================================== */
    /* 1.15.0 -> 1.16.0 (11.10.2026) — DIE EINSAMMLUNG. DIE ZAHL STEHT HIER,
       WEIL SIE DEN BODEN NENNT UND NICHT DIE DATEI.
       Geaendert hat sich kern.php: neun Namenszeilen kommen jetzt von ihren
       Organen, und darunter steht die Naht cluster_one_namensbriefe.
       kranz.php und menue.php sind byte-gleich. Diese Datei aendert genau
       diese Zahl — und traegt sie damit in FUENF Pakete, auch in Pacioli,
       das von diesem Boden nur kasper.php traegt.
           Ein Boden, der sich bewegt, bewegt seine Nummer in jedem Paket,
           das ihn traegt — auch in dem, das die bewegte Datei nicht hat.
       EINGEFROREN: die Schnitte c bis h dieser Runde fahren in 1.17.0.
       Grund in einem Satz: Hollerith kann keinen Katalog gegen einen Boden
       schneiden, der noch geschnitten wird. */
    /* 1.16.0 -> 1.17.0 (19.10.2026, Neo) — DER DECKEL. Der Boden hat sich
       am 19.10. dreimal bewegt, und die Zahl stand still:
           kern.php   Reiterleiste fragt die Produkte (cluster_one_reiterleiste);
                      Einstellungen in Teilen (_felder); Chronik-Wort fuer den
                      abgelegten Site-Kontext (site_context_laid)
           kranz.php  Reiniger mit _felder; Wegweiser; toter Notiz-Handler fort
           menue.php  Ein-Produkt-Knoten: Einstellungen sichtbar
       Hier stand bis heute 1.16.0 ueber einem Inhalt, der nicht mehr 1.16.0
       war — eine geliehene Nummer (H118), vier Pakete lang. Verteilt mit
       Domaenen-Ersetzung, Hash-gleich; Pacioli traegt nur diese Datei und
       bewegt die Zahl mit. Die Werkbank vergibt Nummern; diese ist die, die
       kasper.php selbst angekuendigt hat. */
    /* 1.17.0 -> 1.17.1 (27.10.2026, Neo) — menue.php: das Dach liest den Haken
       einer versteckten Seite aus $_registered_pages statt ihn nachzurechnen
       (cluster_one_menue_seite_haken); auf einer fremden obersten Seite wie
       Bricks fehlten sonst vier von fuenf Eintraegen. Nur menue.php bewegt. */
    /* 1.19.5 -> 1.19.6 (01.11.2026, Neo) — Plugin Check: drei translators-Kommentare
       (kranz.php 1, kern.php 2). Kein Verhalten bewegt; die Nummer bewegt sich, weil
       sich der Boden bewegt (H75). */
    /* 1.19.6 -> 1.19.7 (01.11.2026, Neo) — nur Zeilenenden: die drei Kommentarzeilen
       zu 1.19.6 kamen mit LF in eine CRLF-Datei (Plugin Check: LineEndings.Mixed).
       Kein Zeichen sonst bewegt; die Nummer bewegt sich, weil sich der Boden bewegt. */
    /* 1.19.7 -> 1.19.8 (01.11.2026, Neo) — Plugin Check „auf null", Boden-Teil: kern.php zwei
       Reparaturen (wp_strip_all_tags ohne Rueckfall; Tabellenname als %i in prepare) und
       phpcs-Vermerke mit Grund in kern/kranz/menue (38 Meldungen). kasper.php selbst: nur
       diese Zeilen; die fuenf Meldungen zu Zeile 791/792 bleiben, bis Nemesus entscheidet. */
    /* 1.19.8 -> 1.19.9 (01.11.2026, Neo) — Nemesus hat entschieden: zwei Kommentarzeilen um den
       _doing_it_wrong-Aufruf zu cluster_one_fassung_wirt (phpcs:disable/enable mit Grund).
       Erster Eingriff in den Text dieser Datei seit der Regel; kein Zeichen Code bewegt. */
    /* 1.19.9 -> 1.20.0 (02.11.2026, Neo) — die Praefix-Runde (Nemesus: Umbenennen, fuers Verzeichnis).
       kern/menue/kranz/speicher rufen Pacioli unter den neuen Namen (pacioli_ statt cv_), $c1_* auf
       Dateiebene heisst dort $cluster_one_*. Diese Datei: nur die Nummer. $c1_kasper_deklariert bleibt,
       bis Nemesus entscheidet (eine Meldung je Paket). */
    /* 1.20.0 -> 1.20.1 (02.11.2026, Neo) — Reparatur der Praefix-Runde: die umbenannten Variablen auf
       Dateiebene hatten ihren ersten Buchstaben verloren ($c1_kern_laeuft war zu $cluster_one_ern_laeuft
       geworden; ein substr-Fehler im Bauwerkzeug, 82 Namen in 35 Dateien, gemessen am Null-Satz). Jetzt
       $cluster_one_kern_laeuft usw. in kern/kranz/menue/speicher. Diese Datei: nur die Nummer.
       Kein Verhalten bewegt — jeder Name war in seiner Datei in sich stimmig, nur falsch. */
    /* 1.20.1 -> 1.21.0 (02.11.2026, Neo) — Runde Pi, die Warnungsrunde. Nemesus' Wort: kasper.php darf angefasst
       werden. Hier: $c1_kasper_deklariert heisst $cluster_one_kasper_deklariert (drei Code-Zeilen; die Verzeichnis-Regel
       fuer globale Namen), und drei Vermerke mit Grund an debug_backtrace (misst den Melder). kern/kranz/menue/faust/
       feynman: Vermerke je Zeile, wp_unslash an fuenf Stellen, kranz 636ff. disable/enable (Nonce prueft der Aufrufer),
       Chronik: gregor_unlocked, Akteur 'wp:' . uid (Speicher-Ereignisse), wp:0 heisst „der Knoten selbst".
       Kein Verhalten bewegt ausser der Chronik-Anzeige. */
    /* 1.21.0 -> 1.21.1 (02.11.2026, Neo) — Runde Rho: kern.php 3545/3593 Vermerk MissingUnslash (der Pruefer kennt
       stripslashes nicht als Unslash; der Zweig laeuft ohne WordPress). feynman.php liest why.txt statt why.md
       (Nemesus: „das why in Texte umwandeln"; Inhalt unveraendert). Diese Datei: nur die Nummer. */
    /* 1.21.1 -> 1.22.0 (03.11.2026, Neo) — Runde Tau: FEYNMAN IST BODEN. feynman.php und why.txt reisen in allen fuenf
       Paketen (bis 1.21.1 nur Kleinrock und Bismarck). kern.php: Reiter „Feynman" vor „Knoten" in jeder Leiste
       (cluster_one_render_tabs, zweites Argument fuer zweite Reihen), cluster_one_feynman_seite_aktuell(), die Feynman-
       Seiten heissen nach der Herkunft (cluster_one_page_h1). feynman.php: Hausleiste oben, eigene Reihe darunter ohne
       „Knoten", Herkunft in den Adressen, cluster_one_feynman_seiten(). Diese Datei: nur die Nummer. */
    /* 1.22.0 -> 1.22.1 (03.11.2026 mittags, Neo) — Runde Ypsilon: der Feynman-Reiter steht HINTER „Knoten", als letzter
       (Nemesus nach dem Tau-Lauf: „Feynman sollte immer als letzter Tab kommen"). kern.php, cluster_one_render_tabs:
       gleicher Block, anderer Platz. Diese Datei: nur die Nummer. */
    /* 1.22.1 -> 1.22.2 (03.11.2026 nachmittags, Neo) — Runde Psi: nur why.txt. Die Fusszeilen nannten die Datei noch why.md
       (deutsch: „Stand v0.9.30, 18. August 2026"); jetzt why.txt mit Boden-Stand und Datum, der alte Stand steht dahinter.
       Kein PHP bewegt. Diese Datei: nur die Nummer. */
    /* 1.22.2 -> 1.23.0 (03.11.2026 abends, Neo) — Runde Omega: kern.php cluster_one_freigabe_mitglieder() — die Weissliste ohne
       SysOp, KI-Kennungen und Collective, als Liste fuer die Freigabe eines Hauses (Nemesus: Rechte vergibt das Plugin, das sie
       betreffen, auf seiner eigenen Seite; Crockford und Pacioli). Diese Datei: nur die Nummer. */
    define('CLUSTER_ONE_BODEN_FASSUNG', '1.23.0');   /* v1.7.0 (03.09.2026): H197 — die Boden-Meldung
                                                       verlaesst den function_exists-Riegel. Bis
                                                       hierher meldete nur die zuerst geladene Kopie
                                                       'boden'; seit v1.9.4 zaehlt lager() die
                                                       TRAEGER, also zaehlte es einen von fuenf.
                                                       Der Boden bewegt sich, also bewegt sich seine
                                                       Nummer (H75).
                                                       v1.6.0 (02.09.2026): DER BODEN BEKOMMT EINEN
                                                       VERTRAG. H112 — ein Wirt loest eine Zusage ein
                                                       (<ORDNER>_VERSION) statt einen Namen zu tragen;
                                                       H118 — die Fassung wird beim Melden GEMESSEN
                                                       statt spaeter von CLUSTER_ONE_VERSION geliehen;
                                                       H179/H184 — die Familie wird an fuenf Stellen
                                                       nicht mehr am Ordnernamen erkannt, sondern
                                                       daran, ob der Ordner den Boden traegt
                                                       (cluster_one_familie_auskunft, ja/nein/fehlt).
                                                       Der Boden bewegt sich, also bewegt sich seine
                                                       Nummer (H75).
                                                       v1.5.0 (01.09.2026): DIE SPRACHLISTE BEKOMMT EINEN
                                                       SCHREIBER — und der Konstantenpfad kommt zurueck.
                                                       Holleriths Befund C: seit 1.4.0 gab ein Haekchen eine
                                                       Liste frei, die im Backend niemand fuellen konnte. Das
                                                       Formularfeld fehlte, und der Sanitizer ersetzt statt zu
                                                       mischen (H113) — die Liste fiel bei jedem Speichern weg.
                                                       Die tote Variable dazu stand schon in 1.5.3.
                                                       Dazu zurueckgenommen: 'konstante' => false. Die
                                                       Begruendung war falsch gemessen; CLUSTER_ONE_LANGUAGES
                                                       liefert den Wert seit jeher (anti-babel.php:98) und
                                                       erteilt jetzt auch die Freigabe, wie bei den vier
                                                       anderen Feldern. Und cluster_one_shares() fragt eine
                                                       Liste jetzt auf nicht-leeres Array statt auf !== ''.
                                                       Minor: eine Freigaberegel aendert sich.
                                                       v1.4.0 (31.08.2026): DREI STUECKE IN EINEM SCHNITT, weil
                                                       alle drei den Boden anfassen — zwei Schnitte waeren zwei
                                                       Gelegenheiten, die Byte-Gleichheit ueber fuenf Produkte
                                                       zu verlieren, und genau darauf ruhen H141 und Holleriths
                                                       Zusage.
                                                       (1) Der Uebersetzungs-Lader zieht nach anti-babel.php.
                                                           In der erzeugten Hauptdatei ueberlebte er den
                                                           Neuschnitt nicht (H140, H147).
                                                       (2) cluster_one_auskunft(): ein Knoten kann sich selbst
                                                           befragen, DREIWERTIG — fehlt/nein/ja. Ein Nichtwissen,
                                                           das als Nein zurueckkommt, ist eine Behauptung
                                                           (H112, H119, H144).
                                                       (3) cluster_one_profilfelder(): die freigebbaren Felder
                                                           an genau einer Stelle statt an dreien, 'languages'
                                                           dazu (Christian, 31.08.), und der einzige Riegel
                                                           gegen eine Zusage faellt.
                                                       Minor, nicht Patch: 'languages' reist ab jetzt nur noch
                                                       mit Freigabe. Wer die Antwort des Pings liest, liest
                                                       etwas anderes als vorher.
                                                       v1.3.0: cluster_one_connector_has_key() erkennt eine
                                                       Marke als das, was sie ist — ein Platzhalter ist kein
                                                       Schluessel (Befund Pacioli, roter Zeuge lag bei). Dazu
                                                       neu: cluster_one_ist_fremde_marke(). Der Boden hat sich
                                                       bewegt, also bewegt sich seine Nummer (H75). Die neue
                                                       Minor-Stelle, weil eine Auskunft ihre Bedeutung aendert:
                                                       'configured' ist fuer eine Marke jetzt false, wo es
                                                       vorher true war. Wer das liest, liest etwas anderes als
                                                       gestern — und das ist mehr als eine Nachkommastelle.
                                                       v1.2.1: cluster_one_is_sysop() und
                                                       cluster_one_main_admin_user_id() haben in kern.php eigene
                                                       Riegel bekommen — Zug 1 von drei ihres Umzugs HIERHER
                                                       (H93). Der Begruendungsblock steht in kern.php, an der
                                                       Stelle, die sich bewegt.
                                                       v1.2.0: kern.php bekam cluster_one_akteur_mesh().
                                                       Der Boden hat sich bewegt, also bewegt sich seine
                                                       Nummer (H75). */

    /** Ein Organ meldet sich. Aufruf auf Datei-Ebene, beim Laden.
     *
     * OHNE FASSUNG IST DER NORMALFALL, und das ist eine Lehre aus dem ersten
     * Einbau: Erst stand hier cluster_one_hier('gregor', CLUSTER_ONE_VERSION).
     * Damit hing jede Organdatei an einer Konstante, die erst die Hauptdatei
     * setzt — und keine Datei liess sich mehr allein laden. Der Rauchtest und
     * das Golden-Rig starben sofort, beide zu Recht.
     *
     * Die Fassung wird deshalb beim FRAGEN nachgetragen (da steht die
     * Konstante sicher) und nur dann uebergeben, wenn ein Modul eine EIGENE
     * hat — also spaeter, wenn die Lights getrennte Plugins sind. */
    /* v1.9.14 (11.09.2026) — DER ZWEITE ZUSTAND.
     *
     * $deklariert sagt, ob DIESE Kopie ihre Funktionen deklariert hat oder ob
     * sie in ihrem Riegel stehengeblieben ist. Der Melder sagt damit etwas
     * ueber SICH — nicht ueber die anderen:
     *
     *     Ein Melder kennt sich selbst. „Verdraengt" ist eine BEZIEHUNG, und
     *     Beziehungen gehoeren dem Sammler.          (Maillart, Punkt 6)
     *
     * WARUM DIE VOREINSTELLUNG true IST, UND WARUM DAS KEINE BEQUEMLICHKEIT
     * IST: gezaehlt ueber den Boden und die fuenf Lights liegen 78 Aufrufe von
     * cluster_one_hier(), und JEDER EINZELNE steht innerhalb des Riegels
     * seines Organs. Fuer alle 78 ist „ich habe deklariert" die gemessene
     * Wahrheit. Die Voreinstellung ist also nicht die bequeme Annahme,
     * sondern der gemessene Normalfall.
     *
     *     Eine Voreinstellung, die man gemessen hat, ist eine Aussage.
     *     Eine, die man gewaehlt hat, ist eine Hoffnung.
     *
     * DIE EINE AUSNAHME IST DER KONTROLLFALL: die boden-Meldung am Fuss dieser
     * Datei steht AUSSERHALB des Riegels (H195/H197) und meldet sich aus jedem
     * Paket. Genau sie gibt den Wert ausdruecklich mit. */
    function cluster_one_hier(string $organ, string $fassung = '', bool $deklariert = true): void {
        $c1_stapel = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace -- misst den Melder (eine Ebene, ohne Argumente): Kaspers Frage, aus welcher Datei sich ein Organ meldet
        $c1_wirt   = cluster_one_kasper_wirt((string) ($c1_stapel[0]['file'] ?? ''));
        add_filter('cluster_one_kasper', function ($alle) use ($organ, $fassung, $c1_wirt, $deklariert) {
            if (!is_array($alle)) {
                return $alle;
            }
            /* EINE FORM: IMMER DIE LISTE, auch mit einem Eintrag. (Punkt 7)
               Bis 1.9.13 gab es zwei Formen — mit und ohne 'doppelt' —, und die
               zweite entstand erst beim zweiten Melder. Eine Form, die erst bei
               Bedarf erscheint, zwingt jeden Leser zu einer Fallunterscheidung,
               die er vergessen kann. */
            /* v1.12.5 (17.09.2026) — H104, UND ER IST EINE ZEILE UND KEIN ZUG.
             *
             * DER POSTEN, seit dem 30.08.: „ein Organ nennt im Feld 'fassung'
             * die Nummer SEINES PAKETS, nicht seine eigene." Ich habe ihn
             * zwei Wochen als Feldproblem gefuehrt und dafuer einen
             * Zweischritt ueber fuenf Haeuser veranschlagt.
             *
             * DANN HABE ICH DIE RUFSTELLEN AUFGEMACHT, in allen vier Paketen:
             *
             *     cluster_one_hier('kern',   '', …)    leer
             *     cluster_one_hier('kranz',  '', …)    leer
             *     cluster_one_hier('menue',  '', …)    leer
             *     cluster_one_hier('modi');            leer
             *     cluster_one_hier('umgebung');        leer
             *     cluster_one_hier('landkarte');       leer
             *     cluster_one_hier('faust', CLUSTER_ONE_FAUST_FASSUNG);
             *     cluster_one_hier('post-dolly-light', CLUSTER_ONE_POST_DOLLY_VERSION);
             *
             * KEIN EINZIGES ORGAN NENNT EINE FALSCHE NUMMER. faust nennt seine
             * eigene. Der Lader nennt die seines Pakets — und er IST das Paket.
             * Die uebrigen sechs nennen GAR KEINE, und die Zeile darunter traegt
             * die des Wirts ein.
             *
             * ALSO IST DER FEHLER NICHT IM ORGAN. Er ist hier:
             * zwei Quellen, ein Feld, keine Markierung. Der Leser haelt eine
             * Zahl und kann nicht sagen, woher sie kommt.
             *
             *     Ein Feld, um das sich zwei Fragen streiten, beantwortet am
             *     Ende keine von beiden.                        (Pacioli, 30.08.)
             *
             * UND DIE ANTWORT IST NICHT, DAS FELD ZU TEILEN. Sie ist, ihm ein
             * zweites danebenzustellen, das sagt, WELCHE der beiden Fragen es
             * gerade beantwortet hat. Das weiss nur diese Stelle, im Augenblick
             * des Schreibens — kein Leser kann es nachtraeglich herleiten.
             *
             *     Wer zwei Quellen in ein Feld schreibt, schuldet dem Leser
             *     die Quelle. Sie ist keine Zusatzauskunft. Sie ist die
             *     Haelfte der Zahl.
             *
             * ES BRICHT NICHTS: 'fassung' bleibt Byte fuer Byte, was es war.
             * Jeder bestehende Leser laeuft weiter. Dies ist ein Feld MEHR,
             * keines anders — und deshalb ist es EIN Schritt und kein
             * Zweischritt mit schlechter Mitte.
             *
             * H118 IST DAMIT MITBEANTWORTET: „leere Fassung -> Kasper liest sie
             * am Wirt" war richtig und STILL. Ab jetzt steht es da.
             *
             * BENANNTE GRENZE: 'fassung_von' sagt, WOHER die Zahl kommt, und
             * NICHT, ob sie stimmt. Ob CLUSTER_ONE_FAUST_FASSUNG die Wahrheit
             * ueber faust.php sagt, weiss nur faust.php.
             *     Eine Herkunft ist keine Buergschaft. */
            $alle[$organ]['melder'][] = array(
                'wirt'        => $c1_wirt,
                'fassung'     => ('' !== $fassung) ? $fassung : cluster_one_fassung_wirt($c1_wirt),
                'fassung_von' => ('' !== $fassung) ? 'organ' : 'wirt',
                'deklariert'  => $deklariert,
            );
            /* v1.9.14, ZWEITE FASSUNG — UMBERTOS FUND VOM 11.09., UND ER TRIFFT
               GENAU DIE STELLE, DIE ICH ALS „KANN HEUTE NICHT" GEFUEHRT HABE.

               Bis hierher setzte der ERSTE Melder 'wirt' und 'fassung'
               BEDINGUNGSLOS, und erst ab dem zweiten griff if ($deklariert).
               Bei GENAU EINER Anmeldung mit deklariert=false sagten die zwei
               Formen dann verschiedene Dinge:

                   $e['wirt']                 = 'cluster-one-bismarck'
                   cluster_one_organ_wirt()   = ''  (gemeldet-aber-keiner-…)

               Das alte Feld nannte einen Wirt, aus dem das Organ NICHT laeuft.
               Der neue Zugang sagte die Wahrheit — und zehn Leser lesen noch
               das alte Feld.

                   Eine Luecke ist eine ehrlichere Auskunft als ein Name.
                                                        (Umberto, 11.09.)

               Und meine eigene Zusage neben 'doppelt' lautete: „Zwei Formen
               derselben Wahrheit sind erlaubt, solange EINE geschrieben wird
               und die andere aus ihr faellt." Hier wurden beide geschrieben,
               nach zwei verschiedenen Regeln.

                   Eine benannte Grenze schuetzt den Leser vor dem Fall. Sie
                   macht den Fall nicht richtig.

               Ab jetzt: 'wirt'/'fassung' NUR unter $deklariert, in BEIDEN
               Zweigen. Und 'doppelt' wird aus 'melder' ABGELEITET statt
               geschrieben — eine Wahrheit, eine Schreibstelle. */
            /* v1.12.5 — DIESELBE HERKUNFT AUCH IN DER ALTEN SICHT.
               Zwei Formen derselben Wahrheit sind erlaubt, solange sie NACH
               DERSELBEN REGEL entstehen — genau der Satz, an dem diese Stelle
               am 11.09. schon einmal gescheitert ist. Wer 'fassung' hier
               schreibt und die Herkunft nur oben, hat wieder zwei Formen nach
               zwei Regeln. */
            if ($deklariert) {
                $alle[$organ]['wirt']        = $c1_wirt;
                $alle[$organ]['fassung']     = ('' !== $fassung)
                    ? $fassung : cluster_one_fassung_wirt($c1_wirt);
                $alle[$organ]['fassung_von'] = ('' !== $fassung) ? 'organ' : 'wirt';
            }

            /* 'doppelt' — DIE ALTE SICHT, ABGELEITET. Sie stirbt mit VERTRAG 27
               §5 Schritt 4; bis dahin traegt sie die Leser, die noch nicht
               umgestellt sind. Abgeleitet heisst: aus 'melder', nicht daneben.
               Hat noch KEINE Kopie deklariert, ist der Gewinner leer und alle
               Melder stehen darin — das ist dieselbe Auskunft, die
               cluster_one_organ_wirt() mit seinem dritten Ausgang gibt. */
            $c1_gewinner = (string) ($alle[$organ]['wirt'] ?? '');
            $c1_doppelt  = array();
            foreach ((array) $alle[$organ]['melder'] as $c1_m) {
                $c1_w = (string) ($c1_m['wirt'] ?? '');
                if ('' !== $c1_w && $c1_w !== $c1_gewinner
                    && !in_array($c1_w, $c1_doppelt, true)) {
                    $c1_doppelt[] = $c1_w;
                }
            }
            if (array() !== $c1_doppelt) {
                $alle[$organ]['doppelt'] = $c1_doppelt;
            } else {
                unset($alle[$organ]['doppelt']);
            }

            if (isset($alle[$organ]['wirt'])) {
                return $alle;
            }
            /* v1.9.4 (H118) — DIE FASSUNG WIRD HIER GEMESSEN, NICHT SPAETER
               GELIEHEN. Der Filter laeuft erst, wenn jemand fragt; der Kopf
               des Wirts wird also lazy und je Wirt genau einmal gelesen.
               Bleibt sie leer, bleibt sie leer — eine geliehene Nummer ist
               schlimmer als keine. */
            /* v1.9.14 — HIER STAND EINE ZUWEISUNG AUF DEN GANZEN EINTRAG, und
               sie hat die Liste, die drei Zeilen weiter oben angelegt wurde,
               beim ERSTEN Melder jedes Mal wieder ausgeloescht. W63 hat es
               gefunden: 5 Anmeldungen, 4 gezaehlt, und beim einzelnen Melder
               fehlte das Feld ganz.

                   Ein neuer Zustand, der nur an der Stelle benannt wird, an
                   der er entsteht, trifft weiter unten auf einen Apparat, der
                   ihn nicht kennt.                       (Pacioli, 09.09.)

               Der Apparat war neun Zeilen tiefer und in derselben Funktion.
               Gesetzt werden deshalb die FELDER und nicht der Eintrag. */
            /* v1.9.14, ZWEITE FASSUNG: HIER STAND DIE BEDINGUNGSLOSE ZUWEISUNG,
               die Umberto gefunden hat. Sie ist nach oben gewandert und steht
               jetzt unter $deklariert — in BEIDEN Zweigen dieselbe Regel.
               Uebrig bleibt der Rueckgabewert; der Zweig selbst ist leer und
               steht nur noch da, damit die Form des Eintrags sichtbar bleibt.

                   Ein Zweig, der nur noch zurueckgibt, ist ehrlicher als
                   einer, der etwas tut, wovon der andere nichts weiss. */
            return $alle;
        });
    }

    /** v1.0.8 — WELCHE FASSUNG STECKT IN DIESER DATEI?
     *
     * Bis hierher stand ueberall CLUSTER_ONE_VERSION, an zwoelf Stellen, roh.
     * Mammuts LADER definiert die Konstante. Ein Light nicht. Solange beide
     * nebeneinander liegen, faellt das nicht auf; ein Light ALLEIN — also genau
     * das Ziel des ganzen Umbaus — stirbt daran:
     *
     *     Undefined constant "CLUSTER_ONE_VERSION" @kleinrock.php:487
     *     Undefined constant "CLUSTER_ONE_VERSION" @bismarck.php:531  (admin_init)
     *     Undefined constant "CLUSTER_ONE_VERSION" @platon.php:439    (admin_init)
     *
     * Die beiden letzten sind die schlimmeren: sie haengen an admin_init und
     * laufen nur, wenn ihre Migrations-Marke noch fehlt — also auf einem
     * FRISCHEN Knoten.
     *
     *     Eine Migration laeuft genau einmal — auf dem Knoten,
     *     auf dem noch niemand nachgesehen hat.
     *
     * Kasper weiss bereits per Backtrace, aus WELCHEM Plugin eine Datei kommt.
     * Dann kann er auch sagen, welche Fassung dieses Plugin traegt. Gelesen wird
     * der Plugin-Kopf, einmal je Wirt, gemerkt fuer die Anfrage.
     *
     * WAS SIE ZURUECKGIBT, IST DIE FASSUNG DES WIRTS — nicht "die Fassung des
     * Knotens". Auf einem gemischten Knoten antwortet bismarck.php mit der
     * Nummer von cluster-one-bismarck und galvani.php mit der von cluster-one.
     * Das ist Absicht: eine Migrations-Marke soll sagen, WER sie gesetzt hat.
     *
     * BENANNTE GRENZE, UND SIE IST OFFEN: Fuer die Fassung, die dieser Knoten
     * ANDEREN KNOTEN meldet, ist das die falsche Frage. Ein Knoten hat dort
     * eine Nummer, nicht drei. Die zwei Stellen in kleinrock.php (Mesh-Antwort
     * und Versions-Vergleich) sind deshalb NICHT umgestellt — sie fragen weiter
     * die Konstante, nur geriegelt. Was ein Knoten ohne Mammut seinen Nachbarn
     * sagen soll, ist eine Entscheidung, keine Messung.
     */
    function cluster_one_fassung(string $c1_datei = ''): string {
        static $c1_cache = array();
        if ($c1_datei === '') {
            $c1_stapel = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace -- misst den Melder (eine Ebene, ohne Argumente): Kaspers Frage, aus welcher Datei sich ein Organ meldet
            $c1_datei  = (string) ($c1_stapel[0]['file'] ?? '');
        }
        $c1_wirt = cluster_one_kasper_wirt($c1_datei);
        if ($c1_wirt === '') {
            /* Kein Wirt feststellbar. Dann die Konstante, falls es sie gibt —
               und sonst leer: eine erfundene Nummer waere schlimmer als keine. */
            return defined('CLUSTER_ONE_VERSION') ? (string) CLUSTER_ONE_VERSION : '';
        }
        return cluster_one_fassung_wirt($c1_wirt);
    }

    /** v1.9.4 (H118) — DIE FASSUNG EINES WIRTS, OHNE DEN UMWEG UEBER EINE DATEI.
     *
     * Der Kopfleser stand bis hierher IN cluster_one_fassung() und war nur
     * ueber einen Dateipfad erreichbar. Kasper kennt beim Melden aber den
     * WIRT, nicht die Datei des Fragenden — und musste die Fassung deshalb
     * spaeter aus CLUSTER_ONE_VERSION nachtragen. Das war H118.
     *
     *     Eine Auskunft, die nur ueber einen Umweg erreichbar ist, wird
     *     nicht genommen. Es wird geraten.
     *
     * Herausgeloest, nicht neu geschrieben: derselbe Code, derselbe Cache,
     * eine Tuer mehr. cluster_one_fassung() ruft jetzt hier durch. */
    function cluster_one_fassung_wirt(string $c1_wirt): string {
        static $c1_cache = array();
        if ($c1_wirt === '') {
            return '';
        }
        if (isset($c1_cache[$c1_wirt])) {
            return $c1_cache[$c1_wirt];
        }

        /* v1.9.4 (H112) — DIE ZUSAGE DES WIRTS UND SEIN KOPF.
         *
         * Der Name der Konstante wird aus dem ORDNER abgeleitet, nicht aus
         * einer Liste: cluster-one-bismarck -> CLUSTER_ONE_BISMARCK_VERSION,
         * und cluster-one -> CLUSTER_ONE_VERSION. Mammut faellt damit ohne
         * Sonderfall heraus; es ist einfach ein Wirt wie jeder andere.
         *
         *     Ein Light meldet sich nicht ueber seinen NAMEN als Light,
         *     sondern indem es eine Zusage einloest, die jeder Wirt
         *     einloesen kann.
         *
         * v1.9.8 (Welt A, Vertrag 18) — DIE REIHENFOLGE IST GEDREHT.
         *
         * Bis hierher gewann die Konstante, und der Kopf war ihr Rueckfall.
         * Die Begruendung war gut: die Konstante steht ohne Dateizugriff und
         * ohne WP_PLUGIN_DIR zur Verfuegung. Sie traegt aber nicht den Fall,
         * in dem beide da sind und sich WIDERSPRECHEN — und dieser Fall ist
         * nicht ausgedacht:
         *
         *     Der Kopf wandert mit jedem Schnitt. Die Konstante steht im
         *     LADER, und ein Lader aus dem vorletzten Schnitt macht jede
         *     Fassungsauskunft des Pakets falsch. Still.
         *
         * Deshalb, entschieden am 03.09. und festgehalten in Vertrag 18:
         *
         *     Der Kopf gewinnt, sobald er lesbar ist.
         *     Die Konstante ist der Weg fuer Welten ohne Dateisystem.
         *     Uneinigkeit ist ein BEFUND, keine Auswahl.
         *
         * Der dritte Satz ist der wichtige. Ein stiller Gewinner waere nur
         * die andere Haelfte desselben Fehlers: zwei Stellen, von denen eine
         * kommentarlos gewinnt, sind schlimmer als eine falsche — die falsche
         * faellt irgendwann auf, die stille Auswahl nie. Deshalb wird die
         * Uneinigkeit gemeldet, einmal je Wirt und Anfrage.
         *
         * WAS DAS KOSTET, und es wird hier genannt statt verschwiegen: der
         * Kopf wird jetzt auch dann gelesen, wenn die Konstante da ist — ein
         * glob() und 2 KB je Wirt, EINMAL je Anfrage (statisch gemerkt).
         * Kasper wird von Verwaltungsseiten gefragt, nicht im Seitenaufbau
         * fuer Besucher. Wer das aendert, aendert diese Rechnung mit. */
        $c1_konst = strtoupper(str_replace('-', '_', $c1_wirt)) . '_VERSION';
        $c1_zusage = defined($c1_konst) ? (string) constant($c1_konst) : null;

        $c1_nr = '';
        if (defined('WP_PLUGIN_DIR')) {
            $c1_basis = rtrim(str_replace('\\', '/', (string) WP_PLUGIN_DIR), '/') . '/' . $c1_wirt;
            foreach ((array) @glob($c1_basis . '/*.php') as $c1_f) {
                $c1_kopf = (string) @file_get_contents($c1_f, false, null, 0, 2048);
                if (stripos($c1_kopf, 'Plugin Name:') === false) {
                    continue;
                }
                if (preg_match('~^[\s*]*Version:\s*([0-9][^\s*]*)~mi', $c1_kopf, $c1_m)) {
                    $c1_nr = $c1_m[1];
                }
                break;
            }
        }

        /* Kein Kopf lesbar: die Zusage traegt. Das ist der Rauchtest, das
           Golden-Rig und jede Welt ohne Dateisystem — dort ist die Konstante
           keine zweite Stelle, sondern die einzige. */
        if ($c1_nr === '') {
            $c1_cache[$c1_wirt] = (string) $c1_zusage;
            return $c1_cache[$c1_wirt];
        }

        /* Beide da und uneinig: der Kopf gewinnt, UND es wird gesagt. */
        if ($c1_zusage !== null && $c1_zusage !== $c1_nr && function_exists('_doing_it_wrong')) {
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Entwicklermeldung an _doing_it_wrong(): kein Seiteninhalt; WordPress legt sie selbst durch wp_kses (wp_trigger_error)
            _doing_it_wrong('cluster_one_fassung_wirt',
                sprintf('Wirt %s: Kopf sagt %s, %s sagt %s. Der Kopf gilt. '
                      . 'Die Konstante steht im Lader — vermutlich stammt er aus einem '
                      . 'frueheren Schnitt als das Paket.', $c1_wirt, $c1_nr, $c1_konst, $c1_zusage),
                defined('CLUSTER_ONE_BODEN_FASSUNG') ? CLUSTER_ONE_BODEN_FASSUNG : '');
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            /* KEINE NUMMER VON MIR. Das Haus schreibt hier sonst ein
               eingefrorenes Literal ('0.9.72' bei cluster_one_kasper_zu_frueh) —
               das ist die bessere Auskunft, weil es sagt, SEIT WANN es die
               Pruefung gibt. Ich darf es nicht vergeben; Hollerith setzt es
               beim Schnitt, wenn er die Nummer kennt. Bis dahin steht hier
               die Fassung des Bodens, und die ist wenigstens gemessen.

               UND DIE LUECKE, DIE DAS HAUS SELBST SCHON BENANNT HAT (:430):
               _doing_it_wrong haengt an WP_DEBUG. Auf der Main ist diese
               Meldung stumm — dort gewinnt der Kopf dann doch still. Der
               Befund ist damit BEZEUGT, aber nicht SICHTBAR. Was fehlt, ist
               dieselbe Zahl wie bei zu_frueh: eine, die unter Galvani steht
               und nicht an WP_DEBUG haengt. Sie zu bauen heisst, eine Seite
               eines anderen Hauses zu aendern — deshalb steht sie hier als
               benannte Luecke und nicht als Code. */
        }

        $c1_cache[$c1_wirt] = $c1_nr;
        return $c1_nr;
    }

    /** Aus einem Dateipfad den Plugin-Ordner. Leer = nicht feststellbar. */
    function cluster_one_kasper_wirt(string $datei): string {
        if ($datei === '' || !defined('WP_PLUGIN_DIR')) {
            return '';
        }
        $c1_d = str_replace('\\', '/', $datei);
        $c1_b = rtrim(str_replace('\\', '/', (string) WP_PLUGIN_DIR), '/') . '/';
        if (strpos($c1_d, $c1_b) !== 0) {
            return '';
        }
        $c1_rest = substr($c1_d, strlen($c1_b));
        $c1_p    = strpos($c1_rest, '/');
        return $c1_p === false ? $c1_rest : substr($c1_rest, 0, $c1_p);
    }

    /* v1.9.14 (Maillart, Punkt 8) — EIN BENANNTER ZUGANG FUER „WELCHE LAEUFT".
     *
     * Bis hierher musste jeder Leser, der das wissen wollte, die Tabelle selbst
     * auseinandernehmen — und dabei raten, ob 'wirt' den laufenden oder den
     * ersten Melder nennt. Beide Lesarten waren vertretbar, und genau deshalb
     * gab es sie beide.
     *
     *     Wo eine Auskunft keinen Namen hat, erfindet jeder Leser sich seine
     *     eigene, und zwei Leser erfinden zwei.
     *
     * DREI AUSGAENGE, nicht zwei:
     *     der Wirt      eine Kopie hat deklariert
     *     ''            das Organ meldet sich auf diesem Knoten gar nicht
     *     ''            es meldet sich, aber KEINE Kopie hat deklariert
     * Die letzten beiden sehen von aussen gleich aus und sind es nicht.
     * $wie sagt, welcher Fall vorliegt — wer ihn nicht braucht, uebergibt ihn
     * nicht und bekommt trotzdem keine Luege, nur weniger.
     *
     * BENANNTE GRENZE: der dritte Fall kann heute nicht entstehen. Der Riegel
     * jedes Organs laesst die hoechste aktive Fassung deklarieren, und die
     * findet keine hoehere ueber sich. Er steht hier, weil „kann heute nicht"
     * etwas anderes ist als „kann nicht". */
    function cluster_one_organ_wirt(string $organ, ?string &$wie = null): string {
        $alle = cluster_one_kasper();
        if (!isset($alle[$organ])) { $wie = 'meldet-sich-nicht'; return ''; }
        foreach ((array) ($alle[$organ]['melder'] ?? array()) as $c1_m) {
            if (!empty($c1_m['deklariert'])) { $wie = 'laeuft'; return (string) $c1_m['wirt']; }
        }
        $wie = 'gemeldet-aber-keiner-deklariert';
        return '';
    }

    /** Wie viele Kopien eines Organs haben sich ANGEMELDET? (Punkt 12)
     *
     * Der Name sagt, was gezaehlt wird: ANMELDUNGEN, nicht Kopien auf der
     * Platte und nicht laufende Kopien. Eine Kopie, die hinter ihrem Riegel
     * schweigt, steht hier nicht — und das ist eine Eigenschaft der Zahl und
     * kein Mangel, solange sie im Namen steht. */
    function cluster_one_organ_anmeldungen(string $organ): int {
        $alle = cluster_one_kasper();
        return count((array) ($alle[$organ]['melder'] ?? array()));
    }

    /** „Seid ihr alle da?" — gibt Organ => array('fassung','wirt','melder'[,'doppelt']).
     *
     * 'melder'  v1.9.14: JEDE Anmeldung, immer als Liste, auch bei einer.
     *           array('wirt','fassung','deklariert')
     * 'wirt'    der Wirt der Kopie, die DEKLARIERT hat — bis 1.9.13 war es
     *           der erste Melder, und das ist nur meistens dasselbe.
     * 'doppelt' die alte Sicht auf dieselbe Sache. Sie wird aus 'melder'
     *           beliefert und stirbt mit VERTRAG 27, sobald die elf Leser
     *           umgestellt sind — vier davon wohnen in fremden Haeusern.
     *               Zwei Formen derselben Wahrheit sind erlaubt, solange
     *               EINE geschrieben wird und die andere aus ihr faellt.
     */
    /* =====================================================================
       DER MERKZETTEL — v1.12.0, auf Umbertos Messung und Maillarts drei
       Auflagen.

       UMBERTO, 11.09., GEMESSEN: jeder Aufruf laeuft die ganze Filterkette.
       Die benannten Zugaenge rufen ihn JE ORGAN.

           EIN cluster_one_kasper()      95 Rueckrufe
           organ_wirt() je Organ       1805   (x19)
           beide Zugaenge je Organ     3610   (x38)

       Das ist die Spannung in VERTRAG 27 §5 selbst: „nimm die benannten
       Zugaenge statt eigener Schleifen" ist richtig — und in einer Schleife
       ueber alle Organe ist der benannte Zugang quadratisch.

           Ein statischer Merkzettel im Boden nimmt es fuer ALLE elf Leser
           weg. Eine Zeile hier statt elf Umwegen in fremden Haeusern.
                                                      (Umberto, 11.09.)

       Er hat ausdruecklich NICHT darum herumgebaut. Richtig — wer es in elf
       Haeusern umgeht, hat elf Stellen, an denen es wieder auftaucht.

       MAILLARTS DREI AUFLAGEN (URTEIL XIII §3), und alle drei stehen hier:

       1  ABGELEITET, NICHT PARALLEL. Der Merkzettel IST die Zaehlung und
          keine zweite Wahrheit daneben: EINE Schreibstelle, und sie steht
          in cluster_one_kasper() selbst.

       2  DER GRIFF ZUM VERGESSEN IST SELBST MESSBAR.
          cluster_one_kasper_vergessen() ist benannt und oeffentlich; W63
          benutzt ihn zwischen seinen Welten UND sieht nach, dass das
          Vergessen wirklich vergisst.
              Wer einen Zuruecksetzer baut, schuldet einen Zeugen, der ihn
              benutzt UND nachsieht.               (Maillart, 11.09.)

       3  DIE BEHAUPTUNG UEBER DIE ZEIT, ausgeschrieben, damit ein Zeuge sie
          angreifen kann:

              DER MERKZETTEL BEHAUPTET, DASS SICH KASPERS TABELLE NACH
              'plugins_loaded' INNERHALB EINER ANFRAGE NICHT MEHR AENDERT.

          Warum sie heute traegt: alle Meldungen stehen auf Dateiebene der
          Organdateien und laufen beim Laden, also VOR 'plugins_loaded'.
          W64 bewacht genau das, ohne Ausnahmeliste.
          Wodurch sie fiele: ein Haus, das cluster_one_hier() SPAETER ruft,
          in einem Haken nach 'plugins_loaded'. Dann meldet sich ein Organ
          in eine Tabelle, die schon gemerkt ist, und der Merkzettel
          verschweigt es — still.
              Eine Zwischenspeicherung ist eine Behauptung ueber die ZEIT.

       VOR 'plugins_loaded' WIRD NICHTS GEMERKT. Der Rueckgabewert ist dort
       eine leere Tabelle und kein Ergebnis — sie einzufrieren hiesse, den
       Fehler, den cluster_one_kasper_zu_frueh() MELDET, dauerhaft zu machen.

           Ein Merkzettel, der auch das Scheitern merkt, macht aus einem
           gemeldeten Fehler einen stillen.
       ===================================================================== */
    function cluster_one_kasper_merkzettel(?array $c1_setzen = null,
                                           bool $c1_vergessen = false): ?array {
        static $c1_wert = null;
        if ($c1_vergessen) { $c1_wert = null; return null; }
        if (null !== $c1_setzen) { $c1_wert = $c1_setzen; }
        return $c1_wert;
    }

    /** Vergisst den Merkzettel. Fuer Zeugen, die mehrere Welten in EINEM Lauf
     *  messen — und fuer jeden, der nach 'plugins_loaded' etwas an der
     *  Tabelle aendert und will, dass es ankommt. */
    function cluster_one_kasper_vergessen(): void {
        cluster_one_kasper_merkzettel(null, true);
    }

    function cluster_one_kasper(): array {
        if (!did_action('plugins_loaded')) {
            cluster_one_kasper_zu_frueh();
            return array();
        }
        $c1_gemerkt = cluster_one_kasper_merkzettel();
        if (null !== $c1_gemerkt) {
            return $c1_gemerkt;
        }
        $alle = apply_filters('cluster_one_kasper', array());
        if (!is_array($alle)) {
            return array();
        }
        /* MAILLART, 12.09. — DIE ASYMMETRIE ZWISCHEN MEINEN ZWEI MERKZETTELN,
           und er hat sie GEMESSEN, nicht gelesen.

           Im Zettel steht mein Satz: „eine leere Liste wird nie gemerkt". Er
           ist fuer cluster_one_produkte() richtig — dort steht die Bedingung
           — und war fuer diese Funktion hier FALSCH: sie merkte sich eine
           leere Tabelle und schwieg darueber.

               Zwei Merkzettel nach derselben Regel zu bauen ist eine
               Absicht. Sie nach derselben Regel zu PRUEFEN ist die Arbeit,
               und ich habe nur die erste getan.

           Eine leere Tabelle NACH 'plugins_loaded' ist keine Auskunft: auf
           einem laufenden Knoten meldet sich mindestens kasper.php selbst.
           Sie heisst also „gescheiterte Messung" — und ein Merkzettel, der
           das Scheitern merkt, macht aus einem Fehler einen Dauerzustand. */
        if (array() === $alle) {
            return $alle;
        }
        cluster_one_kasper_merkzettel($alle);
        /* v1.9.4 (H118) — HIER STAND DIE LEIHE, UND SIE IST WEG.
         *
         * Bis 1.9.3:
         *     if (fassung leer && defined('CLUSTER_ONE_VERSION'))
         *         $alle[$o]['fassung'] = CLUSTER_ONE_VERSION;   // „ein Plugin, eine Fassung"
         *
         * Der Kommentar stimmte, solange es EIN Plugin gab. Auf einem
         * gemischten Knoten hat bismarck.php aus cluster-one-bismarck damit
         * MAMMUTS Nummer gemeldet; auf einem Knoten ohne Mammut gar keine.
         *
         *     Eine geliehene Zahl sieht aus wie eine eigene,
         *     bis der Verleiher geht.   (Vertrag 15 §0)
         *
         * Die Fassung kommt jetzt aus dem Kopf des WIRTS, gemessen beim
         * Melden (cluster_one_hier). Bleibt sie leer, bleibt sie leer —
         * die Auskunftsschicht heisst dann „fehlt", nicht „1.9.4". */
        return $alle;
    }

    /** Ist dieses Organ da?
     *
     * BENANNTE GRENZE: Vor 'plugins_loaded' gibt das FALSE zurueck — also die
     * falsche Antwort. Sicherer geht es nicht: ein bool hat keinen dritten
     * Wert, und ein null wuerde in jedem if() genauso zu false. Was diesen
     * Fall findbar macht, ist nicht der Rueckgabewert, sondern die Warnung und
     * die Zahl unter Werkzeuge -> Galvani. Wer eine Entscheidung an modul_da()
     * haengt, haengt sie an den Hook — nicht an die Ladezeit. */
    function cluster_one_modul_da(string $organ): bool {
        $alle = cluster_one_kasper();
        return isset($alle[$organ]);
    }

    /* Der zu fruehe Aufruf wird gemeldet UND gezaehlt.
       WARUM BEIDES: _doing_it_wrong haengt an WP_DEBUG. Auf der Dev steht es
       an, auf der Main nicht — dort waere die Meldung stumm, und eine Warnung,
       die im Betrieb niemand sieht, ist Dekoration. Die Zahl steht unter
       Werkzeuge -> Galvani und ist unabhaengig davon.
       HOECHSTENS EIN SCHREIBVORGANG JE AUFRUF DER SEITE: eine Zahl, die auf
       einem Lesepfad bei jedem Aufruf schreibt, waere ihr eigener Schaden.

       DIESES update_option IST DER GRUND, WARUM KASPER NICHT IN TURING LIEGT:
       Turings einzige echte Eigenschaft ist die maschinell gepruefte Null an
       Schreibvorgaengen. Turing meldet sich stattdessen mit zwei geriegelten
       Zeilen an, statt eine Kopie mitzubringen. */
    function cluster_one_kasper_zu_frueh(): void {
        static $c1_schon = false;
        _doing_it_wrong(
            'cluster_one_kasper',
            'Zu frueh gefragt: vor plugins_loaded ist der Saal nicht voll. Frag im Hook, nicht beim Laden.',
            '0.9.72'
        );
        if ($c1_schon) {
            return;
        }
        $c1_schon = true;
        /* v0.9.72, gefunden von der Galvani-Probe: Diese Funktion lief ohne
           Riegel in update_option(). Wer Kasper SEHR frueh fragt — aus einem
           mu-Plugin, aus einer Umgebung ohne Options-API — bekam statt einer
           Warnung einen fatalen Fehler.

           EINE DIAGNOSE, DIE DEN AUFRUF TOETEN KANN, DEN SIE DIAGNOSTIZIERT,
           ist keine. Die Zahl ist eine Zugabe; die Warnung ist die Hauptsache. */
        if (function_exists('update_option') && function_exists('get_option')) {
            update_option('cluster_one_kasper_zu_frueh', 1 + (int) get_option('cluster_one_kasper_zu_frueh', 0), false);
        }
    }

    /** Traegt ein FRUEHER ladendes Plugin dieselbe Organdatei?
     *
     * Rueckgabe: der Ordner dieses Plugins, oder '' — dann lade selbst.
     * Aufruf gehoert an die Ladestelle, nicht in einen Hook: hier wird
     * entschieden, ob ueberhaupt required wird.
     *
     * BENANNTE GRENZEN:
     * 1. Gesucht wird nach DATEINAMEN, in <ordner>/includes/ und <ordner>/.
     *    Ein Light, das dasselbe Organ unter anderem Dateinamen fuehrt, wird
     *    nicht erkannt — dann kollidiert es wie frueher. Der Dateiname ist
     *    damit ein Vertrag zwischen den Plugins.
     * 2. Nur Ordner, die mit 'cluster-one' beginnen. Ein fremdes Plugin mit
     *    einer gleichnamigen Datei soll uns nicht stilllegen.
     * 3. Ohne WP_PLUGIN_DIR oder ohne Options-API wird NICHT verdraengt. Im
     *    Zweifel laden — ein fehlendes Organ ist der teurere Irrtum. */
    /** v1.9.4 (H179 / H184) — GEHOERT DIESER ORDNER ZUR FAMILIE?
     *
     * An FUENF Stellen stand bisher dieselbe Zeile:
     *
     *     if (strpos($ordner, 'cluster-one') !== 0) { continue; }
     *
     * faust.php:65 · kasper.php:477 · landkarte.php:198 · :283 · :329
     * (Zeilennummern am Baum nach 1.9.3 + Umbertos H188-Lieferung.)
     * 19.10.2026 (Neo): landkarte.php gibt es nicht mehr; ihre drei Stellen
     * leben in turing.php (tu_karte_aktive_familie), weiter am Namen und
     * weiter als benannte Grenze. Die Zeilennummern oben sind Aufzeichnung.
     *
     * Fuenfmal dieselbe Frage, fuenfmal am NAMEN beantwortet — und eine
     * davon hat H170 vier Tage lang versteckt: `connector-vault` hiess nicht
     * so, also war es fuer alle fuenf Stellen kein Verwandter, obwohl es
     * denselben Boden trug. Am 02.09. wurde es umbenannt, und derselbe Code
     * gab ploetzlich eine andere Antwort — ohne dass sich eine Datei
     * geaendert haette, die etwas tut.
     *
     *     Ein Name sagt, wie etwas heisst. Eine mitgelieferte Datei sagt,
     *     was es ist. Wer den Namen fragt, misst eine Absicht von gestern.
     *
     * GEMESSEN WIRD JETZT DER BODEN, und zwar an seinem Anker: `kasper.php`.
     * Diese Datei traegt cluster_one_hier(); ohne sie kann sich ein Plugin
     * gar nicht anmelden. Am 02.09. an allen fuenf ausgelieferten Paketen
     * geprueft — bismarck, kleinrock, pacioli, post-dolly, umberto tragen
     * sie; turing traegt sie nicht und hat kein includes/.
     *
     * WARUM NICHT cluster_one_kasper() FRAGEN: faust.php laeuft auf
     * DATEI-Ebene, lange vor 'plugins_loaded'. Das Register ist dort noch
     * leer, und danach zu fragen hiesse, cluster_one_kasper_zu_frueh() zu
     * wecken. Die Datei liegt schon da, bevor irgendetwas geladen ist —
     * deshalb wird sie gefragt und nicht das Register.
     *
     * BENANNTE GRENZE: Wird der Boden je ohne kasper.php ausgeliefert, ist
     * diese Frage falsch und muss mitwandern. Sie steht deshalb an EINER
     * Stelle statt an fuenf.
     *
     * Ohne WP_PLUGIN_DIR ist die Antwort FALSE — nicht „im Zweifel ja".
     * Ein Fremder, den wir versehentlich als Verwandten behandeln, kann uns
     * stilllegen; ein Verwandter, den wir uebersehen, kostet eine Zeile in
     * einer Uebersicht. */
    function cluster_one_familie_auskunft(string $c1_ordner): string {
        static $c1_cache = array();

        if ($c1_ordner === '' || !defined('WP_PLUGIN_DIR')) {
            return 'fehlt';
        }
        if (isset($c1_cache[$c1_ordner])) {
            return $c1_cache[$c1_ordner];
        }

        $c1_basis = rtrim(str_replace('\\', '/', (string) WP_PLUGIN_DIR), '/') . '/';
        $c1_dir   = $c1_basis . $c1_ordner;

        if (is_file($c1_dir . '/includes/kasper.php')) {
            $c1_cache[$c1_ordner] = 'ja';
        } elseif (!@is_readable($c1_dir)) {
            /* Der Ordner gibt nichts heraus — open_basedir, Rechte, ein
               Einhaengepunkt. Das ist KEIN Nein. */
            $c1_cache[$c1_ordner] = 'fehlt';
        } else {
            $c1_cache[$c1_ordner] = 'nein';
        }

        return $c1_cache[$c1_ordner];
    }

    /** Die bequeme Form: „nicht ausschliessen" — ja ODER fehlt.
     *
     * v1.9.4, und der zweite Wurf. Der erste war ein bool, und
     * `zeuge-h160.php` hat ihn in derselben Minute gefangen: ein Ordner, den
     * open_basedir sperrt, liefert kein kasper.php — und ein bool machte
     * daraus ein „gehoert nicht zur Familie". Genau der Ordner, ueber den
     * H160 eine MELDUNG verlangt, waere damit lautlos verschwunden.
     *
     *     Wer eine fehlende Auskunft als Nein verbucht, baut die
     *     Unwissenheit in die Antwort ein und nennt sie ein Ergebnis.
     *
     * Das ist die Auskunftsschicht, hier zum dritten Mal in diesem Haus:
     * `ja · nein · fehlt`. Uebersprungen wird NUR bei einem gemessenen
     * Nein. Ein `fehlt` laeuft weiter und landet dort, wo es gemeldet
     * werden kann. */
    function cluster_one_familie_ordner(string $c1_ordner): bool {
        return cluster_one_familie_auskunft($c1_ordner) !== 'nein';
    }

    function cluster_one_verdraengt(string $datei): string {
        if ($datei === '' || !defined('WP_PLUGIN_DIR') || !function_exists('get_option')) {
            return '';
        }
        $c1_stapel = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace -- misst den Melder (eine Ebene, ohne Argumente): Kaspers Frage, aus welcher Datei sich ein Organ meldet
        $c1_ich    = cluster_one_kasper_wirt((string) ($c1_stapel[0]['file'] ?? ''));
        if ($c1_ich === '') {
            return '';
        }
        $c1_basis = rtrim(str_replace('\\', '/', (string) WP_PLUGIN_DIR), '/') . '/';
        foreach ((array) get_option('active_plugins', array()) as $c1_e) {
            $c1_teil   = explode('/', str_replace('\\', '/', (string) $c1_e));
            $c1_ordner = (string) ($c1_teil[0] ?? '');
            if ($c1_ordner === $c1_ich) {
                return '';        // ab hier laden alle NACH mir — die koennen mich nicht verdraengen
            }
            if (!cluster_one_familie_ordner($c1_ordner)) {   // H184: der Boden, nicht der Name
                continue;
            }
            if (is_file($c1_basis . $c1_ordner . '/includes/' . $datei)
             || is_file($c1_basis . $c1_ordner . '/' . $datei)) {
                return $c1_ordner;
            }
        }
        return '';
    }

}

/* v1.9.6 (H197) — DIE MELDUNG STEHT JETZT AUSSERHALB DES RIEGELS.
 *
 * Hier stand sie DRINNEN, mit dieser Begruendung:
 *
 *     „Der Boden meldet sich selbst — als letzte Zeile im Riegel, also nur
 *      aus der Kopie, die wirklich laeuft. Genau eine Meldung, kein
 *      'doppelt'."
 *
 * Der Satz war richtig, solange die Meldung hiess „der Boden ist da". Seit
 * v1.9.4 heisst sie etwas anderes: `cluster_one_post_dolly_lager()` zaehlt
 * die TRAEGER des Bodens, und H112 macht daraus den Familienvertrag.
 *
 *     Genau eine Meldung war eine Eigenschaft. Seit jemand sie zaehlt,
 *     ist sie ein Zaehlfehler.
 *
 * GEMESSEN auf Christians Knoten am 03.09.: fuenf Traeger, der Berg zeigt
 * EINE Etappe, und die Landkarte fuehrt `boden` nur unter
 * cluster-one-bismarck. `zeuge-h197.php` stellt es nach: 1 von 5.
 *
 * WARUM DAS HIER SICHER IST: cluster_one_hier() ist zu diesem Zeitpunkt
 * deklariert — entweder von dieser Kopie (dann lief der Riegel gerade) oder
 * von einer frueheren. Dieselbe Bewegung wie H195: die produktbezogene
 * Handlung verlaesst den geteilten Riegel, die Deklarationen bleiben darin.
 *
 * DIE FASSUNG: fuer den ZWEITEN und jeden weiteren Melder wirft
 * cluster_one_hier() das Fassungsargument ohnehin weg — festgehalten wird
 * nur der Wirt unter 'doppelt'. Es wird also nichts geliehen (H118).
 *
 * BENANNTE GRENZE, und sie ist der groessere, offene Teil des Befundes:
 * JEDES mitreisende Organ meldet sich aus seinem eigenen Riegel und damit
 * ebenfalls nur einmal. `doppelt` kann fuer keines von ihnen feuern —
 * Kaspers Satz „der Erste gewinnt, der Zweite wird laut" ist fuer genau die
 * Dateien wirkungslos, die mehrfach reisen. Hier wird NUR der Boden
 * geloest, weil nur er den Familienvertrag traegt. Der Rest ist ein eigener
 * Zug ueber neunzehn Dateien und braucht seine eigene Entscheidung.
 *
 * v1.9.14 — DIE GRENZE IST HALB OFFEN GEBLIEBEN UND WIRD JETZT GESCHLOSSEN,
 * ABER NICHT VON HIER AUS. Diese Zeile gibt ab sofort den DRITTEN Wert mit:
 * ob DIESE Kopie oben die Funktionen deklariert hat. Damit unterscheidet
 * Kasper bei `boden` zum ersten Mal die laufende Kopie von den vier, die
 * mitgereist sind — auf Christians DEV waren das fuenf Anmeldungen, die
 * ununterscheidbar aussahen.
 *
 * Die neunzehn mitreisenden Organdateien bleiben offen. Sie melden weiter aus
 * ihrem Riegel und damit nur, wenn sie laufen. Das ist keine Luege — es ist
 * eine Auslassung, und sie kostet je Datei eine Zeile in fuenf Haeusern.
 * VERTRAG 27 traegt sie, nicht dieser Zug. */
if (function_exists('cluster_one_hier')) {
    cluster_one_hier('boden',
        defined('CLUSTER_ONE_BODEN_FASSUNG') ? CLUSTER_ONE_BODEN_FASSUNG : '',
        $cluster_one_kasper_deklariert);
}
