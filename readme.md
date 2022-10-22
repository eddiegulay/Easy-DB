# Easy DB

## Creating Connection
```php
require_once('easy_control.php');

// create new database connection
$host = "";
$user = "";
$password = "";
$database_name = "";
$port = 3306
$charset = "";

$db = new easy_control($domain, $user, $password, $database_name, $port, $charset);
if(!$db){
    echo "Closing Connection because of Err no: ".$db->errorno;
    $db->__destruct();
}

```

## Creating table
**Create field set list with the following format**
```
[
    ["table_name" => $table_name],
    [
        "field" => $table_field_name,
        "data_type" => $data_type,
        "options" => $field_options
    ]
}
```
**Example**
```php
$table_field_set = array(
    ["table_name" => "test_user"],
    [
        "field" => "id",
        "data_type" => "INT",
        "options" => "PRIMARY KEY"
    ],
    [
        "field" => "user custom name",
        "data_type" => "varchar(255)",
        "options" => "NOT NULL"
    ],
    [
        "field" => "email",
        "data_type" => "varchar(255)",
        "options" => "NOT NULL"
    ],
    [
        "field" => "password",
        "data_type" => "text",
    ]
);
```
**Then call**
```php
$db->create_table($table_field_set);
```

## Deleting Table
**drops a specified table**
```php
$db->drop_table("test_user");
```

## Inserting Data
**Create data field set with the following format**
```
[
    "table_name" => $table_name,
    "field_1" => $value_1,
    "field_2" => $value_2,
    "field_3" => $value_3
]
```

**Example insert**
Data fieldset
```php
$data = array(
    "table_name" => "test_user",
    "id" => "1",
    "user_custom_name" => "edgar",
    "email" => "edgargulay69@gmail.com",
    "password" => "easy_password3433"
);
```

Then call
```php
$db->insert($data);
```


## Updating Data
**Create data field set with respective values to update in following format**
```
[
    "table_name" => $table_name,
    "values" => [
        "field_1" => $value_1,
        "field_2" => $value_2,
        "field_3" => $value_3
    ],
    "conditions" => [
        "field_1" => $value_1,
        "field_2" => $value_2,
        "field_3" => $value_3
    ]
]
```
**Example**
```php
$data = array(
    "table_name" => "test_user",
    "values" => array(
        "user_custom_name" => "Edgar Edward Gulay",
        "email" => "tracyedgar270@gmail.com"
    ),
    "conditions" => array(
        "user_custom_name" => "edgar",
        "id" => 1
    )
);
```
Then call
```php
$db->update($data);
```
