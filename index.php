<?php
ini_set('display_errors', 'Off');
ini_set('log_errors', 'On');
ini_set("error_log", "my_errors.txt");
error_reporting(E_ALL);

require_once("include/constants.php");
require_once("include/autoload.php");
require_once("include/database_creation.php");
$registry = new \Core\Registry();
$registry->set(REG_SITE_ROOT, pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME) . '/');
$registry->set(REG_SESSION, new \Core\Session());
$registry->set(REG_APP, new Core\Application(
    // state templates [state] => [template file name]
    array(
        Core\Application::STATE_LOGIN => "LoginTemplate.html",
        Core\Application::STATE_BROWSE => "BrowseTemplate.php",
        Core\Application::STATE_EMPLOYEE => "EmployeeTemplate.php",
        Core\Application::STATE_EMPLOYEE_LIST => "EmployeeListTemplate.php",
        Core\Application::STATE_BOOK => "BookTemplate.php",
        Core\Application::STATE_DETAILS => "DetailsTemplate.php",
        Core\Application::STATE_DETAILS_RETURN => "DetailsReturnTemplate.html",
        Core\Application::STATE_REDIRECT => null,
    ),
    // vidget templates [vidget class name (like in data-vidgets attribute)] => [template file name]
    array(
        'LoginForm' => 'LoginForm.php',
        'Navigation' => 'Navigation.php',
        'Informer' => 'Informer.php',
        'Messages' => 'Messages.php',
        'Calendar' => 'Calendar.php',
        'SideMenu' => 'SideMenu.php',
        'Error' => 'Error.php',
        'EmpListMessage' => 'EmpListMessage.php',
        'Employee' => 'Employee.php',
        'EmployeeList' => 'EmployeeList.php',
        'PeriodNavigator' => 'PeriodNavigator.php',
        'Book' => 'Book.php',
        'DetailsCaption' => 'DetailsCaption.php',
        'Details' => 'Details.php',
    ),
    $registry->get(REG_SITE_ROOT),
    $registry->get(REG_SESSION)
));

$registry->set(REG_DB, new \Core\Database(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
    DB_USER,
    DB_PASSWORD,
    'appointments',
    $database_creation));

//error_log("\nserver" . print_r($_SERVER, true), 3, 'my_errors.txt');
//error_log("\nget" . print_r($_GET, true), 3, 'my_errors.txt');

(new \Core\Router())->start($registry);
$registry->get(REG_APP)->renderState($registry);

