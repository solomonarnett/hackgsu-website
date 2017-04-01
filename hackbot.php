<?php 

	session_start();
	$more_clicked = 0;
	$more_link_id = null;
	$faq_clicked = 0;
	$faq_link_id = null;
	$quick_link_clicked = 0;
	$mrequest_submitted = 0;
	$completed_request_submitted = 0;
	$completed_request_data = [0];
	$mrequest_data = array(0);
	$const = 'everything';


	if (isset($_POST['mrequestSubmit']) && empty($_POST['mrequestSubmit']) == false) {
		$mrequest_submitted = 1;
		$mrequest_data[0] = 1;
		$mrequest_data[1] = $_POST['firstname'];
		$mrequest_data[2] = $_POST['email'];
		$mrequest_data[3] = $_POST['studentid'];
		$mrequest_data[4] = $_POST['description'];
		$mrequest_data[5] = $_POST['floor'];
		$mrequest_data[6] = $_POST['roomNum'];
		$mrequest_data[7] = $_POST['nearestRoom'];
		$mrequest_data[8] = $_POST['shirt'];
	}elseif (isset($_GET['more_link']) && empty($_GET['more_link']) == false) {
		$more_clicked = 1;
		$more_link_id = $_GET['more_link'];
	}elseif(isset($_GET['faq']) && empty($_GET['faq']) == false) {
		$faq_clicked = 1;
		$faq_link_id = $_GET['faq'];
	}elseif (isset($_GET['quick']) && empty($_GET['quick']) == false) {
		$quick_link_clicked = $_GET['quick'];
	}elseif(isset($_POST['completeSubmit']) && empty($_POST['completeSubmit']) == false){
		$completed_request_submitted = 1;
		$completed_request_data[0] = $_POST['completeRequestid'];
		$completed_request_data[1] = $_POST['completeEmail'];
		// echo "<script type=\"text/javascript\">console.log('". $completed_request_data[1] ."')</script>";
	}

	
	include_once("assets/php/spring2017/strings.php");
	include_once("assets/php/spring2017/hackbot-display.php");


	$search_text = $_GET['search_text'];


	$placeholder = "Search HackGSU";
	$result = "";

	if (isset($search_text) && empty($search_text) === false && $more_clicked != 1 && $faq_clicked != 1 && $quick_link_clicked < 1 && $mrequest_submitted != 1 && $completed_request_submitted !=1) {
		$placeholder = $search_text;

		$ev = new everything;

		$result = $ev -> selectFromTagsLimited($search_text);

		// echo "<script type=\"text/javascript\">console.log('result is " . $result[0]['type'] . "')</script>";

	}elseif ($more_clicked == 1 && isset($more_link_id) && empty($more_link_id) == false) {
		$placeholder = "Search for more";

		$ev = new everything;

		$result = $ev -> selectById($more_link_id);
	}elseif ($faq_clicked == 1 && isset($faq_link_id) && empty($faq_link_id) == false) {
		$placeholder = "Search for more";
		$faq = new faq;
		$result = $faq -> selectById($faq_link_id);
		$const = 'faq';
	}elseif ($quick_link_clicked > 0 && isset($quick_link_clicked) && empty($quick_link_clicked) == false) {
		switch ($quick_link_clicked) {
			case '1':
				$faq = new faq;
				$result = $faq -> selectById(16);
				$const = 'faq';
				break;
			
			case '2':
				$events = new events;
				$result = $events -> nextEvent();
				$const = 'event';
				$placeholder = 'So much info. Search for more?';
				// echo "<script type=\"text/javascript\">console.log('". $result[0]['title'] ."');</script>";
				break;

			case '3':
				$events = new events;
				$result = $events -> nextFood();
				$const = 'event';
				$placeholder = 'Hungry? I am too! Feed me queries.';
				// echo "<script type=\"text/javascript\">console.log('". $result[0]['title'] ."');</script>";
				break;

			case '4':
				// $ev = new everything;
				// $result = $ev -> selectFromTagsLimited('travel reimbursement');
				// break;
				$faq = new faq;
				$result = $faq -> selectById(12);
				$const = 'faq';
				break;

			case '5':
				$faq = new faq;
				$result = $faq -> selectById(7);
				$const = 'faq';
				break;

			case '7':
				$placeholder = 'Mentor Request';
				$result = [0];
				$const = 'mrequest';
				break;
			
			default:
				# code...
				break;
		}
	}elseif ($mrequest_submitted == 1 && isset($mrequest_submitted) && empty($mrequest_submitted) == false) {
		$result = $mrequest_data;
		$const = 'mrequest';
	}elseif ($completed_request_submitted == 1 && isset($completed_request_submitted) && empty($completed_request_submitted) == false) {
		$ev = new everything;
		$mentor = new mentors;
		$id = substr($completed_request_data[0], 6);
		// echo "<script type=\"text/javascript\">console.log('".$completed_request_data[1] ."')</script>";
		$result = $mentor -> validateEmailByRequest($id, $completed_request_data[1]);
		$search = $completed_request_data[0];
		$result = $ev -> selectFromTagsLimited($search);
	}


