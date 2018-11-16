/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms please contact us at sales@dhtmlx.com
*/

scheduler._loaded={};
scheduler._load_format = scheduler.date.date_to_str("%Y-%m-%d");
scheduler._load=function(url,from){
	url=url||this._load_url;
	url+=(url.indexOf("?")==-1?"?":"&")+"timeshift="+(new Date()).getTimezoneOffset();
		
	var to;
	from=from||this._date;
	
	if (this._load_mode){
		from = this._load_date(from);
		from = new Date(from.valueOf()-from.getTimezoneOffset()*60000); //correct timezone
	
		if (this._loaded[this._load_format(from)]) return true; //already loaded
		to=this.date.add(from,1,this._load_mode);
		dhtmlxAjax.get(url+"&from="+this._load_format(from)+"&to="+this._load_format(to),function(l){scheduler.on_load(l);});
		this._loaded[this._load_format(from)]=true;
	} else
		dhtmlxAjax.get(url,function(l){scheduler.on_load(l);});
}
scheduler._load_date=function(date){
	return this.date[this._load_mode+"_start"](new Date(date.valueOf()));
}
scheduler.on_load=function(loader){
	this._loading=true;
	if (this._process)
		var evs=this[this._process].parse(loader.xmlDoc.responseText);
	else
		var evs=this._magic_parser(loader);
	
	this._not_render=true;
	for (var i=0; i<evs.length; i++)
		this.addEvent(evs[i]);
	this._not_render=false;
	if (this._render_wait) this.render_view_data();
	
	if (this._after_call) this._after_call();
	this._after_call=null;
	this._loading=false;
}
scheduler.load=function(url,call){
	if (typeof call == "string"){
		this._process=call;
		call = arguments[2];
	}
	
	this._load_url=url;
	this._after_call=call;
	this._load(url,this._date);
};
//possible values - day,week,month,year,all
scheduler.setLoadMode=function(mode){
	if (mode=="all") mode="";
	this._load_mode=mode;
};

//current view by default, or all data if "true" as parameter provided
scheduler.refresh=function(refresh_all){
	alert("not implemented");
	/*
	this._loaded={};
	this._load();
	*/
}
scheduler._magic_parser=function(loader){
	//xml only for now
	var xml=loader.getXMLTopNode("data");
	if (xml.tagName!="data") return [];//not an xml
	
	var evs=[];
	var xml=loader.doXPath("//event");
	
	for (var i=0; i < xml.length; i++) {
		evs[i]=this.xmlNodeToJSON(xml[i])
		
		evs[i].text=evs[i].text||evs[i]._tagvalue;
		evs[i].start_date=this.templates.xml_date(evs[i].start_date);
		evs[i].end_date=this.templates.xml_date(evs[i].end_date);
	}
	return evs;
}
scheduler.xmlNodeToJSON = function(node){
        var t={};
        for (var i=0; i<node.attributes.length; i++)
            t[node.attributes[i].name]=node.attributes[i].value;
        
        for (var i=0; i<node.childNodes.length; i++){
        	var child=node.childNodes[i];
            if (child.nodeType==1)
                t[child.tagName]=child.firstChild?child.firstChild.nodeValue:"";
        }
                 
        if (!t.text) t.text=node.firstChild?node.firstChild.nodeValue:"";
        
        return t;
}