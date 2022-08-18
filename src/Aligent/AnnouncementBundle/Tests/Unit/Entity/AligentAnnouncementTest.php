<?php
/**
 * @category  Aligent
 * @package
 * @author    Chris Rossi <chris.rossi@aligent.com.au>
 * @copyright 2022 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */
namespace Aligent\AnnouncementBundle\Tests\Unit\Entity;

use Aligent\AnnouncementBundle\Entity\AligentAnnouncement;
use Aligent\AnnouncementBundle\Entity\AligentAnnouncementSchedule;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\ScopeBundle\Entity\Scope;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use Oro\Component\Testing\Unit\EntityTrait;

class AligentAnnouncementTest extends \PHPUnit\Framework\TestCase
{
    use EntityTestCaseTrait;
    use EntityTrait;

    public function testProperties(): void
    {
        $now = new \DateTime('now');
        $properties = [
            ['id', 123],
            ['organization', new Organization()],
            ['name', 'Announcement 1', 'Announcement 2'],
            ['enabled', true, false],
            ['priority', 0, 1, 2],
            ['createdAt', $now, false],
            ['updatedAt', $now, false],
            ['content', 'sample announcement', '<strong>announcement</strong><style>strong { color:red;}</style>'],
        ];

        $this->assertPropertyAccessors(new AligentAnnouncement(), $properties);
    }

    public function testCollections(): void
    {
        $collections = [
            ['scopes', new Scope()],
            ['schedules', new AligentAnnouncementSchedule()],
        ];

        $this->assertPropertyCollections(new AligentAnnouncement(), $collections);
    }

    public function testResetScopes(): void
    {
        $announcement = new AligentAnnouncement();
        $this->assertEmpty($announcement->getScopes());
        $announcement->addScope(new Scope());
        $this->assertNotEmpty($announcement->getScopes());
        $announcement->resetScopes();
        $this->assertEmpty($announcement->getScopes());
    }
}
