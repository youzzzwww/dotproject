<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="utf-8" xml:lang="utf-8" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="popmenu.js"></script>
    <link rel="stylesheet" type="text/css" href="../style/default/main.css" media="all" />
    <title>register</title>
</head>
<body>
    <?php
	require_once '../base.php';
	require_once DP_BASE_DIR . '/includes/config.php';
	require_once (DP_BASE_DIR . '/includes/main_functions.php');
	require_once (DP_BASE_DIR . '/includes/db_connect.php');
    include './User.php';
    include './libmail.php';

    session_start();
    header('Cache-control: private');
    ob_start();
       
    ?>
<table width="100%" border="0" cellpadding="0" cellspacing="1" height="400" class="std">
<form name="editFrm" action="register.php" method="post">
    <br/>
    <br/>
<tr>
    <td align="right" width="230">* <?php echo 'login name';?>:</td>
    <td>	
        <input type="text" class="text" name="username" value="<?php if (isset($_POST['username']))echo htmlspecialchars($_POST['username']); ?>" maxlength="255" size="40" />   
	</td></tr>
<tr>
    <td align="right">* <?php echo 'password';?>:</td>
    <td><input type="password" class="text" name="user_password" value="" maxlength="32" size="32" /> </td>
</tr>
<tr>
    <td align="right">* <?php echo 'reenter password';?>:</td>
    <td><input type="password" class="text" name="password_check" value="" maxlength="32" size="32" /> </td>
</tr>
<tr>
    <td align="right">* <?php echo 'real name';?>:</td>
    <td><input type="text" class="text" name="contact_first_name" value="<?php if (isset($_POST['contact_first_name']))echo htmlspecialchars($_POST['contact_first_name']); ?>" maxlength="50" /> <input type="text" class="text" name="contact_last_name" value="<?php if (isset($_POST['contact_last_name']))echo htmlspecialchars($_POST['contact_last_name']); ?>" maxlength="50" /></td>
</tr>
<tr>
    <td align="right"><?php echo 'company';?>:</td>
    <td>
        <input type="hidden" id="company" name="contact_company" value="<?php echo "";?>" />
        <select id="company_name">
            <option value="0">Company</option>
            <?php
            //get the company information from the database
            $q = 'SELECT company_id,company_name  FROM  companies';
            $rs = db_loadList($q);
			foreach ($rs as $row) 
			{
				echo '<option value="'.$row['company_id'].'">'.$row['company_name'].'</option>';
            }
            ?>
        </select>
    </td>
    <td><?php //echo $row['company_name'];?></td>
</tr>
<tr>
    <td align="right"><?php echo 'department';?>:</td>
    <td>
        <input type="hidden" id="department" name="contact_department" value="<?php echo "";?>" />
        <select id="dept_name">
            <option>Dept</option>
        </select>
    </td>
    <td><div id="dept_error"></div></td>
</tr>
<tr>
    <td align="right">* <?php echo "contact email";?>:</td>
    <td><input type="text" class="text" name="contact_email" value="<?php if (isset($_POST['contact_email']))echo htmlspecialchars($_POST['contact_email']);?>" maxlength="255" size="40" /> </td>
</tr>
<tr>
    <td align="right" valign=top><?php echo "user signature";?>:</td>
    <td><textarea class="text" cols=50 name="user_signature" style="height: 50px"><?php if (isset($_POST['user_signature']))echo htmlspecialchars($_POST['user_signature']);?></textarea></td>
</tr>
<tr>
   <td align="right">*captcha</label></td>
   <td><img src="./captcha.php?nocache=<?php echo time(); ?>" alt=""/><br />
   <input type="text" name="captcha" id="captcha"/></td>
</tr>
<tr>
    <td align="right">* <?php echo "information you must provide"; ?></td>
    <td></td>
<tr>
    <td align="right">
	<input type="submit" name="submit" value="submit"  class="button" />
    </td>
</tr>
</table>
</body>
</html>
<?php
$form = ob_get_clean(); 

// show the form if this is the first time the page is viewed
if (!isset($_POST['submit']))
{
    $GLOBALS['TEMPLATE']['content'] = $form;
}

// otherwise process incoming data
else
{
    // validate password
    $password1 = (isset($_POST['user_password'])) ? $_POST['user_password'] : '';
    $password2 = (isset($_POST['password_check'])) ? $_POST['password_check'] : '';
    $password = ($password1 && $password1 == $password2) ?
        md5($password1) : '';

    // validate CAPTCHA
    $captcha = (isset($_POST['captcha']) && 
        strtoupper($_POST['captcha']) == $_SESSION['captcha']);

    // add the record if all input validates
    if ($password &&
        $captcha &&
        User::validateUsername($_POST['username']) &&
        User::validateEmailAddr($_POST['contact_email']))
    {
        // make sure the user doesn't already exist
        $user = User::getByUsername($_POST['username']);
        if ($user->uid)
        {
            $GLOBALS['TEMPLATE']['content'] = '<p><strong>Sorry, the name' .
                'already exist.</strong></p> <p>Please use a different name ' .
                '.</p>';
            $GLOBALS['TEMPLATE']['content'] .= $form;
        }
        else
        {
            // create an inactive user record
            $u = new User();
            $u->username = $_POST['username'];
            $u->password = $password;
            $u->emailAddr = $_POST['contact_email'];
            $u->first_name = $_POST['contact_first_name'];
            $u->last_name = $_POST['contact_last_name'];
            $u->company_id = $_POST['contact_company'];
            $u->dept_id = $_POST['contact_department'];
            $u->user_signature = $_POST['user_signature'];
            $u->save();
            
            $uid = urlencode($u->uid);
            $message = '<html>'.
                '<p>Thanks for your register！</p>'.
                '<p>Before your login, please ensure your email by click <a href="http://'.
                 $_SERVER['HTTP_HOST'].'/dotproject/register/verify.php?uid='.$uid.'&roleid='.' '.'">'.
                'http://'.$_SERVER['HTTP_HOST'].'/dotproject/register/verify.php?uid='.$uid.'&roleid='.' '.'</a></p></html>';
            $m=new Mail();
            $m->From( '' );
			$m->To( $u->emailAddr );
			$m->Subject( 'dotproject email active' );	
			$m->Body($message);	// set the body
           
            if($m->Send())
            {$GLOBALS['TEMPLATE']['content'] = '<p><strong>Thanks for your register ' .
                '.</strong></p> <p>you will receive a email to validate your email address ' .
                '</p>';
             $GLOBALS['TEMPLATE']['content'] .= $form;
            }
         }
    }
    // there was invalid data
    else
    {
        $GLOBALS['TEMPLATE']['content'] .= '<p><strong>Illegal data' .
            '.</strong></p> <p>Please input the right information' .
            'to complete the register.</p>';
        $GLOBALS['TEMPLATE']['content'] .= $form;
    }
}

if (!empty($GLOBALS['TEMPLATE']['content']))
{
    echo $GLOBALS['TEMPLATE']['content'];
}
?>
