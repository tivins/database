<?php

/**
 * NB: All items should be arrays.
 */
function array_flatten(array $array): array
{
    return array_merge(...array_values($array));
}