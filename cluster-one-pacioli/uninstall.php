<?php
/**
 * Deinstallation.
 *
 * Entfernt alle KI-Identitäten samt hinterlegten Schlüsseln, die Rollen,
 * die Marken aus den Anbieter-Zeilen und die eigenen Optionen.
 *
 * A5 — vorher stand hier `'number' => 500`. Bei mehr Konten blieb der Rest
 * stehen, und weil die Rolle danach entfernt wurde, blieben sie als
 * rollenlose Geisterkonten mit verschlüsseltem Schlüssel im Meta zurück.
 * Genau das Verwaisen, das die Deaktivierung ausdrücklich vermeidet.
 * Jetzt: Schleife, bis nichts mehr kommt.
 *
 * =====================================================================
 *  DIE NARBE — H222, entschieden am 07.09.2026
 * =====================================================================
 *
 *  Zwei Räum-Türen, zwei verschiedene Schlüssel: der Knoten-Reset
 *  (SysOp + Nonce + getipptes „RESET") und diese hier (WordPress ruft,
 *  `WP_UNINSTALL_PLUGIN` ist der Nachweis dafür, kein Schlüssel).
 *
 *  DIE SCHLÜSSEL WERDEN NICHT ANGEGLICHEN, und das ist gemessen und
 *  nicht bequem: wer Plugins löschen darf, kommt ohnehin an die Dateien.
 *
 *      Ein Schlüssel, den man nicht durchsetzen kann, ist keiner.
 *
 *  ANGEGLICHEN WIRD DIE NARBE. Diese Tür hatte gar keine: sie nimmt
 *  Benutzerkonten, zwei Rollen, die Capability und den Tresorschlüssel
 *  mit und hinterlässt nichts, woran man später sähe, dass es sie gab.
 *
 *      Was der SysOp tut, bleibt sichtbar.
 *
 *  Also schreibt diese Datei GENAU EINE Option — `cluster_one_entfernt`
 *  — und diese eine wird ausdrücklich NICHT mitgelöscht. Sie ist der
 *  einzige Rest, den es geben darf, und sie ist absichtlich einer.
 *
 * WARUM SIE ZWEIMAL GESCHRIEBEN WIRD, OBWOHL ES EINE OPTION IST
 *
 *  Der Auftrag sagt „VOR dem Löschen". Die Zahl der gelöschten Konten
 *  gibt es vorher aber noch nicht. Beides zugleich geht nicht:
 *
 *      vorher schreiben   -> die Zahl ist eine ABSICHT
 *      nachher schreiben  -> stirbt der Lauf, gibt es gar keine Narbe
 *
 *  Ein abgebrochener Lauf ist erreichbar: ein Fataler in dieser Datei
 *  bricht `delete_plugins()` ab, die Konten sind dann teilweise weg und
 *  das Plugin noch da.
 *
 *  Deshalb: EINE Optionszeile, zwei Schreibvorgänge, und ein Feld
 *  `stand`, das sagt, welcher der beiden zuletzt gelaufen ist.
 *
 *      'begonnen'        die Narbe steht, das Räumen lief noch
 *      'vollzogen'       das Räumen ist durch, nichts blieb übrig
 *      'unvollstaendig'  das Räumen ist durch, und es blieb etwas übrig
 *
 *      Eine Narbe, die „vier Konten gelöscht" sagt, während zwei
 *      übrig sind, ist schlimmer als keine: sie beendet die Frage,
 *      statt sie zu beantworten.
 *
 *  DER DRITTE WERT KAM MIT DER REPARATUR DER SCHLEIFE, 08.09.2026, und
 *  Mammuts Begründung ist die bessere von beiden:
 *
 *      Ohne ihn wäre die reparierte Schleife SCHLIMMER als die kaputte.
 *      Die kaputte hängt sichtbar. Die reparierte käme sauber durch und
 *      schriebe „vollzogen", während Konten übrig sind.
 *
 * DIE SCHLEIFE — repariert, ohne etwas zu erzwingen
 *
 *  Bis zum 08.09. lief sie, solange `get_users()` etwas lieferte.
 *  Scheitert ein `wp_delete_user()` — ein anderes Plugin hängt an
 *  `pre_delete_user` und schützt ein Konto —, liefert dieselbe Abfrage
 *  dasselbe Konto wieder, und sie kommt nie heraus.
 *
 *  Jetzt bricht sie ab, sobald eine Runde KEINE einzige erfolgreiche
 *  Löschung hatte. Dann steht `stand` auf `'unvollstaendig'`.
 *
 *      ERZWUNGEN WIRD NICHTS. Wer ein Konto schützt, hat einen Grund,
 *      und ein Deinstallations-Skript überstimmt ihn nicht.
 * =====================================================================
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

require_once ABSPATH . 'wp-admin/includes/user.php';

/* 18.10.2026 (Neo): geriegelt wie die uebrigen Deklarationen — ein zweites
 * Einbinden dieser Datei starb bisher an `Cannot redeclare constant`. */
defined( 'PACIOLI_NARBE' )  || define( 'PACIOLI_NARBE', 'cluster_one_entfernt' );
defined( 'PACIOLI_ROLLEN' ) || define( 'PACIOLI_ROLLEN', array( 'ai_persona', 'ai_system' ) );
defined( 'PACIOLI_FRAGE' )  || define( 'PACIOLI_FRAGE', 'pacioli_entfernen_daten' );

