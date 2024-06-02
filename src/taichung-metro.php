<?php

$line_file = __DIR__ . '/../ref/line-TMRT.json';
$line_source = json_decode(file_get_contents($line_file, true), true)[0];

$stations_file = __DIR__ . '/../ref/station-TMRT.json';
$stations_source = json_decode(file_get_contents($stations_file, true), true);

// 整理資料

$line_id = $line_source['LineID'];
$line_number = $line_source['LineNo'];
$line_name = $line_source['LineName']['Zh_tw'];
$line_color = $line_source['LineColor'];

$geometry = [
    'type' => 'Point'
];

foreach ($stations_source as $station) {
    list(
        'StationUID' => $station_uid,
        'StationID' => $station_id,
        'StationAddress' => $address,
        'StationPosition' => $position,
        'StationName' => $station_name,
    ) = $station;

    list(
        'En' => $station_name_en,
        'Zh_tw' => $station_name_tw,
    ) = $station_name;

    list(
        'GeoHash' => $geohash,
        'PositionLat' => $lat,
        'PositionLon' => $lon,
    ) = $position;

    $geometry['coordinates'] = [(float) $lon, (float) $lat];

    $properties = [
        '車站編號' => $station_id,
        '中文站名' => $station_name_tw,
        '英譯站名' => $station_name_en,
        '路線編號' => $line_number,
        '路線名' => $line_name,
        '地址' => $address,
        '緯度' => (float) $lat,
        '經度' => (float) $lon,
        // https://help.github.com/en/github/managing-files-in-a-repository/mapping-geojson-files-on-github#styling-features
        'marker-size' => 'medium',
        'marker-symbol' => 'rail-metro',
        'marker-color' => $line_color,
    ];

    $feature = [
        'type' => 'Feature',
        'geometry' => $geometry,
        'properties' => $properties,
    ];

    $features[] = $feature;
}

$geojson = [
    'type' => 'FeatureCollection',
    'features' => $features,
];

$handle = fopen(__DIR__ . '/../dist/taichung-metro.geojson', 'w+');
fwrite($handle, json_encode($geojson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
fclose($handle);
