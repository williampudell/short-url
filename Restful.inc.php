<?php

	class RESTful {

		public $_allow = array();
		public $_content_type = "application/json";
		public $_request = array();

		private $_method = "";
		private $_code = 200;

		public function __construct(){
			$this->inputs();
		}
	}
?>
