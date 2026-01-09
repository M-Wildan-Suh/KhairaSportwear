<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Konfigurasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kunci',
        'nilai',
        'keterangan',
        'tipe',
        'opsi'
    ];

    protected $casts = [
        'opsi' => 'array'
    ];

    // Custom methods
    public function getNilaiAttribute($value)
    {
        switch ($this->tipe) {
            case 'number':
                return (float) $value;
            case 'boolean':
                return (bool) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    public function setNilaiAttribute($value)
    {
        if ($this->tipe === 'json' && is_array($value)) {
            $this->attributes['nilai'] = json_encode($value);
        } else {
            $this->attributes['nilai'] = $value;
        }
    }

    public function getOpsiAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setOpsiAttribute($value)
    {
        $this->attributes['opsi'] = json_encode($value);
    }

    // Static helper method
    public static function getValue($key, $default = null)
    {
        $config = self::where('kunci', $key)->first();
        return $config ? $config->nilai : $default;
    }

    public static function setValue($key, $value)
    {
        $config = self::where('kunci', $key)->first();
        
        if ($config) {
            $config->nilai = $value;
            $config->save();
        } else {
            self::create([
                'kunci' => $key,
                'nilai' => $value,
                'tipe' => 'text'
            ]);
        }
    }
}