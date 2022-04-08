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
use Oro\Bundle\LocaleBundle\Model\LocaleSettings;
use Oro\Bundle\WebsiteBundle\Manager\WebsiteManager;
use Carbon\Carbon;
use DateTimeZone;

/**
 * Get alert block display configuration values
 */
class AnnouncementDataProvider
{
    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @var WebsiteManager
     */
    protected $websiteManager;

    /**
     * @var LocaleSettings
     */
    protected $localeSettings;

    /**
     * AnnouncementDataProvider constructor.
     * @param ConfigManager $configManager
     * @param WebsiteManager $websiteManager
     * @param LocaleSettings $localeSettings
     */
    public function __construct(
        ConfigManager $configManager,
        WebsiteManager $websiteManager,
        LocaleSettings $localeSettings
    ) {
        $this->configManager = $configManager;
        $this->websiteManager = $websiteManager;
        $this->localeSettings =$localeSettings;
    }

    /**
     * Get background colour for the alert block for the current website
     * @return string|null
     */
    public function getBackgroundColor()
    {
        $website = $this->websiteManager->getCurrentWebsite();
        return $this->configManager->get(
            Configuration::getConfigKeyByName(Configuration::ALERT_BLOCK_BACKGROUND_COLOUR),
            false,
            false,
            $website
        ) ;
    }

    /**
     * Return black or white depending on background contrast
     * @return string
     */
    public function getContrastColor()
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
    public function getDisplayStatus()
    {
        // get config parameters
        $website = $this->websiteManager->getCurrentWebsite();
        $startDateField = $this->configManager->get(
            Configuration::getConfigKeyByName(Configuration::ALERT_BLOCK_DATE_START),
            false,
            false,
            $website
        );
        $endDateField = $this->configManager->get(
            Configuration::getConfigKeyByName(Configuration::ALERT_BLOCK_DATE_END),
            false,
            false,
            $website
        );

        // convert DateTime objects to string format
        if ($startDateField) {
            $startDateFieldString = $startDateField->format('d-m-Y');
        } else {
            $startDateFieldString = '';
        }

        if ($endDateField) {
            $endDateFieldString = $endDateField->format('d-m-Y');
        } else {
            $endDateFieldString = '';
        }

        // convert date to one time zone
        $timeZone = new DateTimeZone($this->localeSettings->getTimeZone());
        $today = Carbon::now()->tz($timeZone);
        $startDate = new Carbon($startDateFieldString, $timeZone);
        $endDate = (new Carbon($endDateFieldString, $timeZone))->endOfDay();

        // compare dates and return display status
        if (!$startDateField && !$endDateField) {
            return true;
        } elseif (!$endDateField && ($startDate->lessThanOrEqualTo($today))) {
            return true;
        } elseif (!$startDateField && ($endDate->greaterThanOrEqualTo($today))) {
            return true;
        } elseif (($endDate->greaterThanOrEqualTo($today)) && ($startDate->lessThanOrEqualTo($today))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get alert block alias for the current website
     * @return string
     */
    public function getContentBlock()
    {
        $website = $this->websiteManager->getCurrentWebsite();
        $contentBlock = $this->configManager->get(
            Configuration::getConfigKeyByName(Configuration::ALERT_BLOCK_ALIAS),
            false,
            false,
            $website
        ) ;

        return $contentBlock ? : 'none';
    }
}
