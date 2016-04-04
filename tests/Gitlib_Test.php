<?php namespace IET_OU\Open_Media_Player\Test;

/**
 * Unit tests for the `Gitlib` class.
 *
 * @license   http://gnu.org/licenses/gpl.html GPL-3.0+
 * @copyright Copyright 2016 The Open University.
 * @author    Nick Freear, 3 April 2016.
 * @link      https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

use \IET_OU\Open_Media_Player\Gitlib;
use \IET_OU\Open_Media_Player\Test\Extend\PHPUnit_TestCase_Extended;

class Gitlib_Test extends PHPUnit_TestCase_Extended
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

        echo 'version.json: ' . json_encode($version, JSON_PRETTY_PRINT);

        // Asserts
        $this->assertIsHex(40, $version->commit, '$v->commit');
        $this->assertRegExp('/^v?\d+\.\d+.+\-g\w{7}$/', $version->describe, '$v->describe');
        $this->assertUrlLike('/github.com[\/:]IET-OU\/open-media-player-core/', $version->origin, '$v->origin');
        $this->assertISODate($version->file_date, '$v->file_date');
        $this->assertISODate($version->date, '$v->date');

        //Was: $this->assertRegExp('/(\* )?master$/', $version->branch);
        $this->assertEquals('OK', $version->{ '#' }, '$v->{ # }');
        $this->assertEmailLike($version->author, '$v->author');
        $this->assertStrMinLength(7, $version->message, '$v->message');
        $this->assertStrMinLength(12, $version->describe, '$v->describe');
    }
}
