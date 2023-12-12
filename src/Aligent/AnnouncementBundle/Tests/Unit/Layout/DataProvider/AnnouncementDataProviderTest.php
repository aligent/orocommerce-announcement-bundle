<?php
/**
 * @category  Aligent
 * @package
 * @author    Chris Rossi <chris.rossi@aligent.com.au>
 * @copyright 2022 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */
namespace Aligent\AnnouncementBundle\Tests\Unit\Layout\DataProvider;

use Aligent\AnnouncementBundle\DependencyInjection\Configuration;
use Aligent\AnnouncementBundle\Layout\DataProvider\AnnouncementDataProvider;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\CustomerBundle\Entity\CustomerGroup;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\LocaleBundle\Model\LocaleSettings;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Oro\Component\Testing\Unit\EntityTrait;
use PHPUnit\Framework\MockObject\MockObject;

class AnnouncementDataProviderTest extends \PHPUnit\Framework\TestCase
{
    use EntityTrait;

    protected ConfigManager&MockObject $configManager;
    protected LocaleSettings&MockObject $localeSettings;
    protected TokenAccessorInterface&MockObject $tokenAccessor;

    public function setUp(): void
    {
        $this->configManager = $this->getMockBuilder(ConfigManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->localeSettings = $this->createMock(LocaleSettings::class);
        $this->tokenAccessor = $this->createMock(TokenAccessorInterface::class);
    }

    public function testGetConfiguration(): void
    {
        $this->configManager->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['aligent_announcement.alert_block_alias', false, false, null, 'test-block-alias'],
                ['aligent_announcement.alert_block_background_colour', false, false, null, '#FFCC00'],
            ]);
        ;

        $provider = new AnnouncementDataProvider(
            $this->configManager,
            $this->localeSettings,
            $this->tokenAccessor,
        );

        $this->assertEquals(
            'test-block-alias',
            $provider->getConfiguration(Configuration::ALERT_BLOCK_ALIAS)
        );

