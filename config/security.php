<?php

return [

  'headers' => [
    'x_frame_options' => 'SAMEORIGIN',
    'x_content_type_options' => 'nosniff',
    'referrer_policy' => 'strict-origin-when-cross-origin',
    'permissions_policy' => 'camera=(self), geolocation=(self), microphone=()',
  ],

  'rate_limits' => [
    'login' => '10,1',
    'api_self_attendance' => '30,1',
    'file_upload' => '20,1',
    'settings' => '10,1',
    'reports_export' => '15,1',
  ],

  'uploads' => [
    'image' => [
      'mimes' => ['jpg', 'jpeg', 'png', 'webp'],
      'max_kb' => 5120,
    ],
    'document' => [
      'mimes' => ['pdf'],
      'max_kb' => 51200,
    ],
    'presentation' => [
      'mimes' => ['ppt', 'pptx'],
      'max_kb' => 51200,
    ],
    'video' => [
      'mimes' => ['mp4', 'webm', 'mov'],
      // Shared hosting: keep one upload from monopolizing PHP workers or disk.
      // Use a video platform link for recordings larger than this limit.
      'max_kb' => (int) env('LMS_MAX_VIDEO_UPLOAD_KB', 102400),
    ],
    'spreadsheet' => [
      'mimes' => ['xlsx', 'xls', 'csv'],
      'max_kb' => 4096,
    ],
    'assignment' => [
      'mimes' => ['pdf', 'doc', 'docx', 'zip', 'jpg', 'jpeg', 'png'],
      'max_kb' => (int) env('LMS_MAX_ASSIGNMENT_UPLOAD_KB', 20480),
    ],
    'announcement' => [
      'mimes' => ['pdf', 'ppt', 'pptx', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
      'max_kb' => (int) env('LMS_MAX_ANNOUNCEMENT_UPLOAD_KB', 20480),
    ],
  ],

  'audit' => [
    'retention_days' => (int) env('AUDIT_LOG_RETENTION_DAYS', 365),
    'login_retention_days' => (int) env('LOGIN_LOG_RETENTION_DAYS', 180),
  ],

  'backup' => [
    'path' => storage_path('app/backups'),
    'retention' => (int) env('BACKUP_RETENTION_DAYS', 14),
    'schedule' => env('BACKUP_SCHEDULE', 'daily'),
  ],

];
