<?php

namespace XdPro\ActionsPM\commands;

use XdPro\ActionsPM\Actions;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use XdPro\ActionsPM\Action;

class Trigger implements CommandExecutor {

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($sender instanceof Player) {
            $player = $sender;
            if (isset($args[0])) {
                $player = Server::getInstance()->getPlayerByPrefix($args[0]);
            }
            Actions::selectAction($sender, function(Action $action, array $params) use ($player) {
                $action->execute($player, $params);
            });
        } else {
            $sender->sendMessage("This command is only usable in-game");;
        }
        $player = null;
        return true;
    }

}