<?php

namespace BajanVlogs;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use jojoe77777\FormAPI\SimpleForm;

class CoinFlipCommand extends Command {

    /** @var CoinFlip */
    private $plugin;

    public function __construct(string $name, CoinFlip $plugin) {
        parent::__construct($name);
        $this->setDescription("Flip a coin");
        $this->setUsage("/cf");
        $this->setPermission("coinflip.use");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        $result = mt_rand(0, 1) == 0 ? "Heads" : "Tails";

        $this->sendCoinFlipMenu($sender, $result);

        return true;
    }

    private function sendCoinFlipMenu(Player $player, string $result): void {
        $form = new SimpleForm(function (Player $player, ?int $data) use ($result): void {
            if ($data === null) {
                return;
            }
            // Handle menu response if needed
        });
        $form->setTitle("Coin Flip");
        $form->setContent("The result is: " . $result);
        $form->addButton("Close");
        $player->sendForm($form);
    }
}
