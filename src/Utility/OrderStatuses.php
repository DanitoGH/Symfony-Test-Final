<?php

namespace App\Utility;

final class OrderStatuses
{
    public const ORDER_RECEIVED = 1;
    public const ORDER_CANCELED =  2;
    public const ORDER_PROCESSING = 3;
    public const ORDER_READY_TO_SHIP = 4;
    public const ORDER_SHIPPED = 5;
}