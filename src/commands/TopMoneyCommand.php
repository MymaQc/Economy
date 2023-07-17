<?php

namespace economy\commands;

use economy\librairies\commando\BaseCommand;
use economy\Economy;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\utils\Config;

final class TopMoneyCommand extends BaseCommand {

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
            $this->config->getNested("economy.commands.topmoney.name") ?? "topmoney",
            $this->config->getNested("economy.commands.topmoney-description") ?? "Voir la liste des joueurs les plus riches",
            $this->config->getNested("economy.commands.topmoney-aliases") ?? []
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
            $i = 1;
            $sender->sendMessage($this->config->getNested("economy.message.topmoney-header"));
            foreach (Economy::getInstance()->getProvider()->getAll(true) as $player => $money) {
                if ($i !== 11) {
                    $sender->sendMessage(str_replace(["{position}", "{player}", "{money}"], [$i, $player, $money], $this->config->getNested("economy.message.topmoney-format")));
                    $i++;
                } else break;
            }
        } else {
            $sender->sendMessage($this->config->getNested("economy.message.not-player"));
        }
    }

}
