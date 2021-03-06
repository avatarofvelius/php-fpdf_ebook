﻿<?php
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
           
  }
  
?>