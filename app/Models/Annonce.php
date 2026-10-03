<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class Annonce extends Model
{
    use HasUuids;

    private const SEEN_COOKIE = 'annonce_seen';
    private const SEEN_MINUTES = 60 * 24 * 365; // 1 year

    protected $fillable = ['image_path', 'is_active', 'is_paused', 'activated_at'];

    protected $casts = [
        'is_active'    => 'boolean',
        'is_paused'    => 'boolean',
        'activated_at' => 'datetime',
    ];

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            $path = str_replace('\\', '/', (string) $this->image_path);

            return '/storage/' . ltrim($path, '/');
        });
    }

    /** What VISITORS see: the chosen annonce, unless it is paused. May be null. */
    public static function current(): ?self
    {
        return static::where('is_active', true)
            ->where('is_paused', false)
            ->orderByDesc('activated_at')
            ->first();
    }


    public static function unseenBy(Request $request): ?self
    {
        $annonce = static::current();

        if (! $annonce) {
            return null;
        }

        $version = $annonce->seenVersion();

        if ($request->cookie(self::SEEN_COOKIE) === $version) {
            return null;
        }

        Cookie::queue(self::SEEN_COOKIE, $version, self::SEEN_MINUTES);

        return $annonce;
    }

    /** Changes every time the admin (re)activates an annonce. */
    public function seenVersion(): string
    {
        return $this->id . '-' . ($this->activated_at?->timestamp ?? 0);
    }

    /** What the DASHBOARD shows: the chosen annonce, paused or not. May be null. */
    public static function chosen(): ?self
    {
        return static::where('is_active', true)->orderByDesc('activated_at')->first();
    }

    /** Make this annonce the only chosen one, and live. */
    public function activate(): void
    {
        DB::transaction(function () {
            static::where('is_active', true)->update(['is_active' => false]);
            $this->forceFill(['is_active' => true, 'is_paused' => false, 'activated_at' => now()])->save();
        });
    }

    public function pause(): void
    {
        $this->forceFill(['is_paused' => true])->save();
    }

    public function resume(): void
    {
        $this->forceFill(['is_paused' => false])->save();
    }
}