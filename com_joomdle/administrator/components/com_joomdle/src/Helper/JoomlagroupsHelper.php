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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Usergroup;
use Joomla\CMS\User\UserHelper;
use Joomla\Database\ParameterType;

/**
 * Joomlagroups helper.
 *
 * @since  1.0.0
 */
class JoomlagroupsHelper
{
    public static function addGroup($course_id, $group_name, $type)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        // dont create if exists
        $group_id = JoomlagroupsHelper::getCourseGroupId($course_id, $type);
        if ($group_id) {
            $query = $db->createQuery()
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__usergroups'))
                ->where($db->quoteName('id') . ' = :group_id');

            $query->bind(':group_id', $group_id, ParameterType::INTEGER);

            $db->setQuery($query);
            $joomla_group_id = $db->loadResult();

            // If course group already exists, nothing to do
            if ($joomla_group_id) {
                return;
            }

            if (!$joomla_group_id) {
                //Group was deleted from Joomla
                // Delete entry in Joomdle table so that it can be created again
                $query = $db->createQuery()
                    ->delete($db->quoteName('#__joomdle_course_groups'))
                    ->where($db->quoteName('group_id') . ' = :group_id');

                $query->bind(':group_id', $group_id, ParameterType::INTEGER);

                $db->setQuery($query);
                $db->execute();
            }
        }

        $data['parent_id'] = JoomlagroupsHelper::getParentId($type);
        $data['title'] = $group_name;

        $row = new Usergroup($db);
        $row->save($data);

        $group_id = $row->id;

        $group = new \stdClass();
        $group->course_id = $course_id;
        $group->group_id = $group_id;
        $group->type = $type;

