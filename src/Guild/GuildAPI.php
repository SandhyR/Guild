<?php

namespace Guild;

use pocketmine\Player;

class GuildAPI
{

    private $plugin;

    public function __construct(Guild $plugin)
    {
        $this->plugin = $plugin;
    }

    public function sendMessageGuild(Player $player, string $msg)
    {
        foreach ($this->plugin->getGuildManager()->getGuildPlayer($this->plugin->getPlayerManager()->getGuildName($player)) as $p) {
            $players = $this->plugin->getServer()->getPlayerExact($p);
            if ($players->isOnline()) {
                $player->sendMessage("GUILD > {$player->getName()}: $msg");
            }
        }
    }

    public function sendCustomMessage(string $msg, string $guildname)
    {
        foreach ($this->plugin->getGuildManager()->getGuildPlayer($guildname) as $p) {
            $players = $this->plugin->getServer()->getPlayerExact($p);
            if ($players->isOnline()) {
                $players->sendMessage("GUILD > {$msg}");
            }
        }
    }
}