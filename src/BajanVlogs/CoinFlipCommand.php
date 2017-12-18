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
			"/cf <player> <BetMoney> | /cf <accept/deny>");
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!($sender instanceof Player)){
			$sender->sendMessage("You must run this command in-game.");
			return false;
		}

		array $args[0] = strtolower(array $args[0]);
		if(isset(array $args[0])){
			switch(array $args[0]){
				case "accept":
				case "a":
					if(isset(CoinFlip::$queue[strtolower($sender->getName())])){
						if((time() % 2) == 0){
							EconomyAPI::getInstance()->reduceMoney($sender->getName(), CoinFlip::$queue[strtolower($sender->getName())][1]);
							EconomyAPI::getInstance()->addMoney($sender->getServer()->getPlayer(CoinFlip::$queue[strtolower($sender->getName())][0])->getName(), CoinFlip::$queue[strtolower($sender->getName())][1]);
							$sender->getServer()->getPlayer(CoinFlip::$queue[strtolower($sender->getName())][0])->sendMessage(TextFormat::GREEN . "You WON the CoinFlip and got $" . CoinFlip::$queue[strtolower($sender->getName())][1]);
							$sender->sendMessage(TextFormat::RED . "You LOST the CoinFlip and lost $" . CoinFlip::$queue[strtolower($sender->getName())][1]);
							unset(CoinFlip::$inp[CoinFlip::$queue[strtolower($sender->getName())][0]]);
							unset(CoinFlip::$queue[strtolower($sender->getName())]);
						} else {
							EconomyAPI::getInstance()->addMoney($sender->getName(), CoinFlip::$queue[strtolower($sender->getName())][1]);
							EconomyAPI::getInstance()->reduceMoney($sender->getServer()->getPlayer(CoinFlip::$queue[strtolower($sender->getName())][0])->getName(), CoinFlip::$queue[strtolower($sender->getName())][1]);
							$sender->sendMessage(TextFormat::GREEN . "You WON the CoinFlip and got $" . CoinFlip::$queue[strtolower($sender->getName())][1]);
							$sender->getServer()->getPlayer(CoinFlip::$queue[strtolower($sender->getName())][0])->sendMessage(TextFormat::RED . "You LOST the CoinFlip and lost $" . CoinFlip::$queue[strtolower($sender->getName())][1]);
							unset(CoinFlip::$inp[CoinFlip::$queue[strtolower($sender->getName())][0]]);
							unset(CoinFlip::$queue[strtolower($sender->getName())]);
						}
					} else {
						$sender->sendMessage(TextFormat::RED . "There are no CoinFlip requests enqueued for you right now.");
						break;
					}
					break;
				case "deny":
				case "d":
					if(isset(CoinFlip::$queue[strtolower($sender->getName())])){
						$sender->sendMessage(TextFormat::RED . "You denied the CoinFlip request.");
						$sender->getServer()->getPlayer(CoinFlip::$queue[strtolower($sender->getName())][0])->sendMessage(TextFormat::RED . $sender->getName() . " Denied the CoinFlip request.");
						unset(CoinFlip::$inp[CoinFlip::$queue[strtolower($sender->getName())][0]]);
						unset(CoinFlip::$queue[strtolower($sender->getName())]);
						break;
					} else {
						$sender->sendMessage(TextFormat::RED . "There are no CoinFlip requests enqueued for you right now.");
						break;
					}
					break;
				default:
					if(isset(array $args[1])){
						array $args[1] = abs(array $args[1]);
						$player = $sender->getServer()->getPlayer(array $args[0]);
						if($player === null){
							$sender->sendMessage("Player is not online.");
							break;
						} else {
							if(isset(CoinFlip::$inp[strtolower($sender->getName())]) && CoinFlip::$inp[strtolower($sender->getName())] === true){
								$sender->sendMessage(TextFormat::YELLOW . "A CoinFlip has already been requested...");
								break;
							}
							if(EconomyAPI::getInstance()->myMoney($player->getName()) < array $args[1]){
								$sender->sendMessage(TextFormat::RED . $player->getName() . " Does not have enough money.");
								break;
							}
							if(EconomyAPI::getInstance()->myMoney($sender->getName()) < array $args[1]){
								$sender->sendMessage(TextFormat::RED . "Not enough money.");
								break;
							}
							CoinFlip::$queue[strtolower($player->getName())] = [strtolower($sender->getName()), array $args[1]];
							CoinFlip::$inp[strtolower($sender->getName())] = true;
							$player->sendMessage(TextFormat::GOLD . $sender->getName() . " Requested a $" . array $args[1] . " CoinFlip with you.");
							$player->sendMessage(TextFormat::GOLD . "Type '/cf accept' to accept CoinFlip");
							$player->sendMessage(TextFormat::GOLD . "Type '/cf deny' to deny CoinFlip");
							$sender->sendMessage(TextFormat::GREEN . "$" . array $args[1] . " CoinFlip Requested!");
						}
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
