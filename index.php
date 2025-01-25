<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Text to Morse Code Converter</title>
	<!-- Favicons -->
	<link rel="shortcut icon" type="image/png" href="/images/favicon.png"/>
	<!-- META data for SEO -->
	<meta property="article:published_time" content="2022-04-21T17:20:00+03:00"/>
	<meta property="article:modified_time" content="<?= date('c'); ?>"/>
	<meta property="og:site_name" content="Morse Converter"/>
	<meta property="og:locale" content="en-US"/>
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="<?= "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>"/>
	<meta property="og:title" content="Text to Morse Code Converter"/>
	<meta property="og:description" content="Our site is a convenient tool for translating text into Morse code. You can enter any text in English or supported languages, and instantly get its translation into Morse codes."/>
	<meta property="og:image:width" content="980"/>
	<meta property="og:image:height" content="980"/>
	<meta name="twitter:card" content="summary_large_image"/>
	<meta property="og:image" content="/images/bg-img.webp"/>
	<link rel="stylesheet" href="/css/styles.css">
	<script>
		let currentLayout = 'unknown'; // Variable to store the current layout

		// Function to detect Cyrillic or Latin keyboard layout
		function detectKeyboardLayout(event) {
			const key = event.key;

			// Regular expressions for the two layouts
			const isCyrillic = /^[а-яіїєґёъыэюя]+$/i.test(key); // Cyrillic
			const isLatin = /^[a-z]+$/i.test(key); // Latin

			// Determine the language
			if (isCyrillic) {
				currentLayout = 'cyrillic'; // Cyrillic
				document.getElementById('language').value = 'cyrillic';
			} else if (isLatin) {
				currentLayout = 'latin'; // Latin
				document.getElementById('language').value = 'latin';
			} else {
				currentLayout = 'unknown'; // Unknown layout
			}

			// Display the current layout on the screen
			const layoutName = {
				cyrillic: 'Cyrillic',
				latin: 'Latin',
				unknown: 'Unknown layout'
			};
			document.getElementById('current-layout').innerText = layoutName[currentLayout];
		}

		// Prevent text paste
		function preventPaste(event) {
			event.preventDefault(); // Disable paste
			alert('Pasting text is not allowed. Please enter the text manually.');
		}

		// Display the current layout on page load
		window.onload = function () {
			document.getElementById('current-layout').innerText = 'Press any key to determine the layout';
		};
	</script>
</head>
<body>
	<div class="container">
		<h1>Text to Morse Code Converter</h1>
		<p><strong>Current Layout:</strong> <span id="current-layout">Determining...</span></p>
		<form method="POST" action="process.php" accept-charset="UTF-8">
			<!-- Field for automatic language detection -->
			<input type="hidden" name="language" id="language" value="unknown">

			<label for="text">Enter text:</label>
			<textarea 
				name="text" 
				id="text" 
				rows="5" 
				cols="50" 
				placeholder="Enter text here..." 
				onkeydown="detectKeyboardLayout(event)" 
				onpaste="preventPaste(event)" 
				ondrop="preventPaste(event)" 
				required
			></textarea>
			
			<div style="margin-top: 15px;">
				<button type="submit">Translate</button>
			</div>
		</form>
	</div>
</body>
</html>
