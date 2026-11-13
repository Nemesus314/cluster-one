=== Cluster One — Turing Diagnostics ===
Contributors: nemesus
Tags: diagnostics, cluster one, ai, api-key
Requires at least: 7.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 4.0.6
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Reads a Cluster One node from the outside and writes nothing. Four tabs: Kasper, Spiegel, Pacioli witness, Feynman.

== Description ==

Turing Diagnostics is the inspector beside the Cluster One suite (Kleinrock, Bismarck, Crockford, Post Dolly,
Pacioli). It carries no Boden itself, on purpose, and it writes no option, no user meta, no table and no file:
what it diagnoses it cannot quietly change.

* **Kasper** — which packages lie on this node and which Boden (ground) each carries; which organ answered from
  which plugin and in what order WordPress loads them; which file lies in which packages and whether the copies
  are equal.
* **Spiegel** — the stored state of Cluster One without its content: structure, lengths and salted fingerprints
  instead of texts; anything that looks like a key, token or mail address as ***. Runs only on a click.
* **Pacioli witness** — the one question Pacioli cannot answer about itself: does the key that leaves the server
  in the outgoing POST come from an option row, or from what was set before the first read? Measured at the
  actual HTTP request. GET passes, POST is read and aborted: no prompt leaves the house, no tokens are spent, and
  no key is ever shown (an 8-character salted fingerprint compares them).
* **Feynman** — what Cluster One is, what this tool tests, and the house rules (why.txt, shipped with the tool; the
  running house's copy is shown when there is one). For whoever installs the inspector before any package.

Its reports name the node, the substrate versions and WP/PHP. Keep them where the node's people can read them,
not in public places. Turing Diagnostics speaks German and has no catalogue.

The installer that used to be the first tab is a separate tool, "Cluster One — Turing Installer", published on
GitHub only.

== Changelog ==

The version history lives in the header comment of the source file (turing.php, Werkbank).

= 4.0.6 =
* Psi: why.txt's footers name the file by its new name (Boden 1.22.2). Cut from the Turing source 4.0.6, numbered like it. Nothing else in the inspector.

= 4.0.5 =
* Ypsilon: a fourth tab, Feynman (last): what Cluster One is, what Turing Diagnostics tests, the house rules from why.txt (now shipped with the tool). Cut from the Turing source 4.0.5 — and numbered like it: the cut carries the source's header.

= 4.0.3 =
* Rho: cut from the Turing source 4.0.3 (a readme-only step there); nothing changed in the inspector.

= 4.0.2 =
* Pi: phpcs notes with reasons on the inspector's direct reads (option names, tables); the Spiegel's events query goes through prepare() with %i. Cut from the Turing source 4.0.2 (4.0.1 was a source-only step).

= 4.0.0 =
* Xi: first release as its own plugin, cut from Turing 3.15.0. Kasper now also shows "What lies on this node"
  (packages, Boden, the tools under the table). Werft stamp on the Kasper tab. Page output-buffered. New door
  turing_zeuge_url() for Pacioli.
