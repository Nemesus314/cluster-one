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
 * Ohne dieses Skript: eine Zeile vom Server, Knopf und Sprachwahl bleiben
 * `hidden`. Nichts hier schreibt HTML — Texte gehen als textContent in die
 * Seite. */
(function () {
    'use strict';
    var MERKER = 'cluster_one_dolly_key';

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

        function finden(key) {
            for (var i = 0; i < zeilen.length; i++) {
                if (zeilen[i].key === key) { return zeilen[i]; }
            }
            return null;
        }

        function zeigen(zeile) {
            span.textContent = text(zeile, sprache, site);
            p.setAttribute('data-key', zeile.key);
            p.className = (String(zeile.key).charAt(0) === 'B') ? 'cluster-one-dolly-ansage' : 'cluster-one-dolly-zitat';
            aktuell[nr] = zeile.key;
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

        if (!knopf || zeilen.length < 2) { return; }

        /* Jede Zeile einmal, dann neu mischen; nie zweimal dieselbe hintereinander. */
        var reihe = [];
        function naechste() {
            if (!reihe.length) {
                reihe = mischen(zeilen.map(function (z) { return z.key; })
                    .filter(function (k) { return k !== aktuell[nr]; }));
            }
            var z = finden(reihe.shift());
            if (z) { zeigen(z); }
        }

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
