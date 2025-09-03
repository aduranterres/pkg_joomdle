<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Site\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Joomdle model.
 *
 * @since  2.0.0
 */
class JoomdleModel extends ListModel
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

        // Load the parameters.
        $app  = Factory::getApplication();
        $params = $app->getParams();
        $this->setState('params', $params);
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

    public function getItems()
    {
        $params = ComponentHelper::getParams('com_joomdle');
        $enrollable_only = $params->get('enrollable_only');
        $show_buttons = $params->get('show_buttons');
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

        $user = Factory::getApplication()->getIdentity();
        $username = $user->username;
        if (($show_buttons) && ($username)) {
            $items = ContentHelper::getCourseList((int) $enrollable_only, $order, 0, $username);
        } else {
            $items = ContentHelper::getCourseList((int) $enrollable_only, $order);
        }

        return $items;
    }
}
