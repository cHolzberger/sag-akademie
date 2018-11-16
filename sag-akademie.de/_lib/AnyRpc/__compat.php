<?php

if ( false === function_exists('lcfirst') ) {
    function lcfirst( $str )
    { return (string)(strtolower(substr($str,0,1)).substr($str,1));}
}