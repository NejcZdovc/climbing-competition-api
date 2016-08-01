<?php

# app/Models/Route.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Route extends Model
{
    protected $table = 'route';
    public $result;
    protected $appends = array('result');

    public function getResultAttribute() {
        return $this->result;
    }
}
