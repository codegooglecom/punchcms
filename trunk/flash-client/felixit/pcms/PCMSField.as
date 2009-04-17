/**
 * PCMSField 1.1 : Punch CMS : The developers CMS - http://www.mypunch.org/pcms/
 * 
 */
 
import com.felixit.pcms.PCMSClient;
import com.felixit.pcms.PCMSElement;

class com.felixit.pcms.PCMSField {
	private var __objField:Object;
	
	public function PCMSField(objField) {
		__objField = objField;
	}
	
	public function get id() {
		return __objField.attributes.id;
	}
	
	public function get apiName() {
		return __objField.attributes.apiName;
	}
	
	public function get typeId() {
		return __objField.attributes.typeId;
	}
	
	public function getValue():String {
		return __objField.value._value;
	}
	
	public function getFileValue():String {
		var objCms = PCMSClient.getInstance();
		
		var arrFiles:Array = __objField.value._value.split("\n");
		var arrFile:Array = arrFiles[0].split(":");
		
		return objCms.getConfig("fileFolder") + arrFile[1];
	}
	
	public function getFileValues():Array {
		var objCms = PCMSClient.getInstance();
		
		var arrReturn:Array = new Array();
		if (__objField.value._value != "") {
			var arrImages:Array = __objField.value._value.split("\n");
			for (var intCount = arrImages.length; intCount > 0; intCount--) {
				if (arrImages[intCount - 1] != "") {
					var arrImage:Array = arrImages[intCount - 1].split(":");
					var strImage:String = objCms.getConfig("fileFolder") + arrImage[1];
					arrReturn.push(strImage);
				}
			}
		}
		
		return arrReturn;
	}
}