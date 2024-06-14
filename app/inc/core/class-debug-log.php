<?php
/**
 * A convenient tool for logging information.
 * Useful during debugging.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 *
 * Usage:
 * 1. \RecyleBin\PhpSSS\DebugLog\log( 'some text...' )
 *      This will create a file general-debug.log inside the uploads folder you specified in config.php
 *      if that file doesn't exist already.
 *      It'll then write 'some text...' along with current date and time info, at the end of that file.
 * 
 * 2. \RecyleBin\PhpSSS\DebugLog\log( 'some text...', 'john' )
 *      This will create a file john-debug.log inside the uploads folder you specified in config.php
 *      if that file doesn't exist already.
 *      It'll then write 'some text...' along with current date and time info, at the end of that file.
 *
 */

namespace RecyleBin\PhpSSS;

class DebugLog
{

    public static function log($text, $group = '')
    {
        if (! \SS_DEBUG) {
            return false;
        }
        
        $target_file = self::getLogFilePath($group);
        
        if (is_array($text) || is_object($text)) {
            $text = json_encode($text);
        }
        
        try {
            $current_time = date(DATE_FORMAT, time()) . ' ' . date(TIME_FORMAT, time());
            $text = !empty($text) ? "\n" . $current_time . " :: " . $text . "\n" : '';
            $fh = fopen($target_file, 'a');
            fwrite($fh, $text);
            fclose($fh);
            
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public static function clearLog($group = '')
    {
        $target_file = self::getLogFilePath($group);
        
        @ unlink($target_file);
        return true;
    }
    
    public static function read($group = '', $separator = '<br>')
    {
        if (! \SS_DEBUG) {
            return false;
        }
        
        $file_contents = "";
        
        $target_file = self::getLogFilePath($group);
        try {
            $myfile = @ fopen($target_file, "r");
            if ($myfile) {
                while (!feof($myfile)) {
                    $file_contents .= fgets($myfile) . $separator;
                }
            }
        } catch (\Exception $ex) {
        }
        
        return $file_contents;
    }

    public static function getLogFilePath($group = '')
    {
        $group = trim($group);
        $group = $group ? $group : 'general';

        $upload_base_dir = ABSPATH . DIRECTORY_SEPARATOR . UPLOADS_DIR;
        return trailingslashit($upload_base_dir) . $group . '-debug.log';
    }
}
