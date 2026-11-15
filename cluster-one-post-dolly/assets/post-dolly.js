/* Cluster One — Post Dolly, der Weiter-Knopf. 27.10.2026 (Neo).
 *
 * Laedt nur, wenn der nackte Shortcode [cluster_one_dolly] auf der Seite
 * steht oder das Dashboard-Feld gezeichnet wird (wp_enqueue_script aus dem
 * Block, in den Fuss). Liest den Topf aus data-dolly, schaltet per Knopf zur
 * naechsten Zeile und nimmt den Schluessel ueber einen Sprachwechsel mit:
 * Klick auf einen Link mit hreflang oder in .lang-item legt ihn in den
 * sessionStorage, die Zielseite liest ihn einmal und raeumt ihn weg. Der
 * Speicher stirbt mit dem Tab; kein Cookie.
 *
 * Im Dashboard-Feld dazu eine Sprachwahl (.cluster-one-dolly-sprache je
 * Spalte): schaltet die Sprache des Blocks um und zeigt dieselbe Zeile neu.
 *
 * 0.4.10 (15.11.2026, Neo) — KETTEN. Nemesus: „Komplexitaet braucht manchmal
 * Laenge." Ein langer Text liegt in Teilen: der Kopf traegt den Schluessel
 * (A6), die Teile denselben Schluessel mit Bindestrich und Nummer (A6-2, A6-3).
 * Der Zufall - der Server beim ersten Satz, dieses Skript beim Mischen - zieht
 * nur Koepfe. Steht ein Kopf oder ein Teil, fuehrt der Knopf zum naechsten Teil
 * der Kette; erst nach dem letzten geht es zurueck in den gemischten Topf. Der
 * Knopf zeigt dabei einen Pfeil nach rechts statt des Kreispfeils und heisst
 * „Weiterlesen" statt „Naechste Zeile" (beide Symbole kommen vom Server, das
 * Skript blendet nur um). Bis 0.4.9 kannte das Skript keine Teile: jede Zeile
 * war ein Zug, gemischt, nie zweimal dieselbe hintereinander.
 *
 * Ohne dieses Skript: eine Zeile vom Server (ein Kopf), Knopf und Sprachwahl
 * bleiben `hidden`. Nichts hier schreibt HTML — Texte gehen als textContent in
 * die Seite. */
