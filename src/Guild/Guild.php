<?php

namespace Guild;

use Guild\command\GonlineCommand;
use Guild\command\GuildChat;
use Guild\command\GuildCommand;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Guild extends PluginBase{

    public $config;

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
        $this->getDatabase()->query("CREATE TABLE IF NOT EXISTS guildinfo ( id INT PRIMARY KEY AUTO_INCREMENT, guildname VARCHAR(255) NOT NULL, leadername VARCHAR(255) NOT NULL , memberguild INT(11) NOT NULL, guildexp INT(11) NOT NULL, guildlevel INT(11) NOT NULL);");
        $this->getDatabase()->query("CREATE TABLE IF NOT EXISTS playerguild ( id INT PRIMARY KEY AUTO_INCREMENT, playername VARCHAR(255) NOT NULL, guildname VARCHAR(255) NOT NULL, guildrank VARCHAR(255) NOT NULL);");
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("Guild", new GuildCommand("guild", "Guild Command", $this));
        $this->getServer()->getCommandMap()->register("GuildChat", new GuildChat("guildchat", "GuildChat Command to send message to guild member", $this));
        $this->getServer()->getCommandMap()->register("Guild Online", new GonlineCommand("gonline", "Check online member guild", $this));
    }

    public function getDatabase()
    {
        return new \mysqli($this->config->get("host"), $this->config->get("user"), $this->config->get("password"), $this->config->get("db-name"));
    }

    public function getGuildManager(){
        return new GuildManager($this);
    }

    public function getPlayerManager(){
        return new PlayerManager($this);
    }

    public function getForm(Player $player){
        return new FormManager($this, $player);
    }

    public function getGuildAPI(){
        return new GuildAPI($this);
    }

    public function getLevelManager(){
        return new GuildLevelManager($this);
    }
}
