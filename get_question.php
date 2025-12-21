<?php
header('Content-Type: application/json; charset=utf-8');

// Загружаем файл с вопросами
// questions.json должен содержать JSON-массив или объект, например:
// const questions = { "Категория 1": [ { "question": "...", "answer": [...] }, ... ], ... };

$questionsFile = 'questions.json';

if (!file_exists($questionsFile)) {
    echo json_encode(['error' => 'Файл с вопросами не найден.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Получаем содержимое файла
$jsContent = file_get_contents($questionsFile);

// Извлекаем JSON из JS (удаляем const questions = и ;)
$jsonStart = strpos($jsContent, '{');
$jsonEnd = strrpos($jsContent, '}');
$jsonString = substr($jsContent, $jsonStart, $jsonEnd - $jsonStart + 1);

// Декодируем JSON
$questions = json_decode($jsonString, true);

if ($questions === null) {
    echo json_encode(['error' => 'Ошибка декодирования JSON.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Выбираем случайную категорию
$categories = array_keys($questions);
$randomCategory = $categories[array_rand($categories)];

// Выбираем случайный вопрос из категории
$questionsInCategory = $questions[$randomCategory];
$randomQuestion = $questionsInCategory[array_rand($questionsInCategory)];

// Формируем ответ
$response = [
    'category' => $randomCategory,
    'question' => $randomQuestion['question'],
    'answer' => $randomQuestion['answer']
];

// Отправляем JSON с нормальной кириллицей
echo json_encode($response, JSON_UNESCAPED_UNICODE);
