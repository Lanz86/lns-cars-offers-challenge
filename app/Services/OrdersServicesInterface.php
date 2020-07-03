<?php
    namespace App\Services;

    interface OrdersServicesInterface {
        public function getAllVisibleItems();
        public function getItemById($id);
    }
