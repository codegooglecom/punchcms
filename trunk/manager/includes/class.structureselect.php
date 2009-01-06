<?php

class StructureSelect extends ClassDynamic {
	protected $__type = "";
	protected $__id = "";
	protected $__logicid = "";
	protected $__description = "";

	public function __construct($objSelectXml) {
		global $objLang;
		
		if (is_object($objSelectXml)) {
			$this->__id = $objSelectXml->getAttribute("id");
			$this->__type = $objSelectXml->getAttribute("type");
			$this->__logicid = $objSelectXml->getAttribute("logicId");
			
			foreach ($objSelectXml->childNodes as $metaNode) {
				if ($metaNode->nodeName == "meta" && $metaNode->getAttribute("language") == $objLang->get("abbr")) {
					$this->__description = $metaNode->getAttribute("description");
				}
			}
		}
	}

}

?>