/* ---------------------------------------------------------------------------
 * DIE ACHT, DIE BLEIBEN — ALS LISTE UND NICHT ALS SATZ
 * 11.10.2026
 *
 *   Weiter unten steht ausfuehrlich, WARUM sie bleiben. Das ist ein
 *   Kommentar, und ein Kommentar ist fuer niemanden messbar.
 *
 *   `zeuge-loeschliste.php` hat heute D1 ROT gemeldet: „acht geschriebene
 *   Optionen werden nie geloescht." Das ist richtig gemessen und beschreibt
 *   seit heute eine ENTSCHEIDUNG und keinen Fehler — der Gegenstand des
 *   Zeugen hat sich geaendert, nicht sein Messen.
 *
 *       Ein Zeuge, dessen Gegenstand sich aendert, muss seinen neuen
 *       nennen. Sonst bewacht er in einem Monat die Erinnerung an den
 *       alten.
 *
 *   Damit er den neuen nennen KANN, muss die Entscheidung an einem Ort
 *   stehen, den man lesen kann, ohne Prosa zu deuten. Hier ist sie. Und
 *   weil eine Liste, die nichts tut, eine Verzierung waere, zaehlt die
 *   Narbe weiter unten, wie viele dieser acht nach dem Entfernen wirklich
 *   noch da sind.
 *
 *   WAS DAS ROT WERT MACHT: eine NEUE Option, die irgendwo im Paket
 *   geschrieben wird und in keiner der zwei Listen steht, faellt dem
 *   Zeugen auf. Vorher fiel ihm alles auf, und darum nichts.
 *
 *       Zwei Listen, die zusammen alles abdecken muessen, sind eine
 *       Pruefung. Eine Liste, die alles abdecken soll, ist eine Hoffnung.
 * ------------------------------------------------------------------------- */
const PACIOLI_BLEIBT = array(
	'pacioli_marker_salt',           // ohne ihn ist jede alte Marke die eines Fremden
	'pacioli_known_providers',        // die Anbieter, die dieser Knoten kennt
	'pacioli_marker_leaks',           // Zaehlung
	'pacioli_marker_unbound',         // Zaehlung
	'pacioli_marker_no_secret',       // Zaehlung
	'pacioli_marker_bound_calls',     // Zaehlung
	'pacioli_owner_gone',             // Zaehlung
	'pacioli_witness_confirmation',   // die Bestaetigung einer Fremdmessung
);

/* ---------------------------------------------------------------------------
 * WIE VIELE KONTEN TRAGEN EINEN HINTERLEGTEN SCHLUESSEL?
 *
 * Geschleift mit Offset, Seite fuer Seite — eine feste Obergrenze liesse
 * einen Rest stehen, und „der Rest" waere hier genau das, was niemand
 * sieht.                                                             (A5)
 *
 * SIE KOMMT ZWEIMAL ZUM EINSATZ, vor und nach dem Loeschen, und das ist
 * der Grund fuer eine Funktion statt zweier Schleifen:
 *
 *     Zwei Schleifen, die dasselbe zaehlen sollen, zaehlen irgendwann
 *     verschieden.                                               (P1-12)
 *
 * GELESEN WIRD DIE META SELBST und nicht `CV_Vault::has_secret()`: beim
 * Entfernen ist der Vault nicht geladen, und was dort geladen ist, ist
 * eine Annahme, solange man es nicht fragt.
 * ------------------------------------------------------------------------- */
function pacioli_konten_mit_schluessel() {
	$mit    = 0;
	$offset = 0;
	$batch  = 200;

	do {
		$seite = get_users(
			array(
				'role__in' => PACIOLI_ROLLEN,
				'fields'   => 'ID',
				'number'   => $batch,
				'offset'   => $offset,
				'orderby'  => 'ID',
				'order'    => 'ASC',
			)
		);

		foreach ( $seite as $id ) {
			if ( '' !== (string) get_user_meta( (int) $id, '_pacioli_secret', true ) ) {
				$mit++;
			}
		}

		$offset += $batch;
	} while ( count( $seite ) === $batch );

	return $mit;
}

