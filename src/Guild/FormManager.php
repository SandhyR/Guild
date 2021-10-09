<?php

namespace Guild;

use pocketmine\Player;

class FormManager
{
    private $playerlist = [];
    private $plugin;
    private $members = [];
    
    public function __construct(Guild $plugin, Player $player)
    {
        $this->plugin = $plugin;
       $this->formpermission($player);
    }

    public function formpermission(Player $player){
        $playername = $player->getName();
        $rank = $this->plugin->getDatabase()->query("SELECT guildrank FROM playerguild WHERE playername='$playername'")->fetch_row();
        if ($rank !== null) {
            switch (strtoupper($rank[0])) {
                case "LEADER":
                    $this->leaderform($player);
                    break;
                case "OFFICER":
                    $this->officerform($player);
                    break;
                case "MEMBER":
                    $this->memberform($player);
                    break;
            }
        } else {
            $this->noguildform($player);
        }
    }

    public function leaderform(Player $player)
    {
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    $this->guildstats($player);
                    break;
                case 1:
                    $this->memberlist($player);
                    break;
                case 2:
                    $this->inviteform($player);
                    break;
                case 3:
                    $this->kickform($player);
                    break;
                case 4:
                    $this->promoteofficer($player);
                    break;
                case 5:
                    $this->demoteofficer($player);
                    break;
                case 6:
                    $this->plugin->getGuildManager()->disbandGuild($this->plugin->getPlayerManager()->getGuildName($player));
            }
            return false;
        });
        $form->setTitle("Guild Management");
        $form->setContent("Manage your guild");
        $form->addButton("Guild statistic");
        $form->addButton("Member [{$this->plugin->getGuildManager()->getGuildCount($this->plugin->getPlayerManager()->getGuildName($player))}]");
        $form->addButton("Invite Member");
        $form->addButton("Kick Member");
        $form->addButton("Promote Officer");
        $form->addButton("Demote Officer");
        $form->addButton("Disband Your guild");
        $form->sendToPlayer($player);
        return $form;
    }

    public function guildstats(Player $player){
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    $this->formpermission($player);
            }
            return false;
        });
        $form->setTitle("GuildStats");
        $form->setContent("GuildName: {$this->plugin->getPlayerManager()->getGuildName($player)} \nGuild Level: {$this->plugin
        ->getLevelManager()->getGuildLevel($this->plugin->getPlayerManager()->getGuildName($player))}\n GuildExp: {$this->plugin->getLevelManager()->getExp($this->plugin->getPlayerManager()->getGuildName($player))}/{$this->plugin->getLevelManager()->getLimitexp($this->plugin->getPlayerManager()->getGuildName($player))}");
        $form->addButton("Back");
        $form->sendToPlayer($player);
        return $form;
    }

    public function inviteform(Player $player){
        $list = [];
        foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
            $list[] = $p->getName();
        }
        $this->playerlist[$player->getName()] = $list;
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createCustomForm(function (Player $player, array $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $index = $data[1];
            $playername = $this->playerlist[$player->getName()][$index];
           $invite = $this->plugin->getServer()->getPlayerExact($playername);
           $this->plugin->getGuildManager()->inviteMember($player, $invite);
            $player->sendMessage("Send invite to $playername");
            return false;
        });
        $form->setTitle("InviteMember");
        $form->addLabel("Invite member to guild");
        $form->addDropdown("Select player", $this->playerlist[$player->getName()]);
        $form->sendToPlayer($player);
        return $form;
    }

    public function kickform(Player $player){
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player,$data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $players = $this->members[$result];
            $this->plugin->getGuildManager()->kickmember($player, $players);
            unset($this->members[$player->getName()]);
            return false;
        });
        $form->setTitle("KickMember");
        $form->setContent("Kick Member guild");
        foreach($this->plugin->getGuildManager()->getGuildPlayer($this->plugin->getPlayerManager()->getGuildName($player)) as $p){
            array_push($this->members, $p);
            var_dump($this->members);
            $form->addButton($p);
        }
        $form->sendToPlayer($player);
        return $form;
    }

    public function promoteofficer(Player $player){
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player,$data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $players = $this->plugin->getServer()->getPlayerExact($this->members[$result]);
            $this->plugin->getGuildManager()->promoteofficer($players);
            unset($this->members[$player->getName()]);
            return false;
        });
        $form->setTitle("PromoteMember");
        $form->setContent("Promote Member to officer");
        foreach($this->plugin->getGuildManager()->getGuildPlayer($this->plugin->getPlayerManager()->getGuildName($player)) as $p){
            array_push($this->members, $p);
            var_dump($this->members);
            $form->addButton($p);
        }
        $form->sendToPlayer($player);
        return $form;
    }

    public function demoteofficer(Player $player){
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player,$data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $players = $this->plugin->getServer()->getPlayerExact($this->members[$result]);
            $this->plugin->getGuildManager()->demoteofficer($players);
            unset($this->members[$player->getName()]);
            return false;
        });
        $form->setTitle("DemoteOfficer");
        $form->setContent("Demote Officer to member");
        foreach($this->plugin->getGuildManager()->getGuildPlayer($this->plugin->getPlayerManager()->getGuildName($player)) as $p){
            array_push($this->members, $p);
            var_dump($this->members);
            $form->addButton($p);
        }
        $form->sendToPlayer($player);
        return $form;
    }

    public function accinvite(Player $player){
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $guildname = $this->members[$data];
            $this->plugin->getGuildManager()->acceptinvite($player, $guildname);
            unset($this->members[$player->getName()]);
            return false;
        });
        $form->setTitle("Invite");
        foreach($this->plugin->getDatabase()->query("SELECT guildname FROM playerguild WHERE playername='{$player->getName()}' AND guildrank='Inviting'") as $p){
            array_push($this->members, $p);
            var_dump($this->members);
            $form->addButton($p);
        }
        $form->sendToPlayer($player);
        return $form;
    }

    public function createguildform(Player $player){
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createCustomForm(function (Player $player, array $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $index = $data[1];
            if ($this->plugin->getGuildManager()->addGuild($index, $player)){
                var_dump($this->plugin->getGuildManager()->addGuild($index, $player));
            }
            return false;
        });
        $form->setTitle("CreateGuild");
        $form->addLabel("Create a guild");
        $form->addInput("GuildName", "Guildname");
        $form->sendToPlayer($player);
        return $form;
    }

    public function officerform(Player $player){
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    $this->guildstats($player);
                    break;
                case 1:
                    $this->memberlist($player);
                    break;
                case 2:
                    $this->inviteform($player);
                    break;
                case 3:
                    $this->kickform($player);
                    break;
                case 4:
                    $this->plugin->getPlayerManager()->leaveguild($player);
            }
            return false;
        });
        $form->setTitle("Guild Management");
        $form->setContent("Manage your guild");
        $form->addButton("Guild statistic");
        $form->addButton("Member [{$this->plugin->getGuildManager()->getGuildCount($this->plugin->getPlayerManager()->getGuildName($player))}]");
        $form->addButton("Invite Member");
        $form->addButton("Kick Member");
        $form->addButton("Leave Guild");
        $form->sendToPlayer($player);
        return $form;
    }

    public function memberlist(Player $player){
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player,$data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            return false;
        });
        $form->setTitle("Memberlist");
        foreach($this->plugin->getGuildManager()->getGuildPlayer($this->plugin->getPlayerManager()->getGuildName($player)) as $p){
            array_push($this->members, $p);
            var_dump($this->members);
            $form->addButton($p);
        }
        $form->sendToPlayer($player);
        return $form;
    }

    public function memberform(Player $player){
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    $this->guildstats($player);
                    break;
                case 1:
                    $this->memberlist($player);
                    break;
                case 2:
                    $this->plugin->getPlayerManager()->leaveguild($player);
            }
            return false;
        });
        $form->setTitle("Guild Management");
        $form->setContent("Manage your guild");
        $form->addButton("Guild statistic");
        $form->addButton("Member [{$this->plugin->getGuildManager()->getGuildCount($this->plugin->getPlayerManager()->getGuildName($player))}]");
        $form->addButton("Leave guild");
        $form->sendToPlayer($player);
        return $form;
    }

    public function noguildform(Player $player){
        $api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api === null) {
        }
        $form = $api->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                    $this->createguildform($player);
                    break;
                case 1:
                    $playername = $player->getName();
                   if($this->plugin->getDatabase()->query("SELECT playername FROM playerguild WHERE playername='$playername' AND guildrank='Inviting'")->fetch_row() !== null) {
                        $this->accinvite($player);
                    } else{
                        $player->sendMessage("You dont have invite guild");
                    }
                    break;
            }
            return false;
        });
        $form->setTitle("Guild");
        $form->addButton("Create Guild");
        $form->addButton("View Invites");
        $form->sendToPlayer($player);
        return $form;
    }
}
