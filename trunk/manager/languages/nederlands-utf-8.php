<?php

//*** Global language definitions.
$_LANG['global']['abbr'] = 'nl';
$_LANG['global']['charset'] = 'utf-8';
$_LANG['global']['text_dir'] = 'ltr'; // ('ltr' for left to right, 'rtl' for right to left)
$_LANG['global']['thousands_separator'] = '.';
$_LANG['global']['decimal_separator'] = ',';

//*** shortcuts for Byte, Kilo, Mega, Giga, Tera, Peta, Exa.
$_LANG['global']['byteUnits'] = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');

$_LANG['global']['day_of_week'] = array('Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za');
$_LANG['global']['month'] = array('Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec');

/* See http://www.php.net/manual/en/function.strftime.php to define the
 * variable below.
 */
$_LANG['global']['locale'] = 'nld_nld';
$_LANG['global']['datefmt'] = '%d %B %Y %H:%M';
$_LANG['global']['datefmtat'] = '%d %B %Y om %H:%M';
$_LANG['global']['timespanfmt'] = '%s dagen, %s uren, %s minuten en %s seconden';

$_LANG['global']['pageTitle'] = '%s (ondersteund door %s v%s)';
$_LANG['global']['noAccountPageTitle'] = 'Account niet gevonden';

//*** Login definitions.
$_LANG['login']['infoMain'] = "Vul uw gebruikersnaam en wachtwoord in.<br />Klik vervolgens op \"Inloggen\".";
$_LANG['login']['infoReminder'] = "Vul uw gebruikersnaam in en klik vervolgens op \"Email wachtwoord\". Het wachtwoord wordt gemaild naar het e-mail adres dat wij in ons bestand hebben.";
$_LANG['login']['labelUser'] = "Gebruikersnaam";
$_LANG['login']['labelPassword'] = "Wachtwoord";
$_LANG['login']['labelLanguage'] = "Interface taal";
$_LANG['login']['labelForgotPassword'] = "Wachtwoord vergeten?";
$_LANG['login']['labelRememberMe'] = "Wachtwoord op deze computer onthouden.";
$_LANG['login']['labelButtonLogin'] = "Inloggen";
$_LANG['login']['labelButtonCancel'] = "Annuleer";
$_LANG['login']['labelButtonReminder'] = "Email wachtwoord";
$_LANG['login']['invalidAccount'] = "Ongeldig Account";
$_LANG['login']['errorMain'] = "De gebruikersnaam en/of het wachtwoord zijn onjuist. Controleer uw gegevens en probeer het opnieuw.";
$_LANG['login']['subjectReminder'] = "Nieuw wachtwoord voor Mijn Punch";
$_LANG['login']['textReminder'] = "Beste %s,\n\nUw nieuwe wachtwoord voor Mijn Punch is:\n\n%s\n\nLogin via de volgende link: %s.";
$_LANG['login']['infoReminderSend'] = "Een nieuw wachtwoord is aan u verstuurd. <a href=\"?\" rel=\"internal\">Login</a> met uw gebruikersnaam en het nieuwe wachtwoord.";
$_LANG['login']['errorReminderSend'] = "De Gebruikersnaam werd niet gevonden. Controleer uw gegevens en probeer het opnieuw.";

//*** Header definitions.
$_LANG['head']['loggedInAs'] = "Ingelogd als";
$_LANG['head']['logout'] = "Uitloggen";

