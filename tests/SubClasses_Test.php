<?php namespace IET_OU\Open_Media_Player\Test;

use \IET_OU\SubClasses\SubClasses;


class SubClasses_Test extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        \IET_OU\Open_Media_Player\Base::$throw_no_framework = false;
        \IET_OU\SubClasses\SubClasses::$verbose = true;
    }

    public function testOembedProviders()
    {
        // Arrange
        $sub = new SubClasses();

        // Act
        $providers = $sub->get_oembed_providers();

        var_dump("Providers:", $providers);

        // Assert
        $this->assertEquals(4, count($providers));
    }

}

