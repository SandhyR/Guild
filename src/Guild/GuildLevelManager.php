<?php

namespace Guild;

class GuildLevelManager{

    private $plugin;

    public function __construct(Guild $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getExp(string $guildname){
        $exp = $this->plugin->getDatabase()->query("SELECT guildexp FROM guildinfo WHERE guildname='$guildname'")->fetch_row();
        return $exp[0];
    }

    public function getGuildLevel(string $guildname){
        $level = $this->plugin->getDatabase()->query("SELECT guildlevel FROM guildinfo WHERE guildname='$guildname'")->fetch_row();
        return $level[0];
    }

    public function addGuildLevel(string $guildname){
        $level = $this->getGuildLevel($guildname);
        $this->plugin->getDatabase()->query("UPDATE guildinfo SET guildlevel=$level + 1 WHERE guildname='$guildname'");
    }

    public function addGuildExp(string $guildname, int $value){
        $exp = $this->getExp($guildname);
        $this->plugin->getDatabase()->query("UPDATE guildinfo SET guildexp=$exp + $value WHERE guildname='$guildname'");
        if($exp >= $this->getLimitexp($guildname)){
            $exp = $exp - $this->getLimitexp($guildname);
            $this->setExp($guildname, $exp);
            $this->addGuildLevel($guildname);
        }
    }

    public function getLimitexp(string $guildname){
        $level = $this->getGuildLevel($guildname);
        return $level * 1000;
    }

    public function setExp(string $guildname, int $value){
        $this->plugin->getDatabase()->query("UPDATE guildinfo SET guildexp=$value WHERE guildname='$guildname'");
    }
}
