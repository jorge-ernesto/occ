<?php
    //Si existen las sesiones flashdata que se muestren
    if($this->session->flashdata('correcto')){
        echo "<div class='alert alert-success' role='alert'>{$this->session->flashdata('correcto')}</div>";
    }                                    
    if($this->session->flashdata('incorrecto')){
        echo "<div class='alert alert-danger' role='alert'>{$this->session->flashdata('incorrecto')}</div>";                        
    } 
    if($this->session->flashdata('database_error')){
        $errors = $this->session->flashdata('database_error');
        echo "<div class='alert alert-danger' role='alert'>{$errors['message']}</div>";
    }                                       
?>