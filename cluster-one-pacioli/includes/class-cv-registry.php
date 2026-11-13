<?php
/**
 * Der dokumentierte Weg.
 *
 * =====================================================================
 *  Was wir zu spät gefunden haben
 * =====================================================================
 *
 *  Der Schlüssel wohnt nach `init` nicht mehr in der Options-Zeile,
 *  sondern als Authentifizierungs-Objekt in der Registry des SDK. Core
 *  selbst legt ihn dort ab:
 *
 *      add_action( 'init', '_wp_connectors_pass_default_keys_to_ai_client', 20 );
 *
 *      $ai_registry->setProviderRequestAuthentication(
 *          $connector_id,
 *          new ApiKeyRequestAuthentication( $api_key )
 *      );
 *
 *  Damit ist der Messbefund vom 24.08.2026 vollständig erklärt: Die
 *  Option wird nach `init` nie wieder gelesen, weil sie nicht mehr
 *  gebraucht wird. Wir haben an der richtigen Tür gemessen und an der
 *  falschen geklopft.
 *
 *  Zwei Eigenschaften des SDK machen den sauberen Weg möglich:
 *
 *    - `ProviderRegistry::getProviderRequestAuthentication()` gibt es —
 *      der vorherige Zustand lässt sich also sichern und zurücksetzen.
 *    - Die Authentifizierung wird beim INSTANZIIEREN DES MODELLS
 *      gebunden (`getProviderModel()` → `bindModelDependencies()`), und
 *      `PromptBuilder::generateText()` holt sich über
 *      `getConfiguredModel()` JE AUFRUF ein frisches Modell.
 *
 *  Also: Wer die Authentifizierung unmittelbar vor dem Aufruf setzt,
 *  wird gehört. Genau das, was 0.1.0 wollte — nur eine Etage höher als
 *  damals versucht.
 *
 * =====================================================================
 *  Warum die Marke trotzdem bleibt
 * =====================================================================
 *
 *  Sie wechselt die Rolle. Bis 0.3.1 war sie der Hauptweg; ab 0.4.0 ist
 *  sie der Stolperdraht.
 *
 *  In der Options-Zeile steht weiter die Marke — R1 bleibt baulich
 *  intakt, ein Datenbank-Abzug liefert nichts, und Core bindet bei
 *  `init` 20 folglich die MARKE als Authentifizierung. Greift der
 *  Override hier, wird sie überschrieben und niemand sieht sie je.
 *  Greift er nicht, reist sie hinaus, `CV_Marker` tauscht sie am
 *  ausgehenden Request (das funktioniert nachweislich), und schlägt
 *  auch das fehl, hält der Riegel den Request auf.
 *
 *  Der dokumentierte Weg trägt also, und der Behelf von gestern wird zu
 *  der Prüfung, die beweist, dass er getragen hat. Schnappt der Riegel
 *  je zu, hat sich das SDK geändert — und man erfährt es sofort, statt
 *  es an einer fremden Abrechnung zu bemerken.
 *
 * =====================================================================
 *
 * Benannte Grenze: Wer den Builder mit einem VORAB gebauten Modell-Objekt
 * benutzt (`usingModel()` mit einer fertigen Instanz), umgeht die
 * Bindung — das Objekt trägt die Authentifizierung von seiner Entstehung.
 * Der Kontext muss deshalb aufgemacht werden, BEVOR der Builder entsteht.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 *  DER RIEGEL UM DIE KLASSE — 12.09.2026, Weg C, dritte Schicht
 *
 *  Ohne ihn stirbt ein Knoten mit ZWEI Produktordnern an
 *  `Cannot redeclare class CV_Registry`, sobald der Lader seine EIGENEN Dateien
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
if ( ! class_exists( 'Pacioli_Registry' ) ) {
	class Pacioli_Registry {

		/** Einstiegspunkt des SDK. */
		const CLASS_CLIENT = 'WordPress\\AiClient\\AiClient';

		/** Das Authentifizierungs-Objekt, das auch der Core benutzt. */
		const CLASS_AUTH = 'WordPress\\AiClient\\Providers\\Http\\DTO\\ApiKeyRequestAuthentication';

		/**
		 * Sind die Klassen des dokumentierten Weges da?
		 *
		 * 02.09.2026 — DIESE METHODE HIESS EINMAL „ist der Weg begehbar".
		 * Das war ein Versprechen, und sie hat es nicht gehalten. Gemessen
		 * werden genau drei Dinge:
		 *
		 *     1. die Einstiegsklasse existiert
		 *     2. die Authentifizierungsklasse existiert
		 *     3. defaultRegistry() ist aufrufbar
		 *
		 * NICHT gemessen wird, ob die Registry ihren Zustand HERAUSGIBT. Das
		 * ist eine zweite Frage, und sie hat seit dem 02.09. eine eigene
		 * Methode: state_readable().
		 *
		 * Der Unterschied ist kein Wortstreit. `available()` kann wahr sein,
		 * während `current()` STRUKTURELL immer null liefert — und dann misst
		 * eine Prüfung, die sich auf `current()` stützt, ihre eigene Blindheit
		 * und schreibt sie als Befund auf. Genau das ist am 02.09.2026 auf
		 * einem echten Knoten passiert.
		 *
		 *     Wer eine Methode nach dem benennt, was er von ihr hofft, statt
		 *     nach dem, was sie misst, hat die Lücke schon eingebaut.
		 */
		public static function available() {
			return class_exists( self::CLASS_CLIENT )
				&& class_exists( self::CLASS_AUTH )
				&& is_callable( array( self::CLASS_CLIENT, 'defaultRegistry' ) );
		}

		/**
		 * Gibt die Registry ihren Zustand heraus?
		 *
		 * Das SETZEN geht über `setProviderRequestAuthentication()` — das
		 * verlangt schon `registry()`. Das LESEN geht über
		 * `getProviderRequestAuthentication()`, und die Methode ist im SDK
		 * vorhanden, aber sie ist nichts, worauf man sich verlassen darf: eine
		 * Registry ohne sie ist eine erlaubte Bauform.
		 *
		 * Für den Betrieb ist das folgenlos — der Tausch braucht sie nicht,
		 * nur das Zurücksetzen wird gröber (dann greift die Marke). Für den
		 * SELBSTTEST ist es der Unterschied zwischen „nein" und „ich sehe
		 * nichts".
		 *
		 * @return bool
		 */
		public static function state_readable() {
			$registry = self::registry();

			return ( $registry && method_exists( $registry, 'getProviderRequestAuthentication' ) );
		}

		/**
		 * Die Registry, oder null.
		 *
		 * @return object|null
		 */
		private static function registry() {
			if ( ! self::available() ) {
				return null;
			}

			try {
				$registry = call_user_func( array( self::CLASS_CLIENT, 'defaultRegistry' ) );
			} catch ( Throwable $e ) {
				return null;
			}

			if ( ! is_object( $registry ) || ! method_exists( $registry, 'setProviderRequestAuthentication' ) ) {
				return null;
			}

			return $registry;
		}

		/**
		 * Setzt die Authentifizierung eines Anbieters für die Dauer eines Aufrufs.
		 *
		 * @param string $provider Connector-ID.
		 * @param string $secret   Klartext-Schlüssel.
		 * @return array|null Zustand für restore(), oder null wenn nichts geschah.
		 */
		public static function override( $provider, $secret ) {
			$registry = self::registry();

			if ( ! $registry || '' === (string) $provider || '' === (string) $secret ) {
				return null;
			}

			$previous = null;

			if ( method_exists( $registry, 'getProviderRequestAuthentication' ) ) {
				try {
					$previous = $registry->getProviderRequestAuthentication( $provider );
				} catch ( Throwable $e ) {
					$previous = null;
				}
			}

			if ( ! self::apply( $registry, $provider, $secret ) ) {
				return null;
			}

			return array(
				'provider' => (string) $provider,
				'previous' => $previous,
			);
		}

		/**
		 * Stellt den Zustand von vor dem Aufruf wieder her.
		 *
		 * Gab es vorher nichts, wird die MARKE gesetzt statt gar nichts. Sonst
		 * bliebe der Schlüssel des letzten Handelnden in der Registry liegen und
		 * ein späterer, ungebundener Aufruf im selben Request würde ihn benutzen
		 * — eine Fehlbuchung, die niemand bemerkt.
		 *
		 * Mit der Marke ist der Ruhezustand derselbe wie nach `init`: harmlos,
		 * und beim Hinausgehen fängt ihn der Riegel ab.
		 */
		public static function restore( $state ) {
			if ( ! is_array( $state ) || empty( $state['provider'] ) ) {
				return;
			}

			$registry = self::registry();

			if ( ! $registry ) {
				return;
			}

			$provider = $state['provider'];
			$previous = isset( $state['previous'] ) ? $state['previous'] : null;

			if ( is_object( $previous ) ) {
				try {
					$registry->setProviderRequestAuthentication( $provider, $previous );
				} catch ( Throwable $e ) {
					self::apply( $registry, $provider, Pacioli_Marker::for_provider( $provider ) );
				}

				return;
			}

			self::apply( $registry, $provider, Pacioli_Marker::for_provider( $provider ) );
		}

		/**
		 * Was beim letzten `apply()` in diesem Request geschehen ist.
		 *
		 * =====================================================================
		 *  H191 — der stille Fehlschlag
		 * =====================================================================
		 *
		 *  `apply()` fängt seinen eigenen Fehler und gibt `false` zurück.
		 *  `override()` macht daraus `null` — und `null` heisst dort auch
		 *  „es gab nichts zu tun". Zwei sehr verschiedene Lagen, ein Wert.
		 *
		 *      Ein Fehlschlag, der aussieht wie ein Nichtstun, wird von
		 *      niemandem gefunden, weil ihn niemand sucht.
		 *
		 *  Deshalb wird er hier festgehalten. Der Aufrufweg ändert sich
		 *  dadurch NICHT: `apply()` gibt weiter `true`/`false` zurück,
		 *  `override()` weiter das Feld oder `null`. Es kommt nur eine
		 *  Auskunft dazu, die der Selbsttest lesen kann.
		 *
		 * @var array{versucht:bool,gelungen:bool,fehler:string}
		 */
		private static $last_apply = array(
			'versucht'  => false,
			'gelungen'  => false,
			'fehler'    => '',
		);

		/**
		 * Der letzte Setzversuch dieses Requests.
		 *
		 * @return array{versucht:bool,gelungen:bool,fehler:string}
		 */
		public static function last_apply() {
			return self::$last_apply;
		}

		/**
		 * Welche Registry-Instanz gilt gerade?
		 *
		 * Nur eine Zahl, und sie beantwortet eine einzige Frage: Ist es beim
		 * Lesen dieselbe Instanz wie beim Setzen? Liefert `defaultRegistry()`
		 * jedes Mal ein neues Objekt, landet das Setzen auf dem einen und das
		 * Lesen auf dem anderen — dann greift der Tausch NIE, auch im Betrieb
		 * nicht, und keine Zeile dieses Prüfstands hätte es bisher sagen können.
		 *
		 * @return int 0, wenn es keine Registry gibt.
		 */
		public static function instance_id() {
			$registry = self::registry();

			return $registry ? spl_object_id( $registry ) : 0;
		}

		/**
		 * Legt ein Authentifizierungs-Objekt an und setzt es.
		 */
		private static function apply( $registry, $provider, $value ) {
			$class = self::CLASS_AUTH;

			self::$last_apply = array(
				'versucht' => true,
				'gelungen' => false,
				'fehler'   => '',
			);

			try {
				$registry->setProviderRequestAuthentication( $provider, new $class( (string) $value ) );
			} catch ( Throwable $e ) {
				self::$last_apply['fehler'] = get_class( $e ) . ': ' . $e->getMessage();

				return false;
			}

			self::$last_apply['gelungen'] = true;

			return true;
		}

		/**
		 * Welches Authentifizierungs-Objekt gilt gerade für diesen Anbieter?
		 *
		 * Nur für den Selbsttest. Der Schlüssel darin wird nie ausgelesen.
		 *
		 * ACHTUNG, und das ist die Lehre vom 02.09.2026: `null` heißt hier
		 * DREIERLEI und die drei Fälle sehen von außen gleich aus —
		 *
		 *     a) es ist nichts gesetzt
		 *     b) der Abruf hat geworfen
		 *     c) diese Registry hat den Getter gar nicht
		 *
		 * Fall c ist kein Zustand, sondern Blindheit. Wer `current()` benutzt,
		 * um einen Befund zu bilden, muss vorher `state_readable()` fragen —
		 * sonst schreibt er sein Nichtsehen als Nein auf.
		 *
		 * @return object|null
		 */
		public static function current( $provider ) {
			$registry = self::registry();

			if ( ! $registry || ! method_exists( $registry, 'getProviderRequestAuthentication' ) ) {
				return null;
			}

			try {
				return $registry->getProviderRequestAuthentication( $provider );
			} catch ( Throwable $e ) {
				return null;
			}
		}
	}
}
