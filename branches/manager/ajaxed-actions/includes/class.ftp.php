<?php

class FTP {
   	private $objFTP;
	private $strHost;
	private $intPort;
	private $intTimeout;

   	/* public Void __construct(): Constructor */
   	public function __construct($host, $port = 21, $timeout = 90, $blnSecure = FALSE) {
   		if (is_null($port)) $port = 21;
   		if (is_null($timeout)) $timeout = 90;
   		
   		$this->strHost = $host;
   		$this->intPort = $port;
   		$this->intTimeout = $timeout;
   		
   		if ($blnSecure && function_exists('ftp_ssl_connect')) {
   			$this->objFTP = ftp_ssl_connect($host, $port, $timeout);
   		}
   		
   		if (!$this->objFTP) {
   			$this->objFTP = ftp_connect($host, $port, $timeout);
   		}
   	}

   	/* public Void __destruct(): Destructor */
   	public function __destruct() {
   		@ftp_close($this->objFTP);
   	}

   	/* public Mixed __call(): Re-route all function calls to the PHP-functions */
	public function __call($function, $arguments) {
		$varReturn = FALSE;
		
       	//*** Prepend the ftp resource to the arguments array
       	array_unshift($arguments, $this->objFTP);

       	//*** Call the PHP function
       	try {
       		$varReturn = @call_user_func_array('ftp_' . $function, $arguments);
       		if ($varReturn === FALSE && $function == "login") {
       			//*** Retry connect unsecured if login fails.
       			ftp_close($this->objFTP);
       			$this->objFTP = ftp_connect($this->strHost, $this->intPort, $this->intTimeout);
       			
       			//*** Re-call the command.
       			array_shift($arguments);
       			array_unshift($arguments, $this->objFTP);
       			$varReturn = call_user_func_array('ftp_' . $function, $arguments);
       		}
       	} catch (Exception $e) {
			echo $e->getMessage();
       	}
       	
       	return $varReturn;
   	}
   	
   	public function delete($strPath) {
   		if (stristr($strPath, "*") === FALSE) {
   			//*** Regular FTP delete.
			try {
				@ftp_delete($this->objFTP, $strPath);
			} catch(Exception $e) {
				echo $e->getMessage();
			}
   		} else {
   			//*** Wildcard delete.
   			$strBasePath = dirname($strPath);
   			$strFileName = basename($strPath);
   			
   			//*** Get files in remote folder.
   			$arrFiles = $this->nlist($strBasePath);
   			if ($arrFiles !== FALSE) {
   				foreach ($arrFiles as $strFile) {
   					$strBaseFile = basename($strFile);
   					if (!$this->is_dir($strFile) && $this->hasWildcard($strFileName, $strBaseFile)) {
						@ftp_delete($this->objFTP, $strBasePath . "/" . $strBaseFile);
   					}
   				}
   			}
   		}
   	}
   	
	public function is_dir($strPath) {
		$origin = @ftp_pwd($this->objFTP); 
		
		if (@ftp_chdir($this->objFTP, $strPath)) {
			ftp_chdir($this->objFTP, $origin);
			return true;
		} else {
			return false;
		}
	}
   	
	private function hasWildcard($strWildcard, $strName) {
		$blnReturn = FALSE;
		
		if (stristr($strWildcard, "*") !== FALSE) {
			if (strpos($strWildcard, "*") === 0) {
				if (strrpos($strWildcard, "*") === (strlen($strWildcard) - 1)) {
					//*** Wildcard at start and end.
					$strNoWildcard = substr(substr($strWildcard, 0, (strlen($strWildcard) - 1)), 1);
					if (strpos($strName, $strNoWildcard) !== FALSE) {
						$blnReturn = TRUE;
					}
				} else {
					//*** Wildcard at start.
					$strNoWildcard = substr($strWildcard, 1);
					if (strpos($strName, $strNoWildcard) === strlen($strName) - strlen($strNoWildcard)) {
						$blnReturn = TRUE;
					}
				}
			} else if (strpos($strWildcard, "*") === (strlen($strWildcard) - 1)) {
				//*** Wildcard at end.
				$strNoWildcard = substr($strWildcard, 0, (strlen($strWildcard) - 1));
				if (strpos($strName, $strNoWildcard) === 0) {
					$blnReturn = TRUE;
				}
			}
		}
		
		return $blnReturn;
	}

}

?>