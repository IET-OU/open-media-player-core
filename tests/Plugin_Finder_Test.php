<?php namespace IET_OU\Open_Media_Player\Test;

/**
 * Unit tests for the `Plugin_Finder` class (was: SubClasses_Test.php)
 *
 * @author Nick Freear, 25 June 2016.
 */

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

        echo 'Providers: ' . json_encode($providers, JSON_PRETTY_PRINT);

        // Assert
        $this->assertCount(5, $providers, 'oEmbed providers');
        $this->assertRegExp('/^IET_OU/', $providers[ 'podcast.open.ac.uk' ]);
    }

    public function testLocalProviders()
    {
        $finder = new Plugin_Finder();

        $locals = $finder->get_local_embed_providers();

        $this->assertCount(0, $locals, 'Local providers');
    }

    public function testPlayerThemes()
    {
        $finder = new Plugin_Finder();

        $themes = $finder->get_player_themes();

        $this->assertCount(1, $themes, 'Player themes');
    }
}
