<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:25
 */

namespace Controllers;


class Browse extends BaseController
{
    private function setBrowseTime(\Core\Application $app){
        $cp = $app->getCurrentPeriod();
        $cp_year = $cp->format('Y');
        $cp_month = $cp->format('n');
        if (isset($_GET['year'])) {
            $cp_year = $_GET['year'];
        }
        if (isset($_GET['month'])) {
            $cp_month = $_GET['month'];
        }
        $app->setCurrentPeriod((new \DateTime())->setDate($cp_year, $cp_month, 1));
    }

    public function act(\Core\Registry $registry, $urlParameters)
    {
        $app = $registry->get(REG_APP);
        $db = $registry->get(REG_DB);
        $this->setBrowseTime($app);
        $roomMapper = new \DBMappers\RoomItem();
        $currentRoomId = $app->getCurrentRoom();
        if (false === $currentRoomId) {
            $roomList = $roomMapper->getAll($db);
            $roomItem = $roomList[0];
            $app->setCurrentRoom($roomItem->getId());
        } else {
            $roomItem = $roomMapper->getById($currentRoomId, $db);
        }
        //error_log("\nschedule:" . print_r($schedule, true), 3, 'my_errors.txt');

        $app->setStateBrowse(array(
            'browse_room_item' => $roomItem
        ));
    }

    public function room(\Core\Registry $registry, $urlParameters)
    {
        $app = $registry->get(REG_APP);
        if (isset($urlParameters[0])) {
            $app->setCurrentRoom($urlParameters[0]);
        }
        $this->act($registry, $urlParameters);
    }
}