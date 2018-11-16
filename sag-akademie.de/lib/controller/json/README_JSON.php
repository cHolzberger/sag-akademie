<?php
/* 
 * Use without written License forbidden
 * Copyright 2010 by MOSAIK Software - Christian Holzberger <ch@mosaik-software.de>
 */

/**
 * Description of README_JSON
 *
 * @author molle
 *
 *
 * Possible values of field types in json headers:
 *
 * price, nicht editierbar
 * date, nicht editierbar
 * anrede, editierbar
 * datetime, nicht editierbar
 * status -> 0 => Nicht Bestatigt, 1 => Bestaetigt, 2 => Stoniert, 3=>Umgebucht,
 * quelle, nicht editierbar
 * bool -> 0 => Nein, 1 => Ja, editierbar
 * email, nicht editierbr aber verlinkt, muss email heissen
 * web, nicht editierbar aber verlinkt, muss web heissen
 * combo, bietet dropdown anzugeben ueber values = array() bzw. Doctrine::getTable("X")->findAll(Doctrine::HYDRATE_ARRAY)
 */

?>
