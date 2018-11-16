<?

/**
 *  bootstrap doctrine
 * einrichten der Datenbankverbindung
 */

class DBPool_Bootstrap {

	private $_config = null;
	
	/**
	 * richtet den dbpool so ein das er funktioniert wenn er in andere Projekte eingebunden wird
	 *
	 * @param string configurations profil das benutzt werden soll
	 */
	function __construct($config) {
		$this->_config = $config;
	}

	
	/**
	 * richtet die datenbankverbindung ein
	 */
	function init() {
		if (file_exists( $this->_config->db->templatesPath . "/init.php") ) {
			include ( $this->_config->db->templatesPath . "/init.php" );
		}

		$manager = Doctrine_Manager::getInstance();
		$manager->setAttribute('auto_free_query_objects', false);
		$manager->setAttribute('portability', 'none');
		$manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);
		//$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
		$manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, false);
		$manager->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_NONE);
		$manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_NONE);
		$manager->setAttribute(Doctrine_Core::ATTR_USE_NATIVE_ENUM, true);
		//$manager->setAttribute(Doctrine::ATTR_DEFAULT_TABLE_COLLATE, "utf8_unicode_ci");
		//$manager->setAttribute(Doctrine::ATTR_DEFAULT_TABLE_CHARSET, "utf8");

		$manager->setCollate('utf8_unicode_ci');
		$manager->setCharset('utf8');

		$this->initMemcached();
	}

	static public $cacheDriver = null;


	function initMemcached() {
		// do we want memcached?
		$manager = Doctrine_Manager::getInstance();

		if ($this->_config->memcache->use) {
			$servers = array(
				'host' => $this->_config->memcache->host,
				'port' => $this->_config->memcache->port,
				'persistent' => false,
				"compression" => true
			);

			DBPool::$cacheDriver = new Doctrine_Cache_Memcache(array(
					'servers' => $servers,
					'compression' => true
					)
			);
			$manager->setAttribute(Doctrine::ATTR_QUERY_CACHE, DBPool::$cacheDriver);
		
		$manager->setAttribute(Doctrine::ATTR_RESULT_CACHE, DBPool::$cacheDriver);
		$manager->setAttribute(Doctrine::ATTR_RESULT_CACHE_LIFESPAN, 3600);
		}
	}

	function connect () {
		$pdo = null;
		if ( @isset ($this->_config->db->user )) {
			$pdo = new PDO($this->_config->db->dsn, $this->_config->db->user, $this->_config->db->password, array(
				PDO::ATTR_PERSISTENT => false,
			));
		} else {
			$pdo = new PDO($this->_config->db->dsn);
		}
		$connection = Doctrine_Manager::connection($pdo);
		$connection->setCharset('utf8');
		$connection->setAttribute(Doctrine_Core::ATTR_USE_NATIVE_ENUM, true);
		return $connection;
	}

}