(function () {
    'use strict';
    var MERKER = 'cluster_one_dolly_key';
    var TEIL = /^([A-Za-z]+\d+)-(\d+)$/;

    var gemerkt = null;
    try { gemerkt = window.sessionStorage.getItem(MERKER); } catch (e) { gemerkt = null; }

    function mischen(liste) {
        var a = liste.slice(), i, j, t;
        for (i = a.length - 1; i > 0; i--) {
            j = Math.floor(Math.random() * (i + 1));
            t = a[i]; a[i] = a[j]; a[j] = t;
        }
        return a;
    }

    function text(zeile, sprache, site) {
        var k, kette = [sprache, site, 'en'];
        for (k = 0; k < kette.length; k++) {
            if (kette[k] && zeile[kette[k]]) { return zeile[kette[k]]; }
        }
        for (k in zeile) {
            if (k !== 'key' && Object.prototype.hasOwnProperty.call(zeile, k) && zeile[k]) { return zeile[k]; }
        }
        return '';
    }

    /* Ketten: Kopf eines Schluessels, Nummer eines Teils, der naechste Teil. */
    function kopfVon(key) { var m = TEIL.exec(String(key)); return m ? m[1].toUpperCase() : String(key).toUpperCase(); }
    function teilNr(key)  { var m = TEIL.exec(String(key)); return m ? Number(m[2]) : 1; }
    function istTeil(key) { return TEIL.test(String(key)); }

    var aktuell = [];   /* je Block der aktuelle Schluessel */

    function einrichten(box, nr) {
        var daten;
        try { daten = JSON.parse(box.getAttribute('data-dolly')); } catch (e) { return; }
        if (!daten || !daten.zeilen || !daten.zeilen.length) { return; }

        var p = box.querySelector('p');
        var span = box.querySelector('.cluster-one-dolly-text');
        var knopf = box.querySelector('.cluster-one-dolly-weiter');
        var wahl = box.querySelector('.cluster-one-dolly-sprachen');
        if (!p || !span) { return; }

        var zeilen = daten.zeilen;
        var sprache = daten.sprache || '';
        var site = daten.site || '';
        var koepfe = zeilen.filter(function (z) { return !istTeil(z.key); }).map(function (z) { return z.key; });

        function finden(key) {
            var gross = String(key).toUpperCase();
            for (var i = 0; i < zeilen.length; i++) {
                if (String(zeilen[i].key).toUpperCase() === gross) { return zeilen[i]; }
            }
            return null;
        }

        /* Der naechste Teil der Kette, in der key steht; null am Ende oder ohne Kette. */
        function folge(key) {
            return finden(kopfVon(key) + '-' + (teilNr(key) + 1));
        }

        var ikonNeu = knopf ? knopf.querySelector('.cluster-one-dolly-ikon-neu') : null;
        var ikonWeiter = knopf ? knopf.querySelector('.cluster-one-dolly-ikon-weiter') : null;

        function knopfStimmen(key) {
            if (!knopf) { return; }
            var weiter = !!folge(key);
            var titel = knopf.getAttribute(weiter ? 'data-titel-weiter' : 'data-titel-neu') || '';
            if (titel) { knopf.setAttribute('title', titel); knopf.setAttribute('aria-label', titel); }
            if (ikonNeu && ikonWeiter) {
                if (weiter) { ikonNeu.setAttribute('hidden', ''); ikonWeiter.removeAttribute('hidden'); }
                else { ikonWeiter.setAttribute('hidden', ''); ikonNeu.removeAttribute('hidden'); }
            }
        }

        function zeigen(zeile) {
            span.textContent = text(zeile, sprache, site);
            p.setAttribute('data-key', zeile.key);
            p.className = (String(zeile.key).charAt(0).toUpperCase() === 'B') ? 'cluster-one-dolly-ansage' : 'cluster-one-dolly-zitat';
            aktuell[nr] = zeile.key;
            knopfStimmen(zeile.key);
        }

        aktuell[nr] = p.getAttribute('data-key') || '';

        /* Der mitgereiste Schluessel: dieselbe Zeile wie auf der anderen Sprache. */
        if (gemerkt && gemerkt !== aktuell[nr]) {
            var her = finden(gemerkt);
            if (her) { zeigen(her); }
        }

        /* Die Sprachwahl (Dashboard): Sprache umschalten, Zeile neu zeigen. */
        if (wahl) {
            var tasten = wahl.querySelectorAll('.cluster-one-dolly-sprache');
            var t;
            function waehlen(ev) {
                var code = ev.currentTarget.getAttribute('data-sprache') || '';
                if (!code) { return; }
                sprache = code;
                for (var i = 0; i < tasten.length; i++) {
                    tasten[i].setAttribute('aria-pressed', tasten[i] === ev.currentTarget ? 'true' : 'false');
                    tasten[i].style.opacity = tasten[i] === ev.currentTarget ? '1' : '.7';
                }
                var jetzt = finden(aktuell[nr]);
                if (jetzt) { zeigen(jetzt); }
            }
            for (t = 0; t < tasten.length; t++) {
                tasten[t].addEventListener('click', waehlen);
                if (tasten[t].getAttribute('aria-pressed') === 'true') { tasten[t].style.opacity = '1'; }
            }
            if (tasten.length > 1) { wahl.removeAttribute('hidden'); }
        }

        /* Der Knopf: nur, wenn es etwas zu schalten gibt - einen zweiten Kopf oder eine Kette. */
        if (!knopf || (koepfe.length < 2 && !folge(aktuell[nr]))) { return; }

        /* Jeden Kopf einmal, dann neu mischen; nie zweimal derselbe hintereinander.
           Teile kommen nicht in die Reihe: sie folgen ihrem Kopf. */
        var reihe = [];
        function naechste() {
            var weiter = folge(aktuell[nr]);
            if (weiter) { zeigen(weiter); return; }
            if (!reihe.length) {
                var jetztKopf = kopfVon(aktuell[nr]);
                reihe = mischen(koepfe.filter(function (k) { return String(k).toUpperCase() !== jetztKopf; }));
            }
            var z = finden(reihe.shift());
            if (z) { zeigen(z); }
        }

        knopfStimmen(aktuell[nr]);
        knopf.addEventListener('click', naechste);
        knopf.removeAttribute('hidden');
    }

    var boxen = document.querySelectorAll('.cluster-one-dolly[data-dolly]');
    for (var i = 0; i < boxen.length; i++) { einrichten(boxen[i], i); }
    try { window.sessionStorage.removeItem(MERKER); } catch (e) { /* kein Speicher, kein Schaden */ }

    if (!boxen.length) { return; }

    /* Sprachwechsel: der Schluessel des ersten Blocks reist mit. */
    document.addEventListener('click', function (ev) {
        var ziel = ev.target && ev.target.closest ? ev.target.closest('a[hreflang], .lang-item a') : null;
        if (!ziel || !aktuell[0]) { return; }
        try { window.sessionStorage.setItem(MERKER, aktuell[0]); } catch (e) { /* dann eben Zufall */ }
    });
})();
