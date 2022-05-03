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
				libEco::reduceMoney($sender, $price, static function(bool $success) use ($sender, $price): void {
                    if($success){
						if (is_null($sender)){
                             libEco::addMoney($sender, $price);
						} else{
							$name = $args[0];
							$item = $sender->getInventory()->getItemInHand();
							$item->setCustomName($name);
							$sender->getInventory()->setItemInHand($item);
							libEco::myMoney($player, static function(float $money) use($sender) : void {
							$sender->sendMessage($this->getMessage("rename-success", ["{name}", "{cost}"], [$sender->getName(), $money]));
								});
                        }
                    } elseif(!is_null($sender)){
						$name = $sender->getName();
						libEco::myMoney($player, static function(float $money) use($sender) : void {
						$sender->sendMessage($this->getMessage("no-money", ["{name}", "{cost}"], [$name, $money]));
							});
					}
                });
                return true;
            }
        }
        return false;
    }
	
	public function getMessage(string $msg, array $search = null, array $replace = null): string{
		if(is_null($search) and is_null($replace)){
			return TextFormat::colorize($this->getConfig()->get($msg));
		} else{
			$msg = TextFormat::colorize($this->getConfig()->get($msg));
			$msg = str_replace($search, $replace, $msg);
			return $msg;
		}
	}
}
