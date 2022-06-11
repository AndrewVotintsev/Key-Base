<?php
namespace Model;

use App\Model;

class Staff extends Model {
    public function position(){
        return $this->belongsTo(Positions::class, 'position_id', 'id');
    }
}