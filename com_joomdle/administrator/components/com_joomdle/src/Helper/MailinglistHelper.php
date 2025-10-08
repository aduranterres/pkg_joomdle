<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\Helper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\UserHelper;
use Joomla\Database\ParameterType;
use Joomla\Event\Event;

/**
 * Mailinglist helper.
 *
 * @since  1.0.0
 */
class MailinglistHelper
{
    public static function getListCourses()
    {
        $cursos = ContentHelper::getCourseList(0);

        $cs = array ();
        if (!is_array($cursos)) {
            return $cs;
        }

        foreach ($cursos as $curso) {
            $c = new \stdClass();
            $c->id = $curso['remoteid'];
            $c->fullname = $curso['fullname'];
            $c->published_students = MailinglistHelper::courseListExists($curso['remoteid'], 'course_students');
            $c->published_teachers = MailinglistHelper::courseListExists($curso['remoteid'], 'course_teachers');

            $cs[] = $c;
        }

        return $cs;
    }

    public static function getCourseListId($course_id, $type)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->createQuery()
            ->select($db->quoteName('list_id'))
            ->from($db->quoteName('#__joomdle_mailinglists'))
            ->where($db->quoteName('course_id') . ' = :course_id')
            ->where($db->quoteName('type') . ' = :type');

