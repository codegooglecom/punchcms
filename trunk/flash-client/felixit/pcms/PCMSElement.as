/**
 * PCMSElement 1.2 : Punch CMS : The developers CMS - http://www.mypunch.org/pcms/
 * 
 */
 
import com.felixit.pcms.PCMSClient;
import com.felixit.pcms.PCMSField;

class com.felixit.pcms.PCMSElement {
	private var __parent:PCMSElement;
	private var __objElement:Object;
	
	public function PCMSElement(objElement, objParent) {
		__parent = objParent;
		__objElement = objElement;
	}
	
	public function get id() {
		return __objElement.attributes.id;
	}
	
	public function get apiName() {
		return __objElement.attributes.apiName;
	}
	
	public function get templateApiName() {
		return __objElement.attributes.templateApiName;
	}
	
	public function get address() {
		return __objElement.attributes.alias;
	}
	
	public function getParent():PCMSElement {
		return __parent;
	}
	
	public function getElements():Array {
		var objReturn:Array = new Array();
		
		for (var intElement = __objElement.element.length; intElement > 0; intElement--) {
			var objElement:PCMSElement = new PCMSElement(__objElement.element[intElement - 1], this);
			objReturn.push(objElement);
		}
		
		return objReturn;
	}
	
	public function getField(strApiName:String):PCMSField {
		var objReturn:PCMSField = null;
		
		for (var intField = __objElement.field.length; intField > 0; intField--) {
			if (__objElement.field[intField - 1].attributes.apiName == strApiName) {
				objReturn = new PCMSField(__objElement.field[intField - 1]);
				break;
			}
		}
		
		return objReturn;
	}
	
	public function getFields():Array {
		var objReturn:Array = new Array();
		
		for (var intField = __objElement.field.length; intField > 0; intField--) {
			var objField:PCMSField = new PCMSField(__objElement.field[intField - 1]);
			objReturn.push(objField);
		}
		
		return objReturn;
	}
}