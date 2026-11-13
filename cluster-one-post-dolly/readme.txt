=== Cluster One — Post Dolly ===
Contributors: nemesus
Tags: quotes, shortcode, federation
Requires at least: 7.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.4.9
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Quotes from the admin, wherever they are wanted.

== Description ==

Post Dolly keeps two tables — quotes and federation announcements — in the languages
of the node, shows one at a time in the dashboard or via shortcode, and lets members
propose quotes to the SysOp. It is one of the five Cluster One plugins; they share a
common ground (der Boden) that travels inside every package.

Not published on wordpress.org. Translations ship in `languages/` and are loaded by
the plugin itself.

== Changelog ==

The version history lives in the header comments of the source files, next to the
code it explains. The Chronik on the node records what happened on that node.

= 0.4.9 =
* Menu order: Post Dolly now stands last under Cluster One, after Pacioli (order number 40 -> 50; Pacioli 0.10.3 takes 40). Nothing else.

= 0.4.8 =
* Empty lines: both tables used to append a fixed three empty lines (fill three, save, three more). Now a line above the tables offers 3 · 6 · 12 · 24; the number travels in the address, is only read, and the page says that empty lines are not saved. Default stays 3. No JavaScript. Boden unchanged (1.23.0).

= 0.4.7 =
* Omega: Boden 1.23.0. New setting “Dashboard box visible to”: members of the node only (default) or everyone who can see the dashboard — until now everyone who could see the dashboard saw the box. The front end is unchanged.

= 0.4.6 =
* Psi: Boden 1.22.2 — why.txt's footers name the file why.txt (they still said why.md) and carry the ground version and date; the text itself is unchanged. Nothing else.

= 0.4.5 =
* Ypsilon: Boden 1.22.1 — the Feynman tab is the last one, after Node (Nemesus' word after seeing Tau). Nothing else.

= 0.4.4 =
* Tau: Boden 1.22.0 — Feynman is back, as a tab in every bar right before Node (Derrida · About Cluster One · Why · DNA · Nemesus), under the bar and the name of the product you came from. feynman.php and why.txt now travel with every package. New in this package: feynman.php and why.txt, with their German strings in the catalog.

= 0.4.3 =
* Rho: Boden 1.21.1 — one more phpcs note in kern.php (the no-WordPress fallback uses stripslashes). No behaviour changed.

= 0.4.2 =
* Pi: the warnings round for the wordpress.org directory — every remaining Plugin Check warning carries a phpcs note with its reason (reads of address switches while drawing, inputs that go through the house sanitisers, the measuring tool's direct database reads), wp_unslash at the whitelist comparisons, and the Chronik learns the words it was missing. Boden 1.21.0, faust 1.1.2. No behaviour changed except the Chronik wording.

= 0.4.1 =
* Xi: repair of the prefix round — the renamed file-level variables had lost their first letter ($cluster_one_irt instead of $cluster_one_wirt; a build-tool slip, consistent within each file, so nothing misbehaved). Boden 1.20.1, faust 1.1.1. No behaviour changed.

= 0.4.0 =
* Ny: the prefix round for the wordpress.org directory — Boden 1.20.0 calls Pacioli under its new names (pacioli_ instead of cv_), and every file-level variable carries the full prefix (cluster_one_ instead of c1_). Pacioli 0.9.0 moves its stored names by itself. No behaviour changed.

= 0.3.73 =
* Eta: Boden 1.19.9 — the one developer notice in kasper.php carries a phpcs note with reason (two comment lines). No behaviour changed.

= 0.3.72 =
* Zeta: Plugin Check, Boden 1.19.8 — two small repairs in kern.php (wp_strip_all_tags without fallback; the events table name goes into prepare() as %i) and phpcs notes with reasons in kern/kranz/menue. The five kasper.php findings stay for now. No behaviour changed.

= 0.3.71 =
* Delta: Boden 1.19.7 (line endings only). No behaviour changed.

= 0.3.70 =
* Plugin Check round: this readme. No behaviour changed.
