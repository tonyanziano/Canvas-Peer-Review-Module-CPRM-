<?php
	class Db extends MySQLi {
		private static $instance = NULL;
		
		private function __construct($host, $user, $pw, $db, $port) {
			parent::__construct($host, $user, $pw, $db, $port);
		}
		
		public static function getInstance() {
			if (self::$instance == NULL) {
				self::$instance = new self(getenv('OPENSHIFT_MYSQL_DB_HOST'), 
					getenv('OPENSHIFT_MYSQL_DB_USERNAME'), 
					getenv('OPENSHIFT_MYSQL_DB_PASSWORD'), 
					getenv('OPENSHIFT_APP_NAME'), 
					getenv('OPENSHIFT_MYSQL_DB_PORT')) or die("Error: " . mysqli_error(self::$instance));
			}
			
			return self::$instance;
		}
	}
