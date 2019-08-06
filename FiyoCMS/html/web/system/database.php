<?php 
/**
* version   2.5
* package   Fiyo CMS
* copyright Copyright (C) 2012 Fiyo CMS.
* license   GNU/GPL, see LICENSE.txt
**/

/*
* Define database variables
*/ 
define('FDBUser', $DBUser);
define('FDBPass', $DBPass);
define('FDBHost', $DBHost);
define('FDBName', $DBName);
define('FDBPrefix', $DBPrefix);

class FQuery {
    /*
     * Edit the following variables
     */ 
    private $db_host = FDBHost;       // Database Host
    private $db_user = FDBUser;       // Username
    private $db_pass = FDBPass;       // Password
    private $db_name = FDBName;       // Database
    /*
     * End edit
     */

    public $db   = false;              // Cek untuk melihat apakah sambungan aktif
    public $result = null;              // Cek untuk melihat apakah sambungan aktif

    public function connect()
    {
		static $conn = false;
		if(!$conn) { 
			try{
				$options = array(
				PDO::ATTR_PERSISTENT    => false,
				PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION);
				$this -> db = $conn = new PDO("mysql:host=$this->db_host;dbname=$this->db_name;charset=utf8",$this->db_user, $this->db_pass, $options);
			}
			catch(PDOException $e){				
                alert('error','Unable to connect database!',true,true);
			}
		} else $this -> db = $conn;		
    }

    /*
    * Mengolah seluruh query
	* Membuat sebuah definisi singkat query
    */
	public function query($query, $fetch = false, $error = true){
		static $cons = false;		
		try{
			$result = $this->connect();       
			$result = @$this->db->prepare($query);
			$result ->execute();
			if($fetch)
				return $result->fetchAll(PDO::FETCH_ASSOC);
			else	
				return $result;
		}
		catch(PDOException $e){
			if(!$cons AND $error) {
				return false;			
				$cons = true;
			}
		}
	}
	
    /*
    * Cek apakah tabel setting ada
	* Sebelum melakukan query lanjutan
    */
    public function tableExists($table)
    {
		$result = $this->query('SHOW TABLES FROM '.$this->db_name.' LIKE "'.$table.'"');
        if($result)
        {
            if(count($result))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    /*
    * Query select sederhana
    */	
	public function select($table, $rows = '*', $where = null, $order = null, $limit = null){	
		$sql = 'SELECT '.$rows.' FROM '.$table;
		
        if($where != null)
            $sql .= ' WHERE '.$where;
        if($order != null)
            $sql .= ' ORDER BY '.$order;	
        if($limit != null)
            $sql .= ' LIMIT '.$limit;	
		
		static $cons = false;
		try{
			$result = $this->connect();       
			$result = @$this->db->prepare($sql);
			$result ->execute();
			return $result->fetchAll(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e){if(!$cons) {				
			return false;
			$cons = true;
			}
		}
	}
	
	
	
    /*
    * Insert values into the table
    * Required: table (the name of the table)
    *           values (the values to be inserted)
    * Optional: rows (if values don't match the number of rows)
    */
	
    public function insert($table,$values,$rows = null)
    {
        $insert = 'INSERT INTO '.$table;
        if($rows != null)
        {
            $insert .= ' ('.$rows.')';
        }

        for($i = 0; $i < count($values); $i++)
        {
            if(is_string($values[$i]))
                $values[$i] = '"'.$values[$i].'"';
        }
        $values = implode(',',$values);
        $insert .= ' VALUES ('.$values.')';
			
		static $cons = false;
		try{
			$result = $this->connect();       
			$result = $this->db->prepare($insert);
			$query = $result ->execute();
        }
		catch(PDOException $e){
			if(!$cons) {				
				return false;		
			    $cons = true;
			}
		}
        
		if($query)
        {
            return true;
        } else
        {
            return false;
        }
    }

    /*
    * Deletes table or records where condition is true
    * Required: table (the name of the table)
    * Optional: where (condition [column =  value])
    */
    public function delete($table,$where = null)
    {
        if($where == null)
            {
            $delete = 'DELETE FROM '.$table;
        }
        else
        {
            $delete = 'DELETE FROM '.$table.' WHERE '.$where;
        }
			
		static $cons = false;
		try{
			$result = $this->connect();       
			$result = $this->db->prepare($delete);
			$query = $result ->execute();
        }
		catch(PDOException $e){
			if(!$cons) {				
                return false;
			    $cons = true;
			}
		}
        
        if(isset($query))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * Updates the database with the values sent
     * Required: table (the name of the table to be updated
     *           rows (the rows/values in a key/value array
     *           where (the row/condition in an array (row,condition) )
     */
    public function update($table,$rows,$where)
    {
        $update = 'UPDATE '.$table.' SET ';
        $keys = array_keys($rows);
		
        for($i = 0; $i < count($rows); $i++){
            if(is_string($rows[$keys[$i]]) AND $rows[$keys[$i]] !== '+hits')
            {
                $update .= $keys[$i].'="'.$rows[$keys[$i]].'"';
            }
            else
            {
				if($rows[$keys[$i]] == '+hits') $rows[$keys[$i]] = $keys[$i] . '+'. 1;
                 $update .= $keys[$i].'='.$rows[$keys[$i]];
            }

            // Parse to add commas
            if($i != count($rows)-1)
            {
                $update .= ',';
            }
        }
			
        $update .= ' WHERE '.$where;			
		static $cons = false;
		try{
			$result = $this->connect();       
			$result = $this->db->prepare($update);
			$query = $result ->execute();
        }
		catch(PDOException $e){
			if(!$cons) {				
                return false;
			    $cons = true;
			}
		}
		
        if(isset($query))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
//auto database query	$db = new FQuery();  