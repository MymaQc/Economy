<?php

namespace economy\api;

use economy\Economy;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

final class EconomyApi {

    use SingletonTrait;

    /**
     * @param Player $player
     * @return bool
     */
    public function hasMoney(Player $player): bool {
        return Economy::getInstance()->getProvider()->exist($player);
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getMoney(Player $player): int {
        return Economy::getInstance()->getProvider()->get($player);
    }

    /**
     * @param Player $player
     * @param int $moneyToSet
     * @return void
     */
    public function setMoney(Player $player, int $moneyToSet): void {
        Economy::getInstance()->getProvider()->set($player, $moneyToSet);
    }

    /**
     * @param Player $player
     * @param int $moneyToAdd
     * @return void
     */
    public function addMoney(Player $player, int $moneyToAdd): void {
        Economy::getInstance()->getProvider()->add($player, $moneyToAdd);
    }

    /**
     * @param Player $player
     * @param int $moneyToReduce
     * @return void
     */
    public function reduceMoney(Player $player, int $moneyToReduce): void {
        Economy::getInstance()->getProvider()->reduce($player, $moneyToReduce);
    }

}
