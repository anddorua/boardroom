<?php
ini_set('display_errors', 'Off');
ini_set('log_errors', 'On');
ini_set("error_log", "my_errors.txt");
error_reporting(E_ALL);
date_default_timezone_set('Europe/Kiev'); // change if needed

require_once("include/constants.php");
require_once("include/autoload.php");
// state templates: [state] => [template file name]
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
// vidget templates: [vidget class name (like in data-vidgets attribute)] => [template file name]
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
\Utility\DependencyInjectionStorage::getInstance()->setPrefix(DI_PREFIX);
\Utility\DependencyInjectionStorage::getInstance()->addInstance($registry);
$http = new \Core\Http();
\Utility\DependencyInjectionStorage::getInstance()->addInstance($http);
$session = new \Core\Session();
\Utility\DependencyInjectionStorage::getInstance()->addInstance($session);
$app = new Core\Application($registry->get(REG_SITE_ROOT), $session);
\Utility\DependencyInjectionStorage::getInstance()->addInstance($app);
$db = new \Core\Database(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
    DB_USER,
    DB_PASSWORD,
    'appointments',
    '\\Utility\\DatabaseCreateScript'
);
\Utility\DependencyInjectionStorage::getInstance()->addInstance($db);
\Utility\DependencyInjectionStorage::getInstance()->addInstance(new \DBMappers\RoomItem());
\Utility\DependencyInjectionStorage::getInstance()->addInstance(new \DBMappers\AppointmentItem());
$empMapper = new \DBMappers\EmpItem();
\Utility\DependencyInjectionStorage::getInstance()->addInstance($empMapper);

(new \Core\Router())->start($http, $app, $db, $empMapper);
if ($app->getState() == \Core\Application::STATE_REDIRECT) {
    $http->redirect(
        $app->getRedirectUrl(),
        $registry->get(REG_SITE_ROOT));
} else {
    $http->setResponseBody((new \Core\View($vidgetViews))->renderState($app->getState(), $app->getAppData(), $templateMap, $registry->get(REG_SITE_ROOT)));
}


