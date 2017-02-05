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
				mysql_query("UPDATE tb_urls u SET u.hits = (u.hits+1) WHERE u.id = {$param[1]}");
				$this->response($this->json($result),301);
			} else {
				$this->response('',404);
			}
		}

		private function users(){
			// Validação do tipo de request
			$request_method = trim($this->get_request_method());
			/*
			if($request_method != "POST" && $request_method != "GET"){
				$this->response('',404);
			}
			*/

			// Tratamento dos dados passados pela url
			$id = array_key_exists("id",$this->_request) ? $this->_request['id'] : "";
			$url = array_key_exists("url",$this->_request) ? $this->_request['url'] : "";
			$method = array_key_exists("method",$this->_request) ? explode("/",$this->_request['method']) : "";

			// Cadastro do usuário
			if($request_method == "POST" && empty($url) && empty($method)){

				$sql = mysql_query("SELECT u.id FROM tb_users u WHERE u.id='{$id}'");
				if(mysql_num_rows($sql) > 0){
					$this->response('',409);
				} else {
					$sql = "INSERT INTO tb_users (id, dt_insert, ip_insert, st_record) VALUES ('{$id}',NOW(), '{$_SERVER['REMOTE_ADDR']}',1);";
					mysql_query($sql);
					$dados['id'] = $id;
					$this->response($this->json($dados),201);
				}

			// Status por usuário
			} elseif($request_method == "GET" && !empty($method) && $method[2] == "stats"){

				$query = "SELECT SUM(u.hits) hits, COUNT(u.id) urlCount FROM tb_urls u WHERE u.user_id = '{$method[1]}';";
				$sql = mysql_query($query);
				if(mysql_num_rows($sql) > 0){
					$result = mysql_fetch_array($sql, MYSQL_ASSOC);
					// Validação de urls cadastradas
					if($result['urlCount'] > 0){
						$dados['hits'] = $result['hits'];
						$dados['urlCount'] = $result['urlCount'];
						$sql2 = mysql_query("SELECT u.id, u.hits, u.url, u.hash FROM tb_urls u WHERE u.user_id = '{$method[1]}' LIMIT 10");
						while($result2 = mysql_fetch_array($sql2, MYSQL_ASSOC)){

							if($this->port = "80"){
								$url = "http://".$this->server."/".$result2['hash'];
							} else {
								$url = "http://".$this->server.":".$this->port."/".$result2['hash'];
							}

							$dados['topUrls'][] = array(
								'id' => $result2['id'],
								'hits' => $result2['hits'],
								'url' => $result2['url'],
								'shortUrl' => $url
							);
						}
						$this->response($this->json($dados),200);
					} else { // caso não tenha url cadastrada retorna erro 404
						$this->response('',404);
					}
				} else {
					$this->response('',404);
				}



			// Cadastro de URL
			} elseif($request_method == "POST") {

				$sql = mysql_query("SELECT u.id FROM tb_users u WHERE u.id='{$id}'");
				if(mysql_num_rows($sql) == 0){
					$this->response('', 404);
				}

				// Utilizando a variavel i para forçar um timeout
				$i = 0;
				$validacao = false;
				while($validacao == false && $i <= 20){
					// Gerador de string encurtada
					$hash = $this->gerador();
					// Validação do Hash gerado para a URL encurtada
					$sql = mysql_query("SELECT u.id FROM tb_urls u WHERE u.hash = '{$hash}'");
					if(mysql_num_rows($sql) > 0){
						$validacao = false;
					} else {
						$validacao = true;
					}
					$i++;
				}

				if($i > 20){
					$this->response('',504);
				}

				$sql = mysql_query("SELECT u.id, u.url, u.hash, u.hits FROM tb_urls u WHERE u.url = '{$url}' AND u.user_id = '{$id}'");
				if(mysql_num_rows($sql) > 0){
					$result = mysql_fetch_array($sql, MYSQL_ASSOC);
					$dados['id'] = $result['id'];
					$dados['hits'] = $result['hits'];
					$dados['url'] = $result['url'];

					if($this->port = "80"){
						$url = "http://".$this->server."/".$result['hash'];
					} else {
						$url = "http://".$this->server.":".$this->port."/".$result['hash'];
					}

					$dados['shortUrl'] = $url;

					$this->response($this->json($dados),201);

				}

				$sql = mysql_query("SELECT u.id FROM tb_urls u WHERE u.url = '{$url}'");
				if(mysql_num_rows($sql) > 0){
					$this->response('',403);
				}

				$sql = "INSERT INTO tb_urls (url, hash, user_id) VALUES ('{$url}','{$hash}','{$id}');";
				mysql_query($sql);
				$dados['id'] = mysql_insert_id();
				$dados['hits'] = 0;
				$dados['url'] = $url;
				if($this->port = "80"){
					$url = "http://".$this->server."/".$hash;
				} else {
					$url = "http://".$this->server.":".$this->port."/".$hash;
				}

				$dados['shortUrl'] = $url;

				$this->response($this->json($dados),201);

			} else if($request_method == "DELETE"){

				$user_id = $method[2];

				mysql_query("DELETE FROM tb_users u WHERE u.id = '{$user_id}'");
				$this->response('',200);

			}

		}

		private function stats(){

			$method = array_key_exists("method", $this->_request) ? explode("/",$this->_request['method']) : "";

			$tam = count($method);

			if($tam > 1 && (int)$method[1]){

				$sql = mysql_query("SELECT u.id, u.hits, u.url, u.hash FROM tb_urls u WHERE u.id = {$method[1]}");
				while($result = mysql_fetch_array($sql, MYSQL_ASSOC)){

					if($this->port = "80"){
						$url = "http://".$this->server."/".$result['hash'];
					} else {
						$url = "http://".$this->server.":".$this->port."/".$result['hash'];
					}

					$dados['topUrls'][] = array(
						'id' => $result['id'],
						'hits' => $result['hits'],
						'url' => $result['url'],
						'shortUrl' => $url
					);
				}

			} else {

				$sql = mysql_query("SELECT SUM(u.hits) hits, COUNT(u.id) urlCount FROM tb_urls u");
				if(mysql_num_rows($sql) > 0){
					$result = mysql_fetch_array($sql, MYSQL_ASSOC);
					$dados['hits'] = $result['hits'];
					$dados['urlCount'] = $result['urlCount'];
					$sql2 = mysql_query("SELECT u.id, u.hits, u.url, u.hash FROM tb_urls u LIMIT 10");
					while($result2 = mysql_fetch_array($sql2, MYSQL_ASSOC)){

						if($this->port = "80"){
							$url = "http://".$this->server."/".$result2['hash'];
						} else {
							$url = "http://".$this->server.":".$this->port."/".$result2['hash'];
						}

						$dados['topUrls'][] = array(
							'id' => $result2['id'],
							'hits' => $result2['hits'],
							'url' => $result2['url'],
							'shortUrl' => $url
						);
					}
				}

			}

			$this->response($this->json($dados),200);
		}

        /*
         *	Função utilizada para retornar um aray no formato JSON
         */
        private function json($data){
            if(is_array($data)){
                return json_encode($data);
            }
        }

		// Gerador de string encurtada
		private function gerador(){

			return substr(md5(microtime()),rand(0,26),8);

		}

	}

	$api = new API;
	$api->processApi();
?>
