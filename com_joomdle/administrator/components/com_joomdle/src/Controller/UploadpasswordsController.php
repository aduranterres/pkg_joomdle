<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\Controller;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Language\Text;

/**
 * Mapping controller class.
 *
 * @since  2.0.0
 */
class UploadpasswordsController extends FormController
{
    protected $view_list = 'users';

    public function uploadpasswordfile()
    {
        if (!Factory::getApplication()->getSession()->checkToken()) {
            exit(Text::_('JINVALID_TOKEN'));
        }

        $this->setRedirect('index.php?option=com_joomdle&view=uploadpasswords&layout=edit');
    }

    public function upload()
    {
        $file = $this->input->files->get('jform', array ());

        if (!$this->getModel()->checkFields($file)) {
            $this->setMessage(Text::_('COM_JOOMDLE_USERNAME_AND_PASSSWORD_FIELDS_REQUIRED'), 'error');
            $this->setRedirect('index.php?option=com_joomdle&view=uploadpasswords&layout=edit');
            return;
        }

        if (!$this->getModel()->upload($file)) {
            $this->setMessage(Text::_('COM_JOOMDLE_PASSWORDS_UPLOAD_ERROR'), 'error');
            $this->setRedirect('index.php?option=com_joomdle&view=uploadpasswords&layout=edit');
            return;
        }

        $this->setMessage(Text::_('COM_JOOMDLE_PASSWORDS_UPLOADED'));
        $this->setRedirect('index.php?option=com_joomdle&view=users');
    }

    public function cancel($key = null)
    {
        $this->setRedirect('index.php?option=com_joomdle&view=users');

        return true;
    }
}
