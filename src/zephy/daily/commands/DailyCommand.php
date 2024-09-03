<?php

namespace zephy\daily\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use zephy\daily\forms\admin\CreatorForm;
use zephy\daily\forms\admin\DeleteForm;
use zephy\daily\forms\admin\ItemsMenu;
use zephy\daily\forms\DailyMenu;
use zephy\daily\Loader;
use zephy\daily\manager\DailyFactory;
use zephy\daily\utils\PermissionUtils;
use zephy\daily\utils\TextUtils;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class DailyCommand extends Command implements PluginOwned
{

    public function __construct()
    {
        parent::__construct("daily", "Open daily rewards", null, ["rewards"]);
        $this->setPermission(PermissionUtils::DEFAULT);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            return;
        }

        if (!isset($args[0])) {
            DailyMenu::send($sender);
            return;
        }

        if (!$sender->hasPermission(PermissionUtils::ADMIN)) {
            $sender->sendMessage(TextFormat::colorize("&cYou don't have permission to use this command"));
            return;
        }

        if ($args[0] === "create") {
            $sender->sendForm(new CreatorForm());
            return;
        }

        if ($args[0] === "delete") {
            $sender->sendForm(new DeleteForm());
            return;
        }

        if ($args[0] === "setitems") {
            if (!isset($args[1])) {
                $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("error-items-arguments")));
                return;
            }
            $this->handleSetItems($sender, $args[1]);
            return;
        }
    }

    public function handleSetItems(Player $sender, string $identifier): void
    {
        if (is_null(DailyFactory::getInstance()->getDaily($identifier))) {
            $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("daily-not-exists"), [
                "{DAILY}" => $identifier
            ]));
            return;
        }
        ItemsMenu::send($sender, DailyFactory::getInstance()->getDaily($identifier));
        return;
    }

    public function getOwningPlugin(): Plugin
    {
        return Loader::getInstance();
    }
    
}
