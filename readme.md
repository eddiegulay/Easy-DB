# Easy DB

Easy DB is a simple database wrapper for PHP. Designed to speed up production by reducing code redundancy.


## Installing
### Clone the repository to project dir
```bash
git clone "https://github.com/eddygulled/Easy-DB.git"
```
### Include the EasyDB class
```php
require_once('easy_control.php');
```

## Creating Connection
```php
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

## Changing table field
**Create data field set with respective values to update in following format**
```
[
    "table_name" =>  $table_name,
    "field_name" => $target_field,
    "field_name_update" => $new_field_name,
    "options" => $field_extra_options
]
```
* If you don't want to change field name pass in the current field name in `field_name_update`


**Example**
Updating field `email` from `test_user` table to `user_email` with text `datatype`
```php
$data = array(
    "table_name" => "test_user",
    "field_name" => "email",
    "field_name_update" => "user_email",
    "options" => "TEXT"
);

$db->change_field($data);
```

## Fetch MySQLi result as Object

**Returning result object for manual manipulation**
*with raw query*
```php
$res =$db->fetch_object("SELECT * FROM test_user");
```
*with query generator*
Create fetch data field in following format
```
[
    "table_name" => $target_table,
    "fields" => "field_1, field_2",
    "conditions" => "condition_1 and condition_2 and condition_3"
]
```

Example fetch from table `test_user`
```php
$data = array(
    "table_name" => "test_user",
    "fields" => "*",
    "conditions" => "id=1"
);

$res = $db->easy_object($data);
```
*Result Preview*
```
mysqli_result Object
(
    [current_field] => 0
    [field_count] => 4
    [lengths] => 
    [num_rows] => 1
    [type] => 0
)
```
### Further operations from object returned
Get number of rows
```php
$num_rows = $res->num_rows;
```
Get data as normal array or as associative array
```php
$res_array = $res->fetch_array();
$res_assoc = $res->fetch_assoc();
```
And So on ....



## Fetch MySQLi result as array

**Returning result as array for manual manipulation**
*with raw query*
```php
$res =$db->fetch_array("SELECT * FROM test_user");
```
*with query generator*
Create fetch data field in following format
```
[
    "table_name" => $target_table,
    "fields" => "field_1, field_2",
    "conditions" => "condition_1 and condition_2 and condition_3"
]
```

Example fetch from table `test_user`
```php
$data = array(
    "table_name" => "test_user",
    "fields" => "*",
    "conditions" => "id=1"
);

$res = $db->easy_array($data);
```
*Result Preview*
```
Array
(
    [0] => 1
    [id] => 1
    [1] => Edgar Edward Gulay
    [user_custom_name] => Edgar Edward Gulay
    [2] => tracyedgar270@gmail.com
    [email] => tracyedgar270@gmail.com
    [3] => qwerd3433
    [password] => qwerd3433
)
```

## Fetch MySQLi result as associative array

**Returning result as associative array for manual manipulation**
*with raw query*
```php
$res =$db->fetch_array("SELECT * FROM test_user");
```
*with query generator*
Create fetch data field in following format
```
[
    "table_name" => $target_table,
    "fields" => "field_1, field_2",
    "conditions" => "condition_1 and condition_2 and condition_3"
]
```

Example fetch from table `test_user`
```php
$data = array(
    "table_name" => "test_user",
    "fields" => "*",
    "conditions" => "id=1"
);

$res = $db->easy_assoc($data);
```
*Result Preview*
```
Array
(
    [id] => 1
    [user_custom_name] => Edgar Edward Gulay
    [email] => tracyedgar270@gmail.com
    [password] => qwerd3433
)
```

## Delete Data Row
**Create data field set with respective values to update in following format**
```
[
    "table_name" => $table_name,
    "conditions" => "condition_1 and condition_2 and ..... and condition_n"
]
```
* Don't provide conditions if you want to delete all data in table

**Example delete from table `test_user` where `id`=`1`**
```php
$data = array(
    "table_name" => "test_user",
    "conditions" => "id=1"
);

$db->delete_row($data);
```