<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:25
 */

namespace Controllers;


class Employee extends BaseController
{
    use \Utility\DependencyInjection;
    public function act($urlParameters, \Core\Http $http, \Core\Application $app, \Core\Database $db, \DBMappers\EmpItem $empMapper)
    {
        $this->edit($urlParameters, $http, $app, $db, $empMapper);
    }

    private function validateLogin($value)
    {
        return empty($value) ? "field required" : '';
    }

    private function validateName($value)
    {
        return empty($value) ? "field required" : '';
    }

    private function validateEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) === false ? "email invalid" : '';
    }

    private function validatePassword(\Application\EmpItem $empItem, $value)
    {
        return $empItem->isPasswordEqual($value) ? '' : 'invalid current password';
    }

    private function addNewEmployee(\Core\Http $http, \Core\Application $app, \Core\Database $db, \DBMappers\EmpItem $empMapper)
    {
        $empItem = new \Application\EmpItem(array(
            'login' => $http->post()['login'],
            'email' => $http->post()['email'],
            'hour_mode' => $http->post()['hour_mode'],
            'first_day' => $http->post()['first_day'],
            'name' => $http->post()['name'],
            'is_admin' => $http->post()['is_admin_proxy']
        ));
        $emp_err = array();
        $emp_err['login'] = $this->validateLogin($empItem->getLogin());
        $emp_err['name'] = $this->validateName($empItem->getName());
        $emp_err['email'] = $this->validateEmail($empItem->getEmail());
        $emp_err['password'] = '';
        if ($this->isEmptyValues($emp_err)) {
            $empMapper->save($empItem, $db);
            $app->setMessage('Employee ' . $empItem->getName() . ' added successfully.');
            $app->setStateRedirect(EMPLOYEE_LIST_URL);
            //error_log("\nredirect to:" . print_r(BROWSE_URL, true), 3, 'my_errors.txt');
        } else {
            $app->setStateEmployee(array(
                'emp_edit' => array(
                    'item' => $empItem,
                    'edit_own' => false,
                    'add_new' => true
                ),
                'emp_err' => $emp_err
            ));
        }
    }


    public function add($urlParameters, \Core\Http $http, \Core\Application $app, \Core\Database $db, \DBMappers\EmpItem $empMapper)
    {
        if ($http->getRequestMethod() == 'GET') {
            $empItem = new \Application\EmpItem(array());
            $this->showEmployee($empItem, false, true, $app);
        } else if ($http->getRequestMethod() == 'POST') {
            $this->addNewEmployee($http, $app, $db, $empMapper);
        }
    }

    private function saveExistedEmployee($urlParameters, \Core\Http $http, \Core\Application $app, \Core\Database $db, \DBMappers\EmpItem $empMapper)
    {
        $isOwnAccount = isset($urlParameters[0]) && $urlParameters[0] == $app->getEmpId();
        $empItem = $empMapper->getById($urlParameters[0], $db);
        $empItem->fromArray(array(
            'login' => $http->post()['login'],
            'email' => $http->post()['email'],
            'hour_mode' => $http->post()['hour_mode'],
            'first_day' => $http->post()['first_day'],
            'name' => $http->post()['name'],
        ));
        // check for is_admin field
        if ($app->isAdmin()) {
            $empItem->fromArray(array(
                'is_admin' => $http->post()['is_admin_proxy']
            ));
        }
        $emp_err = array();
        $emp_err['login'] = $this->validateLogin($empItem->getLogin());
        $emp_err['name'] = $this->validateName($empItem->getName());
        $emp_err['email'] = $this->validateEmail($empItem->getEmail());
        // пароль редактируется только если для своего аккаунта, в противном случае мы только можем сбросить пароль
        if ($isOwnAccount) {
            $emp_err['password'] = $this->validatePassword($empItem, $http->post()['password']);
            // check for new password setting
            // 1) check if we must setup new password
            if ($empItem->isPasswordEqual(null) && empty($http->post()['new_password']) && empty($http->post()['new_password_retype'])) {
                $emp_err['password'] = 'you must setup new password, blank not allowed';
            } else if (!(empty($http->post()['new_password']) && empty($http->post()['new_password_retype']))){
                if ($http->post()['new_password'] != $http->post()['new_password_retype']) {
                    $emp_err['password'] = 'retyped password incorrect, type again';
                } else {
                    $empItem->setPwd($http->post()['new_password']);
                    $emp_err['password'] = '';
                }
            }
        } else {
            // we may drop password
            if ($http->post()['is_admin_proxy'] == 1) {
                $empItem->dropPwd();
                $emp_err['password'] = '';
            }
        }
        // success or reenter form
        if ($this->isEmptyValues($emp_err)) {
            $empMapper->save($empItem, $db);
            $app->setMessage('Employee ' . $empItem->getName() . ' modified successfully.');
            if ($isOwnAccount)  {
                $app->setStateRedirect(BROWSE_URL);
            } else {
                $app->setStateRedirect(EMPLOYEE_LIST_URL);
            }
            //error_log("\nredirect to:" . print_r(BROWSE_URL, true), 3, 'my_errors.txt');
        } else {
            $app->setStateEmployee(array(
                'emp_edit' => array(
                    'item' => $empItem,
                    'edit_own' => true,
                    'add_new' => false
                ),
                'emp_err' => $emp_err
            ));
        }
    }

    private function showEmployee(\Application\EmpItem $empItem, $isOwnAccount, $isAddNew, \Core\Application $app)
    {
        $app->setStateEmployee(array(
            'emp_edit' => array(
                'item' => $empItem,
                'edit_own' => $isOwnAccount,
                'add_new' => $isAddNew
            ),
            'emp_err' => array(
                'login' => '',
                'name' => '',
                'email' => '',
                'password' => ''
            )
        ));
    }

    public function edit($urlParameters, \Core\Http $http, \Core\Application $app, \Core\Database $db, \DBMappers\EmpItem $empMapper)
    {
        // может редактировать:
        // 1) свой аккаунт, если $urlParameters[0] == app->getEmpId
        // 2) чужой аккаунт, если $urlParameters[0] != app->getEmpId && app->isAdmin()
        if (!isset($urlParameters[0])) {
            $app->setStateRedirect(BROWSE_URL);
        } else {
            if ($http->getRequestMethod() == 'POST') {
                $this->saveExistedEmployee($urlParameters, $http, $app, $db, $empMapper);
            } else if ($http->getRequestMethod() == 'GET') {
                $this->showEmployee(
                    $empMapper->getById($urlParameters[0], $db),
                    isset($urlParameters[0]) && $urlParameters[0] == $app->getEmpId(),
                    false,
                    $app);
            }
        }
    }
    public function remove($urlParameters, \Core\Http $http, \Core\Application $app, \Core\Database $db, \DBMappers\EmpItem $empMapper)
    {
        if (!$app->isAdmin() || $http->getRequestMethod() != 'POST') {
            $app->setMessage('You cannot manage employees.');
            $app->setStateRedirect(BROWSE_URL);
        } else {
            if (isset($urlParameters[0])) {
                $empItem = $empMapper->getById($urlParameters[0], $db);
                $empMapper->remove($empItem->getId(), $db);
                $app->setMessage('Employee ' . $empItem->getName() . ' removed successfully.');
                $app->setStateRedirect(EMPLOYEE_LIST_URL);
            } else {
                $app->setMessage('Employee id not set');
                $app->setStateRedirect(EMPLOYEE_LIST_URL);
            }
        }
    }
}