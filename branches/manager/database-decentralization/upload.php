<?php


session_id($_POST["PHPSESSID"]);
require_once('includes/init.php');

global $_CONF, $_PATHS, $objLiveUser;

function HandleError($message) {
	echo "ERROR:" . $message;
	LogError("ERROR: " . $message);
}

function LogError($message) {
	global $_PATHS;
	file_put_contents($_PATHS['upload'] . "log.txt", "$message\n", FILE_APPEND);
}

//*** Take care of any login requests.
$objLiveUser->login(null, null, true, false, $_CONF['app']['account']->getId());
if (!$objLiveUser->isLoggedIn()) {
	header("HTTP/1.1 500 Internal Server Error"); // This will trigger an uploadError event in SWFUpload
	echo "User is not logged in.";
	exit(0);
}

//*** Max size.
$POST_MAX_SIZE = ini_get('post_max_size');
$unit = strtoupper(substr($POST_MAX_SIZE, -1));
$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));
$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';				// Characters allowed in the file name (in a Regular Expression format)

if (isset($_SERVER['CONTENT_LENGTH']) && (int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
	HandleError("POST exceeded maximum allowed size.");
	exit(0);
}

//*** Get remote settings.
$strServer = Setting::getValueByName('ftp_server');
$strUsername = Setting::getValueByName('ftp_username');
$strPassword = Setting::getValueByName('ftp_password');
$strRemoteFolder = Setting::getValueByName('ftp_remote_folder') . "upload/";

$strDo = request('do');
if ($strDo == "remove") {
	$strFile = request('file');
	$strFile = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($strFile));
	@unlink($_PATHS['upload'] . $strFile);
	
	$objFtp = new FTP($strServer, NULL, NULL, TRUE);
	$objFtp->login($strUsername, $strPassword);
	$objFtp->pasv(TRUE);
	$objFtp->delete($strRemoteFolder . $strFile);
} else {
	$fileId = request('fileId');
	if (substr($fileId, 0, 4) == "efv_") {
		//*** Get the template Id from the request
		$intTemplateFieldId = substr($fileId, 4);
		
		if (is_numeric($intTemplateFieldId)) {
			$objTemplateField = TemplateField::selectByPK($intTemplateFieldId);
			if (is_object($objTemplateField)) {
				LogError("Template ok");
				if ($objTemplateField->getTypeId() == FIELD_TYPE_FILE) {
					$objValue = $objTemplateField->getValueByName("tfv_file_extension");
					$strExtensions = (is_object($objValue)) ? $objValue->getValue() : "";
					if (!empty($strExtensions)) {
						$strExtensions = str_replace("%s", Setting::getValueByName('file_upload_extensions'), $strExtensions);
						$strExtensions = str_replace(".", "", $strExtensions);
						$arrExtensions = explode(" ", strtolower($strExtensions));
					} else {
						$strExtensions = str_replace(".", "", strtolower(Setting::getValueByName('file_upload_extensions')));
						$arrExtensions = explode(" ", $strExtensions);
					}
				} else {
					$strExtensions = str_replace(".", "", strtolower(Setting::getValueByName('image_upload_extensions')));
					$arrExtensions = explode(" ", $strExtensions);
				}
				
				$upload_name = "Filedata";
				$max_file_size_in_bytes = 2147483647;				// 2GB in bytes
				$MAX_FILENAME_LENGTH = 260;
				$uploadErrors = array(
			        0=>"There is no error, the file uploaded with success",
			        1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
			        2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
			        3=>"The uploaded file was only partially uploaded",
			        4=>"No file was uploaded",
			        6=>"Missing a temporary folder"
				);
				
				if (!isset($_FILES[$upload_name])) {
					HandleError("No upload found in \$_FILES for " . $upload_name);
					exit(0);
				} else if (isset($_FILES[$upload_name]["error"]) && $_FILES[$upload_name]["error"] != 0) {
					HandleError($uploadErrors[$_FILES[$upload_name]["error"]]);
					exit(0);
				} else if (!isset($_FILES[$upload_name]["tmp_name"]) || !@is_uploaded_file($_FILES[$upload_name]["tmp_name"])) {
					HandleError("Upload failed is_uploaded_file test.");
					exit(0);
				} else if (!isset($_FILES[$upload_name]['name'])) {
					HandleError("File has no name.");
					exit(0);
				}
				
				// Validate the file size (Warning: the largest files supported by this code is 2GB)
				$file_size = @filesize($_FILES[$upload_name]["tmp_name"]);
				if (!$file_size || $file_size > $max_file_size_in_bytes) {
					HandleError("File exceeds the maximum allowed size");
					exit(0);
				}
				
				if ($file_size <= 0) {
					HandleError("File size outside allowed lower bound");
					exit(0);
				}
				
				// Validate file name (for our purposes we'll just remove invalid characters)
				$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES[$upload_name]['name']));
				if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
					HandleError("Invalid file name");
					exit(0);
				}
							
				// Validate that we won't over-write an existing file
				if (file_exists($_PATHS['upload'] . $file_name)) {
					HandleError("File with this name already exists");
					exit(0);
				}
				
				// Validate file extension
				$path_info = pathinfo($_FILES[$upload_name]['name']);
				$file_extension = $path_info["extension"];
				$is_valid_extension = false;
				foreach ($arrExtensions as $extension) {
					if (strcasecmp($file_extension, $extension) == 0) {
						$is_valid_extension = true;
						break;
					}
				}
				if (!$is_valid_extension) {
					HandleError("Invalid file extension");
					exit(0);
				}
				
				if (!@move_uploaded_file($_FILES[$upload_name]["tmp_name"], $_PATHS['upload'].$file_name)) {
					HandleError("File could not be saved.");
					exit(0);
				}
				
				LogError($_FILES[$upload_name]["tmp_name"] . " -> " . $_PATHS['upload'].$file_name);
	
				echo "Done.";
				exit(0);
			} else {
				HandleError("Posted file is missing information.");
				exit(0);
			}
		} else {
			HandleError("Posted file is missing information.");
			exit(0);
		}
	} else {
		HandleError("Posted file is missing information.");
		exit(0);
	}
}

?>