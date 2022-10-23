<?php
// database manager class for easy_db with mysqli

/**usage documentation
 * 
 * $db = new easy_db('localhost', 'root', 'password', 'database');
 * 
 */

class easy_db {
    private $db;
    private $host;
    private $user;
    private $pass;
    private $name;
    private $port;
    private $charset;
    private $result;
    private $query;
    private $stmt;
    private $stmt_affected_rows;
    private $stmt_insert_id;
    private $stmt_num_rows;
    private $stmt_field_count;
    private $stmt_store_result;
    private $stmt_bind_result;
    private $stmt_bind_param;
    private $stmt_fetch;
    private $stmt_fetch_array;
    private $stmt_fetch_assoc;
    private $stmt_fetch_row;
    private $stmt_fetch_field;
    private $stmt_fetch_fields;
    private $stmt_fetch_field_direct;
    private $stmt_fetch_all;
    private $stmt_data_seek;
    private $stmt_free_result;
    private $stmt_reset;
    private $stmt_close;
    private $stmt_get_result;
     
    // constructor
    public function __construct($host, $user, $pass, $name, $port, $charset) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->name = $name;
        $this->port = $port;
        $this->charset = $charset;
        $this->db = new mysqli($this->host, $this->user, $this->pass, $this->name, $this->port);
        if ($this->db->connect_errno) {
            $this->error = $this->db->connect_error;
            $this->errno = $this->db->connect_errno;
            return false;
        }
        $this->db->set_charset($this->charset);
        return true;
    }

    // destructor
    public function __destruct() {
        $this->db->close();
    }

    // query
    public function query($query) {
        $this->query = $query;
        $this->result = $this->db->query($this->query);
        if (!$this->result) {
            $this->error = $this->db->error;
            $this->errno = $this->db->errno;
            return false;
        }
        return true;
    }

    public function fetch_object($query) {
        $this->query = $query;
        $this->result = $this->db->query($this->query);
        if (!$this->result) {
            $this->error = $this->db->error;
            $this->errno = $this->db->errno;
            return false;
        }
        return $this->result;
    }

    // fetch_array
    public function fetch_array($query) {
        $this->query = $query;
        $this->result = $this->db->query($this->query);
        if (!$this->result) {
            $this->error = $this->db->error;
            $this->errno = $this->db->errno;
            return false;
        }
        return $this->result->fetch_array();
    }

    // fetch_assoc
    public function fetch_assoc($query) {
        $this->query = $query;
        $this->result = $this->db->query($this->query);
        if (!$this->result) {
            $this->error = $this->db->error;
            $this->errno = $this->db->errno;
            return false;
        }
        return $this->result->fetch_assoc();
    }

    // num_rows
    public function num_rows($query) {
        $this->query = $query;
        $this->result = $this->db->query($this->query);
        if (!$this->result) {
            $this->error = $this->db->error;
            $this->errno = $this->db->errno;
            return false;
        }
        return $this->result->num_rows;
    }
    
}


?>