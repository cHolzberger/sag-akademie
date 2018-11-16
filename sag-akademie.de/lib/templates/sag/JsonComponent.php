<?php
/*
 * 21.10.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */

class JsonComponent extends k_Component {
	var $table = null;
	public $saveData;
	/**
	 *
	 * @param array $data
	 */
	function saveRow($data) {
		qlog(__CLASS__ . "::" . __FUNCTION__);
		$this->saveData = $data;

		if ($this->getTable() == null)
			return;

		$data = $this->saveData;
		$save = array();
		$headers = $this->getHeaders();

		$save['id'] = $data['id'];

		foreach ($headers as $head) {

			if (isset($head['hidden']))
				continue;

			if (isset($head['editable'])) {
				if (!array_key_exists($head['field'], $data))
					continue;

				switch ( $head['format']) {
					case "price" :
						$save[$head['field']] = priceToDouble($data[$head['field']]);
						break;
					default :
						$save[$head['field']] = $data[$head['field']];
				}
			}
		}

		$data = $this->getTable()->find($save['id']);
		$this->beforeSave($save, $data);
		$data->merge($save);
	//	qdir($save);
		$data->save();

		clearCache($this->getTable(), $data->toArray(), $save['id']);
	}

	function getTable() {
		return null;
	}

	function beforeSave(&$data) {

	}

	function POST() {
		qlog(__CLASS__ . "::" . __FUNCTION__);

		if (isset($_POST['data'])) {
			$data = json_decode($_POST['data'], true);
			MosaikDebug::msg($data, "Data");
			$this->saveRow($data['saveRow']);
			if ($data['noRefresh'])
				return "{}";
		}

		return $this->renderJson();
	}

	function renderJson() {

	}

	function getHeaders() {
		return array();
	}

}
?>
