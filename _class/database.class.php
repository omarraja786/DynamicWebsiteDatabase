<?php

class database extends PDO {
	private static $dbInstance;

	private $debug 		= false;
	private	$dsn   		= ''; //database dsn
	private	$user     	= ''; //database login name
	private	$pass     	= ''; //database login password
	
	private $sql;
	private $parameters;
	private $num_queries = 0;

	public function __construct($dsn=null, $user=null, $pass=null, $debug=false){
		$this->debug = $debug;
		
		// error catching if not passed in connection details
		if(is_null($dsn) || is_null($user) || is_null($pass)){
			$this->oops('Database information must be passed in when the object is first created');
			exit;
		}
		
		$this->dsn			= $dsn;
		$this->user			= $user;
		$this->pass			= $pass;
	}

	public static function obtain($dsn=null, $user=null, $pass=null, $debug=false){
		if(!self::$dbInstance){
			self::$dbInstance = new database($dsn, $user, $pass, $debug);
		}
		return self::$dbInstance; 
	}

	public function connect(){
		$opt = array(
			PDO::ATTR_ERRMODE 				=> PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_INIT_COMMAND 	=> "SET NAMES 'UTF8'",
			PDO::ATTR_EMULATE_PREPARES 		=> false
		);
		
		try {
			// connects to the database on the parent PDO class
			self::$dbInstance = parent::__construct($this->dsn, $this->user, $this->pass, $opt);
			
			return false;
		} catch (PDOException $e){
			// if connection fails, return a false response
			return false;
		}
	}
	
	public function close(){
		self::$dbInstance = null;
	}


	/*
	@methodName escape
	@methodDescription Escapes a string for database security
	@param 	string 		$string 				The string we want to escape
	*/

	public function escape($string){
		if(get_magic_quotes_runtime()) $string = stripslashes($string);
		$string = trim($string);
		$string = $this->quote($string);
		$string = trim($string);

		return substr($string, 1, (strlen($string) - 2));
	}


	/*
	@methodName query_first
	@methodDescription Returns the first result from a raw sql query
	@param 	string 		$sql 				The sql statement we want to execute
	*/
	
	public function query_first($sql){
		return $this->query($sql, array(), 'select', true);
	}


	/*
	@methodName fetch_array
	@methodDescription Returns all results from a sql query
	@param 	string 		$sql 				The sql statement we want to execute
	*/
	
	public function fetch_array($sql){
		return $this->query($sql, array(), 'select', false);
	}


	/*
	@methodName total_rows
	@methodDescription Returns the total number of rows a database table has
	@param 	string 		$table 				The database table we're looking at
	@param 	string 		$where 				A where statement to narrow down the count
	@param 	boolean		$tableIsStatement 	Used to indicate if $table is a full sql statement, or not
	*/

	public function total_rows($table, $where='', $tableIsStatement=false){
		if($tableIsStatement){
			if(substr(strtoupper($table), 0, 30) == 'SELECT COUNT(*) AS TOTAL_ROWS ' || substr(strtoupper($table), 0, 43) == 'SELECT SQL_NO_CACHE COUNT(*) AS TOTAL_ROWS '){
				$sql = $table;
			} else {
				$sql = 'SELECT * FROM ('.$table.') t';
			}
		} else {
			$sql = 'SELECT * FROM ';
			$sql .= (substr($table, 0 , 1) == '(' || strpos($table, ' ') !== false) ? $table : '`'.$table.'`';
			$sql.= ($where !== '') ? ' WHERE '.$where : '';
		}

		return $this->query($sql, array(), 'total', false);
	}


	/*
	@methodName update
	@methodDescription Updates rows within a database table with new data
	@param 	string 		$table 				The database table we're wanting to update
	@param 	array 		$data 				Uses field value pairs - some helper values exist
	@param 	string 		$where 				A where statement to narrow what we want to update
	*/

	public function update($table, $data, $where=''){
		$parameters = array();

		$i = 0;
		$sql = "UPDATE `".$table."` SET ";
		if(is_array($data)){
			foreach ($data as $key => $val){
				$sql .= ($i > 0) ? ', ' : '';
				if(strtolower($val) == 'null'){
					$sql.= "`$key` = NULL";
				} else if(strtolower($val) == 'now()'){
					$sql.= "`$key` = NOW()";
				} else if(preg_match("/^decrement\((\-?\d+)\)$/i", $val, $m)){
					$sql.= "`$key` = `$key` - ".$m[1]; 
				} else if(preg_match("/^increment\((\-?\d+)\)$/i", $val, $m)){
					$sql.= "`$key` = `$key` + ".$m[1]; 
				} else {
					$sql .= $key.'=:'.$key;
					$parameters[':'.$key] = $val;
				}

				$i++;
			}
		} else {
			$sql .= $this->escape($data);
		}
		$sql = rtrim($sql, ', ');
		$sql.= ($where !== '') ? ' WHERE '.$where : '';
		
		return $this->query($sql, $parameters, 'update', false);
	}


