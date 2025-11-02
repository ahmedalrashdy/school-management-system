<?php

namespace App\Models;

use App\Enums\ActivityLogNameEnum;
use App\Enums\GenderEnum;
use App\Traits\HasFileDeletion;
use App\Traits\HasModelLabels;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasFileDeletion, HasModelLabels, HasRoles, LogsActivity, Notifiable;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(ActivityLogNameEnum::Academics->value)
            ->logOnly([
                'first_name',
                'last_name',
                'email',
                'phone_number',
                'is_active',
                'is_admin',
                'address',
                'gender',
            ])

            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public static function logFormats(): array
    {
        return (new static)->getCasts();
    }

    /**
     * The attributes that are mass assignable.
     *
     *     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'gender',
        'address',
        'email_verified_at',
        'password',
        'is_active',
        'is_admin',
        'reset_password_required',
        'avatar',
    ];

    public function deletableFiles(): array
    {
        return [
            'avatar' => 'public',
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Appended attributes.
     *
     * @var list<string>
     */
    protected $appends = [
        'full_name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
            'reset_password_required' => 'boolean',
            'gender' => GenderEnum::class,
            'password' => 'hashed',
        ];
    }

    /**
     * Computed user full name.
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim("{$this->first_name} {$this->last_name}"));
    }

    protected function shortName(): Attribute
    {
        return Attribute::get(
            fn (): string => trim(
                explode(' ', $this->first_name)[0].' '.
                last(explode(' ', $this->last_name))
            )
        );
    }

    /**
     * Get the student profile associated with the user.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the teacher profile associated with the user.
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Get the guardian profile associated with the user.
     */
    public function guardian(): HasOne
    {
        return $this->hasOne(Guardian::class);
    }

    /**
     * Check if user is linked to a critical entity (student, teacher, or guardian).
     */
    public function isLinkedToCriticalEntity(): bool
    {
        return $this->student !== null || $this->teacher !== null || $this->guardian !== null;
    }

    /**
     * Get the user type (student, teacher, guardian, or admin).
     */
    public function getUserTypeAttribute(): ?string
    {
        if ($this->is_admin) {
            return 'admin';
        }
        if ($this->hasRole('طالب')) {
            return 'طالب';
        }
        if ($this->hasRole('مدرس')) {
            return 'مدرس';
        }
        if ($this->hasRole('ولي أمر')) {
            return 'ولي أمر';
        }

        return null;
    }

    /**
     * Get available roles for the user (teacher, student, guardian).
     *
     * @return array<string>
     */
    public function getAvailableRoles(): array
    { // only main roles (teacher,student,guardian)
        return $this->getRoleNames()->toArray();
    }

    /**
     * Get the primary role (teacher has priority).
     */
    public function getPrimaryRole(): ?string
    {
        $roles = $this->getAvailableRoles();

        if (empty($roles)) {
            return null;
        }

        // Teacher has priority
        if (in_array('مدرس', $roles)) {
            return 'مدرس';
        }

        // Return first available role
        return $roles[0];
    }
}
