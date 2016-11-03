<?php
if (isset($_GET['page'])) {$page = $_GET['page'];} else {$page = 'home';}
if  ($page == 'home') { //Home Page
	
} 
elseif  ($page == 'view') {
	include "core/viewer.php";
} 
elseif  ($page == 'api') { //Api
	include "core/api.php";
}
?>