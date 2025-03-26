<?php

namespace App\Http\Middleware\Admin;

use App\Enums\General\StatusCodeEnum;
use App\Traits\RestfulTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerManagement
{
    use RestfulTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if($user->hasRole('admin')){

            return $next($request);
        }
        return   $this->apiResponse(null, StatusCodeEnum::STATUS_FORBIDDEN, "Action Not Allowed!");
    }
}
