<?php

namespace XdPro\ActionsPM;

use Closure;

use pocketmine\player\Player;

abstract class Action {

    abstract public function execute(Player $player, array $params);

    public function getParams(Player $player, Closure $onComplete, Closure $onClose): void {
        $onComplete([]);
    }

}