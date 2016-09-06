<?php
    function hideIfEmpty($value)
    {
        echo empty($value) ? " style=\"display:none\"" : '';
    }
    function checkedIfTrue($value)
    {
        echo $value ? ' checked' : '';
    }
    function fillOptions(array $arr, $selectValue = null)
    {
        foreach($arr as $key => $value) {
            echo "<option value=\"" . $key . "\"" . ($selectValue && $key == $selectValue ? ' selected' : '') . ">" . $value . "</option>";
        }
    }
    $monthArray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
    $dayArray = array();
    for ($i = 1; $i <= 31; $i++) {
        $dayArray[$i] = sprintf('%\'.02u', $i);
    }
    $yearArray = array();
    for ($i = 2015; $i <= 2020; $i++) {
        $yearArray[$i] = sprintf('%\'.04u', $i);
    }
    $hours12 = array();
    for ($i = 1; $i <= 12; $i++) {
        $hours12[$i] = sprintf('%u', $i);
    }
    $hours24 = array();
    for ($i = 0; $i <= 23; $i++) {
        $hours24[$i] = sprintf('%u', $i);
    }
    $minutesArray = array();
    $i = 0;
    while ($i < 60) {
        $minutesArray[$i] = sprintf('%\'.02u', $i);
        $i += 5;
    }
    $empArray = array();
    foreach($tpl_book_emps as $emp) {
        $empArray[$emp->getId()] = $emp->getName();
    }
    $meridiemArray = array('am' => 'AM', 'pm' => 'PM');
/*    $d = (new \DateTime())->setDate(2015,2,31);
    error_log("\ndate:" . $d->format('Y-m-d'), 3, 'my_errors.txt');
    $d = \DateTime::createFromFormat('Y-m-d', '2015-02-31');
    error_log("\ndate:" . $d->format('Y-m-d'), 3, 'my_errors.txt');*/
/*    $d = new \DateTime('2015-10-14 00:02:00');
    error_log("\ntime:" . $d->format('g:i a'), 3, 'my_errors.txt');
    $d = new \DateTime('2015-10-14 12:02:00');
    error_log("\ntime:" . $d->format('g:i a'), 3, 'my_errors.txt');*/


