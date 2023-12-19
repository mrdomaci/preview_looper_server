<?php

namespace App\Console\Commands;

use App\Helpers\ImageHelper;
use App\Models\Advert;
use Illuminate\Console\Command;

class GenerateAdvertImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:advert:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate advert images';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('[START] Generating advert images');
        $adverts = Advert::get();

        foreach ($adverts as $advert) {
            ImageHelper::generateProductImage($advert);
            dd('done');
        }
    
        return Command::SUCCESS;
    }
}
