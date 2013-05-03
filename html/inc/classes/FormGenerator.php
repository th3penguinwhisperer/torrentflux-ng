<?php 

class FormGenerator
{
	private $form = "";
	private $form_name = "";
	private $form_header = "";
	private $javascript = "";
	private $form_footer = "";
	
	function __construct($form_name) {
		$this->form_name = $form_name;
		$this->initialize();
	}
	
	function initialize() {
		$this->form = "";
		$this->form_header = '<form id="' . $this->form_name . '">';
		$this->form_footer = "</form>";
		//$this->add_direct_argument('action', $_REQUEST['action']);
		//$this->add_direct_argument('action', $_REQUEST['action']);
	}
	
	private function getAttributeCode($attribname, $name, $direct = false) {
		if ( $direct )
			return $attribname . "=" . $name;
		else
			return $attribname . "=" . $this->form_name . "_" . $name;
	}
	
	private function getIdNameAttributeCode($name, $direct = false) {
			return $this->getAttributeCode("id", $name, $direct) . " " . $this->getAttributeCode("name", $name, $direct);
	}
	
	function add_direct_argument($name, $value) {
		$this->form .= "<input type=hidden " . $this->getIdNameAttributeCode($name, true) . " value=$value />";;
	}
	
	function add_argument($name, $value) {
		$this->form .= "<input type=hidden " . $this->getIdNameAttributeCode($name) . " value=$value />";
	}
	
	function add_textfield($name, $class = "") {
		$this->form .= "<input type=text " . $this->getIdNameAttributeCode($name) . ($class != '' ? " class=$class" : "" ) . " />";
	}
	
	function add_submit_button($text = "", $class = "") {
		$this->form .= "<input type=submit id=" . $this->form_name . "_submit " . ($class != '' ? " class=$class" : "" ) . ($text != "" ? " value=$text" : "") . " />";
	}
	
	function add_dropdown($name, $data, $disabled_option) {
		$this->form .= "<select " . $this->getIdNameAttributeCode($name) . ">";
		if ( is_array($data) ) {
			foreach ( $data as $entry )
				$this->form .= "<option value=" . urlencode($entry) . ( $disabled_option == $entry ? " disabled=disabled" : "" ) . ">" . $entry . "</option>";
		}
		$this->form .= "</select>";
	}
	
	function get() {
		$this->generateJavascript();
		return $this->form_header . $this->form . $this->javascript . $this->form_footer;
	}
	
	private function generateJavascript() {
		$this->javascript = "\n" . '<script src="http://code.jquery.com/jquery-latest.js" /><script type="text/javascript">

  $("form").live( \'submit\',  function(e) {
  	e.preventDefault();
    var dataString = $("form").serialize();
    
    $.ajax({
      type: "POST",
      url: "dispatcher.php",
      data: dataString,
      success: function(data) {
        showstatusmessage("Message: " + data);
        refreshajaxdata();
        $("form").reset();
      },
      error: function(data) {
      	alert("faulty");
        showstatusmessage("Request couldn\'t be send to the server" + data);
      }
    });
    return false;
  });
</script>';
	}
}

?>