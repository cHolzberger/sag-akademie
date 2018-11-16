<div class="container">
<? $dsName = $do->getAttr("key","news");
$ds = $do->getArray(); 
 if ( sizeof ($ds[$dsName]) > 0 ) {  ?>
<div class="downcast newsheader">Neuigkeiten</div>
				<div id="news-box">
					<? foreach ($ds[$dsName] as $news) {
						 echo $news;
					}	
					?>
				</div>				
<? } ?>
</div>
