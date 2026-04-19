<?php
    use App\Models\News;
    use Illuminate\Console\Command;

    class UnpinExpiredNews extends Command
    {
        protected $signature = 'news:unpin-expired';
        protected $description = 'Nullkan pinned_at untuk berita yang pin_expired_at nya sudah lewat';

        public function handle(): void
        {
            $count = News::whereNotNull('pinned_at')
                ->whereNotNull('pin_expired_at')
                ->where('pin_expired_at', '<', now())
                ->update([
                    'pinned_at' => null,
                    'pin_expired_at' => null,
                ]);

            $this->info("Berhasil unpin {$count} berita yang pinnya sudah expired.");
        }
    }
?>