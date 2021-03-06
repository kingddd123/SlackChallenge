<?php

/**
* Class that stores all response types and API callings
*
* @author   d_yoo
*/
class HttpHelper
{
	/**
	 * Response when game is intialized
	 *
	 * @param string $playerOne
	 * @param string $playerTwo
	 * @return json object
	 * @author d_yoo
	 */
	public function gameStartResponse($playerOne, $playerTwo)
	{
		$message = "A new game has begun!\n" . $playerOne . " VS " . $playerTwo; 

		$response = array(
			"response_type" => "in_channel",
			"text" 			=> $message
		);

		return json_encode($response);
	}

	/**
	 * Response when the display command is received
	 *
	 * @param string $message
	 * @param array $data represents the data from db
	 * @param string $color represents the status 
	 * @param boolean $attachTextBoolean to display attachmentText or not
	 * @return json object
	 * @author d_yoo
	 */
	public function displayResponse($message, $data, $color = "good", $attachTextBoolean = true)
	{
		foreach ($data as &$datum) {
			if (empty($datum)) {
				$datum = '_';
			}
		}

		$turn = $data['turn'];
		$color = "good";

		if ($turn == 1) {
			$playersTurn = $data['player_1'];
		} else {
			$playersTurn = $data['player_2'];
		}

		$attachmentText = '';

		if ($attachTextBoolean) {
			$attachmentText = "It is " . $playersTurn . "'s turn to play";
		}

		$row1 = $data['r1_c1'].$data['r1_c2'].$data['r1_c3'];
		$row2 = $data['r2_c1'].$data['r2_c2'].$data['r2_c3'];
		$row3 = $data['r3_c1'].$data['r3_c2'].$data['r3_c3'];

		$board = array(
			0 => array("title" => $row1), 
			1 => array("title" => $row2),
			2 => array("title" => $row3),
		);

		$attachment = array (
			array(
				"image_url" => "https://agile-chamber-35943.herokuapp.com/merged.png",
				"fields" 	=> $board,
				"color" 	=> $color,
				"text"		=> $message . "\n" . $attachmentText
			)
		);

		$response = array(
			"response_type" => "in_channel",
			"attachments" 	=> $attachment
		);

		return json_encode($response);
	}

	/**
	 * Response for game winning move
	 *
	 * @param string $winner
	 * @return json response
	 * @author d_yoo
	 */
	public function genericResponse($message)
	{
		$response = array(
			"text" 			=> $message
		);

		return json_encode($response);
	}

	/**
	 * Get the list of member ids in the general channel
	 *
	 * @return array $memberIds
	 * @author d_yoo
	 */
	public function getMembersInChannel($channelId)
	{
		$service_url = 'https://slack.com/api/channels.info';
	    $curl = curl_init($service_url);
	   
	    $curl_post_data = array(
	        'token'   => "#",
	        'channel' => $channelId
	    );

	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

	    $curl_response = curl_exec($curl);
	    curl_close($curl);

	    $data = json_decode($curl_response, true);

		return $data['channel']['members'];
	}

	/**
	 * Get the list of member
	 *
	 * @return array $members
	 * @author d_yoo
	 */
	public function getMembersList()
	{
		$service_url = 'https://slack.com/api/users.list';
	    $curl = curl_init($service_url);
	   
	    $curl_post_data = array(
	        'token' => "#"
	    );

	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

	    $curl_response = curl_exec($curl);
	    curl_close($curl);

	    $data = json_decode($curl_response, true);

		return $data['members'];
	}

}

?>