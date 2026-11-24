<?php
defined('ABSPATH') || exit;

// Divi Modul "MGD Slider" > nutzt dieselben Slider wie Shortcode und Block
// Divi 5 spielt Module im alten Divi-Stil (ET_Builder_Module) weiter ab, Anmeldung erst wenn Divi bereit ist
add_action('et_builder_ready', function (): void {
    if (!class_exists('ET_Builder_Module') || class_exists('MGD_Slider_Divi_Module', false)) {
        return;
    }
    require_once MGD_SLIDER_DIR . 'inc/divi-module.php';
    new MGD_Slider_Divi_Module();
});
