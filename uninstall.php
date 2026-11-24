<?php
// Beim Löschen des Plugins: Slider bleiben absichtlich erhalten (nichts löschen ohne Rückfrage)
defined('WP_UNINSTALL_PLUGIN') || exit;

// Abmelden bei theme.michael-gahn.de (immer), Baukasten-Daten raeumt der Baukasten selbst weg
spl_autoload_register(static function (string $class): void {
    $prefix = 'MGD\\WooSlider\\Kit\\';
    if (str_starts_with($class, $prefix)) {
        $file = __DIR__ . '/src/Kit/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
});
try {
    \MGD\WooSlider\Kit\Platform\WordPress\Setup::uninstall('mgd-slider-woocommerce', 'woocommerce');
} catch (\Throwable $e) {
    // Löschen soll immer klappen
}
