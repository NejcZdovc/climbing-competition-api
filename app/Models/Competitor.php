<?php

# app/Models/Competitor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Competitor extends Model
{
    protected $table = 'competitor';
    public $routes;
    protected $appends = array('routes');

    public function getRoutesAttribute() {
        return $this->routes;
    }
}
