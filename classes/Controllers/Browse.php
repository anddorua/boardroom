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
    use \Utility\DependencyInjection;
    private function setBrowseTime(\Core\Application $app, \Core\Http $http){
        $cp = $app->getCurrentPeriod();
        $cp_year = $cp->format('Y');
        $cp_month = $cp->format('n');
        if (isset($http->get()['year'])) {
            $cp_year = $http->get()['year'];
        }
        if (isset($http->get()['month'])) {
            $cp_month = $http->get()['month'];
        }
        $app->setCurrentPeriod((new \DateTime())->setDate($cp_year, $cp_month, 1));
    }

    public function act($urlParameters, \Core\Http $http, \Core\Application $app, \Core\Database $db, \DBMappers\RoomItem $roomMapper)
    {
        $this->setBrowseTime($app, $http);
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

    public function room($urlParameters, \Core\Http $http, \Core\Application $app, \Core\Database $db, \DBMappers\RoomItem $roomMapper)
    {
        if (isset($urlParameters[0])) {
            $app->setCurrentRoom($urlParameters[0]);
        }
        $this->act($urlParameters, $http, $app, $db, $roomMapper);
    }
}