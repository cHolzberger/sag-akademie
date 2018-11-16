<?php
/*
 * 03.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */
//$mpath = getRequiredAttribute("mpath", $attributes);
?>
    <div style="float: left;">
	<textarea id="vorlagen_editor" />
    </div>
    <div style="float: left; width: 200px; padding-left: 10px;">
	<h2>Gl&uuml;ckw&uuml;nsche</h2>
	<div style="padding-left: 10px;">
	    <a href="#" id="geb_mail">Geburtstags-Mail</a><br/>
	</div>
	<h2>Warnungen</h2>
	<div style="padding-left: 10px;">
    	    <a href="#" id="warnung1_mail">Teilnehmer Warnung 1</a><br/>
    	    <a href="#" id="warnung2_mail">Teilnehmer Warnung 2</a><br/>
	    <a href="#" id="warnung3_mail">Teilnehmer Warnung 3</a><br/>
	    <br/>
	    <br/>
	</div>
	
	    <?=$value?>
    </div>
<script type="text/javascript" src="/resources/scripts/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="/resources/scripts/ckeditor/adapters/jquery.js"></script>

<script type="text/javascript" src="/resources/scripts/forms/dokumente.js"></script>

