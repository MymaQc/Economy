<?php

namespace economy\commands;

use economy\librairies\commando\BaseCommand;
use economy\Main;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;

final class MyMoneyCommand extends BaseCommand {

    /* @var Config */
    public Config $config;

    /* CONSTRUCT */
    public function __construct() {
        $this->config = Main::getInstance()->getConfig();
        parent::__construct(
            Main::getInstance(),
            $this->config->getNested("economy.commands.mymoney.name") ?? "mymoney",
            $this->config->getNested("economy.commands.mymoney.description") ?? "Voir son nombre de monnaie",
            $this->config->getAll("economy.commands.mymoney.aliases") ?? []
        );
    }

    /* @return void */
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
            if (Main::getInstance()->getProvider()->exist($sender)) {
                $sender->sendMessage(str_replace("{money}", Main::getInstance()->getProvider()->get($sender), $this->config->getNested("economy.message.mymoney-success")));
            } else {
                $sender->sendMessage($this->config->getNested("economy.message.self-not-exist"));
            }
        } else {
            $sender->sendMessage($this->config->getNested("economy.message.not-player"));
        }
    }

}
