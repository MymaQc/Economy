<?php

namespace economy\commands;

use economy\librairies\commando\BaseCommand;
use economy\Economy;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\utils\Config;

final class MyMoneyCommand extends BaseCommand {

    /**
     * @var Config
     */
    public Config $config;

    /**
     * CONSTRUCT
     */
    public function __construct() {
        $this->config = Economy::getInstance()->getConfig();
        $this->setPermission(DefaultPermissions::ROOT_USER);
        parent::__construct(
            Economy::getInstance(),
            $this->config->getNested("economy.commands.mymoney.name") ?? "mymoney",
            $this->config->getNested("economy.commands.mymoney.description") ?? "Voir son nombre de monnaie",
            $this->config->getAll("economy.commands.mymoney.aliases") ?? []
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
            if (Economy::getInstance()->getProvider()->exist($sender)) {
                $sender->sendMessage(str_replace("{money}", Economy::getInstance()->getProvider()->get($sender), $this->config->getNested("economy.message.mymoney-success")));
            } else {
                $sender->sendMessage($this->config->getNested("economy.message.self-not-exist"));
            }
        } else {
            $sender->sendMessage($this->config->getNested("economy.message.not-player"));
        }
    }

}
