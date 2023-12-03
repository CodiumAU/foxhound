<?php

namespace Foxhound;

enum AttachmentType: string
{
    case Image = 'image';
    case Video = 'video';
    case Audio = 'audio';
    case Document = 'document';
    case Other = 'other';

    /**
     * Get the attachment type for the given extension.
     */
    public static function fromExtension(string $extension): self
    {
        return match ($extension) {
            'pdf', 'txt', 'doc', 'docx', 'csv', 'xls', 'xlsx', 'ppt', 'pptx' => self::Document,
            'png', 'jpg', 'jpeg', 'gif', 'svg', 'bmp', 'webp' => self::Image,
            'avi', 'm4v', 'mp4', 'wmv', 'mkv', 'mov', 'mpeg', 'mpg', 'ogv' => self::Video,
            'aac', 'flac', 'm4a', 'mp3', 'ogg', 'wav', 'wma' => self::Audio,
            default => self::Other,
        };
    }
}
