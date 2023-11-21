<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusEnum;
use Laravel\Sanctum\HasApiTokens;

class Notification extends Model
{
    use HasFactory, HasApiTokens;
     /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notification';

     /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_name',
        'employee_name',
        'admin_name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'title',
        'avatar',
        'desc',
        'receiver',
        'employee_id',
        'admin_id',
        'topic_name',
        'noti_category',
        'noti_type',
        'status',
        'memo',
        'push_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => StatusEnum::class,
        'admin_id' => 'array',
        'avatar' => 'array',
        'employee_id' => 'array',
        'push_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getStatusNameAttribute() {
        return StatusEnum::getDescription(optional($this->status)->value);
    }

    public function getAdminNameAttribute() {
        $admin = Admin::whereIn('id', (array)$this->admin_id)->get();
        if($this->admin_id){
            return join(', ',array_column($admin->toarray(), 'name'));
        }

        return null;
    }

    public function getEmployeeNameAttribute() {
        $employee = Employee::whereIn('id', (array)$this->employee_id)->get();
        if($this->employee_id){
            return join(', ',array_column($employee->toarray(), 'name'));
        }
        return null;
    }

    // public function notificationEmployee()
    // {
    //     return $this->hasMany(NotificationEmployee::class, 'notification_id', 'id');
    // }

    // public function notificationAdmin()
    // {
    //     return $this->hasMany(NotificationAdmin::class, 'notification_id', 'id');
    // }

    public function admins() {
        return $this->morphedByMany(Admin::class, 'notiSends');
    }

    public function employees() {
        return $this->morphedByMany(Employee::class, 'notiSends');
    }

}
