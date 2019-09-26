<?php 
class Database 
    { 
    var $Host     = "10.20.1.188"; // Hostname of our MySQL server. 
	var $Database = "db_master"; // Logical database name on that server. 
    var $User     = "admin"; // User and Password for login. 
    var $Password = "p@ssw0r"; 
	
    /*var $Host     = "10.20.1.180"; // Hostname of our MySQL server. 
    var $Database = "db_master"; // Logical database name on that server. 
    var $User     = "root"; // User and Password for login. 
    var $Password = "tap123"; */
     
    var $Link_ID  = 0;  // Result of mysql_connect(). 
    var $Query_ID = 0;  // Result of most recent mysql_query(). 
    var $Record   = array();  // current mysql_fetch_array()-result. 
    var $Row;           // current row number. 
    var $LoginError = ""; 

    var $Errno    = 0;  // error state of query... 
    var $Error    = ""; 
    
	function __construct($db=array()) {
		/*$default = array(
			'host' => 'localhost',
			'user' => 'root',
			'pass' => '',
			'db' => 'test'
		);
		$db = array_merge($default,$db);
		$this->con=mysql_connect($db['host'],$db['user'],$db['pass'],true) or die ('Error connecting to MySQL');
		mysql_select_db($db['db'],$this->con) or die('Database '.$db['db'].' does not exist!');*/
		$this->connect();
	}
	function __destruct() {
		//mysql_close($this->Link_ID);
	}
	 
//------------------------------------------- 
//    Connects to the database 
//------------------------------------------- 
    function connect() 
        { 
        if( 0 == $this->Link_ID ) 
            $this->Link_ID=mysql_connect( $this->Host, $this->User, $this->Password ); 
        if( !$this->Link_ID ) 
            $this->halt( "Link-ID == false, connect failed" ); 
        if( !mysql_query( sprintf( "use %s", $this->Database ), $this->Link_ID ) ) 
            $this->halt( "cannot use database ".$this->Database ); 
        } // end function connect 

//------------------------------------------- 
//    Queries the database 
//------------------------------------------- 
    function query( $Query_String ) 
        { 
        //$this->connect(); 
        $this->Query_ID = mysql_query( $Query_String,$this->Link_ID ); 
        $this->Row = 0; 
        $this->Errno = mysql_errno(); 
        $this->Error = mysql_error(); 
        if( !$this->Query_ID ) 
            $this->halt( "Invalid SQL: ".$Query_String ); 
        return $this->Query_ID; 
        } // end function query 

//------------------------------------------- 
//    If error, halts the program 
//------------------------------------------- 
    function halt( $msg ) 
        { 
        printf( "</td></tr></table><b>Database error:</b> %s<br>n", $msg ); 
        printf( "<b>MySQL Error</b>: %s (%s)<br>n", $this->Errno, $this->Error ); 
        die( "Session halted." ); 
        } // end function halt 

//------------------------------------------- 
//    Retrieves the next record in a recordset 
//------------------------------------------- 
    function nextRecord() 
        { 
        @ $this->Record = mysql_fetch_array( $this->Query_ID ); 
        $this->Row += 1; 
        $this->Errno = mysql_errno(); 
        $this->Error = mysql_error(); 
        $stat = is_array( $this->Record ); 
        if( !$stat ) 
            { 
            @ mysql_free_result( $this->Query_ID ); 
            $this->Query_ID = 0; 
            } 
        return $stat; 
        } // end function nextRecord 

//------------------------------------------- 
//    Retrieves a single record 
//------------------------------------------- 
    function singleRecord() 
        { 
        $this->Record = mysql_fetch_array( $this->Query_ID ); 
        $stat = is_array( $this->Record ); 
        return $stat; 
        } // end function singleRecord 

//------------------------------------------- 
//    Returns the number of rows  in a recordset 
//------------------------------------------- 
    function numRows() 
        { 
        return mysql_num_rows( $this->Query_ID ); 
        } // end function numRows 
         
//------------------------------------------- 
//    Insert Queries 
//------------------------------------------- 
    function insert($table=null,$array_of_values=array()) {
		if ($table===null || empty($array_of_values) || !is_array($array_of_values)) return false;
		$fields=array(); $values=array();
		foreach ($array_of_values as $id => $value) {
			$fields[]=$id;
			if (is_array($value) && !empty($value[0])) $values[]=$value[0];
			else $values[]="'".mysql_real_escape_string($value,$this->Link_ID)."'";
		}
		$s = "INSERT INTO $table (".implode(',',$fields).') VALUES ('.implode(',',$values).')';
		if (mysql_query($s,$this->Link_ID)) return mysql_insert_id($this->Link_ID);
		return false;
	}
	
	
	
	} // end class Database 
	
	
?>