/*
 * =====================================================================
 *  DREI DINGE, DREI ANTWORTEN — umgebaut am 10.10.2026, Posten B
 * =====================================================================
 *
 *  Bis heute standen hier ZWEI Ausgaenge: „immer" fuer fremdes Eigentum,
 *  „auf Frage" fuer alles Eigene. Christians Grund dafuer bleibt gueltig:
 *  loeschen und neu hochladen ist eine normale Reparatur, und eine
 *  Reparatur soll niemanden seine KIs kosten.
 *
 *  Die Teilung war nur an einer Stelle falsch geschnitten.
 *
 *  1 · FREMDES EIGENTUM — IMMER, OHNE FRAGE
 *
 *      Der verwahrte Schluessel geht in seine Anbieter-Zeile ZURUECK.
 *      Gelingt das nicht, wird die Zeile geleert und es steht da, warum.
 *      Die Faehigkeit `use_own_ai_connector` kommt aus jeder Rolle.
 *
 *          Das ist keine Loeschung. Das ist das Zurueckstellen von
 *          Moebeln, die dieses Plugin verrueckt hat.
 *
 *  2 · DIE EIGENEN SCHLUESSEL — IMMER, OHNE FRAGE (NEU)
 *
 *      `_cv_secret` an jedem Konto, die Kopie in `_cv_quarantine` und
 *      `cv_secret_key`. Gekoppelt wie bisher, nur nicht mehr am Haekchen.
 *
 *          Ein Schluessel, den niemand mehr benutzen kann, ist nicht
 *          harmlos. Er ist nur unbeobachtet.                   (Mammut)
 *
 *      Und das Haekchen, das darueber entschied, war keine Einstellung:
 *
 *          Ein Haekchen, mit dem einer ueber die Schluessel anderer
 *          entscheidet, ist keine Einstellung. Es ist eine Vollmacht
 *          ohne Unterschrift.                                  (Mammut)
 *
 *  3 · DIE KONTEN, DIE ZWEI ROLLEN, DIE EIGENEN OPTIONEN — AUF FRAGE
 *
 *      UNVERAENDERT, UND DAS IST EIN HALTEGRUND, KEINE BEQUEMLICHKEIT.
 *
 *      Mammuts Auftrag B4 sagt, das Haekchen entfaellt. Seine Tabelle
 *      nennt dabei ZWEI Dinge — den verwahrten Schluessel und die
 *      eigenen Schluessel. Das Haekchen traegt DREI: dazu die
 *      KI-Benutzerkonten, die zwei Rollen und neun eigene Optionen.
 *
 *          Es geht etwas, das der Satz nicht nennt.
 *          — dieselbe Form wie Posten 21 §3, nur andersherum
 *
 *      Entfaellt das Haekchen, ist damit ueber BENUTZERKONTEN
 *      entschieden, und zwar ohne dass es jemand gesagt hat. Darueber
 *      entscheidet nicht das Haus (Posten 21 §5). Also bleibt dieser
 *      dritte Teil, wie er war, und die Frage steht im Bericht.
 *
 *      Ein `if ( false )` haette hier dasselbe getan und besser
 *      ausgesehen. Es waere ein Riegel, der immer haelt — der Fehler
 *      von heute morgen, mit Absicht wiederholt.
 *
 *  ZWEI KOPPLUNGEN, und beide kommen aus dem eigenen Code:
 *
 *      Konten OHNE Rollen  = die „rollenlosen Geisterkonten mit
 *      verschluesseltem Schluessel im Meta" aus A5. Also nur zusammen.
 *
 *      Schluessel OHNE cv_secret_key = unlesbar und trotzdem da. Also
 *      auch nur zusammen — jetzt unter „immer".
 *
 *  Auf der Konstanten-Stufe (CV_SECRET_KEY in wp-config.php) hat dieses
 *  Plugin den Tresorschluessel nie angefasst und faengt jetzt nicht
 *  damit an. `delete_option` trifft dort nichts, und die hinterlegten
 *  Schluessel gehen trotzdem — sie sind dann nach dem Entfernen
 *  unlesbar UND fort, und das ist die richtige Haelfte.
 *
 *  DIE FRAGE WIRD NICHT HIER GESTELLT. Eine Deinstallation hat keine
 *  Oberflaeche; sie liest, was vorher entschieden wurde. Fehlt die
 *  Antwort, gilt BEHALTEN — die Voreinstellung ist die, bei der nichts
 *  unwiederbringlich ist.
 *
 *      Eine fehlende Antwort und ein „ja" duerfen nicht dasselbe
 *      bewirken, wenn eines davon Konten vernichtet.
 * =====================================================================
 */
/* ===========================================================================
 *  DIE FRAGE IST WEG — Christians Entscheidung (a), 11.10.2026
 * ===========================================================================
 *
 *  Hier stand:
 *
 *      $cv_daten_loeschen = (bool) get_option( CV_FRAGE, false );
 *
 *  Es gibt nichts mehr zu fragen. Die Schluessel gehen immer, die Konten
 *  und die Rollen bleiben immer. Ein Schalter ohne zwei Ausgaenge ist
 *  kein Schalter.
 *
 *  UND DIE GESPEICHERTE ANTWORT WIRD GELOESCHT, immer: `cv_entfernen_daten`
 *  ist die Antwort auf eine Frage, die es nicht mehr gibt.
 *
 *      Eine gespeicherte Antwort, deren Frage verschwunden ist, wird beim
 *      naechsten Leser eine Vermutung.
 *
 *  Die Konstante CV_FRAGE bleibt stehen, weil die Loeschzeile sie braucht
 *  und `zeuge-loeschliste.php` die Liste gegen den Quelltext prueft.
 * ========================================================================= */

/*
 * WER war das? — gemessen, nicht angenommen.
 *
 * Drei Ausgänge, weil es drei Lagen gibt:
 *
 *   eine Zahl > 0   ein angemeldeter Mensch hat das Plugin entfernt
 *   0               niemand ist angemeldet — WP-CLI, ein Cron, ein Skript
 *   'nicht messbar' die Funktion gibt es hier gar nicht
 *
 * Der dritte Ausgang ist nicht Vorsicht um der Vorsicht willen: diese
 * Datei wird von `uninstall_plugin()` per `include` in einen Zustand
 * hineingezogen, den sie nicht selbst hergestellt hat. Was dort geladen
 * ist, ist eine Annahme, solange man es nicht fragt.
 *
 *     Ein geratener Name ist schlimmer als ein leeres Feld. Das leere
 *     Feld sagt, dass niemand nachgesehen hat; der geratene Name sagt,
 *     jemand habe es getan.
 */
$pacioli_durch = 'nicht messbar';

if ( function_exists( 'get_current_user_id' ) ) {
	$pacioli_durch = (int) get_current_user_id();
}

