<?php
/**
 * Cluster One — Modul merkliste (Bismarck).
 *
 * DIE MERKLISTE JE MENSCH: wen ich auf diesen Knoten einladen will, mit den
 * Kennungen, ueber die ich die Person erreichen wuerde. Ein Notizbuch, das
 * dem Menschen gehoert, nicht dem Knoten — der Unterschied zur Liste des
 * SysOps auf der Seite der KI-Kennungen (cluster_one_humans_external), die
 * knotenweit ist und einen Konsens-Haken traegt.
 *
 * DATEN BLEIBEN BEIM USER — HYBRID (Nemesus, 20.10.2026):
 *   · ohne eigenen Speicher liegt die Liste in WordPress (User-Meta), wie die
 *     Briefe (pn.php);
 *   · traegt der Mensch die Adresse seines eigenen Speichers ein (WebDAV ueber
 *     HTTPS) und die Pruefung gelingt, wird ihm angeboten, die Liste DORT
 *     abzulegen: verschluesselt mit einem Schluessel, den nur dieser Knoten
 *     fuer ihn haelt. Der Knoten behaelt dann nur die Adresse.
 *   · Passwort und Schluessel verwahrt Pacioli (Vertrag 'cluster_one_tresor');
 *     ohne Pacioli gibt es das Angebot nicht, die Liste bleibt in WordPress.
 *
 * WARUM WEBDAV UND NICHT (S)FTP: wp_remote_request() kann es ohne jede
 * PHP-Erweiterung, auf Shared Hosting wie auf einer Storage Box.
 *
 * BENANNTE GRENZEN: Der Knoten kann die Liste lesen, solange er den
 * Schluessel hat — „beim User" heisst: der Speicher ist seiner, nicht: der
 * Knoten ist blind. Faellt Pacioli, faellt der Schluessel, und die Datei im
 * Speicher ist nicht mehr zu lesen; vorher „zurueck nach WordPress holen".
 * Der Honigtopf fuer Fehler ist laut: ist der Speicher nicht erreichbar, steht
 * hier nichts statt einer alten Kopie.
 */
defined('ABSPATH') || exit;

$cluster_one_merkliste_laeuft = !function_exists('cluster_one_merkliste_lesen');
if (function_exists('cluster_one_hier')) { cluster_one_hier('merkliste', '', $cluster_one_merkliste_laeuft); }

if ($cluster_one_merkliste_laeuft) {

/* ---------------------------------------------------------------------------
 * Speicher in WordPress
 * ------------------------------------------------------------------------- */
function cluster_one_merkliste_meta(int $c1_uid): array {
    $c1_m = get_user_meta($c1_uid, 'cluster_one_merkliste', true);
    $c1_m = is_array($c1_m) ? $c1_m : array();
    $c1_m['ort']       = (($c1_m['ort'] ?? 'wp') === 'cloud') ? 'cloud' : 'wp';
    $c1_m['eintraege'] = isset($c1_m['eintraege']) && is_array($c1_m['eintraege']) ? $c1_m['eintraege'] : array();
    $c1_m['cloud']     = isset($c1_m['cloud']) && is_array($c1_m['cloud']) ? $c1_m['cloud'] : array();
    return $c1_m;
}

function cluster_one_merkliste_meta_speichern(int $c1_uid, array $c1_m): void {
    update_user_meta($c1_uid, 'cluster_one_merkliste', $c1_m);
}

/* ---------------------------------------------------------------------------
 * Der Tresor (Pacioli), ueber den Vertrag
 * ------------------------------------------------------------------------- */
function cluster_one_merkliste_tresor_da(): bool {
    return apply_filters('cluster_one_tresor', null, 'da') === true;
}

function cluster_one_merkliste_geheim(int $c1_uid, string $c1_name): string {
    $c1_w = apply_filters('cluster_one_tresor', null, 'lesen', array('user' => $c1_uid, 'name' => $c1_name));
    return is_string($c1_w) ? $c1_w : '';
}

/** @return string '' bei Erfolg, sonst ein Wort */
function cluster_one_merkliste_geheim_setzen(int $c1_uid, string $c1_name, string $c1_wert): string {
    $c1_a = new stdClass();
    $c1_a->user   = $c1_uid;
    $c1_a->name   = $c1_name;
    $c1_a->wert   = $c1_wert;
    $c1_a->fehler = 'kein_tresor';
    do_action('cluster_one_tresor_schreiben', $c1_a);
    return (string) $c1_a->fehler;
}

/* ---------------------------------------------------------------------------
 * Verschluesselung der Datei im fremden Speicher
 * ------------------------------------------------------------------------- */
function cluster_one_merkliste_schluessel(int $c1_uid, bool $c1_anlegen): string {
    $c1_k = cluster_one_merkliste_geheim($c1_uid, 'merkliste_schluessel');
    if ($c1_k === '' && $c1_anlegen && function_exists('sodium_crypto_secretbox')) {
        $c1_k = base64_encode(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES));
        if (cluster_one_merkliste_geheim_setzen($c1_uid, 'merkliste_schluessel', $c1_k) !== '') { return ''; }
    }
    $c1_raw = base64_decode($c1_k, true);
    return ($c1_raw !== false && strlen($c1_raw) === SODIUM_CRYPTO_SECRETBOX_KEYBYTES) ? $c1_raw : '';
}

function cluster_one_merkliste_verschluesseln(array $c1_eintraege, string $c1_key): string {
    $c1_nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $c1_json  = (string) wp_json_encode(array('v' => 1, 'eintraege' => array_values($c1_eintraege)), JSON_UNESCAPED_UNICODE);
    return 'c1m1:' . base64_encode($c1_nonce . sodium_crypto_secretbox($c1_json, $c1_nonce, $c1_key));
}

