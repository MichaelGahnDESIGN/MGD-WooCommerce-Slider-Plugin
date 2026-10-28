<?php
defined('ABSPATH') || exit;

// Beitragstyp (kein öffentlicher Link, keine eigene Seite)
function mgd_slider_register_post_type(): void
{
    register_post_type('mgd_slider', [
        'labels' => ['name' => 'Slider', 'singular_name' => 'Slider', 'add_new_item' => 'Neuen Slider anlegen', 'edit_item' => 'Slider bearbeiten'],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false, // eigenes Menü in admin.php
        'show_in_rest' => false,
        'supports' => ['title'],
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ]);
}

// Anzahl vorhandener Slider (ohne Papierkorb und Auto-Entwürfe)
function mgd_slider_count(): int
{
    $q = new WP_Query(['post_type' => 'mgd_slider', 'post_status' => ['publish', 'draft', 'pending', 'private'], 'fields' => 'ids', 'posts_per_page' => -1, 'no_found_rows' => true]);

    return count($q->posts);
}

// Slider laden > bereinigte Config (Kern) oder null
function mgd_slider_get(int $id): ?array
{
    $post = get_post($id);
    if (!$post || $post->post_type !== 'mgd_slider' || $post->post_status === 'trash') {
        return null;
    }
    $raw = json_decode((string) get_post_meta($id, '_mgd_slider_config', true), true);
    $cfg = \MGD\Slider\Config::normalize(is_array($raw) ? $raw : []);
    if ($cfg['label'] === '') {
        $cfg['label'] = $post->post_title;
    }

    return $cfg;
}

// Limit serverseitig beim Anlegen (auch per REST/Import), nicht nur in der Oberfläche
add_filter('wp_insert_post_data', function (array $data, array $postarr): array {
    if (($data['post_type'] ?? '') !== 'mgd_slider' || in_array($data['post_status'], ['auto-draft', 'trash', 'inherit'], true)) {
        return $data;
    }
    $existing = !empty($postarr['ID']) ? get_post_status((int) $postarr['ID']) : false;
    if ($existing && !in_array($existing, ['auto-draft', 'trash'], true)) {
        return $data; // bestehender Slider wird nur bearbeitet
    }
    if (!\MGD\Slider\Limits::canCreate(mgd_slider_count())) {
        wp_die(esc_html(\MGD\Slider\Limits::reachedMessage()), 'Slider-Limit', ['back_link' => true, 'response' => 403]);
    }

    return $data;
}, 10, 2);
