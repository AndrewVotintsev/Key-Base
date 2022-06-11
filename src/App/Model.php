<?php
namespace App;

abstract class Model {
    /**
     * Поле для обозначения таблицы с которой работает модель
     * @var string
     */
    protected $table = '';

    /**
     * Массив значений объекта модели
     * @var array
     */
    private $values = [];

    /**
     * Model constructor.
     */
    public function __construct() {
        if (empty($this->table)) {
            $arr = preg_split('/(?<=[a-z])(?=[A-Z])/u', basename(str_replace('\\', '/', static::class)));
            $string = implode('_', $arr);
            $this->table = strtolower($string);
        }
    }

    /**
     * Возвращает связанные элементы в обратном отношении
     *
     * @param string $related - класс модели связанной таблицы
     * @param string $foreignKey - внешний ключ
     * @param string $ownerKey - первичный ключ
     * @return array|mixed
     */
    protected function belongsTo(string $related, string $foreignKey, string $ownerKey) {
        $object = new $related;
        $db = new DB();
        $result = $db->getItems($object->table, $ownerKey, $this->table, $foreignKey, $this->values[$foreignKey]);
        $object->values = (!empty($result)) ? $result[0] : [];
        return $object;
    }

    /**
     * Возвращает связанные элементы в отношении многие ко многим
     *
     * @param string $related - класс модели связанной таблицы
     * @param string $relatedKey - первичный ключ связанной таблицы
     * @param string $crossTable - промежуточная таблица
     * @param string $foreignRelatedKey - внешний ключ для связанной таблицы
     * @param string $foreignParentKey - внешний ключ для родительской таблицы
     * @param string $parentKey - первичный ключ для родительской таблицы
     * @return Collection
     */
    protected function belongsToMany(string $related, string $relatedKey, string $crossTable, string $foreignRelatedKey, string $foreignParentKey, string $parentKey = 'id') {
        $relatedTable = (new $related)->table; // связанная таблица
        $parentTable = $this->table; // родительская таблица
        $value = $this->values[$parentKey]; // значение первичного ключа родительской таблицы
        $db = new DB();
        $data = compact('relatedTable', 'relatedKey', 'foreignRelatedKey', 'parentTable', 'parentKey', 'foreignParentKey', 'value', 'crossTable');
        $array = $db->getItemsByCrossTab($data);
        $collection = new Collection();
        foreach ($array as $item) {
            $itemObject = new static();
            $itemObject->values = $item;

            $collection->collection[] = $itemObject;
        }

        return $collection;
    }

    /**
     * Возвращает связанные элементы в отношении один ко многим
     *
     * @param string $related - класс модели связанной таблицы
     * @param string $foreignKey - внешний ключ
     * @param string $localKey - первичный ключ
     * @return Collection
     */
    protected function hasMany(string $related, string $foreignKey, string $localKey) {
        $object = new $related;
        $db = new DB();
        $array = $db->getItems($object->table, $foreignKey, $this->table, $localKey, $this->values[$localKey]);
        $collection = new Collection();
        foreach ($array as $item) {
            $itemObject = new static();
            $itemObject->values = $item;

            $collection->collection[] = $itemObject;
        }

        return $collection;
    }

    /**
     * Возвращает связанные элементы в отношении один к одному
     *
     * @param string $related - класс модели связанной таблицы
     * @param string $foreignKey - внешний ключ
     * @param string $localKey - первичный ключ
     * @return array|mixed
     */
    protected function hasOne(string $related, string $foreignKey, string $localKey) {
        $object = new $related;
        $db = new DB();
        $result = $db->getItems($object->table, $localKey, $this->table, $foreignKey, $this->values[$foreignKey]);
        $object->values = (!empty($result)) ? $result[0] : [];
        return $object;
    }

    /**
     * Возвращает коллекцию элементов
     *
     * @return Collection
     */
    public static function all() {
        $db = new DB();
        $object = new static();
        $array = $db->getList($object->table);
        $collection = new Collection();
        foreach ($array as $item) {
            $itemObject = new static();
            $itemObject->values = $item;

            $collection->collection[] = $itemObject;
        }

        return $collection;
    }

    /**
     * Возвращает объект модели с элементом
     *
     * @param int $id
     * @return static
     */
    public static function find(int $id) {
        $db = new DB();
        $object = new static();
        $object->values = $db->getItem($object->table, $id);
        return $object;
    }

    /**
     * Возвращает массив значений объекта модели
     *
     * @return array
     */
    public function get() {
        return $this->values;
    }

    /**
     * Добавляет элемент
     *
     * @param array $params
     * @return bool|false|int
     */
    public static function add(array $params) {
        $arColumns = [];
        $arValues = [];

        foreach ($params as $name => &$value) {
            $value = trim($value); // удаляет лишние пробелы
            $value = htmlspecialchars($value); // заменяет спецсимволы html
            $value = addslashes($value); // экранирует кавычки и спецсимволы
            $arColumns[] = "`" . $name . "`";
            $arValues[] = "'" . $value . "'";
        }
        unset($value);

        $db = new DB();
        $object = new static();
        $strColumns = implode(', ', $arColumns);
        $strValues = implode(', ', $arValues);

        return $db->add($object->table, $strColumns, $strValues);
    }

    /**
     * Возвращает элемент по фильтру
     *
     * @param string $key
     * @param string $value
     * @return static
     */
    public static function where(string $key, string $value) {
        $value = trim($value); // удаляет лишние пробелы
        $value = htmlspecialchars($value); // заменяет спецсимволы html
        $value = addslashes($value); // экранирует кавычки и спецсимволы

        $db = new DB();
        $object = new static();
        $object->values = $db->where($object->table, $key, $value);
        return $object;
    }

    /**
     * Удаляет элемент по id
     *
     * @param int $id
     * @return bool
     */
    public static function delete(int $id) {
        $id = trim($id); // удаляет лишние пробелы
        $id = htmlspecialchars($id); // заменяет спецсимволы html
        $id = addslashes($id); // экранирует кавычки и спецсимволы

        $db = new DB();
        $object = new static();
        return $db->delete($object->table, $id);
    }

    /**
     * Обновляет данные элемента
     *
     * @param int $id
     * @param array $params
     * @return bool
     */
    public static function update(int $id, array $params) {
        $arParams = [];

        foreach ($params as $name => &$value) {
            if(is_bool($value) === true) $value = (int)$value;
            $value = trim($value); // удаляет лишние пробелы
            $value = htmlspecialchars($value); // заменяет спецсимволы html
            $value = addslashes($value); // экранирует кавычки и спецсимволы
            $arParams[] .= $name . '=' . "'" . $value . "'";
        }
        unset($value);

        $db = new DB();
        $object = new static();
        $strParams = implode(', ', $arParams);

        return $db->update($object->table, $id, $strParams);
    }
}