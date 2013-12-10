<?php

/* General DBA Collection Class v0.2.2
 * Holds a collection of objects.
 *
 * CHANGELOG
 * version 0.2.2, 21 Aug 2007
 *   ADD: Added methods for paginating.
 * version 0.2.1, 02 Aug 2007
 *   ADD: Added the reverse method.
 * version 0.2.0, 16 May 2006
 *   NEW: Created class.
 */

class DBA__Collection implements Iterator {
	protected $collection = array();
	private $isSeek = false;
	private $__pageItems = 0;
	private $__currentPage = 1;

	/**
	 * Constructor method
	 * 
	 * @param array $initArray
	 */
	public function __construct($initArray = array()) {
	   if (is_array($initArray)) {
		   $this->collection = $initArray;
	   }
	}

	/**
	 * Add object to the collection
	 * 
	 * @param object The object
	 * @param boolean Add object to beginning of array or not
	 */
	public function addObject($value, $blnAddToBeginning = false) {
		if ($blnAddToBeginning) {
			array_unshift($this->collection, $value);
		} else {
			array_push($this->collection, $value);
		}
	}

	/**
	 * Advance internal pointer to a specific index
	 * 
	 * @param integer $intPosition
	 */
	public function seek($intPosition) {
        if (is_numeric($intPosition) && $intPosition < count($this->collection)) {
        	reset($this->collection);
			while($intPosition > key($this->collection)) {
				next($this->collection);
			}
        }

		$this->isSeek = true;
	}

	/**
	 * Pick a random child element
	 */
    public function random() {
    	$intIndex = rand(0, (count($this->collection) - 1));
		return $this->collection[$intIndex];
    }

    /**
     * Randomize the collection
     */
    public function randomize() {
		shuffle($this->collection);
    }

    /**
     * Get an element of the collection selected by property value.
     */
    public function getByPropertyValue($strSearchProperty, $strSearchValue) {
    	$objReturn = null;
    	
    	foreach ($this->collection as $objElement) {
    		$strProperty = "get{$strSearchProperty}";
    		if (is_callable(array($objElement, $strProperty))) {
    			if ($objElement->$strProperty() == $strSearchValue) {
    				$objReturn = $objElement;
    				break;
    			}
    		}
    	}
    	
    	return $objReturn;
    }

    /**
     * Get the value of a property of a specific element, selected by property value. 
     */
    public function getValueByValue($strSearchProperty, $strSearchValue, $strResultProperty = "value") {
    	$strReturn = "";
    	
    	$objElement = $this->getByPropertyValue($strSearchProperty, $strSearchValue);
    	if (is_object($objElement)) {
    		$strProperty = "get{$strResultProperty}";
    		if (is_callable(array($objElement, $strProperty))) {
    			$strReturn = $objElement->$strProperty();
    		}
    	}
    	
    	return $strReturn;
    }

    /**
     * Order the collection on a given key [asc]ending or [desc]ending
     * 
     * @param string $strSubject
     * @param string $strOrder
     */
	public function orderBy($strSubject, $strOrder = "asc") {
		for ($i = 0; $i < count($this->collection); $i++) {
			for ($j = 0; $j < count($this->collection) - $i - 1; $j++) {
				if ($strOrder == "asc") {
					if ($this->collection[$j + 1]->$strSubject < $this->collection[$j]->$strSubject) {
						$objTemp = $this->collection[$j];
						$this->collection[$j] = $this->collection[$j + 1];
						$this->collection[$j + 1] = $objTemp;
					}
				} else {
					if ($this->collection[$j + 1]->$strSubject > $this->collection[$j]->$strSubject) {
						$objTemp = $this->collection[$j];
						$this->collection[$j] = $this->collection[$j + 1];
						$this->collection[$j + 1] = $objTemp;
					}
				}
			}
		}
	}

	/**
	 * Order the collection on a given field name [asc]ending or [desc]ending.
	 * 
	 * @param string $strFieldName
	 * @param string $strOrder
	 */
	public function orderByField($strFieldName, $strOrder = "asc") {

		for ($i = 0; $i < count($this->collection); $i++) {
			for ($j = 0; $j < count($this->collection) - $i - 1; $j++) {
				if ($strOrder == "asc") {
					if ($this->collection[$j + 1]->$strSubject < $this->collection[$j]->$strSubject) {
						$objTemp = $this->collection[$j];
						$this->collection[$j] = $this->collection[$j + 1];
						$this->collection[$j + 1] = $objTemp;
					}
				} else {
					if ($this->collection[$j + 1]->$strSubject > $this->collection[$j]->$strSubject) {
						$objTemp = $this->collection[$j];
						$this->collection[$j] = $this->collection[$j + 1];
						$this->collection[$j + 1] = $objTemp;
					}
				}
			}
		}
	}

	public function count() {
		return count($this->collection);
	}

    public function current() {
        return current($this->collection);
    }

    public function next() {
        return next($this->collection);
    }

    public function previous() {
        return prev($this->collection);
    }

    public function key() {
        return key($this->collection);
    }

    public function valid() {
    	if ($this->__pageItems > 0) {
    		if ($this->key() + 1 > $this->pageEnd()) {
    			return false;
    		} else {
    			return $this->current() !== false;
    		}
    	} else {
    		return $this->current() !== false;
    	}
    }
	
    public function rewind() {
    	if ($this->__pageItems > 0) {
			$this->setCurrentPage();
    		$this->seek($this->pageStart() - 1);
    	} else {
			if (!$this->isSeek) {
				reset($this->collection);
			}
    	}
    }

    public function reverse() {
        return array_reverse($this->collection);
    }

    public function end() {
        return end($this->collection);
    }
    
    /**
     * Check if an object is in the collection
     * 
     * @param variable $varValue
     */
    public function inCollection($varValue) {
    	foreach ($this->collection as $object) {
    		if ($object == $varValue) {
    			return true;
    		}
    	}
    	
    	return false;
    }
    	
    /**
     * Set items per page.
     * 
     * @param integer $intValue
     */
	public function setPageItems($intValue) {
		$this->__pageItems = $intValue;
	}
	
	public function getPageItems() {
		return $this->__pageItems;
	}
	
	/**
	 * Set the current page
	 * 
	 * @param integer $intValue
	 */
	public function setCurrentPage($intValue = NULL) {
		if (is_null($intValue)) {
			$intPage = Request::get("page", 1);
			if ($intPage > $this->pageCount() || $intPage < 1) $intPage = 1;

			$this->__currentPage = $intPage;
		} else {
			$this->__currentPage = $intValue;
		}
	}
	
	public function getCurrentPage() {
		return $this->__currentPage;
	}
	
	public function pageCount() {
		if ($this->__pageItems > 0) {
			$intReturn = ceil(count($this->collection) / $this->__pageItems);
		} else {
			$intReturn = 1;
		}
		
		return $intReturn;
	}
	
	public function pageStart() {
		return ($this->getCurrentPage() * $this->__pageItems) - ($this->__pageItems - 1);
	}
	
	public function pageEnd() {
		$intReturn = ($this->getCurrentPage() * $this->__pageItems);
		if ($intReturn > count($this->collection)) $intReturn = count($this->collection);
		
		return $intReturn;
	}
	
	public function nextPage() {
		$intReturn = ($this->getCurrentPage() + 1 < $this->pageCount()) ? $this->getCurrentPage() + 1 : $this->pageCount();
		
		return $intReturn;
	}
	
	public function previousPage() {
		$intReturn = ($this->getCurrentPage() - 1 > 0) ? ($this->getCurrentPage() - 1) : 1;
		
		return $intReturn;
	}
}

?>