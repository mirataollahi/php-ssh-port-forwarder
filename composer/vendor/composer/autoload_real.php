<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit5ce633be2dcf5353d823d68043c6ac81
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit5ce633be2dcf5353d823d68043c6ac81', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit5ce633be2dcf5353d823d68043c6ac81', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit5ce633be2dcf5353d823d68043c6ac81::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
