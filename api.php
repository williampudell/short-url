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
			if(array_key_exists("method",$_REQUEST)){
				// removo os espaços, transformo em minusculo e explodo a chamada para execução da API
	            $func = explode("/",strtolower(trim($_REQUEST['method'])));
				// Conto quantos parametros existem na chamada
				$param = count($func);
				// Verifico se existe a função desejada
	            if((int)method_exists($this,$func[0]) > 0){
					// Se houver mais de um parametro na chamada eu passo para a função
					if($param > 1){
						$this->$func[0]($func[1]);
					} else { // Se não houver, apenas chamo a função
						$this->$func[0]();
					}
	            } else { // Se a função não existe ele retorna o erro 404
					$this->response('',404);
				}
			} else {
				// Se não for passado nenhum parametro retorna o erro 404
				$this->response('',404);
			}
        }

		/*
		 *	Função que retorna a URL completa baseando-se no ID passado
		 *	@param url_id: Id da url encurtada que foi cadastrada no banco de dados
		 *
		 */
		private function urls($url_id){

			$this->response('',200);
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
