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

	public function buildForm($blnHandle = TRUE, $blnClientSide = TRUE) {
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
			if (get_class($objPage) == "VF_Hidden") continue;

			$objParent = $this->renderPage($this->__validForm, $objPage);

			$objFieldsets = $objPage->getElementsByTemplate(array("Fieldset", "Paragraph"));
			foreach ($objFieldsets as $objFieldset) {
				switch ($objFieldset->getTemplateName()) {
					case "Paragraph":
						$this->renderParagraph($this->__validForm, $objFieldset);
						break;
					case "Fieldset":
						$objVfFieldset = $this->renderFieldset($this->__validForm, $objFieldset);
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

		if ($blnHandle) {
			if ($this->__validForm->isConfirmed()) {
				$strReturn = "Awesome. You've confirmed it.";
			} else if ($this->__validForm->isSubmitted() && $this->__validForm->isValid()) {
				$strReturn = $this->__validForm->confirm();
			} else {
				$strReturn = $this->__validForm->toHtml($blnClientSide);
			}
		}

		return $strReturn;
	}
	
	private function renderPage(&$objParent, $objElement) {
		$objReturn = $objParent->addPage($this->generatePageId($objElement), $objElement->getField("Title")->getHtmlValue());
		
		return $objReturn;
	}
	
	private function generatePageId($objElement) {
		$strApiName = $objElement->getElement()->getApiName();
		return (empty($strApiName)) ? "page_" . $objElement->getId() : "page_" . strtolower($strApiName);
	}
	
}

?>