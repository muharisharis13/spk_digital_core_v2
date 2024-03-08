<?php

namespace App\Enums;

use Spatie\Enum\Enum;

class UnitStatusEnum extends Enum
{

  const on_hand = "on_hand";
  const hold = "hold";
  const spk = 'spk';
  const retour = 'retour';
  const repair = 'repair';
}
