<?php
class DB_Connect {
 
    // constructor
    function __construct() {
         
    }
 
    // destructor
    function __destruct() {
        // $this->close();
    }
 
    // Connecting to database
    public function connect() {
        /**
         * Database config variables
         */
         //Local
		 define("DB_HOST", "localhost");
		 define("DB_USER", "root");
         define("DB_PASSWORD", "");
         define("DB_DATABASE", "brand_hub");
 
        // connecting to mysql
        //$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
        // selecting database
        //mysql_select_db(DB_DATABASE);

		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
		// return database handler
		$GLOBALS['conn'] = $conn;
 
        // return database handler
        return $GLOBALS['conn'];
    }
	

 
    // Closing database connection
    public function close() {
        mysql_close();
    }
 
}
?>