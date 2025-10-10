<?php

/**
 * @package     Joomdle
 * @subpackage  plg_user_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\User\Joomdle\Extension;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\CMS\User\UserFactoryAwareTrait;
use Joomla\CMS\User\UserHelper;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\User;
use Joomla\CMS\Event\User\AfterDeleteEvent;
use Joomla\CMS\Event\User\AfterLoginEvent;
use Joomla\CMS\Event\User\AfterSaveEvent;
use Joomla\CMS\Event\User\BeforeSaveEvent;
use Joomla\CMS\Event\User\LoginEvent;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Event\User\AfterLogoutEvent;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\UsercheckHelper;
use Joomla\CMS\Filter\InputFilter;

/**
 * Joomdle user plugin.
 */
final class Joomdle extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;
    use UserFactoryAwareTrait;

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
            'onUserBeforeSave' => 'onUserBeforeSave',
            'onUserAfterSave' => 'onUserAfterSave',
            'onUserAfterDelete' => 'onUserAfterDelete',
            'onUserLogin' => 'onUserLogin',
            'onUserAfterLogin' => 'onUserAfterLogin',
            'onUserAfterLogout' => 'onUserAfterLogout',
        ];
    }

    public function onUserBeforeSave(BeforeSaveEvent $event): void
    {
        $user = $event->getUser();
        $new  = $event->getData();
        $isnew = $event->getIsNew();

        $application = Factory::getApplication();
        $input = $application->getInput();

        // Don't run on Joomdle user view and web service url, so we can sync users
        if (($input->get('option') != 'com_joomdle') || (($input->get('view') != 'users') && ($input->get('task') != 'server'))) {
            // Check that data is valid for user creation/modification in Moodle
            UsercheckHelper::checkUser($user, $isnew, $new);
        }

        // Change username in Moodle if it changed in Joomla
        if (($user['username'] != '') && ($user['username'] != $new['username'])) {
            ContentHelper::changeUsername($user['username'], $new['username']);
        }
    }

    /**
     * Tasks to execute after saving the user
     *
     * @param   AfterSaveEvent $event  The event instance.
     *
     * @return  void
     */
    public function onUserAfterSave(AfterSaveEvent $event): void
    {
        $user  = $event->getUser();
        $isnew = $event->getIsNew();

        // FIXME he puesto el msg='' u ssucess=''; Ver q es esto ... parece q no lo usamos ahora, mirar codigo antiguo
        $msg = '';
        $success = '';
        ContentHelper::syncUser($user, $isnew, $success, $msg);
    }

    public function onUserAfterDelete(AfterDeleteEvent $event): void
    {
        $comp_params = ComponentHelper::getParams('com_joomdle');

        // Don't delete user if not configured to do so.
        if (!$comp_params->get('auto_delete_users')) {
            return;
        }

        $user = $event->getUser();
        ContentHelper::deleteMoodleUser($user);
    }

    /**
     * Perform SSO on user login.
     *
     * @param   User\AfterLoginEvent $event  The event instance.
     *
     * @return  void
     *
     * @since   3.9.0
     */
    public function onUserAfterLogin(AfterLoginEvent $event): void
    {
        if ($this->params->get('login_event_to_hook', 'onUserLogin') != 'onUserAfterLogin') {
            return;
        }

        $options  = $event->getOptions();
        $user = $options['user'];
        $username = $user->username;
        $this->doLogin($username, $options);
    }

    public function onUserLogin(LoginEvent $event)
    {
        if ($this->params->get('login_event_to_hook', 'onUserLogin') != 'onUserLogin') {
            return;
        }

        $user     = $event->getAuthenticationResponse();
        $username = $user['username'];
        $options  = $event->getOptions();

        $this->doLogin($username, $options);
    }

    private function doLogin($username, $options = array())
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication('site');

        if (array_key_exists('skip_joomdleuserplugin', $options)) {
            return;
        }

        if ($app->isClient('administrator')) {
            return true;
        }

        $moodle_user = ContentHelper::getUserId($username);
        // Do nothing if user does not exist in Moodle
        if (!$moodle_user) {
            return;
        }

        $comp_params = ComponentHelper::getParams('com_joomdle');

        $moodle_url = $comp_params->get('MOODLE_URL');
        $redirectless_sso = $comp_params->get('redirectless_sso');

        $session = $app->getSession();
        $token = md5($session->getId());

        // Don't log in Moodle if user is blocked.
        $user_id = UserHelper::getUserId($username);
        $user_obj = $this->getUserFactory()->loadUserById($user_id);
        if ($user_obj->block) {
            return;
        }

        $return = $app->getInput()->get('return', '', 'string');

        if ($return) {
            if (!strncmp($return, 'B:', 2)) {
                // CB login module.
                $login_url = urlencode(base64_decode(substr($return, 2)));
            } else {
                // Normal login.
                $login_url = Route::_(base64_decode($return));
                $login_url = urlencode($login_url);
            }
        } elseif (array_key_exists('url', $options)) {
            $login_url = urlencode($options['url']);
        } else {
            $uri = Uri::getInstance();
            $login_url = urlencode($uri->toString(array('path', 'query')));
        }

        $username = urlencode($username);
        if ($redirectless_sso) {
            // Use redirect-less SSO
            ContentHelper::logIntoMoodle($username, $token);
        } else {
            // Use SSO with redirect
            $app->redirect($moodle_url . "/auth/joomdle/land.php?username=$username&token=$token&use_wrapper=0&create_user=0&wantsurl=$login_url");
        }
    }

    /**
     * Logout from Moodle when logging out from Joomla
     *
     * @param   AfterLogoutEvent  $event  Logout event
     *
     * @return  void
     *
     * @since   3.2
     */
    public function onUserAfterLogout(AfterLogoutEvent $event): void
    {
        /** @var CMSApplication $app */
        $app = $this->getApplication();

        $options  = $event->getOptions();

        if (array_key_exists('skip_joomdleuserplugin', $options)) {
            return;
        }

        if ($app->isClient('administrator')) {
            return;
        }

        $comp_params = ComponentHelper::getParams('com_joomdle');
        $redirectless_logout = $comp_params->get('redirectless_logout');

        if (!$redirectless_logout) {
            // Delete "remember me" cookie if present
            $cookieName  = 'joomla_remember_me_' . UserHelper::getShortHashedUserAgent();

            $cookieValue = $app->getInput()->cookie->get($cookieName);

            if ($cookieValue) {
                $cookieArray = explode('.', $cookieValue);

                // Filter series since we're going to use it in the query
                $filter = new InputFilter();
                $series = $filter->clean($cookieArray[1], 'ALNUM');

                // Remove the record from the database
                $db = $this->getDatabase();
                $query = $db->createQuery();
                $query
                    ->delete('#__user_keys')
                    ->where($db->quoteName('series') . ' = ' . $db->quote($series));

                $db->setQuery($query)->execute();

                // Destroy the cookie
                $app->getInput()->cookie->set(
                    $cookieName,
                    false,
                    time() - 42000,
                    $app->get('cookie_path', '/'),
                    $app->get('cookie_domain')
                );
            }

            $moodle_url = $comp_params->get('MOODLE_URL');
            $app->redirect($moodle_url . "/auth/joomdle/land_logout.php");
            return;
        }

        $cookie_path = $comp_params->get('cookie_path', "/");

        unset($_SESSION['USER']);
        unset($_SESSION['SESSION']);
        setcookie('MoodleSession', '', time() - 3600, $cookie_path, '', '', 0);
    }
}
