<?php

/**
 * BaseAkquiseKontakt
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $zusatz
 * @property string $firma
 * @property string $strasse
 * @property string $nr
 * @property string $plz
 * @property string $ort
 * @property string $bundesland
 * @property integer $bundesland_id
 * @property string $kreis
 * @property string $regierungsbezirk
 * @property string $tel
 * @property string $fax
 * @property string $abteilung
 * @property string $anrede
 * @property string $titel
 * @property string $vorname
 * @property string $name
 * @property string $tel_durchwahl
 * @property string $mobil
 * @property string $url
 * @property string $email
 * @property integer $newsletter
 * @property date $abmelde_datum
 * @property date $anmelde_datum
 * @property integer $geaendert_von
 * @property integer $bereits_in_verteiler
 * @property integer $kontakt_id
 * @property string $vergleich
 * @property string $kontakt_quelle
 * @property date $kontakt_quelle_stand
 * @property string $umkreis
 * @property integer $kontaktkategorie
 * @property string $branche
 * @property timestamp $geaendert
 * @property integer $angelegt_user_id
 * @property integer $quelle_id
 * @property integer $qualifiziert
 * @property date $qualifiziert_datum
 * @property string $qualifiziert_notiz
 * @property string $ansprechpartner_email
 * @property integer $wiedervorlage
 * @property timestamp $deleted_at
 * @property integer $import_ssm
 * @property string $quelle_text
 * @property string $branche_text
 * @property string $alias
 * @property integer $hauptverwaltung
 * @property integer $vdrk_mitglied
 * @property string $vdrk_mitglied_nr
 * @property integer $dwa_mitglied
 * @property string $dwa_mitglied_nr
 * @property integer $rsv_mitglied
 * @property string $rsv_mitglied_nr
 * @property string $kundenstatus
 * @property integer $privat
 * @property string $email2
 * @property string $notiz
 * @property integer $branche_id
 * @property integer $taetigkeitsbereich_id
 * @property timestamp $angelegt_datum
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAkquiseKontakt extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('akquise_kontakt');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('zusatz', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('firma', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('strasse', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('nr', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('plz', 'string', 5, array(
             'type' => 'string',
             'length' => 5,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('ort', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('bundesland', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('bundesland_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('kreis', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('regierungsbezirk', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('tel', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('fax', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('abteilung', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('anrede', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('titel', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('vorname', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('name', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('tel_durchwahl', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('mobil', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('url', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('email', 'string', 256, array(
             'type' => 'string',
             'length' => 256,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('newsletter', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('abmelde_datum', 'date', null, array(
             'type' => 'date',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('anmelde_datum', 'date', null, array(
             'type' => 'date',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('geaendert_von', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('bereits_in_verteiler', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('kontakt_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('vergleich', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('kontakt_quelle', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('kontakt_quelle_stand', 'date', null, array(
             'type' => 'date',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('umkreis', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('kontaktkategorie', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '-1',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('branche', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('geaendert', 'timestamp', null, array(
             'type' => 'timestamp',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('angelegt_user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '-1',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('quelle_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '5',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('qualifiziert', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('qualifiziert_datum', 'date', null, array(
             'type' => 'date',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('qualifiziert_notiz', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('ansprechpartner_email', 'string', 200, array(
             'type' => 'string',
             'length' => 200,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('wiedervorlage', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('deleted_at', 'timestamp', null, array(
             'type' => 'timestamp',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('import_ssm', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('quelle_text', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('branche_text', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('alias', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('hauptverwaltung', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('vdrk_mitglied', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('vdrk_mitglied_nr', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('dwa_mitglied', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('dwa_mitglied_nr', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('rsv_mitglied', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('rsv_mitglied_nr', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('kundenstatus', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('privat', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('email2', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('notiz', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('branche_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '6',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('taetigkeitsbereich_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '10',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('angelegt_datum', 'timestamp', null, array(
             'type' => 'timestamp',
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