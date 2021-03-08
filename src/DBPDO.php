<?php
declare(strict_types=1);

/**
 * Description:
 *     Handy methods to perform general PDO operations over the database.
 *
 * @package  RecursiveFileStructure\DBPDO
 * @author   Andrés Rodríguez
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/andrewf137/technologi-backend-test
 */

namespace RecursiveFileStructure;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Class DBPDO
 *
 * @package  RecursiveFileStructure\DBPDO
 */
class DBPDO
{
    /** @var PDO */
    public $pdo;

    function __construct()
    {
        $this->connect();
    }

    /**
     * @param $query
     * @return PDOStatement|bool
     */
    function prep_query($query)
    {
        return $this->pdo->prepare($query);
    }

    /**
     * @return bool
     */
    function connect(): bool
    {
        $params = require 'config' . DIRECTORY_SEPARATOR . 'config.php';

        if(!$this->pdo){

            $dsn      = 'mysql:dbname=' . $params['DATABASE_NAME'] .
                        ';host=' . $params['DATABASE_HOST'] .
                        ';charset=' . $params['DATABASE_CHARSET'];
            $user     = $params['DATABASE_USER'];
            $password = $params['DATABASE_PASS'];

            try {
                $this->pdo = new PDO($dsn, $user, $password, [PDO::ATTR_PERSISTENT => true]);
                return true;
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }else{
            $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
            return true;
        }
    }

    /**
     * @param $query
     * @param null $values
     * @return PDOStatement|bool
     */
    function execute($query, $values = null)
    {
        if (null === $values) {
            $values = [];
        } elseif (!is_array($values)) {
            $values = [$values];
        }

        /** @var PDOStatement|bool $stmt */
        $stmt = $this->prep_query($query);
        $stmt->execute($values);

        return $stmt;
    }

    /**
     * @param string $query
     * @param null $values
     * @return mixed
     */
    function fetch(string $query, $values = null)
    {
        if (null === $values){
            $values = [];
        } elseif (!is_array($values)) {
            $values = [$values];
        }

        /** @var PDOStatement|bool $stmt */
        $stmt = $this->execute($query, $values);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $query
     * @param null $values
     * @param string|null $key
     * @return array
     */
    function fetchAll(string $query, $values = null, ?string $key = null): array
    {
        if (null === $values){
            $values = [];
        } elseif (!is_array($values)) {
            $values = [$values];
        }

        /** @var PDOStatement|bool $stmt */
        $stmt = $this->execute($query, $values);
        /** @var array $results */
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Allows the user to retrieve results using a
        // column from the results as a key for the array
        if (null !== $key && !empty($results) && $results[0][$key]) {
            $keyed_results = [];
            foreach($results as $result){
                $keyed_results[$result[$key]] = $result;
            }
            $results = $keyed_results;
        }

        return $results;
    }

    /**
     * @return string
     */
    function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}