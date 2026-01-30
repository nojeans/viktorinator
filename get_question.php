<?php

header('Content-Type: application/json; charset=utf-8');

// Разрешаем только GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(
        ['error' => 'Метод не поддерживается'],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

// Читаем базу вопросов
$data = json_decode(file_get_contents(__DIR__ . '/questions.json'), true);

if (
    !$data ||
    !isset($data['categories']) ||
    !is_array($data['categories'])
) {
    http_response_code(500);
    echo json_encode(
        ['error' => 'Некорректная структура questions.json'],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

// Читаем query: ?categories=1,2,3
$allowedCategories = [];
if (!empty($_GET['categories'])) {
    $allowedCategories = array_map('intval', explode(',', $_GET['categories']));
}

$pool = [];

foreach ($data['categories'] as $category) {

    if (
        !isset(
            $category['id_category'],
            $category['title'],
            $category['questions']
        )
    ) {
        continue;
    }

    // Фильтрация по id_category
    if (
        !empty($allowedCategories) &&
        !in_array($category['id_category'], $allowedCategories, true)
    ) {
        continue;
    }

    foreach ($category['questions'] as $q) {
        if (!isset($q['question'], $q['answer'])) {
            continue;
        }

        $pool[] = [
            'category' => $category['title'],
            'question' => $q['question'],
            'answer'   => $q['answer']
        ];
    }
}

// Если после фильтрации вопросов нет
if (empty($pool)) {
    http_response_code(404);
    echo json_encode(
        ['error' => 'Нет вопросов по выбранным категориям'],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

// Отдаём случайный вопрос
echo json_encode(
    $pool[array_rand($pool)],
    JSON_UNESCAPED_UNICODE
);
