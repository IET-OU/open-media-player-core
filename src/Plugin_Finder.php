<?php namespace IET_OU\Open_Media_Player;

/**
 * Part of Open Media Player.
 *
 * @license   http://gnu.org/licenses/gpl.html GPL-3.0+
 * @copyright Copyright 2011-2015 The Open University (IET) and contributors.
 * @link      http://iet-ou.github.io/open-media-player
 */

/**
 * Plugin_Finder - a wrapper class around `SubClasses` / plugin-genie.
 *
 *   grep -nr -B 1 -A 3  SubClasses application
 *   grep -nr -B 0 -A 1  SubClasses vendor/iet-ou
 *
 * @copyright 2015 The Open University.
 * @author  N.D.Freear, 29 August 2015.
 * @link https://github.com/nfreear/plugin-genie
 */

class Plugin_Finder
{
    // The single instance.
    private static $plugins;

    public function __construct()
    {
        if (! self::$plugins) {
            self::$plugins = new \IET_OU\SubClasses\SubClasses();
        }
    }

    /**
    * @return array
    */
    public function get_oembed_providers()
    {
        return self::$plugins->match('IET_OU\\Open_Media_Player\\Oembed_Provider');
    }

    public function get_local_embed_providers()
    {
        return self::$plugins->match('\\IET_OU\\Open_Media_Player\\Oembed_Local_Embed_Interface');
    }

    public function get_player_themes()
    {
        return self::$plugins->match('IET_OU\\Open_Media_Player\\Media_Player_Theme');
    }
}
