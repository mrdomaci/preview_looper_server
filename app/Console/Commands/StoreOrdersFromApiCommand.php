<?php

namespace App\Console\Commands;

use App\Connector\OrderResponse;
use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\ApiRequestFailException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\ConnectorHelper;
use App\Helpers\GeneratorHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Models\ClientService;
use App\Models\ClientSettingsServiceOption;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Service;
use App\Models\SettingsService;
use Illuminate\Console\Command;
use Throwable;

class StoreOrdersFromApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:orders {client_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store orders from API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {        
        $clientId = $this->argument('client_id');
        $success = true;
        $service = Service::find(Service::ORDER_STATUS);
        $serviceId = (int) $service->getAttribute('id');
        $dateNow = new \DateTime();
        $this->info('Updating orders');

        for($i = 0; $i < $this->getMaxIterationCount(); $i++) {

            if ($clientId !== null) {
                $clientServices = ClientService::where('service_id', $serviceId)
                    ->where('status', ClientServiceStatusEnum::ACTIVE)
                    ->where('client_id', $clientId)
                    ->limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            } else {
                $clientServices = ClientService::where('service_id', $serviceId)
                    ->where('status', ClientServiceStatusEnum::ACTIVE)
                    ->limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            }

            foreach ($clientServices as $clientService) {
                $currentClientId = $clientService->getAttribute('client_id');
                if ($clientService->getAttribute('update_in_progress') === true) {
                    continue;
                }

                $clientService->setUpdateInProgress(true);
                $clientService->save();

                $dateLastSynced = $clientService->getAttribute('date_last_synced');

                if ($dateLastSynced !== null) {
                    $dateLastSynced = new \DateTime($dateLastSynced);
                }
                $clientSettingsServiceOption = ClientSettingsServiceOption::where('client_id', $currentClientId)->whereNotNull('settings_service_option_id')->get();
                if (count($clientSettingsServiceOption) === 0) {
                    $clientService->setUpdateInProgress(false);
                    $clientService->save();
                    continue;
                }

                for ($page = 1; $page < ResponseHelper::MAXIMUM_ITERATIONS; $page++) {
                    try {
                        $orderListResponse = ConnectorHelper::getOrders($clientService, $page, $dateLastSynced);
                        if ($orderListResponse === null) {
                            break;
                        }
                        /** @var OrderResponse $orderResponse */
                        foreach (GeneratorHelper::fetchOrders($clientService, $page, $dateLastSynced) as $orderResponse) {
                            $this->info('Updating order ' . $orderResponse->getGuid());

                            $order = Order::where('client_id', $currentClientId)->where('guid', $orderResponse->getGuid())->first();
                            if ($order === null) {
                                $order = new Order();
                            }
                            $orderStatus = OrderStatus::where('client_id', $currentClientId)->where('foreign_id', $orderResponse->getForeignStatusId())->first();
                            if ($orderStatus !== null) {
                                $clientSettingsServiceOption = ClientSettingsServiceOption::where('client_id', $currentClientId)->where('settings_service_option_id', $orderStatus->getAttribute('id'))->first();
                                if ($clientSettingsServiceOption !== null) {
                                    $settingsService = SettingsService::where('id', $clientSettingsServiceOption->getAttribute('settings_service_id'))->first();
                                    if ($settingsService !== null) {
                                        $order->setAttribute('status', $settingsService->getAttribute('name'));
                                    }
                                }
                            }                           
                            $order->setAttribute('client_id', $currentClientId);
                            $order->setAttribute('guid', $orderResponse->getGuid());
                            $order->setAttribute('code', $orderResponse->getCode());
                            $order->setAttribute('created_at', $orderResponse->getCreationTime());
                            $order->setAttribute('updated_at', $orderResponse->getChangeTime());
                            $order->setAttribute('full_name', $orderResponse->getFullName());
                            $order->setAttribute('company', $orderResponse->getCompany());
                            $order->setAttribute('email', $orderResponse->getEmail());
                            $order->setAttribute('phone', $orderResponse->getPhone());
                            $order->setAttribute('remark', $orderResponse->getRemark());
                            $order->setAttribute('cash_desk_order', $orderResponse->isCashDeskOrder());
                            $order->setAttribute('customer_guid', $orderResponse->getCustomerGuid());
                            $order->setAttribute('paid', $orderResponse->isPaid());
                            $order->setAttribute('foreign_status_id', $orderResponse->getForeignStatusId());
                            $order->setAttribute('source', $orderResponse->getSource());
                            $order->setAttribute('vat', $orderResponse->getPrice()->getVat());
                            $order->setAttribute('to_pay', $orderResponse->getPrice()->getToPay());
                            $order->setAttribute('currency_code', $orderResponse->getPrice()->getCurrencyCode());
                            $order->setAttribute('with_vat', $orderResponse->getPrice()->getWithVat());
                            $order->setAttribute('without_vat', $orderResponse->getPrice()->getWithoutVat());
                            $order->setAttribute('exchange_rate', $orderResponse->getPrice()->getExchangeRate());
                            $order->setAttribute('payment_method', $orderResponse->getPaymentMethod()?->getGuid());
                            $order->setAttribute('shipping', $orderResponse->getShipping()?->getGuid());
                            $order->setAttribute('admin_url', $orderResponse->getAdminUrl());
                            $order->save();

                            foreach (GeneratorHelper::fetchOrderDetail($clientService, $orderResponse->getCode()) as $orderDetailResponse) {
                                $product = Product::where('client_id', $currentClientId)->where('guid', $orderDetailResponse->getProductGuid())->first();
                                for ($j = 1; $j <= (int) $orderDetailResponse->getAmount(); $j++) {
                                    $orderProducts = OrderProduct::where('client_id', $currentClientId)->where('order_guid', $orderResponse->getGuid())->where('product_guid', $orderDetailResponse->getProductGuid())->get();
                                    $orderProducts->delete();
                                    
                                    $orderProduct = new OrderProduct();
                                    $orderProduct->setAttribute('client_id', $currentClientId);
                                    $orderProduct->setAttribute('order_id', $order->getAttribute('id'));
                                    $orderProduct->setAttribute('order_guid', $orderResponse->getGuid());
                                    $orderProduct->setAttribute('product_guid', $orderDetailResponse->getProductGuid());
                                    if ($product !== null) {
                                        $orderProduct->setAttribute('product_id', $product->getAttribute('id'));
                                    }
                                    $orderProduct->save();
                                }
                            }
                        }
                        if ($orderListResponse->getPage() === $orderListResponse->getPageCount()) {
                            break;
                        }
                    } catch (ApiRequestFailException) {
                        $clientService->setAttribute('status', ClientServiceStatusEnum::INACTIVE);
                        $clientService->setUpdateInProgress(false);
                        $clientService->save();
                        break;
                    } catch (ApiRequestTooManyRequestsException) {
                        sleep(10);
                        $page--;
                        continue;
                    } catch (Throwable $t) {
                        $this->error('Error updating orders ' . $t->getMessage());
                        LoggerHelper::log('Error updating orders ' . $t->getMessage());
                        $success = false;
                        break;
                    }
                }

                $clientService->setUpdateInProgress(false);
                $clientService->setAttribute('date_last_synced', $dateNow);
                $clientService->save();
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
