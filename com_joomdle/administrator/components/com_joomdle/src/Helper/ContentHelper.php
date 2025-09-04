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

/**
 * Content helper.
 *
 * @since  1.0.0
 */
class ContentHelper
{
    public static function getCourseList($enrollable_only = 0, $orderby = 'fullname ASC', $guest = 0, $username = '')
    {
        $params = [
            'enrollable_only' => $enrollable_only,
            'sortby' => $orderby,
            'guest' => (int) $guest,
            'username' => $username
        ];

        return ContentHelper::callMethod('list_courses', $params);
    }

    public static function getCourseCategories($id = 0)
    {
        $params = [
            'category' => (int) $id,
        ];

        return ContentHelper::callMethod('get_course_categories', $params);
    }

    public static function getCourseCategory($id, $username = '')
    {
        $params = [
            'category' => (int) $id,
            'enrollable_only' => 0,
            'username' => $username,
        ];

        return ContentHelper::callMethod('courses_by_category', $params);
    }

    public static function getCategoryName($id)
    {
        $params = [
            'cat_id' => (int) $id,
        ];

        return ContentHelper::callMethod('get_cat_name', $params);
    }

    public static function getCourseInfo($id, $username = '')
    {
        $params = [
            'id' => (int) $id,
            'username' => $username
        ];

        return ContentHelper::callMethod('get_course_info', $params);
    }

    public static function getCourseContents($id)
    {
        $params = [
            'id' => (int) $id,
        ];

        return ContentHelper::callMethod('get_course_contents', $params);
    }

    public static function getCourseEvents($id)
    {
        $params = [
            'id' => (int) $id,
        ];

        return ContentHelper::callMethod('get_upcoming_events', $params);
    }

    public static function getCourseGradeCategories($id)
    {
        $params = [
            'id' => (int) $id,
        ];

        return ContentHelper::callMethod('get_course_grade_categories', $params);
    }

    public static function getCourseGrades($id, $username)
    {
        $params = [
            'id' => (int) $id,
            'username' => $username
        ];

        return ContentHelper::callMethod('get_grade_user_report', $params);
    }

    public static function changeUsername($old_username, $new_username)
    {
        $params = [
            'old_username' => $old_username,
            'new_username' => $new_username
        ];

        return ContentHelper::callMethod('change_username', $params);
    }

    public static function getUserId($username)
    {
        $params = [
            'username' => $username,
        ];

        return ContentHelper::callMethod('user_id', $params);
    }

    public static function createJoomdleUser($username)
    {
        $params = [
            'username' => $username,
        ];

        return ContentHelper::callMethod('create_joomdle_user', $params);
    }

    public static function deleteUser($username)
    {
        $params = [
            'username' => $username,
        ];

        return ContentHelper::callMethod('delete_user', $params);
    }

    public static function getMyGrades($username)
    {
        $params = [
            'username' => $username,
        ];

        return ContentHelper::callMethod('get_my_grades', $params);
    }

    public static function getMyGradeUserReport($username)
    {
        $params = [
            'username' => $username,
        ];

        return ContentHelper::callMethod('get_my_grade_user_report', $params);
    }

    public static function courseEnrolMethods($id)
    {
        $params = [
            'id' => $id,
        ];

        return ContentHelper::callMethod('course_enrol_methods', $params);
    }

    public static function enrolUser($username, $course_id, $role_id = 0)
    {
        $params = [
            'username' => $username,
            'id' => $course_id,
            'roleid' => $role_id,
        ];

        return ContentHelper::callMethod('enrol_user', $params);
    }

    public static function unenrolUser($username, $course_id)
    {
        $params = [
            'username' => $username,
            'id' => $course_id,
        ];

        return ContentHelper::callMethod('unenrol_user', $params);
    }

    public static function getMyCertificates($username, $type)
    {
        $params = [
            'username' => $username,
            'type' => $type,
        ];

        return ContentHelper::callMethod('my_certificates', $params);
    }

