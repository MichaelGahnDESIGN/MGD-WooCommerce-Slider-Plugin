<?php
// Rauchtest Divi Modul ohne Divi und ohne WordPress > php tests/divi-smoke.php (Stubs statt Divi)
declare(strict_types=1);

define('ABSPATH', __DIR__);
define('MGD_SLIDER_DIR', dirname(__DIR__) . '/');
$hooks = [];
function add_action(string $n, callable $f): void { global $hooks; $hooks[$n][] = $f; }
function current_user_can(string $c): bool { return true; }
function mgd_slider_list(): array { return [['id' => 7, 'title' => 'Start']]; }
function mgd_slider_render(int $id): string { return '<section data-id="' . $id . '"></section>'; }
class ET_Builder_Module { public array $props = []; public $name; public $plural; public $icon; public function __construct() { $this->init(); } public function init(): void {} }

$fails = 0;
$check = function (string $n, bool $ok) use (&$fails): void { echo ($ok ? 'OK    ' : 'FEHLER') . " $n\n"; $fails += $ok ? 0 : 1; };

require MGD_SLIDER_DIR . 'inc/divi.php';
$check('Hook et_builder_ready angemeldet', isset($hooks['et_builder_ready']));
foreach ($hooks['et_builder_ready'] as $f) { $f(); }
$check('Modul-Klasse da', class_exists('MGD_Slider_Divi_Module', false));
$m = new MGD_Slider_Divi_Module();
$f = $m->get_fields();
$check('Feld slider_id mit Auswahl', ($f['slider_id']['options'][7] ?? '') === 'Start' && $f['slider_id']['type'] === 'select');
$m->props = ['slider_id' => '7'];
$check('Render gibt Slider aus', str_contains($m->render([]), 'data-id="7"'));
$m->props = ['slider_id' => '0'];
$check('Ohne Auswahl: Hinweis', str_contains($m->render([]), 'Slider wählen'));
exit($fails === 0 ? 0 : 1);
