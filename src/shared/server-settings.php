<?php

    function load_setting($setting_name): str {
        $result = getenv($setting_name);

        if ($result === false) {
            throw new Exception(
                "Undefined environment variable: " . $setting_name, 1);
            
        }

        return $result;
    }

    define("C_DB_NAME", load_setting("MYSQL_DB_NAME"));
    define("C_DB_USER_NAME", load_setting("MYSQL_DB_USER"));
    define("C_DB_PASSWORD", load_setting("MYSQL_DB_PASS"));    
    define("C_DEBUG", load_setting("ENV_TYPE") == "DEBUG");
    define("C_DB_HOST", load_setting("MYSQL_DB_HOST") 

?>