//*** Menu definitions.
$_LANG['menu']['mypunchAccount'] = "Pakket";
$_LANG['menu']['mypunchUsers'] = "Gebruikers";
$_LANG['menu']['mypunchProfile'] = "Profiel";
$_LANG['menu']['pcms'] = "PunchCMS";
$_LANG['menu']['pcmsElements'] = "Elementen";
$_LANG['menu']['pcmsTemplates'] = "Sjablonen";
$_LANG['menu']['pcmsForms'] = "Formulieren";
$_LANG['menu']['pcmsStorage'] = "Media bibliotheek";
$_LANG['menu']['pcmsInlineStorage'] = "+ Media browser";
$_LANG['menu']['pcmsCloseInlineStorage'] = "- Media browser";
$_LANG['menu']['pcmsOpenActionsMenu'] = "+ Acties";
$_LANG['menu']['pcmsCloseActionsMenu'] = "- Acties";
$_LANG['menu']['pcmsAliases'] = "URL Snelkoppelingen";
$_LANG['menu']['pcmsFeeds'] = "Externe bronnen";
$_LANG['menu']['pcmsLanguages'] = "Talen";
$_LANG['menu']['pcmsSettings'] = "Instellingen";
$_LANG['menu']['pcmsSearch'] = "Zoeken";
$_LANG['menu']['pcmsHelp'] = "Help";
$_LANG['menu']['pprojects'] = "PunchProjects";
$_LANG['menu']['pprojectsProjects'] = "Projecten";
$_LANG['menu']['pprojectsContacts'] = "Contactpersonen";
$_LANG['menu']['pprojectsCalendar'] = "Agenda";
$_LANG['menu']['pprojectsHelp'] = "Help";

//*** Button definitions.
$_LANG['button']['newElement'] = "Element";
$_LANG['button']['newTemplate'] = "Sjabloon";
$_LANG['button']['newField'] = "Veld";
$_LANG['button']['newFolder'] = "Map";
$_LANG['button']['newFile'] = "Bestand";
$_LANG['button']['newForm'] = "Formulier";
$_LANG['button']['newDynamic'] = "Dynamisch";
$_LANG['button']['newStructure'] = "Structuur";
$_LANG['button']['removeTemplate'] = "Verwijder";
$_LANG['button']['removeElement'] = "Verwijder";
$_LANG['button']['removeFolder'] = "Verwijder";
$_LANG['button']['removeForm'] = "Verwijder";
$_LANG['button']['duplicateTemplate'] = "Dupliceer";
$_LANG['button']['selectAll'] = "(De)selecteer alles";
$_LANG['button']['all'] = "Alle";
$_LANG['button']['preview'] = "Voorbeeld";
$_LANG['button']['duplicate'] = "Dupliceer";
$_LANG['button']['delete'] = "Verwijder";
$_LANG['button']['activate'] = "Activeer";
$_LANG['button']['deactivate'] = "Deactiveer";
$_LANG['button']['next'] = "Volgende";
$_LANG['button']['previous'] = "Vorige";
$_LANG['button']['cancel'] = "Annuleren";
$_LANG['button']['back'] = "Terug";
$_LANG['button']['choose'] = "Kies...";
$_LANG['button']['search'] = "Zoek";
$_LANG['button']['edit'] = "Aanpassen";
$_LANG['button']['new'] = "Nieuw";
$_LANG['button']['save'] = "Opslaan";
$_LANG['button']['close'] = "Sluiten";
$_LANG['button']['searchIndex'] = "Indexeer zoekresultaten";
$_LANG['button']['standardLanguage'] = "Zet als standaardtaal";
$_LANG['button']['languageAdd'] = "Nieuwe Taal";
$_LANG['button']['aliasAdd'] = "Nieuwe Snelkoppeling";
$_LANG['button']['feedAdd'] = "Nieuwe bron";
$_LANG['button']['insert'] = "Invoegen";
$_LANG['button']['alttag'] = "Omschrijving toevoegen...";

