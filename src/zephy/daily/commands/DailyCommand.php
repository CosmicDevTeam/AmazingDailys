<?php

namespace zephy\daily\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use zephy\daily\forms\admin\CreatorForm;
use zephy\daily\forms\admin\DeleteForm;
use zephy\daily\forms\admin\ItemsMenu;
use zephy\daily\forms\DailyMenu;
use zephy\daily\manager\DailyFactory;
use zephy\daily\utils\PermissionUtils;
use zephy\daily\utils\TextUtils;

class DailyCommand extends Command
{
    public function __construct()
    {
        parent::__construct("daily", "Open daily rewards", null, ["rewards"]);
        $this->setPermission(PermissionUtils::DEFAULT);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        if (!isset($args[0])) {
            DailyMenu::send($sender);
            return;
        }

        if($sender->hasPermission(PermissionUtils::ADMIN)){
            switch (strtolower($args[0])){
                case "create":
                    $sender->sendForm(new CreatorForm());
                    break;
                case "delete":
                    $sender->sendForm(new DeleteForm());
                    break;
                case "setitems":
                case "rewards":
                    if (!isset($args[1])) {
                        $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("error-items-arguments")));
                        return;
                    }
                    $daily = DailyFactory::getInstance()->getDaily($args[1]);
                    if (is_null($daily)) {
                        $sender->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("daily-not-exists"), [
                            "{DAILY}" => $args[1]
                        ]));
                        return;
                    }
                    ItemsMenu::send($sender, $daily);
                    break;
                default:
                    $sender->sendMessage(TextFormat::RED."Invalid params");
                    return;
            }
        }
    }
}