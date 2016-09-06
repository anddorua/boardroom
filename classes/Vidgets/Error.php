<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class Error implements BaseVidget
{
    use \Utility\DependencyInjection;
    public function render(array $appData, $templateName)
    {
        return (new \Utility\Template())->parse($templateName, array(
            'error_message' => isset($appData['error_message']) ? $appData['error_message'] : null
        ));
    }
}