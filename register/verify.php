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
    // activate the account
    if($_GET['roleid']=='') {
        $role = $user->getRoleByName('Guest', 'guest');
	}

	if ($user->checkHasRole()) {
        $GLOBALS['TEMPLATE']['content'] = '<p><strong>User already has been activated ' .
			'.</strong></p> ';
	} else {
		//add the user to table gacl_aro and gacl_groups_aro_map
		$user->addLogin($role);
        $GLOBALS['TEMPLATE']['content'] = '<p><strong>Dear user ' .$user->username.
			'.</strong></p> <p>Congratulation, activate success.</p>';
        $GLOBALS['TEMPLATE']['content'].='<a href="http://'.
			$_SERVER['HTTP_HOST'].'/dotproject/index.php">'.
			'Click here to login page.';
	}
}

// display the page
if (!empty($GLOBALS['TEMPLATE']['content']))
{
    echo $GLOBALS['TEMPLATE']['content'];
}

?>
</body>
</html>
