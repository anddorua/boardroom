<div class="logo">Boardroom booker</div>
<ul class="navigation">
<?php
    for ($i = 0; $i < count($tpl_nav_items); $i++) {
        echo "<li><a href=\"" . $tpl_nav_items[$i]['link'] . "\">" . $tpl_nav_items[$i]['caption'] . "</a></li>";
    }
?>
</ul>

