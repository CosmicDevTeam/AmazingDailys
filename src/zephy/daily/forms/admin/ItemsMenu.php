<?php

namespace zephy\daily\forms\admin;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use zephy\daily\manager\Daily;
use zephy\daily\utils\TextUtils;

class ItemsMenu
{
    public static function send(Player $player, Daily $daily): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->getInventory()->setContents($daily->getRewards());

        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($daily) {
            $daily->setRewards($inventory->getContents());
            $player->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("items-placed-succesfully"), [
                "{DAILY}" => $daily->getName()
            ]));
        });

        $menu->send($player, TextFormat::colorize("&gInventory Contents"));
    }
}