<?php namespace IET_OU\Open_Media_Player;

/**
 * Part of Open Media Player.
 *
 * @license   http://gnu.org/licenses/gpl.html GPL-3.0+
 * @copyright Copyright 2011-2015 The Open University (IET) and contributors.
 * @link      http://iet-ou.github.io/open-media-player
 */

/**
 * Extend this class to create a custom controller, by default at `/extend/{example}/1/2`
 *
 * @copyright Copyright 2015 The Open University.
 */

use \IET_OU\SubClasses\PluginInterface;

abstract class Extend_Controller implements PluginInterface
{
    protected $method = 'xxyxzz';

    abstract public function call($method, $params = array());

    /** Called by SubClasses.
    */
    public function registerPlugin(array & $class_array)
    {
        $class_array[ $this->method ] = get_class($this);
    }
}
