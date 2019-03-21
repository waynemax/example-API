<?php
    $memcache_connect = false;

    function mc_init($try = 0) {
        global $memcache_connect, $_ERRORS;
        $status = true;
        $errors = [];
        if (!$memcache_connect) {
            $memcache_connect = memcache_connect('localhost', 11211) or $status = false;
            if ($status === false) {
                if ($try == 2) {
                    array_push($errors, addError($_ERRORS['err_connectMC'][0], $_ERRORS['err_connectMC'][1]));
                    http_response_code(500);
                    ethrow($errors);
                } else {
                    $try++;
                    sleep(1);
                    return mc_init($try);
                }
            }
        }
        return $memcache_connect;
    }

    function mc_get($key) {
        $mc = mc_init();
        return memcache_get($mc, $key);
    }

    function mc_set($key, $value, int $ts = 0, bool $compressed = false) {
        $mc = mc_init();
        return memcache_set($mc, $key, $value, ($compressed ? MEMCACHE_COMPRESSED : false), $ts);
    }

    function mc_replace($key, $value, int $ts = 0, bool $compressed = false) {
        $mc = mc_init();
        return memcache_replace($mc, $key, $value, ($compressed ? MEMCACHE_COMPRESSED : false), $ts);
    }

    function mc_remove($key) {
        $mc = mc_init();
        return memcache_delete($mc, $key);
    }

    function mc_close() {
        $mc = mc_init();
        memcache_close($mc);
    }