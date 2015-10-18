<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class Employee implements BaseVidget
{
    use \Utility\DependencyInjection;
    public function render(array $appData, $templateName, \Core\Application $app)
    {
        //error_log('appData in vidget:' . print_r(appData, true), 3, 'my_errors.txt');
        return (new \Utility\Template())->parse($templateName, array(
            'emp_edit' => $appData['emp_edit'],
            'emp_err' => $appData['emp_err'],
            'is_editor_admin' => $app->isAdmin()
        ));
    }
}