<? /* fetches scripts from google cdn */
$useUi = getAttribute("ui");
$useUi = !($useUi == "false" );
 
if ( MosaikConfig::getVar("noGoogle", true) && MosaikConfig::isDebug("googlescript")): ?>
	<script src="/resources/cache/google/jquery.js"></script>
	<script src="/resources/cache/google/jquery-ui.js"></script>
	<script src="/resources/cache/google/swfobject_src.js"></script>
?> else if (MosaikConfig::getVar("noGoogle", true)): ?>
	<script src="/resources/cache/google/jquery.min.js"></script>
	<script src="/resources/cache/google/jquery-ui.min.js"></script>
	<script src="/resources/cache/google/swfobject.js"></script>
?> else if (MosaikConfig::isDebug("googlescript")): ?>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js"></script>
	<? if ($useUi): ?>	
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.js"></script>
	<? endif;?>
	
	<script src="//ajax.googleapis.com/ajax/libs/swfobject/2.1/swfobject_src.js"></script>
<? else: ?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<? if ($useUi): ?>	
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
	<?endif;?>
	<script src="//ajax.googleapis.com/ajax/libs/swfobject/2.1/swfobject.js"></script>
<? endif; ?>