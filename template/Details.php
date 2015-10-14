<?php
    function hideIfEmpty($value)
    {
        echo empty($value) ? " style=\"display:none\"" : '';
    }
    function checkedIfTrue($value)
    {
        echo $value ? ' checked' : '';
    }
    function readonlyIfFalse($value)
    {
        echo !$value ? ' readonly' : '';
    }
    $empArray = array();
    foreach($tpl_details_emps as $emp) {
        $empArray[$emp->getId()] = $emp->getName();
    }
?>
<script type="text/javascript">
    function setApplyChainProxy() {
        document.getElementById("df-apply_chain_proxy").value = document.getElementById("df-apply_chain").checked ? "1" : "0";
    }
    function deleteOnConfirm(el) {
        return window.confirm("Are you sure to delete event?");
    }
</script>
<form method="post">
    <fieldset>
        <legend>B.B. DETAILS</legend>
        <div class="details-form-input">
            <label for="df-start">When:</label>
            <input type="time" name="start" id="df-start" value="<?php \Utility\HtmlHelper::EchoIfPresent($tpl_details_values, 'start') ?>" required<?php readonlyIfFalse($tpl_can_modify)?>> -
            <input type="time" name="end" id="df-end" value="<?php \Utility\HtmlHelper::EchoIfPresent($tpl_details_values, 'end') ?>" required<?php readonlyIfFalse($tpl_can_modify)?>>
            <div class="det-input-error-message" <?php \Utility\HtmlHelper::HideIfEmptyOrNull($tpl_details_errors, 'time');?>><?php \Utility\HtmlHelper::EchoIfPresent($tpl_details_errors, 'time');?></div>
        </div>
        <div class="details-form-input">
            <label for="df-notes">Notes:</label>
            <textarea id="df-notes" name="notes" wrap="soft" rows="3" cols="30" required<?php readonlyIfFalse($tpl_can_modify)?>><?php \Utility\HtmlHelper::EchoIfPresent($tpl_details_values, 'notes') ?></textarea>
            <div class="det-input-error-message" <?php \Utility\HtmlHelper::HideIfEmptyOrNull($tpl_details_errors, 'notes');?>><?php \Utility\HtmlHelper::EchoIfPresent($tpl_details_errors, 'notes');?></div>
        </div>
        <div class="details-form-input">
            <label for="df-emp">Who:</label>
            <select id="df-emp" name="employee" size="1"<?php readonlyIfFalse($tpl_can_modify)?>>
                <?php \Utility\HtmlHelper::EchoOptions($empArray, is_array($tpl_details_values) ? $tpl_details_values['employee'] : null);?>
            </select>
        </div>
        <div class="details-form-input">
            <label for="df-submitted">Submitted:</label>
            <input type="text" name="submitted" id="df-submitted" value="<?php \Utility\HtmlHelper::EchoIfPresent($tpl_details_values, 'submitted');?>" readonly>
        </div>
        <div class="details-form-input"<?php hideIfEmpty($tpl_is_chain);?>>
            <label class="checkbox-label" style="width:inherit;">
            <input type="checkbox" onchange="setApplyChainProxy();" name="apply_chain" id="df-apply_chain"<?php checkedIfTrue(\Utility\HtmlHelper::ValueOrFalse($tpl_details_values,'apply_chain_proxy')==1)?><?php readonlyIfFalse($tpl_can_modify)?>> Apply to all occurencies?
            </label>
            <input type="hidden" name="apply_chain_proxy" id="df-apply_chain_proxy" value="<?php echo \Utility\HtmlHelper::ValueOrFalse($tpl_details_values,'apply_chain_proxy') == 1 ? '1' : '0'; ?>">
        </div>

        <hr>
        <div <?php hideIfEmpty($tpl_can_modify)?>>
            <input type="submit" value="Update">
            <button onclick="return deleteOnConfirm(this);" formaction="<?php echo $tpl_site_root . DETAILS_URL . '/delete/' . $tpl_appointment_id?>">Delete</button>
<!--            <input type="button" value="Delete" onclick="return deleteOnConfirm(this);">
-->        </div>
    </fieldset>
</form>
