<?php

/**
 * @package     Joomdle
 * @subpackage  plg_system_joomdlemfa
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\System\Joomdlemfa\Extension;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\MultiFactor\NotifyActionLog;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\User\UserHelper;
use Joomla\Event\SubscriberInterface;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;
use Joomla\Component\Users\Administrator\Helper\Mfa as MfaHelper;

/**
 * Performs Joomdle SSO after a user successfully completes Joomla's
 * Multi-factor Authentication step.
 */
final class Joomdlemfa extends CMSPlugin implements SubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'onComUsersCaptiveValidateSuccess' => 'onComUsersCaptiveValidateSuccess',
        ];
    }

    public function onComUsersCaptiveValidateSuccess(NotifyActionLog $event): void
    {
        $app = Factory::getApplication();

        if ($app->isClient('administrator')) {
            return;
        }

        $user = $app->getIdentity();

        if (!$user || $user->guest || empty($user->username)) {
            return;
        }

        $username = $user->username;

        // Do nothing if user does not exist in Moodle.
        if (!ContentHelper::getUserId($username)) {
            return;
        }

        // Do nothing if the user is blocked.
        $userId  = UserHelper::getUserId($username);
        $userObj = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($userId);

        if ($userObj->block) {
            return;
        }

        // Nothing to do if the user has no MFA records (this should not happen).
        if (empty(MfaHelper::getUserMfaRecords($userId))) {
            return;
        }

        $compParams      = ComponentHelper::getParams('com_joomdle');
        $moodleUrl       = $compParams->get('MOODLE_URL');
        $redirectlessSso = $compParams->get('redirectless_sso');

        $session = $app->getSession();
        $token   = md5($session->getId());

        if ($redirectlessSso) {
            ContentHelper::logIntoMoodle($username, $token);

            return;
        }

        // Build the wantsurl: prefer the captive return URL stored in session.
        $returnUrl = (string) $session->get('com_users.return_url', '');

        if ($returnUrl === '' || !Uri::isInternal($returnUrl)) {
            $returnUrl = Uri::base();
        }

        $loginUrl = urlencode(Route::_($returnUrl));
        $username = urlencode($username);

        $app->redirect(
            $moodleUrl . "/auth/joomdle/land.php?username=$username&token=$token&use_wrapper=0&create_user=0&wantsurl=$loginUrl"
        );
    }
}