/* Welche der beiden Rollen gibt es auf diesem Knoten überhaupt? */
$pacioli_rollen = array();

foreach ( PACIOLI_ROLLEN as $pacioli_role ) {
	if ( function_exists( 'get_role' ) && get_role( $pacioli_role ) ) {
		$pacioli_rollen[] = $pacioli_role;
	}
}

/* Wie viele Konten stehen da? Sie BLEIBEN, aber die Narbe nennt sie. */
$pacioli_gefunden = 0;

foreach ( PACIOLI_ROLLEN as $pacioli_role ) {
	$pacioli_zaehlung = new WP_User_Query(
		array(
			'role'        => $pacioli_role,
			'fields'      => 'ID',
			'number'      => 1,
			'count_total' => true,
		)
	);

	$pacioli_gefunden += (int) $pacioli_zaehlung->get_total();
}

update_option(
	PACIOLI_NARBE,
	array(
		'wann'      => gmdate( 'c' ),
		'konten'    => 0,
		'geblieben' => $pacioli_gefunden,
		'rollen'    => $pacioli_rollen,
		'durch'     => $pacioli_durch,
		'daten'     => 'schluessel_immer_konten_bleiben',
		'stand'     => 'begonnen',
	),
	false
);

/*
 * DREI FELDER, DREI VERSCHIEDENE WAHRHEITEN — und 'konten' behaelt seine.
 *
 *   'konten'     wie viele KI-Konten dieses Entfernen GELOESCHT hat.
 *                Bedeutung unveraendert seit 07.09. Der Wert ist jetzt
 *                immer 0, und das ist die Auskunft und kein Fehler.
 *   'geblieben'  wie viele stehen geblieben sind. NEU.
 *   'daten'      stand auf 'geloescht' oder 'behalten'. Beides waere
 *                jetzt falsch: die Schluessel gehen und die Konten
 *                bleiben. Also ein dritter Wert, der beides nennt.
 *
 *     Ein Feld, dessen Bedeutung sich aendert, waehrend sein Name bleibt,
 *     ist eine Falle fuer den, der es seit einem Monat liest. Ein neues
 *     Feld daneben ist eine Auskunft.
 *
 * AN DIE WERKBANK: wer `cluster_one_entfernt` liest (H222), bekommt
 * 'konten' => 0 und muss 'geblieben' dazulesen, um zu wissen, dass noch
 * Konten dastehen. Der dritte Wert von 'daten' ist neu und wird von einem
 * Leser, der zwei kennt, nicht erkannt.
 */

/* ===========================================================================
 *  DIE KONTEN UND DIE ZWEI ROLLEN BLEIBEN — IMMER
 *  Christians Entscheidung (a), 11.10.2026, mit Mammuts Schaerfung
 * ===========================================================================
 *
 *  Hier stand eine Schleife, die KI-Konten geloescht hat, und dahinter
 *  `remove_role()`. Beides faellt. Was bleibt, ist die Schleife.
 *
 *      Eine Schleife, die ihren Gegenstand wechselt, stirbt nicht. Was
 *      faellt, sind zwei Aufrufe in ihrem Leib.               (Mammut)
 *
 *  UND DIE ROLLEN BLEIBEN MIT DEN KONTEN, und das ist Mammuts Zusatz zu
 *  meinem Vorschlag:
 *
 *      Konten bleiben + Rollen gehen = ein WP-Benutzer mit einer Rolle,
 *      die es nicht gibt.
 *
 *      Eine Huelle, die bleibt, muss sagen koennen, wessen Huelle sie
 *      ist. Nimmt man ihr die Rolle, bleibt ein Benutzer ohne Erklaerung
 *      — und der ist schlimmer als eine leere Huelle mit Namen.
 *
 *  Die Kopplung Konten/Rollen aus A5 gilt also woertlich weiter, nur in
 *  die andere Richtung: beide bleiben.
 *
 * ---------------------------------------------------------------------------
 *  UND HIER WIDERSPRECHE ICH MAMMUTS RECHNUNG — GEMESSEN
 * ---------------------------------------------------------------------------
 *
 *  Mammut schreibt: „sie zaehlen jetzt jedes Konto und nicht nur die
 *  geloeschten", und die Schleife laufe „weiter ueber ALLE Konten", weil
 *  `_cv_secret` an jedem Konto gehe.
 *
 *  GEMESSEN AN MEINEM EIGENEN B3: der Schluessel geht NICHT je Konto,
 *  sondern in EINEM Aufruf fuer alle —
 *
 *      delete_metadata( 'user', 0, '_cv_secret', '', true )
 *
 *  Eine Schleife, die jedes Konto anfaesst, braucht es dafuer nicht. Und
 *  eine Zaehlung von Konten, die niemals geloescht werden, koennte nie
 *  rot werden.
 *
 *      Ein Zaehler, der nie rot wird, weil sein Gegenstand nicht mehr
 *      scheitern kann, ist eine Verzierung.               (R-ZAEHLER-1)
 *
 *  ABER MAMMUTS SATZ IST TROTZDEM RICHTIG, nur mit einem anderen
 *  Gegenstand: es gibt genau eine Sache, die nach dem Loeschen noch
 *  scheitern kann, und die muss gezaehlt werden —
 *
 *      LIEGT NACH DEM LOESCHEN NOCH EIN SCHLUESSEL AN EINEM KONTO?
 *
 *  `delete_metadata()` kann fehlschlagen oder gefiltert werden; ein
 *  fremdes Plugin an `delete_user_meta` oder ein Objekt-Zwischenspeicher
 *  reicht. Dann steht „vollzogen" ueber einem Knoten, auf dem Schluessel
 *  liegen geblieben sind.
 *
 *      Eine Narbe, die „vollzogen" sagt, waehrend Schluessel uebrig
 *      sind, ist schlimmer als keine: sie beendet die Frage, statt sie
 *      zu beantworten.                                   (der alte Satz,
 *                                                         neuer Gegenstand)
 *
 *  Also: die Schleife ZAEHLT NACH, mit demselben Bau wie vorher — Seite
 *  fuer Seite, feste Obergrenze waere ein Rest, den niemand sieht (A5) —
 *  und der dritte Wert der Narbe behaelt seinen Anlass.
 *
 *  WAS WIRKLICH ENTFAELLT: der Selbstabbruch `$cv_runde > 0`. Er hing an
 *  `wp_delete_user()`, das dasselbe Konto zweimal liefern kann. Eine
 *  Zaehlschleife mit Offset kommt immer heraus; sie braucht keine
 *  Notbremse, und eine, die nie bremst, waere wieder eine Verzierung.
 * ========================================================================= */
