<?php

class TemplateProxy extends Proxy
{
	const NAME = "TemplateProxy";
	
	public function __construct()
	{
		parent::__construct( TemplateProxy::NAME, new VO() );
		$this->template = new Template();
	}
	
	public static function loadFile( $url )
	{
		$file = file_get_contents( $url ) or die("error loading file");
		return $file;
	}
	
	public function createSortableTable( $header_columns, $body_rows, $table_ident="", $header_options = array(), $class='sortable' )
	{
		$table_id = ($table_ident=="") ? "" : "id='$table_ident'";
		
		//HEAD
		$html = br("<table class='$class' ".$table_id." cellspacing='1'>");
		$html.= br("<thead>");
		$html.= br("<tr>");
        $i = 0;
		foreach( $header_columns as $hc )
		{
            $width = "";
            $align = "";
            if( isset($header_options[$i]) ){
                $options = $header_options[$i];
                if( isset($options['width'])){
                   $width = 'width="'.$options['width'].'"'; 
                }
                if( isset($options['align'])){
                    $align = 'align="'.$options['align'].'"'; 
                }
            }
			$html.= br("<th $width $align>$hc</th>");	
            $i++;
		}	
		$html.= br("</tr>");
		$html.= br("</thead>");
		
		//BODY
		$html.= br("<tbody>");		
        
            $j=0;
		foreach( $body_rows as $row )
		{
			$html.= br("<tr>");
            
            $j=0;
			foreach( $row as $col ) {
                
                $width = "";
                $align = "";
                 if( isset($header_options[$j]) ){
                    $options = $header_options[$j];
                    if( isset($options['width'])){
                        $width = 'width="'.$options['width'].'"'; 
                    }
                    if( isset($options['align'])){
                        $align = 'align="'.$options['align'].'"'; 
                    }
                }
                $html.= br("<td $width $align>$col</td>"); 
                $j++;
            }
			$html.= br("</tr>");
           
		}		
		$html.= br("</tbody>");
		$html.= br("</table>");		
		return $html;
	}
    
    public function createOptions( $options, $selected=NULL){
        
        $html = "";
        foreach( $options as $o ){
            $value  = $o[0];
            $name   = $o[1];
            $extra  = ($value == $selected) ? "selected='selected'" : "";
            
            $html .= "<option value='$value' $extra>$name</option>";
        }
        
        return $html;
    }
    
    public function YN( $val ){
        return (intval($val)) ? "Yes" : "No";
    }
    
	public function maxUploadSize(){
		return 20971520; //20Mb
	}
	
    public function maxImageSize(){
		return 2097152; //2Mb
	}
	
	public function validUploadsExt()
	{
		return "pdf|doc|docx|xls|xlsx|txt|mp3|wav|mp4|mov|flv|wmv|avi" . "|" . $this->validImageExt();
	}
	
	public function validImageExt()
	{
		return "jpg|jpeg|png|gif|bmp";
	}
    
    public function handleUpload( $required=false, $field_name='uploadedfile', $filename=null, $destination='/view/uploads/images/profiles/', $exts = null, $max_file_size = null ){
        
        //Set valid extensions
        if( $exts == null ){
            $exts = $this->validUploadsExt();
        }
        //Explode extenstions into array
        $valid_exts = explode( "|", $exts );
        
        //Retrieve max size
        if($max_file_size == null ){
            $max_file_size = $this->maxUploadSize();
        } else {
            $max_file_size = intval($max_file_size);
        }

        //Set the upload location
        $upload_location = HOST.$destination;
        //Check file was uploaded
        if( !isset($_FILES[$field_name]) || $_FILES[$field_name]=="" )
        {
            if( $required ){
                return array('error'=>true, 'message'=>'No file was uploaded');
            } else {
                return array( 'error'=>false, 'filename'=>false );
            }
        }
        
        //Retrieve upload details
        $pathinfo = pathinfo( $_FILES[$field_name]['name']);
        $size	= $_FILES[$field_name]['size'];
        $error	= $_FILES[$field_name]['error'];
        
        
        //PHP error!
        if( $error>0 ){
            switch( $error ){
                //If standard error, return feedback
                default:
                    return array('error'=>true, 'message'=>'Server error saving the file' );
                    break;
                
               case 4:
                   //If error = 4 then no file uploaded
                   //Check if a file was required
                   //return appropriate feedback
                   if( $required ){
                       return array('error'=>true, 'message'=>'No file was uploaded');
                   } else {
                       return array( 'error'=>false, 'filename'=>false );
                   }
                   break;
            }
            
        } else {
            $ext = strtolower($pathinfo['extension']);
        }
        
        
        //Invalid extension
        if( !in_array( $ext, $valid_exts ) ){
            return array( 'error'=>true, 'message'=>'Invalid file');
        }
        
        //Filesize too large
        if( $size>$max_file_size ){
            return array( 'error'=>true, 'message'=>'File too large');
        }
        
        
        
        //Retrieve filename
        $uploadfilename = $_FILES[$field_name]['name'];
        //Check file extension
        $fileext = strtolower(substr($uploadfilename,strrpos($uploadfilename,".")+1));	
        //Create full filename (passed filename + extension)
        
        if( $filename==null && strlen($filename)==0 ){
            $orig_file_name = substr($uploadfilename, 0, strrpos($uploadfilename,".") );
            $orig_file_name = cleanForShortURL( $orig_file_name );	
            $filename = $orig_file_name . "_" . time();
        }					
        
        $fullfilename = $filename . "." . $fileext;
        
        //Check and create directory
        if( !file_exists($upload_location) ){
            mkdir( $upload_location );
        }	
        
        //Move temp file to new location
        move_uploaded_file( $_FILES[$field_name]['tmp_name'], $upload_location.$fullfilename );
        
        //return feedback
        return array( 'error'=>false, 'filename'=>$fullfilename );
    }

	public function vo()
	{
		return $this->getData();
	}
}
?>
