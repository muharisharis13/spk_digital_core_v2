<?php

namespace App\Enums;

use Spatie\Enum\Enum;

class UserRole extends Enum
{

  const admin = "admin";
  const cashier = "cashier";
  const finance = 'finance';
}
