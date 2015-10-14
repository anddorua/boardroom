<script type="text/javascript">
    function removeOnConfirm(el) {
        if (window.confirm("Are you sure to remove employee " + el.dataset.name + "?")) {
            window.location.href = el.dataset.link;
        }
    }
</script>
<ul class="employee-list">
    <?php
    for ($i = 0; $i < count($tpl_item_list); $i++) {
        echo "<li>";
        echo "<div class=\"emp-list-name\"><a href=\"mailto:" . $tpl_item_list[$i]['emp']->getEmail() . "\">" . $tpl_item_list[$i]['emp']->getName() . "</a></div>";
        echo "<div class=\"emp-list-remove\"><a onclick=\"removeOnConfirm(this)\" href=\"#\" data-link=\"" . $tpl_item_list[$i]['remove_link'] . "\" data-name=\"" . $tpl_item_list[$i]['emp']->getName() . "\">REMOVE</a></div>";
        echo "<div class=\"emp-list-edit\"><a href=\"" . $tpl_item_list[$i]['edit_link'] . "\">EDIT</a></div>";
        echo "</li>";
    }
    ?>
</ul>
<a class="emp-add-btn" href="<?php echo $tpl_emp_add_link;?>">Add new employee</a>
