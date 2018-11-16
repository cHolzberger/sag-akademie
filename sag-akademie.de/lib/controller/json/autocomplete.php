<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class JSON_Autocomplete extends SAG_Admin_Component {
    function map ($name) {
        return "JSON_Autocomplete";
    }

    function renderJsonForward($class_name, $namespace = "") {
        $data =array();
        $info = array();
        $search = "";

        switch($this->next()) {
            case "person":
                $table="Person";
                break;
            case "termin":
                $table="Seminar";
                break;
            case "hotel":
                $table = "Hotel";
                break;
            case "kontakt":
                $table = "Kontakt";
                break;
            default:
                return '{"error": "table not found"}';
        }

        if(isset($_POST['q'])) {
            $search = $_POST['q'];
        } else if(isset($_GET['q'])) {
                $search = urldecode ($_GET['q']);
            }

        $data = Doctrine::getTable($table)->autocomplete( $search )->fetchArray();
        if ( $table == "Seminar" ) {
            for ( $i=0; $i < count($data); $i++) {
                $data[$i]['datum_begin'] = mysqlDateToLocal($data[$i]['datum_begin']);
                $data[$i]['datum_ende'] = mysqlDateToLocal($data[$i]['datum_ende']);
            }
        }

        switch ($this->next()) {
            case "hotel":
                for ($i=0; $i<count($data);$i++ ) {
                    $data[$i]['id'] = $data[$i]['_id'];
                    $data[$i]['zimmerpreis_ez'] = euroPreis($data[$i]['zimmerpreis_ez']);
                    $data[$i]['zimmerpreis_dz'] = euroPreis($data[$i]['zimmerpreis_dz']);
                    $data[$i]['zimmerpreis_mb46'] = euroPreis($data[$i]['zimmerpreis_mb46']);
                    $data[$i]['marge'] = euroPreis($data[$i]['marge']);
                    $data[$i]['fruehstuecks_preis'] = euroPreis($data[$i]['fruehstuecks_preis']);
                }
                break;
            
            case "termin":
                for ($i=0; $i<count($data);$i++ ) {
                    $data[$i]['kursgebuehr'] = euroPreis($data[$i]['kursgebuehr']);
                    $data[$i]['kosten_verpflegung'] = euroPreis($data[$i]['kosten_verpflegung']);
                    $data[$i]['kosten_unterlagen'] = euroPreis($data[$i]['kosten_unterlagen']);
                }
                break;
        }

        return json_encode ( array("mode" => "autocomplete",  "data" => $data) );
    }

    function renderJson() {
        return "{'undefined'}";
    }

}
?>