/** @return array|null null = nicht lesbar */
function cluster_one_merkliste_entschluesseln(string $c1_text, string $c1_key): ?array {
    if (strpos($c1_text, 'c1m1:') !== 0) { return null; }
    $c1_raw = base64_decode(substr($c1_text, 5), true);
    if ($c1_raw === false || strlen($c1_raw) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) { return null; }
    $c1_plain = sodium_crypto_secretbox_open(substr($c1_raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES), substr($c1_raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES), $c1_key);
    if ($c1_plain === false) { return null; }
    $c1_d = json_decode($c1_plain, true);
    return (is_array($c1_d) && isset($c1_d['eintraege']) && is_array($c1_d['eintraege'])) ? array_values($c1_d['eintraege']) : null;
}

/* ---------------------------------------------------------------------------
 * WebDAV
 * ------------------------------------------------------------------------- */
/* 0.2.62 (28.10.2026, Neo) — DIE MERKLISTE WIRD LESER. Adresse, Benutzer und
   Passwort des Speichers gehoeren seit Boden 1.19.0 dem Boden (kern.php,
   cluster_one_speicher_*: „Mein Speicher" im Persoenlichen Bereich). Die vier
   Funktionen hier (dav_url, datei, dav, zugang) rufen dorthin, wenn die Tuer da
   ist, und gehen sonst den alten Weg – ein aelterer Boden darf die Liste nicht
   brechen. Der Knoten-Schluessel der Datei (merkliste_schluessel) bleibt hier:
   er gehoert zur Datei, nicht zum Speicher. */
function cluster_one_merkliste_speicher_boden(): bool {
    return function_exists('cluster_one_speicher_zugang') && function_exists('cluster_one_speicher_dav');
}

function cluster_one_merkliste_dav_url(string $c1_url): string {
    if (function_exists('cluster_one_speicher_url')) { return cluster_one_speicher_url($c1_url); }
    $c1_url = trim($c1_url);
    if ($c1_url === '' || strpos($c1_url, 'https://') !== 0 || !wp_http_validate_url($c1_url)) { return ''; }
    return rtrim($c1_url, '/') . '/';
}

function cluster_one_merkliste_datei(int $c1_uid): string {
    /* 0.2.62: die Adresse kommt aus dem Zugang (Boden oder eigener), nicht mehr aus dem eigenen Meta allein. */
    $c1_url = cluster_one_merkliste_zugang($c1_uid)['url'];
    return $c1_url === '' ? '' : $c1_url . 'cluster-one-merkliste.json.enc';
}

/** @return array{code:int, body:string, fehler:string} */
function cluster_one_merkliste_dav(string $c1_methode, string $c1_url, string $c1_benutzer, string $c1_pass, string $c1_body = ''): array {
    if (function_exists('cluster_one_speicher_dav')) { return cluster_one_speicher_dav($c1_methode, $c1_url, $c1_benutzer, $c1_pass, $c1_body); }
    $c1_args = array(
        'method'      => $c1_methode,
        'timeout'     => 15,
        'redirection' => 0,
        'headers'     => array('Authorization' => 'Basic ' . base64_encode($c1_benutzer . ':' . $c1_pass)),
    );
    if ($c1_methode === 'PROPFIND') { $c1_args['headers']['Depth'] = '0'; }
    if ($c1_methode === 'PUT') { $c1_args['headers']['Content-Type'] = 'application/octet-stream'; $c1_args['body'] = $c1_body; }
    $c1_r = wp_remote_request($c1_url, $c1_args);
    if (is_wp_error($c1_r)) { return array('code' => 0, 'body' => '', 'fehler' => $c1_r->get_error_message()); }
    return array('code' => (int) wp_remote_retrieve_response_code($c1_r), 'body' => (string) wp_remote_retrieve_body($c1_r), 'fehler' => '');
}

function cluster_one_merkliste_zugang(int $c1_uid): array {
    /* 0.2.62: der Speicher des Menschen, wie der Boden ihn haelt. Solange der Boden noch
       nichts hat (Umzug steht aus, siehe cluster_one_speicher_uebernahme), gilt der alte Weg. */
    if (cluster_one_merkliste_speicher_boden()) {
        $c1_b = cluster_one_speicher_zugang($c1_uid);
        if ($c1_b['url'] !== '') { return $c1_b; }
    }
    $c1_m = cluster_one_merkliste_meta($c1_uid);
    return array(
        'url'      => cluster_one_merkliste_dav_url((string) ($c1_m['cloud']['url'] ?? '')),
        'benutzer' => (string) ($c1_m['cloud']['benutzer'] ?? ''),
        'pass'     => cluster_one_merkliste_geheim($c1_uid, 'merkliste_speicher'),
        'geprueft' => (int) ($c1_m['cloud']['geprueft'] ?? 0),
    );
}

/* ---------------------------------------------------------------------------
 * Lesen und Schreiben — der eine Weg, ob WordPress oder eigener Speicher
 * ------------------------------------------------------------------------- */
/** @return array{eintraege:array, ort:string, fehler:string} */
function cluster_one_merkliste_lesen(int $c1_uid): array {
    $c1_m = cluster_one_merkliste_meta($c1_uid);
    if ($c1_m['ort'] !== 'cloud') {
        return array('eintraege' => $c1_m['eintraege'], 'ort' => 'wp', 'fehler' => '');
    }
    $c1_z = cluster_one_merkliste_zugang($c1_uid);
    $c1_datei = cluster_one_merkliste_datei($c1_uid);
    if ($c1_datei === '' || $c1_z['pass'] === '') {
        return array('eintraege' => array(), 'ort' => 'cloud', 'fehler' => __('The access data for your storage is incomplete. The list lies there; here stands nothing.', 'cluster-one-bismarck'));
    }
    $c1_key = cluster_one_merkliste_schluessel($c1_uid, false);
    if ($c1_key === '') {
        return array('eintraege' => array(), 'ort' => 'cloud', 'fehler' => __('The key for your stored list is gone. Without it the file cannot be read.', 'cluster-one-bismarck'));
    }
    $c1_r = cluster_one_merkliste_dav('GET', $c1_datei, $c1_z['benutzer'], $c1_z['pass']);
    if ($c1_r['code'] === 404) {
        return array('eintraege' => array(), 'ort' => 'cloud', 'fehler' => '');
    }
    if ($c1_r['code'] < 200 || $c1_r['code'] > 299) {
        return array('eintraege' => array(), 'ort' => 'cloud', 'fehler' => sprintf(
            /* translators: %s: HTTP status code or transport error */
            __('Your storage did not answer (%s). The list lies there; here stands nothing until it answers again.', 'cluster-one-bismarck'),
            $c1_r['code'] > 0 ? (string) $c1_r['code'] : $c1_r['fehler']));
    }
    $c1_e = cluster_one_merkliste_entschluesseln($c1_r['body'], $c1_key);
    if ($c1_e === null) {
        return array('eintraege' => array(), 'ort' => 'cloud', 'fehler' => __('The file in your storage could not be read. It is not what this node wrote there.', 'cluster-one-bismarck'));
    }
    return array('eintraege' => $c1_e, 'ort' => 'cloud', 'fehler' => '');
}

