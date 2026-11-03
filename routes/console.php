<?php

use Illuminate\Support\Facades\Schedule;

// Alerte quotidienne : récoltes non affectées à un lot depuis plus de 3 jours.
Schedule::command('recoltes:alerter --jours=3')->dailyAt('07:00');
