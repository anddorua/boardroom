<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class Details implements BaseVidget
{
    use \Utility\DependencyInjection;
    public function render(array $appData, $templateName, \Core\Registry $registry, \Core\Database $db, \DBMappers\EmpItem $empMapper)
    {
        $emps = $empMapper->getAll($db);

        return (new \Utility\Template())->parse($templateName, array(
            'details_errors' => $appData['details_errors'],
            'details_values' => $appData['details_values'],
            'is_chain' => $appData['is_chain'],
            'details_emps' => $emps,
            'can_modify' => $appData['can_modify'],
            'site_root' => $registry->get(REG_SITE_ROOT),
            'appointment_id' => $appData['details_appointment']->getId(),
        ));
    }
}