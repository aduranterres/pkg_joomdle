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
        return array(); // FIXME
        $db           = JFactory::getDBO();
        $query = 'SELECT * ' .
            ' FROM #__joomdle_bundles' ;
        $db->setQuery($query);
        $data = $db->loadAssocList();

        if (!$data) {
            $data = array ();
        }

        $i = 0;
        $c = array ();
        foreach ($data as $bundle) {
                $c[$i] = new JObject();
                $c[$i]->id = $bundle['id'];
                $c[$i]->name = $bundle['name'];
                $c[$i]->fullname = $bundle['name'];
                $c[$i]->description = $bundle['description'];
                $c[$i]->cost = $bundle['cost'];
                $c[$i]->currency = $bundle['currency'];
                $c[$i]->published = JoomdleHelperShop::is_course_on_sell('bundle_' . $bundle['id']);
                $c[$i]->is_bundle = true;
                $i++;
        }

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
        $event = new Event('onJoomdleIsCourseOnSell', []);
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
}
