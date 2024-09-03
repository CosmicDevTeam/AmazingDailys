<?php

namespace zephy\daily\manager;

use pocketmine\item\Item;
use zephy\daily\cooldown\Cooldown;
use zephy\daily\permissions\PermissionRegister;

final class Daily
{
    private Cooldown $cooldown;
    private bool $needSave = false;

    public function __construct(
        private string $name,
        private Item $decorative,
        private string $permission,
        private int $slot,
        private array $items = [],
        private array $cooldowns = []
    ) {
        $this->cooldown = new Cooldown();
        PermissionRegister::register($permission);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDecorativeItem(): Item
    {
        return $this->decorative;
    }

    public function getPermission(): string
    {
        return $this->permission;
    }

    public function getSlot(): int
    {
        return $this->slot;
    }

    public function setSlot(int $slot): void
    {
        $this->slot = $slot;
    }

    public function getRewards(): array
    {
        return $this->items;
    }

    public function setRewards(array $rewards): void
    {
        $this->items = $rewards;
        $this->setNeedSave(true);
    }

    public function getCooldown(): Cooldown
    {
        return $this->cooldown;
    }

    public function setNeedSave(bool $needSave): void
    {
        $this->needSave = $needSave;
    }

    public function isNeedSave(): bool
    {
        return $this->needSave;
    }

}