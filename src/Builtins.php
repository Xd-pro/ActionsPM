<?php

namespace ActionsPM\ActionsPM;

use Exception;
use pocketmine\player\Player;
use pocketmine\Server;
use Closure;
use pocketmine\entity\Location;
use pocketmine\world\Position;
use pocketmine\world\WorldManager;

class Builtins {

    public function register(array $config) {

        $generators = [
            "message" => function(array $value) {
                return function(Player $player) use ($value) {
                    $player->sendMessage($value["message"] ?? "undefined message");
                };
            },
            "teleport" => function(array $value) {
                return function(Player $player) use ($value) {
                    $pos = new Location(
                        $value["x"] ?? 0, 
                        $value["y"] ?? 64, 
                        $value["z"] ?? 0, 
                        Server::getInstance()->getWorldManager()->getWorldByName(
                            $value["world"]
                        ) ?? Server::getInstance()->getWorldManager()->getDefaultWorld(),
                        $value["yaw"] ?? 0, 
                        $value["pitch"] ?? 0
                        );
                    $player->teleport($pos);
                };
            },
            "multiple" => function(array $value) {
                return function(Player $player) use ($value) {
                    $actions = $value["actions"] ?? [];
                    foreach ($actions as $action) {
                        if (Actions::get($action["namespace"] ?? "undefined namespace", $action["action"] ?? "undefined action") !== null) {
                            Actions::get($action["namespace"], $action["action"])($player);
                        } else {
                            $player->sendMessage("Error in action: " . $action["namespace"] . ":" . $action["action"] ."  is not a defined action");
                        }
                    }
                };
            },
            "null" => function(Player $player) {
    
            }
        ];

        foreach ($config as $name => $value) {
            if (!(isset($value["type"]) || isset($generators[$value["type"]]) )) {
                $value["type"] = "null";
            }
            /** @var Closure $fn */
            $fn = $generators[$value["type"]]($value);
            Actions::register("actionspm", $name, $fn);
        }
    }

}