<?php

use \Aws\DynamoDb\Marshaler;
use Illuminate\Http\Request;

namespace App\Services;

class OrdersDynamoDBJsonFileService implements OrdersServicesInterface
{

    private $items = null;

    public function __construct()
    {
        $this->items = $this->loadAndNormalizeItems();
    }

    private function getArrayPathByDotNotation($array, $path, $separator)
    {
        $keys = explode($separator, $path);

        foreach ($keys as $key) {
            $array = $array[$key];
        }

        return $array;
    }

    public function getAllVisibleItems(\Illuminate\Http\Request $request)
    {

        $orderBy = "pricing.price";

        $minPriceFilter = $request->query('minPrice');
        $maxPriceFilter = $request->query('maxPrice');


        $visibleItems = array_filter($this->items, function($item)  use($minPriceFilter, $maxPriceFilter) {
            return $item['visible'] == true &&
                    (
                      ($minPriceFilter == null || $minPriceFilter <= $item['pricing']['price']) &&
                      ($maxPriceFilter == null || $maxPriceFilter <= $item['pricing']['price'])
                    );
        });

        usort($visibleItems, function($item1, $item2) use ($orderBy) {
            return $this->getArrayPathByDotNotation($item1, $orderBy, '.') - $this->getArrayPathByDotNotation($item2, $orderBy, '.');
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
