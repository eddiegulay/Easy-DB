<?php
// Database manager class for easy_db with mysqli

class easy_db {
    private $db;
    private $error;
    private $errno;

    /**
     * Constructor to initialize a database connection.
     *
     * @param string $host     Database host address.
     * @param string $user     Database username.
     * @param string $pass     Database password.
     * @param string $name     Database name.
     * @param int    $port     Database port number.
     * @param string $charset  Character set to use.
     *
     * @throws Exception If the database connection fails.
     */
    public function __construct($host, $user, $pass, $name, $port, $charset) {
        $this->db = new mysqli($host, $user, $pass, $name, $port);
        if ($this->db->connect_errno) {
            $this->error = $this->db->connect_error;
            $this->errno = $this->db->connect_errno;
            throw new Exception("Database connection failed: {$this->error}");
        }
        $this->db->set_charset($charset);
    }

    /**
     * Destructor to close the database connection when the object is destroyed.
     */
    public function __destruct() {
        if ($this->db) {
            $this->db->close();
        }
    }

    /**
     * Get the last error information.
     *
     * @return array An associative array containing 'error' and 'errno'.
     */
    public function get_last_error() {
        return ['error' => $this->error, 'errno' => $this->errno];
    }

    /**
     * Execute a query.
     *
     * @param string $query SQL query to execute.
     *
     * @return mixed Result set on success, false on failure.
     */
    public function query($query) {
        $result = $this->db->query($query);
        if (!$result) {
            $this->error = $this->db->error;
            $this->errno = $this->db->errno;
            return false;
        }
        return $result;
    }

    /**
     * Execute a prepared statement with parameters.
     *
     * @param string $query  SQL query with placeholders.
     * @param array  $params Parameters to bind to the query.
     * @param string $types  Data types of the parameters (optional).
     *
     * @return mixed Prepared statement on success, false on failure.
     */
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

    /**
     * Fetch a single row as an object.
     *
     * @param string $query SQL query to execute.
     *
     * @return mixed An object representing the row on success, false on failure.
     */
    public function fetch_object($query) {
        $result = $this->query($query);
        if (!$result) {
            return false;
        }
        return $result->fetch_object();
    }

    /**
     * Fetch a single row as an associative array.
     *
     * @param string $query SQL query to execute.
     *
     * @return mixed An associative array representing the row on success, false on failure.
     */
    public function fetch_assoc($query) {
        $result = $this->query($query);
        if (!$result) {
            return false;
        }
        return $result->fetch_assoc();
    }

