/** global helpers **/
console.log("mosaik.core.helpers");
dojo.provide("mosaik.core.helpers");

function isArray(obj) {
    return dojo.isArray(obj);
}

function isFunction(obj) {
    return dojo.isFunction(obj);
}

function isObject(obj) {
    return dojo.isObject(obj);
}

function isString(obj) {
    return dojo.isString(obj);
}


function dump(data, label) {
    console.dir({label: label, data: data});
}

function _dump(arr, level) {
    var dumped_text = "";
    if (!level) level = 0;
    else if (level > 10) return "... max depth reached ...";

//The padding given at the beginning of the line.
    var level_padding = "";
    for (var j = 0; j < level + 1; j++) level_padding += "..";

    if (typeof(arr) == 'object') { //Array/Hashes/Objects
        for (var item in arr) {
            var value = arr[item];

            if (typeof(value) == 'object') { //If it is an array,
                dumped_text += level_padding + "[" + item + "] ... " + typeof ( value ) + "\n";
                dumped_text += _dump(value, level + 1);
            } else {
                dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
            }
        }
    } else { //Stings/Chars/Numbers etc.
        dumped_text = "=== " + arr + " ===(" + typeof(arr) + ")";
    }
    return dumped_text;
}

function ucfirst(str) {
    str += '';
    var f = str.charAt(0).toUpperCase();
    return f + str.substr(1);
}

function _parseFlexData(signal) {
    if (signal.format == "array") {
        signal.data = JSON.parse(signal.data.replace(/#\//g, "\\"));

        for (var i = 0; i < signal.data.length; i++) {
            console.log(i + "" + typeof (""));
            if (typeof(signal.data[i] ) !== "object") {
                signal.data[i] = JSON.parse(signal.data[i]);
            }
        }
    }
    return signal;
}

function _flexPublish(signal) {
    console.log("PUBLISH FROM FLEX:");

    var error = false;
    try {
        //var signal = JSON.parse(signal.replace(/#\//g, "\\"));
        signal = _parseFlexData(signal);
    } catch (e) {
        console.dir(signal);
        console.error("_flexPublish => " + e);
        error = true;
    }
    if (!error) {
        console.dir(signal);


        if (dojo) {
            console.log("Sending Signal to Dojo")
            dojo.publish(signal.topic, signal.data);
        }

        if (currentModule && currentModule.forwardPublish) {
            console.log("Sending Signal to Module")
            currentModule.forwardPublish(signal.topic, signal.data);
        }
    }
}

function _flexSignal(signal, data) {
    // DEPRECATED
    console.log("SIGNAL FROM FLEX:" + signal);
    var args = [];
    console.log(typeof(data));
    try {
        if (typeof (data) === "object") {

            for (var key in data) {
                // a bug in the flex parser used
                // \n and \r are not replaced
                // it is important! that the whole json string is returned in just one line
                // else this code will break the string
                var text = data[key];//.replace(/\n/g, "\\n").replace(/\r/g,"\\r");
                args.push(JSON.parse(text));
            }
        } else if (typeof(data) !== "undefined") {
            //		console.log(data);
            args = JSON.parse(data);
        }


        dojo.publish(signal, args);
    } catch (e) {
        console.log("_flexSignalError => " + e);
    }
}

function _flexUserSettingSet(namespace, name, value) {
    sandbox.setUserVar(namespace + "::" + name, value);
}

function _flexUserSettingGet(namespace, name, def) {
    return {
        name: name,
        namespace: namespace,
        value: sandbox.getUserVar(namespace + "::" + name, def)
    };
}

function mysqlDateToLocal(dateString) {
    if (dateString == "0000-00-00" || dateString == "" || typeof(dateString) === "undefined") return "1900-01-01";

    //format may be 2010-01-01 HH:MM:SS
    if (dateString.lastIndexOf(" ") !== -1) {
        var dateArray = dateString.split(" ");
        dateString = dateArray[0];
    }


    var _date = dateString.split("-");
    var _do = new Date(_date[0], (parseInt(_date[1])).toString(), _date[2]);
    var year = _date[0];
    var day = _date[2];
    var month = _date[1];
    /*
     var month = _do.getMonth();
     if ( parseInt(month ) < 10 ) {
     month = "0" + month;
     }

     var day = _do.getDate();
     if ( parseInt( day ) < 10 ) {
     day = "0" + day;
     } */
    return day + "." + month + "." + year;
}

function mysqlDatetimeToLocal(dateString) {
    if (dateString == "0000-00-00" || dateString == "" || typeof(dateString) === "undefined") return "1900-01-01";
    var timeString;
    //format may be 2010-01-01 HH:MM:SS
    if (dateString.lastIndexOf(" ") !== -1) {
        var dateArray = dateString.split(" ");
        dateString = dateArray[0];
        timeString = dateArray[1];
    }


    var _date = dateString.split("-");
    var _do = new Date(_date[0], (parseInt(_date[1])).toString(), _date[2]);
    var year = _date[0];
    var day = _date[2];
    var month = _date[1];
    /*
     var month = _do.getMonth();
     if ( parseInt(month ) < 10 ) {
     month = "0" + month;
     }

     var day = _do.getDate();
     if ( parseInt( day ) < 10 ) {
     day = "0" + day;
     } */

    var _time = timeString.split(":");


    return day + "." + month + "." + year + " " + _time[0] + ":" + _time[1];
}

function mysqlDateFromDate(date) {

    var month = (parseInt(date.getMonth()) + 1);
    var monthStr = month.toString();
    if (month < 10) {
        monthStr = "0" + monthStr;
    }

    var day = (parseInt(date.getDate()));
    var dayStr = day.toString();
    if (day < 10) {
        dayStr = "0" + dayStr;
    }

    return date.getFullYear() + "-" + monthStr + "-" + dayStr;
}

function mysqlDateToDate(dateString) {
    //format may be 2010-01-01 HH:MM:SS
    if (dateString.lastIndexOf(" ") !== -1) {
        var dateArray = dateString.split(" ");
        dateString = dateArray[0];
    }

    var _date = dateString.split("-");
    _date[1] = _date[1].replace(/0([1-9])/, "$1");
    _date[2] = _date[2].replace(/0([1-9])/, "$1");
    return new Date(_date[0], (parseInt(_date[1]) - 1).toString(), _date[2]);
}

function ucfirst(str) {
    str += '';
    var f = str.charAt(0).toUpperCase();
    return f + str.substr(1);
}

var monate = ["-", "Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];
function intToMonth($i) {
    return monate[$i];
}

function showSWF(containerId, urlString) {
    var display = document.getElementById(containerId);
    if (display) {
        display.appendChild(createSWFObject(urlString, 650, 650));
    }
}

function createSWFObject(urlString, width, height) {
    var SWFObject = document.createElement("object");
    SWFObject.setAttribute("type", "application/x-shockwave-flash");
    SWFObject.setAttribute("width", "100%");
    SWFObject.setAttribute("height", "100%");
    var movieParam = document.createElement("param");
    movieParam.setAttribute("name", "movie");
    movieParam.setAttribute("value", urlString);
    SWFObject.appendChild(movieParam);
    return SWFObject;
}

function connectOnEnterKey(sourceOrId, targetObj, fn) {
    var target = dijit.byId(sourceOrId).domNode;
    dojo.connect(target, "onkeypress", function (e) {
        switch (e.charOrCode) {
            case dojo.keys.ENTER:
                dojo.hitch(targetObj, fn)();
                dojo.stopEvent(e);
                break;
        }

    });
}
