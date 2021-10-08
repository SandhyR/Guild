<?php

namespace Guild;

use pocketmine\Player;

class GuildManager{

    private $plugin;

    public function __construct(Guild $plugin){
        $this->plugin = $plugin;
    }


    public function getGuildPlayer(string $guildname){
        $player = $this->plugin->getDatabase()->query("SELECT playername FROM playerguild WHERE guildname='$guildname'")->fetch_row();
        return $player;
    }

    public function getSlots(): int{
        return $this->plugin->config->get("guild_slots");
    }

    public function getGuildCount(string $guildname): int{
        $member = $this->plugin->getDatabase()->query("SELECT memberguild FROM guildinfo WHERE guildname='$guildname'")->fetch_row();
        return $member[0];
    }

    public function addMember(string $guildname, Player $player){
        if($this->getGuildCount($guildname) < $this->getSlots()){
            $playername = $player->getName();
            $memberguild = $this->getGuildCount($guildname);
            $this->plugin->getDatabase()->query("INSERT INTO playerguild VALUES(null, $playername, '$guildname', 'Member')");
            $this->plugin->getDatabase()->query("UPDATE guildinfo SET memberguild=$memberguild + 1 WHERE guildname='$guildname'");
            return true;
        }
        return false;
    }

    public function addGuild(string $guildname, Player $player){
        if($this->plugin->getDatabase()->query("SELECT * FROM guildinfo WHERE guildname='$guildname'")->fetch_row() == null){
            $playername = $player->getName();
            $this->plugin->getDatabase()->query("INSERT INTO guildinfo VALUES(null, '$guildname', '$playername', 1, 0, 1)");
             $this->plugin->getDatabase()->query("INSERT INTO playerguild VALUES(null, '$playername', '$guildname', 'Leader')");
            return true;
        }
        return false;
    }

    public function disbandGuild(string $guildname){
        $this->plugin->getDatabase()->query("DELETE FROM guildinfo WHERE guildname='$guildname'");
        $this->plugin->getDatabase()->query("DELETE FROM playerguild WHERE guildname='$guildname'");
    }

    public function promoteofficer(Player $player){
        $playername = $player->getName();
        $this->plugin->getDatabase()->query("UPDATE playerguild SET guildrank='Officer' WHERE playername='$playername'");
    }

    public function demoteofficer(Player $player){
        $playername = $player->getName();
        $this->plugin->getDatabase()->query("UPDATE playerguild SET guildrank='Member' WHERE playername='$playername'");
    }

    public function kickmember(Player $kicker, Player $player){
        $playername = $player->getName();
        if($this->plugin->getPlayerManager()->getPlayerRank($kicker) == "LEADER"){
            if($player->isOnline()){
                $playername = $player->getName();
                $guildname = $this->plugin->getPlayerManager()->getGuildName($player);
                $member = $this->plugin->getGuildManager()->getGuildCount($guildname);
                $this->plugin->getDatabase()->query("DELETE FROM playerguild WHERE username='$playername'");
                $this->plugin->getDatabase()->query("UPDATE guildinfo SET memberguild=$member - 1 WHERE guildname='$guildname'");
                $this->plugin->getGuildAPI()->sendCustomMessage("$playername has been kicked by {$kicker->getName()}", $this->plugin->getPlayerManager()->getGuildName($player));
                return true;
            }
        } elseif ($this->plugin->getPlayerManager()->getPlayerRank($kicker) == "OFFICER"){
            if($this->plugin->getPlayerManager()->getPlayerRank($player) !== "OFFICER"){
                $playername = $player->getName();
                $guildname = $this->plugin->getPlayerManager()->getGuildName($player);
                $member = $this->plugin->getGuildManager()->getGuildCount($guildname);
                $this->plugin->getDatabase()->query("DELETE FROM playerguild WHERE username='$playername'");
                $this->plugin->getDatabase()->query("UPDATE guildinfo SET memberguild=$member - 1 WHERE guildname='$guildname'");
                $this->plugin->getGuildAPI()->sendCustomMessage("$playername has been kicked by {$kicker->getName()}", $this->plugin->getPlayerManager()->getGuildName($player));
                return true;
            }
        }
        return false;
    }

    public function inviteMember(Player $inviter, Player $player){
        $playername = $player->getName();
        if(!$this->plugin->getPlayerManager()->isinGuild($player)){
            $inviter->sendMessage("Succesfuly send invite guild to {$player->getName()}");
            if($player->isOnline()){
                $guildname = $this->plugin->getPlayerManager()->getGuildName($inviter);
                $player->sendMessage("You are invited by {$inviter->getName()} to guild $guildname");
                $this->plugin->getGuildAPI()->sendCustomMessage("{$inviter->getName()} Invited $playername", $guildname);
                $this->plugin->getDatabase()->query("INSERT INTO playerguild VALUES(null, $playername, '$guildname', 'Inviting')");
                return true;
            }
        }
        return false;
    }

    public function acceptinvite(Player $player, string $guildname){
        if(!$this->plugin->getPlayerManager()->isinGuild($player)){
            $playername = $player->getName();
            $this->plugin->getDatabase()->query("DELETE FROM playerguild WHERE playername='$playername' AND guildname='$guildname' AND guildrank='Inviting'");
            $this->addMember($guildname, $player);
            $this->plugin->getGuildAPI()->sendCustomMessage("$playername Join guild", $guildname);
            return true;
        }
        return false;
    }
}
