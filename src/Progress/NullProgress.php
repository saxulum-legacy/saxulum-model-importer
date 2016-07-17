<?php

namespace Saxulum\ModelImporter\Progress;

class NullProgress implements ProgressInterface
{
    public function advance()
    {
    }
}
