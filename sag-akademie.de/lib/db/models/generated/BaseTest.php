<?php

/**
 * BaseTest
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property date $datum_begin
 * @property string $kursnr
 * @property string $name
 * @property string $email
 * @property integer $newsletter
 * @property date $newsletter_anmeldedatum
 * @property integer $id
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5845 2009-06-09 07:36:57Z jwage $
 */
abstract class BaseTest extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('test');
        $this->hasColumn('datum_begin', 'date', null, array(
             'type' => 'date',
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('kursnr', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'primary' => false,
             'default' => '',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('name', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('email', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('newsletter', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('newsletter_anmeldedatum', 'date', null, array(
             'type' => 'date',
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

}