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

        $retValue = $this->items->filter(function($value, $key) use($minPriceFilter, $maxPriceFilter)  {

            return $value['visible'] == true &&
            (
              ($minPriceFilter == null || $minPriceFilter <= $value['pricing']['price']) &&
              ($maxPriceFilter == null || $maxPriceFilter <= $value['pricing']['price'])
            );
        });

        $retValue->sortBy(function($item, $key) use ($orderBy) {
            return  $this->getArrayPathByDotNotation($item, $orderBy, '.');
        });

        return $retValue;
    }


    public function getItemById($id)
    {
        $item = $this->items->first(function($value, $key) use($id) {
            return $value['id'] == $id;
        });

        return $item;
    }

    private function loadAndNormalizeItems()
    {
        $dbDumpFileName = env('DB_DYNAMODB_JSON_DUMP');

        if(file_exists($dbDumpFileName) == 1) {
            $marshaler = new \Aws\DynamoDb\Marshaler();
            $fileContent = file_get_contents($dbDumpFileName);

            $decodedFile = json_decode(utf8_encode($fileContent), true);

            $collection = collect($decodedFile["Items"]);

            $items = $collection->map(function($item, $key) use($marshaler) {
                return collect($item)->map(function($value, $keyValue) use($marshaler) {
                    return $marshaler->unmarshalValue($value);
                });
            });

            return $items;
        }
        else
        {
            throw new \Exception("DB file in path '.$dbDumpFileName.' not found!");
        }
    }
}
