<?php

namespace App\Support;

class UploadRules
{
  public static function image(bool $required = false): array
  {
    return self::build('image', $required);
  }

  public static function document(bool $required = false): array
  {
    return self::build('document', $required);
  }

  public static function presentation(bool $required = false): array
  {
    return self::build('presentation', $required);
  }

  public static function video(bool $required = false): array
  {
    return self::build('video', $required);
  }

  public static function spreadsheet(bool $required = false): array
  {
    return self::build('spreadsheet', $required);
  }

  public static function assignment(bool $required = false): array
  {
    return self::build('assignment', $required);
  }

  public static function ruleString(string $type): string
  {
    $config = config("security.uploads.{$type}", []);
    $mimes = implode(',', $config['mimes'] ?? []);
    $max = $config['max_kb'] ?? 5120;

    return "file|mimes:{$mimes}|max:{$max}";
  }

  private static function build(string $type, bool $required): array
  {
    $rule = self::ruleString($type);

    return [$required ? "required|{$rule}" : "nullable|{$rule}"];
  }
}
