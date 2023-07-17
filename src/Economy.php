<?php

namespace economy;

use economy\api\EconomyApi;
use economy\commands\AddMoneyCommand;
use economy\commands\MyMoneyCommand;
use economy\commands\PayCommand;
use economy\commands\ReduceMoneyCommand;
use economy\commands\SeeMoneyCommand;
use economy\commands\SetMoneyCommand;
use economy\commands\TopMoneyCommand;
use economy\commands\TopMoneyFloatingTextCommand;
use economy\entity\TopMoneyFloatingTextEntity;
use economy\librairies\commando\exception\HookAlreadyRegistered;
use economy\librairies\commando\PacketHooker;
use economy\listener\EconomyListener;
use economy\provider\list\JsonProvider;
use economy\provider\Provider;
use JsonException;
use MySQLi;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

final class Economy extends PluginBase {

    use SingletonTrait;

    /**
     * @var Provider
     */
    private Provider $provider;

    /**
     * @return void
     */
    protected function onLoad(): void {
        self::setInstance($this);
        $this->saveDefaultConfig();
    }

    /**
     * @return void
     * @throws HookAlreadyRegistered
     */
    protected function onEnable(): void {
        $config = $this->getConfig();

        $this->getServer()->getPluginManager()->registerEvents(new EconomyListener(), $this);

        EntityFactory::getInstance()->register(TopMoneyFloatingTextEntity::class, function (World $world, CompoundTag $nbt): TopMoneyFloatingTextEntity {
            return new TopMoneyFloatingTextEntity(EntityDataHelper::parseLocation($nbt, $world), null);
        }, ['TopMoneyFloatingTextEntity']);

        $commands = [
            new AddMoneyCommand(),
            new MyMoneyCommand(),
            new PayCommand(),
            new ReduceMoneyCommand(),
            new SeeMoneyCommand(),
            new SetMoneyCommand(),
            new TopMoneyCommand(),
            new TopMoneyFloatingTextCommand(),
        ];
        foreach ($commands as $command) {
            foreach ($this->getServer()->getCommandMap()->getCommands() as $newCommand) {
                if ($command->getName() === $newCommand->getName()) {
                    $this->getServer()->getCommandMap()->unregister($newCommand);
                }
            }
            $this->getServer()->getCommandMap()->register($command->getName(), $command);
        }

        $opRoot = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR);

        foreach ([
            $config->getNested("economy.commands.addmoney.permission"),
            $config->getNested("economy.commands.reducemoney.permission"),
            $config->getNested("economy.commands.setmoney.permission"),
            $config->getNested("economy.commands.topmoneyfloatingtext.permission")
        ] as $key) {
            PermissionManager::getInstance()->addPermission(new Permission($key));
            $opRoot->addChild($key, true);
        }

        switch ($config->getNested("economy.settings.type")) {
            case "json":
                $this->provider = new JsonProvider();
                break;
            case "mysql":
                // $this->provider = new MySQLProvider();
                break;
            default:
                $this->getLogger()->critical($this->getConfig()->getNested("economy.message.invalid-type"));
        }

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this->getInstance());
        }

        $this->getLogger()->notice($config->getNested("economy.message.enable-plugin"));
    }

    /**
     * @return void
     * @throws JsonException
     */
    protected function onDisable(): void {
        if ($this->provider instanceof MySQLi) {
            $this->provider->close();
        } else if ($this->provider instanceof Config) {
            $this->provider->save();
        }
        $this->getLogger()->notice($this->getConfig()->getNested("economy.message.disable-plugin"));
    }

    /* @return Provider */
    public function getProvider(): Provider {
        return $this->provider;
    }

    /**
     * @return EconomyApi
     */
    public function getApi(): EconomyApi {
        return EconomyApi::getInstance();
    }

}
