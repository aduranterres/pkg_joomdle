<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\Helper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\User\UserHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\CMS\User\User;
use Joomla\Registry\Registry;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

/**
 * Usercheck helper.
 *
 * @since  1.0.0
 */
class UsercheckHelper
{
    public static function checkUser($user, $isnew, $data)
    {
        if ($data['username'] == '') {
            return;
        }

        // Check that username is valid for Moodle
        UsercheckHelper::checkUsername($data['username']);

        // Check that name is valid for Moodle
        UsercheckHelper::checkName($data['name']);

        if ((!$isnew) && ((strcasecmp($user['username'], $data['username']) == 0)  && ($user['email'] == $data['email']))) {
            return;
        }

        $username = $data['username'];
        $moodle_user = ContentHelper::getUserId($username);

        /* If username does not exist in Moodle, check email */
        if (!$moodle_user) {
            $usernames = array (0 => array ('username' => 'this_is_a_kludge_to_avoid_empty_array'));
            $users = ContentHelper::getMoodleOnlyUsers($usernames, $data['email']);
            foreach ($users as $u) {
                if ($u['email'] == $data['email']) {
                    // Skip the user if it is the same: this is for the case when username is changed in Joomla
                    if ($u['username'] == $user['username']) {
                        continue;
                    }

                    // Email is the same as user in Moodle
                    throw new \RuntimeException(Text::_('COM_JOOMDLE_USERNAME_INUSE_IN_MOODLE'));
                }
            }
        } else {
            // Moodle user exists
            if ($isnew) {
                // New user in Joomla
                // Username already in use in Moodle
                throw new \RuntimeException(Text::_('COM_JOOMDLE_USERNAME_INUSE_IN_MOODLE'));
            } else {
                // Modify User in Joomla
                // Check email in Moodle
                $usernames = array (0 => array ('username' => 'this_is_a_kludge_to_avoid_empty_array'));
                $users = ContentHelper::getMoodleOnlyUsers($usernames, $data['email']);
                foreach ($users as $u) {
                    if ($u['username'] == $data['username']) {
                        continue;
                    }

                    if ($u['email'] == $data['email']) {
                        // Email is the same as user in Moodle
                        throw new \RuntimeException(Text::_('COM_JOOMDLE_USERNAME_INUSE_IN_MOODLE'));
                    }
                }
            }
        }
    }

    // Checks if username has valid format for Moodle
    public static function checkUsername($username)
    {
        // No spaces
        if (strstr($username, ' ')) {
            throw new \RuntimeException(Text::_('COM_JOOMDLE_USERNAME_CANNOT_CONTAIN_SPACES'));
        }

        // No uppercase
        $clean_username = strtolower($username);
        if (strcmp($clean_username, $username) != 0) {
            throw new \RuntimeException(Text::_('COM_JOOMDLE_USERNAME_MUST_BE_LOWERCASE'));
        }

        $plugin = PluginHelper::getPlugin('user', 'joomdle');
        $params = new Registry($plugin->params);
        $extendedusernamechars =  $params->get('extendedusernamechars');
        if (!$extendedusernamechars) {
            $clean_username = preg_replace('/[^-\.@_a-z0-9]/', '', $username);

            if ($clean_username != $username) {
                throw new \RuntimeException(Text::_('COM_JOOMDLE_INVALID_USERNAME'));
            }
        }
    }

    // Checks if name has valid format for Moodle
    public static function checkName($name)
    {
        // Require space in name
        $plugin = PluginHelper::getPlugin('user', 'joomdle');
        $params = new Registry($plugin->params);
        $space_in_name =  $params->get('space_in_name');

        $name = trim($name);
        if (($space_in_name) && (!strstr($name, ' '))) {
            throw new \RuntimeException(Text::_('COM_JOOMDLE_NAME_NEEDS_SPACE'));
        }
    }
}
