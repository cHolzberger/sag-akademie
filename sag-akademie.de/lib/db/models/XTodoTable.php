<?php
/**
 */
class XTodoTable extends Doctrine_Table
{
	public function setUp() {
		$this->actAs("ChangeCounted");
	}
}