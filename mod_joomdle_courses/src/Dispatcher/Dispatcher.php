<?php

/**
 * @package     Joomdle
 * @subpackage  mod_joomdle_courses
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Module\JoomdleCourses\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Helper\ModuleHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Dispatcher class for mod_joomdle_courses
 *
 * @since  4.4.0
 */
class Dispatcher extends AbstractModuleDispatcher // implements HelperFactoryAwareInterface
{
    /**
     * Returns the layout data.
     *
     * @return  array
     *
     * @since   4.4.0
     */
    protected function getLayoutData(): array
    {
        $data   = parent::getLayoutData();

        $params = $data['params'];

        $guest_courses_only = $params->get('guest courses only', 0);

        $sort_by = $params->get('sort_by', 'name');
        switch ($sort_by) {
            case 'date':
                $order = 'created DESC';
                break;
            case 'sortorder':
                $order = 'sortorder ASC';
                break;
            default:
                $order = 'fullname ASC';
                break;
        }

        $courses = ContentHelper::getCourseList(0, $order, $guest_courses_only);

        if ($params->get('latest courses only')) {
            $limit = $params->get('latest courses only');
        } else {
            $limit = PHP_INT_MAX; //no limit
        }
        $data['limit'] = $limit;

        if ($params->get('courses_shown')) {
            if (is_array($params->get('courses_shown'))) {
                $courses_shown = $params->get('courses_shown');
            } else {
                $courses_shown = array ($params->get('courses_shown'));
            }

            $courses = ContentHelper::filterByValue($courses, 'remoteid', $courses_shown);
        }
        if ($params->get('categories_shown')) {
            if (is_array($params->get('categories_shown'))) {
                $cats_shown = $params->get('categories_shown');
            } else {
                $cats_shown = array ( $params->get('categories_shown'));
            }

            $courses = ContentHelper::filterByValue($courses, 'cat_id', $cats_shown);
        }
        if ($params->get('free courses only')) {
            $courses = ContentHelper::filterByValue($courses, 'cost', array (0));
        }

        $data['courses'] = $courses;

        $data['linkto'] = $params->get('linkto');

        return $data;
    }
}
