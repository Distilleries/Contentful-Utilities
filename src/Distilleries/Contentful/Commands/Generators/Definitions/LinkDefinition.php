<?php

namespace Distilleries\Contentful\Commands\Generators\Definitions;

use Exception;
use App\Eloquent;

class LinkDefinition extends BaseDefinition
{
    /**
     * {@inheritdoc}
     */
    public function modelGetter()
    {
        switch ($this->field['linkType']) {
            case 'Entry':
                $stubPath = __DIR__ . '/stubs/entry.stub';
                break;
            case 'Asset':
                $stubPath = __DIR__ . '/stubs/asset.stub';
                break;
            default:
                throw new Exception('Unknown Array items type "' . $this->field['linkType'] . '"');
        }

        return self::getStub($stubPath, [
            'field_camel' => studly_case($this->id()),
            'field' => $this->id(),
        ]);
    }

}