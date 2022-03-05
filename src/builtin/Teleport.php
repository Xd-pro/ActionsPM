<?php

namespace XdPro\ActionsPM\builtin;

use pocketmine\player\Player;
use Closure;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use Exception;
use pocketmine\entity\Location;
use pocketmine\Server;
use pocketmine\world\World;
use XdPro\ActionsPM\Action;

use function pocketmine\server;

class Teleport extends Action {

    public function getParams(Player $player, Closure $onComplete, Closure $onClose): void
    {
        $worlds = Server::getInstance()->getWorldManager()->getWorlds();
        $form = new CustomForm(
            "Teleport action", 
            [
                new Input("x", "X", "", "0"), 
                new Input("y", "Y", "", "0"), 
                new Input("z", "Z", "", "0"), 
                new Dropdown("world", "World", array_map(function(World $world) {
                    return $world->getDisplayName();
                }, $worlds), ),
                new Input("yaw", "Yaw", "", "0"),
                new Input("pitch", "Pitch", "", "0"),
            ],
            function(Player $player, CustomFormResponse $response) use ($onComplete, $onClose, $worlds): void {
                try {
                    $x = (float) $response->getString("x");
                    $y = (float) $response->getString("y");
                    $z = (float) $response->getString("z");
                    $yaw = (float) $response->getString("yaw");
                    $pitch = (float) $response->getString("pitch");
                    $worldNum = $response->getInt("world");
                } catch (Exception $e) {
                    $player->sendMessage("Please enter valid coordinates");
                    $onClose($player);
                }
                $world = $worlds[$worldNum + 1];
                $onComplete([new Location($x, $y, $z, $world, $yaw, $pitch)]);
            },
            $onClose
        );
        $player->sendForm($form);
    }

    public function execute(Player $player, array $params)
    {
        $player->teleport($params[0]);   
    }

}