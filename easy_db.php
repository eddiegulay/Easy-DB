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

    // prepare
    public function prepare($query) {
        $this->query = $query;
        $this->stmt = $this->db->prepare($this->query);
        if (!$this->stmt) {
            $this->stmt_error = $this->db->error;
            $this->stmt_errno = $this->db->errno;
            return false;
        }
        return true;
    }

    // bind_param
    public function bind_param($types, $vars) {
        $this->stmt_bind_param = $this->stmt->bind_param($types, $vars);
        if (!$this->stmt_bind_param) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // execute
    public function execute() {
        $this->stmt_execute = $this->stmt->execute();
        if (!$this->stmt_execute) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // store_result
    public function store_result() {
        $this->stmt_store_result = $this->stmt->store_result();
        if (!$this->stmt_store_result) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // bind_result
    public function bind_result($vars) {
        $this->stmt_bind_result = $this->stmt->bind_result($vars);
        if (!$this->stmt_bind_result) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // fetch
    public function fetch() {
        $this->stmt_fetch = $this->stmt->fetch();
        if (!$this->stmt_fetch) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // fetch_array
    public function fetch_array() {
        $this->stmt_fetch_array = $this->stmt->fetch_array();
        if (!$this->stmt_fetch_array) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // fetch_assoc
    public function fetch_assoc() {
        $this->stmt_fetch_assoc = $this->stmt->fetch_assoc();
        if (!$this->stmt_fetch_assoc) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // fetch_row
    public function fetch_row() {
        $this->stmt_fetch_row = $this->stmt->fetch_row();
        if (!$this->stmt_fetch_row) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // fetch_field
    public function fetch_field() {
        $this->stmt_fetch_field = $this->stmt->fetch_field();
        if (!$this->stmt_fetch_field) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // fetch_fields
    public function fetch_fields() {
        $this->stmt_fetch_fields = $this->stmt->fetch_fields();
        if (!$this->stmt_fetch_fields) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // fetch_field_direct
    public function fetch_field_direct($field_nr) {
        $this->stmt_fetch_field_direct = $this->stmt->fetch_field_direct($field_nr);
        if (!$this->stmt_fetch_field_direct) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // fetch_all
    public function fetch_all() {
        $this->stmt_fetch_all = $this->stmt->fetch_all();
        if (!$this->stmt_fetch_all) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // fetch_object
    public function fetch_object() {
        $this->stmt_fetch_object = $this->stmt->fetch_object();
        if (!$this->stmt_fetch_object) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // data_seek
    public function data_seek($offset) {
        $this->stmt_data_seek = $this->stmt->data_seek($offset);
        if (!$this->stmt_data_seek) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // num_rows
    public function num_rows() {
        $this->stmt_num_rows = $this->stmt->num_rows;
        if (!$this->stmt_num_rows) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // num_fields
    public function num_fields() {
        $this->stmt_num_fields = $this->stmt->num_fields;
        if (!$this->stmt_num_fields) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // field_count
    public function field_count() {
        $this->stmt_field_count = $this->stmt->field_count;
        if (!$this->stmt_field_count) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // affected_rows
    public function affected_rows() {
        $this->stmt_affected_rows = $this->stmt->affected_rows;
        if (!$this->stmt_affected_rows) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // insert_id
    public function insert_id() {
        $this->stmt_insert_id = $this->stmt->insert_id;
        if (!$this->stmt_insert_id) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // get_warnings
    public function get_warnings() {
        $this->stmt_get_warnings = $this->stmt->get_warnings();
        if (!$this->stmt_get_warnings) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // get_result
    public function get_result() {
        $this->stmt_get_result = $this->stmt->get_result();
        if (!$this->stmt_get_result) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // free_result
    public function free_result() {
        $this->stmt_free_result = $this->stmt->free_result();
        if (!$this->stmt_free_result) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // close
    public function close() {
        $this->stmt_close = $this->stmt->close();
        if (!$this->stmt_close) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

    // reset
    public function reset() {
        $this->stmt_reset = $this->stmt->reset();
        if (!$this->stmt_reset) {
            $this->stmt_error = $this->stmt->error;
            $this->stmt_errno = $this->stmt->errno;
            return false;
        }
        return true;
    }

}


?>