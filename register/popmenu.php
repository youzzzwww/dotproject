<?php
include './User.php';
require_once '../base.php';
require_once DP_BASE_DIR . '/includes/config.php';
require_once (DP_BASE_DIR . '/includes/main_functions.php');
require_once (DP_BASE_DIR . '/includes/db_connect.php');

header('Content-Type:text/xml');
ob_start();
echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';

if(isset($_GET['company_id']))
{
    $q = 'SELECT dept_id,dept_name FROM departments WHERE dept_company='.
            trim($_GET['company_id']);
	$rs = db_loadList($q);
	foreach ($rs as $row) 
	{
        echo '<dept>';
        echo '<id>'.$row['dept_id'].'</id>';
        echo '<name>'.$row['dept_name'].'</name>';
        echo '</dept>';        
    }
    $xml = ob_get_clean();
    echo $xml;
}
?>
