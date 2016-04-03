<?php namespace IET_OU\Open_Media_Player;

/**
 * Gitlib: a simple Git library, to get changeset hashes and information.
 *
 * See: https://bitbucket.org/cloudengine/cloudengine/src/tip/system/application/libraries/Hglib.php
 *
 * @copyright 2012 The Open University.
 * @author N.D.Freear, 2012-04-25.
 */

use \IET_OU\Open_Media_Player\Base;

class Gitlib extends Base
{

    const GIT_DESCRIBE_REGEX = '/v?(?P<major>\d+)\.(?P<minor>\d+)(?P<patch>\.\d+)(?P<id>-[\w\.]+)?-(?P<build>\d+)-(?P<hash>g.+)/';
    const VERSION = '%s.%s%s.%s%s+%s';

    protected $_hash;
    protected $last_success = true;

    protected static $REVISION_FILE = 'version.json';

    public function __construct()
    {
        self::$REVISION_FILE = self::revision_file_path();
    }

    /**
     * Return the start of the most recent commit hash (from file).
     * Maybe md5/ sha() the result?
     */
    public function lastHash($length = 6)
    {
        if ($length < 4) {
            $length = 4;
        }

        if ($this->_hash) {
            return substr($this->_hash, 0, $length);
        }

        $log = $this->get_revision();
        $this->_hash = $log->commit;

        return substr($this->_hash, 0, $length);
    }

    /** Save revision meta-data to a '.' file, JSON-encoded.
     *  (CloudEngine's Hglib uses PHP (un)serialize.)
     */
    public function put_revision($echo = false)
    {
        $result = false;
        $log = $this->_exec('log -1');
        $result[ '#' ] = $log ? 'OK' : 'Warning, Git not found? Proceed without.';
        $result[ 'ok' ] = (bool) $log;
        if (! $log) {
            fprintf(STDERR, "OK (proceeding without Git)\n");
        }

        $log = explode("\n", $log);
        //Hmm, a more efficient way?
        foreach ($log as $line) {
            if (false !== ($p = strpos($line, ' '))) {
#':'
                $key = trim(substr($line, 0, $p), ' :');
                if (!$key) {
                    $key = 'message';
                }
                $result[strtolower($key)] = trim(substr($line, $p+1));
                if ('message'==$key) {
                    break;
                }
            }
        }
        $result = $this->gitDescribeVersion($result);

        $result[ 'branch' ] = trim($this->_exec('symbolic-ref --short HEAD'));
        // http://stackoverflow.com/questions/4089430/how-can-i-determine-the-url-that-a-local-git-repo-was-originally-pulled-from
        $result['origin'] = rtrim($this->_exec('config --get remote.origin.url'), "\r\n");
        #$result['origin url'] = str_replace(array('git@', 'com:'), array('https://', 'com/'), $result['origin']);
        #$result['agent'] = basename(__FILE__);
        #$result['git'] = rtrim($this->_exec('--version'), "\r\n ");
        $result['file_date'] = date('c');

        $bytes = $this->put_json(self::$REVISION_FILE, $result, $echo);

        if (!$echo) {
            fprintf(STDERR, "File written, %d: %s\n", $bytes, self::$REVISION_FILE);
        }

        return $result;
    }

    /** Read revision meta-data from the '.' file.
    */
    public function get_revision()
    {
        return (object) json_decode(file_get_contents(self::$REVISION_FILE));
    }

    protected function put_json($filename, $data, $echo = false)
    {
        $json = str_replace('","', "\",\n\"", json_encode($data));
        if ($echo) {
            echo $json;
        } else {
            return file_put_contents($filename, $json);
        }
    }

    protected static function revision_file_path()
    {
        $path = __DIR__ . '/../../../../';
        if (file_exists($path . 'composer.json')) {
            return $path . self::$REVISION_FILE;
        }
        return __DIR__ . '/../' . self::$REVISION_FILE;
    }

    /** Execute a Git command, if allowed.
    */
    protected function _exec($cmd)
    {
        if (! $this->is_cli_request()) {
            echo "Warning, Git must be run from the commandline.".PHP_EOL;
            exit(1);
        }

        // Quietly 'die'
        if (! $this->last_success) {
            return null;
        }

        //Security?
        $git_path = null;  // Todo: no-CI error? $this->config_item('git_path');

        if (! $git_path) {
            $git_path = "git";  #"/usr/bin/env git";
            #$git_path = "/usr/bin/git";  #Redhat6
            #$git_path = "/usr/local/git/bin/git"; #Mac
        }

        $git_cmd = "$git_path $cmd";

        $result = false;
        // The path may contain 'sudo ../git'.
        if ('git' != $git_path && ! file_exists($git_path)) {
            fprintf(STDERR, "Warning, not found, %s\n", $git_path);
        }

        $cwd = getcwd();
        if ($cwd && defined('APPPATH')) {
            chdir(APPPATH);
        }

        // Exec: combining stderr & stdout - not ideal (hides error messages!)
        $output = $return_var = null;
        $last_line = exec(escapeshellcmd($git_cmd) .' 2>&1', $output, $return_var);

        if ($cwd) {
            chdir($cwd);
        }

        $this->last_success = (0 === $return_var);

        if (! $this->last_success) {
            return false;
        }
        return implode("\n", $output);
    }

    protected function gitDescribeVersion($result)
    {
        // Describe "v0.86-usertest-95-g.."
        // Semantic Versioning, http://semver.org
        $result[ 'describe' ] = trim($this->_exec('describe --tags --long'));
        $version = $result[ 'describe' ];
        if (preg_match(self::GIT_DESCRIBE_REGEX, $version, $matches)) {
            $v = (object) $matches;
            $version = sprintf(self::VERSION, $v->major, $v->minor, $v->patch, $v->build, $v->id, $v->hash);
        }
        $result[ 'version' ] = $version;
        return $result;
    }
}
