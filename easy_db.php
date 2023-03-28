<?php
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
    private $error;
    private $errno;
     
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

    // return raw mysqli object
    public function get_mysqli_con(){return $this->db;}

    // query
    public function query($query) {
        $this->query = $query;
        $this->result = $this->db->query($this->query);
        if (!$this->result) {
            $this->error = $this->db->error;
            $this->errno = $this->db->errno;
            return [$this->error, $this->errno];
        }
        return true;
    }

    public function fetch_object($query) {
        $this->query = $query;
        $this->result = $this->db->query($this->query);
        if (!$this->result) {
            $this->error = $this->db->error;
            $this->errno = $this->db->errno;
            return [$this->error, $this->errno];
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

    public function get_last_insert_id(){return $this->db->insert_id;}
    
}
?>