//*** Label definitions.
$_LANG['label']['in'] = "in";
$_LANG['label']['for'] = "voor";
$_LANG['label']['from'] = "van";
$_LANG['label']['fieldsFor'] = "Velden voor";
$_LANG['label']['elementsIn'] = "Elementen in";
$_LANG['label']['singleFile'] = "Bestand uploaden";
$_LANG['label']['newFolder'] = "Map aanmaken";
$_LANG['label']['details'] = "Details";
$_LANG['label']['detailsFor'] = "Details voor";
$_LANG['label']['templateDetails'] = "Sjabloon details";
$_LANG['label']['templateDetailsFor'] = "Sjabloon details voor";
$_LANG['label']['pageDetails'] = "Element details";
$_LANG['label']['pageDetailsFor'] = "Element details voor";
$_LANG['label']['dynamicDetails'] = "Dynamisch details";
$_LANG['label']['dynamicDetailsFor'] = "Dynamisch details voor";
$_LANG['label']['formDetails'] = "Formulier details";
$_LANG['label']['templateFieldDetails'] = "Veld details";
$_LANG['label']['templateFieldDetailsFor'] = "Veld details voor";
$_LANG['label']['pageNavigation'] = "Pagina %s van %s";
$_LANG['label']['withSelected'] = "met geselecteerde:";
$_LANG['label']['itemsPerPage'] = "Items per pagina:";
$_LANG['label']['typeOptions'] = "Type Opties";
$_LANG['label']['copyOf'] = "Kopie van %s";
$_LANG['label']['userprofile'] = "Gebruikers informatie";
$_LANG['label']['password'] = "Wachtwoord wijzigen";
$_LANG['label']['searchall'] = "Zoek op alle woorden";
$_LANG['label']['searchresult'] = "Resultaten voor";
$_LANG['label']['search_noresult'] = "Geen resultaten.";
$_LANG['label']['folderDetails'] = "Map Details";
$_LANG['label']['folderDetailsFor'] = "Map Details voor";
$_LANG['label']['fileDetails'] = "Bestand Details";
$_LANG['label']['fileDetailsFor'] = "Details voor bestand";
$_LANG['label']['elementFields'] = "Velden";
$_LANG['label']['editedBy'] = "Aangepast door";
$_LANG['label']['settings'] = "Instellingen";
$_LANG['label']['languages'] = "Talen";
$_LANG['label']['path'] = "Pad: ";
$_LANG['label']['help'] = "Help";
$_LANG['label']['altImage'] = "Alternatieve tekst";
$_LANG['label']['browseImage'] = "Bestand";
$_LANG['label']['default'] = "standaard";
$_LANG['label']['imagesCurrent'] = "Reeds geuploade bestanden";
$_LANG['label']['imagesNew'] = "Nieuwe afbeeldingen";
$_LANG['label']['filesCurrent'] = "Reeds geuploade bestanden";
$_LANG['label']['filesNew'] = "Nieuwe bestanden";
$_LANG['label']['standardLanguage'] = "Standaardtaal";
$_LANG['label']['fields'] = "Velden";
$_LANG['label']['publish'] = "Publicatie";
$_LANG['label']['permissions'] = "Rechten";
$_LANG['label']['startDate'] = "Begin datum";
$_LANG['label']['endDate'] = "Eind datum";
$_LANG['label']['date'] = "Datum";
$_LANG['label']['time'] = "Tijd";
$_LANG['label']['langDisabled'] = "Taal gedeactiveerd";
$_LANG['label']['aliases'] = "URL Snelkoppelingen";
$_LANG['label']['feeds'] = "Externe bronnen";
$_LANG['label']['pointsTo'] = "verwijst naar";
$_LANG['label']['aliasUnavailable'] = "Element niet beschikbaar";
$_LANG['label']['structureAdd'] = "Structuur toevoegen";
$_LANG['label']['structureDetails'] = "Structuur details";
$_LANG['label']['mediaIn'] = "Media in";
$_LANG['label']['poweredBy'] = "Ondersteund door";
$_LANG['label']['meta'] = "Meta-informatie";
$_LANG['label']['metaTitle'] = "Paginatitel";
$_LANG['label']['metaKeywords'] = "Sleutelwoorden";
$_LANG['label']['metaDescription'] = "Omschrijving";
$_LANG['label']['chooseFolder'] = "Kies een map";
$_LANG['label']['forLanguage'] = "voor <b>%s</b>";
$_LANG['label']['forAllLanguages'] = "voor <b>Alle talen</b>";

