
<?php

require('../lib/fpdf/fpdf.php');

class PDF extends FPDF

  {
  
    var $B;
    var $I;
    var $U;
    var $HREF;
    
    function PDF($orientation='P', $unit='mm', $size='A4')
    
      {
        $this->FPDF($orientation,$unit,$size);
        
        $this->B = 0;
        $this->I = 0;
        $this->U = 0;
        $this->HREF = '';
      }
      
    function WriteHTML($html)

    {
      $html = str_replace("\n",' ',$html);
      $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);

      foreach($a as $i=>$e)
      
        {
  
        if($i%2==0)

          {
            
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
          }
        
        else
          
          {
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
              $a2 = explode(' ',$e);
              $tag = strtoupper(array_shift($a2));
              $attr = array();
    
              foreach($a2 as $v)
    
              {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])] = $a3[2];

              }
                $this->OpenTag($tag,$attr);
            }
        }
    }
 }   
    function OpenTag($tag, $attr)
    
      {
      
        if($tag=='B' || $tag=='I' || $tag=='U')
          $this->SetStyle($tag,true);
        if($tag=='A')
          $this->HREF = $attr['HREF'];
        if($tag=='BR')
          $this->Ln(5); 
      
      }
      
    function CloseTag($tag)
    
      {
      
      if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
      if($tag=='A')
        $this->HREF = ''; 
      
      }
      
    function SetStyle($tag, $enable)
    
      {
      
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        
        foreach(array('B', 'I', 'U') as $s)
        
        {
        if($this->$s>0)
            $style .= $s;
        }
        
        $this->SetFont('',$style);
      
      }
  
    function PutLink($URL, $txt)

      {
    
      $this->SetTextColor(0,0,255);
      $this->SetStyle('U',true);
      $this->Write(5,$txt,$URL);
      $this->SetStyle('U',false);
      $this->SetTextColor(0);
    
    }
  
  }
  
  $pdf = new PDF();

  $html = '<b> bold text </b> <br> <i> italic text </i> <br> <u> underlined text </u> <br> <br> arbitrary link <a href="awaibara.avatarofvelius.org"> awaibara home </a> <br> <br> or click the image to the left.';

  
  $pdf->AddPage();
  
  $pdf->SetFont('Arial','',20);
  $pdf->Write(5,"This Section Redirects You To the Proper Anchor In This Document : ");
  
  $pdf->SetFont('','U');
  $link = $pdf->AddLink();
  $pdf->Write(5,'here',$link);
  $pdf->SetFont('');
  
  $pdf->AddPage();
  $pdf->SetLink($link);
  
  $pdf->Image('logo.png',10,12,30,0,'','http://awaibara.avatarofvelius.org');
  
  $pdf->SetLeftMargin(45);
  $pdf->SetFontSize(14);
  $pdf->WriteHTML($html);
  
  $pdf->Output('tutorial5.pdf');

?>