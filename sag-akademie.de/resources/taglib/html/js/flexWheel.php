
<script language="JavaScript" type="text/javascript">
<!--
    if(!(document.attachEvent)) {
        window.addEventListener("DOMMouseScroll", handleWheel, false);
        window.addEventListener("mousewheel", handleWheel, false);
    }
    function handleWheel(event) {
	if (FABridge) {
	    if (typeof(FABridge['itable']) == "undefined") return;
	    
	    var app = FABridge["itable"].root();
	    if (app) {
		  var delta = 0;
		if ( event.wheelDelta) {
		    delta = event.wheelDelta / -40
		} else {
		    delta = event.detail;
		}

                var o = {x: event.screenX, y: event.screenY,
                    delta: delta,
                    ctrlKey: event.ctrlKey, altKey: event.altKey,
                    shiftKey: event.shiftKey}

                app.handleWheel(o);
            }
        }
    }
// -->
</script>