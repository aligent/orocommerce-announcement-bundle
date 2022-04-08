<?php
/**
 * @category  Aligent
 * @package
 * @author    Greg Ziborov <greg.ziborov@aligent.com.au>
 * @copyright 2021 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */


namespace Aligent\AnnouncementBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 *Adds functionality to display custom content block as a scheduled announcement on the top of the homepage.
 *It can be customised from System\Configuration\Marketing tab.
 */
class AligentAnnouncementBundle extends Bundle
{
    const ALIAS = 'aligent_announcement';
}
