<?php
/**
 * Plugin Name:       MGD WooCommerce Slider Plugin
 * Description:       Super-Slider für WooCommerce: klassisch (Bild / Text + Bild) oder Prime (Slides automatisch aus Shop-Artikeln). Meldet sich höchstens einmal am Tag bei theme.michael-gahn.de (Plugin, Version, Shopsystem, Domain, zufällige ID). Abschaltbar in den Einstellungen.
 * Version:           1.5.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            Michael Gahn DESIGN
 * Author URI:        https://michael-gahn.de
 * License:           Proprietary
 * Text Domain:       mgd-slider
 */

defined('ABSPATH') || exit;

define('MGD_SLIDER_VERSION', '1.5.0');
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
require_once MGD_SLIDER_DIR . 'inc/render.php';
require_once MGD_SLIDER_DIR . 'inc/admin.php';

// Install-Client (Free-Lizenz) Autoload > inc/Install, kommt vom Generator
define('MGD_SLIDER_INSTALL_PRODUCT', 'mgd-slider-woocommerce');
spl_autoload_register(static function (string $class): void {
    $prefix = 'MGD\\WooSlider\\Install\\';
    if (str_starts_with($class, $prefix)) {
        $file = MGD_SLIDER_DIR . 'inc/Install/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
});
// max 1x am Tag im Admin melden + Hinweis nach Aktivieren
try {
    \MGD\WooSlider\Install\Platform\WordPress::boot(__FILE__, MGD_SLIDER_INSTALL_PRODUCT, 'woocommerce', MGD_SLIDER_VERSION, admin_url('admin.php?page=mgd-slider-license'));
} catch (\Throwable $e) {
    // egal, Slider läuft trotzdem
}

// Slider als eigener Beitragstyp, nur im Backend sichtbar
add_action('init', 'mgd_slider_register_post_type');
// Frontend Assets nur laden, wenn ein Slider auf der Seite ist (siehe render.php)
add_action('wp_enqueue_scripts', 'mgd_slider_register_assets');
