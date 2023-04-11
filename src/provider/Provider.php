<?php

namespace economy\provider;

use pocketmine\player\Player;

interface Provider {

    /* @return void */
    public function init(): void;

    /**
     * @param string|Player $player
     * @return bool
     */
    public function exist(string|Player $player): bool;

    /**
     * @param string|Player $player
     * @param int $defaultMoney
     * @return void
     */
    public function create(string|Player $player, int $defaultMoney): void;

    /**
     * @param string|Player $player
     * @return void
     */
    public function remove(string|Player $player): void;

    /**
     * @param string|Player $player
     * @return int
     */
    public function get(string|Player $player): int;

    /**
     * @param string|Player $player
     * @param int $amount
     * @return void
     */
    public function set(string|Player $player, int $amount): void;

    /**
     * @param string|Player $player
     * @param int $amount
     * @return void
     */
    public function add(string|Player $player, int $amount): void;

    /**
     * @param string|Player $player
     * @param int $amount
     * @return void
     */
    public function reduce(string|Player $player, int $amount): void;

    /**
     * @param bool $top
     * @return array
     */
    public function getAll(bool $top = false): array;

}
