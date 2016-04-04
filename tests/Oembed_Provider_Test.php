<?php namespace IET_OU\Open_Media_Player\Test;

/**
 * Unit tests for 'Oembed_Provider'-derived classes.
 *
 * @author Nick Freear, 24 June 2015.
 */

use \IET_OU\Open_Media_Player\Test\Fixtures\Mock_Service_Provider;
use \IET_OU\Open_Media_Player\Oupodcast_Provider;

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
