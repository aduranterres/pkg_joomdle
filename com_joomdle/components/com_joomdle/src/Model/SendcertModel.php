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

            $data->cert_id = Factory::getApplication()->getInput()->get('cert_id', '', 'string');
            $data->cert_type = Factory::getApplication()->getInput()->get('type', '', 'string');
        }

        return $data;
    }

    public function sendCertificate($data)
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $params = $app->getParams();
        $moodle_url = $params->get('MOODLE_URL');

        $user = Factory::getApplication()->getIdentity();
        $username = $user->username;

        $subject_default = Text::sprintf('COM_JOOMDLE_CERTIFICATE_EMAIL_SUBJECT', $user->name);
        $subject  = $data['subject'];
        if (!$subject) {
            $subject = $subject_default;
        }

        $mailer = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer(); ;

        $config = Factory::getApplication()->getConfig();  
        $sender = array(
            $data['from'],
            $data['sender']
        );

        $mailer->setSender($sender);
        $mailer->addRecipient($data['to']);

        $body   = Text::sprintf('COM_JOOMDLE_CERTIFICATE_EMAIL_BODY', $user->name);
        $mailer->setSubject($subject);
        $mailer->setBody($body);

        // $session = Factory::getSession();
        $session = Factory::getApplication()->getSession();
        $token = md5($session->getId());

        $cert_id = $data['cert_id'];
        switch ($data['cert_type']) {
            case "simple":
                $url = $moodle_url . '/auth/joomdle/simplecertificate_view.php?id=' . $cert_id . '&certificate=1&action=review&username=' . $username . '&token=' . $token;
                break;
            case "custom":
                $url = $moodle_url . '/auth/joomdle/customcert_view.php?id=' . $cert_id . '&action=download&username=' . $username . '&token=' . $token
                    . '&downloadcert=1';
                break;
            case "normal":
            default:
                $url = $moodle_url . '/auth/joomdle/certificate_view.php?id=' . $cert_id . '&certificate=1&action=review&username=' . $username . '&token=' . $token;
                break;
        }

        $pdf = ContentHelper::getFile($url);
        $tmp_path = $config->get('tmp_path');
        $filename = 'certificate-' . $cert_id . '-' . $user->name . '.pdf';
        file_put_contents($tmp_path . '/' . $filename, $pdf);
        $mailer->addAttachment($tmp_path . '/' . $filename);

        $sent = $mailer->Send();
        unlink($tmp_path . '/' . $filename);

        return $sent;
    }
}
