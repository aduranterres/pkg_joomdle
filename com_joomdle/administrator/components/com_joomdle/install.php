<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Exception\FilesystemException;
use Joomla\CMS\Component\ComponentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

return new class () implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container->set(
            InstallerScriptInterface::class,
            new class (
                $container->get(AdministratorApplication::class),
                $container->get(DatabaseInterface::class)
            ) implements InstallerScriptInterface {
                private AdministratorApplication $app;
                private DatabaseInterface $db;

                public function __construct(AdministratorApplication $app, DatabaseInterface $db)
                {
                    $this->app = $app;
                    $this->db = $db;
                }

                public function install(InstallerAdapter $parent): bool
                {
                    $this->app->enqueueMessage('Successful installed.');

                    return true;
                }

                public function update(InstallerAdapter $parent): bool
                {
                    $this->app->enqueueMessage('Successful updated.');

                    return true;
                }

                public function uninstall(InstallerAdapter $parent): bool
                {
                    $this->app->enqueueMessage('Successful uninstalled.');

                    return true;
                }

                public function preflight(string $type, InstallerAdapter $parent): bool
                {
                    // On update, check Moodle supports this version before installing
                    if ($type == 'update') {
                        // First we make sure system check is working, so we don't break on install
                        // If it is not working, with let installation happen, as there's nothing to break anyway
                        if (!$this->systemReadyForVersionCheck()) {
                            return true;
                        }

                        // Check that installed Moodle version supports this Joomdle release
                        $manifest = $parent->getManifest();
                        $installed_moodle_version = $this->getMoodleVersion();
                        if ($installed_moodle_version < $manifest->requiresMoodleVersion) {
                            $parent->getParent()->abort('Your Moodle version does not support this Joomdle release.<br>' .
                                    'Installed Moodle version: ' . $installed_moodle_version . '<br>' .
                                    'Required Moodle version >= ' . $manifest->requiresMoodleVersion);
                            return false;
                        }
                    }

                    return true;
                }

                public function postflight(string $type, InstallerAdapter $parent): bool
                {
                    return true;
                }

                private function systemReadyForVersionCheck()
                {
                    // Get installed Joomdle version in Joomla
                    $xmlfile = JPATH_ADMINISTRATOR . '/components/com_joomdle/joomdle.xml';
                    if (file_exists($xmlfile)) {
                        if ($data = Installer::parseXMLInstallFile($xmlfile)) {
                            $version =  $data['version'];
                        }
                    }
                    $joomdle_release_joomla = $version;

                    $comp_params = ComponentHelper::getParams('com_joomdle');
                    $connection = $comp_params->get('connection_method');

                    $connection_method_enabled = false;
                    if ($connection == 'fgc') {
                        $connection_method_enabled = ini_get('allow_url_fopen');
                    } elseif ($connection == 'curl') {
                        $connection_method_enabled = function_exists('curl_version') == "Enabled";
                    }

                    if (!$connection_method_enabled) {
                        return false;
                    }

                    /* Test Moodle Web services in joomdle plugin */
                    $response = ContentHelper::callMethodDebug('system_check');
                    if ($response == '') {
                        return false;
                    } else {
                        if ($response ['joomdle_auth'] != 1) {
                            return false;
                        } elseif ($response['joomdle_configured'] == 0) {
                            return false;
                        } elseif ($response['test_data'] != 'It works') {
                            return false;
                        }
                    }

                    // Joomdle version has to be the same in Joomla and Moodle
                    if ($response['release'] != $joomdle_release_joomla) {
                        return false;
                    }

                    return true;
                }

                private function getMoodleVersion()
                {
                    $moodle_version = ContentHelper::callMethodDebug('get_moodle_version');

                    return $moodle_version;
                }
            }
        );
    }
};
