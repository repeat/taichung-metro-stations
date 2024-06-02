#!/bin/bash

if [ ! -f .env ]; then
  echo "找不到 .env 檔案。請參考 .env.example 設定 Bearer Token 。"
  exit 1
fi

# 讀取 .env
export "$(grep -v '^#' .env | xargs)"

API_PREFIX="https://tdx.transportdata.tw/api/basic/v2/Rail/Metro/"

declare -A api_endpoints
api_endpoints=(
  ["line"]="${API_PREFIX}Line/TMRT?\$top=30&\$format=JSON"
  ["station"]="${API_PREFIX}Station/TMRT?\$top=30&\$format=JSON"
)

fetch_data() {
  local endpoint_key=$1
  local output_file=$2

  curl -X 'GET' \
    "${api_endpoints[$endpoint_key]}" \
    -H 'accept: application/json' \
    -H "Authorization: Bearer $BEARER_TOKEN" \
    -o "ref/$output_file"
}

fetch_data "line" "line-TMRT.json"
fetch_data "station" "station-TMRT.json"
