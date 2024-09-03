<?php

namespace zephy\daily\utils;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;

final class SoundUtils {
    public static function playSound(Player $player, string $sound): void {
        $p = $player->getPosition();
        $pk = PlaySoundPacket::create($sound, $p->getX(), $p->getY(), $p->getZ(), 1.0, 1.0);
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}