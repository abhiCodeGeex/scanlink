<?php

return [
  /*
  |--------------------------------------------------------------------------
  | YouTube Data API (OAuth 2.0)
  |--------------------------------------------------------------------------
  |
  | Values in Global Settings override these .env entries. Legacy username /
  | password (ClientLogin) is no longer supported by Google — use OAuth instead.
  |
  */
  'client_id' => env('YOUTUBE_CLIENT_ID'),
  'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
  'refresh_token' => env('YOUTUBE_REFRESH_TOKEN'),
  'developer_key' => env('YOUTUBE_DEVELOPER_KEY'),
  'application_name' => env('YOUTUBE_APPLICATION_NAME', 'ScanLink Admin'),
];
