<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Site\Controller;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Application\Web\WebClient;
use Joomla\CMS\Uri\Uri;
use Joomdle\Component\Joomdle\Administrator\Helper\MappingsHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\JoomlagroupsHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\MailinglistHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ShopHelper;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\ParameterType;

class WsController extends BaseController
{
    private function getUserInfo($params)
    {
        $username = $params['username'];
        if (array_key_exists('app', $params)) {
            $app = $params['app'];
        } else {
            $app = '';
        }
        $user_info = MappingsHelper::getUserInfo($username, $app);
        return $user_info;
    }

    private function test()
    {
        return "It works";
    }

    /* Web service used to log in from Moodle */
    private function login($params)
    {
        $username = $params['username'];
        $password = $params['password'];

        /** @var CMSApplication $app */
        $app = Factory::getApplication('site');

        $user_id = UserHelper::getUserId($username);
        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($user_id);

        if (!$user) {
            return false;
        }

        if ($user->block) {
            return false;
        }

        $options = array ('skip_joomdleuserplugin' => '1', 'silent' => 1);
        $credentials = array ( 'username' => $username, 'password' => $password);
        if ($app->login($credentials, $options)) {
            return true;
        }
        return false;
    }

    // FIXME, camel case si nlo quitamos...
    private function joomdle_getDefaultItemid()
    {
        $comp_params = ComponentHelper::getParams('com_joomdle');
        $default_itemid = $comp_params->get('default_itemid');
        return $default_itemid;
    }

    private function confirmJoomlaSession($params)
    {
        $username = $params['username'];
        $token = $params['joomdle_auth_token'];

        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->createQuery()
            ->select($db->quoteName('session_id'))
            ->from($db->quoteName('#__session'))
            ->where($db->quoteName('username') . ' = :username')
            ->where('MD5(' . $db->quoteName('session_id') . ') = :token');

        $query->bind(':username', $username, ParameterType::STRING);
        $query->bind(':token', $token, ParameterType::STRING);

        $db->setQuery($query);
        $session = $db->loadResult();

        if ($session) {
            return true;
        } else {
            return false;
        }
    }

    private function logout($params)
    {
        $username = $params['username'];
        $ua_string = $params['ua_string'];

        /** @var CMSApplication $app */
        $app = Factory::getApplication('site');

        $id = UserHelper::getUserId($username);

        $error = $app->logout($id, array ( 'clientid' => 0, 'skip_joomdleuserplugin' => 1));

        // Return "remember me" cookie name so it  can be deleted
        $ua = new WebClient($ua_string);
        $uaString = $ua->userAgent;
        $browserVersion = $ua->browserVersion;
        $uaShort = str_replace($browserVersion, 'abcd', $uaString);

        $r = md5(Uri::base() . $uaShort);

        return $r;
    }

    private function deleteUserKey($params)
    {
        $series = $params['series'];

        // Delete the key
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query
            ->delete('#__user_keys')
            ->where($db->quoteName('series') . ' = ' . $db->quote($series));

        $db->setQuery($query)->execute();
    }

    private function createUser($params)
    {
        $userinfo = $params['userinfo'];
        return ContentHelper::createJoomlaUserWs($userinfo);
    }

    private function activateUser($params)
    {
        $username = $params['username'];

        return ContentHelper::activateJoomlaUser($username);
    }

    private function updateUser($params)
    {
        $userinfo = $params['userinfo'];
        return MappingsHelper::saveUserInfo($userinfo);
    }

    private function changePassword($params)
    {
        $username = $params['username'];
        $password = $params['password'];

        $user_id = UserHelper::getUserId($username);
        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($user_id);

        // Password comes hashed from Moodle, just store it
        // NOT anymore: hash algo is different in Joomla and Moodle
        $user->password = UserHelper::hashPassword($password);

        @$user->save();

        return true;
    }

    private function changeUsername($params)
    {
        $old_username = $params['old_username'];
        $new_username = $params['new_username'];

        $user_id = UserHelper::getUserId($old_username);
        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($user_id);

        $user->username = $new_username;
        @$user->save();

        return true;
    }

    private function deleteUser($params)
    {
        $username = $params['username'];

        $user_id = UserHelper::getUserId($username);

        if (!$user_id) {
            return;
        }

        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($user_id);
        $user->delete();
    }

    // FIXME rework using generic events, remove all specific events from Moodle side
    private function addMailingSub($params)
    {
        $username = $params['username'];
        $course_id = $params['course_id'];
        $type = $params['type'];

        return MailinglistHelper::addListMember($username, $course_id, $type);
    }

    private function removeMailingSub($params)
    {
        $username = $params['username'];
        $course_id = $params['course_id'];
        $type = $params['type'];

        return MailinglistHelper::removeListMember($username, $course_id, $type);
    }

    private function addUserGroups($params)
    {
        $course_id = $params['course_id'];
        $course_name = $params['course_name'];

        return JoomlagroupsHelper::addCourseGroups($course_id, $course_name);
    }

    private function updateUserGroups($params)
    {
        $course_id = $params['course_id'];
        $course_name = $params['course_name'];

        return JoomlagroupsHelper::updateCourseGroups($course_id, $course_name);
    }

    private function removeUserGroups($params)
    {
        $course_id = $params['course_id'];

        return JoomlagroupsHelper::removeCourseGroups($course_id);
    }

    private function addGroupMember($params)
    {
        $course_id = $params['course_id'];
        $username = $params['username'];
        $type = $params['type'];

        return JoomlagroupsHelper::addGroupMember($course_id, $username, $type);
    }

    private function removeGroupMember($params)
    {
        $course_id = $params['course_id'];
        $username = $params['username'];
        $type = $params['type'];

        return JoomlagroupsHelper::removeGroupMember($course_id, $username, $type);
    }

    // FIXME do when testing shop
    private function getSellUrl($params)
    {
        $course_id = $params['course_id'];

        return ShopHelper::getSellUrl($course_id);
    }

    private function checkJoomdleToken()
    {
        $token = $this->input->get('token');
        $comp_params = ComponentHelper::getParams('com_joomdle');

        $joomla_token = $comp_params->get('joomla_auth_token');

        return  ($token == $joomla_token);
    }

    public function server()
    {
        if (!$this->checkJoomdleToken()) {
            $token = $this->input->get('token');
            print_r(json_encode("Invalid token:" . $token));
            return;
        }

        $methodvariables = array_merge($_GET, $_POST);

        $wsfunction = $this->input->get('wsfunction');

        // Name change because of conflict
        // FIXME change this in Moodle if still needed, remove otherwise
        if ($wsfunction == 'getDefaultItemid') {
            $wsfunction = 'joomdle_getDefaultItemid';
        }

        echo json_encode($this->$wsfunction($methodvariables));
        exit();
    }
}