$pacioli_geloescht = 0;   /* geloeschte KONTEN. Bleibt 0 — und zwar mit Absicht. */
$pacioli_uebrig    = 0;   /* Konten, an denen NACH dem Loeschen noch ein
                        Schluessel liegt. Das ist der neue Gegenstand. */
$pacioli_konten    = 0;   /* KI-Konten, die stehen bleiben. */
$pacioli_vorher    = 0;   /* Konten, die vorher einen Schluessel hatten. */
/* ---------------------------------------------------------------------------
 * IMMER: die Fähigkeit aus JEDER Rolle. Fremdes Eigentum, zurückgestellt.
 *
 * Eine liegengebliebene Fähigkeit ist ein Recht, das auf eine Seite zeigt,
 * die es nicht mehr gibt.
 * ------------------------------------------------------------------------- */
foreach ( wp_roles()->role_objects as $pacioli_role_object ) {
	$pacioli_role_object->remove_cap( 'use_own_ai_connector' );
}

/* Die Schlüssel-Blöcke sind an das Ende dieser Datei gewandert — sie müssen
 * NACH der Rückgabe laufen. Siehe den Block „DIE EIGENEN SCHLUESSEL" unten. */

/* ===========================================================================
 *  IMMER: FREMDES EIGENTUM ZURUECKGEBEN — Posten B1/B2, 10.10.2026
 * ===========================================================================
 *
 *  Bis heute wurde die Anbieter-Zeile GELEERT. Das war die Haelfte der
 *  Rechnung: der Schluessel, der vorher dort lag, ist von diesem Plugin
 *  WEGGENOMMEN worden — nachzulesen im eigenen Kommentar an
 *  class-cv-marker.php:210: „Da liegt ein echter Schluessel.
 *  Sicherstellen statt ueberschreiben — es koennte der einzige Ort sein,
 *  an dem er existiert."
 *
 *      Wer etwas wegnimmt, um es zu schuetzen, schuldet es beim Gehen
 *      zurueck — nicht einen Hinweis darauf, dass es fehlt.   (Mammut)
 *
 *  Und es widersprach der eigenen Zusage zwei Absaetze hoeher auf dem
 *  Schirm: „Das ist keine Loeschung — es ist das Zurueckstellen von
 *  Moebeln, die dieses Plugin verrueckt hat."
 *
 *  DER WEG WAR SCHON DA. Diese Datei schreibt seit 0.3.0 in genau diese
 *  Zeilen; es ist dieselbe Zeile mit einem anderen Wert.
 *
 *  DIE REIHENFOLGE IST DIE SACHE UND NICHT DIE FORM
 *
 *      Der verwahrte Schluessel ist mit `cv_secret_key` verschluesselt.
 *      Dieser Block MUSS vor dem Loeschen des Tresorschluessels laufen.
 *      Steht er danach, gelingt keine einzige Rueckgabe — und zwar
 *      stillschweigend, weil das Entschluesseln dann ordentlich
 *      fehlschlaegt und der Hinweis aus B2 gedruckt wird.
 *
 *          Eine Reparatur, die nach dem Abriss laeuft, ist kein
 *          Zeitfehler. Sie ist eine Reparatur, die nie stattfindet und
 *          dabei ein Protokoll schreibt.
 *
 *      `zeuge-rueckgabe.php` R6 vertauscht die beiden und wird rot.
 *
 *  DIE SIEBEN AUSGAENGE UND IHRE GRUENDE STEHEN NICHT HIER, SONDERN IN
 *  `includes/class-cv-rueckgabe.php`. Dort wohnt die Entscheidung; diese
 *  Datei fuehrt sie aus, und der Schirm des SysOps sagt sie vorher.
 *
 *      Ein Kommentar, der eine Liste aus einer anderen Datei abschreibt,
 *      ist eine zweite Fassung dieser Liste. Sie geht auseinander, ohne
 *      dass jemand sie anfasst.
 * ========================================================================= */
global $wpdb;

