<?php
namespace App\Enums;

enum ClientServiceStatusEnum:string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DELETED = 'deleted';
}