<?php namespace IET_OU\Open_Media_Player;

/**
 * SubClasses class.
 *
 * @copyright 2015 The Open University.
 * @author  N.D.Freear, 23 May 2015.
 * @link    https://gist.github.com/nfreear/72a3a62b8ac810ea4c49
 */

use \IET_OU\Open_Media_Player\OffsetIterator;

class SubClasses extends OffsetIterator
{
    /**
     * How many PHP "core" classes should we skip? (performance)
     *
     * $  php -r 'echo count(get_declared_classes());'
     * Result: 139, Mac/PHP 5.4.38; 125, Ar**s/PHP 5.3.3; 120, Pan**s/RHE 6/PHP 5.5.26;
     */
    const PHP_CORE_OFFSET = 120;

    const ON_ADD_FN = 'onAddClass';

    public function __construct($offset = self::PHP_CORE_OFFSET)
    {
        parent::__construct(get_declared_classes(), $offset);
    }

    /**
    * @param string $base_class A parent class or interface.
    * @param string $callback Optional static function to call on the class, providing the key in the results.
    * @return array Array of result classes, optionally keyed.
    */
    public function match($base_class, $callback = null, $test_instantiable = true)
    {
        $results = array();
        foreach ($this as $class) {
            if (is_subclass_of($class, $base_class)) {
//or $base_class == $class) {
                $reflect = new \ReflectionClass($class);
                if ($reflect->isInstantiable()) {
                    if ($callback) {
                        $obj = new $class;
                        $obj->{ $callback }($results);
                        //Was: $results[ $class::{ $callback }() ] = $class;
                    } else {
                        $results[] = $class;
                    }
                }
            }
        }
        return $results;
    }


    public function get_oembed_providers()
    {
        return $this->match('IET_OU\Open_Media_Player\Oembed_Provider', self::ON_ADD_FN);
    }
}
