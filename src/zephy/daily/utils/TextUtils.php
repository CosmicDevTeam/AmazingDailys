<?php

namespace zephy\daily\utils;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use zephy\daily\Loader;

class TextUtils
{

    public static function formatMessage(string $message, array $replace = [])
    {
        foreach ($replace as $key => $str_replace) {
            $message = str_replace($key, $str_replace, $message);
        }

        return TextFormat::colorize($message);
    }

    public static function getMessages(): Config
    {
        return new Config(Loader::getInstance()->getDataFolder() . "messages.yml");
    }

    public static function PREFIX(): string
    {
        return self::getMessages()->get("prefix");
    }
}