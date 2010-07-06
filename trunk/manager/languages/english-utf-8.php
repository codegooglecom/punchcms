<?php

//*** Global language definitions.
$_LANG['global']['abbr'] = 'en';
$_LANG['global']['charset'] = 'utf-8';
$_LANG['global']['text_dir'] = 'ltr'; // ('ltr' for left to right, 'rtl' for right to left)
$_LANG['global']['thousands_separator'] = ',';
$_LANG['global']['decimal_separator'] = '.';

//*** shortcuts for Byte, Kilo, Mega, Giga, Tera, Peta, Exa.
$_LANG['global']['byteUnits'] = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');

$_LANG['global']['day_of_week'] = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
$_LANG['global']['month'] = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

/* See http://www.php.net/manual/en/function.strftime.php to define the
 * variable below.
 */
$_LANG['global']['locale'] = 'en_en';
$_LANG['global']['datefmt'] = '%B %d, %Y %I:%M %p';
$_LANG['global']['datefmtat'] = '%d %B %Y at %H:%M';
$_LANG['global']['timespanfmt'] = '%s days, %s hours, %s minutes and %s seconds';

$_LANG['global']['pageTitle'] = '%s (powered by %s v%s)';
$_LANG['global']['noAccountPageTitle'] = 'Account not found';

//*** Login definitions.
$_LANG['login']['infoMain'] = "Enter your Username and Password, then click the Login button.";
$_LANG['login']['infoReminder'] = "Enter your Username, then click the \"Send password\" button. The password will be send to the email address we have on file.";
$_LANG['login']['labelUser'] = "Username";
$_LANG['login']['labelPassword'] = "Password";
$_LANG['login']['labelLanguage'] = "Language";
$_LANG['login']['labelForgotPassword'] = "Forgot your password?";
$_LANG['login']['labelRememberMe'] = "Remember me on this computer.";
$_LANG['login']['labelButtonLogin'] = "Login";
$_LANG['login']['labelButtonCancel'] = "Cancel";
$_LANG['login']['labelButtonReminder'] = "Send password	";
$_LANG['login']['invalidAccount'] = "Invalid Account";
$_LANG['login']['errorMain'] = "Incorrect username and/or password. Please check your data and try again.";
$_LANG['login']['subjectReminder'] = "New password for Mijn Punch";
$_LANG['login']['textReminder'] = "Dear %s,\n\nYour new password for Mijn Punch is:\n\n%s\n\nUse the following link to log-in: %s.";
$_LANG['login']['infoReminderSend'] = "A new password has ben send. <a href=\"?\" rel=\"internal\">Log-in</a> using your username and the new password.";
$_LANG['login']['errorReminderSend'] = "The username could not be found. Please check the username and try again.";

//*** Header definitions.
$_LANG['head']['loggedInAs'] = "Logged in as";
$_LANG['head']['logout'] = "Log-out";

//*** Menu definitions.
$_LANG['menu']['mypunchAccount'] = "Account";
$_LANG['menu']['mypunchUsers'] = "Users";
$_LANG['menu']['mypunchProfile'] = "Profile";
$_LANG['menu']['pcms'] = "PunchCMS";
$_LANG['menu']['pcmsElements'] = "Elements";
$_LANG['menu']['pcmsTemplates'] = "Templates";
$_LANG['menu']['pcmsForms'] = "Forms";
$_LANG['menu']['pcmsStorage'] = "Media library";
$_LANG['menu']['pcmsInlineStorage'] = "+ Media browser";
$_LANG['menu']['pcmsCloseInlineStorage'] = "- Media browser";
$_LANG['menu']['pcmsOpenActionsMenu'] = "+ Actions";
$_LANG['menu']['pcmsCloseActionsMenu'] = "- Actions";
$_LANG['menu']['pcmsAliases'] = "URL Aliases";
$_LANG['menu']['pcmsFeeds'] = "Feeds";
$_LANG['menu']['pcmsLanguages'] = "Languages";
$_LANG['menu']['pcmsSettings'] = "Settings";
$_LANG['menu']['pcmsSearch'] = "Search";
$_LANG['menu']['pcmsHelp'] = "Help";
$_LANG['menu']['pprojects'] = "PunchProjects";
$_LANG['menu']['pprojectsProjects'] = "Projects";
$_LANG['menu']['pprojectsContacts'] = "Contacts";
$_LANG['menu']['pprojectsCalendar'] = "Calendar";
$_LANG['menu']['pprojectsHelp'] = "Help";