?>
<form method="post">
    <fieldset>
        <legend><?php echo $tpl_book_room_name;?> booking</legend>

        1. Booked for:<br>
        <select name="employee" size="1">
            <?php fillOptions($empArray, is_array($tpl_book_values) ? $tpl_book_values['employee'] : null);?>
        </select><br><br>
        2. I would like to book this meeting:<br>
            <select name="start-month" size="1">
                <?php fillOptions($monthArray, is_array($tpl_book_values) ? $tpl_book_values['start-month'] : null);?>
            </select>
            <select name="start-day" size="1">
                <?php fillOptions($dayArray, is_array($tpl_book_values) ? $tpl_book_values['start-day'] : null);?>
            </select>
            <select name="start-year" size="1">
                <?php fillOptions($yearArray, is_array($tpl_book_values) ? $tpl_book_values['start-year'] : null);?>
            </select>
            <div class="book-input-error-message" <?php \Utility\HtmlHelper::HideIfEmptyOrNull($tpl_book_errors, 'start-date');?>><?php \Utility\HtmlHelper::EchoIfPresent($tpl_book_errors, 'start-date');?></div>
        <br><br>
        3. Specify what the time and end of the meeting (This will be what people see on the calendar.)<br>
            <select name="start-hour-12" size="1" <?php hideIfEmpty($tpl_book_hour_mode == \Application\EmpItem::MODE_DAY_12);?>>
                <?php fillOptions($hours12, is_array($tpl_book_values) ? $tpl_book_values['start-hour-12'] : null);?>
            </select>
            <select name="start-hour-24" size="1" <?php hideIfEmpty($tpl_book_hour_mode == \Application\EmpItem::MODE_DAY_24);?>>
                <?php fillOptions($hours24, is_array($tpl_book_values) ? $tpl_book_values['start-hour-24'] : null);?>
            </select>
            <select name="start-minute" size="1">
                <?php fillOptions($minutesArray, is_array($tpl_book_values) ? $tpl_book_values['start-minute'] : null);?>
            </select>
            <select name="start-meridiem" size="1"<?php hideIfEmpty($tpl_book_hour_mode == \Application\EmpItem::MODE_DAY_12);?>>
                <?php fillOptions($meridiemArray, is_array($tpl_book_values) ? $tpl_book_values['start-meridiem'] : null);?>
            </select>
            <br><br>
            <select name="end-hour-12" size="1" <?php hideIfEmpty($tpl_book_hour_mode == \Application\EmpItem::MODE_DAY_12);?>>
                <?php fillOptions($hours12, is_array($tpl_book_values) ? $tpl_book_values['end-hour-12'] : null);?>
            </select>
            <select name="end-hour-24" size="1" <?php hideIfEmpty($tpl_book_hour_mode == \Application\EmpItem::MODE_DAY_24);?>>
                <?php fillOptions($hours24, is_array($tpl_book_values) ? $tpl_book_values['end-hour-24'] : null);?>
            </select>
            <select name="end-minute" size="1">
                <?php fillOptions($minutesArray, is_array($tpl_book_values) ? $tpl_book_values['end-minute'] : null);?>
            </select>
            <select name="end-meridiem" size="1"<?php hideIfEmpty($tpl_book_hour_mode == \Application\EmpItem::MODE_DAY_12);?>>
                <?php fillOptions($meridiemArray, is_array($tpl_book_values) ? $tpl_book_values['end-meridiem'] : null);?>
            </select>
            <div class="book-input-error-message" <?php \Utility\HtmlHelper::HideIfEmptyOrNull($tpl_book_errors, 'time');?>><?php \Utility\HtmlHelper::EchoIfPresent($tpl_book_errors, 'time');?></div>
        <br><br>
        4. Enter the specifics for the meeting. (This will be what people see when the click on an event link.)<br>
            <textarea name="notes" wrap="soft" rows="3" cols="40" required><?php \Utility\HtmlHelper::EchoIfPresent($tpl_book_values, 'notes') ?></textarea>
            <div class="book-input-error-message" <?php \Utility\HtmlHelper::HideIfEmptyOrNull($tpl_book_errors, 'notes');?>><?php \Utility\HtmlHelper::EchoIfPresent($tpl_book_errors, 'notes');?></div>
        <br><br>
        <?php
            // default radio values
            $recurringValue = $tpl_book_values ? $tpl_book_values['recurring'] : 1;
            $recurringPeriodValue = $tpl_book_values ? $tpl_book_values['recurring-period'] : 1;
        ?>
        5. Is this going to be a recurring event?<br>
            <label><input type="radio" name="recurring" value="1" <?php checkedIfTrue($recurringValue == 1);?>> no</label><br>
            <label><input type="radio" name="recurring" value="2" <?php checkedIfTrue($recurringValue == 2);?>> yes</label>
        <br><br>
        6. If it recurring, specify weekly, bi-weekly, or monthly.<br>
            <label><input type="radio" name="recurring-period" value="1" <?php checkedIfTrue($recurringPeriodValue == 1);?>> weekly</label><br>
            <label><input type="radio" name="recurring-period" value="2" <?php checkedIfTrue($recurringPeriodValue == 2);?>> bi-weekly</label><br>
            <label><input type="radio" name="recurring-period" value="3" <?php checkedIfTrue($recurringPeriodValue == 3);?>> monthly</label>
        <br><br>
        If weekly or bi-weekly, specify the number of weeks for it to keep recurring. If monthly, specify the number of months. (If you choose "bi-weekly" and put in an odd number of weeks, the computer will round down.)<br><br>
            <label><input type="number" min="0" max="4" name="duration" size="3" style="width:4em;" value="<?php \Utility\HtmlHelper::EchoIfPresent($tpl_book_values, 'duration');?>"> duration (max 4 weeks)</label>
            <div class="book-input-error-message" <?php \Utility\HtmlHelper::HideIfEmptyOrNull($tpl_book_errors, 'duration');?>><?php \Utility\HtmlHelper::EchoIfPresent($tpl_book_errors, 'duration');?></div>
        <br>
        <hr>
        <div>
            <input type="submit" value="Submit">
        </div>
    </fieldset>
</form>
