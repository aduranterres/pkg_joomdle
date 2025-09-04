<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Field\ListField;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\CMS\Component\ComponentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Supports a value from an external table
 *
 * @since  2.0.0
 */
class ThemeField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  2.0.0
     */
    protected $type = 'theme';

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
        $options = array ();

        // If Joomdle not configured, return
        $params = ComponentHelper::getParams('com_joomdle');
        if ($params->get('MOODLE_URL') == "") {
            return $options;
        }

        // If any fatal error in system check, return
        if (!ContentHelper::systemOk()) {
            return $options;
        }

        $themes = ContentHelper::getThemes();

        $option = new \stdClass();
        $option->value = '';
        $option->text = Text::_('COM_JOOMDLE_DEFAULT');
        $options[] = $option;

        $current_level = 0;
        foreach ($themes as $theme) {
            $option = new \stdClass();
            $option->value = $theme['name'];
            $option->text = $theme['name'];
            $options[] = $option;
        }

        return $options;
    }
}
