<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class TokopediaScraper
{
    /**
     * Ambil data toko (daftar produk, nama toko, link halaman berikutnya/sebelumnya)
     * untuk sebuah kata kunci pencarian.
     */
    public function fetchShopData(string $shopUrl, string $keyword, int $page = 1, string $sort = '23'): array
    {
        $url = $this->buildSearchUrl($shopUrl, $keyword, $page, $sort);
        $html = $this->getHtmlContent($url);
        $crawler = new Crawler($html);

        return [
            'products' => $this->extractProducts($crawler, $html),
            'shop_name' => $this->extractShopName($crawler),
            'next_page' => $this->extractPageLink($crawler, 'btnShopProductPageNext'),
            'prev_page' => $this->extractPageLink($crawler, 'btnShopProductPagePrevious'),
        ];
    }

    /**
     * Ambil harga termurah (dalam angka) dari daftar produk hasil scraping.
     */
    public function cheapestPrice(array $products): ?int
    {
        $prices = collect($products)
            ->map(fn ($product) => $this->parsePrice($product['price'] ?? null))
            ->filter(fn ($price) => $price !== null);

        return $prices->isEmpty() ? null : $prices->min();
    }

    public function parsePrice(?string $price): ?int
    {
        if (! $price) {
            return null;
        }

        $numeric = preg_replace('/[^0-9]/', '', $price);

        return $numeric === '' ? null : (int) $numeric;
    }

    private function buildSearchUrl(string $shop_url, string $keyword, int $page, string $sort): string
    {
        $parsed_url = parse_url($shop_url);
        $shop_name = explode('/', trim($parsed_url['path'], '/'))[0];

        // Check if the shop has a "Beranda" page
        $has_beranda = $this->checkForBeranda($shop_url);

        $page_segment = $page > 1 ? "page/{$page}" : '';
        $product_segment = $has_beranda ? 'product/' : '';

        return "https://www.tokopedia.com/{$shop_name}/{$product_segment}{$page_segment}?q=".urlencode($keyword)."&sort={$sort}";
    }

    private function checkForBeranda(string $shop_url): bool
    {
        $html = $this->getHtmlContent($shop_url);
        $crawler = new Crawler($html);

        $beranda_button = $crawler->filter('[data-testid="Beranda"]');

        return $beranda_button->count() > 0;
    }

    private function getHtmlContent(string $url): string
    {
        try {
            $client = new \GuzzleHttp\Client([
                'verify' => false,
                'timeout' => 15,
            ]);
            $response = $client->get($url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                ],
            ]);

            return (string) $response->getBody();
        } catch (\Exception $e) {
            Log::error('Error fetching HTML with Guzzle: '.$url.' - '.$e->getMessage());

            return '';
        }
    }

    private function extractProducts(Crawler $crawler, string $html): array
    {
        $products = [];

        // Ekstrak original image url dari json state Tokopedia
        preg_match_all('/primary_image":{"original":"([^"]+)"/', $html, $imageMatches);
        $realImages = $imageMatches[1] ?? [];
        $imageIndex = 0;

        $crawler->filter('a')->each(function (Crawler $node) use (&$products, $realImages, &$imageIndex) {
            if (count($products) >= 80) {
                return false;
            }

            // Pastikan ini adalah link produk dengan mengecek adanya gambar produk
            $imgNode = $node->filter('img[alt="product-image"]');
            if ($imgNode->count() === 0) {
                return;
            }

            $title = 'N/A';
            $node->filter('span')->each(function (Crawler $span) use (&$title) {
                $text = trim($span->text());
                if ($title === 'N/A' && $text !== '') {
                    $title = $text;
                }
            });

            $price = 'N/A';
            if (preg_match('/Rp\s*[\d.,]+/', $node->text(), $matches)) {
                $price = $matches[0];
            }

            // Ambil gambar asli dari JSON Cache sesuai index, jika tidak ada fallback ke src DOM
            $image = $realImages[$imageIndex] ?? $imgNode->attr('src');
            $imageIndex++;

            $link = $node->attr('href');

            // Ekstrak rating dan terjual dari keseluruhan teks node
            $rating = 'Belum ada rating';
            if (preg_match('/(\d+(\.\d+)?)\s*Bintang/', $node->text(), $matches)) {
                $rating = $matches[0];
            } elseif (preg_match('/(\d+(\.\d+)?)\s*rating/i', $node->text(), $matches)) {
                $rating = $matches[0];
            }

            $sold = 'Belum terjual';
            if (preg_match('/Terjual\s+[\d+.,]+/i', $node->text(), $matches) || preg_match('/[\d+.,]+\s+terjual/i', $node->text(), $matches)) {
                $sold = $matches[0];
            }

            $products[] = [
                'title' => $title,
                'price' => $price,
                'image' => $image,
                'link' => $link,
                'rating' => $rating,
                'sold' => $sold,
            ];
        });

        return $products;
    }

    private function extractShopName(Crawler $crawler): string
    {
        return $crawler->filter('[data-testid="shopNameHeader"]')->text('Nama Toko');
    }

    private function extractPageLink(Crawler $crawler, string $buttonTestId): ?string
    {
        try {
            $link = $crawler->filter("[data-testid=\"{$buttonTestId}\"]")->attr('href');

            return $link ? 'https://www.tokopedia.com'.$link : null;
        } catch (\InvalidArgumentException $e) {
            // If the element is not found, return null
            return null;
        }
    }
}
