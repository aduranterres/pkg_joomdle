<?php

/**
 * @package     Joomdle
 * @subpackage  plg_user_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\User\Joomdle\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Plugin\User\Joomdle\Extension\Joomdle;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.4.0
     */
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $plugin     = new Joomdle(
                    $container->get(DispatcherInterface::class),
                    (array) PluginHelper::getPlugin('user', 'joomdle')
                );
                $plugin->setApplication(Factory::getApplication());
                $plugin->setDatabase($container->get(DatabaseInterface::class));
                $plugin->setUserFactory($container->get(UserFactoryInterface::class));

                return $plugin;
            }
        );
    }
};
