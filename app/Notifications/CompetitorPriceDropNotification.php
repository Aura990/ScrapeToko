<?php

namespace App\Notifications;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompetitorPriceDropNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $keyword,
        public Shop $managedShop,
        public Shop $competitorShop,
        public int $managedPrice,
        public int $competitorPrice,
    ) {
    }

    /**
     * Channel notifikasi. Tambahkan 'mail' aktif secara default; jika MAIL_MAILER
     * belum dikonfigurasi di .env, channel database tetap akan tersimpan dan tampil di lonceng notifikasi.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $selisih = $this->managedPrice - $this->competitorPrice;
        $persentase = $this->managedPrice > 0 ? round(($selisih / $this->managedPrice) * 100, 1) : 0;

        return (new MailMessage)
            ->subject('⚠️ Harga Kompetitor Lebih Murah — "'.$this->keyword.'"')
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Sistem mendeteksi bahwa toko saingan menjual produk dengan kata kunci "'.$this->keyword.'" lebih murah dibanding toko yang Anda kelola.')
            ->line('**Toko Dikelola:** '.$this->managedShop->name.' — Rp '.number_format($this->managedPrice, 0, ',', '.'))
            ->line('**Toko Saingan:** '.$this->competitorShop->name.' — Rp '.number_format($this->competitorPrice, 0, ',', '.'))
            ->line('**Selisih:** Rp '.number_format($selisih, 0, ',', '.').' ('.$persentase.'% lebih murah)')
            ->action('Lihat Perbandingan', url(route('comparison.index')))
            ->line('Segera tinjau strategi harga Anda jika diperlukan.');
    }

    public function toArray(object $notifiable): array
    {
        $selisih = $this->managedPrice - $this->competitorPrice;

        return [
            'type' => 'competitor_price_drop',
            'keyword' => $this->keyword,
            'managed_shop_id' => $this->managedShop->id,
            'managed_shop_name' => $this->managedShop->name,
            'competitor_shop_id' => $this->competitorShop->id,
            'competitor_shop_name' => $this->competitorShop->name,
            'managed_price' => $this->managedPrice,
            'competitor_price' => $this->competitorPrice,
            'difference' => $selisih,
            'message' => 'Toko "'.$this->competitorShop->name.'" menjual "'.$this->keyword.'" lebih murah Rp '.number_format($selisih, 0, ',', '.').' dari toko "'.$this->managedShop->name.'".',
        ];
    }
}
