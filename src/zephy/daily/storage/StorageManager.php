<?php

namespace zephy\daily\storage;

use pocketmine\utils\Config;
use zephy\daily\Loader;
use zephy\daily\manager\Daily;
use zephy\daily\manager\DailyFactory;
use zephy\daily\utils\ItemSerializer;

final class StorageManager
{

    public array $existingData;
    public Config $config;

    public function __construct()
    {
        $this->config = new Config(Loader::getInstance()->getDataFolder() . "dailys.json", Config::JSON);
        $this->existingData = $this->config->getAll();
    }

    public function handleRewards(array $rewards): array
    {
        return array_map(fn($item) => ItemSerializer::decodeItem($item), $rewards);
    }

    public function findAll(): void
    {
        foreach ($this->existingData as $identifier => $data) {
            DailyFactory::getInstance()->addDaily($identifier, $data["slot"], ItemSerializer::decodeItem($data["decorative"]), $data["permission"]);

            $daily = DailyFactory::getInstance()->getDaily($identifier);
            $daily->setRewards($this->handleRewards($data["rewards"]));

            array_walk($data["cooldowns"], function ($time, $player) use ($daily) {
                $daily->getCooldown()->addCooldown($player, $time);
            });

            $daily->setNeedSave(false);
        }
    }

    public function saveAll(): void
    {
        foreach (DailyFactory::getInstance()->getDailys() as $identifier => $daily) {
            if (!$daily->isNeedSave()) {
                continue;
            }

            foreach ($daily->getRewards() as $reward) {
                $rewards[] = ItemSerializer::encodeItem($reward);
            }

            $cooldowns = [];

            foreach ($daily->getCooldown()->getCooldowns() as $player => $cooldown) {
                $cooldowns[$player] = $daily->getCooldown()->getCooldown($player);
            }

            $this->existingData[$identifier] = [
                "rewards" => $rewards,
                "decorative" => ItemSerializer::encodeItem($daily->getDecorativeItem()),
                "slot" => $daily->getSlot(),
                "cooldowns" => $cooldowns,
                "permission" => $daily->getPermission()
            ];
        }
        $this->config->setAll($this->existingData);
        $this->config->save();
    }
}
