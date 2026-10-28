<?php
defined('ABSPATH') || exit;

// CSS + JS registrieren, geladen wird erst beim Rendern
function mgd_slider_register_assets(): void
{
    wp_register_style('mgd-slider', MGD_SLIDER_URL . 'assets/css/mgd-slider.css', [], MGD_SLIDER_VERSION);
    wp_register_script('mgd-slider', MGD_SLIDER_URL . 'assets/js/mgd-slider.js', [], MGD_SLIDER_VERSION, ['in_footer' => true, 'strategy' => 'defer']);
}

// HTML für eine Slider-Id (Shortcode, Block, Template-Tag)
function mgd_slider_render(int $id): string
{
    static $n = 0;
    $cfg = mgd_slider_get($id);
    if ($cfg === null) {
        return current_user_can('edit_posts') ? '<p class="mgd-slider-missing">Slider nicht gefunden.</p>' : '';
    }
    $slides = $cfg['mode'] === 'prime'
        ? \MGD\Slider\Prime::select(mgd_slider_prime_candidates($cfg), $cfg)
        : $cfg['slides'];
    if (!$slides) {
        return current_user_can('edit_posts') ? '<p class="mgd-slider-missing">Slider hat noch keine Slides.</p>' : '';
    }
    if (!wp_style_is('mgd-slider', 'registered')) {
        mgd_slider_register_assets(); // falls der Shortcode vor wp_enqueue_scripts läuft
    }
    wp_enqueue_style('mgd-slider');
    wp_enqueue_script('mgd-slider');
    $n++;

    return \MGD\Slider\Renderer::render($cfg, $slides, $id . '-' . $n);
}

// Shortcode [mgd_slider id="12"]
add_shortcode('mgd_slider', function ($atts): string {
    $a = shortcode_atts(['id' => 0], $atts, 'mgd_slider');

    return mgd_slider_render((int) $a['id']);
});

// Gutenebrg Block (ohne Build-Schritt, Server-Render)
add_action('init', function (): void {
    register_block_type(MGD_SLIDER_DIR . 'blocks/slider', [
        'render_callback' => fn ($attrs): string => mgd_slider_render((int) ($attrs['sliderId'] ?? 0)),
    ]);
});
