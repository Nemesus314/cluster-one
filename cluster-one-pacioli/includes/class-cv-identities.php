<?php
/**
 * KI-Identitäten als WordPress-Benutzer.
 *
 * Warum überhaupt Benutzerkonten? Weil damit drei Dinge auf einmal gelöst
 * sind: Autorschaft von Beiträgen, eine handelnde Identität im Hintergrund-
 * Job (wo es keinen eingeloggten Benutzer gibt) und das Besitzverhältnis.
 *
 * Ein KI-Konto ist eine Dienst-Identität, KEIN Zugang. Wer sich darauf
 * anmelden könnte, spricht im Namen einer fremden KI und gibt fremdes Geld
 * aus. Der grösste Teil dieser Klasse besteht deshalb aus Absperrungen.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 *  DER RIEGEL UM DIE KLASSE — 12.09.2026, Weg C, dritte Schicht
 *
 *  Ohne ihn stirbt ein Knoten mit ZWEI Produktordnern an
 *  `Cannot redeclare class CV_Identities`, sobald der Lader seine EIGENEN Dateien
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
 *
 *  UND DIESE DATEI WIRD NICHT EINGERUECKT — mit Absicht, und hier steht der
 *  Grund, damit ihn niemand fuer Schlamperei haelt:
 *
 *  Sie traegt eine Funktion, auf der eine BYTE-GENAUE Auflage liegt. Die
 *  Einrueckung haette ihr genau einen Tabulator je Zeile hinzugefuegt —
 *  gemessen: 22 Bytes, 22 Tabulatoren, sonst nichts. Byte-genau ist damit
 *  verletzt, sachlich nichts geaendert.
 *
 *      Eine Auflage, die man durch Umformatieren verletzt, ist entweder
 *      falsch gestellt oder wortwoertlich gemeint. Solange sie steht, ist
 *      sie wortwoertlich gemeint.
 *
 *  Bei den Funktionen am 11.09. war die Einrueckung die SACHE — ohne sie
 *  wird vorweg gebunden. Hier ist sie Geschmack: PHP bindet die Klasse
 *  spaeter, ob ihr Rumpf eingerueckt ist oder nicht. Wo Form und Auflage
 *  sich widersprechen und die Form nur Form ist, weicht die Form.
 * ------------------------------------------------------------------------- */
