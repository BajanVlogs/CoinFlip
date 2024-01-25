<?php

namespace BajanVlogs;

use onebone\economyapi\EconomyAPI;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CoinFlipCommand extends VanillaCommand {
    public function __construct($name){
        parent::__construct(
            $name,
            "CoinFlip",
            "/cf <player> <betMoney> | /cf <accept/deny>"
        );
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!($sender instanceof Player)){
            $sender->sendMessage("You must run this command in-game.");
            return false;
        }

        if(count($args) > 0){
            $subCommand = strtolower(array_shift($args));

            switch($subCommand){
                case "accept":
                case "a":
                    // Existing code...
                    break;
                case "deny":
                case "d":
                    // Existing code...
                    break;
                default:
                    if(count($args) > 0){
                        $betAmount = abs(array_shift($args));
                        $targetPlayerName = array_shift($args);

                        $targetPlayer = $sender->getServer()->getPlayer($targetPlayerName);

                        if($targetPlayer === null){
                            $sender->sendMessage("Player is not online.");
                            break;
                        }

                        // Existing code...
                    } else {
                        $sender->sendMessage("USAGE: /cf <player> <betMoney> | /cf <accept/deny>");
                    }
                    break;
            }
        } else {
            $sender->sendMessage("USAGE: /cf <player> <betMoney> | /cf <accept/deny>");
        }

        return true;
    }
}
