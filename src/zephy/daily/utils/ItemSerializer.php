<?php

namespace zephy\daily\utils;

use Exception;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use RuntimeException;

class ItemSerializer
{

    const NULL_ITEM = "undefined";

    public static function encodeItem(Item $item): string
    {
        return $item->isNull() ? self::NULL_ITEM : self::handleSerealizeItem($item);
    }

    public static function decodeItem(string $data): ?Item
    {
        return $data === self::NULL_ITEM ? VanillaItems::AIR() : self::handleDeserializeItem($data);
    }

    public static function handleSerealizeItem(Item $item): string
    {
        $serializer = new LittleEndianNbtSerializer();
        $dataItem   = $serializer->write(new TreeRoot($item->nbtSerialize()));

        return base64_encode($dataItem);
    }

    public static function handleDeserializeItem(string $data): ?Item 
    {
        $serializer = new LittleEndianNbtSerializer();
        $dataItem   = $serializer->read(base64_decode($data))->mustGetCompoundTag();

        try {
            $item = Item::nbtDeserialize($dataItem);
        } catch (SavedDataLoadingException | Exception $error) {
            throw new RuntimeException("Error during decoing of an item, incorrect item: " . $error->getMessage() . ", data " . $data);
            return null;
        }
        return $item;
    }

}
