<?php
/**
 */
class ViewAkquiseTable extends Doctrine_Table
{
    public static function detailed() {

		$q = Doctrine_Query::create()
		->from('ViewAkquise a')
		->where('a.id = ?');

		return $q;
	}

	public static function detailSearch($string) {
		$string = strtolower(trim(utf8_decode($string)));

		if (strlen($string) == 2) {
			$q = Doctrine_Query::create()
			->from("ViewAkquise k")
			->where("k.firma LIKE ?", $string);
		} else {
			$q = Doctrine_Query::create()
			->from("ViewAkquise k")
			->where("k.firma LIKE ?", $string);

			$data = explode(" ", $string);
			foreach ( $data as $d ) {
				$d = utf8_encode($d);

				if ( !empty($d)) {
					$q->orWhere("k.firma <> '' AND k.firma LIKE ?", "%$d%");
				//	$q->orWhere("k.alias <> '' AND k.alias LIKE ?", "%$d%");
				}
			}
		}

		return $q;
	}

	public static function search($string) {
		$string = strtolower(trim(utf8_decode($string)));

		if (strlen($string) == 2) {
			$q = Doctrine_Query::create()
			->select('k.id')
			->from("ViewAkquise k")
			->where("k.firma LIKE ?", $string);
		} else {
			$q = Doctrine_Query::create()
			->select('k.id')
			->from("ViewAkquise k")
			->where("k.firma LIKE ?", $string);

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
		->from('ViewAkquise a')
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

		$firma = str_replace("- ", "-", $firma);
		$firma = str_replace(" -", "-", $firma);
		$firma = str_replace(" - ", "-", $firma);
		$firma = str_replace(" ", "%", $firma);
		$firma = str_replace("-", "%", $firma);

		$firma = utf8_encode($firma);
		$fD = explode("%", $firma);

		$q = Doctrine_Query::create()
		->select("k.*")
		->from('ViewAkquise k')
		->where("k.firma<>''")
		->andWhere("k.firma IS NOT NULL")
		->andWhere("k.id <> ". $kontakt_id);

		$hit = false;
		foreach ($fD as $f) {
			if ( empty($f)) continue ;
			if ( strlen ( $f) < 3) continue;
			if (in_array($f, $ignore)) continue ;

			$q->andWhere("k.firma LIKE ?", "%".$f."%");

			$hit = true;
		}

		if (!$hit) {
			$q->andWhere("1=0");
		}

		$q->limit(1);
		if ( $q->count() == 0) {
			$q = Doctrine_Query::create()
		->select("k.*")
		->from('ViewAkquise k')
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
		->from('ViewAkquise k')
		->limit($limit)
		->orderBy("k.firma ASC")
		->offset($offset);
		return $q;
	}
}