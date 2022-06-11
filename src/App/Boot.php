<?php
namespace App;

class Boot {
    private $title;
    private $cssFiles = [];
    private $jsFiles = [];

    /**
     * Запускает отрисовку страницы
     */
    public function start() {
        $routeData = $this->getRouteData();

        if(!isset($routeData['view']) && empty($routeData['view'])) {
            header('Content-Type: application/json');
            print_r(json_encode($routeData, JSON_UNESCAPED_UNICODE));
            return;
        }
        if(isset($routeData['parameters']) && !empty($routeData['parameters'])) extract($routeData['parameters']);

        $header = '<!doctype html><html lang="en">';
        $header .= $this->getHeadHTML();
        $header .= '<body>';
        $header .= $this->getHeaderHTML();
        $body = $this->getBodyHTML($routeData['view']);
        $footer = $this->getFooterHTML();
        $footer .= '</body></html>';

        ob_start();
        try {
            $html = $header . $body . $footer;
            eval('?>' . $html . '<?');
        } catch (\Throwable $data) {
            $this->cssFiles = [];
            $this->jsFiles = [];
            ob_clean();
            $body = $this->getErrorPageHTML(500);
            $html = $header . $body . $footer;
            eval('?>' . $html . '<?');
        }
        $this->addTags();
    }

    /**
     * Устанавливает заголовок страницы
     *
     * @param $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Подключает css файлы
     *
     * @param $path
     */
    public function addCss($path) {
        $this->cssFiles[] = $path;
    }

    /**
     * Подключает js файлы
     *
     * @param $path
     */
    public function addJs($path) {
        $this->jsFiles[] = $path;
    }

    /**
     * Добавляет в сгенерированный html подключение css и js скриптов, а так же выводит title
     */
    private function addTags() {
        $buffer = ob_get_contents();
        ob_clean();

        $css = '';
        foreach ($this->cssFiles as $cssFilePath) {
            $css .= '<link href="' . $cssFilePath . '" rel="stylesheet">';
        }

        $js = '';
        foreach ($this->jsFiles as $jsFilePath) {
            $js .= '<script src="' . $jsFilePath . '"></script>';
        }

        $buffer = str_replace('</head>', $css . '</head>', $buffer);
        $buffer = str_replace('</body>', $js . '</body>', $buffer);
        $buffer = str_replace('<title>', '<title>' . $this->title, $buffer);
        print_r($buffer);
    }

    /**
     * Возвращает html код тега head
     *
     * @return string
     */
    private function getHeadHTML() {
        $html = '<head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">';
        $html .= '<meta http-equiv="X-UA-Compatible" content="ie=edge">';
        $html .= '<title></title>';
        $html .= '</head>';

        return $html;
    }

    /**
     * Возвращает html код шапки сайта
     *
     * @return string
     */
    private function getHeaderHTML() {
        return file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/template/header.php');
    }

    /**
     * Возвращает html код страницы сайта
     *
     * @param $view - шаблон страницы
     * @return false|string
     */
    private function getBodyHTML($view) {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/template/pages/' . $view . '.php';

        if (!file_exists($filePath)) {
            http_response_code(404);
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/template/404.php';
        }

        return file_get_contents($filePath);
    }

    /**
     * Возвращает результат работы метода контроллера
     *
     * @return array|mixed
     */
    private function getRouteData() {
        $routes = include $_SERVER['DOCUMENT_ROOT'] . '/src/routes.php';
        $url = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
        if(!isset($routes[$url])){
            return ['view' => '', 'parameters' => []];
        }

        $handler = explode('@', $routes[$url]);
        $class = $handler[0];
        $func = $handler[1];
        $ob = new $class();

        if (!method_exists($class, $func)) {
            throw new \Error('Method "' . $func . '" does not exist in ' . $class);
        }

        return call_user_func([$ob, $func], $_REQUEST);
    }

    /**
     * Возвращает HTML страницы ошибки
     *
     * @param $errorCode
     * @return false|string
     */
    private function getErrorPageHTML($errorCode) {
        http_response_code($errorCode);
        return file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/template/' . $errorCode . '.php');
    }

    /**
     * Возвращает html код подвала сайта
     *
     * @return string
     */
    private function getFooterHTML() {
        return file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/template/footer.php');
    }
}