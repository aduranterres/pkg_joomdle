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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\Event\Event;
use Joomla\Database\ParameterType;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\User\UserFactoryInterface;

/**
 * Shop helper.
 *
 * @since  1.0.0
 */
class ShopHelper
{
    public static function getShopCourses()
    {
        $params = ComponentHelper::getParams('com_joomdle');
        $shop = $params->get('shop_integration');

        $courses = array ();
        PluginHelper::importPlugin('joomdleshop');
        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new Event('onJoomdleGetShopCourses', []);
        $dispatcher->dispatch('onJoomdleGetShopCourses', $event);
        $items = $event->getArgument('results') ?? null;

        foreach ($items as $courses) {
            if (count($courses)) {
                break;
            }
        }

        return $courses;
    }

    public static function getBundles()
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__joomdle_bundles'));

        $db->setQuery($query);
        $data = $db->loadAssocList();

        if (!$data) {
            $data = array ();
        }

        $i = 0;
        $c = array ();
        foreach ($data as $bundle) {
            $c[$i] = new \stdClass();
            $c[$i]->id = $bundle['id'];
            $c[$i]->name = $bundle['name'];
            $c[$i]->fullname = $bundle['name'];
            $c[$i]->description = $bundle['description'];
            $c[$i]->cost = $bundle['cost'];
            $c[$i]->currency = $bundle['currency'];
            $c[$i]->published = ShopHelper::isCourseOnSell('bundle_' . $bundle['id']);
            $c[$i]->is_bundle = true;
            $i++;
        }
//echo "<pre>"; print_R ($c);

