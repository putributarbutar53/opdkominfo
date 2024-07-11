<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class editorupload extends Core
{
	var $Submit, $Action, $Id, $uploadDir;
	public function __construct()
	{
		parent::__construct();
		ob_clean();

		//Load General Process
		include '../inc/general_admin.php';
		$this->uploadDir = $this->Config['upload']['dir'];
		$this->Pile->fileDestination = $this->uploadDir;
	}

	function main()
	{
		if ($_FILES['file']['name']) {
			if (!$_FILES['file']['error']) {
				$this->Pile->fileSource = $_FILES['file'];
				$this->Pile->PileRecord();
				$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
				$detectedType = exif_imagetype($this->Pile->fileTmpName);
				$approveType = in_array($detectedType, $allowedTypes);

				if ($approveType) {
					if ($this->Pile->validateFileSize($this->Config['file']['max_file_size'])) {
						$NewFilename = "upload_" . date(Ymdhis) . "." . $this->Pile->fileExt;
						$this->Pile->copyRenameNewFile($NewFilename);
						//echo preg_replace("#\../#",$this->Config['main']['url'],$this->Config['upload']['dir']) . $NewFilename;
						echo $this->Config['main']['url'] . $this->uploadDir . $NewFilename;
					} else
						echo 'Ooops!  Ukuran file lebih besar dari yang di izinkan';
				} else
					echo 'Ooops! Tipe file tidak sesuai dengan tipe file yang di izinkan';
			} else
				echo 'Ooops! Ada error dalam upload file:  ' . $_FILES['file']['error'];
		} else
			echo 'Ops! Tidak ada file yang di upload';
	}
}