if ( ! class_exists( 'Pacioli_Identities' ) ) {
class Pacioli_Identities {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_roles' ) );

		// --- Absperrungen -------------------------------------------------
		add_filter( 'authenticate', array( __CLASS__, 'block_login' ), 100, 1 );
		add_filter( 'wp_is_application_passwords_available_for_user', array( __CLASS__, 'block_app_passwords' ), 10, 2 );
		add_filter( 'rest_user_query', array( __CLASS__, 'hide_from_rest' ) );
		add_filter( 'pre_get_posts', array( __CLASS__, 'block_author_archives' ) );
		add_action( 'pre_user_query', array( __CLASS__, 'hide_from_user_list' ) );
		/* 18.10.2026 (Neo): hier stand 'get_metadata'. Diesen Filter gibt es in
		 * WordPress nicht — wp-includes/meta.php feuert "get_{$meta_type}_metadata",
		 * also 'get_user_metadata'. Der Schild hing seit seiner Geburt an einem
		 * Haken, den nie jemand zog: get_user_meta() gab den Chiffretext an
		 * jeden heraus, read_shielded() und is_unshielded() liefen leer.
		 * Alle Lesestellen des laufenden Plugins gehen ueber read_shielded()
		 * (gemessen: vault, marker:691, admin:536/2527, selftest:1203); nur
		 * uninstall.php liest roh, und dort ist dieser Haken nie gesetzt. */
		add_filter( 'get_user_metadata', array( __CLASS__, 'shield_secret' ), 10, 5 );

		// --- Datenschutz --------------------------------------------------
		add_action( 'delete_user', array( __CLASS__, 'delete_owned_identities' ) );
		add_filter( 'wp_privacy_personal_data_exporters', array( __CLASS__, 'register_exporter' ) );
	}

	/**
	 * Legt die beiden Rollen an.
	 *
	 * Bewusst minimal: gerade genug, um im Raum schreiben zu können.
	 */
	public static function register_roles() {
		if ( ! get_role( PACIOLI_ROLE_PERSONA ) ) {
			add_role(
				PACIOLI_ROLE_PERSONA,
				__( 'AI persona', 'cluster-one-pacioli' ),
				array( 'read' => true )
			);
		}

		if ( ! get_role( PACIOLI_ROLE_SYSTEM ) ) {
			add_role(
				PACIOLI_ROLE_SYSTEM,
				__( 'AI system identity', 'cluster-one-pacioli' ),
				array( 'read' => true )
			);
		}
	}

	/**
	 * Ist dieses Benutzerkonto eine KI?
	 *
	 * @param int|WP_User $user
	 */
	public static function is_ai( $user ) {
		$user = is_numeric( $user ) ? get_userdata( (int) $user ) : $user;

		if ( ! $user instanceof WP_User ) {
			return false;
		}

		return in_array( PACIOLI_ROLE_PERSONA, (array) $user->roles, true )
			|| in_array( PACIOLI_ROLE_SYSTEM, (array) $user->roles, true );
	}

	/**
	 * Ist dieses Konto die Systemidentität?
	 */
	public static function is_system( $user ) {
		$user = is_numeric( $user ) ? get_userdata( (int) $user ) : $user;

		return $user instanceof WP_User && in_array( PACIOLI_ROLE_SYSTEM, (array) $user->roles, true );
	}

	/**
	 * Legt eine KI-Identität an.
	 *
	 * @param array $args {
	 *     @type int    $owner    Benutzer-ID des Besitzers. Pflicht.
	 *     @type string $name     Anzeigename der KI. Pflicht.
	 *     @type string $provider Connector-ID, z. B. 'openai'. Pflicht.
	 *     @type string $model    Modellkennung. Optional.
	 *     @type string $persona  System-Prompt. Optional.
	 *     @type bool   $system   Systemidentität statt Persona. Optional.
	 * }
	 * @return int|WP_Error Benutzer-ID der neuen Identität.
	 */
	public static function create( array $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'owner'    => 0,
				'name'     => '',
				'provider' => '',
				'model'    => '',
				'persona'  => '',
				'system'   => false,
			)
		);

		$owner = get_userdata( (int) $args['owner'] );

		if ( ! $owner ) {
			return new WP_Error( 'pacioli_no_owner', __( 'No valid owner was given.', 'cluster-one-pacioli' ) );
		}

		if ( ! user_can( $owner, PACIOLI_CAP ) ) {
			return new WP_Error( 'pacioli_owner_not_allowed', __( 'This user is not allowed to run their own AIs.', 'cluster-one-pacioli' ) );
		}

		if ( '' === trim( $args['name'] ) || '' === trim( $args['provider'] ) ) {
			return new WP_Error( 'pacioli_missing_args', __( 'Name and provider are required.', 'cluster-one-pacioli' ) );
		}

		// Systemidentität gibt es genau einmal.
		if ( $args['system'] && self::get_system_identity() ) {
			return new WP_Error( 'pacioli_system_exists', __( 'A system identity already exists.', 'cluster-one-pacioli' ) );
		}

		/* =================================================================
		 *  POSTEN 3, 10.10.2026 — DER ANZEIGENAME WIRD EINDEUTIG
		 * =================================================================
		 *
		 *  Eindeutig gemacht wurde bisher nur die ANMELDUNG, zwei Zeilen
		 *  weiter unten: `ai-assistent`, `ai-assistent-2`. Der Anzeigename
		 *  nicht — gemessen ueber drei Dateien, keine einzige Pruefung.
		 *
		 *      Eindeutig ist, was keiner zweimal vergeben kann. Was nur
		 *      noch keiner zweimal vergeben hat, ist frei.      (Mammut)
		 *
		 *  Und Christians Forderung vom September lautet: „Es ist wichtig,
		 *  dass keine KIs auf dem Knoten gleich heissen." Die hielt bisher,
		 *  solange EIN Mensch die Namen vergibt und sich erinnert.
		 *
		 * -----------------------------------------------------------------
		 *  ABSAGE UND NICHT UMBENENNEN
		 *
		 *  Eine stille „-2" am Anzeigenamen macht aus zwei gleichen Namen
		 *  zwei FAST gleiche, und das ist schlimmer als beides.
		 *
		 *      Ein Name, der sich selbst ausweicht, verbirgt die Kollision
		 *      statt sie zu melden.
		 *
		 *  Die ANMELDUNG darf weiter ausweichen — `ai-assistent-2` liest
		 *  kein Mensch.
		 *
		 * -----------------------------------------------------------------
		 *  DAS NIMMT EINE ENTSCHEIDUNG VOM 08.10. ZURUECK, und das steht
		 *  hier, damit die Begruendung nicht verlorengeht.
		 *
		 *      08.10.  „ein Hinweis, kein Verbot — zwei Leute duerfen
		 *              beide Claude sagen, es ist der Name eines Modells"
		 *      10.10.  Absage, mit Nennung der vorhandenen KI
		 *
		 *  Der aeltere Satz war nicht falsch, er war unvollstaendig: er
		 *  wog die Freiheit des Anlegers gegen nichts. Dagegen steht, dass
		 *  `cluster_one_ai_instanz_name()` den ANZEIGENAMEN liefert — in
		 *  Bismarcks Raum stehen dann zwei Zeilen, die gleich heissen, und
		 *  niemand kann sie auseinanderhalten. Christian hat genickt.
		 *
		 *  DER HINWEIS IST DAMIT WEG und nicht neben der Absage
		 *  stehengeblieben: zwei Mechanismen fuer eine Frage waeren zwei
		 *  Antworten darauf, wann ein Name vergeben ist.         (P1-12)
		 *
		 * -----------------------------------------------------------------
		 *  KEIN RIEGEL GEGEN DEN HAUSHERRN. `display_name` ist ein
		 *  gewoehnliches WP-Feld; der Benutzereditor bleibt offen. Wer dort
		 *  einen doppelten Namen setzt, hat es gesehen und gewollt.
		 *
		 *      Ein Riegel am Eingang und eine offene Tuer fuer den
		 *      Hausherrn sind kein Widerspruch. Ein Riegel, der auch den
		 *      Hausherrn aufhaelt, ist einer.
		 *
		 *  KEINE MIGRATION: vorhandene Namen werden nicht angefasst.
		 * ================================================================= */
		$c1_belegt = self::name_belegt( $args['name'] );

		if ( $c1_belegt ) {
			return new WP_Error(
				'pacioli_name_taken',
				sprintf(
					/* translators: 1: der getippte Name, 2: Name der vorhandenen KI, 3: deren Anbieter. */
					__( 'The name "%1$s" is already in use on this node: "%2$s" (%3$s). Two AIs with the same name cannot be told apart where they speak. Please choose a different one — the name is not appended with a number, because two almost-identical names are worse than two identical ones.', 'cluster-one-pacioli' ),
					sanitize_text_field( $args['name'] ),
					$c1_belegt['name'],
					'' !== $c1_belegt['provider'] ? $c1_belegt['provider'] : __( 'no provider', 'cluster-one-pacioli' )
				)
			);
		}

		$login = self::unique_login( $args['name'] );

		$user_id = wp_insert_user(
			array(
				'user_login'    => $login,
				// Zufällig und nirgends gespeichert: Das Konto ist ohnehin
				// vom Login ausgeschlossen, aber ein bekanntes Passwort wäre
				// ein unnötiges Risiko, falls die Sperre je ausfällt.
				'user_pass'     => wp_generate_password( 64, true, true ),
				'user_email'    => '',
				'display_name'  => sanitize_text_field( $args['name'] ),
				'nickname'      => sanitize_text_field( $args['name'] ),
				'role'          => $args['system'] ? PACIOLI_ROLE_SYSTEM : PACIOLI_ROLE_PERSONA,
				'show_admin_bar_front' => false,
			)
		);

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		update_user_meta( $user_id, PACIOLI_META_OWNER, (int) $owner->ID );
		update_user_meta( $user_id, PACIOLI_META_PROVIDER, sanitize_key( $args['provider'] ) );
		update_user_meta( $user_id, PACIOLI_META_MODEL, sanitize_text_field( $args['model'] ) );
		update_user_meta( $user_id, PACIOLI_META_PERSONA, wp_kses_post( $args['persona'] ) );
		update_user_meta( $user_id, PACIOLI_META_STATUS, PACIOLI_STATUS_ACTIVE );

		/**
		 * Eine KI-Identität wurde angelegt.
		 *
		 * Andockpunkt für die Persona-Schicht (Raumzuordnung, Avatar, …).
		 */
		do_action( 'pacioli_identity_created', $user_id, (int) $owner->ID, $args );

		return (int) $user_id;
	}

	/**
	 * Liefert die Systemidentität oder null.
	 */
	public static function get_system_identity() {
		$found = get_users(
			array(
				'role'    => PACIOLI_ROLE_SYSTEM,
				'number'  => 1,
				'fields'  => 'ID',
				'orderby' => 'ID',
			)
		);

		return $found ? (int) $found[0] : null;
	}

	/**
	 * Alle KI-Identitäten eines Besitzers.
	 *
	 * @return int[]
	 */
	public static function owned_by( $owner_id ) {
		// A5 — vorher stand hier 'number' => 200. Bei einer Funktion, deren
		// Ergebnis unter anderem die vollständige Löschung eines Menschen
		// steuert, lässt eine feste Obergrenze ab der 201. Identität den
		// Rest samt Schlüsseln stehen. Also Schleife statt Zahl.
		$all    = array();
		$offset = 0;
		$batch  = 200;

		do {
			$page = get_users(
				array(
					'role__in'   => array( PACIOLI_ROLE_PERSONA, PACIOLI_ROLE_SYSTEM ),
					'meta_key'   => PACIOLI_META_OWNER, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Suche nach Benutzern ueber ein Meta (Besitz, Recht, Speicher): einmal je Aufruf einer Admin-Seite bzw. der Wanderung, kleine Knoten
					'meta_value' => (int) $owner_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Suche nach Benutzern ueber ein Meta (Besitz, Recht, Speicher): einmal je Aufruf einer Admin-Seite bzw. der Wanderung, kleine Knoten
					'fields'     => 'ID',
					'number'     => $batch,
					'offset'     => $offset,
					'orderby'    => 'ID',
					'order'      => 'ASC',
				)
			);

			foreach ( $page as $id ) {
				$all[] = (int) $id;
			}

			$offset += $batch;
		} while ( count( $page ) === $batch );

		return $all;
	}

	/**
	 * Wie viele KI-Identitaeten gibt es auf diesem Knoten?
	 *
	 * 03.09.2026 — fuer den Wegweiser auf der Connector-Seite. Er soll
	 * nicht „Connector Vault ist aktiv" behaupten, sondern sagen, ob er
	 * etwas TUT.
	 *
	 *     „Ist aktiv" sagt, dass Code laeuft. Eine Zahl sagt, ob er
	 *     jemanden vertritt.
	 *
	 * Gezaehlt, nicht geladen: `count` statt `fields => ID`, damit die
	 * Auskunft auf einer fremden Seite nichts kostet, was sie nicht muss.
	 *
	 * @return int
	 */
	public static function count_all() {
		$q = new WP_User_Query(
			array(
				'role__in' => array( PACIOLI_ROLE_PERSONA, PACIOLI_ROLE_SYSTEM ),
				'fields'   => 'ID',
				'number'   => 0,
				'count_total' => true,
			)
		);

		return (int) $q->get_total();
	}

	/**
	 * Anzeigebezeichnung nach Grundregel R5: „Claude · von Anna“.
	 *
	 * Ohne Besitzerangabe weiss niemand, wessen Perspektive spricht und
	 * wessen Geld gerade ausgegeben wurde.
	 */
	public static function label( $ai_user_id ) {
		$ai = get_userdata( (int) $ai_user_id );

		if ( ! $ai ) {
			return '';
		}

		if ( self::is_system( $ai ) ) {
			return sprintf(
				/* translators: %s: Name der Systemidentität. */
				__( '%s · memory', 'cluster-one-pacioli' ),
				$ai->display_name
			);
		}

		$owner = get_userdata( (int) get_user_meta( $ai->ID, PACIOLI_META_OWNER, true ) );

		if ( ! $owner ) {
			return $ai->display_name;
		}

		return sprintf(
			/* translators: 1: Name der KI, 2: Name des Besitzers. */
			__( '%1$s · of %2$s', 'cluster-one-pacioli' ),
			$ai->display_name,
			$owner->display_name
		);
	}

	/* =====================================================================
	 *  DREI GRUPPEN, EINE ENTSCHEIDUNG — Posten A, 10.10.2026
	 * =====================================================================
	 *
	 *  Mammuts Befund §4: der Satz unter „Aufrufe ohne hinterlegten
	 *  Schluessel" sagt „sie sollten es privat erfahren haben" — und bei
	 *  den Kennungen, deren Konto es nicht mehr gibt, setzt er einen
	 *  Empfaenger voraus, den es nicht gibt.
	 *
	 *  Mein eigener Fund dazu, und er ist der Grund fuer DIESE Methode:
	 *
	 *      Die Unterscheidung steht eine Zeile darueber und wird eine
	 *      Zeile darunter weggeworfen. Das ist kein fehlendes Wissen, das
	 *      ist ein Satz, der sein eigenes Material nicht liest.
	 *
	 *  Und Mammuts Schaerfung: es sind DREI Gruppen, nicht zwei. Mein
	 *  Vorschlag nannte „mit Besitzer" und „ohne" und warf damit die
	 *  dritte weg — eine weniger als der Satz, den er ersetzen soll.
	 *
	 *      Besitzer da          es gibt jemanden, dem man es sagen kann
	 *      Konto ist weg        es gibt niemanden — eine TATSACHE
	 *      nicht feststellbar   es ist nicht bekannt, ob es jemanden gibt
	 *                           -> NICHT GEMESSEN, und das ist kein
	 *                              „ohne Besitzer"
	 *
	 *  WARUM ES EINE EIGENE METHODE IST UND KEIN ZWEITER ZWEIG IM SATZ:
	 *  `label_or_trace()` hat diese Dreiteilung schon getroffen, in ihren
	 *  drei Rueckgaben. Der Satz braucht sie auch. Zwei Stellen, die
	 *  entscheiden, WELCHE GRUPPE eine Kennung ist, waeren zwei
	 *  Wahrheiten darueber.                                     (P1-12)
	 *
	 *  Also: EINE Entscheidung, zwei Leser — die Benennung und die
	 *  Zaehlung. Zwei Leser sind erlaubt.
	 *
	 *  BENANNTE GRENZE: die Gruppe haengt an `label()` und an der SPUR.
	 *  Ohne Spur ist eine Kennung ohne Konto „nicht feststellbar", auch
	 *  dann, wenn ein Mensch wuesste, dass das Konto geloescht wurde.
	 *  Diese Methode weiss nur, was der Zaehler aufgeschrieben hat.
	 * ===================================================================== */
	const GRUPPE_BESITZER  = 'besitzer';
	const GRUPPE_KONTO_WEG = 'konto_weg';
	const GRUPPE_UNBEKANNT = 'unbekannt';

	/**
	 * In welcher der drei Gruppen steht diese Kennung?
	 *
	 * @param int   $ai_user_id
	 * @param array $spur Der Zaehlereintrag, falls es einen gibt.
	 * @return string eine der drei GRUPPE_-Konstanten
	 */
	public static function gruppe( $ai_user_id, array $spur = array() ) {
		if ( '' !== self::label( $ai_user_id ) ) {
			return self::GRUPPE_BESITZER;
		}

		$wann = isset( $spur['last'] ) ? (int) $spur['last'] : 0;

		return $wann > 0 ? self::GRUPPE_KONTO_WEG : self::GRUPPE_UNBEKANNT;
	}

	/**
	 * Teilt eine Liste „Kennung => Spur" in die drei Gruppen.
	 *
	 * Gezaehlt wird, nicht geschaetzt: die Summe der drei Gruppen ist die
	 * Laenge der Liste. Wer eine Gruppe druckt, druckt ihre Zahl aus
	 * DIESER Rechnung und nicht aus einer zweiten.
	 *
	 * @param array $liste  array( ai_user_id => array( 'count', 'last', 'provider' ) )
	 * @return array array( GRUPPE_* => array( ai_user_id => bezeichnung ) )
	 */
	public static function in_gruppen( array $liste ) {
		$aus = array(
			self::GRUPPE_BESITZER  => array(),
			self::GRUPPE_KONTO_WEG => array(),
			self::GRUPPE_UNBEKANNT => array(),
		);

		foreach ( $liste as $id => $spur ) {
			$spur = is_array( $spur ) ? $spur : array();

			$aus[ self::gruppe( (int) $id, $spur ) ][ (int) $id ]
				= self::label_or_trace( (int) $id, $spur );
		}

		return $aus;
	}

	/**
	 * Dasselbe wie `label()` — aber es schweigt nie.
	 *
	 * =====================================================================
	 *  WARUM ES DIESE FUNKTION GIBT UND NICHT EINEN ZWEIG IN label()
	 * =====================================================================
	 *
	 *  Christians DEV zeigte am 10.09.2026 „Aufrufe ohne hinterlegten
	 *  Schluessel: 1" und daneben „Betrifft: ." Gemessen: nicht der
	 *  fuenfte Zustand — die KI SELBST hatte kein Konto mehr, `label()`
	 *  gab '' zurueck, und `array_filter()` an der Fundstelle hat den
	 *  Eintrag nicht leer gedruckt, sondern HERAUSGEWORFEN.
	 *
	 *      Eine Zahl und eine Liste aus derselben Quelle, die sich
	 *      widersprechen, und niemand sagt es.
	 *
	 *  `label()` BLEIBT UNANGETASTET, und das ist Mammuts Entscheidung
	 *  vom 10.09. mit dem besseren Grund: sie haengt an `cv_label()`,
	 *  einer Zusage an fremde Haeuser (R5), und steht unter
	 *  Forenbeitraegen. Eine Signatur, die „Kennung 13 · Konto entfernt"
	 *  sagt, waere schlimmer als gar keine.
	 *
	 *      Eine Funktion, die benennt, darf schweigen.
	 *      Eine Auskunft, die zaehlt, darf es nicht.
	 *
	 *  ZWEI LEERE, UND SIE BEDEUTEN NICHT DASSELBE
	 *
	 *  Kaspers Entscheidung v0.9.75 — leer heisst „nicht feststellbar",
	 *  nicht „keiner" — gilt hier, aber sie braucht eine feinere Kante,
	 *  und Mammut hat sie am 10.09. woertlich angenommen:
	 *
	 *      MIT SPUR   die Kennung wurde GEZAEHLT. Wer gezaehlt wurde, hat
	 *                 gehandelt; wer gehandelt hat, hatte zur Zaehlzeit
	 *                 ein Konto. Das ist FESTGESTELLT, nicht
	 *                 unfeststellbar — und „nicht feststellbar" waere
	 *                 hier eine Untertreibung.
	 *
	 *      OHNE SPUR  eine Kennung ohne Konto und ohne Nachweis, dass es
	 *                 je eines gab. DAS ist „nicht feststellbar".
	 *
	 *  Kaspers Regel schuetzt davor, ein Nichtwissen in ein „keiner"
	 *  umzudeuten. Sie verlangt nicht, ein Wissen in ein Nichtwissen
	 *  umzudeuten.
	 *
	 *  DIE SPUR IST KEINE NEUE DATENHALTUNG. Sie ist der Eintrag, den
	 *  `CV_Marker::no_secret_calls()` ohnehin fuehrt:
	 *  `array( 'count', 'last', 'provider' )`. Diese Funktion LIEST ihn
	 *  und schreibt nichts — der Riegel spricht, der Tresor zaehlt, und
	 *  diese hier benennt.
	 *
	 * @param int   $ai_user_id
	 * @param array $spur Der Zaehlereintrag, falls es einen gibt.
	 * @return string Nie leer.
	 */
	public static function label_or_trace( $ai_user_id, array $spur = array() ) {
		$benannt = self::label( $ai_user_id );

		/* Die Gruppe wird GEFRAGT und nicht hier zum zweiten Mal
		 * entschieden. Siehe den Block ueber gruppe(). */
		if ( self::GRUPPE_BESITZER === self::gruppe( $ai_user_id, $spur ) ) {
			return $benannt;
		}

		$wann = isset( $spur['last'] ) ? (int) $spur['last'] : 0;

		if ( $wann <= 0 ) {
			return sprintf(
				/* translators: %d: Kennung der Identität. */
				__( 'Identity %d · not determinable', 'cluster-one-pacioli' ),
				(int) $ai_user_id
			);
		}

		return sprintf(
			/* translators: 1: Kennung der Identität, 2: Anbieter-ID, 3: Datum des letzten Aufrufs. */
			__( 'Identity %1$d · %2$s · last on %3$s · the account no longer exists', 'cluster-one-pacioli' ),
			(int) $ai_user_id,
			isset( $spur['provider'] ) ? (string) $spur['provider'] : __( 'unknown provider', 'cluster-one-pacioli' ),
			date_i18n( get_option( 'date_format' ), $wann )
		);
	}

	// =====================================================================
	//  Absperrungen
	// =====================================================================

	/**
	 * KI-Konten können sich nicht anmelden.
	 */
	public static function block_login( $user ) {
		if ( $user instanceof WP_User && self::is_ai( $user ) ) {
			return new WP_Error(
				'pacioli_no_login',
				__( 'This account belongs to an AI and cannot log in.', 'cluster-one-pacioli' )
			);
		}

		return $user;
	}

	/**
	 * Keine Anwendungspasswörter für KI-Konten.
	 */
	public static function block_app_passwords( $available, $user ) {
		return self::is_ai( $user ) ? false : $available;
	}

	/**
	 * KI-Konten erscheinen nicht unter /wp/v2/users.
	 */
	public static function hide_from_rest( $args ) {
		$excluded = isset( $args['role__not_in'] ) ? (array) $args['role__not_in'] : array();

		$args['role__not_in'] = array_merge( $excluded, array( PACIOLI_ROLE_PERSONA, PACIOLI_ROLE_SYSTEM ) );

		return $args;
	}

	/**
	 * Keine Autorenarchive im Frontend.
	 */
	public static function block_author_archives( $query ) {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_author() ) {
			return;
		}

		$author = $query->get( 'author' );

		if ( $author && self::is_ai( (int) $author ) ) {
			$query->set_404();
			status_header( 404 );
		}
	}

	/**
	 * KI-Konten aus der allgemeinen Benutzerübersicht heraushalten.
	 *
	 * Sie werden in der eigenen Oberfläche des Plugins verwaltet; in der
	 * normalen Liste stiften sie nur Verwirrung und tauchen sonst in jeder
	 * Autoren-Auswahlbox auf.
	 */
	public static function hide_from_user_list( $query ) {
		global $pagenow;

		if ( ! is_admin() || 'users.php' !== $pagenow ) {
			return;
		}

		if ( isset( $_GET['pacioli_show_ai'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		global $wpdb;

		$roles = array( PACIOLI_ROLE_PERSONA, PACIOLI_ROLE_SYSTEM );

		foreach ( $roles as $role ) {
			$query->query_where .= $wpdb->prepare(
				" AND {$wpdb->users}.ID NOT IN (
					SELECT user_id FROM {$wpdb->usermeta}
					WHERE meta_key = %s AND meta_value LIKE %s
				)",
				$wpdb->get_blog_prefix() . 'capabilities',
				'%' . $wpdb->esc_like( '"' . $role . '"' ) . '%'
			);
		}
	}

	/**
	 * Die gespeicherten Schlüssel sind über get_user_meta() nicht lesbar.
	 *
	 * Auch nicht für Administratoren. Die einzigen Stellen, die an den Wert
	 * kommen, gehen über CV_Vault::read_shielded() am Filter vorbei.
	 *
	 * ---------------------------------------------------------------------
	 *  A6 — der Filter liefert fünf Argumente, angenommen wurden vier.
	 *
	 *  `$meta_type` kam nie an. Der Schild griff damit auch für Beitrags-
	 *  und Term-Meta desselben Namens — folgenlos, solange `_cv_secret`
	 *  niemandem sonst gehört, aber breiter als der Kommentar behauptete.
	 *  Kommentar und Code sagen jetzt dasselbe: nur Benutzer-Meta.
	 * ---------------------------------------------------------------------
	 */
	public static function shield_secret( $value, $object_id, $meta_key, $single, $meta_type = 'user' ) {
		if ( 'user' !== $meta_type ) {
			return $value;
		}

		if ( PACIOLI_META_SECRET !== $meta_key && PACIOLI_META_QUARANTINE !== $meta_key ) {
			return $value;
		}

		if ( Pacioli_Vault::is_unshielded() ) {
			return $value;
		}

		return $single ? '' : array();
	}

	// =====================================================================
	//  Datenschutz
	// =====================================================================

	/**
	 * Wird ein Mensch gelöscht, verschwinden seine KIs samt Schlüsseln.
	 */
	public static function delete_owned_identities( $user_id ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';

		foreach ( self::owned_by( $user_id ) as $ai_id ) {
			if ( (int) $ai_id === (int) $user_id ) {
				continue;
			}
			wp_delete_user( $ai_id );
		}
	}

	// =====================================================================
	//  W54 — die KI, deren Besitzer es nicht mehr gibt
	// =====================================================================
	//
	//  Der Haken oben ist richtig gebaut und aktiv. Er sieht nur nicht
	//  alles, und das ist keine Nachlässigkeit, sondern eine Grenze:
	//
	//      owned_by() findet eine KI ueber ZWEI Dinge zugleich — die Rolle
	//      UND `_cv_owner`. Fehlt oder aendert sich eines, findet der Haken
	//      sie nicht. Und `delete_user` feuert nicht auf jedem Weg: ein
	//      Eingriff in die Datenbank, ein halb eingespielter Stand, der
	//      Netzwerkbetrieb.
	//
	//      Ein Raeumer merkt nie, dass er einmal nicht geraeumt hat.
	//      Das merkt nur eine Pruefung beim Benutzen.
	//
	//  Also gibt es hier keine zweite Raeumung, sondern eine PRUEFUNG.
	//
	//  WAS SIE AUSDRUECKLICH NICHT TUT
	//
	//    - Sie versteckt nichts. Eine verwaiste KI, die verschwindet, ist
	//      die leere Huelle wieder: sie waere da und niemand saehe sie.
	//    - Sie loescht nichts beim Lesen.
	//
	//          Ein Lesevorgang, der loescht, ist ein Schreibweg mit einem
	//          harmlosen Namen.
	//
	//      Ein Datenbank-Schluckauf vernichtete sonst Schluessel, die in
	//      Ordnung waren — unwiederbringlich. Geraeumt wird nur auf einen
	//      Knopf, den ein Mensch drueckt, und der zaehlt vorher.

	/** Gezaehlte Faelle, in denen eine verwaiste KI benutzt werden sollte. */
	const OPTION_OWNER_GONE = 'pacioli_owner_gone';

	/**
	 * Ist der Besitzer dieser KI verschwunden?
	 *
	 * DREI ANTWORTEN, nicht zwei — dasselbe Muster wie `cv_marker_unbound`,
	 * und aus demselben Grund:
	 *
	 *   false   der Besitzer ist da
	 *   true    `_cv_owner` zeigt auf ein Konto, das es nicht gibt
	 *   null    NICHT MESSBAR — es steht ueberhaupt kein Besitzer da
	 *
	 *      Ein Feld, das nie gefuellt wurde, und eines, dessen Ziel
	 *      verschwunden ist, sehen in einem bool gleich aus. Der
	 *      Unterschied ist der zwischen einem alten Datensatz und einem
	 *      Verlust, und nur der zweite gehoert gezaehlt.
	 *
	 * @return bool|null
	 */
	public static function owner_gone( $ai_user_id ) {
		$owner = get_user_meta( (int) $ai_user_id, PACIOLI_META_OWNER, true );

		if ( '' === $owner || null === $owner ) {
			return null;
		}

		$owner = (int) $owner;

		if ( $owner <= 0 ) {
			return null;
		}

		// `get_userdata()` liest den Benutzer selbst, nicht eine Rolle und
		// nicht ein Meta. Damit haengt die Antwort an genau einer Tatsache.
		return false === get_userdata( $owner );
	}

	/**
	 * Wessen hinterlegte Schlüssel gehen beim Entfernen weg?
	 *
	 * =====================================================================
	 *  POSTEN B7, 10.10.2026 — MIT NAMEN UND ZAHL
	 * =====================================================================
	 *
	 *  Der Satz auf dem Schirm des SysOps soll nicht „Schlüssel gehen weg"
	 *  sagen, sondern WESSEN.
	 *
	 *      Man kann einen Verlust nicht verhindern, der in der Hand eines
	 *      anderen liegt. Man kann ihn nur vorher ankündigen, solange noch
	 *      Zeit ist, etwas zu retten.                            (Mammut)
	 *
	 *  GEZÄHLT WERDEN MENSCHEN, NICHT KIs. Drei KIs einer Person sind EIN
	 *  Mensch, der etwas verliert — und der Satz richtet sich an Menschen.
	 *
	 *      Eine Zahl, die KIs zählt, und ein Satz, der Personen bittet,
	 *      sind zwei Dinge mit einer Zahl davor.
	 *
	 *  GEFRAGT WIRD `has_secret()`, nicht gelesen. Diese Liste nennt
	 *  Namen; sie hat mit Schlüsseln nichts zu tun, außer dass sie weiß,
	 *  DASS es welche gibt.                                   (Teil 3 T1)
	 *
	 *  GESCHLEIFT wie `orphans()`: eine feste Obergrenze ließe den Rest
	 *  stehen, und „der Rest" wären die Menschen, die nicht gewarnt
	 *  werden.                                                       (A5)
	 *
	 *  BENANNTE GRENZE: eine KI ohne Besitzer (eine Waise) hat keinen
	 *  Menschen, der gewarnt werden könnte. Ihr Schlüssel geht trotzdem.
	 *  Die Zahl der WAISEN steht deshalb getrennt daneben — eine Waise
	 *  unter die Personen zu rechnen, hieße einen Empfänger behaupten.
	 *
	 * @return array array( 'menschen' => array( id => name ), 'waisen' => int, 'kis' => int )
	 */
	public static function wessen_schluessel_gehen() {
		$menschen = array();
		$waisen   = 0;
		$kis      = 0;
		$offset   = 0;
		$batch    = 200;

		do {
			$page = get_users(
				array(
					'role__in' => array( PACIOLI_ROLE_PERSONA, PACIOLI_ROLE_SYSTEM ),
					'fields'   => 'ID',
					'number'   => $batch,
					'offset'   => $offset,
					'orderby'  => 'ID',
					'order'    => 'ASC',
				)
			);

			foreach ( $page as $id ) {
				if ( ! Pacioli_Vault::has_secret( (int) $id ) ) {
					continue;
				}

				$kis++;

				$owner = get_userdata( (int) get_user_meta( (int) $id, PACIOLI_META_OWNER, true ) );

				if ( ! $owner ) {
					$waisen++;
					continue;
				}

				$menschen[ (int) $owner->ID ] = (string) $owner->display_name;
			}

			$offset += $batch;
		} while ( count( $page ) === $batch );

		return array(
			'menschen' => $menschen,
			'waisen'   => $waisen,
			'kis'      => $kis,
		);
	}

	/**
	 * Alle KIs, deren Besitzer verschwunden ist.
	 *
	 * Geschleift wie `owned_by()` (A5): eine feste Obergrenze liesse den
	 * Rest stehen, und gerade hier waere „der Rest" das, was niemand sieht.
	 */
	public static function orphans() {
		$aus    = array();
		$offset = 0;
		$batch  = 200;

		do {
			$page = get_users(
				array(
					'role__in' => array( PACIOLI_ROLE_PERSONA, PACIOLI_ROLE_SYSTEM ),
					'fields'   => 'ID',
					'number'   => $batch,
					'offset'   => $offset,
					'orderby'  => 'ID',
					'order'    => 'ASC',
				)
			);

			foreach ( $page as $id ) {
				if ( true === self::owner_gone( $id ) ) {
					$aus[] = (int) $id;
				}
			}

			$offset += $batch;
		} while ( count( $page ) === $batch );

		return $aus;
	}

	/**
	 * Ein Fall ist aufgetreten — gezaehlt, nicht geraeumt.
	 *
	 * Eigener Name (`cv_owner_gone`), NICHT die Leck-Zaehlung. Ein Leck ist
	 * eine Marke, die hinausgegangen ist; das hier ist ein Aufruf, der gar
	 * nicht erst zustande kam. Wer beides in eine Zahl wirft, kann die
	 * Zahl hinterher nicht mehr lesen.
	 */
	public static function note_owner_gone( $ai_user_id ) {
		$state = get_option( self::OPTION_OWNER_GONE, array() );
		$state = is_array( $state ) ? $state : array();

		$state['count'] = isset( $state['count'] ) ? (int) $state['count'] + 1 : 1;
		$state['last']  = time();
		$state['letzte_ki'] = (int) $ai_user_id;

		update_option( self::OPTION_OWNER_GONE, $state, false );
	}

	/** Was die Zaehlung sagt. */
	public static function owner_gone_state() {
		$state = get_option( self::OPTION_OWNER_GONE, array() );
		$state = is_array( $state ) ? $state : array();

		return array(
			'count'      => isset( $state['count'] ) ? (int) $state['count'] : 0,
			'last'       => isset( $state['last'] ) ? (int) $state['last'] : 0,
			'letzte_ki'  => isset( $state['letzte_ki'] ) ? (int) $state['letzte_ki'] : 0,
		);
	}

	/** Zaehlung zuruecksetzen. */
	public static function restore_owner_gone() {
		delete_option( self::OPTION_OWNER_GONE );
	}

	/**
	 * Datenexport: Die KIs werden genannt, der Schlüssel NICHT exportiert.
	 */
	public static function register_exporter( $exporters ) {
		$exporters['connector-vault'] = array(
			'exporter_friendly_name' => __( 'Connector Vault', 'cluster-one-pacioli' ),
			'callback'               => array( __CLASS__, 'export_personal_data' ),
		);

		return $exporters;
	}

	public static function export_personal_data( $email, $page = 1 ) {
		$user = get_user_by( 'email', $email );
		$data = array();

		if ( $user ) {
			foreach ( self::owned_by( $user->ID ) as $ai_id ) {
				$ai = get_userdata( $ai_id );

				$data[] = array(
					'group_id'    => 'pacioli_identities',
					'group_label' => __( 'Own AI identities', 'cluster-one-pacioli' ),
					'item_id'     => 'cv-identity-' . $ai_id,
					'data'        => array(
						array( 'name' => __( 'Name', 'cluster-one-pacioli' ), 'value' => $ai ? $ai->display_name : '' ),
						array( 'name' => __( 'Provider', 'cluster-one-pacioli' ), 'value' => get_user_meta( $ai_id, PACIOLI_META_PROVIDER, true ) ),
						array( 'name' => __( 'Model', 'cluster-one-pacioli' ), 'value' => get_user_meta( $ai_id, PACIOLI_META_MODEL, true ) ),
						array( 'name' => __( 'Access key', 'cluster-one-pacioli' ), 'value' => __( 'stored; not exported for security reasons', 'cluster-one-pacioli' ) ),
					),
				);
			}
		}

		return array( 'data' => $data, 'done' => true );
	}

	/**
	 * Traegt schon eine KI diesen Anzeigenamen?
	 *
	 * =====================================================================
	 *  Z4 — „Assistent" UND „assistent" SIND DERSELBE NAME.
	 *      Entschieden am 10.10.2026, und hier steht der Grund.
	 * =====================================================================
	 *
	 *  DREI GRUENDE, UND DER ERSTE IST DIE AUFGABE DES NAMENS:
	 *
	 *  1  Ein Name ist dazu da, zwei Dinge in einem SATZ auseinander-
	 *     zuhalten. Ein Unterschied, der beim Vorlesen verschwindet, ist
	 *     fuer diese Aufgabe keiner.
	 *
	 *         Zwei Namen, die man im Gespraech nicht unterscheiden kann,
	 *         sind im Gespraech ein Name.
	 *
	 *  2  DIE ANMELDUNG HAT ES SCHON SO ENTSCHIEDEN. `unique_login()` legt
	 *     `strtolower()` darueber — „Assistent" und „assistent" ergeben
	 *     dieselbe Grundform und weichen einander aus. Zwei verschiedene
	 *     Regeln fuer die Gleichheit EINES Namens waeren zwei Wahrheiten
	 *     darueber, was derselbe Name ist.                      (P1-12)
	 *
	 *  3  Der Gegengrund ist echt und waegt leichter: ein Mensch darf
	 *     seine KI schreiben, wie er will. Das bleibt auch so — WER ZUERST
	 *     KOMMT, BEHAELT SEINE SCHREIBWEISE. Abgewiesen wird nicht eine
	 *     Schreibweise, sondern ein zweiter Traeger desselben Namens.
	 *
	 *  WAS NICHT GEFALTET WIRD: Umlaute, Akzente, Bindestriche.
	 *  „Assistent" und „Assistént" bleiben zwei Namen. Das ist eine
	 *  benannte Grenze und kein Versehen — eine Faltung, die anfaengt,
	 *  Zeichen zu uebersetzen, braucht eine Tabelle, und eine Tabelle
	 *  braucht einen Pfleger.
	 *
	 *      Eine Regel, die man in einem Satz sagen kann, altert besser
	 *      als eine, die eine Liste braucht.
	 *
	 *  `trim()` kommt dazu, weil `sanitize_text_field()` ohnehin trimmt —
	 *  sonst waere „Claude " der Weg um den Riegel herum.
	 *
	 * @return array{id:int,name:string,provider:string}|null
	 */
	public static function name_belegt( $name ) {
		$gesucht = strtolower( trim( sanitize_text_field( (string) $name ) ) );

		if ( '' === $gesucht ) {
			return null;
		}

		$alle   = array();
		$offset = 0;
		$batch  = 200;

		do {
			$page = get_users(
				array(
					'role__in' => array( PACIOLI_ROLE_PERSONA, PACIOLI_ROLE_SYSTEM ),
					'fields'   => array( 'ID', 'display_name' ),
					'number'   => $batch,
					'offset'   => $offset,
					'orderby'  => 'ID',
					'order'    => 'ASC',
				)
			);

			foreach ( $page as $u ) {
				$alle[] = $u;
			}

			$offset += $batch;
		} while ( count( $page ) === $batch );

		foreach ( $alle as $u ) {
			if ( strtolower( trim( (string) $u->display_name ) ) === $gesucht ) {
				return array(
					'id'       => (int) $u->ID,
					'name'     => (string) $u->display_name,
					'provider' => (string) get_user_meta( (int) $u->ID, PACIOLI_META_PROVIDER, true ),
				);
			}
		}

		return null;
	}

	/**
	 * Erzeugt einen eindeutigen, nicht erratbaren Login-Namen.
	 */
	private static function unique_login( $name ) {
		$base = 'ai-' . sanitize_user( strtolower( $name ), true );
		$base = substr( $base ? $base : 'ai', 0, 40 );

		$login = $base;
		$i     = 1;

		while ( username_exists( $login ) ) {
			$login = $base . '-' . ( ++$i );
		}

		return $login;
	}
}
}
