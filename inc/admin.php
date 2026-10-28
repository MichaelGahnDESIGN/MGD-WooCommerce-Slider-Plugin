<?php
defined('ABSPATH') || exit;

// Menü: Slider, Neu, Lizenzen
add_action('admin_menu', function (): void {
    add_menu_page('MGD Slider', 'MGD Slider', 'edit_posts', 'edit.php?post_type=mgd_slider', '', 'dashicons-images-alt2', 58);
    add_submenu_page('edit.php?post_type=mgd_slider', 'Alle Slider', 'Alle Slider', 'edit_posts', 'edit.php?post_type=mgd_slider');
    add_submenu_page('edit.php?post_type=mgd_slider', 'Neuer Slider', 'Neu anlegen', 'edit_posts', 'post-new.php?post_type=mgd_slider');
    add_submenu_page('edit.php?post_type=mgd_slider', 'Lizenzen', 'Lizenzen', 'manage_options', 'mgd-slider-license', 'mgd_slider_license_page');
});

// Lizenzen: Platzhalter (Free). Link zur Seite für Pro-Lizenzen
if (!function_exists('mgd_slider_license_page')) {
function mgd_slider_license_page(): void
{
    echo '<div class="wrap"><h1>Lizenzen</h1>';
    echo '<p>Diese Version ist die kostenlose Free-Version (bis zu ' . (int) \MGD\Slider\Limits::FREE_SLIDERS . ' Slider).</p>';
    echo '<p><label>Lizenzcode <input type="text" class="regular-text" disabled placeholder="folgt mit einer späteren Version"></label></p>';
    echo '<p><a href="https://themes.michael-gahn.de" target="_blank" rel="noopener">Mehr zu den Pro-Lizenzen auf themes.michael-gahn.de</a></p></div>';
}
}

// Slider-Liste für den Block (Titel + Id)
function mgd_slider_list(): array
{
    $out = [];
    foreach (get_posts(['post_type' => 'mgd_slider', 'post_status' => ['publish', 'draft'], 'numberposts' => 100]) as $p) {
        $out[] = ['id' => $p->ID, 'title' => $p->post_title ?: ('Slider ' . $p->ID)];
    }

    return $out;
}
add_action('enqueue_block_editor_assets', function (): void {
    wp_add_inline_script('mgd-slider-editor-script', 'window.MGDSliderList=' . wp_json_encode(mgd_slider_list()) . ';', 'before');
});

// Einstellungsfeld im Slider-Beitrag
add_action('add_meta_boxes_mgd_slider', function (): void {
    add_meta_box('mgd_slider_cfg', 'Einstellungen', 'mgd_slider_metabox', 'mgd_slider', 'normal', 'high');
    add_meta_box('mgd_slider_use', 'Einbinden', function ($post): void {
        echo '<p>Shortcode:</p><p><code>[mgd_slider id="' . (int) $post->ID . '"]</code></p><p>Oder den Block „MGD Slider“ im Editor wählen.</p>';
    }, 'mgd_slider', 'side');
});

function mgd_slider_field_select(string $name, array $choices, string $cur): string
{
    $h = '<select name="mgd_cfg[' . esc_attr($name) . ']">';
    foreach ($choices as $v => $l) {
        $h .= '<option value="' . esc_attr((string) $v) . '"' . selected($cur, (string) $v, false) . '>' . esc_html($l) . '</option>';
    }

    return $h . '</select>';
}

