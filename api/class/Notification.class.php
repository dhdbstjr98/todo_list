<?php
if(!defined("_INCLUDED_")) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

include_once("./class/Todo.class.php");

class Notification {
	private $no;
	private $type;
	private $td_no;
	private $datetime;

	static public function are_columns_invalid($params) {
		$break_point = false;

		foreach($params as $key => $value) {
			switch($key) {
				case "type":
					$break_point = $value !== null && !in_array($value, ["register", "impending", "dead", "done", "edit", "star", "hello"]);
					break;
				case "td_no":
					try {
						Todo::get_todo($value);
					} catch(NonExistentTodoException $e) {
						$break_point = true;
					}
					break;
			}

			if($break_point)
				return $key;
		}

		return false;
	}

	static public function insert($type, $td_no) {
		DB::query(
			"INSERT INTO `notification` SET
				`nt_type`			= :type,
				`td_no`				= :td_no,
				`nt_registered_at`	= :registered_at",
			[
				"type"			=> $type,
				"td_no"			=> $td_no,
				"registered_at"	=> _DATETIME_
			]
		);

		$insert_id = DB::insert_id();

		return self::get_notification($insert_id);
	}

	static public function remove($type, $td_no) {
		DB::query(
			"DELETE FROM `notification` WHERE `nt_type` = :type and `td_no` = :td_no",
			[
				"type"			=> $type,
				"td_no"			=> $td_no
			]
		);
	}

	static public function has($type, $td_no) {
		$row = DB::fetch(
			"SELECT `nt_no` FROM `notification` WHERE `nt_type` = :type and `td_no` = :td_no",
			[
				"type"			=> $type,
				"td_no"			=> $td_no
			]
		);
		return $row == true;
	}

	static public function get_list() {
		$fetch_data = DB::fetch_all("SELECT `nt_no` FROM `notification` WHERE 1 ORDER BY `nt_no` DESC");
		$result = [];
		
		foreach($fetch_data as $row) {
			$that = self::get_notification($row['nt_no']);

			$result[] = $that->get_detail();
		}

		return $result;
	}

	static public function get_notification($no) {
		$DB_data = DB::fetch(
			"SELECT * FROM `notification` WHERE `nt_no` = :no",
			["no"=>$no]
		);

		if(!$DB_data)
			throw new NonExistentNotificationException;

		return new Notification($DB_data);
	}

	static public function trace_impending($td_no=null) {
		if($td_no === null) {	// 전체조회
			$impending_list = DB::fetch_all(
				"SELECT `td_no` FROM `todo` WHERE
					`td_is_done` = :is_done and
					`td_deadline` >= :now and
					`td_deadline` <= :impending_date and
					`td_no` NOT IN (
						SELECT DISTINCT `td_no` FROM `notification` WHERE `nt_type` = :type
					)",
				[
					"is_done"			=> 0,
					"now"				=> _DATE_,
					"impending_date"	=> Todo::IMPENDING_DATE,
					"type"				=> "impending"
				]
			);

			foreach($impending_list as $row)
				self::insert("impending", $row['td_no']);
		} else {	// 개별조회
			if(self::are_columns_invalid(["td_no"=>$td_no]))
				throw new InvalidParamException("td_no");

			$todo = Todo::get_todo($td_no);
			if($todo->is_impending() && !self::has("impending", $td_no))
				self::insert("impending", $td_no);
			elseif(!$todo->is_impending() && self::has("impending", $td_no))
				self::remove("impending", $td_no);
		}
	}

	static public function trace_dead($td_no=null) {
		if($td_no === null) {	// 전체조회
			$impending_list = DB::fetch_all(
				"SELECT `td_no` FROM `todo` WHERE
					`td_is_done` = :is_done and
					`td_deadline` < :now and
					`td_no` NOT IN (
						SELECT DISTINCT `td_no` FROM `notification` WHERE `nt_type` = :type
					)",
				[
					"is_done"	=> 0,
					"now"		=> _DATE_,
					"type"		=> "dead"
				]
			);

			foreach($impending_list as $row)
				self::insert("dead", $row['td_no']);
		} else {	// 개별조회
			if(self::are_columns_invalid(["td_no"=>$td_no]))
				throw new InvalidParamException("td_no");

			$todo = Todo::get_todo($td_no);
			if($todo->is_dead() && !self::has("dead", $td_no))
				self::insert("dead", $td_no);
			elseif(!$todo->is_dead() && self::has("dead", $td_no))
				self::remove("dead", $td_no);
		}
	}
	
	private function  __construct($DB_data) {
		$this->no				= $DB_data['nt_no'];
		$this->type				= $DB_data['nt_type'];
		$this->td_no			= $DB_data['td_no'];
		$this->registered_at	= $DB_data['nt_registered_at'];
	}

	public function get_detail() {
		if($this->type != "hello")
			$todo = Todo::get_todo($this->td_no);

		return [
			"type"		=> $this->type,
			"subject"	=> (($this->type != "hello")
								? $todo->get_subject()
								: "이곳에서 알림을 확인하실 수 있습니다."),
			"star"		=> (($this->type != "hello")
								? $todo->get_star()
								: null)
		];
	}
}

class NonExistentNotificationException extends Exception {};