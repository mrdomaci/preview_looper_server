<?php

namespace App\Http\Controllers;

use App\Helpers\LoggerHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class CommandController extends Controller
{
    public function deploy(Request $request): Response
    {
        if ($request->get('token') !== $_ENV['DEPLOY_TOKEN']) {
            return Response('Unauthorized', 401);
        }
        try {
            $output = shell_exec('git pull');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('migrate');
            LoggerHelper::log('deploy-successful: ' . $output);
        } catch (Throwable $t) {
            LoggerHelper::log('deploy-failed: ' . $t->getMessage());
        }
        return Response('ok', 200);
    }
}
