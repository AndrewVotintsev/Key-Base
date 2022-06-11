<?php
namespace Model;

use App\Model;

class Reports extends Model {
    public function person() {
        return $this->belongsTo(Staff::class, 'staff_id', 'id');
    }

    public function room() {
        return $this->belongsTo(Rooms::class, 'room_id', 'id');
    }
}