<? $forId = $attributes['attach']; ?>
<div style="z-index: 999;" id="sub_<?=$forId?>" class="vmenu">
        <?=$value?>
</div>

<? 
$script = <<<END

var close{$forId} = 0;
var jNav_{$forId} = $("#sub_{$forId}");

function isTimeout{$forId}() {
	with ( jNav_{$forId} ) {
	if ( close{$forId} == 1 ) {
		fadeOut(300);
		children(".menuItem").fadeOut(300);
		close{$forId}  = 2;
	}
	};
};

function instantHideMenu$forId() {
	// nicht gebraucht mit pos: absolute;
};

function showMenu{$forId}() {
	var container = $("#{$forId}");  
	with ( jNav_{$forId} ) {
		close{$forId}  = 0;

		var pos = container.position();
		var h = container.height();
		css("position", "absolute");
		css("z-index", "999");
	
		css("top", pos.top + h -2 );
		css("left", pos.left );
		fadeIn(150);
		children(".menuItem").show();
	};
	$(".nav").css("z-index", "999"); 
	$("img").css("z-index", "0");

};

function hideMenu{$forId}() {
	close{$forId}  = 1;
};

$(function () {

setInterval ("isTimeout$forId()", 1000);

//callbacks
$("#{$forId}_img").mouseover ( showMenu{$forId});
$("#{$forId}").mouseover ( showMenu{$forId});
$("#sub_{$forId}").mouseover ( showMenu{$forId});
$("#{$forId}").mouseout ( hideMenu{$forId});
$("#sub_{$forId}").mouseout ( hideMenu{$forId});
$(window).scroll(instantHideMenu{$forId});
});
;;
END;
addSiteScript($script);
?>
