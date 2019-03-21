<?php

/*
      ___           _____
     / _ \ _ __ ___| ____|__ _ _ __ _ __
    | | | | '__/ _ \  _| / _` | '__| '_ \
    | |_| | | |  __/ |__| (_| | |  | | | |
     \___/|_|  \___|_____\__,_|_|  |_| |_|

    Copyright (C) 2019 SchdowNVIDIA
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.

 */

declare(strict_types = 1);

namespace SchdowNVIDIA\OreEarn;

use onebone\economyapi\EconomyAPI;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\utils\Config;

class EventListener implements Listener {

    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onBreak(BlockBreakEvent $event) {
        $messages = new Config($this->plugin->getDataFolder() . "messages.yml", Config::YAML);
        $ignoredWorlds = $this->plugin->getConfig()->get("ignoredWorlds");
        $ignoredDropWorlds = $this->plugin->getConfig()->get("ignoredDropWorlds");
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
            if($this->plugin->getConfig()->get("ignoreSilktouch") === true) {
                return true;
            }
        }

        if($item->hasEnchantment(Enchantment::FORTUNE)) {
            $lucklevel = $item->getEnchantmentLevel(Enchantment::FORTUNE) + 1;
        }

        if($this->plugin->getConfig()->get("fortuneFix") === false) {
            $luckdrop = 1;
        } else {
            $luckdrop = $lucklevel;
        }

        if($this->plugin->getConfig()->get("fortuneBonus") === false) {
            $lucklevel = 1;
        }

        $economyApi = EconomyAPI::getInstance();

        // Stone
        if($id == Block::STONE) {
            $earn = $this->plugin->getConfig()->getNested("earnings.stoneEarn") * $lucklevel;
            $extraStone = rand(1, 100);
            if($allowDrop === true) {
                if ($extraStone > 98) {
                    $event->setDrops(array(Item::get(4), Item::get(371, 0, rand(0, 1))));
                } else if ($extraStone < 2) {
                    $event->setDrops(array(Item::get(4), Item::get(452, 0, rand(0, 1))));
                }
            }
            $economyApi->addMoney($name, $earn);
        }

        // Coal Ore
        if($id == Block::COAL_ORE) {
            $earn = $this->plugin->getConfig()->getNested("earnings.coalEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(263, 0, rand(1, $luckdrop))));
            } else {
                $event->setDrops([]);
            }
            $economyApi->addMoney($name, $earn);
            if($this->plugin->getConfig()->get("enablePopup") === false) {
                return true;
            }
            $player->sendPopup($this->stringConvert($messages->get("coalEarn"), $earn));
        }

        // Redstone Ore
        if($id == Block::REDSTONE_ORE || $id == Block::GLOWING_REDSTONE_ORE) {
            $earn = $this->plugin->getConfig()->getNested("earnings.redstoneEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(331, 0, rand((2 * $luckdrop), (4 * $luckdrop)))));
            } else {
                $event->setDrops([]);
            }
            $economyApi->addMoney($name, $earn);
            if($this->plugin->getConfig()->get("enablePopup") === false) {
                return true;
            }
            $player->sendPopup($this->stringConvert($messages->get("redstoneEarn"), $earn));
        }

        // Lapis Ore
        if($id == Block::LAPIS_ORE) {
            $earn = $this->plugin->getConfig()->getNested("earnings.lapisEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(351, 4, rand((2 * $luckdrop), (4 * $luckdrop)))));
            } else {
                $event->setDrops([]);
            }
            $economyApi->addMoney($name, $earn);
            $player->sendPopup($this->stringConvert($messages->get("lapisEarn"), $earn));
        }

        // Diamond Ore
        if($id == Block::DIAMOND_ORE) {
            $earn = $this->plugin->getConfig()->getNested("earnings.diamondEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(264, 0, rand(1, $luckdrop))));
            } else {
                $event->setDrops([]);
            }
            $economyApi->addMoney($name, $earn);
            if($this->plugin->getConfig()->get("enablePopup") === false) {
                return true;
            }
            $player->sendPopup($this->stringConvert($messages->get("diamondEarn"), $earn));
        }

        // Emerald Ore
        if($id == Block::EMERALD_ORE) {
            $earn = $this->plugin->getConfig()->getNested("earnings.emeraldEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(388, 0, rand(1, $luckdrop))));
            } else {
                $event->setDrops([]);
            }
            $economyApi->addMoney($name, $earn);
            if($this->plugin->getConfig()->get("enablePopup") === false) {
                return true;
            }
            $player->sendPopup($this->stringConvert($messages->get("emeraldEarn"), $earn));
        }

        // Iron Ore
        if($id == Block::IRON_ORE) {
            $earn = $this->plugin->getConfig()->getNested("earnings.ironEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(265), Item::get(452, 0, rand(0, $luckdrop))));
            } else {
                $event->setDrops([]);
            }
            $event->setXpDropAmount(2);
            $economyApi->addMoney($name, $earn);
            if($this->plugin->getConfig()->get("enablePopup") === false) {
                return true;
            }
            $player->sendPopup($this->stringConvert($messages->get("ironEarn"), $earn));
        }

        // Gold Ore
        if($id == Block::GOLD_ORE) {
            $earn = $this->plugin->getConfig()->getNested("earnings.goldEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(266), Item::get(371, 0, rand(0, $luckdrop))));
            } else {
                $event->setDrops([]);
            }
            $event->setXpDropAmount(5);
            $economyApi->addMoney($name, $earn);
            if($this->plugin->getConfig()->get("enablePopup") === false) {
                return true;
            }
            $player->sendPopup($this->stringConvert($messages->get("goldEarn"), $earn));
        }

        // Quartz Ore
        if($id == Block::QUARTZ_ORE) {
            $earn = $this->plugin->getConfig()->getNested("earnings.quartzEarn") * $lucklevel;
            if($allowDrop === true) {
                $event->setDrops(array(Item::get(406, 0, rand(1, $luckdrop))));
            } else {
                $event->setDrops([]);
            }
            $economyApi->addMoney($name, $earn);
            if($this->plugin->getConfig()->get("enablePopup") === false) {
                return true;
            }
            $player->sendPopup($this->stringConvert($messages->get("quartzEarn"), $earn));
        }
    }

    // String Convert function thanks to JackMD
    public function stringConvert(string $string, float $earn): string{
        $string = str_replace("{money}", $earn, $string);
        return $string;
    }

}





/*
 *
 *<?php
declare(strict_types = 1);

namespace SchdowNVIDIA\CityBuildCore;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;

class EventListener implements Listener {
 */