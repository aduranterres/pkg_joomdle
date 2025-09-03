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

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\MappingsHelper;


class CourseController extends BaseController
{
    public function enrol()
    {
        $app = Factory::getApplication();

        $user = Factory::getApplication()->getIdentity();

        $course_id = (int) $this->input->get('course_id');

        $login_url = MappingsHelper::getLoginUrl($course_id);
        if (!$user->id) {
            $app->redirect($login_url);
        }

        $params = $app->getParams();

        /* Check that self enrolments are OK in course */
        $enrol_methods = ContentHelper::courseEnrolMethods($course_id);
        $self_ok = false;
        foreach ($enrol_methods as $method) {
            if ($method['enrol'] == 'self') {
                $self_ok = true;
                break;
            }
        }

        if (!$self_ok) {
            $url = Route::_("index.php?option=com_joomdle&view=detail&course_id=$course_id");
            $message = Text::_('COM_JOOMDLE_SELF_ENROLMENT_NOT_PERMITTED');
            $this->setRedirect($url, $message);
            return;
        }

        ContentHelper::enrolUser($user->username, $course_id);

        // Redirect to course
        $data = array ();
        $data['moodle_page_type'] = 'course';
        $data['id'] = $course_id;

        $url = ContentHelper::getJumpURL($data);

        $app->redirect($url);
    }

    public function unenrol()
    {
        $app = Factory::getApplication();

        $user = Factory::getApplication()->getIdentity();

        $course_id = (int) $this->input->get('course_id');

        $login_url = MappingsHelper::getLoginUrl($course_id);
        if (!$user->id) {
            $app->redirect($login_url);
        }

        $params = $app->getParams();

        $username = $user->username;
        ContentHelper::unenrolUser($username, $course_id);

        // Redirect to caller URI
        $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
        $message = Text::_('COM_JOOMDLE_YOU_UNENROLED_FROM_COURSE');
        $this->setRedirect($url, $message);
    }
}
