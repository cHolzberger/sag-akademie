<div style="position: fixed; height: 20px; right: 20px; top:10px;padding-right: 0px; text-align: right; vertical-align: middle; color: rgb(0, 0, 0);">

<?
extract ($GLOBALS['debug']);
$ident = Mosaik_ObjectStore::init()->get("/current/identity");
if ( $ident != null ) {
	$user = $ident->user();
} else {
	$user = "Unbekannt";
}
?>
<script type="text/javascript">
window.mUserID = '<?=$ident->uid()?>';
</script>
Angemeldet als: <b><?=$user ?></b><a href="/admin?logout=1"><img src="/img/admin/logout_button.png" border="0" alt="" hspace="5" align="right"/></a>

</div>