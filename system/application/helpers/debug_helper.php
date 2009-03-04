<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    function debug($var = false, $showHtml = false, $showFrom = true) {
        if ($showFrom) {
            $calledFrom = debug_backtrace();
            echo '<strong>' . $calledFrom[0]['file'] . '</strong>';
            echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
        }
        echo "\n<pre>\n";

        $var = print_r($var, true);
        if ($showHtml) {
            $var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
        }
        echo $var . "\n</pre>\n";
    }

?>