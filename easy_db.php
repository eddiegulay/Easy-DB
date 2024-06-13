<?php
// database manager class for easy_db with mysqli

class easy_db {
    private $db;
    private $error;
    private $errno;

    // constructor
    public function __construct($host, $user, $pass, $name, $port, $charset) {
        $this->db = new mysqli($host, $user, $pass, $name, $port);
        if ($this->db->connect_errno) {
            $this->error = $this->db->connect_error;
            $this->errno = $this->db->connect_errno;
            throw new Exception("Database connection failed: {$this->error}");
        }
        $this->db->set_charset($charset);
    }

    // destructor
    public function __destruct() {
        if ($this->db) {
            $this->db->close();
        }
    }

    // Get last error
    public function get_last_error() {
        return ['error' => $this->error, 'errno' => $this->errno];
    }

    // query
    public function query($query) {
        $result = $this->db->query($query);
        if (!$result) {
            $this->error = $this->db->error;
            $this->errno = $this->db->errno;
            return false;
        }
        return $result;
    }

    // prepared statement execution
    public function prepare($query, $params, $types = "") {
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $this->error = $this->db->error;
            $this->errno = $this->db->errno;
            return false;
        }

        if (!empty($params)) {
            if ($types == "") {
                // Generate types string automatically if not provided
                $types = str_repeat("s", count($params));
            }
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            $this->error = $stmt->error;
            $this->errno = $stmt->errno;
            return false;
        }

