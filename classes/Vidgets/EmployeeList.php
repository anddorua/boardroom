<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class EmployeeList implements BaseVidget
{
    public function render(array $appData, $templateName, \Core\Registry $registry)
    {
        $emp_list = (new \DBMappers\EmpItem())->getAll($registry->get(REG_DB));
        $item_list = array();
        $site_root = $registry->get(REG_SITE_ROOT);
        foreach($emp_list as $emp) {
            $item = array();
            $item['emp'] = $emp;
            $item['remove_link'] = $site_root . EMPLOYEE_URL . '/remove/' . $emp->getId();
            $item['edit_link'] = $site_root . EMPLOYEE_URL . '/edit/' . $emp->getId();
            $item_list[] = $item;
        }
        return (new \Utility\Template())->parse($templateName, array(
            'item_list' => $item_list,
            'emp_msg' => isset($appData['emp_msg']) ? $appData['emp_msg'] : '',
            'emp_add_link' => $site_root . EMPLOYEE_URL . '/add',
        ));
    }
}