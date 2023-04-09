<?php
namespace App\Enums;

enum ClientStatusEnum:string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DELETED = 'deleted';
}