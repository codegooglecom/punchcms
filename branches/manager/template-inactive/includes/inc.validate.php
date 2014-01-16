<?php

/*

Usage example

$arrWhitelist = array(
  'name'        => array(
                         'type' => 'string',
                         'maxlength' => 50,
                         'required' => true
                   ),
  'postal_code' => array(
                         'type' => 'postal',
                         'maxlength' => 10,
                         'required' => true
                   ),
  'phone'       => array(
                         'type' => 'phone',
                         'maxlength' => 25
                   ),
  'email'       => array(
                         'type' => 'email',
                         'maxlength' => 255,
                         'required' => true
                   ),
  'age'         => array(
                         'type' => 'int',
                         'maxlength' => 3
                   ),
  'color'       => array(
                        'type' => 'option',
                        'options' => array(
                                           'blue',
                                           'red',
                                           'green',
                                           'yellow'
                                     ),
                        'multiselect' => true
                   ),
  'username'    => array(
                         'type' => 'username',
                         'maxlength' => 16,
                         'required' => true
                   )
);

if ($_POST) {
  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
}

*/

define('VALID_WORD', '/^[-a-zàáâãäåæçèéêëìíîïðñòóôõöøùúûüýA-ZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝ€0-9_\/]*$/i');
define('VALID_STRING', '/^[-a-zàáâãäåæçèéêëìíîïðñòóôõöøùúûüýA-ZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝ€0-9%\.\,\'\/:"_& ]*$/i');
define('VALID_TEXT', '/^[-a-zàáâãäåæçèéêëìíîïðñòóôõöøùúûüýA-ZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝ€0-9\s*\.\,\'\/:"_,?#@^*!&() ]*$/i');
define('VALID_PASSWORD', '/^[-A-Z0-9\.-_!@#$%^(&*?|]*$/i');
define('VALID_EMAIL', '/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i');
define('VALID_PHONE', '/^[\(]?(\d{3})[\)]?[\s]?[\-]?(\d{3})[\s]?[\-]?(\d{4})[\s]?[x]?(\d*)$/');
define('VALID_POSTAL', '/^(\d{5})[\-]?(\d{4})?$/');

function filterInput($input, $whitelist) {
	/* This function filters an input array against a "whitelist" array.
	 * It returns an output array with a $key => value structure where
	 * $key is the name of the input field and $value is one of the
	 * following:
	 * - NULL			: If the input value was NOT valid or empty while required.
	 * - empty string	: If the input value was empty and not required.
	 * - input value	: If the input value was valid.
	 */
	$arrReturn = array();

  	foreach ($whitelist as $key => $value) {
    	$filtered = NULL;

    	if (array_key_exists($key, $input)) {
    		$value = $input[$key];
    		if (is_array($value)) $value = array_pop($value);

			//*** Check input field length.
    		if (isset($whitelist[$key]['maxlength'])
        	  		&& (strlen($value) > $whitelist[$key]['maxlength'])) {
        		$arrReturn[$key] = NULL;
        		continue;
      		}

    		//*** Check "required" option.
    		if (empty($value)) {
    			if (isset($whitelist[$key]['required'])) {
    				if ($whitelist[$key]['required'] === true) {
    					$arrReturn[$key] = NULL;
    				} else {
    					$arrReturn[$key] = "";
    				}
    			} else {
    				$arrReturn[$key] = "";
    			}
        		continue;
    		}

			//*** Check field value.
      		switch ($whitelist[$key]['type']) {
      			case 'text':
      				$filtered = (preg_match(VALID_TEXT, stripslashes($value)))
      					? $value : NULL;
      				break;

      			case 'string':
          			$filtered = (preg_match(VALID_STRING, stripslashes($value)))
          				? $value : NULL;
          			break;

      			case 'word':
          			$filtered = (preg_match(VALID_WORD, $value))
          				? $value : NULL;
          			break;

        		case 'password':
          			$filtered = (preg_match(VALID_PASSWORD, $value))
          				? $value : NULL;
          			break;

        		case 'int':
        			$filtered = (ctype_digit($value))
        				? $value : NULL;
          			break;

        		case 'option':
        			if (is_array($value)) {
        				if ($whitelist[$key]['multiselect']) {
        					$filtered = array();

        					foreach ($value as $option) {
                				if (in_array($option, $whitelist[$key]['options'])) {
                					$filtered[] = $option;
                				}
              				}
            			}
          			} else {
            			$filtered = in_array($value, $whitelist[$key]['options'])
                			? $value : NULL;
          			}
          			break;

        		case 'username':
        			$filtered = (ctype_alnum($value))
        				? $value : NULL;
          			break;

        		case 'email':
        			$filtered = (preg_match(VALID_EMAIL, $value))
        				? $value : NULL;
          			break;

        		case 'phone':
          			$filtered = (preg_match(VALID_PHONE, $value))
            			? $value : NULL;
          			break;

        		case 'postal':
          			$filtered = (preg_match(VALID_POSTAL_US, $value))
            			? $value : NULL;
          			break;

      		}

        	$arrReturn[$key] = $filtered;
    	} else {
    		//*** Check "required" option.
    		if (isset($whitelist[$key]['required'])
    				&& $whitelist[$key]['required'] === true) {
    			$arrReturn[$key] = NULL;
    		} else {
    			$arrReturn[$key] = "";
    		}
    	}
  	}

  	return $arrReturn;
}

?>