<?php

namespace App\Enums;

enum DepositStatus: int
{
    case PENDING = 0;
    case ACCEPTED = 1;
    case REJECTED = 2;
}
