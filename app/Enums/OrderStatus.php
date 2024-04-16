<?php
namespace App\Enums;

enum OrderStatus: String {
    case CREATED = 'CREATED';
    case PROCESSING = 'PROCESSING';
    case COMPLETED = 'COMPLETED';
    case CANCELED = 'CANCELED';
}
