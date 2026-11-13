<?php
/**
 * Die Marke — das Herzstück seit 0.3.0.
 *
 * In der Options-Zeile eines Anbieters steht kein Schlüssel, sondern:
 *
 *     CONNECTOR-VAULT--BITTE-NICHT-AENDERN--openai--a1b2c3d4
 *
 * Sie ist wertlos, ASCII-rein (sie muss durch einen HTTP-Header passen),
 * für einen Menschen als Platzhalter erkennbar, und sie trägt die
 * Anbieter-ID in sich. Letzteres ist der eigentliche Trick: Wenn der Vault
 * sie später im ausgehenden Request wiederfindet, weiss er allein aus dem
 * gefundenen Wert, welcher Anbieter gemeint ist — ohne Host-Verzeichnis,
 * ohne Header-Verzeichnis, ohne irgendetwas, das altern kann.
 *
 * DREI STELLEN, MEHR NICHT:
 *
 *   1. `pre_update_option_…`  — verhindert, dass je ein echter Schlüssel in
 *      die Options-Zeile geschrieben wird. Was hereinkommt und keine Marke
 *      ist, wird verschlüsselt in die Quarantäne genommen; gespeichert wird
 *      die Marke.
 *   2. `http_request_args`    — tauscht die gefundene Marke gegen den
 *      Schlüssel der Identität, die in diesem Moment handelt.
 *   3. `pre_http_request`     — Riegel: Ist danach noch irgendwo eine Marke
 *      übrig (Header, Rumpf oder URL), geht der Request nicht hinaus.
 *
 * Zu 3: Gemessen am 24.08.2026 tragen alle vier Anbieter den Schlüssel im
 * Header, keiner im Query-String — `http_request_args` genügt also heute.
 * Der Riegel bleibt, weil er nichts kostet und den Tag abfängt, an dem ein
 * Anbieter das ändert. Dann scheitert es laut statt falsch abzurechnen.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 *  DER RIEGEL UM DIE KLASSE — 12.09.2026, Weg C, dritte Schicht
 *
 *  Ohne ihn stirbt ein Knoten mit ZWEI Produktordnern an
 *  `Cannot redeclare class CV_Marker`, sobald der Lader seine EIGENEN Dateien
 *  laedt (zweite Schicht). Bis heute war dieser Tod VERDECKT: der Lader
 *  kam gar nicht bis hierher.
 *
 *      Ein Fehler, der von einem anderen Fehler verdeckt wird, ist beim
 *      Reparieren des ersten kein Fortschritt, sondern eine Ueberraschung.
 *
 *  Die Einrueckung ist nicht die Form, sondern die Sache: PHP bindet eine
 *  UNBEDINGTE Deklaration beim Uebersetzen, also bevor der Riegel je
 *  gefragt wird. Nur was IM Block steht, wird spaeter gebunden. Gemessen
 *  am 11.09. an den Funktionen, heute an den Klassen.
 * ------------------------------------------------------------------------- */
