<?php
require('fpdf.php');

class PDF extends FPDF
  {
  
    function Header()    
      {     
        global $title;
        
        $this->SetFont('Arial','B',15);
                
        $w = $this->GetStringWidth($title);
        $this->SetX((210-$w)/2);
        
  //    $this->SetDrawColor(0,80,180);
  //    $this->SetFillColor(230,230,0);
        $this->SetTextColor(128);

        $this->SetLineWidth(1);
        
        $this->Cell($w,9,$title,0,1,'C',false);
        
        $this->Ln(10);      
      }
  
    function Footer()    
      {      
        $this->SetY(-15);
        
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(128);
        
        $this->Cell(0,10,'Copyright: Matt T Myers - avatarofvelius.org ~ 2004-2013',0,0,'C');       
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');

              
      }
      
    function ChapterTitle($num, $label)    
      {      
        $this->SetFont('Arial','',12);
        $this->SetFillColor(200,220,255);
        
        $this->Cell(0,6,"Chapter $num : $label",0,1,'R',true);
        
        $this->Ln(4);
      }  
      
    function ChapterBody($file)    
      {     
        $txt = file_get_contents($file);
      
        $this->SetFont('Times','',12);
        $this->SetLeftMargin(15);
        $this->SetRightMargin(15);
        
        $this->MultiCell(0,5,$txt);
        
        $this->Ln();
        
 //       $this->SetFont('','I');
        
 //       $this->Cell(0,5,'(end of excerpt)');        
      } 

    function ChapterBodyCenter($file)    
      {     
        $txt = file_get_contents($file);
      
        $this->SetFont('Times','',12);

        $this->MultiCell(0,5,$txt,0,'C');
        
        $this->Ln();
        
    //    $this->SetFont('','I');
        
    //    $this->Cell(0,25,' (end of excerpt)');        
      } 
      
    function PrintChapter($num, $title, $file)    
      {    
         $this->AddPage();
         $this->ChapterTitle($num, $title);
         $this->ChapterBody($file);
      } 
    function PrintChapterNoTitle($file)    
      {    
         $this->AddPage();
         $this->ChapterBody($file);
      } 
            
    function PrintBack($file)    
      {    
         $this->AddPage();
         $this->ChapterBodyCenter($file);
      } 
 

     //ATTACHMENT
    var $files = array();
    var $n_files;
    var $open_attachment_pane = false;

    function Attach($file, $name='', $desc='')
    {
        if($name=='')
        {
            $p = strrpos($file,'/');
            if($p===false)
                $p = strrpos($file,'\\');
            if($p!==false)
                $name = substr($file,$p+1);
            else
                $name = $file;
        }
        $this->files[] = array('file'=>$file, 'name'=>$name, 'desc'=>$desc);
    }

    function OpenAttachmentPane()
    {
        $this->open_attachment_pane = true;
    }

    function _putfiles()
    {
        $s = '';
        foreach($this->files as $i=>$info)
        {
            $file = $info['file'];
            $name = $info['name'];
            $desc = $info['desc'];

            $fc = file_get_contents($file);
            if($fc===false)
                $this->Error('Cannot open file: '.$file);

            $this->_newobj();
            $s .= $this->_textstring(sprintf('%03d',$i)).' '.$this->n.' 0 R ';
            $this->_out('<<');
            $this->_out('/Type /Filespec');
            $this->_out('/F '.$this->_textstring($name));
            $this->_out('/EF <</F '.($this->n+1).' 0 R>>');
            if($desc)
                $this->_out('/Desc '.$this->_textstring($desc));
            $this->_out('>>');
            $this->_out('endobj');

            $this->_newobj();
            $this->_out('<<');
            $this->_out('/Type /EmbeddedFile');
            $this->_out('/Length '.strlen($fc));
            $this->_out('>>');
            $this->_putstream($fc);
            $this->_out('endobj');
        }
        $this->_newobj();
        $this->n_files = $this->n;
        $this->_out('<<');
        $this->_out('/Names ['.$s.']');
        $this->_out('>>');
        $this->_out('endobj');
    }

    function _putresources()
    {
        parent::_putresources();
        if(!empty($this->files))
            $this->_putfiles();
    }

    function _putcatalog()
    {
        parent::_putcatalog();
        if(!empty($this->files))
            $this->_out('/Names <</EmbeddedFiles '.$this->n_files.' 0 R>>');
        if($this->open_attachment_pane)
            $this->_out('/PageMode /UseAttachments');
    }
     
  }
  
?>