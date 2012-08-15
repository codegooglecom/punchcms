<?php

/**
 * 
 * Holds the PunchCMS Valid Form classes.
 * Depends on ValidForm Builder and htmlMimeMail5.
 * @author felix
 * @version 0.1.7.6
 *
 */
class PCMS_WizardBuilder extends PCMS_FormBuilder {
	private $__formElement	= FALSE;
	private $__maxLengthAlert = "";
	private $__minLengthAlert = "";
	private $__requiredAlert = "";
	public $__validForm	= FALSE;

	public function __construct($objForm, $strAction = null) {
		$this->__formElement = $objForm;
		$strName = $objForm->getName();
		$strName = (empty($strName)) ? $objForm->getId() : strtolower($strName);
		$this->__validForm = new ValidWizard("validwizard_" . $strName, $this->__formElement->getField("RequiredBody")->getHtmlValue(), $strAction);
	}

	public function buildForm($blnSend = TRUE, $blnClientSide = TRUE) {
		$objCms = PCMS_Client::getInstance();
	
		$strReturn = "";
	
		$this->__maxLengthAlert = $this->__formElement->getField("AlertMaxLength")->getHtmlValue();
		$this->__minLengthAlert = $this->__formElement->getField("AlertMinLength")->getHtmlValue();
		$this->__requiredAlert = $this->__formElement->getField("AlertRequired")->getHtmlValue();

		$this->__validForm->setRequiredStyle($this->__formElement->getField("RequiredIndicator")->getHtmlValue());
		$this->__validForm->setMainAlert($this->__formElement->getField("AlertMain")->getHtmlValue());

		//*** Form starts here.
		$objPages = $this->__formElement->getElementsByTemplate(array("Page", "Paragraph"));
		foreach ($objPages as $objPage) {
			$this->renderPage($this->__validForm, $objPage);

			$objFieldsets = $objPage->getElementsByTemplate(array("Fieldset", "Paragraph"));
			foreach ($objFieldsets as $objFieldset) {
				switch ($objFieldset->getTemplateName()) {
					case "Paragraph":
						$this->renderParagraph($this->__validForm, $objFieldset);
						break;
					case "Fieldset":
						$this->renderFieldset($this->__validForm, $objFieldset);

						$objFields = $objFieldset->getElementsByTemplate(array("Field", "Area", "ListField", "MultiField"));
						foreach ($objFields as $objField) {
							switch ($objField->getTemplateName()) {
								case "Field":
									$this->renderField($this->__validForm, $objField);
									break;
									
								case "ListField":
									$this->renderListField($this->__validForm, $objField);
									break;			
		
								case "Area":
									$this->renderArea($this->__validForm, $objField);
									break;
									
								case "MultiField":
									$this->renderMultiField($this->__validForm, $objField);
									break;
									
							}
						}
				}
			}
		}

		$this->__validForm->setSubmitLabel($this->__formElement->getField("SendLabel")->getHtmlValue());

		if ($this->__validForm->isSubmitted() && $this->__validForm->isValid()) {
			if ($blnSend) {
				$objRecipientEmails = $this->__formElement->getElementsByTemplate("RecipientEmail");	
				foreach ($objRecipientEmails as $objRecipientEmail) {
					$strHtmlBody = "<html><head><title></title></head><body>";
					$strHtmlBody .= sprintf($objRecipientEmail->getField("Body")->getHtmlValue(), $this->__validForm->valuesAsHtml(TRUE));
					$strHtmlBody .= "</body></html>";
	
					//*** Build the e-mail.
					$strTextBody = str_replace("<br /> ", "<br />", $strHtmlBody);
					$strTextBody = str_replace("<br />", "\n", $strTextBody);
					$strTextBody = str_replace("&nbsp;","",$strTextBody);
					$strTextBody = strip_tags($strTextBody);
					$strTextBody = html_entity_decode($strTextBody, ENT_COMPAT, "UTF-8");
	
					$varEmailId = $objRecipientEmail->getField("SenderEmail")->getValue();
					$objEmailElement = $objCms->getElementById($varEmailId);
					$strFrom = "";
					if (is_object($objEmailElement)) {
						$varEmailId = $objEmailElement->getElement()->getApiName();
						if (empty($varEmailId)) $varEmailId = $objEmailElement->getId();
						$strFrom = $this->__validForm->getValidField("formfield_" . strtolower($varEmailId))->getValue();
					}
					
					//*** Send the email.
					$objMail = new htmlMimeMail5();
					$objMail->setHTMLEncoding(new Base64Encoding());
					$objMail->setTextCharset("utf-8");
					$objMail->setHTMLCharset("utf-8");
					$objMail->setHeadCharset("utf-8");
					$objMail->setFrom($strFrom);
					$objMail->setSubject($objRecipientEmail->getField("Subject")->getHtmlValue());
					$objMail->setText($strTextBody);
					$objMail->setHTML($strHtmlBody);
					if (!$objMail->send(explode(",", $objRecipientEmail->getField("RecipientEmail")->getHtmlValue()))) {
						echo $objMail->errors;
					}
				}
	
				$objSenderEmails = $this->__formElement->getElementsByTemplate("SenderEmail");	
				foreach ($objSenderEmails as $objSenderEmail) {
					$strHtmlBody = "<html><head><title></title></head><body>";
					$strHtmlBody .= sprintf($objSenderEmail->getField("Body")->getHtmlValue(), $this->__validForm->valuesAsHtml(TRUE));
					$strHtmlBody .= "</body></html>";
	
					//*** Build the e-mail.
					$strTextBody = str_replace("<br /> ", "<br />", $strHtmlBody);
					$strTextBody = str_replace("<br />", "\n", $strTextBody);
					$strTextBody = str_replace("&nbsp;", "", $strTextBody);
					$strTextBody = strip_tags($strTextBody);
					$strTextBody = html_entity_decode($strTextBody, ENT_COMPAT, "UTF-8");
	
					$varEmailId = $objSenderEmail->getField("RecipientEmail")->getValue();
					$objEmailElement = $objCms->getElementById($varEmailId);
					if (is_object($objEmailElement)) {
						$varEmailId = $objEmailElement->getElement()->getApiName();
						if (empty($varEmailId)) $varEmailId = $objEmailElement->getId();
					}
	
					//*** Send the email.
					$objMail = new htmlMimeMail5();
					$objMail->setHTMLEncoding(new Base64Encoding());
					$objMail->setTextCharset("utf-8");
					$objMail->setHTMLCharset("utf-8");
					$objMail->setHeadCharset("utf-8");
					$objMail->setFrom($objSenderEmail->getField("SenderEmail")->getHtmlValue());
					$objMail->setSubject($objSenderEmail->getField("Subject")->getHtmlValue());
					$objMail->setText($strTextBody);
					$objMail->setHTML($strHtmlBody);
					if (!$objMail->send(array($this->__validForm->getValidField("formfield_" . strtolower($varEmailId))->getValue()))) {
						echo $objMail->errors;
					}
				}
	
				$strReturn = $this->__formElement->getField("ThanksBody")->getHtmlValue();
			} else {
				$strReturn = $this->__formElement->getField("ThanksBody")->getHtmlValue();
			}
		} else {
			$strReturn = $this->__validForm->toHtml($blnClientSide);
		}

		return $strReturn;
	}
	
	private function renderPage(&$objParent, $objElement) {
		$objReturn = $objParent->addPage($this->generateId($objPage), $objElement->getField("Title")->getHtmlValue());
		
		return $objReturn;
	}
	
	private function generateId($objElement) {
		$strApiName = $objElement->getElement()->getApiName();
		$strPrefix 	= ($objElement->getTemplateName() == "Page") ? "page_" : "formfield_";
		
		return (empty($strApiName)) ? "formfield_" . $objElement->getId() : "formfield_" . strtolower($strApiName);;
	}
	
}

?>