/** @return string '' bei Erfolg, sonst der Satz fuer den Menschen */
function cluster_one_merkliste_schreiben(int $c1_uid, array $c1_eintraege): string {
    $c1_m = cluster_one_merkliste_meta($c1_uid);
    if ($c1_m['ort'] !== 'cloud') {
        $c1_m['eintraege'] = array_values($c1_eintraege);
        cluster_one_merkliste_meta_speichern($c1_uid, $c1_m);
        return '';
    }
    $c1_z = cluster_one_merkliste_zugang($c1_uid);
    $c1_datei = cluster_one_merkliste_datei($c1_uid);
    $c1_key = cluster_one_merkliste_schluessel($c1_uid, false);
    if ($c1_datei === '' || $c1_z['pass'] === '' || $c1_key === '') {
        return __('The access data or the key for your storage is missing. Nothing was written.', 'cluster-one-bismarck');
    }
    $c1_r = cluster_one_merkliste_dav('PUT', $c1_datei, $c1_z['benutzer'], $c1_z['pass'], cluster_one_merkliste_verschluesseln($c1_eintraege, $c1_key));
    if ($c1_r['code'] < 200 || $c1_r['code'] > 299) {
        return sprintf(
            /* translators: %s: HTTP status code or transport error */
            __('Your storage did not take the list (%s). Nothing was changed.', 'cluster-one-bismarck'),
            $c1_r['code'] > 0 ? (string) $c1_r['code'] : $c1_r['fehler']);
    }
    return '';
}

/* ---------------------------------------------------------------------------
 * Die Handler
 * ------------------------------------------------------------------------- */
function cluster_one_merkliste_zurueck(string $c1_code, string $c1_detail = ''): void {
    $c1_url = add_query_arg('merkliste', $c1_code, admin_url('admin.php?page=cluster_one_personal_area'));
    if ($c1_detail !== '') { $c1_url = add_query_arg('merkliste_detail', rawurlencode(mb_substr($c1_detail, 0, 200)), $c1_url); }
    wp_safe_redirect($c1_url);
    exit;
}

add_action('admin_post_cluster_one_merkliste_add', function () {
    if (!function_exists('cluster_one_can_access') || !cluster_one_can_access()) { wp_die('-1', 403); }
    check_admin_referer('cluster_one_merkliste');
    $c1_uid  = get_current_user_id();
    $c1_name = isset($_POST['name']) ? trim(sanitize_text_field(wp_unslash($_POST['name']))) : '';
    if ($c1_name === '') { cluster_one_merkliste_zurueck('kein_name'); }
    $c1_l = cluster_one_merkliste_lesen($c1_uid);
    if ($c1_l['fehler'] !== '') { cluster_one_merkliste_zurueck('fehler', $c1_l['fehler']); }
    $c1_e = $c1_l['eintraege'];
    $c1_e[] = array(
        'id'      => bin2hex(random_bytes(4)),
        'name'    => $c1_name,
        'discord' => isset($_POST['discord']) ? trim(sanitize_text_field(wp_unslash($_POST['discord']))) : '',
        'matrix'  => isset($_POST['matrix'])  ? trim(sanitize_text_field(wp_unslash($_POST['matrix'])))  : '',
        'lang'    => isset($_POST['lang'])    ? trim(sanitize_text_field(wp_unslash($_POST['lang'])))    : '',
        'notiz'   => isset($_POST['notiz'])   ? trim(sanitize_text_field(wp_unslash($_POST['notiz'])))   : '',
        'ts'      => time(),
    );
    $c1_f = cluster_one_merkliste_schreiben($c1_uid, $c1_e);
    $c1_f === '' ? cluster_one_merkliste_zurueck('gespeichert') : cluster_one_merkliste_zurueck('fehler', $c1_f);
});

add_action('admin_post_cluster_one_merkliste_delete', function () {
    if (!function_exists('cluster_one_can_access') || !cluster_one_can_access()) { wp_die('-1', 403); }
    check_admin_referer('cluster_one_merkliste');
    $c1_uid = get_current_user_id();
    $c1_id  = isset($_POST['id']) ? sanitize_key((string) wp_unslash($_POST['id'])) : '';
    $c1_l = cluster_one_merkliste_lesen($c1_uid);
    if ($c1_l['fehler'] !== '') { cluster_one_merkliste_zurueck('fehler', $c1_l['fehler']); }
    $c1_e = array_values(array_filter($c1_l['eintraege'], function ($c1_z) use ($c1_id) {
        return !is_array($c1_z) || (string) ($c1_z['id'] ?? '') !== $c1_id;
    }));
    $c1_f = cluster_one_merkliste_schreiben($c1_uid, $c1_e);
    $c1_f === '' ? cluster_one_merkliste_zurueck('geloescht') : cluster_one_merkliste_zurueck('fehler', $c1_f);
});

