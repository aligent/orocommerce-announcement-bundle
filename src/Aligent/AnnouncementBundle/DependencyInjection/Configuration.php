<?php
/**
 * @category  Aligent
 * @package
 * @author    Greg Ziborov <greg.ziborov@aligent.com.au>
 * @copyright 2021 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */
namespace Aligent\AnnouncementBundle\DependencyInjection;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    const ROOT_NODE = AligentAnnouncementExtension::ALIAS;

    const ALERT_BLOCK_BACKGROUND_COLOUR = 'alert_block_background_colour';
    const ALERT_BLOCK_DATE_START = 'alert_block_date_start';
    const ALERT_BLOCK_DATE_END = 'alert_block_date_end';
    const ALERT_BLOCK_ALIAS = 'alert_block_alias';

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(self::ROOT_NODE);

        SettingsBuilder::append(
            $rootNode,
            [
                self::ALERT_BLOCK_BACKGROUND_COLOUR  => ['value' => null],
                self::ALERT_BLOCK_DATE_START  => ['value' => null],
                self::ALERT_BLOCK_DATE_END  => ['value' => null],
                self::ALERT_BLOCK_ALIAS => ['value' => null]
            ]
        );

        return $treeBuilder;
    }

    /**
     * @param string $key
     * @return string
     */
    public static function getConfigKeyByName($key)
    {
        return implode(ConfigManager::SECTION_MODEL_SEPARATOR, [AligentAnnouncementExtension::ALIAS, $key]);
    }
}
