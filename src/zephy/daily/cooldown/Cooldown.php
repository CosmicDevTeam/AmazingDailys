<?php

namespace zephy\daily\cooldown;

final class Cooldown
{

    private array $cooldowns = [];

    public function getCooldowns(): array
    {
        return $this->cooldowns;
    }

    public function inCooldown(string $player): bool
    {
        if (isset($this->cooldowns[$player])) {
            return $this->cooldowns[$player] > time();
        }
        return false;
    }

    public function getCooldown(string $player): int
    {
        return $this->cooldowns[$player] - time();
    }

    public function addCooldown(string $player, int $time): void
    {
        $this->cooldowns[$player] = time() + $time;
    }

    public function removeCooldown(string $player): void
    {
        $this->cooldowns[$player] = 0;
    }
}