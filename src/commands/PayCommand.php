<?php

namespace economy\commands;

use economy\librairies\commando\args\IntegerArgument;
use economy\librairies\commando\args\TargetArgument;
use economy\librairies\commando\BaseCommand;
use economy\librairies\commando\exception\ArgumentOrderException;
use economy\Economy;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

final class PayCommand extends BaseCommand {

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
            $this->config->getNested("economy.commands.pay.name") ?? "pay",
            $this->config->getNested("economy.commands.pay.description") ?? "Envoyer de la monnaie à d'autres joueurs",
            $this->config->getAll("economy.commands.pay.aliases") ?? []
        );
    }

    /**
     * @return void
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
        $this->registerArgument(0, new TargetArgument("player"));
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
            if (isset($args["player"], $args["amount"])) {
                $target = Server::getInstance()->getPlayerByPrefix($args["player"]);
                if ($target instanceof Player) {
                    if (Economy::getInstance()->getProvider()->exist($target)) {
                        if (is_int($args["amount"])) {
                            if ($args["amount"] > 0) {
                                if ($sender->getName() !== $target->getName()) {
                                    if (Economy::getInstance()->getProvider()->get($sender) >= $args["amount"]) {
                                        Economy::getInstance()->getProvider()->add($target, $args["amount"]);
                                        Economy::getInstance()->getProvider()->reduce($sender, $args["amount"]);
                                        $sender->sendMessage(str_replace(["{money}", "{player}"], [$args["amount"], $target->getName()], $this->config->getNested("economy.message.pay-sender-success")));
                                        $target->sendMessage(str_replace(["{money}", "{player}"], [$args["amount"], $sender->getName()], $this->config->getNested("economy.message.pay-target-success")));
                                    } else {
                                        $sender->sendMessage(str_replace("{money}", $args["amount"], $this->config->getNested("economy.message.self-insufficient-amount")));
                                    }
                                } else {
                                    $sender->sendMessage($this->config->getNested("economy.message.target-sender-same-name"));
                                }
                            } else {
                                $sender->sendMessage($this->config->getNested("economy.message.out-of-range-value"));
                            }
                        } else {
                            $sender->sendMessage($this->config->getNested("economy.message.is-not-int-value"));
                        }
                    } else {
                        $sender->sendMessage(str_replace("{player}", $args["player"], $this->config->getNested("economy.message.player-not-exist")));
                    }
                } else {
                    $sender->sendMessage(str_replace("{player}", $args["player"], $this->config->getNested("economy.message.player-not-exist")));
                }
            } else {
                $sender->sendMessage($this->config->getNested("economy.commands.pay-usage"));
            }
        } else {
            $sender->sendMessage($this->config->getNested("economy.message.not-player"));
        }
    }

}
