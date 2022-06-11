<?require_once 'src/autoload.php';

global $APPLICATION;

$APPLICATION = new \App\Boot();
$APPLICATION->start();
