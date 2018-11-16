/**
 * @author molle
 */

function ribbonUpdate() {
	$("#ribbon").find("div.removeOnReload .ui-ribbon-button").click(function() {
		$(".ui-ribbon .ui-ribbon-button").removeClass("ui-state-active");
		$(this).addClass("ui-state-active");
	});
	
	$("#ribbon").find("div.removeOnReload .ui-ribbon-button").find("a").bind("aClick", function() {
		$(".ui-ribbon .ui-ribbon-button").removeClass("ui-state-active");
		$(this).closest(".ui-ribbon-button").addClass("ui-state-active");
	});
	
	$("#ribbon").find("div.removeOnReload .ui-ribbon-button").mouseenter(function() {
		$(this).addClass("ui-state-hover");
	});
	
	$("#ribbon").find("div.removeOnReload .ui-ribbon-button").mouseleave(function() {
		$(this).removeClass("ui-state-hover");
	});
}
function ribbonInit() {
	$(".ui-ribbon").tabs();
	
	$(".ui-ribbon-button").find("a").bind("aClick", function() {
		$(".ui-ribbon .ui-ribbon-button").removeClass("ui-state-active");
		$(this).closest(".ui-ribbon-button").addClass("ui-state-active");
	});
	
	$(".ui-ribbon-button").click(function() {
		$(".ui-ribbon .ui-ribbon-button").removeClass("ui-state-active");
		$(this).addClass("ui-state-active");
	});
	
	$(".ui-ribbon-button").mouseenter(function() {
		$(this).addClass("ui-state-hover");
	});
	
	$(".ui-ribbon-button").mouseleave(function() {
		$(this).removeClass("ui-state-hover");
	});
	
	$(".ui-ribbon").tabs().find(".ui-tabs-nav").sortable({
		axis: 'x'
	});
	
	$(".ui-ribbon").show();
	$(".ui-ribbon").trigger("ribbonReady", this);
	$().bind("loadComplete", function() {
		$("a[href='"+ $.mosaikRuntime.path +"']").closest(".ui-ribbon-button").addClass("ui-state-active");
	})
}

$(function() {
	ribbonInit();
});

