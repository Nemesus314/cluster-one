=== Cluster One — Pacioli ===
Contributors: nemesus
Tags: ai, api-key, connectors, multi-user, security
Requires at least: 7.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.10.3
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Every AI works with its owner's key. No shared key, no shared bill.

== Description ==

WordPress stores the access keys for AI connectors as a setting of the whole
installation. Whoever enters one pays for everybody. Whoever does not enter one
cannot use AI at all.

Connector Vault turns that around: **every AI identity works only with the key
its owner has stored.** After activation the provider row no longer holds a real
key but a worthless marker; the real key is inserted only immediately before
sending — and it is the key of whichever AI is acting at that moment.

= What this means for an operator =

* A few friends or an editorial team may use AI without the bill landing on the
  operator. Two steps belong to that, and they are named here so that nobody has
  to go looking: the operator grants each person the `use_own_ai_connector`
  capability, and something on the installation has to task an AI in the first
  place. On its own, this plugin brings exactly one such place — the "Check key"
  button.
* Nobody can task somebody else's AI. Not even the operator.
* No key sits in the database in plain text. Stored keys are encrypted with
  Sodium.
* If the plugin fails, something worthless goes out and the provider answers
  401. **Doing nothing is the safe state** — no bill is created.

= What this plugin explicitly does NOT claim =

That the swapped key arrives at the provider is something it cannot prove by
itself. A plugin that watches itself swapping only bears witness that it
swapped. The proof requires a measurement from outside — one run with two
identities at the actually outgoing request, in which two different fingerprints
arrive.

As long as that measurement is not confirmed, the plugin says so under
Cluster One → Pacioli → For developers. It does not issue itself a certificate.

= Ground =

Since 0.10.0 Pacioli carries the whole Cluster One ground (Boden), as every other Cluster One plugin does: the shared
menu, the node pages (profile, modes, system, chronicle, AI identities, personal area, setup) and the Feynman pages travel
inside this package. Installed alone, Pacioli shows the Cluster One menu with those pages; installed next to another
Cluster One package, the copy that loads first serves and the others stay silent.

= Language =

The source language is English; a German catalogue ships in `languages/` and is
loaded at `init`. Until 02.09.2026 this plugin was written in German and carried
no catalogue at all — over 190 strings sat inside translation functions with a
German original, which is not an offer of translation but only a wrapping.

The header comments in the source stay German on purpose. They explain the
*why*, they are addressed to the people who build this plugin, and they belong
in no catalogue.

== Installation ==

**Please read this first.** On activation this plugin writes into the provider
rows: existing keys are taken into encrypted safekeeping and replaced by
markers. On an installation that is worked on daily, that is not a test but an
intervention.

1. Have your existing API keys available somewhere else.
2. Install first on an instance whose failure disturbs nobody.
3. Recommended: add one line to `wp-config.php`, so that the encryption key does
   not sit in the same database as the encrypted data:
   `define( 'PACIOLI_SECRET_KEY', '<long random string>' );`
   (Installations from before 0.9.0 may still carry the old name `CV_SECRET_KEY`; it keeps
   working, and the backend asks you to rename it.)
4. Activate the plugin.
5. **Users → My AIs**: create an AI, choose a provider, enter your own key,
   press "Check key".

Without the Sodium extension (part of PHP since 7.2) activation aborts. That is
deliberate: the alternative would be storing keys in plain text.

== Frequently Asked Questions ==

= The Connectors page says "not connected". Is something broken? =

No. A connection check made from that page belongs to nobody, so no key is
inserted. Whether a key works is answered by the "Check key" button under
Users → My AIs — it makes a very short request on your own bill.

= There is a strange text in the key field. Is the key broken? =

That is the marker. It is worthless and belongs there. Please do not overwrite
it — an entered key is intercepted, taken into safekeeping, and the marker is
restored.

= Can another person use my AI? =

No. Only the owner tasks their own AI. An operator can open that deliberately
(filter `pacioli_can_invoke`) — and then pays for it too.

= Why does my editor not see the menu entry? =

Because they do not have the `use_own_ai_connector` capability yet. A vault
anybody can walk into is not a vault. An administrator grants the capability in
the user list.

= What happens on deactivation? =

The markers stay. Every AI call then fails with 401 — for everybody alike. A
real key left behind would instead quietly keep running on the operator's bill.

= What happens on deletion? =

Deletion removes all AI accounts together with their stored keys, the roles and
the markers. **Get the keys out first, then delete.**

= Does this protect against the operator of the installation? =

No, and it does not claim to. The encryption protects against a database dump
and against other plugins. Anyone with server access reaches everything. So:
create a separate key at the provider for this site only, and set a spending
limit there.

== Changelog ==

= 0.10.3 =
* Menu order: Pacioli now stands before Post Dolly under Cluster One (order number 60 -> 40; Post Dolly 0.4.9 takes 50). The roof entry still leads to Kleinrock. Nothing else.

= 0.10.2 =
* Omega: Boden 1.23.0. The bulk actions in the users list are gone — since 0.8.44 every member of the node has the right to run their own AIs and to read “For developers”; a per-person grant did nothing for members and something for non-members, against the rule of the house. The “Developer info” column is gone too; “Own AIs” stays. “For developers” now says so, with the member count and a link to the node’s list. The readme names the page where it lives.

= 0.10.1 =
* Psi: Boden 1.22.2 — why.txt's footers name the file why.txt (they still said why.md) and carry the ground version and date; the text itself is unchanged. Nothing else.