//*** Settings label definitions.
$_LANG['settingsLabel']['section_ftp'] = "FTP";
$_LANG['settingsLabel']['section_files'] = "Bestanden";
$_LANG['settingsLabel']['section_caching'] = "Caching";
$_LANG['settingsLabel']['section_aliases'] = "URL Snelkoppelingen";
$_LANG['settingsLabel']['section_audit'] = "Audit Log";
$_LANG['settingsLabel']['section_elements'] = "Elementen";
$_LANG['settingsLabel']['ftp_server'] = "Serveradres";
$_LANG['settingsLabel']['ftp_username'] = "Gebruikersnaam";
$_LANG['settingsLabel']['ftp_password'] = "Wachtwoord";
$_LANG['settingsLabel']['ftp_remote_folder'] = "Upload pad";
$_LANG['settingsLabel']['file_upload_extensions'] = "Bestand extensies";
$_LANG['settingsLabel']['image_upload_extensions'] = "Foto extensies";
$_LANG['settingsLabel']['file_folder'] = "Upload pad";
$_LANG['settingsLabel']['file_download'] = "Download link";
$_LANG['settingsLabel']['caching_enable'] = "Cache activeren";
$_LANG['settingsLabel']['caching_timeout'] = "Cache leeftijd (minuten)";
$_LANG['settingsLabel']['caching_folder'] = "Cache lokatie";
$_LANG['settingsLabel']['caching_ftp_folder'] = "Server cache lokatie";
$_LANG['settingsLabel']['aliases_enable'] = "Snelkoppelingen activeren";
$_LANG['settingsLabel']['feeds_enable'] = "Bronnen activeren";
$_LANG['settingsLabel']['audit_enable'] = "Audit log activeren";
$_LANG['settingsLabel']['audit_rotation'] = "Verwijder logs ouder dan (dagen)";
$_LANG['settingsLabel']['elmnt_active_state'] = "Nieuwe elementen zijn standaard geactiveerd";
$_LANG['settingsLabel']['web_server'] = "Website URL";

//*** Users and groups label definitions.
$_LANG['usersLabel']['users'] = "Gebruikers";
$_LANG['usersLabel']['groups'] = "Groepen";
$_LANG['usersLabel']['applications'] = "Applicaties";
$_LANG['usersLabel']['areas'] = "Omgevingen";
$_LANG['usersLabel']['area'] = "Omgeving";
$_LANG['usersLabel']['rights'] = "Rechten";
$_LANG['usersLabel']['subGroups'] = "Subgroepen";
$_LANG['usersLabel']['areaAdmins'] = "Omgevings Beheerders";
$_LANG['usersLabel']['impliedRights'] = "Ge&iuml;mpliceerde rechten";
$_LANG['usersLabel']['userDetails'] = "Gebruikersgegevens";
$_LANG['usersLabel']['groupDetails'] = "Groepsgegevens";
$_LANG['usersLabel']['areaDetails'] = "Omgevingsgegevens";
$_LANG['usersLabel']['applicationDetails'] = "Applicatiegegevens";
$_LANG['usersLabel']['rightDetails'] = "Rechtsgegevens";
$_LANG['usersLabel']['publishRights'] = "Publiceer rechten";
$_LANG['usersLabel']['selectedRights'] = "Geselecteerde rechten";
$_LANG['usersLabel']['availableRights'] = "Beschikbare rechten";
$_LANG['usersLabel']['selectedGroups'] = "Geselecteerde groepen";
$_LANG['usersLabel']['availableGroups'] = "Beschikbare groepen";
$_LANG['usersLabel']['selectedUsers'] = "Geselecteerde gebruikers";
$_LANG['usersLabel']['availableUsers'] = "Beschikbare gebruikers";
$_LANG['usersLabel']['selectedAreas'] = "Geselecteerde omgevingen";
$_LANG['usersLabel']['availableAreas'] = "Beschikbare omgevingen";
$_LANG['usersLabel']['selectedAdmins'] = "Geselecteerde beheerders";
$_LANG['usersLabel']['availableAdmins'] = "Beschikbare beheerders";
$_LANG['usersLabel']['userType'] = "Gebruikerstype";
$_LANG['usersLabel']['userName'] = "Gebruikersnaam";
$_LANG['usersLabel']['name'] = "Naam";
$_LANG['usersLabel']['emailAddress'] = "E-mail adres";
$_LANG['usersLabel']['password'] = "Wachtwoord";
$_LANG['usersLabel']['exportSuccess'] = "De rechten zijn succesvol ge&euml;xporteerd.";
$_LANG['usersLabel']['exportError'] = "Tijdens het exporteren van de rechten is een fout opgetreden.";
$_LANG['usersLabel']['typeAnonymous'] = "Anoniem";
$_LANG['usersLabel']['typeUser'] = "Gebruiker";
$_LANG['usersLabel']['typeAdmin'] = "Beheerder";
$_LANG['usersLabel']['typeAreaAdmin'] = "Omgevings Beheerder";
$_LANG['usersLabel']['typeSuperAdmin'] = "Super Beheerder";
$_LANG['usersLabel']['typeMasterAdmin'] = "Master Beheerder";
$_LANG['usersLabel']['application'] = "Applicatie";
$_LANG['usersLabel']['active'] = "Actief";
$_LANG['usersLabel']['groupLevel'] = "Element Ownership";
$_LANG['usersLabel']['levelUser'] = "Gebruiker";
$_LANG['usersLabel']['levelGroup'] = "Groep";
$_LANG['usersLabel']['levelAll'] = "Alle gebruikers";

