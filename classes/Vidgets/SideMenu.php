<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class SideMenu implements BaseVidget
{
    public function render(array $appData, $templateName, \Core\Registry $registry)
    {
        $site_root = $registry->get(REG_SITE_ROOT);
        $nav_items = array(
            array('caption' => 'Book It!', 'link' => $site_root . BOOK_URL),
        );
        if ($registry->get(REG_APP)->isAdmin($registry)) {
            $nav_items[] = array('caption' => 'Employee List', 'link' => $site_root . EMPLOYEE_LIST_URL);
        }
        return (new \Utility\Template())->parse($templateName, array('nav_items' => $nav_items));
    }
}