<?php

namespace App\Traits;

trait RestfulTrait
{
    public function apiResponse($data = null, $code = 200, $message = null, $paginate = null)
    {
        $arrayResponse = [
            'data' => $data,
            'status' => $code == 200 || $code == 201 || $code == 204 || $code == 205,
            'message' => $message,
            'code' => $code,
            'paginate' => $paginate,
        ];
        return response($arrayResponse, $code);
    }

    public function formatPaginateData($data)
    {
        if($data==null)
            return null;
        $paginated_arr = $data->toArray();
        return $paginateData = [
            'currentPage'   => $paginated_arr['current_page'],
            'from'          => $paginated_arr['from'],
            'to'            => $paginated_arr['to'],
            'total'         => $paginated_arr['total'],
            'total_pages'   => ceil($paginated_arr['total'] / $paginated_arr['per_page']),
            'per_page'      => (int)$paginated_arr['per_page'],
        ];
    }
}
