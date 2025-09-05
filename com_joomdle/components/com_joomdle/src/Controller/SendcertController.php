<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Site\Controller;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\MappingsHelper;


class SendcertController extends FormController
{
    public function sendcertificate ()
    {
        $app = Factory::getApplication();
        $params = $app->getParams();
        $moodle_url = $params->get('MOODLE_URL');

        $data = $this->input->get('jform', [], 'array');

        $sent = $this->getModel()->sendCertificate($data);

        if ( $sent !== true ) {
            $error = Text::_( 'COM_JOOMDLE_EMAIL_NOT_SENT' );
            $app->enqueueMessage($error, 'notice');
        } else {
        ?>
        <div style="padding: 10px;">
            <div style="text-align:right">
                <a href="javascript: void window.close()">
                    <?php echo Text::_('COM_JOOMDLE_CLOSE_WINDOW'); ?> <?php echo HTMLHelper::_('image','mailto/close-x.png', NULL, NULL, true); ?></a>
            </div>

            <h2>
                <?php echo Text::_('COM_JOOMDLE_EMAIL_SENT'); ?>
            </h2>
        </div>
        <?php
        }
    }
}
