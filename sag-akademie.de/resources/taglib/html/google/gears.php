<script type="text/javascript" src="/resources/scripts/google/gears_init.js"></script>
<script type="text/javascript">
	/* create a managed store */
		var STORE_NAME="sagadminstore";
		
		if (window.google && google.gears) {
			try {
				/* SQLite code */
				
				window.mStore = google.gears.factory.create('beta.database');
				
				if (window.mStore) {
					window.mStore.open('sag-akademie');
			    }

				/* google local server */
				var localServer = google.gears.factory.create('beta.localserver');
				window.mLocalserver = localServer;

				var store = localServer.openManagedStore( STORE_NAME );
				window.managedStore = store;
				
				
				if (!store) {
					store = localServer.createManagedStore( STORE_NAME );
					store.enabled = true;
					store.manifestUrl = '/_gearsmanifest.php';
				}
				store.checkForUpdate();
				
				window.title = window.title + " [ LV: " + store.version +"]";
			} catch (ex) {
				alert("Google - Gears Fehler:\n" + ex.message);
			}
			
		}
		
		<? if( isset($_POST['gearsUpdate']) ): ?>
			store = localServer.createManagedStore(STORE_NAME);
			store.manifestUrl = '/_gearsmanifest.php';
			store.checkForUpdate();
		<? endif; ?>
</script>