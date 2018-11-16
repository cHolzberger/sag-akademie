<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class JSON_Dropdown extends SAG_Admin_Component {
    function map ($name) {
        return "JSON_Dropdown";
    }

    function renderJsonForward($class_name, $namespace = "") {
        $data =array();
        $info = array();
        $search = "";

        switch($this->next()) {
            case "bundesland":
                $table="XBundesland";
                break;
            default:
                return '{"error": "table not found"}';
        }

        $data = Doctrine::getTable($table)->findAll(Doctrine::HYDRATE_ARRAY);
        
        return json_encode ( array("mode" => "dropdown",  "data" => $data) );
    }

    function renderJson() {
        return "{'undefined'}";
    }

}
?>
