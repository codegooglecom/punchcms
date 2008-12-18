/*
Copyright Scand LLC http://www.scbr.com
To use this component please contact info@scbr.com to obtain license

*/ 
 

 
 dhtmlXTreeObject.prototype.setOnDragIn=function(func){
 if(typeof(func)=="function")this._onDrInFunc=func;else this._onDrInFunc=eval(func);
};

 
dhtmlXTreeObject.prototype._createDragNode=function(htmlObject,e){
 if(!this.dADTempOff)return null;

 var obj=htmlObject.parentObject;
 if(!obj.i_sel)
 this._selectItem(obj,e);

 this._checkMSelectionLogic();
 dhtmlObject=htmlObject.parentObject;
 var dragSpan=document.createElement('div');
 dragSpan.innerHTML="<span><img width='14px' height='14px' src='"+this.imPath+"red.gif'></span>"+dhtmlObject.label;
 dragSpan.style.position="absolute";
 dragSpan.className="dragSpanDiv";
 this._dragged=(new Array()).concat(this._selected);
 this._clearMove();
 return dragSpan;
}

 
 dhtmlXTreeObject.prototype._extSetMove=function(htmlObject,x,y){
 if(this.dragger.dragNode)
 this.dragger.dragNode.childNodes[0].childNodes[0].src=this.imPath+"green.gif";
 this._setMoveA(htmlObject,x,y);
};
 dhtmlXTreeObject.prototype._extClearMove=function(htmlObject,x,y){
 if(this.dragger.dragNode)
 this.dragger.dragNode.childNodes[0].childNodes[0].src=this.imPath+"red.gif";
 this._clearMoveA();
};

 dhtmlXTreeObject.prototype._clearMoveA=dhtmlXTreeObject.prototype._clearMove;
 dhtmlXTreeObject.prototype._clearMove=dhtmlXTreeObject.prototype._extClearMove;

 dhtmlXTreeObject.prototype._setMoveA=dhtmlXTreeObject.prototype._setMove;
 dhtmlXTreeObject.prototype._setMove=dhtmlXTreeObject.prototype._extSetMove;



 
 dhtmlXTreeObject.prototype.disableDropCheck=function(mode){
 if(convertStringToBoolean(mode))
{
 if(!this._old_checkPNodes){
 this._old_checkPNodes=this._checkPNodes;
 this._checkPNodes=function(){return 0;};
}
}
 else{
 if(this._old_checkPNodes){
 this._checkPNodes=this._old_checkPNodes;
 this._old_checkPNodes=null;
}
}
}