/* ---------------------------------------------------------------------------
 * DIE ENTSCHEIDUNG WOHNT NICHT HIER — sie wohnt in class-cv-rueckgabe.php.
 *
 * Dieselbe Frage („kann dieser Schluessel zurueck?") wird an ZWEI Stellen
 * gebraucht: hier, beim Tun, und auf dem Schirm des SysOps, als Vorhersage
 * VOR dem Entfernen (Posten B7). Zwei Stellen, die sie selbst entscheiden,
 * waeren zwei Wahrheiten darueber.                                 (P1-12)
 *
 *     Eine Vorhersage, die ihre eigene Rechnung anstellt, ist keine
 *     Vorhersage. Sie ist eine zweite Meinung mit dem Anspruch der ersten.
 *
 * DIE ZWEI DATEIEN WERDEN AUSDRUECKLICH GEHOLT, und danach wird GEFRAGT, ob
 * es geklappt hat — die eigene benannte Grenze von Zeile 152 gilt: diese
 * Datei wird per `include` in einen Zustand hineingezogen, den sie nicht
 * hergestellt hat. Beim Entfernen liegen die Dateien noch auf der Platte.
 *
 *     Was dort geladen ist, ist eine Annahme, solange man es nicht fragt.
 *
 * KANN SIE NICHT GEHOLT WERDEN, wird keine Zeile angefasst und die Narbe
 * sagt `kein_raeumer`. Die Marken bleiben dann STEHEN.
 *
 *     Eine Marke, die stehen bleibt, taeuscht einen Schluessel vor — und
 *     eine Zeile, die geleert wird, waehrend der echte Schluessel noch
 *     lesbar daneben liegt, vernichtet einen. Von zwei Schaeden ist der
 *     erste der sichtbare und der zweite der endgueltige.
 * ------------------------------------------------------------------------- */
foreach ( array( 'class-cv-crypto.php', 'class-cv-rueckgabe.php' ) as $pacioli_noetig ) {
	if ( is_readable( __DIR__ . '/includes/' . $pacioli_noetig ) ) {
		require_once __DIR__ . '/includes/' . $pacioli_noetig;
	}
}

$pacioli_rueckgabe = array();

if ( class_exists( 'Pacioli_Rueckgabe' ) && method_exists( 'Pacioli_Rueckgabe', 'vollziehen' ) ) {
	$pacioli_rueckgabe = Pacioli_Rueckgabe::vollziehen();
} else {
	$pacioli_rueckgabe = array( '*' => 'kein_raeumer' );
}

/* ===========================================================================
 *  DIE ACHT BETRIEBSZAHLEN BLEIBEN — UND DIE NEUNTE GEHT
 *  11.10.2026, und das ist eine Entscheidung von mir
 * ===========================================================================
 *
 *  Diese neun Loeschungen hingen am Haekchen. Das Haekchen ist weg, also
 *  brauchen sie eine Antwort, und Mammuts Tabelle nennt sie nicht — zum
 *  zweiten Mal an derselben Stelle. Gemeldet, und hier entschieden, weil
 *  der Code sonst halb geschrieben bleibt:
 *
 *      ACHT BLEIBEN. Es sind der Salzwert, die Anbieterliste, fuenf
 *      Zaehlungen und die Fremdmessungs-Bestaetigung. Sie beschreiben
 *      Konten und Rollen, die jetzt STEHEN BLEIBEN.
 *
 *          Daten, die etwas beschreiben, das bleibt, sind kein Rest. Sie
 *          sind die Auskunft darueber.
 *
 *      Und es ist genau der Fall, fuer den Christian am 09.09.
 *      umentschieden hat: loeschen und neu hochladen ist eine Reparatur.
 *      Eine Reparatur, die den Salzwert wegnimmt, gibt jeder Marke eine
 *      neue Signatur — und damit ist jede alte Marke, die noch irgendwo
 *      steht, ploetzlich die eines fremden Knotens.
 *
 *      DIE NEUNTE GEHT: `cv_entfernen_daten` ist die Antwort auf eine
 *      Frage, die es nicht mehr gibt.
 *
 *          Eine gespeicherte Antwort, deren Frage verschwunden ist, wird
 *          beim naechsten Leser eine Vermutung.
 *
 *  BENANNTE GRENZE, und sie ist unangenehm: damit hinterlaesst dieses
 *  Entfernen MEHR als vorher im Behalten-Fall — naemlich dasselbe plus
 *  nichts. Wer den Knoten wirklich leerraeumen will, loescht die
 *  KI-Benutzer in der Benutzerverwaltung von WordPress; dort gehoert das
 *  Loeschen von Konten hin, und dort sieht man, wen man loescht.
 *
 *      Ein Haus, das Benutzerkonten im Vorbeigehen loescht, nimmt der
 *      Benutzerverwaltung eine Entscheidung ab, die dort sichtbar ist
 *      und hier nicht.
 * ========================================================================= */
delete_option( PACIOLI_FRAGE );

