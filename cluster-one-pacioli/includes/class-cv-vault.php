<?php
/**
 * Wer handelt gerade, und welcher Schlüssel gilt für ihn?
 *
 * Massgeblich ist nicht, wer eingeloggt ist, sondern welche Identität
 * handelt. Ein Cron-Lauf hat keinen eingeloggten Benutzer, aber sehr wohl
 * eine handelnde Identität; ein Mensch, der seine Persona fragt, ist
 * eingeloggt, aber gehandelt wird im Namen der Persona.
 *
 * SEIT 0.3.0: Der Stapel bleibt, und das Umschalten je Aufruf lebt wieder.
 * Der Tausch passiert nicht mehr an der Optionsschicht (dort kommt er zu
 * spät, gemessen am 24.08.2026), sondern unmittelbar am ausgehenden
 * Request. Damit sind zwei Identitäten in einem Request wieder möglich —
 * etwa eine Persona-Antwort und deren anschliessende Übersetzung.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 *  DER RIEGEL UM DIE KLASSE — 12.09.2026, Weg C, dritte Schicht
 *
 *  Ohne ihn stirbt ein Knoten mit ZWEI Produktordnern an
 *  `Cannot redeclare class CV_Vault`, sobald der Lader seine EIGENEN Dateien
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
 *  gemessen: 31 Bytes, 31 Tabulatoren, sonst nichts. Byte-genau ist damit
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
if ( ! class_exists( 'Pacioli_Vault' ) ) {
class Pacioli_Vault {

	/** @var int[] Stapel der handelnden Identitäten. */
	private static $stack = array();

	/** @var int Zähler für den Schutzschild um die Meta-Werte. */
	private static $unshielded = 0;

	/**
	 * Führt einen Rückruf im Namen einer KI-Identität aus.
	 *
	 * ---------------------------------------------------------------------
	 *  B1 — 18.10.2026 (Neo): DIE GRUNDREGEL WIRD HIER GEPRUEFT, NICHT NUR
	 *  ERBETEN.
	 *
	 *  Bis 0.8.24 fragte diese Methode nur „ist das eine KI, ist sie
	 *  aktiv". Wer der Besitzer ist, fragte sie nicht; das war Sache der
	 *  aufrufenden Schicht (`cv_can_invoke`, INTEGRATION.md 3.2). Jede
	 *  Aufrufstelle, die die Frage vergass, gab fremdes Geld aus — und
	 *  der Schluesseltresor selbst haette es nicht bemerkt.
	 *
	 *      Eine Grundregel, die man nicht fragt, ist eine Bitte.
	 *                                                   (kern.php, 3b)
	 *
	 *  Jetzt fragt der Tresor selbst: fuer wen laeuft dieser Aufruf, und
	 *  darf der diese KI beauftragen? `$payer_id` ist dieser Mensch; fehlt
	 *  er, ist es der angemeldete Benutzer. Die Antwort gibt `can_invoke()`
	 *  — dieselbe Stelle wie bisher, mit der Maezen-Lampe (`cv_can_invoke`)
	 *  darin. Zwei Leser, ein Entscheider.
	 *
	 *  DIE SYSTEMIDENTITAET BLEIBT AUSSEN VOR, und das ist kein Loch: sie
	 *  hat keinen Besitzer, `can_invoke()` sagt fuer sie immer nein, und
	 *  ihr Betrieb ist unbeaufsichtigt — ihre Grenze ist `CV_Ratelimit`,
	 *  nicht ein Mensch.
	 *
	 *  BENANNTE GRENZE: PHP kennt keine Zugriffskontrolle zwischen Plugins.
	 *  Wer `$payer_id` luegt, luegt. Diese Pruefung faengt die Aufrufstelle,
	 *  die vergisst, nicht die, die betruegt — gegen die hilft auf einem
	 *  WordPress-Knoten nichts, was in PHP steht.
	 * ---------------------------------------------------------------------
	 *
	 * @param int      $ai_user_id
	 * @param callable $callback
	 * @param int      $payer_id   Der Mensch, auf dessen Rechnung gehandelt
	 *                             wird. 0 = der angemeldete Benutzer.
	 * @return mixed|WP_Error
	 */
	public static function acting_as( $ai_user_id, callable $callback, $payer_id = 0 ) {
		$ai_user_id = (int) $ai_user_id;

		if ( ! Pacioli_Identities::is_ai( $ai_user_id ) ) {
			return new WP_Error( 'pacioli_not_an_identity', __( 'No valid AI identity was given.', 'cluster-one-pacioli' ) );
		}

		$status = get_user_meta( $ai_user_id, PACIOLI_META_STATUS, true );

		if ( PACIOLI_STATUS_ACTIVE !== $status ) {
			return new WP_Error( 'pacioli_identity_unavailable', self::status_message( $status ), array( 'status' => $status ) );
		}

		if ( ! Pacioli_Identities::is_system( $ai_user_id ) ) {
			$payer_id = (int) $payer_id;

			if ( $payer_id <= 0 ) {
				$payer_id = (int) get_current_user_id();
			}

			if ( ! self::can_invoke( $payer_id, $ai_user_id ) ) {
				return new WP_Error(
					'pacioli_not_allowed',
					__( 'This AI may not be commissioned on this person’s account. Only the owner, or someone the owner has explicitly lent it to, may call it.', 'cluster-one-pacioli' ),
					array( 'payer' => $payer_id, 'identity' => $ai_user_id )
				);
			}
		}

		self::$stack[] = $ai_user_id;

		$state = null;

		try {
			// Der dokumentierte Weg: die Authentifizierung dieses Anbieters
			// für die Dauer des Aufrufs auf den Schlüssel des Handelnden
			// setzen. Muss NACH dem Aufstapeln geschehen — secret_for()
			// gibt nur heraus, während im Namen dieser Identität gehandelt
			// wird (A1).
			$provider = (string) get_user_meta( $ai_user_id, PACIOLI_META_PROVIDER, true );
			$secret   = self::secret_for( $ai_user_id, $provider );

			if ( '' !== $secret ) {
				$state = Pacioli_Registry::override( $provider, $secret );
			}

			$result = $callback();
		} finally {
			// Zuerst zurücksetzen, dann abstapeln: Bei verschachtelten
			// Kontexten gilt danach wieder die Authentifizierung des
			// äusseren Aufrufs.
			Pacioli_Registry::restore( $state );
			array_pop( self::$stack );
		}

		return $result;
	}

	/**
	 * Welche Identität handelt gerade?
	 *
	 * @return int|null
	 */
	public static function acting_identity() {
		return self::$stack ? (int) end( self::$stack ) : null;
	}

	/**
	 * Für welchen Anbieter wird gerade gehandelt?
	 *
	 * Gebraucht vom Riegel, um zwei Fehlerbilder zu trennen, die sonst
	 * gleich aussehen: die Marke des eigenen Anbieters (ein Leck) und die
	 * eines fremden (erwartbar, siehe CV_Marker::bolt()).
	 *
	 * @return string Leerer String, wenn nichts gebunden ist.
	 */
	public static function acting_provider() {
		$identity = self::acting_identity();

		if ( ! $identity ) {
			return '';
		}

		return (string) get_user_meta( $identity, PACIOLI_META_PROVIDER, true );
	}

	/**
	 * Darf $user_id die KI $ai_user_id beauftragen?
	 *
	 * Grundregel R3. Die Systemidentität ist von niemandem beauftragbar,
	 * auch nicht vom Administrator.
	 */
	public static function can_invoke( $user_id, $ai_user_id ) {
		$user_id    = (int) $user_id;
		$ai_user_id = (int) $ai_user_id;

		if ( ! Pacioli_Identities::is_ai( $ai_user_id ) || Pacioli_Identities::is_system( $ai_user_id ) ) {
			return false;
		}

		if ( ! user_can( $user_id, PACIOLI_CAP ) ) {
			return false;
		}

		$owner   = (int) get_user_meta( $ai_user_id, PACIOLI_META_OWNER, true );
		$allowed = ( $owner > 0 && $owner === $user_id );

		/*
		 * W54, zweite Stelle. Ein verwaister Besitzer faellt hier ohnehin
		 * durch — kein lebender Mensch hat die Nummer eines geloeschten.
		 * Es steht trotzdem ausdruecklich da, denn `$allowed` geht gleich
		 * durch einen Filter, und der darf genau DAS nicht aufheben.
		 *
		 *     Die Maezen-Lampe erlaubt einem Besitzer, seine KI jemandem
		 *     zu leihen. Sie erlaubt nicht, eine KI zu benutzen, die
		 *     keinen Besitzer mehr hat — dann traegt niemand die Rechnung.
		 */
		if ( true === Pacioli_Identities::owner_gone( $ai_user_id ) ) {
			return false;
		}

		/**
		 * Darf dieser Mensch diese KI beauftragen?
		 *
		 * Vorgabe seit jeher: nur der Besitzer (R3). Der Filter ist seit
		 * 0.5.0 da und existiert für genau einen Fall — dass ein Besitzer
		 * seine KI bewusst jemand anderem zur Verfügung stellt und die
		 * Rechnung übernimmt. Eine „Mäzen-Lampe".
		 *
		 * Das bricht R3 nicht, solange drei Dinge gelten, und die zu
		 * gewährleisten ist Sache der Schicht, die den Filter setzt:
		 *
		 *   - Der Besitzer entscheidet es, nicht der Betreiber und nicht
		 *     der Nutzniesser.
		 *   - Es ist sichtbar, wessen Rechnung läuft — `cv_label()` sagt
		 *     das ohnehin an jeder Ausgabe.
		 *   - Es ist widerrufbar, und zwar sofort.
		 *
		 * Wer den Filter benutzt, um die Prüfung generell abzuschalten,
		 * hat kein Mäzenatentum gebaut, sondern die Kostentrennung entfernt.
		 *
		 * @param bool $allowed
		 * @param int  $user_id
		 * @param int  $ai_user_id
		 * @param int  $owner
		 */
		return (bool) apply_filters( 'pacioli_can_invoke', $allowed, $user_id, $ai_user_id, $owner );
	}

	/**
	 * Der Anbieter, zu dem eine Identität gehört.
	 *
	 * Seit 0.5.0 öffentlich, weil die aufrufende Schicht ihn braucht: Wer
	 * einen Anbieter anpinnt, muss denselben nehmen, für den er handelt.
	 */
	public static function provider_of( $ai_user_id ) {
		return (string) get_user_meta( (int) $ai_user_id, PACIOLI_META_PROVIDER, true );
	}

	/**
	 * Liefert den entschlüsselten Schlüssel einer Identität.
	 *
	 * ---------------------------------------------------------------------
	 *  A1 — der Riegel, der in 0.1.0 fehlte.
	 *
	 *  `class-cv-crypto.php` sagt, der Schutz gelte „gegen einen
	 *  Datenbank-Abzug und gegen andere Plugins". Der Schutzschild über
	 *  `get_metadata` setzte das an der Vordertür durch — und diese Methode
	 *  stand daneben offen: `public static`, ohne Prüfung, Klartext im
	 *  Rückgabewert. PHP kennt keine Zugriffskontrolle zwischen Plugins;
	 *  ein beliebiges anderes Plugin konnte
	 *  `CV_Vault::secret_for( 42 )` schreiben und hatte Annas Schlüssel.
	 *
	 *  Damit war die zentrale Zusicherung des Plugins unwahr — eine Regel,
	 *  die als Kommentar dastand, wo sie als Riegel hingehörte.
	 *
	 *  Der Schlüssel ist jetzt nur erreichbar, WÄHREND im Namen genau
	 *  dieser Identität gehandelt wird. Das war ohnehin die Regel des
	 *  Entwurfs; sie wurde nur erwartet statt durchgesetzt.
	 * ---------------------------------------------------------------------
	 *
	 * @param int    $ai_user_id
	 * @param string $provider Erwarteter Anbieter; leer heisst „egal“.
	 * @return string
	 */
	public static function secret_for( $ai_user_id, $provider = '' ) {
		$ai_user_id = (int) $ai_user_id;

		if ( ! $ai_user_id || self::acting_identity() !== $ai_user_id ) {
			return '';
		}

		// Die Identität muss zum angefragten Anbieter passen. Sonst gäbe
		// eine OpenAI-Persona ihren Schlüssel bei einem Anthropic-Aufruf
		// heraus — an einen Anbieter, für den er nicht bestimmt ist.
		if ( '' !== $provider ) {
			$own = (string) get_user_meta( $ai_user_id, PACIOLI_META_PROVIDER, true );

			if ( $own !== $provider ) {
				return '';
			}
		}

		/*
		 * W54 — DIE PRUEFUNG BEIM BENUTZEN, 09.09.2026.
		 *
		 * Ist der Besitzer dieser KI verschwunden, wird ihr Schluessel nicht
		 * mehr herausgegeben. Der Aufruf kommt damit nicht zustande: ohne
		 * Schluessel gibt es kein `override()`, die Marke reist hinaus, und
		 * der Riegel in `pre_http_request` haelt sie auf. Es geht nichts
		 * Echtes hinaus und nichts Wertloses durch.
		 *
		 * WARUM HIER UND NICHT IN acting_as():
		 *
		 *   `acting_as()` steht seit August unter „kein Byte". Der Auftrag
		 *   von heute hebt das nicht auf — er hat es fuer uninstall.php
		 *   getan und fuer nichts sonst. `secret_for()` ist der engere
		 *   Punkt und steht nicht auf der Liste.
		 *
		 *       Eine Auflage, die einem gerade im Weg steht, ist keine, die
		 *       man sich selbst wegnimmt.
		 *
		 *   Der Preis ist eine Ungenauigkeit, und sie gehoert benannt: der
		 *   Riegel meldet danach „kein Schluessel hinterlegt", nicht „der
		 *   Besitzer ist weg". Die ZAHL sagt es (cv_owner_gone), die
		 *   Meldung noch nicht. Ein Satz in acting_as() waere genauer und
		 *   braucht ein Wort von der Werkbank.
		 *
		 * GEZAEHLT WIRD, GERAEUMT NICHT. Ein Lesevorgang, der loescht, ist
		 * ein Schreibweg mit einem harmlosen Namen.
		 */
		if ( true === Pacioli_Identities::owner_gone( $ai_user_id ) ) {
			Pacioli_Identities::note_owner_gone( $ai_user_id );

			return '';
		}

		$stored = self::read_shielded( $ai_user_id, PACIOLI_META_SECRET );

		if ( ! $stored ) {
			return '';
		}

		$plain = Pacioli_Crypto::decrypt( $stored );

		return is_wp_error( $plain ) ? '' : $plain;
	}

	/**
	 * Der Schlüssel für die AUSFUHR — nur an seinen Besitzer, nie an den Betreiber.
	 *
	 * =====================================================================
	 *  WARUM ES DIESE ZWEITE TÜR GIBT — Posten B6, 10.10.2026
	 * =====================================================================
	 *
	 *  `secret_for()` gibt nur heraus, WÄHREND im Namen dieser Identität
	 *  gehandelt wird. Für eine Ausfuhr ist das der falsche Rahmen:
	 *  `acting_as()` setzt die Authentifizierung des Anbieters um
	 *  (`CV_Registry::override()`), also würde ein Export tun, als ginge
	 *  ein Aufruf hinaus.
	 *
	 *      Ein Export, der einen Aufruf vortäuscht, um an einen Schlüssel
	 *      zu kommen, ist kein Export. Er ist ein Aufruf mit einem
	 *      anderen Namen.
	 *
	 *  ZWEI LESER, ZWEI VERSCHIEDENE FRAGEN — und deshalb kein P1-12:
	 *
	 *      secret_for()         darf die HANDELNDE Identität ihren
	 *                           Schlüssel benutzen?
	 *      secret_for_owner()   darf dieser MENSCH seinen eigenen
	 *                           Schlüssel mitnehmen?
	 *
	 *  DER RIEGEL IST ENGER ALS SONST IM HAUS: nur der Besitzer, und
	 *  `manage_options` hilft ausdrücklich NICHT.
	 *
	 *      „Niemand sieht fremde Zugangsschlüssel — auch Sie nicht."
	 *      Der Satz steht auf „Meine KIs", und eine Ausfuhr, die dem
	 *      Betreiber offensteht, nimmt ihn zurück.
	 *
	 *  UND DER WERT GEHT NICHT AUF EINEN SCHIRM. Der einzige Aufrufer ist
	 *  `CV_Ausfuhr::packen()`, und von dort geht er verschlüsselt in eine
	 *  Datei. `zeuge-ausfuhr.php` A4 misst, dass es genau EINEN Aufrufer
	 *  gibt.
	 *
	 *      Eine Tür, von der niemand zählt, wer durch sie geht, ist keine
	 *      Tür. Sie ist eine Öffnung.
	 *
	 *  KEIN VERWAISTER SCHLÜSSEL: ist der Besitzer weg, gibt es keinen
	 *  Menschen, der ausführen könnte — der Riegel unten greift dann von
	 *  selbst, weil `get_current_user_id()` niemals der gelöschte Besitzer
	 *  sein kann.
	 *
	 * @param int $ai_user_id
	 * @return string Leer, wenn es nichts gibt oder der Fragende nicht der Besitzer ist.
	 */
	public static function secret_for_owner( $ai_user_id ) {
		$ai_user_id = (int) $ai_user_id;

		if ( ! $ai_user_id || ! Pacioli_Identities::is_ai( $ai_user_id ) ) {
			return '';
		}

		$owner = (int) get_user_meta( $ai_user_id, PACIOLI_META_OWNER, true );
		$me    = (int) get_current_user_id();

		if ( ! $owner || ! $me || $owner !== $me ) {
			return '';
		}

		$stored = self::read_shielded( $ai_user_id, PACIOLI_META_SECRET );

		if ( ! $stored ) {
			return '';
		}

		$plain = Pacioli_Crypto::decrypt( $stored );

		return is_wp_error( $plain ) ? '' : (string) $plain;
	}

	/**
	 * Liest einen geschützten Meta-Wert am Schutzschild vorbei.
	 *
	 * @internal Nur für den Vault selbst und CV_Marker.
	 */
	public static function read_shielded( $user_id, $meta_key ) {
		self::$unshielded++;
		$value = get_user_meta( (int) $user_id, $meta_key, true );
		self::$unshielded--;

		return $value;
	}

	/**
	 * Hinterlegt einen Schlüssel an einer Identität.
	 *
	 * @return true|WP_Error
	 */
	public static function set_secret( $ai_user_id, $plain ) {
		$ai_user_id = (int) $ai_user_id;

		if ( ! Pacioli_Identities::is_ai( $ai_user_id ) ) {
			return new WP_Error( 'pacioli_not_an_identity', __( 'No valid AI identity was given.', 'cluster-one-pacioli' ) );
		}

		$plain = trim( (string) $plain );

		if ( '' === $plain ) {
			delete_user_meta( $ai_user_id, PACIOLI_META_SECRET );

			return true;
		}

		$cipher = Pacioli_Crypto::encrypt( $plain );

		if ( is_wp_error( $cipher ) ) {
			return $cipher;
		}

		update_user_meta( $ai_user_id, PACIOLI_META_SECRET, $cipher );

		if ( PACIOLI_STATUS_KEY_INVALID === get_user_meta( $ai_user_id, PACIOLI_META_STATUS, true ) ) {
			update_user_meta( $ai_user_id, PACIOLI_META_STATUS, PACIOLI_STATUS_ACTIVE );
		}

		return true;
	}

	/** Ist an dieser Identität ein Schlüssel hinterlegt? */
	public static function has_secret( $ai_user_id ) {
		return ! empty( self::read_shielded( (int) $ai_user_id, PACIOLI_META_SECRET ) );
	}

	/**
	 * Meldet eine Identität als „Schlüssel ungültig“.
	 */
	public static function mark_key_invalid( $ai_user_id, $reason = '' ) {
		$ai_user_id = (int) $ai_user_id;

		update_user_meta( $ai_user_id, PACIOLI_META_STATUS, PACIOLI_STATUS_KEY_INVALID );

		do_action( 'pacioli_key_invalid', $ai_user_id, (int) get_user_meta( $ai_user_id, PACIOLI_META_OWNER, true ), $reason );
	}

	/**
	 * Ist der Schutzschild gerade offen?
	 *
	 * @internal Nur für CV_Identities::shield_secret().
	 */
	public static function is_unshielded() {
		return self::$unshielded > 0;
	}

	/** Lesbare Begründung, warum eine Identität nicht verfügbar ist. */
	public static function status_message( $status ) {
		switch ( $status ) {
			case PACIOLI_STATUS_KEY_INVALID:
				return __( 'The stored access key was refused by the provider. Please enter a new one.', 'cluster-one-pacioli' );

			case PACIOLI_STATUS_PAUSED:
				return __( 'This AI is paused.', 'cluster-one-pacioli' );

			default:
				return __( 'This AI is currently unavailable.', 'cluster-one-pacioli' );
		}
	}
}
}
