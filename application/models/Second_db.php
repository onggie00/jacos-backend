<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Second_db extends CI_Model {
  public function __construct()
  {
    //$otherdb = $this->load->database('otherdb', TRUE);
    parent::__construct();
  }
  public function getall($table)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    return $otherdb->get($table)->result();
  }
  public function getallsort($table,$order,$sort)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    return $otherdb->order_by($order,$sort)->get($table)->result();
  }
  public function withquery($query1,$result)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    $query = $otherdb->query($query1);
    if ($result=='result') {
      return $query->result();
    }else {
        return $query->row();
    }
  }
  public function add_history($msg)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    $date = date('Y-m-d H:i:s');
    $us = $this->session->userdata('admin');
    $cek = $this->admin->checkusername($us);
    $datahistory  = array(
       "id_admin"=> $cek->id_admin,
       "keterangan"=> $msg,
       "date_time" => $date
    );
    $history = $this->second_db->insert('history_admin',$datahistory);
  }
  public function getlast($table,$order)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    return $otherdb->order_by($order,'desc')->get($table)->row();
  }
  public function getfirst($table,$order)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    return $otherdb->order_by($order,'asc')->get($table)->row();
  }
  public function getlastwhere($table,$where,$id="",$order)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    if ($id=="") {
      return $otherdb->distinct()->where($where)->order_by($order,'desc')->get($table)->row();
    }else {
      return $otherdb->distinct()->where($where,$id)->order_by($order,'desc')->get($table)->row();
    }
  }
  public function getalllimit($table,$start,$limit)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    return $otherdb->get($table,$limit,$start)->result();
  }
  public function getalllimitdesc($table,$start,$limit,$order)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    return $otherdb->order_by($order,'desc')->get($table,$limit,$start)->result();
  }
  public function getalllimitasc($table,$start,$limit,$order)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    return $otherdb->order_by($order,'asc')->get($table,$limit,$start)->result();
  }
  public function getbywhere($table,$where,$id="",$result="result")
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    if ($result=='result') {
      if ($id=="") {
        return $otherdb->distinct()->where($where)->get($table)->result();
      }else {
        return $otherdb->distinct()->where($where,$id)->get($table)->result();
      }

    }else {
      return $otherdb->distinct()->where($where,$id)->get($table)->row();
    }

  }
  public function getbywherelimit($table,$where,$key="",$start,$limit)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    if ($key=="") {
      return $otherdb->where($where)->get($table,$limit,$start)->result();
    }else {
      return $otherdb->where($where,$key)->get($table,$limit,$start)->result();
    }

  }
  public function getbywheresort($table,$where,$key="",$order,$sort)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    if ($key=="") {
      return $otherdb->where($where)->order_by($order,$sort)->get($table)->result();
    }else {
      return $otherdb->where($where,$key)->order_by($order,$sort)->get($table)->result();
    }

  }
  public function getbywherelimitsort($table,$where,$key="",$start,$limit,$order,$sort)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    if ($key=="") {
      return $otherdb->where($where)->order_by($order,$sort)->get($table,$limit,$start)->result();
    }else {
      return $otherdb->where($where,$key)->order_by($order,$sort)->get($table,$limit,$start)->result();
    }

  }
  public function insert($table,$data)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    $otherdb->insert($table,$data);
    return $otherdb->affected_rows();
  }

  public function insertid($table,$data)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    $otherdb->insert($table,$data);
    return $otherdb->insert_id();
  }
 
   public function update($table,$data,$where,$id="")
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    if ($id=="") {
      $otherdb->where($where)->update($table,$data);
    }else {
      $otherdb->where($where,$id)->update($table,$data);
    }
    //return $otherdb->affected_rows();
    return $otherdb->last_query();
  }
  public function update2($table,$data,$where,$id="")
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    if ($id=="") {
      $otherdb->where($where)->update($table,$data);
    }else {
      $otherdb->where($where,$id)->update($table,$data);
    }
    return $otherdb->affected_rows();
    // return $otherdb->last_query();
  }
  public function update3($table,$data,$where,$like)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    $otherdb->where($where)->like($like)->update($table,$data);
    return $otherdb->affected_rows();
    // return $otherdb->last_query();
  }
  public function delete($table,$where,$id)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    $otherdb->where($where,$id)->delete($table);
    return $otherdb->affected_rows();
  }
  public function delete2($table,$where,$id,$where2,$id2)
  {
    $otherdb = $this->load->database('otherdb', TRUE);
    $otherdb->where($where,$id)->where($where2,$id2)->delete($table);
    return $otherdb->affected_rows();
  }
}
?>
