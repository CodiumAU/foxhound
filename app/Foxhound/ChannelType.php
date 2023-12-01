<?php

namespace App\Foxhound;

enum ChannelType: string
{
    case Mail = 'mail';
    case Sms = 'sms';
}
