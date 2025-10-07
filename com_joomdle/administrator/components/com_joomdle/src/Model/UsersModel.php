<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\Language\Text;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\MappingsHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ArrayHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Methods supporting a list of Users records.
 *
 * @since  2.0.0
 */
class UsersModel extends ListModel
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
                'name', 'a.name',
                'email', 'a.email',
                'username', 'a.username',
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

    public function getItems()
    {
        // Get a storage key.
        $store = $this->getStoreId();

        // Try to load the data from internal storage.
        if (isset($this->cache[$store])) {
            return $this->cache[$store];
        }

        try {
            // Load the list items and add the items to the internal cache.
            $this->cache[$store] = $this->getData();
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return $this->cache[$store];
    }

    public function getData()
    {
        $search = $this->getState('filter.search');

        $pagination = $this->getPagination();
        $limitstart = $pagination->limitstart;
        $limit = $pagination->limit;

        $listOrder  = $this->state->get('list.ordering');
        $listDirn   = $this->state->get('list.direction');
        $filter_order = $listOrder;

        $filter_order_Dir = $listDirn;

        $group_id = $this->state->get('filter.group_id');

        $filter_type = $this->getState('filter.state');
        switch ($filter_type) {
            case 'moodle':
                $users = $this->getMoodleUsers($limitstart, $limit, $filter_order, $filter_order_Dir, $search, $group_id);
                break;
            case 'joomla':
                $users = $this->getJoomlaUsers($limitstart, $limit, $filter_order, $filter_order_Dir, $search, $group_id);
                break;
            case 'joomdle':
                $users = $this->getJoomdleUsers($limitstart, $limit, $filter_order, $filter_order_Dir, $search, $group_id);
                break;
            case 'not_joomdle':
                $users = $this->getNotJoomdleUsers($limitstart, $limit, $filter_order, $filter_order_Dir, $search, $group_id);
                break;
            default:
                $users = $this->getAllUsers($limitstart, $limit, $filter_order, $filter_order_Dir, $search, $group_id);
                break;
        }

        return $users;
    }

    public function getTotal()
    {
        $search = $this->getState('filter.search');
        $filter_type = $this->state->get('filter.state');
        $group_id = $this->state->get('filter.group_id');
        switch ($filter_type) {
            case 'moodle':
                $total = ContentHelper::getMoodleUsersNumber($search);
                break;
            case 'joomla':
                $total =  $this->getJoomlaUsersNumber($search, $group_id);
                break;
            case 'joomdle':
                $total = count($this->getJoomdleUsers(0, 0, 'username', 'asc', $search, $group_id));
                break;
            case 'not_joomdle':
                $total = count($this->getNotJoomdleUsers(0, 0, 'username', 'asc', $search, $group_id));
                break;
            default:
                $total = count($this->getAllUsers(0, 0, 'username', 'asc', $search, $group_id));
                break;
        }
        return $total;
    }

    private function getAllUsers($limitstart, $limit, $order, $order_dir, $search, $group_id)
    {
        $db = $this->getDatabase();
        $query  = $db->createQuery();

        // Select the required fields from the table.
        $query->select(
            'a.*'
        );

        $query->from('#__users AS a');

        $searchEscaped = $db->Quote('%' . $db->escape($search, true) . '%', false);
        if ($search) {
            $query->where('a.username LIKE ' . $searchEscaped, 'OR')
                ->where('a.email LIKE ' . $searchEscaped, 'OR')
                ->where('a.name LIKE ' . $searchEscaped);
        }

        if ($group_id) {
            $query->join('LEFT', '#__user_usergroup_map as ugm ON ugm.user_id = a.id');
            $query->select('ugm.group_id');
            $query->where('ugm.group_id = ' . $db->quote($group_id));
        }

        $query->order($db->qn($db->escape($this->getState('list.ordering', 'a.name'))) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

        $db->setQuery($query);
        $jusers = $db->loadObjectList();

        $ju_by_usernames = array ();
        foreach ($jusers as $user) {
            $ju_by_usernames[$user->username] = $user;
        }

        // Remove the "a." part to send to Moodle
        $moodle_order = str_replace('a.', '', $order);

        $musers = ContentHelper::getMoodleUsers(0, 0, $moodle_order, $order_dir, $search);
        $mu_by_usernames = array ();
        if (is_array($musers)) {
            foreach ($musers as $user) {
                $mu_by_usernames[$user['username']] = $user;
            }
        }
        $rdo = array();
        foreach ($jusers as $user) {
            $item = get_object_vars($user);
            $item['name_lower'] = strtolower($item['name']);
            $item['username_lower'] = strtolower($item['username']);
            $item['email_lower'] = strtolower($item['email']);

            $item['j_account'] = 1;

            if (!array_key_exists($user->username, $mu_by_usernames)) {
                $item['m_account'] = 0;

                if (ContentHelper::isJoomlaAdmin($user->id)) {
                    $item['admin'] = 1;
                } else {
                    $item['admin'] = 0;
                }

                $item['auth'] = 'N/A';
            } else {
                // User in Joomla and Moodle
                $item['m_account'] = 1;

                if (!$mu_by_usernames[$user->username]['admin']) {
                    if (ContentHelper::isJoomlaAdmin($user->id)) {
                        $item['admin'] = 1;
                    } else {
                        $item['admin'] = 0;
                    }
                } else {
                    $item['admin'] = 1;
                }

                $item['auth'] = $mu_by_usernames[$user->username]['auth'];
            }

            $rdo[] = $item;
        }

        // If there is a Joomla group selected in filter, we don't show Moodle only users
        if (!$group_id) {
            // Get Moodle only users: those without a Joomla account
            $rdo2 =  array ();
            if (is_array($musers)) {
                foreach ($musers as $user) {
                    $item = array ();
                    $item = $user;
                    $item['m_account'] = 1;
                    if (!array_key_exists($user['username'], $ju_by_usernames)) {
                        // User not found in Joomla -> not a Joomdle user
                        $item['j_account'] = 0;
                        $item['m_account'] = 1;

                        // Note: We set a negative ID for Moodle only users
                        $item['id'] = - $user['id'];

                        $item['name_lower'] = strtolower($item['name']);
                        ;
                        $item['username_lower'] = strtolower($item['username']);
                        ;
                        $item['email_lower'] = strtolower($item['email']);
                        ;

                        $rdo2[] = $item;
                    }
                }
            }

            // Kludge for uppercases
            if ($order == 'name') {
                $order = 'name_lower';
            }
            if ($order == 'username') {
                $order = 'username_lower';
            }
            if ($order == 'email') {
                $order = 'email_lower';
            }

            $merged = array_merge($rdo, $rdo2);

            $all = ArrayHelper::multisort(
                $merged,
                strtolower($order_dir),
                $order,
                'id',
                'name',
                'username',
                'email',
                'm_account',
                'j_account',
                'auth',
                'admin'
            );
        } else {
            $all = $rdo;
        }

        if ($limit) {
            return array_slice($all, $limitstart, $limit);
        } else {
            return $all;
        }
    }

    private function getJoomlaUsers($limitstart, $limit, $order, $order_dir, $search = "", $group_id = null)
    {
        $db = $this->getDatabase();
        $query  = $db->createQuery();

        // Select the required fields from the table.
        $query->select(
            'a.*'
        );

        $query->from('#__users AS a');

        if ($group_id) {
            $query->join('LEFT', '#__user_usergroup_map as ugm ON ugm.user_id = a.id');
            $query->select('ugm.group_id');
            $query->where('ugm.group_id = ' . $db->quote($group_id));
        } else {
            $query->where('1 = 1'); // Seems needed to chain next andWhere call
        }

        $searchEscaped = $db->Quote('%' . $db->escape($search, true) . '%', false);
        if ($search) {
            $query->andWhere(array ('a.username LIKE ' . $searchEscaped, 'a.email LIKE ' . $searchEscaped, 'a.name LIKE ' . $searchEscaped), 'OR');
        }

        $searchEscaped = $db->Quote('%' . $db->escape($search, true) . '%', false);

        $query->order($db->qn($db->escape($this->getState('list.ordering', 'a.name'))) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

        if ($limit) {
            $query->setLimit($limit, $limitstart);
        }

        $db->setQuery($query);
        $jusers = $db->loadObjectList();

        $musers = ContentHelper::getMoodleUsers(0, 0, $order, $order_dir, $search);
        $mu_by_usernames = array ();
        foreach ($musers as $user) {
            $mu_by_usernames[$user['username']] = $user;
        }

        $rdo = array();
        $i = 0;
        foreach ($jusers as $user) {
            $item = array ();
            $item = get_object_vars($user);
            $item['j_account'] = 1;
            if (!array_key_exists($user->username, $mu_by_usernames)) {
                // User not in Moodle
                $item['m_account'] = 0;
                $item['auth'] = 'N/A';
            } else {
                // User in Moodle
                $item['m_account'] = 1;
                $item['auth'] = $mu_by_usernames[$user->username]['auth'];
            }

            if ((!$item['m_account']) || (!$mu_by_usernames[$user->username]['admin'])) {
                if (ContentHelper::isJoomlaAdmin($user->id)) {
                    $item['admin'] = 1;
                } else {
                    $item['admin'] = 0;
                }
            } else {
                $item['admin'] = 1;
            }

            $rdo[] = $item;
        }

        return ($rdo);
    }

    private function getJoomlaUsersNumber($search = "", $group_id = null)
    {
        $db = $this->getDatabase();
        $query  = $db->createQuery();

        // Select the required fields from the table.
        $query->select(
            'count(a.id) as n'
        );

        $query->from('#__users AS a');

        if ($group_id) {
            $query->join('LEFT', '#__user_usergroup_map as ugm ON ugm.user_id = a.id');
            $query->select('ugm.group_id');
            $query->where('ugm.group_id = ' . $db->quote($group_id));
        } else {
            $query->where('1 = 1'); // Seems needed to chain next andWhere call
        }

        $searchEscaped = $db->Quote('%' . $db->escape($search, true) . '%', false);
        if ($search) {
            $query->andWhere(array ('a.username LIKE ' . $searchEscaped, 'a.email LIKE ' . $searchEscaped, 'a.name LIKE ' . $searchEscaped), 'OR');
        }

        $db->setQuery($query);
        $n = $db->loadResult();

        return $n;
    }

    private function getMoodleUsers($limitstart = 0, $limit = 20, $order = "", $order_dir = "", $search = "", $group_id = 0)
    {
        if ($group_id) {
            // If a Joomla group is selected, we need to fetch all Moodle users and then remove those not in group
            $users = ContentHelper::getMoodleUsers(0, 0, $order, $order_dir, $search);
        } else {
            $users = ContentHelper::getMoodleUsers($limitstart, $limit, $order, $order_dir, $search);
        }

        if (!is_array($users)) {
            return array();
        }

        $u = array ();
        foreach ($users as $user) {
            /* We set ID negative for Moodle only users */
            $user['id'] = -$user['id'];
            $user['m_account'] = 1;

            $id = UserHelper::getUserId($user['username']);
            if ($id) {
                $user_obj = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($id);

                // If a group is selected, skip all users not in group
                if (($group_id) && (!in_array($group_id, $user_obj->groups))) {
                        continue;
                }

                if (!$user['admin']) {
                    // If not moodle admin, check if joomla admin
                    if (ContentHelper::isJoomlaAdmin($user_obj->id)) {
                        $user['admin'] = 1;
                    } else {
                        $user['admin'] = 0;
                    }
                }

                $user['j_account'] = 1;
                $user['id'] = $id;
            } else {
                // If Joomla group selected, and user has no Joomla account, don't show
                if ($group_id) {
                        continue;
                }
                $user['j_account'] = 0;
            }

            $u[] = $user;
        }

        return $u;
    }

    private function getJoomdleUsers($limitstart, $limit, $order, $order_dir, $search, $group_id)
    {
        $db = $this->getDatabase();
        $query  = $db->createQuery();

        // Select the required fields from the table.
        $query->select(
            'a.*'
        );

        $query->from('#__users AS a');

        if ($group_id) {
            $query->join('LEFT', '#__user_usergroup_map as ugm ON ugm.user_id = a.id');
            $query->select('ugm.group_id');
            $query->where('ugm.group_id = ' . $db->quote($group_id));
        } else {
            $query->where('1 = 1'); // Seems needed to chain next andWhere call
        }

        $searchEscaped = $db->Quote('%' . $db->escape($search, true) . '%', false);
        if ($search) {
            $query->andWhere(array ('a.username LIKE ' . $searchEscaped, 'a.email LIKE ' . $searchEscaped, 'a.name LIKE ' . $searchEscaped), 'OR');
        }

        $query->order($db->qn($db->escape($this->getState('list.ordering', 'a.name'))) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

        if ($limit) {
            $query->setLimit($limit, $limitstart);
        }

        $db->setQuery($query);
        $jusers = $db->loadObjectList();

        $musers = ContentHelper::getMoodleUsers(0, 0, $order, $order_dir, $search);
        $mu_by_usernames = array ();
        foreach ($musers as $user) {
            $mu_by_usernames[$user['username']] = $user;
        }

        $rdo = array();
        $i = 0;
        foreach ($jusers as $user) {
            $item = array ();
            $item = get_object_vars($user);
            $item['j_account'] = 1;

            // User not in Moodle -> not a joomdle user
            if (!array_key_exists($user->username, $mu_by_usernames)) {
                continue;
            }

            // User does not have joomdle auth method -> not a joomdle user
            if ($mu_by_usernames[$user->username]['auth'] != 'joomdle') {
                continue;
            }

            $item['m_account'] = 1;

            if (ContentHelper::isJoomlaAdmin($user->id)) {
                $item['admin'] = 1;
            } else {
                $item['admin'] = 0;
            }

            $item['auth'] = $mu_by_usernames[$user->username]['auth'];

            $rdo[] = $item;
        }

        return $rdo;
    }

    private function getNotJoomdleUsers($limitstart, $limit, $order, $order_dir, $search, $group_id)
    {
        $db = $this->getDatabase();
        $query  = $db->createQuery();

        // Select the required fields from the table.
        $query->select(
            'a.*'
        );

        $query->from('#__users AS a');

        if ($group_id) {
            $query->join('LEFT', '#__user_usergroup_map as ugm ON ugm.user_id = a.id');
            $query->select('ugm.group_id');
            $query->where('ugm.group_id = ' . $db->quote($group_id));
        } else {
            $query->where('1 = 1'); // Seems needed to chain next andWhere call
        }

        $searchEscaped = $db->Quote('%' . $db->escape($search, true) . '%', false);
        if ($search) {
            $query->andWhere(array ('a.username LIKE ' . $searchEscaped, 'a.email LIKE ' . $searchEscaped, 'a.name LIKE ' . $searchEscaped), 'OR');
        }

        $query->order($db->qn($db->escape($this->getState('list.ordering', 'a.name'))) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

        $db->setQuery($query);

        $jusers = $db->loadObjectList();

        $ju_by_usernames = array ();
        foreach ($jusers as $user) {
            $ju_by_usernames[$user->username] = $user;
        }

        $musers = ContentHelper::getMoodleUsers(0, 0, $order, $order_dir, $search);
        $mu_by_usernames = array ();
        foreach ($musers as $user) {
            $mu_by_usernames[$user['username']] = $user;
        }

        $rdo = array ();
        foreach ($jusers as $user) {
            $item = array ();
            $item = get_object_vars($user);
            $item['j_account'] = 1;
            if (!array_key_exists($user->username, $mu_by_usernames)) {
                // User not found in Moodle -> not a Joomdle user
                $item['m_account'] = 0;

                if (ContentHelper::isJoomlaAdmin($user->id)) {
                    $item['admin'] = 1;
                } else {
                    $item['admin'] = 0;
                }

                $item['auth'] = 'N/A';
            } else {
                // User in Joomla and Moodle
                $item['m_account'] = 1;

                if (!$mu_by_usernames[$user->username]['admin']) {
                    if (ContentHelper::isJoomlaAdmin($user->id)) {
                        $item['admin'] = 1;
                    } else {
                        $item['admin'] = 0;
                    }
                } else {
                    $item['admin'] = 1;
                }

                $item['auth'] = $mu_by_usernames[$user->username]['auth'];
            }

            if (($item['m_account'] == 1) && ($item['auth'] == 'joomdle')) {
                continue;
            }

            $rdo[] = $item;
        }

        // Get Moodle only users: those without a Joomla account
        $rdo2 =  array ();
        foreach ($musers as $user) {
            $item = array ();
            $item = $user;
            $item['m_account'] = 1;
            if (!array_key_exists($user['username'], $ju_by_usernames)) {
                // User not found in Joomla -> not a Joomdle user
                $item['j_account'] = 0;
                $item['m_account'] = 1;

                // We set a negative ID
                $item['id'] = -$user['id'];

                $rdo2[] = $item;
            }
        }

        $merged = array_merge($rdo, $rdo2);
        $all = ArrayHelper::multisort($merged, strtolower($order_dir), $order, 'id', 'name', 'username', 'email', 'm_account', 'j_account', 'auth', 'admin');
        if ($limit) {
            return array_slice($all, $limitstart, $limit);
        } else {
            return $all;
        }
    }

    public function addMoodleUsers($user_ids)
    {
        foreach ($user_ids as $id) {
            /* If user not already in Joomla, warn user and continue to next item */
            if ($id < 0) {
                $error = Text::_('COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_JOOMLA') . ": " . $id;
                Factory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($id);
            /* If user already in Moodle, warn user and continue to next item */
            if (ContentHelper::userExists($user->username)) {
                $error = Text::_('COM_JOOMDLE_USER_ALREADY_EXISTS_IN_MOODLE') . ": " . $user->username;
                Factory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            ContentHelper::createJoomdleUser($user->username);

            // Save user to trigger user events
            $user->save();
        }
    }

    public function addJoomlaUsers($user_ids)
    {
        foreach ($user_ids as $id) {
            /* If user already in Joomla, warn user and continue to next item */
            if ($id >= 0) {
                $error = Text::_('COM_JOOMDLE_USER_ALREADY_EXISTS_IN_JOOMLA') . ": " . $id;
                Factory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            /* Here we already know ID is from Moodle, as it is not from Joomla */
            $moodle_user = ContentHelper::userDetailsById(-$id); //We remove the minus
            if (!$moodle_user) {
                $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($id);
                $error = Text::_('COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_MOODLE') . ": " . $user->username;
                Factory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            $username = $moodle_user['username'];
            ContentHelper::createJoomlaUser($username);
        }
    }

    public function migrateuserstojoomdle($user_ids)
    {
        foreach ($user_ids as $id) {
            /* If user not already in Joomla, warn user and continue to next item */
            if ($id < 0) {
                $error = Text::_('COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_JOOMLA') . ": " . $id;
                Factory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($id);
            /* If user not already in Moodle, warn user and continue to next item */
            if (!ContentHelper::userExists($user->username)) {
                $error = Text::_('COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_MOODLE') . ": " . $user->username;
                Factory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            ContentHelper::migratetoJoomdle($user->username);
        }
    }

    public function syncMoodleProfiles($user_ids)
    {
        foreach ($user_ids as $id) {
            /* If user not already in Joomla, warn user and continue to next item */
            if ($id < 0) {
                $error = Text::_('COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_JOOMLA') . ": " . $id;
                Factory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($id);
            /* If user not already in Moodle, warn user and continue to next item */
            if (!ContentHelper::userExists($user->username)) {
                $error = Text::_('COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_MOODLE') . ": " . $user->username;
                Factory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            ContentHelper::createJoomdleUser($user->username);
        }
    }

    public function syncJoomlaProfiles($user_ids)
    {
        foreach ($user_ids as $id) {
            /* If user not already in Joomla, warn user and continue to next item */
            if ($id < 0) {
                $error = Text::_('COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_JOOMLA') . ": " . $id;
                Factory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($id);
            /* If user not already in Moodle, warn user and continue to next item */
            if (!ContentHelper::userExists($user->username)) {
                $error = Text::_('COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_MOODLE') . ": " . $user->username;
                Factory::getApplication()->enqueueMessage($error, 'error');

                continue;
            }

            MappingsHelper::syncUserToJoomla($user->username);
        }
    }
}
