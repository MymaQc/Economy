<?php

namespace economy\api;

use economy\Economy;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

final class EconomyApi {

    use SingletonTrait;

    /**
     * @param Player|string $player
     * @return bool
     */
    public function hasMoney(Player|string $player): bool {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        return Economy::getInstance()->getProvider()->exist($player);
    }

    /**
     * @param Player|string $player
     * @return int
     */
    public function getMoney(Player|string $player): int {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        return Economy::getInstance()->getProvider()->get($player);
    }

    /**
     * @param Player|string $player
     * @param int $moneyToSet
     * @return void
     */
    public function setMoney(Player|string $player, int $moneyToSet): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        Economy::getInstance()->getProvider()->set($player, $moneyToSet);
    }

    /**
     * @param Player|string $player
     * @param int $moneyToAdd
     * @return void
     */
    public function addMoney(Player|string $player, int $moneyToAdd): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        Economy::getInstance()->getProvider()->add($player, $moneyToAdd);
    }

    /**
     * @param Player|string $player
     * @param int $moneyToReduce
     * @return void
     */
    public function reduceMoney(Player|string $player, int $moneyToReduce): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        Economy::getInstance()->getProvider()->reduce($player, $moneyToReduce);
    }

}
