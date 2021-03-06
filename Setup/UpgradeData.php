<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace Magenerds\PageDesigner\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 *
 * @package     Magenerds\PageDesigner\Setup
 * @file        UpgradeData.php
 * @copyright   Copyright (c) 2019 TechDivision GmbH (https://www.techdivision.com)
 * @site        https://www.techdivision.com/
 * @author      Simon Sippert <s.sippert@techdivision.com>
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Upgrade data for module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        // start setup
        $setup->startSetup();

        // version upgrade to 2.0.0
        if (version_compare($context->getVersion(), '2.0.0') < 0) {
            // replace old wysiwyg widget
            $replacedWidget = 'Magenerds\\\\PageDesigner\\\\Block\\\\Widget\\\\Editor';
            $newWidget = 'Magenerds\\\\WysiwygWidget\\\\Block\\\\Widget\\\\Editor';

            // define tables to update
            $tables = [
                'cms_page' => 'content',
                'cms_block' => 'content',
                'widget_instance' => 'instance_type',
            ];

            // iterate over tables
            foreach ($tables as $table => $field) {
                /** @noinspection SqlNoDataSourceInspection */
                $setup->getConnection()->query(sprintf(
                    "UPDATE IGNORE %s SET %s = REPLACE(%s, '%s', '%s') WHERE %s LIKE '%%%s%%' ESCAPE '|'",
                    $table, $field, $field, $replacedWidget, $newWidget, $field, $replacedWidget
                ));
            }
        }

        // end setup
        $setup->endSetup();
    }
}
