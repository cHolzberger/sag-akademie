define(["dojo/_base/declare"], function (declare) {
    var colors = {
        statusGreen: "#00ff00",
        statusRed: "#ff0000",
        statusYellow: "#ffff00",
        statusOrange: "#FF9900"
    };

    /** extern **/
    function mysqlDateToLocal (dateString) {
        if (!dateString || dateString == "0000-00-00" || dateString == "" || typeof(dateString) === "undefined") return "";
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
    function mysqlDatetimeToLocal (dateString) {
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

    function mysqlDateFromDate (date) {

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

    function mysqlDateToDate (dateString) {
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

    return {
        formatter: {
            date: function (rowdata) {
                var value = rowdata[this.field];

                return mysqlDateToLocal(value);
            },
            datetime: function (rowdata) {
                var value = rowdata[this.field];

                return mysqlDatetimeToLocal(value);
            },
            status: function (rowdata) {
                var value = rowdata[this.field];
                switch (value) {
                    case "0": // frueher mal Nicht Bestaetigt
                    case "1":
                    case "4": // vorher abgesagt -> termin ist abgesagt

                        return "Gebucht";
                        break;
                    case "2":

                        return "Storniert";
                        break;
                    case "3":
                        return "Umgebucht";
                        break;
                    case "5":
                        return "Nicht teilgen.";
                        break;
                    default:
                        return "Unbekannt"
                }
            },
            combo: function (rowdata) {
                var value = rowdata[this.field];
                var values = this.values;
                if ( !value ) return "";

                    for (var i = 0; i < values.length; i++) {
                        if (values[i].id == value) {
                            return values[i].name;
                        }
                    }
                    return "";
             },
            bool: function (rowdata) {
                var value = rowdata[this.field];
                if (value === "true" || value === 1 || value === "1") {
                    return "Ja";
                } else {
                    return "Nein";
                }
            }
        },

        style: {
            status: function (cell) {
                var value = cell.rawData();
                switch (value) {
                    case "0": // frueher mal Nicht Bestaetigt
                    case "1":
                    case "4": // vorher abgesagt -> termin ist abgesagt
                        // Gebucht
                        return "background-color: "+ colors.statusGreen+";";
                        break;
                    case "2":
                        //Storniert
                        return "background-color: "+ colors.statusRed+";";
                        break;
                    case "3":
                        //Umgebucht
                        return "background-color: "+ colors.statusYellow+";";
                        break;
                    case "5":
                        // Nicht teilgen
                        return "background-color: "+ colors.statusOrange+";";
                        break;
                    default:
                        return "";
                }


            }
        }



    }
});