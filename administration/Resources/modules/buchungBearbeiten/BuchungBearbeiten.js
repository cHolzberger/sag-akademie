/** require dependencies **/
dojo.require("dojo.number");
dojo.require("mosaik.core.Module");

dojo.require('dijit.form.Select');
dojo.require('dijit.form.DropDownButton');
dojo.require('dijit.form.TextBox');
dojo.require('dijit.form.Button');
dojo.require('dijit.form.Form');
dojo.require('dijit.form.Textarea');
dojo.require('dijit.form.DateTextBox');
dojo.require('dijit.form.CurrencyTextBox');
dojo.require("dijit.form.CheckBox");
dojo.require("dojox.widget.Standby");
dojo.require('mosaik.ui.DatasetNavigator');
dojo.require('mosaik.ui.WeitereInformationen');

dojo.require("module.dialogs.PersonChooser");
dojo.require("module.dialogs.TerminChooser");
dojo.require("module.dialogs.HotelChooser");

dojo.provide("module.buchungBearbeiten.BuchungBearbeiten");

var d = dojo;
var _$ = dojo.byId;

dojo.declare("module.buchungBearbeiten.BuchungBearbeiten", [mosaik.core.Module], {
    moduleName:"BuchungBearbeiten",
    formPrefix:"Buchung",
    _currentBuchung:null,
    _hotelBuchung:null,
    _currentHotel:null,
    container:null, // container node -> border container
    createDialog:null,
    hotelBuchungService:null,

    _nextButtonHandle:null,
    _prevButtonHandle:null,
    _createButtonHandle:null,
    _alternativTermine:null,

    constructor:function () {
        this.dropdowns = {};
    },
    run:function (options) {
        this.widgets.dnav.update(options, "buchungId", "buchungBearbeiten");

        this.createDialog = dijit.byId("chooserFrame");

        this.service = this.sandbox.getRpcService("database/buchung");
        this.hotelBuchungService = this.sandbox.getRpcService("database/hotelBuchung");
        this.terminService = this.sandbox.getRpcService("database/termin");
        this.weitereInformationen = dijit.byId("weitereInformationen");
        this.widgets.editHotelBuchung.domNode.style.display = "none";
        dojo.style(this.widgets.createHotelBuchung.domNode, "display", "none");
        dojo.style(this.widgets.stornoHotelBuchung.domNode, "display", "none");
        this.container = dijit.byId('borderContainer');

        this._options = options;
        console.debug("TerminBearbeiten::run >>>");
        console.dir(this._options);
        console.debug("<<< TerminBearbeiten::run");

        if (typeof (options.create ) !== "undefined") {
            this.onCreate();
        } else {
            this.fetchData();
        }

        this.linkButtons();
        dojo.connect(this, "onValuesSet", this, "onValuesUpdate");

        // relayout the container
        this.container.layout();
        this.flexTable.hide();
    },
    onValuesUpdate:function () {
        this.weitereInformationen.setInformation(this._currentData);
        this.reloadHotelBuchung();
    },
    onCreate:function () {
        // Summary:
        // handle creation of new data entry


        // hite nav
        this.widgets.dnav.domNode.style.display = "none";

        this.nextButton = dijit.byId("nextButton");
        this.prevButton = dijit.byId("prevButton");
        this.createButton = dijit.byId("createButton");

        this.personChooser = dijit.byId("personChooser");
        this.terminChooser = dijit.byId("terminChooser");
        this.hotelChooser = dijit.byId("hotelChooser");
        this.createHinweisText = dijit.byId("createHinweisText");

        this.chooserFrame = dijit.byId("chooserFrame");
        this.chooserFrame.show();
        this.chooserStack = dijit.byId("chooserStack");

        dojo.connect(this.personChooser, "onResultClick", this, "personSelected");

        if (typeof(this._options.personId ) === "undefined") {
            this.createSelectTeilnehmer();
        } else {
            this.createSelectTermin();
        }
    },
    createSelectTeilnehmer:function () {
        // Summary:
        // let the user choose an atendee

        // relink next button
        (this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;

        this.chooserFrame.set("title", "Teilnehmer ausw&auml;hlen");

        this._nextButtonHandle = dojo.connect(this.nextButton, "onClick", this, "createSelectTermin");
        // refresh view
        this.chooserStack.selectChild(dijit.byId("teilnehmerPane"));
        // hide prev and create button
        this.createButton.domNode.style.display = "none";
        this.prevButton.domNode.style.display = "none";
        this.nextButton.domNode.style.display = "block";
    },
    createSelectTermin:function () {
        // Summary:
        // let the user choose a seminar
        (this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
        (this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;

        this.createButton.domNode.style.display = "none";
        this.prevButton.domNode.style.display = "block";
        this.nextButton.domNode.style.display = "block";

        this.chooserFrame.set("title", "Termin ausw&auml;hlen");
        this.chooserStack.selectChild(dijit.byId("terminPane"));
        console.log("next");

        this._nextButtonHandle = dojo.connect(this.nextButton, "onClick", this, "createHinweisForm");
        this._prevButtonHandle = dojo.connect(this.prevButton, "onClick", this, "createSelectTeilnehmer");
    },
    createHinweisForm:function () {
        (this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
        (this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;

        this.createButton.domNode.style.display = "none";
        this.prevButton.domNode.style.display = "block";
        this.nextButton.domNode.style.display = "block";

        this.widgets.newBuchungsDatum.set("value", new Date());

        this.chooserFrame.set("title", "Hinweis eingeben");
        this.chooserStack.selectChild(dijit.byId("hinweisPane"));
        console.log("next");

        this._nextButtonHandle = dojo.connect(this.nextButton, "onClick", this, "createAskHotel");
        this._prevButtonHandle = dojo.connect(this.prevButton, "onClick", this, "createSelectTermin");
    },
    createAskHotel:function () {
        // Summary:
        // display notice that hotel selection is optional

        (this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
        (this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
        (this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;

        this.nextButton.domNode.style.display = "block";
        this.prevButton.domNode.style.display = "block";
        this.createButton.domNode.style.display = "block";

        this.chooserFrame.set("title", "Hotel buchen?");
        this.chooserStack.selectChild(dijit.byId("chooseHotelPane"));
        console.log("next");

        this._nextButtonHandle = dojo.connect(this.nextButton, "onClick", this, "createHotelBuchung");
        this._prevButtonHandle = dojo.connect(this.prevButton, "onClick", this, "createHinweisForm");
        this._createButtonHandle = dojo.connect(this.createButton, "onClick", this, function () {
            this.createDone(false)
        });
    },
    createHotelBuchung:function () {
        // Summary:
        // let the user select a hotel
        (this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
        (this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
        (this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;

        this.chooserFrame.set("title", "Hotel buchen");
        this.chooserStack.selectChild(dijit.byId("hotelPane"));

        this.nextButton.domNode.style.display = "block";
        this.createButton.domNode.style.display = "none";
        this.prevButton.domNode.style.display = "block";

        this.hotelChooser.options = {
            "seminarId":this.terminChooser.selectedItem.id,
            "standortId":this.terminChooser.selectedItem.standort_id,
            "date":this.terminChooser.selectedItem.datum_begin
        };

        this._nextButtonHandle = dojo.connect(this.nextButton, "onClick", this, "createHotelBuchungDetail");
        this._prevButtonHandle = dojo.connect(this.prevButton, "onClick", this, "createAskHotel");

    },
    createHotelBuchungDetail:function () {
        // Summary:
        // let the user select a hotel
        (this._nextButtonHandle != null) ? dojo.disconnect(this._nextButtonHandle) : true;
        (this._prevButtonHandle != null) ? dojo.disconnect(this._prevButtonHandle) : true;
        (this._createButtonHandle != null) ? dojo.disconnect(this._createButtonHandle) : true;

        this.chooserFrame.set("title", "Hotelbuchung Details");
        this.chooserStack.selectChild(dijit.byId("hotelDetailPane"));

        this.nextButton.domNode.style.display = "none";
        this.createButton.domNode.style.display = "block";
        this.prevButton.domNode.style.display = "block";

        this._prevButtonHandle = dojo.connect(this.prevButton, "onClick", this, "createHotelBuchung");
        this._createButtonHandle = dojo.connect(this.createButton, "onClick", this, function () {
            this.createDone(true)
        });
    },
    createDone:function (hotelBuchen) {
        // Summary:
        // cretate process done, send the collected data

        this._options.seminarId = this.terminChooser.selectedItem.id;
        sandbox.showLoadingScreen("Erstelle Buchung...");
        this.createOptions = {
            seminarId:this._options.seminarId,
            personId:this._options.personId,
            hinweis:this.createHinweisText.get("value"),
            hotelId:0,
            uebernachtungen:0,
            anreisedatum:0
        };

        if (hotelBuchen) {
            this.createOptions.hotelId = this.hotelChooser.selectedItem.id;
            this.createOptions.uebernachtungen = dijit.byId("hotelUebernachtungen").get("value");
            this.createOptions.anreisedatum = dijit.byId("hotelAnreiseDatum").get("value");
        }
        this.createOptions.buchungsDatum = mysqlDateFromDate(this.widgets.newBuchungsDatum.get("value"));

        this.service.create(this.createOptions.personId, this.createOptions.seminarId, this.createOptions.buchungsDatum, this.createOptions.hinweis, this.createOptions.hotelId, this.createOptions.anreisedatum, this.createOptions.uebernachtungen).addCallback(dojo.hitch(this, function (data) {
            this._ignoreUpdate = true;
            this._currentBuchung = data;
            this.setValue(data);

            this.initStaticFields();

            this.createDialog.hide();
            sandbox.hideLoadingScreen();
        })).addErrback(dojo.hitch(this, function (data) {
            console.log("==!!> KontaktBearbeiten::createDone Error: " + data);
        }));
    },
    personSelected:function () {
        console.log("person Selected");
        dijit.byId("nextButton").domNode.style.display = "block";
        this._options.personId = this.personChooser.selectedItem.id;
    },
    initStaticFields:function () {
        var angelegtVon = dojo.byId("angelegt_von");
        var personName = dojo.byId("personName");
        var firmaName = dojo.byId("firmaName");
        var seminarKursnr = dojo.byId("seminarKursnr");
        var seminarArtId = dojo.byId("seminarArtId");
        var terminVon = dojo.byId("terminVon");
        var terminBis = dojo.byId("terminBis");

        angelegtVon.innerHTML = this._currentBuchung.angelegt_von;
        personName.innerHTML = this._currentBuchung.name + ", " + this._currentBuchung.vorname;
        firmaName.innerHTML = this._currentBuchung.firma;

        seminarKursnr.innerHTML = this._currentBuchung.kursnr;
        seminarArtId.innerHTML = this._currentBuchung.seminar_art_id;

        terminVon.innerHTML = mysqlDateToLocal(this._currentBuchung.datum_begin);
        terminBis.innerHTML = mysqlDateToLocal(this._currentBuchung.datum_ende);
    },
    linkButtons:function () {
        var personName = dojo.byId("personName");
        dojo.connect(personName, "onclick", this, function () {
            sandbox.navigateToUrl("mailto:" + this._currentBuchung.email);
        })

        dojo.connect(dijit.byId("sendInfoButton"), "onClick", this, function () {
            if (confirm("Wirklich eine Info-Mail an den Ansprechpartner senden?") !== true) {
                return;
            }

            sandbox.showLoadingScreen();
            this.service.sendInfo(this._currentData.id).addCallback(this,function () {
                sandbox.hideLoadingScreen();
            }).addErrback(function () {
                    alert("Fehler beim Senden der InfoMail");
                    sandbox.hideLoadingScreen();
                });
        });
        var saveBtn = dijit.byId("speichernBtn");
        dojo.connect(saveBtn, "onClick", this, "onSave");
        dojo.connect(this.widgets.druckenBtn, "onClick", this, "onPrint");

    },
    onSave:function () {
        console.log("Save");
        sandbox.showLoadingScreen();

        this.service.save(this._currentData.id, this._changedData).addCallback(dojo.hitch(this, function (data) {
            console.log("SaveDone");
            //	console.dir(data);
            this.setValue(data);
            sandbox.hideLoadingScreen();
        })).addErrback(function (data) {
                console.log("Seminar-Save Error: " + data);
            });
    },
    fetchData:function () {
        this.service.find(this._options.buchungId).addCallback(this,function (data) {
            this._ignoreUpdate = true;
            this._currentBuchung = data;

            //console.dir(data);
            this.setValue(data);
            this.fetchAlternatives();

            this.initStaticFields();
            this.weitereInformationen.setInformation(data);

            if (data.alte_buchung != 0) {
                dojo.style(this.nodes.oldBuchung, "display", "block");
                dojo.style(this.nodes.umbuchenForm, "display", "block");
            }

            if (data.umgebucht_id != 0) {
                dojo.style(this.nodes.umbuchenForm, "display", "none");
                dojo.style(this.nodes.newBuchung, "display", "block");
            }

            if (data.storno_datum != "0000-00-00") {
                this.nodes.stornoDatum.innerHTML = mysqlDateToLocal(data.storno_datum);
                dojo.style(this.widgets.stornoBtn.domNode, "display", "none");
            } else {
                dojo.style(this.nodes.stornoDatum, "display", "none");
                dojo.style(this.widgets.stornoBtn.domNode, "display", "block");
            }
            this.reloadHotelBuchung();

        }).addErrback(this, function (data) {
                console.log("==!!> KontaktBearbeiten::run Error: " + data);
            });
    },
    fetchAlternatives:function () {
        console.log("===========> fetchAlternatives")
        console.log("Umgebucht: " + this._currentData.umbuchungs_datum)
        if (this._currentData.umbuchungs_datum == "0000-00-00") {
            dojo.style(this.nodes.umbuchenForm, "display", "block");

            this.terminService.findAlternatives(this._currentData.seminar_id).addCallback(this,function (data) {
                console.log("====================> Alternative Termine recived");
                var _select = this.widgets.alternativTermine;
                this._alternativTermine = data;

                console.log("Generating labels");
                _select.addOption({
                    label:"Umbuchen auf...",
                    value:-1
                });
                _select.addOption(null);
                dojo.forEach(this._alternativTermine, function (item) {
                    console.log(".");
                    item.label = "Am " + mysqlDateToLocal(item.datum_begin) + " in " + item.standort_name;
                    item.value = item.id;
                    _select.addOption(item);
                });

                _select.set("value", "-1");

                console.log("creating in memory store..");

            }).addErrback(this, function (err) {
                    alert("Fehler beim Abrufen von alternativ Terminen: " + err);
                });
        } else {
            dojo.style(this.nodes.umbuchenForm, "display", "none");
        }
    },
    reloadHotelBuchung:function () {
        this.hotelBuchungService.find(this._currentData.id).addCallback(this,function (data) {
            console.log("=========> HotelBuchung: ");
            //	console.dir(data);

            var _n = this.nodes;
            // daten ins formular eintragen

            if (data == null || typeof (data.anreise_datum ) === "undefined") {
                dojo.style(this.nodes.HotelBuchungContainer, "display", "none");
                dojo.style(this.widgets.editHotelBuchung.domNode, "display", "none");
                dojo.style(this.widgets.createHotelBuchung.domNode, "display", "block");
                dojo.style(this.widgets.stornoHotelBuchung.domNode, "display", "none");

                this._currentHotel = null;
            } else {
                if (typeof(data.Hotel) !== "undefined") {
                    this._currentHotel = data.Hotel;

                    _n['HotelBuchung:hotelName'].innerHTML = data.Hotel.name;
                }
                _n['HotelBuchung:uebernachtungen'].innerHTML = data.uebernachtungen;
                _n['HotelBuchung:anreise_datum'].innerHTML = data.anreise_datum;
                _n['HotelBuchung:storno_datum'].innerHTML = mysqlDateToLocal(data.storno_datum);

                if (data.storno_datum != "0000-00-00" && data.storno_datum != "1900-01-01") {
                    dojo.style('hotelBuchungStorniert', "display", "block");
                    dojo.style(this.widgets.stornoHotelBuchung.domNode, "display", "none");

                } else {
                    dojo.style('hotelBuchungStorniert', "display", "none");
                    dojo.style(this.widgets.stornoHotelBuchung.domNode, "display", "block");
                }

                dojo.style(this.nodes.HotelBuchungContainer, "display", "block");
                dojo.style(this.widgets.editHotelBuchung.domNode, "display", "block");
                dojo.style(this.widgets.createHotelBuchung.domNode, "display", "none");
            }
            this._hotelBuchung = data;
        }).addErrback(this, function (data) {
                console.log("==!!> KontaktBearbeiten::reloadHotelBuchung Error: " + data);
                alert(data);
            });
    },
    openSeminar:function () {
        var id = this._currentBuchung.seminar_art_id;
        sandbox.loadShellModule("seminarBearbeiten", {
            seminarId:id
        });
    },
    openTermin:function () {
        var id = this._currentBuchung.seminar_id;
        sandbox.loadShellModule("terminBearbeiten", {
            terminId:id
        });

    },
    openKontakt:function () {
        var id = this._currentBuchung.kontakt_id;
        sandbox.loadShellModule("kontaktBearbeiten", {
            kontaktId:id
        });
    },
    openPerson:function () {
        var id = this._currentBuchung.person_id;
        sandbox.loadShellModule("personBearbeiten", {
            personId:id
        });
    },
    // Hotel Buchung

    _chooserHandle:null,

    onEditHotelBuchung:function () {
        this._createHotelBuchung = false;

        this.widgets.hotelBuchungFrame.show();

        dojo.style(this.nodes.HBEditForm, "display", "block");
        dojo.style(currentModule.widgets.hotelBuchungChooser.domNode, 'display', 'none');

        var _b = this._hotelBuchung;
        var _w = this.widgets;
        var _n = this.nodes;
        // daten ins formular eintragen
        _n['HBEdit:hotelName'].innerHTML = _b.Hotel.name;
        _w['HBEdit:uebernachtungen'].set("value", _b.uebernachtungen);
        _w['HBEdit:anreise_datum'].set("value", _b.anreise_datum);

        if (_b.storno_datum != "0000-00-00" && _b.storno_datum != "1900-00-00") {
            _w['HBEdit:storno_datum'].set("value", mysqlDateToDate(_b.storno_datum));
        }

        if (this._chooserHandle != null) {
            dojo.disconnect(this._chooserHandle);
            this._chooserHandle = null;
        }

        this._chooserHandle = dojo.connect(this.widgets.hotelBuchungChooser, "onResultClick", this, "onHotelBuchungHotelSelected");

        this.widgets.hotelBuchungChooser.options = {
            "seminarId":this._currentData.id,
            "standortId":this._currentData.standort_id,
            "date":this._currentData.datum_begin
        };
    },
    onCreateHotelBuchung:function () {
        this._createHotelBuchung = true;
        this._hotelBuchung = {};
        dojo.style(currentModule.widgets.hotelBuchungChooser.domNode, 'display', 'block');

        this.widgets.hotelBuchungFrame.show();

        dojo.style(this.nodes.HBEditForm, "display", "none");
        console.log("===========> HotelBuchung:");
        console.dir(this._currentData);

        if (this._chooserHandle != null) {
            dojo.disconnect(this._chooserHandle);
            this._chooserHandle = null;
        }

        this._chooserHandle = dojo.connect(this.widgets.hotelBuchungChooser, "onResultClick", this, "onHotelBuchungHotelSelected");

        this.widgets.hotelBuchungChooser.options = {
            "seminarId":this._currentData.id,
            "standortId":this._currentData.standort_id,
            "date":this._currentData.datum_begin
        };
    },
    onHotelBuchungHotelSelected:function (e, hotel) {
        this._currentHotel = hotel;
        dojo.style(this.nodes.HBEditForm, "display", "block");
        console.dir(hotel);
        this.nodes['HBEdit:hotelName'].innerHTML = hotel.name;


        dojo.style(currentModule.widgets.hotelBuchungChooser.domNode, 'display', 'none');
    },
    saveHotelBuchung:function () {
        var _h = this._currentHotel;
        if (_h == null) {
            _h = this._widgets.hotelChooser.selectedItem;
        }
        var _b = this._hotelBuchung;
        var _n = this.nodes;

        _b.uebernachtungen = this.widgets['HBEdit:uebernachtungen'].get("value");
        _b.anreise_datum = this.widgets['HBEdit:anreise_datum'].get("value");

        var stornodate = this.widgets['HBEdit:storno_datum'].get("value");
        if (stornodate != null) {
            _b.storno_datum = mysqlDateFromDate(stornodate);
        } else {
            _b.storno_datum = "0000-00-00";
        }

        _n['HotelBuchung:hotelName'].innerHTML = _n['HBEdit:hotelName'].innerHTML;
        _n['HotelBuchung:uebernachtungen'].innerHTML = _b.uebernachtungen;
        _n['HotelBuchung:anreise_datum'].innerHTML = _b.anreise_datum;

        sandbox.showLoadingScreen("Speichere Daten...");
        var _d = null
        if (this._createHotelBuchung != true) {
            _d = this.hotelBuchungService.save(this._currentData.id, {
                hotel_id:_h.id,
                uebernachtungen:_b.uebernachtungen,
                anreise_datum:_b.anreise_datum,
                storno_datum:_b.storno_datum
            });
        } else {
            _d = this.hotelBuchungService.create(this._currentData.id, {
                hotel_id:_h.id,
                uebernachtungen:_b.uebernachtungen,
                anreise_datum:_b.anreise_datum,
                storno_datum:_b.storno_datum
            });
        }

        _d.addCallback(this,function () {
            this.reloadHotelBuchung();
            sandbox.hideLoadingScreen();
        }).addErrback(this, function (err) {
                alert("Fehler beim Speichern: " + err);
            });

        this.widgets.hotelBuchungFrame.hide();
    },
    onStronoHotelBuchung:function () {
        sandbox.showLoadingScreen("Speichere Daten...");

        this.hotelBuchungService.storno(this._hotelBuchung.id).addCallback(this,function () {
            this.reloadHotelBuchung();
            sandbox.hideLoadingScreen();
        }).addErrback(this, function (err) {
                alert("Fehler beim Speichern: " + err);
            });
    },
    hotelBuchungAendern:function () {
        this.widgets.hotelBuchungChooser.options = {
            "seminarId":this._currentData.id,
            "standortId":this._currentData.standort_id,
            "date":this._currentData.datum_begin
        };

        dojo.style(this.widgets.hotelBuchungChooser.domNode, 'display', 'block');
        dojo.style(this.nodes.HBEditForm, "display", "none");
    },
    _rebookTerminId:null,
    onUmbuchen:function () {
        console.dir(this._alternativTermine);

        var neuerTermin = this.widgets.alternativTermine.get("value");
        if (neuerTermin && neuerTermin != "-1") {
            var _select = this.widgets.alternativTermine;

            this.widgets.umbuchenNotizFrame.show();

            var item = _select.getOptions(neuerTermin);
            console.log("========> Neuer Termin:")
            console.dir(item);
            this._rebookTerminId = item.id;
            this.nodes.umbuchenKursNr.innerHTML = item.kursnr;
            this.nodes.umbuchenStandort.innerHTML = item.standort_name;
            this.nodes.umbuchenDatum.innerHTML = mysqlDateToLocal(item.datum_begin);

        }
    },
    doRebook:function () {
        sandbox.showLoadingScreen("Buchung wird umgebucht...");

        this.service.rebook(this._currentData.id, this._rebookTerminId, this.widgets.umbuchenNotiz.get("value")).addCallback(this,function (data) {
            sandbox.hideLoadingScreen();
            sandbox.loadShellModule("buchungBearbeiten", {
                buchungId:data
            });
        }).addErrback(function (data) {
                alert("Fehler beim Umbuchen: " + data);
            });
    },
    gotoOldBuchung:function () {
        sandbox.loadShellModule("buchungBearbeiten", {
            buchungId:this._currentData.alte_buchung
        });
    },
    gotoNewBuchung:function () {
        sandbox.loadShellModule("buchungBearbeiten", {
            buchungId:this._currentData.umgebucht_id
        });
    },
    personUmbuchen:function () {
        this.widgets.neuePersonChooser.kontaktId = this._currentData.kontakt_id;
        this.widgets.neuePersonForm.show();
    },
    saveNeuePerson:function () {
        this.updateValue("person_id", this.widgets.neuePersonChooser.selectedItem.id);

        this._currentBuchung.email = this.widgets.neuePersonChooser.selectedItem.email;

        this.nodes.personName.innerHTML = this.widgets.neuePersonChooser.selectedItem.name + ", " + this.widgets.neuePersonChooser.selectedItem.vorname
        this.widgets.neuePersonForm.hide();
    },
    doStorno:function () {
        sandbox.showLoadingScreen("Buchung wird storniert...");

        this.service.storno(this._currentData.id).addCallback(this,function (data) {
            sandbox.hideLoadingScreen();
            sandbox.loadShellModule("buchungBearbeiten", {
                buchungId:data
            });
        }).addErrback(function (data) {
                alert("Fehler beim Stornieren: " + data);
            });
    },
    onPrint:function () {
        var token = sandbox.getUserinfo().auth_token;

        var pdfurl = this.sandbox.getServiceUrl("print/buchung") + this._currentBuchung.id + "?token=" + token;

        app.openPdf(pdfurl);
    }
});
