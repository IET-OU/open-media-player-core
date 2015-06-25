<?php namespace IET_OU\Open_Media_Player\Test;

use \IET_OU\Open_Media_Player\SubClasses;
use \IET_OU\Open_Media_Player\Oembed_Provider;

class Mock_Ex_Provider extends Oembed_Provider {

    protected static $hosts = array( 'example.org', 'example.com' );

    public function call($url, $regex_matches) {
    }
}

class SubClasses_Test extends \PHPUnit_Framework_TestCase
{
    // ...

    public function setup()
    {
        \IET_OU\Open_Media_Player\Base::$throw_no_framework = false;
    }

    public function testOembedProviders()
    {
        // Arrange
        $sub = new SubClasses();

        // Act
        $providers = $sub->get_oembed_providers();

        var_dump($providers);

        // Assert
        $this->assertEquals(2, count($providers));
    }

}

