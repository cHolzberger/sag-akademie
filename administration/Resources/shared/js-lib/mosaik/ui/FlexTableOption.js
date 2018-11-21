/**
 * Created with JetBrains PhpStorm.
 * User: cholzberger
 * Date: 2/25/13
 * Time: 10:11 PM
 * To change this template use File | Settings | File Templates.
 */
dojo.provide("mosaik.ui.FlexTableOption");

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.form.CheckBox");

dojo.declare("mosaik.ui.FlexTableOption", [ dijit._Widget, dijit._Templated], {
    templateString:  dojo.cache("mosaik", "resources/flexTableOption.html"),
    widgetsInTemplate: true,
    stm: null,


    construct: function ( ) {
        this.set("id","undefined");
        this.set("label","");
        this.set("checked",false);
    },

    attributeMap: {
        label: { node: "labelNode", type: "innerHTML"},
        value: { node: "checkboxNode", type: "value"}
    },

    startup: function ( ) {
        this.chgHndl=dojo.connect(this.checkboxNode,"onChange", this, "_optionChanged");
    },

    setLabel: function (name) {
        this.set("label", name);
    },

    setId: function( id) {
        this.id=id;
    },

    setChecked: function (checked) {
        if ( this.chgHndl) {
            dojo.disconect(this.chgHndl);
        }

        var curChecked = this.checkboxNode.get("checked");

        if ( (checked === true || checked==="true") && curChecked !== true ) {
            this.checkboxNode.set("checked",true);
            this.set("checked",true);
        } else if ( (checked===false|| checked==="false") && curChecked !== false ) {
            this.checkboxNode.set("checked",false);
            this.set("checked",false);
        }
        this.chgHndl=dojo.connect(this.checkboxNode,"onChange", this, "_optionChanged");
    },

    getId: function () {return this.id},

    _optionChanged: function () {
        if ( this.stm ) {
            clearTimeout(this.stm);
            this.stm=false;
        }
        var checked = this.checkboxNode.get("checked");
        this.stm = setTimeout(dojo.hitch(this, function () {
            this.optionChanged(this.id, this.value, checked);
        }), 1000);
    },

    optionChanged: function(id, value, checked) {
        this.set("checked",checked);
    }

});
