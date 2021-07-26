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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param Mollie $module
 *
 * @return bool
 */
function upgrade_module_4_3_3($module)
{
    /** @var \Mollie\Tracker\Segment $segment */
    $segment = $module->getMollieContainer(\Mollie\Tracker\Segment::class);
    $segment->setMessage('Upgraded to 4.3.3');
    $segment->track();

    return true;
}
