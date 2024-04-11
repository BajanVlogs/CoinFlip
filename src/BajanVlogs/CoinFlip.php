<?php

declare(strict_types = 1);

namespace BajanVlogs\CoinFlip;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;


class CoinFlip extends PluginBase implements Listener {
    
    public function onEnable() : void{
    }

    public function onDisable() : void{
    }

    public function Flip(){

        $result = mt_rand(0,1);//1 tails : 0 heads

        return $result;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        switch ($command->getName()) {
          case "coinflip":
            
            if( $this->flip() == 1){
                
                $this->getServer()->broadcastMessage("§6>> §eA coin flip resulted in: §l§eTails");
                
            } else {
                
                $this->getServer()->broadcastMessage("§6>> §eA coin flip resulted in: §l§eHeads");

            }

            break;
        }
        return true;
    }
}