        return $stmt;
    }

    // fetch_object
    public function fetch_object($query) {
        $result = $this->query($query);
        if (!$result) {
            return false;
        }
        return $result->fetch_object();
    }

    // fetch_assoc
    public function fetch_assoc($query) {
        $result = $this->query($query);
        if (!$result) {
            return false;
        }
        return $result->fetch_assoc();
    }

    // fetch_all_assoc
    public function fetch_all_assoc($query) {
        $result = $this->query($query);
        if (!$result) {
            return false;
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // num_rows
    public function num_rows($query) {
        $result = $this->query($query);
        if (!$result) {
            return false;
        }
        return $result->num_rows;
    }

    // get last insert id
    public function get_last_insert_id() {
        return $this->db->insert_id;
    }

    public function easy_escape_string($string) {
        return $this->db->real_escape_string($string);
    }
}

class QueryBuilder
{
    public static function buildTableQuery($data)
    {
        $table_name = str_replace(" ", "_", $data[0]["table_name"]);
        $q = "CREATE TABLE `$table_name` (";
        foreach ($data as $row) {
            if (isset($row["table_name"])) {
                continue;
            }
            $field = str_replace(" ", "_", $row["field"]);
            $q .= "`$field` {$row['data_type']} ";
            if (isset($row["options"])) {
                $q .= "{$row['options']}, ";
            } else {
                $q .= ", ";
            }
        }
        $q = rtrim($q, ", ") . ");";
        return $q;
    }

    public static function buildInsertQuery($data)
    {
        $table_name = $data['table_name'];
        $columns = [];
        $values = [];
        foreach ($data as $key => $value) {
            if ($key == 'table_name') {
                continue;
            }
            $escaped_value = self::escapeValue($value);
            $columns[] = "`$key`";
            $values[] = "'$escaped_value'";
        }
        $q = "INSERT INTO `$table_name` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
        return $q;
    }

    public static function buildUpdateQuery($data)
    {
        $table_name = $data['table_name'];
        $q = "UPDATE `$table_name` SET ";
        foreach ($data['values'] as $key => $value) {
            $escaped_value = self::escapeValue($value);
            $q .= "`$key` = '$escaped_value', ";
        }
        $q = rtrim($q, ", ");
        $q .= " WHERE ";
        foreach ($data['conditions'] as $key => $value) {
            $escaped_value = self::escapeValue($value);
            $q .= "`$key` = '$escaped_value' AND ";
        }
        $q = rtrim($q, " AND ");
        return $q;
    }

    public static function buildChangeFieldQuery($data)
    {
        $table_name = $data['table_name'];
        $old_field = $data['field_name'];
        $new_field = isset($data['field_name_update']) ? str_replace(" ", "_", $data['field_name_update']) : false;
        $options = $data['options'];
        $q = "ALTER TABLE `$table_name` CHANGE `$old_field` `$new_field` $options";
        return $q;
    }

    public static function buildSelectQuery($data)
    {
        $table_name = $data['table_name'];
        $fields = $data['fields'];
        $q = "SELECT $fields FROM `$table_name`";
        if (isset($data["conditions"])) {
            $conditions = [];
            foreach ($data["conditions"] as $key => $value) {
                $escaped_value = self::escapeValue($value);
                $conditions[] = "`$key` = '$escaped_value'";
            }
            $q .= " WHERE " . implode(" AND ", $conditions);
        }
        $q .= ";";
        return $q;
    }

    public static function buildDeleteQuery($data)
    {
        $table_name = $data['table_name'];
        $q = "DELETE FROM `$table_name`";
        if (isset($data["conditions"])) {
            $conditions = [];
            foreach ($data["conditions"] as $key => $value) {
                $escaped_value = self::escapeValue($value);
                $conditions[] = "`$key` = '$escaped_value'";
            }
            $q .= " WHERE " . implode(" AND ", $conditions);
        }
        $q .= ";";
        return $q;
    }

    private static function escapeValue($value)
    {
        // Implement your value escaping mechanism here
        return addslashes($value); // Example of escaping
    }
}

// create table
class easy_control extends easy_db
{
    private $error;
    private $errno;
    // Constructor to initialize the database connection
    public function __construct($host, $user, $pass, $name, $port, $charset)
    {
        parent::__construct($host, $user, $pass, $name, $port, $charset);
    }

    // Escape values to prevent SQL injection
    private function escape_value($value) {
        $escaped_value = $this->easy_escape_string($value);
        return $escaped_value;
    }

    // query generator
    private function query_generator($state, $data)
    {
        switch ($state) {
            case "table":
                return QueryBuilder::buildTableQuery($data);

            case "insert":
                return QueryBuilder::buildInsertQuery($data);

            case "update":
                return QueryBuilder::buildUpdateQuery($data);

            case "change_field":
                return QueryBuilder::buildChangeFieldQuery($data);

            case "select":
                return QueryBuilder::buildSelectQuery($data);

            case "delete":
                return QueryBuilder::buildDeleteQuery($data);

            default:
                return '';
        }
    }

    // create new table
    public function create_table($table_field_set)
    {
        $q = $this->query_generator("table", $table_field_set);
        return $this->query($q);
    }

    // drop table
    public function drop_table($table_name)
    {
        $q = "DROP TABLE `$table_name`;";
        return $this->query($q);
    }

    // insert into table
    public function insert($data)
    {
        $q = $this->query_generator("insert", $data);
        return $this->query($q);
    }

    // update data in table
    public function update($data)
    {
        $q = $this->query_generator("update", $data);
        return $this->query($q);
    }

    // change field
    public function change_field($data)
    {
        $q = $this->query_generator("change_field", $data);
        return $this->query($q);
    }

    // easy fetch object
    public function easy_object($data)
    {
        $q = $this->query_generator("select", $data);
        return $this->fetch_object($q);
    }

    // easy fetch assoc all
    public function easy_fetch_assoc_all($data)
    {
        $q = $this->query_generator("select", $data);
        return $this->fetch_assoc_all($q);
    }

    // fetch assoc all
    public function fetch_assoc_all($q)
    {
        $response = array();
        $res = $this->query($q);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $response[] = $row;
            }
            return $response;
        } else {
            return false;
        }
    }

    // easy fetch assoc
    public function easy_assoc($data)
    {
        $q = $this->query_generator("select", $data);
        return $this->fetch_assoc($q);
    }

    // easy fetch array
    public function easy_array($data)
    {
        $q = $this->query_generator("select", $data);
        $res = $this->query($q);
        return $res ? $res->fetch_array() : false;
    }

    // easy fetch num rows
    public function easy_num_rows($data)
    {
        $q = $this->query_generator("select", $data);
        return $this->num_rows($q);
    }

    // delete row
    public function delete_row($data)
    {
        $q = $this->query_generator("delete", $data);
        return $this->query($q);
    }
}

?>
