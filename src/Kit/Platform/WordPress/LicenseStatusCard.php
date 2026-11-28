<?php

declare(strict_types=1);

namespace MGD\WooSlider\Kit\Platform\WordPress;

use MGD\WooSlider\Kit\License\LicenseClient;
use MGD\WooSlider\Kit\Support\Text;

// Lizenz-Status-Karte in MGD Tools > Lizenz (Kit 1.18): welche Lizenz läuft, bis wann, was je Plugin frei ist
// nur aus vorhandenen Daten (keine Konto-Knöpfe, die kommen mit 1.19). Schlüssel-Kopieren holt per Ajax, nie im HTML
final class LicenseStatusCard
{
    public const AJAX = 'mgd_tools_license_key';
    // Paket-Namen wie auf theme.michael-gahn.de (Plugin-Pro-Pakete)
    public const PACKAGES = ['mgd-pro-all' => 'Alle Pro-Funktionen'];

    /**
     * reines HTML, testbar
     * @param array{valid:bool,tier:string,reason:string,source:string,entitlements:list<string>} $state
     * @param list<array{name:string,pro:array<string,string>}> $plugins aktive MGD-Plugins mit Pro-Berechtigungen
     */
    public static function html(array $state, string $sku, int $end, string $domain, int $checked, array $plugins, bool $hasToken, string $nonce): string
    {
        $allPro = [];
        foreach ($plugins as $p) {
            $allPro += $p['pro'];
        }
        $b = LicenseBadge::of(array_keys($allPro), $state, $end);
        $art = $state['valid'] && $state['entitlements'] !== [] ? ToolsTexts::LIC_PRO : ToolsTexts::LIC_FREE;
        $pkg = $sku === '' ? '–' : (self::PACKAGES[$sku] ?? ($allPro[$sku] ?? $sku));
        $row = static fn (string $k, string $v): string => '<tr><th scope="row">' . Text::e($k) . '</th><td>' . $v . '</td></tr>';
        $rows = $row(ToolsTexts::LIC_ART, Text::e($art))
            . $row(ToolsTexts::LIC_PACKAGE, Text::e($pkg))
            . $row(ToolsTexts::LIC_STATUS, '<span class="mgd-tools-badge mgd-tools-badge--' . Text::e($b['tone']) . '">' . Text::e($hasToken ? $b['status'] : ToolsTexts::LIC_NONE) . '</span>')
            . $row(ToolsTexts::LIC_UNTIL, Text::e($end > 0 && $state['valid'] ? self::date($end) : '–'))
            . $row(ToolsTexts::LIC_DOMAIN, Text::e($domain !== '' ? $domain : '–'))
            . $row(ToolsTexts::LIC_LAST_CHECK, Text::e($checked > 0 ? self::date($checked, true) : '–'));
        $per = '';
        foreach ($plugins as $p) {
            $pb = LicenseBadge::of(array_keys($p['pro']), $state, $end);
            $what = $p['pro'] === [] ? ToolsTexts::LIC_NO_PRO : implode(', ', array_values($p['pro']));
            $per .= '<tr><th scope="row">' . Text::e(Tools::shortName($p['name'])) . '</th><td>' . Text::e($what) . '</td><td>' . LicenseBadge::html($pb) . '</td></tr>';
        }
        $copy = $hasToken ? '<p class="mgd-tools-keycopy"><button type="button" class="button" data-mgd-keycopy data-nonce="' . Text::e($nonce) . '">' . Text::e(ToolsTexts::KEY_COPY) . '</button>'
            . ' <span class="mgd-tools-note" role="status" aria-live="polite" data-mgd-keycopy-msg></span></p>' : '';

        return '<section class="mgd-kit-section" aria-labelledby="mgd-tools-h-licstate"><h2 id="mgd-tools-h-licstate">' . Text::e(ToolsTexts::LIC_CARD) . '</h2>'
            . '<table class="mgd-tools-table mgd-tools-licstate"><tbody>' . $rows . '</tbody></table>' . $copy
            . ($per !== '' ? '<h3>' . Text::e(ToolsTexts::LIC_PER_PLUGIN) . '</h3><table class="mgd-tools-table"><tbody>' . $per . '</tbody></table>' : '')
            . '</section>' . self::script();
    }

    public static function forClient(LicenseClient $l, string $nonce): string
    {
        $plugins = [];
        foreach (Tools::entries() as $e) {
            $plugins[] = ['name' => (string) $e['name'], 'pro' => (array) $e['pro']];
        }

        return self::html($l->state(), $l->packageSku(), $l->periodEnd(), $l->domain(), $l->checkedAt(), $plugins, $l->hasToken(), $nonce);
    }

    // Ajax: voller Schlüssel nur für Admins mit Nonce, Antwort nicht cachen
    public static function ajax(LicenseClient $l): void
    {
        \check_ajax_referer(self::AJAX);
        if (!\current_user_can('manage_options')) {
            \wp_send_json_error(null, 403);
        }
        \nocache_headers();
        $t = $l->tokenForCopy();
        $t === '' ? \wp_send_json_error(null, 404) : \wp_send_json_success(['key' => $t]);
    }

    // Kopieren im Klick: ClipboardItem mit Promise (Safari), sonst Feld markiert + Hinweis. Schlüssel nicht im DOM lassen
    private static function script(): string
    {
        return '<script>(function(){var b=document.querySelector("[data-mgd-keycopy]");if(!b||b.dataset.ready)return;b.dataset.ready="1";var m=document.querySelector("[data-mgd-keycopy-msg]");'
            . 'var get=function(){var f=new FormData();f.append("action","' . self::AJAX . '");f.append("_wpnonce",b.dataset.nonce);return fetch(window.ajaxurl||"admin-ajax.php",{method:"POST",body:f,credentials:"same-origin"}).then(function(r){return r.json()}).then(function(j){if(!j||!j.success)throw 0;return j.data.key})};'
            . 'var ok=function(){m.textContent=' . json_encode(ToolsTexts::KEY_COPIED) . '};'
            . 'var manual=function(k){var i=document.createElement("input");i.type="text";i.readOnly=true;i.className="regular-text";i.value=k;b.parentNode.appendChild(i);i.focus();i.select();m.textContent=' . json_encode(ToolsTexts::KEY_COPY_FAIL) . ';setTimeout(function(){i.value="";i.remove()},30000)};'
            . 'b.addEventListener("click",function(){m.textContent="";var p=get();'
            . 'if(window.isSecureContext&&window.ClipboardItem&&navigator.clipboard&&navigator.clipboard.write){navigator.clipboard.write([new ClipboardItem({"text/plain":p.then(function(k){return new Blob([k],{type:"text/plain"})})})]).then(ok,function(){p.then(manual,function(){m.textContent=' . json_encode(ToolsTexts::RETRY) . '})});return;}'
            . 'p.then(function(k){var t=document.createElement("textarea");t.value=k;t.style.position="fixed";t.style.opacity="0";document.body.appendChild(t);t.select();var d=false;try{d=document.execCommand("copy")}catch(e){}t.value="";t.remove();d?ok():manual(k)},function(){m.textContent=' . json_encode(ToolsTexts::RETRY) . '})});})();</script>';
    }

    private static function date(int $ts, bool $time = false): string
    {
        $f = $time ? 'd.m.Y H:i' : 'd.m.Y';

        return function_exists('wp_date') ? (string) \wp_date($f, $ts) : gmdate($f, $ts);
    }
}
