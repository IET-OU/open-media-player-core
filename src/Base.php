<?php namespace IET_OU\Open_Media_Player;

/**
 * THE single base class, for extensible components of Open Media Player.
 *
 * @copyright Copyright 2011-2015 The Open University.
 * @author  N.D.Freear, 18 June 2015.
 * @link    http://www.codeigniter.com/userguide2/
 */

abstract class Base
{
    public static $throw_no_framework = true;

    protected $CI;

    public function __construct()
    {
        // Is CodeIgniter present?
        if (defined('CI_VERSION') && function_exists('get_instance')) {
            $ci =& \get_instance();

            if ($ci instanceof \IET_OU\Open_Media_Player\MY_Controller) {
                $this->CI = $ci;
            }
        }
        if (!$this->CI) {
            $this->throw_no_framework_found_warning(__METHOD__, func_get_args());
        }
    }

    protected function has_ci()
    {
        return (bool) $this->CI;
    }

    protected function get_param($key, $default = null, $filter = FILTER_SANITIZE_STRING, $options = null)
    {
        $value = filter_input(INPUT_GET, $key, $filter, $options);
        return $value ? $value : $default;
        // Alt: $this->CI->input->get();
    }

    /** Get a configuration item, set a default if it doesn't exist.
    */
    protected function config_item($item, $default = null, $index = '')
    {
        if ($this->CI) {
            $value = $this->CI->config->item($item, $index);
            return $value ? $value : $default;
        } else {
            $this->throw_no_framework_found_warning(__METHOD__, func_get_args());
        }
    }

    protected function load_library($library = '', $params = null, $object_name = null)
    {
        if ($this->has_ci()) {
            return $this->CI->load->library($library, $params, $object_name);
        } else {
            $this->throw_no_framework_found_warning(__METHOD__, func_get_args());
        }
    }

    protected function load_model($model, $name = '', $db_conn = false)
    {
        if ($this->has_ci()) {
            return $this->CI->load->model($model, $name, $db_conn);
        } else {
            $this->throw_no_framework_found_warning(__METHOD__, func_get_args());
        }
    }

    protected function load_helper($helpers = array())
    {
        if ($this->has_ci()) {
            return $this->CI->load->helper($helpers);
        } else {
            $this->throw_no_framework_found_warning(__METHOD__, func_get_args());
        }
    }

    protected function _log($level = 'error', $message = 'unknown', $php_error = false)
    {
        if ($this->CI) {
            return $this->CI->_log($level, $message, $php_error);
        } else {
            $this->throw_no_framework_found_warning(__METHOD__, func_get_args());
        }
    }

    /** Display an error message and exit?
    */
    protected function _error($message, $code = 500, $from = null, $obj = null)
    {
        if ($this->CI) {
            return $this->CI->_error($mesage, $code, $from, $obj);
        } else {
            $this->throw_no_framework_found_warning(__METHOD__, func_get_args());
        }
    }

    protected function _debug($exp)
    {
        static $count = 0, $debug = true;
        if ($debug) {
            @header(sprintf('X-OMP-Core-%02d: %s', $count, json_encode($exp)));
        }
        $count++;
    }

    /** Get class name, without namespace, optionally trim content by RegExp.
    http://stackoverflow.com/questions/19901850/how-do-i-get-an-objects-unqualified-short-class-
    */
    protected function shortClass($regex = null, $replace = '', $class_name = null)
    {
        $with_namespace = $class_name ? $class_name : get_class($this);
        $short_name = substr($with_namespace, strrpos($with_namespace, '\\') + 1);
        if ($regex) {
            return preg_replace($regex, $replace, $short_name);
        }
        return $short_name;
    }

    protected function throw_no_framework_found_warning($function, $args = null)
    {
        if (static::$throw_no_framework) {
            throw new \Exception('No CodeIgniter framework found: '. $function);
        } else {
            echo "Warning: no CodeIgniter framework found: $function\n";
        }
    }
}