/* ===========================================================================
 *  IMMER: DIE EIGENEN SCHLUESSEL — Posten B3, 10.10.2026
 * ===========================================================================
 *
 *  Sie standen bis heute unter der Frage. Sie stehen jetzt unter „immer",
 *  und die Kopplung an `cv_secret_key` bleibt — sie wandert nur mit.
 *
 *      Ein Schluessel, den niemand mehr benutzen kann, ist nicht harmlos.
 *      Er ist nur unbeobachtet.                                  (Mammut)
 *
 *  Der Grund, gemessen und nicht geglaubt: auf der EINFACHEN Stufe liegt
 *  `cv_secret_key` in der Datenbank (`CV_Crypto::tier()` sagt „database").
 *  Bleibt er liegen, ist jeder hinterlegte Schluessel fuer den lesbar, der
 *  die Datenbank lesen kann — fuer immer, und niemand sieht mehr hin.
 *
 *  DREI DINGE, EINE KOPPLUNG:
 *
 *      _cv_secret      der hinterlegte Schluessel an jedem KI-Konto
 *      _cv_quarantine  die KOPIE des fremden Schluessels. Das Original
 *                      ist oben zurueckgegeben; eine Kopie ohne Tresor
 *                      waere unlesbar und trotzdem da.
 *      cv_secret_key   der Tresorschluessel
 *
 *  Entweder gehen alle drei oder keiner. Es bleibt nie etwas Unlesbares
 *  zurueck, und nie ein Lesbares ohne Aufsicht.
 *
 *  DAS KONTO BLEIBT, DER SCHLUESSEL GEHT. `_cv_secret` wird an ALLEN
 *  Konten geloescht, auch an denen, die stehen bleiben. Bis heute ging er
 *  nur mit `wp_delete_user()` — also nur, wenn auch das Konto ging.
 *
 *      „Die eigenen Schluessel gehen immer" und „die Konten gehen auf
 *      Frage" sind zwei Saetze. Solange der Schluessel am Konto haengt,
 *      ist es einer.
 *
 *  UND NIEMAND VERLIERT ETWAS, DER VORHER GEPACKT HAT: der Ausfuhrweg
 *  liegt auf „Meine KIs", und der Satz darueber steht dort dauerhaft
 *  (Posten B6/B7).
 *
 *      Wer nach dem Abriss noch etwas holen will, braucht einen
 *      Schluessel zum Truemmerfeld. Wer vorher packt, braucht keinen.
 * ========================================================================= */
/* VORHER ZAEHLEN: wie viele Konten tragen einen Schluessel? Danach ist es
   nicht mehr feststellbar, und eine Zahl, die man nur nachher hat, kann
   man nicht mit sich selbst vergleichen. */
$pacioli_vorher = pacioli_konten_mit_schluessel();

delete_metadata( 'user', 0, '_pacioli_secret', '', true );
delete_metadata( 'user', 0, '_pacioli_quarantine', '', true );
/* 20.10.2026 (Neo): die fremden Geheimnisse je Mensch (Vertrag cluster_one_tresor,
   api.php) — Passwort und Schluessel fuer den eigenen Speicher der Merkliste.
   Ohne den Tresorschluessel darunter waeren sie ohnehin nur noch Chiffretext. */
delete_metadata( 'user', 0, '_pacioli_fremd', '', true );
delete_option( 'pacioli_secret_key' );

/* ---------------------------------------------------------------------------
 * UND NACHZAEHLEN. Das ist der neue Gegenstand der Schleife.
 *
 *     Eine Narbe, die „vollzogen" sagt, waehrend Schluessel uebrig sind,
 *     ist schlimmer als keine: sie beendet die Frage, statt sie zu
 *     beantworten.
 *
 * `delete_metadata()` kann gefiltert werden — ein fremdes Plugin an
 * `delete_user_meta`, ein Objekt-Zwischenspeicher. Gezaehlt, nicht
 * geglaubt.
 * ------------------------------------------------------------------------- */
$pacioli_uebrig = pacioli_konten_mit_schluessel();
$pacioli_konten = $pacioli_gefunden;

// IMMER: der Zwischenspeicher der Selbstprüfung. Er ist kein Datum, sondern
// ein Abbild von etwas, das es gleich nicht mehr gibt.
delete_transient( 'pacioli_selftest_results' );

/*
 * ZWEI BERICHTIGUNGEN AN DIESER LISTE, 08.09.2026 — gemessen, nicht erinnert.
 *
 *   cv_marker_bound_calls   AUFGENOMMEN. Der Nenner unter der Leck-Zeile
 *                           (H192, seit dem 03.09.). Er wurde geschrieben
 *                           und nie geloescht.
 *
 *   cv_selftest_passed      ENTFERNT. Die Zeile stand hier seit 0.3.0 und
 *                           hat nie etwas geloescht: KEINE Fassung dieses
 *                           Plugins hat diese Option je geschrieben.
 *                           Gemessen an allen Paketen, die hier liegen.
 *
 *       Eine Loeschzeile fuer eine Option, die es nie gab, sieht aus wie
 *       Sorgfalt und ist eine Behauptung.
 *
 * Beide Fehler sind derselbe: eine gepflegte Liste neben einer wachsenden
 * Menge. Sie geht auseinander, ohne dass jemand sie anfasst — deshalb misst
 * `zeuge-loeschliste.php` sie ab heute bei jedem Lauf gegen den Quelltext.
 *
 *     Und NICHT gegen die Datenbank. „Alles was cv_ heisst, gehoert mir"
 *     waere eine Behauptung ueber den Knoten eines Fremden.  (Mammut, 08.09.)
 */

/* ---------------------------------------------------------------------------
 * Die Narbe, fertig geschrieben — und sie bleibt stehen.
 *
 * `konten` trägt jetzt die GEMESSENE Zahl: wie oft `wp_delete_user()` wahr
 * gesagt hat, nicht wie viele Konten vorher dastanden. Weichen die beiden
 * voneinander ab, ist etwas nicht gelöscht worden, und dann soll die Narbe
 * das sagen und nicht die Absicht von vorhin wiederholen.
 *
 * DIESE OPTION WIRD NICHT GELÖSCHT. Sie ist der einzige Rest, den dieses
 * Plugin absichtlich hinterlässt.
 *
 *     Ein Plugin, das sich vollständig entfernt, hinterlässt einen Knoten,
 *     auf dem nichts mehr davon zeugt, dass hier vier Benutzerkonten
 *     gelöscht wurden. Genau das ist die Lücke, die H222 schliesst.
 * ------------------------------------------------------------------------- */
