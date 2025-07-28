<?php

namespace Akyos\Core\Interface;

interface IModule extends IBootable
{
    public static function getName(): string;

    public static function getDescription(): string;

    public static function isActive(): bool;

    public static function activate(): void;

    public static function deactivate(): void;
}
