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

use Aligent\AnnouncementBundle\DependencyInjection\AligentAnnouncementExtension;
use Oro\Bundle\TestFrameworkBundle\Test\DependencyInjection\ExtensionTestCase;

class AligentAnnouncementExtensionTest extends ExtensionTestCase
{
    public function testLoad(): void
    {
        $this->loadExtension(new AligentAnnouncementExtension());

        // Services
        $expectedDefinitions = [
            \Aligent\AnnouncementBundle\Form\Type\ContentBlockSelectType::class,
            \Aligent\AnnouncementBundle\Form\Type\CustomerGroupMultiSelectType::class,
            \Aligent\AnnouncementBundle\Layout\DataProvider\AnnouncementDataProvider::class
        ];
        $this->assertDefinitionsLoaded($expectedDefinitions);

        $expectedExtensionConfigs = ['aligent_announcement'];
        $this->assertExtensionConfigsLoaded($expectedExtensionConfigs);
    }
}
