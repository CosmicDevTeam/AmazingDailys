<?php

namespace zephy\daily\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use zephy\daily\utils\PermissionUtils;
use zephy\daily\utils\TextUtils;

class DecorativeCommand extends Command
{
    public function __construct()
    {
        parent::__construct("decorate", "Decorate your item");
        $this->setPermission(PermissionUtils::ADMIN);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) return;

        if (!isset($args[0])) {
            return;
        }

        switch ($args[0]) {
            case "rename":
            case "customname":
                if (!isset($args[1])) {
                    $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("decorative-error-rename")));
                    return;
                }
                if ($sender->getInventory()->getItemInHand()->isNull()) {
                    $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("invalid-item-hand")));
                    return;
                }

                $item = clone $sender->getInventory()->getItemInHand();
                $item->setCustomName(TextFormat::colorize($args[1]));

                $sender->getInventory()->setItemInHand($item);
                $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("decorative-renamed-succesfully"), [
                    "{NAME}" => $args[1]
                ]));
                break;
            case "lore":
                if (!isset($args[1])) {
                    $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("decorative-error-lore")));
                    return;
                }
                if ($sender->getInventory()->getItemInHand()->isNull()) {
                    $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("invalid-item-hand")));
                    return;
                }

                $item = clone $sender->getInventory()->getItemInHand();
                $item->setLore([TextFormat::colorize($args[1])]);

                $sender->getInventory()->setItemInHand($item);
                $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("decorative-lore-succesfully"), [
                    "{NAME}" => $args[1]
                ]));
                break;
        }
    }
}
