<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class berita extends Core
{
	public function __construct()
	{
		parent::__construct();

		$this->LoadModule("Content");

		include '../inc/general_api.php';
	}

	function main()
	{
		$Status = array('status' => 'success');
        
        // $per_page = 20;

        // // menentukan halaman saat ini
        // if (isset($_GET["page"])) {
        //     $page = $_GET["page"];
        // } else {
        //     $page = 1;
        // } 

        // // menentukan posisi data pada halaman saat ini
        // $start_from = ($page - 1) * $per_page;
        
        $listBerita = $this->Module->Content->listAllPaginationContent();
        // $listPage = $this->Module->Content->countPagination($page, $per_page);
        for ($i=0; $i < count($listBerita); $i++) { 
            $Data[$i] = array(
                'title'=> $listBerita[$i]['Item']['vTitle'],
                'dinas' => "Dinas Komunikasi Dan Informasi",
                'date' => date('d F Y', strtotime($listBerita[$i]['Item']['dPublishDate'])),
                'type' => 'Setiap Saat',
                'url' => $listBerita[$i]['URL'],
            );
        }
        $Status['result'] = $Data;
        
		// $query = $this->Db->sql_query_array("SELECT COUNT(id) AS total FROM cpcontent");
		// $total_records = $query['total'];
		// $total_pages = ceil($total_records / $per_page);
		
        // $Status['pagination'] = $total_pages;
		echo $this->Template->showAPI($Status);
	}
}