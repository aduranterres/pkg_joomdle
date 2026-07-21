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
use Joomla\CMS\Uri\Uri;

class UserController extends BaseController
{
    public function login()
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();

        $params = $app->getParams();
        $moodle_url = $params->get('MOODLE_URL');

        $login_data = $this->input->post->get('data', '', 'string');
        $wantsurl = base64_decode($this->input->post->get('wantsurl', '', 'string'));

        if (!$login_data) {
            echo "Login error";
            exit();
        }

        $data = base64_decode($login_data);

        if ($data === false) {
            echo "Login error";
            exit();
        }

        $fields = explode(':', $data, 2);

        if (count($fields) !== 2) {
            echo "Login error";
            exit();
        }

        $credentials['username'] = $fields[0];
        $credentials['password'] = $fields[1];

        $options = array('skip_joomdleuserplugin' => '1');

        if (!$app->login($credentials, $options)) {
            echo "Login error";
            exit();
        }

        $wantsurl = $this->cleanReturnUrl($wantsurl, $moodle_url);

        if (!$wantsurl) {
            $wantsurl = $moodle_url;
        }
        $app->redirect($wantsurl);
    }

    public function logout()
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $app->redirect(URI::root() . 'index.php?option=com_joomdle&task=user.getoutlogout');
    }

    public function getout()
    {
        $root = URI::root();
        ?>
        <script type="text/javascript">
            top.location.href = "<?php echo $root; ?>";
        </script>
        <?php
    }

    public function getoutlogin()
    {
        $root = URI::root() . 'index.php?option=com_users&view=login';
        ?>
        <script type="text/javascript">
            top.window.location = "<?php echo $root; ?>";
        </script>
        <?php
    }

    public function getoutlogout()
    {
        $root = URI::root() . 'index.php?option=com_joomdle&task=user.dologout';
        ?>
        <script type="text/javascript">
            top.window.location = "<?php echo $root; ?>";
        </script>
        <?php
    }

    public function dologout()
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $app->logout();
        $app->redirect(URI::root());
    }

    private function sameOrigin($url, $baseurl)
    {
        $urlparts = parse_url($url);
        $baseparts = parse_url($baseurl);

        if (
            empty($urlparts['scheme']) || empty($urlparts['host']) ||
            empty($baseparts['scheme']) || empty($baseparts['host'])
        ) {
            return false;
        }

        $urlscheme = strtolower($urlparts['scheme']);
        $basescheme = strtolower($baseparts['scheme']);
        $urlhost = strtolower($urlparts['host']);
        $basehost = strtolower($baseparts['host']);
        $urlport = $urlparts['port'] ?? (($urlscheme === 'https') ? 443 : 80);
        $baseport = $baseparts['port'] ?? (($basescheme === 'https') ? 443 : 80);

        return $urlscheme === $basescheme && $urlhost === $basehost && $urlport === $baseport;
    }

    private function cleanReturnUrl($url, $moodle_url)
    {
        $url = trim((string) $url);

        if ($url === '' || str_starts_with($url, '//')) {
            return '';
        }

        if (!preg_match('#^[a-z][a-z0-9+.-]*://#i', $url)) {
            return Uri::root() . ltrim($url, '/');
        }

        if ($this->sameOrigin($url, Uri::root()) || $this->sameOrigin($url, $moodle_url)) {
            return $url;
        }

        return '';
    }
}
