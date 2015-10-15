<?php
    $day_names = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
/*    error_log("\nbrowseCalendar:" . print_r($tpl_browse_calendar, true), 3, 'my_errors.txt');
    error_log("\nbrowseCalendar:" . print_r($tpl_browse_first_day, true), 3, 'my_errors.txt');
    error_log("\nbrowseCalendar:" . print_r($tpl_browse_hour_mode, true), 3, 'my_errors.txt');
    error_log("\nbrowseCalendar:" . print_r($tpl_browse_period, true), 3, 'my_errors.txt');
    error_log("\nbrowseCalendar:" . print_r($tpl_browse_site_root, true), 3, 'my_errors.txt');*/
?>
<script type="text/javascript">
    function openDetailsWindow(el) {
        var win = window.open(el.dataset.link, "details", "left=100,top=100,width=420,height=300,menubar=no,toolbar=no,location=no,status=no");
        return false;
    }
</script>
<table>
    <thead><tr>
        <?php
            for ($i = 0; $i < 7; $i++) {
                $day_index = $i;
                if ($tpl_browse_first_day == \Application\EmpItem::FIRST_DAY_MONDAY) {
                    $day_index++;
                }
                if ($day_index == 7) {
                    $day_index = 0;
                }
                echo "<th>" . $day_names[$day_index] . "</th>";
            }
        ?>
    </tr></thead>
    <tbody>
    <?php
    // 1) looking the date of first table cell
    $period_first_date = \Utility\DateHelper::GetFirstDateInMonth($tpl_browse_period);
    $period_last_date = \Utility\DateHelper::GetLastDateInMonth($tpl_browse_period);
    $first_day_dow = $period_first_date->format('N');
    if ($tpl_browse_first_day == \Application\EmpItem::FIRST_DAY_MONDAY) {
        $days_back = $first_day_dow - 1;
    } else {
        $days_back = $first_day_dow == 7 ? 0 : $first_day_dow;
    }
    $first_cell_date = $period_first_date->sub(new DateInterval('P' . $days_back . 'D'));
    $cell_date = $first_cell_date;
    $cell_in_row = 0;
    while ($cell_date->diff($period_last_date)->format('%R%a') >= 0 || $cell_in_row != 7) {
        //error_log("\nwd:$watchdog cell_in_row=$cell_in_row diff=" . print_r($cell_date->diff($period_last_date)->format('%R%a'), true), 3, 'my_errors.txt');
        $cell_in_row = $cell_in_row == 7 ? 0 : $cell_in_row;
        if ($cell_in_row == 0) {
            echo "<tr class=\"cal-row\">";
        }
        echo "<td>";
        if (\Utility\DateHelper::IsDateInSamePeriod($tpl_browse_period, $cell_date)) {
            $day_index = $cell_date->format('j');
            echo "<div class=\"cal-day\">" . $day_index . "</div><br>";
            foreach($tpl_browse_calendar[$day_index] as $appItem) {
                echo "<div class=\"cal-entry\"><a href=\"#\" onclick=\"openDetailsWindow(this)\" data-link=\""
                    . $tpl_site_root . DETAILS_URL . "/edit/" . $appItem->getId() . "\">"
                    . \Utility\DateHelper::FormatTimeAccordingRule($appItem->getTimeStart(), $tpl_browse_hour_mode)
                    . " - "
                    . \Utility\DateHelper::FormatTimeAccordingRule($appItem->getTimeEnd(), $tpl_browse_hour_mode)
                    . "</a></div>";
            }
        }
        echo "</td>";
        if ($cell_in_row == 6) {
            echo "</tr>";
        }
        $cell_in_row++;
        $cell_date->add(new \DateInterval('P1D'));
    }
    //error_log("\ndayList:" . print_r($dayList, true), 3, 'my_errors.txt');
    ?>
    </tbody>
</table>
