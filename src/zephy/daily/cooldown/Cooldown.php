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
        return isset($this->cooldowns[$player]) && $this->cooldowns[$player] > time();
    }

    public function getCooldown(string $player): int
    {
        return $this->inCooldown($player) ? $this->cooldowns[$player] - time() : 0;
    }

    public function addCooldown(string $player, int $time): void
    {
        $this->cooldowns[$player] = time() + $time;
    }

    public function removeCooldown(string $player): void
    {
        unset($this->cooldowns[$player]);
    }

}