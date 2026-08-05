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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\MappingsHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class CourseController extends BaseController
{
    public function enrol()
    {
        $this->checkToken('post');

        /** @var CMSApplication $app */
        $app = Factory::getApplication();

        $user = Factory::getApplication()->getIdentity();

        $course_id = (int) $this->input->post->get('course_id');

        $login_url = MappingsHelper::getLoginUrl($course_id);
        if (!$user->id) {
            $app->redirect($login_url);
        }

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
        $data = array();
        $data['moodle_page_type'] = 'course';
        $data['id'] = $course_id;

        $url = ContentHelper::getJumpURL($data);

        $app->redirect($url);
    }

    public function unenrol()
    {
        $this->checkToken('post');

        /** @var CMSApplication $app */
        $app = Factory::getApplication();

        $params = $app->getParams('com_joomdle');
        $show_unenrol_link = $params->get('show_unenrol_link');

        $user = Factory::getApplication()->getIdentity();

        $course_id = (int) $this->input->post->get('course_id');

        $login_url = MappingsHelper::getLoginUrl($course_id);
        if (!$user->id) {
            $app->redirect($login_url);
        }

        $username = $user->username;
        ContentHelper::unenrolUser($username, $course_id);

        // Redirect to caller URI when it belongs to this Joomla site.
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $url = $this->cleanLocalReferer($referer);
        $message = Text::_('COM_JOOMDLE_YOU_UNENROLED_FROM_COURSE');
        $this->setRedirect($url, $message);
    }

    private function cleanLocalReferer($referer)
    {
        $referer = trim((string) $referer);
        if ($referer === '' || str_starts_with($referer, '//')) {
            return Route::_('index.php?option=com_joomdle&view=mycourses', false);
        }

        $refererparts = parse_url($referer);
        $rootparts = parse_url(Uri::root());

        if (
            empty($refererparts['scheme']) || empty($refererparts['host']) ||
            empty($rootparts['scheme']) || empty($rootparts['host'])
        ) {
            return Route::_('index.php?option=com_joomdle&view=mycourses', false);
        }

        $refererscheme = strtolower($refererparts['scheme']);
        $rootscheme = strtolower($rootparts['scheme']);
        $refererhost = strtolower($refererparts['host']);
        $roothost = strtolower($rootparts['host']);
        $refererport = $refererparts['port'] ?? (($refererscheme === 'https') ? 443 : 80);
        $rootport = $rootparts['port'] ?? (($rootscheme === 'https') ? 443 : 80);

        if ($refererscheme === $rootscheme && $refererhost === $roothost && $refererport === $rootport) {
            return $referer;
        }

        return Route::_('index.php?option=com_joomdle&view=mycourses', false);
    }
}
