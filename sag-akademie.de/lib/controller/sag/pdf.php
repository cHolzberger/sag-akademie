<?php



class SAG_Pdf extends SAG_Component {

	public $map = array(
		"top"  => "SAG_PDF_Top",
		"top2" => "SAG_PDF_Top2",
		"seminarprogramm" => "SAG_PDF_Seminarprogramm",
		"rubriktop" => "SAG_PDF_RubrikTop"
	);

	function map ( $name ) {
		return $this->map[$name];
	}


	function renderPdfForward ( $class_name, $namespace = "" ) {
		//	$this->identity()->authenticate("admin");
		//$this->addBreadcrumb($this->url(), "Admin");

		list($config, $content) = $this->createPageReader();

		if ( is_array( $class_name ) ) {
			$namespace  = $class_name[1];
			$class_name = $class_name[0];
		}

		$next    = $this->createComponent( $class_name, $namespace );
		$content = "";

		qlog( "LoadingContainer PDF Container..." );
		try {
			$content = $next->dispatch();
		} catch (MosaikPageReader_PageNotFound $e) {
			$content = $e->content();
			$this->dataStore->add( "text", $content );
			$this->dataStore->add( "version", MosaikConfig::getVar( "version" ) );
			$this->pageReader->loadPage( "__pdf.xml" );
			throw new k_HttpResponse("404", $this->pageReader->output->get());
			//throw new k_HttpResponse("404", mb_convert_encoding( $this->pageReader->output->get(), "UTF-8","auto"));
		}
		$id = md5( microtime() );
		$tm = $GLOBALS['pdf-margin-top'];
		$bm = $GLOBALS['pdf-margin-bottom'];
		$lm = $GLOBALS['pdf-margin-left'];
		$rm = $GLOBALS['pdf-margin-right'];
		$ps = $GLOBALS['pdf-page-size'];

		qlog( __CLASS__ . "::" . __FUNCTION__ . ": Trying to generate PDF with id: $id" );
		try {
			$landscape = $GLOBALS['pdf-landscape'];

			qlog( "convert_to_pdf ..." );

			$url         = "http://" . $_SERVER['SERVER_NAME'] . str_replace( ";pdf", ";iframe", $_SERVER['REQUEST_URI'] );
			$target      = WEBROOT . "/resources/pdf/$id.pdf";
			$footerRight = "Ausdruck vom: " . date( "d.m.Y H:i" );
			if ( $landscape ) {
				$orientation = "Landscape";
			} else {
				$orientation = "Portrait";
			}


			//convert_to_pdf( $html, $id, $tm, $bm, $lm, $rm, $landscape); // can we get rid of writing the file?
			$cmd = "wkhtmltopdf --zoom 1 -B {$bm}mm -T {$tm}mm -s {$ps} --orientation $orientation \"$url\" " . $target;
			$msg = system( $cmd . " 2>&1 " );
			if ( file_exists( $target ) ) {
				$pdf = file_get_contents( $target );
				unlink( $target );
			} else {
				qlog( "Error Running \"$cmd\":\n" . $msg );
				$pdf = $msg;
			}
		} catch (Exception $e) {
			qlog( "Exception:" );
			qlog( $e->getMessage() );
		}

		return $pdf;
	}


	function renderHtmlForward ( $class_name, $namespace ) {
		list($config, $content) = $this->createPageReader();

		if ( is_array( $class_name ) ) {
			$namespace  = $class_name[1];
			$class_name = $class_name[0];
		}

		$next    = $this->createComponent( $class_name, $namespace );
		$content = "";

		try {
			$content = $next->dispatch();
			$this->dataStore->add( "text", $content );
			$this->dataStore->add( "version", MosaikConfig::getVar( "version" ) );

			//$this->dataStore->add ("path",$GLOBALS['path']);

			$this->pageReader->loadPage( "__pdf.xml" );

			return mb_convert_encoding( $this->pageReader->output->get(), "UTF-8", "auto" );
		} catch (MosaikPageReader_PageNotFound $e) {
			$content = $e->content();
			$this->dataStore->add( "text", $content );
			$this->dataStore->add( "version", MosaikConfig::getVar( "version" ) );
			$this->pageReader->loadPage( "__pdf.xml" );
			throw new k_HttpResponse("404", $this->pageReader->output->get());
			//throw new k_HttpResponse("404", mb_convert_encoding( $this->pageReader->output->get(), "UTF-8","auto"));
		}


		//throw new k_SeeOther("/resources/pdf/$id.pdf");
	}

	function renderHtml () {
		return "";
	}

	function renderPdf () {
		return "";
	}

	function renderIframe() {
		return "";
	}

	function renderIframeForward ( $class_name, $namespace = "" ) {
		//	$this->identity()->authenticate("admin");
		//$this->addBreadcrumb($this->url(), "Admin");

		list($config, $content) = $this->createPageReader();

		if ( is_array( $class_name ) ) {
			$namespace  = $class_name[1];
			$class_name = $class_name[0];
		}

		$next    = $this->createComponent( $class_name, $namespace );
		$content = "";

		try {
			$content = $next->dispatch();
			$this->dataStore->add( "text", $content );
			$this->dataStore->add( "version", MosaikConfig::getVar( "version" ) );

			//$this->dataStore->add ("path",$GLOBALS['path']);

			$this->pageReader->loadPage( "__pdf.xml" );
			// $this->pageReader->output->get();
			//return mb_convert_encoding($this->pageReader->output->get() , "UTF-8","auto");
		} catch (MosaikPageReader_PageNotFound $e) {
			$content = $e->content();
			$this->dataStore->add( "text", $content );
			$this->dataStore->add( "version", MosaikConfig::getVar( "version" ) );
			$this->pageReader->loadPage( "__pdf.xml" );
			throw new k_HttpResponse("404", $this->pageReader->output->get());
			//throw new k_HttpResponse("404", mb_convert_encoding( $this->pageReader->output->get(), "UTF-8","auto"));
		}
		$id = md5( microtime() );
		$tm = $GLOBALS['pdf-margin-top'];
		$bm = $GLOBALS['pdf-margin-bottom'];
		$lm = $GLOBALS['pdf-margin-left'];
		$rm = $GLOBALS['pdf-margin-right'];

		$landscape = $GLOBALS['pdf-landscape'];

		return $this->pageReader->output->get();


	}

	function HEAD () {
		throw new k_http_Response(200);
	}

	function GET () {

	}
}

?>
