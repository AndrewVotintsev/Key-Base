<?php

/**
 * Подключение шаблона страницы
 *
 * @param string $name
 * @param array $variables
 * @return array
 */
function template(string $name, array $variables) {
    return [
        'view' => $name,
        'parameters' => $variables
    ];
}
