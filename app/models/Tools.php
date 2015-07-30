<?php
/**
*
*/
class Tools
{
    public static function reTrue($message, $data = [])
    {
        $re = ['result' => 2000, 'data' => $data, 'message' => $message];
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
}
