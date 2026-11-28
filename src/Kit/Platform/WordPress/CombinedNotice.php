<?php

declare(strict_types=1);

namespace MGD\WooSlider\Kit\Platform\WordPress;

use MGD\WooSlider\Kit\Support\Text;

// EIN Hinweis "Installation melden" für alle MGD-Plugins statt einer Box je Plugin (Befund Michael: 7 gleiche Boxen)
// räumt auch die Einzel-Hinweise älterer Kit-Kopien (1.16/1.17) weg, X gilt für alle und wird je Benutzer gemerkt
final class CombinedNotice
{
    public const DONE = 'mgd_tools_notice_done';
    public const META = 'mgd_tools_notice_hidden';
    public const AJAX = 'mgd_tools_notice_hide';
    // X = nie wieder je Benutzer (Koordination 28.11.: auf Michaels Seite soll es nicht unruhiger werden)

    private static bool $hooked = false;

    public static function register(): void
    {
        if (self::$hooked || !function_exists('add_action')) {
            return;
        }
        self::$hooked = true;
        // vor den Einzel-Hinweisen (Priorität 10) > die werden hier entfernt
        \add_action('admin_notices', [self::class, 'render'], 1);
        \add_action('wp_ajax_' . self::AJAX, [self::class, 'hide']);
    }

    /** nur Tests */
    public static function reset(): void
    {
        self::$hooked = false;
        unset($GLOBALS[self::DONE]);
    }

    public static function render(): void
    {
        try {
            if (!empty($GLOBALS[self::DONE])) {
                return;
            }
            $GLOBALS[self::DONE] = true;
            // nur auf MGD-Tools-Seiten + Dashboard zeigen, sonst Einzel-Hinweise trotzdem wegräumen, Merker bleiben
            $here = self::isShownHere();
            $flags = [];
            foreach (InstallBoot::noticeClosures() as $c) {
                \remove_action('admin_notices', $c['fn'], $c['prio']);
                if (\get_transient($c['flag'])) {
                    $flags[] = $c['flag'];
                }
            }
            if ($flags === [] || !$here || !\current_user_can('manage_options')) {
                return;
            }
            foreach (array_unique($flags) as $f) {
                \delete_transient($f);
            }
            if ((string) \get_user_meta(\get_current_user_id(), self::META, true) !== '') {
                return;
            }
            echo self::html(self::names(), Tools::url(), (string) \wp_create_nonce(self::AJAX));
        } catch (\Throwable) {
        }
    }

    // Dashboard oder eine MGD-Seite (MGD Tools, Lizenz, Einstellungen, Plugin-Seiten unter MGD Tools)
    public static function isShownHere(): bool
    {
        $page = is_string($_GET['page'] ?? null) ? (string) $_GET['page'] : '';
        if ($page !== '') {
            return ColorPicker::isMgdPage($page);
        }
        global $pagenow;

        return ($pagenow ?? '') === 'index.php';
    }

    /** reines HTML, testbar. $names = Kurznamen der aktiven MGD-Plugins */
    public static function html(array $names, string $url, string $nonce): string
    {
        $list = $names === [] ? '' : ' (' . implode(', ', array_map(static fn ($n): string => Text::e((string) $n), $names)) . ')';

        return '<div class="notice notice-info is-dismissible" id="mgd-tools-notice"><p>'
            . sprintf(Text::e(ToolsTexts::NOTICE_ALL), $list)
            . ' <a href="' . Text::e($url) . '">' . Text::e(ToolsTexts::NOTICE_LINK) . '</a></p></div>'
            // X merkt sich das je Benutzer, für alle Plugins zusammen
            . '<script>document.addEventListener("click",function(e){if(!e.target.closest("#mgd-tools-notice .notice-dismiss"))return;var f=new FormData();f.append("action","' . self::AJAX . '");f.append("_wpnonce",' . json_encode($nonce) . ');fetch(window.ajaxurl||"admin-ajax.php",{method:"POST",body:f,credentials:"same-origin"});});</script>';
    }

    public static function hide(): void
    {
        \check_ajax_referer(self::AJAX);
        if (!\current_user_can('manage_options')) {
            \wp_send_json_error(null, 403);
        }
        \update_user_meta(\get_current_user_id(), self::META, (string) time());
        \wp_send_json_success();
    }

    /** @return list<string> */
    private static function names(): array
    {
        return array_values(array_map(static fn (array $e): string => Tools::shortName((string) $e['name']), Tools::entries()));
    }
}
