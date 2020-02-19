<?php
/**
 * benchmark pour SQLite 3 pour PHP7
 */

$table = 'bench' . mt_rand(1000, 9999);

function deleteDb($table)
{
    if (file_exists('./test_sqlite3-' . $table . '.db')) {
        unlink('./test_sqlite3-' . $table . '.db');
    }
}

$start = microtime(true);

deleteDb($table);

$db = new SQLite3('test_sqlite3-' . $table . '.db');

$db->exec("
    CREATE TABLE IF NOT EXISTS " . $table . "(id INTEGER PRIMARY KEY, name TEXT, price INT)
");

for ($i = 0; $i < 1000; $i++) {
    $db->exec("INSERT INTO " . $table . "(name, price) VALUES( 'B-" . mt_rand(111111, 999999) . "' , " . mt_rand(999, 9999) . ")");
}

$afterCommitTime = microtime(true);
$insertTime = $afterCommitTime - $start;

$res = $db->query("SELECT * FROM " . $table);

$request = "<hr><table>";
while ($row = $res->fetchArray()) {
    $request .= "<tr><td>" . $row['id'] . "</td><td>" . $row['name'] . "</td><td>" . $row['price'] . "</td></tr>";
}
$request .= "</table>";

$db->close();

deleteDb($table);

$end = microtime(true);
$selectTime = $end - $afterCommitTime;
$totalTime = $end - $start;

echo "
    <style>
        table {
        border-collapse: collapse;
        }

        table, th, td {
        border: 1px solid black;
        }

        p {
            font-style: italic;
        }
    </style>
    <h2>Temps requis pour traiter les requêtes SQLite3</h2>
    <table>
        <tr>
            <th>Insertion 1000 requêtes</th>
            <th>Récupèration 1000 requêtes</th>
            <th>Execution du script</th>
        </tr>
        <tr>
            <td>" . $insertTime . " seconde(s)</td>
            <td>" . $selectTime . " seconde(s)</td>
            <td>" . $totalTime . " seconde(s)</td>
        </tr>
    </table>
    <p>
    Le temps écoulé pour INSERTION de données est de " . $insertTime . " secondes pour 1000 requetes Ecrites
    </p>
    <p>
    Le temps écoulé pour OBTENIR les données est de " . $selectTime . " secondes pour 1000 requetes Lues
    </p>
    <p>
    Le temps total écoulé pour cette requete SQLite3 est de " . $totalTime . " secondes pour 1000 requetes Ecrit & lu + Création DB
    </p>
";

echo $request;