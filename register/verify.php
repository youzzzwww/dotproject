<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="utf-8" xml:lang="utf-8" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>user activation</title>
</head>
<body>   
<?php
// include shared code
include './User.php';

// make sure a user id and roleid were received
if (!isset($_GET['uid']) || !isset($_GET['roleid']))
{
    $GLOBALS['TEMPLATE']['content'] = '<p><strong>receive wrong information ' .
        '.</strong></p> <p>please register again.</p>';
    $GLOBALS['TEMPLATE']['content'].='<a href="http://'.
     $_SERVER['HTTP_HOST'].'/dotProject/register/register.php">'.
                'click here';
}
// validate userid
else if (!$user = User::getById($_GET['uid']))
{
    $GLOBALS['TEMPLATE']['content'] = '<p><strong>There is no user need to actived.</strong>' .
        '</p> <p>Retry.</p>';
}
// make sure the account is not active
else
{
    //add the user to table gacl_aro
    $user->addLogin();
    // activate the account
    $userid = $_GET['uid'];
    if($_GET['roleid']=='')
        $role = 19;
    //else $role = $_GET['roleid'];
    
    // test to see if object & group exist and if object is already a member
    $query  = '
		SELECT		o.id AS id,g.id AS group_id,gm.group_id AS member
		FROM		'. 'gacl_aro' .' o
		LEFT JOIN	'. 'gacl_aro_groups' .' g ON g.id='. $role .'
		LEFT JOIN	'. 'gacl_groups_aro_map' .' gm ON (gm.'. 'aro_id=o.id AND gm.group_id=g.id)
		WHERE		(o.section_value='. '"user"' .' AND o.value='. $userid .')';
	$row = array();
    $rs = db_loadObject($query, $row);
    //Group_ID == Member
    if ($row[1] == $row[2])
    {
        $GLOBALS['TEMPLATE']['content'] = '<p><strong>User has been activated ' .
        '.</strong></p> ';
        
    }
    else
    {
        $object_id = $row[0];
        $query = 'INSERT INTO '. 'gacl_groups_aro_map' .' (group_id,'. 'aro_id) VALUES ('. $role .','. $object_id .')';
        db_exec($query);

        $GLOBALS['TEMPLATE']['content'] = '<p><strong>Dear user ' .$user->username.
        '.</strong></p> <p>Congratulation, activate success.</p>';
        $GLOBALS['TEMPLATE']['content'].='<a href="http://'.
        $_SERVER['HTTP_HOST'].'/dotProject/index.php">'.
                'Click here to login page';
    }  
    mysql_free_result($rs);    
}

// display the page
if (!empty($GLOBALS['TEMPLATE']['content']))
{
    echo $GLOBALS['TEMPLATE']['content'];
}

?>
</body>
</html>
