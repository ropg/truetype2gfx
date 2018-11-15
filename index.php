<?php

session_start();

if (isset($_GET['reset'])) unset($_SESSION['fonts']);

if (isset($_POST["get-font"])) {

	//if (!isset($_POST['size']) || gettype($_POST['size']) != "integer" || $_POST['size'] < 3) exit();
	$size = $_POST['size'];

	if (!isset($_POST['font'])) exit();	
	$font = escapeshellarg("fonts/" . $_POST['font']);
	
	
	exec("./fontconvert $font $size", $output, $retval);
	if ($retval != 0) exit();
	
	$filename = $output[count($output) - 6];
	$filename = str_replace("const GFXfont ", "", $filename);
	$filename = str_replace(" PROGMEM = {", ".h", $filename);
	

	header("Content-Disposition: attachment; filename=\"$filename\"");
	
	foreach ($output as $line) echo "$line\n";

	exit();
}


if (!isset($_SESSION['fonts'])) $_SESSION['fonts'] = array();

// Delete fonts from session variable if the disk file is not there anymore
foreach ($_SESSION['fonts'] as $index => $font) {
	if (!file_exists("fonts/user/$font")) unset($_SESSION['fonts'][$index]);
}


$select_font = "";
if (isset($_POST["submit-file"])) {
	$target_dir = "fonts/user/";
	$filename = basename($_FILES["fileToUpload"]["name"]);
	$target_file = $target_dir . $filename;
	$select_font = "user/$filename";
	
	if (strtolower(substr($target_file, -4)) == ".ttf") {
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			if (!in_array($filename, $_SESSION['fonts'])) {
				array_push($_SESSION['fonts'], $filename);
				if (count($_SESSION['fonts']) > 5) array_shift($_SESSION['fonts']);
			}
		}
	}
}

?>

<html>

<head>
	<title>truetype2gfx - Converting fonts from TrueType to Adafruit GFX</title>

	<style>
		body {
			background-color: #000000;
			color: #ffffff;
			margin: 100px;
			margin-top: 100px;
			margin-left: 100px;
			font-family: Verdana, sans-serif;
		}
		a {
			text-decoration: none;
			font-weight: bold;
			color: #8080FF;
		}
		td {
			vertical-align: top;
		}
		table {
			width: 960px;
		}
		td#first {
			margin: 0px;
			padding-top: 90px;
			padding-left:50px;
			background-image:url('M5Stack-bg.png');
			background-repeat:no-repeat;
			width: 480px;
			height: 429px;
		}
		td#second {
			width: 240px;
		}
		td#third {
			width: 240px;
		}
		#textfield {
			width: 200px;
		}
		#sizefield {
			width: 35px;
			text-align: center;
		}
		#get-font {
			width: 200px;
			height: 30px;
			font-size: 20px;
			font-weight: bold;
		}
	</style>
</head>

<body onload = 'setFont()'>

	
	&nbsp;<br>

	<table>
		<tr>
			<td colspan=3>
				<h2>truetype2gfx - Converting fonts from TrueType to Adafruit GFX</h2>
				&nbsp;<br>
				&nbsp;<br>
			</td>
		</tr>

		<tr>
			<td id="first">
				<img id="image" src="image.php">
			</td>
			<td id="second">
			
				<form action="" method="post" enctype="multipart/form-data">
			
				<h3>FreeFonts</h3>
				<input type="radio" name="font" value="FreeSans.ttf" checked onChange="updateImage()"> FreeSans<br>
				<input type="radio" name="font" value="FreeSansBold.ttf" onChange="updateImage()"> FreeSansBold<br>
				<input type="radio" name="font" value="FreeSansBoldOblique.ttf" onChange="updateImage()"> FreeSansBoldOblique<br>
				<input type="radio" name="font" value="FreeSansOblique.ttf" onChange="updateImage()"> FreeSansOblique<br>
				<input type="radio" name="font" value="FreeSerif.ttf" onChange="updateImage()"> FreeSerif<br>
				<input type="radio" name="font" value="FreeSerifBold.ttf" onChange="updateImage()"> FreeSerifBold<br>
				<input type="radio" name="font" value="FreeSerifBoldItalic.ttf" onChange="updateImage()"> FreeSerifBoldItalic<br>
				<input type="radio" name="font" value="FreeSerifItalic.ttf" onChange="updateImage()"> FreeSerifItalic<br>
				<input type="radio" name="font" value="FreeMono.ttf" onChange="updateImage()"> FreeMono<br>
				<input type="radio" name="font" value="FreeMonoBold.ttf" onChange="updateImage()"> FreeMonoBold<br>
				<input type="radio" name="font" value="FreeMonoBoldOblique.ttf" onChange="updateImage()"> FreeMonoBoldOblique<br>
				<input type="radio" name="font" value="FreeMonoOblique.ttf" onChange="updateImage()"> FreeMonoOblique<br>
				
				<h3>Your fonts</h3>
				<?php
					foreach ($_SESSION['fonts'] as $font) {
						echo "<input type=\"radio\" name=\"font\" value=\"user/$font\" onChange=\"updateImage()\"> " . str_replace(".TTF", "", str_replace(".ttf", "", $font)) . "<br>\n";
					}
				?>
				
				&nbsp;<br>
				
				<input type="submit" value="Upload" name="submit-file" onClick="return validateUpload();"> <input type="file" name="fileToUpload" id="fileToUpload"> 
			</td>
			<td id="third">
				<h3>Font Size</h3>
				<input type="text" name="size" id="sizefield" value="20" onInput="updateImage()"> points
				
				&nbsp;<br>
				&nbsp;<br>

				<h3>Demo text</h3>
				<input type="text" name="text" id="textfield" value="Testing 123..." onInput="updateImage()">
				
				&nbsp;<br>
				&nbsp;<br>
				&nbsp;<br>
				&nbsp;<br>
				
				<input type="submit" id="get-font" value="Get GFX font file" name="get-font">
				
				</form>
				
			</td>
		</tr>
		
		<tr>
			<td colspan=3>
	
