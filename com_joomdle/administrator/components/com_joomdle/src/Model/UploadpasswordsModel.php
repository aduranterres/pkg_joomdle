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
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Mapping model.
 *
 * @since  2.0.0
 */
class UploadpasswordsModel extends AdminModel
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
    public $typeAlias = 'com_joomdle.uploadpasswords';

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
    public function getTable($type = 'Users', $prefix = '', $config = array())
    {
        return false;
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
        // Get the form.
        $form = $this->loadForm(
            'com_joomdle.uploadpasswords',
            'uploadpasswords',
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
        $data = Factory::getApplication()->getUserState('com_joomdle.edit.uploadpasswords.data', array());

        return $data;
    }

    public function checkFields($file)
    {
        if ($file['passwords_file']['tmp_name']) {
            $tmp_name = $file['passwords_file']['tmp_name'];
            if (($handle = fopen($tmp_name, "r")) !== false) {
                // First line should have the password heading in a column
                $data = fgetcsv($handle, 0, ",");

                if ((!$data) || (!is_array($data))) {
                    return false;
                }

                if ((in_array('password', $data)) && (in_array('username', $data))) {
                    return true;
                }
            }
        }

        return false;
    }

    public function upload($file)
    {
        if ($file['passwords_file']['tmp_name']) {
            $tmp_name = $file['passwords_file']['tmp_name'];
            if (($handle = fopen($tmp_name, "r")) !== false) {
                // First line should have the password heading in a column
                $data = fgetcsv($handle, 0, ",");

                if ((!$data) || (!is_array($data))) {
                    return false;
                }

                if ((!in_array('password', $data)) || (!in_array('username', $data))) {
                    $this->setError(Text::_('COM_JOOMDLE_USERNAME_AND_PASSSWORD_FIELDS_REQUIRED'));
                    return false;
                }

                $password_index = 0;
                while (true) {
                    if ($data[$password_index] == 'password') {
                        break;
                    }

                    $password_index++;
                }

                $username_index = 0;
                while (true) {
                    if ($data[$username_index] == 'username') {
                        break;
                    }

                    $username_index++;
                }

                while (($data = fgetcsv($handle, 0, ",")) !== false) {
                    $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserByUsername($data[$username_index]);
                    $password = UserHelper::hashPassword($data[$password_index]);
                    $user->password = $password;
                    $user->save();
                }
            }
        }

        return true;
    }
}
