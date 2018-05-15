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

include_once "../utils/util.php";

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
    	self::$connectionString = $this->makeConnectionString();
    }

	/**
	 * Make connection string.
	 * @returns string connection string or null if any of the necessary
	 * values required for the connection is not set
	 */
	private function makeConnectionString(): string{
		// Gets Config-instance
		$conf = Config::getInstance();

		$host = $conf->getSetting("host");
		$port = $conf->getSetting("port");
		$dbname = $conf->getSetting("dbname");
		$user = $conf->getSetting("user");
		$password = $conf->getSetting("password");

		if($host !== null && $port !== null && $dbname !== null
		  && $user !== null && $password !== null){
			return "host=$host port=$port dbname=$dbname user=$user password=$password";
		}
		return null;
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
    	if(self::$connectionString !== null) {
		    $dbconn = pg_connect(self::$connectionString);
		    return $dbconn;
	    }
	    return null;
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
     * @return null|resource A result set or false = failed to connect or empty
     * result.
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
        return null;
    }

    /**
     * Queries the PostgreSQL database with a parameterized string.
     * @param $queryString string The query string.
     * @param $params array The parameters for the string.
     * @return null|resource A result set or false = failed to connect or empty
     * result.
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
        return null;
    }
}

?>