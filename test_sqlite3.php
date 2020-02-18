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

$res = $db->query("SELECT * FROM " . $table);

$request = "<hr><table>";
while ($row = $res->fetchArray()) {
    $request .= "<tr><td>" . $row['id'] . "</td><td>" . $row['name'] . "</td><td>" . $row['price'] . "</td></tr>";
}
$request .= "</table>";

$db->close();

deleteDb($table);

$end = microtime(true);
$time = $end - $start;
echo 'Le temps total écoulé pour cette requete SQLite3 est de ' . $time . ' secondes pour 1000 requetes Ecrit & lu + Création DB<br/>';

echo $request;