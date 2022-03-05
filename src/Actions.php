<?php

declare(strict_types=1);

namespace XdPro\ActionsPM;

use XdPro\ActionsPM\commands\Trigger;
use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use XdPro\ActionsPM\builtin\Say;
use XdPro\ActionsPM\builtin\Teleport;

class Actions extends PluginBase {

    /** @var array[] $namespaces */
    private static array $namespaces = [];

    private Config $builtins;

    public function onEnable(): void {
        $this->builtins = new Config($this->getDataFolder() . "builtins.yml", Config::YAML, [
            "hello_world" => [
                "type" => "message",
                "message" => "Hello World!"
            ],
            "two_hello_worlds" => [
                "type" => "multiple",
                "actions" => [
                    ["namespace" => "actionspm", "action" => "hello_world"],
                    ["namespace" => "actionspm", "action" => "hello_world"],
                ]
            ],
            "sample_tp" => [
                "type" => "teleport",
                "x" => 0,
                "y" => 64,
                "z" => 0,
                "world" => "world",
                "yaw" => 0,
                "pitch" => 0
            ]
        ]);
        //$b = new Builtins();
        //$b->register($this->builtins->getAll());
        $this->getCommand("trigger")->{"setExecutor"}(new Trigger());
        self::register("actionspm", "say", new Say());
        self::register("actionspm", "teleport", new Teleport());
    }

    /**
     * @deprecated Prefer getNamespaces and getNamespaceActions
     */
    public static function getRawNamespaces(): array {
        return self::$namespaces;
    }

    /**
     * @return string[]
     */
    public static function getNamespaces(): array {
        return array_keys(self::$namespaces);
    }

    /** @return Action[] */
    public static function getNamespaceActions(string $namespace): array {
        return self::$namespaces[$namespace];
    }

    public static function register(string $namespace, string $name, Action $action): void {

        if (!isset(self::$namespaces[$namespace])) self::$namespaces[$namespace] = [];
        self::$namespaces[$namespace][$name] = $action;
    }

    /** @return ?Action */
    public static function get(string $namespace, string $name) {
        return self::$namespaces[$namespace][$name] ?? null;
    }

    public static function selectAction(Player $player, Closure $onComplete, Closure $onClose = null): void {
        if ($onClose === null) {
            $onClose = function(Player $player): void {};
        }
        $namespaces = self::getNamespaces();
        $form = new MenuForm("Action picker", "Select a namespace", array_map(function(string $item) {
            return new MenuOption($item);
        }, $namespaces), function(Player $player, int $selection) use ($namespaces, $onComplete, $onClose): void {
            $actions = array_keys(self::getNamespaceActions($namespaces[$selection]));
            $namespace = $namespaces[$selection];
            $form = new MenuForm("Action picker", "Select an action", array_map(function(string $item) {
                return new MenuOption($item);
            }, $actions), function(Player $player, int $selection) use ($actions, $namespace, $onComplete, $onClose): void {
                $action = self::get($namespace, $actions[$selection]);
                $action->getParams($player, function(array $params) use ($onComplete, $action) {
                    $onComplete($action, $params);
                }, $onClose);
            }, $onClose);
            $player->sendForm($form);
        }, $onClose);
        $player->sendForm($form);
    }

}
