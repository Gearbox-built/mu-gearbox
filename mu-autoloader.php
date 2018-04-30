<?php
/**
 * Plugin Name: Gearbox Autoloader
 * Plugin URI: https://gearboxbuilt.com
 * Description: Sub-directory must-use plugin autoloader.
 * Author: Gearbox
 * Author URI: https://gearboxbuilt.com
 * Version: 0.1.0
 */

$directories = glob(dirname(__FILE__) . '/*' , GLOB_ONLYDIR);

foreach($directories as $directory) {
    if(file_exists($directory . '/' . basename($directory) . ".php")) {
        require($directory . '/' . basename($directory) . ".php");
    }
}
