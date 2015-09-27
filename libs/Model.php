<?php

class Model {

		function __construct(){
			$this->db = new Database(dbTYPE, dbHOST, dbDATABASE, dbUSER, dbPASS);
		}
}

?>