<?php

use concrete5\HandleManager\Test\TestCase;

define('C5HM_DATABASENAME', 'c5_handlemanager_test');

$connectionCreator = function ($withDBName = true) {
    $dns = 'mysql:host='.(getenv('MYSQL_HOST') ?: 'localhost');
    if ($withDBName) {
        $dns .= ';dbname='.C5HM_DATABASENAME;
    }

    return new PDO(
        $dns,
        getenv('MYSQL_USER') ?: 'root',
        getenv('MYSQL_PASSWORD') ?: '',
        [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false,
        ]
    );
};

$dbName = C5HM_DATABASENAME;
$tableName = TestCase::TABLENAME;
$pdo = $connectionCreator(false);
try {
    $pdo->exec("CREATE DATABASE {$dbName} COLLATE 'utf8_general_ci'");
} catch (PDOException $x) {
}
$pdo->exec("DROP TABLE IF EXISTS {$dbName}.{$tableName}");
$pdo->exec("
CREATE TABLE {$dbName}.{$tableName} (
    handle varchar(64) NOT NULL,
    system_sys0 tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
    system_sys1 tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
    system_sys2 tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
    system_sys3 tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
    system_sys4 tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (handle)
);
");
unset($pdo);

TestCase::initialize($connectionCreator);
