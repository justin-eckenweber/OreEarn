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

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase
{


    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->saveResource("messages.yml");
        $this->cfgChecker();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->saveDefaultConfig();
        if($this->getServer()->getPluginManager()->getPlugin("EconomyAPI") === null) {
            $this->getLogger()->error("OreEarn requires the plugin \"EconomyAPI\" to work!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
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





}