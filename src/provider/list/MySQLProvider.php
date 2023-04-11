<?php

namespace economy\provider\list;

use economy\Main;
use economy\provider\Provider;
use mysqli;
use pocketmine\player\Player;

final class MySQLProvider implements Provider {

    /* @var MySQLi */
    private MySQLi $provider;

    /* @var int */
    private int $host;

    /* @var int */
    private int $port;

    /* @var string */
    private string $user;

    /* @var string */
    private string $password;

    /* @var string */
    private string $database;

    /* COSNTRUCT */
    public function __construct() {
        $config = Main::getInstance()->getConfig();
        $this->host = $config->getNested("economy.mysql.host");
        $this->port = intval($config->getNested("economy.mysql.port"));
        $this->user = $config->getNested("economy.mysql.user");
        $this->password = $config->getNested("economy.mysql.password");
        $this->database = $config->getNested("economy.mysql.database");
        $this->provider = new MySQLi(
            $this->host ?? "127.0.0.1",
            $this->user ?? "myma",
            $this->password ?? "economy_1234",
            $this->database ?? "economy",
            $this->port ?? 3306
        );
        if (!$this->provider->connect_error) {
            $this->init();
        }
    }

    /* @return void */
    public function init(): void {
        $this->provider->query("CREATE TABLE IF NOT EXISTS user_money(name VARCHAR(20) PRIMARY KEY, money FLOAT)");
        $this->provider->close();
    }

    /**
     * @param string|Player $player
     * @return bool
     */
    public function exist(string|Player $player): bool {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        $result = $this->provider->query("SELECT * FROM user_money WHERE username='" . $this->provider->real_escape_string($player) . "'");
        $this->provider->close();
        return $result->num_rows > 0;
    }

    /**
     * @param string|Player $player
     * @param int $defaultMoney
     * @return void
     */
    public function create(string|Player $player, int $defaultMoney): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        if (!$this->exist($player)) {
            $this->provider->query("INSERT INTO user_money (username, money) VALUES ('" . $this->provider->real_escape_string($player) . "', $defaultMoney);");
            $this->provider->close();
        }
    }

    /**
     * @param string|Player $player
     * @return void
     */
    public function remove(string|Player $player): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        if ($this->exist($player)) {
            $this->provider->query("DELETE FROM user_money WHERE username='" . $this->provider->real_escape_string($player) . "'");
            $this->provider->close();
        }
    }

    /**
     * @param string|Player $player
     * @return int
     */
    public function get(string|Player $player): int {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        $query = $this->provider->query("SELECT money FROM user_money WHERE username='" . $this->provider->real_escape_string($player) . "'");
        $result = $query->fetch_array()[0] ?? false;
        $query->free();
        $this->provider->close();
        return intval($result);

    }

    /**
     * @param string|Player $player
     * @param int $amount
     * @return void
     */
    public function set(string|Player $player, int $amount): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        $this->provider->query("UPDATE user_money SET money = $amount WHERE username='" . $this->provider->real_escape_string($player) . "'");
        $this->provider->close();
    }

    /**
     * @param string|Player $player
     * @param int $amount
     * @return void
     */
    public function add(string|Player $player, int $amount): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        $this->provider->query("UPDATE user_money SET money = money + $amount WHERE username='" . $this->provider->real_escape_string($player) . "'");
        $this->provider->close();
    }

    /**
     * @param string|Player $player
     * @param int $amount
     * @return void
     */
    public function reduce(string|Player $player, int $amount): void {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        $this->provider->query("UPDATE user_money SET money = money - $amount WHERE username='" . $this->provider->real_escape_string($player) . "'");
        $this->provider->close();
    }

    /**
     * @param bool $top
     * @return array
     */
    public function getAll(bool $top = false): array {
        $data = [];
        $result = $this->provider->query("SELECT * FROM user_money");
        foreach ($result->fetch_all() as $value) {
            $data[$value[0]] = $value[1];
        }
        if ($top) {
            arsort($data);
        }
        $result->free();
        return $data;
    }

}
