<?php

namespace Foxhound;

enum AttachmentType: string
{
    case Image = 'image';
    case Video = 'video';
    case Document = 'document';
    case Other = 'other';
}
