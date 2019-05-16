<?php
if(!defined("_INCLUDED_")) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

define('TODO_IMPENDING_DATE', date("Y-m-d", strtotime(("+ " . Todo::IMPENDING_DATE_INTERVAL . " day"), _TIME_)));

class Todo {
	const IMPENDING_DATE_INTERVAL	= 1;
	const IMPENDING_DATE			= TODO_IMPENDING_DATE;

	private $no;
	private $subject;
	private $content;
	private $deadline;
	private $star;
	private $is_done;
	private $registered_at;
	
	static public function are_columns_invalid($params) {
		$break_point = false;

		foreach($params as $key => $value) {
			switch($key) {
				case "subject":
					$break_point = $value !== null && (mb_strlen($value) > 60 || mb_strlen($value) < 1);
					break;
				case "content":
					$break_point = $value !== null && (mb_strlen($value) > 65535 || mb_strlen($value) < 1);
					break;
				case "deadline":
					$break_point = $value !== null && $value != "" && (!preg_match("/^2\d{3}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[0-1])$/", $value) || $value < _DATE_);
					break;
				case "star":
					$break_point = $value !== null && !in_array($value, ["0","1","2"]);
					break;
				case "is_done":
					$break_point = $value !== null && !in_array($value, ["0","1"]);
					break;
				case "is_impending":
					$break_point = $value !== null && !in_array($value, ["impending","dead"]);
					break;
			}

			if($break_point)
				return $key;
		}

		return false;
	}

	static public function insert($subject, $content, $deadline, $star) {
		if($deadline == "")		// deadline은 null이 될 수 있음
			$deadline = null;

		if($invalid_param = self::are_columns_invalid(
			[
				"subject"	=> $subject,
				"content"	=> $content,
				"deadline"	=> $deadline,
				"star"		=> $star
			]
		))
			throw new InvalidParamException($invalid_param);

		DB::query(
			"INSERT INTO `todo` SET
				`td_subject`		= :subject,
				`td_content`		= :content,
				`td_deadline`		= :deadline,
				`td_star`			= :star,
				`td_is_done`		= 0,
				`td_registered_at`	= :registered_at",
			[
				"subject"		=> $subject,
				"content"		=> $content,
				"deadline"		=> $deadline,
				"star"			=> $star,
				"registered_at"	=> _DATETIME_
			]
		);

		$insert_id = DB::insert_id();

		return self::get_todo($insert_id);
	}

	static public function get_list($star=null, $is_impending=null, $is_done=null) {
		$sql_where	= " 1 ";
		$sql_order	= " `td_star` DESC, `td_is_done` ASC, `td_deadline` ASC, `td_no` DESC ";
		$sql_column	= [];
		$result		= [];

		if($star !== null) {
			if(self::are_columns_invalid(["star"=>$star]))
				throw new InvalidParamException("star");

			$sql_where .= " and `td_star` = :star ";
			$sql_column['star'] = $star;
		}
		
		if($is_impending !== null) {
			if(self::are_columns_invalid(["is_impending"=>$is_impending]))
				throw new InvalidParamException("is_impending");

			if($is_impending == "impending") {
				$sql_where .= " and `td_deadline` >= :now and `td_deadline` <= :impending_date ";

				$sql_column['now']				= _DATE_;
				$sql_column['impending_date']	= self::IMPENDING_DATE;
			} elseif($is_impending == "dead") {
				$sql_where .= " and `td_deadline` < :now ";

				$sql_column['now']	= _TIME_;
			}
		}

		if($is_done !== null) {
			$sql_where .= " and `td_is_done` = :is_done ";

			$sql_column['is_done'] = ($is_done == true);
		}

		$fetch_data = DB::fetch_all(
			"SELECT `td_no` FROM `todo` WHERE {$sql_where} ORDER BY {$sql_order}",
			$sql_column
		);
		
		foreach($fetch_data as $row) {
			$that = self::get_todo($row['td_no']);

			$result[] = $that->get_detail();
		}

		return $result;
	}

