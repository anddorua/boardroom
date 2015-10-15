<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class Navigation implements BaseVidget
{
    public function render(array $appData, $templateName, \Core\Registry $registry)
    {
        $rooms = (new \DBMappers\RoomItem())->getAll($registry->get(REG_DB));
        $nav_items = array();
        foreach ($rooms as $room) {
            $nav_items[] = array(
                'caption' => $room->getRoomName(),
                'link' => $registry->get(REG_SITE_ROOT) . BROWSE_URL . "/room/" . $room->getId(),
                'selected' => false //$room_id_selected == $room->getId()
            );
        }
        $nav_items[] = array(
            'caption' => 'Settings',
            'link' => $registry->get(REG_SITE_ROOT) . 'employee/edit/' . $registry->get(REG_APP)->getEmpId($registry),
            'selected' => false
        );
        $nav_items[] = array(
            'caption' => 'Logout',
            'link' => $registry->get(REG_SITE_ROOT) . LOGIN_URL,
            'selected' => false
        );
        return (new \Utility\Template())->parse($templateName, array(
            'nav_items' => $nav_items,
        ));
    }
}