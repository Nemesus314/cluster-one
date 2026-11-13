<?php
/**
 * Cluster One — Modul crockford.
 *
 * Der Site-Kontext: was Bricks ueber diese Seite weiss, gelesen aus der
 * Datenbank und aufbereitet als Text fuer eine KI. Dazu Crockfords eigene
 * Seite mit Reiterleiste (Site-Kontext · Randbedingungen). Bis 0.5.16 hing
 * die Landkarte als dritter Reiter daran; sie wohnt seit dem 20.10.2026 in
 * Turing (Reiter Kasper), siehe das Zitat am Ende dieser Datei.
 *
 * WO BRICKS SEINE DATEN ABLEGT (Bricks-Entwicklerdoku, geprueft 19.10.2026):
 *   wp_postmeta  _bricks_page_content_2     Elemente einer Seite/Vorlage
 *                _bricks_page_header_2      Header-Elemente (Vorlagen)
 *                _bricks_page_footer_2      Footer-Elemente (Vorlagen)
 *                _bricks_template_type      Typ einer Vorlage (bricks_template)
 *                _bricks_template_settings  Bedingungen, Vorschau, Popup …
 *   wp_options   bricks_global_classes      id -> Klassenname
 *                bricks_components          Komponenten
 * Elemente sind eine FLACHE Liste: id, name, parent (0 = Wurzel), children,
 * settings, meist label. Genau die Form, die der Strukturbaum beim Kopieren
 * als JSON ausgibt.
 *
 * Diese Datei LIEST NUR. Sie speichert nichts, sie ruft keinen Bricks-Code.
 * Vegas-Prinzip: alles hier bleibt im Backend; der Text verlaesst den Knoten
 * nur, wenn ein Mensch ihn kopiert oder ein Raum ihn einer KI gibt.
 */
defined('ABSPATH') || exit;