add_action('admin_post_cluster_one_merkliste_cloud', function () {
    if (!function_exists('cluster_one_can_access') || !cluster_one_can_access()) { wp_die('-1', 403); }
    check_admin_referer('cluster_one_merkliste_cloud');
    $c1_uid = get_current_user_id();
    $c1_op  = isset($_POST['op']) ? sanitize_key((string) wp_unslash($_POST['op'])) : '';
    $c1_m   = cluster_one_merkliste_meta($c1_uid);

    if (!cluster_one_merkliste_tresor_da()) { cluster_one_merkliste_zurueck('kein_tresor'); }

    if ($c1_op === 'pruefen') {
        $c1_url  = cluster_one_merkliste_dav_url(isset($_POST['url']) ? (string) wp_unslash($_POST['url']) : ''); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- der Wert geht auf dieser Zeile durch den Haus-Reiniger (cluster_one_merkliste_dav_url), den der Pruefer nicht kennt
        $c1_ben  = isset($_POST['benutzer']) ? trim(sanitize_text_field(wp_unslash($_POST['benutzer']))) : '';
        /* 0.2.53: trim() – ein Leerzeichen am Rand stammt aus der Zwischenablage, nicht aus dem
         * Passwort. Bis 0.2.52 ging der Wert roh durch: `(string) wp_unslash($_POST['passwort'])`. */
        $c1_pass = isset($_POST['passwort']) ? trim((string) wp_unslash($_POST['passwort'])) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- ein Passwort wird nicht gereinigt: Bytes fuer den Tresor bzw. die WebDAV-Anmeldung, nie HTML; jede Reinigung koennte es veraendern
        if ($c1_pass === '') { $c1_pass = cluster_one_merkliste_geheim($c1_uid, 'merkliste_speicher'); }
        if ($c1_url === '' || $c1_ben === '' || $c1_pass === '') { cluster_one_merkliste_zurueck('unvollstaendig'); }
        $c1_r = cluster_one_merkliste_dav('PROPFIND', $c1_url, $c1_ben, $c1_pass);
        /* 0.2.53: Bei Ablehnung sagt die Meldung, was der Knoten gesendet hat – Benutzer und Zeichenzahl,
         * nie das Passwort. Ein 401 allein liess nicht erkennen, ob ein Tippfehler oder der Speicher schuld war. */
        $c1_was = sprintf(
            /* translators: 1: user name as sent, 2: number of characters in the password */
            __('Sent: user %1$s, password with %2$d characters.', 'cluster-one-bismarck'),
            $c1_ben, mb_strlen($c1_pass));
        if (preg_match('~\s~u', $c1_pass) === 1) { $c1_was .= ' ' . __('The password contains a space.', 'cluster-one-bismarck'); }
        $c1_ok = ($c1_r['code'] >= 200 && $c1_r['code'] <= 299) || $c1_r['code'] === 207;
        if ($c1_ok) {
            $c1_f = cluster_one_merkliste_geheim_setzen($c1_uid, 'merkliste_speicher', $c1_pass);
            if ($c1_f !== '') { cluster_one_merkliste_zurueck('kein_tresor'); }
        }
        $c1_m['cloud'] = array('url' => $c1_url, 'benutzer' => $c1_ben, 'geprueft' => $c1_ok ? time() : 0);
        cluster_one_merkliste_meta_speichern($c1_uid, $c1_m);
        if ($c1_ok) { cluster_one_merkliste_zurueck('geprueft'); }
        if ($c1_r['code'] === 401 || $c1_r['code'] === 403) { cluster_one_merkliste_zurueck('abgelehnt', $c1_r['code'] . ' · ' . $c1_was); }
        cluster_one_merkliste_zurueck('nicht_erreichbar', $c1_r['code'] > 0 ? (string) $c1_r['code'] : $c1_r['fehler']);
    }

    if ($c1_op === 'hin') {
        if ($c1_m['ort'] === 'cloud') { cluster_one_merkliste_zurueck('hin'); }
        /* 0.2.62: der Pruefstand kommt aus dem Zugang (Boden oder eigener); bis 0.2.61 stand hier
           `(int) ($c1_m['cloud']['geprueft'] ?? 0)`. */
        if (cluster_one_merkliste_zugang($c1_uid)['geprueft'] <= 0) { cluster_one_merkliste_zurueck('ungeprueft'); }
        if (!function_exists('sodium_crypto_secretbox')) { cluster_one_merkliste_zurueck('kein_sodium'); }
        $c1_key = cluster_one_merkliste_schluessel($c1_uid, true);
        if ($c1_key === '') { cluster_one_merkliste_zurueck('kein_tresor'); }
        $c1_eintraege = $c1_m['eintraege'];
        $c1_m['ort'] = 'cloud';
        $c1_m['eintraege'] = array();
        /* Erst schreiben, dann den Zeiger umlegen — und erst dann die Kopie
           hier loeschen. Scheitert das Schreiben, bleibt alles, wie es war. */
        $c1_probe = cluster_one_merkliste_meta($c1_uid);
        $c1_probe['ort'] = 'cloud';
        cluster_one_merkliste_meta_speichern($c1_uid, $c1_probe);
        $c1_f = cluster_one_merkliste_schreiben($c1_uid, $c1_eintraege);
        if ($c1_f !== '') {
            $c1_probe['ort'] = 'wp';
            cluster_one_merkliste_meta_speichern($c1_uid, $c1_probe);
            cluster_one_merkliste_zurueck('fehler', $c1_f);
        }
        cluster_one_merkliste_meta_speichern($c1_uid, $c1_m);
        cluster_one_merkliste_zurueck('hin');
    }

    if ($c1_op === 'zurueck') {
        if ($c1_m['ort'] !== 'cloud') { cluster_one_merkliste_zurueck('zurueck'); }
        $c1_l = cluster_one_merkliste_lesen($c1_uid);
        if ($c1_l['fehler'] !== '') { cluster_one_merkliste_zurueck('fehler', $c1_l['fehler']); }
        $c1_m['ort'] = 'wp';
        $c1_m['eintraege'] = $c1_l['eintraege'];
        cluster_one_merkliste_meta_speichern($c1_uid, $c1_m);
        /* Die Datei im Speicher wird genommen: ein Umzug, keine Kopie. Scheitert
           das Loeschen, steht es da — die Liste ist trotzdem zurueck. */
        $c1_z = cluster_one_merkliste_zugang($c1_uid);
        $c1_r = cluster_one_merkliste_dav('DELETE', cluster_one_merkliste_datei($c1_uid), $c1_z['benutzer'], $c1_z['pass']);
        cluster_one_merkliste_zurueck(($c1_r['code'] >= 200 && $c1_r['code'] <= 299) || $c1_r['code'] === 404 ? 'zurueck' : 'zurueck_rest');
    }

    if ($c1_op === 'vergessen') {
        if ($c1_m['ort'] === 'cloud') { cluster_one_merkliste_zurueck('erst_zurueck'); }
        cluster_one_merkliste_geheim_setzen($c1_uid, 'merkliste_speicher', '');
        cluster_one_merkliste_geheim_setzen($c1_uid, 'merkliste_schluessel', '');
        $c1_m['cloud'] = array();
        cluster_one_merkliste_meta_speichern($c1_uid, $c1_m);
        cluster_one_merkliste_zurueck('vergessen');
    }

    cluster_one_merkliste_zurueck('unvollstaendig');
});

