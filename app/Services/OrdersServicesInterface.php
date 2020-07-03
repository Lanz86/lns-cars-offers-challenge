<?php
    namespace App\Services;

    use Illuminate\Http\Request;

    interface OrdersServicesInterface {
        public function getAllVisibleItems(Request $request);
        public function getItemById($id);
    }
