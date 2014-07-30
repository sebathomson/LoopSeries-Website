<?php
include(dirname(__FILE__) . '/../header.php');

if(!$user_obj->getIsLogged())
	header("location: ../index.php");

$full_screen = true;

include(dirname(__FILE__) . '/../top.php');
?>

<div class="col-lg-12 content_box" style="padding:0px;">
	<div class="col-lg-3" style="margin:0px;padding:0px;border-right:1px solid #EEEEEE;border-bottom:1px solid #EEEEEE;">
		<ul class="nav nav-pills nav-stacked user_cp_ul">
		  <li><a href="home.php" target="side_content"><div class="glyphicon glyphicon-user pull-left"></div>&nbsp;&nbsp;Account</a></li>
		  <li><a href="favorite_list.php" target="side_content"><div class="glyphicon glyphicon-star pull-left"></div>&nbsp;&nbsp;Favorite List</a></li>
		  <li><a href="track_system.php" target="side_content"><div class="glyphicon glyphicon-list pull-left"></div>&nbsp;&nbsp;Track System</a></li>
		  <li><a href="preferences.php" target="side_content"><div class="glyphicon glyphicon-cog pull-left"></div>&nbsp;&nbsp;Preferences</a></li>
		  <!--  <li><a href="messages.php" target="side_content"><span class="badge pull-right">0</span><div class="glyphicon glyphicon-envelope pull-left"></div>&nbsp;&nbsp;Messages</a></li>
		  <li><a href="commentst.php" target="side_content"><span class="badge pull-right">0</span><div class="glyphicon glyphicon-comment pull-left"></div>&nbsp;&nbsp;Comments</a></li>-->
		  <li class="divider"><hr></li>
		</ul>
		<ul class="nav nav-pills nav-stacked">
			<li><a href="../login.php?op=logout"><div class="glyphicon glyphicon-off pull-left"></div>&nbsp;&nbsp;Logout</a></li>
		</ul>
	</div>
	<div class="col-lg-9" style="padding:0px;" id="side_content">
	</div>
</div>

<script type="text/javascript">

	function submit_form(form) {

		var ajax = $(form).attr('ajax');
		
		$.ajax({url: $(form).attr('action'), type: 'GET', data: $(form).serialize()}).success(function(data) {
	
			if(ajax == "false")
				$('#side_content').html(data);
			else { 
				if(data == "OK")
					$('ul.user_cp_ul li.active').trigger("click");
				else
					alert(data);
			}
		});
	}

	$(document).ready(function(e) {

		$('ul.user_cp_ul li').click(function(e) {
			e.preventDefault();
			if($(this).find('a').attr('href') != "") {
				$(this).parents().find('li.active').removeClass('active');
				$(this).addClass('active');
				$('#side_content').html("<div class='row loader_div'></div>");
				$('#side_content').load($(this).find('a').attr('href'));
			}
		});
		
		$('#side_content').on('submit','form', function(e) {
			e.preventDefault();

			submit_form($(this));	
			
			return false;
		});
		
		$('#side_content').on('click','a', function(e) {

			if($(this).hasClass("follow"))
				return true;
			
			if($(this).hasClass("anchor"))
				return false;
			
			e.preventDefault();

			$('#side_content').load($(this).attr('href'));
		});

		$('ul.user_cp_ul li:first').trigger("click");

	});
</script>

<?php
include(dirname(__FILE__) . '/../bottom.php');
?>