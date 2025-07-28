<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

define('APPLICATION_TOP_PATH', realpath(dirname(__FILE__).'/..'));
define('FROM_CRON', php_sapi_name()=='cli');

$config=array_merge(require_once(APPLICATION_TOP_PATH.'/config/global.php'), require_once(APPLICATION_TOP_PATH.'/config/local.php'));

error_reporting($config['env']==='SERVER' ? E_ERROR : E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);

session_start();

require_once(APPLICATION_TOP_PATH.'/vendor/autoload.php');

try
{
    $regular_em=EntityManager::create(array(
        'driver'   => 'pdo_mysql',
        'host'     => $config['db']['host'],
        'user'     => $config['db']['user'],
        'password' => $config['db']['password'],
        'dbname'   => $config['db']['database'],
    ), Setup::createAnnotationMetadataConfiguration(array(APPLICATION_TOP_PATH."/model/Entity"), $config['env']==='LOCAL', sys_get_temp_dir().'/pediatr_doctrine_proxy', null, false));

    /** @var EntityManager|ExtendedEntityManager $em */
    $em = new ExtendedEntityManager($regular_em);

    $em->getConnection()->exec("SET NAMES utf8mb4;");
}
catch (Exception $e)
{
    die($e->getMessage());
}

date_default_timezone_set('Europe/Kiev');

list(, $module, $controller, $action)=explode('/', (str_contains($_SERVER['REQUEST_URI'] ?? '', 'admin') ? '' : '/application').strtok($_SERVER["REQUEST_URI"] ?? '', '?'));

$module     = $module==='admin' ? 'admin' : 'application';
$controller = $controller ? StringHelper::camelize($controller) : 'index';
$action     = $action ? StringHelper::camelize($action) : 'index';

if ($module==='application')
{
    if ($controller==='online')
    {
        $controller='index';
        $modal_name='pay';
    }
    elseif ($controller==='nutritionWebinar')
    {
        $controller='index';
        $modal_name='nutrition_webinar';
    }
    elseif ($controller==='newbornWebinar')
    {
        $controller='index';
        $modal_name='newborn_webinar';
    }
}

if ($module==='admin' && ($controller!=='index' || $action==='upload') && !$_SESSION['id'])
{
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'))
    {
        http_response_code(401);
        die('У вас немає прав на перегляд цієї сторінки');
    }
    else
    {
        header('location: /admin/?redirect_to='.urlencode($_SERVER['REQUEST_URI']));
        die;
    }
}

if (FROM_CRON)
{
    $module='admin';
    $controller='crons';
    $action=StringHelper::camelize($argv[1]);
}

$module_path=APPLICATION_TOP_PATH."/module/$module";

if (is_file($module_path.'/controller/'.ucfirst($controller).'Controller.php'))
{
    ob_start();
    require($module_path.'/controller/'.ucfirst($controller).'Controller.php');

    if (!function_exists($action.'Action'))
    {
        http_response_code(404);
        die('<h1>404 сторінку не знайдено</h1>');
    }

    eval("{$action}Action();");

    $body_html=ob_get_contents();
    ob_end_clean();

    /** @var $modules array|null */
    /** @var $layout array|null */

    if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')) || $layout['disable'] || FROM_CRON)
        die($body_html);

    $layout=array_merge([
        'body' => $body_html,
    ], isset($layout) ? $layout : []);

    ob_start();
    require("$module_path/view/layout.phtml");
    $html=ob_get_contents();
    ob_end_clean();

    echo(trim($html));
}
else
{
    http_response_code(404);
    die('<h1>404 сторінку не знайдено</h1>');
}