= 0.10.0 =
* Phi: the whole Cluster One ground travels with Pacioli (anti-babel, kern, kranz, menue, speicher — identical to the other packages up to the text domain). The tab bar is drawn by the ground (Node and Feynman come from there), so is the version stamp. The German catalogue carries the ground's strings. Nothing changed in the key vault itself.

= 0.9.6 =
* Ypsilon: Boden 1.22.1 — the Feynman tab is the last one, after Node (Nemesus' word after seeing Tau). Nothing else. Pacioli's own bar follows.

= 0.9.5 =
* Tau: Boden 1.22.0 — Feynman is back, as a tab in every bar right before Node (Derrida · About Cluster One · Why · DNA · Nemesus), under the bar and the name of the product you came from. feynman.php and why.txt now travel with every package. New in this package: feynman.php and why.txt, with their German strings in the catalog. Pacioli shows the tab only when a Cluster One ground package on the same node registers Feynman's pages.

= 0.9.4 =
* Sigma: INTEGRATION.md is now INTEGRATION.txt (same text). Nothing else.

= 0.9.3 =
* Rho: Boden 1.21.1 — one more phpcs note in kern.php (the no-WordPress fallback uses stripslashes). No behaviour changed.

= 0.9.2 =
* Pi: the warnings round for the wordpress.org directory — every remaining Plugin Check warning carries a phpcs note with its reason (reads of address switches while drawing, inputs that go through the house sanitisers, the measuring tool's direct database reads), wp_unslash at the whitelist comparisons, and the Chronik learns the words it was missing. Boden 1.21.0. No behaviour changed except the Chronik wording.

= 0.9.1 =
* Xi: the "Measure with Turing and record" button is back. 0.9.0 still called Turing by its old function names
  (tu_*) after Turing had moved to turing_* in the same round, so the developer page claimed Turing was not on the
  node. The link to Turing's witness now comes from Turing itself (Turing Diagnostics 4.0.0). Two file-level
  variables get their first letter back (prefix-round repair). No other behaviour changed.

= 0.9.0 =
* Ny: every global name of this plugin moves from `cv_` / `CV_` to `pacioli_` / `PACIOLI_` / `Pacioli_` — functions,
  classes, constants, hooks, error codes, options and user meta — because the wordpress.org directory asks for a
  unique prefix of at least four letters. Stored data walks over by itself on the first load (ten options, nine user
  meta keys, one capability; nothing is re-serialised, nothing is deleted) and leaves a Chronik entry. The one thing a
  plugin cannot rename is a constant in wp-config.php: `PACIOLI_SECRET_KEY` is the new name, `CV_SECRET_KEY` is still
  read, and the backend asks the operator to rename it. Plugins that call Pacioli (Kleinrock, Bismarck via the Boden)
  use the new names from Boden 1.20.0 on; the mapping is in INTEGRATION.md. No behaviour changed.

= 0.8.53 =
* Eta: Boden 1.19.9 — the one developer notice in kasper.php carries a phpcs note with reason (two comment lines). No behaviour changed.

= 0.8.52 =
* Zeta: Boden 1.19.8 (version line only; the five kasper.php findings stay for now). No behaviour changed.

= 0.8.51 =
* Epsilon: the refused-calls count is passed to printf() through absint() so Plugin Check can see it is a number (it already was). No behaviour changed.

= 0.8.50 =
* Delta: the first LIKE pattern of the return path is passed to prepare() as a parameter too (Plugin Check), Boden 1.19.7 (line endings only). No behaviour changed.

= 0.8.49 =
* Plugin Check round: readme header renamed to the plugin's name (it was still
  "Connector Vault", the name this plugin carried before it joined Cluster One as
  Pacioli), stable tag brought up to the version, one SQL LIKE written with
  prepare(). No behaviour changed. The versions between 0.7.3 and here are recorded
  in the header comments of the source files.

= after 0.7.3 (version assigned at cut) =
* Source language moved to English; a German catalogue ships in `languages/` and
  is loaded at `init`. Loader and catalogue arrive together — a loader without a
  catalogue is a precaution for something that does not exist.
* `includes/kasper.php` is Boden 1.5.0, adopted byte for byte.
* No behaviour changed. Same machine, one language further.

= 0.7.3 =
* Three of the error notes belong to the layer that tasks the AI, not to this
  plugin — on an installation with only this plugin they cannot occur. They are
  marked as such now.
* The missing translatability was written down as a named limit.

= 0.7.2 =
* The notice "The proof at the outgoing request is still outstanding" now
  appears only under Tools → Connector Vault and can be dismissed there. It is
  not a warning about damage but an open bill — and that belongs where it can be
  paid. It returns on reload.
* The other five notices appear everywhere as before and carry no dismiss cross.
  A notice about something that is broken right now belongs everywhere.
* First `readme.txt`.

= 0.7.1 =
* Boden 1.3.0 adopted. No other change.

= 0.7.0 =
* Registration in the map; Boden adopted.

= 0.6.0 =
* One sentence for every error that would not exist without this plugin.

= 0.5.0 =
* The fourth state at the bolt; `cv_can_invoke` became filterable.

= 0.4.0 =
* The key is set in the AI client's registry instead of the options layer. The
  marker remains as a tripwire.

= 0.3.0 =
* The marker: no real key ever sits in the provider row.

The full version history with the reasons is in `CHANGELOG.md` — in German, like
the header comments, and for the same reason.

== Upgrade Notice ==

= after 0.7.3 =
The interface changes language: English source with a German catalogue. A
confirmed outside measurement is always tied to the version it was confirmed
for — after this change it has to be confirmed again.
