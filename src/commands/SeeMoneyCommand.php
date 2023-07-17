<?php

namespace economy\commands;

use economy\librairies\commando\args\RawStringArgument;
use economy\librairies\commando\args\TargetArgument;
use economy\librairies\commando\BaseCommand;
use economy\librairies\commando\exception\ArgumentOrderException;
use economy\Economy;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

final class SeeMoneyCommand extends BaseCommand {

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
            $this->config->getNested("economy.commands.seemoney.name") ?? "seemoney",
            $this->config->getNested("economy.commands.seemoney-description") ?? "Voir la monnaie d'un joueur",
            $this->config->getNested("economy.commands.seemoney-aliases") ?? ["money"]
        );
    }

    /**
     * @return void
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
        $this->registerArgument(0, new TargetArgument("player", true));
        $this->registerArgument(0, new RawStringArgument("player", true));
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if ($sender instanceof Player) {
            if (isset($args["player"])) {
                $player = ($target = Server::getInstance()->getPlayerByPrefix($args["player"])) instanceof Player ? $target->getName() : $args["player"];
                if (Economy::getInstance()->getProvider()->exist($player)) {
                    $sender->sendMessage(str_replace(["{player}", "{money}"], [$player, Economy::getInstance()->getProvider()->get($player)], $this->config->getNested("economy.message.seemoney-success")));
                } else {
                    $sender->sendMessage(str_replace("{player}", $player, $this->config->getNested("economy.message.player-not-exist")));
                }
            } else {
                if (Economy::getInstance()->getProvider()->exist($sender)) {
                    $sender->sendMessage(str_replace("{money}", Economy::getInstance()->getProvider()->get($sender), $this->config->getNested("economy.message.mymoney-success")));
                } else {
                    $sender->sendMessage($this->config->getNested("economy.message.self-not-exist"));
                }
            }
        } else {
            $sender->sendMessage($this->config->getNested("economy.message.not-player"));
        }
    }

}
