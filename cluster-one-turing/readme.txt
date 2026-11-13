=== Cluster One — Turing ===
Contributors: nemesus
Tags: installer, diagnostics, cluster one
Requires at least: 7.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 4.0.6
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Installer and inspector for the Cluster One suite in one tool. GitHub only; the inspector alone is Turing Diagnostics on wordpress.org.

== Description ==

Turing installs Cluster One as a set and inspects a node from the outside: which
packages lie there, which ground (Boden) each carries, what the files on disk say
compared to what runs. It carries no Boden itself, on purpose. Its reports name the
node; keep them where the node's people can read them, not in public places.

Not published on wordpress.org (GitHub only); the inspector part is published there as "Cluster One — Turing Diagnostics",
cut from this very file. Do not run both on one node: they share their names, and the second copy switches itself off
loudly. Turing speaks German and has no catalogue.

The last tab, Feynman, says what Cluster One is, what Turing tests, and shows the house rules (why.txt) — so that someone who
installs Turing first knows what it is about before any package lies on the node.

== Changelog ==

The version history lives in the header comment of turing.php.

= 4.0.6 =
* Psi: why.txt's footers name the file by its new name (Boden 1.22.2); a correction in the header history (Diagnostics is numbered like its source). Nothing else.

= 4.0.5 =
* Ypsilon: a fifth tab, Feynman (last): what Cluster One is, what Turing tests, the house rules from why.txt (which now ships with Turing; the running house's copy is shown when there is one). Link into the house's Feynman pages only when a package registers them.

= 4.0.4 =
* Sigma: the hangar check reads the server's self-description through sanitize_text_field(wp_unslash()); the last two Plugin Check notes are gone. Installer part only.

= 4.0.3 =
* Rho: the short description above fits the 150-character limit of the readme parser. Nothing else.

= 4.0.2 =
* Pi: phpcs notes with reasons on the inspector's direct reads; the Spiegel's events query goes through prepare() with %i; correction in the header history: with both tools active, Turing Diagnostics loads first (active_plugins sorts "-" before "/").

= 4.0.1 =
* Omikron: this whole tool ships again, as "Cluster One — Turing" in the folder cluster-one-turing (Nemesus: the GitHub user should not need two plugins). The separate "Turing Installer" cut from 4.0.0 is withdrawn; Turing Diagnostics stays the directory plugin.

= 4.0.0 =
* Xi: the split. This file stays the single source; the build cuts it into "Cluster One — Turing Diagnostics" (Kasper, Spiegel, Pacioli witness; folder cluster-one-turing-diagnostics, for the directory) and "Cluster One — Turing Installer" (folder cluster-one-turing-installer, prefix turing_installer_, GitHub only). "What lies on this node" becomes a function and also shows under Kasper; the Werft stamp shows on both tabs; the page is output-buffered (the "Details" flash); turing_zeuge_url() is the door Pacioli 0.9.1 asks for.

= 3.15.0 =
* Ny: the prefix round — every global name moves from tu_ / TU_ to turing_ / TURING_ (directory rule: a unique prefix of at least four letters); Pacioli's new names (pacioli_). Plugin name is now "Cluster One — Turing". No behaviour changed.

= 3.14.10 =
* My: Plugin Check — the 28 findings in turing.php (help bubbles, lists and cells that escape every member before joining, the installer's hand: rename/rmdir/unlink inside wp-content/plugins) carry phpcs notes with reasons; one malformed note from 3.0.0 made readable. No behaviour changed.

= 3.14.9 =
* Plugin Check round: this readme. No behaviour changed.
