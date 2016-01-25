<?php

class CanvasWrapper
{
	private $canvas;
	
	public function __construct() {
		require_once('classes/canvas.php');
		$this->canvas = new Canvas();
	}
	
	/*
	 * takes json result and formats it for printing
	 * 
	 */ 
	public function formatCourseData() {
			
		$this->canvas->getCoursesForUser();

		foreach ($this->canvas->getData() as $data) {
			$splitName = $this->splitCourseName($data->name);
			
			//split OSU Id for checking and printing
			$splitArr = explode(" ", $splitName[1]);
			//don't print course unless it's this term
			if ($this->checkCourseTerm($splitArr[4])) {
				$this->buttonMaker($data->id, $splitName, true);
			}
		}
	}
	
	/*
	 * takes json result for canvas user and formats it
	 * 			returns a user object
	 * 
	 * @return $user object{
	 * 					name: user's name
	 * 					email: user's OSU email
	 * 					user_id: user's OSU Id
	 * 					canvas_id: user's Canvas Id	
	 * 		   		 }
	 * 
	 */ 
	public function formatUserData() {
		
		$this->canvas->getUserInfo();
		$data = $this->canvas->getUserInfo();
		
		$user = new stdClass();
		$user->name = $data->name;
		$user->email = $data->login_id;
		$user->user_id = $data->sis_user_id;
		$user->canvas_id = $data->id;
		return $user;
	}
	
	public function printUserName() {
		$user = $this->formatUserData();
		echo "<div class='well well-sm'>";
		echo "<span>Welcome $user->name. To begin, select a course below.</span>";
		echo "</div>";
	}
	
	
	/*
	 * gets the current OSU Term i.e. W2016 and compares
	 * 			against term passed in
	 * @param $term and OSU Term: W2016
	 * 
	 * @return bool true on term equal, false otherwise 
	 * 
	 */ 
	private function checkCourseTerm($term) {
		//get current month
		$currentMonth = date("m");
		 
		//retrieve season in OSU Term format
		if ($currentMonth >= "04" && $currentMonth <= "06") {
			$season = "S";
		}
		elseif ($currentMonth >= "06" && $currentMonth <= "08") {
			$season = "U";
		}
		elseif ($currentMonth >= "09" && $currentMonth <= "11") {
			$season = "F";
		}
		else {
			$season = "W";
		}
		
		//get current year
		$currentYear = date("Y");
		$curTerm = $season . $currentYear;
		
		if ($term == $curTerm) {
			return true;
		}
		
		return false;
	}
	
	/*
	 * takes a course title and splits the name and the OSU ID
	 * @param $title string in the format of 'Computer Science (CS_457_X001_W2016)'
	 * 
	 * @return array of two items, array[0] = name, array[1] = OSU ID
	 * 
	 */ 
	private function splitCourseName($title) {
		//pattern matches a string like: (CS_457_X001_W2016)
		$pattern = "/\(?[a-zA-Z]{2,}\_[0-9]{3}\_[a-zA-Z0-9]{3,}\_[a-zA-Z0-9]{5}\)?/";
		
		$nameArray = array();
		$result = explode('(', $title);
		$nameArray[] = $result[0];
		
		//converts (CS_457_X001_W2016) to CS 457 X001 W2016
		if (preg_match($pattern, $title, $match)) {
			$splitParen = substr($match[0], 1, -1);
			$splitUnder = explode('_',$splitParen);
			$result = '';
			foreach ($splitUnder as $item) {
				$result = $result . ' ' . $item;
			}
			$nameArray[] = $result;
		}
		
		return $nameArray;
	}
	
	/*
	 * directly echos button content to page
	 * @param $id string an id for the button
	 * @param $title array $title[0] = name, $title[1] = OSU ID
	 * @param $rowWrap bool whether or not the buttons should be wrapped in a row
	 * 					defaults to no wrapping
	 * 
	 */
	private function buttonMaker($id, $title, $rowWrap = false) {
		if ($rowWrap) {
			echo "<div style='margin-top:5px;'>";
			echo "<button id='" . $id . "' type='button' data-id='" . $title[1] . "' data-name='" . $title[0] . "' class='btn btn-default courseSwitch'>" . $title[0] . "</button>";
			echo "</div>";
		}
		else {
			echo "<button id='" . $id . "' type='button' data-id='" . $title[1] . "' data-name='" . $title[0] . "' class='btn btn-default courseSwitch'>" . $title[0] . "</button>";
		}
	}
}
