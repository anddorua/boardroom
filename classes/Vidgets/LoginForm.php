<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class LoginForm implements BaseVidget
{
    use \Utility\DependencyInjection;
    public function render(array $appData, $templateName)
    {
        $login_error_message = isset($appData['login_error_message']) ? $appData['login_error_message'] : '';
        return (new \Utility\Template())->parse($templateName, array(
            'login_error_message' => $login_error_message,
            'login_field_login' => isset($appData['login_field_login']) ? $appData['login_field_login'] : ''
        ));
    }
}