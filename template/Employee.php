<?php
    function hideIfEmpty($value)
    {
        echo empty($value) ? " style=\"display:none\"" : '';
    }
    function checkedIfTrue($value)
    {
        echo $value ? ' checked' : '';
    }
?>
<script type="text/javascript">
    function setAdminProxy() {
        document.getElementById("ef-is_admin_proxy").value = document.getElementById("ef-is_admin").checked ? "1" : "0";
    }
    function setDropPasswordProxy() {
        document.getElementById("ef-drop_password_proxy").value = document.getElementById("ef-drop_password").checked ? "1" : "0";
    }
</script>
<form method="post">
    <fieldset>
        <legend>Employee settings</legend>
        <div class="employee-form-input">
            <label for="ef-login">Login</label>
            <input type="text" name="login" id="ef-login" value="<?php echo $tpl_emp_edit['item']->getLogin();?>" required>
            <div class="emp-input-error-message" <?php hideIfEmpty($tpl_emp_err['login']);?>><?php echo $tpl_emp_err['login'];?></div>
        </div>
        <div class="employee-form-input">
            <label for="ef-name">Real name</label>
            <input type="text" name="name" id="ef-name" value="<?php echo $tpl_emp_edit['item']->getName();?>" required>
            <div class="emp-input-error-message" <?php hideIfEmpty($tpl_emp_err['name']);?>><?php echo $tpl_emp_err['name'];?></div>
        </div>
        <div class="employee-form-input">
            <label for="ef-email">E-mail</label>
            <input type="email" name="email" id="ef-email" value="<?php echo $tpl_emp_edit['item']->getEmail();?>" required>
            <div class="emp-input-error-message" <?php hideIfEmpty($tpl_emp_err['email']);?>><?php echo $tpl_emp_err['email'];?></div>
        </div>
        <div class="employee-form-input"<?php hideIfEmpty($tpl_is_editor_admin);?>>
            <label for="ef-is_admin"> </label>
            <input type="checkbox" onchange="setAdminProxy();" name="is_admin" id="ef-is_admin"<?php checkedIfTrue($tpl_emp_edit['item']->isAdmin())?>> admin user
            <input type="hidden" name="is_admin_proxy" id="ef-is_admin_proxy" value="<?php echo $tpl_emp_edit['item']->isAdmin() ? '1' : '0'; ?>">
        </div>
        <div class="employee-form-input">
            <label for="ef-hour_mode">Hour mode</label>
            <div id="ef-hour_mode" style="display:inline-block">
                <input type="radio" value="12" name="hour_mode"<?php checkedIfTrue($tpl_emp_edit['item']->getHourMode() == \Application\EmpItem::MODE_DAY_12)?>> 12
                <input type="radio" value="24" name="hour_mode"<?php checkedIfTrue($tpl_emp_edit['item']->getHourMode() == \Application\EmpItem::MODE_DAY_24)?>> 24
            </div>
        </div>
        <div class="employee-form-input">
            <label for="ef-first_day">First day of week</label>
            <div id="ef-first_day" style="display:inline-block">
                <input type="radio" value="0" name="first_day"<?php checkedIfTrue($tpl_emp_edit['item']->getFirstDay() == \Application\EmpItem::FIRST_DAY_SUNDAY)?>> Sunday
                <input type="radio" value="1" name="first_day"<?php checkedIfTrue($tpl_emp_edit['item']->getFirstDay() == \Application\EmpItem::FIRST_DAY_MONDAY)?>> Monday
            </div>
        </div>
        <div <?php hideIfEmpty(!$tpl_emp_edit['add_new']);?>>
            <hr>
            <div class="employee-form-input" <?php hideIfEmpty(!$tpl_emp_edit['edit_own']);?>>
                <label for="ef-drop_password"> </label>
                <input type="checkbox" onchange="setDropPasswordProxy();" value="1" name="drop_password" id="ef-drop_password"> drop password
                <input type="hidden" name="drop_password_proxy" id="ef-drop_password_proxy" value="0">
            </div>
            <div class="employee-form-input" <?php hideIfEmpty($tpl_emp_edit['edit_own']);?>>
                <label for="ef-password">Current password</label>
                <input type="password" name="password" id="ef-password">
            </div>
            <div class="employee-form-input" <?php hideIfEmpty($tpl_emp_edit['edit_own']);?>>
                <div style="margin-left:186px;font-size:60%">fill new password only if you want to change it, otherwise left blank</div>
                <label for="ef-new_password">New password</label>
                <input type="password" name="new_password" id="ef-new_password">
            </div>
            <div class="employee-form-input" <?php hideIfEmpty($tpl_emp_edit['edit_own']);?>>
                <label for="ef-new_password_retype">Retype new password</label>
                <input type="password" name="new_password_retype" id="ef-new_password_retype">
                <div class="emp-input-error-message" <?php hideIfEmpty($tpl_emp_err['password']);?>><?php echo $tpl_emp_err['password'];?></div>
            </div>
        </div>
        <hr>
        <div>
            <input type="submit" value="Save">
        </div>
    </fieldset>
</form>