        $this->assertEquals('#FFCC00', $provider->getBackgroundColor());
    }

    /**
     * @dataProvider getContrastColorData
     */
    public function testGetContrastColor(string $backgroundColor, string $expectedContrastColor): void
    {
        $provider = $this->getMockBuilder(AnnouncementDataProvider::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBackgroundColor'])
            ->getMock();

        $provider->expects($this->once())
            ->method('getBackgroundColor')
            ->willReturn($backgroundColor);

        $contrastColor = $provider->getContrastColor();

        $this->assertEquals($expectedContrastColor, $contrastColor);
    }

    public function getContrastColorData(): \Generator
    {
        yield 'Black background returns White text' => [
            'backgroundColor' => '#000000',
            'expectedContrastColor' => '#FFF',
        ];

        yield 'White background returns Black text' => [
            'backgroundColor' => '#FFFFFF',
            'expectedContrastColor' => '#000',
        ];

        yield 'Dark Blue background returns White text' => [
            'backgroundColor' => '#20457C',
            'expectedContrastColor' => '#FFF',
        ];

        yield 'Light Yellow background returns Black text' => [
            'backgroundColor' => '#ddc039',
            'expectedContrastColor' => '#000',
        ];
    }

    /**
     * @dataProvider getDisplayStatusData
     * @param array<int,array<int,mixed>> $configuration
     * @param CustomerUser|null $customerUser
     * @param bool $expectedDisplayStatus
     */
    public function testDisplayStatus(
        array $configuration,
        ?CustomerUser $customerUser,
        bool $expectedDisplayStatus,
    ): void {
        if ($customerUser) {
            $this->tokenAccessor->expects($this->any())
                ->method('getUser')
                ->willReturn($customerUser);
        }

        $this->localeSettings->expects($this->any())
            ->method('getTimeZone')
            ->willReturn('Australia/Adelaide');

        $provider = $this->getMockBuilder(AnnouncementDataProvider::class)
            ->setConstructorArgs([$this->configManager, $this->localeSettings, $this->tokenAccessor])
            ->onlyMethods(['getConfiguration'])
            ->getMock();

        $provider->expects($this->any())
            ->method('getConfiguration')
            ->willReturnMap($configuration);

        $this->assertEquals($expectedDisplayStatus, $provider->getDisplayStatus());
    }

    public function getDisplayStatusData(): \Generator
    {
        $customerGroup = $this->getEntity(CustomerGroup::class, [
            'id' => 123,
            'name' => 'CUSTOMER_GROUP',
        ]);

        $otherCustomerGroup = $this->getEntity(CustomerGroup::class, [
            'id' => 456,
            'name' => 'OTHER_CUSTOMER_GROUP',
        ]);

        $customer = $this->getEntity(Customer::class, [
            'group' => $customerGroup,
        ]);

        $customerUser = $this->getEntity(CustomerUser::class, [
            'customer' => $customer,
        ]);

        yield 'All Groups, Guest Customer, no Configuration' => [
            'configuration' => [],
            'customerUser' => null,
            'expectedDisplayStatus' => false,
        ];

        yield 'All Groups, Guest Customer, only Content Block configured' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
            ],
            'customerUser' => null,
            'expectedDisplayStatus' => true,
        ];

        yield 'All Groups, Guest Customer, no start/end date' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', []],
                ['alert_block_date_start', null],
                ['alert_block_date_end', null],
            ],
            'customerUser' => null,
            'expectedDisplayStatus' => true,
        ];

        yield 'All Groups, Guest Customer, starts today, ends tomorrow' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', []],
                ['alert_block_date_start', new \DateTime('today')],
                ['alert_block_date_end', new \DateTime('tomorrow')],
            ],
            'customerUser' => null,
            'expectedDisplayStatus' => true,
        ];

        yield 'All Groups, Guest Customer, no start date, ends tomorrow' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', []],
                ['alert_block_date_start', null],
                ['alert_block_date_end', new \DateTime('tomorrow')],
            ],
            'customerUser' => null,
            'expectedDisplayStatus' => true,
        ];

        yield 'All Groups, Guest Customer, starts yesterday, no end date' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', []],
                ['alert_block_date_start', new \DateTime('yesterday')],
                ['alert_block_date_end', null],
            ],
            'customerUser' => null,
            'expectedDisplayStatus' => true,
        ];

        yield 'All Groups, Guest Customer, starts tomorrow, no end date' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', []],
                ['alert_block_date_start', new \DateTime('tomorrow')],
                ['alert_block_date_end', null],
            ],
            'customerUser' => null,
            'expectedDisplayStatus' => false,
        ];

        yield 'All Groups, Guest Customer, starts yesterday, ends today' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', []],
                ['alert_block_date_start', new \DateTime('yesterday')],
                ['alert_block_date_end', new \DateTime('today')],
            ],
            'customerUser' => null,
            'expectedDisplayStatus' => true,
        ];

        yield 'All Groups, Guest Customer, starts last week, ended yesterday' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', []],
                ['alert_block_date_start', new \DateTime('-7 days')],
                ['alert_block_date_end', new \DateTime('-1 days')],
            ],
            'customerUser' => null,
            'expectedDisplayStatus' => false,
        ];

        yield 'Specific Customer Group, Guest Customer, no start/end dates' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', [$customerGroup]],
                ['alert_block_date_start', null],
                ['alert_block_date_end', null],
            ],
            'customerUser' => null,
            'expectedDisplayStatus' => false,
        ];

        yield 'Specific Customer Group, Logged-in Customer in same Group, no start/end dates' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', [$customerGroup->getId()]],
                ['alert_block_date_start', null],
                ['alert_block_date_end', null],
            ],
            'customerUser' => $customerUser,
            'expectedDisplayStatus' => true,
        ];

        yield 'Specific Customer Group, Logged-in Customer in different Group, no start/end dates' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', [$otherCustomerGroup->getId()]],
                ['alert_block_date_start', null],
                ['alert_block_date_end', null],
            ],
            'customerUser' => $customerUser,
            'expectedDisplayStatus' => false,
        ];

        yield 'Specific Customer Group, Logged-in Customer in same Group, starts tomorrow, ends next week' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', [$customerGroup->getId()]],
                ['alert_block_date_start', new \DateTime('tomorrow')],
                ['alert_block_date_end', new \DateTime('next week')],
            ],
            'customerUser' => $customerUser,
            'expectedDisplayStatus' => false,
        ];

        yield 'Specific Customer Group, Logged-in Customer in same Group, started yesterday, ends next week' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', [$customerGroup->getId()]],
                ['alert_block_date_start', new \DateTime('yesterday')],
                ['alert_block_date_end', new \DateTime('next week')],
            ],
            'customerUser' => $customerUser,
            'expectedDisplayStatus' => true,
        ];

        yield 'Specific Customer Group, Logged-in Customer in different Group, started yesterday, ends next week' => [
            'configuration' => [
                ['alert_block_alias', 'test-content-block'],
                ['alert_block_allowed_customer_groups', [$otherCustomerGroup->getId()]],
                ['alert_block_date_start', new \DateTime('yesterday')],
                ['alert_block_date_end', new \DateTime('next week')],
            ],
            'customerUser' => $customerUser,
            'expectedDisplayStatus' => false,
        ];
    }
}
