<?php

namespace economy\commands;

use economy\librairies\commando\args\IntegerArgument;
use economy\librairies\commando\args\RawStringArgument;
use economy\librairies\commando\args\TargetArgument;
use economy\librairies\commando\BaseCommand;
use economy\librairies\commando\exception\ArgumentOrderException;
use economy\Economy;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

final class AddMoneyCommand extends BaseCommand {

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
            $this->config->getNested("economy.commands.addmoney.name") ?? "addmoney",
            $this->config->getNested("economy.commands.addmoney.description") ?? "Ajouter de la monnaie à un joueur",
            $this->config->getNested("economy.commands.addmoney-aliases") ?? []
        );
    }

    /**
     * @return void
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
        PermissionManager::getInstance()->addPermission(new Permission(Economy::getInstance()->getConfig()->getNested("economy.commands.addmoney.permission") ?? "addmoney.use"));
        $this->registerArgument(0, new TargetArgument("player"));
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new IntegerArgument("amount"));
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
                if (isset($args["player"], $args["amount"])) {
                    $player = ($target = Server::getInstance()->getPlayerByPrefix($args["player"])) instanceof Player ? $target->getName() : $args["player"];
                    if (Economy::getInstance()->getProvider()->exist($player)) {
                        if (is_int($args["amount"])) {
                            if ($args["amount"] > 0) {
                                Economy::getInstance()->getProvider()->add($player, $args["amount"]);
                                $sender->sendMessage(str_replace(["{money}", "{player}"], [$args["amount"], $player], $this->config->getNested("economy.message.addmoney-sender-success")));
                                if ($player instanceof Player) {
                                    $player->sendMessage(str_replace(["{money}", "{player}"], [$args["amount"], $sender->getName()], $this->config->getNested("economy.message.addmoney-target-success")));
                                }
                            } else {
                                $sender->sendMessage($this->config->getNested("economy.message.out-of-range-value"));
                            }
                        } else {
                            $sender->sendMessage($this->config->getNested("economy.message.is-not-int-value"));
                        }
                    } else {
                        $sender->sendMessage(str_replace("{player}", $player, $this->config->getNested("economy.message.player-not-exist")));
                    }
                } else {
                    $sender->sendMessage($this->config->getNested("economy.commands.addmoney.usage"));
                }
            } else {
                $sender->sendMessage($this->config->getNested("economy.message.no-permission"));
            }
        } else {
            $sender->sendMessage($this->config->getNested("economy.message.not-player"));
        }
    }

}
