<?php

namespace Guild;

use pocketmine\Player;

class PlayerManager{

    private $plugin;

    public function __construct(Guild $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getPlayerRank(Player $player){
        $playername = $player->getName();
        $rank = $this->plugin->getDatabase()->query("SELECT guildrank FROM playerguild WHERE playername='$playername'")->fetch_row();
        switch (strtoupper($rank[0])){
            case "LEADER":
                return "LEADER";
                break;
            case "OFFICER":
                return "OFFICER";
                break;
            case "MEMBER":
                return "MEMBER";
                break;
        }
        return "MEMBER";
    }

    public function isinGuild(Player $player){
        $playername = $player->getName();
        $player = $this->plugin->getDatabase()->query("SELECT * FROM playerguild WHERE playername='$playername'")->fetch_row();
        if($player !== null){
            return true;
        }
        return false;
    }

    public function getGuildName(Player $player){
        $playername = $player->getName();
        $name = $this->plugin->getDatabase()->query("SELECT guildname FROM playerguild WHERE playername='$playername'")->fetch_row();
        return $name[0];
    }

    public function leaveguild(Player $player){
        $playername = $player->getName();
        $guildname = $this->getGuildName($player);
        $member = $this->plugin->getGuildManager()->getGuildCount($guildname);
        $this->plugin->getDatabase()->query("DELETE FROM playerguild WHERE username='$playername'");
        $this->plugin->getDatabase()->query("UPDATE guildinfo SET memberguild=$member - 1 WHERE guildname='$guildname'");
    }
}