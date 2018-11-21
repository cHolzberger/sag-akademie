/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */


dojo.provide("module.shell.NeuMenu");

module.shell.NeuMenu = [
	{
		label: "Buchung",
		signal: "NeuMenu/" + this.label,
		onclick: function () { 		
			var options = {create: true};

			if ( typeof ( currentModule._currentModuleOptions) !== "undefined"  && 
			typeof(currentModule._currentModuleOptions.personId) !== "undefined" ) {
				
				options.personId = currentModule._currentModuleOptions.personId;
			}
			
			currentModule.loadShellModule('buchungBearbeiten', options);
		}
	},{
		label: "--"
	}, 
	{
		label: "Person",
		signal: "NeuMenu/" + this.label,
		onclick: function () { 
			var options = {create: true};

			if ( typeof ( currentModule._currentModuleOptions) !== "undefined"  && 
			typeof(currentModule._currentModuleOptions.kontaktId) !== "undefined" ) {
				
				options.kontaktId = currentModule._currentModuleOptions.kontaktId;
			}
			
			
			currentModule.loadShellModule('personBearbeiten', options);
		}
	},{
		label: "Firma",
		signal: "NeuMenu/" + this.label,

		onclick: function () { 
			currentModule.loadShellModule('kontaktBearbeiten', {create: true});
		}
	},{
		label: "--"
	}, {
		label: "Seminar",
		signal: "NeuMenu/" + this.label,

		onclick: function () { 
			currentModule.loadShellModule('seminarBearbeiten', {create: true});
		}
	},{
		label: "Inhouse-Seminar",
		signal: "NeuMenu/" + this.label,

		onclick: function () { 
			currentModule.loadShellModule('inhouseSeminarBearbeiten', {create: true});
		}
	},{
		label: "--"
	},{
		label: "Termin",
		signal: "NeuMenu/" + this.label,

		onclick: function () { 
			var options = {create: true};

			if ( typeof ( currentModule._currentModuleOptions) !== "undefined"  && 
			typeof(currentModule._currentModuleOptions.seminarId) !== "undefined" ) {
				options.seminarArtId = currentModule._currentModuleOptions.seminarId;
			}
			currentModule.loadShellModule('terminBearbeiten', options);
		}
	},{
		label: "Inhouse-Termin",
		signal: "NeuMenu/" + this.label,

		onclick: function () { 
			currentModule.loadShellModule('inhouseTerminBearbeiten', {create: true});
		}
	},{
		label: "--"
	},{


		label: "Aufgabe",
		signal: "NeuMenu/" + this.label,
		onclick: function () { 
			currentModule.loadShellModule('todoBearbeiten', {create: true});
		}
	},{
		label: "Referent",
		signal: "NeuMenu/" + this.label,
		onclick: function () { 
			currentModule.loadShellModule('referentBearbeiten', {create: true});
		}
	}
];

dojo.forEach ( module.shell.NeuMenu, function (item) {
	item.signal = "NeuMenu/" + item.label;
});