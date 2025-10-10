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

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Language\Text;

/**
 * Config controller class.
 *
 * @since  2.0.0
 */
class ConfigController extends FormController
{
    protected $view_list = 'default';

    public function regeneratejoomlatoken()
    {
        $this->getModel()->regenerateJoomlaToken();
        $this->setMessage(Text::_('COM_JOOMDLE_NEW_TOKEN_GENERATED'));
        $this->setRedirect('index.php?option=com_joomdle&view=config');
        return true;
    }

    public function save($key = null, $urlVar = null)
    {
        $data = $this->input->post->get('jform', [], 'array');

        $saved = $this->getModel()->save($data);

        $this->setMessage(Text::_("Configuration saved."));
        $this->setRedirect("index.php?option=com_joomdle&view=config");

        return $saved;
    }

    public function cancel($key = null)
    {
        $this->setRedirect('index.php?option=com_joomdle&view=default');

        return true;
    }
}
