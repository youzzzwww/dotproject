<?php
$baseDir = dirname(dirname(__FILE__));
define('DP_BASE_DIR', $baseDir);
require_once DP_BASE_DIR . '/base.php';
require_once DP_BASE_DIR . '/includes/config.php';
require_once DP_BASE_DIR . '/includes/main_functions.php';
require_once DP_BASE_DIR.'/lib/adodb/adodb.inc.php';

$db = NewADOConnection(dPgetConfig('dbtype'));
if (!$db->Connect(dPgetConfig('dbhost'), dPgetConfig('dbuser'),
		dPgetConfig('dbpass'), dPgetConfig('dbname')))
{
	echo 'error to connect db';
}

require_once DP_BASE_DIR.'/classes/permissions.class.php';
$perms = new dPacl;
$perms->regeneratePermissions();
echo 'generate completed';
?>