        // Bind parameters
        $query->bind(':course_id', $course_id, ParameterType::STRING);
        $query->bind(':type', $type, ParameterType::STRING);

        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;
    }

    public static function courseListExists($course_id, $type)
    {
        $id = MailinglistHelper::getCourseListId($course_id, $type);

        if ($id) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getGeneralListId($type)
    {
        $course_id = 0;
        return MailinglistHelper::getCourseListId($course_id, $type);
    }

    public static function generalListExists($type)
    {
        $id = MailinglistHelper::getGeneralListId($type);

        return $id ? true : false;
        /*
        if ($id)
            return 1;
        else
            return 0;
        */
    }

    public static function getTypeStr($type)
    {
        switch ($type) {
            case 'course_students':
                $str = Text::_('COM_JOOMDLE_STUDENTS');
                break;
            case 'course_teachers':
                $str = Text::_('COM_JOOMDLE_TEACHERS');
                break;
        }

        $str = ' (' . $str . ')';

        return $str;
    }

    public static function saveMailingLists($cid)
    {
        foreach ($cid as $id) {
            MailinglistHelper::saveCourseMailingList($id);
        }
    }

    public static function saveCourseMailingList($course_id, $type = 'course_students')
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $type_str = MailinglistHelper::getTypeStr($type);
        $course_info = ContentHelper::getCourseInfo($course_id);

        // Add to mailing list component
        $data = array ();
        $data['name'] = $course_info['fullname'] . $type_str;
        $data['description'] = $course_info['summary'];

        PluginHelper::importPlugin('joomdlemailinglist');
        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new Event('onJoomdleSaveList', ['data' => $data]);
        $dispatcher->dispatch('onJoomdleSaveList', $event);
        $list_ids = $event->getArgument('results') ?? null;

        foreach ($list_ids as $list_id) {
            if ($list_id !== false) { // We check for FALSE, as returned by non configured plugins
                break;
            }
        }

        if (!$list_id) {
            return false;
        }

        // Add to joomdle table
        $mlist = new \stdClass();
        $mlist->course_id = $course_id;
        $mlist->list_id = $list_id;
        $mlist->type = $type;

        $db->insertObject('#__joomdle_mailinglists', $mlist);

        // Add all course members to list
        MailinglistHelper::addListMembers($course_id, $type);
    }

    public static function saveGeneralMailingList($type = 'course_students')
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $type_str = MailinglistHelper::getTypeStr($type);

        $data = array ();
        $data['name'] = $type_str;
        $data['description'] = '';

        PluginHelper::importPlugin('joomdlemailinglist');
        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new Event('onJoomdleSaveList', ['data' => $data]);
        $dispatcher->dispatch('onJoomdleSaveList', $event);
        $list_ids = $event->getArgument('results') ?? null;

        foreach ($list_ids as $list_id) {
            if ($list_id !== false) { // We check for FALSE, as returned by non configured plugins
                break;
            }
        }

        if (!$list_id) {
            return false;
        }

        // Add to joomdle table
        $mlist = new \stdClass();
        $mlist->course_id = 0;
        $mlist->list_id = $list_id;
        $mlist->type = $type;

        $db->insertObject('#__joomdle_mailinglists', $mlist);

        // Add all course members to list
        MailinglistHelper::addGeneralListMembers($type);
    }

    public static function addSub($list_id, $user_id)
    {
        $data = array ();
        $data['list_id'] = $list_id;
        $data['user_id'] = $user_id;

        PluginHelper::importPlugin('joomdlemailinglist');
        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new Event('onJoomdleAddListSub', ['data' => $data]);
        $dispatcher->dispatch('onJoomdleAddListSub', $event);
    }

    public static function removeSub($list_id, $user_id)
    {
        $data = array ();
        $data['list_id'] = $list_id;
        $data['user_id'] = $user_id;

        PluginHelper::importPlugin('joomdlemailinglist');
        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new Event('onJoomdleRemoveListSub', ['data' => $data]);
        $dispatcher->dispatch('onJoomdleRemoveListSub', $event);
    }

    public static function addListMember($username, $course_id, $type)
    {
        $list_id = MailinglistHelper::getCourseListId($course_id, $type);
        $user_id = UserHelper::getUserId($username);

        if ($list_id) {
            MailinglistHelper::addSub($list_id, $user_id);
        }
        // Add to general list if necessary
        $list_id = MailinglistHelper::getGeneralListId($type);
        if ($list_id) {
            MailinglistHelper::addSub($list_id, $user_id);
        }
    }

    public static function removeListMember($username, $course_id, $type)
    {
        $list_id = MailinglistHelper::getCourseListId($course_id, $type);
        $user_id = UserHelper::getUserId($username);

        // Remove from general list if necessary
        $remove = false;
        $glist_id = MailinglistHelper::getGeneralListId($type);
        if ($glist_id) {
            //Only remove if user has no more course enrolments of this type
            switch ($type) {
                case 'course_students':
                    $my_courses = ContentHelper::getMyCourses($username);
                    if (count($my_courses) == 0) {
                        $remove = true;
                    }
                    break;
                case 'course_teachers':
                    $my_courses = ContentHelper::getTeacherCourses($username);
                    if (count($my_courses) == 0) {
                        $remove = true;
                    }
                    break;
            }
        }

        MailinglistHelper::removeSub($list_id, $user_id);
        if ($remove) {
            MailinglistHelper::removeSub($glist_id, $user_id);
        }
    }

    public static function addListMembers($course_id, $type)
    {
        $list_id = MailinglistHelper::getCourseListId($course_id, $type);

        switch ($type) {
            case 'course_students':
                $students = ContentHelper::getCourseStudents($course_id, 0);
                foreach ($students as $student) {
                    $user_id = UserHelper::getUserId($student['username']);
                    MailinglistHelper::addSub($list_id, $user_id);
                }
                break;
            case 'course_teachers':
                $teachers = ContentHelper::getCourseTeachers($course_id);
                foreach ($teachers as $teacher) {
                    $user_id = UserHelper::getUserId($teacher['username']);
                    MailinglistHelper::addSub($list_id, $user_id);
                }
                break;
            default:
                break;
        }
    }

    public static function addGeneralListMembers($type)
    {
        $list_id = MailinglistHelper::getGeneralListId($type);

        switch ($type) {
            case 'course_students':
                $courses = ContentHelper::getCourseList();
                foreach ($courses as $course) {
                    $teachers = array ();
                    $course_id = $course['remoteid'];
                    $students = ContentHelper::getCourseStudents($course_id, 0);
                    foreach ($students as $student) {
                        $user_id = UserHelper::getUserId($student['username']);
                        MailinglistHelper::addSub($list_id, $user_id);
                    }
                }
                break;
            case 'course_teachers':
                $courses = ContentHelper::getCourseList();
                foreach ($courses as $course) {
                    $teachers = array ();
                    $course_id = $course['remoteid'];
                    $teachers = ContentHelper::getCourseTeachers($course_id);
                    foreach ($teachers as $teacher) {
                        $user_id = UserHelper::getUserId($teacher['username']);
                        MailinglistHelper::addSub($list_id, $user_id);
                    }
                }
                break;
            default:
                break;
        }
    }

    public static function deleteMailingLists($cid, $type = 'course_students')
    {
        foreach ($cid as $id) {
            MailinglistHelper::deletCourseMailingList($id, $type);
        }
    }

    public static function deletCourseMailingList($course_id, $type)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $list_id = MailinglistHelper::getCourseListId($course_id, $type);

        //Delete from Joomdle table
        $query = $db->createQuery()
            ->delete($db->quoteName('#__joomdle_mailinglists'))
            ->where($db->quoteName('course_id') . ' = :course_id')
            ->where($db->quoteName('type') . ' = :type');

        $query->bind(':course_id', $course_id, ParameterType::STRING);
        $query->bind(':type', $type, ParameterType::STRING);

        $db->setQuery($query);
        $db->execute();

        //Delete from mailing list component
        PluginHelper::importPlugin('joomdlemailinglist');
        $dispatcher = Factory::getApplication()->getDispatcher();
        $event = new Event('onJoomdleDeleteList', ['data' => array('list_id' => $list_id)]);
        $dispatcher->dispatch('onJoomdleDeleteList', $event);
    }

    public static function saveListsStudents($cid)
    {
        foreach ($cid as $id) {
            if ($id) {
                MailinglistHelper::saveCourseMailingList($id, 'course_students');
            } else {
                MailinglistHelper::saveGeneralMailingList('course_students');
            }
        }
    }

    public static function saveListsTeachers($cid)
    {
        foreach ($cid as $id) {
            if ($id) {
                MailinglistHelper::saveCourseMailingList($id, 'course_teachers');
            } else {
                MailinglistHelper::saveGeneralMailingList('course_teachers');
            }
        }
    }

    public static function getGeneralLists()
    {
        $c = new \stdClass();
        $c->id = 0;
        $c->fullname = Text::_('COM_JOOMDLE_GENERAL');
        $c->published_students = MailinglistHelper::generalListExists('course_students');
        $c->published_teachers = MailinglistHelper::generalListExists('course_teachers');

        return $c;
    }
}
