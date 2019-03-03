<?php
namespace SchdowNVIDIA\OreEarn;

use pocketmine\entity\Effect;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;


class Main extends PluginBase implements Listener
{


    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->saveResource("messages.yml");
        $this->cfgChecker();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
    }

    public function cfgChecker() {
        $cfgVersion = 4;
        $msgVersion = 1;
        if(($this->getConfig()->get("cfg-version")) < $cfgVersion || !($this->getConfig()->exists("cfg-version"))) {
            $this->getLogger()->critical("Your config.yml is outdated.");
            $this->getLogger()->info("Loading new config version...");
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "old_config.yml");
            $this->saveResource("config.yml");
            $this->getLogger()->notice("Done: The old config has been saved as \"old_config.yml\" and the new config has been successfully loaded.");
        };
        $messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        if(($messages->get("msg-version")) < $msgVersion || !($messages->exists("msg-version"))) {
            $this->getLogger()->critical("Your messages.yml is outdated.");
            $this->getLogger()->info("Loading new messages version...");
            rename($this->getDataFolder() . "messages.yml", $this->getDataFolder() . "old_messages.yml");
            $this->saveResource("messages.yml");
            $this->getLogger()->notice("Done: The old messages config has been saved as \"old_messages.yml\" and the new messages config has been successfully loaded.");
        };
    }




    public function onBreak(BlockBreakEvent $event) {
        $messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        $ignoredWorlds = $this->getConfig()->get("ignoredWorlds");
        $ignoredDropWorlds = $this->getConfig()->get("ignoredDropWorlds");
        $world = $event->getPlayer()->getLevel()->getName();
        $allowDrop = true;
        if($event->isCancelled()) {
            return true;
        }
        if(in_array($world, $ignoredWorlds)) {
            return true;
        }
        if(in_array($world, $ignoredDropWorlds)) {
            $allowDrop = false;
        }

        $player = $event->getPlayer();
        $name = $player->getName();
        $item = $event->getItem();
        $id = $event->getBlock()->getId();
        $lucklevel = 1;

        if($player->getGamemode() > 0) {
            return true;
        }

        if($item->hasEnchantment(Enchantment::SILK_TOUCH)) {
            if($this->getConfig()->get("ignoreSilktouch") === true) {
                return true;
            }
        }

        if($item->hasEnchantment(Enchantment::FORTUNE)) {
            $lucklevel = $item->getEnchantmentLevel(Enchantment::FORTUNE) + 1;
        }

        if($this->getConfig()->get("fortuneFix") === false) {
            $luckdrop = 1;
        } else {
            $luckdrop = $lucklevel;
        }

        if($this->getConfig()->get("fortuneBonus") === false) {
            $lucklevel = 1;
        }



        if($id == Block::STONE) {
            $earn = $this->getConfig()->getNested("earnings.stoneEarn") * $lucklevel;
            $extraStone = rand(1, 100);
            if($allowDrop === true) {
                if ($extraStone > 98) {
                    $event->setDrops(array(Item::get(4), Item::get(371, 0, rand(0, 1))));
                } else if ($extraStone < 2) {
                    $event->setDrops(array(Item::get(4), Item::get(452, 0, rand(0, 1))));
                }
            }
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
        }
        if($id == Block::COAL_ORE) {
            $earn = $this->getConfig()->getNested("earnings.coalEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(263, 0, rand(1, $luckdrop))));
            } else {
                $event->setDrops([]);
            }
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("coalEarn"), $earn));
        }
        if($id == Block::REDSTONE_ORE || $id == Block::GLOWING_REDSTONE_ORE) {
            $earn = $this->getConfig()->getNested("earnings.redstoneEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(331, 0, rand((2 * $luckdrop), (4 * $luckdrop)))));
            } else {
                $event->setDrops([]);
            }
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("redstoneEarn"), $earn));
        }
        if($id == Block::LAPIS_ORE) {
            $earn = $this->getConfig()->getNested("earnings.lapisEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(351, 4, rand((2 * $luckdrop), (4 * $luckdrop)))));
            } else {
                $event->setDrops([]);
            }
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            $player->sendPopup($this->stringConvert($messages->get("lapisEarn"), $earn));
        }
        if($id == Block::DIAMOND_ORE) {
            $earn = $this->getConfig()->getNested("earnings.diamondEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(264, 0, rand(1, $luckdrop))));
            } else {
                $event->setDrops([]);
            }
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("diamondEarn"), $earn));
        }
        if($id == Block::EMERALD_ORE) {
            $earn = $this->getConfig()->getNested("earnings.emeraldEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(388, 0, rand(1, $luckdrop))));
            } else {
                $event->setDrops([]);
            }
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("emeraldEarn"), $earn));
        }
        if($id == Block::IRON_ORE) {
            $earn = $this->getConfig()->getNested("earnings.ironEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(265), Item::get(452, 0, rand(0, $luckdrop))));
            } else {
                $event->setDrops([]);
            }
            $event->setXpDropAmount(2.75);
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("ironEarn"), $earn));
        }
        if($id == Block::GOLD_ORE) {
            $earn = $this->getConfig()->getNested("earnings.goldEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(266), Item::get(371, 0, rand(0, $luckdrop))));
            } else {
                $event->setDrops([]);
            }
            $event->setXpDropAmount(5);
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("goldEarn"), $earn));
    }
        if($id == Block::QUARTZ_ORE) {
            $earn = $this->getConfig()->getNested("earnings.quartzEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(406, 0, rand(1, $luckdrop))));
            } else {
                $event->setDrops([]);
            }
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("quartzEarn"), $earn));
        }
    }
    public function stringConvert(string $string, int $earn): string{
        $string = str_replace("{money}", $earn, $string);
        return $string;
    }
}