?>




<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="assets/css/spring2017/hackbot.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<title>hackbot</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
</head>
<body>
	<form id="" action="" method="get">
		<div class="search_box">
			<input id="search_text" type="text" name="search_text" placeholder=<?php echo "\"$placeholder\""; ?>>
			<input id="hackbot_submit" type="submit" name="submit" value="">
		</div>
	</form>
	<?php 
		if (empty($result) == false) {
	?>
			<div class="top_result">
				<div class="label">Top Result</div>
				<?php
					if ($mrequest_submitted == 1) {
						// echo "<script type=\"text/javascript\">console.log('mrequest_submitted');</script>";
						// echo "<script type=\"text/javascript\">console.log('". sizeof($result) ."');</script>";
						hackbot_display($result, $const);
						$result = array(1);
					}else{
						hackbot_display($result[0], $const); 
					}
					// echo "<script type=\"text/javascript\">console.log('". $result[0]['id'] ."');</script>";
				?>

			</div>
			<div class="more_results">
				<div class="label">More Results</div>
				<?php $counter = 0; 

				if (sizeof($result) < 2) {
					echo "<div class=\"none\">No more results</div>";
					
				}else{
					foreach ($result as $key => $more) {
						if ($counter == 4) {
							break;
						}
						if ($counter == 0){
							$counter ++;
							continue;
						}else{
							?>
							<form class="more_form" action="" method="get">
								<input id=<?php $str = "more_link"; $str .= $more['id']; echo "'". $str. "'"; ?> type="submit" name="more_link" value=<?php echo "'" . $more['id'] . "'"; ?>>
								<label for=<?php echo "'$str'"; ?>>
									<?php
									hackbot_display($more, $const);
									?>
								</label>	
							</form>
							<?php
						}
						$counter ++;
					}
				}

				 ?>
			</div>
	<?php
	
		}else{
	?>
			<div class="welcome">Search Everything HackGSU!</div>
			
	<?php
		}
		?>
		<div class="quick_links">
			<div class="label">Quick Links</div>
			<form action="" method="get">
				<input id="quick_mentor" type="submit" name="quick" value="7">
				<label for="quick_mentor">
					<div class="link">Request a Mentor</div>
				</label>
				<input id="quick_parking" type="submit" name="quick" value="1">
				<label for="quick_parking">
					<div class="link">Parking</div>
				</label>
				<input id="quick_next" type="submit" name="quick" value="2">
				<label for="quick_next">
					<div class="link">Next Event</div>
				</label>
				<input id="quick_food" type="submit" name="quick" value="3">
				<label for="quick_food">
					<div class="link">When's food?</div>
				</label>
				<input id="quick_travel" type="submit" name="quick" value="4">
				<label for="quick_travel">
					<div class="link">Travel Reimbursement</div>
				</label>
				<input id="quick_registered" type="submit" name="quick" value="5">
				<label for="quick_registered">
					<div class="link">Am I registered?</div>
				</label>
				<input id="quick_devpost" type="submit" name="quick" value="6">
				<label class="devpostLabel" for="quick_devpost">
					<a class="link devpost" style="text-decoration: none" target="_blank" href="https://hackgsu-spring-2017.devpost.com/?ref_content=default&ref_feature=challenge&ref_medium=discover">
						<!-- <label for="navigator"> -->
							<span class="link devpost">Devpost</span>
						<!-- </label> -->
					</a>
				</label>
				
			</form>
		</div>
	<?php

		if (empty($result) == false) {
			echo "<script type=\"text/javascript\">$('.quick_links').removeClass('bottom');</script>";
		}else{
			echo "<script type=\"text/javascript\">$('.quick_links:not(.bottom)').addClass('bottom');</script>";
		}
		// if (isset($search_text) && empty($search_text) === false) {
			// echo "<script type=\"text/javascript\">$('.quick_links').removeClass('bottom');</script>";
		// }else{
			// echo "<script type=\"text/javascript\">$('.quick_links:not(.bottom)').addClass('bottom');</script>";
		// }
	?>
	
<script type="text/javascript" src="assets/js/spring2017/main.js"></script>
<script type="text/javascript" src="assets/js/spring2017/hackbot.js"></script>
</body>
</html>