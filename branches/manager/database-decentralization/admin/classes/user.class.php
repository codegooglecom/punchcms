<?php
/**
 * User 
 * 
 * User functionalities
 *
 * @author      Alexander Galarraga <alex@spin.an>
 * @copyright   Spin Internet Media Curacao
 * @date        2012-09-17
 * @version     0.0.1
 */

class User
{  
    private $_userId = NULL;
    private $_email = '';
    private $_password = NULL;
    private $_firstname;
    private $_lastname;
    private $_resetPassHash;
    private $_hashedPass = NULL;
	
	public function __construct($userId = NULL,$email = NULL)
	{        
        // load user
        if($userId !== NULL || $email !== NULL)
        {
            if($userId !== NULL)    $values['user_id'] = intval($userId);
            if($email !== NULL)     $values['email'] = $email;

            $DB = Database::getInstance();
            $result = $DB->select('user', $values);
            $this->setAllValues($result->fetch());
        } 
	}
    
	public function checkAuth()
	{
        if($_SESSION['logindata'] !== NULL)
        {
            if(($_SESSION['logindata']['user_id'] === $this->getUserId() && $this->getUserId() > 0)
                    && $_SESSION['logindata']['ip'] === $_SERVER['REMOTE_ADDR'])
            {
                return true;
            } 
        }
		return false;
	}
	
    public function login($username, $password)
    {		
        $DB = Database::getInstance();

        $hashedPass = $this->hash($password);

                //check user in DB
        $result = $DB->select('user',array(
            'username' => $username,
            'password' => $hashedPass,));

        if($result->rowCount() > 0)
        {
            // login successful
            $user = $result->fetch();
            $this->setAllValues($user);

            // set sesssion variables
            $_SESSION['logindata']['user_id']   = $user['user_id'];
            $_SESSION['logindata']['ip']        = $_SERVER['REMOTE_ADDR'];

            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function logout()
    {
        $_SESSION['logindata'] = NULL;
    }
    
    public function createResetPassHash()
    {
        $string = 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $return = date('ymd') .'_';
        for($i = 0; $i < 43; $i++)
        {
            $return .= $string[rand(0, (strlen($string) - 1))];
        }
        
        
        $this->setResetPassHash($return);
        
        // add to database
        $DB = Database::getInstance();
        $DB->update('user',array('user_id' => $this->getUserId()),array('resetpass_hash' => $return));
        
        return $return;
    }
  
    
       
    public function hash($value)
    {
        return sha1($value);
    }
    
    public function save()
    {        
        parent::save();
        
        $DB = Database::getInstance();
        
        // table values
        $values = array(
                'is_active' => $this->getIsActive(),
                'group_id' => $this->getGroupId(),
                'is_admin' => $this->getIsAdmin(),
                'email' => $this->getEmail(),
                'firstname' => $this->getFirstname(),
                'lastname' => $this->getLastname(),
                'resetpass_hash' => $this->getResetPassHash()
            );
        
        // only update password if changed
        if($this->getPassword() !== NULL)
        {
            $values['password'] = $this->getPassword();
        }
        
        if($this->getUserId() === NULL)
        {
            // insert new
            $values['object_id'] = $this->getObjectId();
            $DB->insert('user',$values);
            $this->setUserId($DB->lastInsertId());
            Log::addAccess('user '. $this->getName() .' (id: '. $this->getUserId() .')',LOG_CREATE);
        }
        else
        {
            // update existing 
            $DB->update('user',
                    array(
                        'user_id' => $this->getUserId()
                    ),
                    $values
            );
            Log::addAccess('user '. $this->getName() .' (id: '. $this->getUserId() .')',LOG_MODIFY);
        }
    }
    
    public function delete()
    {
        $DB = Database::getInstance();
        $DB->delete('user',array('user_id' => $this->getUserId()));
        Log::addAccess('user '. $this->getName() .' (id: '. $this->getUserId() .')',LOG_DELETE);
        
        parent::delete();
    }
    
    public function setAllValues($databaseRow)
    {
        $this->setUserId($databaseRow['user_id']);
        $this->setFirstname($databaseRow['firstname']);
        $this->setLastname($databaseRow['lastname']);
        $this->setEmail($databaseRow['email'],false);
         $this->setHashedPassword($databaseRow['password']);
        $this->setResetPassHash($databaseRow['resetpass_hash']);
    }
    
    public function getResetPassHash() {
        return $this->_resetPassHash;
    }
    
    public function setResetPassHash($hash)
    {
        $this->_resetPassHash = $hash;
    }
        
    public function getName()
    {
        return $this->getFirstname() .' '. $this->getLastname();
    }

    public function getUserId()
    {
        return $this->_userId;
    }

    private function setUserId($userId)
    {
        $this->_userId = $userId;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function setEmail($email)
    {
        $this->_email = $email;
    }

    public function getPassword()
    {
        return $this->_password;
    }

    public function setPassword($password)
    {
        $this->_password = $this->hash($password);
    }

    public function getHashedPassword() {
        return $this->_hashedPass;
    }

    private function setHashedPassword($hashedPass) {
        $this->_hashedPass = $hashedPass;
    }
    
    public function getFirstname() 
    {
        return $this->_firstname;
    }

    public function setFirstname($firstname)
    {
        $this->_firstname = $firstname;
    }

    public function getLastname() 
    {
        return $this->_lastname;
    }

    public function setLastname($lastname)
    {
        $this->_lastname = $lastname;
    }

}

?>