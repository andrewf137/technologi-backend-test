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

session_start();

$searchResult = $_SESSION['searchResult'] ?? '';
unset($_SESSION['searchResult']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Form validation example</title>
        <style>
            input{display:block; margin-bottom:10px;}
        </style>
    </head>

    <body>
        <h1>Search into File Structure</h1>
        <h2>Please enter a search string</h2>
        <p>(Leaving the search input blank will output the whole tree)</p>

        <form action="search.php" method="post">
            <label for="search-string">Search:</label>
            <input name="search-string" id="search-string" type="text" />

            <input type="hidden" name="process" value="1" />
            <input type="submit" name="submit" id="submit" value="Submit" />
        </form>

        <p>Result:</p>
        <?php echo $searchResult ?>
    </body>
</html>