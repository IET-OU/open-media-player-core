<?php namespace IET_OU\Open_Media_Player\Test;

use \IET_OU\Open_Media_Player\Oembed_Provider;
use \IET_OU\Open_Media_Player\Oupodcast_Provider;

class Mock_Service_Provider extends Oembed_Provider {

    public function call($url, $regex_matches) {
    }
}


class Oembed_Provider_Test extends \PHPUnit_Framework_TestCase
{
    // ...

    public function setup()
    {
        \IET_OU\Open_Media_Player\Base::$throw_no_framework = false;
    }

    public function testName()
    {
        // Arrange
        $mock_provider = new Mock_Service_Provider();

        // Act
        $mock_name = $mock_provider->getName();

        // Assert
        $this->assertEquals('mock_service', $mock_name);
    }

    public function testPodcast()
    {
        return;

        // Arrange
        $oup_provider = new Oupodcast_Provider();

        // Act
        $oup_name = $oup_provider->getName();

        // Assert
        $this->assertEquals('oupodcast', $oup_name);
    }
    // ...
}

