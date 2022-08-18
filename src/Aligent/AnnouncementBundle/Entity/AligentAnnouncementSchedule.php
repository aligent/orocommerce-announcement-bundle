<?php
/**
 * @category  Aligent
 * @package
 * @author    Chris Rossi <chris.rossi@aligent.com.au>
 * @copyright 2022 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */

namespace Aligent\AnnouncementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\CronBundle\Entity\ScheduleIntervalInterface;
use Oro\Bundle\CronBundle\Entity\ScheduleIntervalsAwareInterface;
use Oro\Bundle\CronBundle\Entity\ScheduleIntervalTrait;

/**
 * @ORM\Table(name="aligent_announcement_schedule")
 * @ORM\Entity()
 */
class AligentAnnouncementSchedule implements ScheduleIntervalInterface
{
    use ScheduleIntervalTrait;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="\Aligent\AnnouncementBundle\Entity\AligentAnnouncement", inversedBy="schedules")
     * @ORM\JoinColumn(name="announcement_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected ?AligentAnnouncement $announcement = null;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="active_at", type="datetime", nullable=true)
     */
    protected $activeAt = null;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="deactivate_at", type="datetime", nullable=true)
     */
    protected $deactivateAt = null;

    public function __construct(\DateTime $activeAt = null, \DateTime $deactivateAt = null)
    {
        $this->activeAt = $activeAt;
        $this->deactivateAt = $deactivateAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnnouncement(): ?AligentAnnouncement
    {
        return $this->announcement;
    }

    public function setAnnouncement(AligentAnnouncement $announcement): static
    {
        $this->announcement = $announcement;
        return $this;
    }

    public function getScheduleIntervalsHolder(): ScheduleIntervalsAwareInterface|AligentAnnouncement|null
    {
        return $this->getAnnouncement();
    }
}
