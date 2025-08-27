<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class CopyProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:copy-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DBの画像パスをpublic/images/にコピーする';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = Product::all();

        foreach ($products as $product) {
            if ($product->image) {
                // storage/app/public 配下の画像パスを取得
                $source = storage_path('app/public/' . $product->image);
                $destination = public_path('images/' . basename($product->image));

                if (file_exists($source)) {
                    copy($source, $destination);
                    $this->info("Copied: {$product->image} → images/" . basename($product->image));
                } else {
                    $this->warn("Skipped: {$product->image} (file not found in storage/app/public)");
                }
            } else {
                $this->warn("Skipped: {$product->image} (empty path)");
            }
        }

        $this->info('All done!');
        return 0;
    }
}
