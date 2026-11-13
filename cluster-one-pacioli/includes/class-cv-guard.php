<?php
/**
 * Was der Betreiber wissen muss, bevor er sich auf etwas verlässt.
 *
 * Diese Klasse stellt keine Zeugnisse aus. Sie sammelt, was gerade nicht
 * stimmt, und sagt es — auch dann, wenn es unbequem ist.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 *  DER RIEGEL UM DIE KLASSE — 12.09.2026, Weg C, dritte Schicht
 *
 *  Ohne ihn stirbt ein Knoten mit ZWEI Produktordnern an
 *  `Cannot redeclare class CV_Guard`, sobald der Lader seine EIGENEN Dateien
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
if ( ! class_exists( 'Pacioli_Guard' ) ) {
	class Pacioli_Guard {

		/**
		 * Wo eine Meldung hingehört.
		 *
		 * Die Unterscheidung ist NICHT „eigene Meldungen auf die eigene Seite".
		 * Sie ist:
		 *
		 *     Ein Hinweis auf etwas, das gerade kaputt ist, gehört überall hin.
		 *     Ein Hinweis auf etwas, das noch nicht getan ist, gehört dorthin,
		 *     wo man es tun kann.
		 *
		 * Fünf der sechs Meldungen beschreiben einen Schaden, der JETZT besteht —
		 * gesperrte Aufrufe, ein gemeinsamer Schlüssel, ein nicht gegriffener
		 * Tausch. Wer davon erfährt, muss davon erfahren, egal wo er steht. Sie
		 * tragen deshalb kein 'ort' und erscheinen überall, ohne Kreuz.
		 *
		 * Genau eine beschreibt eine offene Rechnung: der Nachweis am ausgehenden
		 * Request. Er ist auf einem Testknoten gar nicht zu führen, weil dort kein
		 * Request hinausgeht — und stand bis 0.7.1 trotzdem über jeder Seite jedes
		 * fremden Plugins. Entscheidung des SysOps, 31.08.2026.
		 */
		const ORT_EIGENE_SEITE = 'eigene_seite';

		public static function init() {
			add_action( 'admin_notices', array( __CLASS__, 'render_notices' ) );
		}

		/**
		 * Alle offenen Probleme.
		 *
		 * Diese Liste ist die eine Quelle: `cv_problems()`, die eigene Seite und
		 * die Meldungen im Backend lesen alle hier. 'ort' sagt, wo eine Meldung
		 * ERSCHEINT — nicht, ob sie besteht. Ein Problem verschwindet nicht
		 * dadurch, dass es woanders nicht angezeigt wird.
		 *
		 * @return array<int,array{level:string,title:string,body:string,ort?:string}>
		 */
		public static function problems() {
			$problems = array();

			if ( ! Pacioli_Crypto::has_key() ) {
				$problems[] = array(
					'level' => 'error',
					'title' => __( 'No encryption key', 'cluster-one-pacioli' ),
					'body'  => __( 'No access keys can be stored. One is normally created on activation — please deactivate the plugin once and activate it again.', 'cluster-one-pacioli' ),
				);
			}

			// 03.09.2026 — die beiden Signale widersprechen sich.
			//
			// Kasper meldet das Organ `menue` als mitreisend, aber der Slug
			// `cluster_one` ist zum Zeitpunkt von `admin_menu` nicht
			// angemeldet. Die Maske liegt dann als eigene Seite da statt als
			// Tab — sie funktioniert, aber sie liegt woanders, als jeder
			// erwartet, der Cluster One kennt.
			//
			//     Zwei Signale, die dasselbe sagen sollten und es nicht tun,
			//     sind ein Befund. Wer den lauteren nimmt und den anderen
			//     wegwirft, hat sich entschieden statt gemessen.
			$befund = class_exists( 'Pacioli_Admin' ) ? Pacioli_Admin::eltern_befund() : array();

			if ( ! empty( $befund['organ'] ) && empty( $befund['slug'] ) ) {
				$problems[] = array(
					'level' => 'warning',
					// KEIN `ort`: das hier ist kein offener Posten, den man auf
					// unserer Seite erledigen koennte, sondern ein Zustand des
					// Knotens. Er gehoert dorthin, wo jemand die Maske sucht —
					// also ueberall.
					'title' => __( 'Cluster One is on board, but its menu is not registered', 'cluster-one-pacioli' ),
					'body'  => __( 'The key mask therefore has its own entry in the main menu instead of sitting under Cluster One. Nothing is lost — but if you expected it under Cluster One, this is why it is not there. A plugin that registers the Cluster One menu is probably deactivated on this site.', 'cluster-one-pacioli' ),
				);
			}

			foreach ( self::leaking_providers() as $provider => $unused ) {
				$problems[] = array(
					'level' => 'error',
					'title' => sprintf(
						/* translators: %s: Anbieter-ID. */
						__( 'Shared key for "%s" — all AI calls are blocked', 'cluster-one-pacioli' ),
						$provider
					),
					'body'  => __( 'The key comes from an environment variable or a constant in wp-config.php. Both beat the database; no filter gets past them, and every user would be working on the same bill. As long as that is the case, Connector Vault refuses every AI call. Please remove it — everything runs again by itself afterwards.', 'cluster-one-pacioli' ),
				);
			}

			if ( $pending = self::unswept_providers() ) { // phpcs:ignore
				$problems[] = array(
					'level' => 'error',
					'title' => __( 'A provider row holds a key in plain text', 'cluster-one-pacioli' ),
					/* POSTEN 1 — die Adresse kommt aus dem Code. Der
					 * Rueckfall ist benannt: ohne CV_Admin nennt der Satz
					 * „die eigene Seite dieses Plugins" und keinen Ort,
					 * den es hier nicht gibt.                 (R-LEIHE-1) */
					'body'  => sprintf(
						/* translators: 1: Liste von Anbieter-IDs, 2: Ort der Pruefungen auf diesem Knoten. */
						__( 'Affected: %1$s. The vault normally takes such keys into safekeeping itself — here that did not work, and it would rather leave them standing than lose them. As long as they sit there, they apply to everyone. Under %2$s the cleanup can be started again; if it fails once more, the encryption key is probably missing.', 'cluster-one-pacioli' ),
						implode( ', ', $pending ),
						class_exists( 'Pacioli_Admin' )
							? Pacioli_Admin::wo_pruefungen()
							: __( 'this plugin\'s own page', 'cluster-one-pacioli' )
					),
				);
			}

			if ( self::competing_gateway() ) {
				$problems[] = array(
					'level' => 'warning',
					'title' => __( 'Another plugin is rerouting AI calls', 'cluster-one-pacioli' ),
					'body'  => __( 'AI Engine can route every AI call on the installation through itself. Together with Connector Vault it is not predictable which key ends up travelling. Please use one of the two.', 'cluster-one-pacioli' ),
				);
			}

			if ( $leaks = Pacioli_Marker::leaks() ) { // phpcs:ignore
				$last = end( $leaks );

				$problems[] = array(
					'level' => 'error',
					'title' => __( 'The key swap did not take hold', 'cluster-one-pacioli' ),
					'body'  => sprintf(
						/* translators: 1: Anzahl, 2: Anbieter, 3: Zeitpunkt, 4: Ort der Pruefungen auf diesem Knoten. */
						__( '%1$d call(s) were stopped because no key could be inserted for the acting provider — most recently "%2$s" on %3$s. Nobody\'s bill was charged. The AI client has probably changed; the checks under %4$s say which of the two paths failed.', 'cluster-one-pacioli' ),
						count( $leaks ),
						esc_html( $last['provider'] ),
						date_i18n( get_option( 'date_format' ) . ' H:i', (int) $last['time'] ),
						class_exists( 'Pacioli_Admin' )
							? Pacioli_Admin::wo_pruefungen()
							: __( 'this plugin\'s own page', 'cluster-one-pacioli' )
					),
				);
			}

			/* =================================================================
			 *  POSTEN A — DIE MAHNUNG HAENGT NICHT MEHR AN EINEM KLICK
			 * =================================================================
			 *
			 *  DAS WAR DER SCHAERFSTE TEIL DES BEFUNDS, und er steht hier und
			 *  nicht am Knopf. Bis zum 08.10.2026 genuegte
			 *  `witness_confirmed()` — also ein Klick hinter
			 *  `manage_options` — und diese Mahnung war WEG. Auf zwei Knoten
			 *  ist genau das passiert.
			 *
			 *      Ich habe gemeldet: „eine Bestaetigung steht falsch da."
			 *      Es war:            „eine Warnung ist verschwunden."
			 *
			 *  Und die Seite, von der die Mahnung verschwand, klagt den
			 *  Vorgaenger dafuer an, auf einem falschen Gruen eine Warnung
			 *  geloescht zu haben.
			 *
			 * -----------------------------------------------------------------
			 *  ZWEI ENTSCHEIDUNGEN
			 *
			 *  1  DIE BEDINGUNG IST DIESELBE FUNKTION, die den Knopf riegelt —
			 *     `witness_refusal()`. Nicht eine zweite, die dasselbe
			 *     bedeuten soll: zwei Bedingungen fuer eine Wahrheit gehen
			 *     auseinander, sobald jemand eine davon anfasst.    (P1-12)
			 *
			 *  2  SIE VERSCHWINDET NICHT, SIE WIRD LEISER. Mammut hat beides
			 *     angeboten; ich nehme das Leiserwerden, und der Grund ist
			 *     sein eigener Befund: der Defekt war, dass ein Klick eine
			 *     Warnung VERSCHWINDEN liess.
			 *
			 *         Solange etwas nur leiser werden kann, kann kein Klick
			 *         einen Knoten so aussehen lassen, als sei hier nie
			 *         etwas offen gewesen.
			 *
			 *     Die leise Zeile sagt dazu, was die Bestaetigung NICHT ist:
			 *     von innen nachpruefbar. Das ist dieselbe Grenze, die am
			 *     Riegel steht, und sie gehoert an beide Enden.
			 * ================================================================= */
			$w_rec = Pacioli_Selftest::witness_record();

			$w_gilt = Pacioli_Selftest::witness_confirmed()
				&& '' === Pacioli_Selftest::witness_refusal(
					(int) Pacioli_Marker::bound_calls()['count'],
					isset( $w_rec['note'] ) ? $w_rec['note'] : ''
				);

			if ( ! $w_gilt ) {
				$problems[] = array(
					'level' => 'warning',
					'title' => __( 'The proof at the outgoing request is still outstanding', 'cluster-one-pacioli' ),
					/* 0.8.47: bis 0.8.46 verlangte der Satz „one run with two identities at the actually
					 * outgoing request, in which two different fingerprints arrive" – die Vorstellung vor
					 * Turing. Jetzt nennt er das Werkzeug und den Knopf. */
					'body'  => __( 'That the key swap arrives at the provider is something this plugin cannot prove by itself — it can only perform the swap and watch itself doing it. What is missing is a measurement from outside this plugin: Turing (CO), tab "Pacioli witness", listening at the actually outgoing request. Further down this page, under "The proof this plugin cannot supply itself", the button "Measure with Turing and record" runs that witness and records its verdict if it is GREEN.', 'cluster-one-pacioli' ),
					'ort'   => self::ORT_EIGENE_SEITE,
				);
			} else {
				$w_wer   = get_userdata( (int) ( $w_rec['by'] ?? 0 ) );
				$w_name  = $w_wer ? $w_wer->display_name : '?';
				$w_datum = date_i18n( get_option( 'date_format' ), (int) ( $w_rec['time'] ?? 0 ) );

				/* 0.8.47 (30.10.2026, Neo): ZWEI EINTRAGSARTEN, BIS HEUTE EIN TEXT. Seit 0.8.46 traegt
				 * der Datensatz `turing` (fassung, urteil, anbieter), wenn Pacioli Turings Durchgang
				 * gefahren hat; die Meldung sagte trotzdem „not verified … a person's statement", den
				 * Satz des Handeintrags. Nemesus las ihn als „du musst noch mit etwas anderem messen".
				 * Jetzt sagt die Meldung bei einem Turing-Eintrag, wer gemessen hat und wo Turing dabei
				 * sitzt. Der alte Satz bleibt fuer Altbestand ohne `turing` (Handeintraege bis 0.8.46;
				 * seit 0.8.47 entstehen keine neuen). */
				if ( isset( $w_rec['turing'] ) && is_array( $w_rec['turing'] ) ) {
					$w_t = $w_rec['turing'];
					$w_a = isset( $w_t['anbieter'] ) ? (array) $w_t['anbieter'] : array();

					$problems[] = array(
						'level' => 'info',
						'title' => __( 'The outside proof is recorded — measured by Turing', 'cluster-one-pacioli' ),
						'body'  => sprintf(
							/* translators: 1: Turing version, 2: date, 3: number of providers, 4: provider list, 5: name of the person who started the run. */
							_n(
								'Turing %1$s measured at the outgoing request of this node on %2$s: %3$d provider GREEN (%4$s), the key in the POST comes from no option row. Pacioli adopted Turing’s verdict and did not measure itself; Turing sits in the same installation and sees what runs through wp_remote_*. Run started by %5$s; it can be withdrawn where it was made.',
								'Turing %1$s measured at the outgoing request of this node on %2$s: %3$d providers GREEN (%4$s), the key in the POST comes from no option row. Pacioli adopted Turing’s verdict and did not measure itself; Turing sits in the same installation and sees what runs through wp_remote_*. Run started by %5$s; it can be withdrawn where it was made.',
								count( $w_a ),
								'cluster-one-pacioli'
							),
							isset( $w_t['fassung'] ) && '' !== $w_t['fassung'] ? (string) $w_t['fassung'] : '?',
							$w_datum,
							count( $w_a ),
							$w_a ? implode( ', ', array_map( 'strval', $w_a ) ) : '—',
							$w_name
						),
						'ort'   => self::ORT_EIGENE_SEITE,
					);
				} else {
					$problems[] = array(
						'level' => 'info',
						'title' => __( 'The outside proof is recorded — not verified', 'cluster-one-pacioli' ),
						'body'  => sprintf(
							/* translators: 1: Name, 2: Datum, 3: Notiz. */
							__( 'Recorded by %1$s on %2$s. Measured with: %3$s. This entry is a person\'s statement about a measurement taken outside this installation — this plugin cannot check it from the inside, and it is not making the claim. It can be withdrawn where it was made.', 'cluster-one-pacioli' ),
							$w_name,
							$w_datum,
							// Eine Notiz kann selbst mit Punkt enden; der Satz hier setzt seinen eigenen.
							( isset( $w_rec['note'] ) && '' !== $w_rec['note'] ) ? rtrim( $w_rec['note'], '.' ) : '—'
						),
						'ort'   => self::ORT_EIGENE_SEITE,
					);
				}
			}

			return $problems;
		}

		/**
		 * Anbieter, deren Schlüssel aus Environment oder Konstante kommt.
		 *
		 * Das ist der einzige verbliebene Weg zu einem gemeinsamen Schlüssel:
		 * Beide schlagen die Datenbank, kein Filter erreicht sie. Deshalb ist
		 * das kein Hinweis, sondern eine Sperre (siehe CV_Interceptor).
		 *
		 * @return array<string,string>
		 */
		public static function leaking_providers() {
			$leaks = array();

			foreach ( Pacioli_Marker::providers() as $provider ) {
				if ( 'env_or_const' === self::key_source( $provider ) ) {
					$leaks[ $provider ] = 'env_or_const';
				}
			}

			return $leaks;
		}

		/**
		 * Anbieter, in deren Options-Zeile noch etwas anderes als die Marke steht.
		 *
		 * @return string[]
		 */
		public static function unswept_providers() {
			$pending = array();

			foreach ( Pacioli_Marker::providers() as $provider ) {
				$stored = Pacioli_Marker::raw_option( Pacioli_Marker::option_name( $provider ) );

				if ( '' === $stored || $stored === Pacioli_Marker::for_provider( $provider ) ) {
					continue;
				}

				// Eine fremde oder veraltete Marke ist kein Schlüssel im
				// Klartext — sie wird beim nächsten Durchlauf ersetzt.
				if ( Pacioli_Marker::looks_like_marker( $stored ) ) {
					continue;
				}

				$pending[] = $provider;
			}

			return $pending;
		}

		/**
		 * Woher stammt der Schlüssel eines Anbieters?
		 *
		 * Nutzt die interne Core-Funktion, wenn es sie gibt. Sie ist als privat
		 * markiert und kann sich ändern — deshalb mit eigener Rückfalllösung
		 * statt mit Vertrauen.
		 *
		 * @return string 'env_or_const' | 'db' | 'none'
		 */
		public static function key_source( $provider ) {
			if ( function_exists( '_wp_connectors_get_api_key_source' ) ) {
				$source = _wp_connectors_get_api_key_source( $provider );

				if ( is_string( $source ) && in_array( $source, array( 'env', 'environment', 'constant' ), true ) ) {
					return 'env_or_const';
				}
			}

			$upper = strtoupper( str_replace( '-', '_', $provider ) ) . '_API_KEY';

			if ( getenv( $upper ) || ( defined( $upper ) && constant( $upper ) ) ) {
				return 'env_or_const';
			}

			return Pacioli_Marker::raw_option( Pacioli_Marker::option_name( $provider ) ) ? 'db' : 'none';
		}

		/** Läuft ein konkurrierendes Gateway? */
		public static function competing_gateway() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			return is_plugin_active( 'ai-engine/ai-engine.php' )
				|| is_plugin_active( 'ai-engine-pro/ai-engine-pro.php' );
		}

		/**
		 * Steht der Betrachter gerade auf der eigenen Seite?
		 *
		 * Zuerst der Bildschirm, weil er die Auskunft des Kerns ist. Steht er noch
		 * nicht — `admin_notices` kann vor `current_screen` laufen —, dann die
		 * Adresszeile. Beide fragen denselben Slug, und der wohnt bei dem, der die
		 * Seite anmeldet.
		 */
		public static function on_own_page() {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
			$id     = $screen ? (string) $screen->id : '';

			if ( '' !== $id ) {
				return false !== strpos( $id, Pacioli_Selftest::PAGE );
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nur gelesen, um zu entscheiden, wo etwas angezeigt wird.
			return isset( $_GET['page'] ) && Pacioli_Selftest::PAGE === sanitize_key( wp_unslash( $_GET['page'] ) );
		}

		/**
		 * Zeigt die offenen Probleme im Backend.
		 *
		 * Das Kreuz bekommt nur, was auf der eigenen Seite steht — und es ist
		 * WordPress' eingebautes `is-dismissible`, das sich nichts merkt.
		 *
		 *     Ein Wegklicken, das sich merkt, versteckt einen offenen Posten für
		 *     immer. Eins, das sich nichts merkt, räumt nur den Blick frei.
		 *
		 * Deshalb hier keine Option, kein user_meta, kein Datum: Der Zustand steht
		 * in CV_Selftest::witness_confirmed(), und ein zweiter Schreibweg darauf
		 * wäre P1-12.
		 */
		public static function render_notices() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$eigene_seite = self::on_own_page();

			foreach ( self::problems() as $problem ) {
				$nur_hier = ( ( $problem['ort'] ?? '' ) === self::ORT_EIGENE_SEITE );

				if ( $nur_hier && ! $eigene_seite ) {
					continue;
				}

				printf(
					'<div class="notice notice-%1$s%2$s"><p><strong>%3$s</strong><br>%4$s</p></div>',
					esc_attr( $problem['level'] ),
					$nur_hier ? ' is-dismissible' : '',
					esc_html( $problem['title'] ),
					esc_html( $problem['body'] )
				);
			}
		}
	}
}
