<?php

namespace zephy\daily\utils;

use Exception;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use RuntimeException;
use zephy\daily\Loader;

class ItemSerializer
{
    public static function encodeItem(Item $item): string
    {
        if ($item->isNull()) {
            return "null";
        }

        $serializer = new LittleEndianNbtSerializer();

        return base64_encode($serializer->write(new TreeRoot($item->nbtSerialize())));
    }

    public static function decodeItem(string $data): ?Item
    {
        if ($data === "null") {
            return VanillaItems::AIR();
        }

        $serializer = new LittleEndianNbtSerializer();

        try {
            $item = Item::nbtDeserialize($serializer->read(base64_decode($data))->mustGetCompoundTag());
        } catch (SavedDataLoadingException | Exception $error) {
            Loader::getInstance()->getLogger()->error("Error during decoing of an item, incorrect item: " . $error->getMessage() . ", data " . $data);
            return null;
        }
        return $item;
    }
}
