<?php

class System
{
    private static $_actions    = array();
    private static $_titles     = array();
    
    public function getNavigationItems($navigationPos,$all = false)
    {
        
       global $objAuthUser;
        
        $DB = Database::getInstance();
        $objReturn = NULL;
        
        if($objAuthUser->getIsAdmin() == 0 && $all === false)
        {
            $and = 'AND gm.user_group_id = :user_group_id';
            $values['user_group_id'] = $objAuthUser->getGroupId();
        }

        $query = "
            SELECT n.*
            FROM navigation n
            LEFT JOIN module m
                ON (m.navigation_id = n.navigation_id)
            LEFT JOIN user_group_has_module gm
                ON (gm.module_id = m.module_id)
            WHERE n.navigation_pos = :navigation_pos
            ". $and ."
            GROUP BY n.navigation_id, n.name 
            ORDER BY n.order_id
        ";
        $values['navigation_pos'] = $navigationPos;
        
       
        $result = $DB->prepare($query,$values);
        
        if($result->rowCount() > 0)
        {
            $objReturn = new Collection();
            
            while($row = $result->fetch())
            {
                $objNavigation = new Navigation();
                $objNavigation->setAllValues($row);
                $objReturn->addObject($objNavigation);
            }
        }
        return $objReturn;
    }
    
    public function getNavigationPositions()
    {
        $DB = Database::getInstance();
        $positions = array();
        
        $result = $DB->select('navigation_pos');
        
        if($result->rowCount() > 0)
        {            
            while($row = $result->fetch())
            {
                $positions[] = $row;
            }
        }
        return $positions;
    }
    
    public function getNavigationName($id)
    {
        $DB = Database::getInstance();
        $result = $DB->select('navigation_pos',array('navigation_pos_id' => $id));
        $row = $result->fetch();
        return $row['name'];
    }
    
    public function findNavigation($page)
    {
        $DB = Database::getInstance();
        $objNavigation = NULL;
        
        $query = "
            SELECT n.*
            FROM navigation n
            LEFT JOIN module m
                ON (m.navigation_id = n.navigation_id)
            WHERE n.api_name = :navigation_api_name
        ";


        $values = array(
            'navigation_api_name' => $page
        );
        
        $result = $DB->prepare($query,$values);
        if($result->rowCount() > 0)
        {
            $objNavigation = new Navigation();
            $objNavigation->setAllValues($result->fetch());
        }
        return $objNavigation;
    }
       
    public function boolean($value)
    {
        if($value == 1 || $value === true)
        {
            return '<img src="images/icn_check.png" alt="true" title="true" />';
        }
        return '&nbsp;';
    }
    
    public function getVariableGroup($apiName)
    {
        $objReturn = new VariableGroup();
        $objReturn->get($apiName);
        return $objReturn;
    }
    
    /**
     * Format database date format 
     * yyy-mm-dd hh:ii:ss to dd-mm-yyy hh:ii:ss
     * if second paramter is set to true a 3 character monthname is returned
     * like: // dd (3 char monthname), yyyy hh:ii:ss
     * 
     * @param string $date
     * @param boolean $textual
     * @return string formatted date
     */
    public function formatDate($datetime, $textual = false)
    {
        if(empty($datetime)) return '';
        
        $newDatetime = '';
        
        list($date,$time)   = explode(' ',$datetime);
        list($y,$m,$d)      = explode('-',$date);
        
        if($textual)
        {
            // dd (3 char monthname), yyyy hh:ii:ss
            $m = date('M', mktime(0, 0, 0, $m, 10));
            $newDatetime = trim($d .' '. $m .', '. $y .' '. $time);
        }
        else
        {
            // dd-mm-yyy hh:ii:ss
            $newDatetime = trim($d .'-'. $m .'-'. $y .' '. $time);
        }
        return $newDatetime;
    }
    
    public function setSuccess($message)
    {
        $_SESSION['alert']['success'] = $message;
    }
    
    public function getSuccess()
    {
        $tmp = $_SESSION['alert']['success'];
        unset($_SESSION['alert']['success']);
        return $tmp;
    }
    
    public function setWarning($message)
    {
        $_SESSION['alert']['warning'] = $message;
    }
    
    public function getWarning()
    {
        $tmp = $_SESSION['alert']['warning'];
        unset($_SESSION['alert']['warning']);
        return $tmp;
    }
    
    public function setError($message)
    {
        $_SESSION['alert']['error'] = $message;
    }
    
    public function getError()
    {
        $tmp = $_SESSION['alert']['error'];
        unset($_SESSION['alert']['error']);
        return $tmp;
    }
    
    public function setNotice($message)
    {
        $_SESSION['alert']['notice'] = $message;
    }
    
    public function getNotice()
    {
        $tmp = $_SESSION['alert']['notice'];
        unset($_SESSION['alert']['notice']);
        return $tmp;
    }
    
    public function exportAction($label,$link,$options = array())
    {
        self::$_actions[] = array(
            'class' => 'export',
            'label' => $label,
            'link' => $link,
            'options' => $options,
        );
    }
    
    public function createAction($label,$link,$options = array())
    {
        self::$_actions[] = array(
            'class' => 'add',
            'label' => $label,
            'link' => $link,
            'options' => $options,
        );
    }
    
    public function modifyAction($label,$link,$options = array())
    {
        self::$_actions[] = array(
            'class' => 'edit',
            'label' => $label,
            'link' => $link,
            'options' => $options,
        );
    }
    
    public function deleteAction($label,$link,$options = array())
    {
        self::$_actions[] = array(
            'class' => 'delete',
            'label' => $label,
            'link' => $link,
            'options' => $options,
        );
    }
    
    public function getActions()
    {
        return self::$_actions;
    }
    
    public function addTitle($title, $link)
    {
        self::$_titles[] = array(
            'title' => $title,
            'link' => $link,
        );
    }
    
    public function getTitles()
    {
        return self::$_titles;
    }
    
	public function redirect($url)
	{		
		header('location: '. $url);
		die('Redirecting...');
	}
    
    public function makeAlias($alias)
    {
        $strippedAlias  = strtolower(trim($alias));
		$search         = array(' ','§','!','@','#','$','%','^','&','*','(',')','=','+','±','<','>','?','/','\\','\'','\"',':',';','|','[',']','{','}',',','.');
		$replace        = array('-','','','','','','','','','','','','','','','','','','','','','','','','','','','','','');
		$strippedAlias  = str_replace($search, $replace, $strippedAlias);
        
        return $strippedAlias;
    }
    
    public function rearrange($fileArray)
    {
        $newArr = array();
        foreach($fileArray as $key => $all)
        {
            foreach($all as $arg => $values)
            {
                if(is_array($values))
                {
                    foreach($values as $i => $val)
                    {
                        $newArr[$key][$i][$arg] = $val;
                    }
                }
                else
                {
                    $newArr[$key][$arg] = $val;
                }
            }
        }
        return $newArr;
    }
}
?>