if (!function_exists('cluster_one_crockford_seiten')) {

cluster_one_hier('crockford');

/* ---------------------------------------------------------------------------
 * DIE SEITEN DIESES PRODUKTS — eine Tabelle, drei Leser (Anmeldung, Anspruch,
 * Leiste). Dieselbe Form wie bis 0.5.16 cluster_one_landkarte_seiten()
 * (Rundbrief 21 §2: die Liste wird GEFRAGT, nicht abgeschrieben).
 * ------------------------------------------------------------------------- */
function cluster_one_crockford_seiten(): array {
    return array(
        'cluster_one_crockford' => array(
            'titel'  => __('Cluster One — Site context', 'cluster-one-crockford'),
            'reiter' => __('Site context', 'cluster-one-crockford'),
            'cap'    => 'cluster_one_access',
            'ruf'    => 'cluster_one_render_crockford_page',
        ),
        'cluster_one_randbedingungen' => array(
            'titel'  => __('Cluster One — Ground conditions', 'cluster-one-crockford'),
            'reiter' => __('Ground conditions', 'cluster-one-crockford'),
            /* Chi (03.11.2026, Neo), 0.6.6: bis 0.6.5 stand hier 'cluster_one_kranz_save' — das Recht des Kranz, das genau ein
               Konto hat. Jetzt Crockfords eigenes Recht, das an der Freigabe haengt (siehe CLUSTER_ONE_CROCKFORD_RANDBEDINGUNGEN). */
            'cap'    => 'cluster_one_crockford_randbedingungen',
            'ruf'    => 'cluster_one_render_randbedingungen_page',
        ),
    );
}

add_action('admin_menu', function () {
    foreach (cluster_one_crockford_seiten() as $c1_slug => $c1_s) {
        add_submenu_page(null, $c1_s['titel'], $c1_s['reiter'], $c1_s['cap'], $c1_slug, $c1_s['ruf']);
    }
}, 5);

add_filter('cluster_one_seite_ansprueche', function ($c1_a, $c1_seite) {
    if (!is_array($c1_a) || !is_string($c1_seite)) { return $c1_a; }
    if (!function_exists('cluster_one_kasper_wirt')) { return $c1_a; }
    if (!array_key_exists($c1_seite, cluster_one_crockford_seiten())) { return $c1_a; }
    $c1_wirt = cluster_one_kasper_wirt(__FILE__);
    if ($c1_wirt === '') { return $c1_a; }
    $c1_a[] = $c1_wirt;
    return $c1_a;
}, 10, 2);

/* Die Leiste: der Boden fragt beim Zeichnen ('cluster_one_reiterleiste'), ob
   das Produkt eine eigene hat. Fuer meine Seiten antworte ich mit meiner.
   20.10.2026 (Neo), 0.5.17: hier stand zusaetzlich
       if (function_exists('cluster_one_modul_da') && cluster_one_modul_da('landkarte')) {
           $c1[] = 'cluster_one_landkarte';
       }
   mit dem Satz „und fuer die Landkarte, die im selben Paket liegt". Die
   Landkarte liegt nicht mehr im Paket (siehe Zitat am Ende der Datei). */
function cluster_one_crockford_reiter_slugs(): array {
    return array_keys(cluster_one_crockford_seiten());
}

add_filter('cluster_one_reiterleiste', function ($c1_fn, $c1_seite) {
    if ($c1_fn !== '' || !is_string($c1_seite)) { return $c1_fn; }
    if (!in_array($c1_seite, cluster_one_crockford_reiter_slugs(), true)) { return $c1_fn; }
    return 'cluster_one_render_crockford_tabs';
}, 10, 2);

/* 28.10.2026 (Neo), Crockford 0.5.27 — MEINE LEISTE AUF ZURUF. Seit Boden 1.18.0
   zeichnen die Knoten-Seiten (Profil, System, Chronik, KI-Kennungen, Knoten-
   Einstellungen) oben die Leiste des Hauses, von dem der Leser kam, darunter die
   Knoten-Reihe — die Form des Kalenders (cluster_one_faust_leisten). Der Boden
   ruft die Aktion mit dem Wirt, der gemeint ist; ich vergleiche und zeichne nur
   dann. Keiner meiner Reiter traegt den Slug einer Knoten-Seite, der Boden
   faerbt dort den Reiter „Knoten" selbst. Dieselbe Gestalt wie bei Kleinrock
   (kleinrock.php) und Bismarck (bismarck.php). */
add_action('cluster_one_leiste', function ($c1_w, $c1_s) {
    if (!function_exists('cluster_one_kasper_wirt')) { return; }
    if ($c1_w !== cluster_one_kasper_wirt(__FILE__)) { return; }
    cluster_one_render_crockford_tabs((string) $c1_s);
}, 10, 2);

function cluster_one_render_crockford_tabs(string $c1_aktuell): void {
    if (!function_exists('cluster_one_render_tabs')) { return; }
    $c1_tabs = array();
    foreach (cluster_one_crockford_seiten() as $c1_slug => $c1_s) {
        /* Chi (03.11.2026, Neo), 0.6.6: bis 0.6.5 stand hier
               if ($c1_s['cap'] === 'cluster_one_kranz_save' && !(function_exists('cluster_one_is_sysop') && cluster_one_is_sysop())) {
                   continue; // Randbedingungen sind SysOp-Sache; ein Reiter ohne Seite dahinter ist keiner.
               }
           Jetzt die allgemeine Form: ein Reiter steht, wenn der Betrachter die Seite dahinter oeffnen darf — dieselbe Frage, die
           WordPress an der Tuer stellt (die Capability der Anmeldung). Ein Reiter ohne Seite dahinter ist keiner. */
        if (!current_user_can($c1_s['cap'])) {
            continue;
        }
        $c1_tabs[] = array('label' => $c1_s['reiter'], 'url' => admin_url('admin.php?page=' . $c1_slug), 'active' => $c1_aktuell === $c1_slug);
    }
    /* 20.10.2026 (Neo), 0.5.17: hier stand der dritte Reiter,
           if (in_array('cluster_one_landkarte', cluster_one_crockford_reiter_slugs(), true) && current_user_can('manage_options')) {
               $c1_tabs[] = array('label' => __('Map', 'cluster-one-crockford'), 'url' => admin_url('admin.php?page=cluster_one_landkarte'), 'active' => $c1_aktuell === 'cluster_one_landkarte');
           }
       Die Landkarte steht in Turing unter Kasper; ein Reiter auf eine Seite,
       die es nicht mehr gibt, waere ein Weg ins Leere. */
    cluster_one_render_tabs($c1_tabs);
}

/* ---------------------------------------------------------------------------
 * LESEN: was Bricks ueber diesen Knoten weiss.
 * ------------------------------------------------------------------------- */

/** Die Elemente eines Beitrags, als flache Liste. Leer heisst: kein Bricks-Inhalt. */
function cluster_one_crockford_elemente(int $c1_id, string $c1_schluessel = '_bricks_page_content_2'): array {
    $c1_e = get_post_meta($c1_id, $c1_schluessel, true);
    if (is_string($c1_e) && $c1_e !== '') {
        $c1_j = json_decode($c1_e, true);
        $c1_e = is_array($c1_j) ? $c1_j : array();
    }
    if (!is_array($c1_e)) { return array(); }
    $c1_aus = array();
    foreach ($c1_e as $c1_el) {
        if (is_array($c1_el) && isset($c1_el['id'])) { $c1_aus[] = $c1_el; }
    }
    return $c1_aus;
}

/** Globale Klassen, roh: id -> ganzer Eintrag (name, settings, …). */
function cluster_one_crockford_klassen_roh(): array {
    $c1_roh = get_option('bricks_global_classes', array());
    $c1_aus = array();
    if (is_array($c1_roh)) {
        foreach ($c1_roh as $c1_k) {
            if (is_array($c1_k) && isset($c1_k['id'])) { $c1_aus[(string) $c1_k['id']] = $c1_k; }
        }
    }
    return $c1_aus;
}

/** Globale Klassen: id -> Name. Ohne Option leer, und dann bleiben ids stehen. */
function cluster_one_crockford_klassen(): array {
    $c1_aus = array();
    foreach (cluster_one_crockford_klassen_roh() as $c1_id => $c1_k) {
        $c1_aus[$c1_id] = (string) ($c1_k['name'] ?? $c1_id);
    }
    return $c1_aus;
}

/** Die Einstellungen der benutzten Klassen, wie Bricks sie speichert: Steuer-
 *  werte, kein kompiliertes CSS. `_cssCustom` ist woertliches CSS und wird so
 *  ausgegeben; der Rest als kompaktes JSON. Was eine KI daraus liest, ist
 *  mehr, als sie aus dem Namen raten kann — und es ist nicht erfunden. */
function cluster_one_crockford_klassen_css(array $c1_ids): array {
    $c1_roh = cluster_one_crockford_klassen_roh();
    $c1_z   = array();
    foreach ($c1_ids as $c1_id) {
        if (!isset($c1_roh[$c1_id])) { continue; }
        $c1_k = $c1_roh[$c1_id];
        $c1_s = is_array($c1_k['settings'] ?? null) ? $c1_k['settings'] : array();
        $c1_name = (string) ($c1_k['name'] ?? $c1_id);
        if ($c1_s === array()) { $c1_z[] = '.' . $c1_name . ': (no settings stored)'; continue; }
        $c1_custom = isset($c1_s['_cssCustom']) && is_string($c1_s['_cssCustom']) ? trim($c1_s['_cssCustom']) : '';
        unset($c1_s['_cssCustom']);
        $c1_z[] = '.' . $c1_name . ':';
        if ($c1_s !== array()) {
            $c1_z[] = '  controls: ' . cluster_one_crockford_kurz((string) wp_json_encode($c1_s, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 900);
        }
        if ($c1_custom !== '') {
            $c1_z[] = '  custom CSS: ' . cluster_one_crockford_kurz(str_replace('%root%', '.' . $c1_name, $c1_custom), 900);
        }
    }
    return $c1_z;
}

/** Sprache und Uebersetzungen eines Beitrags — ueber Polylang, wenn es da ist.
 *  Ohne Polylang: '' und leer, also „nicht feststellbar", nicht „eine Sprache". */
function cluster_one_crockford_sprache(int $c1_id): array {
    $c1_lang = function_exists('pll_get_post_language') ? (string) pll_get_post_language($c1_id, 'slug') : '';
    $c1_tr   = array();
    if (function_exists('pll_get_post_translations')) {
        foreach ((array) pll_get_post_translations($c1_id) as $c1_l => $c1_tid) {
            if ((int) $c1_tid !== $c1_id && (int) $c1_tid > 0) { $c1_tr[(string) $c1_l] = (int) $c1_tid; }
        }
    }
    return array('lang' => $c1_lang, 'translations' => $c1_tr, 'bekannt' => function_exists('pll_get_post_language'));
}

/** Die drei Schluessel, unter denen Bricks Elemente ablegt. Header- und
 *  Footer-Vorlagen tragen NUR den zweiten bzw. dritten — gemessen am 19.10.2026
 *  auf DEV, wo das Inventar sie mit dem Inhalts-Schluessel allein nicht fand. */
function cluster_one_crockford_schluessel(): array {
    return array(
        '_bricks_page_content_2' => 'Content',
        '_bricks_page_header_2'  => 'Header elements',
        '_bricks_page_footer_2'  => 'Footer elements',
    );
}

/** Alle Elemente eines Beitrags, nach Schluessel. Leer heisst: kein Bricks-Inhalt. */
function cluster_one_crockford_elemente_alle(int $c1_id): array {
    $c1_aus = array();
    foreach (cluster_one_crockford_schluessel() as $c1_k => $c1_w) {
        $c1_el = cluster_one_crockford_elemente($c1_id, $c1_k);
        if ($c1_el !== array()) { $c1_aus[$c1_k] = $c1_el; }
    }
    return $c1_aus;
}

/** Das Inventar: jeder Beitrag mit Bricks-Inhalt, in jeder Sprache.
 *  Reihenfolge: Seiten, Beitraege, uebrige Typen, Vorlagen zuletzt; innerhalb nach Titel. */
/* RUNDE 35 (30.10.2026, Neo), 0.5.33: WER ALLES SEHEN DARF, SAGT DER RUFER.
   Bis 0.5.32 las das Inventar fuer jeden, der die Seite erreichte, alle fuenf
   Zustaende – hinter der Weissliste allein. Nemesus: „Du kannst ja damit die
   gesamte Webseite auslesen?" Ein Entwurf, ein privater Beitrag, ein geplanter
   Text sind nicht oeffentlich; wer nur Mitglied ist, bekommt, was ohnehin jeder
   im Netz sieht. Die anderen vier Zustaende gibt es mit cluster_one_crockford_voll(). */
function cluster_one_crockford_inventar(bool $c1_voll = false): array {
    if (!class_exists('WP_Query')) { return array(); }
    $c1_typen = array_values(get_post_types(array(), 'names'));
    $c1_meta  = array('relation' => 'OR');
    foreach (array_keys(cluster_one_crockford_schluessel()) as $c1_k) {
        $c1_meta[] = array('key' => $c1_k, 'compare' => 'EXISTS');
    }
    $c1_q = new WP_Query(array(
        'post_type'      => $c1_typen,
        'post_status'    => $c1_voll ? array('publish', 'draft', 'pending', 'private', 'future') : array('publish'),
        'posts_per_page' => 500,
        'meta_query'     => $c1_meta, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Suche nach Benutzern ueber ein Meta (Besitz, Recht, Speicher): einmal je Aufruf einer Admin-Seite bzw. der Wanderung, kleine Knoten
        'orderby'        => 'title',
        'order'          => 'ASC',
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'lang'           => '', // Polylang: alle Sprachen, nicht nur die des Admin-Filters
    ));
    $c1_aus = array();
    foreach ((array) $c1_q->posts as $c1_id) {
        $c1_id = (int) $c1_id;
        $c1_alle = cluster_one_crockford_elemente_alle($c1_id);
        if ($c1_alle === array()) { continue; }
        $c1_p  = get_post($c1_id);
        if (!$c1_p) { continue; }
        $c1_sektionen = array();
        $c1_n = 0;
        foreach ($c1_alle as $c1_el) {
            $c1_n += count($c1_el);
            foreach ($c1_el as $c1_e) {
                if ((int) ($c1_e['parent'] ?? 0) === 0) {
                    $c1_sektionen[] = (string) ($c1_e['label'] ?? $c1_e['name'] ?? '');
                }
            }
        }
        $c1_typ = get_post_type_object($c1_p->post_type);
        $c1_aus[$c1_id] = array(
            'id'        => $c1_id,
            'titel'     => (string) $c1_p->post_title,
            'typ'       => (string) $c1_p->post_type,
            'typ_label' => $c1_typ ? (string) $c1_typ->labels->singular_name : (string) $c1_p->post_type,
            'vorlage'   => $c1_p->post_type === 'bricks_template' ? (string) get_post_meta($c1_id, '_bricks_template_type', true) : '',
            'status'    => (string) $c1_p->post_status,
            'url'       => (string) get_permalink($c1_id),
            'elemente'  => $c1_n,
            'sektionen' => $c1_sektionen,
            'geaendert' => (string) $c1_p->post_modified_gmt,
            'sprache'   => cluster_one_crockford_sprache($c1_id),
        );
    }
    $c1_rang = function (array $c1_i): int {
        if ($c1_i['typ'] === 'page') { return 0; }
        if ($c1_i['typ'] === 'post') { return 1; }
        if ($c1_i['typ'] === 'bricks_template') { return 3; }
        return 2;
    };
    uasort($c1_aus, function ($a, $b) use ($c1_rang) {
        $c1_d = $c1_rang($a) <=> $c1_rang($b);
        return $c1_d !== 0 ? $c1_d : strcasecmp($a['titel'], $b['titel']);
    });
    return $c1_aus;
}

/** Ein Text auf eine Zeile, ohne Tags, gekuerzt. */
function cluster_one_crockford_kurz(string $c1_t, int $c1_max = 160): string {
    $c1_t = trim(preg_replace('~\s+~u', ' ', wp_strip_all_tags($c1_t)));
    if (function_exists('mb_strlen') && mb_strlen($c1_t) > $c1_max) {
        return mb_substr($c1_t, 0, $c1_max - 1) . '…';
    }
    return $c1_t;
}

/* ---------------------------------------------------------------------------
 * MEDIEN — 20.10.2026 (Neo), 0.5.18. Nemesus: „Die KI soll zum Experten der
 * Seite werden", also auch wissen, wo die Dateien liegen. Bis 0.5.17 stand ein
 * Bild im Text als `image "Hero"` und sonst nichts.
 *
 * DIE SCHLUESSEL WERDEN NICHT GERATEN. Bricks legt Anhaenge an vielen Stellen
 * ab (image, _background.image, items.images, media, file, icon.svg, logo,
 * videoPoster …), und die Liste waechst mit jeder Bricks-Fassung. Gelesen wird
 * deshalb die FORM: jeder Eintrag in den Settings mit numerischer `id` und `url`
 * oder `filename` ist ein Anhang; jede Zeichenkette, die eine reine URL auf eine
 * Mediendatei ist, ist eine Datei (videoUrl, fileUrl, ein Link auf ein PDF).
 * Der Pfad der Schluessel nennt die Rolle. Ein Eintrag `useDynamicData` ist ein
 * Platzhalter und wird als solcher genannt, nicht aufgeloest.
 *
 * DIE WAHRHEIT LIEGT IN DER MEDIATHEK, nicht im JSON: Bricks speichert URL und
 * Dateiname als Momentaufnahme vom Einfuegen. Aufgeloest wird ueber die ID
 * (wp_get_attachment_url, Metadaten, Alt-Text). Eine ID, die es nicht mehr gibt,
 * wird GEMELDET, mit der gespeicherten URL daneben — nicht still weggelassen.
 * ------------------------------------------------------------------------- */

/** Die Medien in den Settings eines Elements, als Liste von
 *  array('rolle' => 'image' | '_background.image' | …, 'id' => int, 'url' => string, 'dynamisch' => string). */
function cluster_one_crockford_medien(array $c1_s): array {
    $c1_aus = array();
    $c1_geh = function ($c1_wert, string $c1_pfad) use (&$c1_geh, &$c1_aus) {
        if (is_array($c1_wert)) {
            if (isset($c1_wert['id']) && is_numeric($c1_wert['id']) && (isset($c1_wert['url']) || isset($c1_wert['filename']))) {
                $c1_aus[] = array('rolle' => $c1_pfad, 'id' => (int) $c1_wert['id'], 'url' => (string) ($c1_wert['url'] ?? ''), 'dynamisch' => '');
                return; /* in einem Anhang steckt kein zweiter */
            }
            if (isset($c1_wert['useDynamicData']) && is_string($c1_wert['useDynamicData']) && $c1_wert['useDynamicData'] !== '') {
                $c1_aus[] = array('rolle' => $c1_pfad, 'id' => 0, 'url' => '', 'dynamisch' => $c1_wert['useDynamicData']);
                return;
            }
            foreach ($c1_wert as $c1_k => $c1_v) {
                $c1_geh($c1_v, is_int($c1_k) ? $c1_pfad : ($c1_pfad === '' ? (string) $c1_k : $c1_pfad . '.' . $c1_k));
            }
            return;
        }
        if (is_string($c1_wert) && preg_match('~^https?://\S+\.(jpe?g|png|gif|webp|avif|svg|mp4|webm|ogv|mp3|wav|m4a|pdf)(\?\S*)?$~i', $c1_wert)) {
            $c1_aus[] = array('rolle' => $c1_pfad, 'id' => 0, 'url' => $c1_wert, 'dynamisch' => '');
        }
    };
    $c1_geh($c1_s, '');
    return $c1_aus;
}

/** Ein Anhang, aus der Mediathek gelesen. 'da' => false heisst: die ID gibt es nicht (mehr). */
function cluster_one_crockford_medium(int $c1_id): array {
    static $c1_merk = array();
    if (isset($c1_merk[$c1_id])) { return $c1_merk[$c1_id]; }
    $c1_m = array('da' => false, 'url' => '', 'mime' => '', 'breite' => 0, 'hoehe' => 0, 'alt' => '');
    if ($c1_id > 0 && get_post_type($c1_id) === 'attachment') {
        $c1_m['da']   = true;
        $c1_m['url']  = (string) wp_get_attachment_url($c1_id);
        $c1_m['mime'] = (string) get_post_mime_type($c1_id);
        $c1_meta = wp_get_attachment_metadata($c1_id);
        if (is_array($c1_meta)) {
            $c1_m['breite'] = (int) ($c1_meta['width'] ?? 0);
            $c1_m['hoehe']  = (int) ($c1_meta['height'] ?? 0);
        }
        $c1_m['alt'] = (string) get_post_meta($c1_id, '_wp_attachment_image_alt', true);
    }
    return $c1_merk[$c1_id] = $c1_m;
}

/** Die Medien eines Elements als Textstuecke, je Rolle eines: `[background image: URL 1600x900 alt "…"]`. */
function cluster_one_crockford_medien_text(array $c1_s): array {
    $c1_gruppen = array();
    foreach (cluster_one_crockford_medien($c1_s) as $c1_md) {
        /* Die Rolle, lesbar: `_background.image` -> background image, `items.images` -> gallery image. */
        $c1_rolle = (string) $c1_md['rolle'];
        if ($c1_rolle === 'items.images') { $c1_rolle = 'gallery image'; }
        $c1_rolle = trim(str_replace(array('_', '.'), ' ', $c1_rolle));
        if ($c1_md['dynamisch'] !== '') {
            $c1_gruppen[$c1_rolle][] = 'dynamic ' . $c1_md['dynamisch'];
            continue;
        }
        $c1_m = $c1_md['id'] > 0 ? cluster_one_crockford_medium((int) $c1_md['id']) : null;
        if ($c1_m !== null && $c1_m['da']) {
            $c1_t = $c1_m['url'];
            if ($c1_m['breite'] > 0) { $c1_t .= ' ' . $c1_m['breite'] . 'x' . $c1_m['hoehe']; }
            if ($c1_m['mime'] !== '' && strpos($c1_m['mime'], 'image/') !== 0) {
                $c1_t .= ' ' . $c1_m['mime'];           /* keine Bilddatei: ein Alt-Text ist hier nicht die Frage */
            } else {
                $c1_t .= $c1_m['alt'] !== '' ? ' alt "' . cluster_one_crockford_kurz($c1_m['alt'], 120) . '"' : ' (no alt text)';
            }
        } elseif ($c1_md['id'] > 0) {
            $c1_t = 'id ' . (int) $c1_md['id'] . ' is not in the media library' . ($c1_md['url'] !== '' ? ', stored URL ' . $c1_md['url'] : '');
        } else {
            $c1_t = $c1_md['url'] . ' (URL, not a media library item)';
        }
        $c1_gruppen[$c1_rolle][] = $c1_t;
    }
    $c1_aus = array();
    foreach ($c1_gruppen as $c1_rolle => $c1_liste) {
        $c1_aus[] = '[' . $c1_rolle . (count($c1_liste) > 1 ? ' (' . count($c1_liste) . ')' : '') . ': ' . implode('; ', $c1_liste) . ']';
    }
    return $c1_aus;
}

/** Eine Zeile je Element: Typ, Label, Tag, Klassen, Text, Link, Loop, Formular — und mit $c1_mit_medien die Dateien. */
function cluster_one_crockford_element_zeile(array $c1_e, array $c1_klassen, bool $c1_nur_struktur = false, bool $c1_mit_medien = false): string {
    $c1_s   = is_array($c1_e['settings'] ?? null) ? $c1_e['settings'] : array();
    $c1_z   = (string) ($c1_e['name'] ?? '?');
    if (!empty($c1_e['cid'])) { $c1_z .= ' [component ' . (string) $c1_e['cid'] . ']'; }
    if (!empty($c1_e['label'])) { $c1_z .= ' "' . cluster_one_crockford_kurz((string) $c1_e['label'], 80) . '"'; }
    /* Eckige statt spitze Klammern: der Text wird auch Praeambel eines Themas,
       und dort reist kein HTML — ein <h1> waere beim Ablegen verschwunden. */
    if (!empty($c1_s['tag']) && is_string($c1_s['tag'])) { $c1_z .= ' [' . $c1_s['tag'] . ']'; }
    if (!empty($c1_s['_cssGlobalClasses']) && is_array($c1_s['_cssGlobalClasses'])) {
        $c1_n = array();
        foreach ($c1_s['_cssGlobalClasses'] as $c1_cid) { $c1_n[] = '.' . ($c1_klassen[(string) $c1_cid] ?? (string) $c1_cid); }
        $c1_z .= ' ' . implode(' ', $c1_n);
    }
    if (!$c1_nur_struktur && isset($c1_s['text']) && is_string($c1_s['text']) && trim($c1_s['text']) !== '') {
        $c1_z .= ' — "' . cluster_one_crockford_kurz($c1_s['text']) . '"';
    }
    if (!empty($c1_s['link']['url']) && is_string($c1_s['link']['url'])) { $c1_z .= ' -> ' . $c1_s['link']['url']; }
    if (!empty($c1_s['hasLoop'])) {
        $c1_q = is_array($c1_s['query'] ?? null) ? $c1_s['query'] : array();
        $c1_z .= ' [loop' . ($c1_q ? ': ' . cluster_one_crockford_kurz((string) wp_json_encode($c1_q, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 200) : '') . ']';
    }
    if ($c1_z !== '' && ($c1_e['name'] ?? '') === 'form' && is_array($c1_s['fields'] ?? null)) {
        $c1_f = array();
        foreach ($c1_s['fields'] as $c1_fld) {
            if (!is_array($c1_fld)) { continue; }
            if (!empty($c1_fld['isHoneypot'])) { continue; } // der Honigtopf bleibt geheim, sonst ist er keiner
            $c1_f[] = cluster_one_crockford_kurz((string) ($c1_fld['label'] ?? $c1_fld['type'] ?? ''), 40)
                . ' (' . (string) ($c1_fld['type'] ?? '?') . (!empty($c1_fld['required']) ? ', required' : '') . ')';
        }
        $c1_z .= ' [form fields: ' . implode(', ', $c1_f) . ']';
        /* Absichtlich NICHT: emailTo, emailBcc, fromEmail, emailContent — Adressen
           reisen nicht, auch nicht geschrubbt. */
    }
    if ($c1_mit_medien) {
        foreach (cluster_one_crockford_medien_text($c1_s) as $c1_md) { $c1_z .= ' ' . $c1_md; }
    }
    return $c1_z;
}

/** Der Baum eines Beitrags als eingerueckte Zeilen. */
function cluster_one_crockford_baum(array $c1_elemente, array $c1_klassen, bool $c1_nur_struktur = false, bool $c1_mit_medien = false): array {
    $c1_nach_id = array();
    foreach ($c1_elemente as $c1_e) { $c1_nach_id[(string) $c1_e['id']] = $c1_e; }
    $c1_zeilen = array();
    $c1_gesehen = array();
    $c1_geh = function ($c1_id, int $c1_tiefe) use (&$c1_geh, &$c1_zeilen, &$c1_gesehen, $c1_nach_id, $c1_klassen, $c1_nur_struktur, $c1_mit_medien) {
        $c1_id = (string) $c1_id;
        if (!isset($c1_nach_id[$c1_id]) || isset($c1_gesehen[$c1_id]) || $c1_tiefe > 40) { return; }
        $c1_gesehen[$c1_id] = true;
        $c1_e = $c1_nach_id[$c1_id];
        $c1_zeilen[] = str_repeat('  ', $c1_tiefe) . '- ' . cluster_one_crockford_element_zeile($c1_e, $c1_klassen, $c1_nur_struktur, $c1_mit_medien);
        foreach ((array) ($c1_e['children'] ?? array()) as $c1_k) { $c1_geh($c1_k, $c1_tiefe + 1); }
    };
    foreach ($c1_elemente as $c1_e) {
        if ((int) ($c1_e['parent'] ?? 0) === 0) { $c1_geh($c1_e['id'], 0); }
    }
    /* Was kein Elter erreicht hat (verwaist), steht trotzdem da — benannt. */
    foreach ($c1_elemente as $c1_e) {
        if (!isset($c1_gesehen[(string) $c1_e['id']])) {
            $c1_zeilen[] = '- (orphan) ' . cluster_one_crockford_element_zeile($c1_e, $c1_klassen, $c1_nur_struktur, $c1_mit_medien);
            $c1_gesehen[(string) $c1_e['id']] = true;
        }
    }
    return $c1_zeilen;
}

/** DER TEXT. Einer fuer beide Wege (Ausfuhrtuer und Raum), wie bei den
 *  Randbedingungen: kein Parameter, der ihn je nach WEG veraendert. Der zweite
 *  Parameter aendert ihn je nach UMFANG — Struktur mit oder ohne Textinhalte —
 *  und gilt auf beiden Wegen gleich.
 *  KEIN SCHRUBBER: alles hier ist oeffentlicher Seiteninhalt (Impressum
 *  eingeschlossen); der Schrubber der Randbedingungen hat am 19.10.2026 auf DEV
 *  „Umsatzsteuer-Identifikationsnummer" fuer einen Schluessel gehalten. Was
 *  nicht oeffentlich ist — Formular-Empfaenger, Honigtopf — wird gar nicht
 *  erst in den Text geschrieben.
 *  @param int[] $c1_ids leer = alles aus dem Inventar */
function cluster_one_crockford_kontext_text(array $c1_ids = array(), bool $c1_nur_struktur = false, bool $c1_mit_theme = false, bool $c1_mit_css = false, bool $c1_mit_medien = false): string {
    $c1_inv = cluster_one_crockford_inventar();
    $c1_ids = array_values(array_filter(array_map('intval', $c1_ids)));
    if ($c1_ids === array()) { $c1_ids = array_keys($c1_inv); }
    /* Reihenfolge des Inventars, nicht der Haken. */
    $c1_ids = array_values(array_intersect(array_keys($c1_inv), $c1_ids));
    $c1_klassen = cluster_one_crockford_klassen();
    $c1_benutzt = array();
    $c1_medien  = array();   /* 0.5.18: id => Anhang, ueber alle gewaehlten Beitraege */
    $c1_medien_fremd = 0;    /* Verweise auf IDs, die die Mediathek nicht kennt */
    $c1_z = array();
    $c1_z[] = 'Site structure of this WordPress site as built in Bricks, read from the database. This is the current build, not a plan. Dynamic tags such as {post_title} are placeholders filled at render time; a [loop] block repeats for every post its query returns.'
        . ($c1_nur_struktur ? ' Text contents are left out on purpose; only the structure is given.' : ' Text contents are shortened to one line each.')
        . ($c1_mit_medien ? ' Media files are given in square brackets per element with their URL in the media library, pixel size and alt text; a list of all files used follows at the end.' : ' Media files are not included; ask for them if a change depends on an image or a file.');
    $c1_z[] = sprintf('Site: %s. Bricks: %s. Items included: %d of %d with Bricks content.',
        home_url('/'), defined('BRICKS_VERSION') ? BRICKS_VERSION : 'version not readable here', count($c1_ids), count($c1_inv));
    $c1_hash = array();
    foreach ($c1_ids as $c1_id) {
        $c1_i    = $c1_inv[$c1_id];
        $c1_alle = cluster_one_crockford_elemente_alle($c1_id);
        $c1_el   = $c1_alle['_bricks_page_content_2'] ?? array();
        $c1_hash[] = $c1_id . ':' . md5(serialize($c1_alle));
        $c1_kopf = sprintf('## %s (%s%s%s%s)', $c1_i['titel'] !== '' ? $c1_i['titel'] : '#' . $c1_id,
            $c1_i['typ'],
            $c1_i['vorlage'] !== '' ? ', template type ' . $c1_i['vorlage'] : '',
            $c1_i['sprache']['lang'] !== '' ? ', language ' . $c1_i['sprache']['lang'] : '',
            $c1_i['status'] !== 'publish' ? ', status ' . $c1_i['status'] : '');
        $c1_z[] = '';
        $c1_z[] = $c1_kopf;
        if ($c1_i['typ'] !== 'bricks_template') { $c1_z[] = 'URL: ' . $c1_i['url']; }
        if ($c1_i['sprache']['translations'] !== array()) {
            $c1_t = array();
            foreach ($c1_i['sprache']['translations'] as $c1_l => $c1_tid) {
                $c1_t[] = $c1_l . ': "' . get_the_title($c1_tid) . '"' . (isset($c1_inv[$c1_tid]) ? '' : ' (no Bricks content)');
            }
            $c1_z[] = 'Translations of this item: ' . implode(' · ', $c1_t);
        }
        if ($c1_i['typ'] === 'bricks_template') {
            $c1_ts = get_post_meta($c1_id, '_bricks_template_settings', true);
            if (is_array($c1_ts) && !empty($c1_ts['templateConditions'])) {
                $c1_z[] = 'Template conditions: ' . cluster_one_crockford_kurz((string) wp_json_encode($c1_ts['templateConditions'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 300);
            }
        }
        foreach (cluster_one_crockford_schluessel() as $c1_k => $c1_w) {
            if (empty($c1_alle[$c1_k])) { continue; }
            if ($c1_k !== '_bricks_page_content_2') { $c1_z[] = $c1_w . ':'; }
            foreach (cluster_one_crockford_baum($c1_alle[$c1_k], $c1_klassen, $c1_nur_struktur, $c1_mit_medien) as $c1_l) { $c1_z[] = $c1_l; }
            foreach ($c1_alle[$c1_k] as $c1_e) {
                foreach ((array) ($c1_e['settings']['_cssGlobalClasses'] ?? array()) as $c1_cid) { $c1_benutzt[(string) $c1_cid] = true; }
                if ($c1_mit_medien && is_array($c1_e['settings'] ?? null)) {
                    foreach (cluster_one_crockford_medien($c1_e['settings']) as $c1_md) {
                        if ($c1_md['id'] <= 0) { continue; }
                        $c1_m = cluster_one_crockford_medium((int) $c1_md['id']);
                        if ($c1_m['da']) { $c1_medien[(int) $c1_md['id']] = $c1_m; } else { $c1_medien_fremd++; }
                    }
                }
            }
        }
    }
    if ($c1_mit_medien) {
        /* Die Liste aller Dateien, einmal je Anhang, mit dem Ort, an dem die
           Mediathek liegt — damit die KI die Upload-Struktur des Knotens kennt
           und nicht nur einzelne Pfade. */
        $c1_ud = function_exists('wp_upload_dir') ? wp_upload_dir() : array();
        $c1_z[] = '';
        $c1_z[] = 'Media library: ' . (is_array($c1_ud) && !empty($c1_ud['baseurl']) ? $c1_ud['baseurl'] : 'base URL not readable here')
            . '. Files used above (' . count($c1_medien) . '):';
        ksort($c1_medien);
        foreach ($c1_medien as $c1_mid => $c1_m) {
            $c1_z[] = '- ' . $c1_m['url'] . ' (id ' . (int) $c1_mid
                . ($c1_m['mime'] !== '' ? ', ' . $c1_m['mime'] : '')
                . ($c1_m['breite'] > 0 ? ', ' . $c1_m['breite'] . 'x' . $c1_m['hoehe'] : '')
                . (strpos($c1_m['mime'], 'image/') === 0
                    ? ($c1_m['alt'] !== '' ? ', alt "' . cluster_one_crockford_kurz($c1_m['alt'], 120) . '"' : ', no alt text')
                    : '') . ')';
        }
        if ($c1_medien_fremd > 0) {
            $c1_z[] = sprintf('%d reference(s) above point to ids the media library does not know; the stored URL is given there and may be stale.', $c1_medien_fremd);
        }
    }
    if ($c1_benutzt !== array()) {
        $c1_n = array();
        foreach (array_keys($c1_benutzt) as $c1_cid) { $c1_n[] = $c1_klassen[$c1_cid] ?? ($c1_cid . ' (name not found)'); }
        sort($c1_n);
        $c1_z[] = '';
        $c1_z[] = 'Global classes used above: ' . implode(', ', $c1_n) . '.'
            . ($c1_mit_css ? '' : ' Their CSS is not included; ask for it if a change depends on it.');
        if ($c1_mit_css) {
            $c1_z[] = '';
            $c1_z[] = 'Settings of these classes as Bricks stores them — control values, not compiled CSS. "custom CSS" lines are literal CSS written by the site owner.';
            foreach (cluster_one_crockford_klassen_css(array_keys($c1_benutzt)) as $c1_l) { $c1_z[] = $c1_l; }
        }
    }
    if ($c1_mit_theme) {
        /* Die Kurzkarte des Themes, ueber den Vertrag, den die Randbedingungen
           seit 0.6.0 benutzen. Antwortet niemand, steht das da — nicht nichts. */
        $c1_dna = (string) apply_filters('cluster_one_erklaerung', '', 'dna');
        $c1_z[] = '';
        $c1_z[] = $c1_dna !== ''
            ? 'The theme documents itself for AI readers. Its short map, read live from the installed version:' . "\n" . trim($c1_dna)
            : 'A theme short map was requested, but nothing on this node answers for one — the theme either has none or the organ that reads it is absent.';
    }
    $c1_z[] = '';
    $c1_z[] = sprintf('Snapshot %s, taken %s UTC. Same id means same build.', substr(hash('sha256', implode("\n", $c1_hash)), 0, 16), gmdate('Y-m-d H:i', current_time('timestamp', 1)));
    return implode("\n", $c1_z);
}

/* DER VERTRAG NACH AUSSEN — fuer einen Raum, der einer KI den Site-Kontext
   geben will, ohne diese Datei beim Namen zu kennen:
       apply_filters('cluster_one_site_kontext', '', 'text', $ids)   der Text
       apply_filters('cluster_one_site_kontext', '', 'medien', $ids) der Text mit Mediendateien (0.5.18)
       apply_filters('cluster_one_site_kontext', array(), 'inventar') das Inventar
   Ohne Crockford kommt die Vorgabe zurueck, und die ist leer. */
add_filter('cluster_one_site_kontext', function ($c1_wert, $c1_was = '', $c1_ids = array()) {
    switch ((string) $c1_was) {
        case 'text':     return cluster_one_crockford_kontext_text(is_array($c1_ids) ? $c1_ids : array());
        case 'struktur': return cluster_one_crockford_kontext_text(is_array($c1_ids) ? $c1_ids : array(), true);
        case 'medien':   return cluster_one_crockford_kontext_text(is_array($c1_ids) ? $c1_ids : array(), false, false, false, true);
        case 'inventar': return cluster_one_crockford_inventar();
    }
    return $c1_wert;
}, 10, 3);

/* ---------------------------------------------------------------------------
 * DIE SEITE: SITE-KONTEXT
 * ------------------------------------------------------------------------- */
function cluster_one_render_crockford_page(): void {
    if (function_exists('cluster_one_page_gate')) { cluster_one_page_gate(); }
    /* RUNDE 35, 0.5.33: das Recht entscheidet, was die Seite zeigt und tut. Ohne
       es: nur Veroeffentlichtes, keine Haken, kein Text. Mit ihm: wie bisher. */
    $c1_voll = cluster_one_crockford_voll();
    $c1_inv  = cluster_one_crockford_inventar($c1_voll);

    $c1_gewaehlt = array();
    $c1_text     = '';
    $c1_nur_struktur = false;
    $c1_mit_theme    = false;
    $c1_mit_css      = false;
    $c1_mit_medien   = false;
    $c1_gesendet     = false;
    /* 0.5.22 (27.10.2026, Neo) — DIE HAKEN BLEIBEN. Nemesus, Test 3: 18 von 22
       angehakt, Text gebaut, Seite neu geladen – alles wieder leer, und beim
       naechsten Mal dieselben vier wieder abhaken. Gemerkt wird je Mensch
       (User-Meta, nicht Option): die Auswahl ist seine Arbeitsliste, nicht
       die des Knotens. Gemerkt wird NUR beim Bauen, nie beim Anhaken allein.
       Bis 0.5.21 stand im Einleitungssatz „Nothing here is stored" – das
       stimmt seit heute fuer die Haken nicht mehr, der Satz sagt es. */
    $c1_uid = get_current_user_id();
    if (isset($_POST['c1_crockford_ausfuhr']) && check_admin_referer('cluster_one_crockford_ausfuhr')) {
        /* Der Knopf steht ohne Recht nicht auf der Seite; ein POST ohne Recht ist
           deshalb kein Bedienfehler, sondern ein gebauter Aufruf. Laut, nicht leise. */
        if (!$c1_voll) {
            wp_die(esc_html__('Not permitted: composing the site context needs a release by the SysOp, per person.', 'cluster-one-crockford'), '', 403);
        }
        $c1_gesendet = true;
        $c1_gewaehlt = isset($_POST['c1_ids']) && is_array($_POST['c1_ids']) ? array_map('intval', $_POST['c1_ids']) : array();
        $c1_nur_struktur = !empty($_POST['c1_nur_struktur']);
        $c1_mit_theme    = !empty($_POST['c1_mit_theme']);
        $c1_mit_css      = !empty($_POST['c1_mit_css']);
        $c1_mit_medien   = !empty($_POST['c1_mit_medien']);
        if ($c1_gewaehlt !== array()) { $c1_text = cluster_one_crockford_kontext_text($c1_gewaehlt, $c1_nur_struktur, $c1_mit_theme, $c1_mit_css, $c1_mit_medien); }
        if ($c1_uid > 0) {
            update_user_meta($c1_uid, 'cluster_one_crockford_auswahl', array(
                'ids' => $c1_gewaehlt, 'nur_struktur' => $c1_nur_struktur, 'mit_theme' => $c1_mit_theme,
                'mit_css' => $c1_mit_css, 'mit_medien' => $c1_mit_medien,
            ));
        }
    } elseif ($c1_uid > 0) {
        $c1_gemerkt = get_user_meta($c1_uid, 'cluster_one_crockford_auswahl', true);
        if (is_array($c1_gemerkt)) {
            /* Nur, was es noch gibt: eine geloeschte Seite verschwindet aus der Auswahl, still ist das
               nicht – sie fehlt dann sichtbar in der Tabelle. */
            $c1_gewaehlt = array_values(array_filter(array_map('intval', (array) ($c1_gemerkt['ids'] ?? array())), function ($c1_id) use ($c1_inv) { return isset($c1_inv[$c1_id]); }));
            $c1_nur_struktur = !empty($c1_gemerkt['nur_struktur']);
            $c1_mit_theme    = !empty($c1_gemerkt['mit_theme']);
            $c1_mit_css      = !empty($c1_gemerkt['mit_css']);
            $c1_mit_medien   = !empty($c1_gemerkt['mit_medien']);
        }
    }
    $c1_laenge = function_exists('mb_strlen') ? mb_strlen($c1_text) : strlen($c1_text);
    ?>
    <div class="wrap">
        <h1><?php echo function_exists('cluster_one_page_h1') ? cluster_one_page_h1('Crockford') : esc_html__('Cluster One — Site context', 'cluster-one-crockford'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- beide Zweige escapt: cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase), der Rueckfall rechts ist esc_html__() ?></h1>
        <?php /* 0.5.25 (27.10.2026, Neo): der Stempel – Fassung, Werft, Boden-Stand (Boden 1.17.6). */
        if (function_exists('cluster_one_stempel')) { echo cluster_one_stempel(__FILE__, defined('CLUSTER_ONE_CROCKFORD_VERSION') ? (string) CLUSTER_ONE_CROCKFORD_VERSION : ''); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapt in der Funktion
        cluster_one_render_crockford_tabs('cluster_one_crockford'); ?>

        <?php if ($c1_voll) : ?>
        <p style="max-width:820px;"><?php esc_html_e('What Bricks knows about this site, read from the database: every page, post and template with Bricks content. Tick what an AI should know about, then compose the text. Your tick marks are remembered for next time, for you alone; nothing else is stored and nothing is sent. The text leaves this node only when you copy it or a room hands it to an AI.', 'cluster-one-crockford'); ?></p>
        <?php else : ?>
        <p style="max-width:820px;"><?php esc_html_e('What Bricks knows about this site, read from the database: every published page, post and template with Bricks content. You see the list; composing the text for an AI — and seeing drafts, scheduled or private items — needs a release by the SysOp, per person. The SysOp grants it in the “Release” section at the bottom of this page.', 'cluster-one-crockford'); // Omega: bis 0.6.7 „…in the WordPress users list." ?></p>
        <?php endif; ?>
        <?php
        if (isset($_GET['ablegen'])) {
            $c1_f = sanitize_key((string) wp_unslash($_GET['ablegen']));
            $c1_saetze = array(
                'kein_organ' => __('Nothing was laid down: no organ on this node answers for threads.', 'cluster-one-crockford'),
                'nicht_sysop' => __('Nothing was laid down: only the SysOp may do this.', 'cluster-one-crockford'),
                'kein_name'  => __('Nothing was laid down: a new thread needs a name.', 'cluster-one-crockford'),
                'kein_text'  => __('Nothing was laid down: the selection produced no text.', 'cluster-one-crockford'),
                'nicht_leer' => __('Nothing was laid down: this thread is not empty. Create a new one, here or in Bismarck.', 'cluster-one-crockford'),
                'unbekannt'  => __('Nothing was laid down: this thread does not exist.', 'cluster-one-crockford'),
                'kein_ziel'  => __('Please choose a thread, or name a new one.', 'cluster-one-crockford'),
            );
            if (isset($c1_saetze[$c1_f])) {
                echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html($c1_saetze[$c1_f]) . '</p></div>';
            }
        }
        ?>

        <?php
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- nur gelesen, um eine Antwort zu zeigen; geschrieben hat der Handler mit Nonce (Omega).
        if (isset($_GET['c1_frei_n']) || isset($_GET['c1_zu_n'])) {
            $c1_fn = isset($_GET['c1_frei_n']) ? (int) $_GET['c1_frei_n'] : 0;
            $c1_zn = isset($_GET['c1_zu_n']) ? (int) $_GET['c1_zu_n'] : 0;
            /* translators: 1: number of people released, 2: number of people withdrawn */
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(sprintf(__('Release saved: %1$d released, %2$d withdrawn.', 'cluster-one-crockford'), $c1_fn, $c1_zn)) . '</p></div>';
        }
        // phpcs:enable
        ?>
        <?php if ($c1_inv === array()) : ?>
            <div class="notice notice-info inline"><p>
                <?php if (defined('BRICKS_VERSION')) : ?>
                    <?php esc_html_e('Bricks is active, but no page, post or template with Bricks content was found. Pages edited in the WordPress editor have no Bricks content.', 'cluster-one-crockford'); ?>
                <?php else : ?>
                    <?php esc_html_e('Bricks is not active on this node. The database may still hold Bricks content; this page lists only what it could find under the Bricks meta keys.', 'cluster-one-crockford'); ?>
                <?php endif; ?>
            </p></div>
        <?php else : ?>
            <?php if (!reset($c1_inv)['sprache']['bekannt']) : ?>
                <p class="description"><?php esc_html_e('Language and translations: not determinable — no multilingual plugin answered. That does not mean the site has one language.', 'cluster-one-crockford'); ?></p>
            <?php endif; ?>
            <form method="post">
                <?php wp_nonce_field('cluster_one_crockford_ausfuhr'); ?>
                <input type="hidden" name="c1_crockford_ausfuhr" value="1">
                <table class="widefat striped" style="max-width:1100px;">
                    <thead><tr>
                        <?php if ($c1_voll) : ?><th style="width:2em;"><input type="checkbox" id="c1-crockford-alle" title="<?php echo esc_attr__('All', 'cluster-one-crockford'); ?>"></th><?php endif; ?>
                        <th><?php esc_html_e('Title', 'cluster-one-crockford'); ?></th>
                        <th><?php esc_html_e('Type', 'cluster-one-crockford'); ?></th>
                        <th><?php esc_html_e('Language', 'cluster-one-crockford'); ?></th>
                        <th><?php esc_html_e('Sections', 'cluster-one-crockford'); ?></th>
                        <th style="text-align:right;"><?php esc_html_e('Elements', 'cluster-one-crockford'); ?></th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($c1_inv as $c1_id => $c1_i) : ?>
                        <tr>
                            <?php if ($c1_voll) : ?><td><input type="checkbox" name="c1_ids[]" value="<?php echo (int) $c1_id; ?>" <?php checked(in_array($c1_id, $c1_gewaehlt, true)); ?>></td><?php endif; ?>
                            <td>
                                <strong><?php echo esc_html($c1_i['titel'] !== '' ? $c1_i['titel'] : '#' . $c1_id); ?></strong>
                                <?php if ($c1_i['status'] !== 'publish') : ?> <span style="opacity:.6;">(<?php echo esc_html($c1_i['status']); ?>)</span><?php endif; ?>
                                <?php if ($c1_i['typ'] !== 'bricks_template') : ?><br><a href="<?php echo esc_url($c1_i['url']); ?>" target="_blank" rel="noopener" style="font-size:.9em;opacity:.7;"><?php echo esc_html(wp_make_link_relative($c1_i['url'])); ?></a><?php endif; ?>
                            </td>
                            <td><?php echo esc_html($c1_i['typ_label']); ?><?php if ($c1_i['vorlage'] !== '') : ?> <code><?php echo esc_html($c1_i['vorlage']); ?></code><?php endif; ?></td>
                            <td>
                                <?php if ($c1_i['sprache']['lang'] !== '') : ?><code><?php echo esc_html($c1_i['sprache']['lang']); ?></code><?php else : ?><span style="opacity:.5;">—</span><?php endif; ?>
                                <?php foreach ($c1_i['sprache']['translations'] as $c1_l => $c1_tid) : ?>
                                    <br><span style="opacity:.7;font-size:.9em;"><?php echo esc_html($c1_l); ?>: <?php echo esc_html(get_the_title($c1_tid)); ?><?php echo isset($c1_inv[$c1_tid]) ? '' : ' <em>' . esc_html__('(no Bricks content)', 'cluster-one-crockford') . '</em>'; ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td style="font-size:.9em;"><?php echo esc_html(implode(' · ', array_filter($c1_i['sektionen']))); ?></td>
                            <td style="text-align:right;"><?php echo (int) $c1_i['elemente']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (!$c1_voll) : ?>
                <?php /* translators: %d: Anzahl der Eintraege. */ ?>
                <p><span class="description"><?php echo esc_html(sprintf(_n('%d published item with Bricks content.', '%d published items with Bricks content.', count($c1_inv), 'cluster-one-crockford'), count($c1_inv))); ?></span></p>
                <?php else : ?>
                <p>
                    <label><input type="checkbox" name="c1_nur_struktur" value="1" <?php checked($c1_nur_struktur); ?>> <?php esc_html_e('Structure only, without text contents', 'cluster-one-crockford'); ?></label><br>
                    <label><input type="checkbox" name="c1_mit_css" value="1" <?php checked($c1_mit_css); ?>> <?php esc_html_e('With the settings of the global classes used', 'cluster-one-crockford'); ?></label><br>
                    <label><input type="checkbox" name="c1_mit_medien" value="1" <?php checked($c1_mit_medien); ?>> <?php esc_html_e('With the media files used (URL in the media library, size, alt text)', 'cluster-one-crockford'); ?></label><br>
                    <label><input type="checkbox" name="c1_mit_theme" value="1" <?php checked($c1_mit_theme); ?>> <?php esc_html_e('With the theme’s short map for AI readers', 'cluster-one-crockford'); ?></label>
                </p>
                <p><?php submit_button(__('Compose text for the selection', 'cluster-one-crockford'), 'primary', 'submit', false); ?>
                   <?php /* translators: %d: Anzahl der Eintraege. */ ?>
                   <span class="description" style="margin-left:1em;"><?php echo esc_html(sprintf(_n('%d item with Bricks content.', '%d items with Bricks content.', count($c1_inv), 'cluster-one-crockford'), count($c1_inv))); ?></span></p>
                <?php endif; ?>
            </form>
            <script>
            (function () {
                var a = document.getElementById('c1-crockford-alle'); if (!a) return;
                a.addEventListener('change', function () {
                    document.querySelectorAll('input[name="c1_ids[]"]').forEach(function (c) { c.checked = a.checked; });
                });
            })();
            </script>

            <?php /* 0.5.22: nur nach dem Bauen – eine gemerkte Auswahl ohne Text ist kein Fehlschlag. */ ?>
            <?php if ($c1_gesendet && $c1_gewaehlt !== array() && $c1_text === '') : ?>
                <div class="notice notice-warning inline"><p><?php esc_html_e('Nothing selected that has Bricks content.', 'cluster-one-crockford'); ?></p></div>
            <?php elseif ($c1_text !== '') : ?>
                <h2 style="margin-top:1.6em;"><?php esc_html_e('Hand it to an AI', 'cluster-one-crockford'); ?></h2>
                <p class="description" style="max-width:820px;"><?php esc_html_e('The same text a room hands to an AI inside the node — byte for byte. Everything in it is public page content; form recipients and honeypot fields are never included.', 'cluster-one-crockford'); ?></p>
                <textarea id="cluster-one-crockford-ausfuhr" readonly rows="24" style="width:100%;max-width:1100px;font-family:monospace;font-size:12px;" onclick="this.select();"><?php echo esc_textarea($c1_text); ?></textarea>
                <p>
                    <button type="button" class="button" id="cluster-one-crockford-ausfuhr-knopf"><?php esc_html_e('Copy to clipboard', 'cluster-one-crockford'); ?></button>
                    <span id="cluster-one-crockford-ausfuhr-echo" style="color:#666;"></span>
                    <?php /* translators: %s: Anzahl der Zeichen, formatiert. */ ?>
                    <span class="description" style="margin-left:1em;"><?php echo esc_html(sprintf(__('%s characters', 'cluster-one-crockford'), number_format_i18n($c1_laenge))); ?></span>
                </p>
                <script>
                document.getElementById('cluster-one-crockford-ausfuhr-knopf').addEventListener('click', function () {
                    var f = document.getElementById('cluster-one-crockford-ausfuhr'),
                        e = document.getElementById('cluster-one-crockford-ausfuhr-echo');
                    f.select();
                    (navigator.clipboard ? navigator.clipboard.writeText(f.value) : Promise.reject())
                        .then(function () { e.textContent = <?php echo wp_json_encode(__('Copied.', 'cluster-one-crockford')); ?>; })
                        .catch(function () { e.textContent = <?php echo wp_json_encode(__('Press Ctrl/Cmd+C.', 'cluster-one-crockford')); ?>; });
                });
                </script>

                <?php cluster_one_crockford_ablegen_formular($c1_gewaehlt, array('c1_nur_struktur' => $c1_nur_struktur, 'c1_mit_theme' => $c1_mit_theme, 'c1_mit_css' => $c1_mit_css, 'c1_mit_medien' => $c1_mit_medien), $c1_laenge); ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php cluster_one_crockford_freigabe_abschnitt(); // Omega: die Freigabe, nur fuer den SysOp gezeichnet ?>
    </div>
    <?php
}

/* ---------------------------------------------------------------------------
 * DER ZWEITE WEG: IN EIN THEMA LEGEN.
 *
 * Der erste Weg ist die Zwischenablage — eine KI ausserhalb, bezahlt vom Abo,
 * kein Token ueber die API. Der zweite legt den Text als Praeambel in ein
 * leeres Thema; der verknuepfte Raum gibt ihn dann bei JEDEM KI-Aufruf mit.
 * Das kostet jedes Mal, und das steht neben dem Knopf (Kostenwahrheit).
 * Nemesus, 19.10.2026: „Mein Abo ist guenstiger als meine Tokens." Deshalb
 * steht dieser Weg als zweiter und nicht als erster.
 *
 * Crockford kennt Themen nur ueber den Vertrag 'cluster_one_thema' /
 * 'cluster_one_thema_ablegen' (bismarck/platon.php). Antwortet niemand,
 * gibt es den Weg auf diesem Knoten nicht, und das steht dann da.
 * ------------------------------------------------------------------------- */
function cluster_one_crockford_ablegen_formular(array $c1_ids, array $c1_haken, int $c1_laenge): void {
    if (!function_exists('cluster_one_is_sysop') || !cluster_one_is_sysop()) { return; }
    $c1_leere = apply_filters('cluster_one_thema', null, 'leere');
    ?>
    <h2 style="margin-top:1.6em;"><?php esc_html_e('Lay it into a thread', 'cluster-one-crockford'); ?></h2>
    <?php if (!is_array($c1_leere)) : ?>
        <p class="description" style="max-width:820px;"><?php esc_html_e('No organ on this node answers for threads, so there is nowhere to lay the text. Copying it is the only way here.', 'cluster-one-crockford'); ?></p>
        <?php return; ?>
    <?php endif; ?>
    <p style="max-width:820px;"><?php echo esc_html(sprintf(
        /* translators: %s: number of characters */
        __('The text becomes the preamble of an empty thread; the chat linked to it hands it to the AIs with every call. That is %s characters, paid again with every answer by whoever calls an AI there. “Structure only” cuts it to roughly a third.', 'cluster-one-crockford'),
        number_format_i18n($c1_laenge))); ?></p>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:820px;">
        <?php wp_nonce_field('cluster_one_crockford_ablegen'); ?>
        <input type="hidden" name="action" value="cluster_one_crockford_ablegen">
        <?php foreach ($c1_ids as $c1_id) : ?><input type="hidden" name="c1_ids[]" value="<?php echo (int) $c1_id; ?>"><?php endforeach; ?>
        <?php foreach ($c1_haken as $c1_hn => $c1_hv) : if ($c1_hv) : ?><input type="hidden" name="<?php echo esc_attr($c1_hn); ?>" value="1"><?php endif; endforeach; ?>
        <p>
            <label><?php esc_html_e('Thread', 'cluster-one-crockford'); ?>
                <select name="c1_thema">
                    <option value=""><?php esc_html_e('— new thread —', 'cluster-one-crockford'); ?></option>
                    <?php foreach ($c1_leere as $c1_tid => $c1_titel) : ?>
                        <option value="<?php echo esc_attr((string) $c1_tid); ?>"><?php echo esc_html($c1_titel); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label style="margin-left:1em;"><?php esc_html_e('Name of the new thread', 'cluster-one-crockford'); ?>
                <input type="text" name="c1_name" class="regular-text" value="<?php echo esc_attr(sprintf(
                    /* translators: %s: date */
                    __('Site context %s', 'cluster-one-crockford'), date_i18n(get_option('date_format')))); ?>">
            </label>
        </p>
        <p class="description"><?php esc_html_e('Only empty threads are offered: no posts but system lines, no preamble. A new thread gets a chat of its own.', 'cluster-one-crockford'); ?></p>
        <?php submit_button(__('Lay it down', 'cluster-one-crockford'), 'secondary', 'submit', false); ?>
    </form>
    <?php
}

add_action('admin_post_cluster_one_crockford_ablegen', function () {
    if (!function_exists('cluster_one_is_sysop') || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_crockford_ablegen');
    $c1_zurueck = admin_url('admin.php?page=cluster_one_crockford');
    $c1_ids  = isset($_POST['c1_ids']) && is_array($_POST['c1_ids']) ? array_map('intval', $_POST['c1_ids']) : array();
    $c1_nur  = !empty($_POST['c1_nur_struktur']);
    $c1_mt   = !empty($_POST['c1_mit_theme']);
    $c1_mc   = !empty($_POST['c1_mit_css']);
    $c1_mm   = !empty($_POST['c1_mit_medien']);
    $c1_tid  = isset($_POST['c1_thema']) ? sanitize_text_field(wp_unslash($_POST['c1_thema'])) : '';
    $c1_name = isset($_POST['c1_name']) ? trim(sanitize_text_field(wp_unslash($_POST['c1_name']))) : '';

    if (apply_filters('cluster_one_thema', null, 'leere') === null) {
        wp_safe_redirect(add_query_arg('ablegen', 'kein_organ', $c1_zurueck)); exit;
    }
    if ($c1_tid === '' && $c1_name === '') {
        wp_safe_redirect(add_query_arg('ablegen', 'kein_ziel', $c1_zurueck)); exit;
    }
    $c1_inv  = cluster_one_crockford_inventar(true); // SysOp, oben geriegelt: alles
    $c1_ids  = array_values(array_intersect(array_keys($c1_inv), $c1_ids));
    if ($c1_ids === array()) {
        wp_safe_redirect(add_query_arg('ablegen', 'kein_text', $c1_zurueck)); exit;
    }
    $c1_text = cluster_one_crockford_kontext_text($c1_ids, $c1_nur, $c1_mt, $c1_mc, $c1_mm);
    $c1_snap = preg_match('~Snapshot ([0-9a-f]{16})~', $c1_text, $c1_m) ? $c1_m[1] : '';

    $c1_a = new stdClass();
    $c1_a->thema = $c1_tid;
    $c1_a->name  = $c1_name;
    $c1_a->text  = $c1_text;
    $c1_a->meldung = sprintf(
        /* translators: 1: number of items, 2: snapshot id */
        __('The SysOp laid this node’s site context into the preamble of this thread: %1$d item(s) with Bricks content, snapshot %2$s. Every AI called in the linked chat receives it with every call.', 'cluster-one-crockford'),
        count($c1_ids), $c1_snap !== '' ? $c1_snap : '—');
    /* Die KI-Pruefung: liegt auf diesem Knoten ueberhaupt ein Connector mit
       Schluessel? Mit oder ohne Pacioli ist einerlei — gefragt wird der Boden.
       Ohne Schluessel geht das Ablegen trotzdem; der Text wartet dann. */
    $c1_a->meldung2 = (function_exists('cluster_one_count_keyed_ai_connectors') && cluster_one_count_keyed_ai_connectors() === 0)
        ? __('No AI on this node can answer yet: no connector has a key. The text lies here until one does.', 'cluster-one-crockford')
        : '';
    do_action('cluster_one_thema_ablegen', $c1_a);

    if (!empty($c1_a->fehler) || empty($c1_a->url)) {
        wp_safe_redirect(add_query_arg('ablegen', !empty($c1_a->fehler) ? sanitize_key((string) $c1_a->fehler) : 'kein_organ', $c1_zurueck)); exit;
    }
    wp_safe_redirect((string) $c1_a->url);
    exit;
});

/* ===========================================================================
 * RUNDE 35 (30.10.2026, Neo), 0.5.33 — SEHEN DUERFEN ALLE, TUN NUR MIT RECHT.
 * ===========================================================================
 *
 * Nemesus, 30.10.: alle Mitglieder sollen alle Plugins sehen und oeffnen
 * koennen, Crockford eingeschlossen – aber Crockford „ist sensibler … Du kannst
 * ja damit die gesamte Webseite auslesen". Also: Seite und Inventar fuer jedes
 * Mitglied (nur Veroeffentlichtes, das sieht ohnehin jeder im Netz); den Text
 * verfassen und Entwuerfe, Geplantes, Privates sehen nur mit FREISCHALTUNG JE
 * MENSCH durch den SysOp. Randbedingungen bleiben SysOp, „In ein Thema legen"
 * bleibt SysOp (steht beide oben, unveraendert).
 * [Chi, 03.11.2026, 0.6.6: die Randbedingungen haengen seit heute an DIESER Freigabe — Nemesus' Entscheidung; siehe
 * CLUSTER_ONE_CROCKFORD_RANDBEDINGUNGEN unten. „In ein Thema legen" bleibt SysOp.]
 *
 * DAS RECHT IST EINE WORDPRESS-CAPABILITY AM KONTO (WP_User::add_cap), wie
 * Paciolis 'use_own_ai_connector': keine eigene Tabelle, vertraegt sich mit
 * Rollen-Plugins, und user_can() fragt sie. Gesetzt wird sie an EINER Stelle,
 * der WordPress-Benutzerliste – dort, wo der SysOp Paciolis Recht schon
 * verwaltet (Nemesus' Entscheidung 30.10.: ein Ort fuer eine Sorte Entscheidung).
 *
 * DREI RIEGEL, DIE SICH NICHT AUFHEBEN LASSEN:
 *   - Der SysOp (das eine Konto, cluster_one_main_admin_user_id) hat das Recht
 *     immer; ihm laesst es sich nicht entziehen, und das wird gesagt.
 *   - Ohne Mitgliedschaft (cluster_one_can_access) gilt die Cap nicht, auch
 *     wenn sie am Konto stehen blieb: wer von der Liste des Knotens geht, nimmt
 *     Crockford nicht mit. Freischalten ohne Mitgliedschaft wird abgewiesen
 *     und gezaehlt, nicht still mitgegeben (P1-12, dieselbe Form wie Pacioli).
 *   - KI-Kennungen (cluster_one_ai_identity_user_ids) werden uebersprungen:
 *     eine KI verfasst keinen Site-Kontext fuer eine KI.
 *
 * KEIN CHRONIK-WORT: die Woerter der Chronik stehen im Boden (kern.php,
 * cluster_one_chronik_typ_label) ohne Filter; ein Wort hier hiesse eine Boden-
 * Runde. Der Zustand ist in der Benutzerliste sichtbar, je Konto, und das
 * Entziehen hat dort seine Antwort – wie bei Pacioli. Kommt die Chronik-Oeffnung
 * im Boden, bekommt dieses Haus sein Wort.
 * ========================================================================= */
if (!defined('CLUSTER_ONE_CROCKFORD_VOLL')) {
    define('CLUSTER_ONE_CROCKFORD_VOLL', 'cluster_one_crockford_voll');
}

/** Ist dieser Mensch der SysOp dieses Knotens? Fuer ein beliebiges Konto; cluster_one_is_sysop() fragt nur das eigene. */
function cluster_one_crockford_ist_sysop(int $c1_uid): bool {
    return $c1_uid > 0 && function_exists('cluster_one_main_admin_user_id') && $c1_uid === (int) cluster_one_main_admin_user_id();
}

/** Darf dieser Mensch bei Crockford alles: alle Zustaende sehen, den Text verfassen? */
function cluster_one_crockford_voll(int $c1_uid = 0): bool {
    $c1_uid = $c1_uid > 0 ? $c1_uid : (int) get_current_user_id();
    if ($c1_uid <= 0) { return false; }
    if (cluster_one_crockford_ist_sysop($c1_uid)) { return true; }
    if (function_exists('cluster_one_can_access') && !cluster_one_can_access($c1_uid)) { return false; }
    return user_can($c1_uid, CLUSTER_ONE_CROCKFORD_VOLL);
}

/* ===========================================================================
 * RUNDE CHI (03.11.2026, Neo), 0.6.6 — DIE RANDBEDINGUNGEN HAENGEN AN DERSELBEN TUER.
 * ===========================================================================
 *
 * Nemesus, 03.11. mittags: „Randbedingungen … Die sollte er freigeben koennen. So etwas in der Art haben wir mit
 * Crockford ja auch schon gemacht?" — ja: die Freigabe oben (Runde 35). Bis 0.6.5 schloss die Seite der Randbedingungen
 * mit 'cluster_one_kranz_save' — dem Recht des Kranz, das genau EIN Konto hat (kern.php, map_meta_cap) und sich nicht
 * vergeben laesst; dazu zwei is_sysop-Fragen in umgebung.php (Abschnitt und Handler). Jetzt ein eigenes Recht, das ueber
 * map_meta_cap DIESELBE Frage stellt wie cluster_one_crockford_voll(): SysOp per Amt; sonst freigeschaltet UND Mitglied.
 *
 *     EINE Freigabe, ZWEI Seiten. Keine zweite Liste, kein zweiter Haken in der Benutzerliste.
 *
 * DER PREIS STEHT IM KOPF VON umgebung.php: die Versionsliste ist ein Angriffs-Fingerabdruck, „niemals oeffentlich, nur auf
 * ausdrueckliche Uebergabe". Wer Crockford freigibt, gibt sie jetzt mit — je Mensch, mit Absicht, in derselben Liste, in der
 * er schon Entwuerfe und Privates freigibt. Die Spalte und die Antwortsaetze der Benutzerliste nennen es.
 * BENANNTE GRENZE: die Cap ist eine Frage an diese Funktion, kein Eintrag am Konto — am Konto steht weiter nur
 * CLUSTER_ONE_CROCKFORD_VOLL. Wer sie am Konto sucht, findet sie nicht; das ist die Absicht (ein Schreibweg).
 * ========================================================================= */
if (!defined('CLUSTER_ONE_CROCKFORD_RANDBEDINGUNGEN')) {
    define('CLUSTER_ONE_CROCKFORD_RANDBEDINGUNGEN', 'cluster_one_crockford_randbedingungen');
}
add_filter('map_meta_cap', function (array $c1_caps, string $c1_cap, int $c1_uid) {
    if ($c1_cap !== CLUSTER_ONE_CROCKFORD_RANDBEDINGUNGEN) {
        return $c1_caps;
    }
    return cluster_one_crockford_voll($c1_uid) ? array('exist') : array('do_not_allow');
}, 10, 3);

/* --- DIE BENUTZERLISTE: eine Spalte, zwei Massenaktionen, eine Antwort ------ */
add_filter('manage_users_columns', function ($c1_spalten) {
    if (!is_array($c1_spalten) || !function_exists('cluster_one_is_sysop') || !cluster_one_is_sysop()) { return $c1_spalten; }
    $c1_spalten['c1_crockford'] = __('Crockford', 'cluster-one-crockford');
    return $c1_spalten;
});

add_filter('manage_users_custom_column', function ($c1_aus, $c1_spalte, $c1_uid) {
    if ($c1_spalte !== 'c1_crockford') { return $c1_aus; }
    $c1_uid = (int) $c1_uid;
    if (function_exists('cluster_one_ai_identity_user_ids') && in_array($c1_uid, array_map('intval', (array) cluster_one_ai_identity_user_ids()), true)) {
        return '<span style="color:#8a8a8a">' . esc_html__('AI identity', 'cluster-one-crockford') . '</span>';
    }
    if (cluster_one_crockford_ist_sysop($c1_uid)) {
        return esc_html__('SysOp · everything', 'cluster-one-crockford');
    }
    if (function_exists('cluster_one_can_access') && !cluster_one_can_access($c1_uid)) {
        return '<span style="color:#8a8a8a">' . esc_html__('not a member of the node', 'cluster-one-crockford') . '</span>';
    }
    if (user_can($c1_uid, CLUSTER_ONE_CROCKFORD_VOLL)) {
        return esc_html__('released · all items, composes the text, ground conditions', 'cluster-one-crockford'); // Chi: „, ground conditions" dazu
    }
    return esc_html__('member · published items, reads only', 'cluster-one-crockford');
}, 10, 3);

/* ===========================================================================
 * RUNDE OMEGA (03.11.2026, Neo), 0.6.8 — DIE FREIGABE ZIEHT AUF CROCKFORDS SEITE.
 * ===========================================================================
 *
 * Nemesus: „Theoretisch kann Crockford auch ohne andere PlugIns benutzt werden. Und theoretisch kann ein SysOp einen anderen
 * User dafuer freischalten." … „alles da steuern, wo wir es brauchen, mit jeweils einem PlugIn, was das PlugIn selbst
 * betrifft." Seit Phi kann jedes Haus allein liegen; ein SysOp, der nur Crockford hat, kennt Paciolis Gewohnheit nicht und
 * sucht die Freigabe bei Crockford. Das kehrt die Regel vom 30.10. um (ein Ort fuer eine Sorte Entscheidung: die Benutzerliste).
 *
 * HIER STANDEN BIS 0.6.7 DREI BLOECKE, Runde 35/0.5.34:
 *     add_filter('bulk_actions-users', …)          „Release Crockford" / „Withdraw Crockford", nur fuer den SysOp
 *     add_filter('handle_bulk_actions-users', …)   add_cap/remove_cap je Konto; KIs uebersprungen, Nicht-Mitglieder und der
 *                                                  SysOp gezaehlt und abgewiesen; Chronik-Wort je Massenaktion; Antwort als
 *                                                  Query-Parameter c1_crockford, _n, _ohne, _sysop, _ki
 *     add_action('admin_notices', …)                die vier Antwortsaetze auf users.php
 * Sie leben weiter in cluster_one_crockford_freigabe_abschnitt() und im Handler admin_post_cluster_one_crockford_freigabe:
 * dieselbe Cap, dasselbe Chronik-Wort. Die Ausnahmen braucht es nicht mehr, weil die Liste vom Boden kommt
 * (cluster_one_freigabe_mitglieder: Weissliste ohne SysOp, KIs, Collective) — es steht nur da, wer freigeschaltet werden kann.
 * Die Spalte „Crockford" in der Benutzerliste bleibt als LESER (zwei Leser sind erlaubt, ein Schreibweg).
 * ========================================================================= */

/** Der Abschnitt „Freigabe" auf der Site-Kontext-Seite — gezeichnet nur fuer den SysOp. */
function cluster_one_crockford_freigabe_abschnitt(): void {
    if (!function_exists('cluster_one_is_sysop') || !cluster_one_is_sysop()) { return; }
    $c1_mitglieder = function_exists('cluster_one_freigabe_mitglieder') ? cluster_one_freigabe_mitglieder() : array();
    $c1_knoten = admin_url('admin.php?page=cluster_one_settings');
    ?>
        <h2 style="margin-top:2.2em;"><?php esc_html_e('Release', 'cluster-one-crockford'); ?></h2>
        <p class="description" style="max-width:820px;"><?php esc_html_e('Who, besides you, has Crockford in full: all items including drafts, scheduled and private ones, composes the text, and sees the ground conditions. Only members of the node can be released — the list of members is kept under Node → Node settings. AI identities are not listed: an AI does not compose a site context for an AI.', 'cluster-one-crockford'); ?></p>
    <?php if ($c1_mitglieder === array()) : ?>
        <p><?php esc_html_e('No other member on this node yet. Put people on the node’s list first.', 'cluster-one-crockford'); ?> <a href="<?php echo esc_url($c1_knoten); ?>"><?php esc_html_e('Node settings', 'cluster-one-crockford'); ?></a></p>
    <?php else : ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:820px;">
            <?php wp_nonce_field('cluster_one_crockford_freigabe'); ?>
            <input type="hidden" name="action" value="cluster_one_crockford_freigabe">
            <?php foreach ($c1_mitglieder as $c1_m) : ?>
                <p style="margin:.2em 0;"><label><input type="checkbox" name="c1_frei[]" value="<?php echo (int) $c1_m->ID; ?>" <?php checked(user_can($c1_m, CLUSTER_ONE_CROCKFORD_VOLL)); ?>> <?php echo esc_html($c1_m->display_name); ?> <span style="color:#666;">(<?php echo esc_html($c1_m->user_login); ?>)</span></label></p>
            <?php endforeach; ?>
            <p><?php submit_button(__('Save release', 'cluster-one-crockford'), 'secondary', 'c1_speichern', false); ?> <a href="<?php echo esc_url($c1_knoten); ?>" style="margin-left:1em;"><?php esc_html_e('Node settings', 'cluster-one-crockford'); ?></a></p>
        </form>
    <?php endif;
}

/* Der Schreibweg der Freigabe: SysOp und Nonce; gesetzt wird genau, was die Haken sagen, fuer genau die Liste des Bodens. */
add_action('admin_post_cluster_one_crockford_freigabe', function () {
    if (!function_exists('cluster_one_is_sysop') || !cluster_one_is_sysop()) {
        wp_die('-1', 403);
    }
    check_admin_referer('cluster_one_crockford_freigabe');
    $c1_soll = isset($_POST['c1_frei']) && is_array($_POST['c1_frei']) ? array_map('intval', $_POST['c1_frei']) : array();
    $c1_frei = 0; $c1_zu = 0;
    foreach (function_exists('cluster_one_freigabe_mitglieder') ? cluster_one_freigabe_mitglieder() : array() as $c1_m) {
        $c1_hat  = user_can($c1_m, CLUSTER_ONE_CROCKFORD_VOLL);
        $c1_will = in_array((int) $c1_m->ID, $c1_soll, true);
        if ($c1_will && !$c1_hat) { $c1_m->add_cap(CLUSTER_ONE_CROCKFORD_VOLL); $c1_frei++; }
        elseif (!$c1_will && $c1_hat) { $c1_m->remove_cap(CLUSTER_ONE_CROCKFORD_VOLL); $c1_zu++; }
    }
    /* Das Tuerereignis wie seit 0.5.34: eine Zeile je Richtung, die Zahl im Feld Count, kein Name (Artikel 4). */
    foreach (array('crockford_released' => $c1_frei, 'crockford_withdrawn' => $c1_zu) as $c1_typ => $c1_n) {
        if ($c1_n > 0 && function_exists('cluster_one_event_emit')) {
            cluster_one_event_emit(array(
                'type'           => $c1_typ,
                'container_type' => 'users',
                'container_id'   => CLUSTER_ONE_CROCKFORD_VOLL,
                'actor_id'       => function_exists('cluster_one_actor_id_human') ? cluster_one_actor_id_human() : 'wp:' . get_current_user_id(),
                'summary_count'  => $c1_n,
            ), true);
        }
    }
    wp_safe_redirect(add_query_arg(array('c1_frei_n' => $c1_frei, 'c1_zu_n' => $c1_zu), admin_url('admin.php?page=cluster_one_crockford')));
    exit;
});

/* ---------------------------------------------------------------------------
 * DIE SEITE: RANDBEDINGUNGEN — der Abschnitt aus umgebung.php, der bis zum
 * 19.10.2026 auf der Boden-Seite „Einstellungen" stand (Ordnung 60).
 * ------------------------------------------------------------------------- */
function cluster_one_render_randbedingungen_page(): void {
    if (function_exists('cluster_one_page_gate')) { cluster_one_page_gate(); }
    ?>
    <div class="wrap">
        <h1><?php echo function_exists('cluster_one_page_h1') ? cluster_one_page_h1('Crockford') : esc_html__('Cluster One — Ground conditions', 'cluster-one-crockford'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- beide Zweige escapt: cluster_one_page_h1() gibt fertiges HTML zurueck (esc_html(Titel) + Hilfeblase), der Rueckfall rechts ist esc_html__() ?></h1>
        <?php cluster_one_render_crockford_tabs('cluster_one_randbedingungen'); ?>
        <?php
        if (isset($_GET['notiz'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            $c1_n = sanitize_key((string) wp_unslash($_GET['notiz'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
            if ($c1_n === 'gespeichert') {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Reasoning saved and confirmed for the current ground conditions.', 'cluster-one-crockford') . '</p></div>';
            } elseif ($c1_n === 'bestaetigt') {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Confirmed for the current ground conditions.', 'cluster-one-crockford') . '</p></div>';
            }
        }
        /* Omega-2 (03.11.2026 abends, Neo), 0.6.9 — DIE DRITTE SYSOP-FRAGE. Nemesus beim Omega-Test auf der DEV: MenschC
           freigeschaltet, der Reiter erscheint, die Seite sagte trotzdem „Nur SysOp: … werden sonst niemandem gezeigt." Chi
           (0.6.6) hatte die Tuer (CLUSTER_ONE_CROCKFORD_RANDBEDINGUNGEN) und die zwei Fragen in umgebung.php auf
           cluster_one_crockford_voll() gestellt — und diese hier, in der eigenen Datei, uebersehen. Bis 0.6.8 stand hier
               if (!function_exists('cluster_one_is_sysop') || !cluster_one_is_sysop()) {
                   echo … esc_html__('SysOp only: the ground conditions are an attack fingerprint and are shown to nobody else.', …) …
           Jetzt dieselbe Frage wie an der Tuer, im Abschnitt und im Handler — vier Stellen, eine Frage. Der Satz sagt, was
           fehlt: die Freigabe, nicht das Amt. */
        if (!function_exists('cluster_one_crockford_voll') || !cluster_one_crockford_voll()) {
            echo '<div class="notice notice-info"><p>' . esc_html__('Only with a release: the ground conditions are an attack fingerprint and are shown to nobody else. The SysOp releases per person, on the Site context page.', 'cluster-one-crockford') . '</p></div>';
        } elseif (function_exists('cluster_one_settings_seite_umgebung')) {
            cluster_one_settings_seite_umgebung();
        }
        ?>
    </div>
    <?php
}

/* ===========================================================================
 * 20.10.2026 (Neo), 0.5.17 — landkarte.php IST FORT, NACH TURING GEZOGEN.
 * ===========================================================================
 *
 * Entscheidung Nemesus/Neo vom 20.10.2026: Turing misst Pakete, Kasper misst
 * Organe, die Landkarte die Dateien — also gehoert sie zu Turings Kasper-Reiter
 * (turing.php, tu_seite_kasper_dateien, Turing 3.14.0). Die Messung (das
 * Verzeichnis statt einer Liste, sha1 erst bei mehr als einer Kopie, dann der
 * Tokenvergleich, bei dem das Domaenen-Argument eines Uebersetzungsrufes der
 * einzige erlaubte Unterschied ist, NICHT GEMESSEN bleibt bei der Warnung) ist
 * dort unveraendert nachgebaut, mit ihren Nummern H141 · H160 · H184 · H196.
 * Crockford hat seither zwei Reiter: Site-Kontext und Randbedingungen.
 *
 * DER KOPF DER DATEI, WOERTLICH (includes/landkarte.php, Stand 0.5.16):
 *
 *     Cluster One — Modul landkarte.
 *     WER LÄUFT HIER, UND AUS WELCHEM PLUGIN?
 *     ---------------------------------------------------------------------------
 *     WAS HIER FRÜHER STAND, UND WARUM ES WEG IST
 *     Diese Datei ist die Hälfte von galvani.php, die den Umbau überlebt hat.
 *     Galvani schaltete einzelne Organe ab, um zu sehen, was ohne sie umfällt. Am
 *     27.08.2026 hat Christian beim Lesen seiner eigenen Seite gemerkt, dass davon
 *     nichts mehr übrig war: […]
 *     UND DER NAME STIRBT MIT. Galvani war der Mann mit dem Froschschenkel: Strom
 *     drauf, es zuckt. Ohne Schalter zuckt nichts. Was bleibt, ist eine Landkarte.
 *     ---------------------------------------------------------------------------
 *     ZWEI LISTEN WERDEN EINE  […]
 *         Verdrängung ist eine Tatsache über den Knoten.
 *         Schaltbarkeit ist eine Entscheidung über die Bedienung.
 *         Wer die Tatsache durch die Erlaubnis zählt, zählt zu wenig.
 *     ---------------------------------------------------------------------------
 *     WARUM DIE DATEI IN MAMMUT BLEIBT  […]
 *     ---------------------------------------------------------------------------
 *     DER SICHTSCHUTZ IST WEG (v1.2.2)  […]
 *         Ein Riegel ohne Gefahr dahinter ist kein Schutz, sondern die
 *         Erinnerung an eine Gefahr, die es nicht mehr gibt.
 *
 * WAS MIT DER DATEI GEGANGEN IST: das Organ `landkarte` (cluster_one_hier) und
 * damit der Kranz-Reiter „Map" (der Boden fragt cluster_one_modul_da('landkarte')
 * und bekommt jetzt nein); die Seite cluster_one_landkarte mit
 * cluster_one_landkarte_seiten() / _slug_von() und ihrem Anspruch
 * (cluster_one_seite_ansprueche); die Messfunktionen cluster_one_landkarte_organe
 * · _boden · _wirt · _dateien · _uebersetzungsrufe · _domaenenstellen ·
 * _nur_domaene · _kopien · _mammut_da und die Konstante
 * CLUSTER_ONE_LANDKARTE_TOKEN_MAX. Der Katalog behaelt seine Eintraege (ein
 * ungenutzter Eintrag schadet nicht; ein fehlender schon).
 *
 * WAS GEBLIEBEN IST, weil es dem PRODUKT gehoert und nicht der Seite: der
 * Steckbrief (die Feynman-Blase fragt ihn ueber cluster_one_steckbrief_von) und
 * der Raeumer mit seiner Anmeldung (die Gefahrenzone und die uninstall.php
 * fragen das Register). Beide stehen unten, mit ihren Begruendungen gekuerzt
 * auf das, was fuer den naechsten Leser traegt.
 * ========================================================================= */

/* --- DER STECKBRIEF — JEDES ORGAN NENNT DAS SEINE SELBST (seit 0.9.66) ------
 *
 * Sechs Felder, positioniert wie bei cluster_one_kleinrock_steckbrief(). Bis
 * 0.5.16 stand er in landkarte.php, weil „ein Steckbrief ueber den NAMEN in das
 * Organ gehoert, das mit dem Namen mitgeht" — das Organ ist fort, der Name
 * bleibt beim Produkt, also steht er hier.
 *
 * FELD 3 IST LEER, UND DAS IST NICHT VERGESSEN: fuer Douglas Crockford liegt
 * keine Jahreszahl aus einer Quelle in meinem Fach (Vertrag 15 §0: eine
 * geliehene Zahl sieht aus wie eine eigene, bis der Verleiher geht).
 *
 * FELD 4 IST CHRISTIANS WORTLAUT, uebersetzt und nicht ergaenzt (AN-CROCKFORD-
 * feld-4-der-wortlaut.md §1); die Anrede „Crockford." ist Feld 1 und steht
 * deshalb nicht zweimal da.
 *
 * FELD 6 IST NEU GESCHRIEBEN (0.5.17). Bis heute begann es mit „Crockford is the
 * map of this node: which organ runs from which plugin, which file lies in
 * several of them at once, and what this node is actually built on" und endete
 * mit „why an entry it rejects is reported rather than silently dropped". Der
 * erste Satz ist seit dem Umzug falsch; der letzte ist die Zeile, an der dieses
 * Haus gemessen werden soll, und bleibt. */
function cluster_one_crockford_steckbrief(): array {
    return array('Crockford', 'Douglas Crockford', '',
        __('Named after Douglas Crockford (the inventor of JSON), the foundation of modern web pages built with Bricks: no code in the payload, no guessing, no implicit types, deterministic rejection. The module extracts Bricks JSON from pages and prepares it in structured form, in order to instruct AIs as precise web-page experts.', 'cluster-one-crockford'),
        __('Not a parser and not a format. The name stands for a way of working, not for JSON: minimal contracts, no guessing, and a rejection you can retrace.', 'cluster-one-crockford'),
        __('Crockford reads what Bricks knows about this site — every page, post and template with Bricks content — and prepares it as text for an AI: structure, classes, links, loops, form fields; never form recipients or honeypots. Its namesake is Douglas Crockford, who specified JSON and wrote “JavaScript: The Good Parts”: reduce to the minimum that holds, and let nothing through that has not been checked. That is why the text says what it leaves out, why nothing in it is guessed, and why an entry it rejects is reported rather than silently dropped. Until 0.5.16 this house also drew the map of the node (which organ runs from which plugin); that map now lives in Turing, under Kasper.', 'cluster-one-crockford'));
}

/* --- DER RAEUMER — RUNDBRIEF 19 §3, und er zaehlt, bevor er etwas anfasst ---
 *
 *     Ein Raeumer, der nichts findet, ist kein ueberfluessiger Raeumer. Er ist
 *     der Unterschied zwischen einer Null und einer Leerstelle.
 *
 * Dieses Haus schreibt genau EINE Option, cluster_one_umgebung_notiz, und die
 * aus umgebung.php — einer Datei, die in keinem anderen Paket liegt. Geraeumt
 * wird sie nur, wenn das Organ `umgebung` aus DIESEM Paket laeuft; laeuft es
 * aus einem fremden, ist die Notiz dort geschrieben worden.
 *
 * ZWEI LEERE, ZWEI ANTWORTEN (M109, 23.09.2026): ist der EIGENE Wirt nicht
 * feststellbar, hat der Raeumer keinen Stand und tut nichts. Antwortet die
 * Kasper-Tabelle fuer `umgebung` nicht, laeuft kein fremdes `umgebung` —
 * beim Loeschen laeuft ueberhaupt keines —, und dann wird geraeumt. Benannte
 * Abhaengigkeit: die uninstall.php laedt alle Organe des Laders und ruft
 * cluster_one_kasper_vergessen(), sonst antwortet die Tabelle gar nicht.
 *
 * @param bool $c1_tun false = ZAEHLEN, kein Byte wird angefasst. true = raeumen.
 * @return array<string,int>  was => wieviel
 */
if (!function_exists('cluster_one_crockford_raeumen')) {
function cluster_one_crockford_raeumen(bool $c1_tun = false): array {
    $c1_zahl = array('einstellungen' => 0, 'notizen' => 0);

    if (!function_exists('get_option')) { return $c1_zahl; }

    if (!function_exists('cluster_one_kasper_wirt')) { return $c1_zahl; }
    $c1_hier = cluster_one_kasper_wirt(__FILE__);
    if ($c1_hier === '') { return $c1_zahl; }

    if (function_exists('cluster_one_organ_wirt')) {
        $c1_umg = (string) cluster_one_organ_wirt('umgebung');
        if ($c1_umg !== '' && $c1_umg !== $c1_hier) { return $c1_zahl; }
    }

    $c1_meine = array('cluster_one_umgebung_notiz');

    foreach ($c1_meine as $c1_name) {
        if (get_option($c1_name, null) === null) { continue; }
        $c1_zahl['einstellungen']++;
        if ($c1_name === 'cluster_one_umgebung_notiz') {
            /* GEZAEHLT WIRD, WAS EIN MENSCH HINEINGESCHRIEBEN HAT. Eine
             * Option mit leerem Text ist ein Rest und keine Notiz. */
            $c1_o = get_option($c1_name, array());
            if (is_array($c1_o) && trim((string) ($c1_o['text'] ?? '')) !== '') {
                $c1_zahl['notizen'] = 1;
            }
        }
        if ($c1_tun && function_exists('delete_option')) { delete_option($c1_name); }
    }

    return $c1_zahl;
}
}

/* DIE ANMELDUNG — FORM B (ENTSCHEIDUNG 20 §3): der Schluessel ist `produkt`,
 * der Wirt ein Feld daneben, erfragt statt getippt. Leer heisst „nicht
 * feststellbar" und nicht „keiner" — der Boden entscheidet dann. Sie steht im
 * Riegel dieser Datei, also meldet nur die laufende Kopie an. */
add_filter('cluster_one_raeumer', function ($c1_r) {
    if (!is_array($c1_r)) { return $c1_r; }
    $c1_r[] = array(
        'produkt' => 'crockford',
        'fn'      => 'cluster_one_crockford_raeumen',
        'wirt'    => function_exists('cluster_one_kasper_wirt')
            ? cluster_one_kasper_wirt(__FILE__)
            : '',
    );
    return $c1_r;
});

}
