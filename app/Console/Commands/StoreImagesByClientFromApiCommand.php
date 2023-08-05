<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\AddonNotInstalledException;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Models\ClientService;
use App\Models\Image;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Console\Command;
use Throwable;

class StoreImagesByClientFromApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:images {client_id?}';

    /**
     *
     * @var string
     */
    protected $description = 'Store images from API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientId = $this->argument('client_id');
        $success = true;
        $service = Service::find(Service::DYNAMIC_PREVIEW_IMAGES);

        for($i = 0; $i < $this->getMaxIterationCount(); $i++) {

            if ($clientId !== null) {
                $clientServices = ClientService::where('service_id', $service->getAttribute('id'))
                    ->where('status', ClientServiceStatusEnum::ACTIVE)
                    ->where('client_id', $clientId)
                    ->get();
            } else {
                $clientServices = ClientService::where('service_id', $service->getAttribute('id'))
                    ->where('status', ClientServiceStatusEnum::ACTIVE)
                    ->limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            }

            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                $client = $clientService->client()->first();
                $currentClientId = $client->getAttribute('id');
                $this->info('Updating images for client id:' . (string)$currentClientId);
                $products = Product::where('client_id', $currentClientId)->where('active', true)->get();
                foreach($products as $product) {
                    $productGuid = $product->getAttribute('guid');
                    $productId = $product->getAttribute('id');
                    try {
                        $imageResponses = ConnectorHelper::getProductImages($clientService, $productGuid);
                        $images = Image::where('client_id', $clientId)->where('product_id', $productId)->get();
                        foreach ($imageResponses as $imageResponse) {
                            $this->info('Updating image ' . $imageResponse->getName() . ' for product ' . $productGuid);
                            $imageExists = false;
                            foreach ($images as $key => $image) {
                                if ($image->getAttribute('name') === $imageResponse->getCdnName()) {
                                    unset($images[$key]);
                                    $imageExists = true;
                                    break;
                                }
                            }
                            if ($imageExists) {
                                continue;
                            }
                            $image = new Image();
                            $image->setAttribute('client_id', $currentClientId);
                            $image->setAttribute('product_id', $productId);
                            $image->setAttribute('name', $imageResponse->getSeoName());
                            $image->save();
                        }
                        foreach ($images as $image) {
                            Image::destroy($image->getAttribute('id'));
                        }
                    } catch (AddonNotInstalledException) {
                        $clientService->setAttribute('status', ClientServiceStatusEnum::INACTIVE);
                        $clientService->save();
                        break;
                    } catch (Throwable $t) {
                        $this->error('Error updating images ' . $t->getMessage());
                        LoggerHelper::log('Error updating images ' . $t->getMessage());
                        $success = false;
                        break;
                    }
                }
                $client->setAttribute('last_synced_at', now());
                $client->save();
            }
            
            if ($clientServices->count() < $this->getIterationCount()) {
                break;
            }
        }

        if ($success === true) {
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}