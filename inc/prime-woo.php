<?php
defined('ABSPATH') || exit;

// Kandidaten für Prime aus WooCommerce (WP_Query). Format wie Prime::select erwartet.
function mgd_slider_prime_candidates(array $cfg): array
{
    if (!function_exists('wc_get_product')) {
        return []; // WooCommerce fehlt > Prime bleibt leer
    }
    $p = $cfg['prime'];
    $args = ['post_type' => 'product', 'post_status' => 'publish', 'fields' => 'ids', 'posts_per_page' => 100, 'no_found_rows' => true,
        'meta_query' => [['key' => '_stock_status', 'value' => 'instock']]];

    if ($p['source'] === 'random') {
        $args['orderby'] = 'rand';
    } elseif ($p['source'] === 'bestseller') {
        $args['meta_key'] = 'total_sales';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'DESC';
    } elseif ($p['source'] === 'category') {
        if (!$p['categories']) {
            return [];
        }
        $args['orderby'] = 'rand';
        $args['tax_query'] = [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => array_map('intval', $p['categories'])]];
    } else { // manual: ID oder Artikelnummer
        $ids = array_map('intval', array_filter($p['products'], 'ctype_digit'));
        $skus = array_values(array_filter($p['products'], fn ($v) => !ctype_digit((string) $v)));
        $found = $ids;
        foreach ($skus as $sku) {
            $id = wc_get_product_id_by_sku($sku);
            if ($id) {
                $found[] = $id;
            }
        }
        if (!$found) {
            return [];
        }
        $args['post__in'] = array_values(array_unique($found));
        $args['orderby'] = 'rand';
    }

    $out = [];
    foreach ((new WP_Query($args))->posts as $pid) {
        $prod = wc_get_product($pid);
        if (!$prod || !$prod->is_visible()) {
            continue;
        }
        $img = $prod->get_image_id();
        $url = $img ? wp_get_attachment_image_url($img, 'large') : '';
        $set = $img ? wp_get_attachment_image_srcset($img, 'large') : '';
        $out[] = [
            'id' => (string) $pid,
            'sku' => (string) $prod->get_sku(),
            'name' => $prod->get_name(),
            'url' => get_permalink($pid),
            'image' => (string) $url,
            'imageSet' => (string) $set,
            'alt' => $img ? (string) get_post_meta($img, '_wp_attachment_image_alt', true) : '',
            'price' => wp_strip_all_tags(html_entity_decode($prod->get_price_html(), ENT_QUOTES, 'UTF-8')),
            'sales' => (int) $prod->get_total_sales(),
            'categories' => array_map('strval', $prod->get_category_ids()),
            'inStock' => $prod->is_in_stock(),
        ];
    }

    return $out;
}

// Suche im Backend: Name oder Artikelnummer (Ajax, nur für Redakteure)
add_action('wp_ajax_mgd_slider_product_search', function (): void {
    check_ajax_referer('mgd_slider_admin', 'nonce');
    if (!current_user_can('edit_posts') || !function_exists('wc_get_product')) {
        wp_send_json_error([], 403);
    }
    global $wpdb;
    $q = sanitize_text_field(wp_unslash($_GET['q'] ?? ''));
    if (mb_strlen($q) < 2) {
        wp_send_json_success([]);
    }
    $like = '%' . $wpdb->esc_like($q) . '%';
    $ids = $wpdb->get_col($wpdb->prepare(
        "SELECT DISTINCT p.ID FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_sku'
         WHERE p.post_type = 'product' AND p.post_status IN ('publish','private') AND (p.post_title LIKE %s OR m.meta_value LIKE %s) LIMIT 20",
        $like,
        $like
    ));
    $res = [];
    foreach ($ids as $id) {
        $prod = wc_get_product((int) $id);
        if ($prod) {
            $res[] = ['id' => (string) $id, 'sku' => (string) $prod->get_sku(), 'name' => $prod->get_name()];
        }
    }
    wp_send_json_success($res);
});
