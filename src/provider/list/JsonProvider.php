<?php

namespace economy\provider\list;

use economy\Main;
use economy\provider\Provider;
use JsonException;
use pocketmine\player\Player;
use pocketmine\utils\Config;

final class JsonProvider implements Provider {

    /* @var Config */
    private Config $provider;

    /* COSNTRUCT */
    public function __construct() {
        $this->provider = new Config(Main::getInstance()->getDataFolder() . "Money.json", Config::JSON);
        $this->init();
    }

    /* @return void */
    public function init(): void {
        if (!is_file(Main::getInstance()->getDataFolder() . "Money.json")) {
            fopen(Main::getInstance()->getDataFolder() . "Money.json", "w");
        }
    }

    /**
     * @param string|Player $player
     * @return bool
     */
    public function exist(string|Player $player): bool {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        return $this->provider->exists($player);
    }

    /**
     * @param string|Player $player
     * @param int $defaultMoney
     * @return void
     * @throws JsonException
     */
    public function create(string|Player $player, int $defaultMoney): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        if (!$this->exist($player)) {
            $this->provider->set($player, $defaultMoney);
            $this->provider->save();
        }
    }

    /**
     * @param string|Player $player
     * @return void
     * @throws JsonException
     */
    public function remove(string|Player $player): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        if ($this->exist($player)) {
            $this->provider->remove($player);
            $this->provider->save();
        }
    }

    /**
     * @param string|Player $player
     * @return int
     */
    public function get(string|Player $player): int {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        return intval($this->provider->get($player));
    }

    /**
     * @param string|Player $player
     * @param int $amount
     * @throws JsonException
     */
    public function set(string|Player $player, int $amount): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        $this->provider->set($player, $amount);
        $this->provider->save();
    }

    /**
     * @param string|Player $player
     * @param int $amount
     * @return void
     * @throws JsonException
     */
    public function add(string|Player $player, int $amount): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        $this->set($player, $this->get($player) + $amount);
        $this->provider->save();
    }

    /**
     * @param string|Player $player
     * @param int $amount
     * @return void
     * @throws JsonException
     */
    public function reduce(string|Player $player, int $amount): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        $this->set($player, $this->get($player) - $amount);
        $this->provider->save();
    }

    /**
     * @param bool $top
     * @return array
     */
    public function getAll(bool $top = false): array {
        $data = [];
        $result = $this->provider->getAll();
        foreach ($result as $player => $money) {
            $data[$player] = $money;
        }
        if ($top) {
            arsort($data);
        }
        return $data;
    }

}
