<?php namespace IET_OU\Open_Media_Player;

/**
 * An abstract theme from which to extend OU Media Player themes or skins.
 *
 * @copyright Copyright 2012 The Open University.
 * @author N.D.Freear, 20 March 2012.
 */

use \IET_OU\Open_Media_Player\Base;

// Based on (private): https://gist.github.com/08e20a98136289bbd7ec
abstract class Media_Player_Theme extends Base
{

    public $name;    // The short theme name, used internally (auto-generated from class name).
  #public $type = 'player';
    public $parent;  // Parent theme name (auto-generated).
    public $display; // A longer display name.
    public $view;    // The name of the main view file, without '.php'
    public $engine;  // The player engine, one of 'mediaelement' or 'flowplayer'.

    public $styles;   // An ordered array of stylesheets (for building/ debug).
    public $css_min;  // 'The' single compressed stylesheet for the theme (live).
    public $js_min;   // 'The' single concatenated, minified Javascript (live).
    public $js_path;  // Path to individual, raw Javascripts.
    public $javascripts; // An ordered array of raw Javascripts (build/ debug).
    public $plugin_path; // Path to Flash/ Silverlight plugins.
    public $builder;  // File-path for a build script.

    protected $controls_height;  // Pixels.
    protected $controls_overlap; // Boolean.

    const ENGINE_PATH = 'engines/';
    const THEME_PATH  = 'themes/';


    /** Constructor: auto-generate 'name' and 'parent' properties.
    */
    public function __construct()
    {
        parent::__construct();

        // We use $this - an instance, not a class.
        $this->name = strtolower($this->shortClass('#_Theme$#i'));
        $this->parent = strtolower($this->shortClass('#_Theme$#i', '', true));
    }

    /** Get the machine-readable name for the Scripts controller.
  * @return string
  */
    public function getName()
    {
        return $this->name;
    }

    /** Get the theme display name.
  * @return string
  */
    public function getDisplayname()
    {
        return $this->display;
    }

    /** Get a path to a theme view (relative to application/ directory, without '.php').
  * @return string
  */
    public function getView($view = null)
    {
        return 'themes/'. $this->getName() .'/views/'. ($view ? $view : $this->view);
    }

    /** Get a path to a view for the parent theme.
    */
    public function getParentView($view = null)
    {
        return 'themes/'. $this->parent .'/views/'. ($view ? $view : $this->view);
    }

    public function getControlsHeight()
    {
        return (int) $this->controls_height;
    }
    public function controlsOverlap()
    {
        return (bool) $this->controls_overlap;
    }


    /** Prepare: initialize features of the theme, given a player object (was abstract).
    */
    public function prepare(& $player)
    {
        if (! is_subclass_of($player, 'Base_player')) {
            die('Error, not a valid player object, '.__CLASS__);
        }
    }


    /** Get a configuration item, set a default if it doesn't exist.
    */
    public function config_option($name, $default = null)
    {
        $cfg = $this->CI->config;
        return $cfg->item($name) ? $cfg->item($name) : $default;
    }
}
