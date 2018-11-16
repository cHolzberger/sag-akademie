<?
class Generic_Admin_Component extends Generic_Component {
        function createPageReader($config = null) {
                $config = new MosaikPageReaderConfig(MosaikConfig::getVar("srvPagePath") . "/admin");
                $config->httpBasepath = MosaikConfig::getVar("srvPagePath") . "/admin/httpstatus/";
                return parent::createPageReader($config);
        }
		
		function forward ( $class_name, $namespace = "") {
			$this->identity()->authenticate("admin");
			return parent::forward($class_name, $namespace);
		}
}
?>