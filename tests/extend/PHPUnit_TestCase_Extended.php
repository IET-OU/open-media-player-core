<?php namespace IET_OU\Open_Media_Player\Test\Extend;

/**
 * Add customassertions -- assertStrMinLength, assertStrContains...?
 * @author Nick Freear, 4 April 2016.
 */

abstract class PHPUnit_TestCase_Extended extends \PHPUnit_Framework_TestCase //\PHPUnit_Framework_Assert
{
    public function assertStrMinLength($expectedLength, $testString, $message = null)
    {
        $this->assertThat(
            strlen($testString),
            new \PHPUnit_Framework_Constraint_GreaterThan($expectedLength),
            self::f($message, __FUNCTION__)
        );
    }

    public function assertISODate($testDateTime, $message = null)
    {
        $this->assertRegExp('/^20\d{2}-\d{2}-\d{2}[T ]\d{2}:/', $testDateTime, self::f($message, __FUNCTION__, 'ISO 8601'));
    }

    public function assertRFCLikeDate($testDateTime, $message = null)
    {
        $this->assertRegExp('/\d{2}:\d{2}:\d{2} 20\d{2}/', $testDateTime, self::f($message, __FUNCTION__, 'RFC 2822'));
    }

    public function assertEmailLike($testEmailish, $message = null)
    {
        $this->assertRegExp('/\w+@\w+/', $testEmailish, self::f($message, __FUNCTION__));
    }

    public function assertUrlLike($expectedRegex, $testUrlish, $message = null)
    {
        $this->assertRegExp($expectedRegex, $testUrlish, self::f($message, __FUNCTION__));
    }

    public function assertIsHex($lengthRange, $testString, $message = null)
    {
        $pattern = sprintf('/^[0-9a-f]{%s}$/', ($lengthRange ? $lengthRange : 40));
        $this->assertRegExp($pattern, $testString, self::f($message, __FUNCTION__, $pattern));
    }

    /**
    * @return string Format a message.
    */
    protected function f($message, $caller, $extra = null)
    {
        return sprintf('%s (%s)', $message, ($extra ? "$caller: $extra" : $caller));
    }
}
