<?php

namespace economy\commands;

use economy\entity\TopMoneyFloatingTextEntity;
use economy\librairies\commando\BaseCommand;
use economy\Economy;
use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\utils\Config;

final class TopMoneyFloatingTextCommand extends BaseCommand {

    /**
     * @var Config
     */
    public Config $config;

    /**
     * CONSTRUCT
     */
    public function __construct() {
        $this->config = Economy::getInstance()->getConfig();
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
        parent::__construct(
            Economy::getInstance(),
            $this->config->getNested("economy.commands.topmoneyfloatingtext.name") ?? "floatingtext",
            $this->config->getNested("economy.commands.topmoneyfloatingtext-description") ?? "Faire apparaître le floatingtext TopMoney",
            $this->config->getNested("economy.commands.topmoneyfloatingtext-aliases") ?? []
        );
    }

    /**
     * @return void
     */
    protected function prepare(): void {
        // NOP
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if ($sender instanceof Player) {
            if ($sender->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
                $entity = new TopMoneyFloatingTextEntity(new Location($sender->getLocation()->getX(), $sender->getLocation()->getY() + 1, $sender->getLocation()->getZ(), $sender->getWorld(), 0, 0));
                $entity->spawnToAll();
            } else {
                $sender->sendMessage($this->config->getNested("economy.message.no-permission"));
            }
        } else {
            $sender->sendMessage($this->config->getNested("economy.message.not-player"));
        }
    }

}
