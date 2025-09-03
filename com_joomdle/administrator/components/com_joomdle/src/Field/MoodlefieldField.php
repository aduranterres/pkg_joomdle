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
class MoodlefieldField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  2.0.0
     */
    protected $type = 'moodlefield';

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

        $fields = array ('firstname', 'lastname', 'email', 'phone1', 'phone2', 'institution',
                'department', 'address', 'city', 'country', 'lang', 'timezone', 'idnumber', 'description', 'lastnamephonetic',
                'firstnamephonetic', 'middlename', 'alternatename', 'id');

        foreach ($fields as $field) {
            $option['value'] = $field;
            $option['text'] = $field;

            $options[] = $option;
        }

        $moodle_custom_fields = MappingsHelper::getMoodleFields();
        foreach ($moodle_custom_fields as $mf) {
            $option['value'] = "cf_" . $mf['id'];
            $option['text'] = $mf['shortname'] . ' - ' . $mf['name'];

            $options[] = $option;
        }

        return $options;
    }
}
