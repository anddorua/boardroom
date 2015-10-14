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
    private function checkTimeValidity($start, $end)
    {
        $t_start = date_parse($start);
        if ($t_start['error_count'] > 0) {
            return implode(',', $t_start['errors']);
        }
        $t_end = date_parse($end);
        if ($t_end['error_count'] > 0) {
            return implode(',', $t_end['errors']);
        }
        $t_start_min = $t_start['hour'] * 60 + $t_start['minute'];
        $t_end_min = $t_end['hour'] * 60 + $t_end['minute'];
        if ($t_start_min < $t_end_min || ($t_end['hour'] == 0 && $t_end['minute'] == 0)) {
            return '';
        } else {
            return 'start time should be less then end time';
        }
    }

    private function validateForm($bookValues, &$bookErrors, &$bookingData)
    {
        if (\Utility\Validator::IsFieldNotEmpty($bookValues, 'start')) {
            $bookingData['start'] = $bookValues['start'];
        } else {
            $bookErrors['time'] = 'start time should be filled.';
            return;
        }
        if (\Utility\Validator::IsFieldNotEmpty($bookValues, 'end')) {
            $bookingData['end'] = $bookValues['end'];
        } else {
            $bookErrors['time'] = 'end time should be filled.';
            return;
        }

        $bookErrors['time'] = $this->checkTimeValidity($bookValues['start'], $bookValues['end']);

        if (\Utility\Validator::IsFieldNotEmpty($bookValues, 'notes')) {
            $bookingData['notes'] = $bookValues['notes'];
        } else {
            $bookErrors['notes'] = 'notes should be filled.';
        }
        $bookingData['employee'] = $bookValues['employee'];
        $bookingData['apply_chain'] = $bookValues['apply_chain_proxy'];
    }

    public function act(\Core\Registry $registry, $urlParameters)
    {
        $db = $registry->get(REG_DB);
        $app = $registry->get(REG_APP);
        $appMapper = new \DBMappers\AppointmentItem();
        $appointment = $appMapper->getById($urlParameters[0], $db);
        $chain = $appMapper->getChain($appointment->getChain(), $db);
        $full_chain_count = $chain->count();
        $chain->applyFilter(new \DateTime());
        $can_modify = $chain->count() > 0;
        $values = array();
        $values['start'] = $appointment->getTimeStart()->format('H:i');
        $values['end'] = $appointment->getTimeEnd()->format('H:i');
        $values['notes'] = $appointment->getNotes();
        $values['submitted'] = $appointment->getSubmitted()->format('Y-m-d H:i:s');
        $values['employee'] = $appointment->getEmpId();
        $values['apply_chain_proxy'] = 0;
        $detailsErrors = array();
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $app->setStateDetails(array(
                'details_appointment' => $appointment,
                'details_errors' => $detailsErrors,
                'details_values' => $values,
                'can_modify' => $can_modify,
                'is_chain' => $full_chain_count > 1,
                'hour_mode' => $app->getHourMode()
            ));
        } else {
            //error_log("\npost:" . print_r($_POST, true), 3, 'my_errors.txt');
            $values = array_merge(array(), $_POST);
            $bookingData = array();
            $this->validateForm($values, $detailsErrors, $bookingData);
            if ($this->isEmptyValues($detailsErrors)) {
                //error_log("\nBookData:" . print_r($bookingData, true), 3, 'my_errors.txt');
                if ($bookingData['apply_chain'] == 1) {
                    $chain->applyChange($bookingData);
                } else {
                    $chain->applyChangeToMember($appointment->getId(), $bookingData);
                }
                $appMatcher = new \Application\AppointmentMatcher();
                $crossings = $appMatcher->getCrossingAppointments($chain, $appMapper, $db);
                if (count($crossings) > 0) {
                    $message = \Utility\HtmlHelper::MakeCrossingMessage($crossings,  new \DBMappers\EmpItem(), $db);
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
                    if ($bookingData['apply_chain'] == 1) {
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
    public function edit(\Core\Registry $registry, $urlParameters)
    {
        $this->act($registry, $urlParameters);
    }
    public function delete(\Core\Registry $registry, $urlParameters)
    {
        $db = $registry->get(REG_DB);
        $app = $registry->get(REG_APP);
        $appMapper = new \DBMappers\AppointmentItem();
        $appointment = $appMapper->getById($urlParameters[0], $db);
        $chain = $appMapper->getChain($appointment->getChain(), $db);
        $chain->applyFilter(new \DateTime());
        $deleted_count = 0;
        if ($_POST['apply_chain_proxy'] == 1) {
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