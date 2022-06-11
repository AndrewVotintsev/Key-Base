<?php
namespace App;

use PDO;

class DB {
    private $DB;

    public function __construct() {
        $phinxConfig = include $_SERVER['DOCUMENT_ROOT'] . '/phinx.php';
        $defaultEnvironment = $phinxConfig['environments']['default_environment'];
        $baseName = $phinxConfig['environments'][$defaultEnvironment]['name'];
        $userName = $phinxConfig['environments'][$defaultEnvironment]['user'];
        $userPass = $phinxConfig['environments'][$defaultEnvironment]['pass'];
        $adapter = $phinxConfig['environments'][$defaultEnvironment]['adapter'];
        $host = $phinxConfig['environments'][$defaultEnvironment]['host'];

        $dsn = $adapter . ':host=' . $host . ';dbname=' . $baseName;
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->DB = new PDO($dsn, $userName, $userPass, $opt);
    }

    /**
     * Добавляет запись в таблицу
     *
     * @param string $table
     * @param string $columns
     * @param string $values
     * @return bool|false|int
     */
    public function add(string $table, string $columns, string $values) {
        try {
            return $this->DB->exec("INSERT INTO $table ($columns) VALUES ($values)");
        } catch (\PDOException $exception) {
            return false;
        }
    }

    /**
     * Обновляет запись в таблице
     *
     * @param string $table
     * @param int $id
     * @param string $data
     * @return bool|false|int
     */
    public function update(string $table, int $id, string $data) {
        try {
            return $this->DB->exec("UPDATE $table SET $data WHERE id=$id");
        } catch (\PDOException $exception) {
            return false;
        }
    }

    /**
     * Удаляет запись из таблицы
     *
     * @param string $table
     * @param int $id
     * @return bool
     */
    public function delete(string $table, int $id) {
        try {
            return $this->DB->prepare("DELETE FROM $table WHERE id=?")->execute([$id]);
        } catch (\PDOException $exception) {
            return false;
        }
    }

    /**
     * Возвращает запись по фильтру
     *
     * @param string $table
     * @param string $name
     * @param string $value
     * @return mixed
     */
    public function where(string $table, string $name, string $value) {
        return $this->DB->query("SELECT * FROM $table WHERE $name=$value")->fetch();
    }

    /**
     * Возвращает связанные записи из таблиц
     *
     * @param string $relatedTable - связанная таблица
     * @param string $foreignRelatedKey - внешний ключ связанной таблицы
     * @param string $parentTable - родительская таблица
     * @param string $parentKey - первичный ключ родительской таблица
     * @param string $value - значение первичного ключа родительской таблицы
     * @return array
     */
    public function getItems(string $relatedTable, string $foreignRelatedKey, string $parentTable, string $parentKey, string $value) {
        $sql = "SELECT $relatedTable.* FROM $relatedTable INNER JOIN $parentTable ON $parentTable.$parentKey=$relatedTable.$foreignRelatedKey AND $parentTable.$parentKey=$value";
        return $this->DB->query($sql)->fetchAll();
    }

    /**
     * Ищет элементы через промежуточную таблицу
     *
     * @param array $data - массив содержащий параметры:
     *      - relatedTable - связанная таблица
     *      - relatedKey - первичный ключ связанной таблицы
     *      - crossTable - промежуточная таблица
     *      - foreignRelatedKey - внешний ключ для связанной таблицы
     *      - foreignParentKey - внешний ключ для родительской таблицы
     *      - parentKey - первичный ключ для родительской таблицы
     *      - parentTable - родительская таблица
     *      - value - значение первичного ключа родительской таблицы
     *
     * @return array
     */
    public function getItemsByCrossTab(array $data) {
        extract($data);
        if(isset($relatedTable) && isset($crossTable) && isset($relatedKey) && isset($foreignRelatedKey) && isset($parentTable) && isset($parentKey) && isset($foreignParentKey) && isset($value)) {
            $sql = "SELECT $relatedTable.* FROM $relatedTable 
                    INNER JOIN $crossTable ON $relatedTable.$relatedKey=$crossTable.$foreignRelatedKey 
                    INNER JOIN $parentTable ON $parentTable.$parentKey=$crossTable.$foreignParentKey 
                    AND $parentTable.$parentKey=$value";
            return $this->DB->query($sql)->fetchAll();
        }
        return [];
    }

    /**
     * Возвращает все записи из таблицы
     *
     * @param string $table
     * @return array
     */
    public function getList(string $table) {
        return $this->DB->query("SELECT * FROM $table")->fetchAll();
    }

    /**
     * Возвращает запись по id
     *
     * @param string $table
     * @param int $id
     * @return mixed
     */
    public function getItem(string $table, int $id) {
        return $this->DB->query("SELECT * FROM $table WHERE id=$id")->fetch();
    }
}