    /**
     * Fetch all rows as an associative array.
     *
     * @param string $query SQL query to execute.
     *
     * @return mixed An array of associative arrays representing all rows on success, false on failure.
     */
    public function fetch_all_assoc($query) {
        $result = $this->query($query);
        if (!$result) {
            return false;
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get the number of rows from the result of a query.
     *
     * @param string $query SQL query to execute.
     *
     * @return mixed Number of rows on success, false on failure.
     */
    public function num_rows($query) {
        $result = $this->query($query);
        if (!$result) {
            return false;
        }
        return $result->num_rows;
    }

    /**
     * Get the last insert ID.
     *
     * @return int ID of the last inserted row.
     */
    public function get_last_insert_id() {
        return $this->db->insert_id;
    }

    /**
     * Escape a string for safe use in SQL queries.
     *
     * @param string $string The string to escape.
     *
     * @return string The escaped string.
     */
    public function easy_escape_string($string) {
        return $this->db->real_escape_string($string);
    }
}

class QueryBuilder
{
    /**
     * Build a CREATE TABLE query.
     *
     * @param array $data Array of field definitions for the table.
     *
     * @return string The CREATE TABLE SQL query.
     */
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

    /**
     * Build an INSERT query.
     *
     * @param array $data Data to insert into the table.
     *
     * @return string The INSERT SQL query.
     */
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

    /**
     * Build an UPDATE query.
     *
     * @param array $data Data to update in the table.
     *
     * @return string The UPDATE SQL query.
     */
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

    /**
     * Build a CHANGE FIELD query.
     *
     * @param array $data Data to change the field in the table.
     *
     * @return string The ALTER TABLE CHANGE SQL query.
     */
    public static function buildChangeFieldQuery($data)
    {
        $table_name = $data['table_name'];
        $old_field = $data['field_name'];
        $new_field = isset($data['field_name_update']) ? str_replace(" ", "_", $data['field_name_update']) : false;
        $options = $data['options'];
        $q = "ALTER TABLE `$table_name` CHANGE `$old_field` `$new_field` $options";
        return $q;
    }

    /**
     * Build a SELECT query.
     *
     * @param array $data Data for selecting fields from the table.
     *
     * @return string The SELECT SQL query.
     */
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

    /**
     * Build a DELETE query.
     *
     * @param array $data Data for deleting rows from the table.
     *
     * @return string The DELETE SQL query.
     */
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

    /**
     * Escape a value for safe use in SQL queries.
     *
     * @param mixed $value The value to escape.
     *
     * @return string The escaped value.
     */
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

    /**
     * Constructor to initialize the database connection.
     *
     * @param string $host     Database host address.
     * @param string $user     Database username.
     * @param string $pass     Database password.
     * @param string $name     Database name.
     * @param int    $port     Database port number.
     * @param string $charset  Character set to use.
     */
    public function __construct($host, $user, $pass, $name, $port, $charset)
    {
        parent::__construct($host, $user, $pass, $name, $port, $charset);
    }

    /**
     * Escape a value for safe use in SQL queries.
     *
     * @param mixed $value The value to escape.
     *
     * @return string The escaped value.
     */
    private function escape_value($value) {
        $escaped_value = $this->easy_escape_string($value);
        return $escaped_value;
    }

    /**
     * Generate a query based on the state and data provided.
     *
     * @param string $state Type of query to generate (e.g., "table", "insert", "update").
     * @param array  $data  Data for building the query.
     *
     * @return string The generated SQL query.
     */
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

    /**
     * Create a new table in the database.
     *
     * @param array $table_field_set Array of field definitions for the table.
     *
     * @return mixed Result set on success, false on failure.
     */
    public function create_table($table_field_set)
    {
        $q = $this->query_generator("table", $table_field_set);
        return $this->query($q);
    }

    /**
     * Drop a table from the database.
     *
     * @param string $table_name Name of the table to drop.
     *
     * @return mixed Result set on success, false on failure.
     */
    public function drop_table($table_name)
    {
        $q = "DROP TABLE `$table_name`;";
        return $this->query($q);
    }

    /**
     * Insert data into a table.
     *
     * @param array $data Data to insert into the table.
     *
     * @return mixed Result set on success, false on failure.
     */
    public function insert($data)
    {
        $q = $this->query_generator("insert", $data);
        return $this->query($q);
    }

    /**
     * Update data in a table.
     *
     * @param array $data Data to update in the table.
     *
     * @return mixed Result set on success, false on failure.
     */
    public function update($data)
    {
        $q = $this->query_generator("update", $data);
        return $this->query($q);
    }

    /**
     * Change a field in a table.
     *
     * @param array $data Data for changing the field in the table.
     *
     * @return mixed Result set on success, false on failure.
     */
    public function change_field($data)
    {
        $q = $this->query_generator("change_field", $data);
        return $this->query($q);
    }

    /**
     * Fetch a single row as an object using generated query data.
     *
     * @param array $data Data for selecting fields from the table.
     *
     * @return mixed An object representing the row on success, false on failure.
     */
    public function easy_object($data)
    {
        $q = $this->query_generator("select", $data);
        return $this->fetch_object($q);
    }

    /**
     * Fetch all rows as an associative array using generated query data.
     *
     * @param array $data Data for selecting fields from the table.
     *
     * @return mixed An array of associative arrays representing all rows on success, false on failure.
     */
    public function easy_fetch_assoc_all($data)
    {
        $q = $this->query_generator("select", $data);
        return $this->fetch_assoc_all($q);
    }

    /**
     * Fetch all rows as an associative array from a query.
     *
     * @param string $q SQL query to execute.
     *
     * @return mixed An array of associative arrays representing all rows on success, false on failure.
     */
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

    /**
     * Fetch a single row as an associative array using generated query data.
     *
     * @param array $data Data for selecting fields from the table.
     *
     * @return mixed An associative array representing the row on success, false on failure.
     */
    public function easy_assoc($data)
    {
        $q = $this->query_generator("select", $data);
        return $this->fetch_assoc($q);
    }

    /**
     * Fetch a single row as a numerical array using generated query data.
     *
     * @param array $data Data for selecting fields from the table.
     *
     * @return mixed A numerical array representing the row on success, false on failure.
     */
    public function easy_array($data)
    {
        $q = $this->query_generator("select", $data);
        $res = $this->query($q);
        return $res ? $res->fetch_array() : false;
    }

    /**
     * Get the number of rows from the result of a query using generated query data.
     *
     * @param array $data Data for selecting fields from the table.
     *
     * @return mixed Number of rows on success, false on failure.
     */
    public function easy_num_rows($data)
    {
        $q = $this->query_generator("select", $data);
        return $this->num_rows($q);
    }

    /**
     * Delete rows from a table using generated query data.
     *
     * @param array $data Data for deleting rows from the table.
     *
     * @return mixed Result set on success, false on failure.
     */
    public function delete_row($data)
    {
        $q = $this->query_generator("delete", $data);
        return $this->query($q);
    }
}

?>