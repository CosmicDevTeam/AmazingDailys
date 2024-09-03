<?php

namespace zephy\daily\utils;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;

final class SoundUtils {

    public static function playSound(Player $player, string $sound): void {
        $position = $player->getPosition();
        $player->getNetworkSession()->sendDataPacket(PlaySoundPacket::create($sound, $position->getX(), $position->getY(), $position->getZ(), 1.0, 1.0));
    }

}