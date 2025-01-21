<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drivers extends Model
{
  use HasFactory;
  /**
   * Summary of fillable
   * @var array
   */
  protected $fillable = [
    'name',
    'nin',
    'status',
    'removed',
  ];

  /**
   * Summary of hidden
   * @var array
   */
  protected $hidden = [
    'removed'
  ];
}
