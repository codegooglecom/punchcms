<?php

define("APP_NAME", "PunchCMS");
define("APP_VERSION", "2.5.2");
define("APP_DEFAULT_STARTDATE", "0000-00-00 00:00:00");
define("APP_DEFAULT_ENDDATE", "2100-01-01 01:00:00");

define('SQL_CONN_ERROR', 1);
define('SQL_DB_ERROR', 2);
define('SQL_QUERY_ERROR', 3);

define("ELM_TYPE_FOLDER", 1);
define("ELM_TYPE_ELEMENT", 2);
define("ELM_TYPE_CONTAINER", 3);
define("ELM_TYPE_DYNAMIC", 4);
define("ELM_TYPE_LOCKED", 5);
define("ELM_TYPE_ALL", "'1','2','3','4','5'");

define('NAV_MYPUNCH_ACCOUNT', 17);
define('NAV_MYPUNCH_PROFILE', 2);
define('NAV_MYPUNCH_USERS', 6);
define('NAV_MYPUNCH_LOGIN', 9);
define('NAV_MYPUNCH_USERS_USER', 10);
define('NAV_MYPUNCH_USERS_GROUP', 11);
define('NAV_MYPUNCH_USERS_APPLICATION', 12);
define('NAV_MYPUNCH_USERS_AREA', 13);
define('NAV_MYPUNCH_USERS_RIGHT', 14);
define('NAV_MYPUNCH_PCMS',18);
define('NAV_MYPUNCH_ANNOUNCEMENTS',24);
define('NAV_MYPUNCH_NOACCOUNT', 28);
define('NAV_PCMS_ELEMENTS', 1);
define('NAV_PCMS_TEMPLATES', 5);
define('NAV_PCMS_HELP', 16);
define('NAV_PCMS_SETTINGS', 7);
define('NAV_PCMS_SEARCH', 8);
define('NAV_PCMS_LANGUAGES', 25);
define('NAV_PCMS_FORMS', 26);
define('NAV_PCMS_STORAGE', 27);
define('NAV_PCMS_ALIASES', 29);
define('NAV_PCMS_FEEDS', 30);
define('NAV_MYPUNCH_PPROJECTS',19);
define('NAV_PPROJECTS_PROJECTS',20);
define('NAV_PPROJECTS_CONTACTS',21);
define('NAV_PPROJECTS_CALENDAR',22);
define('NAV_PPROJECTS_HELP',23);

define('CMD_LIST', 1);
define('CMD_ADD', 2);
define('CMD_EDIT', 3);
define('CMD_REMOVE', 4);
define('CMD_LOGOUT', 5);
define('CMD_PASSREMIND', 6);
define('CMD_ADD_FIELD', 7);
define('CMD_EDIT_FIELD', 8);
define('CMD_REMOVE_FIELD', 9);
define('CMD_DUPLICATE_FIELD', 10);
define('CMD_DUPLICATE', 11);
define('CMD_SORT', 12);
define('CMD_ADD_FOLDER', 13);
define('CMD_EDIT_FOLDER', 14);
define('CMD_BUILD_INDEX', 15);
define('CMD_SET_DEFAULT', 16);
define('CMD_ACTIVATE', 17);
define('CMD_DEACTIVATE', 18);
define('CMD_ADD_STRUCTURE', 19);
define('CMD_ADD_STRUCTURE_DETAIL', 20);
define('CMD_ADD_DYNAMIC', 21);

define('FIELD_TYPE_DATE', 1);
define('FIELD_TYPE_SMALLTEXT', 2);
define('FIELD_TYPE_LARGETEXT', 3);
define('FIELD_TYPE_FILE', 4);
define('FIELD_TYPE_NUMBER', 5);
define('FIELD_TYPE_SELECT_LIST_MULTI', 6);
define('FIELD_TYPE_IMAGE', 7);
define('FIELD_TYPE_USER', 8);
define('FIELD_TYPE_LINK', 9);
define('FIELD_TYPE_BOOLEAN', 10);
define('FIELD_TYPE_SELECT_LIST_SINGLE', 11);
define('FIELD_TYPE_CHECK_LIST_MULTI', 12);
define('FIELD_TYPE_CHECK_LIST_SINGLE', 13);
define('FIELD_TYPE_SIMPLETEXT', 14);

define('PRODUCT_PCMS', 1);

define('AUDIT_TYPE_ELEMENT', 1);
define('AUDIT_TYPE_TEMPLATE', 2);
define('AUDIT_TYPE_TEMPLATEFIELD', 3);
define('AUDIT_TYPE_ALIAS', 4);
define('AUDIT_TYPE_LANGUAGE', 5);
define('AUDIT_TYPE_SETTING', 6);
define('AUDIT_TYPE_USER', 7);
define('AUDIT_TYPE_STORAGE', 8);
define('AUDIT_TYPE_FEED', 9);

define('STORAGE_TYPE_FOLDER', 1);
define('STORAGE_TYPE_FILE', 2);
define("STORAGE_TYPE_ALL", "'1','2'");

?>