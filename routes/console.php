<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('tenants:migrate')->hourly();
