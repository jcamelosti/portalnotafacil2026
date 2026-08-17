<?php
if (! function_exists('canonical_url')) {
    function canonical_url($path = null)
    {
        return $path 
            ? url($path) 
            : url()->current();
    }
}