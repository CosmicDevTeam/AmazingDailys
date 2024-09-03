<?php

namespace zephy\daily\manager;

use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use zephy\daily\Loader;
use zephy\daily\utils\ItemSerializer;

class DailyFactory
{
    use SingletonTrait {
        setInstance as private;
        reset as private;
    }

    private array $dailys = [];

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
    }

    public function destroy(string $identifier): void
    {
        unset($this->dailys[array_search($identifier, $this->dailys)]);
    }

    public function saveAll(): void
    {
        $config = new Config(Loader::getInstance()->getDataFolder() . "dailys.json", Config::JSON);

        foreach ($this->getDailys() as $daily) {
            $rewards = [];
            foreach ($daily->getRewards() as $reward) {
                $rewards[] = ItemSerializer::encodeItem($reward);
            }
            $cooldowns = [];
            foreach ($daily->getCooldown()->getCooldowns() as $player => $cooldown) {
                $cooldowns[$player] = $daily->getCooldown()->getCooldown($player);
           } 

            $config->set($daily->getName(), [
                "rewards" => $rewards,
                "decorative" => ItemSerializer::encodeItem($daily->getDecorativeItem()),
                "slot" => $daily->getSlot(),
                "cooldowns" => $cooldowns,
                "permission" => $daily->getPermission()
            ]);
            
        }
        $config->save();
    }
    public function loadAll(): void
    {
        $config = new Config(Loader::getInstance()->getDataFolder() . "dailys.json", Config::JSON);

        foreach ($config->getAll() as $identifier => $data) {

            $rewards = [];
            foreach ($data["rewards"] as $item) {
                $rewards[] = ItemSerializer::decodeItem($item);
            }


            $this->addDaily($identifier, $data["slot"], ItemSerializer::decodeItem($data["decorative"]), $data["permission"]);
            $daily = $this->getDaily($identifier);
            $daily->setRewards($rewards);
            foreach ($data["cooldowns"] as $player => $time) {
                $daily->getCooldown()->addCooldown($player, $time);
            }
        }
    }
}
