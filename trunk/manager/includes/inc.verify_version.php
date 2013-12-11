<?php

function verifyVersion ($strVersion)
{
    $varReturn = true;

    switch ($strVersion) {
        case "2.6.1":
            $varEditAfterSave = Setting::getValueByName("edit_after_save");
            if (strlen($varEditAfterSave) <= 0) {
                $varReturn = false;
            }

            // Fall-through to previous versions
        case "2.6":
            $varNextAfterSave = Setting::getValueByName("next_after_save");
            if (strlen($varNextAfterSave) <= 0) {
                $varReturn = false;
            }

            $varNextIsChild = Setting::getValueByName("next_is_child");
            if (strlen($varNextIsChild) <= 0) {
                $varReturn = false;
            }
            break;
    }

    return $varReturn;
}

function executeUpdateScript(HTML_Template_IT &$objTpl)
{
    if (!verifyVersion($GLOBALS["_CONF"]["app"]["version"])) {
        $objTpl->setCurrentBlock("update");
        $objTpl->setVariable("VERSION", $GLOBALS["_CONF"]["app"]["version"]);
        $objTpl->parseCurrentBlock();
    }
}
