<?php
/**
 * Collection 
 * 
 *
 * @author      Alexander Galarraga <alex@spin.an>
 * @copyright   Spin Internet Media Curacao
 * @date        2012-09-17
 * @version     0.0.1
 */


class Collection implements Iterator
{  
    private $_collection = array();
    private $_position;
    
    public function __construct() {
        $this->_position = 0;
    }

    public function addObject($object, $addToBeginning = false)
    {
        if($addToBeginning)
        {
            array_unshift($this->_collection, $object);
        }
        else
        {
            array_push($this->_collection, $object);
        }
    }
    
    public function orderBy($value, $order = 'asc') 
    {
        $value = 'get'. ucfirst($value);
		for ($i = 0; $i < count($this->_collection); $i++) 
        {
			for ($j = 0; $j < count($this->_collection) - $i - 1; $j++) 
            {
				if ($order === 'asc') 
                {
					if (strtolower($this->_collection[$j + 1]->$value()) < strtolower($this->_collection[$j]->$value())) {
						$objTemp = $this->_collection[$j];
						$this->_collection[$j] = $this->_collection[$j + 1];
						$this->_collection[$j + 1] = $objTemp;
					}
				} 
                else 
                {
					if (strtolower($this->_collection[$j + 1]->$value()) > strtolower($this->_collection[$j]->$value())) 
                    {
						$objTemp = $this->_collection[$j];
						$this->_collection[$j] = $this->_collection[$j + 1];
						$this->_collection[$j + 1] = $objTemp;
					}
				}
			}
		}
	}
    
    public function random() 
    {
    	$objReturn = null;
    	
    	$index = rand(0, (count($this->_collection) - 1));
    	if (isset($this->_collection[$index])) {
			$objReturn = $this->_collection[$index];
    	}
    	
    	return $objReturn;
    }

    public function randomize() 
    {
		shuffle($this->_collection);
    }
    
    public function rewind() 
    {
        $this->_position = 0;
    }

    public function current() 
    {
        return $this->_collection[$this->_position];
    }

    public function key() 
    {
        return $this->_position;
    }

    public function next() 
    {
        ++$this->_position;
    }

    public function valid() 
    {
        return isset($this->_collection[$this->_position]);
    }
    
}
?>
