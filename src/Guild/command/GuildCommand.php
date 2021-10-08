<?php

namespace Guild\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Guild\Guild;
use pocketmine\Player;

class GuildCommand extends Command{

    public function __construct(string $name, string $description, Guild $plugin)
    {
        parent::__construct($name, $description);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player){
            $this->plugin->getForm($sender);
        } else {
            $sender->sendMessage("You not a player");
        }
    }
}
