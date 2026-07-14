<?php

namespace App\Http\Controllers;

use App\Models\ComparisonHistory;
use App\Models\Shop;
use App\Services\PriceDropNotifier;
use App\Services\TokopediaScraper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComparisonController extends Controller
{
    public function __construct(
        private TokopediaScraper $scraper,
        private PriceDropNotifier $notifier,
    ) {
    }

    public function index()
    {
        $managedShops = Shop::where('jenis', 'dikelola')->get();
        $competitorShops = Shop::where('jenis', 'saingan')->get();

        // Kata kunci yang pernah dicari user untuk fitur autocomplete
        $recentKeywords = ComparisonHistory::query()
            ->when(Auth::id(), fn ($query, $userId) => $query->where('user_id', $userId))
            ->latest()
            ->limit(10)
            ->pluck('keyword')
            ->unique()
            ->values();

        return view('comparison.index', compact('managedShops', 'competitorShops', 'recentKeywords'));
    }

    /**
     * Menampilkan riwayat perbandingan yang pernah dilakukan.
     */
    public function history(Request $request)
    {
        $histories = ComparisonHistory::with(['shop1', 'shop2', 'user'])
            ->when($request->filled('keyword'), fn ($query) => $query->where('keyword', 'like', '%'.$request->keyword.'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('comparison.history', compact('histories'));
    }

    public function compare(Request $request)
    {
        // Meningkatkan batas waktu eksekusi karena proses scraping membutuhkan waktu yang lama
        set_time_limit(0);

        [$shop1, $shop2, $shop1_data, $shop2_data, $current_sort, $validated] = $this->runComparison($request);

        return view('comparison.result', compact('shop1_data', 'shop2_data', 'current_sort', 'shop1', 'shop2'));
    }

    /**
     * Ekspor hasil perbandingan yang sedang tampil menjadi laporan PDF.
     */
    public function exportPdf(Request $request)
    {
        set_time_limit(0);

        [$shop1, $shop2, $shop1_data, $shop2_data, $current_sort, $validated] = $this->runComparison($request, saveHistory: false);

        $shop1Cheapest = $this->scraper->cheapestPrice($shop1_data['products']);
        $shop2Cheapest = $this->scraper->cheapestPrice($shop2_data['products']);

        $pdf = Pdf::loadView('comparison.pdf', [
            'keyword' => $validated['keyword'],
            'shop1' => $shop1,
            'shop2' => $shop2,
            'shop1_data' => $shop1_data,
            'shop2_data' => $shop2_data,
            'shop1_cheapest' => $shop1Cheapest,
            'shop2_cheapest' => $shop2Cheapest,
            'generated_at' => now(),
            'generated_by' => Auth::user()?->name ?? 'Sistem',
        ])->setPaper('a4', 'portrait');

        $filename = 'perbandingan-'.str($validated['keyword'])->slug().'-'.now()->format('Ymd-His').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Logika inti pengambilan & pembandingan data, dipakai bersama oleh compare() dan exportPdf().
     */
    private function runComparison(Request $request, bool $saveHistory = true): array
    {
        $validated = $request->validate([
            'shop1_id' => 'required|exists:shops,id',
            'shop2_id' => 'required|exists:shops,id',
            'keyword' => 'required|string',
            'page1' => 'sometimes|integer|min:1',
            'page2' => 'sometimes|integer|min:1',
            'sort' => 'sometimes|string',
        ]);

        $shop1 = Shop::findOrFail($validated['shop1_id']);
        $shop2 = Shop::findOrFail($validated['shop2_id']);

        // Validasi jenis toko
        if ($shop1->jenis !== 'dikelola' || $shop2->jenis !== 'saingan') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'message' => 'Pilihan toko tidak valid',
            ]);
        }

        $sort = $validated['sort'] ?? '23';
        $page1 = $validated['page1'] ?? 1;
        $page2 = $validated['page2'] ?? 1;

        $shop1_data = $this->scraper->fetchShopData($shop1->link, $validated['keyword'], $page1, $sort);
        $shop2_data = $this->scraper->fetchShopData($shop2->link, $validated['keyword'], $page2, $sort);

        $shop1_data['current_page'] = $page1;
        $shop2_data['current_page'] = $page2;

        $shop1Cheapest = $this->scraper->cheapestPrice($shop1_data['products']);
        $shop2Cheapest = $this->scraper->cheapestPrice($shop2_data['products']);

        // Catat riwayat + kirim notifikasi (hanya untuk pencarian halaman pertama agar tidak duplikat per klik pagination)
        if ($saveHistory && $page1 == 1 && $page2 == 1) {
            ComparisonHistory::create([
                'user_id' => Auth::id(),
                'shop1_id' => $shop1->id,
                'shop2_id' => $shop2->id,
                'keyword' => $validated['keyword'],
                'sort' => $sort,
                'shop1_product_count' => count($shop1_data['products']),
                'shop2_product_count' => count($shop2_data['products']),
                'shop1_min_price' => $shop1Cheapest,
                'shop2_min_price' => $shop2Cheapest,
            ]);

            $this->notifier->notifyIfCheaper($shop1, $shop2, $validated['keyword'], $shop1Cheapest, $shop2Cheapest);
        }

        return [$shop1, $shop2, $shop1_data, $shop2_data, $sort, $validated];
    }
}
