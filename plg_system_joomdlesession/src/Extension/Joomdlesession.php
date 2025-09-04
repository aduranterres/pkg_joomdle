<?php

/**
 * @package     Joomdle
 * @subpackage  plg_system_joomdlesession
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\System\Joomdlesession\Extension;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\CMS\User\UserHelper;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\User;
use Joomla\Database\ParameterType;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Event\Application\AfterRenderEvent;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

/**
 * Joomdlesession system plugin.
 */
final class Joomdlesession extends CMSPlugin // implements SubscriberInterface
{
    /**
     * Load plugin language files.
     */
    protected $autoloadLanguage = true;

    /**
     * @return  array
     *
     * @since   4.1.3
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterRender' => 'onAfterRender',
        ];
    }

    public function onAfterRender(AfterRenderEvent $event): void
    {
        // Do nothing if Joomdle is not present
        if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_joomdle/src/Helper/ContentHelper.php')) {
            return;
        }

        $app = Factory::getApplication();

        if (!$app->isClient('site')) {
            return;
        }

        $user = Factory::getApplication()->getIdentity();
        $user_id = $user->id;

        // Don't update guest sessions
        if (!$user_id) {
            return;
        }

        ContentHelper::updateSession($user->username);
    }
}
