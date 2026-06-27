<?php
class Adoption {
    private $id;
    private $user_id;
    private $pet_id;
    private $request_date;
    private $status;

    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getPetId() { return $this->pet_id; }
    public function getDate() { return $this->request_date; }
    public function getStatus() { return $this->status; }

    public function setId($id) { $this->id = $id; }
    public function setUserId($uid) { $this->user_id = $uid; }
    public function setPetId($pid) { $this->pet_id = $pid; }
    public function setDate($date) { $this->request_date = $date; }
    public function setStatus($status) { $this->status = $status; }
}
?>