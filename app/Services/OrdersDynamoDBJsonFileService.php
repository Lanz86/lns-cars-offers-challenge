<?php

use \Aws\DynamoDb\Marshaler;

namespace App\Services;

class OrdersDynamoDBJsonFileService implements OrdersServicesInterface
{

    private $items = null;

    public function __construct()
    {
        $this->items = $this->loadAndNormalizeItems();
    }

    public function getAllVisibleItems()
    {
        $visibleItems = array_filter($this->items, function($item) {
            return $item['visible'] == true;
        });

        usort($visibleItems, function($item1, $item2) {
            return $item1['pricing']['price'] - $item2['pricing']['price'];
        });

        return $visibleItems;
    }


    public function getItemById($id)
    {
        $item = array_filter($this->items, function($item) use($id) {
            return $item['id'] == $id;
        });

        return empty($item) ? null : $item[0];
    }

    private function loadAndNormalizeItems()
    {
        $dbDumpFileName = env('DB_DYNAMODB_JSON_DUMP');

        if(file_exists($dbDumpFileName) == 1) {
            $marshaler = new \Aws\DynamoDb\Marshaler();
            $fileContent = file_get_contents($dbDumpFileName);

            $decodedFile = json_decode(utf8_encode($fileContent), true);

            foreach ($decodedFile["Items"] as $itemKey => $item) {
                foreach ($item as $valueItemKey => $value) {
                    $values[$valueItemKey]= $marshaler->unmarshalValue($value);
                }
                $items[] = $values;
            }

            return $items;
        }
        else
        {
            throw new \Exception("DB file in path '.$dbDumpFileName.' not found!");
        }
    }
}
