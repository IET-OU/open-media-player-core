<?php namespace IET_OU\Open_Media_Player;

/**
* Privacy and authentication base class.
*
* @copyright 2012 The Open University.
* @author N.D.Freear, 27 July 2012.
*/

abstract class Privacy_Auth
{

    abstract public function authenticate();


    /**
    * Determine if the caller is a private site/ client.
    *
    * Note, it is the responsibility of the caller to set a HTTP GET parameter.
    * Otherwise the caller is assumed to be public, with restricted-access warning being set as appropriate.
    * @return boolean
    */
    public function is_private_caller()
    {
        return ('private' === filter_input(INPUT_GET, 'site_access', FILTER_SANITIZE_STRING));
    }
}
