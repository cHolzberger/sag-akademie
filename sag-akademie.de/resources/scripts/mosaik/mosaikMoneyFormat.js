/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */


function MosaikMoneyFormat () {
	this.seperator = ",";
	this.thousandsSeperator=".";

	this.format =function (num) {
		// validate input

		if (isNaN(Number(num)))
			return '#Wert!#';
		// save the sign

		num = num.toString().replace(/\$|\,/g,'');
		if(isNaN(num))
			num = "0";
		sign = (num == (num = Math.abs(num)));
		num =	 Math.floor(num*100+0.50000000001);
		cents = num%100;
		num = Math.floor(num/100).toString();
		if(cents<10)
			cents = "0" + cents;
		for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
			num = num.substring(0,num.length-(4*i+3))+this.thousandsSeperator+
			num.substring(num.length-(4*i+3));
		return (((sign)?'':'-') +  num +this.seperator + cents);
	}

	this.toNumber =function (str) {
		str = str.replace (this.thousandsSeperator, "");
		str = str.replace ( this.seperator , ".");

		return new Number(str);
	}
}