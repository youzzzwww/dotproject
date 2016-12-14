<?php /* $Id: index.php 6149 2012-01-09 11:58:40Z ajdonnison $ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$hid = dPgetCleanParam($_GET, 'hid', 'help.toc');

$inc = DP_BASE_DIR.'/modules/help/'.$AppUI->user_locale.'/'.$hid.'.hlp';

if (!file_exists($inc)) {
	$inc = DP_BASE_DIR.'/modules/help/en/'.$hid.'.hlp';
	if (!file_exists($inc)) {
		$hid = "help.toc";
		$inc = DP_BASE_DIR.'/modules/help/'.$AppUI->user_locale.'/'.$hid.'.hlp';
		if (!file_exists($inc)) {
		  $inc = DP_BASE_DIR.'/modules/help/en/'.$hid.'.hlp';
		}
	}
}
if ($hid != 'help.toc') {
	echo '<a href="?m=help&amp;dialog=1">' . $AppUI->_('index') . '</a>';
}
readfile($inc);
?>
