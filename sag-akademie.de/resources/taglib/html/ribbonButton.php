<? $class = getAttribute("class", $attributes);
$href = getAttribute("href",$attributes, "#");
$reload = getAttribute("reload",$attributes, "false");
if ( $reload==1) $reload='true';
$onClick = 'onClick=\'$.mosaikRuntime.load("'.$href.'", '.$reload.'); return false;\'';
?> 
    <div class="ui-ribbon-button ui-corner-all <?=$class?>" <?=$onClick?>>
        <a  class="<?=$class?>">
            <div class="ui-ribbon-button-icon <?=$class?>"  >
 					<img src="/img/admin/<?= getAttribute("icon", $attributes) ?>.png" class="<?=$class?>" />
				</a>
				
			</div>
            <div class="ui-ribbon-button-label" class="<?=$class?>"  >
            	<a  class="<?=$class?>"><?=$value?></a>
            </div>
		</a>
    </div>
