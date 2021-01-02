<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1c0bca8d9062f63640b5d756f87d36cf
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Waddle\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Waddle\\' => 
        array (
            0 => __DIR__ . '/..' . '/duckfusion/waddle/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1c0bca8d9062f63640b5d756f87d36cf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1c0bca8d9062f63640b5d756f87d36cf::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1c0bca8d9062f63640b5d756f87d36cf::$classMap;

        }, null, ClassLoader::class);
    }
}
