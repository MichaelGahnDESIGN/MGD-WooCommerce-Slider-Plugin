<?php
defined('ABSPATH') || exit;

// Wird nur geladen wenn Divi da ist (inc/divi.php)
class MGD_Slider_Divi_Module extends ET_Builder_Module
{
    public $slug = 'mgd_slider_divi';
    public $vb_support = 'on';

    public function init(): void
    {
        $this->name = 'MGD Slider';
        $this->plural = 'MGD Slider';
        $this->icon = 'T';
    }

    // Auswahlliste: alle Slider (Id => Titel)
    public function get_fields(): array
    {
        $options = ['0' => '- Slider wählen -'];
        foreach (mgd_slider_list() as $s) {
            $options[(string) $s['id']] = $s['title'];
        }

        return [
            'slider_id' => [
                'label' => 'Slider',
                'type' => 'select',
                'options' => $options,
                'default' => '0',
                'option_category' => 'basic_option',
                'description' => 'Die Slider legst du im Menü „MGD Slider“ an.',
                'toggle_slug' => 'main_content',
            ],
        ];
    }

    // HTML kommt vom Plugin, sonst nix (Divi Rahmen eigener)
    public function render($attrs, $content = null, $render_slug = ''): string
    {
        $id = (int) ($this->props['slider_id'] ?? 0);
        if ($id <= 0) {
            return current_user_can('edit_posts') ? '<p class="mgd-slider-missing">Bitte im Modul einen Slider wählen.</p>' : '';
        }

        return mgd_slider_render($id);
    }
}
