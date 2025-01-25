<?php
	// Заголовок для коректного кодування
	header('Content-Type: text/html; charset=utf-8');

	// Отримання даних із форми
	$language = $_POST['language'] ?? 'en';
	$text = trim($_POST['text'] ?? ''); // Видалення зайвих пробілів

	// Перевірка на порожнє поле
	if (empty($text)) {
		header('Location: https://demo.web-rynok.site/morse/');
		exit; 
	}

	// Завантаження словника
	$morseFile = __DIR__ . "/languages/language.php";
	if (!file_exists($morseFile)) {
		die('Помилка: Словник для вибраної мови не знайдено.');
	}
	$morseCode = require $morseFile;

	// Функція для перекладу тексту в азбуку Морзе
	function textToMorse($text, $morseCode) {
		$text = mb_strtoupper(trim($text), 'UTF-8');
		$result = [];
		$missingChars = [];

		foreach (preg_split('//u', $text, null, PREG_SPLIT_NO_EMPTY) as $char) {
			if (isset($morseCode[$char])) {
				$result[] = $morseCode[$char];
			} elseif (!ctype_space($char)) {
				$missingChars[] = $char;
			}
		}

		$error = '';
		if (!empty($missingChars)) {
			$error = '<p style="color: red;">Наступні символи не знайдено у словнику: ' . implode(', ', array_unique($missingChars)) . '</p>';
		}

		return [
			'translation' => implode(' ', $result),
			'error' => $error,
		];
	}

	// Переклад тексту
	$result = textToMorse($text, $morseCode);
	$morseResult = $result['translation'];
	$error = $result['error'];
?>

<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Результат перекладу - Конвертер тексту в азбуку Морзе</title>
	<!-- Favicons -->
	<link rel="shortcut icon" type="image/png" href="https://drog.info/images/favicon.png"/>
	<!-- META дані для SEO -->
	<meta property="article:published_time" content="2022-04-21T17:20:00+03:00"/>
	<meta property="article:modified_time" content="<?= date('c'); ?>"/>
	<meta property="og:site_name" content="Конвертер Морзе"/>
	<meta property="og:locale" content="uk-UA"/>
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="<?= "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>"/>
	<meta property="og:title" content="Результат перекладу - Конвертер тексту в азбуку Морзе"/>
	<meta property="og:description" content="Наш сайт – це зручний інструмент для перекладу тексту в азбуку Морзе. Ви можете ввести будь-який текст українською чи англійською мовами, і отримати його миттєвий переклад у вигляді кодів Морзе."/>
	<meta property="og:image:width" content="980"/>
	<meta property="og:image:height" content="980"/>
	<meta name="twitter:card" content="summary_large_image"/>
	<meta property="og:image" content="/images/bg-img.webp"/>
	<link rel="stylesheet" href="/morse/css/styles.css">
	<script>
		let stopPlayback = false;

		// Функція копіювання тексту у буфер обміну
		function copyToClipboard() {
			const morseField = document.getElementById('morseOutput');
			morseField.select();
			document.execCommand('copy');
			alert('Текст скопійовано в буфер обміну!');
		}

		// Програвач азбуки Морзе
		async function playMorse() {
			stopPlayback = false;
			const morseCode = document.getElementById('morseOutput').value;

			// Шляхи до звуків
			const shortSound = new Audio('/morse/sounds/01.wav'); // Короткий сигнал
			const longSound = new Audio('/morse/sounds/02.wav'); // Довгий сигнал

			// Час пауз
			const shortPause = 400; // Пауза між сигналами
			const longPause = 1000; // Пауза між символами
			const wordPause = 1400; // Пауза між словами

			// Пробігаємося по кожному символу Морзе
			for (const char of morseCode.split('')) {
				if (stopPlayback) break;

				if (char === '.') {
					await playSound(shortSound, shortPause);
				} else if (char === '-') {
					await playSound(longSound, shortPause);
				} else if (char === ' ') {
					await sleep(longPause);
				} else if (char === '/') {
					await sleep(wordPause);
				}
			}

			if (!stopPlayback) alert('Відтворення завершено!');
		}

		// Програвання одного сигналу
		function playSound(sound, pause) {
			return new Promise((resolve) => {
				sound.play();
				sound.onended = () => setTimeout(resolve, pause);
			});
		}

		// Функція для паузи
		function sleep(ms) {
			return new Promise((resolve) => setTimeout(resolve, ms));
		}

		// Зупинка 
		function stopMorse() {
			stopPlayback = true;
		}
	</script>
</head>
<body>
	<div class="container">
		<h1>Результат перекладу</h1>
		<!-- Виведення попереджень -->
		<?php if (!empty($error)): ?>
			<div><?= $error; ?></div>
		<?php endif; ?>
		<p><strong>Введений текст:</strong> <?= htmlspecialchars($text); ?></p>
		<label for="morseOutput"><strong>Азбука Морзе:</strong></label>
		<textarea id="morseOutput" readonly><?= htmlspecialchars($morseResult); ?></textarea>
		<div style="margin-top: 15px;">
			<button onclick="copyToClipboard()">Скопіювати в пам'ять</button>
			<button onclick="playMorse()">Озвучити</button>
			<button onclick="stopMorse()">Зупинити</button>
		</div>
		<p class="m_top"><a href="/morse/">Назад</a></p>
	</div>
</body>
</html>
