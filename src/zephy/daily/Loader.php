<?php

namespace zephy\daily;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use zephy\daily\commands\DailyCommand;
use zephy\daily\commands\DecorativeCommand;
use zephy\daily\manager\DailyFactory;

class Loader extends PluginBase
{
    use SingletonTrait {
        setInstance as private;
        reset as private;
    }

    protected function onEnable(): void
    {
        self::setInstance($this);
        $this->saveResource("messages.yml");
        $this->getServer()->getCommandMap()->registerAll(
            "Daily",
            [
                new DailyCommand(),
                new DecorativeCommand()
            ]
        );
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        DailyFactory::getInstance()->loadAll();
    }

    protected function onDisable(): void
    {
        DailyFactory::getInstance()->saveAll();
    }
}