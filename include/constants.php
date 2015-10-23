<?php
/**
 * Created by PhpStorm.
 * User: AD
 * Date: 08.10.15
 * Time: 23:59
 */

/**
 *  This section should be edited before site run
 */
define('DB_HOST', 'localhost'); // mysql server host
define('DB_NAME', 'boardroom');
//define('DB_NAME', 'bdr_test'); // database name
define('DB_USER', 'root'); // database user
define('DB_PASSWORD', ''); // user password

/**
 *  Do NOT edit following lines
 */

define('CLASS_ROOT', 'classes');
define('TEMPLATE_ROOT', 'template');
define('TEMPLATE_VAR_PREFIX', 'tpl');
define('DEFAULT_CONTROLLER', 'DefaultController');
define('LOGIN_CONTROLLER', 'Login');
define('EMPLOYEE_CONTROLLER', 'Employee');
define('DEFAULT_ACTION', 'act');
define('CONTROLLER_NAMESPACE', '\\Controllers');
define('VIDGET_NAMESPACE', '\\Vidgets');
define('BROWSE_URL', 'browse');
define('LOGIN_URL', 'login');
define('EMPLOYEE_URL', 'employee');
define('EMPLOYEE_LIST_URL', 'employee-list');
define('BOOK_URL', 'book');
define('DETAILS_URL', 'details');
define('DETAILS_RETURN_URL', 'details-return');
//define('REG_SESSION', 'session');
//define('REG_DB', 'db');
//define('REG_APP', 'app');
//define('REG_HTTP', 'http');
define('REG_SITE_ROOT', 'site_root');
define('DI_PREFIX', 'DI_');
