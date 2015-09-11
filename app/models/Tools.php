<?php
/**
*
*/
class Tools
{
    public static function reTrue($message, $data = null, $list = null)
    {
        $pagination = null;
        if (!empty($list)) {
            $pagination = ['per_page' => $list->getPerPage(), 'page' => $list->getCurrentPage(), 'total_record' => $list->getTotal(), 'total_page' => $list->getLastPage()];
        }
        $re = ['result' => 2000, 'data' => $data, 'message' => $message, 'pagination' => $pagination];
        return $re;
    }

    public static function reFalse($code, $message, $data = [])
    {
        if ($code < 2000) {
            $code = 2001;
        }

        $re = ['result' => $code, 'data' => [], 'message' => $message];
        return $re;
    }

    public static function getDistance($pos1, $pos2)
    {
        $EARTH_RADIUS = 63781370;
        $radLat1 = deg2rad($pos1['lat']);
        $radLat2 = deg2rad($pos2['lat']);
        $a = $radLat1 - $radLat2;
        $b = deg2rad($pos1['lng']) - deg2rad($pos2['lng']);
        $s = 2 * asin(sqrt(pow(sin($a/2), 2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2), 2)));
        $s = $s *$EARTH_RADIUS;
        $s = round($s * 10000) / 10000;
        return $s;
    }

    public static function getAdminId()
    {
        return 1;
    }

    public static function generateDateUserRandomNo($u_id)
    {
        $now = new DateTime();
        $part1 = $now->format('YmdHi');

        $len = strlen($u_id);
        if ($len < 6) {
            $part2 = $u_id;
            for ($i=0; $i < 8 - strlen($u_id); $i++) {
                $part2 = '0'.$part2;
            }
        } else {
            $part2 = substr($u_id, -6, 6);
        }

        $part3 = rand(10, 99);

        return $part1.$part2.$part3;
    }
}