//*** Button definitions.
$_LANG['button']['newElement'] = "New Element";
$_LANG['button']['newTemplate'] = "New Template";
$_LANG['button']['newField'] = "New Field";
$_LANG['button']['newFolder'] = "New Folder";
$_LANG['button']['newFile'] = "New File";
$_LANG['button']['newForm'] = "New Form";
$_LANG['button']['newDynamic'] = "Dynamic";
$_LANG['button']['newStructure'] = "New Structure";
$_LANG['button']['removeTemplate'] = "Remove";
$_LANG['button']['removeElement'] = "Remove";
$_LANG['button']['removeFolder'] = "Remove";
$_LANG['button']['removeForm'] = "Remove";
$_LANG['button']['duplicateTemplate'] = "Duplicate";
$_LANG['button']['selectAll'] = "(De)select all";
$_LANG['button']['all'] = "All";
$_LANG['button']['preview'] = "preview";
$_LANG['button']['duplicate'] = "duplicate";
$_LANG['button']['delete'] = "remove";
$_LANG['button']['activate'] = "activate";
$_LANG['button']['deactivate'] = "deactivate";
$_LANG['button']['next'] = "Next";
$_LANG['button']['previous'] = "Previous";
$_LANG['button']['cancel'] = "Cancel";
$_LANG['button']['back'] = "Back";
$_LANG['button']['choose'] = "Choose...";
$_LANG['button']['search'] = "Search";
$_LANG['button']['edit'] = "Edit";
$_LANG['button']['new'] = "new";
$_LANG['button']['save'] = "Save";
$_LANG['button']['close'] = "Close";
$_LANG['button']['searchIndex'] = "Rebuild search index";
$_LANG['button']['standardLanguage'] = "Set as default language";
$_LANG['button']['languageAdd'] = "New language";
$_LANG['button']['aliasAdd'] = "New alias";
$_LANG['button']['feedAdd'] = "New feed";
$_LANG['button']['insert'] = "Insert";
$_LANG['button']['alttag'] = "Add description...";

