<?php namespace IET_OU\Open_Media_Player;

/** Player for OU Podcasts embedded in OU sites.
 * @link http://podcast.open.ac.uk
 */
use \IET_OU\Open_Media_Player\Openlearn_Player;

class Podcast_Player extends Openlearn_Player
{
    public $url;
    public $_short_url;
    public $thumbnail_url;

    public $summary;

    public $provider_name = 'oupodcast';
    public $provider_mid;
    public $_access; #Access control.
    public $_copyright;
    public $_track_md5;  #Was, _track_id (DB: shortcode)
    public $_podcast_id; #Numeric
    public $_album_id;   #Alpha-numeric (DB: custom_id)

    public $timestamp;



    /** Check 'intranet only' AND private flags etc.
  */
    public function is_restricted_podcast()
    {
        $this->_check_access();

        return self::truthy($this->_access['intranet_only'])
    #Ignore:  || $this->is_private_podcast() //[iet-it-bugs:1463]
        || $this->is_deleted_podcast();
    }

    /** Just test the 'private' flag. */
    public function is_private_podcast()
    {
        $this->_check_access();

        return self::truthy($this->_access['private']);
    }

    public function is_deleted_podcast()
    {
        $this->_check_access();

        return self::truthy($this->_access['deleted']);
    }

    public function is_published_podcast()
    {
        $this->_check_access();

        return self::truthy($this->_access['published']);
    }


    /** A generic 'boolean' test. */
    protected static function truthy($flag)
    {
        return 1 == $flag   // Feed model.
        || 'Y' == $flag // DB model.
        || true === $flag;
    }

    protected function _check_access()
    {
        if (! isset($this->_access['intranet_only'])) {
          // ERROR?
            die('Error, unexpected access condition, '. __CLASS__);
        }
    }
}
