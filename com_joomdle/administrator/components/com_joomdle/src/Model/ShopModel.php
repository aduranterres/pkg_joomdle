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
use Joomdle\Component\Joomdle\Administrator\Helper\ShopHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Shop model.
 *
 * @since  2.0.0
 */
class ShopModel extends ListModel
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
                'name',
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
        $id .= ':' . $this->getState('filter.state');

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

        $filter_type = $this->getState('filter.state');
        if ($filter_type) {
                $query->where('a.joomla_app = ' . $db->quote($filder_type));
        }

        $orderCol    = $this->state->get('list.ordering', 'name');
        $orderDirn     = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    public function getItems()
    {
        $pagination = $this->getPagination();
        $limitstart = $pagination->limitstart;

        $limit = $pagination->limit;

        $listOrder  = $this->state->get('list.ordering');
        $listDirn   = $this->state->get('list.direction');
        $filter_order = $listOrder; // Not really used, always sort by course name
        $filter_order_Dir = $listDirn;

        $filter_type = $this->getState('filter.state');

        $bundles = ShopHelper::getBundles();
        $courses = ShopHelper::getShopCourses();

        $products = array_merge($bundles, $courses);
        usort($products, array($this, "cmp"));

        if ($filter_order_Dir == 'DESC') {
            $products = array_reverse($products);
        }

        $published = $this->getState('filter.published', '');
        $search = $this->getState('filter.search', '');
        $producttype = $this->getState('filter.producttype', '');
        $p = array ();
        foreach ($products as $product) {
            if ($search != '') {
                if (!stristr($product->fullname, $search)) {
                    continue;
                }
            }
            if ($published !== '') {
                if ($product->published != $published) {
                    continue;
                }
            }
            if ($producttype !== '') {
                if ($producttype == 'bundles') {
                    if (!$product->is_bundle) {
                        continue;
                    }
                } elseif ($producttype == 'courses') {
                    if ($product->is_bundle) {
                        continue;
                    }
                }
            }
            $p[] = $product;
        }
        $products = $p;

        $pagination = $this->getPagination();
        $limitstart = $pagination->limitstart;
        $limit = $pagination->limit;

        return array_slice($products, $limitstart, $limit, true);
    }

    private function cmp($a, $b)
    {
        return strcasecmp($a->fullname, $b->fullname);
    }

    private function getTotal()
    {
        $bundles = ShopHelper::getBundles();
        $courses = ShopHelper::getShopCourses();

        $products = array_merge($bundles, $courses);

        $published = $this->getState('filter.published', '');
        $search = $this->getState('filter.search', '');
        $producttype = $this->getState('filter.producttype', '');
        $p = array ();
        foreach ($products as $product) {
            if ($search != '') {
                if (!stristr($product->fullname, $search)) {
                    continue;
                }
            }
            if ($published !== '') {
                if ($product->published != $published) {
                    continue;
                }
            }
            if ($producttype !== '') {
                if ($producttype == 'bundles') {
                    if (!$product->is_bundle) {
                        continue;
                    }
                } elseif ($producttype == 'courses') {
                    if ($product->is_bundle) {
                        continue;
                    }
                }
            }
            $p[] = $product;
        }

        $total = count($p);

        return $total;
    }
}
