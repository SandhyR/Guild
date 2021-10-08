<?php

namespace Guild;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;

class EventListener implements Listener{

    private $plugin;

    public function __construct(Guild $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();
        var_dump($this->plugin->getPlayerManager()->isinGuild($player));
        if($this->plugin->getPlayerManager()->isinGuild($player)){
            $this->plugin->getLevelManager()->addGuildExp($this->plugin->getPlayerManager()->getGuildName($player), mt_rand(1, 80));
        }
    }
}
