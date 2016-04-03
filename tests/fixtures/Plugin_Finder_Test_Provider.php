<?php namespace IET_OU\Open_Media_Player\Test\Fixtures;

use \IET_OU\Open_Media_Player\Oembed_Provider;

class Plugin_Finder_Test_Provider extends Oembed_Provider
{

    public $domain = 'example.org';
    public $subdomains = array( 'example.com', 'test.example.edu' );

    public function call($url, $regex_matches)
    {
    }
}