function mgd_slider_metabox(WP_Post $post): void
{
    wp_nonce_field('mgd_slider_save', 'mgd_slider_nonce');
    $raw = json_decode((string) get_post_meta($post->ID, '_mgd_slider_config', true), true);
    $c = \MGD\Slider\Config::normalize(is_array($raw) ? $raw : []);
    $opt = \MGD\Slider\Config::options()['choices'];
    $chk = fn (string $k) => '<label><input type="checkbox" name="mgd_cfg[' . $k . ']" value="1"' . checked($c[$k], true, false) . '> ';
    echo '<table class="form-table"><tr><th>Art</th><td>' . mgd_slider_field_select('mode', ['classic' => 'Klassisch (eigene Slides)', 'prime' => 'Prime (automatisch aus Artikeln)'], $c['mode']) . '</td></tr>';
    echo '<tr><th>Beschriftung (für Screenreader)</th><td><input type="text" class="regular-text" name="mgd_cfg[label]" value="' . esc_attr($c['label']) . '"></td></tr>';
    echo '<tr><th>Wechsel</th><td>' . $chk('autoplay') . 'Automatisch wechseln</label> &nbsp; Tempo (ms) <input type="number" name="mgd_cfg[interval]" min="2000" max="20000" step="500" value="' . (int) $c['interval'] . '" class="small-text">'
        . '<br>' . $chk('pauseOnHover') . 'Pause bei Mauszeiger</label></td></tr>';
    echo '<tr><th>Bedienung</th><td>' . $chk('arrows') . 'Pfeile</label> ' . $chk('dots') . 'Punkte</label> ' . $chk('swipe') . 'Wischen</label> ' . $chk('loop') . 'Endlos</label></td></tr>';
    echo '<tr><th>Aussehen</th><td>Seitenverhältnis ' . mgd_slider_field_select('aspect', array_combine($opt['aspect'], $opt['aspect']), $c['aspect'])
        . ' Zuschnitt ' . mgd_slider_field_select('fit', ['cover' => 'Füllen (zuschneiden)', 'contain' => 'Ganz zeigen'], $c['fit'])
        . ' Text ' . mgd_slider_field_select('align', ['left' => 'links', 'center' => 'mittig', 'right' => 'rechts'], $c['align'])
        . '<br>Akzentfarbe <input type="text" name="mgd_cfg[accent]" value="' . esc_attr($c['accent']) . '" placeholder="#c8161a" class="small-text"> <span class="description">(leer = Farbe des Themes; Colorpicker kommt aus der Theme-Komponente, folgt)</span></td></tr></table>';

    echo '<h3>Prime</h3><p class="description">Nur bei Art „Prime“. Die Slides entstehen aus deinen Artikeln, du legst nur die Anzahl fest.</p><table class="form-table">';
    echo '<tr><th>Quelle</th><td>' . str_replace('mgd_cfg[', 'mgd_cfg[prime_', mgd_slider_field_select('source', ['random' => 'Zufällig', 'bestseller' => 'Meistverkauft', 'category' => 'Kategorie', 'manual' => 'Manuell'], $c['prime']['source']))
        . ' Anzahl <input type="number" min="1" max="12" name="mgd_cfg[prime_count]" value="' . (int) $c['prime']['count'] . '" class="small-text"></td></tr>';
    echo '<tr><th>Kategorie-IDs</th><td><input type="text" class="regular-text" name="mgd_cfg[prime_categories]" value="' . esc_attr(implode(', ', $c['prime']['categories'])) . '"><p class="description">IDs, mit Komma getrennt (Auswahlliste folgt).</p></td></tr>';
    echo '<tr><th>Artikel (manuell)</th><td><input type="search" id="mgd-product-search" class="regular-text" placeholder="Name oder Artikelnummer suchen"><div id="mgd-product-hits"></div>'
        . '<input type="text" class="large-text" id="mgd-products" name="mgd_cfg[prime_products]" value="' . esc_attr(implode(', ', $c['prime']['products'])) . '"><p class="description">Artikel-IDs oder Artikelnummern. Aus dieser Auswahl wird zufällig gezeigt.</p></td></tr></table>';

    echo '<h3>Slides (klassisch)</h3><p class="description">Bild, optional Titel, Text und Button. Sortieren per Drag &amp; Drop folgt.</p><div id="mgd-slides">';
    foreach ($c['slides'] as $s) {
        mgd_slider_slide_row($s);
    }
    echo '</div><p><button type="button" class="button" id="mgd-add-slide">Slide hinzufügen</button></p>';
    echo '<script type="text/template" id="mgd-slide-tpl">';
    mgd_slider_slide_row(\MGD\Slider\Config::slide(['title' => ' ']) ?? []);
    echo '</script>';
}

function mgd_slider_slide_row(array $s): void
{
    $f = fn (string $k, string $label, string $ph = '') => '<label>' . esc_html($label) . ' <input type="text" name="mgd_slides[' . esc_attr($k) . '][]" value="' . esc_attr((string) ($s[$k] ?? '')) . '" placeholder="' . esc_attr($ph) . '" class="regular-text"></label> ';
    echo '<fieldset class="mgd-slide-row" style="border:1px solid #c3c4c7;padding:8px;margin:0 0 8px">'
        . $f('image', 'Bild-URL', 'Mediathek wählen') . '<button type="button" class="button mgd-pick">Mediathek</button> '
        . $f('alt', 'Alt-Text') . '<br>' . $f('title', 'Titel') . $f('text', 'Text') . '<br>'
        . $f('buttonLabel', 'Button') . $f('buttonUrl', 'Button-Link') . '<button type="button" class="button-link-delete mgd-del">Entfernen</button></fieldset>';
}

// Speichern: Nonce + Recht, alles läuft durch Config::normalize
add_action('save_post_mgd_slider', function (int $id): void {
    if (!isset($_POST['mgd_slider_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mgd_slider_nonce'])), 'mgd_slider_save')
        || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $id)) {
        return;
    }
    $in = wp_unslash($_POST['mgd_cfg'] ?? []);
    $in = is_array($in) ? $in : [];
    foreach (['autoplay', 'pauseOnHover', 'arrows', 'dots', 'swipe', 'loop'] as $b) {
        $in[$b] = isset($in[$b]) ? '1' : '0';
    }
    $in['prime'] = ['source' => $in['prime_source'] ?? '', 'count' => $in['prime_count'] ?? '', 'categories' => $in['prime_categories'] ?? '', 'products' => $in['prime_products'] ?? ''];
    $rows = wp_unslash($_POST['mgd_slides'] ?? []);
    $slides = [];
    foreach ((array) ($rows['image'] ?? []) as $i => $_) {
        $slides[] = array_map(fn ($col) => is_array($col) ? ($col[$i] ?? '') : '', $rows);
    }
    $in['slides'] = $slides;
    update_post_meta($id, '_mgd_slider_config', wp_json_encode(\MGD\Slider\Config::normalize($in)));
});

// Admin Assets nur auf dem Slider-Beitrag
add_action('admin_enqueue_scripts', function (string $hook): void {
    $s = get_current_screen();
    if (!$s || $s->post_type !== 'mgd_slider' || !in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script('mgd-slider-admin', MGD_SLIDER_URL . 'assets/js/admin.js', ['jquery'], MGD_SLIDER_VERSION, true);
    wp_localize_script('mgd-slider-admin', 'MGDSliderAdmin', ['ajax' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('mgd_slider_admin')]);
});