    public static function getMyCourses($username, $order_by_cat)
    {
        $params = [
            'username' => $username,
            'order_by_cat' => $order_by_cat,
        ];

        return ContentHelper::callMethod('my_courses', $params);
    }

    public static function userCustomFields()
    {
        $params = [
        ];

        return ContentHelper::callMethod('user_custom_fields', $params);
    }

    public static function getMoodleUsers($limitstart, $limit, $order, $order_dir, $search)
    {
        $params = [
            'limitstart' => $limitstart,
            'limit' => $limit,
            'order' => $order,
            'order_dir' => $order_dir,
            'search' => $search,
        ];

        return ContentHelper::callMethod('get_moodle_users', $params);
    }

    public static function getMoodleUsersNumber($search)
    {
        $params = [
            'search' => $search,
        ];

        return ContentHelper::callMethod('get_moodle_users_number', $params);
    }

    public static function getMoodleOnlyUsers($usernames, $search)
    {
        $params = [
            'users' => $usernames,
            'search' => $search,
        ];

        return ContentHelper::callMethod('get_moodle_only_users', $params);
    }

    public static function userExists($username)
    {
        $params = [
            'username' => $username,
        ];

        return ContentHelper::callMethod('user_exists', $params);
    }

    public static function userDetails($username)
    {
        $params = [
            'username' => $username,
        ];

        return ContentHelper::callMethod('user_details', $params);
    }

    public static function userDetailsById($id)
    {
        $params = [
            'id' => $id,
        ];

        return ContentHelper::callMethod('user_details_by_id', $params);
    }

    public static function migratetoJoomdle($username)
    {
        $params = [
            'username' => $username,
        ];

        return ContentHelper::callMethod('migrate_to_joomdle', $params);
    }

    public static function updateSession($username)
    {
        $params = [
            'username' => $username,
        ];

        return ContentHelper::callMethod('update_session', $params);
    }

    public static function getThemes()
    {
        return ContentHelper::callMethod('get_themes');
    }

