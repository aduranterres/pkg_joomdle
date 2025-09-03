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
use Joomdle\Component\Joomdle\Administrator\Helper\MappingsHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Supports a value from an external table
 *
 * @since  2.0.0
 */
class JoomlafieldField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  2.0.0
     */
    protected $type = 'joomlafield';

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

        $params = ComponentHelper::getParams('com_joomdle');
        $app = $params->get('additional_data_source');

        $joomla_fields = MappingsHelper::getFields($app);

        if (is_array($joomla_fields)) {
            foreach ($joomla_fields as $field) {
                $option['value'] = $field->id;
                $option['text'] = Text::_($field->name);

                $options[] = $option;
            }
        }

        return $options;
    }
}
