<?php

namespace Potager\Grape\Enums;

enum UnknownPropertiesStrategy
{
    case Keep;
    case Discard;
    case Reject;
}