<?php

/***
 * @noinspection PhpUnused
 *    ___                                          _
 *   / __\___  _ __ ___  _ __ ___   __ _ _ __   __| | ___
 *  / /  / _ \| '_ ` _ \| '_ ` _ \ / _` | '_ \ / _` |/ _ \
 * / /__| (_) | | | | | | | | | | | (_| | | | | (_| | (_) |
 * \____/\___/|_| |_| |_|_| |_| |_|\__,_|_| |_|\__,_|\___/
 *
 * Commando - A Command Framework virion for PocketMine-MP
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * Written by @CortexPE <https://CortexPE.xyz>
 *
 */

namespace economy\librairies\commando\args;

use pocketmine\command\CommandSender;

class OptionArgument extends StringEnumArgument
{
    public function __construct(string $name, private array $enumValues, bool $optional = false)
    {
        parent::__construct($name, $optional);
    }

    public function getTypeName(): string
    {
        return "string";
    }

    public function parse(string $argument, CommandSender $sender): string
    {
        return $argument;
    }

    public function getEnumValues(): array
    {
        return $this->enumValues;
    }
}
