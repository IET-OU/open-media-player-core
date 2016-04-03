<?php namespace IET_OU\Open_Media_Player;

/**
* OU SAMS authentication.
*
* @copyright 2012 The Open University.
* @author N.D.Freear, 27 July 2012.
*/

use \IET_OU\Open_Media_Player\Privacy_Auth;

class Sams_Auth extends Privacy_Auth
{

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
    * Basic OU-SAMS session cookie check and redirect - used for VLE demo/test pages.
    * @return boolean
    */
    public function authenticate()
    {
      // Security: note the 'localhost' check.
        if (#'localhost' != $this->CI-X->input->server('HTTP_HOST') &&
        !$this->cookie('SAMSsession')
        || !$this->cookie('SAMS2session')) {
            header('Location: ' . $this->login_link(current_url()), true, 307);
            exit;
        }

        return true;
    }

    public static function login_link($url)
    {
      //( Redirect to:  https://msds.open.ac.uk/signon/SAMSDefault/SAMS001_Default.aspx?URL= )

      // Ensure abbreviated URLs like "//example.org" resolve to "http://example.org" [Bug: #1]
      // IMPORTANT: urlencode() breaks the SAMS sign-on re-direction! :(
        return str_replace(array( '=%2F%2F', '=//' ), '=http://', 'https://msds.open.ac.uk/signon/?URL=' . $url);
    }


    /**
    * Determine if the authenticated user is staff, including OU tutors.
    * @return boolean
    */
    public function is_staff()
    {
        $sess = $this->cookie('SAMS2session');
        return $sess && (false !== strpos($sess, 'samsStaffID=') || false !== strpos($sess, 'samsTutorID='));
    }

    /** Utility to safely get a cookie.
    * @return string
    */
    protected function cookie($key, $filter = FILTER_SANITIZE_STRING, $options = null)
    {
        return filter_input(INPUT_COOKIE, $key, $filter, $options);
    }
}