    public static function checkJoomdleSystem()
    {
        $comp_params = ComponentHelper::getParams('com_joomdle');

        /* Mandatory Joomdle plugins enabled */
        $system[5]['description'] = Text::_('COM_JOOMDLE_JOOMDLE_USER_PLUGIN');
        $system[5]['value'] = PluginHelper::isEnabled('user', 'joomdle');
        if (PluginHelper::isEnabled('user', 'joomdle') != '1') {
            $system[5]['error'] =  Text::_('COM_JOOMDLE_JOOMDLE_USER_PLUGIN_ERROR');
        } else {
            $system[5]['error'] = '';
        }

        $connection = $comp_params->get('connection_method');

        if ($connection == 'fgc') {
            /* file_get_contents call.  Test to see if allow_url_fopen PHP option is enabled */
            $system[1]['description'] = Text::_('COM_JOOMDLE_ALLOW_URL_FOPEN');
            $system[1]['value'] = ini_get('allow_url_fopen');
            if ($system[1]['value'] != '1') {
                $system[1]['error'] =  Text::_('COM_JOOMDLE_ALLOW_URL_FOPEN_ERROR');
            } else {
                $system[1]['error'] = '';
            }
        } elseif ($connection == 'curl') {
            $system[1]['description'] = Text::_('COM_JOOMDLE_CURL_ENABLED');
            $system[1]['value'] = function_exists('curl_version') == "Enabled";
            if (!$system[1]['value']) {
                $system[1]['error'] =  Text::_('COM_JOOMDLE_CURL_ENABLED_ERROR');
            } else {
                $system[1]['error'] = '';
            }
        }

        if ($system[1]['error'] != '') {
            /* If no working connection, no need to continue */
            return $system;
        }

        // Check bare HTTP connection
        $moodle_url = $comp_params->get('MOODLE_URL');
        $moodle_file_url = $moodle_url . '/auth/joomdle/connection_test.php';
        $joomla_file_url = $moodle_url . '/auth/joomdle/connection_test_joomla.php';

        // Joomla to Moodle
        $result = ContentHelper::getFile($moodle_file_url);
        $system[6]['description'] = Text::_('COM_JOOMDLE_JOOMDLE_JOOMLA_TO_MOODLE_CONNECTION');
        if (strncmp($result, 'OK', 2) != 0) {
            $system[6]['value'] = 0;
            $system[6]['error'] =  Text::_('COM_JOOMDLE_JOOMLA_CANNOT_CONNECT_TO_MOODLE');
        } else {
            $system[6]['value'] = 1;
            $system[6]['error'] =  '';
        }

        // Moodle to Joomla
        $result = ContentHelper::getFile($joomla_file_url);
        $system[7]['description'] = Text::_('COM_JOOMDLE_JOOMDLE_MOODLE_TO_JOOMLA_CONNECTION');
        if (strncmp($result, 'OK', 2) != 0) {
            $system[7]['value'] = 0;
            $system[7]['error'] =  Text::_('COM_JOOMDLE_MOODLE_CANNOT_CONNECT_TO_JOOMLA');
        } else {
            $system[7]['value'] = 1;
            $system[7]['error'] =  '';
        }

        // Get installed Joomdle version in Joomla
        $xmlfile = JPATH_ADMINISTRATOR . '/components/com_joomdle/joomdle.xml';
        if (file_exists($xmlfile)) {
            if ($data = Installer::parseXMLInstallFile($xmlfile)) {
                $version =  $data['version'];
            }
        } else {
            $version = '';
        }

        $joomdle_release_joomla = $version;

        /* Test Moodle Web services in joomdle plugin */
        $system[3]['description'] = Text::_('COM_JOOMDLE_JOOMDLE_WEB_SERVICES');
        $response = ContentHelper::callMethodDebug('system_check');
        if ($response == '') {
            $system[3]['value'] = 0;
            $system[3]['error'] =  Text::_('COM_JOOMDLE_EMPTY_RESPONSE_FROM_MOODLE');
        } elseif ((is_array($response)) && (array_key_exists('exception', $response))) {
            $system[3]['value'] = 0;
            $system[3]['error'] =  $response['message'];

            if (array_key_exists('debuginfo', $response)) {
                $system[3]['error'] .= ' ' . $response['debuginfo'];
            }
        } else {
            if ($response ['joomdle_auth'] != 1) {
                $system[3]['value'] = 0;
                $system[3]['error'] =  Text::_('COM_JOOMDLE_JOOMDLE_AUTH_NOT_ENABLED');
            } elseif ($response['joomdle_configured'] == 0) {
                $system[3]['value'] = 0;
                $system[3]['error'] =  Text::_('COM_JOOMDLE_JOOMLA_URL_NOT_CONFIGURED_IN_MOODLE_PLUGIN');
            } elseif ($response['test_data'] != 'It works') {
                if ($response['test_data'] == '') {
                    $system[3]['value'] = 0;
                    $system[3]['error'] =  Text::_('COM_JOOMDLE_JOOMLA_URL_MISCONFIGURED_IN_MOODLE_PLUGIN');
                } else {
                    $system[3]['value'] = 0;
                    $system[3]['error'] = $response['test_data'];
                }
            } elseif ($response['release'] != $joomdle_release_joomla) {
                    $system[3]['value'] = 0;
                    $system[3]['error'] =  Text::_('COM_JOOMDLE_JOOMDLE_VERSION_MISMATCH');
            } else {
                $system[3]['value'] = 1;
                $system[3]['error'] = '';
            }
        }

        return $system;
    }

