<?php

declare(strict_types = 1);

namespace DigitalCreative\Jaqen\Fields;

class SelectField extends AbstractField
{
    public function options(array $options): self
    {
        return $this->withAdditionalInformation($options);
    }
}
