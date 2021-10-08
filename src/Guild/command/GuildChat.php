<?php

namespace Guild\command;

use Guild\Guild;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GuildChat extends Command{
    
    private $plugin;
    
    public function __construct(string $name, string $description, Guild $plugin)
    {
        parent::__construct($name, $description);
        parent::setAliases(["gc"]);
        $this->plugin = $plugin;
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if ($this->plugin->getPlayerManager()->isinGuild($sender)) {
                foreach ($this->plugin->getGuildManager()->getGuildPlayer($this->plugin->getPlayerManager()->getGuildName($sender)) as $p) {
                    $player = $this->plugin->getServer()->getPlayerExact($p);
                    if ($player->isOnline()) {
                        $chat = implode(" ", $args);
                        $player->sendMessage("GUILD > $chat");
                    }
                }
            }
        }
    }
}