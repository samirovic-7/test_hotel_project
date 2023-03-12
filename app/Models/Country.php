<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use LogsActivity;
    use HasFactory;
 
    protected $fillable =[
        'country_code',
        'name',
        'name_loc',
    ];
    public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logAll()
        ->setDescriptionForEvent(fn(string $eventName) => "Country  has been {$eventName}");
}
    
}
