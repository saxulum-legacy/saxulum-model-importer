<?php

namespace Saxulum\ModelImporter;

class NotImportableException extends \Exception
{
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_PERSIST = 'persist';

    /**
     * @param string          $action
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($action, $code = 0, \Exception $previous = null)
    {
        parent::__construct(
            sprintf('Model with identifier {identifier} is not importable, cause %s failed', $action),
            $code,
            $previous
        );
    }
}
