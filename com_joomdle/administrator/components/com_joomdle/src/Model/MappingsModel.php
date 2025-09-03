<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\MappingsHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Methods supporting a list of Mappings records.
 *
 * @since  2.0.0
 */
class MappingsModel extends ListModel
{
    /**
    * Constructor.
    *
    * @param   array  $config  An optional associative array of configuration settings.
    *
    * @see        JController
    * @since      1.6
    */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'joomla_app',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   Elements order
     * @param   string  $direction  Order direction
     *
     * @return void
     *
     * @throws Exception
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // List state information.
        parent::populateState("a.id", "ASC");

        $context = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $context);

        $joomla_app = $this->getUserStateFromRequest($this->context . '.filter.joomla_app', 'filter_joomla_app');
        $this->setState('filter.joomla_app', $joomla_app);

        // Split context into component and optional section
        if (!empty($context)) {
            $parts = FieldsHelper::extract($context);

            if ($parts) {
                $this->setState('filter.component', $parts[0]);
                $this->setState('filter.section', $parts[1]);
            }
        }
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string  $id  A prefix for the store id.
     *
     * @return  string A store id.
     *
     * @since   2.0.0
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.joomla_app');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  DatabaseQuery
     *
     * @since   2.0.0
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db    = $this->getDatabase();
        $query = $db->createQuery();

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.*'
            )
        );

        $query->from('#__joomdle_field_mappings AS a');

        $search = $this->getState('filter.search');
        if ($search) {
            $search = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where('(a.joomla_field LIKE ' . $search . ' OR a.moodle_field LIKE ' . $search . ')');
        }

        $joomla_app = $this->getState('filter.joomla_app');
        if ($joomla_app) {
            $query->where('a.joomla_app = ' . $db->quote($joomla_app));
        }

        $orderCol    = $this->state->get('list.ordering', 'name');
        $orderDirn     = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();

        $items2 = array();
        foreach ($items as $item) {
            $item->joomla_field_name = MappingsHelper::getFieldName($item->joomla_app, $item->joomla_field);
            $item->moodle_field_name = MappingsHelper::getMoodleFieldName($item->moodle_field);

            $items2[] = $item;
        }

        return $items2;
    }
}
