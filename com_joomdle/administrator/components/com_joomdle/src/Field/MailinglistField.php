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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\Event;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Supports a value from an external table
 *
 * @since  2.0.0
 */
class MailinglistField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  2.0.0
     */
    protected $type = 'mailinglist';

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

        $option = array ('value' => 'no', 'text' => Text::_('COM_JOOMDLE_NONE'));
        $options[] = $option;

        // Add items added via plugins
        PluginHelper::importPlugin('joomdlemailinglist');
        $app = Factory::getApplication();

        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new Event('onJoomdleGetMailingList', []);
        $dispatcher->dispatch('onJoomdleGetMailingList', $event);
        $more_items = $event->getArgument('results') ?? null;

        if (is_array($more_items)) {
            foreach ($more_items as $item) {
                $keys = array_keys($item);
                $key = $keys[0];
                $item_name = array_shift($item);
                $option['value'] = $key;
                $option['text'] = $item_name;

                $options[] = $option;
            }
        }

        return $options;
    }
}
