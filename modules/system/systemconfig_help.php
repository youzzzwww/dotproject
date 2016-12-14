<?php  // $Id: systemconfig_help.php 5872 2009-04-25 00:09:56Z merlinyoda $
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$cn = $_REQUEST['cn'];
?>
<table cellspacing="0" cellpadding="3" border="0" class="std" width="100%" align="center">
	<tr>
 		<td align="left" colspan="2"><?php echo $AppUI->_($cn.'_tooltip'); ?></td>
	</tr>
</table>
