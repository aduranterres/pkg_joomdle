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
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Event\Event;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

/**
 * Mappings helper.
 *
 * @since  1.0.0
 */
class MappingsHelper
{
// FIXME usamos esto de la app ahora?
    public static function getUserInfo($username, $app = '')
    {
        $comp_params = ComponentHelper::getParams('com_joomdle');

        if (!$app) {
            $app = $comp_params->get('additional_data_source');
        }

        $user_id = UserHelper::getUserId($username);
        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($user_id);

        $user_info['email'] = $user->email;
        $user_info['lang'] = MappingsHelper::getMoodleLang($user->getParam('language'));
        $user_info['timezone'] = $user->getParam('timezone');
        $user_info['password'] = $user->password;
        $user_info['suspended'] = $user->block;

        $more_info = array ();
        switch ($app) {
            case 'no':
                $more_info = MappingsHelper::getUserInfoJoomla($username);
                break;
            default:
                PluginHelper::importPlugin('joomdleprofile');
                $app = Factory::getApplication();
                $dispatcher = Factory::getApplication()->getDispatcher();
                $event = new Event('onJoomdleGetUserInfo', ['username' => $username]);
                $dispatcher->dispatch('onJoomdleGetUserInfo', $event);
                $result = $event->getArgument('results') ?? null;

                foreach ($result as $info) {
                    if (count($info)) {
                        // Allow for more than one plugin to return user info
                        $more_info = array_merge($more_info, $info);
                    }
                }
                break;
        }

        $result = array_merge($user_info, $more_info);

        // Do not sync Moodle DB ID field to Moodle
        unset($result['id']);

        return $result;
    }

    public static function getUserInfoJoomla($username)
    {
        $user_id = UserHelper::getUserId($username);
        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($user_id);

        $user_info = array();
        $user_info['firstname'] = MappingsHelper::getFirstname($user->name);
        $user_info['lastname'] = MappingsHelper::getLastname($user->name);
        $user_info['name'] = $user->name;
        $user_info['pic_url'] =  'none';

        return $user_info;
    }

    private static function getMoodleLang($lang)
    {
        if (!$lang) {
            return '';
        }

        return substr($lang, 0, 2);
    }

    public static function getFirstname($name)
    {
        $parts = explode(' ', $name);
        return  $parts[0];
    }

    public static function getLastname($name)
    {
        $parts = explode(' ', $name);

        $lastname = '';
        $n = count($parts);
        for ($i = 1; $i < $n; $i++) {
            if ($i != 1) {
                $lastname .= ' ';
            }
            $lastname .= $parts[$i];
        }

        return $lastname;
    }

    public static function getLoginUrl($course_id)
    {
        $comp_params = ComponentHelper::getParams('com_joomdle');
        $app = $comp_params->get('additional_data_source');
        $itemid = $comp_params->get('joomdle_itemid');

        $url = '';
        // Note: return only seems to work with normal Joomla login page (not CB or Jomsocial)
        $return = base64_encode('index.php?option=com_joomdle&view=detail&course_id=' . $course_id . '&Itemid=' . $itemid);
        switch ($app) {
            case 'no':
                $url = "index.php?option=com_users&view=login&return=$return";
                break;
            default:
                PluginHelper::importPlugin('joomdleprofile');
                $app = Factory::getApplication();
                $dispatcher = Factory::getApplication()->getDispatcher();
                $event = new Event('onJoomdleGetLoginUrl', ['return' => $return]);
                $dispatcher->dispatch('onJoomdleGetLoginUrl', $event);
                $result = $event->getArgument('results') ?? null;
                foreach ($result as $url) {
                    if ($url != '') {
                        break;
                    }
                }
                break;
        }

        // Fail safe
        if ($url == '') {
            $url = "index.php?option=com_users&view=login&return=$return";
        }

        return $url;
    }

    public static function getFieldName($app, $field)
    {
        $params = ComponentHelper::getParams('com_joomdle');
        $additional_data_source = $params->get('additional_data_source');

        $name = '';
        switch ($app) {
            case $additional_data_source:
                PluginHelper::importPlugin('joomdleprofile');
                $app = Factory::getApplication();
                $dispatcher = Factory::getApplication()->getDispatcher();
                $event = new Event('onJoomdleGetFieldName', ['field' => $field]);
                $dispatcher->dispatch('onJoomdleGetFieldName', $event);
                $result = $event->getArgument('results') ?? null;
                foreach ($result as $name) {
                    if ($name != '') {
                        break;
                    }
                }
                break;
            default:
                // Don't return name from not enabled data source
                break;
        }

        return $name;
    }

