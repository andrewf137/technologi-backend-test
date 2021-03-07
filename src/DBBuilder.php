<?php
declare(strict_types=1);

/**
 * Description:
 *     Create db and tables to run the test.
 *
 * @package  RecursiveFileStructure\DBBuilder
 * @author   Andrés Rodríguez
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/andrewf137/technologi-backend-test
 */

namespace RecursiveFileStructure;

use PDO;

/**
 * Class DBBuilder
 *
 * @package  RecursiveFileStructure\DBBuilder
 */
class DBBuilder
{
    /**
     * @desc Using data in config/config.php file, a db plus two tables will be created
     */
    public function buildDB()
    {
        // Retrieve DB parameters
	    $params = require 'config' . DIRECTORY_SEPARATOR . 'config.php';
	    $db = $params['DATABASE_NAME'];
	    $host = $params['DATABASE_HOST'];
	    $port = $params['DATABASE_PORT'];
	    $user = $params['DATABASE_USER'];
	    $pass = $params['DATABASE_PASS'];
	    $charset = $params['DATABASE_CHARSET'];

	    // Create a database server connection without specifying db name
        $dsn = "mysql:host=$host;port=$port;charset=$charset;";
	    $this->pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_PERSISTENT => true]);

	    // Drop schema in case it exists
	    $this->pdo->exec("DROP SCHEMA $db;");

	    // Create db
	    $this->pdo->exec("CREATE DATABASE `$db`;
                CREATE USER '$user'@'$host' IDENTIFIED BY '$pass';
                GRANT ALL ON `$db`.* TO '$user'@'$host';
                FLUSH PRIVILEGES;");

	    // Select db just created
        $this->pdo->exec("USE $db;");

        // Create "nodes" and "relations" tables.
        $this->pdo->exec('CREATE TABLE `nodes` (
                      `id` BIGINT NOT NULL AUTO_INCREMENT,
                      `label` VARCHAR(255) NULL,
                      PRIMARY KEY (`id`),
                      INDEX `index2` (`label` ASC));');

        $this->pdo->exec('CREATE TABLE `relations` (
                      `ancestor` BIGINT NOT NULL,
                      `descendant` BIGINT NOT NULL,
                      `depth` INT NOT NULL,
                      PRIMARY KEY (`ancestor`, `descendant`),
                      INDEX `fk_relations_2_idx` (`descendant` ASC),
                      CONSTRAINT `fk_relations_1`
                        FOREIGN KEY (`ancestor`)
                        REFERENCES `nodes` (`id`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION,
                      CONSTRAINT `fk_relations_2`
                        FOREIGN KEY (`descendant`)
                        REFERENCES `nodes` (`id`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION);');
	}
}