<?php
/**
 * Database 
 * 
 * This class is used to communicate with databases
 *
 * @author      Alexander Galarraga <alex@spin.an>
 * @copyright   Spin Internet Media Curacao
 * @date        2012-09-16
 * @version     0.0.1
 */


class Database
{  
    private $_conn;
    private static $_instance;
    
	private function __construct()
	{
		$this->connect();
	}
    
    /**
     * Get instance of the database object. If no instance exists it will create
     * one.
     * 
     * @return Database
     */
    public static function getInstance() 
    {
        if(!self::$_instance) 
        {
            self::$_instance = new Database();
        }

        return self::$_instance;
    }

	private function connect()
	{
        try {
            $this->_conn = new PDO('mysql:host='. DB_HOSTNAME .';dbname='. DB_DATABASE .'', DB_USERNAME, DB_PASSWORD);
        }
        catch(PDOException $e) 
        {
            die('Unable to open database connection.');
        }
		
	}

    /**
     * 
     * @param string $query
     * @param array $columns
     * @return PDOstatement
     */
    public function prepare($query,$columns=null)
    {
        try
        {
            $stmt = $this->_conn->prepare($query);
            if($stmt)
            {
                if(!$stmt->execute($this->prepareColumns($columns)))
                {
                    // execution failed
                    $errorInfo = $stmt->errorInfo();
                    Log::addDebug('Query execution failed: '. $errorInfo[0] .', '. $errorInfo[1] .', '. $errorInfo[2] .'<br />'. $query .'<br />'. print_r($columns,true) .'<br />'); 
                }
                
                return $stmt;
            }
            else
            {
                // preparation failed
                $errorInfo = $stmt->errorInfo();
                Log::addDebug('Cannot prepare query: '. $errorInfo[0] .', '. $errorInfo[1] .', '. $errorInfo[2]);
            }
        }
        catch(PDOException  $e)
        {
            // exception thrown
            $errorInfo = $stmt->errorInfo();
            Log::addDebug('PDOException: '. $e->getMessage() .', '. $errorInfo[0] .', '. $errorInfo[1] .', '. $errorInfo[2]);
        }       
        return false;
    } 
    
    /**
     * 
     * @param string $table
     * @param array $columns
     * @return PDOstatement
     */
    public function insert($table,$columns)
    {       
        $query = 'INSERT INTO '. $table .' ('. $this->getColumnNames($columns) .') VALUES('. $this->getColumnParameters($columns) .')';
        return $this->prepare($query,$columns);
    }
    
    /**
     * 
     * @param string $table
     * @param array $where
     * @return PDOstatement
     */
    public function delete($table,$where)
    {
        $query = 'DELETE FROM '. $table .' WHERE '. $this->prepareWhere($where);
        return $this->prepare($query,$where);
    }
    
    /**
     * 
     * @param string $table
     * @param array $where
     * @param array $selection
     * @return PDOstatement
     */
    public function select($table, $where = array(), $selection = '*')
    {
        $query = 'SELECT '. $selection .' FROM '. $table;
        if(count($where)>0) $query .=' WHERE '. $this->prepareWhere($where);
        return $this->prepare($query,$this->removeNULL($this->prepareWhereArray($where)));
    }
    
    /**
     * 
     * @param string $table
     * @param array $where
     * @param array $selection
     * @return PDOstatement
     */
    public function update($table, $where = array(), $values = array())
    {
        // UPDATE table SET pietje=:pietje WHERE 
        $query = 'UPDATE '. $table .' SET '. $this->getSetParameters($values);
        if(count($where)>0) $query .=' WHERE '. $this->prepareWhere($where);
        return $this->prepare($query,array_merge($where,$values));
    }
    
    public function prepareWhereArray($columns){
        
        foreach($columns as $column => $value)
        {
            if(is_array($value) && isset($value["value"]))
            { 
                $columns[str_replace('.', '_', $column)] = $value["value"];
            }elseif(is_array($value)){
                foreach($value as $key => $waarden){
                    $columns[str_replace('.', '_', $column).$key] = $waarden["value"];
                    unset($columns[$column]);
                }
            }else{
                unset($columns[$column]);
                $columns[str_replace('.', '_', $column)] = $value;
            }
        }
        return $columns;
        
    }
    
    private function removeNULL($columns)
    {
        foreach($columns as $column => $value)
        {
            if($value === NULL) unset($columns[$column]);
        }
        return $columns;
    }
    
    private function prepareColumns($columns)
    {
        $return = array();
        if($columns)
        {
            foreach($columns as $column => $value)
            {
                $return[':'.$column] = $value;
            }
        }
        return $return;
    }
    
    public function prepareWhere($where)
    {
        
        $whereStr = '';
        foreach($where as $column => $value)
        {
            
            if(!empty($whereStr)) $whereStr .= ' AND ';
            
            if (is_array($value)){
                if (isset($value["comparator"]) && isset($value["value"])){
                    
                    $whereStr .= $column .' ' . $value["comparator"] . ':' .str_replace('.', '_', $column);
                    
                }else{
                    
                    $nr = 0;
                    
                    foreach($value as $key => $waarden){
                        
                        if(!empty($whereStr) && $nr > 0) $whereStr .= ' AND ';
                        
                        if (isset($waarden["comparator"]) && isset($waarden["value"])){
                    
                            $whereStr .= $column .' ' . $waarden["comparator"] . ':' .str_replace('.', '_', $column).$key;

                        }
                        $nr++;
                        
                    }
                    
                }
            }else{

                // if NULL
                if($value === NULL)
                {
                    $whereStr .= $column .' IS NULL';
                }
                else
                {
                    $whereStr .= $column .' = '. ':' .str_replace('.', '_', $column);
                }
            }
        }
        return $whereStr;
    }
    
    private function getColumnNames($columns)
    {
        $return = array();
        foreach($columns as $column => $value)
        {
            $return[] = $column;
        }
        return implode(', ',$return);
    }
    
    private function getColumnParameters($columns)
    {
        $return = array();
        foreach($columns as $column => $value)
        {
            $return[]= ' :'. $column;
        }
        return implode(',',$return);
    }
    
    private function getSetParameters($columns)
    {
        $return = '';
        foreach($columns as $column => $value)
        {
            if(!empty($return)) $return .= ', ';
            $return .= $column .' = :'. $column;
        }
        return $return;
    }
    
    public function lastInsertId()
    {
        return $this->_conn->lastInsertId();
    }
      
}

?>