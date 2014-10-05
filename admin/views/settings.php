<?php

/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://qedev.com
 * @since      1.0.0
 *
 * @package    TKChecker
 * @subpackage TKChecker/admin/views
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
<h2>Mercury Settings</h2>
	<form method="post" action="options.php">
		<?php settings_fields('tk_settings'); ?>
		<?php do_settings_sections('tk-checker/admin/views/settings.php'); ?>
		<button type="submit" class="btn btn-primary">Save Changes</button>
	</form>
</div>