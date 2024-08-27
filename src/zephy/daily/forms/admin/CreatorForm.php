<?php

namespace zephy\daily\forms\admin;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use zephy\daily\manager\DailyFactory;
use zephy\daily\utils\TextUtils;

class CreatorForm extends CustomForm
{

    public function __construct()
    {
        parent::__construct(function (Player $player, ?array $data = null) {
            if (is_null($data)) {
                return;
            }

            if ($player->getInventory()->getItemInHand()->isNull()) {
                $player->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("invalid-item-hand")));
                return;
            }
            $item = clone $player->getInventory()->getItemInHand();

            if (count($data) < 3) {
                $player->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("create-error-arguments")));
                return;
            }

            if (!is_numeric($data[1])) {
                $player->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("slot-error-numeric")));
                return;
            }

            if (DailyFactory::getInstance()->getDaily($data[0]) !== null) {
                $player->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("daily-already-exists"), [
                    "{DAILY}" => $data[0]
                ]));
                return;
            }

            DailyFactory::getInstance()->addDaily($data[0], $data[1], $item, $data[2]);
            $player->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("daily-created-succesfully"), [
                "{DAILY}" => $data[0],
                "{PERMISSION}" => $data[2],
                "{SLOT}" => $data[1],
                "{PREFIX}" => TextUtils::PREFIX()

            ]));
        });

        $this->setTitle("Admin Creator");
        $this->addInput("Identifier");
        $this->addInput("Slot", "Decorative Item Slot");
        $this->addInput("Permission", "Deja vacio si no requiere", "daily.default");
    }
}
