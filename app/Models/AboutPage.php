<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_name',
        'content',
        'is_active'
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean'
    ];

    public static function getSection($sectionName)
    {
        return self::where('section_name', $sectionName)
                   ->where('is_active', true)
                   ->first();
    }

    public static function getSectionContent($sectionName, $key = null)
    {
        $section = self::getSection($sectionName);

        if (!$section) {
            return null;
        }

        if ($key) {
            return $section->content[$key] ?? null;
        }

        return $section->content;
    }
}
