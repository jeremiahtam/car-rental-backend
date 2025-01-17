<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cars extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'brand',
    'model',
    'slug',
    'aircondition',
    'gear_type',
    'fuel_type',
    'seats',
    'cost_per_meter',
    'wait_amount_per_hour',
    'removed',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'removed'
  ];

  public function carImages(): HasMany
  {
    return $this->hasMany(CarImages::class);
  }
}