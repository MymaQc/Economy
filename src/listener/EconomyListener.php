<?php

namespace economy\listener;

use economy\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

final class EconomyListener implements Listener {

    /**
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $provider = Main::getInstance()->getProvider();
        $config = Main::getInstance()->getConfig();
        if (!$provider->exist($player)) {
            $provider->create($player, intval($config->getNested("economy.settings.default-money")));
            Main::getInstance()->getLogger()->notice(str_replace("{player}", $player->getName(), $config->getNested("economy.message.new-player")));
        }
    }

}
