<?php
/**
 * Plugin Name:       MGD Slider
 * Plugin URI:        https://theme.michael-gahn.de/plugins/mgd-woocommerce-slider-plugin
 * Description:       Slider für WordPress, WooCommerce und Divi, mit Slides von Hand oder automatisch aus Beiträgen, Seiten und Shop-Artikeln.
 * Version:           2.2.1
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Michael Gahn DESIGN
 * Author URI:        https://michael-gahn.de
 * License:           Proprietary
 * Text Domain:       mgd-slider
 */

defined('ABSPATH') || exit;

use MGD\WooSlider\Kit\Platform\WordPress\{Setup, Tools};

define('MGD_SLIDER_VERSION', '2.2.1');
define('MGD_SLIDER_FILE', __FILE__);
define('MGD_SLIDER_DIR', plugin_dir_path(__FILE__));
define('MGD_SLIDER_URL', plugin_dir_url(__FILE__));

// Kern aus core/ (gleiche Klassen auf allen Plattformen)
require_once MGD_SLIDER_DIR . 'inc/core/Config.php';
require_once MGD_SLIDER_DIR . 'inc/core/Prime.php';
require_once MGD_SLIDER_DIR . 'inc/core/Renderer.php';
require_once MGD_SLIDER_DIR . 'inc/core/Limits.php';

require_once MGD_SLIDER_DIR . 'inc/storage.php';
require_once MGD_SLIDER_DIR . 'inc/prime-woo.php';
require_once MGD_SLIDER_DIR . 'inc/wp-content.php';
require_once MGD_SLIDER_DIR . 'inc/divi.php';
require_once MGD_SLIDER_DIR . 'inc/render.php';
require_once MGD_SLIDER_DIR . 'inc/admin.php';

// Baukasten (Updates, Weitere Plugins, Installation melden) > inc/Kit, kommt vom Generator
define('MGD_SLIDER_PRODUCT', 'mgd-slider-woocommerce');
spl_autoload_register(static function (string $class): void {
    $prefix = 'MGD\\WooSlider\\Kit\\';
    if (str_starts_with($class, $prefix)) {
        $file = MGD_SLIDER_DIR . 'src/Kit/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
});
// darf nie den Shop kaputt machen, Slider laeuft immer weiter
try {
    // Update-Anzeige, sichere Installation, Hinweis + Start vom Install-Client
    $mgdUpdater = Setup::updater(__FILE__, 'mgd-slider-woocommerce', 'woocommerce', MGD_SLIDER_VERSION, 'MGD Slider', Tools::url());
    // alles unter Menue MGD Tools, eigene Seite = Slider-Liste
    Setup::tools(__FILE__, 'mgd-slider-woocommerce', 'woocommerce', MGD_SLIDER_VERSION, 'MGD Slider', $mgdUpdater, [
        'slug' => 'mgd-woocommerce-slider-plugin',
        'repo' => 'MGD-WooCommerce-Slider-Plugin',
        'settings' => ['page' => 'mgd-slider', 'title' => 'Slider', 'cap' => 'edit_posts', 'render' => 'mgd_slider_tools_page'],
        'license' => false,
    ]);
} catch (\Throwable $e) {
    // egal, Slider laeuft trotzdem
}

// Slider als eigener Beitragstyp, nur im Backend sichtbar
add_action('init', 'mgd_slider_register_post_type');
// Frontend Assets nur laden, wenn ein Slider auf der Seite ist (siehe render.php)
add_action('wp_enqueue_scripts', 'mgd_slider_register_assets');
