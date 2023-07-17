<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace economy\entity;

use economy\Economy;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Hoe;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class TopMoneyFloatingTextEntity extends Entity {

    /**
     * @return EntitySizeInfo
     */
    #[Pure] protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(0.1, 0.1);
    }

    /**
     * @return string
     */
    public static function getNetworkTypeId(): string {
        return EntityIds::ARMOR_STAND;
    }

    /**
     * @return float
     */
    protected function getInitialDragMultiplier(): float {
        return 0.0;
    }

    /**
     * @return float
     */
    protected function getInitialGravity(): float {
        return 0.0;
    }

    /**
     * @param CompoundTag $nbt
     * @return void
     */
    protected function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);
        $this->setScale(0.001);
        $this->setNameTagAlwaysVisible();
        $this->setHealth(1);
        $this->setMaxHealth(1);
        $this->setCanSaveWithChunk(true);
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick): bool {
        $this->setNameTag("");
        $this->setNameTagAlwaysVisible($this->alwaysShowNameTag);
        $this->updateLeaderboard();
        return parent::onUpdate($currentTick);
    }

    /**
     * @param EntityDamageEvent $source
     * @return void
     */
    public function attack(EntityDamageEvent $source): void {
        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();
            if ($damager instanceof Player) {
                $item = $damager->getInventory()->getItemInHand();
                if ($item instanceof Hoe && $damager->getServer()->isOp($damager->getName())) {
                    $source->getEntity()->kill();
                    return;
                }
                $source->cancel();
            }
        }
    }

    /**
     * @return void
     */
    public function updateLeaderboard(): void {
        $i = 1;
        $content = Economy::getInstance()->getConfig()->getNested("economy.message.topmoney-header") . TextFormat::EOL;
        foreach (Economy::getInstance()->getProvider()->getAll(true) as $player => $money) {
            if ($i !== 11) {
                $content .= str_replace(["{position}", "{player}", "{money}"], [$i, $player, $money], Economy::getInstance()->getConfig()->getNested("economy.message.topmoney-format")) . TextFormat::EOL;
                $i++;
            } else break;
        }
        $this->setNameTag($content);
    }

}
