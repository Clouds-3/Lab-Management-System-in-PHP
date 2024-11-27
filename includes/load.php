
<?php
// -----------------------------------------------------------------------
// DEFINE SEPARATOR ALIASES
// -----------------------------------------------------------------------
define("URL_SEPARATOR", '/');
define("DS", DIRECTORY_SEPARATOR);

// -----------------------------------------------------------------------
// DEFINE ROOT PATHS
// -----------------------------------------------------------------------
defined('SITE_ROOT') ? null : define('SITE_ROOT', realpath(dirname(__FILE__)));
define("LIB_PATH_INC", SITE_ROOT . DS);

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// -----------------------------------------------------------------------
// INCLUDE REQUIRED FILES
// -----------------------------------------------------------------------
require_once(LIB_PATH_INC . 'config.php');   // Database configuration
require_once(LIB_PATH_INC . 'functions.php'); // Custom functions
require_once(LIB_PATH_INC . 'session.php');   // Session management
require_once(LIB_PATH_INC . 'upload.php');    // File upload handling
require_once(LIB_PATH_INC . 'database.php');  // Database interaction
require_once(LIB_PATH_INC . 'sql.php');       // SQL query functions

?>
