<?php
/**
 * @category  Aligent
 * @package
 * @author    Chris Rossi <chris.rossi@aligent.com.au>
 * @copyright 2022 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */
namespace Aligent\AnnouncementBundle\Tests\Unit\DependencyInjection;

use Aligent\AnnouncementBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfigTreeBuilder(): void
    {
        $configuration = new Configuration();
        $builder = $configuration->getConfigTreeBuilder();
        $this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder', $builder);

        $root = $builder->buildTree();
        $this->assertInstanceOf('Symfony\Component\Config\Definition\ArrayNode', $root);
        $this->assertEquals('aligent_announcement', $root->getName());
    }

    public function testProcessConfiguration(): void
    {
        $configuration = new Configuration();
        $processor     = new Processor();

        $expected =  [
            'settings' => [
                'resolved' => true,
                'alert_block_background_colour' => [
                    'value' => null,
                    'scope' => 'app'
                ],
                'alert_block_date_start' => [
                    'value' => null,
                    'scope' => 'app'
                ],
                'alert_block_date_end' => [
                    'value' => null,
                    'scope' => 'app'
                ],
                'alert_block_alias' => [
                    'value' => null,
                    'scope' => 'app'
                ],
                'alert_block_allowed_customer_groups' => [
                    'value' => [],
                    'scope' => 'app'
                ],
            ]
        ];

        $this->assertEquals($expected, $processor->processConfiguration($configuration, []));
    }

    public function testGetConfigKeyByName(): void
    {
        $configKey = Configuration::getConfigKeyByName('alert_block_background_colour');
        $this->assertEquals('aligent_announcement.alert_block_background_colour', $configKey);
    }
}