&nbsp<br>	
			
<h3>Introducing truetype2gfx</h3>

<p>Many Arduino projects and ready-built devices come with a display. And many of the display drivers use the Adafruit GFX display driver to display variable-width fonts. Some fonts usually are included with the driver, and then there's a complicated procedure for adding your own fonts. It involves compiling tools and a trial-and-error process for figuring out how big the font will turn out on your display.</p>
			
<p>But now you can skip all that and convert the fonts your Arduino project needs with ease. No need to compile tools, no more guessing how big a font will be. Simply select a FreeFont or upload any TrueType font, select a size, download the include file and you're ready to use the font in your project.</p>

<h3>The size thing</h3>

<p>Font sizes are given in points, where a point is 1/72 of an inch, describing the actual size on a display. Or that's what it's supposed to mean, but pretty much everyone that uses the Adafruit software keeps the setting of 141 pixels per inch. In the Adafruit software it says:</p>

<blockquote><code>#define DPI 141 // Approximate res. of Adafruit 2.8" TFT </code></blockquote>

<p>But since everyone keeps the setting, a certain font at 20 points is going to take up the same number of pixels on a 
lot of devices. And then there's the different fonts displaying at radically different sizes due to various metrics 
included in the font. (See <a href="https://iamvdo.me/en/blog/css-font-metrics-line-height-and-vertical-align">here</a> 
for details.) But I don't have to care about that: when I make gfx fonts and include them on my device, they are the 
same size as they are on the virtual device on the screen above. (This only works if your screen is 320x240 pixels. If your screen dimensions are different, you can still see the size relative to the FreeFonts of a given size.)</p>

<h3>Your own fonts</h3>

<p>TrueType fonts are everywhere online. At the time of writing this, you can get loads and loads of pretty TrueType fonts <a href="https://www.1001freefonts.com">here</a> but you can also pick up fonts at any of <a href = "https://www.google.de/search?q=truetype+free+fonts">these sites</a>. (Beware of malware: do not unpack ".exe archives" or do anything else silly with files downloaded from these sites.)</p>

<p>Using this tool, you can upload and then view and convert up to five fonts (which are only available to you). If you upload a sixth font, the first one disappears. Also note that these fonts will only last as long as your PHP session does, so whenever you come back a day later, your fonts may be gone. It's really only meant to be a short-term buffer.</p>

<h3>Example</h3>

<p>I found a nice font on this website listed above. It was called "Black Street" and the font file I uploaded was 
"Black Street.ttf". I fiddled with the size until it filled the display nicely, at 35 points. I then hit the "Get GFX font file" button and my browser downloaded a file called "Black_Street35pt7b.h". I created a new Arduino sketch with the following content:</p>

<blockquote><pre>
#include &lt;M5Stack.h&gt;
#include "Black_Street35pt7b.h"

void setup
  m5.begin();
  m5.lcd.fillScreen(TFT_WHITE);
  m5.lcd.setTextColor(TFT_BLACK);
  m5.lcd.setTextDatum(CC_DATUM);
  m5.lcd.setFreeFont(&Black_Street35pt7b);
  m5.lcd.drawString("Testing 123...", 160, 120);
}

void loop() {
}
</pre></blockquote>
			
<p>I then added the "Black_Street35p7b.h" from my "Download" directory as a second tab with "Sketch / Add file..." in the Arduino IDE, ran the program et voila:</p>

<img src="truetype2gfx_demo.png">

<p><i>(If you do not have an M5Stack but some other device your library will not be called M5Stack.h and your display will not be at "m5.lcd", but you'll figure it out...)</i></p>

<h3>Source code, bug reports, questions, etc..</h3>

<p>This tool has a <a href="https://github.com/ropg/truetype2gfx">github repository</a> that has the (quick-hack-style) PHP/Javascript code behind all this. And if you have any questions, bug reports or suggestions, simply <a href="https://github.com/ropg/truetype2gfx/issues/new">open a new issue</a> there and I will see what I can do. </p>

			</td>
		</tr>
	</table>
	
	
	
	<script>		
	
		function updateImage() {
			document.getElementById("image").src = "image.php?font=" + font() + "&size=" + document.getElementById("sizefield").value + "&text=" + document.getElementById("textfield").value+ "#" + new Date().getTime();
		}
	
		function font() {
			var fonts = document.getElementsByName('font');
			for (var i = 0, length = fonts.length; i < length; i++) {
				if (fonts[i].checked) {
					return fonts[i].value;
				}
			}
			return "";
		}
		
		function setFont() {
			var e = document.getElementsByName("font");
			for (var i = 0; i < e.length; i++) {
				if (e[i].value == "<?php echo $select_font?>") {
					e[i].checked = true;
					break;
				}
			}
			updateImage();
		}
		
		function validateUpload() {
  			var file = document.getElementById("fileToUpload").value;
			var reg = /(.*?)\.(ttf|TTF)$/;
			if(!file.match(reg)) {
				alert("You can only upload a TrueType font (.ttf or .TTF extension)");
				return false;
			}
		}
		
	</script>
</body>

</html>
