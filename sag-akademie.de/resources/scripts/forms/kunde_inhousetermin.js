$().ready(function() {
	$(".tn").each(function(){
		var rowNo = parseInt($('input[name=count]',this).val())-1;
		
		$("input", this).each(function() {
			var name = this.name.replace("]","").replace("[","][");
			
			$(this).attr("name", "Buchung["+rowNo+"][" + name + "]");
		})
	})
})
