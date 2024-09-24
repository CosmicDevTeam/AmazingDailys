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

        switch ($args[0]) {
            case "create":
                if ($sender->hasPermission(PermissionUtils::ADMIN)) {
                    $sender->sendForm(new CreatorForm());
                    return;
                }
                break;
            case "delete":
                if ($sender->hasPermission(PermissionUtils::ADMIN)) {
                    $sender->sendForm(new DeleteForm());
                    return;
                }
                break;
            case "setitems":
            case "rewards":
                if ($sender->hasPermission(PermissionUtils::ADMIN)) {
                    if (!isset($args[1])) {
                        $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("error-items-arguments")));
                        return;
                    }

                    if (is_null(DailyFactory::getInstance()->getDaily($args[1]))) {
                        $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("daily-not-exists"), [
                            "{DAILY}" => $args[1]
                        ]));
                        return;
                    }

                    $daily = DailyFactory::getInstance()->getDaily($args[1]);
                    ItemsMenu::send($sender, $daily);
                    break;
                }
        }
    }
    public function getOwningPlugin() : Plugin{
        return Loader::getInstance();
    } 
}
