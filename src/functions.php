<?php

/**
 * Converts any items of `$array` as array.
 */
function array_arrays(array $array) : array
{
    return array_map(fn($v) => is_array($v) ? $v : [$v], $array);
}

/**
 * NB: All items should be arrays.
 */
function array_flatten(array $array) : array
{
    return array_merge(...array_values($array));
}