//*** Field type label definitions.
$_LANG['typeLabel']['values'] = "Waardes";
$_LANG['typeLabel']['defaultValue'] = "Standaard Waarde";
$_LANG['typeLabel']['defaultValues'] = "Standaard Waarde(s)";
$_LANG['typeLabel']['maxImages'] = "Maximaal aantal afbeeldingen";
$_LANG['typeLabel']['imageSize'] = "Afmeting (B x H)";
$_LANG['typeLabel']['imageScale'] = "Manier van schalen";
$_LANG['typeLabel']['imageQuality'] = "Foto kwaliteit";
$_LANG['typeLabel']['maxFiles'] = "Maximaal aantal bestanden";
$_LANG['typeLabel']['fileExtensions'] = "Bestandsextensie(s)";
$_LANG['typeLabel']['charsMin'] = "Minimaal aantal letters";

//*** Form label definitions.
$_LANG['form']['templateName'] = "Sjabloon Naam";
$_LANG['form']['elementName'] = "Element Naam";
$_LANG['form']['formName'] = "Formulier Naam";
$_LANG['form']['folderName'] = "Map Naam";
$_LANG['form']['fileName'] = "Bestandsnaam";
$_LANG['form']['template'] = "Sjabloon";
$_LANG['form']['fieldName'] = "Veld Naam";
$_LANG['form']['fieldType'] = "Type";
$_LANG['form']['notes'] = "Opmerkingen";
$_LANG['form']['description'] = "Omschrijving";
$_LANG['form']['name'] = "Naam";
$_LANG['form']['shortName'] = "Korte Naam";
$_LANG['form']['pageContainer'] = "Complete Pagina";
$_LANG['form']['container'] = "Overnemen";
$_LANG['form']['forceCreation'] = "Automatisch aanmaken";
$_LANG['form']['active'] = "Actief";
$_LANG['form']['requiredFields'] = "Verplichte velden zijn met een * gemarkeerd.";
$_LANG['form']['requiredField'] = "Verplicht veld.";
$_LANG['form']['username'] = "Gebruikersnaam";
$_LANG['form']['emailaddress'] = "E-mail adres";
$_LANG['form']['language'] = "Websitetaal";
$_LANG['form']['timezone'] = "Tijdzone";
$_LANG['form']['currentpassword'] = "Huidig wachtwoord";
$_LANG['form']['newpassword'] = "Nieuw wachtwoord";
$_LANG['form']['verifypassword'] = "Herhaal wachtwoord";
$_LANG['form']['searchIndexed'] = "Alle elementen zijn opnieuw ge&iuml;ndexeerd voor de zoekmachine.";
$_LANG['form']['addLanguage'] = "Voeg een nieuwe taal toe";
$_LANG['form']['editLanguage'] = "Pas deze taal aan";
$_LANG['form']['publishInfo'] = "Selecteer de begin- en/of einddatum om deze te activeren.";
$_LANG['form']['permissionInfo'] = "Pas specifieke rechten voor dit element aan.";
$_LANG['form']['editAlias'] = "Pas deze snelkoppeling aan";
$_LANG['form']['editFeed'] = "Pas deze bron aan";
$_LANG['form']['addAlias'] = "Nieuwe snelkoppeling";
$_LANG['form']['addFeed'] = "Nieuwe bron";
$_LANG['form']['alias'] = "Snelkoppeling";
$_LANG['form']['feed'] = "Externe bron";
$_LANG['form']['basepath'] = "Begin pad";
$_LANG['form']['maxItems'] = "Maximum aantal items";
$_LANG['form']['sortBy'] = "Sorteer op";
$_LANG['form']['refresh'] = "Leeftijd";
$_LANG['form']['element'] = "Element";
$_LANG['form']['oneLevelDeeper'] = "Een niveau dieper";
$_LANG['form']['loading'] = "Wordt geladen...";
$_LANG['form']['structureName'] = "Structuur";
$_LANG['form']['sSelectLanguage'] = "* Taal";
$_LANG['form']['sSelectElement'] = "* Element";
$_LANG['form']['metaInfo'] = "Vul een pagina specifieke titel, sleutelwoorden en omschrijving in. Dit wordt door de meeste zoekmachines gebruikt.";
$_LANG['form']['loadingFiles'] = "Bestanden worden geladen...";
$_LANG['form']['allLanguages'] = "Alle talen";

