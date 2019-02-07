<?php

namespace SchdowNVIDIA\OreEarn;

use pocketmine\entity\Effect;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
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
        $this->saveDefaultConfig();
        $this->saveResource("messages.yml");
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN."OreEarn aktiviert.");
    }

    public function onBreak(BlockBreakEvent $event) {
        $messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        if($event->isCancelled()) {
            return true;
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

        if($this->getConfig()->get("luckBonus") === false) {
            $lucklevel = 1;
        }

        if($id == Block::STONE) {
            $earn = $this->getConfig()->getNested("earnings.stoneEarn") * $lucklevel;
            $extraStone = rand(1, 100);
            if($extraStone > 98) {
                $event->setDrops(array(Item::get(4), Item::get(371, 0, rand(0, 1))));
            } else if ($extraStone < 2) {
                $event->setDrops(array(Item::get(4), Item::get(452, 0, rand(0, 1))));
            }
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
        }
        if($id == Block::COAL_ORE) {
            $earn = $this->getConfig()->getNested("earnings.coalEarn") * $lucklevel;
            $event->setDrops(array(Item::get(263, 0 , rand(1, $lucklevel))));
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("coalEarn"), $earn));
        }
        if($id == Block::REDSTONE_ORE || $id == Block::GLOWING_REDSTONE_ORE) {
            $earn = $this->getConfig()->getNested("earnings.redstoneEarn") * $lucklevel;
            $event->setDrops(array(Item::get(331, 0 , rand(1, $lucklevel))));
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("redstoneEarn"), $earn));
        }
        if($id == Block::LAPIS_ORE) {
            $earn = $this->getConfig()->getNested("earnings.lapisEarn") * $lucklevel;
            $event->setDrops(array(Item::get(351, 4 , rand(1, $lucklevel))));
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            $player->sendPopup($this->stringConvert($messages->get("lapisEarn"), $earn));
        }
        if($id == Block::DIAMOND_ORE) {
            $earn = $this->getConfig()->getNested("earnings.diamondEarn") * $lucklevel;
            $event->setDrops(array(Item::get(264, 0 , rand(1, $lucklevel))));
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("diamondEarn"), $earn));
        }
        if($id == Block::EMERALD_ORE) {
            $earn = $this->getConfig()->getNested("earnings.emeraldEarn") * $lucklevel;
            $event->setDrops(array(Item::get(388, 0 , rand(1, 2))));
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("emeraldEarn"), $earn));
        }
        if($id == Block::IRON_ORE) {
            $earn = $this->getConfig()->getNested("earnings.ironEarn") * $lucklevel;
            $event->setDrops(array(Item::get(265), Item::get(452, 0, rand(0, $lucklevel))));
            $event->setXpDropAmount(2.75);
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("ironEarn"), $earn));
        }
        if($id == Block::GOLD_ORE) {
            $earn = $this->getConfig()->getNested("earnings.goldEarn") * $lucklevel;
            $event->setDrops(array(Item::get(266), Item::get(371, 0, rand(0, $lucklevel))));
            $event->setXpDropAmount(5);
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $earn);
            if($this->getConfig()->get("enablePopup") === false) {
                return;
            }
            $player->sendPopup($this->stringConvert($messages->get("goldEarn"), $earn));
    }
    }
    public function stringConvert(string $string, int $earn): string{
        $string = str_replace("{money}", $earn, $string);
        return $string;
    }
}
