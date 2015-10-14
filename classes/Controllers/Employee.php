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
    public function act(\Core\Registry $registry, $urlParameters)
    {
        $registry->get(REG_APP)->setStateEmployee(array());
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

    private function addNewEmployee(\Core\Registry $registry, $urlParameters)
    {
        $empItem = new \Application\EmpItem(array(
            'login' => $_POST['login'],
            'email' => $_POST['email'],
            'hour_mode' => $_POST['hour_mode'],
            'first_day' => $_POST['first_day'],
            'name' => $_POST['name'],
            'is_admin' => $_POST['is_admin_proxy']
        ));
        $emp_err = array();
        $emp_err['login'] = $this->validateLogin($empItem->getLogin());
        $emp_err['name'] = $this->validateName($empItem->getName());
        $emp_err['email'] = $this->validateEmail($empItem->getEmail());
        $emp_err['password'] = '';
        $app = $registry->get(REG_APP);
        if ($this->isEmptyValues($emp_err)) {
            $empMapper = new \DBMappers\EmpItem();
            $empMapper->save($empItem, $registry->get(REG_DB));
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


    public function add(\Core\Registry $registry, $urlParameters)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $empItem = new \Application\EmpItem(array());
            $this->showEmployee($registry, $empItem, false, true);
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->addNewEmployee($registry, $urlParameters);
        }
    }

    private function saveExistedEmployee(\Core\Registry $registry, $urlParameters)
    {
        $app = $registry->get(REG_APP);
        $isOwnAccount = isset($urlParameters[0]) && $urlParameters[0] == $app->getEmpId($registry);
        $empMapper = new \DBMappers\EmpItem();
        $empItem = $empMapper->getById($urlParameters[0], $registry->get(REG_DB));
        $empItem->fromArray(array(
            'login' => $_POST['login'],
            'email' => $_POST['email'],
            'hour_mode' => $_POST['hour_mode'],
            'first_day' => $_POST['first_day'],
            'name' => $_POST['name'],
        ));
        // check for is_admin field
        if ($app->isAdmin($registry)) {
            $empItem->fromArray(array(
                'is_admin' => $_POST['is_admin_proxy']
            ));
        }
        $emp_err = array();
        $emp_err['login'] = $this->validateLogin($empItem->getLogin());
        $emp_err['name'] = $this->validateName($empItem->getName());
        $emp_err['email'] = $this->validateEmail($empItem->getEmail());
        // пароль редактируется только если для своего аккаунта, в противном случае мы только можем сбросить пароль
        if ($isOwnAccount) {
            $emp_err['password'] = $this->validatePassword($empItem, $_POST['password']);
            // check for new password setting
            // 1) check if we must setup new password
            if ($empItem->isPasswordEqual(null) && empty($_POST['new_password']) && empty($_POST['new_password_retype'])) {
                $emp_err['password'] = 'you must setup new password, blank not allowed';
            } else if (!(empty($_POST['new_password']) && empty($_POST['new_password_retype']))){
                if ($_POST['new_password'] != $_POST['new_password_retype']) {
                    $emp_err['password'] = 'retyped password incorrect, type again';
                } else {
                    $empItem->setPwd($_POST['new_password']);
                    $emp_err['password'] = '';
                }
            }
        } else {
            // we may drop password
            if ($_POST['is_admin_proxy'] == 1) {
                $empItem->dropPwd();
                $emp_err['password'] = '';
            }
        }
        // success or reenter form
        if ($this->isEmptyValues($emp_err)) {
            $empMapper->save($empItem, $registry->get(REG_DB));
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

    private function showEmployee(\Core\Registry $registry, \Application\EmpItem $empItem, $isOwnAccount, $isAddNew)
    {
        $registry->get(REG_APP)->setStateEmployee(array(
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

    public function edit(\Core\Registry $registry, $urlParameters)
    {
        // может редактировать:
        // 1) свой аккаунт, если $urlParameters[0] == app->getEmpId
        // 2) чужой аккаунт, если $urlParameters[0] != app->getEmpId && app->isAdmin()
        $app = $registry->get(REG_APP);
        if (!isset($urlParameters[0])) {
            $urlParameters[0] = $app->getEmpId();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->saveExistedEmployee($registry, $urlParameters);
        } else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->showEmployee(
                $registry,
                (new \DBMappers\EmpItem())->getById($urlParameters[0], $registry->get(REG_DB)),
                isset($urlParameters[0]) && $urlParameters[0] == $app->getEmpId(),
                false);
        }
    }
    public function remove(\Core\Registry $registry, $urlParameters)
    {
        $app = $registry->get(REG_APP);
        if (!$app->isAdmin()) {
            $app->setMessage('You cannot manage employees.');
            $app->setStateRedirect(BROWSE_URL);
        } else {
            if (isset($urlParameters[0])) {
                $mapper = new \DBMappers\EmpItem();
                $db = $registry->get(REG_DB);
                $empItem = $mapper->getById($urlParameters[0], $db);
                $mapper->remove($empItem->getId(), $registry->get(REG_DB));
                $app->setMessage('Employee ' . $empItem->getName() . ' removed successfully.');
                $app->setStateRedirect(EMPLOYEE_LIST_URL);
            } else {
                $app->setMessage('Employee id not set');
                $app->setStateRedirect(EMPLOYEE_LIST_URL);
            }
        }
    }
}