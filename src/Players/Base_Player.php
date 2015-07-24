<?php namespace IET_OU\Open_Media_Player;

/**
 * Open Media Player library - player meta-data classes.
 *
 * @license   http://gnu.org/licenses/gpl.html GPL-3.0+
 * @copyright Copyright 2011 The Open University.
 * @author N.D.Freear, 2011-04-07.
 */

use \IET_OU\Open_Media_Player\Base;

/* Ouplayer class - Holds meta-data for the player.
   Consistency between OU and OUVLE variants.
*/
abstract class Base_Player extends Base
{

    const DEF_WIDTH = 560; #Was: 640;
    const DEF_HEIGHT= 315; #Was: 410;
    const AUDIO_WIDTH = 360; #Was: 400, 380;
    const AUDIO_HEIGHT= 36;
    const POD_AUDIO_HEIGHT = 80; #108; #60;

  // Bug #1415, Sizes in reverse-order: largest first. These sizes are NOT final! Need to check...
  // https://docs.google.com/document/d/1zgycCtBTcph7O4wwXAQtQq0jtXoJ3eKnSxKnNr6VRPU/edit
    protected static $video_sizes = array(
        array('width' => 848, 'height' => 480, 'label' => 'x-large'),
        array('width' => 640, 'height' => 360, 'label' => 'large'),
        array('width' => 560, 'height' => 315, 'label' => 'medium'), //'default'?
        array('width' => 480, 'height' => 270, 'label' => 'small'),
        array('width' => 295, 'height' => 166, 'label' => 'x-small'),
    );

  // Hmm, public?
    public $title;
    public $media_url;
    public $media_type; #audio|video (not MIME!)
    public $media_html5 = true;
    public $codec; #?
    public $width = self::DEF_WIDTH;
    public $height= self::DEF_HEIGHT;
    public $object_height = self::DEF_HEIGHT;
    public $size_label = 'default';
    public $duration;
    public $language = 'en';
  #public $is_vle  = FALSE;
    public $poster_url; #poster/ thumbnail?
    public $transcript_url;
    public $caption_url;
    public $iframe_url; #Our <iframe> embed!!

    public $_theme;
    public $_extend; #Odds and ends?


    /** Construct a player, optionally setting properties.
    * @param array $properties
    */
    public function __construct($properties = array())
    {
        foreach ($properties as $key => $value) {
            $this->{ $key } = $value;
        }
    }

    /** Get a list of sizes, suitable for the Services controller.
    * @return array
    */
    public function get_sizes()
    {
        $sizes = self::$video_sizes;
        $sizes['default'] = array('width'=>self::DEF_WIDTH, 'height'=>self::DEF_HEIGHT, 'label'=>'default');
      #$sizes['audio']
        return array('video' => array_values($sizes));
    }

    /** calc_size: Calculate the appropriate size for the Player oEmbed response.
    */
    public function calc_size($width, $height, $audio_poster = false)
    {
    // Need to move 'maxwidth'/maxheight calls into the oEmbed controller.
        $max_width = (int) $this->get_param('maxwidth');
        $max_height= (int) $this->get_param('maxheight');
        $resizable = $this->get_param('_resizable'); //Not used.
        $percent_width = (int) $this->get_param('pcwidth'); //Experimental

   // These sizes are NOT final! Need to check...
   #$rev_sizes = array(720=>460, 640=>410, 620=>400, 560=>350 /*340*/, 480=>360, 460=>350);
   #$sizes = array(460=>350, 480=>360, 560=>340, 620=>400, 640=>410, 720=>460);

   //Validate width/height..?!
        if ('audio'==$this->media_type) {
            $this->width = self::AUDIO_WIDTH;
            $this->object_height = self::AUDIO_HEIGHT;
            if ($this->is_player('podcast')) {
                $this->height = self::POD_AUDIO_HEIGHT;
            } elseif ($audio_poster) {
     #$this->poster_url) {
                $this->height = 304+$this->object_height;
            } else {
                $this->height= $this->object_height;
            }

            if ($max_width && $max_width < self::AUDIO_WIDTH) {
              //end(self::$video_sizes); current()
                $sz_xsmall = self::$video_sizes[ count(self::$video_sizes) - 1 ];
                $this->width = $sz_xsmall['width'];
                $this->size_label = $sz_xsmall['label'];
            }

        } else {
            #'video'

            if ($max_width) {
              // Count down through the potential sizes.
                foreach (self::$video_sizes as $rank => $size) {
                    $width = $size['width'];
                    if ($max_width >= $width) {
                        $this->width = $width;
                        $this->height= $size['height'];
                        $this->size_label = $size['label']; #isset($rev_labels[$width]) ? $rev_labels[$width] : 'unknown';
                        break;
                    }
                }

           // If size is still not set, then maxwidth < smallest size..
                if ($this->size_label == 'default') {
                    $sz_xsmall = self::$video_sizes[ count(self::$video_sizes) - 1 ];
                    $this->width = $sz_xsmall['width'];
                    $this->height = $sz_xsmall['height'];
                    $this->size_label = $sz_xsmall['label'];
                }
            }
            $this->object_height = $this->height - 30;

          // Experimental, <iframe> width=100%
            if ($percent_width) {
                if ($percent_width > 10 && $percent_width <= 100) {
                    $this->width = $percent_width .'%';
                } else {
                    $this->width = '100%';
                }
            }
          #var_dump($this->width, $this->height, $this->object_height, self::$video_sizes); exit;
        }
    }


    /** Which variant of the player is this?
   * @param string Variant, currently 'podcast', 'vle', 'openlearn' (case-insensitive).
   * @return bool
   */
    public function is_player($str_variant)
    {
        $player = strtolower($this->shortClass('#_Player$#i'));
        return $player == strtolower($str_variant);
    }

    /** Are we going to play video or audio?
   * @return bool
   */
    public function is_video()
    {
        return 'video' == $this->media_type;
    }

    public function is_private_podcast()
    {
        return true;
    }
}

/** Player for The Open University's learning environment.
 * @link http://learn.open.ac.uk
 */
class Vle_Player extends Base_Player
{
}
