<?php
class Env
{
    public static function load(string $path): void
    {
        if (!is_file($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }
            $name = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));
            if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
                $quote = $value[0];
                if (substr($value, -1) === $quote) {
                    $value = substr($value, 1, -1);
                }
            }
            // basic variable expansion for ${VAR}
            $value = preg_replace_callback('/\$\{([A-Z0-9_]+)\}/i', function ($m) {
                $k = $m[1];
                $v = getenv($k);
                if ($v === false && isset($_ENV[$k])) $v = $_ENV[$k];
                return $v === false ? '' : $v;
            }, $value);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            if (function_exists('putenv')) {
                putenv($name . '=' . $value);
            }
        }
    }
}

