<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:25
 */

namespace Controllers;


class Book extends BaseController
{

    private function hoursMerToFull($hours, $meridiem)
    {
        if ($meridiem == 'am') {
            return $hours;
        } else {
            if ($hours < 12) {
                return $hours + 12;
            } else {
                return 0;
            }
        }
    }

    private function validateForm($bookValues, &$bookErrors, &$bookingData, $hour_mode)
    {
        if (\Utility\Validator::IsFieldNotEmpty($bookValues, 'employee')) {
            $bookingData['employee'] = $bookValues['employee'];
        } else {
            $bookErrors['employee'] = 'employee should be filled.';
        }
        if (\Utility\Validator::IsDateValid($bookValues['start-year'], $bookValues['start-month'], $bookValues['start-day'])) {
            $bookingData['start-date'] = (new \DateTime())->setDate($bookValues['start-year'], $bookValues['start-month'], $bookValues['start-day']);
        } else {
            $bookErrors['start-date'] = 'date invalid';
            return;
        }
        if ($hour_mode == \Application\EmpItem::MODE_DAY_12) {
            $hours = $this->hoursMerToFull($bookValues['start-hour-12'], $bookValues['start-meridiem']);
        } else {
            $hours = $bookValues['start-hour-24'];
        }
        $bookingData['start-date']->setTime($hours, $bookValues['start-minute']);
        // нельзя назначить на прошлый период
        //error_log("\nstart-date:" . print_r($bookingData['start-date']->getTimestamp(), true), 3, 'my_errors.txt');
        //error_log("\nnow:" . print_r((new \DateTime())->getTimestamp(), true), 3, 'my_errors.txt');
        if ($bookingData['start-date']->getTimestamp() < (new \DateTime())->getTimestamp()) {
            $bookErrors['common'] = 'you cannot book for passed time';
        }
        $bookingData['end-date'] = (new \DateTime())->setTimestamp($bookingData['start-date']->getTimestamp());
        if ($hour_mode == \Application\EmpItem::MODE_DAY_12) {
            $hours = $this->hoursMerToFull($bookValues['end-hour-12'], $bookValues['end-meridiem']);
        } else {
            $hours = $bookValues['end-hour-24'];
        }
        $bookingData['end-date']->setTime($hours, $bookValues['end-minute']);
        // корректировка - если у даты окончания часы и минуты - нули, то это полночь следующего дня
        if ($bookingData['end-date']->format('G') == 0 && $bookingData['end-date']->format('i') == 0) {
            $bookingData['end-date']->add(new \DateInterval('P1D'));
        }
        if ($bookingData['start-date']->getTimestamp() - $bookingData['end-date']->getTimestamp() == 0) {
            $bookErrors['time'] = 'time cannot be equal';
        } else if ($bookingData['start-date']->getTimestamp() - $bookingData['end-date']->getTimestamp() > 0) {
            $bookErrors['time'] = 'start time should be less than end time';
        }
        if (\Utility\Validator::IsFieldNotEmpty($bookValues, 'notes')) {
            $bookingData['notes'] = $bookValues['notes'];
        } else {
            $bookErrors['notes'] = 'notes should be filled.';
        }
        $bookingData['recurring'] = $bookValues['recurring'] == 2;
        if ($bookValues['recurring'] == 2) {
            $bookingData['recurring-period'] = $bookValues['recurring-period'];
            if (\Utility\Validator::IsFieldNotEmpty($bookValues, 'duration')) {
                if (!is_numeric($bookValues['duration'])) {
                    $bookErrors['duration'] = 'duration should be numeric.';
                } else {
                    $bookingData['duration'] = $bookValues['duration'];
                }
            } else {
                $bookErrors['duration'] = 'duration should be filled.';
            }
        }
    }

    public function act(\Core\Registry $registry, $urlParameters, \Core\Http $http)
    {
        $app = $registry->get(REG_APP);
        if ($http->getRequestMethod() == 'GET') {
            $app->setStateBook(array());
        } else if ($http->getRequestMethod() == 'POST') {
            $bookErrors = array();
            $bookValues = array_merge(array(), $http->post());
            $bookingData = array();
            $this->validateForm($bookValues, $bookErrors, $bookingData, $app->getHourMode());
            //error_log("\nbookingData:" . print_r($bookingData, true), 3, 'my_errors.txt');

            if ($this->isEmptyValues($bookErrors)) {
                $appMatcher = new \Application\AppointmentMatcher();
                $chain = $appMatcher->makeChain($bookingData, $app->getEmpId(), $app->getCurrentRoom());
                $db = $registry->get(REG_DB);
                $crossings = $appMatcher->getCrossingAppointments($chain, new \DBMappers\AppointmentItem(), $db);
                // test for crossing appointments
                if (count($crossings) > 0) {

                    $message = \Utility\HtmlHelper::MakeCrossingMessage($crossings,  new \DBMappers\EmpItem(), $db);

                    $app->setStateBook(array(
                        'book_values' => $bookValues,
                        'book_errors' =>$bookErrors,
                        'error_message' => $message,
                        'book_crossings' => $crossings
                    ));
                } else {
                    $appMapper = new \DBMappers\AppointmentItem();
                    $max_chain_id = $appMapper->getMaxChainId($db);
                    if ($max_chain_id === false) {
                        $max_chain_id = 1;
                    } else {
                        ++$max_chain_id;
                    }
                    $chain->setChainId($max_chain_id);
                    foreach($chain as $appointment) {
                        $appMapper->save($appointment, $db);
                    }
                    $chain->rewind();
                    $message = '<span style="font-weight:normal">The event <strong>'
                        . \Utility\DateHelper::FormatTimeAccordingRule($chain->current()->getTimeStart(), $app->getHourMode())
                        . ' - '
                        . \Utility\DateHelper::FormatTimeAccordingRule($chain->current()->getTimeEnd(), $app->getHourMode())
                        . '</strong> has been added.<br>'
                        . 'The text for this event is: '
                        . $chain->current()->getNotes()
                        . '</span>';
                    $app->setMessage($message);
                    $app->setStateRedirect(BROWSE_URL);
                }
            } else {
                $app->setStateBook(array(
                    'book_values' => $bookValues,
                    'book_errors' =>$bookErrors,
                    'error_message' => isset($bookErrors['common']) ? $bookErrors['common'] : null,
                ));
            }
        }
    }
}