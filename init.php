<?php
declare(strict_types=1);

/**
 * Description:
 *     Initiator so test can be run with no preparation.
 *
 * @author   Andrés Rodríguez
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/andrewf137/technologi-backend-test
 */

namespace RecursiveFileStructure;

require_once(__DIR__. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

// Build DB with user and password plus two tables
$dbBuilder = new DBBuilder();
$dbBuilder->buildDB();

// Populate tables from file tree
$treeManagement = new TreeManagement();
$treeManagement->insertNodes();