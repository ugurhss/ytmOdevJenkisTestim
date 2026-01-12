<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dusk Environment File
    |--------------------------------------------------------------------------
    |
    | This is the environment file that Dusk will use when running tests. By
    | default, Dusk will use the ".env.dusk.testing" file. You can override
    | this by setting the DUSK_ENV environment variable.
    |
    */

    'env' => env('DUSK_ENV', 'dusk.testing'),

    /*
    |--------------------------------------------------------------------------
    | Dusk Driver URL
    |--------------------------------------------------------------------------
    |
    | This is the URL of the Selenium WebDriver server. By default, Dusk will
    | use "http://localhost:9515" for ChromeDriver. You can override this by
    | setting the DUSK_DRIVER_URL environment variable.
    |
    */

    'driver_url' => env('DUSK_DRIVER_URL', 'http://localhost:9515'),

    /*
    |--------------------------------------------------------------------------
    | Dusk Screenshot Path
    |--------------------------------------------------------------------------
    |
    | This is the path where Dusk will save screenshots when tests fail. By
    | default, Dusk will save screenshots in the "tests/Browser/screenshots"
    | directory. You can override this by setting the DUSK_SCREENSHOT_PATH
    | environment variable.
    |
    */

    'screenshot_path' => env('DUSK_SCREENSHOT_PATH', 'tests/Browser/screenshots'),

    /*
    |--------------------------------------------------------------------------
    | Dusk Console Log Path
    |--------------------------------------------------------------------------
    |
    | This is the path where Dusk will save console logs when tests fail. By
    | default, Dusk will save console logs in the "tests/Browser/console"
    | directory. You can override this by setting the DUSK_CONSOLE_LOG_PATH
    | environment variable.
    |
    */

    'console_log_path' => env('DUSK_CONSOLE_LOG_PATH', 'tests/Browser/console'),

];
