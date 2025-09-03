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

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Supports a value from an external table
 *
 * @since  2.0.0
 */
class JoomdleuserField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  2.0.0
     */
    protected $type = 'joomdleuser';

    protected $layout = 'joomla.form.field.list';

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
        $options = array();

        $option = array ('value' => '', 'text' => Text::_('COM_JOOMDLE_SELECT_USERS'));
        $options[] = $option;
        $option = array ('value' => 'joomla', 'text' => Text::_('COM_JOOMDLE_JOOMLA_USERS'));
        $options[] = $option;
        $option = array ('value' => 'moodle', 'text' => Text::_('COM_JOOMDLE_MOODLE_USERS'));
        $options[] = $option;
        $option = array ('value' => 'joomdle', 'text' => Text::_('COM_JOOMDLE_JOOMLDE_USERS'));
        $options[] = $option;
        $option = array ('value' => 'not_joomdle', 'text' => Text::_('COM_JOOMDLE_NOT_JOOMDLE_USERS'));
        $options[] = $option;

        return $options;
    }
}
