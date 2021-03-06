<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:25
 */

namespace Controllers;


class Details extends BaseController
{
    use \Utility\DependencyInjection;
    private function validateForm($bookValues, &$bookErrors, \Application\BookingChange &$bookingData)
    {
        if (\Utility\Validator::IsFieldNotEmpty($bookValues, 'start')) {
            $bookingData->setStart($bookValues['start']);
        } else {
            $bookErrors['time'] = 'start time should be filled.';
            return;
        }
        if (\Utility\Validator::IsFieldNotEmpty($bookValues, 'end')) {
            $bookingData->setEnd($bookValues['end']);
        } else {
            $bookErrors['time'] = 'end time should be filled.';
            return;
        }

        if (!$bookingData->isTimeValid()) {
            $bookErrors['time'] = $bookingData->getErrorMessage();
        }

        if (\Utility\Validator::IsFieldNotEmpty($bookValues, 'notes')) {
            $bookingData->setNotes($bookValues['notes']);
        } else {
            $bookErrors['notes'] = 'notes should be filled.';
        }
        $bookingData->setEmpId($bookValues['employee']);
        $bookingData->setApplyChain($bookValues['apply_chain_proxy'] == 1);
    }

    private function getValuesArray(\Application\AppointmentItem $appointment)
    {
        $values = array();
        $values['start'] = $appointment->getTimeStart()->format('H:i');
        $values['end'] = $appointment->getTimeEnd()->format('H:i');
        $values['notes'] = $appointment->getNotes();
        $values['submitted'] = $appointment->getSubmitted()->format('Y-m-d H:i:s');
        $values['employee'] = $appointment->getEmpId();
        return $values;
    }

    public function act($urlParameters, \Core\Http $http, \Core\Application $app, \Core\Database $db, \DBMappers\AppointmentItem $appMapper, \DBMappers\EmpItem $empItemMapper)
    {
        $appointment = $appMapper->getById($urlParameters[0], $db);
        $chain = $appMapper->getChain($appointment->getChain(), $db);
        $full_chain_count = $chain->count();
        $chain->applyFilter(new \DateTime());
        $can_modify = $chain->count() > 0;
        $values = $this->getValuesArray($appointment);
        $values['apply_chain_proxy'] = 0;
        $detailsErrors = array();
        if ($http->getRequestMethod() == 'GET') {
            $app->setStateDetails(array(
                'details_appointment' => $appointment,
                'details_errors' => $detailsErrors,
                'details_values' => $values,
                'can_modify' => $can_modify,
                'is_chain' => $full_chain_count > 1,
                'hour_mode' => $app->getHourMode()
            ));
        } else {
            //error_log("\npost:" . print_r($http->post(), true), 3, 'my_errors.txt');
            $values = array_merge(array(), $http->post());
            $bookingData = new \Application\BookingChange();
            $this->validateForm($values, $detailsErrors, $bookingData);
            if ($this->isEmptyValues($detailsErrors)) {
                //error_log("\nBookData:" . print_r($bookingData, true), 3, 'my_errors.txt');
                if ($bookingData->isApplyChain()) {
                    $chain->applyChange($bookingData);
                } else {
                    $chain->applyChangeToMember($appointment->getId(), $bookingData);
                }
                $appMatcher = new \Application\AppointmentMatcher();
                $crossings = $appMatcher->getCrossingAppointments($chain, $appMapper, $db);
                if (count($crossings) > 0) {
                    $message = \Utility\HtmlHelper::MakeCrossingMessage($crossings,  $empItemMapper, $db);
                    $app->setStateDetails(array(
                        'details_appointment' => $appointment,
                        'details_errors' => $detailsErrors,
                        'details_values' => $values,
                        'can_modify' => $can_modify,
                        'error_message' => $message,
                        'is_chain' => $chain->count() > 1,
                        'hour_mode' => $app->getHourMode()
                    ));
                } else {
                    if ($bookingData->isApplyChain()) {
                        foreach($chain as $member) {
                            $appMapper->save($member, $db);
                        }
                        $chain->rewind();
                        if ($chain->count() > 0) {
                            $appointment = $chain->current();
                        } else {
                            $appointment = null;
                        }
                    } else {
                        $appointment = $chain->get($appointment->getId());
                        if ($chain->isMeetFilter($appointment)) {
                            $appMapper->save($chain->get($appointment->getId()), $db);
                        } else {
                            $appointment = null;
                        }
                    }
                    if (is_null($appointment)) {
                        $message = "No changes were applied.";
                    } else {
                        $message = '<span style="font-weight:normal">The event has been modified to <strong>'
                            . \Utility\DateHelper::FormatTimeAccordingRule($appointment->getTimeStart(), $app->getHourMode())
                            . ' - '
                            . \Utility\DateHelper::FormatTimeAccordingRule($appointment->getTimeEnd(), $app->getHourMode())
                            . '</strong>.<br>'
                            . 'The text for this event is: '
                            . $appointment->getNotes()
                            . '</span>';
                    }
                    $app->setMessage($message);
                    $app->setStateRedirect(DETAILS_RETURN_URL);
                }
            } else {
                $app->setStateDetails(array(
                    'details_appointment' => $appointment,
                    'details_errors' => $detailsErrors,
                    'details_values' => $values,
                    'can_modify' => $can_modify,
                    'is_chain' => $chain->count() > 1,
                    'hour_mode' => $app->getHourMode()
                ));
            }
        }
    }
    public function edit($urlParameters, \Core\Http $http, \Core\Application $app, \Core\Database $db, \DBMappers\AppointmentItem $appMapper, \DBMappers\EmpItem $empItemMapper)
    {
        $this->act($urlParameters, $http, $app, $db, $appMapper, $empItemMapper);
    }
    public function delete($urlParameters, \Core\Http $http, \Core\Application $app, \Core\Database $db, \DBMappers\AppointmentItem $appMapper)
    {
        $appointment = $appMapper->getById($urlParameters[0], $db);
        $chain = $appMapper->getChain($appointment->getChain(), $db);
        $chain->applyFilter(new \DateTime());
        $deleted_count = 0;
        if ($http->post()['apply_chain_proxy'] == 1) {
            foreach($chain as $member) {
                $appMapper->deleteById($member->getId(), $db);
                ++$deleted_count;
            }
        } else {
            if ($chain->isMeetFilter($appointment)) {
                $appMapper->deleteById($appointment->getId(), $db);
                ++$deleted_count;
            }
        }
        if ($deleted_count > 0) {
            $message = "$deleted_count events were deleted successfully.";
        } else {
            $message = 'No events were deleted.';
        }
        //error_log("\ndelete:" . print_r($urlParameters, true), 3, 'my_errors.txt');
        $app->setMessage($message);
        $app->setStateRedirect(DETAILS_RETURN_URL);
    }

}