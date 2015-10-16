<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:25
 */

namespace Controllers;


use Application\BookingOrder;

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

    private function validateForm($bookValues, &$bookErrors, \Application\BookingOrder &$bookingOrder, $hour_mode)
    {
        if (\Utility\Validator::IsFieldNotEmpty($bookValues, 'employee')) {
            $bookingOrder->setEmpId($bookValues['employee']);
        } else {
            $bookErrors['employee'] = 'employee should be filled.';
        }
        if (\Utility\Validator::IsDateValid($bookValues['start-year'], $bookValues['start-month'], $bookValues['start-day'])) {
            $bookingOrder->setDate($bookValues['start-year'], $bookValues['start-month'], $bookValues['start-day']);
        } else {
            $bookErrors['start-date'] = 'date invalid';
            return;
        }
        //error_log("\ndate set:" . print_r($bookingOrder, true), 3, 'my_errors.txt');

        if ($hour_mode == \Application\EmpItem::MODE_DAY_12) {
            $bookingOrder->setStartTime12($bookValues['start-hour-12'], $bookValues['start-minute'], $bookValues['start-meridiem']);
            $bookingOrder->setEndTime12($bookValues['end-hour-12'], $bookValues['end-minute'], $bookValues['end-meridiem']);
        } else {
            $bookingOrder->setStartTime24($bookValues['start-hour-24'], $bookValues['start-minute']);
            $bookingOrder->setEndTime24($bookValues['end-hour-24'], $bookValues['end-minute']);
        }
        //error_log("\ntime set:" . print_r($bookingOrder, true), 3, 'my_errors.txt');

        //error_log("\nstart-date:" . print_r($bookingData['start-date']->getTimestamp(), true), 3, 'my_errors.txt');
        if (!$bookingOrder->isTimeValid()) {
            $bookErrors['time'] = $bookingOrder->getErrorMessage();
        }
        // нельзя назначить на прошлый период
        if ($bookingOrder->isPeriodBeforeTime(new \DateTime())) {
            $bookErrors['common'] = 'you cannot book for passed time';
        }
        if (\Utility\Validator::IsFieldNotEmpty($bookValues, 'notes')) {
            $bookingOrder->setNotes($bookValues['notes']);
        } else {
            $bookErrors['notes'] = 'notes should be filled.';
        }
        if ($bookValues['recurring'] == 1) {
            $bookingOrder->setRecurring(\Application\BookingOrder::NOT_RECURRING);
        } else {
            if (\Utility\Validator::IsFieldNotEmpty($bookValues, 'duration') && is_numeric($bookValues['duration'])) {
                $cases = array(
                    1 => BookingOrder::RECURRING_WEEKLY,
                    2 => BookingOrder::RECURRING_BI_WEEKLY,
                    3 => BookingOrder::RECURRING_MONTHLY,
                );
                $bookingOrder->setRecurring($cases[$bookValues['recurring-period']], $bookValues['duration']);
                $bookValues['duration'] = $bookingOrder->getDuration();
            } else {
                $bookErrors['duration'] = 'duration should be filled with numeric value.';
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
            $bookingOrder = new \Application\BookingOrder();
            $this->validateForm($bookValues, $bookErrors, $bookingOrder, $app->getHourMode());
            //error_log("\nbookingData:" . print_r($bookingOrder, true), 3, 'my_errors.txt');

            if ($this->isEmptyValues($bookErrors)) {
                $appMatcher = new \Application\AppointmentMatcher();
                $chain = $appMatcher->makeChain($bookingOrder, $app->getEmpId(), $app->getCurrentRoom());
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
                    $message = \Utility\HtmlHelper::MakeSuccessAppCreationMessage($chain->current(), $app->getHourMode());
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