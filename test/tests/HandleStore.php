<?php

namespace concrete5\HandleManager\Test;

use concrete5\HandleManager\HandleStore as CoreHandleStore;

class HandleStore extends CoreHandleStore
{
    public function isSystemIdentifierValid($systemIdentifier)
    {
        return parent::isSystemIdentifierValid($systemIdentifier);
    }
}
