<?php

declare(strict_types=1);

namespace MGD\WooSlider\Kit\Platform\WordPress;

// Knopf "Aktualisieren" in Weitere Plugins (Kit 1.18, A19): Angebot frisch holen, dann normaler WP-Updater
// (unser Download-Filter prüft Größe + SHA-256, Einstellungen + Aktiv-Zustand bleiben). Nur update_plugins, Nonce je Datei, nur MGD-Plugins
final class UpdateNow
{
    public const ACTION = 'mgd_tools_update';
    // war das Plugin vor dem Update aktiv? > danach wieder an (Befund A19c: WP schaltet beim Ersetzen aus, Reaktivierung nur per iframe)
    public const REACTIVATE = 'mgd_tools_reactivate';

    public static function nonceAction(string $file): string
    {
        return self::ACTION . '_' . $file;
    }

    public static function url(string $file, \Closure $nonce): string
    {
        return 'admin-post.php?' . http_build_query(['action' => self::ACTION, 'plugin' => $file, '_wpnonce' => (string) $nonce(self::nonceAction($file))]);
    }

    /** nach dem Update: vorher aktive MGD-Plugins wieder einschalten (still, ohne Weiterleitung). Rückgabe = eingeschaltet */
    public static function reactivate(mixed $upgrader = null, mixed $extra = []): array
    {
        $done = [];
        try {
            $list = (array) \get_site_transient(self::REACTIVATE);
            if ($list === [] || !is_array($extra) || ($extra['type'] ?? '') !== 'plugin' || ($extra['action'] ?? '') !== 'update') {
                return [];
            }
            $files = isset($extra['plugins']) && is_array($extra['plugins']) ? $extra['plugins'] : (isset($extra['plugin']) ? [$extra['plugin']] : []);
            foreach ($files as $f) {
                if (!is_string($f) || !isset($list[$f])) {
                    continue;
                }
                $network = $list[$f] === 'network';
                if (!\is_plugin_active($f)) {
                    $r = \activate_plugin($f, '', $network, true);
                    if ($r === null) {
                        $done[] = $f;
                    }
                }
                unset($list[$f]);
            }
            $list === [] ? \delete_site_transient(self::REACTIVATE) : \set_site_transient(self::REACTIVATE, $list, 900);
        } catch (\Throwable) {
        }

        return $done;
    }

    public static function handle(string $owner): void
    {
        $file = is_string($_GET['plugin'] ?? null) ? (string) $_GET['plugin'] : '';
        try {
            $ok = \current_user_can('update_plugins')
                && \wp_verify_nonce((string) ($_GET['_wpnonce'] ?? ''), self::nonceAction($file)) !== false
                && PluginToggle::allowed($file, PluginToggle::known($owner), CatalogState::wpPlugins());
            if (!$ok) {
                \wp_safe_redirect(\admin_url('admin.php?page=' . Tools::PAGE_CATALOG));
                exit;
            }
            // Aktiv-Zustand merken (je Datei, 15 Min), upgrader_process_complete stellt ihn wieder her
            if (function_exists('is_plugin_active') && \is_plugin_active($file)) {
                $list = (array) \get_site_transient(self::REACTIVATE);
                $list[$file] = (function_exists('is_plugin_active_for_network') && \is_plugin_active_for_network($file)) ? 'network' : 'site';
                \set_site_transient(self::REACTIVATE, $list, 900);
            }
            // Kit-Update-Caches + WP-Angebot verwerfen, neu fragen
            global $wpdb;
            if (isset($wpdb) && is_object($wpdb)) {
                foreach ((array) $wpdb->get_col("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '\\_site\\_transient\\_mgd\\_kit\\_upd\\_%'") as $o) {
                    \delete_site_transient(substr((string) $o, strlen('_site_transient_')));
                }
            }
            // gezielt je Produkt (auch Multisite/Objekt-Cache, Befund Prüfung)
            foreach (Tools::entries() as $e) {
                \delete_site_transient('mgd_kit_upd_' . str_replace('-', '_', (string) $e['product']));
            }
            \delete_site_transient('update_plugins');
            if (function_exists('wp_update_plugins')) {
                \wp_update_plugins();
            }
            \wp_safe_redirect(\admin_url('update.php?' . http_build_query(['action' => 'upgrade-plugin', 'plugin' => $file, '_wpnonce' => \wp_create_nonce('upgrade-plugin_' . $file)])));
            exit;
        } catch (\Throwable) {
            \wp_safe_redirect(\admin_url('admin.php?page=' . Tools::PAGE_CATALOG));
            exit;
        }
    }
}
