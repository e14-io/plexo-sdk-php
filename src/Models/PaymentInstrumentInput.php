<?php
namespace Plexo\Sdk\Models;

use Plexo\Sdk\Type;

class PaymentInstrumentInput extends ModelsBase
{
    /**
     * @var string 
     */
    // public $InstrumentToken;
    
    /**
     * @var Dictionary<FieldType,string> Optional
     */
    // public $NonStorableItems;

    /**
     * @var bool (required)
     */
    // public $UseExtendedClientCreditIfAvailable = false;

    protected $data = [
        'InstrumentToken' => null,
        'NonStorableItems' => [],
        'UseExtendedClientCreditIfAvailable' => false,
    ];

    /**
     * @param array $params
     */
    public function __construct(array $params = []) {
        foreach ($params as $k => $v) {
            $this->{$k} = $v;
        }
    }

    public static function getValidationMetadata()
    {
        return [
            'InstrumentToken' => [
                'type' => 'string',
                'required' => true,
            ],
            'NonStorableItems' => [
                'type' => 'array',
                'required' => false,
            ],
            'UseExtendedClientCreditIfAvailable' => [
                'type' => 'bool',
                'required' => true,
            ],
        ];
    }

    public function addNonStorableItems($value, $k = null)
    {
        array_push($this->data['NonStorableItems'], ($value instanceof Type\FieldType ? $value : new Type\FieldType($k, $value)));
        return $this;
    }

    public function setNonStorableItems(array $value)
    {
        $this->data['NonStorableItems'] = [];
        foreach ($value as $k => $item) {
            $this->addNonStorableItems($item, $k);
        }
        return $this;
    }

    public function nonStorableItemsToArray()
    {
        if (count($this->data['NonStorableItems']) === 0) {
            return null;
        }
        $hash = [];
        foreach ($this->data['NonStorableItems'] as $item) {
            $hash[$item->getParamName()] = $item->getValue();
        }
        ksort($hash);
        return $hash;
    }

    public function toArray($canonize = false)
    {
        return [
            'InstrumentToken' => $this->data['InstrumentToken'],
            'NonStorableItems' => $this->nonStorableItemsToArray(),
            'UseExtendedClientCreditIfAvailable' => $this->data['UseExtendedClientCreditIfAvailable'],
        ];
    }
}
