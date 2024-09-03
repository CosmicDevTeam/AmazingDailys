<?php

namespace zephy\daily\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use zephy\daily\utils\PermissionUtils;
use zephy\daily\Loader;
use zephy\daily\utils\TextUtils;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;

class DecorativeCommand extends Command implements PluginOwned
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
            $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("decorative-error-arguments")));
            return;
        }

        if ($args[0] === "rename" || $args[0] === "customname") {
            $this->handleRename($sender, $args[1]);
            return;
        }

        if ($args[0] === "lore") {
            $this->handleLore($sender, $args[1]);
            return;
        }
    }

    public function handleRename(Player $sender, string $name): void
    {
        if ($sender->getInventory()->getItemInHand()->isNull()) {
            $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("invalid-item-hand")));
            return;
        }

        $item = clone $sender->getInventory()->getItemInHand();
        $item->setCustomName(TextFormat::colorize($name));

        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("decorative-renamed-succesfully"), [
            "{NAME}" => $name
        ]));
        return;
    }

    public function handleLore(Player $sender, string $name): void
    {
        if ($sender->getInventory()->getItemInHand()->isNull()) {
            $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("invalid-item-hand")));
            return;
        }

        $item = clone $sender->getInventory()->getItemInHand();
        $item->setLore([TextFormat::colorize($name)]);

        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("decorative-lore-succesfully"), [
            "{NAME}" => $name
        ]));
        return;
    }
    
    public function getOwningPlugin(): Plugin
    {
        return Loader::getInstance();
    }

}
