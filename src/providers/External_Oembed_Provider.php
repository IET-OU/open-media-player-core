<?php namespace IET_OU\Open_Media_Player;

/**
 *
 */
use \IET_OU\Open_Media_Player\Oembed_Provider;

abstract class External_Oembed_Provider extends Oembed_Provider
{

  //protected $_endpoint_url; # oEmbed endpoint for 'external' providers, eg. iSpot.

    public function call($url, $matches)
    {
        $this->_error('sorry the endpoint is: '. $this->_endpoint_url, 400.9);
    }
}
