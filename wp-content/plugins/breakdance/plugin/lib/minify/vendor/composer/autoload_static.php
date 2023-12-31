<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc258dcb585ed97cb27eb23a07623035a
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MatthiasMullie\\PathConverter\\' => 29,
        ),
        'B' => 
        array (
            'Breakdance\\MatthiasMullie\\Minify\\' => 33,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MatthiasMullie\\PathConverter\\' => 
        array (
            0 => __DIR__ . '/..' . '/matthiasmullie/path-converter/src',
        ),
        'Breakdance\\MatthiasMullie\\Minify\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc258dcb585ed97cb27eb23a07623035a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc258dcb585ed97cb27eb23a07623035a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc258dcb585ed97cb27eb23a07623035a::$classMap;

        }, null, ClassLoader::class);
    }
}
