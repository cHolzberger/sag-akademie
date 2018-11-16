/**
 * @author molle
 */
function public_init() {
	var xname = window.location.href.split("/");
	var nav_active = "";

	if (xname[3] == "" || xname[3] == "bildungscheck" || xname[3]=="zfkd" || xname[3] == "sachkunde") {
		nav_active = "startseite";
	} else if (xname[3] == "seminar" || xname[3]=="kunde") {
		nav_active = "seminare";
	} else if (xname[3] == "buchungen" ) {
		nav_active = "termine";
	} else  {
		nav_active=xname[3];
	}

	var prefix="nav_";
	if ( $("#nav_" + nav_active).length > 0 ) {
		prefix = "nav_";
	}
	if ($("#" + prefix + nav_active).length > 0) {
		$("#" + prefix + nav_active).addClass("active");
		$("#" + prefix + nav_active + "_img").addClass("active");
		
		var imgsrc = $("#" + prefix + nav_active + "_img").attr("src").replace(/nav_/g, "nav_a_");
		imgsrc = imgsrc.replace ("nav_a_a_", "nav_a_");
		$("#" + prefix + nav_active + "_img").attr("src", imgsrc);
		$("#" + prefix + nav_active + "_img").attr("onmouseout", "");
	} else { 
		//dlog("Fehler: #" + prefix + nav_active + " nicht gefunden.");
	}
};

function slideshow_init() {
	if ($(".aSlideshow").length > 0) {
		$(".aSlideshow").slideshow();
	}
};

 function imagechange () {
 


                setTimeout( function() {
                $("#homeimage1").fadeOut("slow");
                $("#homeimage2").fadeIn("slow");

                setTimeout( function() {
                $("#homeimage2").fadeOut("slow");
                $("#homeimage3").fadeIn("slow");

                setTimeout( function() {
                $("#homeimage3").fadeOut("slow");
                $("#homeimage1").fadeIn("slow");

                imagechange();

                } , 5000);

                } , 5000);

                } , 5000);



    }

$().ready(function() {
$("#homeimage2").hide(0);
$("#homeimage3").hide(0);
	slideshow_init();
	public_init();
	imagechange();
});

var youtubeError = 0;

function onYouTubeError() {
	youtubeError=1;
	alert("You tube fehler");
}

function onYouTubePlayerReady() { 
	var xname = window.location.href.split("/");
	var player = $("#youTubePlayer")[0];
	
	if (xname[3] == "" ) {
		nav_active = "startseite";
	} else if (xname[3] == "seminar") {
		nav_active = "seminare";
	} else if (xname[3] == "buchungen" ) {
		nav_active = "termine";
	} else  {
		nav_active=xname[3];
	}
	
	player.addEventListener("onError", "onYouTubeError");

	if ( $.cookie("playVideo_" + nav_active) != "1") {
		$.cookie("playVideo_" + nav_active, "1");
		if (youtubeError != 1) {
			player.playVideo();
		}
	}
}
$().ready(function() {
$(".hrefSelect").change(function() {
	window.location.href = $(this).val();
	});
	
});