<?php

namespace App\Services;

use App\Models\Shop;
use App\Models\User;
use App\Notifications\CompetitorPriceDropNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PriceDropNotifier
{
    /**
     * Kirim notifikasi (email + database) kalau toko saingan lebih murah dari toko dikelola.
     * Diberi jeda 6 jam per kombinasi toko+keyword agar tidak spam.
     */
    public function notifyIfCheaper(Shop $shop1, Shop $shop2, string $keyword, ?int $managedPrice, ?int $competitorPrice): bool
    {
        if (is_null($managedPrice) || is_null($competitorPrice) || $competitorPrice >= $managedPrice) {
            return false;
        }

        $cacheKey = "price_drop_notified:{$shop1->id}:{$shop2->id}:".Str::slug($keyword);

        if (Cache::has($cacheKey)) {
            return false;
        }

        Cache::put($cacheKey, true, now()->addHours(6));

        $notification = new CompetitorPriceDropNotification($keyword, $shop1, $shop2, $managedPrice, $competitorPrice);

        // Notifikasi ke user yang sedang login (jika ada, misalnya trigger manual dari web)
        Auth::user()?->notify($notification);

        // Notifikasi juga ke semua admin (kecuali user yang sedang login, agar tidak dobel)
        User::where('role', 'admin')
            ->when(Auth::id(), fn ($query, $userId) => $query->where('id', '!=', $userId))
            ->get()
            ->each(fn (User $admin) => $admin->notify($notification));

        return true;
    }
}
