<?php

/**
 * ViewSeminarTeilnehmerNichtErreicht
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5845 2009-06-09 07:36:57Z jwage $
 */
class ViewSeminarTeilnehmerNichtErreicht extends BaseViewSeminarTeilnehmerNichtErreicht
{
    function setUp() {
	parent::setUp();
	$this->actAs("SeminarTemplate");
    }

 function getDN() {
	$invoker = $this;
	return $invoker->kursnr . " Teilnehmer: ".$invoker->anzahl_teilnehmer." von Minimal: ".$invoker->teilnehmer_min;
    }
}