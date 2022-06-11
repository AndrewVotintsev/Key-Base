<?php
namespace Model;

use App\Model;

class Rooms extends Model {
    public function keys() {
        return $this->hasMany(Reports::class, 'room_id', 'id');
    }

    public function positions() {
        return $this->belongsToMany(Positions::class, 'id', 'staff_access', 'position_id', 'room_id');
    }
}