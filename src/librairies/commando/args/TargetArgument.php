<?php

namespace economy\librairies\commando\args;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class TargetArgument extends BaseArgument {

    public function getNetworkType(): int {
        return AvailableCommandsPacket::ARG_TYPE_TARGET;
    }

    public function getTypeName(): string {
        return "target";
    }

    public function canParse(string $testString, CommandSender $sender): bool {
        return (bool) preg_match("/^(?!rcon|console)[a-zA-Z0-9_ ]{1,16}$/i", $testString);
    }

    public function parse(string $argument, CommandSender $sender): string {
        return strtolower($argument);
    }

}
