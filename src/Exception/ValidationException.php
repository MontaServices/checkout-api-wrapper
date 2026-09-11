<?php

namespace Monta\CheckoutApiWrapper\Exception;

/**
 * Thrown by Option::validate() when a selected shipping/pickup Option does not
 * match the server-side validation cache (unknown code, or a price mismatch).
 */
class ValidationException extends \RuntimeException
{
}
