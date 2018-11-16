<?
class SAG_Component extends k_Component {
	public $dataStore = NULL;
	public $pageReader = NULL;
	public $pageReaderConfig = NULL;

	function __construct () {
	}

	function construct () {

	}

	function addBreadcrumb ( $url = FALSE, $name = FALSE ) {
		if ( $url === FALSE ) $url = $this->url();
		if ( $name === FALSE ) $name = $this->name();

		$GLOBALS['path'][] = array('name' => $name, 'url' => $url);
	}

	function createPageReader ( $config = NULL ) {
		$config == NULL ?
			$this->pageReaderConfig = new MosaikPageReaderConfig() :
			$this->pageReaderConfig = $config;

		if ( $this->dataStore == NULL ) $this->dataStore = new MosaikDatasource("content");
		$this->pageReader = new MosaikPageReader($this->pageReaderConfig);
		$this->pageReader->addDatasource( $this->dataStore );
		$this->pageReader->initElements();

		return array($this->pageReaderConfig, $this->pageReader);
	}

	function getAccepted ( $suffix = "" ) {
		$accept = array();
		foreach ( $this->renderers as $types => $handler ) {
			$handler = $handler . $suffix;
			if ( is_callable( array($this, $handler) ) ) {
				$split = explode( ";", $types );
				foreach ( $split as $type ) {
					$accept[$type] = array("handler" => $handler, "contentType" => $split[0]);
				}
			}
		}

		return $accept;
	}

	function extractSubtype ( $path ) {
		if ( $path == NULL ) return "html";

		$info = explode( ";", $path );

		if ( count( $info ) < 2 ) return "html"; // FIXME!

		return $info[1];
	}


	function forward ( $class_name, $namespace = "" ) {
		$GLOBALS['firephp']->log( "SAG_Component::Forward" );

		if ( is_array( $class_name ) ) {
			$namespace  = $class_name[1];
			$class_name = $class_name[0];
		}

		$subspace = $this->extractSubtype( $this->subspace() );
		//$GLOBALS['firephp']->log($subspace, "subspace");

		$accept = $this->getAccepted( "Forward" );

		$content_type = $this->negotiateContentType( array_keys( $accept ), $subspace );

		if ( isset($accept[$content_type]) ) {
			$GLOBALS['contentType'] = $accept[$content_type]['contentType'];

			return $this->{$accept[$content_type]['handler']}( $class_name, $namespace );
		}
		//$GLOBALS['firephp']->log("SAG_Component::Forward -> dispatch");
		$next = $this->createComponent( $class_name, $namespace );

		return $next->dispatch();
	}

	function renderIframe () {
		return $this->renderHtml();
	}

	function renderDownload () {
		return $this->renderHtml();
	}

	function renderIframeForward ( $class_name, $namespace = "" ) {
		return $this->renderHtmlForward( $class_name, $namespace );
	}

	function renderDownloadForward ( $class_name, $namespace = "" ) {
		return $this->renderHtmlForward( $class_name, $namespace );
	}

	function GET () {
		//$GLOBALS['firephp']->log("SAG_Component::GET");

		$subspace = $this->extractSubtype( $this->segment() );
		//$GLOBALS['firephp']->log($subspace, "subspace");

		$accept = $this->getAccepted();

		$content_type = $this->negotiateContentType( array_keys( $accept ), $subspace );

		if ( isset($accept[$content_type]) ) {
			$GLOBALS['contentType'] = $accept[$content_type]['contentType'];

			return $this->{$accept[$content_type]['handler']}();
		}

		throw new k_NotImplemented();
	}
}

?>
