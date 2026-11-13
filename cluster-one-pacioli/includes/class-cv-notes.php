<?php
/**
 * Die Sätze zu den Fehlercodes, die man wegen dieses Plugins zu sehen bekommt.
 *
 * =====================================================================
 *  Warum es diese Datei gibt
 * =====================================================================
 *
 *  Wer Connector Vault installiert, sieht Fehler, die vorher nicht kamen.
 *  Nicht weil etwas kaputtgegangen wäre, sondern weil vorher etwas
 *  durchgelassen wurde, das nicht zurechenbar war.
 *
 *  Der deutlichste Fall ist der ungepinnte Notweg. Ohne dieses Plugin
 *  darf ein Aufruf, dessen Anbieter-Pin nicht greift, ungepinnt
 *  hinausgehen — solange nur genau ein Anbieter einen Schlüssel hat, ist
 *  „irgendein Anbieter" derselbe Anbieter, und die Zuschreibung bleibt
 *  wahr. Mit diesem Plugin stimmt das nicht mehr: Die Authentifizierung
 *  ist für GENAU EINEN Anbieter gesetzt, und wählt das SDK einen
 *  anderen, geht eine Marke hinaus statt eines Schlüssels.
 *
 *      Ein Verhalten, das von einer Konfiguration abhängt, die sich
 *      morgen ändern kann, ist keine Zusicherung.
 *
 *  Der Aufruf scheitert also, wo er vorher zufällig gelang.
 *
 *      Das ist kein Rückschritt. Das ist die Ehrlichkeit, die vorher
 *      nicht bezahlt wurde.
 *
 *  Nur: Ein Mensch, der plötzlich „pin_failed" liest, weiss davon
 *  nichts. Ein Fehlercode ohne Satz ist eine Zumutung mit Nummer.
 *
 * =====================================================================
 *  Was hier NICHT steht
 * =====================================================================
 *
 *  Keine Kopie fremder Logik. Diese Sätze erklären ausschliesslich die
 *  FOLGE dieses Plugins — nicht, wie die aufrufende Schicht ihre Codes
 *  vergibt. Wo ein Code einem anderen Haus gehört, sagt der Satz, was
 *  Connector Vault damit zu tun hat, und sonst nichts.
 *
 *  DREI CODES GEHOEREN NICHT DIESEM KNOTEN, SONDERN EINER SCHICHT DARUEBER.
 *  `pin_failed`, `no_akteur` und `akteur_keine_person` entstehen in der Schicht,
 *  die KI beauftragt — Cluster One etwa. Liegt auf dem Knoten nur Connector
 *  Vault, koennen sie nicht auftreten, und ihre Saetze sprechen von einem
 *  Vorher, das es dort nie gab („vor Connector Vault durfte so ein Aufruf
 *  trotzdem hinausgehen").
 *
 *  Bis 0.7.2 stand das nur hier im Kopf. Auf der Seite standen alle zehn
 *  gleichrangig untereinander, und wer nur dieses Plugin installiert hatte,
 *  las Erklaerungen fuer Fehler, die er nie sehen wird.
 *
 *      Eine Auskunft, die eine Nachbarschaft voraussetzt, muss sie nennen —
 *      sonst ist sie nur dort wahr, wo die Nachbarn schon stehen.
 *
 *  Gefunden mit der Frage aus Rundbrief 06 §6. Seit 0.7.3 tragen die drei
 *  ein 'nur_mit_schicht' und die Seite sagt es dazu.
 *
 *  Und ein Code fehlt bewusst: `no_key`. Er gehört einer fremden
 *  Prüfung, deren Grundlage sich unter ihr gerade bewegt (die
 *  Options-Zeile trägt jetzt eine Marke). Einen Satz zu einem Code zu
 *  schreiben, dessen Bedeutung gerade verhandelt wird, hiesse eine
 *  zweite Wahrheit anzulegen. Er kommt dazu, wenn die erste steht.
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 *  DER RIEGEL UM DIE KLASSE — 12.09.2026, Weg C, dritte Schicht
 *
 *  Ohne ihn stirbt ein Knoten mit ZWEI Produktordnern an
 *  `Cannot redeclare class CV_Notes`, sobald der Lader seine EIGENEN Dateien
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
if ( ! class_exists( 'Pacioli_Notes' ) ) {
	class Pacioli_Notes {

		/**
		 * Alle Sätze.
		 *
		 * @return array<string,array{titel:string,satz:string,tun:string}>
		 */
		public static function all() {
			return array(

				// --- Codes der aufrufenden Schicht, ausgelöst durch uns -------

				'pin_failed' => array(
					'titel' => __( 'The provider could not be pinned down', 'cluster-one-pacioli' ),
					'satz'  => __( 'Before Connector Vault, such a call was still allowed out if the node had only a single provider with a key — then "some provider" was the same one. Not any more: the key is set for exactly one AI, and if the client picks a different provider, something worthless would go out. So the call would rather stop.', 'cluster-one-pacioli' ),
					'tun'   => __( 'Check whether the AI being addressed belongs to the provider it is meant to address — and whether a key is stored for it.', 'cluster-one-pacioli' ),
					'nur_mit_schicht' => true,
				),

				'no_akteur' => array(
					'titel' => __( 'The call belongs to nobody', 'cluster-one-pacioli' ),
					'satz'  => __( 'It was not passed in whose name the action is taken. Without a name there is no bill the call belongs to — and taking someone else\'s is exactly what this plugin prevents.', 'cluster-one-pacioli' ),
					'tun'   => __( 'This is a fault in the calling layer, not in the setup. It belongs in the log, not in the interface.', 'cluster-one-pacioli' ),
					'nur_mit_schicht' => true,
				),

				/* =====================================================
				 *  POSTEN 2, 10.10.2026 — DER CODE WAR UMBENANNT.
				 * =====================================================
				 *
				 *  Hier stand `akteur_mismatch` mit dem Satz „Der Aufruf
				 *  war auf einen Anbieter festgenagelt, die handelnde KI
				 *  gehoert aber zu einem anderen".
				 *
				 *  DEN CODE SENDET DER BODEN SEIT 0.2.40 NICHT MEHR. Er
				 *  heisst `akteur_keine_person`, und seine Bedeutung ist
				 *  eine ANDERE: der Aufruf nannte eine KI statt einer
				 *  Person.
				 *
				 *  Also kein Umbenennen, sondern ein TAUSCH — Code und
				 *  Satz. Ein umbenannter Code mit dem alten Satz waere die
				 *  schlechtere Haelfte von beidem.
				 *
				 * -----------------------------------------------------
				 *  GEMESSEN, UND DIE MESSUNG WIDERSPRICHT DEM AUFTRAG
				 *  NICHT, SIE BEGRENZT IHN:                (R-TRAEGER-1)
				 *
				 *      kern.php (Boden 1.9.9)        akteur_mismatch: 1
				 *      kern-NEU-1_12_9.php           akteur_mismatch: 1
				 *      beide                         akteur_keine_person: 0
				 *
				 *  Die Umbenennung ist in Boden 0.2.40 gefahren, und 0.2.40
				 *  liegt nicht in meinem Fach. Dieser Tausch ist also aus
				 *  dem Auftrag uebernommen und nicht gemessen — gemeldet,
				 *  nicht verschwiegen.
				 *
				 *  BENANNTE GRENZE: ein Knoten mit einem Boden VOR 0.2.40
				 *  sendet weiter `akteur_mismatch`, und dafuer steht ab
				 *  jetzt kein Satz hier. Das ist Absicht und nicht
				 *  Nachlaessigkeit: ein Verzeichnis, das Codes erklaert,
				 *  die kein Haus mehr sendet, ist genau der Fehler, der
				 *  hier gerade behoben wird.
				 *
				 *      Zwei Saetze fuer zwei Fassungen eines Codes sind
				 *      kein Dienst am Leser. Sie sind die Behauptung, dass
				 *      beide Fassungen im Umlauf sein duerfen.
				 * ===================================================== */
				'akteur_keine_person' => array(
					'titel' => __( 'The call named an AI instead of a person', 'cluster-one-pacioli' ),
					'satz'  => __( 'Whose name the action is taken in must be a person — somebody who holds a key and carries a bill. Here an AI was named instead. An AI cannot own an AI, and it has no bill of its own, so there is nothing to insert and the call stops.', 'cluster-one-pacioli' ),
					'tun'   => __( 'This is a fault in the calling layer, not in the setup. Pass the owner, not the AI that is speaking.', 'cluster-one-pacioli' ),
					'nur_mit_schicht' => true,
				),

				// --- Eigene Codes --------------------------------------------

				'pacioli_shared_key_present' => array(
					'titel' => __( 'There is still a shared key', 'cluster-one-pacioli' ),
					'satz'  => __( 'For at least one provider the key comes from an environment variable or a constant in wp-config.php. Both beat the database; no filter gets past them, and everyone would be working on the same bill. As long as that is the case, every call is refused.', 'cluster-one-pacioli' ),
					'tun'   => __( 'Remove the variable or constant. Everything runs again by itself afterwards.', 'cluster-one-pacioli' ),
				),

				'pacioli_no_identity' => array(
					'titel' => __( 'Nobody is acting', 'cluster-one-pacioli' ),
					'satz'  => __( 'The AI client was addressed outside any context. In normal operation this happens during a connection check on a provider settings page, for instance — there the call belongs to nobody, so no key is inserted.', 'cluster-one-pacioli' ),
					'tun'   => __( 'Nothing. "Not connected" is the normal state on those pages. Whether a key works is answered by "Check key" under "My AIs".', 'cluster-one-pacioli' ),
				),

				'pacioli_no_secret' => array(
					'titel' => __( 'No key is stored for this AI', 'cluster-one-pacioli' ),
					'satz'  => __( 'The AI was addressed, but its owner has not entered an access key yet. This is not a fault of the node, but an open matter for that one person.', 'cluster-one-pacioli' ),
					'tun'   => __( 'Enter a key under "My AIs". The notice goes to the owner, not to the operator.', 'cluster-one-pacioli' ),
				),

				'pacioli_marker_no_secret' => array(
					'titel' => __( 'Stopped: no key stored', 'cluster-one-pacioli' ),
					'satz'  => __( 'As above, only noticed one level lower — the call was already on its way. It was stopped before it left the house.', 'cluster-one-pacioli' ),
					'tun'   => __( 'Enter a key under "My AIs".', 'cluster-one-pacioli' ),
				),

				'pacioli_marker_foreign_provider' => array(
					'titel' => __( 'Stopped: other provider', 'cluster-one-pacioli' ),
					'satz'  => __( 'In passing, the AI client addressed a provider other than the one currently being acted for — usually while exploring available models. This AI has no key for other providers.', 'cluster-one-pacioli' ),
					'tun'   => __( 'Nothing. This is expected and costs nobody anything.', 'cluster-one-pacioli' ),
				),

				'pacioli_marker_unbound' => array(
					'titel' => __( 'Stopped: nobody is acting', 'cluster-one-pacioli' ),
					'satz'  => __( 'A call without an acting AI was stopped before it went out. It could not have been attributed to anyone.', 'cluster-one-pacioli' ),
					'tun'   => __( 'Nothing. It is counted, but not as a fault.', 'cluster-one-pacioli' ),
				),

				'pacioli_marker_leaked' => array(
					'titel' => __( 'The key swap did not take hold', 'cluster-one-pacioli' ),
					'satz'  => __( 'A call would have gone out with a placeholder instead of its owner\'s key. It was stopped — nobody\'s bill was charged. This is the only one of these cases that is a real fault.', 'cluster-one-pacioli' ),
					/* POSTEN 1 — derselbe Satz wie in CV_Guard, und derselbe
					 * Umzug vom 16.09. Die Adresse wird nicht getippt,
					 * sondern aus CV_Admin::wo_pruefungen() gelesen; der
					 * Rueckfall ist benannt.                  (R-LEIHE-1) */
					'tun'   => sprintf(
						/* translators: %s: Ort der Pruefungen auf diesem Knoten. */
						__( 'The AI client has probably changed. The checks under %s say which of the two paths failed.', 'cluster-one-pacioli' ),
						class_exists( 'Pacioli_Admin' )
							? Pacioli_Admin::wo_pruefungen()
							: __( 'this plugin\'s own page', 'cluster-one-pacioli' )
					),
				),
			);
		}

		/**
		 * Der Satz zu einem Code, oder ein leeres Feld.
		 *
		 * @return array{titel:string,satz:string,tun:string}|null
		 */
		public static function get( $code ) {
			$all  = self::all();
			$code = (string) $code;

			return isset( $all[ $code ] ) ? $all[ $code ] : null;
		}

		/**
		 * Zeichnet die Übersicht.
		 */
		public static function render() {
			?>
			<h2><?php esc_html_e( 'When something does not work', 'cluster-one-pacioli' ); ?></h2>

			<p style="max-width:48em">
				<?php esc_html_e( 'After installation, notices may appear that were not there before. That is rarely because something is broken — usually because something used to be let through that could not be attributed to any bill. Here is what the notices mean.', 'cluster-one-pacioli' ); ?>
			</p>

			<table class="widefat striped" style="max-width:64em">
				<thead>
					<tr>
						<th scope="col" style="width:16em"><?php esc_html_e( 'Notice', 'cluster-one-pacioli' ); ?></th>
						<th scope="col"><?php esc_html_e( 'What it means', 'cluster-one-pacioli' ); ?></th>
						<th scope="col" style="width:20em"><?php esc_html_e( 'What to do', 'cluster-one-pacioli' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ( self::all() as $code => $n ) : ?>
					<tr>
						<td>
							<strong><?php echo esc_html( $n['titel'] ); ?></strong><br>
							<code style="font-size:0.85em"><?php echo esc_html( $code ); ?></code>
						</td>
						<td>
							<?php echo esc_html( $n['satz'] ); ?>
							<?php if ( ! empty( $n['nur_mit_schicht'] ) ) : ?>
								<br><em style="color:#555">
									<?php esc_html_e( 'This notice does not arise here, but in the layer that tasks the AI. If only Connector Vault is installed on this node, it cannot occur.', 'cluster-one-pacioli' ); ?>
								</em>
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( $n['tun'] ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<p style="max-width:64em;color:#555">
				<?php esc_html_e( 'Three of the notices above are marked separately: they belong to a calling layer and assume that such a layer is installed on this node. They stand here because this plugin changes their behaviour — not because it produces them.', 'cluster-one-pacioli' ); ?>
			</p>

			<p style="max-width:48em;color:#555">
				<?php esc_html_e( 'One code is deliberately missing here: "no_key". It belongs to a check elsewhere whose foundation is currently moving — the provider row now carries a placeholder instead of a key. Writing a sentence about a code whose meaning is under negotiation would mean laying down a second truth.', 'cluster-one-pacioli' ); ?>
			</p>
			<?php
		}
	}
}
