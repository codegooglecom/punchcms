/*
Copyright Scand LLC http://www.scbr.com
To use this component please contact info@scbr.com to obtain license

*/ 
 


 
dhtmlXTreeObject.prototype.enableItemEditor=function(mode){
 this._eItEd=convertStringToBoolean(mode);
 if(!this._eItEdFlag){
 var self=this;
 this._edn_click_IE=true;
 this._edn_dblclick=true;
 this._ie_aFunc=this.aFunc;
 this._ie_dblclickFuncHandler=this.dblclickFuncHandler;

 this.dblclickFuncHandler=function(a,b){
 if(self._edn_dblclick)self._editItem(a,b);};

 this.aFunc=function(a,b){
 self._stopEditItem(a,b);
 if((self.ed_hist_clcik==a)&&(self._edn_click_IE))
 self._editItem(a,b);
 self.ed_hist_clcik=a;
 if(self._ie_aFunc)self._ie_aFunc(a,b);
};

 this.setOnClickHandler=this.__setOnClickHandler;
 this.setOnDblClickHandler=this.__setOnDblClickHandler;
 this._eItEdFlag=true;

}
};

 
dhtmlXTreeObject.prototype.setOnEditHandler=function(func){
 if(typeof(func)=="function")this._onITCFunc=func;else this._onITCFunc=eval(func);
};

 
dhtmlXTreeObject.prototype.setEditStartAction=function(click_IE,dblclick){
 this._edn_click_IE=convertStringToBoolean(click_IE);
 this._edn_dblclick=convertStringToBoolean(dblclick);
};

dhtmlXTreeObject.prototype._stopEdit=function(a){
 if(this._editCell){
 this.dADTempOff=this.dADTempOffEd;
 if(this._editCell.id!=a){
 if((this._onITCFunc)&&(!this._onITCFunc(2,this._editCell.id,this,this._editCell.span.childNodes[0].value)))
 this._editCell.span.innerHTML=this._editCell._oldValue;
 else
 this._editCell.span.innerHTML=this._editCell.span.childNodes[0].value;
 this._editCell.label=this._editCell.span.innerHTML;
 this._editCell.span.className="standartTreeRow";
 temp.span.onclick=function(){};
 if(this._onITCFunc)this._onITCFunc(3,this._editCell.id,this);
 this._editCell=null;
}
}
}

dhtmlXTreeObject.prototype._stopEditItem=function(id,tree){
 this._stopEdit(id);
};

dhtmlXTreeObject.prototype._editItem=function(id,tree){
 if(this._eItEd){
 this._stopEdit();
 this.dADTempOffEd=this.dADTempOff;
 this.dADTempOff=false;
 if((this._onITCFunc)&&(!this._onITCFunc(0,id,this)))return;
 temp=this._globalIdStorageFind(id);
 this._editCell=temp;
 temp._oldValue=temp.span.innerHTML;
 temp.span.innerHTML="<input type='text' class='intreeeditRow' value='"+temp.span.innerHTML+"'/>";
 temp.span.childNodes[0].focus();
 temp.span.onclick=function(e){(e||event).cancelBubble=true;return false;};
 temp.span.className="standartTreeRow";
 var self=this;
 temp.span.childNodes[0].onkeypress=function(e){
 if(!e)e=window.event;
 if(e.keyCode==13)
 self._stopEdit(-1);
}
 if(this._onITCFunc)this._onITCFunc(1,id,this);
}
 else
 if(this._ie_dblclickFuncHandler)this._ie_dblclickFuncHandler(id,tree);
};



dhtmlXTreeObject.prototype.__setOnDblClickHandler=function(func){
 if(typeof(func)=="function")this._ie_dblclickFuncHandler=func;else this._ie_dblclickFuncHandler=eval(func);
};

dhtmlXTreeObject.prototype.__setOnClickHandler=function(func){
 if(typeof(func)=="function")this._ie_aFunc=func;else this._ie_aFunc=eval(func);
};


