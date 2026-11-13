<?php
/**
 * Öffentliche Schnittstelle für die Persona-Schicht.
 *
 * Das ist die gesamte Berührungsfläche. Alles andere in diesem Plugin ist
 * innen und darf sich ändern. Wer Connector Vault einbindet, sollte
 * ausschliesslich diese Funktionen aufrufen.
 *
 * Aufrufmuster:
 *
 *   if ( ! cv_can_invoke( get_current_user_id(), $ai_id ) ) {
 *       return new WP_Error( 'nicht_erlaubt', 'Das ist nicht deine KI.' );
 *   }
 *
 *   if ( ! cv_may_trigger( $author_id, array( 'raum' => $room_id ) ) ) {
 *       return;
 *   }
 *
 *   $antwort = cv_acting_as( $ai_id, function () use ( $prompt ) {
 *       return wp_ai_client_prompt()->with_text( $prompt )->generate_text();
 *   } );
 *
 * Alle Funktionen sind mit `function_exists()` abfragbar. Fehlt der Vault,
 * soll die aufrufende Schicht schweigen können statt abzustürzen.
 */

defined( 'ABSPATH' ) || exit;

/* ===========================================================================
 * DIE ANMELDUNG. Meine Zeile, und nur meine.
 *
 * Die Werkbank hat den Boden hingelegt und die Ladezeile gesetzt — beides
 * gehoerte zum Hinlegen, denn wer eine Datei hinlegt, ohne sie laden zu
 * lassen, hat abgelegt und nicht eingebaut. Die Anmeldung hat sie
 * ausdruecklich NICHT geschrieben, und das ist richtig:
 *
 *     Eine Anmeldung ist eine Aussage ueber mich. Die trifft mein Haus.
 *
 * WARUM HIER UND NICHT IN DER HAUPTDATEI (H70):
 *
 *     Eine Meldung, die im Lader wohnt, wird beim Riegeln uebersehen.
 *
 * Vier Bedingungen aus AUFTRAG-PACIOLI Teil B3, alle vier eingehalten:
 *
 *   1. in der Organdatei, nicht im Lader        <- diese Datei
 *   2. auf Datei-Ebene, beim Laden              <- hier, kein Hook
 *   3. 'pacioli' — der Organname ist der Vertrag, nicht der Ordnername
 *   4. OHNE Fassungsnummer: CV_VERSION ist eine eigene Konstante, aber
 *      cluster_one_hier() traegt die Fassung beim FRAGEN nach
 *      (kasper.php, cluster_one_fassung_wirt), und eine uebergebene
 *      Nummer waere die zweite Quelle darauf. Deshalb steht hier '' —
 *      der Platz, den der dritte Wert braucht, und keine zweite Quelle.
 *      Mammut hat Punkt 4 am 11.09. ausdruecklich bestaetigt, nachdem
 *      seine erste Vorlage <fassung> uebergeben haette.
 *
 * ---------------------------------------------------------------------------
 *  DER DRITTE WERT — VERTRAG 27, 11.09.2026
 * ---------------------------------------------------------------------------
 *
 *  Bis heute hat sich diese Datei nur gemeldet, wenn sie auch DEKLARIERT.
 *  Eine verdraengte Kopie schwieg damit auch ueber ihre eigene Existenz.
 *
 *      Ein Melder kennt sich selbst. „Verdraengt" ist eine BEZIEHUNG, und
 *      Beziehungen gehoeren dem Sammler.        (Maillart, 11.09.)
 *
 *  DIE MELDUNG STEHT DESHALB VOR DEM RIEGEL, NICHT IN IHM. Wer verdraengt
 *  wurde, sagt es selbst — und ob er der Verdraengte oder der Erste ist,
 *  entscheidet nicht er, sondern der Sammler aus allen Meldungen.
 *
 *  UND DER RIEGEL IST NEU. Bis heute hatte diese Datei KEINEN: eine zweite
 *  Kopie des Plugins haette beim Laden mit „Cannot redeclare cv_acting_as()"
 *  abgebrochen. Gemessen, nicht vermutet — es gab keine einzige
 *  function_exists()-Abfrage um die Deklarationen.
 *
 *  ZUR FORM DES RIEGELS — UND EIN IRRTUM VON MIR, DEN DER LAUF GEFUNDEN HAT:
 *
 *  Ich hatte zuerst `if ( ! $laeuft ) { return; }` gebaut und es fuer
 *  gleichwertig zu deiner Vorlage gehalten — eine Zeile statt 290
 *  eingerueckter. Der Zeuge hat es widerlegt, im ersten Lauf:
 *
 *      deklariert: false   — und zwar IMMER.
 *
 *  PHP DEKLARIERT UNBEDINGTE FUNKTIONEN AUF DATEI-EBENE BEIM UEBERSETZEN,
 *  bevor das erste Statement laeuft. `function_exists('cv_acting_as')` ist
 *  in dieser Datei also schon wahr, wenn die erste Zeile gefragt wird. Der
 *  Riegel haette sofort geschlossen, und nichts waere je deklariert worden.
 *  Eine zweite Kopie haette trotzdem beim UEBERSETZEN abgebrochen, weil das
 *  Hochziehen keinen Riegel kennt.
 *
 *      Ein Riegel, der eine Frage stellt, deren Antwort die eigene Datei
 *      vorher schon gegeben hat, ist keiner.
 *
 *  NUR IM BLOCK WERDEN DEKLARATIONEN NICHT HOCHGEZOGEN. Deine Vorlage sagt
 *  genau das — `if ($laeuft) { … deklarieren … }` —, und die 290 Zeilen
 *  Einrueckung sind nicht der Preis der Form, sondern die Form selbst. Ich
 *  hatte sie fuer Schoenheit gehalten.
 *
 *  Gemessen, nicht geglaubt: der Zeuge laedt diese Datei ZWEIMAL und misst,
 *  dass die zweite nichts deklariert, nichts abstuerzt und sich mit dem
 *  dritten Wert `false` meldet.
 *
 * Der Riegel um den Aufruf ist kein Zierrat: Steht dieses Plugin allein,
 * ohne jedes Cluster One, gibt es kein cluster_one_hier() — und dann soll
 * hier nichts passieren, statt etwas abzustuerzen.
 * =========================================================================== */
