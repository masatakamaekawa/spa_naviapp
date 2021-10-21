<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'caption',
        'info',
    ];

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function getImagePathAttribute()
    {
        $paths =[];
        foreach ($this->attachments as $attachment) {
            $paths[] = 'articles/' . $attachment->name;
        }
        return $paths;
    }
    public function getImageUrlAttribute()
    {
        $urls = [];

        foreach($this->image_path as $path){
            $urls[] =Storage::url($path);
        }
        return $urls;
    }
}
