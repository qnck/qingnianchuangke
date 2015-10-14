<?php
/**
*
*/
class UserFriendInviteLog extends Eloquent
{
    public static function addLog($from, $to)
    {
        $from_log = new UserFriendInviteLog();
        $from_log->u_id = $from;
        $from_log->friend_id = $to;
        $from_log->save();

        $to_log = new UserFriendInviteLog();
        $to_log->u_id = $to;
        $to_log->friend_id = $from;
        $to_log->save();
    }
}