//*** Form error definitions.
$_LANG['formerror']['main'] = "Er zijn fouten opgetreden bij het bewaren van uw gegevens. Controleer alle gemarkeerde velden en probeer het opnieuw.";
$_LANG['formerror']['commonRequired'] = "Dit is een verplicht veld.";
$_LANG['formerror']['commonTypeText'] = "U heeft ongeldige karakters ingevuld.";
$_LANG['formerror']['commonTypeWord'] = "U heeft ongeldige karakters ingevuld. U mag alleen letters en cijfers gebruiken.";
$_LANG['formerror']['commonTypePassword'] = "U heeft ongeldige karakters ingevuld.";
$_LANG['formerror']['templateName'] = "Vul een naam voor het sjabloon in.";
$_LANG['formerror']['elementName'] = "Vul een naam voor het element in.";
$_LANG['formerror']['formName'] = "Vul een naam voor het formulier in.";
$_LANG['formerror']['noFile'] = "Selecteer een bestand om te uploaden.";
$_LANG['formerror']['fieldName'] = "Vul een naam voor het veld in.";
$_LANG['formerror']['fieldType'] = "Kies het veld type.";
$_LANG['formerror']['profileName'] = "Vul uw naam in.";
$_LANG['formerror']['wrongPassword'] = "Onjuist wachtwoord.";
$_LANG['formerror']['shortPassword'] = "Het nieuwe wachtwoord is te kort.";
$_LANG['formerror']['passwordNotMatch'] = "Het nieuwe wachtwoord komt niet overeen.";
$_LANG['formerror']['structure'] = "Kies een structuur.";

