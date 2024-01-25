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
            "/cf <player> <betMoney> | /cf <accept/deny>");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!($sender instanceof Player)){
            $sender->sendMessage("You must run this command in-game.");
            return false;
        }

        $args[0] = strtolower($args[0]);
        if(isset($args[0])){
            switch($args[0]){
                case "accept":
                case "a":
                    $this->handleAccept($sender);
                    break;
                case "deny":
                case "d":
                    $this->handleDeny($sender);
                    break;
                default:
                    $this->handleCoinFlip($sender, $args);
                    break;
            }
        } else {
            $sender->sendMessage("USAGE: /cf <player> <betMoney> | /cf <accept/deny>");
        }

        return true;
    }

    private function handleAccept(Player $sender){
        if(isset(CoinFlip::$queue[strtolower($sender->getName())])){
            $this->resolveCoinFlip($sender, true);
        } else {
            $sender->sendMessage(TextFormat::RED . "There are no CoinFlip requests enqueued for you right now.");
        }
    }

    private function handleDeny(Player $sender){
        if(isset(CoinFlip::$queue[strtolower($sender->getName())])){
            $this->resolveCoinFlip($sender, false);
        } else {
            $sender->sendMessage(TextFormat::RED . "There are no CoinFlip requests enqueued for you right now.");
        }
    }

    private function handleCoinFlip(Player $sender, array $args){
        if(isset($args[1])){
            $args[1] = abs($args[1]);
            $player = $sender->getServer()->getPlayer($args[0]);
            if($player === null){
                $sender->sendMessage("Player is not online.");
                return;
            }

            if(EconomyAPI::getInstance()->myMoney($player->getName()) < $args[1]){
                $sender->sendMessage(TextFormat::RED . $player->getName() . " does not have enough money.");
                return;
            }

            if(EconomyAPI::getInstance()->myMoney($sender->getName()) < $args[1]){
                $sender->sendMessage(TextFormat::RED . "Not enough money.");
                return;
            }

            CoinFlip::$queue[strtolower($player->getName())] = [strtolower($sender->getName()), $args[1]];
            CoinFlip::$inp[strtolower($sender->getName())] = true;

            $player->sendMessage(TextFormat::GOLD . $sender->getName() . " requested a $" . $args[1] . " CoinFlip with you.");
            $player->sendMessage(TextFormat::GOLD . "Type '/cf accept' to accept CoinFlip");
            $player->sendMessage(TextFormat::GOLD . "Type '/cf deny' to deny CoinFlip");
            $sender->sendMessage(TextFormat::GREEN . "$" . $args[1] . " CoinFlip Requested!");
        } else {
            $sender->sendMessage("USAGE: /cf <player> <betMoney> | /cf <accept/deny>");
        }
    }

    private function resolveCoinFlip(Player $sender, bool $accepted){
        $opponentName = CoinFlip::$queue[strtolower($sender->getName())][0];
        $betAmount = CoinFlip::$queue[strtolower($sender->getName())][1];

        if($accepted){
            if((time() % 2) == 0){
                EconomyAPI::getInstance()->reduceMoney($sender->getName(), $betAmount);
                EconomyAPI::getInstance()->addMoney($opponentName, $betAmount);
                $sender->getServer()->getPlayer($opponentName)->sendMessage(TextFormat::GREEN . "You WON the CoinFlip and got $" . $betAmount);
                $sender->sendMessage(TextFormat::RED . "You LOST the CoinFlip and lost $" . $betAmount);
            } else {
                EconomyAPI::getInstance()->addMoney($sender->getName(), $betAmount);
                EconomyAPI::getInstance()->reduceMoney($opponentName, $betAmount);
                $sender->sendMessage(TextFormat::GREEN . "You WON the CoinFlip and got $" . $betAmount);
                $sender->getServer()->getPlayer($opponentName)->sendMessage(TextFormat::RED . "You LOST the CoinFlip and lost $" . $betAmount);
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "You denied the CoinFlip request.");
            $sender->getServer()->getPlayer($opponentName)->sendMessage(TextFormat::RED . $sender->getName() . " denied the CoinFlip request.");
        }

        unset(CoinFlip::$inp[$opponentName]);
        unset(CoinFlip::$queue[strtolower($sender->getName())]);
    }
}
