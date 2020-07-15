<?php

namespace App\Entity;

use App\Entity\BasicEnum;

class UserType extends BasicEnum
{   
    const TYPE_HOSPITAL= 'hospital';
    const TYPE_PATIENT= 'patient';
    const TYPE_DOCTOR= 'doctor';
    const TYPE_ADMIN= 'admin';
    


}