<?php


namespace App\Models\Enums;


enum UserRole: string
{
    case MEMBER = 'MEMBER';
    case PRODUCT_OWNER = 'PRODUCT_OWNER';
    case ADMIN = 'ADMIN';
}
