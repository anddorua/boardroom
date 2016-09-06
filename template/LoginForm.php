<form method="post">
    <fieldset>
        <legend>Boardroom login</legend>
        <div class="login-form-input">
            <label for="lf-login">Login</label>
            <input type="text" name="login" id="lf-login"
                   <?php
                    echo !empty($tpl_login_field_login) ? " value=\"" . addslashes($tpl_login_field_login) . "\"" : "";
                   ?>>
        </div>
        <div class="login-form-input">
            <label for="lf-password">Password</label>
            <input type="password" name="password" id="lf-password">
        </div>
        <div class="error-message-wide"<?php echo empty($tpl_login_error_message) ? " style=\"display:none;\"" : "";?>>
            <?php echo $tpl_login_error_message; ?>
        </div>
        <div>
            <input type="submit" value="Login">
        </div>
    </fieldset>
</form>
