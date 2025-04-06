<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Scramble::registerApi('admin', ['api_path' => 'admin/'])
            ->afterOpenApiGenerated(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
        });

        Scramble::registerApi('customer', ['api_path' => 'customer/'])
            ->expose(
                ui: '/docs/customer',
                document: '/docs/customer/openapi.json',
            )
            ->afterOpenApiGenerated(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });

        // tokenization part
        Scramble::registerApi('admin.tokenization', ['api_path' => 'admin/tokenization/'])
            ->expose(
                ui: '/docs/admin/tokenization',
                document: '/docs/admin/tokenization/openapi.json',
            )
            ->afterOpenApiGenerated(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });


        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });
    }
}