//*** Label definitions.
$_LANG['label']['in'] = "in";
$_LANG['label']['for'] = "for";
$_LANG['label']['from'] = "from";
$_LANG['label']['fieldsFor'] = "Fields for";
$_LANG['label']['elementsIn'] = "Elements in";
$_LANG['label']['singleFile'] = "Upload file";
$_LANG['label']['newFolder'] = "New directory";
$_LANG['label']['details'] = "Details";
$_LANG['label']['detailsFor'] = "Details for";
$_LANG['label']['templateDetails'] = "Template details";
$_LANG['label']['templateDetailsFor'] = "Template details for";
$_LANG['label']['pageDetails'] = "Element details";
$_LANG['label']['pageDetailsFor'] = "Element details for";
$_LANG['label']['dynamicDetails'] = "Dynamic details";
$_LANG['label']['dynamicDetailsFor'] = "Dynamic details for";
$_LANG['label']['formDetails'] = "Form details";
$_LANG['label']['templateFieldDetails'] = "Field details";
$_LANG['label']['templateFieldDetailsFor'] = "Field details for";
$_LANG['label']['pageNavigation'] = "Page %s of %s";
$_LANG['label']['withSelected'] = "with selected:";
$_LANG['label']['itemsPerPage'] = "Items per page:";
$_LANG['label']['typeOptions'] = "Type options";
$_LANG['label']['copyOf'] = "Copy of %s";
$_LANG['label']['userprofile'] = "User profile";
$_LANG['label']['password'] = "Change password";
$_LANG['label']['searchall'] = "Search using all words";
$_LANG['label']['searchresult'] = "Results for";
$_LANG['label']['search_noresult'] = "No results.";
$_LANG['label']['folderDetails'] = "Folder details";
$_LANG['label']['folderDetailsFor'] = "Folder details for";
$_LANG['label']['fileDetails'] = "File Details";
$_LANG['label']['fileDetailsFor'] = "File details for";
$_LANG['label']['elementFields'] = "Fields";
$_LANG['label']['editedBy'] = "Edited by";
$_LANG['label']['settings'] = "Settings";
$_LANG['label']['languages'] = "Languages";
$_LANG['label']['path'] = "Path: ";
$_LANG['label']['help'] = "Help";
$_LANG['label']['altImage'] = "Alternative text";
$_LANG['label']['browseImage'] = "File";
$_LANG['label']['default'] = "default";
$_LANG['label']['imagesCurrent'] = "Images currently uploaded";
$_LANG['label']['imagesNew'] = "Images selected for uploading";
$_LANG['label']['filesCurrent'] = "Files currently uploaded";
$_LANG['label']['filesNew'] = "Files selected for uploading";
$_LANG['label']['standardLanguage'] = "Default language";
$_LANG['label']['fields'] = "Fields";
$_LANG['label']['publish'] = "Schedule";
$_LANG['label']['permissions'] = "Permissions";
$_LANG['label']['startDate'] = "Start date";
$_LANG['label']['endDate'] = "End date";
$_LANG['label']['date'] = "Date";
$_LANG['label']['time'] = "Time";
$_LANG['label']['langDisabled'] = "Language disabled";
$_LANG['label']['aliases'] = "URL Aliases";
$_LANG['label']['feeds'] = "Feeds";
$_LANG['label']['pointsTo'] = "points to";
$_LANG['label']['aliasUnavailable'] = "Target not available";
$_LANG['label']['structureAdd'] = "Add Structure";
$_LANG['label']['structureDetails'] = "Structure details";
$_LANG['label']['mediaIn'] = "Media in";
$_LANG['label']['poweredBy'] = "Powered by";
$_LANG['label']['meta'] = "Meta-information";
$_LANG['label']['metaTitle'] = "Page title";
$_LANG['label']['metaKeywords'] = "Keywords";
$_LANG['label']['metaDescription'] = "Page description";
$_LANG['label']['chooseFolder'] = "Choose a folder";

//*** Settings label definitions.
$_LANG['settingsLabel']['section_ftp'] = "FTP";
$_LANG['settingsLabel']['section_files'] = "Files";
$_LANG['settingsLabel']['section_caching'] = "Caching";
$_LANG['settingsLabel']['section_aliases'] = "URL Aliases";
$_LANG['settingsLabel']['section_audit'] = "Audit Log";
$_LANG['settingsLabel']['section_elements'] = "Elements";
$_LANG['settingsLabel']['ftp_server'] = "Server address";
$_LANG['settingsLabel']['ftp_username'] = "Username";
$_LANG['settingsLabel']['ftp_password'] = "Password";
$_LANG['settingsLabel']['ftp_remote_folder'] = "Upload folder";
$_LANG['settingsLabel']['file_upload_extensions'] = "File extensions";
$_LANG['settingsLabel']['image_upload_extensions'] = "Image extensions";
$_LANG['settingsLabel']['file_folder'] = "Upload folder";
$_LANG['settingsLabel']['file_download'] = "Download link";
$_LANG['settingsLabel']['caching_enable'] = "Enable caching";
$_LANG['settingsLabel']['caching_timeout'] = "Cache timeout (minutes)";
$_LANG['settingsLabel']['caching_folder'] = "Cache folder";
$_LANG['settingsLabel']['caching_ftp_folder'] = "Remote cache folder";
$_LANG['settingsLabel']['aliases_enable'] = "Enable aliases";
$_LANG['settingsLabel']['feeds_enable'] = "Enable feeds";
$_LANG['settingsLabel']['audit_enable'] = "Enable audit log";
$_LANG['settingsLabel']['audit_rotation'] = "Remove logs older than (days)";
$_LANG['settingsLabel']['elmnt_active_state'] = "Activate new elements by default";
$_LANG['settingsLabel']['web_server'] = "Website URL";

