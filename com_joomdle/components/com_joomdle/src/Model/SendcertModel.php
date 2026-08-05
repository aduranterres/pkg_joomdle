<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Site\Model;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Language\Text;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Log\Log;
use Joomla\Filesystem\File;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Sendcert model.
 *
 * @since  2.0.0
 */
class SendcertModel extends AdminModel
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
    public $typeAlias = 'com_joomdle.sendcert';

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
    // FIXME esto no se
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
            'com_joomdle.sendcert',
            'sendcert',
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

    public function getItem($pk = null)
    {
        $item = new \stdClass();

        $user = Factory::getApplication()->getIdentity();

        if ($user) {
            $item->sender = $user->name;
            $item->from = $user->email;
        }

        return $item;
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
        $data = Factory::getApplication()->getUserState('com_joomdle.edit.sendcert.data', array());

        if (empty($data)) {
            if ($this->item === null) {
                $this->item = $this->getItem();
            }

            $data = $this->item;

            $data->cert_id = Factory::getApplication()->getInput()->getInt('cert_id');
            $data->cert_type = Factory::getApplication()->getInput()->getCmd('type');
            $data->module_id = Factory::getApplication()->getInput()->getInt('module_id');
        }

        return $data;
    }

    public function assertCanSendCertificate($cert_type, $cert_id, $module_id = 0)
    {
        $app = Factory::getApplication();
        $user = $app->getIdentity();

        if ($user->guest) {
            throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $params = $app->getParams();
        $allowed_types = ['normal', 'simple', 'custom', 'coursecertificate'];
        $configured_type = $params->get('certificate_type', 'custom');

        if (!in_array($cert_type, $allowed_types, true) || $cert_type !== $configured_type) {
            throw new \RuntimeException(Text::_('COM_JOOMDLE_SEND_CERTIFICATE_NOT_ALLOWED'), 403);
        }

        if (!is_int($cert_id) || $cert_id < 1) {
            throw new \RuntimeException(Text::_('COM_JOOMDLE_SEND_CERTIFICATE_NOT_ALLOWED'), 403);
        }

        return true;
    }

    public function sendCertificate($data)
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $user = $app->getIdentity();

        $this->assertCanSendCertificate($data['cert_type'], $data['cert_id'], $data['module_id'] ?? 0);

        if (strpbrk($data['sender'], "\r\n") !== false || strpbrk($data['subject'], "\r\n") !== false) {
            throw new \RuntimeException(Text::_('COM_JOOMDLE_INVALID_CERTIFICATE_DATA'));
        }

        $subject_default = Text::sprintf('COM_JOOMDLE_CERTIFICATE_EMAIL_SUBJECT', $user->name);
        $subject  = $data['subject'];
        if (!$subject) {
            $subject = $subject_default;
        }

        $mailer = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();

        $config = $app->getConfig();
        $sender = array(
            $data['from'],
            $data['sender']
        );

        $mailer->setSender($sender);
        $mailer->addRecipient($data['to']);

        $body   = Text::sprintf('COM_JOOMDLE_CERTIFICATE_EMAIL_BODY', $user->name);
        $mailer->setSubject($subject);
        $mailer->setBody($body);

        $cert_id = $data['cert_id'];
        $pdf_data = ContentHelper::getCertificate($user->username, $data['cert_type'], $cert_id);

        if (!is_array($pdf_data) || !isset($pdf_data['content']) || !is_string($pdf_data['content'])) {
            throw new \RuntimeException(Text::_('COM_JOOMDLE_INVALID_CERTIFICATE_DATA'));
        }

        $pdf = base64_decode($pdf_data['content'], true);

        if ($pdf === false || $pdf === '' || strncmp($pdf, '%PDF-', 5) !== 0) {
            throw new \RuntimeException(Text::_('COM_JOOMDLE_INVALID_CERTIFICATE_DATA'));
        }

        $tmp_path = $config->get('tmp_path');

        if (!is_string($tmp_path) || !is_dir($tmp_path) || !is_writable($tmp_path)) {
            throw new \RuntimeException(Text::_('COM_JOOMDLE_INVALID_CERTIFICATE_DATA'));
        }

        $tmp_file = tempnam($tmp_path, 'joomdle_cert_');

        if ($tmp_file === false) {
            throw new \RuntimeException(Text::_('COM_JOOMDLE_INVALID_CERTIFICATE_DATA'));
        }

        try {
            $written_bytes = file_put_contents($tmp_file, $pdf, LOCK_EX);

            if ($written_bytes === false || $written_bytes !== strlen($pdf)) {
                throw new \RuntimeException(Text::_('COM_JOOMDLE_INVALID_CERTIFICATE_DATA'));
            }

            $filename = 'certificate-' . $cert_id . '.pdf';
            $attached = $mailer->addAttachment($tmp_file, $filename, 'base64', 'application/pdf');

            if ($attached === false) {
                throw new \RuntimeException(Text::_('COM_JOOMDLE_INVALID_CERTIFICATE_DATA'));
            }

            return $mailer->send();
        } finally {
            if (is_file($tmp_file)) {
                try {
                    File::delete($tmp_file);
                } catch (\Throwable $exception) {
                    Log::add($exception->getMessage(), Log::WARNING, 'com_joomdle.sendcert');
                }
            }
        }
    }
}
