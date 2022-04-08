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
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\LocaleBundle\Model\LocaleSettings;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Oro\Bundle\WebsiteBundle\Manager\WebsiteManager;
use Carbon\Carbon;
use DateTimeZone;

/**
 * Get alert block display configuration values
 */
class AnnouncementDataProvider
{
    protected ConfigManager $configManager;
    protected WebsiteManager $websiteManager;
    protected LocaleSettings $localeSettings;
    protected TokenAccessorInterface $tokenAccessor;

    /**
     * AnnouncementDataProvider constructor.
     * @param ConfigManager $configManager
     * @param WebsiteManager $websiteManager
     * @param LocaleSettings $localeSettings
     * @param TokenAccessorInterface $tokenAccessor
     */
    public function __construct(
        ConfigManager          $configManager,
        WebsiteManager         $websiteManager,
        LocaleSettings         $localeSettings,
        TokenAccessorInterface $tokenAccessor
    ) {
        $this->configManager = $configManager;
        $this->websiteManager = $websiteManager;
        $this->localeSettings = $localeSettings;
        $this->tokenAccessor = $tokenAccessor;
    }

    /**
     * Get background colour for the alert block for the current website
     * @return string|null
     */
    public function getBackgroundColor(): ?string
    {
        $website = $this->websiteManager->getCurrentWebsite();
        return $this->configManager->get(
            Configuration::getConfigKeyByName(Configuration::ALERT_BLOCK_BACKGROUND_COLOUR),
            false,
            false,
            $website
        );
    }

    /**
     * Return black or white depending on background contrast
     * @return string
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
     * @return boolean
     */
    public function getDisplayStatus(): bool
    {
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
     * @return string
     */
    private function getAlertBlockDateConfig(string $dateKey): string
    {
        // get config parameters
        $website = $this->websiteManager->getCurrentWebsite();
        $dateField = $this->configManager->get(
            Configuration::getConfigKeyByName($dateKey),
            false,
            false,
            $website
        );
        return $dateField ? $dateField->format('d-m-Y') : '';
    }

    /**
     * Checks if the announcement should be displayed based on the configured Start and End Dates
     * @return bool
     */
    private function checkAlertBlockDatesRange(): bool
    {
        $startDateFieldString = $this->getAlertBlockDateConfig(Configuration::ALERT_BLOCK_DATE_START);
        $endDateFieldString = $this->getAlertBlockDateConfig(Configuration::ALERT_BLOCK_DATE_END);

        // convert date to one time zone
        $timeZone = new DateTimeZone($this->localeSettings->getTimeZone());
        $today = Carbon::now()->tz($timeZone);
        $startDate = new Carbon($startDateFieldString, $timeZone);
        $endDate = (new Carbon($endDateFieldString, $timeZone))->endOfDay();

        // compare dates and return display status
        if (!$startDateFieldString && !$endDateFieldString) {
            return true;
        } elseif (!$endDateFieldString && ($startDate->lessThanOrEqualTo($today))) {
            return true;
        } elseif (!$startDateFieldString && ($endDate->greaterThanOrEqualTo($today))) {
            return true;
        } elseif (($endDate->greaterThanOrEqualTo($today)) && ($startDate->lessThanOrEqualTo($today))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets all customer group IDs configured in System->Configuration->Marketing->Announcement message
     * @return array<int,int>
     */
    private function getAllowedCustomerGroupIdsFromConfig(): array
    {
        return (array)$this->configManager->get(
            Configuration::getConfigKeyByName(Configuration::ALERT_BLOCK_ALLOWED_CUSTOMER_GROUPS)
        );
    }

    /**
     * Get alert block alias for the current website
     * @return string
     */
    public function getContentBlock(): string
    {
        $website = $this->websiteManager->getCurrentWebsite();
        $contentBlock = $this->configManager->get(
            Configuration::getConfigKeyByName(Configuration::ALERT_BLOCK_ALIAS),
            false,
            false,
            $website
        );

        return $contentBlock ?: 'none';
    }
}
