
	$("#vdrkYes").click( function () {
		$("#vdrkNr").addClass("required");
		$(".vdrkNr").show();
	});

	$("#vdrkNo").click( function () {
		$("#vdrkNr").removeClass("required");
		$(".vdrkNr").hide()
	});

	$("#rsvYes").click( function () {
		$("#rsvNr").addClass("required");
		$(".rsvNr").show()
	});

	$("#rsvNo").click( function () {
		$("#rsvNr").removeClass("required");
		$(".rsvNr").hide()
	});

	$("#dawYes").click( function () {
		$("#dawNr").addClass("required");
		$(".dawNr").show()
	});

	$("#dawNo").click( function () {
		$("#dawNr").removeClass("required");
		$(".dawNr").hide()
	});


