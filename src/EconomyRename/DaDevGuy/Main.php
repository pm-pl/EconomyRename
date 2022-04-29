<?php
declare(strict_types=1);
namespace EconomyRename\DaDevGuy;

use cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\context\ClosureContext;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    public function onEnable(): void {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getResource("config.yml");
        if($this->getConfig()->get("config-ver") != 1){
            $this->getLogger()->info("§l§cWARNING: §r§cEconomyRename's config is NOT up to date. Please delete the config.yml and restart the server or the plugin may not work properly.");
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() === "rename") {
            if (!$sender instanceof Player) {
                $sender->sendMessage("Please Use This Command In-Game!");
                return false;
            }
            if (!$sender->hasPermission("economyrename.use")) {
                $sender->sendMessage($this->getConfig()->get("no-permission"));
                return false;
            }
            if (!isset($args[0])) {
                $sender->sendMessage($this->getConfig()->get("usage"));
            }
            
            if (isset($args[0])) {
                BedrockEconomyAPI::legacy()->getPlayerBalance(
                    $sender->getName(),
                    ClosureContext::create(
                        function (?int $balance) use ($sender, $args): void {
                            if ($balance <= $this->getConfig()->get("rename-price")) {//changed === to <=
                                $money = $this->getConfig()->get("rename-price");
                                $name = $args[0];
                                BedrockEconomyAPI::getInstance()->subtractFromPlayerBalance($sender->getName(), $money);
                                $item = $sender->getInventory()->getItemInHand();
                                $item->setCustomName($name);
                                $message = str_replace("{name}", $name, $this->getConfig()->get("rename-sucess"));
                                $sender->sendMessage($message);
                            } else {
                                $sender->sendMessage($this->getConfig()->get("no-money"));
                            }
                        },
                    )
                );
                return true;
            }
        }
        return false;
      }
    }
