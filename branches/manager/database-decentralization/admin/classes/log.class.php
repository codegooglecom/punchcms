<?php
/**
 * Log 
 * 
 * @author      Mark Kappert <mark@spin.an>
 * @copyright   Spin Internet Media Curacao
 * @date        2012-10-16
 */

define('LOG_CREATE',            1);
define('LOG_MODIFY',            2);
define('LOG_DELETE',            3);
define('LOG_LOGIN',             4);
define('LOG_LOGIN_ATTEMPT',     5);
define('LOG_LOGOUT',            6);
define('LOG_PASS_REQ',          7);
define('LOG_PASS_REQ_ATT',      8);
define('LOG_PASS_RESET',        9);
define('LOG_OTHER',             99);

class Log extends Collection
{
    private $_filter = array();
    private static $_debug = array();

    /**
     * 
     * @param int $action
     * @param string $text 
     * @return boolean
     */
    public function addAccess($text, $action = NULL, $customUserId = NULL)
    {
        $DB         = Database::getInstance();
        $ipAddress  = $_SERVER['REMOTE_ADDR'];
        $curDate    = date('Y-m-d H:i:s');
        $userId     = ($customUserId === NULL) ? $_SESSION['logindata']['user_id'] : $customUserId;
        $action     = (empty($action)) ? LOG_OTHER : $action;
        
        // add to database
        $DB->insert('log',array(
            'datetime' => $curDate,
            'ip' => $ipAddress,
            'user_id' => $userId,
            'action' => $action,
            'text' => $text
        ));
        
    }
    
    public function addDebug($text)
    {
        $trace = debug_backtrace();
        self::$_debug[] = date('H:i:s') .' | '. $text .' (<i>'. $trace[1]['class'] .'::'. $trace[1]['function'] .', Line: '. $trace[0]['line'] .')</i>';
    }
    
    public function printDebug($pre = true)
    {
        $print = '<b>Debug:</b><br />';
        foreach(self::$_debug as $line)
        {
            $print .= $line .'<br />';
        }
        
        echo ($pre) ? '<pre>'. $print . '</pre>' : $print;
    }
    
    public function getRows()
    {
        $DB = Database::getInstance();
        $columns = array();
        $where = 'WHERE ';
        $query = "
            SELECT l.datetime, l.ip, l.text, la.name
            FROM log l
            LEFT JOIN log_action la
                ON (l.action = la.log_action_id)
            ";
        
        if(count($this->_filter['users']) > 0)
        {   
            foreach($this->_filter['users'] as $key => $value)
            {
                if($where !== 'WHERE ') $where .= ' OR ';
                $where .= 'l.user_id = :user_id'. $key;
                $columns['user_id'.$key] = $value;
            }
        }
        
        if($where !== 'WHERE ') $query .= $where;
        $query .= " ORDER BY datetime DESC";

        $result = $DB->prepare($query,$columns);
        if($result->rowCount() > 0)
        {
            $objReturn = new Collection();
            $rows = $result->fetchAll();
            foreach($rows as $row)
            {
                $objReturn->addObject((object) $row);
            }
        }
        
        return $objReturn;
    }
    
    public function filterUser($users)
    {
        if(is_array($users))
        {
            $tmp_users = $users;
            foreach($tmp_users as $user)
            {
                $this->_filter['users'][] = intval($user);
            }
        }
        else
        {
            $this->_filter['users'][] = intval($users);
        }
    }
    
    public function getActionList()
    {
        $actions = array();
        $DB = Database::getInstance();
        $result = $DB->select('log_action');
        if($result->rowCount() > 0)
        {
            $rows = $result->fetchAll();
            foreach($rows as $row)
            {
                $actions[$row['log_action_id']] = $row['name'];
            }
        }
        return $actions;
    }
}
?>
