<?php

namespace App\Traits;

use PDOException;

trait Read
{
    public function find($fetchAll = true)
    {
        try {
            $query = $this->connection->query("SELECT * FROM {$this->table}");
            $result = $fetchAll ? $query->fetchAll() : $query->fetch();
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            // Tratar a exceção de forma apropriada
            throw new \Exception("Erro ao buscar dados: " . $e->getMessage());
        }
    }

    public function findBy($field, $value, $fetchAll = false)
    {
        try {
            $prepared = $this->connection->prepare("SELECT * FROM {$this->table} WHERE {$field} = :{$field}");
            $prepared->bindValue(":{$field}", $value);
            $prepared->execute();
            $result = $fetchAll ? $prepared->fetchAll() : $prepared->fetch();
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            // Tratar a exceção de forma apropriada
            throw new \Exception("Erro ao buscar dados: " . $e->getMessage());
        }
    }
}
