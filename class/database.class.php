<?php
/*******************************************************************************
 * File: database.class.php
 *
 * Desc: Handles database connections and querying with parameterized strings.
 * Actual queries can be added to this class or other classes by having them
 * use doParamQuery() and doSimpleQuery(). It is however prohibited to
 * concatenate your own queries when using doSimpleQuery().
 *
 * Date: 2018-05-08
 ******************************************************************************/

/**
 * Class Database
 * Handles connections and parameterized
 * queries to the database.
 * Singleton, so only one instance exists.
 */
class Database {

    /**
     * @var Database The single instance of this class.
     */
    protected static $instance;

    /**
     * @var string String used to connect to the database.
     */
    private static $connectionString;

    /**
     * Database constructor.
     * Private due to class being singleton.
     */
    private function __construct(){
        // Gets Config-instance
        $conf = Config::getInstance();

        // Constructs connection string and sets it to static.
        $connStr = "host=" . $conf->getSetting("host") .
            " port=" . $conf->getSetting("port") .
            " dbname=" . $conf->getSetting("dbname") .
            " user=" . $conf->getSetting("user") .
            " password=" . $conf->getSetting("password");

        self::$connectionString = $connStr;
    }

    /**
     * Returns the current instance if one exists,
     * otherwise creates it.
     * @return Database The single instance of this class.
     */
    public static function getInstance(){
        if (!self::$instance){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Opens connection to PostgreSQL database.
     * @return resource The connection to the server if it succeeded.
     */
    private function connect(){
        $dbconn = pg_connect(self::$connectionString);
        return $dbconn;
    }

    /**
     * Closes the database PostgreSQL connection.
     * @param resource The connection to be closed.
     */
    private function disconnect($dbconn){
        pg_close($dbconn);
    }

    /**
     * Executes a simple query without parameters.
     * @param $queryString string The query to be asked.
     * @return bool|resource A result set or false = failed to connect or empty result.
     */
    public function doSimpleQuery($queryString){
        // Connects
        $dbconn = $this->connect();

        if ($dbconn){
            // Queries if connection succeeded
            $result = pg_query($dbconn, $queryString);

            // Disconnects
            $this->disconnect($dbconn);

            // Returns result
            return $result;
        }

        // Failed to connect
        return false;
    }

    /**
     * Queries the PostgreSQL database with a parameterized string.
     * @param $queryString string The query string.
     * @param $params array The parameters for the string.
     * @return bool|resource A result set or false = failed to connect or empty result.
     */
    public function doParamQuery($queryString, $params){
        // Connects
        $dbconn = $this->connect();

        if ($dbconn){
            // Queries if connection succeeded
            $result = pg_query_params($dbconn, $queryString, $params);

            // Disconnects
            $this->disconnect($dbconn);

            // Returns result
            return $result;
        }

        // Failed to connect
        return false;
    }
}

?>