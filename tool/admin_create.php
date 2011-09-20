<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_BASICLTI);

require_once('forms.php');

if (isset($_POST['cancel'])) {
        $msg->addFeedback('CANCELLED');
        header('Location: '.AT_BASE_HREF.'mods/basiclti/index_admin.php');
        exit;
} else if (isset($_POST['form_basiclti'])) {
    if ( at_form_validate($blti_admin_form, $msg) ) {
    	global $addslashes;
        $sql = "SELECT count(*) cnt FROM ".TABLE_PREFIX."basiclti_tools WHERE toolid = '".
		$addslashes($_POST['toolid'])."';";
        $result = mysql_query($sql, $db) or die(mysql_error());
        $row = mysql_fetch_assoc($result);

        if ($row["cnt"] != 0) {
           $msg->addFeedback('NEED_UNIQUE_TOOLID');
	} else {
            $sql = at_form_insert($_POST, $blti_admin_form);
            $sql = 'INSERT INTO '.TABLE_PREFIX."basiclti_tools ".$sql;
            $result = mysql_query($sql, $db) or die(mysql_error());
            write_to_log(AT_ADMIN_LOG_INSERT, 'basiclti_create', mysql_affected_rows($db), $sql);
            $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');


            header('Location: '.AT_BASE_HREF.'mods/basiclti/index_admin.php');
            exit;
        }
    }
}

include(AT_INCLUDE_PATH.'header.inc.php');

$msg->printAll();

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];  ?>" name="basiclti_form" enctype="multipart/form-data">
  <input type="hidden" name="form_basiclti" value="true" />
  <div class="input-form">
    <fieldset class="group_form"><legend class="group_form"><?php echo _AT('properties'); ?></legend>
<?php at_form_generate($_POST, $blti_admin_form); ?>
        <div class="buttons">
                <input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
                <input type="submit" name="cancel" value="<?php echo _AT('cancel');?>" />
        </div>
    </fieldset>
  </div>
</form>

<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
