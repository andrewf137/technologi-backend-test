<?php
declare(strict_types=1);

/**
 * Description:
 *     Perform certain operations over tree based data.
 *
 * @package  RecursiveFileStructure\TreeManagement
 * @author   Andrés Rodríguez
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/andrewf137/technologi-backend-test
 */

namespace RecursiveFileStructure;

use RecursiveFileStructure\Exception\OpenFileException;

/**
 * Class TreeManagement
 *
 * @package  RecursiveFileStructure\TreeManagement
 */
class TreeManagement
{
    /**
     * @desc inserts the file structure into DB
     */
    public function insertNodes(): void
    {
        // Depth of nodes will be indicated by the number of indentations each line contains
        /** @var string $indentation */
        $indentation = '    ';

        // We'll keep an array of ancestors of each node (i.e. line)
        /** @var array $path */
        $path = [];

        try {
            /** @var false|resource $resource */
            $resource = fopen("fileStructure.txt", "r");

            if ($resource) {
                while (($line = fgets($resource)) !== false) {
                    // Remove trailing newline
                    $line = rtrim($line);

                    // Get depth of the node
                    /** @var int $depth */
                    $depth = 0;
                    while (substr($line, 0, strlen($indentation)) === $indentation) {
                        $depth += 1;
                        $line = substr($line, strlen($indentation));
                    }

                    // Keep in $path only ancestors of the current node (i.e. line)
                    while ($depth < sizeof($path)) {
                        array_pop($path);
                    }

                    // Insert node
                    $this->insertNode(trim($line));

                    /** @var int $lastNodeId */
                    $lastNodeId = (int)$this->lastInsertId();

                    // Insert self-referencing row
                    $this->insertRelation($lastNodeId, $lastNodeId, 0);

                    // Add new level
                    $path[$depth] = $lastNodeId;

                    // Add a descendant to each of the ancestor of the current node
                    for ($i = $depth - 1; $i >= 0; $i--) {
                        $this->insertDescendant($lastNodeId, $path[$i]);
                    }
                }

                fclose($resource);
            } else {
                $error = error_get_last();
                throw new OpenFileException($error['message']);
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * @desc return all "paths" that contain a given string or the whole tree if no string is supplied
     * @param string|null $searchString
     * @return array
     */
    public function search(string $searchString = null): array
    {
        $dbpdo = new DBPDO();

        if (null !== $searchString) {
            // This query requires disabling MySQL "ONLY_FULL_GROUP_BY" attribute.
            $dbpdo->execute("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

            $query = "SELECT group_concat(n.label ORDER BY n.id ASC separator '\\\') AS path
                        FROM nodes n
                       INNER JOIN relations r ON n.id = r.ancestor
                       WHERE r.descendant IN (SELECT id FROM nodes WHERE label LIKE ?)
                       GROUP BY r.descendant
                       ORDER BY r.descendant ASC, n.id ASC";
            $results = $dbpdo->fetchAll($query, '%' . $searchString . '%', 'path');
        } else {
            // We allow empty search string to display the whole tree
            $query = "SELECT group_concat(n.label ORDER BY r2.depth desc separator '\\\') AS path
                        FROM relations r1
                        JOIN relations r2 ON (r2.descendant = r1.descendant)
                        JOIN nodes n ON (n.id = r2.ancestor)
                       WHERE r1.ancestor IN (1)
                         AND r1.descendant != r1.ancestor
                       GROUP BY r1.descendant";
            $results = $dbpdo->fetchAll($query, null, 'path');
        }

        return !empty($results) ? array_keys($results) : [];
    }

    /**
     * @desc insert a row into nodes table
     * @param string $name
     */
    private function insertNode(string $name): void
    {
        $dbpdo = new DBPDO();

        /** @var string $query */
        $query = 'INSERT INTO `nodes` (`label`) VALUES (?)';

        $dbpdo->execute($query, $name);
    }

    /**
     * @desc insert a row into relations table
     * @param int $ancestor
     * @param int $descendant
     */
    private function insertRelation(int $ancestor, int $descendant, int $depth): void
    {
        $dbpdo = new DBPDO();

        /** @var string $query */
        $query = 'INSERT INTO `relations` (`ancestor`, `descendant`, `depth`) VALUES (?, ?, ?)';

        $dbpdo->execute($query, [$ancestor, $descendant, $depth]);
    }

    /**
     * @return string
     */
    private function lastInsertId(): string
    {
        $dbpdo = new DBPDO();

        return $dbpdo->lastInsertId();
    }

    /**
     * @desc insert a descendant into each parent of a given node
     * @param int $parentNode
     * @param int $node
     */
    private function insertDescendant(int $node, int $parentNode): void
    {
        $dbpdo = new DBPDO();

        /** @var string $query */
        $query = 'INSERT INTO `relations` (`ancestor`, `descendant`, `depth`)
                  SELECT `ancestor`, ?, depth + 1
                    FROM `relations`
                    WHERE `descendant` = ?';

        $dbpdo->execute($query, [$node, $parentNode]);
    }
}