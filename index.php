<!DOCTYPE html>
<html>
	<head>
		<title>Shopping List</title>
		
		<link rel="icon" href="icon.png">
		<link rel="apple-touch-icon" href="apple-touch.png">
		<link rel="stylesheet" href="reset.css">
		<link rel="stylesheet" href="main.css">
		<script>
			function clearCookie(cookieName){
				document.cookie = cookieName + "=;  expires=Thu, 01 Jan 1970 00:00:01 GMT;";
				window.location.href = "?";
			}
			function makeCookie(cookieName, cookieContents){
				document.cookie = cookieName + "=" + cookieContents;
				window.location.href = "?";
			}
			
			
			
			function deleteItem(id, name){
				if(name == "milk" || name == "Milk"){
					if(confirm("Got Milk?")){
						window.location.href = '?delete=' + id;
					}
				}else if(confirm("Remove " + name + "?")){
					window.location.href = '?delete=' + id;
				}									
			}
			function addItem(){
				name = prompt("Add new item:");
				if(name != ""){
					if(name != " "){
						if(name != null){
							if(name != "null"){
								window.location.href = '?add=' + name;
							}
						}
					}
				}
			}
			
			function openNav() {
				document.getElementById("menuWrapper").style.width = "700px";
				document.getElementById("screen").style.display = "block";
				document.getElementById("main").style.marginLeft = "700px";
				document.getElementById("body").style.overflowX = "hidden";
			}

			function closeNav() {
				document.getElementById("menuWrapper").style.width = "0";
				document.getElementById("screen").style.display = "none";
				document.getElementById("menuWrapper").style.padding = "0";
				document.getElementById("main").style.marginLeft = "0";
				document.getElementById("body").style.overflowX = "initial";
				
			}
			
			function login(group){
				if(group != "" && group != " " && group != null && group != "null" ){
					document.cookie = "group="+group.toLowerCase().replace(" ", "_");
					window.location.href="?";
				}
				
			}
		</script>
		<?php
			
			$groupName = "N/A";
			if(isset($_GET["group"])){
				$groupName = $_GET["group"];
			}
			elseif(isset($_COOKIE["group"])){
				$groupName = $_COOKIE["group"];
			}			
			
			
			//update recents |
			//               \/
			
			$recents = array_unique (explode("_", $_COOKIE["recents"]."_".$groupName));
			$recents = implode("_", $recents);
			
			setcookie("recents", $recents);
						
			$maxId = 0;
			$allItems = array();
			class Item{
				function __construct($id, $name, $price, $delete){
					$this->id = $id;
					$this->name = $name;
					$this->price = $price;
					$this->delete = $delete;					
					
					global $allItems;
					array_push($allItems, $this);
				}
				
			}
			
			if($groupName != "N/A"){
				//loop through each line. Each line is a class
				for($i = 0; $i < sizeof(file('group/'.$groupName.'.txt')); $i++) {
					$exploded = explode(";", str_replace("\n", '', file('group/'.$groupName.'.txt')[$i]));
					
					if($exploded[0] != ""){//stop empty classes being made
						new Item($exploded[0], $exploded[1], $exploded[2], false);
						
						if($exploded[0] > $maxId){//make sure every ID is unique
							$maxId = $exploded[0];
						}
					}
				}
				
				
				//set class with matching id to delete
				if(isset($_GET["delete"])){
					$id = $_GET["delete"];
					for($i = 0; $i < sizeof($allItems); $i++){
						if($allItems[$i]->id == $id){
							$allItems[$i]->delete = true;
						}
					}
				}
				if(isset($_GET["add"])){
					$add = $_GET["add"];
					//add 1 to max ID to every ID is unique
					//This means there may be gaps in IDs so not efficient. Can optimize.
					new Item($maxId+1, $add,  "null");
				}
				
				$url = "group/".$groupName.".txt";
				$outputString = "";
				foreach($allItems as $item){
					if($item->name != ""){//Bug where there are empty classes
						if(!($item->delete))
							$outputString .= $item->id . ";" . $item->name . ";" . $item->price . "\n";
					}
				}
				exec("echo '" . $outputString . "' > " . $url);
				
				if(isset($_GET["delete"]) || isset($_GET["add"])){
					header("Refresh:0; url=?");
				}
			}
		?>
	</head>
	
	<body id="body">
		<div id="menuWrapper">
			<span>
				<ul>
					<h1>Group</h1>
					<li>
					<center><input type="name" id="groupInput" value="<?php
						echo $groupName;
					?>"></center></li>
					<li><center><button onclick="makeCookie('group', document.getElementById('groupInput').value.toLowerCase().replace(' ', '_'));">Enter</button></center></li>
				</ul>
				<ul id="recents">	
					<h1>Recents</h1>
					
					<?php
						
						$recents = explode("_", $_COOKIE["recents"]);
						$counter = 0;
						for($i = count($recents); $i > 0; $i--){
							if($recents[$i] != ""){
								echo "<a onclick=\"makeCookie('group', '" . $recents[$i] . "')\"><li>" . $recents[$i] . "</span></li></a>";
								$counter++;
							}
							if($counter>4){
								break;
							}
						}
					?>
				</ul>
			</span>
			
			
			<div style="text-align:center; font-size:40px; padding:20px;">
				<center><button onclick="clearCookie('group')">Sign Out</button></center>
<!--
				<a style="color:white; text-decoration:none;" href="http://86.27.29.21" class="static"><b>Benjamin Thomas - 2016</b></a>
-->
			</div>
		</div>
		<div id="screen" onclick="closeNav()"></div>
		
		<div id="main">
			<header>
				
				<span id="menuButton" onclick="openNav()"></span>
				<span class="title"><?php 
				if($groupName != "N/A"){
					echo str_replace("_", " ", $groupName);
				}else{
					echo "Shopping list";
				}
				
				
				?></span>
				
				
				<span id="addItem" onclick="addItem()"></span>
			
			</header>
			
			
			<div class="content">
				<?php 
					if($_COOKIE["group"] == ""){
						echo "<h1 style='padding-top:100px;'>Join or create a group!</h1>";
						
					}
					elseif($allItems == []){
						echo "<h1>Add some items!</h1>";
					}
				?>
				<ul class="shopping">
					<?php					
						foreach($allItems as $item){
							echo "<li> " . $item->name . "<span class=\"removeItem\" onclick=\"deleteItem(" . $item->id . ", '" . $item->name . "')\"'></span></li>\n";
						}
					?>
					
					
				</ul>			
			</div>
		</div>
	</body>
</html>