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
use Joomla\Database\ParameterType;

/**
 * Sso helper.
 *
 * @since  1.0.0
 */
class SsoHelper
{
    public static function createTicket($user_id)
    {
        $ticket = new \stdClass();
        $ticket->user_id = $user_id;
        $token = bin2hex(random_bytes(32));
        $ticket->token_hash = hash('sha256', $token);
        $ticket->created = time();
        $db = Factory::getContainer()->get('DatabaseDriver');
        $db->insertObject('#__joomdle_sso_tickets', $ticket);

        self::deleteExpiredTickets();

        return $token;
    }

    /**
     * Delete SSO tickets created more than 24 hours ago.
     *
     * @return  void
     */
    private static function deleteExpiredTickets(): void
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $expiration_limit = time() - 86400;

        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__joomdle_sso_tickets'))
            ->where($db->quoteName('created') . ' < :expiration_limit');

        $query->bind(':expiration_limit', $expiration_limit, ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Validate and consume an SSO ticket atomically.
     *
     * @param   string  $token    Plain-text SSO ticket.
     * @param   int     $user_id  Joomla user ID associated with the ticket.
     *
     * @return  bool  True when the ticket was valid and consumed.
     */
    public static function consumeValidTicket($token, $user_id): bool
    {
        if (!$token || !$user_id) {
            return false;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $consumed = time();
        $minimum_created = $consumed - 60;
        $maximum_created = $consumed;
        $token_hash = hash('sha256', $token);

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__joomdle_sso_tickets'))
            ->set($db->quoteName('consumed') . ' = :consumed')
            ->where($db->quoteName('token_hash') . ' = :token_hash')
            ->where($db->quoteName('user_id') . ' = :user_id')
            ->where($db->quoteName('consumed') . ' IS NULL')
            ->where($db->quoteName('created') . ' >= :minimum_created')
            ->where($db->quoteName('created') . ' <= :maximum_created');

        $query->bind(':consumed', $consumed, ParameterType::INTEGER);
        $query->bind(':token_hash', $token_hash, ParameterType::STRING);
        $query->bind(':user_id', $user_id, ParameterType::INTEGER);
        $query->bind(':minimum_created', $minimum_created, ParameterType::INTEGER);
        $query->bind(':maximum_created', $maximum_created, ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();

        return $db->getAffectedRows() === 1;
    }
}
