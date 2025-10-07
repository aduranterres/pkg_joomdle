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

class ArrayHelper
{
    /**
     * Sorts an array of associative arrays by a given key and returns only selected keys.
     *
     * @param array $array The input array
     * @param string $orderDir 'asc' or 'desc'
     * @param string $sortBy The key to sort by
     * @param string ...$keys The keys to keep in the returned array
     * @return array
     */
    public static function multisort(array $array, string $orderDir, string $sortBy, string ...$keys): array
    {
        if (empty($array)) {
            return $array;
        }

        // Default keys: keep the sort key if not explicitly included
        if (!in_array($sortBy, $keys, true)) {
            $keys[] = $sortBy;
        }

        // Sort
        usort($array, function ($a, $b) use ($sortBy, $orderDir) {
            $valA = $a[$sortBy] ?? null;
            $valB = $b[$sortBy] ?? null;
            $cmp = $valA <=> $valB;
            return $orderDir === 'desc' ? -$cmp : $cmp;
        });

        // Rebuild with only requested keys
        return array_map(function ($row) use ($keys) {
            return array_intersect_key($row, array_flip($keys));
        }, $array);
    }
}
