<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\State;
use Magento\Framework\Shell\ComplexParameter;

require_once __DIR__ . '/../../../../../app/bootstrap.php';

$usage = 'Usage: php -f install.php -- --admin_user= [--bootstrap=]
    --admin_user - store\'s admin username. Required for installation.
    [--bootstrap] - add or override parameters of the bootstrap' . PHP_EOL;

$data = getopt('', ['admin_user:', 'bootstrap::']);
if (!isset($data['admin_user']) || empty($data['admin_user'])) {
    echo $usage;
    exit(1);
}

$bootstrapParam = new ComplexParameter('bootstrap');
$params = $bootstrapParam->mergeFromArgv($_SERVER, $_SERVER);
$params[Bootstrap::PARAM_REQUIRE_MAINTENANCE] = null;
$params[State::PARAM_MODE] = State::MODE_DEVELOPER;

$bootstrap = Bootstrap::create(BP, $params);
$app = $bootstrap->createApplication('Magento\Tools\SampleData\InstallerApp', ['data' => $data]);
$bootstrap->run($app);
