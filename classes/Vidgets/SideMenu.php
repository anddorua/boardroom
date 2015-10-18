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
    use \Utility\DependencyInjection;
    public function render(array $appData, $templateName, \Core\Application $app, \Core\Registry $registry)
    {
        $site_root = $registry->get(REG_SITE_ROOT);
        $nav_items = array(
            array('caption' => 'Book It!', 'link' => $site_root . BOOK_URL),
        );
        if ($app->isAdmin()) {
            $nav_items[] = array('caption' => 'Employee List', 'link' => $site_root . EMPLOYEE_LIST_URL);
        }
        return (new \Utility\Template())->parse($templateName, array('nav_items' => $nav_items));
    }
}