//*** Tool tip definitions.
$_LANG['tip']['apiNameNote'] = "Dit veld wordt alleen gebruikt door de ontwikkelaar van de website. Veranderen van de waarde kan de werking van de website verstoren.";
$_LANG['tip']['containerNote'] = "Elementen gebaseerd op deze sjabloon kunnen ook elementen bevatten die gebruik maken van bovenliggende sjablonen. Standaard zal dat alleen met onderliggende sjablonen mogelijk zijn.";
$_LANG['tip']['forceCreationNote'] = "Een element gebaseerd op dit sjabloon wordt automatisch aangemaakt door het bovenliggende sjabloon.";
$_LANG['tip']['newpasswordNote'] = "Het wachtwoord moet uit minimaal %s karakters bestaan. Voor het wachtwoord kunt u letters, cijfers en de volgende leestekens gebruiken: . - _ ! @ # $ %% ^ ( &amp; * ? | .";
$_LANG['tip']['editElement'] = "Element aanpassen";
$_LANG['tip']['editTemplate'] = "Sjabloon aanpassen";
$_LANG['tip']['editFolder'] = "Map aanpassen";
$_LANG['tip']['templateDateType'] = "%a&nbsp;&nbsp;abbreviated weekday name<br />%A&nbsp;&nbsp;full weekday name<br />%b&nbsp;&nbsp;abbreviated month name <br />%B&nbsp;&nbsp;full month name<br />%C&nbsp;&nbsp;century number<br />%d&nbsp;&nbsp;the day of the month (00 .. 31)<br />%e&nbsp;&nbsp;the day of the month (0 .. 31)<br />%H&nbsp;&nbsp;hour (00 .. 23)<br />%I&nbsp;&nbsp;hour (01 .. 12)<br />%j&nbsp;&nbsp;day of the year (000 .. 366)<br />%H&nbsp;&nbsp;hour (0 .. 23)<br />%l&nbsp;&nbsp;hour (1 .. 12)<br />%m&nbsp;&nbsp;month (01 .. 12)<br />%M&nbsp;&nbsp;minute (00 .. 59)<br />%n&nbsp;&nbsp;a newline character&nbsp;&nbsp;<br />%p&nbsp;&nbsp;&quot;PM&quot; or &quot;AM&quot;<br />%P&nbsp;&nbsp;&quot;pm&quot; or &quot;am&quot;<br />%S&nbsp;&nbsp;second (00 .. 59)<br />%s&nbsp;&nbsp;number of seconds since Epoch<br />%t&nbsp;&nbsp;a tab character<br />%U&nbsp;&nbsp;the week number<br />%u&nbsp;&nbsp;the day of the week (1 .. 7, 1 = MON)<br />%w&nbsp;&nbsp;the day of the week (0 .. 6, 0 = SUN)<br />%y&nbsp;&nbsp;year without the century (00 .. 99)<br />%Y&nbsp;&nbsp;year including the century<br />%%&nbsp;&nbsp;a literal % character";
$_LANG['tip']['templateListType'] = "Gebruik een waarde per regel. Labels en waarden kunnen door een &quot;:&quot; teken gescheiden worden.";
$_LANG['tip']['templateImageType'] = "Vul een getal tussen 10 en 100 in. Standaardwaarde is 75.";
$_LANG['tip']['templateFileType'] = "Voorbeeld: .zip .xls .rar<br />Gebruik %s om te bestands extensies van de algemene instellingen mee te nemen.<br />Standaardwaarde zijn de extensies van de algemene instellingen.";
$_LANG['tip']['shortName'] = "Dit veld wordt gebruikt voor de taalwissel in de website.";
$_LANG['tip']['langElementCascade'] = "Waardes voor alle velden overnemen van de standaardtaal";
$_LANG['tip']['langFieldCascade'] = "Veldwaarde overnemen van de standaardtaal";
$_LANG['tip']['langElementUnlock'] = "Alle veldwaarden beschikbaar maken voor deze taal";
$_LANG['tip']['langFieldUnlock'] = "Veldwaarde beschikbaar maken voor deze taal";
$_LANG['tip']['langEnable'] = "Deze taal activeren";
$_LANG['tip']['langDisable'] = "Deze taal deactiveren";
$_LANG['tip']['alias'] = "Geef de naam van de snelkopping voor het navigeren naar het element vanuit de website.";
$_LANG['tip']['feed'] = "Geef de URL van de externe bron.";
$_LANG['tip']['basepath'] = "Geef het begin pad van het eerste item dat u wilt gebruiken.";
$_LANG['tip']['refresh'] = "Maximale leeftijd van de bron in minuten.";
$_LANG['tip']['structureAdd'] = "Kies de gewenste structuur uit de onderstaande lijst en druk op {$_LANG['button']['insert']}.";
$_LANG['tip']['structureSelects'] = "Deze structuur heeft een of meer velden die ingevuld moeten worden.";
$_LANG['tip']['metaKeywords'] = "Maximaal 20 sleutelwoorden voor de pagina.";
$_LANG['tip']['metaDescription'] = "Omschrijving van de pagina met maximaal 200 letters.";
$_LANG['tip']['storageName'] = "Als u het veld leeg laat wordt de naam van het bestand gebruikt.";
$_LANG['tip']['language'] = "U kunt de snelkoppeling aan een specifieke taal koppelen.";

