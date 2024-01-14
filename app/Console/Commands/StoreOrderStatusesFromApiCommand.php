<?php

namespace App\Console\Commands;

use App\Connector\OrderStatusResponse;
use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\ApiRequestFailException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\GeneratorHelper;
use App\Helpers\LoggerHelper;
use App\Models\ClientService;
use App\Models\OrderStatus;
use App\Models\Service;
use Illuminate\Console\Command;
use Throwable;

class StoreOrderStatusesFromApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:order-statuses {client_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store order statuses from API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {        
        $clientId = $this->argument('client_id');
        $success = true;
        $service = Service::find(Service::UPSELL);

        for($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            if ($clientId !== null) {
                $clientServices = ClientService::where('service_id', $service->getAttribute('id'))
                    ->where('status', ClientServiceStatusEnum::ACTIVE)
                    ->where('client_id', $clientId)
                    ->limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            } else {
                $clientServices = ClientService::where('service_id', $service->getAttribute('id'))
                    ->where('status', ClientServiceStatusEnum::ACTIVE)
                    ->limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            }

            foreach ($clientServices as $clientService) {
                $currentClientId = $clientService->getAttribute('client_id');
                $clientService->setUpdateInProgress(true);
                $clientService->save();
                $orderStatuses = OrderStatus::where('client_id', $currentClientId)->get();
                try {
                    $orderStatusesResponse = GeneratorHelper::fetchOrderStatuses($clientService);

                    /** @var OrderStatusResponse $orderStatusResponse */
                    foreach ($orderStatusesResponse as $orderStatusResponse) {
                        $this->info('Updating order status ' . $orderStatusResponse->getName());
                        foreach ($orderStatuses as $key => $orderStatus) {
                            if ($orderStatus->getAttribute('foreign_id') === $orderStatusResponse->getId()) {
                                unset($orderStatuses[$key]);
                                break;
                            }
                        }
                        $orderStatus = OrderStatus::where('client_id', $currentClientId)->where('foreign_id', $orderStatusResponse->getId())->first();
                        if ($orderStatus === null) {
                            $orderStatus = new OrderStatus();
                            $orderStatus->setAttribute('client_id', $currentClientId);
                            $orderStatus->setAttribute('foreign_id', (string) $orderStatusResponse->getId());
                            $orderStatus->setAttribute('name', $orderStatusResponse->getName());
                            $orderStatus->setAttribute('system', $orderStatusResponse->isSystem());
                            $orderStatus->setAttribute('order', $orderStatusResponse->getOrder());
                            $orderStatus->setAttribute('mark_as_paid', $orderStatusResponse->isMarkAsPaid());
                            $orderStatus->setAttribute('color', $orderStatusResponse->getColor());
                            $orderStatus->setAttribute('background_color', $orderStatusResponse->getBackgroundColor());
                            $orderStatus->setAttribute('change_order_items', $orderStatusResponse->isChangeOrderItems());
                            $orderStatus->setAttribute('stock_claim_resolved', $orderStatusResponse->isStockClaimResolved());
                            $orderStatus->save();
                        } else {
                            $orderStatus->setAttribute('name', $orderStatusResponse->getName());
                            $orderStatus->setAttribute('system', $orderStatusResponse->isSystem());
                            $orderStatus->setAttribute('order', $orderStatusResponse->getOrder());
                            $orderStatus->setAttribute('mark_as_paid', $orderStatusResponse->isMarkAsPaid());
                            $orderStatus->setAttribute('color', $orderStatusResponse->getColor());
                            $orderStatus->setAttribute('background_color', $orderStatusResponse->getBackgroundColor());
                            $orderStatus->setAttribute('change_order_items', $orderStatusResponse->isChangeOrderItems());
                            $orderStatus->setAttribute('stock_claim_resolved', $orderStatusResponse->isStockClaimResolved());
                            $orderStatus->save();
                        }
                    }
                    foreach ($orderStatuses as $orderStatus) {
                        $orderStatus->delete();
                    }
                } catch (ApiRequestFailException) {
                    $clientService->setAttribute('status', ClientServiceStatusEnum::INACTIVE);
                    $clientService->setUpdateInProgress(false);
                    $clientService->save();
                    break;
                } catch (ApiRequestTooManyRequestsException) {
                    sleep(10);
                    continue;
                } catch (Throwable $t) {
                    $this->error('Error updating order statuses ' . $t->getMessage());
                    LoggerHelper::log('Error updating order statuses ' . $t->getMessage());
                    $success = false;
                    break;
                }
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
