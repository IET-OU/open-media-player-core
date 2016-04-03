<?php namespace IET_OU\Open_Media_Player\Test;

use \IET_OU\Open_Media_Player\Gitlib;

class Gitlib_Test extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        \IET_OU\Open_Media_Player\Base::$throw_no_framework = false;
        \IET_OU\SubClasses\SubClasses::$verbose = true;
    }

    public function testPutRevision()
    {
        // Arrange
        $gitlib = new Gitlib();

        // Act
        $version = (object) $gitlib->put_revision();

        echo "version.json: ";
        echo json_encode($version, JSON_PRETTY_PRINT);

        // Asserts
        $this->assertRegExp('/^v?\d+\.\d+.+\-g\w{7}$/', $version->describe);
        $this->assertRegExp('@github.com[\/:]IET-OU\/open-media-player-core@', $version->origin);
        $this->assertRegExp('/^20\d{2}-\d{2}-\d{2}T\d{2}:/', $version->file_date);

        $this->assertEquals('master', $version->branch);
        $this->assertEquals('OK', $version->{ '#' });
    }
}
