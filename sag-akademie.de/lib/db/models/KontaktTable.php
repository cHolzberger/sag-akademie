<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class KontaktTable extends Doctrine_Table
{
	public function findDuplicates($data) {
		$ignore = array ("gmbh", "und", "&", "co.", "kg", "u.", "gmbg");

		// php sucks when doing utf8
		$firma = strtolower(trim(utf8_decode($data['firma'])));
		$oFirma = $data['firma'];
		$kontakt_id = $data['id'];

		$firma = str_replace("- ", "-", $firma);
		$firma = str_replace(" -", "-", $firma);
		$firma = str_replace(" - ", "-", $firma);
		$firma = str_replace(" ", "%", $firma);
		$firma = str_replace("-", "%", $firma);
		
		$firma = utf8_encode($firma);
		$fD = explode("%", $firma);

		$q = Doctrine_Query::create()
		->select("k.*")
		->from('Kontakt k')
		->where("k.firma<>''")
		->andWhere("k.firma IS NOT NULL")
		->andWhere("k.id <> ?" , array( $kontakt_id));
		
		$hit = false;
		foreach ($fD as $f) {
			if ( empty($f)) continue ;
			if (in_array($f, $ignore)) continue ;

			$q->andWhere("k.firma LIKE ?", "%".$f."%");

			$hit = true;
		}

		if (!$hit) {
			$q->andWhere("1=0");
		}

		//if ( !empty ($data['strasse']) && !empty ($data['plz']))
		//	$q->orWhere ("k.strasse <> '' AND k.strasse like LOWER(?) AND k.plz LIKE ? AND k.firma<>'' AND k.firma IS NOT NULL ", array($data['strasse'], $data['plz']));
		//;
		
		$q->limit(1);
		if ( $q->count() == 0) {
			$q = Doctrine_Query::create()
		->select("k.*")
		->from('Kontakt k')
		->where("k.firma<>''")
		->andWhere("k.firma IS NOT NULL")
		->andWhere("k.id <> ". $kontakt_id)
		->andWhere("TRIM(k.firma) LIKE ?", $oFirma)
		->limit(1);
			if ( $q->count() == 0 && !empty ($data['kundennr'])) {
				$q = Doctrine_Query::create()
					->select("k.*")
					->from('Kontakt k')
					->where("k.firma<>''")
					->andWhere("k.firma IS NOT NULL")
					->andWhere("k.id <> ?",array( $kontakt_id))
					->andWhere("k.kundennr LIKE ?", $data['kundennr'])
					
					->limit(1);	
			}
		} 
		return $q;
	}

	public function offsetFind($offset = 1, $limit = 100) {
		$q = Doctrine_Query::create()
		->select("k.*")
		->from('Kontakt k')
		->limit($limit)
		->orderBy("k.firma ASC")
		->offset($offset);
		return $q;
	}


	public static function detailedIn($ids) {

		$q = Doctrine_Query::create()
		->from('ViewKontakt a')
		->leftJoin('a.Personen b')
		->leftJoin('b.Buchungen c')
		->whereIn('a.id', $ids);

		return $q;
	}



}
?>
