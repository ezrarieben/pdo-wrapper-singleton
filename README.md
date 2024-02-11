# pdo-wrapper-singleton

A wrapper class written in PHP for PDO MySQL DB connections following the singleton pattern.<br />

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

# Table of contents

- [pdo-wrapper-singleton](#pdo-wrapper-singleton)
- [Table of contents](#table-of-contents)
- [Installation](#installation)
- [Basic example](#basic-example)
- [Usage](#usage)
  - [Importing wrapper class](#importing-wrapper-class)
  - [Setting up DB connection](#setting-up-db-connection)
      - [Minimal setup example](#minimal-setup-example)
      - [Extensive setup example](#extensive-setup-example)
      - [Available parameters](#available-parameters)
  - [DB interaction](#db-interaction)
    - [Using PDO functions](#using-pdo-functions)
    - [Prepared statements](#prepared-statements)
      - [Example](#example)
      - [Example with named parameters](#example-with-named-parameters)
  - [Error handling](#error-handling)

# Installation

To use the wrapper in your project, add it as a dependency via composer:

```
composer require ezrarieben/pdo-wrapper-singleton
```

# Basic example

```php
use \ezrarieben\PdoWrapperSingleton\Database;

Database::setHost("localhost");
Database::setUser("user");
Database::setPassword("123456");
Database::setDbName("foobar");

try {
    $query = "SELECT * FROM `cars` WHERE `color` = ?";
    $stmt = Database::run($query, ['red']);
    $row = $stmt->fetch();
} catch (\PDOException $e) {
    die("PDO ERROR: " . $e->getMessage());
}
```

# Usage

## Importing wrapper class

For ease of use it is recommended to import the wrapper class with `use`

```php
use \ezrarieben\PdoWrapperSingleton\Database;
```

## Setting up DB connection

In order to use the wrapper class `Database` the connection parameters need to be set first.

There are certain required parameters that need to be set in order for the PDO connection to work.
<br/>(See "Required" column in [available parameters table](#available-parameters) for required parameters).

#### Minimal setup example

```php
Database::setHost("localhost");
Database::setUser("user");
Database::setPassword("123456");
```

#### Extensive setup example

```php
Database::setHost("localhost");
Database::setPort(3307);
Database::setUser("user");
Database::setPassword("123456");
Database::setDbName("foobar");
Database::setPdoAttributes(array(
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
));
```

#### Available parameters

| Description    | Setter function      | Parameters          | Required |
| -------------- | -------------------- | ------------------- | -------- |
| DB server host | `setHost()`          | `string` $host      | **YES**  |
| User           | `setUser()`          | `string` $user      | **YES**  |
| Password       | `setPassword()`      | `string` $password  | **YES**  |
| DB server host | `setPort()`          | `int` $port         |          |
| Database name  | `setDbName()`        | `?string` $dbName   |          |
| PDO attributes | `setPdoAttributes()` | `array` $attributes |          |

## DB interaction

### Using PDO functions

All PDO functions can be accessed statically through the `Database` class:

```php
$query = "SELECT * FROM `cars` WHERE `color` = ?";
$stmt = Database::prepare($query);
$stmt->execute(['red']);
$row = $stmt->fetch();
```

### Prepared statements

The `Database` class has a shortcut function for prepared statements called `run()`:

| Parameters      | Description                 | Required |
| --------------- | --------------------------- | -------- |
| `string` $query | SQL query to execute        | **YES**  |
| `array` $params | parameters to pass to query |          |

The function returns a `PDOStatement` object if preperation and execution of query was successfull.
<br/>If preperation or execution of query failed the function will throw a `PDOException` or return `false` depending on the currently set PDO error mode.
<br/>(see: [Error handling](#error-handling) for more info)

#### Example

```php
$query = "SELECT * FROM `cars` WHERE `color` = ?";
$stmt = Database::run($query, ['red']);
$row = $stmt->fetch();
```

#### Example with named parameters

```php
$query = "SELECT * FROM `cars` WHERE `color` = :color";
$stmt = Database::run($query, [':color' => 'red']);
$row = $stmt->fetch();
```

## Error handling

PDO's [error mode](https://www.php.net/manual/en/pdo.error-handling.php) is set to `ERRMODE_EXCEPTION` by default.
<br/>Error handling can therefore be done through try and catch blocks.

```php
try {
    $query = "SELECT * FROM `cars` WHERE `color` = ?";
    $stmt = Database::run($query, ['red']);
    $row = $stmt->fetch();
} catch (PDOException $e) {
    // Handle exception
}
```

When switching to a different [error mode](https://www.php.net/manual/en/pdo.error-handling.php) you will need to handle errors through booleans.

> **NOTE:** Error handling using booleans is only supported if you change PDO's error mode.

```php
$query = "SELECT * FROM `cars` WHERE `color` = :color";
if($stmt = Database::run($query, [':color' => 'red'])) {
    // Preparing and executing statement was successfull so fetch the result
    $row = $stmt->fetch();
}
```
