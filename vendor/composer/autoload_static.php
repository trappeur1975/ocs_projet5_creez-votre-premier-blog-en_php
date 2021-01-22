<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5d8c544af7998ee1659f8825cd7f365e
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'AltoRouter' => __DIR__ . '/..' . '/altorouter/altorouter/AltoRouter.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5d8c544af7998ee1659f8825cd7f365e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5d8c544af7998ee1659f8825cd7f365e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5d8c544af7998ee1659f8825cd7f365e::$classMap;

        }, null, ClassLoader::class);
    }
}
