<script type="text/javascript">
    function removeOnConfirm(el) {
        if (window.confirm("Are you sure to remove employee " + el.dataset.name + "?")) {
            var empIdToDelete = el.dataset.employee;
            var forms = document.querySelectorAll("form");
            for (var i = 0; i < forms.length; i++) {
                if (forms[i].dataset.employee == empIdToDelete) {
                    forms[i].submit();
                }
            }
        }
        return false;
    }
</script>
<ul class="employee-list">
    <?php
    for ($i = 0; $i < count($tpl_item_list); $i++) {
        echo "<li>";
        echo "<div class=\"emp-list-name\"><a href=\"mailto:" . $tpl_item_list[$i]['emp']->getEmail() . "\">" . $tpl_item_list[$i]['emp']->getName() . "</a></div>";
        echo "<div class=\"emp-list-remove\"><a onclick=\"return removeOnConfirm(this)\" href=\"#\" data-employee=\"" . $tpl_item_list[$i]['emp']->getId() . "\" data-name=\"" . $tpl_item_list[$i]['emp']->getName() . "\">REMOVE</a>";
        echo "<form method=\"POST\" data-employee=\"" . $tpl_item_list[$i]['emp']->getId() . "\" action=\"" . $tpl_item_list[$i]['remove_link'] . "\"></form>";
        echo "</div>";
        echo "<div class=\"emp-list-edit\"><a href=\"" . $tpl_item_list[$i]['edit_link'] . "\">EDIT</a></div>";
        echo "</li>";
    }
    ?>
</ul>
<a class="emp-add-btn" href="<?php echo $tpl_emp_add_link;?>">Add new employee</a>
