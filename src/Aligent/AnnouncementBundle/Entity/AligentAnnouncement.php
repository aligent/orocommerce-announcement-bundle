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

use Aligent\AnnouncementBundle\Model\ExtendAligentAnnouncement;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\CronBundle\Entity\ScheduleIntervalInterface;
use Oro\Bundle\CronBundle\Entity\ScheduleIntervalsAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\OrganizationAwareTrait;
use Oro\Bundle\ScopeBundle\Entity\Scope;
use Oro\Bundle\ScopeBundle\Entity\ScopeCollectionAwareInterface;

/**
 * @ORM\Entity(repositoryClass="Aligent\AnnouncementBundle\Entity\Repository\AligentAnnouncementRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(
 *      name="aligent_announcement",
 *      indexes={
 *          @ORM\Index(name="enabled_idx", columns={"enabled"}),
 *      }
 * )
 * @Config(
 *     defaultValues={
 *          "entity"={
 *              "icon"="fa-bullhorn"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"="commerce",
 *              "category"="shopping"
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          }
 *     }
 * )
 */
class AligentAnnouncement extends ExtendAligentAnnouncement implements
    DatesAwareInterface,
    OrganizationAwareInterface,
    ScopeCollectionAwareInterface,
    ScheduleIntervalsAwareInterface
{
    use DatesAwareTrait;
    use OrganizationAwareTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "identity"=true
     *          }
     *      }
     *  )
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected ?string $name = null;

    /**
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected ?bool $enabled = false;

    /**
     * @ORM\Column(name="priority", type="integer", nullable=false)
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected int $priority = 0;

    /**
     * @ORM\OneToMany(
     *      targetEntity="Aligent\AnnouncementBundle\Entity\AligentAnnouncementSchedule",
     *      mappedBy="announcement",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"activeAt" = "ASC"})
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var Collection<int,AligentAnnouncementSchedule>
     */
    protected Collection $schedules;

    /**
     * @ORM\ManyToMany(
     *      targetEntity="Oro\Bundle\ScopeBundle\Entity\Scope"
     * )
     * @ORM\JoinTable(name="aligent_announcement_scope",
     *      joinColumns={
     *          @ORM\JoinColumn(name="announcement_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="scope_id", referencedColumnName="id", onDelete="CASCADE")
     *      }
     * )
     * @var Collection<int,Scope>
     */
    protected Collection $scopes;

    /**
     * @var string|null
     *
     * @ORM\Column(type="wysiwyg", nullable=true)
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "attachment"={
     *              "acl_protected"=false,
     *          },
     *          "draft"={
     *              "draftable"=false
     *          }
     *      }
     * )
     */
    protected ?string $content = null;

    public function __construct()
    {
        parent::__construct();

        $this->schedules = new ArrayCollection();
        $this->scopes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): static
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return Collection<int, AligentAnnouncementSchedule>
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(AligentAnnouncementSchedule $schedule): static
    {
        if (!$this->schedules->contains($schedule)) {
            $schedule->setAnnouncement($this);
            $this->schedules->add($schedule);
        }
        return $this;
    }

    public function removeSchedule(AligentAnnouncementSchedule $schedule): static
    {
        if ($this->schedules->contains($schedule)) {
            $this->schedules->removeElement($schedule);
        }
        return $this;
    }

    /**
     * @return Collection<int, Scope>
     */
    public function getScopes(): Collection
    {
        return $this->scopes;
    }

    public function resetScopes(): static
    {
        $this->scopes->clear();
        return $this;
    }

    public function addScope(Scope $scope): static
    {
        if (!$this->scopes->contains($scope)) {
            $this->scopes->add($scope);
        }
        return $this;
    }

    public function removeScope(Scope $scope): static
    {
        if ($this->scopes->contains($scope)) {
            $this->scopes->removeElement($scope);
        }
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }
}
