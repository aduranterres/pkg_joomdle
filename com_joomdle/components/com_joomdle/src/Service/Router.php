<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Site\Service;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Menu\AbstractMenu;

/**
 * Class JoomdleRouter
 *
 */
class Router extends RouterView
{
    private $noIDs;
    /**
     * The category factory
     *
     * @var    CategoryFactoryInterface
     *
     * @since  1.0.0
     */
    private $categoryFactory;

    /**
     * The category cache
     *
     * @var    array
     *
     * @since  1.0.0
     */
    private $categoryCache = [];

    public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
    {
        $categories = new RouterViewConfiguration('coursecategories');
        $categories->setKey('cat_id');
        $this->registerView($categories);
        $category = new RouterViewConfiguration('coursecategory');
        $category->setKey('cat_id')->setParent($categories, 'cat_id')->setNestable();
        $this->registerView($category);

        $courses = new RouterViewConfiguration('joomdle');
        $courses->setKey('course_id');
        $this->registerView($courses);

        $detail = new RouterViewConfiguration('detail');
        $detail->setKey('course_id')->setParent($courses, 'cat_id');
        $this->registerView($detail);

        // We cannot have a view as a parent for several views, so we cannot set parent in these ones
        $this->registerView(new RouterViewConfiguration('topics'));
        $this->registerView(new RouterViewConfiguration('course'));
        $this->registerView(new RouterViewConfiguration('coursegradecategories'));
        $this->registerView(new RouterViewConfiguration('teachers'));

        $this->registerView(new RouterViewConfiguration('mycoursegrades'));
        $this->registerView(new RouterViewConfiguration('coursemates'));
        $this->registerView(new RouterViewConfiguration('coursestats'));
        $this->registerView(new RouterViewConfiguration('myapplications'));
        $this->registerView(new RouterViewConfiguration('mycompletedcourses'));

        $coursegrades = new RouterViewConfiguration('coursegrades');
        $coursegrades->setKey('course_id');
        $this->registerView($coursegrades);

        $coursenews = new RouterViewConfiguration('coursenews');
        $coursenews->setKey('course_id');
        $this->registerView($coursenews);
        $newsitem = new RouterViewConfiguration('newsitem');
        $newsitem->setKey('id')->setParent($coursenews, 'course_id');
        $this->registerView($newsitem);

        $this->registerView(new RouterViewConfiguration('coursesabc'));
        $this->registerView(new RouterViewConfiguration('coursesbycategory'));
        $this->registerView(new RouterViewConfiguration('mycourses'));
        $this->registerView(new RouterViewConfiguration('myevents'));
        $this->registerView(new RouterViewConfiguration('teachersabc'));
        $this->registerView(new RouterViewConfiguration('mygrades'));
        $this->registerView(new RouterViewConfiguration('buycourse'));
        $this->registerView(new RouterViewConfiguration('courseevents'));
        $this->registerView(new RouterViewConfiguration('childrengrades'));
        $this->registerView(new RouterViewConfiguration('mynews'));
        $this->registerView(new RouterViewConfiguration('page'));
        $this->registerView(new RouterViewConfiguration('wrapper'));
        $this->registerView(new RouterViewConfiguration('mycertificates'));
        $this->registerView(new RouterViewConfiguration('menteescertificates'));

        $assigncourses = new RouterViewConfiguration('assigncourses');
        $this->registerView($assigncourses);
        $register = new RouterViewConfiguration('register');
        $register->setParent($assigncourses);
        $this->registerView($register);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }

    /**
     * Method to get the segment(s) for a category
     *
     * @param   string  $id     ID of the category to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getCoursecategorySegment($id, $query)
    {
        $cat_id = (int) $id;
        $path = array ($cat_id => $id,  0 => '0:root'); // It seems '0:root' is needed here
        return $path;
    }

    /**
     * Method to get the segment(s) for a category
     *
     * @param   string  $id     ID of the category to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getCoursecategoriesSegment($id, $query)
    {
        return $this->getCoursecategorySegment($id, $query);
    }

    /**
     * Method to get the segment(s) for a contact
     *
     * @param   string  $id     ID of the contact to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getDetailSegment($id, $query)
    {
        return array((int) $id => $id);
    }

    public function getJoomdleSegment($id, $query)
    {
        $course_id = (int) $id;
        $path = array ($course_id => $id);
        return $path;
    }

    /**
     * Method to get the id for a category
     *
     * @param   string  $segment  Segment to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getCoursecategoryId($segment, $query)
    {
        return (int) $segment;
    }
    /**
     * Method to get the segment(s) for a category
     *
     * @param   string  $segment  Segment to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getCoursecategoriesId($segment, $query)
    {
        return $this->getCoursecategoryId($segment, $query);
    }

    /**
     * Method to get the segment(s) for a contact
     *
     * @param   string  $segment  Segment of the contact to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getDetailId($segment, $query)
    {
        return (int) $segment;
    }
}