	/*
	@methodName delete
	@methodDescription Deletes rows within a database table
	@param 	string 		$table 				The database table we're wanting to delete from
	@param 	string 		$where 				A where statement to narrow what we want to delete
	*/

	public function delete($table, $where=''){
		$sql = 'DELETE FROM `'.$table.'` WHERE '.$where;
		$sql.= ($where !== '') ? ' WHERE '.$where : '';

		return $this->query($sql, array(), 'delete', false);
	}


	/*
	@methodName insert
	@methodDescription Inserts new rows to a given database table
	@param 	string 		$table 				The database table we're wanting to insert to
	@param 	array 		$data 				Uses field value pairs - some helper values exist
	*/

	public function insert($table, $data=array()){
		$parameters = array();

		$sql = 'INSERT INTO `'.$table.'` ';
		$cols = '';
		$vals = '';
		foreach($data as $key => $val){
			if(!is_array($val)){
				if(strtolower($val) == 'null'){
					$cols .= '`'.$key.'`, ';
					$vals .= 'NULL, ';
				} else if(strtolower($val) == 'now()'){
					$cols .= '`'.$key.'`, ';
					$vals .= 'NULL, ';
				} else {
					$cols .= '`'.$key.'`, ';
					$vals .= ':'.$key.', ';
					$parameters[':'.$key] = $val;
				}
			}
		}
		$sql .= '('. rtrim($cols, ', ') .') VALUES ('. rtrim($vals, ', ') .');';
		
		return $this->query($sql, $parameters, 'insert', false);
	}


	/*
	@methodName table_exists
	@methodDescription Checks to see if a database table exists
	@param 	string 		$table 				The database table we're wanting to check
	*/
	
	public function table_exists($table){
		return (($this->query('SHOW TABLES LIKE "'.$table.'"', array(), 'show') > 0) ? true : false);
	}

	
	public function free_result($pdo){
		return (($pdo->closeCursor()) ? true : false);
	}
	

	public function get_num_queries(){
		return $this->num_queries;
	}


	/*
	@methodName oops
	@methodDescription Outputs error messages
	@param 	string 		$msg 				The error message
	@param 	string 		$sql 				A corresponding sql statement
	*/
	
	private function oops($msg='', $sql=''){
		// if not in debug mode, stop here
		if(!$this->debug) return;
		
		if(!empty($_SERVER['REQUEST_URI'])){
			$msg .= '<br /><strong>Script:</strong> '.$_SERVER['REQUEST_URI'];
		}

		if(!empty($_SERVER['HTTP_REFERER'])){
			$msg .= '<br /><strong>Referer:</strong> '.$_SERVER['HTTP_REFERER'];
		}

		if(!empty($sql)){
			$msg .= '<br /><strong>Query:</strong> '.$sql;
		}
		
		die($msg);
	}

	
	public function query($sql, $parameters=array(), $type='', $single=false){
		$sql = trim($sql);
		if(!is_array($parameters)){
			$parameters = array();
		}
		
		$this->sql = $sql;
		
		try	{
			$preparedStatement = $this->prepare($this->sql);
			foreach($parameters as $key => $val){
				$preparedStatement->bindParam($key, $val);
			}

			$result = null;
			if($preparedStatement->execute($parameters) !== false){
				++$this->num_queries;
				
				if(in_array($type, array('update', 'delete', 'insert', 'show'))){
					if($type == 'insert'){
						// as we're inserting, we'll be returning the row number
						$result = $this->lastInsertId();
					} else {
						// we'll be returning the number of rows affected
						$result = $preparedStatement->rowCount();
					}
				} else if(in_array($type, array('select', 'total'))){
					$optimisedTotal = false;

					if($type == 'total' && (substr(strtoupper($sql), 0, 30) == 'SELECT COUNT(*) AS TOTAL_ROWS ' || substr(strtoupper($sql), 0, 43) == 'SELECT SQL_NO_CACHE COUNT(*) AS TOTAL_ROWS ')){
						$optimisedTotal = true;
						$single = true;
					}

					if($type == 'total' && $optimisedTotal == false){
						// we'll be returning the number of rows affected
						$result = $preparedStatement->rowCount();
					} else {
						$preparedStatement->setFetchMode(PDO::FETCH_ASSOC);
						if($single){
							// we'll be returning the database row
							$result = $preparedStatement->fetch();
						} else {
							// we'll be returning all matching database rows
							$result = $preparedStatement->fetchAll();
						}

						if($optimisedTotal && count($result) == 1 && array_key_exists('total_rows', $result)){
							// we'll be returning the number of rows affected, in an optimised way
							$result = $result['total_rows'];
						}
					}
				} else {
					$result = null;
				}
			}

			$this->free_result($preparedStatement);
			
			return $result;
		} catch(PDOException $e){
			$this->oops($e->getMessage(), $this->sql);	
		}
	}


	public function start_transaction(){
		$this->beginTransaction();
	}

	
	public function end_transaction(){
		$this->rollback();
	}

}