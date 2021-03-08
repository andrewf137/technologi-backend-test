<?php
declare(strict_types=1);

/**
 * Description:
 *     Perform search over the database and redirect to index.php
 *
 * @author   Andrés Rodríguez
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/andrewf137/technologi-backend-test
 */

require_once(__DIR__. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

session_start();

$treeManagement = new \RecursiveFileStructure\TreeManagement();

if (isset($_POST['submit']) && $_POST['submit'] === 'Submit') {
    $searchResult = $treeManagement->search($_POST['search-string']);
    $_SESSION['searchResult'] = implode('<br/>', $searchResult);
}

header('Location:index.php');
exit();
?>