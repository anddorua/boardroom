<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class Book implements BaseVidget
{
    use \Utility\DependencyInjection;
    public function render(array $appData, $templateName, \Core\Application $app, \Core\Database $db, \DBMappers\RoomItem $roomMapper, \DBMappers\EmpItem $empMapper)
    {
        $current_room = $app->getCurrentRoom();
        if ($current_room === false) {
            $rooms = $roomMapper->getAll($db);
            $app->setCurrentRoom($rooms[0]->getId());
            $current_room = $app->getCurrentRoom();
        }
        $roomItem = $roomMapper->getById($current_room, $db);
        $emps = $empMapper->getAll($db);

        if (isset($appData['book_crossings'])) {
            $message = 'Can\'t add appointment, it crosses existing appointments: ';
            foreach($appData['book_crossings'] as $cross) {
                $empItem = $empMapper->getById($cross->getEmpId(), $db);
                $message .= $empItem->getName();
                $message .= ' ' . $cross->getTimeStart()->format('M-j-Y H:i');
                $message .= '-' . $cross->getTimeEnd()->format('H:i') . ';';
            }
        }

        return (new \Utility\Template())->parse($templateName, array(
            'book_hour_mode' => $app->getHourMode(),
            'book_room_name' => $roomItem->getRoomName(),
            'book_emps' => $emps,
            'book_values' => isset($appData['book_values']) ? $appData['book_values'] : null,
            'book_errors' => isset($appData['book_errors']) ? $appData['book_errors'] : null,
        ));
    }
}