//*** Users and groups label definitions.
$_LANG['usersLabel']['users'] = "Users";
$_LANG['usersLabel']['groups'] = "Groups";
$_LANG['usersLabel']['applications'] = "Applications";
$_LANG['usersLabel']['areas'] = "Areas";
$_LANG['usersLabel']['area'] = "Area";
$_LANG['usersLabel']['rights'] = "Rights";
$_LANG['usersLabel']['subGroups'] = "Sub Groups";
$_LANG['usersLabel']['areaAdmins'] = "Area Administrators";
$_LANG['usersLabel']['impliedRights'] = "Implied rights";
$_LANG['usersLabel']['userDetails'] = "User Details";
$_LANG['usersLabel']['groupDetails'] = "Group Details";
$_LANG['usersLabel']['areaDetails'] = "Area Details";
$_LANG['usersLabel']['applicationDetails'] = "Application Details";
$_LANG['usersLabel']['rightDetails'] = "Right Details";
$_LANG['usersLabel']['publishRights'] = "Publish rights";
$_LANG['usersLabel']['selectedRights'] = "Selected rights";
$_LANG['usersLabel']['availableRights'] = "Available rights";
$_LANG['usersLabel']['selectedGroups'] = "Selected groups";
$_LANG['usersLabel']['availableGroups'] = "Available groups";
$_LANG['usersLabel']['selectedUsers'] = "Selected users";
$_LANG['usersLabel']['availableUsers'] = "Available users";
$_LANG['usersLabel']['selectedAreas'] = "Selected areas";
$_LANG['usersLabel']['availableAreas'] = "Available areas";
$_LANG['usersLabel']['selectedAdmins'] = "Selected administrators";
$_LANG['usersLabel']['availableAdmins'] = "Available administrators";
$_LANG['usersLabel']['userType'] = "User Type";
$_LANG['usersLabel']['userName'] = "User Name";
$_LANG['usersLabel']['name'] = "Name";
$_LANG['usersLabel']['emailAddress'] = "E-mail Address";
$_LANG['usersLabel']['password'] = "Password";
$_LANG['usersLabel']['exportSuccess'] = "Right Constantes have been exported successfully.";
$_LANG['usersLabel']['exportError'] = "Error while exporting Right Constantes.";
$_LANG['usersLabel']['typeAnonymous'] = "Anonymous";
$_LANG['usersLabel']['typeUser'] = "User";
$_LANG['usersLabel']['typeAdmin'] = "Administrator";
$_LANG['usersLabel']['typeAreaAdmin'] = "Area Administrator";
$_LANG['usersLabel']['typeSuperAdmin'] = "Super Administrator";
$_LANG['usersLabel']['typeMasterAdmin'] = "Master Administrator";
$_LANG['usersLabel']['application'] = "Application";
$_LANG['usersLabel']['active'] = "Active";
$_LANG['usersLabel']['groupLevel'] = "Element Ownership";
$_LANG['usersLabel']['levelUser'] = "User";
$_LANG['usersLabel']['levelGroup'] = "Group";
$_LANG['usersLabel']['levelAll'] = "Everybody";

