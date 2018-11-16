<span>

<?php
$mpath = (getRequiredAttribute ("mpath", $attributes));
$inputvalue = $dsl->get("dbtable", $mpath);

if ( count ( $inputvalue) == 0 ) {
    echo $value;
}

?>
</span>