<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DhcpList extends Model
{
    use HasFactory;

    protected $table = 'dhcp_lists';

    protected $guarded = [];
}
