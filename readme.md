# Easy DB Wrapper

This is a simple and easy-to-use PHP database wrapper for MySQL using the `mysqli` extension. It simplifies common database operations like executing queries, inserting data, updating records, and more.

## Getting Started

### Requirements
- PHP 7.4 or higher
- MySQL Server
- `mysqli` extension enabled

### Installation
Simply include the `easy_db.php` file in your project and instantiate the `easy_db` class with your database credentials.

```php
require_once 'easy_db.php';
$db = new easy_db('localhost', 'username', 'password', 'database_name', 3306, 'utf8mb4');
```

## Usage

### Creating an Instance
To start using the `easy_db` wrapper, create an instance by passing the database credentials:

```php
try {
    $db = new easy_db('localhost', 'username', 'password', 'database_name', 3306, 'utf8mb4');
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Executing Queries
You can execute a basic SQL query using the `query()` method.

```php
$result = $db->query("SELECT * FROM users");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Query failed: " . $db->get_last_error()['error'];
}
```

### Prepared Statements
To execute a prepared statement with parameters, use the `prepare()` method.

```php
$query = "INSERT INTO users (name, email) VALUES (?, ?)";
$params = ['John Doe', 'john@example.com'];
$stmt = $db->prepare($query, $params);

if ($stmt) {
    echo "Record inserted successfully.";
} else {
    echo "Failed to insert record: " . $db->get_last_error()['error'];
}
```

### Fetching Data
- **Fetch a Single Row as an Object**

```php
$user = $db->fetch_object("SELECT * FROM users WHERE id = 1");
if ($user) {
    echo $user->name;
} else {
    echo "No user found.";
}
```

- **Fetch a Single Row as an Associative Array**

```php
$user = $db->fetch_assoc("SELECT * FROM users WHERE id = 1");
if ($user) {
    echo $user['name'];
} else {
    echo "No user found.";
}
```

- **Fetch All Rows as an Associative Array**

```php
$users = $db->fetch_all_assoc("SELECT * FROM users");
if ($users) {
    foreach ($users as $user) {
        echo $user['name'] . "<br>";
    }
} else {
    echo "No users found.";
}
```

### Inserting Data
Use the `insert()` method to insert data into a table.

```php
$data = [
    'table_name' => 'users',
    'name' => 'Jane Doe',
    'email' => 'jane@example.com'
];
$result = $db->insert($data);
if ($result) {
    echo "Record inserted successfully.";
} else {
    echo "Failed to insert record: " . $db->get_last_error()['error'];
}
```

### Updating Data
Use the `update()` method to update existing records.

```php
$data = [
    'table_name' => 'users',
    'values' => ['email' => 'jane.doe@example.com'],
    'conditions' => ['id' => 1]
];
$result = $db->update($data);
if ($result) {
    echo "Record updated successfully.";
} else {
    echo "Failed to update record: " . $db->get_last_error()['error'];
}
```

### Deleting Data
Use the `delete_row()` method to delete records.

```php
$data = [
    'table_name' => 'users',
    'conditions' => ['id' => 1]
];
$result = $db->delete_row($data);
if ($result) {
    echo "Record deleted successfully.";
} else {
    echo "Failed to delete record: " . $db->get_last_error()['error'];
}
```

### Getting Last Insert ID
To get the ID of the last inserted record, use `get_last_insert_id()`:

```php
$last_id = $db->get_last_insert_id();
echo "Last inserted ID: " . $last_id;
```

### Escaping Strings
To safely escape strings before using them in queries, use `easy_escape_string()`:

```php
$name = $db->easy_escape_string("O'Reilly");
$query = "SELECT * FROM users WHERE name = '$name'";
$result = $db->query($query);
```

## QueryBuilder
The `QueryBuilder` class helps generate common SQL queries like CREATE, INSERT, UPDATE, and DELETE without writing raw SQL.

### Example Usage of QueryBuilder

- **Build a CREATE TABLE Query**

```php
$data = [
    [
        'table_name' => 'users'
    ],
    [
        'field' => 'id',
        'data_type' => 'INT AUTO_INCREMENT PRIMARY KEY'
    ],
    [
        'field' => 'name',
        'data_type' => 'VARCHAR(100)'
    ],
    [
        'field' => 'email',
        'data_type' => 'VARCHAR(100)'
    ]
];
$query = QueryBuilder::buildTableQuery($data);
$db->query($query);
```

- **Build an INSERT Query**

```php
$data = [
    'table_name' => 'users',
    'name' => 'John Smith',
    'email' => 'john.smith@example.com'
];
$query = QueryBuilder::buildInsertQuery($data);
$db->query($query);
```

## License
This project is open-source and available under the [MIT License](LICENSE).

## Contributing
Feel free to contribute by opening issues or submitting pull requests to improve the library.

## Support
If you encounter any issues or have questions, feel free to reach out by creating an issue on the GitHub repository.

