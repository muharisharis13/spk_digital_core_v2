<?php
// app/Traits/UuidTrait.php

namespace App\Traits;

use Illuminate\Support\Str;

trait UuidTrait
{
  /**
   * Boot the trait.
   *
   * @return void
   */
  protected static function bootUuidTrait()
  {
    static::creating(function ($model) {
      $model->uuid = Str::uuid();
    });
  }
}
