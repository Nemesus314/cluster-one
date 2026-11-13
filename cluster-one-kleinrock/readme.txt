=== Cluster One — Kleinrock ===
Contributors: nemesus
Tags: federation, mesh, nodes, ai
Requires at least: 7.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.3.7
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The mesh for a Cluster One node — pairing, friends, and what travels between nodes.

== Description ==

Kleinrock connects one WordPress installation (a node) with other nodes: pairing by
address, a list of friends, hello chats between nodes, and a map of what is known.
It is one of the five Cluster One plugins; they share a common ground (der Boden)
that travels inside every package.

Not published on wordpress.org. Translations ship in `languages/` and are loaded by
the plugin itself.

== Changelog ==

The version history lives in the header comments of the source files, next to the
code it explains. The Chronik on the node records what happened on that node.

= 0.3.7 =
* Omega: Boden 1.23.0 — one shared list of the members a plugin can release to (cluster_one_freigabe_mitglieder). Nothing else in this package.

= 0.3.6 =
* Psi: Boden 1.22.2 — why.txt's footers name the file why.txt (they still said why.md) and carry the ground version and date; the text itself is unchanged. Nothing else.

= 0.3.5 =
* Ypsilon: Boden 1.22.1 — the Feynman tab is the last one, after Node (Nemesus' word after seeing Tau). Nothing else.

= 0.3.4 =
* Tau: Boden 1.22.0 — Feynman is back, as a tab in every bar right before Node (Derrida · About Cluster One · Why · DNA · Nemesus), under the bar and the name of the product you came from. feynman.php and why.txt now travel with every package.

= 0.3.3 =
* Rho: Boden 1.21.1 — one more phpcs note in kern.php (the no-WordPress fallback uses stripslashes). No behaviour changed. why.md is now why.txt (same text; the AIs and the Feynman page read it as before).

= 0.3.2 =
* Pi: the warnings round for the wordpress.org directory — every remaining Plugin Check warning carries a phpcs note with its reason (reads of address switches while drawing, inputs that go through the house sanitisers, the measuring tool's direct database reads), wp_unslash at the whitelist comparisons, and the Chronik learns the words it was missing. Boden 1.21.0, faust 1.1.2. No behaviour changed except the Chronik wording.

= 0.3.1 =
* Xi: repair of the prefix round — the renamed file-level variables had lost their first letter ($cluster_one_irt instead of $cluster_one_wirt; a build-tool slip, consistent within each file, so nothing misbehaved). Boden 1.20.1, faust 1.1.1. No behaviour changed.

= 0.3.0 =
* Ny: the prefix round for the wordpress.org directory — Boden 1.20.0 calls Pacioli under its new names (pacioli_ instead of cv_), and every file-level variable carries the full prefix (cluster_one_ instead of c1_). Pacioli 0.9.0 moves its stored names by itself. No behaviour changed.

= 0.2.70 =
* Kappa: Plugin Check — the 43 remaining findings in kleinrock.php (page headings, table cell helpers, the chat bubble variables) carry phpcs notes with reasons; every helper already escapes inside. No behaviour changed.

= 0.2.69 =
* Iota: Plugin Check — faust.php 1.0.21 and feynman.php carry phpcs notes with reasons at twelve lines (page headings, the why.md translator, wpautop around esc_html, the help bubble, one developer notice). No behaviour changed.

= 0.2.68 =
* Eta: Boden 1.19.9 — the one developer notice in kasper.php carries a phpcs note with reason (two comment lines). No behaviour changed.

= 0.2.67 =
* Zeta: Plugin Check, Boden 1.19.8 — two small repairs in kern.php (wp_strip_all_tags without fallback; the events table name goes into prepare() as %i) and phpcs notes with reasons in kern/kranz/menue. The five kasper.php findings stay for now. No behaviour changed.

= 0.2.66 =
* Delta: translators comments in faust.php and feynman.php (the copies shared with Bismarck), faust 1.0.20, Boden 1.19.7 (line endings only). No behaviour changed.

= 0.2.65 =
* Plugin Check round: translators comments, this readme. No behaviour changed.
