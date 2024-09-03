<?php

namespace zephy\daily\manager;

use pocketmine\item\Item;
use pocketmine\utils\SingletonTrait;
use zephy\daily\storage\StorageManager;

class DailyFactory
{
    use SingletonTrait {
        setInstance as private;
        reset as private;
    }

    private array $dailys = [];

    public StorageManager $storage;

    public function getDailys(): array
    {
        return $this->dailys;
    }

    public function getDaily(string $daily): ?Daily
    {
        return $this->dailys[$daily] ?? null;
    }

    public function addDaily(string $identifier, int $slot, Item $decorative, string $permission): void
    {
        $this->dailys[$identifier] = new Daily($identifier, $decorative, $permission, $slot);
        $this->dailys[$identifier]->setNeedSave(true);
    }

    public function destroy(string $identifier): void
    {
        if (!isset($this->dailys[$identifier])) {
            return;
        }
        unset($this->dailys[$identifier]);
        $this->getStorage()->saveAll();
    }

    public function getStorage(): StorageManager
    {
        if ($this->storage === null) {
            $this->storage = new StorageManager();
        }
        return $this->storage;
    }

}
