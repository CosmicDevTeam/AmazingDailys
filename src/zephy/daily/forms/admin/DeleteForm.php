<?php

namespace zephy\daily\forms\admin;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use zephy\daily\manager\Daily;
use zephy\daily\manager\DailyFactory;
use zephy\daily\permissions\PermissionRegister;
use zephy\daily\utils\TextUtils;

class DeleteForm extends SimpleForm
{
    public function __construct()
    {
        parent::__construct(function (Player $player, $data = null) {

            if (is_null($data)) {
                return;
            }

            if (DailyFactory::getInstance()->getDaily($data) !== null){
                $daily = DailyFactory::getInstance()->getDaily($data);
                PermissionRegister::destroy($daily->getPermission());
               
                DailyFactory::getInstance()->destroy($data);
                $player->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("daily-deleted-succesfully"), [
                    "{DAILY}" => $data
                ]));
                return;
            }
            
            $player->sendMessage(TextUtils::formatMessage(TextUtils::getMessages()->get("daily-not-exists"), [
                "{DAILY}" => $data
            ]));
            return;
            
        });
        $this->setTitle("Dailys Remove");
        foreach (DailyFactory::getInstance()->getDailys() as $daily) {
           $this->addButton("§g" . $daily->getName() . "\n§7Click para borrar", -1, " ", $daily->getName());
        }
    }
}
