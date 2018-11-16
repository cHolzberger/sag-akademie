<?php
class ExtendedColumnsTemplate extends Doctrine_Template {
    static $labelHeaders;

    function setExtendenColumnInfoTableProxy ($col, $name, $converter="default", $hide=false) {
	if ( !is_array($this->labelHeaders[$col])) {
	    $this->labelHeaders[$col] = array();
	}
	$this->labelHeaders[$col]['field'] = $col;
        $this->labelHeaders[$col]['name'] = $name;
	$this->labelHeaders[$col]['converter'] = $converter;
    }

    function getExtendendColumnInfoTableProxy ($col) {
	$resolveTable = explode(":",$col);

	if ( count($resolveTable) > 1 ) {
	    return Doctrine::getTable($resolveTable[count($resolveTable)-1]).getExtendedColumnInfo($col);
	} else {
	    return $this->labelHeaders[$col];
	}
    }
}
?>