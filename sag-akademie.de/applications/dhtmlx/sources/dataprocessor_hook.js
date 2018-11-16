/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms please contact us at sales@dhtmlx.com
*/

scheduler._dp_init=function(dp){
	dp._methods=["setEventTextStyle","","changeEventId","deleteEvent"];
	
	this.attachEvent("onEventAdded",function(id){
		if (!this._loading)
			dp.setUpdated(id,true,"inserted");
	});
	this.attachEvent("onBeforeEventDelete",function(id){
        var z=dp.getState(id);
		if (z=="inserted") {  dp.setUpdated(id,false);		return true; }
		if (z=="deleted")  return false;
    	if (z=="true_deleted")  return true;
    	
		dp.setUpdated(id,true,"deleted");
      	return false;
	});
	this.attachEvent("onEventChanged",function(id){
		if (!this._loading)
			dp.setUpdated(id,true,"updated");
	});
	
	dp._getRowData=function(id,pref){
		pref=pref||"";
		var ev=this.obj.getEvent(id);
		
		var str=[];
		for (var a in ev){
			if (a.indexOf("_")==0) continue;
			if (a.indexOf("_date")!=-1)
				str.push(a+"="+this.obj.templates.xml_format(ev[a]));
			else
				str.push(a+"="+this.escape(ev[a]));
		}
		
		return pref+str.join("&"+pref);
	}
	dp._clearUpdateFlag=function(){}
}


scheduler.setUserData=function(id,name,value){
	this.getEvent(id)[name]=value;
}
scheduler.getUserData=function(id,name){
	return this.getEvent(id)[name];
}
scheduler.setEventTextStyle=function(id,style){
	this.for_rendered(id,function(r){
		r.style.cssText+=";"+style;
	})
	this.getEvent(id)["_text_style"]=style;
}