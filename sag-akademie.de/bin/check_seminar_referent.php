<?

$max_db_count=73;
$results = array();

$mysqli_stable = new mysqli("mysqldb","root","J85sc83H","dev_sagakademie");
$mres = $mysqli_stable->query("SELECT * FROM seminar_referent WHERE start_stunde=0 AND start_minute=0
AND ende_stunde=0 AND ende_minute=0 
AND start_praxis_stunde=0 AND start_praxis_minute=0
AND ende_praxis_stunde=0 AND ende_praxis_minute=0");

while( $row = $mres->fetch_object() ) {

	//echo "Hit on: " .$row->id."\n";
	array_push($results,$row);
}

$mres->close();
$mysqli_stable->close();

for ( $i=1; $i<=$max_db_count; $i++) {
	echo ( "Connecting to: sag-akademie_de_".$i."\n"); 
	$mysql_old = new mysqli("mysqldb","root","J85sc83H", "sag-akademie_de_".$i);

	foreach ( $results as $row ) {
		$query= "SELECT * FROM seminar_referent WHERE seminar_id={$row->seminar_id} ".
"AND tag={$row->tag} ".
"AND referent_id={$row->referent_id} ".
"AND( start_stunde!={$row->start_stunde} OR start_minute!={$row->start_minute} ".
"OR ende_stunde!={$row->ende_stunde} OR  ende_minute!={$row->ende_minute} ".
"OR start_praxis_stunde!={$row->start_praxis_stunde} OR start_praxis_minute!={$row->start_praxis_minute} ".
"OR ende_praxis_stunde!={$row->ende_praxis_stunde} OR ende_praxis_minute!={$row->ende_praxis_minute})";
		
		$mres = $mysql_old->query($query);
		if ( $mres ) {

			while( $rowx = $mres->fetch_object() ) {
				echo "Hit on: " .$rowx->id."\n";
			}

			$mres->close();
		}
		continue;
		$query= "SELECT * FROM seminar_referent WHERE seminar_id={$row->seminar_id} ".
"AND tag={$row->tag} ".
"AND referent_id={$row->referent_id} ".
"AND( start_stunde!={$row->start_stunde} OR start_minute!={$row->start_minute} ".
"OR ende_stunde!={$row->ende_stunde} OR ende_minute!={$row->ende_minute})";
		
		$mres = $mysql_old->query($query);
		if ( $mres ) {

			while( $rowx = $mres->fetch_object() ) {
				echo "Hit on: " .$rowx->id."\n";
			}

			$mres->close();	
		}

	}

	$mysql_old->close();
}