        return $c;
    }

    public static function publishCourses($courses)
    {
        foreach ($courses as $course_id) {
            $course_array = array ($course_id);
            if (ShopHelper::isCourseOnSell($course_id)) {
                ShopHelper::dontSellCourses($course_array);
            } else {
                ShopHelper::sellCourses($course_array);
            }
        }
    }

    public static function isCourseOnSell($course_id)
    {
        $params = ComponentHelper::getParams('com_joomdle');
        $shop = $params->get('shop_integration');

        if (!$shop) {
            return false;
        }

        $on_sell = false;
        PluginHelper::importPlugin('joomdleshop');
        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new Event('onJoomdleIsCourseOnSell', ['course_id' => $course_id]);
        $dispatcher->dispatch('onJoomdleIsCourseOnSell', $event);
        $items = $event->getArgument('results') ?? null;

        foreach ($items as $on_sell) {
            if ($on_sell !== false) { // We check for FALSE, as returned by non configured plugins
                break;
            }
        }

        return $on_sell;
    }

    public static function sellCourses($courses)
    {
        $params = ComponentHelper::getParams('com_joomdle');
        $shop = $params->get('shop_integration');

        PluginHelper::importPlugin('joomdleshop');
        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new Event('onJoomdleSellCourses', ['courses' => $courses]);
        $dispatcher->dispatch('onJoomdleSellCourses', $event);
    }

    public static function dontSellCourses($courses)
    {
        $params = ComponentHelper::getParams('com_joomdle');
        $shop = $params->get('shop_integration');

        PluginHelper::importPlugin('joomdleshop');
        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new Event('onJoomdleDontSellCourses', ['courses' => $courses]);
        $dispatcher->dispatch('onJoomdleDontSellCourses', $event);
    }

    public static function reloadCourses($courses)
    {
        $params = ComponentHelper::getParams('com_joomdle');
        $shop = $params->get('shop_integration');

        PluginHelper::importPlugin('joomdleshop');
        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new Event('onJoomdleReloadCourses', ['courses' => $courses]);
        $dispatcher->dispatch('onJoomdleReloadCourses', $event);
    }

    public static function deleteCourses($courses)
    {
        $params = ComponentHelper::getParams('com_joomdle');
        $shop = $params->get('shop_integration');

        $db = Factory::getContainer()->get('DatabaseDriver');
        foreach ($courses as $sku) {
            if (strncmp($sku, 'bundle_', 7) == 0) {
                $bundle_id = substr($sku, 7);
                $query = "DELETE FROM  #__joomdle_bundles  where id = " . $db->Quote($bundle_id);
                $db->setQuery($query);
                if (!$db->execute()) {
                    $error = Text::_($db->getError());
                    Factory::getApplication()->enqueueMessage($error, 'error');
                    return false;
                }
            }
        }

        PluginHelper::importPlugin('joomdleshop');
        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new Event('onJoomdleDeleteCourses', ['courses' => $courses]);
        $dispatcher->dispatch('onJoomdleDeleteCourses', $event);
    }

    public static function sendConfirmationEmail($email, $course_id)
    {
        $app = Factory::getApplication();

        $comp_params = ComponentHelper::getParams('com_joomdle');
        $linkstarget = $comp_params->get('linkstarget');
        $moodle_url = $comp_params->get('MOODLE_URL');
        $email_subject = $comp_params->get('enrol_email_subject');
        $email_text = $comp_params->get('enrol_email_text');

        if ($linkstarget == 'wrapper') {
            /* XXX After and hour tryng and searching I could not find the GOOD way
               to do this, so I do this kludge and it seems to work ;)
               */
            $url = URI::base();
            $pos = strpos($url, '/administrator/');
            if ($pos) {
                $url = substr($url, 0, $pos);
            }
            $url = trim($url, '/');
            $url = $url . '/index.php?option=com_joomdle&view=wrapper&moodle_page_type=course&id=' . $course_id;
        } else {
            $url = $moodle_url . '/course/view.php?id=' . $course_id;
        }

        $course_info = ContentHelper::getCourseInfo((int) $course_id);
        $name = $course_info['fullname'];

        // Replace variables in text
        $email_text = str_replace('COURSE_NAME', $name, $email_text);
        $email_text = str_replace('COURSE_URL', $url, $email_text);
        $email_subject = str_replace('COURSE_NAME', $name, $email_subject);
        $email_subject = str_replace('COURSE_URL', $url, $email_subject);

        // Set the e-mail parameters
        $from           = $app->getCfg('mailfrom');
        $fromname       = $app->getCfg('fromname');

        // Send the e-mail
        if (!Factory::getMailer()->sendMail($from, $fromname, $email, $email_subject, $email_text, true)) {
                return false;
        }

        return true;
    }

    public static function getBundleInfo($bundle_id)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__joomdle_bundles'))
            ->where($db->quoteName('id') . ' = :bundle_id')
            ->bind(':bundle_id', $bundle_id, ParameterType::INTEGER);

        $db->setQuery($query);
        $data = $db->loadAssoc();

        return $data;
    }


    public static function enrolBundle($username, $bundle_id)
    {
        $user_id = UserHelper::getUserId($username);
        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($user_id);
        $email =  $user->email;

        $bundle = ShopHelper::getBundleInfo($bundle_id);
        $courses = explode(',', $bundle['courses']);

        $comp_params = ComponentHelper::getParams('com_joomdle');
        $send_bundle_emails = $comp_params->get('send_bundle_emails');
        $c = array ();
        foreach ($courses as $course_id) {
            if ($send_bundle_emails != 0) {
                if ($send_bundle_emails == 1) { // Send one email per course
                    ShopHelper::sendConfirmationEmail($email, $course_id);
                }
            }
            $course['id'] = (int) $course_id;
            $c[] = $course;
        }

        if ($send_bundle_emails == 2) { // Send one email per bundle
            ShopHelper::sendBundleEmail($email, $bundle);
        }

        ContentHelper::multipleEnrol($username, $c);
    }

    public static function sendBundleEmail($email, $bundle)
    {
        $app = JFactory::getApplication();

        $comp_params = JComponentHelper::getParams('com_joomdle');
        $linkstarget = $comp_params->get('linkstarget');
        $moodle_url = $comp_params->get('MOODLE_URL');
        $email_subject = $comp_params->get('bundle_email_subject');
        $email_text = $comp_params->get('bundle_email_text');

        $course_ids = explode(',', $bundle['courses']);
        $courses = JoomdleHelperContent::getCourseList();
        $courses_text = '';
        foreach ($courses as $course) {
            if (!in_array($course['remoteid'], $course_ids)) {
                continue;
            }

            $course_id = $course['remoteid'];

            if ($linkstarget == 'wrapper') {
                /* XXX After and hour tryng and searching I could not find the GOOD way
                   to do this, so I do this kludge and it seems to work ;)
                   */
                $url            = JURI::base();
                $pos =  strpos($url, '/administrator/');
                if ($pos) {
                    $url = substr($url, 0, $pos);
                }
                $url = trim($url, '/');
                $url            = $url . '/index.php?option=com_joomdle&view=wrapper&moodle_page_type=course&id=' . $course_id;
            } else {
                $url = $moodle_url . '/course/view.php?id=' . $course_id;
            }

            $courses_text .= "<a href='$url'>" . $course['fullname'] . "</a><br>";
        }

        // Replace variables in text
        $email_text = str_replace('BUNDLE_NAME', $bundle['name'], $email_text);
        $email_text = str_replace('BUNDLE_COURSES', $courses_text, $email_text);
        $email_subject = str_replace('BUNDLE_NAME', $bundle['name'], $email_subject);

        // Set the e-mail parameters
        $from           = $app->getCfg('mailfrom');
        $fromname       = $app->getCfg('fromname');

        // Send the e-mail
        if (!JFactory::getMailer()->sendMail($from, $fromname, $email, $email_subject, $email_text, true)) {
                return false;
        }

        return true;
    }
}
