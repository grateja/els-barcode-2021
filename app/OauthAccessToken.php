<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class OauthAccessToken extends Model
{
    use UsesUuid;
}
