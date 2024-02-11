<?php

namespace ezrarieben\PdoWrapperSingleton;

/**
 * PDO singleton wrapper class for CRUD database operations
 * PDO methods can be run statically thanks to the __callStatic() magic method implementation
 * 
 * @author Ezra Rieben (@ezrarieben)
 */
class Database
{
    private static ?\PDO $connection = null;

    // Connection params
    private static string $host = "";
    private static int $port = 3306;
    private static ?string $dbName = null;
    private static string $charset = "utf8mb4";
    private static ?string $user = null;
    private static ?string $password = null;

    // PDO options
    private static array $pdoAttributes = array(
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false, // Not recommended to enable for modern versions of PHP: https://stackoverflow.com/questions/10113562/pdo-mysql-use-pdoattr-emulate-prepares-or-not. Will cause issues with fetching integers: https://stackoverflow.com/a/1197424/2048290
    );

    /**
     * Private constructor to forbid instantiation of singleton
     */
    private function __construct()
    {
    }
    /**
     * Private clone function to forbid instantiation singleton as object
     */
    private function __clone()
    {
    }

    public static function init(): \PDO
    {
        if (self::$connection === null) {
            $dsn = "mysql:host=" . self::$host;

            // Add optional parameters to DSN if they are set
            if (!empty(self::$port)) {
                $dsn .= ";port=" . self::$port;
            }
            if (!empty(self::$dbName)) {
                $dsn .= ";dbname=" . self::$dbName;
            }
            if (!empty(self::$charset)) {
                $dsn .= ";charset=" . self::$charset;
            }

            self::$connection = new \PDO($dsn, self::$user, self::$password, self::$pdoAttributes);
        }

        return self::$connection;
    }

    /**
     * Forward any static calls to static methods that don't exist locally to PDO
     * See magic method documentation: https://www.php.net/manual/en/language.oop5.overloading.php#object.callstatic
     *
     * @param   string  $method  Name of static method called
     * @param   array  $args    Arguments passed in original static method call 
     *
     * @return  mixed
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return call_user_func_array(array(self::init(), $method), $args);
    }

    /**
     * Run (prepare and execute) an SQL query
     * 
     * @param  string $query      sql query to prep and execute
     * @param  array  $params     params
     * @return PDOStatement|bool       Returns the prepared statement. FALSE if preparing or execution of statement failed.
     */
    public static function run(string $query, array $params = array()): \PDOStatement | bool
    {
        if ($stmt = self::init()->prepare($query)) {
            if ($stmt->execute($params)) {
                return $stmt;
            }
        }

        return false;
    }


    /**
     * Set PDO attributes for the DB connection
     *
     * @param array $attributes Array of PDO attributes to set (see: https://www.php.net/manual/en/pdo.setattribute.php)
     */
    public static function setPdoAttributes(array $attributes)
    {
        self::$pdoAttributes = array_merge(self::$pdoAttributes, $attributes);
    }

    /**
     * Set the password used to connect to DB
     *
     * @param ?string $pass
     */
    public static function setPassword(?string $password)
    {
        self::$password = $password;
    }

    /**
     * Set the username used to connect to DB
     *
     * @param ?string $user
     */
    public static function setUser(?string $user)
    {
        self::$user = $user;
    }

    /**
     * Set the charset used on DB
     *
     * @param string $charset (DEFAULT: utf8mb4)
     */
    public static function setCharset(string $charset = "utf8mb4")
    {
        self::$charset = $charset;
    }

    /**
     * Set the default DB to connect to
     *
     * @param ?string $dbName
     */
    public static function setDbName(?string $dbName)
    {
        self::$dbName = $dbName;
    }

    /**
     * Set the port used to connect to DB server
     *
     * @param ?string $port (DEFAULT: 3306)
     */
    public static function setPort(int $port = 3306)
    {
        self::$port = $port;
    }

    /**
     * Set the hostname (or IP) used to connect to DB server
     *
     * @param string $host
     */
    public static function setHost(string $host)
    {
        self::$host = $host;
    }
}