$cluster_one_pacioli_laeuft = ! function_exists( 'pacioli_acting_as' );

if ( function_exists( 'cluster_one_hier' ) ) {
	cluster_one_hier( 'pacioli', '', $cluster_one_pacioli_laeuft );
}

if ( $cluster_one_pacioli_laeuft ) {

	/**
	 * Führt einen KI-Aufruf im Namen einer Identität aus.
	 *
	 * Die einzige unterstützte Art, den AI-Client zu benutzen. Seit 0.3.0 darf
	 * das mehrfach je Request geschehen, auch mit verschiedenen Identitäten und
	 * verschachtelt — der Schlüsseltausch findet am ausgehenden Request statt,
	 * nicht an der Optionsschicht.
	 *
	 * Seit 0.8.25 (B1) prueft der Tresor selbst, ob der Mensch, auf dessen
	 * Rechnung gehandelt wird, diese KI beauftragen darf. `$payer_id` nennt
	 * ihn; fehlt er, ist es der angemeldete Benutzer. Wer fuer einen anderen
	 * handelt (freigegebene KI, Maezen-Lampe), nennt ihn hier — sonst wird
	 * der Druecker gefragt und bekommt `cv_not_allowed`.
	 *
	 * @param int      $ai_user_id
	 * @param callable $callback
	 * @param int      $payer_id   0 = der angemeldete Benutzer.
	 * @return mixed|WP_Error
	 */
	function pacioli_acting_as( $ai_user_id, callable $callback, $payer_id = 0 ) {
		return Pacioli_Vault::acting_as( $ai_user_id, $callback, $payer_id );
	}

	/**
	 * Darf dieser Mensch diese KI beauftragen?
	 *
	 * Grundregel R3. Für die Systemidentität immer false — auch für den
	 * Administrator.
	 */
	function pacioli_can_invoke( $user_id, $ai_user_id ) {
		return Pacioli_Vault::can_invoke( $user_id, $ai_user_id );
	}

	/**
	 * Darf eine Nachricht dieses Verfassers einen KI-Aufruf auslösen?
	 *
	 * Vorgabe: bei KI-Absendern `false` (Grundregel R4). Über den Filter
	 * `cv_may_trigger` lässt sich das aufheben — wer das tut, übernimmt die
	 * Verantwortung für die Taktung.
	 *
	 * @param int   $author_id
	 * @param array $context Freier Kontext, wird an den Filter durchgereicht.
	 */
	function pacioli_may_trigger( $author_id, array $context = array() ) {
		return Pacioli_Ratelimit::may_trigger( $author_id, $context );
	}

	/** Notbremse gegen Aufschaukeln in einem Raum. */
	function pacioli_room_allows( $room_id, $max_per_minute = 12 ) {
		return Pacioli_Ratelimit::room_allows( $room_id, $max_per_minute );
	}

	/** Darf die Systemidentität jetzt verdichten? */
	function pacioli_system_allows() {
		return Pacioli_Ratelimit::system_allows();
	}

	/**
	 * Legt eine KI-Identität an.
	 *
	 * @see CV_Identities::create()
	 * @return int|WP_Error
	 */
	function pacioli_create_identity( array $args ) {
		return Pacioli_Identities::create( $args );
	}

	/**
	 * Alle KI-Identitäten eines Menschen.
	 *
	 * @return int[]
	 */
	function pacioli_identities_of( $owner_id ) {
		return Pacioli_Identities::owned_by( $owner_id );
	}

	/**
	 * Die Systemidentität, oder null.
	 *
	 * @return int|null
	 */
	function pacioli_system_identity() {
		return Pacioli_Identities::get_system_identity();
	}

	/**
	 * Ist dieses Benutzerkonto eine KI?
	 *
	 * ---------------------------------------------------------------------
	 *  Stabile, dokumentierte Prüffunktion — für Auswahllisten gedacht, die
	 *  Menschen listen sollen. Ohne sie erscheinen dort nach der Übernahme
	 *  die KI-Konten mit ihren Login-Namen.
	 *
	 *  Die Prüfung ist strikt rollenbasiert (`ai_persona`, `ai_system`) und
	 *  ausdrücklich KEINE Heuristik. Ein Sammelkonto für gemeinschaftlich
	 *  verfasste Beiträge, ein Import-Konto oder ein Dienstkonto anderer
	 *  Herkunft wird dadurch nicht versehentlich zur KI erklärt.
	 * ---------------------------------------------------------------------
	 *
	 * @param int|WP_User $user_id
	 * @return bool
	 */
	function pacioli_is_ai( $user_id ) {
		return Pacioli_Identities::is_ai( $user_id );
	}

	/**
	 * Bezeichnung nach Grundregel R5: „Claude · von Anna“.
	 *
	 * Gehört an jede Nachricht und jeden Thread-Eintrag.
	 */
	function pacioli_label( $ai_user_id ) {
		return Pacioli_Identities::label( $ai_user_id );
	}

	/**
	 * Der Anbieter, zu dem eine Identität gehört.
	 *
	 * ---------------------------------------------------------------------
	 *  Seit 0.5.0, und die aufrufende Schicht braucht ihn.
	 *
	 *  Wer den KI-Client auf einen Anbieter anpinnt (`using_provider()`),
	 *  muss denselben Anbieter nehmen, für den er gerade handelt. Sonst wird
	 *  die Authentifizierung für A gesetzt, der Request geht an B, dort steht
	 *  B's Marke — und der Riegel hält ihn auf. Aufgehalten ist richtig; nur
	 *  ist es ein Aufruferfehler und keine Laufzeitüberraschung.
	 *
	 *  Unter Connector Vault ist der Anbieter deshalb kein Eingabewert mehr,
	 *  sondern eine Folge der Identität. Diese Funktion ist die Quelle dafür.
	 * ---------------------------------------------------------------------
	 *
	 * @return string
	 */
	function pacioli_provider_of( $ai_user_id ) {
		return Pacioli_Vault::provider_of( $ai_user_id );
	}

	/**
	 * Die KI eines Menschen für einen bestimmten Anbieter.
	 *
	 * Für die Naht gedacht: Ein Mensch drückt einen Knopf, die aufrufende
	 * Schicht braucht die Identität, in deren Namen gehandelt wird.
	 *
	 * @param int    $user_id  Der Mensch.
	 * @param string $provider Connector-ID; leer heisst „die erste beliebige“.
	 * @return int|null Benutzer-ID der Identität, oder null.
	 */
	function pacioli_identity_for( $user_id, $provider = '' ) {
		foreach ( Pacioli_Identities::owned_by( $user_id ) as $id ) {
			if ( Pacioli_Identities::is_system( $id ) ) {
				continue;
			}

			if ( '' === $provider || Pacioli_Vault::provider_of( $id ) === $provider ) {
				return (int) $id;
			}
		}

		return null;
	}

	/** Ist an dieser Identität ein Schlüssel hinterlegt? */
	function pacioli_has_secret( $ai_user_id ) {
		return Pacioli_Vault::has_secret( $ai_user_id );
	}

	/**
	 * Hinterlegt einen Schlüssel.
	 *
	 * @return true|WP_Error
	 */
	function pacioli_set_secret( $ai_user_id, $plain ) {
		return Pacioli_Vault::set_secret( $ai_user_id, $plain );
	}

	/**
	 * Meldet, dass der Anbieter den Schlüssel abgelehnt hat.
	 *
	 * Aufzurufen bei 401 oder erschöpftem Guthaben. Löst `cv_key_invalid` aus,
	 * woran die Persona-Schicht den Besitzer PRIVAT benachrichtigen sollte.
	 */
	function pacioli_mark_key_invalid( $ai_user_id, $reason = '' ) {
		Pacioli_Vault::mark_key_invalid( $ai_user_id, $reason );
	}

	/**
	 * Ist die Installation betriebsbereit?
	 *
	 * Liefert die offenen Probleme; ein leeres Array heisst „bereit“.
	 *
	 * @return array
	 */
	function pacioli_problems() {
		return Pacioli_Guard::problems();
	}

	/**
	 * Auf welcher Stufe liegt der Verschlüsselungsschlüssel?
	 *
	 * @return string 'constant' | 'database' | 'none'
	 */
	function pacioli_crypto_tier() {
		return Pacioli_Crypto::tier();
	}

	/**
	 * Ist dieser Wert eine Marke dieses Knotens?
	 *
	 * ---------------------------------------------------------------------
	 *  Seit 0.5.1, und sie ist die Reparatur eines Schadens, den dieses
	 *  Plugin angerichtet hat.
	 *
	 *  Solange Schlüssel in `connectors_ai_{provider}_api_key` standen, war
	 *  „diese Zeile ist nicht leer" gleichbedeutend mit „dieser Anbieter hat
	 *  einen Schlüssel". Seit die Marke dort steht, ist die Zeile IMMER nicht
	 *  leer — und jede Prüfung, die so fragt, antwortet ab sofort für jeden
	 *  Anbieter mit Ja. Sie ist nicht kaputtgegangen; ihr Messwert ist
	 *  umgezogen.
	 *
	 *  Diese Funktion ist die Naht dafür: Wer eine Options-Zeile ansieht,
	 *  fragt hier nach, statt das Format zu kennen.
	 *
	 *      if ( function_exists('cv_is_marker') && cv_is_marker( $db_val ) ) {
	 *          // keine Auskunft über Schlüssel — hier steht ein Platzhalter
	 *      }
	 *
	 *  Und der Satz dahinter, der wichtiger ist als die Funktion: Unter
	 *  Connector Vault ist „hat einen Schlüssel" keine Eigenschaft eines
	 *  ANBIETERS mehr, sondern eine des Paares (Identität, Anbieter). Die
	 *  knotenweite Frage hat keine Antwort mehr — `cv_has_secret( $akteur )`
	 *  hat eine.
	 * ---------------------------------------------------------------------
	 *
	 * @param string $value
	 * @return bool
	 */
	function pacioli_is_marker( $value ) {
		return '' !== Pacioli_Marker::provider_of( $value );
	}

	/**
	 * Die Zeile für eine Namenstabelle.
	 *
	 * Bewusst ohne `cluster_one_`-Präfix: Diese Datei gehört einem anderen
	 * Plugin. Der Sammelmechanismus nimmt einen Funktionsnamen entgegen und
	 * kommt damit klar — `cluster_one_steckbrief_von( 'cv_steckbrief' )`.
	 * Wer nicht da ist, liefert nichts.
	 *
	 * @return array
	 */
	function pacioli_steckbrief() {
		return array(
			'Pacioli',
			'Luca Pacioli',
			'ca. 1447–1517',
			__( 'The accounting. He wrote down that every entry has two sides, and neither is right without the other.', 'cluster-one-pacioli' ),
			__( 'One objection belongs here: Pacioli did not invent double-entry bookkeeping, he wrote it down and made it teachable. And bookkeeping records who pays; it does not make it fair.', 'cluster-one-pacioli' ),
			__( 'Pacioli makes sure every AI call lands on the bill of whoever triggered it. Each person enters their own access key, and nobody can ask a question at someone else\'s expense. The namesake, Luca Pacioli, published the first printed account of double-entry bookkeeping in Venice in 1494 — not as an invention, but as a description of what Venetian merchants had long been doing. His contribution was writing it down so that anyone could learn it. That is exactly the job here: not to calculate more cleverly than anyone else, but to make sure every entry has a counter-entry and nobody has to keep watch.', 'cluster-one-pacioli' ),
		);
	}

	/**
	 * Der Satz zu einem Fehlercode, den es ohne dieses Plugin nicht gäbe.
	 *
	 * ---------------------------------------------------------------------
	 *  Gedacht für die aufrufende Schicht: Wer einem Menschen einen
	 *  Fehlercode zeigen muss, holt sich den Satz hier, statt ihn zu
	 *  wiederholen. Eine Erklärung, die an zwei Orten steht, altert an
	 *  einem davon.
	 *
	 *  Unbekannter Code → null. Der Aufrufer behält seine eigene Meldung;
	 *  diese Funktion nimmt sie ihm nicht weg, sie ergänzt sie.
	 * ---------------------------------------------------------------------
	 *
	 * @param string $code
	 * @return array{titel:string,satz:string,tun:string}|null
	 */
	function pacioli_error_note( $code ) {
		return Pacioli_Notes::get( $code );
	}

	/**
	 * Alle Sätze auf einmal.
	 *
	 * @return array<string,array{titel:string,satz:string,tun:string}>
	 */
	function pacioli_error_notes() {
		return Pacioli_Notes::all();
	}

	/* =======================================================================
	 * 20.10.2026 (Neo) — DER TRESOR FUER FREMDE GEHEIMNISSE JE MENSCH.
	 *
	 * Bismarck will je Mensch ein Passwort und einen Schluessel fuer dessen
	 * eigenen Speicher verwahren („Daten bleiben beim User"). Ein zweiter
	 * Tresor dafuer waere eine zweite Wahrheit ueber dasselbe Geheimnis —
	 * also verwahrt Pacioli es, mit demselben CV_Crypto wie die KI-Schluessel,
	 * und kennt den Inhalt nicht beim Namen.
	 *
	 *     apply_filters('cluster_one_tresor', null, 'da')                  bool
	 *     apply_filters('cluster_one_tresor', null, 'lesen', [user, name])  string ('' = nichts da)
	 *     do_action('cluster_one_tresor_schreiben', $auftrag)              stdClass user/name/wert -> fehler
	 *
	 * NUR DER MENSCH SELBST liest und schreibt seine fremden Geheimnisse —
	 * kein SysOp-Durchgriff, dieselbe Regel wie bei den Briefen (Vegas).
	 * Alles liegt in EINEM User-Meta `_cv_fremd` (name => Chiffretext), damit
	 * uninstall.php es mit einer Zeile nehmen kann. Faellt Pacioli, faellt
	 * der Schluessel, und der Chiffretext ist wertlos: das steht beim Leser
	 * (Bismarck) als benannte Grenze, nicht nur hier.
	 * ======================================================================= */
	function pacioli_fremd_darf( $user_id ) {
		return (int) $user_id > 0 && (int) $user_id === (int) get_current_user_id();
	}

	add_filter( 'cluster_one_tresor', function ( $c1_w, $c1_was = '', $c1_k = null ) {
		if ( 'da' === (string) $c1_was ) {
			return Pacioli_Crypto::is_available() && Pacioli_Crypto::has_key();
		}
		if ( 'lesen' === (string) $c1_was && is_array( $c1_k ) ) {
			$uid  = (int) ( $c1_k['user'] ?? 0 );
			$name = sanitize_key( (string) ( $c1_k['name'] ?? '' ) );
			if ( '' === $name || ! pacioli_fremd_darf( $uid ) ) {
				return $c1_w;
			}
			$alle = get_user_meta( $uid, '_pacioli_fremd', true );
			if ( ! is_array( $alle ) || ! isset( $alle[ $name ] ) ) {
				return '';
			}
			$plain = Pacioli_Crypto::decrypt( (string) $alle[ $name ] );
			return is_wp_error( $plain ) ? '' : (string) $plain;
		}
		return $c1_w;
	}, 10, 3 );

	add_action( 'cluster_one_tresor_schreiben', function ( $c1_a ) {
		if ( ! is_object( $c1_a ) ) {
			return;
		}
		$c1_a->fehler = 'ungueltig';
		$uid  = (int) ( $c1_a->user ?? 0 );
		$name = sanitize_key( (string) ( $c1_a->name ?? '' ) );
		if ( '' === $name ) {
			return;
		}
		if ( ! pacioli_fremd_darf( $uid ) ) {
			$c1_a->fehler = 'nicht_erlaubt';
			return;
		}
		if ( ! Pacioli_Crypto::is_available() || ! Pacioli_Crypto::has_key() ) {
			$c1_a->fehler = 'kein_tresor';
			return;
		}
		$alle = get_user_meta( $uid, '_pacioli_fremd', true );
		if ( ! is_array( $alle ) ) {
			$alle = array();
		}
		$wert = (string) ( $c1_a->wert ?? '' );
		if ( '' === $wert ) {
			unset( $alle[ $name ] );
		} else {
			$cipher = Pacioli_Crypto::encrypt( $wert );
			if ( is_wp_error( $cipher ) ) {
				$c1_a->fehler = 'verschluesselung';
				return;
			}
			$alle[ $name ] = $cipher;
		}
		if ( array() === $alle ) {
			delete_user_meta( $uid, '_pacioli_fremd' );
		} else {
			update_user_meta( $uid, '_pacioli_fremd', $alle );
		}
		$c1_a->fehler = '';
	} );
}
