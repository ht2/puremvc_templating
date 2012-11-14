<?php
class MySQL
{
    
    public $host;
    public $database;
    public $user;
    public $pass;
    
    public $mysqli;
    public $result;
    
    public $site_root;
    public $base_dir;
    public $full_url;

    public function __construct()
    {
        $this->site_root = $_SERVER['SERVER_NAME'];
        
        switch( $_SERVER['SERVER_NAME'] )
        {
            default:
            case "localhost":
                $this->host 	= "localhost";
                $this->database = "duke_survey";
                $this->user 	= "root";
                $this->pass 	= "";
                $this->site_root    =   "http://localhost";
                $this->base_dir     =   "/PureMVC";
                $this->debug = true;
            break;
        }

        $this->full_url     =   $this->site_root.$this->base_dir;
        
        $this->mysqli = new mysqli( $this->host, $this->user, $this->pass, $this->database );
        $this->checkConnectError();
        
    }
    
    private function checkConnectError(){
        if ($this->mysqli->connect_errno) {
            $this->error("Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error );
        }
    }
    
    //Runs a MYSQL query
    //Returns the last inserted id
    public function query($query)
    {
        $this->result = $this->mysqli->query($query);
        if( !$this->result )
            $this->error( $this->mysqli->error);
        return $this->last_id();
    }	
    
    //Returns the native MySQL result
    public function result()
    {
        return $this->result;
    }
    
    //Turns MySQL result into an array
    public function results()
    {
        $results = array();
        
        if( $this->result === false ) return array();
        
        while ($obj = $this->result->fetch_object())
            array_push( $results, $obj );
        
        foreach($results as $result) {
            $result->unixtimestamp = 0;
            if (isset($result->timestamp)) {
                $result->unixtimestamp = strtotime($result->timestamp);
            }
        }
        return $results;
    }	
    
    //Returns first value in array
    //Returns false if no values exist
    public function singleResult(){
        $results = $this->results();
        if( sizeof($results)>0 )
            return $results[0];		
        else
            return false;
    }

    //Returns the number of rows in the last query
    public function num_rows()
    {
        return mysqli_num_rows( $this->result ); 
    }

    //Returns the last inserted ID
    public function last_id()
    {
            return $this->mysqli->insert_id;
    }	

    //Close the mysql connection
    public function close()
    {
        $this->mysqli->close();
    }

    //Echos out the mysqli error via var_dump
    public function error( $error )
    {
        var_dump($error);
        exit();
    }

    // HELPERS
    //Select from table where active=1
    public function select( $table, $where = NULL )
    {
        $query = ( is_null( $where ) ) ? "SELECT * FROM $table WHERE active='1'" : "SELECT * FROM $table WHERE active='1' and $where";
        $this->query( $query );
    }

    //Select all records
    public function select_all( $table, $where = NULL )
    {
            $query = ( is_null( $where ) ) ? "SELECT * FROM $table WHERE 1" : "SELECT * FROM $table WHERE $where";
            $this->query( $query );
    }

    //Do an insert 
    //Returns the last inserted ID
    public function insert( $table, $data ) 
    {
        foreach( $data as $field => $value ) 
        {
            $fields[] = '`' . $field . '`';
            $values[] = "'" . $this->safe($value) . "'";
        }
        $field_list = join( ',', $fields );
        $value_list = join( ', ', $values );
        $query = "INSERT INTO `" . $table . "` (" . $field_list . ") VALUES (" . $value_list . ")";
        
        return $this->query( $query );
    }

    //Update an existing record(s)
    //Returns last inserted ID
    public function update($table, $data, $id_field, $id_value) 
    {
        foreach ($data as $field => $value) $fields[] = sprintf("`%s` = '%s'", $field, $this->safe($value));
        $field_list = join(',', $fields);
        $query = sprintf("UPDATE `%s` SET %s WHERE `%s` = %s", $table, $field_list, $id_field, intval($id_value));
        $this->query( $query );
    }
    
    //Removes the data from the database
    public function destroy( $table, $id_field, $id_value)
    {
        if( $where != NULL && $where!="1" )
        {
            $query = "DELETE FROM  $table WHERE $id_field='$id_value'";
            $this->query( $query );
        }
    }
    
    //Turns off the record specified
    public function delete($table, $id_field, $id_value) 
    {
        $this->update($table, array('active'=>0), $id_field, $id_value);
    }
    
    //Does a standard mysqli query
    public function self_query( $query )
    {
        return $this->mysqli->query( $query );
    }
    
    //Escapes any nasty stuff
    public function safe($value)
    {
        return $this->mysqli->real_escape_string($value);
    }
	
    public function array_in($id_array) {
        return "(".implode(',', $id_array).")";
    }
    
}
?>