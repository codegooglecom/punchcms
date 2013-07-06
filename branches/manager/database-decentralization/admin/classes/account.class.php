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
    
    public function __construct($domainId = NULL)
    {		
        if($domainId !== NULL)
        {
            $DB = Database::getInstance();
            $result = $DB->select('accounts',array('account_id' => $domainId));
            $this->setAllValues($result->fetch());
        }
    }
    
    public function setAllValues($databaseRow)
    {
        $this->setDomainId($databaseRow['domain_id']);
        $this->setUrl($databaseRow['url']);
        $this->setDomainPrice($databaseRow['domainprice']);
    }
    
    public function save()
    {        
        parent::save();
        
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
    
    public function delete()
    {
        parent::delete();
        
        $DB = Database::getInstance();
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


}

?>