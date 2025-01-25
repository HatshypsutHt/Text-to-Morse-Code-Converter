<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Конвертер тексту в азбуку Морзе</title>
	<!-- Favicons -->
	<link rel="shortcut icon" type="image/png" href="https://drog.info/images/favicon.png"/>
	<!-- META дані для SEO -->
	<meta property="article:published_time" content="2022-04-21T17:20:00+03:00"/>
	<meta property="article:modified_time" content="<?= date('c'); ?>"/>
	<meta property="og:site_name" content="Конвертер Морзе"/>
	<meta property="og:locale" content="uk-UA"/>
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="<?= "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>"/>
	<meta property="og:title" content="Конвертер тексту в азбуку Морзе"/>
	<meta property="og:description" content="Наш сайт – це зручний інструмент для перекладу тексту в азбуку Морзе. Ви можете ввести будь-який текст українською чи англійською мовами, і отримати його миттєвий переклад у вигляді кодів Морзе."/>
	<meta property="og:image:width" content="980"/>
	<meta property="og:image:height" content="980"/>
	<meta name="twitter:card" content="summary_large_image"/>
	<meta property="og:image" content="/images/bg-img.webp"/>
	<link rel="stylesheet" href="/morse/css/styles.css">
	<script>
		let currentLayout = 'unknown'; // Змінна для збереження поточної розкладки

		// Функція для визначення кирилиці чи латиниці
		function detectKeyboardLayout(event) {
			const key = event.key;

			// Регулярні вирази для двох варіантів
			const isCyrillic = /^[а-яіїєґёъыэюя]+$/i.test(key); // Кирилиця 
			const isLatin = /^[a-z]+$/i.test(key); // Латиниця

			// Визначення мови
			if (isCyrillic) {
				currentLayout = 'cyrillic'; // Кирилиця
				document.getElementById('language').value = 'cyrillic';
			} else if (isLatin) {
				currentLayout = 'latin'; // Латиниця
				document.getElementById('language').value = 'latin';
			} else {
				currentLayout = 'unknown'; // Невизначена розкладка
			}

			// Вивід поточної розкладки на екран
			const layoutName = {
				cyrillic: 'Кирилиця',
				latin: 'Латиниця',
				unknown: 'Невизначена розкладка'
			};
			document.getElementById('current-layout').innerText = layoutName[currentLayout];
		}

		// Забороняємо вставку тексту
		function preventPaste(event) {
			event.preventDefault(); // Відміняємо вставку
			alert('Вставка тексту заборонена. Будь ласка, введіть текст вручну.');
		}

		// Показ поточної розкладки при завантаженні сторінки
		window.onload = function () {
			document.getElementById('current-layout').innerText = 'Натисніть будь-яку клавішу, щоб визначити розкладку';
		};
	</script>
</head>
<body>
	<div class="container">
		<h1>Конвертер тексту в азбуку Морзе</h1>
		<p><strong>Поточна розкладка:</strong> <span id="current-layout">Визначається...</span></p>
		<form method="POST" action="process.php" accept-charset="UTF-8">
			<!-- Поле для автоматичного визначення мови -->
			<input type="hidden" name="language" id="language" value="unknown">

			<label for="text">Введіть текст:</label>
			<textarea 
				name="text" 
				id="text" 
				rows="5" 
				cols="50" 
				placeholder="Введіть текст тут..." 
				onkeydown="detectKeyboardLayout(event)" 
				onpaste="preventPaste(event)" 
				ondrop="preventPaste(event)" 
				required
			></textarea>
			
			<div style="margin-top: 15px;">
				<button type="submit">Перекласти</button>
			</div>
		</form>
	</div>
</body>
</html>