	static public function get_count() {
		$sql_wheres = [
			"all"			=>	[
									"sql"	=>	" 1 ",
									"data"	=>	[]
								],
			"star2"			=>	[
									"sql"	=>	" `td_star` = :star ",
									"data"	=>	["star"=>2]
								],
			"star1"			=>	[
									"sql"	=>	" `td_star` = :star ",
									"data"	=>	["star"=>1]
								],
			"star0"			=>	[
									"sql"	=>	" `td_star` = :star ",
									"data"	=>	["star"=>0]
								],
			"impending"		=>	[
									"sql"	=>	" `td_deadline` >= :now and `td_deadline` <= :impending_date and `td_is_done` = :is_done ",
									"data"	=>	[
													"now"				=> _DATE_,
													"impending_date"	=> self::IMPENDING_DATE,
													"is_done"			=> 0
												]
								],
			"dead"			=>	[
									"sql"	=>	" `td_deadline` < :now and `td_is_done` = :is_done ",
									"data"	=>	[
													"now"		=>_DATE_,
													"is_done"	=> 0
												]
								],
			"done"			=>	[
									"sql"	=>	" `td_is_done` = :is_done ",
									"data"	=>	["is_done"=>1]
								],
			"undone"		=>	[
									"sql"	=>	" `td_is_done` = :is_done ",
									"data"	=>	["is_done"=>0]
								]
		];
		$sql_order	= " `td_star` DESC, `td_is_done` ASC, `td_deadline` ASC, `td_no` DESC ";
		$result = [];

		foreach($sql_wheres as $category => $sql_where) {
			$row = DB::fetch(
				"SELECT count(*) as cnt FROM `todo` WHERE {$sql_where['sql']} ORDER BY {$sql_order}",
				$sql_where['data']
			);
			$result[$category] = $row['cnt'];
		}

		return $result;
	}

	static public function get_todo($no) {
		$DB_data = DB::fetch(
			"SELECT * FROM `todo` WHERE `td_no` = :no",
			["no"=>$no]
		);

		if(!$DB_data)
			throw new NonExistentTodoException;

		return new Todo($DB_data);
	}

	private function  __construct($DB_data) {
		$this->no				= $DB_data['td_no'];
		$this->subject			= $DB_data['td_subject'];
		$this->content			= $DB_data['td_content'];
		$this->deadline			= $DB_data['td_deadline'];
		$this->star				= $DB_data['td_star'];
		$this->is_done			= $DB_data['td_is_done'];
		$this->registered_at	= $DB_data['td_registered_at'];
	}

	public function get_detail() {
		return [
			"no"		=> $this->no,
			"subject"	=> $this->subject,
			"content"	=> $this->content,
			"deadline"	=> $this->deadline,
			"star"		=> $this->star,
			"is_done"	=> $this->is_done
		];
	}

	public function remove() {
		DB::query(
			"DELETE FROM `todo` WHERE `td_no` = :no",
			["no"=>$this->no]
		);

		$this->no				= null;
		$this->subject			= null;
		$this->content			= null;
		$this->deadline			= null;
		$this->star				= null;
		$this->is_done			= null;
		$this->registered_at	= null;
	}

	public function edit($subject=null, $content=null, $deadline=null, $star=null, $is_done=null) {
		if($invalid_param = self::are_columns_invalid(
			[
				"subject"	=> $subject,
				"content"	=> $content,
				"deadline"	=> $deadline,
				"star"		=> $star,
				"is_done"	=> $is_done
			]
		))
			throw new InvalidParamException($invalid_param);
		
		$sql_set	= "";
		$sql_column	= ["no"=>$this->no];
		$use_comma = false;

		if($subject !== null) {
			$sql_set .= " `td_subject` = :subject";
			$sql_column['subject'] = $subject;
		}
		if($content !== null) {
			if(count($sql_column) > 1)
				$sql_set .= " , ";
			$sql_set .= " `td_content` = :content";
			$sql_column['content'] = $content;
		}
		if($deadline !== null) {
			if(count($sql_column) > 1)
				$sql_set .= " , ";
			$sql_set .= " `td_deadline` = :deadline";
			$sql_column['deadline'] = ($deadline == "") ? null : $deadline;
		}
		if($star !== null) {
			if(count($sql_column) > 1)
				$sql_set .= " , ";
			$sql_set .= " `td_star` = :star";
			$sql_column['star'] = $star;
		}
		if($is_done !== null) {
			if(count($sql_column) > 1)
				$sql_set .= " , ";
			$sql_set .= " `td_is_done` = :is_done";
			$sql_column['is_done'] = $is_done;
		}

		if($sql_set == "")
			return;

		DB::query(
			"UPDATE `todo` SET {$sql_set} WHERE `td_no` = :no",
			$sql_column
		);
	}

	public function is_impending() {
		return !$this->is_done && ($this->deadline >= _DATE_) && ($this->deadline <= Todo::IMPENDING_DATE);
	}

	public function is_dead() {
		return !$this->is_done && ($this->deadline < _DATE_);
	}

	public function get_no() {
		return $this->no;
	}

	public function get_subject() {
		return $this->subject;
	}

	public function get_content() {
		return $this->content;
	}

	public function get_deadline() {
		return $this->deadline;
	}
}

class NonExistentTodoException extends Exception {};