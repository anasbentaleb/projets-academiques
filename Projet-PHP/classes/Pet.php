<?php
class Pet {
    private $id;
    private $name;
    private $type;
    private $age;
    private $description;
    private $image;
    private $status;

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getType() { return $this->type; }
    public function getAge() { return $this->age; }
    public function getDescription() { return $this->description; }
    public function getImage() { return $this->image; }
    public function getStatus() { return $this->status; }
    
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setType($type) { $this->type = $type; }
    public function setAge($age) { $this->age = $age; }
    public function setDescription($desc) { $this->description = $desc; }
    public function setImage($image) { $this->image = $image; }
    public function setStatus($status) { $this->status = $status; }
}
?>