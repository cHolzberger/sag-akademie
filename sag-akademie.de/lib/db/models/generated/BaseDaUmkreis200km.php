<?php

/**
 * BaseDaUmkreis200km
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $plz
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseDaUmkreis200km extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('da_umkreis_200km');
        $this->hasColumn('plz', 'string', 6, array(
             'type' => 'string',
             'length' => 6,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}