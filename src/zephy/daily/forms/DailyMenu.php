<?php

namespace zephy\daily\forms;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\player\Player;
use zephy\daily\manager\DailyFactory;
use zephy\daily\utils\SoundUtils;
use zephy\daily\utils\TextUtils;
use zephy\daily\utils\TimeUtils;

final class DailyMenu
{
    public static function send(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);

        foreach (DailyFactory::getInstance()->getDailys() as $identifier => $daily) {

            $item = $daily->getDecorativeItem();

            $item->getNamedTag()->setString("identifier", $identifier);
            $item->getNamedTag()->setString("permission", $daily->getPermission());

            $menu->getInventory()->setItem($daily->getSlot(), $item);
        }

        $menu->setListener(fn(InvMenuTransaction $trans): InvMenuTransactionResult => self::transaction($trans));
        $menu->send($player, "Dailys");
    }

    public static function transaction(InvMenuTransaction $transaction): InvMenuTransactionResult
    {
        $item = $transaction->getItemClicked();
        $player = $transaction->getPlayer();

        if($item->getNamedTag()->getTag("permission") === null or $item->getNamedTag()->getTag("identifier") === null) return $transaction->discard();

        $permission = $item->getNamedTag()->getString("permission");
        $daily = DailyFactory::getInstance()->getDaily($item->getNamedTag()->getString("identifier"));

        if (!is_null($daily)) {
            if ($player->hasPermission($permission)) {
                if (!$daily->getCooldown()->inCooldown($player->getName())) {
                    if (empty($daily->getRewards())) {
                        $player->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("daily-dont-items"), [
                            "{DAILY}" => $item->getNamedTag()->getString("identifier")
                        ]));
                        SoundUtils::playSound($player, TextUtils::getMessages()->get("sound-error"));
                        $player->removeCurrentWindow();
                        return $transaction->discard();
                    }

                    foreach ($daily->getRewards() as $reward) {
                        if ($player->getInventory()->canAddItem($reward)) {
                            $player->getInventory()->addItem($reward);
                        } else {
                            $player->getWorld()->dropItem($player->getPosition(), $reward);
                        }
                    }
                    $player->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("daily-opened-succesfully"), [
                        "{DAILY}" => $item->getNamedTag()->getString("identifier")
                    ]));

                    $daily->getCooldown()->addCooldown($player->getName(), 86400);
                    SoundUtils::playSound($player, TextUtils::getMessages()->get("sound-succesfully"));
                    $player->removeCurrentWindow();
                    return $transaction->discard();
                } else {
                    $player->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("daily-cooldown"), [
                        "{COOLDOWN}" => TimeUtils::stringRoundTime($daily->getCooldown()->getCooldown($player->getName()))
                    ]));
                    SoundUtils::playSound($player, TextUtils::getMessages()->get("sound-error"));
                    $player->removeCurrentWindow();
                    return $transaction->discard();
                }
            } else {
                $player->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("daily-no-permission"), [
                    "{PERMISSION}" => $permission
                ]));
                SoundUtils::playSound($player, TextUtils::getMessages()->get("sound-error"));
                $player->removeCurrentWindow();
                return $transaction->discard();
            }
        }
        return $transaction->discard();
    }
}