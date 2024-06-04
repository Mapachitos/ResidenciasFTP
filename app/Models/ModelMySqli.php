<?php
namespace App\Models;
use mysqli;

/**
 * Clase ModelMySqli para interactuar con la base de datos usando MySQLi
 */
class ModelMySqli {
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
     * @var mysqli Conexión a la base de datos
     */
    protected $connection;

    /**
     * @var string Consulta SQL
     */
    protected $query;

    /**
     * @var array Condiciones WHERE para las consultas
     */
    protected $conditions = [];

    /**
     * @var mixed Resultado de la consulta
     */
    protected $result;

    /**
     * Constructor de la clase. Establece la conexión con la base de datos.
     */
    public function __construct() {
        $this->connection();
    }

    /**
     * Establece la conexión con la base de datos.
     */
    public function connection() {
        $this->connection = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
        if ($this->connection->connect_error) {
            die('Connection failed: ' . $this->connection->connect_error);
        }
    }

    /**
     * Ejecuta una consulta SQL.
     *
     * @param string $sql Consulta SQL a ejecutar
     * @return $this
     */
    public function query($sql) {
        $this->result = $this->connection->query($sql);
        return $this;
    }

    /**
     * Obtiene el primer resultado de la consulta.
     *
     * @return array|null Arreglo asociativo del primer resultado o null si no hay resultados
     */
    public function first() {
        return $this->result->fetch_assoc();
    }

    /**
     * Obtiene todos los resultados de la consulta.
     *
     * @return array Arreglo asociativo con todos los resultados
     */
    public function get() {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->conditions);
        }
        $this->query($sql);
        return $this->result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene todos los registros de la tabla.
     *
     * @return array Arreglo asociativo con todos los registros de la tabla
     */
    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->query($sql)->get();
    }

    /**
     * Encuentra un registro por su ID.
     *
     * @param int $id ID del registro a buscar
     * @return array|null Arreglo asociativo del registro encontrado o null si no se encuentra
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE _id = $id";
        return $this->query($sql)->first();
    }

    /**
     * Agrega una condición WHERE a la consulta.
     *
     * @param string $column Nombre de la columna
     * @param string $operator Operador de comparación
     * @param mixed $value Valor de comparación
     * @return $this
     */
    public function where($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $this->conditions[] = "{$column} {$operator} '{$value}'";
        return $this;
    }

    /**
     * Crea un nuevo registro en la tabla.
     *
     * @param array $data Datos del nuevo registro
     * @return array|null Arreglo asociativo del registro creado o null si falla
     */
    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
        $this->query($sql);
        $insert_id = $this->connection->insert_id;
        return $this->find($insert_id);
    }

    /**
     * Actualiza un registro en la tabla.
     *
     * @param int $id ID del registro a actualizar
     * @param array $data Datos a actualizar
     * @return array|null Arreglo asociativo del registro actualizado o null si falla
     */
    public function update($id, $data) {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = '{$value}'";
        }
        $fields = implode(', ', $fields);
        $sql = "UPDATE {$this->table} SET {$fields} WHERE _id = {$id}";
        $this->query($sql);
        return $this->find($id);
    }

    /**
     * Elimina un registro de la tabla.
     *
     * @param int $id ID del registro a eliminar
     * @return int Número de filas afectadas
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE _id = {$id}";
        $this->query($sql);
        return $this->connection->affected_rows;
    }
}
