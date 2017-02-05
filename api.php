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
				// removo os espaços, transformo em minusculo
	            $func = explode("/",strtolower(trim($_REQUEST['method'])))[0];
				// Verifico se existe a função desejada
	            if((int)method_exists($this,$func) > 0){
					$this->$func();
	            } else { // Se a função não existe ele retorna o erro 404
					$this->response('',404);
				}
			}
        }

		/*
		 *	Função que retorna a URL completa baseando-se no ID passado
		 *
		 */
		private function urls(){
			// Validação do tipo de request
			if($this->get_request_method() != "GET"){
				$this->response('',404);
			}

			// Tratamento dos dados passados pela url
			$param = explode("/",$this->_request['method']);
			$qtd = count($param);

			// Se só houver um parametro na url retorna 404
			if($qtd == 1){
				$this->response('',404);
			}

			$sql = mysql_query("SELECT u.url FROM tb_urls u WHERE u.id = {$param[1]}");
			if(mysql_num_rows($sql)>0){
				$result = mysql_fetch_array($sql, MYSQL_ASSOC);
				$this->response($this->json($response),301);
			} else {
				$this->response('',404);
			}
		}

		private function users(){
			// Validação do tipo de request
			if($this->get_request_method() != "POST"){
				$this->response('',404);
			}

			// Tratamento dos dados passados pela url
			$id = $this->_request['id'];
			$urls = $this->_request['urls'];

			if(empty($urls)){

				$sql = mysql_query("SELECT u.id FROM tb_users u WHERE u.id='{$id}'");
				if(mysql_num_rows($sql) > 0){
					$this->response('',409);
				} else {
					$sql = "INSERT INTO tb_users (id, dt_insert, ip_insert, st_record) VALUES ('{$id}',NOW(), '{$_SERVER['REMOTE_ADDR']}',1);";
					mysql_query($sql);
					$dados['id'] = $id;
					$this->response($this->json($dados),201);
				}

			} else {

				$sql = mysql_query("SELECT u.id FROM tb_users u WHERE u.id='{$id}'");
				if(mysql_num_rows($sql) == 0){
					$this->response('', 404);
				}

				// Gerador de string encurtada
				$rand = substr(md5(microtime()),rand(0,26),9);


				//$this->respose($this->json($dados),201);

			}

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
