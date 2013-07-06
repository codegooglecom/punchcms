<?php
/**
 * Account 
 * 
 * Account functionalities
 *
 * @author      Alexander Galarraga <alex@spin.an>
 * @copyright   Spin Internet Media Curacao
 * @date        2013-07-05
 * @version     0.0.1
 */


class Account
{  
    private $_AccountId = NULL;
    private $_accountName;
    private $_subdomain;
    
    public function __construct($accountId = NULL)
    {		
        if($accountId !== NULL)
        {
            $DB = Database::getInstance();
            $result = $DB->select('accounts',array('account_id' => $accountId));
            $this->setAllValues($result->fetch());
        }
    }
    
    public function createdb()
    {
        $DB = Database::getInstance();
        $DB->create($this->getSubdomain());
    }
    
    
    public function setAllValues($databaseRow)
    {
        $this->setAccountId($databaseRow['account_id']);
        $this->setAccountName($databaseRow['account_name']);
        $this->setSubdomain($databaseRow['subdomain']);
    }
    
    public function save()
    {                
        $DB = Database::getInstance();
        
        // table values
        $values = array(
            'account_name'           => $this->_accountName,
            'subdomain'    => $this->_subdomain,
            );
        
        if($this->getaccountId() === NULL)
        {
            $DB->insert('accounts',$values);
            $this->setAccountId($DB->lastInsertId());
            $this->createdb();
            try 
            {
                $DB2 = new Database('mysql:host='. DB_HOSTNAME .';dbname='. DB_CREATE_PREFIX.$this->getSubdomain() .'');
                $sql=file_get_contents("database/punchcms.sql");
                $DB2->prepare($sql);
                
                $DB2->prepare("update punch_account set uri = :uri where id=1",Array('uri'=>$this->getSubdomain()));
            }
            catch(PDOException $e) 
            {
                die('Unable to open database connection.');
            }
            
        }
        else
        {
            // update existing 
            $DB->update('accounts',
                    array(
                        'account_id' => $this->getAccountId()
                    ),
                    $values
            );
        }
        
        
    }
    
    public function toArray()
    {
        $objReturn=Array(
            'account_id' => $this->getAccountId(),
            'account_name'=> $this->getAccountName(),
            'subdomain'=> $this->getSubdomain(),
            'punch_id'=>$this->getPunchId(),
            'admin'=>$this->getPunchAdmin(),
        );
        return $objReturn;
    }
    
    public function delete()
    {        
        $DB = Database::getInstance();
        $DB->prepare("DROP DATABASE IF EXISTS ".DB_CREATE_PREFIX.$this->getSubdomain());
        return $DB->delete('accounts',array('account_id' => $this->getAccountId()));
    }
    
    public function getAccountId() {
        return $this->_AccountId;
    }

    public function setAccountId($AccountId) {
        $this->_AccountId = $AccountId;
    }

    public function getAccountName() {
        return $this->_accountName;
    }

    public function setAccountName($accountName) {
        $this->_accountName = $accountName;
        
        
    }

    public function getSubdomain() {
        return $this->_subdomain;
    }

    public function setSubdomain($subdomain) {
        $this->_subdomain = $subdomain;
    }
    
    public function getPunchId()
    {
        $DB2 = new Database('mysql:host='. DB_HOSTNAME .';dbname='. DB_CREATE_PREFIX.$this->getSubdomain() .'');
        $sql="select punchId from punch_account where uri = :uri";
        $result = $DB2->prepare($sql,Array('uri'=>$this->getSubdomain()));
        $row = $result->fetch();
        return $row['punchId'];
    }
    public function getPunchAdmin()
    {
        $DB2 = new Database('mysql:host='. DB_HOSTNAME .';dbname='. DB_CREATE_PREFIX.$this->getSubdomain() .'');
        $result = $DB2->prepare("select id from punch_account where uri = :uri",Array('uri'=>$this->getSubdomain()));
        if($row = $result->fetch())
        {
            $result2 = $DB2->prepare("select name,email from punch_liveuser_users where account_id = :id limit 1",Array('id'=>$row['id']));
            if($row2 = $result2->fetch())
            {
                return $row2;
            }
        }
    }
    public function setAdminName($newAdminName)
    {
        $DB2 = new Database('mysql:host='. DB_HOSTNAME .';dbname='. DB_CREATE_PREFIX.$this->getSubdomain() .'');
        $result = $DB2->prepare("select id from punch_account where uri = :uri",Array('uri'=>$this->getSubdomain()));
        if($row = $result->fetch())
        {
            $result2 = $DB2->prepare("update punch_liveuser_users set name = :admin_name where account_id = :id and handle = 'admin' order by authuserid limit 1",
                    Array('id'=>$row['id'],'admin_name'=>$newAdminName));
            if($row2 = $result2->fetch())
            {
                return $row2;
            }
        }
    }
    public function setAdminEmail($newAdminEmail)
    {
        $DB2 = new Database('mysql:host='. DB_HOSTNAME .';dbname='. DB_CREATE_PREFIX.$this->getSubdomain() .'');
        $result = $DB2->prepare("select id from punch_account where uri = :uri",Array('uri'=>$this->getSubdomain()));
        if($row = $result->fetch())
        {
            $result2 = $DB2->prepare("update punch_liveuser_users set email = :admin_email where account_id = :id and handle = 'admin' order by authuserid limit 1",
                    Array('id'=>$row['id'],'admin_email'=>$newAdminEmail));
            if($row2 = $result2->fetch())
            {
                return $row2;
            }
        }
    }
    public function setAdminPassword($newAdminPassword)
    {
        if(!empty($newAdminPassword))
        {       
            $newAdminPassword=sha1($newAdminPassword);
            $DB2 = new Database('mysql:host='. DB_HOSTNAME .';dbname='. DB_CREATE_PREFIX.$this->getSubdomain() .'');
            $result = $DB2->prepare("select id from punch_account where uri = :uri",Array('uri'=>$this->getSubdomain()));
            if($row = $result->fetch())
            {
                $result2 = $DB2->prepare("update punch_liveuser_users set passwd = :admin_password where account_id = :id and handle = 'admin' order by authuserid limit 1",
                        Array('id'=>$row['id'],'admin_password'=>$newAdminPassword));
                if($row2 = $result2->fetch())
                {
                    return $row2;
                }
            }
        }
    }
    
    public function setSiteName($accountName)
    {
        $DB2 = new Database('mysql:host='. DB_HOSTNAME .';dbname='. DB_CREATE_PREFIX.$this->getSubdomain() .'');
        $DB2->prepare("update punch_account set name = :account_name where uri = :uri",Array('uri'=>$this->getSubdomain(),'account_name'=>$accountName));
    }
}

?>