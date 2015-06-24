<?php namespace IET_OU\Open_Media_Player\Test;

use \IET_OU\Open_Media_Player\Media_Player_Theme;

class Mock_Theme extends Media_Player_Theme {
}


class Player_Theme_Test extends \PHPUnit_Framework_TestCase
{
    // ...

    public function testName()
    {
        // Arrange
        \IET_OU\Open_Media_Player\Base::$throw_no_framework = false;

        $theme = new Mock_Theme();

        // Act
        $name = $theme->getName();

        // Assert
        $this->assertEquals('mock', $name);
    }

    // ...
}