    public static function systemOk()
    {
        $comp_params = ComponentHelper::getParams('com_joomdle');
        $connection = $comp_params->get('connection_method');

        if ($connection == 'fgc') {
            $connection_method_enabled = ini_get('allow_url_fopen');
        } elseif ($connection == 'curl') {
            $connection_method_enabled = function_exists('curl_version') == "Enabled";
        }

        if (!$connection_method_enabled) {
            return false;
        }

        /* Test Moodle Web services in joomdle plugin */
        $response = ContentHelper::callMethodDebug('system_check');
        if ($response == '') {
            return false;
        } else {
            if ((!array_key_exists('joomdle_auth', $response)) || ($response ['joomdle_auth'] != 1)) {
                return false;
            } elseif ((!array_key_exists('joomdle_configured', $response)) || ($response ['joomdle_configured'] == 0)) {
                return false;
            } elseif ((!array_key_exists('test_data', $response)) || ($response ['test_data'] != 'It works')) {
                return false;
            }
        }

        return true;
    }

    public static function getFile($file)
    {
        $cm = ContentHelper::getConnectionMethod();

        if ($cm == 'fgc') {
            $response = ContentHelper::getFileFgc($file);
        } else {
            $response = ContentHelper::getFileCurl($file);
        }

        return $response;
    }

    public static function getFileFgc($file)
    {
        $comp_params = ComponentHelper::getParams('com_joomdle');
        $user_agent = $comp_params->get('user_agent', 'Joomdle');

        $context = stream_context_create(array('http' => array(
            'user_agent' => $user_agent
        )));
        $file = @file_get_contents($file, false, $context);

        return $file;
    }

    public static function getFileCurl($file)
    {
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, $file);

