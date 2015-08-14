function test(){
	var out = document.getElementById("test");
	var postContent = document.getElementById("postcontent");
	var range = document.createRange();
	var bold = document.createElement("b");
	postContent.normalize();
	var children = postContent.childNodes;
	for(var i = 0; i < children.length; ++i){
		alert(children[i].nodeType + children[i].nodeValue);
		range.selectNode(children[i]);
		range.surroundContents(bold);
	}
}
// TextNode.splitText(offset); - two text nodes!
function submitPost(){
	cleanContent();
	document.getElementById("content").value = document.getElementByID("postcontent").innerHTML;
	document.getElementById("postform").submit();
}

function cleanContent(){
	var divContent = document.getElementById("postcontent");
	var postContent = document.getElementById("content");

	divContent.normalize();

	var children = divContent.childNodes;
	for(var i = 0; i < children.length; ++i){
		var node = children[i];
		cleanNode(node);
	}
}

function cleanNode(node){
	if(node.nodeType == Node.ELEMENT_NODE){
		switch(node.tagName){
			case "B":
			case "I":
			case "U":
			case "A":
			case "IMG":
				// These nodes are fine. However, must check the children nodes, too.
				var children = node.childNodes;
				for(var i = 0; i < children.length; ++i){
					cleanNode(children[i]);
				}
				break;
			case "BR":
				// Fine, but no children are allowed
				while(node.firstChild){
					node.removeChild(node.firstChild);
				}
				break;
			default:
				if(node.parentNode)
					node.parentNode.removeChild(node);
				break;
		}
	}
}