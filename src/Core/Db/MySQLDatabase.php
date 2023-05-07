<?php

namespace MyBlog\Core\Db;

class MySQLDatabase implements DatabaseInterface
{
    private static ?IDatabase $db = null;
    private \PDO $pdo;

    private function __construct() {}


    public static function connect(): self
    {
        if(self::$db == null) {
            self::$db = new self();
        }

        return self::$db;
    }


    private function connection(array $connectionInfo)
    {
        $host = $connectionInfo['host'];
        $db = $connectionInfo['db'];
        $charset = $connectionInfo['charset'];
        $pass = $connectionInfo['pass'];
        $user = $connectionInfo['user'];

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opts = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // подключение к базе
        return new \PDO($dsn, $user, $pass, $opts);
    }


    public function select(string $sql, array $params, string $class)
    {
        $stmt =  $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchObject($class);
    }


    public function insert2(string $sql, $bound_data)
    {
        //$sql = 'INSERT INTO news (title, content) VALUES (:title, :content)';
        $statement = $this->pdo->prepare($sql);
        //$data = [':title' => $title, ':content' => $content];
    
        return $statement->execute($bound_data);
    }

    public function table(string $table)
    {
        // TODO: Implement table() method.
    }

    public function getAll(int $limit)
    {
        // TODO: Implement getAll() method.
    }

    public function update($id, $data)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
}