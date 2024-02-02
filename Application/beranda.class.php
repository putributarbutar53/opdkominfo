<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class beranda extends Core
{
	function __construct()
	{
		parent::__construct();

		$this->LoadModule("Page");
		$this->LoadModule("Options");
		$this->LoadModule("Banner");
		$this->LoadModule("Content");
		$this->LoadModule("Polling");
		$this->Template->assign("dirNews", $this->Config['news']['dir']);
		$this->Template->assign("dirContent", $this->Config['content']['dir']);
		$this->Template->assign("dirBanner", $this->Config['upload']['bannerdir']);

		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(20, 10, "&laquo; Prev", "Next &raquo;");

		//Load General Process
		include '../inc/general.php';

		//Load Plugin
		$this->LoadPlugin("CAPTCHA");
		$this->Template->assign("urlWEB", $this->Config['sub']['domain']);
	}
	function main()
	{

		// $this->Template->assign('style_2', 1);
		// $this->Template->assign('listBanner', $this->Module->Banner->listBanner(1));
		// $this->Template->assign('listPopuler', $this->Module->Content->listView(12));
		// $this->Template->assign('listVideo', $this->Module->Content->listContentVideo(2));
		// $this->Template->assign('listTerkini', $this->Module->Content->listNewContent(10));
		// $this->Template->assign('listKliping', $this->Module->Content->listContentByCategory('kliping'));
		// $this->Template->assign('listEvent', $this->Module->Content->listContentByCategory('event'));
		// $this->Template->assign('listPemerintah', $this->Module->Content->listContentByCategory('pemerintah'));
		// $this->Template->assign('Detail', $this->Module->Polling->detail($this->Id));
		echo $this->Template->Show("404.html");
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
}
