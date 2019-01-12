<?php

namespace OreEarn;

use pocketmine\entity\Effect;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;


class Main extends PluginBase implements Listener
{


    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN."OreEarn aktiviert.");
    }

    public function onBreak(BlockBreakEvent $event) {
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
            return true;
        }
        if($item->hasEnchantment(Enchantment::FORTUNE)) {
            $lucklevel = $item->getEnchantmentLevel(Enchantment::FORTUNE) + 1;
        }

        $stoneEarn = 0.05 * $lucklevel;
        $coalEarn = 2.5 * $lucklevel;
        $redstoneEarn = 5 * $lucklevel;
        $lapisEarn = 10 * $lucklevel;
        $diamondEarn = 50 * $lucklevel;
        $emeraldEarn = 250 * $lucklevel;
        $ironEarn = 3.5 * $lucklevel;
        $goldEarn = 25 * $lucklevel;

        if($id == Block::STONE) {
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $stoneEarn);
        }
        if($id == Block::COAL_ORE) {
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $coalEarn);
            $player->sendPopup("Du hast soeben §6" . $coalEarn . "$ §ffür das abbauen von §6Coal Ore §ferhalten.");
        }
        if($id == Block::REDSTONE_ORE || $id == Block::GLOWING_REDSTONE_ORE) {
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $redstoneEarn);
            $player->sendPopup("Du hast soeben §6" . $redstoneEarn . "$ §ffür das abbauen von §6Redstone Ore §ferhalten.");
        }
        if($id == Block::LAPIS_ORE) {
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $lapisEarn);
            $player->sendPopup("Du hast soeben §6" . $lapisEarn . "$ §ffür das abbauen von §6Lapis Ore §ferhalten.");
        }
        if($id == Block::DIAMOND_ORE) {
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $diamondEarn);
            $player->sendPopup("Du hast soeben §6" . $diamondEarn . "$ §ffür das abbauen von §6Diamond Ore §ferhalten.");
        }
        if($id == Block::EMERALD_ORE) {
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $emeraldEarn);
            $player->sendPopup("Du hast soeben §6" . $emeraldEarn . "$ §ffür das abbauen von §6Emerald Ore §ferhalten.");
        }
        if($id == Block::IRON_ORE) {
            $event->setDrops(array(Item::get(265)));
            $event->setXpDropAmount(2.75);
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $ironEarn);
            $player->sendPopup("Du hast soeben §6" . $ironEarn . "$ §ffür das abbauen von §6Iron Ore §ferhalten.");
        }
        if($id == Block::GOLD_ORE) {
            $event->setDrops(array(Item::get(266)));
            $event->setXpDropAmount(5);
            $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($name, $goldEarn);
            $player->sendPopup("Du hast soeben §6" . $goldEarn . "$ §ffür das abbauen von §6Gold Ore §ferhalten.");
    }


    }

}