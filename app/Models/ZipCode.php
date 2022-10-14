<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;


class ZipCode extends Model
{
    /**
     * @var string[]
     */
    protected $hidden = [
        'd_CP',
        'c_oficina',
        'c_tipo_asenta',
        'c_cve_ciudad'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'zip_code' => 'string',
        'settlement_name' => 'string',
        'settlement_type' => 'object',
        'municipality_name' => 'string',
        'federal_entity_name' => 'string',
        'locality' => 'string',
        'federal_entity_key' => 'integer',
        'municipality_key' => 'integer',
        'settlement_key' => 'integer',
        'settlement_zone_type' => 'string'
    ];

    /**
     * @param string $string
     * @return string
     */
    public static function formatString(string $string){
        return strtoupper(strtr($string, [
            'Á'=>'A', 'É'=>'E', 'Í'=>'I', 'Ñ'=>'N', 'Ó'=>'O', 'Ú'=>'U',
            'á'=>'a', 'é'=>'e', 'í'=>'i', 'ñ'=>'n', 'ó'=>'o', 'ú'=>'u'
        ]));
    }

    /**
     * Group into array the property values which have same prefix name
     *
     * @param string $pfx
     * @param $properties
     * @param string $separator
     * @return Array
     */
    public function groupValuesByPrefix(string $pfx, $properties, string $separator = '_'): Array
    {
        // $properties must be an array
        $properties = !is_array($properties)?: Arr::wrap($properties);
        /** @var Array $response */
        $response = [];
        foreach ($properties as $ndx => $property){
            $val = $this->getAttribute($pfx . $separator . $property);
            // strings should be uppercase and remove the accents
            $response[$property] = !is_string($val)? $val: self::formatString($val);
        }
        return $response;
    }

    /**
     * Accessors
     */
    protected function federalEntity(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->groupValuesByPrefix('federal_entity', ['key', 'name', 'code'])
        );
    }

    public function municipality(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->groupValuesByPrefix('settlement', ['key', 'name'])
        );
    }

    public function settlement(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->groupValuesByPrefix('settlement', ['key', 'name', 'zone_type', 'settlement_type'])
        );
    }

    public function settlementType(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ['name' => $value]
        );
    }

    /**
     * @param string $zipCode
     * @return Array
     */
    public static function getByZipCode(string $zipCode): Array{
        // get the records
        /** @var Collection $zipCodeCollection */
        $zipCodeCollection = self::where('zip_code', $zipCode)->get();

        // if it's empty abort
        if($zipCodeCollection->isEmpty()){
            abort(404, 'Zip code not found');
        }

        // get the settlements into array
        /** @var Array $settlements */
        $settlements = [];
        $zipCodeCollection->each(function (ZipCode $zipCode, $key) use (&$settlements){
            //
            $settlements[]= $zipCode->settlement;
        });

        /** @var ZipCode $first */
        $first = $zipCodeCollection->first();
        return [
            'zip_code' => $first->zip_code,
            'locality' => self::formatString($first->locality),
            'federal_entity' => $first->federal_entity,
            'settlements' => $settlements,
            'municipality' => $first->municipality
        ];
    }
}
