<?php

namespace App\Exceptions;

use DomainException;

/**
 * Violation d'une règle métier (stock insuffisant, lot déjà clôturé…).
 * Rendue en 422 JSON pour l'API, ou en message d'erreur de formulaire pour le web
 * (voir bootstrap/app.php).
 */
class RegleMetierException extends DomainException
{
}