//*** Field type label definitions.
$_LANG['typeLabel']['values'] = "Values";
$_LANG['typeLabel']['defaultValue'] = "Default Value";
$_LANG['typeLabel']['defaultValues'] = "Default Value(s)";
$_LANG['typeLabel']['maxImages'] = "Maximum images";
$_LANG['typeLabel']['imageSize'] = "Image size (W x H)";
$_LANG['typeLabel']['imageScale'] = "Scale method";
$_LANG['typeLabel']['imageQuality'] = "Image quality";
$_LANG['typeLabel']['maxFiles'] = "Maximum files";
$_LANG['typeLabel']['fileExtensions'] = "File extension(s)";
$_LANG['typeLabel']['charsMin'] = "Minimum Characters";

//*** Form label definitions.
$_LANG['form']['templateName'] = "Template name";
$_LANG['form']['elementName'] = "Element name";
$_LANG['form']['formName'] = "Form name";
$_LANG['form']['folderName'] = "Folder name";
$_LANG['form']['fileName'] = "File name";
$_LANG['form']['template'] = "Template";
$_LANG['form']['fieldName'] = "Field name";
$_LANG['form']['fieldType'] = "Type";
$_LANG['form']['notes'] = "Notes";
$_LANG['form']['description'] = "Description";
$_LANG['form']['name'] = "Name";
$_LANG['form']['shortName'] = "Short Name";
$_LANG['form']['pageContainer'] = "Full Page";
$_LANG['form']['container'] = "Can also contain parent templates";
$_LANG['form']['forceCreation'] = "Force creation by parent";
$_LANG['form']['active'] = "Active";
$_LANG['form']['requiredFields'] = "Required fields are marked with an *.";
$_LANG['form']['requiredField'] = "Required field.";
$_LANG['form']['username'] = "User name";
$_LANG['form']['emailaddress'] = "E-mail address";
$_LANG['form']['language'] = "Language";
$_LANG['form']['timezone'] = "Time zone";
$_LANG['form']['currentpassword'] = "Current password";
$_LANG['form']['newpassword'] = "New password";
$_LANG['form']['verifypassword'] = "Verify password";
$_LANG['form']['searchIndexed'] = "All elements have been re-indexed for the search engine.";
$_LANG['form']['addLanguage'] = "Add a new language";
$_LANG['form']['editLanguage'] = "Edit language";
$_LANG['form']['publishInfo'] = "It's possible to set the exact time an element is published.";
$_LANG['form']['permissionInfo'] = "Edit permissions for this element.";
$_LANG['form']['editAlias'] = "Edit alias";
$_LANG['form']['addAlias'] = "Add a new alias";
$_LANG['form']['alias'] = "Alias";
$_LANG['form']['editFeed'] = "Edit feed";
$_LANG['form']['addFeed'] = "Add a new feed";
$_LANG['form']['feed'] = "Feed";
$_LANG['form']['refresh'] = "Minutes until next refresh";
$_LANG['form']['element'] = "Element";
$_LANG['form']['oneLevelDeeper'] = "One level deeper";
$_LANG['form']['loading'] = "Loading...";
$_LANG['form']['structureName'] = "Structure";
$_LANG['form']['sSelectLanguage'] = "* Language";
$_LANG['form']['sSelectElement'] = "* Element";
$_LANG['form']['metaInfo'] = "Set page specific title, keywords and description. Used by most search engines.";
$_LANG['form']['loadingFiles'] = "Loading files...";

//*** Form error definitions.
$_LANG['formerror']['main'] = "Errors occured while saving your data. Check all marked fields and try again.";
$_LANG['formerror']['commonRequired'] = "This is a required field.";
$_LANG['formerror']['commonTypeText'] = "You have entered invalid characters.";
$_LANG['formerror']['commonTypeWord'] = "You have entered invalid characters. You are only allowed to use alfanumerical characters.";
$_LANG['formerror']['commonTypePassword'] = "You have entered invalid characters.";
$_LANG['formerror']['templateName'] = "Enter a name for the template.";
$_LANG['formerror']['elementName'] = "Enter a name for the element.";
$_LANG['formerror']['formName'] = "Enter a name for the form.";
$_LANG['formerror']['fieldName'] = "Enter a name for the field.";
$_LANG['formerror']['fieldType'] = "Choose a field type.";
$_LANG['formerror']['profileName'] = "Enter your name.";
$_LANG['formerror']['wrongPassword'] = "Wrong password.";
$_LANG['formerror']['shortPassword'] = "The new password is too short.";
$_LANG['formerror']['passwordNotMatch'] = "The new password does not match.";
$_LANG['formerror']['structure'] = "Choose a structure.";

