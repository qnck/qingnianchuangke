<?php
/**
*
*/
class Feature extends Eloquent
{
    public $timestamps = false;

    public static $CATE_CROWD_FUNDING = 1;
    public static $CATE_REGULAR_PRODUCT = 2;
    public static $CATE_FLEA_PRODUCT = 3;
}
