<? if (config::isDebug("firephp")): 
	$GLOBALS['firephp']->log($_SERVER,"_SERVER");
	if ( FALSE === strpos($_SERVER['HTTP_USER_AGENT'], "FirePHP")):
?>
	<script type="text/javascript" src="http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js"></script>

	<? $script = <<<END
		function custinit () { 
			firebug.win.minimize();
		};
	
		$().ready(function() {
			firebug.lib.util.Init.push(custinit);
		}); 
END;
		addSiteScript($script);
	endif; 
endif; ?>