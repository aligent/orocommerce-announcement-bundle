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
use Oro\Component\Testing\Unit\EntityTestCaseTrait;

class AligentAnnouncementScheduleTest extends \PHPUnit\Framework\TestCase
{
    use EntityTestCaseTrait;

    public function testProperties(): void
    {
        $now = new \DateTime('now');
        $properties = [
            ['id', 123, false],
            ['announcement', new AligentAnnouncement(), false],
            ['activeAt', $now, false],
            ['deactivateAt', $now, false]
        ];

        $this->assertPropertyAccessors(new AligentAnnouncementSchedule(), $properties);
    }
}
