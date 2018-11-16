<?php
/**
 */
class AkquiseKontaktTable extends Doctrine_Table
{
public static function detailed() {
		$q = Doctrine_Query::create()
		->from('AkquiseKontakt a')
		->where("a.id = ? AND a.deleted_at = '0000-00-00 00:00:00'")
                ->leftJoin("a.GeaendertVon updater")
		 ->leftJoin("a.AngelegtVon creator");

		return $q;
	}
	
	public static function search($string) {
		$string = strtolower(trim(utf8_decode($string)));
		
		if (strlen($string) == 2) {
			$q = Doctrine_Query::create()
			->select('k.id')
			->from("AkquiseKontakt k")
			->where("(k.firma LIKE ? OR k.alias LIKE ?) AND k.deleted_at = '0000-00-00 00:00:00'", array($string, $string));
		} else {
			$q = Doctrine_Query::create()
			->select('k.id')
			->from("AkquiseKontakt k")
			->where("(k.firma LIKE ? OR k.alias LIKE ? ) AND k.deleted_at = '0000-00-00 00:00:00'", array($string,$string));
			
			$data = explode(" ", $string);
			foreach ( $data as $d ) {
				$d = utf8_encode($d); 
				
				if ( !empty($d)) {
					$q->orWhere("k.firma <> '' AND k.firma LIKE ?", "%$d%");
				//	$q->orWhere("k.alias <> '' AND k.alias LIKE ?", "%$d%");
				}
			}
		}

		return $q->fetchArray();
	}
	
	public static function detailedIn($ids) {

		$q = Doctrine_Query::create()
		->from('AkquiseKontakt a')
		->where("a.deleted_at = '0000-00-00 00:00:00'")
		->whereIn('a.id', $ids);

		return $q;
	}
	
	/* fuer den duplikate check */
	public function findDuplicates($data) {
		$ignore = array ("gmbh", "und", "&", "co.", "kg", "u.", "gmbg", "+");

		// php sucks when doing utf8
		$firma = strtolower(trim(utf8_decode($data['firma'])));
		$oFirma = $data['firma'];
		$kontakt_id = $data['id'];
		$kontakt_plz = $data['plz'];

		$firma = str_replace("- ", "-", $firma);
		$firma = str_replace(" -", "-", $firma);
		$firma = str_replace(" - ", "-", $firma);
		$firma = str_replace(" ", "%", $firma);
		$firma = str_replace("-", "", $firma);
		
		$firma = utf8_encode($firma);
		$fD = explode("%", $firma);

		$q = new Doctrine_RawSql();
		$q->select("{k.*}")
		->from('akquise_kontakt k')
		->where("k.firma <> ''")
		->andWhere("k.firma IS NOT NULL")
		->andWhere("k.plz = ?", $kontakt_plz)
		->andWhere("k.id <> ?", array($kontakt_id))
		->addComponent('k',"AkquiseKontakt");
		
		$hit = false;
		foreach ($fD as $f) {
			if ( empty($f)) continue ;
			if ( strlen ( $f) < 3) continue;
			if (in_array($f, $ignore)) continue ;

			$q->andWhere("k.firma LIKE ?", "% ".$f." %");
			$hit = true;
		}
		if ( $hit == false ) {
		    return false;
		}

		$q->limit(1);

		if ( !$hit && $q->count() == 0) {
			$q = Doctrine_Query::create()
		->select("k.*")
		->from('AkquiseKontakt k')
		->where("k.firma<>''")
		->andWhere("k.firma IS NOT NULL")
		->andWhere("k.id <> ". $kontakt_id)
		->andWhere("TRIM(k.firma) LIKE ?", $oFirma)
		->limit(1);	
		}

		return $q;
	}
	
	public function offsetFind($offset = 1, $limit = 100) {
		$q = Doctrine_Query::create()
		->select("k.*")
		->from('AkquiseKontakt k')
		->limit($limit)
		->orderBy("k.firma ASC")
		->where ("k.firma <> '' AND k.firma IS NOT NULL AND k.deleted_at = '0000-00-00 00:00:00'")
		->offset($offset);
		return $q;
	}
}