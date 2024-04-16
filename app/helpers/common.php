<?php

function generateRandomCoordinates() {

    $maxInt = 1000000;
    return [
        'type' => 'Point',
        'coordinates' => [
            mt_rand(-90 * $maxInt, 90 * $maxInt) / $maxInt,
            mt_rand(-180 * $maxInt, 180 * $maxInt) / $maxInt
        ],
    ];

}

function object_id($id = null) {
    return new MongoDB\BSON\ObjectID($id);
}