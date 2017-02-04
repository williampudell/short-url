<?php

	require_once("Restful.inc.php");

	class API extends RESTful {

		public $data = "";

		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "arun";
		const DB = "users";

		private $db = NULL;

		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->dbConnect();					// Initiate Database connection
		}

        /*
         * Public method for access api.
         * This method dynmically call the method based on the query string
         *
         */
        public function processApi(){
            $func = strtolower(trim(str_replace("/","",$_REQUEST['method'])));
            if((int)method_exists($this,$func) > 0)
                $this->$func();
            else
                $this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
        }

	}

	$api = new API;
	$api->processApi();
?>
