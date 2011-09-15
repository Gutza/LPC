<?php

// Set the page title -- this affects the <TITLE> tag.
$p->title="Hello world";

// Show the title as a <H1>
$p->st();

// Show a confirmation box
$p->la(confirm("All is well!"));

// Show an error box
$p->la(error("This is what it would've look like if it weren't. But it is."));

// Check for mobile devices, just for the heck of it
$p->la(
	"<p>".
	($p->browserIsMobile()?"You <b>ARE</b>":"You're <b>NOT</b>").
	" reading this on a mobile device.</p>"
);

// Uncomment if you want to show the raw content (useful for AJAX stuff)
//$p->renderMode='raw';

// Uncomment if you want to hide the LPC_Page content altogether.
// This is useful if you want to conditionally show an HTML page or some other
// type of content, using the same algorithm (presumably using echo and ob).
//$p->renderMode='none';

// Some free-styling.
$p->appendBuffer=true;
?>
<p>Life is good.</p>
