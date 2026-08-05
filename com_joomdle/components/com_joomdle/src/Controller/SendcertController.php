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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Log\Log;

class SendcertController extends FormController
{
    public function sendcertificate()
    {
        $this->checkToken('post');

        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $user = $app->getIdentity();

        if ($user->guest) {
            throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $model = $this->getModel();
        $form = $model->getForm();

        if (!$form) {
            throw new \RuntimeException($model->getError(), 500);
        }

        $request_data = $this->input->post->get('jform', [], 'array');
        $data = $model->validate($form, $request_data);

        if ($data === false) {
            $app->setUserState('com_joomdle.edit.sendcert.data', $form->filter($request_data));
            $redirect_url = 'index.php?option=com_joomdle&view=sendcert&layout=edit&tmpl=component';
            $item_id = $this->input->getInt('Itemid');

            if ($item_id) {
                $redirect_url .= '&Itemid=' . $item_id;
            }

            $this->setRedirect(
                Route::_($redirect_url, false)
            );

            return false;
        }

        try {
            $sent = $model->sendCertificate($data);
        } catch (\RuntimeException $exception) {
            if ($exception->getCode() === 403) {
                throw $exception;
            }

            Log::add($exception->getMessage(), Log::ERROR, 'com_joomdle.sendcert');
            $sent = false;
        } catch (\Throwable $exception) {
            Log::add($exception->getMessage(), Log::ERROR, 'com_joomdle.sendcert');
            $sent = false;
        }

        if ($sent !== true) {
            $error = Text::_('COM_JOOMDLE_EMAIL_NOT_SENT');
            $app->enqueueMessage($error, 'notice');
        } else {
            $app->setUserState('com_joomdle.edit.sendcert.data', null);
            ?>
            <div style="padding: 10px;">
                <div style="text-align:right">
                    <a href="javascript: void window.close()">
                        <?php echo Text::_('COM_JOOMDLE_CLOSE_WINDOW'); ?>
                        <?php echo HTMLHelper::_('image', 'mailto/close-x.png', null, null, true); ?>
                    </a>
                </div>

                <h2>
                    <?php echo Text::_('COM_JOOMDLE_EMAIL_SENT'); ?>
                </h2>
            </div>
            <?php
        }
    }
}
