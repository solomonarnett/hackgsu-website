<?php 

	session_start();

	include_once("assets/php/spring2017/functions.php");

	function hackbot_display($data, $const){
		switch ($const) {
			case 'everything':
				$dataId = $data['typeid'];
				switch ($data['type']) {
					case 'event':
						hackbot_event($dataId);
						break;
					case 'faq':
						hackbot_faq($dataId);
						break;
					case 'mrequest':
						hackbot_mrequest_search($dataId);
					default:
						hackbot_plain($dataId);
						break;
				}
				break;
			case 'event':
				hackbot_event($data['id']);
				break;
			case 'faq':
				hackbot_faq($data['id']);
				break;
			case 'mrequest':
				hackbot_mentor($data);
				break;
			default:
				# code...
				break;
		}
		
	}



	function hackbot_event($id){
		$events = new events;
		$result = $events -> selectById($id);
		if (empty($result) == false) {
			?>
			<div class="event">
				<table>
					<tbody>
						<tr>
							<td class="time">
								<?php echo getTime($result[0]['date']);  ?>
							</td>
							<td class="title">
								<?php echo $result[0]['title']; ?>
							</td>
						</tr>
						<tr>
							<td class="location" colspan="2">
								<?php echo "Location: " . $result[0]['location']; ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php
		}else{
			// return false;
			echo "<script type=\"text/javascript\">console.log('no event')</script>";
		}
	}

	function hackbot_faq($id){
		$faq = new faq;
		$result = $faq -> selectById($id);
		if (empty($result) == false) {
			?>
			<div class="faq">
				<?php

				echo "
					<div class=\"title\">" . $result[0]['title'] . "</div>
					<div class=\"content\">" . $result[0]['content'] . "</div>
				";
				?>
			</div>
			<?php
			// return true;
		}else{
			// return false;
		}
	}

	function hackbot_mentor($data){
		if ($data[0] == 0) {
			?>
			<div class="mrequest">
				<form action="" method="POST" id="mrequest">
					<input type="text" name="firstname" placeholder="Your name">
					<input type="email" name="email" placeholder="Best contact email">
					<input type="number" name="studentid" placeholder="Student ID (optional)">
					<textarea name="description" form="mrequest" placeholder="Tell me what you need help with..."></textarea>
					<label for="floor">What floor are you on?</label>
					<input type="number" name="floor" value="1" id="floor">
					<input type="checkbox" name="roomyn" id="roomyn" class="checkbox">
					<label for="roomyn" id="checkbox-label"> <- Check the box if you're in a room?</label>
					<label for="roomNum" class="roomin">What room are you in?</label>
					<input type="number" name="roomNum" placeholder="000" id="roomNum" class="roomin">
					<label for="nearestRoom" class="near">What room are you near?</label>
					<input type="number" name="nearestRoom" placeholder="000" class="near">
					<input type="text" name="shirt" placeholder="Shirt color?">
					<input type="submit" name="mrequestSubmit" id="mrequestSubmit">
					<label for="mrequestSubmit" value="submit" id="submitLabel">Submit Request</label>
				</form>
			</div>
			<?php
		}
		elseif ($data[0] == 1) {
			// echo "<script type=\"text/javascript\">console.log('result is successful')</script>";
			$mentor = new mentors;
			$result = $mentor -> createRequest($data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8]);
			displayMentorRequest($result);
		}
	}

	function hackbot_mrequest_search($id){
		$mentor = new mentors;
		$result = $mentor -> checkStatusById($id);
		displayMentorRequest($result);
	}

	function hackbot_plain($id){

	}
	function displayMentorRequest($result){
		if (empty($result) == false) {
				?>
				<div class="mrequest_status">
					<?php 
						// echo "<script type=\"text/javascript\">console.log('".$result[0]['status']."')</script>";
						// echo "<script type=\"text/javascript\">console.log('".var_dump($result)."')</script>";
						switch ($result[0]['status']) {
							case 'waiting':
								$message = "I'm matching you with a mentor with the skills to best assist you. It should be just a few minutes.<br><br>Check your request anytime by searching \"<font class=\"mcolor\">MENTOR" . $result[0]['id'] . "</font>\" in the search box above.";
								break;
							case 'accepted':
								$mentor = new mentors;
								$mentor = $mentor -> getMentorNameById($result[0]['id']);
								$message = $mentor . " will be with you shortly<br><br>Check your request anytime by searching \"<font class=\"mcolor\">MENTOR" . $result[0]['id'] . "</font>\" in the search box above.";
								break;
							case 'completed':
								$message = "This message has been marked \"completed\". If you think this is an error, please visit the volunteer booth.";
								break;
							case 'cancelled':
								# code...
								break;
							case 'closed':
								$message = "All mentors have gone for the day. If you need assistance, please visit the volunteer desk on the ground floor. They'd be happy to help you there.";
								break;
							
							default:
								# code...
								break;
						}
					?>
					<table>
						<tbody>
							<tr>
								<th>Name</th>
								<th>Floor</th>
								<th>Room</th>
								<th>Status</th>
							</tr>
							<tr>
								<td><?php echo $result[0]['student_name']; ?></td>
								<td><?php echo $result[0]['floor']; ?></td>
								<td><?php 
									if (empty($result[0]['room'])) {
										echo $result[0]['nearest_room'];
									}else{
										echo $result[0]['room'];
									}
								?></td>
								<td><?php echo $result[0]['status']; ?></td>
							</tr>
						</tbody>
					</table>
					<span class="message"><?php echo $message; ?></span>
					<?php if ($result[0]['status'] == 'accepted') {
						# code...
					?>
						<div class="buttonContainer">
							<div class="completeButton actionButton">Complete Request</div>
							<form id="completeButtonForm" action="hackbot.php" method="POST">
								<input type="hidden" name="completeRequestid" <?php echo "value=\"MENTOR".$result[0]['id']."\""; ?> >
								<input type="email" name="completeEmail" placeholder="Enter Mentor Email">
								<input type="submit" name="completeSubmit" value="submit" id="completeSubmit">
								<label for="completeSubmit">
									<div class="completeSubmitButton">Complete Request</div>
								</label>
								<span>If you were returned to this page after submission, you may have entered the wrong email.<br><br>Enter the email address that you used to register as a mentor. If you have any other questions, contact Solo at the volunteer desk.</span>
							</form>
							<div class="deferButton actionButton">Defer Request</div>
							<form id="deferButtonForm" action="hackbot.php" method="POST">
								<input type="hidden" name="deferRequestid" <?php echo "value=\"MENTOR".$result[0]['id']."\""; ?> >
								<input type="email" name="deferEmail" placeholder="Enter Mentor Email">
								<input type="submit" name="deferSubmit" value="submit" id="deferSubmit">
								<label for="deferSubmit">
									<div class="deferSubmitButton">Defer Request</div>
								</label>
								<span>You can only defer one request at a time. If you defer this request, it will be assigned to the next available mentor.<br><br>Please do not take advantage of this feature as we would like for all participants to get as much help as possible.<br><br>If you were returned to this page after submission, you may have entered the wrong email.<br><br>Enter the email address that you used to register as a mentor. If you have any other questions, contact Solo at the volunteer desk.</span>
							</form>
						</div>
						
						<script type="text/javascript">
							$(document).ready(function(){
								$('.completeButton').click(function(){
									$('.actionButton').css('display', 'none');
									$('#completeButtonForm').css('display', 'block');
								});
								$('.deferButton').click(function(){
									$('.actionButton').css('display', 'none');
									$('#deferButtonForm').css('display', 'block');
								});
							});
						</script>
						<?php 
						}
						?>
				</div>
				<?php
			}
	}



?>