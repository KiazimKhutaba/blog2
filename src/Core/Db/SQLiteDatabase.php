<?php

namespace MyBlog\Core\Db;


class SQLiteDatabase implements DatabaseInterface
{
    private \PDO $db;
    private static ?self $instance = null;
    private string $table;


    private function __construct(string $pathToDb)
    {
        $this->db = new \PDO("sqlite:" . $pathToDb);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }


    public static function connect(string $dbFile): self
    {
        if (self::$instance == null) {
            self::$instance = new self($dbFile);
        }

        return self::$instance;
    }

    public function table(string $table): void
    {
        $this->table = $table;
    }


    /*public function select(string $sql, array $params, string $class)
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchObject($class);
    }


    public function selectAll(string $sql, array $params, string $class)
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll(\PDO::FETCH_CLASS, $class);
    }*/

    /**
     * @param string $sql
     * @param array $params
     * @param \Closure|null $convertor
     * @param int $fetchMode
     * @return bool|array|object
     */
    public function query(string $sql, array $params, \Closure $convertor = null, int $fetchMode = \PDO::FETCH_ASSOC): mixed
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        $resultSet = $statement->fetchAll($fetchMode);

        if (!$resultSet || count($resultSet) === 0) {
            return false;
        }

        return $convertor !== null ? $convertor($resultSet): $resultSet;
    }


    public function queryOne(string $sql, array $params, \Closure $convertor = null, int $fetchMode = \PDO::FETCH_ASSOC): mixed
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        $resultSet = $statement->fetch($fetchMode);

        if (!$resultSet || count($resultSet) === 0) {
            return false;
        }

        return $convertor !== null ? $convertor($resultSet): $resultSet;
    }


    public function get($id)
    {
        $sql = sprintf("SELECT * FROM %s WHERE id = :id", $this->table);
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getAll(int $limit = 3): array
    {
        $sql = "SELECT * FROM $this->table LIMIT :limit";
        $statement = $this->db->prepare($sql);

        //throw  new \Exception($sql);

        //$statement->bindParam(":sort_order", $sort_order);
        $statement->bindValue(":limit", $limit);
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function insert($data): int
    {
        $keys = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $this->table, $keys, $values);

        //throw new \Exception($sql);

        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute() ? $this->db->lastInsertId() : 0;
    }

    public function update($id, $data): int
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }

        $set = implode(", ", $set);
        $sql = "UPDATE $this->table SET $set WHERE id = :id";
        //throw new \Exception($sql);

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    public function delete($id): bool
    {
        $sql = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        //$stmt->bindParam(":id", $id);
        return $stmt->execute([':id' => $id]);
    }
}