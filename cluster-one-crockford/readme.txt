=== Cluster One — Crockford ===
Contributors: nemesus
Tags: ai, site context, bricks
Requires at least: 7.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.6.9
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Site context for AIs — what an AI needs to understand this site.

== Description ==

Crockford reads what the site is made of (pages, templates, Bricks content, media)
and composes a text an AI can work with, plus the ground conditions of the node
(versions, limits, active plugins) with a note the SysOp confirms. It is one of the
five Cluster One plugins; they share a common ground (der Boden) that travels inside
every package.

Not published on wordpress.org. Translations ship in `languages/` and are loaded by
the plugin itself.

== Changelog ==

The version history lives in the header comments of the source files, next to the
code it explains. The Chronik on the node records what happened on that node.

= 0.6.9 =
* Omega-2: a released member saw the Ground conditions tab but not the page — a third SysOp check inside crockford.php had been missed in Chi. It now asks the same question as the door, the section and the handler (Crockford in full). Found by Nemesus on the DEV.

= 0.6.8 =
* Omega: Boden 1.23.0. Crockford’s release moves from the WordPress users list onto Crockford’s own page: section “Release” at the bottom of Site context, SysOp only — tick the members who get Crockford in full, save. Same right, same Chronik words; the users-list column stays as a reader. The sentence shown to members says where the release is.

= 0.6.7 =
* Psi: Boden 1.22.2 — why.txt's footers name the file why.txt (they still said why.md) and carry the ground version and date; the text itself is unchanged. Nothing else.

= 0.6.6 =
* Chi: the Ground conditions page hangs on Crockford's existing release. Whoever has Crockford in full (the SysOp by office, or released and a member) sees and confirms the ground conditions too; until now that was the one Kranz account only. One release, two pages; the users list says so. Boden unchanged.

= 0.6.5 =
* Ypsilon: Boden 1.22.1 — the Feynman tab is the last one, after Node (Nemesus' word after seeing Tau). Nothing else.

= 0.6.4 =
* Tau: Boden 1.22.0 — Feynman is back, as a tab in every bar right before Node (Derrida · About Cluster One · Why · DNA · Nemesus), under the bar and the name of the product you came from. feynman.php and why.txt now travel with every package. New in this package: feynman.php and why.txt, with their German strings in the catalog.

= 0.6.3 =
* Rho: Boden 1.21.1 — one more phpcs note in kern.php (the no-WordPress fallback uses stripslashes). No behaviour changed.

= 0.6.2 =
* Pi: the warnings round for the wordpress.org directory — every remaining Plugin Check warning carries a phpcs note with its reason (reads of address switches while drawing, inputs that go through the house sanitisers, the measuring tool's direct database reads), wp_unslash at the whitelist comparisons, and the Chronik learns the words it was missing. Boden 1.21.0, faust 1.1.2. No behaviour changed except the Chronik wording.

= 0.6.1 =
* Xi: repair of the prefix round — the renamed file-level variables had lost their first letter ($cluster_one_irt instead of $cluster_one_wirt; a build-tool slip, consistent within each file, so nothing misbehaved). Boden 1.20.1, faust 1.1.1. No behaviour changed.

= 0.6.0 =
* Ny: the prefix round for the wordpress.org directory — Boden 1.20.0 calls Pacioli under its new names (pacioli_ instead of cv_), and every file-level variable carries the full prefix (cluster_one_ instead of c1_). Pacioli 0.9.0 moves its stored names by itself. No behaviour changed.

= 0.5.39 =
* Theta: Plugin Check — the two page headings in crockford.php (site context, ground conditions) carry a phpcs note with reason; both branches of that line were already escaped. No behaviour changed.

= 0.5.38 =
* Eta: Boden 1.19.9 — the one developer notice in kasper.php carries a phpcs note with reason (two comment lines). No behaviour changed.

= 0.5.37 =
* Zeta: Plugin Check, Boden 1.19.8 — two small repairs in kern.php (wp_strip_all_tags without fallback; the events table name goes into prepare() as %i) and phpcs notes with reasons in kern/kranz/menue. The five kasper.php findings stay for now. No behaviour changed.

= 0.5.36 =
* Delta: Boden 1.19.7 (line endings only). No behaviour changed.

= 0.5.35 =
* Plugin Check round: translators comments, this readme. No behaviour changed.
