<?php namespace IET_OU\Open_Media_Player;

/**
 * Part of Open Media Player.
 *
 * @license   http://gnu.org/licenses/gpl.html GPL-3.0+
 * @copyright Copyright 2011-2015 The Open University (IET) and contributors.
 * @link      http://iet-ou.github.io/open-media-player
 */

/**
 * HTTP request library.
 * Code from base_service.php, using cURL.
 *
 * @copyright Copyright 2011 The Open University.
 * @author N.D.Freear, 6 March 2012.
 */


/* https://bugs.php.net/bug.php?id=53543
   https://github.com/dtouzeau/1.6.x/blob/master/ressources/class.ccurl.inc#L5
*/
defined('CURLOPT_NOPROXY') ? null : define('CURLOPT_NOPROXY', 10177);

use \IET_OU\Open_Media_Player\Base;

class Http extends Base
{

    public function request($url, $spoof = true, $options = array())
    {
        $result = $this->_prepare_request($url, $spoof, $options);

        return $this->_http_request_curl($url, $spoof, $options, $result);
    }


    /** Prepare the HTTP request.
    */
    #http://api.drupal.org/api/drupal/core%21includes%21common.inc/function/drupal_http_request/8
    protected function _prepare_request($url, $spoof, &$options)
    {
        $result = new \stdClass();

        // Parse the URL and make sure we can handle the schema.
        $uri = @parse_url($url);

        if ($uri == false) {
            $result->error = 'unable to parse URL';
            $result->code = -1001;
            return $result;
        }

        if (!isset($uri['scheme'])) {
            $result->error = 'missing schema';
            $result->code = -1002;
            return $result;
        }

        #timer_start(__FUNCTION__);


        // Bug #1334, Proxy mode to fix VLE caption redirects (Timedtext controller).
        $options[ 'cookie' ] = null;
        if (isset($options['proxy_cookies'])) {
            $cookie_names =  $this->config_item('httplib_proxy_cookies');
            if (! is_array($cookie_names)) {
                $this->_error('Array expected for $config[httplib_proxy_cookies]', 400);
            }

            $cookies = '';
            foreach ($cookie_names as $cname) {
                $cookies .= "$cname=". $this->cookie($cname) .'; ';
            }
            $options['cookie'] = rtrim($cookies, '; ');
        }

        // Bug #4, Optionally add cookies for every request to a host/ domain.
        $cookie_r = $this->config_item('http_cookie');
        if (is_array($cookie_r)) {
            foreach ($cookie_r as $domain => $cookie) {
                if (false !== strpos($url, $domain)) {
                    $options[ 'cookie' ] .= $cookie;
                }
            }
        }

        $ua = 'Open Media Player/1.1.* (PHP/cURL)';
        if ($spoof) {
            // Updated, April 2012.
            $ua = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.142 Safari/535.19";
           #$ua="Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-GB; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3";
        }

      // Merge the default options.
        $options += array(
            'proxy' => $this->config_item('http_proxy'),
            'no_proxy' => $this->config_item('http_no_proxy'),
            'headers' => array(),
            'method' => 'GET',
            'data' => null,
            'max_redirects' => 2,  #3,
            'timeout' => 5,  #15, 30.0 seconds,
            #'context' => NULL,

            'cookie' => null,
            'ua' => $ua,
            'debug' => false,
            'auth' => null, #'[domain\]user:password'
            'ssl_verify' => true,
        );

        return $result;
    }


    /** Perform the HTTP request using cURL.
    */
    protected function _http_request_curl($url, $spoof, $options, $result)
    {
        if (!function_exists('curl_init')) {
            die('Error, cURL is required.');
        }

        $this->_debug($options);

        $h_curl = curl_init($url);

        curl_setopt($h_curl, CURLOPT_USERAGENT, $options['ua']);
        if (!$spoof) {
            curl_setopt($h_curl, CURLOPT_REFERER, base_url());
        }

        // Required for intranet-restricted content [Bug: #1]
        if ($options['cookie']) {
            curl_setopt($h_curl, CURLOPT_COOKIE, $options['cookie']);
            header('X-Proxy-Cookie: '.$options['cookie']);
        }

        if (! $options['ssl_verify']) {
            curl_setopt($h_curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($h_curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($h_curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($h_curl, CURLOPT_MAXREDIRS, $options['max_redirects']);
        curl_setopt($h_curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($h_curl, CURLOPT_TIMEOUT, $options['timeout']);
        curl_setopt($h_curl, CURLOPT_CONNECTTIMEOUT, $options['timeout']);

        if ($options['debug']) {
            curl_setopt($h_curl, CURLOPT_HEADER, true);
            curl_setopt($h_curl, CURLINFO_HEADER_OUT, true);
        }

        if ($options['auth']) {
            //TODO: http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/
            curl_setopt($h_curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($h_curl, CURLOPT_SSL_VERIFYHOST, false);

            curl_setopt($h_curl, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
            curl_setopt($h_curl, CURLOPT_USERPWD, $options['auth']);
        }

        if ($options[ 'proxy' ]) {
            curl_setopt($h_curl, CURLOPT_PROXY, $options[ 'proxy' ]);
            curl_setopt($h_curl, CURLOPT_NOPROXY, $options[ 'no_proxy' ]);
        }
        curl_setopt($h_curl, CURLOPT_RETURNTRANSFER, true);

        $result->data = curl_exec($h_curl);
        $result->http_code = false;

        $result->_headers = null;
        // Fragile: rely on cURL always putting 'Content-Length' last..
        if ($options['debug'] && preg_match('#^(HTTP\/1\..+Content\-Length: \d+\s)(.*)$#ms', $result->data, $matches)) {
            $result->_headers = $matches[1];
            $result->data = trim($matches[2], "\r\n");
        }
        if ($errno = curl_errno($h_curl)) {
            //Error. Quietly log?
            $this->_log('error', "cURL $errno, ".curl_error($h_curl)." GET $url");
            //$this->CI->firephp->fb("cURL $errno", "cURL error", "ERROR");
            $result->http_code = "500." . $errno;
            $result->curl_errno = $errno;
            $result->curl_error = curl_error($h_curl);
            $result->success = false;
            $result->url = $url;

            $this->_debug($result);
        }
        $result->info = curl_getinfo($h_curl);
        if (!$result->http_code) {
            $result->success = ($result->info['http_code'] < 300);
            $result->http_code = $result->info['http_code'];
        }
        $result->is_not_found = (404 == $result->http_code);

        curl_close($h_curl);

        return (object) $result;
    }

    /** Utility to safely get a cookie. */
    protected function cookie($key, $filter = FILTER_SANITIZE_STRING, $options = null)
    {
        return filter_input(INPUT_COOKIE, $key, $filter, $options);
    }
}