//*** Alert definitions.
$_LANG['alert']['templateRemoveAlert'] = "Weet u zeker dat u deze sjabloon wilt verwijderen?\\nAlle onderliggende sjablonen en elementen gebaseerd op deze sjabloon worden ook verwijderd!";
$_LANG['alert']['templateFieldRemoveAlert'] = "Weet u zeker dat u dit veld wilt verwijderen?\\nDit kan de werking van elementen gebaseerd op deze sjabloon beinvloeden!";
$_LANG['alert']['templateFieldsRemoveAlert'] = "Weet u zeker dat u deze velden wilt verwijderen?\\nDit kan de werking van elementen gebaseerd op deze sjabloon beinvloeden!";
$_LANG['alert']['elementRemoveAlert'] = "Weet u zeker dat u dit element wilt verwijderen?\\nAlle onderliggende elementen worden ook verwijderd!";
$_LANG['alert']['storageItemRemoveAlert'] = "Weet u zeker dat u dit media element wilt verwijderen?";
$_LANG['alert']['storageItemsRemoveAlert'] = "Weet u zeker dat u deze media elementen wilt verwijderen?";
$_LANG['alert']['elementsRemoveAlert'] = "Weet u zeker dat u deze elementen wilt verwijderen?\\nAlle onderliggende elementen worden ook verwijderd!";
$_LANG['alert']['languageRemoveAlert'] = "Weet u zeker dat u deze taal wilt verwijderen?\\nAlle veldwaardes van deze taal worden ook verwijderd!";
$_LANG['alert']['multiItemEmpty'] = "U heeft geen item(s) geselecteerd waar u deze actie op uit kunt voeren."; 
$_LANG['alert']['newsWindowClose'] = "<b>Sluit dit venster niet</b><br />totdat de nieuwsbrief <b>volledig verzonden</b> is.<br />	Als u besluit om het toch te sluiten kunt u op een later tijdstip verder gaan.";
$_LANG['alert']['undefinedHeader'] = "Oeps!";
$_LANG['alert']['undefinedBody'] = "De applicatie is niet in staat om uw verzoek te verwerken. Neem contact op met de beheerder van de applicatie als dit vaker voorkomt.";
$_LANG['alert']['noAccount'] = "Wij kunnen uw account helaas niet vinden. Controleer de URL en probeer het opnieuw.";
$_LANG['alert']['aliasRemoveAlert'] = "Weet u zeker dat u deze snelkoppeling wilt verwijderen?\\nDit kan links op de website onbruikbaar maken!";
$_LANG['alert']['aliasesRemoveAlert'] = "Weet u zeker dat u deze snelkoppelingen wilt verwijderen?\\nDit kan links op de website onbruikbaar maken!";
$_LANG['alert']['feedRemoveAlert'] = "Weet u zeker dat u deze bron wilt verwijderen?\\nAlle dynamische elementen die gebruik maken van deze bron zullen ook verwijderd worden!";
$_LANG['alert']['feedsRemoveAlert'] = "Weet u zeker dat u deze bronnen wilt verwijderen?\\nAlle dynamische elementen die gebruik maken van deze bronnen zullen ook verwijderd worden!";
$_LANG['alert']['moveToFTP'] = "Uploaden naar de webserver is mislukt. Controleer de FTP instellingen en probeer het opnieuw.";
$_LANG['alert']['elementBeforeLanguage'] = "U moet eerst een standaardtaal aanmaken voordat u een element kunt aanmaken!";
$_LANG['alert']['newWindow'] = "Dit bestand zal in een nieuw venster openen.";


$_LANG['help']['docHeader'] = "Documentatie";
$_LANG['help']['docBody'] = "<p><a href=\"images/PunchCMS_handleiding.pdf\" rel=\"external\">Download</a> de documentatie van het CMS voor een inleiding en algemene uitleg van de functionaliteit.</p><p>Voor het bekijken van de documentatie heeft u de <a href=\"http://www.adobe.com/products/acrobat/readstep2.html\" rel=\"external\">Adobe Reader</a> nodig.</p>";

?>