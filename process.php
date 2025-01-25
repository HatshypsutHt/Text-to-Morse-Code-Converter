<?php
	// Header for correct encoding
	header('Content-Type: text/html; charset=utf-8');

	// Retrieve data from the form
	$language = $_POST['language'] ?? 'en';
	$text = trim($_POST['text'] ?? ''); // Remove extra spaces

	// Check for empty input
	if (empty($text)) {
		header('Location: /');
		exit; 
	}

	// Load the dictionary
	$morseFile = __DIR__ . "/languages/language.php";
	if (!file_exists($morseFile)) {
		die('Error: Dictionary for the selected language not found.');
	}
	$morseCode = require $morseFile;

	// Function to translate text to Morse code
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
			$error = '<p style="color: red;">The following characters were not found in the dictionary: ' . implode(', ', array_unique($missingChars)) . '</p>';
		}

		return [
			'translation' => implode(' ', $result),
			'error' => $error,
		];
	}

	// Translate text
	$result = textToMorse($text, $morseCode);
	$morseResult = $result['translation'];
	$error = $result['error'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Translation Result - Text to Morse Code Converter</title>
	<!-- Favicons -->
	<link rel="shortcut icon" type="image/png" href="/images/favicon.png"/>
	<!-- META data for SEO -->
	<meta property="article:published_time" content="2022-04-21T17:20:00+03:00"/>
	<meta property="article:modified_time" content="<?= date('c'); ?>"/>
	<meta property="og:site_name" content="Morse Converter"/>
	<meta property="og:locale" content="en-US"/>
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="<?= "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>"/>
	<meta property="og:title" content="Translation Result - Text to Morse Code Converter"/>
	<meta property="og:description" content="Our site is a convenient tool for translating text into Morse code. You can enter any text in English or other supported languages, and instantly get its translation in Morse codes."/>
	<meta property="og:image:width" content="980"/>
	<meta property="og:image:height" content="980"/>
	<meta name="twitter:card" content="summary_large_image"/>
	<meta property="og:image" content="/images/bg-img.webp"/>
	<link rel="stylesheet" href="/css/styles.css">
	<script>
		let stopPlayback = false;

		// Function to copy text to clipboard
		function copyToClipboard() {
			const morseField = document.getElementById('morseOutput');
			morseField.select();
			document.execCommand('copy');
			alert('Text copied to clipboard!');
		}

		// Morse code player
		async function playMorse() {
			stopPlayback = false;
			const morseCode = document.getElementById('morseOutput').value;

			// Paths to sounds
			const shortSound = new Audio('/sounds/01.wav'); // Short signal
			const longSound = new Audio('/sounds/02.wav'); // Long signal

			// Pause durations
			const shortPause = 400; // Pause between signals
			const longPause = 1000; // Pause between characters
			const wordPause = 1400; // Pause between words

			// Iterate through each Morse code character
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

			if (!stopPlayback) alert('Playback finished!');
		}

		// Play a single sound
		function playSound(sound, pause) {
			return new Promise((resolve) => {
				sound.play();
				sound.onended = () => setTimeout(resolve, pause);
			});
		}

		// Sleep function for pauses
		function sleep(ms) {
			return new Promise((resolve) => setTimeout(resolve, ms));
		}

		// Stop playback
		function stopMorse() {
			stopPlayback = true;
		}
	</script>
</head>
<body>
	<div class="container">
		<h1>Translation Result</h1>
		<!-- Display warnings -->
		<?php if (!empty($error)): ?>
			<div><?= $error; ?></div>
		<?php endif; ?>
		<p><strong>Entered Text:</strong> <?= htmlspecialchars($text); ?></p>
		<label for="morseOutput"><strong>Morse Code:</strong></label>
		<textarea id="morseOutput" readonly><?= htmlspecialchars($morseResult); ?></textarea>
		<div style="margin-top: 15px;">
			<button onclick="copyToClipboard()">Copy to Clipboard</button>
			<button onclick="playMorse()">Play</button>
			<button onclick="stopMorse()">Stop</button>
		</div>
		<p class="m_top"><a href="/morse/">Back</a></p>
	</div>
</body>
</html>
