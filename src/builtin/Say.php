<?php

namespace XdPro\ActionsPM\builtin;

use pocketmine\player\Player;
use Closure;
use XdPro\ActionsPM\Action;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;

class Say extends Action {
    public function getParams(Player $player, Closure $onComplete, Closure $onClose): void
    {
        $form = new CustomForm("Say action", [new Input("message", "Enter a message")], function(Player $player, CustomFormResponse $response) use ($onComplete): void {
            $onComplete([$response->getString("message")]);
        }, function(Player $player): void {});
        $player->sendForm($form);
    }

    public function execute(Player $player, array $params)
    {
        $player->chat($params[0]);
    }
}