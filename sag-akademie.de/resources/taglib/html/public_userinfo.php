<?
$ident = Mosaik_ObjectStore::init()->get("/current/identity");
if ( ! $ident->anonymous() ):
	$user = $ident->user();
?>

<div style="position: absolute; top:0px; width: 100%; text-align: center; height: 14px;">
		<span style="background-color: #ffffff; padding-top: 1px; padding-bottom: 1px;">
			&nbsp;Angemeldet als: <a href="/kunde/startseite" ><?=$user?></a>&nbsp;&nbsp;
		</span>
		<a href="/kunde/startseite?logout=1" border="0"><img src="/img/admin/logout_button.png" border="0" alt="Ausloggen" style="position: absolute; top: 0px;"/></a>
</div>
<? endif;?>