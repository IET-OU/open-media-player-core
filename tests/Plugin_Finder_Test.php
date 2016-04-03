<?php namespace IET_OU\Open_Media_Player\Test;

use \IET_OU\Open_Media_Player\Plugin_Finder;

class Plugin_Finder_Test extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        \IET_OU\Open_Media_Player\Base::$throw_no_framework = false;
        \IET_OU\SubClasses\SubClasses::$verbose = true;
    }

    public function testOembedProviders()
    {
        // Arrange
        $finder = new Plugin_Finder();

        // Act
        $providers = $finder->get_oembed_providers();
        $locals = $finder->get_local_embed_providers();

        var_dump("Providers:", $providers);

        // Assert
        $this->assertEquals(4, count($providers));

        $this->assertEquals(0, count($locals));
    }

    public function testPlayerThemes()
    {
        $finder = new Plugin_Finder();

        $themes = $finder->get_player_themes();

        $this->assertEquals(0, count($themes));
    }
}
