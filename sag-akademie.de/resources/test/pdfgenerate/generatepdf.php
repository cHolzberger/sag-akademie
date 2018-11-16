<?php
ini_set('include_path',
ini_get('include_path')
.PATH_SEPARATOR.realpath(dirname(__FILE__)."/../../../lib/html2pdf")."/");
define("HTML2PS_DIR", realpath(dirname(__FILE__)."/../../../lib/html2pdf")."/");
include_once('pipeline.factory.class.php');
include_once('fetcher.url.class.php');
include_once('config.inc.php');
parse_config_file('html2ps.config');

global $g_config;
$g_config = array(
                  'cssmedia'     => 'screen',
                  'renderimages' => true,
                  'renderforms'  => false,
                  'renderlinks'  => true,
                  'mode'         => 'html',
				  'method'		=> 'fpdf',
                  'debugbox'     => false,
                  'draw_page_border' => false
                  );

$media = Media::predefined('A4');
$media->set_landscape(false);
$media->set_margins(array('left'   => 5,
                          'right'  => 5,
                          'top'    => 50,
                          'bottom' => 50));
$media->set_pixels(1024);

global $g_px_scale;
$g_px_scale = mm2pt($media->width() - $media->margins['left'] - $media->margins['right']) / $media->pixels;

global $g_pt_scale;
$g_pt_scale = $g_px_scale * 1.43; 
$fn = "test";
 
$pipeline = new Pipeline;
$pipeline->fetchers[]     = new FetcherUrl;
$pipeline->data_filters[] = new DataFilterHTML2XHTML;
$pipeline->parser         = new ParserXHTML;
$pipeline->layout_engine  = new LayoutEngineDefault;
$pipeline->output_driver  = new OutputDriverFPDF($media);
$pipeline->destination = new DestinationFile( $fn );



echo $fn;

//http://sag-akademie.localhost/resources/test/pdfgenerate/tischbeschilderung.php

echo $pipeline->process('http://sag-akademie.localhost/pdf/teilnehmerliste/296', $media); 

//$pipeline->process('http://sag-akademie.localhost/resources/test/pdfgenerate/teilnehmerliste.php', $media); 

?>
