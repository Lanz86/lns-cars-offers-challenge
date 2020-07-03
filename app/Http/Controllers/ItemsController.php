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

    function index()
    {
        return response()->json($this->ordersService->getAllVisibleItems(), 200);
    }

    function show($id) {
        $item = $this->ordersService->getItemById($id);
        if($item == null) return response()->json("Not Found!", 404);

        return response()->json($item, 200);
    }

}
