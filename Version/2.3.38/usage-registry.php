<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$config_file = __DIR__ . '/usage-registry-config.php';
if (file_exists($config_file)) {
    require $config_file;
}

$write_key = defined('FEUER_MANNSCHAFT_USAGE_WRITE_KEY') ? (string) FEUER_MANNSCHAFT_USAGE_WRITE_KEY : '';
$read_key  = defined('FEUER_MANNSCHAFT_USAGE_READ_KEY') ? (string) FEUER_MANNSCHAFT_USAGE_READ_KEY : '';
$data_file = defined('FEUER_MANNSCHAFT_USAGE_DATA_FILE') ? (string) FEUER_MANNSCHAFT_USAGE_DATA_FILE : __DIR__ . '/usage-registry-data.php';

function feuer_mannschaft_usage_registry_respond(int $status_code, array $payload): void {
    http_response_code($status_code);
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

function feuer_mannschaft_usage_registry_get_header(string $header_name): string {
    $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $header_name));

    return isset($_SERVER[$normalized]) ? trim((string) $_SERVER[$normalized]) : '';
}

function feuer_mannschaft_usage_registry_sanitize_text(string $value): string {
    return trim(strip_tags($value));
}

function feuer_mannschaft_usage_registry_sanitize_url(string $value): string {
    $value = trim($value);
    $url   = filter_var($value, FILTER_VALIDATE_URL);

    if (false === $url) {
        return '';
    }

    return rtrim((string) $url, '/') . '/';
}

function feuer_mannschaft_usage_registry_read(string $file_path): array {
    if (! file_exists($file_path)) {
        return array('sites' => array());
    }

    $contents = (string) file_get_contents($file_path);
    $header   = "<?php exit; ?>\n";

    if (0 === strpos($contents, $header)) {
        $contents = substr($contents, strlen($header));
    }

    $data = json_decode($contents, true);

    return is_array($data) && isset($data['sites']) && is_array($data['sites'])
        ? $data
        : array('sites' => array());
}

function feuer_mannschaft_usage_registry_write(string $file_path, array $data): bool {
    $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    if (false === $encoded) {
        return false;
    }

    return false !== file_put_contents($file_path, "<?php exit; ?>\n" . $encoded, LOCK_EX);
}

if ('POST' === strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'))) {
    if ('' === $write_key) {
        feuer_mannschaft_usage_registry_respond(503, array(
            'success' => false,
            'message' => 'Write Key wurde noch nicht konfiguriert.',
        ));
    }

    $request_key = feuer_mannschaft_usage_registry_get_header('X-Feuer-Mannschaft-Write-Key');

    if ($request_key !== $write_key) {
        feuer_mannschaft_usage_registry_respond(403, array(
            'success' => false,
            'message' => 'Ungueltiger Write Key.',
        ));
    }

    $payload = json_decode((string) file_get_contents('php://input'), true);

    if (! is_array($payload)) {
        feuer_mannschaft_usage_registry_respond(400, array(
            'success' => false,
            'message' => 'JSON-Payload fehlt oder ist ungueltig.',
        ));
    }

    $site_url = feuer_mannschaft_usage_registry_sanitize_url((string) ($payload['site_url'] ?? ''));

    if ('' === $site_url) {
        feuer_mannschaft_usage_registry_respond(422, array(
            'success' => false,
            'message' => 'Es wurde keine gueltige Site-URL uebergeben.',
        ));
    }

    $registry = feuer_mannschaft_usage_registry_read($data_file);
    $sites    = isset($registry['sites']) ? $registry['sites'] : array();
    $now      = gmdate('c');
    $record   = isset($sites[$site_url]) && is_array($sites[$site_url]) ? $sites[$site_url] : array();

    if (! isset($record['registered_at'])) {
        $record['registered_at'] = $now;
    }

    $record['site_url']        = $site_url;
    $record['site_name']       = feuer_mannschaft_usage_registry_sanitize_text((string) ($payload['site_name'] ?? ''));
    $record['plugin_slug']     = feuer_mannschaft_usage_registry_sanitize_text((string) ($payload['plugin_slug'] ?? 'feuer-mannschaft'));
    $record['plugin_version']  = feuer_mannschaft_usage_registry_sanitize_text((string) ($payload['plugin_version'] ?? ''));
    $record['wp_version']      = feuer_mannschaft_usage_registry_sanitize_text((string) ($payload['wp_version'] ?? ''));
    $record['php_version']     = feuer_mannschaft_usage_registry_sanitize_text((string) ($payload['php_version'] ?? ''));
    $record['locale']          = feuer_mannschaft_usage_registry_sanitize_text((string) ($payload['locale'] ?? ''));
    $record['status']          = in_array((string) ($payload['status'] ?? 'active'), array('active', 'inactive'), true)
        ? (string) $payload['status']
        : 'active';
    $record['is_multisite']    = ! empty($payload['is_multisite']) ? 1 : 0;
    $record['last_seen_at']    = $now;

    $sites[$site_url] = $record;
    $registry['sites'] = $sites;

    if (! feuer_mannschaft_usage_registry_write($data_file, $registry)) {
        feuer_mannschaft_usage_registry_respond(500, array(
            'success' => false,
            'message' => 'Die Registry-Datei konnte nicht gespeichert werden.',
        ));
    }

    feuer_mannschaft_usage_registry_respond(200, array(
        'success'    => true,
        'message'    => 'Site erfolgreich registriert.',
        'site'       => $record,
        'total_sites'=> count($sites),
    ));
}

$action = isset($_GET['action']) ? trim((string) $_GET['action']) : '';

if ('list' !== $action) {
    feuer_mannschaft_usage_registry_respond(200, array(
        'success' => true,
        'message' => 'Usage Registry ist erreichbar.',
    ));
}

if ('' === $read_key) {
    feuer_mannschaft_usage_registry_respond(503, array(
        'success' => false,
        'message' => 'Read Key wurde noch nicht konfiguriert.',
    ));
}

$request_read_key = feuer_mannschaft_usage_registry_get_header('X-Feuer-Mannschaft-Read-Key');
if ('' === $request_read_key && isset($_GET['key'])) {
    $request_read_key = trim((string) $_GET['key']);
}

if ($request_read_key !== $read_key) {
    feuer_mannschaft_usage_registry_respond(403, array(
        'success' => false,
        'message' => 'Ungueltiger Read Key.',
    ));
}

$registry = feuer_mannschaft_usage_registry_read($data_file);
$sites    = array_values(isset($registry['sites']) ? $registry['sites'] : array());

usort(
    $sites,
    function (array $left, array $right): int {
        return strcmp(
            (string) ($right['last_seen_at'] ?? ''),
            (string) ($left['last_seen_at'] ?? '')
        );
    }
);

feuer_mannschaft_usage_registry_respond(200, array(
    'success'    => true,
    'sites'      => $sites,
    'total_sites'=> count($sites),
));