        $comp_params = ComponentHelper::getParams('com_joomdle');
        $user_agent = $comp_params->get('user_agent', 'Joomdle');

        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        return $output;
    }

    public static function getConnectionMethod()
    {
        $params = ComponentHelper::getParams('com_joomdle');
        $connection_method = $params->get('connection_method');
        return $connection_method;
    }

    private static function getRestUrl()
    {
        $params = ComponentHelper::getParams('com_joomdle');
        $moodle_rest_server_url = $params->get('MOODLE_URL') . '/webservice/rest/server.php?moodlewsrestformat=json&wstoken=' . $params->get('auth_token');

        return $moodle_rest_server_url;
    }


    public static function callMethodDebug($method, $params = array())
    {
        $cm = ContentHelper::getConnectionMethod();

        if ($cm == 'fgc') {
            $response = ContentHelper::callMethodDebugRestFgc($method, $params);
        } else {
            $response = ContentHelper::callMethodDebugRestCurl($method, $params);
        }

        return $response;
    }

    public static function callMethodDebugRestFgc($method, $params = array())
    {
        $moodle_rest_server_url = ContentHelper::getRestUrl();

        $url = $moodle_rest_server_url . '&wsfunction=joomdle_' . $method;

        $request = ContentHelper::getRequestRest($method, $params, $params2);

        $comp_params = ComponentHelper::getParams('com_joomdle');
        $user_agent = $comp_params->get('user_agent', 'Joomdle');

        $context = stream_context_create(array('http' => array(
            'method' => "POST",
            'header' => "Content-Type: application/x-www-form-urlencoded",
            'content' => $request,
            'user_agent' => $user_agent
        )));
        $file = @file_get_contents($url, false, $context);
        $file = trim($file);
        $response = json_decode($file, true);

        // Save raw reply to log
        $config = Factory::getConfig();
        $log_path = $config->get('log_path');
        file_put_contents($log_path . '/' . 'joomdle_system_check.json', $file);

        return $response;
    }

    public static function callMethodDebugRestCurl($method, $params = array())
    {
        $moodle_rest_server_url = ContentHelper::getRestUrl();

        $url = $moodle_rest_server_url . '&wsfunction=joomdle_' . $method;

        $request = ContentHelper::getRequestRest($method, $params);

        $headers = array();
        array_push($headers, "Content-Type: application/x-www-form-urlencoded");
        array_push($headers, "Content-Length: " . strlen($request));
        array_push($headers, "\r\n");

        $comp_params = ComponentHelper::getParams('com_joomdle');
        $user_agent = $comp_params->get('user_agent', 'Joomdle');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); # URL to post to
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # return into a variable
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); # custom headers, see above
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); # This POST is special, and uses its specified Content-type
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        $response = curl_exec($ch); # run!
        curl_close($ch);

        // Save raw reply to log
        $config = Factory::getConfig();
        $log_path = $config->get('log_path');
        file_put_contents($log_path . '/' . 'joomdle_system_check.json', $response);

        $response = trim($response);
        $response = json_decode($response, true);

        return $response;
    }

    public static function callMethod($method, $params = array())
    {
        $cm = ContentHelper::getConnectionMethod();

        if ($cm == 'fgc') {
            $response = ContentHelper::callMethodFgc($method, $params);
        } else {
            $response = ContentHelper::callMethodCurl($method, $params);
        }

        return $response;
    }

    public static function callMethodFgc($method, $params = array())
    {
        $moodle_rest_server_url = ContentHelper::getRestUrl();

        $url = $moodle_rest_server_url . '&wsfunction=joomdle_' . $method;

        $request = ContentHelper::getRequestRest($method, $params);

        $comp_params = ComponentHelper::getParams('com_joomdle');
        $user_agent = $comp_params->get('user_agent', 'Joomdle');

        $context = stream_context_create(array('http' => array(
            'method' => "POST",
            'header' => "Content-Type: application/x-www-form-urlencoded",
            'content' => $request,
            'user_agent' => $user_agent
        )));
        $file = @file_get_contents($url, false, $context);
        $file = trim($file);
        $response = json_decode($file, true);

        return $response;
    }

    public static function callMethodCurl($method, $params = array())
    {
        $moodle_rest_server_url = ContentHelper::getRestUrl();

        $url = $moodle_rest_server_url . '&wsfunction=joomdle_' . $method;

        $request = ContentHelper::getRequestRest($method, $params);

        $comp_params = ComponentHelper::getParams('com_joomdle');
        $user_agent = $comp_params->get('user_agent', 'Joomdle');

        $headers = array();
        array_push($headers, "Content-Type: application/x-www-form-urlencoded");
        array_push($headers, "Content-Length: " . strlen($request));
        array_push($headers, "\r\n");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); # URL to post to
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # return into a variable
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); # custom headers, see above
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); # This POST is special, and uses its specified Content-type
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        $response = curl_exec($ch); # run!
        curl_close($ch);

        $response = trim($response);
        $response = json_decode($response, true);

        return $response;
    }

    private static function getRequestRest($method, $params)
    {
        $rest_params = ContentHelper::formatPostdataForCurlcall($params);

        return $rest_params;
    }

    public static function formatArrayPostdataForCurlcall($arraydata, $currentdata, &$data)
    {
        foreach ($arraydata as $k => $v) {
            $newcurrentdata = $currentdata;
            if (is_object($v)) {
                $v = (array) $v;
            }
            if (is_array($v)) { //the value is an array, call the function recursively
                $newcurrentdata = $newcurrentdata . '[' . urlencode($k) . ']';
                ContentHelper::formatArrayPostdataForCurlcall($v, $newcurrentdata, $data);
            } else { //add the POST parameter to the $data array
                $data[] = $newcurrentdata . '[' . urlencode($k) . ']=' . urlencode($v);
            }
        }
    }

    public static function formatPostdataForCurlcall($postdata)
    {
        if (is_object($postdata)) {
            $postdata = (array) $postdata;
        }
        $data = array();
        foreach ($postdata as $k => $v) {
            if (is_object($v)) {
                $v = (array) $v;
            }
            if (is_array($v)) {
                $currentdata = urlencode($k);
                ContentHelper::formatArrayPostdataForCurlcall($v, $currentdata, $data);
            } else {
                $data[] = urlencode($k ?? '') . '=' . urlencode($v ?? '');
            }
        }
        $convertedpostdata = implode('&', $data);
        return $convertedpostdata;
    }

    // FIXME NOT USED?
    /*
    public static function getCourseUrl($course_id)
    {
        $app = Factory::getApplication('site');

        $user = Factory::getApplication()->getIdentity();
        $username = $user->username;

        if (!$user->id) {
            $username = 'guest';
        }

        $session = Factory::getSession();
        $token = md5($session->getId());

        $itemid   = Factory::getApplication()->input->get('Itemid');
        $params = $app->getParams();

        $comp_params = ComponentHelper::getParams('com_joomdle');
        if ($comp_params->get('goto_course_button') == 'moodle') {
            if ($comp_params->get('linkstarget') == 'wrapper') {
                $open_in_wrapper = 1;
            } else {
                $open_in_wrapper = 0;
            }
            $moodle_auth_land_url = $comp_params->get('MOODLE_URL') . '/auth/joomdle/land.php';
            // FIXME esto del land ya habria q quirarlo

            $url = $moodle_auth_land_url . "?username=$username&token=$token&mtype=course&id=$course_id&use_wrapper=$open_in_wrapper&Itemid=$itemid";
        } else // link to course Joomdle view
        {
            $itemid = $params->get('courseview_itemid');
            $url = Route::_("index.php?option=com_joomdle&view=course&course_id=$course_id&Itemid=$itemid");
        }

        return $url;
    }
    */

    public static function getMenuItem()
    {
        $app = Factory::getApplication();
        $menu = $app->getMenu();
        $menuItem = $menu->getActive();

        if (!$menuItem) {
            return;
        }

        $itemid = $menuItem->id;

        return $itemid;
    }

    public static function getLanguageStr($lang)
    {
        require_once(dirname(__FILE__) . '/' . 'languages.php');
        $l = explode("_", $lang);
        $index = $l[0];

        return $LANGUAGES["$index"];
    }

    public static function getLang()
    {
        $client_lang = '';
        $lang_known = false;
        $lang = Factory::getApplication()->getInput()->get('lang');

        if ($lang) {
            //lang set via GET/POST
            $client_lang = $lang;
            $lang_known = true;
        }

        if ($lang_known) {
            return $client_lang;
        } else {
            return false;
        }
    }

    public static function syncUser($user, $isnew, $success, $msg)
    {
        $app = Factory::getApplication('site');

        $comp_params = ComponentHelper::getParams('com_joomdle');

        /* Don't create user if not configured to do so */
        if (($isnew) && (!$comp_params->get('auto_create_users'))) {
            return;
        }

        $username = $user['username'];
        $moodle_user = ContentHelper::getUserId($username);

        // If user doesn't exist in Moodle, and it is not a new user, nothing to do.
        if ((!$moodle_user) && (!$isnew)) {
                return;
        }

        $is_child = false;
        if (array_key_exists('params', $user)) {
            if (is_string($user['params'])) { // check added because CB passes different info in params
                if (strstr($user['params'], '_parent_id')) {
                    $is_child = true;
                }
            }
        }

        // If we reach here, user has to be created.
        ContentHelper::createJoomdleUser($username);
    }

    public static function logIntoMoodle($username, $token)
    {
        $app = Factory::getApplication('site');

        $comp_params = ComponentHelper::getParams('com_joomdle');

        $moodle_url = $comp_params->get('MOODLE_URL');
        $cookie_path = $comp_params->get('cookie_path', "/");

        $username = str_replace(' ', '%20', $username);
        $login_url = $moodle_url;
        $land_file = $moodle_url . "/auth/joomdle/land.php?username=$username&token=$token&use_wrapper=0&create_user=1";

        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, $land_file);

        $config = Factory::getConfig();
        $temppath = $config->get('tmp_path');
        $file = $temppath . "/" . UserHelper::genRandomPassword() . ".txt";

        // First make sure we can write to file
        touch($file);
        if (!file_exists($file)) {
            die(Text::sprintf('COM_JOOMDLE_ERROR_CANT_WRITE_CURL_FILE', $file));
        }

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $file);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        // Accept certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Set user agent
        $user_agent = $comp_params->get('user_agent', 'Joomdle');

        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

        $output = curl_exec($ch);
        curl_close($ch);

        unset($ch);

        $f = fopen($file, 'ro');

        if (!$f) {
            die(Text::sprintf('COM_JOOMDLE_ERROR_CANT_OPEN_CURL_FILE', $file));
        }

        while (!feof($f)) {
                $line = fgets($f);
            if (($line == '\n') || ((is_array($line)) && ($line[0] == '#'))) {
                    continue;
            }
                $parts = explode("\t", $line);
            if (array_key_exists(5, $parts)) {
                $name = $parts[5];
                $value = trim($parts[6]);
                setcookie($name, $value, 0, $cookie_path);
            }
        }
        unlink($file);
    }

    public static function deleteMoodleUser($user)
    {
        $app = Factory::getApplication('site');

        $username = ArrayHelper::getValue($user, 'username', '', 'string');

        $reply = ContentHelper::deleteUser($username);

        if ($reply) {
            $app->enqueueMessage(Text::_('COM_JOOMDLE_USER_DELETED_FROM_MOODLE'));
        }
    }

    public static function getJumpURL($data)
    {
        $params = ComponentHelper::getParams('com_joomdle');
        $moodle_auth_land_url = $params->get('MOODLE_URL') . '/auth/joomdle/land.php';

         $linkstarget = $params->get('linkstarget');
        if ($linkstarget == 'wrapper') {
            $use_wrapper = 1;
        } else {
            $use_wrapper = 0;
        }

        if ($use_wrapper) {
            $url = URI::root() . 'index.php?option=com_joomdle&view=wrapper&moodle_page_type=' . $data['moodle_page_type'] . '&id=' . $data['id'];

            $default_itemid = $params->get('default_itemid');
            if ($default_itemid) {
                $url .= "&itemid=$default_itemid";
            }
        } else {
            $url = $params->get('MOODLE_URL') . "/course/view.php?id=" . $data['id'];

            if ($data['lang']) {
                $url .= "&lang=$lang";
            }
        }

         return $url;
    }

    public static function multisort($array, $order_dir, $sort_by, $key1, $key2 = null, $key3 = null, $key4 = null, $key5 = null, $key6 = null, $key7 = null, $key8 = null)
    {
        if (!count($array)) {
            return $array;
        }

        foreach ($array as $pos => $val) {
            $tmp_array[$pos] = $val[$sort_by];
        }

        $order_dir = strtolower($order_dir);
        if ($order_dir == 'desc') {
            arsort($tmp_array);
        } else {
            asort($tmp_array);
        }

        // display however you want
        foreach ($tmp_array as $pos => $val) {
            $return_array[$pos][$sort_by] = $array[$pos][$sort_by];
            $return_array[$pos][$key1] = $array[$pos][$key1];
            if (isset($key2)) {
                $return_array[$pos][$key2] = $array[$pos][$key2];
            }
            if (isset($key3)) {
                $return_array[$pos][$key3] = $array[$pos][$key3];
            }
            if (isset($key4)) {
                $return_array[$pos][$key4] = $array[$pos][$key4];
            }
            if (isset($key5)) {
                $return_array[$pos][$key5] = $array[$pos][$key5];
            }
            if (isset($key6)) {
                $return_array[$pos][$key6] = $array[$pos][$key6];
            }
            if (isset($key7)) {
                $return_array[$pos][$key7] = $array[$pos][$key7];
            }
            if (isset($key8)) {
                $return_array[$pos][$key8] = $array[$pos][$key8];
            }
        }

        return $return_array;
    }

    public static function isJoomlaAdmin($userid)
    {
        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($userid);
        $groups = UserHelper::getUserGroups($user->id);

        $admin_groups = array();
        $admin_groups[] = "Super Users";
        $admin_groups[] = "Administrator";

        foreach ($admin_groups as $temp) {
            if (!empty($groups[$temp])) {
                return true;
            }
        }

        return false;
    }

    // Used in Joomdle->Users in backend to sync users from Moodle to Joomla
    public static function createJoomlaUser($username)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        // Get required system objects
        $user = Factory::getApplication()->getIdentity();
        $config = Factory::getConfig();

        $usersConfig = ComponentHelper::getParams('com_users');
        $useractivation = $usersConfig->get('useractivation');

        $newUsertype = 'Registered';

        $moodle_user = array();
        $moodle_user['username'] = $username;
        $user_details = ContentHelper::userDetails($username);

        $moodle_user['name'] = $user_details['firstname'] . ' ' . $user_details['lastname'];
        $moodle_user['email'] = $user_details['email'];

        $moodle_user['activation'] = '';
        $moodle_user['sendEmail'] = 0;

        // Bind the post array to the user object
        if (!$user->bind($moodle_user, 'usertype')) {
            Factory::getApplication()->enqueueMessage($user->getError(), 'error');
            return false;
        }

        // Set some initial user values
        $user->set('id', 0);
        $user->set('usertype', $newUsertype);

        $system = 2; // ID of Registered
        $user->groups = array ();
        $user->groups[] = $system;

        $date = Factory::getDate();
        $user->set('registerDate', $date->toSql());

        $user->set('lastvisitDate', $db->getNullDate());

        if (!$user->save()) {
            $error = Text::_($user->getError());
            Factory::getApplication()->enqueueMessage($error, 'error');
            return false;
        }

        // Manually store password from Moodle
        // FIXME Esto ya no va, no es el mismo hash: poner password random y enviar?
        $user->password = $user_details['password'];
        $user->save();
    }

    // Used by the web service call to sync a moodle user on registration
    public static function createJoomlaUserWs($user_info)
    {
        // Do nothing if user already exists
        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserByUsername($user_info['username']);
        if ($user->id) {
            return array();
        }

        $db = Factory::getContainer()->get('DatabaseDriver');

        $usersConfig = ComponentHelper::getParams('com_users');

        $user = new User();

        // Initialize new usertype setting
        $newUsertype = $usersConfig->get('new_usertype');
        if (!$newUsertype) {
                $newUsertype = 2;
        }

        // Password comes in cleartext. On bind, Joomla hashes it again: Only for user registrations, not valid for admin user add/upload

        // Bind the user_info array to the user object
        if (!$user->bind($user_info)) {
            $error = Text::_($user->getError());
            Factory::getApplication()->enqueueMessage($error, 'error');
            return false;
        }

        // Set some initial user values
        $user->set('id', 0);
        $user->groups = array ();
        $user->groups[] = $newUsertype;

        $date = Factory::getDate();
        $user->set('registerDate', $date->toSql());

        if ($user_info['block']) {
            $user->set('block', '1');
        }

        if (!$user_info['confirmed']) {
            $user->set('activation', bin2hex(random_bytes(10)));
        }

        // If there was an error with registration
        if (!$user->save()) {
            $error = Text::_($user->getError());
            Factory::getApplication()->enqueueMessage($error, 'error');
            return false;
        }

        /* Update profile additional data */
        return MappingsHelper::saveUserInfo($user_info);
    }


    public static function filterByValue($array, $index, $value)
    {
        $newarray = array ();
        if (is_array($array) && count($array) > 0) {
            foreach (array_keys($array) as $key) {
                if (array_key_exists($index, $array[$key])) {
                    $temp[$key] = $array[$key][$index];
                } else {
                    $temp[$key] = 0;
                }

                if (in_array($temp[$key], $value)) {
                    $newarray[$key] = $array[$key];
                }
            }
        }
        return $newarray;
    }

    public static function excludeByValue($array, $index, $value)
    {
        $newarray = array ();
        if (is_array($array) && count($array) > 0) {
            foreach (array_keys($array) as $key) {
                if (array_key_exists($index, $array[$key])) {
                    $temp[$key] = $array[$key][$index];
                } else {
                    $temp[$key] = 0;
                }

                if (!in_array($temp[$key], $value)) {
                    $newarray[$key] = $array[$key];
                }
            }
        }
        return $newarray;
    }
}
