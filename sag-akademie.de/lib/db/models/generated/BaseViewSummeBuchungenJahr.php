<?php

/**
 * BaseViewSummeBuchungenJahr
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $jahr
 * @property integer $id
 * @property decimal $teilgenommen
 * @property decimal $abgesagt
 * @property decimal $umgebucht
 * @property decimal $storno
 * @property decimal $bestaetigt
 * @property decimal $nichtbestaetigt
 * @property decimal $gesamt
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5845 2009-06-09 07:36:57Z jwage $
 */
abstract class BaseViewSummeBuchungenJahr extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('view_summe_buchungen_jahr');
        $this->hasColumn('jahr', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => false,
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
        $this->hasColumn('teilgenommen', 'decimal', 32, array(
             'type' => 'decimal',
             'length' => 32,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('abgesagt', 'decimal', 32, array(
             'type' => 'decimal',
             'length' => 32,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('umgebucht', 'decimal', 32, array(
             'type' => 'decimal',
             'length' => 32,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('storno', 'decimal', 32, array(
             'type' => 'decimal',
             'length' => 32,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('bestaetigt', 'decimal', 32, array(
             'type' => 'decimal',
             'length' => 32,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('nichtbestaetigt', 'decimal', 32, array(
             'type' => 'decimal',
             'length' => 32,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('gesamt', 'decimal', 37, array(
             'type' => 'decimal',
             'length' => 37,
             'unsigned' => 0,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }

}