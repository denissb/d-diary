<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
function compress()
{
	$CI =& get_instance();
	$buffer = $CI->output->get_output();
	
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); // remove comments
    $buffer = str_replace('{ ', '{', $buffer); // remove unnecessary spaces.
    $buffer = str_replace(' }', '}', $buffer);
    $buffer = str_replace('; ', ';', $buffer);
    $buffer = str_replace(', ', ',', $buffer);
    $buffer = str_replace(' {', '{', $buffer);
    $buffer = str_replace('} ', '}', $buffer);
    $buffer = str_replace(': ', ':', $buffer);
    $buffer = str_replace(' ,', ',', $buffer);
    $buffer = str_replace(' ;', ';', $buffer);

	$CI->output->set_output($buffer);
	$CI->output->_display();
}
 
/* End of file compress.php */
/* Location: ./system/application/hools/compress.php */