    public static function getMoodleFieldName($field_id)
    {
        static $fields;

// XXX FIXME
//        if (!$fields)
//            $fields = JoomdleHelperContent::call_method ('user_custom_fields');

        if (!$fields) {
            return $field_id;
        }

        foreach ($fields as $field) {
            if ("cf_" . $field['id'] == $field_id) {
                return $field['shortname'] . ' - ' . $field['name'];
            }
        }

        return $field_id;
    }

    public static function getFields($app)
    {
        $fields = array ();
        switch ($app) {
            default:
                PluginHelper::importPlugin('joomdleprofile');
                $app = Factory::getApplication();
                $dispatcher = Factory::getApplication()->getDispatcher();
                $event = new Event('onJoomdleGetFields', ['field' => $app]);
                $dispatcher->dispatch('onJoomdleGetFields', $event);
                $result = $event->getArgument('results') ?? null;
                foreach ($result as $fields) {
                    if (count($fields)) {
                        break;
                    }
                }
                break;
        }

        return $fields;
    }

    public static function getMoodleFields()
    {
        return ContentHelper::userCustomFields();
    }

    public static function getAppMappings($app)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = 'SELECT *' .
            ' FROM #__joomdle_field_mappings' .
            " WHERE joomla_app = " . $db->Quote($app);
        $db->setQuery($query);
        $mappings = $db->loadObjectList();

        if (!$mappings) {
            return array ();
        }

        return $mappings;
    }

    public static function saveUserInfo($user_info)
    {
        $comp_params = ComponentHelper::getParams('com_joomdle');
        $app = $comp_params->get('additional_data_source');

        $username = $user_info['username'];
        $user_id = UserHelper::getUserId($username);
        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($user_id);

        // Save info to joomla user table.
        $user->name = $user_info['firstname'] . " " . $user_info['lastname'];

        // Remove values not present in Joomla
        if (array_key_exists('timezone', $user_info)) {
            if (($user_info['timezone'] == 99) || ($user_info['timezone'] == 'UTC')) {
                $user_info['timezone'] = '';
            }
            $user->setParam('timezone', $user_info['timezone']);
        }

        // FIXME this is not working now...
 //       $user->block = (int) $user_info['block'];

        switch ($app) {
            default:
                PluginHelper::importPlugin('joomdleprofile');
                $app = Factory::getApplication();
                $dispatcher = Factory::getApplication()->getDispatcher();
                $event = new Event('onJoomdleSaveUserInfo', ['user_info' => $user_info]);
                $dispatcher->dispatch('onJoomdleSaveUserInfo', $event);
                break;
        }

        $user->save();
    }

    public static function getMoodleCustomFieldValue($user_info, $field_id)
    {
        foreach ($user_info['custom_fields'] as $field) {
            if ('cf_' . $field['id'] == $field_id) {
                $data = $field['data'];
                break;
            }
        }

        return $data;
    }

    public static function syncUserToJoomla($username)
    {
        $user_info = ContentHelper::userDetails($username);

        if (!$user_info)
            return;

        MappingsHelper::createAdditionalProfile ($user_info);
        MappingsHelper::saveUserInfo($user_info);
    }

    public static function createAdditionalProfile ($user_info)
    {
        $comp_params = ComponentHelper::getParams( 'com_joomdle' );
        $app = $comp_params->get( 'additional_data_source' );

        $username = $user_info['username'];
        $user_id = UserHelper::getUserId($username);

        if (!$user_id)
            return;

        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($user_id);

        switch ($app)
        {
            default:
                PluginHelper::importPlugin('joomdleprofile');
                $app = Factory::getApplication();
                $dispatcher = Factory::getApplication()->getDispatcher();
                $event = new Event('onJoomdleCreateAdditionalProfile', ['user_info' => $user_info]);
                $dispatcher->dispatch('onJoomdleCreateAdditionalProfile', $event);
                break;
        }
    }


}
