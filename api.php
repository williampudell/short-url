<?php

	require_once("Restful.inc.php");

	class API extends RESTful {

		public $data = "";

		const DB_SERVER = "localhost";
		const DB_USER = "short-url";
		const DB_PASSWORD = "short-url";
		const DB = "short-url";

		private $db = NULL;

		public function __construct(){
			parent::__construct();
			$this->dbConnect();
		}

		//Database connection
		private function dbConnect()
		{
			$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
			if($this->db)
				mysql_select_db(self::DB,$this->db);
		}

        /*
         * Método público de acesso a api.
         * Este método chama a função desejada com base na query string
         *
         */
        public function processApi(){
            $func = strtolower(trim(str_replace("/","",$_REQUEST['method'])));
            if((int)method_exists($this,$func) > 0)
                $this->$func();
            else
                $this->response('',404);				// Se o método não existe ele retorna o erro 404
        }

        /*
         *	Função utilizada para retornar um aray no formato JSON
         */
        private function json($data){
            if(is_array($data)){
                return json_encode($data);
            }
        }

	}

	$api = new API;
	$api->processApi();
?>
