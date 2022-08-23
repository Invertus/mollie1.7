<?php
/**
 * Mollie       https://www.mollie.nl
 *
 * @author      Mollie B.V. <info@mollie.nl>
 * @copyright   Mollie B.V.
 * @license     https://github.com/mollie/PrestaShop/blob/master/LICENSE.md
 *
 * @see        https://github.com/mollie/PrestaShop
 */

use Mollie\Adapter\ConfigurationAdapter;
use Mollie\Config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @return bool
 */
function upgrade_module_5_4_0(Mollie $module)
{
    /** @var ConfigurationAdapter $configurationAdapter */
    $configurationAdapter = $module->getMollieContainer(ConfigurationAdapter::class);
    $configurationAdapter->updateValue(Config::MOLLIE_USE_TAXES_FOR_FEES, 0);

    return true;
}
