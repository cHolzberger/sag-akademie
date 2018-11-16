<?php
/* 
 * 03.06.2009 by Christian Holzberger <ch@mosaik-software.de>
 * use without written license not permitted
 */

class Generic_Mail extends k_Component {
	function POST() {
		$messageData = $this->buildMessage($_POST);
		$email = new MosaikEmail();
		$email->setContainer("__admin");
		$email->setPage("buchung_sag"); /*FIXME: needs to be dynamic! */
		$email->addData("Buchungen", $buchungen);
		$email->send(SMTP_ADMIN_RECIVER, SMTP_ADMIN_SENDER);
	}

	function buildMessage($data) {

		$message = "";
		foreach ( $data as $name=>$value) {
			
		}
		return $message;
	}
}

?>
