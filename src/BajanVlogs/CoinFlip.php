<?php

namespace BajanVlogs;

use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\entity\Effect;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Zombie;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\scheduler\CallbackTask;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\math\Vector2;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\Double;
// use pocketmine\nbt\tag\Float;
use pocketmine\nbt\tag\Short;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\tile\Chest;
use pocketmine\block\Block;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class CoinFlip extends PluginBase implements Listener
{
	/** @var string[] */
	public static $queue = [];
	public static $inp = [];

    public function onEnable(){
        $this->getLogger()->info('CoinFlip Loaded');
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("cf", new CoinFlipCommand("cf"));
    }
	
	public function onDisable(){
        $this->getLogger()->info('CoinFlip Disabled');
	}

	public function onLeave(PlayerQuitEvent $ev){
		if(isset(CoinFlip::$queue[strtolower($ev->getPlayer()->getName())])){
			$ev->getPlayer()->getServer()->getPlayer(CoinFlip::$queue[strtolower($ev->getPlayer()->getName())][0])->sendMessage(TextFormat::RED . $ev->getPlayer()->getName() . " Left the game.");
			unset(CoinFlip::$inp[CoinFlip::$queue[strtolower($ev->getPlayer()->getName())][0]]);
			unset(CoinFlip::$queue[strtolower($ev->getPlayer()->getName())]);
		}
	}
}
