## easy_control Library

### Overview

The `easy_control` class extends the `easy_db` class to provide a comprehensive set of database management functions. These include creating tables, inserting data, updating data, fetching data, and more. This library is designed to simplify interactions with a MySQL database using PHP's `mysqli` extension.

### Installation

1. Ensure you have PHP installed on your system.
2. Place the `easy_db.php` file in your project directory.

### Usage

Here's a step-by-step guide on how to use the `easy_db` class.

#### 1. Include the Library

First, include the `easy_db.php` file in your PHP script.

```php
require_once('easy_db.php');
```

#### 2. Initialize the easy_control Object

Create an instance of the `easy_control` class by providing the necessary database connection parameters.

```php
$db = new easy_control('host', 'username', 'password', 'database_name', 'port', 'charset');
```

Example:

```php
$db = new easy_control('localhost', 'root', 'password', 'my_database', 3306, 'utf8');
```

#### 3. Create a Table

Define the table structure and use the `create_table` method to create a new table.

```php
$table_structure = [
    ['table_name' => 'users'],
    ['field' => 'id', 'data_type' => 'INT AUTO_INCREMENT', 'options' => 'PRIMARY KEY'],
    ['field' => 'username', 'data_type' => 'VARCHAR(50)', 'options' => 'NOT NULL'],
    ['field' => 'email', 'data_type' => 'VARCHAR(100)', 'options' => 'NOT NULL'],
    ['field' => 'created_at', 'data_type' => 'TIMESTAMP', 'options' => 'DEFAULT CURRENT_TIMESTAMP']
];

$db->create_table($table_structure);
```

#### 4. Insert Data

Insert a row of data into a table using the `insert` method.

```php
$insert_data = [
    'table_name' => 'users',
    'username' => 'john_doe',
    'email' => 'john@example.com'
];

$db->insert($insert_data);
```

#### 5. Update Data

Update existing data in a table using the `update` method.

```php
$update_data = [
    'table_name' => 'users',
    'values' => [
        'email' => 'john_doe@example.com'
    ],
    'conditions' => [
        'username' => 'john_doe'
    ]
];

$db->update($update_data);
```

#### 6. Fetch Data

Fetch a single row as an associative array using the `easy_assoc` method.

```php
$select_data = [
    'table_name' => 'users',
    'fields' => '*',
    'conditions' => [
        'username' => 'john_doe'
    ]
];

$user = $db->easy_assoc($select_data);
print_r($user);
```

Fetch all rows as an associative array using the `easy_fetch_assoc_all` method.

```php
$select_data_all = [
    'table_name' => 'users',
    'fields' => '*'
];

$users = $db->easy_fetch_assoc_all($select_data_all);
print_r($users);
```

#### 7. Delete Data

Delete a row from a table using the `delete_row` method.

```php
$delete_data = [
    'table_name' => 'users',
    'conditions' => [
        'username' => 'john_doe'
    ]
];

$db->delete_row($delete_data);
```

### Methods Summary

- **create_table($table_field_set)**: Creates a new table based on the provided structure.
- **drop_table($table_name)**: Drops the specified table.
- **insert($data)**: Inserts a new row into the specified table.
- **update($data)**: Updates existing rows in the specified table.
- **change_field($data)**: Changes the definition of a field in the specified table.
- **easy_object($data)**: Fetches a single row as an object.
- **easy_fetch_assoc_all($data)**: Fetches all rows as an associative array.
- **fetch_assoc_all($q)**: Fetches all rows as an associative array based on a custom query.
- **easy_assoc($data)**: Fetches a single row as an associative array.
- **easy_array($data)**: Fetches a single row as a numeric array.
- **easy_num_rows($data)**: Returns the number of rows for a given select query.
- **delete_row($data)**: Deletes rows from the specified table based on conditions.

### Error Handling

To get the last error encountered during a database operation, use the `get_last_error` method from the `easy_db` class.

```php
$error = $db->get_last_error();
if ($error['errno']) {
    echo "Error ({$error['errno']}): {$error['error']}";
}
```

### Conclusion

The `easy_control` class provides a straightforward and secure way to manage MySQL database operations in PHP. By following this guide, you can quickly integrate database management functionalities into your PHP projects.