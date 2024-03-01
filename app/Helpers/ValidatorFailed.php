<?php

namespace App\Helpers;


class ValidatorFailed
{

  public static function validatorFailed($validator)
  {
    if ($validator->fails()) {
      return ResponseFormatter::error($validator->errors(), "Bad Request", 400);
    }
  }
}
