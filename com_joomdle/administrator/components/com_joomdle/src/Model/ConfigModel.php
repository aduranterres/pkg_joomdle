<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\Model;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\User\UserHelper;
use Joomla\Component\Config\Administrator\Model\ComponentModel;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Config model.
 *
 * @since  2.0.0
 */
class ConfigModel extends AdminModel
{
    /**
     * @var    string  The prefix to use with controller messages.
     *
     * @since  2.0.0
     */
    protected $text_prefix = 'COM_JOOMDLE';

    /**
     * @var    string  Alias to manage history control
     *
     * @since  2.0.0
     */
    public $typeAlias = 'com_joomdle.config';

    /**
     * @var    null  Item data
     *
     * @since  2.0.0
     */
    protected $item = null;

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  Table    A database object
     *
     * @since   2.0.0
     */
    public function getTable($type = 'Config', $prefix = 'Administrator', $config = array())
    {
        return parent::getTable($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      An optional array of data for the form to interogate.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  \JForm|boolean  A \JForm object on success, false on failure
     *
     * @since   2.0.0
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Initialise variables.
        $app = Factory::getApplication();

        // Get the form.
        $form = $this->loadForm(
            'com_joomdle.config',
            'config',
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   2.0.0
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_joomdle.edit.config.data', array());

        if (empty($data)) {
            $result = ComponentHelper::getComponent('com_joomdle');
            return $result->getParams()->toArray();
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     *
     * @since   2.0.0
     */
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {
        }

        return $item;
    }

    public function save($data)
    {
        //Get joomdle extension id
        $db = $this->getDatabase();
        $query = 'SELECT extension_id ' .
                ' FROM #__extensions' .
                " WHERE name = 'com_joomdle'";
        $db->setQuery($query);
        $extension_id = $db->loadResult();

        $option = 'com_joomdle';

        // Generate auth token if needed
        if ($data['joomla_auth_token'] == '') {
            $token = UserHelper::genRandomPassword(32);
            $token = preg_replace('/[\x00-\x1F\x7F]/', '', $token);

            $data['joomla_auth_token'] = $token;
        }

//        $data['license_key'] = trim($data['license_key']);
//        $license_key = $data['license_key'];

        // Token cannot have spaces
        $data['auth_token'] = trim($data['auth_token']);

        $data = array(
            'params'    => $data,
            'id'        => $extension_id,
            'option'    => $option
        );

        // Save config using the com_config component.
        $model = Factory::getApplication()->bootComponent('com_config')
            ->getMVCFactory()
            ->createModel('Component', 'Administrator');
        $return = $model->save($data);

        return $return;
    }

    public function regenerateJoomlaToken()
    {
        $model = new ComponentModel();

        //Get joomdle extension id
        $db = $this->getDatabase();

        $option = 'com_joomdle';

        $query = $db->createQuery()
            ->select($db->quoteName('extension_id'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('name') . ' = :name');
        $query->bind(':name', $option, ParameterType::STRING);
        $db->setQuery($query);
        $extension_id = $db->loadResult();

        $query = $db->createQuery()
            ->select($db->quoteName('params'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('name') . ' = :name');
        $query->bind(':name', $option, ParameterType::STRING);
        $db->setQuery($query);
        $params = $db->loadResult();

        $data_o = json_decode($params);
        $data = (array) $data_o;

        // Generate auth token
        $token = UserHelper::genRandomPassword(32);
        $token = preg_replace('/[\x00-\x1F\x7F]/', '', $token);
        $data['joomla_auth_token'] = $token;

        $data   = array(
                    'params'    => $data,
                    'id'        => $extension_id,
                    'option'    => $option
                    );
        $return = $model->save($data);

        return $return;
    }
}
