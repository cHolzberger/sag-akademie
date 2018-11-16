<?

class QueryDebuggerListener extends Doctrine_EventListener
{
    public function preStmtExecute(Doctrine_Event $event)
    {
        $q = $event->getQuery();
        $params = $event->getParams();

        while (sizeof($params) > 0) {
            $param = array_shift($params); 

            if (!is_numeric($param)) {
                $param = sprintf("'%s'", $param);
            }   

            $q = substr_replace($q, $param, strpos($q, '?'), 1); 
        }   
        qlog("Doctrine DB\nQuery ===>\n" . $q . "\n<===DBQUERY\n");
    }
}

if ( MosaikConfig::isDebug("doctrine-query") ) {
    $queryDbg = new QueryDebuggerListener();
    DBPool::$connection->addListener($queryDbg);
}