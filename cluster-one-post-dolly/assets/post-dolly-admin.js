/* Cluster One — Post Dolly, die Knoepfe im Backend. 0.4.10 (15.11.2026, Neo), 0.4.11 am selben Tag.
 *
 * Laedt nur auf dem Reiter „Zitate", wenn der Mensch speichern darf (wp_enqueue_script aus
 * cluster_one_post_dolly_tabelle, in den Fuss). Ohne dieses Skript: eine leere Zeile vom Server,
 * die Knoepfe bleiben `hidden`. Nichts hier schreibt HTML aus Text; jede neue Zeile ist ein Klon
 * einer Zeile, die der Server gezeichnet hat, mit geleerten Feldern und der naechsten freien
 * Nummer im Feldnamen cluster_one_post_dolly[<liste>][<nr>][<spalte>].
 *
 * ZWEI KNOEPFE:
 *   „Neue Zeile" (.cluster-one-dolly-neu, unter der Tabelle): haengt unten eine leere Zeile an,
 *     fuer einen neuen Kopf. Seit 0.4.10 - bis 0.4.9 haengte der Server leere Zeilen an.
 *   „Teil anfuegen" (.cluster-one-dolly-teil, Plus in der letzten Spalte jeder Zeile, 0.4.11):
 *     legt unter dem letzten Teil der Kette eine neue Zeile an, den Schluessel schon eingetragen
 *     (A6 -> A6-2, dann A6-3 …), der Cursor steht im ersten Textfeld. Nemesus (15.11.): „Der
 *     Unterschied waere nur, dass die Daten nicht manuell eingegeben, sondern automatisch
 *     reingeschrieben wuerden." Genau das. Die Datenstruktur ist dieselbe wie bei 0.4.10: der
 *     Schluessel traegt die Kette, der Knopf tippt ihn. Eine Zeile ohne Schluessel (noch nicht
 *     gespeichert) hat kein Plus, weil ihr Kopf seinen Schluessel erst beim Speichern bekommt. */
(function () {
    'use strict';
    var TEIL = /^([A-Za-z]+\d+)-(\d+)$/;

    function kopfVon(key) { var m = TEIL.exec(key); return (m ? m[1] : key).toUpperCase(); }
    function teilNr(key)  { var m = TEIL.exec(key); return m ? Number(m[2]) : 1; }

    function schluesselFeld(zeile) { return zeile.querySelector('input[name$="[key]"]'); }

    function hoechsteNummer(koerper) {
        var hoechste = -1, felder = koerper.querySelectorAll('input[name]'), f, m;
        for (f = 0; f < felder.length; f++) {
            m = felder[f].name.match(/\[(\d+)\]\[/);
            if (m && Number(m[1]) > hoechste) { hoechste = Number(m[1]); }
        }
        return hoechste;
    }

    /* Ein Klon der letzten Zeile (die leere Rueckfallzeile des Servers): Felder leer, Nummer neu. */
    function neueZeile(koerper) {
        var zeilen = koerper.rows;
        var neu = zeilen[zeilen.length - 1].cloneNode(true);
        var nr = hoechsteNummer(koerper) + 1;
        var felder = neu.querySelectorAll('input'), f;
        for (f = 0; f < felder.length; f++) {
            felder[f].value = '';
            felder[f].name = felder[f].name.replace(/\[(\d+)\]\[/, '[' + nr + '][');
        }
        var plus = neu.querySelector('.cluster-one-dolly-teil');
        if (plus) { plus.disabled = true; }   /* ohne Schluessel kein Teil - erst speichern */
        return neu;
    }

    function einrichtenTabelle(tabelle) {
        var koerper = tabelle.tBodies[0];
        if (!koerper || !koerper.rows.length) { return; }
        var liste = tabelle.getAttribute('data-liste');

        /* „Neue Zeile" unter der Tabelle. */
        var neu = document.querySelector('.cluster-one-dolly-neu[data-liste="' + liste + '"]');
        if (neu) {
            neu.addEventListener('click', function () {
                var z = neueZeile(koerper);
                koerper.appendChild(z);
                var erstes = z.querySelector('input');
                if (erstes) { erstes.focus(); }
            });
            neu.removeAttribute('hidden');
        }

        /* „Teil anfuegen" je Zeile: Klicks laufen ueber die Tabelle, damit auch Klone sie haben. */
        koerper.addEventListener('click', function (ev) {
            var plus = ev.target && ev.target.closest ? ev.target.closest('.cluster-one-dolly-teil') : null;
            if (!plus || plus.disabled) { return; }
            var zeile = plus.closest('tr');
            var feld = schluesselFeld(zeile);
            var key = feld ? feld.value.trim().toUpperCase() : '';
            if (!key) { return; }
            var kopf = kopfVon(key);

            /* Die hoechste Teilnummer dieser Kette und die Zeile des letzten Teils. */
            var naechste = 2, hinter = zeile, r, k;
            for (r = 0; r < koerper.rows.length; r++) {
                k = schluesselFeld(koerper.rows[r]);
                k = k ? k.value.trim().toUpperCase() : '';
                if (k && kopfVon(k) === kopf) {
                    if (teilNr(k) + 1 > naechste) { naechste = teilNr(k) + 1; }
                    hinter = koerper.rows[r];
                }
            }
            var z = neueZeile(koerper);
            var zf = schluesselFeld(z);
            if (zf) { zf.value = kopf + '-' + naechste; }
            var zp = z.querySelector('.cluster-one-dolly-teil');
            if (zp) { zp.disabled = false; }
            hinter.insertAdjacentElement('afterend', z);
            var text = z.querySelector('input:not([name$="[key]"])');
            if (text) { text.focus(); }
        });

        var pluesse = koerper.querySelectorAll('.cluster-one-dolly-teil'), p, pz, pf;
        for (p = 0; p < pluesse.length; p++) {
            pz = pluesse[p].closest('tr');
            pf = schluesselFeld(pz);
            pluesse[p].disabled = !(pf && pf.value.trim());
            pluesse[p].removeAttribute('hidden');
        }
        var kopfzelle = tabelle.querySelector('th.cluster-one-dolly-teile');
        if (kopfzelle) { kopfzelle.removeAttribute('hidden'); }
        var zellen = tabelle.querySelectorAll('td.cluster-one-dolly-teile'), c;
        for (c = 0; c < zellen.length; c++) { zellen[c].removeAttribute('hidden'); }
    }

    var tabellen = document.querySelectorAll('table[data-liste]');
    for (var i = 0; i < tabellen.length; i++) { einrichtenTabelle(tabellen[i]); }
})();
