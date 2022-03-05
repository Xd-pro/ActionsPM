<?php

namespace XdPro\ActionsPM\commands;

use XdPro\ActionsPM\Actions;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class Trigger implements CommandExecutor {

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        $player = null;
        if (!isset($args[1])) {
            if ($sender instanceof Player) {
                $sender->sendMessage("Usage: /trigger <namespace> <action> [player]");
                return false;
            } else {
                $sender->sendMessage("Usage: /trigger <namespace> <action> <player>");
                return false;
            }
        }
        if (isset($args[2])) {
            $player = Server::getInstance()->getPlayerByPrefix($args[2]);
        } else {
            if ($sender instanceof Player) {
                $player = $sender;
            } else {
                $sender->sendMessage("Usage: /trigger <namespace> <action> <player>");
                return false;
            }
        }
        if ($player === null) {
            $sender->sendMessage("Player not found");
            return false;
        }

        if (Actions::get($args[0], $args[1]) !== null) {
            Actions::get($args[0], $args[1])($player);
        }
        return true;
    }

}