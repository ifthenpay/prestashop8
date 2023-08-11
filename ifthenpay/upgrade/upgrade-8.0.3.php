<?php
/**
 * File: /upgrade/upgrade-8.0.3.php
 * Author: Ifthenpay Lda <ifthenpay@ifthenpay.com>
 * Copyright: 2007-2023 Ifthenpay Lda
 * License: http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\Module\Ifthenpay\Log\IfthenpayLogProcess;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 *
 * @param [type] $module
 * @return void
 */
function upgrade_module_8_0_3($module)
{
    IfthenpayLogProcess::addLog('Running module upgrade from 8.0.3 module version', IfthenpayLogProcess::INFO, 0);
    $count = 0;

    $tablesToCheck = array(
        array('name' => _DB_PREFIX_ . 'ifthenpay_multibanco',   'oldColumnName' => 'request_id'),
        array('name' => _DB_PREFIX_ . 'ifthenpay_mbway',        'oldColumnName' => 'id_transacao'),
        array('name' => _DB_PREFIX_ . 'ifthenpay_ccard',        'oldColumnName' => 'requestId'),
        array('name' => _DB_PREFIX_ . 'ifthenpay_payshop',      'oldColumnName' => 'id_transacao')
    );

    foreach ($tablesToCheck as $tableInfo) {
        
        $columnCheckResult = checkColumnsExistence($tableInfo['name'], $tableInfo['oldColumnName']);
        
        if ($columnCheckResult == 1) {
            $alterColumnResponse = alterColumnName($tableInfo['name'], $tableInfo['oldColumnName']);
            $count += $alterColumnResponse ? 1 : 0;
        }
    }

    return $count > 0 ? true : false;
}

function checkColumnsExistence($tableName, $oldColumnName)
{
    $query = 'SELECT COUNT(*) AS column_exists
              FROM information_schema.columns
              WHERE table_name = \'' . pSQL($tableName) . '\'
              AND column_name IN (\'' . pSQL($oldColumnName) . '\')';

    $count = Db::getInstance()->getValue($query);

    return $count > 0 ? 1 : 0;
}

function alterColumnName($tableName, $oldColumnName)
{
    $newColumnName = 'transaction_id';

    $alterQuery = 'ALTER TABLE `' . pSQL($tableName) . '`
                   CHANGE `' . pSQL($oldColumnName) . '` `' . pSQL($newColumnName) . '` VARCHAR(20) NULL';

    $result = Db::getInstance()->execute($alterQuery);

    IfthenpayLogProcess::addLog('Ran upgrade script from 8.0.3 module version (alterColumnName()) for table ' . $tableName . ' with result code = ' . $result, IfthenpayLogProcess::INFO, 0);

    return $result == 1 ? true : false;
}
