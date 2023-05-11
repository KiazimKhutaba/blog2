<?php

namespace MyBlog\Core\Db;


use Closure;

class SQLiteDatabase implements DatabaseInterface
{
    private \PDO $db;
    private static ?self $instance = null;
    public string $table;


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
     * @param Closure|null $convertor
     * @param int $fetchMode
     * @return bool|array|object
     */
    public function query(string $sql, array $params, Closure $convertor = null, int $fetchMode = \PDO::FETCH_ASSOC): mixed
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        $resultSet = $statement->fetchAll($fetchMode);

        if (!$resultSet || count($resultSet) === 0) {
            return false;
        }

        return $convertor !== null ? $convertor($resultSet): $resultSet;
    }


    /**
     * @param string $sql
     * @param array $params
     * @param Closure|null $convertor
     * @param int $fetchMode
     * @return mixed
     */
    public function queryEx(string $sql, array $params, Closure $convertor = null, int $fetchMode = \PDO::FETCH_ASSOC): mixed
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        $rows = $statement->fetchAll($fetchMode);

        if (!$rows || count($rows) === 0) {
            return false;
        }

        if($convertor !== null) {
            return array_map(static fn($row) => $convertor($row), $rows);
        }

        return $rows;
    }

    public function queryOne(string $sql, array $params, Closure $convertor = null, int $fetchMode = \PDO::FETCH_ASSOC): mixed
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        $row = $statement->fetch($fetchMode);

        if (!$row || count($row) === 0) {
            return false;
        }

        return $convertor !== null ? $convertor($row): $row;
    }


    public function get(int|string $id, string $table): array
    {
        $sql = sprintf("SELECT * FROM %s WHERE id = :id", $table);
        $statement = $this->db->prepare($sql);

        //$statement->bindParam(":id", $id);
        $statement->execute([
            ':id' => $id
        ]);

        return $statement->fetch(\PDO::FETCH_ASSOC) ?: [];
    }

    public function getAll(int $limit, string $table, Closure $convertor = null): array
    {
        $sql = "SELECT * FROM $table ORDER BY created_at LIMIT 0, :limit";
        $statement = $this->db->prepare($sql);
;
        $statement->execute([':limit' => $limit]);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $convertor !== null ? $convertor($rows) : $rows;
    }

    public function insert(array $data, string $table): int
    {
        $keys = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, $keys, $values);

        //throw new \Exception($sql);

        $statement = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $statement->bindValue(":$key", $value);
            //$params[":$key"] = $value;
        }

        return $statement->execute() ? $this->db->lastInsertId() : 0;
    }

    public function update(int|string $id, array $data, string $table): int
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }

        $set = implode(", ", $set);
        $sql = sprintf("UPDATE %s SET $set WHERE id = :id", $table);
        //throw new \Exception($sql);

        $statement = $this->db->prepare($sql);
        $statement->bindParam(":id", $id);

        foreach ($data as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        return $statement->execute();
    }

    public function delete(int|string $id, string $table): bool
    {
        $sql = sprintf('DELETE FROM %s WHERE id = :id', $table);
        $statement = $this->db->prepare($sql);
        return $statement->execute([':id' => $id]);
    }
}