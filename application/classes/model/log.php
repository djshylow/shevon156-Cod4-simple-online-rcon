<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Log model
 *
 * Copyright (c) 2010, EpicLegion
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA,
 * OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author		EpicLegion
 * @package		rcon
 * @subpackage	model
 * @license		http://www.opensource.org/licenses/bsd-license.php	New BSD License
 */
class Model_Log extends Model {

    /**
     * Add log to database
     *
     * @param	int		$user
     * @param	string	$action
     */
    static public function add($user, $action)
    {
        // Insert to `logs` table
        DB::insert('logs', array('user_id', 'date', 'content'))->values(array(
            (int) $user, date('Y-m-d H:i:s'), $action
        ))->execute();
    }

    /**
     * Retrieve logs
     *
     * @param	string	$order_item
     * @param	string	$order
     */
    static public function get($order_item = 'logs.id', $order = 'DESC')
    {
        // Get result
        return DB::select('logs.id', 'logs.content', 'logs.date', 'users.username')
                 ->join('users', 'LEFT')->on('users.id', '=', 'logs.user_id')
                 ->order_by($order_item, ($order == 'DESC') ? 'DESC' : 'ASC')
                 ->from('logs')
                 ->execute();
    }
}