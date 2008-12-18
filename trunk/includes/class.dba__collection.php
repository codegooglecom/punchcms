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
	private $isSeek = FALSE;
	private $__pageItems = 0;
	private $__currentPage = 1;

	public function __construct($initArray = array()) {
	   if (is_array($initArray)) {
		   $this->collection = $initArray;
	   }
	}

	public function addObject($value, $blnAddToBeginning = FALSE) {
		/* Add an object to the collection.
		 *
		 * Method arguments are:
		 * - object to add.
		 */

		if ($blnAddToBeginning) {
			array_unshift($this->collection, $value);
		} else {
			array_push($this->collection, $value);
		}
	}

	public function seek($intPosition) {
    	//*** advance the internal pointer to a specific index
        if (is_numeric($intPosition) && $intPosition < count($this->collection)) {
        	reset($this->collection);
			while($intPosition > key($this->collection)) {
				next($this->collection);
			}
        }
        
		$this->isSeek = TRUE;
	}

    public function random() {
    	//*** Pick a random child element.
    	$intIndex = rand(0, (count($this->collection) - 1));
		return $this->collection[$intIndex];
    }

    public function randomize() {
    	//*** Randomize the collection.
		shuffle($this->collection);
    }

	public function orderBy($strSubject, $strOrder = "asc") {
    	//*** Order the collection on a given key [asc]ending or [desc]ending.

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

	public function orderByField($strFieldName, $strOrder = "asc") {
    	//*** Order the collection on a given field name [asc]ending or [desc]ending.

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
    			return FALSE;
    		} else {
    			return $this->current() !== FALSE;
    		}
    	} else {
    		return $this->current() !== FALSE;
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
    
    public function inCollection($varValue) {
    	foreach ($this->collection as $object) {
    		if ($object == $varValue) {
    			return TRUE;
    		}
    	}
    	
    	return FALSE;
    }
    	
	public function setPageItems($intValue) {
		$this->__pageItems = $intValue;
	}
	
	public function getPageItems() {
		return $this->__pageItems;
	}
	
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