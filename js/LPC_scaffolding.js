var myFrameID="objectPicker";
var myInputClass='pickFocus';
function LPC_scaffolding_pickObject(className,input)
{
	$(input).addClass(myInputClass);
	var myFrame=document.createElement('iframe');
	myFrame.id=myFrameID;
	$('table').first().hide().before(myFrame);
	myFrame.style.width="100%";
	myFrame.style.border="0px";
	myFrame.style.height=Math.max(500,$('table').height())+"px";
	myFrame.style.position="absolute";
	myFrame.style.overflow="auto";
	myFrame.src="objectPicker.php?c="+className;

	return false;
}

function LPC_scaffolding_cancelPick()
{
	// Can't use jQuery because this runs in the IFRAME
	var myTable=parent.document.getElementsByTagName('table');
	myTable[0].style.display='';

	var myInput=parent.document.getElementsByClassName(myInputClass);
	myInput[0].setAttribute("class","");

	var myFrame=parent.document.getElementById(myFrameID);
	myFrame.parentNode.removeChild(myFrame);
	return false;
}

function LPC_scaffolding_pick(id)
{
	// Can't use jQuery because this runs in the IFRAME
	var myInput=parent.document.getElementsByClassName(myInputClass);
	myInput[0].value=id;
	return LPC_scaffolding_cancelPick();
}

function LPC_scaffolding_onTextInput(el)
{
	var className = "len";

	var $lenEl = $(el).parent().find("."+className);
	if ($lenEl.size() == 0) {
		var lenEl = document.createElement("span");
		$(el).after(lenEl);
		$lenEl = $(lenEl);
		$lenEl.addClass(className).css("marginLeft", "5px").css("fontSize", "90%");
	}
	$lenEl.text(el.value.length);
}
