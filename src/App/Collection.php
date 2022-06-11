<?php
namespace App;

class Collection {
    /**
     * Массив элементов коллекции
     * @var array
     */
    public $collection = [];

    /**
     * Возвращает массив массивов из коллекции
     *
     * @return array
     */
    public function get() {
        $result = [];
        foreach ($this->collection as $collectionItem) {
            $result[] = $collectionItem->get();
        }

        return $result;
    }

    /**
     * Возвращает коллекцию элементов
     *
     * @return array
     */
    public function getCollection() {
        return $this->collection;
    }
}