<?php
namespace App\Models;

use PDO;
use PDOException;

/**
 * Clase Model para interactuar con la base de datos usando PDO
 */
class Model
{
    /**
     * @var string Host de la base de datos
     */
    protected $db_host = DB_HOST;

    /**
     * @var string Usuario de la base de datos
     */
    protected $db_user = DB_USER;

    /**
     * @var string Contraseña de la base de datos
     */
    protected $db_pass = DB_PASS;

    /**
     * @var string Nombre de la base de datos
     */
    protected $db_name = DB_NAME;

    /**
     * @var string Nombre de la tabla
     */
    protected $table;

    /**
     * @var PDO Conexión a la base de datos
     */
    protected $connection;

    /**
     * @var PDOStatement Consulta SQL preparada
     */
    protected $query;

    /**
     * @var array Condiciones WHERE para las consultas
     */
    protected $conditions = [];

    /**
     * @var array Valores de los parámetros de las condiciones
     */
    protected $bindings = [];

    /**
     * @var PDOStatement Resultado de la consulta
     */
    protected $result;

    /**
     * Constructor de la clase. Establece la conexión con la base de datos.
     */
    public function __construct()
    {
        $this->connection();
    }

    /**
     * Establece la conexión con la base de datos.
     */
    public function connection()
    {
        try {
            $this->connection = new PDO("mysql:host={$this->db_host};dbname={$this->db_name}", $this->db_user, $this->db_pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Ejecuta una consulta SQL.
     *
     * @param string $sql Consulta SQL a ejecutar
     * @param array $params Parámetros para la consulta
     * @return $this
     */
    public function query($sql, $params = [])
    {
        $this->query = $this->connection->prepare($sql);
        foreach ($params as $key => &$val) {
            $this->query->bindParam($key, $val);
        }
        $this->query->execute();
        $this->result = $this->query;
        return $this;
    }

    /**
     * Encuentra un registro por su ID.
     *
     * @param int $id ID del registro a buscar
     * @return array|null Arreglo asociativo del registro encontrado o null si no se encuentra
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE _id = :id";
        return $this->query($sql, [':id' => $id])->first();
    }

    /**
     * Obtiene el primer resultado de la consulta.
     *
     * @return array|null Arreglo asociativo del primer resultado o null si no hay resultados
     */
    public function first()
    {
        return $this->result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los registros de la tabla.
     *
     * @return array Arreglo asociativo con todos los registros de la tabla
     */
    public function all()
    {
        return $this->get();
    }

    /**
     * Obtiene todos los resultados de la consulta.
     *
     * @return array Arreglo asociativo con todos los resultados
     */
    public function get()
    {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->conditions);
        }
        return $this->query($sql, $this->bindings)->result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el primer resultado de la consulta con condiciones.
     *
     * @return array|null Arreglo asociativo del primer resultado o null si no hay resultados
     */
    public function getFirst()
    {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->conditions);
        }
        return $this->query($sql, $this->bindings)->first();
    }

    /**
     * Agrega una condición WHERE a la consulta.
     *
     * @param string $column Nombre de la columna
     * @param string $operator Operador de comparación
     * @param mixed $value Valor de comparación
     * @return $this
     */
    public function where($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $placeholder = ':' . $column . count($this->bindings);
        $this->conditions[] = "{$column} {$operator} {$placeholder}";
        $this->bindings[$placeholder] = $value;
        return $this;
    }

    /**
     * Crea un nuevo registro en la tabla.
     *
     * @param array $data Datos del nuevo registro
     * @return array|null Arreglo asociativo del registro creado o null si falla
     */
    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $this->query($sql, $data);
        $insert_id = $this->connection->lastInsertId();
        return $this->find($insert_id);
    }

    /**
     * Actualiza un registro en la tabla.
     *
     * @param int $id ID del registro a actualizar
     * @param array $data Datos a actualizar
     * @return array|null Arreglo asociativo del registro actualizado o null si falla
     */
    public function update($id, $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $fields = implode(', ', $fields);
        $sql = "UPDATE {$this->table} SET $fields WHERE _id = :id";
        $data['id'] = $id;

        $this->query($sql, $data);
        return $this->find($id);
    }

    /**
     * Elimina un registro de la tabla.
     *
     * @param int $id ID del registro a eliminar
     * @return int Número de filas afectadas
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE _id = :id";
        $this->query($sql, [':id' => $id]);
        return $this->query->rowCount();
    }
}

