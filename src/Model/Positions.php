<?php
namespace Model;

use App\Model;

class Positions extends Model {
    public function roomAccess() {
        return $this->belongsToMany(Rooms::class, 'id', 'staff_access', 'room_id', 'position_id');
    }
}