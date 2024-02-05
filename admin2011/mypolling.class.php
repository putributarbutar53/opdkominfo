<?php if (!defined('ONPATH'))
	exit('No direct script access allowed'); //Mencegah akses langsung ke class

class mypolling extends Core
{
	var $Submit, $Action, $Id;
	public function __construct()
	{
		parent::__construct();
		ob_clean();

		$this->LoadModule("Date");
		$this->LoadModule("Paging");
		$this->LoadModule("Polling");
		//Load General Process
		include '../inc/general_admin.php';
		$this->Module->Paging->setPaging(21, 5);

		$this->Template->assign("dirProducts", $this->Config['upload']['products']);
		$this->Template->assign("Signature", "polling");
	}

	function main()
	{
		echo $this->Template->ShowAdmin("polling/index.html");
	}

	function polling()
	{
		$action = htmlspecialchars($_POST['action']);
		$status_message = false;
		if ($action == 'form-polling') {
			$user_ip = $_SERVER['REMOTE_ADDR'];
			$detail = $this->Db->sql_query_array("SELECT * FROM cppolling WHERE user_ip='$user_ip' LIMIT 1");
			// if ($detail['id'])
			// 	$this->Db->update(['option_name' => htmlspecialchars($_POST['poll_option']), 'user_ip' => $_SERVER['REMOTE_ADDR']], $detail['id'], 'cppolling');
			// else
			$this->Db->add(['option_name' => htmlspecialchars($_POST['poll_option']), 'user_ip' => $_SERVER['REMOTE_ADDR']], 'cppolling');
			$status_message = true;
		}
		$sangat_informatif = $this->Db->sql_query_array("SELECT COUNT(*) AS total FROM cppolling WHERE option_name='sangat_informatif'");
		$cukup_informatif = $this->Db->sql_query_array("SELECT COUNT(*) AS total FROM cppolling WHERE option_name='cukup_informatif'");
		$informatif = $this->Db->sql_query_array("SELECT COUNT(*) AS total FROM cppolling WHERE option_name='informatif'");
		$kurang_informatif = $this->Db->sql_query_array("SELECT COUNT(*) AS total FROM cppolling WHERE option_name='kurang_informatif'");
		$data = [
			'sangat_informatif' => $sangat_informatif['total'],
			'cukup_informatif' => $cukup_informatif['total'],
			'informatif' => $informatif['total'],
			'kurang_informatif' => $kurang_informatif['total']
		];
		if ($status_message) {
			$data['message'] = 'Polling berhasil telah terkirim';
		}
		echo json_encode($data);
	}
	function add()
	{
	}
	function edit()
	{
	}
	function delete()
	{
	}
	function submit()
	{
	}
}
