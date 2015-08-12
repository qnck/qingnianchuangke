<?php
/**
*
*/
class DicSchool extends \Eloquent
{
    public $primaryKey = 't_id';
    public $timestamps = false;

    public function showInList()
    {
        $data['id'] = $this->t_id;
        $data['school_name'] = $this->t_name;
        return $data;
    }

    public function showDetail()
    {
        $data['id'] = $this->t_id;
        $data['province'] = $this->t_province;
        $data['city'] = $this->t_city;
        $data['district'] = $this->t_district;
        $data['name'] = $this->t_name;
        return $data;
    }

    // laravel relations
    
    public function user()
    {
        return $this->hasMany('User', 'u_school_id', 't_id');
    }

    public function promo()
    {
        return $this->hasMany('PromotionInfo', 's_id', 't_id');
    }
}
