<?php

	$href = getRequiredAttribute("href",$attributes);
	$year = getRequiredAttribute("year",$attributes);
	$months[0] = 'Januar';
	$months[1] = 'Februar';
	$months[2] = 'März';
	$months[3] = 'April';
	$months[4] = 'Mai';
	$months[5] = 'Juni';
	$months[6] = 'Juli';
	$months[7] = 'August';
	$months[8] = 'September';
	$months[9] = 'Oktober';
	$months[10] = 'November';
	$months[11] = 'Dezember';
	
?>

<div style="width: 150px; height: auto; overflow: auto; float: left; margin-right: 20px;">
				<h2>
					
					<?=$year?>
					<? 
						$value = $dsl->get("dbtable", "$year");
						if( !empty ($value) ) {
						    ?>
				<a href="/admin/buchungen?year=<?=$year?>&list=1"> <?
							echo " ($value)";
							?>
				</a>
							<?
						}
					?>
				</h2>
				<?php
				foreach($months as $key => $month){
					echo ('<a href="/admin/buchungen?year='.$year.'&month='.$key.'&list=1">' . $month );
					$value = $dsl->get("dbtable", $year."_". $key);
					if ( !empty( $value ) ) {
							echo " ($value) " ;
					}
					echo " </a><br/>";

}				?>
</div>
