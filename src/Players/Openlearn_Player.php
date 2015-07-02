<?php namespace IET_OU\Open_Media_Player;

/** Player for OpenLearn-learningspace.
 * @link http://www.open.edu/openlearn
 */

use \IET_OU\Open_Media_Player\Base_Player;

class Openlearn_Player extends Base_Player
{
    public $_related_url;
    public $_related_text;

    public $transcript_html;


    public function is_private_podcast()
    {
        return false;
    }
}
