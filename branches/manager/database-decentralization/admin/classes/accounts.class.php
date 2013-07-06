<?php
/**
 * Accounts 
 * 
 * Punch CMS Accounts functionalities
 *
 * @author      Alexander Galarraga <alex@spin.cw>
 * @copyright   Spin Internet Media Curacao
 * @date        2013-07-05
 * @version     0.0.1
 */


class Accounts
{  
   public static function getAccounts()
    {
        $objReturn = Array();
        
        $DB = Database::getInstance(); 
        $query = "
            SELECT *
            FROM accounts
            ORDER BY account_name ASC
            ";
        $result = $DB->prepare($query);
        if($result->rowCount() > 0)
        {           
            while($row = $result->fetch())
            {
                $objReturn[]=Array(
                    "id"=>$row['account_id'],
                    "account_name"=>$row['account_name'],
                );
            }
        }
        
        return $objReturn;
    }
    
}
?>
