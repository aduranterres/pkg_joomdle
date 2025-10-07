<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Site\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Wrapper model.
 */
class WrapperModel extends ListModel
{
    /**
    * Constructor.
    *
    * @param   array  $config  An optional associative array of configuration settings.
    *
    * @see        JController
    * @since      1.6
    */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   Elements order
     * @param   string  $direction  Order direction
     *
     * @return void
     *
     * @throws Exception
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // List state information.
        parent::populateState("a.id", "ASC");

        // Load the parameters.
        /** @var CMSApplication $app */
        $app  = Factory::getApplication();
        $params = $app->getParams();
        $this->setState('params', $params);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string  $id  A prefix for the store id.
     *
     * @return  string A store id.
     *
     * @since   2.0.0
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    public function getUrl($data)
    {
        $params = ComponentHelper::getParams('com_joomdle');

        switch ($data['mtype']) {
            case "course":
                $path = '/course/view.php?id=';
                $url = $params->get('MOODLE_URL') . $path . $data['id'];
                if ($data['topic']) {
                    $url .= '&topic=' . $data['topic'];
                }
                if ($data['section']) {
                    $url .= '#section-' . $data['section'];
                }
                break;
            case "coursecategory":
                $path = '/course/index.php?categoryid=';
                $url = $params->get('MOODLE_URL') . $path . $data['id'];
                break;
            case "news":
                $path = '/mod/forum/discuss.php?d=';
                $url = $params->get('MOODLE_URL') . $path . $data['id'];
                break;
            case "forum":
                $path = '/mod/forum/view.php?id=';
                $url = $params->get('MOODLE_URL') . $path . $data['id'];
                break;
            case "event":
                $path = "/calendar/view.php?view=day&time=" . $data['time'];
                $url = $params->get('MOODLE_URL') . $path;
                break;
            case "user":
                $path = '/user/view.php?id=';
                $url = $params->get('MOODLE_URL') . $path . $data['id'];
                break;
            case "edituser":
                $user = Factory::getApplication()->getIdentity();
                $id = ContentHelper::getUserId($user->username);
                $path = '/user/edit.php?&course_id=1&id=';
                $url = $params->get('MOODLE_URL') . $path . $id;
                break;
            case "resource":
                $path = '/mod/resource/view.php?id=';
                $url = $params->get('MOODLE_URL') . $path . $data['id'];
                break;
            case "quiz":
                $path = '/mod/quiz/view.php?id=';
                $url = $params->get('MOODLE_URL') . $path . $data['id'];
                break;
            case "page":
                $path = '/mod/page/view.php?id=';
                $url = $params->get('MOODLE_URL') . $path . $data['id'];
                break;
            case "assignment":
                $path = '/mod/assignment/view.php?id=';
                $url = $params->get('MOODLE_URL') . $path . $data['id'];
                break;
            case "folder":
                $path = '/mod/folder/view.php?id=';
                $url = $params->get('MOODLE_URL') . $path . $data['id'];
                break;
            case "messages":
                $path = '/message/index.php';
                $url = $params->get('MOODLE_URL') . $path . $data['id'];
                break;
            case "badge":
                $path = '/badges/badge.php?hash=';
                $url = $params->get('MOODLE_URL') . $path . $data['id'];
                break;
            case "moodle":
                $path = '/?a=1';
                $url = $params->get('MOODLE_URL') . $path;
                break;
            case "customurl":
                $path = $data['customurl'];
                $url = $params->get('MOODLE_URL') . $path;
                break;
            case "fullurl":
                $gotourl = $data['gotourl'];
                $url = $gotourl;
                break;
            default:
                if ($data['mtype']) {
                    $path = '/mod/' . $data['mtype'] . '/view.php?id=';
                    $url = $params->get('MOODLE_URL') . $path . $data['id'];
                    break;
                } else {
                    $path = '/?a=1';
                    $url = $params->get('MOODLE_URL') . $path;
                }
                break;
        }

        return $url;
    }
}