//*** Tool tip definitions.
$_LANG['tip']['apiNameShort'] = "API: Application Programming Interface.";
$_LANG['tip']['apiNameNote'] = "This field is used by the web developer. Changing this value can badly invluence the way your website works.";
$_LANG['tip']['containerNote'] = "Elements based on this template can also contain elements based on parent templates. This is by default only possible with child templates.";
$_LANG['tip']['forceCreationNote'] = "An element based on this template will be created by the parent automatically.";
$_LANG['tip']['newpasswordNote'] = "The password must be at least %s characters long. For your password you can use alfanumerical and the following characters: . - _ ! @ # $ %% ^ ( &amp; * ? | .";
$_LANG['tip']['editElement'] = "Edit element";
$_LANG['tip']['editTemplate'] = "Edit template";
$_LANG['tip']['editFolder'] = "Edit folder";
$_LANG['tip']['templateDateType'] = "%a&nbsp;&nbsp;abbreviated weekday name<br />%A&nbsp;&nbsp;full weekday name<br />%b&nbsp;&nbsp;abbreviated month name <br />%B&nbsp;&nbsp;full month name<br />%C&nbsp;&nbsp;century number<br />%d&nbsp;&nbsp;the day of the month (00 .. 31)<br />%e&nbsp;&nbsp;the day of the month (0 .. 31)<br />%H&nbsp;&nbsp;hour (00 .. 23)<br />%I&nbsp;&nbsp;hour (01 .. 12)<br />%j&nbsp;&nbsp;day of the year (000 .. 366)<br />%H&nbsp;&nbsp;hour (0 .. 23)<br />%l&nbsp;&nbsp;hour (1 .. 12)<br />%m&nbsp;&nbsp;month (01 .. 12)<br />%M&nbsp;&nbsp;minute (00 .. 59)<br />%n&nbsp;&nbsp;a newline character&nbsp;&nbsp;<br />%p&nbsp;&nbsp;&quot;PM&quot; or &quot;AM&quot;<br />%P&nbsp;&nbsp;&quot;pm&quot; or &quot;am&quot;<br />%S&nbsp;&nbsp;second (00 .. 59)<br />%s&nbsp;&nbsp;number of seconds since Epoch<br />%t&nbsp;&nbsp;a tab character<br />%U&nbsp;&nbsp;the week number<br />%u&nbsp;&nbsp;the day of the week (1 .. 7, 1 = MON)<br />%w&nbsp;&nbsp;the day of the week (0 .. 6, 0 = SUN)<br />%y&nbsp;&nbsp;year without the century (00 .. 99)<br />%Y&nbsp;&nbsp;year including the century<br />%%&nbsp;&nbsp;a literal % character";
$_LANG['tip']['templateListType'] = "Use one list value per line. Labels and values can be separated using a &quot;:&quot; character.";
$_LANG['tip']['templateImageType'] = "Should be a number between 10 and 100. Default is 75.";
$_LANG['tip']['templateFileType'] = "Example: .zip .xls .rar<br />Use %s to append the file extensions from the general settings.<br />Default are the extensions from the general settings.";
$_LANG['tip']['shortName'] = "This field is used for the language switch in the website.";
$_LANG['tip']['langElementCascade'] = "Cascade from default language";
$_LANG['tip']['langFieldCascade'] = "Cascade from default language";
$_LANG['tip']['langElementUnlock'] = "Unlock for the current language";
$_LANG['tip']['langFieldUnlock'] = "Unlock this field for the current language";
$_LANG['tip']['langEnable'] = "Enable this language";
$_LANG['tip']['langDisable'] = "Disable this language";
$_LANG['tip']['alias'] = "Enter the desired alias for the element navigation in the website.";
$_LANG['tip']['feed'] = "Enter the Url of the RSS or XML feed.";
$_LANG['tip']['refresh'] = "Amount of minutes until the feed gets refreshed.";
$_LANG['tip']['structureAdd'] = "Choose the desired structure from the list below and press {$_LANG['button']['insert']}.";
$_LANG['tip']['structureSelects'] = "This structure has one or more fields that need to be set.";
$_LANG['tip']['metaKeywords'] = "A maximum of 20 keywords describing the page.";
$_LANG['tip']['metaDescription'] = "Description of the page using a maximum of 200 characters.";
$_LANG['tip']['storageName'] = "Leave this field empty to use the name of the uploaded file.";

