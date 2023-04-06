<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use Laravel\Sanctum\HasApiTokens;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\SoftDeletes; 



class Customer extends Authenticatable

{

    use HasFactory;

    use HasApiTokens;

    use SoftDeletes;

    

    protected $guard = 'customer';



    /**

     * The attributes that are mass assignable.

     *

     * @var array<int, string>

     */

    protected $fillable = [

        'name',

        'email',

        'password',

        'mobile',

       

        'status',

        'plateform',

        'device_id',

        'remark',

        'is_active',

        'mobile_verify_at'

    ];



    /**

     * The attributes that should be hidden for serialization.

     *

     * @var array<int, string>

     */

    protected $hidden = [

        'password',

        'remember_token',

    ];



    /**

     * The attributes that should be mutated to dates.

     * scratchcode.io

     * @var array

     */

    protected $dates = [ 'deleted_at' ];

}

