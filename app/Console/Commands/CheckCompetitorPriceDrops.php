<?php

namespace App\Console\Commands;

use App\Models\ComparisonHistory;
use App\Models\Shop;
use App\Services\PriceDropNotifier;
use App\Services\TokopediaScraper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckCompetitorPriceDrops extends Command
{
    /**
     * Nama & signature command.
     */
    protected $signature = 'app:check-price-drops';

    /**
     * Deskripsi command.
     */
    protected $description = 'Cek ulang kombinasi toko & kata kunci yang pernah dibandingkan, kirim notifikasi jika toko saingan kini lebih murah.';

    public function handle(TokopediaScraper $scraper, PriceDropNotifier $notifier): int
    {
        // Ambil kombinasi unik (toko dikelola, toko saingan, keyword) yang pernah dibandingkan,
        // gunakan record terbaru per kombinasi sebagai acuan sort.
        $pairs = ComparisonHistory::select('shop1_id', 'shop2_id', 'keyword', DB::raw('MAX(sort) as sort'))
            ->groupBy('shop1_id', 'shop2_id', 'keyword')
            ->get();

        if ($pairs->isEmpty()) {
            $this->info('Belum ada riwayat perbandingan untuk dipantau.');

            return self::SUCCESS;
        }

        $this->info("Memeriksa {$pairs->count()} kombinasi toko & kata kunci...");
        $notified = 0;

        foreach ($pairs as $pair) {
            $shop1 = Shop::find($pair->shop1_id);
            $shop2 = Shop::find($pair->shop2_id);

            if (! $shop1 || ! $shop2) {
                continue;
            }

            $shop1Data = $scraper->fetchShopData($shop1->link, $pair->keyword, 1, $pair->sort ?? '23');
            $shop2Data = $scraper->fetchShopData($shop2->link, $pair->keyword, 1, $pair->sort ?? '23');

            $shop1Cheapest = $scraper->cheapestPrice($shop1Data['products']);
            $shop2Cheapest = $scraper->cheapestPrice($shop2Data['products']);

            ComparisonHistory::create([
                'user_id' => null,
                'shop1_id' => $shop1->id,
                'shop2_id' => $shop2->id,
                'keyword' => $pair->keyword,
                'sort' => $pair->sort ?? '23',
                'shop1_product_count' => count($shop1Data['products']),
                'shop2_product_count' => count($shop2Data['products']),
                'shop1_min_price' => $shop1Cheapest,
                'shop2_min_price' => $shop2Cheapest,
            ]);

            if ($notifier->notifyIfCheaper($shop1, $shop2, $pair->keyword, $shop1Cheapest, $shop2Cheapest)) {
                $notified++;
                $this->warn("⚠ {$shop2->name} lebih murah dari {$shop1->name} untuk \"{$pair->keyword}\"");
            }
        }

        $this->info("Selesai. {$notified} notifikasi dikirim.");

        return self::SUCCESS;
    }
}
