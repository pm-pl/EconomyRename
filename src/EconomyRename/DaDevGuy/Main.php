<?php
declare(strict_types=1);

namespace EconomyRename\DaDevGuy;
use davidglitch04\libEco\libEco;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    public function onEnable(): void 
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getResource("config.yml");

        //Config Version

        if($this->getConfig()->get("config-ver") != 2)
        {
            $this->getLogger()->info("§l§cWARNING: §r§cEconomyRename's config is NOT up to date. Please delete the config.yml and restart the server or the plugin may not work properly.");
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() === "rename") 
        {
            if (!$sender instanceof Player) 
            {
                $sender->sendMessage("Please Use This Command In-Game!");
            }
            if (!$sender->hasPermission("economyrename.use")) 
            {
                $sender->sendMessage($this->getConfig()->get("no-permission"));
            }
            if (!isset($args[0]))
            {
                $sender->sendMessage($this->getConfig()->get("usage"));
            }
            
            if (isset($args[0])) 
            {
                $price = $this->getConfig()->get("rename-price");
                $bal = libEco::myMoney($sender);
                if($bal = $price){
                    $p = $sender->getName();
                    libEco::reduceMoney($sender, $price);
                    $name = $args[0];
                    $item = $sender->getInventory()->getItemInHand();
                    $item->setCustomName($name);
                    $message = str_replace("{name}", $name, $this->getConfig()->get("rename-sucess"));
                    $sender->sendMessage($message);
                } else {
                    $name = $sender->getName();
                    $message = str_replace("{name}", $name, $this->getConfig()->get("no-money"));
                    $sender->sendMessage($message);
                }
                return true;
            }
        }
        return false;
    }
}