        $db->insertObject('#__joomdle_course_groups', $group);
    }

    public static function updateGroup($course_id, $group_name, $type)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $group_id = JoomlagroupsHelper::getCourseGroupId($course_id, $type);

        // If group is not created, do nothing
        if (!$group_id) {
            return;
        }

        $group = new \stdClass();
        $group->id = $group_id;
        $group->title = $group_name;

        $db->updateObject('#__usergroups', $group, 'id');
    }

    public static function getParentId($type)
    {
        $comp_params = ComponentHelper::getParams('com_joomdle');

        switch ($type) {
            case 'teachers':
                $id = $comp_params->get('joomlagroups_teachers');
                break;
            case 'students':
                $id = $comp_params->get('joomlagroups_students');
                break;
        }

        return $id;
    }

    public static function addCourseGroups($course_id, $course_name)
    {
        $group_name = $course_name . ' (' . Text::_('COM_JOOMDLE_TEACHERS') . ')';
        JoomlagroupsHelper::addGroup($course_id, $group_name, 'teachers');
        $group_name = $course_name . ' (' . Text::_('COM_JOOMDLE_STUDENTS') . ')';
        JoomlagroupsHelper::addGroup($course_id, $group_name, 'students');
    }

    public static function updateCourseGroups($course_id, $course_name)
    {
        $group_name = $course_name . ' (' . Text::_('COM_JOOMDLE_TEACHERS') . ')';
        JoomlagroupsHelper::updategroup($course_id, $group_name, 'teachers');
        $group_name = $course_name . ' (' . Text::_('COM_JOOMDLE_STUDENTS') . ')';
        JoomlagroupsHelper::updateGroup($course_id, $group_name, 'students');
    }

    public static function removeCourseGroups($course_id)
    {
        $group_id = JoomlagroupsHelper::getCourseGroupId($course_id, 'teachers');
        JoomlagroupsHelper::removeGroup($group_id);
        $group_id = JoomlagroupsHelper::getCourseGroupId($course_id, 'students');
        JoomlagroupsHelper::removeGroup($group_id);
    }

    public static function removeGroup($group_id)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $row = new Usergroup($db);
        $row->delete($group_id);

        // Remove from joomdle table
        $query = $db->createQuery()
            ->delete($db->quoteName('#__joomdle_course_groups'))
            ->where($db->quoteName('group_id') . ' = :group_id');

        $query->bind(':group_id', $group_id, ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();

        // Remove Joomla group
        $query = $db->createQuery()
            ->delete($db->quoteName('#__usergroups'))
            ->where($db->quoteName('id') . ' = :group_id');

        $query->bind(':group_id', $group_id, ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();
    }

    public static function addGroupMember($course_id, $username, $type)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $user_id = UserHelper::getUserId($username);
        if (!$user_id) {
            return;
        }

        // Add to general group if needed
        $parent_id = JoomlagroupsHelper::getParentId($type);
        $query = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__user_usergroup_map'))
            ->where($db->quoteName('group_id') . ' = :group_id')
            ->where($db->quoteName('user_id') . ' = :user_id');

        $query->bind(':group_id', $parent_id, ParameterType::INTEGER);
        $query->bind(':user_id', $user_id, ParameterType::INTEGER);

        $db->setQuery($query);
        $map = $db->loadAssocList();

        if (!count($map)) {
            $data = new \stdClass();
            $data->user_id = $user_id;
            $data->group_id = $parent_id;
            $db->insertObject('#__user_usergroup_map', $data);
        }

        // Add to course group
        $group_id = JoomlagroupsHelper::getCourseGroupId($course_id, $type);
        if (!$group_id) {
            return;
        }

        $query = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__user_usergroup_map'))
            ->where($db->quoteName('group_id') . ' = :group_id')
            ->where($db->quoteName('user_id') . ' = :user_id');

        $query->bind(':group_id', $group_id, ParameterType::INTEGER);
        $query->bind(':user_id', $user_id, ParameterType::INTEGER);

        $db->setQuery($query);
        $map = $db->loadAssocList();

        if (!count($map)) {
            $data = new \stdClass();
            $data->user_id = $user_id;
            $data->group_id = $group_id;
            $db->insertObject('#__user_usergroup_map', $data);
        }
    }

    public static function removeGroupMember($course_id, $username, $type)
    {
        $user_id = UserHelper::getUserId($username);
        if (!$user_id) {
            return;
        }

        $group_id = JoomlagroupsHelper::getCourseGroupId($course_id, $type);
        if (!$group_id) {
            return;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->createQuery()
            ->delete($db->quoteName('#__user_usergroup_map'))
            ->where($db->quoteName('group_id') . ' = :group_id')
            ->where($db->quoteName('user_id') . ' = :user_id');

        $query->bind(':group_id', $group_id, ParameterType::INTEGER);
        $query->bind(':user_id', $user_id, ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();
    }

    public static function getCourseGroupId($course_id, $type)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->createQuery()
            ->select($db->quoteName('group_id'))
            ->from($db->quoteName('#__joomdle_course_groups'))
            ->where($db->quoteName('course_id') . ' = :course_id')
            ->where($db->quoteName('type') . ' = :type');

        $query->bind(':course_id', $course_id, ParameterType::INTEGER);
        $query->bind(':type', $type, ParameterType::STRING);

        $db->setQuery($query);
        $group_id = $db->loadResult();

        return $group_id;
    }

    public static function syncGroupMembers($course_id)
    {
     //   $group_id = JoomlagroupsHelper::getCourseGroupId ($course_id, 'students');

        // Fetch students
        $students = ContentHelper::getCourseStudents($course_id, 0);
        foreach ($students as $student) {
            JoomlagroupsHelper::addGroupMember($course_id, $student['username'], 'students');
        }

     //   $group_id = JoomlagroupsHelper::getCourseGroupId ($course_id, 'teachers');

        // Fetch teachers
        $teachers = ContentHelper::getCourseTeachers($course_id);
        foreach ($teachers as $teacher) {
            JoomlagroupsHelper::addGroupMember($course_id, $teacher['username'], 'teachers');
        }
    }
}
