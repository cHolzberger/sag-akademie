<?php

#load up the lib and run some tests

include ("mosaikPageReader.class.php");

?>
<h1>[Elements] Loading Elements...</h1>
<? 
$eg = new MosaikElementGenerator("./elements/html/");
print_r( $eg->loadElements());
?>
<br/>
<h1>[Elements] Found the following elements...</h1>
<? 
$eg = new MosaikElementGenerator("./elements/html/");

$elements = $eg->loadElements();
foreach ( $elements->items() as $element ) {
	print "{$element->name} filename: {$element->filename}<br/>";
} 
?>

<br/>
<h1>[PageReader] Running with loaded Elements</h1>
<? 
$eg = new MosaikElementGenerator("./elements/html/");

$elements = $eg->loadElements();

#using ./ as docroot
$pr = new MosaikPageReader("./");
$pr->setElementList($elements);
$pr->loadPage("index.php");

?> Parsing Results: 
<br>
<br>
<div>

<?=$pr->output->get();?>

</div>
