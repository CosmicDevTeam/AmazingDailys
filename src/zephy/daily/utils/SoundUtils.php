<?php

namespace zephy\daily\utils;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;

final class SoundUtils {
    public static function playSound(Player $player, string $sound): void {
        $pk = new PlaySoundPacket;
        $pk->soundName = $sound;
        $pk->x = $player->getPosition()->getX();
        $pk->z = $player->getPosition()->getZ();
        $pk->y = $player->getPosition()->getY();
        $pk->volume = 1.0;
        $pk->pitch = 1.0;
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}