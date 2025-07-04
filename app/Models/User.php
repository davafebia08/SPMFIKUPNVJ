<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'profile_photo',
        'nim',
        'nip',
        'nik',
        'npwp',
        'domisili',
        'tahun_lulus',
        'tahun_angkatan',
        'program_studi',
        'nama_instansi',
        'jabatan',
        'no_telepon',
        'is_active',

        'meta_nama_alumni',
        'meta_tahun_lulus_alumni',
        'meta_program_studi_alumni',
        'meta_jenis_mitra',
        'meta_jenis_kerjasama',
        'meta_lingkup_kerjasama',
        'meta_periode_kerjasama',
        'meta_alamat',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Mendapatkan URL foto profil
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/profile-photos/' . $this->profile_photo);
        }

        return null;
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a pimpinan.
     *
     * @return bool
     */
    public function isPimpinan()
    {
        return $this->role === 'pimpinan';
    }

    /**
     * Get all questionnaires that have been filled by the user.
     */
    public function questionnaires()
    {
        return $this->belongsToMany(Questionnaire::class, 'questionnaire_user')
            ->withPivot('submitted_at')
            ->withTimestamps();
    }

    /**
     * Get all responses by the user.
     */
    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    /**
     * Get all suggestions by the user.
     */
    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }

    /**
     * Get all reports generated by the user.
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'generated_by');
    }
}
