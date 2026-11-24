<?php
defined('ABSPATH') || exit;

// WordPress-Inhalte als Slide-Quelle (Beiträge, Seiten, eigene Inhaltstypen) - Kern bleibt unberührt
// Zusatz-Einstellung pro Slider liegt in _mgd_slider_content: {"content":"shop|wp","types":["post","page"]}

const MGD_SLIDER_CONTENT_META = '_mgd_slider_content';

// Läuft WooCommerce? Dann gibt es Shop-Artikel als Quelle
function mgd_slider_shop_active(): bool
{
    return function_exists('wc_get_product');
}

// Inhaltstypen die man wählen darf (öffentlich, mit Bild-Unterstützung), Slider selbst und Shop-Artikel nicht
function mgd_slider_content_types(): array
{
    $out = [];
    foreach (get_post_types(['public' => true], 'objects') as $name => $obj) {
        if (in_array($name, ['mgd_slider', 'attachment', 'product'], true) || !post_type_supports($name, 'thumbnail')) {
            continue;
        }
        $out[$name] = (string) $obj->labels->name;
    }

    return $out;
}

// Einstellung lesen und bereinigen. Standard: Shop wenn WooCommerce da ist, sonst WordPress-Inhalte
function mgd_slider_content_get(int $id): array
{
    $raw = json_decode((string) get_post_meta($id, MGD_SLIDER_CONTENT_META, true), true);

    return mgd_slider_content_clean(is_array($raw) ? $raw : []);
}

function mgd_slider_content_clean(array $raw): array
{
    $content = ($raw['content'] ?? '') === 'wp' ? 'wp' : (($raw['content'] ?? '') === 'shop' ? 'shop' : (mgd_slider_shop_active() ? 'shop' : 'wp'));
    if ($content === 'shop' && !mgd_slider_shop_active()) {
        $content = 'wp'; // ohne WooCommerce gibt es keine Artikel
    }
    $allowed = array_keys(mgd_slider_content_types());
    $types = array_values(array_intersect(array_map('sanitize_key', (array) ($raw['types'] ?? [])), $allowed));
    if (!$types) {
        $types = in_array('post', $allowed, true) ? ['post'] : array_slice($allowed, 0, 1);
    }

    return ['content' => $content, 'types' => $types];
}

// Kandidaten im Format wie Prime::select es erwartet. Reihenfolge: "bestseller" heisst hier Neueste zuerst (sales = Zeitstempel)
function mgd_slider_wp_candidates(array $cfg, array $ctn): array
{
    $p = $cfg['prime'];
    $args = ['post_type' => $ctn['types'], 'post_status' => 'publish', 'fields' => 'ids', 'posts_per_page' => 100, 'no_found_rows' => true,
        'has_password' => false, 'meta_query' => [['key' => '_thumbnail_id', 'value' => 0, 'compare' => '>', 'type' => 'NUMERIC']]]; // echtes Bild, leere Eintraege zaehlen nicht

    if ($p['source'] === 'category') {
        if (!$p['categories']) {
            return [];
        }
        $args['category__in'] = array_map('intval', $p['categories']);
    } elseif ($p['source'] === 'manual') {
        $ids = array_values(array_filter(array_map('intval', array_filter($p['products'], 'ctype_digit'))));
        if (!$ids) {
            return [];
        }
        $args['post__in'] = $ids;
    }
    $args['orderby'] = 'date';
    $args['order'] = 'DESC';

    $out = [];
    foreach ((new WP_Query($args))->posts as $pid) {
        $img = (int) get_post_thumbnail_id($pid);
        $url = $img ? wp_get_attachment_image_url($img, 'large') : '';
        if (!$url) {
            continue;
        }
        $cats = function_exists('wp_get_post_categories') ? wp_get_post_categories($pid) : [];
        $out[] = [
            'id' => (string) $pid,
            'sku' => '',
            'name' => get_the_title($pid),
            'url' => get_permalink($pid),
            'image' => (string) $url,
            'imageSet' => (string) wp_get_attachment_image_srcset($img, 'large'),
            'alt' => (string) get_post_meta($img, '_wp_attachment_image_alt', true),
            // statt Preis: kurzer Auszug
            'price' => wp_trim_words(wp_strip_all_tags(strip_shortcodes((string) get_the_excerpt($pid))), 24, '…'),
            'sales' => (int) get_post_time('U', true, $pid),
            'categories' => array_map('strval', (array) $cats),
            'inStock' => true,
        ];
    }

    return $out;
}

// Slides für einen Slider: Quelle wählen, dann durch den gemeinsamen Prime-Kern
function mgd_slider_prime_slides(int $id, array $cfg): array
{
    $ctn = mgd_slider_content_get($id);
    if ($ctn['content'] === 'wp') {
        return \MGD\Slider\Prime::select(mgd_slider_wp_candidates($cfg, $ctn), $cfg);
    }

    return \MGD\Slider\Prime::select(mgd_slider_prime_candidates($cfg), $cfg);
}