if ( ! class_exists( 'Pacioli_Marker' ) ) {
	class Pacioli_Marker {

		/** Woran eine Marke erkennbar ist. Bewusst lesbar und ohne Umlaute. */
		const PREFIX = 'CONNECTOR-VAULT--BITTE-NICHT-AENDERN--';

		/** Nicht geheimer Zufallswert, damit Marken je Installation verschieden sind. */
		const OPTION_SALT = 'pacioli_marker_salt';

		/** Merkliste der Anbieter, deren Options-Zeilen wir kennen. */
		const OPTION_KNOWN = 'pacioli_known_providers';

		/** Gezählte Leck-Fälle — Marken, die bis zum Riegel durchgekommen sind. */
		const OPTION_LEAKS = 'pacioli_marker_leaks';

		/** Betriebszahl: aufgehaltene Aufrufe ohne Bindung. Kein Alarm. */
		const OPTION_UNBOUND = 'pacioli_marker_unbound';

		/** Aufrufe gebundener Identitäten ohne hinterlegten Schlüssel. */
		const OPTION_NO_SECRET = 'pacioli_marker_no_secret';

		/**
		 * Wie viele ausgehende Aufrufe der Riegel im Namen einer KI gesehen hat.
		 *
		 * =====================================================================
		 *  H192 — der Nenner, den der Leck-Zähler nie hatte
		 * =====================================================================
		 *
		 *  Der Leck-Zähler beantwortet die Frage „greift der Override im
		 *  Betrieb?" mit einer Null. Das tut er auch dann, wenn auf diesem
		 *  Knoten noch nie ein KI-Aufruf hinausgegangen ist.
		 *
		 *      Eine Null, die „nie passiert" und „nie versucht" nicht trennt,
		 *      ist keine Messung. Sie ist eine Abwesenheit von Lärm mit einer
		 *      Zahl davor.
		 *
		 *  Diese Zahl ist der Nenner. Steht sie auf 0, sagt der Leck-Zähler
		 *  nichts, und die Seite schreibt das hin statt einer Null.
		 *
		 *  WARUM NUR DIE GEBUNDENEN, und nicht alle ausgehenden Requests:
		 *
		 *    - Ein `update_option()` bei JEDEM HTTP-Aufruf einer Installation
		 *      ist ein Preis, den dieses Plugin nicht verlangen darf. Gebundene
		 *      Aufrufe sind selten — es sind genau die KI-Aufrufe.
		 *    - Und der Nenner, der den Leck-Zähler zu einer Aussage macht, ist
		 *      ohnehin der gebundene: ein Aktualisierungs-Ping von WordPress
		 *      sagt über den Schlüsseltausch nichts.
		 *
		 *  BENANNTE GRENZE: Hält ein anderes Plugin den Request vorher an
		 *  (`$preempt`), sieht der Riegel ihn nicht und zählt ihn nicht. Die
		 *  Zahl ist „was der Riegel gesehen hat", nicht „was hinausgegangen
		 *  ist" — und heisst deshalb auch so.
		 */
		const OPTION_BOUND_CALLS = 'pacioli_marker_bound_calls';

		public static function init() {
			// Marken früh sicherstellen — vor `init`, wo die Anbieter lesen.
			add_action( 'plugins_loaded', array( __CLASS__, 'fremde_schluessel_sichern' ), 6 );
			add_action( 'init', array( __CLASS__, 'fremde_schluessel_sichern' ), 1 );

			/* =============================================================
			 *  DER FILTER STATT DES SCHREIBENS — 11.10.2026
			 * =============================================================
			 *
			 *  Turings Durchgang auf Christians Knoten hat gezeigt: bei
			 *  JEDEM Seitenaufruf, bei dem eine KI etwas tut, reisen die
			 *  echten Schluessel des SysOps fuer ALLE eingerichteten
			 *  Anbieter nach draussen — einmal je Anbieter, als
			 *  Modell-Erkundung, BEVOR Pacioli den Schluessel des Menschen
			 *  setzt.
			 *
			 *      Genau das Ding, das Pacioli verhindern soll, passiert
			 *      einen Augenblick vor Pacioli.                 (Mammut)
			 *
			 *  WARUM ES BISHER NICHT GEGRIFFEN HAT, gemessen und nicht
			 *  geraten: bis zum 11.10. hing ein
			 *  `pre_option_<name>`-Filter je Anbieter — aber nur fuer
			 *  die Anbieter aus `providers()` (in `guard_option()`, am
			 *  12.10. gefallen). Und `providers()` kennt die
			 *  LEBENDEN Anbieter erst, wenn `did_action('init')` wahr ist
			 *  UND die Anbieter-Plugins sich schon angemeldet haben. Diese
			 *  Datei laeuft bei `plugins_loaded` 6 und `init` 1; Turing hat
			 *  gemessen, dass die Anbieter bei `init` 20 lesen.
			 *
			 *      Ein Anbieter wird erst bekannt, wenn ihn jemand nach
			 *      `init` gesehen hat. Bis dahin steht in seiner Zeile,
			 *      was der SysOp hineingeschrieben hat.
			 *
			 *  Darum sind auf Christians Knoten drei von vier Zeilen
			 *  ungemarkt: google, openai und xai waren nie bekannt, als
			 *  `ensure_markers()` lief.
			 *
			 *  MAMMUTS AUFTRAG SAGT `pre_option_connectors_ai_<anbieter>`.
			 *  Das ist die Form, die diesen Fehler NICHT behebt: sie
			 *  braucht die Anbieterliste, und die ist das Problem. Also
			 *  der GENERISCHE Filter, der am NAMEN erkennt und keine Liste
			 *  braucht.
			 *
			 *      Wer eine Liste braucht, um einen Fehler zu beheben, der
			 *      an der Liste liegt, behebt ihn fuer die Eintraege, die
			 *      schon drinstehen.
			 *
			 *  DASS ES IHN GIBT, ist aus dem eigenen Kopf gemessen und
			 *  nicht von WordPress geliehen: `pre_option` gibt es seit
			 *  WordPress 6.1, und dieses Plugin verlangt im Kopffeld
			 *  „Requires at least: 7.0".
			 *
			 *      Eine geliehene Zusage, die die eigene Mindestfassung
			 *      ohnehin verlangt, ist keine Leihe mehr.
			 * ============================================================= */
			add_filter( 'pre_option', array( __CLASS__, 'zeile_lesen' ), 10, 2 );

			/* ---------------------------------------------------------------
			 *  Q3, 12.10.2026 — DIE SCHREIBSEITE HOLT DIE LESESEITE EIN
			 *
			 *  Bis heute hing der Schreib-Abfang je ZEILE und nur fuer die
			 *  Anbieter aus `providers()` (`guard_option()`, gefallen).
			 *  `zeile_lesen()` beantwortet seit gestern JEDE Anbieter-Zeile,
			 *  bekannt oder nicht. Die zwei Haelften einer Regel standen
			 *  damit auf verschiedenem Wissen.
			 *
			 *      Zwei Haelften einer Regel, von denen die eine die Liste
			 *      nicht mehr braucht und die andere noch, sind eine Regel
			 *      mit einem Loch an einer genau benennbaren Stelle.
			 *
			 *  Mammuts Satz dazu, und er ist der Grund, warum Q3 nicht
			 *  vorher gebaut wurde:
			 *
			 *      Zwei Reparaturen an einer Ursache sind zwei
			 *      Gelegenheiten, sie zu verfehlen.
			 *
			 *  Jetzt dasselbe Muster an derselben Sorte Haken: ein Leser,
			 *  ein Schreiber, und beide kennen keine Liste.
			 * ------------------------------------------------------------- */
			add_filter( 'pre_update_option', array( __CLASS__, 'zeile_schreiben' ), 10, 2 );

			add_filter( 'http_request_args', array( __CLASS__, 'swap' ), 9999, 2 );
			add_filter( 'pre_http_request', array( __CLASS__, 'bolt' ), 9999, 3 );
		}

		// =====================================================================
		//  Die Marke selbst
		// =====================================================================

		/**
		 * Die Marke eines Anbieters.
		 */
		/**
		 * @param string $provider
		 * @param bool   $salz_anlegen Fehlt das Salz, darf es angelegt werden?
		 *                             Aus einem LESE-Filter heraus nicht.
		 */
		public static function for_provider( $provider, $salz_anlegen = true ) {
			$provider = sanitize_key( $provider );

			$salt = get_option( self::OPTION_SALT );

			if ( ! $salt ) {
				/* ---------------------------------------------------------
				 * EIN LESEN DARF NICHT SCHREIBEN — 11.10.2026.
				 *
				 * `zeile_lesen()` haengt an `pre_option` und wird bei JEDEM
				 * Optionslesen gefragt. Legte es dabei das Salz an, wuerde
				 * ein Lesevorgang eine Option schreiben.
				 *
				 *     Ein Lesevorgang, der schreibt, ist ein Schreibweg mit
				 *     einem harmlosen Namen.
				 *
				 * Ohne Salz gibt es keine Marke, und dann wird keine
				 * erfunden: der Filter gibt zurueck, was er bekommen hat.
				 * ------------------------------------------------------- */
				if ( ! $salz_anlegen ) {
					return '';
				}

				$salt = wp_generate_password( 32, false, false );
				update_option( self::OPTION_SALT, $salt, true );
			}

			$sig = substr( hash_hmac( 'sha256', $provider, $salt ), 0, 8 );

			return self::PREFIX . $provider . '--' . $sig;
		}

		/**
		 * Ist dieser Wert eine Marke — und wenn ja, von welchem Anbieter?
		 *
		 * @return string Anbieter-ID oder '' .
		 */
		public static function provider_of( $value ) {
			if ( ! is_string( $value ) || false === strpos( $value, self::PREFIX ) ) {
				return '';
			}

			if ( ! preg_match( '/' . preg_quote( self::PREFIX, '/' ) . '([a-z0-9_\-]+)--([a-f0-9]{8})/', $value, $m ) ) {
				return '';
			}

			// Gegenprobe: Die Signatur muss zu diesem Knoten passen. Sonst ist es
			// die Marke einer anderen Installation und geht uns nichts an.
			// 18.10.2026 (Neo): OHNE Salz anlegen — ein Lesen darf nicht
			// schreiben (derselbe Grund wie in zeile_lesen). Gibt es kein Salz,
			// kann der Wert keine Marke dieses Knotens sein; '' ist dann richtig.
			return ( self::for_provider( $m[1], false ) === self::PREFIX . $m[1] . '--' . $m[2] ) ? $m[1] : '';
		}

		/**
		 * Steht in dem Wert irgendwo eine Marke dieses Knotens?
		 */
		public static function contains_marker( $value ) {
			return is_string( $value ) && '' !== self::provider_of( $value );
		}

		// =====================================================================
		//  1 · Die Options-Zeilen
		// =====================================================================

		/**
		 * Sorgt dafür, dass in jeder Anbieter-Zeile die Marke steht.
		 *
		 * Idempotent: mehrfaches Aufrufen ist folgenlos.
		 *
		 * ---------------------------------------------------------------------
		 *  0.3.1 — der Riegel gegen Datenverlust verlor Daten.
		 *
		 *  Bis 0.3.0 wurde `quarantine()` aufgerufen, die Rückgabe aber nicht
		 *  ausgewertet, und danach die Marke geschrieben. Scheiterte die
		 *  Verwahrung — kein Verschlüsselungsschlüssel, kein Besitzer, ein
		 *  Fehler beim Verschlüsseln —, war der vorgefundene Schlüssel danach
		 *  nirgends mehr. Also genau der Schaden, gegen den der ganze
		 *  Mechanismus gebaut wurde, in der einen Lage, in der das
		 *  Sicherungsnetz fehlte.
		 *
		 *  Der Kommentar sagte „Sicherstellen statt überschreiben" — und der
		 *  Code prüfte nicht nach, ob das Sicherstellen gelungen war. Wieder
		 *  eine Regel im Kommentar statt eines Riegels im Werkzeug.
		 *
		 *  Zwei Änderungen: Ohne Tresor wird gar nicht erst angefangen. Und
		 *  ersetzt wird nur, was nachweislich verwahrt ist.
		 * ---------------------------------------------------------------------
		 */
		/**
		 * Was eine Anbieter-Zeile SAGT, wenn jemand sie liest.
		 *
		 * =====================================================================
		 *  DER FILTER STATT DES SCHREIBENS — 11.10.2026
		 * =====================================================================
		 *
		 *  Haengt an `pre_option` und antwortet, WANN AUCH IMMER gelesen wird.
		 *
		 *      Wer eine Option fuellt, muss vor dem Leser da sein. Wer sie
		 *      filtert, muss nur da sein, wenn gelesen wird — und das ist
		 *      dieselbe Zeile ohne die Wettlauffrage.             (Mammut)
		 *
		 *  ES GIBT IMMER DIE MARKE ZURUECK, auch wenn in der Datenbank ein
		 *  echter Schluessel steht. Das ist der ganze Fund von Turings
		 *  Durchgang: dort, wo `ensure_markers()` nie hingekommen ist, lag
		 *  der echte Schluessel des SysOps — und die Modell-Erkundung hat ihn
		 *  mitgenommen.
		 *
		 *      Ein Schluessel, den das Haus versteckt, verlaesst den Knoten
		 *      nicht. Einer, den es nur ERSETZEN wollte, verlaesst ihn so
		 *      lange, wie das Ersetzen nicht geschehen ist.
		 *
		 *  UND DER REST DES WERKS BLEIBT, WIE ER IST: die Marke reist
		 *  hinaus, `swap()` tauscht sie am ausgehenden Request gegen den
		 *  Schluessel des Handelnden, `bolt()` haelt jede uebrig gebliebene
		 *  auf und zaehlt sie. Dieser Filter aendert nur, WOHER die Marke
		 *  kommt — aus einer Rechnung statt aus einer Zeile.
		 *
		 *  WAS ER NICHT TUT:
		 *
		 *    - Er schreibt nichts. Ohne Salz gibt er zurueck, was er bekam
		 *      (`for_provider( …, false )`) — ein Lesen, das schreibt, waere
		 *      ein Schreibweg mit einem harmlosen Namen.
		 *    - Er antwortet nicht, wenn schon jemand geantwortet hat. Ein
		 *      Filter, der einen Vorgaenger ueberschreibt, ist kein Filter.
		 *    - Er faelscht nichts: `raw_option()` liest weiter an allen
		 *      Filtern vorbei, und genau damit arbeiten
		 *      `fremde_schluessel_sichern()` und der Raeumer beim Entfernen.
		 *
		 *  BENANNTE GRENZE: er haengt an `get_option()`. Wer die
		 *  Anbieter-Zeile mit eigenem SQL liest, sieht, was in der Datenbank
		 *  steht. Turing hat 906 Optionen BELAUSCHT und die Lesezugriffe
		 *  gesehen — also laufen sie ueber `get_option()`. Das ist gemessen,
		 *  nicht angenommen.
		 *
		 * @param mixed  $pre    Was ein frueherer Filter schon gesagt hat.
		 * @param string $option Der Name der Option.
		 * @return mixed
		 */
		public static function zeile_lesen( $pre, $option = '' ) {
			if ( false !== $pre ) {
				return $pre;
			}

			if ( ! is_string( $option ) ) {
				return $pre;
			}

			if ( ! preg_match( '/^connectors_ai_([a-z0-9_\-]+)_api_key$/', $option, $m ) ) {
				return $pre;
			}

			/* -----------------------------------------------------------------
			 * DER ZWISCHENSPEICHER BEHAELT NUR EINE ANTWORT, KEIN NICHTS.
			 *
			 * Er stand hier zuerst so:
			 *
			 *     if ( ! isset( $marken[ $m[1] ] ) ) {
			 *         $marken[ $m[1] ] = self::for_provider( $m[1], false );
			 *     }
			 *
			 * `isset()` ist auch auf dem leeren String wahr. Auf einem Knoten
			 * ohne Salz gibt `for_provider( …, false )` absichtlich '' zurueck
			 * (ein Lesen darf nicht schreiben) — und dieses '' blieb liegen.
			 * Entstand das Salz einen Augenblick spaeter IM SELBEN Durchgang,
			 * antwortete der Filter fuer den Rest des Seitenaufrufs weiter
			 * „keine Marke", obwohl es inzwischen eine gab.
			 *
			 *     Ein Zwischenspeicher, der ein Nichts behaelt, erinnert sich
			 *     an einen Zustand und nicht an eine Antwort.
			 *
			 * GEMESSEN, nicht ueberlegt: zeuge-filter.php L8, Welt C. Vor
			 * dieser Zeile war L8 ROT:
			 *
			 *     ROT L8  Salz da: ja · zweiter Griff: „VORGABE" statt der Marke
			 *
			 * Jetzt wird nur ein nicht-leeres Ergebnis behalten. Der Preis ist
			 * ein `for_provider()`-Aufruf je Lesevorgang, solange es kein Salz
			 * gibt — also genau so lange, wie es noch nichts zu beschleunigen
			 * gibt.
			 * --------------------------------------------------------------- */
			static $marken = array();

			if ( empty( $marken[ $m[1] ] ) ) {
				$marke = self::for_provider( $m[1], false );

				if ( '' === $marke ) {
					return $pre;
				}

				$marken[ $m[1] ] = $marke;
			}

			return $marken[ $m[1] ];
		}

		/**
		 * Einen vorgefundenen fremden Schluessel sicherstellen — und die Zeile
		 * LEEREN, nicht markieren.
		 *
		 * =====================================================================
		 *  HIESS BIS ZUM 11.10.2026 `ensure_markers()`
		 * =====================================================================
		 *
		 *  Sie hat zwei Dinge getan: einen vorgefundenen echten Schluessel in
		 *  den Tresor nehmen, und danach die Marke in die Zeile schreiben.
		 *  Das zweite ist weg — `zeile_lesen()` beantwortet das Lesen, also
		 *  braucht die Datenbank den Wert nicht.
		 *
		 *      Nichts in der Datenbank: die Anbieter-Zeile bleibt leer, und
		 *      R1 ist eingehalten.
		 *
		 *  DER NAME IST MITGEGANGEN. „ensure_markers" hiesse jetzt etwas, was
		 *  sie nicht mehr tut.
		 *
		 *      Ein Name, der eine abgeschaffte Handlung nennt, ist der
		 *      naechste falsche Wegweiser.
		 *
		 *  UND EINE ALTE MARKE WIRD AUFGERAEUMT: wo eine steht — aus einer
		 *  Fassung vor heute —, wird die Zeile geleert. Eine Marke in der
		 *  Datenbank ist ab heute ein Rest und keine Einrichtung.
		 *
		 *  EINE LEERE ODER FEHLENDE ZEILE WIRD NICHT ANGEFASST. Ein
		 *  `update_option()` auf eine Zeile, die es nicht gibt, LEGT SIE AN —
		 *  und dann stuende etwas in der Datenbank, wo nichts stehen soll.
		 *
		 *      Wer eine Zeile leert, die es nicht gibt, hat sie angelegt.
		 */
		public static function fremde_schluessel_sichern() {
			self::zeilen_raeumen( 0 );
		}

		/*
		 * =====================================================================
		 *  EIN SCHREIBER, EIN WERT — 11.10.2026
		 * =====================================================================
		 *
		 *  Hier standen zwei Schleifen ueber dieselben Zeilen:
		 *  `fremde_schluessel_sichern()` (automatisch, bei plugins_loaded und
		 *  init) und `sweep()` (von der Hand, beim Aufspielen und auf dem
		 *  Pruefstand). Nach dem Umbau schrieben sie VERSCHIEDENE Werte in
		 *  dieselbe Zeile: die eine '' , die andere die Marke.
		 *
		 *      Zwei Schreibwege auf eine Wahrheit, und sie waren sich nicht
		 *      einig. Dann flackert die Zeile im Takt dessen, was zuletzt
		 *      gelaufen ist.                                         (P1-12)
		 *
		 *  Jetzt gibt es EINE Schleife. `sweep()` ist, was es immer war —
		 *  dasselbe von Hand, mit einem genannten Verwahrer und mit der
		 *  Liste dessen, was gefunden wurde.
		 *
		 *  UND DER RUHEZUSTAND HAT SICH GEAENDERT: er ist die leere Zeile.
		 *  `sweep()` prueft deshalb nicht mehr `$stored === $marker`
		 *  („steht schon die Marke da?"), sondern `'' === $stored`.
		 *
		 *      Eine Abbruchbedingung, die den alten Ruhezustand nennt,
		 *      arbeitet nach dem Umbau bei jedem Durchgang weiter.
		 *
		 * @param int $owner_id 0 = der handelnde Mensch, sonst dieses Konto.
		 * @return string[] Anbieter, bei denen etwas sichergestellt wurde.
		 */
		private static function zeilen_raeumen( $owner_id ) {
			// Ohne Verschlüsselungsschlüssel arbeitet der Vault ohnehin nicht.
			// Dann darf er auch keine Anbieter-Zeilen umschreiben.
			if ( ! Pacioli_Crypto::has_key() ) {
				return array();
			}

			$gefunden = array();

			foreach ( self::providers() as $provider ) {
				$option = self::option_name( $provider );

				/* `guard_option()` stand hier und ist am 12.10. gefallen:
				   der Schreib-Abfang haengt jetzt in `init()`, einmal und
				   fuer jede Anbieter-Zeile.                        (Q3) */
				$stored = self::raw_option( $option );

				if ( '' === $stored ) {
					continue;
				}

				if ( ! self::looks_like_marker( $stored ) ) {
					// Da liegt ein echter Schlüssel. Sicherstellen statt
					// überschreiben — es könnte der einzige Ort sein, an dem er
					// existiert. Gelingt das nicht, bleibt er stehen.
					//
					// UND ER REIST NICHT MEHR HINAUS, auch wenn er stehen
					// bleibt: `zeile_lesen()` gibt die Marke zurück, nicht ihn.
					// Gemessen: zeuge-filter.php L3, Welt B.
					$verwahrer = $owner_id ? (int) $owner_id : self::quarantine_owner();

					if ( ! self::quarantine( $provider, $stored, $verwahrer ) ) {
						// Nicht verwahrbar: stehen lassen. CV_Guard meldet es.
						continue;
					}

					$gefunden[] = $provider;
				}

				// Erreicht wird diese Zeile nur, wenn nichts zu verlieren war:
				// eine Marke aus einer älteren Fassung, oder ein erfolgreich
				// verwahrter Schlüssel.
				update_option( $option, '', true );
			}

			return $gefunden;
		}

		/**
		 * Trägt dieser Wert das Marken-Präfix — auch wenn es nicht unseres ist?
		 *
		 * 0.3.1 — Eine Marke mit falscher Signatur (nach einem Wechsel des
		 * Salts oder von einer anderen Installation eingeschleppt) ist kein
		 * Schlüssel. Sie landete vorher als vermeintlicher Schlüssel in der
		 * Quarantäne: kein Schaden, aber Müll in einem Beutel, der übersichtlich
		 * bleiben soll.
		 */
		public static function looks_like_marker( $value ) {
			return is_string( $value ) && 0 === strpos( $value, self::PREFIX );
		}

		/**
		 * Hängt die Riegel an eine einzelne Options-Zeile.
		 */
		/**
		 * Faengt jeden Schreibversuch auf eine Anbieter-Zeile ab.
		 *
		 * =====================================================================
		 *  HIER STAND `guard_option()` — gefallen am 12.10.2026 (Q3)
		 * =====================================================================
		 *
		 *  Es hing `pre_update_option_<option>` je Zeile an, und zwar nur fuer
		 *  die Anbieter, die `providers()` kannte. Auf einem Knoten, dessen
		 *  Liste nur einen Anbieter nannte, war der Abfang fuer EINEN Anbieter
		 *  scharf — und ein echter Schluessel, den jemand fuer einen der
		 *  anderen in ein Feld tippte, landete im Klartext in der Datenbank.
		 *
		 *      Ein Riegel, der eine Liste braucht, haelt genau so weit, wie
		 *      die Liste reicht — und die Liste war die Ursache aus §5(a).
		 *
		 *  SEIT §5(a) BRAUCHT DIESE SEITE DIE LISTE NICHT MEHR: der Name der
		 *  Option sagt alles, was gebraucht wird, und zwar im Namen selbst.
		 *  Damit fallen §5(a) und Q3 in EINE Reparatur, wie Mammut es
		 *  entschieden hat.
		 *
		 *  DREI DINGE SIND MITGEGANGEN:
		 *
		 *    - der `static $done`-Zwischenspeicher. Ein Filter, der einmal
		 *      haengt, braucht keinen Zaehler, der sich erinnert, dass er
		 *      haengt. (Und ich habe gestern gelernt, was ein
		 *      Zwischenspeicher kostet — L8.)
		 *    - die anonyme Funktion samt `use ( $provider )`. Der Anbieter
		 *      kommt jetzt aus dem Optionsnamen, nicht aus einer
		 *      mitgeschleppten Variablen.
		 *    - der Ruf von `guard_option()` in `zeilen_raeumen()`. Ein
		 *      Riegel, der beim Raeumen angelegt wird, haengt erst, wenn
		 *      geraeumt wurde — und auf einem Knoten ohne
		 *      Verschluesselungsschluessel wurde nie geraeumt.
		 *
		 *      Ein Riegel, der im Vorbeigehen eines anderen Vorgangs
		 *      angelegt wird, haelt ab dem Augenblick dieses Vorgangs und
		 *      nicht ab dem Laden.
		 *
		 *  BENANNTE GRENZE, dieselbe wie bei `zeile_lesen()`: der Haken ist
		 *  `pre_update_option`. Wer die Zeile mit eigenem SQL schreibt,
		 *  schreibt sie. Das ist keine Annahme — Turing hat die Zugriffe
		 *  ueber die Options-API gemessen.
		 *
		 * @param mixed  $value  Was gespeichert werden soll.
		 * @param string $option Der Name der Option.
		 * @return mixed
		 */
		public static function zeile_schreiben( $value, $option = '' ) {
			if ( ! is_string( $option ) ) {
				return $value;
			}

			if ( ! preg_match( '/^connectors_ai_([a-z0-9_\-]+)_api_key$/', $option, $m ) ) {
				return $value;
			}

			return self::intercept_write( $value, $m[1] );
		}

		/**
		 * Fängt einen Schreibversuch auf eine Anbieter-Zeile ab.
		 */
		/*
		 * =====================================================================
		 *  GESPEICHERT WIRD '', NICHT DIE MARKE — 11.10.2026
		 * =====================================================================
		 *
		 *  Bis heute gab diese Stelle in jedem Ausgang die MARKE zurueck. Das
		 *  war richtig, solange die Marke in der Zeile STAND. Seit
		 *  `zeile_lesen()` das Lesen beantwortet, soll in der Zeile NICHTS
		 *  stehen — und dann ist ein Schreib-Abfang, der die Marke
		 *  zurueckgibt, der zweite Schreibweg auf dieselbe Wahrheit.
		 *
		 *  ER HAT DEN ERSTEN RUECKGAENGIG GEMACHT, UND ZWAR STILL:
		 *  `fremde_schluessel_sichern()` leert die Zeile mit
		 *  `update_option( $option, '' )`. Diese Schreibung laeuft durch
		 *  `pre_update_option_<option>` — also durch genau diesen Abfang —
		 *  und der machte aus dem leeren Wert wieder die Marke. Die Zeile
		 *  wurde nie leer.
		 *
		 *      Zwei Schreibwege auf eine Wahrheit, und der zweite nimmt die
		 *      Arbeit des ersten zurueck, ohne ein Wort zu sagen.    (P1-12)
		 *
		 *  GEMESSEN, nicht ueberlegt: zeuge-filter.php L4, Welt B. Vorher:
		 *
		 *      ROT L4  in der Datenbank steht „CONNECTOR-VAULT--…" — der
		 *              eigene Schreib-Abfang hat das Leeren zurueckgenommen
		 *
		 *  Kein anderer Zeuge dieses Hauses konnte das sehen: sie alle haben
		 *  `function apply_filters( $h, $v ) { return $v; }`, und in so einer
		 *  Welt gibt es diesen Abfang nicht.
		 *
		 *      Eine Attrappe, die den Gegenstand wegnimmt, ist ein
		 *      bestandener Test ohne Pruefung.
		 *
		 *  UND `for_provider()` IST HIER HERAUSGEFALLEN. Es wurde nur fuer den
		 *  Rueckgabewert gebraucht — und es LEGT DAS SALZ AN, wenn es keines
		 *  gibt. Ein fremder Schreibversuch hat damit als Nebenwirkung eine
		 *  Option dieses Hauses erzeugt.
		 * =====================================================================
		 */
		public static function intercept_write( $value, $provider ) {
			if ( ! is_string( $value ) || '' === trim( $value ) ) {
				return '';
			}

			// Eine eigene, fremde oder veraltete Marke: nichts zu verwahren,
			// und nichts aufzubewahren.
			if ( self::looks_like_marker( $value ) ) {
				return '';
			}

			// Jemand hat einen echten Schlüssel in das Feld getippt — meist der
			// Administrator auf der Einstellungsseite eines Anbieter-Plugins.
			// Er wird verschlüsselt sichergestellt und aus der Datenbank
			// herausgehalten; in die Zeile kommt NICHTS. Gelesen wird sie von
			// `zeile_lesen()` beantwortet.
			if ( ! self::quarantine( $provider, $value, self::quarantine_owner() ) ) {
				// 0.3.1 — Nicht verwahrbar, also auch nicht wegnehmen.
				//
				// Dass dann für einen Moment ein Klartext-Schlüssel in der
				// Datenbank steht, widerspricht R1. Aber das ist ein Zustand,
				// den CV_Guard sieht und meldet. Ein verlorener Schlüssel meldet
				// sich nie. Von drei Ausgängen — verwahren, ablehnen,
				// verschlucken — ist der dritte der schlechteste, und genau den
				// hatte 0.3.0.
				return $value;
			}

			return '';
		}

		/**
		 * Räumt alle Anbieter-Zeilen ab und nimmt gefundene Schlüssel in Verwahrung.
		 *
		 * @return string[] Anbieter, bei denen etwas sichergestellt wurde.
		 */
		public static function sweep( $owner_id = 0 ) {
			return self::zeilen_raeumen( $owner_id );
		}

		// =====================================================================
		//  Quarantäne
		// =====================================================================

		/**
		 * Nimmt einen vorgefundenen Schlüssel verschlüsselt in Verwahrung.
		 *
		 * Bewusst KEINE automatische Zuordnung zu einer KI: Wem dieser Schlüssel
		 * gehören soll, ist nicht erratbar. Er liegt sichtbar unter „Meine KIs“
		 * und wartet darauf, zugeordnet oder verworfen zu werden.
		 */
		public static function quarantine( $provider, $plain, $owner_id ) {
			$owner_id = (int) $owner_id;

			if ( ! $owner_id || ! Pacioli_Crypto::has_key() ) {
				return false;
			}

			$cipher = Pacioli_Crypto::encrypt( (string) $plain );

			if ( is_wp_error( $cipher ) ) {
				return false;
			}

			$bag = Pacioli_Vault::read_shielded( $owner_id, PACIOLI_META_QUARANTINE );
			$bag = is_array( $bag ) ? $bag : array();

			$bag[ sanitize_key( $provider ) ] = $cipher;

			update_user_meta( $owner_id, PACIOLI_META_QUARANTINE, $bag );

			/**
			 * Ein vorgefundener Schlüssel wurde sichergestellt.
			 */
			do_action( 'pacioli_key_quarantined', $provider, $owner_id );

			return true;
		}

		/**
		 * Wer bekommt einen sichergestellten Schlüssel in Verwahrung?
		 *
		 * Der handelnde Mensch, falls es einen gibt — sonst der erste
		 * Administrator, damit nichts verlorengeht.
		 */
		private static function quarantine_owner() {
			$me = get_current_user_id();

			if ( $me && user_can( $me, 'manage_options' ) ) {
				$owner = $me;
			} else {
				$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
				$owner  = $admins ? (int) $admins[0] : 0;
			}

			/**
			 * Wer bekommt einen sichergestellten Schlüssel in Verwahrung?
			 *
			 * `0` bedeutet: niemand — dann wird nichts verwahrt und der Schlüssel
			 * bleibt stehen, statt verlorenzugehen. Der Selbsttest benutzt das,
			 * um genau diesen Fall zu prüfen.
			 *
			 * @param int $owner
			 */
			return (int) apply_filters( 'pacioli_quarantine_owner', $owner );
		}

		// =====================================================================
		//  2 · Der Tausch
		// =====================================================================

		/**
		 * Ersetzt die Marke im ausgehenden Request durch den echten Schlüssel.
		 *
		 * @param array  $args
		 * @param string $url
		 */
		public static function swap( $args, $url = '' ) {
			if ( ! is_array( $args ) ) {
				return $args;
			}

			$identity = Pacioli_Vault::acting_identity();

			if ( ! $identity ) {
				// Ohne Bindung wird nichts getauscht. Die Marke reist hinaus und
				// der Anbieter lehnt ab — laut, aber ohne fremde Rechnung.
				return $args;
			}

			$args = self::walk( $args, 'headers', $identity );
			$args = self::walk( $args, 'body', $identity );

			return $args;
		}

		/**
		 * Sucht in einem Teil der Argumente nach Marken und ersetzt sie.
		 */
		private static function walk( $args, $part, $identity ) {
			if ( ! isset( $args[ $part ] ) ) {
				return $args;
			}

			if ( is_string( $args[ $part ] ) ) {
				$args[ $part ] = self::replace( $args[ $part ], $identity );

				return $args;
			}

			if ( is_array( $args[ $part ] ) ) {
				foreach ( $args[ $part ] as $k => $v ) {
					if ( is_string( $v ) ) {
						$args[ $part ][ $k ] = self::replace( $v, $identity );
					}
				}
			}

			return $args;
		}

		/**
		 * Ersetzt eine Marke in einem einzelnen Wert.
		 */
		private static function replace( $value, $identity ) {
			$provider = self::provider_of( $value );

			if ( '' === $provider ) {
				return $value;
			}

			$secret = Pacioli_Vault::secret_for( $identity, $provider );

			if ( '' === $secret ) {
				// Kein passender Schlüssel: Die Marke bleibt stehen und der
				// Riegel unten hält den Request auf.
				return $value;
			}

			return str_replace( self::for_provider( $provider ), $secret, $value );
		}

		// =====================================================================
		//  3 · Der Riegel
		// =====================================================================

		/**
		 * Hält einen Request auf, in dem noch eine Marke steckt.
		 *
		 * Greift nur bei Requests, die tatsächlich eine Marke dieses Knotens
		 * tragen — andere HTTP-Aufrufe sind davon unberührt.
		 *
		 * ---------------------------------------------------------------------
		 *  0.4.1 — zwei Fehlerbilder, zwei Meldungen.
		 *
		 *  Bis 0.4.0 hat dieser Riegel bei JEDER übriggebliebenen Marke Alarm
		 *  geschlagen. Das war im Normalbetrieb dreimal je Aufruf:
		 *
		 *    1. Bei `init` 20 bindet Core für ALLE Anbieter deren Marke.
		 *    2. Ein Aufruf ist an EINE Identität gebunden, also wird auch nur
		 *       deren Anbieter überschrieben.
		 *    3. Der Client erkundet aber die Modelle aller eingerichteten
		 *       Anbieter — im Messbericht vier GETs im ersten Aufruf.
		 *    4. Drei davon tragen die Marken der anderen Anbieter, für die es
		 *       keinen Schlüssel dieser Identität gibt und geben soll.
		 *
		 *  Ein Stolperdraht, über den man bei jedem Gang stolpert, ist keiner
		 *  mehr — und der Schaden ist nicht der Lärm, sondern was er zudeckt:
		 *  Ein echtes Leck sähe genauso aus wie die drei planmässigen.
		 *
		 *  Deshalb wird jetzt unterschieden. Aufgehalten werden beide, aber nur
		 *  eines heisst Leck:
		 *
		 *    - Marke eines FREMDEN Anbieters, während gebunden gehandelt wird:
		 *      erwartbar. Anhalten, kein Alarm, eigener Fehlercode.
		 *    - Marke des EIGENEN Anbieters, oder überhaupt keine Bindung:
		 *      Der Override hat nicht gegriffen. Das ist der Fall, für den
		 *      dieser Riegel gebaut wurde — er wird gezählt und gemeldet.
		 * ---------------------------------------------------------------------
		 *
		 * @param false|array|WP_Error $preempt
		 * @param array                $args
		 * @param string               $url
		 */
		public static function bolt( $preempt, $args = array(), $url = '' ) {
			if ( false !== $preempt ) {
				return $preempt;
			}

			// H192 — VOR der Marken-Suche, und das ist der ganze Punkt.
			//
			// Ein GELUNGENER Tausch hinterlässt keine Marke. Er verlässt diese
			// Methode also gleich unten über `'' === $leftover` und wurde bis
			// heute nirgends gezählt — der Leck-Zähler kannte nur seine
			// Fehlschläge und nannte ihre Abwesenheit einen Erfolg.
			if ( Pacioli_Vault::acting_identity() ) {
				self::note_bound_call();
			}

			$leftover = self::find_leftover( $args, $url );

			if ( '' === $leftover ) {
				return $preempt;
			}

			// 0.4.2 — Es gibt DREI Zustände, nicht zwei.
			//
			// Ungebunden ist kein Leck, sondern eine andere Aussage: Dieser
			// Aufruf war niemandem zurechenbar. Er gehört aufgehalten, aber
			// nicht ins Leck-Buch.
			//
			// Warum das zählt: Die Verbindungsprüfung der Connectors-Seite läuft
			// ohne Bindung — ein Zustand, den dieses Plugin selbst als normal
			// erklärt. Landete sie im Zähler, hiesse eine Null dort nicht mehr
			// „der Override greift", sondern „seit dem Zurücksetzen hat niemand
			// die Einstellungsseite geöffnet".
			if ( ! Pacioli_Vault::acting_identity() ) {
				self::note_unbound();

				return new WP_Error(
					'pacioli_marker_unbound',
					sprintf(
						/* translators: %s: Anbieter-ID. */
						__( 'Connector Vault stopped a call to "%s": nobody is acting right now, so there is no key to insert. That is expected — for instance during a connection check on the settings page.', 'cluster-one-pacioli' ),
						$leftover
					),
					array( 'provider' => $leftover, 'url' => $url )
				);
			}

			// W54, der FUENFTE Zustand — 10.09.2026.
			//
			// Der Besitzer dieser KI gibt es nicht mehr. `CV_Vault::secret_for()`
			// gibt ihren Schluessel deshalb nicht mehr heraus, also reist die
			// Marke hinaus und landet hier.
			//
			// OHNE DIESE ZEILE FIEL DER FALL IN DEN LECK-ZWEIG. Gemessen, nicht
			// vermutet: `has_secret()` ist WAHR (der Schluessel liegt weiter im
			// Meta), der Anbieter stimmt, also lief er bis `note_leak()` durch.
			//
			//     Der Riegel haette gezaehlt, der Override habe versagt — waehrend
			//     der Override gar nicht erst versucht wurde. Ein Zaehler, der
			//     eine Verweigerung als Versagen bucht, macht aus einer
			//     Entscheidung einen Defekt.
			//
			// Das war MEIN Fehler und nicht der des Riegels: die Verweigerung in
			// secret_for() ist am 09.09. dazugekommen, der Riegel kannte sie nicht.
			//
			// VOR dem Anbieter-Vergleich, und das ist Absicht: ein verschwundener
			// Besitzer ist eine Eigenschaft der IDENTITAET, nicht des Anbieters.
			// „erwartbar und kein Fehler" waere hier die falsche Auskunft.
			//
			// GEZAEHLT WIRD HIER NICHT. `secret_for()` hat den Fall schon
			// gezaehlt; zweimal zaehlen hiesse, eine Verweigerung fuer zwei zu
			// halten. Der Riegel SPRICHT, der Tresor ZAEHLT — zwei Leser einer
			// Wahrheit, ein Schreibweg.
			//
			// NUR `true`. Bei `null` — es steht ueberhaupt kein Besitzer da —
			// ist dieser Satz falsch: ein alter Datensatz ist kein Verlust.
			if ( true === Pacioli_Identities::owner_gone( Pacioli_Vault::acting_identity() ) ) {
				return new WP_Error(
					'pacioli_owner_gone',
					sprintf(
						/* translators: %s: Anbieter-ID. */
						__( 'Connector Vault stopped the call: the person who owned this AI no longer exists, so its key for "%s" is not handed out any more. Nothing has been deleted — an administrator can clear these AIs under "My AIs".', 'cluster-one-pacioli' ),
						$leftover
					),
					array( 'provider' => $leftover, 'identity' => Pacioli_Vault::acting_identity(), 'url' => $url )
				);
			}

			$acting_provider = Pacioli_Vault::acting_provider();

			// 0.5.0 — der vierte Zustand.
			//
			// Gebunden, eigener Anbieter, aber kein Schlüssel hinterlegt: Das
			// ist KEIN Leck. Der Tausch hat nicht versagt, es gab nichts zu
			// übertragen. Und es ist auch keine Störung des Knotens, sondern
			// eine offene Rechnung EINER PERSON — die Meldung gehört zu ihr,
			// nicht zum Betreiber.
			//
			// Ohne diese Unterscheidung fiele der häufigste Fall eines frischen
			// Knotens in den Alarmzweig, und der Leck-Zähler hiesse wieder etwas
			// anderes als das, was er behauptet. Das wäre derselbe Fehler zum
			// dritten Mal, an der Stelle, an der er zweimal repariert wurde.
			if ( $leftover === $acting_provider && ! Pacioli_Vault::has_secret( Pacioli_Vault::acting_identity() ) ) {
				self::note_no_secret( Pacioli_Vault::acting_identity(), $leftover );

				return new WP_Error(
					'pacioli_marker_no_secret',
					sprintf(
						/* translators: %s: Anbieter-ID. */
						__( 'Connector Vault stopped the call: no access key is stored on this AI for "%s". It can be entered under "My AIs".', 'cluster-one-pacioli' ),
						$leftover
					),
					array( 'provider' => $leftover, 'identity' => Pacioli_Vault::acting_identity(), 'url' => $url )
				);
			}

			if ( $leftover !== $acting_provider ) {
				return new WP_Error(
					'pacioli_marker_foreign_provider',
					sprintf(
						/* translators: 1: fremder Anbieter, 2: Anbieter der handelnden Identität. */
						__( 'Connector Vault stopped a call to "%1$s". The acting provider is "%2$s"; this identity has no key for other providers. That is expected and not a fault.', 'cluster-one-pacioli' ),
						$leftover,
						$acting_provider
					),
					array( 'provider' => $leftover, 'acting' => $acting_provider, 'url' => $url )
				);
			}

			// Gebunden, und die Marke des eigenen Anbieters ist durchgekommen:
			// Der Override hat versagt. Nur das ist ein Leck.
			self::note_leak( $leftover );

			return new WP_Error(
				'pacioli_marker_leaked',
				sprintf(
					/* translators: %s: Anbieter-ID. */
					__( 'Connector Vault stopped the call: no key of the acting identity could be inserted for "%s". The request was not sent.', 'cluster-one-pacioli' ),
					$leftover
				),
				array( 'provider' => $leftover, 'url' => $url )
			);
		}

		/**
		 * Zählt einen echten Leck-Fall.
		 *
		 * Der Zähler ist die Antwort auf die Frage, mit der 0.4.0 ausgeliefert
		 * wurde: Greift der Override im Betrieb? Steht hier eine Null, ja. Steht
		 * hier eine Zahl, hat sich etwas geändert — und man sieht, wann und bei
		 * welchem Anbieter, statt es an einer Abrechnung zu bemerken.
		 */
		private static function note_leak( $provider ) {
			$log = get_option( self::OPTION_LEAKS, array() );
			$log = is_array( $log ) ? $log : array();

			$log[] = array(
				'provider' => (string) $provider,
				'time'     => time(),
			);

			// Nur die jüngsten behalten — das ist ein Zeuge, kein Archiv.
			if ( count( $log ) > 25 ) {
				$log = array_slice( $log, -25 );
			}

			update_option( self::OPTION_LEAKS, $log, false );

			/**
			 * Eine Marke des eigenen Anbieters ist bis zum Riegel durchgekommen.
			 */
			do_action( 'pacioli_marker_leaked', $provider );
		}

		/**
		 * Die gezählten Leck-Fälle.
		 *
		 * @return array
		 */
		public static function leaks() {
			$log = get_option( self::OPTION_LEAKS, array() );

			return is_array( $log ) ? $log : array();
		}

		/** Setzt den Zähler zurück. */
		public static function forget_leaks() {
			delete_option( self::OPTION_LEAKS );
		}

		/**
		 * Zählt einen ausgehenden Aufruf, den der Riegel im Namen einer KI sah.
		 *
		 * Siehe OPTION_BOUND_CALLS. Kein Alarm, keine Bewertung — eine Zahl,
		 * ohne die die Zahl darüber nichts bedeutet.
		 */
		private static function note_bound_call() {
			$state = get_option( self::OPTION_BOUND_CALLS, array() );
			$state = is_array( $state ) ? $state : array();

			$state['count'] = isset( $state['count'] ) ? (int) $state['count'] + 1 : 1;
			$state['last']  = time();

			update_option( self::OPTION_BOUND_CALLS, $state, false );
		}

		/**
		 * Ausgehende Aufrufe, die der Riegel im Namen einer KI gesehen hat.
		 *
		 * @return array{count:int,last:int}
		 */
		public static function bound_calls() {
			$state = get_option( self::OPTION_BOUND_CALLS, array() );
			$state = is_array( $state ) ? $state : array();

			return array(
				'count' => isset( $state['count'] ) ? (int) $state['count'] : 0,
				'last'  => isset( $state['last'] ) ? (int) $state['last'] : 0,
			);
		}

		/**
		 * Stellt den Nenner auf einen früheren Stand.
		 *
		 * @internal Nur für den Selbsttest — und hier ist es wichtiger als beim
		 *           Leck-Zähler: Die Prüfungen 10 und 11 lösen `pre_http_request`
		 *           GEBUNDEN aus. Ohne dieses Zurücksetzen erzeugte der
		 *           Selbsttest sich seinen eigenen Nenner und meldete
		 *           „Aufrufe gesehen: 4", wo der Knoten nie einen gemacht hat.
		 *
		 *           Ein Zähler, den der Prüfstand selbst füllt, misst den
		 *           Prüfstand.
		 */
		public static function restore_bound_calls( array $state ) {
			if ( ! empty( $state['count'] ) ) {
				update_option( self::OPTION_BOUND_CALLS, $state, false );
			} else {
				delete_option( self::OPTION_BOUND_CALLS );
			}
		}

		/**
		 * Stellt den Zähler auf einen früheren Stand.
		 *
		 * @internal Nur für den Selbsttest: Eine Prüfung darf keine Spur
		 *           hinterlassen, die wie ein echtes Ereignis aussieht.
		 */
		public static function restore_leaks( array $log ) {
			if ( $log ) {
				update_option( self::OPTION_LEAKS, $log, false );
			} else {
				delete_option( self::OPTION_LEAKS );
			}
		}

		/**
		 * =====================================================================
		 *  DIE ZWEI ZAEHLER, DIE VERGESSEN WURDEN — Posten 2, 08.10.2026
		 * =====================================================================
		 *
		 *  Der Satz steht acht Zeilen weiter oben und ist mein eigener:
		 *
		 *      „Ein Zaehler, den der Pruefstand selbst fuellt, misst den
		 *       Pruefstand."
		 *
		 *  Er stand da seit dem Tag, an dem der Selbsttest sich seinen eigenen
		 *  Nenner gebaut hat — und er wurde auf ZWEI von VIER Zaehlern
		 *  angewendet: `bound_calls` und `leaks`. `unbound` und `no_secret`
		 *  hatten nie ein restore_*, und genau die zwei sind gestiegen, als
		 *  Christian je einmal „Pruefungen ausfuehren" gedrueckt hat:
		 *
		 *      DEV    ohne Schluessel  2 -> 4    ohne Identitaet  2 -> 4
		 *      MAIN   ohne Schluessel  3 -> 5    ohne Identitaet 15 -> 17
		 *
		 *      Die Entscheidung war getroffen. Sie war nur zur Haelfte
		 *      gebaut, und die Haelfte sah aus wie die ganze.
		 *
		 * ---------------------------------------------------------------------
		 *  WARUM ZURUECKSTELLEN UND NICHT „NICHT SCHREIBEN"
		 *
		 *  Mammuts erster Weg war, die Pruefung gar nicht ins Buch schreiben
		 *  zu lassen — ihre Konten seien erkennbar ihre. Das waere eine
		 *  AUSNAHME IM ZAEHLER: `note_no_secret()` muesste erkennen, dass
		 *  gerade ein Pruefstand laeuft.
		 *
		 *      Ein Zaehler mit einem eingebauten „ausser diesen" hat einen
		 *      blinden Fleck, und der gilt dann fuer immer und fuer alle —
		 *      auch fuer den, der sein Konto „CV Selftest" nennt.
		 *
		 *  Das Zuruecksetzen laesst den Zaehler vollstaendig und haelt das
		 *  Wissen ueber den Pruefstand IM Pruefstand. Ein Zaehler, der
		 *  zurueckgestellt wurde, hat keine Spur — das ist dieselbe Antwort
		 *  auf Mammuts Einwand, nur ohne den blinden Fleck.
		 *
		 *  UND ES IST EIN MECHANISMUS, NICHT ZWEI. Zwei Wege, einen Zaehler
		 *  vom Pruefstand freizuhalten, waeren zwei Wahrheiten darueber, was
		 *  eine Pruefung im Buch darf.                                (P1-12)
		 * =====================================================================
		 */

		/**
		 * Stellt die Betriebszahl der ungebundenen Aufrufe zurueck.
		 *
		 * @internal Nur fuer den Selbsttest.
		 */
		public static function restore_unbound( array $state ) {
			if ( ! empty( $state['count'] ) ) {
				update_option( self::OPTION_UNBOUND, $state, false );
			} else {
				delete_option( self::OPTION_UNBOUND );
			}
		}

		/**
		 * Stellt die Zaehlung der Aufrufe ohne Schluessel zurueck.
		 *
		 * ANDERE GESTALT als die drei anderen: dieses Buch ist je Identitaet
		 * geschluesselt, nicht ein count. Ein leeres Buch ist die Abwesenheit
		 * der Option und nicht ein Eintrag mit Null — sonst stuende dort nach
		 * einer Pruefung eine Kennung, die es nicht mehr gibt, mit der Zahl 0.
		 *
		 * @internal Nur fuer den Selbsttest.
		 */
		public static function restore_no_secret( array $state ) {
			if ( $state ) {
				update_option( self::OPTION_NO_SECRET, $state, false );
			} else {
				delete_option( self::OPTION_NO_SECRET );
			}
		}

		/**
		 * Zählt einen aufgehaltenen Aufruf ohne Bindung.
		 *
		 * Betriebszahl, kein Alarm. Ein gleichmässiges Ticken ist der
		 * Normalbetrieb — jede Verbindungsprüfung auf einer Einstellungsseite
		 * erzeugt einen. Interessant wäre allein ein plötzlicher Anstieg, und
		 * dafür braucht es eine Zahl, keine Fehlermeldung.
		 */
		private static function note_unbound() {
			$state = get_option( self::OPTION_UNBOUND, array() );
			$state = is_array( $state ) ? $state : array();

			$state['count'] = isset( $state['count'] ) ? (int) $state['count'] + 1 : 1;
			$state['last']  = time();

			update_option( self::OPTION_UNBOUND, $state, false );
		}

		/**
		 * Zählt einen Aufruf einer gebundenen Identität ohne Schlüssel.
		 *
		 * Je Identität, damit die Meldung an die richtige Person gehen kann.
		 * Der Zähler ist nicht der Zweck — die Aktion darunter ist es.
		 */
		private static function note_no_secret( $identity, $provider ) {
			$identity = (int) $identity;

			$state = get_option( self::OPTION_NO_SECRET, array() );
			$state = is_array( $state ) ? $state : array();

			if ( ! isset( $state[ $identity ] ) || ! is_array( $state[ $identity ] ) ) {
				$state[ $identity ] = array( 'count' => 0, 'last' => 0, 'provider' => (string) $provider );
			}

			$state[ $identity ]['count']    = (int) $state[ $identity ]['count'] + 1;
			$state[ $identity ]['last']     = time();
			$state[ $identity ]['provider'] = (string) $provider;

			update_option( self::OPTION_NO_SECRET, $state, false );

			/**
			 * Eine gebundene Identität sollte handeln, hat aber keinen Schlüssel.
			 *
			 * Der Andockpunkt für die Persona-Schicht: Diese Meldung gehört dem
			 * BESITZER, privat — nicht dem Betreiber und nicht in den Raum.
			 *
			 * @param int    $identity
			 * @param int    $owner
			 * @param string $provider
			 */
			do_action(
				'pacioli_no_secret_for_call',
				$identity,
				(int) get_user_meta( $identity, PACIOLI_META_OWNER, true ),
				(string) $provider
			);
		}

		/**
		 * Die Zählung der Aufrufe ohne hinterlegten Schlüssel, je Identität.
		 *
		 * @return array<int,array{count:int,last:int,provider:string}>
		 */
		public static function no_secret_calls() {
			$state = get_option( self::OPTION_NO_SECRET, array() );

			return is_array( $state ) ? $state : array();
		}

		/**
		 * Die Betriebszahl der ungebundenen Aufrufe.
		 *
		 * @return array{count:int,last:int}
		 */
		public static function unbound() {
			$state = get_option( self::OPTION_UNBOUND, array() );
			$state = is_array( $state ) ? $state : array();

			return array(
				'count' => isset( $state['count'] ) ? (int) $state['count'] : 0,
				'last'  => isset( $state['last'] ) ? (int) $state['last'] : 0,
			);
		}

		/**
		 * Findet eine übriggebliebene Marke in Argumenten oder URL.
		 *
		 * @return string Anbieter-ID oder '' .
		 */
		private static function find_leftover( $args, $url ) {
			$haystacks = array( (string) $url );

			foreach ( array( 'headers', 'body' ) as $part ) {
				if ( ! isset( $args[ $part ] ) ) {
					continue;
				}

				if ( is_string( $args[ $part ] ) ) {
					$haystacks[] = $args[ $part ];
				} elseif ( is_array( $args[ $part ] ) ) {
					foreach ( $args[ $part ] as $v ) {
						if ( is_string( $v ) ) {
							$haystacks[] = $v;
						}
					}
				}
			}

			foreach ( $haystacks as $h ) {
				$provider = self::provider_of( $h );

				if ( '' !== $provider ) {
					return $provider;
				}
			}

			return '';
		}

		// =====================================================================
		//  Hilfsmittel
		// =====================================================================

		/**
		 * Alle Anbieter, deren Options-Zeile wir kennen.
		 *
		 * Registrierte Connectors plus eine Merkliste — damit die Marken auch
		 * bei `plugins_loaded` gesetzt werden können, wo die Registry noch nicht
		 * steht.
		 *
		 * @return string[]
		 */
		public static function providers() {
			$auskunft = self::anbieter_ermitteln();

			return $auskunft['liste'];
		}

		/*
		 * =====================================================================
		 *  DIE VIERWERTIGE AUSKUNFT — §5(a), Mammuts Entscheidung vom 11.10.2026
		 * =====================================================================
		 *
		 *  Bis heute gab `providers()` eine Liste und sonst nichts. Eine leere
		 *  Liste hiess dann zweierlei auf einmal:
		 *
		 *      „es gibt keine Anbieter"      — gemessen
		 *      „ich konnte nicht fragen"     — nicht gemessen
		 *
		 *  Und beides sah gleich aus.
		 *
		 *      Leer heisst „nicht feststellbar" und nicht „keiner" — wer die
		 *      zwei nicht trennt, liefert eine Messung, die nie etwas
		 *      gemessen hat.                                        (Mammut)
		 *
		 *  DAS WAR DIE URSACHE HINTER §5(a): nicht das Markieren war schmal,
		 *  sondern das Wissen. Und es ist dieselbe Ursache wie bei Q3 auf der
		 *  Schreibseite — darum fallen die zwei Reparaturen in eine.
		 *
		 *      Zwei Reparaturen an einer Ursache sind zwei Gelegenheiten,
		 *      sie zu verfehlen.                                    (Mammut)
		 *
		 *  VIER WERTE, und jeder sagt, OB gemessen wurde:
		 *
		 *      live        gefragt, geantwortet, es gibt welche
		 *      leer        gefragt, geantwortet, es gibt keine
		 *      zu_frueh    den Ruf gibt es, aber `init` ist nicht gelaufen
		 *      kein_kern   den Ruf gibt es nicht  (Connectors-API, ab WP 7.0)
		 *
		 *  Die zwei letzten sind NICHT GEMESSEN. Das ist dieselbe Ordnung wie
		 *  bei jedem Zeugen dieses Hauses: exit 0 / exit 1 / exit 2, und ein
		 *  „nein" aus Blindheit ist keins.
		 *
		 *  EIN RECHNER, ZWEI LESER. `providers()` gibt die Liste,
		 *  `providers_auskunft()` die Liste MIT ihrem Stand. Beide holen sie
		 *  aus derselben privaten Stelle — zwei Leser sind erlaubt, zwei
		 *  Schreiber nie.                                             (P1-12)
		 *
		 *  BENANNTE GRENZE: `$stand` beschreibt die MESSUNG, nicht die
		 *  gefilterte Liste. Wer an `cv_providers` haengt, kann die Liste
		 *  aendern, ohne dass der Stand davon weiss — und das ist richtig:
		 *  der Stand sagt, was der KNOTEN gesagt hat, nicht was ein Plugin
		 *  daraus gemacht hat.
		 *
		 *  KEIN ZWISCHENSPEICHER. Er waere billig und ich habe heute gelernt,
		 *  was ein Zwischenspeicher kostet, der einen Zustand behaelt statt
		 *  einer Antwort (`zeile_lesen()`, L8). `anbieter_ermitteln()` laeuft
		 *  je Aufruf; die Schreibung darin ist durch `array_diff` geriegelt
		 *  und passiert einmal.
		 * =====================================================================
		 *
		 * @return array{liste:string[],stand:string,live:string[],gespeichert:string[]}
		 */
		public static function providers_auskunft() {
			return self::anbieter_ermitteln();
		}

		/**
		 * Die eine Stelle, an der die Anbieterliste entsteht.
		 */
		private static function anbieter_ermitteln() {
			$known = get_option( self::OPTION_KNOWN, array() );
			$known = is_array( $known ) ? $known : array();

			$live  = array();
			$stand = 'kein_kern';

			if ( ! function_exists( 'wp_get_connectors' ) ) {
				// Die Connectors-API gibt es ab WordPress 7.0. Unser Kopf sagt
				// „Requires at least: 7.0" — fehlt sie trotzdem, ist das eine
				// Aussage ueber den Knoten und keine ueber diese Liste.
				$stand = 'kein_kern';
			} elseif ( ! did_action( 'init' ) ) {
				// Hier laeuft Pacioli bei plugins_loaded 6 und init 1 durch.
				// Die Anbieter-Plugins melden sich bei init 20 an.
				$stand = 'zu_frueh';
			} else {
				foreach ( (array) wp_get_connectors() as $id => $unused ) {
					if ( is_string( $id ) && '' !== $id ) {
						$live[] = sanitize_key( $id );
					}
				}

				$stand = $live ? 'live' : 'leer';
			}

			$all = array_values( array_unique( array_filter( array_merge( $known, $live ) ) ) );

			if ( $live && array_diff( $all, $known ) ) {
				update_option( self::OPTION_KNOWN, $all, true );
			}

			/**
			 * Anbieter, deren Schlüsselzeile der Vault verwaltet.
			 */
			$all = (array) apply_filters( 'pacioli_providers', $all );

			return array(
				'liste'       => $all,
				'stand'       => $stand,
				'live'        => $live,
				'gespeichert' => $known,
			);
		}

		/**
		 * Optionsname eines Anbieters, nach dem Schema des Cores.
		 */
		public static function option_name( $provider ) {
			return 'connectors_ai_' . sanitize_key( $provider ) . '_api_key';
		}

		/**
		 * Liest eine Option unter Umgehung aller Filter.
		 */
		public static function raw_option( $name ) {
			global $wpdb;

			$value = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- liest die Anbieter-Zeile an der Options-API vorbei, damit pre_option_ (Paciolis Tausch) die Messung nicht faelscht
				$wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1", $name )
			);

			return null === $value ? '' : (string) $value;
		}
	}
}
