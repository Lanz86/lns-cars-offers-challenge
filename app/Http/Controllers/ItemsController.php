<?php

namespace App\Http\Controllers;
use Aws\DynamoDb\Marshaler;
use Illuminate\Http\Request;
use App\Services\OrdersServicesInterface;
use phpDocumentor\Reflection\Types\Array_;
use App\Http\Resources\Item;

class ItemsController extends Controller
{

    private $ordersService;

    public function __construct(OrdersServicesInterface $_ordersService)
    {
        $this->ordersService = $_ordersService;
    }

    /**
     * Returns all visible cars item. By default ordered by price
     * @return \Illuminate\Http\JsonResponse
     */
    function index(Request $request)
    {
        return response()->json($this->ordersService->getAllVisibleItems($request), 200);
    }

    /**
     * Returns a secified car by Id
     * @return \Illuminate\Http\JsonResponse
     */
    function show($id) {
        $item = $this->ordersService->getItemById($id);
        if($item == null) return response()->json("Not Found!", 404);

        return response()->json($item, 200);
    }

}
