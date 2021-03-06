<?php

namespace Guild\command;

use pocketmine\command\Command;
use Guild\Guild;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class GonlineCommand extends Command{

    private $plugin;

    public function __construct(string $name, string $description, Guild $plugin)
    {
        parent::__construct($name, $description);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if ($this->plugin->getPlayerManager()->isinGuild($sender)) {
                foreach ($this->plugin->getGuildManager()->getGuildPlayer($this->plugin->getPlayerManager()->getGuildName($sender)) as $p) {
                    $player = $this->plugin->getServer()->getPlayerExact($p);
                    if ($player->isOnline()) {
                        $onlineplayer = [];
                        array_push($onlineplayer, $player->getName());
                        $sender->sendMessage("Online: " . implode(" ", $onlineplayer));
                        var_dump($onlineplayer);
                    }
                }
            }
    }
    }

}
