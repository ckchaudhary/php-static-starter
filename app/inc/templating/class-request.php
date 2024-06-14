<?php

/**
 * Request class.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

namespace RecyleBin\PhpSSS;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Maps http requests to correct templates.
 * Holds information about response like, meta keyword etc.
 */
class Request
{

    /**
     * Information parsed from url
     *
     * @var array
     */
    protected $_request = [];

    /**
     * Information supplied by templates
     *
     * @var array
     */
    protected $_current = [];

    /**
     * Constructor
     */
    private function __construct()
    {
    }

    /**
     * Get the single instance of this class.
     *
     * @return \RecyleBin\PhpSSS\Request
     */
    public static function instance()
    {
        static $instance;
        if (! isset($instance)) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Read the current url and save information.
     *
     * @return void
     */
    public function parse()
    {
        $parts = parse_url($_SERVER['REQUEST_URI']);
        $path = isset($parts['path']) ? trim($parts['path'], '/') : '';
        
        if ($path) {
            // If the admin resides in a subdirectory, dont consider that subdirectory for page name
            if (\SUBDIRECTORY) {
                $sub_directory = trim(\SUBDIRECTORY, '/');
                if (strpos($path, $sub_directory) === 0) {
                    $path = substr($path, strlen($sub_directory));
                    $path = trim($path, '/');
                }
            }
        }

        if ($path) {
            $this->_request[ 'pagenames' ] = explode('/', $path);
        }

        $this->_request[ 'params' ] = [];
        $qs = isset($parts['query']) ? $parts['query'] : '';
        if ($qs) {
            $qs = explode('&', $qs);
            foreach ($qs as $k => $v) {
                $this->_request[ 'params' ][ $k ] = $v;
            }
        }
    }

    /**
     * Get path parts.
     *
     * @return array
     */
    public function getPathParts()
    {
        return isset($this->_request['pagenames']) ? $this->_request[ 'pagenames' ] : [];
    }

    /**
     * Determine which template file to load for the current request.
     *
     * @return string
     */
    public function templateToLoad()
    {
        $pagenames = $this->getPathParts();
        if (empty($pagenames)) {
            return ABSPATH . 'public/templates/home.php';
        }

        $stack = [];
        foreach ($pagenames as $pagename) {
            $stack[] = $pagename;
        }

        $found = false;
        while (!$found) {
            $template = ABSPATH . 'public/templates/' . implode('/', $stack) . '.php';
            if (file_exists($template)) {
                $found = $template;
            } else {
                array_pop($stack);
                if (count($stack) == 0) {
                    break;
                }
            }
        }

        if (!$found) {
            $found = ABSPATH . 'public/templates/404.php';
        }

        return $found;
    }

    /**
     * Get details about current request.
     *
     * @param  string $key
     * @return mixed
     */
    public function getDetails($key = '')
    {
        if ($key) {
            if (isset($this->_current[$key])) {
                return $this->_current[$key];
            } else {
                return null;
            }
        }
        
        return $this->_current;
    }

    /**
     * Set details about current request.
     *
     * @param  array $details
     * @return void
     */
    public function setDetails($details)
    {
        if (empty($details)) {
            return false;
        }

        foreach ($details as $key => $val) {
            // @todo: perform checks for special info
            $this->_current[$key] = $val;
        }
    }
}
