<?php
$caption = \Utility\DateHelper::FormatTimeAccordingRule($tpl_details_appointment->getTimeStart(), $tpl_hour_mode)
    . ' - '
    . \Utility\DateHelper::FormatTimeAccordingRule($tpl_details_appointment->getTimeEnd(), $tpl_hour_mode);
echo $caption;

