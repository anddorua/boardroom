<?php
ini_set('display_errors', 'Off');
ini_set('log_errors', 'On');
ini_set("error_log", "my_errors.txt");
error_reporting(E_ALL);

require_once("include/constants.php");
require_once("include/autoload.php");
// state templates [state] => [template file name]
$templateMap = array(
    Core\Application::STATE_LOGIN => "LoginTemplate.html",
    Core\Application::STATE_BROWSE => "BrowseTemplate.php",
    Core\Application::STATE_EMPLOYEE => "EmployeeTemplate.php",
    Core\Application::STATE_EMPLOYEE_LIST => "EmployeeListTemplate.php",
    Core\Application::STATE_BOOK => "BookTemplate.php",
    Core\Application::STATE_DETAILS => "DetailsTemplate.php",
    Core\Application::STATE_DETAILS_RETURN => "DetailsReturnTemplate.html",
    Core\Application::STATE_REDIRECT => null,
);
// vidget templates [vidget class name (like in data-vidgets attribute)] => [template file name]
$vidgetViews = array(
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
);
$registry = new \Core\Registry();
$registry->set(REG_SITE_ROOT, pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME) . '/');
$registry->set(REG_SESSION, new \Core\Session());
$registry->set(REG_APP, new Core\Application($registry->get(REG_SITE_ROOT), $registry->get(REG_SESSION)));
$registry->set(REG_DB, new \Core\Database(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
    DB_USER,
    DB_PASSWORD,
    'appointments',
    '\\Utility\\DatabaseCreateScript'));

(new \Core\Router())->start($registry);
$app = $registry->get(REG_APP);
if ($app->getState() == \Core\Application::STATE_REDIRECT) {
    $app->redirect($app->getAppData()[\Core\Application::SECTION_REDIRECT]);
} else {
    echo (new \Core\View($vidgetViews))->renderState($app->getState(), $app->getAppData(), $templateMap, $registry);
}