//*** Alert definitions.
$_LANG['alert']['templateRemoveAlert'] = "Are you sure you want to remove this template?\\nAll nested templates and elements based on this template will also be removed!";
$_LANG['alert']['templateFieldRemoveAlert'] = "Are you sure you want to remove this field?\\nThis could badly invluence the operation of elements based on this template!";
$_LANG['alert']['templateFieldsRemoveAlert'] = "Are you sure you want to remove these fields?\\nThis could badly invluence the operation of elements based on this template!";
$_LANG['alert']['elementRemoveAlert'] = "Are you sure you want to remove this element?\\nAll nested elements will also be removed!";
$_LANG['alert']['elementsRemoveAlert'] = "Are you sure you want to remove these elements?\\nAll nested elements will also be removed!";
$_LANG['alert']['storageItemRemoveAlert'] = "Are you sure you want to remove this media element?";
$_LANG['alert']['storageItemsRemoveAlert'] = "Are you sure you want to remove these media elements?";
$_LANG['alert']['languageRemoveAlert'] = "Are you sure you want to remove this language?\\nAll field values for this language will also be removed!";
$_LANG['alert']['multiItemEmpty'] = "There are no item(s) selected.";
$_LANG['alert']['newsWindowClose'] = "<b>Do not close this window</b><br />until the newsletter has been <b>fully send</b>.<br />	If you do close it you can resume at a later time.";
$_LANG['alert']['undefinedHeader'] = "Whoeps!";
$_LANG['alert']['undefinedBody'] = "Currently the application is unable to process your request. Please contact the application administrator if this happens frequently.";
$_LANG['alert']['noAccount'] = "We were unable to locate your account. Please check the URL and try again.";
$_LANG['alert']['aliasRemoveAlert'] = "Are you sure you want to remove this alias?\\nThis could render links on the website unusable!";
$_LANG['alert']['aliasesRemoveAlert'] = "Are you sure you want to remove these aliases?\\nThis could render links on the website unusable!";
$_LANG['alert']['feedRemoveAlert'] = "Are you sure you want to remove this feed?\\nAlle dynamic elements using this feed will also be removed!";
$_LANG['alert']['feedRemoveAlert'] = "Are you sure you want to remove these feeds?\\nAlle dynamic elements using these feeds will also be removed!";
$_LANG['alert']['moveToFTP'] = "Upload to the webserver failed. Check the FTP settings and try again.";
$_LANG['alert']['elementBeforeLanguage'] = "You need to create a default language before you can create an element!";
$_LANG['alert']['newWindow'] = "This file will open in a new window.";


$_LANG['help']['docHeader'] = "Documentation";
$_LANG['help']['docBody'] = "<p><a href=\"images/PunchCMS_manual.pdf\" rel=\"external\">Download</a> the CMS documentation to get an overall overview of the functionality.</p><p>You need <a href=\"http://www.adobe.com/products/acrobat/readstep2.html\" rel=\"external\">Adobe Reader</a> to view the documentation.</p>";

?>