<?php

namespace zephy\daily\permissions;

use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;

final class PermissionRegister
{

    public static function destroy(string $permission): void
    {
        if (!self::isAlreadyRegistered($permission)) {
            PermissionManager::getInstance()->removePermission($permission);
            $perm = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR);
            $perm->removeChild($permission, true);
        }
    }

    public static function register(string $permission): void
    {
        if (!self::isAlreadyRegistered($permission)) {
            PermissionManager::getInstance()->addPermission(new Permission($permission));

            $perm = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR);
            $perm->addChild($permission, true);
        }
    }

    public static function isAlreadyRegistered(string $permission): bool
    {
        if (PermissionManager::getInstance()->getPermission($permission) !== null) {
            return true;
        }
        return false;
    }
}