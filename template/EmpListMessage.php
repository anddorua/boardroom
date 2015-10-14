<?php
function hideIfEmpty($value)
{
    echo empty($value) ? " style=\"display:none\"" : '';
}
?>
<div class="message-panel"<?php hideIfEmpty($tpl_message)?>>
<?php echo $tpl_message;?>
</div>

