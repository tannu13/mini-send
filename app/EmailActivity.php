<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailActivity extends Model
{
    protected $fillable = [
        'sender',
        'recipient',
        'subject',
        'text_content',
        'html_content',
        'attachments',
        'status',
    ];
}
