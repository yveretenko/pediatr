<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

define('APPLICATION_TOP_PATH', realpath(dirname(__FILE__).'/..'));

$config=array_merge(require_once(APPLICATION_TOP_PATH.'/config/global.php'), require_once(APPLICATION_TOP_PATH.'/config/local.php'));

$db_params=array(
    'driver'   => 'pdo_mysql',
    'host'     => $config['db']['host'],
    'user'     => $config['db']['user'],
    'password' => $config['db']['password'],
    'dbname'   => $config['db']['database'],
);

$em_config=Setup::createAnnotationMetadataConfiguration(array(APPLICATION_TOP_PATH."/model"), true, sys_get_temp_dir().'/pediatr_doctrine_proxy', null, false);

try
{
    $entityManager=EntityManager::create($db_params, $em_config);
}
catch (Exception $e)
{
    die($e->getMessage());
}

return ConsoleRunner::createHelperSet($entityManager);

// vendor/bin/doctrine orm:generate-proxies