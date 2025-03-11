<?php

namespace App\Services;

use Google\Cloud\Language\LanguageClient;

class GoogleAPIService
{
    private static ?LanguageClient $languageClient = null;

    public static function getClient(): LanguageClient
    {
        if (self::$languageClient === null) {
            self::$languageClient = new LanguageClient([
                'keyFilePath' => base_path('google_keyFile.json'),
                'projectId' => env('GOOGLE_PROJECT_ID', 'my-project-id')
            ]);
        }
        return self::$languageClient;
    }

    public static function dateText($text)
    {
        $client = self::getClient(); // Singleton 클라이언트 사용

        // 엔터티 분석 실행
        $result = $client->analyzeEntities($text);
        $entities = $result->entities();

        // 날짜(DATE) 엔터티만 필터링
        $dates = array_filter($entities, fn($e) => $e['type'] === 'DATE');
        return reset($dates);
    }
}