<?php

/**
 * @package     Joomdle
 * @subpackage  mod_joomdle_my_courses
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Module\JoomdleMyCourses\Site\Dispatcher;

use Joomla\CMS\Factory;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Helper\ModuleHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;
use Joomdle\Component\Joomdle\Site\Model\MycoursesModel;

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

        $user = Factory::getApplication()->getIdentity();

        $group_by_category = $params->get('group_by_category', 0);

        $data['linkto'] = $params->get('linkto');
        $data['nocourses_text'] = $params->get('nocourses_text');
        $data['show_unenrol_link'] = $params->get('show_unenrol_link');
        $data['courses_shown'] = $params->get('courses_shown');
        $data['courses_not_shown'] = $params->get('courses_not_shown');
        $data['categories_shown'] = $params->get('categories_shown');
        $data['categories_not_shown'] = $params->get('categories_not_shown');
        $data['group_by_category'] = 0;

        $mycourses_model = new MycoursesModel();
        $data['courses'] = $mycourses_model->getCourses($data);

        return $data;
    }
}