/* ---------------------------------------------------------------------------
 * Mitnehmen und Mitbringen (0.2.55, 27.10.2026)
 *
 * Nemesus, Test 4: eine Liste von einem Knoten auf einen anderen bringen hiess
 * bis hierher, die Namen neu tippen. Jetzt gibt es eine Datei – im Klartext,
 * denn sie geht nur an den Browser des Menschen, dem die Liste gehoert, und
 * liegt nirgends. Das Mitbringen HAENGT AN, es ersetzt nie: nichts
 * verschwindet. Was schon drinsteht (gleicher Name, Discord und Matrix), wird
 * nicht noch einmal eingetragen. Verschluesselt wird die Datei nicht – der
 * Schluessel gehoert dem Knoten, nicht dem Menschen, und eine Datei, die nur
 * ein Knoten lesen kann, waere zum Mitnehmen nutzlos.
 * ------------------------------------------------------------------------- */
add_action('admin_post_cluster_one_merkliste_export', function () {
    if (!function_exists('cluster_one_can_access') || !cluster_one_can_access()) { wp_die('-1', 403); }
    check_admin_referer('cluster_one_merkliste');
    $c1_uid = get_current_user_id();
    $c1_l = cluster_one_merkliste_lesen($c1_uid);
    if ($c1_l['fehler'] !== '') { cluster_one_merkliste_zurueck('fehler', $c1_l['fehler']); }
    $c1_aus = array();
    foreach ($c1_l['eintraege'] as $c1_e) {
        if (!is_array($c1_e)) { continue; }
        $c1_aus[] = array(
            'name'    => (string) ($c1_e['name'] ?? ''),
            'discord' => (string) ($c1_e['discord'] ?? ''),
            'matrix'  => (string) ($c1_e['matrix'] ?? ''),
            'lang'    => (string) ($c1_e['lang'] ?? ''),
            'notiz'   => (string) ($c1_e['notiz'] ?? ''),
        );
    }
    nocache_headers();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="cluster-one-merkliste-' . gmdate('Y-m-d') . '.json"');
    echo (string) wp_json_encode(array('v' => 1, 'was' => 'cluster-one-merkliste', 'eintraege' => $c1_aus), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-Datei zum Herunterladen (Content-Type application/json zwei Zeilen hoeher), kein HTML; wp_json_encode ist die Kodierung
    exit;
});

add_action('admin_post_cluster_one_merkliste_import', function () {
    if (!function_exists('cluster_one_can_access') || !cluster_one_can_access()) { wp_die('-1', 403); }
    check_admin_referer('cluster_one_merkliste');
    $c1_uid = get_current_user_id();
    $c1_tmp = isset($_FILES['datei']['tmp_name']) ? (string) $_FILES['datei']['tmp_name'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- $_FILES: tmp_name und size werden mit is_uploaded_file und Groessengrenze geprueft, nie ausgegeben
    if ($c1_tmp === '' || !is_uploaded_file($c1_tmp) || (int) ($_FILES['datei']['size'] ?? 0) > 1048576) { cluster_one_merkliste_zurueck('import_leer'); } // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- $_FILES: tmp_name und size werden mit is_uploaded_file und Groessengrenze geprueft, nie ausgegeben
    $c1_d = json_decode((string) file_get_contents($c1_tmp), true);
    if (!is_array($c1_d) || ($c1_d['was'] ?? '') !== 'cluster-one-merkliste' || !isset($c1_d['eintraege']) || !is_array($c1_d['eintraege'])) {
        cluster_one_merkliste_zurueck('import_form');
    }
    $c1_l = cluster_one_merkliste_lesen($c1_uid);
    if ($c1_l['fehler'] !== '') { cluster_one_merkliste_zurueck('fehler', $c1_l['fehler']); }
    $c1_e = $c1_l['eintraege'];
    $c1_da = array();
    foreach ($c1_e as $c1_z) {
        if (is_array($c1_z)) { $c1_da[(string) ($c1_z['name'] ?? '') . "\n" . (string) ($c1_z['discord'] ?? '') . "\n" . (string) ($c1_z['matrix'] ?? '')] = true; }
    }
    $c1_neu = 0; $c1_doppelt = 0;
    foreach ($c1_d['eintraege'] as $c1_z) {
        if (!is_array($c1_z)) { continue; }
        $c1_name = trim(sanitize_text_field((string) ($c1_z['name'] ?? '')));
        if ($c1_name === '') { continue; }
        $c1_discord = trim(sanitize_text_field((string) ($c1_z['discord'] ?? '')));
        $c1_matrix  = trim(sanitize_text_field((string) ($c1_z['matrix'] ?? '')));
        $c1_k = $c1_name . "\n" . $c1_discord . "\n" . $c1_matrix;
        if (isset($c1_da[$c1_k])) { $c1_doppelt++; continue; }
        $c1_da[$c1_k] = true;
        $c1_e[] = array(
            'id'      => bin2hex(random_bytes(4)),
            'name'    => $c1_name,
            'discord' => $c1_discord,
            'matrix'  => $c1_matrix,
            'lang'    => trim(sanitize_text_field((string) ($c1_z['lang'] ?? ''))),
            'notiz'   => trim(sanitize_text_field((string) ($c1_z['notiz'] ?? ''))),
            'ts'      => time(),
        );
        $c1_neu++;
    }
    if ($c1_neu === 0) { cluster_one_merkliste_zurueck('import_nichts', (string) $c1_doppelt); }
    $c1_f = cluster_one_merkliste_schreiben($c1_uid, $c1_e);
    $c1_f === '' ? cluster_one_merkliste_zurueck('importiert', $c1_neu . '/' . $c1_doppelt) : cluster_one_merkliste_zurueck('fehler', $c1_f);
});

/* ---------------------------------------------------------------------------
 * Der Abschnitt im Persoenlichen Bereich
 * ------------------------------------------------------------------------- */
function cluster_one_merkliste_meldung(): void {
    if (!isset($_GET['merkliste'])) { return; } // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $c1_c = sanitize_key((string) wp_unslash($_GET['merkliste'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $c1_d = isset($_GET['merkliste_detail']) ? sanitize_text_field((string) wp_unslash($_GET['merkliste_detail'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lesen eines Schalters aus der Adresse beim Zeichnen (Hinweis, Reiter, Kennung), kein Schreibweg; die Schreibwege pruefen ihren Nonce im admin_post-Handler
    $c1_ok = array(
        'gespeichert' => __('Noted.', 'cluster-one-bismarck'),
        'geloescht'   => __('Removed.', 'cluster-one-bismarck'),
        'geprueft'    => __('Your storage answered. You can now move the list there.', 'cluster-one-bismarck'),
        'hin'         => __('The list now lives in your storage. This node keeps only the address.', 'cluster-one-bismarck'),
        'zurueck'     => __('The list is back in WordPress; the file in your storage was removed.', 'cluster-one-bismarck'),
        'zurueck_rest' => __('The list is back in WordPress. The file in your storage could not be removed and is still there.', 'cluster-one-bismarck'),
        'vergessen'   => __('Access data and key forgotten.', 'cluster-one-bismarck'),
    );
    /* 0.2.55: 'importiert' traegt zwei Zahlen (neu/doppelt) im Detail; der Satz wird daraus gebaut. */
    if ($c1_c === 'importiert') {
        $c1_t = explode('/', $c1_d);
        $c1_ok['importiert'] = sprintf(
            /* translators: 1: entries added, 2: entries already on the list */
            __('Brought along: %1$d added, %2$d were already on the list.', 'cluster-one-bismarck'),
            (int) ($c1_t[0] ?? 0), (int) ($c1_t[1] ?? 0));
        $c1_d = '';
    }
    $c1_warn = array(
        'kein_name'      => __('A name is needed.', 'cluster-one-bismarck'),
        'unvollstaendig' => __('Address (https), user name and password are needed.', 'cluster-one-bismarck'),
        'abgelehnt'      => __('Your storage refused the access data.', 'cluster-one-bismarck'),
        'nicht_erreichbar' => __('Your storage did not answer.', 'cluster-one-bismarck'),
        'ungeprueft'     => __('Check the access first.', 'cluster-one-bismarck'),
        'kein_tresor'    => __('No key vault on this node: the password cannot be kept, so the list stays in WordPress.', 'cluster-one-bismarck'),
        'kein_sodium'    => __('This server cannot encrypt (no sodium). The list stays in WordPress.', 'cluster-one-bismarck'),
        'erst_zurueck'   => __('Bring the list back first; then the access can be forgotten.', 'cluster-one-bismarck'),
        'fehler'         => __('Nothing was changed:', 'cluster-one-bismarck'),
        'import_leer'    => __('No file arrived, or it is larger than 1 MiB. Nothing was changed.', 'cluster-one-bismarck'),
        'import_form'    => __('This file is not a list this node would write. Nothing was changed.', 'cluster-one-bismarck'),
        'import_nichts'  => __('Nothing new in the file; every entry was already on the list. Already there:', 'cluster-one-bismarck'),
    );
    if (isset($c1_ok[$c1_c])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($c1_ok[$c1_c]) . '</p></div>';
    } elseif (isset($c1_warn[$c1_c])) {
        echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html($c1_warn[$c1_c]) . ($c1_d !== '' ? ' ' . esc_html($c1_d) : '') . '</p></div>';
    }
}

function cluster_one_render_merkliste_section(): void {
    if (!function_exists('cluster_one_can_access') || !cluster_one_can_access()) { return; }
    $c1_uid = get_current_user_id();
    $c1_l   = cluster_one_merkliste_lesen($c1_uid);
    $c1_m   = cluster_one_merkliste_meta($c1_uid);
    $c1_z   = cluster_one_merkliste_zugang($c1_uid);
    $c1_tresor = cluster_one_merkliste_tresor_da();
    ?>
    <div style="max-width:860px;">
        <h2 style="margin-top:1.6em;"><?php esc_html_e('My list of people to invite', 'cluster-one-bismarck'); ?></h2>
        <p style="opacity:.75;max-width:680px;"><?php esc_html_e('Your own notebook: who you want on this node, and how you would reach them. Nobody else on the node sees it, and nothing is sent from here.', 'cluster-one-bismarck'); ?></p>
        <?php cluster_one_merkliste_meldung(); ?>

        <?php if ($c1_l['fehler'] !== '') : ?>
            <div class="notice notice-error inline"><p><strong><?php esc_html_e('Your list is in your own storage and could not be read.', 'cluster-one-bismarck'); ?></strong> <?php echo esc_html($c1_l['fehler']); ?></p></div>
        <?php else : ?>
            <?php if ($c1_l['eintraege'] === array()) : ?>
                <p style="opacity:.6;"><?php esc_html_e('Nobody noted yet.', 'cluster-one-bismarck'); ?></p>
            <?php else : ?>
            <table class="widefat striped" style="max-width:860px;">
                <thead><tr>
                    <th><?php esc_html_e('Name', 'cluster-one-bismarck'); ?></th>
                    <th>Discord</th><th>Matrix</th>
                    <th><?php esc_html_e('Language', 'cluster-one-bismarck'); ?></th>
                    <th><?php esc_html_e('Note', 'cluster-one-bismarck'); ?></th>
                    <th style="width:6em;"></th>
                </tr></thead>
                <tbody>
                <?php foreach ($c1_l['eintraege'] as $c1_e) : if (!is_array($c1_e)) { continue; } ?>
                    <tr>
                        <td><strong><?php echo esc_html((string) ($c1_e['name'] ?? '')); ?></strong></td>
                        <td><?php echo esc_html((string) ($c1_e['discord'] ?? '')); ?></td>
                        <td><?php echo esc_html((string) ($c1_e['matrix'] ?? '')); ?></td>
                        <td><?php echo esc_html((string) ($c1_e['lang'] ?? '')); ?></td>
                        <td><?php echo esc_html((string) ($c1_e['notiz'] ?? '')); ?></td>
                        <td>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                                <?php wp_nonce_field('cluster_one_merkliste'); ?>
                                <input type="hidden" name="action" value="cluster_one_merkliste_delete">
                                <input type="hidden" name="id" value="<?php echo esc_attr((string) ($c1_e['id'] ?? '')); ?>">
                                <button type="submit" class="button button-small"><?php esc_html_e('Remove', 'cluster-one-bismarck'); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:860px;margin-top:.8em;">
                <?php wp_nonce_field('cluster_one_merkliste'); ?>
                <input type="hidden" name="action" value="cluster_one_merkliste_add">
                <p style="display:flex;gap:.5em;flex-wrap:wrap;">
                    <input type="text" name="name" required placeholder="<?php echo esc_attr__('Name', 'cluster-one-bismarck'); ?>" style="flex:1 1 10em;">
                    <input type="text" name="discord" placeholder="Discord" style="flex:1 1 9em;">
                    <input type="text" name="matrix" placeholder="Matrix" style="flex:1 1 9em;">
                    <input type="text" name="lang" placeholder="<?php echo esc_attr__('Language', 'cluster-one-bismarck'); ?>" style="flex:0 1 5em;">
                    <input type="text" name="notiz" placeholder="<?php echo esc_attr__('Note', 'cluster-one-bismarck'); ?>" style="flex:2 1 12em;">
                    <button type="submit" class="button"><?php esc_html_e('Note down', 'cluster-one-bismarck'); ?></button>
                </p>
            </form>

            <?php /* 0.2.55: Mitnehmen und Mitbringen – siehe die Handler oben. */ ?>
            <div style="display:flex;gap:1.2em;flex-wrap:wrap;align-items:center;margin-top:.4em;opacity:.85;">
                <?php if ($c1_l['eintraege'] !== array()) : ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin:0;">
                    <?php wp_nonce_field('cluster_one_merkliste'); ?>
                    <input type="hidden" name="action" value="cluster_one_merkliste_export">
                    <button type="submit" class="button-link"><?php esc_html_e('Take the list with you (file)', 'cluster-one-bismarck'); ?></button>
                </form>
                <?php endif; ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" style="margin:0;display:flex;gap:.5em;align-items:center;">
                    <?php wp_nonce_field('cluster_one_merkliste'); ?>
                    <input type="hidden" name="action" value="cluster_one_merkliste_import">
                    <label><?php esc_html_e('Bring a list along:', 'cluster-one-bismarck'); ?> <input type="file" name="datei" accept="application/json,.json" required></label>
                    <button type="submit" class="button button-small"><?php esc_html_e('Add to my list', 'cluster-one-bismarck'); ?></button>
                </form>
            </div>
            <p class="description" style="max-width:680px;"><?php esc_html_e('The file is plain text and goes only to you. Bringing a list along adds to this one; it never replaces it, and what is already here is not added twice.', 'cluster-one-bismarck'); ?></p>
        <?php endif; ?>

        <h3 style="margin-top:1.4em;"><?php esc_html_e('Where the list lives', 'cluster-one-bismarck'); ?></h3>
        <?php /* 0.2.62 (28.10.2026, Neo): MIT BODEN 1.19.0 zeigt dieser Abschnitt kein Adressformular mehr.
           Der Speicher ist „Mein Speicher" oben auf der Seite (kranz.php); hier steht nur noch, wo die
           Liste liegt, und hin/zurueck. Der alte Zweig darunter bleibt woertlich fuer einen aelteren Boden. */
        if (cluster_one_merkliste_speicher_boden() && $c1_tresor) : ?>
            <?php if ($c1_m['ort'] === 'cloud') : ?>
                <p style="max-width:680px;"><?php echo esc_html(sprintf(
                    /* translators: %s: WebDAV address */
                    __('In your own storage at %s, encrypted with a key this node holds for you. The node keeps only the address.', 'cluster-one-bismarck'), $c1_z['url'])); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('cluster_one_merkliste_cloud'); ?>
                    <input type="hidden" name="action" value="cluster_one_merkliste_cloud">
                    <button type="submit" name="op" value="zurueck" class="button"><?php esc_html_e('Bring the list back to WordPress', 'cluster-one-bismarck'); ?></button>
                </form>
            <?php elseif (function_exists('cluster_one_speicher_da') && cluster_one_speicher_da($c1_uid)) : ?>
                <p style="max-width:680px;"><?php echo esc_html(sprintf(
                    /* translators: %s: WebDAV address */
                    __('In this WordPress. Your storage at %s is checked — you can move the list there.', 'cluster-one-bismarck'), $c1_z['url'])); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('cluster_one_merkliste_cloud'); ?>
                    <input type="hidden" name="action" value="cluster_one_merkliste_cloud">
                    <button type="submit" name="op" value="hin" class="button button-primary"><?php esc_html_e('Move the list there', 'cluster-one-bismarck'); ?></button>
                </form>
                <p class="description" style="max-width:680px;"><?php esc_html_e('Moving means: the list is written there encrypted and deleted here. This node can still read it as long as it holds your key; if Pacioli is removed, the key goes with it — bring the list back first.', 'cluster-one-bismarck'); ?></p>
            <?php else : ?>
                <p style="opacity:.75;max-width:680px;"><?php esc_html_e('In this WordPress. To keep it in a storage of your own, set up “My storage” at the top of this page and check the access; then the list can move there.', 'cluster-one-bismarck'); ?></p>
            <?php endif; ?>
        <?php elseif (!$c1_tresor) : ?>
            <p style="opacity:.75;max-width:680px;"><?php esc_html_e('In this WordPress. Keeping it in a storage of your own needs the key vault (Pacioli) to hold the password; this node does not have it.', 'cluster-one-bismarck'); ?></p>
        <?php else : ?>
            <?php if ($c1_m['ort'] === 'cloud') : ?>
                <p style="max-width:680px;"><?php echo esc_html(sprintf(
                    /* translators: %s: WebDAV address */
                    __('In your own storage at %s, encrypted with a key this node holds for you. The node keeps only the address.', 'cluster-one-bismarck'), $c1_z['url'])); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('cluster_one_merkliste_cloud'); ?>
                    <input type="hidden" name="action" value="cluster_one_merkliste_cloud">
                    <button type="submit" name="op" value="zurueck" class="button"><?php esc_html_e('Bring the list back to WordPress', 'cluster-one-bismarck'); ?></button>
                </form>
            <?php else : ?>
                <?php /* 0.2.55: Die Beispiele sind belegt, nicht geraten. Bis 0.2.54 stand hier
                   „(a Nextcloud, a storage box, a folder on your hosting)" – und „a folder on your
                   hosting" war bei All-Inkl falsch: das KAS-Netzlaufwerk spricht SMB, nicht WebDAV
                   (Test 4, 27.10.2026). GMX Cloud ist seit Test 4 der belegte Fall: Adresse
                   https://webdav.mc.gmx.net/<Ordner>/, Benutzer = volle E-Mail-Adresse, Passwort = App-Passwort. */ ?>
                <p style="opacity:.75;max-width:680px;"><?php esc_html_e('In this WordPress. If you have a storage of your own that speaks WebDAV over https (a Nextcloud, a Hetzner Storage Box, the GMX or Web.de cloud), you can keep the list there instead. Enter the address of a folder, check the access, then move the list. GMX and Web.de take the full e-mail address as user name and an app password, not the account password. A plain hosting folder usually does not speak WebDAV; All-Inkl’s network drive, for one, does not.', 'cluster-one-bismarck'); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:680px;">
                    <?php wp_nonce_field('cluster_one_merkliste_cloud'); ?>
                    <input type="hidden" name="action" value="cluster_one_merkliste_cloud">
                    <p><input type="url" name="url" class="regular-text" style="width:100%;" placeholder="https://cloud.example.org/remote.php/dav/files/me/cluster-one/" value="<?php echo esc_attr($c1_z['url']); ?>"></p>
                    <p style="display:flex;gap:.5em;flex-wrap:wrap;">
                        <input type="text" name="benutzer" placeholder="<?php echo esc_attr__('User name', 'cluster-one-bismarck'); ?>" value="<?php echo esc_attr($c1_z['benutzer']); ?>" style="flex:1 1 10em;" autocomplete="off">
                        <input type="password" name="passwort" placeholder="<?php echo esc_attr($c1_z['pass'] !== '' ? __('Password (kept; leave empty to keep it)', 'cluster-one-bismarck') : __('Password or app password', 'cluster-one-bismarck')); ?>" style="flex:1 1 12em;" autocomplete="new-password">
                        <button type="submit" name="op" value="pruefen" class="button"><?php esc_html_e('Check access', 'cluster-one-bismarck'); ?></button>
                    </p>
                    <?php if ($c1_z['geprueft'] > 0) : ?>
                        <p><span style="color:#1a7f37;">✓</span> <?php echo esc_html(sprintf(
                            /* translators: %s: date and time */
                            __('Access checked on %s.', 'cluster-one-bismarck'), date_i18n(get_option('date_format') . ' H:i', $c1_z['geprueft']))); ?>
                            <button type="submit" name="op" value="hin" class="button button-primary" style="margin-left:.6em;"><?php esc_html_e('Move the list there', 'cluster-one-bismarck'); ?></button>
                            <button type="submit" name="op" value="vergessen" class="button-link" style="margin-left:.6em;color:#8a8a8a;"><?php esc_html_e('Forget access', 'cluster-one-bismarck'); ?></button>
                        </p>
                        <p class="description" style="max-width:680px;"><?php esc_html_e('Moving means: the list is written there encrypted and deleted here. This node can still read it as long as it holds your key; if Pacioli is removed, the key goes with it — bring the list back first.', 'cluster-one-bismarck'); ?></p>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
}

add_action('cluster_one_abschnitt', function ($c1_wo) {
    if ((string) $c1_wo === 'persoenlich') { cluster_one_render_merkliste_section(); }
});

/* 0.2.62 (28.10.2026, Neo): WER ETWAS IM SPEICHER HAT, MELDET ES AN (Boden 1.19.0,
   cluster_one_speicher_belegt). Liegt die Liste im Speicher, kann der Mensch den Zugang
   dort nicht vergessen, bevor er sie zurueckgeholt hat – derselbe Riegel wie 'erst_zurueck'
   hier, nur eine Ebene hoeher. 'knoten_liest' => true: der Knoten haelt den Schluessel. */
add_filter('cluster_one_speicher_belegt', function ($c1_liste, $c1_uid) {
    $c1_liste = is_array($c1_liste) ? $c1_liste : array();
    $c1_m = cluster_one_merkliste_meta((int) $c1_uid);
    if ($c1_m['ort'] === 'cloud') {
        $c1_liste[] = array(
            'wirt'         => function_exists('cluster_one_kasper_wirt') ? cluster_one_kasper_wirt(__FILE__) : 'cluster-one-bismarck',
            'was'          => __('My list of people to invite', 'cluster-one-bismarck'),
            'datei'        => 'cluster-one-merkliste.json.enc',
            'knoten_liest' => true,
        );
    }
    return $c1_liste;
}, 10, 2);

}
