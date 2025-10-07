<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Site\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Supports a value from an external table
 *
 * @since  2.0.0
 */
class CourselistField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  2.0.0
     */
    protected $type = 'courselist';

    protected $layout = 'joomla.form.field.list-fancy-select';

    /**
     * The translate.
     *
     * @var    boolean
     * @since  2.0.0
     */
    protected $translate = true;

    protected $header = false;

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   2.0.0
     */
    protected function getOptions()
    {
        $user = Factory::getApplication()->getIdentity();
        $courses = ContentHelper::getCourseList();

        $options = array();

        foreach ($courses as $course) {
            $option = new \stdClass();
            $option->value = $course['remoteid'];
            $option->text = $course['fullname'];
            $options[] = $option;
        }

        return $options;
    }
}
