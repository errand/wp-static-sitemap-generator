<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit04f79a3ca507c584de35e583dd82b370
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'GpsLab\\Component\\Sitemap\\' => 25,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'GpsLab\\Component\\Sitemap\\' => 
        array (
            0 => __DIR__ . '/..' . '/gpslab/sitemap/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit04f79a3ca507c584de35e583dd82b370::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit04f79a3ca507c584de35e583dd82b370::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit04f79a3ca507c584de35e583dd82b370::$classMap;

        }, null, ClassLoader::class);
    }
}
