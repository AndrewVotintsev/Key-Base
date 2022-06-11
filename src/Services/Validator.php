<?php
namespace Services;

use DateTime;
use DateInterval;

class Validator {

    private $fails = false;
    private $messages;

    private function __construct() {
        $this->messages = include($_SERVER['DOCUMENT_ROOT'] . '/lang/validation.php');
    }

    /**
     * Метод выполняет проверку полей по правилам
     *
     * @param $data - массив полей
     * @param $rules - массив правил
     * @return Validator
     */
    public static function make($data, $rules) {
        $ob = new Validator();
        foreach ($rules as $key => $item) {
            $functions = explode('|', $item);
            foreach ($functions as $function) {
                if(isset($ob->fails[$key])) continue;

                $condition_exist = explode(':', $function);
                $value = (is_string($data[$key])) ? trim($data[$key]) : $data[$key];

                if (count($condition_exist) > 1) {
                    $function = $condition_exist[0];
                    $condition = $condition_exist[1];
                    $ob->$function($value, $key, $condition);
                } else {
                    $ob->$function($value, $key);
                }
            }
        }
        return $ob;
    }

    /**
     * Метод возвращает ошибки валидации
     *
     * @return bool
     */
    public function fails() {
        return $this->fails;
    }

    /**
     * Проверка переменной на пустоту исключая 0
     *
     * @param $value
     * @return bool
     */
    private function isEmpty($value) {
        return empty($value) && $value !== 0 && $value !== '0';
    }

    /**
     * Метод проверяет на пустое значение
     *
     * @param $value
     * @param $key
     */
    private function required($value, $key) {
        if ($this->isEmpty($value) && !isset($this->fails[$key])) {
            $this->fails[$key] = $this->messages['required'];
        }
    }

    /**
     * Метод проверяет значение на числовое значение
     *
     * @param $value
     * @param $key
     */
    private function numeric($value, $key) {
        if (!is_numeric($value) && !$this->isEmpty($value) && !isset($this->fails[$key])) {
            $this->fails[$key] = $this->messages['numeric'];
        }
    }

    /**
     * Метод проверяет является ли значение email
     *
     * @param $value
     * @param $key
     */
    private function email($value, $key) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !$this->isEmpty($value) && !isset($this->fails[$key])) {
            $this->fails[$key] = $this->messages['email'];
        }
    }

    /**
     * Метод проверяет является ли значение массивом
     *
     * @param $value
     * @param $key
     */
    private function array($value, $key) {
        if (!is_array($value) && !$this->isEmpty($value) && !isset($this->fails[$key])) {
            $this->fails[$key] = $this->messages['array'];
        }
    }

    /**
     * Метод проверяет является ли значение строкой
     *
     * @param $value
     * @param $key
     */
    private function string($value, $key) {
        if (!is_string($value) && !$this->isEmpty($value) && !isset($this->fails[$key])) {
            $this->fails[$key] = $this->messages['string'];
        }
    }

    /**
     * Метод проверяет все ли символы в строке русские буквы
     *
     * @param $value
     * @param $key
     */
    private function only_letters_ru($value, $key) {
        $regex_letters = '/^[А-ЯЁа-яё\s]+$/u';
        if (!preg_match($regex_letters, $value) && !$this->isEmpty($value) && !isset($this->fails[$key])) {
            $this->fails[$key] = $this->messages['only_letters_ru'];
        }
    }

    /**
     * Метод проверяет дату на соответствие определенному формату
     *
     * @param $value
     * @param $key
     * @param $format
     */
    private function date_format($value, $key, $format) {
        $date = DateTime::createFromFormat($format, $value);
        if (!$date && !$this->isEmpty($value) && !isset($this->fails[$key])) {
            $this->fails[$key] = str_replace(':values', $format, $this->messages['date_format']);
        }
    }

    /**
     * Метод проверяет дату на попадание в диапазон
     *
     * @param $value
     * @param $key
     * @param $range
     * @throws \Exception
     */
    private function date_range($value, $key, $range) {
        $value = new DateTime($value);
        $range = explode(',', $range);
        $range = [
            'sub' => $range[0],
            'add' => $range[1],
        ];
        $past = (new DateTime())->sub(new DateInterval('P' . $range['sub'] . 'Y'));
        $future = (new DateTime())->add(new DateInterval('P' . $range['add'] . 'Y'));

        if (!$this->isEmpty($value) && ($past > $value || $value > $future)) {
            $replace_min = str_replace(':min', $past->format('d.m.Y'), $this->messages['date_range']);
            $this->fails[$key] = str_replace(':max', $future->format('d.m.Y'), $replace_min);
        }
    }

    /**
     * Метод для проверки значения типа boolean
     *
     * @param $value
     * @param $key
     */
    private function boolean($value, $key) {
        if (!is_bool($value) && !$this->isEmpty($value) && !isset($this->fails[$key])) {
            $this->fails[$key] = $this->messages['boolean'];
        }
    }

    /**
     * Метод проверяет значение типа boolean на соответствие определенному значению
     *
     * @param $value
     * @param $key
     * @param $equal
     */
    private function boolean_equals($value, $key, $equal) {
        $this->boolean($value, $key);
        if ((filter_var($equal, FILTER_VALIDATE_BOOLEAN) !== $value) && !isset($this->fails[$key])) {
            $this->fails[$key] = str_replace(':values', $equal, $this->messages['boolean_equals']);
        }
    }

    /**
     * Метод проверяет чтобы количество символов в строке было не больше чем $max
     *
     * @param $value
     * @param $key
     * @param $max
     */
    private function max($value, $key, $max) {
        if (strlen($value) > (int)$max && !isset($this->fails[$key]) && !$this->isEmpty($value)) {
            $this->fails[$key] = str_replace(':values', $max, $this->messages['max']);
        }
    }

    /**
     * Метод проверяет попадает ли число в диапазон между min и max
     *
     * @param $value
     * @param $key
     * @param $range
     */
    private function between($value, $key, $range) {
        $range = explode(',', $range);
        $range = [
            'min' => $range[0],
            'max' => $range[1],
        ];

        if (((int)$range['min'] > (int)$value || (int)$range['max'] < (int)$value) && !$this->isEmpty($value) && !isset($this->fails[$key])) {
            $replace_min = str_replace(':min', $range['min'], $this->messages['between']);
            $this->fails[$key] = str_replace(':max', $range['max'], $replace_min);
        }
    }
    
    /**
     * Метод проверяет чтобы количество символов в строке было не меньше чем $min
     *
     * @param $value
     * @param $key
     * @param $min
     */
    private function min($value, $key, $min) {
        if (strlen($value) < (int)$min && !isset($this->fails[$key]) && !$this->isEmpty($value)) {
            $this->fails[$key] = str_replace(':values', $min, $this->messages['min']);
        }
    }


    private function only($value, $key, $comparing) {
        $comparing = explode(',', $comparing);
        if(!in_array($value, $comparing, true)){
            $this->fails[$key] = str_replace(':values', implode(', ', $comparing), $this->messages['only']);
        }
    }
}