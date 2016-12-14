<?php
require_once '../base.php';
require_once DP_BASE_DIR . '/includes/config.php';
require_once (DP_BASE_DIR . '/includes/main_functions.php');
require_once (DP_BASE_DIR . '/includes/db_connect.php');
require_once (DP_BASE_DIR . '/classes/permissions.class.php');


        
class User
{
    private $uid;     // user id
    private $fields;  // other record fields
	private $pacl;

    // initialize a User object
    public function __construct()
    {
		$this->pacl = new dPacl();
        $this->uid = null;
        $this->fields = array('username' => '',
                              'password' => '',
                              'emailAddr' => '',
            'first_name' => '',
            'last_name' => '',
            'company_id' => '',
            'dept_id' => '',
            'user_signature' => ''
            );
    }

    // override magic method to retrieve properties
    public function __get($field)
    {
        if ($field == 'uid')
        {
            return $this->uid;
        }
        else 
        {
            return $this->fields[$field];
        }
    }

    // override magic method to set properties
    public function __set($field, $value)
    {
        if (array_key_exists($field, $this->fields))
        {
            $this->fields[$field] = $value;
        }
    }

    // return if username is valid format
    public static function validateUsername($username)
    {
        return preg_match('/^\w{2,20}$/i', $username);
    }
    
    // return if email address is valid format
    public static function validateEmailAddr($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    // return an object populated based on the record's user id
    public static function getById($uid)
    {
        if(trim($uid)=='')
            return false;
        $u = new User();
        //$uid = intval($uid);

        $query = sprintf('SELECT user_username, user_password, user_signature, ' .
            'contact_first_name, contact_last_name, contact_company, contact_department, contact_email ' .
            'FROM users, contacts WHERE user_id = %d AND user_contact = contact_id',            
            $uid);
        $row = array();

        if (db_loadHash($query, $row))
        {
            $u->username = $row['user_username'];
            $u->password = $row['user_password'];
            $u->emailAddr = $row['contact_email'];
            $u->first_name = $row['contact_first_name'];
            $u->last_name = $row['contact_last_name'];
            $u->company_id = $row['contact_company'];
            $u->dept_id = $row['contact_department'];
            $u->user_signature = $row['user_signature'];
            $u->uid = $uid;
        }
        return $u;
    }

    // return an object populated based on the record's username
    public static function getByUsername($username)
    {
        $u = new User();

        $query = sprintf('SELECT user_id, user_password, user_signature, ' .
            'contact_first_name, contact_last_name, contact_company, contact_department, contact_email ' .
            'FROM users, contacts WHERE user_username = "%s" AND user_contact = contact_id',            
            db_escape($GLOBALS['DB'], $username));
        $row = array();

        if (db_loadHash($query, $row))
        {
            $u->username = $username;
            $u->password = $row['user_password'];
            $u->emailAddr = $row['contact_email'];
            $u->first_name = $row['contact_first_name'];
            $u->last_name = $row['contact_last_name'];
            $u->company_id = $row['contact_company'];
            $u->dept_id = $row['contact_department'];
            $u->user_signature = $row['user_signature'];
            $u->uid = $row['user_id'];
        }
        return $u;
    }

    // save the record to the database
    public function save()
    {       
        if ($this->uid)
        {
			$q = sprintf('SELECT contact_id FROM users, contacts'.
					'WHERE user_contact = contact_id AND user_id = %s',
					$this->uid);
			$result = db_loadList($q);
			foreach ($result as $r)
			{
				$contact_id = $r['contact_id'];            
				$query = sprintf('UPDATE contacts SET contact_first_name = "%s", contact_last_name = "%s", '.                        
						'contact_company = %d, contact_department = %d, contact_email = "%s" '.
						'WHERE contact_id = %d',
						db_escape($this->first_name),
						db_escape($this->last_name),
						$this->company_id,
						$this->dept_id,
						db_escape($this->emailAddr),
						$contact_id);
				db_exec($query);
			}

			$query = sprintf('UPDATE users SET user_username = "%s", ' .
					'user_password = "%s", user_signature = "%s" '.
					'WHERE user_id = %d',
					db_escape($this->username),
					db_escape($this->password),
					db_escape($this->user_signature),               
					$this->userId);
			db_exec($query);          
        }
        else
        {
            $query = sprintf('INSERT INTO users (user_username, user_password, ' .
                'user_signature) VALUES ("%s", "%s", "%s")',
                db_escape($this->username),
                db_escape($this->password),
                db_escape($this->signature));
			db_exec($query);
            $this->uid = db_insert_id();
            $query = sprintf('UPDATE users SET user_contact=%d WHERE user_id=%d',
                    $this->uid, $this->uid);           
			db_exec($query);
            $query = sprintf('INSERT INTO contacts (contact_id, contact_first_name, contact_last_name, ' .
                'contact_company, contact_department, contact_email) VALUES (%d, "%s", "%s", %d, %d, "%s")',
                $this->uid,
                db_escape($this->first_name),
                db_escape($this->last_name),
                $this->company_id, $this->dept_id,
                db_escape($this->emailAddr));
			db_exec($query);
        }
    }

    

    // add the user's role information
    public function addLogin($role_id = 19)
    {
        $this->pacl->addLogin($this->uid, $this->username);
		$this->pacl->insertUserRole($role_id, $this->uid);
    }

	public function checkHasRole()
	{
		$rs = $this->pacl->getUserRoles($this->uid);
		if (empty($rs))
			return false;
		else 
			return true;
	}
	public function getRoleByName($name, $value)
	{
		return $this->pacl->get_group_id($name, $value);
	}
    
}
?>