$pacioli_narbe = get_option( PACIOLI_NARBE, array() );
$pacioli_narbe = is_array( $pacioli_narbe ) ? $pacioli_narbe : array();

$pacioli_narbe['konten']       = $pacioli_geloescht;
$pacioli_narbe['geblieben']    = $pacioli_konten;
$pacioli_narbe['schluessel']   = $pacioli_vorher;
$pacioli_narbe['uebrig']       = $pacioli_uebrig;
$pacioli_narbe['stand']        = $pacioli_uebrig > 0 ? 'unvollstaendig' : 'vollzogen';

/* WIE VIELE DER ACHT STEHEN WIRKLICH NOCH DA?
 *
 * Die Liste sagt, welche bleiben SOLLEN. Diese Zahl sagt, welche es sind.
 * Auf einem Knoten, der sie nie gebraucht hat, sind es weniger als acht —
 * und das ist kein Mangel, sondern die Auskunft, dass dort nie gezaehlt
 * wurde.
 *
 *     Eine Liste sagt, was gelten soll. Erst eine Zahl daneben sagt, was
 *     gilt.
 */
$pacioli_bleibt_da = 0;

foreach ( PACIOLI_BLEIBT as $pacioli_name ) {
	if ( null !== get_option( $pacioli_name, null ) ) {
		$pacioli_bleibt_da++;
	}
}

$pacioli_narbe['bleibende']    = $pacioli_bleibt_da;
$pacioli_narbe['bleibensoll']  = count( PACIOLI_BLEIBT );

/*
 * DER DRITTE WERT HAT EINEN NEUEN ANLASS, UND ER IST ERREICHBAR.
 *
 *   'vollzogen'       kein Konto traegt danach noch einen Schluessel
 *   'unvollstaendig'  mindestens eines tut es
 *
 * Bis heute hing er an `wp_delete_user()`. Jetzt haengt er an
 * `delete_metadata()`, und das ist dieselbe Frage mit einem anderen
 * Gegenstand: ist das durch, was durch sein sollte?
 *
 *     Ein dritter Wert, dessen Anlass wegfaellt, ist eine Verzierung.
 *     Einer, dessen Anlass umzieht, ist derselbe Wert.    (R-ZAEHLER-1)
 *
 * UND DER SATZ, DER DANEBEN GEHOERT: 'konten' => 0 ist keine
 * Unvollstaendigkeit. Es ist die Auskunft, dass dieses Entfernen kein
 * Konto angefasst hat — und das ist seit heute der richtige Wert.
 */

/* ---------------------------------------------------------------------------
 * DIE RUECKGABE STEHT IN DER NARBE — Posten B2, 10.10.2026.
 *
 * Christians Satz („trage ihn wieder ein") braucht einen Leser. Eine
 * Deinstallation hat keine Oberflaeche: WordPress bietet keinen Weg, auf dem
 * eine entfernte Datei noch etwas auf den Schirm bekommt.
 *
 *     Ein Hinweis, der an eine Stelle geschrieben wird, an der niemand
 *     liest, ist kein Hinweis. Er ist eine Entlastung des Schreibers.
 *
 * Deshalb steht der Satz an ZWEI Stellen, und beide sind erreichbar:
 *
 *   VORHER   auf dem Schirm des SysOps, als Vorhersage, solange das Plugin
 *            noch laeuft und noch jemand etwas retten kann
 *            (CV_Admin::render_removal_choice, Posten B7)
 *
 *   NACHHER  hier, in der Narbe, je Anbieter mit seinem Grund. Die Narbe
 *            ist der eine Rest, den diese Datei absichtlich hinterlaesst,
 *            und sie wird nicht mitgeloescht.
 *
 * KEINE ZWEITE OPTION. Diese Datei schreibt GENAU EINE, und das ist die
 * Regel, auf der sie gebaut ist (H222). Ein zweiter Rest fuer eine Meldung
 * waere ein zweiter Rest.
 *
 * BENANNTE GRENZE, und sie ist unangenehm: wird Pacioli entfernt und die
 * umgebende Cluster-One-Installation ist nicht da, liest die Narbe niemand.
 * Die Vorhersage VORHER ist deshalb nicht die Hoeflichkeit, sondern der
 * tragende Teil — und sie laeuft, solange noch jemand da ist.
 *
 *     Man kann einen Verlust nicht verhindern, der in der Hand eines
 *     anderen liegt. Man kann ihn nur vorher ankuendigen, solange noch
 *     Zeit ist, etwas zu retten.                                  (Mammut)
 * ------------------------------------------------------------------------- */
$pacioli_narbe['rueckgabe'] = $pacioli_rueckgabe;

/*
 * „behalten" ist kein unvollstaendiger Lauf. Es ist der andere Ausgang.
 * Ohne diese Unterscheidung sagte die Narbe „vollzogen · 0 Konten" — und
 * das hiesse zugleich „es gab keine" und „ich habe keine angefasst".
 */

update_option( PACIOLI_NARBE, $pacioli_narbe, false );
