<?php
/**
 * Plugin Name:       MGD Slider (WordPress, WooCommerce, Divi)
 * Description:       Super-Slider für WordPress, WooCommerce und Divi: klassisch (Bild / Text + Bild) oder Prime (Slides automatisch aus Beiträgen, Seiten oder Shop-Artikeln). Meldet sich höchstens einmal am Tag bei theme.michael-gahn.de (Plugin, Version, Shopsystem, Domain, zufällige ID). Abschaltbar in den Einstellungen.
 * Version:           1.6.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            Michael Gahn DESIGN
 * Author URI:        https://michael-gahn.de
 * License:           Proprietary
 * Text Domain:       mgd-slider
 */

defined('ABSPATH') || exit;

define('MGD_SLIDER_VERSION', '1.6.0');
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
        $file = MGD_SLIDER_DIR . 'inc/Kit/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
});
// darf nie den Shop kaputt machen, Slider laeuft immer weiter
try {
    $mgdKitSetup = '\\MGD\\WooSlider\\Kit\\Platform\\WordPress\\Setup';
    // Update-Anzeige, sichere Installation, Hinweis + Start vom Install-Client
    $mgdUpdater = $mgdKitSetup::updater(__FILE__, MGD_SLIDER_PRODUCT, 'woocommerce', MGD_SLIDER_VERSION, 'MGD Slider',
        $mgdKitSetup::dashboardUrl(MGD_SLIDER_PRODUCT, 'edit.php?post_type=mgd_slider'));
    // Weitere Plugins, eigenes Plugin wird ausgeblendet
    $mgdList = $mgdKitSetup::recommendations(MGD_SLIDER_PRODUCT, 'woocommerce', 'mgd-woocommerce-slider-plugin', [], 'MGD-WooCommerce-Slider-Plugin');
    // Seite "Info und Updates" im eigenen Menue, Lizenz aus
    $mgdKitSetup::dashboard(MGD_SLIDER_PRODUCT, 'woocommerce', MGD_SLIDER_VERSION, 'MGD Slider', $mgdUpdater, $mgdList, false, 'edit.php?post_type=mgd_slider');
    // alter Updater-Muell, gibt es hier nix
    $mgdKitSetup::cleanupLegacy(MGD_SLIDER_PRODUCT, MGD_SLIDER_VERSION, []);
} catch (\Throwable $e) {
    // egal, Slider laeuft trotzdem
}

// Slider als eigener Beitragstyp, nur im Backend sichtbar
add_action('init', 'mgd_slider_register_post_type');
// Frontend Assets nur laden, wenn ein Slider auf der Seite ist (siehe render.php)
add_action('wp_enqueue_scripts', 'mgd_slider_register_assets');
