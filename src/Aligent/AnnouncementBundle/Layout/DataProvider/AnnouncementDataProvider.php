<?php
/**
 * @category  Aligent
 * @package
 * @author    Greg Ziborov <greg.ziborov@aligent.com.au>
 * @copyright 2021 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */
namespace Aligent\AnnouncementBundle\Layout\DataProvider;

use Aligent\AnnouncementBundle\DependencyInjection\Configuration;
use Carbon\Carbon;
use DateTimeZone;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\LocaleBundle\Model\LocaleSettings;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;

/**
 * Get alert block display configuration values
 */
class AnnouncementDataProvider
{
    protected ConfigManager $configManager;
    protected LocaleSettings $localeSettings;
    protected TokenAccessorInterface $tokenAccessor;

    public function __construct(
        ConfigManager          $configManager,
        LocaleSettings         $localeSettings,
        TokenAccessorInterface $tokenAccessor
    ) {
        $this->configManager = $configManager;
        $this->localeSettings = $localeSettings;
        $this->tokenAccessor = $tokenAccessor;
    }

    /**
     * Get background colour for the alert block for the current website
     */
    public function getBackgroundColor(): ?string
    {
        return $this->getConfiguration(Configuration::ALERT_BLOCK_BACKGROUND_COLOUR);
    }

    /**
     * Return black or white depending on background contrast
     */
    public function getContrastColor(): string
    {
        $backgroundColor = $this->getBackgroundColor();

        // hexColor to RGB
        $r = hexdec(substr($backgroundColor, 1, 2));
        $g = hexdec(substr($backgroundColor, 3, 2));
        $b = hexdec(substr($backgroundColor, 5, 2));

        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        // return black or white
        return ($yiq >= 128) ? '#000' : '#FFF';
    }

    /**
     * Get display status for the alert block for the current website
     */
    public function getDisplayStatus(): bool
    {
        // Disable if no Content Block was configured
        if (!$this->getContentBlock()) {
            return false;
        }

        $allowedGroups = $this->getAllowedCustomerGroupIdsFromConfig();

        // if there is no Customer Groups configured, we only check the Start and End dates
        if (!count($allowedGroups)) {
            return $this->checkAlertBlockDatesRange();
        }

        /** @var CustomerUser $customerUser */
        $customerUser = $this->tokenAccessor->getUser();

        // if there is no user logged in, the announcement is not displayed
        if (!$customerUser instanceof CustomerUser) {
            return false;
        }

        // get the Customer Group associated with the current logged in Customer
        $customerGroupId = $customerUser->getCustomer()?->getGroup()?->getId();
        $isCustomerAllowed = in_array($customerGroupId, $allowedGroups);

        // if the current logged in Customer doesn't belong to any of the configured Customer Groups,
        // we don't display the announcement to them
        if (!$isCustomerAllowed) {
            return false;
        }

        // at this point we know that the logged in Customer is associated with a Customer Group,
        // but we still need to check the Start and End Dates
        return $this->checkAlertBlockDatesRange();
    }

    /**
     * Returns the date config value for the provided date config key
     * @param string $dateKey - Date config key
     * @return Carbon|null
     */
    protected function getAlertBlockDateConfig(string $dateKey): ?Carbon
    {
        // get config parameters
        $dateField = $this->getConfiguration($dateKey);

        if (!$dateField instanceof \DateTime) {
            return null;
        }

        return $this->toSystemTimeZone(new Carbon($dateField));
    }

    protected function toSystemTimeZone(Carbon $carbon): Carbon
    {
        $timeZone = new DateTimeZone($this->localeSettings->getTimeZone());
        return $carbon->tz($timeZone);
    }

    /**
     * Checks if the announcement should be displayed based on the configured Start and End Dates
     * @return bool
     */
    protected function checkAlertBlockDatesRange(): bool
    {
        $startDate = $this->getAlertBlockDateConfig(Configuration::ALERT_BLOCK_DATE_START);
        if ($startDate) {
            $startDate = $startDate->startOfDay();
        }
        $endDate = $this->getAlertBlockDateConfig(Configuration::ALERT_BLOCK_DATE_END);
        if ($endDate) {
            $endDate = $endDate->endOfDay();
        }

        $today = $this->toSystemTimeZone(Carbon::now());

        if ($startDate && $startDate->greaterThanOrEqualTo($today)) {
            // Starts in the future
            return false;
        }

        if ($endDate && $endDate->lessThanOrEqualTo($today)) {
            // Ended in the past
            return false;
        }

        return true;
    }

    /**
     * Gets all customer group IDs configured in System->Configuration->Marketing->Announcement message
     * @return array<int,int>
     */
    protected function getAllowedCustomerGroupIdsFromConfig(): array
    {
        return (array)$this->getConfiguration(Configuration::ALERT_BLOCK_ALLOWED_CUSTOMER_GROUPS);
    }

    /**
     * Get alert block alias for the current website
     */
    public function getContentBlock(): ?string
    {
        return $this->getConfiguration(Configuration::ALERT_BLOCK_ALIAS);
    }

    public function getConfiguration(string $key): mixed
    {
        return $this->configManager->get(Configuration::getConfigKeyByName($key));
    }
}
