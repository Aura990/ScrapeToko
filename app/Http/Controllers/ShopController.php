<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout'  => 30,
            'verify' => false,
            'connect_timeout' => 10,
            'read_timeout' => 10,
            'retry_on_timeout' => true,
            'max_retry_attempts' => 3,
        ]);
    }

    public function index()
    {
        $shops = Shop::all();
        return view('shops.index', compact('shops'));
    }

    public function create()
    {
        return view('shops.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'link' => 'required|url',
            'jenis' => 'required|in:dikelola,saingan'
        ]);

        try {
            Log::info('Memulai proses scraping untuk URL: ' . $request->link);
            $shopName = $this->scrapeShopName($request->link);
            
            $shop = Shop::create([
                'name' => $shopName,
                'link' => $request->link,
                'jenis' => $request->jenis
            ]);

            Log::info('Toko berhasil ditambahkan: ' . $shopName);
            return redirect()->route('shops.index')->with('success', 'Toko berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error saat scraping: ' . $e->getMessage());
            return back()->withInput()->withErrors(['link' => 'Terjadi kesalahan saat mengambil data toko: ' . $e->getMessage()]);
        }
    }

    public function edit(Shop $shop)
    {
        return view('shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop)
    {
        $request->validate([
            'link' => 'required|url',
            'jenis' => 'required|in:dikelola,saingan'
        ]);

        try {
            $shopName = $this->scrapeShopName($request->link);
            
            $shop->update([
                'name' => $shopName,
                'link' => $request->link,
                'jenis' => $request->jenis
            ]);

            return redirect()->route('shops.index')->with('success', 'Toko berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error saat scraping: ' . $e->getMessage());
            return back()->withInput()->withErrors(['link' => 'Terjadi kesalahan saat mengambil data toko. Silakan coba lagi.']);
        }
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();
        return redirect()->route('shops.index')->with('success', 'Toko berhasil dihapus.');
    }

    private function scrapeShopName($url)
    {
        try {
            Log::info('Memulai permintaan HTTP ke: ' . $url);
            $response = $this->client->get($url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept-Language' => 'en-US,en;q=0.9',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Connection' => 'keep-alive',
                    'Upgrade-Insecure-Requests' => '1',
                ]
            ]);
            $html = (string) $response->getBody();

            Log::info('Respons HTTP diterima. Panjang konten: ' . strlen($html));

            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);

            Log::info('Mencari elemen nama toko dengan XPath');
            $shopNameElement = $xpath->query("//h1[@data-testid='shopNameHeader']")->item(0);

            if (!$shopNameElement) {
                Log::warning('Elemen nama toko tidak ditemukan. Mencoba XPath alternatif.');
                // Fallback menggunakan class yang mengandung css- pada tag h1
                $shopNameElement = $xpath->query("//h1[contains(@class, 'css-')]")->item(0);
                if (!$shopNameElement) {
                    throw new \Exception('Tidak dapat menemukan elemen nama toko.');
                }
            }

            $shopName = $shopNameElement->textContent;

            if (empty($shopName)) {
                throw new \Exception('Nama toko kosong.');
            }

            Log::info('Nama toko berhasil diekstrak: ' . $shopName);
            return trim($shopName);
        } catch (GuzzleException $e) {
            Log::error('Guzzle error: ' . $e->getMessage());
            throw new \Exception('Gagal mengambil halaman toko: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error saat parsing HTML: ' . $e->getMessage());
            // Log sebagian kecil dari HTML untuk debugging
            Log::debug('Potongan HTML: ' . substr($html, 0, 1000));
            throw new \Exception('Gagal mengekstrak nama toko dari halaman: ' . $e->getMessage());
        }
    }
}